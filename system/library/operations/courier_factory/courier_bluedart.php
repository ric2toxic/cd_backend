<?php
/**
 * CourierBluedart
 *
 * @info - class to process Blue Dart API's and generate API response  
 *
 * @author @MSA (Dec 2017)
 */
class CourierBluedart extends CourierBase
{
    private $api_login_id;
    private $api_username;
    private $api_license_key;
    private $api_version;
    private $api_type;
    
    public $soap_client;
    public $soap_client_header;
    public $soap_client_header_location;
    public $soap_client_action_uri;

    public $product_code       = 'E';
    public $product_type       = 'Dutiables';
    public $sub_product_code   = 'P';
    public $customer_name      = 'Wholesalebox';
    
    public $tracking_url        = 'http://www.bluedart.com/servlet/RoutingServlet';
    
    /** 
    * Class constructor
    * @params  Array  courier request parameters
    * @return  Object class object
    * @author  MSA, <26 Dec. 17>
    */
    public function __construct($param) {

        parent::__construct($param);
        
    /*Soap authentication setting */
        //$this->api_login_id     = BLUE_DART_LOGINID;
        $this->api_username     = BLUE_DART_USERNAME;
        //$this->api_license_key  = BLUE_DART_LICENSE_KEY;
        $this->api_version      = BLUE_DART_VERSION_NUMBER;
        $this->api_type         = BLUE_DART_API_TYPE;
    /*Soap client setting */
        $this->soap_client        = BLUE_DART_SOAP_CLIENT;
        $this->soap_client_header =  BLUE_DART_SOAP_CLIENT_HEADER;
        $this->soap_client_header_location  = BLUE_DART_SOAP_CLIENT_HEADER_LOCATION;
        $this->soap_client_action_uri       = BLUE_DART_SOAP_CLIENT_ACTION_URI;
        
        $this->post_mode        = 'SOAP';
        $this->post_type        = 'JSON';
        $this->response_type    = 'JSON';
    }
    
    /**
    * process() method to process API request and return response data
    * @return  Array API response data
    * @author  MSA, <26 Dec. 17>
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
    * @author  MSA, <26 Dec. 17>
    */
    public function request()
    {
       try{
            $this->api_end_point = $this->soap_client_action_uri . 'IWayBillGeneration/GenerateWayBill';
            $soap_action = $this->soap_client_action_uri . 'IWayBillGeneration/GenerateWayBill';
            $soap = new SoapClient($this->soap_client,array('trace' => 1, 'style' => SOAP_DOCUMENT,'use' => SOAP_LITERAL, 'soap_version' => SOAP_1_2));
            $soap->__setLocation($this->soap_client_header_location);
            $actionHeader = new SoapHeader($this->soap_client_header,'Action',$soap_action,true);
            $soap->__setSoapHeaders($actionHeader);	
            $this->response = $soap->__soapCall('GenerateWayBill',array($this->apiRequestString));
       } catch (SoapFault $exception) {
            $this->response['error'] = json_encode($exception);
        }
    }

