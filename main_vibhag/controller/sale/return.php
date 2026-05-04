<?php
include_once DIR_SYSTEM . '../rabbitmq/task_directive_constants.php';

require_once(DIR_SYSTEM.'library/nuvoExAPI.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ControllerSaleReturn extends Controller {

	private $_shipping_methods	= array(
										'self_courier' => 'Self Courier',
										'wsb_pickup' => 'Wsb Pickup',
										'not_decided' => 'Not Decided'
									);
	
	private $_trxn_pay_initiated_status = array(
                                                "NOT_DONE", 
                                                "NOT_APPLICABLE",
                                                "FAILURE",
                                                "BANK_FAILURE"
                                            );

	private $_is_admin                          = false;
	private $_return_action_objs                = array();
	private $_action_emailer                    = null;
	
	/**
	 *@info: Public function to load return form for specific Order
	 *@author: Nishu, 15th Feb 2018
	*/
	public function return_form(){
 
		$data = array();

		//Set OrderId
		$data['order_id'] = 0;
		$data['order_no'] = 0;
		if(!empty($this->request->get['order_id'])){

			//Assign orderId to order_id key
			$data['order_id'] = $order_id = $this->request->get['order_id'];

			$this->load->model('sale/order');
			$this->load->model('sale/return');
			$this->load->model('sellers/sellers');

			$this->load->autoLoadLanguage('sale/return_order', $data);

			$this->document->setTitle($this->language->get('heading_title'));

			//Define array with keys
			$this->initializeData($data);

			//Get All Seller Invoices for that specific OrderId
			//$this->setAllSellerInvoices($data);

			$this->checkAndSetAdminMode($data);

			//Set Basic Data arrays
        	$this->setBasicData($data);

        	//Set Order and All Product details to $data array
        	$this->setOrderAndProductDetails($data);

        	//Fetch All Sellers by order_id
        	$this->setAllSellersByOrderId($data);

        	//Set and Process All returns for that order_id
        	$this->setReturns($data);
        	
        	$this->setCurrentReturnActionsAndTabs($data);

        	$this->setAllActionClassObject($data);

        	$this->checkReturnIsEditable($data);

        	$this->setCreditNoteData($data);

        	$this->setCustomerCODSecurityBalance($data);

        	$this->setDebitNoteData($data);

        	$this->setReplacementNoteData($data);

        	$this->setReverseShipments($data);

        	$this->getShipmentsBackToCustomer($data);
			
			//Get and Set all return action tabs permission wise with return action lists
        	$this->getReturnActionTabNames($data);

        	$this->setAvailablePostReturnActions($data);

        	$this->setActionTabTpls($data);

        	//Set basic page structue with header and footer
        	$this->setBasicTemplateData($data);

            //Set .tpl file and url links
			$this->setTplFiles($data);

			//Load main tpl
			$this->response->setOutput($this->load->view('sale/return_order_form.tpl', $data));
		}
    }//End of method return_form()

	/**
	 * @info: Initialize $data array with keys
	 * @author: Nishu, 27th Feb 2018
	*/
	public function initializeData(&$data){
		$data['is_admin']                 = false;
		$data['admin_mode']               = 'off';

		$data['token']                    = '';
		$data['order']                    = array();
		$data['products']                 = array();
		$data['oop_options']              = array();
		$data['suborder_ids']             = array();
		$data['suborder']                 = array();
		$data['customer_id']              = 0;
		$data['customer']                 = array();
		$data['returns']                  = array();
		$data['return_history']           = array();
		$data['master_returns']           = array();
		$data['return_reasons']           = array();
		$data['action_reasons']           = array();
		$data['return_actions']           = array();
		$data['returns_for_custom_dn']    = array();
		$data['debit_notes']              = array();
		$data['custom_debit_notes']       = array();
		$data['cancelled_debit_notes']    = array();
		$data['all_debit_notes']          = array();
		$data['cn_wise_returns']          = array();
		$data['returns_for_cn']           = array();
		$data['credit_notes']             = array();
		$data['all_credit_notes']         = array();
		$data['cancelled_cn']             = array();
		$data['sellers']                  = array();
		$data['reverse_shipments']        = array();
		$data['Warehouses']               = array();
		$data['custom_parties']           = array();
		$data['countries']                = array();
		$data['all_seller_invoices']      = array();
		$data['all_tabs']                 = array();
		$data['tab_all_return'] 	      = '';
		$data['tab_return_action'] 	      = '';
		$data['tab_credit_note']          = '';
		$data['tab_bank_details']         = '';
		$data['tab_add_pickup_address']   = '';
		$data['tab_reverse_shipment_add'] = '';
		$data['tab_reverse_shipment_status']      = '';
		$data['current_return_actions_tab_wise']  = array();
		$data['available_action_tabs']            = array();
		$data['all_avaiable_post_actions']        = array();
		$data['all_current_return_actions']       = array();
		$data['tab_return_actions']               = array();
		$data['replacement_notes']        		  = array();
		$data['all_replacement_notes']        	  = array();

		//Set Session Token if it is set
		if(isset($this->session->data['token'])){
			$data['token'] = $this->session->data['token'];
		}
	}

	public function checkAndSetAdminMode(&$data){
		//Check for logged in user is admin or not
		$admin_ids        = explode(',', ADMIN_IDS);
		$return_admin_ids = explode(',', RETURN_ADMIN_IDS);
		$user_id = $this->user->getId();
		if(in_array($user_id, $admin_ids) || in_array($user_id, $return_admin_ids)){
			$this->_is_admin  = true;
			$data['is_admin'] = true;
		}
		//Set Admin Mode is on or off
		if(isset($this->request->post['admin_mode']) && $this->request->post['admin_mode'] == 'on'){
			$data['admin_mode'] = 'on';
		}
	}

	/**
	 * @info: Public method to set return details to $data array
	 * @param: $data Array as refrence 
	 * @return: Nishu. 20th Feb 2018
    */
    public function setBasicTemplateData(&$data){
    	$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('sale/return/return_form', 'token=' . $data['token'] . '&order_id=' . $data['order_id'], 'SSL')
		);
    	$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
    }

    /**
	 * @info: Set some generalaized data to $data
	 * @param: &$data As Refrence
	 * @author: Nishu, 2018 
    */
	public function setBasicData(&$data){
		$data['shipping_method']  = $this->_shipping_methods;
	 	
	 	//Load Models
    	$this->load->model('sale/shipping_label');
		$this->load->model('localisation/country');


		//Get list of WSB warehouses
		$filter_data = array(
					            'search_warehouse' => '',
					            'search_city' => ''
					        );
		$data['Warehouses'] = $this->model_sale_shipping_label->getAddresses($filter_data);

		//Get List of all countries
		$data['countries'] 	= $this->model_localisation_country->getCountries();

		//Get List of all Custom Party Detail
		$filter_data = array(
				            'search_firm_name' => '',
				            'search_city' => ''
				        );
		$data['custom_parties'] = $this->model_sale_return->getCustomParties($filter_data);

		$data['courier_partners_reverse'] = $this->model_sale_shipping_label->getCourierListByServiceType('reverse');

		$data['courier_partners_forward'] = $this->model_sale_shipping_label->getCourierListByServiceType('forward');
	}

	/**
	 * @info: Private Method to Get Seller Invoices fo that specific order_id
	 * @param: $data as reference Array
	 * @author: Nishu, June 2018 
	*/
	private function setAllSellerInvoices(&$data){

		//Get All SellerInvoices by given order_id from orderInfo lib using Static mathod
		//$data['all_seller_invoices'] = OrderInfo::setAllSellerInvoicesByOrderId($this->db, $data['order_id']);
	}

	/**
	 * @info: Set order and order_product details in $data to show over return_form
	 * @param: &$data As Refrence data
	 * @author: Nishu, 2018
	*/
	public function setOrderAndProductDetails(&$data){
		$selector = array(
	                'order'=> array('select' => array()) ,
	                'suborder' => array( 'select' => array()),
	                'order_product' => array( 'select' => array()),
	               );
		//Get OrderInfo details
		$order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'],'',$selector);
		if(isset($order_info['order'])){
			$data['order_no']    = $order_info['order']['order_no'];
			$data['order']       = $order_info['order'];
			$data['customer_id'] = $data['order']['customer_id'];
			$customer            = new Customer($this->registry);
			$data['customer']    = $customer->getCustomerById($data['customer_id']);
		}
		if(isset($order_info['suborder'])){
			$data['suborder_ids']  = array_keys($order_info['suborder']);
			$data['suborder']      = $order_info['suborder'];
			foreach ($data['suborder_ids'] as $suborder_id) {
				//Check if any product exist in oc_order_product for that order
				if(isset($data['suborder'][$suborder_id]['order_product'])){
					$data['products'] = array_merge($data['products'],$data['suborder'][$suborder_id]['order_product']);
				}
			}
			$data['products'] = array_combine(
										array_column($data['products'], 'order_product_id'), 
										$data['products']);
		}

		if(!empty($data['order']['alternate_contact_number'])) {
			$data['shipping_address']['alternate_numbers'] = json_decode($data['order']['alternate_contact_number'], true);
		}
		
		$data['shipping_address']['shipping_lname']  	= $data['order']['shipping_firstname'] . ' ' . $data['order']['shipping_lastname'];
		$data['shipping_address']['shipping_company']	= $data['order']['shipping_company'];
		$data['shipping_address']['shipping_address_1'] = $data['order']['shipping_address_1'];
		$data['shipping_address']['shipping_address_2'] = $data['order']['shipping_address_2'];
		$data['shipping_address']['shipping_city'] 		= $data['order']['shipping_city'];
		$data['shipping_address']['shipping_postcode'] 	= $data['order']['shipping_postcode'];
		$data['shipping_address']['shipping_country'] 	= $data['order']['shipping_country'];
		$data['shipping_address']['shipping_country_id']= $data['order']['shipping_country_id'];
		$data['shipping_address']['shipping_zone'] 		= $data['order']['shipping_zone'];
		$data['shipping_address']['shipping_zone_id'] 	= $data['order']['shipping_zone_id'];
		$data['payment_address']['payment_zone_id'] 	= $data['order']['payment_zone_id'];
		$data['shipping_address']['telephone'] 			= $data['order']['telephone'];
		//Looping of all products
    	foreach ($data['products'] as $oopid => $product) {
    		$data['products'][$oopid]['is_editable'] = false;
    		$data['products'][$oopid]['price_per_piece_with_currency'] = $this->currency->format($product['price_per_piece']);
    		$data['products'][$oopid]['link'] = '/index.php?route=product/product&product_id='.$product['product_id'];
	
			$transfer_per_price = (float)$product['transfer_price_per_piece'];
			$tax                = (float)$product['seller_input_tax'];

		    $data['products'][$oopid]['seller_tax_per_piece'] = number_format( ($transfer_per_price * $tax)/ 100, 2);

    	}
    	//Set all product options product wise
		$this->setProductOptions($data);

		//Set All product images 
		$this->setProductImages($data);
	}

	/**
	 * @info: Set All seller Details to $data for specific order
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setAllSellersByOrderId(&$data){
		if(empty($data['products'])){
			return;
		}

		$op_ids = array_keys($data['products']);
		
		$fields = array('*');

		//Get All Sellers detail by given order_product_ids as $op_ids
		$sellers = $this->model_sellers_sellers->getSellersFromOrderProductId($op_ids, $fields);

		$data['sellers'] = array_combine(
			                array_column($sellers, 'seller_id'), 
			                $sellers
			              );
	}

	/**
	 * @info: Set All order_product related options to $data
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setProductOptions(&$data){
		//Set OrderProductIds
		$order_product_ids = array_column($data['products'], 'order_product_id');
		$oop_options = array();
		if(!empty($order_product_ids)){
			$oop_options = $this->model_sale_order->getOrderOptionByOOPIds(implode(',', $order_product_ids));
		}
		if(!empty($oop_options)){
			$data['oop_options'] = array_combine(array_column($oop_options, 'order_product_id'), $oop_options);	
		}else{
			$data['oop_options'] = $oop_options;
		}
	}

	/**
	 * @info: Set All order_product related images to $data
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setProductImages(&$data){
		//Set ProductIds
		$opids = array_unique( array_column($data['products'], 'order_product_id') );
		//Set Product Images
    	MsProduct::setProductImages($this, $opids, $data);
	}

	/**
	 * @info: Set all tpl URLs  to $data
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setTplFiles(&$data){

		//Set tab urls 
		$data['tab_all_return'] 	 			= $this->load->view('sale/tab_all_return_form.tpl', $data);
		
		$data['tab_add_pickup_address'] 		= $this->load->view('sale/return_reverse_shipment_add_pickup_address.tpl', $data);

		$data['tab_add_forward_pickup_address']	= $this->load->view('sale/return_forward_shipment_add_pickup_address.tpl', $data);
		
		$data['gnrt_dbt_note_no'] 				= $this->url->link('sale/return/generate_debit_note_no', 'token=' . $data['token'].'&order_id='. $data['order_id'], 'SSL');

		$data['add_custom_party_popup'] 		= $this->load->view('sale/add_custom_party_popup.tpl',$data);

		$data['tab_add_shipment']    			= $this->load->view('sale/tab_add_shipment_return.tpl', $data);

		$data['tab_shipment_backto_customer']   = $this->load->view('sale/tab_shipment_backto_customer.tpl', $data);

		$data['tab_reverse_shipment_status'] 	= $this->load->view('sale/tab_reverse_shipment_history_tracking.tpl', $data);
		
		$data['tab_credit_note']   				= $this->load->view('sale/return_tab_credit_note.tpl', $data);
		
		$data['tab_debit_note']    				= $this->load->view('sale/return_tab_debit_note.tpl', $data);

		$data['tab_replacement_note'] 			= $this->load->view('sale/return_tab_replacement_note.tpl', $data);
		
		$data['tab_bank_details']    			= $this->load->view('sale/return_order_tab_bank_detail.tpl', $data);

		$data['order_href'] 					= $this->url->link('sale/order/info', 'order_id='.$data['order_id'].'&token=' . $data['token'], 'SSL');

		$data['cancel'] 						= $this->url->link('sale/order', 'token=' . $data['token'], 'SSL');
	}

	/**
	 * @info: Public method to set return's data to $data Array
	 * @param: $data AS refrence array
	 * @author: Nishu May 2018
	*/
	public function setReturns(&$data){
		$this->load->model('sale/return');

		//Initialize Active return ids array
		$active_return_ids = array();

		$data['master_return_ids'] = $this->model_sale_return->getMasterReturnIdsByOrderId( $data['order_id'] );

		$data['forward_shipment_return_ids'] = $this->model_sale_return->getShipmentBackToCustomerReturnIdsByOrderId( $data['order_id'] );
		
    	//Creating ReturnInfo object
		$return_info  = new ReturnInfo($this);

		$data['return_reasons'] = $return_info->getReturnReason();
		$data['return_actions'] = $return_info->getReturnAction();

		$data['all_credit_notes'] = $return_info->getAllCNByOrderId($data['order_id']);
		$data['all_debit_notes']  = $return_info->getAllDNByOrderId($data['order_id']);
		$active_cns = array();

		if(!empty($data['all_credit_notes'])){
			foreach ($data['all_credit_notes'] as $cn_id => $cn) {
				if($cn['credit_note_status'] == 0){
					$encode_file = array();
					$encode_file['order_id'] = $cn['order_id'];
					$encode_file['credit_note_id'] = $cn_id;
					$encode_file = base64_encode(serialize($encode_file));
					$file_url = $this->securefiledownload->getDownloadLink('buyer_credit_note',$encode_file, false);
					$cn['credit_note_dload'] = $file_url;
					$data['cancelled_cn'][] = $cn;
				}else{
					$active_cns[$cn_id] = $cn; 
				}
			}
		}
		
		$active_cn_ids = array_column($active_cns, 'credit_note_id');

		//Set Return Reasons Order and order product wise
		$this->setReturnReasons($data);

		$return_data = array();
		$return_data['order_id'] = $data['order_id'];
		$selector  =  array(
                        'oc_return'=> array('select' => array()) ,
                        'oc_suborder'=> array('select' => array('shipping_charge')) ,
                        'oc_master_return' => array( 'select' => array()),
                        'oc_return_reason' => array( 'select' => array()),
                    	'oc_return_action' => array( 'select' => array()),
                    	'oc_replacement_note' => array( 'select' => array('replacement_note_id'))
                       );
		//Returns all order product returns for $data['order_id']
		$returns = $return_info->getReturnInfo($this->db, $return_data, $selector);
		
		// get all return replacment_ids from return list
		$return_replacment_ids = array();
		$return_replacment_ids = array_filter(
											array_unique(
												array_column(
															$returns['oc_return'], 'replacement_note_id'
															)
												)
										);

		//Begin Loop to all returns
		foreach ($returns['oc_return'] as $return) {

			$oopid            = $return['order_product_id'];
			$return_id        = $return['return_id'];
			$master_rid       = $return['master_return_id'];
			$order_id 		  = $return['order_id'];
			$cn_id            = $return['credit_note_id'] ?? 0;
			//Set Return reason Name
			if(!empty($return['return_reason'])){
				$return_reason_id   = $return['return_reason_id'];
				$return_reasons     = $return['return_reason'];
                $return_reason_name = !empty($return_reasons) ? $return_reasons['name'] : '';
                $return['return_reason_name'] = $return_reason_name;
				$return['return_type']        = !empty($return_reasons) ? $return_reasons['reason_type'] : '';
			}

			if(!empty($cn_id) && in_array($cn_id, $active_cn_ids)){
				$data['cn_wise_returns'][] = $return;
			}

			//Set Return actoin Name
			if(!empty($return['return_action'])){
				$return_action_id   = $return['return_action_id'];
				$return_actions     = $return['return_action'];
                $return_action_name = !empty($return_actions) ? $return_actions['name'] : '';
                $return['return_action_name'] = $return_action_name;
			}

			$data['return_history'][$oopid][$master_rid][$return_id] = $return;

			//Set Return array as active return with latest row
			if(isset($return['active_row']) && $return['active_row'] == 1){
				
				$active_return_ids[] = $return_id;

				//Set All Returns for this order
				$data['returns'][$return_id] = $return;
				$data['returns'][$return_id]['product'] = $data['products'][$oopid];
			}
		}


		//Replacement Notes
		$data['all_replacement_notes']  = $return_info->getAllRNByReplacementIds($return_replacment_ids);

		//Get Active Return Row wise CreditNote ID and DebitNoteId
		$return_result = $return_info->getReturnIdWiseCnDn($active_return_ids);
		foreach ($return_result as $return_id => $value) {
			
			$data['returns'][$return_id]['credit_note_id'] = $value['cn_id'];
			$data['returns'][$return_id]['debit_note_id']  = $value['dn_id'];
			$data['returns'][$return_id]['replacement_note_id']  = $value['rn_id'];
			$data['returns'][$return_id]['credit_notes']   = !empty($data['all_credit_notes'][$value['cn_id']])
															 ?$data['all_credit_notes'][$value['cn_id']]
														      : '';
		}
		$this->setReturnActionReasons($data);
	}

	/**
	 * @info : Set Return Reasons Order and order product wise
	 * @author: Nishu, 27th Feb 2018
    */
    public function setReturnReasons(&$data){
    	$return_action_base = new ReturnActionBase($this);

    	//Set Return Reasons for complete order by checkin suborder wise
		$return_reason_filter = array();
		$data['all_available_return_reasons'] = array();

		//Set Order Product Return Reasons
		$return_reason_filter = array();
		$all_op_ids = array_keys($data['products']);
		$sor_order_product = new SorOrderProduct($this->registry);
		$sor_order_product_day_limit = $sor_order_product->getSorPeriodByOrderProductIds($all_op_ids);

		foreach ($data['products'] as $oopid => $product) {
			$suborder_id = $product['suborder_id'];
			$suborder    = $data['suborder'][$suborder_id];
			$suborder_status_id = $data['suborder'][$product['suborder_id']]['order_status_id'];
			$return_reason_filter['admin_mode']        = $data['admin_mode'];
			$return_reason_filter['order_id']          = $data['order_id'];
			$return_reason_filter['suborder_id']       = $product['suborder_id'];
			$return_reason_filter['shipping_code']     = $suborder['shipping_code'];
			$return_reason_filter['suborder_status']   = $suborder_status_id;
			$return_reason_filter['buyer_invoice_id']  = $product['buyer_invoice_id'];
			$return_reason_filter['seller_invoice_id'] = $product['seller_invoice_id'];
			$return_reason_filter['order_product_id']  = $oopid;
			$return_reason_filter['is_returnable']     = $product['is_returnable'];
			$return_reason_filter['status_date']       = $this->model_sale_order->getSuborderHistoryTime($data['order_id'],$suborder_id, $suborder_status_id);

			$return_reasons = $return_action_base->returnReasonsForOrderProduct($return_reason_filter);

			$data['all_available_return_reasons'] = array_merge($data['all_available_return_reasons'],$return_reasons);

			$data['products'][$oopid]['return_reasons'] = $return_reasons;

			//If SOR return is/was available on product
			$sor_msg = "";
			if(
				isset($sor_order_product_day_limit[$oopid]) 
				&& isset($return_reasons['sor_return']) 
				&& in_array($suborder_status_id, ORDER_STATUS_CLUSTERS['delivered'])
			){
				$sor_msg = "SOR Return Available";
			}else if(
				isset($sor_order_product_day_limit[$oopid]) 
				&& !isset($return_reasons['sor_return'])
				&& in_array($suborder_status_id, ORDER_STATUS_CLUSTERS['delivered'])
			){
				$sor_msg = "SOR Return Period Lapsed";
			}
			
			$data['products'][$oopid]['sor_msg'] = $sor_msg;

			//Check Returnis editable or not
			if(
				$suborder_status_id == ORDER_STATUS['Failed']
					||
				$data['products'][$oopid]['is_returnable'] == 1
			){
				$data['products'][$oopid]['is_editable']    = true;
			}else{
				$data['products'][$oopid]['is_editable']    = false;
			}


		}
    }

    /**
	 * @info: Set all return_action_reasons from master table oc_return_action_reason,
	 *         to specify action reasons dynamically
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
    public function setReturnActionReasons(&$data){
    	$return_action_reasons = $this->model_sale_return->getActionReason();
		$data['action_reasons'] = array_combine(
									   array_column($return_action_reasons, 'id'), 
									   array_column($return_action_reasons,'action_reason_name')
									);
    }

    /**
	 * @info: Set TAB wise data Like: tab_name, tab_key and tpl file with proper html
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
    protected function setTabMethodData(&$data, $tab_key) {
    	//Calculate Return Action Tab name to display
    	
    	$tab_name = ucwords(strtolower(str_replace('_', ' ', $tab_key)));

      	$data['available_action_tabs'][$tab_key] = $tab_name;

    	//Assign Tab name to show
    	$data['all_tabs'][$tab_key]   = $tab_name;

    	//Set Tpl file for specific return action
    	$data['tab_key'] = $tab_key;
    	$data['tab_return_actions'][$tab_key]  = $this->load->view('sale/tab_change_status_return.tpl', $data);
    }

    /**
	 * @info: Set all return action tab on the bases 
	 *         of their perssion to logged in user and data availbility
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
    private function getReturnActionTabNames(&$data) {
        $excludes = array('__construct','__get','__set','isDeviceMobile','getCustomUrlFilters');

        $return_action_tabs_obj = new ControllerSaleReturnactiontabs();

        //Class's all methods
        $class_methods =  get_class_methods( $return_action_tabs_obj );
        $tab_methods   = array();
        foreach($class_methods as $method) {
           if(!in_array($method,$excludes)) {
           	$permission_url = 'sale/returnactiontabs/'.$method;
	   		$is_admin_check = ($data['admin_mode']== 'off'? false : true);
	    	if( $this->user->hasPermission('access', $permission_url, true, $is_admin_check) ){
	    		
				$tab_key = str_replace('tab', '', $method);
				
	    		$data['current_return_actions_tab_wise'][$tab_key] = $return_action_tabs_obj->$method();
				
			}
           }
       }
       return $tab_methods;
	}

	/**
	 * @info: Set Return action tpl data, if any return is exist in all current returns
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setActionTabTpls(&$data){
		if(!empty($data['current_return_actions_tab_wise'])){
			foreach ($data['current_return_actions_tab_wise'] as $tab_key => $return_actions) {
				
				if(!empty($return_actions)){
					foreach ($return_actions as $return_action_id) {
						if(in_array($return_action_id, $data['all_current_return_actions'])){
							//Set Return action tpl data, any return is exist in all current returns
							$this->setTabMethodData($data, $tab_key);
							break;
						}	
					}
				}

				
			}
		}
	}

    /**
	 * @info: Set Post available return actions, retunr_action_id wise
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setCurrentReturnActionsAndTabs(&$data){

		$current_return_actions = array();
		$all_current_return_actions = array();

		foreach ($data['returns'] as  $rid=> $return) {
			$return_action_id = $return['return_action_id'];

			$all_current_return_actions[] = $return_action_id;
			
		}

		//Set current all unique return action ids
		$data['all_current_return_actions'] = array_unique($all_current_return_actions);
	}

	/**
	 * @info: Create and set return_action classes object to member variable
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setAllActionClassObject(&$data){
		if(!empty($data['return_actions'])){

			foreach ($data['return_actions'] as $return_action) {
				$return_action_id  = $return_action['return_action_id'];
				//Create action class object by given return action id
		    	$action_class_name = $return_action['class_name'];
		    	if(class_exists($action_class_name)) {
		    		$action_obj = new $action_class_name($this);
			    	//Assign Return Action Object to make it globally accessible
			    	$this->_return_action_objs[$return_action_id] = $action_obj;
			    	$data['return_actions'][$return_action_id]['is_qty_editable'] = $action_obj->isQtyEditable($return_action_id);
		    	}
			}
		} 
	}

	/**
	 * @info: Check if return is editable or not for further actions 
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function checkReturnIsEditable(&$data){
		
		if(!empty($data['returns'])){
			foreach ($data['returns'] as $rid => $return) {
				$return_action_id = $return['return_action_id'];
				$oopid            = $return['order_product_id'];

				$data['returns'][$rid]['is_editable'] = true;
				if(isset($this->_return_action_objs[$return_action_id])){
					$action_obj = $this->_return_action_objs[$return_action_id];
					//Check Return is editable or not
					if(
						empty($data['products'][$oopid]['return_reasons'])
							||
						  (
							!empty($return['debit_note_id']) 
								&& 
							$data['all_debit_notes'][$return['debit_note_id']]['debit_note_status'] == 1
						  )
							||
						  (
						 	!empty($return['credit_note_id'])
								&& 
							$data['all_credit_notes'][$return['credit_note_id']]['credit_note_status'] == 1
						  )
						    ||
						!$action_obj->isReturnEditable()
					){
						$data['returns'][$rid]['is_editable'] = false;
					}
				}

				$data['products'][$oopid]['returns'][] = $data['returns'][$rid];
			}
		}
	}

	/**
	 * @info: Set Post available return actions, return_action_id wise
	 * @param: &$data As Ref data
	 * @author: Nishu, 2018
	*/
	public function setAvailablePostReturnActions(&$data){

		$data['all_avaiable_post_actions']['tab']    = array();
		//$data['all_avaiable_post_actions']['action'] = array();

		$temp_tab_post_actions = array();

		$return_action_base = new ReturnActionBase($this);

		$all_avaiable_post_actions = $return_action_base->getAvailableAllPostAction();
		$all_avaiable_post_actions = explode(',', $all_avaiable_post_actions );

		foreach ($data['returns'] as $return) {
			$return_id         = $return['return_id'];
			$return_action_id  = $return['return_action_id'];
			$temp_post_actions = array();

			if(!isset($this->_return_action_objs[$return_action_id])){
				$action_class_name = $return['return_action']['class_name'];
				if(class_exists($action_class_name)){
					$this->_return_action_objs[$return_action_id] = new $action_class_name($this);
				}else{
					continue;
				}
			}
			$action_obj = $this->_return_action_objs[$return_action_id];
    		
    		$post_actions = $action_obj->getAvailablePostAction($return_action_id, $return);

    		if(!empty($post_actions)){
    			$temp_post_actions = explode(',', $post_actions);

    			//Action Filtering for which mode, it is either admin_mode or normal_mode
    			$check_for = 'normal_mode';
    			//Check PostReturn Action visibility
    			$temp_post_actions = $this->filterPostActions($temp_post_actions, $return_id, $data, $check_for);
    		}

			$data['returns'][$return_id]['post_actions']['Available Actions'] = $temp_post_actions;

			if($data['admin_mode'] == 'on' && !empty($all_avaiable_post_actions)){
				$admin_post_Actions = array();
				foreach ($all_avaiable_post_actions as $r_action_id) {
					if(!in_array($r_action_id, $data['returns'][$return_id]['post_actions']['Available Actions'])){
						$admin_post_Actions[] = $r_action_id;
					}
				}
				//Action Filtering for which mode, it is either admin_mode or normal_mode
    			$check_for = 'admin_mode';
				$admin_post_Actions = $this->filterPostActions($admin_post_Actions, $return_id, $data, $check_for);
				$data['returns'][$return_id]['post_actions']['Admin Mode Actions'] = $admin_post_Actions;
			}

    		$temp_tab_post_actions = array_merge($temp_tab_post_actions, $temp_post_actions);
    		$temp_tab_post_actions = array_unique($temp_tab_post_actions);
			
		}
		//Set default Return Actions to tab 
		foreach ($data['current_return_actions_tab_wise'] as $tab_key => $r_actions) {
			//Set all return action ids as default post return action ids
			if($data['admin_mode'] == 'off'){
		    	$data['all_avaiable_post_actions']['tab'][$tab_key] = $temp_tab_post_actions;
		    }else{
				$data['all_avaiable_post_actions']['tab'][$tab_key] = $all_avaiable_post_actions;
		    }
		}
	}

	/**
	* @info: Public method to check PostReturnActions Visibility
	* @param: $temp_post_actions- Return Post action to filter
	          $return_id - ReturnId which we are checking available post actions
	          $data- All data collection for given specipic order
	          $check_for - Specifys mode for which we are checking post actions, 
	                        It can be either AdminMode or NormalMode
	@return: Filtered Post Actions
	* @author: Nishu May 2018
	*/
	public function filterPostActions($temp_post_actions, $return_id, $data, $check_for
		){
		
		$temp_post_actions = array_flip($temp_post_actions);

		//Post Return actions list
		foreach ($temp_post_actions as $return_action => $key) {
			//Check Return Action class object is declared or not
			if(!isset($this->_return_action_objs[$return_action])){
				//Get Class name by return action id
				$action_class_name = $data['return_actions'][$return_action]['class_name'];
				
				//Create ReturnAction Class Object
				$action_obj = new $action_class_name($this);

		    	//Assign Return Action Object to make it globally accessible
		    	$this->_return_action_objs[$return_action] = $action_obj;
			}
			$return_action_object = $this->_return_action_objs[$return_action];

			if(!$return_action_object->checkPostActionVisibility($return_id, $data, $return_action, $check_for)){
				unset($temp_post_actions[$return_action]);
			}
		}
		//Array flip to covert key to value
		$temp_post_actions = array_flip($temp_post_actions);

		return $temp_post_actions;
	}

    /**
	 * Public function to set Credit Note data by manipulating Given data 
	 * @param: $data
	 * @return: void
	 * @author: Nishu, Sept 2017
	*/
	public function setCreditNoteData(&$data){
		$cn_data = array();

		$returns_for_cn = array();
		$return_ids     = array();

		foreach ($data['cn_wise_returns'] as $return) {
			$cn            = array();

			$cn_id         = (int)$return['credit_note_id'];

			if(isset( $data['all_credit_notes'][$cn_id])){
				$cn = $data['all_credit_notes'][$cn_id];
			}
			
			$credit_note   = array();
			
			//Set CN Array for tpl file
			$credit_note['credit_note_id']     =  $cn_id;
			$credit_note['credit_note_no']     =  $cn['credit_note_no'];
			$credit_note['credit_note_amount'] =  $cn['credit_note_amount'];
			$credit_note['net_refundable']     =  $cn['net_refundable'];
			$credit_note['credit_note_status'] =  $cn['credit_note_status'];
			$credit_note['credit_note_prefix'] =  $cn['credit_note_prefix'];
			$credit_note['credit_note_user']   =  $cn['user'];
			$credit_note['date_show']          =  $cn['date_added'];
			$credit_note['trxn_status']        =  $cn['trxn_status'];
			$credit_note['shipping_collected'] =  $cn['shipping_collected'];
			$credit_note['cod_failed_penalty'] =  $cn['cod_failed_penalty'];
			$credit_note['is_cod_failed']      =  $cn['is_cod_failed'];
			$credit_note['payment_cleared']    =  $cn['payment_cleared'];

			$credit_note['reversal_shipping']  =  $cn['reversal_shipping'];
			$credit_note['other_charges']      =  $cn['other_charges'];

			$credit_note['return_id']          =  $return['return_id'];
			$credit_note['rtn_qty']            =  $return['quantity'];
			$credit_note['order_product_id']   =  $return['order_product_id'];
			$credit_note['return_reason_id']   =  $return['return_reason_id'];
			$credit_note['return_action_id']   =  $return['return_action_id'];
			$credit_note['master_return_id']   =  $return['master_return_id'];

			$dn_id = $return['debit_note_id'];
			$credit_note['debit_note_no']   =  ($dn_id > 0)? $data['all_debit_notes'][$dn_id]['debit_note_prefix'].$data['all_debit_notes'][$dn_id]['debit_note_no']:'';

			$encode_file = array();
			$encode_file['order_id']       = $cn['order_id'];
			$encode_file['credit_note_id'] = $cn_id;
			$encode_file = base64_encode(serialize($encode_file));
			$file_url = $this->securefiledownload->getDownloadLink('buyer_credit_note',$encode_file, false);
			$credit_note['credit_note_dload'] = $file_url;
			$credit_note['cn_cancellable'] = true;

			if(!empty($cn['trxn_status'])){
				$trxn_status = explode(',', $cn['trxn_status']);

				foreach ($trxn_status as $val) {
				 	if(!in_array($val, $this->_trxn_pay_initiated_status)){
				 		$credit_note['cn_cancellable'] = false;
				 		break;
				 	}
				}
			}
			$cn_data[] = $credit_note;
		}//End of foreach of cn_wise_returns


		//Loop over returns for those return which are ready for generating CN
		foreach ($data['returns'] as $return) {
			$return_id        = (int)$return['return_id'];
			$op_id            = (int)$return['order_product_id'];
			$buyer_invoice_id = (int)$data['products'][$op_id]['buyer_invoice_id'];
			$suborder_id      = $data['products'][$op_id]['suborder_id'];
			$suborder         = $data['suborder'][$suborder_id];

			//Check Return is eligible to generate CN, 
			//By checking buyer invoice is generated and SubOrder is not cancelled
			if(
				!empty($buyer_invoice_id) 
					&& 
				$suborder['order_status_id'] != ORDER_STATUS['Canceled']
					&&
				empty($return['credit_note_id'])
			){
				$return_action_id = (int)$return['return_action_id'];

				$action_obj = $this->_return_action_objs[$return_action_id];

				$is_cn_generatable = $action_obj->isCnGeneratable($return);

				if(!empty($is_cn_generatable)){

					$return_ids[]  = $return_id;

					$returns_for_cn[$suborder_id][$return_id] = $return; 
				}
			}
		}
		
		$data['credit_notes']   = $cn_data;
		$data['returns_for_cn'] = $returns_for_cn;

		if(!empty($return_ids)){
			//Calculate Reverse Shiping charges regarding Product weight
			$data['shipping']       = $this->getShippingForCNs($return_ids, $data);
		}

		//Set Suborder level info for CN
		$cn_obj = new CreditNote( $this );
		$sdata['suborder_id'] = array();

		foreach ($data['suborder'] as $suborder_id => $sdata) {
			$cod_data = array();
			$cod_data['order_id'] 		= $data['order_id'];
			$cod_data['suborder_id'] 	= $suborder_id;
			$cod_failed_penalty[$suborder_id]['value'] = $cn_obj->getTotalAdvanceForCN($cod_data);
	        $cod_failed_penalty[$suborder_id]['disabled'] = $this->_isCodFailedPenaltyDisabled($sdata['order_status_id']);
	        $data['advance_payments'][$suborder_id] = AdvanceVoucherLib::getTotalAdvanceOfSuborderPaymentGatewayWise($this->db, $data['order_id'], $suborder_id, false);
		}

		$data['cod_failed_penalty'] = $cod_failed_penalty;
    	$data['advance_collected'] 	= $cod_failed_penalty;
	}

	/**
	 * Public function to set Customer COD security balance in data array 
	 * @param: $data
	 * @return: void
	 * @author: MSA, Sept 2017
	*/
	public function setCustomerCODSecurityBalance(&$data)
	{
		
		$CustomerCODSecurityData =  ReturnInfo::getCustomerCODSecurityData( 
																$this->db,
																$data['customer']['master_id']
																);
		$data['CODSecurityData']    = $CustomerCODSecurityData;
		$data['CODSecurityBalance'] = $CustomerCODSecurityData['cod_security_balance'];
		/*
		$customer_id =  $data['customer']['customer_id'];
		$order_id    = $data['order']['order_id'];
		$res = $this->db->query("CALL calculateAvailableCodSecurityAmountForOrder(".$customer_id.",".$order_id.",@cod_amount)");
        $res = $this->db->query("SELECT @cod_amount;");
        $data['CODSecurityBalance'] = $this->currency->format($res->row['@cod_amount']);
		*/        
	}

	/**
	 * Public function to set Debit Note data by manipulating Given data 
	 * @param: $data
	 * @return: void
	 * @author: Nishu, Sept 2017
	*/
	public function setDebitNoteData(&$data){
		$dn_data = array();

		$seller_dn_ids         = array();
		$custom_dn_ids         = array();
		$returns_for_custom_dn = array();

		if(!empty($data['returns'])){
			foreach ($data['returns'] as $return) {
				
				$op_id = $return['order_product_id'];
				$r_id  = $return['return_id'];

				$dn_id   = $return['debit_note_id'];

				if(isset( $data['all_debit_notes'][$dn_id])){
					$dn     = $data['all_debit_notes'][$dn_id];
				}
				if(isset($dn['debit_note_status']) && $dn['debit_note_status'] != 1){
					//If Debit Note is cancelled then unset credit_note_id in returns array
					$data['returns'][$r_id]['debit_note_id'] = NULL;
					$return['debit_note_id'] = NULL;
				}

				if(!empty($return['debit_note_id']) && $return['debit_note_id'] > 0){
				    
				    $dn_id  = $return['debit_note_id'];
					$dn     = $data['all_debit_notes'][$dn_id];

					if($dn['debit_note_status'] == 1){
						
						$debit_note = array();
						
						$cn_id = $return['credit_note_id'];
						$debit_note =  $return;
						$debit_note['suborder_id']       = $dn['suborder_id'];
						$debit_note['seller_id']         = $dn['seller_id'];
						$debit_note['debit_note_prefix'] = $dn['debit_note_prefix'];
						$debit_note['debit_note_no']     = $dn['debit_note_no'];
						$debit_note['debit_note_status'] = $dn['debit_note_status'];
						$debit_note['custom_id']         = $dn['custom_id'];
						$debit_note['date_show']         = $dn['date_added'];
						$debit_note['dn_user']           = $dn['user'];
						$debit_note['trxn_done']         = $dn['trxn_done'];
						$debit_note['debit_note_amount'] = $dn['debit_note_amount'];
						$debit_note['custom_debit_note_ref'] 	 = $dn['custom_debit_note_ref'];
						$debit_note['custom_debit_note_meta'] 	 = $dn['custom_debit_note_meta'];						
						$debit_note['credit_note_id']    = $cn_id;

						$debit_note['credit_note_no']   =  ($cn_id > 0)? $data['all_credit_notes'][$cn_id]['credit_note_prefix'].$data['all_credit_notes'][$cn_id]['credit_note_no']:'';

						//Downloaded file url
	 					$encode_file = array();
						$encode_file['order_no'] = $data['order_no'];
						$encode_file['debit_note_id'] = $dn_id;
						$encode_file = base64_encode(serialize($encode_file));
						$file_url = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false);

						$debit_note['debit_note_dload']    = $file_url;

						if(!empty($dn['custom_id'])){
							$data['custom_debit_notes'][$r_id] = $debit_note;
							$custom_dn_ids[]                   = $dn_id;
						}else{
							$data['debit_notes'][$dn['seller_id']][$r_id] = $debit_note;
							$seller_dn_ids[]                              = $dn_id;
						}
					}
				}else{
					$seller_invoice_id = $data['products'][$op_id]['seller_invoice_id'];
				
					if(!empty($seller_invoice_id)){
						$return_action_id = $return['return_action_id'];
						$action_obj = $this->_return_action_objs[$return_action_id];

						$is_dn_generatable = $action_obj->isDnGeneratable($return);

						if(!empty($is_dn_generatable)){
							$op_id          = $return['order_product_id'];
							$suborder_id    = $data['products'][$op_id]['suborder_id'];

							$returns_for_custom_dn[$return['return_id']] = $return; 
						}
					}
				}
			}

			foreach ($data['all_debit_notes'] as $dn_id => $debit_note) {
				if($debit_note['debit_note_status'] == 0){
					//Downloaded file url
 					$encode_file = array();
					$encode_file['order_no'] = $data['order_no'];
					$encode_file['debit_note_id'] = $dn_id;
					$encode_file = base64_encode(serialize($encode_file));
					$file_url = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false);

					$debit_note['debit_note_dload']    = $file_url;
					
					$data['cancelled_debit_notes'][$dn_id] = $debit_note;
				}
			}
		}

		$data['returns_for_custom_dn'] = $returns_for_custom_dn;
	}

	/**
	 * Public function to set Replacement Note data by manipulating Given data 
	 * @param: $data Array
	 * @return: void
	 * @author: MSA, August 2018
	*/
	public function setReplacementNoteData(array &$data){

		$replacement_note = array();

		if( !empty($data['returns']) ) {

			foreach ($data['returns'] as $return) {

				$rn_id   = $return['replacement_note_id'];

				if( !empty($rn_id) && !empty($data['all_replacement_notes'][$rn_id]) ){

					$replacement_note = $data['all_replacement_notes'][$rn_id];
					$replacement_note['return_reason_name'] = $return['return_reason_name'];
					$replacement_note['return_action_name'] = $return['return_action_name'];
					
					//Downloaded file url
 					$encode_file = array();
					$encode_file['order_no'] = $data['order_no'];
					$encode_file['replacement_note_id'] = $rn_id;
					$encode_file = base64_encode(serialize($encode_file));

					$file_url = $this->securefiledownload->getDownloadLink(
																			'seller_replacement_note',
																			$encode_file, 
																			false
																		);
						
					$replacement_note['replacement_note_dload']    = $file_url;

					$data['replacement_notes'][$rn_id] = $replacement_note;	
				}	
			}	
		 }
	}

	/**
	 * @info: Public function to set ReverseShipment data for that order
	 * @param: $data
	 * @return: void
	 * @author: Nishu, Sept 2017
	*/
	public function setReverseShipments(&$data){
		//Set Reverse Shipments add to specific order
		$all_reverse_shipments 	= $this->load->model_sale_return->getReverseShipments($data['order_no']);

		$reverse_shipments = array();
		$cancel_reverse_shipments = array();
		if(!empty($all_reverse_shipments)){
			foreach ($all_reverse_shipments as $key => $value) {
				if(!$value['is_cancel']) {
					if(!empty($value['shipping_slip'])) {
				        //$file_path = base64_encode( $value['shipping_slip'] );
				        //$value['download_slip'] = $this->securefiledownload->getDownloadLink('return_shipping_slip', $file_path);    
				        $value['download_slip'] = STATIC_CONTENT_URL_SSL . 'return/courier_slip/'. $value['tracking_no'] . '/' . $value['shipping_slip'];    

					}
					$data['reverse_shipments'][$value['shipping_id']] = $value;
				}else if($value['is_cancel']) {
					$data['cancel_reverse_shipments'][$value['shipping_id']] = $value;
				}
			}
        }
	}

	/**
	 * @info: Public function to set Shipment Back to customer data for that order
	 * @param: $data
	 * @return: void
	 * @author: Nishu, Sept 2017
	*/
	public function getShipmentsBackToCustomer(&$data){
		//Set Reverse Shipments add to specific order
		$all_shipments_backto_cutomer = $this->load->model_sale_return->getShipmentsBackToCustomer($data['order_id']);
		if(!empty($all_shipments_backto_cutomer)){
			foreach ($all_shipments_backto_cutomer as $key => $value) {
				if(!$value['is_cancel']) {
					$data['shipments_backto_cutomer'][$value['shipping_id']] = $value;
				}else if($value['is_cancel']) {
					$data['cancel_reverse_shipments'][$value['shipping_id']] = $value;
				}
			}							 
        }
	}

	/**
	 * @info: Public function to add and update return request
	 * @author: Nishu, Sept 2017
	*/
	public function addNewReturns() : void{

		//Add New return with pending status
		$return_base = new ReturnBase($this);
		$return_base->addReturnWithPendingStatus($this->request->post);

    }


