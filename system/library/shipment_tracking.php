<?php
class ShipmentTracking {

    public $tracking_url_ltd      = 'http://www.gati.com/webservices/SINGLEECOMDKTTRACK.jsp';
    public $tracking_url_kwe      = 'http://www.gatikwe.com/webservices/GatiKWEDktTrack.jsp';

    public function __construct( $registry){
        $this->_registry = $registry;
        if(method_exists($registry, 'get')){
               $this->_db = $registry->get('db');
               
        }else{
              $this->_db = $registry->db;
          }
    }
        
    public function storeTrackingInfo($request_data){
        if(is_array($request_data) && !empty($request_data)){
            $check_tracking_no = $this->upadateTrackingStatus($request_data[3]);
            if(empty($check_tracking_no)){
                $query = "INSERT INTO ".DB_PREFIX."order_tracking SET ". implode(",",$request_data);
            }else{
                $query = "UPDATE ".DB_PREFIX."order_tracking SET ". implode(",",$request_data) . " WHERE tracking_no = '".$this->_db->escape($request_data[3])."' " ;
            }
            $this->_db->query($query);
        }
    }

    public function upadateTrackingStatus($tracking_no){
        $sql = "SELECT tracking_id,daily_status FROM ".DB_PREFIX."order_tracking
               WHERE ".$this->_db->escape($tracking_no)."";       
        $result = $this->_db->query($sql);  
           
        return $result->num_rows;
    }

    public function getSubOrdersByCourierPartner($courier_partner){
        if(!empty($courier_partner)){
            $sql = "SELECT 
                osb.order_id,
                o.customer_id,
                o.order_no,
                oc.password,
                o.firstname,
                o.total,
                o.telephone,
                o.payment_code,
                o.shipping_city,
                osb.suborder_id,
                osb.tracking_no As suborder_tracking_no, ";
                if($courier_partner !='failed'){
                    $sql .= "GROUP_CONCAT(osl.tracking_no) AS shipping_label_tracking_no,";
                }
            $sql .= "osb.courier_partner AS shipment_company,
                oz.zone_area
                FROM ".DB_PREFIX."suborder osb
                INNER JOIN ".DB_PREFIX."order o ON (osb.order_id = o.order_id)
                INNER JOIN ".DB_PREFIX."customer oc ON (o.customer_id = oc.customer_id)
                INNER JOIN ".DB_PREFIX."shipping_label osl ON (osb.suborder_id = osl.suborder_id)
                INNER JOIN ".DB_PREFIX."zone oz ON(o.shipping_zone_id = oz.zone_id) ";
                        
                if($courier_partner == 'failed'){
                    $sql .= 'WHERE osb.order_status_id = 17';
                }else{
                    $sql .= "WHERE osb.courier_partner = '".$this->_db->escape($courier_partner)."' AND (osb.order_status_id = 14 || osb.order_status_id = 4 || osb.order_status_id = 17) GROUP BY osl.suborder_id";
                }
            $result = $this->_db->query($sql);
            if($result->num_rows){
                foreach($result->rows as $value){
                    $order_detail[$value['suborder_id']] = $value;
                    $order_tag = $this->getOrderTag($value['order_id']);
                    if(is_array($order_tag)){
                        foreach($order_tag as $tag){
                            $order_detail[$value['suborder_id']]['crm_user_id']  = $tag['crm_user_id'];
                            $order_detail[$value['suborder_id']]['fse_mobile']  = $tag['telephone'];
                        }
                    }
                }
            }else{
                 $order_detail = "No Orders Found";
            }
            return $order_detail;
        }
    }
    
    public function getOrderTag($order_id){
        if(!empty($order_id)){
            $sql = "SELECT oss.crm_user_id,
                    oss.telephone
                    FROM ".DB_PREFIX."order_sales_staff ooss
                    INNER JOIN ".DB_PREFIX."sales_staff oss ON(ooss.sales_staff_id = oss.staff_id)
                    WHERE ooss.order_id = '".(int)$order_id."' ";
            $result = $this->_db->query($sql);           
            if($result->num_rows){
                $return = $result->rows;
            }else{
                $return = '';
            }
            return $return;
        }
    }

    public function getFailedParcelData($values){
        $sql = "SELECT
                oz.zone_area
                FROM ".DB_PREFIX."order o
               INNER JOIN ".DB_PREFIX."zone oz ON(o.shipping_zone_id = oz.zone_id)
               WHERE o.order_id = '".(int)$values['order_id']."' "; 
        $result = $this->_db->query($sql);
        $data['zone_area'] = $result->row['zone_area'];
        $order_tag = $this->getOrderTag($values['order_id']);
        if(is_array($order_tag)){
            foreach($order_tag as $tag){
                $data['crm_user_id'] = $tag['crm_user_id'];
            }
        }
        return $data;
    }

