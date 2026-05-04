<?php
require_once( DIR_SYSTEM . 'library/solr/product.php' );

Class ControllerCronWebengage extends controller {
    
    public $data = array();

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry = $registry;
    }

    public function create_coupon()
    {
      $data = array();
      $this->load->model('checkout/coupon');
      $data['name']            = $this->request->get['name'];
      $data['discount']        = round($this->request->get['discount'], 2);
      $data['total']           = round($this->request->get['total'], 2);
      $data['shipping']        = $this->request->get['shipping'];
      $data['order_from']      = $this->request->get['order_from'];
      $data['date_start']      = date("Y-m-d");
      $data['date_end']        = date('Y-m-d', strtotime($data['date_start']. ' + '.$this->request->get['days'].' days'));
      $data['coupon_customer'] =  array($this->request->get['customer_id']);
      $data['type']            = 'F';
      $data['logged']          = 1;
      $data['uses_total']      = 1;
      $data['uses_customer']   = 1;
      $data['status']          = 1;

     $code_list                = $this->generate_coupon_code(20);
     foreach($code_list as $code)
     {
       if(!$this->checkCouponByCode($code))
       {
         $coupon_id = $this->generateWebengageCoupons($data);
         $data['coupon_id'] = $coupon_id;
         $data['code'] = $code;
         break;
       }

     }
      echo json_encode($data);
      exit;
    }

    public function new_added_products()
    {
      $this->load->model('catalog/product');
      $this->load->model('tool/image');

      $result = array();
      $solr = new SolrProduct($this);

      $filters = array();

      if(!empty($this->request->get['category_id']))
      {
         $filters['filter_category_id']  = $this->request->get['category_id'];
      }

      if(!empty($this->request->get['brand']))
      {
         $brand  = explode(",", $this->request->get['brand']);
         $filters['filter_filter']  = $this->getBrandFilter($brand);
      }
      
      $filters['filter_date_added']   = date('Y-m-d\TH:i:s\Z', strtotime(' -1 day'));
      $filters['limit']               = 100000;

      $results_solr = $solr->getProductFromSolr($filters);

      $i=0; 
      foreach($results_solr['products'] as $products)
      {
        
        $seller_tax_factor     = 1.0 + ( (float)$products['seller_tax'] / 100.0 );
            $commission_factor = 1.0 + ( (float)$products['commission'] / 100.0 );
        $unit_price =  ceil($commission_factor * ((float)($products['price']) / $seller_tax_factor));

        $unformatted_price = $this->tax->calculate($unit_price, $products['tax_class_id'], $this->config->get('config_tax'), $products['mrp']);

        $store_id = $this->config->get('config_store_id');

        if($store_id == INTERNATIONAL_STORE_ID)
        {
          $price = $this->currency->format($unformatted_price,'','',true,2,'');  
        } 
        else 
        {
          $price = $this->currency->format($unformatted_price,'','',true,0,'frontend');  
        }

        $result[$i]['product_id']        = $products['product_id'];
        $result[$i]['name']              = $products['name'];
        $result[$i]['price']             = $price;
        $result[$i]['unformatted_price'] = $unformatted_price;
        $result[$i]['image'] = $this->model_tool_image->resize($products['image'],332,221);
      $i++;
      }
      echo json_encode($result);
      exit;

    }

    public function credit_balance()
    {
       $obj = new WsbCreditPayment($this);  
       $this->load->model('webengage/webengage');   
       $customers = array();
       
       //order customer
       $order_sql = "SELECT
                 o.customer_id
                 from ".DB_PREFIX."order o
                 INNER JOIN ".DB_PREFIX."suborder os
                 ON o.order_id = os.order_id
                 WHERE o.stock_transfer = 0 
                 AND o.store_id IN (0,2,9)
                 AND DATE(os.date_modified) = '".date("Y-m-d")."'
                 AND (os.order_status_id IN (".ORDER_STATUS['Pending'].", ".ORDER_STATUS['Canceled'].", ".ORDER_STATUS['Failed']."))
                 group by o.customer_id"; 
         $orders = $this->db->query($order_sql); 

         foreach($orders->rows as $order)
         {
           $customers[] =  $order['customer_id'];
         }

 
         //return customer
         if(count($customers) > 0)
         {
            $con = " AND customer_id NOT IN (".implode(',',  $customers).")";
         }
         else
         {
            $con = '';
         }

         $returns_sql = "SELECT
                 customer_id
                 from ".DB_PREFIX."return
                 WHERE DATE(date_added) = '".date("Y-m-d")."'
                 ".$con."
                 group by customer_id"; 

         $returns = $this->db->query($returns_sql);

         foreach($returns->rows as $return)
         {
           $customers[] =  $return['customer_id'];
         }

         foreach($customers as $customer_id)
         {
           $credit_limit = $obj->getActiveWsbCreditLimitByCustomerIds(array(  $customer_id ));
           $total_active_credit_limit = $credit_limit[$customer_id] ?? 0;
           $credit_bal = $obj->getAvaiableCreditBalance($customer_id);
           $this->model_webengage_webengage->updateCreditApplicationCreditOnWebengage($customer_id, $total_active_credit_limit, $credit_bal);
         }
        
        echo "<pre>";
        print_r($customers);
        echo "success";
        exit;
    } 

    public function credit_balance_all_customer()
    {
       $start = $this->request->get['start'];
       $limit = $this->request->get['limit'];

       $obj = new WsbCreditPayment($this);  
       $this->load->model('webengage/webengage');   
       $customers = array();
       
       $customer_sql = "SELECT
                 customer_id
                 from ".DB_PREFIX."customer limit ".$start.", ".$limit; 

         $customers = $this->db->query($customer_sql); 

         foreach($customers->rows as $customer)
         {
           $customer_id =  $customer['customer_id'];
           $credit_limit = $obj->getActiveWsbCreditLimitByCustomerIds(array(  $customer_id ));
           $total_active_credit_limit = $credit_limit[$customer_id] ?? 0;
           $credit_bal = $obj->getAvaiableCreditBalance($customer_id);
           $this->model_webengage_webengage->updateCreditApplicationCreditOnWebengage($customer_id, $total_active_credit_limit, $credit_bal);
         }
        
        echo "<pre>";
        print_r($customers->rows);
        echo "success";
        exit;
    }

    /**
     * get coupon dictionary words from DB
     * @param  int    $coupon_count number of words needed from DB
     * @return array coupon codes
     * @author Mahaveer Choudhary 29 May 2019
     */

    private function generate_coupon_code ($coupon_count)
    {
        $coupon_codes_array = array();

        $new_coupon_codes_sql = "SELECT 
                                    UPPER( word ) AS coupon_code 
                                FROM ". DB_PREFIX ."dictionary_entries 
                                WHERE word REGEXP '^[a-zA-Z]+$'
                                    AND length( word ) IN ( 4,5 ) ORDER BY rand() LIMIT " . $coupon_count;

        $coupon_codes_result = $this->db->query( $new_coupon_codes_sql );

        if ( $coupon_codes_result->num_rows ) {
            $coupon_codes_array = array_column( $coupon_codes_result->rows, 'coupon_code' );
        }
        
        while ( count( $coupon_codes_array ) < $coupon_count ) {
            
            $remaining_count = $coupon_count - count( $coupon_codes_array );
            
            $countwise_array = array_count_values( $coupon_codes_array );
            asort( $countwise_array );
            $countwise_array = array_keys( $countwise_array );

            $coupon_codes_array = array_merge( $coupon_codes_array, array_slice( $countwise_array, 0, $remaining_count) );
        }

        shuffle( $coupon_codes_array );

        return $coupon_codes_array;
    }

    /**
     * generates web engage coupons
     * @author Mahaveer Choudhary, 27 May 2019
     */

    private function generateWebengageCoupons($data) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "coupon 
          SET name = '" . $this->db->escape($data['name']) . "', 
              code = '" . $this->db->escape($data['code']) . "', 
              discount = '" . (float)$data['discount'] . "', 
              type = '" . $this->db->escape($data['type']) . "', 
              total = '" . (float)$data['total'] . "', 
              logged = '" . (int)$data['logged'] . "', 
              shipping = '" . (int)$data['shipping'] . "', 
              date_start = '" . $this->db->escape($data['date_start']) . "', 
              date_end = '" . $this->db->escape($data['date_end']) . "', 
              uses_total = '" . (int)$data['uses_total'] . "', 
              uses_customer = '" . (int)$data['uses_customer'] . "', 
              status = '" . (int)$data['status'] . "', 
              order_from = '" . $this->db->escape($data['order_from']) . "', 
              date_added = NOW()");

        $coupon_id = $this->db->getLastId();

        if(isset($data['coupon_customer'])){
            foreach ($data['coupon_customer'] as $customer_id) 
            {
                $sql = "INSERT INTO " . DB_PREFIX . "coupon_customer 
                SET coupon_id = '" . (int)$coupon_id . "', 
                customer_id = '" . (int)$customer_id . "'";

                $this->db->query($sql);
            }
        }

        return $coupon_id;
     }

    private function checkCouponByCode($code) 
    {
      $query = $this->db->query("SELECT coupon_id FROM " . DB_PREFIX . "coupon 
        WHERE 
        code = '" . $this->db->escape($code) . "' 
        AND store_id IN (".WSB_STORES_ID .")");

      return $query->num_rows;
    } 

    private function getBrandFilter($filter_name)
    {
       $filter_name = implode("' , '", $filter_name);
       $query = $this->db->query("SELECT GROUP_CONCAT(filter_id SEPARATOR ',') as filter_id 
        FROM " . DB_PREFIX . "filter_description 
        WHERE 
        name IN ('" . $filter_name . "') 
        AND language_id = " . (int) $this->config->get('config_language_id'));
       
      return $query->row['filter_id'];
    }


    /**
     * @info: Public method to send notification to customer for NACH debit amount 
     * @param: get parameter from GET req. 'day_type', Default value: 'today'
     *            possible values: today, tomorrow 
     * @author: nishu, May 2019
    */
    public function notifyCustomerForNachDebit(){
        
      //Set apache execution time limit to infinite
      ini_set('max_execution_time', 0);

      //  Only acceptable values are today or tomorrow; Invalid values are defaulted to today
      $day_type = strtolower(trim($this->request->get['day_type'] ?? 'today'));
      $day_type = (!in_array($day_type, array('today','tomorrow')) ? 'today' : $day_type);

      $data = array();
      // Enter dummy data for regular verification of receipt
      // Dummy entry for Madhur to test notifcation
      $data[16358] = array(
                           'customer_id'       => '16358',
                           'nach_debit_date'   => date('d-M-Y', strtotime($day_type)),
                           'nach_debit_amount' => random_int(1,500),
                           'day_type'          => strtoupper($day_type) 
                          );
      // Dummy entry for Vikas Jain to test notifcation
      $data[37203] = array(
                           'customer_id'       => '37203',
                           'nach_debit_date'   => date('d-M-Y', strtotime($day_type)),
                           'nach_debit_amount' => random_int(1,500),
                           'day_type'          => strtoupper($day_type)
                          );
      
      // Preparing filters to get Schedules
      $filter_data = array();
      $filter_data['filter_order_payment_id']      = 'NULL';
      $filter_data['filter_deffered_by_customer']  = 0;
      $filter_data['filter_status']                = 'NOT_DONE';
      $filter_data['filter_nach_debit_date_from']  = date('Y-m-d', strtotime($day_type));
      $filter_data['filter_nach_debit_date_to']    = $filter_data['filter_nach_debit_date_from'];
      if ($day_type === 'today') {
          $filter_data['filter_today_active_checksum'] = 1;
      }
      
      // Get actual data
      $nach_obj = new NachBehaviour($this->registry);
      $results  = $nach_obj->getNachSchedulesWithAdditionalInfo($filter_data);
      $results  = $nach_obj->groupDataByCustomer($results);
      
      // Exit if there is no actual data to inform customers (generally holidays)
      if ( empty($results) ) {
          exit("Nothing to send!");
      }
      
      // Enter the actual data
      foreach($results as $comp_key => $value)
      {
        if ( $value['total_nach_debit_amount'] > 0 ) {
          $data[$comp_key]['customer_id']       = $value['customer_id'];
          $data[$comp_key]['nach_debit_date']   = date('d-M-Y', strtotime($value['nach_debit_date']));
          $data[$comp_key]['nach_debit_amount'] = $value['total_nach_debit_amount'];
          $data[$comp_key]['day_type']          = strtoupper($day_type);
        }
      }

      //Load Model
      $this->load->model('webengage/webengage');
      
      $response = 'no-response';
      if(!empty($data)){
        //To use WebEngage API
        $response = $this->model_webengage_webengage->notifyCustomerForNachDebit($data);
      }

      exit($response);
    }

    public function create_business_event()
    {
      $data = array();
      $data['eventName']      = 'nachDebit';
      $data['eventCategory']  = 'BUSINESS';
      $data['attributes'][] = array('attributeName'=>'Nach Debit Date', 'dataType'=>'string');
      $data['attributes'][] = array('attributeName'=>'Nach Debit Amount', 'dataType'=>'string');
      $data['attributes'][] = array('attributeName'=>'Customer Id', 'dataType'=>'string');
       $data['attributes'][] = array('attributeName'=>'Day Type', 'dataType'=>'string');
      $this->load->model('webengage/webengage');   
      $result = $this->model_webengage_webengage->createBusinessEvent($data);
      print_r($result);
      exit;
    } 

}
