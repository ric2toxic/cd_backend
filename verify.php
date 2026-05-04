<?php 

if (isset($_REQUEST['token'])) {
	
	    // Get Url
		if ($_SERVER['SERVER_NAME'] == 'www.wsb.in') {                
			$crm_site_url = "http://localhost/wsbox-crm/";
		
		} elseif ($_SERVER['SERVER_NAME'] == 'www.wholesalebox.in/staging') {        
			$crm_site_url = "http://wholesalebox.biz/staging/";

		} else {
			$crm_site_url = "http://wholesalebox.biz/";
		} 
		
		$data_json = array('token' => $_REQUEST['token']);       


		//curl url       
		$curl_url = $crm_site_url."cron/verify_business_account";


		$data_json = json_encode($data_json);           
		$ch =  curl_init();
		curl_setopt($ch,CURLOPT_URL,$curl_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
		$result=curl_exec($ch);
		curl_close($ch);    

		// echo "<pre>";
		// print_r($result); die; 

		$result = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result), true ); 

		//$msg =  isset($result['status']) ? $result['status'] : '';
		
		if ( isset ($result['status']) && $result['status'] == 1) {
				
				header("location:https://www.wholesalebox.in");
		} else {
			
				echo 'Invalid token !'; die;
		} 	
	
	}

	echo "Empty token";die;

?>
