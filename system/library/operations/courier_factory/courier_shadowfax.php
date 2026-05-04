<?php
/**
 * CourierShadowFax
 *
 * @info - class to process ShadowFax API request and generate response  
 *
 * @author @MSA (Feb 2018)
 */
class CourierShadowfax extends CourierBase
{
    
    private $token;
    public  $tracking_url;

    /**
    * Class constructor
    * @params  Array  courier request parameters
    * @return  Object class object
    * @author  MSA, <Fex. 18>
    */
    public function __construct($param) {

        parent::__construct($param);
        
        $this->post_mode        = 'REST';
        $this->post_type        = 'JSON';
        $this->response_type    = 'JSON';
        
        $this->api_end_point    = SHADOWFAX_BASE_URL;
        $this->token            = SHADOWFAX_TOKEN;
        $this->tracking_url     = SHADOWFAX_BASE_URL . '/api/v2/clients/requests/'; 
    }
    
    /**
    * process() - inherited method,
    * @author  MSA, <Feb. 18>
    */
    public function process(){}
    

    protected function formatJsonData()
    {
        $jsonData = json_encode($this->apiRequestString, JSON_PRETTY_PRINT);
        $excludes = array('&','@');
        foreach ($excludes as $exclude) {
            $jsonData  = str_replace($exclude,' ',$jsonData);        
        }
        $this->apiRequestString = $jsonData;
    }

