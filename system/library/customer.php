<?php
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $mobile_country_code;
	private $telephone;
	private $address_id;
	private $mobile_verified;
	private $email_verified;
	public 	$is_dropshipper;
    private $gst_number;
    private $referral_code;
    private $master_id;
    public  $gstObject;
    private $ws_access_token;
    private $customer_access_token;
    private $self_order;
    private $has_website;

	public function __construct($registry) {
		$this->gstObject = new GST($registry);
		
		$this->registry  = $registry;
        $reg_cid = 0; // customer_id from registry (if available)
		if (method_exists($registry, 'get')) {
			$this->config  = $registry->get('config');
			$this->db      = $registry->get('db');
			$this->request = $registry->get('request');
			$this->session = $registry->get('session');
            $reg_cid       = (int)($registry->get('customer_id') ?? 0);
		}else{
	        $this->config  = $registry->config;
	        $this->db      = $registry->db;
			$this->request = $registry->request;
			$this->session = $registry->session;
            $reg_cid       = (int)($registry->customer_id ?? 0);            
		}
        
        // Customer Id can be provided via various ways, session, cookie, GET request etc
        $customer_id = 0;
        
        if ( ($customer_id = (int)($this->session->data['customer_id'] ?? 0)) > 0 ) {
            
        } elseif ( ($customer_id = (int)($_COOKIE['customer_id'] ?? 0)) > 0 ) {
            setcookie('customer_id', $customer_id, time() + (86400 * 30), "/");
            
        } elseif ( $reg_cid > 0 ) {
            $customer_id = $reg_cid;
        }


		if ($customer_id > 0) {

            $customer_sql = "SELECT customer_id, 
                                    firstname, 
                                    lastname, 
                                    email, 
                                    telephone, 
                                    mobile_country_code, 
                                    address_id, 
                                    referral_code, 
                                    is_dropshipper, 
                                    ws_access_token, 
                                    customer_access_token, 
                                    mobile_verified, 
                                    email_verified, 
                                    gst_number,
                                    master_id, 
                                    has_website, 
                                    self_order
                             FROM " . DB_PREFIX . "customer 
                             WHERE customer_id = " . (int)$customer_id;
            $customer_query = $this->db->query($customer_sql);
            
            if ( $customer_query->num_rows ) {
                
                // Populating fields from query result as object members
                foreach ($customer_query->row as $field => $value) {
                    $this->$field = $value;
                }

			} else {
				$this->logout();
			}
		}

		if(!$this->isLogged()){
            //set  wishlist session id
            if(!isset($_COOKIE['wishlist_session_id']) ){
                setcookie('wishlist_session_id', session_id(), time() + (86400 * 15), "/" );
            }
            else{
                setcookie('wishlist_session_id', $_COOKIE['wishlist_session_id'], time() + (86400 * 15), "/" );
            }
        }
	}

	public function login($email, $password, $override = false, $checkout_login = false, $customer_access_token='') {


		if(is_numeric($email))
	       {
             $check_email_or_mobile = "telephone = '".$this->db->escape(trim($email))."'";
	       }
	       else
	       {
	       	$check_email_or_mobile = "email = '".$this->db->escape(trim($email))."'";
	       }

			$q = "SELECT 
			      customer_id,
			      mobile_country_code, 
			      telephone,
			      email,
			      firstname,
			      lastname,
			      address_id,
			      mobile_verified,
			      email_verified,
			      is_dropshipper,
			      gst_number,
			      ws_access_token,
			      master_id,
			      customer_access_token,
			      has_website,
			      self_order,
			      salt,
			      password,
			      password_mode 
			      FROM " . DB_PREFIX . "customer 
			      WHERE (".$check_email_or_mobile." 
			      OR customer_id = '".(int) $email."')";

		$customer_query = $this->db->query($q);

        //check password decrypt  
		$password_valid = false;
		if($customer_query->num_rows && !$override)
		{
		  if($customer_query->row['password_mode'] != 'new')
            {
              if($customer_query->row['password'] == SHA1($customer_query->row['salt'].SHA1($customer_query->row['salt'].SHA1($password)))
              	 || $customer_query->row['password'] == md5($password))
	           {
	              $password_valid = true;
	           }
            }
           else
           {
           	  if(password_verify($password, $customer_query->row['password']))
	          {
	         	$password_valid = true;
	          }
           } 	
		}
		else
		{
		   $password_valid = true;
		}

		if ($password_valid && $customer_query->num_rows) 
		{
            $this->session->data['ctoken'] = md5(mt_rand());
		    $this->session->data['customer_id'] = $customer_query->row['customer_id'];
		    setcookie('customer_id', $customer_query->row['customer_id'], time() + (86400 * 30), "/");
		    setcookie('register_user', 1, time() + (86400 * 30), "/");


           if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
            {
               setcookie('customer_mobile', $customer_query->row['email'], time() + (86400 * 30), "/");
            } 
           else
            {
               setcookie('customer_mobile', $customer_query->row['telephone'], time() + (86400 * 30), "/");
            } 

			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->mobile_country_code = $customer_query->row['mobile_country_code'];
			$this->address_id = $customer_query->row['address_id'];
			$this->mobile_verified = $customer_query->row['mobile_verified'];
			$this->email_verified = $customer_query->row['email_verified'];
			$this->is_dropshipper = $customer_query->row['is_dropshipper'];
            $this->gst_number = $customer_query->row['gst_number'];
            $this->ws_access_token     = $customer_query->row['ws_access_token'];
            $this->master_id     = $customer_query->row['master_id'];
            $this->customer_access_token = $customer_query->row['customer_access_token'];
            $this->has_website = $customer_query->row['has_website'];
			$this->self_order = $customer_query->row['self_order'];
            
           $ws_access_token_sql = ''; 
           if($customer_access_token != '')
           {
             $ws_access_token_sql = ", customer_access_token='".$this->db->escape($customer_access_token)."'";
             $this->customer_access_token   = $customer_access_token;
             setcookie('customer_id', $this->customer_id, time() + (86400 * 30), "/");
			 setcookie('customer_access_token', $customer_access_token, time() + (86400 * 30), "/");
           }
            
            // update new decrypt password mode
            $password_mode_sql = "";
            if($customer_query->row['password_mode'] != 'new')
            {
              $password_secret = password_hash($password, PASSWORD_DEFAULT);	
              $password_mode_sql = ", password_mode = 'new', password = '".$password_secret."'";
            }

			$this->db->query("UPDATE " . DB_PREFIX . "customer 
                              SET ip = '" . $this->db->escape($this->request->getIpAddress) . "' ". 
                              $ws_access_token_sql." ".
                              $password_mode_sql." 
                              WHERE customer_id = '" . (int)$this->customer_id . "'");


            if (isset($_REQUEST['wishlist_session_id'])) {
                $this->mergeWishlist($_REQUEST['wishlist_session_id']);
            }
            else if (isset($_COOKIE['wishlist_session_id'])) {
                $this->mergeWishlist($_COOKIE['wishlist_session_id']);
                setcookie("wishlist_session_id", "", time() - 3600);
            }


            if(isset($_REQUEST['cart_session_id'])){
                $this->updateUserId($_REQUEST['cart_session_id']);
            }
            else if(isset($_COOKIE['cart_session_id'])){
                $this->updateUserId($_COOKIE['cart_session_id']);
                setcookie("cart_session_id", "", time() - 3600);
            }

			return $this->customer_id;
		} else {
			return false;
		}
	}

    public function loginUsingIdOtp($customerId, $otp, $customer_access_token='') {

        $customer_query = $this->db->query("SELECT 
        	customer_id, 
        	firstname,
        	lastname,
        	email,
        	mobile_country_code,
        	telephone,
        	address_id,
        	mobile_verified,
        	email_verified,
        	is_dropshipper,
        	gst_number,
        	master_id,
        	has_website,
        	self_order,
        	otp
        	FROM " . DB_PREFIX . "customer 
        	WHERE customer_id = '".$this->db->escape($customerId)."'");



        if ($customer_query->num_rows && $customer_query->row['otp'] == $otp) {

            $this->session->data['ctoken'] = md5(mt_rand());
            $this->session->data['customer_id'] = $customer_query->row['customer_id'];
            setcookie('customer_id', $customer_query->row['customer_id'], time() + (86400 * 30), "/");
            setcookie('register_user', 1, time() + (86400 * 30), "/");

            if(!empty($customer_query->row['telephone']))
            {
                setcookie('customer_mobile', $customer_query->row['telephone'], time() + (86400 * 30), "/");
            }
            else
            {
                setcookie('customer_mobile', $customer_query->row['email'], time() + (86400 * 30), "/");
            }

            $this->customer_id = $customer_query->row['customer_id'];
            $this->firstname = $customer_query->row['firstname'];
            $this->lastname = $customer_query->row['lastname'];
            $this->email = $customer_query->row['email'];
            $this->telephone = $customer_query->row['telephone'];
            $this->mobile_country_code = $customer_query->row['mobile_country_code'];
            $this->address_id = $customer_query->row['address_id'];
            $this->mobile_verified = $customer_query->row['mobile_verified'];
            $this->email_verified = $customer_query->row['email_verified'];
            $this->is_dropshipper = $customer_query->row['is_dropshipper'];
            $this->gst_number = $customer_query->row['gst_number'];
            $this->master_id = $customer_query->row['master_id'];
            $this->has_website = $customer_query->row['has_website'];
			$this->self_order = $customer_query->row['self_order'];


            $ws_access_token_sql = ''; 
           if($customer_access_token != '')
           {
             $ws_access_token_sql = ", customer_access_token = '".$this->db->escape($customer_access_token)."' ";
             $this->customer_access_token = $customer_access_token;
             setcookie('customer_access_token', $customer_access_token, time() + (86400 * 30), "/");
           }

            $this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->getIpAddress) . "' ".$ws_access_token_sql." WHERE customer_id = '" . (int)$this->customer_id . "'");

            if (!empty($_REQUEST['wishlist_session_id'])) {
                $this->mergeWishlist($_REQUEST['wishlist_session_id']);
            }
            elseif (isset($_COOKIE['wishlist_session_id'])) {
                $this->mergeWishlist($_COOKIE['wishlist_session_id']);
                setcookie("wishlist_session_id", "", time() - 3600);
            }

            if(!empty($_REQUEST['cart_session_id'])){
                $this->updateUserId($_REQUEST['cart_session_id']);
            }
            elseif(isset($_COOKIE['cart_session_id'])){
                $this->updateUserId();
                setcookie("cart_session_id", "", time() - 3600);
            }

            return true;
        } else {
            return false;
        }
    }

	public function logout() {
		unset($this->session->data['customer_id']);
        unset($this->session->data['multiseller']);
        unset($this->session->data['ctoken']);
		unset($this->session->data['wishlist']);
		unset($this->session->data['payment_address']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['shipping_address']);
		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['comment']);
		unset($this->session->data['order_id']);
		unset($this->session->data['coupon']);
		unset($this->session->data['reward']);
		unset($this->session->data['voucher']);
		unset($this->session->data['vouchers']);

		setcookie('customer_id', '', time() - 3600, "/");
		setcookie('customer_mobile', '', time() - 3600, "/");
		setcookie('wat', '', time() - 3600, "/");
		setcookie('register_user', '', time() - 3600, "/");
		setcookie('country_iso_code', '', time() - 3600, "/");
		setcookie('country_code', '', time() - 3600, "/");
		setcookie('ws_access_token', '', time() - 3600, "/");
		setcookie('cart_session_id', '', time() - 3600, "/");
		setcookie('wishlist_session_id', '', time() - 3600, "/");
		setcookie('customer_access_token', '', time() - 3600, "/");

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->mobile_country_code = '';
		$this->address_id = '';
		$this->mobile_verified = '';
		$this->email_verified = '';
		$this->is_dropshipper = '';
        $this->gst_number = '';
        $this->customer_access_token = '';
        $this->has_website = '';
        $this->self_order = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}
	
	public function getMasterId() {
		return $this->master_id;
	}
	
	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}
    
    public function getCountryCode() {
		return $this->mobile_country_code;
	}

	public function getMobileVerified() {
		return $this->mobile_verified;
	}

	public function getEmailVerified() {
		return $this->email_verified;
	}

	public function getIsDropshipper() {
		return $this->is_dropshipper;
	}

    public function getGSTNumber() {
        return trim($this->gst_number);
    }

    public function getSelfOrder() {
        return trim($this->self_order);
    }

    public function getHasWebsite() {
        return trim($this->has_website);
    }    

	/*** 
	* @update: added $log_changes parameter to identify if logging is to be done
	* 		   logging will be done if $log_changes = true (for now we want to log changes via admin panel)
	***/
    public function setGSTNumber($gst_number, $log_changes = false) {
		$this->gst_number = $this->gst_number ?? "";
		$this->gstObject->updateGstNumber((int) $this->customer_id, (int) $this->master_id, $this->gst_number, $gst_number, $log_changes);
		$this->gst_number = $gst_number;
    }

	public function getAccessToken() {
		return $this->ws_access_token;
	}

	public function getCustomerAccessToken() {
		return $this->customer_access_token;
	}

	public function getAddressId() {
		return $this->address_id;
	}

	public function getreferral_code() {
		return $this->referral_code;
	}

	public function getBalance() {
		if((int)$this->customer_id > 0) {
			$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");

			return $query->row['total'];
		}else{
			return 0;
		}
	}

    public function getCashbackAvailable() {
        $sql = "SELECT customer_cashback_id, (amount - amount_utilized) AS total
                                   FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$this->customer_id . "'
                                     AND expired = 0
                                     AND amount > 0
                                     AND (amount - amount_utilized) > 0";
		$query = $this->db->query($sql);
         if($query->num_rows > 0){
 			$cashback_total = 0;
 			foreach( $query->rows as $cashback ){
 				$cashback_total += $cashback['total'];
 			}
 			return array('cashback_breakup' => $query->rows, 'total_cashback' => $cashback_total);
 		}
	}

	public function getRewardPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}
	public function getStoreConfigForSeller($store_id, $key){
		$sql = "SELECT value FROM ". DB_PREFIX . "setting WHERE store_id = '".(int)$store_id."' AND `key` = '".$key."'";
		$query = $this->db->query($sql);
		if($query->num_rows){
			$sellervalue = $query->row['value'];
			return $sellervalue;
		}
	}
	public function insertIntoCutomerWishlist($customer_id, $share_message, $product_id, $store_id)
	{
	    $sql = "INSERT INTO ".DB_PREFIX."customer_wishlist SET
			customer_id="."'". (int)$customer_id."'" ." ,
			share_message ="."'".$this->db->escape($share_message) ."'  ,
			product_id=".(int)$product_id." ,
			date_added = NOW(),
			wishlist_session_id=NULL,
			store_id =".(int)$store_id;
		$this->db->query($sql);
	}
    public function insertIntoSessionWishlist($wishlist_session_id, $share_message, $product_id, $store_id)
    {
        $sql = "INSERT INTO ".DB_PREFIX."customer_wishlist SET
			wishlist_session_id="."'". $wishlist_session_id."'" ." ,
			share_message ="."'".$this->db->escape($share_message) ."'  ,
			product_id=".(int)$product_id." ,
			date_added = NOW(),
			customer_id=0,
			store_id =".(int)$store_id;
        $this->db->query($sql);
    }

	public function checkExistingWishlistItem( $product_id)
	{
        if($this->isLogged()) 
        {
	        $sql = "SELECT * FROM " . DB_PREFIX . "customer_wishlist
                    WHERE customer_id=" . (int)$this->customer_id . " 
                      AND product_id=" . (int)$product_id;
            return $this->db->query($sql)->num_rows;
        }
        else if(isset($_REQUEST['wishlist_session_id'])) 
        {
             $sql = "SELECT * FROM " . DB_PREFIX . "customer_wishlist
                    WHERE wishlist_session_id='" . $_REQUEST['wishlist_session_id'] . "'
                      AND product_id=" . (int)$product_id;
            return $this->db->query($sql)->num_rows;
        }
        else if(isset($_COOKIE['wishlist_session_id'])) 
        {
            $sql = "SELECT * FROM " . DB_PREFIX . "customer_wishlist
                    WHERE wishlist_session_id='" . $_COOKIE['wishlist_session_id'] . "' AND product_id=" . (int)$product_id;
            return $this->db->query($sql)->num_rows;
         }

	}

	public function getWishlistItems()
	{
        if($this->isLogged()) 
        {
            $sql = "SELECT DISTINCT(product_id) 
                    FROM ".DB_PREFIX."customer_wishlist 
                    WHERE customer_id=". (int)$this->customer_id;
            return $this->db->query($sql, true);
        }
        else if(isset($_REQUEST['wishlist_session_id'])) 
        {
             $sql = "SELECT DISTINCT(product_id) 
             FROM ".DB_PREFIX."customer_wishlist 
             WHERE wishlist_session_id='". $_REQUEST['wishlist_session_id']."'";

            return $this->db->query($sql)->num_rows;
        }
        else if(isset($_COOKIE['wishlist_session_id']))
        {
             $sql = "SELECT DISTINCT(product_id) 
             FROM ".DB_PREFIX."customer_wishlist 
             WHERE wishlist_session_id='". $_COOKIE['wishlist_session_id']."'";
             return $this->db->query($sql, true);
         }

	}
	public function processWishlist($product_id)
	{
	    $sql = "SELECT pd.name, p.selling_price
            FROM ".DB_PREFIX."product p
            INNER JOIN oc_product_description pd
            ON (p.product_id = pd.product_id)
            WHERE p.product_id=" .(int)$product_id;
        $prod_q = $this->db->query($sql)->row;
        if(count(array_filter($prod_q)) > 0)
        {
            $update_price_by = $this->getCustomerSettingByKey('update_price');
            if (empty($update_price_by)) {
                $update_price_by = "50";
            } else {
                $update_price_by = $update_price_by['value'];
            }
            $update_price_by = 1 + (float)$update_price_by/100.0;

            $shr_msg = "Buy ".$prod_q['name']. " @ ".ceil($update_price_by* $prod_q['selling_price'])." Contact - ".$this->telephone ;


            if($this->isLogged())
            {
                $this->insertIntoCutomerWishlist($this->customer_id, $shr_msg,$product_id, $this->config->get('config_store_id') );
            }
            else if(isset($_REQUEST['wishlist_session_id']))
             {
             	$this->insertIntoSessionWishlist($_REQUEST['wishlist_session_id'], $shr_msg,$product_id, $this->config->get('config_store_id') );
             }
            else if(isset($_COOKIE['wishlist_session_id']))
            {
                $this->insertIntoSessionWishlist($_COOKIE['wishlist_session_id'], $shr_msg,$product_id, $this->config->get('config_store_id') );
            }

        }
	}
	public function mergeWishlist($wishlist_session_id){
	    $sql = "UPDATE ".DB_PREFIX."customer_wishlist SET customer_id = ".(int)$this->customer_id.", wishlist_session_id = NULL WHERE wishlist_session_id = '".$wishlist_session_id."'";
	    $this->db->query($sql);
    }


    public function deleteProductFromWishlist($product_id){
        $q = "DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer_id . "' AND product_id = " . (int)$product_id;
        $this->db->query($q);
    }

    public function clearAllProductFromWishlist(){
        $q = "DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = " . (int)$this->customer_id ;
        $this->db->query($q);
    }

    public function getTotalWishlists() {
        $total = 0;
        if($this->isLogged()) {
            $sql = "SELECT COUNT(DISTINCT(product_id)) AS total FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id=" . (int)$this->customer_id;
            $query = $this->db->query($sql);
            $total = (int)$query->row['total'];
        }
        else if(isset($_REQUEST['wishlist_session_id'])  && !empty($_REQUEST['wishlist_session_id']))
         {
            $sql = "SELECT COUNT(DISTINCT(product_id)) AS total FROM " . DB_PREFIX . "customer_wishlist WHERE wishlist_session_id='" . $_REQUEST['wishlist_session_id'] . "'";
            $query = $this->db->query($sql);
            $total = $query->row['total'];
        }
        else if(isset($_COOKIE['wishlist_session_id'])) {
            $sql = "SELECT COUNT(DISTINCT(product_id)) AS total FROM " . DB_PREFIX . "customer_wishlist WHERE wishlist_session_id='" . $_COOKIE['wishlist_session_id'] . "'";
            $query = $this->db->query($sql);
            $total = $query->row['total'];
        } 

        return $total;
    }
    public function getWishlistIcon($product_id){
        $fill_heart = 'fa fa-heart-o';
        if($this->checkExistingWishlistItem($product_id)){
            $fill_heart = 'fa fa-heart custom_heart';
        }
        return $fill_heart;
    }

    public function updateUserId($cart_session_id=false)
    {

    	if(empty($cart_session_id) && !empty($_COOKIE['cart_session_id'])) 
    	{ $cart_session_id = $_COOKIE['cart_session_id'];  }

        $cond = 'cart_session_id = "'. $this->db->escape($cart_session_id) .'" order by date_modified desc limit 1';
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE ".$cond);
        $cartData = $query->row;


        if(isset($cartData['cart_data']) && !empty($cartData['cart_data'])){
            $pCartData  = unserialize($cartData['cart_data']);
            $conditions = 'customer_id = '. (int)$this->customer_id .' order by date_modified desc limit 1';
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE ".$conditions);
            $cart_data = $query->row;
            if(isset($cart_data['cart_data']) && !empty($cart_data['cart_data'])){
                $cCartData = unserialize($cart_data['cart_data']);
                foreach($cCartData as $key => $qty){
                    if ((int)$qty && ((int)$qty > 0)) {
                        if (!isset($pCartData[$key])) {
                            $pCartData[$key] = (int)$qty;
                        } else {
                            $pCartData[$key] += (int)$qty;
                        }
                    }
                }
                $parent_cart_id = $cartData['id'];
                $sql = "UPDATE ".DB_PREFIX."customer_cart 
				SET customer_id=null,
				parent_cart_id=".(int)$parent_cart_id."
				WHERE customer_id = ".(int)$this->customer_id;
                $this->db->query($sql);
            }
        }

        if(isset($pCartData) && !empty($pCartData)){
            $sql =  "UPDATE ".DB_PREFIX."customer_cart 
				SET `customer_id`=".(int)$this->customer_id.",
				`cart_data`= '".serialize($pCartData)."',
				`cart_session_id`= '0'
				WHERE `cart_session_id` = '".$this->db->escape($cart_session_id)."'";

            $this->db->query($sql);
        }

    }

    /**
     * Method for get credit status value of customer 
     *@return if 1 i.e. credit payment method show in payment mode , 
     * otherwise don't show in payment mode
     */
    public function isCustomerCreditStatus(string $type){
        $sql = "SELECT customer_id, credit_status 
                FROM " . DB_PREFIX . "customer_credit
                WHERE customer_id = '" . (int)$this->customer_id  ."' and type = '".$this->db->escape($type)."'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->row['credit_status'];
        } else {
            return false;
        }
    }

    /**
     * Method for get lazypay status value of customer 
     *@return if 1 i.e. credit payment method show in payment mode , 
     * otherwise don't show in payment mode
     */
    /*public function isCustomerLazypayStatus(){
        $sql = "SELECT customer_id, credit_status 
                FROM " . DB_PREFIX . "customer_credit
                WHERE customer_id = '" . (int)$this->customer_id  ."' and type = 'Lazypay'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->row['credit_status'];
        } else {
            return false;
        }
    }*/

    /**
     * Method for get customer's neogrowth registration number
     *@return NeoGrowth registration number
     */
    public function getCustomerNeoGrowthRegistrationNumber(){
        $sql = "SELECT neogrowth_registration_number 
                FROM " . DB_PREFIX . "customer_credit
                WHERE customer_id = '" . (int)$this->customer_id  ."' and type = 'Neogrowth'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->row['neogrowth_registration_number'];
        } else {
            return '';
        }
    }

    /**
     * Method for get customer's lazypay registration number
     *@return Lazypay registration number
     */
    public function getCustomerLazypayRegistrationNumber(){
        $sql = "SELECT lazypay_mobile 
                FROM " . DB_PREFIX . "customer_credit
                WHERE customer_id = '" . (int)$this->customer_id  ."' and type = 'Lazypay'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->row['lazypay_mobile'];
        } else {
            return '';
        }
    }

    public function getCustomerSettingByKey($key){
        //echo substr(substr( "abcdefghijklmnopqrstuvwxyz" ,mt_rand( 0 ,25 ) ,1 ) .substr( md5(16) ,1 ),0,16); exit;
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_settings WHERE key_field = '" . $key . "' AND customer_id = '" . $this->customer_id . "'");
        return $query->row;
    }

    public function checkCustomerSettingByKey($key){
        //echo substr(substr( "abcdefghijklmnopqrstuvwxyz" ,mt_rand( 0 ,25 ) ,1 ) .substr( md5(16) ,1 ),0,16); exit;
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_settings WHERE key_field = '" . $key . "' AND customer_id = '" . $this->customer_id . "'");
        return $query->num_rows;
    }
    public function UpdateCustomerSetting($key,$value){
        $date = Date("Y-m-d H:i:s");
        if(!empty($this->checkCustomerSettingByKey($key))){
            if($this->db->query("UPDATE " . DB_PREFIX . "customer_settings SET value = '" . $value . "' WHERE customer_id = '" . (int)$this->customer_id . "' AND key_field = '" . $key."'")){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($this->db->query("INSERT INTO " . DB_PREFIX . "customer_settings SET customer_id = '" . (int)$this->customer_id . "',key_field = '" . $key . "', value = '" . $value . "' , date_added = '".$date."'")){
                return 1;
            }else{
                return 0;
            }
        }
    }

    public function isFranchise($customer_id = ''){

        $cid = $this->getId();
        // check for provided customer id
        if(!empty($customer_id)){
            $cid = $customer_id;
        }

        $sql = "SELECT franchise_status FROM " . DB_PREFIX . "franchise_data WHERE franchise_id = '". (int)$cid . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return (int)$query->row['franchise_status'];
        }
        
        return 0;
    }
		
	/**
     * Method to get wholesalebox Credit Payment method Data of the customer
     *@return 1 or 0
    */
    public function getWsbCreditPaymentData(int $customer_id = 0): array {
		if ($customer_id == 0) {
			$customer_id = $this->customer_id;
		}
        $sql = "SELECT status, credit_limit 
                FROM " . DB_PREFIX . "customer_wsb_credit 
                WHERE customer_id = '" . (int)$customer_id  ."'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->row;
        } else {
            return array();
        }
    }

    /**
	 * Public method to get customer details by customerId
	 * @param: $customer_id
	 * @return: $data Array
	 * @author: Nishu, Jan 2018
    */
    public function getCustomerById($customer_id = 0) {
    	$data = array();
    	if(empty($customer_id)){
    		return $data;
    	}
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "' ");
		if($query->num_rows > 0){
			$data = $query->row;
		}		
		return $data;
	}//End of getCustomerById() method

    /*
    * Method to get gcm ids of customers
    */
    public function getGCMIds($customer_ids) {
        $sql = "SELECT ws_gcm_registration_id FROM " . DB_PREFIX . "customer WHERE customer_id in (". $customer_ids .") AND ws_gcm_registration_id != ''";
        $query = $this->db->query($sql);
        if( $query->num_rows ){
            return $query->rows;
        } else {
            return array();
        }
    }

    /*
    * Method to update the gcm id of customer
    */
    public function updateGCMId($customer_id, $gcm_id) {
        $sql = "UPDATE " . DB_PREFIX . "customer SET ws_gcm_registration_id = '".$this->db->escape($gcm_id)."' WHERE customer_id = ". (int)$customer_id ;
        if($this->db->query($sql)) return true;
        return false;
    }

    /**
	   * Public Method to get master_id(s) for given customer_ids
	   * @param: $customer_ids String
	   * @return $data Array
	   * @author: Nishu, Sept 2018
	*/
    public static function getMasterIdsByCustomerIds(DataBase\DB $db, string $customer_ids) : array{
      $data = array();
      if(!empty($customer_ids)){
        $sql = "
            SELECT 
              DISTINCT master_id
            FROM 
              ". DB_PREFIX ."customer
            WHERE
              customer_id IN (". $customer_ids .")
            ";
        $result = $db->query($sql);
        if($result->num_rows > 0){
        	$data = $result->rows;
        }
      }
      return $data;
    }
		
		/**
	   * Public Method to Total Purchase value and return value by given master_id and for given days
	   * @param: DataBase\DB $db, int $master_id, int $days
	   * @return float $total_purchase
	   * @author: Devendra, October 2018
	*/
    public static function getTotalPurchaseAndReturnByGivenMasterId(DataBase\DB $db, int $master_id, int $days) : array {
      $response = array(
				'total_purchase' => 0.00,
				'total_return' => 0.00
			);

      if(!empty($master_id) && !empty($days) ){
        $sql = "
	            SELECT 
	              SUM(IFNULL(o.total, 0)) AS total_purchase,
								SUM(IFNULL(cn.net_refundable, 0)) AS total_return
	            FROM
	              ". DB_PREFIX ."order AS o
	            INNER JOIN
	              ". DB_PREFIX ."customer AS c ON o.customer_id = c.customer_id
	            LEFT JOIN
	              ". DB_PREFIX ."credit_note AS cn ON cn.order_id = o.order_id AND cn.credit_note_status = 1
	            WHERE
	              c.master_id = ". (int)$master_id ."
	              AND DATEDIFF( NOW(), o.date_added ) <= ". (int)$days ."
	          ";

		    $result = $db->query($sql);
		    
		    if($result->num_rows > 0){
		      $response['total_purchase'] = (float)$result->row['total_purchase'] ?? 0.00;
					 $response['total_return'] = (float)$result->row['total_return'] ?? 0.00;
		    }
      }

      return $response;
    }

    /**
     * Public method to get customer info by given field list
     * @param: array $customer_ids, String Field list comma seperated
     * @return: Array
     * @author: Nishu, Nov 2018
    */
    public function getCutomerInfo($customer_ids, $field_list='') {
    	$data = array();
    	if(!empty($customer_ids)){

	    	$sql = "SELECT customer_id ";
	    	if(!empty($field_list)){
	    		$sql .= ", ". $field_list;
	    	}
	    	$sql .= " FROM ".DB_PREFIX."customer ";
	    	$sql .= "WHERE customer_id IN (". implode(',', $customer_ids) .")"; 

	    	$result = $this->db->query($sql);
	    	if($result->num_rows > 0){
	    		$data = array_combine(
	    					array_column($result->rows, 'customer_id'), 
	    					$result->rows
	    				);
	    	}
    	}
    	return $data;
    }

    /**
     * Public method to get customer(s) having fashcart website for given customer_ids
     * @param: array $customer_ids
     * @return: Array
     * @author: Nishu, Nov 2018
    */
    public function getCutomersHavingWebsite($customer_ids) {
    	$data = array();
    	if(!empty($customer_ids)){

	    	$sql = "SELECT 
	    				DISTINCT customer_id
	    			FROM 
	    				".DB_PREFIX."customer 
	    	        WHERE 
	    	        	customer_id IN (". implode(',', $customer_ids) .")
	    	        	AND has_website = 1"; 

	    	$result = $this->db->query($sql);
	    	if($result->num_rows > 0){
	    		$data = $result->rows;
	    	}
    	}
    	return $data;
    }

    /**
     * Public method to get customer's website URL for given customer_ids
     * @param: array $customer_ids
     * @return: Array
     * @author: Nishu, Nov 2018
    */
    public function getCutomerWebsiteUrl($customer_ids) {
    	$data = array();
    	if(!empty($customer_ids)){

			$ch  = curl_init();
	        $url = HTTPS_CATALOG;
	        if(SITE_ENVIRONMENT == 'staging'){
	        	//$url .= 'staging/';
	        }
	        $url .= 'index.php?route=restapi/customerwebsite/getCustomerWebsiteUrl';

	        $post_data = array(
	        			  "customer_ids" => $customer_ids,
	        			  "type"         => "CRM"
	        	         );
	        $post_data = json_encode($post_data);
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	       
	       $result = curl_exec($ch);
	       curl_close($ch);
	       $result = json_decode($result);
	       
	       if(!empty($result) && $result->status == 1) {
	          $data = $result->full_url;		
	       }
        }
       return $data;
    }

    /**
	 * Public method to update Customer Info dynamically passed fields of oc_customer
	 * @author: Nishu, Feb 2019
    */
    Public function updateCustomerInfo(array $data){
    	if(!empty($data['customer_id'])){
    		$update_qry = "
 							UPDATE
 								".DB_PREFIX."customer
 							SET
 								customer_id = ".(int)$data['customer_id']."
    		              ";
    		foreach ($data as $key => $value) {
    			$update_qry .= " ,".$key ."= '".$this->db->escape($value)."'";
    		}
    		$update_qry .= " WHERE customer_id = ". (int)$data['customer_id'];

    		$this->db->query($update_qry);
    	}
    	return true;
    }

    /**
	 * Public method to get TL date against given customer ids
	 * @param: array 
	 * @return array
	 * @author: Nishu, Feb 2019
    */
    public static function getTlDataForCustomers($alldata){
    	$results = array(); 

	    $url = 'https://www.wholesalebox.biz/cron/getAgentsDataOfCustomers';

	    $input = array();
	    $input['request_params']                      = array();
	    $input['request_params']['request_call_time'] = date('Y-m-d H:i:s');
	    $input['request_params']['request_url']       = $url;
	    $input['request_params']['customer_ids']      = $alldata['customer_ids'] ?? array();

	    $input['request_params']['credentials']                 = array();
	    $input['request_params']['credentials']['customer_id']  = $alldata['customer']['customer_id'];
	    $input['request_params']['credentials']['access_token'] = $alldata['customer']['ws_access_token'] ?? '';
	    
	    $data_string = json_encode($input);

	    // Set some options - we are passing in a useragent too here
	    // Get cURL resources
	    $ch = curl_init($url);    
	    // Set some options - we are passing in a useragent too here
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                    
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	                                                                          
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                         
	    'Content-Type: application/json',                                                                         
	    'Content-Length: ' . strlen($data_string))                                                                      
	    );
	    // Send the request & save response to $resp
	    $resp = curl_exec($ch);
	    // Close request to clear up some resources
	    curl_close($ch);
	    $agent_data   = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $resp);
	    $res =json_decode($agent_data);

	    foreach ($alldata['customer_ids'] as $customer_id) {
	    	$results[$customer_id] = array();
	    }

	    if(!empty($res->result)){
		    foreach ($res->result as $result) {
		    	$customer_id = $result->customer_id ?? 0;
		    	$results[$customer_id]['agent_data'] = $result->Agent_data ?? array();
		    	
		        if(strtolower($result->Role) == 'field_sales'){
		          $team_heads = (!empty($result->Sale_Support_data) ? $result->Sale_Support_data : $result->TL_data) ;
		        }else{
		          $team_heads = $result->TL_data;
		        }

		        if(!empty($team_heads)){
		          foreach ($team_heads as $tl) {
		          	if(!empty($tl->name)){
		          		$results[$customer_id]['tl_data'][] = $tl;
		          	}
		          }
		        }else{
		          $results[$customer_id]['tl_data'] = array();
		        }       
		    }
	    }

	    return $results;
    }

    /**
     * @info: Public method to get payment details for given order_ids
     * @param: $order_ids: comma seperated
     * @return: Array
     * @author: Nishu, jan 2019
    */
    public static function getPaymentDetailsBySubOrderId($db, $suborder_id){
    	$data = array();
    	
        $sql = "
        		SELECT
					op.txn_status,
					op.payment_mode,
					av.value AS amount,
					op.payment_gateway
				FROM 
					oc_advance_voucher AS av
				INNER JOIN
					" . DB_PREFIX . "order_payment AS op ON av.payment_id = op.payment_id
                WHERE 
                	av.suborder_id = '" . $db->escape($suborder_id) . "'
                	AND av.locked = 1
                	AND av.status = 1
                	AND op.amount > 0
                	AND op.successfull = 1 
                ";

        $result = $db->query($sql)->rows;

        $paid_by_customer       = 0;
        $cashback_coupon_amount = 0;

        foreach ($result as $value) {
            if (
            	$value['amount'] > 0 
                && $value['txn_status']      != 'cheque_deposited' 
                && $value['payment_gateway'] != 'cashback'
                && $value['payment_gateway'] != 'coupon'
                && $value['payment_gateway'] != 'wsb_credit'
            ){
                $paid_by_customer += $value['amount'];
            }

            if($value['payment_gateway'] == 'cashback' || $value['payment_gateway'] == 'coupon') {
                $cashback_coupon_amount += $value['amount'];
            }
        }
        $data['paid_by_customer']       = $paid_by_customer;
        $data['cashback_coupon_amount'] = $cashback_coupon_amount;
        
        return $data;
    } 

    /**
     * Public method to get TL name and email against given customer ids
     * @param: array
     * @return array
     * @author: Manoj Singh, March 2019
     */
    public static function getLeadTlInfoForCustomers($alldata){
        $results = array();

        $url = CRM_URL.'cron/getAgentofCustomer';

        $input = array();

        $input['customer_id'] = $alldata['customer']['customer_id'] ?? '';
        $input['token'] = $alldata['customer']['ws_access_token'] ?? '';

        $data_string = json_encode($input);

        // Set some options - we are passing in a useragent too here
        // Get cURL resources
        $ch = curl_init($url);
        // Set some options - we are passing in a useragent too here
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        // Send the request & save response to $resp
        $resp = curl_exec($ch);

        // Close request to clear up some resources
        curl_close($ch);
        $agent_data = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $resp);
        $res =json_decode($agent_data);

        $team_heads = array();
        if(isset($res->agent_role)){

            if(strtolower($res->agent_role) == 'field_sales'){
                //check agent zone to select address to send mail
                if(strtolower($res->agent_zone) == 'south'){
                    $team_heads =  array('name'=>EMAIL_IDS['south_sales']['name'], 'email'=>EMAIL_IDS['south_sales']['email_id'], 'agent_name'=>$res->agent_name ?? '','lead_id'=>$res->lead_id ?? '');
                }
                else if(strtolower($res->agent_zone) == 'north')
                {
                    $team_heads =  array('name'=>EMAIL_IDS['north_sales']['name'], 'email'=>EMAIL_IDS['north_sales']['email_id'], 'agent_name'=>$res->agent_name ?? '','agent_id'=>$res->agent_id ?? '','lead_id'=>$res->lead_id ?? '');
                }
                else if(strtolower($res->agent_zone) == 'east')
                {
                    $team_heads =  array('name'=>EMAIL_IDS['east_sales']['name'], 'email'=>EMAIL_IDS['east_sales']['email_id'], 'agent_name'=>$res->agent_name ?? '','agent_id'=>$res->agent_id ?? '','lead_id'=>$res->lead_id ?? '');
                }
                else if(strtolower($res->agent_zone) == 'west')
                {
                    $team_heads =  array('name'=>EMAIL_IDS['west_sales_support']['name'], 'email'=>EMAIL_IDS['west_sales_support']['email_id'], 'agent_name'=>$res->agent_name ?? '','agent_id'=>$res->agent_id ?? '','lead_id'=>$res->lead_id ?? '');
                }
                else
                {
                    $team_heads =  array('name'=>'', 'email'=>'', 'agent_name'=>'','agent_id'=>'','lead_id'=>'');
                }

            }else{
                $team_heads =  array('name'=>$res->team_lead_name ?? '', 'email'=>$res->team_lead_email ?? '', 'agent_name'=>$res->agent_name ?? '','agent_id'=>$res->agent_id ?? '','lead_id'=>$res->lead_id ?? '');
            }
        }


        return $team_heads;
    }

    /**
     * @info: Public method to get WSB Credit Application approved limit and followup date  
     * @param: int $customer_id
     * @author: Nishu, June 2019
    */
    public function wsbCreditApplicationApprovedLimitData(int $customer_id) : array{
    	$data = array();
    	if(!empty($customer_id)){
    		$sql = "
    				SELECT 
    					ca.crif_score,
    					DATE(ca.crif_score_date) AS crif_score_date,
					    DATE(casr.date_added) AS credit_approved_date, 
					    casr.credit_limit,
					    casr.bank_account_last_digit
					FROM
					    ".DB_PREFIX."credit_application AS ca
					        LEFT JOIN
					    ".DB_PREFIX."credit_application_status_remarks AS casr ON ca.id = casr.credit_application_id
					    AND casr.status = 'approved_but_agreement_pending'
					WHERE
					    ca.customer_id = ". (int)$customer_id ."
					ORDER BY 
						casr.id ASC
					LIMIT 
						0, 1
    		       ";

    		$qry = $this->db->query($sql);
    		if($qry->num_rows > 0){
    			$data['credit_approved_date'] = $qry->row['credit_approved_date'];
    			$data['crif_score']           = $qry->row['crif_score'];
    			$data['crif_score_date']      = $qry->row['crif_score_date'];
    			$data['credit_limit']         = $qry->row['credit_limit'];
    			$data['credit_ac_number']     = $qry->row['bank_account_last_digit'];
    		}
    	}
    	return $data;
    }

    /**
     * @info: Public method to get customer profile info, for given customer_id
     * @param : int $customer_id
     * @return: array $data- Prifle info
     * @author: Nishu, 21st June 2019
    */
    public function getCustomerProfileInfo( int $customer_id ){
    	$data = array();
    	if(!empty($customer_id)){

    		//Get data WSB credit related details
    		$this->setWsbCreditCustomerProfile($customer_id, $data);
    		
    		//Get CRIF Score from credit application
    		$credit_application_data = $this->wsbCreditApplicationApprovedLimitData($customer_id);
    		$data['crif_score']              = $credit_application_data['crif_score'] ?? '';
    		$data['credit_activation_limit'] = $credit_application_data['credit_limit'] ?? '';

    		//Get order history related info for customer_id, Like: order_total, First order date etc
    		$order_data = OrderInfo::getOrderDetailsForCustomerProfile($this->db, $customer_id);

    		$wsb_credit_vintage = $order_data['credit_vintage'] ?? 'N/A';
   			$data['wsb_credit_vintage'] = $wsb_credit_vintage;

    		$data = array_merge($data, $order_data);

    		//Avg Amt of credit Order -- Credit Business/No of Credit Orders
    		if(!empty($data['total_credit_orders']) && !empty($data['count_credit_orders'])){
    			$avg_wsb_credit_order_amount         = ($order_data['count_credit_orders'] > 0) ? $order_data['total_credit_orders'] / $order_data['count_credit_orders'] : ''; 
    			$data['avg_wsb_credit_order_amount'] = $avg_wsb_credit_order_amount;
    		}else{
    			$data['avg_wsb_credit_order_amount'] = '';
    		}

    		//NACH Payment attempts
    		$nach_data = OrderPayment::getNachAttemptsData($this->db, $customer_id);
    		$data['nach_attempts']              = $nach_data['nach_attempts'] ?? 0;
    		$data['successfull_nach_attempts']  = $nach_data['successfull_nach_attempts'] ?? 0;
            $data['last_successfull_nach_date'] = $nach_data['last_successfull_nach_date'] ?? '';
            $data['last_failed_nach_date']      = $nach_data['last_failed_nach_date'] ?? '';

    		$data['nach_attempts_ratio'] = '-';
    		if(!empty($data['nach_attempts']) && !empty($data['successfull_nach_attempts'])){
    			$data['nach_attempts_ratio'] = ROUND( ($data['successfull_nach_attempts'] * 100 ) / $data['nach_attempts'], 2)  . "%";
    		}
    	}

    	//Get order_ids whose payment is recovered completely
    	$order_ids = OrderPayment::getOrderIdsForBalanceRecovered($this->db, $customer_id);

    	//Get order's last delivered date, and NACH payment date with success status for given order ids
    	$data['order_on_time_nach_recovery'] = OrderPayment::getNachPaymentOnTimeRecovered($this->db, $order_ids);

    	$data['order_total']         = !empty($data['order_total']) ? $this->registry->currency->money_format($data['order_total']) : 'N/A';
    	$data['total_credit_orders'] = !empty($data['total_credit_orders']) ? $this->registry->currency->money_format($data['total_credit_orders']) : 'N/A';

    	$data['avg_wsb_credit_order_amount'] = !empty($data['avg_wsb_credit_order_amount']) ? $this->registry->currency->money_format($data['avg_wsb_credit_order_amount']) : 'N/A';

    	$data['credit_activation_limit']     = !empty($data['credit_activation_limit']) ? $this->registry->currency->money_format($data['credit_activation_limit']) : 'N/A';

    	return $data;
    }

    /**
     * @info: Private function to set WSB credit relates for customer profile
     * @param: int $customer_id,
     * @param: array $data As refrence
     * @return: void
     * @author: Nishu, 22nd June 2019
    */
    private function setWsbCreditCustomerProfile(int $customer_id, array &$data) : void{
    	//WSB Credit details
		$wsb_credit  = new WsbCreditPayment($this->registry);
		$selector    = array('status AS wsb_credit_status', 'credit_limit AS wsb_credit_current_limit');
		$wsb_details = $wsb_credit->getWsbCreditDetailByCustomerIds($customer_id, $selector); 
		$wsb_details = $wsb_details[$customer_id] ?? array();

		$data['wsb_credit_status']        = $wsb_details['wsb_credit_status'] ?? 'N/A';
		$data['wsb_credit_current_limit'] = (!empty($wsb_details['wsb_credit_current_limit']) && $wsb_details['wsb_credit_status'] == 'ENABLED') ? $this->registry->currency->money_format($wsb_details['wsb_credit_current_limit']) : 'N/A';

		return;
    }

        /**
	   * Public Method to get all related ids for a given customer id
	   * @param: $customer_id int
	   * @return $related_ids String
	   * @author: Devendra, August 2019
	*/
    public static function getRelatedIdsByCustomerId(DataBase\DB $db, int $customer_id) : ?string{
      $data = null;
      if(!empty($customer_id)){
        $sql = "
            SELECT 
                GROUP_CONCAT(DISTINCT oc2.customer_id) AS all_related_ids 
            FROM ". DB_PREFIX ."customer oc1 
            INNER JOIN ". DB_PREFIX ."customer oc2 ON oc1.master_id = oc2.master_id 
            WHERE oc1.customer_id = '". $customer_id ."'
        ";
        
        $result = $db->query($sql);
        if($result->num_rows > 0){
        	$data = $result->row['all_related_ids'];
        }
      }
      return $data;
    }

    public static function updateCustomerVPA($db, $customer_id, $upi_vpa)
    {
        if(!empty($customer_id) && !empty($upi_vpa)){
            $sql = "
                    UPDATE
                        ".DB_PREFIX."customer
                    SET
                        upi_vpa = '". $db->escape($upi_vpa)."'
                    WHERE
                        customer_id = ".(int)$customer_id."
                   ";
          
            $db->query($sql);
        }
    }

    /**
     * @info: Public static methos to get customer info from oc-customer table for single or array of customer_ids
     *        Data fields as specified in selector
     * @param: $db DB object
     * @param: $customer_ids - single or array of customer-ids
     * @param: $selector- string or  array oc_customer table fields
     * @return: data Customer Id wise
     * @author: Nishu, Sept 2019
    */
    public static function getCustomerInfo($db, $customer_ids, $selector){
        $data = array();
        if(!empty($customer_ids)){
            if(is_array($customer_ids)){
                $customer_ids = implode(',', $customer_ids);
            }


            if(!empty($selector)){
                if(is_array($selector)){
                    $selector = implode(',', $selector);
                }
                $selector = ", ". $selector;
            }

            $sql = "
                    SELECT
                        customer_id ". $selector."
                    FROM
                        ".DB_PREFIX."customer 
                    WHERE
                        customer_id IN (". $customer_ids.")
                   ";
                
            $results = $db->query($sql);
            if($results->num_rows > 0){

                $data = array_combine(
                            array_column($results->rows, 'customer_id'), 
                            $results->rows
                        );
            }
        }
        return $data;
    }

}
