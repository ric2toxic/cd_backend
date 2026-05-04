<?php
/**
 * CourierDelhivery
 *
 * @info - class to process Express Delivery API request and generate API response  
 *
 * @author @MSA (March 2019)
 */
class CourierDelhivery extends CourierBase
{
    public $api_key;
    public $tracking_url ; 
    public $category_of_goods = 'CLOTHS';
    /**
    * Class constructor
    * @params  Array  courier request parameters
    * @return  Object class object
    * @author  MSA, <1 March 19>
    */
    public function __construct($param) {

        parent::__construct($param);
        
        $this->post_mode        = 'REST';
        $this->post_type        = 'JSON';
        $this->response_type    = 'JSON';
        $this->api_end_point    = EXPRESS_API_URL;
        $this->api_key          = EXPRESS_API_KEY;
        $this->tracking_url     = EXPRESS_API_URL . EXPRESS_API_TRACK_PACKAGE;
    }
    
    /**
    * process() method to process API request and return response data
    * @return  Array API response data
    * @author  MSA, <1 March 19>
    */
    public function process()
    {
       $this->validateFormData();
       
       if(!empty($this->error)){
           return $this->error;
       }
       
        $this->generateApiRequestData();

        $this->request();
       
        if(!$this->validate_response()){
           return $this->error;
        }

        return $this->response();
    }

    /**
    * request() - inherited method, 
    * @Info - creating curl API request 
    * @author  MSA, <1 March 19>
    */
    public function request()
    {   
        $this->api_end_point = $this->api_end_point . EXPRESS_API_CREATE_PACKAGE;
        
        $post_fields = 'format=json&data='.json_encode($this->apiRequestString);

        $ch = curl_init($this->api_end_point);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());

        $result = curl_exec($ch);
        
        if(curl_error($ch)){
            return $response['error'] = curl_error($ch); 
        }
        
        curl_close($ch);
        
