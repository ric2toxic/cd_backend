<?php
class ModelExtensionExtension extends Model {

    function getExtensions($type, $store_id = -1) {

        if($store_id >= 0 && $type == 'payment'){
            $query = $this->db->query(" SELECT e.*, e2s.store_id FROM " . DB_PREFIX . "extension e
									  	INNER JOIN ". DB_PREFIX ."wsb_extension_to_store e2s
										ON e.extension_id = e2s.extension_id
										WHERE `type` = '" . $this->db->escape($type) . "' AND e2s.store_id=". $store_id);
        }else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");
        }

        return $query->rows;
    }

	public function getInstalled($type) {
		$extension_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' ORDER BY code");

		foreach ($query->rows as $result) {
			$extension_data[] = $result['code'];
		}

		return $extension_data;
	}

	public function install($type, $code) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = '" . $this->db->escape($type) . "', `code` = '" . $this->db->escape($code) . "'");
	}

	public function uninstall($type, $code) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");
	}
}
