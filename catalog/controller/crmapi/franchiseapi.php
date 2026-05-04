<?php
class Controllercrmapifranchiseapi extends Controller 
{    
	// used in order details
  public function createInvoiceNo() {
    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      $inputJSON = file_get_contents('php://input');
      $request = json_decode( $inputJSON, TRUE );
    }
    
    $res = array();
    $crm_user_id = (isset($request['crm_user_id']))?$request['crm_user_id']:'';
    $access_token = (isset($request['access_token']))?$request['access_token']:'';
    if($crm_user_id  <= 0){
      $res['status'] = '0';
      $res['status_text'] = 'error';
      $res['message'] = 'Please enter crm_user_id.!';
      echo json_encode($res); exit;  
    }
    if($access_token ==''){
      $res['status'] = '0';
      $res['status_text'] = 'error';
      $res['message'] = 'Please enter access_token.!';
      echo json_encode($res); exit;  
    }
    $this->load->model('crm/tentativeadvance');
    $validation = $this->model_crm_tentativeadvance->userValidation($crm_user_id, $access_token);

    if( !$validation ){
      $res['status'] = '0';
      $res['status_text'] = 'Failed';
      $res['message'] = 'User can not access!';
      echo json_encode($res); exit;  
    }

    $json = array();
    $order_id     = (int) $request['order_id'];
    $suborder_id  = $request['suborder_id'];
    $franchise_id = $request['franchise_id'];
    $data = array(
               "order_id" => $order_id, 
               "suborder_id" =>$suborder_id,
               "franchise_id"=>$franchise_id);
    echo $this->createBuyerInvoiceNo($data); exit;
  }

  /**
     * Public Function to generate Buyer Invoice
     * @param: Array
     * @return: Void
     * @author: Nishu, Dec 2017
  */
  public function createBuyerInvoiceNo($data = array()) {

    $order_id     = (int)$data['order_id'];
    $suborder_id  = $data['suborder_id'];
    $franchise_id = (int)$data['franchise_id'];
    $res          = array();
    //Check If data is missing
    if(empty($order_id) || empty($suborder_id) || empty($franchise_id)){
      $res['status']      = '0';
      $res['status_text'] = 'error';
      $res['message']     = 'Data Incomplete.';
      return json_encode($res);
    }
    //Check if order is existed or not for given franchise Id
    $isExist = $this->isExistFranchiseOrder($data);
    if(!$isExist){
      $res['status']      = '0';
      $res['status_text'] = 'error';
      $res['message']     = 'Order does not exist for given FranchiseId.';
      return json_encode($res);
    }

    //Check if franchise invoice is already existed
    $invoiceExist = $this->checkFranchiseInvoice($data);
    if($invoiceExist){
      $this->load->model('account/customer');
      $this->load->model('account/address');
      $seller_data = $this->model_account_customer->getCustomer($franchise_id);
      try{   
        if(empty($seller_data['address_id'])){
          $res['status']      = '0';
          $res['status_text'] = 'error';
          $res['message']     = 'Your Address is incomplete, Kindly update it to generate invoice.';
          return json_encode($res);
        }
        $seller_address = $this->model_account_address->getAddress($seller_data['address_id'], $franchise_id);
        if(empty($seller_address)){
          $res['status']      = '0';
          $res['status_text'] = 'error';
          $res['message']     = 'Your Address is incomplete, Kindly update it to generate invoice.';
          return json_encode($res);
        }
        $gst_number = $seller_data['gst_number'] ?? '';
        $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number = trim($gst_number);

        $seller_address['gst_number'] = $gst_number;

        //Add database entry for franchise invoice
        $this->addFranchiseInvoiceToDb($order_id, $suborder_id, $franchise_id, $seller_address);

        //send mail to franchise
        $mail_data = array();
        $mail_data['email']       = $seller_data['email'];
        $mail_data['name']        = $seller_data['firstname'].' '.$seller_data['lastname'];
        $mail_data['order_id']    = $order_id;
        $mail_data['suborder_id'] = $suborder_id;
        $mail_data['franchise_id']= $franchise_id;

        $this->mailFromFranchiseOrderInvoice($mail_data);

      } catch (Exception $e) {
          $res['status']      = '0';
          $res['status_text'] = 'error';
          $res['message']     = $e->getMessage();
      }
    }

    //Mark Order as delivered
    $this->load->model('checkout/order');
    $value = array();
    $value['order_id']        = $order_id;
    $value['suborder_id']     = $suborder_id;
    $value["order_status_id"] = (int)ORDER_STATUS['Delivered'];
    $value["notify_email"]    = 0;
    $value["notify_sms"]      = 0;
    $value["comment"] = "Franchise Order mark deliver and generate Buyer Invoice.";
    $value["notes"]   =  "FranchiseOrder mark delivered";

    $this->model_checkout_order->addOrderHistory($value);

    //get Franchise Invoice full file_name to Download
    $filename = array();
    $filename['order_id']      = $order_id;
    $filename['suborder_id']   = $suborder_id;
    $filename['franchise_id']  = $franchise_id;
    $filename = base64_encode(serialize($filename));
    $franchise_invoice = new FranchiseInvoice( $this, $filename );
    $franchise_invoice->setOptions( 'get_full_path' , TRUE );
    $invoice_file_name = base64_decode( $franchise_invoice->getFile() );

    //Download Franchise invoice
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-disposition: attachment; filename='.basename($invoice_file_name));
    header('Expires: 0');
    header('Cache-Control: no-cache');
    header('Pragma: public');
    header('Content-Length: ' . filesize($invoice_file_name));
    ob_clean();
    flush();
    readfile($invoice_file_name);
    exit();
  }

  /**
   * private method to add franchise invoice data into DB
   * @author: Nishu, Aug 2018
  */
  private function addFranchiseInvoiceToDb(int $order_id, string $suborder_id, int $franchise_id, array $seller_address) : void {
      // Get seller invoice prefix as per current financial year
      $prefix_obj     = new Prefixes();

      $gst_number = $seller_address['gst_number'] ?? '';
      $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
      $gst_number = trim($gst_number);

      $prefix_detail  = $prefix_obj->getPrefix($this->db, $gst_number, 'FRANCHISE_INVOICE');
      $prefix         = $prefix_detail['prefix'];
      $financial_year = '';

      if ( (int)(date('m')) <= 3 ) {
          $financial_year = date('y', strtotime('-1 years'));
      } else {
          $financial_year = date('y');
      }

      $invoice_prefix = $prefix . $financial_year . '-';
      $invoice_no = (int)$this->createFranchiseInvoiceNo($invoice_prefix);

      $buyer_data = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id);
      $buyer      = $buyer_data['order'];

      //Get StateCode for buyer
      $this->load->model('localisation/zone');
      $zone_ids = '';
      $zone_ids = $seller_address['zone_id'];
      $zone_ids .= !empty($buyer['payment_zone_id'])? ','.$buyer['payment_zone_id'] : '';
      $zone_ids  = trim($zone_ids, ',');
      $state_codes = $this->model_localisation_zone->getZoneGSTStateCode($zone_ids);
      $gst_state_code = array();
      foreach ($state_codes as $state_code) {
          $gst_state_code[$state_code['zone_id']] = $state_code['gst_state_code'];
      }

      $invoice_meta = array();
      //Set Seller Details
      $invoice_meta['seller_data']['company']          = $seller_address['company'];
      $invoice_meta['seller_data']['address1']         = $seller_address['address_1'];
      $invoice_meta['seller_data']['address2']         = $seller_address['address_2'];
      $invoice_meta['seller_data']['pincode']          = $seller_address['postcode'];
      $invoice_meta['seller_data']['city']             = $seller_address['city'];
      $invoice_meta['seller_data']['tin']              = $gst_number;
      $invoice_meta['seller_data']['zone_id']          = $seller_address['zone_id'];
      $invoice_meta['seller_data']['country_id']       = $seller_address['country_id'];
      $invoice_meta['seller_data']['pickup_city_code'] = $seller_address['city'];
      $invoice_meta['seller_data']['state']            = $seller_address['zone'];
      $invoice_meta['seller_data']['country']          = $seller_address['country'];
      $invoice_meta['seller_data']['gst_state_code']   = $gst_state_code[$seller_address['zone_id']];

      //Set Buyer Data
      /*$invoice_meta['buyer_data']['company'] = $buyer['payment_firstname'].' '.$buyer['payment_lastname'];
      $invoice_meta['buyer_data']['address1'] = $buyer['payment_address_1'];
      $invoice_meta['buyer_data']['address2'] = $buyer['payment_address_2'];
      $invoice_meta['buyer_data']['city'] = $buyer['payment_city'];
      $invoice_meta['buyer_data']['pincode'] = $buyer['payment_postcode'];
      $invoice_meta['buyer_data']['tin'] = $buyer['gst_number'];
      $invoice_meta['buyer_data']['state'] = $buyer['payment_zone'];
      $invoice_meta['buyer_data']['country'] = $buyer['payment_country'];
      $invoice_meta['buyer_data']['zone_id'] = $buyer['payment_zone_id'];
      $invoice_meta['buyer_data']['email'] = $buyer['payment_country_id'];
      $invoice_meta['buyer_data']['gst_state_code'] = $gst_state_code[$buyer['payment_zone_id']];*/

      $sql = "INSERT INTO " . DB_PREFIX . "franchise_invoice 
                SET
                  order_id         = '". (int)$order_id ."',
                  suborder_id      = '". $this->db->escape($suborder_id) ."',
                  franchise_id     = '". (int)$franchise_id ."',
                  invoice_prefix   = '". $this->db->escape($invoice_prefix) ."',
                  invoice_no       = '". (int)$invoice_no ."',
                  date_added       = NOW(),
                  invoice_meta     = '". serialize($invoice_meta) ."',
                  status           = 1
                ";
      $this->db->query($sql);

      return;
  }

  /**
   * Public method to mail franchise with buyer invoice for order type Form-Franchise
   * @param:  array $data
   * @return: void
   * @author: Nishu, Aug 2018
  */
  public function mailFromFranchiseOrderInvoice(array $data) : void{

    if(!empty($data)){

      //get Franchise Invoice full file_name to Download
      $filename = array();
      $filename['order_id']      = $data['order_id'] ?? 0;
      $filename['suborder_id']   = $data['suborder_id'] ?? '';
      $filename['franchise_id']  = $data['franchise_id'] ?? 0;
      $filename = base64_encode(serialize($filename));
      $franchise_invoice = new FranchiseInvoice( $this, $filename );
      $franchise_invoice->setOptions( 'get_full_path' , TRUE );
      $invoice_file_name = base64_decode( $franchise_invoice->getFile() );

      //Mail body content
      $html = $this->mailContentFronFranchiseInvoice($data['suborder_id']);

      $mail = new PHPMailer();

      $mail->isSMTP();
      $mail->Host       = $this->config->get('config_mail_smtp_hostname');
      $mail->Port       = $this->config->get('config_mail_smtp_port');
      $mail->Username   = $this->config->get('config_mail_smtp_username');
      $mail->Password   = $this->config->get('config_mail_smtp_password');
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth   = true;
      $mail->setFrom(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
      $mail->addReplyTo(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
      $mail->addAddress( $data['email'], $data['name']);
      $mail->Subject = 'Franchise Invoice for Order '. $data['suborder_id'];
      $mail->AddAttachment($invoice_file_name);
      $mail->msgHTML($html);
      
      $mail->send(0, false);
    }

  }

  /**
   * Private method to get mail content 
   * @author: Nishu, Aug 2018
  */
  private function mailContentFronFranchiseInvoice(string $suborder_id) : string{
    $text .= "<div>";
    $text .= "Dear Franchise,<br><br>";
    $text .= "<p>Please find attached the invoice for the Order ". $suborder_id .", billed at your store.</p>";
    $text .= "</div>";

    return $text; 

  }


  /**
   * public function to check franchise order is existed or not
   * @param: $data Array
   * @return Boolen
   * @author: Nishu, Dec 2017
  */
  public function isExistFranchiseOrder($data){
    $sql = "SELECT * 
             FROM oc_order
             WHERE 
               order_id ='". (int)$data['order_id'] ."'
               AND franchise_id = '". (int)$data['franchise_id'] ."' ";
    $result = $this->db->query($sql);
    if($result->num_rows > 0 ){
        return true;
    }else{
        return false;
    }
  }

  /**
   * public function to check franchise invoice is already existed or not
   * @param: $data Array
   * @return Boolen
   * @author: Nishu, Dec 2017
  */
  public function checkFranchiseInvoice($data){
    $sql = "SELECT * 
             FROM oc_franchise_invoice
             WHERE 
               order_id ='". (int)$data['order_id'] ."'
               AND suborder_id = '". $this->db->escape($data['suborder_id']) ."'
               AND franchise_id = '". (int)$data['franchise_id'] ."' ";
    $result = $this->db->query($sql);
    if($result->num_rows > 0 ){
        return false;
    }else{
        return true;
    }
  }

  /**
   * Public function to generate franchise invoice number
   *@param: $invoice_prefix String
   *@return: $invoice_no Integer
   *@author: Nishu Dec 2017
  */
  public function createFranchiseInvoiceNo($invoice_prefix){
    $sql  = "SELECT max(invoice_no) as invoice_no ";
    $sql .=        "FROM " . DB_PREFIX . "franchise_invoice ";
    $sql .=        "WHERE invoice_prefix = '".$this->db->escape($invoice_prefix)."'";
    $query = $this->db->query($sql);
    if(!empty($query->row['invoice_no'])){
        $invoice_no = $query->row['invoice_no'] + 1;
    }else{
        $invoice_no = 1;
    }
    return $invoice_no;
  }

}
