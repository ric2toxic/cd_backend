<?php
class ModelSettingSetting extends Model {
	public function getSetting($code, $store_id = 0) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = unserialize($result['value']);
			}
		}

        $this->cache->set('store_setting', $setting_data); 
		return $setting_data;
	}

	public function editSetting($code, $data, $store_id = 0) {
		
		$this->cache->delete('store_setting');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (!is_array($value)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
				}
			}
		}
		
	/*
		Saved Paychange setting data in separate tables
		MSA April 2019
	*/	
		if($code == 'paycharge') 
		{
			//admin change log for paycharge data
			$paycharge_data = $data['paycharge'] ?? array();
			$this->paycharge_admin_change_log($code, $paycharge_data, $store_id);
			// Delete old data from table - oc_paycharge_payment_method_rules
			$this->db->query("TRUNCATE `" . DB_PREFIX . "paycharge_payment_method_rules` ");
			// Delete old data from table - oc_paycharge_payment_method_rules_desc
			$this->db->query("TRUNCATE `" . DB_PREFIX . "paycharge_payment_method_rules_desc` ");
			
			$paycharge_data = $data['paycharge'];
			// save data in paycharge tables
			foreach ($paycharge_data as $key => $value) {
				//save paycharge rules 
				$this->db->query("INSERT INTO " . DB_PREFIX . "paycharge_payment_method_rules 
									SET 
										`payment_method`= '" . $this->db->escape($value['payment_method']) . "',
										`valuep` 		= '" . $this->db->escape($value['valuep']) . "', 
										`amount` 		= '" . $this->db->escape($value['amount']) . "'"
								);
				$rules_id = $this->db->getLastId();
				if($rules_id) {
					foreach ($value['description'] as $language_id => $description) {

						$this->db->query("INSERT INTO " . DB_PREFIX . "paycharge_payment_method_rules_desc 
											SET 
											`rules_id` 	= '".(int)$rules_id."',
											`language_id` 	= '".(int)$language_id."',
											`name`		= '" . $this->db->escape($description['name']) . "'
										");
					}
				}
			}
		}

		if($code == "paytm")
        {
            $rs = $this->db->query("SELECT `extension_id` FROM ".DB_PREFIX."extension  WHERE code = '".$code ."' ");
            if($rs->num_rows > 0)
            {
                //delete prevoius entry for paytm setting
                $this->db->query('DELETE FROM '.DB_PREFIX."wsb_extension_to_store WHERE extension_id=".(int)$rs->row['extension_id']);
                // insert new setting
                $this->db->query("INSERT INTO ".DB_PREFIX."wsb_extension_to_store SET extension_id = ".(int)$rs->row['extension_id'].", store_id = ".(int)$store_id." ");
            }
        }

		//Set payment methods for store

		if(isset($data['checkbox_payment_methods']) && !empty($data['checkbox_payment_methods'])){
			$q = 'DELETE FROM '.DB_PREFIX."wsb_extension_to_store WHERE store_id=".(int)$store_id;
			$this->db->query($q);
			foreach($data['checkbox_payment_methods'] as $extension_id) {
				$q = "INSERT INTO " . DB_PREFIX . "wsb_extension_to_store" .
						" SET extension_id =".(int)$extension_id .",".
						"     store_id =".(int)$store_id;

				$this->db->query($q);
			}

		}
	}

	public function deleteSetting($code, $store_id = 0) {
		$this->cache->delete('store_setting');
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
	}

	public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) {
		$this->cache->delete('store_setting');
		if (!is_array($value)) {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0'  WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		}
	}

	public function addCategoriesToStore($store_id, $category_ids = array()){
		//echo $store_id;
		//echo "<pre>"; print_r($category_ids); exit;
		$q = 'DELETE FROM '.DB_PREFIX."category_to_store WHERE store_id=".(int)$store_id;
		$this->db->query($q);
		foreach($category_ids as $category_id){
			$q = "INSERT INTO " . DB_PREFIX . "category_to_store" .
				" SET category_id =".(int)$category_id .",".
				"     store_id =".(int)$store_id;
			$this->db->query($q);
		}
	}

	public function getCategoriesToStore($store_id){
		$q = 'SELECT category_id FROM '.DB_PREFIX."category_to_store WHERE store_id=".(int)$store_id;
		$category = $this->db->query($q);
		//echo "<pre>"; print_r($category->rows); exit;
		return $category->rows;
	}

	public function getPaycharges($payment_method = '')
	{
		$where_condition = "";
		if(!empty($payment_method)) {
			$where_condition = " WHERE payment_method = '".$this->db->escape($payment_method)."' ";
		}
		$sql = "
				SELECT
					id,
					payment_method,
					valuep,
					amount
				FROM 
					".DB_PREFIX."paycharge_payment_method_rules AS rules
				" . $where_condition;

		$result = $this->db->query($sql);
		$data = array();
		if($result->num_rows) {
			foreach ($result->rows as $key => $value) {
				$data[$key] = $value;
				$data[$key]['description'] = $this->getPaychargeDescription($value['id']);
			}
		}
		return $data;
	}

	public function getPaychargeDescription(int $rules_id){
		$sql = "
				SELECT
					rules_id,
					language_id,
					name
				FROM 
					".DB_PREFIX."paycharge_payment_method_rules_desc
				WHERE
					rules_id = '".(int)$rules_id."'
				";
		$result = $this->db->query($sql);
		$descriptions = array();
		if($result->num_rows){
			foreach ($result->rows as $key => $value) {
				$descriptions[$value['language_id']]['name'] = $value['name'];
			}
		}
		return 	$descriptions;
	}

	public function paycharge_admin_change_log(string $code, array $data, int $store_id)
	{
		if(!empty($data)) {

			foreach ($data as $key => $value) {
				$payment_method = $value['payment_method'] ?? '';
				$old_data = $this->getPaycharges($payment_method);
				//payment method log
				if(!empty($old_data['payment_method']) && $old_data['payment_method'] !=$value['payment_method'])
				{
					$log_data = array(
						'table_id' => $old_data[0]['id'] ?? 0,
						'user_id'  => $this->user->getId(),
						'name'     => 'editSetting',
						'username' => $this->user->getUserName()['username'],
						'table_name'=> 'oc_paycharge_payment_method_rules',
						'source_field' => 'order_totals',
						'field_name' => 'payment_method',
						'ref_url'	=> 'index.php?route=total/paycharge',
						'old_value' => $old_data[0]['payment_method'] ?? '',
						'new_value' => $value['payment_method'],
						'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						'ip_address' => $this->request->getIpAddress,
						'file_location' => 'index.php?route=total/paycharge',
						'user_type'  => $this->user->getGroupName(),
						'comment'	=> 'Updated Paychange Setting'
					);
	       			CommonLib::addAdminChangeLog($this->db, $log_data);
       			}
       			if(!empty($old_data['valuep']) && $old_data['valuep'] !=$value['valuep'])
				{
	       			//valuep log
					$log_data = array(
						'table_id' => $old_data[0]['id'] ?? 0,
						'user_id'  => $this->user->getId(),
						'name'     => 'editSetting',
						'username' => $this->user->getUserName()['username'],
						'table_name'=> 'oc_paycharge_payment_method_rules',
						'source_field' => 'order_totals',
						'field_name' => 'valuep',
						'ref_url'	=> 'index.php?route=total/paycharge',
						'old_value' => $old_data[0]['valuep'] ?? '',
						'new_value' => $value['valuep'],
						'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						'ip_address' => $this->request->getIpAddress,
						'file_location' => 'index.php?route=total/paycharge',
						'user_type'  => $this->user->getGroupName(),
						'comment'	=> 'Updated Paychange Setting'
					);
					CommonLib::addAdminChangeLog($this->db, $log_data);	
				}
       			if( !empty($old_data['valuep']) && $old_data['valuep'] !=$value['valuep'])
				{
	       			//amount log
					$log_data = array(
						'table_id' => $old_data[0]['id'] ?? 0,
						'user_id'  => $this->user->getId(),
						'name'     => 'editSetting',
						'username' => $this->user->getUserName()['username'],
						'table_name'=> 'oc_paycharge_payment_method_rules',
						'source_field' => 'order_totals',
						'field_name' => 'amount',
						'ref_url'	=> 'index.php?route=total/paycharge',
						'old_value' => $old_data[0]['amount'] ?? '',
						'new_value' => $value['amount'],
						'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						'ip_address' => $this->request->getIpAddress,
						'file_location' => 'index.php?route=total/paycharge',
						'user_type'  => $this->user->getGroupName(),
						'comment'	=> 'Updated Paychange Setting'
					);
	       			CommonLib::addAdminChangeLog($this->db, $log_data);
       			}
			}
		}
	} // end of method

	
	public function isPaychargeExceptionExists(string $type, int $type_id): int
	{
		$sql = "SELECT `id`
				FROM ".DB_PREFIX."paycharge_exception
				WHERE
					exception_type = '".$this->db->escape($type)."'
					AND
					type_id  = '".(int)$type_id."'
				";
		$result = $this->db->query($sql);
		if($result->num_rows){
			return $result->row['id'];
		}
		return 0;
	}

	public function getApplicableOnMembershipDiscountStatus(string $type, int $type_id): int
	{
		$sql = "SELECT `applicable_on_membership_discount`
				FROM ".DB_PREFIX."paycharge_exception
				WHERE
					exception_type = '".$this->db->escape($type)."'
					AND
					type_id  = '".(int)$type_id."'
				";
		$result = $this->db->query($sql);
		if($result->num_rows){
			return $result->row['applicable_on_membership_discount'];
		}
		return 0;
	}

	public function addException(string $type, int $type_id, int $applicable):void
	{
		$isExists = $this->isPaychargeExceptionExists($type, $type_id);
		if(!$isExists){
			$sql = "INSERT INTO ".DB_PREFIX."paycharge_exception
					SET
						exception_type 	= '".$this->db->escape($type)."',
						type_id 		= '".(int)$type_id."',
						date_added      = NOW(),
						user_id         = '".$this->user->getId()."',
						applicable_on_membership_discount = '".(int)$applicable."'
					";
			$this->db->query($sql);
			$paycharge_exception_id = $this->db->getLastId();
			// save log data
			$log_data = array(
				'table_id' => $paycharge_exception_id,
				'user_id'  => $this->user->getId(),
				'name'     => 'addException',
				'username' => $this->user->getUserName()['username'],
				'table_name'=> 'oc_paycharge_exception',
				'source_field' => 'order_totals',
				'field_name' => 'exception_type',
				'ref_url'	=> 'index.php?route=total/paycharge',
				'old_value' => '',
				'new_value' => $type,
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'ip_address' => $this->request->getIpAddress,
				'file_location' => 'index.php?route=total/paycharge',
				'user_type'  => $this->user->getGroupName(),
				'comment'	=> 'Updated Paychange Exception Rules'
			);
   			CommonLib::addAdminChangeLog($this->db, $log_data);

   			$log_data['field_name'] = 'type_id';
   			$log_data['new_value']  = (int)$type_id;
   			CommonLib::addAdminChangeLog($this->db, $log_data);

   			$log_data['field_name'] = 'applicable_on_membership_discount';
   			$log_data['new_value']  = (int)$applicable;
   			CommonLib::addAdminChangeLog($this->db, $log_data);

		}
	}
	public function deleteException(string $type, int $type_id):void
	{
		$sql = "SELECT id, applicable_on_membership_discount
				FROM  ".DB_PREFIX."paycharge_exception
				WHERE
					exception_type 	= '".$this->db->escape($type)."'
					AND
					type_id 		= '".(int)$type_id."'
		";
		$this->db->query($sql);
		$result = $this->db->query($sql);
		if($result->num_rows) {
			$old_value = $result->row['id'];
			// save log data
			$log_data = array(
				'table_id' => $old_value,
				'user_id'  => $this->user->getId(),
				'name'     => 'deleteException',
				'username' => $this->user->getUserName()['username'],
				'table_name'=> 'oc_paycharge_exception',
				'source_field' => 'order_totals',
				'field_name' => 'exception_type',
				'ref_url'	=> 'index.php?route=total/paycharge',
				'old_value' => '',
				'new_value' => $type,
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'ip_address' => $this->request->getIpAddress,
				'file_location' => 'index.php?route=total/paycharge',
				'user_type'  => $this->user->getGroupName(),
				'comment'	=> 'Updated Paychange Exception Rules'
			);
   			CommonLib::addAdminChangeLog($this->db, $log_data);

   			$log_data['field_name'] = 'type_id';
   			$log_data['new_value']  = (int)$type_id;
   			CommonLib::addAdminChangeLog($this->db, $log_data);

   			$log_data['field_name'] = 'applicable_on_membership_discount';
   			$log_data['new_value']  = (int)$result->row['applicable_on_membership_discount'];
   			CommonLib::addAdminChangeLog($this->db, $log_data);

   			//Delete Exception	
   			$sql = "DELETE FROM  ".DB_PREFIX."paycharge_exception
					WHERE
						exception_type 	= '".$this->db->escape($type)."'
						AND
						type_id 		= '".(int)$type_id."'
					";
				$this->db->query($sql);
		}
	}

	public function updateApplicableOnMembershipStatus(int $rule_id, int $applicable_status)
	{
		if($rule_id) 
		{
			$sql = "UPDATE ".DB_PREFIX."paycharge_exception
					SET
						applicable_on_membership_discount = '".(int)$applicable_status."'
					WHERE
						id 		= '".(int)$rule_id."'
					";
					$this->db->query($sql);
		}
	}

}