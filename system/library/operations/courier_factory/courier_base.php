<?php
//use crmConnector\crmConnector;

/**
 * CourierBase
 *
 * @info - Base class with required methods 
 *          to implemented by courier API classes.
 *
 * @author @MSA
 */
class CourierBase 
{
   public $registry         = array();
   public $_db              = array(); 
   public $load              = array();    
   public $request          = array();
   public $response         = array();
   public $order_id;
   public $suborder_id;
   public $courier_name;
   public $orderInfo        = array();
   public $warehouseInfo    = array();
   public $totals           = array();
   public $docket;
   public $error            = array();
   public $post_mode;
   public $post_type;
   public $response_type;
   public $courierDataToSaveInDB    = array();
   public $api_end_point;
   public $apiRequestString = array();
   public $apiResponseString= array();
   public $crmRequestData   = array();
   
/*Return data settings*/   
  protected $action_name    = 'REVERSE SHIPMENT GENERATED';
  protected $return_url     = 'http://www.wholesalebox.com/';

   public function __construct($param = array()) 
   {
       //$this->checkData($param);
       $this->setData($param);
   }
   
   /*Methods, implemented by child classes*/
   public function validateFormData(){}
   public function process(){}
   public function request(){}
   public function response(){}
   
   
   private function checkData(array $param)
   {   
       if(!isset($param['registry'])) {
           $this->error['error'] = 'Registry object not found.';
       }
       if(!isset($param['db'])) {
           $this->error['error'] = 'DB object not found.';
       }
       if(!isset($param['courier_name'])) {
           $this->error['error'] = 'Courier name not found.';
       }
       if(!isset($param['request'])) {
           $this->error['error'] = 'Request object can not be blank.';
       }
       if(!isset($param['request']['order_id'])) {
           $this->error['error'] = 'Order Id can not be blank.';
       }
       if(!isset($param['request']['suborder_id'])) {
           $this->error['error'] = 'Sub-Order Id can not be blank.';
       }
       if(!isset($param['orderInfo'])) {
           $this->error['error'] = 'Order details object not found.';
       }
       /* OrderInfo part - InvoiceNo [ BuyerInvoice->getOrderProductsDetailWithOrderInfo() ]*/
       if(empty($param['orderInfo']['InvoiceNo'])) 
       {
          $this->error['error'] = 'InvoiceNo not generated for this order.' ;
       }
       if(empty($param['warehouseInfo'])) {
           $this->error['error'] = 'Warehouse object not found.';
       }
       if(!isset($param['totals'])) {
           $this->error['error'] = 'Order totals object not found.';
       }
   }
   private function setData(array $param)
   {
       $this->registry      = isset($param['registry']) ? $param['registry'] : array();
       $this->_db           = isset($param['db']) ? $param['db'] : array();
       $this->courier_name  = isset($param['courier_name']) ? $param['courier_name'] : array();
       $this->request       = isset($param['request']) ? $param['request'] : array();
       $this->order_id      = isset($param['request']['order_id']) ? $param['request']['order_id'] : 0;
       $this->suborder_id   = isset($param['request']['suborder_id']) ?$param['request']['suborder_id'] : 0;
       $this->orderInfo     = isset($param['orderInfo']) ? $param['orderInfo'] : array();
       $this->warehouseInfo = isset($param['warehouseInfo']) ? $param['warehouseInfo'] : array();
       $this->totals        = isset($param['totals']) ? $param['totals'] : array();
       $this->docket        = isset($param['request']['docket']) ? $param['request']['docket'] : '';
   }
   
   /**
    * isCod() - method to check for COD order 
    * @return  boolean true/false 
    * @author  MSA, <25 Dec. 17>
    */
   public function isCod()
   {
        if( $this->getPaymentMethod() == 'cod' ) {
            return true;
        }
      return false;
   }
   
   /**
    * getNetPayable() - method to get order net payable amount 
    * @return float  net payable amount 
    * @author  MSA, <25 Dec. 17>
    */
   public function getNetPayable()
   {
        if ( !empty($this->totals['net_amount']) && $this->totals['net_amount']['value'] > 0 ) {
            return round( $this->totals['net_amount']['value'],2 );
        }
        return 0;    
   }
   
   /**
    * getOrderTotal() - method to get order total amount, 
    * @return float  net payable amount 
    * @author  MSA, <25 Dec. 17>
    */
   public function getOrderTotal()
   {
       if ( !empty($this->totals['total_amt']) && $this->totals['total_amt']['value'] > 0 ) {
            return round( $this->totals['total_amt']['value'], 2 );
        }
        return 0;
   }
   
   /**
    * getPaymentMethod() - method to get order payment method 
    * @return float  net payable amount 
    * @author  MSA, <25 Dec. 17>
    */
   public function getPaymentMethod()
   {

    $payment_code = $this->orderInfo['payment_code'] ?? '';
    $payment_code = strtolower(trim($payment_code));
       if(
          ($payment_code == "cod" || in_array($payment_code, CREDIT_PAYMENT_CODES) )
          && $this->getNetPayable() > 0 
        ) { 
            return 'cod';
        }else{
            return 'prepaid';
        }
   }
   
   /**
    * validate() - method to validate string for empty data 
    * @param  string $string
    * $param  string $default 
    * @return string $string
    * @author  MSA, <25 Dec. 17>
    */
   public function validate(string $string, string $default = '')
    {
        if( !empty($string) ) {
            return trim($string);
        }
        return $default;
    }
    
    /**
    * getDocket() - method to get docket number 
    * @return integer docket number
    * @author  MSA, <25 Dec. 17>
    */
    public function getDocket()
    {
        return $this->docket;
    }
    
    /**
    * checkString() - method to convert special char in required formatted string
    * @return string string
    * @author  MSA, <25 Dec. 17>
    */
    public function checkString(string $string)
    {
       /*Remove special chars from string */    
        $string = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($string));
        
