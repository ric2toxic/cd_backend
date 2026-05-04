<?php
/**
 * CourierGati
 *
 * @info - class to process Gati API request and generate response  
 *
 * @author @MSA (Dec 2017)
 */

libxml_use_internal_errors(true);

class CourierGati extends CourierBase
{
    public $shipper_code_ltd   = '55604502';  // Gati Big - gati_ltd
    public $shipper_code_kwe   = '59220001';  // Gati Small - gati_kwe
    public $goods_code         = '410';
    public $receiver_code      = '99999';
    public $apiDataList;
    public $pkg_no; 
    
    public $tracking_url_big    = 'http://www.gati.com/webservices/SINGLEECOMDKTTRACK.jsp';
    public $tracking_url_small  = 'http://www.gatikwe.com/webservices/GatiKWEDktTrack.jsp';

    public $tracking_token_no   = '';
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
    }
    
    /**
    * process() - inherited method,
    * @Info - method to process api request and return response data
    * @return  Array Api response data
    * @author  MSA, <25 Dec. 17>
    */
    public function process()
    {
       $this->validateFormData();
       
       $this->getPackageNos($this->request['noOfPkg']);
       
       if(isset($this->request['type']) && $this->request['type'] == 'gati_kwe')
       {
           $this->api_end_point    = GATI_KWE_API_LINK;
           
           $this->generateAPIRequestDataKWE();
           
       }else{
           
           $this->api_end_point    = GATI_LTD_API_LINK;
           
           $this->generateAPIRequestDataLTD();
       }
       
       if(!empty($this->error)){
           return $this->error;
       }
       
       /* save request data agenst docket number*/
       $this->updateGatiDocketField(array(
                                        'post_data' => $this->apiRequestString,
                                        'used' => 1
                                        )
                                    );
        /* save request data agenst docket number*/


       $this->request();


       /* save request data agenst docket number*/
       $this->updateGatiDocketField(array(
                                        'response_data' => $this->response,
                                        )
                                    );
        /* save request data agenst docket number*/

        if(!$this->validate_response()){
           return $this->error;
        }
       
       return $this->response();
    }
    
    /**
    * updateGatiDocketField() - method to update gati docket table field value
    * @param - array list of updated fields
    * @author  MSA, <25 Dec. 17>
    */
    public function updateGatiDocketField($param = array())
    { 
        if(!empty($docket) && !empty($param)) {
            $list = array();
            foreach ($param as $key => $value) {
                $list[] = $key . '=' . "'" . $this->_db->escape($value) . "'";
            }
            if(!empty($list)) {
                $fields = implode(',', $list);
                $update_sql = "
                                UPDATE " . DB_PREFIX . "gati_dockets
                                SET
                                        ".$fields."
                                WHERE
                                    docket_no = '".$this->_db->escape($this->docket)."'

                            ";
                $this->_db->query($update_sql);
            }
        }
    }


    /**
    * request() - inherited method, 
    * @Info - creating curl api request 
    * @author  MSA, <25 Dec. 17>
    */
    public function request()
    {
        $ch = curl_init($this->api_end_point);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->apiRequestString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
        $result = curl_exec($ch);
        
        if(curl_error($ch)){
            return $response['error'] = curl_error($ch); 
        }
        
        curl_close($ch);

        $this->response = $result;
    }
    
    /**
    * response() - inherited method, 
    * @Info - processing api response data
    * @return - Array API response data 
    * @author  MSA, <25 Dec. 17>
    */
    public function response()
    {
        if(!empty($this->response))
        {
            $json = new SimpleXMLElement($this->response);

            /*Wrong XML structure error handling*/
            if(empty($json)) {
                $errors = libxml_get_errors();
                $error_strings = '';
                if(!empty($errors)) {
                    foreach ($errors as $error) {
                        $error_strings .= display_xml_error($error, $json);
                    }
                }
                libxml_clear_errors();
                if(!empty($error_strings)) {
                    $this->error['error'] = $error_strings;
                    return  $this->error['error'];    
                }else{
                    $this->error['error'] = 'Gati API server not responding';
                    return  $this->error['error']; 
                }
            }
            /*Wrong XML structure error handling*/ 

            $response = array();
            if (isset($json->result) && $json->result == "successful") {
                if ($json->reqcnt == 0) {
                    foreach ($json->details->res->errmsg->err as $error) {
                        $response['error'] = (string)$error;
                        $response['comments'] = (string)$error;
                    }
                    $response['success']  = 0;
                } elseif ($json->reqcnt == 1) {
                    $response['success'] = 1;
                }
            } else {
                $response['comments'] = (string)$json->errmsg;
                $response['error']    = (string)$json->errmsg;
                $response['success']  = 0;
            }
        }else{
            $response['comments'] = $this->response;
            $response['error']    = 'Gati server not responding!';
            $response['success']  = 0;
        }
        
        
        $response['docket']               = trim($this->docket);
        
        $this->apiResponseString = $this->response;
        
        $this->response = $response;
        
        $this->setDBSaveData();
        
        if(!$this->saveCourierDocketData()){
            $this->response['error'] = 'Error occured while saving courier data.'  ;
        }
        
        if(isset($response['success']) && $response['success']){
            
            $this->response['shipping_label_id'] = $this->addShippingLabel();
        }
        
        $this->changeGatiDocketStatus();
        
        $this->changeGatiPackageNosStatus();
        
        return $this->response;
    }
    
    /**
     *getShipperCode()  method to get shipper code for selected service type
     * @author  MSA, <Jan. 18>
     */
    public function getShipperCode()
    {
        if(isset($this->request['type']) && $this->request['type'] == 'gati_ltd')
        {
            return $this->shipper_code_ltd;
            
        }else if(isset($this->request['type']) && $this->request['type'] == 'gati_kwe')
        {
            return $this->shipper_code_kwe;
            
        }else{

            return $this->shipper_code_ltd;
        }
    }
    
    /**
     *getHSNcode()  method to get HSN code 
     * @author  MSA, <Jan. 18>
     */
    protected function getHSNcode()
    {
       if(!empty($this->orderInfo['itemdtl'])){
           
           foreach($this->orderInfo['itemdtl'] as $item)
           {
               return $item['HSCode'];
           }
       }
    }

    /**
    * generateAPIRequestDataLTD() method to create required tag with data for GATI SMALL to process in API
    * @author  MSA, <25 Dec. 17>
    */
    protected function generateAPIRequestDataLTD()
    {
        try{
            
            $this->docket = $this->getGatiDocket($this->request['type']);
            
            if($this->docket) {
                
                $request = '<gati>';
                $request .= '<pickuprequest></pickuprequest>'; 
                $request .= '<custcode>'.trim($this->getShipperCode()).'</custcode>'; 
                    $request .= '<details>';
                        $request .= '<req>';
                            $request .= '<DOCKET_NO>'.trim($this->docket).'</DOCKET_NO>';
                            $request .= '<DELIVERY_STN></DELIVERY_STN>';
                            $request .= '<GOODS_CODE>'.trim($this->goods_code).'</GOODS_CODE>';
                            $request .= '<DECL_CARGO_VAL>'.$this->getOrderTotal().'</DECL_CARGO_VAL>';
                            $request .= '<ACTUAL_WT>'.$this->validate($this->request['weight'],1).'</ACTUAL_WT>';
                            $request .= '<CHARGED_WT>'.$this->validate($this->request['weight'],1).'</CHARGED_WT>';
                            $request .= '<SHIPPER_CODE>'.$this->getShipperCode().'</SHIPPER_CODE>';
                            $request .= '<ORDER_NO>'.$this->suborder_id.'</ORDER_NO>';
                            if( $this->isCod() )
                            {
                                $request .= '<COD_AMT>'.$this->getNetPayable().'</COD_AMT>';
                                $request .= '<COD_IN_FAVOUR_OF>G</COD_IN_FAVOUR_OF>';
                            }
                            $request .= '<RECEIVER_CODE>'. $this->receiver_code.'</RECEIVER_CODE>';
                            $request .= '<RECEIVER_NAME>'. $this->checkString($this->orderInfo['customer_name']).'</RECEIVER_NAME>';
                            $request .= '<RECEIVER_ADD1>'.$this->checkString($this->orderInfo['shipping_address_1']).'</RECEIVER_ADD1>';
                            if(trim($this->orderInfo['shipping_address_2'])!=''){
                                $request .= '<RECEIVER_ADD2>'.$this->checkString($this->orderInfo['shipping_address_2']).'</RECEIVER_ADD2>';
                            }else{
                                $request .= '<RECEIVER_ADD2>'.$this->checkString($this->orderInfo['shipping_address_1']).'</RECEIVER_ADD2>';
                            }
                            $request .= '<RECEIVER_CITY>'.$this->checkString($this->orderInfo['shipping_city']).'</RECEIVER_CITY>';
                            $request .= '<RECEIVER_PHONE_NO>'.$this->orderInfo['telephone'].'</RECEIVER_PHONE_NO>';
                            $request .= '<RECEIVER_EMAIL>'.$this->validate_email($this->orderInfo['customer_email']).'</RECEIVER_EMAIL>';
                            $request .= '<RECEIVER_PINCODE>'.$this->orderInfo['shipping_postcode'].'</RECEIVER_PINCODE>';
                            $request .= '<RECEIVER_MOBILE_NO>'.$this->orderInfo['telephone'].'</RECEIVER_MOBILE_NO>';
                            $request .= '<NO_OF_PKGS>'.$this->request['noOfPkg'].'</NO_OF_PKGS>';
                            if(count($this->pkg_no) == 1) {      
                                $request .= '<FROM_PKG_NO>'.current($this->pkg_no).'</FROM_PKG_NO>';
                                $request .= '<TO_PKG_NO>'.current($this->pkg_no).'</TO_PKG_NO>';
                            }else{
                                $request .= '<FROM_PKG_NO>'.current($this->pkg_no).'</FROM_PKG_NO>';
                                $request .= '<TO_PKG_NO>'.end($this->pkg_no).'</TO_PKG_NO>';
                            }    
                            $request .= '<PKGDETAILS>';
                                    for($i=0;$i<count($this->pkg_no);$i++)
                                    {
                                        $request .= '<PKG_INFO>';
                                            $request .= '<PKG_NO>'.trim($this->pkg_no[$i]).'</PKG_NO>';
                                            $request .= '<PKG_LN>'.round(convertUnit($this->request['length'],'CM_TO_INCH'),2).'</PKG_LN>';
                                            $request .= '<PKG_BR>'.round(convertUnit($this->request['breadth'],'CM_TO_INCH'),2).'</PKG_BR>';
                                            $request .= '<PKG_HT>'.round(convertUnit($this->request['height'],'CM_TO_INCH'),2).'</PKG_HT>';
                                            $request .= '<PKG_WT>'.$this->request['weight'].'</PKG_WT>';
                                        $request .= '</PKG_INFO>';
                                    }    
                            $request .= '</PKGDETAILS>';

                            $request .= '<Cust_Date_Delivery></Cust_Date_Delivery>';
                            $request .= '<SPL_Instruction></SPL_Instruction>';
                            $request .= '<PROD_SERV_CODE>1</PROD_SERV_CODE>';
                            $request .= '<CUSTVEND_CODE>'.$this->request['gati_vendor_code'].'</CUSTVEND_CODE>';
                            $request .= '<GOODS_DESC>CLOTHS</GOODS_DESC>';
                            $request .= '<BOOKING_BASIS>2</BOOKING_BASIS>';
                            $request .= '<UOM>I</UOM>';

                            $request .= '<ORDER_QUANTITY>1</ORDER_QUANTITY>';
                            $request .= '<SELLER_NAME>'.trim($this->checkString($this->warehouseInfo['warehouse_name'])).'</SELLER_NAME>';
                            $request .= '<SELLER_ADD1>'.trim($this->checkString($this->warehouseInfo['address_1'])).'</SELLER_ADD1>';
                            $request .= '<SELLER_ADD2>'.trim($this->checkString($this->warehouseInfo['address_2'])).'</SELLER_ADD2>';
                            $request .= '<SELLER_DIST>'.trim($this->checkString($this->warehouseInfo['state'])).'</SELLER_DIST>';
                            $request .= '<SELLER_CITY>'.trim($this->checkString($this->warehouseInfo['city'])).'</SELLER_CITY>';
                            $request .= '<SELLER_PINCODE>'.trim($this->checkString($this->warehouseInfo['postcode'])).'</SELLER_PINCODE>';
                            $request .= '<SELLER_STATE_CODE>'.trim($this->checkString($this->warehouseInfo['code'])).'</SELLER_STATE_CODE>';
                            $request .= '<SELLER_PHONE_NO>'.trim(str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone'])).'</SELLER_PHONE_NO>';
                            $request .= '<SELLER_EMAIL>'.trim($this->warehouseInfo['email']).'</SELLER_EMAIL>';

                            $request .= '<SELLER_TINNO>'.trim($this->orderInfo['gstin']).'</SELLER_TINNO>';
                            $request .= '<SELLER_VATNO></SELLER_VATNO>';
                            $request .= '<EXCHANGEDKTNO></EXCHANGEDKTNO>';

                            /*EWay Bill details*/    
                            if(isset($this->request['eWayBillNo']) && !empty(trim($this->request['eWayBillNo']))) {
                                $eWayBillExpDate = $this->request['eWayBillExpDate'];
                                $eWayBillExpDate = explode(' ',$eWayBillExpDate);
                                $eWayBillExpDate = date('d-m-Y',strtotime($eWayBillExpDate[0]));
                                $request .= '<EWAYBILL>'.$this->request['eWayBillNo'].'</EWAYBILL> ';
                                $request .= '<EWB_EXP_DT>'.$eWayBillExpDate.'</EWB_EXP_DT> ';
                            }else{
                                $request .= '<EWAYBILL></EWAYBILL> ';
                                $request .= '<EWB_EXP_DT></EWB_EXP_DT> ';
                            }
                            /*EWay Bill details*/    

                            $request .= '<HSN_CODE>'.$this->getHSNcode().'</HSN_CODE>';


                        $request .= '</req>';
                    $request .= '</details>';
                $request .= '</gati>';

                $this->apiRequestString = $request;
                
            }else{
                
                return $response['error'] = 'No un-used gati docket found in DB';
                
            }
            
            
        }catch(Exception $e) {
            
            return $response['error'] = $e->getMessage();
        }
    }
    
    /**
    * generateAPIRequestDataKWE() method to create required tag with data for GATI Big to process in API
    * @author  MSA, <25 Dec. 17>
    */
    protected function generateAPIRequestDataKWE()
    {
        try{
            
            $this->docket = $this->getGatiDocket($this->request['type']);
            
            if($this->docket) {
                
                $request = '<gati>';
                $request .= '<pickuprequest></pickuprequest>'; 
                $request .= '<custcode>'.trim($this->getShipperCode()).'</custcode>'; 
                    $request .= '<details>';
                        $request .= '<req>';
                            $request .= '<DOCKET_NO>'.trim($this->docket).'</DOCKET_NO>';
                            $request .= '<DELIVERY_STN></DELIVERY_STN>';
                            $request .= '<GOODS_CODE>'.trim($this->goods_code).'</GOODS_CODE>';
                            $request .= '<DECL_CARGO_VAL>'.$this->getOrderTotal().'</DECL_CARGO_VAL>';
                            $request .= '<ACTUAL_WT>'.$this->validate($this->request['weight'],1).'</ACTUAL_WT>';
                            $request .= '<CHARGED_WT>'.$this->validate($this->request['weight'],1).'</CHARGED_WT>';
                            $request .= '<SHIPPER_CODE>'.$this->getShipperCode().'</SHIPPER_CODE>';
                            $request .= '<ORDER_NO>'.$this->suborder_id.'</ORDER_NO>';
                            if( $this->isCod() )
                            {
                                $request .= '<COD_AMT>'.$this->getNetPayable().'</COD_AMT>';
                                $request .= '<COD_IN_FAVOUR_OF>G</COD_IN_FAVOUR_OF>';
                            }
                            $request .= '<RECEIVER_CODE>'. $this->receiver_code.'</RECEIVER_CODE>';
                            $request .= '<RECEIVER_NAME>'. $this->checkString($this->orderInfo['customer_name']).'</RECEIVER_NAME>';
                            $request .= '<RECEIVER_ADD1>'.$this->checkString($this->orderInfo['shipping_address_1']).'</RECEIVER_ADD1>';

                            if(trim($this->orderInfo['shipping_address_2'])!=''){
                                $request .= '<RECEIVER_ADD2>'.$this->checkString($this->orderInfo['shipping_address_2']).'</RECEIVER_ADD2>';
                            }else{
                                $request .= '<RECEIVER_ADD2>'.$this->checkString($this->orderInfo['shipping_address_1']).'</RECEIVER_ADD2>';
                            }
                            $request .= '<RECEIVER_CITY>'.$this->checkString($this->orderInfo['shipping_city']).'</RECEIVER_CITY>';
                            $request .= '<RECEIVER_PHONE_NO>'.$this->orderInfo['telephone'].'</RECEIVER_PHONE_NO>';
                            $request .= '<RECEIVER_EMAIL>'.$this->validate_email($this->orderInfo['customer_email']).'</RECEIVER_EMAIL>';
                            $request .= '<RECEIVER_PINCODE>'.$this->orderInfo['shipping_postcode'].'</RECEIVER_PINCODE>';
                            $request .= '<RECEIVER_MOBILE_NO>'.$this->orderInfo['telephone'].'</RECEIVER_MOBILE_NO>';
                            $request .= '<NO_OF_PKGS>'.$this->request['noOfPkg'].'</NO_OF_PKGS>';
                            if(count($this->pkg_no) == 1) {      
                                $request .= '<FROM_PKG_NO>'.current($this->pkg_no).'</FROM_PKG_NO>';
                                $request .= '<TO_PKG_NO>'.current($this->pkg_no).'</TO_PKG_NO>';
                            }else{
                                $request .= '<FROM_PKG_NO>'.current($this->pkg_no).'</FROM_PKG_NO>';
                                $request .= '<TO_PKG_NO>'.end($this->pkg_no).'</TO_PKG_NO>';
                            }    
                            $request .= '<PKGDETAILS>';
                                    for($i=0;$i<count($this->pkg_no);$i++)
                                    {
                                        $request .= '<PKG_INFO>';
                                            $request .= '<PKG_NO>'.trim($this->pkg_no[$i]).'</PKG_NO>';
                                            $request .= '<PKG_LN>'.round(convertUnit($this->request['length'],'CM_TO_INCH'),2).'</PKG_LN>';
                                            $request .= '<PKG_BR>'.round(convertUnit($this->request['breadth'],'CM_TO_INCH'),2).'</PKG_BR>';
                                            $request .= '<PKG_HT>'.round(convertUnit($this->request['height'],'CM_TO_INCH'),2).'</PKG_HT>';
                                            $request .= '<PKG_WT>'.$this->request['weight'].'</PKG_WT>';
                                        $request .= '</PKG_INFO>';
                                    }    
                            $request .= '</PKGDETAILS>';
                            $request .= '<PROD_SERV_CODE>1</PROD_SERV_CODE>';
                            $request .= '<CUSTVEND_CODE>'.$this->request['gati_vendor_code'].'</CUSTVEND_CODE>';
                            $request .= '<GOODS_DESC>CLOTHS</GOODS_DESC>';
                            $request .= '<BOOKING_BASIS>2</BOOKING_BASIS>';
                            $request .= '<UOM>I</UOM>';
                            $request .= '<Consignor_GSTIN_No>'.trim($this->orderInfo['gstin']).'</Consignor_GSTIN_No>';
                            $request .= '<Receiver_GSTIN_No>Not-Registered</Receiver_GSTIN_No>';
                            $request .= '<Cust_Date_Delivery></Cust_Date_Delivery>';
                            $request .= '<SPL_Instruction></SPL_Instruction>';

                            /*EWay Bill details*/    
                            if(isset($this->request['eWayBillNo']) && !empty(trim($this->request['eWayBillNo']))) {
                                $eWayBillExpDate = $this->request['eWayBillExpDate'];
                                $eWayBillExpDate = explode(' ',$eWayBillExpDate);
                                $eWayBillExpDate = date('d-m-Y',strtotime($eWayBillExpDate[0]));
                                $request .= '<EWAYBILL>'.$this->request['eWayBillNo'].'</EWAYBILL> ';
                                $request .= '<EWB_EXP_DT>'.$eWayBillExpDate.'</EWB_EXP_DT> ';
                            }else{
                                $request .= '<EWAYBILL></EWAYBILL> ';
                                $request .= '<EWB_EXP_DT></EWB_EXP_DT> ';
                            }
                            /*EWay Bill details*/     

                            $request .= '<HSN_CODE>'.$this->getHSNcode().'</HSN_CODE>';

                        $request .= '</req>';
                    $request .= '</details>';
                $request .= '</gati>';

                $this->apiRequestString = $request;
                
            }else{
                
                return $response['error'] = 'No un-used gati docket found in DB';
                
            }
            
        }catch(Exception $e) {
            
            return $response['error'] = $e->getMessage();
        }
    }
    
    /**
    * validateFormData() method to validate form data
    * @author  MSA, <Jan. 17>
    */
    public function validateFormData()
    {
        if(!isset($this->request['docket']) || trim($this->request['docket'])=='' || trim($this->request['docket']) == '0') 
        {
           $this->error['error'] = 'Docket number can not blank!' ;
        }
        if(!isset($this->request['type']) || trim($this->request['type'])=='') 
        {
           $this->error['error'] = 'Service type can not blank!' ;
        }        
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
        if(!isset($this->request['noOfPkg']) || trim($this->request['noOfPkg'])=='' || trim($this->request['noOfPkg'])=='0') 
        {
           $this->error['error'] = 'No Of Packages can not be 0' ;
        }
    }
    
    /**
    * changeGatiDocketStatus() method to update Gati docket status 
    * @author  MSA, <Jan. 18>
    */
    public function changeGatiDocketStatus() {
        $docket_sql = "UPDATE " . DB_PREFIX . "gati_dockets gd
                        SET 
                            gd.used         = '1',
                            gd.is_success   = '".$this->_db->escape($this->courierDataToSaveInDB['is_success']) ."',
                            gd.order_no     = '".$this->_db->escape($this->courierDataToSaveInDB['order_no'])."',
                            gd.weight       = '".$this->_db->escape($this->courierDataToSaveInDB['weight'])."',
                            gd.comments     = '".$this->_db->escape($this->courierDataToSaveInDB['comments']) ."',
                            gd.post_data    = '".$this->_db->escape($this->courierDataToSaveInDB['post_values']) ."',
                            gd.response_data= '".$this->_db->escape($this->courierDataToSaveInDB['response_value']) ."'
                        WHERE 
                            gd.docket_no  = " . $this->_db->escape($this->courierDataToSaveInDB['docket_no']);
        if ($this->_db->query($docket_sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * getPackageNos() method to get Gati package numbers
    * @author  MSA, <Feb. 18>
    */
    public function getPackageNos($noOfPkg = 1)
    {
        $docket_sql = "SELECT gd.package_no "
                    . "FROM " . DB_PREFIX . "gati_packages gd "
                    . "WHERE gd.used = 0 limit " . $this->_db->escape($noOfPkg);
        $docket_query = $this->_db->query($docket_sql);
        $packages = array();
        if ($docket_query->num_rows) {
            if(!empty($docket_query->rows)) {
               foreach($docket_query->rows as $row)
               {
                 $packages[] = $row['package_no'];  
               }
            }
        }
        if(empty($packages)) {
           $this->error['error'] = 'Gati active package numbers not found' ; 
        }else{
           $this->pkg_no = $packages;
        }
    }
    
    /**
    * changeGatiPackageNosStatus() method to updates Gati package numbers status for used
    * @author  MSA, <Feb. 18>
    */
    public function changeGatiPackageNosStatus() {
        
        if(empty($this->pkg_no)) return ;
        
        $package_sql = "UPDATE " . DB_PREFIX . "gati_packages gd
    					SET 
                                            gd.used         = '1'
                                        WHERE gd.package_no  IN (".implode(',',$this->pkg_no).") " ;

        if ($this->_db->query($package_sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * isPincodeServiceable() - method to check for serviceable pin code area,
    * @Info  method to check for serviceable pin code area
    * @author  MSA, <Feb. 18>
    */
    public function isPincodeServiceable()
    {
        if(!isset($this->request['pincode']) || trim($this->request['pincode'])=='' || trim($this->request['pincode'])=='0') 
        {
           $this->error['error'] = 'Pincode value can not be blank or o' ;
        }

        if(!empty($this->error)){
           return $this->error;
        }
        
        $sql = "SELECT `serviceability` FROM "
                . DB_PREFIX . "gati_pincodes "
                . " WHERE pincode = '".$this->_db->escape($this->request['pincode'])."' AND status = 1 " ;
        
        $query = $this->_db->query($sql);
        if ($query->num_rows) {
           $response['success'] = 1;
        }else{
           $response['success'] = 0;
        }
        return $response;
    }
    
    
    /**
    * getGatiDocket() method to get un-used Gati docket number to generate new shipment
    * @author  MSA, <March. 18>
    */
    public function getGatiDocket($service_type='gati_ltd') {
        
        $docket_sql = "SELECT docket_no "
                    . "FROM " . DB_PREFIX . "gati_dockets "
                    . "WHERE type='". $this->_db->escape($service_type)."' 
                         AND used = 0 
                      ORDER BY id ASC LIMIT 1 ";
        $docket_query = $this->_db->query($docket_sql);

        if ($docket_query->num_rows) {
            return $docket_query->row['docket_no'];
        } else {
            return false;
        }
    }


    /**
    * addReverseShipment() - method to add reverse shipment data,
    * @author  MSA, <March. 18>
    */
    public function addReverseShipment()
    {
       
        $this->error['error'] = 'Gati server not responding!!';
        return $this->error;
    }

    /**
    * validateReverseShipmentData() - method to validate reverse shipment form data,
    * @author  MSA, <March. 18>
    */
    protected function validateReverseShipmentData()
    {
        $this->validateReverseShipmentFormData();
         /* check pincode for serviceable status */
            $isServiceable = $this->isPincodeServiceable($this->request['cust_pin']);
            if(!$isServiceable['success']){
                $this->error['error'] = $this->request['cust_pin'].' PIN is not serviceable.';
            }
        /* check pincode for serviceable status */ 
    }

    /**
    * addForwardShipment() - method to generate return forward shipment,
    * @author  MSA, <May. 18>
    */
    public function addForwardShipment($this_obj)
    {
       $this->validateReverseShipmentFormData();
       
       $this->getPackageNos();

       if(!empty($this->error)){
           return $this->error;
       }

       $this->generateForwardShipmentApiRequest();

       $this->api_end_point    = GATI_LTD_API_LINK;

       $this->request();

        if(!$this->validate_response()){
           return $this->error;
        }
       
        $status = $this->forwardShipmentResponse($this_obj);
        
        return $status;
        
    }

    protected function forwardShipmentResponse($this_obj)
    {

        $status = array();

        if(!empty($this->response)) 
        {

            $json = new SimpleXMLElement($this->response);

            if(empty($json)) {
                $errors = libxml_get_errors();
                $error_strings = '';
                if(!empty($errors)) {
                    foreach ($errors as $error) {
                        $error_strings .= display_xml_error($error, $json);
                    }
                }
                libxml_clear_errors();
                if(!empty($error_strings)) {
                    $this->error['error'] = $error_strings;
                    return  $this->error['error'];    
                }else{
                    $this->error['error'] = 'Gati API server not responding';
                    return  $this->error['error']; 
                }
            }

            if (isset($json->result) && $json->result == "successful") {
                if ($json->reqcnt == 0) {
                    foreach ($json->details->res->errmsg->err as $error) {
                        $response['error'] = (string)$error;
                        $response['comments'] = (string)$error;
                    }
                    $response['success']  = 0;
                } elseif ($json->reqcnt == 1) {
                    $response['success'] = 1;
                    $this->apiResponseString = serialize($this->response);
                    $response['docket']  = $this->docket;
                    $response['result']  = $this->response;
                }
            } else {
                $response['comments'] = (string)$json->errmsg;
                $response['error']    = (string)$json->errmsg;
                $response['success']  = 0;
            }


            if(!empty($response['success']))
            {
                $status['success']    = $response['success'];

                /* Update DB tables*/
                $this->courierDataToSaveInDB['aws_docket']  = $this->docket;
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
                
                $this->courierDataToSaveInDB['is_success']      = $response['success'];
                $this->courierDataToSaveInDB['docket_no']       = $this->docket;
                $this->courierDataToSaveInDB['post_values']     = $this->courierDataToSaveInDB['req'];    
                $this->courierDataToSaveInDB['response_value']  = $this->courierDataToSaveInDB['req'];    
                $this->courierDataToSaveInDB['comments']        = $response['comments'] ?? '';

                //$this->saveReverseShipmentDocketData();
                $return_shipment_id = $this->saveForwardShipmentTrackingData();
                $this->updateReverseShipmentReturnData(
                                                    $return_shipment_id, 
                                                    RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
                                                    true
                                                );
                
               /*checking for reverse shipment email */  
                $this->sendForwardShipmentEmailAlertMessage($this_obj);

                $this->changeGatiDocketStatus();
                $this->changeGatiPackageNosStatus();

            }else{
                $status['error']    = 'Gati API not responding!'; 
            }

        }else{
            $status['error']    = 'Gati API not responding!!';
        }
        
        $status['response1'] = $this->response;
        $status['response2'] = $response;

        return $status;
    }

    protected function generateForwardShipmentApiRequest()
    { 
        try{

            $this->docket = $this->getGatiDocket();
            
            if($this->docket) {
                
                $request = '<gati>';
                $request .= '<pickuprequest></pickuprequest>'; 
                $request .= '<custcode>'.trim($this->getShipperCode()).'</custcode>'; 
                    $request .= '<details>';
                        $request .= '<req>';
                            $request .= '<DOCKET_NO>'.trim($this->docket).'</DOCKET_NO>';
                            $request .= '<DELIVERY_STN></DELIVERY_STN>';
                            $request .= '<GOODS_CODE>'.trim($this->goods_code).'</GOODS_CODE>';
                            $request .= '<DECL_CARGO_VAL>'.$this->getReverseShipmentTotalAmount().'</DECL_CARGO_VAL>';
                            $request .= '<ACTUAL_WT>'.$this->validate($this->request['package_weight'],1).'</ACTUAL_WT>';
                            $request .= '<CHARGED_WT>'.$this->validate($this->request['package_weight'],1).'</CHARGED_WT>';
                            $request .= '<SHIPPER_CODE>'.$this->getShipperCode().'</SHIPPER_CODE>';
                            $request .= '<ORDER_NO>'.$this->request['order_no'].'</ORDER_NO>';
                            $request .= '<RECEIVER_CODE>'. $this->receiver_code.'</RECEIVER_CODE>';
                            $request .= '<RECEIVER_NAME>'. $this->checkString($this->request['cust_name']).'</RECEIVER_NAME>';
                            $request .= '<RECEIVER_ADD1>'.$this->checkString($this->request['cust_address1']).'</RECEIVER_ADD1>';
                            if(trim($this->request['cust_address2'])!=''){
                                $request .= '<RECEIVER_ADD2>'.$this->checkString($this->request['cust_address2']).'</RECEIVER_ADD2>';
                            }else{
                                $request .= '<RECEIVER_ADD2>'.$this->checkString($this->request['cust_address1']).'</RECEIVER_ADD2>';
                            }
                            $request .= '<RECEIVER_CITY>'.$this->checkString($this->request['cust_city']).'</RECEIVER_CITY>';
                            $request .= '<RECEIVER_PHONE_NO>'.$this->request['cust_telephone'].'</RECEIVER_PHONE_NO>';
                            $request .= '<RECEIVER_EMAIL>'.$this->validate_email($this->request['cust_email']).'</RECEIVER_EMAIL>';
                            $request .= '<RECEIVER_PINCODE>'.$this->request['cust_pin'].'</RECEIVER_PINCODE>';
                            $request .= '<RECEIVER_MOBILE_NO>'.$this->request['cust_telephone'].'</RECEIVER_MOBILE_NO>';
                            $request .= '<NO_OF_PKGS>1</NO_OF_PKGS>';
                            if(count($this->pkg_no) == 1) {      
                                $request .= '<FROM_PKG_NO>'.current($this->pkg_no).'</FROM_PKG_NO>';
                                $request .= '<TO_PKG_NO>'.current($this->pkg_no).'</TO_PKG_NO>';
                            }else{
                                $request .= '<FROM_PKG_NO>'.current($this->pkg_no).'</FROM_PKG_NO>';
                                $request .= '<TO_PKG_NO>'.end($this->pkg_no).'</TO_PKG_NO>';
                            }    
                            $request .= '<PKGDETAILS>';
                            for($i=0;$i<count($this->pkg_no);$i++)
                            {
                                $request .= '<PKG_INFO>';
                                    $request .= '<PKG_NO>'.trim($this->pkg_no[$i]).'</PKG_NO>';
                                    $request .= '<PKG_LN>15</PKG_LN>';
                                    $request .= '<PKG_BR>15</PKG_BR>';
                                    $request .= '<PKG_HT>15</PKG_HT>';
                                    $request .= '<PKG_WT>'.$this->request['package_weight'].'</PKG_WT>';
                                $request .= '</PKG_INFO>';
                            }    
                            $request .= '</PKGDETAILS>';

                            $request .= '<Cust_Date_Delivery></Cust_Date_Delivery>';
                            $request .= '<SPL_Instruction></SPL_Instruction>';
                            $request .= '<PROD_SERV_CODE>1</PROD_SERV_CODE>';
                            $request .= '<CUSTVEND_CODE>'.$this->warehouseInfo['gati_vendor_code'].'</CUSTVEND_CODE>';
                            $request .= '<GOODS_DESC>CLOTHS</GOODS_DESC>';
                            $request .= '<BOOKING_BASIS>2</BOOKING_BASIS>';
                            $request .= '<UOM>I</UOM>';

                            $request .= '<ORDER_QUANTITY>1</ORDER_QUANTITY>';
                            $request .= '<SELLER_NAME>'.trim($this->checkString($this->warehouseInfo['warehouse_name'])).'</SELLER_NAME>';
                            $request .= '<SELLER_ADD1>'.trim($this->checkString($this->warehouseInfo['address_1'])).'</SELLER_ADD1>';
                            $request .= '<SELLER_ADD2>'.trim($this->checkString($this->warehouseInfo['address_2'])).'</SELLER_ADD2>';
                            $request .= '<SELLER_DIST>'.trim($this->checkString($this->warehouseInfo['state'])).'</SELLER_DIST>';
                            $request .= '<SELLER_CITY>'.trim($this->checkString($this->warehouseInfo['city'])).'</SELLER_CITY>';
                            $request .= '<SELLER_PINCODE>'.trim($this->checkString($this->warehouseInfo['postcode'])).'</SELLER_PINCODE>';
                            $request .= '<SELLER_STATE_CODE>'.trim($this->checkString($this->warehouseInfo['code'])).'</SELLER_STATE_CODE>';
                            $request .= '<SELLER_PHONE_NO>'.trim(str_replace(['(+91)',' '],['',''],$this->warehouseInfo['telephone'])).'</SELLER_PHONE_NO>';
                            $request .= '<SELLER_EMAIL>'.trim($this->warehouseInfo['email']).'</SELLER_EMAIL>';

                            $request .= '<SELLER_TINNO>'.trim($this->warehouseInfo['gstin']).'</SELLER_TINNO>';
                            $request .= '<SELLER_VATNO></SELLER_VATNO>';
                            $request .= '<EXCHANGEDKTNO></EXCHANGEDKTNO>';

                            $request .= '<EWAYBILL></EWAYBILL> ';
                            $request .= '<EWB_EXP_DT></EWB_EXP_DT> ';   

                            $request .= '<HSN_CODE>'.$this->getHSNCodeForForwardShipment().'</HSN_CODE>';

                        $request .= '</req>';
                    $request .= '</details>';
                $request .= '</gati>';

                $this->apiRequestString = $request;
                
            }else{
                
                return $response['error'] = 'No un-used gati docket found in DB';
            }
            
            
        }catch(Exception $e) {
            
            return $response['error'] = $e->getMessage();
        }
    }

    protected function getHSNCodeForForwardShipment()
    {
        if(!empty($this->orderInfo['suborder'])){
           
           foreach($this->orderInfo['suborder'] as $item)
           {
               if(!empty($item['order_product']))
               {
                 return $item['order_product'][0]['hsn_code'];
               }
           }
       }
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
        $this->tracking_url   = $this->getGatiTrackingURL($this->docket);
        $ch = curl_init($this->tracking_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        $response = array();
        if(curl_error($ch)){
            $this->response['error'] = curl_error($ch); 
        }else{
           $this->response = $result; 
        }
        
        curl_close($ch);
    }

    /**
    * docketTrackingResponse() - method to process docket tracking API response
    * @author  MSA, <March. 18>
    */
    protected function docketTrackingResponse()
    {
        $xml = new SimpleXMLElement($this->response);
        $tracking_history = array();
        if(!empty($xml->dktinfo->TRANSIT_DTLS)) {
            $tracking_history['response'] = 'success';
            $tracking_history['history'] = $this->getTrackingHtml($xml->dktinfo->TRANSIT_DTLS->ROW);
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
        return date('d-m-y h:i a',strtotime($date.' '.$time));
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
             foreach($tracking_detail as $value) {
                $html .=   '<tr>';
                $html .=       '<th>'.$this->processDate($value->INTRANSIT_DATE,$value->INTRANSIT_TIME).'</th>';
                $html .=       '<th>'.$value->INTRANSIT_LOCATION.'</th>';
                $html .=       '<th>'.$value->INTRANSIT_STATUS.'</th>';
                $html .=    '</tr>';
             }
        } 
        return $html;
    }

    protected function getGatiTrackingURL($docket_no = '')
    {
        $docket_type = $this->getGatiDocketType($docket_no);
        if($docket_type == 'gati_ltd'){ 
            return $this->tracking_url_big . '?p1=' . $docket_no . '&p2=A81BFCBC4CA0D373';
        }else{ 
            return $this->tracking_url_small . '?p1=' . $docket_no . '&p2=50826005497E20EE';
        }
    }

    protected function getGatiDocketType($docket_no = '')
    {
        $docket_sql = "SELECT type 
                        FROM " . DB_PREFIX . "gati_dockets 
                        WHERE  docket_no = '".$this->_db->escape($docket_no)."' 
                        ";
        $docket_type = $this->_db->query($docket_sql);
        if ($docket_type->num_rows) {
            return $docket_type->row['type'];
        } else {
            return false;
        }
    }
    
}


