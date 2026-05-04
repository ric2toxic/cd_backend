<?php
/**
* 
*/
class ControllerRestapiCart extends Controller
{

    private $_franchise_id = 0;
    private $_franchise_margin = 0;
    private $_banner_splitter = "__";

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->__getFranchiseDetailsFromHeader();
    }
	
	private function validateMobileApiCall($request){
			//$this->validateApiCall();
			if(isset($request['access_token'])){
				if(isset($request['user_id'])){
					$access_token = $request['access_token'] ?? '';
					$user_id = $request['user_id'] ?? 0;
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
					if($check_access_token > 0){
						return true;
					}else{
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
							
				}else{
					$rt['error_code'] = '1003';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}	
			}else{
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Login failed';
				$rt['message'] = 'http request does not have access token.';
			}
			return json_encode($rt);
		
	}
	
	/**
	 *  @param : {"access_token":"ZUegiLbfYCUASFJP","user_id":"8448","last_session_id":"2"} 
	 *  @author: Ravindra Singh
	 *  @dateTime : 2016-10-25
	 */
	public function updateUserId(){
		$this->load->model('restapi/service');
		$this->load->model('restapi/wsbcartservice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$headers = getallheaders();
			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
			$checkValid = $this->validateMobileApiCall($request);
			if($checkValid == 1){
				$update_user_id = $this->model_restapi_wsbcartservice->updateUserId($request);
				return true;
			}else{
				echo $checkValid;  exit;
			}
		}
	}
	
	
	/**
	 *  @param : {"access_token":"ZUegiLbfYCUASFJP","language":"en","product_id":"39612","product_quantity":"1","user_id":"8448","version":"2"} // For wholesale products (Mobile App)
	 *  @param : {"access_token":"ZUegiLbfYCUASFJP","language":"en","product_id":"38262","product_option":"3265","product_option_value":"10063","product_quantity":"2","user_id":"8448","version":"2"} // For single products (Mobile App)
	 *  @author: Ravindra Singh
	 *  @dateTime : 2016-10-15
	 */
	public function addToCart(){

		$this->load->model('restapi/service');
    	$this->load->model('catalog/product');
		$this->load->model('restapi/wsbcartservice'); 

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$checkValid = '1';
			$headers = getallheaders();
			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );

            $last_cart_modified_from = 'ANDROID';
            if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
                $last_cart_modified_from = 'IOS';
            }
            $cart_modified_once_from = array();
			$extra['record_update'] = 0;
			$user_id = (int)($request['user_id'] ?? 0);
			if( $user_id > 0 ){
				$checkValid = $this->validateMobileApiCall($request);
				$extra['user_id'] = $user_id;
				
				if(isset($headers['User-Agent'])){
					$extra['user_agent'] = $headers['User-Agent'];
				}elseif(isset($request['user_agent'])){
					$extra['user_agent'] = $request['user_agent'];
				}
				$conditions = 'customer_id = '. (int)$user_id .' order by date_modified desc limit 1';
				$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
				if(!empty($customer_cart_data)){
					$extra['record_update'] = 1;
				}
				if(isset($request['last_session_id']) && $request['last_session_id'] != '0'){
					$conditions = 'cart_session_id = "'. $this->db->escape($request['last_session_id']) .'" order by date_modified desc limit 1';	
					$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
					if(!empty($customer_cart_data)){
						//$extra['record_update'] = 0;
					}
				}

			}else{ 

				if(isset($request['cart_session_id'])){
					$user_id = 0;
					$extra['record_update'] = 0;
					
					$extra['cart_session_id'] = $request['cart_session_id'];
					if(isset($request['last_session_id']) && $request['last_session_id'] != '0'){
						$last_session_id = $request['last_session_id'];
					}else{
						$last_session_id = $request['cart_session_id'];
					}
					$extra['user_id'] = $user_id;
					
					if(isset($request['user_agent'])){
						$extra['user_agent'] = $request['user_agent'];
					}
					
					$conditions = 'cart_session_id = "'. $this->db->escape($last_session_id) .'" order by date_modified desc limit 1';	 
					$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
					if(empty($customer_cart_data)){
						$extra['record_update'] = 0;
					}else if($request['cart_session_id'] == $customer_cart_data[0]['cart_session_id']){
						$extra['record_update'] = 1;
					}else if($request['last_session_id'] != $request['cart_session_id']){
						$extra['record_update'] = 0;
					}else if($request['last_session_id'] == $request['cart_session_id']){
						$extra['record_update'] = 1;
					}
					$checkValid = 1;
				}
			}
			
			if($checkValid == 1){ 
				$product_id = $request['product_id'];
        
        $combo_id = $this->model_catalog_product->getComboProductIdOfAssociate($product_id);
        if (!empty($combo_id)) {
          // means this product is associate, so we will replace product id with combo product id 
          $product_id = $combo_id;
        }

                $product_quantity_array = explode(",", $request['product_quantity']);
                $product_quantity = $product_quantity_array[0];

                if (!empty($request['product_option'])) {
                    $product_option_id_array = explode(",", $request['product_option']);
                }else{
                    $product_option_id_array = array();
                }

                if (!empty($request['product_option_value'])) {
                    $product_option_value_array = explode(",", $request['product_option_value']);
                }else{
                    $product_option_value_array = array();
                }

                $cart_data = array();
                $cart_history = array();
                if(!empty($customer_cart_data)) {
                    $ccdata = $customer_cart_data[0];
                    $cart_data = unserialize($ccdata['cart_data']);
                    $cart_history = unserialize($ccdata['cart_history']);
                    $extra['cart_id'] = $ccdata['id'];
                    if($ccdata['parent_cart_id'] == 0){
                        $extra['parent_cart_id'] = $ccdata['id'];
                    }else{
                        $extra['parent_cart_id'] = $ccdata['parent_cart_id'];
                    }
                    if(isset($request['cart_session_id']) && !empty($request['cart_session_id'])){
                        $extra['cart_session_id'] = $request['cart_session_id'];
                    }else{
                        $extra['cart_session_id'] = $ccdata['cart_session_id'];
                    }
                }

                if(!empty($product_option_id_array)){
                    $i = 0;
                    foreach ($product_option_id_array as $option_id){
                        $qty = $product_quantity_array[$i];
                        $product_option_id = $option_id;
                        $product_option_value = $product_option_value_array[$i];
                        $product = array();
                        $product_cart = array();
                        $product['product_id'] = (int)$product_id;
                        $option = array($product_option_id => $product_option_value );
                        $product['option'] = $option;
                        $product_cart['option'] = $option;

                        if(isset($request['ip'])){
                            $product_cart['ip'] = $request['ip'];
                        }
                        if(isset($request['added_by_id'])){
                            $product_cart['added_by_id'] = $request['added_by_id'];
                        }
                        if(isset($request['added_by_name'])){
                            $product_cart['added_by_name'] = $request['added_by_name'];
                        }
                        $product_cart['added_time'] = Date('Y-m-d H:i:s');

                        $key = base64_encode(serialize($product));
                        $product_cart_key = base64_encode(serialize($product_cart));

                        if ((int)$qty && ((int)$qty > 0)) {
                            if(!isset($cart_data[$key])){
                                $cart_data[$key] = (int)$qty;
                            }else{
                                $cart_data[$key] += (int)$qty;
                            }
                            if(!isset($cart_history[$product_cart_key])){
                                $cart_history[$product_cart_key] = (int)$qty;
                            }else{
                                $cart_history[$product_cart_key] += (int)$qty;
                            }
                        }

                        $i++;
                    }
                }
                else{

                    $qty = $product_quantity;

                    $product = array();
                    $product_cart = array();
                    $product['product_id'] = (int)$product_id;
                    $product_cart['product_id'] = (int)$product_id;
                    if(isset($request['ip'])){
                        $product_cart['ip'] = $request['ip'];
                    }
                    if(isset($request['added_by_id'])){
                        $product_cart['added_by_id'] = $request['added_by_id'];
                    }
                    if(isset($request['added_by_name'])){
                        $product_cart['added_by_name'] = $request['added_by_name'];
                    }
                    $product_cart['added_time'] = Date('Y-m-d H:i:s');

                    $key = base64_encode(serialize($product));
                    $product_cart_key = base64_encode(serialize($product_cart));

                    if ((int)$qty && ((int)$qty > 0)) {
                        if(!isset($cart_data[$key])){
                            $cart_data[$key] = (int)$qty;
                        }else{
                            $cart_data[$key] += (int)$qty;
                        }
                        if(!isset($cart_history[$product_cart_key])){
                            $cart_history[$product_cart_key] = (int)$qty;
                        }else{
                            $cart_history[$product_cart_key] += (int)$qty;
                        }
                    }

                }

                $extra['cart_data'] = serialize($cart_data);
                $extra['cart_history'] = serialize($cart_history);

				//echo "<pre>"; print_r($extra);exit;
				if(isset($extra['user_id']) && $extra['user_id'] != 0){
					$user_id = $extra['user_id'];
				}else{
					$user_id = $last_session_id;
				}
				// print_r($extra); die;
				$this->load->model('lead/lead');
				$telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".(int)$extra['user_id'] )->row['telephone'];
				if (!empty($telephone)) {					
					$lead_data = [
						'cart_modified_date'	=> date('Y-m-d H:i:s'),
						'cart'					=> $extra['cart_data'],
						'is_cart'				=> 1
					];
					$this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $extra['user_id'] );
				}

                if (!empty($customer_cart_data)) {
                    $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
                }
                $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
                $cart_modified_once_from = serialize($cart_modified_once_from);

                $extra['last_cart_modified_from'] = $last_cart_modified_from;
                $extra['cart_modified_once_from'] = $cart_modified_once_from;

				$rs = $this->model_restapi_wsbcartservice->add($extra);
				$total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int)$user_id);
				if($rs == '1'){
					$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['total_cart'] = $total_cart;
					if($extra['record_update'] == '1'){
						$rt['message'] = 'New Product Updated To Your Shopping Cart';					
					}else{
						$rt['message'] = 'New Product Added To Your Shopping Cart';					
					}
				}else{
					$rt['status'] = '1001';
					$rt['status_text'] = 'Error';
					$rt['total_cart'] = $total_cart;
					$rt['message'] = 'Add product failed';
				}
			}else{ 
				echo $checkValid;  exit;
			}
			if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
				return true;
			}else{
				echo json_encode($rt); exit;	
			}
		} 
			
	}
	/**
	 * {"user_id" : "198",
		*"access_token" : "x74d97b01eae257e", 
		*"key" : '', 
		*"quantity" : 0
		*
		*}
	 * @author Ravindra Singh
	 */
	public function UpdateCart(){
		$this->load->model('restapi/service');
		$this->load->model('restapi/wsbcartservice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$checkValid = '1';
			$rt = array();
			$headers = getallheaders();

            $last_cart_modified_from = 'ANDROID';
            if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
                $last_cart_modified_from = 'IOS';
            }
            $cart_modified_once_from = array();

			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
			if(isset($request['user_id']) && $request['user_id'] != '0'){
				$user_id = $request['user_id'];
				$checkValid = $this->validateMobileApiCall($request);
				$extra['user_id'] = $user_id;

				$conditions = 'customer_id = '. (int)($request['user_id'] ?? 0) .' order by date_modified desc limit 1';
				$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
				if(isset($request['last_session_id']) && $request['last_session_id'] != '0' && !empty($customer_cart_data)){
					$conditions = 'cart_session_id = "'. $this->db->escape($request['last_session_id']) .'" order by date_modified desc limit 1';	
					$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
				}

			}else{ 
				if(isset($request['cart_session_id'])){
					$user_id = 0;
					$extra['cart_session_id'] = $request['cart_session_id'];
					if(isset($request['last_session_id']) && $request['last_session_id'] != '0'){
						$last_session_id = $request['last_session_id'];
					}else{
						$last_session_id = $request['cart_session_id'];
					}
					$extra['user_id'] = $user_id;
					
					$conditions = 'cart_session_id = "'. $this->db->escape($last_session_id) .'" order by date_modified desc limit 1';	 
					$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
					$checkValid = 1;
				}
			}

            if (!empty($customer_cart_data)) {
                $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
            }
            $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
            $cart_modified_once_from = serialize($cart_modified_once_from);

            $extra['last_cart_modified_from'] = $last_cart_modified_from;
            $extra['cart_modified_once_from'] = $cart_modified_once_from;
			
			if($checkValid == 1){

				if(isset($request['key']) && !empty($request['key'])){
					$extra['key'] = $request['key'];
					$key = $request['key'];
					$extra['quantity'] = $request['quantity'];

                    $extra['customer_cart_data'] = $customer_cart_data;
                    $rs = $this->model_restapi_wsbcartservice->update($extra);

				}else{
                    //Changed code to accept product id in a comma separated string to
                    //update quantity for multiple products

				    $product_id_string = $request['product_id'];
                    $product_quantity_string = $request['product_quantity'];

                    if (isset($request['product_option'])) {
                        $product_option_id_string = $request['product_option'];
                    }else{
                        $product_option_id_string = "";
                    }


                    if (isset($request['product_option_value'])) {
                        $product_option_value_string = $request['product_option_value'];
                    }else{
                        $product_option_value_string = "";
                    }

                    $product_id_array = explode(",", $product_id_string);
                    $product_quantity_array = explode(",", $product_quantity_string);
                    $product_option_id_array = explode(",", $product_option_id_string);
                    $product_option_value_array = explode(",", $product_option_value_string);


                    $i = 0;
                    $item_to_update = array();
                    foreach ($product_id_array as $product_id) {

                        $product_quantity = $product_quantity_array[$i];

                        if (count($product_option_value_array) > $i && $product_option_id_array[$i] > 0) {
                            $product_option_id = $product_option_id_array[$i];
                        } else {
                            $product_option_id = 0;
                        }

                        if (count($product_option_value_array) > $i && $product_option_value_array[$i] > 0) {
                            $product_option_value = $product_option_value_array[$i];
                        } else {
                            $product_option_value = 0;
                        }
                        $item_to_update[] = $this->prepareExtraDataForUpdateCart($product_id, $product_quantity, $product_option_id, $product_option_value);

                        $i++;
                    }
                    if(count($item_to_update) > 0) {

                        $extra['customer_cart_data'] = $customer_cart_data;
                        $rs = $this->model_restapi_wsbcartservice->bulk_update($extra, $item_to_update);
                    }


				}

				// IN CRM
				$this->load->model('lead/lead');
				$telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".$extra['user_id'] )->row['telephone'];
				if (!empty($telephone)) {
					$lead_data = [
						'cart_modified_date'	=> date('Y-m-d H:i:s'),
						'cart'					=> $extra['customer_cart_data'][0]['cart_data'],
						'is_cart'				=> 1
					];
					$this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $extra['user_id'] );
				}


				if(isset($extra['user_id']) && $extra['user_id'] != 0){
					$user_id = $extra['user_id'];
				}else{
					$user_id = $last_session_id;
				}
				$total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int)$user_id);
				if($rs == '1'){
					$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['total_cart'] = $total_cart;
					$rt = $this->cart_data($request);
					$rt['message'] = 'Product Updated To Your Shopping Cart';					
					
				}else{
					$rt['status'] = '1001';
					$rt['status_text'] = 'Error';
					$rt['total_cart'] = $total_cart;
					$rt['message'] = 'Product updated failed';
				}

			}else{
				echo $checkValid; exit;
			}
			if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
				return true;
			}else{
				echo json_encode($rt); exit;	
			}
		}
	}
	
	
	public function mergeCart(){   
		$this->load->model('restapi/wsbcartservice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$checkValid = '1';
			$rt = array();
			$headers = getallheaders();
            $last_cart_modified_from = 'ANDROID';
            if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
                $last_cart_modified_from = 'IOS';
            }
            $cart_modified_once_from = array();
			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
			if(isset($request['user_id']) && $request['user_id'] != '0'){
				$user_id = $request['user_id'];
				$checkValid = $this->validateMobileApiCall($request);
				$extra['user_id'] = $user_id;
				
				$conditions = 'customer_id = '. (int)($request['user_id'] ?? 0) .' order by date_modified desc limit 1';
				$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
				if(isset($request['last_session_id'])){
					$conditions = 'cart_session_id = "'. $this->db->escape($request['last_session_id']) .'" order by date_modified desc limit 1';	
					$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
				}

			}else{ 
				if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
					$user_id = 0;
					$extra['cart_session_id'] = $request['cart_session_id'];
					if(isset($request['last_session_id']) && $request['last_session_id'] != '0'){
						$last_session_id = $request['last_session_id'];
					}else{
						$last_session_id = $request['cart_session_id'];
					}
					$extra['user_id'] = $user_id;
					
					$conditions = 'cart_session_id = "'. $this->db->escape($last_session_id) .'" order by date_modified desc limit 1';	 
					$customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);
					$checkValid = 1;
				}
			}
            if (!empty($customer_cart_data)) {
                $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
            }
            $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
            $cart_modified_once_from = serialize($cart_modified_once_from);

            $extra['last_cart_modified_from'] = $last_cart_modified_from;
            $extra['cart_modified_once_from'] = $cart_modified_once_from;
			if($checkValid == 1){ 
				$user_id = $extra['user_id'];
				$cart = '';
				if(!empty($customer_cart_data)){
					if(isset($customer_cart_data[0]['cart_data']) && $customer_cart_data[0]['customer_id'] == '0'){
						$cart = $customer_cart_data[0]['cart_data'];
						$extra['cart_data'] = $cart;
						$extra['cart_history'] = '';
						$extra['cart_session_id'] = $request['cart_session_id'];
						if($customer_cart_data[0]['parent_cart_id'] == '0'){
							$extra['parent_cart_id'] = $customer_cart_data[0]['id'];	
						}else{
							$extra['parent_cart_id'] = $customer_cart_data[0]['parent_cart_id'];	
						}
						$extra['record_update'] = 0;
						if($this->model_restapi_wsbcartservice->add($extra)){
							return true;	
						}
					}
				}
				
			}else{
				echo $checkValid;  exit;
			}
		}
	}
	
	/**
	 * removeCart
	 * Request Parameters : user_id,access_token,product_id
	 * @author Ravindra Singh
	 * @dateTime 22-10-2016
	 */
	 
	public function removeCart(){
		
		$this->load->model('restapi/service');
		$this->load->model('restapi/wsbcartservice');
		$rt = array();

        if (($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $rt['status'] = '1001';
            $rt['status_text'] = 'Error';
            $rt['message'] = 'Request type not accepted!';
            echo json_encode($rt); exit;
        }

        $inputJSON = file_get_contents('php://input');
        $request = json_decode( $inputJSON, TRUE );

        $checkValid = $this->validateMobileApiCall($request);

        if ($checkValid !== true) {
            echo $checkValid; exit;
        }

        $headers = getallheaders();
        $last_cart_modified_from = 'ANDROID';
        if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
            $last_cart_modified_from = 'IOS';
        }
        $cart_modified_once_from = array();

        $user_id = $request['user_id'];
        $extra['user_id'] = $user_id;
        $extra['add_to_wishlist'] = $request['add_to_wishlist'];
        $extra['share_message'] = (!empty($request['share_message'])) ? $this->db->escape($request['share_message']) : '';

        $conditions = 'customer_id = '. (int)($request['user_id'] ?? 0) .' order by date_modified desc limit 1';
        $customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);

        if (!empty($customer_cart_data)) {
            $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
        }
        $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
        $cart_modified_once_from = serialize($cart_modified_once_from);

        $extra['last_cart_modified_from'] = $last_cart_modified_from;
        $extra['cart_modified_once_from'] = $cart_modified_once_from;

        if(isset($request['key']) && !empty($request['key'])){
            $key = $request['key'];
            $extra['key'] = $request['key'];
        }else{
            $product_id = $request['product_id'];
            if (isset($request['product_option'])) {
                $product_option_id = $request['product_option'];
            } else {
                $product_option_id = '';
            }
            if (isset($request['product_option_value'])) {
                $product_option_value = $request['product_option_value'];
            } else {
                $product_option_value = '';
            }
            $product['product_id'] = (int)$product_id;

            if (!empty($product_option_id)) {
                $option = array($product_option_id => $product_option_value );
                $product['option'] = $option;
            }

            $key = base64_encode(serialize($product));
            $extra['key'] = $key;
        }

        if(!empty($customer_cart_data)){
            $ccdata = $customer_cart_data[0];
            $ccdata['cart_data'] = unserialize($ccdata['cart_data']);
            $ccdata['product_comments'] = unserialize($ccdata['product_comments']);
            $nochanges = 0;
            if(isset($ccdata['cart_data'][$key])){
                $nochanges = 1;
                unset($ccdata['cart_data'][$key]);
            }
            // remove product comment
            if(isset($ccdata['product_comments'][$key])){
                unset($ccdata['product_comments'][$key]);
            }
  
            if($nochanges == 1){

                $extra['cart_id'] = $ccdata['id'];
                $extra['cart_data'] = serialize($ccdata['cart_data']);
                $extra['product_comments'] = serialize($ccdata['product_comments']);
                $remove_cart_data = $this->model_restapi_wsbcartservice->remove_cart_data($extra);
               
               //move to wishlist
               if($extra['add_to_wishlist'] == 1)
               {
                 $keys = explode(',',$extra['key']);
                 foreach($keys as $key)
                 {
                   $product =unserialize(base64_decode($key));
                   $this->model_restapi_service->deleteProductToWishlist((int)$extra['user_id'], (string)$product['product_id']);
				   $this->model_restapi_service->addProductToWishlist((int)$extra['user_id'], (string)$product['product_id'], (string)$extra['share_message']);
				 }
			    }
                //In CRM
                $this->load->model('lead/lead');
                $telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".$extra['user_id'] )->row['telephone'];
                if (!empty($telephone)) {
                    $lead_data = [
                        'cart_modified_date'	=> date('Y-m-d H:i:s'),
                        'cart'					=> $extra['cart_data'],
                        'is_cart'				=> 1
                    ];
                    $this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $extra['user_id'] );
                }

            }
            $total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int)$extra['user_id']);
            if(isset($remove_cart_data) && $remove_cart_data == 1){
                $rt['status'] = '1';
                $rt['status_text'] = 'Success';
                $rt['total_cart'] = $total_cart;
                $rt = $this->cart_data($request);
                $rt['message'] = 'Product removed successfully';
            }else{
                $rt['status'] = '1001';
                $rt['status_text'] = 'Error';
                $rt['total_cart'] = $total_cart;
                $rt['message'] = 'Product remove request failed';
            }
        } else{
            $rt = $this->cart_data($request);
        }

        echo json_encode($rt); exit;
	}

    /**
     * removeProductsFromCart
     * Request Parameters : user_id,access_token,product_id,product_option and product_option_value(comma separated list)
     * @author Devendra Dhayal
     * @dateTime 25-07-2017
     */

    public function removeProductsFromCart(){

        $this->load->model('restapi/service');
        $this->load->model('restapi/wsbcartservice');
        $rt = array();
        if (($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $rt['status'] = '1001';
            $rt['status_text'] = 'Error';
            $rt['message'] = 'Request type not accepted!';
            echo json_encode($rt); exit;
        }

        $inputJSON = file_get_contents('php://input');
        $request = json_decode( $inputJSON, TRUE );

        $checkValid = $this->validateMobileApiCall($request);

        if ($checkValid !== true) {
            echo $checkValid; exit;
        }

        $headers = getallheaders();
        $last_cart_modified_from = 'ANDROID';
        if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
            $last_cart_modified_from = 'IOS';
        }
        $cart_modified_once_from = array();

        $user_id = $request['user_id'];
        $extra['user_id'] = $user_id;

        $conditions = 'customer_id = '. (int)($request['user_id'] ?? 0) .' order by date_modified desc limit 1';
        $customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);

        if (!empty($customer_cart_data)) {
            $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
        }
        $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
        $cart_modified_once_from = serialize($cart_modified_once_from);

        $extra['last_cart_modified_from'] = $last_cart_modified_from;
        $extra['cart_modified_once_from'] = $cart_modified_once_from;

        if(!empty($customer_cart_data)) {
            $product_id_string = $request['product_id'];

            if (isset($request['product_option'])) {
                $product_option_id_string = $request['product_option'];
            }else{
                $product_option_id_string = "";
            }


            if (isset($request['product_option_value'])) {
                $product_option_value_string = $request['product_option_value'];
            }else{
                $product_option_value_string = "";
            }

            $product_id_array = explode(",", $product_id_string);
            $product_option_id_array = explode(",", $product_option_id_string);
            $product_option_value_array = explode(",", $product_option_value_string);

            $ccdata = $customer_cart_data[0];
            $ccdata['cart_data'] = unserialize($ccdata['cart_data']);
            $ccdata['product_comments'] = unserialize($ccdata['product_comments']);
            $nochanges = 0;
            $i = 0;
            foreach ($product_id_array as $product_id) {

                if (count($product_option_value_array) > $i && $product_option_id_array[$i] > 0) {
                    $product_option_id = $product_option_id_array[$i];
                } else {
                    $product_option_id = 0;
                }

                if (count($product_option_value_array) > $i && $product_option_value_array[$i] > 0) {
                    $product_option_value = $product_option_value_array[$i];
                } else {
                    $product_option_value = 0;
                }
                $product = array();
                $product['product_id'] = (int)$product_id;

                if ( $product_option_id > 0) {
                    $option = array($product_option_id => $product_option_value );
                    $product['option'] = $option;
                }

                $key = base64_encode(serialize($product));

                if(isset($ccdata['cart_data'][$key])){
                    $nochanges = 1;
                    unset($ccdata['cart_data'][$key]);
                }

                // remove product comment
                if(isset($ccdata['product_comments'][$key])){
                    unset($ccdata['product_comments'][$key]);
                }

                $i++;
            }

            if($nochanges == 1){
                $extra['cart_id'] = $ccdata['id'];
                $extra['cart_data'] = serialize($ccdata['cart_data']);
                $extra['product_comments'] = serialize($ccdata['product_comments']);
                $remove_cart_data = $this->model_restapi_wsbcartservice->remove_cart_data($extra);

                //In CRM
                $this->load->model('lead/lead');
                $telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".$extra['user_id'] )->row['telephone'];
                if (!empty($telephone)) {
                    $lead_data = [
                        'cart_modified_date'	=> date('Y-m-d H:i:s'),
                        'cart'					=> $extra['cart_data'],
                        'is_cart'				=> 1
                    ];
                    $this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $extra['user_id'] );
                }

            }
            $total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int)$extra['user_id']);
            if(isset($remove_cart_data) && $remove_cart_data == 1){
                $rt['status'] = '1';
                $rt['status_text'] = 'Success';
                $rt['total_cart'] = $total_cart;
                $rt = $this->cart_data($request);
                $rt['message'] = 'Product removed successfully';
            }else{
                $rt['status'] = '1001';
                $rt['status_text'] = 'Error';
                $rt['total_cart'] = $total_cart;
                $rt['message'] = 'Product remove request failed';
            }
        }else{
            $rt = $this->cart_data($request);
        }

        echo json_encode($rt); exit;
    }


    /**
     * addComment
     * Request Parameters : user_id,access_token,product_id,product_option,product_option_value and comment
     * @author Devendra dhayal
     * @dateTime 29-07-2017
     */

    public function addComment(){

        $this->load->model('restapi/service');
        $this->load->model('restapi/wsbcartservice');
        $rt = array();
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $checkValid = '1';
            $headers = getallheaders();
            //echo "<pre>"; print_r($headers); exit;
            //echo "<pre>"; print_r($_SERVER); exit;
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
            $extra['record_update'] = 0;
            //echo "<pre>"; print_r($request); exit;
            if(isset($request['user_id'])){
                $user_id = $request['user_id'];
                $checkValid = $this->validateMobileApiCall($request);
                $extra['user_id'] = $user_id;

                $conditions = 'customer_id = '. (int)($request['user_id'] ?? 0) .' order by date_modified desc limit 1';
                $customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);

            }else{
                if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
                    $user_id = 0;
                    $extra['cart_session_id'] = $request['cart_session_id'];
                    if(isset($request['last_session_id']) && $request['last_session_id'] != '0'){
                        $last_session_id = $request['last_session_id'];
                    }else{
                        $last_session_id = $request['cart_session_id'];
                    }
                    $user_id = 0;
                    $extra['record_update'] = 0;
                    $extra['cart_session_id'] = $request['cart_session_id'];
                    $extra['user_id'] = $user_id;
                    $conditions = "cart_session_id = '". $this->db->escape($last_session_id) ."' order by date_modified desc limit 1";
                    $customer_cart_data = $this->model_restapi_wsbcartservice->get_customer_cart($conditions);

                }
            }

            if($checkValid == 1){

                if(isset($request['key']) && !empty($request['key'])){
                    $key = $request['key'];
                    $extra['key'] = $request['key'];
                }else{
                    $product_id = $request['product_id'];
                    $product_quantity = 0; //$request['product_quantity'];
                    $qty = $product_quantity;
                    if (isset($request['product_option'])) {
                        $product_option_id = $request['product_option'];
                    } else {
                        $product_option_id = '';
                    }
                    if (isset($request['product_option_value'])) {
                        $product_option_value = $request['product_option_value'];
                    } else {
                        $product_option_value = '';
                    }
                    $product['product_id'] = (int)$product_id;

                    if (!empty($product_option_id)) {
                        $option = array($product_option_id => $product_option_value );
                        $product['option'] = $option;
                    }

                    $key = base64_encode(serialize($product));
                    $extra['key'] = $key;
                }
                if(!empty($customer_cart_data)){
                    $ccdata = $customer_cart_data[0];
                    $ccdata['product_comments'] = unserialize($ccdata['product_comments']);
                    $ccdata['product_comments'][$key] = $request['product_comment'];

                    $extra['cart_id'] = $ccdata['id'];
                    $extra['product_comments'] = serialize($ccdata['product_comments']);
                    $add_comment = $this->model_restapi_wsbcartservice->addComment($extra);

                    if(isset($add_comment) && $add_comment == 1){
                        $rt['status'] = '1';
                        $rt['status_text'] = 'Success';
                        $rt['message'] = 'Product comment updated successfully';
                    }else{
                        $rt['status'] = '1001';
                        $rt['status_text'] = 'Error';
                        $rt['message'] = 'Product comment update request failed';
                    }
                }
            }else{
                echo $checkValid;  exit;
            }
        }
        if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
            return true;
        }else{
            echo json_encode($rt); exit;
        }
    }
	
	
	
	public function validateApiCall(){
		return true;
		
		$this->load->model('restapi/service');

		$headers = getallheaders();

		if(!$this->model_restapi_service->validateApiCall($headers)){
			$rt['error_code'] = '9999';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Invalid API call';
			echo json_encode($rt); exit;
		}
	}
	
	/* *******
	 * Function : checkout
	 * Request Parameters : user_id,access_token
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success",}}
	 ******* */
	 
	public function checkout(){
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
			$rt = array();
			$this->validateApiCall();

			if(isset($request['access_token'])){
				if(isset($request['user_id'])){
					$access_token = $request['access_token'] ?? '';
					$user_id = $request['user_id'] ?? 0;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
					if($check_access_token > 0){
            $headers = getallheaders();
						$dynamicotp = rand(1000, 9999);
						$this->load->model('restapi/cartservice');
						$res = $this->model_restapi_cartservice->UpdateOtp($user_id, $dynamicotp);
						$ar[$user_id] = $dynamicotp;
            
            // if FSE is placing order for customer via CRM
            if(!empty($headers['REQUEST_BY']) && strtoupper($headers['REQUEST_BY']) == "CRM APP") {
              if(!empty($request['device_id'])) {
                $sales_agent = $this->model_restapi_service->getSalesStaffWithDeviceId($request['device_id']);
                if(!empty($sales_agent['staff_id'])) {
                  $ar['sales_staff_id'] = $sales_agent['staff_id'];
                }
              }
            }
                        
                        if(!empty($request['language']) && $request['language'] == 'hindi')
                        {
                          $ar['language'] = 2;
                        }
                        else
                        {
                          $ar['language'] = 1;
                        }

						$randstr = base64_encode(serialize($ar));
                        $link = $this->url->link("account/account/checkoutlogin/".$randstr,'','SSL');

						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
						$rt['userlink'] = $link;
						
					}else{
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
							
				}else{
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}	
			}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt); exit;
	}	
	/* *******
	 * Function : get_cart_data
	 * Request Parameters : user_id,access_token,coupon_code(optional),shipping_method(optional)
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success","data" : "cartdata"}}
	 ******* */
	public function get_cart_data(){ 
		
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$checkValid = '1';
			//echo "<pre>"; print_r($headers); exit;
			//echo "<pre>"; print_r($_SERVER); exit;
			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
            $headers = getallheaders();
            if(!empty($this->_franchise_id)){
                $request['franchise_id'] = $this->_franchise_id;
                if(!empty($this->_franchise_margin)){
                    $request['franchise_margin'] = $this->_franchise_margin;
                }
            }
			$checkValid = $this->validateMobileApiCall($request);
			
			if($checkValid == 1){
				$rt = $this->cart_data($request);
				echo json_encode($rt); exit;
			}else{
				echo $checkValid; exit;
			}
		}
	}
	
	public function cart_data($request){
		$this->load->model('restapi/cartservice');
		$rt = $this->model_restapi_cartservice->cart_data($request);
		$rt['banners'] = array();

		if( $this->config->get('config_store_id') != INTERNATIONAL_STORE_ID ) {
			$rt['bottom_page_banner'] = $this->model_restapi_cartservice->getCartBottomBanner();
		}
        
       if($rt['data'])
       {  
        $category_id = ''; 
		foreach($rt['data'] as $key_result)
        {
           foreach($key_result as $product_result)
           {
           	 if(!empty($product_result['product_details']['category_id']))
           	 {
           	 	$category_id .= $product_result['product_details']['category_id'].',';
           	 }
           }
        }

       
       if(!empty(trim($category_id)))
       {
        $category_id = substr($category_id, 0, strlen($category_id)-1);
        $category_id = array_unique(explode(",", $category_id));
       }
       else
       {
       	$category_id = array();
       } 

        if(count($category_id) > 0)
        {
           $this->load->model('tool/image');
		   $banners = $this->model_restapi_cartservice->get_cart_banner_data($category_id);
	     	if ( !empty($banners) ) {
    		foreach ( $banners as $key => $value ) {
                

    			$data['text'] = $value['title'];
    			$data['url']  = "";
    			$data['action']['category_id']    =  '';
			    $data['action']['product_id']     =  '';
				$data['action']['filter']         =  '';
			    $data['action']['price_filter']   =  '';
				$data['action']['sort_options']   =  '';
				$data['action']['filter_options'] =  '';
				$data['action']['search_term']    =  '';
                 
                if($value['type'] == "others")
                {
                  $data['action']['type'] =  $value['type'];	
                  $links = explode("&amp;",$value['link']);
               	  foreach($links as $link)
               	  {
               	  	$tab = explode("=",$link);
               	  	if(isset($tab[1]))
               	  	{
                      $data['action'][$tab[0]]  = $tab[1];
               	  	}
               	  }
                }
    			else if ($value['type'] == "link") 
    			{
					$data['url'] = $value['link'];
					$url = parse_url($data['url']);
					if (isset($url['path'])) 
					{
					    $path = explode('/', $url['path']);
					    $new_path = array_pop($path);
					    $category_id = '';
					    $product_id = '';
					    
					    $cat  = $this->db->query("SELECT query FROM ".DB_PREFIX."url_alias WHERE keyword='". $this->db->escape($new_path) ."'" );
					    if ($cat->num_rows) 
					    {
					    	if (explode('=', $cat->row['query'])[0] == 'category_id') {
					    	 	$category_id = explode('=', $cat->row['query'])[1];
					    	} elseif(explode('=', $cat->row['query'])[0] == 'product_id'){
					    	  	$product_id = explode('=', $cat->row['query'])[1];
					    	}
					    }
					}
					if (isset($url['query'])) {
					    $query_arr = explode('&', $url['query']);
					} elseif (isset($url['fragment'])) {
        				$query_arr = explode('&', substr($url['fragment'], 1));
        				$set_filter = true;
    				} else { $query_arr = array(); }

					$data['action']['category_id'] = isset($category_id) ? $category_id : '';
					$data['action']['product_id'] = isset($product_id)?$product_id:'';
					$i = 0;
					foreach ($query_arr as $value2) {
					    $arr = explode('=', $value2);
					    $arr[0] = str_replace("amp;", '', $arr[0]);

						if ($arr[0] == 'filter') {
            				$data['action']['filter'] =$arr[1] ;
       					}
					    if ($arr[0] == 'rating_filter') {
					        switch ($arr[1]) {
					            case 1:
					                if (isset($data['action']['filter'])) {
					                 $data['action']['filter_options'] = $data['action']['filter'] . "," . "20001";
					                }else {
					                    $data['action']['filter_options'] = "20001";
					                }

					             break;
					            case 2:
					                if (isset($data['action']['filter'])) {
					                    $data['action']['filter_options'] = $data['action']['filter'] . "," . "20002";
					                }  else {
					                    $data['action']['filter_options'] =  "20002";
					                }
					            break;
					            case 3:
					                if (isset($data['action']['filter'])) {
					                    $data['action']['filter_options'] = $data['action']['filter'] . "," . "20003";
					                 } else {
					                    $data['action']['filter_options'] = "20003";
					                }
					            break;
					            case 4:
					                if (isset($data['action']['filter'])) {
					                    $data['action']['filter_options'] = $data['action']['filter'] . "," . "20004";
					                } else {
					                    $data['action']['filter_options'] =  "20004";
					                }
					            break;
					            case 5:
					                if (isset($data['action']['filter'])) {
					                    $data['action']['filter_options'] = $data['action']['filter'] . "," . "20005";
					                } else {
					                    $data['action']['filter_options'] = "20005";
					                }
					            break;
					            default:
					                if (isset($data['action']['filter'])) {
					                    $data['action']['filter_options'] = $data['action']['filter'] . "," . "20000";
					                } else {
					                    $data['action']['filter_options'] =  "20000";
					                }
					            break;
					        }
					    }if ($arr[0] == 'sort') {
					        $inner_temp = $arr[1];
					    }
					    if ($arr[0] == 'order') {
					        if ($arr[1] == 'DESC') {
					            if (isset($inner_temp)) {
					                if ($inner_temp == 'p.date_added') {
					                    $data['action']['sort_options'] = 'latest_designs';
					                } elseif ($inner_temp == 'p.selling_price') {
					                    $data['action']['sort_options'] = 'price_high_to_low';
					                }
					            }
					        } else {
					            if (isset($inner_temp)) {
					                if ($inner_temp == 'p.selling_price') {
					                    $data['action']['sort_options'] = 'price_low_to_high';
					                }
					            }
					        }
					    } if ($arr[0] == 'search') {
					        $data['action']['search_term'] = urldecode($arr[1]);
					    } if ($arr[0] == 'category_id') {
					        $data['action']['category_id'] = $arr[1];
					    } if ($arr[0] == 'price_filter') {
					        $data['action']['price_filter'] = $arr[1];
					    }  if ($arr[0] == 'product_id') {
					        $data['action']['product_id'] = $arr[1];
					    } if ($arr[0] == 'clearance_sale') {
					        $data['action']['clearance_sale'] = $arr[1];
					    }
					    $i++;
					}
    			} 
    			else 
    			{
				   $data['action']['category_id']    =  $value['link'];
				   $data['action']['sort_options']   =  'latest_designs';
				}
                 

    			$data['image'] = $this->model_tool_image->resize( $value['image'], 720, 274 );
                
                
                $rt['banners'][] = $data;

             }
           }
         }  
       }
		return $rt;
	}
	
	/* *******
	 * Function : get_shipping_methods
	 * Request Parameters : user_id,access_token,country_code
	 * Type : Post
	 * Output : {"status":"1","status_text":"Success","data" : "shipping methods data"}
	 ******* */
	 public function get_shipping_methods(){ 
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
			$rt = array();
			$this->validateApiCall();

			if(isset($request['access_token'])){
				if(isset($request['user_id'])){
					$access_token = $request['access_token'] ?? '';
					$user_id = $request['user_id'] ?? 0;
					if(isset($request['country_code'])){
						$country_code = $request['country_code'];
					}else{
						$country_code = "IN";
					}
					$user_data['user_id'] = $user_id;
					$user_data['country_code'] = $country_code;
					$rsdata = array();
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
					if($check_access_token > 0){
						$shipping_methods = $this->quote_cart($user_data);
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
						$rt['data'] = $shipping_methods['shipping_methods'];
						
					}else{
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
							
				}else{
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}	
			}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt); exit;
	}

	public function quote_cart($user_data) {
		$user_id = $user_data['user_id'];
		$country_cod = $user_data['country_code'];;
		$this->load->language('checkout/shipping');
		$this->load->model('localisation/country');
		$json = array();
		$return_data = array();

		$country_data = $this->model_localisation_country->getCountryByCode($country_cod);

		if(isset($country_data['country_id']) && !empty($country_data['country_id'])){
			$country_id = $country_data['country_id'];
		}else{
			$country_id = '99';
		}

		$this->request->post['country_id'] = $country_id;
		$this->request->post['zone_id'] = 1501;
		$this->request->post['postcode']  = '';

		if (!$this->cart->hasProducts($user_id)) {
			$json['error']['warning'] = $this->language->get('error_product');
		}

		if (!$this->cart->hasShipping($user_id)) {
			$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		}

		if ($this->request->post['country_id'] == '') {
			$json['error']['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
			$json['error']['zone'] = $this->language->get('error_zone');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$json['error']['postcode'] = $this->language->get('error_postcode');
		}

		if (!$json) {

			if ($country_info) {
				$country = $country_info['name'];
				$iso_code_2 = $country_info['iso_code_2'];
				$iso_code_3 = $country_info['iso_code_3'];
				$address_format = $country_info['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$zone = $zone_info['name'];
				$zone_code = $zone_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$shipping_address = array(
					'firstname'      => '',
					'lastname'       => '',
					'company'        => '',
					'address_1'      => '',
					'address_2'      => '',
					'postcode'       => $this->request->post['postcode'],
					'city'           => '',
					'zone_id'        => $this->request->post['zone_id'],
					'zone'           => $zone,
					'zone_code'      => $zone_code,
					'country_id'     => $this->request->post['country_id'],
					'country'        => $country,
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format
			);
			$json['shipping_address'] = $shipping_address;
			$quote_data = array();

			$this->load->model('extension/extension');

			$results = $this->model_extension_extension->getExtensions('shipping');
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					if($result['code'] != "free"){ //remove free shipping option
						$this->load->model('shipping/' . $result['code']);

						$quote = $this->{'model_shipping_' . $result['code']}->getAppQuote($shipping_address,$user_id);
						if ($quote) {
							$quote_data[$result['code']] = array(
									'title'      => $quote['title'],
									'quote'      => $quote['quote'],
									'sort_order' => $quote['sort_order'],
									'error'      => $quote['error']
							);
						}
					}
				}
			}
			
			$sub_total = $this->cart->getSubTotal($user_id);

			if($this->cart->is_dropshipper($user_id) == '1'){
				if($sub_total < $this->config->get('config_cart_limit')){
					$dropshipper_shipping_method = $quote_data['weight']['quote']['weight_8'];					
					$quote_data['weight']['quote'] = array();
					$quote_data['weight']['quote']['weight_8'] = $dropshipper_shipping_method;
				}
			}else{
				if(isset($quote_data['weight']['quote']['weight_8'])){
					unset($quote_data['weight']['quote']['weight_8']);
				}
			}
			//print_r($quote_data); exit;
			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);
			$shipping_methods = $quote_data;

			if ($shipping_methods) {
				$json['shipping_methods'] = $shipping_methods;
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			}
			
			//$return_data['shipping_address'] = $shipping_address;
			//$return_data['shipping_methods'] = $shipping_methods;
			
		}

		return $json;
	}

	public function shipping_cart($user_id,$quote_data) {
		$this->load->language('checkout/shipping');

		$json = array();
		//echo "<pre>"; print_r($quote_data['shipping_address']); exit;
		if(isset($quote_data['shipping_address']['country_id']) && $quote_data['shipping_address']['country_id'] == "99"){
			//  Default shipping method for dropshipper
			if($this->cart->getSubTotal($user_id) > $this->config->get('config_cart_limit')){
				$this->request->post['shipping_method'] = 'weight.weight_5';
			}else{
				if($this->cart->is_dropshipper($user_id) == 1){ 
					$this->request->post['shipping_method'] = 'weight.weight_8';
				}else{
					if(isset($quote_data['shipping_method_selected']) && !empty($quote_data['shipping_method_selected'])){
						$this->request->post['shipping_method'] = $quote_data['shipping_method_selected'];
					}else{
						$this->request->post['shipping_method'] = 'weight.weight_5';
					}
				}
			}
		}else{
			//  Default shipping method for dropshipper
			if($this->cart->is_dropshipper($user_id) == 1){
				$this->request->post['shipping_method'] = 'weight.weight_9';
			}else{
				if(isset($quote_data['shipping_method_selected']) && !empty($quote_data['shipping_method_selected'])){
					$this->request->post['shipping_method'] = $quote_data['shipping_method_selected'];
				}else{
					$this->request->post['shipping_method'] = 'weight.weight_9';
				}
			}

		}


		if (!empty($this->request->post['shipping_method'])) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($quote_data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['warning'] = $this->language->get('error_shipping');
			}
		} else {
			$json['warning'] = $this->language->get('error_shipping');
		}

		if (!$json) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			$json['shipping_method'] = $quote_data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			
		}

		return $json;
	}
	/* *******
	 * Function : clear_cart
	 * Request Parameters : user_id,access_token
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success",}}
	 ******* */
	public function clear_cart(){
		$this->load->model('restapi/service');
		$this->load->model('restapi/wsbcartservice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode( $inputJSON, TRUE );
			$rt = array();
			$checkValid = '1';
			$headers = getallheaders();
            $last_cart_modified_from = 'ANDROID';
            if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
                $last_cart_modified_from = 'IOS';
            }
            $request['last_cart_modified_from'] = $last_cart_modified_from;

			//echo "<pre>"; print_r($request); exit;
			if(isset($request['user_id'])){
				$user_id = $request['user_id'];
				$checkValid = $this->validateMobileApiCall($request);
				$extra['user_id'] = $user_id;
				//$extra['customer_cart_data'] = $customer_cart_data;
			}else{
				if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
					$user_id = 0;
					$checkValid = '1';
				}
			}
			if($checkValid == 1){ 
				$clear_cart = $this->model_restapi_wsbcartservice->clearCart($request);

				//clear cart status IN LEAD CRM
				$this->load->model('lead/lead');
				
				$telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id = '".$request['user_id']."' ")->row['telephone'];
				
				if (!empty($telephone)) {
						$lead_data = [						
							'cart'		=> '',
							'is_cart'		=> '0'
						];
					$this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $request['user_id'] );
				}

				if($clear_cart){
					$rt['status'] = '1';
					$rt['status_text'] = 'Your WholesaleBox cart is empty, but it does not have to be.';
				}else{
					$rt['status'] = '0';
					$rt['status_text'] = 'Oops! Something went wrong.';
				}
			}else{ 
				echo $checkValid;  exit;
			}
			
			if(isset($request['last_session_id']) || isset($request['cart_session_id'])){
				return true;
			}else{
				echo json_encode($rt); exit;	
			}
		}
		//echo json_encode($rt); exit;
	}

	public function prepareExtraDataForUpdateCart($product_id, $product_quantity,$product_option_id, $product_option_value){
        $this->load->model('restapi/wsbcartservice');
	    $product['product_id'] = (int)$product_id;

        if (!empty($product_option_id)) {
            $option = array($product_option_id => $product_option_value );
            $product['option'] = $option;
        }

        $key = base64_encode(serialize($product));
        $extra['key'] = $key;
        $extra['quantity'] = $product_quantity;

        return $extra;
    }

    private function __getFranchiseDetailsFromHeader(){
        $headers = getallheaders();
        if(isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int)$headers['crm_role_id'] == (int)CRM_FRANCHISE_ROLE_ID){
            $this->load->model('restapi/service');
            $franchise_id = $this->model_restapi_service->getCustomerIdUsingCRMUserId($headers['crm_user_id']);
            if($franchise_id != 0){
                $this->_franchise_id = $franchise_id;
                if(isset($headers['franchise_margin'])){
                    $this->_franchise_margin = $headers['franchise_margin'];
                }
            }
        }
    }

    /* *******
    * Function : get_cart_data
         * Request Parameters : user_id,access_token,product_id
         * Type : Post
         * Output : {{"status":"1","status_text":"Success","data" : "product_data"}}
    ******* */
    public function getProductCartStatus() {

        $this->load->model('restapi/service');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $checkValid = $this->validateMobileApiCall($request);

            if($checkValid == 1){
                $user_id = $request['user_id'];
                $result = array();
                $products = $this->cart->getProducts($user_id,true);
                $quantity = 0;
                $options = array();
                foreach ($products as $product) {
                    if ($product['product_id'] == $request['product_id']) {
                        $quantity += $product['quantity'];
                        foreach ($product['option'] as $option) {
                            $options[] = array(
                                'product_option_id' => $option['product_option_id'],
                                'product_option_value_id' => $option['product_option_value_id'],
                                'option_id' => $option['option_id'],
                                'option_value_id' => $option['option_value_id'],
                                'quantity' => $product['quantity'],
                            );
                        }
                    }
                }

                $result[$request['product_id']] = array(
                        "total_quantity" => $quantity,
                        "options" => $options
                );

                $rt = array(
                    "status" => "1",
                    "status_text" => "Success",
                    "data" => $result
                );

                echo json_encode($rt); exit;

            }else{
                echo $checkValid; exit;
            }
        } else {
            $rt['error_code'] = '1010';
            $rt['status'] = '0';
            $rt['status_text'] = 'failed';
            $rt['message'] = 'http request type not accepted.';
            echo json_encode($rt); exit;
        }
    }
}
