<?php
/**
 * 	Split Order by city and shipment
 *  @info: We have get single order with one or more products from buyer then we want to these order split by city and shipment.
 *
 */
require(__DIR__.'/../../cart.php');
require(__DIR__.'/../../language.php');
class SplitOrder {

    public $db;
    public $cart_data;
    public $suborder = true;
    public $language;
    public $order_id;
    public $gst;
    public $suborder_id;
    private $_customer_prepaid_suborders = array();

    private $_check_subtotal_in = array();
    private $_give_subtotal_of = array();
    private $_skip_rejected_products_in_splitting = false;

    private $_suborder_id = '';
    private $_suborder_wise_product = false;

    public function __construct( $db ){
        $this->db = $db;
    }

    public function setOptions( $flag_name, $flag_value ){
        if( in_array( $flag_name ,
                      array( 'check_subtotal_in' ,
                             'give_subtotal_of' ,
                             'skip_rejected_products_in_splitting',
                             'suborder_wise_product'
                     )
            )){
            $this->{'_'.$flag_name} = $flag_value;
        }
        else{
            throw new Exception("You can only set 'check_subtotal_in' && 'give_subtotal_of' ");
        }
    }

    public function splitByCity( $order_id, $gst=1 ){
        $this->gst = $gst;

        // To avoid inner joins, we will use individual queries.
        // This is because, oc_order_product table will have milions of rows
        // in future, and join operations will be extremely heavy of server
        // RAM, speed and table locking considerations
        // We will have individual one-to-one field maps

        // map of order_product_id to product_id
		$oop_pid_map = $this->getOrderProductIdToProductIdMap( $order_id );

        // map of product_id to seller_id
		$pid_sid_map = $this->getProductIdToSellerIdMap($oop_pid_map);

        // map of seller_id to pickup_city_code
		$sid_pcc_map = $this->getSellerIdToPickupCityCode($pid_sid_map); // map of seller_id to pickup_city_code

		// map of pickup_city_code to suborder_id
		$pcc_suborder_map = $this->getPickupCityCodeToSuborderIdMap( $order_id , $sid_pcc_map );

        try{
            // Start transaction            
            $this->db->query( " START TRANSACTION " );

            $key_in = array('order_id'=> $order_id,
                            'suborder_id'=> $this->_suborder_id
                           );
            $key_out = array();

            // Copy Order to multiple suborders (oc_order)
            foreach ( $pcc_suborder_map as $suborder_id) {
                $key_out[] = array('order_id'=> $order_id,
                                   'suborder_id'=> $suborder_id
                                  );
            }

            $this->db->copyRow( DB_PREFIX . 'suborder',
                                 $key_in,
                                 $key_out,
                                 true
                             );


            // Assigning suborders productwise in oc_order_product
            $this->splitOrderProducts(
                                      $oop_pid_map,
                                      $pcc_suborder_map,
                                      $sid_pcc_map,
                                      $pid_sid_map
                                     );

            $this->splitOrderProductsOptions(
                                       $order_id,
                                       $oop_pid_map,
                                       $pcc_suborder_map,
                                       $sid_pcc_map,
                                       $pid_sid_map
                                      );


            // Update oc_order_history (handled by operations classes)
            $this->db->copyRow( DB_PREFIX . 'order_history',
                                 $key_in,
                                 $key_out,
                                 true,
                                 array('order_history_id')
                             );


            $this->updateSuborderShippingTotal( $order_id );

            $suborders_total = $this->splitOrderTotals( $order_id, '', '', $this->gst );

            $this->updateSuborderTotal( $order_id, $suborders_total );

            $this->db->query( " COMMIT " );
        }catch(Exception $e){
            $this->db->query( " ROLLBACK " );
            echo $e->getMessage();
        }

    }

    /**
     * [getOrderProductIdToProductIdMap -- create a order_product_id to product_id map and set it to
     *                                      a SplitOrder::_oop_pid_map and return the same ]
     * @return [array] [return array of order_product_id to product_id ]
     */
    private function getOrderProductIdToProductIdMap($order_id){
		$sql = "SELECT order_product_id,
		               product_id
				FROM ". DB_PREFIX ."order_product
				WHERE order_id = '".(int)$order_id."'";
		$query = $this->db->query($sql);



		if ( !$query->num_rows ) { // no products found
			return false; // not possible, splitting cant be done
		}

		$oop_pid_map = array_combine(
						  array_column($query->rows, 'order_product_id'),
						  array_column($query->rows, 'product_id')
						);
        return $oop_pid_map;

    }

