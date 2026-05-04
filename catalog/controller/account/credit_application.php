<?php

class ControllerAccountCreditApplication extends Controller
{
    private $error = array();

        public function index()
    {
        /**** Check CRM User *****/

        // default

        if (isset($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] == 0) {
            echo 'Invalid CRM User';
            exit;
        }

        if (!empty($this->request->get['crm_user_id']) && !empty($this->request->get['crm_user_password'])) {

            // default

            $authorise_crm_user = false;
            $crm_user_id = $this->request->get['crm_user_id'];
            $crm_user_password = $this->request->get['crm_user_password'];
            $customer_id = $this->request->get['customer_id'];
            $authorise_crm_user = $this->authenticationCrmUser($crm_user_id, $crm_user_password);
            if ($authorise_crm_user == false) {
                echo 'Invalid CRM User';
                exit;
            }
        } /**** End check CRM User *****/
        else {
            /**** check Customer *****/
            if (!empty($this->request->get['customer_id']) && !empty($this->request->get['token'])) {
                $customer_id = $this->request->get['customer_id'];
                $token = $this->request->get['token'];
                $this->load->model('account/customer');
                if (CONFIG_IS_MOBILE == 0) {
                    $get_user_info = $this->model_account_customer->getCustomerByTokenAndId($token, $customer_id);
                    if ($get_user_info['customer_id'] == $customer_id) {

                        // do nothing

                    } else {
                        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                    }
                } else {
                    $get_user_info = $this->model_account_customer->getCustomerByTokenAndIdFromAPP($token, $customer_id);
                    if ($get_user_info['customer_id'] == $customer_id) {

                        // override login

                        $this->customer->login($get_user_info['customer_id'], '', true);
                    } else {
                        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                    }
                }
            } else
                if (!empty($this->request->get['ctoken']) && empty($this->request->get['khufiya_user_id'])) {
                    $customer_id = $this->customer->getId();

                    // if ctoken is not valid

                    if (empty($customer_id)) {
                        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                    }
                } else {
                    if (empty($this->request->get['khufiya_user_id'])) {
                        if ((CREDIT_APPLICATION_VERSION == '1')) {
                            $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                        }else{
                            $customer_id = $this->customer->getId();
                            if (empty($customer_id)) {
                                $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                            }else{
                                if(isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != $customer_id){
                                    $this->response->redirect($this->url->link('account/credit_application', '', 'SSL'));
                                }
                            }

                        }
                    } else
                        if (isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != '') {
                            $customer_id = $this->request->get['customer_id'];
                        }
                }

            /**** End check Customer *****/
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $data = array();
        $this->load->model('account/credit_application');

        // $customer_id = $this->customer->getId();

        if (!empty($customer_id)) {
            $data['customer_id'] = $customer_id;
        } else {
            if (!empty($this->request->get['customer_id'])) {
                $data['customer_id'] = $customer_id = $this->request->get['customer_id'];
            } else {
                $data['customer_id'] = '0';
            }
        }

        if (!empty($this->request->get['khufiya_user_id']) && isset($this->request->get['customer_id']) && $this->request->get['customer_id'] == '0') {
            $data['customer_id'] = '0';
        }

        $version = '0';
        if (!empty($customer_id)) {
            $exits_credit_application_version = $this->model_account_credit_application->isExitCustomerCreditApplicationVersion($customer_id);
            if ($exits_credit_application_version['status'] == TRUE) {
                $version = $exits_credit_application_version['version'];
            }
        } else
            if (!empty($this->request->get['khufiya_user_id']) && !empty($this->request->get['credit_application_id'])) {
                $exits_credit_application_version = $this->model_account_credit_application->isExitCustomerCreditApplicationVersionKhufiya($this->request->get['credit_application_id']);
                if ($exits_credit_application_version['status'] == TRUE) {
                    $version = $exits_credit_application_version['version'];
                }
            }

        //===================== LANGUGE START ====================//
        // vernacular language for app
        if (!empty($this->request->get['language'])) {
            $data['app_language'] = $this->request->get['language'];
            $this->session->data['app_language'] = $data['app_language'];
            $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
        } else
            if (!empty($this->session->data['app_language'])) {
                $data['app_language'] = $this->session->data['app_language'];
                $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
            } else {
                $data['app_language'] = $this->config->get('config_language_id');
            }
        //===================== LANGUGE END ====================//

            // =============Version 3 credit application form start==================//

            if (!empty($this->customer->getId()) && empty($customer_id) && empty($this->request->get['khufiya_user_id'])) {
                $customer_id = $this->customer->getId();
            }
            if (!empty($customer_id)) {
                $response = $this->credit_application_v3($customer_id);
            } else {
                $response = $this->credit_application_v3();
            }
            $data = $response;
            $createUrlSrting = $this->createQueryString($data);
            if (empty($data['mode']) && empty($this->request->get['khufiya_user_id'])) {
                if (!empty($data['customer_id'])) {
                    if (!empty($data['draft']) && $data['draft'] == '1') {
                        $request['success_type'] = '1';
                        $createUrlSrting .= '&form_type=2';
                        $createUrlSrting .= '&success_type=1';
                        $this->response->redirect($this->url->link('account/credit_application/success' . $createUrlSrting, '', 'SSL'));
                        exit;

                    } else if (!empty($data['draft']) && $data['draft'] == '2') {
                        $createUrlSrting .= '&form_type=2';
                        $createUrlSrting .= '&success_type=2';
                        $this->response->redirect($this->url->link('account/credit_application/success' . $createUrlSrting, '', 'SSL'));
                        exit;

                    }
                }

            }
            // =============Version 3 credit application form End==================//


        $request_by_app = 0;
        $headers = getallheaders();
        if (!empty($headers['REQUEST_BY'])) {
            $request_by = strtolower($headers['REQUEST_BY']);
            if ((strpos($request_by, 'android') !== false) || (strpos($request_by, 'ios') !== false) || (strpos($request_by, 'crm') !== false) || (strpos($request_by, 'app') !== false)) {
                $request_by_app = 1;
            }
        } else
            if (!empty($headers['user_id'])) { // For CRM APP
                $request_by_app = 1;
            } else {
                $request_by_app = 0;
            }

        $data['request_by_app'] = $request_by_app;
        $header_language = array();
        $footer_language = array();
        $login_language = array();
        $this->load->autoLoadLanguage('common/header', $header_language);
        $this->load->autoLoadLanguage('common/footer', $footer_language);
        $this->load->autoLoadLanguage('account/login', $login_language);
        $data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language'] = json_encode($login_language);
        $credit_language = array();
        $this->load->autoLoadLanguage('account/credit_application', $credit_language);
        $data['credit_language'] = $credit_language;

        // ----end code---

        $data['international_store'] = 0;
        $data['column_right'] = $this->load->controller('common/column_right');
            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_application_v3.tpl';


        if (file_exists($file_path)) {
            $this->response->setOutput($this->load->view($file_path, $data, true));
        } else {
            $this->response->redirect($this->url->link('common/home'));
        }
    }

    public function old_index_backup()
    {
        /**** Check CRM User *****/

        // default

        if (isset($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] == 0) {
            echo 'Invalid CRM User';
            exit;
        }

        if (!empty($this->request->get['crm_user_id']) && !empty($this->request->get['crm_user_password'])) {

            // default

            $authorise_crm_user = false;
            $crm_user_id = $this->request->get['crm_user_id'];
            $crm_user_password = $this->request->get['crm_user_password'];
            $customer_id = $this->request->get['customer_id'];
            $authorise_crm_user = $this->authenticationCrmUser($crm_user_id, $crm_user_password);
            if ($authorise_crm_user == false) {
                echo 'Invalid CRM User';
                exit;
            }
        } /**** End check CRM User *****/
        else {
            /**** check Customer *****/
            if (!empty($this->request->get['customer_id']) && !empty($this->request->get['token'])) {
                $customer_id = $this->request->get['customer_id'];
                $token = $this->request->get['token'];
                $this->load->model('account/customer');
                if (CONFIG_IS_MOBILE == 0) {
                    $get_user_info = $this->model_account_customer->getCustomerByTokenAndId($token, $customer_id);
                    if ($get_user_info['customer_id'] == $customer_id) {

                        // do nothing

                    } else {
                        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                    }
                } else {
                    $get_user_info = $this->model_account_customer->getCustomerByTokenAndIdFromAPP($token, $customer_id);
                    if ($get_user_info['customer_id'] == $customer_id) {

                        // override login

                        $this->customer->login($get_user_info['customer_id'], '', true);
                    } else {
                        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                    }
                }
            } else
                if (!empty($this->request->get['ctoken']) && empty($this->request->get['khufiya_user_id'])) {
                    $customer_id = $this->customer->getId();

                    // if ctoken is not valid

                    if (empty($customer_id)) {
                        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                    }
                } else {
                    if (empty($this->request->get['khufiya_user_id'])) {
                        if ((CREDIT_APPLICATION_VERSION == '1')) {
                            $this->response->redirect($this->url->link('account/login', '', 'SSL'));
                        }
                    } else
                        if (isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != '') {
                            $customer_id = $this->request->get['customer_id'];
                        }
                }

            /**** End check Customer *****/
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $data = array();
        $this->load->model('account/credit_application');

        // $customer_id = $this->customer->getId();

        if (!empty($customer_id)) {
            $data['customer_id'] = $customer_id;
        } else {
            if (!empty($this->request->get['customer_id'])) {
                $data['customer_id'] = $customer_id = $this->request->get['customer_id'];
            } else {
                $data['customer_id'] = '0';
            }
        }

        if (!empty($this->request->get['khufiya_user_id']) && isset($this->request->get['customer_id']) && $this->request->get['customer_id'] == '0') {
            $data['customer_id'] = '0';
        }

        $version = '0';
        if (!empty($customer_id)) {
            $exits_credit_application_version = $this->model_account_credit_application->isExitCustomerCreditApplicationVersion($customer_id);
            if ($exits_credit_application_version['status'] == TRUE) {
                $version = $exits_credit_application_version['version'];
            }
        } else
            if (!empty($this->request->get['khufiya_user_id']) && !empty($this->request->get['credit_application_id'])) {
                $exits_credit_application_version = $this->model_account_credit_application->isExitCustomerCreditApplicationVersionKhufiya($this->request->get['credit_application_id']);
                if ($exits_credit_application_version['status'] == TRUE) {
                    $version = $exits_credit_application_version['version'];
                }
            }

        //===================== LANGUGE START ====================//
        // vernacular language for app
        if (!empty($this->request->get['language'])) {
            $data['app_language'] = $this->request->get['language'];
            $this->session->data['app_language'] = $data['app_language'];
            $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
        } else
            if (!empty($this->session->data['app_language'])) {
                $data['app_language'] = $this->session->data['app_language'];
                $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
            } else {
                $data['app_language'] = $this->config->get('config_language_id');
            }
        //===================== LANGUGE END ====================//

        // =============Previous credit application form start================//

        if ((CREDIT_APPLICATION_VERSION == '1' && empty($this->request->get['khufiya_user_id'])) || (!empty($this->request->get['khufiya_user_id']) && !empty($exits_credit_application_version['status']) && $exits_credit_application_version['status'] == '1' && $version == '1')) {

            // set image validation condition

            $data['required_pancard'] = '1';
            $data['required_aadhaar_card'] = '1';
            $data['required_photo'] = '1';
            $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationId($customer_id);
            if ($exits_credit_application['status'] == false) {

                // first get customer information

                $this->load->model('account/customer');
                $customer_data = $this->model_account_customer->getCustomer($customer_id);

                // set default draft

                $data['draft'] = '0';
                $data['first_name'] = $customer_data['firstname'];
                $data['last_name'] = $customer_data['lastname'];
                $data['email'] = $customer_data['email'];
                $data['phone_no'] = $customer_data['telephone'];
                $data['gst_number'] = $customer_data['gst_number'];

                // get customer address

                $this->load->model('account/address');
                $customer_address_data = $this->model_account_address->getAddress($customer_data['address_id']);
                $data['current_address'] = $customer_address_data['address_1'] . ' ' . $customer_address_data['address_2'];
                $data['current_pincode'] = $customer_address_data['postcode'];
                $data['current_city'] = $customer_address_data['city'];
            } else {

                // get customer_application_detail

                $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id);
                $data = $credit_application_data;

                // set image validation condition
                // For application document

                $data['required_pancard'] = '1';
                $data['required_aadhaar_card'] = '1';
                $data['required_photo'] = '1';
                if (!empty($credit_application_data['documents']) && count($credit_application_data['documents']) > 0) {
                    if (!empty($credit_application_data['documents']['application_document']) && count($credit_application_data['documents']['application_document']) > 0) {

                        // recreate bussiness document array

                        $application_document_result = array();
                        $application_document = $credit_application_data['documents']['application_document'];
                        $application_document_type = $this->uniqueMultiDimensionalArray($application_document, 'name');
                        $application_document_type = array_unique(array_column($application_document_type, 'name'));
                        foreach ($application_document_type as $key => $doc_type) {
                            foreach ($application_document as $k => $val) {
                                if ($val['name'] == $doc_type) {
                                    $application_document_result[$doc_type][$k]['id'] = $val['id'];
                                    $application_document_result[$doc_type][$k]['path'] = $val['file_path'];
                                    if ($val['document_number'] != '') {
                                        $application_document_result[$doc_type][$k]['document_number'] = $val['document_number'];
                                    }

                                    if ($val['expiry_date'] != '') {
                                        $application_document_result[$doc_type][$k]['expiry_date'] = $val['expiry_date'];
                                    }
                                }
                            }
                        }

                        $data['application_document'] = $application_document_result;

                        // check required

                        $application_req_doc = array();
                        $application_req_doc = $application_document_type;
                        if (count($application_req_doc) > 0) {
                            foreach ($application_req_doc as $key => $app_req_doc) {
                                $data['required_' . $app_req_doc] = '0';
                            }
                        }
                    }
                }

                // For Bussiness document

                $data['required_mandatory_document'] = '1';
                if (!empty($credit_application_data['documents']) && count($credit_application_data['documents']) > 0) {
                    if (!empty($credit_application_data['documents']['bussiness_document']) && count($credit_application_data['documents']['bussiness_document']) > 0) {

                        // recreate bussiness document array

                        $bussiness_document_result = array();
                        $bussiness_document = $credit_application_data['documents']['bussiness_document'];
                        $bussiness_document_type = $this->uniqueMultiDimensionalArray($bussiness_document, 'name');
                        $bussiness_document_type = array_unique(array_column($bussiness_document_type, 'name'));
                        foreach ($bussiness_document_type as $key => $doc_type) {
                            foreach ($bussiness_document as $k => $val) {
                                if ($val['name'] == $doc_type) {
                                    $bussiness_document_result[$doc_type][$k]['id'] = $val['id'];
                                    $bussiness_document_result[$doc_type][$k]['path'] = $val['file_path'];
                                }
                            }
                        }

                        $data['bussiness_document'] = $bussiness_document_result;
                        $bussiness_req_doc = array();
                        $bussiness_req_doc = $bussiness_document_type;
                        $mandatory_document = array();
                        $mandatory_document[0] = 'electricity_bill';
                        $mandatory_document[1] = 'phone_landline_bill';
                        $mandatory_document[2] = 'registered_leave_license_agreement';
                        $mandatory_document[3] = 'maintenance_receipt';
                        $mandatory_document[4] = 'rental_agreement';

                        // check required

                        if (count($bussiness_req_doc) > 0) {
                            foreach ($bussiness_req_doc as $key => $buss_req_doc) {
                                if (in_array($buss_req_doc, $mandatory_document)) {
                                    $data['required_mandatory_document'] = '0';
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($this->request->get['draft']) && $this->request->get['draft'] != '') {
                $data['draft'] = $this->request->get['draft'];
            } else
                if ($data['draft'] == 4) {
                    $data['draft'] = '0';
                }

            if (isset($this->request->get['mode'])) {
                $data['draft'] = '0';
            }

            if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '' && $this->request->get['crm_user_id'] != '0') {
                $data['crm_user_id'] = $this->request->get['crm_user_id'];
            } else {
                $data['crm_user_id'] = '';
            }

            if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '' && $this->request->get['khufiya_user_id'] != '0') {
                $data['khufiya_user_id'] = $this->request->get['khufiya_user_id'];
            } else {
                $data['khufiya_user_id'] = '';
            }

            // get error's

            if (!empty($this->session->data['validation_error']) && $this->session->data['validation_error'] != '') {
                $data['validation_error'] = $this->session->data['validation_error'];
            } else {
                $data['validation_error'] = '';
            }

            if (!empty($this->request->get['ctoken']) && $this->request->get['ctoken'] != '') {
                $data['ctoken'] = $this->request->get['ctoken'];
            } else {
                $data['ctoken'] = '';
            }

            $query_string = '';
            if (isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != '') {
                $query_string .= '&customer_id=' . $this->request->get['customer_id'];
            } else
                if (!empty($this->request->get['customer_id']) && $this->request->get['ctoken'] != '') {
                    $query_string .= '&customer_id=' . $this->customer->getId();
                }

            if (!empty($this->request->get['token']) && $this->request->get['token'] != '') {
                $query_string .= '&token=' . $this->request->get['token'];
            }

            if (!empty($this->request->get['ctoken']) && $this->request->get['ctoken'] != '') {
                $query_string .= '&ctoken=' . $this->request->get['ctoken'];
            }

            if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '') {
                $query_string .= '&crm_user_id=' . $this->request->get['crm_user_id'];
            }

            if (!empty($this->request->get['crm_user_password']) && $this->request->get['crm_user_password'] != '') {
                $query_string .= '&crm_user_password=' . $this->request->get['crm_user_password'];
            }

            if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '') {
                $query_string .= '&khufiya_user_id=' . $this->request->get['khufiya_user_id'];
            }

            if (!empty($this->request->get['credit_application_id']) && $this->request->get['credit_application_id'] != '') {
                $query_string .= '&credit_application_id=' . $this->request->get['credit_application_id'];
            }

            if (!empty($this->request->get['source'])) {
                $query_string .= '&source=' . $this->request->get['source'];
            }

            // create full name

            $data['name'] = '';
            if (!empty($data['first_name'])) {
                $data['name'] = $data['first_name'];
            }

            if (!empty($data['middle_name'])) {
                $data['name'] .= ' ' . $data['middle_name'];
            }

            if (!empty($data['last_name'])) {
                $data['name'] .= ' ' . $data['last_name'];
            }

            // explode residing_date

            if (!empty($data['residing_date'])) {
                $explode_residing_date = explode('-', $data['residing_date']);
                $data['residing_date_year'] = $explode_residing_date[0];
                $data['residing_date_month'] = isset($explode_residing_date[1]) ? $explode_residing_date[1] : '';
            }

            if (!empty($data['occupied_since'])) {
                $explode_occupied_date = explode('-', $data['occupied_since']);
                $data['occupied_since_year'] = $explode_occupied_date[0];
                $data['occupied_since_month'] = isset($explode_occupied_date[1]) ? $explode_occupied_date[1] : '';
            }

            if (!empty($data['business_since'])) {
                $explode_business_date = explode('-', $data['business_since']);
                $data['business_since_year'] = $explode_business_date[0];
                $data['business_since_month'] = isset($explode_business_date[1]) ? $explode_business_date[1] : '';
            }

            if (!empty($data['permanent_residing_date'])) {
                $explode_permanent_date = explode('-', $data['permanent_residing_date']);
                $data['permanent_date_year'] = $explode_permanent_date[0];
                $data['permanent_date_month'] = isset($explode_permanent_date[1]) ? $explode_permanent_date[1] : '';
            }

            $data['action'] = $this->url->link('account/credit_application/saveForm' . $query_string, '', 'SSL');
            if (empty($this->request->get['customer_id'])) {
                $data['home'] = $this->url->link('common/home', '', 'SSL');
            } else {
                $data['edit'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
            }

            $data['ajax_upload'] = $this->url->link('account/credit_application/uploadDocument' . $query_string, '', 'SSL');

            // =============Previous credit application form End================//

        } else if ((CREDIT_APPLICATION_VERSION == '2' && empty($this->request->get['khufiya_user_id'])) || (!empty($this->request->get['khufiya_user_id']) && !empty($exits_credit_application_version['status']) && $exits_credit_application_version['status'] == '2' && $version == '2')) {

            // =============New credit application form start================//

            $exits_credit_application = array();
            if (!empty($customer_id)) {
                $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationId($customer_id);
            } else
                if (empty($customer_id) && !empty($this->request->get['credit_application_id'])) {
                    $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationIdKhufiya($this->request->get['credit_application_id']);
                }

            // add default customer data in credit application table

            if (!empty($customer_id) && $exits_credit_application['credit_application_id'] == 0) {
                $this->request->get['credit_application_id'] = $this->model_account_credit_application->addDefaultDataInCreditApplication($customer_id);
                $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationIdKhufiya($this->request->get['credit_application_id']);
            }

            // end code

            if (!empty($exits_credit_application['status']) && $exits_credit_application['status'] == TRUE) {
                if (!empty($customer_id)) {
                    $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id);
                } else {
                    $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id, $this->request->get['credit_application_id']);
                }

                $data = $exits_credit_application['credit_application_data'];
                $data['documents'] = $credit_application_data['documents'] ?? '';
                $data['customer_id'] = $customer_id;
                if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '' && $this->request->get['crm_user_id'] != '0') {
                    $data['crm_user_id'] = $this->request->get['crm_user_id'];
                } else {
                    $data['crm_user_id'] = '';
                }

                if (!empty($credit_application_data['documents']) && count($credit_application_data['documents']) > 0) {
                    if (count($credit_application_data['documents']['application_document']) > 0) {

                        // recreate bussiness document array

                        $application_document_result = array();
                        $application_document = $credit_application_data['documents']['application_document'];
                        $application_document_type = $this->uniqueMultiDimensionalArray($application_document, 'name');
                        $application_document_type = array_unique(array_column($application_document_type, 'name'));
                        foreach ($application_document_type as $key => $doc_type) {
                            foreach ($application_document as $k => $val) {
                                if ($val['name'] == $doc_type) {
                                    $application_document_result[$doc_type][$k]['id'] = $val['id'];
                                    $application_document_result[$doc_type][$k]['path'] = $val['file_path'];
                                    $ext = pathinfo($val['file_path']);
                                    if (!empty($ext['extension']) && strtolower($ext['extension']) == 'pdf') {
                                        $application_document_result[$doc_type][$k]['cdn_path'] = $val['file_path'];
                                    } else {
                                        $application_document_result[$doc_type][$k]['cdn_path'] = 'img/dw=213,q=90/' . $val['file_path'];
                                    }

                                    if ($val['document_number'] != '') {
                                        $application_document_result[$doc_type][$k]['document_number'] = $val['document_number'];
                                    }

                                    if ($val['expiry_date'] != '') {
                                        $application_document_result[$doc_type][$k]['expiry_date'] = $val['expiry_date'];
                                    }
                                }
                            }
                        }

                        $data['application_document'] = $application_document_result;

                        // check required

                        $application_req_doc = array();
                        $application_req_doc = $application_document_type;
                        if (count($application_req_doc) > 0) {
                            foreach ($application_req_doc as $key => $app_req_doc) {
                                $data['required_' . $app_req_doc] = '0';
                            }
                        }
                    }
                }
            }

            if (!empty($credit_application_data['business_start_year'])) {
                $data['business_start_year'] = $credit_application_data['business_start_year'];
            }

            if (!empty($credit_application_data['months_in_current_location'])) {
                $data['months_in_current_location'] = $credit_application_data['months_in_current_location'];
            }

            $data['diffrent_address'] = '0';
            if (!empty($credit_application_data['diffrent_address'])) {
                $data['diffrent_address'] = $credit_application_data['diffrent_address'];
            }

            if (!empty($credit_application_data['permanent_address'])) {
                $data['permanent_address'] = $credit_application_data['permanent_address'];
            }

            if (!empty($credit_application_data['permanent_pincode'])) {
                $data['permanent_pincode'] = $credit_application_data['permanent_pincode'];
            }

            if (!empty($credit_application_data['permanent_city'])) {
                $data['permanent_city'] = $credit_application_data['permanent_city'];
            }

            if (!empty($credit_application_data['diffrent_address'])) {
                $data['permanent_state'] = $credit_application_data['permanent_state'];
            }

            $query_string = '';
            if (isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != '') {
                $query_string .= '&customer_id=' . $this->request->get['customer_id'];
            } else
                if (isset($this->request->get['ctoken']) && $this->request->get['ctoken'] != '') {
                    $query_string .= '&customer_id=' . $this->customer->getId();
                }

            if (!empty($this->request->get['token']) && $this->request->get['token'] != '') {
                $query_string .= '&token=' . $this->request->get['token'];
            }

            if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '') {
                $query_string .= '&crm_user_id=' . $this->request->get['crm_user_id'];
            }

            if (!empty($this->request->get['crm_user_password']) && $this->request->get['crm_user_password'] != '') {
                $query_string .= '&crm_user_password=' . $this->request->get['crm_user_password'];
            }

            if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '') {
                $query_string .= '&khufiya_user_id=' . $this->request->get['khufiya_user_id'];
            }

