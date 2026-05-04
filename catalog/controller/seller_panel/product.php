<?php
class ControllerSellerProduct extends ControllerSellerAccount {
	private $error = array();

	public function index() {
		$this->load->language('seller/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/product');

		$this->getForm();
	}

	public function add() {
		$this->load->language('seller/product');
		$this->load->model('seller/product');

		$this->document->setTitle($this->language->get('heading_title'));
		// $this->load->model('catalog/product');
		// $this->load->model('catalog/filter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			// echo "<pre>";print_r($this->request->post); die;

			// $this->model_seller_product->dynamicmetatags($this->request->post);
			if (isset($this->request->post['product_filter']) && !empty($this->request->post['product_filter'])) {
				$product_filter = $this->request->post['product_filter'];
				$this->request->post['product_filter'] = explode(",", $product_filter);
			}
			$this->request->post['seller_tax']               = ''; 
			$this->request->post['commission']               = ''; 
			$this->request->post['points']                   = ''; 
			$this->request->post['manufacturer_id']			 = ''; 
			$this->request->post['location']				 = ''; 
			$this->request->post['status']				     = 2; 


			$this->model_seller_product->addProduct($this->request->post);	

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			// $this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			$data1 = array();
			
			// list($temp) = $this->MsLoader->MsHelper->loadTemplate('account-dashboard');

			// $this->response->setOutput($this->load->view($temp, $data1));
			if($this->request->post['single'] == 1) {
				$this->response->redirect($this->url->link('seller/manage-inventory/singlesStore', '', 'SSL'));
			}else{
				$this->response->redirect($this->url->link('seller/manage-inventory', '', 'SSL'));
			}
			// $this->load->controller('common/seller_header');


		}
		$this->getForm();
	}

	public function edit() {
		$this->load->language('seller/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if (isset($this->request->post['product_filter']) && !empty($this->request->post['product_filter'])) {
				$product_filter = $this->request->post['product_filter'];
				$this->request->post['product_filter'] = explode(",", $product_filter);
			}
			$this->request->post['points']                   = ''; 
			$this->request->post['manufacturer_id']			 = ''; 
			$this->request->post['location']				 = '';
			$this->request->post['sor_product']			 	 = '';
			$this->model_seller_product->dynamicmetatags($this->request->post);

			$this->model_seller_product->editProduct($this->request->get['product_id'], $this->request->post);
			if(isset($this->request->get['sor_product_id']) && !empty($this->request->get['sor_product_id'])){
				$this->request->post['sor_product']			 = 1;
				$this->model_seller_product->editProduct($this->request->get['sor_product_id'], $this->request->post);	
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			// $this->model_seller_product->store_data();

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('seller/manage-inventory', '', 'SSL'));
		}

		$this->getForm();
	}

