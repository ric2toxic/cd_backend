<?php
class ControllerReactMyAccount extends Controller {

	public function index(){

		if(!$this->customer->isLogged()){
			$this->response->redirect($this->url->link('react/home'));
		}
		
		$this->ordersList();
		
	}

	public function profile(){
		echo "profile";
	}

	public function ordersList(){
		$this->load->model('tool/image');
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$user_id = $this->customer->isLogged();

		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}

    	if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
            $data['custom_store_val'] = $this->session->data['custom_store'];
        } else {
            $data['custom_store_val'] = 'set';
        }

		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
	    $data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

       	$data['home_url'] = $this->url->link('common/home', '', 'SSL'); 

       	$myaccount_language = array();
		$header_language = array();
		$footer_language = array();
		$this->load->autoLoadLanguage('account/account', $myaccount_language);
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);

		$language = $data;
 		
 		$left_menu = array(
						'profiles' => array(	'title' => $myaccount_language['text_title_profile'],
												'icon'	=> 'fa fa-user',
												'sub_menu' => array(
			   												array(
			   														'title' => $myaccount_language['text_title_profile_info'],
			   														'url'	=> $this->url->link('account/edit', '', 'SSL'),
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_manage_address'],
			   														'url'	=> $this->url->link('account/address', '', 'SSL'),
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_save_bank_details'],
			   														'url'	=> $this->url->link('account/bank_details', '', 'SSL'),
			   													),
			   												array(
			   														'title' => $myaccount_language['text_my_returns'],
			   														'url'	=> $this->url->link('account/return/getReturns', '', 'SSL')
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_my_wishlist'],
			   														'url'	=> $this->url->link('account/wishlist','','SSL')
			   													)
												),
										),
						'orders' => array(	'title' => $myaccount_language['text_title_orders'],
											'icon'	=> 'fa fa-bars',
											'sub_menu' => array(
			   												array(
			   														'title' => $myaccount_language['text_title_my_order'],
			   														'filter_order_type' => ORDER_TYPE_FILTER['MY_ORDERS'],
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_payment_pending_order'],
			   														'filter_order_type' => ORDER_TYPE_FILTER['PAYMENT_PENDING_ORDERS'],
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_delivered_order'],
			   														'filter_order_type' => ORDER_TYPE_FILTER['DELIVERED_ORDERS'],
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_return_order'],
			   														'filter_order_type' => ORDER_TYPE_FILTER['RETURNED_ORDERS'],
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_cancel_order'],
			   														'filter_order_type' => ORDER_TYPE_FILTER['CANCELLED_ORDERS'],
			   													),
												),
										),
						'single_list' => array(
							                    'account_statement' => array(	'title' => $myaccount_language['text_title_account_statement'],
																		'url'	=> $this->url->link('account/statement','','SSL'),
																		'icon'	=> 'fa fa-file-excel-o',
																		'show'	=> (in_array($user_id , AC_SMT_BLOCK_CUSTOMERS)) ? 
																		0 : 1 
																	),
												'credit_note' => array(	'title' => $myaccount_language['text_title_credit_note'],
																		'url'	=>  $this->url->link('account/return/getCreditNote', '', 'SSL'),
																		'icon'	=> 'fa fa-file'
																	),
												'credit_application' => array('title'=> $myaccount_language['text_title_credit_application'],
																			  'url'	=> $this->url->link('account/credit_application','','SSL'),
																			  'icon'=> 'fa fa-file'
																	),
												'product_feed' => array('title'=> $myaccount_language['text_product_feed'],
																			  'url'	=> $this->url->link('account/product_feed', '', 'SSL'),
																			  'icon'=> 'fa fa-file-text-o',
																			  'is_dropshipper' => $this->customer->getIsDropshipper()
																	),
												'support'	 => array(	'title' => $myaccount_language['text_title_supoort'],
																		'url'	=> $this->url->link('account/helpdesk', '', 'SSL'),
			   														'icon'	=> 'fa fa-headphones',
																		'icon'	=> 'fa fa-headphones'
																	),
												'logout'	=> array(	'title' => $myaccount_language['text_title_logout'],
																		'url'	=> $this->url->link('account/logout','','SSL'),
																		'icon'	=> 'fa fa-power-off'
																	),
											)
						
					);
 		// set value of order filter type in order list 
 		if(!empty($this->request->get['filter_order_type_value'])){
 			$filter_order_type_value = $this->request->get['filter_order_type_value'];
 		} else {
 			$filter_order_type_value = 0;
 		}

 		$common_order_data = array();
		
		if(!empty($this->request->get['order_id'])){
 			$common_order_data['order_id'] = $this->request->get['order_id'];
 		} else {
 			$common_order_data['order_id'] = '';
 		} 		

 		if(!empty($this->request->get['suborder_id'])){
 			$common_order_data['suborder_id'] = $this->request->get['suborder_id'];
 		} else {
 			$common_order_data['suborder_id'] = '';
 		} 

 		if(!empty($this->request->get['searching_value'])){
 			$common_order_data['searching_value'] = $this->request->get['searching_value'];
 		} else {
 			$common_order_data['searching_value'] = '';
 		} 
 				
 		
 		// set order type title in header section of order list when click on filter order type
 		$order_type_title = array(
 				'0'=> $myaccount_language['text_title_my_order'],
 				'1'=> $myaccount_language['text_title_payment_pending_order'],
 				'2'=> $myaccount_language['text_title_delivered_order'],
 				'3'=> $myaccount_language['text_title_cancel_order'],
 				'4'=> $myaccount_language['text_title_return_order'],
 			);

 		// get a menus and set on not record found components
 		$this->load->model('catalog/category');
 		$data['menus'] = array();

        $this->load->model('module/popular_search');
       	$getPopularTag = $this->model_module_popular_search->getPopularTags();
        $data['popular_tags'] = array();
        foreach($getPopularTag as $values){
           	$data['popular_tags'][] = array(
            	'text' => $values['popular_search'],
             	'link' => $values['link'],
             	'href' => html_entity_decode($this->url->link('product/search','&search='.html_entity_decode(trim($values['popular_search'])),'SSL'))
            );
      	}

 		$data['myaccount_language'] = json_encode($myaccount_language);
		$data['header_language'] 	= json_encode($header_language);
		$data['footer_language'] 	= json_encode($footer_language);
		$data['left_menu']		 	= json_encode($left_menu);
		$data['lang']      		 	= $header_language['code'];
		$data['direction'] 			= $header_language['direction'];
		$data['menus'] 				= json_encode($data['menus']);
		$data['popular_tags'] 		= json_encode($data['popular_tags']);
		$data['order_type_title'] 	= json_encode($order_type_title[$filter_order_type_value]);
		$data['filter_order_type_value'] = json_encode($filter_order_type_value);
		$data['common_order_data'] 	= json_encode($common_order_data);

		if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
       		$data['international_store'] = 1;
      	} else {
            $data['international_store'] = 0;
        }

		if( file_exists( DIR_TEMPLATE . $this->config->get('config_template') . 'template/react/myaccount.tpl' ) ) {
			$this->response->setOutput( $this->load->view( $this->config->get('config_template') . 'template/react/my_account.tpl' , $data) );
		} else {
			$this->response->setOutput( $this->load->view( 'default/template/react/my_account.tpl' , $data) );
		}
	}
}