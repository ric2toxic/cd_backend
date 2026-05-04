<?php
class ModelTrackingTracking extends Model {
	public function add($data) {
		//echo "<pre>"; print_r($this->get_tracking_data_by_session_id()); exit;
		//echo "<pre>"; print_r($_SERVER);
		//echo "<pre>"; print_r($data); exit;
		
		if(isset($data['utm_source'])){
			$utm_source = $data['utm_source'];
		}else{
			$utm_source = '';
		}
		if(isset($data['matchtype'])){
			$matchtype = $data['matchtype'];
		}else{
			$matchtype = '';
		}
		
		if(isset($data['network'])){
			$network = $data['network'];
		}else{
			$network = '';
		}
		
		
		if(isset($data['device'])){
			$device = $data['device'];
		}else{
			if(CONFIG_IS_MOBILE == 1){
				$device = 'm';	
			}else{
				$device = 'D';
			}
			
		}
		
		if(isset($data['creative'])){
			$creative = $data['creative'];
		}else{
			$creative = '';
		}

		if(isset($data['keyword'])){
			$keyword = $data['keyword'];
		}else{
			$keyword = '';
		}
		
		if(isset($data['placement'])){
			$placement = $data['placement'];
		}else{
			$placement = '';
		}
		
		if(isset($data['adposition'])){
			$adposition = $data['adposition'];
		}else{
			$adposition = '';
		}
		
		if(isset($data['utm_campgain'])){
			$campgain = $data['utm_campgain'];
		}else{
			$campgain = '';
		}
		if(isset($data['utm_medium'])){
			$medium = $data['utm_medium'];
		}else{
			$medium = '';
		}
		
		if(isset($data['tracking'])){
			$tracking = $data['tracking'];
		}else{
			$tracking = '';
		}
		
		if(isset($data['customer_id'])){
			$customer_id = $data['customer_id'];
		}else{
			$customer_id = '';
		}
		
		
		if(isset($data['mobile'])){
			$mobile = $data['mobile'];
		}else{
			$mobile = '';
		}
		
		$sess_id = session_id();
		$os = $_SERVER['HTTP_USER_AGENT'];
		$ip = $this->request->getIpAddress;
		$created_date = Date('Y-m-d H:i:s');
		
        /*echo "INSERT INTO " . DB_PREFIX . "wsb_tracking SET sess_id = '". $sess_id ."' , customer_id = '" . (int)$customer_id . "', utm_source = '" . $utm_source. "', matchtype = '" . $matchtype . "',
																			network = '" . $network . "', device = '" . $device . "',
																			creative = '" . $creative . "', keyword = '" . $keyword . "',
																			placement = '" . $placement . "', adposition = '" . $adposition . "',
																			created_date = '" . $created_date . "', mobile = '" . $mobile . "',
																			os = '" . $os . "', campgain = '" . $campgain . "',
																			medium = '" . $medium . "', ip = '" . $ip . "', tracking = '" . $tracking . "'"; exit;*/
		$tracking_record = $this->get_tracking_data_by_session_id();
		if($tracking_record->num_rows == 0){
			$query = $this->db->query("INSERT INTO " . DB_PREFIX . "wsb_tracking SET sess_id = '". $sess_id ."' , customer_id = '" . (int)$customer_id . "', utm_source = '" . $utm_source. "', matchtype = '" . $matchtype . "',
																			network = '" . $network . "', device = '" . $device . "',
																			creative = '" . $creative . "', keyword = '" . $keyword . "',
																			placement = '" . $placement . "', adposition = '" . $adposition . "',
																			created_date = '" . $created_date . "', mobile = '" . $mobile . "',
																			os = '" . $os . "', campgain = '" . $campgain . "',
																			medium = '" . $medium . "', ip = '" . $ip . "', tracking = '" . $tracking . "'");

			
			setcookie("session_id", $sess_id, time() + (86400 * 30), "/"); // 86400 = 1 day
			setcookie("utm_source", $utm_source, time() + (86400 * 30), "/"); // 86400 = 1 day
			
			return $query;
		}else{
			return 0;
		}
	}
	
	public function addDuplicate($data) {
		//echo "<pre>"; print_r($this->get_tracking_data_by_session_id()); exit;
		/*echo "<pre>"; print_r($_SERVER);
		echo "<pre>"; print_r($data); exit;*/
		
		if(isset($data['utm_source'])){
			$utm_source = $data['utm_source'];
		}else{
			$utm_source = '';
		}
		if(isset($data['matchtype'])){
			$matchtype = $data['matchtype'];
		}else{
			$matchtype = '';
		}
		
		if(isset($data['network'])){
			$network = $data['network'];
		}else{
			$network = '';
		}
		
		
		
		if(isset($data['device'])){
			$device = $data['device'];
		}else{
			if(CONFIG_IS_MOBILE == 1){
				$device = 'm';	
			}else{
				$device = 'D';
			}
			
		}
		
		if(isset($data['creative'])){
			$creative = $data['creative'];
		}else{
			$creative = '';
		}

		if(isset($data['keyword'])){
			$keyword = $data['keyword'];
		}else{
			$keyword = '';
		}
		
		if(isset($data['placement'])){
			$placement = $data['placement'];
		}else{
			$placement = '';
		}
		
		if(isset($data['adposition'])){
			$adposition = $data['adposition'];
		}else{
			$adposition = '';
		}
		
		if(isset($data['campgain'])){
			$campgain = $data['campgain'];
		}else{
			$campgain = '';
		}
		if(isset($data['medium'])){
			$medium = $data['medium'];
		}else{
			$medium = '';
		}
		
		if(isset($data['tracking_id'])){
			$tracking = $data['tracking_id'];
		}else{
			$tracking = '';
		}
		
		if(isset($data['customer_id'])){
			$customer_id = $data['customer_id'];
		}else{
			$customer_id = '';
		}
		
		
		if(isset($data['mobile'])){
			$mobile = $data['mobile'];
		}else{
			$mobile = '';
		}
		
		$sess_id = session_id();
		$os = $_SERVER['HTTP_USER_AGENT'];
		$ip = $this->request->getIpAddress;
		$created_date = Date('Y-m-d H:i:s');
		/*echo "INSERT INTO " . DB_PREFIX . "wsb_tracking SET sess_id = '". $sess_id ."' , customer_id = '" . (int)$customer_id . "', utm_source = '" . $utm_source. "', matchtype = '" . $matchtype . "',
																		network = '" . $network . "', device = '" . $device . "',
																		creative = '" . $creative . "', keyword = '" . $keyword . "',
																		placement = '" . $placement . "', adposition = '" . $adposition . "',
																		created_date = '" . $created_date . "', mobile = '" . $mobile . "',
																		os = '" . $os . "', campgain = '" . $campgain . "',
																		medium = '" . $medium . "', ip = '" . $ip . "', tracking = '" . $tracking . "'"; exit;*/
			$query = $this->db->query("INSERT INTO " . DB_PREFIX . "wsb_tracking SET sess_id = '". $sess_id ."' , customer_id = '" . (int)$customer_id . "', utm_source = '" . $utm_source. "', matchtype = '" . $matchtype . "',
																			network = '" . $network . "', device = '" . $device . "',
																			creative = '" . $creative . "', keyword = '" . $keyword . "',
																			placement = '" . $placement . "', adposition = '" . $adposition . "',
																			created_date = '" . $created_date . "', mobile = '" . $mobile . "',
																			os = '" . $os . "', campgain = '" . $campgain . "',
																			medium = '" . $medium . "', ip = '" . $ip . "', tracking = '" . $tracking . "'");

			
			setcookie("session_id", $sess_id, time() + (86400 * 30), "/"); // 86400 = 1 day
			setcookie("utm_source", $utm_source, time() + (86400 * 30), "/"); // 86400 = 1 day
			
			return $query;
	}
	
	public function update_customer_id($customer_id,$mobile = '',$session_id = ''){
		if(isset($_COOKIE['session_id'])){
			$session_id = $_COOKIE['session_id']; 
		}
		$tracking_record = $this->get_tracking_data_by_session_id_mobile_or_customer_id($customer_id);
		if($tracking_record->num_rows == 0){
			$tracking_record = $this->get_tracking_data_by_session_id_mobile_or_customer_id($mobile);
			if($tracking_record->num_rows == 0){ 
				$query = $this->db->query("UPDATE " . DB_PREFIX . "wsb_tracking SET customer_id = '" . (int)$customer_id . "' , mobile = '" . $mobile . "' WHERE sess_id = '" . $session_id . "' AND customer_id = 0");
				//return 1;
			}else{
				$query = $this->db->query("UPDATE " . DB_PREFIX . "wsb_tracking SET customer_id = '" . (int)$customer_id . "' WHERE sess_id = '" . $session_id . "' AND customer_id = 0");
			}
			return 1;
		}else{ 
			//echo "<pre>"; print_r($tracking_record->row); exit;
			$data = $tracking_record->row;
			unset($data['id']);
			$data['customer_id'] = $customer_id;
			$data['mobile'] = $mobile;
			$this->addDuplicate($data);
			return 1;
			//return 0;
		}
	}
	
	public function update_mobile($mobile,$session_id = ''){
		if(isset($_COOKIE['session_id'])){
			$session_id = $_COOKIE['session_id']; 
		}
		$tracking_record = $this->get_tracking_data_by_session_id_mobile_or_customer_id($mobile);
		if($tracking_record->num_rows == 0){
			$query = $this->db->query("UPDATE " . DB_PREFIX . "wsb_tracking SET mobile = '" . $mobile . "' WHERE sess_id = '" . $session_id . "'");
			return 1;

		}else{
			//echo "<pre>"; print_r($tracking_record->row); exit;
			$data = $tracking_record->row;
			unset($data['id']);
			$data['mobile'] = $mobile;
			$this->addDuplicate($data);
			return 1;

		}
	}
	/**
	 * get_tracking_data_by_session_id 
	 * @parameter session_id
	 * @parameter needs to be mobile app
	 * */
	public function get_tracking_data_by_session_id($session_id = ''){
		if(isset($_COOKIE['session_id'])){
			$session_id = $_COOKIE['session_id']; 
		}
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wsb_tracking WHERE sess_id = '" . $session_id . "'");
		return $query; 
	}
	/**
	 * get_tracking_data_by_session_id_mobile_or_customer_id
	 * @parameter session_id , (customer id or mobile)
	 * @parameter needs to be mobile app
	 * */
	public function get_tracking_data_by_session_id_mobile_or_customer_id($mobileorcustomer_id,$session_id = ''){
		if(isset($_COOKIE['session_id'])){
			$session_id = $_COOKIE['session_id']; 
		}
		//echo "SELECT * FROM " . DB_PREFIX . "wsb_tracking WHERE sess_id = '" . $session_id . "' OR (mobile = '".$mobileorcustomer_id."' OR customer_id = '".$mobileorcustomer_id."')"; exit;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wsb_tracking WHERE sess_id = '" . $session_id . "' OR (mobile = '".$mobileorcustomer_id."' OR customer_id = '".$mobileorcustomer_id."')");
		return $query; 
	}
}
