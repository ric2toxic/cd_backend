<?php
class ModelCheckoutCoupon extends Model {

	private $_campaign_referrers = array( "EKOAFF" );

	public function getCoupon($code, $order_from = 'WEB') {
		
		// Check if request is from Android/iOS App or Web (Desktop / Mobile)
		
		if ( CONFIG_IS_MOBILE == 1 ) {
			$order_from = 'MOBILE_WEB';
		}

		if ( isset( $this->session->data['ORDER_FROM'] )) {

			$order_from = $this->session->data['ORDER_FROM'];

			if ( $this->session->data['ORDER_FROM'] == "ANDROIDAPP" ) {
				$order_from = 'ANDROID_APP';
			}
		}

		$coupon_message = array(
							'status' => false,
							'message' => 'Invalid Coupon !'
					      );

		//get coupon details from DB
		$coupon_details = $this->getCouponDetailsByCode( $code );

		// validate coupon if details found
		if ( !empty( $coupon_details )) {

			$validate_result = $this->validateCoupon( $coupon_details, $order_from );

			$product_data = $validate_result['product_data'] ?? array();
			$coupon_message['status'] = $validate_result['status'];
			$coupon_message['message'] = $validate_result['message'];

		}

		// return 
		if ( $coupon_message['status'] ) {
			
			$coupon_details['product'] = $product_data;
			$coupon_details['coupon_message'] = $coupon_message;
			
			return $coupon_details;
		}

		return array( 'coupon_message' => $coupon_message );
	}

	public function getFranchiseCoupon($code, $franchise_id){
	    $sql = "SELECT franchise_discount FROM " . DB_PREFIX . "franchise_data WHERE franchise_id = '". (int)$franchise_id . "' AND franchise_coupon = '". $this->db->escape($code) ."' LIMIT 1";
        $result = $this->db->query($sql);
        if($result->num_rows){
            return $result->row['franchise_discount'];
        }
        else{
            return 0;
        }
    }
		
	/*
	 * method to get coupon data from setting table
	 * @params: coupon code, store id(optional)
	 * @return: coupon code key
	 * @author: Devendra, October 2018
	 */
	public function getCouponFromSetting(string $coupon_code, int $store_id = 0): string {
		$sql = "
						SELECT 
							s.key 
						FROM 
							" . DB_PREFIX . "setting as s 
						WHERE 
							s.store_id = '" . (int)$store_id . "' AND 
							s.code = 'coupon' AND 
							s.key NOT IN ('coupon_status', 'coupon_sort_order') AND 
							s.value = '" . $this->db->escape($coupon_code)  . "'
						";
		 $result = $this->db->query($sql);
		 
		 if ($result->num_rows) {
			 return $result->row['key'];
		 }
		 
		 return '';
	}

	/**
	 * gets coupon using coupon code
	 * @param  string $code
	 * @return array
	 * @author Anurag Jain 11 Feb 2019
	 */
	public function getCouponDetailsByCode( $code ): array
	{
		$coupon_details = array();

		$coupon_sql = " SELECT 
							c.*,
							cc.customer_id
						FROM `" . DB_PREFIX . "coupon` c
							LEFT JOIN ". DB_PREFIX ."coupon_customer cc
								ON cc.coupon_id = c.coupon_id
						WHERE code = '" . $this->db->escape( $code ) . "' ";

		$coupon_result = $this->db->query( $coupon_sql );

		if ( $coupon_result->num_rows ) {

			$free_shipping = 0;

			foreach ( $coupon_result->rows as $key => $coupon_data ) {
				
				if ( $coupon_data['name'] == "Free Shipping Coupon" ) {
					
					$free_shipping = 1;
					
					if ( !empty( $coupon_data['customer_id'] ) &&  ( $coupon_data['customer_id'] == $this->customer->getId() )) {
						$coupon_details = $coupon_data;
					}
				}
			}

			if ( !$free_shipping ) {
				$coupon_details = $coupon_result->row;
			}
		}

		return $coupon_details;
	}

