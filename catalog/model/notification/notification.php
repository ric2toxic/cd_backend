<?php
class ModelNotificationNotification extends Model
{
    /**
    * Get Customers
	* function getCustomers
    * @author Ravindra Singh
    * @date 16-01-2016
    * */
	  public function getCustomers(){
		$sql =   "SELECT customer_id,ws_gcm_registration_id FROM oc_customer";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function insertNotificationInDb($data) {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "notification_message SET message_type = '" . (int)$data['message_type'] . "', url = '" . $this->db->escape($data['url']) . "', category_id = '" . $this->db->escape($data['category_id']) . "', message = '" . $this->db->escape($data['message']) . "', image = '" . $this->db->escape($data['image']) . "', send_date = NOW()");
        return $this->db->getLastId();
    }
    /**
    * Get Customers
	* function sendPushNotification
	* @param registrationIds msg || array array
    * @author Ravindra Singh
    * @date 16-01-2016
    * */
    public function sendPushNotification($registrationIds,$msg){

		// API access key from Google API's Console
		//define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );
		//define( 'API_ACCESS_KEY', 'define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );' );

		$fields = array
		(
			'registration_ids' 	=> $registrationIds,
			'data'			=> $msg
		);

		$headers = array
		(
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		echo $result;
	}

	public function pushNotificationForCartItem(){                      

    $sql = "SELECT occ.customer_id, oc.ws_gcm_registration_id 
	        FROM " . DB_PREFIX . "customer_cart as occ 
	        INNER JOIN " . DB_PREFIX . "customer as oc  
	        ON(occ.customer_id = oc.customer_id) 
	        WHERE occ.date_modified <= DATE_SUB(NOW(), INTERVAL 48 HOUR)
	        AND oc.app_version >=24 AND occ.notified < 3";   
	$customer_data = $this->db->query($sql)->rows;   
	
	return $customer_data ;    
	}                         
	public function updateNotifiedField($customer_ids){
	$sql =  "UPDATE " . DB_PREFIX . "customer_cart SET notified= notified+1 
             WHERE customer_id IN (".$customer_ids.")";     
    
    	$this->db->query($sql);          
	}
}


