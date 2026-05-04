<?php
require_once(DIR_SYSTEM .'library/shipment_tracking.php');
class ControllershipmentShipmentTracking extends Controller {
	
	public function scrapTracking(){
		if(isset($this->request->post)){
			$courier_partner = $this->request->post['courier_partner'];
		}
		$shipment_tracking = new ShipmentTracking($this);
		$get_data = $shipment_tracking->getSubOrdersByCourierPartner($courier_partner);
		if(!empty($get_data)){
			echo json_encode($get_data);
			exit;
		}else{
			return false;
		}
    }

	public function storeApi(){
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$shipment_tracking = new ShipmentTracking($this);
			if(isset($request['update_status']) && !empty($request['update_status'])){
				$this->updateOrderstatus($request['update_status']);
			}
			foreach($request['mysql_details'] as $value){
				$shipment_tracking->storeTrackingInfo($value);
			}
		}
	}

	public function updateOrderstatus($update_order){
		foreach($update_order as $value){
			$this->load->model('checkout/order');
			if($value['status_id'] == 17){
				$add_order_history['notify_email'] = 1;
				$add_order_history['notify_sms']   = 1;
			}
			$add_order_history['order_status_id'] = $value['status_id'];
			$add_order_history['order_id']  = 	$value['order_id'];
			$add_order_history['suborder_id'] = $value['suborder_id'];
			$add_order_history['notes'] = "Automatically Updated By Software";
			$add_order_history['updated_by'] = "software";
			$this->model_checkout_order->addOrderHistory($add_order_history);
		}
	}

	public function tracking(){
		$courier_partner = $this->request->get['courier_partner'];
		$docket_no       = $this->request->get['docket_no'];
		if(isset($courier_partner) && isset($docket_no)){
			$this->trackingGati($courier_partner,$docket_no);
		}
	}

	private function trackingGati($courier_partner,$docket_no){
		if(!empty($docket_no)){
			$shipment_tracking = new ShipmentTracking($this);
			if($courier_partner == 'gati'){
				$data = $shipment_tracking->trackingGati($docket_no);
				if(isset($data['Destination'])){
					$data['Delivered_At'] = $data['Destination'];
				}
			}
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/shipment/gati_tracking.tpl', $data));
		}
	}

	public function getFailedParcelData(){
		if(isset($this->request->post)){
			$inputJSON = file_get_contents('php://input');
			$request = json_decode($inputJSON, TRUE);
			$shipment_tracking = new ShipmentTracking($this);
			foreach($request as $values){
				$get_data[$values['order_id']] = $shipment_tracking->getFailedParcelData($values);
			}
			echo json_encode($get_data);
			exit;
		}
	}

	public function getFailedOrder(){
		$shipment_tracking = new ShipmentTracking($this);
		$get_data = $shipment_tracking->getFailedOrder();
		echo json_encode($get_data);
		exit;
	}

	/**
	* To get All shipment data for all courier partners
	* 
	* @return JSON of Data
	* @author MSA 4 Jan 2019
	*/
	public function scrapAllShipmentData()
	{
		if ($this->validate())
		{
			//Courier partner passed from command line in API URL
			$courier_partner = $this->request->get['courier_partner'] ?? '';

			$shipment_tracking = new ShipmentTracking($this);

			$get_data = $shipment_tracking->getShipmentsToTrack( $courier_partner );

			if(!empty($get_data)){
				echo json_encode($get_data);
				exit;
			}else{
				return false;
			}
		}
    }

    /**
    * Function to Validate the call of API requests made by systems
    *
    * @return boolean
    * @author MSA 4 Jan 2019
    */
    protected function validate()
    {
    	if(WSB_SHIPMENT_ACCESS_TOKEN == ($this->request->get['wsb_shipment_access_token']) )
    	{
    		return true;
    	} else {
    		echo"Please provide Access Key";
    		return false;
    	}
    }


}
