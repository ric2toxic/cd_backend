<?php
class ModelWsbPurchaseImport extends Model {

    public function getPurchaseFirms(){
        $sql = "SELECT purchase_firm_id,
                       purchase_firm_name,
                       purchase_firm_city,
                       purchase_firm_tin_no as tin
                FROM ".DB_PREFIX."vat_input_rules 
                WHERE status = 1 
                GROUP BY purchase_firm_id";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            return $result->rows;
        }
    }

    public function addWsbPurchase($data){

        $user = array(
            'user_id' => $this->user->isLogged(),
        );
        $user = array_merge($user,$this->user->getUserName());
        $user = serialize($user);
        
        $sql  = "INSERT INTO ".DB_PREFIX."wsb_purchase ";
        $sql .=         "SET seller_id                        = '".(int)$data['seller_id']."', ";
        $sql .=             "purchase_firm_id                 = '".(int)$data['purchase_firm_id']."', ";
        $sql .=             "invoice_no                       = '".$this->db->escape($data['invoice_no'])."', ";
        $sql .=             "invoice_date                     = '".$this->db->escape($data['invoice_date'])."', ";
        $sql .=             "total_purchase_value             = '".(float)$data['total_purchase_value']."', ";
        if ( (int)$data['payment_done'] ) {
            $sql .=         "payment_cleared                  = 'NOT_APPLICABLE', ";
        } else {
            $sql .=         "payment_cleared                  = 'NO', ";
        }
        $sql .=             "payment_release_invoice_date_gap = '".(int)$data['payment_release_invoice_date_gap']."', ";
        $sql .=             "date_added                       = NOW(), ";
        $sql .=             "user                             = '".$this->db->escape($user)."', ";
        $sql .=             "seller_firm_meta                 = '".$this->db->escape($data['seller_firm_meta'])."', ";
        $sql .=             "purchase_firm_meta               = '".$this->db->escape($data['purchase_firm_meta'])."', ";
        $sql .=             "sor_purchase                     = '".$this->db->escape($data['sor_purchase'])."', ";
        $sql .=             "purchase_bill_image              = '".$this->db->escape($data['purchase_bill_image'])."' ";
        
        if($this->db->query($sql)){
            $purchase_id = (int)$this->db->getLastId();
            
            // Creating trxn_details entry
            $sql_trxn  = "INSERT INTO " . DB_PREFIX . "trxn_details ";
			$sql_trxn .=         "SET trxn_for                = 'WSB_PURCHASE', ";
			$sql_trxn .=             "trxn_for_id             = '" . $purchase_id . "', ";
			if ( (int)$data['payment_done'] ) {
				$sql_trxn .=         "trxn_done               = 'NOT_APPLICABLE', ";
			} else {
				$sql_trxn .=         "trxn_done               = 'NOT_DONE', ";
			}
			$sql_trxn .=             "trxn_amount             = 0, ";
			$sql_trxn .=             "trxn_date_added         = NOW() ";
            
            $this->db->query($sql_trxn); // inserting
            
            // Inserting products breakup
            array_walk($data['products'],function(&$value,$key,$purchase_id){
                $value = "('". (int)$purchase_id . "','" .
                               (int)$value['product_id']. "','" .
                               $this->db->escape($value['sku']). "','" .
                               (int)$value['pieces']. "','" .
                               (float)$value['transfer_price_per_piece']. "','" .
                               (float)$value['seller_tax']. "')";

            },$purchase_id);

            $sql  = "INSERT INTO ".DB_PREFIX."wsb_purchase_breakup ";
            $sql .= "(purchase_id,product_id,sku,pieces,transfer_price_per_piece,seller_tax) ";
            $sql .= "VALUES ";
            $sql .= implode(",",$data['products']);

            if($this->db->query($sql)){
                return $purchase_id;
            }
        }
    }

    public function checkProducts($product_sku, $sor_order){
        if(!empty($product_sku)){

            $sql  = "SELECT op.product_id, op.store_sales, op.mrp, op.quantity, op.sku, op.hsn_code, op.piece_in_set, op.sor_product, op.price, omp.product_id as ms_product_id, omp.seller_id ";

            $sql .= " FROM ".DB_PREFIX."product op ";
            $sql .= " LEFT JOIN ".DB_PREFIX."ms_product omp  ON ( op.product_id = omp.product_id ) ";
            $sql .= " WHERE op.sku IN ('".implode( "','" , $product_sku )."') AND ";
            
            $sql .= " op.is_single = 0 ";

            $result = $this->db->query($sql);

            if($result->num_rows > 0){

                return $result->rows;
            }
            else{
                return array();
            }
        }

    }

    public function getPurchaseFirmDetails($purchase_firm_id){
        $sql = "SELECT purchase_firm_name as company,
                       purchase_firm_address1 as address1,
                       purchase_firm_address2 as address2,
                       purchase_firm_city as city,
                       purchase_firm_pincode as pincode,
                       purchase_firm_tin_no as tin,
                       purchase_firm_state as state,
                       purchase_firm_country as country,
                       purchase_firm_zone_id as zone_id
                FROM ".DB_PREFIX."vat_input_rules
                WHERE purchase_firm_id = '".(int)$purchase_firm_id."' and status=1";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $result = $result->row;
            $result['email'] = 'info@wholesalebox.in';
           
            $zoneModel = new ModelLocalisationZone($this->registry);

            $result['state_code'] = $zoneModel->getZoneGSTStateCode($result['zone_id']);

            return $result;
        }
    }

    public function getWsbPurchases($filters, $for_total = false ){
        if(!$for_total){
            $sql  = "SELECT * ";
        }
        else{
            $sql  = "SELECT count(wp.purchase_id) as total ";
        }

        $sql .= "FROM ".DB_PREFIX."wsb_purchase wp ";
        
        if(!empty($filters['filter_sku'])){
            $sql .= " INNER JOIN  ".DB_PREFIX."wsb_purchase_breakup wpb on  wp.purchase_id=wpb.purchase_id ";
        }

        $sql .= "WHERE 1 = 1 ";
        if(!empty($filters['filter_purchase_firm_id'])){
            $sql .= " AND wp.purchase_firm_id = '".(int)$filters['filter_purchase_firm_id']."' ";
        }
        if(!empty($filters['filter_seller_id'])){
            $sql .= " AND wp.seller_id = '".(int)$filters['filter_seller_id']."' ";
        }
        if(!empty($filters['filter_invoice_no'])){
            $sql .= " AND wp.invoice_no LIKE '%".$this->db->escape($filters['filter_invoice_no'])."%' ";
        }
        if(!empty($filters['filter_invoice_date_from'])){
            $sql .= " AND wp.invoice_date >= '".$this->db->escape($filters['filter_invoice_date_from'])."' ";
        }
        if(!empty($filters['filter_invoice_date_to'])){
            $sql .= " AND wp.invoice_date <= '".$this->db->escape($filters['filter_invoice_date_to'])."' ";
        }
        if(!empty($filters['filter_purchase_value_from'])){
            $sql .= " AND wp.total_purchase_value >= ".(float)$filters['filter_purchase_value_from']." ";
        }
        if(!empty($filters['filter_purchase_value_to'])){
            $sql .= " AND wp.total_purchase_value <= ".(float)$filters['filter_purchase_value_to']." ";
        }
        if(!empty($filters['filter_date_added_from'])){
            $sql .= " AND DATE(wp.date_added) >= '".$this->db->escape($filters['filter_date_added_from'])."' ";
        }
        if(!empty($filters['filter_date_added_to'])){
            $sql .= " AND DATE(wp.date_added) <= '".$this->db->escape($filters['filter_date_added_to'])."' ";
        }
        if(!empty($filters['filter_sku'])){
            $sql .= " AND wpb.sku = '".$this->db->escape($filters['filter_sku'])."' ";
        }        
        $sql .= "ORDER BY ". ( !empty($filters['order_by']) ? 'wp.'.$this->db->escape($filters['order_by']) : ' wp.purchase_id ' );
        $sql .=           " ". ( !empty($filters['order']) ? $this->db->escape($filters['order']) : ' DESC ' )." ";
        
        if(!$for_total){
            $sql .= " LIMIT " . (!empty($filters['limit']) ? (int)$filters['limit'] : ' 10 ' );
            $sql .= " OFFSET " . (!empty($filters['offset']) ? (int)$filters['offset'] : ' 0 ' );
        }
        $result = $this->db->query($sql);
        if( $result->num_rows > 0){
            if(!$for_total){
                $purchase_id =  array_column( $result->rows , 'purchase_id');
                if(!empty($purchase_id)){
                    $sql = "SELECT purchase_id,
                                   owpb.product_id,
                                   owpb.sku,
                                   owpb.pieces,
                                   owpb.transfer_price_per_piece
                            FROM ".DB_PREFIX."wsb_purchase_breakup owpb
                            WHERE owpb.purchase_id IN ( " . implode(",",$purchase_id) . " ) ";
                    $purchase_breakup = $this->db->query($sql);
                    if($purchase_breakup->num_rows > 0){
                        $products = array();
                        array_walk( $purchase_breakup->rows , function ($breakup,$key) use(&$products){
                            $products[$breakup['purchase_id']][] = $breakup;
                        });
                        array_walk( $result->rows , function( &$row , $key ) use($products){
                            $row['products'] = $products[$row['purchase_id']];
                        });
                    }
                }
                return $result->rows;
            }
            else{
                return $result->row['total'];
            }
        }
    }
    public function getWsbPurchases2($filters, $for_total = false ){
       
        $sql = "SELECT oc_wsb_purchase_breakup.breakup_id,
                        oc_wsb_purchase_breakup.purchase_id,
                        oc_wsb_purchase_breakup.product_id,
                        oc_wsb_purchase_breakup.sku,
                        oc_wsb_purchase_breakup.pieces,
                        oc_wsb_purchase.seller_id,
                        oc_ms_seller.nickname,
                        oc_wsb_purchase.purchase_firm_id,
                        oc_wsb_purchase.invoice_date,
                        oc_wsb_purchase.invoice_no,
                        oc_product.model,
                        oc_product.sku,
                        oc_product.store_sales
                FROM oc_wsb_purchase
                INNER JOIN oc_wsb_purchase_breakup ON oc_wsb_purchase.purchase_id = oc_wsb_purchase_breakup.purchase_id
                INNER JOIN oc_product ON oc_wsb_purchase_breakup.product_id = oc_product.product_id
                Inner JOIN oc_ms_seller ON oc_wsb_purchase.seller_id = oc_ms_seller.seller_id ";

        $sql .= " WHERE 1 = 1 ";

        if(!empty($filters['nickname'])){
            $sql .= " AND oc_ms_seller.nickname LIKE '%".$this->db->escape($filters['nickname'])."%' ";
        }
        if(!empty($filters['sku'])){
            $sql .= " AND oc_product.sku LIKE '%".$this->db->escape($filters['sku'])."%' ";
        }

        $sql .= "ORDER BY ". ( !empty($filters['order_by']) ? $this->db->escape($filters['order_by']) : 'purchase_id' );
        $sql .=           " ". ( !empty($filters['order']) ? $this->db->escape($filters['order']) : ' DESC ' )." ";
        if(!$for_total){
            $sql .= " LIMIT " . (!empty($filters['limit']) ? (int)$filters['limit'] : ' 10 ' );
            $sql .= " OFFSET " . (!empty($filters['offset']) ? (int)$filters['offset'] : ' 0 ' );
        }

        $result = $this->db->query($sql);

        if( $result->num_rows > 0){

                return $result->rows;
        }
    }

    /**
     * Method to getProducts for inventory listing
     * Input(s):
     * @param array  data
     * Return array of product info
     * Author: Murtaza
     */
    public function getProducts($data = array()) {
        $this->db->query("SET SESSION group_concat_max_len = 1000000;");
        $sql = "SELECT owpb.breakup_id,
                        owpb.purchase_id,
                        owpb.product_id,
                        owpb.sku,
                        owpb.pieces,
                        CONCAT('[', GROUP_CONCAT( CONCAT( '{\"pieces\"' ,':\"', owpb.pieces, '\",\"invoice_date\":\"' , owp.invoice_date ,'\"}' ) ORDER BY invoice_date ASC ) ,']' ) as purchase_data,
                        owp.seller_id,
                        oms.nickname,
                        owp.purchase_firm_id,
                        owp.invoice_date,
                        owp.invoice_no,
                        op.model,
                        op.sku,
                        op.image,
                        op.price,
                        op.store_sales
                FROM oc_wsb_purchase owp
                INNER JOIN oc_wsb_purchase_breakup owpb ON owp.purchase_id = owpb.purchase_id
                INNER JOIN oc_product op ON owpb.product_id = op.product_id
                INNER JOIN oc_ms_seller oms ON owp.seller_id = oms.seller_id ";
        //sum(owpb.pieces) as total_purchase
        $sql .= " WHERE op.store_sales != 'NO' ";

        if(!empty($data['nickname'])){
            $sql .= " AND oms.nickname LIKE '%".$this->db->escape($data['nickname'])."%' ";
        }
        if(!empty($data['sku'])){
            $sql .= " AND op.sku LIKE '%".$this->db->escape($data['sku'])."%' ";
        }
        if(!empty($data['store_sales'])){
            $sql .= " AND op.store_sales LIKE '%".$this->db->escape($data['store_sales'])."%' ";
        }
        if(!empty($data['filter_date_added_from'])){
            $sql .= " AND DATE(owp.invoice_date) >= '".$this->db->escape($data['filter_date_added_from'])."' ";
        }
        if(!empty($data['filter_date_added_to'])){
            $sql .= " AND DATE(owp.invoice_date) <= '".$this->db->escape($data['filter_date_added_to'])."' ";
        }
        $sql .=" GROUP BY op.product_id ";
        $sort_data = array(
            'oms.nickname',
            'op.sku',
            'op.store_sales',
            'owp.invoice_date'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY owpb.purchase_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {

            array_walk($query->rows,function(&$val,$key){
                $val['purchase_data'] = json_decode($val['purchase_data'],TRUE);
            });
            return array_combine(array_column($query->rows, 'product_id'), $query->rows );
        }
    }

    /**
     * Method to get Total Entries for Pagination
     * Input(s):
     * @param array  data
     * Return total.
     * Author: Murtaza
     */
    public function getProductsTotal( $data = array() ){
        //$this->db->query("SET SESSION group_concat_max_len = 1000000;");
        $sql = "SELECT COUNT( DISTINCT owpb.product_id) as total
                FROM oc_wsb_purchase owp
                INNER JOIN oc_wsb_purchase_breakup owpb ON owp.purchase_id = owpb.purchase_id
                INNER JOIN oc_product op ON owpb.product_id = op.product_id
                INNER JOIN oc_ms_seller oms ON owp.seller_id = oms.seller_id ";
        //sum(owpb.pieces) as total_purchase
        $sql .= " WHERE op.store_sales != 'NO' ";


        if(!empty($data['nickname'])){
            $sql .= " AND oms.nickname LIKE '%".$this->db->escape($data['nickname'])."%' ";
        }
        if(!empty($data['sku'])){
            $sql .= " AND op.sku LIKE '%".$this->db->escape($data['sku'])."%' ";
        }
        if(!empty($data['store_sales'])){
            $sql .= " AND op.store_sales LIKE '%".$this->db->escape($data['store_sales'])."%' ";
        }

        if(!empty($data['filter_date_added_from'])){
            $sql .= " AND DATE(owp.invoice_date) >= '".$this->db->escape($data['filter_date_added_from'])."' ";
        }
        if(!empty($data['filter_date_added_to'])){
            $sql .= " AND DATE(owp.invoice_date) <= '".$this->db->escape($data['filter_date_added_to'])."' ";
        }
        // if(!empty($data['filter_purchase_value_from'])){
        //     $sql .= " AND total_purchase_value >= ".(float)$data['filter_purchase_value_from']." ";
        // }
        // if(!empty($data['filter_purchase_value_to'])){
        //     $sql .= " AND total_purchase_value <= ".(float)$data['filter_purchase_value_to']." ";
        // }

        $query = $this->db->query($sql);
        if ( $query->num_rows ) {
            return $query->row['total'];
        } else {
            return false;
        }
    }
    /**
     * Method to get Sales according to product_id
     * Input(s):
     * @param array product id (pid)
     * Return Sales product_id wise
     * Author: Murtaza
     */
    public function getSales($pid) {
        $sql = "SELECT  product_id,
                        (oop.quantity * oop.piece_in_set) AS pieces,
                        os.date_added AS sales_date
                FROM oc_suborder os
                INNER JOIN oc_order_product oop ON os.suborder_id = oop.suborder_id
                WHERE oop.product_id IN (". implode( "," , $pid ) .") AND
                      os.order_status_id != 0 AND
                      os.order_status_id != 2
                ORDER BY os.date_added ASC";

        $query = $this->db->query($sql);

        $result = array();

        if ( $query->num_rows ) {
            $result = array();
            foreach ( $query->rows as $row ) {
               $result[$row['product_id']][] = $row;
            }
            return $result;
        }
    }
    /**
     * Method - Calculate Age of product in store.
     * Input(s):
     * @param array of sale_data & Purchase_data
     * This method calculate age & age per piece of particular product.
     * Author: Murtaza
     */
    public function calculateAging( $sale_data , &$purchase_data ){

        $aging = array();
        $remain_quantity = 0;
        foreach ( $purchase_data as $key => $purchase ) {
            if($purchase['pieces'] > 0){
                $purchase_date = $purchase['invoice_date'];
                $purchase_quantity = $purchase['pieces'];
                $remain_quantity += $purchase_quantity;

                foreach ( $sale_data as $i => $sale) {

                    $sale_date = $sale['sales_date'];
                    $sale_quantity = (int)$sale['pieces'];
                    $remain_quantity = $remain_quantity - $sale_quantity;

                    //echo "$purchase_date-----$sale_date  ";
                    
                    $age_sale_purchase_date_diff =  (int)dateDifference( $purchase_date ,  $sale_date );
                    if( $remain_quantity <= 0 ){
                        // purchase not available
                        $purchase_data[$key]['age'] = (int)$age_sale_purchase_date_diff;
                        $purchase_data[$key]['age_per_piece'] = (int)$age_sale_purchase_date_diff / (int)$purchase_quantity;
                        unset($sale_data[$i]);
                        break;
                    }
                    else{
                        $purchase_data[$key]['age'] = (int)$age_sale_purchase_date_diff;
                        $purchase_data[$key]['age_per_piece'][] = (int)$age_sale_purchase_date_diff / (int)$sale_quantity;
                        unset($sale_data[$i]);
                    }
                }
            }
        }
    }

    public function calculateAgin2g( $sale_data , $purchase_data ){
        $aging = array();
        $remain_quantity = 0;
        foreach ( $purchase_data as $purchase ) {

            $purchase_date = $purchase['invoice_date'];
            $purchase_quantity = (int)$purchase['pieces'];
            $remain_quantity += $purchase_quantity;


            foreach ( $sale_data as $sale) {

                $sale_date = $sale['sales_date'];

                $sale_quantity = (int)$sale['pieces'];

                $remain_quantity = $remain_quantity - $sale_quantity;

                if( $remain_quantity <= 0 ){
                    // purchase not available
                    $sale_purchase_date_diff =  (int)dateDifference( $purchase_date ,  $sale_date );
                    $aging[] = $purchase_quantity / $sale_purchase_date_diff;
                }
            }
        }
        return $aging;
    }

    /**
    * update product table with stock wiht quantity
    * update product table with sor_product wiht 1
    * update product table with sor_invoce_id with value
    * @author: Kalyan
    */
    public function updateSorProductsWithStock($data){
        
        $status = '';

        if ($data['sor_order']==1) {

            if (isset($data['status'])) {
                $status = ", status='0' ";
            }

            $sql = "UPDATE `" . DB_PREFIX . "product` SET quantity = '" . (int)$data['pieces'] . "',sor_product ='1',wsb_purchase_id = '" . (int)$data['invoice_id'] . "' ".$status." WHERE product_id = '" . (int)$data['product_id'] . "' AND sku = '" . $this->db->escape($data['sku']) . "'";
        }else{
            
            if (isset($data['status'])) {
                $status = ", status='0' ";
            }

            $sql = "UPDATE `" . DB_PREFIX . "product` SET quantity = '" . (int)$data['pieces'] . "' ".$status." WHERE product_id = '" . (int)$data['product_id'] . "' AND sku = '" . $this->db->escape($data['sku']) . "'";
        }        

        $this->db->query($sql);
    }
    /**
    *
    * Check seller id against product id
    * if not found than insert new record
    * @author: Kalyan
    */
    public function checkValidSellerAgainstProduct($productId, $sellerId){
       
        $sql = "SELECT  seller_id
                FROM `" . DB_PREFIX . "ms_product`
                WHERE product_id= '" . (int)$productId . "'";

        $query = $this->db->query($sql);

        $result = array();

        if ( $query->num_rows >0) {

            $result = $query->row;

            if ($result['seller_id']!=$sellerId) {
                return false;
            }else{
                return true;
            }

        }else{

            $sql = "INSERT INTO `". DB_PREFIX . "ms_product` (product_id,seller_id) values ('" . (int)$productId. "','" . (int)$sellerId. "')";

            $this->db->query($sql);

            return true;
        }
    }
    /**
     * Method - get pick up city code.
     * @param seller_id
     * @param purchase_firm_id
     * @author: Kalyan 19th Sept. 2017
     */
    public function getPickUpCityCode($sellerId, $purchaseFirmId){
        
        $sql = "SELECT  pickup_city_code
                FROM `" . DB_PREFIX . "ms_seller`
                WHERE seller_id= '" . (int)$sellerId . "' AND purchase_firm_id= '" . (int)$purchaseFirmId . "'";

        $query = $this->db->query($sql);

        if ( $query->num_rows >0) {
            $result = $query->row;
            return $result['pickup_city_code'];
        }else{
            return 'not_found';
        }
    }
    /**
     * Method - get seller id based on purchase firm id.
     * @param purchase_firm_id
     * @author: Kalyan 13th Oct. 2017
     */
    public function getSellerId($purchaseFirmId){
        
        $sql = "SELECT  seller_id
                FROM `" . DB_PREFIX . "ms_seller`
                WHERE purchase_firm_id= '" . (int)$purchaseFirmId . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    /**
     * Method - check valid purchase for delete.
     * @param purchase_id
     * @author: Kalyan 17th Oct. 2017
     */
    public function validPurchaseForDelete($purchaseId){
       
        $sql = "SELECT  wsbp.purchase_id, 
                        wsbp.total_purchase_value, 
                        wsbp.payment_release_invoice_date_gap, 
                        wsbp.user, 
                        wsbp.invoice_date, 
                        wsbp.date_added, 
                        wsbp.invoice_no, 
                        vir.purchase_firm_name, 
                        ms.company, 
                        wsbpb.sku, 
                        wsbpb.pieces, 
                        wsbpb.breakup_id, 
                        wsbpb.transfer_price_per_piece, 
                        GROUP_CONCAT(DISTINCT otd.trxn_done) as trxns 
                FROM `" . DB_PREFIX . "wsb_purchase` wsbp 
                INNER JOIN oc_vat_input_rules vir ON wsbp.purchase_firm_id = vir.purchase_firm_id AND status='1'
                INNER JOIN oc_ms_seller ms ON wsbp.seller_id = ms.seller_id
                LEFT JOIN  oc_wsb_purchase_breakup wsbpb ON wsbp.purchase_id = wsbpb.purchase_id 
                LEFT JOIN  oc_trxn_details otd ON otd.trxn_for_id = wsbp.purchase_id AND otd.trxn_for = 'WSB_PURCHASE' 
                WHERE wsbp.purchase_id = '" . (int)$purchaseId . "' 
                  AND wsbp.payment_cleared = 'NO' 
                GROUP BY wsbpb.breakup_id 
                HAVING (trxns IS NULL OR trxns = 'NOT_DONE') ";

        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    /**
     * Method - Delete wsb purchase
     * @param purchase_id
     * @author: Kalyan 17th Oct. 2017
     */
    public function deleteWsbPurchase($purchaseId){
       
        $sql = "DELETE FROM `" . DB_PREFIX . "wsb_purchase` 
                WHERE purchase_id= '" . (int)$purchaseId . "' ";

        if ($this->db->query($sql)) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * Method - check product sor purchase is second time
     * @param product_id
     * @author: Kalyan 25th Oct. 2017
     */
    public function checkSorPurchaseIsSecondTime($productId){
       
        $sql = "SELECT wsbpb.product_id 
                FROM `" . DB_PREFIX . "wsb_purchase_breakup` wsbpb                   
                WHERE wsbpb.product_id= '" . (int)$productId . "'";
              
        $query = $this->db->query($sql);

        if ( $query->num_rows >0) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * Method - check duplicate wsb purchase invoice no
     * @param seller_id
     * @param invoice_no
     * @return true or false
     * @author: Kalyan 2nd Nov. 2017
     */
    public function checkDuplicatWsbPurchase($sellerId,$invoiceNo){
       
        $sql = "SELECT wsbp.invoice_no 
                FROM `" . DB_PREFIX . "wsb_purchase` wsbp 
                WHERE wsbp.seller_id= '" . (int)$sellerId . "' AND wsbp.invoice_no= '" . $this->db->escape($invoiceNo) . "'";

        $query = $this->db->query($sql);

        if ( $query->num_rows >0) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * Method - get wsb purchase inoice image
     * @param purchase_id
     * @return image name
     * @author: Kalyan 16th Nov. 2017
     */
    public function checkDownloadWsbPurchaseInvoiceImage($purchase_id){

        $sql = "SELECT wsbp.purchase_bill_image, s.nickname 
                FROM `" . DB_PREFIX . "wsb_purchase` wsbp 
                INNER JOIN `" . DB_PREFIX . "ms_seller` s
                ON wsbp.seller_id=s.seller_id 
                WHERE wsbp.purchase_id= '" . (int)$purchase_id . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    /**
     * Method - for get seller id in sor condition.
     * @param purchase_firm_id
     * @return seller_id
     * @author: Kalyan 2nd Dec. 2017
     */
    public function getSorSellerId($purchase_firm_id){
        
        $sql = "SELECT  seller_id
                FROM `" . DB_PREFIX . "ms_seller`
                WHERE purchase_firm_id= '" . (int)$purchase_firm_id . "'";

        $query = $this->db->query($sql);

        if ( $query->num_rows >0) {
            
            return $query->row['seller_id'];            
        }else{
            return 'not_found';
        }
    }
}
