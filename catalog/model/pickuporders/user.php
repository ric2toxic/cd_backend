<?php
class ModelPickupordersUser extends Model
{
    /**
     * Method to Login to user by app
     * @param $username     : string of user name
     * @param $password     : string of password
     * @param $json_format  : json format data by app of json format
     * Output: NULL
     * Author: vikas, 2017
     */

    public function login($username, $password , $json_format) {
        if( !empty($username) && !empty($password) ){
            
            $checkUserIsValid = $this->checkUserIsValid( $username );
            
            if( $checkUserIsValid ){
                
                $checkPasswordIsValid = $this->checkPasswordIsValid( $checkUserIsValid, $password );

                if( $checkPasswordIsValid ){
                    $sql = "INSERT INTO " . DB_PREFIX . "pickup_login_history
                            SET pickup_username = '" . $this->db->escape($username). "'
                                success = 1 ,
                                json_format = '" . $this->db->escape($json_format) . "',
                                date_added = NOW() 
                            ";
                    $this->db->query($sql);        
                } else {
                    $this->updateLoginHistory($username, $json_format, 2 );
                    return false;
                }
            } else {
                $this->updateLoginHistory($username, $json_format, 0 );
                return false;
            }
        }
    }

    /**
     * Method to check User is valid
     * @param $username     : string of user name
     * Output: retun pickup_user_id , otherwise false
     * Author: vikas, 2017
     */

    private function checkUserIsValid($username){
        $sql = "SELECT pickup_user_id
                FROM " . DB_PREFIX . "pickup_user
                WHERE username = '". $this->db->escape($username) . "'
                  AND status  =  1 ";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->row['pickup_user_id'] ;
        } else {
            return false;
        }     
    }

    /**
     * Method to check password is valid
     * @param $pickup_user_id   : Integer of user id
     * @param $password         : string of password
     * Output: retun true , otherwise false
     * Author: vikas, 2017
     */

    private function checkPasswordIsValid($pickup_user_id, $password){
        $sql = "SELECT user_id
                FROM " . DB_PREFIX . "pickup_user
                WHERE pickup_user_id  = '". (int)($pickup_user_id) . "'
                  AND password = '". $this->db->escape($password) . "'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return true;
        } else {
            return false;
        }     
    }


    /**
     * Method to Update Login History When pickup user login or not or wrong information given
     * @param $username     : string of username
     * @param $json_format  : json format data by app of json format
     * @param $success      : password wrong = 2, login successfull = 1, and username wrong = 0 
     * Output: NULL
     * Author: vikas, 2017
     */

    private function updateLoginHistory($username , $json_format, $success){
        $sql = "INSERT INTO " . DB_PREFIX . "pickup_login_history
                SET pickup_username = '" . $this->db->escape($username). "'
                    success = " . (int)$success . " ,
                    json_format = '" . $this->db->escape($json_format) . "',
                    date_added = NOW() 
                ";
        $this->db->query($sql);
    }

    /**
     * Method to Set access token when pickup user login
     * @param $pickup_user_id : Integer of pickup user id
     * @param $username       : string of username
     * Output: return access token 
     * Author: vikas, 2017
     */

    public function setAccessToken($pickup_user_id, $username) {
        //generate access token
        $access_token = md5(substr($username, 0, mt_rand(1, strlen($username))) . mt_rand((int)$user_id, ((int)$user_id) + 999));

        $sql = "UPDATE " . DB_PREFIX . "pickup_user
                SET access_token = '" . $access_token . "', 
                    date_access_created = NOW() 
                WHERE pickup_user_id  = '" . (int)$pickup_user_id  . "' 
                  AND username = '" . $this->db->escape($username) . "'";

        $this->db->query( $sql );

        return $access_token;
    }

    /**
     * Method to access token is validate or not
     * @param $username     : string of username
     * @param $access_token : string of access token
     * Output: return true , otherwise false 
     * Author: vikas, 2017
     */

    public function validateAccess($username, $access_token) {
        
        if (!$username or !$access_token) // Avoid empty string hits
            return false;
        
        $sql = "SELECT pickup_user_id 
                FROM " . DB_PREFIX . "pickup_user
                WHERE username = '" . $this->db->escape($username) . "' 
                  AND access_token = '" . $this->db->escape($access_token) . "'";

        $query = $this->db->query($sql);
        
        if ( $query->num_rows )
            return true;
        else
            return false;
    }

    /**
     * Method to check default city of user
     * @param $username     : string of username
     * Output: default city is avaiable so return true , otherwise return false 
     * Author: vikas, 2017
     */

    public function checkDefaultCityCodeOfuser( $username ) {

        if (!$username ) // Avoid empty string hits
            return false;

        
        $sql = "SELECT default_pickup_city_code
                FROM " . DB_PREFIX . "pickup_user
                WHERE username = '" . $this->db->escape($username) . "' ";

        $query = $this->db->query($sql);
        
        if ( $query->num_rows )
            return $query->row['default_pickup_city_code'];
        else
            return false;
    }


    /**
     * Method to set Default City Code By City name
     * @param $cityname  : string of city name
     * Output: get city code by city name i.e. JP, ST, HR
     * Author: vikas, 2017
     */

    public function setDefaultCityCodeByCityname( $cityname , $username) {

        if (!$cityname ) // Avoid empty string hits
            return false;

        $sql = "SELECT pickup_city_code 
                FROM " . DB_PREFIX . "pickup_city
                WHERE pickup_city_name LIKE '%" . $this->db->escape($cityname) . "%'";

        $query = $this->db->query($sql);
        
        if ( $query->num_rows ){
            
            $sql = "UPDATE " . DB_PREFIX . "pickup_user
                    SET default_pickup_city_code = '" . $this->db->escape($query->row['pickup_city_code']) . "'
                    WHERE username LIKE '%" . $this->db->escape($username) . "%'
                    ";
            $this->db->query($sql);  
            return $query->row['pickup_city_code'];
        } else {
            return false;
        }
    }

    /**
    * Method for get Pickup zones by zone pickup city
    * @param: cityname : string of cityname 
    * @return: get Pickup zones
    * @author: vikas, 2017
    */
    public function getPickupZones( $citycode ){
        
        if(!$citycode)
            return false;

        
        $sql = "SELECT pickup_zone_id, 
                       pickup_zone_name
                FROM " . DB_PREFIX . "pickup_zone
                WHERE pickup_city_code LIKE '" . $this->db->escape($citycode) . "'";
        $query = $this->db->query($sql);
        
        if( $query->num_rows ){
            $zone_record = array();
            foreach( $query->rows as $values){
                $zone_record[$values['pickup_zone_id']] = $values['pickup_zone_name'];    
            }

            return $zone_record;
            
        } else {
            return false;
        }
    }

    /**
    * Method for Update Zone ids of pickup user
    * @param: selected_zone_ids : integer values of zone ids in string format
    * @param: username : string of username
    * @return: NULL
    * @author: vikas, 2017
    */
    public function updatePickupZoneIds( $selected_zone_ids, $username ){

        if( !$selected_zone_ids )
            return false;

        $sql = "UPDATE " . DB_PREFIX . "pickup_user
                SET pickup_zone_ids = '" . $this->db->escape($selected_zone_ids) . "'
                WHERE username LIKE '%" . $this->db->escape($username) . "%'
                ";
        $this->db->query($sql);  
    }

}