    /*
    * getProductIdToSellerIdMap -- get seller_id from oc_ms_product by unique_product_ids and
    *                              fill in product to seller map
    * @param - array of order product id to product id map
    */
    private function getProductIdToSellerIdMap($oop_pid_map){
        $pid_sid_map = array();
		foreach (array_unique($oop_pid_map) as $key => $product_id) {
			$sql = "SELECT seller_id
					FROM ". DB_PREFIX ."ms_product
					WHERE product_id = '" . (int)$product_id . "'";
			$query = $this->db->query($sql);

            if ( $query->num_rows != 1 ) {
				// something is wrong. a product must have exactly one seller
				return false;
			}

			$pid_sid_map[$product_id] = $query->row['seller_id'];
		}
        return $pid_sid_map;
    }


    /*
    * getSellerIdToPickupCityCode -- get pickup_city_code from oc_ms_product by unique seller_ids
    *                                and fill in seller to city code map
    */
    private function getSellerIdToPickupCityCode($pid_sid_map){
        $sid_pcc_map = array();
		foreach (array_unique($pid_sid_map) as $key => $seller_id) {
			$sql = "SELECT pickup_city_code
					FROM " . DB_PREFIX . "ms_seller
					WHERE seller_id = '" . (int)$seller_id . "'";
			$query = $this->db->query($sql);

			if ( !$query->num_rows ) {
				// Invalid seller id
				return false;
			}

			$sid_pcc_map[$seller_id] =  strtoupper(trim($query->row['pickup_city_code']));
		}

        return $sid_pcc_map;
    }
    /**
     * [splitOrderProductsOptions description]
     */
    private function splitOrderProductsOptions( $order_id, $oop_pid_map, $pcc_suborder_map, $sid_pcc_map, $pid_sid_map){
        $sql = "SELECT order_product_id
                FROM ".DB_PREFIX."order_option
                WHERE order_id = '" . (int)$order_id . "' ";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            // Assigning suborders productwise in oc_order_product

            foreach ( $result->rows as $row ) {
                $sql = "UPDATE " . DB_PREFIX . "order_option
                        SET suborder_id = '" .
                        $this->db->escape( $pcc_suborder_map[$sid_pcc_map[$pid_sid_map[$oop_pid_map[$row['order_product_id']]]]] ) . "'
                        WHERE order_product_id = '" . (int)$row['order_product_id'] . "'";
                $this->db->query($sql);
            }
        }
    }

    /**
     * [getPickupCityCodeToSellerIdMap description]
     * @param  [int] $order_id    [order id]
     * @param  [array] $sid_pcc_map [seller_id => pickup_city_code]
     * @return [type]              [description]
     */
    private function getPickupCityCodeToSuborderIdMap( $order_id , $sid_pcc_map ){
        $pcc_suborder_map = array();
        // get order no
		$order_no = $this->db->query("SELECT order_no FROM " . DB_PREFIX . "order
			                          WHERE order_id = '" . (int)$order_id . "'")->row['order_no'];

		// Populating $pcc_suborder_map
		foreach ( array_unique($sid_pcc_map) as $key => $pickup_city_code ) {
			$pcc_suborder_map[$pickup_city_code] = $order_no . '-' . $pickup_city_code ;
		}
        $this->_suborder_id = $order_no;
        return $pcc_suborder_map;
    }

    private function splitOrderProducts( $oop_pid_map, $pcc_suborder_map, $sid_pcc_map, $pid_sid_map ){
        // Assigning suborders productwise in oc_order_product
        foreach ( $oop_pid_map as $order_product_id => $product_id ) {

            $sql = "UPDATE " . DB_PREFIX . "order_product
                    SET suborder_id = '" .
                    $this->db->escape($pcc_suborder_map[$sid_pcc_map[$pid_sid_map[$oop_pid_map[$order_product_id]]]]) . "'
                    WHERE order_product_id = '" . (int)$order_product_id . "'";
            $this->db->query($sql);
        }
    }

    /**
     * Method to split a given amount by Subtotals of Suborders in an Order
     * @param $value - number which is to be splitted
     * @param $order_id - int corresponding to order_id
     * @param $suborder_id - not mandatory - string
     * @return - if $suborder_id given and is valid for $order_id, then returns only the
     *           splitted value for that particular suborder
     *           if $suborder_id given and is invalid, returns false
     *           if $suborder_id not given, then returns array ($suborder_id => splitted value for that suborder)
     * @author Sudhanshu, Madhur - 2017
     */
    public function splitBySubtotal($value, $order_id, $suborder_id = array()){

        $grand_subtotal = 0;

        // Get suborder_id to suborder breakup
        $sql  = "SELECT  (price_per_piece * quantity * piece_in_set) as total, os.order_id, os.suborder_id ";
        $sql .=         "FROM " . DB_PREFIX . "order_product oop ";
        $sql .=         "INNER JOIN oc_suborder os ON ( os.suborder_id = oop.suborder_id )";
        $sql .=         "WHERE os.order_id = '" . (int)$order_id . "' AND ";
        $sql .=               "os.order_status_id != 2 ";
        if(!empty($this->_customer_prepaid_suborders)){
            $sql .=    " AND os.suborder_id NOT IN ('". implode("','",$this->_customer_prepaid_suborders) ."') ";
        }
        if(!empty($this->_check_subtotal_in)){
            $sql .=    " AND os.suborder_id IN ('". implode("','",$this->_check_subtotal_in) ."') ";
        }
        $product = $this->db->query($sql);
        $suborder_to_subtotal = array();
        if($product->num_rows > 0){

            foreach ( $product->rows as $key => $product ) {
                if(empty($suborder_to_subtotal[$product['suborder_id']])){
                    $suborder_to_subtotal[$product['suborder_id']] = (float)$product['total'];
                }
                else{
                    $suborder_to_subtotal[$product['suborder_id']] += (float)$product['total'];
                }

                $grand_subtotal += (float)$product['total'];
            }
        } else {
            return false;
        }

        // Looping over suborders to find the split proportion for them
        $suborder_to_value = array();

        $num_suborders = count($suborder_to_subtotal);
        $total_splitted_value = 0;
        $i = 0;
        foreach($suborder_to_subtotal as $sid => $subtotal) {
            // Last splitted value has to be a balance always (to remove rounding off errors)
            if(++$i === $num_suborders) {
                $suborder_to_value[$sid] = (float)$value - $total_splitted_value;
            }

            $splitted_value = round((float)$value*($suborder_to_subtotal[$sid]/$grand_subtotal), 2);
            $total_splitted_value += $splitted_value;
            $suborder_to_value[$sid] = $splitted_value;

            // unset the value which are not in input param $suborder_id for return

            if(!empty($suborder_id) && is_array($suborder_id) && !in_array($sid,$suborder_id)){
                unset($suborder_to_value[$sid]);
            }
        }

        if (empty($suborder_id) ||  (is_array($suborder_id) && !empty($suborder_id) )) {
            return $suborder_to_value;
        } else if(!empty($suborder_to_value[$suborder_id])){
            return $suborder_to_value[$suborder_id];
        }
        else{
            return 0;
        }
    }

    private function splitByWeight( $order_id ){
        $sql  = "SELECT (weight_per_piece * owc.value * quantity * piece_in_set) as weight , oop.order_id, oop.suborder_id ";
        $sql .=         "FROM " . DB_PREFIX . 'order_product oop ';
        $sql .=         "INNER JOIN ".DB_PREFIX."order o ON (oop.order_id = o.order_id) ";
        $sql .=         "INNER JOIN ".DB_PREFIX."weight_class owc ON (owc.weight_class_id = o.weight_class_id) ";
        $sql .=         "WHERE oop.order_id = '" . $order_id . "'";
        $product = $this->db->query($sql);
        $suborder_to_weight = array();
        if($product->num_rows > 0){
            foreach ( $product->rows as $key => $product ) {
                if(empty($suborder_to_weight[$product['suborder_id']])){
                    $suborder_to_weight[$product['suborder_id']] = (float)$product['weight'];
                }
                else{
                    $suborder_to_weight[$product['suborder_id']] += (float)$product['weight'];
                }

            }
        }
        return $suborder_to_weight;
    }


    /**
     * splitOrderTotals This method returns the suborder total. if suborder_id and product_id are empty then it return order total
     * of all suborder which are in suborder table if order is not splitted it return a single suborder total
     *
     * @param  int $order_id    Order Id
     * @param  int $suborder_id - Suborder Id, It is not mandetory. if suborder_id is empty, it returns total of all suborders and
     *                              if it is not empty it shows given suborder total
     * @param  array $products  - Products is array of products which you want to set for a suborder or an order. if it is empty, it
     *                            gets data from db.
     * @return array            - It returns the array of totals
     */
    public function splitOrderTotals( $order_id , $suborder_id = '', $products = '', $gst=0 ){
       
        $suborder_to_products = array();
        if(empty($products)){
            $sql  = "SELECT order_product_id,";
            $sql .=        "order_id,";
            $sql .=        "suborder_id,";
            $sql .=        "product_id,";
            $sql .=        "quantity,";
            $sql .=        "piece_in_set,";
            $sql .=        "(quantity * piece_in_set) as total_pieces,";
            $sql .=        "(quantity * piece_in_set * price_per_piece) as total,";
            $sql .=        "piece_in_set,";
            $sql .=        "price_per_piece,";
            $sql .=        "weight_per_piece,";
            $sql .=        "discount_per_piece,  ";
            $sql .=        "discount_breakup,  ";
            $sql .=        "tax,  ";
            $sql .=        "output_tax_rates  ";
            $sql .=  "FROM " . DB_PREFIX . "order_product ";
            $sql .=  "WHERE order_id = ' $order_id '";
            if(!empty($suborder_id)){
                $sql .=  " AND suborder_id = '$suborder_id'";
            }
            if($this->_skip_rejected_products_in_splitting){
                $sql .=  " AND edit_type IN ('YES','SELLER_LATER_DISPATCH') ";
            }
            $result = $this->db->query($sql);
            foreach ( $result->rows as $key => $value ) {
                $suborder_to_products[$value['suborder_id']][$value['order_product_id']] = $value;
            }
        }
        else if(!$this->_suborder_wise_product){
            $key = !empty($suborder_id) ?  $suborder_id : $order_id;
            $suborder_to_products = array(
                                        $key => $products,
                                    );
        }
        else{
            $suborder_to_products = $products;
        }
        $this->language = new Language(DIR_LANGUAGE);
        $this->order_id = $order_id;
        foreach( $suborder_to_products as $suborder_id => $products ){
            $this->suborder_id = $suborder_id;
            $suborder_ids[] = $suborder_id;
            $this->cart_data = $products;
            $this->gst = $gst;
            $totalfactory = new TotalFactory($this);
            $total_data = $totalfactory->getTotal(true);
            $total_data = array_combine(
                            array_column( $total_data, 'code' ),
                            $total_data
                          );
            $totals[$suborder_id] = $total_data;
        }
        return $totals;
    }

    public function updateSuborderTotal( $order_id,  $suborders_totals ){
        $sql  = "UPDATE ".DB_PREFIX."suborder ";
        $sql .=       "SET total = CASE " ;
        foreach( $suborders_totals as $suborder_id => $total){
            $sql .=               "WHEN suborder_id = '".$suborder_id."' THEN '".$total['total']['value']."' ";
        }
        $sql .=                  " END ";
        $sql .= "WHERE order_id = '$order_id' AND ";
        $sql .=       "suborder_id IN ('".implode( "','" , array_keys( $suborders_totals ) )."')";
        $this->db->query($sql);
    }

    private function updateSuborderShippingTotal($order_id){
        $suborder_to_weight = $this->splitByWeight( $order_id );
        $order_info = $this->getShippingDetails($order_id);
        $shipping_total = $order_info['shipping_charge'];
        $shipping_cost = (float)$shipping_total / (float)$order_info['weight'];
        if( $shipping_cost > 0){

            end($suborder_to_weight);         // move the internal pointer to the end of the array
            $last_suborder_id = key($suborder_to_weight);

            $sql  = "UPDATE ".DB_PREFIX."suborder ";
            $sql .=       "SET shipping_charge = CASE " ;
            $shipping_suborders_total = 0;
            foreach ( $suborder_to_weight as $suborder_id => $weight) {
                if( $suborder_id == $last_suborder_id){
                    $shipping = number_format($shipping_total - $shipping_suborders_total,'2','.','');
                    $sql .=        "WHEN suborder_id = '".$suborder_id."' THEN '".$shipping."' ";
                }
                else{
                    $shipping = number_format((float)$weight * (float)$shipping_cost,'2','.','');
                    $shipping_suborders_total += $shipping;
                    $sql .=        "WHEN suborder_id = '".$suborder_id."' THEN '". $shipping ."' ";
                }
            }
            $sql .=                  " END ";
            $sql .= "WHERE order_id = '$order_id' AND ";
            $sql .=       "suborder_id IN ('".implode( "','" , array_keys( $suborder_to_weight ) )."')";
            $this->db->query($sql);
        }
    }

    private function getShippingDetails($order_id){
        $sql  = "SELECT o.shipping_charge, ";
        $sql .=        "sum(oop.quantity * oop.piece_in_set * oop.weight_per_piece * owc.value ) as weight, ";
        $sql .=        "osub.shipping_method ";
        $sql .=        "FROM  ".DB_PREFIX."order o ";
        $sql .=        "INNER JOIN ".DB_PREFIX."suborder osub ON (o.order_id = osub.order_id) ";
        $sql .=        "INNER JOIN ".DB_PREFIX."order_product oop ON (osub.suborder_id = oop.suborder_id) ";
        $sql .=        "INNER JOIN ".DB_PREFIX."weight_class owc ON ( o.weight_class_id = owc.weight_class_id) ";
        $sql .=       "WHERE oop.order_id = '".(int)$order_id."'";
        $result = $this->db->query($sql);
        if(!empty($result->row)){
            $order_info = $result->row;
            return $order_info;
        }
    }
}

?>
