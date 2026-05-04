<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getInformations() {
		$query = $this->db->query("SELECT 
			      i.information_id,
			      id.title,
			      id.short_description,
			      id.meta_title, 
			      id.meta_description,
			      id.meta_keyword, 
			      i.bottom
			     FROM " . DB_PREFIX . "information i 
			     LEFT JOIN " . DB_PREFIX . "information_description id 
			     ON (i.information_id = id.information_id) 
			     LEFT JOIN " . DB_PREFIX . "information_to_store i2s 
			     ON (i.information_id = i2s.information_id) 
			     WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}
	/**
	 *
	 */
	public function setProductTypeSet(){
		$q = "SELECT product_id
			  FROM ".DB_PREFIX."product_filter pf
			  WHERE pf.filter_id=142";
		$query = $this->db->query($q);

		$query = $this->db->query($q);

		$results = $query->rows;

		$arr_single = array();
		foreach($results as $result){

			$arr_single[] = $result['product_id'];


		}

		$q = "SELECT product_id
			  FROM ".DB_PREFIX."product
			  WHERE product_id not in(".implode(',', $arr_single).")";

		$query = $this->db->query($q);

		$records = $query->rows;

		foreach($records as $record){

			$q = "INSERT INTO ".DB_PREFIX."product_filter
				  SET product_id =". $record['product_id'] .",
				  filter_id = 145;";

			echo $q;
			echo '<br />';
		}

		//echo '<pre>'; print_r($arr_single);
	}
	public function getpages($store_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "seller_store_pages WHERE store_id = '". $store_id ."'";
		$query = $this->db->query($sql);
		$records = $query->rows;
		return $records;
	}
	public function addpages($store_id, $content, $page_title, $status){
		$sql = "INSERT INTO " . DB_PREFIX . "seller_store_pages SET store_id = '" . (int)$store_id . "', content = '". $content ."', title = '". $page_title ."', status = '" . $status . "', created = NOW()";
		$this->db->query($sql);
	}
	public function editpages($data_id, $store_id, $content, $page_title, $status){

		$sql = "UPDATE " . DB_PREFIX . "seller_store_pages SET store_id = '" . (int)$store_id . "', content = '". $content ."', title = '". $page_title ."', status = '" . $status . "', modify = NOW() WHERE id = '" . (int)$data_id . "'";
		$this->db->query($sql);
	}

	public function deletePages($data_id, $store_id){
		$sql = "DELETE FROM ". DB_PREFIX . "seller_store_pages WHERE store_id = '".$store_id."' AND id = '".$data_id."'";
		//echo $sql; die;
		$this->db->query($sql);
	}

	public function addsellerdetail($data =array()){
		$seller_id		=	$data[0]['seller_id'];
		$email 			= 	$data[0]['email'];
		$alt_email 		=	$data[0]['alt_email'];
		$landline_no 	=	$data[0]['landline_no'];
		$mobile_no 		=	$data[0]['mobile_no'];

		$alt_mobile_no 	=	$data[0]['alt_mobile_no'];

		$seller_update = "UPDATE " . DB_PREFIX . "ms_seller
		 					SET email = '" . $email . "', alternate_email = '" . $alt_email . "', landline_no = '" . $landline_no . "', mobile_no = '" . $mobile_no . "', whatsapp_no = '" . $whatsapp_no . "', alternatemobile_no = '" . $alt_mobile_no . "'
		 					WHERE seller_id = '" . (int)$seller_id . "'";
		$update_customer = "UPDATE " . DB_PREFIX . "customer
		 					SET email = '" . $email . "', telephone= '" . $mobile_no . "' 
		 					WHERE customer_id = '" . (int)$seller_id . "'";

		$this->db->query($seller_update);
		$this->db->query($update_customer);
		
		// add seller's Alternate Email in cutomer's additional email
		$sqlCheckEmailExsist= "SELECT * FROM ". DB_PREFIX ."customer_additional_email WHERE email = '".$alt_email."'  LIMIT 1";
		$rsEmailCheck = $this->db->query($sqlCheckEmailExsist);
		if($rsEmailCheck->num_rows == 0){
			$addAdditinalEmail = "INSERT INTO ". DB_PREFIX ."customer_additional_email (customer_id, email) values(" . (int)$seller_id . ", '" . $alt_email . "')";
			$this->db->query($addAdditinalEmail);
		}
	}

	/*
	public function getdetails($seller_id){
		$sql = "SELECT email, alternate_email, landline_no, mobile_no, whatsapp_no, alternatemobile_no FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '". $seller_id ."'";
		$query = $this->db->query($sql);
		$records = $query->row;
		return $records;
	}*/ /*--- commonted by viaks for get seller details from customer (02-05-2016) ---*/

	//get seller details from customer with ms_seller table by vikas (02-05-2016)
	public function getSellerDetails($seller_id){
		$sql = "SELECT c.email, c.telephone, ms.whatsapp_no, ms.alternate_email, ms.landline_no, ms.alternatemobile_no
				FROM " . DB_PREFIX . "customer c
				LEFT JOIN " . DB_PREFIX . "ms_seller ms
				ON(c.customer_id = ms.seller_id)
				WHERE c.customer_id = '". $seller_id ."'";
		$query = $this->db->query($sql);
		$records = $query->row;
		return $records;
	}

	public function getpage_themes(){
		$sql = "SELECT * FROM " . DB_PREFIX . "themes";
		$query = $this->db->query($sql);
		$records = $query->rows;
		return $records;
	}
	public function getcategory(){
		$sql = "SELECT cd.category_id,cd.name,c.parent_id FROM `oc_category_description` as cd LEFT JOIN oc_category as c ON c.category_id = cd.category_id WHERE c.parent_id= 0 AND c.status = 1";
		$query = $this->db->query($sql);
		return $records = $query->rows;
	}
	public function updateSellerWebsitesDetail($store_id, $seller_detail){
		foreach ($seller_detail as $value) {
			$sql = "SELECT * FROM ". DB_PREFIX . "setting WHERE store_id = '".$store_id."' AND code = '".$value['code']."' AND `key` = '".$value['key']."'";
			$query = $this->db->query($sql);
			$sellervalue = $query->row;
			if(!empty($sellervalue)){
				$sql = "UPDATE " . DB_PREFIX . "setting SET store_id = '".$store_id."', code = '".$value['code']."', `key` = '".$value['key']."', value = '".$value['value']."' WHERE store_id = '" . (int)$store_id . "' AND code = '".$value['code']."' AND `key` = '".$value['key']."'";
				$query = $this->db->query($sql);
			}else{
				 $sql = "INSERT INTO " . DB_PREFIX . "setting SET store_id = '".$store_id."', code = '".$value['code']."', `key` = '".$value['key']."', value = '".$value['value']."', serialized = '0'";
				$query = $this->db->query($sql);
			}

		}
		echo $data = "success";
	}
	public function updateSellerCategory($store_id, $category){
		$category_id = array();
		$category_id = explode(",",$category);

		$sql = "DELETE FROM ". DB_PREFIX . "category_to_store WHERE store_id = '".$store_id."'";
		$query = $this->db->query($sql);

		foreach($category_id as $value) {
			$sql = "INSERT INTO " . DB_PREFIX . "category_to_store SET store_id = '".$store_id."', category_id = '".$value."'";
			$query = $this->db->query($sql);
		}
		echo $data = "success";
	}
	public function getSellerCategory($store_id){
		$sql = "SELECT category_id FROM ". DB_PREFIX . "category_to_store WHERE store_id = '".$store_id."'";
			$query = $this->db->query($sql);
			$records = $query->rows;
			return $records;
	}

	public function getpage($page_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "seller_store_pages
				WHERE page_id = '". $page_id ."'";
		$query = $this->db->query($sql);
		return $query->row;

	}

	public function getSellerStoreCategory($seller_store){
		$sql = "SELECT DISTINCT cd.category_id, cd.name, c.parent_id, cd.language_id FROM `oc_category_description` as cd INNER JOIN oc_category_to_store as cts ON cts.category_id = cd.category_id INNER JOIN oc_category as c on c.category_id = cd.category_id WHERE cts.store_id IN (" . $seller_store . ")";
		$query = $this->db->query($sql);
		return $records = $query->rows;
	}

	public function getSellerProductCategory($product_id){
		$sql = "SELECT ptc.category_id, c.parent_id FROM `oc_product_to_category` as ptc INNER JOIN oc_category as c ON c.category_id = ptc.category_id WHERE product_id = '". $product_id ."'";
		$query = $this->db->query($sql);
		return $records = $query->rows;
	}
}
