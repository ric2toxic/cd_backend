<?php
class ModelSellerCustomer extends Model {
	public function addCustomer($data) {

		if(isset($data['customer_credit_allowed']) && !empty($data['customer_credit_allowed'])){
			$is_credit_allowed = $data['customer_credit_allowed'];
		}else{
			$is_credit_allowed = 0;
		}
		if(isset($data['customer_max_credit_limit']) && !empty($data['customer_max_credit_limit'])){
			$customer_max_credit_limit = $data['customer_max_credit_limit'];
		}else{
			$customer_max_credit_limit = 0;
		}
		if(isset($data['customer_credit_limit_consumed']) && !empty($data['customer_credit_limit_consumed'])){
			$customer_credit_limit_consumed = $data['customer_credit_limit_consumed'];
		}else{
			$customer_credit_limit_consumed = 0;
		}




		// Hard coding dropshipper to 1
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET  
			firstname = '" . $this->db->escape($data['firstname']) . "', 
			lastname = '" . $this->db->escape($data['lastname']) . "', 
			email = '" . $this->db->escape($data['email']) . "', 
			telephone = '" . $this->db->escape($data['telephone']) . "',
			salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', 
			password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "',
			is_dropshipper = '" . $this->db->escape(0) ."',
			date_added = NOW(),
			is_credit_allowed = '".(int)$is_credit_allowed ."',
			max_credit_limit = '".$customer_max_credit_limit."',
			credit_limit_consumed ='".$customer_credit_limit_consumed."',
			credit_limit_reset_date = NOW()
			");

		 $customer_id = $this->db->getLastId();

		// update master id when new customer created account
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET master_id = '" .(int)$customer_id . "' WHERE customer_id = '" .(int)$customer_id . "'");

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
		}
	}

	public function editCustomer($customer_id, $data) {

		if(isset($data['customer_credit_allowed']) && !empty($data['customer_credit_allowed'])){
			$is_credit_allowed = $data['customer_credit_allowed'];
		}else{
			$is_credit_allowed = 0;
		}
		if(isset($data['customer_max_credit_limit']) && !empty($data['customer_max_credit_limit'])){
			$customer_max_credit_limit = $data['customer_max_credit_limit'];
		}else{
			$customer_max_credit_limit = 0;
		}
		if(isset($data['customer_credit_limit_consumed']) && !empty($data['customer_credit_limit_consumed'])){
			$customer_credit_limit_consumed = $data['customer_credit_limit_consumed'];
		}else{
			$customer_credit_limit_consumed = 0;
		}

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET  
			firstname = '" . $this->db->escape($data['firstname']) . "', 
			lastname = '" . $this->db->escape($data['lastname']) . "', 
			email = '" . $this->db->escape($data['email']) . "', 
			telephone = '" . $this->db->escape($data['telephone']) . "', 
			is_dropshipper = '" . (int)$data['is_dropshipper'] . "', 
			is_credit_allowed = '".(int)$is_credit_allowed ."',
			max_credit_limit = '".$customer_max_credit_limit."',
			credit_limit_consumed ='".$customer_credit_limit_consumed."',
			credit_limit_reset_date = NOW()
			WHERE customer_id = '" . (int)$customer_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {

				$this->db->query("INSERT INTO " . DB_PREFIX . "address SET address_id = '" . (int)$address['address_id'] . "', customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
		}
	}

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_access_token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteCustomer($customer_id) {
        // Delete seller account when customer deleted
        // Taken from vqmod xml file of multimerch
        $this->MsLoader->MsSeller->deleteSeller($customer_id);
        
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getCustomer($customer_id) {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, 
		NULL as last_login FROM " . DB_PREFIX . "customer c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}

        if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
		}

		if (isset($data['filter_is_dropshipper']) && !is_null($data['filter_is_dropshipper'])) {
			$implode[] = "c.is_dropshipper IN (" . $data['filter_is_dropshipper'] . ")";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}		



		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'c.credit_limit_consumed',
            'c.telephone',
			'c.date_added',
			'c.is_dropshipper',
            'last_login',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

	/**
	 * this function is to filter customer by name or mobile or email
	 */

	public function getCustomersByNameMobileEmail($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM " . DB_PREFIX . "customer c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_all'])) {
			$implodes[] = "c.email LIKE '%" . $this->db->escape($data['filter_all']) . "%'" OR "c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'" OR "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_all']) . "%'";
		}

		if (!empty($data['filter_telephone'])) {
			$implode[] = "c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
		}

	/*	if (isset($data['filter_is_dropshipper']) && !is_null($data['filter_is_dropshipper'])) {
			$implode[] = "c.is_dropshipper IN (" . $data['filter_is_dropshipper'] . ")";
		}*/


		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}



		if ($implode) {
			$sql .= " And " . implode(" OR ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'c.telephone',
			'c.date_added',
			//'c.is_dropshipper'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

	//	echo $sql; die;
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			return array(
				'address_id'     => $address_query->row['address_id'],
				'customer_id'    => $address_query->row['customer_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}
	}

	public function getAddresses($customer_id) {

		$address_data = array();

		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		foreach ($query->rows as $result) {
			$address_info = $this->getAddress($result['address_id']);

			if ($address_info) {
				$address_data[$result['address_id']] = $address_info;
			}
		}

		return $address_data;
	}

	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}
        
        if (!empty($data['filter_telephone'])) {
			$implode[] = "telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_is_dropshipper']) && !is_null($data['filter_is_dropshipper'])) {
			$implode[] = "is_dropshipper IN (" . $data['filter_is_dropshipper'] . ")";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getTotalAddressesByCustomerId($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function addHistory($customer_id, $comment) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET customer_id = '" . (int)$customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getHistories($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT comment, date_added FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalHistories($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));

			$mail = new PHPMailer();
			// $mail->protocol = $this->config->get('config_mail_protocol');
			// $mail->parameter = $this->config->get('config_mail_parameter');
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->isSMTP();
			// $mail->SMTPDebug = 2;
			// $mail->Debugoutput = 'html';

			$mail->addAddress($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			// $mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->Subject = sprintf($this->language->get('text_transaction_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->Body = $message;
			$mail->send();
		}
	}

	public function deleteTransaction($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();
		}
	}

	public function deleteReward($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");
	}

	public function getRewards($customer_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}

	public function getisdropshipper($customer_id) {
	   	$query = $this->db->query("SELECT is_dropshipper FROM `" . DB_PREFIX . "customer` WHERE `customer_id` = '" . $customer_id . "'");
		return $query->row['is_dropshipper'];
	}

	public function getcategories() {
		$sql = "SELECT cd.category_id,cd.name,c.parent_id FROM `oc_category_description` as cd LEFT JOIN oc_category as c ON c.category_id = cd.category_id WHERE c.parent_id= 0 AND c.status = 1";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function customer_preferences(){

		if(isset($this->request->get['customer_id'])){
			$customer_id = $this->request->get['customer_id'];
		}
		
		if(isset($this->request->post['quality'])){
			$quality = array();
			foreach ($this->request->post['quality'] as $value) {
				$quality[] = $value ;
			}
			$qualitys = implode(',', $quality);
		}else{
			$qualitys = '';

		}

		if(isset($this->request->post['category'])){
			
			foreach ($this->request->post['category'] as $value) {
				if(!empty($value['price-min'])){
					$price_min = $value['price-min'];
				}
				if(!empty($value['price-max'])){
					$price_max = $value['price-max'];
				}

				if(isset($value['category_id'])){
					if(!empty($value['category_id'])){
						$category_id = $value['category_id'];

						if(!empty($value['filter'])){
							$fil = $value['filter'];
							$filter = array();
							foreach ($fil as $value) {
								$filter[] = $value ;
							}
							$filters = implode(',', $filter);
						} 

						$sql = "SELECT customer_id,category_id FROM ".DB_PREFIX."customer_preference WHERE customer_id = '".$customer_id."' AND category_id= '".$category_id."'";
						$query_data = $this->db->query($sql);
						$query_data = $query_data->row;
						if(!empty($query_data)){
							$sql = "UPDATE " . DB_PREFIX . "customer_preference SET customer_id = '".$customer_id."', category_id= '".$category_id."', filter_id= '".$filters."', min_price= '".$price_min."', max_price= '".$price_max."', modify= NOW() WHERE customer_id = '".$customer_id."'AND category_id= '".$category_id."'";
							$query = $this->db->query($sql);
						}else{
							$sql  = "INSERT INTO oc_customer_preference set customer_id = '".$customer_id."', category_id= '".$category_id."', filter_id= '".$filters."', min_price= '".$price_min."', max_price= '".$price_max."', created= NOW(), modify= NOW()";
							$query = $this->db->query($sql);
						}
				
					}
				}

			}
		}
	
	}

	public function get_customer_preferences($customer_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "customer_preference WHERE customer_id= '".$customer_id."'";
		$query = $this->db->query($sql);
		return $query->rows;	
	}

	public function get_customer_preferences_filter($customer_id,$category_id){
		$sql = "SELECT filter_id FROM " . DB_PREFIX . "customer_preference WHERE customer_id= '".$customer_id."' AND category_id= '".$category_id."'";
		$query = $this->db->query($sql);
		return $query->row;	
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
			/***
			 * Get Products According to seller (Quality Expectations)
			 ***/
			if (isset($data['seller']) && !empty($data['seller'])) {
				//$sql .= "INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
				$sql .= "INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
				//$sql .= " FROM " . DB_PREFIX ."ms_product mp INNER JOIN " . DB_PREFIX . "product p ON p.product_id = mp.product_id";
			}

		} else {
			//$sql .= " FROM " . DB_PREFIX . "product p";
			/***
			 * Get Products According to seller (Quality Expectations)
			 ***/
			if (isset($data['seller']) && !empty($data['seller'])) {
				//$sql .= " FROM " . DB_PREFIX ."ms_product mp INNER JOIN " . DB_PREFIX . "product p ON p.product_id = mp.product_id";
				$sql .= "INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
			}else{
				$sql .= " FROM " . DB_PREFIX . "product p";
			}
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		/***
		 * Get Products According to seller (Quality Expectations)
		 ***/
		if (isset($data['seller']) && !empty($data['seller'])) {
			$sql .= " AND mp.seller_id IN(".$data['seller'].")";
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}



	// update Credit limit consumed value by ajax by vikas (15-07-2016)
	public function updateCreditLimitConsumed($customer_id){
		$this->db->query("UPDATE oc_customer SET credit_limit_consumed = 0  WHERE customer_id = ".$customer_id);
	}
}