        return trim( htmlspecialchars( $string, ENT_QUOTES, "UTF-8" ) );
    }
    
    /**
     * Method to get courier partner id by courier title
     * @param String $title
     * @return integer $id
     * Author: MSA
     */
    public function getCourierId()
    {
        $query = $this->_db->query("SELECT id
                                    FROM " . DB_PREFIX . "courier_partners
                                    WHERE courier_name = '" . $this->_db->escape($this->courier_name) . "'");
        if($query->num_rows) {
                return $query->row['id'];
            } else {
                return false;
            }
    }
    /**
     * Method to get courier partner Name by courier title
     * @param integer $courier_id
     * @return string courier_name
     * Author: MSA (Jan, 2018)
     */
    public function getCourierPartnerNameById(int $courier_id)
    {
        if(!empty($courier_id)) {
            
            $get_courier_name = $this->_db->query("SELECT courier_name
                                              FROM " . DB_PREFIX . "courier_partners
                                              WHERE id = '" . $this->_db->escape($courier_id) . "'");
            if($get_courier_name->num_rows) {
                return $get_courier_name->row['courier_name'];
            } else {
                return false;
            }
        }
        return;
    }
    
    /**
    * setDBData() method to set data to save in DB
    * @author  MSA, <25 Dec. 17>
    */
    protected function setDBSaveData()
    {
        $db_data = array();
        $db_data['warehouse_id']          = (int)$this->request['warehouse_id'];
        $db_data['weight']                = (float)$this->request['weight'];
        $db_data['order_id']              = (int)$this->order_id;
        $db_data['order_no']              = (int)$this->orderInfo['order_no'];
        $db_data['suborder_id']           = $this->suborder_id;
        $db_data['docket_no']             = $this->docket;
        $db_data['courier_id']            = (int)$this->getCourierId();
        
        if(strtolower($this->post_type) == 'json') // Gati & Dotzot
        {
            $db_data['post_values']           = json_encode($this->apiRequestString);
        }else{
            $db_data['post_values']           = $this->apiRequestString;
        }
        
        $db_data['response_value']        = $this->apiResponseString;
        
        $db_data['response_type']         = $this->response_type;
        $db_data['payment_mode']          = $this->getPaymentMethod();
        $db_data['post_mode']             = $this->post_mode;
        $db_data['post_type']             = $this->post_type;
        $db_data['post_url']              = $this->api_end_point;
        $db_data['cod_amount']            = 0;
        if($this->isCod()){
            $db_data['cod_amount']        = (float)$this->getNetPayable();
        }
        $db_data['parcel_amount']         = (float)$this->getOrderTotal();
        $db_data['date_added']            = date('Y-m-d');
        
        $db_data['comments'] = isset($this->response['comments']) ? $this->response['comments'] : '';
        
        $db_data['is_success'] = 0;
        if(isset($this->response['success']) && $this->response['success']){
            $db_data['is_success'] = 1;
        }
        /*For Fedex label file name*/
        $db_data['file_name']   = isset($this->response['file_name']) ? $this->response['file_name'] : '';
        
        $this->courierDataToSaveInDB = $db_data;
        
        /*Set CRM Push Data For Docket Tracking*/
          $this->crmRequestData['order_no']     = (int)$this->orderInfo['order_no'];
          $this->crmRequestData['suborder_id']  = $this->suborder_id;
          $this->crmRequestData['customer_id']  = (int)$this->orderInfo['customer_id'];
          $this->crmRequestData['tracking_no']  = $this->docket;
          $this->crmRequestData['courier_name'] = $this->courier_name;
        /*Set CRM Push Data For Docket Tracking*/
    }
    
    /**
     * saveCourierDocketData() Method to save courier data
     * Author: MSA (Jan, 2018)
     */
    public function saveCourierDocketData()
    {
        $docket_sql = "INSERT INTO ".DB_PREFIX."courier_dockets
                    SET 
                        order_id                = '".$this->_db->escape($this->courierDataToSaveInDB['order_id'])."',
                        suborder_id             = '".$this->_db->escape($this->courierDataToSaveInDB['suborder_id'])."',
                        courier_partners_id     = '".$this->_db->escape($this->courierDataToSaveInDB['courier_id'])."',
                        docket_no               = '".$this->_db->escape($this->courierDataToSaveInDB['docket_no'])."',
                        payment_mode            = '".$this->_db->escape($this->courierDataToSaveInDB['payment_mode'])."',
                        cod_amount              = '".$this->_db->escape($this->courierDataToSaveInDB['cod_amount'])."',
                        parcel_amount           = '".$this->_db->escape($this->courierDataToSaveInDB['parcel_amount'])."',
                        post_mode               = '".$this->_db->escape($this->courierDataToSaveInDB['post_mode'])."',
                        post_type               = '".$this->_db->escape($this->courierDataToSaveInDB['post_type'])."',
                        post_url                = '".$this->_db->escape($this->courierDataToSaveInDB['post_url'])."',
                        post_values             = '".$this->_db->escape($this->courierDataToSaveInDB['post_values'])."',
                        response_type           = '".$this->_db->escape($this->courierDataToSaveInDB['response_type'])."',
                        response_value          = '".$this->_db->escape($this->courierDataToSaveInDB['response_value'])."',
                        date_added              = '".$this->_db->escape($this->courierDataToSaveInDB['date_added'])."',
                        active                  = 1
      ";
        if ($this->_db->query($docket_sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * addShippingLabel() Method to save shipping label data
     * Author: MSA (Jan, 2018)
     */
    public function addShippingLabel()
    {
        $sql = "INSERT INTO ". DB_PREFIX ."shipping_label
                SET order_id            = '" .$this->_db->escape($this->courierDataToSaveInDB['order_id'])."',
                    suborder_id         = '" .$this->_db->escape($this->courierDataToSaveInDB['suborder_id'])."',
                    from_warehouse_id   = '" .$this->_db->escape($this->courierDataToSaveInDB['warehouse_id']). "',
                    courier_name        = '" .$this->_db->escape($this->getCourierPartnerNameById($this->courierDataToSaveInDB['courier_id'])). "',
                    tracking_no         = '" .$this->_db->escape($this->courierDataToSaveInDB['docket_no']). "',
                    weight              = '" .$this->_db->escape($this->courierDataToSaveInDB['weight']). "',
                    file_name           = '" .$this->_db->escape($this->courierDataToSaveInDB['file_name']). "',    
                    barcode             = '1'";
        
        $this->_db->query($sql);
        return $this->_db->getLastId();
    }
    
    
    /**
    * updateOrderTrackingNoInCrm() method to register new docket on CRM App for tracking
    * @Author: MSA (Jan, 2018) 
    */
   public function updateOrderTrackingNoInCrm()
   {  
       /*
       $crm_connector = new crmConnector();
       $crm_connector->entity = 'Order';
       $crm_connector->action = 'updateOrderTrackingNo';
       $crm_connector->data = $this->crmRequestData;
       $crm_connector->pushToCrm();
       */
   }
    
  /**
    * validateFormData() - method to validate reverse shipment request data
    * @author  MSA, <Feb. 18>
    */
    public function validateReverseShipmentFormData()
    {
        if(!isset($this->request['order_no']) || trim($this->request['order_no'])=='' || trim($this->request['order_no'])=='0') 
        {
           $this->error['error'] = 'Order no value can not be blank or 0' ;
        }
        if(!isset($this->request['order_id']) || trim($this->request['order_id'])=='' || trim($this->request['order_id'])=='0') 
        {
           $this->error['error'] = 'Order id value can not be blank or 0' ;
        }
        if(!isset($this->request['selected_master_return_id']) || trim($this->request['selected_master_return_id'])=='' || trim($this->request['selected_master_return_id'])=='0') 
        {
           $this->error['error'] = 'Master return ids value can not be blank or 0' ;
        }
        if(!isset($this->request['package_desc']) || trim($this->request['package_desc'])=='') 
        {
           $this->error['error'] = 'Package description value can not be blank' ;
        }
        if(!isset($this->request['package_value']) || trim($this->request['package_value'])=='') 
        {
           $this->error['error'] = 'Package value can not be blank' ;
        }
        if(!isset($this->request['package_qty']) || trim($this->request['package_qty'])=='') 
        {
           $this->error['error'] = 'Package qty can not be blank' ;
        }
        if(!isset($this->request['package_weight']) || trim($this->request['package_weight'])=='' || trim($this->request['package_weight'])=='0') 
        {
           $this->error['error'] = 'Package weight can not be blank or 0' ;
        }
        if(!isset($this->request['cust_name']) || trim($this->request['cust_name'])=='' ) 
        {
           $this->error['error'] = 'Customer name can not be blank' ;
        }
        if(empty($this->request['cust_address1']) && empty($this->request['cust_address2'])) 
        {
           $this->error['error'] = 'Customer address can not be blank' ;
        }
        if(!isset($this->request['cust_city']) || trim($this->request['cust_city'])=='' ) 
        {
           $this->error['error'] = 'Customer city can not be blank' ;
        }
        if(!isset($this->request['cust_pin']) || trim($this->request['cust_pin'])=='' ) 
        {
           $this->error['error'] = 'Customer pincode can not be blank' ;
        }
        if(!isset($this->request['cust_telephone']) || trim($this->request['cust_telephone'])=='' ) 
        {
           $this->error['error'] = 'Customer phone number can not be blank' ;
        }
    } 

  /**
  * saveReverseShipmentDocketData() - method to save return docket data
  * @author  MSA, <Feb. 18>
  */  
  public function saveReverseShipmentDocketData()
  {
      if(!empty($this->courierDataToSaveInDB))
      {
        $sql = "INSERT INTO " . DB_PREFIX . "return_dockets 
                        SET 
                          docket_no         = '". $this->_db->escape($this->courierDataToSaveInDB['aws_docket']). "', 
                          date_added        = Now(), 
                          used              = 1, 
                          master_return_id  = '" .$this->_db->escape($this->courierDataToSaveInDB['master_return_id']). "', 
                          req_res           = '" .$this->_db->escape($this->courierDataToSaveInDB['req_res']). "'
                        ";
          $this->_db->query($sql);
      }
  }

  /**
  * saveReverseShipmentTrackingData() - method to save return shipment tracking data
  * @author  MSA, <Feb. 18>
  */ 
  public function saveReverseShipmentTrackingData()
  {
    if(!empty($this->courierDataToSaveInDB))
    {
        $token_no = !empty($this->courierDataToSaveInDB['token_no'])
                      ? $this->courierDataToSaveInDB['token_no']
                      : '';
      $shipping_details = array(
        'name'          => !empty($this->request['cust_name'])?$this->request['cust_name']:'',
        'email'         => !empty($this->request['cust_email'])?$this->request['cust_email']:'',
        'address_1'     => !empty($this->request['cust_address1'])?$this->request['cust_address1']:'',
        'address_2'     => !empty($this->request['cust_address2'])?$this->request['cust_address2']:'',
        'city'          => !empty($this->request['cust_city'])?$this->request['cust_city']:'',
        'postcode'      => !empty($this->request['cust_pin'])?$this->request['cust_pin']:'',
        'phone'         => !empty($this->request['cust_telephone'])?$this->request['cust_telephone']:'',
        'zone_id'       => !empty($this->request['cust_zone_id'])?$this->request['cust_zone_id']:'',
        'account_code'  => !empty($this->request['account_code'])?$this->request['account_code']:'',
      );

        $sql  = "INSERT INTO ".DB_PREFIX."return_shipment_tracking 
                SET 
                  master_return_ids   = '" .$this->_db->escape($this->courierDataToSaveInDB['master_return_id']). "',
                  courier_company     = '". $this->_db->escape($this->courierDataToSaveInDB['courier_company'])."',
                  tracking_no         = '". $this->_db->escape($this->courierDataToSaveInDB['aws_docket'])."',
                  date_added          = Now(),
                  order_id            = '". $this->_db->escape($this->courierDataToSaveInDB['order_id'])."',
                  order_no            = '". $this->_db->escape($this->courierDataToSaveInDB['order_no'])."',
                  weight              = '". $this->_db->escape($this->courierDataToSaveInDB['weight'])."',
                  value               = '". (float)$this->courierDataToSaveInDB['total_amount']."',
                  status              = '". $this->_db->escape($this->courierDataToSaveInDB['status'])."',
                  equivalent_status   = '". $this->_db->escape($this->courierDataToSaveInDB['status'])."',
                  package_description = '". $this->_db->escape($this->courierDataToSaveInDB['package_desc'])."',
                  qty                 = '". (int)$this->courierDataToSaveInDB['qty']."',
                  vendor_code         = '". $this->_db->escape($this->courierDataToSaveInDB['vendor_code'])."',
                  warehouse_id        = '". $this->_db->escape($this->courierDataToSaveInDB['warehouse_id'])."',
                  request_param       = '". $this->_db->escape($this->courierDataToSaveInDB['req_res'])."',
                  shipping_details    = '". $this->_db->escape(serialize($shipping_details))."',
                  token_no            = '". $this->_db->escape($token_no)."',
                  return_reason       = '". $this->_db->escape($this->courierDataToSaveInDB['return_reason'])."',
                  remarks             = '". $this->_db->escape($this->courierDataToSaveInDB['remarks'])."'
          ";
      $this->_db->query($sql);
      return $this->_db->getLastId();
    }
  }

  /**
  * saveReverseShipmentTrackingData() - method to save return shipment tracking data
  * @author  MSA, <Feb. 18>
  */ 
  public function saveForwardShipmentTrackingData()
  {
    if(!empty($this->courierDataToSaveInDB))
    {
      $shipping_details = array(
        'name'          => !empty($this->request['cust_name'])?$this->request['cust_name']:'',
        'email'         => !empty($this->request['cust_email'])?$this->request['cust_email']:'',
        'address_1'     => !empty($this->request['cust_address1'])?$this->request['cust_address1']:'',
        'address_2'     => !empty($this->request['cust_address2'])?$this->request['cust_address2']:'',
        'city'          => !empty($this->request['cust_city'])?$this->request['cust_city']:'',
        'postcode'      => !empty($this->request['cust_pin'])?$this->request['cust_pin']:'',
        'phone'         => !empty($this->request['cust_telephone'])?$this->request['cust_telephone']:'',
        'zone_id'       => !empty($this->request['cust_zone_id'])?$this->request['cust_zone_id']:'',
        'account_code'  => !empty($this->request['account_code'])?$this->request['account_code']:'',
      );

      $file_name = !empty($this->courierDataToSaveInDB['file_name'])?$this->courierDataToSaveInDB['file_name']:'';

        $sql  = "INSERT INTO ".DB_PREFIX."return_shipment_backto_customer 
                SET 
                  return_ids          = '". $this->_db->escape($this->courierDataToSaveInDB['master_return_id'])."',
                  courier_company     = '". $this->_db->escape($this->courierDataToSaveInDB['courier_company'])."',
                  tracking_no         = '". $this->_db->escape($this->courierDataToSaveInDB['aws_docket'])."',
                  date_added          = Now(),
                  order_id            = '". $this->_db->escape($this->courierDataToSaveInDB['order_id'])."',
                  order_no            = '". $this->_db->escape($this->courierDataToSaveInDB['order_no'])."',
                  weight              = '". $this->_db->escape($this->courierDataToSaveInDB['weight'])."',
                  value               = '". (float)$this->courierDataToSaveInDB['total_amount']."',
                  status              = '". $this->_db->escape($this->courierDataToSaveInDB['status'])."',
                  equivalent_status   = '". $this->_db->escape($this->courierDataToSaveInDB['status'])."',
                  package_description = '". $this->_db->escape($this->courierDataToSaveInDB['package_desc'])."',
                  qty                 = '". (int)$this->courierDataToSaveInDB['qty']."',
                  vendor_code         = '". $this->_db->escape($this->courierDataToSaveInDB['vendor_code'])."',
                  warehouse_id        = '". $this->_db->escape($this->courierDataToSaveInDB['warehouse_id'])."',
                  request_param       = '". $this->_db->escape($this->courierDataToSaveInDB['req_res'])."',
                  shipping_details    = '". $this->_db->escape(serialize($shipping_details))."',
                  file_name           = '". $this->_db->escape($file_name)."',
                  return_reason       = '". $this->_db->escape($this->courierDataToSaveInDB['return_reason'])."',
                  remarks             = '". $this->_db->escape($this->courierDataToSaveInDB['remarks'])."'
          ";
      $this->_db->query($sql);
      return $this->_db->getLastId();
    }
  }

  /**
  * updateReverseShipmentMasterReturnData() - method to update master return table data
  * @author  MSA, <Feb. 18>
  */
  public function updateReverseShipmentMasterReturnData(int $return_shipment_id)
  {
    if(!empty($this->courierDataToSaveInDB) && $return_shipment_id)
    {
      $sql  = "UPDATE " . DB_PREFIX . "master_return  
                  SET 
                        return_shipment_tracking_id = '". (int)$return_shipment_id."'
                  WHERE 
                        master_return_id IN (". $this->courierDataToSaveInDB['master_return_id'] . ")
                  ";
      $this->_db->query($sql);
    }
  }



  /**
  * updateReverseShipmentReturnData() - method to update return table data
  * @author  MSA, <Feb. 18>
  */
  public function updateReverseShipmentReturnData(int $return_shipment_tracking_id, $action_id='',$isForwardShipment=false, $isManuallyShipment=false)
  {
    if(empty($action_id)) {
      $action_id = RETURN_ACTION_IDS['Reverse_Shipment_Generated'];
    }
    $returnInfoObj = new ReturnInfo($this->registry);
    $returns = $returnInfoObj->getReturnById($this->request['return_ids']);

    $obj = new ReturnActionBase($this->registry);
  
    if(!empty($returns)) {
      $returns_data = array();
      foreach($returns as $key => $value) {
       
        $data = array(
                'master_return_id'    => $value['master_return_id'],
                'order_product_id'    => $value['order_product_id'],
                'quantity'            => $value['quantity'],
                'return_reason_id'    => $value['return_reason_id'],
                'comment'             => $value['comment'],
                'return_action_id'    => $action_id,
                'return_action_reason_id' => $value['return_action_reason_id'],
                'shipping_method'     => 'wsb_pickup',
                'date_added'          => $value['date_added'],
                'user'                => $value['user'],
                'user_id'             => $value['user_id'],
                'customer_id'         => $value['customer_id'],
                'crm_user_id'         => $value['crm_user_id'],
                'debit_note_id'       => $value['debit_note_id'],
                'credit_note_id'      => $value['credit_note_id'],
                'active_row'          => $value['active_row'],
                'return_shipment_tracking_id'        => $value['return_shipment_tracking_id'],
                'return_shipment_backto_customer_id' => $value['return_shipment_backto_customer_id']
        );
        if($isForwardShipment) {
          $data['return_shipment_backto_customer_id'] = $return_shipment_tracking_id;
          $data['internal_note']  = "Shipment generated to send item(s) back to cutomer for Return Id# ".$value['return_id'];
        }else{
          $data['return_shipment_tracking_id'] = $return_shipment_tracking_id;
          $data['internal_note'] = "Reverse Shipment generated to get back item(s) from cutomer for Return Id# ".$value['return_id'];
        }
        if($isManuallyShipment) {
          $data['internal_note'] = "Customer requested for manually reverse shipment to get back item(s) from cutomer for Return Id# ".$value['return_id'];
        }
        //$obj->insertReturn($data);
        $returns_data[] = $data;
      }
      if(!empty($returns_data)) {
        $obj->insertReturnMultiple($returns_data);  
      }
      $obj->resetReturnActiveStatusForReturnIds(0,$this->request['return_ids']);
    }
  }

  public function validate_response()
   {
      if(empty($this->response))
      { 
         $this->error['error'] = $this->courier_name . ' API server not responding, inform to '.$this->courier_name.' contact/support person about this issue.';
         return false;
      }
      return true;
   }


  /**
  * sendReverseShipmentSMSAlertMessage() - method to send SMS alert message 
  * @author  MSA, <Feb. 18>
  */

  public function sendReverseShipmentEmailAlertMessage($this_obj)
  {
    $mail_data = array();
    $mail_data['order_no']      = isset($this->request['order_no'])?$this->request['order_no']:'';
    $mail_data['order_id']      = isset($this->request['order_id'])?$this->request['order_id']:'';
    $mail_data['customer_id']   = isset($this->request['customer_id'])?$this->request['customer_id']:'';
    $mail_data['courier_partner']  = $this->courier_name;
    $pickup_address = '<p><b>';
    if(!empty($this->request['cust_name'])) {
      $pickup_address .= ucfirst($this->request['cust_name']). '<br>';
    }
    if(!empty($this->request['cust_address1'])){
      $pickup_address .= $this->request['cust_address1']. '<br>';
    }  
    if(!empty($this->request['cust_address2'])){
      $pickup_address .= $this->request['cust_address2']. '<br>';
    }
    if(!empty($this->request['cust_city'])){
      $pickup_address .= $this->request['cust_city']. '<br>';
    }
    if(!empty($this->request['cust_pin'])) {
      $pickup_address .= $this->request['cust_pin'];
    } 
    $pickup_address .='</b></p>';

    $return_products  = $this->courierDataToSaveInDB['return_products'];
    $op_ids      = array_column($return_products, 'order_product_id');
    $product_images   = array();
    if(!empty($op_ids)) {
      MsProduct::setProductImages($this_obj, $op_ids, $product_images);
    }
    if(!empty($return_products)) 
    {
      $sno=0;
      foreach ($return_products as $key => $value) {
        $op_id = $value['order_product_id'];
       $mail_data['returns'][$sno++]= array(
                      'return_id'         => $value['return_id'],
                      'master_return_id'  => $value['master_return_id'],
                      'helpdesk_ticket_id'=> $value['helpdesk_ticket_id'],
                      'image'             => (array_key_exists($op_id, $product_images['pid_to_imgs'])) ? $product_images['pid_to_imgs'][$op_id] : '',
                      'model'             => $value['model'],
                      'return_reason'     => $value['reason_name'],
                      'return_quantity'   => $value['quantity'],
                      'return_type'       => $value['reason_type'],
                      'payment_method_id' => $value['payment_method'],
                      'comment'           => $value['comment']
                    ) ;
      }

        $mail_data['customer']      = array(
                                    'id'      => isset($this->request['customer_id'])?$this->request['customer_id']:'',
                                    'name'    => isset($this->request['cust_name'])?$this->request['cust_name']:'',
                                    'email'   => isset($this->request['cust_email'])?$this->request['cust_email']:'',
                                    'phone'   => isset($this->request['cust_telephone'])?$this->request['cust_telephone']:''
                                );
        $mail_data['pickup_address'] = $pickup_address; 
        if(!empty($mail_data)) {
          $mailer = new ActionReverseShipmentGenerated($this->registry);
          // send email to customer while auto generating shipment back to customer
          $mailer->sendEmailReverseShipment($mail_data);
          // send push notification and SMS to customer while auto generating shipment back to customer
          $mailer->sendPNForReverseShipment($mail_data);
        }
    }
  }


/**
    * isCod() - method to check for COD order 
    * @return  boolean true/false 
    * @author  MSA, <25 Dec. 17>
    */
   public function isReverseShipmentPaymentMethodCod()
   {
        if( $this->getReverseShipmentPaymentMethod() == 'cod' ) {
            return true;
        }
      return false;
   }

  /**
    * getTotalAmount() - method to get order total amount from order details
    * @return  float total amount
    * @author  MSA, <Feb. 18>
    */
    public function getReverseShipmentTotalAmount()
    {
        if(!empty($this->courierDataToSaveInDB['return_products'])){
          $order_products = $this->courierDataToSaveInDB['return_products'];
        }else{
          $order_products = $this->getReturnProducts();
        }
        
        $total_price = 0;
        $total_tax   = 0;
        if(!empty($order_products))
        {
            foreach ($order_products as $op_data)
            {
              $total_price += (float)$op_data['p_price'] * (int)$op_data['quantity'];
              $total_tax   += (float)$op_data['p_tax'] * (int)$op_data['quantity'];
            }
        } 
        return  ceil(($total_price+$total_tax));
    }

    public function getReverseShipmentCollectableAmount()
    {
        return 0;
    }
    /**
    * getPaymentMethod() - method to get order payment method 
    * @return float  net payable amount 
    * @author  MSA, <25 Dec. 17>
    */
   public function getReverseShipmentPaymentMethod()
   {
       if( isset($this->orderInfo['order']['payment_code']) && strtolower(trim($this->orderInfo['order']['payment_code'])) != "cod"){
            return 'prepaid';
        } else if(isset($this->orderInfo['order']['payment_code']) && strtolower(trim($this->orderInfo['order']['payment_code'])) == "cod" && $this->getNetPayable() > 0) { 
            return 'cod';
        }else{
            return 'prepaid';
        }
   }

  /**
  * getReturnProducts() - method to get order products list from order details
  * @return Array order product list
  * @author  MSA, <Feb. 18>
  */
  public function getReturnProducts()
    {
        $resp = array();
        if(!empty($this->request['return_ids'])){
            $sql = "SELECT 
                        ocr.return_id,
                        ocr.master_return_id,
                        omr.helpdesk_ticket_id,
                        ocr.comment,
                        ocr.shipping_method,
                        ocr.payment_method,
                        oop.order_product_id,
                        oop.product_id,
                        oop.seller_sku,
                        ((oop.price_per_piece + oop.discount_per_piece) * oop.output_tax_rates) / 100 as p_tax,
                        ocr.quantity,
                        oop.name as p_name,
                        oop.hsn_code,
                        oop.price_per_piece,
                        oop.piece_in_set,
                        oop.discount_per_piece,
                        (oop.price_per_piece + oop.discount_per_piece) as p_price,
                        oop.model,
                        orr.name as reason_name,
                        orr.reason_type,
                        so.invoice_no
                    FROM
                        ".DB_PREFIX."return as ocr
                            INNER JOIN
                        ".DB_PREFIX."master_return as omr ON omr.master_return_id = ocr.master_return_id
                            INNER JOIN
                        ".DB_PREFIX."return_reason as orr ON orr.return_reason_id = ocr.return_reason_id AND orr.language_id = 1
                            INNER JOIN
                        ".DB_PREFIX."order_product as oop ON oop.order_product_id = ocr.order_product_id
                            INNER JOIN
                        ".DB_PREFIX."suborder as so ON so.order_id = oop.order_id
                    WHERE
                        ocr.return_id IN (".$this->request['return_ids'].") 
                        AND so.suborder_id = oop.suborder_id
                        AND active_row = 1
                  ";
            $result = $this->_db->query($sql);
            if($result->num_rows > 0){
                $resp = $result->rows;          
            }
        }
        return $resp;
    }

    public function getInvoiceNumberInReverseShipment()
    { 
      $invoice_number = '';
      $invoice_date   = '';
      $order_product_count= 0;

      if(isset($this->orderInfo['suborder']))
      {
          foreach($this->orderInfo['suborder'] as $key => $value)
          { 
              if(isset($value['order_product']) && count($value['order_product']) > $order_product_count )
              {
                  $invoice_number = $value['invoice_no']; 
                  $order_product_count= count($value['order_product']); 
              }
          }
      }
      return $invoice_number;
    }

    /**
    * validateTrackingData() - method to validate docket tracking request data
    * @author  MSA, <March. 18>
    */
    protected function validateTrackingData()
    {
        if(empty($this->request['tracking_no'])){
            $this->error['error'] = 'Tracking number not found!!';
        }else{
            $this->docket = $this->request['tracking_no'];
        }
    }



  /**
  * sendReverseShipmentSMSAlertMessage() - method to send SMS alert message 
  * @author  MSA, <Feb. 18>
  */
  public function sendForwardShipmentEmailAlertMessage($this_obj)
  {
    $mail_data = array();
    $mail_data['order_no']      = isset($this->request['order_no'])?$this->request['order_no']:'';
    $mail_data['order_id']      = isset($this->request['order_id'])?$this->request['order_id']:'';
    $mail_data['customer_id']   = isset($this->request['customer_id'])?$this->request['customer_id']:'';

    $return_products  = $this->courierDataToSaveInDB['return_products'];
    $op_ids           = array_column($return_products, 'order_product_id');
    $product_images   = array();
    if(!empty($op_ids)) {
      MsProduct::setProductImages($this_obj, $op_ids, $product_images);
    }
   
    if(!empty($return_products)) {
      $sno=0;
      foreach ($return_products as $key => $value) {
        $op_id = $value['order_product_id'];
        $mail_data['returns'][$sno++]= array(
                      'return_id'         => $value['return_id'],
                      'master_return_id'  => $value['master_return_id'],
                      'helpdesk_ticket_id'=> $value['helpdesk_ticket_id'],
                      'image'             => (array_key_exists($op_id, $product_images['pid_to_imgs'])) ? $product_images['pid_to_imgs'][$op_id] : '',
                      'model'             => $value['model'],
                      'return_reason'     => $value['reason_name'],
                      'return_quantity'   => $value['quantity'],
                      'return_type'       => $value['reason_type'],
                      'payment_method_id' => $value['payment_method'],
                      'comment'           => $value['comment']
                    ) ;
      }

      $mail_data['customer']  = array(
                                    'id'      => isset($this->request['customer_id'])?$this->request['customer_id']:'',
                                    'name'    => isset($this->request['cust_name'])?$this->request['cust_name']:'',
                                    'email'   => isset($this->request['cust_email'])?$this->request['cust_email']:'',
                                    'phone'   => isset($this->request['cust_telephone'])?$this->request['cust_telephone']:''
                                );

    }

    

      if(!empty($mail_data)) {

        $mailer = new ActionGoodsBackToCustomer($this->registry);
        // send email to customer while auto generating shipment back to customer
        $mailer->sendEmailBackToCustomer($mail_data);
        // send push notification and SMS to customer while auto generating shipment back to customer
        $mailer->sendPNBackToCustomer($mail_data);
      }

    }

  /**
  * getStateCodeByZoneId() - method to get state code by zone id
  * @param integer $zone_id
  * @author  MSA, <Feb. 18>
  */
    public function getStateCodeByZoneId($zone_id)
    {
      $code = '';
      $sql = "SELECT code FROM ".DB_PREFIX."zone WHERE zone_id = '".(int) $zone_id ."' " ;
      $result = $this->_db->query($sql);
      if($result->num_rows){
        $code = $result->row['code'];
      }
      return $code;
    }

    /**
    * Public function - addManuallyReverseShipment(), to process return penal - manually generated reverse shipment action data
    * @param Void
    * @author MSA June 18
    */
    public function addManuallyReverseShipment()
    {   
        $status = array();
        $return_shipment_id = 0;
        $docket             = implode(',',array_unique(array_column($this->request, 'docket')));
        $master_return_id   = implode(',',array_unique(array_column($this->request, 'master_return_id')));
        $return_id          = implode(',',array_unique(array_column($this->request, 'return_id')));
        $warehouse_id       = implode(',',array_unique(array_column($this->request, 'warehouse_id')));

            $this->request['return_ids'] = $return_id;
            $this->courierDataToSaveInDB['aws_docket']  = $docket;
            $this->courierDataToSaveInDB['status']      = 'Manually';
            $this->courierDataToSaveInDB['req']         = '';
            $this->courierDataToSaveInDB['res']         = '';
            $this->courierDataToSaveInDB['req_res']     = '';
            $this->courierDataToSaveInDB['master_return_id'] = $master_return_id;
            $this->courierDataToSaveInDB['total_amount']     = $this->getReverseShipmentTotalAmount();
            $this->courierDataToSaveInDB['courier_company']  = $this->courier_name;
            $this->courierDataToSaveInDB['order_id']         = $return_id;
            $this->courierDataToSaveInDB['order_no']         = $this->orderInfo['order']['order_no'];
            $this->courierDataToSaveInDB['customer_id']      = $this->orderInfo['order']['customer_id'];
            $this->courierDataToSaveInDB['qty']              = $this->getReturnQtyById($return_id);
            $this->courierDataToSaveInDB['req']              = '';
            $this->courierDataToSaveInDB['remarks']          = '';
            $this->courierDataToSaveInDB['return_reason']    = '';
            $this->courierDataToSaveInDB['weight']           = $this->getTotalWeightForReturns($return_id);

            $this->courierDataToSaveInDB['vendor_code']      = $this->warehouseInfo['bluedart_vendor_code'];
            $this->courierDataToSaveInDB['warehouse_id']     = $warehouse_id;
            $this->courierDataToSaveInDB['package_desc']     = '';
            $this->courierDataToSaveInDB['cust_telephone']   = $this->orderInfo['order']['telephone'];
            $this->courierDataToSaveInDB['cust_name']        = $this->orderInfo['order']['shipping_firstname'];
            $this->courierDataToSaveInDB['cust_address1']    = $this->orderInfo['order']['shipping_address_1'];
            $this->courierDataToSaveInDB['cust_address2']    = $this->orderInfo['order']['shipping_address_2'];
            $this->courierDataToSaveInDB['cust_city']        = $this->orderInfo['order']['shipping_city'];
            $this->courierDataToSaveInDB['cust_pin']         = $this->orderInfo['order']['shipping_postcode'];
            
            $this->courierDataToSaveInDB['return_products'] = $this->getReturnProducts();

            $return_shipment_id = $this->saveReverseShipmentTrackingData();

            if(!empty($return_shipment_id))
            {
                $this->updateReverseShipmentMasterReturnData($return_shipment_id);

                $this->updateReverseShipmentReturnData($return_shipment_id, 
                                                        RETURN_ACTION_IDS['Manually_Generated_Reverse_Shipment'],
                                                        false,
                                                        true
                                                    );
            } 
    }

  /**
  * getReturnQtyById() - method to get return quantity by return ids
  * @param string comma separated return ids
  * @author  MSA, June 18
  */
  public function getReturnQtyById($return_ids=''){

      $total_qty = 0;

      if(empty($return_ids)){ return $total_qty; }
       
        $sql = "SELECT 
                  GROUP_CONCAT(CONCAT(r.return_id, ':' ,r.order_product_id, ':', r.quantity)) as return_combo,
                  r.return_reason_id, 
                  r.return_id,
                  SUM(r.quantity) as total_return_qty
              FROM  
                 " . DB_PREFIX . "return as r
              INNER JOIN 
                 " . DB_PREFIX . "order_product as oop ON oop.order_product_id = r.order_product_id  
              WHERE  
                r.return_id  IN (".$return_ids.")  
                AND r.active_row = 1
              GROUP BY  
                r.order_product_id
            ";
        $result = $this->_db->query($sql);
        $return_ids = array();
        if ($result->num_rows > 0) {
            foreach ($result->rows as $key => $value) {
                $total_qty += $value['total_return_qty'];
            }
        }
        return $total_qty;
    }

  /**
  * getTotalWeightForReturns() - method to get total weight for return items
  * @param string comma separated return ids
  * @author  MSA, June 18
  */
    public function getTotalWeightForReturns($return_ids=''){
      
      $return_weight = 0;

      if(empty($return_ids)){ return $return_weight; }

      $sql  = "SELECT 
          sum(case
                WHEN oop.weight_per_piece >= 0.01 THEN oop.weight_per_piece * ocr.quantity
                WHEN op.weight >= 0.01 THEN op.weight * ocr.quantity
                ELSE 0.25 * ocr.quantity
            END) AS total_weight, oop.suborder_id
        FROM ".DB_PREFIX."order_product oop
          INNER JOIN ".DB_PREFIX."product as op ON op.product_id = oop.product_id
          INNER JOIN ".DB_PREFIX."return as ocr ON ocr.order_product_id = oop.order_product_id
        WHERE 
            ocr.return_id IN (". $return_ids .")
            AND ocr.active_row = 1
        GROUP BY oop.suborder_id
        ";
    $result = $this->_db->query($sql);
    
    if($result->num_rows > 0){
      foreach ($result->rows as $row) {
        $return_weight += $row['total_weight'];
      }
    }
    return $return_weight;
  }

  /**
  * getReverseShipmentByShippingId() - method to get shipment tracking details by shipping id
  * @param integer shipping id
  * @author  MSA, June 18
  */
  public function getReverseShipmentByShippingId($shipping_id)
  {
      $data = array();
      if(!empty($shipping_id)) {

            $sql  = "
                    SELECT 
                      rst.master_return_ids,
                      rst.tracking_no,
                      r.*
                    FROM 
                        ".DB_PREFIX."return_shipment_tracking as rst 
                    INNER JOIN 
                      ".DB_PREFIX."return as r on r.return_shipment_tracking_id=rst.shipping_id AND active_row = 1
                    WHERE 
                      shipping_id = '". (int)$shipping_id ."'
                ";
          
          $result = $this->_db->query($sql);
          
          if($result->num_rows > 0){
            $data = $result->rows;
          }
      }

      return $data;
  }

  /**
  * getForwardShipmentByShippingId() - method to get shipment tracking details by shipping id
  * @param integer shipping id
  * @author  MSA, June 18
  */
  public function getForwardShipmentByShippingId($shipping_id)
  {
      if(!empty($shipping_id)) {

            $sql  = "SELECT rst.return_ids,
                            rst.tracking_no,
                            r.*
                        FROM ".DB_PREFIX."return_shipment_backto_customer as rst 
                        INNER JOIN ".DB_PREFIX."return as r on r.return_shipment_backto_customer_id=rst.shipping_id
                        WHERE 
                          shipping_id = '". (int)$shipping_id ."'
                          ";
          $result = $this->_db->query($sql);
          
          if($result->num_rows > 0){
            return $result->row;
          }
      }
  }

  /**
  * cancelReverseShipment() - Method to cancel reverse shipment
  * @param: $this- registery object
  * @return: Response with error msg or Success Flag
  * @author  MSA, <March. 18>
  */
  public function cancelReverseShipment($this_obj) {
        
    $return_data = array();
    
    $shipping_id = $this->request['shipping_id'];

    if(empty($shipping_id)) {

        $this->response['error'] = 'Shipping id not found!!';
    }else{
      //Get shipment details and return details accordingly
      $shipment_details = $this->getReverseShipmentByShippingId($shipping_id);

      if(!empty($shipment_details)) {
          
          $obj = new ReturnActionBase($this_obj);

          //Cancel shipment APIs for future use
          $this->cancelRequest($details);

          $all_old_return_ids = array_column($shipment_details, 'return_id');
          $return_ids = implode(',', $all_old_return_ids);

          if($this->response['success']){
              foreach ($shipment_details as $details) {

                  $return_id          = $details['return_id'];
                  $tracking_no        = $details['tracking_no'];
                  $master_return_ids  = $details['master_return_ids'];    
                  
                  if($this->response['success'])
                  {
                    $return_data = $details;
                      
                      //Clear Old internal note form return datils
                    $return_data['internal_note'] = "Cancel Reverse Shipment For ID#".$return_data['return_shipment_tracking_id'].", ReturnId#".$return_data['return_id'];

                    $return_data['return_action_id'] = RETURN_ACTION_IDS['Action_Cancel_Reverse_Shipment'];

                    $return_data['return_shipment_tracking_id'] = 0;

                    //Entery to oc_return with updated return action
                    $obj->insertReturn($return_data);
                  }  
              }

              // update master return table for cancel status - set shipment tracking id with NULL value
              $obj->setShipmentCancelStatusInMasterReturn($master_return_ids);

              //Update Old Return with active_row as 0
              $obj->resetReturnActiveStatusForReturnIds(0, $return_ids);

              // update is_cancel status [1] for shipment tracking record
              $obj->setReverseShipmentForCancelStatus($shipping_id, 1);
              
          }
        }else{ //If shippping details are missing
            $this->response['error'] = 'Shipping details not found!!';
        }
    }

    return $this->response; //Return response for Cancel Shipment
  }

  /**
  * cancelRequest() - Method to call courier company API 
  *                      to cancel reverse shipment request
  * @author  MSA, <March. 18>
  */
  protected function cancelRequest($shipment_details)
  {
      /* call cancel API*/
      $this->response['success'] = 1;
  }

  /**
    * @info: Public static method to get courier company website tracking page url
    * for given docket number
    * @param: $courier_name, courier company name
    * @param: $docket,  docket number
    * @author: MSA, August 2018
  */
  public function getCourierTrackingURL($db, $courier_name = '', $docket = ''){

    $tracking_url = false; //Default value, and false means to no tracking URL is exist

    if(!empty($courier_name) && !empty($docket)) {
      //CourierName and Docket number must be passed to method
        $sql = "
                SELECT
                  tracking_url
                FROM  
                  " . DB_PREFIX . "courier_partners
                WHERE
                  courier_name = '".$db->escape($courier_name)."'
                  AND TRIM(tracking_url) IS NOT NULL
                  AND TRIM(tracking_url) != ''
              ";
        //Query Execution
        $result = $db->query($sql);
        
        if($result->num_rows) {//If data get from Database
          $tracking_url = $result->row['tracking_url'] . $docket;
        } 
    }
    
    return $tracking_url;
  }

  /**
   * Public method to get all courier partners
   * @return: $data Array
   * @author: Nishu, Aug 2018
  */
  public static function getAllActiveCourierPartners($db){
    $data = array();

    $sql = "
            SELECT 
              id, courier_name
            FROM 
              " . DB_PREFIX . "courier_partners
            WHERE
              status = 1
              AND is_forward_shipment = 1
           ";

    $result = $db->query($sql);

    $data = $result->rows; 

    //set Id as array's key
    $data = array_combine(array_column($data, 'id'), $data);

    return $data;
  }

  /**
  * Method to get help desk ticket id for master return id
  * @param: integer master return ids
  * @author  MSA, August 2018
  */
  public function getHelpDeskTicketForMasterReturnId($master_return_ids)
  {
    $helpdesk_ticket_ids = array();
     if(!empty($master_return_id)) {
      $sql = "
              SELECT 
                  `master_return_id`,
                  `helpdesk_ticket_id`
              FROM 
                  ".DB_PREFIX."master_return
              WHERE
                  master_return_id IN (".$this->_db->escape($master_return_ids).")    
              GROUP BY helpdesk_ticket_id;
            ";
      $result = $this->_db->query($sql);
      if($result->num_rows) {
        $helpdesk_ticket_ids =  $result->rows;
      }
      return $helpdesk_ticket_ids;
     }
  }

  /**
  * Method to validate customer email address to generate shipment
  *  if found invalid customer email then return default email address for shipment
  * @param: string email
  * @author  MSA, Oct 2018
  */
  public function validate_email( ?string $email ): string
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = DEFAULT_SHIPMENT_EMAIL;
    } 
    return $email;
  }



} /* end of class */
