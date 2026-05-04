<?php
class ControllerAccountReturn extends Controller {

	private $error = array();

	private $_delivered_state_id               = 15;
	private $_limit_in_days_for_quality        = 4;
	private $_limit_in_days_for_replacement    = 15;
	private $_limit_in_days_for_upload_courier = 3;
	private $_return_pickup_insert_id;
	private $_master_return_id;
	
	public function index() {

		$this->load->model('account/return');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		$total_return_quantity = 0;
		$data = array();
		$this->load->autoLoadLanguage('account/return', $data);

		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}		

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
	    $data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$data['all_returns'] = "";
		if(isset($this->request->get['order_id']) && !empty($this->request->get['order_id'])){
			$order_id = $this->request->get['order_id'];
		}else{
			$data['order_id'] = 0;
		}
		if(isset($this->request->get['ctoken']) && !empty($this->request->get['ctoken'])){
			$data['ctoken'] = $this->request->get['ctoken'];
		}else{
			$data['ctoken'] = 0;
		}
		$ctoken = $data['ctoken'];
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_my_orders'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['column_left'] = $this->load->controller('common/column_left');

		if(CONFIG_IS_MOBILE == 1)
        {
		  $data['footer'] = $this->load->controller('common/footer');
		  $data['header'] = $this->load->controller('common/header');
		}  

		if(isset($this->request->get['master_return_id'])){
			$data['master_return_id'] = $this->request->get['master_return_id'];
			$master_return_id =  $this->model_account_return->getLastMasterReturnId($data['master_return_id']);
			$data['heading']  = "Your Return Products";
		}else{
			$data['heading'] = 'Add/Update Product Return';
		}

		$this->load->model('account/customer');
		$getCustomerId   = $this->customer->isLogged();
		$customer_data   = $this->model_account_customer->getCustomer($getCustomerId);

		$delivered_date  = $this->model_account_return->getHistoryTime($order_id,$this->_delivered_state_id);
		$data['delivered_date'] = $delivered_date;
		$fields = array( 'bank_ac_holder_name' , 'bank_ac_number' , 'ifsc_code' );
		$customer_bank_details = $this->model_account_customer->getCustomerDetails( $this->customer->getId() , $fields);
		$data = array_merge($data,$customer_bank_details);
		$return_time_limit = $this->model_account_return->checkReturnTimeLimit($delivered_date);
		$selector = array( 'order'=> array() );
		$result = OrderInfo::getOrderInfo($this->db, $order_id,'',$selector);
		$data['order_no'] = $result['order']['order_no'];

