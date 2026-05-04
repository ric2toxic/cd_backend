<?php

class ControllerRestapiCustomerwebsite extends Controller
{
    public $registry;


    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->registry = $registry;
    }

   	/**
	 * Method to check Eligibility For Website
	 * Request Parameters : access_token,user_id
	 * @return void It returns -1 if access token is valid or not,
	 *					  -2 check Eligibility Based On Order using customer id
	 *         			  -3 get sub-domain name using company name and city if company is null
     *                      then create subdomain using customer first-name, last-name and city
	 * @author Rahul, 11th June 2018
	 */
    public function checkEligibilityForWebsite() : void {
    	$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$request['call_by_api'] = "check eligibility for website";
			$app_version_code = $request['app_version_code'];
			$rt = array();
			$this->validateApiCall();
			if (isset($request['access_token'])){
				if (isset($request['user_id'])){
					$access_token = $request['access_token'] ?? '';
					$user_id = $request['user_id'] ?? 0;
					$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
					if($check_access_token > 0){
						$userId=$request['user_id'];
						$check_eligibility_based_on_order= $this->model_restapi_customerwebsite->checkEligibilityBasedOnOrder($userId);
						if($check_eligibility_based_on_order){
							$fields=array("address_id","has_website");
    						$customerCustomData=$this->model_account_customer->getCustomerDetails($userId,$fields);
        					$customerDetail=$this->model_restapi_service->getAddressDetail($customerCustomData['address_id']);
							// =======check for customer website is processing or build and return customer website required detail ===//
								$testAndGetResponseAccordingCustomer=$this->checkCustomerDetailAndReturnCustomerWesbiteDetail($customerCustomData,$customerDetail,$app_version_code);
									$rt=$testAndGetResponseAccordingCustomer;
						}else{
							$rt['domain_name'] = CUSTOMER_WEBSITE_DOMAIN;
							$rt['subdomain'] = '';
							$rt['error_code'] = '1001';
							$rt['is_check_eligibility_for_website']=false;
							$rt['status'] = '1';
							$rt['status_text'] = 'Failed';
							$rt['message'] = $this->not_eligble_message($app_version_code);
						}
					}else{
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['is_check_eligibility_for_website']=false;
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
					}
						 	
					}
				  else
					{
					$rt['error_code'] = '1002';
					$rt['domainName'] = '';
					$rt['status'] = '0';
					$rt['is_check_eligibility_for_website']=false;
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';
					}
				}else{
				$rt['error_code'] = '1004';
				$rt['status'] = '0';
				$rt['is_check_eligibility_for_website']=false;
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
			}
			}else{
				$rt['error_code'] = '1005';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['is_check_eligibility_for_website']=false;
				$rt['message'] = 'No request found.';
			}
			//=======clientSide validation=========//
			$rt['subdomain_min_length']=CUSTOMER_WEBSITE_SUBDOMAIN_MIN_LENGTH;
			$rt['subdomain_max_length']=CUSTOMER_WEBSITE_SUBDOMAIN_MAX_LENGTH;
			$rt['subdomain_length_error_message']='domain name must be between '.CUSTOMER_WEBSITE_SUBDOMAIN_MIN_LENGTH.' to '.CUSTOMER_WEBSITE_SUBDOMAIN_MAX_LENGTH.' characters';
			echo json_encode($rt); exit;
	}
	
