<?php
class ModelAccountHelpdesk extends Model {

	 public function updateHelpdeskId($helpdesk_id , $customer_id){

	    $sql = "UPDATE ".DB_PREFIX."customer SET helpdesk_id = $helpdesk_id 
	    		WHERE customer_id = ".$customer_id;

			    $response = $this->db->query($sql);
		 		return $response;       
    }

    public function updateCustomerTokenByHelpdesk($customer_id,$token){

	    $sql = "UPDATE ".DB_PREFIX."customer SET 
	    		ws_access_token = '".$token."' 
	    		WHERE customer_id = ".$customer_id." ";
			    $response = $this->db->query($sql);
		 		if($response){
		 			return true;		 			
		 		}else{
		 			return  false;
		 		}
    }

    public function getHelpdeskId($customer_id){

        $sql ="SELECT helpdesk_id,firstname,lastname,email,customer_id,ws_access_token,password,telephone
               FROM ".DB_PREFIX."customer as c  
               WHERE customer_id = ".$customer_id." " ; 
               $result = $this->db->query($sql);
               return $result->row;	         
    }

    public function getCustomerByPassword($customer_id,$password,$token){

    	if(!empty($customer_id)){
    		$filter[] = "customer_id = '".$customer_id."' "; 
    	} 
    	if(!empty($password)){
    		$filter[] = "password = '".$password."' "; 
    	}
    	if(!empty($token)){
    		$filter[] = "ws_access_token = '".$token."' "; 
    	}


        $sql ="SELECT customer_id
               FROM ".DB_PREFIX."customer as c  
               WHERE ".implode("AND ", $filter)." ";
        $result = $this->db->query($sql)->row;
       	if(!empty($result)){	
       	   return $result['customer_id'];	         
        }
    }

      /**
    *@author Yogesh Mishra.
    *@param  mobile.
    *@return (json)$rt, $rt contains(statusCode = 200/1000, message = "whatever the situation is",data).
    *@desc   This method/api is called from wsb-helpdesk-api to get the customer from oc_customer 
    *        by customer's mobile number . 
    */


    public function getCustomerForHelpdek($filter){      
      // $ticket_type_data = $this->cache->get('ticket.type'); 

      // if(empty($ticket_type_data)){

        if(isset($filter['mobile']) && !empty($filter['mobile'])){
            $field = "c.telephone =".$filter['mobile']." ";
        }

        if(isset($filter['wsb_id']) && !empty($filter['wsb_id'])){
            $field = "c.customer_id =".$filter['wsb_id']." ";
        }

      	$sql = "SELECT c.customer_id,c.master_id,c.firstname,c.lastname,c.email,c.telephone, 
                oa.company 
      			FROM ".DB_PREFIX."customer as c 
                LEFT JOIN ".DB_PREFIX."address as oa ON c.customer_id = oa.customer_id  WHERE ".$field." ";      
        // echo $sql; die;
        $ticket_type_data = $this->db->query($sql);
        // print_r($ticket_type_data); die();
        $count = array();
        if($ticket_type_data->num_rows > 1){
            foreach ($ticket_type_data->rows as $key => $value) {                
                $sql  = "SELECT COUNT(*) as count FROM ".DB_PREFIX."order WHERE customer_id  = ".$value['customer_id']." ";
                $order_count = $this->db->query($sql)->row;
                $count[$value['customer_id']] = $order_count['count'];
            }       
            // print_r($count); die;
            arsort($count);
            $highest_count = reset($count);
            if($highest_count == 0){
                
                if(empty($latest)){
                    $latest['customer_id'] = $ticket_type_data->row['customer_id'];
                }
                foreach ($ticket_type_data->rows as $key => $value) {    
                    if($value['customer_id'] == $latest['customer_id'] ){
                        $data['customer_id'] = $value['customer_id'];
                        $data['telephone']   = $value['telephone'];
                        $data['email']    = $value['email'];
                        $data['firstname'] = $value['firstname'];
                        $data['lastname']  = $value['lastname'];
                        $data['company']   = $value['company'];
                    }
                }
                return $data;

            }elseif ($count > 0 ) {
                $max_id = array_keys($count, max($count));   
                foreach ($ticket_type_data->rows as $key => $value) {    
                    if($value['customer_id'] == $max_id[0] ){
                        $data['customer_id'] = $value['customer_id'];
                        $data['telephone']   = $value['telephone'];
                        $data['email']    = $value['email'];
                        $data['firstname'] = $value['firstname'];
                        $data['lastname']  = $value['lastname'];
                        $data['company']   = $value['company'];
                    }
                }
                return $data;
            }
        }else{
            return $ticket_type_data->row;
        }

        // $this->cache->set('ticket.type', $ticket_type_data);
     // print_r($count); die;
    	
       // $ticket_type_data;	
    	
    }

}
