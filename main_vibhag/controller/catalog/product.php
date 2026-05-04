<?php

include_once '../rabbitmq/task_directive_constants.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ControllerCatalogProduct extends Controller {
	private $error = array();

	public function index() {

		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {

			$this->load->language('franchise/product');
			$this->document->setTitle($this->language->get('heading_title'));

		} else {

			$this->load->language('catalog/product');
			$this->document->setTitle($this->language->get('heading_title'));

		}

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
		$this->load->model('catalog/filter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_product->dynamicmetatags($this->request->post);

			$this->model_catalog_product->addProduct($this->request->post);

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

			if (isset($this->request->get['filter_seller_sku'])) {
				$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_commission'])) {
				$url .= '&filter_commission=' . $this->request->get['filter_commission'];
			}

			if (isset($this->request->get['filter_non_single'])) {
				$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
			}
			if (isset($this->request->get['filter_non_sor'])) {
				$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
			}

			if (isset($this->request->get['filter_seller_list'])) {
				$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
			}

			if (isset($this->request->get['filter_solr_enabled'])) {
				$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
			}

			if (isset($this->request->get['filter_page_limit'])) {
				$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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

		$this->getForm();
	}

	public function edit() {

		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$total_quantity = $this->request->post['quantity'];
			if (isset($this->request->post['product_option']) && !empty($this->request->post['product_option'])) {
				$total_quantity = 0;
				foreach ($this->request->post['product_option'] as $product_options) {
					foreach ($product_options['product_option_value'] as $product_option_value) {
						$total_quantity += (int) $product_option_value['quantity'];
					}
				}
			}
			$this->request->post['quantity'] = $total_quantity;

			$this->model_catalog_product->dynamicmetatags($this->request->post);

			$this->request->post['changes_data'] = $_POST['changes_data'];

			$this->model_catalog_product->editProduct($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->model_catalog_product->store_data($this->request->post, $this->request->get['product_id']);

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

			if (isset($this->request->get['filter_seller_sku'])) {
				$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_commission'])) {
				$url .= '&filter_commission=' . $this->request->get['filter_commission'];
			}

			if (isset($this->request->get['filter_non_single'])) {
				$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
			}
			if (isset($this->request->get['filter_non_sor'])) {
				$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
			}

			if (isset($this->request->get['filter_seller_list'])) {
				$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
			}

			if (isset($this->request->get['filter_solr_enabled'])) {
				$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
			}

			if (isset($this->request->get['filter_page_limit'])) {
				$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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

			if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
				$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&filter_franchise_tab=' . '1', 'SSL'));
			} else {
				$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->deleteProduct($product_id);
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

			if (isset($this->request->get['filter_seller_sku'])) {
				$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			}

			if (isset($this->request->get['filter_commission'])) {
				$url .= '&filter_commission=' . $this->request->get['filter_commission'];
			}

			if (isset($this->request->get['filter_non_single'])) {
				$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
			}
			if (isset($this->request->get['filter_non_sor'])) {
				$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
			}

			if (isset($this->request->get['filter_solr_enabled'])) {
				$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
			}

			if (isset($this->request->get['filter_seller_list'])) {
				$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
			}

			if (isset($this->request->get['filter_page_limit'])) {
				$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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

			if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
				$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&filter_franchise_tab=' . '1', 'SSL'));
			} else {
				$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}

		}

		$this->getList();
	}

	public function copy() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->copyProduct($product_id);
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

			if (isset($this->request->get['filter_seller_sku'])) {
				$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_non_single'])) {
				$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
			}
			if (isset($this->request->get['filter_non_sor'])) {
				$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
			}

			if (isset($this->request->get['filter_solr_enabled'])) {
				$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
			}

			if (isset($this->request->get['filter_seller_list'])) {
				$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
			}

			if (isset($this->request->get['filter_page_limit'])) {
				$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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
	public function copyToSingle() {
		$this->load->language('catalog/product');

		$this->load->model('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopyToSingle()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->copyProductToSingle($product_id);
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

			if (isset($this->request->get['filter_seller_sku'])) {
				$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_non_single'])) {
				$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
			}

			if (isset($this->request->get['filter_non_sor'])) {
				$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
			}

			if (isset($this->request->get['filter_solr_enabled'])) {
				$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
			}

			if (isset($this->request->get['filter_seller_list'])) {
				$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
			}

			if (isset($this->request->get['filter_page_limit'])) {
				$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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

	public function SetPriceMarkup($value = '') {
		if (isset($this->request->get['ajax_request'])) {
			if (isset($this->request->get['price_markup'])) {
				$this->session->data['copy_price_markup'] = $this->request->get['price_markup'];
			}
			if (isset($this->request->get['comm'])) {
				$this->session->data['copy_commission'] = $this->request->get['comm'];
			}

			echo json_encode(array('success' => 'success'));
		} else {
			echo json_encode(array('success' => 'fail'));
		}
	}

	protected function getList() {

		$this->load->model('catalog/category');

		// Initializing
		$data = array();
		$filter_data = array();
		$general_url = '';

		// Autoloading the lanugage
		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$this->load->autoLoadLanguage('franchise/product', $data);
		} else {
			$this->load->autoLoadLanguage('catalog/product', $data);
		}

		// Looping over GET request params
		foreach ($this->request->get as $key => $value) {
			// Deal with filter_% keys
			if (stripos($key, 'filter_') === 0 || stripos($key, 'sllr_') === 0) {
				// Filter(s) to get Orders from Model
				$filter_data[$key] = $value;

				// Populating URL
				$general_url .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));

				// Populating data array
				$data[$key] = $value;
			}
		}

		if (!empty($this->request->get['filter_model'])) {
			$data['filter_model'] = $this->request->get['filter_model'] ?? null;
			$data['filter_operator'] = $this->request->get['filter_operator'] ?? null;
			$data['filter_feature_box_type'] = $this->request->get['filter_feature_box_type'] ?? null;
			$data['filter_type_string'] = $this->request->get['filter_type_string'] ?? null;
			$data['filter_val_from'] = $this->request->get['filter_val_from'] ?? 0;
			$data['filter_val_to'] = $this->request->get['filter_val_to'] ?? 0;
		}

		if (!empty($this->request->get['filter_seller_sku'])) {
			$data['filter_seller_sku'] = $this->request->get['filter_seller_sku'] ?? '';
			$data['sllr_sku_filter_operator'] = $this->request->get['sllr_sku_filter_operator'] ?? null;
			$data['sllr_sku_filter_feature_box_type'] = $this->request->get['sllr_sku_filter_feature_box_type'] ?? null;
			$data['sllr_sku_filter_type_string'] = $this->request->get['sllr_sku_filter_type_string'] ?? null;
			$data['sllr_sku_filter_val_from'] = $this->request->get['sllr_sku_filter_val_from'] ?? 0;
			$data['sllr_sku_filter_val_to'] = $this->request->get['sllr_sku_filter_val_to'] ?? 0;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$data['breadcrumbs'][] = array(
				'text' => $data['heading_title'],
				'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url . '&filter_franchise_tab=' . '1', 'SSL'),
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $data['heading_title'],
				'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url, 'SSL'),
			);
		}

		if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) {
			$data['add'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
			$data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
			$data['copy_single'] = $this->url->link('catalog/product/copyToSingle', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
			$data['convert_single'] = $this->url->link('catalog/product/convertToSingle', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
			$data['copy_sor'] = $this->url->link('catalog/product/copyToSOR', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
		}
		$data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
		$data['apply_bulk_product_csv_upload'] = $this->url->link('catalog/product/applyBulkProductCsvUpload', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
		$data['apply_bulk_product_csv_download'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url, 'SSL');

		$data['product_status_list'] = $this->model_catalog_product->getProductStatus();
		$data['store_sales_options'] = $this->db->getEnumValues(DB_PREFIX . 'product', 'store_sales');

		// to get the franchise List
		$data['franchise_lists'] = array(); //$this->model_catalog_product->getFranchiseList();

		//To get the product table exclusive column enum values
		$data['exclusive_options'] = $this->db->getEnumValues(DB_PREFIX . 'product', 'exclusive');

		//get all column name of products and get from config.php file
		$data['product_table_column_name'] = unserialize(PRODUCT_LIST_DATA);

		$data['products'] = array();

		$data['filter_page_limit'] = $this->request->get['filter_page_limit'] ?? 30;

		// Sorting is always ORDER BY product_id DESC; Getting Page number
		$page = $this->request->get['page'] ?? 'FIRST';
		$filter_data['limit'] = $data['filter_page_limit'];
		$filter_data['page'] = $page;

		$sort = $this->request->get['sort'] ?? '';
		$order = $this->request->get['order'] ?? '';

		$filter_data['sort'] = $sort;
		$filter_data['order'] = $order;

		//check page request commming from Franchise Product or simple product
		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$filter_data['request_page'] = 'franchise_product';
		} else {
			$filter_data['request_page'] = 'catalog_product';
		}

		$results_data = $this->model_catalog_product->getProducts($filter_data);

		$filter_sorting_data = '';

		$data_index = end($results_data['products']);

		if ($filter_data['sort'] == 'p.orice') {
			$filter_sorting_data = $data_index['price'];
		} else if ($filter_data['sort'] == 'p.sku') {
			$filter_sorting_data = $data_index['sku'];
		} else if ($filter_data['sort'] == 'p.quantity') {
			$filter_sorting_data = $data_index['quantity'];
		} else if ($filter_data['sort'] == 'p.status') {
			$filter_sorting_data = $data_index['status'];
		} else if ($filter_data['sort'] == 'p.commission') {
			$filter_sorting_data = $data_index['commission'];
		} else {
			$filter_sorting_data = $data_index['product_id'];
		}

		$filter_product_id = $data_index['product_id'];

		$results = $results_data['products'];

		$product_ids = array();
		$product_ids = array_column($results, 'product_id');

		$has_product_option = $this->model_catalog_product->checkAnyProductHasProductOption($product_ids);

		$archive_inventory = new InventoryArchive($this);

		$tax = new Tax($this->registry);
		foreach ($results as $key => $value) {

			//setting value of tax_class_id_from_hsn
			unset($results[$key]['tax_class_id']);
			$results[$key]['tax_class_id'] = $tax->getTaxClassIdFromHSNCode($results[$key]['hsn_code']);

			unset($results[$key]['seller_tax']);
			$results[$key]['seller_tax'] = $tax->getTaxRateForTaxIncludedPrice($results[$key]['price'], $results[$key]['hsn_code'], $results[$key]['mrp']);

			//getting seller_tax_factor and commission factor
			$seller_tax_factor = 1.0 + ((float) $results[$key]['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $value['commission'] / 100.0);
			unset($results[$key]['selling_price']);
			$results[$key]['selling_price'] = ceil(($value['price'] / $seller_tax_factor) * $commission_factor);
			$results[$key]['output_tax_rate'] = $tax->getTaxRate($results[$key]['selling_price'], $results[$key]['tax_class_id'], array(), $results[$key]['mrp']);
			$results[$key]['stock_status_info'] = Cart::getProductStockStatus($results[$key]);
		}

		$this->load->model('localisation/weight_class');
		$weight = $this->model_localisation_weight_class->getWeightClasses();
		$weight = array_combine(
			array_column($weight, 'weight_class_id'),
			array_column($weight, 'unit')
		);

		//getting tax title
		$this->load->model('localisation/tax_class');
		$tax_map = $this->model_localisation_tax_class->getTaxClasses();
		$tax_map = array_combine(
			array_column($tax_map, 'tax_class_id'),
			array_column($tax_map, 'title')
		);

		$this->load->model('tool/image');

		//get all sellerid
		$all_seller_ids = array_unique(array_column($results, 'seller_id'));
		//get sellers data[nickname,seller_invoice_generate]
		$all_seller_data = SellerInfo::getSellersData($this->db, $all_seller_ids);
		//get all products specials
		$all_products_ids = array_unique(array_column($results, 'product_id'));
		$all_products_specials = $this->model_catalog_product->getProductsSpecialsData($all_products_ids);

		foreach ($results as $result) {

			$image = $this->model_tool_image->resize($result['image'], 40, 40);
			$special = false;
			$product_special = $all_products_specials[$result['product_id']] ?? array();
			if (!empty($product_special)) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];
				}
			}

			$exclusive = '';
			if (isset($result['exclusive'])) {
				switch ($result['exclusive']) {
				case 'normal':
					$exclusive = 'N';
					break;
				case 'exclusive':
					$exclusive = 'E';
					break;
				case 'both':
					$exclusive = 'E & N';
					break;
				}
			}
			/*
				* get seller data if seller id is not balnk
			*/
			$seller_data = array();
			$nickname = '';
			$seller_invoice_generate_status = '';

			if (!empty($result['seller_id'])) {

				$seller_data = $all_seller_data[$result['seller_id']] ?? array();
			}

			if (isset($seller_data['nickname'])) {

				$nickname = $seller_data['nickname'];
			}

			if (isset($seller_data['seller_invoice_generate'])) {

				$seller_invoice_generate_status = $seller_data['seller_invoice_generate'];
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image' => $image,
				'name' => $result['name'],
				'hsn_code' => $result['hsn_code'],
				'model' => $result['model'],
				'sku' => $result['sku'],
				'price' => $result['price'],
				'commission' => $result['commission'],
				'special' => $special,
				'quantity' => $result['quantity'],
				'weight' => $result['weight'],
				'is_single' => $result['is_single'],
				'seller_tax' => $result['seller_tax'],
				'piece_in_set' => $result['piece_in_set'],
				'is_archived' => $result['is_archived'],
				'stock_status_info' => isset($result['stock_status_info']) ? $result['stock_status_info'] : '',
				'tax_class' => $result['tax_class_id'],
				'output_tax_rate' => $result['output_tax_rate'],
				'selling_price' => $result['selling_price'],
				'seller_tax' => $result['seller_tax'],
				'exclusive' => $exclusive,
				'stock_status_info' => isset($result['stock_status_info']) ? $result['stock_status_info'] : '',
				'set_description' => $result['set_description'],
				'status' => ($result['status']) ? $data['text_enabled'] : $data['text_disabled'],
				'status' => ($result['status']) ? $result['status'] : '',
				'product_status_id' => $result['status'],
				'edit' => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $general_url, 'SSL'),
				'products_order_list' => $this->url->link('catalog/product/productsOrderList', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $general_url, 'SSL'),
				'product_rating' => isset($result['product_rating']) ? $result['product_rating'] : '',
				'store_sales' => ($result['store_sales'] != 'NO') ? $result['store_sales'] : '',
				'nickname' => $nickname,
				'seller_invoice_generate_status' => $seller_invoice_generate_status,
				'seller_id' => ($result['seller_id']) ? $result['seller_id'] : 0,
				'sor_product' => $result['sor_product'],
				'sort_order' => (!empty($result['sort_order']) ? $result['sort_order'] : ''),
				'franchise_id' => (!empty($result['franchise_id'])) ? $result['franchise_id'] : 0,
				'filter_franchise_id' => $filter_franchise_id ?? '',
				'exclusive_options' => isset($result['exclusive']) ? $result['exclusive'] : 'normal',
				'cod_available' => $result['cod_available'],
				'non_returnable' => $result['non_returnable'],
				'is_associate' => $result['is_associate'],
				'has_product_option' => ((in_array($result['product_id'], $has_product_option)) ? 'true' : 'false'),
				'merge_different_design_product' => $this->url->link('catalog/product/mergeDifferentDesignProduct', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $general_url, 'SSL'),
				'hidden_selling_price' => (!empty($result['hidden_selling_price']) ? $result['hidden_selling_price'] : ''),
				'product_option_name' => $result['product_option_name'] ?? '',
			);
		}

		if (!empty($this->request->post['bulk_csv_download'])) {
			$download_results = $results;

			$file_name = DIR_DLOAD . 'bulk_product_updates.csv';
			$fp = fopen($file_name, 'w');

			$selected_csv_data = $this->request->post['bulk_csv_download'];
			// echo "<pre>"; print_r($selected_csv_data); die;
			$table_name = array();
			$table_name[] = 'oc_product';
			$table_name[] = 'oc_product';
			$table_name[] = 'oc_product';
			foreach ($selected_csv_data as $key => $value) {
				$table_key_name = $this->getProductKeyTableName($value)['table_name'];
				$table_name[] = $table_key_name;
				$table_name[] = $table_key_name;
			}
			fputcsv($fp, $table_name);

			$new_data = array();
			$new_data[] = 'product_id';
			$new_data[] = 'WSB Product Code';
			$new_data[] = 'Seller SKU';

			foreach ($selected_csv_data as $key => $value) {
				if ($value != 'product_id') {
					$new_data[] = 'Old ' . $value;
					$new_data[] = 'New ' . $value;
				} else {
					$new_data[] = $value;
				}
			}

			fputcsv($fp, $new_data);

			if (!empty($results)) {

				foreach ($download_results as $key => $sub_value) {
					$csv_record = array();
					foreach ($new_data as $data_values) {
						if ($data_values != 'product_id' && $data_values != 'WSB Product Code' && $data_values != 'Seller SKU') {

							if (strpos($data_values, 'New') === false) {
								// get product column name
								$data_values = trim(substr($data_values, 3));

								$csv_record[] = $sub_value[$this->getProductKeyTableName($data_values)['column_name']];

							} else {
								$csv_record[] = '';
							}

						} else {
							if ($data_values != 'product_id') {
								$csv_record[] = $sub_value[$this->getProductKeyTableName($data_values)['column_name']];
							} else {
								$csv_record[] = $sub_value[$data_values];
							}

						}
					}

					fputcsv($fp, $csv_record);
				}
			}

			fclose($fp);

			if (file_exists($file_name)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
				header('Expires: 0');
				header('Cache-Control: no-cache');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file_name));
				ob_clean();
				flush();
				readfile($file_name);
				exit();
			}
		}

		$data['product_edit_enable'] = '';

		if (in_array($this->user->getId(), explode(',', ADMIN_IDS)) || in_array($this->user->getId(), STORE_INVENTORY_ADMIN_IDS)) {

			$data['product_edit_enable'] = 1;
		}

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
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		// URL For sorting
		$sort_url = $general_url;

		if ($order == 'ASC') {
			$sort_url .= '&order=DESC';
		} else {
			$sort_url .= '&order=ASC';
		}

		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$data['sort_model'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
			$data['sort_seller_sku'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sku' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
			$data['sort_price'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
			$data['sort_commission'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.commission' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
			$data['sort_quantity'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
			$data['sort_status'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
			$data['sort_order'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $sort_url . '&filter_franchise_tab=' . '1', 'SSL');
		} else {
			$data['sort_model'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $sort_url, 'SSL');
			$data['sort_seller_sku'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sku' . $sort_url, 'SSL');
			$data['sort_price'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $sort_url, 'SSL');
			$data['sort_commission'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.commission' . $sort_url, 'SSL');
			$data['sort_quantity'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $sort_url, 'SSL');
			$data['sort_status'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $sort_url, 'SSL');
			$data['sort_order'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $sort_url, 'SSL');
		}

		// URL for pagination
		$pagination_url = $general_url;

		if (isset($this->request->get['sort'])) {
			$pagination_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$pagination_url .= '&order=' . $this->request->get['order'];
		}
		if (!empty($filter_sorting_data)) {
			$pagination_url .= '&filter_sorting_data=' . $filter_sorting_data . '&filter_product_id=' . $filter_product_id;
		}

		$pagination = new PaginationV2();
		$pagination->page = $page;
		//$pagination->next = end($data['products'])['product_id'];
		$pagination->next = $data_index['product_id'];
		$pagination->total = count($data['products']);
		$pagination->limit = $data['filter_page_limit'];
		$pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');
		$data['pagination'] = $pagination->render();

		$data['page_limit_array'] = array('30', '60', '100', '200', '500', '1000');

		$data['sort'] = $sort;
		$data['order'] = $order;

		// get seller list
		$data['seller_list'] = $this->model_catalog_product->getSellerList();

		$data['filter_seller_list'] = $this->request->get['filter_seller_list'] ?? null;

		// get product status
		$data['product_status_list'] = $this->model_catalog_product->getProductStatus();

		//category tree for filters
		$all_categories = $this->model_catalog_category->getCategoriesNameForTree();
		$data['categories'] = $this->model_catalog_category->buildCategoryTree($all_categories);

		//archive option in inventory
		$data['archive_inventory'] = array(
			'1' => 'set archive',
			'0' => 'unset archive',
		);

		$data['moderate_approve'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $pagination_url, 'SSL');
		$data['moderate_reject'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $pagination_url, 'SSL');

		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$filter_data['request_page'] = 'franchise_product';
		} else {
			$filter_data['request_page'] = 'catalog_product';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('catalog/product_list.tpl', $data));

	}

	protected function getForm() {

		$data = array(); // Initializing the data array to be passed on to template files

		// Autoloading the lanugage
		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$this->load->autoLoadLanguage('franchise/product', $data);
		} else {
			$this->load->autoLoadLanguage('catalog/product', $data);
		}

		$data['text_form'] = !isset($this->request->get['product_id']) ? $data['text_add'] : $data['text_edit'];

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

		if (isset($this->error['seller_tax'])) {
			$data['error_seller_tax'] = $this->error['seller_tax'];
		} else {
			$data['error_seller_tax'] = array();
		}

		if (isset($this->error['tax_class_id'])) {
			$data['error_seller_tax_class_id'] = $this->error['tax_class_id'];
		} else {
			$data['error_seller_tax_class_id'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['meta_description'])) {
			$data['error_meta_description'] = $this->error['meta_description'];
		} else {
			$data['error_meta_description'] = array();
		}

		if (isset($this->error['meta_keyword'])) {
			$data['error_meta_keyword'] = $this->error['meta_keyword'];
		} else {
			$data['error_meta_keyword'] = array();
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['minimum'])) {
			$data['error_minimum'] = $this->error['minimum'];
		} else {
			$data['error_minimum'] = '';
		}

		if (isset($this->error['hsn_code'])) {
			$data['error_hsn_code'] = $this->error['hsn_code'];
		} else {
			$data['error_hsn_code'] = '';
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

		if (isset($this->request->get['filter_seller_sku'])) {
			$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_commission'])) {
			$url .= '&filter_commission=' . $this->request->get['filter_commission'];
		}

		if (isset($this->request->get['filter_non_single'])) {
			$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		}
		if (isset($this->request->get['filter_non_sor'])) {
			$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['filter_seller_list'])) {
			$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
		}

		if (isset($this->request->get['filter_solr_enabled'])) {
			$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
		}

		if (isset($this->request->get['filter_page_limit'])) {
			$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'),
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
				$data['action'] = $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url . '&filter_franchise_tab=' . '1', 'SSL');
			} else {
				$data['action'] = $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
			}
		}

		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&filter_franchise_tab=' . '1', 'SSL');
		} else {
			$data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$data['token'] = $this->session->data['token'];

		$data['user_id'] = $this->user->getId();

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['product_id'])) {
			$data['p_id'] = $this->request->get['product_id'];
			$data['associate_products'] = $this->getAssociateProducts($this->request->get['product_id']);
		} else {
			$data['associate_products'] = $this->getAssociateProducts(0);
		}

		$data['store_data'] = $this->model_catalog_product->get_store_data();

		if (isset($this->request->post['changes_data'])) {
			$data['changes_data'] = $this->request->post['changes_data'];
		} else {
			$data['changes_data'] = '';
		}

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}

		if (isset($this->request->post['only_for_search'])) {
			$data['only_for_search'] = $this->request->post['only_for_search'];
		} elseif (!empty($product_info)) {
			$data['only_for_search'] = $product_info['only_for_search'];
		} else {
			$data['only_for_search'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) /*&& is_file(DIR_IMAGE . $this->request->post['image'])*/) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($product_info) /*&& is_file(DIR_IMAGE . $product_info['image'])*/) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// it's using get image path and send by ajax click on perticular image when product edited
		$data['directory'] = "";
		$img_dir = (isset($product_info['image'])) ? dirname(dirname($product_info['image'])) : '';
		if (!empty($img_dir) && $img_dir != ".") {
			$data['directory'] = $img_dir;
		}

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

		if (isset($this->request->post['hsn_code'])) {
			$data['hsn_code'] = $this->request->post['hsn_code'];
		} elseif (!empty($product_info)) {
			$data['hsn_code'] = $product_info['hsn_code'];
		} else {
			$data['hsn_code'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}

		if (isset($this->request->post['product_rating'])) {
			$data['product_rating'] = $this->request->post['product_rating'];
		} elseif (!empty($product_info)) {
			$data['product_rating'] = $product_info['rating'];
		} else {
			$data['product_rating'] = NULL;
		}

		$this->load->model('catalog/category');

		/*
			 * getting all the unit ids and unit names
		*/
		$data['units_array'] = array();

		$data['units_array'] = $this->model_catalog_category->getUnitIdsAndNames();
		//getting unit ids for product form
		if (isset($this->request->post['unit_id'])) {
			$data['unit_id'] = $this->request->post['unit_id'];
		} elseif (!empty($product_info)) {
			$data['unit_id'] = $product_info['unit_id'];
		} else {
			$data['unit_id'] = 0;
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
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

		if (isset($this->request->post['mrp'])) {
			$data['mrp'] = $this->request->post['mrp'];
		} elseif (!empty($product_info)) {
			$data['mrp'] = $product_info['mrp'];
		} else {
			$data['mrp'] = '';
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
			$data['commission'] = 0.0;
		}

		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_catalog_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['expected_dispatch_date'])) {
			$data['expected_dispatch_date'] = $this->request->post['expected_dispatch_date'];
		} elseif (!empty($product_info['expected_dispatch_date'])) {
			$data['expected_dispatch_date'] = ($product_info['expected_dispatch_date'] != '0000-00-00') ? $product_info['expected_dispatch_date'] : '';
		} else {
			$data['expected_dispatch_date'] = '';
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['cod_available'])) {
			$data['cod_available'] = $this->request->post['cod_available'];
		} elseif (!empty($product_info)) {
			$data['cod_available'] = $product_info['cod_available'];
		} else {
			$data['cod_available'] = "1";
		}

		$data['sor_product_terms'] = 0;
		$data['sor_type'] = '';
		$data['sor_days'] = '';
		if (isset($this->request->post['sor_product_terms'])) {
			$data['sor_product_terms'] = $this->request->post['sor_product_terms'];
			$data['sor_type'] = $this->request->post['sor_type'];
			$data['sor_days'] = $this->request->post['limit_days'];
		} elseif (!empty($product_info)) {
			$sor_product = $this->model_catalog_product->getSorProduct($product_info['product_id']);
			if ($sor_product) {
				$data['sor_product_terms'] = 1;
				$data['sor_type'] = $sor_product['sor_type'];
				$data['sor_days'] = $sor_product['sor_days'];
			}
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
		if (isset($this->request->post['is_single'])) {
			$data['is_single'] = $this->request->post['is_single'];
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

		if (isset($this->request->post['non_returnable'])) {
			$data['non_returnable'] = $this->request->post['non_returnable'];
		} elseif (!empty($product_info)) {
			$data['non_returnable'] = $product_info['non_returnable'];
		} else {
			$data['non_returnable'] = 0;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
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

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
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
		}

		$data['show_option_tab'] = 1;

		if (isset($this->request->post['is_associate'])) {
			$data['is_associate'] = $this->request->post['is_associate'];
		} elseif (!empty($product_info)) {
			$data['is_associate'] = $product_info['is_associate'];
		} else {
			$data['is_associate'] = 0;
		}

		// disabled options for associate product
		if ($data['is_associate']) {
			$data['show_option_tab'] = 0;
		}

		if (isset($this->request->post['associate_product_ids'])) {
			$data['associate_product_ids'] = $this->request->post['associate_product_ids'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['associate_product_ids'] = $this->model_catalog_product->getAssociateProductIds($this->request->get['product_id']);
		} else {
			$data['associate_product_ids'] = array();
		}

		// disabled option for combo products
		if (!empty($data['associate_product_ids'])) {
			$data['show_option_tab'] = 0;
		}

		$data['show_associate_checkbox'] = 1;
		if (!empty($this->request->get['filter_franchise_tab'])) {
			$data['show_associate_checkbox'] = 0;
		}

		$data['show_associate_tab'] = 1;
		if (!empty($product_info['is_associate']) || (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) {
			$data['show_associate_tab'] = 0;
		}

		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
				);
			}
		}

		if (isset($this->request->get['product_id'])) {
			$data['category_str'] = $this->model_catalog_product->get_product_store_data($this->request->get['product_id']);
		}

		if (!empty($data['category_str'])) {
			$data['store_id'] = $data['category_str'][0]['store_id'];
			$data['language'] = $data['category_str'][0]['language'];
			$data['meta_title'] = $data['category_str'][0]['meta_title'];
			$data['meta_keywords'] = $data['category_str'][0]['meta_keywords'];
			$data['meta_description'] = $data['category_str'][0]['meta_description'];
		}

		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->post['product_filter'])) {
			$filters = $this->request->post['product_filter'];
		} elseif (isset($this->request->get['product_id'])) {
			$filters = $this->model_catalog_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name'],
				);
			}
		}

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name' => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description'],
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					//if (is_file(DIR_IMAGE . $product_option_value['option_image'])) {
					$image = $product_option_value['option_image'];
					$thumb = $product_option_value['option_image'];
					$directory = dirname($product_option_value['option_image']);
					/*} else {
						$image = '';
						$thumb = 'no_image.png';
						$directory = '';
					}*/
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id' => $product_option_value['option_value_id'],
						'quantity' => $product_option_value['quantity'],
						'subtract' => $product_option_value['subtract'],
						'price' => $product_option_value['price'],
						'image_thumb' => $this->model_tool_image->resize($thumb, 100, 100),
						'option_image' => $image,
						'price_prefix' => $product_option_value['price_prefix'],
						'points' => $product_option_value['points'],
						'points_prefix' => $product_option_value['points_prefix'],
						'weight' => $product_option_value['weight'],
						'weight_prefix' => $product_option_value['weight_prefix'],
						'option_code' => $product_option_value['option_code'],
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id' => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id' => $product_option['option_id'],
				'name' => $product_option['name'],
				'type' => $product_option['type'],
				'value' => isset($product_option['value']) ? $product_option['value'] : '',
				'required' => $product_option['required'],
			);
		}
		// echo("<pre>");print_r($data);die;

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}

		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id'], 0);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'quantity' => $product_discount['quantity'],
				'priority' => $product_discount['priority'],
				'price' => $product_discount['price'],
				'store_id' => $product_discount['store_id'],
				'date_start' => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end' => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : '',
			);
		}

		if (isset($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_catalog_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'priority' => $product_special['priority'],
				'price' => $product_special['price'],
				'date_start' => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end' => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] : '',
			);
		}

		if (isset($this->request->post['store_sales'])) {
			$data['store_sales'] = $this->request->post['store_sales'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['store_sales'] = $product_info['store_sales'];
		} else {
			$data['store_sales'] = '';
		}

		$data['exclusive_data'] = array('normal', 'exclusive', 'both');
		if (isset($this->request->post['exclusive'])) {
			$data['exclusive'] = $this->request->post['exclusive'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['exclusive'] = $product_info['exclusive'];
		} else {
			$data['exclusive'] = 'normal';
		}

		// Get Store Sales Options

		$data['store_sales_options'] = $this->db->getEnumValues(DB_PREFIX . 'product', 'store_sales');

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			$width = "";
			$height = "";
			//if (is_file(DIR_IMAGE . $product_image['image'])) {
			$image = $product_image['image'];
			$thumb = $product_image['image'];
			$directory = dirname($product_image['image']);
			$dimesions = !empty($product_image['image_dimensions']) ? unserialize($product_image['image_dimensions']) : '';
			if (!empty($dimesions['width'])) {
				$width = $dimesions['width'];
			}

			if (!empty($dimesions['height'])) {
				$height = $dimesions['height'];
			}

			/*} else {
				$image = '';
				$thumb = 'no_image.png';
				$directory = '';
			}*/

			$data['product_images'][] = array(
				'image' => $image,
				'thumb' => $this->model_tool_image->resize($thumb, $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
				'sort_order' => $product_image['sort_order'],
				'directory' => $directory,
				'width' => $width,
				'height' => $height,
			);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_catalog_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name' => $download_info['name'],
				);
			}
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);
			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name' => $related_info['name'],
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_catalog_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		if (isset($this->request->get['product_id'])) {

			//sending bool tax to tpl for client side validation
			$sql = "SELECT seller_id from oc_ms_product WHERE product_id = " . (int) ($this->request->get['product_id']) . "";
			$query = $this->db->query($sql);

			if ($query->num_rows) {
				$seller_id = $query->row['seller_id'];
				$data['bool_tax'] = SellerInfo::checkIfSellerCanListTaxableProducts($this->db, $seller_id);
			}
		}

		$data['sor_data'] = array('No', 'Yes');

		if (isset($this->request->post['sor_product'])) {

			$data['sor_product'] = $this->request->post['sor_product'];

		} elseif (!empty($product_info)) {

			$data['sor_product'] = $product_info['sor_product'];

		} else {

			$data['sor_product'] = 0;
		}
		if (isset($this->request->get['product_id']) && $this->request->get['product_id'] != '') {
			$data['show_sor_dropdown'] = 1;
		} else {
			$data['show_sor_dropdown'] = 0;
		}
		//
		$data['sor_enable'] = 0;
		/*
			* check product seller has seller_invoice_generate status 0
		*/
		$seller_status = 0;

		if (!empty($this->request->get['product_id'])) {

			$seller_status = $this->model_catalog_product->getSellerInvoiceGenerateStatusOfProduct($this->request->get['product_id']);
		}

		if (!empty($this->request->get['product_id'])) {

			$seller_status = $this->model_catalog_product->getSellerInvoiceGenerateStatusOfProduct($this->request->get['product_id']);
		}

		if (($data['sor_product'] == 0 && $seller_status == 0) || in_array($this->user->getId(), STORE_INVENTORY_ADMIN_IDS)) {
			$data['sor_enable'] = 1;
		}

		if (($data['sor_product'] == 1 || $seller_status) && (in_array($this->user->getId(), explode(',', ADMIN_IDS)) || in_array($this->user->getId(), STORE_INVENTORY_ADMIN_IDS))) {
			$data['sor_enable'] = 1;
		}

		// get product status
		$data['product_status_list'] = $this->model_catalog_product->getProductStatus();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/product_form.tpl', $data));
	}

	protected function validateForm() {

		$data = array(); // Initializing the data array to be passed on to template files
		// Autoloading the lanugage
		$this->load->autoLoadLanguage('catalog/product', $data);

		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $data['error_permission'];
		}

		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if ($language_id == 1) {
				if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 510)) {
					$this->error['name'][$language_id] = $data['error_name'];
				}

				if ((utf8_strlen($value['meta_title']) <= 0) || (utf8_strlen($value['meta_title']) > 255)) {
					$this->error['meta_title'][$language_id] = $data['error_meta_title'];
				}

				if ((utf8_strlen($value['meta_description']) <= 0) || (utf8_strlen($value['meta_description']) > 255)) {
					$this->error['meta_description'][$language_id] = $data['error_meta_description'];
				}

				if ((utf8_strlen($value['meta_keyword']) <= 0) || (utf8_strlen($value['meta_keyword']) > 255)) {
					$this->error['meta_keyword'][$language_id] = $data['error_meta_keyword'];
				}
			}
		}

		//validation for tax class and seller tax

		if (isset($this->request->post['seller_tax']) || isset($this->request->post['tax_class_id'])) {
			if ($this->request->post['seller_tax'] > 0 || $this->request->post['tax_class_id'] > 0) {

				$seller_id = $this->db->query("SELECT seller_id from oc_ms_product WHERE product_id = " . (int) ($this->request->get['product_id']) . "")->row['seller_id'];

				$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($this->db, $seller_id);

				if (($bool_tax == 1) && ($this->request->post['seller_tax'] > 0)) {
					$this->error['seller_tax'] = '"Tax Rates for seller tax are Not Correct. Seller cannot list taxable products"';

				}
				if (($bool_tax == 1) && ($this->request->post['tax_class_id'] > 0)) {
					$this->error['tax_class_id'] = '"Tax Rates for tax class are Not Correct. Seller cannot list taxable products"';
				}
			}
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $data['error_model'];
		}

		if (isset($this->request->post['keyword']) && utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['product_id']) && $url_alias_info['query'] != 'product_id=' . $this->request->get['product_id']) {
				$this->error['keyword'] = sprintf($data['error_keyword']);
			}

			if ($url_alias_info && !isset($this->request->get['product_id'])) {
				$this->error['keyword'] = sprintf($data['error_keyword']);
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $data['error_warning'];
		}

		if (isset($this->request->post['product_image']) && !empty($this->request->post['product_image'])) {
			foreach ($this->request->post['product_image'] as $product_image) {
				if ($product_image['sort_order'] == '') {
					$this->error['warning'] = $data['error_product_image_sort_order'];
				}
			}
		}

		if (isset($this->request->post['minimum']) && (int) $this->request->post['minimum'] < 1) {
			$this->error['minimum'] = $data['error_minimum'];
			$this->error['warning'] = $data['error_minimum'];
		}

		if (!(strlen($this->request->post['hsn_code']) >= 4 && strlen($this->request->post['hsn_code']) <= 8 && preg_match("/[0-9]{4,}/", $this->request->post['hsn_code']))) {
			$this->error['hsn_code'] = $data['error_hsn_code'];
		}