    public function getGatiDocketType($docket_no)
    {
        $sql = "
                SELECT 
                    type
                FROM 
                 ".DB_PREFIX."gati_dockets
                WHERE
                    docket_no = '".$this->_db->escape($docket_no)."'
            ";
        $result = $this->_db->query($sql);
        if($result->num_rows) {
            return $result->row['type'];
        }
        return '';
    }
    public function getAPIEndPoint($docket_type, $docket_no)
    {
        if($docket_type == 'gati_ltd'){ 
            return $this->tracking_url_ltd . '?p1=' . $docket_no . '&p2=A81BFCBC4CA0D373';
        }else{ 
            return $this->tracking_url_kwe . '?p1=' . $docket_no . '&p2=50826005497E20EE';
        }
    }
    public function getTypeOfManualDocketNumber($docket_no)
    {
        $docket_series = substr($docket_no, 0, 1);
        if(in_array($docket_series, array(2,3))) {
            return 'gati_kwe';
        }else if(in_array($docket_series, array(5))) {
            return 'gati_ltd';
        }
       return ''; 
    }
    public function trackingGati($docket_no){
       
       return false;

        $type = $this->getGatiDocketType($docket_no);
        if(empty($type)) {
            // check type for manual dockets
            $type = $this->getTypeOfManualDocketNumber($docket_no);
            if(empty($type)) {
                echo 'Wrong docket number'; exit;    
            }
        }      

        $tracking_url = $this->getAPIEndPoint($type, $docket_no);

        $ch = curl_init($tracking_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        if(curl_error($ch)){
            $html = curl_error($ch); 
        }else{
           $html = $this->getTrackingHistoryHtml($result); 
        }
        
        curl_close($ch);
            
        return $html; 
    }

    public function getTrackingHistoryHtml($response) 
    {
        $tracking = array();
        if(!empty($response))
        {
            $string         =  preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $response);
            $xml            = new SimpleXMLElement($string);
            $data_array     = json_decode(json_encode($xml), TRUE);
            if(is_array($data_array) && !empty($data_array['dktinfo']))
            {
                $tracking = array();
                $tracking_status = array();
                $tracking['Docket_No']      = (string) $data_array['dktinfo']['dktno'];
                $tracking['Reference']      = (string) $data_array['dktinfo']['ORDER_NO'];
                $tracking['Origin']         = (string) $data_array['dktinfo']['BOOKING_STATION'];
                $tracking['No_Of_Packages'] = (string) $data_array['dktinfo']['NO_OF_PKGS'];
                $tracking['Assured_Dly_Dt'] = (string) $data_array['dktinfo']['ASSURED_DELIVERY_DATE'];
                $tracking['Receiver_s_Name']= (string) $data_array['dktinfo']['CONSIGNEE_NAME'];
                $tracking['Destination']    = (string) $data_array['dktinfo']['DELIVERY_STATION'];
                $tracking['Status']         = (string) $data_array['dktinfo']['DOCKET_STATUS'];
                $tracking['Booking_date']   = (string) $data_array['dktinfo']['BOOKED_DATETIME'];
                $tracking['Weight']         = (string) $data_array['dktinfo']['ACTUAL_WEIGHT'];
                $tracking['PickUpDate']     = (string) ($data_array['dktinfo']['PREPICKUP_INFO']['PINFO'][0]['PICKUP_DATE'] ?? '');
                $tracking['tracking_status']= array();
                $tracking_data = array();
                if(is_array($data_array['dktinfo']['TRANSIT_DTLS']['ROW']) 
                    && 
                   !empty($data_array['dktinfo']['TRANSIT_DTLS']['ROW'])
                ) {
                    foreach ($tracking_data = $data_array['dktinfo']['TRANSIT_DTLS']['ROW'] as $key => $value) {
                        $tracking['tracking_status'][] = array(
                                'date'      => (string) $value['INTRANSIT_DATE'],
                                'time'      => (string) $value['INTRANSIT_TIME'],
                                'location'  => (string) $value['INTRANSIT_LOCATION'],
                                'status'    => (string) $value['INTRANSIT_STATUS'],
                            );
                    }
                }
            }
        }
        return $tracking;
    }