    /**
    * request() - inherited method,
    * @author  MSA, <Feb. 18>
    */
    public function request(){

        $this->formatJsonData();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_end_point."/api/v3/clients/requests");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->apiRequestString);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                          'Authorization: Token '.SHADOWFAX_TOKEN,
                          'Content-Type: application/json'
                        ));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->error = 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        $this->response = json_decode($result);
    }
    
    /**
    * response() - inherited method,
    * @author  MSA, <Feb. 18>
    */
    public function response(){

        if(isset($this->response->client_request_id)){
            $responseData['success']       = 1;
            $responseData['response_text']  = 'success';
            $responseData['aws_docket']     = $this->response->client_request_id;
            $responseData['status']         = $this->response->status;
            //$responseData['response']       = $this->response;
        }else{
            $responseData['error']       = 1;
            if(isset($this->response->errors)){
                $responseData['response_text']  = $this->response->errors;
            }else{
                $responseData['response_text'] = 'failed to add reverse shipment';
            }
        }

       $this->response =  $responseData;

    }
    
    /**
    * validateFormData() - method to validate reverse shipment request data
    * @author  MSA, <Feb. 18>
    */
    public function validateFormData()
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
    * addReverseShipment() - method to add reverse shipment data,
    * @author  MSA, <Feb. 18>
    */
    public function addReverseShipment($this_obj)
    {
        $this->validateFormData();

        if(!empty($this->error)){
           return $this->error;
        }

        $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

        $this->setShadowFaxAPIString();

        $this->request();

        $this->response();

        $status = array();

        if(isset($this->response['success']) && $this->response['success'])
        {
            $status['success']    = $this->response['success'];

            /* Update DB tables*/
            $this->courierDataToSaveInDB['aws_docket']  = isset($this->response['aws_docket'])?$this->response['aws_docket']:'';
            $this->courierDataToSaveInDB['status']      = isset($this->response['status'])?$this->response['status']:'';
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
            $this->courierDataToSaveInDB['weight']           = $this->request['package_weight'];
            $this->courierDataToSaveInDB['return_reason']    = $this->request['return_reason'];
            $this->courierDataToSaveInDB['vendor_code']      = $this->warehouseInfo['nuvoex_vendor_code'];
            $this->courierDataToSaveInDB['warehouse_id']      = $this->request['select_warehouse_id'];
            $this->courierDataToSaveInDB['package_desc']     = $this->request['package_desc'];
            $this->courierDataToSaveInDB['cust_telephone']     = $this->request['cust_telephone'];
            $this->courierDataToSaveInDB['cust_name']        = $this->request['cust_name'];
            $this->courierDataToSaveInDB['cust_address1']    = $this->request['cust_address1'];
            $this->courierDataToSaveInDB['cust_address2']    = $this->request['cust_address2'];
            $this->courierDataToSaveInDB['cust_city']        = $this->request['cust_city'];
            $this->courierDataToSaveInDB['cust_pin']         = $this->request['cust_pin'];    

            //$this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

            $return_shipment_id = $this->saveReverseShipmentTrackingData();
            $this->updateReverseShipmentMasterReturnData($return_shipment_id);
            $this->updateReverseShipmentReturnData($return_shipment_id);
        
        /*checking for reverse shipment email */ 
            $this->sendReverseShipmentEmailAlertMessage($this_obj);

        }else if(isset($this->response['error']) && $this->response['error']){

            $status['error']      = $this->response['response_text'];

        }

       return $status;
    }

    /**
    * setShadowFaxAPIString() - set API request data for shadow fax
    * @author  MSA, <Feb. 18>
    */
    protected function setShadowFaxAPIString()
    {
        $this->apiRequestString['client_order_number']    = $this->getOrderNo();
        $this->apiRequestString['warehouse_name']         = $this->getWarehouseName();
        $this->apiRequestString['warehouse_address']      = $this->getWarehouseAddress();
        $this->apiRequestString['destination_pincode']    = $this->getWarehousePincode();
        $this->apiRequestString['price']                  = $this->getPrice();
        $this->apiRequestString['total_amount']           = $this->getReverseShipmentTotalAmount();
        $this->apiRequestString['address_attributes']     = $this->getAddressAttributes();
        $this->apiRequestString['skus_attributes']        = $this->getOrderProducts();
    }

    /**
    * getOrderNo() - get order number from order details
    * @author  MSA, <Feb. 18>
    */
    protected function getOrderNo(){
        return $this->request['order_no'];
    }

    /**
    * getWarehouseName() - get warehouse name
    * @author  MSA, <Feb. 18>
    */
    protected function getWarehouseName(){
        return $this->warehouseInfo['warehouse_name'];
    }

    /**
    * getWarehouseAddress() - get warehouse address
    * @author  MSA, <Feb. 18>
    */
    protected function getWarehouseAddress(){
        return $this->warehouseInfo['address_1'] . ', ' 
                . $this->warehouseInfo['address_2'] . ', ' 
                . $this->warehouseInfo['city']. ', ' 
                . $this->warehouseInfo['state'];
    }

    /**
    * getWarehousePincode() - get warehouse pincode
    * @author  MSA, <Feb. 18>
    */
    protected function getWarehousePincode(){
        return $this->warehouseInfo['postcode'];
    }

    /**
    * getAddressAttributes() - method to get address attributes data for API
    * @author  MSA, <Feb. 18>
    */
    protected function getAddressAttributes()
    {
        return array(
            'address_line'  => $this->request['cust_address1']. ', ' . $this->request['cust_address2'],
            'city'          => $this->request['cust_city'],
            'country'       => isset($this->orderInfo['order']['shipping_country']) ? $this->orderInfo['order']['shipping_country'] : '',
            'pincode'       => $this->request['cust_pin'],
            'name'          => $this->request['cust_name'],
            'phone_number'  => $this->request['cust_telephone'],
        );
    }

    /**
    * getCustomerName() - method to get customer name from order details
    * @author  MSA, <Feb. 18>
    */
    protected function getCustomerName()
    {   
        return $this->orderInfo['order']['shipping_firstname'] . ' ' . $this->orderInfo['order']['shipping_lastname'];
    }

    /**
    * getPrice() - method to get price from order details
    * @author  MSA, <Feb. 18>
    */
    protected function getPrice()
    {
        if(!empty($this->courierDataToSaveInDB['return_products'])) {
            $order_products = $this->courierDataToSaveInDB['return_products'];    
        }else {
            $order_products = $this->getReturnProducts();    
        }

        $total_price = 0;
        if(!empty($order_products))
        {
            foreach ($order_products as $op_data)
            {
                for($qty_count = 1; $qty_count <= $op_data['quantity']; $qty_count++)
                {
                    $total_price += (float)$op_data['p_price'];
                }
            }
        } 
        return  ceil($total_price);
    }

    

    /**
    * getOrderProducts() - method to get order products list from order details
    * @author  MSA, <Feb. 18>
    */
    protected function getOrderProducts()
    {
        if(!empty($this->courierDataToSaveInDB['return_products'])) {
            $order_products = $this->courierDataToSaveInDB['return_products'];    
        }else {
            $order_products = $this->getReturnProducts();    
        }

        //$order_products = $this->getReturnProducts();
        $shadowfaxData = array();
        if(isset($this->warehouseInfo['zone_id']) && $this->warehouseInfo['zone_id'] == $this->orderInfo['order']['payment_zone_id']){
            $tax_type = 'cgst';
        }else{
            $tax_type = 'igst';
        }
        if(!empty($order_products))
        {
            foreach ($order_products as $op_data)
            {
                for($qty_count = 1; $qty_count <= $op_data['quantity']; $qty_count++)
                {
                    $cgst = $sgst = $igst = 0;
                    if($tax_type == 'igst'){
                        $igst = round($op_data['p_tax'], 2);
                    }else{
                        $cgst = $sgst = round($op_data['p_tax']/2, 2);
                    }
                    if(!empty($op_data['product_id'])){
                        $pid = array(0 => $op_data['product_id']); 
                    }
                    $product_detail = array(
                                        "name"          => $op_data['p_name'],
                                        "client_sku_id" => $op_data['seller_sku']. '_'. $qty_count,
                                        "price"         => ceil((float)$op_data['p_price']),
                                        "product_category" => $op_data['cat_name'],
                                        "brand"         => "WholesaleBox",
                                        "return_reason" => $op_data['reason_name'],
                                        "qc_required"   => "false",
                                        "seller_details" => array(
                                                            "regd_name" => $this->warehouseInfo['warehouse_name'],
                                                            "regd_address" => preg_replace("/[\r\t\n]+/", " ", $this->getWarehouseAddress()),
                                                            "state" => $this->warehouseInfo['state'],
                                                            "gstin" => $this->warehouseInfo['gstin']
                                                            ),
                                        "taxes"         => array(
                                                            "cgst_amount" => $cgst,
                                                            "sgst_amount" => $sgst,
                                                            "igst_amount" => $igst,
                                                            "total_tax_amount"=> round($op_data['p_tax'], 2)
                                                            ),
                                        "hsn_code" => $op_data['hsn_code'],
                                        "invoice_id"=> $op_data['invoice_prefix'].$op_data['invoice_no'],
                                        "additional_details" => array("sku_images" => '') 
                                        );
                    $shadowfaxData[] = $product_detail;
                }
            }
        }
        return $shadowfaxData;
    }

    /**
    * isPincodeServiceable() - method to check for serviceable pin code area,
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
        $sql = "SELECT `id` FROM "
                . DB_PREFIX . "shadowfax_pincodes "
                . " WHERE pincode = '".$this->_db->escape($pincode)."' AND status = 1 " ;
        $query = $this->_db->query($sql);
        if ($query->num_rows) {
           $response['success'] = 1;
        }else{
           $response['success'] = 0;
        }
        return $response;
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
        $ch     = curl_init();
        $url    = $this->tracking_url . $this->docket;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                          'Authorization: Token '.SHADOWFAX_TOKEN
                        ));
       
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        $this->response = json_decode($result,true);
        $result = json_decode($result);
    }

    /**
    * docketTrackingResponse() - method to process docket tracking API response
    * @author  MSA, <March. 18>
    */
    protected function docketTrackingResponse()
    {
        $tracking_history = array();
        if(!empty($this->response['pickup_request_state_histories'])) {
            $tracking_history['response'] = 'success';
            $tracking_history['history'] = $this->getTrackingHtml($this->response['pickup_request_state_histories']);
        }else{
            $tracking_history['error'] = isset($this->response['responseMsg'])
                                            ? $this->response['responseMsg']
                                            : 'Tracking data not available.';
        }
        return json_encode($tracking_history);
    }

    /**
    * processDate() - method to format date value
    * @author  MSA, <March. 18>
    */
    protected function processDate($date)
    {
        $date = str_replace(array('T','Z'), array(' ',''), $date);
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
                $html .=       '<th>Status</th>';
                $html .=       '<th>Comment</th>';
                $html .=    '</tr>';
                $html .=  '</thead>';
                $html .=  '<tbody>';

             foreach($tracking_detail as $key => $value) {
                $html .=   '<tr>';
                $html .=       '<th>'.$this->processDate($value['created_at']).'</th>';
                $html .=       '<th>'.$value['current_location'].'</th>';
                $html .=       '<th>'.$value['state'].'</th>';
                $html .=       '<th>'.$value['comment'].'</th>';
                $html .=    '</tr>';
             }
        } 
        return $html;
    }

}
