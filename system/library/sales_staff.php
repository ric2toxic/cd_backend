<?php
class SalesStaff{

    public static function checkStoreVoucher( $db , $voucher_code ){
        $sql = "SELECT store_code
                FROM ".DB_PREFIX."sales_staff
                WHERE store_voucher = '".($db->escape($voucher_code))."' AND
                      active_status = 1 AND
                      store_code != 'N/A' 
                LIMIT 1";
        $result = $db->query($sql);
        if( $result->num_rows > 0){
            return $result->row['store_code'];
        }
        else{
            return false;
        }
    }

    public static function checkStoreDelivery( $db , $voucher_code ){
        $sql = "SELECT store_code
                FROM ".DB_PREFIX."sales_staff
                WHERE store_delivery = '".($db->escape($voucher_code))."' AND
                      active_status = 1 AND
                      store_code != 'N/A' 
                LIMIT 1";
        $result = $db->query($sql);
        if( $result->num_rows > 0){
            return $result->row['store_code'];
        }
        else{
            return false;
        }
    }

    public static function getStaffInfoByStaffId($db, $staff_id){
        $sql = "SELECT name 
                FROM " . DB_PREFIX. "sales_staff
                WHERE staff_id = " . (int)$staff_id . " LIMIT 1 ";
        $query = $db->query($sql);
        if( $query->num_rows ){
            return $query->row;
        } else {
            return false;
        }
    }
}
?>
