<?php
class ModelCommonCommon extends Model {

    /**
    * Method for display field value on click to see. it's a common function. 
    * we can use multiple place in adminpanel tpl files. 
    * @author: vikas, 2017, Updated BY Nishu (For admin_change_log)
    */
    public function fieldValueOnClickToSee($data = array()){

        $ip = $this->request->getIpAddress; //$_SERVER['HTTP_X_FORWARD'];
        $file_location      = $_SERVER['HTTP_REFERER'];
        $user_agent         = $_SERVER['HTTP_USER_AGENT'];
        $user_id            = $this->user->getId();
        $username           = $this->user->getUserName($this->user->getId())['username'];
        $name               = $this->user->getUserName($this->user->getId())['name'];
        $user_group_name    = $this->user->getGroupName();

        //Set Data to add into admin_change_log
        $admin_change_data                  = array();
        $admin_change_data['field_name']    = $data['field_name'];
        $admin_change_data['new_value']     = $data['new_value'];
        $admin_change_data['file_location'] = $file_location;
        $admin_change_data['ip_address']    = $ip;
        $admin_change_data['user_agent']    = $user_agent;
        $admin_change_data['user_id']       = (int)$user_id;
        $admin_change_data['user_type']     = $user_group_name;
        $admin_change_data['username']      = $username;
        $admin_change_data['table_name']    = 'oc_admin_info_log';
        $admin_change_data['name']          = $name;

        //Call dynamic static function for entry into admin change log
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);
       
        /*$sql = "INSERT INTO oc_admin_change_log 
                SET field_name = '" . $this->db->escape($data['field_name']) . "' ,
                    new_value = '" . $this->db->escape($data['new_value']) . "' ,
                    date_added = NOW() ,
                    file_location = '" . $this->db->escape($file_location) . "' ,
                    ip_address  = '" . $this->db->escape($ip) . "' ,
                    user_agent = '" . $this->db->escape($user_agent) . "' ,
                    user_id  = '" . $this->db->escape($user_id) . "' ,
                    user_type = '" . $this->db->escape($user_group_name) . "' ,
                    username = '" . $this->db->escape($username) . "' ,
                    table_name ='oc_admin_info_log',
                    name = '" . $this->db->escape($name) . "'";
       $this->db->query($sql);*/

    }
    
}
