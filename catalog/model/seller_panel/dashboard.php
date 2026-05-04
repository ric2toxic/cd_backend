<?php
class ModelSellerPanelDashboard extends Model {

    public function getSellerStoreCustomers(){
        //$stores = array_column($this->getSellerStores($seller_id), 'store_id');

        $sql = "SELECT * 
                FROM ".DB_PREFIX."customer" ;

        return $this->db->query($sql)->rows;
    }

    /**
    * Method for get Seller stores
    * @param $seller_id : Integer of seller id
    */
    public function getSellerStores($seller_id){
        $this->load->model('setting/store');
        $stores = $this->db->query("SELECT os.store_id FROM ".DB_PREFIX."setting os
            WHERE os.code = 'config' 
            AND os.key = 'config_seller_id'     
            AND os.value = ".$seller_id);

        if ($stores->num_rows) {
            return $stores->rows;
        }
        else{
            return 0;
        }
    }


    public function getOrdersStats($data = array(), $sort = array()) {

        $amount_query = "SELECT SUM(op.transfer_price_per_piece * op.quantity * op.piece_in_set) as total_amount ";
        $order_query = "SELECT COUNT(DISTINCT o.order_id) as total_order ";

        $sql = "FROM `" . DB_PREFIX . "order` o
                INNER JOIN `" . DB_PREFIX . "suborder` osub ON osub.order_id = o.order_id
                INNER JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id 
                WHERE oop.seller_id = '" . (int)$data['seller_id'] . "'
                  AND osub.order_status_id > 0 ";

        if ( !empty($data['order_status']) ) {
           $sql .= " AND osub.order_status_id IN (" . $this->db->escape(implode(',', $data['order_status'])) . ")";
        }

        if ( !empty($data['stores']) ) {
           $sql .= " AND o.store_id IN (" . $this->db->escape(implode(',', $data['stores'])) . ")";
        }

        $order_query .= $sql;
        $order_res = $this->db->query($order_query);

        $result = array();
        $result['total_order'] = isset($order_res->row['total_order']) ? (int)$order_res->row['total_order'] : 0;

        if ( !empty($sort['order_by']) and !empty($sort['order_way']) ) {
           $sql .= " ORDER BY " . $sort['order_by'] . " " . $sort['order_way'];
        }

        if ( isset($sort['limit']) and isset($sort['offset']) ) {
           $sql .= " LIMIT " . (int)$sort['offset'].', '.(int)($sort['limit']);
        }

        $amount_query .= $sql;
        $amount_res = $this->db->query($amount_query);
        $result['total_amount'] = isset($amount_res->row['total_amount']) ? (float)$amount_res->row['total_amount'] : 0;

        return $result;
    }

}
