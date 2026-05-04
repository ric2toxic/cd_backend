<?php

ini_set("soap.wsdl_cache_enabled", "0");
ini_set('default_socket_timeout', 180);

/**
 * CourierFedex
 *
 * @info - class to process Fedex API request and generate api response  
 *
 * @author @MSA (JAN 2017)
 */
class CourierFedex extends CourierBase
{
    private $api_license_key;
    private $api_password;
    private $api_account_number;
    private $api_meter_number;
    
    public $number_of_packages = 1;
   
    public $wsdl_file      = __DIR__ .'/ShipService_v21.wsdl';
    public $track_wsdl_file= __DIR__ .'/TrackService_v14.wsdl';


    public $shipment_drop_off_type = 'REGULAR_PICKUP'; // REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
    public $shipment_service_type  = 'STANDARD_OVERNIGHT';  //STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_EXPRESS_SAVER     
    public $shipment_packaging_type= 'YOUR_PACKAGING'; //FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING
    
    public $tracking_url            = 'https://www.fedex.com/trackingCal/track';
    
    /** 
    * Class constructor
    * @params  Array  courier request parameters
    * @return  Object class object
    * @author  MSA, <JAN 2017>
    */
    public function __construct($param) {

        parent::__construct($param);
        
        $this->api_end_point        = FEDEX_API_URL;

        $this->post_mode        = 'SOAP';
        $this->post_type        = 'JSON';
        $this->response_type    = 'JSON';
        
        if(isset($this->request['service_type']) && trim($this->request['service_type'])!='')
        {
            $this->shipment_service_type = $this->request['service_type'];
        }

        if(isset($this->request['account_code']) && trim($this->request['account_code'])!='')
        {
            $this->api_account_number = $this->request['account_code'];
        }else{
            $this->api_account_number = FEDEX_ACCOUNT_1;
        }
        
        $this->getCredentials();
        
    }
    
    public function getCredentials()
    { 
        $warehouse_city = strtolower(str_replace(' ','_',$this->warehouseInfo['city']));
        if(!empty(FEDEX_ACCESS[$warehouse_city][$this->api_account_number])) {
            $credentials = FEDEX_ACCESS[$warehouse_city][$this->api_account_number];
            $this->api_license_key      = ( !empty($credentials['Key']) ? $credentials['Key'] : '' );
            $this->api_password         = ( !empty($credentials['Password']) ? $credentials['Password'] : '' );
            $this->api_account_number   = ( !empty($credentials['Account']) ? $credentials['Account'] : '' );
            $this->api_meter_number     = ( !empty($credentials['Meter']) ? $credentials['Meter'] : '' );
        }

    }
    
    /**
    * process() method to process api request and return response data
    * @return  Array Api response data
    * @author  MSA, <JAN 2017>
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
    * @Info - creating curl api request 
    * @author  MSA, <JAN 2017>
    */
    public function request()
    {
        try{
                $client = new SoapClient($this->wsdl_file, array('trace' => 1)); 
                $client->__setLocation($this->api_end_point);
                $this->response = @$client->processShipment($this->apiRequestString); 

            }catch(SoapFault $exception)
            { 
                $this->response['error'] = 'Fedex server not responding for API call!!'; //$exception->faultstring;
            }
        //echo $client->__getLastRequest();   
        //echo '<br><br><br>';
        //echo $client->__getLastResponse(); 
        //die;
            
    }
    
