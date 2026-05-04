<?php
class Variables {
	private $config;
	private $db;
	private $data = array();

	public function __construct($registry) {
		/*$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');
*/
		/*if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}*/
	}
	
	public function get_variables(){
		$cat_array['61'] = "kurti";
		$cat_array['69'] = "suits";
		$cat_array['91'] = "dresses";
		$cat_array['72'] = "tops";
		$cat_array['76'] = "bottoms";
		return $cat_array;
	}
}
