<?php

class ControllerAccountLogin extends Controller {

    private $error = array();

    public function index() {

       /* if(!$this->customer->isLogged()) { $this->session->data['otp_verify_success'] = 3; }
    	$this->response->redirect($this->url->link('common/home', '', 'SSL'));
    	exit;*/

     if ($this->customer->isLogged())
       {
           //Check for seller redirection
           if($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())){
               if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE)
               {
                   $redirect_url =  $this->url->link('seller_panel/account-order', '', 'SSL');
               }
               else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
               {
                   $redirect_url =  $this->url->link('seller_panel/profile', '', 'SSL');
               }
               else{
                   $redirect_url =  $this->url->link('account/logout', '', 'SSL');
               }
           }
           else
           {
               $redirect_url =  $this->url->link('account/order', '', 'SSL'); // buyer redirection
           }

           $this->response->redirect($redirect_url);

        }
       else
       {
           $this->session->data['otp_verify_success'] = 3;
           $this->response->redirect($this->url->link('common/home', '', 'SSL'));
       }
       exit;

        $this->document->addStyle('catalog/view/theme/default/stylesheet/stylesheet_new.css');
        //echo "<pre>"; print_r($this->request->post); echo "</prE>"; die;
        if (isset($this->request->get['referrers'])) {
            $data['referrers'] = $this->request->get['referrers'];
        } else {
            $data['referrers'] = '';
        }
        /*         * *************For Login**************** */
        $this->load->model('account/customer');
        $this->load->language('account/login');