/**
	 * Function : not_eligble_message
	 * Request Parameters : app_version_code
	 * Type : Post
	 * @author Rahul 22 September 2018
	 * Output : not eligble message device vesion vise
	 * */
	private function not_eligble_message($app_version_code){
		$customer_website_language = array();
		$this->load->autoLoadLanguage('restapi/customerwebsite', $customer_website_language);
		if($app_version_code>'89'){
			$message=$customer_website_language['not_eligible_mssg'];
		}
		else{
			$message=$customer_website_language['not_eligible_mssg_older_version'];
		}
		return $message;
	}

	/**
	 * Function : getWebsiteCoreCustumer
	 * Request Parameters : customer_id
	 * Type : Post
	 * @author Rahul 14th June 2018
	 * Output : website customer detail ex. logo, name, contact
	 * */
	public function getWebsiteCoreCustumer(){
		if (($this->request->server['REQUEST_METHOD'] == 'POST')){
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$check_access_token=$this->validateAccessTokenForUser($request);
			if($check_access_token['status']=='1'){
			$request['customer_id']=$request['user_id'];
			$request['api_key']=CUSTOMER_WEBSITE_API_KEY;
			$data_json = json_encode($request);
                              $api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/getWebsiteCoreCustomer";
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
                              	$this->load->model('tool/image');
                              	$result['data']['domain']=CUSTOMER_WEBSITE_DOMAIN;
                              	$result['data']['logo']=$this->model_tool_image->getOriginalImage($result['data']['logo']);
                              	if(!empty($result['data']['images_array'])){
                              		foreach($result['data']['images_array'] as $key=>$storeimage){
                              			$result['data']['images_array'][$key]['image_url']=$this->model_tool_image->getOriginalImage($storeimage['image_url']);
                              			$result['data']['images_array'][$key]['file_name']=$storeimage['image_url'];
                              		}
                              	}
                              	if(!empty($result['data']['theme_list'])){
                              		foreach($result['data']['theme_list'] as $key=>$themeDetail){
                              			$result['data']['theme_list'][$key]['image_url']=$this->model_tool_image->getOriginalImage($themeDetail['image']);
                              		}
                              	}
                              	$full_url = ' https://'.$result['data']['subdomain'].'.'.CUSTOMER_WEBSITE_DOMAIN;
                              	$result['data']['gmb_authenticate_url']=trim($full_url.'/authenticate_google_post');
                              	$result['data']['name']=html_entity_decode($result['data']['name']);
                              		$result['data']['sms_footer']=$full_url;
									$rt['status'] = '1';
									$rt['customer_detail'] = $result['data'];
									$rt['status_text'] = 'Success';
									$rt['message'] = 'User datail';
                              }else{
                              		$rt['status'] = '0';
									$rt['error_code'] = '1001';
									$rt['status_text'] = 'Failed';
									$rt['message'] = $result['message'];
                              }
                          }else{
					$rt=$check_access_token;
				}
				
		}

                              echo json_encode($rt); exit;
	}
	 /**
	 * Function : validateDomainForWebsite
	 * Method to check Domain name For Website is valid or not
	 * Request Parameters : domain_name
	 * Type : Post
	 * @author Rahul 11th June 2018
	 * Output : domainName is valid or not, Customer is eligible or not for this domain name and
	 * also validate format of domain for special character.
	 * */

	public function validateDomainForWebsite(){
	    	$this->load->model('restapi/service');
	    	$this->load->model('restapi/customerwebsite');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')){

					$inputJSON = file_get_contents('php://input');
					$request = json_decode($inputJSON, TRUE);

					$check_access_token=$this->validateAccessTokenForUser($request);
					if($check_access_token['status']=='1'){
					$request['call_by_api'] = "validate domain for website";
					$app_version_code = $request['app_version_code'];
					$rt = array();
					$rt['is_validate_domain_for_website'] = true;
					$sub_domain_name = strtolower($request['subdomain']);
					//validate format of domain for special character
					 if (preg_match ('/^[a-zA-Z0-9]+([a-zA-Z0-9]+)*$/', $sub_domain_name)){
					 	$responseDomain=$this->checkAvailibiltyOnCustomerDomain($sub_domain_name);
                              if ($responseDomain['status'] == 1) {
						 			$rt['status'] = '1';
									$rt['subDomainName'] = $sub_domain_name;
	    							$rt['status_text'] = 'Success';
	      							$rt['message'] = 'Customer is eligble for create website.';	
					 		}
					 		else{
					 				$rt['status'] = '0';
					 				$rt['error_code'] = '1001';
									$rt['subDomainName'] = $sub_domain_name;
    								$rt['status_text'] = 'Failed';
      								$rt['message'] = 'Sub-domain name already exist.';
					 			}
							
					}else{
							$rt['status'] = '0';
							$rt['error_code'] = '1002';
							$rt['subDomainName'] = $sub_domain_name;
    						$rt['status_text'] = 'Failed';
      						$rt['message'] = 'Customer not use special character or Dot in domain name.';
					}
					}else{
						$rt=$check_access_token;
					}

				}
			echo json_encode($rt); exit;

	}

	/**
	 * Function : checkAvailibiltyOnCustomerDomain
	 * Method to check the available domain_name for customer which is passed as parameter
	 * Request Parameters : str sub_domain_name
	 * Type : Post
	 * Output / Return : status 1 on success and 0 on failed in json format
	 * @author Rahul 14th June 2018
	 * */
	public function checkAvailibiltyOnCustomerDomain($sub_domain_name){ 
					 	$requestdomain['subdomain']=$sub_domain_name;
					 	$requestdomain['api_key']=CUSTOMER_WEBSITE_API_KEY;
					 	$data_json = json_encode($requestdomain);
					 	$api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/checkWebsiteDomainApi";
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
                              if ($result['statusCode'] == '200' && $result['data']=='0') {
						 			$rt['status'] = '1';
									$rt['domainName'] = $sub_domain_name;
	    							$rt['status_text'] = 'Success';
	      							$rt['message'] = 'Customer is eligible for create website.';	
						 		}
						 		else{
						 				$rt['status'] = '0';
						 				$rt['error_code'] = '1001';
										$rt['domainName'] = $sub_domain_name;
	    								$rt['status_text'] = 'Failed';
	      								$rt['message'] = 'Domain name already exist.';
						 		}
						 		return $rt; exit;
					

	}

	 /**
	 * Function : createWebsiteForCustomer
	 * Method to validate phone number add cutomer detail in website table
	 * Request Parameters : customer detail and accessToke, User ID
	 * Type : Post
	 * @author Rahul 11th June 2018
	 * Output : mobile validation and insert customer data in website
	 * */
	public function createWebsiteForCustomer(){
    	$this->load->model('restapi/service');
		$this->load->model('restapi/customerwebsite');

		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$request = $this->request->post;
			$request['call_by_api'] = "create website for customer";
			if (!isset($request['is_crm']) && $request['is_crm'] != '1')
				{
				$app_version_code = $request['app_version_code'];
				}

			$rt = array();
			$rt['is_create_website_for_customer'] = true;
			$this->validateApiCall();


			if (isset($request['access_token']) || (isset($request['is_crm']) && $request['is_crm'] == '1')){
				if (isset($request['user_id'])){
					$request[''] = $request['customer_id'];
					$user_id = $request['user_id'];
					if (!isset($request['is_crm']) && $request['is_crm'] != '1')
						{
							$access_token = $request['access_token'];
							$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
						}else{
							$check_access_token=1;
						}
					if ($check_access_token > 0 )
						{
						$request['mobile'] = $request['contact_number'];
						$userId = $request['user_id'];
						if (empty($request['mobile']))
							{
							$rt['error_code'] = '1004';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'Please enter mobile number.';
							$rt['next_action'] = '';
							echo json_encode($rt);
							exit;
							}
						  else
							{
							$country_code = $request['country_code'];
							if($country_code == '+91' || $country_code==''){
									if (strlen($request['mobile'] > 10))
										{
										$phone_number = substr($request['mobile'], -10);
										$country_code = substr($request['mobile'], 1, -10);
										}
									  else
										{
										$phone_number = $request['mobile'];
										$country_code = '';
										}
									if (is_numeric($phone_number) && !preg_match('/^[0-9]{10}+$/', $phone_number))
										{
										$rt['error_code'] = '1005';
										$rt['status'] = '0';
										$rt['status_text'] = 'Failed';
										$rt['message'] = 'Mobile no is invalid, Please enter valid mobile no';
										$rt['next_action'] = '';
										echo json_encode($rt);
										exit;
										}
									  else
									if (!is_numeric($phone_number))
										{
										$rt['error_code'] = '1005';
										$rt['status'] = '0';
										$rt['status_text'] = 'Failed';
										$rt['message'] = 'Mobile no is invalid, Please enter valid mobile no';
										$rt['next_action'] = '';
										echo json_encode($rt);
										exit;
										}
							}

							// =======create user domain=========//
							// $userAdd= $this->model_restapi_customerwebsite->customerAdd($this->$db_webcustomer,$request);

							$directory = 'fashcart/logo';
							if (!empty($_FILES['logo']['name']))
								{
								$values = $_FILES['logo']['name'];
								$dt = new DateTime();
								$ext = pathinfo($values, PATHINFO_EXTENSION);

								// Sanitize the filename

								$values = $request['user_id'] . "_" . $dt->format('Y_m_d_H_i_s') ."_".uniqid(rand()). "." . $ext;
								$filename = basename(html_entity_decode($values, ENT_QUOTES, 'UTF-8'));
								$request['logo'] = $directory . '/' . $filename;
								}
							  	else if(isset($request['is_crm'])){
							  		$request['logo']=$request['logo'];
							  		}
								else{
								$request['logo'] = '';
								}

							$updateDb = '0';
							$request['wsb_store_id'] = (int)$this->config->get('config_store_id');
							if (isset($request['update']) && !empty($request['update']) && $request['update'] == '1')
								{
								$api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/updateWebsite";
								$updateDb = '1';
								}
							  else
								{
								$subdomain = strtolower($request['subdomain']);
								$api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/createWebsite";

								// =====For test domain string length is greater then 2 and less than 3===//

								if (strlen($subdomain) > CUSTOMER_WEBSITE_SUBDOMAIN_MAX_LENGTH || strlen($subdomain) < CUSTOMER_WEBSITE_SUBDOMAIN_MIN_LENGTH)
									{
										$rt['status'] = '0';
										$rt['error_code'] = '1009';
										$rt['subDomainName'] = $subdomain;
										$rt['status_text'] = 'Failed';
										$rt['message'] = 'Subdomain can contain only between ' . CUSTOMER_WEBSITE_SUBDOMAIN_MIN_LENGTH . ' to ' . CUSTOMER_WEBSITE_SUBDOMAIN_MAX_LENGTH . ' characters.';
										echo json_encode($rt);
										exit;
									}
								  else if (preg_match('/^[a-zA-Z0-9]+([a-zA-Z0-9]+)*$/', $subdomain))
									{
									$responseDomain = $this->checkAvailibiltyOnCustomerDomain($subdomain);
									if ($responseDomain['status'] == 1)
										{
										$rt['status'] = '1';
										$rt['subDomainName'] = $subdomain;
										$rt['status_text'] = 'Success';
										$rt['message'] = 'Customer is eligible for create website.';
										}
									  else
										{
										$rt['status'] = '0';
										$rt['error_code'] = '1001';
										$rt['subDomainName'] = $subdomain;
										$rt['status_text'] = 'Failed';
										$rt['message'] = 'Sub-domain name already exist.';
										echo json_encode($rt);
										exit;
										}
									}
								  else
									{
									$rt['status'] = '0';
									$rt['error_code'] = '1002';
									$rt['subDomainName'] = $subdomain;
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Customer not use special character or Dot in domain name.';
									echo json_encode($rt);
									exit;
									}

								// ========user_product_data=======//

								$request['user_product_data'] = $this->model_restapi_customerwebsite->addNewCustomerProduct($request['user_id']);
								}

									$request['name'] = $request['business_name'];
									$request['mobile'] = $request['contact_number'];
									$request['customer_id'] = $request['user_id'];
									$request['api_key'] = CUSTOMER_WEBSITE_API_KEY;
									$subdomain = strtolower($request['subdomain']);
									$request['subdomain'] = $subdomain;
									$data_json = json_encode($request);
									$ch = curl_init($api_url);
									curl_setopt($ch, CURLOPT_HEADER, 0);
									curl_setopt($ch, CURLOPT_HTTPHEADER, array(
										'Content-Type: application/json',
										'Content-Length: ' . strlen($data_json)
									));
									curl_setopt($ch, CURLOPT_VERBOSE, 1);
									curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
									curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									$result = curl_exec($ch);
									$result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
									$result = json_decode($result, true);
									if ($result['statusCode'] == '200')
										{
										if (!empty($request['logo']) && !isset($request['is_crm']))
											{
											$this->uploadImages($directory, $filename);
											}

										// =======update user as a procesing========//

										if ($updateDb == '0')
											{
											$this->model_restapi_customerwebsite->updateCustomerWebsiteStatus($request['user_id'], '1', '0');
											$message = "Your website has been created"; //'Customer sucessfully added.';
											$full_url = 'https://' . $subdomain . '.' . CUSTOMER_WEBSITE_DOMAIN;
											if (!empty($request['business_name']))
												{

												// $message_txt='I have launched the website for my store '.$request['business_name'].'! Now you can access our store\'s products on '.$full_url.'! For any help, call us on '.$request['contact_number'].' !\n <br /> Regards,\n <br />'.$request['business_name'];

												$message_txt = "I have launched the website for my store! Now you can access my store's products on " . $full_url . "! For any help, call me on " . $request['contact_number'] . "  - " . $request['business_name'];
												}
											  else
												{
												$message_txt = "I have launched the website for my store! Now you can access my store's products on " . $full_url . "! For any help, call me on " . $request['contact_number'] . "  - " . $request['business_name'];

												// $message_txt='I have launched a website for my store! Now you can access my store\'s products on '.$full_url.'! For any help, call us on '.$request['contact_number'].' !\n <br />Regards,\n <br />'.$request['name'];

												}

											$share_text = $message_txt;
											}
										  else
											{
											$message = "Your website has been updated."; //'Customer sucessfully updated.';
											$full_url = 'https://' . $subdomain . '.' . CUSTOMER_WEBSITE_DOMAIN;
											$share_text = "I have launched the website for my store! Now you can access my store's products on " . $full_url . "! For any help, call me on " . $request['contact_number'] . "  - " . $request['business_name'];
											}

										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
										$rt['message'] = $message;
										$rt['mywebsite_url'] = $full_url;
										$rt['is_instant'] = 'true';
										$rt['sharing_text'] = $share_text;
										}
									  else
										{
										$rt['error_code'] = '1007';
										$rt['status'] = '0';
										$rt['status_text'] = 'Failed';
										$rt['message'] = $result['message'];
										}
									}
						}
					  else
						{
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Invalid Access Token.';
						}
					}
				  else
					{
					$rt['error_code'] = '1008';
					$rt['domainName'] = '';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'http request does not have user id.';

					// $this->logForTest('create_profile', $inputJSON, $rt);

					}
				}
			  else
				{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
				}
			}
		  else
			{
			$rt['error_code'] = '1010';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'No request found.';
			}

		echo json_encode($rt);
		exit;
	}

	/**
	 * Function : cronEntryForCustomerWebsite
	 * Method to upload product on new website server
	 * Request Parameters : customer_id,$key key=3 is new customer else existing customer
	 * @author Rahul 14th June 2018
	 * */

