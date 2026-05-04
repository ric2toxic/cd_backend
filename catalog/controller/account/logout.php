<?php
class ControllerAccountLogout extends Controller {
	public function index() {

        $this->load->model('account/customer');
        $this->model_account_customer->removeCustomerToken($this->customer->getId());
        
        $this->event->trigger('pre.customer.logout');
        $this->customer->logout();
        //$this->cart->clear();

        unset($this->session->data['wishlist']);
        unset($this->session->data['shipping_address']);
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_address']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);
        unset($this->session->data['seller_store_id']);
        setcookie("customer_id", "", time() - 3600);
        setcookie("customer_mobile", "", time() - 3600);
        setcookie("ws_access_token", "", time() - 3600);

        $this->event->trigger('post.customer.logout');

        $this->response->redirect($this->url->link('common/home','','SSL'));

	}
}