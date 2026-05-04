<?php
class ControllerUserUser extends Controller {
	private $error = array();
        private $ignor_methods = array(
                                        '__construct',
                                        '__get',
                                        '__set',
                                        '_validateToken',
                                        'isDeviceMobile',
                                        'getCustomUrlFilters'
                                        );
	public function index() {
		$this->load->language('user/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user');

		$this->getList();
	}

	public function add() {
		$this->load->language('user/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
                        $this->model_user_user->addUser($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('user/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
                        $this->model_user_user->editUser($this->request->get['user_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('user/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $user_id) {
				$this->model_user_user->deleteUser($user_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('user/user', $data);

	    $this->load->model('user/user_group');
		$data['user_groups'] = $this->model_user_user_group->getUserGroups();

	    if (isset($this->request->get['filter_user_name'])) {
            $filter_user_name = $this->request->get['filter_user_name'];
        } else {
            $filter_user_name = null;
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_user_group'])) {
            $filter_user_group = $this->request->get['filter_user_group'];
        } else {
            $filter_user_group = null;
        }

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'username';
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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
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
			'href' => $this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('user/user/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('user/user/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['users'] = array();

		$filter_data = array(
			'filter_name' 	   => $filter_name,
			'filter_user_name' => $filter_user_name,
			'filter_user_group'=> $filter_user_group,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$user_total = $this->model_user_user->getTotalUsers($filter_data);

		$results = $this->model_user_user->getUsers($filter_data);

		foreach ($results as $result) {
			$data['users'][] = array(
				'user_id'    => $result['user_id'],
				'username'   => $result['username'],
				'name'		 => $result['name'],
				'branch_code'=> ($result['branch_code']) ? $result['branch_code'] : '--',
				'group_name' => $result['group_name'],
				'status'     => ($result['status'] ? $data['text_enabled'] : $data['text_disabled']),
				'date_added' => date($data['date_format_short'], strtotime($result['date_added'])),
				'edit'       => $this->url->link('user/user/edit', 'token=' . $this->session->data['token'] . '&user_id=' . $result['user_id'] . $url, 'SSL')
			);
		}

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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_username'] = $this->url->link('user/user', 'token=' . $this->session->data['token'] . '&sort=username' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('user/user', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('user/user', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $user_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('user/user', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($user_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($user_total - $this->config->get('config_limit_admin'))) ? $user_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $user_total, ceil($user_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['token'] = $this->session->data['token'];
		$data['filter_name'] = $filter_name;
		$data['filter_user_name'] = $filter_user_name;
		$data['filter_user_group'] = $filter_user_group;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/user_list.tpl', $data));
	}

	protected function getForm() {
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
            $this->load->autoLoadLanguage('user/user_group', $data);
            $this->load->autoLoadLanguage('user/user', $data);
		
            
                $data['text_form'] = !isset($this->request->get['user_id']) ? $data['text_add'] : $data['text_edit'];
                $data['user_id']  = isset($this->request->get['user_id']) ? $this->request->get['user_id'] : '';
                

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['user_id'])) {
			$data['action'] = $this->url->link('user/user/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('user/user/edit', 'token=' . $this->session->data['token'] . '&user_id=' . $this->request->get['user_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('user/user', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['user_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                    $user_info = $this->model_user_user->getUser($this->request->get['user_id']);
		}
                // pr($user_info); die;
		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} elseif (!empty($user_info)) {
			$data['username'] = $user_info['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['user_group_id'])) {
			$data['user_group_id'] = $this->request->post['user_group_id'];
		} elseif (!empty($user_info)) {
			$data['user_group_id'] = $user_info['user_group_id'];
		} else {
			$data['user_group_id'] = '';
		}

		$this->load->model('user/user_group');

		$data['user_groups'] = $this->model_user_user_group->getUserGroups();

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

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($user_info)) {
			$data['firstname'] = $user_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($user_info)) {
			$data['lastname'] = $user_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($user_info)) {
			$data['email'] = $user_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($user_info)) {
			$data['image'] = $user_info['image'];
		} else {
			$data['image'] = '';
		}

		if (isset($this->request->post['branch_code'])) {
			$data['branch_code'] = $this->request->post['branch_code'];
		} elseif (!empty($user_info)) {
			$data['branch_code'] = $user_info['branch_code'];
		} else {
			$data['branch_code'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($user_info) && $user_info['image']) {
			$data['thumb'] = $this->model_tool_image->resize($user_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($user_info)) {
			$data['status'] = $user_info['status'];
		} else {
			$data['status'] = 0;
		}
            
                $this->load->model('user/user_group');
                $data['permission_groups'] = $this->model_user_user_group->getAllGroups();
              // pr($data); die;
                
            /* user permission */    
                $ignore = array(
			'common/dashboard',
			'common/startup',
			'common/login',
			'common/logout',
			'common/reset',
			'error/not_found',
			'error/permission',
			'common/footer',
			'common/header',
			'dashboard/order',
			'dashboard/sale',
			'dashboard/customer',
			'dashboard/online',
			'dashboard/map',
			'dashboard/activity',
			'dashboard/chart',
			'dashboard/recent'
		);

		$data['permissions'] = array();
                $methods = array();
                
		$files = glob(DIR_APPLICATION . 'controller/*/*.php');
                
		foreach ($files as $file) {
			$part = explode('/', dirname($file));
                        $permission = end($part) . '/' . basename($file, '.php');

                        $methods[str_replace('/','_',$permission)] = $this->getClassMethods($file);

                        if (!in_array($permission, $ignore)) {
				$data['permissions'][] = $permission;
                                $data['methods'] = $methods;
			}   
		}
                //pr($data['methods']); die;
                
                
                if (isset($this->request->post['permission']['access'])) {
			$data['access'] = $this->request->post['permission']['access'];
		} elseif (isset($user_info['permission']['access'])) {
			$data['access'] = $user_info['permission']['access'];
		} else {
			$data['access'] = array();
		}

		if (isset($this->request->post['permission']['modify'])) {
			$data['modify'] = $this->request->post['permission']['modify'];
		} elseif (isset($user_info['permission']['modify'])) {
			$data['modify'] = $user_info['permission']['modify'];
		} else {
			$data['modify'] = array();
		} 
            /* user permission */     
                
                $data['token'] = $this->request->get['token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
		$this->response->setOutput($this->load->view('user/user_form.tpl', $data));
	}
        
        /**
        * userGroupPermissions  - To get list of user permissions
        * @param   user_group_id  integer
        * @return  Array User permissions list [access, access_methods, permissions, methods, modify_methods]  
        * @author  MSA, <16 Nov. 17>
        */
        public function userGroupPermissions()
        {   
            $this->load->model('user/user_group');
            $user_group_id = $this->request->get['user_group_id'];
            $access_methods = array();
            $modify_methods = array();
            if(!empty($user_group_id)) {
                
                $permissions = $this->model_user_user_group->getUserGroup($user_group_id);
                
                if(!empty($permissions['permission']['access'])) {
                    if(isset($permissions['permission']['access']['methods'])) {
                        
                        foreach($permissions['permission']['access']['methods'] as $key => $value){
                            $access_methods[str_replace('/','_',$key)] = $value;
                        }
                        unset($permissions['permission']['access']['methods']);
                    }
                    $data['access'] = isset($permissions['permission']['access']) ? $permissions['permission']['access'] : array();
                    $data['access_methods'] = $access_methods;
                }
                
                $ignore = array(
			'common/dashboard',
			'common/startup',
			'common/login',
			'common/logout',
			'common/reset',
			'error/not_found',
			'error/permission',
			'common/footer',
			'common/header',
			'dashboard/order',
			'dashboard/sale',
			'dashboard/customer',
			'dashboard/online',
			'dashboard/map',
			'dashboard/activity',
			'dashboard/chart',
			'dashboard/recent'
		);

		$data['permissions'] = array();
                $methods = array();
                
		$files = glob(DIR_APPLICATION . 'controller/*/*.php');
                
		foreach ($files as $file) {
			$part = explode('/', dirname($file));
                        $permission = end($part) . '/' . basename($file, '.php');

                        $methods[str_replace('/','_',$permission)] = $this->getClassMethods($file);

                        if (!in_array($permission, $ignore)) {
				$data['permissions'][] = $permission;
                                $data['methods'] = $methods;
			}   
		}
                
                //pr($methods); die;
                
                if(!empty($permissions['permission']['modify'])) {
                    if(isset($permissions['permission']['modify']['methods'])) {
                        foreach($permissions['permission']['modify']['methods'] as $key => $value){
                            $modify_methods[str_replace('/','_',$key)] = $value;
                        }
                        unset($permissions['permission']['modify']['methods']);
                    }
                    $data['modify'] = isset($permissions['permission']['modify']) ? $permissions['permission']['modify'] : array();
                    $data['modify_methods'] = $modify_methods;
                }
                
            } else {
                
                $data['error'] = 'Invalid user group id.';
                
            }
            //pr($data['access']); die;
            echo json_encode($data);
        }
        
        /**
        * listClassMethods  - To get list class methods according to 
         *                  - visibility modifiers (PUBLIC | PRIVATE | PROTECTED)
        * @return  Object ReflectionClass
        * @author  MSA, <16 Nov. 17>
        */
        protected function listClassMethods($obj){
            $list = '';
            $methods = $obj->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_PROTECTED); 
            if(!empty($methods)) {
                  foreach($methods as $method){
                      $list[] = $method->name;
                  }
              }
            return $list;
        }
        
        /**
        * getClassMethods  - To get list class methods
        * @params  $file  string
        * @return  Array class methods list 
        * @author  MSA, <16 Nov. 17>
        */
        protected function getClassMethods($file)
        {
            if(file_exists($file)) {
                
                $methods = array();
                
                $file_code = file_get_contents($file);
                
                if(strlen($file_code)) 
                {
                    $methods = array();
                    
                    $className = $this->getClasses($file_code);
                    
                    if(!class_exists($className, false)) {
                      
                       @require_once($file);
                      
                       $obj = new ReflectionClass($className);

                       if(is_object($obj)) {
                           
                           $class_methods = $this->listClassMethods($obj);
                       }
                      
                       $methods =  array_diff($class_methods,$this->ignor_methods);
                       
                       return $methods;
                        
                   } else { 

                        if($className === get_class($this))
                        {
                            $methods =  array_diff(get_class_methods($this),$this->ignor_methods);
                        }    
                      return $methods;
                   }
                   
                }
            }
        }
        
        /**
        * getClasses  - To get class name from raw file code
        * @params  $php_code  string
        * @return  String class name
        * @author  MSA, <16 Nov. 17>
        */
        protected function getClasses($php_code) {
            $classes = ''; 
            $tokens = token_get_all($php_code);
            $count = count($tokens);
            for ($i = 2; $i < $count; $i++) {
              if (   $tokens[$i - 2][0] == T_CLASS
                  && $tokens[$i - 1][0] == T_WHITESPACE
                  && $tokens[$i][0] == T_STRING) {

                  $class_name = $tokens[$i][1];
                  $classes = $class_name;
              }
            }
            return $classes;
          }
         
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'user/user')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['username']) < 3) || (utf8_strlen($this->request->post['username']) > 20)) {
			$this->error['username'] = $this->language->get('error_username');
		}

		$user_info = $this->model_user_user->getUserByUsername($this->request->post['username']);

		if (!isset($this->request->get['user_id'])) {
			if ($user_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($user_info && ($this->request->get['user_id'] != $user_info['user_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ($this->request->post['password'] || (!isset($this->request->get['user_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'user/user')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['selected'] as $user_id) {
			if ($this->user->getId() == $user_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}
		}

		return !$this->error;
	}
}