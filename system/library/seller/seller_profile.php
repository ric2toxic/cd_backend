<?php
class SellerProfile {
	/**
	 *Update Seller Profile
	 */
	private $_registry;
	private $_db;
	private $_load;
	private $_sellerGSTObject;

	public function __construct($registry){
		$this->_registry = $registry;
	    if(method_exists($registry, 'get')){
	   		$this->_db = $registry->get('db');
	   		$this->_load = $registry->get('load');
	   	}else{
	   		$this->_db = $registry->db;
	   		$this->_load = $registry->load;
	   	}
		$this->_sellerGSTObject = new SellerGST($this->_registry);
	}

	/**
     * Update Seller Profile for fornt end seller
     */
    public function updateSellerProfile($data){
				// echo "<pre>";
				// print_r($data);
				// exit;
          if(isset($data) && !empty($data)){
                  $seller_id   = $data['seller_id'];
									if(isset($data['field']) && $data['field'] == 'additional_emails'){
											$sql = "DELETE FROM `" . DB_PREFIX . "customer_additional_email` WHERE customer_id = $seller_id";
											$query = $this->_db->query($sql);
											if($query){
												$old_value = $data['old_value'];
												$new_value = $data['update_value'];
												$update_type = $data["field"];
												$additional_emails_array = explode(',', $new_value);
                        foreach ($additional_emails_array as $addtinal_emails) {
                          $sql  = "INSERT INTO `" . DB_PREFIX . "customer_additional_email` SET ";
                          $sql .= "customer_id= $seller_id ,";
                          $sql .= "email= '".$addtinal_emails."' ";
                          $query = $this->_db->query($sql);
                        }
												if($query){
														$field = "profile";
														$result =  $this->updatelog($seller_id,$old_value,$new_value,$field,$update_type);
														return $result;
														exit;
												}
										  }
									 }
                  $sql  = "UPDATE `" . DB_PREFIX . "ms_seller` SET ";

                  if(isset($data["type"]) && !empty($data["type"])){
                        $primary_contact_name = (!empty($data["primary_contact_name"])) ? $data["primary_contact_name"] : '';
                        $primary_contact_no   = (!empty($data["primary_contact_no"])) ? $data["primary_contact_no"] : '';
                          if($data["type"] == 'same_as_primary_pickup'){
                              $sql .= "pickup_holder_name='".$primary_contact_name."',";
                              $sql .= "pickup_contact_no='".$primary_contact_no."',";
                              $sql .=  $data['type']."='1'";
                              $old_value["pickup_holder_name"] = $data["old_contact_name"];
                              $old_value["pickup_contact_no"]   = $data["old_contact_no"];
                              $new_value["pickup_holder_name"] = $primary_contact_name;
                              $new_value["pickup_contact_no"]   = $primary_contact_no;
                              $update_type = $data['type'];
                          }
                          if($data["type"] == 'same_as_primary_account'){
                                  $sql .= "account_holder_name='".$primary_contact_name."',";
                                  $sql .= "account_contact_no='".$primary_contact_no."',";
                                  $sql .=  $data['type']."='1'";
                                  $old_value["account_holder_name"] = $data["old_contact_name"];
                                  $old_value["account_contact_no"] = $data["old_contact_no"];
                                  $new_value["account_contact_no"] = $primary_contact_no;
                                  $new_value["account_holder_name"] = $primary_contact_name;
                                  $update_type = $data['type'];
                          }
                          if($data["type"] == 'same_as_primary_inventory'){
                              $sql .= "inventory_holder_name='".$primary_contact_name."',";
                              $sql .= "inventory_contact_no='".$primary_contact_no."',";
                              $sql .=  $data['type']."='1'";
                              $old_value["inventory_holder_name"] = $data["old_contact_name"];
                              $old_value["inventory_contact_no"] = $data["old_contact_no"];
                              $new_value["inventory_holder_name"] = $primary_contact_name;
                              $new_value["inventory_contact_no"] = $primary_contact_no;
                              $update_type = $data['type'];
                          }
                          if($data["type"] == 'same_as_primary_address'){
                              $sql .= "pickup_address='".$data['primary_address']."',";
                              $sql .= "pickup_city='".$data['primary_city']."',";
															$sql .= "pickup_pincode='".$data['primary_pincode']."',";
                              $sql .= "pickup_zone_id='".$data['primary_zone']."',";
                              $sql .= "pickup_country_id='99',";
                              $sql .=  $data['type']."='1'";
                              $old_value["pickup_address"] = $data["old_pickup_address"];
                              $old_value["pickup_city"]    = $data["old_pickup_city"];
															$old_value["pickup_pincode"] = $data["old_pickup_pincode"];
                              $old_value["pickup_zone_id"] = $data["old_pickup_zone_id"];
                              $new_value["pickup_address"] = $data['primary_address'];
                              $new_value["pickup_city"]    = $data['primary_city'];
															$new_value["pickup_pincode"] = $data['primary_pincode'];
                              $new_value["pickup_zone_id"] = $data['primary_zone'];
                              $update_type = $data['type'];
                          }
                          $sql .= " WHERE seller_id = $seller_id";
                   } else{
                          if(isset($data['same_as_primary'])){
                                  if($data["field"] == 'pickup_holder_name' || $data["field"] == 'pickup_contact_no'){
                                      $sql .= "same_as_primary_pickup='0', ";
                                  }
                                  if($data["field"] == 'account_holder_name' || $data["field"] == 'account_contact_no'){
                                      $sql .= "same_as_primary_account='0', ";
                                  }
                                  if($data["field"] == 'inventory_holder_name' || $data["field"] == 'inventory_contact_no'){
                                      $sql .= "same_as_primary_inventory='0',";
                                  }
                          }
                          if($data['field']  == "primary_contact_name" || $data['field']  == "primary_contact_no" ){
                              $sql .= "same_as_primary_pickup='0', ";
                              $sql .= "same_as_primary_account='0', ";
                              $sql .= "same_as_primary_inventory='0',";
                          }
                          $old_value   = $data['old_value'];
                          $new_value   = $data['update_value'];
                          $update_type = $data["field"];
                          $sql .= $data['field'] ." = '".$new_value."' ";
                          $sql .= "WHERE seller_id = $seller_id";
                   }
                  	$query = $this->_db->query($sql);
                  	if($query){
                      	$field = "profile";
                      	$result =  $this->updatelog($seller_id,$old_value,$new_value,$field,$update_type);
												return $result;
                  }
          }
     }

	/**
     * Update Busniess Seller Profile for fornt end seller
     */
    public function updateBusniessProfile($data){
        if(isset($data) && !empty($data)){
						if($data["field"] == "tin"){
							$old_value["tin_tax_type"] = $data["old_tin_tax_type"];
							$new_value["tin_tax_type"] = $data['tin_tax_type'];
						}
            $seller_id    = $data["seller_id"];
						$seller_nickname = $data["seller_nickname"];
            $old_value[$data["field"]] = $data["old_value"];
            $old_value[$data["field"]."_image"]    = $data["img"];
            $field = "business";
            $new_value[$data["field"]]    = $data["update_value"];
            $pan_image_name = $data["upload_file"]["name"];
            $tmpName        = $data["upload_file"]["tmp_name"];
            $currentDate    = date("Y-m-d");
            $file_name      = $seller_id.$data["field"].$currentDate.$pan_image_name;
            $new_value[$data["field"]."_image"] = DIR_SELLER_UPLOADS . $seller_nickname .'/'. $file_name;
						if(!file_exists(DIR_SELLER_UPLOADS.$seller_nickname)){
							mkdir(DIR_SELLER_UPLOADS.$seller_nickname, 0777, true);
						}
						$targetPath = DIR_SELLER_UPLOADS.$seller_nickname.'/'.$file_name;
            move_uploaded_file($tmpName,$targetPath);
            $result = $this->updatelog($seller_id,$old_value,$new_value,$field);
            $final_result["msg"] = $result['msg'];
            $final_result["update_id"] = $result['update_id'];
						$final_result["data"] = $file_name;
						return $final_result;
         }
    }

    public function validate($value,$type){
        if($type == "ifsc_code"){

            // system/helper/utilities.php
            $json = validateBankIFSC($value);
            echo $json;
            exit;
        }
    }