    public function getTrackingHistoryHtmlOLD($response)
    {
        $xml = new SimpleXMLElement($response);
        $tracking = array();
        $tracking_status = array();
        if(!empty($xml->dktinfo)) {
            $tracking['Docket_No']      = (string) $xml->dktinfo->dktno;
            $tracking['Reference']      = (string) $xml->dktinfo->ORDER_NO;
            $tracking['Origin']         = (string) $xml->dktinfo->BOOKING_STATION;
            $tracking['No_Of_Packages'] = (string) $xml->dktinfo->NO_OF_PKGS;
            $tracking['Assured_Dly_Dt'] = (string) $xml->dktinfo->ASSURED_DELIVERY_DATE;
            $tracking['Receiver_s_Name']= (string) $xml->dktinfo->CONSIGNEE_NAME;
            $tracking['Destination']    = (string) $xml->dktinfo->DELIVERY_STATION;
            $tracking['Status']         = (string) $xml->dktinfo->DOCKET_STATUS;
            $tracking['Booking_date']   = (string) $xml->dktinfo->BOOKED_DATETIME;
            $tracking['Weight']         = (string) $xml->dktinfo->ACTUAL_WEIGHT;
            $tracking['PickUpDate']     = '';
            if(!empty($xml->dktinfo->PREPICKUP_INFO->PINFO)) {
                foreach ($xml->dktinfo->PREPICKUP_INFO->PINFO as $value) {
                    $tracking['PickUpDate'] = (string)$value->PICKUP_DATE;
                    break;
                }
            }

            if(!empty($xml->dktinfo->TRANSIT_DTLS))
            {
                foreach ($xml->dktinfo->TRANSIT_DTLS->ROW as $ROW) {
                    $tracking_status[] = array(
                                'date'      => (string) $ROW->INTRANSIT_DATE,
                                'time'      => (string) $ROW->INTRANSIT_TIME,
                                'location'  => (string) $ROW->INTRANSIT_LOCATION,
                                'status'    => (string) $ROW->INTRANSIT_STATUS,
                            );
                }
            }
        }
        $tracking['tracking_status'] = serialize($tracking_status);
        return $tracking;
    }

    
    public function trackingGatiOld($docket_no){
        $data = array(
                'docket_id' =>  $docket_no,
                'Submit'    =>      'Submit',
                );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://www.gati.com/track-by-docket');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);                
        
        $html = $this->getGatiParseHtml($result);
                
        return $html; 
    }

    private function getGatiParseHtml($html){
        if(!empty($html)){

            /*** a new dom object ***/ 
            $dom = new domDocument;             

            $internalErrors = libxml_use_internal_errors(true);

            /*** load the html into the object ***/ 
            $dom->loadHTML($html);

            libxml_use_internal_errors($internalErrors);

            /*** discard white space ***/ 
            $dom->preserveWhiteSpace = false; 

            /*** the table by its tag name ***/ 
            $table = $dom->getElementsByTagName('table');
            
            /*** get all rows from the table ***/ 
            $rows = $table->item(2)->getElementsByTagName('tr');
           
             $i = 0;
             $j = 0;   
             foreach ($rows as $row) {
                 
                /*** get each column by tag name ***/ 
                $cols = $row->getElementsByTagName('td');                  
                if($cols->length){
                    if($cols->item($i+2) != NULL){
                        $tracking_status[$j]['date']     = $cols->item($i)->nodeValue;
                        $tracking_status[$j]['time']     = $cols->item($i+1)->nodeValue;
                        $tracking_status[$j]['location'] = $cols->item($i+2)->nodeValue;
                        $tracking_status[$j]['status']   = $value = $cols->item($i+3)->nodeValue;
                         $j++;
                    }else{    
                        if($cols->item($i+1) != NULL){
                            $value = preg_replace("/[^A-Za-z0-9\-]/",'_',$cols->item($i)->nodeValue);
                            $value = str_replace("__",'_',$value);    
                            $data[$value] = $cols->item($i+1)->nodeValue;    
                        }    
                    }
                }
            }
            //unset($tracking_status[0]);
            $data['tracking_status'] = serialize($tracking_status);
            
            return $data;
        }
    }

