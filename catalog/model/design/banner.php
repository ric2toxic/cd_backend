<?php
class ModelDesignBanner extends Model {
	public function getBanner($banner_id, $preferences = 0) {
		$query = $this->db->query("SELECT 
			     b.banner_id,
			     bid.title,
			     bi.target_blank,
			     bi.link,
			     bi.image
		        FROM " . DB_PREFIX . "banner b 
		        INNER JOIN ". DB_PREFIX . "banner_image bi 
		        ON(b.banner_id  = bi.banner_id) 
		        LEFT JOIN " . DB_PREFIX . "banner_image_description bid 
		        ON (bi.banner_image_id  = bid.banner_image_id) 
		        INNER JOIN " . DB_PREFIX . "wsb_banners_image_category bic 
		        ON (bi.banner_image_id  = bic.banner_image_id) 
		        WHERE bi.banner_id = '" . (int)$banner_id . "' 
		        AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "'AND b.store_id = '" . (int)$this->config->get('config_store_id') . "' and bic.category_id = '". (int)$preferences ."' AND bi.status = 1 ORDER BY bi.sort_order ASC");
		return $query->rows;
	}

	public function addBanner($data) {
		$this->event->trigger('pre.admin.banner.add', $data);

		 $sql = "INSERT INTO " . DB_PREFIX . "banner
				SET name = '" . $this->db->escape($data['name']) . "',
				status = '" . (int)$data['status'] . "',
				store_id = '" . (int)$data['store_id'] . "',
				positions = '" . $data['banner_position'] . "',
				width = '" . (int)$data['banner_width'] . "',
				height = '" . (int)$data['banner_height'] . "'";

		$this->db->query($sql);

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				$q = "INSERT INTO " . DB_PREFIX . "banner_image
					  SET banner_id = '" . (int)$banner_id . "',
					  link = '" .  $this->db->escape($banner_image['link']) . "',
					  image = '" .  $this->db->escape($banner_image['image']) . "',
					  sort_order = '" . (int)$banner_image['sort_order'] . "'";
				$this->db->query($q);

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}

		$this->event->trigger('post.admin.banner.add', $banner_id);

		return $banner_id;
	}

	public function editBanner($banner_id, $data) {
		$this->event->trigger('pre.admin.banner.edit', $data);

		$this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', store_id = '" . (int)$data['store_id'] . "', positions = '" . $data['banner_position'] . "', width = '" . $data['banner_width'] ."', height = '" . $data['banner_height'] . "' WHERE banner_id = '" . (int)$banner_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}

		$this->event->trigger('post.admin.banner.edit', $banner_id);
	}

	public function deleteBanner($banner_id) {
		$this->event->trigger('pre.admin.banner.delete', $banner_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		$this->event->trigger('post.admin.banner.delete', $banner_id);
	}

	public function getBannerNew($banner_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		return $query->row;
	}

	public function getBanners($data = array()) {
		$stores = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
		$store_id = array();
		foreach($stores as $store) {
			$store_id[] = $store['store_id'];
		}
		$id = implode(',' , $store_id);

		 $sql = "SELECT * FROM " . DB_PREFIX . "banner WHERE store_id IN ($id)";

		$sort_data = array(
			'name',
			'status'
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

	public function getBannerImages($banner_id) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_description_data = array();

			$banner_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int)$banner_image['banner_image_id'] . "' AND banner_id = '" . (int)$banner_id . "'");

			foreach ($banner_image_description_query->rows as $banner_image_description) {
				$banner_image_description_data[$banner_image_description['language_id']] = array('title' => $banner_image_description['title']);
			}

			$banner_image_data[] = array(
				'banner_image_description' => $banner_image_description_data,
				'link'                     => $banner_image['link'],
				'image'                    => $banner_image['image'],
				'sort_order'               => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}

	public function getTotalBanners() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

		return $query->row['total'];
	}

}