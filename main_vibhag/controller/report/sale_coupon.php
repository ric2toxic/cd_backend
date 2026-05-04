<?php
class ControllerReportSaleCoupon extends Controller {
	public function index() {
		//$this->load->language('report/sale_coupon');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('report/sale_coupon', $data);

		$this->document->setTitle($data['heading_title']);

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('report/sale_coupon', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/coupon');

		$data['coupons'] = array();

		$filter_data = array(
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$coupon_total = $this->model_report_coupon->getTotalCoupons($filter_data);

		$results = $this->model_report_coupon->getCoupons($filter_data);

		foreach ($results as $result) {
			$data['coupons'][] = array(
				'name'   => $result['name'],
				'code'   => $result['code'],
				'orders' => $result['orders'],
				'total'  => $this->currency->format($result['total'], $this->config->get('config_currency')),
				'edit'   => $this->url->link('marketing/coupon/edit', 'token=' . $this->session->data['token'] . '&coupon_id=' . $result['coupon_id'] . $url, 'SSL')
			);
		}

		$data['token'] = $this->session->data['token'];

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		$pagination = new Pagination();
		$pagination->total = $coupon_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/sale_coupon', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($coupon_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($coupon_total - $this->config->get('config_limit_admin'))) ? $coupon_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $coupon_total, ceil($coupon_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/sale_coupon.tpl', $data));
	}
}