    /**
    * response() - inherited method, 
    * @Info - processing API response data
    * @return - Array API response data 
    * @author  MSA, <26 Dec. 17>
    */
    public function response()
    {

        $response = array();
        
        if(isset($this->response->GenerateWayBillResult->AWBNo) && $this->response->GenerateWayBillResult->AWBNo != '')
        {
           $this->docket = $this->response->GenerateWayBillResult->AWBNo;
           $response['docket']  = $this->response->GenerateWayBillResult->AWBNo;
           $response['success'] = isset($this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation)
                                    ? $this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation
                                    : 'Success';
            
            $this->response->GenerateWayBillResult->AWBPrintContent = 'PDF content removed';

       } else { 

            if(count($this->response->GenerateWayBillResult->Status->WayBillGenerationStatus)>1){
                $response['error'] =  $this->response->GenerateWayBillResult->Status->WayBillGenerationStatus[0]->StatusInformation;
            }else if(isset($this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation)){
               $response['error'] =  $this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation; 
            }else{
               $response['error'] =  'BlueDart API not responding!!'; 
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
    * generateApiRequestString() method to generate API request data string
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function generateApiRequestString()
    {
       $request = array(); 
       $request['Request']['Consignee'] = $this->getConsignee();
       $request['Request']['Services']  = $this->getServices();
       $request['Request']['Shipper']   = $this->getShipper();
       $request['Profile']              = $this->getProfile();
       $this->apiRequestString = $request;
    }
    
    /**
    * getConsignee() method to set consignee data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getConsignee()
    {
        $consignee = array();
        
        if(!empty($this->orderInfo['shipping_address_1'])){
            $consignee['ConsigneeAddress1'] = $this->checkString($this->orderInfo['shipping_address_1']);
        }
        if(!empty($this->orderInfo['shipping_address_2']) && trim($this->orderInfo['shipping_address_2'])!=''){
            $consignee['ConsigneeAddress2'] = $this->checkString($this->orderInfo['shipping_address_2']);
        }else{
            $consignee['ConsigneeAddress2'] = $this->checkString($this->orderInfo['shipping_address_1']);
        }
        $consignee['ConsigneeAddress3'] = '';
        if(!empty($this->orderInfo['shipping_postcode'])){
            $consignee['ConsigneePincode'] = $this->checkString($this->orderInfo['shipping_postcode']);
        }
        if(!empty($this->orderInfo['telephone'])){
            $consignee['ConsigneeTelephone'] = $this->checkString($this->orderInfo['telephone']);
        }
        if(!empty($this->orderInfo['alternate_contact_number'])){
            $consignee['ConsigneeMobile'] = $this->checkString($this->orderInfo['alternate_contact_number']);
        }
        if(!empty($this->orderInfo['customer_name'])){
            $consignee['ConsigneeName'] = $this->checkString($this->orderInfo['customer_name']);
        }
        if(!empty($this->orderInfo['customer_email'])){
            $consignee['ConsigneeEmailID'] = $this->validate_email($this->orderInfo['customer_email']);
        }else{
            $consignee['ConsigneeEmailID'] = DEFAULT_SHIPMENT_EMAIL;
        }
        if(!empty($this->orderInfo['customer_email'])){
            $consignee['ConsigneeAttention'] = $this->checkString($this->orderInfo['customer_name']);
        }
        return $consignee;
    }
    
    /**
    * getServices() method to set services data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getServices()
    {
        $services = array();
        
        if(isset($this->request['weight'])){
            $services['ActualWeight'] = $this->checkString($this->request['weight']);
        }
        if( $this->isCod() && $this->getNetPayable() > 0 ){
            $services['CollectableAmount'] = $this->getNetPayable();
        } else { 
            $services['CollectableAmount'] = 0;
        }
        $services['Commodity']          = $this->getCommodity();
        if(isset($this->request['suborder_id'])){
            $services['CreditReferenceNo'] = $this->request['suborder_id'];
        }
        if(isset($this->request['declared_value'])){
            $services['DeclaredValue'] = $this->request['declared_value'];
        } else { 
            $services['DeclaredValue'] = $this->getOrderTotal();
        }
        $services['Dimensions'] = $this->getDimensions();
        if(isset($this->orderInfo['InvoiceNo'])){
             $services['InvoiceNo'] = $this->checkString($this->orderInfo['InvoiceNo']);
        }
        if(isset($this->orderInfo['itemdtl'])){
            $services['ItemCount'] = count($this->orderInfo['itemdtl']);
        }
        $services['PackType'] = '';
        if(isset($this->request['pickup_date'])){
            $order_pickup = explode(' ',$this->request['pickup_date']);
            $services['PickupDate'] = $order_pickup[0];
            $services['PickupTime'] = str_replace(':','',$order_pickup[1]);
        }
        if(isset($this->request['no_of_pkg'])){
            $services['PieceCount'] = $this->request['no_of_pkg'];
        }else{
            $services['PieceCount'] = 1;
        }
        
        $services['ProductCode']            = $this->product_code;
        $services['ProductType']            = $this->product_type;
        $services['SpecialInstruction']     = 'CLOTHS';
        $services['SubProductCode']         = $this->getSubProductCode();
        
        $services['itemdtl']    = $this->getItems();
        
        return $services;
    }
    
    /**
    * getSubProductCode() method to get sub product code - Cod(C), Pre-paid(P)
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getSubProductCode()
    {
        return $this->isCod() ? 'C' : 'P' ; 
    }

    /**
    * getCommodity() method to set commodity data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getCommodity()
    {
        $commodity = array(
            'CommodityDetail1'  => 'CLOTHS',
            'CommodityDetail2'  => '',
            'CommodityDetail3'  => ''
        );
        return $commodity;
    }
    
    /**
    * getCommodity() method to set dimensions data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getDimensions()
    {
        $dimension = array();
        
        if(isset($this->request['breadth'])){
            $dimension['Dimension']['Breadth'] = $this->request['breadth'];
        }else{
            $dimension['Dimension']['Breadth'] = '10';
        }
        if(isset($this->request['no_of_pkg'])){
            $dimension['Dimension']['Count'] = $this->request['no_of_pkg'];
        }else{
            $dimension['Dimension']['Count'] = 1;
        }
        if(isset($this->request['height'])){
            $dimension['Dimension']['Height'] = $this->request['height'];
        }else{
            $dimension['Dimension']['Height'] = 10;
        }
        if(isset($this->request['length'])){
            $dimension['Dimension']['Length'] = $this->request['length'];
        }else{
            $dimension['Dimension']['Length'] = 10;
        }
        
        return $dimension;
    }
    
    /**
    * getShipper() method to set shipper data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getShipper()
    {
        $shipper = array();
        
        if(isset($this->warehouseInfo['address_1'])){
            $shipper['CustomerAddress1'] = $this->checkString($this->warehouseInfo['address_1']);
        }
        if(isset($this->warehouseInfo['address_2'])){
            $shipper['CustomerAddress2'] = $this->checkString($this->warehouseInfo['address_2']);
        }
        if(isset($this->warehouseInfo['city'])){
            $shipper['CustomerAddress3'] = $this->checkString($this->warehouseInfo['city']);
        }
        if(isset($this->warehouseInfo['bluedart_surface_customer_code'])){
            $shipper['CustomerCode'] = $this->warehouseInfo['bluedart_surface_customer_code'];
        }
        if(isset($this->warehouseInfo['email'])){
            $shipper['CustomerEmailID'] = $this->warehouseInfo['email'];
        }
        if(isset($this->warehouseInfo['telephone'])){
            $shipper['CustomerMobile'] = str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone']);
        }
        $shipper['CustomerName'] = $this->customer_name;
        if(isset($this->warehouseInfo['postcode'])){
            $shipper['CustomerPincode'] = $this->warehouseInfo['postcode'];
        }
        if(isset($this->warehouseInfo['telephone'])){
            $shipper['CustomerTelephone'] = str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone']);
        }
        $shipper['isToPayCustomer'] = false;
        if(isset($this->warehouseInfo['bluedart_origin_area'])){
            $shipper['OriginArea'] = $this->warehouseInfo['bluedart_origin_area'];
        }
        $shipper['Sender'] = $this->customer_name;
        if(isset($this->warehouseInfo['bluedart_vendor_code'])){
            $shipper['Vendor'] = $this->warehouseInfo['bluedart_vendor_code'];
        }
        
        return $shipper;
    }
    
    /**
    * getItems() method to set product items data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getItems()
    {
        $items = array();
        if(isset($this->orderInfo['itemdtl'])){
          $index=0;
            foreach($this->orderInfo['itemdtl'] as $item) {

                if(isset($item['CGSTAmount'])){
                    $items[$index]['CGSTAmount'] = $item['CGSTAmount'];
                }
                if(isset($item['HSCode'])){
                    $items[$index]['HSCode'] = $item['HSCode'];
                }
                if(isset($item['IGSTAmount'])){
                    $items[$index]['IGSTAmount'] = $item['IGSTAmount'];
                }
                $items[$index]['Instruction'] = 'Nothing';
                $items[$index]['InvoiceDate'] = date('Y-m-d');
                if(isset($item['InvoiceNo'])){
                    $items[$index]['InvoiceNumber'] = $item['InvoiceNo'];
                }
                if(isset($item['ItemID'])){
                    $items[$index]['ItemID'] = $item['ItemID'];
                }
                if(isset($item['ItemName'])){
                    $items[$index]['ItemName'] = $this->checkString($item['ItemName']);
                }
                if(isset($item['ItemValue'])){
                    $items[$index]['ItemValue'] = $item['ItemValue'];
                }
                if(isset($item['Itemquantity'])){
                    $items[$index]['Itemquantity'] = $item['Itemquantity'];
                }
                if(isset($this->warehouseInfo['code']))
                {
                   $items[$index]['PlaceofSupply'] = $this->warehouseInfo['code'];
                }
                
                $items[$index]['ProductDesc1'] = '';

                if(isset($item['SKUNumber'])){
                    $items[$index]['SKUNumber'] = $item['SKUNumber'];
                }
                if(isset($item['SellerGSTNNumber'])){
                    $items[$index]['SellerGSTNNumber'] = $item['SellerGSTNNumber'];
                }
                if(isset($item['SellerName'])){
                    $items[$index]['SellerName'] = $item['SellerName'];
                }
                if(isset($item['TaxableAmount'])){
                    $items[$index]['TaxableAmount'] = $item['TaxableAmount'];
                }
                if(isset($item['TotalValue'])){
                    $items[$index]['TotalValue'] = $item['TotalValue'];
                }
                
                /*new tags added for - eWayBill details*/
                if(isset($this->request['eWayBillNo']) && !empty(trim($this->request['eWayBillNo']))) {
                    $eWayBillExpDate = $this->request['eWayBillExpDate'];
                    $eWayBillExpDate = explode(' ',$eWayBillExpDate);
                    $eWayBillExpDate = date('d-m-Y',strtotime($eWayBillExpDate[0]));
                    
                    //$items[$index]['eWaybillNumber'] = $this->request['eWayBillNo'];
                    //$items[$index]['eWaybillDate']   = $eWayBillExpDate;
                    //$items[$index]['supplyType']     = 'I';
                    //$items[$index]['subSupplyType']  = 1;
                    //$items[$index]['docType']        = 'INV';
                }
                /*eWayBill details*/
                
                $index++;
            }
        }
       return $items;
    }
    
