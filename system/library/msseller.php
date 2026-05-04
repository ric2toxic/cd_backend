<?php
final class MsSeller extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	const STATUS_UNPAID = 5;
	const STATUS_INCOMPLETE = 6;

	const MS_SELLER_VALIDATION_NONE = 1;
	const MS_SELLER_VALIDATION_ACTIVATION = 2;
	const MS_SELLER_VALIDATION_APPROVAL = 3;

	private $isSeller = FALSE;
	private $seller_id= 0;
	private $nickname;
	private $description;
	private $company;
	private $country_id;
	private $avatar;
	private $seller_status;
	private $paypal;
	private $seller_approved;

  	public function __construct($registry) {
  		parent::__construct($registry);

  		//$this->log->write('creating seller object: ' . $this->session->data['customer_id']);
		//if (isset($this->session->data['customer_id'])) {
  		if( is_object($this->customer) && !empty($this->customer->getId()) )
  	    {		
			//TODO
			//$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->session->data['customer_id'] . "' AND seller_status = '1'");
			$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->customer->getId() . "'");

			if ($seller_query->num_rows) {
				$this->isSeller = TRUE;
				$this->nickname = $seller_query->row['nickname'];
				$this->seller_id = $seller_query->row['seller_id'];
				$this->description = $seller_query->row['seller_description'];
				$this->company = $seller_query->row['company'];
				$this->country_id = $seller_query->row['country_id'];
				$this->avatar = $seller_query->row['avatar'];
				$this->seller_status = $seller_query->row['seller_status'];
				$this->paypal = $seller_query->row['paypal'];
				$this->seller_approved = $seller_query->row['seller_approved'];
			}
  		}
	}

	private function _dupeSlug($slug) {
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($slug) . "%'");
			return ($similarity_query->num_rows > 0) ? $slug . $similarity_query->num_rows : $slug;
	}

  	public function isCustomerSeller($customer_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$customer_id;

		$res = $this->db->query($sql);

		if ($res->row['total'] == 0)
			return FALSE;
		else
			return TRUE;
  	}

	public function getSellerName($seller_id) {
		$sql = "SELECT firstname as 'firstname'
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		return $res->row['firstname'];
	}

	public function getSellerEmail($seller_id) {
		$sql = "SELECT email as 'email'
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		return $res->row['email'];
	}

	public function createSeller($data) {

		$avatar = isset($data['avatar_name']) ? $this->MsLoader->MsFile->moveImage($data['avatar_name']) : '';
		$banner = isset($data['banner_name']) ? $this->MsLoader->MsFile->moveImage($data['banner_name']) : '';

		if (isset($data['commission']))
			$commission_id = 0;
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller
				SET seller_id = " . (int)$data['seller_id'] . ",
					seller_status = " . (isset($data['status']) ? (int)$data['status'] : self::STATUS_INACTIVE) . ",
					seller_approved = " . (isset($data['approved']) ? (int)$data['approved'] : 0) . ",
					seller_group = " .  (isset($data['seller_group']) ? (int)$data['seller_group'] : $this->config->get('msconf_default_seller_group_id'))  .  ",
					nickname = '" . (int)$data['seller_id'] . "',
					seller_description = '" . $this->db->escape(isset($data['description']) ? $data['description'] : '') . "',
					company = '" . $this->db->escape(isset($data['company']) ? $data['company'] : '') . "',
					country_id = " . (isset($data['country']) ? (int)$data['country'] : 0) . ",
					zone_id = " . (isset($data['zone']) ? (int)$data['zone'] : 0) . ",
					commission_id = " . (isset($commission_id) ? $commission_id : 'NULL') . ",
					product_validation = " . (isset($data['product_validation']) ? (int)$data['product_validation'] : 0) . ",
					paypal = '" . $this->db->escape(isset($data['paypal']) ? $data['paypal'] : '') . "',
					avatar = '" . $this->db->escape($avatar) . "',
					banner = '" . $this->db->escape($banner) . "', 
					email = '" . $this->db->escape(isset($data['reg_email']) ? $data['reg_email'] : '') . "', 
					mobile_no = '" . $this->db->escape(isset($data['reg_telephone']) ? $data['reg_telephone'] : '') . "', 
					date_created = NOW()";
		
		$this->db->query($sql);		
		$seller_id = $this->db->getLastId();

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($this->_dupeSlug($data['keyword'])) . "'");
		}
	}
	
	
	public function getTotalSellersByTelephone($mobile_no) {
		
		$q = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ms_seller WHERE LOWER(mobile_no) = '" . $this->db->escape(utf8_strtolower($mobile_no)) . "' ";
		$query = $this->db->query($q);
		return $query->row['total'];
	}
	
	
	public function getTotalSellersByEmail($email) {
		
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ms_seller WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		return $query->row['total'];
	}

	public function nicknameTaken($nickname) {
		$sql = "SELECT nickname
				FROM `" . DB_PREFIX . "ms_seller` p
				WHERE p.nickname = '" . $this->db->escape($nickname) . "'";

		$res = $this->db->query($sql);

		return $res->num_rows;
	}

	public function editSeller($data) {

		$seller_id = (int)$data['seller_id'];

		$old_avatar = $this->getSellerAvatar($seller_id);

		if (!isset($data['avatar_name']) || ($old_avatar['avatar'] != $data['avatar_name'])) {
			$this->MsLoader->MsFile->deleteImage($old_avatar['avatar']);
		}

		if (isset($data['avatar_name'])) {
			if ($old_avatar['avatar'] != $data['avatar_name']) {
				$avatar = $this->MsLoader->MsFile->moveImage($data['avatar_name']);
			} else {
				$avatar = $old_avatar['avatar'];
			}
		} else {
			$avatar = '';
		}

		$old_banner = $this->getSellerBanner($seller_id);

		if (!isset($data['banner_name']) || ($old_banner['banner'] != $data['banner_name'])) {
			$this->MsLoader->MsFile->deleteImage($old_banner['banner']);
		}

		if (isset($data['banner_name'])) {
			if ($old_banner['banner'] != $data['banner_name']) {
				$banner = $this->MsLoader->MsFile->moveImage($data['banner_name']);
			} else {
				$banner = $old_banner['banner'];
			}
		} else {
			$banner = '';
		}

		if($data['business_or_bank'] == 0){
			$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET seller_description = '" . $this->db->escape($data['description']) . "',
					company = '" . $this->db->escape($data['company']) . "',
					nickname = '" . $this->db->escape($data['nickname']) . "',
					country_id = " . (int)$data['country'] . ",
					zone_id = " . (int)$data['zone'] . ","
					. (isset($data['status']) ? "seller_status=  " .  (int)$data['status'] . "," : '')
					. (isset($data['approved']) ? "seller_approved=  " .  (int)$data['approved'] . "," : '')
					. "paypal = '" . $this->db->escape($data['paypal']) . "',
					banner = '" . $this->db->escape($banner) . "',
					avatar = '" . $this->db->escape($avatar) . "',
					address1 = '" . $this->db->escape($data['address1']) . "',
					address2 = '" . $this->db->escape($data['address2']) . "',
					pincode = '" . $this->db->escape($data['pincode']) . "',
					city = '" . $this->db->escape($data['city']) . "',
					pan = '" . $this->db->escape($data['pan']) . "',
					tin = '" . $this->db->escape($data['tin']) . "',
					tan = '" . $this->db->escape($data['tan']) . "'
				WHERE seller_id = " . (int)$seller_id;
		}
		if($data['business_or_bank'] == 1){
			$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET bank_ac_holder_name = '" . $this->db->escape($data['bank_ac_holder_name']) . "',
					bank_ac_number = '" . $this->db->escape($data['bank_ac_number']) . "',
					ifsc_code = '" . $this->db->escape($data['ifsc_code']) . "',
					bank_name = '" . $this->db->escape($data['bank_name']) . "',
					bank_state = '" . $this->db->escape($data['bank_state']) . "',
					bank_city = '" . $this->db->escape($data['bank_city']) . "',
					bank_branch = '" . $this->db->escape($data['bank_branch']) . "'
				WHERE seller_id = " . (int)$seller_id;
		}

		$this->db->query($sql);

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seller_id=" . (int)$seller_id. "'");
		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($this->_dupeSlug($data['keyword'])) . "'");
		}
	}

	public function getSellerAvatar($seller_id) {
		$query = $this->db->query("SELECT avatar as avatar FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");

		return $query->row;
	}

	public function getSellerBanner($seller_id) {
		$query = $this->db->query("SELECT banner as banner FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");

		return $query->row;
	}

  	public function getNickname() {
  		return $this->nickname;
  	}

  	public function getCompany() {
  		return $this->company;
  	}

  	public function getCountryId() {
  		return $this->country_id;
  	}

  	public function getDescription() {
  		return $this->description;
  	}

  	public function getStatus() {
  		return $this->seller_status;
  	}

  	public function getPaypal() {
  		return $this->paypal;
  	}

  	public function isSeller() {
  		return $this->isSeller;
  	}

  	public function getSellerID() {
  		return $this->seller_id;
  	}
  	
	public function sellerApproval() {
		return $this->seller_approved;
	}

	public function getSalesForSeller($seller_id) {
		$sql = "SELECT 0 as total
				FROM `" . DB_PREFIX . "ms_product`
				WHERE seller_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getSalt($seller_id) {
		$sql = "SELECT salt
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		return $res->row['salt'];
	}


    /**
     * @param $data
     */
    public function adminEditSeller($data) {
		$seller_id = (int)$data['seller_id'];

		$commission_id = 0;

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seller_id=" . (int)$seller_id. "'");

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($this->_dupeSlug($data['keyword'])) . "'");
		}
		$nickname = '';
		if(isset($data['nickname']) && $data['nickname'] != ''){
            $nickname = $this->db->escape($data['nickname']);
		}
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET seller_description = '" . $this->db->escape($data['description']) . "',
					nickname = '" . $nickname . "',
					company = '" . $this->db->escape($data['company']) . "',
					country_id = " . (int)$data['country'] . ",
					paypal = '" . $this->db->escape($data['paypal']) . "',
					seller_status = '" .  (int)$data['status'] .  "',
					zone_id = " . (int)$data['zone'] . ",
					seller_approved = '" .  (int)$data['approved'] .  "',
					product_validation = '" .  (int)$data['product_validation'] .  "',
					commission_id = " . (!is_null($commission_id) ? (int)$commission_id : 'NULL' ) . ",
					seller_group = '" .  (int)$data['seller_group'] .  "'
				WHERE seller_id = " . (int)$seller_id;

		$this->db->query($sql);
	}

	/********************************************************/


	public function getTotalSellers($data = array()) {
		$sql = "
			SELECT COUNT(*) as total
			FROM " . DB_PREFIX . "ms_seller ms
			WHERE 1 = 1 "
			. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getSeller($seller_id, $data = array()) {
		$sql = "SELECT	CONCAT(c.firstname, ' ', c.lastname) as name,
						c.email as 'c.email',
						c.telephone as 'c.telephone',
                        NULL as last_login,
						ms.seller_id as 'seller_id',
						ms.nickname as 'ms.nickname',
						ms.company as 'ms.company',
						ms.website as 'ms.website',
						ms.paypal as 'ms.paypal',
						ms.seller_status as 'ms.seller_status',
						ms.seller_approved as 'ms.seller_approved',
						ms.date_created as 'ms.date_created',
						ms.product_validation as 'ms.product_validation',
						ms.avatar as 'ms.avatar',
						ms.banner as 'banner',
						ms.country_id as 'ms.country_id',
						ms.zone_id as 'ms.zone_id',
						ms.seller_description as 'ms.seller_description',
						ms.commission_id as 'ms.commission_id',
						ms.seller_group as 'ms.seller_group',
						ms.address1 as 'ms.address1',
						ms.address2 as 'ms.address2',
						ms.pincode as 'ms.pincode',
						ms.city as 'ms.city',
						ms.pan as 'ms.pan',
						ms.tan as 'ms.tan',
						ms.tin as 'ms.tin',
						ms.tan as 'ms.tan',
						ms.bank_ac_holder_name as 'ms.bank_ac_holder_name',
						ms.bank_ac_number as 'ms.bank_ac_number',
						ms.ifsc_code as 'ms.ifsc_code',
						ms.bank_name as 'ms.bank_name',
						ms.bank_state as 'ms.bank_state',
						ms.bank_city as 'ms.bank_city',
						ms.bank_branch as 'ms.bank_branch',
						ms.sor_enabled as 'ms.sor_enabled',
						ms.gst_provisional_id as 'ms.gst_provisional_id',
						0 as 'total_sales',
						(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE `query` = 'seller_id=" . (int)$seller_id . "' LIMIT 1) AS keyword
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				WHERE ms.seller_id = " .  (int)$seller_id
				. (isset($data['product_id']) ? " AND mp.product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				. " GROUP BY ms.seller_id
				LIMIT 1";

		$res = $this->db->query($sql);

		if (!isset($res->row['seller_id']) || !$res->row['seller_id'])
			return FALSE;
		else
			return $res->row;
	}

	public function getSellers($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`c.name`" => 1, "total_sales" => 1, "`ms.date_created`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS"
					// additional columns
					. (isset($cols['total_products']) ? "
						(SELECT COUNT(*) FROM " . DB_PREFIX . "product p
						LEFT JOIN " . DB_PREFIX . "ms_product mp USING (product_id)
						LEFT JOIN " . DB_PREFIX . "ms_seller USING (seller_id)
						WHERE seller_id = ms.seller_id) as total_products,
					" : "")

					// default columns
					." CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
					c.email as 'c.email',
					ms.seller_id as 'seller_id',
					ms.nickname as 'ms.nickname',
					ms.company as 'ms.company',
					ms.website as 'ms.website',
					ms.seller_status as 'ms.seller_status',
					ms.seller_approved as 'ms.seller_approved',
					ms.date_created as 'ms.date_created',
					ms.avatar as 'ms.avatar',
					ms.banner as 'banner',
					ms.country_id as 'ms.country_id',
					ms.zone_id as 'ms.zone_id',
					ms.seller_description as 'ms.seller_description',
					ms.paypal as 'ms.paypal',
					c.telephone as 'c.telephone',
                    NULL as last_login,
					0 as 'total_sales'
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')

				. $wFilters

				. " GROUP BY ms.seller_id HAVING 1 = 1 "

				. $hFilters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}

	public function getCustomers($sort = array()) {
		$sql = "SELECT  CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
						c.email as 'c.email',
						c.customer_id as 'c.customer_id',
						ms.seller_id as 'seller_id'
				FROM `" . DB_PREFIX . "customer` c
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				WHERE ms.seller_id IS NULL"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		return $res->rows;
	}

	public function changeStatus($seller_id, $seller_status) {
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET	seller_status =  " .  (int)$seller_status . "
				WHERE seller_id = " . (int)$seller_id;

		$res = $this->db->query($sql);
	}

	public function changeApproval($seller_id, $approved) {
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET	approved =  " .  (int)$approved . "
				WHERE seller_id = " . (int)$seller_id;

		$res = $this->db->query($sql);
	}

	public function deleteSeller($seller_id) {
		$products = $this->MsLoader->MsProduct->getProducts(array('seller_id' => $seller_id));

		foreach ($products as $product) {
			$this->MsLoader->MsProduct->changeStatus($product['product_id'], MsProduct::STATUS_DELETED);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE `query` = 'seller_id=".(int)$seller_id."'");
	}

	public function getTotalOrders($seller_id, $data = array()) {
		// note: update getSellers() if updating this
		$sql = "SELECT  COUNT(DISTINCT order_id, seller_id) as total FROM " . DB_PREFIX . "order_product  WHERE seller_id=".$seller_id ." GROUP BY seller_id";

		$res = $this->db->query($sql);
		if(isset($res->row['total'])){
            return $res->row['total'];
        }else{
            return 0;
        }

	}
	public function getTotalPiecesSold($seller_id, $data = array()) {
		// note: update getSellers() if updating this
		$sql = "SELECT  SUM(piece_in_set * quantity) as total FROM " . DB_PREFIX . "order_product  WHERE seller_id=".$seller_id ." GROUP BY seller_id";

		$res = $this->db->query($sql);
        if(isset($res->row['total'])) {
            return $res->row['total'];
        }else{
            return 0;
        }
	}

	public function getTotalSetSold($seller_id, $data = array()) {
		// note: update getSellers() if updating this
		$sql = "SELECT  SUM(quantity) as total FROM " . DB_PREFIX . "order_product  WHERE seller_id=".$seller_id ." GROUP BY seller_id";

		$res = $this->db->query($sql);
		return $res->row['total'];

	}

	public function getTotalSales($seller_id, $data = array()) {
		// note: update getSellers() if updating this
		$sql = "SELECT  SUM(transfer_price_per_piece * piece_in_set * quantity) as total FROM " . DB_PREFIX . "order_product  WHERE seller_id=".$seller_id ." GROUP BY seller_id";

		$res = $this->db->query($sql);
		//return $res->row['total'];
        if(isset($res->row['total'])){
            return $res->row['total'];
        }else{
            return 0;
        }

	}
	public function getTotalCompleteOrders($value=''){

		$sql = "SELECT COUNT(DISTINCT order_id, seller_id) as total FROM " . DB_PREFIX . "order_product op.order_id INNER JOIN " . DB_PREFIX."order o ON op.   =o.order_id   WHERE seller_id=".$seller_id ." GROUP BY seller_id";
	}

	/**
	 * Enable/disable SOR selling for a seller
	 * @param $seller_id
	 * @param $sor_setting
	 * @return array
	 */
	public function updateSorSetting($seller_id, $sor_setting){
		$response = array();
		if($seller_id > 0){
			 $sql = "UPDATE ". DB_PREFIX."ms_seller
					SET sor_enabled = ".$sor_setting."
					WHERE seller_id = ".$seller_id;
		$this->load->language('seller/manage-inventory');

			if($this->db->query($sql)){
				if($sor_setting == 1){
					$label = $this->language->get('ms_sor_disabled');
					$css = 'sor_disabled';
					$change_sor = 0;
				}else{
					$label = $this->language->get('ms_sor_enabled');
					$css = 'sor_enabled';
					$change_sor = 1;
				}
				$response = array('label'=>$label, 'css'=>$css, 'change_sor'=>$change_sor);

			}
			$solr = new SolrProduct($this);
			if($sor_setting == 1) {
				//Copy product to SOR
				$solr->copySellerProductToSor($seller_id);
			}else{
				//Remove product to SOR
				$solr->removeSellerProductToSor($seller_id);
			}

		}
		return $response;

	}

	public function checkIfSellerHasSOR($seller_id){
		$sor_enabled = 0;
		if($seller_id > 0){
			$sql = " SELECT sor_enabled
 					 FROM ". DB_PREFIX."ms_seller
 					 WHERE seller_id = ". $seller_id;
			$query = $this->db->query($sql);

			if(isset($query->row['sor_enabled'])){
				$sor_enabled = (int)$query->row['sor_enabled'];
			}
		}

		return $sor_enabled;
	}

	public function getSellerAgreementStatus($seller_id) {
		$sql = "SELECT seller_agreement
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		return $res->row['seller_agreement'];
	}


	/**
	 * Function checks if an invoice is already generated by the seller for an order
	 * @param $order_id : Integer for the order id
	 * @param $suborder_id : String for the suborder id
	 * @param $seller_id : Integer for the seller id
	 * @return True, if invoice no is generated else false
	 * @author Vikas Agarwal, 2016
	 */
	public function checkInvoiceIsGenerated($order_id, $suborder_id, $seller_id){

		$sql = "SELECT seller_invoice_no,
						seller_invoice_prefix
		        FROM " . DB_PREFIX . "seller_invoice
		        WHERE order_id = '".(int)$order_id."'
				  AND suborder_id = '" . $this->db->escape($suborder_id) . "'
		          AND seller_id = '" . (int)$seller_id . "'";
		
		$query = $this->db->query($sql);
		$result = false;

		if ( $query->num_rows and !empty($query->row['seller_invoice_no']) ) {
			$result = $query->row;
		}

		return $result;
	}
}

?>