public function cronEntryForCustomerWebsite(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				if(!empty($request['customers'])){
					foreach($request['customers'] as $key=>$cutomerId){
						if($key=='3'){
							$cutomerDetail['month']=$this->model_restapi_customerwebsite->addNewCustomerProduct($cutomerId['customer_id']);
						}else{
							$cutomerDetail['hour']=$this->model_restapi_customerwebsite->addExistingCustomerProduct($cutomerId['customer_id']);
						}
					}
				}
				echo json_encode($cutomerDetail); exit; 
				//echo '<pre>'; print_r($cutomerDetail); exit;

			}

}
	/**
	 * Function : uploadImages
	 * Method to upload image on cdnimages.net
	 * Request Parameters : directory,filename
	 * @author Rahul 14th June 2018
	 * */



	public function uploadImages($directory,$filename,$fileType='logo'){
                // Check to see if any PHP files are trying to be uploaded
		
                $file_name_with_full_path = $_FILES[$fileType]['tmp_name'];
            
                if(is_uploaded_file($_FILES[$fileType]['tmp_name'])){
                    if (function_exists('curl_file_create')) { // php 5.5+
                        $cFile = curl_file_create($file_name_with_full_path);
                    } else { // 
                        $cFile = '@' . realpath($file_name_with_full_path);
                    }
                }
            
                $post = array('file'=> $cFile);
                $ch = curl_init();
                $target_url = 'https://cdnimages.net/fileupload.php?directory='.$directory.'&filename='.$filename;
                	curl_setopt($ch, CURLOPT_URL,$target_url);
                	curl_setopt($ch, CURLOPT_POST,1);
                	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                	$result = curl_exec($ch);              
                	curl_close($ch);
                    $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                	$result = json_decode($result, true);
                              if (!empty($result['success'])) {
                              		return true;

                              }else{
                              		return false;
                              }
        
    }

    	/**
	 * Function : uploadMultipleImages
	 * Method to upload multiple image on cdnimages.net
	 * Request Parameters : directory,file
	 * @author Rahul 14th June 2018
	 * */



	public function uploadMultipleImages($directory='fashcart_store_image',$files){
                $post = $files;
                $ch = curl_init();

                $target_url = 'https://cdnimages.net/multiplefileupload.php?directory='.$directory;
                	curl_setopt($ch, CURLOPT_URL,$target_url);
                	curl_setopt($ch, CURLOPT_POST,1);
                	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                	$result = curl_exec($ch);              
                	curl_close($ch);
                    $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                	$result = json_decode($result, true);
                              if (!empty($result['success'])) {
                              		return true;

                              }else{
                              		return false;
                              }
        
    }
	/**
	 * Function : validateApiCall
	 * @author Rahul 11th June 2018
	 * Output : if config_app_maintenance show error in json format
	 			if any error occur related to header then show error in json format
	 * */
	public function validateApiCall(){
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		$this->manage_error_reporting();

		if ($this->config->get('config_app_maintenance') == 1)
			{
			$rt['error_code'] = '8888';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Hey!! Engineers @ work!!. We will be back shortly. C Ya';
			echo json_encode($rt);
			exit;
			}

		return true;
		$this->load->model('restapi/service');
		$headers = getallheaders();
		if (!$this->model_restapi_service->validateApiCall($headers))
			{
			$rt['error_code'] = '9999';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Invalid API call';
			echo json_encode($rt);
			exit;
			}
	}
	/**
	 * Function : removeSpecialCharacter
	 * @author Rahul 11th June 2018
	 * Output : remove Special character from given string 
	 * */
	private function removeSpecialCharacter($string) {
			   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
			   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}
	/**
	 * Function : manage_error_reporting
	 * @author Rahul 11th June 2018
	 * Output : if header disable_error_reporting is not empty then error_repoting set 0
	 * */
	private function manage_error_reporting() {
	    $headers = getallheaders();
		    if(!empty($headers['disable_error_reporting'])) {
		      error_reporting(0);
		    }
	    return true;
  	}

  	/**
	 * Function : logForTest
	 * Method to maintain log error occur when call api
	 * Request Parameters : method, request and response
	 * @author Rahul 11th June 2018
	 * Output : create log text file
	 * */
    private function logForTest($method, $request, $response) {
        $txt = "\n*********************************************************\n";
	    $myfile = fopen(DIR_LOGS."log_customerwebsite_api.txt", "a");
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
    }

    	/**
	 * Function : upcomingDesignCustomerWebsite
	 * Method to upcoming product list according user preference
	 * Request Parameters : customer_id
	 * Output :product list
	 * @author Rahul 16th June 2018
	 * */

	public function upcomingDesignCustomerWebsite(){
		$this->load->model('restapi/service');
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
    	 		//$request['customer_id']=array('12163');
				if(!empty($request['customer_id'])){
					$customers=$request['customer_id'];
					$data=array();
					$data['customers'] = $customers;
					if(!empty($request['page'])){
						$data['page'] = $request['page'];
					}
					$data['limit'] = 100;
					if(!empty($request['limit'])){
						$data['limit'] = $request['limit'];
					}
					if(!empty($request['handpicked_ids'])){
						$data['handpicked_ids'] = $request['handpicked_ids'];
					}
            		$product_lists = $this->model_restapi_service->getProductsByCustomerPreferences($data);
				}
				echo json_encode($product_lists); exit; 
				//echo '<pre>'; print_r($cutomerDetail); exit;
			}
	}

		/**
	 * Function : sendMailRequestCustomerWebsite
	 * Method to send request mail define user
	 * Request Parameters : customer_store_name and customer_phone_number
	 * Output :true or false
	 * @author Rahul 4 July 2018
	 * */

	public function sendMailRequestCustomerWebsite(){
		$this->load->model('restapi/service');
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
    	 		//$request['customer_id']=array('12163');
				if(!empty($request['customer_store_name'])){
					$subject="Fashcart request from ".$request['customer_store_name']."/".$request['customer_phone_number'];
					$message='';
					$message .='Hi!';
					$message .='<br /><br />';
					$message .='Following customer has placed a request to create a website for his/her store '.$request['customer_store_name'].'.';
					$message .='<br/><br>';
					$message .= ' Please assist.';
					$message .='<br/><br>';
					$message .= ' Find following information.';
					$message .='<br/><br>';
					$message .='Shop Name: <b>'.$request['customer_store_name'].'</b>';
					$message .='<br>';
					$message .='Customer Mobile Number: '.$request['customer_phone_number'];
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
				        $mail->addAddress('training@wholesalebox.co', 'Training');
				        $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
				        $mail->Subject =  html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
				        $mail->msgHTML($message);
				        if($mail->send()){
				        	$msg = "Email has been sent.";
				        	$status = "1";
				        }else{
				        	$msg = "Email not sent. try later.";
				        	$status = "0";
				        }
				        $data=array();
						$data['msg'] = $msg;
     					$data['status'] = $status;
                        
                        echo json_encode($data);  exit;
				}
				
				exit; 
			}
	}

	/**
	 * Function : uploadStoreImageOnCustomerWebsite
	 * Method to upload image on cdn server and save on fashcart DB
	 * Request Parameters : customer_id and imageArray
	 * Output :true or false
	 * @author Rahul 6 July 2018
	 * */

	public function uploadStoreImageOnCustomerWebsite(){
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$request['user_id'] =$this->request->post['user_id'];
				$request['access_token'] =$this->request->post['access_token'];
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
				  $directory = 'customer_website';
			            if(!empty($_FILES['store_images']['name'])){
			            	$rt = array();
				            $values=$_FILES['store_images']['name'];
	                        $dt = new DateTime();
	                        $ext = pathinfo($values, PATHINFO_EXTENSION);
	                        // Sanitize the filename
	                        $values   = $this->request->post['user_id']."_store_image_".$dt->format('Y_m_d_H_i_s')."_".uniqid(rand()).".".$ext;
	                        $filename = basename(html_entity_decode($values, ENT_QUOTES, 'UTF-8'));
							$upload_image_response=$this->uploadImages($directory,$filename,'store_images');
							if($upload_image_response){
								$image_path= $directory."/".$filename;
								$saveImageOnStoreWebsite=$this->saveImageOnStoreWebsite($this->request->post['user_id'],$image_path);
								if ($saveImageOnStoreWebsite) {
									$image_url=$this->model_tool_image->getOriginalImage($image_path);
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
										$rt['image'] = $image_path;
										$rt['image_url'] = $image_url;
										if(!empty($this->request->post['update']) && $this->request->post['update']=='1'){
											$file_path=$this->request->post['old_image'];
											$path  = $file_path;
											$deleteImageResponse=$this->deleteImageOnCdnServer($path);
											if($deleteImageResponse){

												$request['customer_id'] =$this->request->post['user_id'];
												$request['image_url'] = $path;
												$this->postApiandDataUsingCurlCustomerWebsite($request,'removeStoreImage');
											}
										}
								}else{
									$rt['error_code'] = '1002';
									$rt['image'] = '';
									$rt['image_url'] = '';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Image not save on store Website.';

								}
								

							}else{
								$rt['error_code'] = '1001';
								$rt['image'] = '';
								$rt['image_url'] = '';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Image not save.';
							}
							 
			}
			}else{
					$rt=$check_access_token;
				}
			echo json_encode($rt);  exit;
		}
	}

	/**
	 * Function : deleteStoreImage
	 * Method to delete Image on CDN server and customer website using user_id and image path
	 * Request Parameters : file path and user_id
	 * Output : delete process status
	 * @author Rahul 13 July 2018
	 * */
	public function deleteStoreImage(){
		if (($this->request->server['REQUEST_METHOD'] == 'POST')){
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$app_version_code = $request['app_version_code'];
				$rt = array();
				$this->validateApiCall();
				if (isset($request['access_token'])){
					$rt=$this->deleteStoreImageAfterAccessGranted($request);
				}
			else{
				$rt['error_code'] = '1001';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'http request does not have access token.';
			}
			}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['is_check_eligibility_for_website']=false;
				$rt['message'] = 'No request found.';
			}

			 echo json_encode($rt);  exit;
		
	}


	/**
	 * Function : deleteStoreImageAfterAccessGranted
	 * Method to delete Image on CDN server and customer website using user_id and image path
	 * Request Parameters : file path and user_id
	 * Output : delete process status
	 * @author Rahul 13 July 2018
	 * */
	public function deleteStoreImageAfterAccessGranted($request){
		if (!empty($request['user_id']) && !empty($request['image_url'])){
							$request['customer_id'] = $request['user_id'];
							$request['image_url'] = $request['image_url'];
							$delete_image_on_customer_website=$this->postApiandDataUsingCurlCustomerWebsite($request,'removeStoreImage');
							if($delete_image_on_customer_website){
											$path  = $request['image_url'];
											$deleteImageResponse=$this->deleteImageOnCdnServer($path);
											//if($deleteImageResponse){
												$rt['status'] = '1';
												$rt['status_text'] = 'Success';
												$rt['message'] = 'Image successfully removed';
											// }else{
											// 	$rt['error_code'] = '1005';
											// 	$rt['status'] = '1';
											// 	$rt['status_text'] = 'Failed';
											// 	$rt['message'] = 'Image removed on customer website but not remove on cdn server';
											// }
							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Image cannot remove on customer website. Please try again later';
							}

					}else{
						$rt['error_code'] = '1003';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'User id or image path is empty.';
					}
					return $rt;
	}

	/**
	 * Function : deleteImageOnCdnServer
	 * Method to delete Image on CDN server
	 * Request Parameters : file path
	 * Output :true or false
	 * @author Rahul 9 July 2018
	 * */
	public function deleteImageOnCdnServer($path=NULL){
		$ch = curl_init();
		$post = array('path[]' => $path);
		$target_url = 'https://cdnimages.net/filemanager/delete.php';
		//$target_url = 'http://localhost/wholesalebox/cdn/delete.php';
		curl_setopt($ch, CURLOPT_URL,$target_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec ($ch);
		$result_array = json_decode($result,true);
		if(isset($result_array['success'])){
			return 1;
		}else{
			return 0;
		}
	}

	/**
	 * Function : getCustomerOrderProductDetailForStoreWebsite
	 * Request Parameters : customer_id and date_added
	 * Output :product list
	 * @author Rahul 9 July 2018
	 * */
	public function getCustomerOrderProductDetailForStoreWebsite(){
		$this->load->model('restapi/customerwebsite');
		$this->load->model('restapi/service');
		$productDetail=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				if(!empty($request['customer_id'])){
					$store_website_create_date=$request['date_added'];
					$befor_three_month_date_str=strtotime($store_website_create_date.' -3 months');
					$queryData['customer_id']=$request['customer_id'];
					$queryData['start_date']=date('Y-m-d H:i:s',$befor_three_month_date_str);
					$queryData['end_date']=date('Y-m-d H:i:s');
					$productDetail=$this->model_restapi_customerwebsite->getCustomerProductDetailAccordingDate($queryData);
				}
			}
								$rerturn['status'] = '1';
								$rerturn['productDetail'] = $productDetail;
								echo json_encode($rerturn); exit; 
		 //"96103": "2018-06-14 16:44:02"
		exit;
		
	}

		/**
	 * Function : getCustomerOrderProductDetailForStoreWebsite
	 * Request Parameters : customer_id and date_added in array
	 * Output :product list group by customer_id
	 * @author Rahul 21 May 2019
	 * */
	public function getMultipleCustomerOrderProductDetailForStoreWebsite(){
		$this->load->model('restapi/customerwebsite');
		$this->load->model('restapi/service');
		$productDetail=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
					foreach ($request as $key => $value) {
						if(!empty($value['details']['customer_id'])){
						$store_website_create_date=$value['details']['date_added'];
						//$befor_three_month_date_str=strtotime($store_website_create_date.' -3 months');
						//get only 3 month back order product
						$befor_three_month_date_str=strtotime(date('Y-m-d H:i:s').' -3 months');
						$queryData['customer_id']=$value['details']['customer_id'];
						$queryData['start_date']=date('Y-m-d H:i:s',$befor_three_month_date_str);
						$queryData['end_date']=date('Y-m-d H:i:s');
						$productDetail[$value['details']['customer_id']]=$this->model_restapi_customerwebsite->getCustomerProductDetailAccordingDate($queryData);
						}
					}
			}
								$rerturn['status'] = '1';
								$rerturn['productDetail'] = array_filter($productDetail);
								echo json_encode($rerturn); exit; 
		 //"96103": "2018-06-14 16:44:02"
		exit;
		
	}