        $this->response = $result;

    }

    /**
    * getCurlHeaderType() - get curl header type - JSON, 
    * @Info - creating curl request headers
    * @author  MSA, <1 March 19>
    */
    protected function getRequestHeaders()
    {
       return array(
                    'Content-type: application/json',
                    'Authorization: Token ' . EXPRESS_API_KEY
                    );
    }
    
    /**
    * response() - inherited method, 
    * @Info - processing API response data
    * @return - Array API response data 
    * @author  MSA, <1 March 19>
    */
    public function response()
    {
        $json = json_decode($this->response,true); 
        
        $response = array();
        if( isset( $json['success'] ) &&  $json['success'])
        {
            if(!empty($json['packages'][0]['waybill'])) {
                $this->docket        = $json['packages'][0]['waybill']; 
                $response['success'] = 'Docket number generated : '.$this->docket;
            }else{
                $response['error'] = $json['rmk'] ?? 'Error : Waybill number not found in API response data!!';
            }
        }else{
             $response['error'] = $json['rmk'] ?? $json['detail'] ?? 'API error!!';
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
            
        }
        
        return $this->response;
    }
    
    /**
    * generateApiRequestString() method to create required tag with data to process in API
    * @author  MSA, <1 March 19>
    */
    protected function generateApiRequestData()
    {
        $request = array();
        $request['pickup_location'] = $this->getPickupLocation();
        $request['shipments'][] = $this->getShipmentData();    
        $this->apiRequestString = $request;
    }

    /**
    * getShipmentData() method to create required shipment data to process in API
    * @author  MSA, <1 March 19>
    */
    protected function getShipmentData()
    {
        $data = array(
            "return_name"   => $this->removeSpecialChars($this->warehouseInfo['warehouse_name']),
            "return_pin"    => $this->warehouseInfo['postcode'],
            "return_city"   => $this->removeSpecialChars($this->warehouseInfo['city']),
            "return_phone"  => $this->warehouseInfo['telephone'],
            "return_add"    => $this->removeSpecialChars($this->warehouseInfo['address_1']).' '.$this->removeSpecialChars($this->warehouseInfo['address_2']),
            "return_state"  => $this->removeSpecialChars($this->warehouseInfo['state']),
            "return_country"=> $this->warehouseInfo['country'],
            "order"         => substr(uniqid($this->orderInfo['order_no'].'-'),0,21),
            "phone"         => $this->orderInfo['telephone'],
            "products_desc" => "",
            "cod_amount"    => ( $this->isCod() ) ? ceil( $this->getNetPayable() ) : '0.0',
            "name"          => $this->removeSpecialChars($this->orderInfo['customer_name']),
            "country"       => $this->orderInfo['shipping_country'],
            "seller_inv_date"=> "",
            "order_date"    => date('Y-m-d h:i:s'),
            "total_amount"  => $this->getOrderTotal(),
            "seller_add"    => $this->removeSpecialChars($this->warehouseInfo['address_1']).' '.$this->removeSpecialChars($this->warehouseInfo['address_2']),
            "seller_cst"    => "",
            "add"           => $this->removeSpecialChars($this->orderInfo['shipping_address_1']).' '.$this->removeSpecialChars($this->orderInfo['shipping_address_2']).', '.$this->orderInfo['shipping_city'],
            "seller_name"   => $this->removeSpecialChars($this->warehouseInfo['warehouse_name']),
            "seller_inv"    => "",
            "seller_tin"    => trim($this->orderInfo['gstin']),
            "pin"           => $this->orderInfo['shipping_postcode'],
            "quantity"      => $this->request['no_of_pkg'],
            "payment_mode"  => $this->getPaymentMethod(),
            "state"         => $this->orderInfo['shipping_zone'],
            "city"          => $this->removeSpecialChars($this->orderInfo['shipping_city'])
        );
        return $data;
    }
    
    /**
    * getPackageItems() method to create required shipment items data to process in API
    * @author  MSA, <1 March 19>
    */
    protected function getPackageItems()
    {
        $data = array();
        if( !empty($this->orderInfo['itemdtl']) ) {

            foreach ($this->orderInfo['itemdtl'] as $key => $value) 
            {
                $data['item'][] = array(
                            'descr'  => $value['ItemName'],
                            'ean'    => $value['SKUNumber'],
                            'brand'  => 'WHOLESALEBOX',
                        ); 
            }
        }
       return $data;
    }

    /**
    * getPickupLocation() method to create required shipment pickup location data to process in API
    * @author  MSA, <1 March 19>
    */
    protected function getPickupLocation()
    {
       $data = array(
                "pin"           => $this->warehouseInfo['postcode'],
                "add"           => $this->removeSpecialChars($this->warehouseInfo['address_1']) . ' ' . $this->removeSpecialChars($this->warehouseInfo['address_2']),
                "phone"         => $this->warehouseInfo['telephone'],
                "state"         => $this->removeSpecialChars($this->warehouseInfo['state']),
                "city"          => $this->removeSpecialChars($this->warehouseInfo['city']),
                "country"       => $this->warehouseInfo['country'],
                "name"          => $this->getDelhiveryWarehouseClient($this->warehouseInfo['city']),
            );
        return $data;
    }

    /**
    * validateFormData() method to validate form data
    * @author  MSA, <March. 19>
    */
    public function validateFormData()
    {
        if(!isset($this->request['weight']) || trim($this->request['weight'])=='' || trim($this->request['weight'])=='0') 
        {
           $this->error['error'] = 'Weight value can not be 0' ;
        } 
        if(!isset($this->request['no_of_pkg']) || trim($this->request['no_of_pkg'])=='' || trim($this->request['no_of_pkg'])=='0') 
        {
           $this->error['error'] = 'Number of packages value can not be 0' ;
        }      
    }

    /**
    * addReverseShipment() - method to add reverse shipment data,
    * @author  MSA, <1 March. 19>
    */
    public function addReverseShipment($this_obj)
    {

       $this->validateReverseShipmentData();
       
       if(!empty($this->error)){
           return $this->error;
       }

       $this->generateReverseShipmentApiRequest();

       $this->request();

       $status = $this->reverseShipmentResponse($this_obj);

       return $status;

    }

    /**
    * validateReverseShipmentData() - method to validate reverse shipment form data,
    * @author  MSA, <1 March. 18>
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
               $this->error['error'] = 'Pincode value can not be blank or 0' ;
            } else{
               $pincode =  $this->request['pincode'];
            }
        }

        if(!empty($this->error)){
           return $this->error;
        }
        
        $sql = "SELECT `pickup` FROM "
                . DB_PREFIX . "delhivery_pincodes "
                . " WHERE pincode = '".$this->_db->escape($pincode)."' AND status = 1 " ;
        $query = $this->_db->query($sql);
        if ($query->num_rows) {
           $response['success'] = $query->row['pickup'];
        }else{
           $response['success'] = 0;
        }
        return $response;
    }

    /**
    * generateReverseShipmentApiRequest() - method to generate reverse shipment API request data
    * @author  MSA, <4 March. 18>
    */
    protected function generateReverseShipmentApiRequest()
    {
        $request = array();
        $request['pickup_location'] = $this->getReversePickupLocation();
        $request['shipments'][] = $this->getReverseShipmentData();    
        $this->apiRequestString = $request;
    }

     /**
    * getReversePickupLocation() method to create required reverse shipment pickup location data to process in API
    * @author  MSA, <1 March 19>
    */
    protected function getReversePickupLocation()
    {
       $data = array(
                "pin"           => $this->checkString(trim($this->request['cust_pin'])),
                "add"           => $this->removeSpecialChars($this->request['cust_address1']).', '.$this->removeSpecialChars($this->request['cust_address2']),
                "phone"         => $this->checkString(trim($this->request['cust_telephone'])),
                "state"         => $this->request['cust_zone'] ?? $this->orderInfo['order']['shipping_zone'] ?? '',
                "city"          => $this->removeSpecialChars(trim($this->request['cust_city'])),
                "country"       => $this->request['cust_country'] ?? $this->orderInfo['order']['shipping_country'] ?? '',
                "name"          => $this->getDelhiveryWarehouseClient($this->warehouseInfo['city']),
            );
        return $data;
    }

    /**
    * getReverseShipmentData() method to create required shipment data to process in API
    * @author  MSA, <1 March 19>
    */
    protected function getReverseShipmentData()
    {
        $data = array(
            "return_name"   => $this->removeSpecialChars($this->request['cust_name']),
            "return_pin"    => $this->request['cust_pin'],
            "return_city"   => $this->removeSpecialChars($this->request['cust_city']),
            "return_phone"  => $this->request['cust_telephone'],
            "return_add"    => $this->removeSpecialChars($this->request['cust_address1']).' '.$this->removeSpecialChars($this->request['cust_address2']),
            "return_state"  => $this->request['cust_zone'],
            "return_country"=> $this->request['cust_country'],
            "order"         => substr(uniqid($this->request['order_no'].'-'),0,21),
            "phone"         => $this->warehouseInfo['telephone'],
            "products_desc" => "",
            "cod_amount"    => '0.0',
            "name"          => $this->removeSpecialChars($this->warehouseInfo['warehouse_name']),
            "country"       => $this->warehouseInfo['country'],
            "seller_inv_date"=> "",
            "order_date"    => date('Y-m-d h:i:s'),
            "total_amount"  => $this->getReverseShipmentTotalAmount(),
            "seller_add"    => $this->removeSpecialChars($this->request['cust_address1']).' '.$this->removeSpecialChars($this->request['cust_address2']),
            "seller_cst"    => "",
            "add"           => $this->removeSpecialChars($this->warehouseInfo['address_1']).' '.$this->removeSpecialChars($this->warehouseInfo['address_2']).', '.$this->removeSpecialChars($this->warehouseInfo['city']),
            "seller_name"   => $this->removeSpecialChars($this->warehouseInfo['warehouse_name']),
            "seller_inv"    => "",
            "seller_tin"    => trim($this->warehouseInfo['gstin']),
            "pin"           => $this->warehouseInfo['postcode'],
            "quantity"      => $this->request['package_qty'],
            "payment_mode"  => 'Pickup',
            "state"         => $this->removeSpecialChars($this->warehouseInfo['state']),
            "city"          => $this->removeSpecialChars($this->warehouseInfo['city'])
        );
        return $data;
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
            
            $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

            /**
            * Load API response data XML in required xml format string to process data
            */
            $response_data = json_decode($this->response,true);
            
            if(!empty($response_data['success']) && !empty($response_data['packages'][0]['waybill']))
            {
                $this->courierDataToSaveInDB['aws_docket']  = $response_data['packages'][0]['waybill'];
            }  

            /* Save API request data, either API call success or failed, 
            * in both cases request data will be saved
            */
            $return_shipment_id = $this->saveReverseShipmentTrackingData();

            if(!empty($this->courierDataToSaveInDB['aws_docket']))
            {
                $status['success']    = 'Docket : ' . $this->courierDataToSaveInDB['aws_docket'];
               
                $this->updateReverseShipmentMasterReturnData($return_shipment_id);

                $this->updateReverseShipmentReturnData($return_shipment_id);
                
               /*checking for reverse shipment email */  
                $this->sendReverseShipmentEmailAlertMessage($this_obj);

            }else{

                $status['error']    = $json['rmk'] ?? $json['detail'] ?? 'API error!!';
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
        $this->tracking_url = $this->tracking_url .'?Authorization='.rawurlencode('Token '.EXPRESS_API_KEY)
                                                  .'&waybill='.$this->request['tracking_no']
                                                  .'&verbose=1';
        $ch = curl_init($this->tracking_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());
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
        if(!empty($this->response['ShipmentData'])) {
            $tracking_history['response'] = 'success';
            foreach($this->response['ShipmentData'] as $key => $value) {
                $tracking_history['history'] = $this->getTrackingHtml($value['Shipment']['Scans']);
            }   
        }else{
            $tracking_history['error'] = 'Tracking data not available.';
        }
        return json_encode($tracking_history);
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
                $html .=       '<th>Status</th>';
                $html .=    '</tr>';
                $html .=  '</thead>';
                $html .=  '<tbody>';

             foreach($tracking_detail as $key => $value) {
                $html .=   '<tr>';
                $html .=       '<th>'.$this->processDate($value['ScanDetail']['StatusDateTime']).'</th>';
                $html .=       '<th>'.$value['ScanDetail']['ScannedLocation'].'</th>';
                $html .=       '<th>'.$value['ScanDetail']['Instructions'].'</th>';
                $html .=    '</tr>';
             }
        } 
        return $html;
    }

    /**
    * processDate() - method to format date value
    * @author  MSA, <March. 18>
    */
    protected function processDate($date)
    {
        $date = str_replace(array('/'), array('-'), $date);
        return date('d-m-y h:i a',strtotime($date));
    }

    /**
    * Public method - addForwardShipment(), to generate forward shipment
    * @param  Object $this_obj
    * @return Array $status
    * @author MSA, <March. 18>
    */
    public function addForwardShipment($this_obj)
    {
        $this->validateReverseShipmentData();
       
       if(!empty($this->error)){
           return $this->error;
       }

       $this->generateBackToCustomerShipmentApiRequest();
       
       $this->request();
       
        if(!$this->validate_response()){
           return $this->error;
        }

        $status = $this->forwardShipmentResponse($this_obj);

        return $status;
    }

    /**
    * Protected method - generateBackToCustomerShipmentApiRequest(), to generate shipment api string
    * @return Void
    * @author MSA, <March. 18>
    */
    protected function generateBackToCustomerShipmentApiRequest()
    {
        $request = array();
        $request['pickup_location'] = $this->getBackToCustomerPickupLocation();
        $request['shipments'][] = $this->getBackToCustomerShipmentData();    
        $this->apiRequestString = $request;
    }

    /**
    * Protected method - getBackToCustomerPickupLocation(), to generate back to customer shipment pickup location
    * @return Void
    * @author MSA, <March. 18>
    */
    protected function getBackToCustomerPickupLocation()
    {
        $data = array(
                "pin"           => $this->warehouseInfo['postcode'],
                "add"           => $this->removeSpecialChars($this->warehouseInfo['address_1']) . ' ' . $this->removeSpecialChars($this->warehouseInfo['address_2']),
                "phone"         => $this->warehouseInfo['telephone'],
                "state"         => $this->removeSpecialChars($this->warehouseInfo['state']),
                "city"          => $this->removeSpecialChars($this->warehouseInfo['city']),
                "country"       => $this->warehouseInfo['country'],
                "name"          => $this->getDelhiveryWarehouseClient($this->warehouseInfo['city']),
            );
        return $data;
    }

    /**
    * Protected method - getBackToCustomerShipmentData(), to generate back to customer shipment api string
    * @return Void
    * @author MSA, <March. 18>
    */
    protected function getBackToCustomerShipmentData()
    {
        $data = array(
            "return_name"   => $this->removeSpecialChars($this->warehouseInfo['warehouse_name']),
            "return_pin"    => $this->warehouseInfo['postcode'],
            "return_city"   => $this->removeSpecialChars($this->warehouseInfo['city']),
            "return_phone"  => $this->warehouseInfo['telephone'],
            "return_add"    => $this->removeSpecialChars($this->warehouseInfo['address_1']).' '.$this->removeSpecialChars($this->warehouseInfo['address_2']),
            "return_state"  => $this->removeSpecialChars($this->warehouseInfo['state']),
            "return_country"=> $this->warehouseInfo['country'],
            "order"         => substr(uniqid($this->request['order_no'].'-'),0,21),
            "phone"         => $this->request['cust_telephone'],
            "products_desc" => "",
            "cod_amount"    => '0.0',
            "name"          => $this->removeSpecialChars($this->request['cust_name']),
            "country"       => $this->request['cust_country'],
            "seller_inv_date"=> "",
            "order_date"    => date('Y-m-d h:i:s'),
            "total_amount"  => $this->getReverseShipmentTotalAmount(),
            "seller_add"    => $this->removeSpecialChars($this->warehouseInfo['address_1']).' '.$this->removeSpecialChars($this->warehouseInfo['address_2']),
            "seller_cst"    => "",
            "add"           => $this->removeSpecialChars($this->request['cust_address1']).' '.$this->removeSpecialChars($this->request['cust_address2']).', '.$this->removeSpecialChars($this->warehouseInfo['city']),
            "seller_name"   => $this->removeSpecialChars($this->warehouseInfo['warehouse_name']),
            "seller_inv"    => "",
            "seller_tin"    => trim($this->warehouseInfo['gstin']),
            "pin"           => $this->request['cust_pin'],
            "quantity"      => $this->request['package_qty'],
            "payment_mode"  => 'PrePaid',
            "state"         => $this->removeSpecialChars($this->request['cust_zone']),
            "city"          => $this->removeSpecialChars($this->request['cust_city'])
        );
        return $data;
    }

    /**
    * Protected method - forwardShipmentResponse(), to process shipment api response data
    * @return Array $status
    * @author MSA, <March 19>
    */
    protected function forwardShipmentResponse($this_obj)
    {
         $status = array();

            $json = json_decode($this->response,true); 

            $response = array();

           if( isset( $json['success'] ) &&  $json['success'])
            {
                if(!empty($json['packages'][0]['waybill'])) {
                    $this->docket        = $json['packages'][0]['waybill']; 
                    $response['success'] = 'Docket number generated : '.$this->docket;
                }else{
                    $response['error'] = $json['rmk'] ?? 'Error : Waybill number not found in API response data!!';
                }
            }else{
                 $response['error'] = $json['rmk'] ?? $json['detail'] ?? 'API error!!';
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
            $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

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

    /**
    * Protected method - getDelhiveryWarehouseClient(), to get delhivery client name for warehouse city
    * @return string warehouse city
    * @author MSA, <March 19>
    */
    public function getDelhiveryWarehouseClient(string $warehouse_city): string
    {
        $delhivery_client = '';

        if(!empty($warehouse_city)) {

            if(array_key_exists(strtolower($warehouse_city), EXPRESS_API_CLIENTS)) {

                $delhivery_client = EXPRESS_API_CLIENTS[strtolower($warehouse_city)];
            }
        }
        return $delhivery_client;
    }


    public function removeSpecialChars(string $string)
    {
        $search = array("\\","&","#","%","$","&amp;");
        $replace = array("","","","","","");
        return str_replace($search, $replace, $string);
    }


}