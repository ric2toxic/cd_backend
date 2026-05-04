<?php

class ModelOpsapiAccount extends Model
{

    public function login($username, $password) {
        $q = "SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'";
        $user_query = $this->db->query($q);
        $data = array();
        if ($user_query->num_rows) {
            $data['user_id'] = $user_query->row['user_id'];
            $data['username'] = $user_query->row['username'];
            $data['email'] = $user_query->row['email'];
            $data['name'] = $user_query->row['firstname']." ".$user_query->row['lastname'];

           /* $user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

            $permissions = unserialize($user_group_query->row['permission']);

            if (is_array($permissions)) {
                foreach ($permissions as $key => $value) {
                    $this->permission[$key] = $value;
                }
            }
            */

        }

        return $data;
    }


    public function setOpsAccessToken($user_id, $username)
    {
        $access_token = '';

        if($user_id > 0) {
            //generate access token
            $access_token = substr(substr($username, mt_rand(0, 25), 1) . substr(md5(16), 1), 0, 16);//rand(1000000,rand(0,10000000));

            $sql = "UPDATE " . DB_PREFIX . "user
                SET access_token = '" . $access_token . "'
                WHERE user_id = " . $user_id;

            $this->db->query($sql);
        }

        return $access_token;
    }

    public function getUserInfo($user_id){
        $sql = "SELECT u.* FROM " . DB_PREFIX . "user u
                WHERE u.user_id = ".$user_id;


        return $this->db->query($sql)->row;

    }


}