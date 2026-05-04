<?php

 class WsbPurchaseInvoiceImage{

    private $_purchase_id = 0;
    private $_db;

    public function __construct($registry, $file_path = ''){

        $this->_registry = $registry;

        if(method_exists($registry, 'get')){
            $this->_db = $registry->get('db');
        }else{
            $this->_db = $registry->db;
        }

        if(!empty($file_path)){
            $file_path = unserialize(base64_decode($file_path));
            $this->_purchase_id = $file_path['purchase_id'];
        }

    }

    public function getDownloadImage(){
        $sql = "SELECT CONCAT(s.nickname,'/',wsbp.purchase_bill_image) as image_name
                FROM `" . DB_PREFIX . "wsb_purchase` wsbp 
                INNER JOIN `" . DB_PREFIX . "ms_seller` s
                ON wsbp.seller_id=s.seller_id 
                WHERE wsbp.purchase_id= '" . (int)$this->_purchase_id . "'";

        $query = $this->_db->query($sql);

        return base64_encode($query->row['image_name']);

    }

    
}