//		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
//		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
//		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		if (!empty($this->request->get['token'])) {
			$this->event->trigger('pre.customer.login');

			$this->customer->logout();
			$this->cart->clear();

			unset($this->session->data['wishlist']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);
			if ($customer_info && $this->customer->login($customer_info['telephone'], '', true)) {
				// Default Addresses
				$this->load->model('account/address');

				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				$this->event->trigger('post.customer.login');

				if ($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())) {

				    if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE) {
                        $this->response->redirect($this->url->link('seller_panel/account-order', '', 'SSL'));
                    }
                    else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
                    {
                        //## If seller status is not active redirected to profile page to complete his profile (previously redirect to seller/account-profile)
                        $this->response->redirect($this->url->link('seller_panel/profile', '', 'SSL'));
                    } else {
                    	$this->response->redirect($this->url->link('account/logout', '', 'SSL'));
                    }
				} else {
					$this->response->redirect($this->url->link('account/order', '', 'SSL'));
				}
			}
		}
    	
		if ($this->customer->isLogged()) {
			if ($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())) {

                if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE) {
                    $this->response->redirect($this->url->link('seller_panel/account-order', '', 'SSL'));
                }
                else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
                {
                    //## If seller status is not active redirected to profile page to complete his profile (previously redirect to seller/account-profile)
                    $this->response->redirect($this->url->link('seller_panel/profile', '', 'SSL'));
                } else {
                	$this->response->redirect($this->url->link('account/logout', '', 'SSL'));
                }

			} else {
				$this->response->redirect($this->url->link('account/order', '', 'SSL'));
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_my_orders'),
				'href' => $this->url->link('account/order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_login'),
				'href' => $this->url->link('account/login', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');
		if(isset($this->request->post['form_button']) && $this->request->post['form_button']== 'log-button'){
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->loginValidation()) {
				unset($this->session->data['guest']);

				// Default Shipping Address
				$this->load->model('account/address');

				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
				if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
					$this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));

				} else {
					if (isset($this->request->post['referrers']) && $this->request->post['referrers'] =='cart'){
						$this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL'));
					} else {
                        if ($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())) {

                            if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE) {
                                $this->response->redirect($this->url->link('seller_panel/account-order', '', 'SSL'));
                            }
		                    else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
		                    {
		                        //## If seller status is not active redirected to profile page to complete his profile (previously redirect to seller/account-profile)
		                        $this->response->redirect($this->url->link('seller_panel/profile', '', 'SSL'));
		                    } else {
		                    	$this->response->redirect($this->url->link('account/logout', '', 'SSL'));
		                    }

				        } else {
					        $this->response->redirect($this->url->link('account/order', '', 'SSL'));
				        }
					}
				}
			}
		}else if(isset($this->request->post['form_button']) && $this->request->post['form_button']== 'reg_button'){
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->registerValidation()) {

				$arr_name = explode(" ", $this->request->post['name']);
				$this->request->post['firstname'] = $arr_name[0];
				if(isset($arr_name[1])) {
					$this->request->post['lastname'] = $arr_name[1];
				}else{
					$this->request->post['lastname'] = '';
				}

				if(empty($this->request->post['reg_email'])){

					$this->request->post['reg_email'] = 'noemailwsb@gmail.com';
				}
				if(isset($this->request->post['reg_telephone'])){
					$mobile = $this->request->post['reg_telephone'];
				}else{
					$mobile = '';
				}
				$customer_id = $this->model_account_customer->addCustomer($this->request->post);

				if(isset($_COOKIE['session_id']) && isset($_COOKIE['utm_source'])){
					$this->load->model('tracking/tracking');
					$this->model_tracking_tracking->update_customer_id($customer_id,$mobile);
				}
				// Clear any previous login attempts for unregistered accounts.
				$this->model_account_customer->deleteLoginAttempts($this->request->post['reg_email']);

				$this->customer->login($this->request->post['reg_telephone'], $this->request->post['password']);

				unset($this->session->data['guest']);
                
				if($this->request->post['referrers']=='cart'){
					$this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL'));
				}else{

					$this->response->redirect($this->url->link('account/success'));

				}
				//$this->response->redirect($this->url->link('account/success'));
			}
		}else if(isset($this->request->post['form_button']) && $this->request->post['form_button']== 'forget_button'){
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->forgotValidation()) {
				$this->load->language('mail/forgotten');
				$this->load->language('account/sms_templates');

				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				//$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
				$password = substr(mt_rand(), 0, 6);

				$this->model_account_customer->editPassword($customer_info['customer_id'], $password);

				$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

				$username = $this->model_account_customer->getUserNameByMobileNumber($this->request->post['email']);
				$message = $this->language->get('forgot_pass_msg');
				$msg = sprintf($message,$username,$password );
				$this->send_mail_or_sms($this->request->post['email'],$msg,$subject,$password);
			}
		}

		$data['text_returning_customer'] = $this->language->get('text_returning_customer');
		$data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
		$data['text_forgotten'] = $this->language->get('text_forgotten');

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_mobile_or'] = $this->language->get('entry_mobile_or');
		$data['entry_password'] = $this->language->get('entry_password');

		//this line is belongs to english.php language file
		$data['button_login'] = $this->language->get('button_login');


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
		if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
			$data['redirect'] = $this->request->post['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
			$data['redirect'] = $this->session->data['redirect'];
			unset($this->session->data['redirect']);
		} elseif(isset($_POST['seller_login'])) {
			$str = 'error=error_in_login';
			$error = base64_encode($str);
			$this->response->redirect($this->url->link('common/seller_home', 'error='.$error, 'SSL'));
		}else{
			$data['redirect'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}
		$data['login_email'] = $data['email'] ;
		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

	/*****************************************/

	/************For Registration*************/
		$this->load->language('common/footer');
		$data['text_mob_pop'] = $this->language->get('text_pop_mob_for_whatsapp');
		$data['entry_whatsapp_telephone'] = $this->language->get('entry_whatsapp_telephone');

		$data['heading_text_register'] = $this->language->get('heading_text_register');
		$data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', 'SSL'));
		$data['text_your_details'] = $this->language->get('text_your_details');
		$data['text_your_address'] = $this->language->get('text_your_address');
		$data['text_your_password'] = $this->language->get('text_your_password');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_email_address'] = $this->language->get('entry_email_address');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_confirm'] = $this->language->get('entry_confirm');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['entry_referred'] = $this->language->get('entry_referred');

		if(isset($this->request->get['is_dropshipper'])){
			$data['is_dropshipper'] = $this->request->get['is_dropshipper'];
			$this->is_dropshipper = 2;
		}else {
			$data['is_dropshipper'] = 0;
		}

		//	echo $_GET['is_dropshipper']; die;

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
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

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['address_1'])) {
			$data['error_address_1'] = $this->error['address_1'];
		} else {
			$data['error_address_1'] = '';
		}

		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}

		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}

		if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$data['error_zone'] = $this->error['zone'];
		} else {
			$data['error_zone'] = '';
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

		//$data['action'] = $this->url->link('account/login&static=login', '', 'SSL');

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name']; //$this->request->post['firstname'];
		} else {
			$data['name'] = '';
		}
		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname']; //$this->request->post['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = '';
		}

		/*
		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $arrName[0]; //$this->request->post['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = '';
		}
		*/
		/*
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}
		*/
		if (isset($this->request->post['reg_email'])) {
			$data['email'] = $this->request->post['reg_email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['reg_telephone'])) {
			$data['telephone'] = $this->request->post['reg_telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} else {
			$data['company'] = '';
		}

		if (isset($this->request->post['address_1'])) {
			$data['address_1'] = $this->request->post['address_1'];
		} else {
			$data['address_1'] = '';
		}

		if (isset($this->request->post['address_2'])) {
			$data['address_2'] = $this->request->post['address_2'];
		} else {
			$data['address_2'] = '';
		}

		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} elseif (isset($this->session->data['shipping_address']['postcode'])) {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		} elseif (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

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

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}
	/*****************************************/

	/***********For Forget Password************/
		$data['heading_text_forget_password'] = $this->language->get('heading_text_forget_password');

		$data['text_your_email'] = $this->language->get('text_your_email');
		$data['text_email'] = $this->language->get('text_email');

		$data['entry_email'] = $this->language->get('entry_email');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		//$data['action'] = $this->url->link('account/login&static=login', '', 'SSL');

		//$data['back'] = $this->url->link('account/login', '', 'SSL');
	/*****************************************/
//		$data['action'] = $this->url->link('account/login&static=login', '', 'SSL');
        $data['action'] = $this->url->link('account/login', '', 'SSL');
        $data['reg_action'] = $this->url->link('account/login#sign_up', '', 'SSL');
        //$data['forgot_action'] = $this->url->link('account/login', $url, 'SSL');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/login.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/login.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/account/login.tpl', $data));
        }
    }

    public function loginValidation() {
        $this->event->trigger('pre.customer.login');
        //$this->load->model('account/customer');
        //$this->load->language('account/login');
        // Check how many login attempts have been made.

        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {

                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

                $this->event->trigger('post.customer.login');
                $this->load->model('lead/lead');
                $telephone = $this->customer->getTelephone();
                if (!empty($telephone)) {
                    $lead_data = [
                        'last_login_date' => date('Y-m-d H:i:s')
                    ];
                    $this->model_lead_lead->updateLead($lead_data, $telephone, 'Last Login', $this->customer->getId());
                }
            }
        }
        return !$this->error;
    }

    public function registerValidation() {

        if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        /*
          if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
          $this->error['firstname'] = $this->language->get('error_firstname');
          }

          if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
          $this->error['lastname'] = $this->language->get('error_lastname');
          }
         */

        /*
          if(!empty($this->request->post['email'] )) {
          if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
          $this->error['email'] = $this->language->get('error_email');
          }
          if ($this->model_account_customer->getTotalCustomersByEmailToValidate($this->request->post['email'])) {
          $this->error['warning'] = $this->language->get('error_exists');
          }
          }
         */
// this is validation for same email in registration page
        if (!empty($this->request->post['reg_email'])) {
            if ((utf8_strlen($this->request->post['reg_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['reg_email'])) {
                $this->error['email'] = $this->language->get('error_email');
            }
            if ($this->model_account_customer->getTotalCustomersByEmailToValidate($this->request->post['reg_email'])) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        }


        // if ((utf8_strlen($this->request->post['reg_telephone']) < 10) || (utf8_strlen($this->request->post['reg_telephone']) > 10)) {
        // 	$this->error['telephone'] = $this->language->get('error_telephone');
        // }

        if ((int) (is_numeric($this->request->post['reg_telephone'])) == 0 || utf8_strlen($this->request->post['reg_telephone']) != 10) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if (!empty($this->request->post['reg_telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($this->request->post['reg_telephone'])) {
            $this->error['warning'] = $this->language->get('error_exists_phone');
        }
        /*
          if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
          $this->error['address_1'] = $this->language->get('error_address_1');
          }

          if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
          $this->error['city'] = $this->language->get('error_city');
          }

          $this->load->model('localisation/country');

          $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

          if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
          $this->error['postcode'] = $this->language->get('error_postcode');
          }

          if ($this->request->post['country_id'] == '') {
          $this->error['country'] = $this->language->get('error_country');
          }

          if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
          $this->error['zone'] = $this->language->get('error_zone');
          }
         */


        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        // Agree to terms
        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info && !isset($this->request->post['agree'])) {
                $this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
            }
        }

        return !$this->error;
    }

    public function forgotValidation() {
        if (!isset($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        } elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        } else if (empty($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        }

        return !$this->error;
    }

    /*
     * send mail or sms by email or mobile number by vikas
     *
     */

    public function send_mail_or_sms($email, $message, $subject, $password) {
        $this->load->language('account/sms_templates');
        $this->load->language('account/forgotten');
        if (is_numeric($email)) {
            $send_sms = new SMS($message, $email);
            $send_sms->sendMessage();
            $this->session->data['success'] = sprintf($this->language->get('text_mobile_success'), $email);
        } else {

            $message = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
            $message .= $this->language->get('text_password') . "\n\n";
            $message .= $password;

            $mail = new PHPMailer();
            $mail->isSMTP();
            // $mail->SMTPDebug = 2;
            // $mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->SMTPSecure = 'ssl';
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addReplyTo($this->config->get('config_email'), 'WholesaleBox');

            // $mail->addAddress($this->config->get('config_email'), 'WholesaleBox');
            $mail->addAddress($email);
            $mail->addBCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();


            // $transport = Swift_SmtpTransport::newInstance($this->config->get('config_mail_smtp_hostname'), $this->config->get('config_mail_smtp_port'));
            // $transport->setUsername($this->config->get('config_mail_smtp_username'));
            // $transport->setPassword(html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'));
            // // Create the Mailer using your created Transport
            // $mailer = Swift_Mailer::newInstance($transport);
            // $mail = Swift_Message::newInstance();
            // $mail->setSubject($subject);
            //   // Set the From address with an associative array
            // // $mail->setSender(array($this->config->get('config_email') => html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
            // $mail->setFrom(array($this->config->get('config_email') => html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8') ));
            //   // Set the To addresses with an associative array
            // $mail->setTo($email);
            // $mail->setBody($message);
            // $numsent = $mailer->send($mail);
            // $mail = new PHPMailer();
            // $mail->protocol = $this->config->get('config_mail_protocol');
            // $mail->parameter = $this->config->get('config_mail_parameter');
            // $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            // $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            // $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            // $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            // $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
            // $mail->setTo($email);
            // $mail->setFrom($this->config->get('config_email'));
            // $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            // $mail->setSubject($subject);
            // $mail->setText($message);
            // $mail->send();
            $this->session->data['success'] = sprintf($this->language->get('text_success'), $email);
        }
    }

    /**
     * Ajax Login
     */
    public function ajaxLogin() {

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->loginForm()) {
            unset($this->session->data['guest']);

            // Default Shipping Address
            $this->load->model('account/address');

            if ($this->config->get('config_tax_customer') == 'payment') {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            if ($this->config->get('config_tax_customer') == 'shipping') {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            $data['actionlogin'] = $this->url->link('account/login', '', 'SSL');

            // Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
            if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
                $data['redirect'] = str_replace('&amp;', '&', $this->request->post['redirect']);
            } else {
                $data['redirect'] = $this->url->link('account/order', '', 'SSL');
            }
        } else {
            //print errors
            $data['errors'] = $this->error['warning'];
        }

        echo json_encode($data);
    }

    /**
     * [sendMessageOnRegistration description]
     * @param  string $mobile mobile number on which sms is sent
     * @param  [type] $passwd password of the registered person
     * @param  string $otp    otp number
     * @return void
     * @author Parth Gupta
     */
    public function sendMessageOnRegistration($mobile, $passwd, $otp) {
        $this->load->language('account/sms_templates');
        $otp_encoded = base64_encode($otp);
        $login_id = $mobile;
        $base = "www.wholesalebox.in/";
        $message = $this->language->get('on_csv_signup');
        $preferred_url = $base . 'preferred/' . $otp_encoded;

        $msg = sprintf($message, $login_id, $passwd, $preferred_url);

        $csv_sms = new SMS($msg, $mobile);
        $csv_sms->sendMessage();
    }

}
