<?php
class ModelAccountOrder extends Model {
	public function getOrder($order_id, $is_seller=false) {

		$q = "SELECT o.*, ";
		$q .=       "osub.shipping_method as shipping_method, ";
		$q .=       "osub.order_status_id as order_status_id, ";
		$q .=       "osub.cform_submit as cform_submit,";
		$q .=       "osub.cst_with_cform as cst_with_cform, ";
		$q .=       "osub.refundable_cform as refundable_cform ";
		$q .=   "FROM `" . DB_PREFIX . "order` o INNER JOIN ";
		$q .=      	  "`". DB_PREFIX . "suborder` osub USING (order_id) ";
		$q .=   "WHERE o.order_id = '" . (int)$order_id . "'";
		if($is_seller == false) {
			$q .= " AND o.customer_id = '" . (int)$this->customer->getId() . "'";
		}
		$q .= " AND o.order_status_id > '0'";

		$order_query = $this->db->query($q);
		//$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
				$shipping_zone = $zone_query->row['name'];
			} else {
				$shipping_zone_code = '';
				$shipping_zone = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
                'order_no'                => $order_query->row['order_no'],
				'invoice_no'              => isset($order_query->row['invoice_no'])?$order_query->row['invoice_no']:'',
				'invoice_prefix'          => isset($order_query->row['invoice_prefix'])?$order_query->row['invoice_prefix']:'',
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postgetOrder_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_city'     => $order_query->row['shipping_city'],
				'shipping_postcode'     => $order_query->row['shipping_postcode'],
				'shipping_zone'     => $shipping_zone,
				'shipping_zone_code'     => $shipping_zone_code,
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => isset($order_query->row['shipping_method']) ? $order_query->row['shipping_method'] : '',
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'cform_submit'           => $order_query->row['cform_submit'],
				'cst_with_cform'          => $order_query->row['cst_with_cform'],
				'refundable_cform'          => $order_query->row['refundable_cform'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}

	public function getOrders($start = 0, $limit = 20, $user_id) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

        // Showing orders from all the stores
        /*$query = $this->db->query("SELECT 	o.order_id,
        									o.order_no,
        									osub.invoice_no,
        									osub.suborder_id,
        									o.firstname,
        									o.lastname,
        									osub.order_status_id,
        									os.name as status,
        									o.date_added,
        									o.total,
        									osub.total as suborder_total,
        									o.currency_code,
        									o.currency_value
        							FROM `" . DB_PREFIX . "order` o
        							INNER JOIN " . DB_PREFIX . "suborder osub
        								ON (osub.order_id = o.order_id)
        							LEFT JOIN " . DB_PREFIX . "order_status os
        								ON (osub.order_status_id = os.order_status_id)
        							WHERE o.customer_id = '" . (int)$this->customer->getId() . "'
        								AND osub.order_status_id > '0'
        								AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
        								AND o.store_id IN (" . WSB_STORES_ID . ")
        							ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);*/

        $query = $this->db->query("SELECT  DISTINCT o.order_id
                                    FROM `" . DB_PREFIX . "order` o
                                    INNER JOIN " . DB_PREFIX . "suborder osub
                                        ON (osub.order_id = o.order_id)
                                    WHERE o.customer_id = '" . (int)$this->customer->getId() . "'
                                        AND osub.order_status_id > '0'  
                                        AND o.store_id IN (" . WSB_STORES_ID . ")
                                    ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);
        if( $query->num_rows ){
            return $query->rows;
        } else {
            return false;
        }


	}

	public function getOrderProduct($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT oop.*, op.image FROM " . DB_PREFIX . "order_product oop INNER JOIN " . DB_PREFIX . "product op ON op.product_id = oop.product_id WHERE order_id = '" . (int)$order_id . "' ORDER BY oop.order_product_id ASC");

		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT *, pov.option_image FROM " . DB_PREFIX . "order_option oo
			INNER JOIN ".DB_PREFIX."product_option_value pov ON(oo.product_option_value_id = pov.product_option_value_id) WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT sum(value) as value, code, title, sort_order FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' GROUP BY code ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderHistories($order_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify_email FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");

		return $query->rows;
	}

