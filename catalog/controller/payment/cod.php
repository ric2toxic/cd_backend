<?php

class ControllerPaymentCod extends Controller {
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
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cod.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/cod.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/cod.tpl', $data);
		}
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'cod') {
			$this->load->model('checkout/order');
			$this->load->model('dialer/dialer');

            $input = array();
            $input['order_id'] = $this->session->data['order_id'];
            $input['order_status_id'] = $this->config->get('cod_order_status_id');
			$this->model_checkout_order->addOrderHistory($input);

			// call desktop dailer api to send COD order alert
			$this->model_dialer_dialer->sendDataToDesktopDailer($this->customer->getTelephone(), '999999');
		}
	}

}