	/**
	 * validate coupon code 
	 * @param  array  $coupon_details
	 * @param  string $order_from
	 * @return array return status(true/false) and message(error/success message) for coupon
	 * @author Anurag Jain 11 Feb 2019
	 */
	public function validateCoupon( array $coupon_details , $order_from ): array
	{

		if ( empty( $coupon_details )) {

			return array(
						'status' => false,
						'message' => 'Invalid Coupon !'
				   );
		}

		$status = true;
		$message = 'Coupon applied successfully !!!';
		
		// define free shipping specifications
		$is_free_shipping = ( $coupon_details['name'] == "Free Shipping Coupon" ) ? 1 : 0;
		$min_sub_total_for_free_shipping = 10000;

		$cart_sub_total = $this->cart->getSubTotal();

		// validate coupon status
		if ( $status && empty( $coupon_details['status'] )) {
			$status = false;
			$message = 'Invalid Coupon !';
		}

		// login validation
		if ( $status && $coupon_details['logged'] && !$this->customer->getId() ) {
			$status = false;
			$message = 'Login to apply coupon !';
		}

		// coupon amount validation
		if ( $status && $coupon_details['total'] > $cart_sub_total ) {
			$status = false;
			$message = 'Coupon amount exceeds order amount !'; // Shop more to use this coupon !
		}

		// free shipping validations
		if ( $is_free_shipping ) {

			if ( $status ) {
				if ( empty( $coupon_details['customer_id'] ) || ( $this->customer->getId() != $coupon_details['customer_id'] )) {
					$status = false;
					$message = 'Invalid Coupon !';
				}
			}

			if ( $status ) {
				if (( $cart_sub_total < $min_sub_total_for_free_shipping )) {
					$status = false;
					$message = 'Sorry! To use this coupon, Cart value (Subtotal) should be atleast Rs '. $min_sub_total_for_free_shipping .'. Kindly add more items to your cart.';
				}
			}
		}

		if ( !empty( $coupon_details['customer_id'] ) && ( $this->customer->getId() != $coupon_details['customer_id'] )) {
			$status = false;
			$message = 'Invalid Coupon !';
		}

		// coupon expiry validation
		$date_start = strtotime( $coupon_details['date_start']." 00:00:00" );
		$date_end = strtotime( $coupon_details['date_end']."23:59:59" );

		if ( $status 
			 && (( $coupon_details['date_start'] != '0000-00-00' && $date_start > time() )
			 || ( $coupon_details['date_end'] != '0000-00-00' && $date_end < time() ))) {

			$status = false;
			$message = 'Coupon is expired !';
		}

		// coupon platform validation
		if ( $status && !( in_array( $coupon_details['allowed_from'], array( 'ALL', $order_from )))) {
			$status = false;
			$message = 'Coupon is valid only on '. ucwords(str_replace( '_', ' ', strtolower( $coupon_details['allowed_from'] ))) .' !';
		}

		// validate coupon usage history
		$coupon_history = $this->getCouponHistory( (int) $coupon_details['coupon_id'] );

		if ( $status && !empty( $coupon_history )) {
			
			if ( $coupon_details['uses_total'] > 0 && ( count( $coupon_history ) >= $coupon_details['uses_total'] )) {

				$status = false;
				$message = 'Coupon reached its usage limit !';
			}

			if ( $this->customer->getId() && $coupon_details['uses_customer'] > 0 ) {

				$coupon_history_cutomer_count = array_count_values( array_values( array_column( $coupon_history, 'customer_id' )))[$this->customer->getId()] ?? 0;

				if ( $coupon_history_cutomer_count  >= $coupon_details['uses_customer'] ) {
					$status = false;
					$message = 'Coupon reached its usage limit !';
				}
			}
		}

		if ( $status ) {
			
			// Products
			$coupon_product_data = array();

			$coupon_product_query = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_details['coupon_id'] . "'" );

			foreach ( $coupon_product_query->rows as $product ) {
				$coupon_product_data[] = $product['product_id'];
			}

			// Categories
			$coupon_category_data = array();

			$coupon_category_query = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category_path` cp ON (cc.category_id = cp.path_id) WHERE cc.coupon_id = '" . (int)$coupon_details['coupon_id'] . "'" );

			foreach ( $coupon_category_query->rows as $category ) {
				$coupon_category_data[] = $category['category_id'];
			}

			$product_data = array();

			if ( $coupon_product_data || $coupon_category_data ) {

				foreach ($this->cart->getProducts() as $product) {

					if (in_array($product['product_id'], $coupon_product_data)) {

	                    $product_data[$product['product_id']]['product_id'] = $product['product_id'];
						$product_data[$product['product_id']]['filter_name'] = 'product_id';
						$product_data[$product['product_id']]['filter_value'] = $product['product_id'];
						continue;
					}

					foreach ($coupon_category_data as $category_id) {

						$coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "'");

						if ($coupon_category_query->row['total']) {

	                        $product_data[$product['product_id']]['product_id'] = $product['product_id'];
							$product_data[$product['product_id']]['filter_name'] = 'category_id';
							$product_data[$product['product_id']]['filter_value'] = $category_id;
							continue;
						}
					}
				}

				if ( !$product_data ) {
					$status = false;
					$message = 'Invalid Coupon !';
				}
			}
		}
		
		if ( $is_free_shipping && $status ) {
			$this->session->data['free_shipping_coupon_applied'] = true;
		}

		return array(
						'status' => $status,
						'message' => $message
				   );
	}

	public function getCouponHistory( int $coupon_id ): array
	{
		$coupon_history = array();

		$coupon_history_sql = "SELECT 
							   	* 
							   FROM `" . DB_PREFIX . "coupon_history` ch 
							   WHERE ch.coupon_id = '" . $coupon_id . "'";
		
		$coupon_history_result = $this->db->query( $coupon_history_sql );

		if ( $coupon_history_result->num_rows ) {
			$coupon_history = $coupon_history_result->rows;
		}

		return $coupon_history;
	}

	public function addReferralCode( int $customer_id, string $referral_code, array $utm_data = array() )
	{
		$add_status = array(
						"status" => false,
						"message" => "Invalid referral code !"
					  );

		$is_valid = false;

		/*** validate referral code ***/
		
		// check for normal referral or campaign referral
		
		if ( !empty( $utm_data )) {

			$utm_data['utm_campaign'] = strtoupper( $utm_data['utm_campaign'] ?? "" );

			/** campaign referral */
			if ( !in_array( $utm_data['utm_campaign'], $this->_campaign_referrers )) {
				$add_status['message'] = "Invalid campaign referral code !";
				return $add_status;
			}

		} else {

			/** normal referral */
			// check if already have used referral code
			$code = $this->checkReferralAlreadyApplied( $customer_id );
			if ( !empty( $code )) {
				$add_status['message'] = "You have already used a referral code !";
				return $add_status;
			}
		}
			
		// check if code is applied within 7 days of user creation
		$original_creation_date = $this->getCustomerCreationDate( $customer_id );
		$creation_time = strtotime( $original_creation_date );
		$calculated_referral_end_date = date( 'Y-m-d', strtotime( "+7 day", $creation_time ));

		if ( date( 'Y-m-d', time() ) <= $calculated_referral_end_date ) {

			if ( !empty( $utm_data['utm_campaign'] )) {
				$affiliate_referral_code = $utm_data['utm_campaign'];
			} else {
				$affiliate_referral_code = $referral_code;
			}

			$affiliate_details = $this->getAffiliateByCode( $affiliate_referral_code );
			
			// check if affiliate is enabled
			if ( !empty( $affiliate_details )) {

				if ( $affiliate_details['status'] == '0' ) {
					
					$add_status['message'] = "Referral code expired !";
					return $add_status;
				}
				$is_valid = true;
			}
		} else {
			$add_status['message'] = "Referral code expired !";
			return $add_status;
		}

		// add referral details / coupon (if applicable) to db
		if ( $is_valid ) {

			if ( !empty( $utm_data['utm_campaign'] )) {
				$this->addCampaignReferral( (int) $affiliate_details['affiliate_id'], $customer_id, $referral_code, $utm_data );
			} else {
				$this->insertReferralCode( $customer_id, $referral_code );
				$this->addReferralCoupon( $customer_id, $affiliate_details );
			}

			$add_status['status'] = $is_valid;
			$add_status['message'] = "Success";
		}

		return $add_status;
	}

	public function getCustomerCreationDate( int $customer_id )
	{
		$creation_date = "";
		$sql = "
				SELECT
					date_added
				FROM
					".DB_PREFIX."customer
				WHERE customer_id = '" . $customer_id . "'";

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$creation_date = $result->row['date_added'];
		}

		return $creation_date;
	}

	public function checkReferralAlreadyApplied(  int $customer_id  )
	{
		$code = $this->getReferralCoupon( $customer_id );
		return $code;
	}

	public function getAffiliateByCode( string $referral_code ): array
	{
		$affiliate_details = array();

		$sql = "SELECT 
					*
				FROM ". DB_PREFIX ."affiliate 
				WHERE 
					code = '". $this->db->escape( $referral_code ) ."'";

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$affiliate_details = $result->row;
		}

		return $affiliate_details;
	}

	public function insertReferralCode( int $customer_id, string $referral_code )
	{
		$sql = "UPDATE ". DB_PREFIX ."customer
				SET referral_code = '". $this->db->escape( $referral_code ) ."'
				WHERE customer_id = '". $customer_id ."'";

		$result = $this->db->query($sql);

		return $result;
	}

	public function addReferralCoupon( int $customer_id, array $affiliate_details = array() )
	{
		$referral_coupon_code = $this->generateReferralCouponCode();

		if ( !empty( $referral_coupon_code )) {

			$coupon_data = array();
			$coupon_data['name'] = 'REFERRAL_' . strtoupper( $affiliate_details['firstname'] ) . "_" . strtoupper( $affiliate_details['lastname'] );
			$coupon_data['code'] = $referral_coupon_code;
			$coupon_data['discount'] = $affiliate_details['discount_amount'];
			$coupon_data['type'] = 'F';
			$coupon_data['total'] = '0.00';
			$coupon_data['logged'] = '1';
			$coupon_data['shipping'] = '0';
			$coupon_data['date_start'] = date( "Y-m-d" );
			$coupon_data['date_end'] = date( "Y-m-d", strtotime( "31-12-2090" ));
			$coupon_data['uses_total'] = $affiliate_details['uses_customer'];
			$coupon_data['uses_customer'] = $affiliate_details['uses_customer'];
			$coupon_data['status'] = '1';
			$coupon_data['store_id'] = '0';
			$coupon_data['allowed_from'] = 'ALL';
			$coupon_data['customer_id'] = $customer_id;

			$this->addCoupon( $coupon_data );
		}
	}

	public function addCoupon( array $data ): int
	{
		$coupon_id = 0;

		$add_sql = "INSERT INTO " . DB_PREFIX . "coupon 
					SET 
						name = '" . $this->db->escape( $data['name'] ) . "', 
						code = '" . $this->db->escape( $data['code'] ) . "', 
						discount = '" . (float) $data['discount'] . "', 
						type = '" . $this->db->escape( $data['type'] ) . "', 
						total = '" . (float) $data['total'] . "', 
						logged = '" . (int) $data['logged'] . "', 
						shipping = '" . (int) $data['shipping'] . "', 
						date_start = '" . $this->db->escape( $data['date_start'] ) . "', 
						date_end = '" . $this->db->escape( $data['date_end'] ) . "', 
						uses_total = '" . (int) $data['uses_total'] . "', 
						uses_customer = '" . (int) $data['uses_customer'] . "', 
						status = '" . (int) $data['status'] . "', 
						date_added = NOW(), 
						store_id = '" . (int) $data['store_id'] . "',
						allowed_from = '" . $data['allowed_from'] . "'";

		$this->db->query( $add_sql );

		$coupon_id = (int) $this->db->getLastId();

		if ( !empty( $data['customer_id'] )) {
			$this->db->query( "INSERT INTO " . DB_PREFIX . "coupon_customer SET coupon_id = '" . (int) $coupon_id . "', customer_id = '" . (int) $data['customer_id'] . "'" );
		}

		return $coupon_id;
	}

	public function generateReferralCouponCode( $length = 8 )
	{
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $characters_length = strlen( $characters );
	    $random_string = '';

	    for ( $i = 0; $i < $length; $i++ ) {
	        $random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
	    }

	    return $random_string;
	}

	public function autoApplyReferralCoupon( int $customer_id )
	{
		$this->load->model('setting/setting');
		$this->load->language('checkout/coupon');

		$coupon = $this->getReferralCoupon( $customer_id );

		if ( !empty( $coupon )) {

			$coupon_info = $this->getCoupon( $coupon );

			$coupon_data = array();

	        if ( $coupon_info['coupon_message']['status'] ) {

	            $coupon_data['coupon'] = $coupon;
	            $coupon_data['coupon_type'] = $coupon_info['type'];

	            $this->cart->setCoupon( $coupon_data );
	            
	        } else {
	        	$coupon = '';
	        }
		}
		
        return $coupon;
	}

	public function getReferralCoupon( $customer_id ) 
	{
		$coupon_code = "";

		$coupon_sql = "
						SELECT 
							code 
					   	FROM " . DB_PREFIX . "coupon c
					   	INNER JOIN ". DB_PREFIX ."coupon_customer cc
					   		ON cc.coupon_id = c.coupon_id 
						WHERE 
							cc.customer_id = '". (int) $customer_id ."'
							AND c.name like '%REFERRAL%'
						ORDER BY cc.coupon_id ASC";

		$coupon_result = $this->db->query( $coupon_sql );

		if ( $coupon_result->num_rows ) {
			$coupon_code = $coupon_result->row['code'];
		}

		return $coupon_code;
	}

	/**
	 * @param int    $affiliate_id  [description]
	 * @param int    $customer_id   [description]
	 * @param string $referral_code [description]
	 * @param array  $utm_data      [description]
	 * @return bool
	 * @author Anurag Jain, 3 Oct 2019
	 */
	public function addCampaignReferral( int $affiliate_id, int $customer_id, string $referral_code, array $utm_data ): bool
	{
		$add_sql = "INSERT IGNORE INTO " . DB_PREFIX . "affiliate_to_customer 
					SET 
						affiliate_id = '" . $affiliate_id . "', 
						customer_id = '" . $customer_id . "', 
						referral_code = '" . $this->db->escape( $referral_code ) . "', 
						utm_campaign = '" . $this->db->escape( $utm_data['utm_campaign'] ?? '' ) . "', 
						utm_medium = '" . $this->db->escape( $utm_data['utm_medium'] ?? '' ) . "', 
						utm_source = '" . $this->db->escape( $utm_data['utm_source'] ?? '' ) . "', 
						utm_channel = '" . $this->db->escape( $utm_data['utm_channel'] ?? '' ) . "', 
						utm_term = '" . $this->db->escape( $utm_data['utm_term'] ?? '' ) . "'";

		return $this->db->query( $add_sql );
	}
}
