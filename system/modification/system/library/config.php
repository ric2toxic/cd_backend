<?php
class Config {
	private $data = array();

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
		return isset($this->data[$key]);
	}

	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$_ = array();

			require(modification($file));

			$this->data = array_merge($this->data, $_);
		} else {
			trigger_error('Error: Could not load config ' . $filename . '!');
			exit();
		}
	}

	/**
	 * @author  Rakesh Singh Shekhawat
	 * @description international store does not have any entry in p2s table
	 * so in that case we have to get all products on international store which are belongs default store.
	 * @return int
	 */
	public function getStoreIdForSql(){
		$data['store_id_for_sql'] = 0;
		if($this->get('config_store_id') == INTERNATIONAL_STORE_ID){
			$data['store_id_for_sql'] = 0;
		}else{
			$data['store_id_for_sql'] = $this->get('config_store_id');
		}

		return $data['store_id_for_sql'];
	}

	public function solrConfig($core=''){
		if($core != ''){
			$path = $core;
		}else{
			$path = SOLR_PATH;
		}
		$config_solr = array(
				'endpoint' => array(
						'localhost' => array(
								'host' => SOLR_HOST,
								'port' => SOLR_PORT,
								'path' => $path,
                                'timeout' => 50000
						)
				)
		);

		return $config_solr;
	}

	public function solrIndexingFields(){

		$solr_indexing_fields['product'] = array(
			'product_id'		=>'id',
			'name'				=>'name',
			'set_description'	=>'set_description',
			'description'		=>'description',
			'tag'				=>'tag',
			'model' 			=>'model',
			'quantity'			=>'quantity',
			'stock_status_id'	=>'stock_status_id',
			'stock_status' 		=>'stock_status',
			'price'				=>'price',
			'selling_price'		=>'selling_price',
			'piece_in_set'		=>'piece_in_set',
			'date_available'	=>'date_available',
			'date_added'		=>'date_added',
			'store_id'			=>'store_id',
			'categories'		=>'categories',
			'category_id'		=>'category_id',
			'filter_id'			=>'filter_id',
			'filters'			=>'filters',
			'is_single'			=>'is_single',
			'status'			=>'status',
			'seller_id'			=>'seller_id',
			'sort_order'		=>'sort_order',
			'rating'			=>'rating',
			'minimum'			=>'minimum',
			'seller_id'			=>'seller_id',
			'seller_status'		=>'seller_status',
			'vacation_mode'		=>'vacation_mode'
		);
		return $solr_indexing_fields['product'];
	}
}