<?php
class ControllerAccountEdit extends Controller {
	private $error = array();

	public function index() {
		 $this->load->model('tool/image'); 
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');

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


       $this->load->language('account/edit');

        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] =$this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->load->model('account/customer');

			if($this->customer->getEmail() != '')
            {
			  $this->request->post['email'] = $this->customer->getEmail();
			}

			if($this->customer->getTelephone() != '' && $data['international_store'] == 0)
            {
			  $this->request->post['telephone'] = $this->customer->getTelephone();
			}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if($this->customer->getGSTNumber() != '')
            {
			  $this->request->post['gst_number'] = $this->customer->getGSTNumber();
			}

			$this->model_account_customer->editCustomer($this->request->post);

       	    $update_details['customer_id'] 	  = $this->customer->getId();        
		    $this->load->model('account/helpdesk');
        	$access_token   		  = $this->model_account_helpdesk->getHelpdeskId($update_details['customer_id']);
        	if(!empty($access_token['helpdesk_id'])){
				$obj = new Helpdesk($this); 
				$update_details['customer_name']    =  $this->request->post['firstname'].' '.$this->request->post['lastname'];
				$update_details['email'] 			  =  $this->request->post['email'];
				$update_details['telephone']          =  $this->request->post['telephone'];
	        	$update_details['customer_access_token']  = $access_token['password'];
	        	$update_details['helpdesk_id']  	  = $access_token['helpdesk_id'];
		        $obj->updateHelpdeskCustomer($update_details);        		
        	}

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('account/order', '', 'SSL'));
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
			'text'      => $this->language->get('text_edit'),
			'href'      => $this->url->link('account/edit', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_your_details'] = $this->language->get('text_your_details');
		$data['text_additional'] = $this->language->get('text_additional');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_gst_number'] = $this->language->get('entry_gst_number');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');
		$data['button_upload'] = $this->language->get('button_upload');
                
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

		$data['error_warning']      = $this->error['warning'] ?? '';
		$data['error_firstname']    = $this->error['firstname'] ?? '';
		$data['error_lastname']     = $this->error['lastname'] ?? '';
		$data['error_email']        = $this->error['email'] ?? '';
		$data['error_telephone']    = $this->error['telephone'] ?? '';
		$data['error_gst_number']   = $this->error['gst_number'] ?? '';
		
		$data['action'] = $this->url->link('account/edit', '', 'SSL');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
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

       if (!empty($customer_info) && $customer_info['email'] != '') {
			$data['email'] = $customer_info['email'];
			$data['email_exist'] = 1;
		}
		elseif (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		}  else {
			$data['email'] = '';
		}		

       if (!empty($customer_info) && $customer_info['telephone'] != '') {
			$data['telephone'] = $customer_info['telephone'];
			$data['telephone_exist'] = 1;
		}
		elseif (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		}  else {
			$data['telephone'] = '';
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

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/edit.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/edit.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/edit.tpl', $data));
		}
	}

	protected function validate() {
		
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		// if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
		// 	$this->error['lastname'] = $this->language->get('error_lastname');
		// }

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if (($this->customer->getTelephone() != $this->request->post['telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($this->request->post['telephone'])) {
			$this->error['warning'] = $this->language->get('error_telephone_exists');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}
		
		$customer_id = $this->customer->getId();
		
		$this->request->post['gst_number'] = $this->request->post['gst_number'] ?? "";
		$this->request->post['old_gst_number'] = $this->request->post['old_gst_number'] ?? "";
		
		if( $this->request->post['gst_number'] != $this->request->post['old_gst_number'] ) { // don't validate if same value
			
			$valid_gst_result = $this->customer->gstObject->validateGSTNumber($this->request->post['gst_number'], $customer_id);
			
			if ( !empty(trim($this->request->post['gst_number'])) && !($valid_gst_result['result'] === true) ) {
				if($valid_gst_result['message'] == "error_regex") {
					$this->error['gst_number'] = $this->language->get('error_gst_number');
				} elseif($valid_gst_result['message'] == "error_checksum") {
					$this->error['gst_number'] = sprintf($this->language->get('error_gst_checksum'),
					 									 $valid_gst_result['gst_number_details']['gst_number_without_checksum']."<b>".$valid_gst_result['gst_number_details']['gst_number_checksum']."</b>");
				} else if($valid_gst_result['message'] == "error_duplicate") {
					/**
                     *  - Commenting below code to allow customer to add duplicate GST Number (Duplicacy mail is already being sent)
                     *  - By Anurag Jain(Sept 2018)
                     **/
	                // $user_str = '';
	                // if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
					// 	$duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
	                //     if(!empty($duplicate_gst_number_customer['telephone'])) {
	                //         $user_str = 'mobile number <strong>' . substr($duplicate_gst_number_customer['telephone'],0, 2) . 'xxxxx' . substr($duplicate_gst_number_customer['telephone'],7) . '</strong>';
	                //     } else if(!empty($duplicate_gst_number_customer['email'])){
	                //         $len = strlen(explode('@',$duplicate_gst_number_customer['email'])[0]);
	                //         $user_str = 'email <strong>' . 'xxxxx' . substr($duplicate_gst_number_customer['email'],$len/2).'</strong>';
	                //     }
	                //     $this->error['warning'] = sprintf($this->language->get('error_exists_gst'), $this->request->post['gst_number'], $user_str, $this->url->link('information/contact') );
	                // }
				}
			}
		}

		return !$this->error;
	}
	
	/**
	 * @deprecated: function is obsolete now; please use validateGSTNumber($gst_number) from Customer/GST library class
	 * @author: Anurag Jain (24 July 2018)
	 */
	protected function validateGSTNo($gst_number) {
	// $regex_for_gst = '/^[0-9]{2}[A-Z]{3}[C,P,H,F,A,T,B,L,J,G,E]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}[A-Z]{1}[A-Z0-9]{1}?$/';
	$regex_for_gst = '/^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/';
  
	if (!preg_match($regex_for_gst, $gst_number)) 
		{
            return true;
        }
    else
       {
           return false;
       }
	
     }


}