//                if (isset($this->error['unit_id'])) {
		//			$data['error_unit_id'] = $this->error['unit_id'];
		//		} else {
		//			$data['error_unit_id'] = '';
		//		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	protected function validateCopyToSingle() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function store_data() {
		$json = array();
		if ($this->request->get['store_id']) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = '';
		}
		if ($this->request->get['product_id']) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = '';
		}
		$this->load->model('catalog/product');

		$results = $this->model_catalog_product->get_ajax_product_store_data($store_id, $product_id);

		foreach ($results as $result) {
			$json = array(
				'product_id' => $result['product_id'],
				'store_id' => strip_tags(html_entity_decode($result['store_id'], ENT_QUOTES, 'UTF-8')),
				'language' => $result['language'],
				'meta_title' => strip_tags(html_entity_decode($result['meta_title'], ENT_QUOTES, 'UTF-8')),
				'meta_keywords' => strip_tags(html_entity_decode($result['meta_keywords'], ENT_QUOTES, 'UTF-8')),
				'meta_description' => strip_tags(html_entity_decode($result['meta_description'], ENT_QUOTES, 'UTF-8')),
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	/**
	 *
	 */
	public function copyToSOR() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopyToSingle()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->copyProductToSOR($product_id);
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

			if (isset($this->request->get['filter_seller_sku'])) {
				$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_non_single'])) {
				$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
			}

			if (isset($this->request->get['filter_non_sor'])) {
				$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
			}

			if (isset($this->request->get['filter_solr_enabled'])) {
				$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
			}

			if (isset($this->request->get['filter_seller_list'])) {
				$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
			}

			if (isset($this->request->get['filter_page_limit'])) {
				$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
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
	/**
	 * Manage product to approve
	 *
	 **/
	public function product_to_approve() {
		$this->load->model('catalog/product');
		$colMap = array(
			'seller' => '`c.name`',
			'company' => 'ms.company',
			'email' => 'c.email',
			'balance' => '`current_balance`',
			'date_created' => '`ms.date_created`',
			'status' => '`ms.seller_status`',
			'telephone' => 'c.telephone',
			'last_login' => 'last_login',
		);

		$sorts = array('seller', 'company', 'email', 'total_sales', 'total_products', 'total_earnings', 'date_created', 'balance', 'status', 'date_created', 'telephone', 'last_login');
		$filters = array_diff($sorts, array('status', 'last_login'));

		//var_dump($this->request->get);

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$sellers = $this->MsLoader->MsSeller->getSellers(
			array(),
			array(
				'order_by' => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				//'offset' => $this->request->get['iDisplayStart'],
				//'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'total_products' => 1,
				'total_earnings' => 1,
				'current_balance' => 1,
			)
		);
		$data['sellers'] = $sellers;
		//echo "<pre>"; print_r($sellers); exit;

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['seller'])) {
			$seller = $this->request->get['seller'];
		} else {
			$seller = '';
		}
		$limit = $this->config->get('config_limit_admin');
		$url = '';
		$filter_data = array(
			'seller' => $seller,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		);

		$product_to_approve_list_total = $this->model_catalog_product->product_to_approve_list_total($filter_data);

		$product_to_approve_list = $this->model_catalog_product->product_to_approve_list($filter_data);

		$data['product_to_approve_list'] = $product_to_approve_list;

		$pagination = new Pagination();
		$pagination->total = $product_to_approve_list_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('catalog/product/product_to_approve', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['edit_link'] = $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'], '', 'SSL');
		$data['approve_link'] = $this->url->link('catalog/product/update_product_approve&token=' . $this->session->data['token'], '', 'SSL');
		$data['cancel_link'] = $this->url->link('catalog/product/update_product_cancel&token=' . $this->session->data['token'], '', 'SSL');

		$this->response->setOutput($this->load->view('catalog/product_to_approve_list.tpl', $data));
	}
	/**
	 * Update approved status
	 **/
	public function update_product_approve() {

		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		}
		$date_approved = Date("Y-m-d H:i:s");
		$this->db->query("UPDATE " . DB_PREFIX . "product_to_approve SET approved = '1', date_approved = '" . $date_approved . "' WHERE product_id = '" . $product_id . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '1'WHERE product_id = '" . $product_id . "'");

		$this->response->redirect($this->url->link('catalog/product/product_to_approve', 'token=' . $this->session->data['token'], 'SSL'));
	}

	/**
	 * Update comment on cancel
	 **/
	public function update_product_cancel() {
		//echo "<pre>"; print_r($this->request->post); exit;
		$comment = $this->request->post['comment'];
		$product_id = $this->request->post['product_id'];
		$this->db->query("UPDATE " . DB_PREFIX . "product_to_approve SET approved = '0', comment = '" . $comment . "' WHERE product_id = '" . $product_id . "'");

		$this->response->redirect($this->url->link('catalog/product/product_to_approve', 'token=' . $this->session->data['token'], 'SSL'));
	}

	// updateProductList with transfer price , commission, and quantity by vikas (29-06-2016)
	public function updateProductList() {
		$this->load->language('catalog/product');
		$this->load->model('catalog/product');
		$data = array();
		$this->load->autoLoadLanguage('catalog/product', $data);
		$json = array();
		if (isset($this->request->get['product_id']) && isset($this->request->get['field_type']) && isset($this->request->get['field_value'])) {
			$product_id = (int) ($this->request->get['product_id']);
			$field_type = $this->request->get['field_type'];
			$field_value = $this->request->get['field_value'];

			if ($field_type == 'quantity') {
				$query = $this->model_catalog_product->checkAnyProductHasProductOption($product_id);

				if (in_array($product_id, $query)) {
					$json['error'] = 1;
					$json['message'] = $data['error_has_product_option'];
					echo json_encode($json);
					exit;
				}
			}

			$old_value = $this->request->get['old_value'];
			$new_value = $this->request->get['new_value'];

			if ($field_type != 'quantity' && $field_type != 'weight') {
				if (strpos($field_value, ".") !== false) {
					$price_value = $this->request->get['field_value'] . '00';
				} else {
					$price_value = $this->request->get['field_value'] . '.0000';
				}
			} else {
				$price_value = $this->request->get['field_value'];
			}

			$changes_data = array(
				$field_type => array(
					'old_value' => $old_value,
					'new_value' => $new_value,
				),
			);
			$this->model_catalog_product->updateProductList($product_id, $field_type, $field_value, $changes_data);

			$json['error'] = '';
			$json['price_value'] = $price_value;
			echo json_encode($json);
			exit;
		}
	}

	// update product to moderate approve by vikas(30-06-2016)
	public function productToModerateApprove() {
		$this->load->model('inventory/producttomoderate');
		$this->load->language('inventory/product_to_moderate');
		$json = array();

		if (isset($this->request->post['selected']) && $this->moderateValidatePermission()) {
			$product_id = $this->request->post['selected'];
			$this->model_inventory_producttomoderate->updateProductModerateApprove($product_id);
			$json['success'] = $this->language->get('text_success');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// update product to moderate reject by vikas(30-06-2016)
	public function productToModerateReject() {
		$this->load->model('inventory/producttomoderate');
		$this->load->language('inventory/product_to_moderate');
		$json = array();

		if (isset($this->request->post['selected']) && $this->moderateValidatePermission()) {
			$product_id = $this->request->post['selected'];
			$this->model_inventory_producttomoderate->updateProductModerateReject($product_id);
			$json['success'] = $this->language->get('text_success');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function moderateValidatePermission() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	public function productsOrderList() {
		$this->load->language('catalog/product');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$show_permission = false;

		if ($this->user->hasPermission('modify', 'sale/edit_order')) {
			$show_permission = true;
		}

		$data = array(); // Initializing the data array to be passed on to template files
		// Autoloading the lanugage
		$this->load->autoLoadLanguage('catalog/product', $data);

		$this->document->setTitle($data['products_order_heading']);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['product_id'])) {
			$url .= '&product_id=' . $this->request->get['product_id'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['products_order_heading'],
			'href' => $this->url->link('catalog/product/productsOrderList', 'token=' . $this->session->data['token'] . $url, 'SSL'),
		);

		$data['edit_button'] = $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$content = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		$data['content'] = $content;
		$data['product_id'] = $content['product_id'];
		$data['product_model'] = $content['model'];
		$data['product_title'] = $content['name'];
		//pr($content); die;
		$this->load->model('report/product');
		// product_id is available or not available in wsb_product_breakup table
		$wsb_pro_id_available = SellerInfo::checkProductIsWsb($this->db, (int) $this->request->get['product_id']);

		if ($wsb_pro_id_available) {
			$data['html'] = $this->_productIdAvailableInWsbProductBreakup($this->request->get['product_id'], $data);
		} else {
			$data['html'] = $this->_productIdNotAvailableInWsbProductBreakup($this->request->get['product_id'], $data);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/products_order_list.tpl', $data));
	}

	/**
	 * Method for get tpl file of Product order list which product id available in wsb product breakup
	 * @param : $product_id : Integer of product id
	 * @param : $data : reference of data
	 * @return : data with tpl file
	 * @author : vikas, July 2018
	 */
	private function _productIdAvailableInWsbProductBreakup($product_id, &$data) {
		$content = $data['content'];
		$this->load->model('report/product');
		$records = $this->model_report_product->getProductOrderListPurchaseInventory($this->request->get['product_id']);

		// get pickup_city_code corresponding to purchase_firm_tin_no(GST No.)
		$gst_no_with_pickup_code_map = $this->model_report_product->getPickupCityCodeCorrespondingToGSTNo();

		// get current seller nick name
		$gst_number = $this->getAlphaNumericString(end($records)['wsb_gst']);

		if ($gst_number == '08195900085') {

			$gst_number = '08AABCW7022Q1ZU';
		}

		$get_current_seller_data = $this->model_report_product->getCurrentSellerNickNameByPorductId((int) $product_id);

		$final_product_record = array();

		foreach ($records as $record) {
			$final_product_record[$record['wsb_firm']][] = $record;
		}

		$data['products_order'] = array();
		$data['record_summary'] = array();
		$data['common_record'] = array();

		$data['current_seller'] = array();
		// ex: STR_DL
		$data['current_seller']['nickname'] = $get_current_seller_data['nickname'] ?? '';
		$data['current_seller']['pickup_city_code'] = $get_current_seller_data['pickup_city_code'] ?? '';
		// ex: DL in from STR_DL
		$current_seller_code = $get_current_seller_data['pickup_city_code'] ?? '';
		$data['current_seller']['code'] = $current_seller_code;

		$pickup_city_codes = array('JP' => 0, 'DL' => 0, 'ST' => 0, 'KL' => 0, 'BL' => 0, 'MU' => 0);

		$data['intermediates'] = array();
		$data['store_stock_status'] = array();

		foreach ($final_product_record as $wsb_firm_key => $records) {

			$running_balance = 0;
			foreach ($records as $record) {

				$running_balance += (int) $record['qty_in'];

				if ($record['order_status'] != ORDER_STATUS['Shipped'] && $record['order_status'] != ORDER_STATUS['Shipped with tracking'] && $record['order_status'] != ORDER_STATUS['Out for delivery']) {

					$running_balance -= (int) $record['qty_out'];
				}

				$available_qty = $running_balance;

				$intermediates = 0;

				if ($running_balance < 0) {
					$intermediates = 1;
				}

				if (!empty($gst_no_with_pickup_code_map[$record['wsb_gst']])) {
					$new_pickup_city_code = $gst_no_with_pickup_code_map[$record['wsb_gst']];
				} else {
					$new_pickup_city_code = $gst_no_with_pickup_code_map[$gst_number];
				}
				$data['products_order'][$new_pickup_city_code][] = array(
					'transaction_type' => $record['transaction_type'],
					'seller_sku' => $record['seller_sku'],
					'invoice_date' => (!empty($record['invoice_date'])) ? date('d-m-Y', strtotime($record['invoice_date'])) : '',
					'qty_in' => $record['qty_in'],
					'qty_out' => $record['qty_out'],
					'running_balance' => $running_balance,
					'reference' => $this->__setReference((array) $record),
					'reference_status' => isset($record['reference_status']) ? $record['reference_status'] : '',
					'stock_movement' => isset($record['stock_movement']) ? $record['stock_movement'] : '',
					'stock_movement_detail' => $record['stock_movement_detail'],
					'wsb_firm' => isset($record['wsb_firm']) ? $record['wsb_firm'] : '',
					'wsb_gst' => isset($record['wsb_gst']) ? $record['wsb_gst'] : '',
					'seller_firm' => isset($record['seller_firm']) ? $record['seller_firm'] : '',
					'seller_tax' => isset($record['seller_tax']) ? $record['seller_tax'] : '',
					'transfer_price_per_piece' => isset($record['transfer_price_per_piece']) ? $record['transfer_price_per_piece'] : '',
					'option_name' => isset($record['option_name']) ? $record['option_name'] : '',
					'option_value' => isset($record['option_value']) ? $record['option_value'] : '',
					'reference_document_date' => $record['reference_document_date'],
				);

				if (array_key_exists($new_pickup_city_code, $pickup_city_codes)) {

					$pickup_city_codes[$new_pickup_city_code] += $intermediates;
				}
			}
			/*
				* remove blank key value from intermediate array
			*/
			foreach ($pickup_city_codes as $key => $value) {
				if (empty($pickup_city_codes[$key])) {
					unset($pickup_city_codes[$key]);
				}
			}
			$data['intermediates'] = $pickup_city_codes;

			$system_stock = 0;
			$red_color = '';
			if ($system_stock != $running_balance) {
				$red_color = 'bg-danger';
			}

			$data['record_summary'][$new_pickup_city_code]['system_stock'] = $system_stock;
			$data['record_summary'][$new_pickup_city_code]['running_balance'] = $running_balance;
			$data['record_summary'][$new_pickup_city_code]['diff_stock'] = $red_color;

			if ($current_seller_code == $new_pickup_city_code) {

				$system_stock = ($content['quantity'] * $content['piece_in_set']);

				$data['record_summary'][$new_pickup_city_code]['system_stock'] = $system_stock;

				$red_color = 'bg-info';

				if ($system_stock != $running_balance) {

					$red_color = 'bg-danger';
				}

				$data['record_summary'][$new_pickup_city_code]['diff_stock'] = $red_color;

			} else {

				$system_stock = ($content['quantity'] * $content['piece_in_set']);
				$data['record_summary'][$current_seller_code]['system_stock'] = $system_stock;
			}

			$data['common_record'][$new_pickup_city_code]['wsb_firm'] = isset($record['wsb_firm']) ? $record['wsb_firm'] : '';
			$data['common_record'][$new_pickup_city_code]['wsb_gst'] = isset($record['wsb_gst']) ? $record['wsb_gst'] : '';
		}
		if (!empty($data['products_order']) && !array_key_exists($data['current_seller']['pickup_city_code'], $data['products_order'])) {

			$data['products_order'][$data['current_seller']['pickup_city_code']] = array();
			$data['common_record'][$data['current_seller']['pickup_city_code']]['wsb_firm'] = $get_current_seller_data['nickname'];
			$data['common_record'][$data['current_seller']['pickup_city_code']]['wsb_gst'] = $get_current_seller_data['gst_provisional_id'];
			$data['record_summary'][$data['current_seller']['pickup_city_code']]['system_stock'] = $system_stock;
			$data['record_summary'][$data['current_seller']['pickup_city_code']]['running_balance'] = 0;
			$data['record_summary'][$data['current_seller']['pickup_city_code']]['diff_stock'] = $red_color;
		}

		return $this->load->view('catalog/product_id_available_in_wsb_product_breakup.tpl', $data);
	}
	/**
	 * Method for get tpl file of Product order list which product id not available in wsb product breakup
	 * @param : $product_id : Integer of product id
	 * @param : $data : reference of data
	 * @return : data with tpl file
	 * @author : vikas, July 2018
	 */
	private function _productIdNotAvailableInWsbProductBreakup($product_id, &$data) {
		$this->load->language('catalog/product');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$content = $data['content'];
		//pr($content); die;
		$records = $this->model_catalog_product->productsOrderList($this->request->get['product_id']);

		$data['product_model'] = $content['model'];
		$data['product_title'] = $content['name'];

		$total_quantity = 0;
		$total_pieces = 0;
		$running_balance = 0;
		$received_qty = 0;
		$sales_qty = 0;
		$return_qty = 0;
		$available_qty = 0;
		$purchase_return = 0;
		$stock_transfer = 0;

		$data['products_order'] = array();

		foreach ($records as $record) {

			if (in_array($record['transaction_type'], array('WSB Purchase', 'Sales Return'))) {

				if ($record['transaction_type'] == 'WSB Purchase') {
					$received_qty += $record['qty_in'];
				}
				if ($record['transaction_type'] == 'Sales Return') {
					$return_qty += $record['qty_in'];
				}
			} else if ($record['transaction_type'] === 'Sales') {

				$sales_qty += $record['qty_out'];

			} else if ($record['transaction_type'] === 'WSB Purchase Return') {

				$purchase_return += $record['qty_out'];

			} else if ($record['transaction_type'] === 'Stock Transfer' || $record['transaction_type'] === 'Stock Transfer Return') {

				$stock_transfer += $record['stock_movement'];
			}

			$running_balance += $record['qty_in'];
			$running_balance -= $record['qty_out'];

			$available_qty = $running_balance;

			$data['products_order'][] = array(

				'transaction_type' => $record['transaction_type'],
				'seller_sku' => $record['seller_sku'],
				'invoice_date' => (!empty($record['invoice_date'])) ? date('d-m-Y', strtotime($record['invoice_date'])) : '',
				'qty_in' => $record['qty_in'],
				'qty_out' => $record['qty_out'],
				'running_balance' => $running_balance,
				'reference' => $record['reference'],
				'reference_status' => $record['reference_status'],
				'stock_movement' => $record['stock_movement'],
				'stock_movement_detail' => $record['stock_movement_detail'],
			);

		}

		$data['received_qty'] = $received_qty;
		$data['sales_qty'] = $sales_qty;
		$data['return_qty'] = $return_qty;
		$data['available_qty'] = $available_qty;
		$data['purchase_return'] = $purchase_return;
		$data['stock_transfer'] = $stock_transfer;

		$data['system_stock'] = ($content['quantity'] * $content['piece_in_set']);

		$data['total_quantity'] = $total_quantity;
		$data['total_pieces'] = $total_pieces;

		return $this->load->view('catalog/product_id_not_available_in_wsb_product_breakup.tpl', $data);
	}

	// update oc_ms_product and products assigned to seller
	public function ProductAssignToSeller() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {
			$seller_id = $this->request->post['seller_id'];
			$product_ids = $this->request->post['product_ids'];
			$check_product_exclusive = 0;
			if (!empty($this->request->post['check_product_exclusive'])) {
				$check_product_exclusive = $this->request->post['check_product_exclusive'];
			}
			$this->load->model('catalog/product');
			$this->model_catalog_product->ProductAssignToSeller($seller_id, $product_ids, '', $check_product_exclusive);
			echo json_encode(array('success' => 'Products assigned to seller.'));
		}
	}

	/*
		 * public method to set unset archive option
		 * for selected product ids
		 * @input : array(product_ids)
		 * @output : string(mesage)
	*/
	public function productManageArchive() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {

			$archive_option = $this->request->post['archive_val'];
			$product_ids = $this->request->post['product_ids'];
			$archive_inventory = new InventoryArchive($this);
			$flag = $archive_inventory->setInventoryArchive($archive_option, $product_ids);
			$val = '';
			echo ($archive_option == 1 ? json_encode(array('success' => "Selected products have been marked as archive"))
				: json_encode(
					array('success' => 'Selected products have been removed from archive')
				));
		}
	}

	/**
	 * Method for set product rating by ajax and rating value store in oc_product table
	 * @request: product_rating : product_rating
	 * @request: product_ids : product_ids
	 * @request: changes_data : changes_data
	 * @return : if data update then successfully msg show , otherwise error msg show
	 * @author : vikas , Apr 2018
	 */
	public function setProductRatingByAjax() {
		if (empty($this->request->post['product_rating']) OR empty($this->request->post['product_ids']) OR empty($this->request->post['changes_data'])) {
			echo json_encode(array('error' => 'You don\'t have modified rating for products!'));
			exit;
		}

		$this->load->model('catalog/product');
		$return_data = $this->model_catalog_product->setProductRatingByAjax($this->request->post);
		if ($return_data) {
			echo json_encode(array('success' => 'You have modified rating for products!'));
		} else {
			echo json_encode(array('success' => 'You don\'t have modified rating for products!'));
		}

	}

	/* Added by Amarat */

	public function setUnitByCategory() {

		$this->load->model('catalog/product');
		$data = array();
		//echo "<pre>"; print_r($this->request); die;
		$product_id = $this->request->post['product_id'];
		$categories = $this->request->post['category'];
		$product_unit_id = $this->request->post['product_unit_id'];
		$data = $this->model_catalog_product->setUnitByCategory($product_id, $categories, $product_unit_id);
		echo json_encode($data);
	}

	/**
	 * public method to change product status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @return 	: string(mesage)
	 * @author: vikas, 2017
	 */
	public function bulkUpdateChangeProductStatus() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {
			$product_status_id = $this->request->post['product_status_id'];
			$product_ids = $this->request->post['product_ids'];

			$this->load->model('catalog/product');
			$this->model_catalog_product->bulkUpdateChangeProductStatus($product_status_id, $product_ids);
			echo json_encode(array('success' => 'Product status changed.'));
		}
	}

	/**
	 * public method to assgin store code for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @return 	: string(mesage)
	 * @author: vikas, 2017
	 */
	public function bulkUpdateAssignStoreCode() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {
			$store_code = $this->request->post['store_code'];
			$product_ids = $this->request->post['product_ids'];

			$this->load->model('catalog/product');
			$this->model_catalog_product->bulkUpdateAssignStoreCode($store_code, $product_ids);
			echo json_encode(array('success' => 'Assigned store code to product.'));
		}
	}

	/**
	 * public method to assgin store code for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @return 	: string(mesage)
	 * @author: vikas, 2017
	 */
	public function bulkUpdateChangeQuantity() {
		if (empty($this->request->post['quantity']) && empty($this->request->post['product_ids'])) {
			echo json_encode(array('error' => 'Please select products or enter quantity greater than 0!'));
			exit;
		}

		$this->load->model('catalog/product');

		$quantity = $this->request->post['quantity'];
		$product_ids = $this->request->post['product_ids'];

		$has_product_option = $this->model_catalog_product->checkAnyProductHasProductOption($product_ids);
		$product_diff = array_diff($product_ids, $has_product_option);

		$this->load->model('catalog/product');
		if (count($product_diff)) {
			$this->model_catalog_product->bulkUpdateChangeQuantity($quantity, $product_diff);
		}
		echo json_encode(array('success' => 'Quantity Updated.', 'has_product_option' => $has_product_option));
		exit;
	}

	/**
	 * public method to assign products exclusive status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @return 	: string(mesage)
	 * @author  : ashish, 2017
	 */
	public function bulkUpdateAssignExclusiveStatus() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {
			$exclusive_status = $this->request->post['exclusive_status'];
			$product_ids = $this->request->post['product_ids'];

			$this->load->model('catalog/product');
			$this->model_catalog_product->bulkUpdateAssignExclusiveStatus($exclusive_status, $product_ids);
			echo json_encode(array('success' => 'Product assigned exclusive.'));
		}
	}

	/**
	 * public method to assgin store code for selected product ids
	 * @request : product_ids: array of product id
	 * @request : update_minimum_quantity: integer of update minimum quantity
	 * @return 	: json(mesage)
	 * @author: vikas, 2018
	 */
	public function applyBulkUpdateMinimumQuantity() {

		if (!empty($this->request->post['update_minimum_quantity']) && !empty($this->request->post['product_ids'])) {
			$update_minimum_quantity = $this->request->post['update_minimum_quantity'];
			$product_ids = $this->request->post['product_ids'];

			$this->load->model('catalog/product');
			$this->model_catalog_product->applyBulkUpdateMinimumQuantity($update_minimum_quantity, $product_ids);
			echo json_encode(array('success' => 'Minimum Quantity Updated.'));
		} else {
			echo json_encode(array('error' => 'Please select product(s) and enter the minimum quantity, should be greater than zero.'));
		}
	}

	/**
	 * Method for Product convert to single
	 * vikas , Jan 2018
	 **/
	public function applyBulkProductConvertToSingle() {

		$this->load->model('catalog/product');
		if (!empty($this->request->post['product_ids'])) {
			$product_ids = $this->request->post['product_ids'];
			foreach ($product_ids as $product_id) {
				$this->model_catalog_product->applyBulkProductConvertToSingle($product_id);
			}
			echo json_encode(array('success' => 'Product convert to singles.'));
		} else {
			echo json_encode(array('error' => 'Please select product(s) to convert to single.'));
		}
	}

	/** Method for product sync to solr
	 * @request : product_ids: array of product id
	 * @return 	: null
	 * @author  : vikas, 2018
	 */
	public function applyBulkProductSyncToSolr() {
		$product_ids = $this->request->post['product_ids'];

		if (empty($product_ids)) {
			echo json_encode(array('error' => 'Please select your product !'));
		}

		$this->load->model('catalog/product');
		$result = $this->model_catalog_product->applyBulkProductSyncToSolr($product_ids);
		if ($result) {
			echo json_encode(array('success' => 'Product(s) sync to solr.'));
		} else {
			echo json_encode(array('error' => 'Product(s) does not sync to solr.'));
		}
	}

	/**
	 * Method for Bulk Update Commission
	 * vikas , Jan 2018
	 **/
	public function applyBulkUpdateCommission() {

		$this->load->model('catalog/product');
		if (!empty($this->request->post['bulk_commission_update']) && !empty($this->request->post['product_ids'])) {
			$bulk_commission_update = $this->request->post['bulk_commission_update'];
			$product_ids = $this->request->post['product_ids'];
			$this->model_catalog_product->applyBulkUpdateCommission($bulk_commission_update, $product_ids);
			echo json_encode(array('success' => 'Your commission Update.'));
		} else {
			echo json_encode(array('error' => 'Please select product(s) to commission update.'));
		}

	}

	/**
	 * Method for Bulk product CSV upload
	 * vikas , Mar 2018
	 **/
	public function applyBulkProductCsvUpload() {
		$this->load->model('catalog/product');
		$data = array();
		$this->load->autoLoadLanguage('catalog/product', $data);

		$config_csv_datas = unserialize(PRODUCT_DATA);

		if (!empty($_FILES['bulk_csv_upload']['tmp_name'])) {
			$products = array();
			$file = fopen($_FILES['bulk_csv_upload']['tmp_name'], 'r');

			// data collect of csv record
			$csv_record = array();
			while (!feof($file)) {
				$csv_record[] = fgetcsv($file);
			}
			fclose($file);

			// collection of data with in array of products info with
			// product id key and product fields is value (old and new )
			$new_csv_data_array = array();
			$validation_data = array();
			$validation_content = '';

			foreach ($csv_record as $csv_keys => $csv_value) {
				if ($csv_keys == 0 || $csv_keys == 1) {
					continue;
				}

				if (!empty($csv_value)) {

					foreach ($csv_value as $csv_key => $csv_data) {
						// product_id , model , sku fixed column so these field skip
						if ($csv_key < 3) {
							continue;
						}

						// checking on even index of $csv_value for new value is not empty
						// if new value is empty then old value and new value does not show.

						if ($csv_key % 2 == 0 && $csv_data != '') {
							// table name i.e. oc_product, oc_product_description etc....
							$table_name = $csv_record[0][$csv_key];
							// single_string i.e. Set Quantity, WSB Product Code etc....
							$single_string = trim(substr($csv_record[1][$csv_key], 3));
							// key_name i.e. model, sku... etc....
							$key_name = $this->getProductKeyTableName($single_string)['column_name'];

							// it's used for if any product have any options.
							if ($key_name == 'quantity' && in_array($csv_value[0], $this->model_catalog_product->checkAnyProductHasProductOption($csv_value[0]))) {
								$validation_content = $csv_value[0] . '~' . $csv_value[1] . '~' . $csv_value[2];
								$validation_data[$validation_content][$single_string] = $data['error_has_product_option'];
							} else if (!empty($config_csv_datas[$table_name][$key_name])
								&&
								!preg_match($config_csv_datas[$table_name][$key_name]['validation_regex'], $csv_data)) {
								$validation_content = $csv_value[0] . '~' . $csv_value[1] . '~' . $csv_value[2];
								$validation_data[$validation_content][$single_string] = $config_csv_datas[$table_name][$key_name]['error_message'];
							} else {
								// if regex match then these data store in new array of new_csv_data_array for rabbitMQ
								$new_csv_data_array['products'][$table_name][$csv_value[0]][$key_name]['old_value'] = $csv_value[$csv_key - 1];
								$new_csv_data_array['products'][$table_name][$csv_value[0]][$key_name]['new_value'] = $csv_data;
							}
						}
					}
				}
			}

			$new_csv_data_array['validation_data'] = $validation_data;

			// // IP and server details
			// $ip = $this->_request->getIpAddress;
			// User Details
			$user_id = !empty((int) $this->user->getId()) ? (int) $this->user->getId() : 0;
			if (method_exists($this->user, 'getUserName')) {
				$user_name = $this->user->getUserName($this->user->getId())['username'];
				$name = $this->user->getUserName($this->user->getId())['name'];
				$user_type = $this->user->getGroupName();
			} else {
				$user_name = 'Automatic Cron';
				$name = 'Automatic Cron';
				$user_type = 'System';
			}

			$dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
			$class = isset($dbt[0]['class']) ? $dbt[0]['class'] : '';
			$function = isset($dbt[0]['function']) ? $dbt[0]['function'] : '';
			$ref_url = $class . '/' . $function;

			$url = !empty($ref_url) ? $ref_url : '';
			if (!empty($route)) {
				$url = $route;
			}

			$additional_data = array();
			$additional_data['user_id'] = $user_id;
			$additional_data['username'] = $user_name;
			$additional_data['name'] = $name;
			$additional_data['user_type'] = $user_type;
			$additional_data['ip'] = $this->request->getIpAddress;
			$additional_data['server'] = $_SERVER['HTTP_USER_AGENT'];
			$additional_data['source_field'] = 'product_list';
			$additional_data['url'] = $url;
			$additional_data['table_name'] = 'oc_product';

			// set additional_data in new_csv_data_array
			$new_csv_data_array['additional_data'] = $additional_data;
			$new_csv_data_array['validation_data'] = $validation_data;

			//Set Connection With RabbitMQ
			$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
			$channel = $connection->channel();

			$queue_name = 'STAGING_GENERAL_TASKS_QUEUE';

			if (SITE_ENVIRONMENT == 'Production') {
				$queue_name = 'GENERAL_TASKS_QUEUE';
			}

			// third parameter is for queue durability. we set it to true
			// so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
			// passive - false ; exclusive - false; auto-delete - false
			$channel->queue_declare($queue_name, false, true, false, false);

			$data = array(
				'constant_value' => unserialize(BULK_PRODUCT_CSV_UPLOAD),
				'data_array' => $new_csv_data_array,
			);
			$queue_object = base64_encode(serialize($data));

			// delivery_mode = 2 makes message persistent (durable)
			$msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
			$channel->basic_publish($msg, '', $queue_name); // send to sms_queue

			$channel->close();
			$connection->close();
		}

		$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'], 'SSL'));
	}

	/**
	 * Method for get key name of product column or table name against of title_name
	 * there are using when bulk product CSV upload by admin
	 * @param: $title_name : string of title name ie. WSB product code, Seller SKU, Set Quantity
	 * @return : return of array with table name ie. oc_product OR column name ie. sku, mode etc....
	 * @author : vikas , March 2018
	 */
	private function getProductKeyTableName($title_name) {
		$product_data_array = unserialize(PRODUCT_LIST_DATA);
		$table_name = '';
		$column_name = '';

		foreach ($product_data_array as $table_name_key => $product_column_array) {
			foreach ($product_column_array as $key => $value) {
				if ($title_name == $value) {
					$table_name = $table_name_key;
					$column_name = $key;
				}
			}
		}

		return array('table_name' => $table_name, 'column_name' => $column_name);
	}

	/*
		 * @method: getAssociateProducts
		 * @param: product_id
		 * @author: Devendra, June 2018
	*/
	protected function getAssociateProducts($product_id) {
		$data = array();
		$this->load->model('catalog/product');

		$this->load->autoLoadLanguage('catalog/product', $data);

		$data['product_status_list'] = $this->model_catalog_product->getProductStatus();
		$data['store_sales_options'] = $this->db->getEnumValues(DB_PREFIX . 'product', 'store_sales');
		//get all column name of products and get from config.php file
		$data['product_table_column_name'] = unserialize(PRODUCT_LIST_DATA);

		$data['products'] = array();
		$data['p_id'] = $product_id;

		$results = $this->model_catalog_product->getAssociateProducts($product_id);
		$product_total = count($results);

		$product_ids = array();
		$product_ids = array_column($results, 'product_id');

		$has_product_option = $this->model_catalog_product->checkAnyProductHasProductOption($product_ids);

		$tax = new Tax($this->registry);
		foreach ($results as $key => $value) {

			//setting value of tax_class_id_from_hsn
			unset($results[$key]['tax_class_id']);
			$results[$key]['tax_class_id'] = $tax->getTaxClassIdFromHSNCode($results[$key]['hsn_code']);

			unset($results[$key]['seller_tax']);
			$results[$key]['seller_tax'] = $tax->getTaxRateForTaxIncludedPrice($results[$key]['price'], $results[$key]['hsn_code'], $results[$key]['mrp']);

			//getting seller_tax_factor and commission factor
			$seller_tax_factor = 1.0 + ((float) $results[$key]['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $value['commission'] / 100.0);
			unset($results[$key]['selling_price']);
			$results[$key]['selling_price'] = ceil(($value['price'] / $seller_tax_factor) * $commission_factor);
			$results[$key]['output_tax_rate'] = $tax->getTaxRate($results[$key]['selling_price'], $results[$key]['tax_class_id'], array(), $results[$key]['mrp']);
			$results[$key]['stock_status_info'] = Cart::getProductStockStatus($results[$key]);
		}

		$this->load->model('localisation/weight_class');
		$weight = $this->model_localisation_weight_class->getWeightClasses();
		$weight = array_combine(
			array_column($weight, 'weight_class_id'),
			array_column($weight, 'unit')
		);

		$this->load->model('tool/image');

		foreach ($results as $result) {
			//if (is_file(DIR_IMAGE . $result['image'])) {
			$image = $this->model_tool_image->resize($result['image'], 40, 40);
			//} else {
			//	$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			//}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}

			$exclusive = '';
			if (isset($result['exclusive'])) {
				switch ($result['exclusive']) {
				case 'normal':
					$exclusive = 'N';
					break;
				case 'exclusive':
					$exclusive = 'E';
					break;
				case 'both':
					$exclusive = 'E & N';
					break;
				}
			}
			/*
				* get seller data if seller id is not balnk
			*/
			$seller_data = array();
			$nickname = '';
			$seller_invoice_generate_status = '';

			if (!empty($result['seller_id'])) {

				$seller_data = SellerInfo::getSellerInvoiceGenerateStatusAndNickname($this->db, $result['seller_id']);
			}

			if (isset($seller_data['nickname'])) {

				$nickname = $seller_data['nickname'];
			}

			if (isset($seller_data['seller_invoice_generate'])) {

				$seller_invoice_generate_status = $seller_data['seller_invoice_generate'];
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image' => $image,
				'name' => $result['name'],
				'hsn_code' => $result['hsn_code'],
				'model' => $result['model'],
				'sku' => $result['sku'],
				'price' => $result['price'],
				'commission' => $result['commission'],
				'special' => $special,
				'quantity' => $result['quantity'],
				'weight' => $result['weight'],
				'weight_unit' => $weight[$result['weight_class_id']],
				'is_single' => $result['is_single'],
				'seller_tax' => $result['seller_tax'],
				'piece_in_set' => $result['piece_in_set'],
				'tax_title' => $result['tax_title'],
				'is_archived' => $result['is_archived'],
				'stock_status_info' => isset($result['stock_status_info']) ? $result['stock_status_info'] : '',
				'tax_class' => $result['tax_class_id'],
				'output_tax_rate' => $result['output_tax_rate'],
				'selling_price' => $result['selling_price'],
				'seller_tax' => $result['seller_tax'],
				'exclusive' => $exclusive,
				'stock_status_info' => isset($result['stock_status_info']) ? $result['stock_status_info'] : '',
				'set_description' => $result['set_description'],
				'status' => ($result['status']) ? $data['text_enabled'] : $data['text_disabled'],
				'status' => ($result['status']) ? $result['status'] : '',
				'product_status_id' => $result['status'],
				'product_rating' => isset($result['product_rating']) ? $result['product_rating'] : '',
				'store_sales' => ($result['store_sales'] != 'NO') ? $result['store_sales'] : '',
				'nickname' => $nickname,
				'seller_invoice_generate_status' => $seller_invoice_generate_status,
				'seller_id' => ($result['seller_id']) ? $result['seller_id'] : 0,
				'sor_product' => $result['sor_product'],
				'sort_order' => (!empty($result['sort_order']) ? $result['sort_order'] : ''),
				'franchise_id' => (!empty($result['franchise_id'])) ? $result['franchise_id'] : 0,
				'exclusive_options' => isset($result['exclusive']) ? $result['exclusive'] : 'normal',
				'cod_available' => $result['cod_available'],
				'non_returnable' => $result['non_returnable'],
				'is_associate' => $result['is_associate'],
				'has_product_option' => ((in_array($result['product_id'], $has_product_option)) ? 'true' : 'false'),
				'product_option_name' => (!empty($result['product_option_name']) ? $result['product_option_name'] : ''),
			);
		}

		$data['product_edit_enable'] = '';

		if (in_array($this->user->getId(), explode(',', ADMIN_IDS)) || in_array($this->user->getId(), STORE_INVENTORY_ADMIN_IDS)) {
			$data['product_edit_enable'] = 1;
		}

		$data['token'] = $this->session->data['token'];

		// URL for pagination
		/*	$pagination_url = $url;

		if (isset($this->request->get['sort'])) {
			$pagination_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$pagination_url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		//$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->limit = 100;
		$pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		//$data['results'] = sprintf($data'text_pagination'], ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
		$data['results'] = sprintf($data['text_pagination'],
									($product_total) ? (($page - 1) * $filter_page_limit) + 1 : 0,
									((($page - 1) * $filter_page_limit) > ($product_total - $filter_page_limit)) ? $product_total : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
										$product_total, ceil($product_total / $filter_page_limit));*/

		return $this->load->view('catalog/associate_products.tpl', $data);
	}

	/*
		 * @method: searchAssociateProducts
		 * @purpose: search the associate products,  called from product edit section in admin panel
		 * @author: Devendra, June 2018
	*/
	public function searchAssociateProducts() {
		$this->load->model('catalog/product');

		if (isset($this->request->post['filter_name'])) {
			$filter_name = $this->request->post['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->post['filter_model'])) {
			$filter_model = $this->request->post['filter_model'];
			$filter_operator = (!empty($this->request->post['filter_operator']) ? $this->request->post['filter_operator'] : null);
			$filter_feature_box_type = (!empty($this->request->post['filter_feature_box_type']) ? $this->request->post['filter_feature_box_type'] : null);
			$filter_type_string = (!empty($this->request->post['filter_type_string']) ? $this->request->post['filter_type_string'] : null);
			$filter_val_from = (!empty($this->request->post['filter_val_from']) ? $this->request->post['filter_val_from'] : 0);
			$filter_val_to = (!empty($this->request->post['filter_val_to']) ? $this->request->post['filter_val_to'] : 0);
		} else {
			$filter_model = null;
			$filter_operator = null;
			$filter_feature_box_type = null;
			$filter_type_string = null;
			$filter_val_from = 0;
			$filter_val_to = 0;
		}

		if (isset($this->request->post['filter_solr_enabled'])) {
			$filter_solr_enabled = $this->request->post['filter_solr_enabled'];
		} else {
			$filter_solr_enabled = null;
		}

		if (isset($this->request->post['sort'])) {
			$sort = $this->request->post['sort'];
		} else {
			$sort = 'p.date_added';
		}

		if (isset($this->request->post['order'])) {
			$order = $this->request->post['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->post['filter_page_limit'])) {
			$filter_page_limit = $this->request->post['filter_page_limit'];
		} else {
			$filter_page_limit = $this->config->get('config_limit_admin');
		}

		$data = array();
		// Initializing the data array to be passed on to template files

		// Autoloading the lanugage
		$this->load->autoLoadLanguage('catalog/product', $data);

		$data['product_status_list'] = $this->model_catalog_product->getProductStatus();
		$data['store_sales_options'] = $this->db->getEnumValues(DB_PREFIX . 'product', 'store_sales');

		//get all column name of products and get from config.php file
		$data['product_table_column_name'] = unserialize(PRODUCT_LIST_DATA);

		$data['products'] = array();

		if ((isset($filter_seller_list) and $filter_seller_list == 'all') or !isset($filter_seller_list)) {
			$filter_data = array(
				'filter_name' => $filter_name,
				'filter_model' => $filter_model,
				'filter_operator' => $filter_operator,
				'filter_type_string' => $filter_type_string,
				'filter_val_from' => $filter_val_from,
				'filter_val_to' => $filter_val_to,
				'filter_feature_box_type' => $filter_feature_box_type,
				'filter_solr_enabled' => $filter_solr_enabled,
				'sort' => $sort,
				'order' => $order,
				'start' => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit' => $filter_page_limit,
				'is_associate' => 1,
			);
		} else {
			$filter_data = array(
				'filter_name' => $filter_name,
				'filter_model' => $filter_model,
				'filter_operator' => $filter_operator,
				'filter_type_string' => $filter_type_string,
				'filter_val_from' => $filter_val_from,
				'filter_val_to' => $filter_val_to,
				'filter_feature_box_type' => $filter_feature_box_type,
				'filter_solr_enabled' => $filter_solr_enabled,
				'sort' => $sort,
				'order' => $order,
				'start' => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit' => $filter_page_limit,
				'is_associate' => 1,
			);
		}

		//Some Changes in catalog/Product
		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED && $filter_data['filter_solr_enabled'] == 1) {

			$solr = new SolrProduct($this);
			$backend = 1;
			/*if ($filter_data['filter_solr_enabled']==1 && $filter_data['filter_search_like_web_enabled']==1) {
				    	$backend = 0;
			*/
			$results_solr = $solr->getProductFromSolr($filter_data, $backend);
			$this->load->model('catalog/product');
			//$productids = implode(',', array_keys($results_solr['products']));
			$productids = implode(',', $results_solr['products']);
			$results = $this->model_catalog_product->getProductsUsingProductIds($productids);
			$p_ids = explode(',', $productids);
			$arr = array();
			foreach ($p_ids as $value) {
				foreach ($results as $key => $val) {
					if ($value == $val['product_id']) {
						$arr[] = $val;
						break;
					}
				}
			}
			$results = $arr;
			$product_total = $results_solr['product_total'];
		} else {
			//check page request commming from Franchise Product or simple product
			$filter_data['request_page'] = 'catalog_product';

			$results_data = $this->model_catalog_product->getProducts($filter_data);

			$results = $results_data['products'];
			$product_total = $results_data['total_count'];
		}

		$product_ids = array();
		$product_ids = array_column($results, 'product_id');

		$has_product_option = $this->model_catalog_product->checkAnyProductHasProductOption($product_ids);

		$archive_inventory = new InventoryArchive($this);

		$tax = new Tax($this->registry);
		foreach ($results as $key => $value) {

			//setting value of tax_class_id_from_hsn
			unset($results[$key]['tax_class_id']);
			$results[$key]['tax_class_id'] = $tax->getTaxClassIdFromHSNCode($results[$key]['hsn_code']);

			unset($results[$key]['seller_tax']);
			$results[$key]['seller_tax'] = $tax->getTaxRateForTaxIncludedPrice($results[$key]['price'], $results[$key]['hsn_code'], $results[$key]['mrp']);

			//getting seller_tax_factor and commission factor
			$seller_tax_factor = 1.0 + ((float) $results[$key]['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $value['commission'] / 100.0);
			unset($results[$key]['selling_price']);
			$results[$key]['selling_price'] = ceil(($value['price'] / $seller_tax_factor) * $commission_factor);
			$results[$key]['output_tax_rate'] = $tax->getTaxRate($results[$key]['selling_price'], $results[$key]['tax_class_id'], array(), $results[$key]['mrp']);
			$results[$key]['stock_status_info'] = Cart::getProductStockStatus($results[$key]);
		}

		$this->load->model('localisation/weight_class');
		$weight = $this->model_localisation_weight_class->getWeightClasses();
		$weight = array_combine(
			array_column($weight, 'weight_class_id'),
			array_column($weight, 'unit')
		);

		//getting tax title
		$this->load->model('localisation/tax_class');
		$tax_map = $this->model_localisation_tax_class->getTaxClasses();
		$tax_map = array_combine(
			array_column($tax_map, 'tax_class_id'),
			array_column($tax_map, 'title')
		);

		$this->load->model('tool/image');

		foreach ($results as $result) {
			//if (is_file(DIR_IMAGE . $result['image'])) {
			$image = $this->model_tool_image->resize($result['image'], 40, 40);
			//} else {
			//	$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			//}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}

			$exclusive = '';
			if (isset($result['exclusive'])) {
				switch ($result['exclusive']) {
				case 'normal':
					$exclusive = 'N';
					break;
				case 'exclusive':
					$exclusive = 'E';
					break;
				case 'both':
					$exclusive = 'E & N';
					break;
				}
			}
			/*
				* get seller data if seller id is not balnk
			*/
			$seller_data = array();
			$nickname = '';
			$seller_invoice_generate_status = '';

			if (!empty($result['seller_id'])) {

				$seller_data = SellerInfo::getSellerInvoiceGenerateStatusAndNickname($this->db, $result['seller_id']);
			}

			if (isset($seller_data['nickname'])) {

				$nickname = $seller_data['nickname'];
			}

			if (isset($seller_data['seller_invoice_generate'])) {

				$seller_invoice_generate_status = $seller_data['seller_invoice_generate'];
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image' => $image,
				'name' => $result['name'],
				'hsn_code' => $result['hsn_code'],
				'model' => $result['model'],
				'sku' => $result['sku'],
				'price' => $result['price'],
				'commission' => $result['commission'],
				'special' => $special,
				'quantity' => $result['quantity'],
				'weight' => $result['weight'],
				'weight_unit' => $weight[$result['weight_class_id']],
				'is_single' => $result['is_single'],
				'seller_tax' => $result['seller_tax'],
				'piece_in_set' => $result['piece_in_set'],
				'tax_title' => $result['tax_title'],
				'is_archived' => $result['is_archived'],
				'stock_status_info' => isset($result['stock_status_info']) ? $result['stock_status_info'] : '',
				'tax_class' => $result['tax_class_id'],
				'output_tax_rate' => $result['output_tax_rate'],
				'selling_price' => $result['selling_price'],
				'seller_tax' => $result['seller_tax'],
				'exclusive' => $exclusive,
				'stock_status_info' => isset($result['stock_status_info']) ? $result['stock_status_info'] : '',
				'set_description' => $result['set_description'],
				'status' => ($result['status']) ? $data['text_enabled'] : $data['text_disabled'],
				'status' => ($result['status']) ? $result['status'] : '',
				'product_status_id' => $result['status'],
				'product_rating' => isset($result['product_rating']) ? $result['product_rating'] : '',
				'store_sales' => ($result['store_sales'] != 'NO') ? $result['store_sales'] : '',
				'nickname' => $nickname,
				'seller_invoice_generate_status' => $seller_invoice_generate_status,
				'seller_id' => ($result['seller_id']) ? $result['seller_id'] : 0,
				'sor_product' => $result['sor_product'],
				'sort_order' => (!empty($result['sort_order']) ? $result['sort_order'] : ''),
				'franchise_id' => (!empty($result['franchise_id'])) ? $result['franchise_id'] : 0,
				'exclusive_options' => isset($result['exclusive']) ? $result['exclusive'] : 'normal',
				'cod_available' => $result['cod_available'],
				'non_returnable' => $result['non_returnable'],
				'is_associate' => $result['is_associate'],
				'has_product_option' => ((in_array($result['product_id'], $has_product_option)) ? 'true' : 'false'),
				'product_option_name' => (!empty($result['product_option_name']) ? $result['product_option_name'] : ''),
			);
		}

		$data['product_edit_enable'] = '';

		if (in_array($this->user->getId(), explode(',', ADMIN_IDS)) || in_array($this->user->getId(), STORE_INVENTORY_ADMIN_IDS)) {

			$data['product_edit_enable'] = 1;
		}

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
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		//$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->limit = $filter_page_limit;

		$pagination->url = $this->url->link('catalog/product/searchAssociateProducts', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		//$data['results'] = sprintf($data'text_pagination'], ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
		$data['results'] = sprintf($data['text_pagination'],
			($product_total) ? (($page - 1) * $filter_page_limit) + 1 : 0,
			((($page - 1) * $filter_page_limit) > ($product_total - $filter_page_limit)) ? $product_total : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
			$product_total, ceil($product_total / $filter_page_limit));

		$filter_data['request_page'] = 'catalog_product';

		$json['success'] = $this->load->view('catalog/search_associate_products.tpl', $data);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Method for merge Different Design Product
	 * get all product image and arrange it according design
	 * @param: $product_id : intiger
	 * @return : return of array product related image....
	 * @author : RAHUL , 29 June 2018
	 */
	public function mergeDifferentDesignProduct() {
		// get product status

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data = array();
		// Initializing the data array to be passed on to template files

		// Autoloading the lanugage
		$this->load->autoLoadLanguage('catalog/design_product', $data);
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			if (!empty($this->request->get['filter_feature_box_type'])) {
				$url .= '&filter_feature_box_type=' . urlencode(html_entity_decode($this->request->get['filter_feature_box_type'], ENT_QUOTES, 'UTF-8'));
			}
			if (!empty($this->request->get['filter_operator'])) {
				$url .= '&filter_operator=' . urlencode(html_entity_decode($this->request->get['filter_operator'], ENT_QUOTES, 'UTF-8'));
			}
			if (!empty($this->request->get['filter_type_string'])) {
				$url .= '&filter_type_string=' . urlencode(html_entity_decode($this->request->get['filter_type_string'], ENT_QUOTES, 'UTF-8'));
			}
			if (!empty($this->request->get['filter_val_from']) && !empty($this->request->get['filter_val_to'])) {
				$url .= '&filter_val_from=' . urlencode(html_entity_decode($this->request->get['filter_val_from'], ENT_QUOTES, 'UTF-8'));
				$url .= '&filter_val_to=' . urlencode(html_entity_decode($this->request->get['filter_val_to'], ENT_QUOTES, 'UTF-8'));
			}
		}

		if (isset($this->request->get['filter_price_from'])) {
			$url .= '&filter_price_from=' . $this->request->get['filter_price_from'];
		}

		if (isset($this->request->get['filter_price_to'])) {
			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];
		}

		if (isset($this->request->get['filter_commission_from'])) {
			$url .= '&filter_commission_from=' . $this->request->get['filter_commission_from'];
		}

		if (isset($this->request->get['filter_commission_to'])) {
			$url .= '&filter_commission_to=' . $this->request->get['filter_commission_to'];
		}

		if (isset($this->request->get['filter_seller_sku'])) {
			$url .= '&filter_seller_sku=' . $this->request->get['filter_seller_sku'];
			if (!empty($this->request->get['sllr_sku_filter_feature_box_type'])) {
				$url .= '&sllr_sku_filter_feature_box_type=' . urlencode(html_entity_decode($this->request->get['sllr_sku_filter_feature_box_type'], ENT_QUOTES, 'UTF-8'));
			}
			if (!empty($this->request->get['sllr_sku_filter_operator'])) {
				$url .= '&sllr_sku_filter_operator=' . urlencode(html_entity_decode($this->request->get['sllr_sku_filter_operator'], ENT_QUOTES, 'UTF-8'));
			}
			if (!empty($this->request->get['sllr_sku_filter_type_string'])) {
				$url .= '&sllr_sku_filter_type_string=' . urlencode(html_entity_decode($this->request->get['sllr_sku_filter_type_string'], ENT_QUOTES, 'UTF-8'));
			}
			if (!empty($this->request->get['sllr_sku_filter_val_from']) && !empty($this->request->get['sllr_sku_filter_val_to'])) {
				$url .= '&sllr_sku_filter_val_from=' . urlencode(html_entity_decode($this->request->get['sllr_sku_filter_val_from'], ENT_QUOTES, 'UTF-8'));
				$url .= '&sllr_sku_filter_val_to=' . urlencode(html_entity_decode($this->request->get['sllr_sku_filter_val_to'], ENT_QUOTES, 'UTF-8'));
			}
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['filter_non_single'])) {
			$url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
		}

		if (isset($this->request->get['filter_solr_enabled'])) {
			$url .= '&filter_solr_enabled=' . $this->request->get['filter_solr_enabled'];
		}
		if (isset($this->request->get['filter_search_like_web_enabled'])) {
			$url .= '&filter_search_like_web_enabled=' . $this->request->get['filter_search_like_web_enabled'];
		}

		if (isset($this->request->get['filter_non_sor'])) {
			$url .= '&filter_non_sor=' . $this->request->get['filter_non_sor'];
		}

		if (isset($this->request->get['filter_seller_list'])) {
			$url .= '&filter_seller_list=' . $this->request->get['filter_seller_list'];
		}

		if (isset($this->request->get['filter_sort_order_from'])) {
			$url .= '&filter_sort_order_from=' . $this->request->get['filter_sort_order_from'];
		}

		if (isset($this->request->get['filter_sort_order_to'])) {
			$url .= '&filter_sort_order_to=' . $this->request->get['filter_sort_order_to'];
		}

		if (isset($this->request->get['filter_store_sales_code'])) {
			$url .= '&filter_store_sales_code=' . $this->request->get['filter_store_sales_code'];
		}

		if (isset($this->request->get['filter_hsn_code'])) {
			$url .= '&filter_hsn_code=' . $this->request->get['filter_hsn_code'];
		}

		if (isset($this->request->get['filter_page_limit'])) {
			$url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
		}

		if (isset($this->request->get['filter_franchise_tab'])) {
			$url .= '&filter_franchise_tab=' . $this->request->get['filter_franchise_tab'];
		}

		if (isset($this->request->get['filter_franchise_id'])) {
			$url .= '&filter_franchise_id=' . $this->request->get['filter_franchise_id'];
		}

		if (isset($this->request->get['filter_images'])) {
			$url .= '&filter_images=' . $this->request->get['filter_images'];
		}

		// URL for General links to ensure we reach same settings again on the list page
		$general_url = $url;

		if (isset($this->request->get['sort'])) {
			$general_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$general_url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$general_url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
			$data['breadcrumbs'][] = array(
				'text' => $data['text_product'],
				'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url . '&filter_franchise_tab=' . '1', 'SSL'),
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $data['text_product'],
				'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url, 'SSL'),
			);
		}

		$data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url, 'SSL');

		$product_id = $this->request->request['product_id'];
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if (isset($this->request->post['updateDesign']) && $this->request->post['updateDesign'] == 'update') {
				//======delete default image=======//
				//$this->model_catalog_product->deleteDefaultDesignProduct($product_id);

				//=======reset all design before update========//
				$this->model_catalog_product->resetDefaultDesignProduct($product_id);

				foreach ($this->request->post['image'] as $key => $productImageArray) {
					foreach ($productImageArray as $key2 => $value) {
						$saveData = array();
						$imageIdval = $value != '' ? $value : '0';
						$saveData['front_image'] = '0';
						if (isset($this->request->post['imageorder']) && in_array($imageIdval, $this->request->post['imageorder'])) {
							$saveData['front_image'] = '1';
						}
						if (isset($this->request->post['product'][$key]['design_group'])) {
							$saveData['design_group_title'] = $this->request->post['product'][$key]['design_group'];
						} else {
							$saveData['design_group_title'] = '';
						}

						$saveData['group_sort_order'] = $key2;
						if ($value != '') {

							$saveData['product_image_id'] = $value;
							$this->model_catalog_product->mergeDifferentDesignProduct($saveData, 1);
						} else {
							$fields = array("image", "product_id", "image_dimensions");
							$product_data = $this->model_catalog_product->getProduct($product_id, $fields);
							$saveData['product_id'] = $product_data['product_id'];
							$saveData['image'] = $product_data['image'];
							$saveData['image_dimensions'] = $product_data['image_dimensions'];
							$saveData['default_image'] = '1';
							$this->model_catalog_product->mergeDifferentDesignProduct($saveData, 0);

						}
					}
				}

				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $general_url, 'SSL'));

			}

		}

		$fields = array("piece_in_set");
		$data['product_data'] = $this->model_catalog_product->getProduct($product_id, $fields);
		$data['product_images'] = $this->model_catalog_product->getProductImages($product_id);
		$defaultImageSave = '0';
		$groupTitleArray = array();
		foreach ($data['product_images'] as $key => $productImage) {
			if (empty($productImage['product_image_id'])) {
				$defaultImageKey = $key;
			}
			if (!empty($productImage['default_image']) && $productImage['default_image'] == '1') {
				$defaultImageSave = '1';
			}
			if (!empty($productImage['design_group_title'])) {
				$groupTitleArray[$key] = $productImage['design_group_title'];
			}
			//$data['product_images'][$key]['thumb'] = $this->model_tool_image->resize($productImage['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			$data['product_images'][$key]['thumb'] = $this->model_tool_image->resize($productImage['image'], 150, 150);
		}
		if (!empty($groupTitleArray)) {
			$groupTitleArrayFillter = array_unique(array_filter($groupTitleArray));
			$data['groupTitleArray'] = array_values($groupTitleArrayFillter);
		}
		//echo '<pre>';print_r($data['groupTitleArray']); exit;
		//========remove default image from array if it was saved in productimage table=====//
		if (isset($defaultImageKey) && $defaultImageSave == '1') {
			unset($data['product_images'][$defaultImageKey]);
		}

		if (isset($this->request->request['product_id'])) {
			$general_url .= '&product_id=' . $this->request->request['product_id'];
		}

		$data['action'] = $this->url->link('catalog/product/mergeDifferentDesignProduct', 'token=' . $this->session->data['token'] . $general_url, 'SSL');

		$data['text_form'] = $data['text_edit'];
		$data['product_status_list'] = $this->model_catalog_product->getProductStatus();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['error_warning'] = '';

		$this->response->setOutput($this->load->view('catalog/merge_different_design_product.tpl', $data));
	}

	/**
	 * Function updateHiddenSellingPriceOfProduct used to update hidding selling price of product
	 * @auther: Nilesh, 2018
	 */
	public function updateHiddenSellingPriceOfProduct(): string{
		$this->load->language('catalog/product');
		$this->load->model('catalog/product');
		$data = array();
		$this->load->autoLoadLanguage('catalog/product', $data);
		$json = array();
		if (!empty($this->request->get['product_id']) && (float) $this->request->get['new_value'] > 0) {
			$product_id = (int) ($this->request->get['product_id']);
			$field_type = 'hidden_selling_price';
			$field_value = (float) $this->request->get['new_value'];

			$old_value = (float) $this->request->get['old_value'];
			$new_value = (float) $this->request->get['new_value'];

			$changes_data = array(
				$field_type => array(
					'old_value' => $old_value,
					'new_value' => $new_value,
				),
			);
			$this->model_catalog_product->updateProductList($product_id, $field_type, $field_value, $changes_data);

			$json['error'] = '';
			$json[$field_type] = $field_value;
			echo json_encode($json);
			exit;
		}
	}
	/**
	 * Method for get reference no for all row entries
	 * @param : data array required
	 * @return : status string
	 * @author : Kalyan, 13th Sept 2018
	 */
	private function __setReference(array $data): string{

		$reference = '';

		switch ($data['transaction_type']) {

		case 'WSB Purchase':
			$reference = 'Inv.# ' . $data['reference_document'];
			break;
		case 'WSB Purchase Return':
			$reference = 'Dn.# ' . $data['reference_document'];
			break;
		case 'Stock Transfer':
			$reference = $data['reference_no'] . ' <br> ' . $data['order_status'];
			break;
		case 'Stock Transfer Return':
			$reference = $data['reference_no'] . ' <br> ' . 'Cn.# : ' . $data['reference_document'] . '';
			break;
		case 'Sales':
			$reference = $data['reference_no'] . ' <br> ' . $data['order_status'];
			break;
		case 'Sales Return':
			$reference = $data['reference_no'] . ' <br> ' . 'Cn.# : ' . $data['reference_document'] . '';
			break;
		case 'Loss Booked By WSB':
			$reference = $data['transaction_type'] . ' <br> ' . $data['reference_document'];
			break;
		case 'Debited to Logistics':
			$reference = 'Dn.# : ' . $data['reference_document'] . '';
			break;
		default:
			# code...
			break;
		}
		return $reference;
	}
	/**
	 * get alpha numeric string
	 * @param string required
	 * @return string
	 * @author Kalyan 26th Sept. 2018
	 */
	public function getAlphaNumericString($string) {
		return preg_replace('/[^A-Za-z0-9\- ]/', '', $string); // Removes special chars.
	}

	/**
	 * public method to assign products sor for selected product ids
	 * @request : product_ids: array of product id
	 * @request : sor_days: integer of sor_days
	 * @request : sor_type: string of sor_type
	 * @return 	: string(mesage)
	 * @author  : mahaveer, 2019
	 */
	public function bulkUpdateSor() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {

			$this->load->model('catalog/product');
			if (!empty($this->request->post['sor_days'])) {
				$sor_days = $this->request->post['sor_days'];
				$sor_type = $this->request->post['sor_type'];
				$product_ids = $this->request->post['product_ids'];
				$success = $this->model_catalog_product->bulkUpdateSor($sor_days, $sor_type, $product_ids, 'update');
				if ($success) {
					echo json_encode(array('success' => 'Selected product marked sor except non-reurnable.'));
				} else {
					echo json_encode(array('success' => 'Error: Product is non-reurnable.'));
				}

			} else {
				$product_ids = $this->request->post['product_ids'];
				$this->model_catalog_product->bulkUpdateSor(0, '', $product_ids, 'remove');
				echo json_encode(array('success' => 'Selected product remove from sor.'));
			}
		}
	}

	public function getsearchproduct($name) {
		$this->load->model('catalog/searchproduct');
		//echo $name; exit;
		/*echo json_encode($this->request->post['name']); exit;
	        echo '<pre>'; print_r($this->request->post['name']); exit;
*/

		//echo '<pre>'; print_r($this->request->post['name']); exit;
		if (isset($this->request->post['name'])) {
			$data['filter_name'] = $this->request->post['name'];
		} else {
			$data['filter_name'] = 'Yellow';
		}
		$data = $this->model_catalog_searchproduct->getProducts($data);
		echo json_encode($data);exit;
		//echo '<pre>';print_r($data); exit;
	}

	/**
	 * public method to assign products sor for selected product ids
	 * @request : filter_name
	 * @return 	: string(mesage)
	 * @author  : rahul, 2019
	 */

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {

			$this->load->model('catalog/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start' => 0,
				'limit' => 15,
			);

			$filters = $this->model_catalog_product->getProductsNameOnly($filter_data);

			foreach ($filters as $filter) {
				$json[] = array(
					'product_id' => $filter['product_id'],
					'name' => strip_tags(html_entity_decode($filter['name'], ENT_QUOTES, 'UTF-8')),
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
