<?php
class ControllerAccountPanelNAchNotAvailableDates extends Controller {
	public function index() {
		$this->document->setTitle('Bank Holiday Dates');
		
		$this->getList();
	}

	protected function getList() {
		
		$token       = $this->session->data['token'];
		$url         = '';
		$filter_data = array();

		$filter_data['filter_date_from'] = $this->request->post['filter_date_from'] ?? NULL;
		$filter_data['filter_date_to']   = $this->request->post['filter_date_to'] ?? NULL;
		$filter_data['filter_month']     = $this->request->post['filter_month'] ?? date('m');
		$filter_data['filter_year']      = $this->request->post['filter_year'] ?? date('Y');
		
		if (isset($this->request->post['filter_date_from'])) {
			$url .= '&filter_date_from=' . urlencode(html_entity_decode($this->request->post['filter_date_from'], ENT_QUOTES, 'UTF-8'));
		} 
		if (isset($this->request->post['filter_date_to'])) {
			$url .= '&filter_date_to=' . urlencode(html_entity_decode($this->request->post['filter_date_to'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->post['filter_month'])) {
			$url .= '&filter_month=' . $this->request->post['filter_month'];
		}

		if (isset($this->request->post['filter_year'])) {
			$url .= '&filter_year=' . $this->request->post['filter_year'];
		}

		$page = $this->request->post['page'] ?? 1;

		$filter_data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
		$filter_data['limit'] = $this->config->get('config_limit_admin');

	    // URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;

		if (isset($this->request->get['page'])) {
			$general_url .= '&page=' . $this->request->get['page'];
		}

		$data = array();// Initializing the data array to be passed on to template files
		$data = $filter_data;

		$this->load->autoLoadLanguage('accounts/nach_not_available_dates',$data);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => 'Home',
			'href' => $this->url->link('common/dashboard', 'token=' . $token, 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => 'Bank Holiday Dates',
			'href' => $this->url->link('account_panel/nach_not_available_dates', 'token=' . $token . $general_url, 'SSL')
		);

		//Get result for all tentative NACH for given filters
		
		$select_data = array('holiday_date',
                             'remark',
                             'user_id',
                             'user_name',
                             'date_added'
                            );

		$bank_holiday = BankHolidayAction::getInstance($this->registry);
		$results = $bank_holiday->getBankHolidayDates($filter_data, $select_data);

		// Results
		$not_nach_dates = $results ?? array();
		//Total count for pagination
		$total_count = count($not_nach_dates);
		
		$data['not_nach_dates'] = $not_nach_dates;
		$data['months'] = array(
                           1  => 'January',
                           2  => 'February',
                           3  => 'March',
                           4  => 'April',
                           5  => 'May',
                           6  => 'June',
                           7  => 'July',
                           8  => 'August',
                           9  => 'September',
                           10 => 'October',
                           11 => 'November',
                           12 => 'December'
			              );
		// URL for pagination
		$pagination_url = $url;

		$pagination = new Pagination();
		$pagination->total = $total_count;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('account_panel/nach_not_available_dates', 'token=' . $token . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['token'] = $token;
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_count) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_count - $this->config->get('config_limit_admin'))) ? $total_count : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_count, ceil($total_count / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('account_panel/nach_not_available_dates.tpl', $data));
	}

	/**
     * @info: Public method to add not available date for NACH into DB 
     * @author: Nishu, March 2019
	*/
	public function addDates(){
		$data      = $this->request->post;
		$error_msg = "";
		if(!empty($data['holiday_date']) && !empty($data['remark'])){
			$nach_obj      = new NachBehaviour($this->registry);
			$error_summary = $nach_obj->addBankHolidayDates($data['holiday_date'], $data['remark']);

			//If any error exist, handle
	        if(!empty($error_summary)){
	        	foreach ($error_summary as $key => $value) {
	        		$error_msg .= " ".($key+1).":- ".$value." ";
	        	}
	        }else{
				$error_msg = "Successfully Bank Holiday Date(s) added.";
	        }
		}else{
			$error_msg = "Holiday Date or Remark is missing to add bank holiday, So no bank holiday date added.";
		}
		print_r($error_msg);
		exit();
	}
}
