<?php
class ModelSaleCustomer extends Model {
	public function addCustomer($data) {
        
        $password_secret = password_hash($data['password'], PASSWORD_DEFAULT);

		$insertQuery = "INSERT INTO " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', password = '" . $password_secret . "', password_mode = 'new', `is_dropshipper` = '" . (isset($data['is_dropshipper']) ? $data['is_dropshipper'] : 0) . "', date_added = NOW()";

		$this->db->query($insertQuery);

		$customer_id = $this->db->getLastId();
		// update master id when new customer created account
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET master_id = '" .(int)$customer_id . "' WHERE customer_id = '" .(int)$customer_id . "'");

		if(isset($data['gst_number']) && !empty(trim($data['gst_number']))) {
			$gstObject = new GST($this->registry);
			$old_gst_number = "";
			$gst_number     = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $data['gst_number']);
			$gst_number     = trim($gst_number);
			$gstObject->updateGstNumber((int) $customer_id, (int) $customer_id, $old_gst_number, $gst_number);
		}
		
		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
		}
		
        $this->_addFranchiseData( array(
                                    "franchise_id" => $customer_id,
                                    "franchise_status" => $data['franchise_status'],
                                    "franchise_coupon" => $data['franchise_coupon'],
                                    "franchise_discount" => $data['franchise_discount'],
                                    "franchise_prefix" => $data['franchise_prefix']
                                    ) 
                                );
        return $customer_id;
	}

	public function editCustomer($customer_id, $data) {
				
		$customer = $this->db->query("SELECT gst_number, ws_access_token FROM " . DB_PREFIX . "customer where customer_id = ".$customer_id)->row;

		$support_number =  8696491521;
		$request['customer_id'] = $customer_id;
		$request['token']       = $customer['ws_access_token'];

        $data_json = json_encode($request);
        $api_url = WSBOX_CRM_URL."cron/getAgentofCustomer";
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Content-Type: application/json',
                'Content-Length: ' . strlen($data_json))
               );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true );
            
        if(!empty($result['agent'])) { 
            $support_number = $result['agent'];  
        } else if(!empty($result['team_lead'])) { 
            $support_number = $result['team_lead']; 
        } else { 
            $support_number = $result['support']; 
        } 

        $updateGst = "";
        if(isset($data['gst_number']))
        {	
			/*** using customer library function as it handles same masterids gst updation too; Anurag Jain ***/
			//$this->registry->set('customer_id', $customer_id);
			$customerObject = new CustomerEntity( $this->registry, $customer_id );
			$log_changes = true;
			$customerObject->setGSTNumber($data['gst_number'], $log_changes);
        }

        $sql = "UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', 
                email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', ".$updateGst." 
                is_dropshipper = '" . (int)$data['is_dropshipper'] . "'";

        if (isset($data['bank_ac_holder_name'])) {
        	$bank_ac_holder_name = $this->db->escape($data['bank_ac_holder_name']);
        } else {
        	$bank_ac_holder_name = '';
        }

        if (isset($data['bank_ac_number'])) {
        	$bank_ac_number = $this->db->escape($data['bank_ac_number']);
        } else {
        	$bank_ac_number = '';
        }

        if (isset($data['ifsc_code'])) {
        	$ifsc_code = $this->db->escape($data['ifsc_code']);
        } else {
        	$ifsc_code = '';
        }

        $sql .= " WHERE customer_id = '" . (int)$customer_id . "' ";
        
        $this->db->query($sql);


		// Start To log of the fields whose values are chaged, field_name, old_value and new_value of the columns
		$changes_data = json_decode($data['changes_data'],true);
		$source_field = 'edit_customer';
		$this->customerRecordLogs($customer_id, $changes_data, $source_field);
		//End to Log the changes made in form
		
		// commented as GST number update is handeled using customer library setGSTNumber function; Anurag Jain
        // if ($data['super_admin_gst_number']) {
		// 	$this->updateGSTNumber($changes_data, $this->request->get['customer_id']);
		// }

		// start when customer email id change then these email id also update in ms_seller table (by vikas(21-11-2017))
		$this->db->query("UPDATE " . DB_PREFIX . "ms_seller SET email = '" . $this->db->escape($data['email']) . "' WHERE seller_id = '" . (int)$customer_id . "'");
		// end ///

		if ($data['password']) 
		{
		 
		  $password_secret = password_hash($data['password'], PASSWORD_DEFAULT);
		  $this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $password_secret . "', password_mode = 'new' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {

				$this->db->query("INSERT INTO " . DB_PREFIX . "address SET address_id = '" . (int)$address['address_id'] . "', customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();
					$default_address_exists = 1;

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
			if ( empty($default_address_exists) ) {
				$address_id = $this->db->getLastId();
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
			}
		}

        $this->_addFranchiseData( array('franchise_id' => $customer_id,
                                        "franchise_status" => $data['franchise_status'],
                                        "franchise_coupon" => $data['franchise_coupon'],
                                        "franchise_discount" => $data['franchise_discount'],
                                        "franchise_prefix" => $data['franchise_prefix']
                                        )
                                );

        /** Update Dropshipper In CRM **/
        if (!empty($data['telephone']) && !empty($customer_id)) {            
            $this->load->model('lead/lead', 'frontend');
            $lead_data = [
                'user_id'           => UMA_USER_ID,
                'is_dropshipper'    => isset($data['is_dropshipper']) ? $data['is_dropshipper'] : 0
            ];          
            $this->frontend_model_lead_lead->updateLead($lead_data, $data['telephone'], 'Lead has been marked to dropshipper account, so assigned to UMA', $customer_id);
        }
	}

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_access_token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editCustomerAccessToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_access_token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteCustomer($customer_id) {

        $this->load->model('sellers/sellers');
        $this->model_sellers_sellers->deleteSeller($customer_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_cashback WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_settings WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_additional_email WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_preference WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "' ");
		$result = array();
		$result = $query->row;
		$credit_sql = "SELECT customer_id, type, credit_status, neogrowth_registration_number, neogrowth_account_number, lazypay_email, lazypay_mobile
					   FROM " . DB_PREFIX . "customer_credit 
					   WHERE customer_id = '" . (int)$customer_id . "' ";
		$credit_query = $this->db->query($credit_sql);
		
	    $result['credit_status'] = 0 ;
        $result['neogrowth_registration_number'] = '';
		$result['neogrowth_account_number'] = '';

		$result['lazypay_status'] = 0 ;
        $result['lazypay_email'] = '';
		$result['lazypay_mobile'] = '';

		if( $credit_query->num_rows )
		{
            foreach($credit_query->rows as $credit_row)
            {
            	if($credit_row['type'] == 'Neogrowth')
            	{
            	  $result['credit_status'] = $credit_row['credit_status'];	
			      $result['neogrowth_registration_number'] = $credit_row['neogrowth_registration_number'];
			      $result['neogrowth_account_number'] = $credit_row['neogrowth_account_number'];	
            	}

            	else if($credit_row['type'] == 'Lazypay')
            	{
            	  $result['lazypay_status'] = $credit_row['credit_status'];	
			      $result['lazypay_email'] = $credit_row['lazypay_email'];
			      $result['lazypay_mobile'] = $credit_row['lazypay_mobile'];	
            	}
            }
		}
		
		$wsb_credit_sql = "SELECT status, credit_limit 
					   FROM " . DB_PREFIX . "customer_wsb_credit 
					   WHERE customer_id = '" . (int)$customer_id . "' ";
		$credit_query = $this->db->query($wsb_credit_sql);
		if( $credit_query->num_rows ){
			$result['wsb_credit_payment_status'] = $credit_query->row['status'];	
			$result['wsb_credit_payment_limit'] = $credit_query->row['credit_limit'];
		} else {
			$result['wsb_credit_payment_status'] = '' ;
            $result['wsb_credit_payment_limit'] = '';
		}
		
		return $result;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT customer_id 
                                   FROM " . DB_PREFIX . "customer 
                                   WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'
                                   LIMIT 1");

		return $query->row;
	}

	public function getCustomerByMobile($mobile) {
		$query = $this->db->query("SELECT customer_id 
                                   FROM " . DB_PREFIX . "customer 
                                   WHERE telephone = '" . $this->db->escape($mobile) . "'
                                   LIMIT 1");

		return $query->row;
	}

	public function getCustomers($data = array()) {
		$this->load->model('sale/order');
		
		// Handling filter_order_count. If provided, it should be positive (>0) integer
		$filter_order_count = intval( $data['filter_order_count'] ?? 0 );

		$sql = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS name,
                  c.customer_id,
                  c.master_id,
                  c.firstname,
                  c.lastname,
                  c.email,
                  c.telephone,
                  c.ip,
                  c.date_added,
                  c.is_dropshipper,
                  c.ws_gcm_registration_id,
                  c.self_order,
                  c.has_website,";
                  
        if (  !empty($data['filter_city']) 
           || !empty($data['filter_customer_postcode']) 
           || !empty($data['filter_company_name']) 
           ){
                $sql .= " oa.company,
                          oa.city,
                          oa.postcode, ";
            }
            
        if ( $filter_order_count > 0 ) {
			
			$sql .= " COUNT(DISTINCT o.order_id) AS order_count, ";
		}
        
		$sql .= " CONVERT( SUBSTRING_INDEX(SUBSTRING_INDEX(cc.cart_data,':',2),':',-1), UNSIGNED INTEGER ) as cart_items,
                  cc.date_modified as last_cart_modified,
                  TIMEDIFF(cc.date_modified, cc.date_added) as buildup_time
                  FROM " . DB_PREFIX . "customer c
                  INNER JOIN " . DB_PREFIX . "customer_cart cc ON cc.customer_id = c.customer_id ";
        
        if ( !empty($data['filter_city']) 
          || !empty($data['filter_customer_postcode']) 
          || !empty($data['filter_company_name']) 
           ){
              $sql .= " INNER JOIN " . DB_PREFIX . "address oa ON oa.address_id = c.address_id ";
            }

        if(!empty($data['filter_umrn_lan'])){
        	$sql .= " INNER JOIN " . DB_PREFIX . "customer_nach_details AS cnd ON cnd.customer_id = c.customer_id ";
        }
            
        if ( $filter_order_count > 0 ) {
			
			$sql .= " INNER JOIN " . DB_PREFIX . "customer c2 ON c2.master_id = c.master_id 
			          INNER JOIN " . DB_PREFIX . "order o ON o.customer_id = c2.customer_id AND 
			                                                 o.store_id IN (" . WSB_STORES_ID . ") AND 
			                                                 o.franchise_id = 0 
			          INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id AND 
			                                                       osub.order_status_id > 0 ";
		}

		if ( $filter_order_count == 0  && !empty($data['ordering_customer'])) 
		{
			$sql .= " INNER JOIN " . DB_PREFIX . "order o ON o.customer_id = c.customer_id AND o.store_id IN (" . WSB_STORES_ID . ")
			    INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id AND 
			                                                       osub.order_status_id > 0 ";
        }

        if (isset($data['filter_customer_type']) && $data['filter_customer_type'] == 5 ) {
            $sql .= " INNER JOIN " . DB_PREFIX . "franchise_data AS fd ON fd.franchise_id = c.customer_id ";
        }

                  
        $sql .= " WHERE 1=1 ";
       
        // FILTER(s)       
        $implode = array();

        if (!empty($data['filter_customer_id'])) {
			$implode[] = "c.customer_id =" . (int)$data['filter_customer_id'];
        }

        if (!empty($data['filter_master_id'])) {
			$implode[] = "c.master_id =" . (int)$data['filter_master_id'];
		}
        
        if (!empty($data['filter_name'])) {
            $fts_str = getFullTextSearchString($data['filter_name']);
            $implode[] = "MATCH(c.firstname, c.lastname) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }
        
        if (!empty($data['filter_referral_code'])) 
        {
            $implode[] = "c.referral_code = '" . $this->db->escape($data['filter_referral_code']) . "'";
        }
       

		if (isset($data['filter_customer_type']) && $data['filter_customer_type'] !='all' ) {
			if($data['filter_customer_type'] == 4){
				$implode[] = "c.is_dropshipper IN (0,1)";	
			} else {
				$implode[] = "c.is_dropshipper IN (" . $data['filter_customer_type'] . ")";	
			}		
		}

		if (!empty($data['filter_date_added'])) {
            $implode[] = "c.date_added >='" . $this->db->escape($data['filter_date_added']) . " 00:00:00'";
            $implode[] = "c.date_added <='" . $this->db->escape($data['filter_date_added']) . " 23:59:59'";
		}

		if (isset($data['filter_not_customer_id'])) {
			$implode[] = "c.customer_id != '" . (int)$data['filter_not_customer_id'] . "'";
        }
        
        if(!empty($data['filter_umrn_lan'])){
        	$implode[] = " 
        				  ( cnd.umrn_no LIKE '" . $this->db->escape($data['filter_umrn_lan']) . "' 
        				  			OR
        				  	cnd.lan_no LIKE '" . $this->db->escape($data['filter_umrn_lan']) . "'
        				  )
			        	";
        }

        if (!empty($data['filter_city'])) {
            $implode[] = "oa.city LIKE '" . $this->db->escape($data['filter_city']) . "%'";
        }

        if (!empty($data['filter_customer_postcode'])) {
            $implode[] = "oa.postcode LIKE '" . $this->db->escape($data['filter_customer_postcode']) . "%'";
        }

        if (!empty($data['filter_company_name'])) {
            $implode[] = "oa.company LIKE '" . $this->db->escape($data['filter_company_name']) . "%'";
        }


		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

        if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'";
		}
	
		if (isset($data['filter_gst_number'])) {
			$implode[] = "c.gst_number LIKE '" .  $this->db->escape(trim($data['filter_gst_number'])) . "'";
		}
    
        if (!empty($implode)) {
            $sql .= " AND " . implode(" AND ", $implode);
        }
        
        if (isset($data['order']) && isset($data['page_id']) && $data['page_id'] != 'FIRST') {
            if ($data['order'] == 'ASC') {
                $sql .= " AND " . $data['sort'] . " > '" . $data['page_id'] . "'";
            } else {
                $sql .= " AND " . $data['sort'] . " < '" . $data['page_id'] . "'";
            }
        }
		
		// If filter_order_count is provided ( > 0 ), then we need to GROUP BY on customer_id and use HAVING to filter
		if ( $filter_order_count > 0 ) {
			$sql .= " GROUP BY c.customer_id 
			          HAVING order_count >= " . (int)$filter_order_count;
		}

		$sort_data = array(
			'name',
			'c.email',
            'c.telephone',
			'c.date_added',
            'cart_items',
            'cc.date_modified',
            'buildup_time', 
            'order_count' 
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
			
		} else {
            $sql .= " ASC";
        }
        
		if (isset($data['limit'])) {
	
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        
        $results = $query->rows;

		if(!$query->num_rows) {
            return array();
        }

        $customer_ids = array_column($results,'customer_id');
		$customer_data = array_combine($customer_ids, $results) ;
		$customer_implode_ids = implode(',',$customer_ids);

        // Getting Address Table Data, if City/Company/Postcode was not being filtered
        // As we don't join address table, to optimize the query
		if(empty($data['filter_city'])
        && empty($data['filter_customer_postcode']) 
        && empty($data['filter_company_name'])
          ) {
			$sql_address = "SELECT oa.customer_id, 
                                   oa.city, 
                                   oa.company, 
                                   oa.postcode 
                            FROM " . DB_PREFIX . "address oa 
							WHERE oa.customer_id IN (". $customer_implode_ids .")";
            $query_address = $this->db->query($sql_address)->rows;
			$address_data_array = array_combine(array_column($query_address,'customer_id'),$query_address) ;
		}
        
        // Total orders placed today by the customers
        $total_orders_today_data = $this->model_sale_order->getTotalTodayOrders($customer_ids);

        // Adding the additional data to customer_data array
		foreach($customer_data as $key=>$value) {
			if(empty($data['filter_city'])
            && empty($data['filter_customer_postcode']) 
            && empty($data['filter_company_name'])) {
				$customer_data[$key]['city'] = isset($address_data_array[$key]['city']) ? $address_data_array[$key]['city']:'';
				$customer_data[$key]['company'] = isset($address_data_array[$key]['company']) ? $address_data_array[$key]['company']:'';
                $customer_data[$key]['postcode'] = isset($address_data_array[$key]['postcode']) ? $address_data_array[$key]['postcode']:'';
			}
			$customer_data[$key]['total_orders_today'] = isset($total_orders_today_data[$key]) ? $total_orders_today_data[$key] : 0;
		}
		
		return $customer_data;
    }
    
    public function getCustomersWithoutCart($data = array()) {
		$this->load->model('sale/order');
		
		// Handling filter_order_count. If provided, it should be positive (>0) integer
		$filter_order_count = intval( $data['filter_order_count'] ?? 0 );

		$sql = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS name,
                  c.customer_id,
                  c.master_id,
                  c.firstname,
                  c.lastname,
                  c.email,
                  c.telephone,
                  c.date_added,
                  c.is_dropshipper,
                  c.ws_gcm_registration_id,
                  c.self_order,
                  c.has_website,";
                  
        if (  !empty($data['filter_city']) 
           || !empty($data['filter_customer_postcode']) 
           || !empty($data['filter_company_name']) 
           ){
                $sql .= " oa.company,
                          oa.city,
                          oa.postcode, ";
            }
            
        if ( $filter_order_count > 0 ) {
			
			$sql .= " COUNT(DISTINCT o.order_id) AS order_count, ";
		}
        
		$sql .= " CONVERT( SUBSTRING_INDEX(SUBSTRING_INDEX(cc.cart_data,':',2),':',-1), UNSIGNED INTEGER ) as cart_items,
                  cc.date_modified as last_cart_modified,
                  TIMEDIFF(cc.date_modified, cc.date_added) as buildup_time
                  FROM " . DB_PREFIX . "customer c
                  LEFT JOIN " . DB_PREFIX . "customer_cart cc ON cc.customer_id = c.customer_id ";
        
        if ( !empty($data['filter_city']) 
          || !empty($data['filter_customer_postcode']) 
          || !empty($data['filter_company_name']) 
           ){
              $sql .= " INNER JOIN " . DB_PREFIX . "address oa ON oa.address_id = c.address_id ";
            }

        if(!empty($data['filter_umrn_lan'])){
        	$sql .= " INNER JOIN " . DB_PREFIX . "customer_nach_details AS cnd ON cnd.customer_id = c.customer_id ";
        }
            
        if ( $filter_order_count > 0 ) {
			
			$sql .= " INNER JOIN " . DB_PREFIX . "customer c2 ON c2.master_id = c.master_id 
			          INNER JOIN " . DB_PREFIX . "order o ON o.customer_id = c2.customer_id AND 
			                                                 o.store_id IN (" . WSB_STORES_ID . ") AND 
			                                                 o.franchise_id = 0 
			          INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id AND 
			                                                       osub.order_status_id > 0 ";
		}

		if ( $filter_order_count == 0  && !empty($data['ordering_customer'])) 
		{
			
			$sql .= " INNER JOIN " . DB_PREFIX . "customer c2 ON c2.master_id = c.master_id 
			          INNER JOIN " . DB_PREFIX . "order o ON o.customer_id = c2.customer_id AND 
			                                                 o.store_id IN (" . WSB_STORES_ID . ") AND 
			                                                 o.franchise_id = 0 
			          INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id AND 
			                                                       osub.order_status_id > 0 ";
		}
                  
        $sql .= " WHERE 1=1 ";
       
        // FILTER(s)       
        $implode = array();

        if (!empty($data['filter_customer_id'])) {
			$implode[] = "c.customer_id =" . (int)$data['filter_customer_id'];
        }

        if (!empty($data['filter_master_id'])) {
			$implode[] = "c.master_id =" . (int)$data['filter_master_id'];
		}
        
        if (!empty($data['filter_name'])) {
            $fts_str = getFullTextSearchString($data['filter_name']);
            $implode[] = "MATCH(c.firstname, c.lastname) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }
        
        if (!empty($data['filter_referral_code'])) {
            $implode[] = "c.referral_code = '" . $this->db->escape($data['filter_referral_code']) . "'";
        }

		if (isset($data['filter_customer_type']) && $data['filter_customer_type'] !='all' ) {
			if($data['filter_customer_type'] == 4){
				$implode[] = "c.is_dropshipper IN (0,1)";	
			} else {
				$implode[] = "c.is_dropshipper IN (" . $data['filter_customer_type'] . ")";	
			}		
		}

		if (!empty($data['filter_date_added'])) {
            $implode[] = "c.date_added >='" . $this->db->escape($data['filter_date_added']) . " 00:00:00'";
            $implode[] = "c.date_added <='" . $this->db->escape($data['filter_date_added']) . " 23:59:59'";
		}

		if (isset($data['filter_not_customer_id'])) {
			$implode[] = "c.customer_id != '" . (int)$data['filter_not_customer_id'] . "'";
        }
        
        if(!empty($data['filter_umrn_lan'])){
        	$implode[] = " 
        				  ( cnd.umrn_no LIKE '" . $this->db->escape($data['filter_umrn_lan']) . "' 
        				  			OR
        				  	cnd.lan_no LIKE '" . $this->db->escape($data['filter_umrn_lan']) . "'
        				  )
			        	";
        }

        if (!empty($data['filter_city'])) {
            $implode[] = "oa.city LIKE '" . $this->db->escape($data['filter_city']) . "%'";
        }

        if (!empty($data['filter_customer_postcode'])) {
            $implode[] = "oa.postcode LIKE '" . $this->db->escape($data['filter_customer_postcode']) . "%'";
        }

        if (!empty($data['filter_company_name'])) {
            $implode[] = "oa.company LIKE '" . $this->db->escape($data['filter_company_name']) . "%'";
        }


		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

        if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'";
		}
	
		if (isset($data['filter_gst_number'])) {
			$implode[] = "c.gst_number LIKE '" .  $this->db->escape(trim($data['filter_gst_number'])) . "'";
		}
    
        if (!empty($implode)) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $sql .= " AND cc.customer_id IS NULL ";
        
        if (!empty($data['page_id']) && $data['page_id'] != 'FIRST') {
            if (isset($data['order']) && ($data['order'] == 'ASC')) {
                $sql .= " AND c.customer_id > '" . $data['page_id'] . "'";
            } else {
                $sql .= " AND c.customer_id < '" . $data['page_id'] . "'";
            }
        }
		
		// If filter_order_count is provided ( > 0 ), then we need to GROUP BY on customer_id and use HAVING to filter
		if ( $filter_order_count > 0 ) {
			$sql .= " GROUP BY c.customer_id 
			          HAVING order_count >= " . (int)$filter_order_count;
		}

		$sql .= " ORDER BY c.customer_id";

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
		} else {
            $sql .= " DESC";
        }
        
		if (isset($data['limit'])) {
	
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['limit'];
        }
        
        
		
        $query = $this->db->query($sql);
        
        $results = $query->rows;
		
		if(!$query->num_rows) {
            return array();
        }
        
        $customer_ids = array_column($results,'customer_id');
		$customer_data = array_combine($customer_ids, $results) ;
		$customer_implode_ids = implode(',',$customer_ids);

        // Getting Address Table Data, if City/Company/Postcode was not being filtered
        // As we don't join address table, to optimize the query
		if(empty($data['filter_city'])
        && empty($data['filter_customer_postcode']) 
        && empty($data['filter_company_name'])
          ) {
			$sql_address = "SELECT oa.customer_id, 
                                   oa.city, 
                                   oa.company, 
                                   oa.postcode 
                            FROM " . DB_PREFIX . "address oa 
							WHERE oa.customer_id IN (". $customer_implode_ids .")";
            $query_address = $this->db->query($sql_address)->rows;
			$address_data_array = array_combine(array_column($query_address,'customer_id'),$query_address) ;
		}
        
        // Total orders placed today by the customers
        $total_orders_today_data = $this->model_sale_order->getTotalTodayOrders($customer_ids);

        // Adding the additional data to customer_data array
		foreach($customer_data as $key=>$value) {
			if(empty($data['filter_city'])
            && empty($data['filter_customer_postcode']) 
            && empty($data['filter_company_name'])) {
				$customer_data[$key]['city'] = isset($address_data_array[$key]['city']) ? $address_data_array[$key]['city']:'';
				$customer_data[$key]['company'] = isset($address_data_array[$key]['company']) ? $address_data_array[$key]['company']:'';
                $customer_data[$key]['postcode'] = isset($address_data_array[$key]['postcode']) ? $address_data_array[$key]['postcode']:'';
			}
			$customer_data[$key]['total_orders_today'] = isset($total_orders_today_data[$key]) ? $total_orders_today_data[$key] : 0;
		}
		
		return $customer_data;
	}

	/**
	 * this function is to filter customer by name or mobile or email
	 */

	public function getCustomersByNameMobileEmail($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM " . DB_PREFIX . "customer c WHERE 1=1 ";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_all'])) {
			$implodes[] = "c.email LIKE '" . $this->db->escape($data['filter_all']) . "%'" OR "c.telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'" OR "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_all']) . "%'";
		}

		if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}


		if ($implode) {
			$sql .= " And " . implode(" OR ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'c.telephone',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

		return $query->rows;
	}

	/**
	 * ends function to filter customer by  name or mobile or email
	 */


	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			return array(
				'address_id'     => $address_query->row['address_id'],
				'customer_id'    => $address_query->row['customer_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}
	}

	public function getAddresses($customer_id) {
		$address_data = array();

		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		foreach ($query->rows as $result) {
			$address_info = $this->getAddress($result['address_id']);

			if ($address_info) {
				$address_data[$result['address_id']] = $address_info;
			}
		}

		return $address_data;
	}

	public function getTotalCustomers($data = array()) {
		
		// Handling filter_order_count. If provided, it should be positive (>0) integer
		$filter_order_count = intval( $data['filter_order_count'] ?? 0 );
		
		if ( $filter_order_count > 0 ) {
			
			$sql = "SELECT COUNT(DISTINCT dt.customer_id) AS total 
			        FROM (
			               SELECT c.customer_id, 
			                      COUNT(DISTINCT o.order_id) AS order_count ";
		} else {
			
			$sql = "SELECT COUNT(DISTINCT c.customer_id) AS total ";
		}

        $sql .= " FROM " . DB_PREFIX . "customer c ";
        
        if ( !empty($data['filter_city']) 
          || !empty($data['filter_customer_postcode']) 
          || !empty($data['filter_company_name']) 
          ){
              $sql .= " LEFT JOIN " . DB_PREFIX . "address oa ON oa.address_id = c.address_id ";
           }

        if(!empty($data['filter_umrn_lan'])){
        	$sql .= " INNER JOIN " . DB_PREFIX . "customer_nach_details AS cnd ON cnd.customer_id = c.customer_id ";
        }
           
        if ( $filter_order_count > 0 ) {
			
			$sql .= " INNER JOIN " . DB_PREFIX . "customer c2 ON c2.master_id = c.master_id 
			          INNER JOIN " . DB_PREFIX . "order o ON o.customer_id = c2.customer_id AND 
			                                                 o.store_id IN (" . WSB_STORES_ID . ") AND 
			                                                 o.franchise_id = 0 
			          INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id AND 
			                                                       osub.order_status_id > 0 ";
		}
           
        $sql .= " WHERE 1=1 ";
        
        // FILTER(s)          
        $implode = array();

        if(!empty($data['filter_umrn_lan'])){
        	$implode[] = " 
        				  ( cnd.umrn_no LIKE '" . $this->db->escape($data['filter_umrn_lan']) . "' 
        				  			OR
        				  	cnd.lan_no LIKE '" . $this->db->escape($data['filter_umrn_lan']) . "'
        				  )
			        	";
        }

        if (!empty($data['filter_referral_code'])) {
            $implode[] = "c.referral_code = '" . $this->db->escape($data['filter_referral_code']) . "'";
        }

        if (!empty($data['filter_city'])) {
            $implode[] = "oa.city LIKE '" . $this->db->escape($data['filter_city']) . "%'";
        }

        if (!empty($data['filter_customer_postcode'])) {
            $implode[] = "oa.postcode LIKE '" . $this->db->escape($data['filter_customer_postcode']) . "%'";
        }

        if (!empty($data['filter_company_name'])) {
            $implode[] = "oa.company LIKE '" . $this->db->escape($data['filter_company_name']) . "%'";
        }

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_customer_id'])) {
			$implode[] = "c.customer_id = " . (int)$data['filter_customer_id'];
		}

		if (!empty($data['filter_master_id'])) {
			$implode[] = "c.master_id = " . (int)$data['filter_master_id'];
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

        if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'";
		}

		if (isset($data['filter_customer_type']) && $data['filter_customer_type'] !='all' ) {
			if($data['filter_customer_type'] == 4){
				$implode[] = "c.is_dropshipper IN (0,1)";	
			} else {
				$implode[] = "c.is_dropshipper IN (" . $data['filter_customer_type'] . ")";	
			}		
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
        
		if (isset($data['filter_customer_cid'])) {
			$implode[] = "c.customer_id = '" . (int)$data['filter_customer_cid'] . "'";
		}

		if (isset($data['filter_customer_id'])) {
			$implode[] = "c.customer_id != '" . (int)$data['filter_customer_id'] . "'";
		}
	
		if (isset($data['filter_gst_number'])) {
			$implode[] = "c.gst_number LIKE '" .  $this->db->escape(trim($data['filter_gst_number'])) . "'";
		}
    
        if (!empty($implode)) {
            $sql .= " AND " . implode(" AND ", $implode);
        }
		
		// If filter_order_count is provided ( > 0 ), then we need to GROUP BY on customer_id and use HAVING to filter
		if ( $filter_order_count > 0 ) {
			$sql .= " GROUP BY c.customer_id 
			          HAVING order_count >= " . (int)$filter_order_count . ") AS dt ";
		}
		
		$query = $this->db->query($sql);
        
		return (int)$query->row['total'];
	}

	public function getTotalAddressesByCustomerId($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "' ");

		return $query->row['total'];
	}

	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function addHistory($customer_id, $comment) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET customer_id = '" . (int)$customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getHistories($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT comment, date_added FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalHistories($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));

			$mail = new PHPMailer();
			// $mail->protocol = $this->config->get('config_mail_protocol');
			// $mail->parameter = $this->config->get('config_mail_parameter');
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->isSMTP();
			// $mail->SMTPDebug = 2;
			// $mail->Debugoutput = 'html';

			$mail->addAddress($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			// $mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->Subject = sprintf($this->language->get('text_transaction_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->Body = $message;
			$mail->send();
		}
	}

	public function deleteTransaction($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

    public function getCashbacks($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT customer_cashback_id, date_added, description, amount, amount_utilized, expired
                                   FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "' AND amount > 0
                                   ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

    public function getCashbackUsage($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT date_added, description, amount
                                   FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "' AND amount < 0
                                   ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

    public function getTotalCashbacks($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "' AND amount > 0");

		return $query->row['total'];
	}

    public function getTotalCashbackUsage($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "' AND amount < 0");

		return $query->row['total'];
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

    public function getCashbackTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "' AND amount > 0");

		return $query->row['total'];
	}

    public function getCashbackAvailable($customer_id) {
		$query = $this->db->query("SELECT SUM(amount - amount_utilized) AS total FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "'
                                     AND expired = 0
                                     AND amount > 0
                                     AND (amount - amount_utilized) > 0");

		return $query->row['total'];
	}

    public function getCashbackUtilized($customer_id) {
		$query = $this->db->query("SELECT SUM(amount_utilized) AS total FROM " . DB_PREFIX . "customer_cashback WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

    public function getCashbackExpired($customer_id) {
		$query = $this->db->query("SELECT SUM(amount - amount_utilized) AS total FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$customer_id . "' AND expired = 1 AND amount > 0");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();
		}
	}

	public function deleteReward($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");
	}

	public function getRewards($customer_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}

	public function getisdropshipper($customer_id) {
	   	$query = $this->db->query("SELECT is_dropshipper FROM `" . DB_PREFIX . "customer` WHERE `customer_id` = '" . $customer_id . "' ");
        if ($query->num_rows)
		    return $query->row['is_dropshipper'];
        else
            return false;
	}

	public function getcategories() {
		$sql = "SELECT cd.category_id,cd.name,c.parent_id FROM `oc_category_description` as cd LEFT JOIN oc_category as c ON c.category_id = cd.category_id WHERE c.parent_id= 0 AND c.status = 1 AND cd.language_id = 1";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getFiltersOfProducts($category_id, $filters = '') {

		$cats = $category_id;

		$q = "SELECT pf.filter_id
			   FROM " . DB_PREFIX . "product p
			   INNER JOIN " . DB_PREFIX . "product_to_category p2c
			   ON (p.product_id = p2c.product_id)
			   INNER JOIN " . DB_PREFIX . "product_filter pf
			   ON (p.product_id = pf.product_id)
			   INNER JOIN " . DB_PREFIX . "product_to_store p2s
			   ON (p.product_id = p2s.product_id)
			   WHERE p2c.category_id  = '". $cats ."'
			   AND p.status = '1'
			   AND p.quantity > 0
			   AND p.stock_status_id != '5'
			   AND p.date_available <= NOW()";

		//echo $q;
		$query = $this->db->query($q);
		//Skip filters for Size and Color for singles store and add
		$skip_filters = array(62, 63, 103, 104, 105, 106, 107, 108, 109, 110, 145,  246, 247, 248, 249, 250);

		foreach ($query->rows as $result) {
			if (in_array($result['filter_id'], array(145))) {
				continue;
			}
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if (!empty($implode)) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fgd.description, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");
			
			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");
				
				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
							'filter_id' => $filter['filter_id'],
							'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
							'filter_group_id' => $filter_group['filter_group_id'],
							'name'            => $filter_group['name'],
							'description'    => $filter_group['description'],
							'filter'          => $filter_data
					);
				}
			}
		}
		return $filter_group_data;
	}

	public function customer_preferences(){

		if(isset($this->request->get['customer_id'])){
			$customer_id = $this->request->get['customer_id'];
		}

		if(isset($this->request->post['quality'])){
			$quality = array();
			foreach ($this->request->post['quality'] as $value) {
				$quality[] = $value ;
			}
			$qualitys = implode(',', $quality);
		}else{
			$qualitys = '';

		}

		if(isset($this->request->post['category'])){

			foreach ($this->request->post['category'] as $value) {
				if(!empty($value['price-min'])){
					$price_min = $value['price-min'];
				}
				if(!empty($value['price-max'])){
					$price_max = $value['price-max'];
				}

				if(isset($value['category_id'])){
					if(!empty($value['category_id'])){
						$category_id = $value['category_id'];

						if(!empty($value['filter'])){
							$fil = $value['filter'];
							$filter = array();
							foreach ($fil as $value) {
								$filter[] = $value ;
							}
							$filters = implode(',', $filter);
						}

						$sql = "SELECT customer_id,category_id FROM ".DB_PREFIX."customer_preference WHERE customer_id = '".$customer_id."' AND category_id= '".$category_id."'";
						$query_data = $this->db->query($sql);
						$query_data = $query_data->row;
						if(!empty($query_data)){
							$sql = "UPDATE " . DB_PREFIX . "customer_preference SET customer_id = '".$customer_id."', category_id= '".$category_id."', filter_id= '".$filters."', min_price= '".$price_min."', max_price= '".$price_max."', modify= NOW() WHERE customer_id = '".$customer_id."'AND category_id= '".$category_id."'";
							$query = $this->db->query($sql);
						}else{
							$sql  = "INSERT INTO oc_customer_preference set customer_id = '".$customer_id."', category_id= '".$category_id."', filter_id= '".$filters."', min_price= '".$price_min."', max_price= '".$price_max."', created= NOW(), modify= NOW()";
							$query = $this->db->query($sql);
						}

					}
				}

			}
		}

	}

	public function get_customer_preferences($customer_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "customer_preference WHERE customer_id= '".$customer_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function get_customer_preferences_filter($customer_id,$category_id){
		$sql = "SELECT filter_id FROM " . DB_PREFIX . "customer_preference WHERE customer_id= '".$customer_id."' AND category_id= '".$category_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	// for autocomplete customer and email and telephone in customer

	public function getCustomerList($data = array()){
		$sql = "SELECT
                  customer_id,
                  CONCAT(c.firstname, ' ', c.lastname) AS name,
                  c.email,
                  c.telephone
                  FROM " . DB_PREFIX . "customer c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '" . $this->db->escape($data['filter_telephone']) . "%'";
		}

        if (!empty($data['filter_all'])) {
            $all  = " ( CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_all']) . "%' OR ";
            $all .= "c.email LIKE '" . $this->db->escape($data['filter_all']) . "%' OR ";
            $all .= "c.telephone LIKE '" . $this->db->escape($data['filter_all']) . "%' ) ";
			$implode[] = $all;
		}

        if (!empty($implode)) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

		return $query->rows;
	}


	// Expire a Cashback
	public function expireCashBack($csh_bck_id){

        // Expire cashback
        $sql = "UPDATE " . DB_PREFIX . "customer_cashback 
                SET expired = 1 
                WHERE customer_cashback_id = ". (int)$csh_bck_id;
        return $this->db->query($sql);
	}

	/**
	* Method For Record log about any information change in Cutomer Details from backend
	* @param $customer_id 	INTEGER
	* @param $changes_data  ARRAY
	* @param $source_field  String
	* @author Garvit, Updated BY Nishu (For admin_change_log)
	*/
	public function customerRecordLogs( $customer_id, $changes_data, $source_field ){
		if(!empty($changes_data)){
			$admin_change_data = array();

			$user_id = $this->user->getId();
			$user_name = $this->user->getUserName()['username'];

			$name = $this->user->getUserName()['name'];
			$dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
			$class = isset($dbt[1]['class']) ? $dbt[1]['class'] : '';
			$function = isset($dbt[1]['function']) ? $dbt[1]['function'] : '';
			$ref_url = $class .'/'. $function;
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$ip = $this->request->getIpAddress;
			$user_type = $this->user->getGroupName();
			$table_name = 'oc_customer';

			//Set Data to add into admin_change_log
			$admin_change_data['table_id']      = (int)$customer_id;
			$admin_change_data['user_id']       = $user_id;
			$admin_change_data['username']      = $user_name;
			$admin_change_data['table_name']    = $table_name;
			$admin_change_data['name']          = $name;
			$admin_change_data['source_field']  = $source_field;
			$admin_change_data['ref_url']       = $ref_url;
			$admin_change_data['file_location'] = $ref_url;
			$admin_change_data['user_agent']    = $user_agent;
			$admin_change_data['ip_address']    = $ip;
			$admin_change_data['user_type']     = $user_type;

			//Loop to add multiple enteries into admin_change log
			foreach ($changes_data as $key => $value) {
				
				$admin_change_data['old_value'] = $value['previous_value'];
				$admin_change_data['new_value'] = $value['new_value'];
				$admin_change_data['field_name'] = $key;

				//Call dynamic static function for entry into admin change log
				CommonLib::addAdminChangeLog($this->db, $admin_change_data);
			}
		}
	}

	/**
	 * _sendNotificationAndSmsToCustomer
	 * @info  Here, We Send a notification and sms to customer.
	 * @param $customer_id 	INTEGER
	 * @param $message 		INTEGER
	 * @author Garvit
	 **/
	Private function _sendNotificationAndSmsToCustomer($customer_id, $message){

		$customer_info = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id = ".$customer_id)->row;
		// SEND SMS
		if(isset($customer_info['telephone']) && !empty($customer_info['telephone']) && is_numeric($customer_info['telephone']) && !empty($message)){
			$send_sms = new SMS($message, $customer_info['telephone']);
			$send_sms->sendMessage();
		}
	}

    /**
     * [getCustomerById description]
     * @param  [int] $customer_id
     * @param  [array] $fields - array of fields you want to retrieve
     * @return array of customer data you mentioned in fields,if field is empty it return all fields
     * @author sudhanshu
     */
    public function getCustomerById( $customer_id , $fields ){
        $sql  = "SELECT ";
        $sql .= !empty($fields) ? implode(', ',$fields) : ' * ';
        $sql .= " FROM  ".DB_PREFIX."customer  ";
        $sql .= " WHERE customer_id = '".(int)$customer_id."' ";
        $results =  $this->db->query($sql);
        if($results->num_rows > 0){
            return $results->row;
        }
        else{
            return false;
        }
    }

    /* Method to update Bank details of customer
	* @param: $data array
	* @return: null
	* @author: Nishu
	*/
	public function updateBankDetails($data){
		if(!empty($data)){
			$sql  = "UPDATE ".DB_PREFIX."customer  ";
	        $sql .= "SET bank_ac_holder_name 	= '".$data['ac_holder']."', ";
	        $sql .= "bank_ac_number 			= '".$data['ac_no']."', ";
	        $sql .= "ifsc_code 					= '".$data['ifsc_code']."' ";
	        $sql .= " WHERE customer_id = ".(int)$data['customer_id'];
	        $this->db->query($sql);
	    }
	}

    public function getTotalCustomersByGst($gst_number) {

        $q = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE gst_number = '" . $this->db->escape(trim($gst_number)) . "' ";
        
		$query = $this->db->query($q);

		return $query->row['total'];
	}
	/**
	* Check this customer is already a master_id
	* @param: customer id
	* @author: kalyan 26th sep 2017
	*/
	public function checkCustomerIdIsMasterId($customerId){

		$q = "SELECT c.master_id FROM " . DB_PREFIX . "customer c 
        WHERE c.master_id= '" . (int)$customerId . "' AND c.customer_id!= '" . (int)$customerId . "' limit 1";
		
		$query = $this->db->query($q);
		
		if ($query->num_rows>0) {
			return '1';
		}else{
			return '0';
		}
	}
	/**
	* Check this customer has already a master_id
	* @param: customer id
	* @author: kalyan 26th sep 2017
	*/
	public function checkMasterCustomerHasMasterId($customerId){

		$q = "SELECT c.master_id FROM " . DB_PREFIX . "customer c WHERE c.customer_id= '" . (int)$customerId . "' AND c.master_id>0 limit 1";
		
		$query = $this->db->query($q);
		
		if ($query->num_rows>0) {
			return $query->row['master_id'];
		}else{
			return '0';
		}
	}
	/**
	* set a master id to order customer
	* @param: customer id
	* @author: kalyan 26th sep 2017
	*/
	public function setMasterCustomerIdToOrderCustomer($orderCustomerId,$masterCustomerId){

		$q = "UPDATE " . DB_PREFIX . "customer set master_id='" . (int)$masterCustomerId . "' WHERE customer_id= '" . (int)$orderCustomerId . "'";
		
		$query = $this->db->query($q);
		
		if ($query) {
			return '1';
		}else{
			return '0';
		}
	}
	/**
	* get order customer's master id
	* @param: customer id
	* @author: kalyan 27th sep 2017
	*/
	public function getOrderCustomerMasterId($customerId){

		$q = "SELECT c.master_id FROM " . DB_PREFIX . "customer c WHERE c.customer_id= '" . (int)$customerId . "' AND c.master_id>0 limit 1";
		
		$query = $this->db->query($q);
		
		if ($query->num_rows>0) {
			return $query->row['master_id'];
		}else{
			return '0';
		}
	}
	/**
	* get all customers id of a master customer id
	* @param: master id
	* @author: kalyan 27th sep 2017
	*/
	public function getCustomersIdOfMasterId($masterCustomerId){

		$q = "SELECT c.customer_id FROM " . DB_PREFIX . "customer c WHERE c.master_id= '" . (int)$masterCustomerId . "'";
		
		$query = $this->db->query($q);
		
		return $query->rows;
	}
	/**
	* get all duplicate customers
	* @author: kalyan 27th sep 2017
	*/
	public function getDuplicateCustomers(){

	 $q = "SELECT 
		c.customer_id,
		c.master_id,
		CONCAT(c.firstname, ' ', c.lastname) AS name,
		c.email,
		c.telephone,
		cl.ledger_name
		FROM " . DB_PREFIX . "customer c
		LEFT JOIN " . DB_PREFIX . "customer_ledgers cl
		on c.customer_id=cl.customer_id
		WHERE c.master_id>0";
		
		$query = $this->db->query($q);
		
		return $query->rows;
	}
	/**
	* get all master customers
	* @author: kalyan 27th sep 2017
	*/
	public function getMasterCustomers($customerIds){

	 $q = "SELECT 
		c.customer_id,
		c.master_id,
		CONCAT(c.firstname, ' ', c.lastname) AS name,
		c.email,
		c.telephone,
		cl.ledger_name
		FROM " . DB_PREFIX . "customer c
		LEFT JOIN " . DB_PREFIX . "customer_ledgers cl
		on c.customer_id=cl.customer_id
		WHERE c.customer_id IN(".implode(',',$customerIds).")";
		
		$query = $this->db->query($q);
		
		return $query->rows;
	}

	private function _checkFranchiseData($franchise_id){
	    $sql = "SELECT franchise_id FROM " . DB_PREFIX . "franchise_data WHERE franchise_id = '" .(int)$franchise_id . "' LIMIT 1";
        return $this->db->query($sql)->num_rows;
	}

    private function _addFranchiseData($franchise_data){
        if(!isset($franchise_data['franchise_id'])) return;
        
        // If franchise status is In-Active, then we will update only franchise status.
        // Also in this case we will not check whether the franchise data exists or not. If exist then update, otherwise do nothing.
        if (empty($franchise_data['franchise_status'])) {
            $sql = "UPDATE " . DB_PREFIX . "franchise_data SET 
                            franchise_status = '0'
                             WHERE franchise_id = '".(int)$franchise_data['franchise_id']."' ";

            $this->db->query($sql);
            return;
        }

        $franchise_id = $franchise_data['franchise_id'];
        $franchise_status = $franchise_data['franchise_status'] ?? '0';
	    $franchise_coupon = $franchise_data['franchise_coupon'] ?? '';
	    $franchise_discount = $franchise_data['franchise_discount'] ?? '';
	    $franchise_prefix = $franchise_data['franchise_prefix'] ?? '';

	    $update = $this->_checkFranchiseData($franchise_id);
	    if($update){
            $sql = "UPDATE " . DB_PREFIX . "franchise_data SET 
                            franchise_status = '".(int)$franchise_status."',
                            franchise_coupon = '".$this->db->escape($franchise_coupon)."',
                             franchise_discount = '".$this->db->escape($franchise_discount)."', 
                             franchise_prefix = '".$this->db->escape($franchise_prefix)."'
                             WHERE franchise_id = '".(int)$franchise_id."' ";
        }
        else{
            $sql = "INSERT INTO " . DB_PREFIX . "franchise_data SET 
                                franchise_status = '".(int)$franchise_status."',
                                franchise_id = '".(int)$franchise_id."',
                                franchise_coupon = '".$this->db->escape($franchise_coupon)."',
                                franchise_discount = '".$this->db->escape($franchise_discount)."', 
                                franchise_prefix = '".$this->db->escape($franchise_prefix)."' ";
        }
        $this->db->query($sql);
    }
    
    public function getAllMasterDuplicateCustomerIds($customer_ids) {
        
        if (empty($customer_ids)) {
            return array();
        }
       
        //to unset the keys of the values which are not available
        foreach($customer_ids as $key =>$value) {
                if (empty($value)){
                    unset($customer_ids[$key]);
                }
        }
      
      if(count($customer_ids) == 0)
      {
      	return array();
      }
        // Finding master_id and all the related (child + master) id(s) of the corresponding master, for the input customer_id(s)
        $sql = "SELECT oc1.customer_id, 
                       oc1.master_id, 
                       GROUP_CONCAT(DISTINCT oc2.customer_id) AS all_related_ids 
                FROM " . DB_PREFIX . "customer oc1 
                INNER JOIN " . DB_PREFIX . "customer oc2 ON oc1.master_id = oc2.master_id 
                WHERE oc1.customer_id IN (" . implode(',', $customer_ids) . ") 
                GROUP BY oc1.customer_id, oc2.master_id";

        $query = $this->db->query($sql);
        
        $customer_data = array();
        
        if ($query->num_rows) {
            
            foreach ($query->rows as $row) {
                
                $customer_data[$row['customer_id']] = $row;
                
                // Defaulting the id_status as it is a master_id
                $customer_data[$row['customer_id']]['id_status'] = 'master';
                
                // If customer_id != master_id, then this is an already marked DUPLICATE customer_id
                if ($row['customer_id'] != $row['master_id']) {
                    $customer_data[$row['customer_id']]['id_status'] = 'duplicate';
                } elseif ($row['customer_id'] == $row['master_id'] && $row['customer_id'] == $row['all_related_ids']) {
                    // If customer_id == master_id, and customer_id == all_related_ids, 
                    // then it is not having other related id(s) yet. It is  unmarked.
                    $customer_data[$row['customer_id']]['id_status'] = 'unmarked';
                }
            }
        }
        
        return $customer_data;
    }

    /**
    * Update GST Number and then we have to log the recordswhicih master Id's records are getting Updated.
    * @param $request_data array   contains_gst_number_for_log_tracking
    * @param $customer_id  integer customer_id
    * 
    * @author Ashish 17 Feb 2017
    */
    public function updateGSTNumber($request_data, $customer_id)
	{
		$gst_number = $request_data['gst_number']['new_value'] ?? '';
		$gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
		$gst_number = trim($gst_number);

		$sql = "UPDATE " . DB_PREFIX . "customer 
		       SET gst_number = '". $this->db->escape($gst_number) ."' 
		       WHERE customer_id = '" . (int)$customer_id . "' ";

		$result = $this->db->query($sql);

		// Start To log of the fields whose values are chaged, field_name, old_value and new_value of the columns
		//Commenting the code as time of code creation we had to log details now we dont need and commenting if we can make use in future here
		/*if ($result){
			$changes_data = $request_data;
			$source_field = 'gst_number_customer_id';
			$this->customerRecordLogs($customer_id, $changes_data, $source_field);
		}*/
		//End to Log the changes made in form
	}

	/**
    * Method for is franchise
    * @param $customer_id : string customer id
    * @return true or false
    * @author vikas , APR 2018
    */
	public function isFranchise($customer_id = ''){

        if( !empty($customer_id) ){
            $sql = "SELECT franchise_status FROM " . DB_PREFIX . "franchise_data  WHERE franchise_id = '". (int)$customer_id . "'";
            $query = $this->db->query($sql);
            if($query->num_rows){
                return ( (int)$query->row['franchise_status'] );
            }else{
                return 0;
            }
        } else {
        	return 0;
        }
    }

    public function isCustomerSeller($customer_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$customer_id;

		$res = $this->db->query($sql);

		if ($res->row['total'] == 0)
			return FALSE;
		else
			return TRUE;
  	}

  	public function checkExistCustomerShortSMS($implode_cid) {
  		
  		if(SITE_ENVIRONMENT != 'production'){
			return false;  			
  		}

        try {
            if(!empty($implode_cid)) {
                $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);

                $sql_msg = " select customer_id from short_message_logs where customer_id IN (".$implode_cid.") LIMIT 1";
                $query = $aws_mysqli->query($sql_msg);

                $sql_msg_old = " select customer_id from short_message_logs_old where customer_id IN (".$implode_cid.") LIMIT 1";
                $query_old = $aws_mysqli->query($sql_msg_old);


                if ($query->num_rows > 0 || $query_old->num_rows > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

    }

    public function getAllShortSmsLog($implode_cid, $start=0, $limit=2000){

        $limit = ($limit / 2);
        $limit_sql = " LIMIT $start, $limit ";

        $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
		if(mysqli_connect_errno()){
         	return array();
         }else{
		        $sql_msg = " SELECT * FROM short_message_logs WHERE customer_id IN (".$implode_cid.") ORDER BY timestamp DESC ".$limit_sql;
		        $query = $aws_mysqli->query($sql_msg);

		        $sql_msg_old = " SELECT * FROM short_message_logs_old WHERE customer_id IN (".$implode_cid.") ORDER BY timestamp DESC ".$limit_sql;
		        $query_old = $aws_mysqli->query($sql_msg_old);

		        if ($query->num_rows < 1 && $query_old->num_rows < 1) {

		            return array();
		        } else {

		            $sms_data = array();

		            if($query->num_rows > 0) {
		                while ($row = $query->fetch_assoc()) {
		                    $sms_data[] = $row;
		                }
		            }

		            if($query_old->num_rows > 0){
		                while($row_sms = $query_old->fetch_assoc())
		                {
		                    $sms_data[] = $row_sms;
		                }
		            }

		            return $sms_data;
		        }
    	}

    }

}