		if(isset($this->request->get['master_return_id'])){
			if($return_time_limit){
			}else{
				$return_time_limit = 1;
			}
		}
		$data['bank_detail_validate_url'] = $this->url->link( 'account/bank_details/userBankDetailValidationProcess','&ctoken='. $data['ctoken'], 'SSL' );
		if($return_time_limit){
			$data['return_review_url'] = $this->url->link('account/return/returnReview', '', 'SSL');
			if(empty($data['master_return_id'])){
				$master_return_id =  $this->model_account_return->checkLastReturn($order_id);
			}
			if(isset($master_return_id)){
				$last_master_return_id      = $master_return_id['master_return_id'];
				$data['shipping_method']    = $master_return_id['shipping_method'];
				$data['check_box_disabled'] = "disabled";
				$this->_master_return_id    = $last_master_return_id;
				$master_return_no           = $master_return_id['return_no'];
				$data['master_return_no']   = $master_return_no;
			}else{
				$data['check_box_disabled'] = "";
			}
			
			if(!empty($this->request->post)){ 

				$this->load->model('restapi/return');
				$this->load->model('account/customer');
				$date_today	     = time();
				$data['add_return_link'] = $this->url->link( 'account/return','&ctoken='. $data['ctoken'] .'&order_id=' . $order_id , 'SSL' );
				$shipping_method = $this->request->post['pickup_method'];
				$order_no        = $this->request->post['order_no'];

				if(!empty($this->request->post['ac_holder_name'])){
					$bank_details['bank_ac_holder_name'] = $this->request->post['ac_holder_name'];
				}
				if(!empty($this->request->post['ac_number'])){
					$bank_details['bank_ac_number'] = $this->request->post['ac_number'];
				}
				if(!empty($this->request->post['ifsc_code']) && $this->request->post['ifsc_error']){
					$bank_details['ifsc_code'] = $this->request->post['ifsc_code'];
				}
				if(!empty($bank_details) && !empty($getCustomerId)){
					$this->model_account_customer->updateCustomerDetails($bank_details,$getCustomerId);
				}

				$tentative_data['product']         = $this->request->post['product'];
				$tentative_data['shipping_method'] = $shipping_method;
				$tentative = $this->getReturnTentativeAmount($tentative_data);
				$log_data = array();
				
				if($tentative['success']){
					$tentative_refund_amount = $tentative['tentative_refund_amount'];

					$log_data['order_id'] = $order_id;
					$log_data['post_products'] = $this->request->post['product'];
					$defected_images=array();

					//Initialize $return_data with empty array, stores row data to add return(s)
					$return_data = array();

					//Filter data for only those order_products for which we have to add returns
					foreach($this->request->post['product'] as $order_product_id => $product){
						if(!empty($product['return_reason_id']) && !empty($product['quantity'])){
							$return_data[$order_product_id] = $product;
						}
					}

					//Merge orderProducts to $data, 
		    		//If any product is missing from combo to add return 
		    		//$return_data = $this->addMissingComboOrderProductsForReturn($return_data);

					//Loop to add Return(s)
					foreach($return_data as $order_product_id => $product){
						if(!empty($product['return_reason_id']) && !empty($product['quantity'])){
							
							if(empty($this->_master_return_id)){
								$master_return_data['customer_id']     = $getCustomerId;
								$master_return_data['shipping_method'] = $shipping_method;
								$master_return_data['order_id'] = $order_id;
								$master_data = $this->model_account_return->addMasterReturn($master_return_data,'front_end');
								if(!empty($master_data)){
									$return_data['master_return_id'] = $master_data['last_master_return_id'];
									$master_return_no         = $master_data['return_no'];
									$this->_master_return_id  = $master_data['last_master_return_id'];
								}
							}else{
								$return_data['master_return_id'] = $this->_master_return_id;
							}

							$return_data['order_product_id'] = $order_product_id;

							if(!empty($product['return_reason_id'])){
								$return_data['return_reason_id'] = $product['return_reason_id'];
							}
							//Get total oper_product's quantity
							$total_qty = $this->model_account_return->getTotalQtyForOrderProduct($order_product_id);

							//Check for return_qty must not be greater then total purchased product qty
							if(!empty($product['quantity']) && $product['quantity'] <= $total_qty){
								$return_data['quantity'] = $product['quantity'];
							}else{
								$return_data['quantity'] = $total_qty;
							}

							if($shipping_method == "self_courier"){
								$max_date_for_upload_courier = strtotime(
																	"+$this->_limit_in_days_for_upload_courier days",
																	 strtotime($date_today)
																);

								$return_show_data[$master_return_no]['upload_courier'] = false;
								$return_show_data[$master_return_no]['max_date_for_upload_courier'] = date('d-m-Y', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));
								$return_data['shipping_method'] = $shipping_method;
							}else{
								$return_data['shipping_method'] = $shipping_method;
							}
							$return_data['comment'] = $product['comment'];
							$return_data['app_version_code'] = 68;
							
							$log_data['return_data'] = $return_data;
							$this->model_restapi_return->writeLog($log_data);
							
							$total_return_quantity += $return_data['quantity'];
							
							$images_path = $this->model_restapi_return->addReturn($return_data,$getCustomerId);
							//Get ReturnId for latest Insertion in oc_return table
							$return_id = $this->model_restapi_return->getAddedReturnId($return_data);
					
							/*
							for pickup address - self courier, add extra return with self_shipment action status
							April 2018 MSA
							*/
							if(isset($this->request->post['pickup_method']) && $shipping_method == "self_courier") {
								//$this->model_restapi_return->resetReturnActiveStatus($this->db->getLastId(),0);
								//Create object of ReturnActionBase class
    							$return_action_base = new ReturnActionBase($this);
								$return_action_base->updateExistingAsInactive($return_id);
								
								$return_data['return_action_id'] = RETURN_ACTION_IDS['Self_Shipment'];
								$this->model_restapi_return->addReturn($return_data,$getCustomerId);
							}
							/* end of self courier */	

							if(isset($images_path)){
								$defected_images[$order_product_id] = $images_path[$order_product_id];
							}
							$return_show_data[$master_return_no]['order_no']          = $order_no;
							$return_show_data[$master_return_no]['master_return_id']  = $this->_master_return_id;
							$return_show_data[$master_return_no]['master_return_no']  = $master_return_no;
							$return_show_data[$master_return_no]['total_quantity']    = $total_return_quantity;							
							$return_show_data[$master_return_no]['show_cancel_button']     = 1;
							$return_show_data[$master_return_no]['view_return_link']  = $this->url->link( 'account/return','&ctoken='. $ctoken .'&order_id=' . $order_id .'&master_return_id='.$this->_master_return_id, 'SSL' );
							$data['success'] = "Your Return Request successfully has been added and your return no # ".$master_return_no;
						}
					}
					$product_data = array();
					$product_data['updated']  = '';
					$product_data['defect_images']  = $defected_images;
					$product_data['new']      = $tentative['data'];
					$product_data['image_width'] = $this->config->get('config_image_additional_width');
					$product_data['image_height'] = $this->config->get('config_image_additional_height');
					$product_data['buyer_name']    = ucfirst($customer_data['firstname']);
					$product_data['buyer_email']   = $customer_data['email'];
					$product_datal['buyer_mobile'] = $customer_data['telephone'];
					$product_data['order_no']      = $order_no;
					$product_data['tentative_refund_amount'] = $tentative_refund_amount;
					$product_data['shipping_method'] = $shipping_method;
					$product_data['comment_message'] = "We have received your return/replacement request for the following items in your order : #";

					$template = MailTemplate::getReturnMailTemplate($product_data);

					$subject  = "Return Request - Order No: ".$order_no."-".Date("d/m/Y");
					$this->model_account_return->sendMailFromApi($product_data,$subject,$template);

					$data['return'] = $return_show_data;
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/customer_return_list.tpl', $data));
				}else{
					$data['error'] = "Return Request Failed Please Enter Valid Return Values";
					$data['order_id']   = $order_id;
					$this->getReturnFormData($data);
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/return_list.tpl', $data));
				}
			}else{
				$this->load->model('tool/image');
             
				$order_product_ids = array();
				$opid_to_pid       = array();
				$products          = array();
				if(!empty($order_id)){
					$data['order_id']   = $order_id;
					$data['order_href'] = $this->url->link('account/order', 'order_id='.$order_id, 'SSL');
					if($this->customer->getId() != $result['order']['customer_id']){
						$this->response->redirect($this->url->link('account/order', '' , 'SSL'));
					}
					$this->getReturnFormData($data);
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/return_list.tpl', $data));
				}
			}
		}else{

			$data['error'] = "Return/Replacement date for this order has been exceed. You can not request for return/replacement for this order. For any clarification, please call on +91 141 4049163";
			$data['all_returns'] = true;
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/return_list.tpl', $data));
		}
	}

	/**
	* Protected method addMissingComboOrderProducts() to add missing combo product return
	* @param  Array $data
	* @return Array $data
	* @author Nishu July 2018
	*/
	/*protected function addMissingComboOrderProductsForReturn($data)
	{
		//All order_product_ids for all returns to be added
		$op_ids = array_keys($data);

		//Create Object for ReturnInfo Class
		$return_info = new ReturnInfo($this);

		//Get all order_product_ids with group over combo_product_id as array key
		$combo_products = $return_info->getComboProductForOrderProductIds($op_ids);

		//Initialize as empty array
		$combo_data = array();

		//Check if there are exist order_products as combo products
		if(!empty($combo_products) && !empty($data)) {

			$data_group_by_combo_product_id = array();

			//Group by combo product id from $data
			foreach ($data as $opid => $d) {
				$seller_invoice_id = $d['seller_invoice_id'];
				if(!isset($data_group_by_combo_product_id[$combo_id][$seller_invoice_id])){
					$combo_id = $d['combo_id'];
					$data_group_by_combo_product_id[$combo_id][$seller_invoice_id] = array($opid);
				}else{
					$data_group_by_combo_product_id[$combo_id][$seller_invoice_id][] = $opid;
				}
			}

			//Loop for all combo products
			foreach ($combo_products as $combo_product_id => $si_wise_associate_op_ids) {
				foreach ($si_wise_associate_op_ids as $seller_invoice_id => $associate_op_ids) {
					$missing_combo_product_from_data = array_diff(
														  explode(',', $associate_op_ids), 
														  $data_group_by_combo_product_id[$combo_product_id][$seller_invoice_id]
														  );
					
					//If any combo order product is missing from $data
					if(!empty($missing_combo_product_from_data)){
						foreach ($missing_combo_product_from_data as $key => $missing_op_id) {
							//Already exist order_product_id wise data in $data
							$op_id_exist_in_data =  $data_group_by_combo_product_id[$combo_product_id][$seller_invoice_id][0];

							//Copy data for missing associated order_product to $data array
							$data[$missing_op_id] = $data[$op_id_exist_in_data];
						}
					}
				}
			}
		}

		//Refactor $data for combo products on basis of qty and reason
        $data = $this->refactorReturnDataForComboProducts($data, $combo_products);
        pr($data);die;
		//Update $data's values 
		return $data;
	}*/

	/**
     * @info: Public method to process combo products to add Return(s)
     *         for Quantity and Return/Replacement Reason
     * @author: Nishu, July 2018
    */
    /*public function refactorReturnDataForComboProducts($data, $combo_products){
        
        if(!empty($data) && !empty($combo_products)){
            //////////////---------Starts data manipulation---------//////////////

            //Return will added for minimum qty in order_products of same combo
            foreach ($combo_products as $combo_id => $si_wise_op_ids) { 
                //si_wise_op_ids means to seller invoice id wise data
                foreach ($si_wise_op_ids as $combo_id => $op_ids) {
                    $op_ids = explode(',', $op_ids);

                    $min_qty = 0;
                    $return_reason = 0;
                    //Loop over order_product_ids combo_wise
                    foreach ($op_ids as $op_id) {
                        if(empty($min_qty) || $min_qty > $data[$op_id]['quantity']){
                            $min_qty         = $data[$op_id]['quantity'];
                            $return_reason   = $data[$op_id]['return_reason_id'];
                        }
                    }
                    
                    //OverWrite return's qty of all products from same combo
                    foreach ($op_ids as $op_id) {
                        $data[$op_id]['quantity']         = $min_qty;
                        $data[$op_id]['return_reason_id'] = $return_reason;
                    }
                }
            }

            //////////////---------Ends data manipulation---------//////////////
        }

        return $data;
    }*/

	public function returnReview(){
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$data['shipping_method'] = $this->request->post['pickup_method'];
			$data['product']	     =  $this->request->post['product'];
			$return = $this->getReturnTentativeAmount($data);
			echo json_encode($return);
			exit;
	   }
	}

	public function getReturnTentativeAmount($data){
		if(!empty($data)){
			$this->load->model('restapi/return');
			$shipping_method = $data['shipping_method'];
			$total_refund_quanity = 0;
			$total_weight = 0;
			$refund_amount = 0;
			$_quality_return_reason_id = array( 
    										RETURN_REASON_IDS['Quality_Issue'], 
    										RETURN_REASON_IDS['Pricing_Issue'], 
    										RETURN_REASON_IDS['Wrong_Item_Received'] 
    									);
			$_return_reason_text = array(
                                    RETURN_REASON_IDS['Manufacturing_Defect'] => "Manufacturing defect/damaged goods",
                                    RETURN_REASON_IDS['Quality_Issue']        => "Quality issue",
                                    RETURN_REASON_IDS['Pricing_Issue']        => "Pricing issue",
                                    RETURN_REASON_IDS['Wrong_Item_Received']  => "Worng Itmes Recevied",
                                    RETURN_REASON_IDS['Wrong_Item_Received_Replacement'] => "Wrong Item Received(Replacement)"
                                        );
			foreach($data['product'] as $order_product_id => $product){
					if(!empty($product['return_reason_id']) && !empty($product['quantity'])){
						if(in_array($product['return_reason_id'],$_quality_return_reason_id)){
							$refund_params = $this->model_restapi_return->getRefundParameters($order_product_id);
							if(!empty($refund_params)){
								$price_per_piece    = $refund_params->row['price_per_piece'];
								$discount_per_piece = $refund_params->row['discount_per_piece'];
								$weight_per_piece   = $refund_params->row['weight_per_piece'];
								$output_tax_rates   = $refund_params->row['output_tax_rates'];

								$refund = ( ( (float)$price_per_piece +  (float)$discount_per_piece ) *
												( 1 + (float)$output_tax_rates / 100 ) * (int)$product['quantity'] );

								$total_weight += (float)$weight_per_piece * (int)$product['quantity'];
							}else{
								$refund       = 0;
								$total_weight = 0;
							}

							$product['refund_amount']  =  $this->currency->format($refund,'INR');
							$product['return_type']    = 'Return';

							$refund_amount += $refund;
                		}else{
							$product['return_type']   = 'Replacement';
							$product['refund_amount'] = "Rs. 0.00";
						}
						$product['return_reason_name'] = $_return_reason_text[$product['return_reason_id']];

						$total_refund_quanity += $product['quantity'];
                
						$return_review['data'][$order_product_id] = $product;
				   }

		    }
			if(!empty($total_refund_quanity)){
				if($shipping_method == 'wsb_pickup'){
                	$total_weight = ceil($total_weight);
					if( $total_weight >= 1 ){
						$shipping_charge = ($total_weight - 1) * 30 + 70;
					}else{
						$shipping_charge = "Rs. 0.00";
					}
				}else{
					$shipping_charge = 0;
				}
				if($refund_amount != 0){
					$refund_amount = (float)$refund_amount - (float)$shipping_charge;
				}else{
					$refund_amount = 0;
				}

				$return_review['tentative_refund_amount'] = $this->currency->format($refund_amount,'INR');
				$return_review['shipping_adjustment_ammount'] = $shipping_charge;
				$return_review['success'] = true;
				$return_review['total_refund_quanity'] = $total_refund_quanity;
			}else{
				$return_review['success'] = false;
			}

			return $return_review;
		}
	}

    private function getReturnFormData(&$data){
		$date_today	     = time();
		if(isset($data['master_return_id'])){
			$master_return_id = $data['master_return_id'];
			if($this->request->get['order_id']){
				$order_id = $this->request->get['order_id'];
			}else{
				$order_id = '';
			}
			$credit_note = $this->model_account_return->findCreditNote($order_id,'');
			if(!empty($credit_note)){
				$result_credit = $this->model_account_return->crediteNote($credit_note);
				$data['credit_note'] = $result_credit;
			}else{
				$data['credit_note'] = '';
			}
		}else{
			$master_return_id = '';
		}
		$this->load->model('tool/image');
   	    $order_id = $data['order_id'];
   	    $data['quality_return'] = array( 
    										RETURN_REASON_IDS['Quality_Issue'], 
    										RETURN_REASON_IDS['Pricing_Issue'], 
    										RETURN_REASON_IDS['Wrong_Item_Received'] 
    									);
		     
	    $data['replacement_return']  = array(
				                             RETURN_REASON_IDS['Manufacturing_Defect'],
						                     RETURN_REASON_IDS['Wrong_Item_Received_Replacement']
						                    );
        
        $selector = array(
			'suborder'=> array(),
            'order_product' => array(
                'sort' => array( 'order_product_id' => 'ASC' )
            ),
        );
		$suborder_to_products = OrderInfo::getOrderInfo( $this->db , $data['order_id'] , '' , $selector );
		$uborders = array_keys($suborder_to_products['suborder']);
		$suborders_delivery = $this->model_account_return->getAllSuborderDeliveryDate($uborders,$order_id);
		$suborder_ids = array_column($suborders_delivery,'suborder_id');
		$suborders_delivery_date = array_combine($suborder_ids,$suborders_delivery);
        $suborder_to_products = $suborder_to_products['suborder'];
         foreach ( $suborder_to_products as $suborder_id => $sub_products) {
			 if(isset($suborders_delivery_date[$suborder_id])){
				 $suborder_delivery_date = $suborders_delivery_date[$suborder_id]['date_added'];
			 	$check_return_limit = $this->model_account_return->checkReturnTimeLimit($suborder_delivery_date);
			 }else{
				 $check_return_limit = 0;
			 }
			 if($check_return_limit){

			 	$shipping_code = strtolower($sub_products['shipping_code']);
				foreach ( $sub_products['order_product'] as $key => $row) {

					if($sub_products['buyer_invoice_id'] == $row['buyer_invoice_id']){
						$order_product_ids[] = $row['order_product_id'];

						$opid_to_pid[$row['order_product_id']] = $row['product_id'];
						// mapping the product in $products array like ('product_id' => array(details))
						$products[$row['order_product_id']] = $row;
						$products[$row['order_product_id']]['price_per_piece_with_currency'] = $this->currency->format($row['price_per_piece']);

						$max_date_for_quality_issue = strtotime(
													"+$this->_limit_in_days_for_quality days",
													strtotime($suborder_delivery_date)
													);

						if(
							$date_today <= $max_date_for_quality_issue
								&&
							!in_array($shipping_code, array("store_pickup","warehouse_pickup","weight.weight_0"))
						){
							$products[$row['order_product_id']]['non_returnable'] = 0;
							$products[$row['order_product_id']]['quality_issue']  = 1;
						}												
						//$check_product = $this->model_account_return->checkProductIsReturnable($row['seller_id']);
						if(empty($row['is_returnable'])){
							$products[$row['order_product_id']]['non_returnable'] = 1;
						}
					}
				}
			 }
		 }
		 $images = $this->model_catalog_product->getProductImagesByProductsIds($opid_to_pid);

         $data['pid_to_imgs'] = array();
         foreach ( $images as $row) {
             $data['pid_to_imgs'][$row['product_id']] = $this->model_tool_image->resize(
                                                                                 $row['image'],
                                                                                 $this->config->get('config_image_additional_width'),
                                                                                 $this->config->get('config_image_additional_height')
                                                                             );
			$data['hrefs'][$row['product_id']]  = $this->url->link('product/product', 'product_id=' . $row['product_id'] , 'SSL');

         }

        if(!empty($products)){
            $data['products'] = $products;
        }

        // Getting return reason
		$reason = $this->model_account_return->getReturnReason();
		$data['return_reasons'] = array_combine(
											array_column($reason,'return_reason_id'),
											array_column($reason,'name')
									  );

        // $result = $this->model_account_return->getReturnAction();
        // if($result){
        //     $data['return_actions'] = array_combine(
        //         array_column($result,'return_action_id'),
        //         array_column($result,'name')
        //     );
        // }

        // Getting the product which are also in return table
        $returns         = array();
        // Getting Return Details On basis of order_product_id
        if(!empty($order_product_ids)){

            $result = $this->model_account_return->getReturnDetailsByProduct_ids($order_product_ids);

            if(!empty($result)){
				$returns = $this->model_account_return->transformReturn($result,$master_return_id);	
				if(isset($returns['total_quantity'])){
					$total_quantity = "true";
					unset($returns['total_quantity']);
				}
				if(!empty($master_return_id)){
					$tentative_amount_for_view = $returns['return_tentative_data'];
					$tentative_amount_for_view['shipping_method'] = $data['shipping_method'];
					$get_tentative = $this->getReturnTentativeAmount($tentative_amount_for_view);
					$data['tentative_refund_amount']     = $get_tentative['tentative_refund_amount'];
					$data['shipping_adjustment_ammount'] = $get_tentative['shipping_adjustment_ammount'];
					$data['return_total_quantity']        = $get_tentative['total_refund_quanity'];
					//array_shift($returns);
				}else{
					if(!isset($total_quantity)){
						if(count($order_product_ids) == count($returns)){
							$data['all_returns'] = "true";
						}
					}
				}
            }else{
				$returns[] = '';
			}
        }
        if(!empty($returns)){
            $data['returns'] = $returns;
        }
    }

    public function courierDetails(){
        if($this->request->post){
            $this->load->model('account/return');
            $this->load->model('restapi/return');
            $data["tracking_no"] = $this->request->post['tracking_no'];
            $data["courier_company"] = $this->request->post['shipping_company'];
            $data["master_return_id"] = $this->request->post['master_return_id'];
            $data["order_no"] = $this->request->post['order_no'];
            $data["warehouse_id"] = (!empty($this->request->post['warehouse_id']))?$this->request->post['warehouse_id']:1;
            //$image_name   = $_FILES["upload_file"]["name"];
            //$tmpName      = $_FILES["upload_file"]["tmp_name"];
            $image_name  	= $_FILES["shipment_slip"]["name"];
            $tmpName        = $_FILES["shipment_slip"]["tmp_name"];
            $currentDate    = date("Y-m-d");
            $data['shipping_slip'] = $currentDate.$image_name;
            $targetPath = DIR_UPLOAD.$data['shipping_slip'];
            //move_uploaded_file($tmpName,$targetPath);
            $result = $this->model_account_return->courierDetails($data);
            /*upload shipping slip on CDN*/
            $this->model_restapi_return->uploadReturnCourierSlippingSlip($data['tracking_no'],$data['shipping_slip']);
            $result = json_encode($result,true);
            echo $result;
            exit;
        }
    }

	public function getReturns(){
		$this->load->model('tool/image'); 
		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}		

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


		if(isset($this->request->get['order_id'])){
			$order_id = $this->request->get['order_id'];
		}else{
			$order_id = '';
		}
		if(isset($this->request->get['ctoken'])){
			$ctoken = $this->request->get['ctoken'];
			$data['ctoken'] = $ctoken;
		}else{
			$ctoken = '';
		}
		$this->load->language('account/return');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
	    $data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$getCustomerId = $this->customer->isLogged();
		$this->load->model('restapi/return');
		$this->load->model('account/return');
		$master_returns = $this->model_restapi_return->getMasterReturns((int)$getCustomerId,(int)$order_id);
		$order_delivered = $this->model_account_return->getHistoryTime( (int)$order_id , $this->_delivered_state_id);
		$date_today	 = time();
		if(!empty($order_id)){
			if(!empty($order_delivered)){
				$show_add_button = $this->model_account_return->checkReturnTimeLimit($order_delivered);
				if($show_add_button){
				}else{
					$data['show_add_button'] = $show_add_button;
					$data['error'] = "Return/Replacement date for this order has been exceed. You can not request for return/replacement for this order. For any clarification, please call on +91 141 4049163";
				}
			}
		}else{
			$data['show_add_button'] = 0;
		}

		if($master_returns->num_rows){
			$master_return_ids = array_column($master_returns->rows,'master_return_id');
			$master_return_id  = array_combine($master_return_ids,$master_returns->rows);
			$result = $this->model_account_return->getReturnsByOrderId($master_return_ids,$order_id);		
			if(!empty($result)){
				$chk_order_id = 0;
				$chk_master_return_id = 0;
				$index = 0;
				foreach($result->rows as $return){

					if(!isset($check[$return['order_product_id']]['status'])){
						$check[$return['order_product_id']]['status'] = 1;
					}

					if(!empty($return['order_no'])){
						$order_no = $return['order_no'];
					}else{
						if(isset($master_return_id[$return['master_return_id']]['order_no'])){
							$order_no = $master_return_id[$return['master_return_id']]['order_no'];
							$return['order_id'] = $order_id;
						}
					}
					$master_return_no = $master_return_id[$return['master_return_id']]['return_no'];
					if($return['order_id'] <> $chk_order_id || $return['master_return_id'] <> $chk_master_return_id)
					{
						$index++;
						$chk_order_id         = $return['order_id'];
						$chk_master_return_id = $return['master_return_id'];
						
					}
					if(empty($check_master[$return['master_return_id']][$return['order_product_id']])){
						$check_master[$return['master_return_id']][$return['order_product_id']] = "not empty";
						$return_quantity = $this->model_account_return->getQuantityMasterReturn($return['master_return_id'],$return['order_product_id'],'show');
					}else{
						$return_quantity = '';
					}
					if(!empty($return_quantity)){
						if(!isset($return_data[$index]['total_quantity'])){
							$return_data[$index]['total_quantity'] = 0;
						}
						$return_actions_not_for_cancel_btn = array(
																//2, 5,
																RETURN_ACTION_IDS['Old_Returned_Goods_Received'],
																RETURN_ACTION_IDS['Old_Replacement_Sent'],
																RETURN_ACTION_IDS['Return_Goods_Rejected'],
																RETURN_ACTION_IDS['Return_Request_Rejected'],
																RETURN_ACTION_IDS['Replacement_Request_Rejected'],
																RETURN_ACTION_IDS['CN_For_Client'],
																RETURN_ACTION_IDS['DN_Generated_For_Seller'],
																RETURN_ACTION_IDS['Shipment_Lost_by_Courier'],
																RETURN_ACTION_IDS['Shipment_Lost_by_Courier_While_Resending_Back'],
																RETURN_ACTION_IDS['Goods_Received'],
																RETURN_ACTION_IDS['Extra_Goods_Received'],
																RETURN_ACTION_IDS['Short_Goods_Received']
															);
						if(in_array($return_quantity['return_action_id'], $return_actions_not_for_cancel_btn)){
							if(!isset($order_product_quantity[$index][$return['order_product_id']]['quantity'])){
								$order_product_quantity[$index][$return['order_product_id']]['quantity'] = "true"; 
							}
							if($order_product_quantity[$index][$return['order_product_id']]['quantity'] == "true"){
								$return_data[$index]['total_quantity'] += $return_quantity['quantity'];	
								$order_product_quantity[$index][$return['order_product_id']]['quantity'] = "false";
							}
							$return_data[$index]['upload_courier'] = 0;	
							$return_data[$index]['show_cancel_button'] = 0;	
						}else{
							if(
								$return_quantity['return_action_id'] == 0 
										|| 
								$return_quantity['return_action_id'] == RETURN_ACTION_IDS['Pending']
							){
								$return_data[$index]['show_cancel_button'] = 1;
							}
							$return_data[$index]['total_quantity'] += $return_quantity['quantity'];
						}
						if(!isset($return_data[$index]['total_quantity'])){
							$return_data[$index]['total_quantity'] = 0;
							//$order_product_quantity[$return['master_return_id']][$return['order_product_id']]['quantity'] = "true";
						}
						$return_data[$index]['master_return_no'] = $master_return_no;
						$shipping_method = $master_return_id[$return['master_return_id']]['shipping_method'];
						$return_data[$index]['order_no'] = $order_no;
						if($shipping_method == 'self_courier' && empty($master_return_id[$return['master_return_id']]['return_shipment_tracking_id'])){
							$return_date_added = $master_return_id[$return['master_return_id']]['date_added'];

							$max_date_for_upload_courier = strtotime(
															"+$this->_limit_in_days_for_upload_courier days",
															strtotime($return_date_added)
														);
							if($date_today <= $max_date_for_upload_courier 
								&&
								in_array($return['return_action_id'],array(
											RETURN_ACTION_IDS['Pending'],
											RETURN_ACTION_IDS['Return_Request_Accepted'],
											RETURN_ACTION_IDS['Self_Shipment']
											)
										)
								)

							$return_data[$index]['upload_courier'] = $date_today <= $max_date_for_upload_courier;
							
							$return_data[$index]['max_date_for_upload_courier'] =  date("d-m-Y",$max_date_for_upload_courier);
						}
						$return_data[$index]['master_return_id'] = $return['master_return_id'];
						$return_data[$index]['view_return_link'] = $this->url->link( 'account/return','&ctoken='. $ctoken .'&order_id=' .$return['order_id'] .'&master_return_id='.$return['master_return_id'], 'SSL' );
					}
				}
				$data['add_return_link'] = $this->url->link( 'account/return','&ctoken='. $ctoken .'&order_id=' . $order_id , 'SSL' );
				$data['return'] = $return_data;
			}else{
				$data['add_return_link'] = $this->url->link( 'account/return','&ctoken='. $ctoken .'&order_id=' . $order_id , 'SSL' );
			}
		}else{
			$data['add_return_link'] = $this->url->link( 'account/return','&ctoken='. $ctoken .'&order_id=' . $order_id , 'SSL' );
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_my_orders'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);
                
                //get chatbox language
                $data['text_chatbox_message'] = $this->language->get('text_chatbox_message');
                $data['text_whatsApp'] = $this->language->get('text_whatsApp');
                $data['text_chat_on_whatsappw_web'] = $this->language->get('text_chat_on_whatsappw_web');
                $data['text_whatsappw_no'] = $this->language->get('text_whatsappw_no');
                $data['text_whatsappw_no_msg_text'] = $this->language->get('text_whatsappw_no_msg_text');
                $data['text_callus'] = $this->language->get('text_callus');
                $data['text_callus_no'] = $this->language->get('text_callus_no');
                $data['text_call_back_request'] = $this->language->get('text_call_back_request');
                $data['text_call_back_request_messgae'] = $this->language->get('text_call_back_request_messgae');
                $data['text_chat_here'] = $this->language->get('text_chat_here');
                $data['text_error_mobile_no'] = $this->language->get('text_error_mobile_no');
                $data['text_error_mobile_not_start_zero'] = $this->language->get('text_error_mobile_not_start_zero');
                
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/customer_return_list.tpl', $data));
	}

	public function getCreditNote(){
		$this->load->model('tool/image'); 
        if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}		

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		$credit_note = '';
		$this->load->model('account/return');
        
        $this->document->setTitle($this->language->get('heading_title'));

		$data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
	    $data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$customer_id = $this->customer->isLogged();
		$result = $this->model_account_return->findCreditNote('',$customer_id);
		if(!empty($result)){
			$credit_note = $this->model_account_return->crediteNote($result);
		}
		$data['credit_note'] =  $credit_note;
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => "Credit Note",
			'href' => $this->url->link('account/return/getCreditNote', '', 'SSL')
		);
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		if(CONFIG_IS_MOBILE == 1)
        {
		  $data['footer'] = $this->load->controller('common/footer');
		  $data['header'] = $this->load->controller('common/header');
		}
                
                //get chatbox language
                $data['text_chatbox_message'] = $this->language->get('text_chatbox_message');
                $data['text_whatsApp'] = $this->language->get('text_whatsApp');
                $data['text_chat_on_whatsappw_web'] = $this->language->get('text_chat_on_whatsappw_web');
                $data['text_whatsappw_no'] = $this->language->get('text_whatsappw_no');
                $data['text_whatsappw_no_msg_text'] = $this->language->get('text_whatsappw_no_msg_text');
                $data['text_callus'] = $this->language->get('text_callus');
                $data['text_callus_no'] = $this->language->get('text_callus_no');
                $data['text_call_back_request'] = $this->language->get('text_call_back_request');
                $data['text_call_back_request_messgae'] = $this->language->get('text_call_back_request_messgae');
                $data['text_chat_here'] = $this->language->get('text_chat_here');
                
                
                
		$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/credit_note.tpl', $data));
		
	}


	public function info() {
		$this->load->language('account/return');

		if (isset($this->request->get['return_id'])) {
			$return_id = $this->request->get['return_id'];
		} else {
			$return_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $return_id, 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->model('account/return');
        $this->load->model('checkout/order');
        
		$return_info = $this->model_account_return->getReturn($return_id);

		if ($return_info) {
			$this->document->setTitle($this->language->get('text_return'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', '', 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL')
			);

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/return', $url, 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_return'),
				'href' => $this->url->link('account/return/info', 'return_id=' . $this->request->get['return_id'] . $url, 'SSL')
			);

			$data['heading_title'] = $this->language->get('text_return');

			$data['text_return_detail'] = $this->language->get('text_return_detail');
			$data['text_return_id'] = $this->language->get('text_return_id');
			$data['text_order_id'] = $this->language->get('text_order_id');
            $data['text_order_no'] = $this->language->get('text_order_no');
			$data['text_date_ordered'] = $this->language->get('text_date_ordered');
			$data['text_customer'] = $this->language->get('text_customer');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			$data['text_status'] = $this->language->get('text_status');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_product'] = $this->language->get('text_product');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['text_history'] = $this->language->get('text_history');

			$data['column_product'] = $this->language->get('column_product');
			$data['column_model'] = $this->language->get('column_model');
			$data['column_quantity'] = $this->language->get('column_quantity');
			$data['column_opened'] = $this->language->get('column_opened');
			$data['column_reason'] = $this->language->get('column_reason');
			$data['column_action'] = $this->language->get('column_action');
			$data['column_date_added'] = $this->language->get('column_date_added');
			$data['column_status'] = $this->language->get('column_status');
			$data['column_comment'] = $this->language->get('column_comment');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['return_id'] = $return_info['return_id'];
			$data['order_id'] = $return_info['order_id'];
            $data['order_no'] = OrderInfo::getOrderNo($this->db, $return_info['order_id']);
			$data['date_ordered'] = date($this->language->get('date_format_short'), strtotime($return_info['date_ordered']));
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_added']));
			$data['firstname'] = $return_info['firstname'];
			$data['lastname'] = $return_info['lastname'];
			$data['email'] = $return_info['email'];
			$data['telephone'] = $return_info['telephone'];
			$data['product'] = $return_info['product'];
			$data['model'] = $return_info['model'];
			$data['quantity'] = $return_info['quantity'];
			$data['reason'] = $return_info['reason'];
			$data['opened'] = $return_info['opened'] ? $this->language->get('text_yes') : $this->language->get('text_no');
			$data['comment'] = nl2br($return_info['comment']);
			$data['action'] = $return_info['action'];

			$data['histories'] = array();

			$results = $this->model_account_return->getReturnHistories($this->request->get['return_id']);

			foreach ($results as $result) {
				$data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'comment'    => nl2br($result['comment'])
				);
			}

			$data['continue'] = $this->url->link('account/return', $url, 'SSL');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/return_info.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/return_info.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/account/return_info.tpl', $data));
			}
		} else {
			$this->document->setTitle($this->language->get('text_return'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/return', '', 'SSL')
			);

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_return'),
				'href' => $this->url->link('account/return/info', 'return_id=' . $return_id . $url, 'SSL')
			);

			$data['heading_title'] = $this->language->get('text_return');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('account/return', '', 'SSL');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			}
		}
	}

	public function add() {
		$this->load->language('account/return');

		$this->load->model('account/return');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$return_id = $this->model_account_return->addReturn($this->request->post);

			//If return is added successfully
			if(!empty($return_id)){
				$this->response->redirect($this->url->link('account/return/success', '', 'SSL'));
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/return/add', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_description'] = $this->language->get('text_description');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_product'] = $this->language->get('text_product');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_order_id'] = $this->language->get('entry_order_id');
		$data['entry_date_ordered'] = $this->language->get('entry_date_ordered');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_reason'] = $this->language->get('entry_reason');
		$data['entry_opened'] = $this->language->get('entry_opened');
		$data['entry_fault_detail'] = $this->language->get('entry_fault_detail');

		$data['button_submit'] = $this->language->get('button_submit');
		$data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['order_id'])) {
			$data['error_order_id'] = $this->error['order_id'];
		} else {
			$data['error_order_id'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['reason'])) {
			$data['error_reason'] = $this->error['reason'];
		} else {
			$data['error_reason'] = '';
		}

		if (isset($this->error['captcha'])) {
			$data['error_captcha'] = $this->error['captcha'];
		} else {
			$data['error_captcha'] = '';
		}

		$data['action'] = $this->url->link('account/return/add', '', 'SSL');

		$this->load->model('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_account_order->getOrder($this->request->get['order_id']);
		}

		$this->load->model('catalog/product');

		if (isset($this->request->get['product_id'])) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		if (isset($this->request->post['order_id'])) {
			$data['order_id'] = $this->request->post['order_id'];
		} elseif (!empty($order_info)) {
			$data['order_id'] = $order_info['order_id'];
		} else {
			$data['order_id'] = '';
		}

        if (isset($this->request->post['order_no'])) {
            $data['order_no'] = $this->request->post['order_no'];
        } elseif (!empty($order_info)) {
            $data['order_no'] = $order_info['order_no'];
        } else {
            $data['order_no'] = '';
        }

		if (isset($this->request->post['date_ordered'])) {
			$data['date_ordered'] = $this->request->post['date_ordered'];
		} elseif (!empty($order_info)) {
			$data['date_ordered'] = date('Y-m-d', strtotime($order_info['date_added']));
		} else {
			$data['date_ordered'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($order_info)) {
			$data['firstname'] = $order_info['firstname'];
		} else {
			$data['firstname'] = $this->customer->getFirstName();
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($order_info)) {
			$data['lastname'] = $order_info['lastname'];
		} else {
			$data['lastname'] = $this->customer->getLastName();
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($order_info)) {
			$data['email'] = $order_info['email'];
		} else {
			$data['email'] = $this->customer->getEmail();
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($order_info)) {
			$data['telephone'] = $order_info['telephone'];
		} else {
			$data['telephone'] = $this->customer->getTelephone();
		}

		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (!empty($product_info)) {
			$data['product'] = $product_info['name'];
		} else {
			$data['product'] = '';
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['opened'])) {
			$data['opened'] = $this->request->post['opened'];
		} else {
			$data['opened'] = false;
		}

		if (isset($this->request->post['return_reason_id'])) {
			$data['return_reason_id'] = $this->request->post['return_reason_id'];
		} else {
			$data['return_reason_id'] = '';
		}

		$this->load->model('localisation/return_reason');

		$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();

		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} else {
			$data['comment'] = '';
		}

		if ($this->config->get('config_google_captcha_status')) {
			$this->document->addScript('https://www.google.com/recaptcha/api.js');

			$data['site_key'] = $this->config->get('config_google_captcha_public');
		} else {
			$data['site_key'] = '';
		}

		if ($this->config->get('config_return_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_return_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}

		$data['back'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/return_form.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/return_form.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/return_form.tpl', $data));
		}
	}

	public function success() {
		$this->load->language('account/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/return', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_message'] = $this->language->get('text_message');

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
		}
	}

	protected function validate() {
		if (!$this->request->post['order_id']) {
			$this->error['order_id'] = $this->language->get('error_order_id');
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
			$this->error['product'] = $this->language->get('error_product');
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if (empty($this->request->post['return_reason_id'])) {
			$this->error['reason'] = $this->language->get('error_reason');
		}

		if ($this->config->get('config_google_captcha_status')) {
			$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->config->get('config_google_captcha_secret')) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->getIpAddress);

			$recaptcha = json_decode($recaptcha, true);

			if (!$recaptcha['success']) {
				$this->error['captcha'] = $this->language->get('error_captcha');
			}
		}

		if ($this->config->get('config_return_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		return !$this->error;
	}
	private function getReturnCases($delivered_date){

		$case = array();
        $delivered_date = date("Y-m-d 00:00:00", strtotime($delivered_date));
		// Getting Max Date For return request with quality issue used for showing quality reason options
		$max_date_for_quality = strtotime(
				"+$this->_limit_in_days_for_quality days",
				strtotime($delivered_date));

		// Getting Max Date For return request with replacement issue used for showing replacement reason options
		$max_date_for_replacement = strtotime(
				"+$this->_limit_in_days_for_replacement days",
				strtotime($delivered_date)
			);

		//exit($max_date_for_replacement_issue);
		$date_today	= time();
	    $case['quality']     = $date_today <= $max_date_for_quality;

		$case['replacement'] = $date_today <= $max_date_for_replacement;

		$case["max_date_for_quality"]     = $max_date_for_quality;
		$case["max_date_for_replacement"] = $max_date_for_replacement;
		return $case;
	}

    public function getZone($country_id) {
		if (isset($country_id) && !empty($country_id)){
			$this->load->model('localisation/zone');
			$result = $this->model_localisation_zone->getZonesByCountryId($country_id);
			return $result;
		}
	}

    public function cancelReturn(){
        if($this->request->post){
            $master_return_id = $this->request->post['master_return_id'];
			$master_return_no = $this->request->post['master_return_no'];
			$order_no         = $this->request->post['order_no'];
            $this->load->model('restapi/return');
			$this->load->model('tool/image');
			$this->load->model('catalog/product');
			$this->load->model('account/customer');
			$this->load->model('account/return');
			$getCustomerId   = $this->customer->isLogged();
			$customer_data   = $this->model_account_customer->getCustomer($getCustomerId);
			$result = $this->model_account_return->cancelReturn($master_return_id);
			$returns = $this->model_restapi_return->getReturns($master_return_id);
			$order_product_ids = array_column($returns,'order_product_id');
			$product_details = $this->model_account_return->getProductDetails($order_product_ids);
			$order_product_details = array_column($product_details,'order_product_id');
			$order_product_details = array_combine($order_product_details,$product_details);
			$product_ids = array_column($product_details,'product_id');
			$product_image = $this->model_catalog_product->getProductImagesByProductsIds($product_ids);
			if(isset($product_image) && !empty($product_image)){
				$product_images = array_column($product_image,'product_id');
				$product_images = array_combine($product_images,$product_image);
			}

			$_return_reason_text = array(
                                         RETURN_REASON_IDS['Manufacturing_Defect'] => "Manufacturing defect/damaged goods",
                                         RETURN_REASON_IDS['Quality_Issue']        => "Quality issue",
                                         RETURN_REASON_IDS['Pricing_Issue']        => "Pricing issue",
                                         RETURN_REASON_IDS['Wrong_Item_Received']  => "Worng Itmes Recevied",
                                         RETURN_REASON_IDS['Wrong_Item_Received_Replacement'] => "Wrong Item Received(Replacement)"
                                        );

			$i = 0;
            foreach($returns as $values){
				$product_id = $order_product_details[$values['order_product_id']]['product_id'];
				$image = $product_images[$product_id]['image'];
				$image = $this->model_tool_image->resize(
												$image,
												$this->config->get('config_image_additional_width'),
												$this->config->get('config_image_additional_height')
											);
				$image_width  = $this->config->get('config_image_additional_width');
				$image_height = $this->config->get('config_image_additional_height');							
				$return_mail_array[$i]['image'] = $image;
				$return_mail_array[$i]['quantity'] = $values['return_quantity'];
				$return_mail_array[$i]['return_reason_name']   = $_return_reason_text[$values['return_reason_id']];
				$return_mail_array[$i]['return_type']     = "Canceled";
				$return_mail_array[$i]['comment'] = $values['comment'];
				$return_mail_array[$i]['model'] = $order_product_details[$values['order_product_id']]['model'];
				$i++;
			}
			$product_data['updated']  = '';
			$product_data['new']      = $return_mail_array;
			$product_data['buyer_name']    = ucfirst($customer_data['firstname']);
			$product_data['buyer_email']   = $customer_data['email'];
			$product_datal['buyer_mobile'] = $customer_data['telephone'];
			$product_data['master_return_no'] = $master_return_no;
			$product_data['comment_message'] = "We have canceled your return request for the following items in your return : #";
			$product_data['image_width']  = $this->config->get('config_image_additional_width');
			$product_data['image_height'] = $this->config->get('config_image_additional_height');
			$template = MailTemplate::getReturnMailTemplate($product_data);
			$subject  = "Return Request Canceled - Order No: ".$order_no."-".Date("d/m/Y");
			$this->model_account_return->sendMailFromApi($product_data['buyer_email'],$subject,$template);
			echo true;
			exit;
        }
    }
}