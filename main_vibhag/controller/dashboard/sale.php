<?php
class ControllerDashboardSale extends Controller {
	public function index() {
		$this->load->language('dashboard/sale');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_view'] = $this->language->get('text_view');

		$data['token'] = $this->session->data['token'];

		$this->load->model('report/sale');

		$sale_total = $this->model_report_sale->getTotalSales();

		if ($sale_total > 1000000000000) {
			$data['total'] = round($sale_total / 1000000000000, 1) . 'T';
		} elseif ($sale_total > 1000000000) {
			$data['total'] = round($sale_total / 1000000000, 1) . 'B';
		} elseif ($sale_total > 1000000) {
			$data['total'] = round($sale_total / 1000000, 1) . 'M';
		} elseif ($sale_total > 1000) {
			$data['total'] = round($sale_total / 1000, 1) . 'K';
		} else {
			$data['total'] = round($sale_total);
		}

		$data['sale'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL');

		return $this->load->view('dashboard/sale.tpl', $data);
	}
}
