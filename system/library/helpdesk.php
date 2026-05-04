<?php 

class Helpdesk{

	// private $api_key;	
	// private $domain;	
	// private $db;
	// private $load;
	// var $ticket_info = array(); 
		
 	function __construct() {
	  	// $this->api_key = "17q72DciDwIFOayvPFr";
	  	// $this->domain  = "wsb";
	  	$this->api_url  = HELPDESK_API_URL;
	}
	
	// public function checkContactIfExist($result){
	// 	// echo "<pre>";
	// 	// $result['email'];
	// 	// die();
 //    	$api_url 	 	 = $this->api_url;		 
	// 	$method_name 	 = "customer/getCustomer";
	// 	$data            = array("customer_email"  => $result['email'],
	// 							 "password" 	   => $result['password'],
	// 							 "customer_mobile" => $result['telephone'],
	// 							 "customer_access_token" => $result['customer_access_token'],
	// 							 "wsb_id" => $result['wsb_id'],
	// 							 "source_id" => 7 );

	// 	$url = $api_url.$method_name;
	// 	// print_r($url);
	// 	// die();
	// 	$apiKey = HELPDESK_API_KEY; // api key 
	// 	$headers = array(
	// 	     'api_key: '.$apiKey
	// 	);
	// 	$ch = curl_init($url);
	// 	curl_setopt($ch, CURLOPT_POST, true);
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	$response =  curl_exec($ch);		
	// 	// print_r($response);
	// 	$response_array = json_decode($response,true); 
	// 	if($response_array['statusCode'] == 200){
	// 		return $response_array['data'];
	// 	}		
		
	// 	curl_close($ch);
	// }	
                                           
	public function createContact($customer_details){  
				
		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "customer/addCustomer";
		$data 		     = array("name"  => $customer_details['name'], 
							     "email" => $customer_details['email'],
							     "customer_mobile" => $customer_details['telephone'],
							     "customer_access_token" => $customer_details['customer_access_token'],
							     "wsb_id" => $customer_details['wsb_id'],
							     "password" => $customer_details['password'],
							 	 "source_id" => 7);

		$url = $api_url.$method_name;
		// print_r($url);
		// die();
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true); 
		// if($response_array['statusCode'] == 200){			
		return $response_array['data'];
		// }

