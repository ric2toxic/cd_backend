<?php

require_once( DIR_SYSTEM . 'library/operations/orders/order_info.php' );

/**
 * Main Class for getting all OrderProduct Review Related Info.
 * It is recommended to utilize dynamic functions common overall 
 * @Author Nishu, 2018
 */
class OrderProductReview {
	/**
     * Save data for OrderProduct Review, if finds duplicate entery update previous data
     * @param: $data Array
     * @author: Nishu 2018
	*/
	public static function addOrderProductReview($db, $data = array())
	{
		//Not Empty check
		if(!empty($data)){
			
			//IP is not passed in $data
			if(empty($data['ip'])){
				$data['ip']  = getClientIpAddress();
            }
            
            //User agent is not passed in $data
			if(empty($data['user_agent'])){
				$data['user_agent']  = $_SERVER['HTTP_USER_AGENT'];
			}

			//Insert ... ON DUPLICATE KEY UPDATE ... Query
			   //update database row when inserting data for duplicate primary key
				// We have to update field value using update query
			$sql = "
                    INSERT INTO 
                     	" . DB_PREFIX . "order_product_review 
                    SET 
	                    order_product_id = ".(int)$data['order_product_id'].",
	                    product_review   = '". $db->escape($data['product_review']) ."',
	                    ip               = '". $db->escape($data['ip']) ."',
	                    user_agent       = '". $db->escape($data['user_agent']) ."'
 					ON DUPLICATE KEY
 						UPDATE 
 							product_review               = '". $db->escape($data['product_review']) ."',
 							product_review_date          = NOW(),
 							ip                           = '". $db->escape($data['ip']) ."',
 							user_agent                   = '". $db->escape($data['user_agent']) ."'
			       ";
			
			$db->query($sql);
		}

		return;
	}

	/**
     * Public method to get OrderProduct Review list
     * @param: $order_product_id, $suborder_id, $order_id\
     *   $order_product_id: can single value or an array
     *   $suborder_id: if order_product_id is empty, review of all order_products of given Suborder
     *   $order_id : if order_product_id is empty, review of all order_products of given order_id
     * @author: Nishu, Aug 2018
	*/
	public static function getOrderProductReview($db, $order_product_id, $suborder_id = '', $order_id = ''){
		$result = array();

		if(!empty($order_product_id) || !empty($suborder_id) || !empty($order_id)){
		    $whr    = '';
			//Check order_product_id single value or an array
			if(!empty($order_product_id)){
				if(is_array($order_product_id)){
					$whr .= " AND opr.order_product_id IN (".implode(',', $order_product_id). ") ";
				}else{
					$whr .= "AND opr.order_product_id = ".(int)$order_product_id ;
				}
			}else if(!empty($suborder_id)){
				$whr .= " AND op.suborder_id = '". $db->escape($suborder_id) ."' ";
			}else if(!empty($order_id)){
				$whr .= " AND op.order_id = ". (int)$order_id;
			}

			$sql = "
				     SELECT 
				     	opr.*
				     FROM
				     	" . DB_PREFIX . "order_product_review AS opr
				     INNER JOIN
				     	" . DB_PREFIX . "order_product AS op ON opr.order_product_id = op.order_product_id
				     WHERE
				     	opr.product_review IS NOT NULL
			       ". $whr;
			$data = $db->query($sql);
			if($data->num_rows > 0){
				$result = $data->rows;
				
				$result = array_combine(array_column($result, 'order_product_id'), $result);
			}
		}
		return $result;
	}
}
// close OrderProductReview class
?>