    public function getFailedOrder(){
        $order_detail = array();
        $sql = "SELECT 
            osb.order_id,
            o.customer_id,
            osh.date_added,
            o.order_no,
            oc.password,
            o.firstname,
            o.total,
            o.telephone,
            o.payment_code,
            o.shipping_city,
            osb.suborder_id,
            osb.tracking_no As suborder_tracking_no,
            osb.courier_partner AS shipment_company,
            oz.zone_area
            FROM ".DB_PREFIX."suborder osb
            INNER JOIN ".DB_PREFIX."order o ON (osb.order_id = o.order_id)
            INNER JOIN ".DB_PREFIX."customer oc ON (o.customer_id = oc.customer_id)
            INNER JOIN ".DB_PREFIX."order_history osh ON (osb.suborder_id = osh.suborder_id)
            INNER JOIN ".DB_PREFIX."zone oz ON(o.shipping_zone_id = oz.zone_id)
            WHERE osb.order_status_id = 17 AND osh.order_status_id = 17  GROUP BY osb.suborder_id ORDER BY osh.date_added DESC";
        
        $result = $this->_db->query($sql);
        if($result->num_rows){
            foreach($result->rows as $value){
                $order_detail[$value['suborder_id']] = $value;
                $order_tag = $this->getOrderTag($value['order_id']);
                if(is_array($order_tag)){
                    foreach($order_tag as $tag){
                        $order_detail[$value['suborder_id']]['crm_user_id']  = $tag['crm_user_id'];
                        $order_detail[$value['suborder_id']]['fse_mobile']  = $tag['telephone'];
                    }
                }
            }
        }
        return $order_detail;
        
    }
    
    /**
    * Get All Shipment Data for each courier partner having status 
    * not in (5=> complete, 8=> failed, 15=> delivered) for each suborder
    * 
    * @return Array of all suborders with their courier partners
    * @author MSA 4 Jan 2019
    */
    public function getShipmentsToTrack( string $courier_partner = '' )
    {
        if(!empty(trim($courier_partner))) {
            $courier_partner_list = explode(',', trim($courier_partner));
        }else{
            $courier_partner_list = array('gati','dotzot','bluedart','fedex','delivery');
        }

        $courier_partner_list = implode("' , '", $courier_partner_list);

        $status_ids = array_merge(ORDER_STATUS_CLUSTERS['shipped'], 
                                  ORDER_STATUS_CLUSTERS['delivery_issues']
                                  );
        $shipped = implode("' , '", $status_ids);
        
        
        $sql = "SELECT 
                    osb.order_id,
                    o.customer_id,
                    o.order_no,
                    trim(concat(o.firstname, ' ', o.lastname)) AS customer_name,
                    osb.total,
                    o.telephone,
                    o.payment_code,
                    o.shipping_city,
                    osb.suborder_id,
                    osb.tracking_no As suborder_tracking_no,
                    osb.courier_partner AS shipment_company,
                    oz.zone_area,
                    ogd.type,
                    osb.order_status_id
                FROM ".DB_PREFIX."suborder osb
                INNER JOIN 
                    ".DB_PREFIX."order o ON (osb.order_id = o.order_id)
                INNER JOIN 
                    ".DB_PREFIX."shipping_label osl ON (osb.order_id = osl.order_id)
                INNER JOIN 
                    ".DB_PREFIX."zone oz ON(o.shipping_zone_id = oz.zone_id) 
                INNER JOIN 
                    ".DB_PREFIX."courier_dockets ocd ON (osb.order_id = ocd.order_id)
                INNER JOIN 
                    ".DB_PREFIX."courier_partners ocp ON (ocd.courier_partners_id = ocp.id)
                LEFT JOIN 
                    ".DB_PREFIX."gati_dockets ogd ON (ocd.docket_no = ogd.docket_no)
                WHERE 
                    osb.order_status_id IN ('" . $shipped . "') 
                    AND (osb.suborder_id = osl.suborder_id AND trim(osb.tracking_no) = trim(osl.tracking_no)) 
                    AND (ocd.suborder_id = osb.suborder_id AND trim(osb.tracking_no) = trim(ocd.docket_no))
                    AND ocp.status = '1' 
                    AND osb.courier_partner IN ('". $courier_partner_list ."')
                GROUP BY 
                    osb.suborder_id
                ";
        
        $result = $this->_db->query($sql);

        if($result->num_rows){

            foreach($result->rows as $value){
                $order_detail[$value['suborder_id']] = $value;
                $order_tag = $this->getOrderTag($value['order_id']);
                if(is_array($order_tag)){
                    foreach($order_tag as $tag){
                        $order_detail[$value['suborder_id']]['crm_user_id']  = $tag['crm_user_id'];
                        $order_detail[$value['suborder_id']]['fse_mobile']  = $tag['telephone'];
                    }
                }
            }
        }else{
            $order_detail = array();
        }

        return $order_detail;
    }


}
            