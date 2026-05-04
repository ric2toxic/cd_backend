<?php
  /**
 * Main Class for getting all Seller Related Info.
 * Majority of functions here will be static.
 * Major usage: getSellerInfo method
 * It is recommended to utilize the getSellerInfo method to create
 * specfic methods in this class fetching a particular data.
 * Specific selectors can be made and stored in this class as well.checkIfSellerCanListTaxableProducts
 * @Author Vikas, 2017
 */
class SellerInfo {
  /**
   * Default Selector
   * Using this we can specify tablewise fields to select, and sorting rules to apply.
   * array - ( [table_name without DB_PREFIX] => array ( 'select' => array (<fields>)  OR <field>
   *                                                   )
   *         )
   * @note: This is a default selector. 
   * @note: 'select' key can have a single field OR array of fields.
   * @author Madhur
   */
  private static $_default_selector = array( 'select'  => array() );

  /**
   * General method to get Seller details given seller_id
   * Public static method. No need to create Class object to call this method.
   * @params:
   * $db - Database object (required)
   * $seller_id - Seller ID (int, required)
     * $selector - Selector array for fields to select from oc_ms_seller table.
   *              Defaults to '', meaning $_default_selector of this table is used.
   *
   * @return:
   * array( <seller_id> => array(<field> => <value>,
   *                            ),
   *      )
   * @author Vikas
   */
  public static function getSellerInfo($db, $seller_id, $selector='') {

    // Flag to determine, if we need the data from customer table also
    // eg: telephone, email, etc.
    // 0 means only ms_seller, 1 means ms_seller + oc_customer, 2 means oc_customer only
    $get_from_customer_table = 0;

    // Using default selector, if no selector provided
    if ( empty($selector) )
      $selector = self::$_default_selector;

    // Initializing return variable
    $seller_info[$seller_id] = array();
    
    // Looping over the selector
      $sql = "SELECT ";

      // Checking if 'select' rules are defined
      if ( !empty($selector['select']) ) {

        if ( is_array($selector['select']) ) {

          $selector['select'] = array_unique($selector['select']);
          
          if ( in_array('email', $selector['select']) 
            || in_array('telephone', $selector['select']) ) {

              $get_from_customer_table = 1;

              foreach ( $selector['select'] as $key => $field ) {
                if ($field == 'email' || $field == 'telephone') {
                  $selector['select'][$key] = 'c.'.$field;
                } else {
                  $selector['select'][$key] = 'ms.'.$field;
                }
              }
          }
          
          $sql .= implode(", ", $selector['select']);

        } else { // Only single select field specified
            if ($selector['select'] == 'email' || $selector['select'] == 'telephone') {
              $get_from_customer_table = 2;
              $selector['select'] = 'c.'.$selector['select'];
            } else {
              $selector['select'] = 'ms.'.$selector['select'];
            }

            $sql .= $selector['select'];
        }

      } else { // all fields to be obtained
        $sql .= "*";
      }

      $sql .= " FROM ";

      if ($get_from_customer_table === 0 || $get_from_customer_table === 1) {
        $sql .=  DB_PREFIX . "ms_seller ms "; 
        if ($get_from_customer_table === 1) {
          $sql .= " INNER JOIN " . DB_PREFIX . "customer c ON c.customer_id = ms.seller_id ";
        }
        $sql .= " WHERE ms.seller_id = '" . (int)$seller_id . "'";
      }
      else {
        $sql .=  DB_PREFIX ." customer c ";
        $sql .= " WHERE c.customer_id = '" . (int)$seller_id . "'"; 
      }
      
      // Executing SQL query
      $query = $db->query($sql);

      if ( $query->num_rows ) {
		
		// Sending 'seller_id' also in result array
		$seller_info[$seller_id]['seller_id'] = (int)$seller_id;
		
        foreach ($query->row as $field => $value) {
          $seller_info[$seller_id][$field] = $value;
        }
      } // close if ($query->num_rows)

    return $seller_info ;

  } // close getSellerInfo function


