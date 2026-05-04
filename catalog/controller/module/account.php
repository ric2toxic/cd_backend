<?php
class ControllerModuleAccount extends Controller {
	public function index() {
		
        $data['ms_seller_created'] = $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId());
		$data = array_merge($this->load->language('multiseller/multiseller'), $data);

		$this->load->language('module/account');
		$this->load->language('account/account');
		if(!in_array($this->customer->getId() , AC_SMT_BLOCK_CUSTOMERS))
		{
          $data['statement'] = $this->url->link('account/statement', '', 'SSL');
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_forgotten'] = $this->language->get('text_forgotten');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_edit'] = $this->language->get('text_edit');
        $data['text_bank_details'] = $this->language->get('text_bank_details');
		$data['text_my_returns'] = $this->language->get('text_my_returns');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_credit_note'] = $this->language->get('text_credit_note');
		$data['text_wishlist'] = $this->language->get('text_title_my_wishlist');
		$data['text_order'] = $this->language->get('text_title_orders');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_return'] = $this->language->get('text_my_returns');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_recurring'] = $this->language->get('text_recurring');
		$data['text_my_orders'] = $this->language->get('text_title_orders');
		$data['text_dashboard'] = $this->language->get('text_dashboard');
		$data['text_product_feed'] = $this->language->get('text_product_feed');
		$data['text_support'] = $this->language->get('text_support');
		$data['text_credit_apply'] = $this->language->get('text_credit_apply');
		$data['text_account_statement'] = $this->language->get('text_account_statement');
		$data['text_go_to_seller_dashboard'] = $this->language->get('text_go_to_seller_dashboard');
		$data['text_title_profile'] = $this->language->get('text_title_profile');
		$data['text_title_profile_info'] = $this->language->get('text_title_profile_info');
		$data['text_title_manage_address'] = $this->language->get('text_title_manage_address');
		$data['text_title_save_bank_details'] = $this->language->get('text_title_save_bank_details');
		$data['text_title_my_order'] = $this->language->get('text_title_my_order');
		$data['text_title_payment_pending_order'] = $this->language->get('text_title_payment_pending_order');
		$data['text_title_delivered_order'] = $this->language->get('text_title_delivered_order');
		$data['text_title_return_order'] = $this->language->get('text_title_return_order');
		$data['text_title_cancel_order'] = $this->language->get('text_title_cancel_order');
		

		$data['logged'] = $this->customer->isLogged();
		$data['is_dropshipper'] = $this->customer->getIsDropshipper();
		$data['seller_login'] = $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId());
		$data['register'] = $this->url->link('account/register', '', 'SSL');
		$data['login'] = $this->url->link('account/login', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['edit'] = $this->url->link('account/edit', '', 'SSL');
        $data['bank_details'] = $this->url->link('account/bank_details', '', 'SSL');
		$data['my_returns'] = $this->url->link('account/return/getReturns', '', 'SSL');
		$data['credit_note'] = $this->url->link('account/return/getCreditNote', '', 'SSL');
		$data['password'] = $this->url->link('account/password', '', 'SSL');
		$data['address'] = $this->url->link('account/address', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['reward'] = $this->url->link('account/reward', '', 'SSL');
		$data['return'] = $this->url->link('account/return', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['recurring'] = $this->url->link('account/recurring', '', 'SSL');
		$data['product_feed'] = $this->url->link('account/product_feed', '', 'SSL');		
		$data['support'] = $this->url->link('account/helpdesk', '', 'SSL');
		$data['manufacturer_dashboard_link'] = HTTPS_SERVER.'seller_panel/#';
		$data['credit_application'] = $this->url->link( 'account/credit_application','','SSL' );
		$data['pending_order'] = $this->url->link('account/order&filter_order_type_value=1', '', 'SSL');
		$data['delivered_order'] = $this->url->link('account/order&filter_order_type_value=2', '', 'SSL');
		$data['return_order'] = $this->url->link('account/order&filter_order_type_value=3', '', 'SSL');
		$data['cancel_order'] = $this->url->link('account/order&filter_order_type_value=4', '', 'SSL');

		$data['active'] = '';

         if($this->request->get['route'] == "account/product_feed")
         {
         	$data['active'] = 'product_feed';
         }

         if($this->request->get['route'] == "account/return/getCreditNote")
         {
         	$data['active'] = 'credit_note';
         }

         if($this->request->get['route'] == "account/return/getReturns")
         {
         	$data['active'] = 'return';
         }

         if($this->request->get['route'] == "account/helpdesk" || $this->request->get['route'] == "account/helpdesk/createHelpdeskTicket" || $this->request->get['route'] == "account/helpdesk/viewSingleTicketAndConversation")
         {
         	$data['active'] = 'helpdesk';
         }
		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/account.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/account.tpl', $data);
		} else {
			return $this->load->view('default/template/module/account.tpl', $data);
		}
	}
}
