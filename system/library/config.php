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

	public function load($filename)
	{
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$_ = array();

			require($file);

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
		if($this->get('store_id') == INTERNATIONAL_STORE_ID){
			$data['store_id_for_sql'] = 0;
		}else{
			$data['store_id_for_sql'] = $this->get('config_store_id');
		}

		return $data['store_id_for_sql'];
	}
}
