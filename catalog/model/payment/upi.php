<?php 
	class ModelPaymentUPI extends Model 
	{  
		public function getMethod($address, $total) 
		{
			$this->load->language('payment/upi');
			$method_data = array();
			$status = true;
			if ($status) 
			{        
				$method_data = array(
					'code'=> 'upi',
					'title'=> $this->language->get('text_title'),
					'terms' => '',
					'sort_order' => $this->config->get('upi_sort_order'));
			}       
			return $method_data; 
		}

		public function getOrderPaymentDetailsfromOrderID($order_id){

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_payment` 
			                           WHERE order_id = '" . (int)$order_id . "' ORDER BY payment_id DESC LIMIT 1");
			
	        if ($query->rows) {
	            return $query->rows;
	        } else {
	            //return $order_id;
	            throw new Exception("Invalid link generation request.");
	        }
		}

		public function getOrderPaymentDetailsfromMerchTxnID($merchant_txn_id){
			$sql = "SELECT *
                    FROM
                    ".DB_PREFIX."order_payment
                    WHERE merchant_txn_id = '". $this->db->escape($merchant_txn_id)."'";              
        	$result = $this->db->query($sql);
        	return $result;
		}

		public function updateOrderPaymentDetails($responseStatus, $json_format, $payment_id, $successfull){
			$sql = "UPDATE
	                        ".DB_PREFIX."order_payment 
	                        SET txn_status = '".$this->db->escape($responseStatus)."',
	                        json_format = '".$this->db->escape($json_format)."',
	                        successfull = '".$this->db->escape($successfull)."'
	                        WHERE payment_id = '".$payment_id."'";              
	        $update_result = $this->db->query($sql);
	        return $update_result;
		}

	}
?>