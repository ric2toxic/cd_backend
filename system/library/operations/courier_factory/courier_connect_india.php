<?php
/**
 * CourierConnectIndia
 *
 * @info - class to process ConnectIndia API request and generate api response  
 *
 * @author @MSA (Dec 2017)
 */
class CourierConnectIndia extends CourierBase
{
    /*Auth parameters*/
    private $api_access_id;
    private $api_access_key;
    
    /*Other required fixed parameters*/
    public $category;
    public $operationModelCategory;
    public $shipType;
    public $consigneeAddressType;
    public $originType;
    public $handoverClientLocationType;
    public $estimatedTimeArrivalInDays;
    public $consignmentDescription;
    
    public $tracking_url        = 'https://prod.connectindia.com/api/trackConsignment';
    
    /**
    * Class constructor
    * @params  Array  courier request parameters
    * @return  Object class object
    * @author  MSA, <25 Dec. 17>
    */
    public function __construct($param) {

        parent::__construct($param);
        
        /*API Auth setting*/
        $this->api_access_id  = CONNECT_INDIA_ACCESS_ID;
        $this->api_access_key = CONNECT_INDIA_ACCESS_KEY;
        $this->api_end_point  = CONNECT_INDIA_API_URL; 
        
        $this->post_mode        = 'REST';
        $this->post_type        = 'JSON';
        $this->response_type    = 'JSON';
        
        /*Other fixed setting*/
        $this->category                 = 'ECOMMERCE';
        $this->operationModelCategory   = 'BRANCH_DELIVERY'; //{ BRANCH_DELIVERY , CLIENT_ASSOCIATE_CONSIGNMENTS }
        $this->shipType                 = 'DELIVERY';// { PICKUP , DELIVERY , PICKUP_AND_DELIVER } 
        $this->consigneeAddressType     = 'COMMERCIAL';// { RESIDENTIAL , COMMERCIAL }      
        $this->originType               = 'PRE_CONFIGURED_CLIENT';// { PRE_CONFIGURED_OWN , PRE_CONFIGURED_CLIENT , ON_DEMAND } 
        $this->handoverClientLocationType= 'RETURN_TO_ORIGIN'; //{ PRE_CONFIGURED_OWN , PRE_CONFIGURED_CLIENT , ON_DEMAND , RETURN_TO_ORIGIN } 
        $this->estimatedTimeArrivalInDays= 5;
        $this->consignmentDescription   = 'CLOTHS';
    }
    
    /**
    * process() method to process API request and return response data
    * @return  Array API response data
    * @author  MSA, <25 Dec. 17>
    */
    public function process()
    {
        $this->validateFormData();
       
        if(!empty($this->error)){
           return $this->error;
        }
        
        $this->generateApiRequestString();

        $this->request();
        
        if(!$this->validate_response()){
           return $this->error;
        }
        
       return $this->response();
    }
    
