<?php
class ControllerInformationInformation extends Controller {
	private $error = array();
	public function index() {
		$this->load->language('information/information');
		$this->load->model('catalog/information');
		 $this->load->model('tool/image');

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
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		
		 $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {

			$this->document->setTitle($information_info['meta_title']);
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);

			$data['heading_title'] = $information_info['title'];

			$data['button_continue'] = $this->language->get('button_continue');

			$data['description_data'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = $this->url->link('common/home');

		  
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
		  if(CONFIG_IS_MOBILE == 1)
           {	
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		   }	

		    $data['title'] = $this->document->getTitle();
            $data['social_meta_tags'] = $this->document->getSocialMetaTags();
            $data['base'] = $server;
		    $data['description'] = $this->document->getDescription();
		    $data['keywords'] = $this->document->getKeywords();
	    	$data['links'] = $this->document->getLinks();
		    $data['styles'] = $this->document->getStyles();

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/information.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/information.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/information/information.tpl', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		  
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
		   if(CONFIG_IS_MOBILE == 1)
            {
			  $data['footer'] = $this->load->controller('common/footer');
			  $data['header'] = $this->load->controller('common/header');
		    }	
          	$data['title'] = $this->document->getTitle();
            $data['social_meta_tags'] = $this->document->getSocialMetaTags();
            $data['base'] = $server;
		    $data['description'] = $this->document->getDescription();
		    $data['keywords'] = $this->document->getKeywords();
	    	$data['links'] = $this->document->getLinks();
		    $data['styles'] = $this->document->getStyles();

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			}
		}
	}

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		 // seller info
		$this->load->model('seller_panel/profile');
		$fields = "company, zone_id,gst_provisional_id, primary_contact_name, city, email, address1, address2";
        $seller_id = $this->customer->getId();
        $result = $this->model_seller_panel_profile->getSellersInformation($seller_id,$fields);

		 if(!empty($result['zone_id']))
          {
           $information_info = str_replace("[COMPANY_GST]", WSB_STATE_GST[$result['zone_id']], $information_info);
          }
          else
          {
             $information_info = str_replace("[COMPANY_GST]", '.............', $information_info);
           }

          if(!empty($result['gst_provisional_id']))
          {
            $information_info = str_replace("[SELLER_GST]", $result['gst_provisional_id'], $information_info);
          }
          else
          {
             $information_info = str_replace("[SELLER_GST]", '.............', $information_info);
          }

          if(!empty($result['primary_contact_name']))
          {
             $information_info = str_replace("[SELLER_NAME]", $result['primary_contact_name'], $information_info);
           }
          else
           {
              $information_info = str_replace("[SELLER_NAME]", '..................', $information_info);
            }

           if(!empty($result['company']))
            {
              $information_info = str_replace("[SELLER_COMPANY]", $result['company'], $information_info);
             }
           else
            {
               $information_info = str_replace("[SELLER_COMPANY]", '..................', $information_info);
            }


		    if(!empty($result['address1']))
		    {
		      $information_info = str_replace("[SELLER_ADDRESS]", $result['address1'].' '.$result['address2'].' '.$result['city'], $information_info);
		    }
		    else
		    {
		     $information_info = str_replace("[SELLER_ADDRESS]", '........................', $information_info);
		    }

			if(!empty($result['email']))
			 {
			    $information_info = str_replace("[SELLER_EMAIL]", $result['email'], $information_info);
			}
			else
			 {
			   $information_info = str_replace("[SELLER_ADDRESS]", '.....................', $information_info);
			}

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		echo $output; die;
	}
	
	public function campustalenthunt(){

		$this->load->model('catalog/campustalenthunt');
		$this->load->language('information/information');
		$data = array();
		$data['heading_title'] = $this->language->get('text_campushunt');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data['campuses_list'] = $this->model_catalog_campustalenthunt->getCampuses();


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->dataValidate() )
		{
				//echo $html;

				//include_once()
				$mail = new PHPMailer();

				$mail->isSMTP();
				//$mail->SMTPDebug = 2;
				//$mail->Debugoutput = 'html';
				$mail->Host = $this->config->get('config_mail_smtp_hostname');
				$mail->Port = $this->config->get('config_mail_smtp_port');
				$mail->SMTPSecure = 'ssl';
				$mail->SMTPAuth = true;
				$mail->Username = $this->config->get('config_mail_smtp_username');
				$mail->Password = $this->config->get('config_mail_smtp_password');
				$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
				$mail->addReplyTo('wsbcampus@gmail.com', 'Wholesale Box');

				$mail_data = array();

				$team_id = $this->model_catalog_campustalenthunt->createTeam($this->request->post['team_name'], 5000);

				$mail_data['team_name'] = $this->request->post['team_name'];
				$mail_data['city'] = $this->request->post['city'];
				$mail_data['campus_name'] = $this->request->post['campus']; //$this->model_catalog_campustalenthunt->getCampusName($this->request->post['campus']);
				$mail_data['team_id'] = $team_id;
				$mail_data['members_list'] = '';
				$members_email = array();
				// echo '<pre>';print_r($this->request->post); exit;
				for ($i = 0; $i < count($this->request->post['name']); $i++) {

					$rs['name'] = $this->request->post['name'][$i];
					$arr_name = explode(" ", $rs['name']);
					$mail_data['fname'] =  $arr_name[0];
					$rs['email'] = $this->request->post['email'][$i];
					$rs['contact'] = $this->request->post['contact'][$i];
					$members_email[] = $rs['email'];
					$members_name[] = $rs['name'];

					$rs['internship_company'] = $this->request->post['internship_company'][$i];
					$rs['grade_point'] = $this->request->post['grade_point'][$i];
					$rs['internship_role'] = $this->request->post['internship_role'][$i];
					$rs['desired_profile'] = $this->request->post['desired_profile'][$i];
					$rs['city'] = $this->request->post['city'];
					$rs['team_name'] = $this->request->post['team_name'];
					$rs['campus'] = $this->request->post['campus'];
					$rs['team_id'] = $team_id;

					$j = $i +1;
					$rs['team_member_id'] = $team_id."-".$j;

					$mail_data['members_list'] .= '
												<div style="margin:20px 0px 0px 0px;color:#333333;font:normal 13px Arial,Helvetica,sans-serif;font-size:16px">
													<b>Member'.$j.'</b>
												</div>

											<span style="font-size:14px">
												Member id: '.$rs['team_member_id'].'
											</span>
											<br>
												<span style="font-size:14px">
													Name: '.$rs['name'].'
												</span>
											<br>
												<span style="font-size:14px">
													Email: '.$rs['email'].'
												</span>
										<br>
											<span style="font-size:14px">
												Mobile: '.$rs['contact'].'
											</span>
										<br >
										<span style="font-size:14px">
												Internship Company: '.$rs['internship_company'].'
											</span>
										<br>
											<span style="font-size:14px">
												Grade Point: '.$rs['grade_point'].'
											</span>
										<br>
											<span style="font-size:14px">
												Internship Role: '.$rs['internship_role'].'
											</span>
										<br>
											<span style="font-size:14px">
												Desired Profile: '.$rs['desired_profile'].'
											</span>


										<div style="clear:both;border-bottom:#ececec solid 1px; margin:20px 0;"></div>';

					$this->model_catalog_campustalenthunt->campustalenthunt($rs);



				}



			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/campus_talent_hunt.tpl')) {
				$html = $this->load->view($this->config->get('config_template') . '/template/mail/campus_talent_hunt.tpl', $mail_data);
			} else {
				$html = $this->load->view('default/template/mail/campus_talent_hunt.tpl', $mail_data);
			}


			$mail->addAddress('wsbcampus@gmail.com', 'WholesaleBox');
			$mail->Subject = 'New signup for Campus Talent Hunt 2015';
			$mail->msgHTML($html);

			//send to admin;
			$mail->send();


			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/campus_talent_hunt_to_member.tpl')) {
				$html_member = $this->load->view($this->config->get('config_template') . '/template/mail/campus_talent_hunt_to_member.tpl', $mail_data);
			} else {
				$html_member = $this->load->view('default/template/mail/campus_talent_hunt_to_member.tpl', $mail_data);
			}
			//foreach($members_email as $member) {

			$mail->addAddress($members_email[0], $members_name[0]);
			$mail->addCC($members_email[1], $members_name[1]);
			$mail->addCC($members_email[2], $members_name[2]);
			$mail->addCC($members_email[3], $members_name[3]);
			$mail->Subject = 'WholesaleBox - Signup for Campus Talent Hunt 2015';
			$mail->msgHTML($html_member);

			$mail->send();
			//}
			//redirect
			$this->response->redirect($this->url->link('information/information/thankyou', '', 'SSL'));

	
		}

		for($i=0;$i<4;$i++) {
			if (isset($this->error['name'][$i])) {
				$data['error_name_'.$i] = $this->error['name'][$i];
			} else {
				$data['error_name_'.$i] = '';
			}

			if (isset($this->error['email'][$i])) {
				$data['error_email_'.$i] = $this->error['email'][$i];
			} else {
				$data['error_email_'.$i] = '';
			}

			if (isset($this->error['contact'][$i])) {
				$data['error_contact_'.$i] = $this->error['contact'][$i];
			} else {
				$data['error_contact_'.$i] = '';
			}
		}

		if (isset($this->error['team_name'])) {
			$data['error_team_name'] = $this->error['team_name'];
		} else {
			$data['error_team_name'] = '';
		}

		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}

		if (isset($this->error['campus'][$i])) {
			$data['error_campus'] = $this->error['campus'];
		} else {
			$data['error_campus'] = '';
		}

		//echo '<pre>'; print_r($this->error); echo '</pre>';
		//Data after submission
		for ($i = 0; $i < 4; $i++) {
			if (isset($this->request->post['name'][$i])) {
				$data['name'][$i] = $this->request->post['name'][$i];
			} else {
				$data['name'][$i] = '';
			}

			if (isset($this->request->post['email'][$i])) {
				$data['email'][$i] = $this->request->post['email'][$i];
			} else {
				$data['email'][$i] = '';
			}

			if (isset($this->request->post['contact'][$i])) {
				$data['contact'][$i] = $this->request->post['contact'][$i];
			} else {
				$data['contact'][$i] = '';
			}

			if (isset($this->request->post['internship_company'][$i])) {
				$data['internship_company'][$i] = $this->request->post['internship_company'][$i];
			} else {
				$data['internship_company'][$i] = '';
			}

			if (isset($this->request->post['internship_role'][$i])) {
				$data['internship_role'][$i] = $this->request->post['internship_role'][$i];
			} else {
				$data['internship_role'][$i] = '';
			}

			if (isset($this->request->post['grade_point'][$i])) {
				$data['grade_point'][$i] = $this->request->post['grade_point'][$i];
			} else {
				$data['grade_point'][$i] = '';
			}

			if (isset($this->request->post['desired_profile'][$i])) {
				$data['desired_profile'][$i] = $this->request->post['desired_profile'][$i];
			} else {
				$data['desired_profile'][$i] = '';
			}
		}

		if (isset($this->request->post['team_name'])) {
			$data['team_name'] = $this->request->post['team_name'];
		} else {
			$data['team_name'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->request->post['campus'])) {
			$data['campus'] = $this->request->post['campus'];
		} else {
			$data['campus'] = '';
		}


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/campustalenthunt.tpl'))
		{
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/campustalenthunt.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/information/campustalenthunt.tpl', $data));
		}

	}

	public function dataValidate(){
		$this->load->model('catalog/campustalenthunt');
		for($i=0; $i < count($this->request->post['name']); $i++) {
			//echo $this->request->post['name'][$i]."--".$i;

			if (empty(trim($this->request->post['name'][$i]))) {

				$this->error['name'][$i] = 'Enter your name';

			} else {

				// check if name only contains letters and whitespace
				if (!preg_match("/^[a-zA-Z ]*$/",$this->request->post['name'][$i])) {
					$this->error['name'][$i] = 'Please enter valid name';
				}

			}

			if (empty($this->request->post['email'][$i])) {
				$this->error['email'][$i] = 'Enter your email';
			}elseif($this->model_catalog_campustalenthunt->getMemberByEmailToValidate($this->request->post['email'][$i])){
				$this->error['email'][$i] = 'Email address is already exist';
			} else {
				// check if e-mail address is well-formed
				if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/",$this->request->post['email'][$i])) {
					$this->error['email'][$i] = 'Please enter valid email';

				}
			}

			if (empty($this->request->post['contact'][$i])) {
				$this->error['contact'][$i] = "Enter your mobile number";
			}elseif($this->model_catalog_campustalenthunt->getMemberByPhoneToValidate($this->request->post['contact'][$i])){
				$this->error['contact'][$i] = 'Phone number is already exist';
			} else {
				// check if contact is well-formed
				if (preg_match('/^0\d{9}$/',$this->request->post['contact'][$i]) || strlen($this->request->post['contact'][$i]) < 10 || strlen($this->request->post['contact'][$i]) > 10) {
					$this->error['contact'][$i] = 'Mobile number is not valid';

				}
			}


		}


		if (empty($this->request->post['team_name'])) {
			$this->error['team_name'] = 'Please enter team name';
			
		}


		if (empty($this->request->post['city'])) {
			$this->error['city'] = 'Please enter city';

		}

		if (empty($this->request->post['campus'])) {
			$this->error['campus'] = 'Please enter college name';

		}

		return !$this->error;

		
	}

	public function thankyou(){
		$this->load->language('information/information');
		$data = array();
		$data['heading_title'] = $this->language->get('text_campushunt');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/thankyou.tpl'))
		{
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/thankyou.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/information/thankyou.tpl', $data));
		}
	}

	/**
	 *
	 */
	public function set_set_type(){
		$this->load->model('catalog/information');
		$results = $this->model_catalog_information->setProductTypeSet();


	}

	/**
	 *
	 */
	public function test(){

		//include_once()
		$mail = new PHPMailer();

		$mail->isSMTP();
		$mail->SMTPDebug = 2;
		$mail->Debugoutput = 'html';
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'ssl';
	    $mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
	    $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
		$mail->addReplyTo('wsbcampus@gmail.com', 'Wholesale Box');
		$mail->addAddress('rakesh.shekhawat@gmail.com', 'John Doe');
		$mail->addCC('ravindra.shekhawat.rajnota@gmail.com', 'Ravindra Doe');
		$mail->Subject = 'PHPMailer SMTP test 22222222';
		$mail->msgHTML('hello, how are you');

		$mail->send();
	}
}