<?php
class ControllerCatalogCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		$this->load->model('catalog/filter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_category->dynamicmetatags($this->request->post);
			
			$this->model_catalog_category->addCategory($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_catalog_category->dynamicmetatags($this->request->post);
			$this->request->post['user_id'] = $this->user->getId();
            $this->request->post['changes_data'] = $_POST['changes_data'];
			$this->model_catalog_category->editCategory($this->request->get['category_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->model_catalog_category->store_data($this->request->post, $this->request->get['category_id']);
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
			
			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $category_id) {
				$this->model_catalog_category->deleteCategory($category_id);
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

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if ($this->validateRepair()) {
			$this->model_catalog_category->repairCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'], 'SSL'));
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
        $this->load->autoLoadLanguage('catalog/category', $data);
        
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
                
        //create breadcrumbs categorywise
        $data['label_text'] = '';
        if(isset($this->request->get['category_id'])){
            //$url .= '&category_id=' . $this->request->get['category_id'];
            
            //first check have parent or not
            $parents_cat = $this->model_catalog_category->getParentCategories($this->request->get['category_id']);
            
            //parents category link
            if(count($parents_cat) > 0){
                $text = '';
                foreach($parents_cat as $pc){
                    $data['breadcrumbs'][] = array(
                        'text' => $pc['name'],
                        'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url . '&category_id=' . $pc['id'], 'SSL')
                    );

                    $href = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url . '&category_id=' . $pc['id'], 'SSL');
                    $text .= "<a href =" . $href. ">" . $pc['name'] . " </a> / " ;
                }

                $data['label_text'] = rtrim($text,' / ');
            }
            
        }
        
        //get currrent category_info
        $data['category_id'] = '';
        $data['name'] = '';
        $data['parent_id'] = '';
        $data['parent_name'] = '';
        
        if( isset($this->request->get['category_id']) ){
            $category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);
            $data['category_id'] = $category_info['category_id'];
            $data['name'] = $category_info['name'];
        
            //get currrent category parent info
            $parent_info = $this->model_catalog_category->getParentCategory($this->request->get['category_id']);
            if( count($parent_info) > 0){
                $data['parent_id'] = $parent_info['parent_id'];
                $data['parent_name'] = $parent_info['parent_name'];
            }
        }
        
        //default parent link
        $data['parent_link'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL');
        
        if($data['parent_id'] != 0){
            $data['parent_link'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url . '&category_id=' . $data['parent_id'], 'SSL');
        }
                
		$data['add'] = $this->url->link('catalog/category/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['repair'] = $this->url->link('catalog/category/repair', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['categories'] = array();
                
                
        //get category_id 
        $parent_id = 0; 
        if( isset($this->request->get['category_id'] ) ){
            $parent_id = $this->request->get['category_id'];  
        }
                
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
                
                
        $category_total = $this->model_catalog_category->getTotalCategories($parent_id);

        $results = $this->model_catalog_category->getChildCategories($parent_id,$filter_data);
        
        foreach ($results as $result) {
            
            //check have sub category
            $sub_cate_count = $this->model_catalog_category->getTotalCategories($result['category_id']);
            
            if($sub_cate_count > 0){
                $sub_cat = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] , 'SSL');
            }else{
                $sub_cat = '';
            }
                
        	$data['categories'][] = array(
				'category_id' 		=> $result['category_id'],
				'name'        		=> $result['name'],
				'total_sub_category'=> $sub_cate_count,
				'sort_order'  		=> $result['sort_order'],
				'sub_cat'        	=> $sub_cat,
				'edit'        		=> $this->url->link('catalog/category/edit', 
														'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 
														'SSL'),
				'delete'      		=> $this->url->link('catalog/category/delete', 
														'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 
														'SSL')
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

		$data['sort_name'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));
                
                $data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
                $this->response->setOutput($this->load->view('catalog/category_list.tpl', $data));
	}

	protected function getForm() {
		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('catalog/category', $data);

		$data['text_form'] = !isset($this->request->get['category_id']) ? $data['text_add'] : $data['text_edit'];
		$data['tab_store_data'] = $this->language->get('Store data');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
                
                if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}
		
		if (isset($this->error['price_range'])) {
			$data['error_price_range'] = $this->error['price_range'];
		} else {
			$data['error_price_range'] = '';
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
			'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

                
                
		if (!isset($this->request->get['category_id'])) {
			$data['action'] = $this->url->link('catalog/category/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $this->request->get['category_id'] . $url, 'SSL');
		}
		$data['store_data'] = $this->model_catalog_category->get_store_data();

		$data['cancel'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);
		}

		if(!empty($category_info['price_range'])){
			$price_range = explode('-',$category_info['price_range']);
			$data['price_range_start'] = $price_range[0];
			$data['price_range_end'] = $price_range[1];
		}
		if(isset($this->request->post['price_range_start']) || isset($this->request->post['price_range_end'])) {
			$data['price_range_start'] = $this->request->post['price_range_start'];
			$data['price_range_end'] = $this->request->post['price_range_end'];
		}
		
		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['category_description'])) {
			$data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($this->request->get['category_id']);
		} else {
			$data['category_description'] = array();
		}
		/*
		* get seller list
		*/
		$data['sellers_list'] = array();
		$this->load->model('catalog/product');

		$seller_data = $this->model_catalog_product->getSellerList();

		foreach ($seller_data as $key => $value) {
			$data['sellers_list'][]=  array(
						'seller_id' => $value['seller_id'],
						'name'      => $value['nickname'].' '.$value['company'],
					);
		}

		$data['category_filters_groups'] = array();
		$this->load->model('catalog/filter');

		if(!empty($category_info)) {
			
			$filter_groups = explode(",",$category_info['filter_groups']);
		
			$non_mandatory_filter_groups = explode(",",$category_info['non_mandatory_filter_groups']);
			
			$naming_filter_groups = explode(",",$category_info['naming_filters']);
			
			foreach ($filter_groups as $filter_group_id) {
				$filter_info = $this->model_catalog_filter->getFilterGroup($filter_group_id);
				
				if ($filter_info) {
					$data['category_filters_groups'][] = array(
						'filter_group_id' => $filter_info['filter_group_id'],
						'name'      => $filter_info['name'],
					);
				}
			}
			
			foreach ($non_mandatory_filter_groups as $filter_group_id) {
				$filter_info = $this->model_catalog_filter->getFilterGroup($filter_group_id);
				
				if ($filter_info) {
					$data['non_mandatory_filter_groups'][] = array(
						'filter_group_id' => $filter_info['filter_group_id'],
						'name'      => $filter_info['name'],
					);
				}
			}
			
			foreach ($naming_filter_groups as $filter_group_id) {
				$filter_info = $this->model_catalog_filter->getFilterGroup($filter_group_id);
				
				if ($filter_info) {
					$data['naming_filter_groups'][] = array(
						'filter_group_id' => $filter_info['filter_group_id'],
						'name'      => $filter_info['name'],
					);
				}
			}
		}
		
		if (isset($this->request->post['min_weight']) && isset($this->request->post['max_weight']) && isset($this->request->post['taxable']) && isset($this->request->post['show_for_import'])) {
			$data['min_weight'] = $category_info['min_weight'];
			$data['max_weight'] = $category_info['max_weight'];
			$data['taxable'] = $category_info['taxable'];
			$data['show_for_import'] = $category_info['show_for_import'];
			
		} elseif (isset($this->request->get['category_id'])) {
			$data['min_weight'] = isset($category_info['min_weight'])?$category_info['min_weight']:0;
			$data['max_weight'] = isset($category_info['max_weight'])?$category_info['max_weight']:0;
			$data['taxable'] = isset($category_info['taxable'])?$category_info['taxable']:0;
			$data['show_for_import'] = isset($category_info['show_for_import'])?$category_info['show_for_import']:0;

		} else {
			$data['min_weight'] =0;
			$data['max_weight'] =0;
			$data['taxable'] = 0;
			$data['show_for_import'] = 0;
		}
		
		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($category_info)) {
			$data['path'] = $category_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($category_info)) {
			$data['parent_id'] = $category_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		/*
		 * getting all the unit ids and unit names
		 * */
		$data['units_array'] = array();
		$data['units_array'] = $this->model_catalog_category->getUnitIdsAndNames();

		//setting base_unit_id
		if (isset($this->request->post['unit_id'])) {
			$data['unit_id'] = $this->request->post['unit_id'];
		} elseif (!empty($category_info)) {
			$data['unit_id'] = $category_info['unit_id'];
		} else {
			$data['unit_id'] = 1;
		}

		if (isset($this->request->post['category_filter'])) {
			$filters = $this->request->post['category_filter'];
		} elseif (isset($this->request->get['category_id'])) {
			$filters = $this->model_catalog_category->getCategoryFilterGroups($this->request->get['category_id']);
		} else {
			$filters = array();
		}

		$data['category_filters'] = array();
		if(!empty($filters)){
				foreach ($filters as $filter_id) {
					$filter_info = $this->model_catalog_filter->getFilterGroup($filter_id);
					if ($filter_info) {
						$data['category_filters'][] = array(
							'filter_group_id' => $filter_info['filter_group_id'],
							'name'      => $filter_info['name']
						);
					}
				}
			}
		
		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();
		foreach ( $stores as $store ) {
			if (in_array($store['store_id'], explode(',', WSB_STORES_ID)) ) {
				$data['stores'][] = $store;
			}
		}

		$data['c_id'] = '' ;
        $data['category_str'] = '';
        if ( isset($this->request->get['category_id']) ) {
            $data['c_id'] = $this->request->get['category_id'];
            $data['category_str'] = $this->model_catalog_category->get_category_store_data($this->request->get['category_id']);
        }

		if (!empty($data['category_str'])){
			$data['store_id'] = $data['category_str'][0]['store_id'];
			$data['language'] = $data['category_str'][0]['language'];
			$data['meta_title'] = $data['category_str'][0]['meta_title'];
			$data['description'] = $data['category_str'][0]['description'];
			$data['meta_keywords'] = $data['category_str'][0]['meta_keywords'];
			$data['meta_description'] = $data['category_str'][0]['meta_description'];	
		}
		
		if (isset($this->request->post['category_store'])) {
			$data['category_store'] = $this->request->post['category_store'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_store'] = $this->model_catalog_category->getCategoryStores($this->request->get['category_id']);
		} else {
			$data['category_store'] = array(0);
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($category_info)) {
			$data['keyword'] = $category_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($category_info)) {
			$data['image'] = $category_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) /*&& is_file(DIR_IMAGE . $this->request->post['image'])*/) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($category_info) /*&& is_file(DIR_IMAGE . $category_info['image'])*/) {
			$data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($category_info)) {
			$data['top'] = $category_info['top'];
		} else {
			$data['top'] = 0;
		}

        if (isset($this->request->post['non_returnable'])) {
            $data['non_returnable'] = $this->request->post['non_returnable'];
        } elseif (!empty($category_info)) {
            $data['non_returnable'] = $category_info['non_returnable'];
        } else {
            $data['non_returnable'] = 0;
        }

        if (isset($this->request->post['changes_data'])) {
            $data['changes_data'] = $this->request->post['changes_data'];
        } else {
            $data['changes_data'] = '';
        }

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($category_info)) {
			$data['column'] = $category_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($category_info)) {
			$data['sort_order'] = $category_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($category_info)) {
			$data['status'] = $category_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['category_layout'])) {
			$data['category_layout'] = $this->request->post['category_layout'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_layout'] = $this->model_catalog_category->getCategoryLayouts($this->request->get['category_id']);
		} else {
			$data['category_layout'] = array();
		}

		$category_images = '';
				
		if (isset($this->request->get['category_id'])) {
			$category_images = $this->model_catalog_category->getCategoryImages($this->request->get['category_id']);
		}

		$data['category_images'] = array();
	    if (!empty($category_images) && count($category_images)>0) {

            foreach ($category_images as $category_image) {
                if (NGINX_ENABLED == 1) {
                    $image = $category_image['image'];
                    $thumb = $category_image['image'];
                    $directory = dirname($category_image['image']);
                } else {
                    if (is_file(DIR_IMAGE . $category_image['image'])) {
                        $image = $category_image['image'];
                        $thumb = $category_image['image'];
                        $directory = dirname($category_image['image']);
                    } else {
                        $image = '';
                        $thumb = 'no_image.png';
                        $directory = '';
                    }
                }
                $data['category_images'][] = array(
                    'image'      => $image,
                    'thumb'      => $this->model_tool_image->resize($thumb, $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
                    'image_height' => $category_image['image_height'],
                    'image_width' => $category_image['image_width'],
                    'sort_order' => $category_image['sort_order'],
                    'directory'	 => $directory
                );
            }
		}

		$this->load->model('design/layout');
		
		$data['layouts'] = $this->model_design_layout->getLayouts();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('catalog/category_form.tpl', $data));
	}

	protected function validateForm() {
		
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		$price_range_start = $this->request->post['price_range_start'];
		$price_range_end = $this->request->post['price_range_end'];
		if($price_range_start == '0' || $price_range_end == '0') {
			$this->error['price_range'] = $this->language->get('error_invalid_price_range');
		}
		if(!empty($price_range_start) || !empty($price_range_end)) {
			if(!preg_match('/^[0-9]+$/', $price_range_start) || !preg_match('/^[0-9]+$/', $price_range_end)) {
				$this->error['price_range'] = $this->language->get('error_invalid_price_format');
			}
			if((int)$price_range_start > (int)$price_range_end 
				|| ((int)$price_range_start == 0 || (int)$price_range_end == 0)) {
				$this->error['price_range'] = $this->language->get('error_invalid_price_range');
			}
		}
		foreach ($this->request->post['category_description'] as $language_id => $value) {  
			
		    if($language_id == 1){
                if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 1010)) {
                    $this->error['name'][$language_id] = $this->language->get('error_name');
                }
            }
                        if ((utf8_strlen($value['meta_title']) < 0) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['category_id']) && $url_alias_info['query'] != 'category_id=' . $this->request->get['category_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['category_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
		}
		
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 20
			);

			$results = $this->model_catalog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function store_data() {
		$json = array();
		if($this->request->get['store_id']){
			$store_id = $this->request->get['store_id'];
		}else{
			$store_id = '';
		}
		if($this->request->get['category_id']){
			$category_id = $this->request->get['category_id'];
		}else{
			$category_id = '';
		}
		$this->load->model('catalog/category');
		

		$results = $this->model_catalog_category->get_ajax_category_store_data($store_id,$category_id);
		
		foreach ($results as $result) {
				$json = array(
					'category_id' => $result['category_id'],
					'store_id'        => strip_tags(html_entity_decode($result['store_id'], ENT_QUOTES, 'UTF-8')),
					'language'        => $result['language'],
					'description'        => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
					'short_description'        => strip_tags(html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8')),
					'meta_title'        => strip_tags(html_entity_decode($result['meta_title'], ENT_QUOTES, 'UTF-8')),
					'meta_keywords'        => strip_tags(html_entity_decode($result['meta_keywords'], ENT_QUOTES, 'UTF-8')),
					'meta_description'        => strip_tags(html_entity_decode($result['meta_description'], ENT_QUOTES, 'UTF-8'))
				);
			}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
	
	//filter groups for category.php (in inventory upload data)
	public function autocomplete_filter_group() {
		$json = array();

		if (isset($this->request->get['filter_group_name'])) {
			
			$this->load->model('catalog/filter');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_group_name'],
				'start'       => 0,
				'limit'       => 15
			);
			
			$filters = $this->model_catalog_filter->getFilterGroups($filter_data);
			
			foreach ($filters as $filter) {
				$json[] = array(
					'filter_group_id' => $filter['filter_group_id'],
					'name'      => strip_tags(html_entity_decode($filter['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