	public function getTotalOrders($customer_id) {

		// Getting count of all the Orders from WSB stores for the Customer
		$query = $this->db->query("SELECT COUNT( DISTINCT o.order_id ) AS total_orders
							FROM `" . DB_PREFIX . "order` o
							INNER JOIN " . DB_PREFIX . "suborder osub
								ON (osub.order_id = o.order_id)
							WHERE o.customer_id = '" . (int)$this->customer->getId() . "'
								AND osub.order_status_id > '0'  
								AND o.store_id IN (" . WSB_STORES_ID . ")");
		return $query->row['total_orders'];
	}
	public function getNetOrders() {

	        // Showing orders from all the stores
	        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE  o.order_status_id > '0'");

	  return $query->row['total'];
	 }
	public function getTotalOrderProductsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderVouchersByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getCustomerHasPreviousOrder($customer_id, $order_id){
		$sql = "SELECT * FROM `" . DB_PREFIX . "order` WHERE customer_id = '$customer_id' AND order_status_id = '5'AND order_id != '$order_id'";
		$query = $this->db->query($sql);
		return count($query->rows);
	}

	public function getcustomeridbyreferralcode($referral_code)
	{
		$sql = "SELECT customer_id, firstname FROM " . DB_PREFIX . "customer WHERE referral_code = '$referral_code' ";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function addTransaction($customer_id, $amount, $order_id = 0)
	{
        $customer_info = $this->getCustomer($customer_id);
        if ($customer_info) {
		    $sql ="INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', amount = '" . (float)$amount . "', date_added = NOW()";
		    $query = $this->db->query($sql);
        }
	}

	public function getOrdersInTransit(){

        // Order status: 13 - Shipped, 14 - Shipped with Tracking, 17 - Delivery Issues
        // Getting suborders which are in transit
        $sql = "SELECT oh.suborder_id,
                       o.shipping_firstname,
                       o.shipping_lastname,
                       o.shipping_company,
                       o.shipping_city,
                       osub.total,
                       o.payment_code,
                       osub.date_added,
                       oh.date_added as date_history,
                       osub.courier_partner,
                       osub.tracking_no
                FROM oc_order AS o
                INNER JOIN oc_suborder AS osub ON osub.order_id = o.order_id
                INNER JOIN oc_order_history AS oh ON oh.order_id = osub.order_id
                WHERE osub.order_status_id IN (13,14,17)
                  AND oh.order_status_id = osub.order_status_id
                  AND o.store_id IN (".WSB_STORES_ID .")
                  AND osub.suborder_id = oh.suborder_id
                GROUP BY oh.suborder_id
                ORDER BY oh.date_added ASC";
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            $sql = "SELECT courier_name, tracking_url FROM oc_courier_partners WHERE 1";
            $couriers_query = $this->db->query($sql);
            $couriers = array_combine( array_column($couriers_query->rows, 'courier_name'),
                                       array_column($couriers_query->rows, 'tracking_url')
                                     );
            $result = $query->rows;
            foreach ($result as $key => $value) {

                $result[$key]['tracking_url'] = '';
                if ( !empty($couriers[$value['courier_partner']]) ) {
                    $result[$key]['tracking_url'] = $couriers[$value['courier_partner']];
                }
            }

            return $result;

        } else {
            return array();
        }
	}

	public function notificationForIdealCustomer(){

    	 $sql = "SELECT o.order_id, o.order_no, o.firstname, o.lastname, o.telephone, o.payment_company, o.total, o.date_added, oh.date_added as date_history
   			FROM `oc_order_history` AS oh
   			INNER JOIN oc_order o ON o.order_id = oh.order_id
   			WHERE (
   				DATEDIFF(NOW(),oh.date_added) < 30 AND
    			o.order_status_id = 5 AND oh.order_status_id = 5 AND o.store_id IN (".WSB_STORES_ID .") )
   			GROUP BY oh.order_id
   			ORDER BY oh.date_added ASC";
  			$query = $this->db->query($sql);
  			return $query->rows;
	}

    public function getProcessedDate($order_id, $suborder_id) {

        $query = $this->db->query("SELECT ooh.date_added FROM " . DB_PREFIX . "order_history ooh
                                   WHERE ooh.order_id = '" . (int)$order_id . "'
                                     AND ooh.suborder_id = '" . $this->db->escape($suborder_id) . "'
                                     AND ooh.order_status_id IN (9,16)
                                   ORDER BY ooh.date_added ASC LIMIT 1");

        if ($query->num_rows) {
            return $query->row['date_added'];
        }
        // nothing returned
        return false;
    }

	public function getOutForDeliveryOrders(){

        // Getting suborders which are in transit
        $sql = "SELECT oh.suborder_id,
                       o.firstname,
                       o.lastname,
                       o.shipping_city,
                       GROUP_CONCAT(oss.name) as sales_staff_name,
                       osub.total,
                       o.payment_code,
                       osub.date_added,
                       oh.date_added as date_history,
                       osub.courier_partner,
                       osub.tracking_no
                FROM oc_order AS o
                INNER JOIN oc_suborder AS osub ON osub.order_id = o.order_id
                INNER JOIN oc_order_history AS oh ON oh.order_id = osub.order_id
				LEFT JOIN oc_order_sales_staff as ooss ON ( o.order_id = ooss.order_id )
				LEFT JOIN oc_sales_staff as oss ON ( ooss.sales_staff_id = oss.staff_id )
                WHERE osub.order_status_id = 4
                  AND oh.order_status_id = 4
                  AND osub.suborder_id = oh.suborder_id
                  AND o.store_id IN (".WSB_STORES_ID .")
                GROUP BY oh.suborder_id
                HAVING DATEDIFF(NOW(),oh.date_added) >= 1
                ORDER BY oh.date_added ASC";
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            $sql = "SELECT courier_name, tracking_url FROM oc_courier_partners WHERE 1";
            $couriers_query = $this->db->query($sql);
            $couriers = array_combine( array_column($couriers_query->rows, 'courier_name'),
                                       array_column($couriers_query->rows, 'tracking_url')
                                     );
            $result = $query->rows;
            foreach ($result as $key => $value) {

                $result[$key]['tracking_url'] = '';
                if ( !empty($couriers[$value['courier_partner']]) ) {
                    $result[$key]['tracking_url'] = $couriers[$value['courier_partner']];
                }

                if ( !empty( $value['sales_staff_name'] ) ) {
                    $result[$key]['sales_staff_name'] = $value['sales_staff_name'];
                }else{
					$result[$key]['sales_staff_name'] = '--N/A--';
				}
            }

            return $result;

        } else {
            return array();
        }
	}

	public function getOrderStatus(){
		$sql = "SELECT name FROM " . DB_PREFIX . "order_status";
		$query = $this->db->query($sql);
		return $query->rows;
	}


	/** Method for get SUM payemnt received
	* @param : order_id : Integer for order id
	* @output : sum of payment received
	* @author : Vikas, 2017
	*/
    public function getSumPaymentReceived($order_id){
    	$sql = "SELECT SUM(amount) as payment_received
    			FROM " . DB_PREFIX . "order_payment
    			WHERE order_id ='".(int)$order_id."'
					AND successfull = '1'";

		$query = $this->db->query($sql);
		if($query->num_rows ){
			return $query->row['payment_received'];
		}else{
			return false;
		}

    }

    public function getOrderHistoryByStatus($order_id, $order_status_id) {
        $query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify
                                   FROM " . DB_PREFIX . "order_history oh
                                   LEFT JOIN " . DB_PREFIX . "order_status os
                                   ON oh.order_status_id = os.order_status_id
                                   WHERE oh.order_id = '" . (int)$order_id . "'
                                   AND oh.order_status_id = '".(int)$order_status_id."'
                                   AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
                                   ORDER BY oh.date_added");

        return $query->rows;
    }

    public function getWsbUpiVpa(){
    	$wsb_upi_vpa = '';
    	$sql = "SELECT * FROM oc_setting WHERE code = 'upi'";
    	$result = $this->db->query($sql);
    	if($result->num_rows){
    		foreach ($result->rows as $row) { 
    			if($row['key'] == 'upi_wsb_vpa') {
    				$wsb_upi_vpa = $row['value'];
    			}
    		}
    	}
    	return $wsb_upi_vpa;
    }

    public function setOrderProductReviews($order_product_id, $review) {
    	
    	$order_product_review_data = array();
    	$order_product_review_data['order_product_id'] = $order_product_id;
    	$order_product_review_data['product_review']   = $review;
    	$order_product_review_data['ip']               = getClientIpAddress();
    	$order_product_review_data['user_agent']       = $_SERVER['HTTP_USER_AGENT'];

        //---------Add/Update OrderProductReview in oc_order_product_review
    	OrderProductReview::addOrderProductReview($this->db, $order_product_review_data);

    	return true;
    }
		
		public function getTotalCouponCashbackDiscountByOrder($order_id) {
			$total_amount = 0;
			$sql = "SELECT amount FROM oc_order_payment
							WHERE payment_gateway IN ('cashback','coupon') AND order_id = '".$this->db->escape($order_id)."'";
			$result = $this->db->query($sql);
    	if($result->num_rows){
    		foreach ($result->rows as $row) { 
    			$total_amount += $row['amount'];
    		}
    	}
			return $total_amount;
		}
		
		public function checkInvoiceStatus($suborder_id) {
			$invoice_generated = false;
			$sql = "SELECT invoice_no, buyer_invoice_id FROM oc_suborder
							WHERE suborder_id = '".$this->db->escape($suborder_id)."' AND invoice_no > 0 AND buyer_invoice_id > 0";
							
			$result = $this->db->query($sql);
			if($result->num_rows) {
				$invoice_generated = true;
			}
			return $invoice_generated;
		}
		
		/*
			* @method: updateSuborder - update the suborder fields
			* @params: $update_fields - array of fields to update with value, ex -  array('no_wsb_tape=1', 'no_invoice_with_shipment=0')
			* @params: $order_id, $suborder_id
		  * @author: Devendra, June 2018
		 */
		public function updateSuborder($update_fields, $order_id, $suborder_id) {
			if (empty($update_fields) || empty($order_id)) return false;
			
			$sql = "UPDATE " . DB_PREFIX . "suborder SET " . implode(', ', $update_fields) . 
			 				" WHERE order_id='" . (int)$order_id . "' ";
							
			if (!empty($suborder_id)) {
				$sql .= " AND suborder_id='" . $this->db->escape($suborder_id) . "'";
			}
						
			if($this->db->query($sql)){
				return true;
			}
			
			return false;
		}

		/*
			* @method: checkOrderRatingAvailableOrNot
			* @params: $order_id 
		  * @author: Rahul, Sep 2019
		 */
		public function checkOrderRatingAvailableOrNot($order_id) {			
			$query = $this->db->query("SELECT COUNT(id) AS total
										FROM
										  `" . DB_PREFIX . "order_review`
										WHERE
										  order_id = '" . (int)$order_id . "'");

	  			return $query->row['total'];
						
		}
		/*
			* @method: orderReviewReasonsList
		  	* @author: Rahul, Sep 2019
		 */
		public function orderReviewReasonsList() {			
			$result = $this->db->query("SELECT id,reasons,type,show_comment 
										FROM `" . DB_PREFIX . "order_review_reasons_list` 
										WHERE status = '1'");
			$list=array();
	  		if($result->num_rows){
	    		foreach ($result->rows as $key=>$row) { 
	    			if($row['type'] == 'positive') {
	    				$list['positive_reasons'][] = $row;
	    			}else{
	    				$list['negative_reasons'][] = $row;
	    			}
	    		}
    		}
    	return $list;
						
		}
		/*
			* @method: saveOrderReview
			* @params: order_id,rating, comment
		  	* @author: Rahul, Sep 2019
		 */
		public function saveOrderReview($request) {			
			$sql = "INSERT INTO " . DB_PREFIX . "order_review SET "
                        . "order_id = '" . (int)$request['order_id']. "', "
                        . "rating = " . (int)$request['rating'] . ", "
                        . "comment = '" .$this->db->escape(trim($request['comment'])) . "', "
                        . "status = '1', "
                        . "created = '" . date("Y-m-d H:i:s") ."', "
                        . "modified = '" . date("Y-m-d H:i:s") . "' ";
                $query = $this->db->query($sql);
                $last_review_id = $this->db->getLastId();
    		return $last_review_id;
						
		}

		/*
			* @method: saveOrderReviewReasons
			* @params: order_review_id,reason_id
		  	* @author: Rahul, Sep 2019
		 */
		public function saveOrderReviewReasons($order_review_id,$reason_id) {
			$values=array();	
			$sql = "INSERT INTO " . DB_PREFIX . "order_review_reasons (order_review_id,review_reasons_id,created,modified) VALUES";
			foreach ($reason_id as $key => $value) {
				$values[$key]="('".(int)$order_review_id."', '".(int)$value['id']."','" . date("Y-m-d H:i:s") ."','" . date("Y-m-d H:i:s") ."')";
			}
			if(!empty($values)){

				$insert_value=implode(', ', $values);
				$sql .=$insert_value;
				$query = $this->db->query($sql);
			}
    		return true;
						
		}


		
}
