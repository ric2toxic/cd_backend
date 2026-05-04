<?php
declare(strict_types=1);

require_once( DIR_SYSTEM . 'library/operations/orders/order_info.php' );

/**
 * Main Class for getting Customer Membership related data
 * @author Nishu, 2018
 */
class Membership {

  private $_registry      = null;
  private $_db            = null;
  private $_config        = null;
  private $_membership_id = 0;
  private $_master_id     = 0;
  private $_total_purchase= 0.00;

  public function __construct($controller) {
    if (method_exists($controller, 'get')) {
      $this->_registry = $controller;
      $this->_db       = $controller->get('db');
      $this->_config   = $controller->get('config');
      $this->_currency = $controller->get('currency');
    } else {
      $this->_registry = $controller;
      $this->_db       = $controller->db;
      $this->_config   = $controller->config;
      $this->_currency = $controller->currency;
    }
  }

  /**
   * Public method which gives membership details for given customer ids 
   *    If these customer ids belongs to multiple master_ids, throw alert
   * @param:  String $customer_ids , Comma separate ids
   * @return: Array $data
   * @author: Nishu, Sept 2018
  */
  public function getCustomerMembershipInfo(string $customer_ids) : array{
    $data = array();

    if( !empty($customer_ids) ){
      $all_master_ids = Customer::getMasterIdsByCustomerIds($this->_db, $customer_ids);
      
      //If there are multiple master_ids for given customer_ids
      if( count($all_master_ids) > 1 ){
        //Send alert for unusal case for multiple master_ids
        //$this->sendAlertForMultipleMasterIds($customer_ids, $all_master_ids);
      }
      $master_id        = (int)$all_master_ids[0]['master_id'] ?? 0;
      $this->_master_id = (int)$master_id;

      //Total Purchase value by given master_id and for given days
      // get customer purchase and return data
      $customer_purchase_return_data = Customer::getTotalPurchaseAndReturnByGivenMasterId($this->_db, $master_id, 90);
      $this->_total_purchase  = $customer_purchase_return_data['total_purchase'] - $customer_purchase_return_data['total_return'];
      $data['total_purchase'] = $this->_total_purchase;

      //Get membership info for given master_id
      $data['current_membership_info'] = $this->getMembershipInfoByCustomerIds();

      //Check, If currently no membership available
      if(!empty($data['current_membership_info'])){
        $this->_membership_id = $data['current_membership_info']['membership_id'] ?? 0;
      }

      //Get next available membership plan(s)
      $data['next_available_plans'] = $this->getAllNextLevelMemberships();
      
    }
    return $data;
  }

  /**
   * Public Method to get details membership details for given customer_ids
   * @param: void
   * @return $data Array
   * @author: Nishu, Sept 2018
  */
  public function getMembershipInfoByCustomerIds() : array{
    $data = array();
    if(!empty($this->_master_id)){
      $sql = "
            SELECT 
              master_id,
              membership_id,
              membership_name,
              membership_fees,
              purchase_value,
              return_value,
              purchase_return_months,
              target_for_free_factor,
              target_amount_for_free,
              membership_purchase_type,
              DATE(start_date) AS membership_join_date,
              DATE(expiry_date) AS membership_expiry_date,
              prepaid_discount,
              cod_discount,
              credit_discount
            FROM 
              ". DB_PREFIX ."master_customer_membership
            WHERE 
              master_id = ". (int)$this->_master_id ."
              AND status = 1
            ORDER BY 
              id DESC
            LIMIT 
              0, 1
            ";

      $result = $this->_db->query($sql);
      if($result->num_rows > 0){
        $data = $result->row;
        //Add Target for free amount text string displayable to customer
        $data['text_target_for_free'] = "Purchase goods worth ".$this->_currency->format((float)$data['target_amount_for_free'], 'INR', 1)." during membership period, and get next 6 months renewal FREE of cost!";
      }
    }

    return $data;
  }

  /**
   * Private method which gives all membership plans for next level from current plan
   * @param:  void
   * @return: Array $data
   * @author: Nishu, Sept 2018
  */
  private function getAllNextLevelMembershipsAlreadyExistingMembership() : array{
    $data = array();

    $sql = "
            SELECT
              mem1.membership_id,
              mem1.name,
              mem1.membership_fees,
              mem1.target_for_free_factor,
              mem1.prepaid_discount,
              mem1.cod_discount,
              mem1.credit_discount,
              mem1.priority_level
            FROM 
              ". DB_PREFIX ."membership_level AS mem1
            INNER JOIN 
              ". DB_PREFIX ."membership_level AS mem2 ON mem1.priority_level <= mem2.priority_level
            WHERE 
              mem1.status = 1 
              AND mem2.membership_id = ". (int)$this->_membership_id ."
            GROUP BY 
              mem1.membership_id
            ORDER BY 
              mem1.priority_level DESC
           ";
             
    $result = $this->_db->query($sql);
    if($result->num_rows > 0){
      $data = $result->rows;
      //Set membership_id set array keys
      $data = array_combine(
                           array_column($data, 'membership_id'), 
                           $data
              );
    }
    
    return $data;
  }