    /**
    * getProfile() method to set profile data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getProfile()
    {
        $warehouse_city = str_replace(array(' '),array('_'),strtolower($this->warehouseInfo['city']));
        
        if(array_key_exists($warehouse_city, BLUE_DART_ACCESS))
        {
           $this->api_login_id = isset(BLUE_DART_ACCESS[$warehouse_city]['login_id']) 
                                    ? BLUE_DART_ACCESS[$warehouse_city]['login_id']
                                    : '';
           $this->api_license_key = isset(BLUE_DART_ACCESS[$warehouse_city]['license_key']) 
                                    ? BLUE_DART_ACCESS[$warehouse_city]['license_key']
                                    : '';
        } else{
            $this->error['error'] = 'API credentials not found for '.$warehouse_city . ' location.';
        }
        
        $profile = array(
            'Api_type'      => $this->api_type,
            'LicenceKey'    => $this->api_license_key,
            'LoginID'       => $this->api_login_id,
            'Version'       => $this->api_version,
        );

       return $profile; 
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
        if(!isset($this->request['breadth']) || trim($this->request['breadth'])=='' || trim($this->request['breadth'])=='0') 
        {
           $this->error['error'] = 'Breadth value can not be 0' ;
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
    public function isPincodeServiceable($pincode = '', $mode = 'surface')
    {
        if($pincode=='') {
            if(!isset($this->request['pincode']) || 
                trim($this->request['pincode'])=='' || 
                trim($this->request['pincode'])=='0'
            ) {
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
            $where = " AND mode = '".$this->_db->escape(trim($this->request['serviceability_type']))."' ";
        }else{
            $where = " AND mode = '".$this->_db->escape(trim($mode))."' ";
        }

        $sql = "SELECT 
                    `id`
                FROM 
                    ". DB_PREFIX . "bluedart_pincodes 
                WHERE 
                    pincode = '".$this->_db->escape($pincode)."' 
                    AND 
                    status = 1 
                "  . $where;

        $query = $this->_db->query($sql);

        if ($query->num_rows) {
           $response['success'] = 1;
        }else{
           $response['success'] = 0;
        }
        return $response;
    }

    /**
    * addReverseShipment() - method to add reverse shipment data,
    * @author  MSA, <Feb. 18>
    */
    public function addReverseShipment($this_obj)
    {
        $this->validateReverseShipmentData();

        if(!empty($this->error)){
           return $this->error;
        }

        $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

        $this->generateReverseShipmentApiRequest();

        $this->request();

        $reverseShipmentResponse = $this->reverseShipmentResponse();
        
        if(isset($reverseShipmentResponse['docket']) && $reverseShipmentResponse['docket']!=='')
        {
            $status['success']    = isset($reverseShipmentResponse['success']) ? $reverseShipmentResponse['success'] : '1';

            /* Update DB tables*/
            $this->courierDataToSaveInDB['token_no']    = $reverseShipmentResponse['token_no'];
            $this->courierDataToSaveInDB['aws_docket']  = $reverseShipmentResponse['docket'];
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

            
            //$this->saveReverseShipmentDocketData();
            $return_shipment_id = $this->saveReverseShipmentTrackingData();
            $this->updateReverseShipmentMasterReturnData($return_shipment_id);
            $this->updateReverseShipmentReturnData($return_shipment_id);
            
             /*checking for reverse shipment email */  
            $this->sendReverseShipmentEmailAlertMessage($this_obj);

        }else{
            $status['error']    = isset($reverseShipmentResponse['error']) ? $reverseShipmentResponse['error'] : 'Error';
        } 
        return $status;
    }
    
