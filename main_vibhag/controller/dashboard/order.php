<?php
class ControllerDashboardOrder extends Controller {
	public function index() {
		$this->load->language('dashboard/order');
        $this->load->model('sale/order');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_view'] = $this->language->get('text_view');

		$data['token'] = $this->session->data['token'];

        // Getting only successful order count (not canceled or missing)
		$order_total = $this->model_sale_order->getTotalOrders(array(), false);

		if ($order_total > 1000000000000) {
			$data['total'] = round($order_total / 1000000000000, 1) . 'T';
		} elseif ($order_total > 1000000000) {
			$data['total'] = round($order_total / 1000000000, 1) . 'B';
		} elseif ($order_total > 1000000) {
			$data['total'] = round($order_total / 1000000, 1) . 'M';
		} elseif ($order_total > 1000) {
			$data['total'] = round($order_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $order_total;
		}

		$data['order'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL');

		return $this->load->view('dashboard/order.tpl', $data);
	}
}
