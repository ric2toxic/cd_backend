<?php
class ControllerReportAffiliateActivity extends Controller {
	public function index() {
		//$this->load->language('report/affiliate_activity');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('report/affiliate_activity', $data);

		$this->document->setTitle($data['heading_title']);

		if (isset($this->request->get['filter_affiliate'])) {
			$filter_affiliate = $this->request->get['filter_affiliate'];
		} else {
			$filter_affiliate = null;
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = null;
		}

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

		if (isset($this->request->get['filter_affiliate'])) {
			$url .= '&filter_affiliate=' . urlencode($this->request->get['filter_affiliate']);
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
			'text' => $data['text_home']
		);

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('report/affiliate_activity', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'text' => $data['heading_title']
		);

		$this->load->model('report/affiliate');

		$data['activities'] = array();

		$filter_data = array(
			'filter_affiliate'   => $filter_affiliate,
			'filter_ip'         => $filter_ip,
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'             => ($page - 1) * 20,
			'limit'             => 20
		);

		$activity_total = $this->model_report_affiliate->getTotalAffiliateActivities($filter_data);

		$results = $this->model_report_affiliate->getAffiliateActivities($filter_data);

		foreach ($results as $result) {
			$comment = vsprintf($this->language->get('text_' . $result['key']), unserialize($result['data']));

			$data['activities'][] = array(
				'comment'    => str_replace('affiliate_id=', $this->url->link('marketing/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=', 'SSL'), $comment),
				'ip'         => $result['ip'],
				'date_added' => date($data['datetime_format'], strtotime($result['date_added']))
			);
		}

		$data['token'] = $this->session->data['token'];

		$url = '';

		if (isset($this->request->get['filter_affiliate'])) {
			$url .= '&filter_affiliate=' . urlencode($this->request->get['filter_affiliate']);
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		$pagination = new Pagination();
		$pagination->total = $activity_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/affiliate_activity', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($activity_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($activity_total - $this->config->get('config_limit_admin'))) ? $activity_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $activity_total, ceil($activity_total / $this->config->get('config_limit_admin')));

		$data['filter_affiliate'] = $filter_affiliate;
		$data['filter_ip'] = $filter_ip;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/affiliate_activity.tpl', $data));
	}
}