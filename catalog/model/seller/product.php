<?php
class ModelSellerProduct extends Model {

	public function getProduct($product_id) {
		$seller_id = $this->customer->getId();
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) INNER JOIN " . DB_PREFIX . "ms_product mp ON (p.product_id = mp.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND mp.seller_id = '" . $seller_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}
        
        if (isset($data['filter_commission']) && !is_null($data['filter_commission'])) {
			$sql .= " AND p.commission = '" . (float)$data['filter_commission'] . "'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
        
        if (isset($data['filter_non_single']) && $data['filter_non_single']==1) {
			$sql .= " AND p.is_single = '0'";
		}
		
		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
            'p.commission',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
                'set_description' => $result['set_description'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po INNER JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) INNER JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

			foreach ($product_option_value_query->rows as $product_option_value) {
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

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND store_id NOT IN (0) ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}
        
        if (isset($data['filter_commission']) && !is_null($data['filter_commission'])) {
			$sql .= " AND p.commission = '" . (float)$data['filter_commission'] . "'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
        
        if (isset($data['filter_non_single']) && $data['filter_non_single']==1) {
			$sql .= " AND p.is_single = '0'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
	public function setSeoUrl($form_name, $form_model){
		
		$name_after_apostrophe = str_replace("'","",trim($form_name));
		$name_after_ampersand = str_replace("amp","",trim($name_after_apostrophe));
		$name_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($name_after_ampersand));
		$name = strtolower(implode("-",preg_split('/[\s]+/', trim($name_after_regex))));

		$model_after_apostrophe = str_replace("'","",trim($form_model));
		$model_after_ampersand = str_replace("amp","",trim($model_after_apostrophe));
		$model_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($model_after_ampersand));
		$model = strtolower(implode("-",preg_split('/[\s]+/', trim($model_after_regex))));

		$seo_line = $name."-".$model ;
		return $seo_line;
	}
	public  function getProductFiltersData($product_id){

		$q = "SELECT 
				f.filter_id, 
				GROUP_CONCAT(fd.name SEPARATOR ', ') as filter_name,
				(
				SELECT name 
				FROM ".DB_PREFIX."filter_group_description fgd 
				WHERE fl.filter_group_id = fgd.filter_group_id 
				AND fgd.language_id = 1
				) as group_name
			  FROM 
			  	".DB_PREFIX."product_filter f
			  INNER JOIN ".DB_PREFIX."filter_description fd 
			  	ON f.filter_id = fd.filter_id AND fd.language_id =1
			  INNER JOIN ".DB_PREFIX."filter fl
			  	ON f.filter_id = fl.filter_id
			  WHERE 
			  	f.product_id = ".(int)$product_id."
			  GROUP BY 
			  	group_name";

		$query = $this->db->query($q);

		return $query->rows;
	}
	public function dynamicmetatags(){

		$this->load->model('seller/filter');

		if($this->request->post['product_description'][1]['meta_description'] == ''){

			$filter_ids = $this->request->post['product_filter'];
			$filters = array();
			foreach($filter_ids as $id){
				$data = $this->model_seller_filter->getFilter($id);
				$filters[] = $data['name'];
			}
			$filters = implode(',',$filters);
			$this->request->post['product_description'][1]['meta_description'] = sprintf($this->language->get('meta_description'), $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name'], $filters, $this->request->post['product_description'][1]['name']);

		}

		if($this->request->post['product_description'][1]['meta_title'] == ''){

			$this->request->post['product_description'][1]['meta_title'] = sprintf($this->language->get('meta_title'), $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name']);
		}

		if($this->request->post['product_description'][1]['meta_keyword'] == ''){

			$this->request->post['product_description'][1]['meta_keyword'] = sprintf($this->language->get('meta_title'), $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name']);
		}
	}

	/**
	 * get product availability in alternate store
	 * @param $is_single
	 * @param $product_name
	 * @return mixed
	 */
	public function getAlternateProductInfo($is_single, $product_name){
		if(!$is_single){
			$sql = "SELECT p.selling_price, p.product_id FROM ".DB_PREFIX."product p WHERE model LIKE '".$product_name."-SNGL'";
			$query = $this->db->query($sql);

		}else{
			$sql = "SELECT p.selling_price, p.product_id FROM ".DB_PREFIX."product p WHERE model LIKE '".chop($product_name,'-SNGL')."'";
			$query = $this->db->query($sql);
		}
		
		return $query->row;

	}

	public function get_store_data(){
		$sql = "SELECT * from " . DB_PREFIX . "store";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function store_data(){
		if($this->request->post['store_id']){
			$store_id = $this->request->post['store_id'];
		}else{
			$store_id = $this->request->post['product_store'];
		}
		$product_id = $this->request->get['product_id'] ?? 0;
		
		if($this->request->post['Language']){
			$language = (int)$this->request->post['Language'];
		}else{
			$language = (int)$this->request->post['language_id'];;
		}
		if($this->request->post['Meta_Tag_Title']){
			$meta_title = $this->request->post['Meta_Tag_Title'];
		}else{
			$meta_title = $this->request->post['meta_title'];
		}
		if($this->request->post['Meta_Tag_Keywords']){
			$meta_keyword = $this->request->post['Meta_Tag_Keywords'];
		}else{
			$meta_keywords = $this->request->post['meta_keywords'];;
		}
		if($this->request->post['Meta_Tag_Description']){
			$meta_description = $this->request->post['Meta_Tag_Description'];
		}else{
			$meta_description = $this->request->post['meta_description'];
		}

		if (is_array($store_id)) {
			foreach ($store_id as $store) {
				$p_id = "SELECT product_id,store_id FROM 
						" . DB_PREFIX . "product_storeinfo WHERE product_id= '". (int)$product_id ."' 
						AND store_id = '". $store ."'";
				$p_id   = $this->db->query($p_id);
				$pro_id = $p_id->row['product_id'];
				$s_id   = trim($p_id->row['store_id']);
				$store = trim($store); 
				if(($pro_id == $product_id)&&($s_id == $store)){
					$sql = "UPDATE " . DB_PREFIX . "product_storeinfo SET store_id = '". $store ."', product_id = '". (int)$product_id ."', language = '". $language ."', meta_title = '". $meta_title ."', meta_keywords = '".  $meta_keyword ."', meta_description = '". $meta_description ."', modify = NOW() WHERE product_id= '". (int)$product_id ."' AND store_id = '". $store ."'";

				}else{
					$sql = "INSERT INTO " . DB_PREFIX . "product_storeinfo SET store_id = '". $store ."', product_id = '". (int)$product_id ."', language = '". $language ."', meta_title = '". $meta_title ."', meta_keywords = '".  $meta_keyword ."', meta_description = '". $meta_description ."', created = NOW(), modify = NOW()";
				}
				$query = $this->db->query($sql);			
			}
		} else {

			$p_id = "SELECT product_id,store_id FROM 
					" . DB_PREFIX . "product_storeinfo WHERE product_id= '". (int)$product_id ."' 
					AND store_id = '". (int)$store_id ."'";
			$p_id   = $this->db->query($p_id);
			$pro_id = $p_id->row['product_id'];
			$s_id   = trim($p_id->row['store_id']);
			$store_id = trim($store_id); 
			if(($pro_id == $product_id)&&($s_id == $store_id)){
				$sql = "UPDATE " . DB_PREFIX . "product_storeinfo SET store_id = '". $store_id ."', product_id = '". (int)$product_id ."', language = '". $language ."', meta_title = '". $meta_title ."', meta_keywords = '".  $meta_keyword ."', meta_description = '". $meta_description ."', modify = NOW() WHERE product_id= '". (int)$product_id ."' AND store_id = '". $store_id ."'";

			}else{
				$sql = "INSERT INTO " . DB_PREFIX . "product_storeinfo SET store_id = '". $store_id ."', product_id = '". (int)$product_id ."', language = '". $language ."', meta_title = '". $meta_title ."', meta_keywords = '".  $meta_keyword ."', meta_description = '". $meta_description ."', created = NOW(), modify = NOW()";
			}
			$query = $this->db->query($sql);			
		}
		
		return $query->rows;

	}
	public function get_product_store_data(){
		
		if($this->request->get['product_id']){
			$product_id = $this->request->get['product_id'];
		}else{
			$product_id = '';
		}

		$sql = "SELECT * from " . DB_PREFIX . "product_storeinfo WHERE product_id= '". $product_id ."'";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function get_ajax_product_store_data($store_id,$product_id){
		$sql = "SELECT * from " . DB_PREFIX . "product_storeinfo WHERE product_id= '". $product_id ."' AND store_id= '".$store_id."'";

		$query = $this->db->query($sql);
		return $query->rows;	

	}

	public function getUpcomingProducts() {

		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.status = 4 ";

		/*if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_commission']) && !is_null($data['filter_commission'])) {
			$sql .= " AND p.commission = '" . (float)$data['filter_commission'] . "'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_non_single']) && $data['filter_non_single']==1) {
			$sql .= " AND p.is_single = '0'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.commission',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		*/

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	* Method for get seller products for bulk updated
	* @param: $seller_id : Integer for seller id
	* @return: return an array with product info i.e. (product_id, sku, price, quantity)
	* @author: Vikas, 2017
	*/
	public function getSellerProductsForBulkUpdate($seller_id){

		$sql = "SELECT product_id 
				FROM " . DB_PREFIX . "ms_product
				WHERE seller_id = " . (int)$seller_id;				
		$query = $this->db->query($sql);

		$product_ids = array_column($query->rows , 'product_id');

		$sql = "SELECT p.product_id,
					   p.sku,
					   p.price,
					   p.quantity, 
					   p.store_sales, 
                       p.hsn_code, 
                       pd.name 
				FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id 
				WHERE p.product_id IN ( " . implode(',', $product_ids). " ) 
                  AND pd.language_id = 1";
		$query = $this->db->query($sql);		

		if( $query->num_rows ){
			return $query->rows;
		} else {
			return false;
		}
	}

	/**
	* Method for check HSN code of perticular product_id
	* @param: $product_id : Integer for product id
	* @return: return old value of HSN Code if already exists, otherwise false,
	* @author: Vikas, 2017
	*/
	public function checkHSNCodeInProductBySeller($product_id){

		$sql = "SELECT hsn_code 
				FROM " . DB_PREFIX . "product
				WHERE product_id = " . (int)$product_id;				
		$query = $this->db->query($sql);		

		if( $query->num_rows ){
			return $query->row['hsn_code'];
		} else {
			return false;
		}
	}
}