/**
	 * Function : saveImageOnStoreWebsite
	 * Request Parameters : $customer_id, $image_path
	 * Output :true or false
	 * @author Rahul 9 July 2018
	 * */
	public function saveImageOnStoreWebsite($customer_id, $image_path){
						$request['customer_id'] = $customer_id;
						$request['image_url'] = $image_path;
						$request['api_key']=CUSTOMER_WEBSITE_API_KEY;
			            	$data_json = json_encode($request);
			            	$api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/saveWebsiteStoreImage";
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
	}

	/**
	 * Method to get Customer Detail For Store Website
	 * Request Parameters : mobile
	 * @return sub-domain name and customer detail
	 * @author Rahul, 9 July 2018
	 */
    public function getCustomerDetailForStoreWebsite(){
				$this->load->model('restapi/service');
				$this->load->model('restapi/customerwebsite');
				$this->load->model('account/customer');
				if (($this->request->server['REQUEST_METHOD'] == 'POST'))
					{
					$inputJSON = file_get_contents('php://input');
					$request = json_decode($inputJSON, TRUE);
					$rt = array();
					if (isset($request['mobile']))
						{
						$customer_mobile = $request['mobile'];
						$rs = $this->model_restapi_service->getCustomerByMobile($customer_mobile);
						if(!empty($rs['customer_id']) && $rs['customer_id']!=''){
						$userId = $rs['customer_id'];
						$fields = array(
							"address_id",
							"has_website"
						);
						$customerCustomData = $this->model_account_customer->getCustomerDetails($userId, $fields);
						$customerDetail = $this->model_restapi_service->getAddressDetail($customerCustomData['address_id']);
						$subDomainName = '';

						if(!empty($request['app_version_code'])){
							$app_version_code=$request['app_version_code'];
						}else{
							$app_version_code='89';
						}

						// =======check for customer website is processing or build and return customer website required detail ===//
						$testAndGetResponseAccordingCustomer=$this->checkCustomerDetailAndReturnCustomerWesbiteDetail($customerCustomData,$customerDetail,$app_version_code);
							$rt=$testAndGetResponseAccordingCustomer;
						
						}else{

							$rt['error_code'] = '1003';
							$rt['domainName'] = '';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = $request['mobile'].' mobile number does not have user id';
							}

							///
						}
					  else
						{
						$rt['error_code'] = '1002';
						$rt['domainName'] = '';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'http request does not have user id.';

						// $this->logForTest('create_profile', $inputJSON, $rt);

						}
					}
			  else
				{
				$rt['error_code'] = '1005';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'No request found.';
				}

			// =======clientSide validation=========//

			$rt['subdomain_min_length'] = CUSTOMER_WEBSITE_SUBDOMAIN_MIN_LENGTH;
			$rt['subdomain_max_length'] = CUSTOMER_WEBSITE_SUBDOMAIN_MAX_LENGTH;
			$rt['subdomain_length_error_message'] = 'domain name must be between ' . CUSTOMER_WEBSITE_SUBDOMAIN_MIN_LENGTH . ' to ' . CUSTOMER_WEBSITE_SUBDOMAIN_MAX_LENGTH . ' characters';
			echo json_encode($rt);
			exit;
			}
	/**
	 * Method to get customer Website required data
	 * Request Parameters : $customerCustomData , $customerDetail
	 * @return get customer Website required data if Customer full fill customer-website rules.
	 * @author Rahul, 10 July 2018
	 */

			public function checkCustomerDetailAndReturnCustomerWesbiteDetail($customerCustomData,$customerDetail,$app_version_code='0'){
				if(empty($app_version_code)){
					$app_version_code = '89';
				}
				$rerturn=array();
				if ($customerCustomData['has_website'] > 0)
							{
							if ($customerCustomData['has_website'] == '1')
								{
								$rerturn['domain_name'] = CUSTOMER_WEBSITE_DOMAIN;
								$rerturn['is_check_eligibility_for_website']=false;
								$rerturn['subdomain'] = '';
								$rerturn['error_code'] = '2001';
								$rerturn['status'] = '1';
								$rerturn['status_text'] = 'Failed';
								$message = $this->not_eligble_message($app_version_code);
								}
							  else
								{
								$rerturn['domain_name'] = CUSTOMER_WEBSITE_DOMAIN;
								$rerturn['is_check_eligibility_for_website']=false;
								$rerturn['subdomain'] = '';
								$rerturn['error_code'] = '2002';
								$rerturn['status'] = '1';
								$rerturn['status_text'] = 'Failed';
								$message = $this->not_eligble_message($app_version_code);
								}

							$rerturn['message'] = $message;
							}
						  else
							{
								//=========createSubdomainAccordingCustomerDetail========//
								$subDomainName=$this->createSubdomainAccordingCustomerDetail($customerDetail);

								$sub_domain = $this->removeSpecialCharacter($subDomainName);
								$subDomainNameStr = strtolower($sub_domain);
								$lengthValidateSubDomainName = substr($subDomainNameStr, 0, 20);
								$rerturn['status'] = '1';
								$rerturn['domain_name'] = CUSTOMER_WEBSITE_DOMAIN;
								$rerturn['is_check_eligibility_for_website']=true;
								$rerturn['subdomain'] = $lengthValidateSubDomainName;
								$rerturn['status_text'] = 'Success';
								$rerturn['message'] = 'Now "' . $lengthValidateSubDomainName . '" can have it\'s website';
								$rerturn['customer_detail'] = $customerDetail;

								// $this->logForTest('create_profile', $inputJSON, $rt);

							}
							return $rerturn;
			}

	/**
	 * Method to get createSubdomainAccordingCustomerDetail
	 * Request Parameters : customerDetail
	 * @return get sub-domain name using company name and city if company is null 						
	 *		   then create sub-domain using customer first-name, last-name and city
	 * @author Rahul, 10 July 2018
	 */

	public function createSubdomainAccordingCustomerDetail($customerDetail){
		$subDomainName='';
				if (!empty($customerDetail['company']))
								{
								$subDomainName = $customerDetail['company'] . $customerDetail['city'];
								}
							  else
								{
								if (!empty($customerDetail['firstname']))
									{
									$subDomainName.= $customerDetail['firstname'];
									}

								if (!empty($customerDetail['lastname']))
									{
									$subDomainName.= $customerDetail['lastname'];
									}

								$subDomainName.= $customerDetail['city'];
								}
								//=======make domain suggestion blank=======//
								$subDomainName='';
								return $subDomainName;
	}

	/**
	 * Function : postApiandDataUsingCurlCustomerWebsite
	 * Method to post json data using curl
	 * Request Parameters : $request
	 * Output :true or false
	 * @author Rahul 9 July 2018
	 * */
	public function postApiandDataUsingCurlCustomerWebsite($request,$action){
							$request['api_key']=CUSTOMER_WEBSITE_API_KEY;
			            	$data_json = json_encode($request);
			            	$api_url = CUSTOMER_WEBSITE_DOMAIN_URL . "website/".$action;
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
	}


		/**
	 * Function : sendMailRequestCustomerWebsite
	 * Method to send request mail define user
	 * Request Parameters : customer_store_name and customer_phone_number
	 * Output :true or false
	 * @author Rahul 4 July 2018
	 * */

	public function sendMailFunction($subject,$message,$sendTo){

					$subject=$subject;
					$message=$message;
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
				        foreach($sendTo as $sendToDatail){
				        	$mail->addAddress($sendToDatail['email_id'], $sendToDatail['name']);
				        }
				        //ail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
				        $mail->Subject =  html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
				        $mail->msgHTML($message);
				        if($mail->send()){
				        	$msg = "Email has been sent.";
				        	$status = "1";
				        }else{
				        	$msg = "Email not sent. try later.";
				        	$status = "0";
				        }
				        $data=array();
						$data['msg'] = $msg;
     					$data['status'] = $status;
                        
                        echo json_encode($data);  exit;
	}


	/**
	 * Function : addProduct
	 * Method to add selected Product by customer 
	 * Request Parameters : accessToke, User ID and, product id
	 * Type : Post
	 * @author Rahul 26th July 2018
	 * Output : response accoding request product add or not
	 * */
	public function addProduct(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
    	$this->load->model('catalog/product');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$request['call_by_api'] = "add product";
			$app_version_code = $request['app_version_code'];
			$rt = array();
			$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
					if(!empty($request['product_id'])){
						$productDetail=$this->model_catalog_product->getProduct($request['product_id']);
						if($productDetail){
							$product['product_id']=$productDetail['product_id'];
							$product['product_data']['product_id']=$productDetail['product_id'];
							$product['user_id']=$request['user_id'];
							$product['product_data']['name']=$productDetail['name'];
							$product['product_data']['price']=$productDetail['selling_price'];
							$product['product_data']['sku']=$productDetail['sku'];
							$product['product_data']['image']=$productDetail['image'];
							$product['product_data']['date_added']=date('Y-m-d H:i:s');
							$product['added_by']='app';
							$product['product_images']=$this->model_restapi_service->getProductImages($productDetail['product_id']);

							$result=$this->postApiandDataUsingCurlAndGetResponse($product,'product/addProduct');
							if($result['statusCode']=='200'){
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['expiry_date'] = $result['data'];
								$rt['message'] = $result['message'];
							}else{
								$rt['error_code'] = '1006';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $result['message'];
							}
						}else{
							$rt['error_code'] = '1005';
							$rt['status'] = '0';
							$rt['status_text'] = 'Failed';
							$rt['message'] = 'product has not found.';
						}
					}
					else{
						$rt['error_code'] = '1004';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'http request does not have product id.';
					}
				}else{
					$rt=$check_access_token;
				}
		}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'No request found.';
			}
			echo json_encode($rt); exit;
	}

	/**
	 * Function : validateAccessTokenForUser
	 * Method to validate Access Token For User
	 * Request Parameters : User ID, access token
	 * Type : Post
	 * @author Rahul 26th July 2018
	 * Output : access to ken is valid or not
	 * */
	public function validateAccessTokenForUser($request){
		$this->load->model('restapi/service');
			if (!empty($request['user_id']) && !empty($request['access_token']))
				{
				$validate_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$request['access_token'], (int)$request['user_id']);
				if ($validate_access_token == '1')
					{
					$return['status'] = '1';
					$return['status_text'] = 'Success';
					}
				  else
					{
					$return['error_code'] = '1003';
					$return['status'] = '0';
					$return['status_text'] = 'Failed';
					$return['message'] = 'Invalid access token or user id.';
					}
				}
			  else
				{
				$return['error_code'] = '1001';
				$return['status'] = '0';
				$return['status_text'] = 'Failed';
				$return['message'] = 'http request does not have user id or access token.';
				}

			return $return;
	}
	/**
	 * Function : postApiandDataUsingCurlAndGetResponse
	 * Method to post json data using curl
	 * Request Parameters : $request
	 * Output :response accoding request
	 * @author Rahul 26 July 2018
	 * */
	public function postApiandDataUsingCurlAndGetResponse($request,$action){
							$request['api_key']=CUSTOMER_WEBSITE_API_KEY;
			            	$data_json = json_encode($request);
			            	$api_url = CUSTOMER_WEBSITE_DOMAIN_URL .$action;
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
	}

	/**
	 * Function : getWebsiteProduct
	 * Method to get Website Product detail according type
	 * Request Parameters : $request
	 * Output :response accoding request
	 * @author Rahul 26 July 2018
	 * */

	public function getWebsiteProduct(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
    	$this->load->model('catalog/product');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$app_version_code = $request['app_version_code'];
			$rt = array();
			$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							$product=$request;
							$result=$this->postApiandDataUsingCurlAndGetResponse($product,'product/getWebsiteProduct');
							if($result['statusCode']=='200'){
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = $result['message'];
								$rt['product'] = $result['data']['product_list'];
								$rt['count'] = $result['data']['count'];
								$rt['previous_page'] = $result['data']['previous_page'];
								$rt['next_page'] = $result['data']['next_page'];
							}else{
								$rt['error_code'] = '1006';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $result['message'];
							}
					
				}else{
					$rt=$check_access_token;
				}
		}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'No request found.';
			}

			echo json_encode($rt); exit;
	}

		/**
	 * Function : updateProductVisibiltyDuration
	 * Method to get Website Product detail according type
	 * Request Parameters : $request
	 * Output :response accoding request
	 * @author Rahul 31 July 2018
	 * */

	public function updateProductVisibiltyDuration(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
    	$this->load->model('catalog/product');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$app_version_code = $request['app_version_code'];
			$rt = array();
			$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							$product=$request;
							$result=$this->postApiandDataUsingCurlAndGetResponse($product,'website/updateProductVisibiltyDuration');
							if($result['statusCode']=='200'){
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = $result['message'];
							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $result['message'];
							}
				}else{
					$rt=$check_access_token;
				}
		}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'No request found.';
			}

			echo json_encode($rt); exit;
	}

