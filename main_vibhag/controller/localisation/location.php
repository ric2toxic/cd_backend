<?php
class ControllerLocalisationLocation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_location->addLocation($this->request->post);

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

			$this->response->redirect($this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_location->editLocation($this->request->get['location_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $location_id) {
				$this->model_localisation_location->deleteLocation($location_id);
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

			$this->response->redirect($this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'l.name';
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

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('localisation/location', $data);

		$data['breadcrumbs'] =   array();

		$data['breadcrumbs'][] =   array(
			'text' =>  $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] =   array(
			'text' =>  $data['heading_title'],
			'href' =>  $this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('localisation/location/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('localisation/location/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['location'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$location_total = $this->model_localisation_location->getTotalLocations();

		$results = $this->model_localisation_location->getLocations($filter_data);

		foreach ($results as $result) {
                    
                        //create full address
                        $address = '';
                        $address = $result['address'];
                        $address .= ', ' . $result['city'];
                        if($result['postcode'] != ''){
                            $address .= '-' . $result['postcode'];
                        }
                        $address .= ', ' . $result['state'];
                        $address .= ', ' . $result['country'];
                        
                        $status = 'Enable';
                        if($result['status'] == '0'){
                            $status = 'Disable';
                        }
                        
                        $data['location'][] =   array(
				'location_id' => $result['location_id'],
				'name'        => $result['name'],
				'address'     => $address,
				'sort_order'     => $result['sort_order'],
				'status'     => $status,
				'edit'        => $this->url->link('localisation/location/edit', 'token=' . $this->session->data['token'] . '&location_id=' . $result['location_id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('localisation/location', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_address'] = $this->url->link('localisation/location', 'token=' . $this->session->data['token'] . '&sort=address' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $location_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($location_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($location_total - $this->config->get('config_limit_admin'))) ? $location_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $location_total, ceil($location_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/location_list.tpl', $data));
	}

	protected function getForm() {
	    $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('localisation/location', $data);

		$data['text_form'] = !isset($this->request->get['location_id']) ? $data['text_add'] : $data['text_edit'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

                //echo "<pre>"; print_r($data); die; 
                
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
                
                if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
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
                
                if (isset($this->error['state'])) {
			$data['error_state'] = $this->error['state'];
		} else {
			$data['error_state'] = ''; 
		}
                
                if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = ''; 
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}
                
                if (isset($this->error['show_on'])) {
			$data['error_show_on'] = $this->error['show_on'];
		} else {
			$data['error_show_on'] = '';
		}
                
                if (isset($this->error['direction_url'])) {
			$data['error_direction_url'] = $this->error['direction_url'];
		} else {
			$data['error_direction_url'] = ''; 
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
			'href' => $this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['location_id'])) {
			$data['action'] = $this->url->link('localisation/location/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('localisation/location/edit', 'token=' . $this->session->data['token'] .  '&location_id=' . $this->request->get['location_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('localisation/location', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['location_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$location_info = $this->model_localisation_location->getLocation($this->request->get['location_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('setting/store');
                
                
                if( isset($this->request->get['location_id']) ){
                    $data['location_id'] = $this->request->get['location_id'];
                }else{
                    $data['location_id'] =   '';  
                }
                
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($location_info)) {
			$data['name'] = $location_info['name'];
		} else {
			$data['name'] =   '';
		}
                
                if (isset($this->request->post['location_type'])) {
			$data['location_type'] = $this->request->post['location_type'];
		} elseif (!empty($location_info)) {
			$data['location_type'] = $location_info['location_type'];
		} else {
			$data['location_type'] =   '';
		}
                
                if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif (!empty($location_info)) {
			$data['city'] = $location_info['city'];
		} else {
			$data['city'] =   ''; 
		}
                
                if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} elseif (!empty($location_info)) {
			$data['postcode'] = $location_info['postcode'];
		} else {
			$data['postcode'] =   ''; 
		}

                if (isset($this->request->post['state'])) {
			$data['state'] = $this->request->post['state'];
		} elseif (!empty($location_info)) {
			$data['state'] = $location_info['state'];
		} else {
			$data['state'] =   ''; 
		}
                
		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		} elseif (!empty($location_info)) {
			$data['address'] = $location_info['address'];
		} else {
			$data['address'] = '';
		}
                
                if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		} elseif (!empty($location_info)) {
			$data['address'] = $location_info['address'];
		} else {
			$data['address'] = '';
		}

		if (isset($this->request->post['country'])) {
			$data['country'] = $this->request->post['country'];
		} elseif (!empty($location_info)) {
			$data['country'] = $location_info['country'];
		} else {
			$data['country'] = ''; 
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($location_info)) {
			$data['telephone'] = $location_info['telephone'];
		} else {
			$data['telephone'] = '';
		}
                
                if (isset($this->request->post['show_on'])) {
			$data['show_on'] = $this->request->post['show_on'];
		} elseif (!empty($location_info)) {
			$data['show_on'] = $location_info['show_on'];
		} else {
			$data['show_on'] = ''; 
		}
                
                if (isset($this->request->post['geocode'])) {
			$data['geocode'] = $this->request->post['geocode'];
		} elseif (!empty($location_info)) {
			$data['geocode'] = $location_info['geocode'];
		} else {
			$data['geocode'] = ''; 
		}
                
                if (isset($this->request->post['direction_url'])) {
			$data['direction_url'] = $this->request->post['direction_url'];
		} elseif (!empty($location_info)) {
			$data['direction_url'] = $location_info['direction_url'];
		} else {
			$data['direction_url'] = ''; 
		}
                
                if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($location_info)) {
			$data['status'] = $location_info['status'];
		} else {
			$data['status'] = ''; 
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($location_info)) {
			$data['image'] = $location_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($location_info)) {
			$data['thumb'] = $this->model_tool_image->resize($location_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($location_info)) {
			$data['sort_order'] = $location_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}
                
                if (isset($this->request->post['timing'])) {
			$data['timing'] = $this->request->post['timing'];
		} elseif (!empty($location_info)) {
			$data['timing'] = $location_info['timing'];
		} else {
			$data['timing'] = '';
		}

		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (!empty($location_info)) {
			$data['comment'] = $location_info['comment'];
		} else {
			$data['comment'] = '';
		}
                
                $this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();
                
                $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
		$this->response->setOutput($this->load->view('localisation/location_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}
                
                if ( (utf8_strlen($this->request->post['city']) < 3) || (utf8_strlen($this->request->post['city']) > 32) ) {
			$this->error['city'] = $this->language->get('error_city'); 
		}
                
                if ( ( preg_match ("/[^0-9]/", $this->request->post['postcode'] ) || (utf8_strlen($this->request->post['postcode']) > 6) ) ) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}
                
                if ( (utf8_strlen($this->request->post['state']) < 3) || (utf8_strlen($this->request->post['state']) > 32) ) {
			$this->error['state'] = $this->language->get('error_state');
		}
                
                if ( (utf8_strlen($this->request->post['country']) < 3) || (utf8_strlen($this->request->post['country']) > 32) ) {
			$this->error['country'] = $this->language->get('error_country'); 
		}
                
		if ((utf8_strlen($this->request->post['address']) < 3) || (utf8_strlen($this->request->post['address']) > 128)) {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 10) || (utf8_strlen($this->request->post['telephone']) > 100)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}
                
                if ((utf8_strlen($this->request->post['direction_url']) < 3) || (utf8_strlen($this->request->post['direction_url']) > 50) || (strpos($this->request->post['direction_url'], 'https://goo.gl/maps/') === false)) {
			$this->error['direction_url'] = $this->language->get('error_direction_url'); 
		}
                
                return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}