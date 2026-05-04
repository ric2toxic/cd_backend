<?php
class ModelCatalogCustomUrl extends Model {
	public function getCustomUrlInfo($url_alias_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "url_alias as UA LEFT JOIN " . DB_PREFIX . "custom_url_description as CD ON UA.url_alias_id = CD.url_alias_id WHERE UA.url_alias_id = " . $url_alias_id;
                $query = $this->db->query($sql);
		return $query->row;
	}
    
    public function getMobileCustomUrlInfo($keyword) {
		$sql = "SELECT UA.url_alias_id, UA.query, UA.keyword, UA.url_type, UA.store_id, UA.search_id, UA.is_custom, UA.is_redirect_301, CD.title, CD.description, CD.short_description, CD.meta_title, CD.meta_description FROM " . DB_PREFIX . "url_alias as UA INNER JOIN " . DB_PREFIX . "custom_url_description as CD ON UA.url_alias_id = CD.url_alias_id WHERE UA.keyword = '" . $this->db->escape($keyword)."'";
                $query = $this->db->query($sql);
		return $query->row;
	}

	
}
