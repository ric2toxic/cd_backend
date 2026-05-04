<?php

/**
 * Common Library class for multiple category
 * 
 * @author: Nishu, Nov 2017
 */
class CommonLib {

    /**
    * Public function to add data into oc_admin_change_log DB table dynamically
    * @param: $db, $data array
    * @return Boolen
    * @author Nishu, Nov 2017
    */
    public static function addAdminChangeLog($db, $data){

        $sql = "INSERT INTO " . DB_PREFIX . "admin_change_log 
                    SET ";

        if(!empty($data['table_name'])){
            $sql .= "table_name = '".$db->escape($data['table_name'])."',";
        }
        if(!empty($data['field_name'])){
            $sql .= "field_name = '".$db->escape($data['field_name'])."',";
        }
        if(!empty($data['ref_url'])){
            $sql .= "ref_url = '".$db->escape($data['ref_url'])."',";
        }
        if(isset($data['old_value'])){
            $sql .= "old_value = '".$db->escape($data['old_value'])."',";
        }
        if(isset($data['new_value'])){
            $sql .= "new_value = '".$db->escape($data['new_value'])."',";
        }
        if(!empty($data['comment'])){
            $sql .= "comment = '".$db->escape($data['comment'])."',";
        }
        if(!empty($data['username'])){
            $sql .= "username = '".$db->escape($data['username'])."',";
        }
        if(!empty($data['date_added'])){
            $sql .= "date_added = '".$db->escape($data['date_added'])."',";
        }else{
            $sql .= "date_added = NOW(),";
        }
        if(isset($data['table_id'])){
            $sql .= "table_id = '".(int)$data['table_id']."',";
        }
        if(!empty($data['user_id'])){
            $sql .= "user_id = '".(int)$data['user_id']."',";
        }
        if(!empty($data['name'])){
            $sql .= "name = '".$db->escape($data['name'])."',";
        }
        if(!empty($data['file_location'])){
            $sql .= "file_location = '".$db->escape($data['file_location'])."',";
        }
        if(!empty($data['user_type'])){
            $sql .= "user_type = '".$db->escape($data['user_type'])."',";
        }
        if(!empty($data['source_field'])){
            $sql .= "source_field = '".$db->escape($data['source_field'])."',";
        }
        if(!empty($data['ip_address'])){
            $sql .= "ip_address = '".$db->escape($data['ip_address'])."',";
        }

        if(!empty($data['user_agent'])){
            $sql .= "user_agent = '".$db->escape($data['user_agent'])."' ";
        }else{
            $sql .= "user_agent = '".$db->escape($_SERVER['HTTP_USER_AGENT'])."' ";
        }

        //Execute Query
        $db->query($sql);

        //Get Last inserted Id
        $log_id = $db->getLastId();

        //Check if Id is not empty, means successfully inserted
        if($log_id > 0){
            return true;
        }else{
            return false;
        }
    }

}

?>
