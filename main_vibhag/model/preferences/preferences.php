<?php 
class ModelPreferencesPreferences extends Model {
	public function saveMasterPreferences($data){
		$sql ="TRUNCATE `oc_master_preference`";
		$this->db->query($sql);

		foreach ($data as $value) {
			if(!empty($value['category_id'])){
				$sql = "INSERT INTO oc_master_preference SET category_id = '" . $value['category_id'] . "', filter_id = '" . $value['filter_id'] ."', category_image= '" . $value['category_image'] . "',  created = now()";	
				$this->db->query($sql);
			}
		}
	}
	public function getMasterPreferences(){
		$sql = "SELECT * FROM ". DB_PREFIX ."master_preference";
		$result = $this->db->query($sql);
		$preferences = $result->rows;
		$i = 0;
		$data = array();
		foreach ($preferences as $value) {
			// Get Category Name with category id
			$this->load->model('catalog/category');
			$filter_data['filter_category_id'] = $value['category_id'];
			$results = $this->model_catalog_category->getCategories($filter_data);
			$data[$i]['category_name'] 	= $results[0]['name'];
			$data[$i]['category_id'] 	= $results[0]['category_id'];
			$data[$i]['category_image'] = $value['category_image'];

			// Get Filter Name with category id
			$filters = explode(',', $value['filter_id']);
			$this->load->model('catalog/filter');
			foreach ($filters as $filter_id) {
				$filter_info = $this->model_catalog_filter->getFilter($filter_id);
				if ($filter_info) {
					$data[$i]['filters'][] = array(
						'filter_id' 	=> $filter_info['filter_id'],
						'filter_name'   => $filter_info['group'] . ' &gt; ' . $filter_info['name']
					);
				}
			}
		$i++;			
		}
		return $data;
	}
}