<?php
class ControllerSaleCustomer extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('sale/customer');

		$this->load->model('sale/customer');
		$this->load->model('sale/order');

		$this->getList();
	}

	public function add() {

		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_customer->addCustomer($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {

		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->request->post['changes_data'] = $_POST['changes_data'];

        	/*$this->load->model('account/customer', 'frontend');
        	$fields=array("has_website");
    		$customerCustomData=$this->frontend_model_account_customer->getCustomerDetails($this->request->get['customer_id'],$fields);
    		if($customerCustomData['status'] != $this->request->post['status'] && $customerCustomData['has_website'] > 0){
    			$request=array();
    			$request['status']=$this->request->post['status'];
    			$request['customer_id']=$this->request->get['customer_id'];
    			$this->updateCustomerWebsiteStatusUsingCustomerStatus($request);
    		}*/
			$this->model_sale_customer->editCustomer($this->request->get['customer_id'], $this->request->post);

			$this->model_sale_customer->customer_preferences($this->request->get['customer_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_telephone'])) {
				$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_city'])) {
				$url .= '&filter_city=' . $this->request->get['filter_city'];
			}

			if (isset($this->request->get['filter_customer_type'])) {
				$url .= '&filter_customer_type=' . $this->request->get['filter_customer_type'];
			}

			if (isset($this->request->get['filter_gst_number'])) {
				$url .= '&filter_gst_number=' . $this->request->get['filter_gst_number'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}


		$this->getForm();
	}

	public function delete() {

		$this->load->model('sale/customer');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $customer_id) {
				$this->model_sale_customer->deleteCustomer($customer_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	public function unlock() {
		$this->load->language('sale/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/customer');

		if (isset($this->request->get['email']) && $this->validateUnlock()) {
			$this->model_sale_customer->deleteLoginAttempts($this->request->get['email']);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
    
    /*
     * Private method to sanitize and set default (if needed) for the 
     * filters input, on the list page etc
     * 
     * @param $source - Array - eg: POST / GET Request array
     * 
     * @return Array - $filter_data
     */
    private function validateFilters(array $source) : array {
        
        // FILTERS - Sanitization and Setting default (if not provided and needed)
        $filter_data = array();
		$filter_data['filter_name']                 = trim($source['filter_order_no'] ?? '');
		$filter_data['filter_email']                = $source['filter_customer_name'] ?? NULL;
		$filter_data['filter_telephone']           = $source['filter_customer_id'] ?? NULL;
        $filter_data['filter_status'] = (int)($source['filter_today_active_checksum'] ?? 1);
        $filter_data['filter_date_added']  = $source['filter_nach_debit_date_from'] ?? date('Y-m-d');
        $filter_data['filter_city']    = $source['filter_nach_debit_date_to'] ?? date('Y-m-d');
        $filter_data['filter_customer_type']                = $source['filter_status'] ?? 'NOT_DONE';
        $filter_data['filter_gst_number']  = (int)($source['filter_deffered_by_customer'] ?? 0);
        
        $filter_data['filter_customer_id']              = $source['filter_order_no'] ?? NULL;
        $filter_data['filter_master_id']              = $source['filter_order_no'] ?? NULL;
        $filter_data['filter_order_count']              = $source['filter_order_no'] ?? NULL;
        $filter_data['filter_umrn_lan']              = $source['filter_order_no'] ?? NULL;
        
        
        
        		$filter_name              = $this->request->get['filter_name'] ?? null;
		$filter_email             = $this->request->get['filter_email'] ?? null;
		$filter_telephone         = $this->request->get['filter_telephone'] ?? null;
		$filter_status            = $this->request->get['filter_status'] ?? null;
		$filter_date_added        = $this->request->get['filter_date_added'] ?? null;
		$filter_city              = $this->request->get['filter_city'] ?? null;
		$filter_customer_type     = $this->request->get['filter_customer_type'] ?? 'all';
		$filter_gst_number        = $this->request->get['filter_gst_number'] ?? null;
		$filter_customer_id       = $this->request->get['filter_customer_id'] ?? null;
		$filter_master_id         = $this->request->get['filter_master_id'] ?? null;
		$filter_order_count       = $this->request->get['filter_order_count'] ?? null;
		$filter_umrn_lan           = $this->request->get['filter_umrn_lan'] ?? null;
        
        return $filter_data;        
    }
    

	protected function getList() {
		$data = array();
		$this->load->autoLoadLanguage('sale/customer',$data);

		$this->document->setTitle($this->language->get('heading_title'));
		
		// Page Limit (Customers per page)
		$page_limit = 15; // Setting to 15, to increase page load
		
		// GET Input Params to variables.
		$ordering_customer        = $this->request->get['ordering_customer'] ?? null;
		$filter_referral_code     = $this->request->get['filter_referral_code'] ?? null;
		$filter_name              = $this->request->get['filter_name'] ?? null;
		$filter_email             = $this->request->get['filter_email'] ?? null;
		$filter_telephone         = $this->request->get['filter_telephone'] ?? null;
		$filter_date_added        = $this->request->get['filter_date_added'] ?? null;
		$filter_city              = $this->request->get['filter_city'] ?? null;
		$filter_customer_type     = $this->request->get['filter_customer_type'] ?? 'all';
		$filter_gst_number        = $this->request->get['filter_gst_number'] ?? null;
		$filter_customer_id       = $this->request->get['filter_customer_id'] ?? null;
		$filter_master_id         = $this->request->get['filter_master_id'] ?? null;
		$filter_order_count       = $this->request->get['filter_order_count'] ?? null;
		$filter_umrn_lan           = $this->request->get['filter_umrn_lan'] ?? null;

		$sort = $this->request->get['sort'] ?? 'cc.date_modified';
		$order = $this->request->get['order'] ?? 'DESC';
        $page = $this->request->get['page'] ?? 'FIRST';
        
        if ($page == 'FIRST') $sort = 'cc.date_modified';
		
		
		// Creating URL based on Filters
		$url = '';

        if (isset($this->request->get['ordering_customer'])) {
			$url .= '&ordering_customer=' . urlencode(html_entity_decode($this->request->get['ordering_customer'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_referral_code'])) {
			$url .= '&filter_referral_code=' . urlencode(html_entity_decode($this->request->get['filter_referral_code'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}
		if (isset($this->request->get['filter_customer_type'])) {
			$url .= '&filter_customer_type=' . $this->request->get['filter_customer_type'];
		}
		if (isset($this->request->get['filter_gst_number'])) {
			$url .= '&filter_gst_number=' . $this->request->get['filter_gst_number'];
		}
		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}
		if (isset($this->request->get['filter_master_id'])) {
			$url .= '&filter_master_id=' . $this->request->get['filter_master_id'];
		}
		if (isset($this->request->get['filter_order_count'])) {
			$url .= '&filter_order_count=' . $this->request->get['filter_order_count'];
		}
		if (isset($this->request->get['filter_umrn_lan'])) {
			$url .= '&filter_umrn_lan=' . $this->request->get['filter_umrn_lan'];
		}

		$general_url = $url;

		if (isset($this->request->get['sort'])) {
			$general_url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$general_url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$general_url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
		);

		$data['add'] = $this->url->link('sale/customer/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
		$data['delete'] = $this->url->link('sale/customer/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');

		$data['customers'] = array();

		$filter_data = array(
			'ordering_customer'         => $ordering_customer,  
			'filter_referral_code'      => $filter_referral_code,
			'filter_name'              => trim($filter_name),
			'filter_customer_id'       => $filter_customer_id,
			'filter_master_id'         => $filter_master_id,
			'filter_email'             => trim($filter_email),
            'filter_telephone'         => trim($filter_telephone),
			'filter_date_added'        => $filter_date_added,
			'filter_city'    		   => $filter_city,
			'filter_customer_type'     => $filter_customer_type,
			'filter_gst_number'        => $filter_gst_number,
			'filter_order_count'       => $filter_order_count, 
			'filter_umrn_lan'          => $filter_umrn_lan,
			'sort'                     => $sort,
			'order'                    => $order,
			'page_id'                    => $page,
			'limit'                    => $page_limit
		);

        if ($sort == 'c.customer_id') {
            $results = $this->model_sale_customer->getCustomersWithoutCart($filter_data);
        } else {
            $results = $this->model_sale_customer->getCustomers($filter_data);
        }

        $total = sizeof($results);
        if ($sort !== 'c.customer_id' && $total < $page_limit) {
            // if required number of records not found with inner join query, 
            // then find the remaining records with left join query on cart
            $filter_data['limit'] = $page_limit - $total;
            $filter_data['page_id'] = 0;
            $res = $this->model_sale_customer->getCustomersWithoutCart($filter_data);
            $results = array_merge($results, $res);
            $sort = 'c.customer_id';
        }
        
        $customer_ids = array_column($results,'customer_id');
        
        // Getting Sales (CRM) Agent names for these customers
        $this->load->model('lead/lead', 'frontend');
        $agent_names = $this->frontend_model_lead_lead->getCustomerAgentName($customer_ids);
        
        // Finding all the related id(s) and its duplicate/master marking status
        $duplicate_master_ids = $this->model_sale_customer->getAllMasterDuplicateCustomerIds($customer_ids);

        $this->load->model('sale/customer_credit_application');

        //Check is WSB_credit for that customer is enabled or not
        $wsb_credit = new WsbCreditPayment($this);
        $wsb_credit_details = $wsb_credit->getWsbCreditDetailByCustomerIds($customer_ids);

        $credit_leads_arr = $this->model_sale_customer_credit_application->checkCreditLeadByCustomerIds(implode(",", $customer_ids));

		foreach ($results as $result) {
            
            $customer_id = $result['customer_id'];
            $wsb_credit_detail = $wsb_credit_details[$customer_id] ?? array();
           
            $has_approved_wsb_credit = $wsb_credit_detail['status'] ?? '';

            $data['customers'][] = array(
				'customer_id'    => $result['customer_id'],
				'master_id'		 => $result['master_id'],
				'name'           => $result['name'],
				'agent_name'     => isset($agent_names[$result['customer_id']]) ? $agent_names[$result['customer_id']] : 0 ,
				'email'          => $result['email'],
                'telephone'      => $result['telephone'],
				'date_added'     => date($data['date_format_short'], strtotime($result['date_added'])),
                'cart_items'     => $result['cart_items'] ? $result['cart_items']: '--' ,
                'last_cart_modified' => ($result['last_cart_modified'] && $result['last_cart_modified'] != '0000-00-00 00:00:00') ? date($data['datetime_format'], strtotime($result['last_cart_modified'])) : 'Never',
				'is_dropshipper' => $result['is_dropshipper'],
				'edit'           => $this->url->link('sale/customer/edit', 
                                                     'token=' . $this->session->data['token'] . 
                                                     '&customer_id=' . $result['customer_id'] . $url, 'SSL'),
				'credit_tab'     => $this->url->link('sale/customer_credits', 
                                                     'token=' . $this->session->data['token'] .
                                                     '&customer_id=' . $result['customer_id'] . $url, 'SSL'),
				'edit_bank_detail' => $this->url->link('sale/customer/bankDetail', 
                                                     'token=' . $this->session->data['token'] .
                                                     '&customer_id=' . $result['customer_id'] . $url, 'SSL'),
                'app_installed'  => ($result['ws_gcm_registration_id'] != '') ? true : false,
                'company'        => isset($result['company']) ? $result['company'] : '',
                'city'           => isset($result['city']) ? $result['city'] : '',
				'total_orders_today' => $result['total_orders_today'],
				'buildup_time' => $result['buildup_time'] ? $result['buildup_time'] : '--',
				'id_status'=> $duplicate_master_ids[$result['customer_id']]['id_status'],
                            'self_order' => $result['self_order'],               
				'has_website'    => $result['has_website'],     
				'has_approved_wsb_credit' => $has_approved_wsb_credit,
                'credit_lead' => $credit_leads_arr[$result['customer_id']] ?? 0
            );
        }

        $total_results = sizeof($results);
        $first_result = reset($results);
        $last_result = end($results);

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$sort_url = $url;

		if ($order == 'ASC') {
			$sort_url .= '&order=DESC';
		} else {
			$sort_url .= '&order=ASC';
		}

		// if (isset($this->request->get['page'])) {
		// 	$sort_url .= '&page=' . $this->request->get['page'];
		// }

		$data['sort_date_added'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.date_added' . $sort_url, 'SSL');
        $data['sort_last_cart_modified'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=cc.date_modified' . $sort_url, 'SSL');
        
		$pagination_url = $url;

		$pagination_url .= '&sort=' . $sort;

		if (isset($this->request->get['order'])) {
			$pagination_url .= '&order=' . $this->request->get['order'];
        }

        $next_page_id = '';
        $prev_page_id = '';

        if ($sort == "cc.date_modified") {
            $next_page_id = $last_result['last_cart_modified'];
            $prev_page_id = $first_result['last_cart_modified'];
        } else if ($sort == "c.date_added") {
            $next_page_id = $last_result['date_added'];
            $prev_page_id = $first_result['date_added'];
        } else if ($sort == "c.customer_id") {
            $next_page_id = $last_result['customer_id'];
            $prev_page_id = $first_result['customer_id'];
        }

		$pagination = new PaginationV2();
        $pagination->page = $page;
        $pagination->next = $next_page_id;
        $pagination->prev = $prev_page_id;
        $pagination->limit = $page_limit;
        $pagination->total = $total_results;
		$pagination->url = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['filter_name']              = $filter_name;
		$data['filter_email']             = $filter_email;
        $data['filter_telephone']         = $filter_telephone;
		$data['filter_date_added']        = $filter_date_added;
		$data['filter_city']              = $filter_city;
		$data['filter_customer_type']     = $filter_customer_type;
		$data['filter_gst_number']        = $filter_gst_number;
		$data['filter_customer_id']       = $filter_customer_id;
		$data['filter_master_id']         = $filter_master_id;
		$data['filter_umrn_lan']          = $filter_umrn_lan;
		$data['filter_order_count']       = $filter_order_count;
		$data['btn_update_bank_detail']   = $this->language->get('btn_update_bank_detail');

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['duplicate_customer_url'] = $this->url->link('sale/customer/downloadDuplicateCustomers', 'token=' . $this->session->data['token'] , 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['duplicate_popup'] = $this->load->view('sale/marked_duplicate_popup.tpl');
		$this->response->setOutput($this->load->view('sale/customer_list.tpl', $data));
	}

	protected function getForm() {
		$data = array();
		$this->load->autoLoadLanguage('sale/customer',$data);

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['customer_id']) ? $data['text_add'] : $data['text_edit'];

		$data['token'] = $this->session->data['token'];
		if(isset($this->request->get['customer_id']))
		{
		  $data['categories_data'] = $this->model_sale_customer->get_customer_preferences($this->request->get['customer_id']);
		}
		else
		{
			$data['categories_data'] = array();
		}
		

		$category = array();
		$filter_ids = array();

		foreach ($data['categories_data'] as $value) {
			if(!empty($value['category_id'])){
				$data['category'][] 	= 	$value['category_id'];
				$data['min_price'][]	= 	$value['min_price'];
				$data['max_price'][]	=	$value['max_price'];
			}else{
				$data['category'] 	= 	array();
				$data['min_price'] 	=	array();
				$data['max_price']	=	array();
			}
		}

		$this->load->model('preferences/preferences');
		$data['master_preferences'] = $this->model_preferences_preferences->getMasterPreferences();

		//$data['categories'] = $this->model_sale_customer->getcategories();

		if (isset($this->request->get['customer_id'])) {
			$data['customer_id'] = $this->request->get['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		if (isset($this->error['country_code'])) {
			$data['error_country_code'] = $this->error['country_code'];
		} else {
			$data['error_country_code'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['gst_number'])) {
			$data['error_gst_number'] = $this->error['gst_number'];
		} else {
			$data['error_gst_number'] = '';
		}


		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}

		if (isset($this->request->get['filter_customer_type'])) {
			$url .= '&filter_customer_type=' . $this->request->get['filter_customer_type'];
		}

		if (isset($this->request->get['filter_gst_number'])) {
			$url .= '&filter_gst_number=' . $this->request->get['filter_gst_number'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_is_dropshipper'])) {
			$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
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
			'href' => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['customer_id'])) {
			$data['action'] = $this->url->link('sale/customer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$customer_info = $this->model_sale_customer->getCustomer($this->request->get['customer_id']);
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['country_code'])) {
			$data['country_code'] = $this->request->post['country_code'];
		} elseif (!empty($customer_info)) {
			$data['country_code'] = $customer_info['country_code'];
		} else {
			$data['country_code'] = '';
		}

       if (!empty($customer_info) && $customer_info['gst_number'] != '') {
			$data['gst_number'] = $customer_info['gst_number'];
			$data['gst_number_exist'] = 1;
		}
		elseif (isset($this->request->post['gst_number'])) {
			$data['gst_number'] = $this->request->post['gst_number'];
		}  else {
			$data['gst_number'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['is_dropshipper'])) {
			$data['is_dropshipper'] = $this->request->post['is_dropshipper'];
		} elseif (!empty($customer_info)) {
			$data['is_dropshipper'] = $customer_info['is_dropshipper'];
		} else {
			$data['is_dropshipper'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}
        
        // get franchise data
        $franchise      = new Franchise($this);
        $franchise_data = $franchise->getFranchiseData($this->request->get['customer_id']);

        if (isset($this->request->post['franchise_status'])) {
            $data['franchise_status'] = $this->request->post['franchise_status'];
        } elseif (!empty($franchise_data)) {
            $data['franchise_status'] = $franchise_data['franchise_status'];
        } else {
            $data['franchise_status'] = 0;
        }

        if (isset($this->request->post['franchise_coupon'])) {
            $data['franchise_coupon'] = $this->request->post['franchise_coupon'];
        } elseif (!empty($franchise_data)) {
            $data['franchise_coupon'] = $franchise_data['franchise_coupon'];
        } else {
            $data['franchise_coupon'] = "";
        }

        if (isset($this->request->post['franchise_discount'])) {
            $data['franchise_discount'] = $this->request->post['franchise_discount'];
        } elseif (!empty($franchise_data)) {
            $data['franchise_discount'] = $franchise_data['franchise_discount'];
        } else {
            $data['franchise_discount'] = "";
        }

        if (isset($this->request->post['franchise_prefix'])) {
            $data['franchise_prefix'] = $this->request->post['franchise_prefix'];
        } elseif (!empty($franchise_data)) {
            $data['franchise_prefix'] = $franchise_data['franchise_prefix'];
        } else {
            $data['franchise_prefix'] = "";
        }

        // Customer Bank details Start here
        if (isset($this->request->post['bank_ac_holder_name'])) {
			$data['bank_ac_holder_name'] = $this->request->post['bank_ac_holder_name'];
		} elseif (!empty($customer_info)) {
			$data['bank_ac_holder_name'] = $customer_info['bank_ac_holder_name'];
		} else {
			$data['bank_ac_holder_name'] = '';
		}

		if (isset($this->request->post['bank_ac_number'])) {
			$data['bank_ac_number'] = $this->request->post['bank_ac_number'];
		} elseif (!empty($customer_info)) {
			$data['bank_ac_number'] = $customer_info['bank_ac_number'];
		} else {
			$data['bank_ac_number'] = '';
		}

		if (isset($this->request->post['ifsc_code'])) {
			$data['ifsc_code'] = $this->request->post['ifsc_code'];
		} elseif (!empty($customer_info)) {
			$data['ifsc_code'] = $customer_info['ifsc_code'];
		} else {
			$data['ifsc_code'] = '';
		}
		// Customer Bank details End here

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['address'])) {
			$data['addresses'] = $this->request->post['address'];
		} elseif (isset($this->request->get['customer_id'])) {
			$data['addresses'] = $this->model_sale_customer->getAddresses($this->request->get['customer_id']);
		} else {
			$data['addresses'] = array();
		}

		if (isset($this->request->post['address_id'])) {
			$data['address_id'] = $this->request->post['address_id'];
		} elseif (!empty($customer_info)) {
			$data['address_id'] = $customer_info['address_id'];
		} else {
			$data['address_id'] = '';
		}
		$customer_id = $customer_info['customer_id'] ?? 0;
		if (isset($customer_info) && $customer_id > 0) {
			$this->load->model('report/customer');
        	$count 		= $this->model_report_customer->getLifeTimeOrdersCountOfCustomers(array($customer_id));
        	
        	$data['count_order'] = 0;

			if(isset($count[$customer_id]) && $count[$customer_id]['total_orders'] > 0){
				$data['count_order'] = $count[$customer_id]['total_orders'];
			}
			$data['total_order_link'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_customer_id=' . $customer_id, 'SSL');
		} else {
			$data['count_order'] = '';
			$data['total_order_link'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/customer_form.tpl', $data));
	}

	protected function validateForm() {
		$this->load->language('sale/customer');
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}
		
		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$customer_info = $this->model_sale_customer->getCustomerByEmail($this->request->post['email']);

		if (!isset($this->request->get['customer_id'])) {
			if ($customer_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}


		
		$customer_info = $this->model_sale_customer->getCustomerByMobile($this->request->post['telephone']);

		if (!isset($this->request->get['customer_id'])) {
			if ($customer_info) {
				$this->error['warning'] = $this->language->get('error_exists_mobile');
			}
		} else {
			if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
				$this->error['warning'] = $this->language->get('error_exists_mobile');
			}
		} 
		
		/*** GST Number check starts here ***/
		$this->request->post['gst_number'] = $this->request->post['gst_number'] ?? "";
		$this->request->post['old_gst_number'] = $this->request->post['old_gst_number'] ?? "";
		
		if( $this->request->post['gst_number'] != $this->request->post['old_gst_number'] ) { // don't validate if same value
			$customer = new Customer($this->registry);
			$customer_id = $this->request->get['customer_id'] ?? 0;
			$current_customer = array();
			$seller_duplicacy_check = true;
			if(empty($customer_id)) { // new customer
				$current_customer = array(
										'customer_id' => 0,
										'master_id' => 0,
										'telephone' => $this->request->post['telephone'],
										'email' => $this->request->post['email'],
										'firstname' => $this->request->post['firstname']
									);
			}
			$valid_gst_result = $customer->gstObject->validateGSTNumber($this->request->post['gst_number'], $customer_id, $seller_duplicacy_check, $current_customer);
			if ( !empty(trim($this->request->post['gst_number'])) && !($valid_gst_result['result'] === true) ) {
				if($valid_gst_result['message'] == "error_regex") {
					$this->error['gst_number'] = $this->language->get('error_gst_number');
				} elseif($valid_gst_result['message'] == "error_checksum") {
					$this->error['gst_number'] = sprintf($this->language->get('error_gst_checksum'),
					 									 $valid_gst_result['gst_number_details']['gst_number_without_checksum']."<b>".$valid_gst_result['gst_number_details']['gst_number_checksum']."</b>");
				} else if($valid_gst_result['message'] == "error_duplicate") {	
	                $user_str = '';
	                if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
						$duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
	                    if(!empty($duplicate_gst_number_customer['telephone'])) {
	                        $user_str = 'mobile number <strong>' .$duplicate_gst_number_customer['telephone']. '</strong>';
	                    } else if(!empty($duplicate_gst_number_customer['email'])){
	                        $len = strlen(explode('@',$duplicate_gst_number_customer['email'])[0]);
	                        $user_str = 'email <strong>' .$duplicate_gst_number_customer['email'].'</strong>';
	                    }
	                    $this->error['warning'] = sprintf($this->language->get('error_exists_gst'), $this->request->post['gst_number'], $user_str, '<strong>'.$duplicate_gst_number_customer['customer_id'].'</strong>' );
	                }
				}
			}
		}
        /*** GST Number validation Ends here ***/
        

		if ($this->request->post['password'] || (!isset($this->request->get['customer_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		if (isset($this->request->post['address'])) {
			foreach ($this->request->post['address'] as $key => $value) {
				if ((utf8_strlen($value['firstname']) < 1) || (utf8_strlen($value['firstname']) > 32)) {
					$this->error['address'][$key]['firstname'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen($value['address_1']) < 3) || (utf8_strlen($value['address_1']) > 128)) {
					$this->error['address'][$key]['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($value['city']) < 2) || (utf8_strlen($value['city']) > 128)) {
					$this->error['address'][$key]['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($value['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($value['postcode']) < 2 || utf8_strlen($value['postcode']) > 10)) {
					$this->error['address'][$key]['postcode'] = $this->language->get('error_postcode');
				}

				if ($value['country_id'] == '') {
					$this->error['address'][$key]['country'] = $this->language->get('error_country');
				}

				if (!isset($value['zone_id']) || $value['zone_id'] == '') {
					$this->error['address'][$key]['zone'] = $this->language->get('error_zone');
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateUnlock() {
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateHistory() {
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['comment']) || utf8_strlen($this->request->post['comment']) < 1) {
			$this->error['warning'] = $this->language->get('error_comment');
		}

		return !$this->error;
	}

	public function login() {
		$json = array();

		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->load->model('sale/customer');

		$customer_info = $this->model_sale_customer->getCustomer($customer_id);

		if ($customer_info) {
			$token = md5(mt_rand());

			$this->model_sale_customer->editToken($customer_id, $token);

			if (isset($this->request->get['store_id'])) {
				$store_id = $this->request->get['store_id'];
			} else {
				$store_id = 0;
			}

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($store_id);
            
            $login_page = 'login';	
			if($this->model_sale_customer->isCustomerSeller($customer_id))
			{
		      $login_page = 'seller_login';		
			}

			if ($store_info) {
				$this->response->redirect($store_info['url'] . 'index.php?route=common/login_popup/'.$login_page.'&token=' . $token);
			} else {
				$this->response->redirect(HTTP_CATALOG . 'index.php?route=common/login_popup/'.$login_page.'&token=' . $token);
			}
			
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_not_found'] = $this->language->get('text_not_found');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found.tpl', $data));
		}
	}

	public function history() {
		$this->load->language('sale/customer');

		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateHistory()) {
			$this->model_sale_customer->addHistory($this->request->get['customer_id'], $this->request->post['comment']);

			$data['success'] = $this->language->get('text_success');
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_comment'] = $this->language->get('column_comment');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_sale_customer->getHistories($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'comment'     => $result['comment'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_customer->getTotalHistories($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/customer/history', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('sale/customer_history.tpl', $data));
	}

	public function transaction() {
		$this->load->language('sale/customer');

		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) {
			$this->model_sale_customer->addTransaction($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['amount']);

			$data['success'] = $this->language->get('text_success');
		} else {
			$data['success'] = '';
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
			$data['error_warning'] = $this->language->get('error_permission');
		} else {
			$data['error_warning'] = '';
		}

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_balance'] = $this->language->get('text_balance');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_amount'] = $this->language->get('column_amount');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$results = $this->model_sale_customer->getTransactions($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->currency->format($this->model_sale_customer->getTransactionTotal($this->request->get['customer_id']), $this->config->get('config_currency'));

		$transaction_total = $this->model_sale_customer->getTotalTransactions($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/customer/transaction', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($transaction_total - 10)) ? $transaction_total : ((($page - 1) * 10) + 10), $transaction_total, ceil($transaction_total / 10));

		$this->response->setOutput($this->load->view('sale/customer_transaction.tpl', $data));
	}


    public function cashback() {
		$this->load->language('sale/customer');

		$this->load->model('sale/customer');

		$data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_cashback_total'] = $this->language->get('text_cashback_total');
        $data['text_cashback_utilized'] = $this->language->get('text_cashback_utilized');
        $data['text_cashback_expired'] = $this->language->get('text_cashback_expired');
        $data['text_cashback_available'] = $this->language->get('text_cashback_available');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_amount'] = $this->language->get('column_amount');
        $data['column_amount_utilized'] = $this->language->get('column_amount_utilized');
        $data['column_expired'] = $this->language->get('column_expired');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['token'] = $this->session->data['token'];
		$data['cashbacks'] = array();

		$results = $this->model_sale_customer->getCashbacks($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['cashbacks'][] = array(
				'csh_bck_id'  => $result['customer_cashback_id'],
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
                'amount_utilized' => $this->currency->format($result['amount_utilized'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'expired'     => $result['expired'] ? 'Expired' : 'Available'
			);
		}

		$data['cashback_total'] = $this->currency->format($this->model_sale_customer->getCashbackTotal($this->request->get['customer_id']),
                                                                                                       $this->config->get('config_currency'));

        $data['cashback_available'] = $this->currency->format($this->model_sale_customer->getCashbackAvailable($this->request->get['customer_id']),
                                                                                                               $this->config->get('config_currency'));

        $data['cashback_utilized'] = $this->currency->format($this->model_sale_customer->getCashbackUtilized($this->request->get['customer_id']),
                                                                                                             $this->config->get('config_currency'));

        $data['cashback_expired'] = $this->currency->format($this->model_sale_customer->getCashbackExpired($this->request->get['customer_id']),
                                                                                                           $this->config->get('config_currency'));

		$total_cashbacks = $this->model_sale_customer->getTotalCashbacks($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $total_cashbacks;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/customer/cashback',
                                            'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}',
                                            'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'),
                                   ($total_cashbacks) ? (($page - 1) * 10) + 1 : 0,
                                   ((($page - 1) * 10) > ($total_cashbacks - 10)) ? $total_cashbacks : ((($page - 1) * 10) + 10),
                                   $total_cashbacks, ceil($total_cashbacks / 10));

		$this->response->setOutput($this->load->view('sale/customer_cashback.tpl', $data));
	}


    public function cashbackUsage() {
		$this->load->language('sale/customer');

		$this->load->model('sale/customer');

		$data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_cashback_total'] = $this->language->get('text_cashback_total');
        $data['text_cashback_utilized'] = $this->language->get('text_cashback_utilized');
        $data['text_cashback_expired'] = $this->language->get('text_cashback_expired');
        $data['text_cashback_available'] = $this->language->get('text_cashback_available');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_amount'] = $this->language->get('column_amount');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['cashback_usage'] = array();

		$results = $this->model_sale_customer->getCashbackUsage($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['cashback_usage'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			);
		}

        $data['cashback_total'] = $this->currency->format($this->model_sale_customer->getCashbackTotal($this->request->get['customer_id']),
                                                                                                       $this->config->get('config_currency'));

        $data['cashback_available'] = $this->currency->format($this->model_sale_customer->getCashbackAvailable($this->request->get['customer_id']),
                                                                                                               $this->config->get('config_currency'));

        $data['cashback_utilized'] = $this->currency->format($this->model_sale_customer->getCashbackUtilized($this->request->get['customer_id']),
                                                                                                             $this->config->get('config_currency'));

        $data['cashback_expired'] = $this->currency->format($this->model_sale_customer->getCashbackExpired($this->request->get['customer_id']),
                                                                                                           $this->config->get('config_currency'));

		$total_cashback_usage = $this->model_sale_customer->getTotalCashbackUsage($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $total_cashback_usage;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/customer/cashbackUsage',
                                            'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}',
                                            'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'),
                                   ($total_cashback_usage) ? (($page - 1) * 10) + 1 : 0,
                                   ((($page - 1) * 10) > ($total_cashback_usage - 10)) ? $total_cashback_usage : ((($page - 1) * 10) + 10),
                                   $total_cashback_usage, ceil($total_cashback_usage / 10));

		$this->response->setOutput($this->load->view('sale/customer_cashback_usage.tpl', $data));
	}


	public function reward() {
		$this->load->language('sale/customer');

		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) {
			$this->model_sale_customer->addReward($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['points']);

			$data['success'] = $this->language->get('text_success');
		} else {
			$data['success'] = '';
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
			$data['error_warning'] = $this->language->get('error_permission');
		} else {
			$data['error_warning'] = '';
		}

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_balance'] = $this->language->get('text_balance');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_points'] = $this->language->get('column_points');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['rewards'] = array();

		$results = $this->model_sale_customer->getRewards($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['rewards'][] = array(
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->model_sale_customer->getRewardTotal($this->request->get['customer_id']);

		$reward_total = $this->model_sale_customer->getTotalRewards($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $reward_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('sale/customer/reward', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($reward_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($reward_total - 10)) ? $reward_total : ((($page - 1) * 10) + 10), $reward_total, ceil($reward_total / 10));

		$this->response->setOutput($this->load->view('sale/customer_reward.tpl', $data));
	}

	public function autocomplete() {
		$json = array();
        //echo "dfs"; die;
		if ( isset($this->request->get['filter_name']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_telephone']) || isset($this->request->get['filter_all'])) {

            if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

            if (isset($this->request->get['filter_telephone'])) {
				$filter_telephone = $this->request->get['filter_telephone'];
			} else {
				$filter_telephone = '';
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$filter_is_dropshipper = $this->request->get['filter_is_dropshipper'];
			} else {
				$filter_is_dropshipper = 0;
			}

			if( isset($this->request->get['filter_all']) ){
				$filter_name = $this->request->get['filter_all'];
				$filter_telephone = $this->request->get['filter_all'];
				$filter_email = $this->request->get['filter_all'];
			}

			$this->load->model('sale/customer');

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
                'filter_telephone' => $filter_telephone,
                'filter_is_dropshipper' => $filter_is_dropshipper,
				'start'        => 0,
				'limit'        => 50
			);
			if( isset($this->request->get['filter_all']) ) {
				$results = $this->model_sale_customer->getCustomersByNameMobileEmail($filter_data);
			}else{
				$results = $this->model_sale_customer->getCustomerList($filter_data);
			}
			foreach ($results as $result) {
				$json[] = array(
					'customer_id'       => $result['customer_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'is_dropshipper'	=> $result['is_dropshipper'],
					'telephone'         => $result['telephone'],
					'address'           => $this->model_sale_customer->getAddresses($result['customer_id'])
				);
			}
		}
      // echo "<pre>"; print_r($json[0]['address']); die;
		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function address() {
		$json = array();

		if (!empty($this->request->get['address_id'])) {
			$this->load->model('sale/customer');

			$json = $this->model_sale_customer->getAddress($this->request->get['address_id']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function filtersofproducts() {

		$json = array();
		if($this->request->get['category_id']){
			$category_id = $this->request->get['category_id'];
		}else{
			$category_id = '';
		}

		$this->load->model('sale/customer');
		$filter_groups = $this->model_sale_customer->getOptionsOfProducts($category_id);

	}


	public function customer_autocomplete(){
		$json = array();
		//echo "dfs"; die;
		if ( isset($this->request->get['filter_name']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_telephone'])) {

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['filter_telephone'])) {
				$filter_telephone = $this->request->get['filter_telephone'];
			} else {
				$filter_telephone = '';
			}

			$this->load->model('sale/customer');

			$filter_data = array(
					'filter_name'  => $filter_name,
					'filter_email' => $filter_email,
					'filter_telephone' => $filter_telephone,
					'start'        => 0,
					'limit'        => 50
			);

			$results = $this->model_sale_customer->getCustomerList($filter_data);
			foreach ($results as $result) {
				$json[] = array(
						'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
						'email'             => $result['email'],
						'telephone'         => $result['telephone'],
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	// expire cash back by vikas(30-07-2016)
	public function expireCashBack(){
		$this->load->language('sale/customer');

		if(isset($this->request->post['csh_bck_id'])){
			if (!$this->user->hasPermission('modify', 'sale/customer') && (!$this->user->hasPermission('modify', 'sale/order'))) {
				$json['error'] = $this->language->get('error_permission');
			} else {
				$csh_bck_id = $this->request->post['csh_bck_id'];

				$this->load->model('sale/customer');
				$this->model_sale_customer->expireCashBack($csh_bck_id);
			}
		}
	}

	/* Method to update Bank details of customer
	* @param: $data array
	* @return: null
	* @author: Nishu
	*/
	public function updateBankDetails(){
		$this->load->model('sale/customer');
		$this->model_sale_customer->updateBankDetails($this->request->post);
	}
	/**
	* download all duplicate customers
	* @author: Kalyan 27th sep, 2017
	*/
	public function downloadDuplicateCustomers(){
		$this->load->model('sale/customer');
		
		$customers 		= $this->model_sale_customer->getDuplicateCustomers();
		$finalCustomers = array();
		$mastersIds 	= array();
		
		if (!empty($customers) && count($customers)>0) {
			foreach ($customers as $key => $value) {
				$mastersIds[] = $value['master_id'];
			}
			foreach ($customers as $key => $value) {
				$finalCustomers[$value['customer_id']] = $value;
			}
		}

		$mastersIds = array_unique($mastersIds);
		
		if (!empty($mastersIds) && count($mastersIds)) {
			$masterCustomers = $this->model_sale_customer->getMasterCustomers($mastersIds);
		}

		if (!empty($masterCustomers) && count($masterCustomers)>0) {
			foreach ($masterCustomers as $key => $value) {
				$finalCustomers[$value['customer_id']] = $value;
			}
		}		
		if (!empty($finalCustomers) && count($finalCustomers)>0) {
			$this->downloadDuplicateCustomersCsv($finalCustomers);	
		}		
	}
	/**
	* download all duplicate customers
	* @author: Kalyan 27th sep, 2017
	*/
	public function downloadDuplicateCustomersCsv($csvData){
		
		$file_name 	= DIR_DOWNLOAD .'duplicate_customer_report_'.time().'.csv';
	    $fp 		= fopen($file_name, 'w');
	    
	    ob_clean();
	    
	    if(empty($csvData)){
	      $data = array("No Data Found.");
	      fputcsv($fp, $data);
	    }else{
	      $csvFileData = array();
	      /*
	      * heading values
	      */
	      $heading = array(	
	        'Customer Id',
	        'Master Id',
	        'Name',
	        'Email',
	        'Phone',
	        'Ledger Name'
	        );
	  	}
	  	$csvFileData[0] = $heading;
	  	$i=1;
	  	if (!empty($csvData) && count($csvData)>0) {
	  		foreach ($csvData as $key => $value) {
	  			$csvFileData[$i] = $value;
	  			$i++;
	  		}
	  	}	  	

	    foreach ($csvFileData as $line) {
          fputcsv($fp, $line);
        }
      	fclose($fp);

	    if (file_exists($file_name)) {
	      header('Content-Description: File Transfer');
	      header('Content-Type: application/octet-stream');
	      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	      header('Expires: 0');
	      header('Cache-Control: must-revalidate');
	      header('Pragma: public');
	      header('Content-Length: ' . filesize($file_name));
	      readfile($file_name);
	      exit();
	    }
	}

	/**
	 * Function : updateCustomerWebsiteStatusUsingCustomerStatus
	 * Request Parameters : request (customer_id, status)
	 * @author Rahul 11 July 2018
	 * Output : website customer detail ex. logo, name, contact
	 * */
	public function updateCustomerWebsiteStatusUsingCustomerStatus($request){
			$request['api_key']=CUSTOMER_WEBSITE_API_KEY;
			$data_json = json_encode($request);
                              $api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/update_website_status_by_customer";
                              $ch = curl_init($api_url);
                              curl_setopt($ch, CURLOPT_HEADER, 0);
                              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                  'Content-Length: ' . strlen($data_json))
                              );
                              curl_setopt($ch, CURLOPT_VERBOSE, 1);
                              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                              curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                              $result = curl_exec($ch);
                              $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                              $result = json_decode($result, true);
                              if ($result['statusCode'] == '200') {
                              			return true;
                              }else{
                              			return false;
                              }
                              exit;
	}

	/**
     * @info: Public method to save customer related NACH Details for WSB Credit
     * @author: Nishu, Feb 2019
	*/
	public function checkForDuplicateNachDetails(){
		$status = 'success';
		//Checks if data is set into post params
		if(!empty($this->request->post['customer_id'])){

			$customer_nach = new CustomerNachDetails($this->registry);
			$duplicate_data = $customer_nach->checkForDuplicateNachDetails($this->request->post);
			if(!empty($duplicate_data)){
				$status = 'Given NACH A/c details already exist for Customer Id: '.$duplicate_data['customer_id'];
			}
		}
		echo $status; exit();
	}

	/**
     * @info: Public method to save customer related NACH Details for WSB Credit
     * @author: Nishu, Feb 2019
	*/
	public function saveCustomerNachDetails(){
		$status = 'success';
		//Checks if data is set into post params
		if(!empty($this->request->post['customer_id'])){

			$customer_nach = new CustomerNachDetails($this->registry);
			$status = $customer_nach->insertNachDetails($this->request->post);
		}
		echo $status; exit();
	}

	/**
     * @info: Public method to update customer related NACH Details for WSB Credit
     * @author: Nishu, Feb 2019
	*/
	public function updateCustomerNachDetails(){
		$status = 'success';
		//Checks if data is set into post params
		if(!empty($this->request->post['id'])){

			$customer_nach = new CustomerNachDetails($this->registry);
			$status = $customer_nach->updateCustomerNachDetails($this->request->post);
		}
		echo $status; exit();
	}

	/**
     * @info: Public method to mark customer related NACH A/c as Default NACH A/c For deduction
     * @author: Nishu, Feb 2019
	*/
	public function markNachDetailAsDefault(){
		
		//Checks if data is set into post params
		if(!empty($this->request->post['id'])){

			$customer_nach = new CustomerNachDetails($this->registry);
			$customer_nach->markNachDetailAsDefault($this->request->post);
		}
		echo 'success'; exit();
	}

	/**
     * @info: Public method to change availability 
     			from available to unavailable 
     			and unavailable to available customer related NACH A/c
     * @author: Nishu, April 2019
	*/
	public function changeAvailabilityNachDetail(){
		
		//Checks if data is set into post params
		if(!empty($this->request->post['id'])){

			$customer_nach = new CustomerNachDetails($this->registry);
			$customer_nach->changeAvailabilityNachDetail($this->request->post);
		}
		echo 'success'; exit();
	}

	/**
     * @info: Public method to Move NACH account details from One customer a/c to another customer a/c 
     * @author: Nishu, Aug 2019
	*/
	public function moveNachAcDetail(){
		
		//Checks if data is set into post params
		if(!empty($this->request->post)){
			$data = $this->request->post;

			$from_customer_id = (int)($data['from_customer_id'] ?? 0);
			$to_customer_id   = (int)($data['to_customer_id'] ?? 0);
			if($from_customer_id == $to_customer_id){
				echo 'From Customer Id and To Customer Id can not be same!!'; exit();
			}

			$customer_nach = new CustomerNachDetails($this->registry);
			$resp = $customer_nach->moveNachAcDetail($data);
			if(!empty($resp)){
				echo $resp; exit();
			}
		}

		echo 'success'; exit();
	}

	/**
     * @info: Public method to get crontab string's meaning as natural language
     * @author: Nishu, Feb 2019
	*/
	public function getCrontabStringMeaning(){
		$data = '';
		//Checks if data is set into post params
		if(!empty($this->request->post['crontab'])){
			$crontab = $this->request->post['crontab'];
			$schedule = CronSchedule::fromCronString($crontab);
			$data = $schedule->asNaturalLanguage();
		}
		echo $data; exit();
	}

    /**
     * Get Short SMS log
     * @param customer_id
     * @author Manoj Singh Rajpurohit, Feb 2019
     */
    public function readShortSmsLog(){

        $result = '';

        if(!empty($this->request->get['customer_id']) && !isset($this->request->get['dwnld'])){

            if(!empty($this->request->get['page']))
            {
                $page = $this->request->get['page'];
            }
            else
            {
                $page = 0;
            }

            $limit = 2000;
            $start = (($limit/2)*$page);

            if($page == 0)
            {
                $this->load->model('sale/customer_credit_application');
                $bank_detail = $this->model_sale_customer_credit_application->getCustomerBankDetail($this->request->get['customer_id']);
                if($bank_detail){
                    $result .= '<div class="bank_details_data"> 
					<table id="notesTable" style="width:100%"><tr><td colspan="3"><strong>Bank Details:</strong></td></tr>';
                    if(!empty($bank_detail['bank_ac_number'])){
                        $result .= '<tr>';
                        $result .= '<td>'. $bank_detail['bank_ac_holder_name'].'</td>';
                        $result .= '<td>'. $bank_detail['bank_ac_number'].'</td>';
                        $result .= '<td>'. $bank_detail['ifsc_code'].'</td>';
                        $result .= '</tr>';
                        $result .= '</table></div> <br />';
                    } else {

                        $result .= '<tr>';
                        $result .= '<td colspan="3"><span style="color:RED;">Record not found</span></td>';
                        $result .= '</tr>';
                        $result .= '</table></div> <br />';
                    }

                }
            }

            $this->load->model('sale/customer');
            $response = $this->model_sale_customer->getAllShortSmsLog($this->request->get['customer_id'],$start, $limit);

            if(!empty($response))
            {
                if($page == 0)
                {
                    $_SESSION['sms_sr_no'] = 1;

                    $result .= '<div class="short_sms_data"> <table id="shortSmsTable" style="width:100%">
					<tbody><tr>
						<th>(#)</th>
						<th>Message</th>
						<th>Sender Name</th>
						<th>Msg Receive Date</th>
						<th>Created Date</th> </tr>';
                }

                foreach($response as $short_sms)
                {
                    $result .= '<tr>';
                    $result .= '<td style="width:2%">'.$_SESSION['sms_sr_no']++.'</td>';
                    $result .= '<td style="width:50%">'.$short_sms['message'].'</td>';
                    $result .= '<td>'.$short_sms['sender_name'].'</td>';
                    $result .= '<td style="width:10%">'.date('d-m-Y', $short_sms['timestamp']/1000).'</td>';
                    $result .= '<td style="width:10%">'.date('d-m-Y',strtotime($short_sms['created'])).'</td>';
                    $result .= '</tr>';
                }

                if($page == 0)
                {
                    $result .= '</tbody></table>';
                }

                if($page == 0 && count($response) >= ($limit/2))
                {
                    $result .= '<br />';
                    $result .= '<div class="more_div"><a class="more_short_sms_btn" data-customer_id="'.$this->request->get['customer_id'].'" >Show More</a>';
                    $result .= '</div>';
                }

                if($page == 0)
                {
                    $result .= '</div>';
                }



            } else {

                $result  = "<span style='color:RED;'>No Record Found!!!</span>";
            }
        }

        // Download CSV on mail
        if(isset($this->request->get['dwnld']) && $this->request->get['dwnld'] == "yup"){

            // get SMS data
            $this->load->model('sale/customer');
            $response = $this->model_sale_customer->getAllShortSmsLog($this->request->get['customer_id']);

            // get customer Info
            $sql_customer = "SELECT c.telephone, CONCAT( c.firstname, ' ', c.lastname ) as `name` , a.company 
                FROM `oc_customer` c
                LEFT JOIN oc_address a ON a.address_id = c.address_id
                WHERE c.customer_id IN (".(int)$this->request->get['customer_id'].") LIMIT 1";
            $customer_info = $this->db->query($sql_customer)->row;


            $today = Date('d_M_y');
            $filename = "short_message_".time().".csv";
            $dir_path =  DIR_SYSTEM . 'upload/assets/short_message';
            if (!is_dir($dir_path)) {
                mkdir($dir_path, 0777, true);
            }
            $i = 1;
            $full_path = $dir_path . '/'.$filename;
            $handle = fopen($full_path, 'w');

            $i = 1;
            foreach ($response as $result) {

                $customer_id = $result['customer_id'];
                $name = $customer_info['name']??'';
                $message = $result['message'];
                $created = $result['created'];
                $sender_name = $result['sender_name'];
                $message_date = date('d-m-Y', $result['timestamp']/1000);
                $business = $customer_info['company']??'';
                $mobile = $customer_info['telephone']??'';


                if ($i == 1) {
                    $i++;
                    $data = array('Name',
                        'Business Name',
                        'Mobile',
                        'Sender Name',
                        'Message',
                        'Msg Receive date',
                        'Created date'
                    );


                    fputcsv($handle,$data);
                    $data = array($name, $business, $mobile, $sender_name, $message, $message_date, $created);
                    fputcsv($handle,$data);

                } else {
                    $i++;
                    $data = array($name, $business, $mobile, $sender_name, $message, $message_date, $created);
                    fputcsv($handle,$data);
                }

            }

            fclose($handle);
            sleep(5);
            if (file_exists($full_path)) {
                $csv_path = $full_path;

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'html';

                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
                $mail->addReplyTo($this->config->get('config_email'), 'WholesaleBox');
                $mail->Subject = 'Short Message data - '.$customer_info['telephone'];
                $mail->Body = 'Please find the attached CSV of Short Message data - '.$customer_info['telephone'];
                $mail->AddAttachment($csv_path);
                //send to admin;
                $mail->addAddress($this->user->getUserName()['email'], $this->user->getUserName()['name']);
                $mail_response = $mail->send();

                if($mail_response) { $result = "<div><i class='fa fa-send' style='font-size:20px;color:GREEN;'> Mail Sent</i></div>"; } else { $result = "<div style='color:RED;'>Failed. Try Again</div>"; }

                sleep(2);
                unlink($csv_path);

            } else { $result = "<div style='color:RED;'>Failed. Try Again</div>"; }

        }

        echo $result;
        exit;
    }

    public function bankDetail() {

    	$this->load->language('sale/customer');
    	$this->load->model('account/customer', 'frontend');
    	$this->load->model('sale/customer');
    	$data['error_warning'] = '';

    	if(empty($this->request->get['customer_id'])){

    		$this->response->redirect($this->url->link('sale/customer/index', 'token=' . $this->session->data['token'], 'SSL'));
    	}else{


		$this->document->setTitle($this->language->get('tab_bank_details'));
		$data['heading_title'] = $this->language->get('tab_bank_details');

		$this->load->model('sale/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->request->post['customer_id'] = $this->request->get['customer_id'];
			$result  =$this->bankValidate();
            if(isset($result) && !empty($result)){
                $data['error'] = $result;
            }else{
            	$request_data['customer_id']=$this->request->get['customer_id'];
            	$request_data['ac_holder']=$this->request->post['bank_ac_holder_name'];
            	$request_data['ac_no']=$this->request->post['bank_ac_number'];
            	$request_data['ifsc_code']=$this->request->post['ifsc_code'];
    			$this->model_sale_customer->updateBankDetails($request_data);
                $data['success'] = $this->language->get('bank_text_success');

            }


		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$general_url='';
		$url = '';
		if (isset($this->request->get['customer_id'])) {
				$url .= '&customer_id=' . $this->request->get['customer_id'];

				
        		$fields = array("customer_id","bank_ac_holder_name","bank_ac_number","ifsc_code");
    			$customerCustomData=$this->frontend_model_account_customer->getCustomerDetails($this->request->get['customer_id'], $fields);
    			//$data=$customerCustomData
    			    $data['customer_id'] = $customerCustomData['customer_id'];
					$data['bank_ac_holder_name'] = $customerCustomData['bank_ac_holder_name'];
					$data['bank_ac_number'] = $customerCustomData['bank_ac_number'];
					$data['ifsc_code'] = $customerCustomData['ifsc_code'];
			}
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/customer/bankDetail', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['action'] = $this->url->link('sale/customer/bankDetail', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		
		$data['btn_update_bank_detail'] = $this->language->get('btn_update_bank_detail');
		$data['button_cancel'] = $this->language->get('bank_back');

		
		$data['cancel'] = $this->url->link('sale/customer/index', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['text_form'] = $this->language->get('text_bank_detail_edit');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['entry_bank_ac_holder_name'] = $this->language->get('entry_bank_ac_holder_name');
		$data['entry_bank_ac_number'] = $this->language->get('entry_bank_ac_number');
		$data['entry_ifsc_code'] = $this->language->get('entry_ifsc_code');

		$this->response->setOutput($this->load->view('sale/bank_detail.tpl', $data));
	}
	}
	private function bankValidate() {
		$this->load->model('account/customer', 'frontend');
        $error = array();
		if ((utf8_strlen(trim($this->request->post['bank_ac_holder_name'])) < 1) || (utf8_strlen(trim($this->request->post['bank_ac_holder_name'])) > 32)) {
			$error['error_account_holder_name'] = $this->language->get('error_account_holder_name');
		}

		if ((utf8_strlen(trim($this->request->post['bank_ac_number'])) < 1) || (utf8_strlen(trim($this->request->post['bank_ac_number'])) > 32)) {
			$error['error_account_number'] = $this->language->get('error_account_number');
		}

        if ((utf8_strlen(trim($this->request->post['ifsc_code'])) < 1) || (utf8_strlen(trim($this->request->post['ifsc_code'])) > 32)) {
			$error['error_ifsc_code'] = $this->language->get('error_ifsc_code');
		}

		// if ((utf8_strlen(trim($this->request->post['customer_vpa'])) > 0)) {
		// 	$upi_vpa = trim($this->request->post['customer_vpa']);
		// 	(int) $length = utf8_strlen($upi_vpa);
  //       	if((int) $length > 0){
  //       		$pos = strpos($upi_vpa, '@');
	 //        	$pos_next = strpos($upi_vpa, '@', $pos+1);
	 //        	if($pos == 0 || $pos == ($length-1) || $pos === FALSE || $pos_next > 0){
	 //        		$error['error_customer_vpa'] = $this->language->get('error_customer_vpa');
	 //        	}
  //       	}
		// }
		if(empty($error['error_account_number']) && empty($error['error_ifsc_code'])){
			$check_if_bank_account_exist=$this->frontend_model_account_customer->checkIfBankAccountExists($this->request->post);
			if(empty($check_if_bank_account_exist)){
				$error['error_account_number'] = $this->language->get('error_account_number_exist');
			}

		}

		if(empty($error['error_account_number']) && empty($error['error_ifsc_code'])){
			$check_if_bank_account_block=$this->frontend_model_account_customer->checkIfBankAccountIsBlock($this->request->post);
			if(!empty($check_if_bank_account_block)){
				$error['error_account_number'] = $this->language->get('error_account_number_block');
			}

		}

		return $error;
	}

	/**
     * @info: Public method to check Last NACH schedule date and total days(including all bank holidays etc) 
     *         to recover complete amount according to customer's config data 
     *         Like: crontab string, Days After NACH Starts and Total Days to complete NACH 
     * @param : post data 
     * @return: string resp
     * @author: Nishu, June 2019
	*/
	public function checkRecoveryDaysForNachRules(){
		$resp = 'CrontTab String is missing.';

		//Checks if data is set into post params
		if(!empty($this->request->post['crontab'])){

			$data = array();
			$data['delivered_date']         = date('Y-m-d'); //Consider delivered_date is current date
			$data['nach_schedule_crontab']  = $this->request->post['crontab'];
			$data['days_before_nach_start'] = $this->request->post['days_before_nach_start'];
			$data['schedule_days_for_nach'] = $this->request->post['schedule_days_for_nach'];

			//Create object of NachBehaviour to get recovery days for NACH rules
			$nach_obj = new NachBehaviour($this->registry);
			
			//Returns array of dates
			$results  = $nach_obj->getNextComingCronDates($data);
			
			//Last date for NACH schedule(s)
			$last_nach_debit_date = (array_values(array_slice($results, -1))[0]);
			//$last_nach_debit_date = date('Y-m-d', strtotime($last_nach_debit_date));

			//Total days for NACH schedules
			$days_to_recover = round((strtotime($last_nach_debit_date) - strtotime($data['delivered_date']))/(24*3600));

			$resp = "If delivery date is: " . $data['delivered_date'] . ", then as per current rules, NACH recovery will be finished by: " . $last_nach_debit_date . ". Total number of days (incl. holidays) to recover: " . $days_to_recover;
		}

		echo $resp; exit();
    }
    
    public function getSMSDetails(){
        $customer_id = $this->request->get['customer_id'] ?? 0;
        if (empty($customer_id)) {
            return "Error: Customer Id is incorrect";
        }

        $this->load->model('sale/customer');

        $customer = $this->model_sale_customer->getCustomerById($customer_id, array('firstname', 'lastname', 'telephone'));
        if (empty($customer)) {
            return "Error: Customer data not found";
        }

        $data = array();

        $this->load->model('sale/customer_credit_application');

        $criteria_wise_sms_logs = $this->model_sale_customer_credit_application->checkTextMsgLogCriteriaWiseByCustomerId($customer_id);

        $sms_data = array(
            'have_sms_pos_log' => $criteria_wise_sms_logs['pos'] ?? 0,
            'have_sms_credit_log' => $criteria_wise_sms_logs['udaan'] ?? 0,
            'have_sms_bounce_log' => $criteria_wise_sms_logs['bounce'] ?? 0,
            'have_sms_gst_log' => $criteria_wise_sms_logs['gst'] ?? 0,
            'have_sms_bank_log' => $criteria_wise_sms_logs['account'] ?? 0,
            'have_sms_paytm_log' => $criteria_wise_sms_logs['paytm'] ?? 0,
            'have_sms_lazypay_log' => $criteria_wise_sms_logs['lazypay'] ?? 0,
            'has_sms_loan_log' => $criteria_wise_sms_logs['loan'] ?? 0,
            'has_sms_mswipe_log' => $criteria_wise_sms_logs['pos'] ?? 0,
            'has_customer_short_sms' => $this->model_sale_customer->checkExistCustomerShortSMS($customer_id)
        );

        $data['sms_data'] = $sms_data;
        $data['customer'] = array(
            'customer_id' => $customer_id,
            'name' => $customer['firstname'] . " " . $customer['lastname'],
            'telephone' => $customer['telephone']
        );
        $data['token'] = $this->session->data['token'];

        $this->response->setOutput($this->load->view('sale/sms_details.tpl', $data));
    }

}
