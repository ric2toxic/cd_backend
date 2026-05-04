<?php
 require_once('system.php');
 require_once(DIR_SYSTEM.'library/customer.php');

class ProfileController extends SystemController {

    private $error = array();

    public function __construct($params) 
    {
      parent::__construct($params);
      $this->registry->set('customer',new Customer($this->registry));
    }

	public function left_menu()
	{
       	$myaccount_language = array();
		$this->load->autoLoadLanguage('account/account', $myaccount_language);
 		
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
			   														'url'	=> $this->url->link('account/order','','SSL')
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_payment_pending_order'],
			   														'url'	=> $this->url->link('account/order&filter_order_type_value=1','','SSL')
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_delivered_order'],
			   														'url'	=> $this->url->link('account/order&filter_order_type_value=2','','SSL')
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_return_order'],
			   														'url'	=> $this->url->link('account/order&filter_order_type_value=4','','SSL')
			   													),
			   												array(
			   														'title' => $myaccount_language['text_title_cancel_order'],
			   														'url'	=> $this->url->link('account/order&filter_order_type_value=3','','SSL')
			   													),
												),
										),
						'single_list' => array(
							                    'account_statement' => array(	'title' => $myaccount_language['text_title_account_statement'],
																		'url'	=> $this->url->link('account/statement','','SSL'),
																		'icon'	=> 'fa fa-file-excel-o'
																	),
												'credit_application' => array('title'=> $myaccount_language['text_title_credit_application'],
																			  'url'	=> $this->url->link('account/credit_application','','SSL'),
																			  'icon'=> 'fa fa-file'
											   						),
											    'credit_note' => array('title' => $myaccount_language['text_credit_note'],
			   														'url'	=> $this->url->link('account/return/getCreditNote', '', 'SSL'),
			   														'icon'	=> 'fa fa-file'
			   													),
			   									'support' =>array(
			   														'title' => $myaccount_language['text_support'],
			   														'url'	=> $this->url->link('account/helpdesk', '', 'SSL'),
			   														'icon'	=> 'fa fa-headphones',
			   													),
												'product_feed' => array('title'=> $myaccount_language['text_product_feed'],
																			  'url'	=> $this->url->link('account/product_feed', '', 'SSL'),
																			  'icon'=> 'fa fa-file-text-o',
																			  'is_dropshipper' => $this->customer->getIsDropshipper()
																	),
												'logout'	=> array(	'title' => $myaccount_language['text_title_logout'],
																		'url'	=> $this->url->link('account/logout','','SSL'),
																		'icon'	=> 'fa fa-power-off'
																	),
											)
						
					);
  	
  	    $this->data_packet->statusCode = 200;
        $this->data_packet->data       = $left_menu;
        $this->data_packet->message    = "Data Successfully Found.";
        
        return $this->data_packet;
	}

}