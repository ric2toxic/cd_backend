<?php
class ControllerSaleCustomerCredits extends Controller {
	
	public function index() {
		$this->getCustomerCredits();
    }

	public function getCustomerCredits() {

		$data = array();
		$data['wsb_credit_details'] = array();
		
		$customer_id = (int)($this->request->get['customer_id'] ?? 0);

		//Master_ids data fro given customer_id
		$master_id_data = Customer::getMasterIdsByCustomerIds($this->db, (string)$customer_id);
		
		$data['customer_id'] = $customer_id;
		$data['master_id']   = $master_id_data[0]['master_id'] ?? 0;
		$this->load->autoLoadLanguage('sale/customer_credits', $data);
		//Set Page Title
		$this->document->setTitle($this->language->get('heading_title'));

		//Token to mantian session
		$data['token'] = $this->session->data['token'] ?? '';
		
		if(!empty($customer_id) && !empty($master_id_data) ){
			//WSB_CREDIT library class object
			$wsb_credit = new WsbCreditPayment($this);
			$credit     = new CreditPayment($this);

			//Fetch WSB_CREDIT related details for specific customer_id
			$wsb_credit_details = $wsb_credit->getWsbCreditDetailByCustomerIds($customer_id);
			$data['wsb_credit_details'] = $wsb_credit_details[$customer_id] ?? array();
			
			if(!empty($data['wsb_credit_details']['nach_schedule_crontab'])){
				$schedule = CronSchedule::fromCronString($data['wsb_credit_details']['nach_schedule_crontab']);
				$data['wsb_credit_details']['crontab_title'] = $schedule->asNaturalLanguage();
			}

			//Get Customer's all credit details(i.e. for Neogrowth/Lazypay)
			$all_credits = $credit->getCreditDetailsByCustomerId($customer_id);

			//Set Neogrowth Details
			$data['neogrowth_credit_status']       = $all_credits['neogrowth']['credit_status'] ?? 0;
			$data['neogrowth_registration_number'] = $all_credits['neogrowth']['neogrowth_registration_number'] ?? '';
			$data['neogrowth_account_number']      = $all_credits['neogrowth']['neogrowth_account_number'] ?? '';
			
			//Set Lazy Details
			$data['lazypay_status']   = $all_credits['lazypay']['credit_status'] ?? 0;
			$data['lazypay_email']    = $all_credits['lazypay']['lazypay_email']  ?? '';
			$data['lazypay_mobile']   = $all_credits['lazypay']['lazypay_mobile'] ?? '';

			//Set RBL Details
			$data['rbl_credit_status']       = $all_credits['Rbl']['credit_status'] ?? 0;
			$customer_nach_details_ob = new CustomerNachDetails($this->registry);
			$customer_nach_details = $customer_nach_details_ob->getCustomerNachDetails($customer_id);
		
			$data['customer_nach_details'] = $customer_nach_details;
			//Credit tpl file content
			$data['nach_details_content'] = $this->load->view('sale/customer_nach_details.tpl', $data);

			$customer      = new Customer($this);
			$customer_info = $customer->getCustomerById($customer_id);

			$data['customer_profile'] = $customer->getCustomerProfileInfo($customer_id);

			//WSB credit_application approved limit and date
			$wsb_application_data  = $customer->wsbCreditApplicationApprovedLimitData($customer_id);
			$data['crif_score']             = $wsb_application_data['crif_score'] ?? NULL;
			$data['crif_score_date']        = $wsb_application_data['crif_score_date'] ?? '';
			$data['credit_approved_date']   = $wsb_application_data['credit_approved_date'] ?? ''; 
			$data['credit_approved_limit']  = $wsb_application_data['credit_limit'] ?? ''; 
			$data['credit_ac_number']       = $wsb_application_data['credit_ac_number'] ?? ''; 

			$data['all_wsb_credit_payment_statuses'] = array(
	                                                      'DISABLED',
	                                                      'ENABLED',
	                                                      'BLOCKED',
	                                                      'REJECTED',
	                                                      'ON_HOLD',
	                                                      'PENDING_APPROVAL'
				                                        );

			//WSB Credit tpl file content
			$data['tab_wsb_credit_content'] = $this->load->view('sale/wsb_credit_tab.tpl', $data);

			//Neogrowth Credit tpl file content
			$data['tab_neogrowth_content'] = $this->load->view('sale/neogrowth_credit_tab.tpl', $data);

			//Lazypay Credit tpl file content
			$data['tab_laypay_content'] = $this->load->view('sale/lazypay_credit_tab.tpl', $data);

		}else{
			$data['error_msg'] = "Invalid Customer Id :" .$customer_id;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/customer_credits', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('sale/customer_credits/saveCreditDetails', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('sale/customer_credits', 'token=' . $this->session->data['token'], 'SSL');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/customer_credits.tpl', $data));
		
	}

	/**
     * Public method to save credit details for customer
     * @author: Nishu, Feb 2019
	*/
	public function saveCreditDetails(){
		$customer_id = 0;
		if(!empty($this->request->post) ){
			$customer_id = $this->request->post['customer_id'];
			if(!empty($this->request->post['update_for'])){

				if($this->request->post['update_for'] == 'wsb_credit'){
					$wsb_credit_data = array();
					$wsb_credit_data['customer_id']            = $customer_id;
					$wsb_credit_data['status']                 = $this->request->post['wsb_credit_payment_status'] ?? '';
					$wsb_credit_data['credit_limit']           = $this->request->post['wsb_credit_payment_limit'] ?? '';
					$wsb_credit_data['nach_schedule_crontab']  = $this->request->post['nach_schedule_crontab'] ?? '';
					$wsb_credit_data['days_before_nach_start'] = $this->request->post['days_before_nach_start'] ?? '';
					$wsb_credit_data['schedule_days_for_nach'] = $this->request->post['schedule_days_for_nach'] ?? '';
					$wsb_credit_data['auto_nach_enabled']      = $this->request->post['hddn_auto_nach_enabled'] ?? 0;
					$wsb_credit_data['comment']                = $this->request->post['wsb_credit_comment'] ?? '';
					$wsb_credit = new WsbCreditPayment($this);

					//====webengage start===//
					if(!empty($this->request->post['wsb_credit_payment_status']) && ($this->request->post['wsb_credit_payment_status']=='ENABLED' || $this->request->post['wsb_credit_payment_status']='BLOCKED' || $this->request->post['wsb_credit_payment_status']='REJECTED')){
						$status_for_webengage=$this->request->post['wsb_credit_payment_status'];
					}
                    if(!empty($customer_id) && !empty($status_for_webengage)){
                    	$this->load->model('webengage/webengage','frontend');
                      $this->frontend_model_webengage_webengage->updateCreditApplicationStatusOnWebengage($customer_id,'NA',$status_for_webengage);
                    }
             //====webengage end===//
					$wsb_credit->updateCreditDetails($wsb_credit_data);
				}else if($this->request->post['update_for'] == 'neogrowth'){
					$neogrowth_data = array();
					$neogrowth_data['customer_id']                   = $customer_id;
					$neogrowth_data['credit_status']                 = $this->request->post['neogrowth_credit_status'] ?? '';
					$neogrowth_data['neogrowth_registration_number'] = $this->request->post['neogrowth_registration_number'] ?? '';
					$neogrowth_data['neogrowth_account_number']      = $this->request->post['neogrowth_account_number'] ?? '';
					$credit = new CreditPayment($this);
					$credit->updateCreditDetails($neogrowth_data);
				}else if($this->request->post['update_for'] == 'lazypay'){
					$lazypay_data = array();
					$lazypay_data['customer_id']    = $customer_id;
					$lazypay_data['credit_status']  = $this->request->post['lazypay_status'] ?? '';
					$lazypay_data['lazypay_mobile'] = $this->request->post['lazypay_mobile'] ?? '';
					$lazypay_data['lazypay_email']  = $this->request->post['lazypay_email']  ?? '';
					
					$lazypay = new LazypayPayment($this);
					$lazypay->updateCreditDetails($lazypay_data);
				}else if($this->request->post['update_for'] == 'rbl_credit'){
					$rbl_data = array();
					$rbl_data['customer_id']                   = $customer_id;
					$rbl_data['credit_status']                 = $this->request->post['rbl_credit_status'] ?? '';
					$credit = new RblPayment($this);
					$credit->updateCreditDetails($rbl_data);
				}
			}
		}
		//$this->getCustomerCredits();
		$token = $this->request->get['token'];
		
		$url = 'index.php?route=sale/customer_credits&token='.$token.'&customer_id='.$customer_id;
		header('Location: '.$url);
	}

	/**
	 * Public method to log admin change log
	 * @author : Nishu
	*/
	public function logAdminChangeLog($admin_change_data){
		/////////////// Insert a row in customer change log/////////////////////
          
        $admin_change_data['user_id']       = $this->user->getId() ?? 0;
        $admin_change_data['name']          = 'Customer Credits';
        $admin_change_data['username']      = $this->user->getUserName()["name"] ?? '';
        $admin_change_data['table_name']    = 'oc_customer';
        $admin_change_data['source_field']  = 'customer_credits';
        $admin_change_data['ref_url']       = 'sale/customer_credits';
        $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
        $admin_change_data['ip_address']    = $_SERVER['REMOTE_ADDR'];
        $admin_change_data['file_location'] = 'sale/customer_credits';
        $admin_change_data['user_type']     = 'Administrator';

        //Call dynamic static function for entry into admin change log
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);
	}

	/**
     * @info: Public method to shift NACH schedule at the end
     * @author :Nishu, April 2019
	*/
	public function getWsbCreditHistory(){
		$data = array();
		//Checks if data is set into post params
		if(!empty($this->request->post['customer_id'])){

			$wsb_credit = new WsbCreditPayment($this);
			$data = $wsb_credit->getWsbCreditHistory( (int)$this->request->post['customer_id'] );
		}
		echo json_encode($data); exit();
	}
	
}