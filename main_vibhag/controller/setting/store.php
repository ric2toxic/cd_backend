<?php
class ControllerSettingStore extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('setting/store');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/store');

		$this->getList();
	}

	public function add() {
		$this->load->language('setting/store');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/store');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$store_id = $this->model_setting_store->addStore($this->request->post);

			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('config', $this->request->post, $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('setting/store');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/store');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_store->editStore($this->request->get['store_id'], $this->request->post);
			//	echo "<pre>"; print_r($this->request->post); exit;

			$this->load->model('setting/setting');

			if(isset($this->request->post['selected_category'])){
				$categories = $this->request->post['selected_category'];
				$this->model_setting_setting->addCategoriesToStore($this->request->get['store_id'], $categories);
			}

			$this->model_setting_setting->editSetting('config', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('setting/store', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->get['store_id'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('setting/store');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/store');

		$this->load->model('setting/setting');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $store_id) {
				$this->model_setting_store->deleteStore($store_id);

				$this->model_setting_setting->deleteSetting('config', $store_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('setting/store', $data);

		$url = '';

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
				'href' => $this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['add'] = $this->url->link('setting/store/add', 'token=' . $this->session->data['token'], 'SSL');
		$data['delete'] = $this->url->link('setting/store/delete', 'token=' . $this->session->data['token'], 'SSL');

		$data['stores'] = array();

		$data['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->config->get('config_name') . $data['text_default'],
				'url'      => HTTP_CATALOG,
				'edit'     => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL')
		);

		$store_total = $this->model_setting_store->getTotalStores();

		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = array(
					'store_id' => $result['store_id'],
					'name'     => $result['name'],
					'url'      => $result['url'],
					'edit'     => $this->url->link('setting/store/edit', 'token=' . $this->session->data['token'] . '&store_id=' . $result['store_id'], 'SSL'),
			'add_products'     => $this->url->link('setting/store/addProducts', 'token=' . $this->session->data['token'] . '&store_id=' . $result['store_id'], 'SSL')
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

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('setting/store_list.tpl', $data));
	}

	public function getForm() {
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('setting/store', $data);

		$this->load->model('setting/setting');
		$data['text_form'] = !isset($this->request->get['store_id']) ? $data['text_add'] : $data['text_edit'];
			
		$data['entry_ajax_cart'] = $this->language->get('entry_ajax_cart');
	
		$data['tab_developer'] = 'Developer'; //$this->language->get('tab_mail');

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

		$data['categories'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
		);
		$this->load->model('catalog/category');

		$results = $this->model_catalog_category->getCategories($filter_data);

		foreach ($results as $result) {
			$data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
			);
		}

		if(isset($this->request->get['store_id']) && $this->request->get['store_id'] > 0){
			$selected_categories = $this->model_setting_setting->getCategoriesToStore($this->request->get['store_id']);
			//echo "<pre>"; print_r($data['selected_categories']); exit;
			$data['selected_categories'] = array();
			foreach($selected_categories as $sc){
				$data['selected_categories'][] = $sc['category_id'];
			}
			//echo "<pre>"; print_r($data['selected_categories']); exit;
		}

		$data['column_sort_order'] = $this->language->get('column_sort_order');

		$data['sort'] = $sort;
		$data['order'] = $order;
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['url'])) {
			$data['error_url'] = $this->error['url'];
		} else {
			$data['error_url'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['owner'])) {
			$data['error_owner'] = $this->error['owner'];
		} else {
			$data['error_owner'] = '';
		}

		if (isset($this->error['store_code'])) {
			$data['error_store_code'] = $this->error['store_code'];
		} else {
			$data['error_store_code'] = '';
		}

		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = '';
		}

		if (isset($this->error['image_category'])) {
			$data['error_image_category'] = $this->error['image_category'];
		} else {
			$data['error_image_category'] = '';
		}

		if (isset($this->error['image_thumb'])) {
			$data['error_image_thumb'] = $this->error['image_thumb'];
		} else {
			$data['error_image_thumb'] = '';
		}

		if (isset($this->error['image_popup'])) {
			$data['error_image_popup'] = $this->error['image_popup'];
		} else {
			$data['error_image_popup'] = '';
		}

		if (isset($this->error['image_product'])) {
			$data['error_image_product'] = $this->error['image_product'];
		} else {
			$data['error_image_product'] = '';
		}

		if (isset($this->error['image_additional'])) {
			$data['error_image_additional'] = $this->error['image_additional'];
		} else {
			$data['error_image_additional'] = '';
		}

		if (isset($this->error['image_related'])) {
			$data['error_image_related'] = $this->error['image_related'];
		} else {
			$data['error_image_related'] = '';
		}

		if (isset($this->error['image_compare'])) {
			$data['error_image_compare'] = $this->error['image_compare'];
		} else {
			$data['error_image_compare'] = '';
		}

		if (isset($this->error['image_wishlist'])) {
			$data['error_image_wishlist'] = $this->error['image_wishlist'];
		} else {
			$data['error_image_wishlist'] = '';
		}

		if (isset($this->error['image_cart'])) {
			$data['error_image_cart'] = $this->error['image_cart'];
		} else {
			$data['error_image_cart'] = '';
		}

		if (isset($this->error['image_location'])) {
			$data['error_image_location'] = $this->error['image_location'];
		} else {
			$data['error_image_location'] = '';
		}

		if (isset($this->error['product_limit'])) {
			$data['error_product_limit'] = $this->error['product_limit'];
		} else {
			$data['error_product_limit'] = '';
		}

		if (isset($this->error['product_description_length'])) {
			$data['error_product_description_length'] = $this->error['product_description_length'];
		} else {
			$data['error_product_description_length'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
				'text' => $data['text_home'],
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
				'text' => $data['heading_title'],
				'href' => $this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (!isset($this->request->get['store_id'])) {
			$data['breadcrumbs'][] = array(
					'text' => $data['text_settings'],
					'href' => $this->url->link('setting/store/add', 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
					'text' => $data['text_settings'],
					'href' => $this->url->link('setting/store/edit', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->get['store_id'], 'SSL')
			);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (!isset($this->request->get['store_id'])) {
			$data['action'] = $this->url->link('setting/store/add', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('setting/store/edit', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->get['store_id'], 'SSL');
		}

		$data['cancel'] = $this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$this->load->model('setting/setting');

			$store_info = $this->model_setting_setting->getSetting('config', $this->request->get['store_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['config_mail_smtp_username'])) {
			$data['config_mail_smtp_username'] = $this->request->post['config_mail_smtp_username'];
		} elseif(isset($store_info['config_mail_smtp_username'])){
			$data['config_mail_smtp_username'] = $store_info['config_mail_smtp_username'];
		} else {
			$data['config_mail_smtp_username'] = $this->config->get('config_mail_smtp_username');
		}

		if (isset($this->request->post['config_mail_protocol'])) {
			$data['config_mail_protocol'] = $this->request->post['config_mail_protocol'];
		} elseif(isset($store_info['config_mail_protocol'])){
			$data['config_mail_protocol'] = $store_info['config_mail_protocol'];
		} else {
			$data['config_mail_protocol'] = $this->config->get('config_mail_protocol');
		}

		if (isset($this->request->post['config_mail_parameter'])) {
			$data['config_mail_parameter'] = $this->request->post['config_mail_parameter'];
		} elseif(isset($store_info['config_mail_parameter'])){
			$data['config_mail_parameter'] = $store_info['config_mail_parameter'];
		} else {
			$data['config_mail_parameter'] = $this->config->get('config_mail_parameter');
		}

		if (isset($this->request->post['config_mail_smtp_hostname'])) {
			$data['config_mail_smtp_hostname'] = $this->request->post['config_mail_smtp_hostname'];
		} elseif(isset($store_info['config_mail_smtp_hostname'])){
			$data['config_mail_smtp_hostname'] = $store_info['config_mail_smtp_hostname'];
		} else {
			$data['config_mail_smtp_hostname'] = $this->config->get('config_mail_smtp_hostname');
		}

		if (isset($this->request->post['config_mail_smtp_password'])) {
			$data['config_mail_smtp_password'] = $this->request->post['config_mail_smtp_password'];
		} elseif(isset($store_info['config_mail_smtp_password'])){
			$data['config_mail_smtp_password'] = $store_info['config_mail_smtp_password'];
		} else {
			$data['config_mail_smtp_password'] = $this->config->get('config_mail_smtp_password');
		}

		if (isset($this->request->post['config_mail_smtp_port'])) {
			$data['config_mail_smtp_port'] = $this->request->post['config_mail_smtp_port'];
		} elseif(isset($store_info['config_mail_smtp_port'])){
			$data['config_mail_smtp_port'] = $store_info['config_mail_smtp_port'];
		} elseif ($this->config->has('config_mail_smtp_port')) {
			$data['config_mail_smtp_port'] = $this->config->get('config_mail_smtp_port');
		} else {
			$data['config_mail_smtp_port'] = 25;
		}

		if (isset($this->request->post['config_mail_smtp_timeout'])) {
			$data['config_mail_smtp_timeout'] = $this->request->post['config_mail_smtp_timeout'];
		} elseif(isset($store_info['config_mail_smtp_timeout'])){
			$data['config_mail_smtp_timeout'] = $store_info['config_mail_smtp_timeout'];
		} elseif ($this->config->has('config_mail_smtp_timeout')) {
			$data['config_mail_smtp_timeout'] = $this->config->get('config_mail_smtp_timeout');
		} else {
			$data['config_mail_smtp_timeout'] = 25;
		}

		if (isset($this->request->post['config_mail_alert'])) {
			$data['config_mail_alert'] = $this->request->post['config_mail_alert'];
		} elseif(isset($store_info['config_mail_alert'])){
			$data['config_mail_alert'] = $store_info['config_mail_alert'];
		} else {
			$data['config_mail_alert'] = $this->config->get('config_mail_alert');
		}

		if (isset($this->request->post['config_url'])) {
			$data['config_url'] = $this->request->post['config_url'];
		} elseif (isset($store_info['config_url'])) {
			$data['config_url'] = $store_info['config_url'];
		} else {
			$data['config_url'] = '';
		}

		if (isset($this->request->post['config_ssl'])) {
			$data['config_ssl'] = $this->request->post['config_ssl'];
		} elseif (isset($store_info['config_ssl'])) {
			$data['config_ssl'] = $store_info['config_ssl'];
		} else {
			$data['config_ssl'] = '';
		}

		if (isset($this->request->post['config_name'])) {
			$data['config_name'] = $this->request->post['config_name'];
		} elseif (isset($store_info['config_name'])) {
			$data['config_name'] = $store_info['config_name'];
		} else {
			$data['config_name'] = '';
		}

		if (isset($this->request->post['config_owner'])) {
			$data['config_owner'] = $this->request->post['config_owner'];
		} elseif (isset($store_info['config_owner'])) {
			$data['config_owner'] = $store_info['config_owner'];
		} else {
			$data['config_owner'] = '';
		}

		if (isset($this->request->post['config_store_code'])) {
			$data['config_store_code'] = $this->request->post['config_store_code'];
		} elseif (isset($store_info['config_store_code'])) {
			$data['config_store_code'] = $store_info['config_store_code'];
		} else {
			$data['config_store_code'] = '';
		}

		if (isset($this->request->post['config_address'])) {
			$data['config_address'] = $this->request->post['config_address'];
		} elseif (isset($store_info['config_address'])) {
			$data['config_address'] = $store_info['config_address'];
		} else {
			$data['config_address'] = '';
		}

		if (isset($this->request->post['config_geocode'])) {
			$data['config_geocode'] = $this->request->post['config_geocode'];
		} elseif (isset($store_info['config_geocode'])) {
			$data['config_geocode'] = $store_info['config_geocode'];
		} else {
			$data['config_geocode'] = '';
		}

		if (isset($this->request->post['config_email'])) {
			$data['config_email'] = $this->request->post['config_email'];
		} elseif (isset($store_info['config_email'])) {
			$data['config_email'] = $store_info['config_email'];
		} else {
			$data['config_email'] = '';
		}

		if (isset($this->request->post['config_telephone'])) {
			$data['config_telephone'] = $this->request->post['config_telephone'];
		} elseif (isset($store_info['config_telephone'])) {
			$data['config_telephone'] = $store_info['config_telephone'];
		} else {
			$data['config_telephone'] = '';
		}

		if (isset($this->request->post['config_fax'])) {
			$data['config_fax'] = $this->request->post['config_fax'];
		} elseif (isset($store_info['config_fax'])) {
			$data['config_fax'] = $store_info['config_fax'];
		} else {
			$data['config_fax'] = '';
		}

		if (isset($this->request->post['config_image'])) {
			$data['config_image'] = $this->request->post['config_image'];
		} elseif (isset($store_info['config_image'])) {
			$data['config_image'] = $store_info['config_image'];
		} else {
			$data['config_image'] = '';
		}

		if (isset($this->request->post['config_bank_details'])) {
			$data['config_bank_details'] = $this->request->post['config_bank_details'];
		} elseif (isset($store_info['config_bank_details'])) {
			$data['config_bank_details'] = $store_info['config_bank_details'];
		} else {
			$data['config_bank_details'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['config_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['config_image'], 100, 100);
		} elseif (isset($store_info['config_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($store_info['config_image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['config_open'])) {
			$data['config_open'] = $this->request->post['config_open'];
		} elseif (isset($store_info['config_open'])) {
			$data['config_open'] = $store_info['config_open'];
		} else {
			$data['config_open'] = '';
		}

		if (isset($this->request->post['config_comment'])) {
			$data['config_comment'] = $this->request->post['config_comment'];
		} elseif (isset($store_info['config_comment'])) {
			$data['config_comment'] = $store_info['config_comment'];
		} else {
			$data['config_comment'] = '';
		}

		$data['sellers'] = $this->MsLoader->MsSeller->getSellers();

		if (isset($this->request->post['config_seller_id'])) {
			$data['config_seller_id'] = $this->request->post['config_seller_id'];
		} elseif(isset($store_info['config_seller_id'])) {
			$data['config_seller_id'] = $store_info['config_seller_id'];
		} else {
			$data['config_seller_id'] ='';
		}		

		if (isset($this->request->post['config_seller_store_commission'])) {
		    $data['config_seller_store_commission'] = $this->request->post['config_seller_store_commission'];
		} elseif(isset($store_info['config_seller_store_commission'])) {
		    $data['config_seller_store_commission'] = $store_info['config_seller_store_commission'];
		} else {
			$data['config_seller_store_commission'] ='5';
		}		
		if (isset($this->request->post['config_cart_limit'])) {
		    $data['config_cart_limit'] = $this->request->post['config_cart_limit'];
		} elseif(isset($store_info['config_cart_limit'])) {
		    $data['config_cart_limit'] = $store_info['config_cart_limit'];
		} else {
			$data['config_cart_limit'] = 0;
		}

		if (isset($this->request->post['config_store_body_wrapper_css'])) {
			$data['config_store_body_wrapper_css'] = $this->request->post['config_store_body_wrapper_css'];
		} elseif(isset($store_info['config_store_body_wrapper_css'])) {
			$data['config_store_body_wrapper_css'] = $store_info['config_store_body_wrapper_css'];
		} else {
			$data['config_store_body_wrapper_css'] = '';
		}

		if (isset($this->request->post['config_store_custom_css'])) {
			$data['config_store_custom_css'] = $this->request->post['config_store_custom_css'];
		} elseif(isset($store_info['config_store_custom_css'])) {
			$data['config_store_custom_css'] = $store_info['config_store_custom_css'];
		} else {
			$data['config_store_custom_css'] = '';
		}

		if (isset($this->request->post['config_store_custom_css_mobiletheme'])) {
			$data['config_store_custom_css_mobiletheme'] = $this->request->post['config_store_custom_css_mobiletheme'];
		} elseif(isset($store_info['config_store_custom_css_mobiletheme'])) {
			$data['config_store_custom_css_mobiletheme'] = $store_info['config_store_custom_css_mobiletheme'];
		} else {
			$data['config_store_custom_css_mobiletheme'] = '';
		}

		if (isset($this->request->post['config_google_analytics'])) {
			$data['config_google_analytics'] = $this->request->post['config_google_analytics'];
		} elseif(isset($store_info['config_google_analytics'])) {
			$data['config_google_analytics'] = $store_info['config_google_analytics'];
		} else {
			$data['config_google_analytics'] = '';
		}
		$this->load->model('localisation/location');

		$data['locations'] = $this->model_localisation_location->getLocations();

		if (isset($this->request->post['config_location'])) {
			$data['config_location'] = $this->request->post['config_location'];
		} elseif ($this->config->get('config_location')) {
			$data['config_location'] = $this->config->get('config_location');
		} else {
			$data['config_location'] = array();
		}

		if (isset($this->request->post['config_meta_title'])) {
			$data['config_meta_title'] = $this->request->post['config_meta_title'];
		} elseif (isset($store_info['config_meta_title'])) {
			$data['config_meta_title'] = $store_info['config_meta_title'];
		} else {
			$data['config_meta_title'] = '';
		}

		if (isset($this->request->post['config_meta_description'])) {
			$data['config_meta_description'] = $this->request->post['config_meta_description'];
		} elseif (isset($store_info['config_meta_description'])) {
			$data['config_meta_description'] = $store_info['config_meta_description'];
		} else {
			$data['config_meta_description'] = '';
		}

		if (isset($this->request->post['config_meta_keyword'])) {
			$data['config_meta_keyword'] = $this->request->post['config_meta_keyword'];
		} elseif (isset($store_info['config_meta_keyword'])) {
			$data['config_meta_keyword'] = $store_info['config_meta_keyword'];
		} else {
			$data['config_meta_keyword'] = '';
		}

		if (isset($this->request->post['config_layout_id'])) {
			$data['config_layout_id'] = $this->request->post['config_layout_id'];
		} elseif (isset($store_info['config_layout_id'])) {
			$data['config_layout_id'] = $store_info['config_layout_id'];
		} else {
			$data['config_layout_id'] = '';
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		if (isset($this->request->post['config_template'])) {
			$data['config_template'] = $this->request->post['config_template'];
		} elseif (isset($store_info['config_template'])) {
			$data['config_template'] = $store_info['config_template'];
		} else {
			$data['config_template'] = '';
		}

		if (isset($this->request->post['config_colors'])) {
			$data['config_colors'] = $this->request->post['config_colors'];
		} elseif (isset($store_info['config_colors'])) {
			$data['config_colors'] = $store_info['config_colors'];
		} else {
			$data['config_colors'] = '';
		}

		$data['templates'] = array();

		$data['colors'] = $this->getStyle($store_info['config_template']);

		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);

		foreach ($directories as $directory) {
			$data['templates'][] = basename($directory);
		}

		if (isset($this->request->post['config_country_id'])) {
			$data['config_country_id'] = $this->request->post['config_country_id'];
		} elseif (isset($store_info['config_country_id'])) {
			$data['config_country_id'] = $store_info['config_country_id'];
		} else {
			$data['config_country_id'] = $this->config->get('config_country_id');
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['config_zone_id'])) {
			$data['config_zone_id'] = $this->request->post['config_zone_id'];
		} elseif (isset($store_info['config_zone_id'])) {
			$data['config_zone_id'] = $store_info['config_zone_id'];
		} else {
			$data['config_zone_id'] = $this->config->get('config_zone_id');
		}

		if (isset($this->request->post['config_language'])) {
			$data['config_language'] = $this->request->post['config_language'];
		} elseif (isset($store_info['config_language'])) {
			$data['config_language'] = $store_info['config_language'];
		} else {
			$data['config_language'] = $this->config->get('config_language');
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['config_currency'])) {
			$data['config_currency'] = $this->request->post['config_currency'];
		} elseif (isset($store_info['config_currency'])) {
			$data['config_currency'] = $store_info['config_currency'];
		} else {
			$data['config_currency'] = $this->config->get('config_currency');
		}

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		if (isset($this->request->post['config_product_limit'])) {
			$data['config_product_limit'] = $this->request->post['config_product_limit'];
		} elseif (isset($store_info['config_product_limit'])) {
			$data['config_product_limit'] = $store_info['config_product_limit'];
		} else {
			$data['config_product_limit'] = '15';
		}

		if (isset($this->request->post['config_product_description_length'])) {
			$data['config_product_description_length'] = $this->request->post['config_product_description_length'];
		} elseif (isset($store_info['config_product_description_length'])) {
			$data['config_product_description_length'] = $store_info['config_product_description_length'];
		} else {
			$data['config_product_description_length'] = '100';
		}

		if (isset($this->request->post['config_tax'])) {
			$data['config_tax'] = $this->request->post['config_tax'];
		} elseif (isset($store_info['config_tax'])) {
			$data['config_tax'] = $store_info['config_tax'];
		} else {
			$data['config_tax'] = '';
		}

		if (isset($this->request->post['config_tax_default'])) {
			$data['config_tax_default'] = $this->request->post['config_tax_default'];
		} elseif (isset($store_info['config_tax_default'])) {
			$data['config_tax_default'] = $store_info['config_tax_default'];
		} else {
			$data['config_tax_default'] = '';
		}

		if (isset($this->request->post['config_tax_customer'])) {
			$data['config_tax_customer'] = $this->request->post['config_tax_customer'];
		} elseif (isset($store_info['config_tax_customer'])) {
			$data['config_tax_customer'] = $store_info['config_tax_customer'];
		} else {
			$data['config_tax_customer'] = '';
		}

		if (isset($this->request->post['config_customer_price'])) {
			$data['config_customer_price'] = $this->request->post['config_customer_price'];
		} elseif (isset($store_info['config_customer_price'])) {
			$data['config_customer_price'] = $store_info['config_customer_price'];
		} else {
			$data['config_customer_price'] = '';
		}

		if (isset($this->request->post['config_account_id'])) {
			$data['config_account_id'] = $this->request->post['config_account_id'];
		} elseif (isset($store_info['config_account_id'])) {
			$data['config_account_id'] = $store_info['config_account_id'];
		} else {
			$data['config_account_id'] = '';
		}

		$this->load->model('catalog/information');

		$data['informations'] = $this->model_catalog_information->getInformations();

		if (isset($this->request->post['config_cart_weight'])) {
			$data['config_cart_weight'] = $this->request->post['config_cart_weight'];
		} elseif (isset($store_info['config_cart_weight'])) {
			$data['config_cart_weight'] = $store_info['config_cart_weight'];
		} else {
			$data['config_cart_weight'] = '';
		}

		if (isset($this->request->post['config_checkout_guest'])) {
			$data['config_checkout_guest'] = $this->request->post['config_checkout_guest'];
		} elseif (isset($store_info['config_checkout_guest'])) {
			$data['config_checkout_guest'] = $store_info['config_checkout_guest'];
		} else {
			$data['config_checkout_guest'] = '';
		}

		if (isset($this->request->post['config_checkout_id'])) {
			$data['config_checkout_id'] = $this->request->post['config_checkout_id'];
		} elseif (isset($store_info['config_checkout_id'])) {
			$data['config_checkout_id'] = $store_info['config_checkout_id'];
		} else {
			$data['config_checkout_id'] = '';
		}

		if (isset($this->request->post['config_order_status_id'])) {
			$data['config_order_status_id'] = $this->request->post['config_order_status_id'];
		} elseif (isset($store_info['config_order_status_id'])) {
			$data['config_order_status_id'] = $store_info['config_order_status_id'];
		} else {
			$data['config_order_status_id'] = '';
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['config_stock_display'])) {
			$data['config_stock_display'] = $this->request->post['config_stock_display'];
		} elseif (isset($store_info['config_stock_display'])) {
			$data['config_stock_display'] = $store_info['config_stock_display'];
		} else {
			$data['config_stock_display'] = '';
		}

		if (isset($this->request->post['config_stock_checkout'])) {
			$data['config_stock_checkout'] = $this->request->post['config_stock_checkout'];
		} elseif (isset($store_info['config_stock_checkout'])) {
			$data['config_stock_checkout'] = $store_info['config_stock_checkout'];
		} else {
			$data['config_stock_checkout'] = '';
		}

		if (isset($this->request->post['config_logo'])) {
			$data['config_logo'] = $this->request->post['config_logo'];
		} elseif (isset($store_info['config_logo'])) {
			$data['config_logo'] = $store_info['config_logo'];
		} else {
			$data['config_logo'] = '';
		}

		if (isset($this->request->post['config_logo'])) {
			$data['logo'] = $this->model_tool_image->resize($this->request->post['config_logo'], 100, 100);
		} elseif (isset($store_info['config_logo'])) {
			$data['logo'] = $this->model_tool_image->resize($store_info['config_logo'], 100, 100);
		} else {
			$data['logo'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['config_icon'])) {
			$data['config_icon'] = $this->request->post['config_icon'];
		} elseif (isset($store_info['config_icon'])) {
			$data['config_icon'] = $store_info['config_icon'];
		} else {
			$data['config_icon'] = '';
		}

		if (isset($this->request->post['config_icon'])) {
			$data['icon'] = $this->model_tool_image->resize($this->request->post['config_icon'], 100, 100);
		} elseif (isset($store_info['config_icon'])) {
			$data['icon'] = $this->model_tool_image->resize($store_info['config_icon'], 100, 100);
		} else {
			$data['icon'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['config_image_category_height'])) {
			$data['config_image_category_height'] = $this->request->post['config_image_category_height'];
		} elseif (isset($store_info['config_image_category_height'])) {
			$data['config_image_category_height'] = $store_info['config_image_category_height'];
		} else {
			$data['config_image_category_height'] = 80;
		}

		if (isset($this->request->post['config_image_thumb_width'])) {
			$data['config_image_thumb_width'] = $this->request->post['config_image_thumb_width'];
		} elseif (isset($store_info['config_image_thumb_width'])) {
			$data['config_image_thumb_width'] = $store_info['config_image_thumb_width'];
		} else {
			$data['config_image_thumb_width'] = 250;
		}

		if (isset($this->request->post['config_image_thumb_height'])) {
			$data['config_image_thumb_height'] = $this->request->post['config_image_thumb_height'];
		} elseif (isset($store_info['config_image_thumb_height'])) {
			$data['config_image_thumb_height'] = $store_info['config_image_thumb_height'];
		} else {
			$data['config_image_thumb_height'] = 375;
		}

		if (isset($this->request->post['config_image_popup_width'])) {
			$data['config_image_popup_width'] = $this->request->post['config_image_popup_width'];
		} elseif (isset($store_info['config_image_popup_width'])) {
			$data['config_image_popup_width'] = $store_info['config_image_popup_width'];
		} else {
			$data['config_image_popup_width'] = 800;
		}

		if (isset($this->request->post['config_image_popup_height'])) {
			$data['config_image_popup_height'] = $this->request->post['config_image_popup_height'];
		} elseif (isset($store_info['config_image_popup_height'])) {
			$data['config_image_popup_height'] = $store_info['config_image_popup_height'];
		} else {
			$data['config_image_popup_height'] = 1200;
		}

		if (isset($this->request->post['config_image_product_width'])) {
			$data['config_image_product_width'] = $this->request->post['config_image_product_width'];
		} elseif (isset($store_info['config_image_product_width'])) {
			$data['config_image_product_width'] = $store_info['config_image_product_width'];
		} else {
			$data['config_image_product_width'] = 250;
		}

		if (isset($this->request->post['config_image_product_height'])) {
			$data['config_image_product_height'] = $this->request->post['config_image_product_height'];
		} elseif (isset($store_info['config_image_product_height'])) {
			$data['config_image_product_height'] = $store_info['config_image_product_height'];
		} else {
			$data['config_image_product_height'] = 375;
		}

		if (isset($this->request->post['config_image_category_width'])) {
			$data['config_image_category_width'] = $this->request->post['config_image_category_width'];
		} elseif (isset($store_info['config_image_category_width'])) {
			$data['config_image_category_width'] = $store_info['config_image_category_width'];
		} else {
			$data['config_image_category_width'] = 80;
		}

		if (isset($this->request->post['config_image_additional_width'])) {
			$data['config_image_additional_width'] = $this->request->post['config_image_additional_width'];
		} elseif (isset($store_info['config_image_additional_width'])) {
			$data['config_image_additional_width'] = $store_info['config_image_additional_width'];
		} else {
			$data['config_image_additional_width'] = 74;
		}

		if (isset($this->request->post['config_image_additional_height'])) {
			$data['config_image_additional_height'] = $this->request->post['config_image_additional_height'];
		} elseif (isset($store_info['config_image_additional_height'])) {
			$data['config_image_additional_height'] = $store_info['config_image_additional_height'];
		} else {
			$data['config_image_additional_height'] = 111;
		}

		if (isset($this->request->post['config_image_related_width'])) {
			$data['config_image_related_width'] = $this->request->post['config_image_related_width'];
		} elseif (isset($store_info['config_image_related_width'])) {
			$data['config_image_related_width'] = $store_info['config_image_related_width'];
		} else {
			$data['config_image_related_width'] = 250;
		}

		if (isset($this->request->post['config_image_related_height'])) {
			$data['config_image_related_height'] = $this->request->post['config_image_related_height'];
		} elseif (isset($store_info['config_image_related_height'])) {
			$data['config_image_related_height'] = $store_info['config_image_related_height'];
		} else {
			$data['config_image_related_height'] = 375;
		}

		if (isset($this->request->post['config_image_compare_width'])) {
			$data['config_image_compare_width'] = $this->request->post['config_image_compare_width'];
		} elseif (isset($store_info['config_image_compare_width'])) {
			$data['config_image_compare_width'] = $store_info['config_image_compare_width'];
		} else {
			$data['config_image_compare_width'] = 74;
		}

		if (isset($this->request->post['config_image_compare_height'])) {
			$data['config_image_compare_height'] = $this->request->post['config_image_compare_height'];
		} elseif (isset($store_info['config_image_compare_height'])) {
			$data['config_image_compare_height'] = $store_info['config_image_compare_height'];
		} else {
			$data['config_image_compare_height'] = 111;
		}

		if (isset($this->request->post['config_image_wishlist_width'])) {
			$data['config_image_wishlist_width'] = $this->request->post['config_image_wishlist_width'];
		} elseif (isset($store_info['config_image_wishlist_width'])) {
			$data['config_image_wishlist_width'] = $store_info['config_image_wishlist_width'];
		} else {
			$data['config_image_wishlist_width'] = 74;
		}

		if (isset($this->request->post['config_image_wishlist_height'])) {
			$data['config_image_wishlist_height'] = $this->request->post['config_image_wishlist_height'];
		} elseif (isset($store_info['config_image_wishlist_height'])) {
			$data['config_image_wishlist_height'] = $store_info['config_image_wishlist_height'];
		} else {
			$data['config_image_wishlist_height'] = 111;
		}

		if (isset($this->request->post['config_image_cart_width'])) {
			$data['config_image_cart_width'] = $this->request->post['config_image_cart_width'];
		} elseif (isset($store_info['config_image_cart_width'])) {
			$data['config_image_cart_width'] = $store_info['config_image_cart_width'];
		} else {
			$data['config_image_cart_width'] = 74;
		}

		if (isset($this->request->post['config_image_cart_height'])) {
			$data['config_image_cart_height'] = $this->request->post['config_image_cart_height'];
		} elseif (isset($store_info['config_image_cart_height'])) {
			$data['config_image_cart_height'] = $store_info['config_image_cart_height'];
		} else {
			$data['config_image_cart_height'] = 111;
		}

		if (isset($this->request->post['config_image_location_width'])) {
			$data['config_image_location_width'] = $this->request->post['config_image_location_width'];
		} elseif (isset($store_info['config_image_location_width'])) {
			$data['config_image_location_width'] = $store_info['config_image_location_width'];
		} else {
			$data['config_image_location_width'] = 240;
		}

		if (isset($this->request->post['config_image_location_height'])) {
			$data['config_image_location_height'] = $this->request->post['config_image_location_height'];
		} elseif (isset($store_info['config_image_location_height'])) {
			$data['config_image_location_height'] = $store_info['config_image_location_height'];
		} else {
			$data['config_image_location_height'] = 180;
		}

		if (isset($this->request->post['config_secure'])) {
			$data['config_secure'] = $this->request->post['config_secure'];
		} elseif (isset($store_info['config_secure'])) {
			$data['config_secure'] = $store_info['config_secure'];
		} else {
			$data['config_secure'] = '';
		}

		if (isset($this->request->post['config_logo_width'])) {
			$data['config_logo_width'] = $this->request->post['config_logo_width'];
		} elseif (isset($store_info['config_logo_width'])) {
			$data['config_logo_width'] = $store_info['config_logo_width'];
		} else {
			$data['config_logo_width'] = 268;
		}

		if (isset($this->request->post['config_logo_height'])) {
			$data['config_logo_height'] = $this->request->post['config_logo_height'];
		} elseif (isset($store_info['config_logo_height'])) {
			$data['config_logo_height'] = $store_info['config_logo_height'];
		} else {
			$data['config_logo_height'] = 70;
		}

		if (isset($this->request->post['config_mobile_logo_width'])) {
			$data['config_logo_width'] = $this->request->post['config_mobile_logo_width'];
		} elseif (isset($store_info['config_mobile_logo_width'])) {
			$data['config_mobile_logo_width'] = $store_info['config_mobile_logo_width'];
		} else {
			$data['config_mobile_logo_width'] = 268;
		}

		if (isset($this->request->post['config_mobile_logo_height'])) {
			$data['config_mobile_logo_height'] = $this->request->post['config_mobile_logo_height'];
		} elseif (isset($store_info['config_mobile_logo_height'])) {
			$data['config_mobile_logo_height'] = $store_info['config_mobile_logo_height'];
		} else {
			$data['config_mobile_logo_height'] = 70;
		}


		if (isset($this->request->post['payment_methods'])) {
			$selected_payment_methods = $this->request->post['payment_methods'];
		} else {
			$payment_methods = '';
		}


		$this->load->model('setting/store');
		$data['payment_methods'] = $this->model_setting_store->getExtensions();
		if(isset($this->request->get['store_id']) && $this->request->get['store_id'] > 0){
			$data['selected_payment_methods'] = $this->model_setting_store->getSelectedExtensions($this->request->get['store_id']);

		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('setting/store_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['config_url']) {
			$this->error['url'] = $this->language->get('error_url');
		}

		if (!$this->request->post['config_name']) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['config_owner']) < 3) || (utf8_strlen($this->request->post['config_owner']) > 64)) {
			$this->error['owner'] = $this->language->get('error_owner');
		}

		if ((utf8_strlen($this->request->post['config_store_code']) < 1) || (utf8_strlen($this->request->post['config_store_code']) > 16)) {
			$this->error['store_code'] = $this->language->get('error_store_code');
		}

		if ((utf8_strlen($this->request->post['config_address']) < 3) || (utf8_strlen($this->request->post['config_address']) > 256)) {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ((utf8_strlen($this->request->post['config_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['config_email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['config_telephone']) < 3) || (utf8_strlen($this->request->post['config_telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if (!$this->request->post['config_meta_title']) {
			$this->error['meta_title'] = $this->language->get('error_meta_title');
		}

		if (!$this->request->post['config_image_category_width'] || !$this->request->post['config_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
			$this->error['image_product'] = $this->language->get('error_image_product');
		}

		if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['config_image_related_width'] || !$this->request->post['config_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['config_image_compare_width'] || !$this->request->post['config_image_compare_height']) {
			$this->error['image_compare'] = $this->language->get('error_image_compare');
		}

		if (!$this->request->post['config_image_wishlist_width'] || !$this->request->post['config_image_wishlist_height']) {
			$this->error['image_wishlist'] = $this->language->get('error_image_wishlist');
		}

		if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
			$this->error['image_cart'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['config_image_location_width'] || !$this->request->post['config_image_location_height']) {
			$this->error['image_location'] = $this->language->get('error_image_location');
		}

		if (!$this->request->post['config_product_limit']) {
			$this->error['product_limit'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['config_product_description_length']) {
			$this->error['product_description_length'] = $this->language->get('error_limit');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('sale/order');

		foreach ($this->request->post['selected'] as $store_id) {
			if (!$store_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}

			$store_total = $this->model_sale_order->getTotalOrdersByStoreId($store_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
		}

		return !$this->error;
	}

	public function template() {
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		if (is_file(DIR_IMAGE . 'templates/' . basename($this->request->get['template']) . '.png')) {
			$this->response->setOutput($server . 'image/templates/' . basename($this->request->get['template']) . '.png');
		} else {
			$this->response->setOutput($server . 'image/no_image.jpg');
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
					'country_id'        => $country_info['country_id'],
					'name'              => $country_info['name'],
					'iso_code_2'        => $country_info['iso_code_2'],
					'iso_code_3'        => $country_info['iso_code_3'],
					'address_format'    => $country_info['address_format'],
					'postcode_required' => $country_info['postcode_required'],
					'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
					'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/**
	 * polulating product to store table 
	 * @author Parth Gupta
	 * @dateTime 2016-03-28T16:43:40+0530
	 * @return void diplays the insert queries on screen
	 */
	public function addProducts(){
	    $this->load->model('setting/setting');

	    if (isset($this->request->get['store_id'])) {
	    	$store_id = $this->request->get['store_id'];
	    } else {
	    	return false;
	    }

	    $store_info = $this->model_setting_setting->getSetting('config', $store_id);

	    $sql = "SELECT mp.product_id FROM ".DB_PREFIX."ms_product mp
	            INNER JOIN ".DB_PREFIX."product_to_store p2s 
	            ON (mp.product_id = p2s.product_id)
	            WHERE mp.seller_id =".
	            $store_info['config_seller_id']." 
	            AND p2s.product_id NOT IN 
	            (SELECT product_id FROM oc_product_to_store WHERE store_id IN (".$store_id.")) 
	            GROUP BY mp.product_id";
	    
	    $results = $this->db->query($sql);

	    foreach ($results->rows as $result) {
	        echo "INSERT INTO ".DB_PREFIX."product_to_store 
	                SET product_id =".$result['product_id'].", 
	                store_id=".$store_id . ";<br>";
	    }
	}

	public function getStyle($store_info){

		if (isset($this->request->post['style'])) {
			$style = $this->request->post['style'];
			if ($style != '' && $style != null) {
				if(file_exists(DIR_STOREFRONT . "catalog/view/theme/" . $style . "/stylesheet/css.txt")) {
					$filename = DIR_STOREFRONT . "catalog/view/theme/" . $style . "/stylesheet/css.txt";
					$handle = fopen($filename, "r");
					if (filesize($filename) > 0) {
						$contents = fread($handle, filesize($filename));
						fclose($handle);
						$remove = "\n";
						$rm = explode($remove, $contents);
						$colors = array();
						$data['colors'] = array_filter($rm);
						$data['token'] = $this->session->data['token'];
						if (isset($this->request->post['temp'])) {
							echo $this->response->setOutput($this->load->view('ajax/css_at_store.tpl', $data));
						}
					} else {
						if (isset($this->request->post['temp'])) {
							$data['colors'] = '';
							$data['token'] = $this->session->data['token'];
							$data['default_style'] = $style;
							echo $this->response->setOutput($this->load->view('ajax/css_at_store.tpl', $data));
						}
					}
				}else{
					$data['colors'] = '';
					$data['token'] = $this->session->data['token'];
					$data['default_style'] = $style;
					echo $this->response->setOutput($this->load->view('ajax/css_at_store.tpl', $data));
				}
			}
		}else{
			if(file_exists(DIR_STOREFRONT . "catalog/view/theme/" . $store_info . "/stylesheet/css.txt")) {
				$filename = DIR_STOREFRONT . "catalog/view/theme/" . $store_info . "/stylesheet/css.txt";
				$handle = fopen($filename, "r");
				if (filesize($filename) > 0) {
					$contents = fread($handle, filesize($filename));
					fclose($handle);
					$remove = "\n";
					$rm = explode($remove, $contents);
					$colors = array();
					$data['colors'] = array_filter($rm);
					return $data['colors'];
				} else {
					$data['colors'] = array(
						0=> $store_info
					);
					return $data['colors'];				}

			}else{
				$data['colors'] = array(
					0=> $store_info
				);
				return $data['colors'];
			}

		}

	}
}