/*	public function delete() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_seller_product->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
*/
/*	public function copy() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_seller_product->copyProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
            
            if (isset($this->request->get['filter_commission'])) {
			    $url .= '&filter_commission=' . $this->request->get['filter_commission'];
            }

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
            
            if (isset($this->request->get['filter_non_single'])) {
			    $url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		    }

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
*/
/*	public function copyToSingle() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
        

		if (isset($this->request->post['selected']) && $this->validateCopyToSingle()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_seller_product->copyProductToSingle($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
            
            if (isset($this->request->get['filter_commission'])) {
			$url .= '&filter_commission=' . $this->request->get['filter_commission'];
            }

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
            
            if (isset($this->request->get['filter_non_single'])) {
			$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		    }

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
*/
/*  public function SetPriceMarkup($value=''){
		if (isset($this->request->get['ajax_request'])) {
			if (isset($this->request->get['price_markup'])) {
				$this->session->data['copy_price_markup'] = $this->request->get['price_markup'];
			}
			if (isset($this->request->get['comm'])) {
				$this->session->data['copy_commission'] = $this->request->get['comm'];
			}

			echo json_encode(array('success' => 'success'));
		}else {
			echo json_encode(array('success' => 'fail'));
		}
	}
*/
/*	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

        if (isset($this->request->get['filter_commission'])) {
			$filter_commission = $this->request->get['filter_commission'];
		} else {
			$filter_commission = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
        
        if (isset($this->request->get['filter_non_single'])) {
			$filter_non_single = $this->request->get['filter_non_single'];
		} else {
			$filter_non_single = 0;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

        if (isset($this->request->get['filter_commission'])) {
			$url .= '&filter_commission=' . $this->request->get['filter_commission'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_non_single'])) {
			$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		}

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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy_single'] = $this->url->link('catalog/product/copyToSingle', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
            'filter_commission' => $filter_commission,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
            'filter_non_single' => $filter_non_single,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_seller_product->getTotalProducts($filter_data);

		$results = $this->model_seller_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_seller_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $result['price'],
                'commission' => $result['commission'],
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
        $data['column_commission'] = $this->language->get('column_commission');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
        $data['entry_commission'] = $this->language->get('entry_commission');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_copy_single'] = $this->language->get('button_copy_single');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

        if (isset($this->request->get['filter_commission'])) {
			$url .= '&filter_commission=' . $this->request->get['filter_commission'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_non_single'])) {
			$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$data['sort_model'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
		$data['sort_price'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
        $data['sort_commission'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.commission' . $url, 'SSL');
		$data['sort_quantity'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
		$data['sort_order'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

        if (isset($this->request->get['filter_commission'])) {
			$url .= '&filter_commission=' . $this->request->get['filter_commission'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_non_single'])) {
			$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
        $data['filter_commission'] = $filter_commission;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;
        $data['filter_non_single'] = $filter_non_single;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/seller_header');
		// $data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/seller_footer');

		$this->response->setOutput($this->load->view('catalog/product_list.tpl', $data));
	}
*/
	protected function getForm() {

        $this->document->addScript('catalog/view/javascript/multimerch/summernote/summernote.js');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_moderated'] = $this->language->get('text_moderated');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_option_value'] = $this->language->get('text_option_value');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');
		$data['text_compress_image'] = $this->language->get('text_compress_image');

		$data['entry_name'] = $this->language->get('entry_name');
        $data['entry_set_description'] = $this->language->get('entry_set_description');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_sku'] = $this->language->get('entry_sku');
		$data['entry_location'] = $this->language->get('entry_location');
		$data['entry_minimum'] = $this->language->get('entry_minimum');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_date_available'] = $this->language->get('entry_date_available');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_stock_status'] = $this->language->get('entry_stock_status');
		$data['entry_price'] = $this->language->get('entry_price');
        $data['entry_price_per_set'] = $this->language->get('entry_price_per_set');
        $data['entry_piece_in_set'] = $this->language->get('entry_piece_in_set');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_points'] = $this->language->get('entry_points');
		$data['entry_option_points'] = $this->language->get('entry_option_points');
		$data['entry_subtract'] = $this->language->get('entry_subtract');
		$data['entry_single'] = $this->language->get('entry_single');
		$data['entry_weight_class'] = $this->language->get('entry_weight_class');
		$data['entry_weight'] = $this->language->get('entry_weight');
		$data['entry_dimension'] = $this->language->get('entry_dimension');
		$data['entry_length_class'] = $this->language->get('entry_length_class');
		$data['entry_length'] = $this->language->get('entry_length');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_download'] = $this->language->get('entry_download');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_attribute'] = $this->language->get('entry_attribute');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_option'] = $this->language->get('entry_option');
		$data['entry_option_value'] = $this->language->get('entry_option_value');
		$data['entry_required'] = $this->language->get('entry_required');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_priority'] = $this->language->get('entry_priority');
		$data['entry_tag'] = $this->language->get('entry_tag');
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_recurring'] = $this->language->get('entry_recurring');
        $data['entry_seller_tax'] = $this->language->get('entry_seller_tax');
        $data['entry_commission'] = $this->language->get('entry_commission');
		$data['entry_language'] = $this->language->get('entry_language');

		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_sku'] = $this->language->get('help_sku');
		$data['help_minimum'] = $this->language->get('help_minimum');
		$data['help_manufacturer'] = $this->language->get('help_manufacturer');
		$data['help_stock_status'] = $this->language->get('help_stock_status');
		$data['help_points'] = $this->language->get('help_points');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_filter'] = $this->language->get('help_filter');
		$data['help_download'] = $this->language->get('help_download');
		$data['help_related'] = $this->language->get('help_related');
		$data['help_tag'] = $this->language->get('help_tag');
        $data['help_price'] = $this->language->get('help_price');
        $data['help_price_per_set'] = $this->language->get('help_price_per_set');
        $data['help_piece_in_set'] = $this->language->get('help_piece_in_set');
        $data['help_seller_tax'] = $this->language->get('help_seller_tax');
        $data['help_commission'] = $this->language->get('help_commission');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_attribute_add'] = $this->language->get('button_attribute_add');
		$data['button_option_add'] = $this->language->get('button_option_add');
		$data['button_option_value_add'] = $this->language->get('button_option_value_add');
		$data['button_discount_add'] = $this->language->get('button_discount_add');
		$data['button_special_add'] = $this->language->get('button_special_add');
		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_recurring_add'] = $this->language->get('button_recurring_add');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_attribute'] = $this->language->get('tab_attribute');
		$data['tab_option'] = $this->language->get('tab_option');
		$data['tab_recurring'] = $this->language->get('tab_recurring');
		$data['tab_discount'] = $this->language->get('tab_discount');
		$data['tab_special'] = $this->language->get('tab_special');
		$data['tab_image'] = $this->language->get('tab_image');
		$data['tab_links'] = $this->language->get('tab_links');
		$data['tab_design'] = $this->language->get('tab_design');
		$data['tab_openbay'] = $this->language->get('tab_openbay');
		$data['tab_store_data'] = $this->language->get('Store data');

		$data['add_single'] = 0;

		if(isset($this->request->get['single']) && $this->request->get['single'] == 1){
			$data['add_single'] = 1;
		}

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

		if (isset($this->error['sku'])) {
			$data['error_sku'] = $this->error['sku'];
		} else {
			$data['error_sku'] = '';
		}		
		if (isset($this->error['price'])) {
			$data['error_price'] = $this->error['price'];
		} else {
			$data['error_price'] = '';
		}		
		if (isset($this->error['weight'])) {
			$data['error_weight'] = $this->error['weight'];
		} else {
			$data['error_weight'] = '';	
		}	
		if (isset($this->error['tax_class'])) {
			$data['error_tax_class'] = $this->error['tax_class'];
		} else {
			$data['error_tax_class'] = '';
		}

		if (isset($this->error['date_available'])) {
			$data['error_date_available'] = $this->error['date_available'];
		} else {
			$data['error_date_available'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}


		if (isset($this->error['product_category'])) {
			$data['error_product_category'] = $this->error['product_category'];
		} else {
			$data['error_product_category'] = '';
		}

		if (isset($this->error['product_filter'])) {
			$data['error_product_filter'] = $this->error['product_filter'];
		} else {
			$data['error_product_filter'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('seller_panel/account-order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('seller/product', $url, 'SSL')
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('seller/product/add' . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('seller/product/edit' . '&product_id=' . $this->request->get['product_id'] .'&sor_product_id=' . $this->request->get['sor_product_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('seller/manage-inventory' . $url, 'SSL');

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_seller_product->getProduct($this->request->get['product_id']);
			if (empty($product_info)){
				$this->response->redirect($this->url->link('error/not_found'));
			}
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['p_id'] = isset($this->request->get['product_id'])?$this->request->get['product_id']:'';
		$data['store_data'] = $this->model_seller_product->get_store_data();

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_seller_product->getProductDescriptions($this->request->get['product_id']);
			//echo "<pre>"; print_r($data['product_description']); die;
		} else {
			$data['product_description'] = array();
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($product_info)) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}

		$this->load->model('setting/store');

		// $data['stores'] = $this->model_setting_store->getStores();

		$strs = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
		$stores = array();
		if ($strs) {
			foreach ($strs as $st) {
				if (isset($this->request->get['product_id'])) {
					$stores[] = array_merge($this->MsLoader->MsProduct->getStore($st['store_id']), 
					        				array('store_price'=> $this->MsLoader->MsProduct->getPriceForStore($st['store_id'], $this->request->get['product_id']) ));
				} else {
					$stores[] = array_merge($this->MsLoader->MsProduct->getStore($st['store_id']), 
					        				array('store_price' => ''));
				}
			}		
		}

		$data['stores'] = isset($stores)? $stores : 0;

		// echo "<pre>"; print_r($stores);die;
		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_seller_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($product_info)) {
			$data['keyword'] = $product_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($product_info)) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}

        if (isset($this->request->post['price_per_set'])) {
			$data['price_per_set'] = $this->request->post['price_per_set'];
		} elseif (!empty($product_info)) {
			$data['price_per_set'] = $product_info['price_per_set'];
		} else {
			$data['price_per_set'] = 0.0;
		}

        if (isset($this->request->post['piece_in_set'])) {
			$data['piece_in_set'] = $this->request->post['piece_in_set'];
		} elseif (!empty($product_info)) {
			$data['piece_in_set'] = $product_info['piece_in_set'];
		} else {
			$data['piece_in_set'] = 1;
		}

        if (isset($this->request->post['seller_tax'])) {
			$data['seller_tax'] = $this->request->post['seller_tax'];
		} elseif (!empty($product_info)) {
			$data['seller_tax'] = $product_info['seller_tax'];
		} else {
			$data['seller_tax'] = 0.0;
		}

        if (isset($this->request->post['commission'])) {
			$data['commission'] = $this->request->post['commission'];
		} elseif (!empty($product_info)) {
			$data['commission'] = $product_info['commission'];
		} else {
			$data['commission'] = 10.0;
		}

		// $this->load->model('seller/recurring');

		// $data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		/*		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_seller_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}*/

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($product_info)) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}		
		if (isset($this->request->post['single'])) {
			$data['is_single'] = $this->request->post['single'];
		} elseif (!empty($product_info)) {
			$data['is_single'] = $product_info['is_single'];
		} else {
			$data['is_single'] = 0;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 999;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getSellerStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 7;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = 2;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = $product_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = $product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = $product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = $product_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}
		/*
		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}*/

		/*		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}*/

		// Categories
		$this->load->model('catalog/information');
		$sellerStore = array();
		foreach ($data['stores'] as $value) {
			$sellerStore[] = $value['store_id'];
		}
		$seller_store = implode(',', $sellerStore);
		$get_language_id = (int)$this->config->get('config_language_id');
		$category = $this->model_catalog_information->getSellerStoreCategory($seller_store);
		$cat = array();
		foreach ($category as $value) {
			if($value['parent_id'] == 0 ){
				$cat_id = $value['category_id'];
				foreach ($category as $key) {
					if($key['parent_id'] != 0 &&  ($get_language_id == $value['language_id'] )){
						if($cat_id == $key['parent_id']){
							$data['category'][$cat_id]['category_id'] =$value['category_id'];
							$data['category'][$cat_id]['name'] =$value['name'];
							$data['category'][$cat_id]['parent_id'] =$value['parent_id'];
							$data['category'][$cat_id]['sub-category'][] = $key;
						}
					}
				}
			}
		}
		
		//get product category 
		if(isset($this->request->get['product_id']) && !empty($this->request->get['product_id'])){
			$data['seller_category'] = $this->model_catalog_information->getSellerProductCategory($this->request->get['product_id']);
		}else{
			$data['seller_category'] = '';
		}
        //echo "<pre>"; print_r($data['seller_category']); echo "<pre>";
		// Product store Info
		if (isset($this->request->post['product_store_info'])) {
			$data['product_store_info'] = $this->request->post['product_store_info'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store_info'] = 
			!empty($this->model_seller_product->get_product_store_data($this->request->get['product_id'])) ? $this->model_seller_product->get_product_store_data($this->request->get['product_id']) : $strs;
		} else {
			$data['product_store_info'] = $strs;
		}
		if (isset($this->request->get['product_id'])) {
			$data['category_str'] = $this->model_seller_product->get_product_store_data($this->request->get['product_id']);
			if (!empty($data['category_str'])){
				$data['store_id'] = $data['category_str'][0]['store_id'];
				$data['language'] = $data['category_str'][0]['language'];
				$data['meta_title'] = $data['category_str'][0]['meta_title'];
				$data['meta_keywords'] = $data['category_str'][0]['meta_keywords'];
				$data['meta_description'] = $data['category_str'][0]['meta_description'];	
			}		
		}

		// Filters
		$this->load->model('seller/filter');
		$all_filters = $this->model_seller_filter->getFilters();

		foreach ($all_filters as $one_filter) {
			$data['all_filters'][$one_filter['filter_group_id']]['name'] = $one_filter['group'];
			$data['all_filters'][$one_filter['filter_group_id']]['filter'][] = $one_filter;
		}

		$this->load->model('seller/filter');
		if (isset($this->request->post['product_filter'])) {
			$filters_string = $this->request->post['product_filter'];
			$filters = explode(',', $filters_string);
		} elseif (isset($this->request->get['product_id'])) {
			$filters = $this->model_seller_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();
		if(isset($filters) && !empty($filters)){
			foreach ($filters as $filter_id) {
				$filter_info = $this->model_seller_filter->getFilter($filter_id);

				if ($filter_info) {
					$data['product_filters'][] = array(
						'filter_id' => $filter_info['filter_id'],
						'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
					);
				}
			}
		}

		// Attributes
		/*		$this->load->model('catalog/attribute');

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_seller_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}*/

		// Options
		$this->load->model('seller/option');

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_seller_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_seller_option->getOptionValues($product_option['option_id']);
				}
			}
		}


		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_seller_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'quantity'          => $product_discount['quantity'],
				'priority'          => $product_discount['priority'],
				'price'             => $product_discount['price'],
				'store_id'          => $product_discount['store_id'],
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}

		/*		if (isset($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_seller_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'priority'          => $product_special['priority'],
				'price'             => $product_special['price'],
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
			);
		}*/

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_seller_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			//if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			//} else {
				//$image = '';
				//$thumb = 'no_image.png';
			//}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}

		// Downloads
		/*		$this->load->model('catalog/download');

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_seller_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}
		*/
		/*		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_seller_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_seller_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}
		*/
		/*		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}
		*/
		/*		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_seller_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}*/

		// $this->load->model('design/layout');

		// $data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/seller_header');
		// $data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/seller_footer');

		list($template) = $this->MsLoader->MsHelper->loadTemplate('product_form');

		$this->response->setOutput($this->load->view($template, $data));
	}

	protected function validateForm() {
		// if (!$this->user->hasPermission('modify', 'catalog/product')) {
		// 	$this->error['warning'] = $this->language->get('error_permission');
		// }

		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if($language_id == 1){
                if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 510)) {
                    $this->error['name'][$language_id] = $this->language->get('error_name');
                }
            }
			if ((utf8_strlen($value['meta_title']) < 0) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ((utf8_strlen($this->request->post['sku']) < 1) || (utf8_strlen($this->request->post['sku']) > 64)) {
			$this->error['sku'] = $this->language->get('error_sku');
		}		
		// if ((utf8_strlen($this->request->post['price']) < 1) || (utf8_strlen($this->request->post['price']) > 64)) {
		// 	$this->error['price'] = $this->language->get('error_price');
		// }elseif (!is_numeric($this->request->post['price'])) {
		// 	$this->error['price'] = $this->language->get('error_non_numeric_price');
		// }
		if (!is_numeric($this->request->post['price'])) {
			$this->error['price'] = $this->language->get('error_non_numeric_price');		
		}

		if (empty($this->request->post['weight'])) {
			$this->error['weight'] = $this->language->get('error_empty_weight');
		} elseif (!is_numeric($this->request->post['weight'])) {
			$this->error['weight'] = $this->language->get('error_non_numeric_weight');
		}		
		if (empty($this->request->post['tax_class_id'])) {
			$this->error['tax_class'] = $this->language->get('error_empty_tax_class');
		} 
		if (empty($this->request->post['product_category'])) {
			$this->error['product_category'] = $this->language->get('error_empty_category');
		} 
		if (empty($this->request->post['product_filter'])) {
			$this->error['product_filter'] = $this->language->get('error_empty_filter');
		} 

		/*  if (utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['product_id']) && $url_alias_info['query'] != 'product_id=' . $this->request->get['product_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['product_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}*/

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

/*	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
*/
/*	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}*/
/*	protected function validateCopyToSingle() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}*/

/*	public function autocomplete() {
		$json = array();
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 300;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_seller_product->getProducts($filter_data);
			//print_r($results);
			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_seller_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}
				$check_store = $this->model_seller_product->getAlternateProductInfo($result['is_single'], $result['model']);
				//print_r($check_store['selling_price']);
				//link for more details
				$href = HTTP_SERVER.'index.php?route=product/product&product_id='.$result['product_id'];
				$link = str_replace('khufiya_vibhag/', '', $href);
				//end
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'] . '  ' . strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'option'     => $option_data,
					'price'      => $result['price'],
					'set_description' => $result['set_description'],
					'selling_price' => $result['selling_price'],
					'price_per_set' => $result['price_per_set'],
					'piece_in_set'  => $result['piece_in_set'],
					'price_per_piece' => $result['selling_price'],
					'is_single'    => $result['is_single'],
					'quantity'     => $result['quantity'],
					'href'      => $link,
					'check_store'  => $check_store,
				);
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}*/
/*	public function store_data() {
		$json = array();
		if($this->request->get['store_id']){
			$store_id = $this->request->get['store_id'];
		}else{
			$store_id = '';
		}
		if($this->request->get['product_id']){
			$product_id = $this->request->get['product_id'];
		}else{
			$product_id = '';
		}
		$this->load->model('catalog/product');
		
		$results = $this->model_seller_product->get_ajax_product_store_data($store_id,$product_id);
		
		foreach ($results as $result) {
				$json = array(
					'product_id' => $result['product_id'],
					'store_id'        => strip_tags(html_entity_decode($result['store_id'], ENT_QUOTES, 'UTF-8')),
					'language'        => $result['language'],
					'meta_title'        => strip_tags(html_entity_decode($result['meta_title'], ENT_QUOTES, 'UTF-8')),
					'meta_keywords'        => strip_tags(html_entity_decode($result['meta_keywords'], ENT_QUOTES, 'UTF-8')),
					'meta_description'        => strip_tags(html_entity_decode($result['meta_description'], ENT_QUOTES, 'UTF-8'))
				);
			}
			

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}*/
	public function autocomplete_catagories() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('seller/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_seller_category->getCategories($filter_data);

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
	public function autocomplete_filters() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('seller/filter');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 13
			);

			$filters = $this->model_seller_filter->getFilters($filter_data);

			foreach ($filters as $filter) {
				$json[] = array(
					'filter_id' => $filter['filter_id'],
					'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
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

	public function getFiltersCategory($category_ids) {

		$this->load->model('catalog/category');
		if(isset($this->request->post['category']) && !empty($this->request->post['category'])){
			$category_ids = $this->request->post['category'];
		}
		if(isset($category_ids)){
			$data = explode(",", $category_ids);
			$child = array();
			foreach ($data as $value) {
				$child[] = explode("_", $value);
			}
			$val = array();
			foreach ($child as $value) {
				if($value[0] == "parent"){
					$val[] = $value[1];
				}
			}
			$category_id = implode(",", $val);
			if(isset($category_id) && !empty($category_id)){
				$data['all_filters'] = $this->model_catalog_category->getFiltersOfProducts($category_id);
			}else{
				$data['all_filters'] = '';
			}
			if(isset($this->request->post['temp']) && !empty($this->request->post['temp'])){

				list($template) = $this->MsLoader->MsHelper->loadTemplate('filter_product_form');
				echo $this->response->setOutput($this->load->view($template, $data));

			}else{
				return $data['all_filters'];
			}	
		}
		
	}
}