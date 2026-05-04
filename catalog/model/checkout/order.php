<?php

class ModelCheckoutOrder extends Model {

    public function addOrder($data) {
        $this->event->trigger('pre.order.add', $data);

        if(!empty($data['address_telephone']))
        {
         $data['address_telephone'] = json_encode($data['address_telephone']); 
        }
        else
        {
           $data['address_telephone'] = '';
        }

        // Check if Order is from Android App or Web (Desktop / Mobile)
        if (isset($this->session->data['ORDER_FROM']) && !empty($this->session->data['ORDER_FROM'])) {
            $order_from = $this->session->data['ORDER_FROM'];
        } else if (CONFIG_IS_MOBILE == 1) {
            $order_from = 'MOBILE_WEB';
        } else {
            $order_from = 'WEB';
        }

        // determining shipping charge (if exists)
        $shipping_charge = 0;
        if (!empty($data['totals'])) {
            foreach ($data['totals'] as $total) {
                if (strtolower(trim($total['code'])) == 'shipping') {
                    $shipping_charge = $total['value'];
                    break;
                }
            }
        }

        $gst_number  = $data['gst_number'] ?? '';
        $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number  = trim($gst_number);

        $query = "INSERT INTO `" . DB_PREFIX . "order`
              SET
                store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "',
                store_url = '" . $this->db->escape($data['store_url']) . "',
                customer_id = '" . (int) $data['customer_id'] . "',
                firstname = '" . $this->db->escape($data['firstname']) . "',
                lastname = '" . $this->db->escape($data['lastname']) . "',
                email = '" . $this->db->escape($data['email']) . "',
                telephone = '" . $this->db->escape($data['telephone']) . "',
                alternate_contact_number = '" . $this->db->escape($data['address_telephone']) . "',
                custom_field = '',
                payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "',
                payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "',
                payment_company = '" . $this->db->escape($data['payment_company']) . "',
                payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "',
                payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "',
                payment_city = '" . $this->db->escape($data['payment_city']) . "',
                payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "',
                payment_country = '" . $this->db->escape($data['payment_country']) . "',
                payment_country_id = '" . (int) $data['payment_country_id'] . "',
                payment_zone = '" . $this->db->escape($data['payment_zone']) . "',
                payment_zone_id = '" . (int) $data['payment_zone_id'] . "',
                payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "',
                payment_custom_field = '',
                payment_method = '" . $this->db->escape($data['payment_method']) . "',
                payment_code = '" . $this->db->escape($data['payment_code']) . "',
                shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "',
                shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',
                shipping_company = '" . $this->db->escape($data['shipping_company']) . "',
                shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "',
                shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "',
                shipping_city = '" . $this->db->escape($data['shipping_city']) . "',
                shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "',
                shipping_country = '" . $this->db->escape($data['shipping_country']) . "',
                shipping_country_id = '" . (int) $data['shipping_country_id'] . "',
                shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "',
                shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "',
                shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "',
                shipping_custom_field = '',
                comment = '" . $this->db->escape($data['comment']) . "',
                total = '" . (float) $data['total'] . "',
                affiliate_id = '" . (int) $data['affiliate_id'] . "',
                commission = '" . (float) $data['commission'] . "',
                marketing_id = '" . (int) $data['marketing_id'] . "',
                tracking = '" . $this->db->escape($data['tracking']) . "',
                language_id = '" . (int) $data['language_id'] . "',
                currency_id = '" . (int) $data['currency_id'] . "',
                currency_code = '" . $this->db->escape($data['currency_code']) . "',
                currency_value = '" . (float) $data['currency_value'] . "',
                live_currency_conversion_rate = '" . (float) $data['currency_live_conversion_rate'] . "',
                ip = '" . $this->db->escape($data['ip']) . "',
                forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "',
                user_agent = '" . $this->db->escape($data['user_agent']) . "',
                accept_language = '" . $this->db->escape($data['accept_language']) . "',
                date_added = NOW(),
                date_modified = NOW(),
                order_from = '" . $this->db->escape($order_from) . "',
                shipping_charge = '" . (float) $shipping_charge . "',
                weight_class_id = '" . (int) $data['weight_class_id'] . "',
                store_voucher = '" . (!empty($data['coupon']) ? $this->db->escape($data['coupon']) : '') . "',
                gst_number = '" . $this->db->escape($gst_number) . "',
                gst_unregister_declared = '" . $this->db->escape($data['gst_unregister_declared']) . "',
                campaign_event_no = '" . ($_COOKIE['campaign_event_no'] ?? '') . "',
                code_version = '2.0',
                franchise_id = '" . (int)$data['franchise_id'] . "',
                franchise_margin = '" . (int) $data['franchise_margin'] . "'";

                if(!empty($this->session->data['SALES_STAFF_ID'])) {
                  $query .= ", sales_staff_id = '". (int)$this->session->data['SALES_STAFF_ID']."'";
                }

        $this->db->query($query);

        $order_id = $this->db->getLastId(); // Getting Order ID. Note Order No is different from Order ID
        // Now we get the order_no from order_id - 1 row AND from wholesalebox stores
        $yyyymm_curr = ((int) (date('Y')) * 100) + (int) (date('m'));
        $order_query = $this->db->query("SELECT `order_no` FROM `" . DB_PREFIX . "order`
                                         WHERE `order_id` < '" . (int) $order_id . "'
                                           AND store_id IN (" . WSB_STORES_ID . ")
                                         ORDER BY `order_id` DESC LIMIT 1");
        $order_no = (float) ($yyyymm_curr * 100000) + mt_rand(1000, 2000);

        if ($order_query->row) { // An order id exists before this new added one
            if ((float) ($order_query->row['order_no'])) {
                // Getting last five digits
                $prev_no = (float) (substr($order_query->row['order_no'], -5));
                $order_no = (float) ($yyyymm_curr * 100000) + $prev_no + 1;
            }
        }
        // Updating order_no
        $this->db->query("UPDATE " . DB_PREFIX . "order
                          SET order_no = '" . $this->db->escape($order_no) . "'
                          WHERE order_id = " . (int) $order_id);

        // If this order is by franchise, then we will tag this order to franchise.
        if (!empty($data['order_by_franchise'])) {
            $staff_id = 0;
            $staff = $this->db->query("SELECT staff_id FROM `" . DB_PREFIX . "sales_staff` WHERE customer_id = " . (int) $data['franchise_id'] . " ");
            if ($staff->num_rows)
                $staff_id = $staff->row['staff_id'];

            if (!empty($staff_id)) {
              $sql = "INSERT INTO `" . DB_PREFIX . "order_sales_staff`
                    SET order_id = " . (int) $order_id . ",
                     sales_staff_id = " . (int) $staff_id . ",
                      user_id = 0,
                      date_added = NOW()";
              $this->db->query($sql);
            }
        }

        // Creating Suborder, which will be same as the Main order. Splitting happens (if) later.
        // Default Suborder ID is kept as same as Order No
        $suborder_query = "INSERT INTO `" . DB_PREFIX . "suborder`
               SET   order_id        = '" . (int) $order_id . "',
                 suborder_id     = '" . $this->db->escape($order_no) . "',
                 invoice_prefix  = '" . $this->db->escape($data['invoice_prefix']) . "',
                 invoice_no      = '0',
                 shipping_method = '" . $this->db->escape($data['shipping_method']) . "',
                 shipping_code   = '" . $this->db->escape($data['shipping_code']) . "',
                 total         = '" . (float) $data['total'] . "',
                 order_status_id = ".(int)ORDER_STATUS['Missing'].",
                 date_added      = NOW(),
                 date_modified   = NOW(),
                 cform_submit    = 'no_submit',
                 refund_status   = '" . $this->db->escape($data['refund_status']) . "',
                 tracking_no   = '',
                 shipping_charge = '" . (float) $shipping_charge . "',
                 no_wsb_tape = '" . (int) $data['no_wsb_tape'] . "',
                 no_invoice_with_shipment = '" . (int) $data['no_invoice_with_shipment'] . "'";

        $this->db->query($suborder_query);

        // Products
        // Sorting by seller nickname ASC and then SKU ASC
        $pid = array();
        $sid = array();
        foreach ($data['products'] as $key => $row) {
            $snn[$key] = $row['seller_nickname'];
            $sku[$key] = $row['sku'];
        }
        array_multisort($snn, SORT_ASC, $sku, SORT_ASC, $data['products']);

        // for solr insert
        $oop_op_slr_id = array();
        $product_ids = array();
        $seller_nicknames = array();

        foreach ($data['products'] as $product) {
            //echo '<pre>'; print_r($product);
             
            $sql = "SELECT sor_days, sor_type FROM " . DB_PREFIX . "product_sor_terms
                WHERE product_id = '" . (int)$product['product_id'] . "'";
            $sor_query = $this->db->query($sql);

            if(!empty($sor_query->row['sor_days']))
            {
              if($product['piece_in_set'] > 1)
              {
                $product['name'] = 'SOR - '.$sor_query->row['sor_days'].' days - FullSetReturnOnly - '.$product['name'];
              }
              else
              {
               $product['name'] = 'SOR - '.$sor_query->row['sor_days'].' days - '.$product['name'];  
              }
            }

            $query = "INSERT INTO " . DB_PREFIX . "order_product
                      SET   order_id = '" . (int) $order_id . "',
                            suborder_id = '" . $this->db->escape($order_no) . "',
                            product_id = '" . (int) $product['product_id'] . "',
                            name = '" . $this->db->escape($product['name']) . "',
                            model = '" . $this->db->escape($product['model']) . "',
                            hsn_code = '" . $this->db->escape($product['hsn_code']) . "',
                            quantity = '" . (int) $product['quantity'] . "',
                            piece_in_set = '" . (int) $product['piece_in_set'] . "',
                            price_per_piece = '" . (float) $product['price_per_piece'] . "',
                            discount_per_piece = '" . (float) $product['discount_per_piece'] . "',
                            discount_breakup = '" . $this->db->escape($product['discount_breakup']) . "',
                            weight_per_piece = '" . (float) $product['weight_per_piece'] . "',
                            output_tax_rates = '" . $this->db->escape($product['output_tax_rates']) . "',
                            comment = '" . $this->db->escape($product['comment']) . "',
                            store_sales = '" . $this->db->escape($product['store_sales']) . "',
                            seller_id = '" . (int) $product['seller_id'] . "',
                            seller_sku = '" . $this->db->escape($product['sku']) . "',
                            transfer_price_per_piece = '" . (float) $product['transfer_price_per_piece'] . "',
                            seller_input_tax = '" . (float) $product['seller_tax'] . "',
                            store_pickup = '" . (float) $product['store_pickup'] . "',
                            seller_cst = '0',
                            unit_id = '" . (int) $product['unit_id'] . "',
                            customer_comment = '" . $this->db->escape($product['customer_comment']) . "',
                            sor_product = '" . (int)$product['sor_product'] ."',
                            wsb_purchase_id = '" . (int)$product['wsb_purchase_id'] ."',
                            franchise_id = '" . (int)$product['franchise_id'] ."',
                            notes = '".$this->db->escape($product['notes'])."',
                            is_returnable = '".(int)(!$product['non_returnable'])."'";
                            
            if (!empty($product['combo_product_id'])) {
              $query .= ", combo_product_id='" . (int)$product['combo_product_id'] . "'";
            }
             //echo '<pre>'; print_r($query); exit;
            $this->db->query($query);

            $order_product_id = $this->db->getLastId();

            //for sor terms insert
            if(!empty($sor_query->row['sor_days']))
            {
              $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_sor_terms
                                  SET order_product_id = '" . (int) $order_product_id . "',
                                      sor_type = '" . $this->db->escape($sor_query->row['sor_type']) . "',
                                      limit_days = '" . (int) $sor_query->row['sor_days'] . "'");
            }  

            //for solr insert
            $product_ids[] = $product['product_id'];
            $oop_op_slr_id[] = $order_product_id . '-' . $product['product_id'] . '-' . $product['seller_id'];
            $seller_nicknames[] = $product['seller_nickname'];
            foreach ($product['option'] as $option) {

                // Order Options are inserted to ensure proper inventory reduction takes place
                // We update comment also, and use that to identify the product level comments.
                // Order Options (unlike default Opencart are not used for displaying in backend etc.
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option
                                  SET order_id = '" . (int) $order_id . "',
                                      suborder_id = '" . $this->db->escape($order_no) . "',
                                      order_product_id = '" . (int) $order_product_id . "',
                                      product_option_id = '" . (int) $option['product_option_id'] . "',
                                      product_option_value_id = '" . (int) $option['product_option_value_id'] . "',
                                      name = '" . $this->db->escape($option['name']) . "',
                                      value = '" . $this->db->escape($option['value']) . "',
                                      type = '" . $this->db->escape($option['type']) . "'");

                $option_comment = (int) $product['piece_in_set'] > 1 ? 'Set of ' : 'Single piece of ';
                $option_comment .= $option['name'] . ": " . $option['value'] . ". ";

                $this->db->query("UPDATE " . DB_PREFIX . "order_product
                                  SET comment = '" . $this->db->escape($option_comment) . "'
                                  WHERE order_product_id = '" . (int) $order_product_id . "'");
            }
        }

        // Vouchers
        if (!empty($data['vouchers'])) {
            $this->load->model('checkout/voucher');
            foreach ($data['vouchers'] as $voucher) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher
                                  SET order_id = '" . (int) $order_id . "',
                                      description = '" . $this->db->escape($voucher['description']) . "',
                                      code = '" . $this->db->escape($voucher['code']) . "',
                                      from_name = '" . $this->db->escape($voucher['from_name']) . "',
                                      from_email = '" . $this->db->escape($voucher['from_email']) . "',
                                      to_name = '" . $this->db->escape($voucher['to_name']) . "',
                                      to_email = '" . $this->db->escape($voucher['to_email']) . "',
                                      voucher_theme_id = '" . (int) $voucher['voucher_theme_id'] . "',
                                      message = '" . $this->db->escape($voucher['message']) . "',
                                      amount = '" . (float) $voucher['amount'] . "'");
                $order_voucher_id = $this->db->getLastId();
                $voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $voucher);
                $this->db->query("UPDATE " . DB_PREFIX . "order_voucher
                                  SET voucher_id = '" . (int) $voucher_id . "'
                                  WHERE order_voucher_id = '" . (int) $order_voucher_id . "'");
            }
        }

        ////////////////
        //Add to crm  //
        ////////////////
        $lead_data = [
            'name' => $this->db->escape($data['firstname']),
            'business_name' => $this->db->escape($data['shipping_company']),
            'email' => $this->db->escape($data['email']),
            'address' => $this->db->escape($data['shipping_address_1']),
            'zip' => $this->db->escape($data['shipping_postcode']),
            'country_id' => $this->db->escape($data['shipping_country']),
            'status' => 'MISSING ORDER',
            //'is_registered' => 1 ,
            'cart' => '',
            'is_cart' => '0',
            'priority' => 1,
            'lead_exact_value' => (float) $data['total'],
            'state_id' => $this->db->escape($data['shipping_zone']),
            'city' => $this->db->escape($data['shipping_city']),
            'order_no' => $order_no,
            'ip' => $this->db->escape($data['ip'])
        ];
        //$this->load->model('lead/lead');
        // $this->model_lead_lead->updateLead($lead_data,$data['telephone'],'Missing Order' , $data['customer_id']);

        /* if( SOLR_ORDER_SYNC_QUEUE ){
          $data['products'] = $product_ids;
          $data['seller_to_product'] = $oop_op_slr_id;
          $data['sellers'] = $sellers;
          $data['order_no'] = $order_no;
          $data['seller_nicknames'] = $seller_nicknames;
          $data['order_id'] = $order_id;
          $this->addSolrIntoSolr($data);
          } */

        $this->event->trigger('post.order.add', $order_id);

        return $order_id;
    }

    private function addSolrIntoSolr($order_data) {
        $data['id'] = $order_data['order_id'];
        $data['order_no'] = $order_data['order_no'];
        $data['order_status'][] = (int)ORDER_STATUS['Missing'];
        $data['firstname'] = $order_data['firstname'];
        $data['lastname'] = $order_data['lastname'];
        $data['telephone'] = $order_data['telephone'];
        $data['email'] = $order_data['email'];
        $data['total'] = $order_data['total'];

        $data['payment_firstname'] = $order_data['payment_firstname'];
        $data['payment_lastname'] = $order_data['payment_lastname'];
        $data['payment_company'] = $order_data['payment_company'];
        $data['payment_city'] = $order_data['payment_city'];
        $data['payment_zone'] = $order_data['payment_zone'];

        $data['shipping_firstname'] = $order_data['shipping_firstname'];
        $data['shipping_lastname'] = $order_data['shipping_lastname'];
        $data['shipping_company'] = $order_data['shipping_company'];
        $data['shipping_city'] = $order_data['shipping_city'];
        $data['shipping_postcode'] = $order_data['shipping_postcode'];
        $data['shipping_zone'] = $order_data['shipping_zone'];

        $data['sales_staff_id'] = 0;
        $data['customer_id'] = $order_data['customer_id'];
        $data['products'] = $order_data['products'];
        $data['sellers'] = $order_data['sellers'];

        $data['seller_to_products'] = $order_data['seller_to_product'];

        $data['seller_nicknames'] = $order_data['seller_nicknames'];

        $data['suborders'] = array($order_data['order_no']);
        $data['store_id'] = $order_data['store_id'];
        $data['invoice_no'] = array(0);
        $data['invoice_date'] = array();
        $data['invoice_prefix'] = array();
        $solr = new SolrOrder($this);
        $solr->addSolrOrder($data);
    }

    public function editOrder($order_id, $data) {
        // function moved- this one is obsolete
    }

    public function deleteOrder($order_id) {
        // Order should never be deleted
    }

    /**
     * Following keys can be provided in $input
     * 'order_id' - required
     * 'suborder_id' - defaulted to '' (all suborders of the order will be updated same)
     * 'order_status_id' - defaulted to current order status id of the suborder
     * Do not specify the key 'order_status_id' if you want to update the new order status
     * to the old order status value. If a key is specified, but empty value is passed in, 
     * it would consider the new order status id as 0 (Missing order)
     * 'comment' - defaulted to ''
     * 'notes' - defaulted to ''
     * 'notify_email' - defaulted to false
     * 'notify_sms' - defaulted to false
     * 'give_cashback' - defaulted to true
     */
    public function addOrderHistory($input) {

      // order_id is Mandatory field
      if (empty((int)$input['order_id'])) {
          return false;
      }
      
      try{
      
        $buyer_invoice = new BuyerInvoice($this);
        $totals = array();
        if(!empty($input['suborder_id'])) {
            $totals = $buyer_invoice->getTotals($input['order_id'], $input['suborder_id']);
        }
        
        $net_payble = 0;

        if (!empty($totals['net_amount'])) {
            $net_payble = $totals['net_amount']['value'];
        }


        // Order Statuses
        $canceled              = (int)ORDER_STATUS['Canceled'];
        $out_for_delivery      = (int)ORDER_STATUS['Out for delivery'];
        $failed                = (int)ORDER_STATUS['Failed'];
        $shipped               = (int)ORDER_STATUS['Shipped'];
        $shipped_with_tracking = (int)ORDER_STATUS['Shipped with tracking'];
        $delivered             = (int)ORDER_STATUS['Delivered'];
        $delivery_issues       = (int)ORDER_STATUS['Delivery Issues'];


        $order_id      = (int)($input['order_id'] ?? 0);
        $suborder_id   = $input['suborder_id'] ?? '';
        $comment       = $input['comment'] ?? '';
        $notes         = $input['notes'] ?? '';
        $notify_email  = $input['notify_email'] ?? false;
        $notify_sms    = $input['notify_sms'] ?? false;
        $give_cashback = $input['give_cashback'] ?? true;
        $user          = $input['user'] ?? '';
        $auto_cancel   = $input['auto_cancel'] ?? false;


        // Hardcoding to english language to use
        $language_directory = 'english';
        $language = new Language($language_directory);
        $language->load($language_directory);
        $language->load('mail/order');
        $this->load->language('account/sms_templates');
        $this->load->language('mail/customer');

        // Fetching order data
        $selector = array(
                        'order'         => array(),
                        'suborder'      => array(),
                        'order_product' => array(
                                          'select'=>array(
                                                    'name',
                                                    'product_id',
                                                    'model',
                                                    'quantity',
                                                    'piece_in_set',
                                                    'order_product_id',
                                                    'seller_id',
                                                    'seller_sku',
                                                    'store_sales',
                                                    'comment',
                                                    'edit_type',
                                                    'edit_history'
                                                    )
                                            ),
                        'order_option'  => array(
                                             'select'=>array(
                                                        'order_product_id',
                                                        'product_option_value_id', 
                                                        'value as option_value'
                                                        )
                                            )
                        );
        $order_data = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        
        $order_info          = $order_data['order'];
        $suborder_info       = $order_data['suborder'];
        $old_order_status_id = (int)ORDER_STATUS['Missing'];

        if($input['order_status_id'] == $canceled) {
            
            $cancel_status = OrderInfo::checkForOrderCancelationIsApplicable($this->db, $order_id, $suborder_id);
            if(!empty($cancel_status) && !$cancel_status['status']) {
                return array('error'=> $cancel_status['error']);
            }
            /*if(!(OrderInfo::checkForOrderCancelationIsApplicable($this->db, $order_id, $suborder_id))) {
                //return array('error'=> 'Order cannot be Canceled. Because Return has not completed.');
            }*/
        }

        if ($order_info && $suborder_info) {
            $payment_mode = trim(strtolower($order_info['payment_code']));

            $this->event->trigger('pre.order.history.add', $order_id);

            // Looping over all Suborders to Change their Status
            // If suborder_id is given, then getOrderInfo should return only one suborder_id
            // Else, if suborder_id is not given, it means that this is the order placing stage,
            // so by default, there should be only one suborder

            foreach ($suborder_info as $sid => $sdata) {

                $old_order_status_id = (int)$sdata['order_status_id'];
                $order_status_id     = (int)($input['order_status_id'] ?? 0);
                $order_status_id     = ($order_status_id > 0 ? $order_status_id : $old_order_status_id);
                
                if ( $order_status_id <= 0 ) {
                    throw new \Exception("Issue in addOrderHistory: order_status_id cannot be zero!");
                }


                /**************Nilesh code section start*****************/
                //code added by Nilesh,2018 for stock updation after cancel and revival of suborder
                $order_product_info = $sdata['order_product'];
                $order_product_info = array_combine(array_column($order_product_info, 'order_product_id'), $order_product_info);
                
                $order_product_option_info = array();
                if (!empty($sdata['order_option'])) {
                    //To set data for order product option vlaue
                    $order_product_option_info = array_combine(
                                                    array_column(
                                                        $sdata['order_option'], 
                                                        'order_product_id'
                                                    ), 
                                                    $sdata['order_option']
                                                );
                   
                    //Set option value to order_product_info array
                    foreach ($order_product_info as $opid => $value) {
                        $order_product_info[$opid]['product_option_value_id'] = $order_product_option_info[$opid]['product_option_value_id'] ?? Null;
                    }
                }

                //Update product's available stock after adding order history
                if(empty($input['is_stock_update'])) {
                    $update_status = OrderEdit::updateProductStocksByOrderHistory($this->db, $old_order_status_id, $order_status_id, $order_product_info, $order_product_option_info);

                    if(!$update_status){
                        return array('error'=> "Product stock is not available for order revival.");
                    }
                }

                //Mark payment_cleared field as 'NO', when order moved cancelled state to non-cancelled state
                if(
                    $old_order_status_id == (int)ORDER_STATUS['Canceled'] &&
                    $order_status_id != (int)ORDER_STATUS['Canceled'] 
                ){
                    OrderPayment::updatePaymentClearForOrder($this, $order_id, 'NO');
                }

                /**************Nilesh code section END*****************/
                
                // If it is being delivered, we need to inform customer anyhow.
                // Delivered mark should be first time only, in a succession.
                if ($order_status_id != $old_order_status_id && 
                    $order_status_id == (int)ORDER_STATUS['Delivered'] && 
                    (int)$order_info['franchise_id'] == 0) {
                    $notify_email = 1;
                }

                // Inform to Accounts team in case an Order has been canceled but its invoice has been generated already
                if (($order_status_id == 2) && ($sdata["invoice_no"] > 0)) {
                    $subject  = "Suborder No: " . $sdata['suborder_id'] . " Canceled | Please Cancel Invoice";
                    $message  = MailTemplate::getGeneralHeader();
                    $message .= "<br>Suborder No: " . $sdata['suborder_id'] . " has been canceled. <br>However, the Buyer (Sale) invoice was already generated. \n";
                    $message .= "<br><br>Invoice no: " . $sdata['invoice_prefix'] . $sdata['invoice_no'] . " is to be marked Canceled in your Records.";
                    $message .= MailTemplate::getGeneralFooter();
                    $mail = new PHPMailer();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->isSMTP();
                    $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
                    $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
                    $mail->setFrom($this->config->get('config_email'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
                    $mail->Subject = $subject;
                    $mail->msgHTML($message);
                    $mail->send();
                }

                // Changing Order Status
                if ($old_order_status_id != 0) {
                    $sql = "UPDATE 
                                " . DB_PREFIX . "suborder
                            SET 
                                order_status_id = '" . (int)$order_status_id . "',
                                date_modified = NOW()
                            WHERE 
                                order_id = '" . (int) $order_id . "'
                                AND suborder_id = '" . $this->db->escape($sid) . "'";
                    $this->db->query($sql);
                }

                if($order_status_id != $old_order_status_id && ($order_status_id==9 || $order_status_id==16)){
                    $mail_data = array();
                    $mail_data['order_no'] = $order_info['order_no'];
                    $mail_data['date_added'] = $order_info['date_added'];
                    $mail_data['order_status_id'] = $order_status_id;
                    $mail_data['order_product'] = $sdata['order_product'];
                }

                if($order_status_id != $old_order_status_id && $order_status_id == 2 ){
                    $mail_data = array();
                    $mail_data['order_no'] = $order_info['order_no'];
                    $mail_data['date_added'] = $order_info['date_added'];
                    $mail_data['order_status_id'] = $order_status_id;
                    $mail_data['order_product'] = $sdata['order_product'];
                }

                $notes = ($give_cashback) ? $notes : "Cashback Not Given. \n" . $notes;

                /*
                 * checking process product is marked out of status by seller befoer suborder status is being processed
                 */
                $outOfStockNotes = '';
                /*
                 * this process will follow when order_status_is will be 9 or 16
                 */
                if (
                    (int)$order_status_id == (int)ORDER_STATUS['Processed'] 
                    || (int)$order_status_id == (int)ORDER_STATUS['Tentative Processed'] ) {
                    /*
                     * checking order is processed first time
                     */
                    $result = $this->checkSubOrderIsProcessingFirstTime($order_id, $suborder_id);

                    if ($result) {
                        /*

                         * get all products
                         */
                        $products = $buyer_invoice->getProductsArrayBySuborderId($order_id, $sdata['suborder_id']);
                        $orderDateAdded = $sdata['date_added'];

                        $seller_ids = array();

                        if (!empty($products)) {
                            $seller_id_arr = array_unique(array_column($products, 'seller_id'));

                            //Sending Sellers mail for first time.
                            $this->load->model('sale/order', 'admin');

                            foreach ($seller_id_arr as $seller_key => $seller_id) {
                                $this->admin_model_sale_order->sendSellerMail($order_id, $suborder_id, $seller_id);
                            }
                            foreach ($products as $key => $value) {
                                /*
                                 * check product is marked out of stock by seller for all product of sub order
                                 */
                                $datas = $this->checkProductIsMarkedOutOfStockBySeller($value['seller_id'], $value['product_id'], $orderDateAdded);

                                if (!empty($datas)) {
                                    $outOfStockNotes .= " \n" . $datas['product'] . ' is marked out of stock by ' .
                                            $datas['nickname'] . ' (seller) on ' .
                                            date('d-m-Y', strtotime($datas['modified'])) . "\n ";
                                }
                                array_push($seller_ids, $value['seller_id']);
                            }
                        }

                        // send push notification to following sellers
                        $seller_ids = array_unique($seller_ids);
                        if ( !empty($seller_ids) ) {
                            $this->sendPushNotificationToSellers($seller_ids, $order_id, $suborder_id);
                        }
                    }
                }

                // Adding to SubOrder History
                $sql = "INSERT INTO " . DB_PREFIX . "order_history
            SET `order_id` = '" . (int) $order_id . "',
              `suborder_id` = '" . $this->db->escape($sid) . "',
              `order_status_id` = '" . (int)$order_status_id . "',
              `notify_email` = '" . (int) $notify_email . "',
              `notify_sms` = '" . (int) $notify_sms . "',
              `comment` = '" . $this->db->escape($comment) . "',
              `notes` = '" . $this->db->escape($notes . $outOfStockNotes) . "',
              `date_added` = NOW(),
                            `user` = '" . $this->db->escape($user) . "'";
                $this->db->query($sql);

                /* Handling cases when Order is already placed and its status is now being changed */

                // Various SMSes to be sent in case of some specific status updates
                if ($old_order_status_id && $order_status_id && $notify_sms) {
                    $message = '';
                    $number = $order_info['telephone'];
                    if ($order_status_id == $canceled) {
                        $message = sprintf($this->language->get('on_cancellation'), $sid);
                    } elseif ($order_status_id == $out_for_delivery) {
                        $message = sprintf($this->language->get('on_arrival'), $sid);
                    } elseif ($order_status_id == $delivered) {
                        $message = sprintf($this->language->get('on_delivery'), $sid);
                    } elseif ($order_status_id == $shipped) {
                        $message = sprintf($this->language->get('on_dispatch_without_tracking'), $sid, $sdata['courier_partner']);
                    } elseif ($order_status_id == $shipped_with_tracking) {
                        $message = sprintf($this->language->get('on_dispatch_with_tracking'), $sid, $sdata['courier_partner'], $sdata['tracking_no']);
                    }

                    //net payable > 0 and payment mode = cod, check for order_status_id = 4
                    if ($net_payble > 0 && $payment_mode == 'cod' && $order_status_id == $out_for_delivery) {

                        //if subsequent delivery attempt
                        if ($old_order_status_id == $order_status_id) {

                            $message = sprintf($this->language->get('out_for_delivery_reattempt'), $sdata['suborder_id'], $this->currency->format($net_payble));
                        } elseif ($old_order_status_id != $order_status_id) {

                            //first delivery attempt
                            $message = sprintf($this->language->get('out_for_delivery_first_attempt'), $sdata['suborder_id'], $this->currency->format($net_payble));
                        }
                    }

                    if ($message) {
                        $status_message = new SMS();
                        $status_message->setMessage($message);
                        $status_message->setNumber($number);
                        $status_message->sendMessage();
                    }
                } // End SMSes on various Status Changes
                // Emails to be sent in case of some specific status updates
               if ($old_order_status_id && $order_status_id && $notify_email) {

                    $subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $sid);
                    $message = MailTemplate::getGeneralHeader();
                    $message .= '<br>'. $language->get('text_update_suborder_no') . $sid . "<br>";
                    $message .= $language->get('text_update_date_added')
                            . date($language->get('date_format_short'), strtotime($sdata['date_added'])) . "<br><br>";

                    $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status
                                                  WHERE order_status_id = '" . (int) $order_status_id . "'
                                                    AND language_id = '1'");

                    if ($order_status_query->num_rows) {
                        $message .= $language->get('text_update_order_status') . $order_status_query->row['name'] . "<br><br>";
                    }

                    if ($order_info['customer_id']) {
                        $message .= $language->get('text_update_link') . "<br>";
                        $message .= $order_info['store_url'] . 'index.php?route=account/order&order_id=' . $order_id . '&suborder_id=' . $sid . "<br><br>";
                    }

                    if ($comment) {
                        $message .= $language->get('text_update_comment') . "<br><br>";
                        $message .= strip_tags($comment) . "<br><br>";
                    }

                    $message .= $language->get('text_update_footer'). '<br>';

                    $message .= MailTemplate::getGeneralFooter();

                    // Preparing mail object
                    $mail = new PHPMailer();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->isSMTP();
                    
                    $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
                    $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']); 

                    $mail->addAddress($order_info['email']);
                    
                    $mail->Subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
                    $mail->msgHTML($message);


                    //////////////////////////////////////////////////////
                    /*     email invoice pdf to buyer (on delivered mark)   */
                    //////////////////////////////////////////////////////

                    if ($order_status_id == $delivered) {
                        $file_name = array();
                        $file_name['order_id'] = $order_id;
                        $file_name['suborder_id'] = $sid;
                        $file_name = base64_encode(serialize($file_name));
                        $buyer_invoice = new BuyerInvoice($this->registry, $file_name);
                        $buyer_invoice->setOptions('get_full_path', TRUE);
                        $invoice_pdf_file = base64_decode($buyer_invoice->getFile()); // true flag returns the full path for attachment
                        if (!empty($invoice_pdf_file))
                            $mail->addAttachment($invoice_pdf_file);
                    }

                    // Send the mail
                    $mail->send();
                }
                /*
                * update stock quantity when order is stock transfer
                */
                if ( $order_status_id == ORDER_STATUS['Delivered'] ){

                  // This should be the first time when Delivered is being marked against this Suborder
                    $check_delivery_query = $this->db->query("SELECT  
                                                                COUNT(*) as count_delivered,
                                                                MIN(date_added) as delivered_date
                                                              FROM " . DB_PREFIX . "order_history
                                          WHERE order_id = '" . (int)$order_id . "'
                                            AND suborder_id = '" . $this->db->escape($sid) . "'
                                            AND order_status_id = '" . (int)ORDER_STATUS['Delivered'] . "'");

                  if ($check_delivery_query->row['count_delivered'] ==1 ) {

                    $delivered_date = $check_delivery_query->row['delivered_date'] ?? NULL;
                    //Update first mark suborder as deliver, date update in oc_suborder
                    Suborder::updateDeliveredDateIntoSuborder($this->db, $sid, $delivered_date);

                    $this->load->model('tool/image');
                    $this->load->model('catalog/product', 'admin');
                   /*
                    * update product quantity when it is a stock transfer order
                    * and it will be processed first time only when delivered marked first time
                    *
                    */
                    $customer_id = $order_info['customer_id'];
                    /*
                    * check order is related to stock transfer
                    */
                    $transfer_status = 0;
                    $transfer_status = $this->checkOrderIsRelatedToStockTransfer($customer_id, $order_id);

                    if ($transfer_status) {

                      /*
                      * get stock transfer products
                      */
                      $send_mail_data   = array();
                      $products_detail  = array();
                      $all_product_ids  = '';
                      $i                = 0;

                      foreach ($suborder_info as $key => $valuesb) {

                        foreach ($valuesb['order_product'] as $keyop => $valueop) {

                            $seller_id    = $valueop['seller_id'];
                            /*
                            * update stock quantity
                            */
                            $this->updateStockTransferProductQuantity($valueop['product_id'], $valueop['quantity']);

                            $all_product_ids .= $valueop['product_id'].',';

                            /*
                            * get the product options if any
                            */
                            $product_option = $this->_getOrderProductOption($valueop['order_product_id']);

                            if(!empty($product_option)){

                              $product_option_value_id = $product_option['product_option_value_id'];

                            } else {

                              $product_option_value_id = 0;
                            }
                            /*
                            * update product options quantity
                            */
                            if ($product_option_value_id) {

                                $this->admin_model_catalog_product->updateProductOptionQuantity($valueop['product_id'],$product_option_value_id,$valueop['quantity']);
                            }

                            $products_detail[$i]['product_id']         = $valueop['product_id'];
                            $products_detail[$i]['qty_added']          = $valueop['quantity'];
                            $products_detail[$i]['product_title']      = $valueop['name'];
                            $products_detail[$i]['sku']                = $valueop['seller_sku'];

                            if(!empty($product_option)){

                                $products_detail[$i]['piece_in_set']       = $product_option['piece_in_set'];
                                $products_detail[$i]['total_pieces']       = ($product_option['piece_in_set']*($product_option['quantity']+$valueop['quantity']));
                                $products_detail[$i]['new_stock']          = $product_option['quantity']+$valueop['quantity'];
                                $products_detail[$i]['option_name']        = $product_option['name'];
                                $products_detail[$i]['option_sub_name']    = $product_option['value'];
                                $products_detail[$i]['image']              = $this->model_tool_image->resize( $product_option['image'], 100, 150);
                            }else{

                                $get_product_data = array();
                                $get_product_data = $this->admin_model_catalog_product->getProduct($valueop['product_id']);
                                $products_detail[$i]['piece_in_set']       = $get_product_data['piece_in_set'];
                                $products_detail[$i]['total_pieces']       = ($get_product_data['piece_in_set'] * $get_product_data['quantity']);
                                $products_detail[$i]['new_stock']          = $get_product_data['quantity'];
                                $products_detail[$i]['option_name']        = '';
                                $products_detail[$i]['option_sub_name']    = '';
                                $products_detail[$i]['image']              = $this->model_tool_image->resize( $get_product_data['image'], 100, 150);
                            }
                           /*
                           * add record for notification
                           */
                           $sql_notification = "INSERT INTO " . DB_PREFIX . "product_notification (
                                                `seller_id`,
                                                `product_id`,
                                                `operation_type`
                                                ) VALUES (
                                                '".(int)$customer_id."',
                                                '".(int)$valueop['product_id']."',
                                                'STOCK_TRANSFER'
                                                )";

                           $this->db->query($sql_notification);

                            $i++;
                        }
                      }

                      $all_product_ids = rtrim($all_product_ids,',');

                      $seller_selector = array('select' => array('email','telephone','pickup_city_code','company'));
                      $seller_data = SellerInfo::getSellerInfo($this->db,$seller_id, $seller_selector);

                      /*
                      * send mail to store manager
                      */
                      $send_mail_data['products_detail']  = $products_detail;
                      $send_mail_data['seller_data']      = current($seller_data);

                      $this->upgradedStockReportMailToStoreManager($send_mail_data);

                    }
                  }
                }

        /////////////////////////////////////////////////////////////////////////////
        /*             CASHBACK CODE ON DELIVERED STATUS UDPATE                    */
        /////////////////////////////////////////////////////////////////////////////
        if ($order_status_id == $delivered && !$this->isCashbackGivenForSuborder($order_id, $sid)) {
              
          $cashback_not_to_give = (defined('CASHBACK_DISABLED_CUSTOMERS') ? CASHBACK_DISABLED_CUSTOMERS : array());  

          if ( !(in_array($order_info['customer_id'], $cashback_not_to_give))
            && $this->config->get('cashback_status') // Cashback is active
            && ((float)$order_info['total'] >= (float)($this->config->get('config_cart_limit'))) // Order total above cart limit
            && $order_info['store_id'] == 0  // Only for the Indian Default Store
            && $give_cashback
            && !$this->customer->isFranchise($order_info['customer_id'])  // Customer should not be franchise
            && empty($order_info['franchise_id']) // Should not be the order by franchise
            && (($this->config->get('cashback_app_order') && ($order_info['order_from']=='ANDROIDAPP' || $order_info['order_from']=='IOSAPP' || $order_info['order_from']=='MOBILE_WEB')) || ($this->config->get('cashback_website_order') && $order_info['order_from']=='WEB'))
            && empty($order_info['sales_staff_id']) ) { // no cashback if SALES_STAFF_ID is there (FSE is ordering for customer via CRM)

              // Computing (Subtotal - Discount) value on which we will give Cashback
              // Version Handling to be done here
              $subtotal_for_cashback = 0.0;
              if ($order_info['code_version'] == '1.0') {
                $sql = "SELECT SUM(value) AS subforcshbck
                        FROM " . DB_PREFIX . "order_total
                        WHERE order_id = '" . (int) $order_id . "'
                          AND suborder_id = '" . $this->db->escape($sid) . "'
                          AND (code = 'sub_total'
                               OR code = 'coupon'
                               OR code = 'discount'
                               OR code = 'deal_discount'
                               OR code = 'paycharge'
                               OR code = 'cashback')";
                              $subforcshbck_query = $this->db->query($sql);
                              $subtotal_for_cashback = $subforcshbck_query->num_rows ? (float) $subforcshbck_query->row['subforcshbck'] : 0.0;
                          } else { // code_version = 2.0
                              $sql = "SELECT SUM( ((price_per_piece + discount_per_piece) * piece_in_set * quantity) ) AS subforcshbck
                        FROM " . DB_PREFIX . "order_product
                        WHERE order_id = '" . (int) $order_id . "'
                          AND suborder_id = '" . $this->db->escape($sid) . "' 
                          AND buyer_invoice_id > 0";
                              $subforcshbck_query = $this->db->query($sql);
                              $subtotal_for_cashback = $subforcshbck_query->num_rows ? (float) $subforcshbck_query->row['subforcshbck'] : 0.0;
                          }

                          // Computing cashback value based on current defined cashback rate
                          $cashback = floor($subtotal_for_cashback * ((float) $this->config->get('cashback_rate') / 100.0));

                          if ($cashback > 0) {

                              $this->db->query("INSERT INTO " . DB_PREFIX . "customer_cashback
                          SET customer_id = '" . (int) $order_info['customer_id'] . "',
                              order_id = '" . (int) $order_id . "',
                              suborder_id = '" . $this->db->escape($sid) . "',
                              description = '" . $this->db->escape('SubOrder No: ' . $sid . ' - ' . $this->config->get('cashback_scheme')) . "',
                              amount = '" . (float) $cashback . "',
                              date_added = NOW(),
                              validity = '" . (int) ($this->config->get('cashback_validity')) . "',
                              amount_utilized = '0',
                              expired = '0'");

                              $validity_date = date('F j', strtotime("+" . $this->config->get('cashback_validity') . " day"));
                              $cashback_curr = html_entity_decode($this->currency->format($cashback, $order_info['currency_code'], $order_info['currency_value'], true));

                              // Get Email and SMS of the Customer -  We need to inform current phone and email of the customer
                              $sql = "SELECT telephone, email, customer_id, ws_access_token FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int) $order_info['customer_id'] . "'";
                              $customer_query = $this->db->query($sql);
                              // Send SMS to Customer
                              $support_number = 8696491521;
                              $request['customer_id'] = $customer_query->row['customer_id'];
                              $request['token'] = $customer_query->row['ws_access_token'];

                              $data_json = json_encode($request);
                              $api_url = CRM_URL . "cron/getAgentofCustomer";
                              $ch = curl_init($api_url);
                              curl_setopt($ch, CURLOPT_HEADER, 0);
                              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                  'Content-Length: ' . strlen($data_json))
                              );
                              curl_setopt($ch, CURLOPT_VERBOSE, 1);
                              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                              curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                              $result = curl_exec($ch);
                              $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                              $result = json_decode($result, true);

                              if ($result['status'] == 1) {
                                  if (!empty($result['agent'])) {
                                      $agent_number = $result['agent'];
                                      $support_number = $result['agent'];
                                  } else if (!empty($result['team_lead'])) {
                                      $support_number = $result['team_lead'];
                                  } else {
                                      $support_number = $result['support'];
                                  }
                              }

                if ( !empty($customer_query->row['telephone']) ) {
                  $message = sprintf( $this->language->get('cashback_on_delivery'),
                            $cashback_curr,
                            $sid,
                            $validity_date, '(+91) '.$support_number );
                  $status_message = new SMS();
                  $status_message->setMessage($message);
                  $status_message->setNumber($customer_query->row['telephone']);
                  $status_message->sendMessage();
                }

                // Send Email to Customer
                if ( !empty($customer_query->row['email']) ) {

                    $cashback_mail_data = array();
                    $cashback_mail_data['cashback_amount'] = $cashback_curr;
                    $cashback_mail_data['suborder_id']     = $sid;
                    $cashback_mail_data['validity_date']   = $validity_date;

                    // Loading tpl file (To get HTML for mail content)
                    $message = $this->load->view(DIR_CATALOG . 'view/theme/default/template/mail/cashback_received.tpl', $cashback_mail_data, true);

                    $mail = new PHPMailer();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->isSMTP();
                    $mail->addAddress($customer_query->row['email']);
                    $mail->setFrom($this->config->get('config_email'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
                    $mail->Subject = sprintf($this->language->get('text_cashback_subject'),
                                      $sid,
                                      date('Y-m-d'));
                    $mail->msgHTML($message);
                    $mail->send();
                }
              }
            }
        }
        
        
        ////////////////////// END CASHBACK CODE //////////////////////////

        ////////////////////// START FRANCHISE PRODUCT COPY CODE //////////////////////////
        ///----------This Block of Code shifted to Generate Buyer Invoice-----------///
          //TO check order id Delivered & this is order for franchise  then make a entry in oc_product)
          // This should be the first time when Delivered is being marked against this Suborder
          /*if ($check_delivery_query->row['count_delivered'] == 1) {
              if (($order_status_id == $delivered) && $this->customer->isFranchise($order_info['customer_id'])) {
                  // check franchise coupon
                  $this->load->model('account/customer');
                  $franchise_data = $this->model_account_customer->getFranchiseData($order_info['customer_id']);
                  if (!empty($franchise_data)) {
                      $franchise_id = $order_info['customer_id'];
                      $products = $buyer_invoice->getProductsArrayBySuborderId($suborder_info[$suborder_id]['order_id'], $suborder_info[$suborder_id]['suborder_id']);
                      if (!empty($products)) {

                         $this->load->model('catalog/product', 'admin');

                          foreach ($products as $key => $product) {



                              // get the product options if any
                              $product_option = $this->_getOrderProductOption($product['order_product_id']);

                              if(!empty($product_option)){

                                  $product_option_value_id = $product_option['product_option_value_id'];
                              } else {

                                  $product_option_value_id = 0;
                              }

                              // get the new_product_id corresponding to the current product_id and franchise_id
                              $new_product_id = $this->admin_model_catalog_product->getProductToFranchise($product['product_id'], $franchise_id);
                              if (empty($new_product_id)) {
                                  // If new_product_id empty means, we need to copy current product with the purchased quantity,
                                  // also need to map new_product_id with the current product_id for current franchise
                                  $franchise_product_data = $product;
                                  $franchise_product_data['franchise_id'] = $franchise_id;
                                  $copy_product_id = $this->admin_model_catalog_product->copyProduct($product['product_id'], $franchise_product_data, $product['quantity'], $product_option_value_id);

                                  $this->admin_model_catalog_product->insertProductToFranchise($product['product_id'], $franchise_id, $copy_product_id);

                              } else {
                                  // If new_product_id exists means product already exist for this franchise, we just need to update the quantity
                                  $new_product_quantity = $this->admin_model_catalog_product->getProductQuantity($new_product_id);

                                  $updated_quantity = (int) $product['quantity'] + (int) $new_product_quantity;

                                  $this->admin_model_catalog_product->updateNewProductQuantity($new_product_id, $updated_quantity);

                                        // check for option
                                        if ($product_option_value_id) {
                                            $this->admin_model_catalog_product->updateProductOptionQuantity($new_product_id,$product_option_value_id,$product['quantity']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }*/

                ////////////////////// END FRANCHISE PRODUCT COPY CODE //////////////////////////

                // If new order status is Failed , trigger COD failed email to sellers
                if ($order_status_id == $failed) {
                    $this->sendCODFailMail($order_id, $sid, $order_info['order_no']);
                }

                // Informing internally, if an Order has CANCELED or FAILED
                if ($order_status_id == $failed || $order_status_id == $canceled || $order_status_id == $delivery_issues) {
                    $mail_subject = "";
                    if ($order_status_id == $failed) {
                        $mail_subject .= "Attention: COD has FAILED for Suborder No: " . $sid . ". ";
                    } elseif ($order_status_id == $canceled) {
                        //*********************************************************************//
                        //              Order Cancelled(Only when an order has been either
                        //              PROCESSED OR TENTATIVE PROCESSED once ,
                        //              then cancellation email will go to seller)
                        //********************************************************************//
                        $once_cancelled_sql = "SELECT oh.order_id,
                                                      oh.suborder_id,
                                                      SUM(case when((order_status_id =9 || order_status_id =16)) then 1 else 0 end) as process,
                                                      SUM(case when(order_status_id =2) then 1 else 0 end) as cancel
                                               FROM " . DB_PREFIX . "order_history oh
                                               WHERE oh.suborder_id='" . $this->db->escape($suborder_id) . "'
                                               GROUP BY suborder_id
                                               HAVING cancel = 1  AND process >=1 ";

                        $once_cancelled_sql = $this->db->query($once_cancelled_sql);

                        if ($once_cancelled_sql->num_rows) {
                            $products_data = $this->OrderCancelledProductData($once_cancelled_sql);

                            foreach ($products_data as $seller_id_key => $order_products_data) {
                                $html = '';
                                $selector = array('select' => array('nickname', 'email'));
                                $seller_info = SellerInfo::getSellerInfo($this->db, $seller_id_key, $selector)[$seller_id_key];
                                $html = MailTemplate::mailOrderCancelledToSellers($this, $seller_info['nickname'], $order_info['order_no'], $order_products_data);

                                $mail = new PHPMailer();
                                $mail->isSMTP();
                                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                                $mail->Port = $this->config->get('config_mail_smtp_port');
                                $mail->SMTPSecure = 'ssl';
                                $mail->SMTPAuth = true;
                                $mail->Username = $this->config->get('config_mail_smtp_username');
                                $mail->Password = $this->config->get('config_mail_smtp_password');
                                $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
                                $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

                                $mail->addAddress($seller_info['email'], $seller_info['nickname']);

                                $mail->Subject = 'Wholesalebox - Order No: ' . $order_info['order_no'] . ' - Cancelled Order';
                                $mail->msgHTML($html);
                                $mail->send();
                            }
                        }
                        //****************************Order Cancelled End*********************************************/
                        $mail_subject .= "Attention: Suborder No: " . $sid . " has been CANCELED. ";
                        $cn_obj = new CreditNote($this);
                        $cn_obj->cancelAllCNsBySuborderId($order_id, $sid);
                    } else {
                        $mail_subject .= "Attention: DELIVERY ISSUES - Suborder No: " . $sid;
                    }

                    $mail_body   = MailTemplate::getGeneralHeader();

                    $mail_body .= $mail_subject;
                    $mail_body .= "<br><br>";
                    $mail_body .= "Order Date: " . date('d-M-Y', strtotime($sdata['date_added'])) . "<br>";
                    $mail_body .= "Client Name: " . trim($order_info['payment_firstname'] . " " . $order_info['payment_lastname']) . "<br>";
                    $mail_body .= "Company Name: " . trim($order_info['payment_company']) . "<br>";
                    $sales_person['name'] = "N/A";
                    if (!empty($order_id)) {
                        $sales_person = $this->_getSalesPersonName($order_id);
                    }
                    $mail_body .= "Salesperson: <b>" . trim($sales_person['name']) . "</b><br>";
                    $mail_body .= "City: " . trim($order_info['payment_city']) . "<br>";
                    $mail_body .= "Suborder Value: " . $this->currency->format($sdata['total'], $order_info['currency_code'], $order_info['currency_value']) . "<br>";
                    $mail_body .= "Payment Method: " . trim($order_info['payment_code']) . "<br>";
                    $mail_body .= "Comments: " . trim($comment) . "<br>";
                    $mail_body .= "Internal Notes: " . trim($notes) . "<br>";
                    $mail_body .= "courier_partner:  " . $sdata['courier_partner'] . "<br>";
                    $mail_body .= "tracking_no:  " . $sdata['tracking_no'] . "<br>";

                    $mail = new PHPMailer();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->isSMTP();
                    $mail->addAddress(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
                    $mail->addAddress(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);

                    if (!empty($sales_person['crm_user_id'])) {
                        $this->load->model('lead/lead', 'frontend');
                        $result = $this->frontend_model_lead_lead->getCrmTeamLeadEmailId($sales_person['crm_user_id']);
                        if ($result->num_rows > 0) {
                            foreach ($result->rows as $val) {
                                $name = $val['name'];
                                $email = $val['email'];
                                $mail->addAddress($email, $name);
                                if ($email == 'north.sales@wholesalebox.in') {
                                    $mail->addAddress(EMAIL_IDS['north_sales_support']['email_id'], EMAIL_IDS['north_sales_support']['name']);
                                } elseif ($email == 'south.sales@wholesalebox.in') {
                                    $mail->addAddress(EMAIL_IDS['south_sales_support']['email_id'], EMAIL_IDS['south_sales_support']['name']);
                                } elseif ($email == 'west.sales@wholesalebox.in') {
                                    $mail->addAddress(EMAIL_IDS['west_sales_support']['email_id'], EMAIL_IDS['west_sales_support']['name']);
                                    $mail->addCC(EMAIL_IDS['manish']['email_id'], EMAIL_IDS['manish']['name']);
                                } elseif ($email == 'east.sales@wholesalebox.in') {
                                    $mail->addAddress(EMAIL_IDS['east_sales_support']['email_id'], EMAIL_IDS['east_sales_support']['name']);
                                }
                            }
                        }
                    }

                    // Check if this Order contains STORE products
                    if ($order_status_id != $delivery_issues) {

                        // delivery issues mail not send to them
                        $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                        $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);

                        $store_check = $this->_checkOrderProductIsStoreOrder($sid);
                        if ($store_check) {
                            $mail_body .= "<br><br>";
                            $mail_body .= "@Store Manager: Suborder contains Store Inventory. Please manage their stock accordingly.<br>";
                            $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
                        }
                    }

                    $mail_body .= MailTemplate::getGeneralFooter();
                    $mail->setFrom($this->config->get('config_email'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
                    $mail->Subject = $mail_subject;
                    $mail->msgHTML($mail_body);
                    $mail->send();
                }

                //For cancelled order revived to store manager
                if(($old_order_status_id == $canceled) &&
                        ($order_status_id != $canceled) &&
                        ($order_status_id != $failed)
                ) {
                    $store_check = $this->_checkOrderProductIsStoreOrder($sid);
                    if ($store_check) {
                        $mail = new PHPMailer();
                        $mail->Host = $this->config->get('config_mail_smtp_hostname');
                        $mail->Username = $this->config->get('config_mail_smtp_username');
                        $mail->Password = $this->config->get('config_mail_smtp_password');
                        $mail->Port = $this->config->get('config_mail_smtp_port');
                        $mail->SMTPSecure = 'ssl';
                        $mail->SMTPAuth = true;
                        $mail->isSMTP();
                        $mail_body = "";
                        $mail_body .= MailTemplate::getGeneralHeader();
                        $mail_body .= "Attention: Suborder No: " . $sid . " has been REVIVED. <br>";
                        $mail_body .= "Order Date: " . date('d-M-Y', strtotime($sdata['date_added'])) . "<br>";
                        $mail_body .= "Client Name: " . trim($order_info['payment_firstname'] . " " . $order_info['payment_lastname']) . "<br>";
                        $mail_body .= "Company Name: " . trim($order_info['payment_company']) . "<br>";
                        $sales_person['name'] = "N/A";
                        if (!empty($order_id)) {
                            $sales_person = $this->_getSalesPersonName($order_id);
                        }
                        $mail_body .= "Salesperson: " . trim($sales_person['name']) . "<br>";
                        $mail_body .= "City: " . trim($order_info['payment_city']) . "<br>";
                        $mail_body .= "Suborder Value: " . $this->currency->format($sdata['total'], $order_info['currency_code'], $order_info['currency_value']) . "<br>";
                        $mail_body .= "Payment Method: " . trim($order_info['payment_code']) . "<br>";
                        $mail_body .= "Comments: " . trim($comment) . "<br>";
                        $mail_body .= "Internal Notes: " . trim($notes) . "<br>";
                        $mail_body .= "courier_partner:  " . $sdata['courier_partner'] . "<br>";
                        $mail_body .= "tracking_no:  " . $sdata['tracking_no'] . "<br>";
                        $mail_subject = "Attention: Cancelled order REVIVED (Suborder no: ".$sid.")";
                        $mail_body .= "<br><br>";
                        $mail_body .= "@Store Manager: Suborder contains Store Inventory. Please manage their stock accordingly.<br>";
                        $mail_body .= MailTemplate::getGeneralFooter();
                        $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
                        $mail->setFrom($this->config->get('config_email'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
                        $mail->Subject = $mail_subject;
                        $mail->msgHTML($mail_body);
                        $mail->send();
                    }
                }  
                
                //If order status id is different from current order status id then update order totals (this is due to remove suborder triggers)
                if($order_status_id != $old_order_status_id && $old_order_status_id > 0) {
                    OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $sid);
                }
            } // End Suborder Loop
            // If Order not placed yet, and now status changed to NOT Canceled / Failed status
            if (isset($suborder_info[$order_info['order_no']]['order_status_id']) && $suborder_info[$order_info['order_no']]['order_status_id'] == 0 && $order_status_id != $canceled && $order_status_id != $failed) {

                // As we know that there is only one suborder now,
                // we can point $suborder_info directly to first array-value in it
                $suborder_info = reset($suborder_info);

                // Now we get all the Products in this Order
                $selector = array('order_product' => array());
                $order_product_data = reset(OrderInfo::getOrderInfo($this->db, $order_id, '', $selector)['suborder']
                        )['order_product'];

                // Converting missing order to pending order
                // calculate totals of missing order
                $total_data = array();
                if (!empty($this->request->post['missing_order'])) {
                    $split_order = new SplitOrder($this->db);
                    $total_data = $split_order->splitOrderTotals($order_id);
                    $total_data = $total_data[key($total_data)];
                    // updating cst to no submit in case of missing order
                    $sql = "UPDATE " . DB_PREFIX . "suborder
                                SET cform_submit = 'no_submit'
                             WHERE order_id = '" . (int) $order_id . "' ";
                    $this->db->query($sql);
                    $cst = 0;
                }

                $total_factory = new TotalFactory($this, true);
                $total_data = $total_factory->getTotal();
                $refund_cform = 0.0;
                $cst = 0;

                // Stock subtraction (also setting date_out_of_stock despite item going out of stock or not.
                // Cron checks for quantity before sending out of stock alerts, so no issues.
                foreach ($order_product_data as $order_product) {
                    $sql = "UPDATE " . DB_PREFIX . "product
                            SET quantity = GREATEST( 0 , CAST(quantity AS SIGNED) - " . (int) $order_product['quantity'] . " ),
                                date_out_of_stock = NOW()
                            WHERE product_id = '" . (int) $order_product['product_id'] . "'
                              AND subtract = '1'";
                    $this->db->query($sql);

                    // Also subtracting Option quantity
                    $sql = "SELECT product_option_value_id FROM " . DB_PREFIX . "order_option
                WHERE order_product_id = '" . (int) $order_product['order_product_id'] . "'";
                    $order_option_query = $this->db->query($sql);
                    foreach ($order_option_query->rows as $option) {
                        $sql = "UPDATE " . DB_PREFIX . "product_option_value
                                SET quantity = GREATEST( 0 , CAST(quantity AS SIGNED) - " . (int) $order_product['quantity'] . " )
                                WHERE product_option_value_id = '" . (int) $option['product_option_value_id'] . "'
                                  AND subtract = '1'";
                        $this->db->query($sql);
                    }
                    
                    // added by Devendra, 15 June 2018
                    // ----start ----
                    // need to update combo product quantity, if current product is associate of any combo product
                    // here we are not checking wether this product is associate or not
                    // ( also currently we dont have this information in order product),
                    // this method will handle this
                    Product::updateComboProductQuantityUsingAssociate($this->db, $order_product['product_id']);
                    // ---end----
                }
                $shipment_range = '6-9';
                if (isset($suborder_info[$order_info['order_no']]['shipping_code']))
                    $shipment_code = $suborder_info[$order_info['order_no']]['shipping_code'];
                if (isset($suborder_info['shipping_code']))
                    $shipment_code = $suborder_info['shipping_code'];
                if ($shipment_code == "weight.weight_6") {
                    $shipment_range = '4-5';
                }


                // Send SMS to Customer on Successful Order Placement
                $support_number = 8696491521;
                $request['customer_id'] = $this->customer->getId();
                $request['token'] = $this->customer->getAccessToken();

                $data_json = json_encode($request);
                $api_url = CRM_URL . "cron/getAgentofCustomer";
                $ch = curl_init($api_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                    'Content-Length: ' . strlen($data_json))
                );
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                $result = json_decode($result, true);

                if ($result['status'] == 1) {
                    if (!empty($result['agent'])) {
                        $agent_number = $result['agent'];
                        $support_number = $result['agent'];
                    } else if (!empty($result['team_lead'])) {
                        $support_number = $result['team_lead'];
                    } else {
                        $support_number = $result['support'];
                    }
                }
                $balance_amount_array = array();
                if (!empty($total_data)) {
                    $balance_amount_array = array_combine(array_column($total_data, 'code'), $total_data);
                }

                $balance_amount = $order_info['total'];
                if (array_key_exists('net_payable_amount', $balance_amount_array)) {
                    $balance_amount = $balance_amount_array['net_payable_amount']['value'];
                }

                $message = sprintf($this->language->get('on_new_order'), $order_info['order_no'], $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']), $this->currency->format($balance_amount, $order_info['currency_code'], $order_info['currency_value']), $shipment_range, $support_number);
                $order_sms = new SMS($message, $order_info['telephone']);
                $order_sms->sendMessage();


                /*                 * ********************************************************************* */
                //        Send Email To Customer For Successful Order Placement        //
                /*                 * ********************************************************************* */
                $order_info['order_product'] = $order_product_data;
                $order_info['order_totals'] = $total_data;
                // If this is order from franchise(order of customer of franchise), then we will not send mail.
                if (empty($order_info['franchise_id'])) {
                    $this->sendOrderEmail($order_info, $suborder_info, $cst, $refund_cform, $comment, $notify_email, true);
                    $order_info['order_status_id'] = $order_status_id;
//                    $this->sendOrderEmailToStores($order_info);
                }

                /*                 * ********************************************************************* */
                //                       Splitting the Order                            //
                /*                 * ********************************************************************* */
                $splitorder = new SplitOrder($this->db);
                $splitorder->splitByCity($order_id, 1);

                // Confirming coupon/cashback/voucher etc and inserting payment in order_payment table
                $total_factory->confirm($order_info, $total_data);




                if ($old_order_status_id == 0 && $order_status_id == (int)ORDER_STATUS['Pending'] ) {
                    $sql = "UPDATE " . DB_PREFIX . "suborder
            SET order_status_id = '" . (int) $order_status_id . "',
              date_modified = NOW()
            WHERE order_id = '" . (int)$order_id . "'";
          $this->db->query($sql);
        }

        // Now we can Clear the cart, as we have computed totals for this order and stored them in $total_data
          // copy cart in cart history.
          // Check if Order is from Android App or Web (Desktop / Mobile)
          if($order_info['order_from'] == 'ANDROIDAPP'){
              $last_cart_modified_from = 'ANDROID';
          }else if($order_info['order_from'] == 'IOSAPP'){
              $last_cart_modified_from = 'IOS';
          }else if ($order_info['order_from'] == 'MOBILE_WEB'){
              $last_cart_modified_from = 'MWEB';
          }else{
              $last_cart_modified_from = 'DWEB';
          }
          $this->cart->addCartHistory($order_info['customer_id'],'order_placed',$order_info['order_no'],$last_cart_modified_from);
                // 31-05-2017: From now, we will not remove products from cart which are out of stock
                //@todo: need to find better solution intead of calling getProducts
                $products = $this->cart->getProducts();
                $product_keys = '';
                foreach ($products as $key => $value) {
                    $product_keys .= $value['key'] . ',';
                }

                $this->cart->removeProducts($product_keys);
                //Remove coupon code
                $this->cart->setCoupon('');

                $this->cart->setFranchiseId(0);
                $this->cart->setFranchiseMargin(0);


                ///////////////////////////
                //AUTO TAGGING OF ORDER //
                ///////////////////////////

                $order_data = [
                    'order_id' => ($order_info['order_id']),
                    'order_no' => ($order_info['order_no']),
                    'customer_id' => ($order_info['customer_id']),
                    'action' => 'AUTO_TAG'
                    ];
                $order_queue = new updateorder($order_data);
                $order_queue->updateOrderData();

                ///////////////////////////
                //CRM INSERTION OF ORDER //
                ///////////////////////////

                $lead_data = [
                    'name' => ($order_info['firstname']),
                    'business_name' => ($order_info['shipping_company']),
                    'email' => ($order_info['email']),
                    'address' => ($order_info['shipping_address_1']),
                    'zip' => ($order_info['shipping_postcode']),
                    'country_id' => ($order_info['shipping_country']),
                    'status' => 'ORDERED',
                    'priority' => 1,
                    // 'is_registered'      => 1 ,
                    'is_ordered' => 1,
                    'order_date' => date('Y-m-d H:i:s'),
                    'cart' => '',
                    'is_cart' => '0',
                    'lead_exact_value' => (float) $order_info['total'],
                    'state_id' => ($order_info['shipping_zone']),
                    'city' => ($order_info['shipping_city']),
                    'order_id' => ($order_info['order_id']),
                    'order_no' => ($order_info['order_no']),
                    'ip' => ($order_info['ip'])
                ];

                if (empty(trim($order_info['firstname']))) {
                    unset($lead_data['name']);
                }

                if (empty(trim($order_info['shipping_company']))) {
                      unset($lead_data['business_name']);
                }

                $this->load->model('lead/lead', 'frontend');
                $this->frontend_model_lead_lead->updateLead($lead_data, $order_info['telephone'], 'Order Recieved', $order_info['customer_id']);
            } // Ending Order Placed Loop

            $this->event->trigger('post.order.history.add', $order_id);
        } // Close if ($order_info && $suborder_info)
        
      }
      catch (\Throwable $exception) {
        $subject = "Order Placing Exception - Add Order History Method: ". date('d M Y H:i:s');
        //Mail to track exception, to resolve issue
        mailException($exception, $this->config, $subject);
      }
      
    } // Close addOrderHistory function

    public function updateShippingInfo($order_id, $suborder_id = '', $shippingco = '', $tracking = '') {
        if ($shippingco and $tracking) {
            $this->db->query("UPDATE `" . DB_PREFIX . "suborder`
                        SET courier_partner = '" . $this->db->escape($shippingco) . "',
                            tracking_no = TRIM(REPLACE('" . $this->db->escape($tracking) . "', CHAR(9), ''))
                        WHERE order_id = '" . (int) $order_id . "'
                          AND suborder_id = '" . $this->db->escape($suborder_id) . "'");
        }
    }

    public function getOrderId($order_no) {
        $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE order_no = '" . (float) $order_no . "'");
        if ($query->rows) {
            return $query->row['order_id'];
        } else {
            return $order_no;
        }
    }

    private function sendCODFailMail($order_id, $suborder_id, $order_no) {

        $data['logo'] = 'https://www.wholesalebox.in/image/' . $this->config->get('config_logo');

        $sql = "SELECT DISTINCT oop.seller_id
                FROM " . DB_PREFIX . "order_product oop
                WHERE oop.order_id = '" . (int) $order_id . "'
                  AND oop.suborder_id = '" . $this->db->escape($suborder_id) . "'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            foreach ($query->rows as $row) {

                $seller_id = (int) $row['seller_id'];
                $seller_emails = SellerInfo::getSellerEmails($this->db, $seller_id, true);
                if (empty($seller_emails)) {
                    continue; // Move to next seller
                }

                $seller_info = SellerInfo::getSellerFirmDetails($this->db, $seller_id);
                $data['seller_name'] = $seller_info['company'];

                $data['order_no'] = $order_no;
                $html = '';
                $html = $this->load->view('default/template/mail/cod-fail-to-seller.tpl', $data);

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
                $mail->addReplyTo($this->config->get('config_mail_smtp_username'), 'WholesaleBox');

                foreach ($seller_emails as $email) {
                    $mail->addAddress($email, $data['seller_name']);
                }
                $mail->Subject = 'Wholesalebox - Order No: ' . $data['order_no'] . ' - COD Failed';
                $mail->msgHTML($html);
                $mail->send();
            }
        }
    }

    public function updateQuantity($quantity, $product_id) {
        $sql = "Update oc_product SET quantity = '" . $quantity . "' WHERE product_id = '" . $product_id . "'";
        $this->db->query($sql);
    }

    //order successfully email send on COD
    public function successfullyEmailOnCOD($order_no, $customer_email, $message) {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
        $mail->addAddress($customer_email);
        $mail->Subject = 'WholesaleBox: COD Order Placed on - ' . $order_no;
        $mail->msgHTML($message);
        $mail->send();
    }

    public function getOrderOfCustomer($customer_id) {
        $sql = "SELECT o.shipping_city,
                       o.date_added as order_date,
                       o.order_no,
                       o.shipping_zone_id,
                       o.shipping_country_id,
                       o.shipping_postcode
                FROM oc_order o
                WHERE o.customer_id = '" . (int) $customer_id . "'
                ORDER BY date_added DESC";
        return $this->db->query($sql)->row;
    }

    /**
     * Method for Send order mail to client and admin
     */
    public function sendOrderEmail($order_info, $suborder_info, $cst, $refund_cform, $comment = '', $notify_email = '', $sendToAdmin = true) {
        $order_id = $order_info['order_id'];

        $language_directory = 'english';
        $language = new Language($language_directory);
        $language->load($language_directory);
        $language->load('mail/order', DIR_CATALOG . 'language/');

        $order_status = 'Order Placed';

        $subject = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_info['order_no']);

        // HTML Mail
        $data = array();

        $data['title'] = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_info['order_id']);
        $data['text_greeting'] = sprintf($language->get('text_new_greeting'), $order_info['store_name']);
        $data['text_link'] = $language->get('text_new_link');
        $data['text_download'] = $language->get('text_new_download');
        $data['text_order_detail'] = $language->get('text_new_order_detail');
        $data['text_instruction'] = $language->get('text_new_instruction');
        $data['text_order_id'] = $language->get('text_new_order_id');
        $data['text_order_no'] = $language->get('text_new_order_no');
        $data['text_date_added'] = $language->get('text_new_date_added');
        $data['text_payment_method'] = $language->get('text_new_payment_method');
        $data['text_shipping_method'] = $language->get('text_new_shipping_method');
        $data['text_email'] = $language->get('text_new_email');
        $data['text_telephone'] = $language->get('text_new_telephone');
        $data['text_ip'] = $language->get('text_new_ip');
        $data['text_order_status'] = $language->get('text_new_order_status');
        $data['text_payment_address'] = $language->get('text_new_payment_address');
        $data['text_shipping_address'] = $language->get('text_new_shipping_address');
        $data['text_product'] = $language->get('text_new_product');
        $data['text_image'] = $language->get('text_new_image');
        $data['text_name'] = $language->get('text_new_name');
        $data['text_model'] = $language->get('text_new_model');
        $data['text_product_code'] = $language->get('text_new_product_code');
        $data['text_quantity'] = $language->get('text_new_quantity');
        $data['text_pieces'] = $language->get('text_new_pieces');
        $data['text_price'] = $language->get('text_new_price');
        $data['text_price_per_piece'] = $language->get('text_new_price_per_piece');
        $data['text_discount_per_piece'] = $language->get('text_discount_per_piece');
        $data['text_total'] = $language->get('text_new_total');
        $data['text_subtotal'] = $language->get('text_new_subtotal');
        $data['text_tax'] = $language->get('text_new_tax');
        $data['text_footer'] = $language->get('text_new_footer');
        $data['text_cform_footer'] = $language->get('text_cform_footer');
        $data['text_cst'] = $language->get('text_cst');
        $data['text_tax_refund'] = $language->get('text_tax_refund');
        $data['text_total_qty'] = $language->get('text_total_qty');
        $data['text_new_quantity'] = $language->get('text_new_quantity');
        $data['text_new_pieces'] = $language->get('text_new_pieces');

        $data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
        $data['store_name'] = $order_info['store_name'];
        $data['store_url'] = $order_info['store_url'];
        $data['customer_id'] = $order_info['customer_id'];
        $data['customer_name'] = $order_info['firstname']." ". $order_info['lastname'];
        $data['link'] = $order_info['store_url'] . 'index.php?route=account/order';
        $data['order_id'] = $order_info['order_id'];
        $data['order_no'] = $order_info['order_no'];
        $data['cform_submit'] = 0;
        $data['cst_with_cform'] = $this->currency->format($cst);
        $data['refundable_cform'] = $this->currency->format($refund_cform);
        $data['date_added'] = date('d M Y', strtotime($order_info['date_added']));
        $data['payment_method'] = $order_info['payment_method'];
        $data['shipping_method'] = $suborder_info['shipping_method'];
        $data['email'] = $order_info['email'];
        $data['telephone'] = $order_info['telephone'];
        $data['ip'] = $order_info['ip'];
        $data['order_status'] = $order_status;

        if ($comment && $notify_email) {
            $data['comment'] = nl2br($comment);
        } else {
            $data['comment'] = '';
        }

        $data['gst_number'] = $order_info['gst_number'] ?? '';
       
        // Address format
        $format = '{firstname} {lastname}' . ", " .
                '{company}' . ", " .
                '{address_1}' . ", " .
                '{address_2}' . ", " .
                '{city} {postcode}' . ", " .
                '{zone}' . ", " .
                '{country}';

        $find = array(
            '{firstname}',
            '{lastname}',
            '{company}',
            '{address_1}',
            '{address_2}',
            '{city}',
            '{postcode}',
            '{zone}',
            '{country}'
        );

        $replace = array(
            'firstname' => $order_info['payment_firstname'],
            'lastname' => $order_info['payment_lastname'],
            'company' => $order_info['payment_company'],
            'address_1' => $order_info['payment_address_1'],
            'address_2' => $order_info['payment_address_2'],
            'city' => $order_info['payment_city'],
            'postcode' => $order_info['payment_postcode'],
            'zone' => $order_info['payment_zone'],
            'country' => $order_info['payment_country']
        );

        $data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))
                )
        );

        $replace = array(
            'firstname' => $order_info['shipping_firstname'],
            'lastname' => $order_info['shipping_lastname'],
            'company' => $order_info['shipping_company'],
            'address_1' => $order_info['shipping_address_1'],
            'address_2' => $order_info['shipping_address_2'],
            'city' => $order_info['shipping_city'],
            'postcode' => $order_info['shipping_postcode'],
            'zone' => $order_info['shipping_zone'],
            'country' => $order_info['shipping_country']
        );

        $data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))
                )
        );

        // Products
        $data['products'] = array();
        $total_sets = 0;
        $total_pieces = 0;
        $this->load->model('tool/image');
        foreach ($order_info['order_product'] as $product) {

            // Getting image
            $product_image_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product
                                               WHERE product_id = '" . (int) ($product['product_id']) . "'");

            if ($product_image_query->row['image']) {
                $image = $this->model_tool_image->resize($product_image_query->row['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }

            $total_sets += $product['quantity'];
            $total_pieces += ($product['quantity'] * $product['piece_in_set']);

            $tax_per_piece = ($product['price_per_piece'] * $product['output_tax_rates']) / 100;

            $data['products'][] = array(
                'name' => $product['name'],
                'thumb' => $image,
                'href' => HTTPS_CATALOG.'index.php?route=product/product&product_id=' . $product['combo_product_id'],
                'model' => $product['model'],
                'option' => false, // Not using it for display purposes
                'quantity' => $product['quantity'],
                'pieces' => $product['quantity'] * $product['piece_in_set'],
                'price_per_piece' => $this->currency->format($product['price_per_piece'], $order_info['currency_code'], $order_info['currency_value'], true),
                'discount_per_piece' => $this->currency->format($product['discount_per_piece'], $order_info['currency_code'], $order_info['currency_value'], true),
                'total' => $this->currency->format(($product['price_per_piece'] + $product['discount_per_piece']) * $product['piece_in_set'] * $product['quantity'], $order_info['currency_code'], $order_info['currency_value'], true),
                'tax' => (float) $product['output_tax_rates'] . ' %',
                'tax_per_piece' => $this->currency->format($tax_per_piece, $order_info['currency_code'], $order_info['currency_value'], true),
                'comment' => $product['comment'],
                'store_pickup' => $product['store_pickup']
            );
        }

        $data['total_sets'] = $total_sets;
        $data['total_pieces'] = $total_pieces;
        
        // Order Totals
        $data['totals'] = array();
        foreach ($order_info['order_totals'] as $total) {
            if($total['code'] == 'paycharge' && !empty($total['breakup'])) {
                $data['totals'][] = array(
                'title' => $total['title'],
                'code' => $total['code'],
                'text' => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], true),
                 'breakup'=>   $total['breakup']
            );
            }else if( trim($total['title']) == 'Amount in words' ){
              //////////////Commented code because of Amount in words column is not reuired in mail
              // $data['totals'][] = array(
              //     'title' => $total['title'],
              //     'code' => $total['code'],
              //     'text' =>$total['value']
              // );
            } else {
                $data['totals'][] = array(
                'title' => $total['title'],
                'code' => $total['code'],
                'text' => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], true)
            );
            }
            
        }

        // if international order, then send custom duty message in mail
        if (isset($order_info['store_id']) && $order_info['store_id'] == INTERNATIONAL_STORE_ID) {
            $data['custom_duty_charge_message'] = $language->get('text_custom_duty_charge');
        }

        if(!empty($order_info['email'])){

          // Loading tpl file (To get HTML for mail content)
          $html = $this->load->view(DIR_CATALOG . 'view/theme/default/template/mail/order.tpl', $data, true);

          // Creating mail object and sending mail to customer
          $mail = new PHPMailer();
          $mail->Host = $this->config->get('config_mail_smtp_hostname');
          $mail->Username = $this->config->get('config_mail_smtp_username');
          $mail->Password = $this->config->get('config_mail_smtp_password');
          $mail->Port = $this->config->get('config_mail_smtp_port');
          $mail->SMTPSecure = 'ssl';
          $mail->SMTPAuth = true;
          $mail->isSMTP();
          $mail->addAddress($order_info['email']);
          $mail->setFrom($this->config->get('config_email'), 'DONOTREPLY@wholesalebox.in');
          $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
          $mail->Subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
          $mail->msgHtml($html);
          $mail->send(1, false);

        }


        /*         * ********************************************************************* */
        //          Send Email To Admin For Successful Order Placement          //
        /*         * ********************************************************************* */
        if ($sendToAdmin) {
            $subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_info['order_no']);
            // HTML Mail
            $data['text_greeting'] = $language->get('text_new_received');

            if ($comment) {
                if ($order_info['comment']) {
                    $data['comment'] = nl2br($comment) . '<br/><br/>' . $order_info['comment'];
                } else {
                    $data['comment'] = nl2br($comment);
                }
            } else {
                if ($order_info['comment']) {
                    $data['comment'] = $order_info['comment'];
                } else {
                    $data['comment'] = '';
                }
            }

            $data['text_download'] = '';
            $data['text_footer'] = '';
            $data['text_link'] = '';
            $data['link'] = '';
            $data['download'] = '';


            // Loading tpl file
            $html = $this->load->view(DIR_CATALOG . 'view/theme/default/template/mail/order.tpl', $data, true);

            // Creating mail object and sending mail to customer
            $mail = new PHPMailer();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->isSMTP();
            if (strpos($order_info['store_url'], 'staging') !== false || strpos($order_info['store_url'], 'dev') !== false) {
                $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            } else {
                $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            }
            $mail->setFrom($this->config->get('config_email'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
            $mail->Subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
            $mail->msgHtml($html);
            $mail->send();
        }
    }

    /*
     *
     * checking product is marked out of stock by seller before order is processed and
     * after this order place by customer
     *
     */

    private function checkProductIsMarkedOutOfStockBySeller($sellerId = null, $productId = null, $date_from = null) {
        $sql = "SELECT new, product, nickname, updated_type, modified
                FROM " . DB_PREFIX . "seller_change_log
                WHERE seller_id = '" . (int) $sellerId . "'
                    AND ( updated_type = 'Stock_status' OR updated_type = 'set_quantity' )
                    AND product_id = '" . (int) $productId . "'
                    AND modified BETWEEN '" . date('Y-m-d H:i:s', strtotime($date_from)) . "' AND '" . date('Y-m-d H:i:s') . "'
                ORDER BY modified DESC LIMIT 1";
        $record = $this->db->query($sql);
        $result = $record->row;
        if (!empty($result)) {
            if (($result['updated_type'] == 'Stock_status' && $result['new'] != 'out_stock') || ($result['updated_type'] == 'set_quantity' && (float) $result['new'] > 0)) {
                $result = array();
            }
        }

        return $result;
    }

    /*
     *
     * checking order is processing first time for status of penidng to processed
     *
     */

    private function checkSubOrderIsProcessingFirstTime($orderId = null, $subOrderId = null) {
        $sql = "SELECT order_history_id
                FROM " . DB_PREFIX . "order_history
                WHERE order_id = '" . (int) $orderId . "'
                  AND suborder_id = '" . $this->db->escape($subOrderId) . "'
                  AND order_status_id IN(9,16)
                LIMIT 1";

        $record = $this->db->query($sql);

        if ($record->num_rows == 0) { // No previous Processed/Tentative Processed entry found
            return true;
        }

        return false;
    }

    /**
     * Method for order cancelled product data
     * @param: $once_cancelled_sql : array of data,
     * @return : order products data on order cancelled
     * vikas, 2017
     */
    private function OrderCancelledProductData($once_cancelled_sql) {
        $fetch_order_id = $once_cancelled_sql->row['order_id'];
        $fetch_suborder_id = $once_cancelled_sql->row['suborder_id'];
        $fetch_products_sql = "SELECT order_product_id,
                                  seller_sku,
                                  comment,
                                  quantity,
                                  piece_in_set,
                                  transfer_price_per_piece,
                                  seller_id,
                                  product_id
                            FROM " . DB_PREFIX . "order_product
                            WHERE order_id='" . (int) $fetch_order_id . "'
                              AND suborder_id ='" . $this->db->escape($fetch_suborder_id) . "'
                           ";
        $fetch_products_query = $this->db->query($fetch_products_sql);

        $product_ids = array_column($fetch_products_query->rows, 'product_id');

        $query_images = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX . "product
              WHERE product_id IN (" . implode(',', $product_ids) . ")");

        $product_images = array();
        foreach ($query_images->rows as $images_value) {
            $product_images[$images_value['product_id']] = $images_value['image'];
        }

        $products_data = array();
        $this->load->model('tool/image');
        
        foreach ($fetch_products_query->rows as $key => $value) {
            $total_pieces = $value['quantity'] * $value['piece_in_set'];
            $amt_inc_tax = $total_pieces * $value['transfer_price_per_piece'];

            $products_data[$value['seller_id']]['products'][$key]['product_image'] = $this->model_tool_image->resize($product_images[$value['product_id']], 150, 150);
            $products_data[$value['seller_id']]['products'][$key]['sku'] = $value['seller_sku'];
            $products_data[$value['seller_id']]['products'][$key]['comment'] = $value['comment'];
            $products_data[$value['seller_id']]['products'][$key]['quantity'] = $value['quantity'];
            $products_data[$value['seller_id']]['products'][$key]['total_pieces'] = $total_pieces;
            $products_data[$value['seller_id']]['products'][$key]['transfer_price_per_piece'] = $value['transfer_price_per_piece'];
            $products_data[$value['seller_id']]['products'][$key]['total_amount'] = $amt_inc_tax;
        }

        return $products_data;
    }

  private function _getOrderProductOption($order_product_id){

      $sql = "SELECT
      oo.product_option_id,
      oo.product_option_value_id,
      oo.name,
      oo.value,
      pov.quantity,
      p.image,
      p.piece_in_set
      FROM " . DB_PREFIX . "order_option oo
      INNER JOIN " . DB_PREFIX . "product_option_value as pov
      ON pov.product_option_value_id=oo.product_option_value_id
      INNER JOIN " . DB_PREFIX . "order_product as op
      ON op.order_product_id=oo.order_product_id
      INNER JOIN " . DB_PREFIX . "product p
      ON p.product_id=op.product_id
      WHERE oo.order_product_id = ". (int)$order_product_id ." LIMIT 1";

      $result = $this->db->query($sql);

      if($result->num_rows){

          return $result->row;
      }
      else{
          return array();
      }
  }
  /**
  * Method for check order is related to stock transfer
  * @param: customer_id,
  * @return : true
  * kalyan 27th Nov 2017
  */
  private function checkOrderIsRelatedToStockTransfer($customer_id, $order_id){

    $sql = "SELECT
              o.customer_id
            FROM
              oc_order o
            INNER JOIN
              oc_customer c ON c.customer_id = o.customer_id
            INNER JOIN
              oc_ms_seller ms ON ms.seller_id = c.customer_id
            WHERE
              o.stock_transfer = 1
              AND ms.seller_invoice_generate = 0
              AND o.customer_id ='" . (int)$customer_id . "'
              AND o.order_id ='" . (int)$order_id . "'";

    $query = $this->db->query($sql);

    if ($query->num_rows>0) {

      return true;
    }else{

      return false;
    }
  }
  /**
  * Method for update stock transfer product quantity
  * @param: product_id,
  * @param: quantity,
  * kalyan 27th Nov 2017
  */
  private function updateStockTransferProductQuantity($product_id, $quantity){

    $sql = "UPDATE " . DB_PREFIX . "product
            SET quantity = quantity + '" . (int)$quantity . "'
            WHERE product_id = '" . (int)$product_id."'";

    $this->db->query($sql);
  }
  /**
  * Method to send mail to store manager.
  * @author kalyan 1st DEC 2017
  */
  private function upgradedStockReportMailToStoreManager($params = array()){

    $datas                    = array();

    $datas['company']         = $params['seller_data']['company'];
    $datas['data']            = $params['products_detail'];

    /*
    * send mail
    */
    $html = $this->load->view($this->config->get('config_template') . '/template/mail/upgraded_stock_report_mail_to_store_manager.tpl', $datas);

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $this->config->get('config_mail_smtp_hostname');
    $mail->Port = $this->config->get('config_mail_smtp_port');
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $this->config->get('config_mail_smtp_username');
    $mail->Password = $this->config->get('config_mail_smtp_password');
    $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
    $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
    $mail->addAddress($params['seller_data']['email'], 'Store Manager');
    $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
    $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
    $mail->Subject = 'Stock Updated On '.$params['seller_data']['pickup_city_code'].' Store';
    $mail->msgHTML($html);
    $mail->send();
  }

    /**
     * Method for send a mail to stores on an order placed from their store
     * @param: $order_info = array of order data with products details
     * @return : NULL
     * vikas, Jan 2018
     */
    private function sendOrderEmailToStores($order_info) {
        $store_wise_product_data = array();
        $order_details = array(
                            'order_no' => $order_info['order_no'],
                            'date_added' => $order_info['date_added'],
                            'order_status_id' => $order_info['order_status_id']
                            );

        foreach ($order_info['order_product'] as $product) {

            // Getting image
            $product_image_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product
                                               WHERE product_id = '" . (int) ($product['product_id']) . "'");

            if ($product_image_query->row['image']) {
                $image = $this->model_tool_image->resize($product_image_query->row['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }


            $store_wise_product_data[$product['store_sales']][] = array(
                'name' => $product['name'],
                'thumb' => $image,
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'model' => $product['model'],
                'quantity' => $product['quantity'],
                'pieces' => $product['quantity'] * $product['piece_in_set'],
                'comment' => $product['comment'],
            );
        }

        foreach($store_wise_product_data as $store_key => $product_data){
            if($store_key !='NO'){

                $html = MailTemplate::sendOrderEmailToStores($store_key, $order_details, $product_data);

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
                $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

                foreach (STORE_EMAIL_IDS[$store_key] as $email_ids) {
                    $mail->addAddress($email_ids['email_id'], $email_ids['name']);
                }

                $mail->Subject = 'Wholesalebox -Order  '.$order_info['order_no'];
                $mail->msgHTML($html);
                $mail->send();
            }
        }
    }
    /**
     * Method for send a mail to stores on an order status change from their store
     * @param: $order_info = array of order data with products details
     * @return : NULL
     * vikas, Jan 2018
     */
    private function orderStatusChangeSendMailToStore($mail_data=array()) {
        $store_wise_product_data = array();
        $order_details = array(
                            'order_no' => $mail_data['order_no'],
                            'date_added' => $mail_data['date_added'],
                            'order_status_id' => $mail_data['order_status_id'],
                            );

        foreach ($mail_data['order_product'] as $product) {

            // Getting image
            $product_image_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product
                                               WHERE product_id = '" . (int) ($product['product_id']) . "'");

            if ($product_image_query->row['image']) {
                $image = $this->model_tool_image->resize($product_image_query->row['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }


            $store_wise_product_data[$product['store_sales']][] = array(
                'name' => $product['name'],
                'thumb' => $image,
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'model' => $product['model'],
                'quantity' => $product['quantity'],
                'pieces' => $product['quantity'] * $product['piece_in_set'],
                'comment' => $product['comment'],
            );
        }

        foreach($store_wise_product_data as $store_key => $product_data){
            if($store_key !='NO'){

                $html = MailTemplate::sendOrderEmailToStores($store_key, $order_details, $product_data);
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
                $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

                foreach (STORE_EMAIL_IDS[$store_key] as $email_ids) {
                    $mail->addAddress($email_ids['email_id'], $email_ids['name']);
                }

                $mail->Subject = 'Wholesalebox -Order  '.$mail_data['order_no'];
                $mail->msgHTML($html);
                $mail->send();
            }
        }
    }

    /*
     * send push notification to seller when suborder is processed
     * function sendPushNotificationToSellers
     * @date March 2018
     * @return true
    */
    public function sendPushNotificationToSellers($seller_ids, $order_id, $suborder_id ) {
        // first get the gcm_ids
        $sellers = implode(',', $seller_ids);
        $result = $this->customer->getGCMIds($sellers);
        if (empty($result)) return;
        $gcm_id_arr = array_column($result, 'ws_gcm_registration_id');
        // custom_notifcation is used by android app, while ios app use directly message data
        $message = array(
            'custom_notification' => array
            (
                'sound' => 'default',
                'title' 	=> 'Greetings from Wholesalebox!',
                'body'    => 'You have received a new order ' . $suborder_id,
                'large_icon' => 'https://cdnimages.net/img/wsbseller.png',
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'priority'      => 'high',
                'show_in_foreground' => true,
                'type' => 'PickUpOrder' // user will be redirected to order detail page
            ),
            'order_id' => $order_id,
            'suborder_id' => $suborder_id,
            'priority'      => 'high',
            'show_in_foreground' => true,
            'type' => 'PickUpOrder' // user will be redirected to order detail page
        );

        // send notification
        $notification = new Notification($gcm_id_arr, $message);
        $notification->sendPushNotificationToSellers();
    }

    // method to add courier docket for order status id - 14
    public function updateCourierDockets($params = array())
    {
        if(!empty($params['tracking'])) {
            //get courier id by name
            $query = $this->db->query("SELECT id
                                    FROM " . DB_PREFIX . "courier_partners
                                    WHERE courier_name = '" . $this->db->escape($params['shippingco']) . "'");
           // echo $query->num_rows; die;
            if($query->num_rows) {
                $courier_partner_id = $query->row['id'];
                //check for existing docket number
                $query = $this->db->query("SELECT id FROM " . DB_PREFIX . "courier_dockets "
                        . " WHERE  courier_partners_id = '".$this->db->escape($courier_partner_id)."' "
                        . " AND docket_no = '".$this->db->escape($params['tracking'])."' " );
                if(!$query->num_rows) {
                    $insertSql = "INSERT INTO " . DB_PREFIX . "courier_dockets"
                                . " SET order_id    = '".$this->db->escape($params['order_id'])."',"
                                . " suborder_id     = '".$this->db->escape($params['suborder_id'])."', "
                                . " courier_partners_id = '".$this->db->escape($courier_partner_id)."',"
                                . " docket_no       = '".$this->db->escape($params['tracking'])."',"
                                . " payment_mode    = '".$this->db->escape($params['payment_mode'])."',"
                                . " cod_amount      = '".$this->db->escape($params['cod_amount'])."',"
                                . " parcel_amount   = '".$this->db->escape($params['parcel_amount'])."'";
                    @$this->db->query($insertSql);
                }
            }
        }
    }

    private function _checkOrderProductIsStoreOrder($suborder_id) {
        $store_check_sql = "SELECT order_product_id
                            FROM " . DB_PREFIX . "order_product
                            WHERE suborder_id = '" . $this->db->escape($suborder_id) . "'
                              AND store_sales != 'NO'
                            LIMIT 1";
        $store_check_query = $this->db->query($store_check_sql);
        if ($store_check_query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    private function _getSalesPersonName($order_id) {
        $sales_person = array();
        $sql = "SELECT GROUP_CONCAT(DISTINCT(ss.name)) as name,
                        GROUP_CONCAT(DISTINCT (ss.crm_user_id)) as crm_user_id
                        FROM " . DB_PREFIX . "sales_staff ss
                        INNER JOIN " . DB_PREFIX . "order_sales_staff oss ON (ss.staff_id = oss.sales_staff_id)
                                WHERE oss.order_id = " . (int) $order_id;
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $sales_person = $query->row;
        }

        return $sales_person;
    }

    // method to check whether cashback is given or not against a suborder
    public function isCashbackGivenForSuborder($order_id, $suborder_id) {
      $sql = "SELECT customer_id FROM " . DB_PREFIX . "customer_cashback
              WHERE order_id = " . (int)$order_id . " AND suborder_id = '" . $this->db->escape($suborder_id) . "' 
              LIMIT 1";
      $query = $this->db->query($sql);
      if ($query->num_rows) {
          return true;
      }

      return false;
    }
}
