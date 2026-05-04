<?php
/**
 * CourierDotzot
 *
 * @info - class to process DotZot API request and generate API response  
 *
 * @author @MSA (Dec 2017)
 */

libxml_use_internal_errors(true);

class CourierDotzot extends CourierBase
{
    public $customer_id;
    public $xml_schema_url  = 'http://schemas.datacontract.org/2004/07/WebX.Entity';
    public $unit            = 'KG';
    public $type_of_delivery= 'Home Delivery';
    public $type_of_service = 'Express';
    public $agent_id;

    public $tracking_url ; //       = 'http://instacom.dotzot.in/GUI/Tracking/Track.aspx';
    
    /**
    * Class constructor
    * @params  Array  courier request parameters
    * @return  Object class object
    * @author  MSA, <25 Dec. 17>
    */
    public function __construct($param) {

        parent::__construct($param);
        
        $this->post_mode        = 'REST';
        $this->post_type        = 'XML';
        $this->response_type    = 'JSON';
        $this->api_end_point    = DotZot_API_LINK . 'PushOrderData_PUDO_New';
        $this->customer_id      = DotZot_API_CUSTCD;
        $this->agent_id         = '';
        $this->tracking_url     = DotZot_API_TRACKING_LINK . 'GetDocketTrackingDetails';
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
       if(strtolower($this->post_type) == 'xml'){
           $this->generateApiRequestStringXML();
       }else{
           $this->generateApiRequestStringJSON();
       }
       
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
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        
        if(strtolower($this->post_type) == 'xml'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->apiRequestString);
        }
        
