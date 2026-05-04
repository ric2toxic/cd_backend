<?php
class ControllerCommonCart extends Controller {
	public function index() {
		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('common/cart', $data);

		$this->load->language('common/cart');

		$total_sets = $this->cart->countProducts();
		$data['text_items'] = sprintf($this->language->get('text_items'), $total_sets);
		$data['total_in_cart'] = $total_sets;
		$data['cart_url'] = $this->url->link('checkout/cart', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/cart.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/cart.tpl', $data);
		} else {
			return $this->load->view('default/template/common/cart.tpl', $data);
		}
	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}
