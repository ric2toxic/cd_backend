<?php
class LogisticsAdvisor{
    private $_order_info = array();
    private $_logics = array(
      'gati'            => 'checkForGati',
      'bluedart_apex'   => 'checkForBluedartApex',
      'bluedart_surface'=> 'checkForBluedartSurface',
      'dotzot_express'  => 'checkForDotZotExpress',
      'dotzot_economy'  => 'checkForDotZotEconomy',
      'fedex'           => 'checkForFedex',
      'delhivery'       => 'checkForDelhivery',
      'truxcargo'       => 'checkForTruxCargo'
    );
    private $_db;
    private $_registry;
    public function __construct(&$order_info,$registry){
      $this->_order_info = $order_info;
      $this->_registry = $registry;
      $this->_db = $registry->db;
    }
    /**
     * [getAdvise This function call the function on each logic and return an array of all logic's outputs.
     * @return [array] [{gati,fedex,bluedart}]
     * @usage $_logics
     */
    public function getAdvise(){
      $logic_output = array();
      foreach ($this->_logics as $key => $logic){
        if(method_exists($this,$logic)){
            $logic_output[$key] = $this->$logic();
        }
      }
      uasort($logic_output, array('LogisticsAdvisor','sortCouriers'));
      if(!empty($this->_order_info['zone_id'])){
        $logic_output['paperwork'] = $this->getWaybillReport($this->_order_info['zone_id']);
      }
      return $logic_output;
    }
    /**
     * [getAdvise This function call to sort courier data by is_serviceable flag .
     * @return [array] 
     * @usage $_logics
     */
    private static function sortCouriers($a, $b)
    {  
        if ($a['is_serviceable'] == $b['is_serviceable']) return 0;
        elseif ($a['is_serviceable'] < $b['is_serviceable']) return 1;
        else return -1;
    }
    /**
     * [checkForGati - This function provide the shipment advice for gati such as serviceability, additional notes and expected time. ]
     * @return [array]  [Returns an array {service_available,serviceability,additional_notes}]
     * @usage - LogisticsAdvisor::getShipmentDetails
     */
    public function checkForGati(){
      extract($this->_order_info);
      $advice = array('courier'=>'Gati');
      $pincode_data = $this->getShipmentDetails($pincode,'gati');
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['service_available'] = false;
        $advice['serviceability_info'] = "There is no service available to the pincode $pincode";
        $advice['additional_notes'] = "You should try an another shipment option.";
      }
      switch($pincode_data['serviceability']){
        case 'direct':
            $advice['is_serviceable']   = 1;
            $advice['serviceability']   = ucfirst($pincode_data['serviceability']);
            $advice['cod']              = $pincode_data['cod'];
            $advice['prepaid']          = $pincode_data['prepaid'];
            $advice['distance']         = $pincode_data['distance'];
            $advice['location']         = $pincode_data['ou_name'];
          $advice['service_available']  = true;
          $advice['serviceability_info']= $pincode_data['serviceability'] ." service is available in this area.";
          $advice['additional_notes']   = "No additional charges Applicable.";
        break;
        case 'ess':
            $advice['is_serviceable']   = 1;
            $advice['serviceability']   = ucfirst($pincode_data['serviceability']);
            $advice['cod']              = $pincode_data['cod'];
            $advice['prepaid']          = $pincode_data['prepaid'];
            $advice['distance']         = $pincode_data['distance'];
            $advice['location']         = $pincode_data['ou_name'];  
          $advice['service_available']  = true;
          $advice['serviceability_info']= $pincode_data['serviceability'] ." service is available in this area.";
          if($pincode_data['distance'] <= 25){
            $advice['additional_notes'] = "Ess Pincode is having ".$pincode_data['distance']."km distance. So No additional charges Applicable.";
          }
          else{
            $parcel_weight =  !empty($suborder['weight']) ? $suborder['weight'] : (!empty($weight) ? $weight : null);
            if(!empty($parcel_weight)){
              $advice['additional_notes'] = "";
              $sql  = "SELECT `max_weight`,";
              $sql .=        "`max_km`,";
              $sql .=        "`charges`,";
              $sql .=        "`ext_transit` ";
              $sql .= "FROM `".DB_PREFIX."gati_ess_loc_rate` ";
              $sql .= "WHERE `max_weight`>= ".(float)$parcel_weight." AND ";
              $sql .=       "`max_km` >= ".(int)$pincode_data['distance']." ";
              $sql .=       " order by `max_km` ASC ";
              $sql .=       " limit 1";
              $result = $this->_db->query($sql);
            }
            if(!empty($result)){
              $ess = $result->row;
              $advice['additional_notes'] = "You shipment ess charges for ".$parcel_weight."kg and ".$pincode_data['distance']."km is ".$ess['charges']." and it takes ".$ess['ext_transit']." to be shipped.";
            }
            else{
              $advice['additional_notes'] = "We are unable to find the charges for ".$parcel_weight."kg and ".$pincode_data['distance']."km.";
            }
          }
        break;

        default:
          //throw new Exception("There is no serviceability in db of the given pincode");
        break;
      }
      $advice['order'] = 1;
      return $advice;
    }
    /**
     * [checkForDotZotExpress - This function provide the shipment advice for dotzot express such as serviceability, additional notes and expected time. ]
     * @return [array]  [Returns an array {service_available,serviceability,additional_notes}]
     * @usage - LogisticsAdvisor::getShipmentDetails
     */
    public function checkForDotZotExpress(){
      extract($this->_order_info);
      $advice = array('courier'=>'Dotzot Express');
      $pincode_data = $this->getShipmentDetails($pincode,'dotzot_express');
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['reverse_pickup']   = 0;
        $advice['pickup']           = 0;
        $advice['service_available'] = false;
        $advice['additional_notes'] = "You should try an another shipment option.";
      } else{
        $advice['is_serviceable']   = 1;
        $advice['serviceability']   = 'Available';
        $advice['cod']              = $pincode_data['cod'];
        $advice['prepaid']          = $pincode_data['prepaid'];
        $advice['distance']         = 0; // no distance information for pincode in table
        $advice['location']         = $pincode_data['city'];
        $advice['reverse_pickup']   = $pincode_data['reverse_pickup'];
        $advice['pickup']           = $pincode_data['pickup'];
        $advice['service_available'] = true;
        if(!$pincode_data['prepaid'] && !$pincode_data['cod'] && !$pincode_data['pickup'] && !$pincode_data['reverse_pickup'])
        {
            $advice['serviceability_info'] = "No service is available in this area.";
        } else{
            $advice['serviceability_info'] = "Service is available in this area.";
        }
        $prepaid = ($pincode_data['prepaid'])?'Available':'Not available';
        $advice['additional_notes'] = 'Prepaid - ' . $prepaid . '<br />';  
        $cod = ($pincode_data['cod'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Cod - ' . $cod . '<br />';
        $pickup = ($pincode_data['pickup'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Pickup - ' . $pickup . '<br />';  
        $reverse_pickup = ($pincode_data['reverse_pickup'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Reverse pickup - ' . $reverse_pickup . '<br />';  
      }
      $advice['order'] = 4;
      return $advice;
    }
    /**
     * [checkForDotZotEconomy - This function provide the shipment advice for dotzot economy such as serviceability, additional notes and expected time. ]
     * @return [array]  [Returns an array {service_available,serviceability,additional_notes}]
     * @usage - LogisticsAdvisor::getShipmentDetails
     */
    public function checkForDotZotEconomy(){
      extract($this->_order_info);
      $advice = array('courier'=>'Dotzot Economy');
      $pincode_data = $this->getShipmentDetails($pincode,'dotzot_economy');
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['reverse_pickup']   = 0;
        $advice['pickup']           = 0;
        $advice['service_available'] = false;
        $advice['additional_notes'] = "You should try an another shipment option.";
      } else{
        $advice['is_serviceable']   = 1;
        $advice['serviceability']   = 'Available';
        $advice['cod']              = $pincode_data['cod'];
        $advice['prepaid']          = $pincode_data['prepaid'];
        $advice['distance']         = 0; // no distance information for pincode in table
        $advice['location']         = $pincode_data['city'];
        $advice['reverse_pickup']   = $pincode_data['reverse_pickup'];
        $advice['pickup']           = $pincode_data['pickup'];
        $advice['service_available'] = true;
        if(!$pincode_data['prepaid'] && !$pincode_data['cod'] && !$pincode_data['pickup'] && !$pincode_data['reverse_pickup'])
        {
            $advice['serviceability_info'] = "No service is available in this area.";
        } else{
            $advice['serviceability_info'] = "Service is available in this area.";
        }
        $prepaid = ($pincode_data['prepaid'])?'Available':'Not available';
        $advice['additional_notes'] = 'Prepaid - ' . $prepaid . '<br />';  
        $cod = ($pincode_data['cod'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Cod - ' . $cod . '<br />';
        $pickup = ($pincode_data['pickup'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Pickup - ' . $pickup . '<br />';  
        $reverse_pickup = ($pincode_data['reverse_pickup'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Reverse pickup - ' . $reverse_pickup . '<br />';  
      }
      $advice['order'] = 4;
      return $advice;
    }
    
    
    public function checkForTruxCargo()
    {
        extract($this->_order_info);
        $advice = array('courier'=>'Trux Cargo');
        $pincode_data = $this->getShipmentDetails($pincode,'truxcargo');
        if(empty($pincode_data)){
            $advice['is_serviceable']   = 0;
            $advice['serviceability']   = 'Not available';
            $advice['cod']              = 0;
            $advice['prepaid']          = 0;
            $advice['distance']         = 0;
            $advice['location']         = 'Not available';
            $advice['service_available'] = false;
            $advice['serviceability_info'] = "There is no service available to the pincode $pincode";
            $advice['additional_notes'] = "You should try an another shipment option.";
        }else{
            $advice['is_serviceable']   = 1;
            $advice['serviceability']   = (strtolower($pincode_data['oda']) == 'oda') ? 'ODA' : 'Available';
            $advice['cod']              = 1;
            $advice['prepaid']          = 1;
            $advice['distance']         = 0; // no distance information for pincode in table
            $advice['location']         = $pincode_data['center'];
            $advice['service_available'] = true;
            $advice['serviceability_info'] = "Trux Cargo direct service is available to the pincode $pincode";
            $advice['additional_notes'] = "Available shippind method is " . $pincode_data['product_type'];
            $advice['additional_notes'] .= " and weight limit is " . $pincode_data['weight_limit'];
        }
        $advice['order'] = 2;
        return $advice;
    }
    /**
     * [checkForFedex This function provide the shipment advice for fedex such as serviceability, additional notes.]
     * @return [array] [{service_available,serviceability,additional_notes}]
     */
    public function checkForFedex(){
      extract($this->_order_info);
      $advice = array('courier'=>'Fedex');
      $payment_code = !empty($this->_order_info['payment_code']) ? strtolower(trim($this->_order_info['payment_code'])) : null;
      $pincode_data = $this->getShipmentDetails($pincode,'fedex',$payment_code);
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['service_available'] = false;
        $advice['serviceability'] = "There is no service available to the pincode $pincode";
        $advice['additional_notes'] = "You should try an another shipment option.";
      }
      else{
        $advice['is_serviceable']   = (strtolower($pincode_data['oda_opa_or_reg']) == 'regular')
                                        ? 1  
                                        : 0;
        $advice['serviceability']   = $pincode_data['oda_opa_or_reg'];
        $advice['cod']              = $pincode_data['cod_serviceable'];
        $advice['prepaid']          = 1; 
        $advice['distance']         = 0; // no distance information for pincode in table
        $advice['location']         = $pincode_data['city'];
        $advice['service_available'] = true;  // if pincode exist in DB means area is serviceble
        $advice['serviceability_info'] = "Fedex ".$pincode_data['oda_opa_or_reg']." service is available to the pincode $pincode";
        $advice['additional_notes'] = "Available shippind method is ".$pincode_data['oda_opa_or_reg'];
        if($payment_code == 'cod'){
          $advice['additional_notes'] .= " and cod is also available.";
        }
      }
      $advice['order'] = 5;
      return $advice;
    }
    /**
     * [checkForExpress This function provide the shipment advice for delhivery such as serviceability, additional notes.]
     * @return [array] [{service_available,serviceability,additional_notes}]
     */
    public function checkForDelhivery(){
      extract($this->_order_info);
      $advice = array('courier'=>'Delhivery');
      $payment_code = !empty($this->_order_info['payment_code']) ? strtolower(trim($this->_order_info['payment_code'])) : null;
      $pincode_data = $this->getShipmentDetails($pincode,'delhivery',$payment_code);
      //pr($pincode_data); die;
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['service_available'] = false;
        $advice['serviceability'] = "There is no service available to the pincode $pincode";
        $advice['additional_notes'] = "You should try an another shipment option.";
      }else{
        $advice['is_serviceable']   = 1;
        $advice['serviceability']   = 'Available';
        $advice['cod']              = $pincode_data['cod'];
        $advice['prepaid']          = $pincode_data['prepaid'];
        $advice['distance']         = 0; // no distance information for pincode in table
        $advice['location']         = $pincode_data['city'];
        $advice['reverse_pickup']   = $pincode_data['pickup'];
        $advice['pickup']           = 1;
        $advice['service_available'] = true;
        if(!$pincode_data['prepaid'] && !$pincode_data['cod'] && !$pincode_data['pickup'])
        {
            $advice['serviceability_info'] = "No service is available in this area.";
        } else{
            $advice['serviceability_info'] = "Service is available in this area.";
        }
        
        $prepaid = ($pincode_data['prepaid'])?'Available':'Not available';
        $advice['additional_notes'] = 'Prepaid - ' . $prepaid . '<br />';  
        $cod = ($pincode_data['cod'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Cod - ' . $cod . '<br />';
        $pickup = 'Available';
        $advice['additional_notes'] .= 'Pickup - ' . $pickup . '<br />';  
        $reverse_pickup = ($pincode_data['pickup'])?'Available':'Not available';
        $advice['additional_notes'] .= 'Reverse pickup - ' . $reverse_pickup . '<br />';  
      }
      $advice['order'] = 6;
      return $advice;
    }
    /**
     * [checkForBluedartApex description]
     * @return [type] [description]
     */
    public function checkForBluedartApex(){
        extract($this->_order_info);
      $advice = array('courier'=>'Bluedart Apex');
      $payment_code = !empty($this->_order_info['payment_code']) ? strtolower(trim($this->_order_info['payment_code'])) : null;
      $pincode_data = $this->getShipmentDetails($pincode,'bluedart_apex',$payment_code);
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['service_available'] = false;
        $advice['serviceability_info'] = "There is no service available to the pincode $pincode";
        $advice['additional_notes'] = "You can check manually on <a href='https://www.bluedart.com/locationfinder.html'>BlueDart Location Finder</a> <br>";
        $advice['additional_notes'] .= "Or you should try an another shipment option.";
        
      }
      else{
        $advice['is_serviceable']   = 1;
        $advice['serviceability']   = 'Available';
        $advice['cod']              = 0;
        $advice['prepaid']          = $pincode_data['prepaid'];
        $advice['distance']         = 0; // no distance information for pincode in table
        $advice['location']         = $pincode_data['city'];
        $advice['service_available'] = true;
        $advice['serviceability_info'] = "Bluedart direct service is available to the pincode $pincode";
        $advice['additional_notes'] = "Available shippind method is direct";
        if($payment_code == 'cod'){
          $advice['additional_notes'] .= " and cod is also available.";
        }
      }
      $advice['order'] = 3;
      return $advice;
    }
    /**
     * [checkForBluedartSurface description]
     * @return [type] [description]
     */
    public function checkForBluedartSurface(){
        extract($this->_order_info);
      $advice = array('courier'=>'Bluedart Surface');
      $payment_code = !empty($this->_order_info['payment_code']) ? strtolower(trim($this->_order_info['payment_code'])) : null;
      $pincode_data = $this->getShipmentDetails($pincode,'bluedart_surface',$payment_code);
      if(empty($pincode_data)){
        $advice['is_serviceable']   = 0;
        $advice['serviceability']   = 'Not available';
        $advice['cod']              = 0;
        $advice['prepaid']          = 0;
        $advice['distance']         = 0;
        $advice['location']         = 'Not available';
        $advice['service_available'] = false;
        $advice['serviceability_info'] = "There is no service available to the pincode $pincode";
        $advice['additional_notes'] = "You can check manually on <a href='https://www.bluedart.com/locationfinder.html'>BlueDart Location Finder</a> <br>";
        $advice['additional_notes'] .= "Or you should try an another shipment option.";
        
      }
      else{
        $advice['is_serviceable']   = 1;
        $advice['serviceability']   = 'Available';
        $advice['cod']              = $pincode_data['cod'];
        $advice['prepaid']          = $pincode_data['prepaid'];
        $advice['distance']         = 0; // no distance information for pincode in table
        $advice['location']         = $pincode_data['city'];
        $advice['service_available'] = true;
        $advice['serviceability_info'] = "Bluedart direct service is available to the pincode $pincode";
        $advice['additional_notes'] = "Available shippind method is direct";
        if($payment_code == 'cod'){
          $advice['additional_notes'] .= " and cod is also available.";
        }
      }
      $advice['order'] = 4;
      return $advice;
    }
    /**
     * [getWaybillReport This method is for getting waybill report of given state.]
     * @param  [int] $zone_id
     * @return [array] [return an array of reports of given state id]
     */
    public function getWaybillReport($zone_id){
      if(!empty($zone_id)){
        $sql   = "Select * ";
        $sql .= "From oc_paperwork ";
        $sql .= "Where zone_id = $zone_id";
        $result = $this->_db->query($sql);
        if(!empty($result->row)){
          return $result->row;
        }else{
          return array(
              'zone_id'=>'',
              'state'=>'',
              'b2c_stat_levy_type'=>'',
              'b2c_stat_levy_liable'=>'',
              'b2c_paperwork_req'=>'',
              'b2c_paperwork_exem_lim'=>'',
              'b2c_paperwork_req'=>'',
              'b2b_paperwork_inb_req'=>'',
              'b2b_paperwork_outb_req'=>'',
              'additional_notes'=>'');
        }
      }
    }
    /**
     * [getShipmentDetails This function takes an input and checks]
     * @param  [int] $pin_code [pincode of the shipment city]
     * @param [string] $logistic [Logistic company name such as gati, fedex, bluedart]
     * @param [string] $payment_method [This contain cod or prepaid.]
     * @return [array ] [serviceability,distance]
     */
    public function getShipmentDetails($pincode, $logistic, $payment_method = '') {
        if (!empty($pincode)) {
            if ($logistic == 'gati') {
                $sql = "SELECT `serviceability`,`distance`,`cod`,
                               `ou_name`,`pincode`,`prepaid`";
                $sql .= "FROM `" . DB_PREFIX . "gati_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                status = 1 
                        ";
            } elseif ($logistic == 'fedex') {
                $sql = "SELECT `pincode`,`city`,`oda_opa_or_reg`,`cod_serviceable`";
                $sql .= "FROM `" . DB_PREFIX . "fedex_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                status = 1 
                        ";
            } elseif ($logistic == 'dotzot_express') {
                $sql = "SELECT `product`,`pincode`,`city`,
                               `prepaid`,`cod`,`reverse_pickup`,
                               `pickup`";
                $sql .= "FROM `" . DB_PREFIX . "dotzot_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                product = 'Express' 
                                AND 
                                status = 1 ";
            } elseif ($logistic == 'dotzot_economy') {
                $sql = "SELECT `product`,`pincode`,`city`,
                               `prepaid`,`cod`,`reverse_pickup`,
                               `pickup`";
                $sql .= "FROM `" . DB_PREFIX . "dotzot_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                product = 'Economy' 
                                AND 
                                status = 1 ";
            } elseif ($logistic == 'bluedart_surface') {
                $sql = "SELECT `city`, `pincode`,
                               `mode`,`cod`,`prepaid` ";
                $sql .= "FROM `" . DB_PREFIX . "bluedart_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                mode='surface' 
                                AND 
                                status = 1 ";
            }elseif ($logistic == 'bluedart_apex') {
                $sql = "SELECT `city`, `pincode`,
                               `mode`,`cod`,`prepaid` ";
                $sql .= "FROM `" . DB_PREFIX . "bluedart_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                mode='apex' 
                                AND 
                                status = 1 ";
            } elseif ($logistic == 'delhivery') {
                $sql = "SELECT `city`, `pincode`,`cod`,
                               `prepaid`,`pickup` ";
                $sql .= "FROM `" . DB_PREFIX . "delhivery_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                status = 1 ";
            }elseif ($logistic == 'truxcargo') {
                $sql = "SELECT `pincode`,`product_type`,`center`,
                               `facility_type`,`weight_limit`,`oda` ";
                $sql .= "FROM `" . DB_PREFIX . "truxcargo_pincodes` ";
                $sql .= "WHERE `pincode` = '".$this->_db->escape($pincode)."' 
                                AND 
                                status = 1 ";
            }

            if (!empty($sql)) {
                $result = $this->_db->query($sql);
                if (!$result) {
                    return false;
                }
                if ($result->num_rows > 0) {
                    return $result->row;
                }
            }
        }
    }

}
?>