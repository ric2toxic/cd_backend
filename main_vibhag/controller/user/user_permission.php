<?php
class ControllerUserUserPermission extends Controller {
	private $error = array();
        private $ignor_methods = array(
                                        '__construct',
                                        '__get',
                                        '__set',
                                        '_validateToken',
                                        'isDeviceMobile',
                                        'getCustomUrlFilters'
                                        );
	/**
        * Public function [index] to get user group listing
        * @return Array
        * @author MSA, June 2018
        */
        public function index() {
		$this->load->language('user/user_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user_group');

		$this->getList();
	}
        
        /**
        * Public function [add] to add new user permission group
        * @param  Array [form post data]  
        * @return void
        * @author MSA, June 2018
        */
	public function add() {
		$this->load->language('user/user_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
                        $this->request->post['permission'] = $this->reset_permission_list($this->request->post);
                    
                        $this->model_user_user_group->addUserGroup($this->request->post);

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

			$this->response->redirect($this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
        
        /**
        * Public function [reset_permission_list] to reset permission listing data to access classes and methods
        * @param  Array [permission data]
        * @return Array
        * @author MSA, June 2018
        */
        public function reset_permission_list($data)
        {
            $access_permissions['access'] = array();
            
            if(isset($data['permission_list']) && !empty($data['permission_list'])) 
            {
                $permissions =  json_decode(html_entity_decode($data['permission_list']),true);
                
                foreach( $permissions as $item )
                {
                    $access_permissions['access'][] = $item['controller'];
                    
                    $access_permissions['access']['methods'][$item['controller']] = $item['methods'];
                }
            }
            return $access_permissions;
        }
        
        /**
        * Public function [edit] to edit user group data
        * @param  Array [form post data]
        * @return Void
        * @author MSA, June 2018
        */
	public function edit() {
		$this->load->language('user/user_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
                        $this->request->post['permission'] = $this->reset_permission_list($this->request->post);
                        
                        $this->model_user_user_group->editUserGroup($this->request->get['user_group_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
                
		$this->getForm();
	}
        
        /**
        * Public function [delete] to delete user group data
        * @param  Array [selected user group ids]
        * @return Void
        * @author MSA, June 2018
        */
	public function delete() {
		$this->load->language('user/user_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('user/user_group');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $user_group_id) {
				$this->model_user_user_group->deleteUserGroup($user_group_id);
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

			$this->response->redirect($this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
        
        /**
        * Protected function [getList] to generate user group listing data
        * @param  Void
        * @return Array
        * @author MSA, June 2018
        */
	protected function getList() {
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('user/user_group', $data);
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
			'href' => $this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('user/user_permission/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('user/user_permission/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['user_groups'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$user_group_total = $this->model_user_user_group->getTotalUserGroups();

		$results = $this->model_user_user_group->getUserGroups($filter_data);

		foreach ($results as $result) {
			$data['user_groups'][] = array(
				'user_group_id' => $result['user_group_id'],
				'name'          => $result['name'],
				'edit'          => $this->url->link('user/user_permission/edit', 'token=' . $this->session->data['token'] . '&user_group_id=' . $result['user_group_id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $user_group_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($user_group_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($user_group_total - $this->config->get('config_limit_admin'))) ? $user_group_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $user_group_total, ceil($user_group_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/user_group_list.tpl', $data));
	}
        
        /**
        * Protected function [getForm] to generate user group add/edit form 
        * @param  Void
        * @return Void
        * @author MSA, June 2018
        */
	protected function getForm() {
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('user/user_group', $data);
		
		$data['text_form'] = !isset($this->request->get['user_group_id']) ? $data['text_add'] : $data['text_edit'];

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
			'href' => $this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['user_group_id'])) {
			$data['action'] = $this->url->link('user/user_permission/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('user/user_permission/edit', 'token=' . $this->session->data['token'] . '&user_group_id=' . $this->request->get['user_group_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['user_group_id']) && $this->request->server['REQUEST_METHOD'] != 'POST') {
			$user_group_info = $this->model_user_user_group->getUserGroup($this->request->get['user_group_id']);
		}
                //pr($user_group_info); die;
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($user_group_info)) {
			$data['name'] = $user_group_info['name'];
		} else {
			$data['name'] = '';
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

		$data['controller_classes'] = array();
                $data['methods']            = array();
                $methods = array();
		$files = glob(DIR_APPLICATION . 'controller/*/*.php');

		foreach ($files as $file) {
			$part = explode('/', dirname($file));
                        $controller_classes = end($part) . '/' . basename($file, '.php');
                        $methods[$controller_classes] = $this->getClassMethods($file);
                        if (!in_array($controller_classes, $ignore)) {
				$data['controller_classes'][] = $controller_classes;
                                $data['methods'] = $methods;
			}   
		}

                if (isset($this->request->post['permission']['access'])) {
			$data['access'] = $this->request->post['permission']['access'];
		} elseif (isset($user_group_info['permission']['access'])) {
			$data['access'] = $user_group_info['permission']['access'];
		} else {
			$data['access'] = array();
		}

		if (isset($this->request->post['permission']['modify'])) {
			$data['modify'] = $this->request->post['permission']['modify'];
		} elseif (isset($user_group_info['permission']['modify'])) {
			$data['modify'] = $user_group_info['permission']['modify'];
		} else {
			$data['modify'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/user_group_form.tpl', $data));
	}
        
        /**
        * listClassMethods  - To get list class methods according to 
         *                  - visibility modifiers (PUBLIC | PRIVATE | PROTECTED)
        * @return  Object ReflectionClass
        * @author  MSA, <16 Nov. 17>
        */
        protected function listClassMethods($obj){
            $list = array();
            $methods = $obj->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_PROTECTED); 
            if(!empty($methods)) {
                  foreach($methods as $method){
                      $list[$method->name] = array(
                          'title'   => $method->name,
                          'comment' => $method->getDocComment()
                      );
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
                
                $class_methods = array();
                
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
                       
                       if(!empty($class_methods)) {
                           
                           $method_list = array();
                           
                           foreach($class_methods as $method_data) {
                           
                               if(!in_array($method_data['title'],$this->ignor_methods))
                               {
                                   $method_list[$method_data['title']] = $method_data;
                               }
                           }
                       }

                       sort($method_list);

                       return $method_list;
                        
                   } else { 

                       $obj = new ReflectionClass( get_class($this) );
                       
                        $class_methods = $this->listClassMethods($obj);
                        
                        if(!empty($class_methods)) {
                           
                           $method_list = array();
                           
                           foreach($class_methods as $method_data) {
                           
                               if(!in_array($method_data['title'],$this->ignor_methods))
                               {
                                   $method_list[$method_data['title']] = $method_data;
                               }
                           }
                        }
                        
                        sort($method_list);
                        
                        return $method_list;
                   }
                   
                }
            } 
        }
        
        /**
        * Protected function [getClasses] to get class name from raw file code
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
        
        /**
        * Protected function [validateForm] to check and validate user group add/edit form data
        * @param  Array [user group form post data]
        * @return Array
        * @author MSA, June 2018
        */  
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'user/user_permission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
        
        /**
        * Protected function [validateDelete] to validate user group id to delete
        * @param  Array [selected user group id]
        * @return Array
        * @author MSA, June 2018
        */ 
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'user/user_permission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('user/user');

		foreach ($this->request->post['selected'] as $user_group_id) {
			$user_total = $this->model_user_user->getTotalUsersByGroupId($user_group_id);

			if ($user_total) {
				$this->error['warning'] = sprintf($this->language->get('error_user'), $user_total);
			}
		}

		return !$this->error;
	}
}