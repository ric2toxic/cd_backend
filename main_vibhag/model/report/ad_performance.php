<?php
class ModelReportAdPerformance extends Model {
    public function getConversionByAffiliateTrackingCode($tracking_code, $date_start, $date_end) {
      echo  $sql = "SELECT o.customer_id, o.firstname, o.lastname, o.total, o.date_added, o.payment_city, o.tracking
                FROM `oc_order` o
                LEFT JOIN oc_suborder os ON o.order_id = os.order_id
                WHERE `tracking` = '".$tracking_code."'
                AND os.order_status_id > 0
                AND DATE(o.`date_added`) >= DATE('".$date_start."')
                AND DATE(o.`date_added`) <= DATE('".$date_end."')
                AND o.store_id IN (" . WSB_STORES_ID . ") 
                AND o.franchise_id = 0 
                ORDER BY o.`date_added` ASC
                ";

        $query = $this->db->query($sql);

        $data = array();
        $this->load->model('sale/order');
        if ($query->num_rows > 0) {
            $i = 0;
            foreach ($query->rows as $row) {
                $data[$i]['order_date'] = $row['date_added'];
                $data[$i]['order_amount'] =  $row['total'];
                $data[$i]['order_source'] =  $row['tracking'];
                $data[$i]['customer_name'] =  $row['firstname'] ." ".  $row['lastname'];
                $data[$i]['city'] =  $row['payment_city'];

                $first_order_info = $this->model_sale_order->getFirstOrderDataOfCustomer($row['customer_id']);
                $data[$i]['first_order_date'] = $first_order_info['date_added'];
                $data[$i]['first_order_source'] = $first_order_info['tracking'];

                $i++;
            }
        }
        echo '<pre>';
        print_r($data);
    }
}