  /**
   * Private method which gives all membership plans for next level 
   *       //If customer not having any existing membership,
   *      //Calculate next member plans, on the basis of customer's purchase value of last 90 dyas
   * @param:  void
   * @return: Array $data
   * @author: Nishu, Sept 2018
  */
  private function getAllNextLevelMembershipsNotHavingMembership() : array{
    $data = array();
    
    $sql = "
            SELECT
              mem1.membership_id,
              mem1.name,
              mem1.membership_fees,
              mem1.target_for_free_factor,
              mem1.prepaid_discount,
              mem1.cod_discount,
              mem1.credit_discount,
              mem1.priority_level
            FROM 
              ". DB_PREFIX ."membership_level AS mem1
            ". $join ."
            WHERE 
              mem1.status = 1 
              AND ". $this->_total_purchase ." <= mem1.purchase_range_ends
            ORDER BY 
              mem1.priority_level ASC
           ";
           
    $result = $this->_db->query($sql);

    if($result->num_rows > 0){
      $data = $result->rows;
      //Set membership_id set array keys
      $data = array_combine(
                           array_column($data, 'membership_id'), 
                           $data
                          );
    } 
    return $data;
  }

  /**
   * Public method which gives all membership plans for next level
   * @param:  void
   * @return: Array $data
   * @author: Nishu, Sept 2018
  */
  public function getAllNextLevelMemberships() : array{
    $data = array();
    
    if(!empty($this->_membership_id)){
      //If Customer Already having membership
      $data = $this->getAllNextLevelMembershipsAlreadyExistingMembership();
    }else{
      //If customer not having any existing membership,
      //Calculate next member plans, on the basis of customer's purchase value of last 90 dyas
      $data = $this->getAllNextLevelMembershipsNotHavingMembership();
    }

    return $data;
  }