  /**
  * Method to get Seller all detail with customer table , url_alias...
  * @param $seller_id : Interger of seller id
  * @return All details from diffs. tables
  * @author vikas, 2017
  */
  public static function getSellerDetails($db, $seller_id) {
    $selector = array('select' => array());
    $result = self::getSellerInfo($db, $seller_id, $selector);

    //get information from oc_customer with wsb seller store and customer additional email
    $customer_query = $db->query("SELECT c.firstname,
                                         c.lastname,
                                         c.telephone,
                                         c.ws_gcm_registration_id,
                                         wss.seller_markup_over_tp,
                                         wss.wsb_commission_over_tp,
                                         (SELECT keyword FROM `oc_url_alias` WHERE query = 'seller_id=".$seller_id."' ) as seller_seokeyword
                                  FROM " . DB_PREFIX . "customer c
                                  LEFT JOIN ". DB_PREFIX ."wsb_seller_to_store wss
                                    ON (wss.seller_id = c.customer_id)
                                  WHERE customer_id=".(int)$seller_id);

    $additional_emails = $db->query("SELECT email FROM ". DB_PREFIX ."customer_additional_email WHERE customer_id = ". $seller_id);
    $customer_query->row['additional_email'] = implode(',',array_column($additional_emails->rows,'email'));
    
    $result[$seller_id] = array_merge($result[$seller_id],$customer_query->row);

    if ( !empty($result) ) {
      return $result;
    } else {
      return 0;
    }
  }


  /**
  * Method to check customer is seller or not.
  * @param $db : 
  * @param $seller_id : Interger of customer id
  * @return true, or false
  * @author vikas, 2017
  */
  public static function isCustomerSeller($db, $seller_id) {

    $selector = array('select' => 'seller_id');
    $result = self::getSellerInfo($db, $seller_id, $selector);
    if ( !empty($result[$seller_id]) ) {
      return true;
    } else {
      return false;
    }
  }

  /**
  * Method to get Seller Status.
  * @param $db : 
  * @param $seller_id : Interger of seller id
  * @return integer for seller_status
  * @author vikas, 2017
  */
  public static function getSellerStatus($db, $seller_id) {

    $selector = array('select' => 'seller_status');
    $result = self::getSellerInfo($db, $seller_id, $selector);
    if ( !empty($result[$seller_id]) ) {
      return $result[$seller_id]['seller_status'];
    } else {
      return 0;
    }
  }

  /**
  * Method to get Seller nickname & company (firmname) and gst number of the seller.
  * @param $seller_id : Interger of seller id
  * @return seller nickname, company
  * @author vikas, 2017
  */
  public static function getSellerFirmDetails($db, $seller_id) {

    $selector = array('select' => array('nickname','company','gst_provisional_id'));
    $result = self::getSellerInfo($db, $seller_id, $selector);
    if ( !empty($result) ) {
      return $result[$seller_id];
    } else {
      return 0;
    }
  }

  /**
  * Method to check seller invoice generated
  * @param $db : 
  * @param $seller_id : Interger of seller id
  * @return true or false
  * @author vikas, 2017
  */
  public static function checkSellerInvoiceToBeGenerated($db, $seller_id) {

    $selector = array('select' => 'seller_invoice_generate');
    $result = self::getSellerInfo($db, $seller_id, $selector);
    if ( !empty($result[$seller_id]['seller_invoice_generate']) ) {
      return $result[$seller_id]['seller_invoice_generate'];
    } else {
      return false;
    }
  }

  /**
  * Method to check seller can list taxable product
  * @param $db : 
  * @param $seller_id : Interger of seller id
  * @return true or false
  * @author vikas, 2017
  */
  public static function checkIfSellerCanListTaxableProducts($db, $seller_id) {

    $selector = array('select' => array('tin','tin_tax_type'));
    $result = self::getSellerInfo($db, $seller_id, $selector);

    $tin = $result[$seller_id]['tin'];
    $tin_tax_type = explode(',', $result[$seller_id]['tin_tax_type']);
    
	//updated code to handle composite case as well
    if( !empty($tin) && ( in_array(2, $tin_tax_type))){
	  //taxable seller
      return 2;
    } else if( !empty($tin) && in_array(3, $tin_tax_type)) {
	  //composite seller
      return 3;
    } else {
		//if seller is tax free
		return 1;
	}
  }

    /**
     * Method to check non returnable
     * @param $db : Database Object
     * @param $seller_id : Interger of seller id
     * @return true or false
     * @author vikas, 2017
     */
    public static function checkNonReturnableSeller($db, $seller_id) {

        $selector = array('select' => array('non_returnable'));
        $result = self::getSellerInfo($db, $seller_id, $selector);
        if( $result[$seller_id]['non_returnable'] ){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Method to check non returnable
     * @param $db : Database Object
     * @param $seller_id : Interger of seller id
     * @param $additional_emails: Optional (default: false). Set to true to get CC emails for the seller
     * @return array of emails
     * @author Madhur, 2017
     */
    public static function getSellerEmails($db, $seller_id, $additional_emails=false) {
        
        // return array
        $emails = array();
        
        // Getting primary email
        $sql = "SELECT email 
                FROM " . DB_PREFIX . "customer 
                WHERE customer_id = '" . (int)$seller_id . "'";
        $query = $db->query($sql);
        if (!empty($query->row['email'])) {
            $emails[] = $query->row['email'];
        }
        
        // Getting additional emails
        if ( $additional_emails ) {
            $sql = "SELECT email 
                    FROM " . DB_PREFIX . "customer_additional_email  
                    WHERE customer_id = '" . (int)$seller_id . "'";
            $query = $db->query($sql);
            if ( $query->num_rows ) {
                foreach ($query->rows as $row) {
                    if ( !empty($row['email']) ) {
                        $emails[] = $row['email'];
                    }
                }
            }
        }
        
        return $emails;
    }
    /**
    * Method to get Seller zone_id & pickup_city_code
    * @param $seller_id : Interger of seller id
    * @return seller zone_id, pickup_city_code
    * @author kalyan, 8 Sept. 2017
    */
    public static function getSellerZoneIdAndPickupCityCode($db, $seller_id) {

      $selector = array('select' => array('zone_id','pickup_city_code'));
      $result = self::getSellerInfo($db, $seller_id, $selector);
      if ( !empty($result) ) {
        return $result[$seller_id];
      } else {
        return 0;
      }
    }
    /**
     * Method to get seller nickname based on sku
     * @param $db : Database Object
     * @param $product_sku : string     
     * @return array of emails
     * @author Kalyan 12th Oct, 2017
     */
    public static function getSellerNicknameBasedOnSku($db, $productSku) {
                
        
        $sql = "SELECT p.product_id,mss.nickname 
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "ms_product mp
                ON p.product_id=mp.product_id
                INNER JOIN " . DB_PREFIX . "ms_seller mss
                ON mp.seller_id=mss.seller_id
                WHERE p.sku = '" . $db->escape($productSku) . "'";
        $query = $db->query($sql);

        if ($query->num_rows>0) {
          return $query->row;
        }else{
          return 0;
        }
        
    }

    /**
     * Method to get seller nickname based on sku
     * @param $db : Database Object
     * @param $seller_ids : array     
     * @return array of emails
     * @author MSA march 2018
     */
    public static function getSellersBySellerIds($db, $seller_ids) {
        if(empty($seller_ids)) {return 0;}        
        $sql = "SELECT
                      ocs.seller_id, 
                      ocs.nickname,
                      ocs.company,
                      ocs.email,
                      occ.ws_gcm_registration_id,
                      GROUP_CONCAT( DISTINCT occam.email ) as additional_emails
                FROM " . DB_PREFIX . "ms_seller ocs
                INNER JOIN " . DB_PREFIX . "customer occ ON occ.customer_id = ocs.seller_id
                LEFT JOIN oc_customer_additional_email occam ON occam.customer_id = occ.customer_id
                WHERE ocs.seller_id IN (" . implode(',', $seller_ids) . ")
                GROUP BY 
                    ocs.seller_id,
                    occ.customer_id
                ORDER BY NULL
                ";
        $query = $db->query($sql);

        if ($query->num_rows>0) {
          $seller = array();
          foreach ($query->rows as $key => $value) {
              $seller[$value['seller_id']] = $value;
              if(!empty($value['additional_emails'])) {
                $seller[$value['seller_id']]['additional_emails'] = explode(",", $value['additional_emails']);
              }else{
                $seller[$value['seller_id']]['additional_emails'] = array($value['email']);
              }
          }
          return $seller;
        }else{
          return 0;
        }
        
    }



 /**
  * Method to get Seller seller_invoice_generate & nickname.
  * @param $seller_id : Interger of seller id
  * @return seller seller_invoice_generate, nickname
  * @author kalyan 25th Nov 2017
  */
  public static function getSellerInvoiceGenerateStatusAndNickname($db, $seller_id) {

    $selector = array('select' => array('nickname','seller_invoice_generate'));
    $result   = self::getSellerInfo($db, $seller_id, $selector);
    
    if ( !empty($result) ) {
      
      return $result[$seller_id];
    } else {
      
      return 0;
    }
  }

  /**
   * @info: Public method to get BD's email Ids from seller 
   * @param: $seller_id, $nickname, $pickup_city_code
   * @return: Array EmailIds
   * @author: Nishu, March 2018
  */
  public static function getBdEmailIdsBySeller($db, $seller_id, $nickname='', $pickup_city_code =''){
    $email_arr        = array();
    
    if(empty($pickup_city_code) && empty($nickname)){
      $selector       = array('select' => array('nickname','pickup_city_code'));
      $seller_details = self::getSellerInfo($db, $seller_id, $selector);
      if(!empty($seller_details)){
        $nickname = $seller_details[$seller_id]['nickname'];
        $pickup_city_code = $seller_details[$seller_id]['pickup_city_code'];
      }
    }else if(!empty($pickup_city_code) && $pickup_city_code == 'DL' && empty($nickname)){
      $selector       = array('select' => array('nickname','pickup_city_code'));
      $seller_details = self::getSellerInfo($db, $seller_id, $selector);
      if(!empty($seller_details)){
        $nickname = $seller_details[$seller_id]['nickname'];
        $pickup_city_code = $seller_details[$seller_id]['pickup_city_code'];
      }
    }
    //Check If pickup city code is for Agra
    if($pickup_city_code == 'DL'){
      $sub_str = substr($nickname , 0, 2);
      if(strtolower($sub_str) == 'ag'){
        $pickup_city_code = 'AG_DL';
      }
    }

    //Check BD Email Ids set for Seller's pickup city code
    if(
      isset(EMAIL_IDS['BD'][$pickup_city_code])
          &&
      !empty(EMAIL_IDS['BD'][$pickup_city_code])
    ){
      $email_arr = EMAIL_IDS['BD'][$pickup_city_code];
    }

    if(empty($email_arr)){ //If there is not email id for Seller'spickup city code, Set default EmailId
      $email_arr =  array(
                      '0' => EMAIL_IDS['sellers']
                    );
    }

    return $email_arr;  //Return array of emial Ids.
  }
  /**
  * Method to product is web or not
  * @param $db : Database Object
  * @param $product_id : int     
  * @return true/false
  * @author Kalyan 17th Sept, 2017
  */
    public static function checkProductIsWsb($db, int $product_id) : bool {                
        
        $sql = "SELECT mp.product_id 
                FROM " . DB_PREFIX . "ms_product mp 
                INNER JOIN " . DB_PREFIX . "ms_seller mss
                ON mp.seller_id=mss.seller_id
                WHERE mp.product_id = '" .  (int)$product_id . "' 
                  AND mss.seller_invoice_generate='0' 
                LIMIT 1";
        $query = $db->query($sql);

        if ($query->num_rows>0) {
          return true;
        }else{
          return false;
        }        
    }  
  /**
   * 
   * @param Database\DB $db object
   * @param array $seller_id_arr - array of seller ids 
   * @return seller products
   * @author Nilesh, 2018
   */
  public static function getSellerProducts(Database\DB $db, array $seller_id_arr): array {
        $result = array();
        $sql = "SELECT seller_id,
                       product_id
                FROM " . DB_PREFIX . "ms_product
                WHERE seller_id IN (" . implode(',', $seller_id_arr) . ")";
        $query = $db->query($sql);
        if ($query->num_rows) {
            foreach ($query->rows as $value) {
                $result[$value['seller_id']][] = $value['product_id'];
            }
        }
        return $result;
    }

    /**
     * Get Seller By Nickname
     * @param Database\DB $db object
     * @param string $nickname
     * @return seller detail
     * @author MSA, June 2019
     */
    public static function getSellerByNickname(Database\DB $db, string $nickname): array
    {
        $seller_info = array();
        if(empty($nickname)) {return $seller_info;}   
        $sql = "SELECT
                      seller_id, 
                      nickname,
                      company
                FROM 
                  " . DB_PREFIX . "ms_seller 
                WHERE 
                    nickname = '".$db->escape($nickname)."' 
                ";
        $query = $db->query($sql);
        if ($query->num_rows>0) {
          $seller_info = $query->row;
        }
        return $seller_info;
    }

    /**
     * Get Sellers data 
     * @param: Database\DB $db object
     * @param: array $seller_ids
     * @return: array 
     * @author: MSA, Oct 2019
     */
    public static function getSellersData(Database\DB $db, array $seller_ids)
    {
      $sellers = array();

      $seller_ids = array_unique(array_filter(array_map('intval', $seller_ids), function($v) {return $v > 0;}));

      if(empty($seller_ids)){ return $sellers; }

      $seller_ids = implode(',', $seller_ids);

      $sql = "SELECT 
                  seller_id,
                  nickname,
                  seller_invoice_generate 
              FROM ". DB_PREFIX . "ms_seller ms 
              WHERE
                seller_id IN (".$db->escape($seller_ids).")
              "; 

      $query = $db->query($sql);

      if ($query->num_rows>0) {

         $sellers = array_combine(
                                  array_column($query->rows, 'seller_id'),
                                  $query->rows
                                );
      }
        return $sellers;
    }


}