		curl_close($ch);			

  	}			

  	public function updateHelpdeskCustomer($customer_details){  			
		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "customer/updateCustomer";
		$data 		     = array("customer_name"  => $customer_details['customer_name'], 
							     "email" 		  => $customer_details['email'],
							     "mobile" 		  => isset($customer_details['telephone'])?$customer_details['telephone']:'',
							     "customer_access_token" => $customer_details['customer_access_token'],
							     "wsb_id" 		  => $customer_details['customer_id'],
							     "customer_id"    => $customer_details['helpdesk_id'],
							     "password" 	  => $customer_details['customer_access_token'],
							 	 "source_id" 	  => isset($customer_details['source_id'])?$customer_details['source_id']:7);

		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		// print_r($response); die('test die updateCustomer');
		$response_array = json_decode($response,true); 
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}

		curl_close($ch);			

  	}		   

  	public function createTicket($ticket){	 		
	 		// echo "<pre>";
	  	// 	print_r($ticket);
	  	// 	die();

	  	$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/createTicket";
		$data 		     = array("ticket_subject"        => $ticket['ticket_subject'], 
							     "customer_access_token" => $ticket['customer_access_token'],
							     "description" => $ticket['ticket_description'],
							     "group_id" => $ticket['ticket_type_id'],
							     "wsb_id"      => $ticket['wsb_id'],
 							     "customer_id" => $ticket['helpdesk_id'],	
							     "password"    => $ticket['password'],
							     "priority"    => 4,
							     "status"	   => 1,
							     "agent_id"    => 0,
							     "assigned_to" => 0,
							     "source_id"   => 7,
							     "notify_customer" => 1
        					    );

		if(isset($ticket['attachment']['error']) && $ticket['attachment']['error'] == 0 ){
		$data['attachment'] = curl_file_create(
    	  						$ticket['attachment']['tmp_name'],
    	  						$ticket['attachment']['type'],
    	  				        $ticket['attachment']['name']);	
		}else{
			$data['attachment'] = '';
		}
		
		$url = $api_url.$method_name;
		// print_r($url);
		// die();
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);		
		$response_array = json_decode($response,true);
		// echo "<pre>";
		// print_r($response_array);
		// die('sadf');
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}else{
			return $response_array['message'];
		}		
		
		curl_close($ch);	
	

  	} 
  	
	public function viewAllTickets($filter){
		// print_r($filter);
		// die();
    	$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/getAllTickets";		
		$data 		     = array("customer_id"  => $filter['helpdesk_id'],
								 "wsb_id"       => $filter['customer_id'],	
								 "page" 		=> $filter['page'],
								 "password"     => $filter['password'],
 								 "customer_access_token" => $filter['customer_access_token'],
								 "source_id"   => 7,
								 "ticket_status"   => $filter['status']);
		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);		
		// echo "<pre>";
		// print_r($response_array);	
		// die;	
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}else{
			return false;
		}		
		
		curl_close($ch);		
	}

	public function viewsingleTicket($single_ticket_param){  		

  		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/getTicket";		
		$data 		     = array("ticket_id"  => $single_ticket_param['ticket_id'],
								 "source_id"  => 7,
								 "page"       => $single_ticket_param['page'],
								 "wsb_id"     => $single_ticket_param['customer_id'],
								 "dont_update_read" => $single_ticket_param['dont_update_read'],
								 "customer_access_token" => $single_ticket_param['customer_access_token'],
								 "customer_id" => $single_ticket_param['helpdesk_id'],
								 "password"    =>  $single_ticket_param['password']
								);
		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);	
		// echo "<pre>";
		// print_r($response_array);	
		// die();
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}else{
			return false;
		}		
		
		curl_close($ch);	

	}	

	public function conversation($ticket_id){         

		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/getConversation";		
		$data 		     = array("ticket_id"  => $ticket_id);
		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);	
		// echo "<pre>";
		// print_r($response_array);	
		// die;
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}else{
			return false;
		}		
		
		curl_close($ch);	

	}
 				
	public function customerReply($reply_content){


		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/createReply";
		$data 		     = array("ticket_id"  			 => $reply_content['ticket_id'], 
							     "password"				 => $reply_content['password'],
							     "customer_access_token" => $reply_content['customer_access_token'],
							     "customer_id" 			 => $reply_content['helpdesk_id'],
							     "wsb_id"    			 => $reply_content['customer_id'],
							     "body"        			 => $reply_content['body'],
							     "source_id" 		     => 7
        					    );

		if(isset($reply_content['attachment']['error']) && $reply_content['attachment']['error'] == 0 ){
		$data['attachment'] = curl_file_create(
    	  						$reply_content['attachment']['tmp_name'],
    	  						$reply_content['attachment']['type'],
    	  				        $reply_content['attachment']['name']);	
		}else{
			$data['attachment'] = '';
		}
		
		$url = $api_url.$method_name;
		// print_r($url);
		// die();
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);
		// echo "<pre>";
		// print_r($response_array);	
		// die;
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}else{
			return $response_array['message'];
		}		
		
		curl_close($ch);		
	}		


	public function ticketClosed($close){

		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/updateTicket";		
		$data 		     = array("ticket_id"   => $close['ticket_id'],
								 "customer_id" => $close['helpdesk_id'],
								 "wsb_id"      => $close['wsb_id'],								
 								 "password"    => $close['password'],
								 "customer_access_token" => $close['customer_access_token'],
							     "notify_customer" => 1,
							     "source_id"      => 7,		
								 "status"      => 2	);
		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);
		// print_r($response_array);
		// die();
		if($response_array['statusCode'] == 200){
			return true;
		}else{
			return false;
		}		
		
		curl_close($ch);						

	
	}		

	public function getHelpdeskTicketType(){         

		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "ticket/getHelpdeskTicketType";		
		$data 		     = array();
		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);	
		// echo "<pre>";
		// print_r($response_array);	
		// die;
		if($response_array['statusCode'] == 200){
			return $response_array['data'];
		}else{
			return array();
		}		
		
		curl_close($ch);	

	}


    /**
    *@author Yogesh Mishra
    *@param  agent_access_token
    *@return array("statusCode"=>200/1000,"message"=>"message","data" => array())   
    *@desc   curl call url {{HELPDESK_API}/agent/agentAuthentication}
    */

    public function checkAgentExistance($token){    	
		$api_url 	 	 = $this->api_url;		 
		$method_name 	 = "agent/agentAuthentication";
		$url = $api_url.$method_name;
		$apiKey = HELPDESK_API_KEY; // api key 
		$headers = array(
		     'api_key: '.$apiKey
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response =  curl_exec($ch);
		$response_array = json_decode($response,true);	
		return $response_array;

		curl_close($ch);	

    }



}	

	
	