/**
	 * Function : updateProductUsingApp
	 * Method to update Product Using App
	 * Request Parameters : product update field
	 * Output :response accoding request
	 * @author Rahul 26 July 2018
	 * */

	public function updateProductUsingApp(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
    	$this->load->model('catalog/product');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$app_version_code = $request['app_version_code'];
			$rt = array();
			$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
					if(!empty($request['product_id']) || !empty($request['fashcart_product_id'])){
							$product=$request;
							$result=$this->postApiandDataUsingCurlAndGetResponse($product,'product/updateProductUsingApp');
							if($result['statusCode']=='200'){
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['expiry_date'] = $result['data'];
								$rt['message'] = $result['message'];
							}else{
								$rt['error_code'] = '1006';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $result['message'];
							}
					}
					else{
						$rt['error_code'] = '1004';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'http request does not have product id or fashcart product id.';
					}
				}else{
					$rt=$check_access_token;
				}
		}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'No request found.';
			}
			echo json_encode($rt); exit;
	}

	/**
	 * Function : updatewebsiteData
	 * Method to update website margin or etc
	 * Request Parameters : website update field
	 * Output :response accoding request
	 * @author Rahul 26 July 2018
	 * */

	public function updatewebsiteData(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
    	$this->load->model('catalog/product');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			//$app_version_code = $request['app_version_code'];
			$rt = array();
			$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							$product=$request;
							$result=$this->postApiandDataUsingCurlAndGetResponse($product,'website/updatewebsiteData');
							if($result['statusCode']=='200'){
								$rt['status'] = '1';
								$rt['status_text'] = 'Success';
								$rt['message'] = $result['message'];
							}else{
								$rt['error_code'] = '1006';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = $result['message'];
							}
					
				}else{
					$rt=$check_access_token;
				}
		}else{
				$rt['error_code'] = '1002';
				$rt['status'] = '0';
				$rt['status_text'] = 'Failed';
				$rt['message'] = 'No request found.';
			}
			echo json_encode($rt); exit;
	}


	/**
	 * Function : getProductStockStatus
	 * Method to get multiple Product Stock Status
	 * Request Parameters : product_id (array)
	 * Output :response product stock_status with product id
	 * @author Rahul 31 July 2018
	 * */