    /**
    * response() - inherited method, 
    * @Info - processing api response data
    * @return - Array API response data 
    * @author  MSA, <JAN 2017>
    */
    public function response()
    {
       $response = array();
       
        if (isset($this->response->HighestSeverity) && $this->response->HighestSeverity != 'FAILURE' && $this->response->HighestSeverity != 'ERROR'){
           
           $this->docket        = $this->response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
           $response['docket']  = $this->response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
           $response['success'] = $this->response->Notifications->Severity;
           
           if(isset($this->response->CompletedShipmentDetail->AssociatedShipments->PackageOperationalDetail->Barcodes->StringBarcodes->Value)) {
               $response['FEDEX_1D'] = $this->response->CompletedShipmentDetail->AssociatedShipments->PackageOperationalDetail->Barcodes->StringBarcodes->Value;
           }
           
           $response['file_name'] = $this->save_label();
           
           if(isset($this->response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image))
           {
               $this->response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image = '';
           }
           if(isset($this->response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image))
           {
               $this->response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image = '';
           }
           
        }else{
           
           if(isset($this->response->HighestSeverity) && ($this->response->HighestSeverity == 'FAILURE' || $this->response->HighestSeverity == 'ERROR'))
           {
               if(is_array($this->response->Notifications)) {
                    $response['error'] = $this->response->Notifications[count($this->response->Notifications)-1]->Message;
               }
               if(isset($this->response->Notifications->Message)){
                   $response['error'] = $this->response->Notifications->Message;
               }
           }else{
               $response['error'] = isset($this->response['error']) ? $this->response['error'] : 'Fedex API Error';
           }
        }

         $this->apiResponseString = serialize($this->response);
         
         $this->response = $response;
        
        $this->setDBSaveData();
        
        if(!$this->saveCourierDocketData()){
            $this->response['error'] = 'Error occure while saving courier data.'  ;
        }
        
        if(isset($this->response['success'])){
            
            $this->response['shipping_label_id'] = $this->addShippingLabel();
            
         /*Set CRM Push Data For Docket Tracking*/  
            $this->crmRequestData['tracking_url']     = $this->tracking_url;
            //$this->updateOrderTrackingNoInCrm();
         /*Set CRM Push Data For Docket Tracking*/
            
        }
        
        return $this->response;
    }
    
    protected function save_label()
    {
        if(isset($this->response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image))
        {
            $pdf = $this->response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
            $docket = $this->response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
            
            if(isset($this->orderInfo['order_no'])) {
                $file_name_wout_ext = trim($this->orderInfo['order_no']).'_Fedex_'.$docket;  
            }else{
                $file_name_wout_ext = trim($this->request['order_id']).'_Fedex_'.$docket;  
            }
            $obj = new SecureFileDownload($this->registry);
            $file_links = $obj->generateFileDownload('shipping_label', $file_name_wout_ext); 
            if(!empty($file_links)) {
                $fp = fopen($file_links['clean_file_path'], 'w+');
                fwrite($fp, $pdf);
                fclose($fp);
                return $file_links['db_file_string'];
            }
            
        }
    }
    