    /**
    * request() - inherited method, 
    * @Info - creating curl API request 
    * @author  MSA, <25 Dec. 17>
    */
    public function request()
    {
        $ch = curl_init($this->api_end_point);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->apiRequestString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-TR-ACCESS-ID:'.$this->api_access_id,
                'X-TR-ACCESS-KEY:'.$this->api_access_key,
                'Content-type: application/json'
            ));
        $result = curl_exec($ch);
        if(curl_error($ch))
        {
            return $response['error'] = curl_error($ch); 
        }
        curl_close($ch);
        
        $this->response = json_decode($result);
    }
    
    /**
    * response() - inherited method, 
    * @Info - processing API response data
    * @return - Array API response data 
    * @author  MSA, <25 Dec. 17>
    */
    public function response()
    {
        $response = array();
        
        if (isset($this->response->status) &&  $this->response->status === 'OK'){
            
            if (isset($this->response->consignments) && is_array($this->response->consignments)){
            
                if(isset( $this->response->consignments[0]->status ) && 
                    $this->response->consignments[0]->status === 'ERROR'
                  ) {
                    $response['error']     = isset( $this->response->consignments[0]->error->description ) 
                                                        ? $this->response->consignments[0]->error->description 
                                                        : '' ;
                } else { // Status - OK / DUPLICATE

                    $docket     = isset( $this->response->consignments[0]->awbNumber )
                                        ? $this->response->consignments[0]->awbNumber
                                        : '' ;
                    $this->docket = $docket;
                    $response['docket']    =  $docket;
                    $response['success']   = 'Docket number generated : '.$docket;
                }
            }
            
        } else if( isset($this->response->status) &&  $this->response->status === 'ERROR' )  {

            if(isset($this->response->error->description)) {

                $response['error']     = $this->response->error->description;

            } else { 

                $response['error'] = 'API error!';

            }
        }
        
        $this->apiResponseString = serialize($this->response);
        
        $this->response = $response;
        
        $this->setDBSaveData();
        
        if(!$this->saveCourierDocketData()){
            $this->response['error'] = 'Error occure while saving courier data.'  ;
        }
        
        if(isset($response['success'])){
            
            $this->response['shipping_label_id'] = $this->addShippingLabel();
            
         /*Set CRM Push Data For Docket Tracking*/  
            $this->crmRequestData['tracking_url']     = $this->tracking_url;
            $this->updateOrderTrackingNoInCrm();
         /*Set CRM Push Data For Docket Tracking*/
            
        }
        
        return $this->response;
    }
    
    /**
    * generateApiRequestString() method to create required tag with data to process in API
    * @author  MSA, <25 Dec. 17>
    */
   protected function generateApiRequestString()
    {
        $request = array();
        
        $request['transactionId'] = time();
        $request['consignments'][] = $this->getConsignments();
        
        $this->apiRequestString = $request;
    }
    
    
    /**
    * getConsignments() method to set consignments in API request data
    * @author  MSA, <25 Dec. 17>
    */
    protected function getConsignments()
    {
        $consignments = array();
        
        if(isset($this->suborder_id) && $this->suborder_id !=''){
            $consignments['consignmentId']  = $this->suborder_id;
            $consignments['orderId']        = $this->suborder_id;
            $consignments['trackingId']     = $this->suborder_id;
        }
        
        $consignments['category']               = $this->category;
        $consignments['operationModelCategory'] = $this->operationModelCategory;
        
        if(isset($this->request['weight'])) {
            $consignments['weightInKilogram']  = $this->request['weight'];
        }
        if(isset($this->request['length'])) {
            $consignments['lengthInMeter']  = convertUnit($this->request['length'], 'CM_TO_METER');
        }
        if(isset($this->request['width'])) {
            $consignments['widthInMeter']  = convertUnit($this->request['width'], 'CM_TO_METER');
        }
        if(isset($this->request['height'])) {
            $consignments['heightInMeter']  = convertUnit($this->request['height'], 'CM_TO_METER');
        }
        if(isset($this->orderInfo['InvoiceNo'])) {
            $consignments['referenceId']  = $this->orderInfo['InvoiceNo'];
        }
 
        $consignments['shipType']           = $this->shipType;
        $consignments['paymentType']        = strtoupper($this->getPaymentMethod());
       
        if( $this->isCod() && $this->getNetPayable() > 0 ) {
            $consignments['receivableAmount']  = $this->getNetPayable();
        }else{
            $consignments['receivableAmount']  = 0;
        }
        if(isset($this->orderInfo['customer_name'])) {
            $consignments['consigneeName']      = $this->orderInfo['customer_name'];
        }
        $consignments['consigneeAddressType']   = $this->consigneeAddressType;
        
        if(isset($this->orderInfo['shipping_address_1'])) {
            $consignments['consigneeAddressLine1']  = $this->checkString($this->orderInfo['shipping_address_1']);
        }
        if(isset($this->orderInfo['shipping_address_2']) && trim($this->orderInfo['shipping_address_2'])!='') {
            $consignments['consigneeAddressLine2']  = $this->checkString($this->orderInfo['shipping_address_2']);
        }else{
            $consignments['consigneeAddressLine2']  = $this->checkString($this->orderInfo['shipping_address_1']);
        }
        if(isset($this->orderInfo['shipping_city'])) {
            $consignments['consigneeDistrict']  = $this->checkString($this->orderInfo['shipping_city']);
            $consignments['consigneeCity']      = $this->checkString($this->orderInfo['shipping_city']);
        }
        if(isset($this->orderInfo['shipping_zone'])) {
           $consignments['consigneeState']  = $this->checkString($this->orderInfo['shipping_zone']); 
        }
        if(isset($this->orderInfo['shipping_country'])) {
           $consignments['consigneeCountry']  = $this->checkString($this->orderInfo['shipping_country']); 
        }
        if(isset($this->orderInfo['shipping_postcode'])) {
           $consignments['consigneePincode']  = $this->checkString($this->orderInfo['shipping_postcode']); 
        }
        if(isset($this->orderInfo['telephone'])) {
           $consignments['consigneePrimaryTelephone']  = $this->orderInfo['telephone']; 
        }
        
        $consignments['originType']  = $this->originType;
        if(isset($this->warehouseInfo['connect_india_origin_code'])) {
           $consignments['originCode']  = $this->warehouseInfo['connect_india_origin_code']; 
        }
        if(isset($this->warehouseInfo['telephone'])) {
           $consignments['originPhone']  = str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone']); 
        }
        if(isset($this->warehouseInfo['address_1'])) {
           $consignments['originAddressLine1']  = $this->checkString($this->warehouseInfo['address_1']); 
        }
        if(isset($this->warehouseInfo['address_2'])) {
           $consignments['originAddressLine2']  = $this->checkString($this->warehouseInfo['address_2']); 
        }
        if(isset($this->warehouseInfo['postcode'])) {
           $consignments['originPincode']  = $this->checkString($this->warehouseInfo['postcode']); 
        }
        if(isset($this->warehouseInfo['city'])) {
           $consignments['originCity']  = $this->checkString($this->warehouseInfo['city']); 
        }
        if(isset($this->warehouseInfo['state'])) {
           $consignments['originState']  = $this->checkString($this->warehouseInfo['state']); 
        }
        $consignments['handoverClientLocationType']  = $this->handoverClientLocationType;
        if(isset($this->warehouseInfo['state'])) {        
            $consignments['dispatchTime'] = strtotime($this->request['pickup_date']);
        }else{
            $consignments['dispatchTime'] = time();
        }
        $consignments['estimatedTimeOfArrival'] = $this->estimatedTimeArrivalInDays;
        $consignments['consignmentValue']       = $this->getOrderTotal();
        $consignments['consignmentDescription'] = $this->consignmentDescription;
        
        return $consignments;
    }
 
    
    /**
    * validateFormData() method to validate form data
    * @author  MSA, <Jan. 17>
    */
    public function validateFormData()
    {
        if(!isset($this->request['weight']) || trim($this->request['weight'])=='' || trim($this->request['weight'])=='0') 
        {
           $this->error['error'] = 'Weight value can not be 0' ;
        }         
        if(!isset($this->request['width']) || trim($this->request['width'])=='' || trim($this->request['width'])=='0') 
        {
           $this->error['error'] = 'Width value can not be 0' ;
        }        
        if(!isset($this->request['length']) || trim($this->request['length'])=='' || trim($this->request['length'])=='0') 
        {
           $this->error['error'] = 'Length value can not be 0' ;
        }
        if(!isset($this->request['height']) || trim($this->request['height'])=='' || trim($this->request['height'])=='0') 
        {
           $this->error['error'] = 'Height value can not be 0' ;
        }
    }
    
    /**
    * isPincodeServiceable() - method to check for serviceable pin code area,
    * @Info  method to check for serviceable pin code area
    * @author  MSA, <Feb. 18>
    */
    public function isPincodeServiceable($pincode = '')
    {
        if($pincode=='') {
            if(!isset($this->request['pincode']) || trim($this->request['pincode'])=='' || trim($this->request['pincode'])=='0') 
            {
               $this->error['error'] = 'Pincode value can not be blank or o' ;
            } else {
               $pincode =  $this->request['pincode'];
            }
        }
        if(!empty($this->error)){
           return $this->error;
        }
        
        $sql = "SELECT `id` FROM "
                . DB_PREFIX . "connect_india_pincodes "
                . " WHERE pincode = '".$this->_db->escape($pincode)."' " ;
        $query = $this->_db->query($sql);
        if ($query->num_rows) {
           $response['success'] = 1;
        }else{
           $response['success'] = 0;
        }
        return $response;
    }


    /* Return forward shipment generation */
    public function addForwardShipment($this_obj)
    {
        $this->validateReverseShipmentData();
       
       if(!empty($this->error)){
           return $this->error;
       }

       $this->generateForwardShipmentApiRequest();
       
       $this->request();
       
        if(!$this->validate_response()){
           return $this->error;
        }

        $status = $this->forwardShipmentResponse($this_obj);

        return $status;
    }



    /**
    * validateReverseShipmentData() - method to validate reverse shipment form data,
    * @author  MSA, <March. 18>
    */
    protected function validateReverseShipmentData()
    {
        $this->validateReverseShipmentFormData();
         /* check pincode for serviceable status */
         if(!empty($this->request['cust_pin'])) {
            $isServiceable = $this->isPincodeServiceable($this->request['cust_pin']);
            if(!$isServiceable['success']){
                $this->error['error'] = $this->request['cust_pin'].' PIN is not serviceable.';
            }
         }else{
            $this->error['error'] = 'Customer pincode not found!!';
         }
        /* check pincode for serviceable status */  
    }

    protected function generateForwardShipmentApiRequest()
    {
        $request = array();
        
        $request['transactionId'] = time();
        $request['consignments'][] = $this->getForwardConsignments();
        
        $this->apiRequestString = $request;
    }

    /**
    * getConsignments() method to set consignments in API request data
    * @author  MSA, <May 18>
    */
    protected function getForwardConsignments()
    {
        $consignments = array();
        
        if(isset($this->request['order_no'])) {
            $consignments['consignmentId']  = $this->request['order_no'];
        } 
        if(isset($this->request['order_id'])) {
            $consignments['orderId']        = $this->request['order_id'];
        }
        if(isset($this->request['order_no'])) {
            $consignments['trackingId']     = $this->request['order_no'];    
        }
        
        $consignments['category']               = $this->category;
        $consignments['operationModelCategory'] = $this->operationModelCategory;
        
        if(isset($this->request['package_weight'])) {
            $consignments['weightInKilogram']  = $this->request['package_weight'];
        }

        $consignments['lengthInMeter']      = '15';
        $consignments['widthInMeter']       = '15';
        $consignments['heightInMeter']      = '15';
        $consignments['referenceId']        = $this->getInvoiceNumberInReverseShipment();
        $consignments['shipType']           = $this->shipType;
        $consignments['paymentType']        = 'PREPAID';
        $consignments['receivableAmount']   = 0;

        if(isset($this->request['cust_name'])) {
            $consignments['consigneeName']      = $this->request['cust_name'];
        }
        $consignments['consigneeAddressType']   = $this->consigneeAddressType;
        
        if(isset($this->request['cust_address1'])) {
            $consignments['consigneeAddressLine1']  = $this->checkString($this->request['cust_address1']);
        }
        if(isset($this->request['cust_address2']) && trim($this->request['cust_address2'])!='') {
            $consignments['consigneeAddressLine2']  = $this->checkString($this->request['cust_address2']);
        }else{
            $consignments['consigneeAddressLine2']  = $this->checkString($this->request['cust_address1']);
        }
        if(isset($this->request['cust_city'])) {
            $consignments['consigneeDistrict']  = $this->checkString($this->request['cust_city']);
            $consignments['consigneeCity']      = $this->checkString($this->request['cust_city']);
        }
        if(isset($this->request['cust_zone_id'])) {
           $consignments['consigneeState']      = $this->getStateCodeByZoneId($this->request['cust_zone_id']); 
        }
        if(isset($this->request['shipping_country'])) {
           $consignments['consigneeCountry']    = 'INDIA'; 
        }
        if(isset($this->request['cust_pin'])) {
           $consignments['consigneePincode']    = $this->checkString($this->request['cust_pin']); 
        }
        if(isset($this->request['cust_telephone'])) {
           $consignments['consigneePrimaryTelephone']  = $this->request['cust_telephone']; 
        }
        
        $consignments['originType']  = $this->originType;

        if(isset($this->warehouseInfo['connect_india_origin_code'])) {
           $consignments['originCode']  = $this->warehouseInfo['connect_india_origin_code']; 
        }
        if(isset($this->warehouseInfo['telephone'])) {
           $consignments['originPhone']  = str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone']); 
        }
        if(isset($this->warehouseInfo['address_1'])) {
           $consignments['originAddressLine1']  = $this->checkString($this->warehouseInfo['address_1']); 
        }
        if(isset($this->warehouseInfo['address_2'])) {
           $consignments['originAddressLine2']  = $this->checkString($this->warehouseInfo['address_2']); 
        }
        if(isset($this->warehouseInfo['postcode'])) {
           $consignments['originPincode']  = $this->checkString($this->warehouseInfo['postcode']); 
        }
        if(isset($this->warehouseInfo['city'])) {
           $consignments['originCity']  = $this->checkString($this->warehouseInfo['city']); 
        }
        if(isset($this->warehouseInfo['state'])) {
           $consignments['originState']  = $this->checkString($this->warehouseInfo['state']); 
        }
        $consignments['handoverClientLocationType']  = $this->handoverClientLocationType;
        
        $consignments['dispatchTime']           = time();
        $consignments['estimatedTimeOfArrival'] = $this->estimatedTimeArrivalInDays;
        $consignments['consignmentValue']       = $this->getReverseShipmentTotalAmount();
        $consignments['consignmentDescription'] = $this->consignmentDescription;
        
        return $consignments;
    }

    protected function forwardShipmentResponse($this_obj)
    {
         $status = array();

        if(!empty($this->response)) {

            if (isset($this->response->status) &&  $this->response->status === 'OK'){
            
                if (isset($this->response->consignments) && is_array($this->response->consignments)){
                
                    if(isset( $this->response->consignments[0]->status ) 
                        && 
                        $this->response->consignments[0]->status === 'ERROR'
                      ) {

                        $response['error']     = isset( $this->response->consignments[0]->error->description ) 
                                                            ? $this->response->consignments[0]->error->description 
                                                            : '' ;
                    } else { // Status - OK / DUPLICATE

                        $docket     = isset( $this->response->consignments[0]->awbNumber )
                                            ? $this->response->consignments[0]->awbNumber
                                            : '' ;
                        $this->docket = $docket;
                        $response['docket']    =  $docket;
                        $response['success']   = 'Docket number generated : '.$docket;
                    }
                }
                
            } else if( isset($this->response->status) &&  $this->response->status === 'ERROR' )  {

                if(isset($this->response->error->description)) {

                    $response['error']     = $this->response->error->description;

                } else { 

                    $response['error'] = 'API error!';

                }
            }

            if(isset($response['success']))
            {
                $status['success']    = (string)$response['success'];

                /* Update DB tables*/
                $this->courierDataToSaveInDB['aws_docket']  = $response['docket'];
                $this->courierDataToSaveInDB['status']      = 'New';
                $this->courierDataToSaveInDB['req']         = $this->apiRequestString;
                $this->courierDataToSaveInDB['res']         = $this->response;
                $this->courierDataToSaveInDB['req_res']     = serialize(array(
                                                                    $this->courierDataToSaveInDB['req'],
                                                                    $this->courierDataToSaveInDB['res']
                                                                    ));
                $this->courierDataToSaveInDB['master_return_id'] = $this->request['selected_master_return_id'];
                $this->courierDataToSaveInDB['total_amount']     = $this->getReverseShipmentTotalAmount();
                $this->courierDataToSaveInDB['courier_company']  = $this->request['courier_partner'];
                $this->courierDataToSaveInDB['order_id']         = $this->request['order_id'];
                $this->courierDataToSaveInDB['order_no']         = $this->request['order_no'];
                $this->courierDataToSaveInDB['customer_id']      = $this->request['customer_id'];
                $this->courierDataToSaveInDB['qty']              = $this->request['package_qty'];
                $this->courierDataToSaveInDB['req']              = $this->apiRequestString;
                $this->courierDataToSaveInDB['remarks']          = $this->request['remarks'];
                $this->courierDataToSaveInDB['return_reason']    = $this->request['return_reason'];
                $this->courierDataToSaveInDB['weight']           = $this->request['package_weight'];
                $this->courierDataToSaveInDB['vendor_code']      = $this->warehouseInfo['bluedart_vendor_code'];
                $this->courierDataToSaveInDB['warehouse_id']     = $this->request['select_warehouse_id'];
                $this->courierDataToSaveInDB['package_desc']     = $this->request['package_desc'];
                $this->courierDataToSaveInDB['cust_telephone']   = $this->request['cust_telephone'];
                $this->courierDataToSaveInDB['cust_name']        = $this->request['cust_name'];
                $this->courierDataToSaveInDB['cust_address1']    = $this->request['cust_address1'];
                $this->courierDataToSaveInDB['cust_address2']    = $this->request['cust_address2'];
                $this->courierDataToSaveInDB['cust_city']        = $this->request['cust_city'];
                $this->courierDataToSaveInDB['cust_pin']         = $this->request['cust_pin'];
                
                $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();
                
                //$this->saveReverseShipmentDocketData();
                $return_shipment_id = $this->saveForwardShipmentTrackingData();
                $this->updateReverseShipmentReturnData(
                                                    $return_shipment_id, 
                                                    RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
                                                    true
                                                );
                
               /*checking for reverse shipment email */  
                $this->sendForwardShipmentEmailAlertMessage($this_obj);

            }else{
                $status['error']    = (string)$response_xml->ORDER->Reason; 
            }

        }else{
            $status['error']    = 'ConnectIndia API not responding!!';
        }
        return $status;
    }    


    /**
    * getDocketTrackingHistory() - method to get courier docket tracking history data
    * @author  MSA, <March. 18>
    */
    public function getDocketTrackingHistory()
    {
        $this->validateTrackingData();

        if(!empty($this->error)){
           return $this->error;
        }

       $this->docketTrackingRequest();
       
       $tracking_history = $this->docketTrackingResponse();

       echo $tracking_history; exit;
    }

    /**
    * docketTrackingRequest() - method to send docket tracking request
    * @author  MSA, <March. 18>
    */
    protected function docketTrackingRequest()
    {
        /*LIVE Credentials*/
            $this->tracking_url   = $this->tracking_url . '?shipmentId=' . $this->docket;
            $this->api_access_id  = 'r8pwXuLSPZJgwCUJYcnTnGHFtU8sI'; //CONNECT_INDIA_ACCESS_ID
            $this->api_access_key = 'pOkfZR12IiaV5rfXt1qyeTqA7UMxOsF8Bwm'; //CONNECT_INDIA_ACCESS_KEY
        /*LIVE Credentials*/
        $ch = curl_init($this->tracking_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-TR-ACCESS-ID:'.$this->api_access_id,
                'X-TR-ACCESS-KEY:'.$this->api_access_key,
                'Content-type: application/json'
            ));    
        $result = curl_exec($ch);
        $response = array();
        if(curl_error($ch)){
            $response['error'] = curl_error($ch); 
        }
        
        curl_close($ch);

        $this->response = json_decode($result,true);
    }

    /**
    * docketTrackingResponse() - method to process docket tracking API response
    * @author  MSA, <March. 18>
    */
    protected function docketTrackingResponse()
    {
        $tracking_history = array();
        if(!empty($this->response['consignments'][0]['trackingDetails'])) {
            $tracking_history['response'] = 'success';
            foreach($this->response['consignments'] as $key => $value) {
                $tracking_history['history'] = $this->getTrackingHtml($value['trackingDetails']);
            }   
        }else{
            $tracking_history['error'] = 'Tracking data not available.';
        }
        return json_encode($tracking_history);
    }

    /**
    * processDate() - method to format date value
    * @author  MSA, <March. 18>
    */
    protected function processDate($timeStamp='')
    {
        return date('d-m-y h:i a',strtotime($timeStamp));
    }

    /**
    * getTrackingHtml() - method generate tracking history html
    * @author  MSA, <March. 18>
    */
    protected function getTrackingHtml($tracking_detail)
    {
        $html = '';
        if(!empty($tracking_detail)) {
                $html .= '<thead>';
                $html .=   '<tr>';
                $html .=      '<th>Time</th>';
                $html .=       '<th>Location</th>';
                $html .=       '<th>Step Description</th>';
                $html .=    '</tr>';
                $html .=  '</thead>';
                $html .=  '<tbody>';
             foreach($tracking_detail as $key => $value) {
                $location = isset($value['location'])?$value['location']:'';
                $html .=   '<tr>';
                $html .=       '<th>'.$this->processDate($value['timeStamp']).'</th>';
                $html .=       '<th>'.$location.'</th>';
                $html .=       '<th>'.$value['stepDescription'].'</th>';
                $html .=    '</tr>';
             }
        } 
        return $html;
    }

}