public function getProductStockStatus(){
    	$this->load->model('catalog/product');
    	$this->load->model('restapi/customerwebsite');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
				foreach ($request['product_data'] as $key => $value) {
					$t_stock= $this->model_restapi_customerwebsite->getProductStockStatusValue($value['wsb_product_id']);
					         if ($t_stock == false) {
					            $stock_status = '0';
					          }else{
					          	$stock_status = '1';
					          }
					        $request['product_data'][$key]['stock_status'] = $stock_status;
				}
					$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['request'] = $request;
			}else{
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
			echo json_encode($rt); exit;
}
	/**
	 * Function : addCustomProduct
	 * Method to add custom Product by customer 
	 * Request Parameters : accessToke, User ID and, produt detail in bulk
	 * image in array name etc.
	 * Type : Post
	 * @author Rahul 9th August 2018
	 * Output : response accoding request product add or not
	 * */
	public function addCustomProduct(){
		$this->load->model('restapi/service');
    	$this->load->model('restapi/customerwebsite');
    	$this->load->model('account/customer');
    	if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$request['user_id'] =$this->request->post['user_id'];
				$request['access_token'] =$this->request->post['access_token'];
				$check_access_token=$this->validateAccessTokenForUser($request);
				$directory='store_product_image';
				$file=array();
				if($check_access_token['status']=='1'){
					$k='1';
					$fileType='product_images';
					if(!empty($this->request->files['product_images']['name'])){
					foreach ($this->request->files['product_images']['name'] as $key => $product_name) {
						foreach ($_FILES['product_images']['name'][$key] as $image_key => $value) {
								$file_name_with_full_path = $_FILES[$fileType]['tmp_name'][$key][$image_key];
								$values=$value;
				                        $dt = new DateTime();
				                        $ext = pathinfo($values, PATHINFO_EXTENSION);
				                        // Sanitize the filename
				                        $values   = "product_image_".$dt->format('Y_m_d_H_i_s')."_".$k."_".uniqid(rand()).".".$ext;
				                        $filename = basename(html_entity_decode($values, ENT_QUOTES, 'UTF-8'));
					            		$product_image[$key][$image_key]['image']=$directory."/".$filename;
					                if(is_uploaded_file($_FILES[$fileType]['tmp_name'][$key][$image_key])){
					                    if (function_exists('curl_file_create')) { // php 5.5+
					                        $cFile = curl_file_create($file_name_with_full_path,'',$filename);
					                    } else { // 
					                        $cFile = '@' . realpath($file_name_with_full_path);
					                    }
					                }
					                $file[$k]=$cFile;
					                $k++;
							}

						}
					}
					//======upload_multiple_image_process=========//
					if(!empty($file)){
					$upload_image_result=$this->uploadMultipleImages($directory,$file);
					//$upload_image_result=true;
					}
					if(empty($file) || $upload_image_result){
							$form_data=$this->request->post;
							$form_data['file']=$product_image;
							$result=$this->postApiandDataUsingCurlAndGetResponse($form_data,'product/addCustomProduct');
								if($result['statusCode']=='200'){
									$rt['status'] = '1';
									$rt['status_text'] = 'Success';
									$rt['fashcart_product_ids'] = $result['data']['fashcart_product_id'];
									$rt['image_file_name_array'] = $result['data']['image_file_name_array'];
									$rt['image_url_array'] = $result['data']['image_url_array'];
									$rt['message'] = $result['message'];
								}else{
									$rt['error_code'] = '1006';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = $result['message'];
								}	
					}else{
						$rt['error_code'] = '1010';
						$rt['status'] = '0';
						$rt['status_text'] = 'Failed';
						$rt['message'] = 'Images not upload on server please try again.';
					}
					
				}
				else{
					$rt=$check_access_token;
				}
				
    		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
    	}

	/**
	 * Function : addProductImage
	 * Method to add Product image or update image if old image pass 
	 * Request Parameters : accessToke, User ID and, product id,fashcart_product_id
	 * new_image, old_image(optional)
	 * Type : Post
	 * @author Rahul 9th August 2018
	 * Output : response accoding request image add and update or not
	 * */

	public function addProductImage(){
    	
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$request['user_id'] =$this->request->post['user_id'];
				$request['access_token'] =$this->request->post['access_token'];
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
				  $directory = 'store_product_image';
			            if(!empty($_FILES['new_image']['name']) && !empty($this->request->post['fashcart_product_id'])){
			            	$rt = array();
				            $values=$_FILES['new_image']['name'];
	                        $dt = new DateTime();
	                        $ext = pathinfo($values, PATHINFO_EXTENSION);
	                        // Sanitize the filename
	                        $values   = "product_image_".$dt->format('Y_m_d_H_i_s')."_".uniqid(rand()).".".$ext;
	                        $filename = basename(html_entity_decode($values, ENT_QUOTES, 'UTF-8'));
							$upload_image_response=$this->uploadImages($directory,$filename,'new_image');
							//$upload_image_response = true;
							if($upload_image_response){
								$image_path= $directory."/".$filename;
								$product['fashcart_product_id']=$this->request->post['fashcart_product_id'];
								$product['image_path']=$image_path;
								if(!empty($this->request->post['old_image_path'])){
								$product['old_image_path']=$this->request->post['old_image_path'];	
								}
								$result=$this->postApiandDataUsingCurlAndGetResponse($product,'product/saveProductImage');
								if($result['statusCode']=='200'){
									$image_url=$this->model_tool_image->getOriginalImage($image_path);
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
										$rt['image'] = $image_path;
										$rt['image_url'] = $image_url;
										if(!empty($this->request->post['old_image_path']) && $this->request->post['product_id']=='0'){
											$file_path=$this->request->post['old_image_path'];
											$path  = $file_path;
											$deleteImageResponse=$this->deleteImageOnCdnServer($path);
										}
								}else{
									$rt['error_code'] = '1002';
									$rt['image'] = '';
									$rt['image_url'] = '';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Image not save on store Website.';

								}
								

							}else{
								$rt['error_code'] = '1001';
								$rt['image'] = '';
								$rt['image_url'] = '';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Image not save.';
							}
							 
			}
			}else{
					$rt=$check_access_token;
				}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}


	/**
	 * Function : removeProductImage
	 * Method to remove Product image  
	 * Request Parameters : accessToke, User ID and, product id,
	 * fashcart_product_id, rmove image path
	 * Type : Post
	 * @author Rahul 9th August 2018
	 * Output : response accoding request image remove or not
	 * */

	public function removeProductImage(){
    	
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
								$image_path= $request['image_path'];
								$product['fashcart_product_id']=$request['fashcart_product_id'];
								$product['image_path']=$image_path;
								$result=$this->postApiandDataUsingCurlAndGetResponse($product,'product/removeProductImage');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
										if($request['product_id']=='0'){
											$file_path=$request['image_path'];
											$path  = $file_path;
											$deleteImageResponse=$this->deleteImageOnCdnServer($path);
										}
								}else{
									$rt['error_code'] = '1002';
									$rt['image'] = '';
									$rt['image_url'] = '';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Image not save on store Website.';

								}
			}else{
					$rt=$check_access_token;
				}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	/**
	 * Function : saveCustomerContacts
	 * Method to save Customer Contacts  
	 * Request Parameters : accessToke, User ID, contact detail(name,number)
	 * Type : Post
	 * @author Rahul 31 August 2018
	 * Output : response accoding request contact save or not
	 **/

	public function saveCustomerContacts(){
    	
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							if(!empty($request['contact_list'])){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/saveCustomerContacts');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Contact not save on store Website.';
								}

							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Contact List is empty';
							}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	/**
	 * Function : deleteCustomerContact
	 * Method to delete Customer Contacts  
	 * Request Parameters : accessToke, User ID, contact number
	 * Type : Post
	 * @author Rahul 31 August 2018
	 * Output : response accoding request contact delete or not
	 **/

	public function deleteCustomerContact(){
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							if(!empty($request['contact_number'])){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/deleteCustomerContact');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Contact not remove on store Website.';
								}

							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Contact List is empty';
							}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	/**
	 * Function : saveCustomerSendSmsDetail
	 * Method to save Customer Contacts  
	 * Request Parameters : accessToke, User ID, contact number
	 * Type : Post
	 * @author Rahul 4 September 2018
	 * Output : response accoding request contact save or not
	 **/

	public function saveCustomerSendSmsDetail(){
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							if(!empty($request['contact_list'])){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/saveCustomerSendSmsDetail');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'Contact SMS detail not save on store Website.';
								}

							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Contact SMS detail List is empty';
							}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	/**
	 * Function : updateContacts
	 * Method to save Customer Contacts  
	 * Request Parameters : accessToke, User ID, contact detail(name,number),type=add,remove
	 * Type : Post
	 * @author Rahul 5 September 2018
	 * Output : response accoding request contact save or not
	 **/

	public function updateContacts(){
    	$product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
					if(!empty($request['contacts_to_add']) || !empty($request['contacts_to_remove'])){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/updateContacts');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = $result['message'];
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'There is some error please try after some time!';
								}
							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'Contact List is empty';
							}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

		/**
	 * Function : getContactsToSendSms
	 * Method to get Contacts To SendSms  
	 * Request Parameters : accessToke, User ID, send_sms_by_user(to send sms by user)
	 * Type : Post
	 * @author Rahul 7 September 2018
	 * Output : response accoding request contact save or not
	 **/

	public function getContactsToSendSms(){
    	$product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/getContactsToSendSms');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = $result['message'];
										$rt['list'] = $result['data']['list'];
										$rt['today_limit'] = $result['data']['limit'];
										$rt['sms_template'] = $result['data']['sms_template'];
										$full_url = ' https://'.$result['data']['subdomain'].'.'.CUSTOMER_WEBSITE_DOMAIN;
										$rt['sms_footer'] = $full_url;
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'There is some error please try after some time!';
								}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}
	/**
	 * Function : getCampaignStatusAccordingDevice
	 * Method to get Campaign Status According Device 
	 * Request Parameters : accessToke, User ID, device_id
	 * Type : Post
	 * @author Rahul 7 September 2018
	 * Output : response accoding request contact save or not
	 **/

	public function getCampaignStatusAccordingDevice(){
    	$product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/getCampaignStatusAccordingDevice');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['daily_sms_limit']=$result['data']['daily_sms_limit'];
										$rt['status_text'] = $result['message'];
										$rt['is_auto_sms_campaign_active']=$result['data']['is_auto_sms_campaign_active'];
								}else{
									$rt['status'] = '1';
									$rt['daily_sms_limit']='0';
									$rt['message'] = 'Campaining status off';
									$rt['is_auto_sms_campaign_active']='0';
								}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	/**
	 * Function : setCampaignStatusAccordingDevice
	 * Method to save SMS Campaign Status  
	 * Request Parameters : accessToke, User ID, device detail
	 * Type : Post
	 * @author Rahul 7 September 2018
	 * Output : response accoding request SMS campaign save or not
	 **/

	public function setCampaignStatusAccordingDevice(){
    	 $product_lists=array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
							if(!empty($request['device_id'])){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/setCampaignStatusAccordingDevice');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
									$rt['message'] = 'SMS campaign detail not save on store Website.';
								}

							}else{
								$rt['error_code'] = '1004';
								$rt['status'] = '0';
								$rt['status_text'] = 'Failed';
								$rt['message'] = 'SMS campaign detail List is empty';
							}
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	

	/**
	 * Function : getCartProductList
	 * Method to get Cart Product List  
	 * Request Parameters : User ID
	 * Type : Post
	 * @author Rahul 27th August 2018
	 * Output : response get Cart Product List according user
	 * */

	public function getCartProductList(){
		$this->load->model('restapi/service');
		$this->load->model('restapi/cartservice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{ 
				//$request['user_id']= '96103';
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$request_user_id['user_id']=$request['user_id'];
    	 		$cart_product = $this->model_restapi_cartservice->cart_data($request_user_id);
    	 	
    	 		if($cart_product['status'] && !empty($cart_product['data'])){
    	 			$product_list=array();
    	 			foreach ($cart_product['data'] as $key => $value) {
    	 				if(!empty($value['available_stock']) && $value['stock_quantity'] > '0'){
    	 						$product_list[$key]=$value['product_details'];
    	 				}
    	 			}
    	 			$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['product_list'] = $product_list;
    	 		}else{
    	 			$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';	
    	 		}
    	 	}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); 
				exit;
	}

		/**
	 * Function : getLeadsDetail
	 * Method to get Leads Detail  
	 * Request Parameters : User ID
	 * Type : Post
	 * @author Rahul 10th October 2018
	 * Output : response get Leads List according user
	 * */

	public function getLeadsDetail(){
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				$check_access_token=$this->validateAccessTokenForUser($request);
				if($check_access_token['status']=='1'){
								$result=$this->postApiandDataUsingCurlAndGetResponse($request,'website/getLeadsDetail');
								if($result['statusCode']=='200'){
										$rt['status'] = '1';
										$rt['status_text'] = 'Success';
										$rt['leads'] = $result['data']['customer_leads_list'];
										$rt['previous_page'] = $result['data']['previous_page'];
										$rt['next_page'] = $result['data']['next_page'];
								}else{
									$rt['error_code'] = '1003';
									$rt['status'] = '0';
									$rt['status_text'] = 'Failed';
								}
			
			}else{
					$rt=$check_access_token;
							}
		}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt); exit;
	}

	
	/**
	 * Function : getWishlistProductList
	 * Method to get Wishlist Product List 
	 * Request Parameters : User ID
	 * Type : Post
	 * @author Rahul 27th August 2018
	 * Output : response get Wishlist Product List according user
	 * */

	public function getWishlistProductList(){
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			{
				$inputJSON = file_get_contents('php://input');
				$request = json_decode($inputJSON, TRUE);
				//parameter  customer_id start limit
    	 		$short_list_product = $this->model_restapi_service->getWishlist($request);
    	 		if(!empty($short_list_product['products'])){
    	 			foreach ($short_list_product['products'] as $key => $value) {
    	 				if($value['stock_status_id']=='5' || $value['quantity']=='0'){ //quantity $value['quantity']=='0'
    	 					unset($short_list_product['products'][$key]);
    	 				}
    	 			}
    	 			$product_list=array();
    	 			$rt['status'] = '1';
					$rt['status_text'] = 'Success';
					$rt['product_list'] = $short_list_product['products'];
    	 		}else{
    	 			$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';	
    	 		}
    	 	}else{ 
					$rt['error_code'] = '1002';
					$rt['status'] = '0';
					$rt['status_text'] = 'Failed';
					$rt['message'] = 'No request found.';
				}
				echo json_encode($rt);
				exit;
	}

	/**
	 * Function : getCustomerWebsiteUrl
	 * Method to get Customer Website Url  
	 * Request Parameters : User ID
	 * Type : Post
	 * @author Rahul 17th Octuber 2018
	 * Output : response Customer website URL
	 * */
		public function getCustomerWebsiteUrl() {
		    if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		        $inputJSON = file_get_contents('php://input');
		        $request = json_decode($inputJSON, TRUE);
		        $check_access_token = $this->validateAccessTokenForUser($request);
		       
		        if ($check_access_token['status'] == '1' || trim($request['type'])=='CRM') {
		            $result = $this->postApiandDataUsingCurlAndGetResponse($request, 'website/getCustomerSubdomain');
		            if ($result['statusCode'] == '200') {
		            	foreach ($result['data'] as $key => $value) {
		            		$urls[$key]='https://'.$value.'.'.CUSTOMER_WEBSITE_DOMAIN;
		            	}
		                $rt['status'] = '1';
		                $rt['full_url'] = $urls;
		                $rt['status_text'] = 'Success';
		            } else {
		                $rt['error_code'] = '1003';
		                $rt['status'] = '0';
		                $rt['status_text'] = 'Failed';
		                $rt['message'] = $result['message'];
		            }
		        } else {

		            $rt = $check_access_token;
		        }
		    } else {
		        $rt['error_code'] = '1002';
		        $rt['status'] = '0';
		        $rt['status_text'] = 'Failed';
		        $rt['message'] = 'No request found.';
		    }
		    echo json_encode($rt);
		    exit;
		}

}