    /**
    * generateApiRequestString() method to generate api request data string
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function generateApiRequestString()
    {
       $request = array();
       $request['WebAuthenticationDetail'] = $this->getWebAuthDetails();  
       $request['ClientDetail']            = $this->getClientDetail();
       $request['TransactionDetail']       = $this->getTransactionDetail();
       $request['Version']                 = $this->getVersion();
       $request['RequestedShipment']       = $this->getRequestedShipment();
       //pr($request); die;
       $this->apiRequestString = $request;
    }
    
    /**
    * getTransactionDetail() method to get required API transaction id format string
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getTransactionDetail()
    {
       $request = array(
                         'CustomerTransactionId' => '*** Intra India Shipping Request using PHP ***'
                );
        return $request; 
    }
    
    /**
    * getWebAuthDetails() method to get API Auth details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getWebAuthDetails()
    {
        $request = array(
            'ParentCredential' => array(
		'Key'       => $this->api_license_key, 
		'Password'  => $this->api_password
            ),
            'UserCredential' => array(
                    'Key'       => $this->api_license_key, 
                    'Password'  => $this->api_password
            )
        );
        return $request;
    }
    
    /**
    * getClientDetail() method to get API Client details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getClientDetail()
    {
        $request = array(
            'AccountNumber' => $this->api_account_number, 
            'MeterNumber'   => $this->api_meter_number
        );
        return $request;
        
    }
    
    /**
    * getVersion() method to get API Version details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getVersion()
    {
       $request = array(
                'ServiceId'     => 'ship', 
                'Major'         => '21', 
                'Intermediate'  => '0', 
                'Minor'         => '0'
        ); 
        return $request; 
    }
    
    /**
    * getRequestedShipment() method to get API Shipment details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getRequestedShipment()
    {
        $request = array();
        $request['ShipTimestamp']   = date('c');
        $request['DropoffType']     = $this->shipment_drop_off_type;
        $request['ServiceType']     = $this->shipment_service_type;
        $request['PackagingType']   = $this->shipment_packaging_type;
        $request['Shipper']         = $this->getShipper();
        $request['Recipient']       = $this->getRecipient();
        $request['ShippingChargesPayment'] = $this->getShippingChargesPayment();
        
        if($this->isCod()){
            $request['SpecialServicesRequested']  = $this->getSpecialServicesRequested();
        }
        
        $request['CustomsClearanceDetail']      = $this->CustomsClearanceDetail();
        $request['LabelSpecification']          = $this->getLabelSpecification();
        $request['CustomerSpecifiedDetail']     = $this->getMaskData();
        $request['PackageCount']                = $this->getPackageCount();
        $request['RequestedPackageLineItems']   = $this->getRequestedPackageLineItems();
        
       return $request;  
    }
    
    /**
    * getShipper() method to get API Shipper details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getShipper()
    {
      $request = array(
                'Contact' => array(
			'PersonName'    => 'WholeSaleBox',
			'CompanyName'   => $this->checkString($this->warehouseInfo['warehouse_name']),
			'PhoneNumber'   => str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone'])
		),
		'Address' => array(
			'StreetLines'           => array(
                            $this->checkString($this->warehouseInfo['address_1']), 
                            $this->checkString($this->warehouseInfo['address_2']),
                        ) , //
			//'StreetLines'           => $this->checkString($this->warehouseInfo['address_2']),
                        'City'                  => $this->checkString($this->warehouseInfo['city']),
			'StateOrProvinceCode'   => $this->warehouseInfo['code'],
			'PostalCode'            => $this->warehouseInfo['postcode'],
			'CountryCode'           => 'IN',
			'CountryName'           => 'INDIA'
		)
        ); 
        return $request;  
        
    }
    
    /**
    * getRecipient() method to get API Recipient details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getRecipient()
    {
        $request = array(
                'Contact' => array(
			'PersonName'        => $this->checkString($this->orderInfo['customer_name']),
			'CompanyName'       => $this->checkString($this->orderInfo['shipping_company']),
			'PhoneNumber'       => $this->orderInfo['telephone']
		),
		'Address' => array(
			'StreetLines'       => array(
                            $this->checkString($this->orderInfo['shipping_address_1']),
                            $this->checkString($this->orderInfo['shipping_address_2'])
                        ), 
			//'StreetLines'       => $this->checkString($this->orderInfo['shipping_address_2']),
                        'City'              => $this->orderInfo['shipping_city'],
			'StateOrProvinceCode'=> $this->orderInfo['shipping_state_code'],
			'PostalCode'        => $this->orderInfo['shipping_postcode'],
			'CountryCode'       => 'IN',
			'CountryName'       => 'INDIA',
			'Residential'       => false
		)
        ); 
        return $request;
    }
    
    /**
    * getRecipient() method to get API Shipping charges details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getShippingChargesPayment()
    {
        $request = array(
                'PaymentType' => 'SENDER',
                'Payor' => array(
                                'ResponsibleParty' => array(
                                    'AccountNumber' => $this->api_account_number,
                                    'Contact' => null,
                                    'Address' => array('CountryCode' => 'IN')
                                )
                        )
        ); 
        return $request;
    }
    
    /**
    * getSpecialServicesRequested() method to get API Special Services details for COD order
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getSpecialServicesRequested()
    {
        $request = array(
                'SpecialServiceTypes' => 'COD',
                'CodDetail' => array(
                        'CodCollectionAmount' => array(
                                'Currency'  => 'INR', 
                                'Amount'    => round($this->getNetPayable(),1)
                        ),
                        'CollectionType' => 'CASH',// ANY, GUARANTEED_FUNDS, CASH
                        'FinancialInstitutionContactAndAddress' => array(
                                'Contact' => array(
                                        'PersonName'    => $this->checkString($this->orderInfo['customer_name']),
                                        'CompanyName'   => $this->checkString($this->orderInfo['shipping_company']),
                                        'PhoneNumber'   => $this->orderInfo['telephone']
                                ),
                                'Address' => array(
                                        'StreetLines'   => $this->checkString($this->orderInfo['shipping_address_1']), // . ', ' . $this->checkString($this->orderInfo['shipping_address_2']),
                                        'StreetLines'   => $this->checkString($this->orderInfo['shipping_address_2']),
                                        'City'          => $this->orderInfo['shipping_city'],
                                        'StateOrProvinceCode' => $this->orderInfo['shipping_state_code'],
                                        'PostalCode'    => $this->orderInfo['shipping_postcode'],
                                        'CountryCode'   => 'IN',
                                        'CountryName'   => 'INDIA'
                                )
                        ),
                        'RemitToName' => 'Remitter'
                )
        );
            
        return $request; 
    }
    
    /**
    * CustomsClearanceDetail() method to get API custom clearance details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function CustomsClearanceDetail()
    {
        $request = array(
		'DutiesPayment' => array(
			'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => $this->getAccountNumber(),
					'Contact' => null,
					'Address' => array(
						'CountryCode' => 'IN'
					)
				)
			)
		),
		'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
		'CustomsValue' => array(
			'Currency' => 'INR', 
			'Amount' => round($this->getOrderTotal(),1)
		),
		'CommercialInvoice' => array(
			'Purpose' => 'SOLD',
			'CustomerReferences' => array(
				'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
				'Value' => $this->request['suborder_id']
			)
		),
		'Commodities' => array(
			'NumberOfPieces' => 1,
			'CountryOfManufacture' => 'IN',
			'Weight' => array(
				'Units' => 'KG', 
				'Value' => round($this->request['weight'],1)
			),
            'Description' => 'CLOTHS',
            'Quantity' => count($this->orderInfo['itemdtl']),
			'QuantityUnits' => 'EA',
			'UnitPrice' => array(
				'Currency'  => 'INR', 
				'Amount'    => '0.0'
			),
			'CustomsValue' => array(
				'Currency' => 'INR', 
				'Amount' => round($this->getOrderTotal(),1)
			)
                        
		)
	);
	return $request;
    }
    
    /**
    * getLabelSpecification() method to get API label specification details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getLabelSpecification()
    {
        $request = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL'
	);
	return $request;
    }
    
    /**
    * getMaskData() method to get API masked data details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getMaskData()
    {
       $request = array(
                        'MaskedData'=> $this->getAccountNumber()
                    );
       return $request;
    }
    
    /**
    * getPackageCount() method to get order package count
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getPackageCount()
    {
        if(!empty($this->request['no_of_pkg']))
        {
            //$this->number_of_packages = (int)$this->request['no_of_pkg'];
        }

        return $this->number_of_packages;
    }
    
    /**
    * getRequestedPackageLineItems() method to get order item list 
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getRequestedPackageLineItems()
    {
        
       for($i=0; $i<$this->getPackageCount(); $i++)
       {
           $request[] = array(
                    'SequenceNumber'    => $i + 1,
                    'GroupPackageCount' => 1, //$this->getPackageCount(),
                    'InsuredValue' => array(
                            'Amount'    => round($this->getOrderTotal(),1), 
                            'Currency'  => 'INR'
                    ),
                    'Weight' => array(
                            'Value' => round($this->request['weight'],1),
                            'Units' => 'KG'
                    ),
                    'Dimensions' => array(
                            'Length'    => $this->request['length'],
                            'Width'     => $this->request['width'],
                            'Height'    => $this->request['height'],
                            'Units'     => 'CM'
                    ),
                    'CustomerReferences' => array(
                            'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
                            'Value' => $this->request['suborder_id']
                    )
                );
       }
        
        
        return $request;
    }
    
    /**
    * getAccountNumber() method to get account number
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getAccountNumber()
    {
        return $this->api_account_number;
    }
    
    /**
    * validateFormData() method to validate form data
    * @author  MSA, <Jan. 17>
    */
    public function validateFormData()
    {
        if(!isset($this->request['service_type']) || trim($this->request['service_type'])=='') 
        {
           $this->error['error'] = 'Service type value can not be blank' ;
        }
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
               $this->error['error'] = 'Pincode value can not be blank or 0' ;
            } else{
               $pincode =  $this->request['pincode'];
            }
        }

        if(!empty($this->error)){
           return $this->error;
        }
        
        $sql = "SELECT `cod_serviceable` FROM "
                . DB_PREFIX . "fedex_pincodes "
                . " WHERE pincode = '".$this->_db->escape($pincode)."' AND status = 1 " ;
        $query = $this->_db->query($sql);
        if ($query->num_rows) {
           $response['success'] = $query->row['cod_serviceable'];
        }else{
           $response['success'] = 0;
        }
        return $response;
    }


    public function addForwardShipment($this_obj)
    {

        $this->validateForwardShipmentData();
       
        if(!empty($this->error)){
           return $this->error;
        }
        
        $this->generateForwardApiRequestString();
        
        $this->request();
        
        if(!$this->validate_response()){
           return $this->error;
        }
        
       return $this->returnResponse($this_obj);

    }

    /**
    * generateApiRequestString() method to generate api request data string
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function generateForwardApiRequestString()
    {
       $request = array();
       $request['WebAuthenticationDetail'] = $this->getWebAuthDetails();  
       $request['ClientDetail']            = $this->getClientDetail();
       $request['TransactionDetail']       = $this->getTransactionDetail();
       $request['Version']                 = $this->getVersion();
       $request['RequestedShipment']       = $this->getReturnRequestedShipment();
       $this->apiRequestString = $request;
    }

    /**
    * getRequestedShipment() method to get API Shipment details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getReturnRequestedShipment()
    {
        $request = array();
        $request['ShipTimestamp']   = date('c');
        $request['DropoffType']     = $this->shipment_drop_off_type;
        $request['ServiceType']     = $this->shipment_service_type;
        $request['PackagingType']   = $this->shipment_packaging_type;
        $request['Shipper']         = $this->getShipper();
        $request['Recipient']       = $this->getReturnRecipient();
        $request['ShippingChargesPayment'] = $this->getShippingChargesPayment();
        $request['CustomsClearanceDetail']      = $this->getReturnCustomsClearanceDetail();
        $request['LabelSpecification']          = $this->getLabelSpecification();
        $request['CustomerSpecifiedDetail']     = $this->getMaskData();
        $request['PackageCount']                = $this->getPackageCount();
        $request['RequestedPackageLineItems'][0]= $this->getReturnRequestedPackageLineItems();
        
       return $request;  
    }

    /**
    * getRecipient() method to get API Recipient details
    * 
    * @author  MSA, <JAN 2017>
    */
    protected function getReturnRecipient()
    {
        
        $request = array(
                'Contact' => array(
                'PersonName'        => $this->checkString($this->request['cust_name']),
                'CompanyName'       => '',
                'PhoneNumber'       => $this->request['cust_telephone']
        ),
        'Address' => array(
                'StreetLines'       => array(
                            $this->checkString($this->request['cust_address1']),
                            $this->checkString($this->request['cust_address2'])
                        ), 
                'City'               => $this->request['cust_city'],
                'StateOrProvinceCode'=> $this->getStateCodeByZoneId($this->request['cust_zone_id']),
                'PostalCode'         => $this->request['cust_pin'],
                'CountryCode'        => 'IN',
                'CountryName'        => 'INDIA',
                'Residential'        => false
        )
        ); 
        return $request;
    }

    protected function getReturnCustomsClearanceDetail()
    {
        $request = array(
                'DutiesPayment' => array(
                    'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                    'Payor' => array(
                        'ResponsibleParty' => array(
                            'AccountNumber' => $this->getAccountNumber(),
                            'Contact' => null,
                            'Address' => array(
                                'CountryCode' => 'IN'
                            )
                        )
                    )
                ),
                'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
                'CustomsValue' => array(
                    'Currency' => 'INR', 
                    'Amount' => round($this->request['package_value'],1)
                ),
                'CommercialInvoice' => array(
                    'Purpose' => 'SOLD',
                    'CustomerReferences' => array(
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => time()
                    )
                ),
                'Commodities' => array(
                    'NumberOfPieces' => 1,
                    'CountryOfManufacture' => 'IN',
                    'Weight' => array(
                        'Units' => 'KG', 
                        'Value' => round($this->request['package_weight'],1)
                    ),
                    'Description' => 'CLOTHS',
                    'Quantity' => count($this->getReturnProducts()),
                    'QuantityUnits' => 'EA',
                    'UnitPrice' => array(
                        'Currency'  => 'INR', 
                        'Amount'    => '0.0'
                    ),
                    'CustomsValue' => array(
                        'Currency' => 'INR', 
                        'Amount' => round($this->request['package_value'],1)
                    )
                                
                )
            );
            return $request;
    }

    protected function getReturnRequestedPackageLineItems()
    {
        $request = array(
                    'SequenceNumber'    =>1,
                    'GroupPackageCount' =>1,
                    'InsuredValue' => array(
                            'Amount'    => round($this->request['package_value'],1), 
                            'Currency'  => 'INR'
                    ),
                    'Weight' => array(
                            'Value' => round($this->request['package_weight'],1),
                            'Units' => 'KG'
                    ),
                    'Dimensions' => array(
                            'Length'    => 10,
                            'Width'     => 10,
                            'Height'    => 10,
                            'Units'     => 'CM'
                    ),
                    'CustomerReferences' => array(
                            'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
                            'Value' => time()
                    )
                );
        return $request;
    }

    /**
    * returnResponse() - inherited method, 
    * @Info - processing api response data
    * @return - Array API response data 
    * @author  MSA, <JAN 2017>
    */
    public function returnResponse($this_obj)
    {
       $response = array();
       
       if (isset($this->response->HighestSeverity) && $this->response->HighestSeverity != 'FAILURE' && $this->response->HighestSeverity != 'ERROR'){
           
           $this->docket        = $this->response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
           $response['docket']  = $this->response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
           $response['success'] = $this->response->Notifications->Severity;
           
           if(isset($this->response->CompletedShipmentDetail->AssociatedShipments->PackageOperationalDetail->Barcodes->StringBarcodes->Value)) {
               $response['FEDEX_1D'] = $this->response->CompletedShipmentDetail->AssociatedShipments->PackageOperationalDetail->Barcodes->StringBarcodes->Value;
           }
           
           $response['file_name'] = $this->save_label();
           
           if(isset($this->response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image))
           {
               $this->response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image = '';
           }
           if(isset($this->response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image))
           {
               $this->response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image = '';
           }
           
       }else{
           
           if(isset($this->response->HighestSeverity) && ($this->response->HighestSeverity == 'FAILURE' || $this->response->HighestSeverity == 'ERROR'))
           {
               if(isset($this->response->Notifications->Message)){
                   $response['error'] = $this->response->Notifications->Message;
               }
           }else{
               $response['error'] = isset($this->response['error']) ? $this->response['error'] : 'Fedex API Error';
           }
       }

        $this->apiResponseString = serialize($this->response);
         
        $reverseShipmentResponse = $response;

        if(isset($reverseShipmentResponse['docket']) && $reverseShipmentResponse['docket']!=='')
        {
            $status['success']    = isset($reverseShipmentResponse['success']) ? $reverseShipmentResponse['success'] : '1';

            /* Update DB tables*/
            $this->courierDataToSaveInDB['aws_docket']  = $reverseShipmentResponse['docket'];
            $this->courierDataToSaveInDB['status']      = 'New';
            $this->courierDataToSaveInDB['req']         = $this->apiRequestString;
            $this->courierDataToSaveInDB['res']         = $reverseShipmentResponse;
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
            $this->courierDataToSaveInDB['file_name']        = $reverseShipmentResponse['file_name'];

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
            $status['error']    = isset($reverseShipmentResponse['error']) ? $reverseShipmentResponse['error'] : 'Error';
        } 
        return $status;
        
    }

    /**
    * validateReverseShipmentData() method to update API request data
    * 
    * @author  MSA, <26 Dec. 17>
    */
    protected function validateForwardShipmentData()
    {
        $this->validateReverseShipmentFormData();
         /* check pincode for serviceable status */
            if(!empty($this->request['cust_pin'])) {
                $isServiceable = $this->isPincodeServiceable($this->request['cust_pin']);
                if(isset($isServiceable['success']) && !$isServiceable['success']){
                    $this->error['error'] = $this->request['cust_pin'].' PIN is not serviceable.';
                }
            }else{
                $this->error['error'] = 'Customer pincode not found!!';
            }
        /* check pincode for serviceable status */  
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
        $path_to_wsdl          = $this->track_wsdl_file; //DIR_SYSTEM. 'library/operations/courier_factory/TrackService_v14.wsdl';
        $request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                'Key'       => $this->api_license_key, 
                'Password'  => $this->api_password
            ),
            'UserCredential'=> array(
                'Key'       => $this->api_license_key, 
                'Password'  => $this->api_password
            )
        );
        $request['ClientDetail'] = array(
            'AccountNumber' => $this->api_account_number, 
            'MeterNumber'   => $this->api_meter_number
        );
        $request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request using PHP ***');
        $request['Version'] = array(
            'ServiceId'     => 'trck', 
            'Major'         => '14', 
            'Intermediate'  => '0', 
            'Minor'         => '0'
        );
        $request['SelectionDetails'] = array(
            'PackageIdentifier' => array(
                'Type'      => 'TRACKING_NUMBER_OR_DOORTAG',
                'Value'     => $this->request['tracking_no']
            )
        );
        try{
            $client = new SoapClient($path_to_wsdl, array('trace' => 1)); 
            $this->response = $client ->track($request);

        }catch(SoapFault $exception)
        { 
            $this->response['error'] = 'Error on Fedex server - ' . $exception->faultstring;
        }
    }

    protected function docketTrackingResponse()
    {
        $tracking_history = array();
        $html = '';
        if(!empty($this->response)) {
            $tracking_history['response']   = 'success';
            if(isset($this->response->HighestSeverity) && $this->response->HighestSeverity === 'SUCCESS')
            {
                $html .= '<table>';
                    $html .= '<tr>';
                        $html .= '<th colspan="2">';
                            $html .= 'Travel History';
                        $html .= '<th>';
                    $html .= '<tr>';
                    $html .= '<tr>';
                        $html .= '<th>Date/Time</th>';
                        $html .= '<th>Activity</th>';
                        $html .= '<th>Location</th>';
                    $html .= '<tr>';

                if(!empty($this->response->CompletedTrackDetails->TrackDetails->StatusDetail)) 
                {
                    $html .= '<tr>';
                        if(!empty($this->response->CompletedTrackDetails->TrackDetails->StatusDetail->CreationTime)) {
                            $html .= '<td>'.date('d-m-Y',strtotime($this->response->CompletedTrackDetails->TrackDetails->StatusDetail->CreationTime)).'</td>';    
                        }
                        if(!empty($this->response->CompletedTrackDetails->TrackDetails->StatusDetail->Description)) {
                            $html .= '<td>'.$this->response->CompletedTrackDetails->TrackDetails->StatusDetail->Description.'</td>';    
                        }
                        if(!empty($this->response->CompletedTrackDetails->TrackDetails->StatusDetail->Location->City)){
                            $html .= '<td>'.$this->response->CompletedTrackDetails->TrackDetails->StatusDetail->Location->City.'</td>';    
                        }
                    $html .= '<tr>';
                }  
                $html .= '</table>';
            } else {
                 $html = '<p>Tracking data not found!!</p>';
            }
            $tracking_history['history']    = $html;
        }else{
            $tracking_history['error'] = 'Tracking data not available.';
        }
        return json_encode($tracking_history);
    }

}