  /**
   * Private method to send alert mail to notify about multiple master_ids or nay dispute
   * @author: Nishu, Sept 2018
  */
  private function sendAlertForMultipleMasterIds(string $customer_ids, array $all_master_ids){
    $master_ids = array_column($all_master_ids, 'master_id');
    $body = "Hi All, <br/><br/> Here find disputed case for, find multiple master_ids for given customer_ids to get membership information.<br><br>
      Given Customer Id(s): <b>". $customer_ids ."</b><br><br>
      Multiple Mater Ids:  <b>". implode(', ', $master_ids) ."</b><br><br>
     ";
    
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $this->_config->get('config_mail_smtp_hostname');
    $mail->Port = $this->_config->get('config_mail_smtp_port');
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $this->_config->get('config_mail_smtp_username');
    $mail->Password = $this->_config->get('config_mail_smtp_password');

    $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
    $mail->addReplyTo(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
    $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
    $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
    $mail->addCC(EMAIL_IDS['ravindra']['email_id'], EMAIL_IDS['ravindra']['name']);

    $mail->Subject = "Customer Membership: Having Multiple Master Id(s)- " . date('d/M/Y H:i:s', time());
    $mail->msgHTML($body);
    
    $mail->send(1,false);
  }
  
  /* 
    * public function to update class variables 
    * @ author : Devendra, September 2018
   */
  public function setProperty($name, $value) {
      if (property_exists($this, '_' . $name)) {
          $this->{'_' . $name} = $value;
      } else {
          throw new Exception("Class MembershipApi doesn't contain $name");
      }
  }

  /**
   * Public Method to get details membership details for given customer_ids
   * @param: $master_id, $date
   * @return $data Array
   * @author: Devendra, Sept 2018
  */
  public function getMembershipInfoByMasterIdAndDate(int $master_id, string $date) : array{
    $data = array();

    $sql = "
          SELECT 
            master_id,
            membership_id,
            membership_name,
            prepaid_discount,
            cod_discount,
            credit_discount,
            status
          FROM 
            ". DB_PREFIX ."master_customer_membership
          WHERE 
            master_id = ". (int)$master_id ."
            AND '" . $date . "' BETWEEN start_date AND expiry_date
          ORDER BY 
            expiry_date DESC
          ";

    $result = $this->_db->query($sql);
    if($result->num_rows > 0){
      // this will be the result if no record with status 1 found
      $data = $result->row;
      
      foreach ($result->rows as $row) {
        if ($row['status'] == 1) {
          $data = $row;
          break;
        }
      }
    }

    return $data;
  }
  
  /**
   * Public Method to insert membership transaction details
   * @param: $master_customer_membership_id, $table_name, $table_id
   * @return membership transaction table id
   * @author: Devendra, Sept 2018
  */
  public function insertMembershipTransaction(int $master_customer_membership_id, string $table_name, int $table_id): int {
    $sql = "INSERT INTO 
              ". DB_PREFIX ."membership_transaction 
            SET 
              master_customer_membership_id = '" . $master_customer_membership_id . "',
              table_name = '" . $this->_db->escape($table_name) . "',
              table_id = '" . $table_id . "'
            ";
    if ($this->_db->query($sql)) {
      return $this->_db->getLastId();
    }
    
    return 0;
  }
  
  /**
   * Public Method to insert customer membership details
   * @param: $data
   * @return master customer membership id
   * @author: Devendra, Sept 2018
  */
  public function insertCustomerMembership(array $data): int {
    $sql = "INSERT INTO 
              ". DB_PREFIX ."master_customer_membership 
            SET 
              master_id = '" . (int)$data['master_id'] . "',
              membership_id = '" . (int)$data['membership_id'] . "',
              membership_name = '" . $this->_db->escape($data['membership_name']) . "',
              membership_fees = '" . (float)$data['membership_fees'] . "',
              prepaid_discount = '" . (float)$data['prepaid_discount'] . "',
              cod_discount = '" . (float)$data['cod_discount'] . "',
              credit_discount = '" . (float)$data['credit_discount'] . "',
              purchase_value = '" . (float)$data['purchase_value'] . "',
              return_value = '" . (float)$data['return_value'] . "',
              purchase_return_months = '" . (int)$data['purchase_return_months'] . "',
              target_for_free_factor = '" . (float)$data['target_for_free_factor'] . "',
              target_amount_for_free = '" . (float)$data['target_amount_for_free'] . "',
              membership_purchase_type = '" . $this->_db->escape($data['membership_purchase_type']) . "',
              start_date = '" . $this->_db->escape($data['start_date']) . "',
              expiry_date = '" . $this->_db->escape($data['expiry_date']) . "',
              status = '" . (int)$data['status'] . "'
            ";
    if ($this->_db->query($sql)){
      return $this->_db->getLastId();
    }
    
    return 0;
  }
  
  /**
   * Public Method to get membership level details
   * @param: membership id
   * @return membership level data
   * @author: Devendra, Sept 2018
  */
  public function getMembershipLevelDetail(int $membership_id): array {
    $sql = "SELECT 
              membership_id,
              name as membership_name,
              membership_fees,
              purchase_range_starts,
              purchase_range_ends,
              target_for_free_factor,
              prepaid_discount,
              cod_discount,
              credit_discount,
              priority_level,
              status,
              date_added,
              last_modified
            FROM
              ". DB_PREFIX ."membership_level
            WHERE 
              membership_id = '" . $membership_id . "'
            ";
    
    $query = $this->_db->query($sql);
    if ($query->num_rows) {
      return $query->row;
    }
    
    return array();
  }
  
  /**
   * Private Method to deactivate all memberships of a customer
   * @param: master id
   * @return success or failure
   * @author: Devendra, Sept 2018
  */
  private function __deactivateAllMembershipsOfCustomer(int $master_id): bool {
    $sql = "UPDATE 
              ". DB_PREFIX ."master_customer_membership
            SET
              status = 0,
              last_modified = NOW()
            WHERE 
              master_id = '" . $master_id . "' 
              AND status = 1
            ";
    
    if ($this->_db->query($sql)){
      return true;
    }
    
    return false;
  }
  
  /**
   * Public Method to add customer membership
   * @param: master id, membership id, purchase data
   * @return success or failure
   * @author: Devendra, Sept 2018
  */
  public function addCustomerMembership(int $master_id, int $membership_id, array $purchase_data): bool {
    // get the membership level data 
    $membership_data = $this->getMembershipLevelDetail($membership_id);
    // if no membership data found against the provided membership id, then return false
    if (empty($membership_data)) return false;
    
    // first need to deactivate existing membership of this customer (if any)
    $this->__deactivateAllMembershipsOfCustomer($master_id);
    
    $customer_membership_data = $membership_data;
    $customer_membership_data['master_id'] = $master_id;
    $customer_membership_data['purchase_value'] = $purchase_data['purchase_value'];
    $customer_membership_data['return_value'] = $purchase_data['return_value'];
    $customer_membership_data['purchase_return_months'] = $purchase_data['purchase_return_months'];
    $customer_membership_data['target_amount_for_free'] = $purchase_data['target_amount_for_free'];
    $customer_membership_data['membership_purchase_type'] = 'PURCHASE';
    $customer_membership_data['start_date'] = $purchase_data['start_date'];
    $customer_membership_data['expiry_date'] = $purchase_data['expiry_date'];
    $customer_membership_data['status'] = 1;
    
    $customer_membership_id = $this->insertCustomerMembership($customer_membership_data);
    
    $this->insertMembershipTransaction($customer_membership_id, $purchase_data['table_name'], $purchase_data['table_id']);
  
    return true;
  }
  
  /**
   * Public Method to get all membership levels
   * @return membership levels data
   * @author: Devendra, October 2018
  */
  public function getAllActiveMembershipLevels(): array {
    $sql = "SELECT 
              membership_id,
              name as membership_name,
              membership_fees,
              purchase_range_starts,
              purchase_range_ends,
              target_for_free_factor,
              prepaid_discount,
              cod_discount,
              credit_discount,
              priority_level,
              status,
              date_added,
              last_modified
            FROM
              ". DB_PREFIX ."membership_level
            WHERE
              status = 1 
            ORDER BY  
              priority_level
            ";
    
    $query = $this->_db->query($sql);
    if ($query->num_rows) {
      return $query->rows;
    }
    
    return array();
  }

} // close MembershipApi class
?>
