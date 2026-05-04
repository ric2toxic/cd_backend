<?php
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA; 

class ControllerSaleCustomerCreditApplication extends Controller {
	private $error = array();

	public function index() {
          
        $this->load->model('sale/customer_credit_application');
        $this->load->model('sale/customer');
        $this->load->model('user/user');
        $this->load->model('localisation/zone');
		$this->getCustomerCreditApplicationList();
    }

    /** Kusum Joshi
	* Get all Customer Credit Applications based on filters
	*
    **/
	public function getCustomerCreditApplicationList() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/customer_credit_application', $data);

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_step'])) {
			$filter_step = $this->request->get['filter_step'];
		} else {
			$filter_step = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_pancard']) && !empty($this->request->get['filter_pancard'])) {
			$filter_pancard = $this->request->get['filter_pancard'];
		} else {
			$filter_pancard = null;
		} 
		if (isset($this->request->get['filter_document_type']) && !empty($this->request->get['filter_document_type'])) {
			$filter_document_type = $this->request->get['filter_document_type'];
		} else {
			$filter_document_type = null;
		}

        if (isset($this->request->get['filter_sms_log_type']) && !empty($this->request->get['filter_sms_log_type'])) {
            $filter_sms_log_type = $this->request->get['filter_sms_log_type'];
        } else {
            $filter_sms_log_type = null;
        }

		if (isset($this->request->get['filter_document_status']) && !empty($this->request->get['filter_document_status'])) {
			$filter_document_status = $this->request->get['filter_document_status'];
		} else {
			$filter_document_status = null;
		}  
                if (isset($this->request->get['filter_application_id']) && !empty(trim($this->request->get['filter_application_id']))) {
			$filter_application_id = trim($this->request->get['filter_application_id']);
		} else {
			$filter_application_id = null;
		}
                
                if (isset($this->request->get['filter_telephone'])) {
			$filter_telephone = $this->request->get['filter_telephone'];
		} else {
			$filter_telephone = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
		 $filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
		 $filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['filter_followup_date'])) {
		 $filter_followup_date = $this->request->get['filter_followup_date'];
		} else {
			$filter_followup_date = null;
		}

		if (isset($this->request->get['filter_rbl_approved'])) {
		 $filter_rbl_approved = $this->request->get['filter_rbl_approved'];
		} else {
			$filter_rbl_approved = null;
		}

		if (isset($this->request->get['filter_discrepency_status'])) {
		 $filter_discrepency_status = $this->request->get['filter_discrepency_status'];
		} else {
			$filter_discrepency_status = null;
		}

		if (isset($this->request->get['filter_customer_id'])) {
		 $filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
        }
        
        if (isset($this->request->get['filter_city'])) {
			$filter_city = $this->request->get['filter_city'];
		} else {
			$filter_city = null;
        }
        
        if (isset($this->request->get['filter_state'])) {
			$filter_state = $this->request->get['filter_state'];
		} else {
			$filter_state = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'capp.created_date';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		if (isset($this->request->get['filter_by_crif']) && $this->request->get['filter_by_crif']=='1') {
			$filter_by_crif = $this->request->get['filter_by_crif'];
		} else {
			$filter_by_crif = 0;
		}
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_step'])) {
			$url .= '&filter_step=' . urlencode(html_entity_decode($this->request->get['filter_step'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['filter_followup_date'])) {
			$url .= '&filter_followup_date=' . $this->request->get['filter_followup_date'];
		}

        if (isset($this->request->get['filter_pancard'])) {
			$url .= '&filter_pancard=' . urlencode(html_entity_decode($this->request->get['filter_pancard'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_document_type'])) {
			$url .= '&filter_document_type=' . urlencode(html_entity_decode($this->request->get['filter_document_type'], ENT_QUOTES, 'UTF-8'));
		}

        if (isset($this->request->get['filter_sms_log_type'])) {
            $url .= '&filter_sms_log_type=' . urlencode(html_entity_decode($this->request->get['filter_sms_log_type'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_document_status'])) {
			$url .= '&filter_document_status=' . urlencode(html_entity_decode($this->request->get['filter_document_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_rbl_approved'])) {
			$url .= '&filter_rbl_approved=' . urlencode(html_entity_decode($this->request->get['filter_rbl_approved'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_discrepency_status'])) {
			$url .= '&filter_discrepency_status=' . urlencode(html_entity_decode($this->request->get['filter_discrepency_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . urlencode(html_entity_decode($this->request->get['filter_customer_id'], ENT_QUOTES, 'UTF-8'));
        }
        
        if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
        }
        
        if (isset($this->request->get['filter_state'])) {
			$url .= '&filter_state=' . urlencode(html_entity_decode($this->request->get['filter_state'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_by_crif']) && $this->request->get['filter_by_crif']=='1') {
			$url .= '&filter_by_crif=' .$this->request->get['filter_by_crif'];
		}

		$sort_url = $url;

		if ($order == 'ASC') {
			$sort_url .= '&order=DESC';
		} else {
			$sort_url .= '&order=ASC';
		}
		$data['sort_last_modified'] = $this->url->link('sale/customer_credit_application', 'token=' . $this->session->data['token'] . '&sort=capp.last_modified' . $sort_url, 'SSL');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/customer_credit_application', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['customers'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_step'              => $filter_step,
			'filter_email'             => $filter_email,
            'filter_application_id'    => $filter_application_id,
            'filter_telephone'         => $filter_telephone,
            'filter_date_added'        => $filter_date_added,
            'filter_date_modified'     => $filter_date_modified,
            'filter_followup_date'    => $filter_followup_date,
            'filter_pancard'           => $filter_pancard,
            'filter_document_type'     => $filter_document_type,
            'filter_sms_log_type'     => $filter_sms_log_type,
            'filter_document_status'   => $filter_document_status,
            'filter_rbl_approved'      => $filter_rbl_approved,
            'filter_discrepency_status'=> $filter_discrepency_status,
            'filter_customer_id'        => $filter_customer_id,
            'filter_by_crif'			=>$filter_by_crif,
            'filter_city'               => $filter_city,
            'filter_state'               => $filter_state,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$credit_partners_list = $this->model_sale_customer_credit_application->getCreditPartnersList();

		$credit_app_total = $this->model_sale_customer_credit_application->getTotalCustomerCreditApplication($filter_data);
		$results = $this->model_sale_customer_credit_application->getCustomerCreditApplication($filter_data);

        $customer_ids = array_column($results,'customer_id');  
        
        /**
         * fetch Images List From Crm for Credit Application Customer
         */
        $customer_shop_images = $this->fetchCustomerShopImagesAndContactsFromCrm($customer_ids);
     
        // Getting Sales (CRM) Agent names for these customers
        $this->load->model('lead/lead', 'frontend');
        $agent_names = $this->frontend_model_lead_lead->getCustomerAgentName($customer_ids);

        // Getting Sales (CRM) Agent ids for these customers
        $agent_ids = $this->frontend_model_lead_lead->getCustomerUserId($customer_ids);
        
        // Finding all the related id(s) and its duplicate/master marking status
        $duplicate_master_ids = $this->model_sale_customer->getAllMasterDuplicateCustomerIds($customer_ids);
    	
        // all customer id of master id
        $all_customer_ids = array_column($duplicate_master_ids, 'all_related_ids');


        //Check WSB_credit for the customers is enabled or not
        $wsb_credit = new WsbCreditPayment($this);

		foreach ($results as $result) {
            
            $all_related_ids = '';
            if(isset($duplicate_master_ids[$result['customer_id']]['all_related_ids']))
            {
            	 $all_related_ids = $duplicate_master_ids[$result['customer_id']]['all_related_ids'];
                 $all_related_ids_array = explode(',',$all_related_ids);
            }

           $result['master_id'] = $duplicate_master_ids[$result['customer_id']]['master_id'] ?? '';
           $result['filter_by_master_id_url'] = $this->url->link('sale/customer_credit_application', 'token=' . $this->session->data['token'] . '&filter_customer_id=' . $result['master_id'] . $sort_url, 'SSL');

           $tl_name = '';
           if(!empty($agent_ids[$result['customer_id']]))
           {
             $tl_names = $this->frontend_model_lead_lead->getCrmTeamLeadEmailId($agent_ids[$result['customer_id']]);
              if($tl_names->num_rows)
              {
           	    $tl_name = $tl_names->row['name'] ?? '';
               }
            }


            //23rd August 2019 Devendra, commenting below code
            //If customer has not submitted email in credit application then show it empty instead of showing customer's original email id Because it creates confusion.
			// if(empty($result['email'])){
	       	// $this->load->model('account/customer', 'frontend');
	        //  $userEmail=$this->frontend_model_account_customer->getCustomerDetails($result['customer_id'], $fields = array('email'));
	        //  $result['email']=$userEmail['email'];
	    	// }
            $this->load->model('account/credit_application', 'frontend');
            $get_all_document=$this->frontend_model_account_credit_application->getCustomerCreditApplicationDocumentByFormId($result['id']);
            
            $lead_contacts = $customer_shop_images[$result['customer_id']]['contact'] ?? array();
            if (!empty($lead_contacts)) {
                $contact_key = array_search($result['phone_no'], $lead_contacts);
                if (false !== $contact_key) {
                    unset($lead_contacts[$contact_key]);
                }
            }
            		  $result['shop_distance']='';
                      $result['home_distance']='';
                      $result['location_count']='0';
                      $result['customer_email_count']='0';
            if(!empty($result['customer_id'])){
                    $location_distance = $this->getDistanceBetweenTwoLocation($result['customer_id'],$result['current_address'],$result['permanent_address']);
                      $result['shop_distance']=$location_distance['shop_distance']??'';
                      $result['home_distance']=$location_distance['home_distance']??''; 
                      $location_count_response=$this->frontend_model_account_credit_application->getCustomerLocationCount($result['customer_id']);
                      $result['location_count']=$location_count_response['count'];

                //=======check customer email save or not==========//
                $result['customer_email_count']=$this->frontend_model_account_credit_application->getCustomerSavedEmailCount($result['customer_id']);
             }
            $data['customers'][] = array(
				 'customer_credit_application_id' => $result['id'],
                 'customer_id'		 => $result['customer_id'],
                 'master_id'		 => $result['master_id'],
                 'filter_by_master_id_url'		 => $result['filter_by_master_id_url'],
				 'rbl_credit_status'		=> $this->model_sale_customer_credit_application->getCustomerApplicationStatus($result['customer_id']),
				 'rbl_status'		=> $this->model_sale_customer_credit_application->getCustomerApplicationRBLStatus($result['customer_id']),
				 'customer_id_with_prefix' => $result['customer_id'],
				 'name'              => $result['name'] ,
				 'email'             => $result['email'] ,
				 'telephone'         => $result['phone_no'] ,
				 'current_city'      => $result['current_city'] ,
				 'current_state'     => $result['current_state'] ,
				 'current_pincode'     => $result['current_pincode'] ,
                 'company_name'      => $result['company_name'],
				 'gst_number'        => $result['gst_number'] ,
				 'crif_score'        => $result['crif_score'] ,
				 'date_added'		 => date($data['date_format_short'], strtotime($result['date_added'])),
				 'step'				 => $result['draft'] ,
				 'applicationId'	 => $result['application_id'] ,
				 'applicantId'		 => $result['applicant_id'],
				 'epaylater_otp_flag'=> $result['epaylater_otp_flag'],
				 'epaylater_kyc_otp_flag'=> $result['epaylater_kyc_otp_flag'],
				 'redirect_url'		 => $result['redirect_url'],
				 'last_note'		 => $this->model_sale_customer_credit_application->getLastNote($result['id']),
				 'credit_limit'		 => $this->model_sale_customer_credit_application->getCreditLimit($result['id']),
				 'fi_verification'		=> $this->model_sale_customer_credit_application->getFiAvailability($result['permanent_city'], $result['permanent_pincode'], $result['current_city'], $result['current_pincode']),
				  'gst_verification'	=> $this->model_sale_customer_credit_application->getGstVerificationCheck($result['id']),
				  'fi_document' => $this->model_sale_customer_credit_application->getFiDocument($result['id']),
				 'shared_with'		 => $result['shared_with'],
				 'kyc_done'			 => $result['kyc_done'],
				 'version'			 => $result['version'],
				 'get_all_document'	 => $get_all_document,
				 'document_status'	 => $result['document_status'],
				 'last_modified'	 => $result['last_modified'],
				 'credit_preapproved_id' => $result['credit_preapproved_id'],
				 'cif_creation_id'   => $result['cif_creation_id'],
				 'cif_creation_status' => $result['cif_creation_status'],
				 'home_distance'	 => $result['home_distance'],
				 'shop_distance'	 => $result['shop_distance'],
				 'location_count'	 => $result['location_count'],
				 'edit_link'		 => $this->url->link('sale/customer_credit_application/edit', 'customer_id='.$result['customer_id'].'&token=' . $this->session->data['token'] , 'SSL'),
				 'user_link'		 => $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'].'&customer_id='.$result['customer_id'], 'SSL'),
				'agent_name'     => isset($agent_names[$result['customer_id']]) ? $agent_names[$result['customer_id']] : 0 ,
                'tl_name'  => $tl_name,
                'lead_images' => $customer_shop_images[$result['customer_id']]['images']??array(),
                'lead_contacts' => $lead_contacts,
                'bank_details' => $this->model_sale_customer_credit_application->getCustomerBankDetail($result['customer_id']),
                'credit_tab'   => $this->url->link('sale/customer_credits', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL'),
                'cif_creation_form_data'  => $this->getCustomerCifCreationData($result['id'],$result['customer_id']),
                'has_active_wsb_credit'=> $this->model_sale_customer_credit_application->wsbCreditStatus($result['customer_id']),
                'customer_email_count' => $result['customer_email_count']

			);

			// if(!empty($result['customer_id']) && !empty($result['master_id']) && $result['customer_id']!=$result['master_id']){


			// }
		}
		
		$data['khufiya_user_id'] = $this->session->data['user_id'];

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

	  $data['checklist_data'] = $this->model_sale_customer_credit_application->getCreditChecklist();
	  $data['questions_data'] = $this->model_sale_customer_credit_application->getCreditQuestions();

      $this->load->model('module/credit_application', 'frontend');
      $data['all_states'] = $this->frontend_model_module_credit_application->getState();

		$pagination = new Pagination();
		$pagination->total = $credit_app_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/customer_credit_application/index', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$data['pagination'] = $pagination->render();


		$data['results'] = sprintf($data['text_pagination'], ($credit_app_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($credit_app_total - $this->config->get('config_limit_admin'))) ? $credit_app_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $credit_app_total, ceil($credit_app_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_name'] = $filter_name;
        $data['filter_application_id'] = $filter_application_id;
		$data['filter_step'] = $filter_step;
		$data['filter_email'] = $filter_email;
		$data['filter_pancard'] = $filter_pancard;
		$data['filter_document_type'] = $filter_document_type;
        $data['filter_sms_log_type'] = $filter_sms_log_type;
        $data['filter_document_status'] = $filter_document_status;
        $data['filter_rbl_approved'] = $filter_rbl_approved;
        $data['filter_discrepency_status'] = $filter_discrepency_status;
        $data['filter_telephone'] = $filter_telephone;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;
        $data['filter_followup_date'] = $filter_followup_date;
        $data['text_no_results'] = $this->language->get('text_no_results');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['token'] =$this->session->data['token'];
        $data['filter_customer_id'] =$filter_customer_id;
        $data['filter_city'] = $filter_city;
        $data['filter_state'] = $filter_state;

        //reasons data
        $data['approved_but_agreement_pending_status'] = array('Wsb Credit', 'SRNG', 'RBL');
        $data['approved_without_bank_statement'] = $data['approved_but_agreement_pending_status'];
        $data['customer_not_interested_status'] = array('Filled by mistake','Expacted higher limit', 'Pospond for the timebeing', 'Others');
        $data['rejected_status'] = array('Low average balance','Low credit transactions', 'OD/CC', 'Existing high exposer', 'Low CIBIL score', 'Cash collection not available', 'Non coverage location', 'Negative profile', 'Not dealing in wsb business segment');
        $data['document_awaited_status'] = array();
        $data['under_process_status'] = array();
        $data['approved_document_received'] = array();
        $data['activated_status'] = array();
        $data['approved_but_not_interested'] = array();
	        $data['need_more_information'] = array();
	        $data['filter_by_crif'] = $filter_by_crif;

        $data['lead_url'] = $this->url->link('sale/customer_credit_application/today_dialer_list', 'token=' .$this->session->data['token'], 'SSL');

        $table=DB_PREFIX."credit_application_status_remarks";
        $field='payment_cycle';
        $payment_cycle_options = $this->db->getEnumValues($table, $field);
		$data['payment_cycle_options']=$payment_cycle_options;
		$this->response->setOutput($this->load->view('sale/customer_credit_application_list.tpl', $data));
	}


	public function getCustomerCifCreationData(int $credit_application_id, int $customer_id)
	{
		$this->load->model('sale/customer_credit_application');
		$cif_creation_data = $this->model_sale_customer_credit_application->getCustomerCifCreationData($customer_id);
		if(!empty($cif_creation_data)) {
			return $cif_creation_data;
		}else{
			$cif_creation_data = array();
			$credit_application_data = $this->model_sale_customer_credit_application->getCreditApplicationData($credit_application_id);
			if(!empty($credit_application_data)) {
				$cif_creation_data = array(
					'gender' 				=> ucfirst($credit_application_data['customer']['gender']),
					'title'  				=> '',
					'first_name' 			=> $credit_application_data['customer']['firstName'],
					'middle_name'  			=> $credit_application_data['customer']['middleName'],
					'last_name'				=> $credit_application_data['customer']['lastName'],
					'dob'					=> $credit_application_data['customer']['dob'],
					'mother_maiden_name'	=> '',
					'community'				=> '',
					'marital_status'		=> $credit_application_data['customer']['maritalStatus'],
					'gross_income'			=> $credit_application_data['businessDetails']['annual_turnover'],
					'refer_pan_no'			=> $credit_application_data['customer']['panNumber'],
					'refer_address_type'  	=> '',
					'refer_address_line1' 	=> $credit_application_data['customer']['permanentAddress']['line1'],
					'refer_address_line2' 	=> $credit_application_data['customer']['permanentAddress']['line2'],
					'refer_address_line3' 	=> $credit_application_data['customer']['permanentAddress']['line3'],
					'refer_city'			=> $credit_application_data['customer']['permanentAddress']['city'],
					'refer_state'			=> $credit_application_data['customer']['permanentAddress']['state'],
					'refer_postcode'		=> $credit_application_data['customer']['permanentAddress']['postcode'],
					'refer_phone'			=> $credit_application_data['customer']['telephoneNumber'], //['residentialAddress']['landlinePhoneNumber'],
					'corporate_name'		=> $credit_application_data['businessDetails']['companyName'],
					'incorporation_date' 	=> $credit_application_data['businessDetails']['dateOfIncorporation'] ?? '',
					'entity_pan_no' 		=> $credit_application_data['customer']['panNumber'],
					'annual_turnover' 		=> $credit_application_data['businessDetails']['businessAddress']['annual_turnover'] ?? '',
					'entity_gst_no' 		=> $credit_application_data['businessDetails']['businessAddress']['gstNumber'] ?? '',
					'entity_address_type' 	=> '',
					'entity_address_line1' 	=> $credit_application_data['businessDetails']['businessAddress']['line1'],
					'entity_address_line2' 	=> $credit_application_data['businessDetails']['businessAddress']['line2'],
					'entity_address_line3' 	=> $credit_application_data['businessDetails']['businessAddress']['line3'],
					'entity_city' 			=> $credit_application_data['businessDetails']['businessAddress']['city'],
					'entity_state' 			=> $credit_application_data['businessDetails']['businessAddress']['state'],
					'entity_country' 		=> '',
					'entity_postcode' 		=> $credit_application_data['businessDetails']['businessAddress']['postcode'],
					'entity_phone' 			=> $credit_application_data['customer']['telephoneNumber'],
					'entity_email' 			=> $credit_application_data['customer']['emailAddress'],
					'consent_details' 		=> '',
					'request_ref_number' 	=> '',
					'applied_on' 			=> '',
					'tc_acceptance_flag' 	=> ''
				);
			}
			return $cif_creation_data;
		}
	}

	public function download_csv()
	{
        $this->load->model('sale/customer_credit_application');
        $this->load->model('sale/customer');
        $this->load->model('user/user');

	     $data = array(); // Initializing the data array to be passed on to template files

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_step'])) {
			$filter_step = $this->request->get['filter_step'];
		} else {
			$filter_step = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_pancard']) && !empty($this->request->get['filter_pancard'])) {
			$filter_pancard = $this->request->get['filter_pancard'];
		} else {
			$filter_pancard = null;
		} 
		if (isset($this->request->get['filter_document_type']) && !empty($this->request->get['filter_document_type'])) {
			$filter_document_type = $this->request->get['filter_document_type'];
		} else {
			$filter_document_type = null;
		}

        if (isset($this->request->get['filter_sms_log_type']) && !empty($this->request->get['filter_sms_log_type'])) {
            $filter_sms_log_type = $this->request->get['filter_sms_log_type'];
        } else {
            $filter_sms_log_type = null;
        }

		if (isset($this->request->get['filter_document_status']) && !empty($this->request->get['filter_document_status'])) {
			$filter_document_status = $this->request->get['filter_document_status'];
		} else {
			$filter_document_status = null;
		}  
                if (isset($this->request->get['filter_application_id']) && !empty(trim($this->request->get['filter_application_id']))) {
			$filter_application_id = trim($this->request->get['filter_application_id']);
		} else {
			$filter_application_id = null;
		}
                
                if (isset($this->request->get['filter_telephone'])) {
			$filter_telephone = $this->request->get['filter_telephone'];
		} else {
			$filter_telephone = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
		 $filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
		 $filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['filter_followup_date'])) {
		 $filter_followup_date = $this->request->get['filter_followup_date'];
		} else {
			$filter_followup_date = null;
        }
        
        if (isset($this->request->get['filter_rbl_approved'])) {
            $filter_rbl_approved = $this->request->get['filter_rbl_approved'];
        } else {
            $filter_rbl_approved = null;
        }

        if (isset($this->request->get['filter_discrepency_status'])) {
            $filter_discrepency_status = $this->request->get['filter_discrepency_status'];
        } else {
            $filter_discrepency_status = null;
        }

		if (isset($this->request->get['filter_customer_id'])) {
		 $filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
        }
        
        if (isset($this->request->get['filter_city'])) {
			$filter_city = $this->request->get['filter_city'];
		} else {
			$filter_city = null;
        }
        
        if (isset($this->request->get['filter_state'])) {
			$filter_state = $this->request->get['filter_state'];
		} else {
			$filter_state = null;
		}

		if (isset($this->request->get['filter_by_crif']) && $this->request->get['filter_by_crif']=='1') {
			$filter_by_crif = $this->request->get['filter_by_crif'];
		} else {
			$filter_by_crif = 0;
		}

// print_r($this->request->get);die;
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'capp.created_date';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		$data['customers'] = array();
		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_step'              => $filter_step,
			'filter_email'             => $filter_email,
            'filter_application_id'    => $filter_application_id,
            'filter_telephone'         => $filter_telephone,
            'filter_date_added'        => $filter_date_added,
            'filter_date_modified'     => $filter_date_modified,
            'filter_followup_date'    => $filter_followup_date,
            'filter_pancard'           => $filter_pancard,
            'filter_document_type'     => $filter_document_type,
            'filter_sms_log_type'     => $filter_sms_log_type,
            'filter_document_status'   => $filter_document_status,
            'filter_customer_id'        => $filter_customer_id,
            'filter_rbl_approved'      => $filter_rbl_approved,
            'filter_discrepency_status'=> $filter_discrepency_status,
            'filter_city'              => $filter_city,
            'filter_state'              => $filter_state,
            'filter_by_crif'			=>	$filter_by_crif,
			'sort'  => $sort,
			'order' => $order
		);

		$results = $this->model_sale_customer_credit_application->getCustomerCreditApplication($filter_data);
		$customer_ids = array_column($results,'customer_id');  
        
        // Getting Sales (CRM) Agent names for these customers
        $this->load->model('lead/lead', 'frontend');
        $agent_names = $this->frontend_model_lead_lead->getCustomerAgentName($customer_ids);

        // Getting Sales (CRM) Agent ids for these customers
        $agent_ids = $this->frontend_model_lead_lead->getCustomerUserId($customer_ids);
        
        $wsb_credit = new WsbCreditPayment($this);
        $customer = new Customer($this->registry);
		foreach ($results as $result) {
        $avaiable_credit_balance = $wsb_credit->getAvaiableCreditBalance($result['customer_id']);
        $customer_wsb_credit_payment_data = $customer->getWsbCreditPaymentData($result['customer_id']);
		   $tl_name = '';
           if(!empty($agent_ids[$result['customer_id']]))
           {
             $tl_names = $this->frontend_model_lead_lead->getCrmTeamLeadEmailId($agent_ids[$result['customer_id']]);
              if($tl_names->num_rows)
              {
           	    $tl_name = $tl_names->row['name'] ?? '';
               }
            }

            $data['customers'][] = array(
				 'customer_credit_application_id' => $result['id'],
				 'customer_id'		 => $result['customer_id'],
				 'name'              => $result['name'] ,
                 'company_name'      => $result['company_name'],
				 'date_added'		 => $result['date_added'],
				 'document_status'	 => $result['document_status'],
				 'agent_name'        => isset($agent_names[$result['customer_id']]) ? $agent_names[$result['customer_id']] : 0,
				 'tl_name'           => $tl_name??'',
				 'crif_score'		=>$result['crif_score']??'',
				 'credit_limit'		=>$customer_wsb_credit_payment_data['credit_limit']??'',
				 'avaiable_credit_balance'=>$avaiable_credit_balance??'0',
			);
		}
      
      $file_name = 'Customer-Credit-Application'.date('dmYHis').'.csv';
      $fp = fopen('php://output', 'w');

      if(!empty($this->request->get['filter_document_status']) && $this->request->get['filter_document_status']=='activated'){
      	$csv_data = array('Credit Application Id','Customer Id','Customer Name','Agent Name','TL Name','Shop', 'Date Added','Document Status','Crif Score','Credit Limit','Avaiable Credit Balance');
      }else{
      	$csv_data = array('Credit Application Id','Customer Id','Customer Name','Agent Name','TL Name','Shop', 'Date Added','Document Status','Crif Score','Credit Limit');
      }
      
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$file_name);
        fputcsv($fp, $csv_data);
        if(!empty($this->request->get['filter_document_status']) && $this->request->get['filter_document_status']=='activated'){
	      	foreach($data['customers'] as $customers){
			      	$csv_data = array(
		           	         $customers['customer_credit_application_id'],
		           	         $customers['customer_id'],
		           	         $customers['name'],
		           	         $customers['agent_name'],
		           	         $customers['tl_name'],
		           	         $customers['company_name'],
		           	         $customers['date_added'], 
		           	         $customers['document_status'],
		           	     	 $customers['crif_score'],
		           	     	 $customers['credit_limit'],
		           	     	 $customers['avaiable_credit_balance']);
	           fputcsv($fp, $csv_data);
	        }
      	}else{
	      	foreach($data['customers'] as $customers){
			      	$csv_data = array(
		           	         $customers['customer_credit_application_id'],
		           	         $customers['customer_id'],
		           	         $customers['name'],
		           	         $customers['agent_name'],
		           	         $customers['tl_name'],
		           	         $customers['company_name'],
		           	         $customers['date_added'], 
		           	         $customers['document_status'],
		           	     	 $customers['crif_score'],
		           	     	 $customers['credit_limit']);
	           fputcsv($fp, $csv_data);
	        }
      	}
        
        exit;
	}


    public function today_dialer_list()
    {
        $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/today_dialer_list', $data);

		$this->document->setTitle($this->language->get('heading_title'));


		if (isset($this->request->get['filter_lead_id'])) {
			$filter_lead_id = $this->request->get['filter_lead_id'];
		} else {
			$filter_lead_id = null;
		}

		if (isset($this->request->get['filter_mobile_number'])) {
			$filter_mobile_number = $this->request->get['filter_mobile_number'];
		} else {
			$filter_mobile_number = null;
		}

		if (isset($this->request->get['filter_lead_name'])) {
			$filter_lead_name = $this->request->get['filter_lead_name'];
		} else {
			$filter_lead_name = null;
		}

		if (isset($this->request->get['filter_request_from'])) {
			$filter_request_from = $this->request->get['filter_request_from'];
		} else {
			$filter_request_from = null;
		}

		if (isset($this->request->get['filter_sequence_number'])) {
			$filter_sequence_number = $this->request->get['filter_sequence_number'];
		} else {
			$filter_sequence_number = null;
		}

		
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sequence_number';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		

		$url = '';

		if (isset($this->request->get['filter_lead_id'])) {
			$url .= '&filter_lead_id=' . urlencode(html_entity_decode($this->request->get['filter_lead_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_mobile_number'])) {
			$url .= '&filter_mobile_number=' . urlencode(html_entity_decode($this->request->get['filter_mobile_number'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_lead_name'])) {
			$url .= '&filter_lead_name=' . urlencode(html_entity_decode($this->request->get['filter_lead_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_request_from'])) {
			$url .= '&filter_request_from=' . urlencode(html_entity_decode($this->request->get['filter_request_from'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sequence_number'])) {
			$url .= '&filter_sequence_number=' . urlencode(html_entity_decode($this->request->get['filter_sequence_number'], ENT_QUOTES, 'UTF-8'));
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

		$sort_url = $url;

		if ($order == 'ASC') {
			$sort_url .= '&order=DESC';
		} else {
			$sort_url .= '&order=ASC';
		}
		$data['sort_created'] = $this->url->link('sale/customer_credit_application/today_dialer_list', 'token=' . $this->session->data['token'] . '&sort=created' . $sort_url, 'SSL');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/customer_credit_application/today_dialer_list', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['dialer_list'] = array();

		$filter_data = array(
			'filter_user_id'         => $this->user->getCrmId(),
			'filter_lead_id'         => $filter_lead_id,
			'filter_lead_id'         => $filter_lead_id,
			'filter_mobile_number'   => $filter_mobile_number,
			'filter_lead_name'      => $filter_lead_name,
            'filter_request_from'    => $filter_request_from,
            'filter_sequence_number' => $filter_sequence_number,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

        $this->load->model('lead/lead', 'frontend');

        $dialer_total = $this->frontend_model_lead_lead->getTotalTodayDialerList($filter_data);
        $results = $this->frontend_model_lead_lead->getTodayDialerList($filter_data);


		foreach ($results as $result) {
           
            $data['dialer_list'][] = array(
            	'id' => $result['id'],
            	'user_id' => $result['user_id'],
            	'call_status' => $result['call_status'],
            	'lead_id' => $result['lead_id'],
            	'sequence_number' => $result['sequence_number'],
            	'date' => $result['date'],
            	'content' => $result['content'],
            	'created' => $result['created'],
            	'lead_business_name' => $result['lead_business_name'],
            	'lead_followup_date' => $result['lead_followup_date'],
            	'mobile_number' => $result['mobile_number'],
            	'lead_name' => $result['lead_name'],
            	'crm_lead_link' => CRM_URL.'leads/view/'.$result['lead_id'],
            	'credit_link' =>  $this->url->link('sale/customer_credit_application/index', 'token=' . $this->session->data['token'] . '&filter_telephone='.$result['mobile_number'], 'SSL')
            	);
		}

		$data['khufiya_user_id'] = $this->session->data['user_id'];

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
      
		$pagination = new Pagination();
		$pagination->total = $dialer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/customer_credit_application/today_dialer_list', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$data['pagination'] = $pagination->render();

       
		$data['results'] = sprintf($data['text_pagination'], ($dialer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($dialer_total - $this->config->get('config_limit_admin'))) ? $dialer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $dialer_total, ceil($dialer_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_lead_id'] = $filter_lead_id;
        $data['filter_mobile_number'] = $filter_mobile_number;
        $data['filter_lead_name'] = $filter_lead_name;
        $data['filter_request_from']  = $filter_request_from;
        $data['filter_sequence_number'] = $filter_sequence_number;
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['token'] =$this->session->data['token'];



		$this->response->setOutput($this->load->view('sale/today_dialer_list.tpl', $data));
    }
	/** Kusum Joshi
	*	Get credit partners list and send it to modal
	*
	**/

	public function shareApplication(){
		$data=array();
		if (!empty($this->request->get['credit_application_id'])) {
			$this->load->model('sale/customer_credit_application');
			$credit_partners_data = $this->model_sale_customer_credit_application->getCreditPartnersList();
			foreach($credit_partners_data as $partner){
				if($partner['name'] == "ePayLater"){
				$partner['url']	="index.php?route=sale/customer_credit_application/sharetoEpaylater&token=".$this->session->data['token']."&credit_application_id=" .$this->request->get['credit_application_id'];
				}
				else{
					$partner['url'] = '';
				}

				$credit_partners[]=$partner;
			}
			$data['credit_partners']=$credit_partners;
		}

		$this->response->setOutput($this->load->view('sale/share_credit_application_popup.tpl',$data));
			
	}

	/**
	* Kusum Joshi
	* Shares customer application with epayLater and saves the applicationId
	* in tables. If kyc docs submiited, kyc is also done
	**/

	public function sharetoEpaylater(){

		if(!empty($this->request->get['credit_application_id'])){

			$this->load->model('sale/customer_credit_application');
			// Get Credit Application Data
			$credit_application_data = $this->model_sale_customer_credit_application->getCreditApplicationData($this->request->get['credit_application_id']);
			if(!empty($credit_application_data)){

				// Send the customer application data to Epaylater
				// and maintain logs for Api. On Success, save applicationId and applicantId 
					$submit_credit_application = $this->sendApplicationtoEpaylater($this->request->get['credit_application_id'],$credit_application_data);
				if(!empty($submit_credit_application)){
					if(isset($submit_credit_application['requestSucceeded'])){
						$message = 'Successfully submitted application to ePayLater';
					}else if($submit_credit_application['message']){
						$message = $submit_credit_application['message'];
						$data['status'] = 0;
						$data['message'] = $message;
						echo json_encode($data);
						die;
					} else {
						$message = '';
					}
					$data['status'] = 1;
					$data['message'] = $message;
					$data['redirect_url'] = isset($submit_credit_application['redirectUrl']) ? $submit_credit_application['redirectUrl'] : false;
					echo json_encode($data);
					die;
				}else{
					$data['status'] = 0;
					$data['message'] = 'Sharing application to ePayLater failed';
					echo json_encode($data);
					die;
				}

			}else{
				$data['status'] = 0;
				$data['message'] = 'No Application exists';
				echo json_encode($data);
				die;
			}

		}else{
			$data['status'] = 0;
			$data['message'] = 'Customer Credit Application Not Provided';
			echo json_encode($data);
			die;
		}
	}

	/**
	* Kusum Joshi
	* Checks application status based on credit application id 
	**/
	public function checkApplicationStatus(){
		if(!empty($this->request->get['credit_application_id'])){

			$this->load->model('sale/customer_credit_application');
			$credit_application_status = $this->checkEpaylaterApplicationStatus($this->request->get['credit_application_id']);
			if(!empty($credit_application_status)){
				if(!empty($credit_application_status[0]['applicationId'])){
					$credit_application_status = $credit_application_status[0];
					$json['status'] = 1;
					$json['message'] = 'Application Id: '. $credit_application_status['applicationId'] . "<br>".
					"Amount: ". $credit_application_status['amount'] . "<br>".
					"Tenor: ". $credit_application_status['tenor'];
					echo json_encode($json);
					die;
				}else{
					$json['status'] = 0;
					$json['message'] = isset($credit_application_status['message']) ? $credit_application_status['message'] :'No Application Found';
					echo json_encode($json);
					die;
				}
			}else{
				$json['status'] = 0;
				$json['message'] = 'No Application Found';
				echo json_encode($json);
				die;
			}

			}else{
				$json['status'] = 0;
				$json['message'] = 'Customer Credit Application Not Provided';
				echo json_encode($json);
				die;
			}
		}

	public function edit(){

		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->load->model('sale/customer');

		$customer_info = $this->model_sale_customer->getCustomerById($customer_id,array('customer_access_token'));
		if ($customer_info) {

			if(!empty($customer_info['customer_access_token'])){
				$token = $customer_info['customer_access_token'];
			} else {
				$token = md5(mt_rand());
				$this->model_sale_customer->editCustomerAccessToken($customer_id, $token);
			}
			$json['status'] = 1;
			$json['message'] = 'Customer Found';
			$json['token'] = $token;
			$json['user_id'] = $this->session->data['user_id'];
			echo json_encode($json);
			die;
		} else{
			$json['status'] = 0;
			$json['message'] = 'No Customer Found';
			echo json_encode($json);
			die;
		}

	}

	public function create_lead()
	{
	 if (!empty($this->request->post['customer_id'])) 
	 {
		$this->load->model('sale/customer_credit_application');
       $customer_data = $this->model_sale_customer_credit_application->addDefaultDataInCreditApplication($this->request->post['customer_id']);
       
       if($customer_data > 0)
       {
       	 $json['status'] = 1;
         $json['message'] = 'Lead create successfully';
	     echo json_encode($json);
	     die;
       }
       else
       {
       	 $json['status'] = 0;
	     $json['message'] = 'No Customer Found';
	     echo json_encode($json);
	      die;
       }
     }
     else
     {
       $json['status'] = 0;
	   $json['message'] = 'No Customer Found';
	   echo json_encode($json);
	   die;	
     }
			
			
	}

	public function creditApplicationLog(){

		$data=array();
		$data['crm_users'] = array();
			$data['khufiya_users'] = array();
			$data['customer_name'] = '';
		if (!empty($this->request->get['credit_application_id'])) {
			$this->load->model('sale/customer_credit_application');
			$application_log = $this->model_sale_customer_credit_application->getCreditApplicationLog($this->request->get['credit_application_id']);
			$data['application_log']=$application_log;
			if(!empty($application_log)){
				$group_log = $this->model_sale_customer_credit_application->getCreditApplicationGroupbyTypeLog($this->request->get['credit_application_id']);

				if(!empty($group_log)){

					foreach($group_log as $group){
						if($group['type'] == 'CRM_USER'){
							$crm_users = $this->model_sale_customer_credit_application->getCrmUserNameFromCrmUserIds($group['user_id']);
							foreach($crm_users as $user){
								$crm_user_list[$user['crm_user_id']] = $user['name'];
							}

							$data['crm_users'] = $crm_user_list;
						} 
						else if ($group['type'] == 'KHUFIYA_USER'){
							$this->load->model('user/user');
							$khufiya_users = $this->model_user_user->getUserNameByUserIds($group['user_id']);

							foreach($khufiya_users as $user){
								$khufiya_user_list[$user['user_id']] = $user['name'];
							}

							$data['khufiya_users'] = $khufiya_user_list;

						}else if ($group['type'] == 'CRON'){

							$data['cron_user'] = 'CRON SYSTEM';

						} else {
							$this->load->model('account/customer', 'frontend');
                    		$customer_data = $this->frontend_model_account_customer->getCustomer($group['user_id']);
                    		$data['customer_name'] = $customer_data['firstname'] . ' ' .  $customer_data['lastname'];
						}
					}
					
				}
			}
			
		}
		$this->response->setOutput($this->load->view('sale/credit_application_log.tpl',$data));
	}


	public function getEmailList(){

		$data=array();
		if (!empty($this->request->get['customer_id'])) {
			$this->load->model('account/credit_application', 'frontend');
    		$customer_email_data = $this->frontend_model_account_credit_application->getCustomerSavedEmail($this->request->get['customer_id']);
    		$data['customer_email_data'] = $customer_email_data;
			
		}
		$this->response->setOutput($this->load->view('sale/credit_application_email_list.tpl',$data));
	}

	public function shareKycDocumentstoEpaylater(){
		if(!empty($this->request->get['credit_application_id'])){

			$this->load->model('sale/customer_credit_application');
					$credit_application_doc_data = $this->model_sale_customer_credit_application->getKycDocuments($this->request->get['credit_application_id']);
					if(!empty($credit_application_doc_data)){
						$submit_kyc_application = $this->sendKyctoEpaylater($this->request->get['credit_application_id'],$credit_application_doc_data);
						if(empty($submit_kyc_application)){
							$data['status'] = 0;
							$data['message'] = '. Uploading Kyc documents failed';
							echo json_encode($data);
							die;
						}
					}
					$data['status'] = 1;
					$data['message'] = 'Kyc documents uploaded successfully';
					echo json_encode($data);
					die;
			
		}else{
			$data['status'] = 0;
			$data['message'] = 'Customer Credit Application Not Provided';
			echo json_encode($data);
			die;
		}
	}

	/** Kusum Joshi
    * send Application to Epaylater and save result in database tables
    **/
    public function sendApplicationtoEpaylater($credit_application_id, $credit_application_data = array()){

        if(!empty($credit_application_data)){

        $data_json = json_encode($credit_application_data);
        $api_url = EPAYLATER_URL . 'credit-application';
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Content-Type: application/json',
                'Authorization: Bearer ' . EPAYLATER_SECRET_KEY,
                'Cache-Control: no-cache')
               );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true );
        // print_r($result);die;

        $this->load->model('sale/customer_credit_application');

        $credit_application_entry = $this->model_sale_customer_credit_application->saveEpaylaterApplicationShareLog($credit_application_id,$result,$data_json, $share_type="EPAY_CREATE");
        if(!empty($result['applicationId'])){
            $credit_application_share_id = $this->model_sale_customer_credit_application->saveEpaylaterApplicationSuccess($result,$credit_application_id);
        }

        return $result;
        }
        else{
            return array();
        }
    }

    /**
    * Kusum Joshi
    * upload files to epaylater for kyc
    **/
    public function sendKyctoEpaylater($credit_application_id, $credit_kyc_data = array()){

   	$this->load->model('sale/customer_credit_application');

        $credit_response = $this->model_sale_customer_credit_application->getCreditResponseApplicationId($credit_application_id);
        

        if(!empty($credit_kyc_data) && !empty($credit_response['credit_response_application_id'])){
        $data_json = json_encode($credit_kyc_data);
        $api_url = EPAYLATER_URL .'/credit-application/'. $credit_response['credit_response_application_id'] .'/uploadFile';
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Authorization: Bearer ' . EPAYLATER_SECRET_KEY,
                'Cache-Control: no-cache')
               );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $credit_kyc_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true );

        $credit_application_entry = $this->model_sale_customer_credit_application->saveEpaylaterApplicationShareLog($credit_application_id,$result,$data_json, $share_type="EPAY_KYC");
        if(!empty($result['applicationId'])){
            $credit_application_entry = $this->model_sale_customer_credit_application->updateKycFlag($credit_application_id);
        }

        return $result;
        }
        else{
            return array();
        }

    }

    /**
    * Kusum Joshi
    * Calls epaylater Api to check application Status
    **/
    public function checkEpaylaterApplicationStatus($credit_application_id){

    	$this->load->model('sale/customer_credit_application');

        $credit_response = $this->model_sale_customer_credit_application->getCreditResponseApplicationId($credit_application_id);
        
        if(!empty($credit_application_id) && !empty($credit_response['credit_response_applicant_id'])){
        $api_url = EPAYLATER_URL .'/credit-application/'. $credit_response['credit_response_applicant_id'] .'/getApplicationsStatus';
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Authorization: Bearer ' . EPAYLATER_SECRET_KEY,
                'Cache-Control: no-cache')
               );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true );
        return $result;
        }
        else{
            return array();
        }

    }
    
    /**
     * @author Vishnu shekhawat
     * @param array $customer_ids
     * @return array
     */
    public function fetchCustomerShopImagesAndContactsFromCrm(array $customer_ids): array {

        if (!empty($customer_ids)) {

            $post_data = json_encode(array('customer_ids' => $customer_ids));
           
            if (strtolower(SITE_ENVIRONMENT) == 'production') {
                $url = WSBOX_CRM_URL . 'webapi/CreditApplication/fetchAvailableImagesAndContactsForCustomer';
            } else {
                $url = WSBOX_CRM_URL . 'staging/webapi/CreditApplication/fetchAvailableImagesAndContactsForCustomer';
            }
            //echo $url; die;
            // $url = 'http://localhost/wsbox-crm/webapi/CreditApplication/fetchAvailableImagesAndContactsForCustomer';
            
            $curl = curl_init();
            // Set SSL if required
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            try {
                $response = curl_exec($curl);
                curl_close($curl);
            } catch (Exception $e) {
                return array();
            }
         
            $result = json_decode($response, true);

            if(!empty($result)) { return $result['result']; }
            else { return array(); }
            
        } else {
            return array();
        }
    }

    public function ePayLaterVerifyOTP(){
    	if(!empty($this->request->get['credit_application_id'])){
    		$credit_application_id = $this->request->get['credit_application_id'];
    		$type=$this->request->get['flag_type'];
    		$is_checked=$this->request->get['is_checked'];
    		if($is_checked == "false"){
    			$checked = 0;
    		}else{
    			$checked = 1;
    		}
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->updateePayLaterOTPFlags($credit_application_id,$this->request->get['flag_type'],$checked);
    	}
    	if($response){
    			$data['status'] = '1';
    			if($type == "kyc"){
    				$data['message'] = "ePayLater KYC Verify OTP Update Successful";
    			}else{
    				$data['message'] = "ePayLater Onboard Verify OTP Update Successful";
    			}
    		}else{
    			$data['status'] = '0';
    			$data['message'] = 'Error';
    		}
    	echo json_encode($data);
		die; 
    }

    public function updateDocumentStatus(){
    	if(!empty($this->request->get['credit_application_id'])){
           
    		$credit_application_id = $this->request->get['credit_application_id'];
    		$document_status=$this->request->get['document_status'];
                $credit_followup_date = $this->request->get['followup_date']??'';
    		$customer_mail= (isset($this->request->get['customer_mail'])) ? $this->request->get['customer_mail'] : 0;
    		$this->load->model('sale/customer_credit_application');

            $this->load->model('lead/lead','frontend');
            $this->load->model('dialer/dialer','frontend');
            $this->load->model('webengage/webengage','frontend');
            $this->load->model('account/credit_application','frontend');
                
                $customer_id = $this->model_sale_customer_credit_application->getCustomerIdUsingCreditApplicationId($credit_application_id);
                if(!empty($this->request->get['document_status'])){
					$status_for_webengage = ucwords(str_replace('_', ' ', $this->request->get['document_status']));
                }
                
    		$response = $this->model_sale_customer_credit_application->updateDocumentStatus($credit_application_id,$document_status);

    		if($document_status=='approved_document_received' && !empty($this->request->get['payment_cycle'])){
    			$response = $this->model_sale_customer_credit_application->updatePaymentCycle($credit_application_id,$this->request->get['payment_cycle']);
    		}

    		 $this->model_sale_customer_credit_application->addStatusRemarks($this->request->get);

    		 $this->model_sale_customer_credit_application->addCreditPreapproved($this->request->get);
                   
                $crm_user_id = $this->model_sale_customer_credit_application->getCrmUserIdFromKhufiyaUserId( $this->user->getId());
                $comment = 'Update from Credit application status updated: .' . $this->request->get['document_status'];
                if(isset($this->request->get['show_comment']) && $this->request->get['show_comment'] == 1){
                    if(isset($this->request->get['remark']) && !empty($this->request->get['remark'])){
                        $comment .= "  And Remark is :- " . $this->request->get['remark'];
                    }
                }
                if(!empty($this->request->get['telephone'])){
                     $this->frontend_model_lead_lead->addCreditApplicationCommentInTask($this->request->get['telephone'], $crm_user_id, $comment , $customer_id, $document_status, $credit_followup_date);
                }
               

              

            $date = date("d-m-Y h:i:s");
            $username = $this->user->getUserName();
            //=====add status on desktop Dailer=====//
            $creditFormDetail=$this->model_sale_customer_credit_application->getCreditApplicationData($credit_application_id);
            if($response){
            		$list_id='0';
	              if(!empty($creditFormDetail['customer']['telephoneNumber']) && !empty($document_status)){
		              	if($document_status=='document_awaited' && $creditFormDetail['customer']['draft']=='2'){
		              		$list_id='988988';
		              	}else if($document_status=='document_awaited' && ($creditFormDetail['customer']['draft']=='0' || $creditFormDetail['customer']['draft']='1')){
		              		$list_id='986986';
		              	}else if($document_status=='approved_but_agreement_pending'){
		              		$list_id='985985';
		              	}else if($document_status=='customer_not_interested'){
		              		$list_id='987987';
		              	}
			              	if(!empty($list_id)){
			              		$this->frontend_model_dialer_dialer->sendDataToDesktopDailer($creditFormDetail['customer']['telephoneNumber'], $list_id);
			              	}
	              }
            }
            //=====add status on desktop Dailer End=====//
            $get_data_for_mail = $this->model_sale_customer_credit_application->getLeadDetailByCreditApplicationId($credit_application_id);



            //====push notification======//
                              $lead_detail=$this->frontend_model_lead_lead->getLeadIdUsingCustomerInCrm($customer_id);
                              $customer_name=$this->frontend_model_account_credit_application->getCreditApplicationCustomerName($customer_id);
                                    if(!empty(trim($customer_name))){
                                        $notification_name=$customer_name;
                                      }else{
                                        $notification_name='Customer';
                                      }
                                    if(!empty($lead_detail['lead_id']) && !empty($lead_detail['user_id'])){
                                        $lead_id=$lead_detail['lead_id'];
                                        $lead_user_id=$lead_detail['user_id'];
                                        $message=$notification_name.' status update on credit form Status:'.ucwords(str_replace('_', ' ', $this->request->get['document_status']));
                                        $title='Status update on credit form';
                                        $action=CRM_URL.'leads/view/'.$lead_id;
                                        $this->frontend_model_account_credit_application->sendPushNotificationAgent($customer_id,$lead_id,$lead_user_id,$action,$message,$title);
                                    }
            

            if(($document_status == "approved_but_agreement_pending" || $document_status == "approved_document_received" || $document_status == "approved_without_bank_statement") && !empty($this->request->get['credit_limit']))
            {
            	//===========//
            	$count_pre_approved_process=$this->model_sale_customer_credit_application->countPreApprovedProcess($credit_application_id);
            	if(!empty($count_pre_approved_process) && $count_pre_approved_process=='1'  && !empty($creditFormDetail['customer']['customer_id'])){
            		$this->model_sale_customer_credit_application->makeCreditApplicationStickyToAgent($creditFormDetail['customer']['customer_id']);
            	}
            	//============//
                $limitForCustomer = $this->request->get['credit_limit'];

                $credit_app_status = ucwords(str_replace('_', ' ', $this->request->get['document_status']));

                $get_customer_name = $get_data_for_mail['name'] ?? '';
                $get_company_name = $get_data_for_mail['company_name'] ?? '';
                $get_lead_name = $get_data_for_mail['lead_data']['agent_name'] ?? '';

                $credit_customer_id = $get_data_for_mail['customer_id'] ?? '';

                $htmlForTL = "Hi,<br/><br/> Following Lead is approved for credit:<br/><br/> 
                        <b>Customer Id:</b> " . $credit_customer_id . " <br />
                        <b>Customer Name:</b> " . $get_customer_name . " <br />
                        <b>Business Name:</b> " . $get_company_name . " <br />  
                        <b>Status:</b> " . $credit_app_status . " <br />  
                        <b>Approved By:</b> " . $username['username'] . " <br />
                        <b>Agent Name:</b> " . $get_lead_name . " <br />";

                $subjectForTL = $credit_app_status . " " . $get_company_name . " " . $get_lead_name . " " . $date;
                $htmlForTL .= "<b>Credit Limit:</b> Rs " . $this->request->get['credit_limit'] . "/-<br />";

                if (($document_status == "approved_but_agreement_pending" || $document_status == "approved_document_received" || $document_status == "approved_without_bank_statement") && !empty($get_data_for_mail['email']) && $customer_mail == 1) {

                    $customerDetail['name'] = $get_customer_name;
                    $customerDetail['email'] = $get_data_for_mail['email'];

                    if(!empty($get_company_name))
                        $subjectForCustomer = $get_company_name . ' - ' . 'Credit Limit Approved_' . $limitForCustomer . '/-';
                    else
                        $subjectForCustomer = $get_customer_name . ' - ' . 'Credit Limit Approved_' . $limitForCustomer . '/-';

                    $this->sendMailAfterUserCreditApproved($subjectForCustomer, $limitForCustomer, $customerDetail);
                }

                if (!empty($get_data_for_mail['lead_data']['email'])) {
                    $this->sendMailLeadTL($subjectForTL, $htmlForTL, $get_data_for_mail['lead_data']);
                }
            }

            $html = "<b>Telephone:</b> ".$this->request->get['telephone']." <br />  <b>Status:</b> ".ucwords(str_replace('_', ' ', $this->request->get['document_status']))." <br /> ";

            if(!empty($this->request->get['reason']))
            {
              $html .= "<b>Reason:</b> " . implode(",", $this->request->get['reason']) . "  <br /> ";
            }

            $html .= "<b>Remark:</b> " . $this->request->get['remark'] . "  <br /><br /><br /> ";
            $html .= "<b>Date:</b> " . $date . "<br /> <b>Username:</b> " . $username['username'] . " <br /> ";

            $subject = 'Credit Application Update Status: '.$this->request->get['telephone'].' '.$date;

    		$this->sendMailNewNote($subject, $html);


    	}
    	if($response){

    		//====webengage start===//
                    if(!empty($customer_id) && !empty($status_for_webengage)){
                      $this->frontend_model_webengage_webengage->updateCreditApplicationStatusOnWebengage($customer_id,'NA',$status_for_webengage);
                    }
             //====webengage end===//
    			$data['status'] = '1';
    			$data['message'] = "Document status Update Successfully";
    		}else{
    			$data['status'] = '0';
    			$data['message'] = 'Error';
    		}
    	echo json_encode($data);
		die; 
    }
    
    public function getDocumentStatus(){

    	$result = '';

    	if(!empty($this->request->get['credit_application_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->getDocumentStatus($this->request->get['credit_application_id']);
            
            if(count($response) > 0)
            {
              $result = '<div class="document_status_data"> <table id="notesTable" style="width:100%">
                <tbody><tr>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Bank Account</th>
                    <th>IFSC Code</th>
                    <th>Credit Limit</th>
                    <th>Payment Cycle</th>
                    <th>Comment</th>
                    <th>Following Date</th>
                    <th>Date/Username</th> </tr> ';	
            }  
          
    		foreach($response as $note)
    		{

    		    $result .= '<tr class="'. strtolower($note['type']).'">';
    		    $result .= '<td>'. $note['type'].'</td>';
    		    $result .= '<td>'. ucwords(str_replace('_', ' ', $note['status'])).'</td>';
                $result .= '<td>'.$note['reason'].'</td>';
                $result .= '<td>'.$note['bank_account_last_digit'].'</td>';
                $result .= '<td>'.$note['ifsc_code'].'</td>';
                $result .= '<td>'.$note['credit_limit'].'</td>';
                   if(!empty($note['payment_cycle'])){
               	 		$result .= '<td>'.ucfirst(str_replace('_', ' ', $note['payment_cycle'])).'</td>';
	               }
	               else
	               {
	               		$result .= '<td></td>';
	               }
                $result .= '<td>'.$note['remark'].'</td>';

               if(!empty($note['followup_date']) && date('Y-m-d', strtotime($note['followup_date'])) == $note['followup_date'])
               {
               	 $result .= '<td>'.date("d-m-Y", strtotime($note['followup_date'])).'</td>';
               }
               else
               {
               	$result .= '<td></td>';
               }
               

                if($note['user_id'] == 0)
                {
                    $result .= '<td>'.date("d-m-Y h:i:s", strtotime($note['date_added'])).' <br /> System Update</td>';
                }
                else
                {
                    $result .= '<td>'.date("d-m-Y h:i:s", strtotime($note['date_added'])).' <br /> '.$note['username'].'</td>';
                }

              
                $result .= '</tr>';
    		}

    		if(count($response) > 0)
            {
            	$result .= '</tbody></table></div>';	
            }
             
          

    	}
    	echo $result;
		die; 
    }


    public function addNote(){
    	if(!empty($this->request->get['credit_application_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->addNote($this->request->get);

    		$date = date("d-m-Y h:i:s");
            $username = $this->user->getUserName();

            $html = "<b>Telephone:</b> ".$this->request->get['telephone']." <br /> <b>Note:</b> ". $this->request->get['note']." <br /><br /><br /> <b>Date:</b> " . $date . "  <br /> <b>Username:</b> " . $username['username'];

            $subject = 'Credit Application Note Posted: '.$this->request->get['telephone'].' '.$date;

    		$this->sendMailNewNote($subject, $html);

    	}
    	if($response){
    			$data['status'] = '1';
    			$data['message'] = "Note Added Successfully";
    		}else{
    			$data['status'] = '0';
    			$data['message'] = 'Error';
    		}
    	echo json_encode($data);
		die; 
    }

    public function getGstVerification(){
    	$result = '';
    	if(!empty($this->request->get['credit_application_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->getGstVerification($this->request->get['credit_application_id']);
            
            if(count($response) > 0)
            {
              $result = '<div class="document_status_data"> <table id="notesTable" style="width:100%">
                <tbody><tr>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Last Filling Date</th>
                    <th>Registration Date</th>
                    <th>Username</th> </tr> ';	
            }  
          
    		foreach($response as $gst)
    		{
    		   $result .= '<tr>';
    		   $result .= '<td>'. $gst['gst_type'].'</td>';
    		   if($gst['gst_active'] == 1)
    		   {
                 $result .= '<td>Active</td>';
    		   }
    		   else
    		   {
    		   	$result .= '<td>Deactive</td>';
    		   }
               $result .= '<td>'.$gst['last_filling_date'].'</td>';
               $result .= '<td>'.$gst['registration_date'].'</td>';

               if($gst['user_id'] == 0)
               {
                 $result .= '<td>'.$gst['date_added'].' <br /> System Update</td>';
               }
               else
               {
               	 $result .= '<td>'.$gst['date_added'].' <br /> '.$gst['username'].'</td>';
               }

              
               $result .= '</tr>';
    		}

    		if(count($response) > 0)
            {
            	$result .= '</tbody></table></div>';	
            }
    	}
    	echo $result;
		die; 
    }


    public function uploadFIDocument()
    {
        $credit_application_id = $this->request->post['credit_application_id'];
        $file_data = $this->request->files['fi_document_file'];

        $uploads_dir = DIR_IMAGE.'credit/agreement-docs/' . $credit_application_id . '/';

        if(!is_dir($uploads_dir))
       {
           mkdir($uploads_dir);
       }

        if (!empty($file_data['tmp_name'])) {
           $file_name = time().$file_data['name'];
           $result = $this->uploadImages($uploads_dir, $file_name, $file_data['tmp_name']);
         }

      $this->load->model('sale/customer_credit_application');
      $this->model_sale_customer_credit_application->addFiDocument($credit_application_id, 'credit/agreement-docs/' . $credit_application_id . '/'.$file_name);

       $json = array();
       $json['status']    = '1';
       $json['message']   = "Document upload Successfully";
        $json['file_name'] = $credit_application_id . '/'.$file_name;
       $json['credit_application_id'] = $credit_application_id;
       echo json_encode($json);
	   die;
    }

   
   public function get_crif_json()
   {
   	   $json = array();
   	   $credit_application_id = $this->request->get['credit_application_id'];
       $this->load->model('sale/customer_credit_application');
       $file_path = $this->model_sale_customer_credit_application->getCreditCrifJson($credit_application_id);

       	$json['success'] = 0;
        $json['content'] = '';
       if(!empty($file_path))
       {
       	$strJsonFileContents = file_get_contents(STATIC_CONTENT_URL_SSL.$file_path);
       	$data = json_decode($strJsonFileContents, true);
        if(isset($data[0]) && isset($data[0]['indvreports']))
        {
          $json['success'] = 1;
          $json['content'] = $data[0]['indvreports']['indvreport']['printablereport']['content'];	
        }
       }
       echo json_encode($json);
       exit;

   }

    public function uploadCrifDocument()
    {
    	$this->load->model('sale/customer_credit_application');
        $credit_application_id = $this->request->post['credit_application_id'];
        $customer_id = $this->request->post['customer_id'];
        $file_data = $this->request->files['filename'];
        $json = array();

        if (!empty($file_data['tmp_name'])) 
        {
        	if (SITE_ENVIRONMENT == 'Production') {
                $customer_folder_name = $customer_id;
            } else {
                $customer_folder_name = 'staging_' . $customer_id;
            }

            if ($customer_id == '0') {
                $customer_folder_name = $customer_folder_name . '_' . $credit_application_id;
            }

           $uploads_dir = 'credit_application/' . $customer_folder_name . '/crif-docs/';

           $file_name = time().$file_data['name'];
           $result = $this->uploadCrifImages($uploads_dir, $file_name, $file_data['tmp_name']);
           if($result)
           {  
              $this->model_sale_customer_credit_application->addCreditCrifJson($credit_application_id, $customer_id, $uploads_dir.$file_name);
 
         	  $strJsonFileContents = file_get_contents(STATIC_CONTENT_URL_SSL.$uploads_dir.$file_name);
         	  $data = json_decode($strJsonFileContents, true);
           }	
        }
     
       	if(isset($data[0]) && isset($data[0]['indvreports']))
        {
          $json['success'] = 1;
          $json['content'] = $data[0]['indvreports']['indvreport']['printablereport']['content'];
          $json['message']   = "Document upload Successfully";	
        }
        else
        {
          $json['content'] = '';
          $json['status']    = '0';	
          $json['message']   = "Invalid document";
        }
       
       $json['file_name'] = $credit_application_id . '/'.$file_name;
       $json['credit_application_id'] = $credit_application_id;
       echo json_encode($json);
	   die;
    }


   public function sendDocumentAttachmentMail()
   {
       $file_name = $this->request->get['file_name'];
       $credit_application_id = $this->request->get['credit_application_id'];

       $this->load->model('sale/customer_credit_application');
       $subject = "FI Document";

       $data = $this->model_sale_customer_credit_application->getFICustomeDetail($credit_application_id);
       
     if($data)
     {  

       if (method_exists($this, 'get')) {
           $config = $this->get('config');
       } else {
           $config = $this->config;
       }

       $html = $this->load->view('mail/send_mail_fi_verification.tpl',$data);
       $mail = new  PHPMailer();

       $mail->isSMTP();
       $mail->Host = $config->get('config_mail_smtp_hostname');
       $mail->Port = $config->get('config_mail_smtp_port');
       $mail->SMTPSecure = 'ssl';
       $mail->SMTPAuth = true;
       $mail->Username = $config->get('config_mail_smtp_username');
       $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
       $mail->addReplyTo(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
       $mail->addAddress(EMAIL_IDS['fi_company']['email_id'], EMAIL_IDS['fi_company']['name']);
       $mail->addCC(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
       $mail->AddAttachment(DIR_IMAGE.$file_name);
       $mail->AddAttachment(DIR_IMAGE.'credit/agreement-docs/NACH-Printout-explanation.jpg');
       $mail->AddAttachment(DIR_IMAGE.'credit/agreement-docs/sample-cheque.jpeg');
       $mail->AddAttachment(DIR_IMAGE.'credit/agreement-docs/PayLater_Docket_May19.pdf');
       
       $mail->Subject = $subject;
       $mail->msgHTML($html);
       $mail->isHTML(true);
       $mail->send();

      $json = array();
      $json['status'] = '1';
      $json['message'] = "SUCCESS: Document mailed Successfully";
    }
    else
    {
      $json = array();
      $json['status'] = '0';
      $json['message'] = "ERROR: Customer data not found. please try again";	
    }
      echo json_encode($json);
       die;
   }

     public function uploadImages($directory, $filename, $file_tmp)
    {
       if(move_uploaded_file($file_tmp, $directory . $filename))
       {
            return true;
        } else {
            return false;
        }
    }

    public function getDocumentChecklist()
    {
        $response = array();
    	if(!empty($this->request->get['credit_application_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->getDocumentChecklist($this->request->get['credit_application_id']);
        }
        echo json_encode($response);
        exit;			
    } 

    public function saveDocumentChecklist()
    {     
    	 $response = false;
         if(!empty($this->request->post['credit_application_id'])){
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->saveDocumentChecklist($this->request->post);
    	}
    	if($response){
    			$data['status'] = '1';
    			$data['message'] = "Document status update successfully";
    		}else{
    			$data['status'] = '0';
    			$data['message'] = 'Error';
    		}
    	echo json_encode($data);
		die; 	
    }    

    public function getQuestionsAndAnswer()
    {
        $response = array();
    	if(!empty($this->request->get['credit_application_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->getQuestionAndAnswer($this->request->get['credit_application_id']);
        }
        echo json_encode($response);
        exit;			
    }

    public function saveQuestions()
    {     
    	 $response = false;
         if(!empty($this->request->post['credit_application_id'])){
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->saveQuestions($this->request->post);
    	}
    	if($response){
    			$data['status'] = '1';
    			$data['message'] = "Document status update successfully";
    		}else{
    			$data['status'] = '0';
    			$data['message'] = 'Error';
    		}
    	echo json_encode($data);
		die; 	
    } 

    public function saveGstVerification(){

    	if(!empty($this->request->get['credit_application_id'])){
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->saveGstVerification($this->request->get);
    	}
    	if($response){
    			$data['status'] = '1';
    			$data['message'] = "Gst Verification Added Successfully";
    		}else{
    			$data['status'] = '0';
    			$data['message'] = 'Error';
    		}
    	echo json_encode($data);
		die; 
    } 


    public function get_bank_details(){
    	$result = '';

    	if(!empty($this->request->get['customer_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->getCustomerBankDetail($this->request->get['customer_id']);

            if(!empty($response['bank_ac_number']))
            {
              $result = '<div class="bank_details_data"> 
              <table id="notesTable" style="width:100%">
                <tbody>';

    		   $result .= '<tr><td>Account Holder Name</td>';
    		   $result .= '<td>'. $response['bank_ac_holder_name'].'</td>';
    		   $result .= '</tr>';

    		   $result .= '<tr><td>Account Number</td>';
    		   $result .= '<td>'. $response['bank_ac_number'].'</td>';
    		   $result .= '</tr>';

    		   $result .= '<tr><td>IFSC Code</td>';
    		   $result .= '<td>'. $response['ifsc_code'].'</td>';
    		   $result .= '</tr>';
              
               $result .= '</table></div>';
            }
            else
            {
              $result = '<div class="bank_details_data"> 
              <table id="notesTable" style="width:100%">
                <tbody>';
               $result .= '<tr><td>No bank detail for this customer</td></tr>';
              $result .= '</table></div>';  
	
            }   

    	}
    	echo $result;
		die; 
    } 

    public function get_location_details(){
    	$result = '';

    	if(!empty($this->request->get['customer_id'])){
    		
    		$this->load->model('account/credit_application', 'frontend');
    		$response = $this->frontend_model_account_credit_application->getCustomerLocationCount($this->request->get['customer_id']);

    		$getShopLocationResponse = $this->frontend_model_account_credit_application->getCustomerLocationDetailInGroupByLocation($this->request->get['customer_id'],'shop');

    		if(!empty($getShopLocationResponse->num_rows) && $getShopLocationResponse->num_rows > 0){

    		$lat_long_count='0';
            foreach($getShopLocationResponse->rows as $key => $value){
               if($key!='0'){
                    $lat_diffrence = $this->frontend_model_account_credit_application->claculateLocationDiffrence($getShopLocationResponse->row['location_lat'],$value['location_lat']);
                    $lng_diffrence = $this->frontend_model_account_credit_application->claculateLocationDiffrence($getShopLocationResponse->row['location_lng'],$value['location_lng']);
                        if($lat_diffrence <= 0.003 && $lng_diffrence <= 0.003){
                          $lat_long_count = $lat_long_count + $value['lat_long_count'];
                        }
               }else{
                    $lat_long_count=$getShopLocationResponse->row['lat_long_count'];
               }     
            }
            if($lat_long_count > 2){
    			$result .= '<div class="bank_shop_mdetails_data" style="margin-bottom: 30px;"> 
    			<div><a style="font-weight: bold; font-size: 16px;">Shop Merge Near location</a></div> 
	              <table id="notesTable" style="width:100%">
	              <thead>
	                <tr>
	                  <td class="text-left"><a>Latitude</a></td>
	                  <td class="text-left"><a>Longitude</a></td>
	                  <td class="text-left"><a>Location timestamp</a></td>
	                  <td class="text-left"><a>Created</a></td>
	                  <td class="text-left"><a>Modified</a></td>
	                  <td class="text-left"><a>Count</a></td>
	                </tr>
	              </thead>
	              <tbody>';
	              	$result .= '<tr>';
	              	$result .= '<td>'.$getShopLocationResponse->row['location_lat'].'</td>';
	              	$result .= '<td>'.$getShopLocationResponse->row['location_lng'].'</td>';
	              	$result .= '<td>'.date('d-m-Y H:i:s l',$getShopLocationResponse->row['location_timestamp']).'</td>';
	              	$result .= '<td>'.$getShopLocationResponse->row['created'].'</td>';
	              	$result .= '<td>'.$getShopLocationResponse->row['modified'].'</td>';
	              	$result .= '<td>'.$lat_long_count.'</td>';
	              	$result .= '</tr>';
               $result .= '</table></div>';

            }else{
            $result .= '<div class="bank_shop_details_data" style="margin-bottom: 30px;"> 
    			<div><a style="font-weight: bold; font-size: 16px;">Shop location</a>  <a style="font-weight: bold; font-size: 14px; cursor: pointer; color:#ea2400;" class="view_shop_loc_table">View</a></div> 
	              <table style="width:100%; display:none;" class="shop_loc_table">
	              <thead>
	                <tr>
	                  <td class="text-left"><a>Latitude</a></td>
	                  <td class="text-left"><a>Longitude</a></td>
	                  <td class="text-left"><a>Location timestamp</a></td>
	                  <td class="text-left"><a>Created</a></td>
	                  <td class="text-left"><a>Modified</a></td>
	                  <td class="text-left"><a>Count</a></td>
	                </tr>
	              </thead>
	              <tbody>';
	              foreach ($getShopLocationResponse->rows as $key => $value) {
	              	$result .= '<tr>';
	              	$result .= '<td>'.$value['location_lat'].'</td>';
	              	$result .= '<td>'.$value['location_lng'].'</td>';
	              	$result .= '<td>'.date('d-m-Y H:i:s l',$value['location_timestamp']).'</td>';
	              	$result .= '<td>'.$value['created'].'</td>';
	              	$result .= '<td>'.$value['modified'].'</td>';
	              	$result .= '<td>'.$value['lat_long_count'].'</td>';
	              	$result .= '</tr>';
	              }
               $result .= '</table></div>';
            }

            }else{
            	$result .= '<div class="bank_details_data" style="margin-bottom: 30px;"> 
            	<div><a style="font-weight: bold; font-size: 16px;">Shop location</a></div> 
              <table id="notesTable" style="width:100%">
                <tbody>';
               $result .= '<tr><td>No location found between 12pm to 7pm for Shop</td></tr>';
              $result .= '</table></div>';  

            }



    		$getHomeLocationResponse = $this->frontend_model_account_credit_application->getCustomerLocationDetailInGroupByLocation($this->request->get['customer_id'],'home');

    		if(!empty($getHomeLocationResponse->num_rows) && $getHomeLocationResponse->num_rows > 0){
               $lat_long_count='0';
            foreach($getHomeLocationResponse->rows as $key => $value){
               if($key!='0'){
                    $lat_diffrence = $this->frontend_model_account_credit_application->claculateLocationDiffrence($getHomeLocationResponse->row['location_lat'],$value['location_lat']);
                    $lng_diffrence = $this->frontend_model_account_credit_application->claculateLocationDiffrence($getHomeLocationResponse->row['location_lng'],$value['location_lng']);
                        if($lat_diffrence <= 0.003 && $lng_diffrence <= 0.003){
                          $lat_long_count = $lat_long_count + $value['lat_long_count'];
                        }
               }else{
                    $lat_long_count=$getHomeLocationResponse->row['lat_long_count'];
               }     
            }
            if($lat_long_count > 2){
    			$result .= '<div class="bank_shop_mdetails_data" style="margin-bottom: 30px;"> 
    			<div><a style="font-weight: bold; font-size: 16px;">Home Merge Near location</a></div> 
	              <table id="notesTable" style="width:100%">
	              <thead>
	                <tr>
	                  <td class="text-left"><a>Latitude</a></td>
	                  <td class="text-left"><a>Longitude</a></td>
	                  <td class="text-left"><a>Location timestamp</a></td>
	                  <td class="text-left"><a>Created</a></td>
	                  <td class="text-left"><a>Modified</a></td>
	                  <td class="text-left"><a>Count</a></td>
	                </tr>
	              </thead>
	              <tbody>';
	              	$result .= '<tr>';
	              	$result .= '<td>'.$getHomeLocationResponse->row['location_lat'].'</td>';
	              	$result .= '<td>'.$getHomeLocationResponse->row['location_lng'].'</td>';
	              	$result .= '<td>'.date('d-m-Y H:i:s l',$getHomeLocationResponse->row['location_timestamp']).'</td>';
	              	$result .= '<td>'.$getHomeLocationResponse->row['created'].'</td>';
	              	$result .= '<td>'.$getHomeLocationResponse->row['modified'].'</td>';
	              	$result .= '<td>'.$lat_long_count.'</td>';
	              	$result .= '</tr>';
               $result .= '</table></div>';

            }else{
          $result .= '<div class="bank_home_details_data" style="margin-bottom: 30px;" >
    			<div><a style="font-weight: bold; font-size: 16px;">Home location</a> <a style="font-weight: bold; font-size: 14px; cursor: pointer; color:#ea2400;" class="view_home_loc_table">View</a></div> 
	              <table style="width:100% display:none;" class="home_loc_table">
	              <thead>
	                <tr>
	                  <td class="text-left"><a>Latitude</a></td>
	                  <td class="text-left"><a>Longitude</a></td>
	                  <td class="text-left"><a>Location timestamp</a></td>
	                  <td class="text-left"><a>Created</a></td>
	                  <td class="text-left"><a>Modified</a></td>
	                  <td class="text-left"><a>Count</a></td>
	                </tr>
	              </thead>
	              <tbody>';
	              foreach ($getHomeLocationResponse->rows as $key => $value) {
	              	$result .= '<tr>';
	              	$result .= '<td>'.$value['location_lat'].'</td>';
	              	$result .= '<td>'.$value['location_lng'].'</td>';
	              	$result .= '<td>'.date('d-m-Y H:i:s l',$value['location_timestamp']).'</td>';
	              	$result .= '<td>'.$value['created'].'</td>';
	              	$result .= '<td>'.$value['modified'].'</td>';
	              	$result .= '<td>'.$value['lat_long_count'].'</td>';
	              	$result .= '</tr>';
	              }
               $result .= '</table></div>';
            }

    		}else{

    		$result .= '<div class="bank_details_data" style="margin-bottom: 30px;"> 
            	<div><a style="font-weight: bold; font-size: 16px;">Home location</a></div> 
              <table id="notesTable" style="width:100%">
                <tbody>';
               $result .= '<tr><td>No location found between 12am to 6am for home</td></tr>';
              $result .= '</table></div>';  	
    		}

    		
            if(!empty($response['results']))
            {
              $result .= '<div class="bank_details_data" style="margin-bottom: 30px;"> 
              <table id="notesTable" style="width:100%">
              <thead>
                <tr>
                  <td class="text-left"><a>Latitude</a></td>
                  <td class="text-left"><a>Longitude</a></td>
                  <td class="text-left"><a>Location timestamp</a></td>
                  <td class="text-left"><a>Created</a></td>
                  <td class="text-left"><a>Modified</a></td>
                </tr>
              </thead>
              <tbody>';
              foreach ($response['results'] as $key => $value) {
              	$result .= '<tr>';
              	$result .= '<td>'.$value['location_lat'].'</td>';
              	$result .= '<td>'.$value['location_lng'].'</td>';
              	$result .= '<td>'.date('d-m-Y H:i:s l',$value['location_timestamp']).'</td>';
              	$result .= '<td>'.$value['created'].'</td>';
              	$result .= '<td>'.$value['modified'].'</td>';
              	$result .= '</tr>';
              }
               $result .= '</table></div>';
            }
            else
            {
              $result = '<div class="bank_details_data"> 
              <table id="notesTable" style="width:100%">
                <tbody>';
               $result .= '<tr><td>No location detail for this customer</td></tr>';
              $result .= '</table></div>';  
	
            }   

    	}
    	echo $result;
		die; 
    } 


    
    

    public function sendNotificationToCustomer() {

        $customer_id = $this->request->post['credit_application_customer_id'];
        $application_form_id = $this->request->post['credit_application_form_id'];
        $notification_message = $this->request->post['notification_message'];

        $this->load->model('sale/customer');
        $customer_info = $this->model_sale_customer->getCustomerById($customer_id, array('ws_gcm_registration_id'));

        $response_array = array();
        if (empty($customer_info['ws_gcm_registration_id'])) {
            $response_array['status'] = 0;
            $response_array['message'] = 'Customer is not using Android app';
            echo json_encode($response_array);
            die;
        }

        $support_no = '7232064505';

        $pnData = array();
        $pnData['msg_type'] = '1';
        $pnData['message'] = $notification_message;
        $pnData['title'] = 'Credit Application Notification';
        $pnData['tickerText'] = 'Wholesalebox';
        $pnData['vibrate'] = 1;
        $pnData['sound'] = 1;
        $pnData['show_chat'] = 1;
        $pnData['show_call'] = 1;
        $pnData['mobile'] = $support_no;

        $userData['0']['type'] = 'customer';
        $userData['0']['id'] = $customer_id;
        $userData['0']['is_pn_to_send'] = true;
        $userData['0']['pn'] = $pnData;


        $response_array['status'] = 1;
        $response_array['message'] = 'Notification Successfully sent..';



        $objDateTime = new DateTime();
        $apiData['notification_sending_time'] = $objDateTime->format('Y-m-d 10:00:00');
        $apiData['type'] = 'instant';
        $apiData['data'] = $userData;

        $jsonData = json_encode($apiData);
        if (strtolower(SITE_ENVIRONMENT) == 'production') {
            $url = 'https://www.wholesalebox.biz/crmapi/Notifications/sendNotificationFromWeb';
        } else {
            $url = 'https://www.wholesalebox.biz/staging/crmapi/Notifications/sendNotificationFromWeb';
        }

        $curl = curl_init();
        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($curl);
        curl_close($curl);

        $this->load->model('sale/customer_credit_application');
        $this->model_sale_customer_credit_application->saveCreditNotificationLogs($application_form_id, $notification_message);

        $response_array['status'] = 1;
        $response_array['message'] = 'Notification Successfully sent..';
        echo json_encode($response_array);
        die;
    }

    public function getCreditApplicationSentNotification(){
        
    	$result = '';

    	if(!empty($this->request->get['credit_application_id'])){
    		
    		$this->load->model('sale/customer_credit_application');
    		$response = $this->model_sale_customer_credit_application->getCreditApplicationNotificationLog($this->request->get['credit_application_id']);
            
            if(count($response) > 0)
            {
              $result = '<div class="document_status_data"> <table id="notesTable" style="width:100%">
                <tbody><tr>
                    <th>Notification</th>
                    <th>Date</th>
                    <th>Username</th> </tr> ';	
            }  
          
    		foreach($response as $note)
    		{
    		   $result .= '<tr>';
               $result .= '<td>'.$note['remark'].'</td>';
               $result .= '<td>'.$note['date_added'].'</td>';
               if($note['user_id'] == 0)
               {
                $result .= '<td>System Update</td>';
               }
               else
               {
               	$result .= '<td>'.$note['username'].'</td>';
               }
               
               $result .= '</tr>';
    		}

    		if(count($response) > 0)
            {
            	$result .= '</tbody></table></div>';	
            }
             
          

    	}
    	echo $result;
		die; 
    }

    /**
     * Get All Bank Names Having Bank Statement
     * @param customer_id
     * @author Manoj Singh Rajpurohit, May 2019
     */
    public function getAllBanksHavingStatements()
    {
        $result = '';

        if(!empty($this->request->get['customer_id'])){

            $this->load->model('sale/customer_credit_application');
            $get_all_banks = $this->model_sale_customer_credit_application->allBanksNamesHavingStatement($this->request->get['customer_id']);

            if(!empty($get_all_banks)){

                $result .= '<div id="tabs" >	
							<ul id="myStatementTab"  class="nav nav-tabs">';

                $j=0;
                foreach($get_all_banks as $val)
                {
                    $tab_class = '';
                    if($j==0){
                        $tab_class = 'active';
                    }

                    $result .= '<li  class="'.$tab_class.' sms_bank_statement nav-item">
											<a href="#t'.$j.'" data-toggle="tab" 
											data-id="'.$this->request->get['customer_id'].'" 
											data-bank_name="'.str_replace(' ','_',$val['senderName']).'" 
											>'.$val['senderName'].'</a></li>';
                    $j++;
                }
                $result .= '</ul>';
                $result .='<div class="tab-content clearfix">';

                $k=0;
                foreach($get_all_banks as $val)
                {
                    $result .='<div class="tab-pane active" id="t'.$k.'"></div>';
                    $k++;
                }

                $result .= '</div></div>';
            }
        } else {
            $result .='<div class="row" style="padding-left:0px;">
                        <div class="col-sm-12" style="color:RED; min-height:50px; border:1px solid; padding:20px 0px 0px 10px;">Record Not Found</div>
                       </dic>';
        }

        echo $result;
        die;
    }


    /**
     * Get SMS log POS and Credit
     * @param customer_id
     * @author Manoj Singh Rajpurohit, May 2019
     */
    public function getAllBanksNameFromSmsLog(){

        $result = '';

        if(!empty($this->request->get['customer_id'])){

            $this->load->model('sale/customer_credit_application');
            $CustomerNachObj = new CustomerNachDetails($this->registry);
            $customer_nach_details = $CustomerNachObj->getCustomerNachDetails($this->request->get['customer_id']);
            $edit_bank_details = $this->url->link('sale/customer_credits', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'], 'SSL');
            $result .= $this->load->view('sale/customer_nach_details_on_banksms_popup.tpl',
                array(
                    'customer_nach_details' => $customer_nach_details,
                    'edit_bank_details' => $edit_bank_details
                )
            );

            $result .= '<div><div class="radio-inline">
						  <label class="text-primary"><input type="radio" data-id="'.$this->request->get['customer_id'].'" name="radio_bank_sms_log" id="radio_all_bank_sms" value="all_bank_sms" checked> SMS</label>
						</div>
						<div class="radio-inline">
						  <label class="text-primary"><input type="radio" data-id="'.$this->request->get['customer_id'].'" name="radio_bank_sms_log" id="radio_bank_statement" value="bank_statement_sms"> Statement</label>
						</div></div><br/>';

            $get_all_banks = $this->model_sale_customer_credit_application->getAllBanksFromSmsLog($this->request->get['customer_id']);

            $result .= '<div id="data_container" >';
            if(!empty($get_all_banks)){

                $result .= '<div id="tabs" >	
							<ul id="myTab"  class="nav nav-tabs">';

                $j=0;
                foreach($get_all_banks as $val)
                {
                    $tab_class = '';
                    if($j==0){
                        $tab_class = 'active';
                    }

                    $result .= '<li  class="'.$tab_class.' sms_bank_name nav-item">
											<a href="#t'.$j.'" data-toggle="tab" 
											data-id="'.$this->request->get['customer_id'].'" 
											data-bank_name="'.$val['senderName'].'" 
											>'.$val['senderName'].'</a></li>';
                    $j++;
                }
                $result .= '</ul>';

                $result .='<div class="tab-content clearfix">';
                $k=0;
                foreach($get_all_banks as $val)
                {
                    $result .='<div class="tab-pane active" id="t'.$k.'"></div>';
                    $k++;
                }

                $result .= '</div></div></div>';
            }

        }
        echo $result;
        die;
    }

    /**
     * Get SMS log POS and Credit
     * @param customer_id
     * @author Manoj Singh Rajpurohit, Feb 2019
     */
    public function readSmsLog(){

        $result = '';

        if(!empty($this->request->get['customer_id'])){

            $this->load->model('sale/customer_credit_application');

            

            if(!empty($this->request->get['page']))
            {
              $page = $this->request->get['page']; 	
            }
            else
            {
              $page = 0;
            }
            $limit = 1500;
            $start = $limit*$page;

            if($page == 0)
            {
                if(isset($this->request->get['type']) && $this->request->get['type'] != "account") {
                    $CustomerNachObj = new CustomerNachDetails($this->registry);
                    $customer_nach_details = $CustomerNachObj->getCustomerNachDetails($this->request->get['customer_id']);
                    $edit_bank_details = $this->url->link('sale/customer_credits', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'], 'SSL');
                    $result = $this->load->view('sale/customer_nach_details_on_banksms_popup.tpl',
                        array(
                            'customer_nach_details' => $customer_nach_details,
                            'edit_bank_details' => $edit_bank_details
                        )
                    );
                }
            }

            $bank_name = $this->request->get['bank']??'';
            $response = $this->model_sale_customer_credit_application->getCustomerSmsLog($this->request->get['customer_id'], $this->request->get['type'], $start, $limit, $bank_name);

            if(count($response) > 0)
            {
               if($page == 0)
               {
                   $_SESSION['criteria_sms_sr_no'] = 1;
                   $result .= '<div class="text_sms_data"> <table id="smsTable" style="width:100%">
                  <tbody><tr>
                    <th>(#)</th>
                    <th>Message</th>
                    <th>Sender Name</th>
                    <th>Transaction Type</th>
                    <th>Date Received</th> </tr>';
                }    

                foreach($response as $text_sms)
                {
                    $result .= '<tr>';
                    $result .= '<td style="width:2%">'.$_SESSION['criteria_sms_sr_no']++.'</td>';
                    $result .= '<td style="width:50%">'.$text_sms['message'].'</td>';
                    $result .= '<td>'.$text_sms['sender_name'].'</td>';
                    $result .= '<td>'.$text_sms['txn_type'].'</td>';
                    $result .= '<td>'.date('d-m-Y',strtotime($text_sms['msg_original_date'])).'</td>';
                    $result .= '</tr>';
                }
                 
                if($page == 0)
               { 
                $result .= '</tbody></table>';
               } 

                 if($page == 0 && count($response) == $limit)
                 { 
                   $result .= '<br />';
                   $result .= '<div class="more_div"><a class="more_btn" data-customer_id="'.$this->request->get['customer_id'].'" data-type="'.$this->request->get['type'].'">Show More</a>';
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
        echo $result;
        die;
    }


    public function readBankStatementSmsLog(){

        $result = '';

        if(!empty($this->request->get['customer_id'])){

            $this->load->model('sale/customer_credit_application');

            $bank_name = $this->request->get['bank']??'';
            $response = $this->model_sale_customer_credit_application->getCustomerAccountFromBankStatement($this->request->get['customer_id'], str_replace('_',' ',$bank_name));

            if(count($response) > 0)
            {
                $result .= '<div class="btn-group"  id="btn_grp_bank_'.$bank_name.'" data-toggle="buttons">Account Numbers : <br/>';
                $k=0;
                foreach($response as $bank_ac_no)
                {


                    $btnClass = '';
                    $checked=  '';
                    $result .= '<label class="btn btn-primary '.$btnClass.'">
									<input type="radio" data-id="'.$this->request->get['customer_id'].'" class="bank_ac_no_smt" name="options" data-bank_name="'.$bank_name.'" data-ac_no="'.$bank_ac_no.'"  > '.$bank_ac_no.'
								 </label>';
                    $k++;
                }
                $result .= '</div> <br/>
                <div id="show_bank_smt_'.$bank_name.'"></div>
                ';

            } else {

                $result  = "<span style='color:RED;'>No Record Found!!!</span>";
            }
        }
        echo $result;
        die;
    }



    public function getBankAccountStatement(){

        $result = '';

        if(!empty($this->request->get['customer_id'])){

            $this->load->model('sale/customer_credit_application');

            if(!empty($this->request->get['page']))
            {
                $page = $this->request->get['page'];
            }
            else
            {
                $page = 0;
            }
            $limit = 1500;
            $start = $limit*$page;

            $response = $this->model_sale_customer_credit_application->getCompleteBankAccountStatement($this->request->get['customer_id'], $this->request->get['ac_no'], $start, $limit);

            $rs_bal_smt = $this->model_sale_customer_credit_application->getLastAndMonthlyAvailBalance($this->request->get['customer_id'], $this->request->get['ac_no'], $this->request->get['bank_name']);

            $result .= '<table class="table table-striped" style="border-top:1px solid #e0e0e0"><thead>';
            if(isset($rs_bal_smt['last_avail_bal']) && count($rs_bal_smt['last_avail_bal']) > 0)
            {
                $result .= '<br/><tr>
                                  <td scope="row">Last Available Balance <strong class="text-primary">Rs.'.$rs_bal_smt['last_avail_bal']['balance'].'</strong> on date '.DATE('d-m-Y',strtotime($rs_bal_smt['last_avail_bal']['last_available_balance_date'])).'</td>                                                                  
                                </tr>';
            } else {
                $result .= '<br/><tr>
								  <td scope="row" style="color:RED;">Last Available Balance Not Found</td>
								</tr>';
            }
            $result .= '</thead></table>';

            $result .= '<table class="table table-striped" style="border-top:1px solid #e0e0e0">								  
								  <thead>
									<tr>
									  <th scope="col">Month - Year</th>
									  <th scope="col">Last Available Balance</th>
									  <th scope="col">Balance Date</th>  
									  <th scope="col">Credit Msg Count</th>
									  <th scope="col">Total Credit Amount</th>                                 
									</tr>
								  </thead>
								  <tbody>';
            if((isset($rs_bal_smt['monthly_avail_bal']) &&  count($rs_bal_smt['monthly_avail_bal']) > 0))
            {
                foreach($rs_bal_smt['monthly_avail_bal'] as $mab){
                    $tot_credit_sms = $response['credit'][$mab['year'].'-'.$mab['month']]['tot_credit_sms']??0;
                    $tot_credit_amount = $response['credit'][$mab['year'].'-'.$mab['month']]['tot_credit_amount']??0;

                    $result .= '<tr>
								  <td>'.$mab['month'].'-'.$mab['year'].'</td>
								  <td>'.$mab['mab'].'</td> 
								  <td>'.DATE('d-m-Y',strtotime($mab['last_message_date'])).'</td> 
								  <td>'. $tot_credit_sms .'</td>  
								  <td>'. $tot_credit_amount .'</td>                         
								</tr>';
                }
            } elseif(empty($rs_bal_smt['monthly_avail_bal']) && !empty($response['credit'])){

                foreach($response['credit'] as $key => $credit_val){
                    $result .= '<tr>
								  <td>'.$key.'</td>
								  <td style="color:RED;">NA</td> 
								  <td style="color:RED;">NA</td> 
								  <td>'. $credit_val['tot_credit_sms'] .'</td>  
								  <td>'. number_format($credit_val['tot_credit_amount'],2) .'</td>                         
								</tr>';
                }

            } else {
                $result .= '<tr> 
								  <td colspan="5" style="color:RED;">Not Available</td>								                        
								</tr>';
            }
            $result .= '</tbody></table>';




            if(count($response['all_sms']) > 0)
            {
                if($page == 0)
                {
                    $_SESSION['smt_sms_sr_no'] = 1;
                    $result .= '<div class="text_sms_data"> <table id="smsTable" style="width:100%">
                  <tbody><tr>
                    <th>(#)</th>
                    <th>Txn Date</th> 
                    <th>Debit</th>
                    <th>Credit</th>  
                    <th>Balance</th>
                    <th>Others</th>                    
                    <th>Message</th>                    
                    </tr>';
                }

                foreach($response['all_sms'] as $text_sms)
                {
                    if($text_sms['txn_type'] == "debit")
                        $rowColor = 'bgcolor="#e2b5c1"';
                    else if($text_sms['txn_type'] == "credit")
                        $rowColor = 'bgcolor="#a3d0a7"';
                    else if($text_sms['txn_type'] == "balance")
                        $rowColor = 'bgcolor="#e6d784"';
                    else
                        $rowColor = '';

                    $result .= '<tr '.$rowColor.'>';
                    $result .= '<td style="width:2%">'.$_SESSION['smt_sms_sr_no']++.'</td>';
                    $result .= '<td style="width:10%">'.date('d-m-Y',strtotime($text_sms['txn_date'])).'</td>';

                    if($text_sms['txn_type'] == "debit"){

                        $result .= '<td style="width:10%">'.$text_sms['amount'] ?? ''.'</td>';
                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:15%">'.$text_sms['balance'] ?? '' .'</td>';
                        $result .= '<td style="width:10%"></td>';

                    } else if($text_sms['txn_type'] == "credit"){

                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:10%">'.$text_sms['amount'] ?? ''.'</td>';
                        $result .= '<td style="width:15%">'.$text_sms['balance'] ?? '' .'</td>';
                        $result .= '<td style="width:10%"></td>';

                    } else if($text_sms['txn_type'] == "balance"){

                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:15%">'.$text_sms['balance'] ?? '' .'</td>';
                        $result .= '<td style="width:10%"></td>';

                    } else if(empty($text_sms['txn_type'])){

                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:15%">'.$text_sms['balance'] ?? '' .'</td>';
                        $result .= '<td style="width:10%">'.$text_sms['amount'] ?? ''.'</td>';

                    }else{

                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:10%"></td>';
                        $result .= '<td style="width:15%"></td>';
                        $result .= '<td style="width:10%"></td>';
                    }

                    $result .= '<td style="width:30%">'.$text_sms['original_msg'].'</td>';
                    $result .= '</tr>';
                }

                if($page == 0)
                {
                    $result .= '</tbody></table>';
                }

                if($page == 0 && count($response['all_sms']) == $limit)
                {
                    $result .= '<br />';
                    $result .= '<div class="more_div"><a class="more_btn" data-customer_id="'.$this->request->get['customer_id'].'" data-ac_no="'.$this->request->get['ac_no'].'">Show More</a>';
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
        echo $result;
        die;
    }


    /**
     * Sending mails
     * @param $data array of mail details
     * @author Mahaveer, 2019
     */
    public function sendMailNewNote($subject, $html) 
    {
        if (method_exists($this, 'get')) {
            $config = $this->get('config');
        } else {
            $config = $this->config;
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom($config->get('config_mail_smtp_username'), 'Wholesale Box');
	    $mail->addReplyTo(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addAddress(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
    }

   public function sendMailLeadTL($subject, $html, $toAddress)
    {
        if (method_exists($this, 'get')) {
            $config = $this->get('config');
        } else {
            $config = $this->config;
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addReplyTo(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addAddress($toAddress['email'], $toAddress['name']);
        $mail->addBCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addBCC(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
    }

    /*
	* Protected method to reset cohort data for sales data 
	* @param : array cohort analysis data
	* @return: array
	* @author: MSA March 2019
    */
    protected function resetCohortAnalysisData(array $data)
    {
    	$sales_data = array();
   		$sales_data['date_of_first_order'] = $data[0]['Acq. Month'] ?? '';
   		$sales_data['M0'] = $data[0]['+0 M'] ?? '0.00';
   		$sales_data['M1'] = $data[0]['+1 M'] ?? '0.00';
   		$sales_data['M2'] = $data[0]['+2 M'] ?? '0.00';
   		$sales_data['M3'] = $data[0]['+3 M'] ?? '0.00';
   		$sales_data['M4'] = $data[0]['+4 M'] ?? '0.00';
   		$sales_data['M5'] = $data[0]['+5 M'] ?? '0.00';
   		$sales_data['M6'] = $data[0]['+6 M'] ?? '0.00';
   		$sales_data['M7'] = $data[0]['+7 M'] ?? '0.00';
   		$sales_data['M8'] = $data[0]['+8 M'] ?? '0.00';
   		$sales_data['M9'] = $data[0]['+9 M'] ?? '0.00';
   		$sales_data['M10'] = $data[0]['+10 M'] ?? '0.00';
   		$sales_data['M11'] = $data[0]['+11 M'] ?? '0.00';
   		$sales_data['M12'] = $data[0]['+12 M'] ?? '0.00';
   		return $sales_data;
    }

    /*
	* Public method to get customer credit pre approved data
	* @param : int customer id
	* @param : int credit application id
	* @return: string
	* @author: MSA March 2019
    */
    public function getPreApprovedCreditData()
    {
    	$this->load->model('sale/customer_credit_application');
    	$this->load->model('report/cohort');
    	$customer_id = $this->request->get['customer_id'];
    	$credit_application_id = $this->request->get['credit_application_id'];
    	$customer_credit_data = $this->model_sale_customer_credit_application->getCreditApplicationData((int)$credit_application_id);
    	$cohort_data = $this->model_report_cohort
    								->getCohortData(
	    											'order_total',
	    											array('num_intervals' => 12),
	    											array(),
	    											array(),
	    											array('consider_master_id' => $customer_id)
    											);
    	$customer_sales_data = $this->resetCohortAnalysisData($cohort_data);
    	$user_documents = $this->model_sale_customer_credit_application->getKycDocuments((int)$credit_application_id);
    	
    	if(!empty($customer_credit_data)) {
    		
    		$customer_data 			= $customer_credit_data['customer'];
    		$customer_business_data = $customer_credit_data['businessDetails'];
    		//Shop address data 
    		$residentialAddress     = $customer_data['residentialAddress'];


    		$customerNameAsOnPanNumber = $customer_data['firstName']; 
    		if(!empty($customer_data['middleName'])) {
    			$customerNameAsOnPanNumber .= ' ' . $customer_data['middleName'];
    		}
    		if(!empty($customer_data['lastName'])) {
    			$customerNameAsOnPanNumber .= ' ' . $customer_data['lastName'];
    		}
    		$residence_address = $customer_data['permanentAddress']['line1'] ?? '';
    		if(!empty($customer_data['permanentAddress']['line2'])){
				$residence_address .= ', ' . $customer_data['permanentAddress']['line2'];
			}
			if(!empty($customer_data['permanentAddress']['city'])){
				$residence_address .= ', ' . $customer_data['permanentAddress']['city'];
			}
			if(!empty($customer_data['permanentAddress']['district'])){
				$residence_address .= ', ' . $customer_data['permanentAddress']['district'];
			}
			if(!empty($customer_data['permanentAddress']['postcode'])){
				$residence_address .= ', ' . $customer_data['permanentAddress']['postcode'];
			}
			if(!empty($customer_data['permanentAddress']['country'])){
				$residence_address .= ', ' . $customer_data['permanentAddress']['country'];
			}
			
			$customer_delivery_address = $this->model_sale_customer_credit_application->isCustomerLastDeliveredOrderAddress($customer_id);
			$is_delivery_address = 1;
			if(!empty($customer_delivery_address)){
				$delivery_address_line_1	= $customer_delivery_address['shipping_address_1'];
				$delivery_address_line_2	= $customer_delivery_address['shipping_address_2'];
				$delivery_address_city		= $customer_delivery_address['shipping_city'];
				$delivery_address_state		= $customer_delivery_address['shipping_zone'];
				$delivery_address_postcode	= $customer_delivery_address['shipping_postcode'];
			}else{
				$delivery_address_line_1	= $residentialAddress['line1'] ?? '';
				$delivery_address_line_2	= $residentialAddress['line2'] ?? '';
				$delivery_address_city		= $residentialAddress['city'] ?? '';
				$delivery_address_state		= $residentialAddress['state'] ?? '';
				$delivery_address_postcode	= $residentialAddress['postcode'] ?? '';
				$is_delivery_address = 0;
			}

		//get customer first successful order date	
			$date_of_first_order = $this->model_sale_customer_credit_application->getCustomerFirstDeliveredOrderDate($customer_id);
			if(!empty(trim($date_of_first_order))) {
				$date_of_first_order = date('m/d/Y',strtotime($date_of_first_order));
			}

			$pan_card_image = $user_documents['personalPan'] ?? 'placeholder.jpg';
			
			$annual_turnover = 0;
			if(!empty(trim($customer_business_data['annual_turnover']))){
				$annual_turnover = $customer_business_data['annual_turnover'];
			}

			$data = array(
					'customer_id' 				=> $customer_id,
					'name_as_on_pan_number' 	=> $customerNameAsOnPanNumber,
					'dob'						=> $customer_data['dob'] ?? '',
					'pan_number' 				=> $customer_data['panNumber'] ?? '',
					'image'						=> STATIC_CONTENT_URL_SSL.$pan_card_image,
					'gender' 					=> $customer_data['gender'] ?? '',
					'telephone_number' 			=> $customer_data['telephoneNumber'] ?? '',
					'residence_address' 		=> $residence_address,
					'residence_postcode' 		=> $customer_data['residentialAddress']['postcode'] ?? '',
					'email_address' 			=> $customer_data['emailAddress'] ?? '',
					'company_name' 				=> $customer_business_data['companyName'] ?? '',
					'delivery_address_line_1' 	=> $delivery_address_line_1,
					'delivery_address_line_2' 	=> $delivery_address_line_2,
					'delivery_address_city' 	=> $delivery_address_city,
					'delivery_address_state' 	=> $delivery_address_state,
					'delivery_address_postcode' => $delivery_address_postcode,
					'annual_turnover'			=> $annual_turnover,
					'business_vintage'			=> $customer_business_data['business_vintage'] ?? '',
					'date_of_first_order'		=> $date_of_first_order ?? '',
					'M0' 						=> $customer_sales_data['M0'],
					'M1' 						=> $customer_sales_data['M1'],
					'M2' 						=> $customer_sales_data['M2'],
					'M3' 						=> $customer_sales_data['M3'],
					'M4' 						=> $customer_sales_data['M4'],
					'M5' 						=> $customer_sales_data['M5'],
					'M6' 						=> $customer_sales_data['M6'],
					'M7' 						=> $customer_sales_data['M7'],
					'M8' 						=> $customer_sales_data['M8'],
					'M9' 						=> $customer_sales_data['M9'],
					'M10' 						=> $customer_sales_data['M10'],
					'M11' 						=> $customer_sales_data['M11'],
					'M12' 						=> $customer_sales_data['M12'],
			);

			$data['rbl_status']	= $this->model_sale_customer_credit_application->getCustomerApplicationRBLStatus((int)$customer_id);
			$data['is_delivery_address'] = $is_delivery_address;

			$_html =  $this->load->view('sale/rbl_pre_approval_data.tpl', $data);

    	}else{

    		$_html = '<table width="100%">';
    			$_html .= '<tr>';
    				$_html .= '<td>No credit application data found!!</td>';
    			$_html .= '</tr>';
    		$_html .= '</table>';
    	}

    	echo $_html;

    }

    public function format_string(string $subject)
    {
    	return  preg_replace( "/<br>|\r|\n|,/", "", $subject ); 
    }
    
    /*
	* Public method to send customer credit pre approved data for RBL process
	* @param : int customer id
	* @param : int credit application id
	* @return: string
	* @author: MSA March 2019
    */
    public function sendPreApprovedCreditData()
    {
    	$this->load->model('sale/customer_credit_application');
    	$this->load->model('report/cohort');

    	$customer_id = $this->request->get['customer_id'];
    	$credit_application_id = $this->request->get['credit_application_id'];

    	$customer_credit_data = $this->model_sale_customer_credit_application->getCreditApplicationData($credit_application_id);
    	$customer_id = $customer_credit_data['customer']['customer_id'];

    	$isCustomerAlreadyProcessedForRBL = $this->model_sale_customer_credit_application->isCustomerProcessedForRBL($customer_id);
    	if($isCustomerAlreadyProcessedForRBL) {
    		$response['error'] = 'Customer already sent for RBL process.';
    		echo json_encode($response);
    		exit;
    	}

    	$cohort_data = $this->model_report_cohort
    								->getCohortData(
	    											'order_total',
	    											array('num_intervals' => 12),
	    											array(),
	    											array(),
	    											array('consider_master_id' => $customer_id)
    											);
    	$customer_sales_data = $this->resetCohortAnalysisData($cohort_data);
    	$response = array();
    	
    	if(!empty($customer_credit_data)) 
    	{
    		$customer_data 			= $customer_credit_data['customer'];
    		$customer_business_data = $customer_credit_data['businessDetails'];
    		$customerNameAsOnPanNumber = $customer_data['firstName'] ?? ''; 
    		if(!empty($customer_data['middleName'])) {
    			$customerNameAsOnPanNumber .= ' ' . $customer_data['middleName'];
    		}
    		if(!empty($customer_data['lastName'])) {
    			$customerNameAsOnPanNumber .= ' ' . $customer_data['lastName'];
    		}
    		$residence_address = $customer_data['permanentAddress']['line1'] ?? '';
    		if(!empty($customer_data['permanentAddress']['line2'])){
				$residence_address .= ' ' . $customer_data['permanentAddress']['line2'];
			}
			if(!empty($customer_data['permanentAddress']['city'])){
				$residence_address .= ' ' . $customer_data['permanentAddress']['city'];
			}
			if(!empty($customer_data['permanentAddress']['district'])){
				$residence_address .= ' ' . $customer_data['permanentAddress']['district'];
			}
			if(!empty($customer_data['permanentAddress']['postcode'])){
				$residence_address .= ' ' . $customer_data['permanentAddress']['postcode'];
			}
			if(!empty($customer_data['permanentAddress']['country'])){
				$residence_address .= ' ' . $customer_data['permanentAddress']['country'];
			}
			$residence_address = $this->format_string($residence_address);
    		
			$customer_telephone_number = $customer_data['telephoneNumber'] ?? '';
			$customer_telephone_number = $this->validate_mobile_number($customer_telephone_number);
	    	$browser   		= $customer_data['browser'] ?? 'Unknown Browser';
	    	$ip   			= $customer_data['ip'] ?? '';
	    	
	    	$customer_delivery_address = $this->model_sale_customer_credit_application->isCustomerLastDeliveredOrderAddress($customer_id);
			$delivery_address_landmark = '';

			if(!empty($customer_delivery_address)){
				$delivery_address_line_1	= $customer_delivery_address['shipping_address_1'];
				$delivery_address_line_2	= $customer_delivery_address['shipping_address_2'];
				$delivery_address_city		= $customer_delivery_address['shipping_city'];
				$delivery_address_state		= $customer_delivery_address['shipping_zone'];
				$delivery_address_postcode	= $customer_delivery_address['shipping_postcode'];
			}else{
				//Shop address data 
				$delivery_address_line_1	= $customer_data['residentialAddress']['line1'] ?? '';
				$delivery_address_line_2	= $customer_data['residentialAddress']['line2'] ?? '';
				$delivery_address_city		= $customer_data['residentialAddress']['city'] ?? '';
				$delivery_address_state		= $customer_data['residentialAddress']['state'] ?? '';
				$delivery_address_postcode	= $customer_data['residentialAddress']['postcode'] ?? '';
			}

			if(empty(trim($delivery_address_line_2))) {
				$delivery_address_line_2 = 	$delivery_address_city;
			}

			if(!empty(trim($customer_data['dob']))) {
				$customer_dob = date('m/d/Y',strtotime($customer_data['dob']));
			}else{
				$customer_dob = "";
			}

			if(!empty(trim($customer_data['csv_date_timing']))) {
				$csv_date_timing = date('m/d/Y h:i',strtotime($customer_data['csv_date_timing']));
			}else{
				$csv_date_timing = "";
			}

			if(!empty(trim($customer_data['csv_date_register_with_anchor']))) {
				$csv_date_register_with_anchor = date('m/d/Y',strtotime($customer_data['csv_date_register_with_anchor']));
			}else{
				$csv_date_register_with_anchor = "";
			}

		//get customer first successful order date	
			$date_of_first_order = $this->model_sale_customer_credit_application->getCustomerFirstDeliveredOrderDate($customer_id);
			if(!empty(trim($date_of_first_order))) {
				$date_of_first_order = date('m/d/Y',strtotime($date_of_first_order));
			}


	    	$row_data = array(
	    						date('m/d/Y'),
	    						'WHOLESALEBOX',
	    						RBL_API_ANCHORID,
	    						$customer_id ?? '',
	    						$customerNameAsOnPanNumber ?? '',
	    						$customer_dob,
	    						trim($customer_data['panNumber']) ?? '',
	    						trim($customer_data['gender']) ?? '',
	    						trim($customer_telephone_number),
	    						$residence_address ?? '',
	    						trim($customer_data['residentialAddress']['postcode']) ?? '',
	    						trim($customer_data['emailAddress']) ?? '',
	    						trim($this->format_string($customer_business_data['companyName'])) ?? '',
	    					//Delivery Address
	    						trim($this->format_string($delivery_address_line_1)),
	    						trim($this->format_string($delivery_address_line_2)),
	    						$delivery_address_landmark,
	    						trim($this->format_string($delivery_address_city)),
	    						trim($this->format_string($delivery_address_state)),
	    						trim($delivery_address_postcode),
	    					//Delivery Address
	    						(int)(trim($customer_business_data['annual_turnover'])!='') ? $customer_business_data['annual_turnover'] :'0',
	    						trim($customer_business_data['business_vintage']) ?? '',
	    						$date_of_first_order,
	    						'',
	    						$customer_sales_data['M0'] ?? '0.00',
	    						$customer_sales_data['M1'] ?? '0.00',
	    						$customer_sales_data['M2'] ?? '0.00',
	    						$customer_sales_data['M3'] ?? '0.00',
	    						$customer_sales_data['M4'] ?? '0.00',
	    						$customer_sales_data['M5'] ?? '0.00',
	    						$customer_sales_data['M6'] ?? '0.00',
	    						$customer_sales_data['M7'] ?? '0.00',
	    						$customer_sales_data['M8'] ?? '0.00',
	    						$customer_sales_data['M9'] ?? '0.00',
	    						$customer_sales_data['M10'] ?? '0.00',
	    						$customer_sales_data['M11'] ?? '0.00',
	    						$customer_sales_data['M12'] ?? '0.00',
	    						'CONSENT',
	    						$browser,
	    						$ip,
	    						$csv_date_timing,
	    						$csv_date_register_with_anchor
	    					);

	    	try {

	    		$validation_errors = $this->isValidPreOnBoardingData($row_data);

	    		if(empty($validation_errors['error'])) {

	    			$obj = new MYSFTP();

		    		if( $obj->put( RBL_CSV_FILES['PRE_APPROVAL_REQUEST'], $row_data ) ) {
					    
					    $this->updateCustomerDataForRBLPreApprovalProcess($credit_application_id, $row_data);

						$response = array('success'=>'Customer data send to RBL for Credit Pre-Approval process');
						
		    		}else{

		    			$response['error'] = 'Error to write csv file data over RBL Server';
		    		}

	    		}else{

	    			$response['error'] = $validation_errors['error'];

	    		}

	    	}catch(Exception $e) {

	    		$response['error'] = $e->getMessage();
	    	}

		}else {
			$response['error'] = 'No Data found to send for RBL process';
		}
    	
    	echo json_encode($response);
    }

    /*
	* Protected method to validate PreOnBoarding data before to send to RBL
	* @param : array $data
	* @return: array $response
	* @author: MSA August 2019
    */
    protected function isValidPreOnBoardingData( array $data ): array
    {
    	$response = array();
    	
    	$error = '';

    #validate anchor id - RBL_API_ANCHORID [config setting]
    	if(empty($data[2])) { $error .= 'Anchor ID can not be blank!!<br>'; }
    
    #validate customer id	
    	if(empty($data[3])) { $error .= 'Invalid customer id!!<br>'; }
    
    #validate customer name in application data
    	if(empty(trim($data[4]))) { $error .= 'Customer name can not be blank!!<br>'; }
    
    #validate customer DOB	
    	if(empty(trim($data[5]))) { $error .= 'Customer DOB can not be blank!!<br>';}
    
    #validate customer PAN number	
    	if (!preg_match("/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/", $data[6])) {
		  $error .= 'Invalid customer pan number!!<br>';
		}
	
	#validate customer gender value	
    	if(empty(trim($data[7]))) { $error .= 'Customer gender can not be blank!!<br>';}
	
	#validate customer mobile number	
    	if(!preg_match("/^([0-9]){10}?$/", $data[8])) { $error .= 'Invalid mobile number!!<br>'; } 

    #validate customer regidence address line 1 data
    	//if(empty(trim($data[9]))) { $error .= 'Customer residence address can not be blank!!<br>';}

	#validate customer regidence address pincode
    	//if(empty(trim($data[10]))) { $error .= 'Customer residence address pincode can not be blank!!<br>';}

    #validate customer email address	
    	/*if(empty(trim($data[11]))) { $error .= 'Customer email address can not be blank!!<br>';}
    	if(!empty(trim($data[11]))){
    	  	if(!filter_var($data[11], FILTER_VALIDATE_EMAIL)) { $error .= 'Invalid customer email address format!!<br>';}
    	}*/

    #validate customer company name 		
    	//if(empty(trim($data[12]))) { $error .= 'Customer company name can not be blank!!<br>';}

    #validate customer business address line 1 data
    	if(empty($data[13])) { $error .= 'Customer delivery address 1 can not be blank!!<br>'; } 

	#validate customer business address line 1 data
    	//if(empty($data[14])) { $response['error'] .= 'Customer delivery address 2 can not be blank!!<br>'; } 

    #validate customer business address landmark data	
    	//if(empty($data[15])) { $response['error'] .= 'Customer delivery address landmark can not be blank!!<br>'; } 

    #validate customer business address city data	
    	if(empty($data[16])) { $error .= 'Delivery address city can not be blank!!<br>'; } 

    #validate customer business address state data		
    	if(empty($data[17])) { $error .= 'Delivery address state can not be blank!!<br>';}

    #validate customer business address postcode data	
    	if(empty($data[18])) { $error .= 'Delivery address postcode can not be blank!!<br>'; } 

    #validate customer annual turnover data
    	//if(empty($data[19])) { $error .= 'Customer annual turnover data can not be blank or 0!!<br>'; } 

    #validate customer business vintage
    	//if(empty($data[20])) { $error .= 'Customer business vintage data can not be blank!!<br>'; } 

    #validate date of customer first successful order date
	   if(empty($data[21])) { $error .= 'Date of customer first successful order date can not be blank!!<br>'; }     	

	#validate customer browser data	   
	   //if(empty($data[37])) { $error .= 'Browser data can not be blank!!<br>'; }     	

	#validate customer ip address data  
	   //if(empty($data[38])) { $error .= 'Customer IP address data can not be blank!!<br>'; }     	
   
	   if(!empty($error)) {
	   		$response['error'] = $error;
	   }

    	return $response;
    }

    /*
	* Protected method to validate mobile phone number
	* @param : string $mobile_number
	* @return: string $mobile_number
	* @author: MSA August 2019
    */
    protected function validate_mobile_number( string $mobile_number )
    {
    	//remove extra chars - space, plus or hypen sign allow only numbers 0-9
    	$mobile_number = preg_replace("/[^0-9]/", "", $mobile_number);

    	// get last 10 digit from mobile string and validate for numbers only
    	$mobile_number = substr( $mobile_number, -10 );

    	return $mobile_number;
    }

    /*
	* Protected method to update customer credit application status for pre-approval process
	* @param : int $credit_application_id
	* @param : array $data
	* @return: void
	* @author: MSA March 2019
    */
    protected function updateCustomerDataForRBLPreApprovalProcess(int $credit_application_id, array $data)
    {
    	$this->model_sale_customer_credit_application->saveCreditPreApprovalData($data);

    	// Add remark data
	    $remark_data = array(
	    	'credit_application_id' => $credit_application_id,
	    	'type'					=> 'note',
	    	'document_status'		=> 'under_process',
	    	'reason' 				=> array('Send for RBL Pre-Approval'),
	    	'remark' 				=> 'Send for RBL Pre-Approval',
	    	'followup_date'			=> date('Y-m-d')
	    );
	    $this->model_sale_customer_credit_application->addStatusRemarks($remark_data);
	    
	    //Add in action log
	    $action_log = array(
	    	'credit_application_id' => $credit_application_id,
	    	'type'					=> 'KHUFIYA_USER',
	    	'user_id'				=> $this->user->getId(),
	    	'step'					=> 0,
	    	'action'				=> 'Send to RBL Pre-Approval'
	    );
	    $this->model_sale_customer_credit_application->addCreditApplicationActionLog($action_log);
    }

    private function sendMailAfterUserCreditApproved($subject, $credit_limit, $toAddress) {

		if (method_exists($this, 'get')) {
            $config = $this->get('config');
        } else {
            $config = $this->config;
        }
        $data['limit'] = $credit_limit;

        $data['limit_break'] = $this->creditLimitBreakList($credit_limit);
        if(is_array($data['limit_break']))
        {
         $data['limit'] =  $data['limit_break']['limit'][12];	
         $subject = str_replace($credit_limit, $data['limit'], $subject);
        }
        
        $html = $this->load->view('mail/send_mail_after_user_credit_approved.tpl',$data);

        $mail = new  PHPMailer();

        $mail->isSMTP();
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
		$mail->setFrom(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addReplyTo(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addAddress($toAddress['email'], $toAddress['name']);
        $mail->addCC('info@srngfin.com', 'SRNG Finance');
        $mail->addBCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addBCC(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
        $mail->AddAttachment(DIR_IMAGE.'credit/agreement-docs/NACH-Printout-explanation.jpg');
        //$mail->AddAttachment(DIR_IMAGE.'credit/agreement-docs/sample-cheque.jpeg');
        $mail->AddAttachment(DIR_IMAGE.'credit/agreement-docs/PayLater_Docket_May19.pdf');
        $mail->Subject = $subject;

        $mail->msgHTML($html);
        $mail->isHTML(true);
        $mail->send();
    }

      /**
       * Function : getDistanceBetweenTwoLocation
       * Method to get distance between two location
       * Request Parameters : customer_id, location, type(shop or home)
       * Type : Post
       * @author Rahul
       * Output : distance
       * */
      public function getDistanceBetweenTwoLocation($customer_id,$shop_location=NULL,$home_location=NULL){
        $this->load->model('account/credit_application', 'frontend');
             $customer_shop_location = $customer_home_location = $response = array();
             if(!empty($shop_location)){
              $customer_shop_location=$this->frontend_model_account_credit_application->getUserLocation($customer_id,'shop');
             }

            if(!empty($home_location)){
              $customer_home_location=$this->frontend_model_account_credit_application->getUserLocation($customer_id,'home');
             }
             $distancePlace = $this->getOriginAndDestination($shop_location,$customer_shop_location,$home_location,$customer_home_location);
             if(!empty($distancePlace)){
                    $result = $this->frontend_model_account_credit_application->calculateDistance($distancePlace);
                   if(!empty($result['status']) && strtolower($result['status'])=='ok'){
                    $response=$this->getLocationAccordingType($result['rows'],$distancePlace['address_type']);
                   }
             }
                
             return $response;
      }

        public function getOriginAndDestination($shop_location,$customer_shop_location,$home_location,$customer_home_location){
          $location_array = array();
          // address_type => 1=both,2=only shop,3=only home;
           if(!empty($customer_shop_location) && !empty($customer_home_location) && !empty($shop_location) && !empty($home_location)){
                $location_array['origin'][0]['value']=$shop_location??'';
                $location_array['origin'][1]['value']=$home_location??'';
                $location_array['destination'][0]['lat']=$customer_shop_location['lat']??'';
                $location_array['destination'][0]['lng']=$customer_shop_location['lng']??'';
                $location_array['destination'][1]['lat']=$customer_home_location['lat']??'';
                $location_array['destination'][1]['lng']=$customer_home_location['lng']??'';
                $location_array['address_type']='1';
            }else if(!empty($customer_shop_location) && !empty($shop_location)){
                $location_array['origin'][0]['value']=$shop_location??'';
                $location_array['destination'][0]['lat']=$customer_shop_location['lat']??'';
                $location_array['destination'][0]['lng']=$customer_shop_location['lng']??'';
                $location_array['address_type']='2';
            }else if(!empty($customer_home_location) && !empty($home_location)){
                $location_array['origin'][0]['value']=$home_location;
                $location_array['destination'][0]['lat']=$customer_home_location['lat']??'';
                $location_array['destination'][0]['lng']=$customer_home_location['lng']??'';
                $location_array['address_type']='3';
            }
         return $location_array;
        }


       public function getLocationAccordingType($distance,$address_type){
        // address_type => 1=both,2=only shop,3=only home;
        $result=array();
        if($address_type=='1'){
         $result['shop_distance'] = $distance[0]['elements'][0]['distance']['value']??'';
         $result['home_distance'] = $distance[1]['elements'][1]['distance']['value']??'';
        }
        else if($address_type=='2'){
          $result['shop_distance'] = $distance[0]['elements'][0]['distance']['value']??'';

        }
        else if($address_type=='3'){
          $result['home_distance'] = $distance[0]['elements'][0]['distance']['value']??'';
        }
        return $result;
       }

    public function create_customer()
    {
       $result = array();
       if(!empty($this->request->get['credit_application_id']))
        {
          $this->load->model('sale/customer_credit_application');

         $credit_data = $this->model_sale_customer_credit_application->getCreditApplicationData($this->request->get['credit_application_id']);

          $customer_id = $this->model_sale_customer_credit_application->addCreditApplicationDataInCustomer($this->request->get['credit_application_id'], $credit_data);
         
          $result = array(
          	    'customer_id' => $customer_id,
                'status' => 'Success',
                'message' => 'Customer Added succesfully'
               );

        }
        echo json_encode($result);
        die;
    }   

      
	public function change_document_type()
	{

	    $result = array();
		if(!empty($this->request->get['document_id']))
        {
			$this->load->model('sale/customer_credit_application');
			$this->model_sale_customer_credit_application->updateDocumentType($this->request->get['document_id'], $this->request->get['name']);
			$result = array(
				'status' => 'Success',
				'message' => 'Document Updated succesfully'
               );
        }

        echo json_encode($result);
        die;	
    }  

      public function get_pan_detail()
      {
      	 $this->load->model('tool/image');

         $result = array();

        if(!empty($this->request->get['credit_application_id']))
        {
           $this->load->model('sale/customer_credit_application');
           $result = $this->model_sale_customer_credit_application->getCustomerPancardDetail($this->request->get['credit_application_id']);
           if(!empty($result['dob']))
           {
           	$result['dob'] = date("d-m-Y", strtotime($result['dob']));
           }
           if(!empty($result['file_path']))
           {
           	$result['file_path'] = $this->model_tool_image->resize($result['file_path'], 500, 700);
           }
        }
        echo json_encode($result);
        die;	
      }

      public function crif_score()
      {
      	 $this->load->model('sale/customer_credit_application');

      	 $result = array();

         if(!empty($this->request->post['credit_application_id']))
         {
            $save_pancard = $this->model_sale_customer_credit_application->savePancardDetail($this->request->post);
            
           if($save_pancard)
           {
         	$result = $this->crif_score_application_id($this->request->post);
         	$data = (array)json_decode($result);
         	if(!empty($data['applicationId']))
         	{
         	  $result = $this->crif_score_api($data['applicationId']);
         	  $data = (array)json_decode($result);
         	  if(isset($data['creditScore']))
         	  {
         	  	if($data['creditScore'] > 100 && $data['creditScore'] < 650)
         	  	{
         	  		$this->model_sale_customer_credit_application->updateCrifScoreRemark($this->request->post['credit_application_id']);
         	  	}
         	  	$result = $this->model_sale_customer_credit_application->updateCrifScore($data['creditScore'], $this->request->post['credit_application_id']);
         	  	$result = json_encode($result);
         	  }
         	}
           }
           else
           {
                $result = json_encode(array(
                'status' => 'Failed',
                'message' => 'Email address and mobile number allready exist '
               ));
           }	

    	}
        
    	echo $result;
		die;
      } 

    private function crif_score_application_id($data)
    {
	        $post_data = array (
					  'loanDetails' => 
					  array (
					    'marketplaceProductName' => 'Working Capital Loan for MyWholesaleBox',
					    'marketplaceProductId' => 67,
					    'loanAmount' => '200000',
					    'loanTenor' => '14',
					  ),
					  'imeiNumber' => '353398090450991',
					  'date' => date("Y-m-d"),
					  'customerEmailVerified' => false,
					  'customerMobileVerified' => true,
					  'customer' => 
					  array (
					    'firstName' => $data['first_name'],
					    'middleName' => $data['middle_name'],
					    'lastName' => $data['last_name'],
					    'emailAddress' => $data['email'],
					    'telephoneNumber' => $data['mobile'],
					    'education' => '',
					    'dob' => date("Y-m-d", strtotime($data['dob'])),
					    'gender' => 'male',
					    'panNumber' => $data['pan_number'],
					    'fatherOrHusbandsFirstName' => '',
					    'fatherOrHusbandsMiddleName' => '',
					    'fatherOrHusbandsLastName' => '',
					    'firstNameOfMother' => '',
					    'middleNameOfMother' => '',
					    'lastNameOfMother' => '',
					    'maritalStatus' => 'Single',
					    'citizenship' => 'Indian',
					    'residentialStatus' => 'Both',
					    'govtId' => 
					    array (
					      0 => 
					      array (
					        'govtIdNumber' => '',
					        'govtIdType' => 'AADHAAR',
					        'govtIdExpiryDate' => '2029-01-01',
					      ),
					    ),
					    'residentialAddress' => 
					    array (
					      'line1' => 'B-1, Crystal Mall, Banipark',
					      'line2' => '',
					      'line3' => '',
					      'city' => 'Jaipur',
					      'state' => 'Rajasthan',
					      'district' => '',
					      'postcode' => '302016',
					      'country' => 'India',
					      'landlinePhoneNumber' => '',
					      'typeOfPremise' => 'Owned',
					      'dateOccupiedYear' => '2006',
					      'dateOccupiedMonth' => '01',
					    ),
					    'permanentAddress' => 
					    array (
					      'line1' => 'B-1, Crystal Mall, Banipark',
					      'line2' => '',
					      'line3' => '',
					      'city' => 'Jaipur',
					      'state' => 'Rajasthan',
					      'district' => '',
					      'postcode' => '302016',
					      'country' => 'India',
					      'landlinePhoneNumber' => '',
					      'typeOfPremise' => 'Owned',
					      'dateOccupiedYear' => '2006',
					      'dateOccupiedMonth' => '01',
					    ),
					  ),
					  'businessDetails' => 
					  array (
					    'tradingName' => 'test business',
					    'companyName' => 'test business',
					    'businessPan' => 'testa1234b',
					    'dateOfJoining' => '2017-01-01',
					    'dateOfIncorporation' => '2017-01-01',
					    'shopEstablishmentNumber' => 'test',
					    'businessAddress' => 
					    array (
					      'line1' => 'B-1, Crystal Mall, Banipark',
					      'line2' => '',
					      'line3' => '',
					      'city' => 'Jaipur',
					      'state' => 'Rajasthan',
					      'district' => '',
					      'postcode' => '302016',
					      'country' => 'India',
					      'landlinePhoneNumber' => '',
					      'typeOfPremise' => 'Owned',
					      'dateOccupiedYear' => '2006',
					      'dateOccupiedMonth' => '01',
					    ),
					    'typeOfBusinessEntity' => 'Partnership',
					    'natureOfBusiness' => 'Retail',
					    'noOfDirectorsOrPartners' => '1',
					    'businessSegment' => 'FMCG',
					    'businessVintage' => '2',
					    'monthsInCurrentBusiness' => '2',
					    'gstRegistered' => 'false',
					    'gstNumber' => '',
					  ),
					  'marketplaceSpecificSection' => 
					  array (
					    'marketplaceCustomerId' => '00000'.$data['customer_id'],
					    'marketplaceCustomerIdFormalName' => 'Customer Id',
					  ),
					);

	   $url = 'https://verify.epaylater.in:443/credit-application';
	   $post_data = json_encode($post_data);

	    $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_HEADER, false);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = @curl_exec($ch);
        
        $this->model_sale_customer_credit_application->updateCrifScoreLog($this->request->post['credit_application_id'], $post_data, $result); 

        if (!$result) {
            $result = json_encode(array(
                'status' => 'Failed',
                'message' => 'Error while getting data - ' . curl_error($ch)
            ));
        }
        @curl_close($ch);
        return $result;
    } 

    private function crif_score_api($application_id)
    {
	   $url = 'https://verify.epaylater.in:443/credit-application/'.$application_id.'/moveApplicationToApplied/v2';
	    $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_HEADER, false);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = @curl_exec($ch);
        if (!$result) {
            $result = json_encode(array(
                'status' => 'Failed',
                'message' => 'Error while getting data - ' . curl_error($ch)
            ));
        }
        @curl_close($ch);
        return $result;
    }


    public function getRequestHeaders()
    {
       $headers = array();
       $headers[] = 'Authorization: Bearer secret_bb430518-6679-11e9-872c-2b6c7cb60ae0';
       $headers[] = 'content-type: application/json';
       return $headers;
    } 

    private function uploadCrifImages($directory, $filename, $file_tmp)
    {
        // Check to see if any PHP files are trying to be uploaded

        // $file_name_with_full_path = $_FILES[$fileType]['tmp_name'];

        if (is_uploaded_file($file_tmp)) {
            if (function_exists('curl_file_create')) { // php 5.5+
                $cFile = curl_file_create($file_tmp);
            } else { // 
                $cFile = '@' . realpath($file_tmp);
            }
        }

        $post = array('file' => $cFile);
        $ch = curl_init();
        $target_url = STATIC_CONTENT_URL_SSL . 'fileupload.php?directory=' . $directory . '&filename=' . $filename;
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true);
        //print_r($result);die;
        if (!empty($result['success'])) {
            return true;

        } else {
            return false;
        }

    }
        
   private function creditLimitBreakList($limit)
   {
   	 $data = array();
  
   	$data['month_list'] =  array('01','02','03','04','05','06','07','08','09','10','11','12');
   	$data['month_list'][] = 'Total';
    
    $data['limit']['10000'] = array(10000,10000,15000,15000,20000,20000,25000,25000,25000,30000,30000,30000,255000);
     $data['limit']['15000'] = array(15000,15000,20000,20000,30000,30000,40000,40000,40000,45000,45000,45000,385000);
     $data['limit']['20000'] = array(20000,20000,30000,30000,40000,40000,50000,50000,60000,60000,80000,80000,560000);
     $data['limit']['25000'] = array(25000,25000,40000,40000,50000,50000,60000,60000,75000,75000,100000,100000,700000);
     $data['limit']['30000'] = array(30000,30000,48000,48000,60000,60000,72000,72000,90000,90000,120000,120000,840000);
     $data['limit']['35000'] = array(35000,35000,56000,56000,70000,70000,84000,84000,105000,105000,140000,140000,980000);
     $data['limit']['40000'] = array(40000,40000,64000,64000,80000,80000,96000,96000,120000,120000,160000,160000,1120000);
     $data['limit']['45000'] = array(45000,45000,72000,72000,90000,90000,108000,108000,135000,135000,180000,180000,1260000);
     $data['limit']['50000'] = array(50000,50000,80000,80000,100000,100000,120000,120000,150000,150000,200000,200000,1400000);
     $data['limit']['60000'] = array(60000,60000,96000,96000,120000,120000,144000,144000,180000,180000,240000,240000,1680000);
     $data['limit']['70000'] = array(70000,70000,112000,112000,140000,140000,168000,168000,210000,210000,280000,280000,1960000);
     $data['limit']['75000'] = array(75000,75000,120000,120000,150000,150000,180000,180000,225000,225000,300000,300000,2100000);
     $data['limit']['100000'] = array(100000,100000,160000,160000,200000,200000,240000,240000,300000,300000,400000,400000,2800000);
    
     if(isset($data['limit'][$limit]))
     {
       return array('limit'=>$data['limit'][$limit],  'month_list' => $data['month_list']);
     }
     else
     {
     	return $limit;
     } 
     
   }

}
