<?php
class ControllerPaymentMswipeCredit extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/success');
        
        if ( isset($this->request->post['one_page_checkout_payment_method']) ) { 
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mswipe_credit.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/mswipe_credit.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/mswipe_credit.tpl', $data);
		}
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] != 'mswipe_credit') {
			return;
		}
		
		$selector = array('order' => array());
		$order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
		if (!empty($this->session->data['net_order_totals'])) {
				$order_info['total'] = $this->session->data['net_order_totals'];
		}
		
		$this->load->model('checkout/order');

          $input = array();
          $input['order_id'] = $this->session->data['order_id'];
          $input['order_status_id'] = $this->config->get('mswipe_credit_order_status_id');
		$this->model_checkout_order->addOrderHistory($input);
	}
}
