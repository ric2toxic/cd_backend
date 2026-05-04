<?php
class ControllerAccountBankDetails extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('tool/image'); 
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/bank_details', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}


		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}		

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;


        $this->load->language('account/bank_details');
        
        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


		
		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $data['bank_ac_holder_name'] = $this->request->post['bank_ac_holder_name'];
            $data['bank_ac_number']      = $this->request->post['bank_ac_number'];
            $data['ifsc_code']           = $this->request->post['ifsc_code'];
            $data['customer_vpa']        = $this->request->post['customer_vpa'];
            $data['otp']                 = $this->request->post['otp'];
            $this->request->post['customer_id'] = $this->customer->getId();
            $this->request->post['otp_page'] = 'bank_update';

            $result  = $this->validate();
            if(isset($result) && !empty($result)){
                $data['error'] = $result;
            } else{
                $this->request->post['customer_id'] = $this->customer->getId();
    			$this->model_account_customer->editCustomerBankDetails($this->request->post);
                $data['success'] = $this->language->get('text_success');
            }
		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_my_orders'),
			'href'      => $this->url->link('account/order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_bank'),
			'href'      => $this->url->link('account/bank_details', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_your_bank_details'] = $this->language->get('text_your_details');
                
                //get chatbox language
                $data['text_chatbox_message'] = $this->language->get('text_chatbox_message');
                $data['text_whatsApp'] = $this->language->get('text_whatsApp');
                $data['text_chat_on_whatsappw_web'] = $this->language->get('text_chat_on_whatsappw_web');
                $data['text_whatsappw_no'] = $this->language->get('text_whatsappw_no');
                $data['text_whatsappw_no_msg_text'] = $this->language->get('text_whatsappw_no_msg_text');
                $data['text_callus'] = $this->language->get('text_callus');
                $data['text_callus_no'] = $this->language->get('text_callus_no');
                $data['text_call_back_request'] = $this->language->get('text_call_back_request');
                $data['text_call_back_request_messgae'] = $this->language->get('text_call_back_request_messgae');
                $data['text_chat_here'] = $this->language->get('text_chat_here');
                $data['text_error_mobile_no'] = $this->language->get('text_error_mobile_no');
                $data['text_error_mobile_not_start_zero'] = $this->language->get('text_error_mobile_not_start_zero');
                
                

		$data['entry_account_holder_name'] = $this->language->get('entry_account_holder_name');
	$data['entry_account_no'] = $this->language->get('entry_account_no');
		$data['entry_ifsc_code'] = $this->language->get('entry_ifsc_code');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}
		/***** UPI *****/
		$data['entry_customer_vpa'] = $this->language->get('entry_customer_vpa');
		$data['customer_vpa'] = $this->model_account_customer->getCustomerVPA($this->customer->getId());
		/****/
		if (isset($this->error['account_holder_name'])) {
			$data['error_account_holder_name'] = $this->error['account_holder_name'];
		}

		if (isset($this->error['bank_account_number'])) {
			$data['error_bank_account_number'] = $this->error['bank_account_number'];
		}

		if (isset($this->error['ifsc_code'])) {
			$data['error_ifsc_code'] = $this->error['ifsc_code'];
		}

		$data['action'] = $this->url->link('account/bank_details', '', 'SSL');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$bank_details = $this->model_account_customer->getCustomerBank_details($this->customer->getId());
            if(isset($bank_details)){
                $data['bank_ac_holder_name'] = $bank_details['bank_ac_holder_name'];
                $data['bank_ac_number']      = $bank_details['bank_ac_number'];
                $data['ifsc_code']           = $bank_details['ifsc_code'];
            }
		}
		$data['back'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		if(CONFIG_IS_MOBILE == 1)
       {
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	   }	
                
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/bank_details.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/bank_details.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/bank_details.tpl', $data));
		}
	}

	   public function userBankDetailValidationProcess(){
          $this->load->language('account/bank_details');
          $this->load->model('account/customer');

            $this->request->post['bank_ac_holder_name'] = $this->request->request['bank_ac_holder_name'];
            $this->request->post['bank_ac_number']      = $this->request->request['bank_ac_number'];
            $this->request->post['ifsc_code']           = $this->request->request['ifsc_code'];
            $this->request->post['otp']                 = $this->request->post['otp'];
            $this->request->post['otp_page'] = 'bank_update';
            //$data['customer_vpa']        = $this->request->request['customer_vpa'];
            $this->request->post['customer_id'] = $this->customer->getId();
          	$validate=$this->validate();
            if($validate)
            {
		        foreach ($validate as $key => $value) {
    	               $data['data']=str_replace('_', " ", $key);
    	               $data['message']=$value;
		        }
               $data['statusCode']  = 400;
               echo json_encode($data); exit;
            } 
            else{
                $data['data']        = $this->language->get('text_success');
                $data['message']    = $this->language->get('text_success');
                $data['statusCode']  = 200;
                echo json_encode($data); exit;
        }
          exit;
      }

	protected function validate() {
		$this->load->model('account/customer');
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

		if (!$this->request->post['otp_override'] && (utf8_strlen(trim($this->request->post['otp'])) < 1) || (utf8_strlen(trim($this->request->post['otp'])) > 4)) {
			$error['error_otp'] = $this->language->get('error_otp');
		}

		if (isset($this->request->post['customer_vpa']) && (utf8_strlen(trim($this->request->post['customer_vpa'])) > 0)) {
			$upi_vpa = trim($this->request->post['customer_vpa']);
			(int) $length = utf8_strlen($upi_vpa);
        	if((int) $length > 0){
        		$pos = strpos($upi_vpa, '@');
	        	$pos_next = strpos($upi_vpa, '@', $pos+1);
	        	if($pos == 0 || $pos == ($length-1) || $pos === FALSE || $pos_next > 0){
	        		$error['error_customer_vpa'] = $this->language->get('error_customer_vpa');
	        	}
        	}
		}
		if(empty($error['error_account_number']) && empty($error['error_ifsc_code'])){
			$check_if_bank_account_exist=$this->model_account_customer->checkIfBankAccountExists($this->request->post);
			if(empty($check_if_bank_account_exist)){
				$error['error_account_number'] = $this->language->get('error_account_number_exist');
			}

		}

		if(empty($error['error_account_number']) && empty($error['error_ifsc_code'])){
			$check_if_bank_account_block=$this->model_account_customer->checkIfBankAccountIsBlock($this->request->post);
			if(!empty($check_if_bank_account_block)){
				$error['error_account_number'] = $this->language->get('error_account_number_block');
			}

		}

		if(!$this->request->post['otp_override'] && empty($error['error_otp']))
		{
			$check_data = $this->model_account_customer->checkOTP($this->request->post);
			if(count($check_data) == 0)
             {
              $check_data = $this->model_account_customer->checkMasterOTP($this->request->post);
             }

			if(count($check_data) == 0)
			{
				$error['error_otp'] = $this->language->get('error_otp_exist');
			}
			else
			{
				
			  $this->model_account_customer->verifyOTP($check_data);
			}

		}

		return $error;
	}
}
