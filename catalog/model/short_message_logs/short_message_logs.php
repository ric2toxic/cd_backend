<?php
class ModelShortMessageLogsShortMessageLogs extends Model {

	/**
     * @return int
     * @author Anurag Jain, 18 Apr 2019
     */
    public function getLastCustomerCriteriaSyncId(): int
    {
        $last_sync_id = 0;
        $sql_last_sync_id = "
                            SELECT value 
                            FROM " . DB_PREFIX . "wsb_credit_configs
                            WHERE config_keys = 'last_track_id_customer_to_criteria_sync'";

        $result_last_sync_id = $this->db->query( $sql_last_sync_id );

        if ( $result_last_sync_id->num_rows ) {
            $last_sync_id = (int) $result_last_sync_id->row['value'];
        }

        return $last_sync_id;
    }

    /**
     * @return int
     * @author Anurag Jain, 18 Apr 2019
     */
    public function getLatestCustomerCriteriaSyncId( $aws_mysqli ): int
    {
        $latest_sync_id = 0;
        $sql_latest_sync_id = "
                                SELECT max(txn_id) AS latest_id 
                                FROM processed_sms";
        
        $result_latest_sync_id = $aws_mysqli->query( $sql_latest_sync_id );

        if ( $result_latest_sync_id->num_rows ) {
           $latest_sync_id = $result_latest_sync_id->fetch_row()[0];
        }

        return $latest_sync_id;
    }

    /**
     * @param  $aws_mysqli     
     * @param  int $last_sync_id   
     * @param  int $latest_sync_id 
     * @return array
     * @author Anurag Jain, 18 Apr 2019
     */
    public function getCustomersToSyncForSMSCriteria( $aws_mysqli, int $last_sync_id, int $latest_sync_id ): array
    {
        $all_customers_data = array();

        $sql_customers_to_sync = "
                                    SELECT 
                                        customer_id,
                                        GROUP_CONCAT( distinct CONCAT_WS( ',',
                                                                 ps.criteria_type,
                                                                 ps.txn_type
                                                               ), 
                                                     ','
                                                    ) AS all_criteria
                                    FROM processed_sms ps
                                    WHERE 
                                        txn_id > '" . $last_sync_id . "'
                                        AND txn_id <= '" . $latest_sync_id . "'
                                    GROUP BY customer_id";
        
        $result_customers_to_sync = $aws_mysqli->query( $sql_customers_to_sync );

        if ( $result_customers_to_sync->num_rows ) {
            $all_customers_data = $result_customers_to_sync->fetch_all();
        }

        return $all_customers_data;
    }

    /**
     * @param  int    $customer_id 
     * @return array
     * @author Anurag Jain, 18 Apr 2019
     */
    public function getCustomerOldSyncedCriteria( int $customer_id ): array
    {
        $old_critera = array();

        $sql_old_stored_criteria = "
                                    SELECT 
                                    GROUP_CONCAT(criteria) AS criteria
                                    FROM ". DB_PREFIX ."wsb_customer_to_sms_criteria_sync
                                    WHERE customer_id = '" . $customer_id . "'
                                    ";
        $result_old_stored_criteria = $this->db->query( $sql_old_stored_criteria );
        
        $old_critera = array();
        
        if ( $result_old_stored_criteria->num_rows ) {
            $old_critera = $result_old_stored_criteria->row['criteria'];
            $old_critera = explode( ',', $old_critera );
            $old_critera = array_filter($old_critera);
        }

        return $old_critera;
    }

    /**
     * @param int    $customer_id  [description]
     * @param string $criteria_key [description]
     * @author Anurag Jain, 18 Apr 2019
     */
    public function addCustomerCriteriaAvailability( int $customer_id, string $criteria_key )
    {
        $criteria_sql = "
                        INSERT INTO ".DB_PREFIX."wsb_customer_to_sms_criteria_sync( customer_id, criteria, is_criteria_tracked ) 
                        VALUES ( '". $customer_id ."', '". $criteria_key ."', '1' )
                        ";
        return $this->db->query( $criteria_sql );
    }

    /**
     * @param int $latest_sync_id [description]
     * @author Anurag Jain, 18 Apr 2019
     */
    public function updateCustomerSMSCriteriaLatestSyncId( int $latest_sync_id )
    {
        
        $sql_update_last_sync_id = "
                                    UPDATE " . DB_PREFIX . "wsb_credit_configs
                                    SET value = '". $latest_sync_id ."'
                                    WHERE config_keys = 'last_track_id_customer_to_criteria_sync'";

        return $this->db->query( $sql_update_last_sync_id );
    }
}