<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
class ControllerRestapiRestapi extends Controller {
	public $registry;
	private $_limit_in_days_for_replacement = 15;
	private $_limit_in_days_for_quality = 4;
	private $_quality_return_action_id = array(2, 3, 4);
	private $_franchise_id = 0;
	private $_franchise_margin = 0;
	private $_request_from;
	private $_banner_splitter = "__";

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->__getFranchiseDetailsFromHeader();
	}

	/***
		 * @function generate_access_token
	*/
	public function generate_access_token() {
		$length = 16;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	/**
	 * Function : customer_signup
	 * Request Parameters : mobile,device_id(V2 email,password,action(login or signup),version)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"User has been created successfully"}
	 **/
	public function customer_signup() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$this->load->model('restapi/service');
			$this->load->model('account/customer');
			$rt = array();

			$this->validateApiCall();
			$international_request = 0;
			if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
				$international_request = 1;
			}

			$headers = getallheaders();
			$request_by = $headers['REQUEST_BY'] ?? "";
			$app_version_code = $request['app_version_code'] ?? 0;

			if (isset($request['mobile'])) {
				//resetting login attempt counter Parth
				if (isset($request['device_id'])) {
					$mobile = $request['mobile'];
					if (isset($request['password'])) {
						$password = $request['password'];
					} else {
						$password = '';
					}
					if (isset($request['email'])) {
						$email = $request['email'];
					} else {
						$email = '';
						$this->model_account_customer->deleteMobileLoginAttempts($request['mobile']);
					}
					if (isset($request['action'])) {
						$action = $request['action'];
					} else {
						$action = 'signup';
					}
					$device_id = trim($request['device_id']);
					if (!empty($mobile)) {
						$get_customer = $this->model_restapi_service->getCustomerByMobile($mobile);
					}
					if (empty($get_customer) && !empty($email)) {
						$get_customer = $this->model_restapi_service->getCustomerByEmail($email);
					}
					/*
						* check master customer id si set or not
					*/
					if (isset($request['master_customer_id']) && $request['master_customer_id'] > 0) {

						/*
							* check this customer has a master id
						*/
						$hasMasterCustomerId = $this->model_account_customer->checkCustomerHasMasterId($request['master_customer_id']);

						if (!$hasMasterCustomerId) {

							$request['master_id'] = $hasMasterCustomerId;
						} else {
							$request['master_id'] = $request['master_customer_id'];
						}
					}

					if (!empty($get_customer)) {
						if ($action == "signup") {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'You are already registered.';
							$rt['next_action'] = '';

						} else {
							if (!empty($mobile)) {
								$check_customer = $this->model_restapi_service->checkCustomerByMobileAndPassword($mobile, $password);
							} else {
								$check_customer = $this->model_restapi_service->checkCustomerByEmailAndPassword($email, $password);
							}
							if ($check_customer == 0) {
								$rt['error_code'] = '1001';
								$rt['status'] = '0';
								$rt['status_text'] = 'failed';
								$rt['message'] = 'Incorrect login credential.';
								$rt['next_action'] = '';
							} elseif ($check_customer == 'not_registered') {
								$rt['error_code'] = '1011';
								$rt['status'] = '0';
								$rt['status_text'] = 'User not registered';
								$rt['message'] = 'You are not registered, Kindly register first';
								$rt['next_action'] = '';
							} else {
								if (isset($get_customer['company']) && $get_customer['company'] != '') {
									$company = $get_customer['company'];
								} else {
									$company = '';
								}
								//$get_customer = $check_customer; ## No need of this line. Due to this login not happen for international users.
								$customer_id = $get_customer['customer_id'];
								$access_token = $this->generate_access_token();
								//$access_token = substr(substr( "abcdefghijklmnopqrstuvwxyz" ,mt_rand( 0 ,25 ) ,1 ) .substr( md5(16) ,1 ),0,16);//rand(1000000,rand(0,10000000));
								$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, (string) $access_token, '0');
								if ($rs == 1) {
									// Update location finder description during login and signup
									if (isset($request['locationFinderMethod'])) {
										$this->model_restapi_service->updateLocationFinderText($customer_id, '0', $request['locationFinderMethod']);
									}

									/*** Check if FSE is trying to log in to customer's account
                       FSE not allowed to login in to other customer's account ***/

									$sales_agent = null;
									$login_by_sales_agent = 0;
									$sales_agent_self_account = 0;
									if (!empty($device_id)) {
										$sales_agent = $this->model_restapi_service->getSalesStaffWithDeviceId($device_id);
										if (!empty($sales_agent)) {
											$login_by_sales_agent = 1; // FSE is trying to login in via his device
											if ($mobile == $sales_agent['telephone']) {
												$sales_agent_self_account = 1; // FSE is trying to login in his account => (allow access)
											}
										}
									}

									if ($login_by_sales_agent && !$sales_agent_self_account) {
										$rt['error_code'] = '1001';
										$rt['status'] = '0';
										$rt['status_text'] = 'failed';
										$rt['message'] = "Not allowed to login.";
										$rt['popup_message'] = "Why are you trying to Login from another number? For any help, contact Head Office Sales Coordinator!";
										$rt['next_action'] = '';
									} else {
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
										$rt['message'] = 'User logged in successfully.';
										$rt['data']['customer_id'] = $customer_id;
										$rt['data']['new_country'] = $get_customer['country_code'];
										$rt['data']['access_token'] = $access_token;
										$rt['data']['gcm_id'] = $get_customer['ws_gcm_registration_id'];
										$rt['data']['name'] = $get_customer['firstname'] . ' ' . $get_customer['lastname'];
										$rt['data']['email'] = $get_customer['email'];
										$rt['data']['mobile'] = $get_customer['telephone'];
										$rt['data']['customer_type'] = $get_customer['customer_type_id'];
										$rt['data']['customer_types'] = $this->model_restapi_service->getCustomerTypes();
										$rt['data']['business_name'] = $company;
										$rt['data']['gst_number'] = $get_customer['gst_number'];

										$arr_address = $this->model_restapi_service->getDefaultAddress((int) $customer_id);
										$rt['data']['address'] = $arr_address['address_1'];
										$rt['data']['address_2'] = $arr_address['address_2'];
										$rt['data']['pin_code'] = $arr_address['postcode'];
										$rt['data']['city'] = $arr_address['city'];
										$rt['data']['zone_id'] = $arr_address['zone_id'];
										$rt['data']['country_id'] = $arr_address['country_id'];

										$rt['next_action'] = 'open_main';
										if (!$international_request) {
											if (empty($get_customer['customer_type_id'])
												|| (empty($get_customer['telephone']))
												|| (strtoupper($request_by) == "ANDROID_APP"
													&& $app_version_code > 89
													&& isset($get_customer['customer_type_id'])
													&& strpos($get_customer['customer_type_id'], '2') !== false
													&& (empty($arr_address['address_1'])))) {
												$rt['next_action'] = 'open_profile';
											}
										}

										$this->logForTest('customer_signup', $inputJSON, $rt);
									}
								}
							}
						}
					} else {
						if ($action == "login") {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'User not registered';
							$rt['message'] = 'You are not registered yet, Kindly register first';
							$rt['next_action'] = '';
						} else {
							$check = TRUE;
							$store_id = $this->config->get('config_store_id');
							if ($store_id == 0 || $store_id == 9) {
								if (!preg_match('/^[0-9]{10}+$/', $mobile)) {
									$rt['error_code'] = '1001';
									$rt['status'] = '0';
									$rt['status_text'] = 'Mobile No is invalid';
									$rt['message'] = 'Mobile no is invalid, Please enter valid mobile no';
									$rt['next_action'] = '';
									$check = FALSE;
								}
							}

							$emailval = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
							if (!empty($email) && !preg_match($emailval, $email)) {
								$rt['error_code'] = '1001';
								$rt['status'] = '0';
								$rt['status_text'] = 'Email is invalid';
								$rt['message'] = 'Email is invalid, Please enter valid Email';
								$rt['next_action'] = '';
								$check = FALSE;
							} else {
								if ($action == "signup") {
									$check = TRUE;
								}
							}

							if ($check == TRUE) {
								$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
								$access_token = $this->generate_access_token();
								//$access_token = substr(substr( "abcdefghijklmnopqrstuvwxyz" ,mt_rand( 0 ,25 ) ,1 ) .substr( md5(16) ,1 ),0,16);//rand(1000000,rand(0,10000000));
								$request['mobile'] = $mobile;
								$request['email'] = $email;
								$request['password'] = $password;
								$request['device_id'] = $device_id;
								$request['otp'] = $otp;
								$request['access_token'] = $access_token;

								if (isset($request['gcm_id'])) {
									$request['gcm_id'] = $request['gcm_id'];
								} else {
									$request['gcm_id'] = "";
								}

								$customer_id = $this->model_restapi_service->addCustomer($request);
								if ($customer_id) {
									// Update location finder description during login and signup
									if (isset($request['locationFinderMethod'])) {
										$this->model_restapi_service->updateLocationFinderText((int) $customer_id, '0', $request['locationFinderMethod']);
									}
									$rt['status'] = '1';
									$rt['status_text'] = 'Success';
									$rt['message'] = 'User has been created successfully.';
									$rt['data']['customer_id'] = $customer_id;
									$rt['data']['access_token'] = $access_token;
									$rt['data']['name'] = '';
									$rt['data']['email'] = '';
									$rt['data']['mobile'] = '';
									$rt['data']['customer_type'] = '';
									$rt['data']['customer_types'] = $this->model_restapi_service->getCustomerTypes();
									$rt['data']['business_name'] = '';

									$arr_address = $this->model_restapi_service->getDefaultAddress((int) $customer_id);
									$rt['data']['address'] = $arr_address['address_1'];
									$rt['data']['address_2'] = $arr_address['address_2'];
									$rt['data']['pin_code'] = $arr_address['postcode'];
									$rt['data']['city'] = $arr_address['city'];
									$rt['data']['zone_id'] = $arr_address['zone_id'];
									$rt['data']['country_id'] = $arr_address['country_id'];
									$rt['next_action'] = 'open_main';
									if (!$international_request) {
										$rt['next_action'] = 'open_profile';
									}

									$this->logForTest('customer_signup', $inputJSON, $rt);

								} else {
									$rt['error_code'] = '1001';
									$rt['status'] = '0';
									$rt['status_text'] = 'failed';
									$rt['message'] = 'User creation has been failed.';
									$rt['next_action'] = '';
								}
							}
						}
					}
				} else {
					$rt['error_code'] = '1001';
					$rt['status'] = '0';
					$rt['status_text'] = 'Signup failed';
					$rt['message'] = 'http request does not have device id.';
					$rt['next_action'] = '';
				}
			} else {
				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have mobile.';
				$rt['next_action'] = '';
			}

			echo json_encode($rt);exit;
		}
	}

	/**
	 * Function : send_otp
	 * Request Parameters : reg_telephone,country_code version(2)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Mobile no 9772737137 accepted. Otp sent on this number."}
	 * */
	public function send_otp() {

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			if (empty($request['action'])) {$request['action'] = 'sign_up';}

			$app_version_code = $request['app_version_code'];

			$this->load->model('restapi/service');
			$this->load->language('account/sms_templates');
			$this->load->language('account/login');

			$result = array('status' => 0, 'status_text' => 'failed');

			$this->validateApiCall();

			$emailval = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
			$check = True;

			$international_request = 0;
			if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
				$international_request = 1;
			}

			if (empty($request['country_code'])) {
				$result['error_code'] = '1001';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'country code is invalid.';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (empty($request['mobile'])) {
				$result['error_code'] = '1002';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'Please enter mobile or email';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!empty($request['mobile']) && is_numeric($request['mobile']) && !preg_match('/^[0-9]{10}+$/', $request['mobile'])) {
				$result['error_code'] = '1003';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'Mobile no is invalid, Please enter valid mobile no';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!empty($request['mobile']) && !is_numeric($request['mobile']) && !preg_match($emailval, $request['mobile'])) {
				$result['error_code'] = '1004';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'Email is invalid, Please enter valid Email';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!empty($request['mobile'])) {
				if (is_numeric($request['mobile'])) {
					$user_data = $this->model_restapi_service->checkCustomerByMobile($request['mobile']);
				} else {
					$user_data = $this->model_restapi_service->checkCustomerByEmail($request['mobile']);
				}

				if ($check && $user_data > 0 && $request['action'] == 'sign_up') {
					$result['is_register'] = 1;
					$result['status_text'] = 'Success';
					$result['status'] = 1;
					$result['message'] = 'Please enter password to login.';
					$result['next_action'] = 'password';
					$check = FALSE;
				}
				if ($check && $user_data == 0 && $request['action'] == 'forgot_password') {
					$result['error_code'] = '1005';
					$result['status_text'] = 'Failed';
					$result['status'] = 0;
					$result['message'] = 'Data not found in database!';
					$result['next_action'] = '';
					$check = FALSE;
				}

				if ($check && $international_request && $app_version_code >= 75) {
					if ($user_data == 0 && $request['action'] == 'sign_up') {
						$check = FALSE;
						$result['status'] = 1;
						$result['status_text'] = 'Success';
						$result['message'] = "Please enter a new password.";
						$result['is_register'] = 0;
						$result['next_action'] = 'new_password';
					}
				}
			}
			if ($check) {
				$request['session_id'] = session_id();
				$request['ip'] = $this->request->getIpAddress;
				$request['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
				$request['otp'] = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
				$request['otp_page'] = $request['action'];
				$request['customer_id'] = 0;

				if (is_numeric($request['mobile'])) {
					$otp_data = $this->model_restapi_service->addOtpForMobile($request);
					if ($otp_data > 0) {$request['otp'] = $otp_data;}
					$message = $this->language->get('on_app_signup');
					$message = sprintf($message, $request['otp']);

					if (SITE_ENVIRONMENT == 'Production') {
						$sms = new SMS($message, $request['country_code'] . $request['mobile']);
						$sms->sendOTPMessage();
					}

					$msg = "Mobile number " . $request['mobile'] . " is accepted. OTP has been sent on this number.";
				} else {
					$otp_data = $this->model_restapi_service->addOtpForEmail($request);
					if ($otp_data > 0) {$request['otp'] = $otp_data;}
					$message = $this->language->get('on_app_signup');
					$message = sprintf($message, $request['otp']);
					$mail = new PHPMailer();
					$mail->isSMTP();
					$mail->Host = $this->config->get('config_mail_smtp_hostname');
					$mail->Port = $this->config->get('config_mail_smtp_port');
					$mail->SMTPSecure = 'ssl';
					$mail->SMTPDebug = 0;
					$mail->Debugoutput = 'html';
					$mail->SMTPAuth = true;
					$mail->Username = $this->config->get('config_mail_smtp_username');
					$mail->Password = $this->config->get('config_mail_smtp_password');
					$mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
					$mail->addAddress($request['mobile'], "WholesaleBox");
					$mail->Subject = html_entity_decode("OTP", ENT_QUOTES, 'UTF-8');
					$mail->msgHTML($message);
					$mail->send();
					$msg = "Email address " . $request['mobile'] . " is accepted. OTP has been sent on this email.";
				}

				$result['message_regex'] = ".*(\\d{4}).*";
				$result['message_regex_position'] = 1;
				$result['gateway_name'] = 'WHSBOX';
				$result['message'] = $msg;
				$result['status_text'] = 'Success';
				$result['status'] = 1;
				$result['is_register'] = ($request['action'] == 'forgot_password') ? 1 : 0;
				$result['next_action'] = 'otp';
			}

		}

		header('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Function : verify_otp
	 * Request Parameters : reg_telephone,country_code,otp version(2)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Mobile no 9772737137 accepted. Otp sent on this number."}
	 * */
	public function verify_otp() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$headers = getallheaders();
			$request_by = $headers['REQUEST_BY'] ?? "";
			$app_version_code = $request['app_version_code'] ?? 0;

			if (empty($request['action'])) {$request['action'] = 'sign_up';}
			$request['otp_page'] = $request['action'];
			$this->load->model('restapi/service');
			$this->load->language('account/sms_templates');
			$this->load->language('account/login');
			$result = array('status' => 0, 'status_text' => 'failed');

			$this->validateApiCall();
			$international_request = 0;
			if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
				$international_request = 1;
			}
			$emailval = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
			$check = True;

			$emailval = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
			$check = True;

			if (empty($request['country_code'])) {
				$result['error_code'] = '1001';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'country code is invalid.';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (empty($request['mobile'])) {
				$result['error_code'] = '1002';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'Please enter mobile or email';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!empty($request['mobile']) && is_numeric($request['mobile']) && !preg_match('/^[0-9]{10}+$/', $request['mobile'])) {
				$result['error_code'] = '1003';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'Mobile no is invalid, Please enter valid mobile no';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!empty($request['mobile']) && !is_numeric($request['mobile']) && !preg_match($emailval, $request['mobile'])) {
				$result['error_code'] = '1004';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'Email is invalid, Please enter valid Email';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!isset($request['device_id'])) {
				$result['error_code'] = '1005';
				$result['status'] = '0';
				$result['status_text'] = 'Signup failed';
				$result['message'] = 'http request does not have device id.';
				$result['next_action'] = '';
				$check = FALSE;
			}
			if (!isset($request['otp'])) {
				$result['error_code'] = '1006';
				$result['status'] = '0';
				$result['status_text'] = 'Failed';
				$result['message'] = 'http request does not have Verification Code.';
				$result['next_action'] = '';
				$check = FALSE;
			}

			if (!empty($request['mobile'])) {
				if (is_numeric($request['mobile'])) {
					$user_data = $this->model_restapi_service->getCustomerByMobile($request['mobile']);
				} else {
					$user_data = $this->model_restapi_service->getCustomerByEmail($request['mobile']);
				}

				if (count($user_data) > 0 && $request['action'] == 'sign_up') {
					$result['is_register'] = 1;
					$result['status_text'] = 'Failure';
					$result['status'] = 0;
					$result['message'] = 'You are already registered with ' . $request['mobile'];
					$result['next_action'] = '';
					$check = FALSE;

				}

				if (count($user_data) == 0 && $request['action'] == 'forgot_password') {
					$result['error_code'] = '1005';
					$result['status_text'] = 'Failed';
					$result['status'] = 0;
					$result['message'] = 'Data not found in database!';
					$result['next_action'] = '';
					$check = FALSE;
				}

			}

			if ($check) {
				$check_data = $this->model_restapi_service->checkOTP($request);
				if (count($check_data) == 0 && $request['action'] == 'sign_up') {
					$check_data = $this->model_restapi_service->checkMasterOTP($request);
				}

				if (count($check_data) > 0) {
					$this->model_restapi_service->verifyOTP($check_data);
					if ($check_data['otp_page'] == 'sign_up') {
						$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
						$access_token = $this->generate_access_token();
						$request['password'] = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
						$request['access_token'] = $access_token;
						if (isset($request['gcm_id'])) {
							$request['gcm_id'] = $request['gcm_id'];
						} else {
							$request['gcm_id'] = "";
						}

						$request['ip'] = $this->request->getIpAddress;

						$customer_id = $this->model_restapi_service->addCustomer($request);
						if ($customer_id) {
							// Update location finder description during login and signup
							if (isset($request['locationFinderMethod'])) {
								$this->model_restapi_service->updateLocationFinderText($customer_id, '0', $request['locationFinderMethod']);
							}

							$result['status'] = '1';
							$result['status_text'] = 'Success';
							$result['message'] = 'User has been created successfully.';
							$result['data']['customer_id'] = $customer_id;
							$result['data']['access_token'] = $access_token;
							$result['data']['name'] = '';
							$result['data']['email'] = !is_numeric($request['mobile']) ? $request['mobile'] : '';
							$result['data']['mobile'] = is_numeric($request['mobile']) ? $request['mobile'] : '';
							$result['data']['customer_type'] = '';
							$result['data']['customer_types'] = $this->model_restapi_service->getCustomerTypes();
							$result['data']['business_name'] = '';

							$arr_address = $this->model_restapi_service->getDefaultAddress((int) $customer_id);
							$result['data']['address'] = $arr_address['address_1'];
							$result['data']['pin_code'] = $arr_address['postcode'];
							$result['data']['city'] = $arr_address['city'];
							$result['data']['zone_id'] = $arr_address['zone_id'];
							$result['data']['country_id'] = $arr_address['country_id'];
							$result['next_action'] = 'open_profile';

							$this->logForTest('verify_otp', $inputJSON, $result);

						} else {
							$result['error_code'] = '1001';
							$result['status'] = '0';
							$result['status_text'] = 'failed';
							$result['message'] = 'User creation has been failed.';
							$result['next_action'] = '';
						}
					} else {
						$get_customer = $user_data;
						if (isset($get_customer['company']) && $get_customer['company'] != '') {
							$company = $get_customer['company'];
						} else {
							$company = '';
						}

						$customer_id = $get_customer['customer_id'];
						$access_token = $this->generate_access_token();
						$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, $access_token, '0');
						if ($rs == 1) {
							// Update location finder description during login and signup
							if (isset($request['locationFinderMethod'])) {
								$this->model_restapi_service->updateLocationFinderText((int) $customer_id, '0', $request['locationFinderMethod']);
							}

							/*** Check if FSE is trying to log in to customer's account
                 FSE not allowed to login in to other customer's account ***/

							$sales_agent = null;
							$login_by_sales_agent = 0;
							$sales_agent_self_account = 0;
							$device_id = trim(!empty($request['device_id']) ? $request['device_id'] : '');

							if (!empty($device_id)) {
								$sales_agent = $this->model_restapi_service->getSalesStaffWithDeviceId($device_id);

								if (!empty($sales_agent)) {
									$login_by_sales_agent = 1; // FSE is trying to login in via his device
									if ($request['mobile'] == $sales_agent['telephone']) {
										$sales_agent_self_account = 1; // FSE is trying to login in his account => (allow access)
									}
								}
							}

							if ($login_by_sales_agent && !$sales_agent_self_account) {
								$result['error_code'] = '1001';
								$result['status'] = '0';
								$result['status_text'] = 'failed';
								$result['message'] = "Not allowed to login.";
								$result['popup_message'] = "Why are you trying to Login from another number? For any help, contact Head Office Sales Coordinator!";
								$result['next_action'] = '';
							} else {
								$result['status'] = '1';
								$result['status_text'] = 'Success';
								$result['message'] = 'User logged in successfully.';
								$result['data']['customer_id'] = $customer_id;
								$result['data']['new_country'] = $get_customer['country_code'];
								$result['data']['access_token'] = $access_token;
								$result['data']['gcm_id'] = $get_customer['ws_gcm_registration_id'];
								$result['data']['name'] = $get_customer['firstname'] . ' ' . $get_customer['lastname'];
								$result['data']['email'] = $get_customer['email'];
								$result['data']['mobile'] = $get_customer['telephone'];
								$result['data']['customer_type'] = $get_customer['customer_type_id'];
								$result['data']['customer_types'] = $this->model_restapi_service->getCustomerTypes();
								$result['data']['business_name'] = $company;
								$result['data']['gst_number'] = $get_customer['gst_number'];
								$arr_address = $this->model_restapi_service->getDefaultAddress((int) $customer_id);
								$result['data']['address'] = $arr_address['address_1'];
								$result['data']['pin_code'] = $arr_address['postcode'];
								$result['data']['city'] = $arr_address['city'];
								$result['data']['zone_id'] = $arr_address['zone_id'];
								$result['data']['country_id'] = $arr_address['country_id'];
								$result['next_action'] = 'open_main';

								if (empty($get_customer['customer_type_id'])
									|| (!$international_request && empty($get_customer['telephone']))
									|| ($international_request && empty($get_customer['email']))
									|| (strtoupper($request_by) == "ANDROID_APP"
										&& $app_version_code > 89
										&& isset($get_customer['customer_type_id'])
										&& strpos($get_customer['customer_type_id'], '2') !== false
										&& (empty($arr_address['address_1'])))) {
									$result['next_action'] = 'open_profile';
								}
								if (!empty($request['action']) && $request['action'] == 'forgot_password' && $international_request) {
									$result['next_action'] = 'change_password';
								}

								$this->logForTest('verify_otp', $inputJSON, $result);
							}
						}
					}
				} else {
					$result['message'] = 'Invalid OTP';
					$result['status'] = 0;
					$result['status_text'] = 'failed';
					$result['next_action'] = '';
				}

			}

		}
		//unset($result['otp']);
		header('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Function : send_password_otp
	 * Request Parameters : reg_telephone,country_code version(2)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Mobile no 9772737137 accepted. Otp sent on this number."}
	 * */
/*	public function send_password_otp()
{

if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

$inputJSON = file_get_contents('php://input');
$request = json_decode( $inputJSON, TRUE );
$this->load->model('restapi/service');
$this->load->language('account/sms_templates');
$this->load->language('account/login');
$this->load->model('account/customer');
$this->load->form('RegisterForm');
$RegisterForm = new RegisterForm();
$result = array('status'=>0, 'status_text'=>'failed');

$this->validateApiCall();

$user_data = $this->model_account_customer->getCustomerByMobile($request['reg_telephone']);
if(count($user_data) > 0)
{
$request['session_id']    = session_id();
$request['ip']            = $_SERVER['REMOTE_ADDR'];
$request['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
$request['otp']           = rand(1000, 9999);
$request['otp_page']      = 'Forgot password';
$request['customer_id']   = $user_data['customer_id'];
$user_data = $this->model_account_customer->addOTP($request);

$message = $this->language->get('on_app_signup');
$message = sprintf($message,$request['otp']);
$sms = new SMS($message, $request['country_code'].$request['reg_telephone']);

$result['otp']         = $request['otp'];
$result['otp_page']    = $request['otp_page'];
$result['message']     = sprintf($this->language->get('otp_success'), $request['reg_telephone']);
$result['success']     = 1;
$result['status_text'] = 'Success';
}
}

header('Content-Type: application/json');
$this->response->setOutput(json_encode($result));
}
 */

	/**
	 * Function : forgot_password
	 * Request Parameters : mobile,email,version(2)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Verification code sent successfully on your mobile."}
	 * */
	public function forgot_password() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$this->load->model('restapi/service');
			$rt = array();
			$this->validateApiCall();

			if (isset($request['mobile']) || isset($request['a'])) {
				$mobile = $request['mobile'];
				if ($mobile == '' && isset($request['a'])) {

					$mobile = $request['a'];
				}
				if (isset($request['email'])) {
					$email = $request['email'];
				} else {
					$email = '';
				}
				if (!empty($mobile)) {
					$get_customer = $this->model_restapi_service->getCustomerByMobile($mobile);
				} else {
					$get_customer = $this->model_restapi_service->getCustomerByEmail($email);
				}

				if (!empty($get_customer)) {
					$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
					$request['mobile'] = $mobile;
					$request['email'] = $email;
					//$password = substr(substr( "abcdefghijklmnopqrstuvwxyz" ,mt_rand( 0 ,25 ) ,1 ) .substr( md5(16) ,1 ),0,6);//rand(1000000,rand(0,10000000));
					$password = substr(rand(), 0, 4);
					$request['password'] = $password;
					if (isset($get_customer['company']) && !empty($get_customer['company'])) {
						$request['name'] = $get_customer['company'];
					}
					if (!empty($email)) {
						$update_otp_result = $this->model_restapi_service->Updatepassword($request);
					} else {
						$update_otp_result = $this->model_restapi_service->Updatepassword($request);
					}
					if ($update_otp_result == 1) {

						if (!empty($email)) {
							$this->sendPasswordOnAppForgotPassword($email, $password, $request);
						} else {
							$this->sendPasswordOnAppRegistration($mobile, $password, $request);
						}
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
						$rt['message_regex'] = ".*(\\d{4}).*";
						$rt['message_regex_position'] = 1;
						$rt['gateway_name'] = 'WHSBOX';
						$rt['message'] = 'Password Sent Successfully.';
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'failed';
						$rt['message'] = 'Unable to send password.';
					}
				} else {
					$rt['error_code'] = '1001';
					$rt['status'] = '0';
					$rt['status_text'] = 'Resend Code Failed';
					$rt['message'] = 'Invalid mobile or email.';
				}

			} else {
				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have mobile.';
			}

			echo json_encode($rt);exit;
		}
	}

	/**
	 * Function : change_password
	 * Request Parameters : access_token,user_id,password,version(2)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Password changed successfully."}
	 * */
	public function change_password() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$this->load->model('restapi/service');
			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$password = $request['password'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$get_customer = $this->model_restapi_service->checkCustomerByID((int) $user_id);
						if (!empty($get_customer)) {
							$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
							$request['customer_id'] = $user_id;
							//$password = '123456';
							$request['password'] = $password;

							if (!empty($email)) {
								$update_otp_result = $this->model_restapi_service->Updatepassword($request);
							} else {
								$update_otp_result = $this->model_restapi_service->Updatepassword($request);
							}
							if ($update_otp_result == 1) {
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = 'Password changed successfully.';
							} else {
								$rt['error_code'] = '1001';
								$rt['status'] = '0';
								$rt['status_text'] = 'failed';
								$rt['message'] = 'Unable to change password.';
							}
						}
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'Resend Code Failed';
						$rt['message'] = 'Invalid mobile or email.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}

			echo json_encode($rt);exit;
		}
	}

	/**
	 * Function : code_resend
	 * Request Parameters : mobile,email,device_id
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Verification code sent successfully on your mobile."}
	 * */
	public function code_resend() {

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$this->load->model('restapi/service');
			$rt = array();
			$this->validateApiCall();

			if (isset($request['mobile'])) {
				if (isset($request['device_id'])) {
					$mobile = $request['mobile'];
					if (isset($request['email'])) {
						$email = $request['email'];
					} else {
						$email = '';
					}
					$device_id = $request['device_id'];
					if (!empty($mobile)) {
						$check_customer = $this->model_restapi_service->checkCustomerByMobile($mobile);
					} else {
						$check_customer = $this->model_restapi_service->checkCustomerByEmail($email);

					}
					if ($check_customer > 0) {
						$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
						$request['mobile'] = $mobile;
						$request['email'] = $email;
						$request['device_id'] = $device_id;
						$request['otp'] = $otp;

						if (!empty($email)) {
							$update_otp_result = $this->model_restapi_service->UpdateOtpByEmail($email, $device_id, $otp, $gcm_id);
						} else {
							$update_otp_result = $this->model_restapi_service->UpdateOtp($mobile, $device_id, $otp, $gcm_id);
						}

						if ($update_otp_result == 1) {
							if (!empty($email)) {
								$this->sendCodeOnAppRegistration($email, $otp);
							} else {
								$this->sendMessageOnAppRegistration($mobile, $otp);
							}

							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = 'Verification Code Sent Successfully.';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'User creation has been failed.';
						}
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'Resend Code Failed';
						$rt['message'] = 'Mobile Number does not match.';
					}
				} else {
					$rt['error_code'] = '1001';
					$rt['status'] = '0';
					$rt['status_text'] = 'Resend Code Failed';
					$rt['message'] = 'http request does not have device id.';
				}
			} else {
				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have mobile.';
			}

			echo json_encode($rt);exit;
		}
	}

	/* *******
		    * Function : verify
		    * Request Parameters : mobile,email,device_id,otp
		    * Type : Post
		    * Output : {"status":1,"status_text":"Success","message":"Mobile Number has been verified successfully","data":{"customer_id":"1","access_token":"123456","name":"Ravindra Singh","business_name":"Business","email":"test@test.com"}}
	*/

	public function verify() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['mobile'])) {

				if (isset($request['device_id'])) {

					if (isset($request['otp'])) {

						$mobile = $request['mobile'];
						if (isset($request['email'])) {
							$email = $request['email'];
						} else {
							$email = '';
						}

						$device_id = $request['device_id'];
						$otp = $request['otp'];
						if (!empty($email)) {
							$check_customer = $this->model_restapi_service->checkCustomerByEmailAndOtp($email, $otp);
						} else {
							$check_customer = $this->model_restapi_service->checkCustomerByMobileAndOtp($mobile, $otp);
						}

						if (!empty($check_customer)) {
							$customer_id = $check_customer['customer_id'];
							$access_token = $this->generate_access_token();
							if ($check_customer['device_id'] != $device_id) {
								$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, $access_token, $device_id);
							} else {
								$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, $access_token, '0');
							}
							if ($rs == 1) {
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = 'Mobile Number has been verified successfully.';
								$rt['data']['customer_id'] = $customer_id;
								$rt['data']['access_token'] = $access_token;
								$rt['data']['name'] = $check_customer['firstname'] . ' ' . $check_customer['lastname'];
								$rt['data']['email'] = $check_customer['email'];
								$rt['data']['business_name'] = $check_customer['company'];
							} else {
								$rt['error_code'] = '1001';
								$rt['status'] = '0';
								$rt['status_text'] = 'failed';
								$rt['message'] = 'User creation has been failed.';
							}
						} else {
							$rt['error_code'] = '1002';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['mobile'] = $mobile;
							$rt['message'] = 'Invalid OTP.';
						}
					} else {
						$rt['error_code'] = '1002';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['mobile'] = $mobile;
						$rt['message'] = 'No matching record found.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have Verification Code.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have device id.';
			}
		} else {
			$rt['error_code'] = '1002';
			$rt['status'] = '0';
			$rt['status_text'] = 'Signup failed';
			$rt['message'] = 'http request does not have mobile.';
		}

		echo json_encode($rt);exit;

	}

	/* *******
		 * Function : create_profile
		 * Request Parameters : user_id,access_token,name,email,business_name
		 * Type : Post
		 * Output : {"status":1,"status_text":"success","message":"Customer Profile Successfully created."}
	*/
	public function create_profile($call_by_api = '') {

		$headers = getallheaders();
		$request_by = $headers['REQUEST_BY'] ?? "";

		$this->load->model('restapi/service');
		$this->load->model('restapi/return');
		$this->load->model('restapi/wsbcartservice');
		$this->load->model('account/helpdesk');
		$this->load->model('account/credit_application');
		$user_id = 0;
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			if (!empty($request['language']) && $request['language'] == 'hindi') {
				$app_language = 2;
			} else {
				$app_language = 1;
			}

			$request['call_by_api'] = $call_by_api;
			$app_version_code = $request['app_version_code'];
			$rt = array();
			$rt['is_show_help_desk_support'] = true;
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$request['name'] = isset($request['name']) ? $request['name'] : '';
					$request['business_name'] = isset($request['business_name']) ? $request['business_name'] : '';

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					if (isset($request['a'])) {
						$request['customer_type_id'] = $request['a'];
					}
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);

					if ($check_access_token > 0) {
						$find_returns = $this->model_restapi_return->findReturns((int) $user_id);
						if (!empty($find_returns)) {
							if ($find_returns->row['count'] > 0) {
								$is_show_my_returns = true;
							} else {
								$is_show_my_returns = false;
							}
						}

						/*** below condition is to handle if we want only data or data with update ***/
						if ($call_by_api == 'get_profile') {
							$customer = $this->model_restapi_service->getCustomerProfile((int) $user_id);
							if (!empty($request['is_session_from_mswipe']) && date('Y-m-d H:i:s') <= "2019-10-10 23:59:59") {
								$this->model_restapi_service->updateMswipeReferralCodeForCustomer((int) $user_id);
							}
						} else {
							$customer = $this->model_restapi_service->createCustomerProfile($request);
						}

						if (((strtoupper($request_by) == "ANDROID_APP" || strtoupper($request_by) == "ANDROID APP") && $app_version_code > 72)
							|| (strtoupper($request_by) == "IOS_APP" && $app_version_code > 2)) {

							if (!empty($customer['error'])) {
								$rt = array();
								$rt['error_code'] = '1005';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $customer['error'];
								echo json_encode($rt);exit;
								$this->logForTest('create_profile', $inputJSON, $rt);
							}

							if (!empty($customer['error_referral_code'])) {
								$rt = array();
								$rt['error_code'] = '1006';
								$rt['error_field'] = 'referral_code';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $customer['error_referral_code'];
								echo json_encode($rt);exit;
								$this->logForTest('create_profile', $inputJSON, $rt);
							}
						}

						if (!empty($customer)) {
							$obj = new Helpdesk($this);
							// Update customer name, email in helpdesk.
							if (!empty($customer['helpdesk_id'])) {
								if (isset($request['source_id'])) {
									$update_details['source_id'] = $request['source_id'];
									$update_details['customer_name'] = $customer['firstname'] . ' ' . $customer['lastname'];
									$update_details['email'] = $customer['email'];
									$update_details['customer_access_token'] = $request['access_token'];
									$update_details['helpdesk_id'] = $customer['helpdesk_id'];
									$update_details['customer_id'] = $customer['customer_id'];
									$obj->updateHelpdeskCustomer($update_details);
								}
							}

							$wdata = array(
								'customer_id' => $customer['customer_id'],
							);
							$wishlist_coun = $this->model_restapi_service->getTotalWishlist($wdata);
							$update_price_by = $this->model_restapi_service->getCustomerSettingByKey('update_price', (int) $customer['customer_id']);
							if (empty($update_price_by)) {
								$update_price_by = DEFAULT_SHARE_MARGIN;
								$this->model_restapi_service->UpdateCustomerSetting((int) $customer['customer_id'], 'update_price', $update_price_by);
							} else {
								$update_price_by = $update_price_by['value'];
							}

							$total_cart = 0;
							//===========store iamge count===========//
							$customer['store_image'] = '0';
							if (!empty($customer['has_website']) && $customer['has_website'] > 0 && !empty($customer['customer_id'])) {
								$request['customer_id'] = $customer['customer_id'];
								$store_image_response = $this->getDataFromCustomerWebsite($request, 'getStoreImageCountForCustomer');
								if (!empty($store_image_response) && $store_image_response['statusCode'] == '200') {
									$customer['store_image'] = $store_image_response['data']['store_image_count'];
								}

								// $campaignResult = $this->getDataFromCustomerWebsite( $request, 'getCampaignStatusAccordingDevice' );
								// $autoCampaignStatus = $campaignResult['data']['is_auto_sms_campaign_active'] ?? '0';

								// if ( !empty($autoCampaignStatus) && $call_by_api == 'get_profile' ) {
								// 	$rt['preferences_to_update_boolean']['IS_AUTO_CAMPAIGNING_ENABLED_BOOLEAN_PREF'] =  true;
								// }

								//$rt['preferences_to_update_boolean']['IS_AUTO_CAMPAIGNING_ENABLED_BOOLEAN_PREF'] =  ? true : false;
								//$rt['preferences_to_update_long']['SMS_CAMPAIGN_STARTED_DATE_FIRST_MILLIS_PREF'] = (string) (strtotime("midnight", round(microtime(true))) * 1000);

							}

							// if device_manufacturer is coming in the request,
							// then we need to store device info into customer table
							if (!empty($request['device_manufacturer'])) {
								$device_info = $request['device_manufacturer'];
								$device_info .= isset($request['device_model']) ? '-' . $request['device_model'] : '';
								$device_info .= isset($request['os_version']) ? '-' . $request['os_version'] : '';
								$this->model_restapi_service->updateCustomerDeviceInfo((int) $user_id, $device_info);
							}

							$customer_preferences = $this->model_restapi_service->customerPreferencesSavedOrNot((int) $customer['customer_id']);
							if (!empty($customer_preferences)) {
								$preferences_saved = true;
							} else {
								$preferences_saved = false;
							}
							$total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int) $customer['customer_id']);
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['updated_default_price'] = '40';
							if (empty($customer['gst_number'])) {
								$rt['preferences_to_update_string']['GST_NUMBER_PREF'] = "";
							}
							//=====get cusrtomer location count start============//
							$get_customer_location_count = $this->model_restapi_service->getCustomerLocationCount((int) $customer['customer_id']);
							if ($get_customer_location_count < LIMIT_CUSTOMER_LOCATION) {
								$rt['location_require_count'] = LIMIT_CUSTOMER_LOCATION - $get_customer_location_count;
							} else {
								$rt['location_require_count'] = '0';
							}
							//=====get cusrtomer location count end============//
							$rt['message'] = 'Customer Profile Successfully created.';
							$rt['data']['customer_id'] = $customer['customer_id'];
							$rt['data']['text_msg_permit'] = 1;
							$rt['data']['is_dropshipper'] = $customer['is_dropshipper'];
							$rt['data']['name'] = $customer['firstname'] . ' ' . $customer['lastname'];
							$rt['data']['has_website'] = $customer['has_website'];
							$rt['data']['store_image'] = $customer['store_image'];
							$rt['data']['email'] = $customer['email'];
							$rt['data']['mobile'] = $customer['telephone'];
							$rt['data']['customer_vpa'] = $customer['upi_vpa'];
							$rt['data']['is_show_my_returns'] = $is_show_my_returns;
							$rt['data']['business_name'] = $customer['company'];
							$rt['data']['zone_id'] = $customer['zone_id'];
							$rt['data']['city'] = $customer['city'];
							$rt['data']['country_id'] = $customer['country_id'];
							$rt['data']['address'] = $customer['address_1'];
							$rt['data']['address_2'] = $customer['address_2'];
							$rt['data']['pincode'] = $customer['postcode'];
							$rt['data']['is_preferences_saved'] = $preferences_saved;
							$rt['data']['last_order_id'] = $this->model_restapi_service->getCustomerLastOrderIdWithNonCancelledStatus((int) $customer['customer_id']);
							$all_categories = $this->get_menu(true);
							if (!empty($this->_franchise_id)) {
								if (!empty($all_categories['data']['categories'])) {
									foreach ($all_categories['data']['categories'] as $key => $category_data) {
										if (!empty($category_data['sub_categories'])) {
											array_unshift($all_categories['data']['categories'][$key]['sub_categories'], array(
												'sub_category_id' => $category_data['category_id'] . "_F",
												'sub_category_name' => "Franchise",
												'image' => $this->config->get('config_url') . "image/no_image.png",
												'view_type_grid' => true,
											));
										}
									}
									$franchise_category = array();
									$franchise_category['data']['categories'][] = $this->model_restapi_service->getFranchiseCategory();
									$all_categories['data']['categories'] = array_merge($franchise_category['data']['categories'], $all_categories['data']['categories']);
								}
							}
							$rt['data']['categories'] = $all_categories;
							$offer_data = array('mobile' => $customer['telephone'], 'name' => $rt['data']['name'], 'customer_id' => $customer['customer_id'], 'token' => $customer['ws_access_token']);
							$rt['data']['offers'] = $this->get_offers(true, $offer_data);
							$rt['data']['single_store_banner'] = $this->getSingleStoreBanners(true, $offer_data);
							$rt['data']['customer_type'] = $customer['customer_type_id'];
							$rt['data']['customer_types'] = $this->model_restapi_service->getCustomerTypes();
							$rt['data']['total_cart'] = $total_cart;
							$rt['data']['wishlist_count'] = $wishlist_coun;
							$rt['data']['is_customer_exclusive'] = "1";
							$rt['data']['gst_number'] = $customer['gst_number'];
							$rt['data']['helpdesk_id'] = $customer['helpdesk_id'];
							$rt['data']['hd_ticket_type_new'] = $obj->getHelpdeskTicketType();
							$rt['data']['helpdesk_ticket_type'] = array("Tech", "Operation", "Sales");

							// prepare data for left navigation drawer and popup model
							$navigation_and_popup_data = array(
								'customer_id' => $customer['customer_id'],
								'token' => $customer['ws_access_token'],
								'has_website' => $customer['has_website'],
								'is_dropshipper' => $customer['is_dropshipper'],
								'credit_application_url' => HTTPS_SERVER . "index.php?route=account/credit_application&customer_id=" . $customer['customer_id'] . "&token=" . $customer['ws_access_token'] . "&language=" . $app_language,
							);

							$rt['data']['popup_model'] = $this->model_restapi_service->getPopupModelData($navigation_and_popup_data);
							if (empty($app_version_code)) {
								$app_version_code = '0';
							}
							$rt['left_navigation_drawer_list'] = $this->model_restapi_service->getLeftNavigationDrawerList($navigation_and_popup_data, $app_version_code);

							//$rt['gst_regex'] = '^[0-9]{2}[A-Z]{3}[C,P,H,F,A,T,B,L,J,G,E]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}[A-Z]{1}[A-Z0-9]{1}?$';
							// New gst regex(12-09-2017)
							$rt['gst_regex'] = '^[0-9]{2}[A-Z]{3}[C,P,H,F,A,T,B,L,J,G,E]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}?$';
							$base_url = $this->config->get('config_url');
							$rt['bank_details'] = array(
								'bank_ac_holder_name' => !empty($customer['bank_ac_holder_name']) ? $customer['bank_ac_holder_name'] : '',
								'bank_ac_number' => !empty($customer['bank_ac_number']) ? $customer['bank_ac_number'] : '',
								'ifsc_code' => !empty($customer['ifsc_code']) ? $customer['ifsc_code'] : '',
								'bank_details_verified' => !empty($customer['bank_details_verified']) ? $customer['bank_details_verified'] : '0',
							);

							$rt['add_nav_link'] = array(array("name" => "Store Locator", "url" => "https://www.wholesalebox.in/storelocator?popup=true"));
							$rt['notification']['cart'] = array(
								"first_notification" => array(
									"content_info" => "Complete your order",
									"title" => "Buy before it goes out of stock",
									"message" => "Hey customer_name, Our popular products are fast moving, Let me know if I can help to complete the order.",
								),
								"next_notification" => array(
									"content_info" => "Your cart items are going out of stock",
									"title" => "Any help needed? Chat with support_name",
									"message" => "Hey customer_name, Some of the items in your cart are going out of stock, Hurry up!",
								),
								"first_notification_after_time" => 2880, //minutes - 48hrs
								"next_notification_after_time" => 1440, //minutes - 24hrs
								"notification_send_number_of_time" => 3,
							);

							$rt['notification']['recently_viewed'] = array(
								"content_info" => "Liked these products?",
								"title" => "Liked these products?",
								"message" => "Buy before these goes out of stock.",
								"first_notification_after_time" => 120, //minutes - 2hrs
								"next_notification_after_time" => 1440, //minutes - 1 day
								"notification_send_number_of_time" => 3,
							);

							$rt['notification']['user_inactivity'] = array(
								"content_info" => "Hey customer_name, we missed you!!",
								"title" => "Fresh stock arrived.",
								"message" => "Hurry up! Buy before these goes out of stock.",
								"first_notification_after_time" => 4320, //minutes - 72hrs
								"next_notification_after_time" => 1440, //minutes - 24rs
								"notification_send_number_of_time" => 5,
							);

							$rt['notification']['near_store'] = array(
								"content_info" => "Visit our store!!",
								"title" => "You are near our wholesalebox Store.",
								"message" => "Step into our store to check more than 1.5 Lakh designs.",
							);

							$getblack_lists = $this->model_restapi_service->getblacklist();

							if (!empty($getblack_lists)) {
								$rt['blacklist'] = array(
									"mobile" => isset($getblack_lists['mobile']) ? $getblack_lists['mobile'] : '',
									"app" => isset($getblack_lists['app']) ? $getblack_lists['app'] : '',
									"name" => isset($getblack_lists['name']) ? $getblack_lists['name'] : '',
								);

							} else {
								$rt['blacklist'] = array();
							}

							$rt['store_array'] = $this->model_restapi_service->getStoreData();

							$rt['cash_coupon'] = $this->config->get('coupon_franchise_cash');
							$rt['credit_coupon'] = $this->config->get('coupon_franchise_credit');

							$this->load->model('localisation/currency');
							$currency_list = $this->model_localisation_currency->getCurrencies();
							// unset default INR currency
							unset($currency_list['INR']);
							$rt['currency_list'] = array_map(function ($a) {$a['title'] = $a['text'];return $a;}, array_values($currency_list));

						} else {
							$rt['error_code'] = '1003';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							//$rt['mobile'] = $mobile;
							$rt['message'] = 'Update query failed.';
						}

						//}else{
						//	 $rt['error_code'] = '1005';
						//	 $rt['status'] = '0';
						//	 $rt['status_text'] = 'Failed';
						//	 $rt['message'] = 'Email address already exist.';
						//  }
						// }else{
						//	 $rt['error_code'] = '1005';
						//	 $rt['status'] = '0';
						//	 $rt['status_text'] = 'Failed';
						//	 $rt['message'] = 'Mobile number already exist.';
						//  }
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';

						$this->logForTest('create_profile', $inputJSON, $rt);
					}
					// }else{
					// 	$rt['error_code'] = '1003';
					// 	$rt['status'] = '0';
					// 	$rt['status_text'] = 'Failed';
					// 	$rt['message'] = 'Customer Name and Business Name Must be filled.';
					//                 $this->logForTest('create_profile',  $inputJSON, $rt);
					// }
				} else {
					$rt['error_code'] = '1003';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
					$this->logForTest('create_profile', $inputJSON, $rt);
				}
			} else {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
				$this->logForTest('create_profile', $inputJSON, $rt);
			}

			//paytm QRcode URL
			// $rt['paytm_qr_code_url'] = "http://d36qiqd7gl7e25.cloudfront.net/img/paytm_QR.jpeg";

			//get support person detail
			// $rt['support'] = $this->model_restapi_service->getSupportPersonDetail($this->registry, (int) $user_id, ($customer['has_website'] ?? 0));
			// echo 'hai';exit;
			//get all support number details
			// $rt['all_support_number_array'] = $this->model_restapi_service->getAllSupportNumberDetails();

			if (!empty($rt['data']['is_dropshipper']) && $rt['data']['is_dropshipper'] == "1") {

				$rt['all_support_number_array'][] = array(
					"name" => "Reseller Helpline",
					"description" => "Reseller Related Issues",
					"number" => "9116121383",
					"is_on_whatsapp" => "1",
					"is_on_call" => "1",
				);
				$rt['support'][] = array(
					'name' => 'WholesaleBox Reseller Helpline',
					'phone' => '9116121383',
				);
			}

			// credit permission text
			$rt['sms_permission_text'] = 'Permissions needed for higher limit approvals.';

			//Here, We get Dymanic layout from System/cache folder.
			$dynamic_layouts = array_values($this->model_restapi_service->getDynamicLayout());
			if (in_array(strtoupper($request_by), array('ANDROID_APP', 'CRM_APP', 'ANDROID APP', 'CRM APP'))
				&& $app_version_code > 104) {

				//Check if this application is given to RBL and in preapproved stage
				$rbl_array = $this->model_account_credit_application->getRblPreapprovedUrl($user_id);

				if (!empty($rbl_array)) {
					//RBL preapproved case start

					$action_button = array();

					if (!empty($rbl_array['action_title']) && $rbl_array['action_title'] == 'pre_approved') {
						$action_button = array(
							'action_image_url' => $rbl_array['action_image_url'],
							'open_view' => 'open_webview',
							'web_view_url' => $rbl_array['rbl_url'],
						);
					} else if (!empty($rbl_array['action_title']) && $rbl_array['action_title'] == 'cif_created') {
						$action_button = array(
							'action_image_url' => $rbl_array['action_image_url'],
							'open_view' => 'nothing',
						);
					}

					$button_after_banner[0] = array(
						'action_button' => $action_button,
						'layout_index' => 0,
						'layout' => array('status' => 1),
						'layout_type_custom' => 5,
						'store_visibility' => "both",
						'module' => null,
					);
				} else {

					$credit_application_status = $this->model_account_credit_application->creditApplicationStatusForBanner($user_id);
					if ($app_version_code >= '111') {
						$wsb_credit_payment = new WsbCreditPayment($this);
						$customer_id = array($user_id);
						$limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
						if (!empty($limit_data[$user_id])) {
							$limit = $limit_data[$user_id];
						} else {
							if (!empty($credit_application_status['gst_number'])) {
								$limit = '25000';
							} else {
								$limit = '10000';
							}
						}

						if (!empty($limit_data[$user_id])) {
							$limit = $limit_data[$user_id];
						} else {
							$limit = '0';
						}
						if (!empty($credit_application_status['gst_number'])) {
							$pre_approved_limit = '25000';
						} else {
							$pre_approved_limit = '10000';
						}

						if ($credit_application_status['activation_status'] == '1') {
							if (!empty($credit_application_status['message'])) {
								$approval_message = $credit_application_status['message'];
							} else {
								$approval_message = "Your credit limit is now active for Rs. " . $limit . "/-. Please use the “Order on WholesaleBox credit” payment method at the time of order placement.\n\nAvailable limit: Rs. " . $limit . "/-";
							}
							$button_after_banner[0] = array(
								'action_button' => array('open_view' => 'open_credit_form'),
								'layout_index' => 0,
								'layout' => array('status' => '1'),
								'background_gradient_color_list' => array("#253F96", "#3189FE"),
								'text_hex_color' => "#FFFFFF",
								'text_to_display' => $approval_message,
							);
						} else if ($credit_application_status['credit_application_form_status'] == '2') {
							$button_after_banner[0] = array(
								'action_button' => array('open_view' => 'open_credit_form'),
								'layout_index' => 0,
								'layout' => array('status' => 1),
								'background_gradient_color_list' => array("#253F96", "#3189FE"),
								'text_hex_color' => "#FFFFFF",
								'text_to_display' => "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680",
							);

						} else if ($credit_application_status['credit_application_form_status'] == '1') {
							$button_after_banner[0] = array(
								'action_button' => array('open_view' => 'open_credit_form'),
								'layout_index' => 0,
								'layout' => array('status' => 1),
								'background_gradient_color_list' => array("#253F96", "#3189FE"),
								'text_hex_color' => "#FFFFFF",
								'text_to_display' => "You have been pre-approved for credit of Rs. " . $pre_approved_limit . "/- @ 0% interest for 30 days. For activation and higher limit, please complete step 2 and upload all your KYC documents",
							);

						} else {
							$button_after_banner[0] = array(
								'action_button' => array(
									'action_image_url' => 'http://d36qiqd7gl7e25.cloudfront.net/credit_banner_english_new.png',
									'open_view' => 'open_credit_form',
								),
								'layout_index' => 0,
								'layout' => array('status' => 1),
							);

						}

					} else {
						$button_after_[0] = array(
							'action_button' => array(
								'action_image_url' => 'http://d36qiqd7gl7e25.cloudfront.net/credit_banner_english_new.png',
								'open_view' => 'open_webview',
								'web_view_url' => HTTPS_SERVER . 'index.php?route=account/credit_application&customer_id=placeholder_customer_id&token=placeholder_access_token&language=' . $app_language,
							),
							'layout_index' => 0,
							'layout' => array('status' => 1),
						);
					}

				} //RBL preapproved case closed

				$button_after_banner[1] = array(
					'is_category_layout' => '1',
					'layout_index' => 0,
					'layout' => array('status' => 1),
				);

				array_unshift($dynamic_layouts, $button_after_banner[0], $button_after_banner[1]);
			}

			$shuffled_layouts = array();

			/*if($rt['data']['is_dropshipper'] == "1")
				        {
				           unset($dynamic_layouts[0]);
			*/

			foreach ($dynamic_layouts as $layout) {
				$layout['store_visibility'] = (isset($layout['layout']['layout_title']) && $layout['layout']['layout_title'] == 'Shop By Brands') ? 'wholesale' : 'both';

				if (!empty($layout['module'])) {
					shuffle($layout['module']);
				}

				$shuffled_layouts[] = $layout;
			}
			$rt['dynamic_layout'] = $shuffled_layouts;

			// credit_activation_status key given for app page list bannners
			$rt['data']['credit_activation_status'] = $credit_application_status['activation_status'] ?? '0';

			// get ssl status
			$ssl_status = $this->get_ssl_status(true);
			if ($ssl_status) {
				$ssl = '1';
			} else {
				$ssl = '0';
			}
			$rt['ssl_status'] = $ssl;

			echo json_encode($rt);exit;
		}
	}

	/* *******
		 * Function : get_categories
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {"status":1,"status_text":"Success","message":"Get All Categories List","data":categories list}
	*/

	public function get_categories($return = false) {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			$this->validateApiCall();

			// if request is from ios_app, then we will allow to send data even without access token.
			if ($this->_request_from == 'IOS_APP' && !isset($request['access_token'])) {
				$rt = $this->get_menu(true);
			} else if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						/**
						 * Only For Update Gcm id for all previous users(29-01-2016 Ravindra Singh)
						 * Check And Update gcm id according to user
						 **/
						if (isset($request['gcm_id'])) {
							$gcm_id = $request['gcm_id'];
							$rs = $this->model_restapi_service->updategcmid((int) $user_id, $gcm_id);
						}

						$this->load->model('restapi/wsbcartservice');
						$total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int) $user_id);

						$wdata = array(
							'customer_id' => $user_id,
						);
						$wishlist_coun = $this->model_restapi_service->getTotalWishlist($wdata);
						$categories = $this->get_menu(true);

						if (!empty($categories)) {
							$rt = $categories;
							$rt['data']['quality_expectation'] = "1,2,3";
							$rt['data']['total_cart'] = $total_cart;
							$rt['data']['wishlist_count'] = $wishlist_coun;

						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Categories Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		if ($return == true) {
			return $rt;
		} else {
			echo json_encode($rt);
			exit;
		}
	}

	/* *******
		 * Function : get_menu
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {"status":1,"status_text":"Success","message":"Get All Categories List","data":menu}
	*/

	public function get_menu($return = false) {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token']) || $this->_request_from == 'IOS_APP') {
				if (isset($request['user_id']) || $this->_request_from == 'IOS_APP') {

					$access_token = isset($request['access_token']) ? $request['access_token'] : "";
					$user_id = isset($request['user_id']) ? $request['user_id'] : "";
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0 || $this->_request_from == 'IOS_APP') {

						/**
						 * Only For Update Gcm id for all previous users(29-01-2016 Ravindra Singh)
						 * Check And Update gcm id according to user
						 **/
						if (isset($request['gcm_id'])) {
							$gcm_id = $request['gcm_id'];
							$rs = $this->model_restapi_service->updategcmid((int) $user_id, $gcm_id);
						}

						if (!empty($user_id)) {

							$this->load->model('restapi/wsbcartservice');
							$total_cart = $this->model_restapi_wsbcartservice->get_cart_total((int) $user_id);

							$wdata = array(
								'customer_id' => $user_id,
							);
							$wishlist_count = $this->model_restapi_service->getTotalWishlist($wdata);

						}
						$menu = $this->model_restapi_service->getMenu();
						if (!empty($menu)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['data']['categories'] = $menu;
							$rt['data']['quality_expectation'] = "1,2,3";
							$rt['data']['total_cart'] = isset($total_cart) ? $total_cart : "0";
							$rt['data']['wishlist_count'] = isset($wishlist_count) ? $wishlist_count : "0";
							$rt['whatsapp_number'] = WHATSAPP_NUMBER;
							$rt['phone_number'] = PHONE_NUMBER;

						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Categories Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		if ($return == true) {
			return $rt;
		} else {
			echo json_encode($rt);
			exit;
		}
	}

	/* *******
		    * Function : get_sort_options
		    * Request Parameters : user_id,access_token
		    * Type : Post
		    * Output : {"status":"1","status_text":"Success","options":["Hand-picked","Latest Designs","Price(Low > High)","Price(High > Low)","Rating(Highest)","Rating(Lowest)"]}
	*/

	public function get_sort_options() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token']) || $this->_request_from == 'IOS_APP') {
				if (isset($request['user_id']) || $this->_request_from == 'IOS_APP') {

					$access_token = isset($request['access_token']) ? $request['access_token'] : '';
					$user_id = isset($request['user_id']) ? $request['user_id'] : 0;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($this->_request_from == 'IOS_APP' && $user_id == 0) {
						$check_access_token = 1;
					}
					if ($check_access_token > 0) {

						$sortoptions[0]['key'] = 'hand_picked';
						$sortoptions[0]['value'] = 'Hand-picked';
						$sortoptions[0]['selected'] = 'NO';

						$sortoptions[1]['key'] = 'latest_designs';
						$sortoptions[1]['value'] = 'Latest Designs';
						$sortoptions[1]['selected'] = 'NO';

						$sortoptions[2]['key'] = 'price_low_to_high';
						$sortoptions[2]['value'] = 'Price(Low > High)';
						$sortoptions[2]['selected'] = 'NO';

						$sortoptions[3]['key'] = 'price_high_to_low';
						$sortoptions[3]['value'] = 'Price(High > Low)';
						$sortoptions[3]['selected'] = 'NO';

						$sortoptions[4]['key'] = 'rating_low';
						$sortoptions[4]['value'] = 'Rating(Lowest)';
						$sortoptions[4]['selected'] = 'NO';

						$sortoptions[5]['key'] = 'rating_high';
						$sortoptions[5]['value'] = 'Rating(Highest)';
						$sortoptions[5]['selected'] = 'NO';

						$sortoptions[6]['key'] = 'special_price';
						$sortoptions[6]['value'] = 'Discount';
						$sortoptions[6]['selected'] = 'NO';

						if (!empty($sortoptions)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['options'] = $sortoptions;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Categories Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		echo json_encode($rt);exit;

	}

	/* *******
		 * Function : get_products_by_category
		 * Request Parameters : user_id,access_token,category_id,page(default : 1),if(Filter)filter
		  * $request_data - send post data in this array if you want to call this method internally
		 * Type : Post
		 * Output : {"status":1,"status_text":"Success","message":"Get All Products List By Category","data":product list}
	*/

	public function get_products_by_category($request_data = '') {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') || !empty($request_data)) {

			if (empty($request_data)) {
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
			} else {
				$request = $request_data;
			}

			$app_version_code = $request['app_version_code'] ?? 0;

			$rt = array();
			$this->validateApiCall();

			$prnd = "1";
			if (isset($request['access_token']) || $this->_request_from == 'IOS_APP') {
				if (isset($request['user_id']) || $this->_request_from == 'IOS_APP') {

					$access_token = isset($request['access_token']) ? $request['access_token'] : '';
					$user_id = isset($request['user_id']) ? $request['user_id'] : 0;
					if (isset($request['category_id']) && !empty($request['category_id'])) {
						if (strpos($request['category_id'], '_F')) {
							$request['category_id'] = str_replace('_F', '', $request['category_id']);
							$filter_franchise_tab = true;
						}
						$cat_id = $request['category_id'];
					} else {
						$cat_id = '';
					}
					$sort_data = array("product_id");

					if (isset($request['page']) && $request['page'] > 0) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$search = '';

					// for store 30 products
					$store_product = 0;
					$date_added_less_than = '';
					$store_code = '';

					if (!empty($request['search'])) {
						$store_purchased_days_map = $this->__getStorePurchasedDaysMap();
						if (isset($store_purchased_days_map[$request['search']])) {
							$store_product = 1;
							$store_code = $store_purchased_days_map[$request['search']]['store'];
							$purchase_days = $store_purchased_days_map[$request['search']]['days'];
							$date = date_create();
							date_sub($date, date_interval_create_from_date_string($purchase_days . " days"));
							$date_added_less_than = date_format($date, "Y-m-d\TH:i:s\Z");
						} else {
							$search = $request['search'];
						}
					}
					$show_exclusive_only = 0;

					if (isset($request['offers'])) {

						$offer_data = array();
						if (is_array($request['offers'])) {
							foreach ($request['offers'] as $offer_key => $offer_value) {
								$offer_data = explode("__", $offer_key);
								break;
							}
						}

						if (count($offer_data) == 2) {

							$offer_value = $offer_data[0];
							$offer_type = $offer_data[1];

							if ($offer_type == 'category') {
								$cat_id = (int) $offer_value;

							} elseif ($offer_type == 'search') {
								$search = (string) $offer_value;

							} elseif ($offer_type == 'sale') {
								/** to use clearance sale please add banner_type = sale in khufiya and give value 1 */
								$clearance_sale = (string) $offer_value;
							}

						} else {

							if (isset($request['offers']['show_exclusive_only'])) {
								$show_exclusive_only = trim($request['offers']['show_exclusive_only']);

							} elseif (is_array($request['offers']) && sizeof($request['offers']) > 0) {
								$search = (string) array_values($request['offers'])[0];

							} else {
								$search = (string) $request['offers'];
							}
						}
					}

					if (isset($request['category_facet'])) {
						$category_facet = $request['category_facet'];
					} else {
						$category_facet = 0;
					}
					if (isset($request['rating_filter'])) {
						$rating_filter = $request['rating_filter'];
					} else {
						$rating_filter = '';
					}

					if (isset($request['filter'])) {
						//$custom_ar = array("50001","50002","50003");
						$custom_ar_rating = array("20000", "20001", "20002", "20003", "20004", "20005");
						$filter = $request['filter'];
						//$quality_exp_check = 0;
						$rating_filter_check = 0;
						if (!empty($request['filter'])) {

							$filters_data = explode(",", $request['filter']);
							foreach ($filters_data as $fkey => $fdata) {
								if (in_array($fdata, $custom_ar_rating)) {
									$rating_filters[] = $fdata;
									$rating_filter_check = 1;
								}
							}
						}

						if ($rating_filter_check == 1) {
							$customvalrate = "20000";
							foreach ($rating_filters as $rf) {
								$new_rating_filters[] = $rf - $customvalrate;
							}

							$rating_filter = min($new_rating_filters);

							$filter = explode(",", $filter);
							$filter = array_diff($filter, $custom_ar_rating);
							$filter = implode(",", $filter);
						}

					} else {
						$filter = '';
					}

					if (isset($request['show_single']) && $request['show_single'] == "1") {
						$custom_store = "single";
					} else {
						$custom_store = "wholesale";
					}

					if (isset($request['sort'])) {
						$prnd = "0";
						if ($request['sort'] == "hand_picked") {
							$sort = 'sort_order';
							$order = "ASC";
						} else if ($request['sort'] == "latest_designs") {
							$sort = 'date_added';
							$order = "DESC";
						} else if ($request['sort'] == "price_low_to_high") {
							$sort = 'selling_price';
							$order = "ASC";
						} else if ($request['sort'] == "price_high_to_low") {
							$sort = 'selling_price';
							$order = "DESC";
						} else if ($request['sort'] == "rating_low") {
							$sort = 'rating';
							$order = "ASC";
						} else if ($request['sort'] == "rating_high") {
							$sort = 'rating';
							$order = "DESC";
						} else if ($request['sort'] == "special_price") {
							$sort = 'special_price';
							$order = "DESC";
						} else if (strtolower(trim($request['search'])) == "rxt") {
							// if sort is not present in request and search keyword is rxt, then set sort to date added
							$sort = "p.date_added";
							$order = "DESC";
						} else {
							/* $sort = 'RAND()';
								$order = "ASC";
							*/
							if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
								$sort = 'sort_order';
							} else {
								$sort = 'rand()';
							}
							$order = "ASC";
						}

					} else if (strtolower(trim($request['search'])) == "rxt") {
						$sort = "p.date_added";
						$order = "DESC";
					} else {
						if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
							$sort = 'sort_order';
						} else {
							$sort = 'rand()';
						}
						$order = "ASC";
					}
					if (isset($request['price_filter'])) {
						$price_filter = $request['price_filter'];
					} else {
						$price_filter = '';
					}
					if (isset($request['product_ids'])) {
						$product_ids = $request['product_ids'];
					} else {
						$product_ids = '';
					}

					// Handpicked ids, if we are in sort_order mode
					if (isset($request['handpicked_ids'])) {
						$handpicked_ids = $request['handpicked_ids'];
					} else {
						$handpicked_ids = '';
					}

					// product total for page 2 to 5
					if (isset($request['product_total'])) {
						$filter_product_total = $request['product_total'];
					} else {
						$filter_product_total = 0;
					}

					// product total for page 2 to 5
					if (isset($request['random_string'])) {
						$random_string = trim($request['random_string']);
					} else {
						$random_string = '';
					}

					if (isset($request['limit'])) {
						$limit = $request['limit'];
					} else {
						$limit = '10';
					}

					// last_filter_action
					if (isset($request['last_filter_action'])) {
						$last_filter_action = $request['last_filter_action'];
					} else {
						$last_filter_action = '';
					}

					// filter_only
					if (isset($request['filter_only'])) {
						$filter_only = $request['filter_only'];
					} else {
						$filter_only = 0;
					}

					if (isset($request['client_preferences'])) {
						$client_preferences = $request['client_preferences'];
					} else {
						$client_preferences = '';
					}

					if (isset($request['filter_seller_id'])) {
						$filter_seller_id = $request['filter_seller_id'];
					} else {
						$filter_seller_id = '';
					}

					// page filters inflated string
					if (!empty($request['page_filters'])) {
						$page_filters = $request['page_filters'];
					} else {
						$page_filters = '';
					}

					// page filter flag
					$create_page_filters = '0';
					$show_page_filters = '0';

					if ((int) $app_version_code > 107) {
						if (empty($last_filter_action) && empty($filter) && !empty($cat_id) && empty($price_filter) && empty($rating_filter)) {

							if ((!isset($request['show_page_filters'])) || (!empty($request['show_page_filters']))) {

								if ($page == '1' && empty($page_filters)) {
									$create_page_filters = '1';
								}

								if (!empty($request['show_page_filters']) || $page % 2 == 0) {
									$show_page_filters = '1';
								}
							}
						}
					}

					if (isset($this->restapi->getRequestHeader()['REQUEST_BY']) && strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
						$rt['APP_VERSION'] = IOS_APP_VERSION;
						$rt['APP_MESSAGE'] = 'You\'re missing out some great features, Update WholesaleBox App now!.';
					} else {
						$rt['ANDROID_APP_VERSION'] = ANDROID_APP_VERSION; //current Android app version is 58 .update/change ANDROID_APP_VERSION to APP_VERSION in app version >= 63.
						$rt['ANDROID_APP_MESSAGE'] = 'You\'re missing out some great features, Update WholesaleBox App now!.';
					}
					// If user_id is zero means request is coming from ios_app, so in this case will not check user information.
					if ($user_id == 0) {
						$check_access_token = 1;
						$country_code = '';
						$rt['compulsory_update_flag'] = 'not';
						if (isset($request['app_version_code'])) {
							if ((float) $request['app_version_code'] < IOS_MINIMUM_APP_VERSION_CODE_REQUIRED) {
								$rt['compulsory_update_flag'] = 'must';
							}
						}
						$postcode = '';
					} else {
						$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);

						$rs = $this->model_restapi_service->checkCustomerByID((int) $user_id);
						/**
						 * Get country code of user
						 **/
						$country_code = $rs['country_code'];

						/*if (!empty($request['voucher_code'])) {
							                            $store_code = SalesStaff::checkStoreVoucher($this->db, $request['voucher_code']);

							                            if ($store_code) {
							                                if (!file_exists(DIR_LOGS . 'voucher_code_useages.csv')) {
							                                    $fp = fopen(DIR_LOGS . 'voucher_code_useages.csv', 'a');
							                                    $data = array('Customer Id', 'Voucher Code', 'Store Code', 'Date Added', 'Device Type');
							                                    fputcsv($fp, $data);
							                                } else {
							                                    $fp = fopen(DIR_LOGS . 'voucher_code_useages.csv', 'a');
							                                }
							                                $data = array($user_id, $request['voucher_code'], $store_code, Date('Y-m-d H:i:s'), 'app');
							                                fputcsv($fp, $data);
							                            } else {
							                                $rt['status'] = '0';
							                                $rt['status_text'] = 'Success';
							                                $rt['message'] = 'Invalid Voucher code';
							                                echo json_encode($rt);
							                                exit;
							                            }
						*/

						$arr_update = array('show_exclusive_only' => $show_exclusive_only);
						$force_update = $this->model_restapi_service->forceUpdate((int) $user_id, $arr_update);
						$rt['compulsory_update_flag'] = $force_update;

						// if (isset($request['app_version_code'])) {
						//     $update_app_version = $this->model_restapi_service->updateAppVersion((int)$user_id, $request['app_version_code']);
						// }

						//$address = $this->model_restapi_service->getDefaultAddress((int)$user_id);

						$postcode = NULL; //$address['postcode'];
					}

					if (!empty($store_product)) {
						$sort = 'date_added';
						$order = 'DESC';
					}

					if ($check_access_token > 0) {
						$filter_data = array(
							'filter_name' => $search,
							'filter_filter' => $filter,
							'sort' => $sort,
							'order' => $order,
							'start' => ($page - 1) * $limit,
							'limit' => $limit,
							'price_filter' => $price_filter,
							'user_id' => $user_id, // To get message according to product
							'seller' => '', //$sellers,
							'custom_store' => $custom_store,
							'product_ids' => $product_ids,
							'page' => $page,
							'handpicked_ids' => $handpicked_ids,
							'product_total' => $filter_product_total,
							'random_string' => $random_string,
							'filter_special' => isset($clearance_sale) ? $clearance_sale : '',
							'call_from' => 'app',
							'rating_filter' => isset($rating_filter) ? $rating_filter : '',
							'is_facet' => 1,
							'show_exclusive_only' => $show_exclusive_only,
							'last_filter_action' => $last_filter_action,
							'filter_only' => $filter_only,
							'facets' => true,
							'client_preferences' => $client_preferences,
							'category_facet' => $category_facet,
							'filter_seller_id' => $filter_seller_id,
							'date_added_less_than' => $date_added_less_than,
							'store_product' => $store_product,
							'store_code' => $store_code,
							'create_page_filters' => $create_page_filters,
							'show_page_filters' => $show_page_filters,
							'page_filters' => $page_filters,
							'device_id' => isset($request['device_id']) ? $request['device_id'] : '',
							'gcm_id' => isset($request['gcm_id']) ? $request['gcm_id'] : '',
						);
						if (!empty($cat_id)) {
							$filter_data['filter_category_id'] = $cat_id;
						}

						if (!empty($this->_franchise_id)) {
							$filter_data['franchise_id'] = $this->_franchise_id;
							if (!empty($filter_franchise_tab)) {
								$filter_data['filter_franchise_tab'] = $filter_franchise_tab;
							}
						}
						$popular = $this->model_restapi_service->getPopularSearch();
						$i = 0;
						$popular_search = array();
						foreach ($popular as $pkey => $pvalue) {

							$popular_search[] = (object) $pvalue['popular_search'];
							$i++;
						}
						$popular = $popular_search;

						$fdata = array(
							'filter_name' => $search,
							'filter_filter' => $filter,
							'sort' => $sort,
							'order' => $order,
							'seller' => '',
							'custom_store' => $custom_store,
							'price_filter' => $price_filter,
							'product_ids' => $product_ids,
							'filter_special' => isset($clearance_sale) ? $clearance_sale : '',
							'rating_filter' => isset($rating_filter) ? $rating_filter : '',
						);
						if (!empty($cat_id)) {
							$fdata['filter_category_id'] = $cat_id;
						}
						if (isset($request['minimal']) && $request['minimal'] == 1) {
							$filter_data['minimal'] = 1;
						}
						if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {

							//$filter_data['show_out_of_stock'] = 1;
							$filter_data['call_from'] = 'app';

							if (!empty($postcode)) {
								$filter_data['post_code'] = $postcode;
							}

							isset($request['filter']) ? $filter_data['clicked_filter'] = $request['filter'] : "";
							//I will use minimal in solr model to modify query for new improved version where minimal information will be sent on first call

							$solr = new SolrProduct($this);
							$products = $solr->getProductFromSolr($filter_data);
							$coun = $products['total']; //get total from SOLR query

						} else {
							$products = $this->model_restapi_service->getProducts($filter_data);
							$coun = $this->model_restapi_service->getTotalProducts($fdata);
						}

						if ($coun > $limit) {
							$tpage = ceil($coun / $limit);
							$cpage = $page;
							$npage = $cpage + 1;
							if ($npage > $tpage) {
								$npage = 0;
							}
							$ppage = $cpage - 1;
							if ($cpage < 1) {
								$ppage = 0;
							}

						} else {
							$tpage = ceil($coun / $limit);
							$cpage = $page;
							$npage = '0';
							$ppage = '0';
						}
						/*if($prnd == "1" && !empty($products['data'])){
							shuffle($products['data']);
						}*/
						if (isset($products['data']) && !empty($products['data'])) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['current_page'] = $cpage;
							$rt['next_page'] = $npage;
							$rt['previous_page'] = $ppage;
							$rt['total_page'] = $tpage;
							$rt['total_products'] = ($coun % 10 == 1) ? ($coun + 1) : $coun;
							$rt['data']['products'] = $products['data'];
							$rt['data']['popular_search'] = $popular;

							if (isset($products['page_filters'])) {
								$rt['data']['page_filters'] = $products['page_filters'];
							}

							if (isset($products['current_page_filter']) && $npage > 0) {
								$rt['data']['current_page_filter'] = $products['current_page_filter'];
							}

							if ((int) $request['app_version_code'] > 114 && strtoupper($this->_request_from) == "ANDROID APP") {

								$language = $request['language'] ?? "en";
								$credit_activation_status = $request['credit_activation_status'] ?? 0;
								$customer_dropshipper = $request['customer_dropshipper'] ?? 0;

								$page_banners = $this->model_restapi_service->appListPageAllBanners($language, (int) $credit_activation_status, (int) $customer_dropshipper);

								if (!empty($page_banners)
									&& ($cpage % 2 != 0)) {
									// only on odd pages

									$current_page_banner_key = (floor($cpage / 2));

									if (isset($page_banners[$current_page_banner_key])) {
										$rt['data']['banner'] = $page_banners[$current_page_banner_key];
									}
								}
							}

							$rt['whatsapp_number'] = WHATSAPP_NUMBER;
							$rt['phone_number'] = PHONE_NUMBER;

							$rt['new_country'] = $country_code;
							$rt['handpicked_ids'] = (isset($products['handpicked_ids'])) ? $products['handpicked_ids'] : '';
							$rt['product_total'] = $coun;
							$rt['random_string'] = '';

						} else if (isset($products['filter_facets']) && $coun > 0 && empty($products['send_status'])) {

							if (!empty($request['app_version_code']) && ((int) $request['app_version_code'] > 70 || ($this->_request_from == 'IOS_APP' && (int) $request['app_version_code'] > 1))) {
								if (isset($products['filter_facets']['rating'])) {
									$rating_arr_old = $products['filter_facets']['rating'];
									$rating_arr_new = array();
									if (isset($rating_arr_old['0.0'])) {unset($rating_arr_old['0.0']);}
									if (isset($rating_arr_old['3.0'])) {$rating_arr_new['3.0'] = array('label' => 'Average', 'count' => $rating_arr_old['3.0']);}
									if (isset($rating_arr_old['4.0'])) {$rating_arr_new['4.0'] = array('label' => 'Good', 'count' => $rating_arr_old['4.0']);}
									if (isset($rating_arr_old['5.0'])) {$rating_arr_new['5.0'] = array('label' => 'Excellent', 'count' => $rating_arr_old['5.0']);}

									$products['filter_facets']['rating'] = $rating_arr_new;
								}
							}
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['current_page'] = $cpage;
							$rt['next_page'] = $npage;
							$rt['previous_page'] = $ppage;
							$rt['total_page'] = $tpage;
							$rt['total_products'] = $coun;
							$rt['data']['popular_search'] = $popular;
							$rt['data']['price_currency_symbol'] = !empty($this->currency->getSymbolLeft()) ? $this->currency->getSymbolLeft() : $this->currency->getSymbolRight();
							$rt['data']['filter_facets'] = $products['filter_facets'];
							$rt['whatsapp_number'] = WHATSAPP_NUMBER;
							$rt['phone_number'] = PHONE_NUMBER;

							$rt['new_country'] = $country_code;
							$rt['handpicked_ids'] = (isset($products['handpicked_ids'])) ? $products['handpicked_ids'] : '';
							$rt['product_total'] = $coun;
							$rt['random_string'] = '';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';

							if (!empty($products['send_status'])) {
								$rt['status'] = $products['send_status'];
								$rt['total_products'] = $coun;
								$rt['next_page'] = $npage;
							}

							$rt['status_text'] = 'failed';
							$rt['message'] = 'Products Not Found.';

							if ($page <= 1 && !empty($request['show_single'])) {
								$rt['message'] = 'No products found in Single store. For more products, switch to Wholesale store from left navigation menu.';
							}

							if (!empty($search)) {
								$rt['extended_message'] = "Search Term: '" . $search . "'. \n You can find similar items in other categories, tap on button given below to switch category.";
							}
							if (!empty($products['filter_facets']['price'][0]) || !empty($products['filter_facets']['price'][1])) {
								if (!empty($request['app_version_code']) && ((int) $request['app_version_code'] > 70 || ($this->_request_from == 'IOS_APP' && (int) $request['app_version_code'] > 1))) {
									if (isset($products['filter_facets']['rating'])) {
										$rating_arr_old = $products['filter_facets']['rating'];
										$rating_arr_new = array();
										if (isset($rating_arr_old['0.0'])) {unset($rating_arr_old['0.0']);}
										if (isset($rating_arr_old['3.0'])) {$rating_arr_new['3.0'] = array('label' => 'Average', 'count' => $rating_arr_old['3.0']);}
										if (isset($rating_arr_old['4.0'])) {$rating_arr_new['4.0'] = array('label' => 'Good', 'count' => $rating_arr_old['4.0']);}
										if (isset($rating_arr_old['5.0'])) {$rating_arr_new['5.0'] = array('label' => 'Excellent', 'count' => $rating_arr_old['5.0']);}

										$products['filter_facets']['rating'] = $rating_arr_new;
									}
								}
								$rt['data']['filter_facets'] = $products['filter_facets'];
							}
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';

					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		if (empty($request_data)) {
			echo json_encode($rt);exit;
		} else {
			return $rt;
		}
	}
	/* *******
		 * Function : getProduct
		 * Request Parameters : user_id,access_token,product_id
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"products":{"product_id":"533","name":" Cotton Kurti Blue Colour Gold Print","set_description":"1 Set = Total 6 pieces; 1 each of sizes 36&quot;,38&quot;, 40&quot;, 42&quot;, 44&quot; and 46&quot;","description":"Best quality Jaipur cotton Kurti\r\nPure cotton (60-60)\r\nNo color bleed \r\nNo shrinkage               \r\nInterlocking done everywhere","meta_title":" Cotton Kurti Blue Colour Gold Print @ Wholesale Prices","meta_description":" Cotton Kurti Blue Colour Gold Print @ Wholesale Prices","meta_keyword":"Cotton,Kurti,Blue,Colour,Gold,Print, ethnicwear, wholesale","tag":"Cotton,Kurti,Blue,Colour,Gold,Print ethnicwear, wholesale","model":"VAS_JP_VJKPFG10","sku":"VJKPFG10","upc":"","ean":"","jan":"","isbn":"","mpn":"","location":"","quantity":"2","stock_status":"In Stock","image":"catalog\/VAS_JP\/kurti\/VJKPFG10.jpg","manufacturer_id":null,"manufacturer":null,"price":"235.0000","piece_in_set":"6","seller_tax":"5.0000","commission":"5.0000","special":null,"reward":null,"points":"0","tax_class_id":"9","date_available":"0000-00-00","weight":"1.20000000","weight_class_id":"1","length":"0.00000000","width":"0.00000000","height":"0.00000000","length_class_id":"1","subtract":"1","rating":0,"reviews":0,"minimum":"1","sort_order":"0","status":"1","date_added":"2015-10-12 11:51:31","date_modified":"2015-11-03 07:31:03","viewed":"164","sold_out":"53"}}}}
	*/

	public function product_details() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$pid = $request['product_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$product = $this->model_restapi_service->getProduct((int) $pid, (int) $user_id);
						if (!empty($product)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['data']['products'] = $product;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Products Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/* *******
		 * Function : add_product_to_wishlist
		 * Request Parameters : user_id,access_token,product_id,share_message
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success",}}
	*/

	public function add_product_to_wishlist() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$product_id = trim($request['product_id'] ?? '', ", \t\n\r\0\x0B");
					$message = $this->db->escape($request['share_message']);
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$check_wishlist = $this->model_restapi_service->checkWishlist((int) $customer_id, (int) $product_id);
						if ($check_wishlist == 0) {
							$status = $this->model_restapi_service->addProductToWishlist((int) $customer_id, (int) $product_id, (string) $message);
							$wdata = array(
								'customer_id' => $customer_id,
							);
							$wishlist_coun = $this->model_restapi_service->getTotalWishlist($wdata);
							if ($status == '1') {
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = "Product Added to shortlist";
								$rt['data']['wishlist_count'] = $wishlist_coun;
							} else {
								$rt['error_code'] = '1001';
								$rt['status'] = '0';
								$rt['status_text'] = 'failed';
							}
						} else {

							$this->model_restapi_service->deleteProductToWishlist((int) $customer_id, (string) $product_id);
							$status = $this->model_restapi_service->addProductToWishlist((int) $customer_id, (int) $product_id, (string) $message);

							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'Already added to shortlist.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}
	/* *******
		 * Function : delete_product_to_wishlist
		 * Request Parameters : user_id,access_token,product_id
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","message":"Product Removed Successfully from your wishlist"}}
	*/

	public function delete_product_to_wishlist() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$product_id = trim($request['product_id'] ?? '', ", \t\n\r\0\x0B");
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$status = $this->model_restapi_service->deleteProductToWishlist((int) $customer_id, (string) $product_id);
						if ($status == '1') {
							$wdata = array(
								'customer_id' => $customer_id,
							);
							$wishlist_coun = $this->model_restapi_service->getTotalWishlist($wdata);
							$rt['data']['wishlist_count'] = $wishlist_coun;
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = "Product Removed Successfully from your Shortlist";
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/* *******
		 * Function : clear_product_to_wishlist
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","message":"Products Removed Successfully from your wishlist"}}
	*/

	public function clear_product_to_wishlist() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$status = $this->model_restapi_service->clearProductToWishlist((int) $customer_id);
						if ($status == '1') {
							$wdata = array(
								'customer_id' => $customer_id,
							);
							$wishlist_coun = $this->model_restapi_service->getTotalWishlist($wdata);
							$rt['data']['wishlist_count'] = $wishlist_coun;
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = "Products Removed Successfully from your wishlist";
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/* *******
		    * Function : get_wishlist
		    * Request Parameters : user_id,access_token
		    * Type : Post
		    * Output : {{"status":"1","status_text":"Success","data":{"List Data"}}}
	*/

	public function get_wishlist() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if (isset($request['page'])) {
							$page = $request['page'];
						} else {
							$page = 1;
						}
						$limit = 500;
						$data = array(
							'customer_id' => $customer_id,
							'start' => ($page - 1) * $limit,
							'limit' => $limit,
						);

						$list = $this->model_restapi_service->getWishlist($data);
						$wdata = array(
							'customer_id' => $customer_id,
						);
						$coun = $this->model_restapi_service->getTotalWishlist($wdata);
						if ($coun > $limit) {
							$tpage = ceil($coun / $limit);
							$cpage = $page;
							$npage = $cpage + 1;
							if ($npage > $tpage) {
								$npage = 0;
							}
							$ppage = $cpage - 1;
							if ($cpage < 1) {
								$ppage = 0;
							}

						} else {
							$tpage = ceil($coun / $limit);
							$cpage = $page;
							$npage = '0';
							$ppage = '0';
						}

						if (!empty($list)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['current_page'] = $cpage;
							$rt['next_page'] = $npage;
							$rt['previous_page'] = $ppage;
							$rt['total_page'] = $tpage;
							$rt['total_records'] = $coun;
							$rt['data'] = $list;

						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = "Error in database query";
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		echo json_encode($rt);exit;
	}
	/* *******
		 * Function : update_customer_setting
		 * Request Parameters : user_id,access_token,key,value
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"List Data"}}}
	*/

	public function update_customer_setting() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$key = $request['key'];
					$value = $request['value'];
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$status = $this->model_restapi_service->UpdateCustomerSetting((int) $customer_id, (string) $key, (string) $value);
						if ($status == '1') {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
						} else if ($status == '1') {
							$rt['status'] = '1001';
							$rt['status_text'] = 'Invalid Key';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		echo json_encode($rt);exit;
	}

	/* *******
		    * Function : get_filters_by_category
		    * Request Parameters : user_id,access_token,category_id
		    * Type : Post
		    * Output : {{"status":"1","status_text":"Success","data":{"Filters Data"}}}
		    *
	*/

	public function get_filters_by_category() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token']) || $this->_request_from == 'IOS_APP') {
				if (isset($request['user_id']) || $this->_request_from == 'IOS_APP') {
					$access_token = isset($request['access_token']) ? $request['access_token'] : '';
					$user_id = isset($request['user_id']) ? $request['user_id'] : 0;
					$cid = $request['category_id'];
					if (isset($request['show_single'])) {
						$is_single = $request['show_single'];
					} else {
						$is_single = 0;
					}
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($this->_request_from == 'IOS_APP' && $user_id == 0) {
						$check_access_token = 1;
					}
					if ($check_access_token > 0) {

						$filter_groups = $this->model_restapi_service->getFiltersOfProducts((int) $cid, (int) $is_single);

						if ($filter_groups) {
							foreach ($filter_groups as $filter_group) {
								$childen_data = array();

								foreach ($filter_group['filter'] as $filter) {
									$filter_data = array(
										'filter_category_id' => $cid,
										'filter_sub_category' => true,
										'filter_filter' => $filter['filter_id'],
									);

									$childen_data[] = array(
										'filter_id' => $filter['filter_id'],
										'name' => html_entity_decode(trim($filter['name'])),
									);
								}

								$data['filter_groups'][] = array(
									'filter_group_id' => $filter_group['filter_group_id'],
									'name' => html_entity_decode(trim($filter_group['name'])),
									'description' => html_entity_decode(trim($filter_group['description'])),
									'filter' => $childen_data,
								);
							}
						}
						$filters = $data['filter_groups'];

						$customvalrate = "20000";
						$filter_rate_arr = array();
						$filter_rate_arr['filter_group_id'] = $customvalrate;
						$filter_rate_arr['name'] = "Ratings";
						$filter_rate_arr['description'] = "";

						$i = 0;
						$rate_filter = array();
						$minimum_rating = 3;
						if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
							$minimum_rating = 4;
						}
						for ($ratings = 5; $ratings >= $minimum_rating; $ratings--) {
							$selected = "NO";
							$rating_name = $ratings . ' star';
							$filter_id = $customvalrate + $ratings;
							if ($ratings > 4) {
								$quality_tag = 'Excellent Quality';
							} elseif ($ratings > 3) {
								$quality_tag = 'Good Quality';
							} elseif ($ratings > 2) {
								$quality_tag = 'Average Quality';
							}
							$rate_filter[$i]['filter_id'] = $filter_id;
							if (isset($this->restapi->getRequestHeader()['REQUEST_BY']) && strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
								$rate_filter[$i]['name'] = $quality_tag;
							} else {
								if (isset($request['app_version_code']) && $request['app_version_code'] > 59) {
									$rate_filter[$i]['name'] = $quality_tag;
								} else {
									$rate_filter[$i]['name'] = $rating_name;
								}
							}
							$rate_filter[$i]['selected'] = $selected;
							$i++;
						}
						$rate_filter[$i] = array('filter_id' => $customvalrate + 0, 'name' => 'All', 'selected' => 'NO');

						//echo "<pre>"; print_r($rate_filter); exit;
						$filter_rate_arr['filter'] = $rate_filter;
						array_unshift($filters, $filter_rate_arr);

						/*Price Filters By Parth 11/8/16*/
						$price_range = $this->model_restapi_service->getPriceFilterByCategoryForPrice_filter($cid, $is_single);
						if (!empty($filters)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['data'] = $filters;
							$rt['price_range'] = $price_range;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}
	/* *******
		 * Function : get_options_by_category
		 * Request Parameters : user_id,access_token,category_id
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"Filters Data"}}}
		 *
	*/

	public function get_options_by_category() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$cid = $request['category_id'];
					$is_single = $request['show_single'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						$option_groups = $this->model_catalog_category->getOptionsOfProducts((int) $category_id);

						if (isset($option_groups) && !empty($option_groups)) {
							foreach ($option_groups as $option_group) {
								$option_children = array();

								foreach ($option_group['option'] as $option) {
									$option_data = array(
										'option_category_id' => $category_id,
										'option_sub_category_id' => true,
										'option_option' => $option['option_value_id'],
									);
									$option_children[] = array(
										'option_value_id' => $option['option_value_id'],
										'option_value' => $option['option_value'],
									);

								}
								$data['option_groups'][] = array(
									'option_group_id' => $option_group['option_id'],
									'name' => $option_group['option_name'],
									'option' => $option_children,
								);
							}

						}
						$options = $data['option_groups'];

						if (!empty($options)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['data'] = $filters;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * [sendMessageOnRegistration description]
	 * @param  string $mobile mobile number on which sms is sent
	 * @param  string $password    password string
	 * @return void
	 * @author Ravindra Singh
	 */
	public function sendPasswordOnAppRegistration($mobile, $password, $data = array()) {

		$this->load->language('account/sms_templates');
		$message = $this->language->get('on_app_signup');

		$msg = sprintf($message, $password);
		if (SITE_ENVIRONMENT == 'Production') {
			$csv_sms = new SMS($msg, $mobile);
			$res = $csv_sms->sendOTPMessage();
			$sta = explode('|', $res);
			/*$this->db->query("INSERT INTO ".DB_PREFIX."otp_failure
				SET telephone = "."'".$mobile."'".",
				status = "."'".$sta[0]."'".",
				message="."'".$res."'".",
			*/

			if ($sta[0] == 'success') {
				return $password;
			} else {
				return false;
			}
		} else {
			return $password;
		}
	}
	/**
	 * [sendCodeOnRegistration description]
	 * @param  string $email email on which code is sent
	 * @param  string $password    password string
	 * @return void
	 * @author Ravindra Singh
	 */
	public function sendPasswordOnAppForgotPassword($email, $password, $data = array()) {
		$this->load->language('account/sms_templates');
		$this->load->language('account/customer');
		$message = $this->language->get('forgot_pass_msg');
		if (isset($data['name']) && !empty($data['name'])) {
			$name = $data['name'];
		} else {
			$name = $email;
		}
		/*$msg = sprintf($message,$otp);
			$csv_sms = new SMS($msg, $mobile);
			$res=$csv_sms->sendMessage();
			$sta = explode('|', $res);

			$this->db->query("INSERT INTO ".DB_PREFIX."otp_failure
				SET telephone = "."'".$mobile."'".",
				status = "."'".$sta[0]."'".",
				message="."'".$res."'".",
		*/

		//$customer_data = $this->model_account_customer->getCustomer($this->session->data['customer_id']);
		//echo "<pre>"; print_r($customer_data); exit;
		$seller_email = $email;

		$html = sprintf($message, $name, $password);

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';

		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
		$mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

		$mail->addAddress($email, 'WholesaleBox');

		$mail->Subject = 'New Password.';
		$mail->msgHTML($html);

		if ($mail->send()) {

			/*$this->db->query("INSERT INTO ".DB_PREFIX."otp_failure
				status = "."'success'".",
				message="."'".$email."'".",
				email_sent = "."'1'".",
			*/
			$return = "success";
		} else {
			$return = "error";
		}
		if ($return == 'success') {
			return $password;
		} else {
			return false;
		}

	}

	public function sendPasswordOnAppForgotPasswordtest() {
		$email = "ravindra.shekhawat.rajnota@gmail.com";
		$password = "12346";
		$this->load->language('account/sms_templates');
		$this->load->language('account/customer');
		$message = $this->language->get('forgot_pass_msg');
		if (isset($data['name']) && !empty($data['name'])) {
			$name = $data['name'];
		} else {
			$name = $email;
		}
		/*$msg = sprintf($message,$otp);
			$csv_sms = new SMS($msg, $mobile);
			$res=$csv_sms->sendMessage();
			$sta = explode('|', $res);

			$this->db->query("INSERT INTO ".DB_PREFIX."otp_failure
				SET telephone = "."'".$mobile."'".",
				status = "."'".$sta[0]."'".",
				message="."'".$res."'".",
		*/

		//$customer_data = $this->model_account_customer->getCustomer($this->session->data['customer_id']);
		//echo "<pre>"; print_r($customer_data); exit;
		$seller_email = $email;

		$html = sprintf($message, $name, $password);

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';

		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
		$mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

		$mail->addAddress($email, 'WholesaleBox');

		$mail->Subject = 'Verification Code.';
		$mail->msgHTML($html);

		if ($mail->send()) {

			/*$this->db->query("INSERT INTO ".DB_PREFIX."otp_failure
				status = "."'success'".",
				message="."'".$email."'".",
				email_sent = "."'1'".",
			*/
			echo $return = "success";die;
		} else {
			echo $return = "error";exit;
		}
		if ($return == 'success') {
			return $password;
		} else {
			return false;
		}

	}

	/**
	 * [sendMessageOnRegistration description]
	 * @param  string $mobile mobile number on which sms is sent
	 * @param  string $otp    otp number
	 * @return void
	 * @author Ravindra Singh
	 */
	public function sendMessageOnAppRegistration($mobile, $otp) {
		$this->load->language('account/sms_templates');
		$message = $this->language->get('on_app_signup');
		$msg = sprintf($message, $otp);
		if (SITE_ENVIRONMENT == 'Production') {
			$csv_sms = new SMS($msg, $mobile);
			$res = $csv_sms->sendMessage();
			$sta = explode('|', $res);

			$this->db->query("INSERT INTO " . DB_PREFIX . "otp_failure
			SET telephone = " . "'" . $mobile . "'" . ",
			status = " . "'" . $sta[0] . "'" . ",
			message=" . "'" . $res . "'" . ",
			date_added=NOW()");

			if ($sta[0] == 'success') {
				return $otp;
			} else {
				return false;
			}
		} else {
			return $otp;
		}
	}
	/**
	 * [sendCodeOnRegistration description]
	 * @param  string $email email on which code is sent
	 * @param  string $otp    otp number
	 * @return void
	 * @author Ravindra Singh
	 */
	public function sendCodeOnAppRegistration($email, $otp) {
		$this->load->language('account/sms_templates');
		$this->load->language('account/customer');
		$message = $this->language->get('on_app_signup');
		/*$msg = sprintf($message,$otp);
			$csv_sms = new SMS($msg, $mobile);
			$res=$csv_sms->sendMessage();
			$sta = explode('|', $res);

			$this->db->query("INSERT INTO ".DB_PREFIX."otp_failure
				SET telephone = "."'".$mobile."'".",
				status = "."'".$sta[0]."'".",
				message="."'".$res."'".",
		*/

		//$customer_data = $this->model_account_customer->getCustomer($this->session->data['customer_id']);
		//echo "<pre>"; print_r($customer_data); exit;
		$seller_email = $email;

		$html = sprintf($message, $otp);

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';

		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
		$mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

		$mail->addAddress($email, 'WholesaleBox');

		$mail->Subject = 'Verification Code.';
		$mail->msgHTML($html);

		if ($mail->send()) {

			$this->db->query("INSERT INTO " . DB_PREFIX . "otp_failure
			status = " . "'success'" . ",
			message=" . "'" . $email . "'" . ",
			email_sent = " . "'1'" . ",
			date_added=NOW()");
			$return = "success";
		} else {
			$return = "error";
		}
		if ($return == 'success') {
			return $otp;
		} else {
			return false;
		}

	}

	/**
	 * Buy all shortlisted products direct from app
	 * Function : buy_now_direct
	 * Type : Post
	 * @param
	 * Output : {{"status":"1","status_text":"Success","data":{"login link"}}}
	 * @return void
	 * @author Ravindra Singh
	 */

	public function buy_now_direct() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						if (isset($request['product_id']) && !empty($request['product_id'])) {
							$products = explode(",", $request['product_id']);

							$dynamicotp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
							if ($this->model_restapi_service->UpdateCartShortlistOtp((int) $user_id, $dynamicotp) == '1') {
								$ar[$user_id] = $dynamicotp;
								$randstr = base64_encode(serialize($ar));
								$link = HTTP_SERVER . "index.php?route=account/account/autoLoginEmail/" . $randstr;
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['userlink'] = $link;
							} else {
								$rt['error_code'] = '1000';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Could Not Update Cart.';
							}

						} else {
							$rt['error_code'] = '1000';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Invalid Products.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	public function debug() {
		$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
		//$txt = print_r($request);
		fwrite($myfile, $inputJSON);
		fclose($myfile);
		/*

			$myfile = fopen("newfilerequest.txt", "w") or die("Unable to open file!");
			//$txt = print_r($request);
			fwrite($myfile, $inputJSON);
			fclose($myfile);
			$myfile = fopen("newfileresponse.txt", "w") or die("Unable to open file!");
			//$txt = print_r($request);
			fwrite($myfile, json_encode($rt));
		*/
	}

	/**
	 *
	 */
	public function validateApiCall() {
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		$this->manage_error_reporting();
		if ($this->config->get('config_app_maintenance') == 1) {
			$rt['error_code'] = '8888';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Hey!! Engineers @ work!!. We will be back shortly. C Ya';
			echo json_encode($rt);exit;
		}
		return true;

		$this->load->model('restapi/service');

		$headers = getallheaders();

		if (!$this->model_restapi_service->validateApiCall($headers)) {
			$rt['error_code'] = '9999';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Invalid API call';
			echo json_encode($rt);exit;
		}
	}
	/* *******
		 * Function : getUserTemplates
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"User Templates"}}}
		 *
	*/

	public function getUserTemplates() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token']) || isset($request['b'])) {
				if (isset($request['user_id']) || isset($request['a'])) {

					if (isset($request['b']) && !empty($request['b'])) {
						$access_token = $request['b'];
					} else {
						$access_token = $request['access_token'];
					}
					if (isset($request['a']) && !empty($request['a'])) {
						$user_id = $request['a'];
					} else {
						$user_id = $request['user_id'];
					}
					$customer_id = $user_id;
					$user = $this->model_restapi_service->checkCustomerByIdwithAdd((int) $user_id);
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$templates = $this->model_restapi_service->getUserTemplates((int) $customer_id);
						$default_template = $this->model_restapi_service->getDefaultTemplate((int) $customer_id);
						$admin_templates = $this->model_restapi_service->getUserTemplates('0');

						$find = array('[product_name]', '[set]',
							'[model]', '[markup_price]',
							'[shop_name]', '[my_name]', '[mobile]', '[set_showonlysize]');

						$replace = array('Cotton Palazzo',
							'1 set = Total 5 pieces of Free Size',
							'XYZ_123',
							'Rs 450',
							$user['company'],
							$user['firstname'],
							$user['telephone'],
							'Size = 40,42,44');

						$temp = array();
						$i = 0;
						if ($templates) {

							foreach ($templates as $template) {
								$temp[$i]['template_id'] = $template['template_id'];
								$temp[$i]['template_title'] = $template['title'];
								$temp[$i]['template_text'] = $template['template'];
								$temp[$i]['template_preview'] = str_replace($find, $replace, $template['template']);
								$temp[$i]['is_admin_default'] = 0;
								if ($default_template == $template['template_id']) {
									$temp[$i]['template_status'] = 1;
								} else {
									$temp[$i]['template_status'] = 0;

								}
								$i++;
							}
						}
						$j = $i;
						if ($admin_templates) {
							foreach ($admin_templates as $admin_template) {
								$temp[$j]['template_id'] = $admin_template['template_id'];
								$temp[$j]['template_title'] = $admin_template['title'];
								$temp[$j]['template_text'] = $admin_template['template'];
								$temp[$j]['template_preview'] = str_replace($find, $replace, $admin_template['template']);
								$temp[$j]['is_admin_default'] = 1;
								if ($default_template == $admin_template['template_id']) {
									$temp[$j]['template_status'] = 1;
								} else {
									$temp[$j]['template_status'] = 0;

								}
								$j++;
							}
						}

						if (!empty($temp)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['data'] = $temp;
							$rt['short_tags'] = array('[product_name]', '[set]',
								'[model]', '[markup_price]',
								'[shop_name]', '[my_name]', '[mobile]', '[set_showonlysize]');

							$rt['dummy_text'][0]['key'] = '[product_name]';
							$rt['dummy_text'][0]['value'] = 'Cotton Patiyala Suit';

							$rt['dummy_text'][1]['key'] = '[set]';
							$rt['dummy_text'][1]['value'] = '1 set = 4 pieces of same color';

							$rt['dummy_text'][2]['key'] = '[model]';
							$rt['dummy_text'][2]['value'] = 'XYZ_123';

							$rt['dummy_text'][3]['key'] = '[markup_price]';
							$rt['dummy_text'][3]['value'] = 'Rs. 300';

							$rt['dummy_text'][4]['key'] = '[shop_name]';
							$rt['dummy_text'][4]['value'] = $user['company'];

							$rt['dummy_text'][5]['key'] = '[my_name]';
							$rt['dummy_text'][5]['value'] = $user['firstname'];

							$rt['dummy_text'][6]['key'] = '[mobile]';
							$rt['dummy_text'][6]['value'] = $user['telephone'];

							$rt['dummy_text'][7]['key'] = '[set_showonlysize]';
							$rt['dummy_text'][7]['value'] = 'Size = 40,42,44';

						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {

						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;

	}

	/* *******
		 * Function : delete_user_template
		 * Request Parameters : user_id,access_token,template_id
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","message":"Product Removed Successfully from your wishlist"}}
	*/

	public function delete_user_template() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$template_id = rtrim(ltrim($request['template_id'], ','), ',');
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$status = $this->model_restapi_service->deleteUserTemplate($template_id);
						if ($status == '1') {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = "User Template Removed Successfully from your wishlist";
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/* *******
		 * Function : update_quality_expectations
		 * Request Parameters : user_id,access_token,quality_expectations
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"Quality Expectation Data"}}}
		 *
	*/

	public function update_quality_expectations() {
		// Oct 2019: this method is deprecated
		$rt = array();
		$rt['error_code'] = '1001';
		$rt['status'] = '0';
		$rt['status_text'] = 'Failed';
		$rt['message'] = 'Please update your app';
		echo json_encode($rt);exit;
	}

	/* *******
		 * Function : get_quality_expectations
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"Quality Expectation Data"}}}
		 *
	*/

	public function get_quality_expectations() {
		// Oct 2019: this method is deprecated
		$rt = array();
		$rt['error_code'] = '1001';
		$rt['status'] = '0';
		$rt['status_text'] = 'Failed';
		$rt['message'] = 'Please update your app';
		echo json_encode($rt);exit;
	}
	/* *******
		 * Function : saveUserTemplate
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success","data":{"Quality Expectation Data"}}}
		 *
	*/
	public function saveUserTemplate() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$template_title = $request['template_title'];
					$template_text = $request['template_text'];
					$user_id = $request['user_id'];
					if (isset($request['template_id'])) {
						$template_id = $request['template_id'];
					} else {
						$template_id = '';
					}
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$templates = $this->model_restapi_service->setUserTemplates($template_id, (int) $customer_id, $template_text, $status = 1, $template_title);
						if ($templates) {
							$rt['status'] = '1';
							$rt['status_text'] = 'New Template Created Successfully And Set As Default';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	public function setNewDefaultTemplate() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$template_id = $request['template_id'];
					$user_id = $request['user_id'];
					$customer_id = $user_id;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$templates = $this->model_restapi_service->setDefaultTemplate((int) $customer_id, (int) $template_id);
						if ($templates) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 *
	 * Function : get_chat_link
	 * Type : Post
	 * @param
	 * Output : {"status":"1","status_text":"Success","data":{"login link"}}
	 * @return void
	 * @author Ravindra Singh
	 */

	public function get_chat_link() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$link = HTTP_SERVER . "index.php?route=account/account/autoLoginEmail/";
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
						$rt['userlink'] = $link;

					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	public function live_chat() {

		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		//echo "here"; exit;
		$this->response->setOutput($this->load->view('default/template/restapi/live_chat.tpl', $data));

	}

	public function getOrdersOfCustomer() {
		$this->load->model('restapi/service');
		$this->load->model('account/invoice');
		$this->load->model('checkout/order');
		$this->load->model('localisation/order_status');
		$this->load->model('account/return');
		$this->load->model('restapi/return');
		$this->load->model('account/order');
		$headers = getallheaders();
		$login_by_franchise = 0;
		if (isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int) $headers['crm_role_id'] == (int) CRM_FRANCHISE_ROLE_ID) {
			$login_by_franchise = 1;
		}
		//     if(isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int)$headers['crm_role_id'] == (int)CRM_FRANCHISE_ROLE_ID){
		//         $rt['error_code'] = '1002';
		//         $rt['status'] = '0';
		//         $rt['status_text'] = 'Failed';
		//         $rt['message'] = 'You are not authorized to see this section.';
		//     }
		// else
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			$this->validateApiCall();
			$dummy_currency_id = $this->currency->currencies[DUMMY_INR_CURRENCY]['currency_id'];
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$app_version_code = isset($request['app_version_code']) ? $request['app_version_code'] : '0';

					$customer_info = $this->model_restapi_service->checkCustomerByIdwithAdd((int) $user_id);

					if (isset($request['page']) && $request['page'] > 0) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = '5';
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					$rs = $this->model_restapi_service->checkCustomerByID((int) $user_id);
					if ($check_access_token > 0) {
						if (($app_version_code > 62) || ($headers['REQUEST_BY'] == "IOS_APP" && $app_version_code >= 1)) {
							$return_reason = array(
								'Others Issues' => array(
									2 => 'Quality Issue',
									3 => 'Pricing',
									4 => 'Wrong item received',
								),
								'Replacement Issues' => array(
									1 => 'Manufacturing defect',
									13 => 'Wrong Item Received (Replacement)',
								),
							);

							$sql = "SELECT bank_ac_holder_name,bank_ac_number,ifsc_code FROM " . DB_PREFIX . "customer
									WHERE customer_id = " . $user_id . "";
							$bank_details = $this->db->query($sql);
							if ($bank_details->num_rows) {
								$bank_details = $bank_details->row;
							}
						}

						$start = ($page - 1) * $limit;

						// Getting order ids for the current page
						$sql = "SELECT DISTINCT o.order_id
								FROM " . DB_PREFIX . "order o
								LEFT JOIN " . DB_PREFIX . "suborder osub
									ON (osub.order_id = o.order_id) ";
						if ($login_by_franchise) {
							$sql .= " LEFT JOIN " . DB_PREFIX . "order_sales_staff oss
                          ON (oss.order_id = o.order_id)
                        LEFT JOIN " . DB_PREFIX . "sales_staff ss
                          ON (ss.staff_id = oss.sales_staff_id) ";
						}
						$sql .= " WHERE o.customer_id = '" . (int) $user_id . "'
  						        AND osub.order_status_id > 0 ";
						if ($login_by_franchise) {
							$sql .= " AND ss.crm_user_id = " . $headers['crm_user_id'];
						}
						if (!empty($request['order_id'])) {
							$sql .= " AND o.order_id = '" . (int) $request['order_id'] . "'";
						}
						$sql .= " ORDER BY o.order_id DESC LIMIT " . $start . "," . $limit;

						$query = $this->db->query($sql);
						if ($query->num_rows) {

						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Records Not Found.';
							echo json_encode($rt);
							exit;
						}

						//get all order status
						$order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

						$master_returns = $this->model_restapi_return->getMasterReturns((int) $user_id, 0);

						if (($app_version_code > 62) || ($headers['REQUEST_BY'] == "IOS_APP")) {
							if (!empty($master_returns)) {
								$return_shipment = array_column($master_returns->rows, "master_return_id");
								$return_shipment = array_combine($return_shipment, $master_returns->rows);
							}
						}

						$order_statuses = array();
						foreach ($order_statuses_qry as $order_status_data) {
							$order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
						}
						$order_statuses[0] = 'Missing Order';
						/* ---- END All Order Status --- */
						//$orders = array();
						$records = array();
						if ($query->num_rows) {
							$selector = array(
								'order' => array('select' => array('order_id',
									'order_no',
									'store_id',
									'payment_firstname',
									'payment_lastname',
									'payment_company',
									'payment_address_1',
									'payment_address_2',
									'payment_city',
									'payment_postcode',
									'payment_country',
									'payment_zone',
									'payment_method',
									'shipping_firstname',
									'shipping_lastname',
									'shipping_company',
									'shipping_address_1',
									'shipping_address_2',
									'shipping_city',
									'shipping_postcode',
									'shipping_country',
									'shipping_zone',
									'total',
									'currency_code',
									'date_added',
									'bank_slip_image',
									'code_version',
									'currency_code',
									'currency_value',
									'payment_code',
									'franchise_id',
									'currency_id',
								)),
								'suborder' => array('select' => array('order_id',
									'suborder_id',
									'invoice_no',
									'invoice_date',
									'order_status_id',
									'date_added',
									'delivered_date',
									'courier_partner',
									'tracking_no',
									'cform_submit',
									'total',
									'shipping_code',
									'no_wsb_tape',
									'no_invoice_with_shipment',
									'courier_partner_preference',
								)),
								'order_history' => array('select' => array('order_status_id',
									'date_added',
								)),
								//'order_product'=> array(),
							);

							$default_template_text = '';
							if (!empty($request['default_sharing_template'])) {
								$default_template_text = $request['default_sharing_template'];
							}
							$update_price_by = '0';
							if (!empty($request['sharing_margin'])) {
								$update_price_by = $request['sharing_margin'];
							}
							$find = array('[product_name]', '[set]',
								'[model]', '[markup_price]',
								'[shop_name]', '[my_name]', '[mobile]');

							require_once DIR_SYSTEM . 'library/buyerinvoice.php';
							foreach ($query->rows as $order) {
								$total = array();
								//$records = $this->model_checkout_order->getOrder($order['order_id'],'',$selector_tables);
								$records[$order['order_id']] = OrderInfo::getOrderInfo($this->db, $order['order_id'], '', $selector);

								// giving reported_deposit_amount and adding proper path in image
								if (!empty($records[$order['order_id']]['order']['bank_slip_image'])) {

									if (strpos($records[$order['order_id']]['order']['bank_slip_image'], "http") === false) {
										$records[$order['order_id']]['order']['bank_slip_image'] = HTTPS_SERVER . "image/" . $records[$order['order_id']]['order']['bank_slip_image'];
									}

									$amount = $this->model_restapi_service->getLatestTentativeAdvanceAmountUploadedByCustomer((int) $user_id, (int) $order['order_id']);
									$records[$order['order_id']]['order']['reported_deposit_amount'] = $amount;
								}

								// swap the shipping and billing address for old version of android app
								if ($this->_request_from !== 'IOS_APP' && $app_version_code < 103) {
									$shipping_firstname = $records[$order['order_id']]['order']['shipping_firstname'];
									$shipping_lastname = $records[$order['order_id']]['order']['shipping_lastname'];
									$shipping_company = $records[$order['order_id']]['order']['shipping_company'];
									$shipping_address_1 = $records[$order['order_id']]['order']['shipping_address_1'];
									$shipping_address_2 = $records[$order['order_id']]['order']['shipping_address_2'];
									$shipping_city = $records[$order['order_id']]['order']['shipping_city'];
									$shipping_postcode = $records[$order['order_id']]['order']['shipping_postcode'];
									$shipping_country = $records[$order['order_id']]['order']['shipping_country'];
									$shipping_zone = $records[$order['order_id']]['order']['shipping_zone'];

									$records[$order['order_id']]['order']['shipping_firstname'] = $records[$order['order_id']]['order']['payment_firstname'];
									$records[$order['order_id']]['order']['shipping_lastname'] = $records[$order['order_id']]['order']['payment_lastname'];
									$records[$order['order_id']]['order']['shipping_company'] = $records[$order['order_id']]['order']['payment_company'];
									$records[$order['order_id']]['order']['shipping_address_1'] = $records[$order['order_id']]['order']['payment_address_1'];
									$records[$order['order_id']]['order']['shipping_address_2'] = $records[$order['order_id']]['order']['payment_address_2'];
									$records[$order['order_id']]['order']['shipping_city'] = $records[$order['order_id']]['order']['payment_city'];
									$records[$order['order_id']]['order']['shipping_postcode'] = $records[$order['order_id']]['order']['payment_postcode'];
									$records[$order['order_id']]['order']['shipping_country'] = $records[$order['order_id']]['order']['payment_country'];
									$records[$order['order_id']]['order']['shipping_zone'] = $records[$order['order_id']]['order']['payment_zone'];

									$records[$order['order_id']]['order']['payment_firstname'] = $shipping_firstname;
									$records[$order['order_id']]['order']['payment_lastname'] = $shipping_lastname;
									$records[$order['order_id']]['order']['payment_company'] = $shipping_company;
									$records[$order['order_id']]['order']['payment_address_1'] = $shipping_address_1;
									$records[$order['order_id']]['order']['payment_address_2'] = $shipping_address_2;
									$records[$order['order_id']]['order']['payment_city'] = $shipping_city;
									$records[$order['order_id']]['order']['payment_postcode'] = $shipping_postcode;
									$records[$order['order_id']]['order']['payment_country'] = $shipping_country;
									$records[$order['order_id']]['order']['payment_zone'] = $shipping_zone;

								}

								$order_currency_value = $records[$order['order_id']]['order']['currency_value'];
								$order_currency_code = $records[$order['order_id']]['order']['currency_code'];
								$order_currency_id = $records[$order['order_id']]['order']['currency_id'];

								if ($records[$order['order_id']]['order']['payment_code'] == 'bank_transfer' || $records[$order['order_id']]['order']['payment_code'] == 'cod') {
									$records[$order['order_id']]['order']['should_upload_bank_slip'] = false;
								} else {
									$records[$order['order_id']]['order']['should_upload_bank_slip'] = false;
								}

								// fill order status in suborder
								foreach ($records[$order['order_id']]['suborder'] as $suborder_id => $suborder_detail) {

									$suborder_detail['order_status_id'] = (int) ($suborder_detail['order_status_id'] ?? 0);

									$suborder_detail['order_status'] = $order_statuses[$suborder_detail['order_status_id']];
									$suborder_detail['suborder_total'] = $this->currency->format(
										$suborder_detail['total'], $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']);

									// Create tracking url
									$suborder_detail['tracking_url'] = "";
									if (!(
										$suborder_detail['order_status_id'] == 2
										|| $suborder_detail['order_status_id'] == 5
										|| $suborder_detail['order_status_id'] == 15
									)
									) {
										if (!(empty($suborder_detail['tracking_no'])) && $suborder_detail['tracking_no'] != null && $suborder_detail['tracking_no'] != "") {
											$tracking_url = $this->getTrackingURL($suborder_detail['courier_partner']);
											$suborder_detail['tracking_url'] = $tracking_url . $suborder_detail['tracking_no'];
										}
									}

									$suborder_detail['show_product_review'] = "0";
									// fill order status in order history
									foreach ($suborder_detail['order_history'] as $key => $history_details) {
										if ($app_version_code > 62 || $this->_request_from == "IOS_APP") {
											if ($history_details['order_status_id'] == 15) {
												$suborder_detail['show_product_review'] = "1";
												$delivery_date = $history_details['date_added'];
												$delivered_date = date("Y-m-d 23:59:59", strtotime($delivery_date));
												$max_date_for_quality = strtotime(
													"+$this->_limit_in_days_for_quality days", strtotime($delivered_date));
												$max_date_for_replacement = strtotime(
													"+$this->_limit_in_days_for_replacement days", strtotime($delivered_date)
												);
												$date_today = time();
												$suborder_detail['max_date_for_replacement'] = DATE('d-m-y', $max_date_for_replacement);
												if ($date_today <= $max_date_for_replacement) {
													$suborder_detail['show_return_replacement_button'] = "yes";
													$suborder_detail['return_replacement_message'] = "";
												} else {
													$suborder_detail['show_return_replacement_button'] = 'no';
													$suborder_detail['return_replacement_message'] = "You can not return/replace this product as Return/Replacement time has expired. For any help, call on +91 141 4049163";
												}

												$shipping_code = '';
												if (!empty($suborder_detail['shipping_code'])) {
													$shipping_code = strtolower($suborder_detail['shipping_code']);
												}

												if (
													$date_today <= $max_date_for_quality &&
													!in_array($shipping_code, array("store_pickup", "warehouse_pickup", "weight.weight_0"))
												) {
													$suborder_detail['quality_reason_disabled'] = "no";
													$suborder_detail['return_replacement_message'] = "";
												} else {
													$suborder_detail['quality_reason_disabled'] = "yes";
													$suborder_detail['quality_reason_disabled_ids'] = "2,3,4";
												}

												if (!empty($records[$order['order_id']]['order']['franchise_id'])) {
													$suborder_detail['show_return_replacement_button'] = 'no';
													$suborder_detail['return_replacement_message'] = "You can not return/replace this product as this product is purchased from franchise store.";
												}
											}
										}
										$suborder_detail['order_history'][$key]['order_history_status'] = $order_statuses[$history_details['order_status_id']];
									}

									$suborder_detail['shipment_status_hex_color'] = $this->getShipmentStatusHexColor((int) $suborder_detail['order_status_id']);
									if (in_array($suborder_detail['order_status_id'], ORDER_STATUS_CLUSTERS['delivered'])) {
										$suborder_detail['shipment_status_text'] = 'Delivered on ' . date('dM', strtotime($suborder_detail['delivered_date']));
									} else {
										$order_status_list = array_flip(ORDER_STATUS);
										$suborder_detail['shipment_status_text'] = $order_status_list[$suborder_detail['order_status_id']];
									}

									if (!empty($suborder_detail['suborder_id'])) {
										$suborder_detail['pickup_city'] = $this->getSuborderPickUpCityName($suborder_detail['suborder_id']);
									} else {
										$suborder_detail['pickup_city'] = 'Jaipur';
									}

									// Filling in order totals for this suborder
									$buyer_invoice = new BuyerInvoice($this);
									$buyer_invoice->setOptions('file_type', 'b2b');
									$buyer_invoice->setOptions('show_image', TRUE);
									$order_info = $records[$order['order_id']];
									$order_info['suborder'] = array();
									$order_info['suborder'][$suborder_id] = $suborder_detail;
									$buyer_invoice->setOrderInfo($order_info);
									$get_products['order_product'] = $buyer_invoice->getProductsArrayBySuborderId(
										$order['order_id'], $suborder_detail['suborder_id']
									);
									$get_products['total'] = $buyer_invoice->getTotals(
										$order['order_id'], $suborder_detail['suborder_id']
									);

									foreach ($get_products['order_product'] as $key => $product_info) {
										$suborder[$product_info['order_product_id']]['suborder'] = $suborder_id;
										$suborder[$product_info['order_product_id']]['order_id'] = $order['order_id'];
										$get_products['order_product'][$key]['product_id'] = $get_products['order_product'][$key]['combo_product_id'];
										$other_imgs = array();
										$order_product_ids[] = $product_info['order_product_id'];

										$other_imgs = $this->model_restapi_service->getOtherImages((int) $product_info['product_id'], $product_info['image']);

										$get_products['order_product'][$key]['other_imgs'] = $other_imgs;

										$get_products['order_product'][$key]['product_price_total_inr'] = ($product_info['quantity'] * $product_info['piece_in_set']) *
											$product_info['price_per_piece'];

										$get_products['order_product'][$key]['product_price_total'] = $this->currency->format(($product_info['quantity'] *
											$product_info['piece_in_set'] *
											$product_info['price_per_piece']
										), $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']);

										$get_products['order_product'][$key]['price_per_piece_inr'] = $product_info['price_per_piece'];

										$get_products['order_product'][$key]['price_per_piece'] = $this->currency->format(
											$product_info['price_per_piece'], $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']);

										$get_products['order_product'][$key]['option'] = $this->model_restapi_service->getProductOptionsUsingSubOrderId((int) $order['order_id'], (string) $suborder_detail['suborder_id'], (int) $product_info['order_product_id']);

										$non_returnable = $this->model_restapi_service->checkReturnable((int) $product_info['product_id']);

										if (!$non_returnable) {
											$get_products['order_product'][$key]['returnable_message'] = "";
											$get_products['order_product'][$key]['is_returnable'] = 1;
										} else {
											$get_products['order_product'][$key]['returnable_message'] = "This item is non-returnable";
											$get_products['order_product'][$key]['is_returnable'] = 0;
										}

										if ($product_info['product_review'] != NULL && $product_info['product_review'] != '') {
											if ($product_info['product_review'] == 'like') {
												$get_products['order_product'][$key]['product_review'] = "1";
											}
											if ($product_info['product_review'] == 'dislike') {
												$get_products['order_product'][$key]['product_review'] = "0";
											}
										}

										$share_message = '';
										$up_price = $product_info['price_per_piece'];
										if (!empty($default_template_text)) {
											$testString = $up_price;
											$pr = ltrim(preg_replace("/[^0-9.]/", "", $testString), '.');
											if ($order_currency_id == $dummy_currency_id) {
												$pr = $pr / $this->currency->getValue(DUMMY_INR_CURRENCY);
											}
											$updated_price = ($update_price_by * $pr) / 100;
											$updated_price = ceil($updated_price + $pr);

											$updated_price = $this->currency->format($updated_price, $this->currency->getCode(), $this->currency->getValue(), false);
											// will round to 5 only if currency is inr
											if ($this->currency->getCode() == 'INR' || $this->currency->getCode() == DUMMY_INR_CURRENCY) {
												$updated_price = $this->model_restapi_service->roundUpToAny($updated_price, 5);
											}

											$uprice = $this->currency->format($updated_price, $this->currency->getCode(), 1);
											$up_price = $uprice;
											$replace = array(trim(html_entity_decode($product_info['name'])),
												html_entity_decode($product_info['comment']),
												$product_info['model'], $uprice, $customer_info['company'],
												$customer_info['firstname'] . ' ' . $customer_info['lastname'],
												$customer_info['telephone']);

											$share_message = str_replace($find, $replace, $default_template_text);
										}
										$get_products['order_product'][$key]['updated_price'] = $up_price;
										$get_products['order_product'][$key]['share_message'] = $share_message;
										$get_products['order_product'][$key]['template_text'] = $share_message;
									}
									// filling currency format in total
									$invoice_generated = $this->model_account_order->checkInvoiceStatus($suborder_id);
									$unset = false;
									// Changes made by NILESH. Changes are if paycharge discount is customly applied then breakup in display should be paycharge discount wise.
									$filter_total_arr = array();
									foreach ($get_products['total'] as $product_total_key => $product_total_value) {
										//echo "<pre>"; print_r($product_total_value[$product_total_key]);
										if ($product_total_key == 'amount_in_word') {
											$filter_total_arr[$product_total_key] = $get_products['total'][$product_total_key];
											$filter_total_arr[$product_total_key]['total_value'] = $product_total_value['value'];
											//$get_products['total'][$product_total_key]['total_value'] = $product_total_value['value'];
										} else {
											if ($unset) {
												unset($get_products['total'][$product_total_key]);
											} else {
												if ($product_total_key == 'paycharge' && !empty($product_total_value['breakup'])) {
													$paychage_data = $product_total_value['breakup'];
													foreach ($product_total_value['breakup'] as $dis_rate => $paycharge_arr) {
														$dis_percent = "Discount (" . (-1) * $dis_rate . "%)";
														$filter_total_arr[$product_total_key . '_' . $dis_rate]['total_value'] = $this->currency->format(
															$paycharge_arr['discount'], $records[$order['order_id']]['order']['currency_code'], 1, true);
														$filter_total_arr[$product_total_key . '_' . $dis_rate]['title'] = $dis_percent;
														$filter_total_arr[$product_total_key . '_' . $dis_rate]['value'] = (string) $paycharge_arr['discount'];
													}
												} else {
													$filter_total_arr[$product_total_key] = $get_products['total'][$product_total_key];
													$filter_total_arr[$product_total_key]['total_value'] = $this->currency->format(
														$product_total_value['value'], $records[$order['order_id']]['order']['currency_code'], 1, true);
													if ($product_total_key == 'total_amt' && !($invoice_generated)) {
														$filter_total_arr[$product_total_key]['title'] = "Tentative Invoice Amount";
														$unset = true;
													}
												}
											}
										}
									}
									$get_products['total'] = $filter_total_arr;

									$records[$order['order_id']]['suborder'][$suborder_id] = array_merge(
										$suborder_detail, $get_products
									);
								}

								$invoice_value = 0;
								$cashback_discount = 0;
								$net_payable = 0;

								$invoice_value = $records[$order['order_id']]['order']['total'];
								$cashback_discount = $this->model_account_order->getTotalCouponCashbackDiscountByOrder($order['order_id']);
								$net_payable = ($invoice_value - $cashback_discount);

								$records[$order['order_id']]['order']['total_amt'] = $this->currency->format($invoice_value, $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']);

								$payable_invoice_value_arr = array(
									'label' => 'Invoice Value',
									'value' => $this->currency->format($invoice_value, $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']),
								);
								if (!empty($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP') {
									$records[$order['order_id']]['order']['order_totals'][]['payable_invoice_value'] = $payable_invoice_value_arr;
								} else {
									$records[$order['order_id']]['order']['order_totals']['payable_invoice_value'] = $payable_invoice_value_arr;
								}

								$records[$order['order_id']]['order']['order_status_hex_color'] = $this->getOrderStatusHexColorCode($order['order_id']);

								if ((int) $cashback_discount != 0 && (int) $net_payable > 0) {
									$payable_cashback_coupon_discount_arr = array(
										'label' => 'Cashback/Coupon Discount',
										'value' => $this->currency->format(-$cashback_discount, $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']),
									);
									$payable_net_payable_arr = array(
										'label' => 'Net Payable After Cashback/Coupon Discount',
										'value' => $this->currency->format($net_payable, $records[$order['order_id']]['order']['currency_code'], $records[$order['order_id']]['order']['currency_value']),
									);

									if (!empty($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP') {
										$records[$order['order_id']]['order']['order_totals'][]['payable_cashback_coupon_discount'] = $payable_cashback_coupon_discount_arr;
										$records[$order['order_id']]['order']['order_totals'][]['payable_net_payable'] = $payable_net_payable_arr;
									} else {
										$records[$order['order_id']]['order']['order_totals']['payable_cashback_coupon_discount'] = $payable_cashback_coupon_discount_arr;
										$records[$order['order_id']]['order']['order_totals']['payable_net_payable'] = $payable_net_payable_arr;
									}
								}
							}
						}
						// Getting total count of orders for the customer
						$sql = "SELECT count(DISTINCT o.order_id) as total_orders
								FROM " . DB_PREFIX . "order o
								LEFT JOIN " . DB_PREFIX . "suborder osub
									ON(osub.order_id = o.order_id)
								WHERE o.customer_id = '" . (int) $user_id . "'
									AND osub.order_status_id > 0";
						$query = $this->db->query($sql);
						$coun = (int) $query->row['total_orders'];

						// Doing pagination calculations
						if ($coun > $limit) {
							$tpage = ceil($coun / $limit);
							$cpage = $page;
							$npage = $cpage + 1;
							if ($npage > $tpage) {
								$npage = 0;
							}
							$ppage = $cpage - 1;
							if ($cpage < 1) {
								$ppage = 0;
							}
						} else {
							$tpage = ceil($coun / $limit);
							$cpage = $page;
							$npage = '0';
							$ppage = '0';
						}
						if (($app_version_code > 62) || ($headers['REQUEST_BY'] == "IOS_APP")) {
							$result = $this->model_account_return->getReturnDetailsByProduct_ids($order_product_ids);
							if (isset($result) && !empty($result)) {
								foreach ($result as $key => $value) {
									if (!isset($ordercheck[$value['order_product_id']])) {
										$ordercheck[$value['order_product_id']] = '';
									}

									$return_actions_for_btn = array(
										RETURN_ACTION_IDS['Old_Returned_Goods_Received'],
										RETURN_ACTION_IDS['Old_Replacement_Sent'],
										RETURN_ACTION_IDS['CN_For_Client'],
										RETURN_ACTION_IDS['DN_Generated_For_Seller'],
										RETURN_ACTION_IDS['Shipment_Lost_by_Courier'],
										RETURN_ACTION_IDS['Shipment_Lost_by_Courier_While_Resending_Back'],
										RETURN_ACTION_IDS['Goods_Received'],
										RETURN_ACTION_IDS['Extra_Goods_Received'],
										RETURN_ACTION_IDS['Short_Goods_Received'],
										RETURN_ACTION_IDS['Return_Complete'],
										RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
									);

									$return_action_id = $value['return_action_id'];
									$actions_to_upload_courier_details = array(
										0,
										RETURN_ACTION_IDS['Pending'],
										RETURN_ACTION_IDS['Return_Request_Accepted'],
										RETURN_ACTION_IDS['Replacement_Request_Accepted'],
										RETURN_ACTION_IDS['Self_Shipment'],
									);

									$op_id = $value['order_product_id'];
									$master_return_id = $value['master_return_id'];
									$order_id = $suborder[$op_id]['order_id'];
									$suborder_id = $suborder[$op_id]['suborder'];
									$buyer_invoice_no = $records[$order_id]['suborder'][$suborder_id]['invoice_no'];
									$suborder_status = $records[$order_id]['suborder'][$suborder_id]['order_status_id'];

									if (
										in_array(
											$return_action_id, $actions_to_upload_courier_details
										) &&
										$ordercheck[$value['order_product_id']] != "true" &&
										!empty($buyer_invoice_no) &&
										$buyer_invoice_no > 0 &&
										(
											$suborder_status == ORDER_STATUS['Delivered'] ||
											$suborder_status == ORDER_STATUS['Complete']
										)
									) {
										$ordercheck[$value['order_product_id']] = "true";
										$records[$order_id]['suborder'][$suborder_id]["return_status"][$op_id]["button_text"] = "Return In Process";
										$records[$order_id]['suborder'][$suborder_id]["return_status"][$op_id]["button_enabled"] = "yes";
										$records[$order_id]['suborder'][$suborder_id]["return_status"][$op_id]["return_message"] = "Your return request is in processing.We shall update you shortly";
										if ($value['master_return_id'] != 0) {

											if ($return_shipment[$master_return_id]['shipping_method'] == 'self_courier') {
												$records[$suborder[$value['order_product_id']]['order_id']]['suborder'][$suborder[$value['order_product_id']]['suborder']]['master_return_ids'][$value['master_return_id']] = $value['master_return_id'];
												if ($return_shipment[$master_return_id]['return_shipment_tracking_id'] > 0) {
													if (isset($records[$order_id]['suborder'][$suborder_id]['upload_courier_details_button_enabled'])) {
														if ($records[$order_id]['suborder'][$suborder_id]['upload_courier_details_button_enabled'] != "yes") {
															$records[$order_id]['suborder'][$suborder_id]['upload_courier_details_button_enabled'] = "no";
														}
													} else {
														$records[$order_id]['suborder'][$suborder_id]['upload_courier_details_button_enabled'] = "no";
													}
												} else {
													$records[$order_id]['suborder'][$suborder_id]['upload_courier_details_button_enabled'] = "yes";
												}
											}
										}
									} else if (
										in_array($value['return_action_id'], $return_actions_for_btn) &&
										$ordercheck[$value['order_product_id']] != "true"
									) {
										$ordercheck[$value['order_product_id']] = "true";
										$records[$suborder[$value['order_product_id']]['order_id']]['suborder'][$suborder[$value['order_product_id']]['suborder']]["return_status"][$value['order_product_id']]["button_text"] = "Returned";
										$records[$suborder[$value['order_product_id']]['order_id']]['suborder'][$suborder[$value['order_product_id']]['suborder']]["return_status"][$value['order_product_id']]["button_enabled"] = "no";
										$records[$suborder[$value['order_product_id']]['order_id']]['suborder'][$suborder[$value['order_product_id']]['suborder']]["return_status"][$value['order_product_id']]["return_message"] = "";
									}
								}
							}
						}
						/*                         * *
							                          For Ios Parsing if app version code greater than 1
							                          decoded JSON not parsed as array
						*/
						if (!empty($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP' && $request['app_version_code'] > 1) {
							foreach ($records as $key => $value) {
								foreach ($value['suborder'] as $_key => $_value) {
									unset($records[$key]['suborder'][$_key]['total']);
									$records[$key]['suborder'][$_key]['total'] = array();
									foreach ($_value['total'] as $total_key => $total_value) {
										$total_value['key'] = $total_key;
										$records[$key]['suborder'][$_key]['total'][] = $total_value;
									}
								}
							}
						}

						$order_info['suborder'][$suborder_id] = $suborder_detail;
						if (isset($records) && !empty($records)) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['current_page'] = $cpage;
							$rt['next_page'] = $npage;
							$rt['previous_page'] = $ppage;
							$rt['total_page'] = $tpage;
							$rt['total_orders'] = $coun;
							$rt['wsb_address'] = '<address><p>B-1, Crystal Mall, Banipark,<br>Jaipur - 302016<br>Rajasthan <br>Email: info@wholesalebox.in </p></address>';
							$rt['telephone'] = '(+91) 141 - 4049163';
							if (($app_version_code > 62) || ($headers['REQUEST_BY'] == "IOS_APP" && $app_version_code >= 1)) {
								$rt['support_telephone'] = '+918696491521';
								$rt['bank_details'] = $bank_details;
								$rt['return_reason'] = $return_reason;
							}
							$rt['data']['orders'] = $records;
							$rt['data']['seller'] = $records;
							$rt['whatsapp_number'] = WHATSAPP_NUMBER;
							$rt['phone_number'] = PHONE_NUMBER;
							$rt['text_no_wsb_tape'] = 'Don\'t use wholesalebox packing tape.';
							$rt['text_no_offline_invoice'] = 'Don\'t send invoice with shipment.';
							$rt['text_courier_preferences'] = 'Courier Partner Preference';
							if (isset($this->restapi->getRequestHeader()['REQUEST_BY']) && strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
								$rt['APP_VERSION'] = IOS_APP_VERSION;
							} else {
								$rt['ANDROID_APP_VERSION'] = ANDROID_APP_VERSION; //current Android app version is 58 .update/change ANDROID_APP_VERSION to APP_VERSION in app version >= 63.
							}
							$rt['ANDROID_APP_MESSAGE'] = 'You\'re missing out some great features, Update WholesaleBox App now!.';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Records Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);
		die;
	}

	public function getOrderStatusHexColorCode(int $order_id) {
		$this->load->model('restapi/service');
		$suborders = $this->model_restapi_service->getAllSubOrderStatus($order_id);
		$check_status = array_merge(ORDER_STATUS_CLUSTERS['delivered'], ORDER_STATUS_CLUSTERS['cancelled']);
		$inactive_status = 1;
		foreach ($suborders as $key => $value) {
			if (!in_array($value['order_status_id'], $check_status)) {
				$inactive_status = 0;
			}
		}
		if ($inactive_status) {
			return '#a0a0a0';
		} else {
			return '#f0313f';
		}
	}

	public function getShipmentStatusHexColor(int $order_status_id) {
		switch ($order_status_id) {
		case ORDER_STATUS['Pending']:
			return '#f13041';
			break;
		case in_array($order_status_id, ORDER_STATUS_CLUSTERS['shipped']):
			return '#4ED900';
			break;
		case in_array($order_status_id, ORDER_STATUS_CLUSTERS['delivered']):
			return '#CCCCCC';
			break;
		case in_array($order_status_id, ORDER_STATUS_CLUSTERS['processed']):
			return '#233c98';
			break;
		default:
			# code...
			break;
		}
	}

	public function getSuborderPickUpCityName(string $suborder_id) {
		$pickup_code = substr($suborder_id, 12);

		switch ($pickup_code) {
		case 'JP':
			return 'Jaipur';
			break;
		case 'ST':
			return 'Surat';
			break;
		case 'DL':
			return 'Delhi';
			break;
		default:
			return 'Jaipur';
			break;
		}
	}

	public function setPdf() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					$rs = $this->model_restapi_service->checkCustomerByID((int) $user_id);
					if ($check_access_token > 0) {
						$order_id = $request['order_id'];
						$suborder_id = $request['suborder_id'];
						if ($this->model_restapi_service->checkUserOrder((int) $user_id, (int) $order_id)) {

							$filename = array();
							$filename['order_id'] = $order_id;
							$filename['suborder_id'] = $suborder_id;
							$filename = base64_encode(serialize($filename));
							$buyer_invoice = new BuyerInvoice($this, $filename);
							$buyer_invoice->setOptions('get_full_path', TRUE);
							$detail_invoice = base64_decode($buyer_invoice->getFile());
							//echo $detail_invoice; exit();
							header('Content-Description: File Transfer');
							header('Content-Type: application/pdf');
							header('Content-disposition: attachment; filename=' . basename($detail_invoice));
							header('Expires: 0');
							header('Cache-Control: no-cache');
							header('Pragma: public');
							header('Content-Length: ' . filesize($detail_invoice));
							ob_clean();
							flush();
							readfile($detail_invoice);
							exit();
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
			}

			// echo json_encode($rt); exit;

		}
		echo json_encode($rt);exit;

	}
	/* *******
		* Function : deep_link
		* Request Parameters : user_id,access_token,read_status,notification_id
		* Type : Post
		* Output : {{"status":"1","status_text":"Success","data":{"filers, sorting and other parameters"}}}
		*
	*/
	public function notification_tracking() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$read_status = 1;
					$notification_id = $request['notification_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$this->model_restapi_service->trackNotification((int) $notification_id, (int) $user_id, $read_status);
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';

					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}
	/* *******
		* Function : deep_link
		* Request Parameters : user_id,access_token,link
		* Type : Post
		* Output : {{"status":"1","status_text":"Success","data":{"filers, sorting and other parameters"}}}
		*
	*/
	public function deep_link() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$link = $request['link'];

					$url = parse_url($link);
					if (isset($url['path'])) {
						$path = explode('/', $url['path']);
						$path = array_filter($path);
						$new_path = array_pop($path);
						$cat = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias
					                              WHERE keyword='" . $this->db->escape($new_path) . "'
					                                AND is_custom = 1");

						if ($cat->num_rows) {
							if ($cat->row['url_type'] == 'category') {
								$category_id = $cat->row['search_id'];
							} else if ($cat->row['url_type'] == 'product') {
								$product_id = $cat->row['search_id'];
							}

						} else {
							$cat = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias
					                                 WHERE keyword='" . $this->db->escape($new_path) . "'");

							if (explode('=', $cat->row['query'])[0] == 'category_id') {
								$category_id = explode('=', $cat->row['query'])[1];
							} elseif (explode('=', $cat->row['query'])[0] == 'product_id') {
								$product_id = explode('=', $cat->row['query'])[1];
							}
						}

						if ($new_path == 'cart') {
							$open_view = 'cart';
						}
						if ($new_path == 'wishlist') {
							$open_view = 'wishlist';
						}
						if (isset($url['query']) && $url['query'] == 'route=account/credit_application') {
							$open_view = 'open_credit_form';
						}
						if (isset($url['query']) && $url['query'] == 'route=account/wishlist') {
							$open_view = 'wishlist';
						}
					}

					$query_arr = array();
					if (isset($url['query'])) {
						$query_arr = explode('&', $url['query']);
					} elseif (isset($url['fragment'])) {
						$query_arr = explode('&', substr($url['fragment'], 1));
						$set_filter = true;
					}

					$inner_query_arr = [];
					$inner_query_arr['category_id'] = isset($category_id) ? $category_id : '';
					$inner_query_arr['product_id'] = isset($product_id) ? $product_id : '';
					$inner_query_arr['open_view'] = isset($open_view) ? $open_view : '';
					$i = 0;
					foreach ($query_arr as $value) {
						$arr = explode('=', $value);

						if ($arr[0] == 'open_view') {
							$open_view = $arr[1];
						}

						if ($arr[0] == 'filter') {
							$inner_query_arr['filter'] = $arr[1];
						}
						if ($arr[0] == 'rating_filter') {
							switch ($arr[1]) {
							case 1:
								if (isset($inner_query_arr['filter'])) {
									$inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20001";
								} else {
									$inner_query_arr['filter_options'] = "20001";
								}

								break;
							case 2:
								if (isset($inner_query_arr['filter'])) {
									$inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20002";
								} else {
									$inner_query_arr['filter_options'] = "20002";
								}
								break;
							case 3:
								if (isset($inner_query_arr['filter'])) {
									$inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20003";
								} else {
									$inner_query_arr['filter_options'] = "20003";
								}
								break;
							case 4:
								if (isset($inner_query_arr['filter'])) {
									$inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20004";
								} else {
									$inner_query_arr['filter_options'] = "20004";
								}
								break;
							case 5:
								if (isset($inner_query_arr['filter'])) {
									$inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20005";
								} else {
									$inner_query_arr['filter_options'] = "20005";
								}
								break;
							default:
								if (isset($inner_query_arr['filter'])) {
									$inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20000";
								} else {
									$inner_query_arr['filter_options'] = "20000";
								}
								break;
							}
						}if ($arr[0] == 'sort') {
							$inner_temp = $arr[1];
						}
						if ($arr[0] == 'order') {
							if ($arr[1] == 'DESC') {
								if (isset($inner_temp)) {
									if ($inner_temp == 'p.date_added') {
										$inner_query_arr['sort_options'] = 'latest_designs';
									} elseif ($inner_temp == 'p.selling_price') {
										$inner_query_arr['sort_options'] = 'price_high_to_low';
									}
								}
							} else {
								if (isset($inner_temp)) {
									if ($inner_temp == 'p.selling_price') {
										$inner_query_arr['sort_options'] = 'price_low_to_high';
									}
								}
							}
						}if ($arr[0] == 'search') {
							$inner_query_arr['search_term'] = urldecode($arr[1]);
						}if ($arr[0] == 'category_id') {
							$inner_query_arr['category_id'] = $arr[1];
						}if ($arr[0] == 'price_filter') {
							$inner_query_arr['price_filter'] = $arr[1];
						}if ($arr[0] == 'product_id') {
							$inner_query_arr['product_id'] = $arr[1];
						}if ($arr[0] == 'clearance_sale') {
							$inner_query_arr['clearance_sale'] = $arr[1];
							$inner_query_arr['offer_key'] = '@@clearance_sale__search';
						}
						$i++;
					}
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						if (!empty($open_view)) {

							$inner_query_arr['open_view'] = $open_view;

							if ($open_view == "referral_activity") {

								$app_version = $this->model_restapi_service->getCustomerAppVersion((int) $user_id);

								if ($app_version < 134) {

									$inner_query_arr['open_view'] = "open_webview";
									$inner_query_arr["web_view_url"] = $request['link'];
								}
							}
						}

						$rt['data'] = $inner_query_arr;
						$rt['data']['action'] = $inner_query_arr;
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * updalod contacts csv
	 *
	 **/
	public function updalod_contacts_csv() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			//$inputJSON = file_get_contents('php://input');
			//$request = json_decode( $inputJSON, TRUE );

			$rt = array();
			if (isset($_REQUEST['access_token'])) {
				if (isset($_REQUEST['user_id'])) {
					$access_token = $_REQUEST['access_token'];
					$name = $_REQUEST['first_name'];
					$mobile = $_REQUEST['mobile'];
					$user_id = $_REQUEST['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						if (isset($_FILES['csv_file']['tmp_name']) && !empty($_FILES['csv_file']['tmp_name'])) {
							$today = Date('Y-m-d');
							$source = $_FILES['csv_file']['tmp_name'];
							$name = $name . '-' . $mobile . '-' . $user_id . '.csv';
							$dest = DIR_SYSTEM . 'upload/assets/customers_contacts_csvs/' . $today;
							if (!file_exists($dest)) {
								mkdir($dest, 0777, true);
							}
							if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $dest . '/' . $name)) {
								$csv_path = $dest . '/' . $name;
								$this->model_restapi_service->updateCsvPath((int) $user_id, (string) $csv_path);

								$rt['data'] = $dest;
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
							} else {
								$rt['error_code'] = '1003';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'File not uploaded successfully.';
							}

						} else {
							$rt['error_code'] = '1003';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Please select a file.';
						}

					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}
	/* *******
		* Function : askAQuestion
		* Request Parameters : user_id,access_token,customer_name,popup_question,mobile,email,product_id
		* Type : Post
		* Output : {{"status":"1","status_text":"Success","data":{"filers, sorting and other parameters"}}}
		*
	*/

	public function askAQuestion() {
		//echo "<pre>"; print_r($this->request->post); die;
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					if (isset($request['customer_name'])) {$customer_name = $request['customer_name'];} else { $customer_name = '';}
					if (isset($request['popup_question'])) {$popup_question = $request['popup_question'];} else { $popup_question = '';}
					if (isset($request['mobile'])) {$customer_telephone = $request['mobile'];} else { $customer_telephone = '';}
					if (isset($request['email'])) {$email = $request['email'];} else { $email = '';}
					if (isset($request['product_id'])) {$product_id = $request['product_id'];} else { $product_id = '';}
					$user_id = $request['user_id'];
					$access_token = $request['access_token'];

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						$this->load->model('catalog/product');
						$this->load->language('product/product');

						$json = array();

						$data_user_comment = array(
							'customer_name' => $customer_name,
							'product_id' => $product_id,
							'telephone' => $customer_telephone,
							'popup_question' => $popup_question,
							'email' => $email,
						);
						$this->load->model('tool/image');
						$getInformation = $this->model_catalog_product->askAQuestion($data_user_comment);

						$rt['success_code'] = '1001';
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';

					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * get_shipping_estimator
	 * Request Parameters : user_id,access_token,country_id,zone_id
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success","data":{"filers, sorting and other parameters"}}}
	 **/
	public function get_shipping_estimator() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$user_id = $request['user_id'];
					$access_token = $request['access_token'];
					$country_id = $request['country_id'];
					$zone_id = $request['zone_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$this->load->language('checkout/shipping');

						$this->load->model('localisation/country');

						$country_info = $this->model_localisation_country->getCountry($country_id);

						if ($country_info) {
							$country = $country_info['name'];
							$iso_code_2 = $country_info['iso_code_2'];
							$iso_code_3 = $country_info['iso_code_3'];
							$address_format = $country_info['address_format'];
						} else {
							$country = '';
							$iso_code_2 = '';
							$iso_code_3 = '';
							$address_format = '';
						}

						$this->load->model('localisation/zone');

						$zone_info = $this->model_localisation_zone->getZone($zone_id);

						if ($zone_info) {
							$zone = $zone_info['name'];
							$zone_code = $zone_info['code'];
						} else {
							$zone = '';
							$zone_code = '';
						}

						$shipping_address = array(
							'firstname' => '',
							'lastname' => '',
							'company' => '',
							'address_1' => '',
							'address_2' => '',
							'postcode' => '',
							'city' => '',
							'zone_id' => $zone_id,
							'zone' => $zone,
							'zone_code' => $zone_code,
							'country_id' => $country_id,
							'country' => $country,
							'iso_code_2' => $iso_code_2,
							'iso_code_3' => $iso_code_3,
							'address_format' => $address_format,
						);

						$quote_data = array();

						$this->load->model('extension/extension');

						$results = $this->model_extension_extension->getExtensions('shipping');

						foreach ($results as $result) {
							if ($this->config->get($result['code'] . '_status')) {
								if ($result['code'] != "free") {
									//remove free shipping option
									$this->load->model('shipping/' . $result['code']);

									$quote = $this->{'model_shipping_' . $result['code']}->getAppQuote($shipping_address, $user_id);

									if ($quote) {
										$quote_data[$result['code']] = array(
											'title' => $quote['title'],
											'quote' => $quote['quote'],
											'sort_order' => $quote['sort_order'],
											'error' => $quote['error'],
										);
									}
								}
							}
						}

						$sort_order = array();

						foreach ($quote_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}

						array_multisort($sort_order, SORT_ASC, $quote_data);

						$shipping_methods = $quote_data;

						$rt['success_code'] = '1001';
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
						$rt['data'] = $shipping_methods;
					} else {
						$rt['error_code'] = '1002';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * @deprecated
	 * track_sharing
	 * Request Parameters : user_id,access_token,data
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success"}}
	 **/
	public function track_sharing() {
		// We have deprecated this API - nothing to be done
		// as other tools like Firebase etc are doing tracking now.
		// So, just returning a success message without doing anything.
		$rt = array();
		$rt['success_code'] = '1001';
		$rt['status'] = '1';
		$rt['status_text'] = 'Success - Deprecated API (Nothing done)';
		echo json_encode($rt);exit;
	}

	/**
	 * get_related_products
	 * Request Parameters : user_id,access_token,product_id
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success","data":{"Related Products"}}}
	 **/
	public function get_related_product() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token']) || $this->_request_from == 'IOS_APP') {
				if (isset($request['user_id']) || $this->_request_from == 'IOS_APP') {
					$access_token = isset($request['access_token']) ? $request['access_token'] : '';
					$user_id = isset($request['user_id']) ? $request['user_id'] : 0;
					$product_id = $request['product_id'];
					$category_id = $request['category_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($this->_request_from == 'IOS_APP' && $user_id == 0) {
						$check_access_token = 1;
						$postcode = '';
					} else {
						$address = $this->model_restapi_service->getDefaultAddress((int) $user_id);
						$postcode = $address['postcode'];
					}

					if ($check_access_token > 0) {
						if (empty($category_id)) {
							$category = $this->model_restapi_service->getParentCategoryOfProduct((int) $product_id);
							if (!empty($category)) {
								$category_id = $category['category_id'];
							} else {
								$category_id = '';
							}
						}
						// Get category name
						if (isset($category_id) && !empty($category_id)) {
							$category = $this->model_restapi_service->getCategoryName($category_id);
						}
						if (isset($category) && !empty($category)) {
							$category_name = $category['name'];
						} else {
							$category_name = '';
						}
						$filter_data = array();

						/*** show single ***/
						$show_single = $request['show_single'] ?? 0;
						if ((int) $show_single == 1) {
							$custom_store = "single";
						} else {
							$custom_store = "wholesale";
						}
						$filter_data['custom_store'] = $custom_store;

						if (!empty($this->_franchise_id)) {
							$filter_data['franchise_id'] = $this->_franchise_id;
						}
						$filter_data['page'] = 1;
						$price = $this->model_restapi_service->getPriceOfProduct((int) $product_id);

						// Get style filter of product
						$filters = $this->model_restapi_service->CheckProductHaveStyleFilter((int) $product_id);
						if (!empty($filters['filter_name'])) {
							$filter_data['filter_name'] = $filters['filter_name'] . ' ' . $category_name;
							$filter_data['filter_seller_id'] = $filters['seller_id'];
							$filter_data['filter_category_id'] = $category_id;
							$filter_data['user_id'] = $user_id;
							if (!empty($postcode)) {
								$filter['post_code'] = $postcode;
							}

							$results = $this->model_restapi_service->getReletedProduct($filter_data);
							if ($results['total'] == 0) {
								$filters_data = array();
								if (!empty($price['price'])) {
									$releted_price = 0.30 * $price['price'];
									$min_price = $price['price'] - $releted_price;
									$max_price = $price['price'] + $releted_price;
									$filters_data['price_filter'] = $min_price . '-' . $max_price;
									$filters_data['filter_name'] = $category_name;
									$filters_data['filter_category_id'] = $category_id;
									$filters_data['user_id'] = $user_id;
									if (!empty($postcode)) {
										$filter['post_code'] = $postcode;
									}

									$results = $this->model_restapi_service->getReletedProduct($filters_data);
								}
							}
						} else {
							$seller_id = $this->model_restapi_service->getProductSeller((int) $product_id);
							if (!empty($price['price']) && isset($seller_id['seller_id'])) {
								$releted_price = 0.30 * $price['price'];
								$min_price = $price['price'] - $releted_price;
								$max_price = $price['price'] + $releted_price;
								$filter_data['price_filter'] = $min_price . '-' . $max_price;
								$filter_data['filter_name'] = $category_name;
								$filter_data['filter_category_id'] = $category_id;
								$filter_data['filter_seller_id'] = $seller_id['seller_id'];
								$filter_data['user_id'] = $user_id;
								if (!empty($postcode)) {
									$filter['post_code'] = $postcode;
								}

								$results = $this->model_restapi_service->getReletedProduct($filter_data);
							}
						}

						if (isset($results['data']) && !empty($results['data'])) {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['total_products'] = $results['total'];
							$rt['data']['products'] = $results['data'];
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Products Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	public function track_product_seen() {

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$this->load->model('restapi/service');
					$user_id = $request['user_id'];
					$access_token = $request['access_token'];
					$product_ids = $request['product_ids'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					$check_access_token = 1;
					if ($check_access_token > 0) {
						$this->load->model('catalog/product');

						if (is_array($product_ids) && count($product_ids) > 0) {

							foreach ($product_ids as $product_id) {
								$this->model_catalog_product->updateViewed($product_id);
							}
						}

						$rt['success_code'] = '1001';
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';

					} else {
						$rt['error_code'] = '1002';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Authantication failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * get_customer_preferences
	 * Request Parameters : user_id,access_token
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success","data":{"Customer Preferences"}}}
	 **/
	public function get_customer_preferences() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						$master_preferences = $this->model_restapi_service->getMenu();

						// Get Preferences from customer_preferences table
						$customer_preferences = $this->model_restapi_service->getCustomerSelectedPreferences((int) $user_id);

						if ($this->currency->getCode() == 'INR') {
							$price_values = $this->currency->currencies['INR']['value'];
							$currency = 'INR';
						} else {
							$price_values = $this->currency->currencies['USD']['value'];
							$currency = 'USD';
						}
						if (!empty($master_preferences)) {
							$this->load->language('account/get_customer_preferences');
							$preference_text = $this->language->get('preference_text');
							$i = 0;
							foreach ($master_preferences as $master_pref) {
								$master_cat_id = $master_pref['category_id'];
								$master_cat_image = $this->model_restapi_service->getCategoryImages((int) $master_cat_id);
								if (!empty($master_cat_image[0]['db_image'])) {
									$thumb_image = $this->model_tool_image->resize($master_cat_image[0]['db_image'], 250, 375);
								} else {
									$thumb_image = $this->model_tool_image->resize('no_image.png', 250, 375);
								}

								$data[$i]['category_id'] = $master_cat_id;
								$data[$i]['category_name'] = html_entity_decode($master_pref['name']);
								$data[$i]['category_image'] = $thumb_image;

								if (!empty($master_pref['price_range'])) {
									$price_range = explode('-', $master_pref['price_range']);
									$cat_min_price = (int) ($price_range[0] * $price_values);
									$cat_max_price = ceil($price_range[1] * $price_values);
								} else {
									$this->load->model('catalog/category');
									/**
									 * Commented below function as for now we are not using it in app and getPriceByCategory is taking lot of time
									 * static min max prices are given
									 * Anurag Jain, 2 July 2019
									 */
									$cat_min_price = 0 * $price_values; //$price['minimum_price'];
									$cat_max_price = 5000 * $price_values; //$price['maximum_price'];
								}
								if ($cat_min_price === $cat_max_price) {
									$cat_max_price += 1;
								}

								$data[$i]['currency'] = $currency;
								$data[$i]['min_price'] = $cat_min_price;
								$data[$i]['max_price'] = $cat_max_price;
								$data[$i]['filters'] = array();

								if ($data[$i]['min_price'] == 0) {
									$data[$i]['min_price'] = 1;
								}
								if ($data[$i]['max_price'] == 0) {
									$data[$i]['max_price'] = 10;
								}
								if ($data[$i]['min_price'] == $data[$i]['max_price']) {
									$data[$i]['max_price'] = $data[$i]['max_price'] + 1;
								}
								if (isset($customer_preferences) && !empty($customer_preferences)) {
									foreach ($customer_preferences as $cust_pref) {
										$cust_cat_id = $cust_pref['category_id'];
										if ($cust_cat_id == $master_cat_id) {
											$data[$i]['is_selected'] = "1";
											$data[$i]['cust_min_price'] = $cust_pref['min_price'] * $price_values;
											$data[$i]['cust_max_price'] = $cust_pref['max_price'] * $price_values;
											break;
										} else {
											$data[$i]['is_selected'] = 0;
											$data[$i]['cust_min_price'] = "";
											$data[$i]['cust_max_price'] = "";
										}
									}
								} else {
									$data[$i]['is_selected'] = 0;
									$data[$i]['cust_min_price'] = "";
									$data[$i]['cust_max_price'] = "";
								}
								$i++;
							}
						}
						if (isset($data) && !empty($data)) {
							if (!empty($this->_franchise_id)) {
								if ($this->currency->getCode() == 'INR') {
									$franchise_currency = "INR";
								} else {
									$franchise_currency = "USD";
								}
								$franchise_category[] = array(
									"category_id" => "700000",
									"category_name" => "Franchise",
									"category_image" => "",
									"currency" => $franchise_currency,
									"min_price" => "0",
									"max_price" => "1",
									"filters" => array(),
									"is_selected" => '1',
									"cust_min_price" => "",
									"cust_max_price" => "",
								);

								$data = array_merge($franchise_category, $data);
							}

							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['preference_text'] = $preference_text;
							$rt['data']['preferences'] = $data;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Preferences Not Found.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * update_customer_preferences
	 * Request Parameters : user_id,access_token, Preferences
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success","data":{"Customer Preferences"}}}
	 **/
	public function update_customer_preferences() {
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						// Update Preferences
						$preferences = $request['preferences'];
						$update_prefs_result = $this->model_restapi_service->updatePreferencesFromApp((int) $user_id, $preferences);
						$rt['success_code'] = '1001';
						$rt['status'] = '1';
						$rt['status_text'] = 'Success';
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * getCountryList
	 **/
	/*public function get_countries(){

	}*/
	/**
	 * uploadBankSlip
	 * Request Parameters : user_id,access_token, order_id
	 * Type : Post
	 * Output : {{"status":"1","status_text":"Success","data":{"image path"}}}
	 **/
	public function uploadBankSlip() {
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$rt = array();
			if (isset($_REQUEST['access_token'])) {
				if (isset($_REQUEST['user_id'])) {
					$access_token = $_REQUEST['access_token'];
					$order_id = $_REQUEST['order_id'];
					$user_id = $_REQUEST['user_id'];
					$amount = $_REQUEST['amount'] ?? '0.00';
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {

						if (isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
							$source = $_FILES['image']['tmp_name'];
							$t = time();
							$mask = $order_id . '_*.*';

							$name = $order_id . "_" . $t . '.jpg';
							$dest = DIR_IMAGE . 'bank_slips';
							if (!file_exists($dest)) {
								mkdir($dest, 0777, true);
							}
							if (glob($dest . '/' . $mask)) {
								array_map('unlink', glob($dest . '/' . $mask));
							}
							if (move_uploaded_file($_FILES['image']['tmp_name'], $dest . '/' . $name)) {
								$img_path = 'bank_slips' . '/' . $name;
								$this->model_restapi_service->updateImgPath((int) $user_id, (int) $order_id, $img_path);
								$this->model_restapi_service->addTentativeAdvanceFromBankSlip((int) $user_id, (int) $order_id, $img_path, (float) $amount);
								$rt['data'] = $this->model_tool_image->resize($img_path, 375, 250);
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
							} else {
								$rt['error_code'] = '1003';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'File not uploaded successfully.';
							}

						} else {
							$rt['error_code'] = '1003';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Please select a file.';
						}

					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;

	}

	/*
			 * uploadBankDetails
			 * Request Param: user_id,access_token,account_holder_name,account_number,ifsc_code,image
			 * Type: Post - form data
			 * Comment: use for uploading bank details of customer
			 * Output: Success or Failed
			 * @author Devendra Dhayal
		     * @date 26-09-2017
	*/
	public function uploadBankDetails() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if (isset($_REQUEST['access_token'])) {
				$access_token = $_REQUEST['access_token'];
				$user_id = $_REQUEST['user_id'];
				$this->load->model('restapi/service');
				$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
				if ($check_access_token > 0) {
					$account_holder_name = $_REQUEST['account_holder_name'];
					$data['bank_ac_holder_name'] = $account_holder_name;
					if (empty($account_holder_name) || !isset($_REQUEST['account_holder_name'])) {
						$rt['status'] = 0;
						$rt['status_text'] = "failed";
						$rt['message'] = 'Account holder name is requried';
						echo json_encode($rt);
						exit;
					}
					$account_number = $_REQUEST['account_number'];
					$data['bank_ac_number'] = $account_number;
					if (empty($account_number) || !isset($_REQUEST['account_number'])) {
						$rt['status'] = 0;
						$rt['status_text'] = "failed";
						$rt['message'] = 'Account number is requried';
						echo json_encode($rt);
						exit;
					}
					$ifsc_code = $_REQUEST['ifsc_code'];
					$data['ifsc_code'] = $ifsc_code;
					if (empty($ifsc_code) || !isset($_REQUEST['ifsc_code'])) {
						$rt['status'] = 0;
						$rt['status_text'] = "failed";
						$rt['message'] = 'Ifsc code is requried';
						echo json_encode($rt);
						exit;
					}

					if (isset($_REQUEST['customer_vpa'])) {
						$customer_vpa = isset($_REQUEST['customer_vpa']) ? $_REQUEST['customer_vpa'] : "";
						$data['upi_vpa'] = $customer_vpa;
						if ((utf8_strlen(trim($customer_vpa)) > 0)) {
							$upi_vpa = trim($customer_vpa);
							(int) $length = utf8_strlen($upi_vpa);
							if ((int) $length > 0) {
								$pos = strpos($upi_vpa, '@');
								$pos_next = strpos($upi_vpa, '@', $pos + 1);
								if ($pos == 0 || $pos == ($length - 1) || $pos === FALSE || $pos_next > 0) {
									$rt['status'] = 0;
									$rt['status_text'] = "failed";
									$rt['message'] = 'Invalid UPI Vpa';
									echo json_encode($rt);
									exit;
								}
							}
						}
					}

					$json = validateBankIFSC($ifsc_code, 'return');
					if ($json == '"Not Found"') {
						$rt['status'] = 0;
						$rt['status_text'] = "failed";
						$rt['message'] = 'Ifsc code is not valid';
						echo json_encode($rt);
						exit;
					}
					if (isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
						$name = $user_id . '.jpg';
						$directory = 'bank_details';
						$file_name_with_full_path = $_FILES['image']['tmp_name'];
						if (function_exists('curl_file_create')) {
							// php 5.5+
							$cFile = curl_file_create($file_name_with_full_path);
						} else {
							//
							$cFile = '@' . realpath($file_name_with_full_path);
						}
						$post = array('file' => $cFile);
						$ch = curl_init();
						$target_url = STATIC_CONTENT_URL_SSL . 'fileupload.php?directory=' . $directory . '&filename=' . $name;
						curl_setopt($ch, CURLOPT_URL, $target_url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						$upload_result = curl_exec($ch);
						curl_close($ch);
						if (FALSE === $upload_result) {
							$rt['error_code'] = '1004';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'File not uploaded successfully.';
							echo json_encode($rt);
							exit;
						}
					}

					$this->load->model('account/customer');
					if ($this->model_account_customer->checkCustomerBankDetails($data, $user_id)) {
						$rt['status'] = 0;
						$rt['status_text'] = "failed";
						$rt['message'] = 'Bank Details allready exist';
						echo json_encode($rt);
						exit;
					}
					$data['bank_details_verified'] = "0";
					$this->model_account_customer->updateCustomerDetails($data, $user_id);
					$msg = $this->_sendOTPOnUpdateBankDetails($user_id);
					$rt['status'] = 1;
					$rt['status_text'] = "Success";
					$rt['message'] = $msg;
				} else {
					$rt['error_code'] = '1003';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'Invalid Access Token.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/*
			 * verifyBankDetails
			 * Request Param: user_id,access_token,otp
			 * Type: Post
			 * Comment: Confirm the otp and update the bank_details_verified flag for customer
			 * Output: Success or Failed
		     * @author Devendra Dhayal
		     * @date 26-09-2017
	*/
	public function verifyBankDetails() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			if (isset($request['access_token'])) {
				$access_token = $request['access_token'];
				$user_id = $request['user_id'];
				$this->load->model('restapi/service');
				$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
				if ($check_access_token > 0) {
					if (isset($request['otp'])) {
						$otp = $request['otp'];
						$status = $this->model_restapi_service->verifyBankDetailOtp((int) $user_id, $otp);
						if ($status) {
							$data['bank_details_verified'] = "1";
							$data['otp'] = "";
							$this->load->model('account/customer');
							$this->model_account_customer->updateCustomerDetails($data, (int) $user_id);
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = 'Bank Details successfully verified';
						} else {
							$rt['error_code'] = '1005';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Invalid OTP.Please try again.';
						}
					} else {
						$rt['error_code'] = '1004';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'http request does not have otp.';
					}
				} else {
					$rt['error_code'] = '1003';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'Invalid Access Token.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		} else {
			$rt['error_code'] = '1001';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'HTTP Request does not have POST data.';
		}
		echo json_encode($rt);exit;
	}

	/*
			 * verifyBankDetails
			 * @Request Param: user_id,access_token
			 * @Type: Post
			 * @Comment: send the otp for bank details, this method is used to re-verify bank details in app.
			 * @Output: Success or Failed
		     * @author Devendra Dhayal
		     * @date 26-09-2017
	*/
	public function sendBankDetailsOTP() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			if (isset($request['access_token'])) {
				$access_token = $request['access_token'];
				$user_id = $request['user_id'];
				$this->load->model('restapi/service');
				$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
				if ($check_access_token > 0) {
					$msg = $this->_sendOTPOnUpdateBankDetails($user_id);
					$rt['status'] = 1;
					$rt['status_text'] = 'Success';
					$rt['message'] = $msg;
				} else {
					$rt['error_code'] = '1003';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'Invalid Access Token.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		} else {
			$rt['error_code'] = '1001';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'HTTP Request does not have POST data.';
		}
		echo json_encode($rt);exit;
	}

	/*
			 * _sendOTPOnUpdateBankDetails
			 * @Param: user_id
			 * @Comment: Private method used internally to send otp to customers for verifying their bank details.
			 * @Output: otp msg
		     * @author Devendra Dhayal
		     * @date 26-09-2017
	*/
	private function _sendOTPOnUpdateBankDetails($user_id) {
		$this->load->model('restapi/service');
		$this->load->language('account/sms_templates');
		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomer($user_id);
		$telephone = $customer['telephone'];
		$mobile_country_code = $customer['mobile_country_code'];
		$email = $customer['email'];
		$customer_name = $customer['firstname'];
		$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
		$this->model_restapi_service->updateBankDetailOtp((int) $user_id, $otp);
		$message = $this->language->get('on_bank_detail_verify');
		$message = sprintf($message, $otp);

		if (!empty($telephone)) {
			if (SITE_ENVIRONMENT == 'Production') {
				$sms = new SMS($message, $mobile_country_code . $telephone);
				$sms->sendOTPMessage();
			}

			$msg = "OTP has been successfully sent on mobile:" . $mobile_country_code . $telephone;
		} else {
			$mail = new PHPMailer();
			$mail->isSMTP();
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPDebug = 0;
			$mail->Debugoutput = 'html';
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');
			$mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
			$mail->addAddress($email, $customer_name);
			$mail->Subject = html_entity_decode("OTP", ENT_QUOTES, 'UTF-8');
			$mail->msgHTML($message);
			$mail->send();
			$msg = "OTP has been successfully sent on email:" . $email;
		}
		return $msg;
	}

	public function get_offers($return = false, $data = array()) {

		$offers = array();

		$this->load->model('tool/image');
		$this->load->model('restapi/service');

		$banners = $this->model_restapi_service->getHomeBanners();

		if (!empty($banners)) {

			foreach ($banners as $key => $value) {

				$data['text'] = $value['title'];
				$data['url'] = "";
				$data['key'] = "";

				if ($value['type'] == "link") {

					$data['url'] = $value['link'];

					if (strtolower($value['title']) == 'credit application') {
						$data['url'] .= '&customer_id=' . $data['customer_id'] . '&token=' . $data['token'];
					}

				} elseif ($value['type'] == "others") {
					$data['key'] = $value['link'];
				} else {
					$data['key'] = $value['link'] . $this->_banner_splitter . $value['type'];
				}

				$data['image'] = $this->model_tool_image->resize($value['image'], 720, 274);

				if (isset($data['action'])) {
					unset($data['action']);
				}

				if (strtolower($data['text']) == "create website") {
					$data['action'] = array("open_view" => "fashcart_website");
				}

				if (strtolower($data['text']) == strtolower("End of Season Sale")) {

					$data['action'] = array("search_term" => WSB_PURCHASE_INVENTORY_SEARCH_TERM, "offer_key" => $data['key']);
					$data['key'] = '';
				}

				if ($value['type'] == "others") {
					$links = explode("&amp;", $value['link']);
					foreach ($links as $link) {
						$tab = explode("=", $link);
						if (isset($tab[1])) {
							$data['action'][$tab[0]] = $tab[1];
						}
					}
				}

				$offers[] = $data;
			}
		}

		if ($return) {
			return $offers;
		} else {
			echo json_encode($offers);
		}
	}

	public function get_locale_data() {
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$countries = $this->model_localisation_country->getCountries();
		$zones = $this->model_localisation_zone->getAllZones();

		$response = array(
			'country_list' => $countries,
			'stateCountryList' => $zones,
			'support_telephone' => '+91-8696491521',
		);
		echo json_encode($response);

	}

	// Customer Recommended Items
	// @author Garvit
	public function youMayLike() {
		$this->load->model('restapi/service');
		$this->load->model('tool/image');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);

					if ($check_access_token > 0) {
						if (!empty($request['user_id'])) {

							$customer_id = $request['user_id'];
							$this->load->model('catalog/product');
							$request['call_from'] = 'app';
							//Here, we Get Products Which are Releted to Customer Preferences, Order, WishList of customer
							//$result = $this->model_catalog_product->getProductsAccordingToCustomerPreferences($customer_id, 'you_may_like');
							$result = $this->model_catalog_product->getLikePreferenceProducts($request, 'you_may_like');

							$unique_result = array_map("unserialize", array_unique(array_map("serialize", $result)));
							$final_result = array();

							foreach ($unique_result as $val) {
								if (isset($val['stock_status']) && $val['stock_status'] == 'out of stock') {
									// Don't add the out of stock product in final result
								} else {
									$final_result[] = $val;
								}
							}

							$rt['success_code'] = '1001';
							$rt['status'] = '1';
							$rt['message'] = 'No more product suggestions matching your profile. Please browse more products and come to this space.';
							$rt['status_text'] = 'Success';
							$rt['result'] = $final_result;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Customer id Empty.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);
		exit;
	}

	// Other Customers Recommended Items
	// @author Garvit
	public function whatOthersLike() {
		$this->load->model('restapi/service');
		$this->load->model('tool/image');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);

					if ($check_access_token > 0) {
						if (!empty($request['user_id'])) {

							$customer_id = $request['user_id'];
							$this->load->model('catalog/product');
							$request['call_from'] = 'app';

							//Here, we Get Products Which are Releted to Others Preferences Like Customer Preferences, Order, WishList of customer
							//$result = $this->model_catalog_product->getProductsAccordingToCustomerPreferences($customer_id, 'what_others_like');
							$result = $this->model_catalog_product->getLikePreferenceProducts($request, 'what_others_like');
							$unique_result = array_map("unserialize", array_unique(array_map("serialize", $result)));
							$final_result = array();

							foreach ($unique_result as $val) {
								$final_result[] = $val;
							}

							$rt['success_code'] = '1001';
							$rt['status'] = '1';
							$rt['message'] = 'No more product suggestions matching your profile. Please browse more products and come to this space.';
							$rt['status_text'] = 'Success';
							$rt['result'] = $final_result;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Customer id Empty.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);
		exit;
	}

	// Customer dislike Items
	// @author Garvit
	public function dislike() {
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if (!empty($request['user_id'])) {
							$customer_id = $request['user_id'];
							$product_ids = $request['product_id'];

							//Here, we SET Customer Dislike Product
							$this->model_restapi_service->setCustomerDislikeProduct((int) $customer_id, $product_ids);

							$rt['success_code'] = '1001';
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Customer id Empty.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);
		exit;
	}

	/**
	 * Used to get x products to cache in APP for each category
	 */

	public function category_products_to_cache() {

		/* if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {

			            $inputJSON = file_get_contents('php://input');
			            $request = json_decode($inputJSON, TRUE);

			            $this->load->model('catalog/category');

			            $categories = $this->model_catalog_category->getAllCategoryIds();

			            $response = array();
			            $i = 0;
			            foreach($categories as $category) {
			                $category_id = $category['category_id'];
			                $request['category_id'] = $category_id;
			                $response[$i]['id'] = $category_id;
			                $response[$i]['name'] = $category['name'];
			                $response[$i]['data'] = $this->get_products_by_category($request);
			                $i++;

			            }

			            echo json_encode($response); exit;

			        }
			        $response[] = ['id' => 61,
			                     'data' => array('status' => 1, "data" => [])
			            ];
		*/
		echo '[{"id":61,"data":{"status":1,"data”:{}}}]';exit;
	}

	public function get_products_by_ids() {
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			$product_data = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if (isset($request['product_id']) && !empty($request['product_id'])) {

							$arr_product_id = explode(",", $request['product_id']);
							$data['user_id'] = $user_id;
							$call_from = 'app';

							$solr = new SolrProduct($this);
							$solr->getProductDetails($arr_product_id, $data, $call_from, $product_data);

						}

						if (!empty($product_data)) {
							$product_data['success_code'] = '1001';
							$product_data['status'] = '1';
							$product_data['status_text'] = 'Success';
							$rt = $product_data;
						}
					} else {
						$rt['error_code'] = '1002';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Token authantication failed.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}

		echo json_encode($rt);
		exit;
	}
	/**
	 * customer feedback
	 * @author Garvit
	 **/
	public function customerFeedback() {
		$this->load->model('tool/image');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$this->load->model('restapi/service');
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if (!empty($request['user_id'])) {
							$customer_id = $request['user_id'];
							$feedback_text = $request['feedback_text'];

							//Here, we Set customer feedback for android app
							$this->model_restapi_service->setCustomerFeedback((int) $customer_id, $feedback_text);

							$rt['success_code'] = '1001';
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = 'Thanks for your feedback.';
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Customer id Empty.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);
		exit;
	}

	public function requestExclusiveCollection() {

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$rt = array();
			if (isset($_REQUEST['access_token'])) {
				if (isset($_REQUEST['user_id'])) {
					$access_token = $_REQUEST['access_token'];
					$user_id = $_REQUEST['user_id'];
					$this->load->model('restapi/service');
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if ((isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) || (isset($_FILES['shop_image']['tmp_name']) && !empty($_FILES['shop_image']['tmp_name']))) {
							$t = time();
							$mask = $user_id . '_*.*';
							if (!empty($_FILES['image']['tmp_name'])) {
								$image_name_1 = $user_id . "_1_" . $t . '.jpg';
							} else {
								$image_name_1 = '';
							}
							if (!empty($_FILES['shop_image']['tmp_name'])) {
								$image_name_2 = $user_id . "_2_" . $t . '.jpg';
							} else {
								$image_name_2 = '';
							}

							$dest = DIR_IMAGE . 'request_for_exclusive';

							if (!file_exists($dest)) {
								mkdir($dest, 0777, true);
							}
							if (glob($dest . '/' . $mask)) {
								array_map('unlink', glob($dest . '/' . $mask));
							}
							$check = 0;
							if (!empty($image_name_1) && !empty($image_name_2)) {
								$check = 1;
								move_uploaded_file($_FILES['image']['tmp_name'], $dest . '/' . $image_name_1);
								move_uploaded_file($_FILES['shop_image']['tmp_name'], $dest . '/' . $image_name_2);
							} elseif (!empty($image_name_1) && empty($image_name_2)) {
								$check = 1;
								move_uploaded_file($_FILES['image']['tmp_name'], $dest . '/' . $image_name_1);
							} elseif (empty($image_name_1) && !empty($image_name_2)) {
								$check = 1;
								move_uploaded_file($_FILES['shop_image']['tmp_name'], $dest . '/' . $image_name_2);
							}
							if ($check) {
								$this->model_restapi_service->requestExclusiveCollection((int) $user_id, $image_name_1, $image_name_2);
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = "Thank You!\nWe shall update you soon!";
							} else {
								$rt['error_code'] = '1003';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'File not uploaded successfully.';
							}
						} else {
							$rt['error_code'] = '1003';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Please select a file.';
						}

					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	private function logForTest($method, $request, $response) {

		// Uncomment the below lines to enable logging.
		// To be used only when required to debug.
		// Once debugging is done, dont forget to comment out this portion again
		/*
	        $txt = "\n*********************************************************\n";
		    $myfile = fopen(DIR_LOGS."log_rest_api.txt", "a");
	        $txt .= date("Y-m-d H:i:s").' '.$method;
	        $txt .= "\n";
	        $txt .= json_encode($request);

	        $txt .= "\n-----------Headers--------------:\n";
	        $txt .= json_encode($this->restapi->getRequestHeader());

	        $txt .= "\n-----------Response--------------:\n";
	        $txt .= json_encode($response);
	        $txt .= "\n*********************************************************\n";
	        $txt .= "\n\n";
	        fwrite($myfile, $txt);
	        fclose($myfile);
*/

		return true;
	}

	/**
	 * get_latest_product
	 * Get latest product according to customer preference
	 * @param 	access_token 	POST
	 * @param 	user_id 		POST
	 * @param 	category_ids 	POST
	 * @return 	latest_product 	JSON
	 * @author 	GARVIT JOSHI
	 */
	public function get_latest_product() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token']) || $this->_request_from == 'IOS_APP') {
				if (isset($request['user_id']) || $this->_request_from == 'IOS_APP') {

					$access_token = isset($request['access_token']) ? $request['access_token'] : '';
					$user_id = isset($request['user_id']) ? $request['user_id'] : 0;

					$this->load->model('restapi/service');
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);

					if ($this->_request_from == 'IOS_APP' && $user_id == 0) {
						$check_access_token = 1;
					}

					if ($check_access_token > 0) {
						if (!empty($request['user_id']) || $this->_request_from == 'IOS_APP') {
							$customer_id = $user_id;

							if (isset($request['category_ids'])) {
								$category_ids = $request['category_ids'];
							} else {
								$category_ids = '';
							}

							if (isset($request['view_more'])) {
								$view_more = $request['view_more'];
							} else {
								$view_more = 0;
							}

							if (!empty($request['page'])) {
								$page = $request['page'];
							} else {
								$page = 1;
							}

							$filter_data = array();

							if (!empty($this->_franchise_id)) {
								$filter_data['franchise_id'] = $this->_franchise_id;
							}

							/*** show single ***/
							$show_single = $request['show_single'] ?? 0;
							if ((int) $show_single == 1) {
								$custom_store = "single";
							} else {
								$custom_store = "wholesale";
							}
							$filter_data['custom_store'] = $custom_store;

							$flag = null;
							$group_limit = 2;
							$result = $this->model_restapi_service->getLatestProducts($category_ids, $user_id, $view_more, $page, $group_limit, $filter_data, $flag, $request);
							if (!empty($request['app_version_code']) && ((int) $request['app_version_code'] > 70 || ($this->_request_from == 'IOS_APP' && (int) $request['app_version_code'] > 1))) {
								if (isset($result['filter_facets']['rating'])) {
									$rating_arr_old = $result['filter_facets']['rating'];
									$rating_arr_new = array();
									if (isset($rating_arr_old['0.0'])) {unset($rating_arr_old['0.0']);}
									if (isset($rating_arr_old['3.0'])) {$rating_arr_new['3.0'] = array('label' => 'Average', 'count' => $rating_arr_old['3.0']);}
									if (isset($rating_arr_old['4.0'])) {$rating_arr_new['4.0'] = array('label' => 'Good', 'count' => $rating_arr_old['4.0']);}
									if (isset($rating_arr_old['5.0'])) {$rating_arr_new['5.0'] = array('label' => 'Excellent', 'count' => $rating_arr_old['5.0']);}

									$result['filter_facets']['rating'] = $rating_arr_new;
								}
							}
							$rt['success_code'] = '1001';
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['result'] = $result;
							if (isset($request['view_more']) && $request['view_more'] == 1) {
								$rt['total_page'] = ceil($result['total'] / 10);
								$rt['next_page'] = ($page < $rt['total_page']) ? $page + 1 : 0;
							}
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Customer_id is Empty.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);exit;
	}

	/**
	 * save blacklist data to leads in CRM
	 * @param array type (customer_id, mobile, app, name, other_app_name_installed, other_app_package_installed)
	 * @author manish
	 * @date 24-06-17
	 */
	public function blacklist() {

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = '';

			if (isset($request['access_token'])) {

				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$this->load->model('restapi/service');
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);

					if ($check_access_token > 0) {

						$this->load->model('lead/lead');
						$rt = $this->model_lead_lead->leadUsingCompetitors($request);

					} else {

						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}

			echo json_encode($rt);exit;
		}
	}

	/**
	 * customerDataToTrack
	 * @author Garvit
	 **/
	public function customerDataToTrack() {
		$this->load->model('tool/image');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$rt = array();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {

					$access_token = $request['access_token'];
					$user_id = $request['user_id'];

					$this->load->model('restapi/service');
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if (!empty($request['user_id'])) {
							$customer_id = $request['user_id'];
							//Here, we Set customer feedback for android app
							$result = $this->model_restapi_service->customerDataToTrack((int) $customer_id);
							$rt['success_code'] = '1001';
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['result'] = $result;
							$rt['result']['customer_id'] = $customer_id;
						} else {
							$rt['error_code'] = '1001';
							$rt['status'] = '0';
							$rt['status_text'] = 'failed';
							$rt['message'] = 'Customer id Empty.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}

				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
		}
		echo json_encode($rt);
		exit;
	}

	public function getTrackingURL($courier_partner) {
		$url = "";
		$sql = "SELECT tracking_url FROM " . DB_PREFIX . "courier_partners WHERE courier_name = '" . $courier_partner . "'";
		$query = $this->db->query($sql);
		if ($query->num_rows) {
			foreach ($query->row as $field => $value) {
				$url = $value;
			}
		}
		return $url;
	}

	public function postYourRequirement() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$allowedImages = array('image/jpg', 'image/jpeg', 'image/png');

			$access_token = isset($this->request->post['access_token']) ? $this->request->post['access_token'] : '';
			$user_id = isset($this->request->post['user_id']) ? $this->request->post['user_id'] : '';
			$category_name = isset($this->request->post['category_name']) ? $this->request->post['category_name'] : '';
			$quantity = isset($this->request->post['quantity']) ? $this->request->post['quantity'] : '';
			$required_days = isset($this->request->post['required_days']) ? $this->request->post['required_days'] : '';
			$price_from = isset($this->request->post['price_from']) ? $this->request->post['price_from'] : '';
			$price_to = isset($this->request->post['price_to']) ? $this->request->post['price_to'] : '';
			$description = isset($this->request->post['description']) ? $this->request->post['description'] : '';

			$all_images_uploaded = 'true';

			if (!empty($access_token)) {
				if (empty($user_id) || empty($category_name) || empty($quantity) || empty($required_days) || empty($price_from) || empty($price_to) || empty($description) || empty($_FILES)) {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'Data not proper';

					echo json_encode($rt);
					exit;
				}
				$this->load->model('restapi/service');
				$this->load->model('restapi/wsbcartservice');
				$this->load->model('account/customer');
				$this->load->model('account/return');
				$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
				if ($check_access_token > 0) {
					$customer_data = $this->model_account_customer->getCustomer($user_id);
					$data['quantity'] = $quantity;
					$data['user_id'] = $user_id;
					$data['category_name'] = $category_name;
					$data['required_days'] = $required_days;
					$data['price_to'] = $price_to;
					$data['price_from'] = $price_from;
					$data['description'] = $description;
					$data['name'] = $customer_data['firstname'];
					$result = $this->model_restapi_wsbcartservice->insertRequirement($data);

					if (isset($result['success']) && !empty($result['success'])) {
						$mail_data['firstname'] = $customer_data['firstname'];
						$mail_data['telephone'] = $customer_data['telephone'];
						$mail_data['email'] = $customer_data['email'];
						$mail_data['category_name'] = $category_name;
						$mail_data['quantity'] = $quantity;
						$mail_data['required_days'] = $required_days;
						$mail_data['price_from'] = $price_from;
						$mail_data['price_to'] = $price_to;
						$mail_data['description'] = $description;
						$mail_data['image_name'] = $result['images_data'];
						$body = $this->model_restapi_wsbcartservice->getmailBody($mail_data);
						$subject = 'Product Requirement query from ' . $category_name;
						$email['buyer_email'] = EMAIL_IDS['prabhav']['email_id'];
						$email['buyer_name'] = EMAIL_IDS['prabhav']['name'];
						$email['post_your_req'] = 1;
						$this->model_account_return->sendMailFromApi($email, $subject, $body);

						$rt['status'] = '1';
						$rt['status_text'] = 'success';
						$rt['message'] = 'Thank You! Our representative will get to you shortly';
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Data insert unsuccessfully';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have access token.';
				}
			} else {
				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request has empty access token.';
			}
			echo json_encode($rt);
			exit;
		}
	}

	private function __getFranchiseDetailsFromHeader() {
		$headers = getallheaders();
		if (isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int) $headers['crm_role_id'] == (int) CRM_FRANCHISE_ROLE_ID) {
			$this->load->model('restapi/service');
			$franchise_id = $this->model_restapi_service->getCustomerIdUsingCRMUserId($headers['crm_user_id']);
			if ($franchise_id != 0) {
				$this->_franchise_id = $franchise_id;
				if (isset($headers['franchise_margin'])) {
					$this->_franchise_margin = $headers['franchise_margin'];
				}
			}
		}

		if (isset($headers['REQUEST_BY'])) {
			$this->_request_from = $headers['REQUEST_BY'];
		} else {
			$this->_request_from = '';
		}
	}

	public function get_ssl_status($intra_call = false) {
		$ssl = true;

		if ($ssl) {
			$rt['status'] = '1';
			$rt['ssl_status'] = '1';
			$rt['status_text'] = 'success';
			$rt['message'] = 'SSL is enabled.';
		} else {
			$rt['status'] = '1';
			$rt['ssl_status'] = '0';
			$rt['status_text'] = 'success';
			$rt['message'] = 'SSL is disabled.';
		}

		if ($intra_call) {
			return $ssl;
		} else {
			echo json_encode($rt);
			exit;
		}
		return false;
	}

	/* *******
		 * Function : set_product_reviews
		 * Request Parameters : user_id,access_token
		 * Type : Post
		 * Output : {{"status":"1","status_text":"Success"}}}
		 *
	*/

	public function set_order_product_reviews() {

		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						if (!empty($request['order_product_id']) && isset($request['review'])) {
							$order_product_id = (int) $request['order_product_id'];
							$raw_review = $request['review'];
							$review = "";
							if ($raw_review === '1') {$review = "like";}
							if ($raw_review === '0') {$review = "dislike";}
							if ($this->model_restapi_service->setOrderProductReviews($order_product_id, $review)) {
								$rt['status'] = '1';
								$rt['status_text'] = 'success';
								$rt['message'] = 'Product review successful.';
							} else {
								$rt['error_code'] = '1005';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Product review failed.';
							}
						} else {
							$rt['error_code'] = '1004';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Invalid request data.';
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'Request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'Request does not have access token.';
			}
			echo json_encode($rt);
			exit;
		}
	}

	private function manage_error_reporting() {
		$headers = getallheaders();
		if (!empty($headers['disable_error_reporting'])) {
			error_reporting(0);
		}
		return true;
	}

	private function setFranchiseSubCategories(&$menus) {

		foreach ($menus as $menu_key => $menu_data) {
			if (!empty($menu_data['children'])) {
				$menu_categories = array();
				foreach ($menu_data['children'] as $child_key => $child_data) {
					if ($child_data['link_type'] == "category") {
						// category
						$menu_categories[] = $child_data['value'] . "_F";
					} else if ($child_data['link_type'] == "others") {
						// custom_link
						if (!empty($child_data['children'])) {
							$sub_menu_categories = $this->getAllChildCategoryForCustomLink($child_data['children']);
							if (!empty($sub_menu_categories)) {
								$menu_categories = array_merge($menu_categories, $sub_menu_categories);
							}
						}
					} else if ($child_data['link_type'] == "page") {
						// custom_link

					}
					$parent_id = $child_data['parent_id'];
					$position = $child_data['position'];
					$store_id = $child_data['store_id'];
					$language_id = $child_data['language_id'];
				}
				$this->setFranchiseSubCategories($menus[$menu_key]['children']);
				array_unshift($menus[$menu_key]['children'], array(
					"link_title" => "Franchise",
					"link_type" => "category",
					"value" => implode(',', $menu_categories),
					"type" => "child",
					"parent_id" => $parent_id,
					"position" => $position + 1,
					"status" => "1",
					"store_id" => $store_id,
					"language_id" => $language_id,
					"href" => "",
					"name_for_id" => "franchise",
				));
			} else {
				continue;
			}
		}
	}

	private function getAllChildCategoryForCustomLink($data) {
		$menu_categories = array();
		foreach ($data as $key => $child_data) {
			if ($child_data['name_for_id'] != "franchise") {
				if ($child_data['link_type'] == "category") {
					// category
					$menu_categories[] = $child_data['value'] . "_F";
				} else if ($child_data['link_type'] == "others") {
					// custom_link
					if (!empty($child_data['children'])) {
						$sub_menu_categories = $this->getAllChildCategoryForCustomLink($child_data['children']);
						if (!empty($sub_menu_categories)) {
							$menu_categories = array_merge($menu_categories, $sub_menu_categories);
						}
					}
				}
			}
		}
		return array_unique($menu_categories);
	}

	public function get_profile() {
		$customer_id = 0;
		$access_token = "";
		$app_version_code = 0;
		$rt = array();
		$call_by_api = 'get_profile';
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			echo $this->create_profile($call_by_api);exit;
		} else {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Request.';
			echo json_encode($rt);exit;
		}
	}

	public function update_profile() {
		$user_id = 0;
		$rt = array();
		$call_by_api = 'update_profile';

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->load->model('restapi/service');
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$request['call_by_api'] = $call_by_api;
			$this->validateApiCall();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$customer = $this->model_restapi_service->createCustomerProfile($request);
						if (!empty($customer['error'])) {
							$rt['error_code'] = '1003';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = $customer['error'];
							$this->logForTest('create_profile', $inputJSON, $rt);
						} else {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = 'Profile updated successfully.';
							$this->logForTest('create_profile', $inputJSON, $rt);
						}
					} else {
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token !!!';
					}
				} else {
					$rt['error_code'] = '1003';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
			}
		} else {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Request.';
		}
		echo json_encode($rt);exit;
	}

	/*
		   * @method: getWishlistMessages
		   * @params: user_id, access_token
		   * @return(on success): default_sharing_template, sharing_margin, wishlist_data
		   * @author: Devendra Dhayal
	*/
	public function getWishlistMessages() {
		// check request method
		if (($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Request.';
			echo json_encode($rt);exit;
		}

		$inputJSON = file_get_contents('php://input');
		$request = json_decode($inputJSON, TRUE);
		$rt = array();
		$this->validateApiCall();

		// check access token
		if (!isset($request['access_token'])) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'http request does not have access token.';
			echo json_encode($rt);exit;
		}

		// check user id
		if (!isset($request['user_id'])) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'http request does not have user id.';
			echo json_encode($rt);exit;
		}

		$access_token = $request['access_token'];
		$user_id = $request['user_id'];

		$this->load->model('restapi/service');

		// validate access token
		$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
		if ($check_access_token < 1) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Access Token !!!';
			echo json_encode($rt);exit;
		}

		// get wishlist data
		$rt['data'] = $this->model_restapi_service->getWishlistMessages((int) $user_id);
		$rt['status'] = '1';
		$rt['status_text'] = 'Success';
		$rt['message'] = 'Data fetched successfully.';
		echo json_encode($rt);exit;
	}

	/*
		     * @method: updateNotificationToken(gcm_id in case of android or apns_token in case of IOS)
		     * @params: user_id, access_token, gcm_id or apns_token
		     * @return: success or failure
		     * @author: Devendra Dhayal, 08-05-2018
	*/
	public function updateNotificationToken() {
		// check request method
		if (($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Request Type!';
			echo json_encode($rt);exit;
		}

		$inputJSON = file_get_contents('php://input');
		$request = json_decode($inputJSON, TRUE);
		$rt = array();
		$this->validateApiCall();

		// check access token
		if (!isset($request['access_token'])) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'http request does not have access token.';
			echo json_encode($rt);exit;
		}

		// check user id
		if (!isset($request['user_id'])) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'http request does not have user id.';
			echo json_encode($rt);exit;
		}

		$access_token = $request['access_token'];
		$user_id = $request['user_id'];

		$this->load->model('restapi/service');

		// validate access token
		$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
		if ($check_access_token < 1) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Access Token !!!';
			echo json_encode($rt);exit;
		}

		// get wishlist data
		$response = $this->model_restapi_service->updateNotificationToken($request);
		if ($response) {
			$rt['status'] = '1';
			$rt['status_text'] = 'Success';
			$rt['message'] = 'Token successfully updated.';
			echo json_encode($rt);exit;
		} else {
			$rt['error_code'] = '1000';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Failed to update token.';
			echo json_encode($rt);exit;
		}

	}

	/**
	 * trueCallerAuth
	 * ture caller authentication
	 * @param : $signature, $package
	 * @author : Manish, 26-05-18
	 */
	public function trueCallerAuth() {
		$rt = array();
		// check request method
		if (($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$rt['error_code'] = '1001';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Request.';
			$rt['next_action'] = '';
			echo json_encode($rt);exit;
		}

		$inputJSON = file_get_contents('php://input');
		$request = json_decode($inputJSON, TRUE);

		if (empty($request['signature']) || empty($request['payload'])) {
			$rt['error_code'] = '1002';
			$rt['status'] = '0';
			$rt['status_text'] = 'error';
			$rt['message'] = 'Empty signature or payload.';
			$rt['next_action'] = '';
			echo json_encode($rt);exit;
		}

		# Verify with true caller
		$this->load->model('restapi/service');
		$tc_verify = $this->model_restapi_service->trueCallerApi($request['signature'], $request['payload']);

		if ($tc_verify === FALSE) {
			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'error';
			$rt['message'] = 'Unable to login with Truecaller. Kindly try another login/signup method.';
			$rt['next_action'] = '';
			echo json_encode($rt);exit;
		}

		$payload = base64_decode($request['payload']);
		$payload_arr = json_decode($payload, true);

		if (empty($payload_arr['phoneNumber'])) {
			$rt['error_code'] = '1004';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Please enter mobile number.';
			$rt['next_action'] = '';
			echo json_encode($rt);exit;
		} else {
			if (strlen($payload_arr['phoneNumber'] > 10)) {
				$phone_number = substr($payload_arr['phoneNumber'], -10);
				$country_code = substr($payload_arr['phoneNumber'], 1, -10);

			} else {
				$phone_number = $payload_arr['phoneNumber'];
				$country_code = '';
			}

			$first_name = isset($payload_arr['firstName']) ? $payload_arr['firstName'] : '';
			$last_name = isset($payload_arr['lastName']) ? $payload_arr['lastName'] : '';

			if (is_numeric($phone_number) && !preg_match('/^[0-9]{10}+$/', $phone_number)) {
				$rt['error_code'] = '1005';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'Mobile no is invalid, Please enter valid mobile no';
				$rt['next_action'] = '';
				echo json_encode($rt);exit;
			}

			# Get user details
			$user_data = $this->model_restapi_service->getCustomerByMobile($phone_number);

			if (!empty($user_data)) {

				$get_customer = $user_data;

				if (isset($get_customer['company']) && $get_customer['company'] != '') {
					$company = $get_customer['company'];
				} else {
					$company = '';
				}

				$customer_id = $get_customer['customer_id'];
				$access_token = $this->generate_access_token();

				$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, (string) $access_token, '0');

				/*** Check if FSE is trying to log in to customer's account
                     FSE not allowed to login in to other customer's account ***/

				$sales_agent = null;
				$login_by_sales_agent = 0;
				$sales_agent_self_account = 0;
				$device_id = trim(!empty($payload_arr['device_id']) ? $payload_arr['device_id'] : '');

				if (!empty($device_id)) {
					$sales_agent = $this->model_restapi_service->getSalesStaffWithDeviceId($device_id);

					if (!empty($sales_agent)) {

						$login_by_sales_agent = 1; // FSE is trying to login in via his device

						if ($mobile == $sales_agent['telephone']) {
							$sales_agent_self_account = 1; // FSE is trying to login in his account => (allow access)
						}
					}
				}

				if ($login_by_sales_agent && !$sales_agent_self_account) {
					$rt['error_code'] = '1001';
					$rt['status'] = '0';
					$rt['status_text'] = 'failed';
					$rt['message'] = "Not allowed to login.";
					$rt['popup_message'] = "Why are you trying to Login from another number? For any help, contact Head Office Sales Coordinator!";
					$rt['next_action'] = '';
					echo json_encode($rt);exit;
				} else {
					$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['message'] = 'User logged in successfully.';
					$rt['customer_id'] = $customer_id;
					$rt['access_token'] = $access_token;
					$rt['next_action'] = 'open_main';
					$rt['customer_types'] = $this->model_restapi_service->getCustomerTypes();
					$rt['data']['customer_id'] = $customer_id;
					$rt['data']['access_token'] = $access_token;
					$rt['data']['new_country'] = $get_customer['country_code'];
					$rt['data']['customer_type'] = $get_customer['customer_type_id'];

					if (empty($get_customer['customer_type_id'])
						|| (!$international_request && empty($get_customer['telephone']))
						|| ($international_request && empty($get_customer['email']))
						|| (strtoupper($request_by) == "ANDROID_APP"
							&& $app_version_code > 89
							&& isset($get_customer['customer_type_id'])
							&& strpos($get_customer['customer_type_id'], '2') !== false
							&& (empty($arr_address['address_1'])))) {

						$rt['next_action'] = 'open_profile';
					}

					$this->logForTest('true_caller_auth', $inputJSON, $rt);
					echo json_encode($rt);exit;
				}

			} else {
				# Sign up when user is not found.
				$otp = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
				$access_token = $this->generate_access_token();
				$request['password'] = (SITE_ENVIRONMENT == 'Production') ? rand(1000, 9999) : 1010;
				$request['access_token'] = $access_token;
				$request['mobile'] = $phone_number;
				$request['country_code'] = $country_code;
				$request['first_name'] = $first_name;
				$request['last_name'] = $last_name;

				if (isset($payload_arr['gcm_id'])) {
					$request['gcm_id'] = $payload_arr['gcm_id'];
				} else {
					$request['gcm_id'] = "";
				}

				$request['ip'] = $this->request->getIpAddress;

				$customer_id = $this->model_restapi_service->addCustomer($request);

				if ($customer_id) {
					/** add address if sufficient data found in truecaller data
					commenting for now as defualt address is being fetched with zone and country
					and truecaller doesnt give uus both
					 **/
					// $address_data['firstname'] = $first_name ?? "";
					// $address_data['lastname'] = $last_name ?? "";
					// $address_data['company'] = !empty( $payload_arr['companyName'] ) ? trim( $payload_arr['companyName'] ) : '';
					// $address_data['address_1'] = !empty( $payload_arr['street'] ) ? trim( $payload_arr['street'] ) : '';
					// $address_data['address_2'] = '';
					// $address_data['postcode'] = !empty( $payload_arr['zipcode'] ) ? trim( $payload_arr['zipcode'] ) : '';
					// $address_data['city'] = !empty( $payload_arr['city'] ) ? trim( $payload_arr['city'] ) : '';
					// $address_data['address_telephone'] = $phone_number ?? "";
					// $address_data['zone_id'] = '0';
					// $address_data['country_id'] = '0';

					// if( !empty( $address_data['company'] )
					//     || !empty( $address_data['address_1'] )
					//     || !empty( $address_data['postcode'] )
					//     || !empty( $address_data['city'] )) {

					//     $this->load->model( "account/address" );
					//     $this->model_account_address->addAddress( $address_data, (int) $customer_id );
					// }

					// Update location finder description during login and signup
					$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['message'] = 'User has been created successfully.';
					$rt['customer_id'] = $customer_id;
					$rt['access_token'] = $access_token;
					$rt['next_action'] = 'open_profile';
					$rt['is_signup'] = '1';
					$rt['customer_types'] = $this->model_restapi_service->getCustomerTypes();
					$rt['data']['customer_id'] = $customer_id;
					$rt['data']['access_token'] = $access_token;
					$rt['data']['customer_type'] = '';

					$this->logForTest('true_caller_auth', $inputJSON, $rt);
					echo json_encode($rt);exit;

				} else {
					$rt['error_code'] = '1006';
					$rt['status'] = '0';
					$rt['status_text'] = 'failed';
					$rt['message'] = 'User creation has been failed.';
					$rt['next_action'] = '';
					echo json_encode($rt);exit;
				}
			}
		}
	}

	public function getProductsByCustomerPreferences($data) {
		$this->load->model('restapi/service');
		$product_data = array();
		$product_data = $this->model_restapi_service->getProductsByCustomerPreferences($data);
		return $product_data;
	}
	/**
	 * Function : getDataFromCustomerWebsite
	 * Method to post json data using curl
	 * Request Parameters : $request
	 * Output :response according action
	 * @author Rahul 9 July 2018
	 * */
	public function getDataFromCustomerWebsite($request, $action) {
		$request['api_key'] = CUSTOMER_WEBSITE_API_KEY;
		$data_json = json_encode($request);
		$api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/" . $action;
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
		return $result;
		// if ($result['statusCode'] == '200') {
		// 	return true;
		// }else{
		// 	return false;
		// }
	}

	/**
	 * Function : __getStorePurchasedDaysMap
	 * Method to get key => value pair for store30 products
	 * @Output :StorePurchasedDaysMap
	 * @author Devendra July 2018
	 * */
	private function __getStorePurchasedDaysMap() {
		$result = array();
		foreach (STORE_PURCHASED_DAYS as $store => $store_data) {
			foreach ($store_data as $key => $value) {
				$result['STORE' . $store . $key] = array(
					'store' => $store,
					'days' => $value,
				);
			}
		}
		return $result;
	}

	/**
	 * Function : seller_login
	 * Method for seller login through seller app
	 * Request Parameters : mobile, email, passord, device_id
	 * @author Devendra, August 2018
	 * */
	public function seller_login() {
		if (!($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Invalid request',
			);
			echo json_encode($response);exit;
		}

		$inputJSON = file_get_contents('php://input');
		$request = json_decode($inputJSON, TRUE);
		$this->load->model('restapi/service');
		$this->load->model('account/customer');

		$this->validateApiCall();

		$headers = getallheaders();

		if (!isset($request['mobile'])) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Invalid request',
			);
			echo json_encode($response);exit;
		}

		$mobile = $request['mobile'];
		if (isset($request['password'])) {
			$password = $request['password'];
		} else {
			$password = '';
		}
		if (isset($request['email'])) {
			$email = $request['email'];
		} else {
			$email = '';
			$this->model_account_customer->deleteMobileLoginAttempts($request['mobile']);
		}

		$device_id = $request['device_id'];
		if (!empty($mobile)) {
			$get_customer = $this->model_restapi_service->getCustomerByMobile($mobile);
		}
		if (empty($get_customer) && !empty($email)) {
			$get_customer = $this->model_restapi_service->getCustomerByEmail($email);
		}

		if (empty($get_customer)) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'You are not registered yet. Please register (signup) first.',
			);
			echo json_encode($response);exit;
		}

		if (!empty($mobile)) {
			$check_customer = $this->model_restapi_service->checkCustomerByMobileAndPassword($mobile, $password);
		} else {
			$check_customer = $this->model_restapi_service->checkCustomerByEmailAndPassword($email, $password);
		}

		if ($check_customer == 0) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Incorrect login credential.',
			);
			echo json_encode($response);exit;
		}

		if ($check_customer == 'not_registered') {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'You are not registered yet. Please register (signup) first.',
			);
			echo json_encode($response);exit;
		}

		$customer_id = $get_customer['customer_id'];

		$seller_info = new SellerInfo();
		$seller_status = $seller_info->getSellerStatus($this->db, $customer_id);
		if ($seller_status == 0) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'You are not registered yet. Please register (signup) first.',
			);
			echo json_encode($response);exit;
		}

		if ($seller_status != MsSeller::STATUS_ACTIVE) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Dear Seller, Currently you are not allowed to login. Please contact Wholesalebox.',
			);
			echo json_encode($response);
			exit;
		}

		if (isset($get_customer['company']) && $get_customer['company'] != '') {
			$company = $get_customer['company'];
		} else {
			$company = '';
		}

		$access_token = $this->generate_access_token();
		$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, (string) $access_token, '0');
		if ($rs != 1) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Something went wrong. Please try again',
			);
			echo json_encode($response);exit;
		}

		$rt = array();
		$rt['status'] = '1';
		$rt['status_text'] = 'Success';
		$rt['message'] = 'User logged in successfully.';
		$rt['data']['customer_id'] = $customer_id;
		$rt['data']['access_token'] = $access_token;
		$rt['data']['gcm_id'] = $get_customer['ws_gcm_registration_id'];
		$rt['data']['name'] = $get_customer['firstname'] . ' ' . $get_customer['lastname'];
		$rt['data']['email'] = $get_customer['email'];
		$rt['data']['mobile'] = $get_customer['telephone'];

		$rt['data']['business_name'] = $company;
		$rt['data']['gst_number'] = $get_customer['gst_number'];
		$this->logForTest('customer_signup', $inputJSON, $rt);
		echo json_encode($rt);exit;
	}

	/**
	 * Function : verify_seller_otp
	 * Request Parameters : reg_telephone,country_code,otp version(2)
	 * Type : Post
	 * Output : {"status":1,"status_text":"Success","message":"Mobile no 9772737137 accepted. Otp sent on this number."}
	 * @author Devendra, August 2018
	 * */
	public function verify_seller_otp() {
		if (!($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Invalid request',
			);
			echo json_encode($response);exit;
		}

		$inputJSON = file_get_contents('php://input');
		$request = json_decode($inputJSON, TRUE);
		$this->load->model('restapi/service');
		$this->load->model('account/customer');

		$this->validateApiCall();

		$headers = getallheaders();

		if (!isset($request['mobile']) || !isset($request['otp'])) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Invalid request',
			);
			echo json_encode($response);exit;
		}

		$mobile = $request['mobile'];

		$device_id = $request['device_id'];

		if (is_numeric($mobile)) {
			$get_customer = $this->model_restapi_service->getCustomerByMobile($mobile);
		} else {
			$get_customer = $this->model_restapi_service->getCustomerByEmail($mobile);
		}

		if (empty($get_customer)) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'You are not registered yet. Please register (signup) first.',
			);
			echo json_encode($response);exit;
		}

		$request['otp_page'] = 'forgot_password';
		$check_data = $this->model_restapi_service->checkOTP($request);
		if (count($check_data) == 0) {
			$check_data = $this->model_restapi_service->checkMasterOTP($request);
		}

		if (empty($check_data)) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Invalid OTP.',
			);
			echo json_encode($response);exit;
		}

		$this->model_restapi_service->verifyOTP($check_data);

		$customer_id = $get_customer['customer_id'];

		$seller_info = new SellerInfo();
		$seller_status = $seller_info->getSellerStatus($this->db, $customer_id);
		if ($seller_status == 0) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'You are not registered yet. Please register (signup) first.',
			);
			echo json_encode($response);exit;
		}

		if ($seller_status != MsSeller::STATUS_ACTIVE) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Dear Seller, Currently you are not allowed to login. Please contact Wholesalebox.',
			);
			echo json_encode($response);
			exit;
		}

		if (isset($get_customer['company']) && $get_customer['company'] != '') {
			$company = $get_customer['company'];
		} else {
			$company = '';
		}

		$access_token = $this->generate_access_token();
		$rs = $this->model_restapi_service->VeriFyMobile((int) $customer_id, (string) $access_token, '0');
		if ($rs != 1) {
			$response = array(
				'error_code' => '1001',
				'status' => '0',
				'status_text' => 'Failed',
				'message' => 'Something went wrong. Please try again',
			);
			echo json_encode($response);exit;
		}

		$rt = array();
		$rt['status'] = '1';
		$rt['status_text'] = 'Success';
		$rt['message'] = 'User logged in successfully.';
		$rt['data']['customer_id'] = $customer_id;
		$rt['data']['access_token'] = $access_token;
		$rt['data']['gcm_id'] = $get_customer['ws_gcm_registration_id'];
		$rt['data']['name'] = $get_customer['firstname'] . ' ' . $get_customer['lastname'];
		$rt['data']['email'] = $get_customer['email'];
		$rt['data']['mobile'] = $get_customer['telephone'];

		$rt['data']['business_name'] = $company;
		$rt['data']['gst_number'] = $get_customer['gst_number'];
		$this->logForTest('customer_signup', $inputJSON, $rt);

		echo json_encode($rt);exit;
	}

	public function getSingleStoreBanners($return = false, $data = array()) {

		$offers = array();

		$this->load->model('tool/image');
		$this->load->model('restapi/service');

		$single_store_banners = $this->model_restapi_service->getSingleStoreAppBanners();

		if (!empty($single_store_banners)) {

			foreach ($single_store_banners as $key => $value) {

				$data['text'] = $value['title'];
				$data['url'] = "";
				$data['key'] = "";

				if ($value['type'] == "link") {

					$data['url'] = $value['link'];
				} else {
					$data['key'] = $value['link'] . $this->_banner_splitter . $value['type'];
				}

				$data['image'] = $this->model_tool_image->resize($value['image'], 720, 274);

				$offers[] = $data;
			}
		}

		if ($return) {
			return $offers;
		} else {
			echo json_encode($offers);
		}
	}

	/**
	 * Function : saveCustomerLocation
	 * Request Parameters : access_token,user_id,device_id,location_lat,location_lng,timestamp
	 * Type : Post
	 * Return : location save or not
	 * Author Rahul Singh, 22 Nov 2018
	 * */
	public function saveCustomerLocation() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$this->load->model('restapi/service');
			$rt = array();
			$this->validateApiCall();
			if (isset($request['access_token'])) {
				if (isset($request['user_id'])) {
					$access_token = $request['access_token'];
					$user_id = $request['user_id'];
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string) $access_token, (int) $user_id);
					if ($check_access_token > 0) {
						$response = $this->model_restapi_service->saveCustomerLocation($request);
						if ($response == '1') {
							$rt['status'] = '1';
							$rt['status_text'] = 'Success';
							$rt['message'] = 'Location successfully save.';
						} else {
							$rt['error_code'] = '1004';
							$rt['status'] = '0';
							$rt['status_text'] = 'Error';
							$rt['message'] = 'Location not save.';
						}
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'Resend Code Failed';
						$rt['message'] = 'Invalid access token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Signup failed';
				$rt['message'] = 'http request does not have access token.';
			}
			echo json_encode($rt);exit;
		}
	}

	/**
	 * gets referral details of a customer
	 * referral details includes: referral_code, order_count, commission amount
	 * @return json encoded referral details
	 * @author Anurag Jain, 17 July 2019
	 */
	public function getReferralDetails() {

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {

				if (isset($request['user_id'])) {

					$this->load->model('restapi/service');

					$access_token = $request['access_token'];
					$customer_id = $request['user_id'];
					$language = $request['language'] ?? "en";

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken($access_token, $customer_id);

					if ($check_access_token > 0) {

						$rt['status'] = '1';
						$rt['status_text'] = 'Success';

						// get referral details for customer
						$referral_details = array();
						$referral_details = $this->model_restapi_service->getReferralDetailsOfCustomer((int) $customer_id);

						if (!empty($referral_details)) {
							// details already exists (affiliate is already created)
							$rt['referral_details'] = $referral_details;
							$rt['message'] = 'Referral details fetched successfully.';

						} else {
							// affiliate is not created

							// create new affiliate for this customer
							$this->model_restapi_service->createCustomerAffiliate($customer_id);

							// get referral details for customer
							$referral_details = $this->model_restapi_service->getReferralDetailsOfCustomer((int) $customer_id);

							if (!empty($referral_details)) {
								$rt['referral_details'] = $referral_details;
								$rt['message'] = 'Referral details fetched successfully.';

							} else {
								$rt['message'] = 'No Referral details found.';
							}
						}

						if ($language == "hindi") {

							$rt['referral_details']['line_1'] = "आप 6 लाख तक कमा सकते हैं !";
							$rt['referral_details']['line_2'] = "यदि आपके द्वारा रेफ़र किया हुआ दुकानदार एक वर्ष में होलेसेलबॉक्स से 10 लाख की खरीद करता है, तो आप प्रति दुकानदार 30000 कमाते हैं और अगर आप 20 ऐसे दुकानदारों को रेफ़र करते हैं तो आप 6 लाख कमा सकते हैं !";
							$rt['referral_details']['line_3'] = "1 साल की ख़रीददारी के लिए वैध !";
							$rt['referral_details']['referral_image'] = STATIC_CONTENT_URL . "img/catalog/homepage_banners/referral_hindi_new.png";
							$rt['referral_details']['whatsapp_sharing_image'] = STATIC_CONTENT_URL . "img/catalog/homepage_banners/referral_whatsapp_share_image_hindi_new.png";

							if (!empty($rt['referral_details']['share_link'])) {
								$rt['referral_details']['share_link'] = "मैं *होलेसेलबॉक्स* पर नियमित रूप से सूरत, जयपुर और अहमदाबाद का माल सबसे कम थोक भावों पर ऑर्डर करता हूँ ।\n\nआप भी दूसरों की तुलना में लगभग 30-60 दिन पहले नए डिज़ाइन प्राप्त करके अपनी बिक्री बढ़ा सकते हैं ।\n\nसारा माल आप *30 दिन की उधारी* पर प्राप्त कर सकते हैं ।\n\nमेरे रेफरल कोड (" . $rt['referral_details']['code'] . ") का उपयोग करके या लिंक से ऐप डाउनलोड करके आप अपने पहले ऑर्डर पर 50 रुपये की छूट पा सकते हैं -\n\n" . $rt['referral_details']['share_link'];
							}

						} else {

							$rt['referral_details']['line_1'] = "You Can Earn Upto 6 Lakh!";
							$rt['referral_details']['line_2'] = "If buyer buys for 10 Lakhs in a year, you earn 30000 per buyer, 20 such buyers, and your earning is 6 Lakh!";
							$rt['referral_details']['line_3'] = "Valid for 1 year of buying!";
							$rt['referral_details']['referral_image'] = STATIC_CONTENT_URL . "img/catalog/homepage_banners/referral_english_new.png";
							$rt['referral_details']['whatsapp_sharing_image'] = STATIC_CONTENT_URL . "img/catalog/homepage_banners/referral_whatsapp_share_image_english_new.png";

							if (!empty($rt['referral_details']['share_link'])) {
								$rt['referral_details']['share_link'] = "Hey, I regularly order Surat, Jaipur and Ahmedabad items from *Wholesalebox*, at lowest wholesale prices.\n\nYou can also grow your sales by getting the latest fashion almost 30-60 days earlier than others.\n\nAlso get *30 Days credit*.\n\nDownload app & Get Rs. 50 discount in first order if you use my referral code (" . $rt['referral_details']['code'] . ") using link - \n\n" . $rt['referral_details']['share_link'];
							}
						}
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed.';
						$rt['message'] = 'Invalid access token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'HTTP request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'HTTP request does not have access token.';
			}
			echo json_encode($rt);
			exit;
		}
	}

	/**
	 * updates app version code for customer
	 * also updates in CRM
	 * @return json response
	 * @author Anurag Jain, 07 Aug 2019
	 */
	public function update_app_version() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);

			$rt = array();
			$this->validateApiCall();

			if (isset($request['access_token'])) {

				$access_token = $request['access_token'];

				if (isset($request['user_id'])) {

					$customer_id = $request['user_id'];

					$this->load->model('restapi/service');

					$check_access_token = $this->model_restapi_service->checkUserByAccessToken($access_token, $customer_id);

					if ($check_access_token > 0) {

						if (isset($request['app_version_code'])) {

							$app_version_code = $request['app_version_code'];

							$rt['status'] = '1';
							$rt['status_text'] = 'Success';

							// update app version code ( also in crm )
							$this->model_restapi_service->updateAppVersion((int) $customer_id, $app_version_code);

						} else {

							$rt['error_code'] = '1004';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed.';
							$rt['message'] = 'App version code not specified.';
						}
					} else {
						$rt['error_code'] = '1001';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed.';
						$rt['message'] = 'Invalid access token.';
					}
				} else {
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'HTTP request does not have user id.';
				}
			} else {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'HTTP request does not have access token.';
			}
			echo json_encode($rt);
			exit;
		}
	}

	/**
	 * updates journey status for RBL pre-approved user
	 * @return json response
	 * @author MSA, 10 Sept 2019
	 */
	public function track_app_event() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$this->load->model('restapi/service');

			$inputJSON = file_get_contents('php://input');

			$request = json_decode($inputJSON, TRUE);

			$rt = array();

			$this->validateApiCall();

			if (!empty($request['access_token'])) {

				if (!empty($request['user_id']) && $request['event'] == 'rbl_journey_completed') {

					$customer_id = $request['user_id'];

					$this->load->model('restapi/service');

					$isPreApprovedUserExist = $this->model_restapi_service->checkRBLPreApprovedUserExist($customer_id);

					if ($isPreApprovedUserExist) {

						$this->model_restapi_service->updateRBLPreApprovedUserForJourneyStatus($customer_id);

						$rt['status'] = '1';
						$rt['status_text'] = 'Success';

					} else {

						$rt['error_code'] = '1004';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'No RBL pre-approved user exist for this user id.';

					}

				} else {

					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'HTTP request does not have user id or rbl journey status.';
				}

			} else {

				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed.';
				$rt['message'] = 'Invalid access token.';
			}

			echo json_encode($rt);
			exit;
		}

	} // end of method

}
