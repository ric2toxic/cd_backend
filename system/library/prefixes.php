<?php

class Prefixes {

    /**
    * Public function to get prefix to generate DN or CN etc
    * @param: $db, Gstin Num, prefix type
    * @return: Prefix String
    * @author: Nishu, August 2017
    */
    public static function getPrefix($db, $gstin, $prefix_type) {
        $data = array();
        $sql = "SELECT * 
                    from " . DB_PREFIX . "wsb_prefixes  
                WHERE status = 1 
                    AND gstin = '" . $db->escape($gstin) . "' 
                    AND prefix_type = '" . $db->escape($prefix_type) . "'";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            $data = $result->row;
        } else {
            $sql = "SELECT *
                        from " . DB_PREFIX . "wsb_prefixes  
                    WHERE status = 1 
                        AND gstin = 'DEFAULT' 
                        AND prefix_type = '" . $db->escape($prefix_type) . "'";
            $result = $db->query($sql);
            $data = $result->row;
        }
        return $data;
    }

    /**
    * Public function to Update available_no, against given prefix
    * @param: $db, $data
    * @return: Prefix String
    * @author: Nishu, August 2017
    */
    public static function updatePrefixAvailableNo($db, $data) {
        $sql = "
                UPDATE " . DB_PREFIX . "wsb_prefixes  
                 SET 
                    financial_year = '". $db->escape($data['financial_year']) ."',
                    available_no   = '". (int)$data['available_no'] ."'
                 WHERE
                    prefix = '". $db->escape($data['prefix']) ."'
                    AND status = 1
              ";
        $result = $db->query($sql);
    }

    

}

?>