    /**
    * response() - inherited method, 
    * @Info - processing API response data
    * @return - Array API response data 
    * @author  MSA, <26 Dec. 17>
    */
    public function reverseShipmentResponse()
    {

        $response = array();
        
        if(isset($this->response->GenerateWayBillResult->AWBNo) && $this->response->GenerateWayBillResult->AWBNo != '')
        {
           $this->docket = $this->response->GenerateWayBillResult->AWBNo;
           $response['docket']  = $this->response->GenerateWayBillResult->AWBNo;
           $response['success'] = isset($this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation)
                                    ? $this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation
                                    : 'Success';
            
           $response['token_no']=$this->response->GenerateWayBillResult->TokenNumber;

            $this->response->GenerateWayBillResult->AWBPrintContent = 'PDF content removed';

       } else { 

            if(count($this->response->GenerateWayBillResult->Status->WayBillGenerationStatus)>1){
                $response['error'] =  $this->response->GenerateWayBillResult->Status->WayBillGenerationStatus[0]->StatusInformation;
            }else if(isset($this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation)){
               $response['error'] =  $this->response->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation; 
            }else{
               $response['error'] =  'BlueDart API not responding!!'; 
            }
       }
        
        return $response;
    }

    /**
    * generateApiRequestString() method to generate API request data string
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function generateReverseShipmentApiRequest()
    {
       $request = array(); 
       $request['Request']['Consignee'] = $this->getReverseShipmentConsignee();
       $request['Request']['Services']  = $this->getReverseShipmentServices();
       $request['Request']['Shipper']   = $this->getReverseShipmentShipper();
       $request['Profile']              = $this->getReverseShipmentProfile();
       $this->apiRequestString = $request;
    }

    /**
    * getReverseShipmentProfile() method to set profile data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReverseShipmentProfile()
    {
        $warehouse_city = str_replace(array(' '),array('_'),strtolower($this->warehouseInfo['city']));
        
        if(array_key_exists($warehouse_city, BLUE_DART_ACCESS))
        {
           $this->api_login_id = isset(BLUE_DART_ACCESS[$warehouse_city]['login_id']) 
                                    ? BLUE_DART_ACCESS[$warehouse_city]['login_id']
                                    : '';
           $this->api_license_key = isset(BLUE_DART_ACCESS[$warehouse_city]['license_key']) 
                                    ? BLUE_DART_ACCESS[$warehouse_city]['license_key']
                                    : '';
        } else{
            $this->error['error'] = 'API credentials not found for '.$warehouse_city . ' location.';
        }
        $profile = array(
            'Api_type'      => $this->api_type,
            'Area'          => $this->warehouseInfo['bluedart_origin_area'],
            'Customercode'  => $this->warehouseInfo['bluedart_surface_customer_code'],
            'LicenceKey'    => $this->api_license_key,
            'LoginID'       => $this->api_login_id,
            'Version'       => $this->api_version,
        );

       return $profile; 
    }

    /**
    * getReverseShipmentConsignee() method to get consignee - seller (wholesalebox location) details
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReverseShipmentConsignee()
    {
        $consignee = array();
        
        if(!empty($this->warehouseInfo['address_1'])){
            $consignee['ConsigneeAddress1'] = $this->checkString($this->warehouseInfo['address_1']);
        }
        if(!empty($this->warehouseInfo['address_2']) && trim($this->warehouseInfo['address_2'])!=''){
            $consignee['ConsigneeAddress2'] = $this->checkString($this->warehouseInfo['address_2']);
        }else{
            $consignee['ConsigneeAddress2'] = $this->checkString($this->warehouseInfo['shipping_address_1']);
        }
        $consignee['ConsigneeAddress3'] = '';
        if(!empty($this->warehouseInfo['postcode'])){
            $consignee['ConsigneePincode'] = $this->checkString($this->warehouseInfo['postcode']);
        }
        if(!empty($this->warehouseInfo['telephone'])){
            $consignee['ConsigneeTelephone'] = str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone']);
        }
        if(!empty($this->warehouseInfo['telephone'])){
            $consignee['ConsigneeMobile'] = str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone']);
        }
        if(!empty($this->warehouseInfo['warehouse_name'])){
            $consignee['ConsigneeName'] = $this->checkString($this->warehouseInfo['warehouse_name']);
        }
        if(!empty($this->warehouseInfo['email'])){
            $consignee['ConsigneeEmailID'] = $this->warehouseInfo['email'];
        }
        if(!empty($this->warehouseInfo['email'])){
            $consignee['ConsigneeAttention'] = $this->checkString($this->warehouseInfo['email']);
        }
        return $consignee;
    }

    /**
    * getReverseShipmentShipper() method to get shipper - customer details
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReverseShipmentShipper()
    {
        $shipper = array();

        if(!empty($this->request['cust_address1'])) {
            $shipper['CustomerAddress1'] = $this->checkString($this->request['cust_address1']);    
        }
        if(!empty($this->request['cust_address2'])) {
            $shipper['CustomerAddress2'] = $this->checkString($this->request['cust_address2']);
        }
        if(!empty($this->request['cust_city'])) {
            $shipper['CustomerAddress3'] = $this->checkString($this->request['cust_city']);    
        }
        if(!empty($this->warehouseInfo['bluedart_surface_customer_code'])){
            $shipper['CustomerCode'] = $this->warehouseInfo['bluedart_surface_customer_code'];
        } 
        if(!empty($this->orderInfo['order']['email'])) {
            $shipper['CustomerEmailID'] = $this->validate_email($this->orderInfo['order']['email']);
        }else{
            $shipper['CustomerEmailID'] = DEFAULT_SHIPMENT_EMAIL;
        }
        if(!empty($this->request['cust_telephone'])) {
            $shipper['CustomerMobile']  = str_replace(['(+91)',' '],['',''],$this->request['cust_telephone']);
        }
        if(!empty($this->request['cust_name'])) {
            $shipper['CustomerName']    = $this->request['cust_name'];
        }else{
            $shipper['CustomerName']    = $this->request['shipping_company'];
        }
        if(!empty($this->request['cust_pin'])) {
            $shipper['CustomerPincode'] = $this->request['cust_pin'];
        }
        if(!empty($this->request['cust_telephone'])) {
            $shipper['CustomerTelephone'] = str_replace(['(+91)',' '],['',''],$this->request['cust_telephone']);
        }
        
        $shipper['IsToPayCustomer'] = true;
        $shipper['OriginArea']      = $this->warehouseInfo['bluedart_origin_area'];
        $shipper['Vendor']          = $this->warehouseInfo['bluedart_vendor_code'];

        return $shipper;
    }

    /**
    * getReverseShipmentServices() method to get service data for API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReverseShipmentServices()
    {
        $services = array();
        $services['ActualWeight']       = $this->checkString($this->request['package_weight']);
        $services['CollectableAmount']  = $this->getReverseShipmentCollectableAmount();
        $services['Commodity']          = $this->getCommodity();
        $services['CreditReferenceNo']  = substr(uniqid($this->request['order_no'].'-'),0,19);
        $services['DeclaredValue']      = $this->getReverseShipmentTotalAmount();
        $services['Dimensions']         = $this->getDimensions();
        $services['InvoiceNo']          = $this->courierDataToSaveInDB['return_products'][0]['invoice_no'] 
                                            ?? $this->getInvoiceNumberInReverseShipment();
        if(!empty($this->courierDataToSaveInDB['return_products'])) {
            $services['ItemCount']          = count($this->courierDataToSaveInDB['return_products']);    
        }else{
            $services['ItemCount']          = count($this->getReturnProducts());
        }
        $services['PackType']           = '';
        $services['RegisterPickup']     = true;
        $services['IsReversePickup']    = true;
        $services['PickupDate']         = date('Y-m-d', strtotime(' +1 day'));
        $services['PickupTime']         = '1100';
        $services['PieceCount']         = 1;
        $services['ProductCode']        = $this->product_code;
        $services['ProductType']        = $this->product_type;
        $services['SpecialInstruction'] = 'CLOTHS';
        $services['SubProductCode']     = $this->getReverseShipmentSubProductCode();
        $services['itemdtl']            = $this->getReverseShipmentItems();
        
        return $services;
    }

    /**
    * getReverseShipmentSubProductCode() method to get sub product code - Cod(C), Pre-paid(P)
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReverseShipmentSubProductCode()
    {
        return $this->isReverseShipmentPaymentMethodCod() ? 'C' : 'P' ; 
    }

    /**
    * getReverseShipmentItems() method to set product items data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReverseShipmentItems()
    {   
        if(!empty($this->courierDataToSaveInDB['return_products'])){
          $order_products = $this->courierDataToSaveInDB['return_products'];
        }else{
          $order_products = $this->getReturnProducts();
        }

        $items = array();
        if(isset($order_products)){
          $index=0; $ItemID=1;
            foreach($order_products as $item) {
                $product_qty            = $item['quantity']; //* $item['piece_in_set'];
                $product_value          = ((float) $item['price_per_piece'] + (float) $item['discount_per_piece']);
                $product_taxable_amount = $product_value * $product_qty;
                $items[$index]['CGSTAmount']    = 0;
                $items[$index]['HSCode']        = $item['hsn_code'];
                $items[$index]['IGSTAmount']    = 0;   
                $items[$index]['Instruction']   = 'Nothing';
                $items[$index]['InvoiceDate']   = date('Y-m-d');
                //$items[$index]['InvoiceNumber'] = '';
                $items[$index]['ItemID']        = $ItemID++;
                $items[$index]['ItemName']      = $this->checkString($item['p_name']);
                $items[$index]['ItemValue']     = $item['price_per_piece'];
                $items[$index]['Itemquantity']  = $item['quantity'];
                $items[$index]['PlaceofSupply'] = ''; //$this->warehouseInfo['code'];
                $items[$index]['ProductDesc1']  = '';
                $items[$index]['SKUNumber']     = '';
                $items[$index]['SellerGSTNNumber'] = $this->warehouseInfo['gstin'];
                $items[$index]['SellerName']    = $this->warehouseInfo['warehouse_name'];
                $items[$index]['TaxableAmount'] = $product_taxable_amount;
                $items[$index]['TotalValue']    = $product_taxable_amount + $item['p_tax'];
                $index++;
            }
        }
       return $items;
    }

    /**
    * validateReverseShipmentData() method to update API request data
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function validateReverseShipmentData()
    {
        $this->validateReverseShipmentFormData();
         /* check pincode for serviceable status */
            if(!empty($this->request['cust_pin'])) {
                $mode = (strtolower($this->request['service_type']) == 'express') ? 'Apex' : 'Surface';
                $isServiceable = $this->isPincodeServiceable($this->request['cust_pin'], $mode);
                if(isset($isServiceable['success']) && !$isServiceable['success']){
                    $this->error['error'] = $this->request['cust_pin'].' PIN is not serviceable.';
                }
            }else{
                $this->error['error'] = 'Customer pincode not found!!';
            }
        /* check pincode for serviceable status */  
    }

    /**
    * addForwardShipment() method to generate forward shipment request from return panel
    * 
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

        $reverseShipmentResponse = $this->reverseShipmentResponse();
        
        if(isset($reverseShipmentResponse['docket']) && $reverseShipmentResponse['docket']!=='')
        {
            $status['success']    = isset($reverseShipmentResponse['success']) ? $reverseShipmentResponse['success'] : '1';

            /* Update DB tables*/
            $this->courierDataToSaveInDB['aws_docket']  = $reverseShipmentResponse['docket'];
            $this->courierDataToSaveInDB['return_ids']  = $this->request['return_ids'];
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
            $status['error']    = isset($reverseShipmentResponse['error']) ? $reverseShipmentResponse['error'] : 'Error';
        } 
        return $status;
        

    }

    /**
    * generateForwardShipmentApiRequest() method to generate API request data string
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function generateForwardShipmentApiRequest()
    {
       $request = array(); 
       $request['Request']['Consignee'] = $this->getReturnConsignee();
       $request['Request']['Services']  = $this->getReturnServices();
       $request['Request']['Shipper']   = $this->getShipper();
       $request['Profile']              = $this->getProfile();
       $this->apiRequestString = $request; 
    }

    /**
    * getReturnConsignee() method to set consignee data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReturnConsignee()
    {
        $consignee = array();
        
        if(!empty($this->request['cust_address1'])){
            $consignee['ConsigneeAddress1'] = $this->checkString($this->request['cust_address1']);
        }
        if(!empty($this->request['cust_address2']) && trim($this->request['cust_address2'])!=''){
            $consignee['ConsigneeAddress2'] = $this->checkString($this->request['cust_address2']);
        }else{
            $consignee['ConsigneeAddress2'] = $this->checkString($this->request['cust_address1']);
        }
        $consignee['ConsigneeAddress3'] = '';

        if(!empty($this->request['cust_pin'])){
            $consignee['ConsigneePincode'] = $this->checkString($this->request['cust_pin']);
        }
        if(!empty($this->request['cust_telephone'])){
            $consignee['ConsigneeTelephone'] = $this->checkString($this->request['cust_telephone']);
        }
        if(!empty($this->request['cust_telephone'])){
            $consignee['ConsigneeMobile'] = $this->checkString($this->request['cust_telephone']);
        }
        if(!empty($this->request['cust_name'])){
            $consignee['ConsigneeName'] = $this->checkString($this->request['cust_name']);
        }
        if(!empty($this->request['cust_email'])){
            $consignee['ConsigneeEmailID'] = $this->validate_email($this->request['cust_email']);
        }else{
            $consignee['ConsigneeEmailID'] = DEFAULT_SHIPMENT_EMAIL;
        }
        if(!empty($this->request['cust_email'])){
            $consignee['ConsigneeAttention'] = $this->checkString($this->request['cust_email']);
        }
        return $consignee;

    }

    /**
    * getReturnServices() method to set services data in API request
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function getReturnServices()
    {
        $services = array();
        
        if(isset($this->request['package_weight'])){
            $services['ActualWeight'] = $this->checkString($this->request['package_weight']);
        }

        $services['CollectableAmount']  = $this->getReverseShipmentCollectableAmount();
        $services['Commodity']          = $this->getCommodity();
        
        if(isset($this->request['selected_master_return_id'])){
            $services['CreditReferenceNo'] = $this->request['selected_master_return_id'];
        }

        $services['DeclaredValue']  = $this->getReverseShipmentTotalAmount();
        $services['Dimensions']     = $this->getDimensions();
        $services['InvoiceNo']      = $this->getInvoiceNumberInReverseShipment();

        if(!empty($this->getReverseShipmentItems())){
            $services['ItemCount'] = count($this->getReverseShipmentItems());
        }

        $services['PackType']               = '';
        $services['PickupDate']             = date('Y-m-d', strtotime(' +1 day'));
        $services['PickupTime']             = '1100';
        $services['PieceCount']             = 1;
        $services['ProductCode']            = $this->product_code;
        $services['ProductType']            = $this->product_type;
        $services['SpecialInstruction']     = 'CLOTHS';
        $services['SubProductCode']         = $this->getReverseShipmentSubProductCode();
        $services['itemdtl']                = $this->getReverseShipmentItems();
        
        return $services;
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
        $this->setTrackingUrl();

        $ch = curl_init($this->tracking_url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        
        if(curl_error($ch)){
            return $response['error'] = curl_error($ch); 
        }
        curl_close($ch);

        $this->response = $result;
    }

    /**
    * setTrackingUrl() - method to set tracking url
    * @author  MSA, <March. 18>
    */
    protected function setTrackingUrl()
    {
        $this->getAPILoginIdForLocation();
        if(empty($this->api_login_id)) {
            $this->api_login_id = isset(BLUE_DART_ACCESS['jaipur']['login_id']) 
                                        ? BLUE_DART_ACCESS['jaipur']['login_id']
                                        : '';
        }
        if(empty($this->api_license_key)) {
            $this->api_license_key = isset(BLUE_DART_ACCESS['jaipur']['license_key']) 
                                    ? BLUE_DART_ACCESS['jaipur']['license_key']
                                    : ''; 
        }
        $tracking_params = array(
            'handler'   => 'tnt',
            'action'    => 'custawbquery',
            'loginid'   => 'JA337654', //$this->api_login_id,
            'awb'       => 'awb',
            'numbers'   => $this->docket,
            'format'    => 'html',
            'lickey'    => 'cc32aed23f7c2bf00e2f0172e618e818', //$this->api_license_key,
            'verno'     => '1.3',
            'scan'      => '1'
        );

        $tracking_query = '';
        foreach($tracking_params as $key => $value) {
            $tracking_query .= $key . '=' . $value . '&' ; 
        }
        $this->tracking_url = $this->tracking_url .'?' .$tracking_query; 
    }

    protected function getAPILoginIdForLocation()
    {
        $api_login_id = '';
        $sql = "SELECT rst.vendor_code,rst.warehouse_id,wa.city 
                    FROM ".DB_PREFIX."return_shipment_tracking rst
                    INNER JOIN ".DB_PREFIX."warehouse_address wa
                        ON wa.warehouse_id = rst.warehouse_id
                WHERE
                    rst.tracking_no = '".$this->_db->escape($this->request['tracking_no'])."'         
                " ;  
        $result = $this->_db->query($sql);
        if($result->num_rows) {

            $warehouse_city = str_replace(array(' '),array('_'),strtolower($result->row['city']));
        
            if(array_key_exists($warehouse_city, BLUE_DART_ACCESS))
            {
               $this->api_login_id = isset(BLUE_DART_ACCESS[$warehouse_city]['login_id']) 
                                        ? BLUE_DART_ACCESS[$warehouse_city]['login_id']
                                        : '';
               $this->api_license_key = isset(BLUE_DART_ACCESS[$warehouse_city]['license_key']) 
                                    ? BLUE_DART_ACCESS[$warehouse_city]['license_key']
                                    : '';                         
            } 
        }
    }

    /**
    * docketTrackingResponse() - method to process docket tracking API response
    * @author  MSA, <March. 18>
    */
    protected function docketTrackingResponse()
    {
        $tracking_history = array();
        if(!empty($this->response)) {
            $tracking_history['response']   = 'success';
            $tracking_history['history']    = $this->response;
        }else{
            $tracking_history['error'] = 'Tracking data not available.';
        }
       
        return json_encode($tracking_history);
    }


    

}