        if(strtolower($this->post_type) == 'json'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->apiRequestString));
        }
        
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeaderType());
        $result = curl_exec($ch);
        
        if(curl_error($ch)){
            return $response['error'] = curl_error($ch); 
        }
        
        curl_close($ch);
        
        $this->response = $result;
    }
    
    /**
    * getCurlHeaderType() - get curl header type - XML / JSON, 
    * @Info - creating curl API request 
    * @author  MSA, <25 Dec. 17>
    */
    protected function getCurlHeaderType()
    {
        if(strtolower($this->post_type) == 'xml')
        {
            return array('Content-type: text/xml');
        }
        if(strtolower($this->post_type) == 'json')
        {
            return array('Content-type: application/json');
        }
    }
    
    /**
    * response() - inherited method, 
    * @Info - processing API response data
    * @return - Array API response data 
    * @author  MSA, <25 Dec. 17>
    */
    public function response()
    {
        $json = json_decode($this->response,true); 
        $response = array();
        if( isset( $json[0]['DockNo'] ) &&  $json[0]['DockNo'] != '')
        {
            $this->docket        = $json[0]['DockNo']; 
            $response['success'] = 'Docket number generated : '.$this->docket;
             
        }else{
             $response['error'] = isset($json[0]['Reason']) ? $json[0]['Reason'] : 'Error';
        }
        
        $this->apiResponseString = serialize($this->response);
        
        $response['docket']  = $this->docket;
        
        $response['result']  = $this->response;
        
        $this->response = $response;
        
        $this->setDBSaveData();
        
        if(!$this->saveCourierDocketData()){
            $this->response['error'] = 'Error occure while saving courier data.'  ;
        }
        
        if(isset($this->response['success'])){
            
            $this->response['shipping_label_id'] = $this->addShippingLabel();
            
         /*Set CRM Push Data For Docket Tracking*/  
            $this->crmRequestData['tracking_url']     = $this->tracking_url; // . '?AwbNos='.trim($this->docket);
            $this->updateOrderTrackingNoInCrm();
         /*Set CRM Push Data For Docket Tracking*/
            
        }
        
        return $this->response;
    }
    
    /**
    * generateApiRequestString() method to create required tag with data to process in API
    * @author  MSA, <25 Dec. 17>
    */
    protected function generateApiRequestStringXML()
    {
        try{
            
             $request = '<NewDataSet xmlns="'. $this->xml_schema_url .'">';
                $request .= '<Customer>';
                    $request .= '<BRCD></BRCD>';
                    $request .= '<CUSTCD>'.$this->customer_id.'</CUSTCD>';
                $request .= '</Customer>';

                $request .= '<DocketList>';

                if( isset( $this->orderInfo['itemdtl'] ) ) {

                    $TotalAmount = $this->getOrderTotal();

                    if(strtolower($this->getPaymentMethod()) == 'prepaid') {
                        $CollectableAmount = 0;
                        $api_payment_mode = 'P';
                    }else {
                        $CollectableAmount = ceil($this->getNetPayable()); 
                        $api_payment_mode = 'C';
                    }

                    $Itemquantity = 0;
                    $ProductCode = '';

                    foreach ($this->orderInfo['itemdtl'] as $key => $value)
                    {
                        $Itemquantity += $value['Itemquantity'];
                        $ProductCode   = $value['SKUNumber'];

                    }    
                        $request .= '<DocketList>';

                            $request .= '<AgentID></AgentID>';
                            $request .= '<AwbNo></AwbNo>';
                            $request .= '<Breath>1</Breath>';

                            $request .= '<CollectableAmount>' . $CollectableAmount . '</CollectableAmount>';
                            $request .= '<CustomerName>' . $this->checkString($this->orderInfo['customer_name']) . '</CustomerName>';
                            $request .= '<Height>0</Height>';
                            $request .= '<IsPudo>N</IsPudo>';

                            $request .= '<ItemName>CLOTHS</ItemName>';
                            $request .= '<Length>0</Length>';
                            $request .= '<Mode>' . $api_payment_mode . '</Mode>';
                            
                            if(!empty($this->request['no_of_pkg'])) {
                                $request .= '<NoOfPieces>' . $this->request['no_of_pkg'] . '</NoOfPieces>';
                            }else{
                                $request .= '<NoOfPieces>1</NoOfPieces>';
                            }

                            $request .= '<OrderConformation>Y</OrderConformation>';
                            $request .= '<OrderNo>' . $this->orderInfo['order_no'] . '</OrderNo>';
                            $request .= '<ProductCode>' . $ProductCode . '</ProductCode>';
                            $request .= '<PudoId></PudoId>';
                            $request .= '<RateCalculation>N</RateCalculation>';

                            $request .= '<ShippingAdd1>' . $this->checkString($this->orderInfo['shipping_address_1']) . '</ShippingAdd1>';
                            $request .= '<ShippingAdd2>' . $this->checkString($this->orderInfo['shipping_address_2']) . '</ShippingAdd2>';
                            $request .= '<ShippingCity>' . $this->checkString($this->orderInfo['shipping_city']) . '</ShippingCity>';
                            $request .= '<ShippingEmailId>' . $this->validate_email($this->orderInfo['customer_email']) . '</ShippingEmailId>';
                            $request .= '<ShippingMobileNo>' . $this->orderInfo['telephone'] . '</ShippingMobileNo>';
                            $request .= '<ShippingState>' . $this->checkString($this->orderInfo['shipping_zone']) . '</ShippingState>';
                            $request .= '<ShippingTelephoneNo>' . $this->orderInfo['telephone'] . '</ShippingTelephoneNo>';
                            $request .= '<ShippingZip>' . $this->orderInfo['shipping_postcode'] . '</ShippingZip>';

                            $request .= '<TotalAmount>' . $TotalAmount . '</TotalAmount>';
                            $request .= '<TypeOfDelivery>Home Delivery</TypeOfDelivery>';
                            
                            if(!empty($this->request['service_type'])) {
                                $request .= '<TypeOfService>'.$this->request['service_type'].'</TypeOfService>';   
                            }
                            
                            $request .= '<UOM>Per '.$this->unit.'</UOM>';
                            $request .= '<VendorAddress1>' . $this->checkString($this->warehouseInfo['address_1']) . '</VendorAddress1>';
                            $request .= '<VendorAddress2>' . $this->checkString($this->warehouseInfo['address_2']) . '</VendorAddress2>';
                            $request .= '<VendorName>' . $this->checkString($this->warehouseInfo['warehouse_name']) . '</VendorName>';
                            $request .= '<VendorPincode>' . $this->warehouseInfo['postcode'] . '</VendorPincode>';
                            $request .= '<VendorTeleNo>' . $this->warehouseInfo['telephone'] . '</VendorTeleNo>';

                            $request .= '<Weight>' . $this->request['weight'] . '</Weight>';
                        $request .= '</DocketList>';

                    }

                $request .= '</DocketList>';
             $request .=  '</NewDataSet>';

             $this->apiRequestString = $request;
        }catch(Exception $e){
            
            return $response['error'] = $e->getMessage();
            
        }
    }
    
    /**
    * generateApiRequestString() method to create required tag with data to process in API
    * @author  MSA, <25 Dec. 17>
    */
    protected function generateApiRequestStringJSON()
    {
        $request = array();
        $request['Customer'] = array(
            'CUSTCD' => $this->customer_id
        );
        
        $request['DocketList'][] = $this->getDocketList();
        
         $this->apiRequestString = $request;
    }
    
    /**
    * getDocketList() method to create docket item list in API
    * @author  MSA, <25 Dec. 17>
    */
    public function getDocketList()
    {
        $request = array();

        if( isset( $this->orderInfo['itemdtl'] ) ) {

            $request = array(
                'AgentID'               => $this->getAgentID(),
                'AwbNo'                 => '',
                'Breath'                => 1,
                
                'CollectableAmount'     => $this->isCod() ? ceil($this->getNetPayable()) : '0',
                
                'CustomerName'          => $this->checkString($this->orderInfo['customer_name']),
                
                'Height'                => 1,
                'IsPudo'                => 'N',
                'ItemName'              => 'CLOTHS',
                'Length'                => 1,
                
                'Mode'                  => $this->isCod() ? 'C' : 'P' ,
                'NoOfPieces'            => $this->getItemQuantity(),
                'OrderConformation'     => 'Y',
                'OrderNo'               => $this->suborder_id,
                'ProductCode'           => $this->getProductCode()
,               'PudoId'                => '',
                'RateCalculation'       => 'N',
                
                'ShippingAdd1'          => $this->checkString($this->orderInfo['shipping_address_1']),
                'ShippingAdd2'          =>  (trim($this->orderInfo['shipping_address_2'])!='')
                                            ? $this->checkString($this->orderInfo['shipping_address_2'])
                                            :$this->checkString($this->orderInfo['shipping_address_1']),
                
                'ShippingCity'          => $this->checkString($this->orderInfo['shipping_city']),
                'ShippingEmailId'       => $this->validate_email($this->orderInfo['customer_email']),
                'ShippingMobileNo'      => $this->orderInfo['telephone'],
                'ShippingState'         => $this->checkString($this->orderInfo['shipping_zone']),
                'ShippingTelephoneNo'   => $this->orderInfo['telephone'],
                'ShippingZip'           => $this->orderInfo['shipping_postcode'],
                
                'TotalAmount'           => $this->getOrderTotal(),
                
                'TypeOfDelivery'        => $this->type_of_delivery,
                'TypeOfService'         => $this->type_of_service,
                
                'UOM'                   => 'Per ' . $this->unit,
                
                'VendorAddress1'        => $this->checkString($this->warehouseInfo['address_1']),
                'VendorAddress2'        => $this->checkString($this->warehouseInfo['address_2']),
                'VendorName'            => $this->checkString($this->warehouseInfo['warehouse_name']),
                'VendorPincode'         => $this->warehouseInfo['postcode'],
                'VendorTeleNo'          => $this->warehouseInfo['telephone'],
                
                'Weight'                => $this->request['weight']
                
            );
        }
        return $request;
    }
    
    /**
    * getAgentID() method to set agent id in API request data
    * @author  MSA, <Jan. 17>
    */
    protected function getAgentID()
    {
        return $this->agent_id;
    }
    
    /**
    * getProductCode() method to set product code in API request data
    * @author  MSA, <Jan. 17>
    */
    protected function getProductCode()
    {
        if(!empty($this->orderInfo['itemdtl']))
        {
            return isset($this->orderInfo['itemdtl'][0]['SKUNumber'])
                    ? $this->orderInfo['itemdtl'][0]['SKUNumber']
                    : '';
        }
    }
    
    /**
    * getItemQuantity() method to set item quantity in API request data
    * @author  MSA, <Jan. 17>
    */
    protected function getItemQuantity()
    {
        $Itemquantity = 1;
        if(!empty($this->orderInfo['itemdtl']))
        {
            foreach ($this->orderInfo['itemdtl'] as $key => $value)
            {
                $Itemquantity += $value['Itemquantity'];
            }
        }
        return $Itemquantity;
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
    }
    
    /**
    * isPincodeServiceable() - method to check for serviceable pin code area,
    * @Info  method to check for serviceable pin code area
    * @author  MSA, <Feb. 18>
    */
    public function isPincodeServiceable($pincode = '', $product = 'Economy')
    {
        if($pincode=='') {
            if(!isset($this->request['pincode']) || trim($this->request['pincode'])=='' || trim($this->request['pincode'])=='0') 
            {
               $this->error['error'] = 'Pincode value can not be blank or 0' ;
            } else{
               $pincode =  $this->request['pincode'];
            }
        }

        if(!empty($this->error)){
           return $this->error;
        }
        
        $where = "";
        if(!empty($this->request['serviceability_type'])) {
            $product = (strtolower(trim($this->request['serviceability_type'])) == 'surface') 
                        ? 'Economy' 
                        : 'Express';
            $where = " AND product = '".$this->_db->escape($product)."' ";
        }else{
            $where = " AND product = '".$this->_db->escape(trim($product))."' ";
        }

        $sql = "SELECT 
                    `reverse_pickup` 
                FROM 
                    ". DB_PREFIX . "dotzot_pincodes 
                WHERE 
                    pincode = '".$this->_db->escape($pincode)."' 
                    AND 
                    status = 1 
                " . $where;

        $query = $this->_db->query($sql);

        if ($query->num_rows) {
           $response['success'] = $query->row['reverse_pickup'];
        }else{
           $response['success'] = 0;
        }
        return $response;
    }
    

    /**
    * addReverseShipment() - method to add reverse shipment data,
    * @author  MSA, <March. 18>
    */
    public function addReverseShipment($this_obj)
    {

       $this->validateReverseShipmentData();
       
       if(!empty($this->error)){
           return $this->error;
       }

       $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

       $this->generateReverseShipmentApiRequest();
       
       $this->reverseShipmentRequest();
       
       $status = $this->reverseShipmentResponse($this_obj);

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
            $mode = $this->request['service_type'] ?? 'Economy';
            $isServiceable = $this->isPincodeServiceable($this->request['cust_pin'], $mode);
            if(!$isServiceable['success']){
                $this->error['error'] = $this->request['cust_pin'].' PIN is not serviceable.';
            }
         }else{
            $this->error['error'] = 'Customer pincode not found!!';
         }
        /* check pincode for serviceable status */  
    }

    /**
    * generateReverseShipmentApiRequest() - method to generate reverse shipment API request data
    * @author  MSA, <March. 18>
    */
    protected function generateReverseShipmentApiRequest()
    {
       
        $request = array();

        $request['ClientId'] = DOTZOT_API_CLIENT_ID;
        $request['UserName'] = DOTZOT_API_USERNAME;
        $request['Password'] = DOTZOT_API_PASSWORD;
        
        if(isset($this->request['selected_master_return_id'])) {
           $request['RequestId'] = $this->request['order_no'] . '_' . $this->request['selected_master_return_id'];   
        }
        if(isset($this->request['cust_name'])) {
           $request['ConsignorName']       = $this->checkString($this->request['cust_name']);    
        }
        if(isset($this->request['cust_address1'])) {
            $request['ConsignorAddress1']      = $this->checkString($this->request['cust_address1']);
        }
        if(isset($this->request['cust_address2'])) {
           $request['ConsignorAddress2']   = $this->checkString($this->request['cust_address2']);           
        }
        if(isset($this->request['cust_telephone'])) {
            $request['MobileNo']        = str_replace(['(+91)',' '],['',''],trim($this->request['cust_telephone']));    
        }
        if(isset($this->request['cust_pin'])) {
            $request['Pincode']       = $this->request['cust_pin'];    
        }

        $request['SkuDescription']      = 'CLOTHS';
        $request['DeclaredValue']       = $this->getReverseShipmentTotalAmount();;
        $request['AgentId']             = '';
        $request['CustomerCode']        = $this->customer_id;

        if(isset($this->warehouseInfo['warehouse_name'])) {
            $request['VendorName']          = $this->checkString($this->warehouseInfo['warehouse_name']);    
        }
        if(isset($this->warehouseInfo['address_1'])) {
            $request['VendorAddress1']      = $this->checkString($this->warehouseInfo['address_1']);
        }
        if(isset($this->warehouseInfo['address_2'])) {
            $request['VendorAddress2']      = $this->checkString($this->warehouseInfo['address_2']);    
        }
        if(isset($this->warehouseInfo['postcode'])) {
            $request['VendorPincode']       = $this->warehouseInfo['postcode'];    
        }
        if(isset($this->warehouseInfo['telephone'])) {
            $request['VendorTeleNo']        = str_replace(['(+91)',' '],['',''],trim($this->warehouseInfo['telephone']));    
        }

        $request['TransportMode']       = !empty($this->request['service_type'])?$this->request['service_type']:'Express';
        $request['ItemChecked']         = 'N';
        $request['DockNo']              = '';

        $this->apiRequestString = $request;

    }

    /**
    * reverseShipmentRequest() - method to send reverse shipment API request
    * @author  MSA, <March. 18>
    */
    public function reverseShipmentRequest()
    {
        $this->api_end_point = DOTZOT_REVERSE_API_LINK . '/PushReverseOrderData_PUDO';

        if(!empty($this->apiRequestString)) {

            $post_string = '';
            
            foreach($this->apiRequestString as $key=>$value) {

                $post_string .= $key . '=' . $value . '&';
            }
            
            $ch = curl_init($this->api_end_point);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
            $result = curl_exec($ch);
            
            if(curl_error($ch)){
                return $response['error'] = curl_error($ch); 
            }
            
            curl_close($ch);

            $this->response = $result;

        }else{
            $this->response['error'] = 'Error in order data, Please check order details';
        }
    }
    
    /**
    * reverseShipmentResponse() - method to process reverse shipment API response data
    * @author  MSA, <March. 18>
    */
    public function reverseShipmentResponse($this_obj)
    {
        $status = array();
        $return_shipment_id = 0;

            /* Update DB tables*/
            $this->courierDataToSaveInDB['aws_docket']  = '';
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
            
            if(empty($this->courierDataToSaveInDB['return_products'])){
                $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();
            }

            /**
            * Load API response data XML in required xml format string to process data
            */
            $response_xml = simplexml_load_string(simplexml_load_string($this->response));
            
            if(isset($response_xml->ORDER->Succeed) && strtolower((string)$response_xml->ORDER->Succeed) === 'yes')
            {
                $this->courierDataToSaveInDB['aws_docket']  = (string)$response_xml->ORDER->DOCKNO;
            }  

            /* Save API request data, either API call success or failed, 
            * in both cases request data will be saved
            */
            $return_shipment_id = $this->saveReverseShipmentTrackingData();

            if(!empty($this->courierDataToSaveInDB['aws_docket']))
            {
                $status['success']    = (string)$response_xml->ORDER->Reason;

                //$this->saveReverseShipmentDocketData();
                
                $this->updateReverseShipmentMasterReturnData($return_shipment_id);

                $this->updateReverseShipmentReturnData($return_shipment_id);
                
               /*checking for reverse shipment email */  
                $this->sendReverseShipmentEmailAlertMessage($this_obj);


            }else if(!empty($response_xml->ORDER->Reason)){

                $status['error']    = (string)$response_xml->ORDER->Reason; 

            }else{
                $xml_error = simplexml_load_string($this->response);
                $status['error']    = (string) $xml_error;
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
        $post_string = json_encode(['DocketNo' => $this->docket]);
        $ch = curl_init($this->tracking_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        
        if(curl_error($ch)){
            return $response['error'] = curl_error($ch); 
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
        if(!empty($this->response)) {
            $tracking_history['response'] = 'success';
            foreach($this->response as $key => $value) {
                $tracking_history['history'] = $this->getTrackingHtml($value['Detail']);
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
    protected function processDate($date,$time)
    {
        $date = str_replace(array('/'), array('-'), $date . ' ' . $time);
        return date('d-m-y h:i a',strtotime($date));
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
                $html .=      '<th>Date Time</th>';
                $html .=       '<th>Current Location</th>';
                $html .=       '<th>Next Location</th>';
                $html .=       '<th>Status</th>';
                $html .=    '</tr>';
                $html .=  '</thead>';
                $html .=  '<tbody>';

             foreach($tracking_detail as $key => $value) {
                $html .=   '<tr>';
                $html .=       '<th>'.$this->processDate($value['EVENTDATE'],$value['EVENTTIME']).'</th>';
                $html .=       '<th>'.$value['CURRENT_CITY'].'</th>';
                $html .=       '<th>'.$value['NEXT_LOCATION'].'</th>';
                $html .=       '<th>'.$value['CURRENT_STATUS'].'</th>';
                $html .=    '</tr>';
             }
        } 
        return $html;
    }

    /**
    * Public method - addForwardShipment(), to generate forward shipment
    * @param  Object $this_obj
    * @return Array $status
    * @author  MSA, June 18
    */
    public function addForwardShipment($this_obj)
    {
        $this->validateReverseShipmentData();

       if(!empty($this->error)){
           return $this->error;
       }

       $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

       $this->generateForwardShipmentApiRequest();
       
       $this->request();
       
        if(!$this->validate_response()){
           return $this->error;
        }

        $status = $this->forwardShipmentResponse($this_obj);

        return $status;
    }

    /**
    * Protected method - generateForwardShipmentApiRequest(), to generate shipment api string
    * @return Void
    * @author MSA, June 18
    */
    protected function generateForwardShipmentApiRequest()
    {
        try{
             $request = '<NewDataSet xmlns="'. $this->xml_schema_url .'">';
                $request .= '<Customer>';
                    $request .= '<BRCD></BRCD>';
                    $request .= '<CUSTCD>'.$this->customer_id.'</CUSTCD>';
                $request .= '</Customer>';

                $request .= '<DocketList>';

                if(!empty($this->courierDataToSaveInDB['return_products'])) {
                    $returnProducts = $this->courierDataToSaveInDB['return_products'];
                }else{
                    $returnProducts = $this->getReturnProducts();    
                }

                if( !empty( $returnProducts ) ) {

                    $TotalAmount        = $this->getReverseShipmentTotalAmount();
                    $CollectableAmount  = 0;
                    $api_payment_mode   = 'P';
                    $Itemquantity       = 0;
                    $ProductCode        = '';
                    $TypeOfService      = !empty($this->request['service_type'])?$this->request['service_type']:'Economy';

                    foreach ($returnProducts as $key => $value)
                    {
                        $Itemquantity += $value['quantity'];
                         $ProductCode  = $value['model'];
                    }    
                    $request .= '<DocketList>';
                        $request .= '<AgentID></AgentID>';
                        $request .= '<AwbNo></AwbNo>';
                        $request .= '<Breath>1</Breath>';
                        $request .= '<CollectableAmount>' . $CollectableAmount . '</CollectableAmount>';
                        $request .= '<CustomerName>' . $this->checkString($this->request['cust_name']) . '</CustomerName>';
                        $request .= '<Height>0</Height>';
                        $request .= '<IsPudo>N</IsPudo>';
                        $request .= '<ItemName>CLOTHS</ItemName>';
                        $request .= '<Length>0</Length>';
                        $request .= '<Mode>' . $api_payment_mode . '</Mode>';
                        $request .= '<NoOfPieces>1</NoOfPieces>';
                        $request .= '<OrderConformation>Y</OrderConformation>';
                        $request .= '<OrderNo>' . $this->request['order_no'] . '</OrderNo>';
                        $request .= '<ProductCode>' . $ProductCode . '</ProductCode>';
                        $request .= '<PudoId></PudoId>';
                        $request .= '<RateCalculation>N</RateCalculation>';
                        $request .= '<ShippingAdd1>' . $this->checkString($this->request['cust_address1']) . '</ShippingAdd1>';
                        $request .= '<ShippingAdd2>' . $this->checkString($this->request['cust_address2']) . '</ShippingAdd2>';
                        $request .= '<ShippingCity>' . $this->checkString($this->request['cust_city']) . '</ShippingCity>';
                        $request .= '<ShippingEmailId>' . $this->validate_email($this->request['cust_email']) . '</ShippingEmailId>';
                        $request .= '<ShippingMobileNo>' . $this->request['cust_telephone'] . '</ShippingMobileNo>';
                        $request .= '<ShippingState>' . $this->checkString($this->getStateCodeByZoneId($this->request['cust_zone_id'])) . '</ShippingState>';
                        $request .= '<ShippingTelephoneNo>' . $this->request['cust_telephone'] . '</ShippingTelephoneNo>';
                        $request .= '<ShippingZip>' . $this->request['cust_pin'] . '</ShippingZip>';
                        $request .= '<TotalAmount>' . $TotalAmount . '</TotalAmount>';
                        $request .= '<TypeOfDelivery>Home Delivery</TypeOfDelivery>';
                        $request .= '<TypeOfService>'.$TypeOfService.'</TypeOfService>';   
                        $request .= '<UOM>Per '.$this->unit.'</UOM>';
                        $request .= '<VendorAddress1>' . $this->checkString($this->warehouseInfo['address_1']) . '</VendorAddress1>';
                        $request .= '<VendorAddress2>' . $this->checkString($this->warehouseInfo['address_2']) . '</VendorAddress2>';
                        $request .= '<VendorName>' . $this->checkString($this->warehouseInfo['warehouse_name']) . '</VendorName>';
                        $request .= '<VendorPincode>' . $this->warehouseInfo['postcode'] . '</VendorPincode>';
                        $request .= '<VendorTeleNo>' . $this->warehouseInfo['telephone'] . '</VendorTeleNo>';
                        $request .= '<Weight>' . $this->request['package_weight'] . '</Weight>';
                    $request .= '</DocketList>';

                    }

                $request .= '</DocketList>';
             $request .=  '</NewDataSet>';

             $this->apiRequestString = $request;

        }catch(Exception $e){
            
            return $response['error'] = $e->getMessage();
            
        }

    }

    /**
    * Protected method - forwardShipmentResponse(), to process shipment api response data
    * @return Array $status
    * @author MSA, June 18
    */
    protected function forwardShipmentResponse($this_obj)
    {
         $status = array();

            $json = json_decode($this->response,true); 

            $response = array();

            if( isset( $json[0]['DockNo'] ) &&  $json[0]['DockNo'] != '')
            {
                $this->docket        = $json[0]['DockNo']; 
                $response['success'] = 'Docket number generated : '.$this->docket;
                 
            }else{
                 $response['error'] = isset($json[0]['Reason']) ? $json[0]['Reason'] : 'Error';
            }
            
            $this->apiResponseString = serialize($this->response);
            
            $response['docket']  = $this->docket;
            
            $response['result']  = $this->response;

            /* Update DB tables*/
            $this->courierDataToSaveInDB['aws_docket']  = $response['docket'];
            $this->courierDataToSaveInDB['return_ids']  = $this->request['return_ids'];
            $this->courierDataToSaveInDB['status']      = 'New';
            $this->courierDataToSaveInDB['req']         = $this->apiRequestString;
            $this->courierDataToSaveInDB['res']         = $response['result'];
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
            
            if(empty($this->courierDataToSaveInDB['return_products'])) {
                $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();
            }

            $return_shipment_id = $this->saveForwardShipmentTrackingData();

            if(isset($response['success']))
            {
                $status['success']    = (string)$response['success'];

                $this->updateReverseShipmentReturnData(
                                                    $return_shipment_id, 
                                                    RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
                                                    true
                                                );
                
               /*checking for reverse shipment email */  
                $this->sendForwardShipmentEmailAlertMessage($this_obj);
            }else{
                $status['error'] = $response['error'];
            }

        return $status;
    }

}