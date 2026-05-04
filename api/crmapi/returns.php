<?php 

 // Configuration
if (is_file(__DIR__.'/../../config.php')) {
    require_once(__DIR__.'/../../config.php');
}

require_once DIR_API."/../system.php";
require_once DIR_API."/../data_packet.php";

/**
 * Returns controller
 * use for get returns to crm 
 */
class ReturnsController extends SystemController
{    
    public function __construct($params) {

        parent::__construct($params);
        
    }
    
    /**
      * getReturns
      * get master returns and its all details in a single array
      * @param : $order_id
      * @return: array 
    */
    public function getReturns($order_id=array()){
		
        $order_id = $this->request['args'];
        $refund_amount = 0;
        $weight_per_piece = 0;
        $refund = 0;
        $return_quantity = 0;
        $flag = 'crmapi';
        $price_per_piece = 0;
        $discount_per_piece = 0;
        $output_tax_rates = 0;

        $this->load->model('account/return');
        $this->load->model('restapi/return');
        $this->load->model('tool/image');
        
        $master_returns = $this->model_restapi_return->getMasterReturns(0, (int)$order_id);

        $master_return_ids = array_column($master_returns->rows,'master_return_id');
        
        $master_return_data  = array_combine($master_return_ids,$master_returns->rows);        

        $result = $this->model_account_return->getReturnsByOrderId($master_return_ids,'', $flag);        

        $total_product_details = $this->model_account_return->getOrderProducts($order_id);        
       
        $total_product_ids = array_column($total_product_details,'order_product_id');

        $combine_product_ids  = array_combine($total_product_ids,$total_product_details);        
        
        $arr_push = array();
        
		$i = 0;
		
		//echo "<pre>"; print_r($result); exit;
		
		if (!empty($result)) {
		   
			foreach ($result->rows as $key => $value) {         

                // pr($value); die;
				
				if (!in_array($value['order_product_id'],$arr_push)) {
					
                    if (isset($combine_product_ids[$value['order_product_id']]['price_per_piece'])) {
                        $price_per_piece = $combine_product_ids[$value['order_product_id']]['price_per_piece'];
                    }

                    if(isset($combine_product_ids[$value['order_product_id']]['discount_per_piece'])) {					
					   $discount_per_piece = $combine_product_ids[$value['order_product_id']]['discount_per_piece'];
                    }
					
					if (isset($combine_product_ids[$value['order_product_id']]['output_tax_rates'])) {
                        $output_tax_rates = $combine_product_ids[$value['order_product_id']]['output_tax_rates'];
                    }			
					
					$return_quantity = $value['quantity'];

					$_quality_return_reason_id = array( 
    										RETURN_REASON_IDS['Quality_Issue'], 
    										RETURN_REASON_IDS['Pricing_Issue'], 
    										RETURN_REASON_IDS['Wrong_Item_Received']
    									);

					if(in_array($value['return_reason_id'],$_quality_return_reason_id)) {
						
						if (isset($combine_product_ids[$value['order_product_id']]['weight_per_piece'])) {
                            $weight_per_piece = $combine_product_ids[$value['order_product_id']]['weight_per_piece'];
                        }
						
						$refund = ( ( (float)$price_per_piece +  (float)$discount_per_piece ) *
													( 1 + (float)$output_tax_rates / 100 ) * (int)$value['quantity'] );
					} else {
						$refund       = 0;
						$weight_per_piece = 0;	
					}
					
					//address
					$address 	 = isset($value['address']) ? $value['address'] : '';
					$address 	.= isset($value['city']) ? ", ".$value['city'] : '';
					$address 	.= isset($value['zone_name']) ? ", ".$value['zone_name'] : '';
					$address 	.= isset($value['postcode']) ? ", ".$value['postcode'] : '';
					
					$_return_reason_text = array(
                                         RETURN_REASON_IDS['Manufacturing_Defect']  => "Manufacturing defect/damaged goods",
                                         RETURN_REASON_IDS['Quality_Issue']         => "Quality issue",
                                         RETURN_REASON_IDS['Pricing_Issue']         => "Pricing issue",
                                         RETURN_REASON_IDS['Wrong_Item_Received']   => "Worng Itmes Recevied",
                                         RETURN_REASON_IDS['Wrong_Item_Received_Replacement'] => "Wrong Item Received(Replacement)"
                                        );

					$master_return_id = $value['master_return_id'];
					
					@$rt_data['all_returns'][$master_return_id]['return_product'][$i]['product_model'] = $combine_product_ids[$value['order_product_id']]['model'];
					
					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['return_no'] = $master_return_data[$master_return_id]['return_no'];
					
					@$rt_data['all_returns'][$master_return_id]['return_product'][$i]['product_name'] = $combine_product_ids[$value['order_product_id']]['name'];
					
					@$rt_data['all_returns'][$master_return_id]['return_product'][$i]['product_quantity'] = $combine_product_ids[$value['order_product_id']]['quantity']*$combine_product_ids[$value['order_product_id']]['piece_in_set'];
					
					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['product_price_per_piece'] = $price_per_piece;
					
					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['reason_for_return'] = $_return_reason_text[$value['return_reason_id']];
					
					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['return_quantity'] = $value['quantity'];

					
					if (isset($combine_product_ids[$value['order_product_id']]['image'])) {									

						$img_url = $this->model_tool_image->resize($combine_product_ids[$value['order_product_id']]['image'], '74', '111');
						
					} else {
						$img_url = $this->model_tool_image->resize('placeholder.png', '74', '111');	              
					}
					  

					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['image'] = isset($img_url) ? $img_url : '';
					
					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['status'] = isset($value['product_return_status']) ? $value['product_return_status'] : ''; 
					
					$rt_data['all_returns'][$master_return_id]['return_product'][$i]['comment'] = isset($value['comment']) ? $value['comment'] : '';
					
					@$rt_data['all_returns'][$master_return_id]['total_weight']+=(float)$weight_per_piece * (int)$return_quantity;
					
					@$rt_data['all_returns'][$master_return_id]['total_refund']+=$refund;
					
					@$rt_data['all_returns'][$master_return_id]['shipping_method'] = $master_return_data[$master_return_id]['shipping_method'];
					
					@$rt_data['all_returns'][$master_return_id]['total_return_quantity']+=$return_quantity;
					
					$rt_data['all_returns'][$master_return_id]['address'] = $address;
					
					$i++;
					
					$arr_push[] = $value['order_product_id'];
				}
			}


			foreach($rt_data['all_returns'] as $master_return_id => $return_product_data) {			
				
				//pickup address
				$pickup_address = $return_product_data['address'];
				
				// shipping method
				if ($return_product_data['shipping_method'] == 'wsb_pickup') {
						$pickup_type = 'Wholesalebox Pickup';
						$total_weight = ceil($return_product_data['total_weight']);
						if( $total_weight >= 1 ){
							$shipping_charge = ($total_weight - 1) * 30 + 70;
						}else{
							$shipping_charge = "Rs. 0.00";
						}
				} else if($return_product_data['shipping_method'] == 'self_courier') {				
						$pickup_address = "Wholesalebox Pvt. Ltd. B-1, Crystal Mall, Banipark,Jaipur-302016 Rajasthan";
						$shipping_charge = 0;
						$pickup_type = 'Other';
				} else {
						$shipping_charge = 0;
						$pickup_type = 'Other';
				}
				
				// calc refund amount
				$refund_amount = $return_product_data['total_refund'];		
				
				if($refund_amount != 0){
					$refund_amount = (float)$refund_amount - (float)$shipping_charge;
				}else{
					$refund_amount = 0;
				}
				
				
				// shipment history
				$tracking_url = '';
				$shipment_history = array();
				
				if (!empty($master_return_data[$master_return_id]['tracking_no']) && !empty($master_return_data[$master_return_id]['courier_company'])) 
				{						
					switch ($master_return_data[$master_return_id]['courier_company']) 
					{
						case "NuvoEx":
							$shipment_history = $this->getShipmentHistory($master_return_data[$master_return_id]['tracking_no'],$master_return_data[$master_return_id]['courier_company']);							
							break;												
							
						default:
						   $tracking_url = $this->model_restapi_return->getShipTrackingURL($master_return_data[$master_return_id]['courier_company']);
					}					
				}

				$rt_data['all_returns'][$master_return_id]['calculate_values']['shipping_charge'] = $shipping_charge;
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['pickup_type'] = $pickup_type;
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['return_no'] = $master_return_data[$master_return_id]['return_no'];
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['pickup_address'] = $pickup_address;
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['total_refund_replacement_pieces'] = $return_product_data['total_return_quantity'];
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['shipping_adjustment'] = $shipping_charge;

				$rt_data['all_returns'][$master_return_id]['calculate_values']['return_product_amount'] = $return_product_data['total_refund'];
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['tentative_refund'] = $refund_amount;

				$rt_data['all_returns'][$master_return_id]['calculate_values']['tracking_no'] = $master_return_data[$master_return_id]['tracking_no'];
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['courier_company'] = $master_return_data[$master_return_id]['courier_company'];
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['order_no'] = $master_return_data[$master_return_id]['order_no'];			
				
				$rt_data['all_returns'][$master_return_id]['calculate_values']['tracking_url'] = $tracking_url;
	
				$rt_data['all_returns'][$master_return_id]['shipping_history'] = $shipment_history;
				   
			}
		} else {
			$rt_data = (object) array();
		}		

        // Get correct return product array
        if (is_array($rt_data) && !empty($rt_data)) {
            
            foreach ($rt_data['all_returns'] as $key => $master_return_id_value) {
                
                $rt_data['all_returns'][$key]['return_product'] = array_values($rt_data['all_returns'][$key]['return_product']);
            }
        }
        return $rt_data;exit;
	}

    /**
     * getShipmentHistory
     * get shipment history by nuvoex api
     * @param : tracking_no, courier_company
     * @return: array
     */
    public function getShipmentHistory($tracking_no, $courier_company) {                  

        $url = NUVOEX_BASE_URL.$tracking_no;
        $auth = base64_encode(NUVOEX_USER.":".NUVOEX_PASSWORD);
        $headers = array
          (
            "Authorization: Basic $auth",
            "Content-Type: application/json"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);       
        curl_close($ch);
        $api_result = json_decode($output, true);
        return !empty($api_result['history']) ? $api_result['history'] : array();
    } 
}
?>
