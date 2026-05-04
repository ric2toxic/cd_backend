<?php

class ModelSellerapiAccount extends Model
{


    public function login($email, $password) {

        $q = "SELECT c.customer_id, c.firstname, c.lastname, c.email, c.telephone, c.address_id, ms.nickname, ms.company FROM " . DB_PREFIX . "customer c
              INNER JOIN ".DB_PREFIX."ms_seller ms
              ON c.customer_id = ms.seller_id
              WHERE (LOWER(c.email) = '" . $this->db->escape(utf8_strtolower($email)) . "' OR telephone = '".$this->db->escape(utf8_strtolower($email))."')
              AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "')))))
              OR password = '" . $this->db->escape(md5($password)) . "')";

        $customer_query = $this->db->query($q);

        $seller_data = array();

        if ($customer_query->num_rows) {
            $seller_data['seller_id'] = $customer_query->row['customer_id'];

            $seller_data['firstname']  = $customer_query->row['firstname'];
            $seller_data['lastname']  = $customer_query->row['lastname'];
            $seller_data['email']  = $customer_query->row['email'];
            $seller_data['telephone']  = $customer_query->row['telephone'];
            $seller_data['company']  = $customer_query->row['company'];
            $seller_data['nickname']  = $customer_query->row['nickname'];
            $seller_data['address_id']  = $customer_query->row['address_id'];


        }
            return $seller_data;

    }

    public function setSellerAccessToken($customer_id, $username)
    {
        $access_token = '';

        if($customer_id > 0) {
            //generate access token
            $access_token = substr(substr($username, mt_rand(0, 25), 1) . substr(md5(16), 1), 0, 16);//rand(1000000,rand(0,10000000));

            $sql = "UPDATE " . DB_PREFIX . "ms_seller
                SET access_token = '" . $access_token . "'
                WHERE seller_id = " . $customer_id;

            $this->db->query($sql);
        }

        return $access_token;
    }

    public function getSellerInfo($seller_id){
        $sql = "SELECT c.* FROM " . DB_PREFIX . "ms_seller ms
                INNER JOIN ".DB_PREFIX."customer c
                ON ms.seller_id = c.customer_id
                WHERE ms.seller_id = ".$seller_id;


        return $this->db->query($sql)->row;

    }


}