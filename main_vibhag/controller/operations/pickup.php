<?php
class ControllerOperationsPickup extends Controller {
	private $error = array();
	public function index() {

                $this->load->language('operations/picker');

		$this->document->setTitle($this->language->get('heading_title'));

		// load model
		$this->load->model('pickers/pickers');

		$this->getList(); 
	}

	protected function getList() {
            
            $data = array();
                
            //get filter
            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = null;
            }
            if (isset($this->request->get['filter_pickup_city_code'])) {
                $filter_pickup_city_code = $this->request->get['filter_pickup_city_code'];
            } else {
                $filter_pickup_city_code = null;
            }
            if (isset($this->request->get['filter_pickup_city'])) {
                $filter_pickup_city = $this->request->get['filter_pickup_city'];
            } else {
                $filter_pickup_city = null; 
            }
            if (isset($this->request->get['filter_status'])) {
                $filter_status = $this->request->get['filter_status'];
            } else {
                $filter_status = null;  
            }
            if (isset($this->request->get['filter_seller'])) {
                $filter_seller = $this->request->get['filter_seller'];
            } else {
                $filter_seller = null;    
            }
            
            $data['filter_name'] = $filter_name;
            $data['filter_pickup_city_code'] = $filter_pickup_city_code;
            $data['filter_pickup_city'] = $filter_pickup_city;
            $data['filter_status'] = $filter_status;
            $data['filter_seller'] = $filter_seller;
            
            
            if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
            } else {
                    $page = 1;
            }        
                
       
            // General URL (without sort or page)
            $url = '';

            if (isset($this->request->get['filter_name'])) {
                    $url .= '&filter_name=' . $this->request->get['filter_name'];
            }    
            if (isset($this->request->get['filter_page_limit'])) {
                    $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
            } 
            if (isset($this->request->get['filter_pickup_city_code'])) {
                    $url .= '&filter_pickup_city_code=' . $this->request->get['filter_pickup_city_code'];
            }        
            if (isset($this->request->get['filter_pickup_city'])) {
                    $url .= '&filter_pickup_city=' . $this->request->get['filter_pickup_city'];
            } 
            if (isset($this->request->get['filter_status'])) {
                    $url .= '&filter_status=' . $this->request->get['filter_status']; 
            }
            if (isset($this->request->get['filter_seller'])) {
                    $url .= '&filter_seller=' . $this->request->get['filter_seller']; 
            }
        
            
            // URL for General links to ensure we reach same settings again on the list page
            $general_url = $url;
            if (isset($this->request->get['page'])) {
                    $general_url .= '&page=' . $this->request->get['page'];
            } 

	    // Autoloading the lanugage
            $this->load->autoLoadLanguage('operations/picker', $data);
            
            
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
            
            if (isset($this->session->data['delete_success'])) {
                    $data['delete_success'] = $this->session->data['delete_success'];
                    unset($this->session->data['delete_success']);
            } else {
                    $data['delete_success'] = '';
            }
             
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                    'text' => $data['text_home'],
                    'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                    'text' => $data['heading_title'],
                    'href' => $this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
            );

            $data['token'] = $this->session->data['token'];

                if (isset($this->request->get['filter_page_limit'])) {
			$filter_page_limit = $this->request->get['filter_page_limit'];
		} else {
			$filter_page_limit = $this->config->get('config_limit_admin');
		}
		
                $data['page_start_index']= (($page-1) * $filter_page_limit) + 1; 
                $data['filter_page_limit']= $filter_page_limit; 

                // URL for pagination
		$pagination_url = $url; 
                
                $data['delete'] = $this->url->link('operations/pickup/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
                $data['add'] = $this->url->link('operations/pickup/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
                $data['all_pickup_list'] = $this->url->link('operations/pickup/all_pickup_list', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
                
                $filter_data = array(
                        'filter_name'	  		=> $filter_name,
                        'filter_pickup_city_code'	=> $filter_pickup_city_code,
                        'filter_pickup_city'	  	=> $filter_pickup_city,
                        'filter_status'                 => $filter_status, 
                        'filter_seller'       => $filter_seller, 
                        'start'           => ($page - 1) * $filter_page_limit,
			'limit'           => $filter_page_limit
		);
                
                
                $pickers = $this->model_pickers_pickers->getPickerLists($filter_data);
                
                $data['pickers'] = array();
                foreach ($pickers as $result) {
                
                    $data['pickers'][] = array(
				'id' 	=> $result['picker_id'],
				'first_name' 	=> $result['first_name'],
				'last_name' 	=> $result['last_name'],
				'pickup_city' 	=> $result['pickup_city'],
				'phone_no' 	=> $result['phone_no'],
				'email' 	=> $result['email'],
				'pickup_city_code' 	=> $result['pickup_city_code'],
				'status' 	=> $result['status'],
				'created' 	=> $result['created'],
				'modified' 	=> $result['modified'],
				'edit'       => $this->url->link('operations/pickup/edit', 'token=' . $this->session->data['token'] . '&picker_id=' . $result['picker_id'] . $url, 'SSL'),
				'delete'       => $this->url->link('operations/pickup/delete', 'token=' . $this->session->data['token'] . '&picker_id=' . $result['picker_id'] . $url, 'SSL'),
				
			);
                }
                $data['no_records'] = $data['text_no_records'];
                
                $piker_total = $this->model_pickers_pickers->getTotalPickers($filter_data);
                $data['piker_total'] = $piker_total;
                $pagination = new Pagination();
                $pagination->total = $piker_total;
		$pagination->page = $page;
		$pagination->limit = $filter_page_limit;
		$pagination->url = $this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
                $data['results'] = sprintf($data['text_pagination'],
									($piker_total) ? (($page - 1) * $filter_page_limit) + 1 : 0,
									((($page - 1) * $filter_page_limit) > ($piker_total - $filter_page_limit)) ? $piker_total : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
										$piker_total, ceil($piker_total / $filter_page_limit));
                
		
                $data['page_limit_array'] = array('30','60','100','200','500','1000');
                
                //seller list
                $data['seller_list'] = $this->model_pickers_pickers->getSellerList();
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('operations/pickup_list.tpl', $data));
	}
        
    public function all_pickup_list()
    {
        $this->load->language('operations/picker');
        $this->load->model('pickers/pickers');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = array();

        // Autoloading the lanugage
        $this->load->autoLoadLanguage('operations/picker', $data);
            
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
            
        if (isset($this->session->data['delete_success'])) {
                    $data['delete_success'] = $this->session->data['delete_success'];
                    unset($this->session->data['delete_success']);
        } else {
                    $data['delete_success'] = '';
        }
             
        $data['breadcrumbs'] = array();
        $general_url = '';

        $data['breadcrumbs'][] = array(
                    'text' => $data['text_home'],
                    'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

        $data['breadcrumbs'][] = array(
                    'text' => $data['heading_title'],
                    'href' => $this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

       $data['add'] = $this->url->link('operations/pickup/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
       
       $data['all_pickup_list'] = $this->url->link('operations/pickup/all_pickup_list', 'token=' . $this->session->data['token'] . $general_url, 'SSL');

       $data['token'] = $this->session->data['token'];

       $data['picker_cities'] = $this->model_pickers_pickers->getPickerCities();
             
       $pickers = $this->model_pickers_pickers->getAllPickerInfo();
                
       $data['pickers'] = array();

        foreach ($pickers as $result) 
        {
          $data['pickers'][$result['pickup_city_code']][] = array(
                'id'    => $result['id'],
                'first_name'    => $result['first_name'],
                'last_name'     => $result['last_name'],
                'pickup_city'   => $result['pickup_city'],
                'phone_no'  => $result['phone_no'],
                'email'     => $result['email'],
                'pickup_city_code'  => $result['pickup_city_code'],
                'status'    => $result['status'],
                'created'   => $result['created'],
                'modified'  => $result['modified'],
                'seller_list' =>  $result['seller'],
                'assign_seller' =>  $result['assign_seller'],
                'self_assign_seller' =>  $result['self_assign_seller']
            );
        }

        $data['no_records'] = $data['text_no_records'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('operations/all_pickup_list.tpl', $data));
        
    }

    public function add() {
		$this->load->language('operations/picker');
                
                //load model
                $this->load->model('pickers/pickers');
                
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
                    
                        $url = '';
                        if (isset($this->request->get['filter_name'])) {
                            $url .= '&filter_name=' . $this->request->get['filter_name'];
                        }    
                        if (isset($this->request->get['filter_page_limit'])) {
                            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                        } 
                        if (isset($this->request->get['filter_pickup_city_code'])) {
                            $url .= '&filter_pickup_city_code=' . $this->request->get['filter_pickup_city_code'];
                        }        
                        if (isset($this->request->get['filter_pickup_city'])) {
                            $url .= '&filter_pickup_city=' . $this->request->get['filter_pickup_city'];
                        } 
                        if (isset($this->request->get['filter_status'])) {
                            $url .= '&filter_status=' . $this->request->get['filter_status']; 
                        }
                        if (isset($this->request->get['filter_seller'])) {
                            $url .= '&filter_seller=' . $this->request->get['filter_seller']; 
                        }
                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];  
                        } 
                    
                        $this->model_pickers_pickers->addPicker($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
        
        
        public function edit() {
            
                $this->load->language('operations/picker');
                
                //load model
                $this->load->model('pickers/pickers');
                
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
                        
                        $url = '';
                        if (isset($this->request->get['filter_name'])) {
                            $url .= '&filter_name=' . $this->request->get['filter_name'];
                        }    
                        if (isset($this->request->get['filter_page_limit'])) {
                            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                        } 
                        if (isset($this->request->get['filter_pickup_city_code'])) {
                            $url .= '&filter_pickup_city_code=' . $this->request->get['filter_pickup_city_code'];
                        }        
                        if (isset($this->request->get['filter_pickup_city'])) {
                            $url .= '&filter_pickup_city=' . $this->request->get['filter_pickup_city'];
                        } 
                        if (isset($this->request->get['filter_status'])) {
                            $url .= '&filter_status=' . $this->request->get['filter_status']; 
                        }
                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];  
                        } 
                        if (isset($this->request->get['filter_seller'])) {
                            $url .= '&filter_seller=' . $this->request->get['filter_seller']; 
                        }
                        //echo $url; die;
                    
                        $this->model_pickers_pickers->editPicker($this->request->get['picker_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
        
        public function delete() {
                
		$this->load->language('operations/picker');
                
                //load model
                $this->load->model('pickers/pickers');
                
		$this->document->setTitle($this->language->get('heading_title'));
                
                if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validateDelete()) {
                        
                        $this->model_pickers_pickers->deletePicker($this->request->get['picker_id'], $this->request->post);

			$this->session->data['delete_success'] = $this->language->get('text_delete_success');
                        
                        $url = '';
                        if (isset($this->request->get['filter_name'])) {
                            $url .= '&filter_name=' . $this->request->get['filter_name'];
                        }    
                        if (isset($this->request->get['filter_page_limit'])) {
                            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                        } 
                        if (isset($this->request->get['filter_pickup_city_code'])) {
                            $url .= '&filter_pickup_city_code=' . $this->request->get['filter_pickup_city_code'];
                        }        
                        if (isset($this->request->get['filter_pickup_city'])) {
                            $url .= '&filter_pickup_city=' . $this->request->get['filter_pickup_city'];
                        } 
                        if (isset($this->request->get['filter_status'])) {
                            $url .= '&filter_status=' . $this->request->get['filter_status']; 
                        }
                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];  
                        } 
                        
                        

			$this->response->redirect($this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
                $this->getList();
	}
        
        
        
        protected function getForm() {
            
            $data = array(); 
            // Autoloading the lanugage
            $this->load->autoLoadLanguage('operations/picker', $data);
            
            $data['text_form'] = !isset($this->request->get['picker_id']) ? $data['text_add'] : $data['text_edit'];
            
            //handel errors
            if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            } else {
                $data['error_warning'] = ''; 
            }
            
            if (isset($this->error['first_name'])) {
                $data['error_first_name'] = $this->error['first_name'];
            }else{
                $data['error_first_name'] = '';
            }
            
            if (isset($this->error['last_name'])) {
                $data['error_last_name'] = $this->error['last_name'];
            }else{
                $data['error_last_name'] = ''; 
            }
            
            if (isset($this->error['phone'])) {
                $data['error_phone'] = $this->error['phone'];
            }else{
                $data['error_phone'] = ''; 
            }
            
            if (isset($this->error['email'])) {
                $data['error_email'] = $this->error['email'];
            }else{
                $data['error_email'] = ''; 
            }

            if (isset($this->error['password'])) {
                $data['error_password'] = $this->error['password'];
            }else{
                $data['error_password'] = ''; 
            }
            
            if (isset($this->error['confirm_password'])) {
                $data['error_confirm_password'] = $this->error['confirm_password'];
            }else{
                $data['error_confirm_password'] = ''; 
            }                       
            
            if (isset($this->error['device_id'])) {
                $data['error_device_id'] = $this->error['device_id'];
            }else{
                $data['error_device_id'] = ''; 
            }

            if (isset($this->error['pickup_city_code'])) {
                $data['error_pickup_city_code'] = $this->error['pickup_city_code'];
            }else{
                $data['error_pickup_city_code'] = ''; 
            }
            
            if (isset($this->error['pickup_city'])) {
                $data['error_pickup_city'] = $this->error['pickup_city'];
            }else{
                $data['error_pickup_city'] = ''; 
            }
            
            if (isset($this->error['status'])) {
                $data['error_status'] = $this->error['status'];
            }else{
                $data['error_status'] = ''; 
            }
            
            if (isset($this->error['seller'])) {
                $data['error_seller'] = $this->error['seller'];
            }else{
                $data['error_seller'] = ''; 
            }
            
            
            //get data
            if (isset($this->request->post['first_name'])) {
                $data['first_name'] = $this->request->post['first_name'];
            } else {
                $data['first_name'] = '';
                $data['error_first_name'] = '';
            }
            
            if (isset($this->request->post['last_name'])) {
                $data['last_name'] = $this->request->post['last_name'];
            } else {
                $data['last_name'] = '';
            }
            
            if (isset($this->request->post['phone'])) {
                $data['phone'] = $this->request->post['phone'];
            } else {
                $data['phone'] = '';
            }
            
            if (isset($this->request->post['email'])) {
                $data['email'] = $this->request->post['email'];
            } else {
                $data['email'] = '';
            }

             if (isset($this->request->post['password'])) {
                $data['password'] = $this->request->post['password'];
            } else {
                $data['password'] = '';
            }  
            
            if (isset($this->request->post['confirm_password'])) {
                $data['confirm_password'] = $this->request->post['confirm_password'];
            } else {
                $data['confirm_password'] = '';
            } 

            if (isset($this->request->post['device_id'])) {
                $data['device_id'] = $this->request->post['device_id'];
            } else {
                $data['device_id'] = '';
            }

            if (isset($this->request->post['pickup_city_code'])) {
                $data['pickup_city_code'] = $this->request->post['pickup_city_code'];
            } else {
                $data['pickup_city_code'] = '';
            }
            
            if (isset($this->request->post['pickup_city'])) {
                $data['pickup_city'] = $this->request->post['pickup_city'];
            } else {
                $data['pickup_city'] = '';
            }
            
            if (isset($this->request->post['status'])) {
                $data['status'] = $this->request->post['status'];
            } else {
                $data['status'] = '';
            }
            
            if (isset($this->request->post['seller']) && isset($this->request->get['picker_id']) ) {
                //$data['picker_seller_list'] = $this->request->post['seller'];
                $picker_info = $this->model_pickers_pickers->getPickerById($this->request->get['picker_id']);
                $data['picker_seller_list'] = $picker_info['seller'];
            } else {
                $data['picker_seller_list'] = array();
            }
            
            $url = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . $this->request->get['filter_name'];
            }    
            if (isset($this->request->get['filter_page_limit'])) {
                $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
            } 
            if (isset($this->request->get['filter_pickup_city_code'])) {
                $url .= '&filter_pickup_city_code=' . $this->request->get['filter_pickup_city_code'];
            }        
            if (isset($this->request->get['filter_pickup_city'])) {
                $url .= '&filter_pickup_city=' . $this->request->get['filter_pickup_city'];
            } 
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status']; 
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
                    'href' => $this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $url, 'SSL')
            );
            
            if ($this->error && !isset($this->error['warning'])) {
                $this->error['warning'] = $data['error_warning'];
            }
            
            if (!isset($this->request->get['picker_id'])) {
                    $data['action'] = $this->url->link('operations/pickup/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
            } else {
                    $data['action'] = $this->url->link('operations/pickup/edit', 'token=' . $this->session->data['token'] . '&picker_id=' . $this->request->get['picker_id'] . $url, 'SSL');
            }
            
            
            $data['seller_list'] = array();
            $data['picker_id'] = '';
            
            // Get picker_detail for edit case 
            if (isset($this->request->get['picker_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                $picker_info = $this->model_pickers_pickers->getPickerById($this->request->get['picker_id']);
                $data['picker_id'] = $this->request->get['picker_id'];
                $data['first_name'] = $picker_info['first_name'];
                $data['last_name'] = $picker_info['last_name'];
                $data['phone'] = $picker_info['phone_no'];
                $data['password'] = $picker_info['password'];
                $data['device_id'] = $picker_info['device_id'];
                $data['email'] = $picker_info['email'];
                $data['pickup_city'] = $picker_info['pickup_city'];
                $data['pickup_city_code'] = $picker_info['pickup_city_code'];
                $data['status'] = $picker_info['status'];
                //get all seller realted to city
                $seller_list = $this->model_pickers_pickers->getSellerByPickupCityCode($picker_info['pickup_city_code']);
                $data['seller_list'] = $seller_list; 
                $data['picker_seller_list'] = $picker_info['seller'];
                $data['assign_seller_list'] = $picker_info['assign_seller'];
                $data['self_assign_seller'] = $picker_info['self_assign_seller']; 
                
            }elseif( isset($this->request->post['pickup_city_code']) ){ 
                $data['seller_list'] = $this->model_pickers_pickers->getSellerByPickupCityCode($this->request->post['pickup_city_code']);
            }
            
            $data['cancel'] = $this->url->link('operations/pickup', 'token=' . $this->session->data['token'] . $url, 'SSL');
            $data['token'] = $this->session->data['token'];
            
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            
            $this->response->setOutput($this->load->view('operations/picker_form.tpl', $data));
        }
        
        protected function validateForm() {
            
                // load model
                $this->load->model('pickers/pickers');
            
                $data = array(); 
                // Autoloading the lanugage
                $this->load->autoLoadLanguage('operations/picker', $data);   
                
                if (!$this->user->hasPermission('modify', 'operations/pickup')) { 
		          	$this->error['warning'] = $data['error_permission'];
		          }

                if ( (!ctype_alpha ( trim($this->request->post['first_name'] ))) || (utf8_strlen(trim($this->request->post['first_name'])) < 3) || (utf8_strlen(trim($this->request->post['first_name'])) > 15)  ){
                    $this->error['first_name'] = $data['error_first_name'];
                }
                
                 
                if ( (!ctype_alpha ( trim($this->request->post['last_name'] ))) || (utf8_strlen(trim($this->request->post['last_name'])) < 3) || (utf8_strlen(trim($this->request->post['last_name'])) > 15)  ){
                    $this->error['last_name'] = $data['error_last_name'];
                }
                
                if ( (!preg_match('/^[1-9][0-9]*$/', trim($this->request->post['phone']))) || (utf8_strlen(trim($this->request->post['phone'])) != 10) ) {
		     	$this->error['phone'] = $data['error_phone'];
	      	}    
                
                //check phone exits
                $phone = trim($this->request->post['phone']);
                if(isset($this->request->get['picker_id']) && intval($this->request->get['picker_id']) > 0){
                    $picker_id = $this->request->get['picker_id'];
                    $is_phone_exits = $this->model_pickers_pickers->isPhoneExits($phone, $picker_id); 
                }
                else{
                    $is_phone_exits = $this->model_pickers_pickers->isPhoneExits($phone); 
                }
                if($is_phone_exits == true ){
                    $this->error['phone'] = $data['error_phone_exits'];
                }
                //end check phone exits
                
                if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', trim($this->request->post['email'])))
                  {
			         $this->error['email'] = $data['error_email'];
	            	} 
                
                //check email exits
                $email = trim($this->request->post['email']);
                if(isset($this->request->get['picker_id']) && intval($this->request->get['picker_id']) > 0){
                    $picker_id = $this->request->get['picker_id'];
                    $is_email_exits = $this->model_pickers_pickers->isEmailExits($email, $picker_id); 
                }
                else{
                    $is_email_exits = $this->model_pickers_pickers->isEmailExits($email); 
                }
                if($is_email_exits == true ){
                    $this->error['email'] = $data['error_email_exits'];
                }
                 //end check email exits

	         	if ($this->request->post['password'] || (!isset($this->request->get['picker_id']))) {
		      	if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
			     	$this->error['password'] = $this->language->get('error_password');
		     	}

		    	if ($this->request->post['password'] != $this->request->post['confirm_password']) {
				   $this->error['confirm_password'] = $this->language->get('error_confirm_password');
			     }
		        }

                /*if (empty($this->request->post['device_id'])){
                    echo $this->error['device_id'] = $data['error_device_id'];
                }*/
                
                if ( (!ctype_alpha ( trim($this->request->post['pickup_city_code'])) )|| (utf8_strlen(trim($this->request->post['pickup_city_code'])) != 2) ) {
			$this->error['pickup_city_code'] = $data['error_pickup_city_code'];
		}  
                
                if ((utf8_strlen(trim($this->request->post['pickup_city'])) < 3) || (utf8_strlen(trim($this->request->post['pickup_city'])) > 32)) {
			$this->error['pickup_city'] = $data['error_pickup_city'];
		}  
                
                if ((utf8_strlen($this->request->post['status']) != '1') || (utf8_strlen($this->request->post['status']) == '0')) {
			$this->error['status'] = $data['status'];
		}  
                
                if (isset($this->request->post['seller']) && count($this->request->post['seller']) > '0' ) {
			
                    $check_same_city_seller = $this->model_pickers_pickers->checkSameCitySeller($this->request->post['seller'],$this->request->post['pickup_city_code']); 
                    if($check_same_city_seller == false ){
                        $this->error['seller'] = $data['error_seller'];
                    }
		} 
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $data['error_warning'];
		}

		
		return !$this->error;
	}
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'operations/pickup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        
        public function getSellerByPickupCityCode() {  
            
            $data = array(); 
            // Autoloading the lanugage
            $this->load->autoLoadLanguage('operations/picker', $data);     
            
            //load model
            $this->load->model('pickers/pickers');
            
           // if (isset($this->request->get['picker_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                //get picker seller in edit case not get already selected seller in ajax 
               // $picker_info = $this->model_pickers_pickers->getPickerById($this->request->get['picker_id']);
                //$result = $this->model_pickers_pickers->getSellerByPickupCityCode($this->request->get['pickup_city_code'],$picker_info['seller']);
           // }else{
                $result = $this->model_pickers_pickers->getSellerByPickupCityCode($this->request->get['pickup_city_code']);  
           // }
            $html = '';
            foreach($result as $row){
                $html .= '<p id="' . $row['seller_id'] . '">&nbsp;<input name = "seller_id[]" type="checkbox" value="' . $row['seller_id'] . '">' . $row['nickname'] . '</p>';
            }
            echo $html; 
             
        }
        
       public function assign_seller()
        {
            $data = array();
            //load model
            $this->load->model('pickers/pickers');
            
            $pickup_id = $this->request->get['pickup_id'];
            $city_code = $this->request->get['city_code'];
            $sellers   = explode(",", $this->request->get['sellers']);
            
            $data['seller_list'] = $this->model_pickers_pickers->getSellerListByIds($sellers);
            $data['pickup_list'] = $this->model_pickers_pickers->getPickupByCityCode($this->request->get['city_code'], $pickup_id);
            $data['pickup_id']   = $pickup_id;

            $this->response->setOutput($this->load->view('operations/assign_seller.tpl', $data));

        }

       public function save_assign_seller()
        {
            $request = array();
            $data = array();
            $language = array();
            //load model
            $this->load->model('pickers/pickers');
             $this->load->autoLoadLanguage('operations/picker', $language); 

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAssignSeller()) {
            
               $request['pickup_id']  = $this->request->post['pickup_id'];
               $request['assign_id']  = $this->request->post['assign_id'];
               $request['sellers']    = $this->request->post['sellers'];
               $request['start_date'] = $this->request->post['start_date'];
               $request['end_date']   = $this->request->post['end_date'];

               $this->model_pickers_pickers->assign_seller_save($request);
               $data['error']=0;
               $data['success_msg']= $language['text_assign_seller'];
               echo json_encode($data);
             }
             else
             {
                $data['error']=1;
                $data['error_msg']=$this->error['error_msg'];
                echo json_encode($data); 
             }  
        }

    public function delete_assign_seller()
    {
        $this->load->model('pickers/pickers'); 
        $id  = $this->request->get['id'];
        $this->model_pickers_pickers->assign_seller_delete($id);
        echo 1;
        exit;
    }

        protected function validateAssignSeller() 
           {
                // load model
                $this->load->model('pickers/pickers');
            
                $data = array(); 
                // Autoloading the lanugage
                $this->load->autoLoadLanguage('operations/picker', $data);   
                
                 if (isset($this->request->post['start_date']) && isset($this->request->post['end_date']) && strtotime($this->request->post['start_date']) > strtotime($this->request->post['end_date'])) 
                  {
                     $this->error['error_msg'] = $data['error_date'];
                  }

                if (!isset($this->request->post['end_date']) || empty($this->request->post['end_date']) ) 
                  {
                     $this->error['error_msg'] = $data['error_end_date'];
                  }

                if (!isset($this->request->post['start_date']) || empty($this->request->post['start_date']) ) 
                  {
                     $this->error['error_msg'] = $data['error_start_date'];
                  } 

                  if (!isset($this->request->post['sellers']) || count($this->request->post['sellers']) == 0 ) 
                  {
                     $this->error['error_msg'] = $data['error_seller'];
                  }

                 if (!isset($this->request->post['assign_id']) || empty($this->request->post['assign_id']) ) 
                  {
                     $this->error['error_msg'] = $data['error_no_assign'];
                  }

                 if (!isset($this->request->post['pickup_id']) || empty($this->request->post['pickup_id']) ) 
                  {
                     $this->error['error_msg'] = $data['error_no_pickup'];
                  }       

                return !$this->error;

            }



}
