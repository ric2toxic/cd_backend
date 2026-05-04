<?php
class ModelUserUser extends Model {
	public function getUser($user_id) {
		$query = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "user_group` ug WHERE ug.user_group_id = u.user_group_id) AS user_group FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'");

		return $query->row;
	}

	public function getUsersByGroupId($user_group_id, $branch_code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "' and branch_code = '".$this->db->escape($branch_code)."'");

		return $query->rows;
	}
}