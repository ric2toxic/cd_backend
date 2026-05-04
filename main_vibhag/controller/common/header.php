<?php
class ControllerCommonHeader extends Controller {
	public function index() {
            
		$data['title'] = $this->document->getTitle();

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
        
        $data['token'] = $this->session->data['token'] ?? '';

		$this->load->language('common/header');

		//$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('common/header', $data);

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());

		if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			$data['logged'] = '';

			$data['home'] = $this->url->link('common/dashboard', '', 'SSL');
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
			$data['logout'] = $this->url->link('common/logout', 'token=' . $this->session->data['token'], 'SSL');

			// Online Stores
			$data['stores'] = array();

			$data['stores'][] = array(
				'name' => $this->config->get('config_name'),
				'href' => HTTP_CATALOG
			);

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$data['stores'][] = array(
					'name' => $result['name'],
					'href' => $result['url']
				);
			}

			$data['product_notification'] = false;
			$data['user_questions_notification'] = false;
			
			$data['alerts'] = 0;

			if ($this->user->hasPermission('access', 'catalog/product',true)) {
				$data['product_notification'] = true;
                
                // Reviews
                $data['review_total'] = 0;

                // product moderate to approve
                $this->load->model('inventory/producttomoderate');

                $data['moderate_total'] = $this->model_inventory_producttomoderate->getTotalProducts(array('filter_status' => false));
                $data['moderate_link'] = $this->url->link('inventory/producttomoderate', 'token=' . $this->session->data['token'] . '&filter_status=0', 'SSL');
                
				$data['alerts'] += $data['review_total'] + $data['moderate_total'];
			}

			if($this->user->hasPermission('access', 'review/user_questions',true)){
				$data['user_questions_notification'] = true;
                
                // Un-answered user question
                $this->load->model('review/user_questions');
                $data['user_question_total'] = $this->model_review_user_questions->getTotalUnansweredQuestions();
                $data['user_questions_link'] = $this->url->link('review/user_questions', 'token=' . $this->session->data['token'] . '&filter_answered=0', 'SSL');
                
				$data['alerts'] += $data['user_question_total'];
			}
            
			/*
			* url for common search for orders
			*/
			$data['common_search_orders_url'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL');

			$data['common_search_filter_order_no'] = '';
			if (!empty($this->request->get['filter_order_no'])) {
				$data['common_search_filter_order_no'] = $this->request->get['filter_order_no'];
			}
			$data['common_search_filter_customer'] = '';
			if (!empty($this->request->get['filter_customer'])) {
				$data['common_search_filter_customer'] = $this->request->get['filter_customer'];
            }
            $data['common_search_filter_customer_id'] = '';
			if (!empty($this->request->get['filter_customer_id'])) {
				$data['common_search_filter_customer_id'] = $this->request->get['filter_customer_id'];
			}
			$data['common_search_filter_city'] = '';
			if (!empty($this->request->get['filter_city'])) {
				$data['common_search_filter_city'] = $this->request->get['filter_city'];
			}
			$data['common_search_filter_tracking_number'] = '';
            if (!empty($this->request->get['filter_tracking_no'])) {
            	$data['common_search_filter_tracking_number'] = $this->request->get['filter_tracking_no'];
            }
		}
		$data['is_mobile_site'] ='';
		
		$mbileobj = new Mobile_Detect_Class();
		
		$data['is_mobile_site'] = $mbileobj->isMobile();
		
		if (isset($this->request->get['profiling']) && $this->request->get['profiling'] == DEBUG_SQL_PROFILE)
		{
			Debug::output();
		}

		$data['old_password_status'] = $this->session->data['old_password_status'] ?? 0;
		$data['new_password_string'] = $this->session->data['new_password_string'] ?? '';
		$data['username'] = $this->session->data['username'] ?? '';
		$data['isLoggedIn'] = $this->user->isLogged() ?? 0;
		return $this->load->view('common/header.tpl', $data);
	}
} 