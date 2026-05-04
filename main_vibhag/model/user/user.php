<?php
class ModelUserUser extends Model {
	public function addUser($data) {
		$password_data = $this->user->getRandomPasswordString();
        $password_string = password_hash( $password_data['password'], PASSWORD_DEFAULT );
		$dont_show_dashboard = (!empty($data['dont_show_dashboard'])) ? 1 : 0;
		$this->db->query("INSERT INTO `" . DB_PREFIX . "user` 
							SET 
								username 		= '" . $this->db->escape($data['username']) . "', 
								user_group_id 	= '" . (int)$data['user_group_id'] . "', 
								password 		= '" . $this->db->escape($password_string) . "', 
								firstname 		= '" . $this->db->escape($data['firstname']) . "', 
								lastname 		= '" . $this->db->escape($data['lastname']) . "', 
								email 			= '" . $this->db->escape($data['email']) . "', 
								image 			= '" . $this->db->escape($data['image']) . "', 
								status 			= '" . (int)$data['status'] . "', 
								branch_code 	= '" . $this->db->escape($data['branch_code']) . "', 
								device_id 		= '" . $this->db->escape($data['device_id']) . "', 
								dont_show_dashboard = '" . $dont_show_dashboard . "', 
								default_landing_page_url = '" . $this->db->escape($data['default_landing_page_url']) . "', 
								date_added 		= NOW(), 
								permission 		= '" . (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '') . "'
						");
	}

	public function editUser($user_id, $data) {
		$dont_show_dashboard = (!empty($data['dont_show_dashboard'])) ? 1 : 0;
		$this->db->query("UPDATE `" . DB_PREFIX . "user` 
                          SET username = '" . $this->db->escape($data['username']) . "', 
                              user_group_id = '" . (int)$data['user_group_id'] . "', 
                              firstname = '" . $this->db->escape($data['firstname']) . "', 
                              lastname = '" . $this->db->escape($data['lastname']) . "', 
                              email = '" . $this->db->escape($data['email']) . "', 
                              image = '" . $this->db->escape($data['image']) . "', 
                              status = '" . (int)$data['status'] . "', 
                              branch_code = '" . $this->db->escape($data['branch_code']) . "', 
                              device_id = '" . $this->db->escape($data['device_id']) . "', 
                              dont_show_dashboard = '" . $dont_show_dashboard . "', 
							  default_landing_page_url = '" . $this->db->escape($data['default_landing_page_url']) . "', 
                              permission = '" . (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '') . "'    
                          WHERE user_id = '" . (int)$user_id . "'");
	}

	public function editPassword($user_id, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE user_id = '" . (int)$user_id . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}


	public function editToken($token, $user_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET wc_notification_access_token = '" . $this->db->escape($token) . "' WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getNotificationToken($user_id) {
		$query = $this->db->query("SELECT wc_notification_access_token FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'");

		return $query->row['wc_notification_access_token'];
	}

	public function deleteUser($user_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getUser($user_id) {
		$query = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "user_group` ug WHERE ug.user_group_id = u.user_group_id) AS user_group FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'");

                $user_data = $query->row;
                $user_data['permission'] = unserialize($query->row['permission']);
                
		return $user_data;
	}

	public function getUserByUsername($username) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->db->escape($username) . "'");

		return $query->row;
	}

	public function getUserByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getUsers($data = array()) {
		$sql = "SELECT user_id,
					   user_group_id,
					   username,
					   CONCAT(firstname,' ', lastname) as name,
					   branch_code,
					   status,
					   date_added	 
				FROM `" . DB_PREFIX . "user`
				WHERE 1=1";


		if (!empty($data['filter_name'])) {
			$sql .= " AND CONCAT(firstname,' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) ."%'";
		}

		if (!empty($data['filter_user_name'])) {
			$sql .= " AND username LIKE '%" . $this->db->escape($data['filter_user_name']) ."%'";
		}

		if (!empty($data['filter_user_group'])) {
			$sql .= " AND user_group_id = '" . (int)$data['filter_user_group'] ."%'";
		}

		if (isset($data['filter_user_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_user_status'] ."'";
		}		

		$sort_data = array(
			'username',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY username";
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

		if( $query->num_rows ){
			$records = array();
			foreach ($query->rows as $key => $value) {
				$sql_grp_name = "SELECT name 
								 FROM " . DB_PREFIX ."user_group
								 WHERE user_group_id =  " . (int) $value['user_group_id'];
				$query_grp_name = $this->db->query($sql_grp_name);
				if( $query_grp_name->num_rows ){
					$records[] = array_merge($value, array('group_name' => $query_grp_name->row['name']));	
				} else {
					$records[] = $value;
				}				
			} 
			return $records;
		} else {
			return array();
		}
	}

	public function getTotalUsers($data = array()) {
		$sql = "SELECT COUNT(*) AS total 
				FROM `" . DB_PREFIX . "user`
				WHERE 1=1";
		
		if (!empty($data['filter_user_name'])) {
			$sql .= " AND username LIKE '%" . $this->db->escape($data['filter_user_name']) ."%'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) ."%'";
		}

		if (!empty($data['filter_user_group'])) {
			$sql .= " AND user_group_id = '" . (int)$data['filter_user_group'] ."%'";
		}
		if (isset($data['filter_user_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_user_status'] ."' ";
		}	
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalUsersByGroupId($user_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "'");

		return $query->row['total'];
	}

	public function getTotalUsersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function getUserNameByUserIds($user_ids = ''){
		$query = $this->db->query("SELECT user_id, concat(firstname, ' ' ,lastname) as name FROM `" . DB_PREFIX . "user` WHERE user_id IN (" . $user_ids . ")");

		return $query->rows;
	}
}