<?php
class ControllerDesignBanner extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		$this->getList();
	}

	public function add() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
                        //echo "<pre>"; print_r($this->request->post); die('add_con');
                    
			$this->model_design_banner->addBanner($this->request->post);

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

			$this->response->redirect($this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_design_banner->editBanner($this->request->get['banner_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $banner_id) {
				$this->model_design_banner->deleteBanner($banner_id);
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

			$this->response->redirect($this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
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
        
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('design/banner', $data);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('design/banner/add', 'token=' . $this->session->data['token'] . '&category_id=0&mode=add' . $url, 'SSL');
		$data['delete'] = $this->url->link('design/banner/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['banners'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$banner_total = $this->model_design_banner->getTotalBanners();

		$results = $this->model_design_banner->getBanners($filter_data);

		foreach ($results as $result) {
			$data['banners'][] = array(
				'banner_id' => $result['banner_id'],
				'name'      => $result['name'],
				'status'    => ($result['status'] ? $data['text_enabled'] : $data['text_disabled']),
				'edit'      => $this->url->link('design/banner/edit', 'token=' . $this->session->data['token'] . '&banner_id=' . $result['banner_id'] . '&category_id=0&mode=edit' . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $banner_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($banner_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($banner_total - $this->config->get('config_limit_admin'))) ? $banner_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $banner_total, ceil($banner_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/banner_list.tpl', $data));
	}

	protected function getForm() {
		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('design/banner', $data);

		$data['text_form'] = !isset($this->request->get['banner_id']) ? $data['text_add'] : $data['text_edit'];

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
                
        if (isset($this->error['select_category'])) {
			$data['error_select_category'] = $this->error['select_category'];
		} else {
			$data['error_select_category'] = '';
		}

		if (isset($this->error['banner_image'])) {
			
			foreach ($this->error['banner_image'] as $banner_image_id => $banners_image) {
				if(isset($banners_image['banner_image_description']))
				{
				  foreach ($banners_image['banner_image_description'] as $language_id => $desc_data) {
				  $data['error_banner_image'][$banner_image_id]['banner_image_description'][$language_id]['error_title'] = $desc_data['title'];
				  }
				}
				if ( isset($banners_image['error_category_id']) ) {
					$data['error_banner_image'][$banner_image_id]['error_category_id'] = $banners_image['error_category_id'];
				} 
			}

		} else {
			$data['error_banner_image'] = array();
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
			'href' => $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
                
                if (isset($this->request->get['category_id'])) {
                    $url .= '&category_id=' . $this->request->get['category_id'];
                }
                
                if (isset($this->request->get['mode'])) {
                    $url .= '&mode=' . $this->request->get['mode'];
                }
                
		if (!isset($this->request->get['banner_id'])) {
			$data['action'] = $this->url->link('design/banner/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
                    	$data['action'] = $this->url->link('design/banner/edit', 'token=' . $this->session->data['token'] . '&banner_id=' . $this->request->get['banner_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['banner_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$banner_info = $this->model_design_banner->getBanner($this->request->get['banner_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($banner_info)) {
			$data['name'] = $banner_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($banner_info)) {
			$data['status'] = $banner_info['status'];
		} else {
			$data['status'] = true;
		}
                
        if (isset($this->request->post['is_category'])) {
			$data['is_category'] = $this->request->post['is_category']; 
		} elseif (!empty($banner_info)) {
			$data['is_category'] = $banner_info['is_category'];
		} else {
			$data['is_category'] = false; 
		}

		if (isset($this->request->get['mode'])) {
			$data['mode'] = $this->request->get['mode'];  
		} 
                

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');
                
        //get banner and category_id for edit cahe
        if (isset($this->request->get['banner_id'])) {
            
            $banner_id = $this->request->get['banner_id'];
            $category_id = '';
            
            if(isset($this->request->get['category_id'])){
                $category_id = $this->request->get['category_id']; 
            }
        }
        
        if (isset($this->request->post['banner_image'])) {
			$banner_images = $this->request->post['banner_image'];
        } else if (isset($this->request->get['banner_id'])) { 
            $banner_images = $this->model_design_banner->getBannerImages($banner_id,$category_id);	
		} else { 
			$banner_images = array();
		}

/*		if(isset($this->request->get['banner_id'])){
			$banner_id = $this->request->get['banner_id'];
			$categories_id = $this->model_design_banner->findSelectedCategories($banner_id);
			if(!empty($categories_id)){
				$categories = array_column($categories_id,'category_id');
				$categories_id = array_combine($categories,$categories_id);
				$data['categories_id'] = $categories_id;
			}else{
				$data['categories_id'] = '';
			}
		}else{
			$data['categories_id'] = '';
		}*/


		// it's using get image path and send by ajax click on perticular image when product edited
		$data['directory'] = (isset($banner_images['image'])) ? dirname(dirname($banner_images['image'])) : '';
                
        //for add case set store id is 0
        if(empty($banner_info['store_id'])){
            $banner_info['store_id'] = 0;
        }
        
        $categories = $this->model_design_banner->findParentCategories($banner_info['store_id']);

		if(!empty($categories)){
			$data['categories'] = $categories;
		}else{
			$data['categories'] = '';
		}        
                
		$this->load->model('catalog/category');

		$data['banner_images'] = array();

        //echo "<pre>"; print_r($banner_images); die('12345'); 
        foreach ($banner_images as $banner_image) {

            $categories_id = array();
            if(isset($banner_image['banner_image_id']) && $banner_image['banner_image_id'] != ''){     
                $categories_id = $this->model_design_banner->findSelectedCategories($banner_image['banner_image_id']);
            }
            $i = 0;
            foreach($categories as $values){
                foreach($categories_id as $val){
                    if($values['category_id'] == $val){
                        $i++;
                    }
                }
            }
            $flage_select_all = 0;
            if($i == count($categories)){
                $flage_select_all = 1;
            }
            //echo "<pre>"; print_r($banner_image); die;
            $category_label = '';

            if(is_numeric($banner_image['link']) && $banner_image['link'] > 0)
            {
            	$category_label = $this->model_catalog_category->getCategory($banner_image['link'])['name'] ?? '';
            }

            $cart_categories = array();
            if(isset($banner_image['banner_image_id']) && $banner_image['banner_image_id'] != ''){     
              $cart_categories = $this->model_design_banner->getCartCategories($banner_image['banner_image_id']);
            }

            $data['banner_images'][] = array(
	            	'banner_image_description' => $banner_image['banner_image_description'],
					'link'                     => $banner_image['link'],
					'sort_order'               => $banner_image['sort_order'],
					'status'                   => $banner_image['status'], 
					'target_blank'             => isset($banner_image['target_blank']) ? $banner_image['target_blank'] : '0', 
	                'banner_image_id'          => isset($banner_image['banner_image_id']) ? $banner_image['banner_image_id'] : '', 
	                'categories_id'            => $categories_id,
	                'flage_select_all'         => $flage_select_all,
	                'type'					   => $this->model_design_banner->getBannerType($banner_image['banner_image_id']),
	                'value_label'              => $category_label,
	                'cart_categories'          => $cart_categories
            	);
		}

		$filter_data = array();
		$store_id = 0;
		$data['type_link_categories'] = $this->model_catalog_category->getCategories($filter_data, $store_id);

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} else {
			$data['type'] = 'link';
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
                
        if(isset($this->request->get['banner_id'])){
            $data['banner_id'] = $this->request->get['banner_id'];
            $data['token'] = $this->request->get['token'];
        }
        $data['category_id'] = '';
        if(isset($this->request->get['category_id'])){
            $data['category_id'] = $this->request->get['category_id']; 
        }     
        
        //get stores
        $data['store_data'] = $this->model_design_banner->getStores();
        //echo "<pre>"; print_r($data['store_data']); die;
                
                
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$filter_data = array(
				'filter_name' => '',
				'sort'        => 'name',
				'order'       => 'ASC'
			);

		$cart_category = $this->model_catalog_category->getCategories($filter_data, $store_id);

		foreach ($cart_category as $cart_category_result) {
				$data['cart_category'][] = array(
					'category_id' => $cart_category_result['category_id'],
					'name'        => strip_tags(html_entity_decode($cart_category_result['name'], ENT_QUOTES, 'UTF-8'))
				);
		}

		$sort_order = array();
		foreach ($data['cart_category'] as $key => $value) {
			$sort_order[$key] = $value['name'];
		}
		array_multisort($sort_order, SORT_ASC, $data['cart_category']);
        $this->response->setOutput($this->load->view('design/banner_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
                
        if(isset($this->request->post['is_category'])){
            if($this->request->post['is_category'] == '1' && $this->request->post['select_category'] == '0'){ 
                    $this->error['select_category'] = $this->language->get('error_select_category');
            }
        }

        if (isset($this->request->post['banner_image'])) {
			foreach ($this->request->post['banner_image'] as $banner_image_id => $banner_image) {
				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					if ((utf8_strlen($banner_image_description['title']) < 2) || (utf8_strlen($banner_image_description['title']) > 64)) {
						$this->error['banner_image'][$banner_image_id]['banner_image_description'][$language_id]['title'] = $this->language->get('error_title');
					}
				}

				if ( !empty($banner_image['type']) && $banner_image['type'] == 'category' && !$banner_image['category_id'] )
				{
					$this->error['banner_image'][$banner_image_id]['error_category_id'] = $this->language->get('error_category_id');
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        function getMenuByStoreID() { 
            
            $this->load->model('design/banner');
            
            //echo "<pre>54564"; die; print_r($this->request); die;
            $store_id = $this->request->post['store_id'];
            //echo $store_id; die;
            $categories = $this->model_design_banner->findParentCategories($store_id);
            //echo "<pre>"; print_r($categories); die;
            $html = '';
            if(count($categories) > 0){
                foreach($categories as $val){
                    $html .= '<option value=' . $val['category_id'] .'>' . $val['name'] .'</option>';
                }
            }
            $data['html'] = $html;
            echo json_encode($data);
            
        }
        
}