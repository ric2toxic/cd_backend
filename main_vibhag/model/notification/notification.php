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
		$sql =   "SELECT  DISTINCT ws_gcm_registration_id FROM oc_customer where ws_gcm_registration_id != '' ";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	/**
	 * Get Customers GCM ID
	 * function getCustomers
	 * @author Rakesh Singh
	 * @date 6-09-2016
	 * */
	public function getCustomerGcmId($customers_ids = ''){
		$sql 	= "SELECT DISTINCT ws_gcm_registration_id FROM oc_customer c where c.ws_gcm_registration_id != '' AND c.app_version >= '24'";
		if(!empty($customers_ids)){
			$sql 	.= " AND c.customer_id IN (". $customers_ids .")";
		}
		$query 	= $this->db->query($sql);
		return $query->rows;
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
		// curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );

	}

	public function getNotificationMessageType() {
		$query = $this->db->query("SELECT message_type_id, name FROM " . DB_PREFIX . "notification_message_type");
		//	echo "<pre>"; print_r($query->rows); die;
		return $query->rows;

	}

	public function sendNotification($data) {

        $sql = "INSERT INTO " . DB_PREFIX . "notification_message 
                SET message_type = '" . (int)$data['message_type'] . "', 
                url = '" . $this->db->escape($data['url']) . "', 
                category_id = '" . $this->db->escape($data['category_id']) . "', 
                message = '" . $this->db->escape($data['message']) . "', 
                image = '" . $this->db->escape($data['notification_img_path']) . "', 
                notes = '" . $this->db->escape($data['notes']) . "', 
                send_date = NOW()";

        $query = $this->db->query($sql);
		return $this->db->getLastId();
	}

	public function getCategory($data) {
		$sql = "SELECT * FROM " . DB_PREFIX . "category_description WHERE ";

		if (!empty($data['category_name'])) {
			$sql .= " name LIKE '" . $this->db->escape($data['category_name']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function check_category_type($category_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "category WHERE category_id= '".$category_id."'";	
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function check_category_type_multi($category_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "category WHERE parent_id= '".$category_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getGCMById($arr_user_id) {
	     $sql = "SELECT ws_gcm_registration_id FROM ". DB_PREFIX."customer 
	            WHERE customer_id IN (". implode(",", $arr_user_id).")";

        $query = $this->db->query($sql);
        $arr_gcm_id = array();
        foreach ($query->rows as $row) {
            if ($row['ws_gcm_registration_id'] != '') {
                $arr_gcm_id[] = $row['ws_gcm_registration_id'];
            }
        }
        return $arr_gcm_id;
    }
}