	/**
    * Method for update seller log
    */
    public function updatelog($seller_id,$old_value = array(),$new_value = array(),$field,$update_type = '', $other_data = array()){
            if($field == "profile"){
              if( !empty($other_data['seller_change_update_id']) ){
                $verification_status = 'attached_tin_address';
              }else{
                $verification_status = "not_required";
              }
            } elseif ($field == "business") {
              $verification_status = "pending";
            }

            if(is_array($old_value)){

                $fields = array_keys($old_value);
                foreach ($fields as $key => $value) {
                  $this->_db->query("UPDATE " . DB_PREFIX ."seller_updates
                                     SET verification_status = 'change_by_seller'
                                     WHERE seller_id = '" . (int) $seller_id ."'
                                       AND update_type = '" . $this->_db->escape($value) . "'
                                       AND (verification_status = 'pending' OR verification_status = 'attached_tin_address')");

                  $sql = "INSERT INTO `" . DB_PREFIX . "seller_updates`
                          SET seller_id = '".$seller_id."' ,
                              update_group = '".$field."',
                              update_type =  '".$value."',
                              new_value ='". $this->_db->escape($new_value[$value]) . "',
                              verification_status = '".$verification_status."',
                              date_added = NOW(),
                              previous_value = '". $this->_db->escape($old_value[$value]) . "'";
                  if( !empty($other_data['seller_change_update_id'])){
                    $additional_details = array('update_id'=>$other_data['seller_change_update_id']);
                    $sql .= " ,additional_details = '" . $this->_db->escape(serialize($additional_details)). "'";
                  }
                  $query = $this->_db->query($sql);
                }
                return array('msg'=> "success", 'update_id'=> $this->_db->getLastId());
            } else {

              $sql = "INSERT INTO `" . DB_PREFIX . "seller_updates`
                      SET seller_id = '".$seller_id."' ,
                          update_group = '".$field."',
                          update_type =  '".$update_type."',
                          new_value ='". $this->_db->escape($new_value) . "',
                          verification_status = '".$verification_status."',
                          date_added = NOW(),
                          previous_value = '". $this->_db->escape($old_value) . "'";
              $query = $this->_db->query($sql);
              if($query){
                return array('msg'=> "success", 'update_id'=> $this->_db->getLastId());
              }
            }
    }

	/**
	* Method for get Seller Log histroy i.e. change email id , telephone no. etc
	* @param $seller_id : Integer of seller id
	* @return get array of history of seller activity
	* @author vikas, 2017
	*/

	public function getSellerUpdateActivity( $seller_id ){

		$sql = "SELECT update_id,
					   update_group,
					   update_type,
					   new_value,
					   date_added,
					   verification_status,
					   verified_by,
             previous_value,
             additional_details
				FROM " . DB_PREFIX . "seller_updates
				WHERE seller_id = " . (int)$seller_id . "
				ORDER BY date_added DESC LIMIT 10 ";
		$query = $this->_db->query( $sql );

		if( $query->num_rows ) {
			return $query->rows;
		} else {
			return false;
		}
	}

  /**
  * Method for get zone name for change Log histroy
  * @param $data : array of zone id
  * @return get array of zone name value  with zone id key
  * @author vikas, 2017
  */

  public function getZoneName($data = array()){

      $zone_array = array();

      if(!empty($data)){

          $zones = implode(',', $data);

          if (substr($zones, -1, 1) == ',')
          {
              $zones = substr($zones, 0, -1); // remove last char if comma exsist in string
          }
          if(!empty($zones)) {
            $sql = "SELECT 
                        zone_id,
                        name 
                    FROM " . DB_PREFIX . "zone 
                    WHERE 
                        country_id = 99 
                        AND 
                        zone_id IN  (" . $zones . " ) ";
            $query = $this->_db->query($sql);

            if( !empty($query) ){
                foreach ($query->rows as $key => $value) {
                    $zone_array[$value['zone_id']]  = $value['name'];
                }
            }
          }
      }

      return $zone_array;
  }


    /**
    * Method for get pending verification  i.e. change email id , telephone no. etc
    * @param $seller_id : Integer of seller id
    * @return get array of history of pending verification
    * @author vikas, 2017
    */

    public function getPendingVerification( $seller_id ){

        $sql = "SELECT update_id,
                       update_group,
                       update_type,
                       new_value,
                       date_added,
                       verification_status,
                       verified_by,
                       previous_value
                FROM " . DB_PREFIX . "seller_updates
                WHERE verification_status IN ('".$this->_db->escape('pending')."','".$this->_db->escape('attached_tin_address')."')
                  AND seller_id = '" . (int)$seller_id ."'
                ORDER BY date_added DESC ";

        $query = $this->_db->query( $sql );

        if( $query->num_rows ) {
            return $query->rows;
        } else {
            return array();
        }
    }

	/* comment after open when separate seller panel created
	/**
	* Method for update verification with approved or Cancel and insert history into seller log, and update perticular field in ms seller
	* @param $seller_id : Integer of seller id
	* @param $update_id : Integer of update id
	* @param $validation_status : String of validation status ie. approved or Cancel
	* @param $input_name : String of column name of ms seller ie. tin or bank_ac_number or tin etc...
	* @param $new_input_value : String of new value for seller log
	* @return NULL
	* @author vikas, 2017
	*/

	public function updateVerificationApproved( $data = array() ){

    $seller_id         = $data['seller_id'];
    $update_id         = $data['update_id'];
    $update_type       = $data['update_type'];
    $verification_status= $data['verification_status'];
    $seller_data = !empty( $data['seller_data'] ) ? $data['seller_data'] : '';

    // fetach previous value from ms seller table by input name
    $previous_value_sql = "SELECT " . $update_type . "
        FROM " . DB_PREFIX . "ms_seller
        WHERE seller_id =  " . (int)$seller_id ;
    $previous_value_sql = $this->_db->query($previous_value_sql);

    $update_types_array = explode(',', $update_type);

    if( $verification_status == 'approved' ){
      foreach ($update_types_array as $update_type) {
        $sql = "UPDATE " . DB_PREFIX . "seller_updates
                SET verification_status = '". $this->_db->escape($verification_status)."',
                    verified_by = '" . $this->_db->escape($this->_registry->user->getUserName()['username']) . "',
                    previous_value = '" . $this->_db->escape($previous_value_sql->row[$update_type]) . "',
                    date_added = NOW()
                WHERE update_id IN (" . $this->_db->escape($update_id) . ")
                  AND update_type = '". $update_type."'";
        $this->_db->query( $sql );
      }

      // update ms seller table
      foreach($seller_data as $seller_field_key => $seller_field_value){
	  
		/*** $sellerGSTObject->updateSellerGstNumber function updates gst_arn and pan columns too;
		  hence skipped if gst_provisional_id column updation is there else will be updated as usual ***/
		if(!empty($seller_data['gst_provisional_id']) && ($seller_field_key == "gst_arn" || $seller_field_key == "pan")) {
		   continue;
		}
		
		/*** updateSellerGstNumber using sellerGSTObject ***/
		if($seller_field_key == "gst_provisional_id") {
			$seller_gst_arn = $seller_data['gst_arn'] ?? "";
			$sellerGSTObject = new SellerGST($this->_registry);
			$sellerGSTObject->updateSellerGstNumber((int)$seller_id, $seller_field_value, $seller_gst_arn);
		} else {
	        $sql = "UPDATE " . DB_PREFIX . "ms_seller
	                SET $seller_field_key = '" . $this->_db->escape($seller_field_value). "'
	                WHERE seller_id = " . (int)$seller_id ;
	        $this->_db->query($sql);
		}
      }
    }

    if( $verification_status == 'cancelled' ){
      foreach ($update_types_array as $update_type) {
        $sql = "UPDATE " . DB_PREFIX . "seller_updates
                SET verification_status = '". $this->_db->escape($verification_status)."',
                    verified_by = '" . $this->_db->escape($this->_registry->user->getUserName()['username']) . "',
                    date_added = NOW()
                WHERE update_id IN (" . $this->_db->escape($update_id) . ")
                  AND update_type = '". $update_type."'";
        $this->_db->query( $sql );
      }
    }
  }



	/* comment after open when separate seller panel created */
	/**
	* Method for add seller
	* @param $data : array of seller data
	* @return NULL
	* @author vikas, 2017
	*/
	// this function copy from model/sellers/sellers by vikas, 2017
    public function addSeller($data = array()) {
        // set nickname
        $seller_nickname = ( !empty($data['seller_nickname']) ? trim($data['seller_nickname']) : '' );

        if( $seller_nickname ){
            // insert into firstname and lastname
            $name = explode('_', $seller_nickname);
            $firstname = trim($name[0]);
            $lastname = trim($name[1]);

            // insert seller seokeyword
            $seller_seokeyword = trim(strtolower(str_replace('_', '-',$seller_nickname)));

        } else {
            $firstname = '';
            $lastname = '';
            $pickup_city_code = '';
            $seller_seokeyword = '';
        }

        // check if seller already exists in DB or not
        $total_sellers = $this->getTotalSellerByEmailTelephoneToValidate($data['seller_email'], $data['seller_telephone']);
        $customer_id = 0;
        if( $total_sellers ){
          return array('status'=> false, 'message' => 'Seller already exists !');
        } else{
          //check if there are customers against this email OR telephone
          $total_customer = $this->getTotalCustomerByEmailTelephoneToValidate($data['seller_email'], $data['seller_telephone']);

          if( $total_customer['total'] == 1 ) {
            $customer_id = $total_customer['customer_id'];
            $sql = "UPDATE ". DB_PREFIX ."customer
                    SET email = '" . $this->_db->escape(trim(strtolower($data['seller_email']))) . "',
                        firstname = '" . $this->_db->escape(trim($firstname)) . "',
                        lastname = '" . $this->_db->escape(trim($lastname)) . "',
                        telephone = '" . $this->_db->escape(trim($data['seller_telephone'])) . "',
                        bank_ac_holder_name = '" .$this->_db->escape(trim($data['bank_ac_holder_name'])) . "',
                        bank_ac_number = '" .$this->_db->escape(trim($data['bank_ac_number'])) . "',
                        ifsc_code = '" .$this->_db->escape(trim(strtoupper($data['ifsc_code']))) . "'
                    WHERE customer_id = '". (int)$customer_id ."'";
            $this->_db->query($sql);
          } else if( $total_customer['total'] == 0 ){

            $salt = substr(md5(uniqid(rand(), true)), 0, 9);
            $password_secret = password_hash($data['seller_password'], PASSWORD_DEFAULT);


            $sql = "INSERT INTO ". DB_PREFIX ."customer
                    SET email = '" . $this->_db->escape(trim(strtolower($data['seller_email']))) . "',
                        firstname = '" . $this->_db->escape(trim($firstname)) . "',
                        lastname = '" . $this->_db->escape(trim($lastname)) . "',
                        password = '" . $password_secret . "',
                        password_mode = 'new',
                        telephone = '" . $this->_db->escape(trim($data['seller_telephone'])) . "',
                        bank_ac_holder_name = '" .$this->_db->escape(trim($data['bank_ac_holder_name'])) . "',
                        bank_ac_number = '" .$this->_db->escape(trim($data['bank_ac_number'])) . "',
                        ifsc_code = '" .$this->_db->escape(trim(strtoupper($data['ifsc_code']))) . "',
                        date_added = NOW()";
            $this->_db->query($sql);
            $customer_id = $this->_db->getLastId();
            $sql = "UPDATE " . DB_PREFIX . "customer SET master_id = '" . (int)$customer_id . "' WHERE customer_id = " . (int)$customer_id;
            $this->_db->query($sql);
          } elseif( $total_customer['total'] > 1 ) {
            return array('status'=> false, 'message' => 'More than 2 Customer Records already exist! Please contact administrator!!');;
          }
        }

        if(!empty($data['sor_enable_checkbox'])){
            $sor_enabled = $data['sor_enable_checkbox'];
            // update oc_wsb_seller_to_store
            $this->_db->query("INSERT INTO " . DB_PREFIX . "wsb_seller_to_store
                              SET seller_markup_over_tp = '" . (float)$data['seller_markup'] . "',
                                  wsb_commission_over_tp = '" . (float)$data['wsb_commission'] . "',
                                  store_id = 9
                                  seller_id = '" . (int)$seller_id . "'");
        }else{
            $sor_enabled = 0;
        }


        $data['same_as_primary_pickup']  = !empty($data['same_as_primary_pickup']) ? $data['same_as_primary_pickup'] : '0';
        $data['same_as_primary_account']  = !empty($data['same_as_primary_account']) ? $data['same_as_primary_account'] : '0';
        $data['same_as_primary_inventory']  = !empty($data['same_as_primary_inventory']) ? $data['same_as_primary_inventory'] : '0';
        $data['same_as_primary_address']  = !empty($data['same_as_primary_address']) ? $data['same_as_primary_address'] : '0';

        $actual_address = $data['seller_firm_address'];
        $address1 = ucwords(trim(substr($actual_address, 0, strpos($actual_address, ',', strpos($actual_address, ',')+1)+1)));
        $address2 = ucwords(trim(substr($actual_address, strpos($actual_address, ',', strpos($actual_address, ',')+1)+1, strlen($actual_address))));

        if(!empty($data['seller_app_only'])){
          $data['app_only'] = $data['seller_app_only'];
        }else{
          $data['app_only'] = 0;
        }

        if( !empty($data['tin'])){
          $tin_tax_type = '2';
        }else{
          $tin_tax_type = '1';
        }

        $data['seller_non_returnable'] = $data['seller_non_returnable'] ?? 0;
        $data['prefix_mode']           = $data['prefix_mode'] ?? 0;
        $data['product_name_prefix']   = str_replace(' ', '', $data['product_name_prefix']);
        $data['product_name_prefix']   = ucfirst( $data['product_name_prefix'] );
        
        $gst = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $data['gst_provisional_id']);
        $gst = trim($gst);

        $sql = "INSERT INTO " . DB_PREFIX ."ms_seller
               SET seller_id = '" . (int)$customer_id . "',
                   nickname = '" . $this->_db->escape(trim(strtoupper($seller_nickname))) . "',
                   company = '" .$this->_db->escape(trim($data['seller_firmname'])) . "',
                   prefix_mode = '" .(int)$data['prefix_mode'] . "',
                   mobile_no = '" .$this->_db->escape(trim($data['seller_telephone'])) . "',
                   email = '" .$this->_db->escape(trim(strtolower($data['seller_email']))) . "',
                   address1 = '" .$this->_db->escape($address1) . "',
                   address2 = '" .$this->_db->escape($address2) . "',
                   city = '" .$this->_db->escape(trim(ucwords($data['city']))) . "',
                   pincode = '" .$this->_db->escape(trim($data['pincode'])) . "',
                   country_id = " .(int)($data['country_id']) . ",
                   zone_id = " .(int)($data['zone_id']) . ",
                   pickup_address = '" .$this->_db->escape(trim(ucwords($data['pickup_address']))) . "',
                   pickup_city = '" .$this->_db->escape(trim(ucwords($data['pickup_city']))) . "',
                   pickup_pincode = '" .$this->_db->escape(trim($data['pickup_pincode'])) . "',
                   pickup_country_id = " .(int)($data['pickup_country']) . ",
                   pickup_zone_id = " .(int)($data['pickup_zone']) . ",
                   seller_status = " .(int)($data['seller_status']) . ",
                   primary_contact_name = '" .$this->_db->escape(trim($data['primary_contact_name'])) . "',
                   primary_contact_no = '" .$this->_db->escape(trim($data['primary_contact_no'])) . "',
                   same_as_primary_pickup = " .(int)($data['same_as_primary_pickup']) . ",
                   pickup_holder_name = '" .$this->_db->escape(trim($data['pickup_holder_name'])) . "',
                   pickup_contact_no = '" .$this->_db->escape(trim($data['primary_contact_no'])) . "',
                   same_as_primary_account = " .(int)($data['same_as_primary_account']) . ",
                   same_as_primary_address = " .(int)($data['same_as_primary_address']) . ",
                   account_holder_name  = '" .$this->_db->escape(trim($data['account_holder_name'])) . "',
                   account_contact_no = '" .$this->_db->escape(trim($data['account_contact_no'])) . "',
                   same_as_primary_inventory = " .(int)($data['same_as_primary_inventory']) . ",
                   inventory_holder_name = '" .$this->_db->escape(trim($data['inventory_holder_name'])) . "',
                   inventory_contact_no = '" .$this->_db->escape(trim($data['inventory_contact_no'])) . "',
                   pan = '" .$this->_db->escape(trim(strtoupper($data['pan']))) . "',
                   tin = '" .$this->_db->escape(trim($data['tin'])) . "',
                   tin_tax_type = '" .$this->_db->escape($tin_tax_type) . "',
                   bank_ac_holder_name = '" .$this->_db->escape(trim($data['bank_ac_holder_name'])) . "',
                   bank_ac_number = '" .$this->_db->escape(trim($data['bank_ac_number'])) . "',
                   ifsc_code = '" .$this->_db->escape(trim(strtoupper($data['ifsc_code']))) . "',
                   seller_group = '1',
                   sor_enabled = '".(int)$sor_enabled."',
                   pickup_city_code = '" .$this->_db->escape(strtoupper(trim($data['pickup_city_code']))) . "',
                   app_only = '" . (int)$data['app_only'] . "',
                   non_returnable = '" . (int)$data['seller_non_returnable'] . "',
                   non_serviceable_areas = '" .$this->_db->escape($data['non_serviceable_areas']) . "',
                   date_created = NOW(),
                   gst_arn = '" .$this->_db->escape($data['gst_arn']) . "',
                   gst_provisional_id = '" .$this->_db->escape($gst) . "',";

        if(!empty($data['product_name_prefix'])){
          $sql .= "product_name_prefix = '" . $this->_db->escape($data['product_name_prefix']) . "' ";
        }else{
          $sql .= "product_name_prefix = NULL ";
        }

        $this->_db->query($sql);

        if(!empty($data['seller_additional_email'])){
            $array_additional_email = explode(',',rtrim(trim($data['seller_additional_email']),','));
            foreach($array_additional_email as $emails){
                $this->_db->query("INSERT INTO oc_customer_additional_email
                        SET customer_id = '" . $customer_id . "',
                        email = '" . $this->_db->escape(trim(strtolower($emails))) . "'");
            }
        }

        // adding content in oc_review_rules
        //get content of oc_review_rules

        if(isset($data['category_rating']) && !empty($data['category_rating'])){
            foreach($data['category_rating'] as $data_rating){
                if($data_rating['rating'] != 0){
                    $this->_db->query("INSERT INTO oc_review_rules
                          SET seller_id = ".(int)$customer_id.",
                                rule_type = 'category',
                                category_id = ".(int)$data_rating['category_id'].",
                                rating = ".(int)$data_rating['rating']."
                       ");
                }
            }
        }

        if(isset($data['categories_global_review']) && ($data['categories_global_review'] != 0 )){
            $this->_db->query("INSERT INTO oc_review_rules
                              SET seller_id = ".(int)$customer_id.",
                                rating = ".$this->_db->escape($data['categories_global_review']).",
                                rule_type = 'global'");
        }

        $this->_db->query("INSERT INTO ". DB_PREFIX ."url_alias
                           SET query = 'seller_id=".$customer_id."',
                               keyword = '" . $this->_db->escape($seller_seokeyword) . "'
                         ");
        return array('status'=> true);
        
        //update seller promotion
        if(count($data['promotion']) > 0){
            $this->updateSellerPromotion($seller_id,$data['promotion']);
        }
    }

	/**
	* Method for update seller information
	* @param $seller_id : Integer of seller id
	* @param $data : array of seller data
	* @return NULL
	* @author vikas, 2017
	*/
	// this function copy from model/sellers/sellers by vikas, 2017
	public function editSeller($seller_id, $data) {
        // if user something change values of seller information
        $changes_data = json_decode($data['changes_data'],true);

        if( !empty($changes_data['seller_status'])  && !empty($changes_data['additional_details']) ){
            $changes_data['seller_status']['additional_details'] = $changes_data['additional_details']['new_value'];
            unset($changes_data['additional_details']);
        }

        if( $changes_data ){
            foreach ($changes_data as $key => $change_detail) {
                $additional_details = !empty($change_detail['additional_details']) ?  $change_detail['additional_details'] : '';
                $sql = "INSERT INTO " . DB_PREFIX . "seller_updates
                        SET seller_id = " . (int)$seller_id . ",
                            update_type = '" . stripcslashes($this->_db->escape($key)) . "',
                            update_group = '" . $this->_db->escape($change_detail['update_group']) . "',
                            previous_value = '" . $this->_db->escape($change_detail['previous_value']) . "',
                            new_value = '" . $this->_db->escape($change_detail['new_value']) . "',
                            verification_status = 'admin_updated',
                            verified_by =  '" . $this->_db->escape($this->_registry->user->getUserName()['username']) . "',
                            additional_details = '" . $this->_db->escape($additional_details) . "',
                            date_added = NOW()
                            ";
                $query = $this->_db->query($sql);
            }
        }

        // Getting current status of the seller for future comparisons
        $currentSellerStatus = $this->getSellerStatus($seller_id);

        // get seller nickname
        $seller_nickname = ( $data['seller_nickname'] ) ? $data['seller_nickname'] : '';

        if( $seller_nickname ){
            // insert into firstname and lastname
            $name = explode('_', $seller_nickname);
            $firstname = $name[0];
            $lastname = $name[1];

            // insert seller seokeyword
            $seller_seokeyword = strtolower(str_replace('_', '-',$seller_nickname));

        } else {
            $firstname = '';
            $lastname = '';
            $pickup_city_code = '';
            $seller_seokeyword = '';
        }


        // check sor enabled
        if(isset($data['sor_enable_checkbox'])){
            $sor_enabled = $data['sor_enable_checkbox'];
            $check_markup_commission = $this->checkMarkupCommission($seller_id);

            if( $check_markup_commission ){
                // update oc_wsb_seller_to_store
                $this->_db->query("UPDATE " . DB_PREFIX . "wsb_seller_to_store
                                  SET seller_markup_over_tp = '" . (float)$data['seller_markup'] . "',
                                      wsb_commission_over_tp = '" . (float)$data['wsb_commission'] . "'
                                  WHERE seller_id = '" . (int)$seller_id . "'");
            } else {
                // insert oc_wsb_seller_to_store
                $this->_db->query("INSERT INTO " . DB_PREFIX . "wsb_seller_to_store
                                  SET seller_markup_over_tp = '" . (float)$data['seller_markup'] . "',
                                      wsb_commission_over_tp = '" . (float)$data['wsb_commission'] . "',
                                      store_id = 9,
                                      seller_id = '" . (int)$seller_id . "'");
            }

        }else{
            $sor_enabled = 0;
        }


        $sql = "UPDATE ". DB_PREFIX ."customer
                            SET email = '" . $this->_db->escape($data['seller_email']) . "',
                                firstname = '" . $this->_db->escape($firstname) . "',
                                lastname = '" . $this->_db->escape($lastname) . "',
                                telephone = '" . $this->_db->escape($data['seller_telephone']) . "',
                                bank_ac_holder_name = '" .$this->_db->escape($data['bank_ac_holder_name']) . "',
                                bank_ac_number = '" .$this->_db->escape($data['bank_ac_number']) . "',
                                ifsc_code = '" .$this->_db->escape($data['ifsc_code']) . "'
                            WHERE customer_id = '". (int)$seller_id ."'
               ";
        $this->_db->query($sql);


         if (!empty($data['seller_password']) ) {

            $password_secret = password_hash($data['seller_password'], PASSWORD_DEFAULT);

            $this->_db->query("UPDATE " . DB_PREFIX . "customer
                              SET
                              password = '" .$password_secret . "',
                              password_mode = 'new'
                              WHERE customer_id = '" . (int)$seller_id . "'");

        }


        $data['same_as_primary_pickup']  = !empty($data['same_as_primary_pickup']) ? $data['same_as_primary_pickup'] : '0';
        $data['same_as_primary_account']  = !empty($data['same_as_primary_account']) ? $data['same_as_primary_account'] : '0';
        $data['same_as_primary_inventory']  = !empty($data['same_as_primary_inventory']) ? $data['same_as_primary_inventory'] : '0';
        $data['same_as_primary_address']  = !empty($data['same_as_primary_address']) ? $data['same_as_primary_address'] : '0';

        $app_only = 0;
        if( !empty($data['seller_app_only']) ) {
          $app_only = 1 ;
        }

        $actual_address = $data['seller_firm_address'];
        $second_comma_position = strpos($actual_address, ',', strpos($actual_address, ',')+1);
        $address1 = ucwords(trim($actual_address));
        $address2 = '';
        if ($second_comma_position) {
          $address1 = ucwords(trim(substr($actual_address, 0, $second_comma_position+1)));
          $address2 = ucwords(trim(substr($actual_address, $second_comma_position+1, strlen($actual_address))));
        }

        if( !empty($data['tin'])){
          $tin_tax_type = '2';
        }else{
          $tin_tax_type = '1';
        }
		
		/*** updating gst before others because of approved validation in gst update 
			i.e. we don't update gst if seller_id != nickname (assumption is seller gst is already approved)
			hence will not be updated 
			Also, PAN and GST ARN will also be updated in this function only if gst number is updated ***/
		$this->_sellerGSTObject->updateSellerGstNumber((int) $seller_id, $data['gst_provisional_id'], $data['gst_arn'], true);
    $data['seller_non_returnable'] = $data['seller_non_returnable'] ?? 0;
    $data['prefix_mode']           = $data['prefix_mode'] ?? 0;
    $data['product_name_prefix']   = str_replace(' ', '', $data['product_name_prefix']);
    $data['product_name_prefix']   = ucfirst( $data['product_name_prefix'] );

    $sql = "UPDATE " . DB_PREFIX ."ms_seller
            SET nickname = '" .$this->_db->escape( $seller_nickname ) . "',
               company = '" .$this->_db->escape($data['seller_firmname']) . "',
               prefix_mode = '" .(int)$data['prefix_mode'] . "',
               mobile_no = '" .$this->_db->escape($data['seller_telephone']) . "',
               email = '" .$this->_db->escape($data['seller_email']) . "',
               address1 = '" .$this->_db->escape($address1) . "',
               address2 = '" .$this->_db->escape($address2) . "',
               city = '" .$this->_db->escape($data['city']) . "',
               pincode = '" .$this->_db->escape($data['pincode']) . "',
               country_id = " .(int)($data['country_id']) . ",
               zone_id = " .(int)($data['zone_id']) . ",
               pickup_address = '" .$this->_db->escape($data['pickup_address']) . "',
               pickup_city = '" .$this->_db->escape($data['pickup_city']) . "',
               pickup_pincode = '" .$this->_db->escape($data['pickup_pincode']) . "',
               pickup_country_id = " .(int)($data['pickup_country']) . ",
               pickup_zone_id = " .(int)($data['pickup_zone']) . ",
               seller_status = " .(int)($data['seller_status']) . ",
               primary_contact_name = '" .$this->_db->escape($data['primary_contact_name']) . "',
               primary_contact_no = '" .$this->_db->escape($data['primary_contact_no']) . "',
               same_as_primary_pickup = " .(int)($data['same_as_primary_pickup']) . ",
               pickup_holder_name = '" .$this->_db->escape($data['pickup_holder_name']) . "',
               pickup_contact_no = '" .$this->_db->escape($data['pickup_contact_no']) . "',
               same_as_primary_account = " .(int)($data['same_as_primary_account']) . ",
               account_holder_name  = '" .$this->_db->escape($data['account_holder_name']) . "',
               account_contact_no = '" .$this->_db->escape($data['account_contact_no']) . "',
               same_as_primary_inventory = " .(int)($data['same_as_primary_inventory']) . ",
               same_as_primary_address = " .(int)($data['same_as_primary_address']) . ",
               inventory_holder_name = '" .$this->_db->escape($data['inventory_holder_name']) . "',
               inventory_contact_no = '" .$this->_db->escape($data['inventory_contact_no']) . "',
               tin = '" .$this->_db->escape($data['tin']) . "',
               tin_tax_type = '" .$this->_db->escape($tin_tax_type) . "',
               bank_ac_holder_name = '" .$this->_db->escape($data['bank_ac_holder_name']) . "',
               bank_ac_number = '" .$this->_db->escape($data['bank_ac_number']) . "',
               ifsc_code = '" .$this->_db->escape($data['ifsc_code']) . "',
               pickup_city_code = '" .$this->_db->escape(strtoupper(trim($data['pickup_city_code']))) . "',
               sor_enabled = " . (int)$sor_enabled . ",
               app_only = '" . (int)$app_only . "',
               non_returnable = '" . (int)$data['seller_non_returnable'] . "',
               non_serviceable_areas = '" .$this->_db->escape($data['non_serviceable_areas']) . "',";
            
        if(!empty($data['product_name_prefix'])){
          $sql .= "product_name_prefix = '" . $this->_db->escape($data['product_name_prefix']) . "' ";
        }else{
          $sql .= "product_name_prefix = NULL ";
        }
        $sql .= " WHERE seller_id = " . (int) $seller_id;

        $this->_db->query($sql);


        // Additional emails of the seller
        $this->_db->query("DELETE FROM oc_customer_additional_email WHERE customer_id = '" . (int)$seller_id . "'");

        if(!empty($data['seller_additional_email'])){
            $array_additional_email = explode(',',rtrim(trim($data['seller_additional_email']),','));
            foreach($array_additional_email as $emails){
                $this->_db->query("INSERT INTO oc_customer_additional_email
                                  SET customer_id = '" . (int)$seller_id . "',
                                      email = '" . $this->_db->escape($emails) . "'");
            }
        }


        // Updating product statuses, if the seller status is changed.
        if ( (int)$data['seller_status'] != $currentSellerStatus) {

            if ( (int)$data['seller_status'] == 1) {
                $this->_db->query("UPDATE  ". DB_PREFIX ."product p
                                  LEFT JOIN ". DB_PREFIX ."ms_product mp ON (mp.product_id = p.product_id)
                                  SET p.status = '1'
                                  WHERE mp.seller_id = '". (int)$seller_id . "'
                                    AND p.status = '0'");

            } else if ( (int)$data['seller_status'] == 2 OR $data['seller_status'] == 3) {
                $this->_db->query("UPDATE  ". DB_PREFIX ."product p
                                  LEFT JOIN ". DB_PREFIX ."ms_product mp ON (mp.product_id = p.product_id)
                                  SET p.status = '0'
                                  WHERE mp.seller_id = '". (int)$seller_id . "'
                                    AND p.status = '1'");
            }
        }

        //$this->updateSorSetting($seller_id,$data['sor_enable_checkbox']);


        /*------------------------------------ Updating Review/Rating rules ------------------------------------*/
        $this->_db->query("DELETE FROM " . DB_PREFIX . "review_rules WHERE seller_id = '" . (int)$seller_id . "'");

        if(isset($data['category_rating']) && !empty($data['category_rating'])){
            foreach($data['category_rating'] as $data_rating){
                if($data_rating['rating'] != 0 ){
                    $this->_db->query("INSERT INTO oc_review_rules
                          SET seller_id = ".(int)$seller_id.",
                                rule_type = 'category',
                                category_id = ".(int)$data_rating['category_id'].",
                                rating = ".(int)$data_rating['rating']."
                       ");
                }
            }
        }
        
        if(isset($data['categories_global_review']) && ($data['categories_global_review'] != 0)){
            $this->_db->query("INSERT INTO oc_review_rules
                              SET seller_id = ".(int)$seller_id.",
                                  rating = ".$this->_db->escape($data['categories_global_review']).",
                                  rule_type = 'global'");
        }

        /*** set product rating  in oc_product table*/
        $this->_load->model('catalog/product','admin');
        $this->_registry->admin_model_catalog_product->setProductRatingWhenProductAssignToSeller($seller_id);
        /*** */

        $check_seoKeyword = $this->_db->query("SELECT * FROM oc_url_alias WHERE query = 'seller_id=". (int)$seller_id. "'");

        if( !empty( $seller_seokeyword ) ){
            if($check_seoKeyword->num_rows > 0){
                $this->_db->query("UPDATE ". DB_PREFIX ."url_alias
                            SET keyword = '" . $this->_db->escape($seller_seokeyword) . "'
                            WHERE query = 'seller_id=". (int)$seller_id. "'
                        ");
            }else{
                $this->_db->query("INSERT INTO ". DB_PREFIX ."url_alias
                        SET query = 'seller_id=".(int)$seller_id."',
                            keyword = '" . $this->_db->escape($seller_seokeyword) . "'
                        ");
            }
        }
        
        //update seller promotion
        // if(count($data['promotion']) > 0){
        //     $this->updateSellerPromotion($seller_id,$data['promotion']);
        // }
    }

    /**
	* Method for delete seller a/c
	* @param $seller_id : Integer of seller id
	* @return NULL
	* @author vikas, 2017
	*/
	// this function copy from model/sellers/sellers by vikas, 2017
    public function deleteSeller($seller_id){
        $this->_db->query("DELETE FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
        $this->_db->query("DELETE FROM " . DB_PREFIX . "ms_product WHERE seller_id = '" . (int)$seller_id . "'");
    }

    // Get only seller status for a seller_id
    /**
	* Method for get seller status
	* @param $seller_id : Integer of seller id
	* @return seller_status value with 1 other wise return false
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getSellerStatus($seller_id) {

    	$sql = "SELECT seller_status
    			FROM " . DB_PREFIX . "ms_seller
    			WHERE seller_id = '" . (int)$seller_id . "'";
        $query = $this->_db->query($sql);

        if ($query->num_rows)
            return (int)($query->row['seller_status']);
        else
            return false;
    }


    /**
	* Method for get seller list
	* @param $data : array of filter data
	* @return seller information with in array format
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getSellers($data = array()){
        if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] < 0 ) {
          $seller_ids = $this->getSellerIdOfVerificationStatus();
          if(!$seller_ids){
            return array();
          }
        }
        $sql = "SELECT  ms.seller_id,
                        CONCAT(c.firstname, ' ', c.lastname) AS seller,
                        ms.nickname,
                        ms.company,
                        ms.product_name_prefix,
                        ms.prefix_mode,
                        c.email,
                        c.telephone,
                        ms.date_created,
                        ms.seller_status,ms.gst_provisional_id,
                        ms.exclusive, ";
        if(!empty($data['get_address'])){
            $sql .= " ms.address1, ms.address2, ms.city, " ;
        }
        $sql .=         "(SELECT COUNT(*) as total FROM `oc_ms_product` WHERE seller_id = ms.seller_id ) as product_total,
                        (SELECT COUNT(*) as total FROM `oc_review_rules` WHERE seller_id = ms.seller_id ) as seller_total_rating,
                        ms.vacation_mode
                FROM ". DB_PREFIX ."ms_seller ms
                INNER JOIN ". DB_PREFIX ."customer c
                ON (ms.seller_id = c.customer_id)";

        if (!empty($data['filter_sale'])) {
            $sql .= " INNER JOIN ". DB_PREFIX ."ms_product mp
                ON (ms.seller_id = mp.seller_id)
                INNER JOIN ". DB_PREFIX ."product_special ps
                ON (mp.product_id = ps.product_id)
                AND date_start <= DATE(NOW()) AND date_end >= DATE(NOW())";
        }

        $sql .= ' WHERE 1=1 ';

        if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] < 0 ) {
          $sql .= ' AND ms.seller_id IN ('. implode(',', $seller_ids ).') ' ;
        }

        if (!empty($data['only_seller_invoice_generate'])) {
          $sql .= " AND ms.seller_invoice_generate=1 ";
        }

        if (!empty($data['filter_seller'])) {
            $sql .= " AND ms.nickname LIKE '%" . $this->_db->escape($data['filter_seller']) . "%'";
        }

        if (!empty($data['filter_company'])) {
            $sql .= " AND ms.company LIKE '%" . $this->_db->escape($data['filter_company']) . "%'";
        }

        if (!empty($data['filter_product_name_prefix'])) {
            $sql .= " AND ms.product_name_prefix LIKE '%" . $this->_db->escape($data['filter_product_name_prefix']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $sql .= " AND c.email LIKE '%" . $this->_db->escape($data['filter_email']) . "%'";
        }

        if (!empty($data['filter_telephone'])) {
            $sql .= " AND c.telephone LIKE '%" . $this->_db->escape($data['filter_telephone']) . "%'";
        }

        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND ms.seller_id = '" . $this->_db->escape($data['filter_seller_id']) . "'";
        }

        if (isset($data['filter_vacation']) and $data['filter_vacation'] != "*") {
            $sql .= " AND ms.vacation_mode = '" . (int)$data['filter_vacation'] . "'";
        }

        if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] != "*") {
          if($data['filter_seller_status'] > 0 ){
            $sql .= " AND ms.seller_status = '" . $this->_db->escape($data['filter_seller_status']) . "'";
          }
        }        

        if (isset($data['filter_rating']) and $data['filter_rating'] != "*") {
            $sql .= " HAVING seller_total_rating = '" . (int)$data['filter_rating'] . "'";
        }

        if (!empty($data['filter_city'])) {
            $sql .= " AND ms.city LIKE '%" . $this->_db->escape($data['filter_city']) . "%'";
        }
        if (!empty($data['filter_state'])) {
            $sql .= " AND ms.zone_id = '".(int)$this->_db->escape($data['filter_state'])."'";
        }

        $sort_data = array(
            'ms.nickname',
            'ms.company',
            'c.firstname',
            'c.email',
            'c.telephone',
            'ms.seller_id',
            'ms.seller_status',
            'ms.date_created',
            'product_total'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY ms.nickname";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 30;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->_db->query($sql);
        return $query->rows;
    }

    /**
	* Method for get total seller
	* @param $data : array of filter data
	* @return total sellers
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getTotalSellers($data = array()){
        if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] < 0 ) {
          $seller_ids = $this->getSellerIdOfVerificationStatus();
          if(!$seller_ids){
            return 0;
          }
        }
        $sql = "SELECT ms.seller_id, ms.seller_status,
                  (SELECT COUNT(*) as total FROM `oc_review_rules` WHERE seller_id = ms.seller_id ) as seller_total_rating
                FROM ". DB_PREFIX ."ms_seller ms
                INNER JOIN ". DB_PREFIX ."customer c
                  ON (ms.seller_id = c.customer_id)";

        if (!empty($data['filter_sale'])) {
            $sql .= " INNER JOIN ". DB_PREFIX ."ms_product mp
                ON (ms.seller_id = mp.seller_id)
                INNER JOIN ". DB_PREFIX ."product_special ps
                ON (mp.product_id = ps.product_id) WHERE
                date_start <= DATE(NOW()) AND date_end >= DATE(NOW())";
        }

        $sql .= " WHERE 1=1 ";

         if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] < 0 ) {
            $sql .= ' AND ms.seller_id IN ('. implode(',', $seller_ids ).') ' ;
        }

        if (!empty($data['filter_seller'])) {
            $sql .= " AND ms.nickname LIKE '%" . $this->_db->escape($data['filter_seller']) . "%'";
        }

        if (!empty($data['filter_company'])) {
            $sql .= " AND ms.company LIKE '%" . $this->_db->escape($data['filter_company']) . "%'";
        }

        if (!empty($data['filter_product_name_prefix'])) {
            $sql .= " AND ms.product_name_prefix = '" . $this->_db->escape($data['filter_product_name_prefix']) . "'";
        }

         if (!empty($data['filter_email'])) {
            $sql .= " AND c.email LIKE '%" . $this->_db->escape($data['filter_email']) . "%'";
        }

        if (!empty($data['filter_telephone'])) {
            $sql .= " AND c.telephone LIKE '%" . $this->_db->escape($data['filter_telephone']) . "%'";
        }

        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND ms.seller_id = '" . $this->_db->escape($data['filter_seller_id']) . "'";
        }

        if (!empty($data['filter_vacation']) ) {
            $sql .= " AND ms.vacation_mode = '" . (int)$data['filter_vacation'] . "'";
        }

        if ( !empty($data['filter_rating']) ) {
            $sql .= " HAVING seller_total_rating = '" . (int)$data['filter_rating'] . "'";
        }

        if (!empty($data['filter_seller_status']) ) {
          if($data['filter_seller_status'] > 0 ){
            $sql .= " AND ms.seller_status = '" . $this->_db->escape($data['filter_seller_status']) . "'";
          }
        }
        
        if (!empty($data['filter_city'])) {
            $sql .= " AND ms.city = '" . $this->_db->escape($data['filter_city']) . "'";
        }

        if (!empty($data['filter_state'])) {
            $sql .= " AND ms.zone_id = '".(int)$this->_db->escape($data['filter_state'])."'";
        }
        
        $query = $this->_db->query($sql);
        return $query->num_rows;
    }

    /**
	* Method for get seller clearance sale status
	* @param $seller_id : Integer of seller id
	* @return product id
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getSellerClearanceSaleStatus($seller_id) {
        $sql = "SELECT ps.product_id
                FROM ". DB_PREFIX ."ms_product mp
                INNER JOIN ". DB_PREFIX ."product_special ps
                ON (mp.product_id = ps.product_id)
                WHERE mp.seller_id = ".$seller_id ."
                AND date_start <= DATE(NOW()) AND date_end >= DATE(NOW())";

        $query = $this->_db->query($sql);
        return $query->row;
    }

    /**
	* Method for get seller information by seller id
	* @param $seller_id : Integer of seller id
	* @return seller information with in array format
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getSeller($seller_id) {
        $query = $this->_db->query("SELECT ms.nickname,
                                           ms.product_name_prefix,
                                           ms.prefix_mode,
                                           ms.company,
                                           ms.email,
                                           ms.address1,
                                           ms.address2,
                                           ms.city,
                                           ms.pincode,
                                           ms.country_id,
                                           ms.zone_id,
                                           ms.pickup_address,
                                           ms.pickup_city,
                                           ms.pickup_pincode,
                                           ms.pickup_country_id,
                                           ms.pickup_zone_id,
                                           ms.same_as_primary_address,
                                           ms.seller_status,
                                           ms.sor_enabled,
                                           ms.pan,
                                           ms.tin,
                                           ms.tin_tax_type,
                                           ms.bank_ac_holder_name,
                                           ms.bank_ac_number,
                                           ms.ifsc_code,
                                           ms.primary_contact_name,
                                           ms.primary_contact_no,
                                           ms.account_holder_name,
                                           ms.account_contact_no,
                                           ms.inventory_holder_name,
                                           ms.inventory_contact_no,
                                           ms.pickup_holder_name,
                                           ms.pickup_contact_no,
                                           ms.same_as_primary_pickup,
                                           ms.same_as_primary_inventory,
                                           ms.same_as_primary_account,
                                           ms.app_only,
                                           ms.non_serviceable_areas,
                                           ms.non_returnable,
                                           ms.pickup_city_code,
                                           c.telephone,
                                           wss.seller_markup_over_tp,
                                           wss.wsb_commission_over_tp,
                                           ms.gst_arn,
                                           ms.gst_provisional_id,
                                           ms.gst_certificate_image
                                    FROM ". DB_PREFIX ."ms_seller ms
                                    INNER JOIN ". DB_PREFIX ."customer c
                                      ON (ms.seller_id = c.customer_id)
                                    LEFT JOIN ". DB_PREFIX ."wsb_seller_to_store wss
                                      ON (wss.seller_id = ms.seller_id)
                                    WHERE ms.seller_id = '" . (int)$seller_id . "'
                                  ");

        $additional_emails = $this->_db->query("SELECT email FROM ". DB_PREFIX ."customer_additional_email WHERE customer_id = ". $seller_id);
        $query->row['additional_email'] = implode(',',array_column($additional_emails->rows,'email'));

        $seokeyword = $this->_db->query("SELECT keyword FROM `oc_url_alias` WHERE query = 'seller_id=".$seller_id."' GROUP BY keyword ");
        $query->row['keyword'] = !empty($seokeyword->row['keyword']) ? $seokeyword->row['keyword'] : '';

        return $query->row;
    }

    /**
	* Method for get seller category rating
	* @param $seller_id : Integer of seller id
	* @return seller id, category id , rating and name
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getSellerCategoryRating($seller_id){
        $sql = "SELECT rr.seller_id,
                        rr.category_id,
                        rr.rating,
                        cd.name
                FROM oc_review_rules rr
                LEFT JOIN oc_category_description cd
                   ON (rr.category_id = cd.category_id)
                WHERE rr.seller_id = ".$seller_id . "
                   AND cd.language_id = 1 ";
        $query = $this->_db->query($sql);

        return $query->rows;
    }

    /**
	* Method for get seller global rating
	* @param $seller_id : Integer of seller id
	* @return seller id, category id , rating
	* @author vikas, 2017
	*/
    // this function copy from model/sellers/sellers by vikas, 2017
    public function getSellerGlobalRating($seller_id){
        $sql = "SELECT rr.seller_id,
                        rr.category_id,
                        rr.rating
                FROM oc_review_rules rr
                WHERE rr.seller_id = ".$seller_id . "
                   AND rr.category_id = '0'
                ";
        $query = $this->_db->query($sql);
        return $query->row;
    }

    /**
	 * Enable/disable SOR selling for a seller
	 * @param $seller_id
	 * @param $sor_setting
	 * @return array
	 */
    // this function copy from msseller library by vikas, 2017
	public function updateSorSetting($seller_id, $sor_setting){

		$response = array();
		if($seller_id > 0){
			 $sql = "UPDATE ". DB_PREFIX."ms_seller
					SET sor_enabled = ".$sor_setting."
					WHERE seller_id = ".$seller_id;
			$this->_load->language('seller/manage-inventory');

			if($this->_db->query($sql)){
				if($sor_setting == 1){
					$label = $this->_registry->language->get('ms_sor_disabled');
					$css = 'sor_disabled';
					$change_sor = 0;
				}else{
					$label = $this->_registry->language->get('ms_sor_enabled');
					$css = 'sor_enabled';
					$change_sor = 1;
				}
				$response = array('label'=>$label, 'css'=>$css, 'change_sor'=>$change_sor);

			}
			$solr = new SolrProduct($this->_registry);
			if($sor_setting == 1) {
				//Copy product to SOR
				$solr->copySellerProductToSor($seller_id);
			}else{
				//Remove product to SOR
				$solr->removeSellerProductToSor($seller_id);
			}

		}
		return $response;
	}



    /**
    * Method for get all agreements for sellers
    */
    public function get_agreements(){
            $result = $this->_db->query("SELECT * FROM `" . DB_PREFIX . "seller_agreement` WHERE status =1");
            return $result->rows;
    }

    /**
    * Method for get agreement status of seller
    */
    public function get_seller_agreement_status($seller_id){
            if(isset($seller_id)){
                    $sql = "SELECT  COUNT(agreement_id) AS total_agreement,
                                    GROUP_CONCAT(agreement_id) AS agreement_ids,
                                    seller_id,acceptance_date
                                    FROM`" . DB_PREFIX . "seller_agreement_status` WHERE `seller_id` = 1 ";

                     $query = $this->_db->query($sql);
                     return $query->row;
            }
    }

    /**
    * Method for get uncheck agreement of seller
    */
    public function getUncheckSellerAgreement($agreement_id){
            if(isset($agreement_id) && !empty($agreement_id)){
                $agreement_ids = implode(",",$agreement_id);
                $result = $this->_db->query("SELECT agreement_id,clause_content,date_added FROM `". DB_PREFIX ."seller_agreement` WHERE agreement_id IN($agreement_ids)");
                return $result->rows;
            }
    }

		public function updateTinType($data){
			if(isset($data) && !empty($data)){
				if(is_array($data)){
						$sql = "UPDATE   ". DB_PREFIX."ms_seller SET
										tin_tax_type='1'
										WHERE seller_id=".$data['seller_id']."";
						$query = $this->_db->query($sql);
						if($query){
							$sql = "INSERT INTO ". DB_PREFIX."seller_updates
			 								SET seller_id = ".$data['seller_id'].",
											update_group='business',
											update_type='tin_tax_type',
											new_value='".$data['tin_type']."',
											date_added=NOW(),
											verification_status='not_required',
											previous_value='".$data['old_tin_tax_type']."'";
							$query = $this->_db->query($sql);
						}
			  }
		  }
		}

    /**
    * Method for get new nick name
    * @param $seller_id : Integer of seller id
    * @return seller id, category id , rating
    * @author vikas, 2017
    */
    public function getNewNickname( $citycode, $seller_id ){

        $new_nickname = '';

        if( $citycode == 'ST' || $citycode == 'DL' || $citycode == 'MU' || $citycode == 'KL') {
            $sql = "SELECT SUBSTRING(nickname,1,3) as nickname
                FROM " . DB_PREFIX. "ms_seller
                WHERE nickname LIKE '%_" .$this->_db->escape($citycode) . "'
                ORDER BY CAST( SUBSTRING( nickname,1,3) as UNSIGNED) DESC
                limit 1 ";

            $query = $this->_db->query($sql);

            if( is_numeric($query->row['nickname']) ){
                $new_nickname = $query->row['nickname'];
                $new_nickname = str_pad(($new_nickname + 1), 3, '0',STR_PAD_LEFT);

                if( $new_nickname <= 999){
                    $new_nickname = $new_nickname.'_'.$citycode;
                } else {
                    $new_nickname = '';
                }
            } else {
                $new_nickname = '001_'.$citycode;
            }

        } else if( $citycode == 'JP' ) {
            $sql = "SELECT SUBSTRING(nickname, 2,2) as nickname
                FROM " . DB_PREFIX. "ms_seller
                WHERE nickname LIKE 'X%_JP'
                ORDER BY CAST( SUBSTRING( nickname, 2,2 ) as UNSIGNED ) DESC
                limit 1 ";

            $query = $this->_db->query($sql);

            if( $query->num_rows ){
                $new_nickname =  $query->row['nickname'];
                $new_nickname = str_pad(($new_nickname + 1), 2 , '0' , STR_PAD_LEFT) ;

                if( $new_nickname <= 99 ){
                    $new_nickname = 'X'.$new_nickname.'_'.$citycode;
                } else {
                    $new_nickname = '';
                }
            } else {
                $new_nickname = 'X01_'.$citycode;
            }
        }

        return $new_nickname;
    }

    /**
    * Method for check markup and commission value of perticular seller id
    * @param $seller_id : Integer of seller id
    * @return true or false
    * @author vikas, 2017
    */
    public function checkMarkupCommission( $seller_id ){
        $sql = "SELECT seller_id
                FROM " . DB_PREFIX . "wsb_seller_to_store
                WHERE seller_id = " . (int)$seller_id ;
        $query = $this->_db->query($sql);

        if( $query->num_rows ){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Method Update seller new address for firm
    */
    public function updateSellerBankDetailsAndAddress($data){
        if(isset($data) && !empty($data)){
            $seller_id      = $data["seller_id"];
            if($data['type'] == 'gst_details'){
              $image_name  = $_FILES["upload_file"]["name"];
              $tmpName     = $_FILES["upload_file"]["tmp_name"];
              $currentDate = date("Y-m-d");
              $file_name   = $seller_id."gst_ceritificate_upload".$currentDate.$image_name;
              if(!file_exists(DIR_SELLER_UPLOADS.$data['seller_nickname'])){
                mkdir(DIR_SELLER_UPLOADS.$data['seller_nickname'], 0777, true);
              }
              $targetPath = DIR_SELLER_UPLOADS.$data['seller_nickname'].'/'.$file_name;
              move_uploaded_file($tmpName,$targetPath);
              $new_value["gst_arn"]               = $data["gst_arn"];
              $old_value["gst_arn"]               = $data["old_gst_arn"];
              $new_value["gst_provisional_id"]    = $data["gst_provision_id"];
              $old_value["gst_provisional_id"]    = $data["old_gst_provision_id"];
              $old_value['gst_certificate_image'] = $data["img"];
              $new_value["gst_certificate_image"] = DIR_SELLER_UPLOADS.$data['seller_nickname'].'/'.$file_name;
              $field = "business";
              $result =  $this->updatelog($seller_id,$old_value,$new_value,$field);
              if($result['msg'] == 'success'){
                $final_result["msg"] = "update seller gst details";
                $final_result["data"] = $file_name;
                return $final_result;
              }
            } elseif($data['type'] == 'bank_details'){
								$seller_nickname = $data["seller_nickname"];
                $image_name  = $_FILES["upload_file"]["name"];
                $tmpName     = $_FILES["upload_file"]["tmp_name"];
                $currentDate = date("Y-m-d");
                $file_name   = $seller_id."cancel_cheque".$currentDate.$image_name;
								if(!file_exists(DIR_SELLER_UPLOADS.$data['seller_nickname'])){
									mkdir(DIR_SELLER_UPLOADS.$data['seller_nickname'], 0777, true);
								}
								$targetPath = DIR_SELLER_UPLOADS.$data['seller_nickname'].'/'.$file_name;
                move_uploaded_file($tmpName,$targetPath);
                $new_value["bank_ac_holder_name"] = $data["bank_ac_holder_name"];
                $old_value["bank_ac_holder_name"] = $data["old_bank_ac_holder_name"];
                $old_value['cancel_cheque_image'] = $data["img"];
                $new_value["cancel_cheque_image"] = DIR_SELLER_UPLOADS.$data['seller_nickname'].'/'.$file_name;
                $new_value["bank_ac_number"]      = $data["bank_ac_number"];
                $old_value["bank_ac_number"]      = $data["old_bank_ac_number"];
                $new_value["ifsc_code"]           = $data["ifsc_code"];
                $old_value["ifsc_code"]           = $data["old_ifsc_code"];
                $field = "business";
                $result =  $this->updatelog($seller_id,$old_value,$new_value,$field);
                if($result['msg'] == 'success'){
                  $final_result["msg"] = "update seller bank details";
									$final_result["data"] = $file_name;
									return $final_result;
                }
            } elseif($data["type"] == "pickup_address"){
                $pickup_address              = $data["pickup_address"];
                $new_value["pickup_address"] = $data["pickup_address"];
                $old_value["pickup_address"] = $data["old_pickup_address"];
                $pickup_city                 = $data["pickup_city"];
                $new_value["pickup_city"]    = $data["pickup_city"];
                $old_value["pickup_city"]    = $data["old_pickup_city"];
								$pickup_pincode              = $data["pickup_pincode"];
								$new_value["pickup_pincode"] = $data["pickup_pincode"];
                $old_value['pickup_pincode'] = $data["old_pickup_pincode"];
								$pickup_zone_id              = $data["pickup_zone_id"];
                $new_value["pickup_zone"] = $data["pickup_zone_id"];
                $old_value["pickup_zone"] = $data["old_pickup_zone_id"];
                $field = "profile";
                $sql = "UPDATE `" . DB_PREFIX . "ms_seller` SET
                                pickup_address    = '".$this->_db->escape($pickup_address)."',
                                pickup_city       = '".$this->_db->escape($pickup_city)."',
																pickup_pincode    = '".$this->_db->escape($pickup_pincode)."',
                                pickup_zone_id    = '".(int)$pickup_zone_id."',
                                pickup_country_id = '99',
                                same_as_primary_address = 0
                                WHERE seller_id   =" . (int)$seller_id;
                $query = $this->_db->query($sql);
                if($query){
                    $result =  $this->updatelog($seller_id,$old_value,$new_value,$field);
                    if($result['msg'] == "success"){
                            $final_result["msg"] = "pickup details updated";
														return $final_result;
                    }
                }
            } else{
                $address         = $data["address"];
                $old_address     = $data["old_address"];
                $seller_city     = $data["seller_city"];
								$pincode         = $data["pincode"];
                $seller_zone_id  = $data["seller_zone_id"];

                // full address split into address1 and address2  
                $actual_address = $address;
                $second_comma_position = strpos($actual_address, ',', strpos($actual_address, ',')+1);
                $address1 = ucwords(trim($actual_address));
                $address2 = '';
                if ($second_comma_position) {
                  $address1 = ucwords(trim(substr($actual_address, 0, $second_comma_position+1)));
                  $address2 = ucwords(trim(substr($actual_address, $second_comma_position+1, strlen($actual_address))));
                }

                $new_value["address1"] = $address1;
                $new_value["address2"] = $address2;
                $new_value["city"]     = $data["seller_city"];
                $new_value["pincode"]  = $data["pincode"];
                $new_value["zone_id"]  = $data["seller_zone_id"];

                // full address split into address1 and address2
                if(!empty($old_address)) {
                  $old_actual_address = $old_address;
                  $second_comma_position = strpos($old_actual_address, ',', strpos($old_actual_address, ',')+1);
                  $old_address1 = ucwords(trim($old_actual_address));
                  $old_address2 = '';
                  if ($second_comma_position) {
                    $old_address1 = ucwords(trim(substr($actual_address, 0, $second_comma_position+1)));
                    $old_address2 = ucwords(trim(substr($actual_address, $second_comma_position+1, strlen($actual_address))));
                  }
                } else {
                  $old_address1 = '';
                  $old_address2 = '';
                }

                $old_value["address1"] = $old_address1;
                $old_value["address2"] = $old_address2;
                $old_value["city"]     = $data["old_seller_city"];
                $old_value["pincode"]  = $data["old_pincode"];
                $old_value["zone_id"]  = $data["old_seller_zone_id"];

                $other_data = array();
                $other_data['seller_change_update_id'] = $data['seller_change_update_id'];
                $field = "profile";

                if( empty($other_data['seller_change_update_id']) && $other_data['seller_change_update_id'] == 0 ){
                  $sql = "UPDATE `" . DB_PREFIX . "ms_seller`
                          SET address1    = '".$this->_db->escape($address1)."',
                              address2    = '".$this->_db->escape($address2)."',
                              city        = '".$this->_db->escape($seller_city)."',
                              pincode     = '".$this->_db->escape($pincode)."',
                              zone_id     = '".(int)$seller_zone_id."',
                              country_id  = '99',
                              same_as_primary_address = 0
                              WHERE seller_id=" . (int)$seller_id;
                  $query = $this->_db->query($sql);
                }
                  $result =  $this->updatelog($seller_id,$old_value,$new_value,$field, '' , $other_data);
                  if($result['msg'] == "success"){
                      $final_result["msg"] =  "update seller address details";
											return $final_result;
                  }
            }
        }
    }

    /**
    * Method for get Seller Id of verification status
    * @return pending seller ids in array
    * @author vikas/sudhanshu, 2017
    */
    public function getSellerIdOfVerificationStatus(){
      $sql = "SELECT seller_id
              FROM " . DB_PREFIX. "seller_updates
              WHERE verification_status = 'pending'
              GROUP BY seller_id";
      $query = $this->_db->query($sql);

      $seller_ids = array();
      if( $query->num_rows ) {
        $seller_ids = array_column($query->rows, 'seller_id');
        return $seller_ids;
      } else {
        return false;
      }
    }

    /**
    * Method for existsing seller account
    * @return total of existsing seller account
    * @author vikas, 2017
    */
    public function getTotalSellerByEmailTelephoneToValidate($email_id, $telephone){
      $sql = "SELECT count( DISTINCT seller_id ) as total
              FROM ". DB_PREFIX ."ms_seller
              WHERE email LIKE '" . $this->_db->escape($email_id) . "'
                OR mobile_no = '" . $this->_db->escape($telephone) . "'";
      $query = $this->_db->query($sql);

      if($query->num_rows > 0 ){
        return $query->row['total'];
      } else {
        return 0;
      }
    }

    /**
    * Method for existsing customer account
    * @return total of existsing customer account
    * @author vikas, 2017
    */
    public function getTotalCustomerByEmailTelephoneToValidate($email_id, $telephone){
      $sql = "SELECT customer_id
              FROM ". DB_PREFIX ."customer
              WHERE email LIKE '" . $this->_db->escape($email_id) . "'
                OR telephone = '" . $this->_db->escape($telephone) . "'";
      $query = $this->_db->query($sql);

      if($query->num_rows > 0 ){
        return array('customer_id' => $query->row['customer_id'], 'total'=>$query->num_rows);
      } else {
        return 0;
      }
    }


    // via gstin --  insert value of pan number in database without request and when we are remove tin block and others then pan and tin code optimize 
    // vikas, 2017

    public function UpdatePanNumberViaGstin($data){
      $this->_db->query("UPDATE " . DB_PREFIX . "ms_seller
              SET pan = '" . $this->_db->escape($data['pan_number']) ."'
              WHERE seller_id = '" . (int)$data['seller_id'] . "'");
    }

    /**
    * Get All cities fo sellers where they are located
    * @return distinct cities
    * @author Ashish, jan 2018 
    */
    public function getSellerCities() {
      $sql = "SELECT DISTINCT trim(city) AS city
              from ".DB_PREFIX."ms_seller 
              WHERE ( city != '' OR city != NULL ) 
              ORDER BY city ASC ";
      $result = $this->_db->query($sql);

      if ($result->num_rows) {
        return array_column($result->rows, 'city');
      } else {
        return false;
      }
    }

    /* *******
     * Function : update seller app info
     * Request Parameters : seller_id, app_info array
     * Type : Post
     * @author Devendra Dhayal
     *
     ******* */
    public function updateSellerDeviceInfo($seller_id, $app_info)
    {
        $sql = "UPDATE " . DB_PREFIX . "ms_seller SET 
                app_info = '" . $this->_db->escape($app_info['device_info']) . "', 
                app_version_code = " . (int)$app_info['app_version_code'] . " 
                WHERE seller_id = " . (int)$seller_id;

        $this->_db->query($sql);

        return true;
    }

    /**
    * Get Total exists Gst provisional ID of sellers
    * @return total existsing gst seller
    * @author vikas, FEB 2018 
    */
    public function getTotalSellersByGst($gst_no, $seller_id = 0){
      
      if(empty($gst_no)){
        return false;
      }

      $sql = "SELECT count( DISTINCT (seller_id))  as total
              FROM " . DB_PREFIX . "ms_seller
              WHERE gst_provisional_id LIKE '" .$this->_db->escape($gst_no) ."'";
      if(!empty($seller_id)){
        $sql .= " AND seller_id != " . (int)$seller_id;
      }        
      $query = $this->_db->query($sql);

      if($query->num_rows){
        return $query->row['total'];
      } else {
        return false;
      }
    }

        /* *******
     * Function : get seller id by product name prefix,
     * Product prefix is unique, so it will return either seller id or zero.
     * Request Parameters : product_name_prefix
     * Return : seller id Int
     * @author Devendra Dhayal, Nov 2019
     *
     ******* */
    public function getSellerIdByProductNamePrefix($product_name_prefix)
    {
        $sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_seller 
                WHERE product_name_prefix = '" . $this->_db->escape($product_name_prefix) . "'";

        $query = $this->_db->query($sql);
        if ($query->num_rows) {
            return $query->row['seller_id'];
        }

        return 0;
    }
}
