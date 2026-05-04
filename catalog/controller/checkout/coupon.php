<?php
class ControllerCheckoutCoupon extends Controller {
	public function index() {
		if ($this->config->get('coupon_status')) {
			$this->load->language('checkout/coupon');

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_coupon'] = $this->language->get('entry_coupon');

			$data['entry_coupon_placeholder'] = $this->language->get('entry_coupon_placeholder');

			$data['button_coupon'] = $this->language->get('button_coupon');

			$coupon_data = $this->cart->getCoupon();
            if (isset($coupon_data['coupon'])) {
                $data['coupon'] = $coupon_data['coupon'];
            } else {
                $data['coupon'] = '';
            }

            if(!empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code'])){
                $data['coupon'] = $coupon_data['wsb_store_voucher'];
            }

            if(!empty($coupon_data['wsb_topay_coupon'])){
                $data['coupon'] = $coupon_data['wsb_topay_coupon'];
            }

			if (isset($this->request->get['redirect']) && !empty($this->request->get['redirect'])) {
				$data['redirect'] = $this->request->get['redirect'];
			} else {
				$data['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
			}

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/coupon.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/checkout/coupon.tpl', $data);
			} else {
				return $this->load->view('default/template/checkout/coupon.tpl', $data);
			}
		}
	}

	public function coupon() {
		$this->load->language('checkout/coupon');

		$json = array();

		$this->load->model('checkout/coupon');

		if (isset($this->request->post['coupon'])) {
			$coupon = $this->request->post['coupon'];
		} else {
			$coupon = '';
		}

		if(!empty($coupon)){
            $coupon_data = array();
            if( $store_code = SalesStaff::checkStoreVoucher( $this->db , $coupon ) ) {
                $coupon_data['wsb_store_voucher'] = $coupon;
                $coupon_data['wsb_store_code'] = $store_code;
            }
            else if( $coupon == 'topay'){
                $coupon_data['wsb_topay_coupon'] = $coupon;
            }
            else{
                $coupon_info = $this->model_checkout_coupon->getCoupon($coupon);
                if ( $coupon_info['coupon_message']['status'] ) {
                    $coupon_data['coupon'] = $coupon;
                }
                else{
                    $json['error'] = $coupon_info['coupon_message']['message'] ?? $this->language->get('error_coupon');
                }
            }

		    if(!isset($json['error'])){
                $this->cart->setCoupon( $coupon_data );
                $this->session->data['success'] = $this->language->get('text_success');
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
            }
        }
        else{
            $json['error'] = $this->language->get('error_empty');
        }

        /*
        if( $store_code = SalesStaff::checkStoreVoucher( $this->db , $coupon ) ){
            $this->session->data['wsb_store_voucher'] = $coupon;
            $this->session->data['wsb_store_code'] = $store_code;
            $this->session->data['success'] = $this->language->get('text_success');
            $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

        }
        else if( $coupon == 'topay'){
            $this->session->data['wsb_topay_coupon'] = $coupon;
            $this->session->data['success'] = $this->language->get('text_success');
            $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
        }
        else{
            $coupon_info = $this->model_checkout_coupon->getCoupon($coupon);
    		if (empty($this->request->post['coupon'])) {
    			$json['error'] = $this->language->get('error_empty');
    			unset($this->session->data['coupon']);
       			unset($this->session->data['wsb_store_voucher']);
       			unset($this->session->data['wsb_store_code']);
       			unset($this->session->data['wsb_topay_coupon']);
    		} elseif ($coupon_info) {
    			$this->session->data['coupon'] = $this->request->post['coupon'];
    			$this->session->data['success'] = $this->language->get('text_success');

    			$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
    		} else {
                unset($this->session->data['wsb_store_voucher']);
                unset($this->session->data['wsb_store_code']);
                unset($this->session->data['wsb_topay_coupon']);
    			$json['error'] = $this->language->get('error_coupon');
    		}
        }
        */

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