            if (!empty($this->request->get['source'])) {
                $query_string .= '&source=' . $this->request->get['source'];
            }

            if (!empty($this->request->get['credit_application_id']) && $this->request->get['credit_application_id'] != '') {
                $query_string .= '&credit_application_id=' . $this->request->get['credit_application_id'];
            }

            if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '' && $this->request->get['khufiya_user_id'] != '0') {
                $data['khufiya_user_id'] = $this->request->get['khufiya_user_id'];
            } else {
                $data['khufiya_user_id'] = '';
            }

            if (!empty($this->request->get['ctoken'])) {
                $data['ctoken'] = $this->request->get['ctoken'];
            }

            if (!empty($this->request->get['draft'])) {
                $data['draft'] = $this->request->get['draft'];
            }

            if (!empty($this->request->get['update_success'])) {
                $data['update_success'] = $this->request->get['update_success'];
                $query_string .= '&form_type=1';
            }

            if (!empty($data['app_language'])) {
                $query_string .= '&language=' . $data['app_language'];
            }

            $data['show_header'] = '0';
            if (CONFIG_IS_MOBILE == 0) {
                $data['show_header'] = '1';
            }

            if (empty($data['draft'])) {
                $data['draft'] = '0';
            }

            if (empty($this->request->get['form_type']) || $this->request->get['form_type'] == '1') {
                if (empty($this->request->get['form_type']) && !empty($credit_application_data['draft']) && $credit_application_data['draft'] == '1') {
                    if (empty($this->request->get['update_success'])) {
                        $data['form_type'] = '2';
                    }

                    if (empty($this->request->get['khufiya_user_id'])) {
                        $query_string .= '&form_type=1';
                        $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                    } else
                        if (CONFIG_IS_MOBILE == 0 && !empty($this->request->get['khufiya_user_id'])) {
                            $data['form_type'] = '2';
                            $query_string .= '&form_type=1';
                            $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                        }
                } else {
                    $data['form_type'] = '1';
                    if (empty($this->request->get['update_success'])) {
                        $query_string .= '&form_type=2';
                    }

                    if (CONFIG_IS_MOBILE == 0 && empty($this->request->get['khufiya_user_id'])) {
                        $data['back_button'] = $this->url->link('account/order', '', 'SSL');
                    }
                }
            } else {
                if (empty($this->request->get['update_success'])) {
                    $data['form_type'] = '2';
                }

                if (CONFIG_IS_MOBILE == 0 && empty($this->request->get['khufiya_user_id'])) {
                    $query_string .= '&form_type=1';
                    $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                } else
                    if (!empty($this->request->get['form_type']) && $this->request->get['form_type'] == '2') {
                        $query_string .= '&form_type=1';
                        $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                    }
            }

            $data['form_cancel'] = $this->url->link('account/order', '', 'SSL');
            $data['action'] = $this->url->link('account/credit_application/saveShortForm' . $query_string, '', 'SSL');
            if (empty($this->request->get['customer_id'])) {
                $data['home'] = $this->url->link('common/home', '', 'SSL');
            } else {
                $data['edit'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
            }

            $data['ajax_upload'] = $this->url->link('account/credit_application/uploadDocument' . $query_string, '', 'SSL');
            if (!empty($customer_id) && (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['phone_no']) || empty($data['gst_number']))) {
                $this->load->model('account/customer');
                $userInfo = $this->model_account_customer->getCustomerDetails($customer_id, $fields = array(
                    'email',
                    'firstname',
                    'lastname',
                    'telephone',
                    'gst_number'
                ));
                if (empty($data['first_name'])) {
                    $data['first_name'] = $userInfo['firstname'];
                }

                if (empty($data['last_name'])) {
                    $data['last_name'] = $userInfo['lastname'];
                }

                if (empty($data['email'])) {
                    $data['email'] = $userInfo['email'];
                }

                if (empty($data['phone_no'])) {
                    $data['phone_no'] = $userInfo['telephone'];
                }

                if (empty($data['gst_number'])) {
                    $data['gst_number'] = $userInfo['gst_number'];
                }
            }

            // =============New credit application form end==================//
            // echo '<pre>';print_r($data); exit;

        } else {
            // =============Version 3 credit application form start==================//

            if (!empty($this->customer->getId()) && empty($customer_id) && empty($this->request->get['khufiya_user_id'])) {
                $customer_id = $this->customer->getId();
            }
            if (!empty($customer_id)) {
                $response = $this->credit_application_v3($customer_id);
            } else {
                $response = $this->credit_application_v3();
            }
            $data = $response;
            $createUrlSrting = $this->createQueryString($data);
            if (empty($data['mode']) && empty($this->request->get['khufiya_user_id'])) {
                if (!empty($data['customer_id'])) {
                    if (!empty($data['draft']) && $data['draft'] == '1') {
                        $request['success_type'] = '1';
                        $createUrlSrting .= '&form_type=2';
                        $createUrlSrting .= '&success_type=1';
                        $this->response->redirect($this->url->link('account/credit_application/success' . $createUrlSrting, '', 'SSL'));
                        exit;

                    } else if (!empty($data['draft']) && $data['draft'] == '2') {
                        $createUrlSrting .= '&form_type=2';
                        $createUrlSrting .= '&success_type=2';
                        $this->response->redirect($this->url->link('account/credit_application/success' . $createUrlSrting, '', 'SSL'));
                        exit;

                    }
                }

            }
            // =============Version 3 credit application form End==================//

        }

        $request_by_app = 0;
        $headers = getallheaders();
        if (!empty($headers['REQUEST_BY'])) {
            $request_by = strtolower($headers['REQUEST_BY']);
            if ((strpos($request_by, 'android') !== false) || (strpos($request_by, 'ios') !== false) || (strpos($request_by, 'crm') !== false) || (strpos($request_by, 'app') !== false)) {
                $request_by_app = 1;
            }
        } else
            if (!empty($headers['user_id'])) { // For CRM APP
                $request_by_app = 1;
            } else {
                $request_by_app = 0;
            }

        $data['request_by_app'] = $request_by_app;
        $header_language = array();
        $footer_language = array();
        $login_language = array();
        $this->load->autoLoadLanguage('common/header', $header_language);
        $this->load->autoLoadLanguage('common/footer', $footer_language);
        $this->load->autoLoadLanguage('account/login', $login_language);
        $data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language'] = json_encode($login_language);
        $credit_language = array();
        $this->load->autoLoadLanguage('account/credit_application', $credit_language);
        $data['credit_language'] = $credit_language;

        // ----end code---

        $data['international_store'] = 0;
        $data['column_right'] = $this->load->controller('common/column_right');
        if ((CREDIT_APPLICATION_VERSION == '1' && empty($this->request->get['khufiya_user_id'])) || (!empty($this->request->get['khufiya_user_id']) && !empty($exits_credit_application_version['status']) && $exits_credit_application_version['status'] == '1' && $version == '1')) {
            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_application.tpl';
        } else if ((CREDIT_APPLICATION_VERSION == '2' && empty($this->request->get['khufiya_user_id'])) || (!empty($this->request->get['khufiya_user_id']) && !empty($exits_credit_application_version['status']) && $exits_credit_application_version['status'] == '2' && $version == '2')) {
            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/short_credit_application.tpl';
        } else {
            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_application_v3.tpl';
        }

        if (file_exists($file_path)) {
            $this->response->setOutput($this->load->view($file_path, $data, true));
        } else {
            $this->response->redirect($this->url->link('common/home'));
        }
    }


    private function credit_application_v3($customer_id = '0')
    {
        $exits_credit_application = array();
        if (!empty($this->request->get['credit_application_id'])) {
            $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationIdKhufiya($this->request->get['credit_application_id']);
        }else if (!empty($customer_id)) {
            $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationId($customer_id);
        } 
            
        if (empty($customer_id) && !empty($exits_credit_application['credit_application_data']['customer_id'])) {
            $customer_id = $exits_credit_application['credit_application_data']['customer_id'];
        }

        // add default customer data in credit application table

        if (!empty($customer_id) && $exits_credit_application['credit_application_id'] == 0) {
            $this->request->get['credit_application_id'] = $this->model_account_credit_application->addDefaultDataInCreditApplication($customer_id);
            $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationIdKhufiya($this->request->get['credit_application_id']);
        }

        // end code

        if (!empty($exits_credit_application['status']) && $exits_credit_application['status'] == TRUE) {
            if (isset($this->request->get['credit_application_id'])) {
                $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id, $this->request->get['credit_application_id']);
            } else {
                $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id);
            }

            $data = $exits_credit_application['credit_application_data'];
            $data['documents'] = $credit_application_data['documents'] ?? '';
            $data['customer_id'] = $customer_id;
            if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '' && $this->request->get['crm_user_id'] != '0') {
                $data['crm_user_id'] = $this->request->get['crm_user_id'];
            } else {
                $data['crm_user_id'] = '';
            }


        }


        $query_string = '';
        if (isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != '') {
            $query_string .= '&customer_id=' . $this->request->get['customer_id'];
        } else
            if (isset($this->request->get['ctoken']) && $this->request->get['ctoken'] != '') {
                $query_string .= '&customer_id=' . $this->customer->getId();
            }

        if (!empty($this->request->get['token']) && $this->request->get['token'] != '') {
            $query_string .= '&token=' . $this->request->get['token'];
        }

        if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '') {
            $query_string .= '&crm_user_id=' . $this->request->get['crm_user_id'];
        }

        if (!empty($this->request->get['crm_user_password']) && $this->request->get['crm_user_password'] != '') {
            $query_string .= '&crm_user_password=' . $this->request->get['crm_user_password'];
        }

        if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '') {
            $query_string .= '&khufiya_user_id=' . $this->request->get['khufiya_user_id'];
        }

        if (!empty($this->request->get['source'])) {
            $query_string .= '&source=' . $this->request->get['source'];
        }

        if (!empty($this->request->get['credit_application_id']) && $this->request->get['credit_application_id'] != '') {
            $query_string .= '&credit_application_id=' . $this->request->get['credit_application_id'];
        }

        if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '' && $this->request->get['khufiya_user_id'] != '0') {
            $data['khufiya_user_id'] = $this->request->get['khufiya_user_id'];
        } else {
            $data['khufiya_user_id'] = '';
        }

        if (!empty($this->request->get['ctoken'])) {
            $data['ctoken'] = $this->request->get['ctoken'];
        }

        if (!empty($this->request->get['draft'])) {
            $data['draft'] = $this->request->get['draft'];
        }

        if (!empty($this->request->get['update_success'])) {
            $data['update_success'] = $this->request->get['update_success'];
            $query_string .= '&form_type=1';
        }

        if (!empty($data['app_language'])) {
            $query_string .= '&language=' . $data['app_language'];
        }

        $data['show_header'] = '0';
        if (CONFIG_IS_MOBILE == 0) {
            $data['show_header'] = '1';
        }
        if (!empty($this->request->get['mode']) && $this->request->get['mode'] == 'edit') {
            $data['mode'] = 'edit';
        }
        if (empty($this->request->get['form_type']) || $this->request->get['form_type'] == '1') {
            if (empty($this->request->get['form_type']) && !empty($credit_application_data['draft']) && $credit_application_data['draft'] == '1') {
                if (empty($this->request->get['update_success'])) {
                    $data['form_type'] = '2';
                }

                if (empty($this->request->get['khufiya_user_id'])) {
                    $query_string .= '&form_type=2';
                    $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                } else
                    if (CONFIG_IS_MOBILE == 0 && !empty($this->request->get['khufiya_user_id'])) {
                        $data['form_type'] = '2';
                        $query_string .= '&form_type=1';
                        $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                    }
            } else {

                if (empty($this->request->get['form_type']) && !empty($credit_application_data['draft']) && $credit_application_data['draft'] == '2') {
                    $data['form_type'] = '2';
                    if (empty($this->request->get['update_success'])) {
                        $query_string .= '&form_type=1';
                        if(CONFIG_IS_MOBILE == 0 ){

                        $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                        }
                    }else{
                       $query_string .= '&form_type=2'; 
                       if(CONFIG_IS_MOBILE == 0 ){
                       $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                   }
                    }

                    if (CONFIG_IS_MOBILE == 0 && empty($this->request->get['khufiya_user_id'])) {
                        $data['back_button'] = $this->url->link('account/order', '', 'SSL');
                    }

                } else {
                    $data['form_type'] = '1';
                    if (empty($this->request->get['update_success'])) {
                        $query_string .= '&form_type=2';
                    }

                    if (CONFIG_IS_MOBILE == 0 && empty($this->request->get['khufiya_user_id'])) {
                        $data['back_button'] = $this->url->link('account/order', '', 'SSL');
                    }
                }

            }
        } else {
            if (empty($this->request->get['update_success'])) {
                $data['form_type'] = '2';
            }

            if (CONFIG_IS_MOBILE == 0 && empty($this->request->get['khufiya_user_id'])) {
                $query_string .= '&form_type=1';
                $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
            } else
                if (!empty($this->request->get['form_type']) && $this->request->get['form_type'] == '2') {
                    $query_string .= '&form_type=1';
                    $data['back_button'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
                }
        }

        $data['form_cancel'] = $this->url->link('account/order', '', 'SSL');
        $data['action'] = $this->url->link('account/credit_application/saveShortForm' . $query_string, '', 'SSL');
        if (empty($this->request->get['customer_id'])) {
            $data['home'] = $this->url->link('common/home', '', 'SSL');
        } else {
            $data['edit'] = $this->url->link('account/credit_application&mode=edit' . $query_string, '', 'SSL');
        }

        $data['ajax_upload'] = $this->url->link('account/credit_application/uploadDocument' . $query_string, '', 'SSL');
        return $data;
    }


    public function authenticationCrmUser($crm_user_id, $crm_user_password)
    {

        if (!function_exists('curl_init')) {
            die('cURL not available!');
        }
        $curl = curl_init();
        $crm_url = $this->getCrmUrl();
        curl_setopt($curl, CURLOPT_URL, $crm_url . 'cron/checkUserExists');
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


        // Send JSON body via POST request
        $postData = array(
            'user_id' => $crm_user_id,
            'password' => $crm_user_password
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData, true));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept:application/json'));
        // As said above, the target script needs to read `php://input`, not `$_POST`!


        $output = curl_exec($curl);
        if ($output === FALSE) {
            echo 'An error has occurred: ' . curl_error($curl) . PHP_EOL;
            $authorise_crm_user = false;
        } else {
            $output;
        }
        //remove space and dash
        $output = str_replace(' ', '-', $output);
        //remove spacial charaters
        $output = preg_replace('/[^A-Za-z0-9\-]/', '', $output);

        if (trim($output) != '0') {
            $authorise_crm_user = true;
        } else {
            $authorise_crm_user = false;
        }

        //var_dump($authorise_crm_user);

        return $authorise_crm_user;

    }

    public function uniqueMultiDimensionalArray($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    function reArrayFiles($file_post)
    {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];

            }
        }

        return $file_ary;
    }

    public function saveForm()
    {
        if (isset($this->session->data['draft'])) unset($this->session->data['draft']);

        $data = array();

        //create file array
        $file_data = array();
        if (count($this->request->files) > 0) {
            foreach ($this->request->files as $key => $val) {
                $file_data[$key] = $this->reArrayFiles($val);

            }
        }

        $this->load->model('account/credit_application');

        //set current draft in session
        if ($this->request->post['draft'] != '') {
            $this->session->data['draft'] = $this->request->post['draft'];
        }

        //check validate
        $error_status = $this->validate($file_data);

        //upload document
        if (count($file_data) > 0) {

            if (SITE_ENVIRONMENT == 'Production') {
                $customer_folder_name = $this->request->get['customer_id'];
            } else {
                $customer_folder_name = 'staging_' . $this->request->get['customer_id'];
            }

            if ($this->request->get['customer_id'] == '0') {
                $customer_folder_name = $customer_folder_name . '_' . $this->request->post['credit_application_id'];
            }

            $uploads_dir = 'credit_application/' . $customer_folder_name . '/' . $this->request->post['document_type'] . '/';
            foreach ($file_data as $j => $files) {
                foreach ($files as $k => $file) {
                    if (!empty($file['tmp_name'])) {
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $file_name = $j . '_' . time() . '.' . $extension;
                        $result = $this->uploadImages($uploads_dir, $file_name, $file['tmp_name']);
                        if ($result) {
                            $file_data[$j][$k]['name'] = $file_name;
                        }
                    }
                }
            }
        }

        //get form status for send mail
        $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationId($this->request->post['customer_id']);
        $form_action_status = $exits_credit_application['form_action'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST' && $error_status == false)) {
            if ($this->request->post['draft'] <= '2') {

                $request = $this->request->post;

                if (isset($request['residing_date_month']) && isset($request['residing_date_year'])) {
                    $request['residing_date'] = $request['residing_date_year'] . '-' . $request['residing_date_month'] . '-01';
                }

                if (isset($request['occupied_since_month']) && isset($request['occupied_since_year'])) {
                    $request['occupied_since'] = $request['occupied_since_year'] . '-' . $request['occupied_since_month'] . '-01';
                }

                if (isset($request['business_since_month']) && isset($request['business_since_year'])) {
                    $request['business_since'] = $request['business_since_year'] . '-' . $request['business_since_month'] . '-01';
                }

                if (isset($request['permanent_date_month']) && isset($request['permanent_date_year'])) {
                    $request['permanent_residing_date'] = $request['permanent_date_year'] . '-' . $request['permanent_date_month'] . '-01';
                }


                $this->model_account_credit_application->saveCredit($request);
            } else if ($this->request->post['draft'] >= '3') {
                $this->model_account_credit_application->saveCreditDocument($file_data, $this->request->post);
                if (isset($this->request->post['delete_document']) && !empty($this->request->post['delete_document'])) {
                    // $documents = $this->model_account_credit_application->getCreditDocumentsfromIds($this->request->post['delete_document']);
                    // foreach($documents as $doc){
                    //     $this->deleteCreditApplicationImageOnCdnServer($doc['file_path']);
                    // }
                    $this->model_account_credit_application->deleteCreditDocument($this->request->post['delete_document']);
                }

            }

            //send mail after complete form
            // $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationId($this->request->post['customer_id']);
            // if($exits_credit_application['form_action'] == 'edit' && $exits_credit_application['draft'] == 4){
            //     $this->sendMailAfterCompleteForm();
            // }
        }

        //send mail after complete form
        if ($form_action_status == 'add' && $this->request->post['draft'] == 4) {

            //get coustomer credit info
            $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($this->request->post['customer_id']);

            //recreate application document array
            $application_document_result = array();
            $application_document = $credit_application_data['documents']['application_document'];
            $application_document_type = $this->uniqueMultiDimensionalArray($application_document, 'name');
            $application_document_type = array_unique(array_column($application_document_type, 'name'));
            $credit_application_data['documents']['application_document'] = $application_document_type;
            foreach ($application_document_type as $key => $doc_type) {
                foreach ($application_document as $k => $val) {
                    if ($val['name'] == $doc_type) {
                        $application_document_result[$doc_type][$k]['id'] = $val['id'];
                        $application_document_result[$doc_type][$k]['path'] = $val['file_path'];
                        if ($val['document_number'] != '') {
                            $application_document_result[$doc_type][$k]['document_number'] = $val['document_number'];
                        }
                        if ($val['expiry_date'] != '') {
                            $application_document_result[$doc_type][$k]['expiry_date'] = $val['expiry_date'];
                        }
                    }
                }
            }
            $credit_application_data['documents']['application_document'] = $application_document_result;


            //recreate bussiness document array
            $bussiness_document_result = array();
            $bussiness_document = $credit_application_data['documents']['bussiness_document'];
            $bussiness_document_type = $this->uniqueMultiDimensionalArray($bussiness_document, 'name');
            $bussiness_document_type = array_unique(array_column($bussiness_document_type, 'name'));
            foreach ($bussiness_document_type as $key => $doc_type) {
                foreach ($bussiness_document as $k => $val) {
                    if ($val['name'] == $doc_type) {
                        $bussiness_document_result[$doc_type][$k]['id'] = $val['id'];
                        $bussiness_document_result[$doc_type][$k]['path'] = $val['file_path'];
                    }
                }
            }
            $credit_application_data['documents']['bussiness_document'] = $bussiness_document_result;
            //send mail
            $this->sendMailAfterCompleteForm($credit_application_data);
        }

        $query_string = '';
        if ($this->request->get['customer_id'] != '') {
            $query_string .= '&customer_id=' . $this->request->get['customer_id'];
        }
        if ($this->request->get['token'] != '') {
            $query_string .= '&token=' . $this->request->get['token'];
        }

        if ($this->request->get['crm_user_id'] != '') {
            $query_string .= '&crm_user_id=' . $this->request->get['crm_user_id'];
        }
        if ($this->request->get['crm_user_password'] != '') {
            $query_string .= '&crm_user_password=' . $this->request->get['crm_user_password'];
        }
        if ($this->request->get['khufiya_user_id'] != '') {
            $query_string .= '&khufiya_user_id=' . $this->request->get['khufiya_user_id'];
        }
        if ($this->request->post['draft'] != '') {
            $query_string .= '&draft=' . $this->request->post['draft'];
        }
        $this->response->redirect($this->url->link('account/credit_application' . $query_string, '', 'SSL'));
    }

    public function saveShortForm()
    {
        $this->load->model('account/customer');
        if (isset($this->session->data['draft'])) unset($this->session->data['draft']);

        if (isset($this->request->post['customer_id']) && $this->request->post['customer_id'] == 0) {
            $userCheckByMobile = $this->model_account_customer->getCustomerByMobile($this->request->post['phone_no']); //'7905339713'
            if (empty($userCheckByMobile['customer_id'])) {
                $userCheckByEmail = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
                if (empty($userCheckByEmail['customer_id'])) {
                    //reg_email

                    $insert_user_data['firstname'] = $this->request->post['first_name'];
                    $insert_user_data['lastname'] = $this->request->post['last_name'];
                    $insert_user_data['mobile_country_code'] = '+91';
                    $insert_user_data['reg_telephone'] = $this->request->post['phone_no'];
                    $insert_user_data['reg_email'] = $this->request->post['email'];
                    $insert_user_data['gst_number'] = $this->request->post['gst_number'];
                    $insert_user_data['whatsapp_telephone'] = '';
                    $insert_user_data['password'] = $this->model_account_customer->generatereferralcode();

                    /*$lastInsertId=$this->model_account_customer->addCustomer($insert_user_data);
                    if(!empty($lastInsertId)){
                         $customer_id = $this->request->post['customer_id'] =$lastInsertId;
                         $this->sendMailAfterUserSignUp($this->request->post, $insert_user_data['password']);

                    }else{
                        $this->response->redirect($this->url->link('account/credit_application', '', 'SSL'));
                    }*/

                } else {
                    $customer_id = $this->request->post['customer_id'] = $userCheckByEmail['customer_id'];
                }
            } else {
                $customer_id = $this->request->post['customer_id'] = $userCheckByMobile['customer_id'];

            }
        }
        $data = array();
        //create file array
        $file_data = array();
        if (count($this->request->files) > 0) {
            foreach ($this->request->files as $key => $val) {
                $file_data[$key] = $this->reArrayFiles($val);

            }
        }

        $this->load->model('account/credit_application');
        if (!empty($this->request->get['customer_id'])) {
            $customer_id = $this->request->get['customer_id'];
        } else {
            $customer_id = '0';
        }

        //upload document
        if (count($file_data) > 0) {

            if (SITE_ENVIRONMENT == 'Production') {

                $customer_folder_name = $customer_id;

            } else {
                $customer_folder_name = 'staging_' . $customer_id;
            }

            if ($customer_id == '0') {
                $customer_folder_name = $customer_folder_name . '_' . $this->request->post['credit_application_id'];
            }

            $uploads_dir = 'credit_application/' . $customer_folder_name . '/' . $this->request->post['document_type'] . '/';
            foreach ($file_data as $j => $files) {
                foreach ($files as $k => $file) {
                    if (!empty($file['tmp_name'])) {
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $file_name = $j . '_' . time() . '.' . $extension;
                        $result = $this->uploadImages($uploads_dir, $file_name, $file['tmp_name']);
                        if ($result) {
                            $file_data[$j][$k]['name'] = $file_name;
                        }
                    }
                }
            }
        }

        if (!empty($customer_id) || !empty($this->request->post['customer_id'])) {
            if (!empty($this->request->post['customer_id']) || $this->request->post['customer_id'] == '0') {
                $customer_id = $this->request->post['customer_id'];
            }
            $save_data = $this->saveShortFormFunction($customer_id);

        } else if (isset($customer_id) && $customer_id == '0') {
            if (empty($this->request->post['credit_application_id'])) {
                $this->request->post['credit_application_id'] = '0';
            }
            $save_data = $this->saveShortFormFunction($customer_id, $this->request->post['credit_application_id']);
        }

    }

    public function saveShortFormFunction($customer_id, $credit_application_id = '0')
    {
        //get form status for send mail
        if (!empty($customer_id)) {
            $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationId($customer_id, 2);
        } else if ($credit_application_id > 0) {
            $exits_credit_application = $this->model_account_credit_application->isExitCustomerCreditApplicationIdKhufiya($credit_application_id, 2);
        }
        $form_action_status = $exits_credit_application['form_action'];
        $request = $this->request->post;
        if (!empty($exits_credit_application['credit_application_id'])) {
            $credit_application_id = $exits_credit_application['credit_application_id'];
        } else {
            $credit_application_id = 0;
        }

        // set city and state
        $request['pincode'] = $request['current_pincode'];
        $data_json = json_encode($request);
        $api_url = HTTPS_SERVER . "index.php?route=restapi/lookup/pincode";
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type: application/json',
                'Content-Length: ' . strlen($data_json))
        );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $data = json_decode($result, true);

        $request['current_city'] = isset($data[0]['city']) ? $data[0]['city'] : '';
        $request['current_state'] = isset($data[0]['zone']) ? $data[0]['zone'] : '';
        //------------


        //source
        $request['source'] = 'website';

        if ($this->request->get['khufiya_user_id'] != '') {
            $request['source'] = 'admin';
        } else if (!empty($this->request->get['source'])) {
            $request['source'] = $this->request->get['source'];
        } else if (CONFIG_IS_MOBILE == 1) {
            $request['source'] = 'mobile';
        }

        //---------

        $response = $this->model_account_credit_application->saveShortCredit($request, $credit_application_id);

        if ($response) {
            if (!empty($file_data)) {
                $this->model_account_credit_application->saveCreditDocument($file_data, $this->request->post, 2);
            }

            if (isset($this->request->post['delete_document']) && !empty($this->request->post['delete_document'])) {
                // $documents = $this->model_account_credit_application->getCreditDocumentsfromIds($this->request->post['delete_document']);
                // foreach($documents as $doc){
                //     $this->deleteCreditApplicationImageOnCdnServer($doc['file_path']);
                // }

                $this->model_account_credit_application->deleteCreditDocument($this->request->post['delete_document']);
            }

            //get coustomer credit info
            if (isset($credit_application_id) && $credit_application_id > 0) {
                $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id, $credit_application_id);
            } else {
                $credit_application_data = $this->model_account_credit_application->getCustomerCreditApplication($customer_id);
            }
            //recreate application document array
            $application_document_result = array();
            $application_document = $credit_application_data['documents']['application_document'];
            $application_document_type = $this->uniqueMultiDimensionalArray($application_document, 'name');
            $application_document_type = array_unique(array_column($application_document_type, 'name'));
            $credit_application_data['documents']['application_document'] = $application_document_type;
            foreach ($application_document_type as $key => $doc_type) {
                foreach ($application_document as $k => $val) {
                    if ($val['name'] == $doc_type) {
                        $application_document_result[$doc_type][$k]['id'] = $val['id'];
                        $application_document_result[$doc_type][$k]['path'] = $val['file_path'];
                        if ($val['document_number'] != '') {
                            $application_document_result[$doc_type][$k]['document_number'] = $val['document_number'];
                        }
                        if ($val['expiry_date'] != '') {
                            $application_document_result[$doc_type][$k]['expiry_date'] = $val['expiry_date'];
                        }
                    }
                }
            }
            $credit_application_data['documents']['application_document'] = $application_document_result;
            if (empty($credit_application_data['email'])) {
                $userEmail = $this->model_account_customer->getCustomerDetails($credit_application_data['customer_id'], $fields = array('email'));
                $credit_application_data['email'] = $userEmail['email'];
            }

            if (!empty($this->request->post['draft']) && $this->request->post['draft'] == '2') {
                $this->sendMailAfterCompleteForm($credit_application_data);
            }
        }


        $query_string = '';
        if ($this->request->get['customer_id'] != '') {
            $query_string .= '&customer_id=' . $this->request->get['customer_id'];
        } else if (isset($customer_id) && $customer_id != '') {
            $query_string .= '&customer_id=' . $customer_id;
        }
        if ($this->request->get['token'] != '') {
            $query_string .= '&token=' . $this->request->get['token'];
        }

        if ($this->request->get['crm_user_id'] != '') {
            $query_string .= '&crm_user_id=' . $this->request->get['crm_user_id'];
        }
        if ($this->request->get['crm_user_password'] != '') {
            $query_string .= '&crm_user_password=' . $this->request->get['crm_user_password'];
        }
        if ($this->request->get['khufiya_user_id'] != '') {
            $query_string .= '&khufiya_user_id=' . $this->request->get['khufiya_user_id'];
        }
        if (!empty($this->request->get['form_type']) && $this->request->post['draft'] == '1') {
            $query_string .= '&form_type=2';
        } else {
            $query_string .= '&update_success=success';
        }
        if ($credit_application_id > 0 || (isset($response) && $response > 0)) {
            if ($credit_application_id == '0' && (isset($response) && $response > 0)) {
                $credit_application_id = $response;
            }
            $query_string .= '&credit_application_id=' . $credit_application_id;
        }


        $this->response->redirect($this->url->link('account/credit_application' . $query_string, '', 'SSL'));
    }


    protected function validate($file_data)
    {

        $status = false;

        if (isset($this->session->data['validation_error'])) unset($this->session->data['validation_error']);

        if ($this->request->post['draft'] == 1) {


            if (empty(trim($this->request->post['business_entity_type'])) || trim($this->request->post['business_entity_type'] == '')) {
                $this->session->data['validation_error'] = 'Please select business entity type';
            } elseif (utf8_strlen(trim($this->request->post['first_name'])) < 3) {
                $this->session->data['validation_error'] = 'First Name must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['last_name'])) < 3) {
                $this->session->data['validation_error'] = 'last Name must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['father_name'])) < 3) {
                $this->session->data['validation_error'] = 'Father Name must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['mother_name'])) < 3) {
                $this->session->data['validation_error'] = 'Mother Name must be minimum 3 characters!';
            } elseif (empty(trim($this->request->post['marital_status'])) || trim($this->request->post['marital_status'] == '')) {
                $this->session->data['validation_error'] = 'Please select Marital Status';
            } elseif (!preg_match("/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/", trim($this->request->post['pan_no']))) {
                $this->session->data['validation_error'] = 'Invalid pan number';
            } elseif (utf8_strlen(trim($this->request->post['aadhaar_no'])) != 12 || !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['aadhaar_no']))) {
                $this->session->data['validation_error'] = 'Aadhaar No must be 12 digit number';
            } elseif (empty(trim($this->request->post['dob'])) || trim($this->request->post['dob'] == '')) {
                $this->session->data['validation_error'] = 'Please select Date of Birth';
            } elseif (empty(trim($this->request->post['gender'])) || trim($this->request->post['gender'] == '')) {
                $this->session->data['validation_error'] = 'Please select Gender';
            } elseif (empty(trim($this->request->post['education'])) || trim($this->request->post['education'] == '')) {
                $this->session->data['validation_error'] = 'Please select Education';
            } elseif (utf8_strlen(trim($this->request->post['phone_no'])) != 10 || (!preg_match('/^[1-9][0-9]*$/', trim($this->request->post['phone_no'])))) {
                $this->session->data['validation_error'] = 'Phone No must be 10 digit Number';
            } elseif (utf8_strlen(trim($this->request->post['email'])) < 3 || !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', trim($this->request->post['email']))) {
                $this->session->data['validation_error'] = 'Please enter Valid Email';
            } elseif (utf8_strlen(trim($this->request->post['current_address'])) < 3) {
                $this->session->data['validation_error'] = 'Current Address must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['current_pincode'])) != 6 || !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['current_pincode']))) {
                $this->session->data['validation_error'] = 'Pincode must be 6 digit number';
            } elseif (utf8_strlen(trim($this->request->post['current_city'])) < 3) {
                $this->session->data['validation_error'] = 'Current City must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['current_state'])) < 3) {
                $this->session->data['validation_error'] = 'Current State must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['current_landline_phone_no'])) == '') {
                $this->session->data['validation_error'] = 'Current Landline Phone No Must be number';
            } elseif (empty(trim($this->request->post['current_resident_premises'])) || trim($this->request->post['current_resident_premises'] == '')) {
                $this->session->data['validation_error'] = 'Please select Current Resident Premises';
            } elseif (empty(trim($this->request->post['residing_date_month'])) || trim($this->request->post['residing_date_month'] == '')) {
                $this->session->data['validation_error'] = 'Please select Residing Month';
            } elseif (empty(trim($this->request->post['residing_date_year'])) || trim($this->request->post['residing_date_year'] == '')) {
                $this->session->data['validation_error'] = 'Please select Residing Year';
            } elseif (utf8_strlen(trim($this->request->post['permanent_address'])) < 3) {
                $this->session->data['validation_error'] = 'Permanent Address must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['permanent_pincode'])) != 6 || !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['permanent_pincode']))) {
                $this->session->data['validation_error'] = 'Permanent Pincode must be 6 digit number';
            } elseif (utf8_strlen(trim($this->request->post['permanent_city'])) < 3) {
                $this->session->data['validation_error'] = 'Permanent City must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['permanent_state'])) < 3) {
                $this->session->data['validation_error'] = 'Permanent State must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['permanent_landline_phone_no'])) == '') {
                $this->session->data['validation_error'] = 'Permanent Landline Phone No Must be number';
            } elseif (empty(trim($this->request->post['permanent_resident_premises'])) || trim($this->request->post['permanent_resident_premises'] == '')) {
                $this->session->data['validation_error'] = 'Please select Permanent Resident Premises';
            } elseif (empty(trim($this->request->post['permanent_date_month'])) || trim($this->request->post['permanent_date_month'] == '')) {
                $this->session->data['validation_error'] = 'Please select Permanent Residing Month';
            } elseif (empty(trim($this->request->post['permanent_date_year'])) || trim($this->request->post['permanent_date_year'] == '')) {
                $this->session->data['validation_error'] = 'Please select Permanent Residing Year';
            }


        } elseif ($this->request->post['draft'] == 2) {

            if (utf8_strlen(trim($this->request->post['company_name'])) < 3) {
                $this->session->data['validation_error'] = 'Company Name must be minimum 3 characters!';
            }
            //               elseif (utf8_strlen(trim($this->request->post['entity_name'])) < 3){
            //                $this->session->data['validation_error'] = 'Entity Name must be minimum 3 characters!';
            // }
            elseif ((int)$this->request->post['partners'] < 1) {
                $this->session->data['validation_error'] = 'Partners must be minimum 1';
            } elseif (utf8_strlen(trim($this->request->post['shop_establishment_number'])) == '' && utf8_strlen(trim($this->request->post['business_pan_no'])) == '') {
                $this->session->data['validation_error'] = 'Either Shop Establishment Number or Business PAN is mandatory!';
            } elseif (utf8_strlen(trim($this->request->post['shop_establishment_number'])) < 3 && utf8_strlen(trim($this->request->post['business_pan_no'])) == '') {
                $this->session->data['validation_error'] = 'Shop Establishment Number must be minimum 3 characters!';
            } elseif (!preg_match("/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/", trim($this->request->post['business_pan_no'])) && utf8_strlen(trim($this->request->post['shop_establishment_number'])) == '') {
                $this->session->data['validation_error'] = 'Invalid Business Pan Number';
            }
            //               elseif (utf8_strlen(trim($this->request->post['company_identification_number'])) < 3){
            //                $this->session->data['validation_error'] = 'Company Identification Number must be minimum 3 characters!';
            // }
            elseif (utf8_strlen(trim($this->request->post['trading_name'])) < 3) {
                $this->session->data['validation_error'] = 'Trading Name must be minimum 3 characters!';
            } elseif (empty(trim($this->request->post['nature_of_business'])) || trim($this->request->post['nature_of_business'] == '')) {
                $this->session->data['validation_error'] = 'Please select Nature Of Business';
            } elseif (empty(trim($this->request->post['business_ownership'])) || trim($this->request->post['business_ownership'] == '')) {
                $this->session->data['validation_error'] = 'Please select Business Ownership';
            } elseif (empty(trim($this->request->post['business_segment'])) || trim($this->request->post['business_segment'] == '')) {
                $this->session->data['validation_error'] = 'Please select Business Segment';
            } elseif (empty(trim($this->request->post['business_vintage'])) || trim($this->request->post['business_vintage'] == '')) {
                $this->session->data['validation_error'] = 'Please select Business Vintage';
            } elseif (empty(trim($this->request->post['months_in_current_business'])) || trim($this->request->post['months_in_current_business'] == '')) {
                $this->session->data['validation_error'] = 'Please select Months in Current Business';
            } elseif (empty(trim($this->request->post['business_premises'])) || trim($this->request->post['business_premises'] == '')) {
                $this->session->data['validation_error'] = 'Please select Business Premises';
            } elseif (empty(trim($this->request->post['occupied_since_month'])) || trim($this->request->post['occupied_since_month'] == '')) {
                $this->session->data['validation_error'] = 'Please select Occupied Since Month';
            } elseif (empty(trim($this->request->post['occupied_since_year'])) || trim($this->request->post['occupied_since_year'] == '')) {
                $this->session->data['validation_error'] = 'Please select Occupied Since Year';
            } elseif (utf8_strlen(trim($this->request->post['business_address'])) < 3) {
                $this->session->data['validation_error'] = 'Business Address must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['business_pincode'])) != 6 || !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['business_pincode']))) {
                $this->session->data['validation_error'] = 'Business Pincode must be 6 digit number';
            } elseif (utf8_strlen(trim($this->request->post['business_city'])) < 3) {
                $this->session->data['validation_error'] = 'Business City must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['business_state'])) < 3) {
                $this->session->data['validation_error'] = 'Business State must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['reg_office_pincode'])) != 6 || !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['reg_office_pincode']))) {
                $this->session->data['validation_error'] = 'Registred Office Pincode must be 6 digit number';
            } elseif (utf8_strlen(trim($this->request->post['reg_office_city'])) < 3) {
                $this->session->data['validation_error'] = 'Registered Office City must be minimum 3 characters.';
            } elseif (utf8_strlen(trim($this->request->post['reg_office_state'])) < 3) {
                $this->session->data['validation_error'] = 'Registred Office State must be minimum 3 characters.';
            } elseif ($this->request->post['has_other_entity'] == 'yes' && utf8_strlen($this->request->post['other_business_entity_detail']) < 10) {
                $this->session->data['validation_error'] = 'Other Business Entity Detail must be minimum 10 characters!';
            } elseif (empty(trim($this->request->post['business_since_month'])) || trim($this->request->post['business_since_month'] == '')) {
                $this->session->data['validation_error'] = 'Please select Business Since Month';
            } elseif (empty(trim($this->request->post['business_since_year'])) || trim($this->request->post['business_since_year'] == '')) {
                $this->session->data['validation_error'] = 'Please select Business Since Year';
            } elseif (empty(trim($this->request->post['annual_turnover'])) || trim($this->request->post['annual_turnover'] == '')) {
                $this->session->data['validation_error'] = 'Please select Annual Turnover';
            } elseif ($this->request->post['has_litigation'] == 'yes' && utf8_strlen($this->request->post['litigation']) < 10) {
                $this->session->data['validation_error'] = 'Litigation must be minimum 10 characters!';
            } elseif (utf8_strlen(trim($this->request->post['contact_person_first_name'])) < 3) {
                $this->session->data['validation_error'] = 'contact Person First Name must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['contact_person_last_name'])) < 3) {
                $this->session->data['validation_error'] = 'contact Person Last Name must be minimum 3 characters!';
            } elseif ($this->request->post['is_contact_person_same'] == '0' && utf8_strlen(trim($this->request->post['contact_person_designation'])) < 3) {
                $this->session->data['validation_error'] = 'Contact Person Designation must be minimum 3 characters!';
            } elseif ($this->request->post['is_contact_person_same'] == '0' && utf8_strlen(trim($this->request->post['contact_person_relation_with_borrower'])) < 3) {
                $this->session->data['validation_error'] = 'Contact Person Relation With Borrower must be minimum 3 characters!';
            } elseif (utf8_strlen(trim($this->request->post['contact_person_email'])) < 3 || !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', trim($this->request->post['contact_person_email']))) {
                $this->session->data['validation_error'] = 'Please enter valid Contact Person Email';
            } elseif (utf8_strlen(trim($this->request->post['contact_person_phone_no'])) != 10 || !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['contact_person_phone_no']))) {
                $this->session->data['validation_error'] = 'Contact Person Phone No Must be 10 digit number';
            } elseif ($this->request->post['declaration'] != 1) {
                $this->session->data['validation_error'] = 'Please select declaration';
            }
        }


        if ($this->request->post['draft'] == 3) {


            //get document
            if (!empty($this->request->get['customer_id'])) {
                $customer_id = $this->request->get['customer_id'];
                $application_document_type = array();
                $document_data = $this->model_account_credit_application->getCustomerCreditApplicationDocument($customer_id);
                //echo "<pre>"; print_r($document_data); die;
                if (count($document_data['application_document']) > 0) {
                    $application_document = $document_data['application_document'];
                    $application_document_type = $this->uniqueMultiDimensionalArray($application_document, 'name');
                    $application_document_type = array_unique(array_column($application_document_type, 'name'));
                }
            }


            if ($this->request->post['required_pancard'] == 1) {
                if ($file_data['pancard'][0]['name'] == '') {
                    $this->session->data['validation_error'] = 'Please upload the Pancard';
                }
            }
            if ($this->request->post['required_aadhaar_card'] == 1) {
                if ($file_data['aadhaar_card'][0]['name'] == '') {
                    $this->session->data['validation_error'] = 'Please upload the Aadhaar card';
                }
            }
            if ($this->request->post['required_photo'] == 1) {
                if ($file_data['photo'][0]['name'] == '') {
                    $this->session->data['validation_error'] = 'Please upload the Photo';
                }
            }


            //if select optional document then validation on document Number and Expiry date
            if (trim($file_data['voter_id'][0]['name']) != '' && utf8_strlen(str_replace("/", "", trim($this->request->post['voter_id']['number']))) != 10) {
                $this->session->data['validation_error'] = 'Please Enter Voter Id must be minimum 10 characters! ';
            } //if enter voter_id no and not select voter_id
            elseif (!in_array("voter_id", $application_document_type) && trim($file_data['voter_id'][0]['name']) == '' && utf8_strlen(str_replace("/", "", trim($this->request->post['voter_id']['number']))) != '') {
                $this->session->data['validation_error'] = 'Please select Voter Id';
            } elseif (in_array("voter_id", $application_document_type)) {
                if (utf8_strlen(trim($this->request->post['voter_id']['number'])) != 10) {
                    $this->session->data['validation_error'] = 'Please Voter Id must be minimum 10 characters!';
                }
            } elseif (trim($file_data['driving_license'][0]['name']) != '' && utf8_strlen(str_replace("/", "", trim($this->request->post['driving_license']['number']))) != 15) {
                $this->session->data['validation_error'] = 'Please Enter Driving License Number must be 15 characters!';
            } elseif (trim($file_data['driving_license'][0]['name']) != '' && trim($this->request->post['driving_license']['expiry_date']) == '') {
                $this->session->data['validation_error'] = 'Please Select Driving License Expiry Date';
            } elseif (!in_array("driving_license", $application_document_type) && trim($file_data['driving_license'][0]['name']) == '' && (utf8_strlen(str_replace("/", "", trim($this->request->post['driving_license']['number']))) != '' || trim($this->request->post['driving_license']['expiry_date']) != '')) {
                $this->session->data['validation_error'] = 'Please Select Driving License!';
            } elseif (in_array("driving_license", $application_document_type)) {

                if (utf8_strlen(trim($this->request->post['driving_license']['number'])) != 15) {
                    $this->session->data['validation_error'] = 'Please Driving License Number must be 15 characters!';
                } elseif (trim($this->request->post['driving_license']['expiry_date']) == '') {
                    $this->session->data['validation_error'] = 'Please Select Driving License Expiry Date';
                }
            } elseif (trim($file_data['passport'][0]['name']) != '' && utf8_strlen(trim($this->request->post['passport']['number'])) != 8) {
                $this->session->data['validation_error'] = 'Please Enter Passport Number must be 8 characters!';
            } elseif (trim($file_data['passport'][0]['name']) != '' && trim($this->request->post['passport']['expiry_date']) == '') {
                $this->session->data['validation_error'] = 'Please Select Passport Expiry Date';
            } elseif (!in_array("passport", $application_document_type) && trim($file_data['passport'][0]['name']) == '' && (utf8_strlen(str_replace("/", "", trim($this->request->post['passport']['number']))) != '' || trim($this->request->post['passport']['expiry_date']) != '')) {
                $this->session->data['validation_error'] = 'Please Select Driving License!';
            } elseif (in_array("passport", $application_document_type)) {
                if (utf8_strlen(trim($this->request->post['passport']['number'])) != 8) {
                    $this->session->data['validation_error'] = 'Please Passport Number must be 8 characters!';
                } elseif (trim($this->request->post['passport']['expiry_date']) == '') {
                    $this->session->data['validation_error'] = 'Please Select Passport Expiry Date';
                }
            }

        }


        if ($this->request->post['draft'] == 4) {

            //echo "<pre>"; print_r($file_data); die;

            if (count($file_data['blank_optional_doc']) > 0 || count($file_data['blank_mandatory_doc']) > 0) {
                $this->session->data['validation_error'] = 'Please select Business Proof and upload the correct document';
            }


            //mandatory document array
            $mandatory_document = array();
            $mandatory_document[0] = 'electricity_bill';
            $mandatory_document[1] = 'phone_landline_bill';
            $mandatory_document[2] = 'registered_leave_license_agreement';
            $mandatory_document[3] = 'maintenance_receipt';
            $mandatory_document[4] = 'rental_agreement';
            $manadatory_requried_status = true;


            $optional_requried_status = true;
            if ($this->request->post['required_optional_document'] == '0') {
                $optional_requried_status = false;
            }

            if ($this->request->post['required_mandatory_document'] == '0') {
                $manadatory_requried_status = false;
            }


            if ($manadatory_requried_status == true) {
                if (count($this->request->post['business_proof']) > 0) {
                    foreach ($this->request->post['business_proof'] as $buss_prof) {
                        if ($buss_prof == '') {
                            $this->session->data['validation_error'] = 'Please select Business Proof';
                            break;
                        }
                    }
                }
            }
            if (count($file_data) > 0) {
                foreach ($file_data as $key => $files) {
                    if ($manadatory_requried_status == true || $optional_requried_status = true) {
                        if (count($files) > 0) {
                            foreach ($files as $k => $file) {
                                if ($file['name'] == '') {
                                    $this->session->data['validation_error'] = 'Please select Business Proof and upload the correct document';
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }


        if (isset($this->session->data['validation_error']) || $this->session->data['validation_error'] != '' || !empty($this->session->data['validation_error'])) {
            $status = true;
        }

        return $status;
    }

    /**
     * getCrmUrl
     * @author kusum Joshi
     * @description return crm url
     * @return string
     */
    public static function getCrmUrl()
    {

        $url = HTTP_SERVER;
        $crm_site_url = 'https://www.wholesalebox.biz/';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] == '127.0.0.1' || $_SERVER['HTTP_X_FORWARDED_FOR'] == '::1')) {
            $crm_site_url = "http://localhost/wsbox-crm/";

        } elseif (strpos($url, 'staging') !== false) {
            $crm_site_url = "https://www.wholesalebox.biz/staging/";
        } elseif (strpos($url, 'wsb.in') !== false || strpos($url, 'localhost') !== false) {
            $crm_site_url = "http://localhost/wsbox-crm/";
        }
        return $crm_site_url;
    }

    public function uploadImages($directory, $filename, $file_tmp)
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
        $target_url = UPLOAD_CONTENT_URL_SSL . 'fileupload.php?directory=' . $directory . '&filename=' . $filename;
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

    public function deleteCreditApplicationImageOnCdnServer($path = NULL)
    {
        $ch = curl_init();
        $post = array('path[]' => $path);
        $target_url = STATIC_CONTENT_URL_SSL . 'filemanager/delete.php';
        //$target_url = 'http://localhost/wholesalebox/cdn/delete.php';
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $result_array = json_decode($result, true);
        if (isset($result_array['success'])) {
            return 1;
        } else {
            return 0;
        }
    }

    public function sendMailAfterCompleteForm($data)
    {

        $mail = new PHPMailer();

        $html = '';
        $html .= '<table border CELLSPACING=0 style="border:0; margin-top:10px">';

        $month_array = array();
        $month_array['01'] = 'Jan';
        $month_array['02'] = 'Feb';
        $month_array['03'] = 'Mar';
        $month_array['04'] = 'Apr';
        $month_array['05'] = 'May';
        $month_array['06'] = 'June';
        $month_array['07'] = 'July';
        $month_array['08'] = 'Aug';
        $month_array['09'] = 'Sep';
        $month_array['10'] = 'Oct';
        $month_array['11'] = 'Nov';
        $month_array['12'] = 'Dec';

        $no_need = array();
        $no_need[0] = 'id';
        $no_need[1] = 'customer_id';
        $no_need[2] = 'is_contact_person_same';
        $no_need[3] = 'declaration';
        $no_need[4] = 'draft';
        $no_need[5] = 'form_action';
        $no_need[6] = 'created_date';
        $no_need[7] = 'entity_name';

        $version_need = array();
        if (CREDIT_APPLICATION_VERSION == '2') {
            $version_need[0] = 'first_name';  //documents
            $version_need[1] = 'middle_name';
            $version_need[2] = 'last_name';
            $version_need[3] = 'father_name';
            $version_need[4] = 'dob';
            $version_need[5] = 'current_pincode';
            $version_need[6] = 'documents';
            $version_need[7] = 'email';
            $version_need[8] = 'phone_no';
            $version_need[9] = 'company_name';
            $version_need[10] = 'pan_no';
            $version_need[11] = 'aadhaar_no';
            $version_need[12] = 'business_start_year';
            $version_need[13] = 'months_in_current_location';
            $version_need[14] = 'gst_number';


        }

        $html .= '<tr>';
        $html .= '<td align="center" colspan="2" style="border:0">Application Detail</td>';
        $html .= '<td colspan="2" style="border:0"></td>';
        $html .= '</tr>';

        $customer_email = '';

        if (CREDIT_APPLICATION_VERSION == '1') {
            foreach ($data as $key => $val) {
                if (!in_array($key, $no_need)) {

                    if ($key == 'documents') {
                        $display = 'style="display:none"';
                    } else {
                        $display = '';
                    }

                    if ($key == 'email') {
                        $customer_email = $val;
                    }

                    if ($key == 'company_name') {
                        $html .= '<tr>';
                        $html .= '<td align="center" colspan="2" style="border:0"><br><br>Business Detail</td>';
                        $html .= '<td colspan="2" style="border:0"></td>';
                        $html .= '</tr>';
                    }

                    if ($key == 'residing_date' || $key == 'permanent_residing_date' || $key == 'occupied_since' || $key == 'business_since') {

                        $explode_val = explode('-', $val);
                        $month = '';
                        if (isset($explode_val[1])) {
                            $month = $month_array[$explode_val[1]] . '/';
                        }
                        $val = $month . $explode_val[0];
                    }

                    $html .= '<tr ' . $display . '>';
                    if ($key == 'company_name') {
                        $html .= '<td>Entity Name</td>';
                    } else if ($key == 'other_business_entity_detail') {
                        $html .= '<td>Other Associate Entity</td>';
                    } else {
                        $html .= '<td>' . ucfirst(str_replace('_', ' ', $key)) . '</td>';
                    }
                    if ($key == 'business_premises' || $key == 'current_resident_premises' || $key == 'permanent_resident_premises') {
                        $html .= '<td>' . ucfirst(str_replace('_', ' ', $val)) . '</td>';
                    } else {
                        $html .= '<td>' . $val . '</td>';
                    }
                    $html .= '</tr>';

                    if ($key == 'documents') {
                        if (count($val) > 0) {
                            foreach ($val as $kkk => $vvv) {

                                $html .= '<tr>';
                                $html .= '<td align="center" colspan="2" style="border:0"><br><br>' . ucfirst(str_replace('_', ' ', $kkk)) . '</td>';
                                $html .= '<td colspan="2" style="border:0"></td>';
                                $html .= '</tr>';


                                foreach ($vvv as $kk => $vv) {

                                    $html .= '<tr>';
                                    $html .= '<td colspan="2" style="border:0"><br>' . ucfirst(str_replace('_', ' ', $kk)) . '</td>';
                                    $html .= '</tr>';

                                    foreach ($vv as $k => $v) {

                                        $html .= '<tr>';
                                        $html .= '<td colspan="2" style="border:0"><a target="_blank" href="' . STATIC_CONTENT_URL_SSL . $v['path'] . '">Click here to view </a></td>';
                                        $html .= '</tr>';

                                        if ($v['document_number'] != '') {
                                            $html .= '<tr>';
                                            $html .= '<td>Document Number</td>';
                                            $html .= '<td>' . $v['document_number'] . '</td>';
                                            $html .= '</tr>';
                                        }

                                        if ($v['expiry_date'] != '') {
                                            $html .= '<tr>';
                                            $html .= '<td>Expiry Date</td>';
                                            $html .= '<td >' . $v['expiry_date'] . '</td>';
                                            $html .= '</tr>';
                                        }


                                    }
                                }

                            }
                        }
                    }
                }
            }
        } else if (CREDIT_APPLICATION_VERSION == '2') {
            foreach ($data as $key => $val) {
                if ($key == 'email') {
                    $customer_email = $val;
                }
                if (in_array($key, $version_need)) {

                    if ($key == 'documents') {
                        $display = 'style="display:none"';
                    } else {
                        $display = '';
                    }

                    if ($key == 'current_pincode') {
                        $html .= '<tr ' . $display . '>';
                        $html .= '<td>Pincode</td>';
                        $html .= '<td width="90%">' . $val . '</td>';
                        $html .= '</tr>';
                    } else if ($key == 'dob') {
                        $html .= '<tr ' . $display . '>';
                        $html .= '<td>Date of Birth</td>';
                        $html .= '<td>' . date("d-m-Y", strtotime($val)) . '</td>';
                        $html .= '</tr>';
                    } else if ($key == 'company_name') {
                        $html .= '<tr ' . $display . '>';
                        $html .= '<td>Proprietary Firm name</td>';
                        $html .= '<td>' . $val . '</td>';
                        $html .= '</tr>';
                    } else if ($key == 'months_in_current_location') {
                        $html .= '<tr ' . $display . '>';
                        $html .= '<td>How long (in months)</td>';
                        $html .= '<td>' . $val . '</td>';
                        $html .= '</tr>';
                    } else if ($key != 'documents') {
                        $html .= '<tr ' . $display . '>';
                        $html .= '<td>' . ucfirst(str_replace('_', ' ', $key)) . '</td>';
                        $html .= '<td>' . $val . '</td>';
                        $html .= '</tr>';

                    }

                    if ($key == 'documents') {

                        if (count($val) > 0) {
                            //application_document

                            foreach ($val as $kkk => $vvv) {
                                if ($kkk == 'application_document') {
                                    $html .= '<tr>';
                                    $html .= '<td align="center" colspan="2" style="border:0"><br><br>' . ucfirst(str_replace('_', ' ', $kkk)) . '</td>';
                                    $html .= '<td colspan="2" style="border:0"></td>';
                                    $html .= '</tr>';

                                    if (count($vvv) > 0) {


                                        foreach ($vvv as $kk => $vv) {
                                            if ($kk == 'pancard' || $kk == 'aadhaar_card' || $kk == 'six_months_bank_statement' || $kk == 'shop_photo' || $kk == 'selfie_with_shop') {
                                                $html .= '<tr>';
                                                $html .= '<td colspan="2" style="border:0"><br>' . ucfirst(str_replace('_', ' ', $kk)) . '</td>';
                                                $html .= '</tr>';

                                                foreach ($vv as $k => $v) {
                                                    $html .= '<tr>';
                                                    $html .= '<td colspan="2" style="border:0"><a target="_blank" href="' . STATIC_CONTENT_URL_SSL . $v['path'] . '">Click here to view </a></td>';
                                                    $html .= '</tr>';

                                                    if (isset($v['document_number']) && $v['document_number'] != '') {
                                                        $html .= '<tr>';
                                                        $html .= '<td>Document Number</td>';
                                                        $html .= '<td>' . $v['document_number'] . '</td>';
                                                        $html .= '</tr>';
                                                    }

                                                    if (isset($v['document_number']) && $v['expiry_date'] != '') {
                                                        $html .= '<tr>';
                                                        $html .= '<td>Expiry Date</td>';
                                                        $html .= '<td >' . $v['expiry_date'] . '</td>';
                                                        $html .= '</tr>';
                                                    }

                                                }
                                            }

                                        }
                                    }

                                }

                            }
                        }
                    }
                }
            }
        }


        $html .= '</table>';

        $phone_no = $data['phone_no'];
        $name = $data['first_name'];
        if (!empty($data['middle_name'])) {
            $name .= ' ' . $data['middle_name'];
        }
        $name .= ' ' . $data['last_name'];

        $subject = "Credit request from " . $phone_no;

        $body = "";
        $body .= "Dear " . $name . "<br>";
        // $body .= "Following customer has placed a request for credit. Please assist. Find following information.<br>";
        $body .= "Please find below information submitted to place a request for Credit. Let us know, if any correction is needed. <br>";
        // $body .= "Customer Name: " . $name . "<br>";
        // $body .= "Customer Phone No: " . $phone_no . "<br>";
        $body .= $html;
        $mail->isSMTP();
        //$mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->SetFrom(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addReplyTo(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        $mail->addAddress(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
        if (!empty($customer_email)) {
            $mail->addAddress($customer_email, $name);
        }
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(true);
        $mail = $mail->Send();

    }


    /**
     * uploadDocument
     * @author Rahul Singh
     * @description upload document vai ajax
     * @return status and file path
     * Date 4 Jan 2019
     */
    public function uploadDocument()
    {

        //create file array
        $file_data = array();
        if (count($this->request->files) > 0) {
            foreach ($this->request->files as $key => $val) {
                $file_data[$key] = $this->reArrayFiles($val);

            }
        }

        $this->load->model('account/credit_application');
        $this->load->model('lead/lead');

        //upload document
        $this->request->post['document_type'] = 'application_document';
        if (count($file_data) > 0) {

            if (SITE_ENVIRONMENT == 'Production') {
                $customer_folder_name = $this->request->get['customer_id'];
            } else {
                $customer_folder_name = 'staging_' . $this->request->get['customer_id'];
            }

            if ($this->request->get['customer_id'] == '0') {
                $customer_folder_name = $customer_folder_name . '_' . $this->request->get['credit_application_id'];
            }

            $uploads_dir = 'credit_application/' . $customer_folder_name . '/application_document/';

            foreach ($file_data as $j => $files) {
                foreach ($files as $k => $file) {
                    if (!empty($file['tmp_name'])) {
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $file_name = $j . '_' . time() . '.' . $extension;
                        $result = $this->uploadImages($uploads_dir, $file_name, $file['tmp_name']);
                        if ($result) {
                            $file_data[$j][$k]['name'] = $file_name;
                            $this->request->post['customer_id'] = $this->request->get['customer_id'];
                            if (!empty($this->request->get['credit_application_id'])) {
                                $this->request->post['credit_application_id'] = $this->request->get['credit_application_id'];
                            }

                            if (!empty($this->request->get['khufiya_user_id'])) {
                                $this->request->post['khufiya_user_id'] = $this->request->get['khufiya_user_id'];
                            }

                            if (!empty($this->request->get['crm_user_id'])) {
                                $this->request->post['crm_user_id'] = $this->request->get['crm_user_id'];
                            }
                            $this->request->post['draft'] = '2';
                            $type = 'ajax';
                            $result_resonse = $this->model_account_credit_application->saveCreditDocument($file_data, $this->request->post, 2, $type);
                            if (!empty($result_resonse['last_image_id'])) {
                                $res = json_encode($result_resonse);
                                if(empty($this->request->get['khufiya_user_id']) && !empty($this->request->get['customer_id']) && !empty($result_resonse['file_path'])){
                                    $file_path['0']=$result_resonse['file_path'];
                                    $customer_id=$this->request->get['customer_id'];
                                    $lead_detail=$this->model_lead_lead->getLeadIdUsingCustomerInCrm($customer_id);

                                    $customer_name=$this->model_account_credit_application->getCreditApplicationCustomerName($customer_id);
                                    if(!empty(trim($customer_name))){
                                        $notification_name=$customer_name;
                                      }else{
                                        $notification_name='Customer';
                                      }
                                    if(!empty($lead_detail['lead_id']) && !empty($lead_detail['user_id'])){
                                        $lead_id=$lead_detail['lead_id'];
                                        $lead_user_id=$lead_detail['user_id'];
                                        $message='Document upload on credit form';
                                        $title=$notification_name.' upload document on credit form';
                                        $action=CRM_URL.'leads/view/'.$lead_id;
                                        $this->model_account_credit_application->sendPushNotificationAgent($customer_id,$lead_id,$lead_user_id,$action,$message,$title);
                                    }
                                    
                                }
                                echo $res;
                                exit;
                            } else {
                                exit;
                            }
                        }
                    }
                }
            }
        }

    }


    /**
     * phoneValidate
     * @author Mahaveer Choudhary
     * @description Check phone number allready register or not
     * @return boolen true/false
     * Date 05 fab 2019
     */
    public function phoneOrEmailValidate()
    {
        $value = $this->request->get['value'];
        $type = $this->request->get['type'];
        $credit_application_id = $this->request->get['credit_application_id'];

        $this->load->model('account/credit_application');

        if ($credit_application_id) {
            $resonse = $this->model_account_credit_application->CheckPhoneOrEmailIsRegisterInEdit($type, $value, $credit_application_id);
        } else {
            $resonse = $this->model_account_credit_application->CheckPhoneOrEmailIsRegister($type, $value);
        }

        if ($resonse > 0) {
            echo 0;
        } else {
            echo 1;
        }
        exit;
    }

    public function createQueryString($request)
    {
        $query_string = '';
        if (isset($request['customer_id']) && $request['customer_id'] != '') {
            $query_string .= '&customer_id=' . $request['customer_id'];
        } else
            if (isset($request['ctoken']) && $request['ctoken'] != '') {
                $query_string .= '&customer_id=' . $this->customer->getId();
            } else {
                if (!empty($request['customer_id'])) {
                    $query_string .= '&customer_id=' . $request['customer_id'];
                }
            }

        if (!empty($request['token']) && $request['token'] != '') {
            $query_string .= '&token=' . $request['token'];
        }
        if (empty($this->request->get['ctoken']) && !empty($request['ctoken']) && $request['ctoken'] != '') {
            $query_string .= '&ctoken=' . $request['ctoken'];
        }

        if (!empty($request['crm_user_id']) && $request['crm_user_id'] != '') {
            $query_string .= '&crm_user_id=' . $request['crm_user_id'];
        }

        if (!empty($request['crm_user_password']) && $request['crm_user_password'] != '') {
            $query_string .= '&crm_user_password=' . $request['crm_user_password'];
        }

        if (!empty($request['khufiya_user_id']) && $request['khufiya_user_id'] != '') {
            $query_string .= '&khufiya_user_id=' . $request['khufiya_user_id'];
        }

        if (!empty($request['source'])) {
            $query_string .= '&source=' . $request['source'];
        }

        if (!empty($request['credit_application_id']) && $request['credit_application_id'] != '') {
            $query_string .= '&credit_application_id=' . $request['credit_application_id'];
        }


        if (!empty($data['app_language'])) {
            $query_string .= '&language=' . $data['app_language'];
        }
        return $query_string;
    }

    public function success(){

            $credit_language = array();
            $this->load->autoLoadLanguage('account/credit_application', $credit_language);
            $data['credit_language'] = $credit_language;
                //===================== LANGUGE START ====================//
    // vernacular language for app
    if (!empty($this->request->get['language'])) {
            $data['app_language'] = $this->request->get['language'];
            $this->session->data['app_language'] = $data['app_language'];
            $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
        }
        else
        if (!empty($this->session->data['app_language'])) {
            $data['app_language'] = $this->session->data['app_language'];
            $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
        }
        else {
            $data['app_language'] = $this->config->get('config_language_id');
        }
      //===================== LANGUGE END ====================//

        $query_string = '';
        if (isset($this->request->get['customer_id']) && $this->request->get['customer_id'] != '') {
            $query_string .= '&customer_id=' . $this->request->get['customer_id'];
        } else
            if (isset($this->request->get['ctoken']) && $this->request->get['ctoken'] != '') {
                $query_string .= '&customer_id=' . $this->customer->getId();
            } else {
                $query_string .= '&customer_id=' . $request['customer_id'];
            }

        if (!empty($this->request->get['token']) && $this->request->get['token'] != '') {
            $query_string .= '&token=' . $this->request->get['token'];
        }

        if (!empty($this->request->get['crm_user_id']) && $this->request->get['crm_user_id'] != '') {
            $query_string .= '&crm_user_id=' . $this->request->get['crm_user_id'];
        }

        if (!empty($this->request->get['crm_user_password']) && $this->request->get['crm_user_password'] != '') {
            $query_string .= '&crm_user_password=' . $this->request->get['crm_user_password'];
        }

        if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '') {
            $query_string .= '&khufiya_user_id=' . $this->request->get['khufiya_user_id'];
        }

        if (!empty($this->request->get['source'])) {
            $query_string .= '&source=' . $this->request->get['source'];
        }

        if (!empty($this->request->get['credit_application_id']) && $this->request->get['credit_application_id'] != '') {
            $query_string .= '&credit_application_id=' . $this->request->get['credit_application_id'];
        }

        if (!empty($this->request->get['khufiya_user_id']) && $this->request->get['khufiya_user_id'] != '' && $this->request->get['khufiya_user_id'] != '0') {
            $data['khufiya_user_id'] = $this->request->get['khufiya_user_id'];
        } else {
            $data['khufiya_user_id'] = '';
        }

        if (!empty($this->request->get['ctoken'])) {
            $data['ctoken'] = $this->request->get['ctoken'];
        }

        if (!empty($this->request->get['draft'])) {
            $data['draft'] = $this->request->get['draft'];
        }

        if (!empty($data['app_language'])) {
            $query_string .= '&language=' . $data['app_language'];
        }
        $this->load->model('account/credit_application');
        if (!empty($this->request->get['success_type']) && !empty($this->request->get['customer_id']) && $this->request->get['success_type'] == '1') {

            $request['customer_id'] = $this->request->get['customer_id'];

            $data['back_button'] = $this->url->link('account/credit_application&mode=edit&form_type=1' . $query_string, '', 'SSL');

            $data['next_button'] = $this->url->link('account/credit_application&mode=edit&form_type=2' . $query_string, '', 'SSL');

            $data['home'] = $this->url->link('common/home', '', 'SSL');

            $request['customer_id'] = $this->request->get['customer_id'];
            $credit_application_activation_status = $this->model_account_credit_application->creditApplicationStatusForBanner($request['customer_id']);

            $wsb_credit_payment = new WsbCreditPayment($this);
            $customer_id = array($request['customer_id']);
            $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
            if (!empty($limit_data[$request['customer_id']])) {
                $limit = $limit_data[$request['customer_id']];
            } else {
                $limit = '0';
            }
            if (!empty($credit_application_activation_status['gst_number'])) {
                $pre_approved_limit = '25000';
            } else {
                $pre_approved_limit = '10000';
            }
            $data['approval_message'] = 'You have been pre-approved for credit of Rs. ' . $pre_approved_limit . '/- @ 0% interest for 30 days. For activation and higher limit, please complete step 2 and upload all your KYC documents';

            $data["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

            $data["documents_to_be_couriered"] = array("Agreement", "NACH Form", "PDC", "Self Attested PAN Copy", "Self Attested ID Proof");

            $data["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';
            $data['data_save'] = 1;


            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_form_success.tpl';

        } else if (!empty($this->request->get['success_type']) && !empty($this->request->get['customer_id']) && $this->request->get['success_type'] == '2') {

            $request['customer_id'] = $this->request->get['customer_id'];

            $data['back_button'] = $this->url->link('account/credit_application&mode=edit&form_type=1' . $query_string, '', 'SSL');

            $data['next_button'] = $this->url->link('account/credit_application&mode=edit&form_type=2' . $query_string, '', 'SSL');

            $data['home'] = $this->url->link('account/order', '', 'SSL');

            $request['customer_id'] = $this->request->get['customer_id'];
            $credit_application_activation_status = $this->model_account_credit_application->creditApplicationStatusForBanner($request['customer_id']);

            $wsb_credit_payment = new WsbCreditPayment($this);
            $customer_id = array($request['customer_id']);
            $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
            if (!empty($limit_data[$request['customer_id']])) {
                $limit = $limit_data[$request['customer_id']];
            } else {
                $limit = '0';
            }

            if (!empty($credit_application_activation_status['message'])) {
                $approval_message = $credit_application_activation_status['message'];
            } else {
                $approval_message = "Your credit limit is now active for Rs. " . $limit . "/-. Please use the “Order on WholesaleBox credit” payment method at the time of order placement.\n\nAvailable limit: Rs. " . $limit . "/-";
            }
            if (!empty($credit_application_activation_status['gst_number'])) {
                $pre_approved_limit = '25000';
            } else {
                $pre_approved_limit = '10000';
            }


            $data["activation_message"] = $approval_message;

            $data["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

            $data["documents_to_be_couriered"] = array("Agreement", "NACH Form", "PDC", "Self Attested PAN Copy", "Self Attested ID Proof");

            $data["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';
            $data['data_save'] = 2;


            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_form_success.tpl';

        } else if (!empty($this->request->get['success_type']) && !empty($this->request->get['customer_id']) && $this->request->get['success_type'] == '3') {

            $request['customer_id'] = $this->request->get['customer_id'];

            $data['back_button'] = $this->url->link('account/credit_application&mode=edit&form_type=1' . $query_string, '', 'SSL');

            $data['next_button'] = $this->url->link('account/credit_application&mode=edit&form_type=2' . $query_string, '', 'SSL');

            $data['home'] = $this->url->link('account/order', '', 'SSL');

            $request['customer_id'] = $this->request->get['customer_id'];
            $credit_application_activation_status = $this->model_account_credit_application->creditApplicationStatusForBanner($request['customer_id']);

            $wsb_credit_payment = new WsbCreditPayment($this);
            $customer_id = array($request['customer_id']);
            $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
            if (!empty($limit_data[$request['customer_id']])) {
                $limit = $limit_data[$request['customer_id']];
            } else {
                $limit = '0';
            }

            if (!empty($credit_application_activation_status['message'])) {
                $approval_message = $credit_application_activation_status['message'];
            } else {
                $approval_message = "Your credit limit is now active for Rs. " . $limit . "/-. Please use the “Order on WholesaleBox credit” payment method at the time of order placement.\n\nAvailable limit: Rs. " . $limit . "/-";
            }
            if (!empty($credit_application_activation_status['gst_number'])) {
                $pre_approved_limit = '25000';
            } else {
                $pre_approved_limit = '10000';
            }


            $data["activation_message"] = $approval_message;

            $data["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

            $data["documents_to_be_couriered"] = array("Agreement", "NACH Form", "PDC", "Self Attested PAN Copy", "Self Attested ID Proof");

            $data["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';
            $data['data_save'] = 2;


            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_form_success.tpl';

        } else if (empty($this->request->get['customer_id'])) {
            $request['customer_id'] = $this->request->get['customer_id'];

            $data['back_button'] = $this->url->link('account/credit_application&mode=edit&form_type=1' . $query_string, '', 'SSL');

            $data['login_btton'] = $this->url->link('account/login', '', 'SSL');

            $data['home'] = $this->url->link('account/order', '', 'SSL');
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';
            $data['data_save'] = 4;


            $file_path = DIR_TEMPLATE_MOBILE . $this->config->get('config_template') . '/template/account/credit_form_success.tpl';

        }
        if (file_exists($file_path)) {
            $this->response->setOutput($this->load->view($file_path, $data, true));
        } else {
            $this->response->redirect($this->url->link('account/order'));
        }
    }

    /**
     * sendMailAfterUserSignUp
     * @author Rahul Singh
     * @description send Mail After User SignUp
     * Date 11 Jan 2019
     */
    public function sendMailAfterUserSignUp($data, $password)
    {

        $mail = new PHPMailer();

        $email = $data['email'];
        $phone_no = $data['phone_no'];
        $name = $data['first_name'];
        if (!empty($data['middle_name'])) {
            $name .= ' ' . $data['middle_name'];
        }
        $name .= ' ' . $data['last_name'];

        $subject = "WholesaleBox login details";

        $body = "";
        $body .= "Dear " . $name . "<br/><br/>";
        $body .= "Thanks for signup on Wholesalebox. You can login with following credentials: <br/><br/>";
        $body .= "Phone No: " . $phone_no . "<br/><br/>";
        $body .= "Password: " . $password . "<br/><br/><br/><br/>";
        $body .= "Regards,<br/>";
        $body .= "Wholesalebox Team<br/>";
        $mail->isSMTP();
        //$mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->SetFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
        $mail->addAddress($email, $name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(true);
        $mail = $mail->Send();

    }

}