////////////////////////////////////////////////
///////////////////////////////////////////////
//////////////////////////////////////////////
/////////////////////////////////////////////
///////////End OF NEW RETURN PANEL//////////
///////////////////////////////////////////
//////////////////////////////////////////
/////////////////////////////////////////
////////////////////////////////////////


	/**
	 * Public function to get Shipping charges all order product Ids on debit note basis
	 * @param: Return Ids, array
	 * @return: Number
	 * @author: Nishu, Sept 2017
	*/
	public function getShippingForCNs($return_ids, &$data){
		
		$reverse_shipping = array();

		//To get Total weight Suborder wise
		$suborder_total_weights = $this->model_sale_return->getTotalWeightForSuborder($data['order_id']);

		//To get total weight for all returns for CN, suborder-wise
		$total_weights = $this->model_sale_return->getTotalWeightForReturns($return_ids);
	
		//Get Return's weight to calculate reverse shipping suborder wise
		$returns_total_weights = $this->model_sale_return->getTotalWeightForReverseShipping($return_ids);
	
		if(!empty($total_weights)){
			foreach ($total_weights as $suborder_id => $total_weight) {

				//Set Reversal Shipping chargese to refund
				$data['reversal_shipping'][$suborder_id] = 0;
				$suborder_shipping = 0;
				
				if(isset($data['suborder'][$suborder_id])){

					$suborder_weight   = !empty($suborder_total_weights[$suborder_id])? $suborder_total_weights[$suborder_id] : 0 ;

					$suborder_shipping = $data['suborder'][$suborder_id]['shipping_charge'];

					//Calculate reversal shipping, suborderwise
					if(!empty($suborder_weight)){
						$data['reversal_shipping'][$suborder_id] = round( ($suborder_shipping / $suborder_weight) * $total_weight, 2);
					}
				}
			}	
		}
		if(!empty($returns_total_weights)){
			foreach ($returns_total_weights as $suborder_id => $total_weight) {
				$total_weight = ceil($total_weight);
				if($total_weight >= 1){
					$total_weight = $total_weight - 1;
					$reverse_shipping[$suborder_id] = 70 + ($total_weight * 30);
				}else{
					$reverse_shipping[$suborder_id] = 70;
				}
			}	
		}

		return $reverse_shipping;
	}

	public function resetReversalShipping()
	{
		$this->load->model('sale/return');
		if(!empty($this->request->post['return_ids'])) {
			$return_ids 	= $this->request->post['return_ids'];
			$order_id 		= $this->request->post['order_id'];
			$order_no 		= $this->request->post['order_no'];
			$suborder_id 	= $this->request->post['suborder_id'];
			$shipping_charge = $this->request->post['shipping_charge'];
			$data = array(
				'order_id' => $order_id,
				'order_no' => $order_no,
				'suborder_id' => $suborder_id,
			);

			$data['suborder'][$suborder_id]['shipping_charge'] = $shipping_charge;
			$this->getShippingForCNs($return_ids, $data);
			if(!empty($data['reversal_shipping'][$suborder_id])){
				echo $data['reversal_shipping'][$suborder_id];
			}
		}
	}

	private function _isCodFailedPenaltyDisabled($order_status_id){
		$is_disabled = true;

		if( $order_status_id == ORDER_STATUS['Failed']) {
	        $is_disabled = false;
	    }
	    return $is_disabled;
	}

	/**
	 * @info: Default method to execute for return listing
	 * @author: Nishu, 2017
	*/
	public function index() {

            $this->load->language('sale/return');

            $this->document->setTitle($this->language->get('heading_title'));

            $this->load->model('sale/return');

            $this->getList();	
	}


	protected function getList() {
		$url = '';
		$filter_data = array();
		if (isset($this->request->get['filter_order_no'])) {
			$filter_data['filter_order_no'] = $this->request->get['filter_order_no'];
			$url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
		} else {
			$filter_data['filter_order_no'] = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_data['filter_customer'] = $this->request->get['filter_customer'];
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_customer'] = null;
		}

		if (isset($this->request->get['filter_company'])) {
			$filter_data['filter_company'] = $this->request->get['filter_company'];
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_company'] = null;
		}

		if (isset($this->request->get['filter_city'])) {
			$filter_data['filter_city'] = $this->request->get['filter_city'];
			$url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_city'] = null;
		}

		if (isset($this->request->get['filter_return_action'])) {
			$filter_data['filter_return_action'] = $this->request->get['filter_return_action'];
			$url .= '&filter_return_action=' . urlencode(html_entity_decode($this->request->get['filter_return_action'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_return_action'] = null;
		}

		if (isset($this->request->get['filter_return_replacement'])) {
			$filter_data['filter_return_replacement'] = $this->request->get['filter_return_replacement'];
			$url .= '&filter_return_replacement=' . urlencode(html_entity_decode($this->request->get['filter_return_replacement'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_return_replacement'] = null;
		}

		if (isset($this->request->get['filter_dn_generated'])) {
			$filter_data['filter_dn_generated'] = $this->request->get['filter_dn_generated'];
			$url .= '&filter_dn_generated=' . urlencode(html_entity_decode($this->request->get['filter_dn_generated'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_dn_generated'] = null;
		}

		if (isset($this->request->get['filter_debit_note_no'])) {
			$filter_data['filter_debit_note_no'] = $this->request->get['filter_debit_note_no'];
			$url .= '&filter_debit_note_no=' . urlencode(html_entity_decode($this->request->get['filter_debit_note_no'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_debit_note_no'] = null;
		}

		if (isset($this->request->get['filter_debit_note_date_from'])) {
			$filter_data['filter_debit_note_date_from'] = $this->request->get['filter_debit_note_date_from'];
			$url .= '&filter_debit_note_date_from=' . urlencode(html_entity_decode($this->request->get['filter_debit_note_date_from'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_debit_note_date_from'] = null;
		}

		if (isset($this->request->get['filter_debit_note_date_to'])) {
			$filter_data['filter_debit_note_date_to'] = $this->request->get['filter_debit_note_date_to'];
			$url .= '&filter_debit_note_date_to=' . urlencode(html_entity_decode($this->request->get['filter_debit_note_date_to'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_debit_note_date_to'] = null;
		}

        if (isset($this->request->get['filter_return_request_date_added_to'])) {
			$filter_data['filter_return_request_date_added_to'] = $this->request->get['filter_return_request_date_added_to'];
			$url .= '&filter_return_request_date_added_to=' . urlencode(html_entity_decode($this->request->get['filter_return_request_date_added_to'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_return_request_date_added_to'] = null;
		}

        if (isset($this->request->get['filter_return_request_date_added_from'])) {
			$filter_data['filter_return_request_date_added_from'] = $this->request->get['filter_return_request_date_added_from'];
			$url .= '&filter_return_request_date_added_from=' . urlencode(html_entity_decode($this->request->get['filter_return_request_date_added_from'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_return_request_date_added_from'] = null;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$filter_data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
		$filter_data['limit'] = $this->config->get('config_limit_admin');


	    // URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;

		if (isset($this->request->get['page'])) {
			$general_url .= '&page=' . $this->request->get['page'];
		}

		$data = array();// Initializing the data array to be passed on to template files
		$data = $filter_data;
        // Autoloading the lanugage
		$this->load->autoLoadLanguage('sale/return',$data);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/return', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
		);

		$data['download'] = $this->url->link('sale/return/download', 'token=' . $this->session->data['token'] . $url, 'SSL');

		//Get total count for all return
		$return_total = $this->model_sale_return->getAllReturns($filter_data, 'count');
		//Get Data for all return which is showable
		$results = $this->model_sale_return->getAllReturns($filter_data, 'data');
		$data['returns'] = array();
		if(!empty($results)){
			foreach ($results as $result) {
				$data['returns'][] = array(
					'order_id'      	=> $result['order_id'],
					'master_return_id'  => $result['master_return_id'],
					'order_no'      	=> $result['order_no'],
					//'customer'			=> $result['customer'],
					'customer'     		=> $result['firstname'] . ' ' . $result['lastname'],
					'email'      		=> $result['email'],
					'telephone'     	=> $result['telephone'],
					'company'       	=> $result['shipping_company'],
					'city'       		=> $result['shipping_city'],
					//'dn_details'       	=> !empty($result['dn_detail'])? explode(',', $result['dn_detail']) : '',
					'dn_details'		=> !empty($result['debit_note_id'])
											? array( $result['debit_note_id'] . 
													 '#' .
													 $result['debit_note_prefix'] .
													 '#' .
													 $result['debit_note_no'] .
													 '#' .
													 $result['debit_note_amount'] .
													 '#' .
													 $result['date_added']
												    ) 	
											: '', 
					'order_product_id'  => $result['order_product_id'],
					'master_return_id'  => $result['master_return_id'],
                    'return_request_date' => date('d-m-Y',strtotime($result['return_requested_date'])),
					'view'          => $this->url->link('sale/return/return_form', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $general_url, 'SSL'),
				);
			}
		}
		$data['token'] = $this->session->data['token'];
		$data['getReturnAction']  = $this->load->model_sale_return->getReturnAction();
		$data['getReturnReplace'] = array("RETURN", "REPLACEMENT");
		$data['isDNGenerated'] = array("YES", "NO");

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		// URL For sorting
        $sort_url = $url;

		if (isset($this->request->get['page'])) {
			$sort_url .= '&page=' . $this->request->get['page'];
		}

		// URL for pagination
		$pagination_url = $url;

		$pagination = new Pagination();
		$pagination->total = $return_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/return', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($return_total - $this->config->get('config_limit_admin'))) ? $return_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $return_total, ceil($return_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('sale/return_list.tpl', $data));
	}

	protected function getForm() {
		$this->load->model('localisation/return_reason');
		$this->load->model('localisation/return_action');
		$this->load->model('localisation/return_status');

		$data = array();// Initializing the data array to be passed on to template files
        // Autoloading the lanugage
		$this->load->autoLoadLanguage('sale/return',$data);

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['return_id'])) {
			$data['return_id'] = $this->request->get['return_id'];
		} else {
			$data['return_id'] = 0;
		}

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

		$url = '';

		if (isset($this->request->get['filter_return_id'])) {
			$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/return', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['return_id'])) {
			$data['action'] = $this->url->link('sale/return/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('sale/return/edit', 'token=' . $this->session->data['token'] . '&return_id=' . $this->request->get['return_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('sale/return', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['return_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$return_info = $this->model_sale_return->getReturn($this->request->get['return_id']);
		}

		if (isset($this->request->post['order_id'])) {
			$data['order_id'] = $this->request->post['order_id'];
		} elseif (!empty($return_info)) {
			$data['order_id'] = $return_info['order_id'];
		} else {
			$data['order_id'] = '';
		}

		if (isset($this->request->post['date_ordered'])) {
			$data['date_ordered'] = $this->request->post['date_ordered'];
		} elseif (!empty($return_info)) {
			$data['date_ordered'] = ($return_info['date_ordered'] != '0000-00-00' ? $return_info['date_ordered'] : '');
		} else {
			$data['date_ordered'] = '';
		}

		if (isset($this->request->post['customer'])) {
			$data['customer'] = $this->request->post['customer'];
		} elseif (!empty($return_info)) {
			$data['customer'] = $return_info['customer'];
		} else {
			$data['customer'] = '';
		}

		if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($return_info)) {
			$data['customer_id'] = $return_info['customer_id'];
		} else {
			$data['customer_id'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($return_info)) {
			$data['firstname'] = $return_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($return_info)) {
			$data['lastname'] = $return_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($return_info)) {
			$data['email'] = $return_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($return_info)) {
			$data['telephone'] = $return_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (!empty($return_info)) {
			$data['product'] = $return_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($return_info)) {
			$data['product_id'] = $return_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($return_info)) {
			$data['model'] = $return_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($return_info)) {
			$data['quantity'] = $return_info['quantity'];
		} else {
			$data['quantity'] = '';
		}

		if (isset($this->request->post['opened'])) {
			$data['opened'] = $this->request->post['opened'];
		} elseif (!empty($return_info)) {
			$data['opened'] = $return_info['opened'];
		} else {
			$data['opened'] = '';
		}

		if (isset($this->request->post['return_reason_id'])) {
			$data['return_reason_id'] = $this->request->post['return_reason_id'];
		} elseif (!empty($return_info)) {
			$data['return_reason_id'] = $return_info['return_reason_id'];
		} else {
			$data['return_reason_id'] = '';
		}

		$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();

		if (isset($this->request->post['return_action_id'])) {
			$data['return_action_id'] = $this->request->post['return_action_id'];
		} elseif (!empty($return_info)) {
			$data['return_action_id'] = $return_info['return_action_id'];
		} else {
			$data['return_action_id'] = '';
		}

		$data['return_actions'] = $this->model_localisation_return_action->getReturnActions();

		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (!empty($return_info)) {
			$data['comment'] = $return_info['comment'];
		} else {
			$data['comment'] = '';
		}

		if (isset($this->request->post['return_status_id'])) {
			$data['return_status_id'] = $this->request->post['return_status_id'];
		} elseif (!empty($return_info)) {
			$data['return_status_id'] = $return_info['return_status_id'];
		} else {
			$data['return_status_id'] = '';
		}

		$data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		//$this->response->setOutput($this->load->view('sale/return_form.tpl', $data));
		$this->response->setOutput($this->load->view('sale/new_return_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/return')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['order_id'])) {
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

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/return')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function history() {
		$data = array();
		$this->load->autoLoadLanguage('sale/return',$data);

		$data['error'] = '';
		$data['success'] = '';

		$this->load->model('sale/return');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'sale/return')) {
				$data['error'] = $data['error_permission'];
			}

			if (!$data['error']) {
				$this->model_sale_return->addReturnHistory($this->request->get['return_id'], $this->request->post);

				$data['success'] = $data['text_success'];
			}
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_sale_return->getReturnHistories($this->request->get['return_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $data['text_yes'] : $data['text_no'],
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($data['date_format_short'], strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_return->getTotalReturnHistories($this->request->get['return_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/return/history', 'token=' . $this->session->data['token'] . '&return_id=' . $this->request->get['return_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('sale/return_history.tpl', $data));
	}

	/**
	* Method for Generate Debit Note No
	* @request order_id, seller_id,return_ids
	* @author: Vikas/Sudhanshu, 2016
	*/
	public function generate_debit_note_no(){
		if(empty($this->request->post)){
			return false;
		}

		$this->load->model('sale/return');
		if(isset($this->request->post['return_type']) && $this->request->post['return_type'] == "damaged_by_courier_cmpny" ){
			$this->addCustomDebitNote($this->request->post);
		}
	}

	/**
	 * Public function to geterate custom debit note
	 * @param: $data Array
	 * @return: 
	 * @author: Nishu, Dec 2017
	*/
	public function addCustomDebitNote($data){
		$order_id 	     = $data['order_id'];
		$order_no 	     = $data['order_no'];
		$return_ids      = $data['return_ids'];
		$custom_party_id = $data['select_custom_id'];
		$custom_debit_note_ref   = $data['custom_debit_note_ref'];
		$custom_dn_value = $data['custom_dn_value'];

		$tax_obj = new Tax($this);
		$tax_class_id = $tax_obj->getTaxClassIdFromHSNCode(LOGISTIC_HSN_CODE);
		$tax_rate = $tax_obj->getTaxRate(0, $tax_class_id, array(), 0);

		$suborder_ids = array();
		$seller_ids   = array();
		$token        = $this->session->data['token'];
		$seller_inv   = new SellerInvoice( $this );
		$custom_dn    = new CustomDebitNote( $this );
		
		if(!empty($return_ids)){
			foreach ($return_ids as $return_id) {
				$suborder_id 	= $data['suborder_id_'.$return_id];
				$seller_id      = $data['seller_id_'.$return_id];
				$suborder_ids[]	= $suborder_id;
				$seller_ids[$suborder_id]	= $seller_id;
				$returns[$suborder_id][] = $return_id;
			}
			$dn_amounts = array();
			$dn_value   = array();
			$dn_amounts = $this->model_sale_return->getRetrunProductsAmount($returns);
			$dn_total = array_sum($dn_amounts);
			foreach ($dn_amounts as $suborder_id => $dn_amount) {
				$dn_value[$suborder_id] = (float)($dn_amount / $dn_total) * $custom_dn_value;
			}

			//Get Unique Suborder Ids
			$suborder_ids = array_unique($suborder_ids);
			$files = array();
			foreach ($seller_ids as $seller_id) {
				$invoice_detail = $seller_inv->getSellerInvoices( $order_id , $suborder_ids, $seller_id);
				$invoice_detail = array_combine(array_column($invoice_detail, 'suborder_id'), $invoice_detail);
				if(!empty($invoice_detail)){
					foreach ($invoice_detail as $suborder_id => $invoice_data) {
						if($invoice_data['gst'] != '1'){
							continue;
						}
						$invoice_meta = unserialize($invoice_data['seller_invoice_meta']);
						$returns_ids_data = array(
												'order_id' 		 => $order_id,
												'order_no'		 => $order_no,
												'suborder_id' 	 => $suborder_id,
												'seller_id' 	 => $seller_id,
												'custom_party_id'=> $custom_party_id,
												'custom_debit_note_ref'  => $custom_debit_note_ref,
												'custom_dn_value'=> $dn_value[$suborder_id],
												'return_ids' 	 => $returns[$suborder_id],
												'gstin'          => $invoice_meta['buyer_data']['tin'],
												'tax_rate'       => $tax_rate,
												'buyer_data'     => $invoice_meta['buyer_data']
											);
						
						//Data insert in seller_debit_note and get debit_note_no and generated date
						$debit_note_id = $this->model_sale_return->addCustomDebitNote($returns_ids_data);
						if(!empty($debit_note_id)){
							$file_data = array();
							$file_data['order_no'] = $order_no;
							$file_data['debit_note_id'] = $debit_note_id;
							$enc_data = base64_encode(serialize($file_data));
							$debit_note = new DebitNote($this->registry,$enc_data);
							$dn_data = $debit_note->getDebitNoteData();
							$path = $debit_note->debitNotePdf($dn_data, $dn_data);
							$files[] = DIR_DLOAD_SLR_DBT_NOTE.base64_decode($path);
						}else{
							echo "Custom DebitNote is not generated.";
						}
					}
				}
			}
			if(!empty($files)){
				$mail_data = array();
				$mail_data['files'] = $files;
				$mail_data['order_no'] = $order_no;
				$this->sendMailForCustomDn($mail_data);
			}
		}
		$url  = 'order_id=' . $order_id . '&token=' .$token.'#debit_note_button';
		$this->response->redirect($this->url->link('sale/return/return_form',  $url , 'SSL'));
	}

	/**
	 * @info: To send mail for custom DebitNote
	 * @param: $data Array
	 * @return: Void
	 * @author: Nishu, 31 Jan 2018
	*/
	public function sendMailForCustomDn($mail_data = array()){
		//Create Connection with rabitMQ Server
		$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $queue_name = 'STAGING_GENERAL_TASKS_QUEUE';
            
        if (SITE_ENVIRONMENT == 'Production') {
           $queue_name = 'GENERAL_TASKS_QUEUE';
        }

        // third parameter is for queue durability. we set it to true
        // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
        // passive - false ; exclusive - false; auto-delete - false
        $channel->queue_declare($queue_name, false, true, false, false);

        $data = array(
                        'constant_value' => unserialize(CUSTOMDEBITNOTE),
                        'data_array' => $mail_data
                    );
        $queue_object = base64_encode(serialize($data));

        // delivery_mode = 2 makes message persistent (durable)
        $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
        $channel->basic_publish($msg, '', $queue_name);

        $channel->close(); //Closes Channel
        $connection->close();// Closes Connection
	}


	/**
	 * @info: Method to set customer COD failed Penalty amount
	 * @param:  string $order_no
	 * @param:  int $customer_id
	 * @param:  float $cod_failed_penalty
	 * @return: void
	 * @author: MSA, 11 Sept 2018
	*/
	protected function setCODFailedPenaltyLedgerEntries( int $order_id,
														 string	$suborder_id, 
														 int $customer_id, 
														 float $cod_failed_penalty_from_cod_security,
														 int $credit_note_id
														): void
	{
		$this->load->model('sale/return');
		$this->load->model('account_panel/bankreceipt');
    	$this->load->model('accounts/salesreports');
    	$this->load->model('account_panel/bankpayment');

        $ledger 			 = $this->model_accounts_salesreports->getCustomerLedger( $customer_id );
        $customer            = new Customer($this->registry);
		$customer_detail     = $customer->getCustomerById($customer_id);
		
		// COD Failed Penalty Ledger ID
		$codFailedPenaltyLedgerId = COD_FAILED_PENALTY_LEDGER_ID; 

		// get availabel COD security balance and ReceiptCSV table ids to adjust COD penalty 
		$cod_data 	= ReturnInfo::getCustomerCODSecurityData($this->db, $customer_detail['master_id']); 

		$cod_security_balance 	= $cod_data['cod_security_balance'] ?? 0;
		
		// COD failed penalty greater than remaining advance collected amount, show error
	  	if($cod_failed_penalty_from_cod_security > $cod_security_balance) {
	  		$cod_failed_penalty_from_cod_security = $cod_security_balance;
	  	}	

		if( !empty($ledger) ) // customer ladger must be exis in system
		{
	   	
	   	   $dated = date('Y-m-d');
		   $ledger_id = $codFailedPenaltyLedgerId;
		   
		   //Amount deducted from COD security balance amount 
		   $amount    = (float)$cod_failed_penalty_from_cod_security;
		   
		   $reference = $suborder_id;
		   $mode 	  = '';
		   $user_id   = $this->user->getId();
	       $user_name = $this->user->getUserName()['username'];
	       $datedCM   = date("Y-m-d H:i:s");
	       $user_array = array(
	                    'user_id' 	=> $user_id,
	                    'user_name' => $user_name,
	                    'date' 		=> $datedCM
	                    );

	       //Insert COD Failed Penalty entry in Payment Table	
		   $payment_id = $this->model_account_panel_bankpayment->InsertBankPayments(
					   																$dated, 
					   																$ledger_id, 
					   																$amount, 
					   																$mode, 
					   																$reference, 
					   																$user_id, 
					   																$datedCM, 
					   																$user_array
					   															);
		  if($payment_id) {

		  		$customer_ledger_details = $this->model_account_panel_bankreceipt->getCustomerLedgerDetails( $customer_detail['master_id'] );
				$input_value = $customer_ledger_details['ledger_id']; 
				$group_id 	 = $customer_ledger_details['group_id'];  
				$file_path	 = ''; 
		  	
		  		//Insert COD Failed Penalty entry in Payment Sub Table
		  		$payment_sub_id = $this->model_account_panel_bankpayment->insertPaymentSubForLedger(
																					$payment_id, 
																					$input_value, 
																					$group_id, 
																					$amount, 
																					$file_path, 
																					$user_id, 
																					$datedCM, 
																					$user_array
																				);
		  		// Insert into COD Receipt Table for payment sub table data 
		  		$this->model_account_panel_bankreceipt->setCustomerCODSecurityReceiptTableData(
										                    $customer_detail['master_id'],
										                    'oc_payment_sub',
										                    $payment_sub_id,
										                    $credit_note_id
										                );

		  		// reset COD Security Amount to deducted charges amount
				$this->model_account_panel_bankreceipt->setCustomerCODSecurityBalance( 
                                                         $customer_detail['master_id'], 
                                                         (-1 * $amount)
                                                        );
		 	} // checking for payment id
		} // checking for customer ledger name
	}

	/**
	 * @info: Private method to get COD security amount from min of available security amount and excess COD security amount from advance collected for that CN 
	 * @param:  array
	 * @author: Nishu, May 2019
	*/
	private function getCodSecurityAndPenaltyData($cod_data){
		$data = array();
		$data['cod_failed_penalty'] = 0;
		$data['advance_collected']  = 0;

		if(!empty($cod_data)){

			$customer        = new Customer($this->registry);
			$customer_detail = $customer->getCustomerById($cod_data['customer_id']);
			
			// get availabel COD security balance and ReceiptCSV table ids to adjust COD penalty 
			$cod_bal = ReturnInfo::getCustomerCODSecurityData($this->db, $customer_detail['master_id']); 

			$cod_security_balance = $cod_bal['cod_security_balance'] ?? 0;

			$excess_penalty = (float)($cod_data['cod_failed_penalty'] - $cod_data['advance_collected']);

			$data['cod_security_amount'] = MIN($cod_security_balance, $excess_penalty );
			$data['cod_failed_penalty']  = (float)$cod_data['cod_failed_penalty'] - (float)$data['cod_security_amount'];
		}

		return $data;
	}

	/**
	 * @info: Public method to generate Credit_note
	 * @author: Nishu, July 2018
	*/
	public function generate_credit_note_no(){
		$result = array();
		$this->load->model('sale/return');

	    $return_ids     = implode(",",$this->request->post["return_ids"]);
		$order_no 		= $this->request->post["order_no"];
		$order_id 		= $this->request->post["order_id"];
		$customer_id    = $this->request->post['customer_id'];
		$customer_name  = $this->request->post["customer_name"];
		$customer_email = !empty($this->request->post["customer_email"])?$this->request->post["customer_email"]:'';
		$telephone      = $this->request->post['telephone'];
        $suborder_id 	= $this->request->post["suborder_id"];
        $seller_id 		= $this->request->post["seller_id"];
        $seller_inv     = new SellerInvoice( $this );
		$invoice_data   = $seller_inv->getInvoiceInfo( $order_id , $suborder_id, $seller_id);
		
		$invoice_meta = unserialize($invoice_data['seller_invoice_meta']);
		$gstin = $invoice_meta['buyer_data']['tin'];

		$shipping_charges   = ROUND((float)($this->request->post["shipping_charges"]   ?? 0), 2);
        $reversal_shipping  = ROUND((float)($this->request->post["reversal_shipping"]  ?? 0), 2);
        $cod_failed_penalty = ROUND((float)($this->request->post["cod_failed_penalty"] ?? 0), 2);

        if($shipping_charges < 0){
        	$result["success"] = "error";
			$result["error"]   = "Shipping charges cannot be negative.";

			//Json Response to ajax call
			echo json_encode($result); exit();
        }else if($cod_failed_penalty < 0){
        	$result["success"] = "error";
			$result["error"]   = "COD Failed Penalty cannot be negative.";

			//Json Response to ajax call
			echo json_encode($result); exit();
        }

        //Refund Process
        $refund_onhold = !empty($this->request->post["refund_onhold"])? $this->request->post["refund_onhold"]:'NO';
        
        $other_charges = isset($this->request->post["other_charges"])? $this->request->post["other_charges"]:0;

        //Initialize default values 
        $cod_failed_penalty_from_cod_security = $advance_collected = $cash_discount = $less_cash_discount = 0.00;

        
        //Get Return COD failed reason status
		$is_cod_failed = $this->model_sale_return->isCODFailed($return_ids);

		if($is_cod_failed){

			//Advance and cash discount calculation for given order_id and suborder_id
			$advanced 	=  AdvanceVoucherLib::getTotalAdvanceOfSuborderPaymentGatewayWise($this->db, $order_id, $suborder_id, false); 
			
			$advance_collected  = ROUND((float)$advanced['advance_collected'], 2);
			$cash_discount      = ROUND((float)$advanced['cash_discount'], 2);
			$less_cash_discount = (float)$cash_discount;
			
			//Check if COD failed penalty excess from advance collected
			if($cod_failed_penalty > $advance_collected) {

				//Check for COD security Amount for excess COD failed amount from advance against that suborder_id
				$cod_data = array();
				$cod_data['advance_collected']  = $advance_collected;
				$cod_data['cod_failed_penalty'] = $cod_failed_penalty;
				$cod_data['customer_id']        = $customer_id;
				$cod_data = $this->getCodSecurityAndPenaltyData($cod_data);

				$cod_failed_penalty_from_cod_security = $cod_data['cod_security_amount'] ?? 0;

				//Set $cod_failed_penalty value calculate after adjusting excess amount form COD_security
				$cod_failed_penalty = $cod_data['cod_failed_penalty'] ?? 0;
			}

		}

		$cn_data = array(
                        'order_id'              => $order_id,
                        'suborder_id'           => $suborder_id,
                        'return_ids'            => $return_ids,
                        'shipping_collected'    => $shipping_charges,
                        'cod_failed_penalty'    => $cod_failed_penalty,
                        'advance_collected'     => (float)$advance_collected,
                        'cash_discount'         => (float)$cash_discount,
                        'less_cash_discount'    => (float)$less_cash_discount,
                        'reversal_shipping'     => trim($reversal_shipping),
                        'payment_cleared'       => $refund_onhold,
                        'other_charges'         => $other_charges,
                        'gstin'                 => $gstin,
                        'is_cod_failed'         => (int)$is_cod_failed
					   );
		

		//Generate Credit Note
		$credit_note = $this->model_sale_return->addCreditNote($cn_data);

		if(!empty($credit_note)){
			$cn_id = (int)$credit_note['credit_note_id'];

			/*--- Start block check for COD failed reason and generate COD failed ledger entries */
			if( $is_cod_failed  && $cod_failed_penalty_from_cod_security > 0) {
				$this->setCODFailedPenaltyLedgerEntries(   $order_id,
														   $suborder_id,
														   $customer_id, 
														   $cod_failed_penalty_from_cod_security,
														   $cn_id
														);
			}
			/* --- End block check for COD failed reason and generate COD failed ledger entries */

			$encode_file['order_id']       = $order_id;
			$encode_file['credit_note_id'] = $cn_id;
			$encode_file                   = base64_encode(serialize($encode_file));

			$credit_noteLink = $this->securefiledownload->getDownloadLink('buyer_credit_note',$encode_file, false);
		
			$result["success"] = "success";
			$result["credit_note_download_link"] = $credit_noteLink;

			////////////////Send Mail for CreditNote/////////////////

			//Create Attachment file for sending mail
			$credit_note = new CreditNote($this->registry,$encode_file);

			//Calculate and update credit_note amount
			$credit_note->calculateAndUpdateCreditNoteAmount((int)$cn_id);
			
			$cn_data     = $credit_note->getCreditNoteData();
			
	        $path        = $credit_note->creditNotePdf($cn_data);

	        $cn_data     = $credit_note->getCreditNoteData();
			
	        $cn_data['credit_note_info']['net_refundable'] = $credit_note->getCnNetRefundable($cn_id);

			$files[] = DIR_DLOAD_BYR_CDT_NOTE.base64_decode($path);

			$returnClassObj = new ActionCnForClient($this);
			
			$cn_no = '';
			if(!empty($cn_data['credit_note_info']['credit_note_no'])){
				$cn_no = $cn_data['credit_note_info']['credit_note_prefix'].$cn_data['credit_note_info']['credit_note_no'];
			}
			$cn_amount = '0';
			if(!empty($cn_data['credit_note_info']['net_refundable'])){
				$cn_amount = $cn_data['credit_note_info']['net_refundable'];
			}

			$customer            = new Customer($this->registry);
			$customer_detail     = $customer->getCustomerById($customer_id);

			$ctoken = '';
			if(!empty($customer_detail)){
				$ctoken = $customer_detail['ws_access_token'];
			}

		// Send Email and SMS to customer, IF is_cod_failed = 0 and net_refundable > 0	

		if(	$cn_data['credit_note_info']['is_cod_failed'] == 0 
				&& 
			$cn_data['credit_note_info']['net_refundable'] > 0
		) {

			//Prepare Mail related data
			$mail_data = array();
			
			$mail_data['order_id']        = $order_id;
			$mail_data['order_no']        = $order_no;
			$mail_data['return_ids']      = $return_ids;
			$mail_data['customer_id']     = $customer_id;
			$mail_data['attachments']     = $files;
			$mail_data['cn_no']           = $cn_no;
			$mail_data['cn_amount']   	  = $cn_amount;
			$mail_data['bank_detail_url'] = HTTPS_CATALOG.'index.php?route=account/bank_details&ctoken='.$ctoken;
			$mail_data['customer'] = array(
										'id'     => $customer_id,
										'name'   => $customer_name,
										'email'  => $customer_email,
										'phone'  => $telephone
									 );

			//Send Email To Customer
			$returnClassObj->sendEmail($mail_data);

			//Send SMS and PushNotification To Customer
			$returnClassObj->sendPushNotification($mail_data);

		}	

		}else{
			$result["success"] = "error";
			$result["error"]   = "CreditNote is not generated. Try again after some time, If still got error 'Contact to Administrator.'";
		}
		//Json Response to ajax call
		echo json_encode($result);exit();
	}

	/**
	* @info:  Method for Cancel debit note pdf download
	* @request: $debit_note_no: Integer of Debit note no
	* @output: null
	* @author: Vikas, 2016
	*/
	public function cancelDebitNoteDownloadPdf(){
		$this->load->model('sale/return');

		if($this->request->get['debit_note_id']){
			$debit_note_id = $this->request->get['debit_note_id'];
		}else{
			$debit_note_id = NULL;
		}

		$order_no = $this->request->get['order_no'];
		$order_id = $this->request->get['order_id'];
		$dn_type  = $this->request->get['dn_type'];
		$result = $this->model_sale_return->cancelDebitNoteDownloadPdf($debit_note_id, $dn_type);

		if(!empty($result)){

			//Create Attachment file for sending mail
			$file_data = array();
			$file_data['order_no']      = $order_no;
			$file_data['debit_note_id'] = $debit_note_id;

			$enc_data = base64_encode(serialize($file_data));
			$debit_note = new DebitNote($this->registry,$enc_data);
			$dn_data = $debit_note->getDebitNoteData();

			//This section is not executed for Custom DN Cancellation
			if(!empty($dn_data['debit_note_info'])){
				$debit_note_info = $dn_data['debit_note_info'];
				$seller_id    = $dn_data['debit_note_info']['seller_id'];
				$seller_info  = SellerInfo::getSellersBySellerIds($this->db, array($seller_id)); 

				$return_ids   = array_column($dn_data['product_data'], 'return_id');
				$master_return_ids = array_unique(array_column($dn_data['product_data'], 'master_return_id'));
				
				$seller_email = '';
				if(!empty($seller_info[$seller_id])){
					$seller_email = $seller_info[$seller_id]['email'];
				}

				// get calculated prdouct totals
	            $seller_invo_obj = new SellerInvoice($this->registry);
	            $return_calculated_data = $seller_invo_obj->calculationProducts($this->registry, '', '', '', $dn_data['product_data']);

				$path = $debit_note->debitNotePdf($return_calculated_data, $dn_data);

				$files[] = DIR_DLOAD_SLR_DBT_NOTE.base64_decode($path);

				if(strtolower($dn_type) == "seller"){
					$returnClassObj = new ActionDnCancelled($this);
					$seller_details = $seller_info[$seller_id];

					//Remove seller's primary emailId from additional_email_ids
					$seller_details['additional_emails'] = array_flip( $seller_details['additional_emails']);
					unset($seller_details['additional_emails'][$seller_email]);
					$seller_details['additional_emails'] = array_flip( $seller_details['additional_emails']);

					//Prepare Mail related data
					$mail_data = array();
					
					$mail_data['order_id']                 = $order_id;
					$mail_data['order_no']                 = $order_no;
					$mail_data['seller_id']                = $seller_id;
					$mail_data['ws_gcm_registration_id']   = $seller_details['ws_gcm_registration_id'];
					$mail_data['return_id']                = implode(',', $return_ids);
					$mail_data['master_return_id']         = implode(',', $master_return_ids);
					$mail_data['attachments']              = $files;
					$mail_data['debit_note_prefix']        = $debit_note_info['debit_note_prefix'];
					$mail_data['debit_note_no']            = $debit_note_info['debit_note_no'];
					$mail_data['debit_note_amount']        = $debit_note_info['debit_note_amount'];
					$mail_data['debit_note_date']          = $debit_note_info['debit_note_date'];
					$mail_data['id']                       = $seller_id;
					$mail_data['company']                  = $seller_details['company'];
					$mail_data['email']                    = $seller_email;
					$mail_data['additional_emails']        = $seller_details['additional_emails'];
					
					//Send Email To Seller
					$returnClassObj->sendEmail($mail_data);
				}
			}
			//Send Successfully Cancelled message
			$status = 'DebitNote Successfully Cancelled.';
		}else{
			$status = 'DebitNote cancellation failed.';
		}

		$response = array('status' => $status);
		echo json_encode($response);
		exit();
	}

	/** 
	 * Method for Cancel credit note pdf download
	 * @param: $credit_note_no: Integer
	 * @author: Nishu, 2018
	*/
	public function cancelCreditNote(){
		$this->load->model('sale/return');

		$status = "CreditNote Cancellation Failed.";

		$order_id       = 0;
		$order_no       = 0;
		$credit_note_id = NULL;
		$customer_email = '';
		$customer_name  = '';
		$telephone      = '';
		$customer_id    = 0; 
		$mail_data      = array();

		if(!empty($this->request->get['cn_id'])) {

			$order_id       = $this->request->get['order_id'];
			$order_no       = $this->request->get['order_no'];
			$credit_note_id = $this->request->get['cn_id'];
			$customer_id    = $this->request->get['customer_id'];
			$customer_name  = $this->request->get['name'];
			$customer_email = $this->request->get['email'];
			$telephone      = $this->request->get['telephone'];

			//Mark CN as cancelled
			$result = $this->model_sale_return->cancelCreditNote($credit_note_id);

			if(!empty($result)){

				//Mark Tentative refund as rejected, After Canceling CN
				$this->markTentativeRefundRejected($credit_note_id);

				$encode_file                   = array();
				$encode_file['order_id']       = $order_id;
				$encode_file['credit_note_id'] = $credit_note_id;
				$encode_file                   = base64_encode(serialize($encode_file));

				$credit_noteLink = $this->securefiledownload->getDownloadLink('buyer_credit_note',$encode_file, false);
				
				//Create Attachment file for sending mail
				$credit_note = new CreditNote($this->registry,$encode_file);

				//calculate and update cancelled CN's amount(net_refundable) and distribute amount to NACH schedules
				$is_canceled = 1;
				$credit_note->calculateAndUpdateCreditNoteAmount((int)$credit_note_id, $is_canceled);

				$cn_data     = $credit_note->getCreditNoteData();
		        $path        = $credit_note->creditNotePdf($cn_data);

				$files[] = DIR_DLOAD_BYR_CDT_NOTE.base64_decode($path);

				$returnClassObj = new ActionCnCancelled($this);

				$mail_data['order_id']    = $order_id;
				$mail_data['order_no']    = $order_no;
				$mail_data['customer_id'] = $customer_id;
				$mail_data['cn_id']    	  = $cn_data['credit_note_info']['credit_note_id'];
				$mail_data['cn_no']    	  = $cn_data['credit_note_info']['credit_note_prefix'].$cn_data['credit_note_info']['credit_note_no'];
				$mail_data['cn_amount']   = $cn_data['credit_note_info']['net_refundable'];
				$mail_data['attachments'] = $files;
				$mail_data['customer'] = array(
											'id'     => $customer_id,
											'name'   => $customer_name,
											'email'  => $customer_email,
											'phone'  => $telephone
										 );

				//Send Email To Customer
				$returnClassObj->sendEmail($mail_data);

				//Send SMS and PushNotification To Customer
				$returnClassObj->sendPushNotification($mail_data);

				$status = "CreditNote Cancelled Successfully.";
			}
		}

		$response = array('status' => $status);
		echo json_encode($response);
		exit();
	}

	/**
	 * @info: Public method to mark tentative refund as Rejected
	 *         Only if tentative refund for that CN must not be rejected before this
	 * @author: Nishu, May 2018
	*/
	public function markTentativeRefundRejected($credit_note_id){
		
		if(!empty($credit_note_id)){
			
			$data = array();
			$data['ref_id']      = $credit_note_id;
			$data['refund_type'] = "CREDIT_NOTE";

			//Get TentativeRefundId from Database
			$tr_data = TentativePaymentRefund::getTentativeIdByRefIdAndType($this->db, $data);
			if(!empty($tr_data) && isset($tr_data['is_approved']) && $tr_data['is_approved'] != 2){
				$data['id']      = $tr_data['id'];
				$data['comment'] = "Tentative Refund is auto cancelled, because of CreditNote is cancelled.";
				$data['is_approved'] = 2;

				//Load Model
				$this->load->model('sale/tentative_refund');

				//Mark Tentative refund as cancelled
				$this->model_sale_tentative_refund->updateIsApprovedTentativeRefund($data);
			}
		}
	}

	/**
	 * @info: public method to get email retricted action
	 * @author: MSA, August 2018
	*/
	public function getMailRestrictionActions()
	{
		return  [
					RETURN_ACTION_IDS['DN_Generated_For_Seller']
				];
	}

	/**
	 * @info: public method to get return customer id
	 * @author: MSA, August 2018
	*/
	public function getChangeReturnCustomerId($returns = array())
	{
		$customer_id = 0;
		if(!empty($returns))
		{
			$customer_id = array_unique(array_column($returns, 'customer_id'));
			if(!empty($customer_id)){
				return trim($customer_id[0]);
			}
		}
		return $customer_id;
	}

	/**
	 * @info: public method to check return action for DN to seller
	 * @author: MSA, August 2018
	*/
	public function IsDnToSeller($return = array())
	{
		if(!empty($return))
		{
			return ($return['return_action_id'] == RETURN_ACTION_IDS['DN_Generated_For_Seller'])
					? 1
					: 0;
		}
		return 0;
	}

	/**
	 * @info: public method to check return action for Replacement Note
	 * @author: MSA, August 2018
	*/
	public function isReplacementNote($return = array())
	{
		if(!empty($return))
		{
			return ($return['return_action_id'] == RETURN_ACTION_IDS['Replacement_Note'])
					? 1
					: 0;
		}
		return 0; 
	}

	/**
	 * @info: public method to check return action for Manually Generated Reverse Shipment
	 * @author: MSA, August 2018
	*/
	public function isManuallyGeneratedReverseShipment($return = array())
	{
		if(!empty($return))
		{
			return ($return['return_action_id'] == RETURN_ACTION_IDS['Manually_Generated_Reverse_Shipment'])
					? 1
					: 0;
		}
		return 0;
	}

	/**
	 * @info: public method to set return data for action - DN for Seller 
	 * @author: MSA, August 2018
	*/
	public function getDnToSellerData(&$dn_data, $returnAction, $seller_info, $return = array())
	{
		if(!empty($return))
		{
			$so_id                    = $return['suborder_id'];
			$seller_id                = $return['seller_id'];
			$seller_invoice_id        = $return['seller_invoice_id'];
			$nickname                 = $return['seller_nickname'];
			$company                  = $return['seller_company'];
			$dn_data[$so_id][$seller_invoice_id]['order_id']     = $return['order_id'];
			$dn_data[$so_id][$seller_invoice_id]['order_no']     = $return['order_no'];
			$dn_data[$so_id][$seller_invoice_id]['suborder_id']  = $so_id;
			$dn_data[$so_id][$seller_invoice_id]['seller_id']    = $seller_id;
			$dn_data[$so_id][$seller_invoice_id]['seller_invoice_id']    = $seller_invoice_id;
			$dn_data[$so_id][$seller_invoice_id]['email']        = $seller_info[$seller_id]['email'];
			$dn_data[$so_id][$seller_invoice_id]['additional_emails'] = $seller_info[$seller_id]['additional_emails'];
			$dn_data[$so_id][$seller_invoice_id]['nickname']     = $nickname;
			$dn_data[$so_id][$seller_invoice_id]['company']      = $company;
			$dn_data[$so_id][$seller_invoice_id]['action_class'] = $returnAction[$return['return_action_id']]['class_name'];
			$dn_data[$so_id][$seller_invoice_id]['op_ids'][]     = $return['order_product_id'];
			$dn_data[$so_id][$seller_invoice_id]['return_ids'][] = $return['return_id'];
		}
	}

	/**
	 * @info: public method to set return data for action - Replacement Note to seller 
	 * @author: MSA, August 2018
	*/
	public function getReplacementNoteToSellerData(&$replacement_note_data, $returnAction, $seller_info, $return = array())
	{
		if(!empty($return))
		{
			$so_id                    = $return['suborder_id'];
			$seller_id                = $return['seller_id'];
			$seller_invoice_id        = $return['seller_invoice_id'];
			$nickname                 = $return['seller_nickname'];
			$company                  = $return['seller_company'];
			$replacement_note_data[$so_id][$seller_invoice_id]['order_id']     = $return['order_id'];
			$replacement_note_data[$so_id][$seller_invoice_id]['order_no']     = $return['order_no'];
			$replacement_note_data[$so_id][$seller_invoice_id]['suborder_id']  = $so_id;
			$replacement_note_data[$so_id][$seller_invoice_id]['seller_id']    = $seller_id;
			$replacement_note_data[$so_id][$seller_invoice_id]['seller_invoice_id']    = $seller_invoice_id;
			$replacement_note_data[$so_id][$seller_invoice_id]['email']        = $seller_info[$seller_id]['email'];
			$replacement_note_data[$so_id][$seller_invoice_id]['additional_emails'] = $seller_info[$seller_id]['additional_emails'];
			$replacement_note_data[$so_id][$seller_invoice_id]['nickname']     = $nickname;
			$replacement_note_data[$so_id][$seller_invoice_id]['company']      = $company;
			$replacement_note_data[$so_id][$seller_invoice_id]['action_class'] = $returnAction[$return['return_action_id']]['class_name'];
			$replacement_note_data[$so_id][$seller_invoice_id]['op_ids'][]     = $return['order_product_id'];
			$replacement_note_data[$so_id][$seller_invoice_id]['return_ids'][] = $return['return_id'];
		}
	}

	/**
	 * @info: public method to check return email status
	 * @author: MSA, August 2018
	*/
	public function isReturnEmail($returnInfo, $change_return)
	{
		  if(
				!in_array($change_return['return_action_id'], $this->getMailRestrictionActions())
	    			&&
		    	!empty($returnInfo['buyer_invoice_id'])
		    	    &&
		    	!empty($returnInfo['buyer_invoice_no'])
		    	    &&
		    	(int)$returnInfo['order_status_id'] != ORDER_STATUS['Canceled']
		    )
			{
				return 1;
			}

		return 0;
	}

	/**
	 * @info: public method to get tracking url for shipment id
	 * @author: MSA, August 2018
	*/
	public function getTrackingUrls($shipment_tracking_ids)
	{
		$tracking_urls = array();
		if(!empty($shipment_tracking_ids)) {
			$this->load->model('sale/return');
			$obj = new ReturnActionBase();
			$tracking_data = $this->model_sale_return->getCourierCompanyTrackingUrls($shipment_tracking_ids);
			if(!empty($tracking_data)) {
				foreach ($tracking_data as $tracking) {
					if(!empty($tracking['tracking_url'])) {
						$short_url = $obj->convertUrlToShortUrl($tracking['tracking_url'] . $tracking['tracking_no']);
						if(!empty($short_url['message'])) {
							$tracking_urls[] = array(
								'tracking_no' => $tracking['tracking_no'],
								'tracking_url' => $short_url['message']
							);
						}
					}
				}
			}
		}
		return $tracking_urls;
	}

	/**
	 * @info: public method to get return email data
	 * @author: MSA, August 2018
	*/
	public function getReturnEmailData($email_data = array())
	{	
		$return_email_data 	= array();
		$customer 			= array();
		$seller 	 		= array();
		$action_ids 		= array();
		$shipment_tracking_ids  = array_filter(array_unique(array_column($email_data, 'return_shipment_tracking_id')));
		if( !empty($email_data) )
		{
			foreach ($email_data as $key => $value) {

				$return_email_data['order_id']		= $value['order_id'];
				$return_email_data['order_no']		= $value['form_data']['order_no'];
				$return_email_data['customer_id']	= $value['customer_id'];
				$return_email_data['action_id']		= isset($value['change_action']['return_action_id']) 
														? $value['change_action']['return_action_id']
														: 0 ;
				$return_email_data['invoice_status']= isset($value['invoice_status']) ? $value['invoice_status'] : 0;
					
				$return_email_data['cn_no']			= isset($value['form_data']['credit_note_no'])
														? $value['form_data']['credit_note_no']
														: '';

				$return_email_data['cn_amount']		= isset($value['form_data']['credit_note_amount'])
														? $value['form_data']['credit_note_amount']
														: '';										
				$helpdesk_ticket_id 				= isset($value['form_data']['helpdesk_ticket_id'])
														? $value['form_data']['helpdesk_ticket_id']
														: '';

				$return_email_data['returns'][$key] = array(
					'return_id' 			=> $value['return_id'],
					'master_return_id'		=> $value['master_return_id'],
					'helpdesk_ticket_id'	=> $helpdesk_ticket_id,
					'order_product_id'		=> $value['order_product_id'],
					'seller_id'				=> $value['seller_id'],
					'image'					=> $value['image'],
					'model'					=> $value['model'],
					'seller_sku'			=> $value['seller_sku'],
					'return_reason'			=> $value['return_reason']['name'],	
					'return_type'			=> $value['return_reason']['reason_type'],
					'return_quantity'		=> $value['form_data']['return_quantity'],
					'actual_quantity'		=> $value['return_quantity'],
					'comment'				=> $value['return_comment']
				);
				if(!empty($value['relisted_product'])) {
					$return_email_data['relisted'][$key] = array(
					'return_id' 			=> $value['return_id'],
					'master_return_id'		=> $value['master_return_id'],
					'order_product_id'		=> $value['order_product_id'],
					'product_id'			=> $value['relisted_product']['product_id'],
					'seller_id'				=> DUMMY_SELLER_ID,
					'image'					=> $value['image'],
					'sku'					=> $value['relisted_product']['sku'],
					'model'					=> $value['relisted_product']['model'],
					'return_reason'			=> $value['return_reason']['name'],	
					'return_type'			=> $value['return_reason']['reason_type'],
					'return_quantity'		=> $value['form_data']['return_quantity'],
					'actual_quantity'		=> $value['return_quantity'],
					'piece_in_set'			=> $value['relisted_product']['piece_in_set'],
					'internal_note'			=> $value['form_data']['internal_note']
				);
				}
				$customer = array();
				if(!empty($value['customer'])){
					$customer = array(
						'id'		=> (!empty($value['customer']['customer_id']))?$value['customer']['customer_id']:'',
						'name'		=> (!empty($value['customer']['firstname']))?$value['customer']['firstname']:'',
						'email'		=> $value['customer']['email'] ?? '',
						'phone'		=> $value['customer']['telephone'] ?? '',
						'ws_access_token' => $value['customer']['ws_access_token'] ?? '',
					);
				}
				$seller_data = $value['seller'][$value['seller_id']];
				$additional_emails = array_flip($seller_data['additional_emails']);
				unset($additional_emails[$seller_data['email']]);
				$additional_emails = array_flip($additional_emails);
				$seller[$seller_data['seller_id']] = array(
					'id'		             => $seller_data['seller_id'] ?? 0,
					'name'		             => $seller_data['company'] ?? $seller_data['nickname'],
					'email'		             => $seller_data['email'] ?? '',
					'phone'		             => $seller_data['mobile_no'] ?? '',
					'company'	             => $seller_data['company'] ?? '',
					'ws_gcm_registration_id' => $seller_data['ws_gcm_registration_id'] ?? '',
					'additional_emails'      => $additional_emails
				);
			}
			$return_email_data['customer'] 	= $customer;
			$return_email_data['seller'] 	= $seller;
			$return_email_data['tracking']  = '';
			if(in_array($return_email_data['action_id'], 
						array(
								RETURN_ACTION_IDS['Goods_Picked_Up']
							)
					   )
			){
				if(!empty($shipment_tracking_ids)) {
					$return_email_data['tracking']  = $this->getTrackingUrls($shipment_tracking_ids);	
				}
			}
		}
		return $return_email_data;
	}

	/**
	 * @info: public method to set data for relisted return product on wsb action
	 * @author: MSA, August 2018
	*/
	protected function relistedReturnProductOnWSB($returnInfo, $change_return)
	{
		$copy_product = array();
		if(!empty($returnInfo)) {

			$this->load->model('catalog/product');
			$product_id 		= $returnInfo['product_id'];
			$order_product_id 	= $returnInfo['order_product_id'];
			$quantity 			= $change_return['return_quantity'];
			$shipping_method_id	= $change_return['shipping_method_id'];

			$seller_info = array();
			if(!empty($returnInfo['seller_invoice_id'])) {
				$seller_info = $this->model_catalog_product->getWsbSellerBySellerInvoiceId($returnInfo['seller_invoice_id']);	
			}else{
				$seller_info = $this->model_catalog_product->getWsbSellerBySellerInvoiceId();
			}
			
			$product_option_value_id = $this->model_catalog_product->getOptionValueIdByOrderProductId($order_product_id);
			
			$model = $seller_info['nickname'] . '_' . $returnInfo['seller_sku'].'_'. $returnInfo['return_id'];

			$sku = $change_return['order_no'] . $seller_info['nickname'] . $returnInfo['return_id'] . '_' . $returnInfo['seller_sku'];

			$return_product = array(
							'seller_id'	    => $seller_info['seller_id'],
							'sku'           => $sku,
							'model'         => $model
						);
			$copy_product_id = $this->model_catalog_product->copyProduct($product_id,array(),$quantity,$product_option_value_id,$return_product);
			$copy_product = $this->model_catalog_product->getProduct($copy_product_id);

			/*
				Insert a row in admin change log for WSB relisted product
              	Set Data to add into admin_change_log
			*/
               $admin_change_data                  = array();
               $admin_change_data['table_id']      = (int)$copy_product_id;
               $admin_change_data['user_id']       = $this->user->getId();
               $admin_change_data['name']          = $this->user->getUserName($this->user->getId())['name'];
               $admin_change_data['username']      = $this->user->getUserName($this->user->getId())['username'];
               $admin_change_data['table_name']    = 'oc_product';
               $admin_change_data['source_field']  = 'return_product';
               $admin_change_data['field_name']    = 'wsb_books_relisted';
               $admin_change_data['ref_url']       = '';
               $admin_change_data['old_value']     = $product_id;
               $admin_change_data['new_value']     = $copy_product_id;
               $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
               $admin_change_data['ip_address']    = $this->request->getIpAddress;
               $admin_change_data['file_location'] = '';
               $admin_change_data['comment'] 	   = 'Return Penal - Action :: Goods Taken on WSB and Relisted';
               $admin_change_data['user_type']     = $this->user->getGroupName();
               //Call dynamic static function for entry into admin change log
               CommonLib::addAdminChangeLog($this->db, $admin_change_data);
			/*
				Insert a row in admin change log
			*/
		}
		return $copy_product;
	}

	/**
	 * @info: public method to update return action id
	 * @author: MSA, August 2018
	*/
	public function change_status(){

		if(!empty($this->request->post['params'])) {

			//Load return model 
			$this->load->model('sale/return');
		
			//Get Return Aciton Reasons
			$return_action_reasons = $this->model_sale_return->getActionReason();
			
			$return_action_reasons = array_combine(
									array_column($return_action_reasons, 'id'),
									array_column($return_action_reasons, 'action_reason_name')
								);
		
			$params         = html_entity_decode($this->request->post['params']);
			$returns        = json_decode($params,true); 

			$returnInfoObj  = new ReturnInfo($this->registry);
			$obj            = new ReturnActionBase($this);
			$saveReturn     = array();
			$dn_data        = array();
			$replacement_note_data  = array();
			$email_data		= array();
			$opids          = array();
			$product_images = array();
			$relisted_product = array();
			$manually_reverse_shipment = array();

			if(!empty($returns)) 
			{  
				$selector = array(
                        'oc_return'        => array('select' => array()) ,
                        'oc_return_reason' => array( 'select' => array()),
                        'oc_return_action' => array( 'select' => array()),
                        'oc_order_product' => array( 'select' => array()),
                        'oc_suborder'      => array( 'select' => array('invoice_no as buyer_invoice_no', 'order_status_id'))
                       );
				$return_data = array();
				
				$return_data['return_ids'] = implode(',', array_column($returns, 'return_id'));
				//Returns all order product returns for $data['order_id']
	            $returns_details = $returnInfoObj->getReturnInfo($this->db, $return_data, $selector);

	            $seller_id = array_unique(array_column($returns_details['oc_return'], 'seller_id'));

	            $seller_info = SellerInfo::getSellersBySellerIds($this->db, $seller_id);

				$customer_id = !empty($this->request->post['customer_id']) 
								? $this->request->post['customer_id']
								: 0;
	            
	            $customer_columns = "customer_id,master_id,firstname,lastname,email,mobile_country_code,
	            					 telephone,address_id,ws_access_token,customer_access_token,helpdesk_id
	            					";
			    $customerInfo   = $this->model_sale_return->getCustomer($customer_id,$customer_columns);
				$returnAction 	= $returnInfoObj->getReturnAction();
				 
				$bulk_returns = array();
				$bulk_return_ids = array();

				foreach($returns as $change_return)
				{ 

					if(empty($change_return['return_action_id']) || empty($change_return['return_quantity'])){
						continue;
					}

					$change_return['internal_note'] = rawurldecode($change_return['internal_note']);
					
					$return_id = 0;
					if(!empty($change_return['return_id'])){
						$return_id = $change_return['return_id'];
					}
					
					if(!empty($returns_details['oc_return'][$return_id]))
					{
						$returnInfo 	= $returns_details['oc_return'][$return_id];
						
						if($returnInfo['active_row'] != 1){
							continue;
						}
						$change_return['order_product_id'] = $returnInfo['order_product_id'];
						$returnReason   = $returnInfo['return_reason'];

						//Set return data to save
						$saveReturn = $this->setSaveReturnData($returnInfo, $change_return);

						//If new return action Id is for generating DN for Seller
						if( $this->IsDnToSeller($change_return) )
						{	
							$this->getDnToSellerData($dn_data, $returnAction, $seller_info, $change_return);

						}else if( $this->isReplacementNote($change_return) ) {	

							$this->getReplacementNoteToSellerData($replacement_note_data, $returnAction, $seller_info, $change_return);

						}else if( $this->isManuallyGeneratedReverseShipment($change_return) ){

						/* Manually Reverse Shipment action handling */
				 			$returnInfo['courier_name'] = $change_return['manually_shipment_courier'];
				 			$returnInfo['docket'] 		= $change_return['manually_shipment_docket'];
				 			$returnInfo['warehouse_id'] = $change_return['manually_shipment_warehouse'];
				 			$manually_reverse_shipment[]= $returnInfo;

				 		}else{
							/* Re-listed Product on WSB (Goods_Taken_On_WSB_Books_And_Relisted) */
							if( $change_return['return_action_id'] == RETURN_ACTION_IDS['Goods_Taken_On_WSB_Books_And_Relisted'])
						 	{
						 		$relisted_product = $this->relistedReturnProductOnWSB($returnInfo,$change_return);
						 		$saveReturn['relisted_product_id'] = (int)$relisted_product['product_id'];
						 	}
						 	/* Re-listed Product on WSB */

						  	//Entery to oc_return with updated return action
						  	//$obj->insertReturn($saveReturn);  
						  	$bulk_returns[] = $saveReturn;

						  	//Update Old Return with active_row as 0
						  	//$obj->resetReturnActiveStatusForReturnIds(0, $return_id);
						  	$bulk_return_ids[] = $return_id;
						}

						if( in_array($change_return['return_action_id'], array(
												RETURN_ACTION_IDS['Return_Request_Rejected'],
												RETURN_ACTION_IDS['Replacement_Request_Rejected'],
												RETURN_ACTION_IDS['Return_Goods_Rejected']
												)
							)
						){
					 		//Change EDIT_TYPE in oc_order_product for REJECTED Actions(For Processed)
							$this->changeEditTypeForRejectedReturns($returnInfo, $change_return);
					 	}
					
					    /*Preparing email list*/	
						$returnInfo['change_action']              = $returnAction[$change_return['return_action_id']];
					    $returnInfo['form_data'] 	              = (array)$change_return;
					    $returnInfo['form_data']['internal_note'] = $change_return['internal_note'];

					    if(!empty($change_return['return_action_reason_id'])){

					    	$return_action_reason = isset($return_action_reasons[trim($change_return['return_action_reason_id'])])
					    															? $return_action_reasons[trim($change_return['return_action_reason_id'])]
					    															: '';
					    	$returnInfo['form_data']['internal_note'] = $return_action_reason . ',  ' . $returnInfo['return_comment'];
					    }

					    $returnInfo['customer'] = $customerInfo;

					    $returnInfo['seller'] 	= $seller_info; //[$returnInfo['seller_id']]; 

					    $returnInfo['invoice_status'] = $this->isReturnEmail($returnInfo, $change_return);

						$returnInfo['relisted_product'] = $relisted_product;

					    if(!$this->IsDnToSeller($change_return) )
						{
					    	$email_data[$change_return['return_action_id']][$return_id] = $returnInfo;
					    	$opids[]  =  $returnInfo['order_product_id'];
						}
					}
				}

				/*new code to insert bluk returns and updates old status*/
				$obj->insertReturnMultiple($bulk_returns);
				$update_return_ids = implode(',', $bulk_return_ids);
				$obj->resetReturnActiveStatusForReturnIds(0, $update_return_ids);	
				
			}
			
			/* Save Manually Reverse Shipment action data */	
			if(!empty($manually_reverse_shipment)) {
				$this->addManuallyReverseShipment($manually_reverse_shipment);	
			}
			/* Save Manually Reverse Shipment action data */	

			//get product images list
			if(!empty($opids)) {
		    	MsProduct::setProductImages($this, $opids, $product_images);
			}

			/* Strat sending email && SMS*/
			$this->sendMailForChangeReturnAction($email_data, $product_images);
			/* End sending email */

			$error_msg = '';
			$download_dn = '';
			//return_ids not empty for generating DN for Seller
			if(!empty($dn_data)){
				foreach ($dn_data as $so_id => $datas) {
					foreach ($datas as $seller_invoice_id => $dn) {
						if(!empty($dn['return_ids'])){
							//Generate DN for Seller automatic suborder and seller wise
							$result = $this->generateDnForSeller($dn);
							if(!empty($result['status'])){
								$dn['attachments']       = $result['files'];
								$dn['debit_note_prefix'] = $result['dn_data']['debit_note_prefix'];
								$dn['debit_note_no']     = $result['dn_data']['debit_note_no'];
								$dn['debit_note_amount'] = $result['dn_data']['debit_note_amount'];
								$dn['order_no']          = $result['dn_data']['order_no'];
								$dn['debit_note_date']   = $result['dn_data']['debit_note_date'];
								$download_dn = $result['files'];
								$actionClass = $dn['action_class'];
								if(class_exists($actionClass)) {
									$returnClassObj = new $actionClass($this);

									//Send Mail to Seller Generating Debit Note
									$returnClassObj->sendEmail($dn, 'seller');
								}
							}else{
								$error_msg .= $result['msg'].'\n';
							}
						}
					}
				}
			}
		// return ids not empty for generating Replacement Note for seller	
			if(!empty($replacement_note_data)) {
				foreach ($replacement_note_data as $suborder_id => $returns_data) {
					foreach ($returns_data as $seller_invoice_id => $replacement_note) {
						if(!empty($replacement_note['return_ids'])){
							//Generate Replacement Note for suborder and seller wise
							$result = $this->generateReplacementNoteForSeller($replacement_note);
							if(!empty($result['status'])){
								$download_dn = $result['files'];
							}
							/* No email, Push notification and SMS for Replacement Note action */
						}
					}
				}	
			}

			$error_msg = trim($error_msg, '\n');

			$response = array('status'=>'success', 'msg'=> $error_msg, 'download_dn'=>$download_dn);
			echo json_encode($response);
		} else{
			$response = array('status'=>'error', 'msg'=> '', 'download_dn'=>'');
			echo json_encode($response);
		}
	}

	/**
	 * @info: Private Method to Send Emails for Changing return status
	 * @param: $email_data Array
	 * @author: Nishu, June 2018
	*/
	private function sendMailForChangeReturnAction($email_data, $product_images){

		$actionClass = '';
		if(!empty($email_data)) 
		{
			foreach($email_data as $key=>$value) 
			{
				if(!empty($value)) 
				{
					foreach ($value as $return_id => $action_data) 
					{
						//Set image for return product with details
						if(!empty($product_images['pid_to_imgs'][$action_data['order_product_id']])) 
						{
							$value[$return_id]['image'] = $product_images['pid_to_imgs'][$action_data['order_product_id']];
						}else{
							$value[$return_id]['image'] = '';
						}
						if(!empty($action_data['change_action']))
						{
							$actionClass = $action_data['change_action']['class_name'];
						}
					}
					if(class_exists($actionClass)) {

						$return_email_data = $this->getReturnEmailData($value);

						//Create Object for Return Action Class
						$returnClassObj = new $actionClass($this);
						$returnClassObj->customer_ws_access_token = $return_email_data['customer']['ws_access_token'] ?? '';
						
						//Send  To Customer
						$returnClassObj->sendEmail($return_email_data);

						//Send sms & push notification
						$returnClassObj->sendPushNotification($return_email_data);
					}
				}
			}
		}
	}


	/**
	 * @info: Private Method to Change EDIT_TYPE in oc_order_product table
	 * @Note: Change EDIT_TYPE to previous state before adding return, 
	 *          Only for processed Return/Replacement
	 * @return: Void
	 * @author: Nishu, June 2018
	*/
	private function changeEditTypeForRejectedReturns($returnInfo, $change_return){
		//Pending for now, Implementation from other team 
			//Edit_type history is shifted to a different table
	}

	public function setSaveReturnData($returnInfo, $change_return){

		$obj        = new ReturnActionBase($this);

		$saveReturn = array(
						'master_return_id'            => $returnInfo['master_return_id'], 
						'order_product_id'            => $returnInfo['order_product_id'],  
						'quantity'                    => $returnInfo['return_quantity'],  
						'return_reason_id'            => $returnInfo['return_reason_id'],  
						'comment'                     => $returnInfo['return_comment'],  
						'return_action_id'            => $change_return['return_action_id'] ?? $returnInfo['return_action_id'], 
						'return_action_reason_id'     => $change_return['return_action_reason_id'] ?? 0, 
						'internal_note'               => '',  
						'shipping_method'             => $change_return['shipping_method_id'] ?? $returnInfo['shipping_method'],  
						'date_added'                  => $returnInfo['date_added'],  
						'user'                        => $returnInfo['user'],  
						'user_id'                     => $returnInfo['user_id'],  
						'customer_id'                 => $returnInfo['customer_id'],  
						'crm_user_id'                 => $returnInfo['crm_user_id'], 
						'return_shipment_tracking_id' => $returnInfo['return_shipment_tracking_id'],  
						'debit_note_id'               => $returnInfo['debit_note_id'],  
						'credit_note_id'              => $returnInfo['credit_note_id'],
						'relisted_product_id'         => NULL,
						'active_row'                  => $returnInfo['active_row']
						);

		if(
	  		!empty(trim($change_return['internal_note'])) 
	  			&& 
	  		trim($change_return['internal_note']) != trim($returnInfo['internal_note'])
	  	){
	  		$saveReturn['internal_note'] = trim($change_return['internal_note']);
	  	}
	    /* IF action - Extra_Goods_Received OR Short_Goods_Received BUT same quantity, then action will be - Return_Request_Accepted*/
	  	if(
	  		!empty($change_return['return_action_id']) 
	  		          &&
	  		(
		  		$change_return['return_action_id'] == RETURN_ACTION_IDS['Extra_Goods_Received']
	  					||
	  		    $change_return['return_action_id'] == RETURN_ACTION_IDS['Short_Goods_Received']
	  		)
	  	){
	  		if($change_return['return_quantity'] == $returnInfo['return_quantity']) {
	  			$saveReturn['return_action_id'] = RETURN_ACTION_IDS['Goods_Received'];
	  		}else if($change_return['return_quantity'] < $returnInfo['return_quantity']) {
	  			$saveReturn['return_action_id'] = RETURN_ACTION_IDS['Short_Goods_Received'];
	  		}else if($change_return['return_quantity'] > $returnInfo['return_quantity']) {
	  			$saveReturn['return_action_id'] = RETURN_ACTION_IDS['Extra_Goods_Received'];
	  		}
	  		$saveReturn['quantity']		 = $change_return['return_quantity'];
		  	$saveReturn['internal_note'] = $this->setInternalNote($change_return, $returnInfo);
				
	  	}
	  	return $saveReturn;
	}

	public function setInternalNote($change_return, $returnInfo){

		$internal_note = "Privious return qty: ".$returnInfo['return_quantity']." pieces ";
	  	$internal_note .= "for ReturnId #".$returnInfo['return_id'];
	  	$internal_note .= "\nMasterReturnId #".$returnInfo['master_return_id'];
	  	$internal_note .= "\nOrderProductId #".$returnInfo['order_product_id'];
	  	$internal_note .= "\nthat is now:".$change_return['return_quantity']." pieces ";
	  	//Set Static Internal note
	  	
	  	if(
	  		!empty(trim($change_return['internal_note'])) 
	  			&& 
	  		trim($change_return['internal_note']) != trim($returnInfo['internal_note'])
	  	){
	  		$internal_note .= "\n\n" . trim($change_return['internal_note']);
	  	}

	  	return $internal_note;
	}
	/* MSA (March 18)*/

	/**
	 * @info : Public method to generate DN for Seller
	 * @param: $data Array
	 * @author: Nishu, April 2018
	*/
	public function generateDnForSeller($data){
		
		$result = array();
		$result['status'] = 1;
		$result['msg']    = '';
		$this->load->model('sale/return');

		$suborder_ids   = array();
		$order_id 	    = $data['order_id'];
		$order_no 	    = $data['order_no'];
		$suborder_ids[] = $data['suborder_id'];
		$return_ids     = $data['return_ids'];
		$seller_id 	    = $data['seller_id'];
		$seller_invoice_id = $data['seller_invoice_id'];
		$token          = $this->session->data['token'];
		$files          = array();
		
		$returns_ids_data = array();
		$seller_inv = new SellerInvoice( $this );
		$invoice_detail = $seller_inv->getSellerInvoiceById($seller_invoice_id);
		//$invoice_detail = array_combine(array_column($invoice_detail, 'suborder_id'), $invoice_detail);
		if(!empty($invoice_detail)){
			foreach ($invoice_detail as $seller_invoice_id => $invoice_data) {
				
				$returns_ids_data = array(
										'order_id' 		 => $order_id,
										'order_no'		 => $order_no,
										'suborder_id' 	 => $invoice_data['suborder_id'],
										'seller_id' 	 => $seller_id,
										'seller_invoice_id' => $seller_invoice_id,
										'custom_party_id'=> 0,
										'return_ids' 	 => $return_ids
									);
				$invoice_meta = unserialize($invoice_data['seller_invoice_meta']);
				$returns_ids_data['gstin'] = $invoice_meta['buyer_data']['tin'];
				$debit_note_id = NULL;
				
				// data insert in seller_debit_note and get debit_note_no and generated date
				$debit_note_id = $this->model_sale_return->addDebitNote($returns_ids_data);
				if(empty($debit_note_id)){
					$result['status'] = 0;
					$result['msg']    = 'Debit Note did not generated for OrderProductIds: '.implode(', ', $data['op_ids']);
					return $result;
				}
		        $dn              = new DebitNote($this);
		        $debit_note_data = $dn->getReturnDetails( $debit_note_id );

		        if(!empty($debit_note_data)){
			        $opids = array_column($debit_note_data , 'order_product_id');	
			        
			        $opis_to_qty = array_combine(
							            $opids,
							            array_column(
							                $debit_note_data , 'product_quantity'
							            )
							        );
			        $totals     = $seller_inv->getSellerProductsTotalsByProductIds( $opids , $opis_to_qty );
			        $this->model_sale_return->updateDebitNoteAmonut( $debit_note_id , $totals);

			        //Create Attachment file for sending mail
					$file_data = array();
					$file_data['order_no'] = $order_no;
					$file_data['debit_note_id'] = $debit_note_id;
					$enc_data = base64_encode(serialize($file_data));
					$debit_note = new DebitNote($this->registry,$enc_data);
					$dn_data = $debit_note->getDebitNoteData();

					$result['dn_data'] = $dn_data['debit_note_info'];

					// get calculated prdouct totals
		            $seller_invo_obj = new SellerInvoice($this->registry);
		            $return_calculated_data = $seller_invo_obj->calculationProducts($this->registry, '', '', '', $dn_data['product_data']);

					$path = $debit_note->debitNotePdf($return_calculated_data, $dn_data);

					$files[] = DIR_DLOAD_SLR_DBT_NOTE.base64_decode($path);
		    	}
			}
		}
		$result['files'] = $files;
		return $result;
	}

	/**
	 * @info : Public method to generate DN for Seller
	 * @param: $data Array
	 * @author: Nishu, April 2018
	*/
	public function generateReplacementNoteForSeller(array $data){
		$result = array();
		$result['status'] = 1;
		$result['msg']    = '';
		$this->load->model('sale/return');

		$suborder_ids   = array();
		$order_id 	    = $data['order_id'];
		$order_no 	    = $data['order_no'];
		$suborder_ids[] = $data['suborder_id'];
		$return_ids     = $data['return_ids'];
		$seller_id 	    = $data['seller_id'];
		$seller_invoice_id = $data['seller_invoice_id'];
		$token          = $this->session->data['token'];
		$files          = array();
		
		$returns_ids_data = array();
		$seller_inv = new SellerInvoice( $this );
		$invoice_detail = $seller_inv->getSellerInvoiceById($seller_invoice_id);
		if(!empty($invoice_detail)){
			foreach ($invoice_detail as $seller_invoice_id => $invoice_data) {
				
				$returns_ids_data = array(
										'order_id' 		 => $order_id,
										'order_no'		 => $order_no,
										'suborder_id' 	 => $invoice_data['suborder_id'],
										'seller_id' 	 => $seller_id,
										'seller_invoice_id' => $seller_invoice_id,
										'custom_party_id'=> 0,
										'return_ids' 	 => $return_ids
									);
				$invoice_meta = unserialize($invoice_data['seller_invoice_meta']);
				$returns_ids_data['gstin'] = $invoice_meta['buyer_data']['tin'];
				$debit_note_id = NULL;
				$replacement_note_id = NULL;
				
				// data insert in replacement_note and get replacement_note_no and generated date
				$replacement_note_id = $this->model_sale_return->addReplacementNote($returns_ids_data);
				if(empty($replacement_note_id)){
					$result['status'] = 0;
					$result['msg']    = 'Replacement Note did not generated for OrderProductIds: '.implode(', ', $data['op_ids']);
					return $result;
				}

		        $dn              = new DebitNote($this);
		        $replacement_note_data = $dn->getReturnReplacementDetails($replacement_note_id);
		        if(!empty($replacement_note_data)){
			        $opids = array_column($replacement_note_data , 'order_product_id');	
			        
			        $opis_to_qty = array_combine(
							            $opids,
							            array_column(
							                $replacement_note_data , 'product_quantity'
							            )
							        );
			        
			        $totals     = $seller_inv->getSellerProductsTotalsByProductIds( $opids , $opis_to_qty );
			        
			        $this->model_sale_return->updateReplacementNoteAmonut( $replacement_note_id , $totals );

			        //Create Attachment file for sending mail
					$file_data = array();
					$file_data['order_no'] = $order_no;
					$file_data['replacement_note_id'] = $replacement_note_id;
					$enc_data = base64_encode(serialize($file_data));
					$debit_note = new DebitNote($this->registry,$enc_data);
					$rn_data = $debit_note->getReplacementNoteData();

					$result['rn_data'] = $rn_data['replacement_note_info'];

					// get calculated prdouct totals
		            $seller_invo_obj = new SellerInvoice($this->registry);
		            $return_calculated_data = $seller_invo_obj->calculationProducts($this->registry, '', '', '', $rn_data['product_data']);

					$path = $debit_note->replacementNotePdf($return_calculated_data, $rn_data);

					$files[] = DIR_DLOAD_SLR_RPMT_NOTE.base64_decode($path);
		    	}
			}
		}

		$result['files'] = $files;
		return $result;
	}
	
    public function download(){
    	$filter_data = array();
        $url = array();
        foreach ($this->request->get as $key => $value) {
            if( $key != 'route' && $key != 'token' ){
                $filter_data[$key] = $value;
                $url[] = "$key=$value";
            }
        }
        $url = implode('&',$url);
        $this->load->model('sale/return');
		$results = $this->model_sale_return->getAllReturns($filter_data, 'download');

    	require_once DIR_SYSTEM.'library/PHPExcel/IOFactory.php';
		$phpExcel = new PHPExcel;

		$phpExcel->getDefaultStyle()->getFont()->setName('Arial Black');
		$phpExcel->getDefaultStyle()->getFont()->setSize(14);
		$phpExcel ->getProperties()->setTitle("Bulk Inventory Listing");
		$phpExcel ->getProperties()->setCreator("Wholesalebox");
		$phpExcel ->getProperties()->setDescription("Excel for Returns Listing");

		// Creating PHPExcel spreadsheet writer object
		$writer = PHPExcel_IOFactory::createWriter($phpExcel, "Excel2007");

		// will get the already created sheet

		$sheet = $phpExcel ->getActiveSheet();

		// setting title of the sheet
		$sheet->setTitle('Returns Listing');

		// Creating spreadsheet header
		$sheet ->getCell('A1')->setValue('Order No');
		$sheet ->getCell('B1')->setValue('Customer');
		$sheet ->getCell('C1')->setValue('Email');
		$sheet ->getCell('D1')->setValue('Company');
		$sheet ->getCell('E1')->setValue('City');
		$sheet ->getCell('F1')->setValue('Master Return Id');
		$sheet ->getCell('G1')->setValue('Return Request Added Date');
		$sheet ->getCell('H1')->setValue('DebitNote');
		$sheet ->getCell('I1')->setValue('DebitNote Amt');
		$sheet ->getCell('J1')->setValue('DebitNote Date');
		
		/*new column for shipment data*/
		$sheet ->getCell('K1')->setValue('Courier Partner');
		$sheet ->getCell('L1')->setValue('Tracking Number');
		$sheet ->getCell('M1')->setValue('Shipping Date');
		//$sheet ->getCell('N1')->setValue('Customer Address');
		/*new column for shipment data*/


		$row_count = 2;
		foreach ($results as $result){
			$sheet ->getCell('A'.$row_count)->setValue($result['order_no']);
			$sheet ->getCell('B'.$row_count)->setValue($result['firstname'].' '.$result['lastname']);
			$sheet ->getCell('C'.$row_count)->setValue($result['email']);
			$sheet ->getCell('D'.$row_count)->setValue($result['shipping_company']);
			$sheet ->getCell('E'.$row_count)->setValue($result['shipping_city']);
			$sheet ->getCell('F'.$row_count)->setValue($result['master_return_id']);
			$sheet ->getCell('G'.$row_count)->setValue($result['return_requested_date']);
			$dn_details = !empty($result['debit_note_id'])
							? array( $result['debit_note_id'].'#'.
									 $result['debit_note_prefix'].'#' .
									 $result['debit_note_no'].'#'.
									 $result['debit_note_amount'].'#'.
									 $result['date_added']
								    ) 
							: '';
			if(!empty($dn_details)){
				foreach($dn_details as $dn){
					$dn_data = explode('#', $dn);
					$sheet ->getCell('H'.$row_count)->setValue($dn_data[1].$dn_data[2]);
					$sheet ->getCell('I'.$row_count)->setValue($dn_data[3]);
					$sheet ->getCell('J'.$row_count)->setValue($dn_data[4]);
					$row_count++;
				}
			}

			/*new column for shipment data*/
			 $sheet ->getCell('K'.$row_count)->setValue($result['courier_company']);
			 $sheet ->getCell('L'.$row_count)->setValue($result['tracking_no']);
			 $shipping_date = '';
			 if(!empty($result['shipping_date'])) {
			 	$shipping_date = date('d-m-Y', strtotime($result['shipping_date']));
			 }
			 $sheet ->getCell('M'.$row_count)->setValue($shipping_date);
			/*new column for shipment data*/

			$row_count++;
		}
		// Making headers text bold and larger
		//$sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(12);
		$sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(12);

		// Autosize the columns
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		$sheet->getColumnDimension('J')->setAutoSize(true);

		$sheet->getColumnDimension('K')->setAutoSize(true);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		$sheet->getColumnDimension('M')->setAutoSize(true);



		// download file 
		$file_path =  DIR_DOWNLOAD.'returns.xlsx';
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=".basename($file_path));
		header("Cache-Control: max-age=0");
		ob_clean();
		$writer->save('php://output');
		flush();
		readfile($file_path);
        exit();
	}

	/**
    * Public function to check Given Pincode is servicable or not by NuvoEx for reverse shipping
    * @return: Boolen
    * @author: Nishu, August 2017
    */
    public function checkAreaServiceableOrNot(){
        
        $pincode         = isset($this->request->get['pincode']) ? $this->request->get['pincode'] : '';
        $courier_partner = isset($this->request->get['courier_partner']) ? $this->request->get['courier_partner'] : '';
        $param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier_partner,
                    'request'       => $this->request->get,
                    'orderInfo'     => array(),
                    'totals'        => array(),
                    'warehouseInfo' => array()
                );
        $courierObject  = CourierFactory::build($courier_partner,$param);
        if( is_object($courierObject) ) {
            $response  =  $courierObject->isPincodeServiceable();
        } else { 
            $response  = $courierObject;
        }
        
        echo json_encode($response);
    }

    /**
     * addReverseShipment() to add request for reverse shipment
     * @param: $data
     * @return: string
     * @author: MSA, March 2018
    */
    public function addReverseShipment(){
    	$this->load->model('sale/shipping_label');
    	$courier_partner = $this->request->post['courier_partner'] ?? '';
    	$warehouse_id    = $this->request->post['select_warehouse_id'] ?? 0;
    	$order_id 		 = $this->request->post['order_id'] ?? 0;

	   	$selector = array(
	                'order'=> array('select' => array('email','payment_code')) ,
	               );
		//Get OrderInfo details
		$order_info = OrderInfo::getOrderInfo($this->db, $order_id,'',$selector);
        $warehouseInfo  = $this->model_sale_shipping_label->getAddress($warehouse_id);
        $param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier_partner,
                    'request'       => $this->request->post,
                    'orderInfo'     => $order_info,
                    'totals'        => array(),
                    'warehouseInfo' => $warehouseInfo
                );
        $courierObject  = CourierFactory::build($courier_partner,$param);
        $response = array();
        if( is_object($courierObject) ) {
            $response  =  $courierObject->addReverseShipment($this); // to get product images from MsProduct class require controller reference
        } else { 
            $response  = $courierObject;
        }

        echo json_encode($response); 
    }

    /**
     * addForwardShipment() to add request for reverse shipment
     * @param: $data
     * @return: string
     * @author: MSA, March 2018
    */
    public function addForwardShipment(){
    	$this->load->model('sale/shipping_label');
    	$courier_partner = $this->request->post['courier_partner'];
    	$warehouse_id    = $this->request->post['select_warehouse_id'];
    	$order_id 		 = $this->request->post['order_id'];
    	$selector = array(
	                'order'=> array('select' => array('order_id','email')) ,
	                'suborder' => array( 'select' => array('suborder_id','invoice_no')),
	                'order_product' => array( 'select' => array('order_product_id')),
	               );
		//Get OrderInfo details
		$orderInfo 	= OrderInfo::getOrderInfo($this->db, $order_id,'',$selector);
        $warehouseInfo  	= $this->model_sale_shipping_label->getAddress($warehouse_id);
        $param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier_partner,
                    'request'       => $this->request->post,
                    'orderInfo'     => $orderInfo,
                    'totals'        => array(),
                    'warehouseInfo' => $warehouseInfo
                );
        $courierObject  = CourierFactory::build($courier_partner,$param);
        if( is_object($courierObject) ) {
            $response  =  $courierObject->addForwardShipment($this); // to get product images from MsProduct class require controller reference
        } else { 
            $response  = $courierObject;
        }

        echo json_encode($response); 
    }
    
    /**
     * addManuallyReverseShipment() to add request for manually reverse shipment
     * @param: $data
     * @return: string
     * @author: MSA, June 2018
    */	
    function addManuallyReverseShipment($data = array())
    {
    	if(!empty($data))
    	{	
    		$this->load->model('sale/shipping_label');
    		$courier_name 	= array_unique(array_column($data, 'courier_name'));
    		$docket  		= array_unique(array_column($data, 'docket'));
    		$order_id       = array_unique(array_column($data, 'order_id'));
    		$warehouse_id   = array_unique(array_column($data, 'warehouse_id'));
    		$return_id     = implode(',',array_unique(array_column($data, 'return_id')));
    		
    		if(!empty($courier_name) && !empty($docket))
    		{
    			$courier_partner 	= $courier_name[0];
    			$docket 	  		= $docket[0];
    			$order_id 	  		= $order_id[0];
    			$warehouse_id 		= $warehouse_id[0];
    			$selector = array(
			                'order'=> array('select' => array()) ,
			                'suborder' => array( 'select' => array()),
			                'order_product' => array( 'select' => array()),
			               );
				//Get OrderInfo details
				$orderInfo 	= OrderInfo::getOrderInfo($this->db, $order_id,'',$selector);
		        $warehouseInfo  	= $this->model_sale_shipping_label->getAddress($warehouse_id);
    			
    			$param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier_partner,
                    'request'       => $data,
                    'orderInfo'     => $orderInfo,
                    'totals'        => array(),
                    'warehouseInfo' => $warehouseInfo
                );
    			$courierObject  = CourierFactory::build($courier_partner,$param);
		        if( is_object($courierObject) && method_exists($courierObject, 'addManuallyReverseShipment')) {
		            $response  =  $courierObject->addManuallyReverseShipment(); // to get product images from MsProduct class require controller reference
		        } 
    		}
    	}
    }

	/**
    * getCourierTrackingHistory() method to call logistic class to get tracking data for a docket number
    * @param: void
    * @return: array
    * @author: MSA, March 2018
    */
	public function getCourierTrackingHistory(){
		$tracking_no         = isset($this->request->get['tracking_no']) ? $this->request->get['tracking_no'] : '';
        $courier_partner 	 = isset($this->request->get['courier_company']) ? $this->request->get['courier_company'] : '';
        $this->load->model('sale/return');
        $courier_class = $this->model_sale_return->getTrackingCourierClass($courier_partner);
        $courier_partner = ($courier_class!='')?$courier_class:$courier_partner;
        $param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier_partner,
                    'request'       => $this->request->get,
                    'orderInfo'     => array(),
                    'totals'        => array(),
                    'warehouseInfo' => array()
                );
        $courierObject  = CourierFactory::build($courier_partner,$param);
        if( is_object($courierObject) ) {
            $response  =  $courierObject->getDocketTrackingHistory();
        } else { 
            $response  = $courierObject;
        }
        
        echo json_encode($response);
	}

	/**
	* Public function cancelReverseShipment() to cancel reverse shipment
	* @param void
	* @return Array $status
	* @author MSA 9 July 18
	*/
	public function cancelReverseShipment()
	{
		$response = array();
		$shipment_id = $this->request->get['shipping_id'];
		$shipment_type = $this->request->get['type'];

		$this->load->model('sale/return');
        $shipment_tracking_details = $this->model_sale_return->getShipmentTrackingDetails($shipment_id);

		if(!empty($shipment_tracking_details['courier_company']))
		{
			$courier_partner = $shipment_tracking_details['courier_company'];
			$param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier_partner,
                    'request'       => $this->request->get,
                    'orderInfo'     => array(),
                    'totals'        => array(),
                    'warehouseInfo' => array()
                );
	        $courierObject  = CourierFactory::build($courier_partner,$param);
	        if( is_object($courierObject) ) {
	           $response  =  $courierObject->cancelReverseShipment($this);	
	        } else { 
	            $response  = $courierObject;
	        }
		} 
		echo json_encode($response);
	}

	/**
    * Public function to get return value, Qty and weight by master return id
    * @param: void
    * @return: array
    * @author: Nishu, August 2017
    */
	public function getReturnDetailByMaster(){
		$master_return_id = $_GET['master_return_id'];
		$this->load->model('sale/return');
		$credit_note = new CreditNote( $this );
		$return = array();
		$return = $credit_note->getReturnQtyByMasterId($master_return_id);
		$return['return_weight'] = 0.0;
		$return_weight = $this->model_sale_return->getTotalWeightForReturns($return['return_ids']);
		foreach ($return_weight as $suborder_id => $weight) {
			$return['return_weight'] += $weight;
		}
		if(!empty($return)){
			$result = $this->load->model_sale_return->getReturnReason($return['return_reason_id']);
	 		$return['return_reason'] = $result[0]['name'];
			//$return['return_value'] = $return['return_value'] + 1000;
			//$base_val=500;
			//$return['return_value'] = $base_val*(ceil($return['return_value']/$base_val));
			$return['response'] = 'success';
		}else{
			$return['response'] = 'empty';
		}
		echo json_encode($return);
		exit();
	}

	/**
    * Public function to get return value, Qty and weight by return id
    * @param: void
    * @return: array
    * @author: msa, may 2018
    */
	public function getReturnDetailById(){
		$return_id = $_GET['return_id'];
		$this->load->model('sale/return');
		$credit_note = new CreditNote( $this );
		$return = array();
		$return = $credit_note->getReturnQtyById($return_id);
		$return['return_weight'] = 0.0;
		$return_weight = $this->model_sale_return->getTotalWeightForReturns($return['return_ids']);
		foreach ($return_weight as $suborder_id => $weight) {
			$return['return_weight'] += $weight;
		}
		if(!empty($return)){
			$return['response'] = 'success';
		}else{
			$return['response'] = 'empty';
		}
		echo json_encode($return);
		exit();
	}



	protected function orderInfoTabCourierPartners(&$data) {
		$this->load->model('sale/return');
        $data['token'] = $this->session->data['token'];

        $filter_data = array(
            'search_firm_name' => '',
            'search_city' => ''
        );
       //show default Custom Party Detail
        $data['getCustomParties'] = $this->model_sale_return->getCustomParties($filter_data);
    }

	/**
	Public function to save Custom party details 
	@author: Nishu, 2017
    */
    public function save_custom_party(){
    	$this->load->model('sale/return');
    	$post_data = $this->request->post;
    	$validate_gst_value = validateGSTNo($post_data['gst_num'],true);
        if( $validate_gst_value ){ //Validate GST number
    		$this->model_sale_return->save_custom_party($post_data);
        }
    }  


    //get search custom parties
    public function getSearchAddress() {
    	$json = array();
        $this->load->model('sale/return');
        $search_firm_name = '';
        $search_city = '';
        if (isset($this->request->post['filter_firm_name'])) {
            $search_firm_name = $this->request->post['filter_firm_name'];
        }
        if (isset($this->request->post['filter_city'])) {
            $search_city = $this->request->post['filter_city'];
        }

        $filter_data = array(
            'search_firm_name' => $search_firm_name,
            'search_city' => $search_city
        );

        //get Custom party detail
        $company_addresses = $this->model_sale_return->getCustomParties($filter_data);

        foreach ($company_addresses as $address) {
            $json[] = array(
                'custom_id' => $address['custom_id'],
                'firm_name' => $address['firm_name'],
                'address_1' => $address['address1'],
                'address_2' => $address['address2'],
                'city' 		=> $address['city'],
                'pincode' 	=> $address['pincode'],
                'state' 	=> $address['state'],
                'country' 	=> $address['country'],
                'gst_number' => $address['gst_number'],
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
	Public function to edit credit note 
	@param: $post, array of requested post data
	@return: void
	@author: Nishu, 2017
    */
    public function update_credit_note(){
    	parse_str($_POST['data'], $data);
    	
    	$update_cn = array();
    	$update_cn['cn_id'] = $data['edit_cn_id'];
    	$update_cn['fields'] = array();
		$cn_data = array();
		$order_id = $data['edit_cn_order_id'];

		$edited_fields = array();

    	if(!empty($data['is_cod_failed_cn_edit']) &&  $data['is_cod_failed_cn_edit'] == 1){
    		$update_cn['fields']['cod_failed_penalty'] 	        = $data['cod_penality'];
    		$edited_fields['cod_failed_penalty']['old_value'] 	= $data['old_cod_penality'];
    		$edited_fields['cod_failed_penalty']['new_value'] 	= $data['cod_penality'];
    	}else{
    		$update_cn['fields']['shipping_collected'] 	        = $data['rev_shipping'];
    		$edited_fields['shipping_collected']['old_value'] 	= $data['old_rev_shipping'];
    		$edited_fields['shipping_collected']['new_value'] 	= $data['rev_shipping'];
    	}

    	$update_cn['fields']['reversal_shipping'] 	= $data['reversal_shipping'];
    	$edited_fields['reversal_shipping']['old_value'] 	= $data['old_reversal_shipping'];
    	$edited_fields['reversal_shipping']['new_value'] 	= $data['reversal_shipping'];
	
	    $update_cn['fields']['other_charges'] 	        = $data['other_charges'];
	    $edited_fields['other_charges']['old_value'] 	= $data['old_other_charges'];
    	$edited_fields['other_charges']['new_value'] 	= $data['other_charges'];
	
    	$cn_data['table_name'] 	= 'oc_credit_note';
    	$cn_data['ref_url'] 	= 'index.php?route=sale/return/update_credit_note'; 
    	$cn_data['comment'] 	= $data['cn_comment'];
    	$cn_data['user'] 		= $this->user->getUserName()['name'];
    	$cn_data['date_added'] 	= date('Y-m-d H:i:s');
    	$cn_data['user_agent']	= $_SERVER['HTTP_USER_AGENT'];
    	$cn_data['ip_address'] 	= $this->request->getIpAddress;

    	$cn_data['cn_id']       = $update_cn['cn_id'];
    	$cn_data['user_id']     = $this->user->getId();
    	$cn_data['name']        = $this->user->getUserName($this->user->getId())['name'];
    	$cn_data['user_type']   = $this->user->getGroupName();


    	if(empty($data['old_refund_on_hold']) || $data['old_refund_on_hold'] != 'NOT_APPLICABLE' ){
	    	//Set Default value for payment_cleared, whenever CN is edited
	    	$update_cn['payment_cleared'] = 'NO';

	    	//Check for, Is CN marked as refund is ON_HOLD
	    	if(!empty($data['refund_on_hold']) && $data['refund_on_hold'] == 'ON_HOLD'){
				$update_cn['payment_cleared'] = 'ON_HOLD';
	    	}
    	}else{
    		$update_cn['payment_cleared'] = 'NOT_APPLICABLE';
    	}
    	
    	$this->load->model('sale/return');
    	$is_updated = $this->model_sale_return->updateCNByFieldList($update_cn);
    	if($is_updated && !empty($edited_fields)){

    		/*Send CN update email to returns & accounts */
    			$encode_file['order_id']       = $order_id;
				$encode_file['credit_note_id'] = $update_cn['cn_id'];
				$encode_file                   = base64_encode(serialize($encode_file));
				//Create Attachment file for sending mail
				$credit_note = new CreditNote($this->registry,$encode_file);

				//Calculate and update credit_note amount
				$credit_note->calculateAndUpdateCreditNoteAmount((int)$update_cn['cn_id']);

				$cn_data     = $credit_note->getCreditNoteData();
				$path        = $credit_note->creditNotePdf($cn_data);
				$files[] 	 = DIR_DLOAD_BYR_CDT_NOTE.base64_decode($path);
				$cn_data['attachments'] = $files;
				$returnClassObj = new ActionCnForClient($this);
				$returnClassObj->sendCnUpdatedEmail($cn_data);
			/*Send CN update email to returns & accounts */

    		foreach ($edited_fields as $key => $values) {
    			if($values['old_value'] != $values['new_value']){
	    			$cn_data['field_name'] 	= $key;
					$cn_data['old_value'] 	= $values['old_value'];
	  				$cn_data['new_value'] 	= $values['new_value'];
	  				//Add to admin change log to track: value edited for multiple fields
	    			$result = CommonLib::addAdminChangeLog($this->db, $cn_data);
    			}
    		}
    	}
    	$returnData = array('status'=> $is_updated);
    	echo json_encode($returnData); exit();
    }

    /**
	* Public function to get all return action reasons or specific action reason name
	* @param: $parent_action_id
	* @return: Array
	* @author: Nishu, Nov 2017
	*/
	public function getActionReasonByActionId(){
		$this->load->model('sale/return');
		$action_id = isset($this->request->get['return_action_id']) ? (int)$this->request->get['return_action_id'] : 0;
		$result = $this->model_sale_return->getActionReasonByActionId($action_id);
		echo json_encode($result); exit();
	}

	/**
    * Public function to get return value, Qty and weight by return id
    * @param: void
    * @return: array
    * @author: Nishu, August 2017
    */
	public function getReturnDetailByReturn(){
		$return_id = $_GET['return_id'];
		$this->load->model('sale/return');
		$credit_note = new CreditNote( $this );
		$return = array();
		$return = $credit_note->getReturnQtyByReturnId($return_id);
		if(!empty($return)){
			$return['response'] = 'success';
		}else{
			$return['response'] = 'empty';
		}
		echo json_encode($return);
		exit();
	}

	/**
	 * @info: Public method to split return into two returns
	 * @author: Nishu, April 2018
	*/
	public function splitReturn(){
		
		$post = $this->request->post;
		
		if(
			!isset($post['return_id']) && empty($post['return_id']) 
			    && 
			!isset($post['qty']) && empty($post['qty']) 
		){
			echo json_encode(array("Invalid Data sent."));
			exit();
		}

		//Creating ReturnInfo object
		$return_info  = new ReturnInfo($this);
		$return_base  = new ReturnActionBase($this);

		//Get return details by return_id
		$return_details = $return_info->getReturnById($post['return_id']);

		if(!empty($return_details)){
			$return = $return_details[0];
			//Return split qty validation
			$actual_qty = $return['quantity'];

			if($post['qty'] >= $actual_qty){
				echo "Given Split quantity is not valid.";
				exit();
			}

			//Unset return_id from return details array
			unset($return['return_id']);

			$return['quantity'] = $actual_qty - $post['qty'];

			$return['internal_note']  = "This return is split from rId #".$post['return_id'];
			$return['internal_note'] .= "<br>Actual Qty was: ".$actual_qty;

			//Entery to oc_return with updated return action
			$return_base->insertReturn($return);

			//Update Old Return with active_row as 0
			$return_base->resetReturnActiveStatusForReturnIds(0, $post['return_id']);


			//Set New splited return qty with new master return id
			$return['quantity'] = $post['qty'];

			$master_return_data = array(
						  				'order_id'			=> $post['order_id'],
						  				'customer_id' 		=> $return['customer_id'],
						  				'shipping_method'	=> $return['shipping_method'],
						  				'return_shipment_tracking_id' => $return['return_shipment_tracking_id']
						  			  );

			//generate new master return id for rejected returns
			$return['master_return_id'] = $return_base->addMasterReturn( $master_return_data );

			//Entery to oc_return with split return action
			$return_base->insertReturn($return);
		}
		echo json_encode(array('success'));
	}

	/**
	 * @info: Public method to split return into two returns
	 * @author: Nishu, April 2018
	*/
	public function getReturnReasonsForConversion(){
		$post = $this->request->post;
		$data = array();
		if(!empty($post)){
			$this->load->model('sale/return');
			$data = $this->model_sale_return->getReturnReasonsForConversion($post);
 		}
 		echo json_encode($data);
	}

	/**
	 * @info: Public method to convert return into replacement or Vice-Versa
	 * @author: Nishu, April 2018
	*/
	public function convertReturn(){
		
		$post = $this->request->post;

		if( empty($post['return_id']) ){
			echo json_encode(array("Invalid Data sent."));
			exit();
		}

		//Creating ReturnInfo object
		$return_info  = new ReturnInfo($this);
		$return_base  = new ReturnActionBase($this);

		//Get return details by return_id
		$return_details = $return_info->getReturnById($post['return_id']);

		if(!empty($return_details)){
			$return = $return_details[0];

			//Unset return_id from return details array
			unset($return['return_id']);

			if(strtoupper($post['rtype']) == 'RETURN'){
				$return['internal_note'] = "Return Converted to Replacement, Old ReturnId: ".$post['return_id'];
			}else{
				$return['internal_note'] = "Replacement Converted to Return, Old ReturnId: ".$post['return_id'];
			}
		   
			if(isset($post['return_reason_id'])){
				$return['return_reason_id'] = $post['return_reason_id'];
			}

			if(isset($post['return_action_id'])){
				$return['return_action_id'] = $post['return_action_id'];
			}

			if(isset($post['internal_note'])){
				$return['internal_note'] .= "\n\n" . urldecode($post['internal_note']);
			}

			//Entery to oc_return with updated return action
			$return_base->insertReturn($return);

			//Update Old Return with active_row as 0
			$return_base->resetReturnActiveStatusForReturnIds(0, $post['return_id']);
		}
		echo json_encode(array('success'));
	}

	/**
	* Public method shippingLabelPdf() to generate and download shipping label PDF file
										for Return Action - Back To Customer Shipments
	* @param: integer shipping_id
	* @return: void
	* @author: MSA July 2018
	*/
	public function shippingLabelPdf() {

		$this->load->model('sale/return');
		$shipping_id = $this->request->get['shipping_id'];

		$data = array();

		if(!empty($shipping_id)) {

		   $shipment_details = $this->load->model_sale_return->getBackToCustomerShipmentDetails($shipping_id);
		  
		   if(!empty($shipment_details)) {

		   	/* check for barcode directory */
		   		if(!file_exists(DIR_IMAGE . '/barcode/')) {
		            $oldmask = umask(0);
		            mkdir(DIR_IMAGE . '/barcode/', 0775, true);
		            umask($oldmask);
		        }
		        if(trim($shipment_details['tracking_no'])!=='')
	            {
	               $barcode_tracking_no = DIR_IMAGE . 'barcode/' . trim($shipment_details['tracking_no']) . '.png'; 
	            }
	            if(trim($shipment_details['order_no'])!=='')
	            {
	               $barcode_order_no = DIR_IMAGE . 'barcode/' . trim($shipment_details['order_no']) . '.png';
	            }
	            
	            $text = trim($shipment_details['tracking_no']);
	            $text1 = trim($shipment_details['order_no']);
	            $size = 60;
	            $orientation = 'horizontal';
	            $code_type = 'code128';
	            $print = true;
	            barcode($barcode_tracking_no, $text, $size, $orientation, $code_type, $print);
	            barcode($barcode_order_no, $text1, $size, $orientation, $code_type, $print);

	    //set data to show in shipping lable pdf file    
	        $data['courier_company']		= $shipment_details['courier_company'];
	        $data['tracking_no']			= $shipment_details['tracking_no'];
	        $data['date_added']				= $shipment_details['date_added'];
	        $data['order_id']				= $shipment_details['order_id'];
	        $data['order_no']				= $shipment_details['order_no'];
	        $data['weight']					= $shipment_details['weight'];
	        $data['value']					= $shipment_details['value'];
	        $data['qty']					= $shipment_details['qty'];
	        $data['gstin']					= $shipment_details['gstin'];
	        $data['pan_no']					= substr($shipment_details['gstin'], 2, -3);
	        $data['barcode_tracking_no']  	= $barcode_tracking_no;
	        $data['barcode_order_no']  		= $barcode_order_no; 
	        $data['site_logo'] 				= DIR_IMAGE . 'site_logo.png';

	        $data['from_address'] = array(
	        	'warehouse_name' => $shipment_details['warehouse_name'],
	        	'address_1'		 => $shipment_details['address_1'],
	        	'address_2'		 => $shipment_details['address_2'],
	        	'city'		 	 => $shipment_details['city'],
	        	'postcode'		 => $shipment_details['postcode'],
	        	'telephone'		 => $shipment_details['telephone'],
	        	'email'		 	 => $shipment_details['email'],
	        );

	        if(empty($shipment_details['shipping_details'])) {
	        	$data['to_address'] = $this->getCustomerFromAPIRequestParams(
	        																	$shipment_details['courier_company'], 
	        																	$shipment_details['request_param']
	       																);	
	        }else{
	        	$data['to_address'] = unserialize($shipment_details['shipping_details']);
	        }

	        if(!empty($shipment_details['alternate_contact_number'])) {
	            $alternate_numbers = json_decode($shipment_details['alternate_contact_number'],true);
	            $alternate_numbers = array_merge($alternate_numbers, array($data['to_address']['phone']));
	        }else{
	            $alternate_numbers = array($shipment_details['shipping_telephone']);
	        }
	        
	        $data['to_address']['alternet_numbers'] = $alternate_numbers;

	        // generate HTML view for shipping label PDF file 
	        ob_start();
		      include(DIR_SYSTEM . 'library/backto_customer_shipping_label_html.php');    /// need to move this file in template folder
		      $html = ob_get_contents();
		    ob_get_clean();
	        
		    $shipping_lable_file = trim($data['order_no']) . '_' . trim($data['courier_company']) . '_' . trim($data['tracking_no']) . '.pdf';    

       		$file_path = DIR_DLOAD_SHP_LBL . $shipping_lable_file;

       	// generate Shipping label PDF file and saved in download folder
		    require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');
		    try{
		            $html2pdf = new MyHtml2Pdf('P','A4','en', false, 'UTF-8');
		            $html2pdf->pdf->SetDisplayMode('fullpage');    
		            $html2pdf->writeHTML($html);
		            ob_end_clean();
		            $html2pdf->output($file_path , 'F');
		      } catch (Exception $ex) {

		          echo $ex->getMessage(); die;
		      }

		// generate required data structure to direct download shipping label pdf file      
		      $file_data = array(
		      	'file_desc'	=> 'return_shipping_label',
		      	'file_path'	=> base64_encode($shipping_lable_file)
		      );
		    // call secure file download for shipping label  
		      $this->securefiledownload->downloadFile($file_data, 'admin',true);
		   }
		}
	}

	/**
	* Protected method getCustomerFromAPIRequestParams() to get customer details
	*				   from Back To Customer API request params data
	* @param: string courier partner name
	* @param: string request param data
	* @return: void
	* @author: MSA July 2018
	*/
	protected function getCustomerFromAPIRequestParams($courier_partner, $request_param)
	{
		$data = array();

		$api_string = unserialize($request_param);

		if(is_array($api_string) && !empty($api_string)) {

			if(strtolower($courier_partner) == 'gati') {

				$api_string = new SimpleXMLElement($post_values);

				$data = array(
					'name'		=> !empty($postValues->details->req->RECEIVER_NAME)
									? (string)$postValues->details->req->RECEIVER_NAME
									: '',
					'phone'		=> !empty($postValues->details->req->RECEIVER_MOBILE_NO)
									? (string)$postValues->details->req->RECEIVER_MOBILE_NO
									: '',
					'address_1'	=>  !empty($postValues->details->req->RECEIVER_ADD1)
									? (string)$postValues->details->req->RECEIVER_ADD1
									: '',
					'address_2'	=>  !empty($postValues->details->req->RECEIVER_ADD2)
									? (string)$postValues->details->req->RECEIVER_ADD1
									: '',
					'city'		=> !empty($postValues->details->req->RECEIVER_CITY)
									? (string)$postValues->details->req->RECEIVER_CITY
									: '',	
					'state'		=>  '',
					'postcode'	=> !empty($postValues->details->req->RECEIVER_PINCODE)
									? (string)$postValues->details->req->RECEIVER_PINCODE
									: '',
					);	

			}else if(strtolower($courier_partner) == 'dotzot') {

				$api_string = new SimpleXMLElement($api_string[0]);

			  	$data = array(
					'name'		=> !empty($api_string->DocketList->DocketList->ShippingAdd1)
									? (string)$api_string->DocketList->DocketList->ShippingAdd1
									: '',
					'phone'		=> !empty($api_string->DocketList->DocketList->ShippingTelephoneNo)
									? (string)$api_string->DocketList->DocketList->ShippingTelephoneNo
									: '',
					'address_1'	=>  !empty($api_string->DocketList->DocketList->ShippingAdd1)
									? (string)$api_string->DocketList->DocketList->ShippingAdd1
									: '',
					'address_2'	=>  !empty($api_string->DocketList->DocketList->ShippingAdd2)
									? (string)$api_string->DocketList->DocketList->ShippingAdd2
									: '',
					'city'		=> !empty($api_string->DocketList->DocketList->ShippingCity)
									? (string)$api_string->DocketList->DocketList->ShippingCity
									: '',	
					'state'		=> !empty($api_string->DocketList->DocketList->ShippingState)
									? (string)$api_string->DocketList->DocketList->ShippingState
									: '',
					'postcode'	=> !empty($api_string->DocketList->DocketList->ShippingZip)
									? (string)$api_string->DocketList->DocketList->ShippingZip
									: '',
					);	



			}else if(strtolower($courier_partner) == 'bluedart') {


				$data = array(
					'name'		=> !empty($api_string[0]['Request']['Consignee']['ConsigneeName'])
									? $api_string[0]['Request']['Consignee']['ConsigneeName']
									: '',
					'phone'		=> !empty($api_string[0]['Request']['Consignee']['ConsigneeTelephone'])
									? $api_string[0]['Request']['Consignee']['ConsigneeTelephone']
									: '',
					'address_1'	=>  !empty($api_string[0]['Request']['Consignee']['ConsigneeAddress1'])
									? $api_string[0]['Request']['Consignee']['ConsigneeAddress1']
									: '',
					'address_2'	=>  !empty($api_string[0]['Request']['Consignee']['ConsigneeAddress2'])
									? $api_string[0]['Request']['Consignee']['ConsigneeAddress2']
									: '',
					'city'		=>  '',	
					'state'		=> '',
					'postcode'	=> !empty($api_string[0]['Request']['Consignee']['ConsigneePincode'])
									? $api_string[0]['Request']['Consignee']['ConsigneePincode']
									: '',
								);	


			}
		}
		return $data;
	}

	/**
	* Protected method editCustomDNRef() to update custom dn ref data
	* @param: integer debit note id
	* @param: string custom debit note ref
	* @return: void
	* @author: MSA July 2018
	*/
	public function editCustomDNRef()
	{
		$dn_id 			= $this->request->get['dn_id'];
		$custom_debit_note_ref 	= $this->request->get['custom_debit_note_ref'];
		$order_no       = $this->request->get['order_no'];

		if(!empty($dn_id) && !empty($custom_debit_note_ref)) {
			
			$this->load->model('sale/return');
			$this->model_sale_return->updateCustomDNRef($dn_id, $custom_debit_note_ref);

			/*send email for debit not updated*/
			$file_data = array();
			$file_data['order_no'] = $order_no;
			$file_data['debit_note_id'] = $dn_id;
			$enc_data = base64_encode(serialize($file_data));
			$debit_note = new DebitNote($this->registry,$enc_data);
			$dn_data = $debit_note->getDebitNoteData();
			$path = $debit_note->debitNotePdf($dn_data, $dn_data);
			$files[] = DIR_DLOAD_SLR_DBT_NOTE.base64_decode($path);
			// email contents 
			$dn_data['attachments'] = $files;
			$returnClassObj = new ActionDnForLogistic($this);
			$returnClassObj->sendDnUpdateEmail($dn_data);
			/*send email for debit not updated*/
		}

	}

	/**
	* Protected method getReturnDefectedImages() to get replacement defected images
	* @param: integer master return id
	* @param: integer order product id
	* @return: array images list
	* @author: MSA July 2018
	*/
	public function getReturnDefectedImages()
	{
		$images = array();
		if(!empty($this->request->get['master_return_id']) && !empty($this->request->get['order_product_id']))
		{
			$return_info  = new ReturnInfo($this);
			$data['master_return_id'] = $this->request->get['master_return_id'];
			$data['order_product_id'] = $this->request->get['order_product_id'];
			$result = $return_info->getReturnsDefectedImages($data);
			$images = array_column($result, 'image');
		}
		echo json_encode($images);
	}

}
