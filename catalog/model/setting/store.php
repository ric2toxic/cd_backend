<?php
class ModelSettingStore extends Model {

    public $alertOnRedirection = false;

	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');

		if (!$store_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");

			$store_data = $query->rows;

			$this->cache->set('store', $store_data);
		}

		return $store_data;
	}
	/**
	 * Get store detail
	 */
	public function getStore($store_id) {

		$stores_data = $this->cache->get('store');

		$store_data = array();
		if (!$stores_data) {
			$q = "SELECT * FROM " . DB_PREFIX . "store WHERE store_id = " . $store_id;
			$query = $this->db->query($q);

			$store_data = $query->row;
		}else{

			foreach($stores_data as $store){

				if($store['store_id'] == $store_id) {
					$store_data['store_id'] = $store['store_id'];
					$store_data['name'] = $store['name'];
					$store_data['url'] = $store['url'];
					$store_data['ssl'] = $store['ssl'];
				}
			}
		}

		return $store_data;
	}
	/**
	 *	Commented as Singles store is now defunct
	 *	All single products are indicated by is_single in oc_product
	 *	Commented by Parth Gupta on 19-01-2015
	 */
	public function getStoreSwitch(){

		$request_uri =  substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
		$qry_string = parse_url($request_uri, PHP_URL_QUERY);
		$url_path =  parse_url($request_uri, PHP_URL_PATH);

		if(isset($this->request->get['route'])) {

			$page_route = explode('/', $this->request->get['route']);
			if (isset($page_route[1]) && $page_route[1] && isset($this->request->get['path'])) {

				$url_cat = $this->url->link('product/category', 'path=' . $this->request->get['path']);
				$url_path = parse_url($url_cat, PHP_URL_PATH);
				$url_path = substr($url_path, 1, strlen($url_path));

			}
		}
		parse_str($qry_string, $arr_query_strings);

		unset($arr_query_strings['session_id']);

		$arr_query_strings['session_id'] = $this->session->getId();
		$arr_query_string = '';

		foreach($arr_query_strings as $key=>$value){
			$arr_query_string = $key."=".$value."&";
		}

		$build_query_string = '?'.$arr_query_string; //substr($arr_query_string,0, (strlen($arr_query_string)-1));

		$this->load->language('common/header');
		$data['store_switch'] = array();

		$data['store_id'] = $this->config->get('config_store_id');

		$this->load->model('setting/store');

		if($data['store_id'] == 0){

			$single_store_desktop = 2;
			$result = $this->model_setting_store->getStore($single_store_desktop);
			$data['store_switch']['label'] = $this->language->get('text_single_store');

			$data['store_switch'] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name'],
				'url'      => $result['url'].$url_path .$build_query_string,
				'label'	   => $this->language->get('text_single_store')
			);

		}elseif($data['store_id'] == 2){

			$main_store_desktop = 0;
			$data['store_switch'] = array(
				'store_id' => 0,
				'name'     => $this->language->get('text_default'),
				'url'      => HTTP_SERVER .$url_path.$build_query_string,
				'label'    => $this->language->get('text_wholesale_set_store')

			);

		}

		return $data['store_switch'];
	}
	public function getInternationalSwitch(){
		$host = $_SERVER['HTTP_HOST'];
        $request_uri = $_SERVER['REQUEST_URI'];


		//echo $this->customer->getId(); die;
		if ($this->customer->isLogged() && in_array($this->customer->getId(), array(2095, 2207))) {

			return $result = array('is_redirect' => 0, 'redirect_url' => '', 'user_country' => '');
			//do not redirect
		}else {
			if(isset($_GET['country']) && $_GET['country'] != '') {
				$country = strtoupper($_GET['country']);
				setcookie('user_country', $country, time() + 60 * 60 * 24 * 30, '/', INDIA_STORE_HOST);
				setcookie('user_country', $country, time() + 60 * 60 * 24 * 30, '/', INTERNATIONAL_STORE_HOST);
			}elseif((isset($_COOKIE['user_country']) && $_COOKIE['user_country'] != '')) {
				$country = strtoupper($_COOKIE['user_country']);
			}else{

				$ip_string = '';
				if (isset($_GET['ip']) && !empty($_GET['ip'])) {
					$ip = $_GET['ip'];

				} else {
					$ip = $this->request->getIpAddress; //$this->getIpAddress();
				}

				//$country = geoip_country_code_by_addr($gi, $ip);
				//echo $ip;
				if ($ip == '127.0.0.1') {

					$country = 'IN';

				} else {
					$country = $this->ip_info($ip, 'countrycode');
				}
				//echo $country;
				if ($country == '') {
					$country = 'IN';
				}
				setcookie('user_country', $country, time() + 60 * 60 * 24 * 30, '/', INDIA_STORE_HOST);
				setcookie('user_country', $country, time() + 60 * 60 * 24 * 30, '/', INTERNATIONAL_STORE_HOST);

			}
			//geoip_close($gi);
			//echo $country; echo $host ."--". INTERNATIONAL_STORE_HOST;die;
			$my_country = array('in');

           if(CONFIG_IS_MOBILE == 0)
           {
			if (!in_array(strtolower($country), $my_country)) {
				//echo $host ."--". INDIA_STORE_HOST;
                if ($host == INDIA_STORE_HOST) {
                    //## Stop Redirection, let keep open india store on international IP with alert message - [20-02-2017]
                    $this->alertOnRedirection = false; //replace to true to show alert on home page
                    //header('Location: http://' . INTERNATIONAL_STORE_HOST . $request_uri);
                    //exit;
                }
			} else {
				if ($host == INTERNATIONAL_STORE_HOST) {
					header('Location: https://' . INDIA_STORE_HOST . $request_uri);
					exit;
				}
			}
		  }
		  else
		  {
		  	$result = array('is_redirect' => 0, 'redirect_url' => '', 'user_country' => $country);

		  	if (!in_array(strtolower($country), $my_country)) {
				//echo $host ."--". INDIA_STORE_HOST;
                if ($host == INDIA_STORE_HOST) {
                  
                    $result['is_redirect'] = 0;
					$result['redirect_url'] = '';
                }
			} else {
				if ($host == INTERNATIONAL_STORE_HOST) {
					$result['is_redirect'] = 1;
					$result['redirect_url'] = 'https://' . INDIA_STORE_HOST;
				}
			}
			return $result;
		  }	


		}


	}
	/**
	 * Get IP address of client machine
	 */
	public function getIpAddress(){

		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';

		return  $ipaddress;
	}
	/**
	 *  http://www.geoplugin.net/json.gp API
	 */
	function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $this->request->getIpAddress;
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}
		return $output;
	}
}
