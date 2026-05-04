<?php

class ControllerCronCron extends Controller {

    public $data = array();

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry = $registry;
        $this->MsLoader->MsHelper->addStyle('multiseller');
        $this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'), $this->language->load('product/product'));
    }

    public function getproductsfillters()
    {
        $this->load->model('catalog/product');

        $products = $this->MsLoader->MsProduct->getProducts(array());

        foreach ($products as $values) {

            $product_id = $values['product_id'];
            echo '<span style="color: #0000cc;">product_id=</span>' . $product_id;

            $filters = $this->model_catalog_product->getProductFilters($product_id);

            $html = $this->load->view($this->config->get('config_template') . '/template/product/product.tpl');
            $trdata = '';

            foreach ($filters as $filter_id) {
                $filter_info = $this->model_catalog_product->getFilter($filter_id);

                $group = $filter_info['group'];
                $name = $filter_info['name'];

                $trdata .= '<tr>' . $group . ':' . '</tr>' . '<tr>' . $name . '</tr>';
            }

            $html = str_replace("[filters]", $trdata, $html);

            echo $html;
        }
    }


    public function notificationAboutSellerChangeLog() {

        $this->load->model('seller/seller_activity');
        $results = $this->model_seller_seller_activity->getSellerActivities();

        if (empty($results)) {
            exit();
        }

        $html = $this->load->view($this->config->get('config_template') . '/template/mail/sellerchangelog.tpl');
        $trdata = '';
        $i = 1;
        $mail = array();
        foreach ($results as $result) {
            $nickname = $result['nickname'];
            $updated_type = $result['updated_type'];
            $old = $result['old'];
            $new = $result['new'];
            $product = $result['product'];
            $trdata .= '<tr><td height="40px" style="padding: 0 20px">' . $i . '</td><td height="40px" style="padding: 0 20px">' . $nickname . '</td><td height="40px" style="padding: 0 20px">' . $product . '</td><td height="40px" style="padding: 0 20px">' . $updated_type . '</td><td height="40px" style="padding: 0 20px">' . $old . '</td><td height="40px" style="padding: 0 20px">' . $new . '</td></tr>';
            $i++;
        }
        $html = str_replace("PRODUCTS", $trdata, $html);

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        ;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
        $mail->addAddress(EMAIL_IDS['ashu']['email_id'], EMAIL_IDS['ashu']['name']);
        $mail->addAddress(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
        $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
        $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);


        $mail->Subject = 'Seller Change Log information - ' . date('d/M/Y', time());
        $mail->msgHTML($html);
        if ($mail->send()) {
            echo "success";
            $this->model_seller_seller_activity->setEmailSent();
            exit;
        } else {
            echo "error";
            exit;
        }
    }

    public function getOrdersInTransit() {
        $this->load->model('account/order');
        $results = $this->model_account_order->getOrdersInTransit();

        // If required to send inside the mail body as html; use the following commented out code.
        $html = $this->load->view($this->config->get('config_template') . '/template/mail/orders_in_transit.tpl', array('results' => $results) );

        $today_date = date("d/F/Y");
        $file = DIR_DLOAD . 'ShippedOrderSummary-' . date("dMY") . '.csv';
        $fp = fopen($file, 'w');

        $head = array(
                      'S. No.',
                      'Suborder No',
                      'Customer',
                      'Amount',
                      'Payment',
                      'Order Date',
                      'Ship Date',
                      'Days',
                      'Courier',
                      'Tracking No'
                    );
        fputcsv($fp, $head);

        $i=1;
        foreach ($results as $data){
            $data['date_history'] = date('Y-m-d', strtotime($data['date_history']));
            $data['date_added'] = date('Y-m-d', strtotime($data['date_added']));

            $datetime = new DateTime($data['date_history']);
            $current_date = new DateTime("now");
            $interval = date_diff($datetime,$current_date);
            $date_difference= $interval->format('%a');

            $row = array(
                      $i,
                      $data['suborder_id'],
                      trim($data['shipping_firstname'] . " " . $data['shipping_lastname']). "  ". $data['shipping_company'],
                      number_format($data['total'], 2, '.', ''),
                      $data['payment_code'],
                      date("d-m-Y", strtotime($data['date_added'])),
                      date("d-m-Y", strtotime($data['date_history'])),
                      $date_difference,
                      $data['courier_partner'],
                      $data['tracking_no']
                    );
            fputcsv($fp, $row);
            $i++;
        }
        fclose($fp);
        chmod($file,0777);

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        ;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->addCC(EMAIL_IDS['delhi_operations']['email_id'], EMAIL_IDS['delhi_operations']['name']);
        $mail->addCC(EMAIL_IDS['surat_operations']['email_id'], EMAIL_IDS['surat_operations']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);

        $mail->Subject = 'Shipped Order Summary (' . $today_date . ')';

        $mail->msgHTML($html);
        //Statement For adding attachment in mail
        $mail->AddAttachment($file);

        $mail->send(0,false);

        unlink($file);
        echo 'Complete';
    }

    public function notifySellerAboutOutOfStock() {
        $sql = "SELECT
                    p.product_id, p.model, p.sku, p.image, pd.name, mp.seller_id, ms.company
                FROM ".DB_PREFIX."product p
                INNER JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id)
                INNER JOIN ".DB_PREFIX."ms_product mp ON (p.product_id = mp.product_id)
                INNER JOIN ".DB_PREFIX."ms_seller ms ON (mp.seller_id = ms.seller_id)
                WHERE DATEDIFF( NOW(), p.date_out_of_stock ) = 7
                      AND p.quantity <= 0
                      AND ms.seller_status = 1
                      AND pd.language_id = 1";
        $data = $this->db->query($sql)->rows;
        $html = '';

        $se = array();
        foreach ($data as $product) {
            $seller_id = $product['seller_id'];
            $se[$seller_id][] = $product;
        }

        foreach ($se as $seller_id => $sp) {
            $html = $this->load->view($this->config->get('config_template') . '/template/mail/outofstockproductemail.tpl');
            $trdata = '';
            $i = 1;
            $seller_email = $this->MsLoader->MsSeller->getSellerEmail($seller_id);
            $seller_name = $sp[0]['company'];
            $html = str_replace("[SELLERNAME]", $seller_name, $html);

            foreach ($sp as $seller_p) {
                $image = $seller_p['image'];
                $sku = $seller_p['sku'];
                $name = $seller_p['name'];

                $trdata .= '<tr><td style="padding: 0 20px">' . $i . '</td><td style="padding: 0 20px"><img src = "' . HTTP_SERVER . "image/" . $image . '" width = "100px" height = "120px;"></td><td style="padding: 0 20px; width:200px; font-size:12px;">' . $name . '</td><td style="padding: 0 20px">' . $sku . '</td><td style="padding: 0 20px"><form action = "http://www.wholesalebox.in/index.php?route=account/login" method = "post"><input type = "hidden" name="redirect" value="http://www.wholesalebox.in/index.php?route=seller/manage-inventory&filters=' . $sku . '"><input type="submit" name="submit" value="Update"></form></td></tr>';
                $i++;
            }
            $html = str_replace("PRODUCTS", $trdata, $html);

            $mail = array();
            $mail = new PHPMailer();
            $mail->isSMTP();
            //$mail->maillerDebug = true;
            //$mail->SMTPDebug = 2;
            //$mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addAddress($seller_email, $seller_name);
            // Get list of additional emails
            $add_emails = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_additional_email WHERE customer_id = '" . (int) $seller_id . "'");
            if ($add_emails->rows) {
                foreach ($add_emails->rows as $row) {
                    $mail->addAddress($row['email'], $seller_name);
                }
            }
            $mail->Subject = 'WholesaleBox - Inventory Alert for Out of Stock products';
            $mail->msgHTML($html);
            $mail->send();
        }
    }

    /**
     * @author Vishnu Shekhawat
     * @description open task when an order has been delivered 7 days back or cancelled
     */
    public function reOpenTaskAfterOrder() {

        $this->load->language('account/sms_templates');
        $sql = "SELECT DISTINCT(ord.order_id),
                       GROUP_CONCAT( DISTINCT (osub.suborder_id) ) as suborders,
                       ord.order_no,
                       CONCAT(ord.firstname, ' ',ord.lastname) as buyer_name,
                       ord.telephone,
                       ord.email,
                       ord.customer_id,
                       ord.shipping_address_1,
                       ord.shipping_company,
                       ord.shipping_city,
                       ord.shipping_country_id,
                       ord.shipping_postcode,
                       ord.shipping_zone_id,
                       ord.total,
                       ss.name as sales_name,
                       ss.telephone as sales_telephone,
                       ss.active_status,
                       ord.date_added as order_date

                FROM ".DB_PREFIX."order ord
                LEFT JOIN ".DB_PREFIX."suborder osub ON ord.order_id = osub.order_id
                LEFT JOIN ".DB_PREFIX."order_history oh ON ord.order_id = oh.order_id
                LEFT JOIN ".DB_PREFIX."sales_staff ss ON ord.sales_staff_id = ss.staff_id
                WHERE
                    oh.order_status_id IN (".
                                        (int)ORDER_STATUS['Delivered'].",".
                                        (int)ORDER_STATUS['Canceled'].",".
                                        (int)ORDER_STATUS['Complete']."
                                       )
                      AND DATEDIFF(NOW(),oh.date_added) = 7
                      AND ord.store_id IN (" . WSB_STORES_ID . ") GROUP BY ord.order_id";

        $data = $this->db->query($sql)->rows;
        foreach ($data as $key => $order) {

            /** Lead task need to be changed only after successful delivery/cancellation of all suborders  **/

            $suborders = explode(',',$order['suborders']);

            foreach($suborders as $suborder){
                $query = $this->db->query("SELECT date_added FROM ".DB_PREFIX."order_history
                                                 WHERE order_id = " . (int) $order['order_id'] .
                                                 " AND suborder_id = '". $suborder . "'".
                                                 " AND order_status_id IN (".
                                                        (int)ORDER_STATUS['Delivered'].",".
                                                        (int)ORDER_STATUS['Canceled'].",".
                                                        (int)ORDER_STATUS['Complete']."
                                                       )
                                                 ORDER BY date_added ASC LIMIT 1");
                if(!$query->num_rows){
                    unset($data[$key]);
                    break;
                }
            }
        }

        $this->load->model('lead/lead');
        $this->model_lead_lead->getLeadOrder($data);
    }

    public function outForDeliveryMoreThanOneDay() {
        $this->load->model('account/order');
        $results['results'] = $this->model_account_order->getOutForDeliveryOrders();
        if (!empty($results['results'])) {
            $html = $this->load->view($this->config->get('config_template') . '/template/mail/outForDeliveryMoreThanOneDay.tpl', $results);
            $today_date = date("d/F/Y");
            $file = DIR_DLOAD . 'OutForDeliveryOrders-' . date("dMY") . '.csv';
            $fp = fopen($file, 'w');

            $head = array(
                          'S. No.',
                          'Suborder No',
                          'Customer',
                          'Amount',
                          'Sales Person',
                          'Payment',
                          'Out for delivery',
                          'Days',
                          'Courier',
                          'Tracking No'
                        );
            fputcsv($fp, $head);

            $i=1;
            foreach ($results['results'] as $data){
                $data['date_history'] = date('Y-m-d', strtotime($data['date_history']));
                $data['date_added'] = date('Y-m-d', strtotime($data['date_added']));

                $datetime = new DateTime($data['date_history']);
                $current_date = new DateTime("now");
                $interval = date_diff($datetime,$current_date);
                $date_difference= $interval->format('%a');

                $row = array(
                          $i,
                          $data['suborder_id'],
                          trim($data['firstname'] . " " . $data['lastname']). "  ". $data['shipping_city'],
                          number_format($data['total'], 2, '.', ''),
                          $data['sales_staff_name'],
                          $data['payment_code'],
                          date("d-m-Y", strtotime($data['date_history'])),
                          $date_difference,
                          $data['courier_partner'],
                          $data['tracking_no']
                        );
                fputcsv($fp, $row);
                $i++;
            }
            fclose($fp);
            chmod($file,0777);


            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');

            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            if ( isset($this->request->get['mailTo']) && $this->request->get['mailTo'] == 'all' ){
                $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
                $mail->addAddress(EMAIL_IDS['south_sales']['email_id'], EMAIL_IDS['south_sales']['name']);
                $mail->addAddress(EMAIL_IDS['east_sales']['email_id'], EMAIL_IDS['east_sales']['name']);
                $mail->addAddress(EMAIL_IDS['north_sales']['email_id'], EMAIL_IDS['north_sales']['name']);
                $mail->addAddress(EMAIL_IDS['south_sales_support']['email_id'], EMAIL_IDS['south_sales_support']['name']);
                $mail->addAddress(EMAIL_IDS['west_sales_support']['email_id'], EMAIL_IDS['west_sales_support']['name']);
                $mail->addAddress(EMAIL_IDS['east_sales_support']['email_id'], EMAIL_IDS['east_sales_support']['name']);
                $mail->addAddress(EMAIL_IDS['north_sales_support']['email_id'], EMAIL_IDS['north_sales_support']['name']);
                $mail->addAddress(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
                $mail->addCC(EMAIL_IDS['manish']['email_id'], EMAIL_IDS['manish']['name']);
            }
            $mail->Subject = 'Out for delivery for more than 24 hours (' . $today_date . ')';
            $mail->msgHTML($html);
            //Statement For adding attachment in mail
            $mail->AddAttachment($file);
            $mail->send(0,false);
            unlink($file);

        } else {
            exit();
        }
    }

    // Cron to expire all Cashbacks which have gone past their validity days
    public function cashbackExpire() {
        $this->db->query("UPDATE ".DB_PREFIX."customer_cashback
                          SET expired = 1
                          WHERE DATEDIFF(NOW(), date_added) > validity
                            AND amount > 0
                            AND expired = 0");
    }

    public function notifySupportInfoToCustomerOnRegister() {

        if ($this->request->get['test'] == 1) {
            $sql = "SELECT customer_id, ws_gcm_registration_id
              FROM ".DB_PREFIX."customer
              WHERE customer_id IN(4042, 12163,198)";
        } else {
            $sql = " SELECT customer_id, ws_gcm_registration_id
                FROM ".DB_PREFIX."customer
                WHERE customer_id NOT IN
                (SELECT DISTINCT
                 customer_id from ".DB_PREFIX."wsb_notification_tracking
                 where notification_id = 4)
                 AND app_version >=24 AND ws_gcm_registration_id != ''
                  AND date_added >=DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        }

        $customer_data = $this->db->query($sql)->rows;

        $cust_gsm_id = array_column($customer_data, 'ws_gcm_registration_id');
        $cust_id = array_column($customer_data, 'customer_id');

        $data['message_type'] = 4;

        $this->load->language('cron/cron');
        $message = $this->language->get('message');
        $title = $this->language->get('title');


        $msg = array(
            'msg_type' => $data['message_type'],
            'message_2' => $message,
            'title' => $title,
            'subtitle' => '',
            'tickerText' => '',
            'vibrate' => 1,
            'sound' => 1
        );

        $notification = New Notification($cust_gsm_id, $msg);
        $res = $notification->sendPushNotification();
        // echo $res;

        $sql = "INSERT INTO " . DB_PREFIX . "notification_message SET
                        message_type = 4,
                        message = '" . $message . "',
                        send_date = now()";
        $this->db->query($sql);

        $last_notification_id = $this->db->getLastId();

        foreach ($cust_id as $value) {
            $this->InsertIntoWsbNotificationTracking($last_notification_id, $value, $message);
        }
    }

    private function InsertIntoWsbNotificationTracking($last_notification_id, $customer_id, $message) {
        $sql = "INSERT INTO " . DB_PREFIX . "wsb_notification_tracking SET
                        notification_id = " . $last_notification_id . ",
                        customer_id = " . $customer_id . ",
                        message = '" . $message . "',
                        date_added = now()";
        $this->db->query($sql);
    }

    // Send PN if user has items in wishlist but did not place order in last 24hrs
    public function sendPushNotificationIfItemInWishlist() {
        $sql = "SELECT ocw.customer_id, oc.ws_gcm_registration_id
                FROM ".DB_PREFIX."customer_wishlist as ocw
                INNER JOIN
                ".DB_PREFIX."customer as oc
                ON(ocw.customer_id = oc.customer_id)
                WHERE
                ocw.date_added <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                AND oc.app_version <=24";

        $customer_data = $this->db->query($sql)->rows;

        $cust_gsm_id = array_column($customer_data, 'ws_gcm_registration_id');

        $data['message_type'] = 4;

        $this->load->language('cron/cron');
        $message = $this->language->get('message');

        $title = $this->language->get('title');

        $msg = array(
            'msg_type' => $data['message_type'],
            'message_2' => $message,
            'title' => $title,
            'subtitle' => '',
            'tickerText' => '',
            'vibrate' => 1,
            'sound' => 1);

        $notification = New Notification($cust_gsm_id, $msg);
        $res = $notification->sendPushNotification();

    }

    private function getAlphaNumericString($string) {
       return  preg_replace('/[^A-Za-z0-9\- ]/', '', $string); // Removes special chars.
    }

    public function addingLeadAfterAnHour() {
        $this->load->model('lead/lead');
        $this->load->model('account/customer');
        $this->load->model('checkout/order');
        $last_hour_customers = $this->model_account_customer->getCustomersRegisteredBeforeOneHour();

        foreach ($last_hour_customers as $last_hour_customer) {
            $lead_data = array();
            if (empty($last_hour_customer['telephone'])) {
                continue;
            }

            if ((strtotime(date('Y-m-d H:i:s')) - strtotime($last_hour_customer['date_added'])) / (60 * 60) >= 1) {

                $ordered = $this->model_checkout_order->getOrderOfCustomer($last_hour_customer['customer_id']);

                $campaign_event_no = '';
                $state_id = $last_hour_customer['address_zone_id'];
                $country_id = $last_hour_customer['address_country_id'];
                $zip = $last_hour_customer['address_zip'];
                $address = $last_hour_customer['address_address'];
                if (!empty($last_hour_customer['address_city'])) {
                    $city_id = $this->model_lead_lead->getCrmCityId($last_hour_customer['address_city']);
                } if ($ordered) {
                    $order_no = $ordered['order_no'];
                }
                $check_lead = $this->model_lead_lead->getLeadDetailsInCrm($last_hour_customer['telephone']);

                if (!empty($check_lead['user_id'])) {
                    $user_id = $check_lead['user_id'];
                } else {
                    // getting user of cityptoducyt
                    /*
                     * No need to fetch tse user from city as now new leads assigned to ROHIT tl users
                    if (isset($city_id) && !empty($city_id)) {
                        $user_id = $this->model_lead_lead->getTseUserFromCity($city_id);
                    }
                     *
                     */
                }
                $refer_by_customer_id = 0;
                $lead_user_id = 0;
                if(!empty(trim($last_hour_customer['referral_code']))){

                    $refer_by_customer_id = $this->model_account_customer->getReferByCustomerId($last_hour_customer['referral_code']);
                    $refer_lead_data = array();
                    if(!empty($refer_by_customer_id)){
                        $refer_lead_data = $this->model_lead_lead->getLeadIdUsingCustomerInCrm($refer_by_customer_id);
                    }

                    if(!empty($refer_lead_data)){
                        $lead_user_id  = $refer_lead_data['user_id'];
                    }
                }


                $is_registered = 2;
                $rohit_tl_user_id = ROHIT_TL_USER_ID;
              //  $random_tl_user = $this->model_lead_lead->get_random_TL();
               // $random_tl_user = $this->model_lead_lead->getTlRandomUserId($rohit_tl_user_id);

                /* No need to fetch random user id as all fresh leads user will be assign to shyam sundar
                $random_tl_user = $this->model_lead_lead->getDesktopDialerRandomUserId();


                if(empty($random_tl_user)){

                    if (isset($city_id) && !empty($city_id)) {
                        $user_id = $this->model_lead_lead->getTseUserFromCity($city_id);
                    }

                    if(empty($user_id)){
                        $user_id = $this->model_lead_lead->get_random_TL();
                    }
                }
                 *
                 */

                # define dropshipper user_id
                if (isset($last_hour_customer['is_dropshipper']) && $last_hour_customer['is_dropshipper'] > 0) {
                    $dropshipper_holder = 36;
                    $lead_user_id = $dropshipper_holder; //uma
                } 
                /*elseif ($last_hour_customer['store_id'] == 2) {
                    $manish_user_id = 134;
                    $lead_user_id = $manish_user_id; //uma
                } */
                else if(empty($lead_user_id)) {
                    $assign_to_user_id = $this->model_lead_lead->getAssignedToUserIdForLead();
                    $lead_user_id = (!empty($assign_to_user_id)) ? $assign_to_user_id : SHYAM_SUNDAR_CRM_USER_ID;
                }

                $lead_data = [
                    'name' => $this->getAlphaNumericString($last_hour_customer['name']),
                    'business_name' => $this->getAlphaNumericString($last_hour_customer['name']),
                    'user_id' => $lead_user_id,
                    'is_dropshipper' => (isset($last_hour_customer['is_dropshipper']) && !empty($last_hour_customer['is_dropshipper'])) ? $last_hour_customer['is_dropshipper'] : 0,
                    'city_id' => (isset($city_id) && !empty($city_id)) ? $city_id : 0,
                    'country_id' => !empty($country_id) ? $country_id : 99,
                    'state_id' => (!empty($state_id) && !empty($city_id)) ? $state_id : 0,
                    'zip' => (!empty($zip) && !empty($city_id)) ? $zip : 0,
                    'priority' => 1,
                    'refer_by_customer_id'=>$refer_by_customer_id,
                    'campaign_event_no' => (isset($campaign_event_no) && !empty($campaign_event_no) ) ? $campaign_event_no : 0,
                    'lead_show' => 1,
                    'is_registered' => "'" . $is_registered . "'",
                    'ip' => (isset($last_hour_customer['ip']) && !empty($last_hour_customer['ip'])) ? $last_hour_customer['ip'] : 0,
                    'app_installed' => !empty($last_hour_customer['ws_access_token']) ? 1 : 0,
                    'app_install_date' => !empty($last_hour_customer['ws_access_token']) ? Date('Y-m-d H:i:s') : NULL,
                    'signup_date' => $last_hour_customer['date_added'],
                    'gst' => $last_hour_customer['gst_number']??NULL
                ];


                if ($this->model_lead_lead->addLead($lead_data, $last_hour_customer['telephone'], $last_hour_customer['customer_id'], 'Initalising lead in CRM') === true) {
                    $this->model_account_customer->leadInserted($last_hour_customer['customer_id']);

                    if ($ordered) {
                        $lead_datas = array();
                        $lead_datas = [
                            'status' => 'ORDERED',
                            'is_ordered' => '1',
                            'order_date' => isset($ordered['order_date']) ? $ordered['order_date'] : '',
                            'order_no' => isset($order_no) ? $order_no : '',
                            'campaign_event_no' => $campaign_event_no
                        ];

                        $this->model_lead_lead->updateLead($lead_datas, $last_hour_customer['telephone'], 'Ordered Within an hour', $last_hour_customer['customer_id']);
                    }
                }
            }
        }
        die("Herer");
    }

    //create cron for email alerts on return not received
    // by sudhanshu/ vikas , 2017
    //Updated By: Nishu, June 2018
    public function mailToOperationsForReturnNotReceived() {
        //Default value is set to 5 Days
        $days_diff = 5;
        if(!empty($this->request->get['days']) ){
            $days_diff = $this->request->get['days'];
        }

        $sql = "
                SELECT
                    ocr.return_id,
                    ocr1.return_date AS return_request_date,
                    ocr.date_added,
                    ocr.order_product_id,
                    ocr.return_reason_id,
                    ocr.master_return_id,
                    ocr.quantity,
                    ocr.return_action_id,
                    ora.name AS return_action_name,
                    ocr.debit_note_id,
                    o.order_no,
                    CONCAT(o.payment_firstname,
                            ' ',
                            o.payment_lastname) AS customer_name,
                    o.shipping_city,
                    ocr.shipping_method,
                    SUM(ocr.quantity * (oop.price_per_piece + oop.discount_per_piece) * (1 + (CAST(oop.output_tax_rates AS DECIMAL (10 , 2 )) / 100))) AS tentative_refund_amount,
                    rst.courier_company,
                    rst.tracking_no
                FROM
                    ".DB_PREFIX."return AS ocr
                        INNER JOIN
                    ".DB_PREFIX."return_action AS ora ON ora.return_action_id = ocr.return_action_id
                        INNER JOIN
                    ".DB_PREFIX."order_product AS oop ON oop.order_product_id = ocr.order_product_id
                        INNER JOIN
                    ".DB_PREFIX."order AS o ON o.order_id = oop.order_id
                        INNER JOIN
                    (SELECT
                        MAX(return_id) AS return_id,
                            MIN(date_added) AS return_date,
                            order_product_id,
                            master_return_id
                    FROM
                        ".DB_PREFIX."return AS inner_ocr
                    GROUP BY order_product_id , master_return_id) AS ocr1 ON ocr.return_id = ocr1.return_id
                        LEFT JOIN
                    ".DB_PREFIX."return_shipment_tracking AS rst ON rst.shipping_id = ocr.return_shipment_tracking_id
                WHERE
                   ocr.return_action_id NOT IN ( ". implode(',', CLOSED_ACTION_IDS) .")
                        AND DATEDIFF(NOW(), ocr1.return_date) >= ". $days_diff ."
                        AND DATE(o.date_added) >= DATE('2017-05-01')
                GROUP BY o.order_id, ocr.return_shipment_tracking_id, ocr.return_action_id
                ORDER BY ocr1.return_date
            ";

        $return_query = $this->db->query($sql);
        if ($return_query->num_rows) {
            $return_mail = array();

            foreach ($return_query->rows as $info) {
                if(!isset($return_mail[$info['return_action_name']])){
                    $return_mail[$info['return_action_name']] = array();
                }

                $return_mail[$info['return_action_name']][] = array(
                    'order_no' => $info['order_no'],
                    'customer_name' => $info['customer_name'],
                    'shipping_city' => $info['shipping_city'],
                    'tentative_refund_amount' => $this->currency->format($info['tentative_refund_amount'], 'INR', 1),
                    'return_request_date' => date('d-m-Y', strtotime($info['return_request_date'])),
                    'shipping_method' => $info['shipping_method'],
                    'courier_company' => $info['courier_company'],
                    'tracking_no'     => $info['tracking_no']
                );
            }

            // Sending mail to operation
            $html = MailTemplate::mailToOperationsForReturnNotReceived($return_mail);

            $subject = 'Approved Return Request status pending for more than '.$days_diff.' day(s) :'. date('d-m-Y H:i:s');

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            ;
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addCC(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->Subject = $subject;
            $mail->msgHTML($html);
            $mail->send();
            echo 'Mail Sent';
        }
    }

    /**
     * Auto cancelation of Orders lying Pending for over 7 days (due to non payment confirmation)
     * International orders are not being canceled due to longer payment transfer cycle
     */

    public function autoCancelOrders(){

        $days = (!empty($this->request->get['days']))
                  ? (int)$this->request->get['days']
                  : 7 ;

        $sql = "SELECT osub.order_id,
                       osub.suborder_id
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id
                WHERE DATEDIFF(NOW(), osub.date_added) > ".$days."
                  AND osub.order_status_id = ".(int)ORDER_STATUS['Pending']."
                  AND osub.shipping_method NOT LIKE '%store%'
                  AND o.store_id = 0
                  AND o.stock_transfer = 0
                  AND o.franchise_id = 0";
        $query = $this->db->query($sql);

        $this->load->model('checkout/order');
        foreach ($query->rows as $key => $value) {
            $value["order_status_id"] = (int)ORDER_STATUS['Canceled'];
            $value["notify_email"] = 1;
            $value["notify_sms"] = 1;
            $value["comment"] = "Your Order is Canceled, since no payment confirmation has been done, even after ".$days." days of placing the order.";

            $value["auto_cancel"] = 1;
            $value["notes"] =  "Auto-Cancellation of Order in Pending for ".$days."+ days";

            $this->model_checkout_order->addOrderHistory($value);
        }
    }

    /**
     * Cron Job to Inform Unrated Active Sellers
     */
    public function informUnratedActiveSellers(){

        $sql = "SELECT oms.seller_id,
                       oms.nickname,
                       oms.company
                FROM " . DB_PREFIX . "ms_seller oms
                INNER JOIN " . DB_PREFIX . "ms_product omp ON omp.seller_id = oms.seller_id
                LEFT JOIN
                  ".DB_PREFIX."review_rules orr ON orr.seller_id = oms.seller_id
                WHERE
                  oms.seller_status = 1 AND orr.rule_type IS NULL
                GROUP BY oms.seller_id";
        $query = $this->db->query($sql);

        if($query->num_rows){
            $i = 1;
            $body  = 'Please find the list of unrated Active sellers: ';
            $body .= '</br></br>';
            $body .= '<table border="1">';
            $body .=    '<thead>';
            $body .=        '<tr>';
            $body .=            '<td>S.No</td>';
            $body .=            '<td>Seller Code</td>';
            $body .=            '<td>Company</td>';
            $body .=        '</tr>';
            $body .=    '</thead>';
            $body .=    '<tbody>';
            foreach ($query->rows as $key => $value) {
                $body .= '<tr>';
                $body .=    '<td>'.$i.'</td>';
                $body .=    '<td>'.$value['nickname'].'</td>';
                $body .=    '<td>'.$value['company'].'</td>';
                $body .=  '</tr>';
                $i++;
            }
            $body .=    '</tbody>';
            $body .=  '</table>';

            $mail = new  PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addReplyTo(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->Subject = "List of Unrated Active Sellers - " . date('d/M/Y', time());
            $mail->msgHTML($body);
            $mail->send();
        }
    }

    public function getOrdersShippedWithoutGeneratingInvoice(){
        $sql = "SELECT GROUP_CONCAT(osub.suborder_id SEPARATOR ', ') as suborders
                FROM ".DB_PREFIX."suborder osub
                INNER JOIN ".DB_PREFIX."order o ON osub.order_id = o.order_id
                WHERE
                osub.order_status_id NOT IN (".
                                         (int)ORDER_STATUS['Missing'].",".
                                         (int)ORDER_STATUS['Pending'].",".
                                         (int)ORDER_STATUS['Canceled'].",".
                                         (int)ORDER_STATUS['Processed'].",".
                                         (int)ORDER_STATUS['Tentative Processed'].")
                AND (osub.invoice_no = 0 OR osub.invoice_no IS NULL OR osub.buyer_invoice_id = 0 OR osub.buyer_invoice_id IS NULL)
                AND o.franchise_id = 0";
        $query = $this->db->query($sql);
        if( $query->num_rows && !empty($query->row['suborders']) ){
            $suborders = $query->row['suborders'];
            $body  = "Orders for which invoice is not generated, but they are marked Shipped: \n\n";
            $body .= $suborders;
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->Subject = "ALERT ! Orders Shipped without Generating Invoice - " . date('d/M/Y', time());
            $mail->Body = $body;
            $mail->send();
        } else {
            echo "No Issues Found !";
            exit();
        }
    }

    public function alertLongPendingOrders(){
        $days = $this->request->get['days'] ?? 5;

        $sql = " SELECT
                 o.order_no,
                 o.date_added,
                 o.total,
                 o.firstname,
                 o.lastname,
                 o.shipping_city,
                 o.currency_code,
                 o.currency_value,
                 o.payment_method,
                 o.payment_company,
                 GROUP_CONCAT(DISTINCT oss.crm_user_id) as crm_user_id,
                 GROUP_CONCAT(DISTINCT oss.name) as sales_staff_name
                 FROM  ".DB_PREFIX."order o
                 INNER JOIN ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
                 LEFT JOIN ".DB_PREFIX."order_sales_staff ooss ON (o.order_id = ooss.order_id)
                 LEFT JOIN ".DB_PREFIX."sales_staff oss on (ooss.sales_staff_id = oss.staff_id)
                 WHERE osub.order_status_id = ".(int)ORDER_STATUS['Pending']."
                   AND o.store_id IN (". WSB_STORES_ID . ")
                   AND DATEDIFF(NOW(), o.date_added) >= ".(int)$days."
                   AND o.franchise_id = 0
                 GROUP BY o.order_id ";

        $query = $this->db->query($sql);

        $j          = 0;
        $userData   = array();
        $apiData    = array();

        if($query->num_rows){
            foreach ( $query->rows as $value ) {
                    $i = 1;
                    $body  = 'Following order will get Auto-Canceled soon. Please get the payment ASAP: ';
                    $body .= '<table border="1">';
                    $body .=    '<thead>';
                    $body .=        '<tr>';
                    $body .=            '<td>S.No</td>';
                    $body .=            '<td>Order No</td>';
                    $body .=            '<td>Order Date</td>';
                    $body .=            '<td>Client Name</td>';
                    $body .=            '<td>Company Name</td>';
                    $body .=            '<td>Salesperson</td>';
                    $body .=            '<td>City</td>';
                    $body .=            '<td>Order Value</td>';
                    $body .=            '<td>Payment Method</td>';
                    $body .=        '</tr>';
                    $body .=    '</thead>';
                    $body .=    '<tbody>';
                    $body .= '<tr>';
                    $body .=    '<td>'.$i.'</td>';
                    $body .=    '<td>'.$value['order_no'].'</td>';
                    $body .=    '<td>'.$value['date_added'].'</td>';
                    $body .=    '<td>'.$value['firstname']." ".$value['lastname'].'</td>';
                    $body .=    '<td>'.trim($value['payment_company']).'</td>';
                    $body .=    '<td>'.(!empty($value['sales_staff_name']) ? $value['sales_staff_name'] : '' ).'</td>';
                    $body .=    '<td>'.trim($value['shipping_city']).'</td>';
                    $body .=    '<td>'.$this->currency->format($value['total'],$value['currency_code'],$value['currency_value']).'</td>';
                    $body .=    '<td>'.$value['payment_method'].'</td>';
                    $body .=  '</tr>';
                    $i++;
                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');
                    $mail->addAddress(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);

                    if( !empty($value['crm_user_id']) ) {
                        $this->load->model('lead/lead');
                        $result = $this->model_lead_lead->getCrmTeamLeadEmailId($value['crm_user_id']);
                        if($result->num_rows){
                            foreach($result->rows as $val){
                                $name   = $val['name'];
                                $email  = $val['email'];
                                $mail->addAddress($email, $name);
                            }
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
                        /*
                        * set message and user data for push notification
                        */
                        $message = 'Payment of '.$this->currency->format($value['total'],$value['currency_code'],$value['currency_value']).' for Order No '.$value['order_no'].' is pending. Customer '.$value['firstname'];

                        $pnData = array();
                        $pnData['msg_type']     = '6';
                        $pnData['message']      = $message;
                        $pnData['title']        = 'Payment Pending for Order '.$value['order_no'];
                        $pnData['tickerText']   = 'Wholesalebox';
                        $pnData['vibrate']      = 1;
                        $pnData['sound']        = 1;
                        $pnData['mobile']       = '';

                        $userData[$j]['type']               = 'crm_user';
                        $userData[$j]['id']                 = $value['crm_user_id'];
                        $userData[$j]['is_pn_to_send']      = true;
                        $userData[$j]['pn']                 = $pnData;
                        $j++;
                    }
                    $mail->Subject = "Payment Pending for Order No: " . $value['order_no'] . " - " . date('d/M/Y H:i:s', time());
                    $mail->msgHTML($body);
                    $mail->send();

             }
            /*
            * send Push Notification To Agent
            */
             if (!empty($userData) && count($userData)>0) {

                 $objDateTime                           = new DateTime();
                 $apiData['notification_sending_time']  = $objDateTime->format('Y-m-d 10:00:00');
                 $apiData['data']                       = $userData;
                 $jsonData                              = json_encode($apiData);
                 $url  = 'https://www.wholesalebox.biz/crmapi/Notifications/sendNotificationFromWeb';
                 $curl = curl_init();

                 // Set SSL if required
                 if (substr($url, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                 }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $url);

                if ($this->request->post) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('data'=>$jsonData)));
                }

                $json = curl_exec($curl);
                curl_close($curl);
             }
        }
    }

    /**
     * Cron find clients who have not ordered in last 30 days or send  mail to the interal team
     */
    public function alertNonRepeatingUntaggedClients(){

        $sql = "SELECT o.firstname,
                       o.lastname,
                       o.shipping_city,
                       MAX(o.date_added) AS last_order_date,
                       oss.sales_staff_id
                FROM  ". DB_PREFIX ."order o
                LEFT JOIN " . DB_PREFIX . "order_sales_staff oss ON oss.order_id = o.order_id
                WHERE o.store_id IN (" . WSB_STORES_ID . ")
                GROUP BY o.customer_id
                HAVING DATEDIFF(NOW(), last_order_date) = 30
                   AND (oss.sales_staff_id = 82
                     OR oss.sales_staff_id = 0
                     OR oss.sales_staff_id IS NULL)
                ORDER BY o.date_added DESC";
        $query = $this->db->query($sql);

        if($query->num_rows){
            $body  = '';
            $i     = 1;
            foreach ($query->rows as $key => $value) {
                $body .= $i.". ".$value["firstname"]." ".
                         $value["lastname"]." " .
                         "last order on ".$value['last_order_date']."\n\n";
                $i++;
            }

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
            $mail->addAddress(EMAIL_IDS['field_sales_support']['email_id'], EMAIL_IDS['field_sales_support']['name']);
            $mail->addAddress(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
            $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->Subject = "WSB Support / Untagged Clients NOT Ordering in 30 Days - " . date('d/M/Y', time());
            $mail->Body = $body;
            $mail->send();
     }
    }

    /**
      *  Cron of sellers whose inventory is live and they have not touched the panel in last 15 days or send mail to internal team
    */
    public function informSellersNotActiveOnInventory(){
        $sql = "SELECT
                  oms.seller_id,
                  oms.nickname,
                  oms.company,
                  oms.city
                FROM
                  ".DB_PREFIX."ms_seller oms
                INNER JOIN
                  ".DB_PREFIX."ms_product omp ON omp.seller_id = oms.seller_id
                INNER JOIN
                  ".DB_PREFIX."product op ON op.product_id = omp.product_id
                WHERE
                  oms.seller_status = 1 AND oms.vacation_mode = 0 AND op.quantity > 0 AND op.stock_status_id != 6 AND op.status = 1
                GROUP BY
                  oms.seller_id";
        $query = $this->db->query($sql);
        if($query->num_rows){
            $seller_ids     = array_column($query->rows, 'seller_id');
            $seller_details = array_combine($seller_ids, $query->rows);
            $seller_ids     = implode(",",$seller_ids);
            $sql = "SELECT
                      seller_id
                    FROM
                      ".DB_PREFIX."seller_change_log
                    WHERE
                      seller_id IN(" . $seller_ids . ")
                    GROUP BY
                      seller_id
                    HAVING
                      DATEDIFF(NOW(),
                      MAX(modified)) >= 15";
            $query = $this->db->query($sql);
            if($query->num_rows){
                $i = 1;
                $body  = '<table border="1">';
                $body .=    '<thead>';
                $body .=        '<tr>';
                $body .=            '<td>S.No</td>';
                $body .=            '<td>Seller Code</td>';
                $body .=            '<td>Company</td>';
                $body .=            '<td>Seller City</td>';
                $body .=        '</tr>';
                $body .=    '</thead>';
                $body .=    '<tbody>';

                foreach ($query->rows as $key => $value) {
                    $body .= '<tr>';
                    $body .=    '<td>'.$i.'</td>';
                    $body .=    '<td>'.$seller_details[$value['seller_id']]['nickname'].'</td>';
                    $body .=    '<td>'.$seller_details[$value['seller_id']]['company'].'</td>';
                    $body .=    '<td>'.$seller_details[$value['seller_id']]['city'].'</td>';
                    $body .=  '</tr>';
                    $i++;

                }

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
                $mail->addAddress(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
                $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
                $mail->Subject = "Sellers Not Updating Inventory For 15+ days - " . date('d/M/Y', time());
                $mail->msgHTML($body);
                $mail->send();
            }
        }
    }

    public function filterProducts(){
        if(SOLR_ENABLED){
            require_once(DIR_SYSTEM."library/solr/product.php");
            $solr = new SolrProduct($this);
            $data['filter_old_days_products'] = 60;
            $data['filter_limit'] = 100;

            $start = 0;
            $product_total = 1;
            $check = 1;
            $solr_data = array();
            while($start < $product_total){

                $filter_data = array(
                    'sort'  => 'p.date_added',
                    'order' => 'DESC',
                    'filter_old_days_products' => 60,
                    'start' => $start,
                    'limit' => 100
                );
                $result = $solr->getProductFromSolr($filter_data);
                $start += 100;
                $product_total = $result['product_total'];
                $product_ids = array_column($result['products'],'product_id');
                $product_ids = implode(",",$product_ids);
                $sql = "SELECT
                          oop.product_id
                        FROM
                          ".DB_PREFIX."order_product oop
                        INNER JOIN
                          ".DB_PREFIX."order o ON oop.order_id = o.order_id
                        WHERE
                          oop.product_id IN($product_ids)
                        GROUP BY
                          oop.product_id
                        HAVING
                          DATEDIFF(NOW(),
                          MAX(o.date_added)) < 60";
                $result = $this->db->query($sql);
            }
        }
    }

    public function staffMasterOtp(){
       $query = $this->db->query("select staff_id from " . DB_PREFIX . "sales_staff");
         foreach ($query->rows as $key => $value)
         {
           $otp = rand(1000,9999);
           $this->db->query("UPDATE " . DB_PREFIX . "sales_staff SET master_otp='".$otp."', master_otp_last_updated='".date("Y-m-d h:i:s")."' where staff_id = '".$value['staff_id']."'");
         }
    }

    // Generating Store Voucher
    public function generateDailyStoreVoucher(){
        //$voucher_code = $this->genRandomString(6);
        $sql = "UPDATE  " .DB_PREFIX . "sales_staff
                SET store_voucher  = LCASE(lpad(conv(floor(rand()*pow(36,6)), 10, 36), 6, 0)),
                    store_voucher_last_updated = now(),
                    store_delivery = LCASE(lpad(conv(floor(rand()*pow(36,6)), 10, 36), 6, 0)),
                    store_delivery_last_updated = now()
                WHERE store_code  !=  'N/A' AND
                      active_status = 1";

        $this->db->query($sql);

        $sql = "UPDATE  " .DB_PREFIX . "sales_staff
                SET store_voucher  = '',
                    store_voucher_last_updated = now(),
                    store_delivery = '',
                    store_delivery_last_updated = now()
                WHERE store_code  =  'N/A' OR
                      active_status != 1";
        $this->db->query($sql);
    }

    public function alertInStockPurchasedInventory(){

        $temp = 0;
        $start = 0;
        $limit = 500;

        $store_to_products = array();

        while( $temp != 1 ){

            $sql = "SELECT owpb.product_id
                    FROM ". DB_PREFIX."wsb_purchase_breakup owpb
                    INNER JOIN ". DB_PREFIX."product op
                      ON owpb.product_id = op.product_id
                    WHERE op.quantity > 0";
            if( !empty($this->request->get['store'])) {
                $sql .= " AND op.store_sales ='" . $this->db->escape($this->request->get['store']). "'";
            }

            $sql .= " GROUP BY owpb.product_id
                      ORDER BY owpb.product_id ASC
                      LIMIT $start, $limit";
            $query = $this->db->query($sql);

            if( $query->num_rows ){
                $product_sql = "SELECT op.product_id,
                                       op.model,
                                       op.selling_price,
                                       op.quantity,
                                       op.quantity * op.piece_in_set AS available_pieces ,
                                       op.image,
                                       op.store_sales,
                                       op.price,
                                       op.hsn_code,
                                       op.mrp,
                                       (op.quantity * op.piece_in_set * op.price) AS total_purchased_value,
                                       op.sor_product
                            FROM ".DB_PREFIX."product op
                            WHERE op.product_id IN (". implode(',', array_column($query->rows, 'product_id')).")";
                $product_query = $this->db->query($product_sql);

                array_walk($product_query->rows, function($key,$value) use(&$store_to_products){
                    $store_to_products[$key['store_sales']][] = $key;
                });

                if(count(array_values($query->rows)) < 500){
                    $temp = 1;
                } else {
                    $start = $start + 500;
                }
            } else {
                $temp = 1;
            }
        }


        if(!empty($store_to_products)){

            $this->load->model('tool/image');

            $tax = new Tax($this->registry);

            $body  = 'Please find the list of In-Stock Purchased Inventory: ';
            $body .= '</br></br>';

            $body .= '<table border="1">';
            $body .=    '<thead>';
            $body .=        '<tr>';
            $body .=            '<td>S.No</td>';
            $body .=            '<td>Store</td>';
            $body .=            '<td>Available Designs</td>';
            $body .=            '<td colspan=3>Available Sets</td>';
            $body .=            '<td colspan=3>Available Pieces</td>';
            $body .=            '<td colspan=3>Total Available Base Product Value</td>';
            $body .=            '<td colspan=3>Total Stock Value</td>';
            $body .=        '</tr>';
            $body .=        '<tr>';
            $body .=            '<td>&nbsp;</td>';
            $body .=            '<td>&nbsp;</td>';
            $body .=            '<td>&nbsp;</td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td width="30%">SOR</td>
                                                <td width="30%">Non-SOR</td>
                                                <td width="30%">Total</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td width="30%">SOR</td>
                                                <td width="30%">Non-SOR</td>
                                                <td width="30%">Total</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td width="30%">SOR</td>
                                                <td width="30%">Non-SOR</td>
                                                <td width="30%">Total</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td width="30%">SOR</td>
                                                <td width="30%">Non-SOR</td>
                                                <td width="30%">Total</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=        '</tr>';
            $body .=    '</thead>';
            $body .=    '<tbody>';

            // totals
            $total_available_set = 0;
            $total_sor_available_set     = 0;
            $total_non_sor_available_set = 0;

            $total_available_pieces = 0;
            $total_sor_available_pieces = 0;
            $total_non_sor_available_pieces = 0;

            $total_base_prod_value = 0;


            $total_all_stock_value = 0;



            $j = 1;
            foreach($store_to_products as $store_key => $product_datas){

                $body .= '<tr>';
                $body .=    '<td>'.$j.'</td>';
                $body .=    '<td>'.$store_key.'</td>';
                $body .=    '<td align="center">'.count($product_datas).'</td>';


                $available_set          = 0;
                $sor_available_set      = 0 ;
                $non_sor_available_set  = 0 ;


                $available_pieces          = 0;
                $sor_available_pieces      = 0 ;
                $non_sor_available_pieces  = 0 ;


                $base_product_value        = 0;
                $sor_base_product_value    = 0 ;
                $non_sor_base_product_value= 0 ;


                $total_stock_value         = 0;
                $sor_total_stock_value     = 0 ;
                $non_sor_total_stock_value = 0 ;


                foreach($product_datas as $product_data){
                    $seller_tax_rate = $tax->getTaxRateForTaxIncludedPrice($product_data['price'],$product_data['hsn_code'],$product_data['mrp']);
                    $base_price = round($product_data['price']/(1+($seller_tax_rate/100)),2);

                    if($product_data['sor_product']){
                        $sor_available_set          += $product_data['quantity'];
                        $sor_available_pieces       += $product_data['available_pieces'];
                        $sor_base_product_value     += ($base_price * $product_data['available_pieces']);
                        $sor_total_stock_value      += $product_data['total_purchased_value'];
                    } else {
                        $non_sor_available_set      += $product_data['quantity'];
                        $non_sor_available_pieces   += $product_data['available_pieces'];
                        $non_sor_base_product_value += ($base_price * $product_data['available_pieces']);
                        $non_sor_total_stock_value  += $product_data['total_purchased_value'];
                    }

                    $available_set += $product_data['quantity'];
                    $available_pieces += $product_data['available_pieces'];

                    $base_product_value += ($base_price * $product_data['available_pieces']);

                    $total_stock_value += $product_data['total_purchased_value'];
                }
                $body .=    '<td colspan=3>
                                <table border=1 width="100%" style="border:none;">
                                    <tr>
                                        <td width="30%">' . round($sor_available_set,2) . '</td>
                                        <td width="30%">' . round($non_sor_available_set,2) . '</td>
                                        <td width="30%">' . round($available_set,2) . '</td>
                                    </tr>
                                </table>
                            </td>';
                $body .=    '<td colspan=3>
                                <table border=1 width="100%" style="border:none;">
                                    <tr>
                                        <td width="30%">' . round($sor_available_pieces,2) . '</td>
                                        <td width="30%">' . round($non_sor_available_pieces,2) . '</td>
                                        <td width="30%">' . round($available_pieces,2) . '</td>
                                    </tr>
                                </table>
                            </td>';
                $body .=    '<td colspan=3>
                                <table border=1 width="100%" style="border:none;">
                                    <tr>
                                        <td width="30%">' . round($sor_base_product_value,2) . '</td>
                                        <td width="30%">' . round($non_sor_base_product_value,2) . '</td>
                                        <td width="30%">' . round($base_product_value,2) . '</td>
                                    </tr>
                                </table>
                            </td>';
                $body .=    '<td colspan=3>
                                <table border=1 width="100%" style="border:none;">
                                    <tr>
                                        <td width="30%">' . round($sor_total_stock_value,2) . '</td>
                                        <td width="30%">' . round($non_sor_total_stock_value,2) . '</td>
                                        <td width="30%">' . round($total_stock_value,2) . '</td>
                                    </tr>
                                </table>
                            </td>';
                $body .=  '</tr>';
                $j++;

                $total_sor_available_set     += $sor_available_set;
                $total_non_sor_available_set += $non_sor_available_set;
                $total_available_set         += $available_set;

                $total_sor_available_pieces     += $sor_available_pieces;
                $total_non_sor_available_pieces += $non_sor_available_pieces;
                $total_available_pieces         += $available_pieces;


                $total_sor_base_product_value       += $sor_base_product_value;
                $total_non_sor_base_product_value   += $non_sor_base_product_value;
                $total_base_prod_value              += $base_product_value;


                $total_sor_total_stock_value     += $sor_total_stock_value;
                $total_non_sor_total_stock_value += $non_sor_total_stock_value;
                $total_all_stock_value           += $total_stock_value;

            }

            $body .=        '<tr>';
            $body .=            '<td colspan=2 align=center> <b>Total</b> </td>';
            $body .=            '<td> &nbsp; </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td><b>' . round($total_sor_available_set,2) . '</b></td>
                                                <td><b>' . round($total_non_sor_available_set,2) . '</b></td>
                                                <td><b>' . round($total_available_set,2).'</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td><b>' . round($total_sor_available_pieces,2) . '</b></td>
                                                <td><b>' . round($total_non_sor_available_pieces,2) . '</b></td>
                                                <td><b>'.  round($total_available_pieces,2).'</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td><b>' . round($total_sor_base_product_value,2) . '</b></td>
                                                <td><b>' . round($total_non_sor_base_product_value,2) . '</b></td>
                                                <td><b>'.  round($total_base_prod_value,2).'</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=            '<td colspan=3>
                                    <table border=1 width=100% style="border:none;">
                                        <tbody>
                                            <tr>
                                                <td><b>' . round($total_sor_total_stock_value,2) . '</b></td>
                                                <td><b>' . round($total_non_sor_total_stock_value,2) . '</b></td>
                                                <td><b>'.  round($total_all_stock_value,2).'</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>';
            $body .=        '</tr>';

            $body .=    '</tbody>';
            $body .=  '</table>';

            $file_name = DIR_DLOAD .'in_stock_purchase_inventory.csv';
            $fp = fopen($file_name, 'w');
            $data = array('S.No.',
                          'Product ID',
                          'WSB Product Code',
                          'Store',
                          'SOR',
                          'Available Sets',
                          'Available Pieces',
                          'Base Price',
                          'Tax Rate',
                          'Basic value of stock',
                          'Transfer Price per piece',
                          'Stock Value',
                         );
            fputcsv($fp, $data);
            $s_no = 1;

            foreach ( $store_to_products as $store => $products) {

                $store_to_products[$store]['sor_product_amt'] = 0;
                $store_to_products[$store]['non_sor_product_amt'] = 0;

                foreach ( $products as $product) {
                    if($product['sor_product']){
                        $store_to_products[$store]['sor_product_amt'] += $product['total_purchased_value'];
                    } else{
                        $store_to_products[$store]['non_sor_product_amt'] += $product['total_purchased_value'];
                    }
                }

                $store_to_products[$store]['total_purchases_value_storewise'] = array_sum(array_column($products, 'total_purchased_value'));
                $store_value = round($store_to_products[$store]['total_purchases_value_storewise'],2);

                $body .= '<h1>Store : '.$store.'</h1>';
                $body .= '<h1>Total Purchased Value :- SOR : '.round($store_to_products[$store]['sor_product_amt'],2).'&nbsp; Non-SOR: ' . round($store_to_products[$store]['non_sor_product_amt'],2) . ' &nbsp; Total: '. $store_value . '</h1>';
                $body .= '<table border="1">';
                $body .=    '<thead>';
                $body .=        '<tr>';
                $body .=            '<td>S.No</td>';
                $body .=            '<td>Product ID</td>';
                $body .=            '<td>WSB Product Code</td>';
                $body .=            '<td>Image</td>';
                $body .=            '<td>Store</td>';
                $body .=            '<td>SOR</td>';
                $body .=            '<td>Available Sets</td>';
                $body .=            '<td>Available Pieces</td>';
                $body .=            '<td>Base Price</td>';
                $body .=            '<td>Tax Rate</td>';
                $body .=            '<td>Basic value of stock</td>';
                $body .=            '<td>Transfer Price per piece</td>';
                $body .=            '<td>Stock Value</td>';
                $body .=        '</tr>';
                $body .=    '</thead>';
                $body .=    '<tbody>';

                $i = 1;
                foreach ( $products as $store => $product) {
                    $img = $this->model_tool_image->resize($product['image'], 150, 150);
                    $image = "<img src='" . $img . "' />";
                    $imgUrl = $this->url->link('product/product', '&product_id=' . $product['product_id'],'SSL');
                    $image = "<a href='". $imgUrl ."' target='_blank'>". $image ."</a>";

                    $seller_tax_rate = $tax->getTaxRateForTaxIncludedPrice($product['price'],$product['hsn_code'],$product['mrp']);

                    $base_price = (float)($product['price']/(1+($seller_tax_rate/100)));

                    $basic_value_of_stock = (float)($base_price * $product['available_pieces']);

                    if($product['sor_product']){
                        $sor_product = 'YES';
                    } else {
                        $sor_product = 'NO';
                    }


                    $body .= '<tr>';
                    $body .=    '<td>'.$i.'</td>';
                    $body .=    '<td>'.$product['product_id'].'</td>';
                    $body .=    '<td>'.$product['model'].'</td>';
                    $body .=    '<td>'.$image.'</td>';
                    $body .=    '<td>'.$product['store_sales'].'</td>';
                    $body .=    '<td>'.$sor_product.'</td>';
                    $body .=    '<td>'.$product['quantity'].'</td>';
                    $body .=    '<td>'.$product['available_pieces'].'</td>';
                    $body .=    '<td>'.round($base_price,2).'</td>';
                    $body .=    '<td>'.round($seller_tax_rate,2).'</td>';
                    $body .=    '<td>'.round($basic_value_of_stock,2).'</td>';
                    $body .=    '<td>'.round($product['price'],2).'</td>';
                    $body .=    '<td>'.round($product['total_purchased_value'],2).'</td>';
                    $body .=  '</tr>';


                    $data = array($s_no,
                                  $product['product_id'],
                                  $product['model'],
                                  $product['store_sales'],
                                  $sor_product,
                                  $product['quantity'],
                                  $product['available_pieces'],
                                  round($base_price,2),
                                  round($seller_tax_rate,2),
                                  round($basic_value_of_stock,2),
                                  round($product['price'],2),
                                  round($product['total_purchased_value'],2)
                                );
                    fputcsv($fp, $data);
                    $i++;
                    $s_no++;
                }

                $body .=    '</tbody>';
                $body .=  '</table>';
            }

            fclose($fp);

            $mail = new  PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addReplyTo(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);

            $mail->Subject = $store_name."List of In-Stock Purchased Inventory - " . date('d/M/Y H:i:s', time());
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);
            $mail->send(1,false);
            unlink($file_name);
        }
    }

    public function cleanUpTables() {

        // Clean up customer activity table for data older than 15 days
        $sql = "DELETE from ".DB_PREFIX."customer_activity
                WHERE date_added < DATE_SUB(NOW(), INTERVAL 15 DAY)";
        $this->db->query($sql);

    }

     /*
     * cron method which will run daily and find out all HSN codes
     * which are present in oc_product table but are missing
     * oc_hsn table. This will send a reminder mail to Madhur Bhaiya to update
     * the missing HSN Codes in oc_hsn table
     * */

     public function emailToInformNewHSNCodes() {

        $sql = "
                SELECT
                    p.hsn_code
                FROM
                    oc_product p
                WHERE
                    p.hsn_code IS NOT NULL AND p.hsn_code <> ''
                    AND NOT EXISTS (SELECT 1 FROM oc_hsn h WHERE h.hsn_code = p.hsn_code)
                ";

        $sql_result = $this->db->query($sql);

        if($sql_result->num_rows > 0) {

            $body = '<table border="1" width=100%>';
            $body .=    '<thead>';
            $body .=        '<tr>';
            $body .=            '<td>S.No</td>';
            $body .=            '<td>HSN Code</td>';
            $body .=        '</tr>';
            $body .=    '</thead>';
            $body .=    '<tbody>';
            $i  = 1;

            foreach($sql_result->rows as $row) {
                if ( !empty($row['hsn_code']) ) {
                    $body .= '<tr>';
                    $body .=    '<td>'.$i.'</td>';
                    $body .=    '<td>'.$row['hsn_code'].'</td>';
                    $body .=  '</tr>';
                    $i++;
                }
            }

            $body .=    '</tbody>';
            $body .=  '</table>';

            $text  = MailTemplate::getGeneralHeader();
            $text .= '<div style="width:680px;">';
            $text .= "Hi,</br></br>";
            $text .= "Please find the list of HSN Code which are present in product table but missing in HSN table.</br>";
            $text .= $body;
            $text .= MailTemplate::getGeneralFooter();
            $text .= '</div>';

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
            $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->Subject = 'WholesaleBox :: Missing HSN Codes - ' . date('d/M/Y', time());
            $mail->msgHTML($text);
            $mail->send();
        }
     }

     public function alertOrdersInProcessingForLong(){

        $day = 2;

        if(isset($this->request->get['day']) && (int)$this->request->get['day']>0){
          $day = (int)$this->request->get['day'];
        }

        $body  = "Following are the orders which are STILL in PROCESSED status for more than " . $day . " days: ";

         $sql = "SELECT ooh.suborder_id,
                        date(o.date_added) as order_date,
                        trim(concat(o.firstname, ' ', o.lastname)) as client_name,
                        o.shipping_city,
                        osub.total,
                        o.currency_code,
                        o.currency_value,
                        date(min(ooh.date_added)) as processing_date
                from ".DB_PREFIX."suborder osub
                inner join ".DB_PREFIX."order o on o.order_id = osub.order_id
                inner join ".DB_PREFIX."order_history ooh on ooh.order_id = osub.order_id
                where ooh.suborder_id = osub.suborder_id
                  and DATE(ooh.date_added) < DATE_SUB(NOW(),INTERVAL " . $day . "  DAY)
                  and osub.order_status_id IN (".
                                               (int)ORDER_STATUS['Processed'].",".
                                               (int)ORDER_STATUS['Tentative Processed'].")
                  and ooh.order_status_id IN (".
                                               (int)ORDER_STATUS['Processed'].",".
                                               (int)ORDER_STATUS['Tentative Processed'].")
                  and o.store_id IN (" . WSB_STORES_ID .")
                GROUP BY ooh.suborder_id
                ORDER BY o.date_added ASC
                ";

        $processed_orders =  $this->db->query($sql);


        $citydata = array();
        foreach ($processed_orders->rows as $key => $value) {
            $suborderid = explode('-', $value['suborder_id']);
            $citykey = substr($suborderid[1], 0,2);
            $citydata[$citykey][] = $value;
        }

        if(count($citydata)>0){

            foreach($citydata as $mainKey => $orderManin){
                $i = 1;

                $body .= '<table border="1" style="margin-top:20px;"width=100%>';
                $body .= '<caption style="text-align:left:">City '.$mainKey.'</caption>';
                $body .=    '<thead>';
                $body .=        '<tr>';
                $body .=            '<td>S.No</td>';
                $body .=            '<td>Suborder No</td>';
                $body .=            '<td>Order Date</td>';
                $body .=            '<td>Processing Date</td>';
                $body .=            '<td>Suborder Value</td>';
                $body .=            '<td>Client Name</td>';
                $body .=            '<td>City</td>';
                $body .=        '</tr>';
                $body .=    '</thead>';
                $body .=    '<tbody>';
                foreach ($orderManin as $key => $order) {


                    $body .= '<tr>';
                    $body .=    '<td>'.$i.'</td>';
                    $body .=    '<td>'.$order['suborder_id'].'</td>';
                    $body .=    '<td>'.DATE("d-m-Y",strtotime($order['order_date'])).'</td>';
                    $body .=    '<td>'.DATE("d-m-Y",strtotime($order['processing_date'])).'</td>';
                    $body .=    '<td>'.$this->currency->format($order['total'],
                                                               $order['currency_code'],
                                                               $order['currency_value'],
                                                               true,
                                                               2).'</td>';
                    $body .=    '<td>'.$order['client_name'].'</td>';
                    $body .=    '<td>'.$order['shipping_city'].'</td>';
                    $body .=  '</tr>';
                    $i++;
                }
                $body .=    '</tbody>';
                $body .=    '</table>';
            }

        }else{
             return true;
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->addAddress(EMAIL_IDS['surat_operations']['email_id'], EMAIL_IDS['surat_operations']['name']);
        $mail->addAddress(EMAIL_IDS['delhi_operations']['email_id'], EMAIL_IDS['delhi_operations']['name']);
        $mail->addAddress(EMAIL_IDS['surat_sellers']['email_id'], EMAIL_IDS['surat_sellers']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->Subject = 'Alert: Orders in Processing for Long - ' . date('d-m-Y');
        $mail->msgHTML($body);
        $mail->send();
     }


     /**
      * Updated BY Nishu (For admin_change_log)
      * 27 June 2018
     */
     public function sortOrderNotification() {

        $url = $this->request->get['route'];
        $sql = "SELECT op.product_id,
                       op.sort_order,
                       op.model,
                       oac.date_added,
                       DATEDIFF(NOW(), oac.date_added) as since_days
                FROM ".DB_PREFIX."admin_change_log oac
                INNER JOIN ".DB_PREFIX."product op ON oac.table_id = op.product_id
                INNER JOIN (
                            SELECT MAX(date_added) as max_date,
                                   table_id
                            FROM ".DB_PREFIX."admin_change_log
                            WHERE field_name = 'sort_order'
                            GROUP BY table_id
                           ) oapc2 ON oac.table_id = oapc2.table_id
                                  AND oac.date_added = oapc2.max_date

                WHERE op.sort_order = oac.new_value
                  AND oac.field_name = 'sort_order'
                  AND op.sort_order < 999
                  AND DATEDIFF(NOW(), oac.date_added) > 2
                  AND oac.table_name = 'oc_product'
                ORDER BY oac.date_added ASC";

        $query = $this->db->query($sql);
        if($query->num_rows) {
            $fetch_product_ids = array_column($query->rows, 'product_id');
            $this->db->query("UPDATE ".DB_PREFIX."product SET sort_order = 999 WHERE product_id IN (". implode(',', $fetch_product_ids) . ")");

            $records = array();
            foreach($query->rows as $value) {

                //Set Data to add into admin_change_log
                $admin_change_data                  = array();
                $admin_change_data['table_id']      = (int)$value['product_id'];
                $admin_change_data['user_id']       = 0;
                $admin_change_data['name']          = 'Automatic Cron';
                $admin_change_data['username']      = 'Automatic Cron';
                $admin_change_data['table_name']    = 'oc_product';
                $admin_change_data['source_field']  = 'cron';
                $admin_change_data['field_name']    = 'sort_order';
                $admin_change_data['ref_url']       = $url;
                $admin_change_data['old_value']     = $value['sort_order'];
                $admin_change_data['new_value']     = 999;
                $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                $admin_change_data['ip_address']    = $this->request->getIpAddress;
                $admin_change_data['file_location'] = $url;
                $admin_change_data['user_type']     = 'System';

                //Call dynamic static function for entry into admin change log
                CommonLib::addAdminChangeLog($this->db, $admin_change_data);

                $records[] = array(
                                'model'     => $value['model'],
                                'sort_order' => $value['sort_order'],
                                'date_added' => $value['date_added'],
                                'since_days' => $value['since_days']
                                );
            }
            $html = MailTemplate::listDataForSortOrderMail($records);
            $mailer_html = MailTemplate::mailContentsForSortOrderMailer($html);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
            $mail->addAddress(EMAIL_IDS['ashu']['email_id'], EMAIL_IDS['ashu']['name']);
            $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
            $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->Subject = 'Attention: SORT Order Reset to 999 - ' . date('d-m-Y');
            $mail->msgHTML($mailer_html);
            $mail->send();
        }
     }

     public function getTentativeOrdersBefore48Hours($suborder_id){
         if(!empty($suborder_id)){
             $sql = "SELECT
                    date_added AS process_date,
                    order_status_id
                    FROM
                    ".DB_PREFIX."order_history
                    WHERE suborder_id = '".$suborder_id."'
                     AND order_status_id = ".(int)ORDER_STATUS['Tentative Processed']."
                     AND date_added < DATE_SUB(NOW(),INTERVAL 48 HOUR)
                    ORDER BY date_added ASC
                    LIMIT 1";
            $result = $this->db->query($sql);
            if(!empty($result->row)){
                $result = $result->row;
            }else{
                $result = '';
            }
            return $result;
         }
     }


     /**
     * cron method which order Split By Seller Information Mail To Operation Team
     * @author: vikas, 2017
     * */
    public function orderSplitBySellerInformationMailToOperationTeam(){
        $this->load->model('tool/image');
        if(!empty($this->request->get['day'])){
            $day = $this->request->get['day'];
        } else {
            $day = 1;
        }

        $result = array();

        $sql = "SELECT o.order_no,
                       date(o.date_added) AS date_added,
                       oop.order_product_id,
                       oop.order_id,
                       oop.suborder_id,
                       oop.product_id,
                       oop.seller_id,
                       oop.seller_invoice_id,
                       oop.model,
                       oop.quantity,
                       oop.piece_in_set,
                       oop.edit_type,
                       oop.last_modified,
                       DATE(MIN(oh.date_added)) AS order_process_date
                FROM " . DB_PREFIX . "order_product oop
                INNER JOIN " . DB_PREFIX . "order o
                  ON (o.order_id = oop.order_id)
                INNER JOIN ".DB_PREFIX."order_history oh
                  ON (oh.order_id = o.order_id)
                WHERE datediff(NOW(), oop.last_modified) <= '" .$day . "'
                  AND (edit_type LIKE 'SELLER_NOT_SUPPLIED' OR edit_type LIKE 'SELLER_LATER_DISPATCH')
                  AND oh.suborder_id = oop.suborder_id
                  AND oh.order_status_id IN (
                                             ".(int)ORDER_STATUS['Processed'].",".
                                             (int)ORDER_STATUS['Tentative Processed'].")
                GROUP BY oop.product_id
                order by o.order_id DESC ";
        $query = $this->db->query($sql);

        if( $query->num_rows ){

            // Now getting seller_details
            $seller_ids = array_unique(array_column($query->rows, 'seller_id'));
            $sql = "SELECT ms.seller_id,
                           ms.nickname,
                           ms.company,
                           ms.city,
                           ms.pickup_city_code
                    FROM " . DB_PREFIX . "ms_seller ms
                    WHERE ms.seller_id IN (" . implode(',', $seller_ids) . ")";

            $seller_query = $this->db->query($sql);
            $result['sellers'] = array();

            foreach ($seller_query->rows as $seller) {
                $result['sellers'][$seller['pickup_city_code']][$seller['seller_id']] =array(
                                                                    'nickname' => $seller['nickname'],
                                                                    'company' => $seller['company'],
                                                                    'city' => ucfirst($seller['city']),
                                                                    'pickup_city_code' => $seller['pickup_city_code'],
                    );
            }

            // seller wise product list
            $seller_products_array = array();
            // foreach ($result['products'] as $key => $value) {
            foreach ($query->rows as $key => $value) {
                $sub_sql = "SELECT product_id , new, seller_id
                            FROM " . DB_PREFIX . "seller_change_log
                            WHERE seller_id = '" . (int)$value['seller_id'] . "'
                              AND product_id = '". (int)$value['product_id'] ."'
                              AND DATE(modified) >= '" . DATE($value['date_added']) . "'
                              AND DATE(modified) <= '" . DATE($value['order_process_date'])."'
                              AND new <= 0 ";
                $sub_query = $this->db->query($sub_sql);

                if( !$sub_query->num_rows ){
                    $seller_products_array[$value['seller_id']][$value['order_no']][] = $value;
                }
            }
            $result['seller_wise_product'] = $seller_products_array;
        }

        foreach($result['sellers'] as $seller_pickup_city_key => $seller_data_values){
            $html = '';
            $html = MailTemplate::orderSplitBySellerInformationMailToOperationTeam($seller_data_values, $result['seller_wise_product']);

            $subject = $seller_pickup_city_key . ' - Short supply by sellers - '. date('d/m/y');

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addReplyTo(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['email_id']);

            if(!empty(EMAIL_IDS['BD'][$seller_pickup_city_key])){
                foreach (EMAIL_IDS['BD'][$seller_pickup_city_key] as $index => $email_array) {
                    $mail->addAddress($email_array['email_id'], $email_array['name']);
                }
            }
            $mail->Subject = $subject;
            $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['email_id']);
            $mail->addCC(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['email_id']);
            $mail->msgHTML($html);
            $mail->send();
        }
    }


    public function getGatiDocketsUnused(){
        $sql = "SELECT count(id) as total FROM " . DB_PREFIX ."gati_dockets WHERE used = 0 ";
        $query = $this->db->query($sql);
        if($query->row['total'] <= 100 ){
            $msg = "Only ". $query->row['total'] ." gati dockets are available to use. Please arrange for fresh Gati docket series at EARLIEST.";
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
            $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->Subject = 'Alert :: Gati docket series are remaining ' . $query->row['total'];
            $mail->msgHTML($msg);
            $mail->send();
        }
    }

    /**
     * Cron: update Customer Preferences every day
     * updates prefs of previous day
     * @author Anurag Jain, 22nd Aug 2019
     */
    public function updateCustomerPreferences() 
    {    
        //Set apache execution time limit to infinite
        ini_set('max_execution_time', 0);

        $this->load->model("preferences");

        $type = "update";
        $this->model_preferences->updateCustomerPreferences( $type );

        echo "Preferences updated successfully !!!";
        exit();
    }

    /**
     * CRON: runs every 15th day
     * resets shortlist prefs for all customers with last 15 days data
     * @author Anurag Jain, 30nd Aug 2019
     */
    public function resetShortlistPreferencesByDuration()
    {
        //Set apache execution time limit to infinite
        ini_set('max_execution_time', 0);

        $this->load->model("preferences");
        
        $start_date = date( "Y-m-d", strtotime( "-15 days" )) . " 00:00:00";
        $end_date   = date( "Y-m-d", strtotime( "yesterday" )) . " 23:59:59";
        $pref_type = 'shortlist';

        $this->model_preferences->resetPreferenceTypeByDuration( $pref_type, $start_date, $end_date );

        echo "Shortlist Preferences updated successfully !!!";
        exit();
    }

    /**
     * CRON: runs every 15th day
     * resets product review prefs for all customers with last 15 days data
     * @author Anurag Jain, 30nd Aug 2019
     */
    public function resetProductReviewPreferencesByDuration()
    {
        //Set apache execution time limit to infinite
        ini_set('max_execution_time', 0);

        $this->load->model("preferences");
        
        $start_date = date( "Y-m-d", strtotime( "-15 days" )) . " 00:00:00";
        $end_date   = date( "Y-m-d", strtotime( "yesterday" )) . " 23:59:59";
        $pref_type = 'product_review';

        $this->model_preferences->resetPreferenceTypeByDuration( $pref_type, $start_date, $end_date );

        echo "Product Review Preferences updated successfully !!!";
        exit();
    }


    /**
     * seller fulfillment rate
     * @author: vikas, 2017
     * */
    public function sellerFulfillmentRate(){
        $sql_approved ="SELECT oop.seller_id,
                      ms.nickname,
                      ms.company,
                      SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total_sale,
                      SUM(oop.quantity*oop.piece_in_set) as total_quantity

                FROM ".DB_PREFIX."order_product oop
                INNER JOIN ".DB_PREFIX."ms_seller ms
                ON ms.seller_id = oop.seller_id
                WHERE oop.last_modified <= (NOW() - INTERVAL 1 DAY)
                AND oop.edit_type != 'YES'
                GROUP BY seller_id
                ORDER BY total_sale DESC";
        $query_approved = $this->db->query($sql_approved);

        $query_approved = array_combine(array_column($query_approved->rows, 'seller_id'), $query_approved->rows) ;

        $sql_not_approved ="SELECT oop.seller_id,
                      ms.nickname,
                      ms.company,
                      SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total_sale,
                      SUM(oop.quantity*oop.piece_in_set) as total_quantity

                FROM ".DB_PREFIX."order_product oop
                INNER JOIN ".DB_PREFIX."ms_seller ms
                ON ms.seller_id = oop.seller_id
                WHERE oop.last_modified <= (NOW() - INTERVAL 1 DAY)
                AND oop.edit_type IN ('SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH','REJECTED_WRONG_PRODUCT','REJECTED_SELLER_DAMAGE','REJECTED_WSB_DAMAGE')
                GROUP BY seller_id
                ORDER BY total_sale DESC";
        $query_not_approved = $this->db->query($sql_not_approved);
        $query_not_approved = array_combine(array_column($query_not_approved->rows, 'seller_id'), $query_not_approved->rows) ;

        $one_day_fullfillment = array();
        foreach($query_approved as $seller_id_key => $sellers_total_amt_data){
            $not_approved_total_quantity = ($query_not_approved[$seller_id_key]['total_quantity']) ? $query_not_approved[$seller_id_key]['total_quantity'] : 0.00;
            $not_approved_total_sale = ($query_not_approved[$seller_id_key]['total_sale']) ? $query_not_approved[$seller_id_key]['total_sale'] : 0.00;
            $one_day_fullfillment[$seller_id_key]['nickname'] = $sellers_total_amt_data['nickname'];
            $one_day_fullfillment[$seller_id_key]['company'] = $sellers_total_amt_data['company'];
            $one_day_fullfillment[$seller_id_key]['approved_total_quantity'] = $sellers_total_amt_data['total_quantity'];
            $one_day_fullfillment[$seller_id_key]['approved_total_sale'] = $sellers_total_amt_data['total_sale'];
            $one_day_fullfillment[$seller_id_key]['not_approved_total_quantity'] = $not_approved_total_quantity;
            $one_day_fullfillment[$seller_id_key]['not_approved_total_sale'] = $not_approved_total_sale;
            $one_day_fullfillment[$seller_id_key]['fullfillment_rate'] = number_format((float)($not_approved_total_sale*100/$sellers_total_amt_data['total_sale']),2) .'%';
        }

        $html  = '';
        $label = 'Seller Fulfillment Rate ( Till 1 day before )';
        $html .= MailTemplate::sellerFulfillmentRate($label, $one_day_fullfillment);



        $sql_approved_all ="SELECT oop.seller_id,
                      ms.nickname,
                      ms.company,
                      SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total_sale,
                      SUM(oop.quantity*oop.piece_in_set) as total_quantity

                FROM ".DB_PREFIX."order_product oop
                INNER JOIN ".DB_PREFIX."ms_seller ms
                ON ms.seller_id = oop.seller_id
                WHERE oop.edit_type IN ('SELLER_APPROVED','SELLER_PARTIAL')
                GROUP BY seller_id
                ORDER BY total_sale DESC";
        $query_approved_all = $this->db->query($sql_approved_all);
        $query_approved_all = array_combine(array_column($query_approved_all->rows, 'seller_id'), $query_approved_all->rows) ;


        $sql_not_approved_all ="SELECT oop.seller_id,
                      ms.nickname,
                      ms.company,
                      SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total_sale,
                      SUM(oop.quantity*oop.piece_in_set) as total_quantity

                FROM ".DB_PREFIX."order_product oop
                INNER JOIN ".DB_PREFIX."ms_seller ms
                ON ms.seller_id = oop.seller_id
                WHERE oop.edit_type IN ('SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH')
                GROUP BY seller_id
                ORDER BY total_sale DESC";
        $query_not_approved_all = $this->db->query($sql_not_approved_all);
        $query_not_approved_all = array_combine(array_column($query_not_approved_all->rows, 'seller_id'), $query_not_approved_all->rows) ;


        $overall_fullfillment = array();
        foreach($query_approved_all as $seller_id_key => $sellers_total_amt_data){
            $not_approved_total_quantity = ($query_not_approved_all[$seller_id_key]['total_quantity']) ? $query_not_approved_all[$seller_id_key]['total_quantity'] : 0.00;
            $not_approved_total_sale = ($query_not_approved_all[$seller_id_key]['total_sale']) ? $query_not_approved_all[$seller_id_key]['total_sale'] : 0.00;
            $overall_fullfillment[$seller_id_key]['nickname'] = $sellers_total_amt_data['nickname'];
            $overall_fullfillment[$seller_id_key]['company'] = $sellers_total_amt_data['company'];
            $overall_fullfillment[$seller_id_key]['approved_total_quantity'] = $sellers_total_amt_data['total_quantity'];
            $overall_fullfillment[$seller_id_key]['approved_total_sale'] = $sellers_total_amt_data['total_sale'];
            $overall_fullfillment[$seller_id_key]['not_approved_total_quantity'] = $not_approved_total_quantity;
            $overall_fullfillment[$seller_id_key]['not_approved_total_sale'] = $not_approved_total_sale;
            $overall_fullfillment[$seller_id_key]['fullfillment_rate'] = number_format((float)($not_approved_total_sale*100/$sellers_total_amt_data['total_sale']),2) .'%';
        }


        $label = 'Seller Fulfillment Rate ( Over All )';
        $html .= MailTemplate::sellerFulfillmentRate($label, $overall_fullfillment);


        $subject = 'Seller fullfillment rate';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');

        $mail->Subject = $subject;
        $mail->addCC(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
        $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
        $mail->msgHTML($html);
        $mail->send();
    }


    /**
    * Method for display field value on click to see. it's a common function.
    * we can use multiple place in adminpanel tpl files.
    * @author: vikas, 2017
    */
    public function fieldValueOnClickToSee(){
        $sql = "SELECT user_id,
                       username,
                       name,
                       COUNT(log_id) AS total_views,
                       COUNT(IF(field_name='email',1,NULL)) AS total_email_views,
                       COUNT(IF(field_name='telephone',1,NULL)) AS total_telephone_views
                FROM ".DB_PREFIX."admin_change_log
                WHERE date_added >= DATE_SUB(NOW(),INTERVAL 1 HOUR) AND table_name='oc_admin_info_log'
                GROUP BY user_id
                HAVING total_views >= 30
                ORDER BY NULL ";
        $query = $this->db->query($sql);
        $html = '';
        if( $query->num_rows ){
            $html .= MailTemplate::fieldValueOnClickToSee($query->rows);

            $subject = 'ALERT: Contact Details Seen more than 30 Times - ' . date("F j, Y, g:i a"); ;

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');

            $mail->Subject = $subject;
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
            $mail->msgHTML($html);
            $mail->send();
        }

    }

    /**
    * Cron to send mail of those, debit notes don't have credit notes
    * @param: void
    * @return: triggering mail
    * @author: Nishu, August 2017
    */
    public function getDNsNotHavingCN() {

        $html = '';
        $sql = "
                SELECT
                    GROUP_CONCAT(DISTINCT dn.debit_note_no, ' ') AS debit_note_no,
                    GROUP_CONCAT(DISTINCT dn.debit_note_id, ' ') AS debit_note_id,
                    oo.order_no,
                    oo.payment_code,
                    dn.date_added AS debit_note_date
                FROM
                    ".DB_PREFIX."return AS ocr1
                        INNER JOIN
                    (SELECT
                        MAX(return_id) AS return_id,
                        MAX(debit_note_id) AS debit_note_id,
                        MAX(credit_note_id) AS credit_note_id
                    FROM
                        ".DB_PREFIX."return
                    GROUP BY order_product_id , master_return_id) AS ocr2 ON ocr1.return_id = ocr2.return_id
                        INNER JOIN
                    ".DB_PREFIX."order_product AS op ON op.order_product_id = ocr1.order_product_id
                        INNER JOIN
                    ".DB_PREFIX."suborder AS so ON so.order_id = op.order_id
                        INNER JOIN
                    ".DB_PREFIX."order AS oo ON so.order_id = oo.order_id
                        INNER JOIN
                    ".DB_PREFIX."seller_debit_note AS dn ON dn.debit_note_id = ocr2.debit_note_id
                        AND dn.debit_note_status = 1
                        LEFT JOIN
                    ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr2.credit_note_id
                        AND cn.credit_note_status = 1
                WHERE
                    so.suborder_id = op.suborder_id
                        AND so.invoice_no > 0
                        AND op.buyer_invoice_id > 0
                        AND so.order_status_id > ".(int)ORDER_STATUS['Missing']."
                        AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                        AND (cn.credit_note_id IS NULL
                        OR cn.credit_note_id = 0)
                        AND DATE(dn.date_added) <= DATE(NOW())
                        AND DATE(dn.date_added) >= SUBDATE(CURDATE(), 30)
                GROUP BY oo.order_id
                ";
        $results = $this->db->query($sql);
        $data['debit_notes'] = array();
        if( $results->num_rows > 0){
            $data['debit_notes'] = $results->rows;
        }
        $html = MailTemplate::mailDNsNotHavingCNs($data);

        $today_date = date("d/F/Y");
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->Subject = 'Debit Note Generated, but Credit Note is Pending  (' . $today_date . ')';
        $mail->msgHTML($html);
        $mail->send();
        echo 'Completed';
    }

    public function tentativeRefundExcessPayment(){
        //Set apache execution time limit to infinite
        ini_set('max_execution_time', 0);

        //Create Object for PaymentRefund class
        $obj = new TentativePaymentRefund($this);

        //Cancel cashback/coupon for fully canceled orders
        $obj->cancelCashbackCouponInFullyCanceledOrders();

        //Cancel cashback/coupon for Partially canceled orders
        $obj->cancelCashbackCouponInPartiallyCanceledOrders();

        $obj->executeRefund();
        die;
    }

    public function refundExcessPayment(){
        //Set apache execution time limit to infinite
        ini_set('max_execution_time', 0);

        //Create Object for PaymentRefund class
        $obj = new PaymentRefund($this);

        $obj->executeRefund();
        die;
    }

    public function checkUPIPaymentsStatus() {
        $sql = "SELECT payment_id, merchant_txn_id, json_format, order_id
                    FROM
                    ".DB_PREFIX."order_payment
                    WHERE txn_status = 'pending'
                     AND payment_gateway = 'upi'";
        $result = $this->db->query($sql);
        if($result->num_rows){
            $data = array();
            $upi = new UPI($this);
            foreach ($result->rows as $order_payment) {
                $data['txnID'] = $order_payment['merchant_txn_id'];
                $raw_json_format = unserialize($order_payment['json_format']);

                $query_response = $upi->transactionsEnquiry($data);

                if(isset($query_response->responseStatus)) {
                    $query_response->responseStatus = $query_response->responseStatus;
                } else {
                    $query_response->responseStatus = '';
                }
                $responses = array();
                $responses['initiate'] = $raw_json_format['initiate'];
                $responses['query'] = $query_response;
                $json_format = serialize($responses);


                if(!(empty($query_response->responseStatus))){

                    $successfull = 0;
                    if(strtolower($query_response->responseStatus) == 'completed') {
                        $successfull = 1;
                        $success_txnID = "UPI/".strtoupper($data['txnID']);
                    }

                    if(isset($query_response->requestTime)) {
                        $var = $query_response->requestTime;
                        $date = str_replace('/', '-', $var);
                        $txnDateTime = date('Y-m-d H:i:s', strtotime($date));
                    } else if(isset($responses['initiate']->trnDateTime)) {
                        $var = $responses['initiate']->trnDateTime;
                        $date = str_replace('/', '-', $var);
                        $txnDateTime = date('Y-m-d H:i:s', strtotime($date));
                    } else {
                        $txnDateTime = date('Y-m-d H:i:s');
                    }

                    $update_data['TxStatus'] = $query_response->responseStatus;
                    $update_data['TxMsg'] = "UPI Payment";
                    $update_data['json_format'] = $json_format;
                    $update_data['successfull'] = $successfull;
                    $update_data['OrderNO'] = $data['txnID'];
                    $update_data['TxId'] = !empty($success_txnID)?$success_txnID:$data['txnID'];
                    $update_data['payment_gateway'] = "UPI";
                    $update_data['payment_id'] = $order_payment['payment_id'];
                    $update_data['order_id'] = $order_payment['order_id'];
                    $update_data['amount'] = $responses['initiate']->amount;
                    $update_data['currency'] = "INR";
                    $update_data['paymentMode'] = 'UPI';
                    $update_data['txnDateTime'] = $txnDateTime;

                    $upi->updatePaymentDetailsIntoDb($this, $update_data);
                }

                // if(strtolower($query_response->responseStatus) == 'completed') {
                //     $comment = "UPI Payment";
                //     $sql = "UPDATE
                //                 ".DB_PREFIX."order_history
                //                 SET comment = '".$this->db->escape($comment)."'
                //                 WHERE order_id = '".$order_payment['order_id']."'";
                //     $update_result = $this->db->query($sql);
                // }
            }
        }
    }

    /**
    * Cron to send mail of those, Credit notes don't have DebitNotes
    * @param: void
    * @return: triggering mail
    * @author: Nishu, Dec 2017
    */
    public function getCNsNotHavingDN() {
        $html = '';
        $return_reason_ids = array(
                                RETURN_REASON_IDS['Loss_By_WSB'],
                                RETURN_REASON_IDS['Transfer_To_WSB_Books'],
                                RETURN_REASON_IDS['Cancelled_By_Customer']
                             );

        $sql = "
                SELECT
                    GROUP_CONCAT(DISTINCT cn.credit_note_no, ' ') AS credit_note_no,
                    GROUP_CONCAT(DISTINCT cn.credit_note_id, ' ') AS credit_note_id,
                    oo.order_no,
                    oo.payment_code,
                    cn.date_added AS credit_note_date,
                    so.invoice_no
                FROM
                    ".DB_PREFIX."return AS ocr1
                        INNER JOIN
                    (SELECT
                        MAX(return_id) AS return_id,
                            MAX(debit_note_id) AS debit_note_id,
                            MAX(credit_note_id) AS credit_note_id
                    FROM
                        ".DB_PREFIX."return
                    GROUP BY order_product_id , master_return_id) AS ocr2 ON ocr1.return_id = ocr2.return_id
                        INNER JOIN
                    ".DB_PREFIX."order_product AS op ON op.order_product_id = ocr1.order_product_id
                        INNER JOIN
                    ".DB_PREFIX."suborder AS so ON so.order_id = op.order_id
                        INNER JOIN
                    ".DB_PREFIX."order AS oo ON so.order_id = oo.order_id
                        INNER JOIN
                    ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr2.credit_note_id
                        AND cn.credit_note_status = 1
                        LEFT JOIN
                    ".DB_PREFIX."seller_debit_note AS dn ON dn.debit_note_id = ocr2.debit_note_id
                        AND dn.debit_note_status = 1
                WHERE
                    so.suborder_id = op.suborder_id
                        AND ocr1.return_reason_id NOT IN (". implode(',', $return_reason_ids) .")
                        AND ocr1.return_action_id NOT IN (". implode(',', WSB_LOSS_N_BOOKS_ACTION_IDS) .")
                        AND so.order_status_id > ".(int)ORDER_STATUS['Missing']."
                        AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                        AND (dn.debit_note_id IS NULL
                        OR dn.debit_note_id = 0)
                        AND DATE(cn.date_added) <= DATE(NOW())
                        AND DATE(cn.date_added) >= SUBDATE(CURDATE(), 30)
                GROUP BY oo.order_id
               ";

        $results = $this->db->query($sql);

        $data['credit_notes'] = array();
        if( $results->num_rows > 0){
            $data['credit_notes'] = $results->rows;
        }
        $html = MailTemplate::mailCNsNotHavingDNs($data);

        $today_date = date("d/F/Y");
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        ;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->Subject = 'Credit Note Generated, but Debit Note is Pending  (' . $today_date . ')';
        $mail->msgHTML($html);
        $mail->send();
        echo "Completed";
    }

    /*
      * method to make csv data for get order details with shipping details
      * using attaching .csv
      * @author : Nishu, Dec 2017
    */
    public function mailOrderInsuranceReport(){
        if(empty($this->request->get['date_from']) || empty($this->request->get['date_to'])){
            echo "DateFrom and DateTo in URL";die;
        }
        $date_from = $this->request->get['date_from'];
        $date_to   = $this->request->get['date_to'];

        $sql = "SELECT
                    oo.order_no,
                    so.suborder_id,
                    so.order_status_id,
                    so.total as suborder_val,
                    CONCAT(oo.shipping_firstname,' ',oo.shipping_lastname) as customer,
                    CONCAT(oo.shipping_city, ', ',oo.shipping_zone) as destination,
                    so.courier_partner,
                    so.tracking_no,
                    os.name as order_status,
                    oh.order_status_id,
                    IF(oh.order_status_id = ".(int)ORDER_STATUS['Shipped with tracking'].",
                        oh.comment,
                        '') AS status_comment,
                    oh.date_added as shippment_date
                FROM
                    ".DB_PREFIX."order as oo
                        INNER JOIN
                    ".DB_PREFIX."suborder as so ON so.order_id = oo.order_id
                        AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                        INNER JOIN
                    ".DB_PREFIX."order_status as os ON so.order_status_id = os.order_status_id
                        AND os.language_id = 1
                        INNER JOIN
                    ".DB_PREFIX."order_history as oh ON oh.order_id = oo.order_id
                WHERE
                    so.suborder_id = oh.suborder_id
                        AND oo.franchise_id = 0
                        AND oo.store_id IN (".WSB_STORES_ID.")
                        AND oh.order_status_id IN (".
                                                  (int)ORDER_STATUS['Shipped'].",".
                                                  (int)ORDER_STATUS['Shipped with tracking'].")
                        AND oh.date_added >= '". $this->db->escape($date_from) ."'
                        AND oh.date_added <= '". $this->db->escape($date_to) ." 23:59'
                ORDER BY oo.date_added DESC
                ";
        $result = $this->db->query($sql);
        $data = array();
        if($result->num_rows > 0){
            $data = $result->rows;
            $path = DIR_DLOAD;
            $file = $path.'OrdersInsuranceReport.csv';
            $fp = fopen($file, 'w');
            $head = array(
                      'Order No',
                      'Suborder Id',
                      'Suborder Value',
                      'Order Status',
                      'Customer Name',
                      'Shippment DateTime',
                      'Source Location',
                      'Destination Location',
                      'Courier Name',
                      'Tracking Number',
                      'Comment'
                    );

            fputcsv($fp, $head);
            foreach($data as $value){
                $source_location = '';
                if(strpos($value['suborder_id'], 'DL')){
                    $source_location = 'DELHI';
                }else if(strpos($value['suborder_id'], 'JP')){
                    $source_location = 'JAIPUR';
                }else if(strpos($value['suborder_id'], 'ST')){
                    $source_location = 'SURAT';
                }else if(strpos($value['suborder_id'], 'BLR')){
                    $source_location = 'BANGALORE';
                }else if(strpos($value['suborder_id'], 'KL')){
                    $source_location = 'KOLKATA';
                }else if(strpos($value['suborder_id'], 'MU')){
                    $source_location = 'MUMBAI';
                }
                $row = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['suborder_val'],
                        $value['order_status'],
                        $value['customer'],
                        $value['shippment_date'],
                        $source_location,
                        $value['destination'],
                        $value['courier_partner'],
                        $value['tracking_no'],
                        $value['status_comment']
                    );
                fputcsv($fp, $row);
             }
            fclose($fp);

            //running mailer function to send the geenrated csv in the loop.
            $html = MailTemplate::mailHtmlForOrderInsuranceReport();
        }else{
            $html = "No Order in Last 15 days";
        }

        $subject = "Insurance Report for Order: From ". date("d/M/Y",strtotime($date_from)) ." to " . date("d/M/Y",strtotime($date_to));
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
        $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        //Condition For adding attachment in mail
        $mail->AddAttachment($file);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send(1, false);
        echo "Mail Sent";
        unlink($file);
    }

    /*
      * method to make csv data for get return details which are in pendinf status
      * using attaching .csv
      * @author : Nishu, Dec 2017
    */
    public function mailReturnInsuranceReport(  ){
        if(empty($this->request->get['date_from']) || empty($this->request->get['date_to'])){
            echo "DateFrom and DateTo in URL";die;
        }
        $date_from = $this->request->get['date_from'];
        $date_to   = $this->request->get['date_to'];
        $return_ids = array(
                        RETURN_ACTION_IDS['Return_Request_Rejected'],
                        RETURN_ACTION_IDS['Replacement_Request_Rejected'],
                        RETURN_ACTION_IDS['Return_Goods_Rejected'],
                        RETURN_ACTION_IDS['Cancelled_By_Customer']
                    );
        $sql = "SELECT
                    oo.order_no,
                    oop.suborder_id,
                    ocr.return_id,
                    CONCAT(oo.shipping_firstname,
                            ' ',
                            oo.shipping_lastname) as customer,
                    CONCAT(oo.shipping_city, ', ', oo.shipping_zone) as pickup_location,
                    ocr.quantity * ((oop.price_per_piece + oop.discount_per_piece) * (1 + oop.output_tax_rates / 100)) as parcel_value,
                    ocr.quantity,
                    ocr.date_added as return_date
                FROM
                    ".DB_PREFIX."return as ocr
                        INNER JOIN
                    ".DB_PREFIX."order_product as oop ON oop.order_product_id = ocr.order_product_id
                        INNER JOIN
                    ".DB_PREFIX."order as oo ON oo.order_id = oop.order_id
                WHERE
                    ocr.return_action_id NOT IN (".implode(',', $return_ids).")
                    AND oo.franchise_id = 0
                    AND oo.store_id IN (".WSB_STORES_ID.")
                    AND ocr.date_added >= '". $this->db->escape($date_from) ."'
                    AND ocr.date_added <= '". $this->db->escape($date_to) ." 23:59'
                    AND ocr.active_row = 1
                ORDER BY ocr.date_added
                ";
        $result = $this->db->query($sql);
        $data = array();
        if($result->num_rows > 0){
            $data = $result->rows;
            $path = DIR_DLOAD;
            $file = $path.'ReturnInsuranceReport.csv';
            $fp = fopen($file, 'w');
            $head = array(
                      'Order No',
                      'Suborder Id',
                      'Customer Name',
                      'Pickup Location',
                      'Destination Location',
                      'Return DateTime',
                      'Return Value'
                    );

            fputcsv($fp, $head);
            foreach($data as $value){
                $destination_location = '';
                if(strpos($value['suborder_id'], 'DL')){
                    $destination_location = 'DELHI';
                }else if(strpos($value['suborder_id'], 'JP')){
                    $destination_location = 'JAIPUR';
                }else if(strpos($value['suborder_id'], 'ST')){
                    $destination_location = 'SURAT';
                }else if(strpos($value['suborder_id'], 'BLR')){
                    $destination_location = 'BANGALORE';
                }else if(strpos($value['suborder_id'], 'KL')){
                    $destination_location = 'KOLKATA';
                }else if(strpos($value['suborder_id'], 'MU')){
                    $destination_location = 'MUMBAI';
                }
                $row = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['customer'],
                        $value['pickup_location'],
                        $destination_location,
                        $value['return_date'],
                        $value['parcel_value']
                    );
                fputcsv($fp, $row);
             }
            fclose($fp);

            //running mailer function to send the geenrated csv in the loop.
            $html = MailTemplate::mailHtmlForReturnInsuranceReport();
        }else{
            $html = "No Pending Return in Last 15 days";
        }

        $subject = "Insurance Report for Return: From ". date("d/M/Y",strtotime($date_from)) ." to " . date("d/M/Y",strtotime($date_to));
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
        //Condition For adding attachment in mail
        $mail->AddAttachment($file);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send(1, false);
        echo "Mail Sent";
        unlink($file);
    }

    /**
    * Cron to dis-able Product price where price is >= 1050 and price is < 1200
    *
    *
    */
    public function disableWrongPricedGarmentProductPerGST()
    {
        $html = '';

        $price_from = 1050;
        if ( isset($this->request->get['price_from']) ) {
            $price_from = (float)$this->request->get['price_from'];
        }

        $tax_class_id = 16;
        if ( isset($this->request->get['tax_class_id']) ) {
            $tax_class_id = (int)$this->request->get['tax_class_id'];
        }

        $price_to = 1120;
        if ( isset($this->request->get['price_to']) ) {
            $price_to = (float)$this->request->get['price_to'];
        }

        $sql = " SELECT op.product_id AS product_id,
                        op.model AS model,
                        op.quantity AS quantity,
                        concat(oms.nickname, ' ', oms.company) AS seller_name,
                        op.price AS transfer_price,
                        oh.hsn_code AS hsncode,
                        op.status AS status
                 FROM `".DB_PREFIX."product` op
                 INNER JOIN ".DB_PREFIX."hsn oh on op.hsn_code = oh.hsn_code
                 INNER JOIN ".DB_PREFIX."ms_product omp on omp.product_id = op.product_id
                 INNER JOIN ".DB_PREFIX."ms_seller oms on oms.seller_id = omp.seller_id
                 WHERE oh.tax_class_id = '". $tax_class_id ."'
                   AND price >= '". $price_from ."'
                   AND price < '". $price_to ."'
                   AND op.status = '". 1 ."' " ;

        $products = $this->db->query($sql);
        if ( $products->num_rows > 0 ) {
            $data['product_prices'] = $products->rows;
            $html = MailTemplate::mailDisabledWrongPricedGarmentProductPerGST($data, $price_from, $price_to);
            $today_date = date("d/F/Y");
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
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->addAddress(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->Subject = 'Product Disabled Between '. $price_from .' to ' . $price_to . ' Prices on (' . $today_date . ')';
            $mail->msgHTML($html);
            $mail->send();

            $this->load->model('catalog/product', 'admin');

            $pr = array_column($products->rows, 'product_id');
            $field = 'status';
            $route = $this->request->get['route'];
            $update_products = $this->admin_model_catalog_product->bulkUpdateChangeProductStatus(0, $pr, $field, $route);
        }
    }

    /*
    * Generate the CSV for Dispatch Data for various locations
    */
    public function generateDispatchList()
    {
        if ( isset($this->request->get['pickup_city']) ) {
            $pickup_city = trim($this->request->get['pickup_city']);
            $city_code = "HAVING RIGHT(LEFT(osub.suborder_id, 14),2) = '". $this->db->escape($pickup_city) ."'" ;
        } else {
            $city_code = " " ;
        }

        $name = 'Dispatch-data.csv';
        $file = DIR_DLOAD.$name;

        $fhandle = fopen($file, 'w');
        ob_clean();
        $dispatch_heading = array('Order Id', 'Suborder Id', 'Order No', 'Customer Name', 'Payment Mode', 'Processing Date', 'Good To Dispatch', 'Received');
        fputcsv($fhandle, $dispatch_heading);

        $sql =  "SELECT
                    o.order_id AS order_id,
                    osub.suborder_id AS suborder_id,
                    o.order_no AS order_no,
                    concat(o.shipping_firstname, ' ', o.shipping_lastname ) AS customer_name,
                    o.payment_code AS payment_mode,
                    MIN(date(ooh.date_added)) as processing_date,
                    CASE WHEN o.operations_status = 'good_to_dispatch' THEN 'Yes' ELSE 'No' END AS good_to_dispatch,
                    IFNULL(SUM(amount)/COUNT(DISTINCT ooh.order_history_id), 0) as received
                    FROM ".DB_PREFIX."order o
                    INNER JOIN ".DB_PREFIX."suborder osub on osub.order_id = o.order_id
                    INNER JOIN ".DB_PREFIX."order_history ooh on ooh.order_id = o.order_id
                    LEFT JOIN ".DB_PREFIX."order_payment oop on oop.order_id = o.order_id and oop.successfull = 1 and oop.amount > 0 and oop.payment_gateway not in ('cash', 'coupon', 'cashback') and oop.bank_transfer_mode not in ('cheque_deposited', 'cheque_failed')
                    WHERE
                    osub.order_status_id in (".
                                           (int)ORDER_STATUS['Processed'].",".
                                           (int)ORDER_STATUS['Tentative Processed'].")
                    AND ooh.suborder_id = osub.suborder_id
                    AND ooh.order_status_id IN (".
                                           (int)ORDER_STATUS['Processed'].",".
                                           (int)ORDER_STATUS['Tentative Processed'].")
                    GROUP BY osub.suborder_id
                    ".$city_code."
                    ORDER BY o.order_id ASC ";

        $sql_result = $this->db->query($sql);

        if ( $sql_result->rows )
        {
            foreach ($sql_result->rows as $value) {
                $order_id           = $value['order_id'];
                $suborder_id        = $value['suborder_id'];
                $order_no           = $value['order_no'];
                $customer_name      = $value['customer_name'];
                $prepaid            = $value['payment_mode'];
                $processing_date    = $value['processing_date'];
                $good_to_dispatch   = $value['good_to_dispatch'];
                $received           = $value['received'];

                $data_result = array($order_id, $suborder_id, $order_no, $customer_name,
                    $prepaid, $processing_date, $good_to_dispatch, $received);

                fputcsv($fhandle, $data_result);
            }
            fclose($fhandle);
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addReplyTo(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
        if(isset($pickup_city))
        {
            if ($pickup_city == 'JP' || $pickup_city == 'KL' || $pickup_city == 'BL') {
                $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            } else if ( $pickup_city == 'DL' || $pickup_city == 'ST' || $pickup_city == 'MU' ) {
                $mail->addAddress(EMAIL_IDS['ops'][$pickup_city]['email_id'], EMAIL_IDS['ops'][$pickup_city]['name']);
                $mail->addCC(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            } else {
                // do nothing
            }
            $mail->Subject = 'Dispatch List For - ' . $pickup_city . ' Location ' . date("d/M/Y H:i:s");
            $mail->Body = 'Please find the Dispatch List csv for '.$pickup_city.' location in csv attachment';
        } else {
            $mail->Subject = 'Dispatch List for All Locations ' . date("d/M/Y H:i:s");
            $mail->Body = 'Please find the Dispatch List for All Location csv in attachment';
            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
        }

        $mail->addAttachment($file);
        if ($mail->send(0, false))
        {
            echo "success";
            unlink($file);
            exit;
        } else {
            echo "error";
            exit;
        }
    }

    /** Pendancy Report for Order-history/Seller Order Status (9, 16) and pickup_status != received
    *
    */
    public function generatePendancyReport()
    {
        if ( isset($this->request->get['pickup_city']) ) {
            $pickup_city = trim($this->request->get['pickup_city']);
            $city_code = "HAVING RIGHT(LEFT(os.suborder_id, 14),2) = '". $this->db->escape($pickup_city) ."'" ;
        } else {
            $city_code = " " ;
        }

        $time = date('Y-m-d');

        $name = 'Pendancy-data.csv';
        $file = DIR_DLOAD.$name;

        $fhandle = fopen($file, 'w');
        $pendancy_heading = array('Order No',
                                  'Suborder No',
                                  'Customer Name',
                                  'Processing Date',
                                  'Seller Name',
                                  'Invoiced By Seller',
                                  'Product Name',
                                  'Product Code',
                                  'Pickup Status',
                                  'No Of Sets',
                                  'Total Pieces',
                                  'Set Description',
                                  'Launch Date');


        fputcsv($fhandle, $pendancy_heading);

        $sql = "select
                    oo.order_no as order_no,
                    os.suborder_id as suborder_no,
                    trim(concat(oo.firstname, ' ', oo.lastname)) AS customer_name,
                    MIN(date(oh.date_added)) AS processing_date,
                    concat(oms.company, ' - ', oms.nickname) AS seller_name,
                    if(oop.seller_invoice_id > 0, 'YES', 'NO') AS invoiced_by_seller,
                    oop.name AS product_name,
                    oop.model AS product_code,
                    oop.pickup_status,
                    oop.quantity as no_of_sets,
                    (oop.quantity * oop.piece_in_set) as total_pieces,
                    oop.comment as set_description,
                    op.expected_dispatch_date AS launch_date

                FROM
                    ".DB_PREFIX."order oo
                    INNER JOIN ".DB_PREFIX."suborder os on os.order_id = oo.order_id
                    INNER JOIN ".DB_PREFIX."order_product oop on oop.order_id = oo.order_id
                    INNER JOIN ".DB_PREFIX."ms_seller oms on oms.seller_id = oop.seller_id
                    INNER JOIN ".DB_PREFIX."order_history oh on oh.order_id = oo.order_id
                    INNER JOIN ".DB_PREFIX."product op on op.product_id = oop.product_id

                WHERE
                    oop.suborder_id = os.suborder_id
                    and oh.suborder_id = os.suborder_id
                    and oh.order_status_id in (".
                                               (int)ORDER_STATUS['Processed'].",".
                                               (int)ORDER_STATUS['Tentative Processed'].")
                    and os.order_status_id in (".
                                               (int)ORDER_STATUS['Processed'].",".
                                               (int)ORDER_STATUS['Tentative Processed'].")
                    and oop.pickup_status != 'received'
                    and oop.edit_type != 'SELLER_NOT_SUPPLIED'
                GROUP BY
                    os.suborder_id, oop.order_product_id
                " . $city_code ;

        $result = $this->db->query($sql);

        if ( $result->num_rows )
        {
            foreach ($result->rows as $value) {
                $data = array(
                    $value['order_no'],
                    $value['suborder_no'],
                    $value['customer_name'],
                    date('d-m-Y', strtotime( $value['processing_date'] )),
                    $value['seller_name'],
                    $value['invoiced_by_seller'],
                    $value['product_name'],
                    $value['product_code'],
                    $value['pickup_status'],
                    $value['no_of_sets'],
                    $value['total_pieces'],
                    $value['set_description'],
                    date('d-m-Y', strtotime( $value['launch_date'] ))
                );

                fputcsv($fhandle, $data);
            }
            fclose($fhandle);
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
        $mail->addReplyTo($this->config->get('config_email'), 'WholesaleBox');

        if(isset($pickup_city))
        {
            if ($pickup_city == 'JP' || $pickup_city == 'KL' || $pickup_city == 'BL') {
                $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            } else if ( $pickup_city == 'DL' || $pickup_city == 'ST' || $pickup_city == 'MU' ) {
                $mail->addAddress(EMAIL_IDS['ops'][$pickup_city]['email_id'], EMAIL_IDS['ops'][$pickup_city]['name']);
                $mail->addCC(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            } else {
                // do nothing
            }
            $mail->Subject = 'Pickup Pendancy Report - ' . $pickup_city . ' Location ' . date("d/M/Y H:i:s");
            $mail->Body = 'Please find the Pickup Pendancy Report for '.$pickup_city.' location in csv attachment';
        } else {
            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            $mail->Subject = 'Pickup Pendancy Report For All Locations - ' . date("d/M/Y H:i:s");
            $mail->Body = 'Please find the Pickup Pendancy Report for all locations in csv attachment';
        }

        $mail->addAttachment($file);

        if ($mail->send(1,false))
        {
            echo "success";
        } else {
            echo "error";
        }
        unlink($file);
        exit;
    }

    /**
     * Get list of COD orders which has been delivered and whose payment hasn't
     * reflected in bank account within/on 10th day of delivery date
     * Date parameter applied in report and some column addition and some mail body format as per Ketan Sir.
     * @author Murtaza
     * @return html email
     * Function changed by Nilesh, Ashish as per reqirement of COD amount of suborder, COD amount pending report task by Ketan
     * Function updated by MSA 09 June 2019
     */
    public function notificationAboutCODPending() {

        $days = $this->request->get['days'] ?? 10;
        $date = $this->request->get['date'] ?? '2018-04-01'; // default COD order checking from - 2018-04-01
        $date = $date . ' 00:00:00';

        $sql = "SELECT
                  o.order_no,
                  osub.suborder_id,
                  CONCAT(o.firstname, ' ', o.lastname) AS customer_name,
                  osub.invoice_date AS invoice_date,
                  osub.courier_partner,
                  osub.tracking_no,
                  osub.delivered_date,
                  o.total AS order_amount,
                  osub.total AS suborder_amount,
                  COALESCE(SUM(oav.value), 0) AS suborder_advance_applied_amount,
                  osub.total - COALESCE(SUM(oav.value), 0) AS net_payable_amount,
                  ocd.cod_amount AS collectable_amount_as_per_awb,
                  COALESCE(SUM(oop.amount),0) AS order_received_amount
                FROM
                  ".DB_PREFIX."order o
                INNER JOIN
                  ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
                INNER JOIN
                  ".DB_PREFIX."courier_dockets ocd ON ocd.suborder_id = osub.suborder_id AND
                                            ocd.payment_mode = 'cod' AND
                                            ocd.cod_amount > 0 AND
                                            ocd.docket_no = osub.tracking_no
                LEFT JOIN
                  ".DB_PREFIX."order_payment oop ON oop.order_id = osub.order_id AND
                                          oop.successfull = 1 AND
                                          oop.amount > 0
                LEFT JOIN
                  ".DB_PREFIX."advance_voucher oav ON oav.payment_id = oop.payment_id AND
                                            oav.suborder_id = osub.suborder_id AND
                                            oav.status = 1
                WHERE
                  osub.order_status_id IN(".
                                           (int)ORDER_STATUS['Complete'].",".
                                           (int)ORDER_STATUS['Delivered']
                                        .")
                  AND
                  o.store_id IN(".WSB_STORES_ID.")
                  AND
                  o.stock_transfer = 0
                  AND
                  o.franchise_id = 0
                  AND
                  NOT EXISTS(
                              SELECT
                                1
                              FROM
                                ".DB_PREFIX."cod_writeoffs ocw
                              WHERE
                                ocw.order_id = o.order_id AND ocw.suborder_id = osub.suborder_id
                            )
                  AND
                  osub.delivered_date >= '" . $this->db->escape($date) . "'
                  AND
                  osub.delivered_date <= CURRENT_DATE() - INTERVAL " . (int)$days . " DAY
                GROUP BY
                  osub.suborder_id
                HAVING
                  NOT SUM(osub.tracking_no = oop.merchant_txn_id)
                  AND
                  (suborder_amount - suborder_advance_applied_amount) > 0
                  AND
                  ( order_amount - order_received_amount > 5)
               ";
        $result = $this->db->query($sql);

        if ( $result->num_rows )
        {
            $name = 'COD-Delivered-But-Amount-Not_received.csv';
            $file = DIR_DLOAD.$name;
            $fhandle = fopen($file, 'w');
            $csv_heading = array(
                                  'Order No',
                                  'Suborder No',
                                  'Customer Name',
                                  'Invoice Date',
                                  'Dispatch Date',
                                  'Delivered Date',
                                  'Courier Partner',
                                  'Tracking Number',
                                  'Total Inv Amount (Suborder)',
                                  'Net Payable Amount (Suborder)',
                                  'Collectable Amount As Per AWB (Suborder)',
                                  'Total Amount Received',
                                );

            fputcsv($fhandle, $csv_heading);

            $suborder_id = array_column($result->rows, 'suborder_id');

            $order_info = new OrderInfo();
            $get_dispatch_date = $order_info->getDispatchDate($this->db, $suborder_id);

            $courier_prtnr_total = array();
            $total_amount_cur_prt = 0;

            foreach ($result->rows as $value) {

                if(!empty($value['courier_partner'])){
                    $courier_prtnr_total[$value['courier_partner']] += $value['collectable_amount_as_per_awb'];
                } else {
                    $courier_prtnr_total['No Courier Partner Assigned'] += $value['collectable_amount_as_per_awb'];
                }

                $total_amount_cur_prt += $value['collectable_amount_as_per_awb'];

                $data = array(
                    $value['order_no'],
                    $value['suborder_id'],
                    $value['customer_name'],
                    date('d-m-Y', strtotime( $value['invoice_date'] )),
                    date('d-m-Y', strtotime( $get_dispatch_date[$value['suborder_id']] )),
                    date('d-m-Y', strtotime( $value['delivered_date'] )),
                    $value['courier_partner'],
                    $value['tracking_no'],
                    (float)$value['suborder_amount'],
                    (float)$value['net_payable_amount'],
                    (float)$value['collectable_amount_as_per_awb'],
                    (float)$value['order_received_amount'],
                );

                fputcsv($fhandle, $data);
            }

            fclose($fhandle);

            $body = "Hi All, <br/><br/> PFA, COD amount pending report in csv attachment";
            $body .= '<br/><br/>';

            $body .= '<table border="1">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Courier Partner</th>
                                        <th>Collectable Amount As Per AWB</th>
                                    </tr>
                                </thead>
                                <tbody>';
                if (!empty($courier_prtnr_total)) {
                    $y= 1;
                    foreach ($courier_prtnr_total as $c_key => $c_value) {
                        $body .= '<tr>
                                            <td>'.$y.'</td>
                                            <td>'.(str_replace('~', ' ', $c_key)).'</td>
                                            <td>'.$c_value.'</td>
                                        </tr>';
                        $y++;
                    }
                }

                $body .= '  <tr>';
                $body .= '    <td>Total</td>';
                $body .= '    <td>&nbsp;</td>';
                $body .= '    <td>'.round($total_amount_cur_prt,2).'</td>';
                $body .= '  </tr>';
                $body .= '</tbody></table>';

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
                $mail->addReplyTo(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);

                $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
                $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
                $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
                $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);

                $mail->Subject = 'COD Amount Pending Report - ' . date("d/M/Y H:i:s");
                $mail->msgHTML($body);

                $mail->addAttachment($file);

                if ($mail->send(1,false))
                {
                    echo "Success";
                } else {
                    echo "Error";
                }
                unlink($file);
                exit;
        }
    }

    /**
    * Cron to update Product where weight of the product id <= 0
    *
    * @author Ashish 31 Jan 2018
    */
    public function identifyProductsWeight()
    {
        $html = '';

        $old_weight = 0;
        if ( isset($this->request->get['old_weight']) ) {
            $old_weight = (float)$this->request->get['old_weight'];
        }

        $new_weight = 0.75;
        if ( isset($this->request->get['new_weight']) ) {
            $new_weight = (float)$this->request->get['new_weight'];
        }

        $sql = " SELECT product_id AS product_id,
                        weight AS weight
                 FROM ".DB_PREFIX."product
                 WHERE weight <= '". $old_weight ."'
                   AND status = '". 1 ."' " ;
        $products = $this->db->query($sql);
        if ( $products->num_rows > 0 ) {

            $this->load->model('catalog/product', 'admin');

            $product_ids = array_column($products->rows, 'product_id');
            $field = 'weight';
            $route = $this->request->get['route'];

            $update_products = $this->admin_model_catalog_product->bulkUpdateChangeProductStatus($new_weight, $product_ids, $field, $route);

        } else {
            echo "No Records";
        }
    }

    /**
    * Cron to reset sellers promotion
    * @author Anurag Jain ( 15th Feb 2018 )
    * @param void
    * @return void
    */
    public function manageSellerPromotion() {

      $this->load->model('catalog/product');

      // get disabled promotions and promotions with more than 2 days of promotion to reset/remove
      $promotion_disable_result = $this->model_catalog_product->getPromotionsToDisable();
      if(!empty($promotion_disable_result)) {
        $product_change_log = new ProductChangeLog($this->registry);
        $mail_data = array();
        foreach ($promotion_disable_result as $key => $promotion_data) {
          $product_ids = !empty($promotion_data['product_ids'])?$promotion_data['product_ids']:"";
          if(!empty($product_ids)) {
            $new_sort_order = 999;
            $eligible_ids = $this->model_catalog_product->getDemotionEligibleProductIds($product_ids);
            $this->model_catalog_product->updateProductSortOrder( $product_ids, $new_sort_order );
            $all_prod_ids = explode(",", $eligible_ids);
            foreach ($all_prod_ids as $key => $prod_id) {
              $changes_data['sort_order']['new_value'] = $new_sort_order;
              $source_field = 'product_edit';
              $changes_data['sort_order']['old_value'] = 1;
              $product_change_log->recordLogs($prod_id, $changes_data, $source_field);
            }
            // delete promotion
            $this->model_catalog_product->deletePromotion($promotion_data['id']);

            // seller promotion mail data
            if(empty($mail_data[$promotion_data['seller_id']])) {
              $seller_data = $this->model_catalog_product->getSeller($promotion_data['seller_id']);
              $mail_data[$promotion_data['seller_id']] = array(
                                                          "seller_name" => $seller_data['seller_company'],
                                                          "seller_code" => $seller_data['nickname']
                                                        );
            }
            $mail_data[$promotion_data['seller_id']]['categories'][] = array(
                                                                        "category_name" => $this->model_catalog_product->getCategoryName($promotion_data['category_id'])['name'],
                                                                        "count" => $promotion_data['product_count']
                                                                      );
          }
        }
        // seller promotion reset mail
        if(!empty($mail_data)) {
          $html = MailTemplate::mailForResetSellerPromotion($mail_data);
          $mail = new  PHPMailer();
          $mail->isSMTP();
          $mail->Host = $this->config->get('config_mail_smtp_hostname');
          $mail->Port = $this->config->get('config_mail_smtp_port');
          $mail->SMTPSecure = 'ssl';
          $mail->SMTPAuth = true;
          $mail->Username = $this->config->get('config_mail_smtp_username');
          $mail->Password = $this->config->get('config_mail_smtp_password');
          $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');

          if(SITE_ENVIRONMENT == "Production") {
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
          } else {
            $mail->addAddress(EMAIL_IDS['anurag']['email_id'], EMAIL_IDS['anurag']['name']);
            $mail->addAddress(EMAIL_IDS['ankita']['email_id'], EMAIL_IDS['ankita']['name']);
          }

          $mail->Subject = 'Seller Promotion - End';
          $mail->isHTML(true);
          $mail->msgHTML($html);
          $mail->send();
        }
      }
    }

    /**
    * Cron to Identify the Credit Orders which are currently lying in Pending status
    *
    * @author Ashish 16 Feb 2018
    */
    public function creditOrderLyingInPending()
    {
        $mails = array();
        $days = 1;
        if (isset($this->request->get['days'])) {
            $days = (int)$this->request->get['days'];
        }

        $send = '';
        if (isset($this->request->get['send'])) {
            $send = $this->request->get['send'];
        }

        if ($send) {
            $mails = explode(',', $send);
            foreach ($mails as $key => $value) {
                $email_id = EMAIL_IDS[$value]['email_id'];
                $name = EMAIL_IDS[$value]['name'];
            }
        }

        $date = date('Y-m-d', strtotime('-'.$days. ' days'));

        $sql = "SELECT
                oo.order_no AS order_no,
                concat(oo.firstname, ' ', oo.lastname) AS customer_name,
                oo.payment_company AS company,
                oo.payment_city AS city,
                oo.total AS total,
                oo.payment_method
                FROM ".DB_PREFIX."order oo
                INNER JOIN ".DB_PREFIX."suborder os on os.order_id = oo.order_id
                WHERE oo.payment_code IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "') and os.order_status_id = ".(int)ORDER_STATUS['Pending']."
                    and date(oo.date_added) >= '" . $date . "'
                GROUP BY oo.order_id ";

        $result = $this->db->query($sql);

        if($result->num_rows > 0) {
            $data['credit_order_in_pending'] = $result->rows;
            $html = MailTemplate::mailcreditOrderLyingInPending($data, $date);
            $today_date = date("d/F/Y");
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            if ($send) {
                $mails = explode(',', $send);
                foreach ($mails as $key => $value) {
                    $email_id = EMAIL_IDS[$value]['email_id'];
                    $name = EMAIL_IDS[$value]['name'];
                    $mail->addAddress($email_id, $name);
                }
            }
            $mail->Subject = 'Credit Orders Lying Pending Status From '. $date ;
            $mail->msgHTML($html);
            $mail->send();
        }
    }

    /**
     * @info: Cron to send report for
     * Calculate Return qty at SKU level against Sale qty
     * @author Nishu 28 Feb 2018
    */
    public function returnReportMailing() {
        ini_set('memory_limit', '-1');

        //Send Data mail for Return SKU wise
        $this->getDataForReturnSkuWise();

        //Send Data mail for Replacement SKU wise
        $this->getDataForReplacementSkuWise();

        //Send Data mail for Return Seller wise
        $this->getDataForReturnSellerWise();

        //Send Data mail for Replacement Seller wise
        $this->getDataForReplacementSellerWise();

    }

    /**
     * @info: Public method to create dataset for SKU wise returns
     * @author Nishu 1st March 2018
    */
    public function getDataForReturnSkuWise() {
        $data = array();
        $data['file_name'] = DIR_UPLOAD . 'returns/SKU_Return_' . date('d_M_y') . '.csv';
        $data['subject']   = "SKU Wise Return Report : ". date('d/M/Y H:i:s');
        //Get Data for SKU wise return
        $data['head'] = array(
                        'SKU CODE',
                        'Product ID',
                        'Seller code',
                        'Seller Name',
                        'Number Sold',
                        'Number Returned',
                        'Return %',
                        'Total Orders',
                        'Orders Returned',
                        'Order Return %'
                      );
        $data['data'] = array();

        //New query
        $sql = "
               SELECT
                    orderproductidwise.product_id,
                    orderproductidwise.seller_sku,
                    orderproductidwise.seller_id,
                    orderproductidwise.nickname,
                    orderproductidwise.company,
                    COUNT(DISTINCT orderproductidwise.order_id) AS order_count,
                    COUNT(DISTINCT orderproductidwise.return_order_id) AS return_order_count,
                    SUM(orderproductidwise.int_sale_pieces_count) AS sale_pieces_count,
                    SUM(orderproductidwise.int_return_pieces_count) AS return_pieces_count,
                    ROUND((SUM(orderproductidwise.int_return_pieces_count) / SUM(orderproductidwise.int_sale_pieces_count)) * 100,
                            2) AS return_pieces_percent,
                    ROUND((COUNT(DISTINCT orderproductidwise.return_order_id) / COUNT(DISTINCT orderproductidwise.order_id)) * 100,
                            2) AS return_order_percent,
                    ROUND(COUNT(DISTINCT orderproductidwise.order_id) / 10,
                            2) AS order_10_percent
                FROM
                    (SELECT
                        oop.product_id,
                            op.sku AS seller_sku,
                            ms.seller_id,
                            ms.nickname,
                            ms.company,
                            oop.order_product_id,
                            oo.order_id,
                            GROUP_CONCAT(DISTINCT IF(ocr.order_product_id > 0
                                AND ocr.order_product_id = oop.order_product_id
                                AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                                AND orr.reason_type = 'RETURN'
                                AND cn.credit_note_status = 1, oo.order_id, NULL)) AS return_order_id,
                            oop.quantity * oop.piece_in_set AS int_sale_pieces_count,
                            SUM(IF(ocr.order_product_id > 0
                                AND ocr.order_product_id = oop.order_product_id
                                AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                                AND orr.reason_type = 'RETURN'
                                AND cn.credit_note_status = 1, ocr.quantity, 0)) AS int_return_pieces_count
                    FROM
                        ".DB_PREFIX."order AS oo
                    INNER JOIN ".DB_PREFIX."suborder AS so ON oo.order_id = so.order_id
                    INNER JOIN ".DB_PREFIX."order_product AS oop ON so.buyer_invoice_id = oop.buyer_invoice_id
                    INNER JOIN ".DB_PREFIX."product op ON op.product_id = oop.product_id
                    INNER JOIN ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = oop.seller_id
                    LEFT JOIN ".DB_PREFIX."return AS ocr ON ocr.order_product_id = oop.order_product_id
                        AND ocr.credit_note_id > 0
                    LEFT JOIN ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr.credit_note_id
                    LEFT JOIN ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                    WHERE
                        op.status = 1 AND so.order_status_id > ".(int)ORDER_STATUS['Missing']."
                            AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                            AND so.order_status_id != ".(int)ORDER_STATUS['Failed']."
                            AND oo.stock_transfer = 0
                            AND oo.store_id IN (0 , 2, 9)
                            AND so.buyer_invoice_id > 0
                            AND so.invoice_no > 0
                            AND oo.franchise_id = 0
                    GROUP BY oop.order_product_id) AS orderproductidwise
                WHERE
                    1 = 1
                GROUP BY orderproductidwise.product_id
                HAVING CASE
                    WHEN order_count <= 5 THEN 2
                    WHEN order_count BETWEEN 6 AND 10 THEN 3
                    WHEN order_count BETWEEN 11 AND 30 THEN 5
                    WHEN order_count > 30 THEN GREATEST(5, order_10_percent)
                END <= return_order_count
                ORDER BY return_pieces_percent DESC , return_order_percent DESC
               ";

        $results = $this->db->query($sql);
        if( $results->num_rows > 0){
            foreach ($results->rows as $key => $value) {
                $data['data'][$key]['seller_sku']            = $value['seller_sku'];
                $data['data'][$key]['product_id']            = $value['product_id'];
                $data['data'][$key]['nickname']              = $value['nickname'];
                $data['data'][$key]['company']               = $value['company'];
                $data['data'][$key]['sale_pieces_count']     = $value['sale_pieces_count'];
                $data['data'][$key]['return_pieces_count']   = $value['return_pieces_count'];
                $data['data'][$key]['return_pieces_percent'] = $value['return_pieces_percent'];
                $data['data'][$key]['order_count']           = $value['order_count'];
                $data['data'][$key]['return_order_count']    = $value['return_order_count'];
                $data['data'][$key]['return_order_percent']  = $value['return_order_percent'];
            }
            //Filter Data by only disabled products
            $data['data'] = $this->ProcessDisableSellerSku($data['data']);
            $mail_data    = array_slice($data['data'], 0, 30);

            //Generate Mail Body
            $data['html'] = MailTemplate::mailReturnReportSkuWise($mail_data);
        }else{
            $data['html'] = "No Data for this Report.";
        }
        //Send mail by invoking method
        $this->sendMailForReturnReports($data);
    }

    /**
     * @info: Public method to create dataset for SKU wise replacements
     * @author Nishu 1st March 2018
    */
    public function getDataForReplacementSkuWise() {
        $data = array();
        $data['file_name'] = DIR_UPLOAD . 'returns/SKU_Repalcement_' . date('d_M_y') . '.csv';
        $data['subject']   = "SKU Wise Replacement Report : ". date('d/M/Y H:i:s');
        //Get Data for SKU wise return
        $data['head'] = array(
                        'SKU CODE',
                        'Product ID',
                        'Seller code',
                        'Seller Name',
                        'Number Sold',
                        'Number Replaced',
                        'Replacement %',
                        'Total Orders',
                        'Orders Replaced',
                        'Order Replacement %'
                      );
        $data['data'] = array();

        $sql = "SELECT
                    orderproductidwise.product_id,
                    orderproductidwise.seller_sku,
                    orderproductidwise.seller_id,
                    orderproductidwise.nickname,
                    orderproductidwise.company,

                    COUNT(DISTINCT orderproductidwise.order_id) as order_count,

                    COUNT(DISTINCT orderproductidwise.return_order_id) as return_order_count,

                    SUM(orderproductidwise.int_sale_pieces_count) as sale_pieces_count,

                    SUM(orderproductidwise.int_return_pieces_count) as return_pieces_count,

                    ROUND((SUM(orderproductidwise.int_return_pieces_count) / SUM(orderproductidwise.int_sale_pieces_count)) * 100, 2) AS return_pieces_percent,

                    ROUND((COUNT(DISTINCT orderproductidwise.return_order_id) / COUNT(DISTINCT orderproductidwise.order_id)) * 100, 2) AS return_order_percent,

                    ROUND(COUNT(DISTINCT orderproductidwise.order_id) / 10, 2 ) as order_10_percent

                FROM
                    (SELECT
                        oop.product_id,
                        op.sku as seller_sku,
                        ms.seller_id,
                        ms.nickname,
                        ms.company,

                        oop.order_product_id,

                        oo.order_id,

                        GROUP_CONCAT(DISTINCT IF(ocr.order_product_id > 0
                           AND ocr.order_product_id = oop.order_product_id
                           AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                           AND orr.reason_type = 'REPLACEMENT'
                           AND cn.credit_note_status = 1, oo.order_id, NULL) ) AS return_order_id,

                        oop.quantity * oop.piece_in_set AS int_sale_pieces_count,

                        SUM(IF(ocr.order_product_id > 0
                               AND ocr.order_product_id = oop.order_product_id
                               AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                               AND orr.reason_type = 'REPLACEMENT'
                               AND cn.credit_note_status = 1, ocr.quantity, 0)) AS int_return_pieces_count
                    FROM
                        ".DB_PREFIX."order AS oo
                    INNER JOIN ".DB_PREFIX."suborder AS so ON oo.order_id = so.order_id
                    INNER JOIN ".DB_PREFIX."order_product AS oop ON so.buyer_invoice_id = oop.buyer_invoice_id
                    INNER JOIN ".DB_PREFIX."product op ON op.product_id = oop.product_id
                    INNER JOIN ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = oop.seller_id
                    LEFT JOIN ".DB_PREFIX."return AS ocr ON ocr.order_product_id = oop.order_product_id AND ocr.credit_note_id > 0
                    LEFT JOIN ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr.credit_note_id
                    LEFT JOIN ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                    WHERE
                        op.status = 1
                            ANd so.order_status_id > ".(int)ORDER_STATUS['Missing']."
                            AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                            AND so.order_status_id != ".(int)ORDER_STATUS['Failed']."
                            AND oo.stock_transfer = 0
                            AND oo.store_id IN (0 , 2, 9)
                            AND so.buyer_invoice_id > 0
                            AND so.invoice_no > 0
                            AND oo.franchise_id = 0
                    GROUP BY oop.order_product_id) AS orderproductidwise

                WHERE
                  1 = 1

                GROUP BY orderproductidwise.product_id

                HAVING
                    CASE
                         WHEN order_count <= 5 THEN 2
                         WHEN order_count BETWEEN 6 AND 10 THEN 3
                         WHEN order_count BETWEEN 11 AND 30 THEN 5
                         WHEN order_count > 30 THEN GREATEST(5, order_10_percent)
                    END <= return_order_count

                ORDER BY return_pieces_percent DESC , return_order_percent DESC
                ";
        $results = $this->db->query($sql);
        if( $results->num_rows > 0){
            foreach ($results->rows as $key => $value) {
                $data['data'][$key]['seller_sku']            = $value['seller_sku'];
                $data['data'][$key]['product_id']            = $value['product_id'];
                $data['data'][$key]['nickname']              = $value['nickname'];
                $data['data'][$key]['company']               = $value['company'];
                $data['data'][$key]['sale_pieces_count']     = $value['sale_pieces_count'];
                $data['data'][$key]['return_pieces_count']   = $value['return_pieces_count'];
                $data['data'][$key]['return_pieces_percent'] = $value['return_pieces_percent'];
                $data['data'][$key]['order_count']           = $value['order_count'];
                $data['data'][$key]['return_order_count']    = $value['return_order_count'];
                $data['data'][$key]['return_order_percent']  = $value['return_order_percent'];
            }
            //Filter Data by only disabled products
            $data['data'] = $this->ProcessDisableSellerSku($data['data']);
            $mail_data    = array_slice($data['data'], 0, 30);

            //Generate Mail Body
            $data['html'] = MailTemplate::mailReplacementSkuWise($mail_data);
        }else{
            $data['html'] = "No Data for this Report.";
        }
        //Send mail by invoking method
        $this->sendMailForReturnReports($data);
    }

    /**
     * @info: Public method to create dataset for SKU wise returns
     * @author Nishu 1st March 2018
    */
    public function getDataForReturnSellerWise() {
        $data = array();
        $data['file_name'] = DIR_UPLOAD . 'returns/Seller_Return_' . date('d_M_y') . '.csv';
        $data['subject']   = "Seller Wise Return Report : ". date('d/M/Y H:i:s');
        //Get Data for SKU wise return
        $data['head'] = array(
                        'Seller Code',
                        'Seller Name',
                        'Seller Rating',
                        'SKU Sold',
                        'SKU Returned',
                        '%SKU Return',
                        'Total Sales',
                        'Total Returned',
                        '%Sales Return',
                        'Total Orders',
                        'Orders Returned',
                        'Order Return %'
                      );
        $data['data'] = array();

        //New query
        $sql = "
                SELECT
                        orderproductidwise.seller_id,
                        orderproductidwise.nickname,
                        orderproductidwise.company,
                        orderproductidwise.rating,

                        COUNT(DISTINCT orderproductidwise.product_id) as sku_sold,
                        COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.product_id, NULL)) as sku_return,

                        COUNT(DISTINCT orderproductidwise.order_id) as order_count,
                        COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.order_id, NULL)) as return_order_count,


                        SUM(orderproductidwise.int_sale_pieces_count) as sale_pieces_count,
                        SUM(orderproductidwise.int_return_pieces_count) as return_pieces_count,

                        SUM(orderproductidwise.int_sale_pieces_value) as sale_value,
                        SUM(orderproductidwise.int_return_pieces_value) as return_value,

                        ROUND( SUM(orderproductidwise.int_return_pieces_count) / SUM(orderproductidwise.int_sale_pieces_count) * 100, 2) AS return_pieces_percent,

                        ROUND( COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.order_id, NULL)) / COUNT(DISTINCT orderproductidwise.order_id) * 100, 2) AS return_order_percent,

                        ROUND( COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.product_id, NULL)) / COUNT(DISTINCT orderproductidwise.product_id) * 100, 2) AS sku_return_percent,

                        ROUND( SUM(orderproductidwise.int_return_pieces_value) / SUM(orderproductidwise.int_sale_pieces_value) * 100, 2) AS sale_return_percent

                FROM
                        (SELECT
                            oop.product_id,
                            ms.seller_id,
                            ms.nickname,
                            ms.company,
                            orrs.rating,

                            oop.order_product_id,
                            op.price as tp_price,

                            oo.order_id,

                            oop.quantity * oop.piece_in_set AS int_sale_pieces_count,

                            oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece as int_sale_pieces_value,

                            SUM(IF(ocr.order_product_id > 0
                                   AND ocr.order_product_id = oop.order_product_id
                                   AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                                   AND orr.reason_type = 'RETURN'
                                   AND cn.credit_note_status = 1, ocr.quantity, 0)) AS int_return_pieces_count,

                            SUM(IF(ocr.order_product_id > 0
                                   AND ocr.order_product_id = oop.order_product_id
                                   AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                                   AND orr.reason_type = 'RETURN'
                                   AND cn.credit_note_status = 1, ocr.quantity*oop.transfer_price_per_piece, 0)) AS int_return_pieces_value

                        FROM
                            ".DB_PREFIX."order AS oo
                        INNER JOIN ".DB_PREFIX."suborder AS so ON oo.order_id = so.order_id
                        INNER JOIN ".DB_PREFIX."order_product AS oop ON so.buyer_invoice_id = oop.buyer_invoice_id
                        INNER JOIN ".DB_PREFIX."product op ON op.product_id = oop.product_id
                        INNER JOIN ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = oop.seller_id
                        LEFT JOIN ".DB_PREFIX."return AS ocr ON ocr.order_product_id = oop.order_product_id AND ocr.credit_note_id > 0
                        LEFT JOIN ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr.credit_note_id
                        LEFT JOIN ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                        LEFT JOIN ".DB_PREFIX."review_rules AS orrs ON orrs.seller_id = oop.seller_id AND orrs.rule_type = 'global'
                        WHERE
                            op.status = 1
                                AND so.order_status_id > ".(int)ORDER_STATUS['Missing']."
                                AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                                AND so.order_status_id != ".(int)ORDER_STATUS['Failed']."
                                AND oo.stock_transfer = 0
                                AND oo.store_id IN (0 , 2, 9)
                                AND so.buyer_invoice_id > 0
                                AND so.invoice_no > 0
                                AND oo.franchise_id = 0
                                AND ms.seller_status = 1
                        GROUP BY oop.order_product_id) AS orderproductidwise

                WHERE
                  1 = 1

                GROUP BY orderproductidwise.seller_id

                HAVING
                    return_pieces_percent >= 8

                ORDER BY sku_return_percent DESC , return_order_percent DESC
               ";
        $results = $this->db->query($sql);
        if( $results->num_rows > 0){
            foreach ($results->rows as $key => $value) {
                $data['data'][$key]['nickname']             = $value['nickname'];
                $data['data'][$key]['company']              = $value['company'];
                $data['data'][$key]['rating']               = $value['rating'];
                $data['data'][$key]['sku_sold']             = $value['sale_pieces_count'];
                $data['data'][$key]['sku_return']           = $value['return_pieces_count'];
                $data['data'][$key]['sku_return_percent']   = round($value['return_pieces_percent'], 2);
                $data['data'][$key]['total_sale']           = $value['sale_value'];
                $data['data'][$key]['return_value']         = $value['return_value'];
                $data['data'][$key]['sale_return_percent']  = round($value['sale_return_percent'], 2);
                $data['data'][$key]['total_order']          = $value['order_count'];
                $data['data'][$key]['total_return']         = $value['return_order_count'];
                $data['data'][$key]['order_return_percent'] = round($value['return_order_percent'], 2);
            }
            $mail_data = array_slice($data['data'], 0, 30);
            //Generate Mail Body
            $data['html'] = MailTemplate::mailReturnSellerWise($mail_data);
        }else{
            $data['html'] = "No Data for this Report.";
        }
        //Send mail by invoking method
        $this->sendMailForReturnReports($data);
    }

    /**
     * @info: Public method to create dataset for SKU wise replacements
     * @author Nishu 1st March 2018
    */
    public function getDataForReplacementSellerWise() {
        $data = array();
        $data['file_name'] = DIR_UPLOAD . 'returns/Seller_Replacement_' . date('d_M_y') . '.csv';
        $data['subject']   = "Seller Wise Replacement Report : ". date('d/M/Y H:i:s');
        //Get Data for SKU wise return
        $data['head'] = array(
                        'Seller Code',
                        'Seller Name',
                        'Seller Rating',
                        'SKU Sold',
                        'SKU Replaced',
                        '%SKU Replacement',
                        'Total Sales',
                        'Total Replaced',
                        '%Sales Replacement',
                        'Total Orders',
                        'Orders Replaced',
                        'Order Replacement %'
                      );
        $data['data'] = array();

        $sql = "
                SELECT
                        orderproductidwise.seller_id,
                        orderproductidwise.nickname,
                        orderproductidwise.company,
                        orderproductidwise.rating,

                        COUNT(DISTINCT orderproductidwise.product_id) as sku_sold,
                        COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.product_id, NULL)) as sku_return,

                        COUNT(DISTINCT orderproductidwise.order_id) as order_count,
                        COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.order_id, NULL)) as return_order_count,


                        SUM(orderproductidwise.int_sale_pieces_count) as sale_pieces_count,
                        SUM(orderproductidwise.int_return_pieces_count) as return_pieces_count,

                        SUM(orderproductidwise.int_sale_pieces_value) as sale_value,
                        SUM(orderproductidwise.int_return_pieces_value) as return_value,

                        ROUND( SUM(orderproductidwise.int_return_pieces_count) / SUM(orderproductidwise.int_sale_pieces_count) * 100, 2) AS return_pieces_percent,

                        ROUND( COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.order_id, NULL)) / COUNT(DISTINCT orderproductidwise.order_id) * 100, 2) AS return_order_percent,

                        ROUND( COUNT(DISTINCT IF(orderproductidwise.int_return_pieces_count > 0, orderproductidwise.product_id, NULL)) / COUNT(DISTINCT orderproductidwise.product_id) * 100, 2) AS sku_return_percent,

                        ROUND( SUM(orderproductidwise.int_return_pieces_value) / SUM(orderproductidwise.int_sale_pieces_value) * 100, 2) AS sale_return_percent

                FROM
                        (SELECT
                            oop.product_id,
                            ms.seller_id,
                            ms.nickname,
                            ms.company,
                            orrs.rating,

                            oop.order_product_id,
                            op.price as tp_price,

                            oo.order_id,

                            oop.quantity * oop.piece_in_set AS int_sale_pieces_count,

                            oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece as int_sale_pieces_value,

                            SUM(IF(ocr.order_product_id > 0
                                   AND ocr.order_product_id = oop.order_product_id
                                   AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                                   AND orr.reason_type = 'REPLACEMENT'
                                   AND cn.credit_note_status = 1, ocr.quantity, 0)) AS int_return_pieces_count,

                            SUM(IF(ocr.order_product_id > 0
                                   AND ocr.order_product_id = oop.order_product_id
                                   AND orr.return_reason_id != ". RETURN_REASON_IDS['COD_Failed'] ."
                                   AND orr.reason_type = 'REPLACEMENT'
                                   AND cn.credit_note_status = 1, ocr.quantity*oop.transfer_price_per_piece, 0)) AS int_return_pieces_value

                        FROM
                            ".DB_PREFIX."order AS oo
                        INNER JOIN ".DB_PREFIX."suborder AS so ON oo.order_id = so.order_id
                        INNER JOIN ".DB_PREFIX."order_product AS oop ON so.buyer_invoice_id = oop.buyer_invoice_id
                        INNER JOIN ".DB_PREFIX."product op ON op.product_id = oop.product_id
                        INNER JOIN ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = oop.seller_id
                        LEFT JOIN ".DB_PREFIX."return AS ocr ON ocr.order_product_id = oop.order_product_id AND ocr.credit_note_id > 0
                        LEFT JOIN ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr.credit_note_id
                        LEFT JOIN ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                        LEFT JOIN ".DB_PREFIX."review_rules AS orrs ON orrs.seller_id = oop.seller_id AND orrs.rule_type = 'global'
                        WHERE
                            op.status = 1
                                AND so.order_status_id > ".(int)ORDER_STATUS['Missing']."
                                AND so.order_status_id != ".(int)ORDER_STATUS['Canceled']."
                                AND so.order_status_id != ".(int)ORDER_STATUS['Failed']."
                                AND oo.stock_transfer = 0
                                AND oo.store_id IN (0 , 2, 9)
                                AND so.buyer_invoice_id > 0
                                AND so.invoice_no > 0
                                AND oo.franchise_id = 0
                        GROUP BY oop.order_product_id) AS orderproductidwise

                WHERE
                  1 = 1

                GROUP BY orderproductidwise.seller_id

                HAVING
                    sku_return_percent >= 8

                ORDER BY sku_return_percent DESC , return_order_percent DESC
                ";
        $results = $this->db->query($sql);
        if( $results->num_rows > 0){
            foreach ($results->rows as $key => $value) {
                $data['data'][$key]['nickname']             = $value['nickname'];
                $data['data'][$key]['company']              = $value['company'];
                $data['data'][$key]['rating']               = $value['rating'];
                $data['data'][$key]['sku_sold']             = $value['sku_sold'];
                $data['data'][$key]['sku_return']           = $value['sku_return'];
                $data['data'][$key]['sku_return_percent']   = round($value['sku_return_percent'], 2);
                $data['data'][$key]['total_sale']           = $value['sale_value'];
                $data['data'][$key]['return_value']         = $value['return_value'];
                $data['data'][$key]['sale_return_percent']  = round($value['sale_return_percent'], 2);
                $data['data'][$key]['total_order']          = $value['order_count'];
                $data['data'][$key]['total_return']         = $value['return_order_count'];
                $data['data'][$key]['order_return_percent'] = round($value['return_order_percent'], 2);
            }
            $mail_data = array_slice($data['data'], 0, 30);
            //Generate Mail Body
            $data['html'] = MailTemplate::mailReplacementSellerWise($mail_data);
        }else{
            $data['html'] = "No Data for this Report.";
        }
        //Send mail by invoking method
        $this->sendMailForReturnReports($data);
    }


    /**
     * @info: Sending Mail
     * @author Nishu 28 Feb 2018
    */
    public function sendMailForReturnReports($data) {
        if (!file_exists(DIR_UPLOAD. 'returns/')) {
            mkdir(DIR_UPLOAD . 'returns/', 0777, true);
        }
        $fp = fopen($data['file_name'], 'w');
        if(!empty($data['data'])){
            //Writing header in CSV file
            fputcsv($fp, $data['head']);
            //Writing Data Dynamically
            foreach ($data['data'] as $key => $value) {
                fputcsv($fp, $value);
            }
        }
        ob_clean();

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPDebug = false;
        $mail->Debugoutput = 'html';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
        $mail->addReplyTo(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
        $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
        $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addCC(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
        $mail->Subject = $data['subject'];
        $mail->msgHTML($data['html']);
        $mail->AddAttachment($data['file_name']);
        //send to admin;
        $mail->send(1, false);
    }

    /**
     * @info: Public method to process product disabling SKU and sending mail to seller
     * @author Nishu 3rd March 2018
    */
    public function ProcessDisableSellerSku($data) {
        $data = array_combine(
                array_column($data, 'product_id')
            , $data);

        $temp_data = array();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $temp = $this->disableSellerSkuAndLog($value);
                if(!empty($temp)){
                    $temp_data[] = $temp;
                }
            }
        }
        return $temp_data;
    }

    /**
     * @info: Public method to mark status as disable in oc_product table and
     * maintan log in oc_admin_log_change
     * @author: Nishu March 2018
    */
    public function disableSellerSkuAndLog($data){
        //Execute Query to get product's old status
        $sql = "
                SELECT
                   op.*,
                   ms.seller_invoice_generate
                FROM
                    ".DB_PREFIX."product AS op
                        INNER JOIN
                    ".DB_PREFIX."ms_product AS mp ON op.product_id = mp.product_id
                        INNER JOIN
                    ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = mp.seller_id
                        LEFT JOIN
                    ".DB_PREFIX."admin_change_log AS acl ON acl.table_id = op.product_id
                        AND field_name = 'status'
                WHERE
                    op.product_id = ".(int)$data['product_id']."
                        AND (acl.log_id IS NULL OR acl.log_id = 0
                        OR (acl.name = 'returnReportMailing'
                        AND DATE(acl.date_added) < DATE(DATE_SUB(NOW(), INTERVAL 30 DAY))))
                ORDER BY acl.log_id DESC
                LIMIT 0 , 1
               ";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            if($result->row['seller_invoice_generate'] == 1){
                $old_status = $result->row['status'];
                //Change Product status to Rejected
                $sql = "
                        UPDATE ".DB_PREFIX."product
                          SET
                            status = 4
                          WHERE
                            product_id = ".(int)$data['product_id']."
                       ";
                $this->db->query($sql);

                //Set Data to add into admin_change_log
                $admin_change_data                  = array();
                $admin_change_data['table_id']      = (int)$data['product_id'];
                $admin_change_data['user_id']       = 0;
                $admin_change_data['name']          = 'returnReportMailing';
                $admin_change_data['username']      = 'Automatic Cron';
                $admin_change_data['table_name']    = 'oc_product';
                $admin_change_data['source_field']  = 'cron';
                $admin_change_data['field_name']    = 'status';
                $admin_change_data['ref_url']       = 'index.php?route=cron/cron/returnReportMailing';
                $admin_change_data['old_value']     = (int)$old_status;
                $admin_change_data['new_value']     = 4;
                $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                $admin_change_data['ip_address']    = $this->request->getIpAddress;
                $admin_change_data['file_location'] = 'cron/cron/returnReportMailing';
                $admin_change_data['user_type']     = 'System';

                //Call dynamic static function for entry into admin change log
                CommonLib::addAdminChangeLog($this->db, $admin_change_data);
            }
        }else{
            $data = array();
        }
        return $data;
    }


    /**
     * @info: Public method send mail to seller to inform about disabled SKUs
     * @author: Nishu March 2018
    */
    public function sendMailToSellerForDisabledSkus() {
        //Invoke Method to get detail data for 2 days ago disabled products
        $data = $this->getDisabledProducts();
        if(!empty($data)){
            foreach ($data as $seller_id => $detail) {
                $html = '';
                //Mail Body content
                $html = MailTemplate::mailToSellerDisableSkuForReturn($detail);


                //Set BD Email Ids for specific to seller
                $bd_email_ids = SellerInfo::getBdEmailIdsBySeller($this->db, $seller_id, $detail['nickname'], $detail['pickup_code'] );
                if($html != ''){
                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPDebug = false;
                    $mail->Debugoutput = 'html';
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPAuth = true;
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');
                    $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
                    $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
                    $mail->addAddress($detail['email'], $detail['name']);
                    if( !empty($bd_email_ids) ){
                        foreach ($bd_email_ids as $key => $email_arr) {
                            $mail->addCC($email_arr['email_id'], $email_arr['name']);
                        }
                    }
                    $mail->Subject = 'List of disabled SKU: '.date('d/M/Y H:i:s') ;
                    $mail->msgHTML($html);

                    //send to admin;
                    $mail->send(1,false);
                }
            }
        }
        echo 'Completed';
    }

    /**
     * @info : Public Method to get data for Disabled SKUs 2 days ago From oc_admin_change_log
     * @author: Nishu, March 2018
    */
    public function getDisabledProducts(){
        $data = array();
        $product_images = array();
        $sql = "
                SELECT
                    op.*,
                    ms.seller_id,
                    ms.nickname,
                    ms.company,
                    ms.email,
                    ms.pickup_city_code
                FROM
                    ".DB_PREFIX."admin_change_log AS cl
                        INNER JOIN
                    ".DB_PREFIX."product AS op ON cl.table_id = op.product_id
                        INNER JOIN
                    ".DB_PREFIX."ms_product AS mp ON mp.product_id = op.product_id
                        INNER JOIN
                    ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = mp.seller_id
                WHERE
                    cl.name = 'returnReportMailing'
                        AND cl.table_name = 'oc_product'
                        AND cl.field_name = 'status'
                        AND cl.source_field = 'cron'
                        AND cl.old_value = '1'
                        AND cl.new_value = '4'
                        AND op.status = 4
                        AND DATE(cl.date_added) = DATE(DATE_SUB(NOW(), INTERVAL 2 DAY))
              ";

        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $temp_data = $result->rows;
            $p_ids = array_column($temp_data, 'product_id');
            //All unique Disabled product_ids
            $p_ids = array_unique($p_ids);
            $is_op_id = 0;
            //Get Product Images by Product ids
            MsProduct::setProductImages($this, $p_ids, $product_images, $is_op_id);

            foreach ($temp_data as $value) {
                if(!isset($data[$value['seller_id']])){
                    $data[$value['seller_id']]['name'] =  $value['company'];
                    $data[$value['seller_id']]['nickname'] =  $value['nickname'];
                    $data[$value['seller_id']]['email'] =  $value['email'];
                    $data[$value['seller_id']]['pickup_code'] =  'DL'; //$value['pickup_city_code'];
                    $data[$value['seller_id']]['products'] =  array();
                }
                $data[$value['seller_id']]['products'][$value['product_id']] =  array(
                                                                  'model'   => $value['model'],
                                                                  'sku'     => $value['sku'],
                                                                  'hsn_code'=> $value['hsn_code'],
                                                                  'image'   => $product_images['pid_to_imgs'][$value['product_id']],
                                                                  'image_height' => $product_images['image_height'],
                                                                  'image_width' => $product_images['image_width']
                                                                    );
            }
        }
        return $data;
    }


    /**
     * @info: Public method to send WSB Loss report to accounts
     * @author Nishu, March 2018
    */
    public function sendMailWsbLossReport(){
        $data = array();
        $sql  = "
                SELECT
                    ocr.return_id,
                    oop.order_id,
                    oop.order_product_id,
                    o.order_no,
                    o.customer_id,
                    CONCAT(o.firstname, ' ', o.lastname) as cust_name,
                    o.payment_company,
                    ocr.quantity,
                    ocr.return_action_id,
                    oop.transfer_price_per_piece as tp_price,
                    oop.seller_input_tax,
                    oop.seller_cst,
                    si.seller_invoice_meta,
                    orr.name as return_reason
                FROM
                    ".DB_PREFIX."return AS ocr
                        INNER JOIN
                    ".DB_PREFIX."return_reason AS orr ON ocr.return_reason_id = orr.return_reason_id
                        INNER JOIN
                    ".DB_PREFIX."order_product AS oop ON oop.order_product_id = ocr.order_product_id
                        INNER JOIN
                    ".DB_PREFIX."order AS o ON o.order_id = oop.order_id
                        INNER JOIN
                    ".DB_PREFIX."seller_invoice AS si ON si.seller_invoice_id = oop.seller_invoice_id
                WHERE
                    ocr.quantity > 0
                        AND ( ocr.return_reason_id IN (".
                                    RETURN_REASON_IDS['Loss_By_WSB'] .",".
                                    RETURN_REASON_IDS['Transfer_To_WSB_Books'] ."
                                  ) OR
                              ocr.return_action_id IN (". implode(',', WSB_LOSS_N_BOOKS_ACTION_IDS) ."))
                        AND DATE(ocr.date_added) >= DATE(DATE_SUB(NOW(), INTERVAL 70 DAY))
                        AND ocr.active_row = 1
                ORDER BY ocr.return_reason_id
               ";

        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
        }

        //Mail Body content
        $html = MailTemplate::mailToSendWsbLossReport($data);
        //Mail Sending Code
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPDebug = false;
        $mail->Debugoutput = 'html';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->Subject = 'Goods marked as Loss to WSB: '.date('d/M/Y H:i:s') ;
        $mail->msgHTML($html);

        //send to admin;
        $mail->send(1,false);

    }

    /**
     * Method of mail sent for parcel is getting missing with in or more 6 days
     * @author vikas, 27 March 2018
    */
    public function sendMailParcelMissingFromSixDays(){
        $data = array();
        $sql  = "SELECT o.order_id,
                        o.order_no,
                        o.shipping_city,
                        o.total,
                        o.currency_code,
                        o.currency_value
                 FROM " . DB_PREFIX . "order o
                 INNER JOIN " . DB_PREFIX . "order_history oh
                   ON ( oh.order_id = o.order_id )
                 WHERE o.stock_transfer = 1
                   AND oh.order_status_id IN (".
                                        (int)ORDER_STATUS['Out for delivery'].",".
                                        (int)ORDER_STATUS['Shipped'].",".
                                        (int)ORDER_STATUS['Shipped with tracking']."
                                       )
                 GROUP BY o.order_id
                 HAVING datediff(now(), date(min(oh.date_added))) >= 6
                 ORDER BY o.order_id ASC ";
        $result = $this->db->query($sql);

        if( $result->num_rows ){
            $html = '<table border=1>';
            $html .= '   <thead>';
            $html .= '       <tr>';
            $html .= '          <td>Order No</td>';
            $html .= '          <td>Shipping City</td>';
            $html .= '          <td>Total</td>';
            $html .= '       </tr>';
            $html .= '   </thead>';
            $html .= '   <tbody>';

            foreach ($result->rows as $result_data) {
                $html .= '       <tr>';
                $html .= '          <td>' . $result_data['order_no'] . '</td>';
                $html .= '          <td>' . $result_data['shipping_city'] . '</td>';
                $html .= '          <td>' . $this->currency->format($result_data['total'],$result_data['currency_code'],$result_data['currency_value']) . '</td>';
                $html .= '       </tr>';
            }

            $html .= '   </tbody>';
            $html .= '</table>';

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPDebug = false;
            $mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->Subject = 'Parcel(s) are missing form 6 days - '. date('d/M/Y H:i:s') ;
            $mail->msgHTML($html);
            $mail->send();
        }
    }

    public function processingOrderReport() {

      if(isset($this->request->get['start_date']) && isset($this->request->get['end_date']))
      {
        $start_date = $this->request->get['start_date'];
        $end_date   = $this->request->get['end_date'];
      }
      else
      {
        $start_date =date('Y-m',strtotime("-1 days")).'-01';
        $end_date  = date('Y-m-d',strtotime("-1 days"));
      }

      if(isset($this->request->get['city']))
      {
        $city = $this->request->get['city'];
      }
      else
      {
        $city = 'Ahmedabad';
      }

       $sql = "SELECT MS.nickname,
               MS.company,
               MS.seller_id,
               O.order_id,
               O.order_no,
               O.date_added,
               SO.suborder_id,
               SO.order_status_id,
               OP.order_product_id,
               OP.product_id,
               OP.name AS product_name,
               OP.seller_sku AS seller_sku,
               OP.model AS product_model,
               OP.pickup_status AS pickup_status,
               OH.date_added AS order_processing_date,
               OP.pickup_last_modified AS order_received_date
               FROM ".DB_PREFIX."order_product as OP"
               . " INNER JOIN ".DB_PREFIX."ms_seller AS MS ON MS.seller_id = OP.seller_id "
               . " INNER JOIN ".DB_PREFIX."order AS O ON O.order_id = OP.order_id "
               . " INNER JOIN ".DB_PREFIX."suborder AS SO ON SO.order_id = OP.order_id "
               . " INNER JOIN " . DB_PREFIX. "order_history AS OH ON OH.order_id = SO.order_id "
               . " WHERE O.store_id IN (".WSB_STORES_ID.")";
               $sql = $sql. " AND OP.pickup_status = 'Received'";
               $sql = $sql. " AND DATE(OH.date_added) >= '".$start_date."'";
               $sql = $sql. " AND DATE(OH.date_added) <= '".$end_date."'";
               $sql = $sql. " AND OH.order_status_id IN (".
                                         (int)ORDER_STATUS['Processed'].",".
                                         (int)ORDER_STATUS['Tentative Processed'].")";
               $sql = $sql. " AND MS.city = '".$city."'";
               $sql = $sql. " AND SO.suborder_id = OP.suborder_id";
               $sql = $sql. " AND OH.suborder_id = SO.suborder_id";
               $sql = $sql. " GROUP BY OP.order_product_id ORDER BY OH.date_added DESC";

        $query = $this->db->query($sql);

        $filename = DIR_DOWNLOAD.'processingOrderReport_'.date("d-m-Y").'.csv';
        // open the file "demosaved.csv" for writing
        $file = fopen($filename, 'w');
        // save the column headers
        fputcsv($file, array('Seller', 'Order ID', 'Order Number', 'Order Date', 'Process Date', 'Received Date', 'Product Name', 'Product Model', 'Seller Sku'));
        // Sample data. This can be fetched from mysql too
        $data = array();
        foreach($query->rows as $row)
        {

         $data[] = array($row['company'], $row['order_id'], $row['order_no'], $row['date_added'], $row['order_processing_date'], $row['order_received_date'], $row['product_name'], $row['product_model'], $row['seller_sku']);
        }

        // save each row of the data
        foreach ($data as $row)
        {
          fputcsv($file, $row);
        }
        // Close the file
        fclose($file);

        //delete last file
        $lastfile = DIR_DOWNLOAD.'processingOrderReport_'.'01-'.date('m-Y',strtotime("-1 days")).'.csv';
        @unlink($lastfile);

        //Mail Body content
        $html = 'Please find csv in attachment';
        //Mail Sending Code
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPDebug = false;
        $mail->Debugoutput = 'html';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->addAttachment($filename);
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
        $mail->Subject = 'Processing Order Report: '.date('d/M/Y') ;
        $mail->msgHTML($html);
        //send to admin;
        $mail->send(1,false);

        echo "Completed";
    }

    /**
     * @info: Private method to data for unlinked seller payments refrences
     * @param: $reference_keys -- refrences from oc_payment only from unlinked payments
     * @return: array $data
     * @author: Nishu, 2oth June 2019
    */
    private function getUnlinkedPaymentData($reference_keys): array{
        $bankName = 'stanc';
        $data     = array();

        $sql = "
                (
                    SELECT
                        osi.seller_invoice_id AS id,
                        'oc_seller_invoice' as tablename,
                        o.order_id,
                        o.order_no,
                        osi.seller_id,
                        osi.trxn_amount AS amount,
                        osi.trxn_utr AS refernce
                    FROM
                        ".DB_PREFIX."seller_invoice as osi
                    INNER JOIN
                        ".DB_PREFIX."order as o ON o.order_id = osi.order_id
                    WHERE
                        osi.trxn_utr in ('" . $reference_keys . "')
                        AND osi.trxn_done IN ('BANK_REQUESTED',
                                              'BANK_PROCESSED',
                                              'BANK_SUCCESS')
                        AND osi.trxn_bank = '" . $this->db->escape($bankName) . "'
                )
                UNION
                (
                    SELECT
                        osdn.debit_note_id AS id,
                        'oc_seller_debit_note' as tablename,
                        o.order_id,
                        o.order_no,
                        osdn.seller_id,
                        osdn.trxn_amount AS amount,
                        osdn.trxn_utr AS refernce
                    FROM
                        ".DB_PREFIX."seller_debit_note as osdn
                    INNER JOIN
                        ".DB_PREFIX."order as o ON o.order_id = osdn.order_id
                    WHERE
                        osdn.trxn_utr in ('" . $reference_keys . "')
                    AND osdn.trxn_done IN ('BANK_REQUESTED',
                                           'BANK_PROCESSED',
                                           'BANK_SUCCESS')
                    AND osdn.trxn_bank = '" . $this->db->escape($bankName) . "'
                )
                UNION
                (
                    SELECT
                        owp.purchase_id AS id,
                        'oc_wsb_purchase' as tablename,
                        '' AS order_id,
                        '' AS order_no,
                        owp.seller_id,
                        otd.trxn_amount AS amount,
                        otd.trxn_utr AS refernce
                    FROM
                        ".DB_PREFIX."wsb_purchase owp
                    INNER JOIN
                        ".DB_PREFIX."trxn_details otd ON otd.trxn_for_id = owp.purchase_id
                    WHERE
                        otd.trxn_for = 'WSB_PURCHASE'
                        AND otd.trxn_utr IN ('" . $reference_keys . "')
                        AND otd.trxn_done IN ('BANK_REQUESTED',
                                          'BANK_PROCESSED',
                                          'BANK_SUCCESS')
                        AND otd.trxn_bank = '" . $this->db->escape($bankName) . "'
                )
                UNION
                (
                    SELECT
                        otd.id AS id,
                        'oc_trxn_details' as tablename,
                        '' AS order_id,
                        '' AS order_no,
                        owp.seller_id,
                        otd.trxn_amount ,
                        otd.trxn_utr AS refernce
                    FROM
                        ".DB_PREFIX."trxn_details as otd
                    INNER JOIN
                        ".DB_PREFIX."wsb_purchase_return owpr ON owpr.debit_note_id = otd.trxn_for_id
                    INNER JOIN
                        ".DB_PREFIX."wsb_purchase owp ON owp.purchase_id = owpr.purchase_id
                    WHERE
                        otd.trxn_for = 'WSB_PURCHASE_RETURN'
                        AND otd.trxn_utr in ('" . $reference_keys . "')
                        AND otd.trxn_done IN ('BANK_REQUESTED',
                                              'BANK_PROCESSED',
                                              'BANK_SUCCESS')
                        AND otd.trxn_bank = '" . $this->db->escape($bankName) . "'
                )
                UNION
                (
                    SELECT
                        owsp.sor_payment_id AS id,
                        'oc_wsb_sor_payment' as tablename,
                        o.order_id,
                        o.order_no,
                        owsp.seller_id,
                        owsp.trxn_amount AS amount,
                        owsp.trxn_utr AS refernce
                    FROM
                        ".DB_PREFIX."wsb_sor_payment as owsp
                    INNER JOIN
                        ".DB_PREFIX."order as o ON o.order_id = owsp.order_id
                    WHERE
                        owsp.trxn_utr in ('" . $reference_keys . "')
                        AND owsp.trxn_done IN ('BANK_REQUESTED',
                                               'BANK_PROCESSED',
                                               'BANK_SUCCESS')
                        AND owsp.trxn_bank = '" . $this->db->escape($bankName) . "'
                )
            ";

        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
        }

        return $data;
    }

    /**
     * Private method to link, unlinked seller payment data from oc_payment table
     * @param: $unlinked_records array--> data from oc_payment table array keys payment refrences
     * @param: $data array
     * @param: $seller_ids array(Unique seller_ids for payment linking)
     *
     * @return: void
     * @author: Nishu, 21st June 2019
    */
    private function linkSellerPaymentPendingEntriesForGivenData($unlinked_records, $data, $seller_ids){

        if(!empty($data) && !empty($seller_ids) ){

            $sellerPaymentGroupID   = 7;
            $sellerPaymentLedgerID  = 12;
            $sundryCreditorsGroupID = 11;

            //Initialize array to inform mismatch amount internally
            $alert_error_data = array();

            //To get ledger id on seller_id starts here
            $seller_ledgers = $this->getSellerLedgers($seller_ids);

            $user_id    = 0;
            $user_name  = 'System Generated';
            $datedCM    = date("Y-m-d H:i:s");
            $user_array = array(
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'date' => $datedCM
                                );
            //Load model of khufiya Vibhag
            $this->load->model('account_panel/bankpayment' ,'admin');

            foreach ($data as $ref_no => $value) {

                $payment_id   = (int)($unlinked_records[$ref_no]['payment_id'] ?? 0);
                $ref_amount   = (float)($unlinked_records[$ref_no]['amount'] ?? 0);
                $total_amount = (float)($value['total_amount'] ?? 0);

                $seller_id    = (int)($value['seller_id'] ?? 0);

                //Check if diffrence amount is > 0.01, inform internal team to adjust payment
                if(abs($total_amount - $ref_amount) > 0.01 ){
                    $alert_error_data[$ref_no]['ref_data']     = $value;
                    $alert_error_data[$ref_no]['payment_data'] = $unlinked_records[$ref_no];
                    continue;
                }else{

                    //------------Link Seller Payment----------------------

                    // Get OR Create seller ledger ID
                    $seller_ledger_id = (int)($seller_ledgers[$seller_id] ?? 0);

                    if( empty($seller_ledger_id) ) {

                        //to get the seller details from sellerinfo library
                        $selector = array('select' => array('nickname','company','gst_provisional_id'));
                        $result   = SellerInfo::getSellerInfo($this->db, $seller_id, $selector);

                        $seller_details = $result[$seller_id] ?? array();

                        $gst      = $seller_details['gst_provisional_id'] ?? '';
                        $nickname = $seller_details['nickname'] ?? '';
                        $company  = $seller_details['company'] ?? '';

                        $sellerledgerName = $seller_id . '_'. $nickname . '_'. $company . $gst;

                        //=================================================
                        //Create Leadger If doesn't exists on basis of customer_id which is seller_id starts
                        //=================================================
                        $seller_ledger_id = $this->admin_model_account_panel_bankpayment->saveLedger( $sellerledgerName, $sundryCreditorsGroupID, $seller_id, $user_id, $datedCM, $user_array);

                        $seller_ledgers[$seller_id] = $seller_ledger_id;

                        $this->admin_model_account_panel_bankpayment->saveSellerLedger( $seller_id, $sellerledgerName );
                        $this->admin_model_account_panel_bankpayment->updateCustomerLedger( $seller_ledger_id, $seller_id);
                        //=========== Create Leadger If doesn't exists on basis of customer_id which is seller_id ends ===========//
                    }//End of If statement

                    //Create entry in oc_payment_sub table for oc_payment
                    $payment_sub_id = $this->admin_model_account_panel_bankpayment->insertOcPaymentSub(
                                            $payment_id,
                                            $sellerPaymentLedgerID,
                                            $sellerPaymentGroupID,
                                            $ref_amount,
                                            '',
                                            $user_id,
                                            $datedCM,
                                            $user_array);


                     // Loop over payment breakups, to link
                    foreach ($value['data'] as $key => $row_data) {

                        $ref_id        = (int)($row_data['id'] ?? 0);
                        $ref_tablename = $row_data['tablename'] ?? 0;
                        $order_id      = $row_data['order_id'] ?? 0;
                        $order_no      = $row_data['order_no'] ?? 0;

                        if ($row_data['amount'] > 0) {
                            $this->admin_model_account_panel_bankpayment->insertPaymentSubCSV(
                                                $payment_id,
                                                $payment_sub_id,
                                                $datedCM,
                                                $seller_ledger_id,
                                                $row_data['amount'],
                                                $order_no,
                                                $ref_no,
                                                0,
                                                $order_id,
                                                $ref_id,
                                                $ref_tablename );
                        }else{
                            $this->admin_model_account_panel_bankpayment->insertPaymentSubIncomesCr(
                                                $payment_id,
                                                $payment_sub_id,
                                                $datedCM,
                                                $seller_ledger_id,
                                                $row_data['amount'],
                                                $order_no,
                                                $ref_no,
                                                0,
                                                $order_id,
                                                $ref_id,
                                                $ref_tablename );
                        }
                    }// End of Foreach LOOP, for payment breakups

                }
            }

            //Send Alert (Internal Team), for amount mismatch
            if(!empty($alert_error_data )){
                $this->sendAlertForSellerPaymentAmountMismatch($alert_error_data);
            }
        }
        return;
    }

    /**
     * @info: Private Cron method to send Alert mail internally,
     *         to notify about Seller payment amount mismatch against refrence number
     * @param: $data Array, data about payment related and its breakup available
     * @return: Void
     * @author: Nishu, 21st June 2019
    */
    private function sendAlertForSellerPaymentAmountMismatch($data){
        if(!empty($data)){

            $html = MailTemplate::sendAlertForSellerPaymentAmountMismatch( $data );
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
            $mail->addReplyTo(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);

            $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
            $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);

            $mail->Subject = 'Alert: Seller Payment Amount Mismatch, payment linking(' . date("d F Y H:i") . ')';
            $mail->msgHTML($html);
            $mail->send(0, false);

        }
    }

    /**
    * @info: To link seller payment entries which are not linked yet
    *        Step 1 -> Get seller payment enteries which are not linked yet
    * @author Ashish 4-4-2018
    * @author: Updated By Nishu, 20-06-2019
    */
    public function linkSellerPaymentPendingEntries(){

        $stancLedgerID          = 379;

        //--------- To get Unlinked Payment Entries start ---------//
        $sql = "SELECT
                    op.payment_id,
                    op.reference,
                    op.amount,
                    op.dated,
                    op.ledger_id,
                    ol.ledger_name
                FROM
                    ".DB_PREFIX."payment op
                INNER JOIN
                    ".DB_PREFIX."ledger ol on op.ledger_id = ol.ledger_id
                LEFT JOIN
                    ".DB_PREFIX."payment_sub ops on ops.payment_id = op.payment_id
                where
                    ops.payment_sub_id is null
                    AND op.ledger_id = " . (int)$stancLedgerID;

        $unlinked_entries = $this->db->query($sql);

        //Array initialize
        $unlinked_records = array();

        //Unlink Payment enteries exists
        if($unlinked_entries->num_rows > 0){

            $unlinked_records = array_combine(
                                    array_column($unlinked_entries->rows, 'reference'),
                                    $unlinked_entries->rows
                                );

            //Unique refrence keys
            $reference_keys = implode("' , '",array_keys($unlinked_records));

            $result = $this->getUnlinkedPaymentData($reference_keys);

            if(!empty($result)){
                $seller_ids = array();
                $data       = array();
                foreach ($result as $key => $value) {
                    $ref                         = $value['refernce'] ?? '';
                    $data[$ref]                  = $data[$ref] ?? array();

                    //Set seller_id for refrence number
                    $data[$ref]['seller_id']     = $value['seller_id'];

                    //To get ledger id on seller_id starts here
                    $seller_ids[$value['seller_id']] = $value['seller_id'];

                    //Set Total amount for refrence number
                    $data[$ref]['total_amount']  = $data[$ref]['total_amount'] ?? 0;
                    $data[$ref]['total_amount'] += $value['amount'];

                    $data[$ref]['data'][]        = $value;
                }

                //To link, unlinked seller payment data from oc_payment table
                $this->linkSellerPaymentPendingEntriesForGivenData($unlinked_records, $data, $seller_ids);
            }

        }else{
            echo("NO Unlinked Seller Payment Entries!!<br>");
        }

        echo "Cron Completed.";
    }


    /**
    * To get ledger_id from seller_id on every customer_id
    * @param  array  $seller_id seller_id
    * @author Ashish 4-4-2018
    * @Updated: By Nishu, 21 st June 2019
    */
    private function getSellerLedgers($seller_ids){
        $result = array();

        if (!empty($seller_ids)) {

            $seller_ids_imploded = implode(',', $seller_ids);
            $sql = "SELECT
                        customer_id,
                        ledger_id
                    FROM
                        ".DB_PREFIX."ledger
                    WHERE
                        customer_id IN (" . $seller_ids_imploded . ")
                        AND group_id = 11
                    GROUP BY
                        customer_id ";
            $query = $this->db->query($sql);

            if($query->num_rows > 0 ){

                $result = array_combine(
                            array_column($query->rows, 'customer_id'),
                            array_column($query->rows, 'ledger_id')
                         );
            }
        }

        return $result;
    }

    /**
     * kusum joshi
     * get Failure Logs and requeue them again
     */

    public function requeueFailureLog(){

         $sql = "SELECT *
                    FROM " . DB_PREFIX . "crm_failure_queue_log ";
            $query = $this->db->query($sql);

            if($query->num_rows){
                foreach ($query->rows as $index => $data) {
                    if($data['queue_name'] == 'lead_updation_queue'){
                        $failure_log = unserialize($data['failure_log']);

                         $lead = new updateLead($failure_log['data'], $failure_log['mobile'], $failure_log['message'], $failure_log['customer_id']);
                         $result = $lead->updateLeadData();
                         if($result){
                             $result = $this->db->query("DELETE FROM " . DB_PREFIX . "crm_failure_queue_log WHERE failure_queue_id = " . (int) $data['failure_queue_id']);
                         }
                    }
                }
            }

            die('success');
    }
    /*
     * Function autoProcessOrders: will do Auto Processing of good_to_process orders.
     * If customer already has 3 or more processed orders, it will update order to 'Processed' status.
     * If customer has less than 3 processed orders, then it will update order to 'Tentative Processed' status.
     * Orders having customer comments, or products which require checking, are not auto-processed.
     * @author: NILESH (2018)
     */
    public function autoProcessOrders() {

        $product_ids_to_check = PRODUCT_IDS_NOT_CHECK_FOR_AUTO_PROCESSING;

        //Finding suborders in good_to_process and having no customer comments / no product_ids requiring check
        $sql = "SELECT oo.order_id,
                       os.suborder_id,
                       oo.customer_id,
                       oo.shipping_postcode,
                       oo.shipping_zone_id,
                       oo.payment_code,
                       GROUP_CONCAT(DISTINCT TRIM(oop.customer_comment) ) AS customer_comments,
                       GROUP_CONCAT(DISTINCT oop.product_id) AS product_ids
                FROM ".DB_PREFIX."order oo INNER JOIN
                     ".DB_PREFIX."suborder os ON oo.order_id = os.order_id INNER JOIN
                     ".DB_PREFIX."order_product oop ON os.order_id = oop.order_id
                WHERE oo.operations_status = 'good_to_process' AND
                      os.suborder_id = oop.suborder_id AND
                      os.order_status_id = ".(int)ORDER_STATUS['Pending']."
                GROUP BY os.suborder_id
                HAVING (customer_comments IS NULL OR customer_comments = '') ";
        if (!empty($product_ids_to_check)) {
            $sql .= " AND product_ids NOT IN (" . implode(',', $product_ids_to_check) . ") ";
        }
        $query = $this->db->query($sql);


        if ($query->num_rows) {
            $suborders_to_process = $query->rows;
            $unique_customer_ids = array_values(array_unique(array_column($suborders_to_process, 'customer_id')));

            $pincode_wise_arr = array();
            // Getting count of processed orders customer_id wise
            $cus_sql = "SELECT o.customer_id,
                               COUNT(DISTINCT o.order_id) AS processed_orders
                       FROM ".DB_PREFIX."order o
                       INNER JOIN ".DB_PREFIX."suborder osub
                         ON osub.order_id = o.order_id
                       WHERE o.customer_id IN (" . implode(',', $unique_customer_ids) . ")
                         AND o.store_id IN (0,2,9)
                         AND osub.order_status_id > ".(int)ORDER_STATUS['Canceled']."
                       GROUP BY o.customer_id ";
            $cus_query = $this->db->query($cus_sql);

            $customerwise_processed_orders_count = array();
            if ($cus_query->num_rows) {
                $customerwise_processed_orders_count = array_combine(array_column($cus_query->rows, 'customer_id'), $cus_query->rows);
            }

            // Processing Suborders
            $this->load->model('checkout/order');
            foreach ($suborders_to_process as $suborder) {
                if ($suborder['payment_code'] == 'cod') {
                    if (!isset($pincode_wise_arr[$suborder['shipping_postcode']])) {
                        $order_info = array(
                            'pincode' => $suborder['shipping_postcode'],
                            'payment_code' => $suborder['payment_code'],
                            'zone_id' => $suborder['shipping_zone_id']
                        );
                        $obj_logistic_advisor = new LogisticsAdvisor($order_info, $this);
                        $data_value = $obj_logistic_advisor->getAdvise();
                        $delivery_courier = 0;
                        if (!empty($data_value)) {

                            foreach ($data_value as $courier_part_key => $courier_part_info) {
                                if ($courier_part_key == 'paperwork' || $courier_part_key == 'fedex') {
                                    continue;
                                }
                                if ($courier_part_info['is_serviceable'] &&
                                        $courier_part_info['cod'] &&
                                        strtolower($courier_part_info['serviceability']) != 'ess') {
                                    $delivery_courier = 1;
                                    break;
                                } else if ($courier_part_info['is_serviceable'] &&
                                        $courier_part_info['cod'] &&
                                        strtolower($courier_part_info['serviceability']) == 'ess') {
                                    $pincode_wise_arr[$suborder['shipping_postcode']]['is_ess'] = 1;
                                }
                            }
                        }
                        $pincode_wise_arr[$suborder['shipping_postcode']]['is_delivery'] = $delivery_courier;
                    }

                    if ((isset($pincode_wise_arr[$suborder['shipping_postcode']]['is_delivery']) &&
                            ($pincode_wise_arr[$suborder['shipping_postcode']]['is_delivery'] == 0))) {
                        if(!empty($pincode_wise_arr[$suborder['shipping_postcode']]['is_ess'])
                                &&
                                ((float) OrderEdit::getEssShippingCharges($this->db, $suborder['order_id'], $suborder['suborder_id']) > 0)) {

                        } else {
                            continue;
                        }

                    }
                }
                $order_status = 16; // Initializing to Tentative Processed status
                if (isset($customerwise_processed_orders_count[$suborder['customer_id']]) && (int) $customerwise_processed_orders_count[$suborder['customer_id']]['processed_orders'] >= 3) {
                    $order_status = 9;
                }
                $data = array(
                    'order_id' => $suborder['order_id'],
                    'suborder_id' => $suborder['suborder_id'],
                    'order_status_id' => $order_status,
                    'user' => 'Operations Bot',
                    'notify_email' => 0,
                    'notify_sms' => 0,
                    'comment' => '',
                    'notes' => 'Auto Processed',
                    'shippingco' => '',
                    'tracking' => '',
                    'give_cashback' => 1
                );
                $this->model_checkout_order->addOrderHistory($data);
            }
        }
    }

    /*
     * Function autoPlaceMissingOrders: will do Auto Pending from Missing order at after every 6 hours only if there is no order of that customer on before 2 hours
     * @author: NILESH (2018)
     */
    public function autoPlaceMissingOrders() {

        $max_hours_for_fetch_result = 6;
        $minimum_hours_for_missin_order_check = 2;
        if (isset($this->request->get['max_hours_for_fetch_result']) && isset($this->request->get['minimum_hours_for_missin_order_check'])) {
            $max_hours_for_fetch_result = (int) $this->request->get['max_hours_for_fetch_result'];
            $minimum_hours_for_missin_order_check = (int) $this->request->get['minimum_hours_for_missin_order_check'];
        }
        $sql = "SELECT MAX(IF(os.order_status_id=0,os.order_id,0)) as max_order_id,
                       MAX(IF(os.order_status_id=0,os.suborder_id,0)) as max_suborder_id,
                       SUM(IF(os.order_status_id>0,1,0)) as order_statuses,
                       MAX(IF(os.order_status_id=0,os.date_added,NULL)) as max_date_added_check,
                       oo.customer_id
                 FROM ".DB_PREFIX."order oo INNER JOIN
                      ".DB_PREFIX."suborder os ON oo.order_id=os.order_id
                 WHERE os.date_added >= DATE_SUB(NOW(),INTERVAL ".$max_hours_for_fetch_result." HOUR)
                 GROUP BY oo.customer_id
                 HAVING order_statuses = 0 AND
                        max_date_added_check <=  DATE_SUB(NOW(),INTERVAL ".$minimum_hours_for_missin_order_check." HOUR) AND
                        max_order_id > 0 AND
                        max_suborder_id > 0 ";
        $query = $this->db->query($sql);
        if($query->num_rows) {
            $this->load->model('checkout/order');
            foreach ($query->rows as $value) {
                //Now updating auto pending from missing
                $data = array(
                    'order_id' => $value['max_order_id'],
                    'suborder_id' => $value['max_suborder_id'],
                    'order_status_id' => (int)ORDER_STATUS['Pending'],
                    'user' => 'Operations Bot',
                    'notify_email' => 1,
                    'notify_sms' => 1,
                    'comment' => '',
                    'notes' => 'Auto Placement From Missing Order',
                    'shippingco' => '',
                    'tracking' => '',
                    'give_cashback' => 1
                );
                $this->model_checkout_order->addOrderHistory($data);
            }
        }
    }


    /**
     * @info: Public method to Notify Internal to about returns marked as self-shipment
     *   from more then 3 days
     * @author: Nishu, May 2018
    */
    public function sendAlertForPendingSelfShipment(){
        $sql = "
                SELECT
                   ocr.quantity as return_quantity,
                   oop.model,
                   oop.product_id,
                   oop.order_product_id,
                   oop.order_id,
                   o.order_no,
                   orr.name as return_reason,
                   orr. reason_type as return_type
                FROM
                   ".DB_PREFIX."return AS ocr
                     INNER JOIN
                ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                     INNER JOIN
                ".DB_PREFIX."order_product AS oop ON ocr.order_product_id = oop.order_product_id
                     INNER JOIN
                ".DB_PREFIX."order AS o ON o.order_id = oop.order_id
                WHERE
                   ocr.active_row = 1
                   AND ocr.return_action_id = ".RETURN_ACTION_IDS['Self_Shipment']."
                   AND NOW() > date_add(ocr.date_added, INTERVAL 3 DAY)
               ";
        $result =  $this->db->query($sql);

        if($result->num_rows > 0){
            $data  = $result->rows;
            $opids = array_column($data, 'order_product_id');

            $product_images = array();
            //Set Product Images
            MsProduct::setProductImages($this, $opids, $product_images);

            $html = MailTemplate::mailPendingSelfShipments($data, $product_images);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);

            //$mail->addAddress(EMAIL_IDS['nishu']['email_id'], EMAIL_IDS['nishu']['name']);

            $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
            $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);

            $mail->Subject = 'Return(s) Pending as SELF-SHIPMENT(' . date("d F Y H:i") . ')';
            $mail->msgHTML($html);
            $mail->send();

            echo 'Completed';
        }
    }

    /**
     * @info: Public method to Notify Internal to about returns marked as self-shipment
     *   from more then 3 days
     * @author: MSA, May 2018
    */
    public function sendAlertForPendingGoodsReceived(){
        //Return Action Ids for Goods Received
        $goods_received_return_action = array(
                                            RETURN_ACTION_IDS['Goods_Received'],
                                            RETURN_ACTION_IDS['Extra_Goods_Received'],
                                            RETURN_ACTION_IDS['Short_Goods_Received']
                                        );

        $sql = "
                SELECT
                   ocr.quantity as return_quantity,
                   oop.model,
                   oop.product_id,
                   oop.order_product_id,
                   oop.order_id,
                   o.order_no,
                   orr.name as return_reason,
                   orr. reason_type as return_type
                FROM
                   ".DB_PREFIX."return AS ocr
                     INNER JOIN
                ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                     INNER JOIN
                ".DB_PREFIX."order_product AS oop ON ocr.order_product_id = oop.order_product_id
                     INNER JOIN
                ".DB_PREFIX."order AS o ON o.order_id = oop.order_id
                WHERE
                   ocr.active_row = 1
                   AND ocr.return_action_id IN (". implode(',', $goods_received_return_action) .")
                   AND NOW() > date_add(ocr.date_added, INTERVAL 48 HOUR)
               ";

        $result =  $this->db->query($sql);
        if($result->num_rows > 0){
            $data  = $result->rows;
            $opids = array_column($data, 'order_product_id');

            $product_images = array();
            //Set Product Images
            MsProduct::setProductImages($this, $opids, $product_images);

            $html = MailTemplate::mailPendingGoodsReceived($data, $product_images);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->Subject = 'Return(s) Pending as GOODS RECEIVED(' . date("d F Y H:i") . ')';
            $mail->msgHTML($html);
            $mail->send();

            echo 'Completed';
        }
    }

    /**
     * Method to get COD Failed suborders, for which Debit Notes
     * have not been generated yet.
     * @author: vikas, 2018
     */
    public function getCODFailedSubordersButDebitNoteNotGenerated(){

        $sql = "SELECT
                    DISTINCT osub.suborder_id, osub.courier_partner, osub.tracking_no
                FROM ".DB_PREFIX."suborder osub
                WHERE osub.order_status_id = ".(int)ORDER_STATUS['Failed']."
                  AND DATE(osub.date_added) >= DATE('2018-04-01')
                  AND NOT EXISTS
                        (
                            SELECT 1 FROM ".DB_PREFIX."seller_debit_note osdn WHERE osdn.suborder_id = osub.suborder_id AND osdn.debit_note_status = 1
                        )
                ORDER BY osub.order_id ASC";

        $query = $this->db->query($sql);

        if( $query->num_rows ){

            $file_name = DIR_DLOAD . 'cod_failed_not_generated_debit_note.csv';

            $fp = fopen($file_name,'w');
            $data = array(
                            'Suborder No.',
                            'Courier Partner',
                            'Tracking Number'
                        );
            fputcsv($fp, $data);

            foreach ($query->rows as $key => $records) {

                $data = array(
                                $records['suborder_id'],
                                $records['courier_partner'],
                                $records['tracking_no']
                            );
                fputcsv($fp, $data);
            }

            fclose($fp);

            $body = "PFA the list of Suborders having COD Failed status, but no Debit notes have been generated yet.";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);

            $mail->Subject = "COD Failed but DN not generated yet - " . date('d/M/Y H:i:s', time());
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);
            $mail->send(1,false);
        }
        echo 'Completed';
    }

    /**
    * Currency converter function used to exchange the currenct from USD to INR, SGD, EUR ets
    * Openexchange rates API is used for currency conversion
    * Base cann't be changed, it will be USD only
    *
    *
    * @author Ashish (18-June-2018)
    */

    public function currencyexchange()
    {
        // Initialize CURL:
        $url = curl_init(OPEN_EXCHANGE_CURRENCY_CONVERSION_URL.OPEN_EXCHANGE_CURRENCY_CONVERSION_KEY);
        // prd($url);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($url);
        curl_close($url);

        // Decode JSON response:
        $exchangeRates = json_decode($json, true);

        $this->load->model('localisation/currency');
        $currency_list = $this->model_localisation_currency->getCurrencies();
        // unset default INR currency
        unset($currency_list['INR']);

        $international_price_hike_factor = $currency_list[DUMMY_INR_CURRENCY]['value'];

        // also unset the dummy inr currency
        unset($currency_list[DUMMY_INR_CURRENCY]);

        $inr = $exchangeRates['rates']['INR'];

        foreach ($currency_list as $currency) {
          $currency_code = $currency['code'];

          if (!isset($exchangeRates['rates'][$currency_code])) continue;

          $cur = $exchangeRates['rates'][$currency_code];

          $rate = $cur / $inr ;

          $wsb_rate = ( $cur / $inr ) * $international_price_hike_factor;

          $sql = "UPDATE
                      " . DB_PREFIX . "currency
                  SET
                      value = '" . (float)$wsb_rate . "',
                      conversion_rate = '" . (float)$rate . "'
                  WHERE
                      code = '" . $this->db->escape($currency_code) . "' ";

          $result = $this->db->query($sql);

        }

        echo "Successfully updated the currencies.";
        exit();
    }

  /*
  * Method For get Orders which dispatched without full payment
  * @author: Vikas, June 2018
  */
  public function getOrdersDispatchedWithoutFullPayment(){
    $day = 1;
    if(!empty($this->request->get['day'])){
        $day = (int)$this->request->get['day'];
    }

    $base_qry = "
                SELECT 
                  o.order_id,
                  o.order_no,
                  o.payment_city,
                  o.payment_code,
                  o.payment_method,
                  o.date_added AS order_date,
                  TRIM(CONCAT(o.firstname, ' ', o.lastname)) AS customer_name,
                  MIN(oh.date_added) AS history_date
                FROM
                  oc_order AS o
                    INNER JOIN
                  oc_order_history AS oh ON o.order_id = oh.order_id
                    AND oh.order_status_id IN (".
                                              (int)ORDER_STATUS['Out for delivery'].",".
                                              (int)ORDER_STATUS['Complete'].",".
                                              (int)ORDER_STATUS['Shipped'].",".
                                              (int)ORDER_STATUS['Shipped with tracking'].",".
                                              (int)ORDER_STATUS['Delivered'].")
                WHERE
                  o.payment_code NOT IN ('cod', '" .implode("', '", CREDIT_PAYMENT_CODES) . "')
                  AND o.date_added >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                  AND o.store_id IN (". WSB_STORES_ID .")
                  AND o.franchise_id = 0
                  AND o.stock_transfer = 0
                GROUP BY 
                  o.order_id
                HAVING
                  DATE(history_date) >= DATE(DATE_SUB(NOW(), INTERVAL $day DAY))
                ";
    
    $base_results = $this->db->query($base_qry);

    if($base_results->num_rows > 0){
      
      $order_ids = array_column($base_results->rows, 'order_id');
      $base_data = array_combine(
                      array_column($base_results->rows, 'order_id'),
                      $base_results->rows 
                    );

      //Check order balance is not recovered for given order_ids
      $filter_data = array(
                      "where"         => array(" o.order_id IN (". implode(',', $order_ids) . ") " ),
                      "having"        => " HAVING order_bal < -5 ",
                      "suborder"      => array("where" => array(" order_id IN (".implode(',', $order_ids).") " ) ),
                      "order_payment" => array("where" => array(" order_id IN (".implode(',', $order_ids).") " ) ),
                      "credit_note"   => array("where" => array(" order_id IN (".implode(',', $order_ids).") " ) )
                    );

      //get Order Balance 
      $order_wise_bal = OrderAccounts::getOrderBalance($this->db, $filter_data);
      
      $file_name = DIR_DLOAD . 'orders_disptach_without_full_payment.csv';
      $fp = fopen($file_name,'w');
      $data = array(
                      'Order No.',
                      'Customer Name',
                      'City',
                      'Payment Type',
                      'Payment Method',
                      'Order Date',
                      'Order Balance'
                  );
      fputcsv($fp, $data);

      foreach ($order_wise_bal as $order_id => $bal_data) {

          $data = array(
                          $base_data[$order_id]['order_no'],
                          $base_data[$order_id]['customer_name'],
                          $base_data[$order_id]['payment_city'],
                          $base_data[$order_id]['payment_code'],
                          $base_data[$order_id]['payment_method'],
                          $base_data[$order_id]['order_date'],
                          $bal_data['order_bal']
                      );
          fputcsv($fp, $data);
      }

      fclose($fp);

      $body = "PFA the list of Prepaid Orders Dispatched without Full payment.";

      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = $this->config->get('config_mail_smtp_hostname');
      $mail->Port = $this->config->get('config_mail_smtp_port');
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = $this->config->get('config_mail_smtp_username');
      $mail->Password = $this->config->get('config_mail_smtp_password');

      $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
      $mail->addReplyTo(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);

      $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
      $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
      $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);

      $mail->Subject = "Prepaid Orders Dispatched Without Full Payment  - " . date('d/M/Y H:i:s', time());
      $mail->AddAttachment($file_name);
      $mail->msgHTML($body);
      $mail->send(1,false);
    }

    echo 'Completed';
  }

    /**
    * Method For get all Orders shipments data with tracking details
    *               Forward shipments
    *               Reverse Shipment (Return Penal)
    *               BackToCustomer Shipment (Return Penal)
    * @author: MSA, July 2018
    */
    public function getAllOrdersShipments()
    {
        $data = array();

        $start_date     = null;
        $end_date       = null;
        $date_condition_arr = array();
        $date_condition = '';
        $email_subject  = '';

        $from = "Beginning";
        if(!empty($this->request->get['start_date'])) {
            $start_date = date('Y-m-d',strtotime($this->request->get['start_date']));
            $date_condition_arr[] = " DATE(master_db.date_added) >= DATE('" . $start_date . "') ";
            $from = $start_date;
        }
        $to = "Today";
        if(!empty($this->request->get['end_date'])) {
            $end_date = date('Y-m-d',strtotime($this->request->get['end_date']));
            $date_condition_arr[] = " DATE(master_db.date_added) <= DATE('" . $end_date . "') ";
            $to = $end_date;
        }

        if (empty($date_condition_arr)) {
            $date_condition = " DATE(master_db.date_added) =  DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) ";
            $from = $to = date('Y-m-d', strtotime('-1 day'));
        } else {
            $date_condition = implode(" AND ", $date_condition_arr);
        }

        $email_subject = "From " . $from . " To " . $to;

        // Get shipments, generated for formward shipping from Order Info > Courier Partner tab
        $sql = "
                SELECT
                        'Forward Shipment' as type,
                        oor.order_no,
                        oc.master_id,
                        CONCAT(oor.shipping_firstname,' ',oor.shipping_firstname) as name,
                        oor.shipping_address_1 as address_1,
                        oor.shipping_address_2 as address_2,
                        oor.shipping_city as city,
                        oor.shipping_postcode as postcode,
                        oc.email,
                        oc.telephone as phone,
                        ocp.courier_name,
                        master_db.docket_no,
                        '' as 'token_no',
                        DATE_FORMAT(master_db.date_added,'%d-%m-%Y') AS date_added
                FROM
                    ".DB_PREFIX."courier_dockets master_db
                INNER JOIN
                    ".DB_PREFIX."courier_partners ocp ON ocp.id = master_db.courier_partners_id
                INNER JOIN
                    ".DB_PREFIX."order oor ON oor.order_id = master_db.order_id
                INNER JOIN
                    ".DB_PREFIX."customer oc ON oc.customer_id = oor.customer_id
                WHERE
                    master_db.docket_no != '' AND master_db.docket_no IS NOT NULL
                    AND
                    ".$date_condition."
        ";
        $result = $this->db->query($sql);
        if($result->num_rows){
            $data = $result->rows;
        }

        // Get shipments, generated for reverse shipping from Return Penal > Reverse Shipments
        $sql = "
            SELECT
                    'Reverse Shipment' as type,
                    master_db.order_no,
                    oc.master_id,
                    master_db.shipping_details,
                    master_db.courier_company,
                    master_db.tracking_no,
                    master_db.token_no,
                    DATE_FORMAT(master_db.date_added,'%d-%m-%Y') AS date_added
            FROM
                ".DB_PREFIX."return_shipment_tracking master_db
            INNER JOIN
                ".DB_PREFIX."order oor ON oor.order_id = master_db.order_id
            INNER JOIN
                ".DB_PREFIX."customer oc ON oc.customer_id = oor.customer_id
            WHERE
                master_db.tracking_no != ''
                AND
                master_db.tracking_no IS NOT NULL
                AND
                ".$date_condition."

        ";
        $result = $this->db->query($sql);
        if($result->num_rows){
            foreach ($result->rows as $key => $value) {
                $shipping_details = unserialize($value['shipping_details']);
                $data[] = array(
                    'type'      => $value['type'],
                    'order_no'  => $value['order_no'],
                    'master_id' => $value['master_id'],
                    'name'      => $shipping_details['name'],
                    'address_1' => $shipping_details['address_1'],
                    'address_2' => $shipping_details['address_2'],
                    'city'      => $shipping_details['city'],
                    'postcode'  => $shipping_details['postcode'],
                    'email'     => $shipping_details['email'],
                    'courier_name' => $value['courier_company'],
                    'docket_no' => $value['tracking_no'],
                    'token_no'  => $value['token_no'],
                    'date_added'=> $value['date_added'],
                );
            }
        }

        // Get shipments, generated for back to customer shipping from Return Penal > Back To Customer Shipments
        $sql = "
            SELECT
                    'Back To Customer Shipment' as type,
                    master_db.order_no,
                    oc.master_id,
                    master_db.shipping_details,
                    master_db.courier_company,
                    master_db.tracking_no,
                    '' as 'token_no',
                    DATE_FORMAT(master_db.date_added,'%d-%m-%Y') AS date_added
            FROM
                ".DB_PREFIX."return_shipment_backto_customer master_db
            INNER JOIN
                ".DB_PREFIX."order oor ON oor.order_id = master_db.order_id
            INNER JOIN
                ".DB_PREFIX."customer oc ON oc.customer_id = oor.customer_id
            WHERE
                master_db.tracking_no != '' AND master_db.tracking_no IS NOT NULL
                AND
                ".$date_condition."

        ";
        $result = $this->db->query($sql);
        if($result->num_rows){
            foreach ($result->rows as $key => $value) {
                $shipping_details = unserialize($value['shipping_details']);
                $data[] = array(
                    'type'      => $value['type'],
                    'order_no'  => $value['order_no'],
                    'master_id' => $value['master_id'],
                    'name'      => $shipping_details['name'],
                    'address_1' => $shipping_details['address_1'],
                    'address_2' => $shipping_details['address_2'],
                    'city'      => $shipping_details['city'],
                    'postcode'  => $shipping_details['postcode'],
                    'email'     => $shipping_details['email'],
                    'courier_name' => $value['courier_company'],
                    'docket_no' => $value['tracking_no'],
                    'token_no'  => $value['token_no'],
                    'date_added'=> $value['date_added'],
                );
            }
        }

      /* Generate csv file for shipments data */
            $file_name = DIR_DLOAD . 'All_Shipments_Tracking.csv';
            $fp = fopen($file_name,'w');
            $csv_data = array(
                            'Shipment Type',
                            'Order No.',
                            'Master ID',
                            'Client Name',
                            'Address',
                            'Email ID',
                            'Phone No.',
                            'Courier Company',
                            'Tracking No.',
                            'Token No.',
                            'Request Given Date'
                        );
            fputcsv($fp, $csv_data);

            foreach ($data as $key => $value) {

                $address = $value['address_1'];
                if(!empty($value['address_2'])){
                    $address .= ', '.$value['address_2'];
                }
                $csv_data = array(
                                $value['type'],
                                $value['order_no'],
                                $value['master_id'],
                                $value['name'],
                                $address,
                                $value['email'],
                                $value['phone'],
                                $value['courier_name'],
                                $value['docket_no'],
                                '',
                                $value['date_added']
                            );
                fputcsv($fp, $csv_data);
            }

            fclose($fp);

            $body = "PFA the list of shipments generated as Forward shipment, Reverse shipment && BackToCustomer shipment";
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');

            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);

            //$mail->addAddress(EMAIL_IDS['manoj']['email_id'], EMAIL_IDS['manoj']['name']);

            $mail->Subject = "Shipment Tracking Details  - " . $email_subject;
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);
            $mail->send(1,false);

           echo 'CSV File generated with shipments details';
    }

    /**
    * Method to get the Product(s) Expected Dispatch Date based on Days from Today
    *
    * @author Ashish, August 2018
    */
    public function alertProductsOnExpectedDispatchDate()
    {
        $days = !((int)$this->request->get['days']) ?? 1 ;

        if ( !empty($this->request->get['pickup_city_code']) )
        {
            $seller_city = (array) $this->request->get['pickup_city_code'];
        } else {
            $seller_city = array('ST');
        }

        $city_code = implode(" ',' ", $seller_city);

        $sql = "SELECT
                    op.product_id,
                    op.model,
                    op.sku,
                    opd.name AS product_name,
                    CONCAT(oms.nickname, ' ', oms.company) AS seller_name,
                    op.expected_dispatch_date
                FROM
                    oc_product op
                INNER JOIN
                    oc_product_description opd ON opd.product_id = op.product_id
                INNER JOIN
                    oc_ms_product omp ON omp.product_id = op.product_id
                INNER JOIN
                    oc_ms_seller oms ON oms.seller_id = omp.seller_id
                WHERE
                    opd.language_id = 1
                    AND date(op.expected_dispatch_date) >= date(NOW())
                    AND date(op.expected_dispatch_date) <= DATE(DATE_ADD(NOW(), INTERVAL $days DAY))
                    AND oms.pickup_city_code IN ('" . $city_code . "') ";

        $result = $this->db->query($sql);

        if ( $result->num_rows )
        {
            $data['product_expected_dispatch_date'] = $result->rows;
            $html = MailTemplate::mailProductsOnExpectedDispatchDate($data, $days);

            $today_date = date("d/F/Y");
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
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);

            if(!empty($seller_city)){
                foreach ( $seller_city as $key => $city_code )
                {
                    if ( !empty(EMAIL_IDS['BD'][$city_code]) )
                    {
                        foreach (EMAIL_IDS['BD'][$city_code] as $email_array)
                        {
                            $mail->addAddress($email_array['email_id'], $email_array['name']);
                        }
                    }
                }
            }

            $mail->Subject = 'Product(s) with Expected Dispatch Date within ' . $days . ' Day(s) - ' . date('d/M/Y H:i:s', time());
            $mail->msgHTML($html);

            if ($mail->send(1,false))
            {
                echo "success";
            } else {
                echo "error";
            }
        }
    }

    /**
    * function to get the Seller Cancellation Rate based on Days from Today
    *
    * @author Ashish, September 2018
    */
    public function sellerCancellationReport()
    {
        if ( !empty($this->request->get['days']) )
        {
            $days = $this->request->get['days'];
        } else {
            $days = 30 ;
        }

        $sql = "SELECT
                    CONCAT(oms.nickname, ' ', oms.company) AS seller_name,

                    SUM(oop.quantity * oop.piece_in_set) AS no_of_sku_ordered,

                    SUM(IF(oop.edit_type = 'SELLER_NOT_SUPPLIED', oop.quantity * oop.piece_in_set, 0)) as no_of_sku_cancelled,

                    count( DISTINCT oop.order_id ) AS no_of_order_received,

                    count( DISTINCT IF(oop.edit_type = 'SELLER_NOT_SUPPLIED', oop.order_id, NULL) ) AS no_of_order_edited

                FROM
                    oc_order_product oop
                INNER JOIN
                    oc_ms_seller oms ON oms.seller_id = oop.seller_id
                INNER JOIN
                    oc_suborder osub ON osub.order_id = oop.order_id
                WHERE
                    osub.suborder_id = oop.suborder_id
                    AND DATE(osub.date_added) >= DATE(DATE_SUB(NOW(), INTERVAL $days DAY))
                    AND osub.order_status_id > 1
                GROUP BY
                oms.seller_id ";

        $query = $this->db->query($sql);

        if( $query->num_rows ){

            $file_name = DIR_DLOAD . 'Seller_Cancellation_Rate_'.date("d-m-Y").'.csv';

            $fp = fopen($file_name,'w');
            $data = array(
                            'Seller Name',
                            'No of Skus ordered',
                            'No of Skus Cancelled',
                            'Sku Cancellation Rate',
                            'No of Orders Received',
                            'No of orders edited',
                            'Order Cancellation Rate',
                        );
            fputcsv($fp, $data);

            foreach ($query->rows as $key => $records) {

                $data = array(
                                $records['seller_name'],
                                $records['no_of_sku_ordered'],
                                $records['no_of_sku_cancelled'],
                                round( (($records['no_of_sku_cancelled'] / $records['no_of_sku_ordered']) * 100), 2 ),
                                $records['no_of_order_received'],
                                $records['no_of_order_edited'],
                                round( (($records['no_of_order_edited'] / $records['no_of_order_received']) * 100), 2)
                            );

                fputcsv($fp, $data);
            }

            fclose($fp);
            $body = "Hi All, <br/><br/> PFA, Seller Cancellation report in csv attachment";

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
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);

            $mail->Subject = "Seller Cancellation Rate for $days Day(s) - " . date('d/M/Y H:i:s', time());
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);

            if ( $mail->send(1,false) )
            {
                echo "success";
            } else {
                echo "error";
            }
            unlink($file_name);
            exit;
        }
    }

    /**
    * Kusum Joshi
    * Cron to send email with csv file contains list of searched terms with no results
    *
    **/
    public function emailSearchedTermswithNoResults(){

        $sql = "SELECT keyword,count,DATE_FORMAT(created,'%d/%m/%Y') as created,DATE_FORMAT(modified,'%d/%m/%Y') as modified FROM oc_searched_terms WHERE has_results = 0 AND DATE(modified)='" .date('Y-m-d')."'";
        $query = $this->db->query($sql);

        if( $query->num_rows ){

            $file_name = DIR_DLOAD . 'Searched_Terms_NoResults_'.date("d-m-Y").'.csv';

            $fp = fopen($file_name,'w');
            $data = array(
                            'Keyword',
                            'Count',
                            'Created',
                            'Modified'
                        );
            fputcsv($fp, $data);

            foreach ($query->rows as $key => $records) {

                $data = array(
                                $records['keyword'],
                                $records['count'],
                                $records['created'],
                                $records['modified']
                            );

                fputcsv($fp, $data);
            }

            fclose($fp);
            $body = "Hi All, <br/><br/> PFA, Searched Terms with Blank Results in csv attachment";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            // $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
            // $mail->setFrom(EMAIL_IDS['sellers']['email_id']);
            $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
            $mail->addAddress(EMAIL_IDS['sellers']['email_id']);
            $mail->addCC(EMAIL_IDS['rakesh']['email_id'],EMAIL_IDS['rakesh']['name']);
            $mail->addCC(EMAIL_IDS['prabhav']['email_id'],EMAIL_IDS['prabhav']['name']);

            $mail->Subject = "Searched Terms with Blank Results - " . date('d/M/Y H:i:s', time());
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);

            if ( $mail->send(1,false) )
            {
                echo "success";
            } else {
                echo "error";
            }
            unlink($file_name);
        }
            exit;
    }

    /**
    * Kusum Joshi
    *  Send mail to customer to complete Credit Application form.
    **/
    public function notifyCustomertoCompleteCreditApplication(){

         $sql = "SELECT ca.email,ca.first_name,ca.middle_name,ca.last_name,ca.phone_no, ca.customer_id,c.customer_access_token as token FROM oc_credit_application ca LEFT JOIN oc_customer c ON (ca.customer_id = c.customer_id) WHERE draft != 4 ";
        $result = $this->db->query($sql);

        if ( $result->num_rows )
        {
            foreach ($result->rows as $key => $records) {
                print_r($records);

            $name = $records['first_name'];
            if(!empty($records['middle_name'])){
                $name .= ' ' . $records['middle_name'];
            }
            $name .= ' ' . $records['last_name'];

            $phone_no = $records['phone_no'];

            $link = "<a target='_blank' href='".HTTPS_SERVER."index.php?route=account/credit_application&customer_id=".$records['customer_id']."&token=". $records['token']. "'>Credit Application Link</a>";

            $body = "Dear " . $name . ", <br/> You have not completed your Credit Application Form. To complete, Please click the link below. <br/><br/>" . $link;

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');


            $mail->addAddress($records['email'],$name);
            $mail->addCC(EMAIL_IDS['credit']['email_id'],EMAIL_IDS['credit']['name']);
            $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

            $mail->Subject = "Incomplete Credit request from " . $phone_no;
            $mail->msgHTML($body);

            if ( $mail->send(1,false) )
            {
                echo "success";
            } else {
                echo "error";
            }
            }
        }

        die;

    }

    /**
     * Function informInProcessGujaratOrders is used for send notification about gujrat products
     * @author Nilesh, 2018
     */
    public function informInProcessGujaratOrders() {
        $sql = "SELECT oop.suborder_id,
                       os.date_added AS suborder_date,
                       MIN(ooh.date_added) AS processing_date,
                       oos.name as order_status,
                       oo.payment_method,
                       oo.payment_code,

                       CASE
                         WHEN SUM(IF(oms.nickname LIKE 'A%ST', 1, 0)) > 0 AND SUM(IF(oms.nickname NOT LIKE 'A%ST', 1, 0)) > 0
                              THEN 'Ahmedabad + Surat'
                         WHEN SUM(IF(oms.nickname LIKE 'A%ST', 1, 0)) > 0
                              THEN 'Ahmedabad Only'
                         ELSE 'Surat Only'
                       END AS Seller_cities
                 FROM oc_order_product oop INNER JOIN
                      oc_ms_seller oms ON oop.seller_id = oms.seller_id INNER JOIN
                      oc_suborder os ON oop.order_id = os.order_id INNER JOIN
                      oc_order oo ON oop.order_id = oo.order_id INNER JOIN
                      oc_order_status oos ON os.order_status_id = oos.order_status_id AND oo.language_id = 1 INNER JOIN
                      oc_order_history ooh ON ooh.order_id = oop.order_id AND ooh.order_status_id IN (9,16)
                 WHERE os.suborder_id = oop.suborder_id AND
                       ooh.suborder_id = oop.suborder_id AND
                       os.order_status_id IN (9,16) AND
                       oms.nickname LIKE '%\_ST' AND
                       oop.edit_type NOT IN ('CANCELLED_BY_CUSTOMER', 'SELLER_NOT_SUPPLIED')
                 GROUP BY oop.suborder_id";
        $query = $this->db->query($sql);

        if( $query->num_rows ){

            $file_name = DIR_DLOAD . 'Gujrat_Orders_Info_'.date("d-m-Y").'.csv';

            $fp = fopen($file_name,'w');
            $data = array(
                            'Suborder Id',
                            'Suborder Date',
                            'Processing Date',
                            'Order Status',
                            'Payment Method',
                            'Payment Code',
                            'Seller Cities',
                        );
            fputcsv($fp, $data);

            foreach ($query->rows as $key => $records) {

                $data = array(
                                $records['suborder_id'],
                                $records['suborder_date'],
                                $records['processing_date'],
                                $records['order_status'],
                                $records['payment_method'],
                                $records['payment_code'],
                                $records['Seller_cities']
                            );

                fputcsv($fp, $data);
            }

            $body = "Please find attached the details for Gujarat Orders in Processed status ";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            $mail->Subject = "In Processed Status orders of Gujarat - " .date('d/M/Y h:i:s');
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);

            if ( $mail->send(1,false) )
            {
                echo "success";
            } else {
                echo "error";
            }
            unlink($file_name);
            exit;
        }


    }

    /*
      @Method: updateCitrusTransactions
      * this method will get the citrus transactions between two dates if speciefied in url,
        otherwise get the all yesterday transactions.
        then we will check for successfull transactions entry in order payment, if entry does not exist, then we will create one.
      @GET PARAMS(optional): startDate=20180824, endDate=20180915
      @author: Devendra, October 2018
    */
    public function updateCitrusTransactions() {
      if (isset($this->request->get['startDate']) && isset($this->request->get['endDate'])) {
        $filter = array(
          'txnStartDate' => date('Ymd',strtotime($this->request->get['startDate'])),
          'txnEndDate' => date('Ymd',strtotime($this->request->get['endDate']))
        );
      } else {
        $current_date = date('Ymd');
        $filter = array(
          'txnStartDate' => $current_date,
          'txnEndDate' => $current_date
        );
      }

      $payment_gateway = new Citrus($this);
      $citrus_transactions = $payment_gateway->getTransactionsByDate($filter);

      if (!empty($citrus_transactions['errors'])) {
        $this->sendMailOnCitrusCronFail(json_encode($citrus_transactions['errors']));
      }

     if (empty($citrus_transactions['transactions'])) {
       echo "No transactions found!";
       exit();
     }

     // log the new transactions into csv file
     $file_name = DIR_SYSTEM.'logs/citrus_trxns_updated_via_cron.csv';
     $add_csv_title = true;
     if (file_exists($file_name)) {
       $add_csv_title = false;
     }

     $fp = fopen($file_name, 'a');
     if ($add_csv_title) {
       $data = array('Order No','Transaction Id','Transaction Date Time', 'Amount');
       fputcsv($fp, $data);
     }

      $count = 0;
      foreach ($citrus_transactions['transactions'] as $transaction) {
        // if not a successfull purchase transaction, then continue
        if ($transaction['respCode'] != '0' || $transaction['txnType'] != 'SALE') continue;
        $sql = "SELECT
                  order_id, successfull, order_no
                FROM
                  ".DB_PREFIX."order_payment
                WHERE
                  merchant_txn_id = '" . $this->db->escape($transaction['merchantTxnId']) . "' AND
                  payment_gateway = 'citrus'
                ORDER BY successfull DESC
               ";
        $query = $this->db->query($sql);
        $order_id = 0; // Initialize order_id
        $order_no = $transaction['merchantTxnId'];
        // if citrus payment entry already exists && first row is successfull, the continue
        if ($query->num_rows) {
            if ((int)$query->row['successfull'] === 1) {
                continue;
            }

            // If not successfull, get the order_id
            $order_id = (int)$query->row['order_id'];
            $order_no = $query->row['order_no'];
        }

        // if order_id still not known
        if ( empty($order_id) ) {
            $sql = "SELECT
                      order_id
                    FROM
                      ".DB_PREFIX."order
                    WHERE
                      order_no = '" . $this->db->escape($transaction['merchantTxnId']) . "'
                   ";
            $query = $this->db->query($sql);
            if (!$query->num_rows) continue;
            $order_id = (int)$query->row['order_id'];
        }

        $amount_arr = explode(' ', $transaction['amount']); // ex of transaction amount = "10678.67 INR"
        $amount = $this->currency->convertLiveRates($amount_arr[0], $amount_arr[1], 'INR');

        $payable_amt = 0;
        $paid_amt = $amount;

        //Define data array
        $data = array();
        $data['order_id']           = (int)$order_id;
        $data['merchant_txn_id']    = $transaction['merchantTxnId'];
        //$data['trxn_id']            = 0;
        $data['order_no']           = $order_no;
        $data['txn_status']         = 'SUCCESS';
        $data['payment_mode']       = $transaction['paymentMode'];
        $data['amount']             = (float)$paid_amt;
        $data['txn_date_time']      = $transaction['txnDateTime'];
        $data['date_added']         = 'NOW()';
        $data['payment_gateway']    = 'citrus';
        $data['successfull']        = '1';
        $data['reference']          = '';
        $data['payment_link']       = 'Customer Direct Payment';
        $data['json_format']        = serialize($transaction);
        $data['user_id']            = '0';


        OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
        $count = $count + 1;
        $data = array($data['order_no'], $data['trxn_id'], $data['txn_date_time'], $data['amount']);
        fputcsv($fp, $data);
      }
      fclose($fp);
      echo "Transactions successfully updated in database: " . $count;
      exit();
    }

    private function sendMailOnCitrusCronFail($citrus_response) {
      $body = "Received following response from citrus api:<br/><br/>" . $citrus_response;

      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = $this->config->get('config_mail_smtp_hostname');
      $mail->Port = $this->config->get('config_mail_smtp_port');
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = $this->config->get('config_mail_smtp_username');
      $mail->Password = $this->config->get('config_mail_smtp_password');

      $mail->addAddress(EMAIL_IDS['madhur']['email_id'],EMAIL_IDS['madhur']['name']);

      $mail->Subject = "Alert! Cron to update citrus transactions has been failed.";
      $mail->msgHTML($body);
      $mail->send(1,false);
    }

    /**
     * @info: Public method to send alert SMS to sellers to keep ready product for
                order statuses "Orders requested for pickup"
     * @return: void
     * @author: Nishu, Dec 2018
    */
    public function sendAlertSmsToSellerForPickupRequestedOrders() : void{
        $sql = "
                SELECT
                    oop.seller_id,
                    COALESCE(COUNT(DISTINCT o.order_id), 0) total_orders,
                    SUM(IF(TRIM(oop.pickup_status) != 'Received'
                            OR oop.seller_invoice_id = 0
                            OR oop.seller_invoice_id IS NULL,
                        1,
                        0)) AS not_picked_completely
                FROM
                    oc_order o
                        INNER JOIN
                    oc_suborder osub ON (osub.order_id = o.order_id)
                        INNER JOIN
                    oc_order_product oop ON (oop.order_id = o.order_id)
                WHERE
                    oop.suborder_id = osub.suborder_id
                        AND osub.order_status_id IN (9 , 16)
                        AND o.store_id IN (0 , 2, 9)
                        AND oop.sor_product = '0'
                        AND o.stock_transfer = 0
                        AND oop.edit_type NOT IN ('CANCELLED_BY_CUSTOMER')
                GROUP BY oop.seller_id
                HAVING not_picked_completely > 0
                    AND total_orders > 0
                Order BY
                    oop.seller_id DESC
               ";

        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
            $orderCountBySellerIds = array_combine(
                                         array_column($data, 'seller_id'),
                                         array_column($data, 'total_orders')
                                      );
            $seller_ids = array_column($data, 'seller_id');
            //Get Seller Details for sending SMS
            $sql = "
                    SELECT
                       ms.seller_id,
                       c.telephone,
                       CASE
                          WHEN ms.pickup_city_code = 'JP' THEN '9351508816'
                          WHEN ms.pickup_city_code = 'DL' THEN '9971748657'
                          WHEN ms.pickup_city_code = 'ST' THEN '9898256409'
                          ELSE ''
                       END AS helpline_num
                    FROM
                       oc_ms_seller AS ms
                    INNER JOIN
                       oc_customer AS c ON c.customer_id = ms.seller_id
                    WHERE
                       ms.seller_id IN (". implode(',', $seller_ids) .")
                   ";

            $seller_details_qry = $this->db->query($sql);
            if($seller_details_qry->num_rows > 0){
               $seller_details = $seller_details_qry->rows;
               foreach ($seller_details as $value) {

                    $seller_id = $value['seller_id'];
                    $telephone = $value['telephone'];
                    $message   = "You have ". $orderCountBySellerIds[$seller_id] ." no of orders today from Wholesalebox. Please keep ready by 12 pm. Contact ". $value['helpline_num'] ." for any query.";

                    //Initiaizing SMS object and send sms
                    $send_sms = new SMS($message, $telephone);
                    $send_sms->sendMessage();

               }
            }
        }
    }

    /*
      @Method: getListOfSellersNotGetting10OrdersInLastMonth
      * this method get the list of sellers not getting 10 orders process in last month
      @author: MSA, DEC 2018
    */
    public function getListOfSellersNotGetting10OrdersInLastMonth()
    {
        $sql = "
                SELECT
                    oms.seller_id,
                    oms.nickname,
                    oms.company,
                    COUNT(DISTINCT o.order_id) as total_orders_placed,
                    COUNT(DISTINCT CASE WHEN op.edit_type <> 'CANCELLED_BY_CUSTOMER' AND sub.order_status_id NOT IN (1,2) THEN op.order_id END) AS total_orders_processed,
                     COUNT(DISTINCT CASE WHEN op.edit_type = 'SELLER_NOT_SUPPLIED' THEN op.order_id END) AS total_orders_not_fulfilled_completely,
                     COUNT(DISTINCT CASE WHEN op.seller_invoice_id > 0 THEN op.order_id END) AS total_fulfilled_orders
                    FROM
                     oc_ms_seller AS oms
                    INNER JOIN
                     oc_order_product AS op
                      ON op.seller_id = oms.seller_id
                    INNER JOIN
                     oc_suborder AS sub
                      ON sub.suborder_id = op.suborder_id AND
                         sub.order_status_id > 0
                    INNER JOIN
                     oc_order AS o
                      ON o.order_id = sub.order_id AND
                         o.date_added >= (LAST_DAY(CURDATE() - INTERVAL 2 MONTH) + INTERVAL 1 DAY) AND
                         o.date_added < (LAST_DAY(CURDATE()) - INTERVAL 1 MONTH + INTERVAL 1 DAY)

                    WHERE
                     oms.seller_status = 1

                    GROUP BY
                     op.seller_id

                    HAVING
                     total_orders_processed < 10
                ";

        $query = $this->db->query($sql);

        if( $query->num_rows ){

            $file_name = DIR_DLOAD . 'Seller-Not-Getting-10-Orders-'.date("d-m-Y").'.csv';

            $fp = fopen($file_name,'w');
            $data = array(
                            'Seller Id',
                            'Seller Nickname',
                            'Company',
                            'Total Order Placed',
                            'Total Order Processed',
                            'Total Order Not Fulfilled Completely',
                            'Total Fulfilled Orders',
                        );
            fputcsv($fp, $data);

            foreach ($query->rows as $key => $records) {

                $data = array(
                                $records['seller_id'],
                                $records['nickname'],
                                $records['company'],
                                $records['total_orders_placed'],
                                $records['total_orders_processed'],
                                $records['total_orders_not_fulfilled_completely'],
                                $records['total_fulfilled_orders']
                            );

                fputcsv($fp, $data);
            }

            $body = "Please find attached the details for Sellers not getting 10 Orders Processed in last month.";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);

            $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            //$mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
            //$mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            //$mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            //$mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);

            $mail->Subject = 'Sellers not getting 10 orders-'.date('d/M/Y h:i:s');
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);

            if ( $mail->send(1,false) )
            {
                echo "success";
            } else {
                echo "error";
            }
            unlink($file_name);
            exit;

        }
    }

    /*
      @Method: getSeller10thDeliveredOrderIn5DaysBack
      * this method get return and sales data of sellers 10th delivered in 5 day before
      @author: MSA, DEC 2018
    */

     public function getSeller10thDeliveredOrderIn5DaysBack()
    {

        $sql = "
            SELECT dt2.seller_id,
                   dt2.nickname,
                   dt2.company,
                   GROUP_CONCAT(dt2.order_id ORDER BY dt2.delivery_date) AS order_ids,
                   MAX(CASE WHEN dt2.row_no = 10 AND DATE(dt2.delivery_date) = CURDATE() - INTERVAL 6 DAY
                            THEN 1
                       END) AS tenth_dlvd_5daysback
            FROM
            (
            SELECT
              @rn := CASE WHEN @si = dt.seller_id THEN @rn + 1
                          WHEN @si := dt.seller_id THEN 1
                     END AS row_no,
              dt.*

            FROM
            (
            SELECT
              oms.seller_id,
              oms.nickname,
              oms.company,
              oop.order_id,
              MIN(ooh.date_added) AS delivery_date
            FROM oc_ms_seller oms
            JOIN oc_order_product oop
              ON oop.seller_id = oms.seller_id AND
                 oop.buyer_invoice_id > 0
            JOIN oc_suborder osub
              ON osub.buyer_invoice_id = oop.buyer_invoice_id AND
                 osub.order_status_id IN (5,15)
            JOIN oc_order_history ooh
              ON ooh.order_id = oop.order_id AND
                 ooh.order_status_id IN (5,15)
            WHERE oms.seller_status = 1
              AND oms.seller_invoice_generate = 1
              AND oms.seller_id <> 78355
              AND ooh.suborder_id = osub.suborder_id
            GROUP BY oms.seller_id, oop.order_id
            ORDER BY oms.seller_id, delivery_date
            ) AS dt
            CROSS JOIN (SELECT @rn := 0, @si := 0, @oids := '') AS user_init_vars
            ) AS dt2
            GROUP BY dt2.seller_id
            HAVING tenth_dlvd_5daysback
        ";
        $query = $this->db->query($sql);
        $result = $query->rows;
        $report_data = array();
        if(!empty($result)) {

            foreach ($result as $key => $value) {

                $seller_id       = $value['seller_id'];
                $seller_nickname = $value['nickname'];
                $seller_company  = $value['company'];
                $order_ids = explode(',',$value['order_ids']);
                $order_ids = array_slice($order_ids, 0,10);
                $order_ids = implode(',', $order_ids);
                $suq_query = "
                            SELECT
                                oop.order_id,

                                SUM(oop.quantity * oop.piece_in_set) as sale_pieces,

                                SUM(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS purchase_pieces,

                                SUM(CASE WHEN oop.edit_type = 'seller_not_supplied' THEN (oop.piece_in_set * oop.quantity) ELSE 0 END) AS short_supply_pieces,

                                SUM(CASE WHEN oop.edit_type = 'seller_not_supplied' THEN (oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) ELSE 0 END) AS short_supply_amount,

                                COALESCE(SUM(
                                               (
                                                SELECT
                                                    SUM(quantity)
                                                FROM
                                                    oc_return
                                                WHERE
                                                    order_product_id = oop.order_product_id
                                                    AND active_row = 1
                                                    AND return_action_id NOT IN (104,107,108)
                                                )
                                            ),
                                            0
                                        ) AS return_qty,

                                COALESCE(SUM(
                                                (
                                                SELECT
                                                    SUM(quantity * oop.transfer_price_per_piece)
                                                FROM
                                                    oc_return
                                                WHERE
                                                    order_product_id = oop.order_product_id
                                                    AND active_row = 1
                                                    AND return_action_id NOT IN (104,107,108)
                                                )
                                            ),
                                        0) AS return_amount
                            FROM
                                oc_order_product AS oop
                            WHERE
                                oop.order_id IN (".$order_ids.")
                            GROUP BY
                                oop.order_id
                            ";
                $sub_query_result = $this->db->query($suq_query)->rows;

                if(!empty( $sub_query_result )) {

                    $seller_orders = array();

                    foreach ($sub_query_result as $key1 => $value1) {

                        $seller_orders[] = $value1;

                    }
                    $report_data[] = array(
                                'seller_id'         => $seller_id,
                                'seller_nickname'   => $seller_nickname,
                                'seller_company'    => $seller_company,
                                'seller_orders'     => $seller_orders
                            );
                }
            }

           // generating csv file with final data
           if(!empty($report_data)) {

                $file_name = DIR_DLOAD . 'Seller-First-10-Orders-Delivered-'.date("d-m-Y").'.csv';

                    $fp = fopen($file_name,'w');
                    $data = array(
                                    'Seller Id',
                                    'Seller Nickname',
                                    'Company',
                                    'Order Id',
                                    'Total Sale Pieces',
                                    'Total Purchase Pieces',
                                    'Total Short Supply Pieces',
                                    'Total Short Supply Amount',
                                    'Total Return Items',
                                    'Total Return Items Amount',
                                );
                    //add heading line
                    fputcsv($fp, $data);

                foreach ($report_data as $seller_key => $seller_orders) {

                        $data = array(
                                $seller_orders['seller_id'],
                                $seller_orders['seller_nickname'],
                                $seller_orders['seller_company'],
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                            );
                        //add seller heading line
                        fputcsv($fp, $data);
                        foreach ($seller_orders['seller_orders'] as $key => $value) {
                            $data = array(
                                '',
                                '',
                                '',
                                $value['order_id'],
                                $value['sale_pieces'],
                                $value['purchase_pieces'],
                                $value['short_supply_pieces'],
                                $value['short_supply_amount'],
                                $value['return_qty'],
                                $value['return_amount']
                            );
                            //add seller orders data
                            fputcsv($fp, $data);
                        }
                    }

                    // email sending
                    $body = "Please find attached the details of sellers return and sales data of first 10 orders delivered.";

                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');

                    $mail->setFrom(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
                    $mail->addReplyTo(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);

                    $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
                    //$mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
                    //$mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
                    //$mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                    //$mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);

                    $mail->Subject = 'Sellers return and sales data of first 10 orders dellivered '.date('d/M/Y h:i:s');
                    $mail->AddAttachment($file_name);
                    $mail->msgHTML($body);

                    if ( $mail->send(1,false) )
                    {
                        echo "success";
                    } else {
                        echo "error";
                    }
                    unlink($file_name);
                    exit;

                }
            }
        }

        /*
          @Method: customerNotOrderInLast15Days
          * Method to get all customers not order in last 15 days
          @author: MSA, DEC 2018
        */
        public function alertMembersNotOrderingForLong()
        {
            $days = $_GET['days'] ?? '14';

            $sql = "
                    SELECT
                        result.master_id,
                        ord.payment_company as shop_name,
                        TRIM(CONCAT(ord.firstname,' ', ord.lastname)) as owner_name,
                        ord.payment_city as city,
                        ocs.name as agent_name,
                        ord.order_no as last_order_no,
                        ord.date_added as last_order_date,
                        ord.total as last_order_amount
                    FROM
                    (
                        SELECT
                          c.master_id,
                          MAX(o.date_added) AS last_order_date,
                          MAX(o.order_id) AS last_order_id
                        FROM
                          oc_order o
                        JOIN oc_suborder osub
                          ON osub.order_id = o.order_id AND
                             osub.order_status_id > 0 AND
                             osub.order_status_id <> 2
                        JOIN oc_customer c
                          ON c.customer_id = o.customer_id
                        JOIN oc_master_customer_membership mcm
                          ON mcm.master_id = c.master_id AND
                             mcm.status = 1
                        WHERE o.store_id IN (0,2,9) AND
                              o.franchise_id = 0 AND
                              o.stock_transfer = 0
                        GROUP BY c.master_id
                        HAVING last_order_date < CURDATE() - INTERVAL ".(int)$days." DAY

                    ) AS result

                    INNER JOIN
                        oc_order AS ord ON ord.order_id = result.last_order_id
                    LEFT JOIN
                        oc_order_sales_staff AS ocss ON ocss.order_id = result.last_order_id
                    LEFT JOIN
                        oc_sales_staff AS ocs ON ocs.staff_id = ocss.sales_staff_id

                    ";

                $result = $this->db->query($sql);

                if( $result->num_rows ){

                $_html = '<table width="100%" border="1" style="border-collapse:collapse;">';

                //manage multiple staff data
                    $result_data = array();
                    foreach ($result->rows as $key => $records) {
                        $result_data[$records['master_id']] = $records;
                    }

                    $_html .= '<tr>';
                        $_html .= '<th align="left">Master ID</th>';
                        $_html .= '<th align="left">Shop Name</th>';
                        $_html .= '<th align="left">Owner Name</th>';
                        $_html .= '<th align="left">City</th>';
                        $_html .= '<th align="left">Agent Name Assigned</th>';
                        $_html .= '<th align="left">Last Order No</th>';
                        $_html .= '<th align="left">Last Order Date</th>';
                        $_html .= '<th align="left">Last Order Amount</th>';
                     $_html .= '</tr>';

                    foreach ($result_data as $key => $value) {

                        if(!empty($value['master_id'])) {
                            $_html .= '<tr>';
                                $_html .= '<td align="left">'.$value['master_id'].'</td>';
                                $_html .= '<td align="left">'.$value['shop_name'].'</td>';
                                $_html .= '<td align="left">'.$value['owner_name'].'</td>';
                                $_html .= '<td align="left">'.$value['city'].'</td>';
                                $_html .= '<td align="left">'.$value['agent_name'].'</td>';
                                $_html .= '<td align="left">'.$value['last_order_no'].'</td>';
                                $_html .= '<td align="left">'.$value['last_order_date'].'</td>';
                                $_html .= '<td align="left">'.$value['last_order_amount'].'</td>';
                             $_html .= '</tr>';
                        }
                    }

                    $_html .= '</table>';

                    $body = "<p><b>Please find the list of member customers which have not placed order for more than 15 days.</b></p>";

                    $body .= $_html;

                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = $this->config->get('config_mail_smtp_hostname');
                    $mail->Port = $this->config->get('config_mail_smtp_port');
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPAuth = true;
                    $mail->Username = $this->config->get('config_mail_smtp_username');
                    $mail->Password = $this->config->get('config_mail_smtp_password');

                    $mail->setFrom(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
                    $mail->addReplyTo(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);

                    $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                    $mail->addAddress(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);

                    $mail->Subject = 'Members Not Ordering for More than 15 Days -'.date('d/M/Y h:i:s');
                    $mail->msgHTML($body);

                    if ( $mail->send(1,false) )
                    {
                        echo "success";
                    } else {
                        echo "error";
                    }
                    exit;

                }
        }

        /*
          @Method: findCategoriesWithLowProducts
          * this method will find out categories whose total product count is less than or equal to 5.
          then mail these categories list.
          @author: Devendra, December 2018
        */
        public function findCategoriesWithLowProducts() {
          $sql = "SELECT
                    pc.category_id,
                    cd.name,
                    COUNT(pc.product_id) AS total_products,
                    c.status AS category_status
                  FROM
                    `oc_product_to_category` pc
                  JOIN
                    `oc_category` c ON c.category_id = pc.category_id
                  JOIN
                    `oc_category_description` cd ON c.category_id = cd.category_id AND cd.language_id = 1
                  JOIN
                    `oc_product` p ON p.product_id = pc.product_id
                      AND franchise_id = 0
                      AND is_associate = 0 AND p.status = 1 AND quantity > 0 AND stock_status_id <> 5 AND price > 0 AND selling_price > 0 AND is_archived = 0 AND hsn_code IS NOT NULL AND exclusive IN('normal','both')
                  JOIN
                    `oc_product_to_store` ps ON p.product_id = ps.product_id AND ps.store_id = 0
                  JOIN
                    `oc_ms_product` mp ON mp.product_id = p.product_id
                  JOIN
                    `oc_ms_seller` ms ON ms.seller_id = mp.seller_id AND ms.seller_status=1 AND ms.vacation_mode=1
                  GROUP BY
                    pc.category_id
                  HAVING
                    total_products < 6";

          $result = $this->db->query($sql);

          if ($result->num_rows) {
            $_html = '<table width="60%" border="1" style="border-collapse:collapse;">';
            $_html .= '<tr>';
            $_html .= '<th align="left">Category ID</th>';
            $_html .= '<th align="left">Category Name</th>';
            $_html .= '<th align="left">Category Status</th>';
            $_html .= '<th align="left">Total Products</th>';
            $_html .= '</tr>';

            foreach ($result->rows as $key => $records) {
              $_html .= '<tr>';
              $_html .= '<td align="left">'.$records['category_id'].'</td>';
              $_html .= '<td align="left">'.$records['name'].'</td>';
              if ($records['category_status'] == 1) {
                $_html .= '<td align="left" style="color:green;">Enabled</td>';
              } else {
                $_html .= '<td align="left" style="color:red;">Disabled</td>';
              }

              $_html .= '<td align="left">'.$records['total_products'].'</td>';
              $_html .= '</tr>';
            }

            $_html .= '</table>';

            $body = "<p><b>Please find the list of categories whose total product count is less than or equal to 5.</b></p>";
            $body .= $_html;

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');

            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

            $mail->Subject = 'Categories having total products less than or equal to 5.';
            $mail->msgHTML($body);

            if ( $mail->send(1,false) )
            {
                echo "success";
            } else {
                echo "error";
            }
            exit;
          }

        }

    /**
     * gets customer eligible for free shipping
     * - have GCM id stamped with us
     * - have orders
     * @return array customer_ids
     * @author Anurag Jain, 6 Feb 2019
     */
    public function getFreeShippingEligibleCustomers( array $generate_coupon_data ): array
    {
        $eligible_customers_array = array();

        $free_shipping_eligible_customers_sql = "SELECT
                                                    DISTINCT c.customer_id
                                                FROM ". DB_PREFIX ."customer c ";

        $free_shipping_sql_where = array();

        // coupons only for ordering customers
        if ( !empty( $generate_coupon_data['is_ordered'] )) {

            $free_shipping_eligible_customers_sql .= "INNER JOIN ". DB_PREFIX ."order o
                                                        ON o.customer_id = c.customer_id
                                                     ";
            //$free_shipping_sql_where[] = "o.order_id > '0'";
        }

        // coupons for customers with app (gcm id)
        if ( !empty( $generate_coupon_data['gcm_id'] )) {

            $free_shipping_sql_where[] = "c.ws_gcm_registration_id IS NOT NULL";
            $free_shipping_sql_where[] = "c.ws_gcm_registration_id <> ''";
        }

        if ( !empty( $free_shipping_sql_where )) {
            $free_shipping_eligible_customers_sql .= " WHERE " . implode( ' AND ', $free_shipping_sql_where );
        }

        $free_shipping_eligible_customers_sql .= " AND c.customer_id NOT IN ('16358','4042') GROUP BY c.ws_gcm_registration_id";

        $eligible_customers_result = $this->db->query( $free_shipping_eligible_customers_sql );

        if ( $eligible_customers_result->num_rows ) {

            $eligible_customers_array = array_column( $eligible_customers_result->rows, 'customer_id' );

            // to randomly select customers every week
            shuffle( $eligible_customers_array );
        }

        return $eligible_customers_array;
    }

    /**
     * get coupon dictionary words from DB
     * @param  int    $coupon_count number of words needed from DB
     * @return array coupon codes
     * @author Anurag Jain, 6 Feb 2019
     */
    public function getFreeShippingCouponWords( int $coupon_count ): array
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
     * clean up last week or any previous free shipping codes
     * @return bool
     * @author Anurag Jain, 6 Feb 2019
     */
    public function removeOldFreeShippingCoupons(): bool
    {
        $remove_result = true;

        $old_coupons_sql = "SELECT
                                cc.coupon_id AS coupon_id
                            FROM ". DB_PREFIX ."coupon_customer cc
                            INNER JOIN ". DB_PREFIX ."coupon c ON c.coupon_id = cc.coupon_id
                            WHERE
                                c.name = 'Free Shipping Coupon'";

        $old_coupons_result = $this->db->query( $old_coupons_sql );

        if ( $old_coupons_result->num_rows ) {

            $old_coupons_ids = implode( ',', array_column( $old_coupons_result->rows, 'coupon_id' ));

            if ( !empty( $old_coupons_ids )) {

                $remove_coupons_sql = "DELETE FROM ". DB_PREFIX ."coupon WHERE coupon_id IN (". $old_coupons_ids .") ";
                $remove_result = $this->db->query( $remove_coupons_sql );

                $remove_coupon_customers_sql = "DELETE FROM ". DB_PREFIX ."coupon_customer WHERE coupon_id IN (". $old_coupons_ids .") ";
                $remove_result = $this->db->query( $remove_coupon_customers_sql );
            }
        }

        return $remove_result;
    }

    /**
     * inserts free shipping coupons in oc_coupon and oc_coupon_customer tables
     * @param  array  $coupon_customer_array
     * @return bool
     * @author Anurag Jain, 6 Feb 2019
     */
    public function insertFreeShippingCouponsInDB( array $coupon_customer_array, string $allowed_from ): bool
    {
        if ( empty( $coupon_customer_array )) {
            return false;
        }

        $total_days = 7;

        // daily dummy coupon customers
        $dummy_coupon_customers = array('16358' => 'DUMMY_SHIP', '4042' => "FAKE_SHIP" );

        // distribute all customers in 7 arrays for 7 days
        list( $customers_day_1,
              $customers_day_2,
              $customers_day_3,
              $customers_day_4,
              $customers_day_5,
              $customers_day_6,
              $customers_day_7 ) = array_chunk( $coupon_customer_array, ceil( count( $coupon_customer_array ) / $total_days ), true );

        $daywise_coupon_customer_array = array(
                                                array_replace( $customers_day_1, $dummy_coupon_customers ),
                                                array_replace( $customers_day_2, $dummy_coupon_customers ),
                                                array_replace( $customers_day_3, $dummy_coupon_customers ),
                                                array_replace( $customers_day_4, $dummy_coupon_customers ),
                                                array_replace( $customers_day_5, $dummy_coupon_customers ),
                                                array_replace( $customers_day_6, $dummy_coupon_customers ),
                                                array_replace( $customers_day_7, $dummy_coupon_customers )
                                            );

        $daywise_coupon_customer_array = array_filter( $daywise_coupon_customer_array );

        // first clean last week / old free shipping coupons
        $remove_result = $this->removeOldFreeShippingCoupons();

        if ( $remove_result ) {

            $insert_coupon_values_sql_array = array();
            $last_insert_id = 0;

            foreach ( $daywise_coupon_customer_array as $day_key => $current_day_customers ) {

                foreach ( $current_day_customers as $customer_id => $coupon_code ) {
                    /**
                     * prepare and insert data in oc_coupon table
                     */
                    $insert_coupon_sql = "INSERT INTO ". DB_PREFIX ."coupon
                                                (
                                                    name,
                                                    code,
                                                    type,
                                                    discount,
                                                    logged,
                                                    shipping,
                                                    total,
                                                    date_start,
                                                    date_end,
                                                    uses_total,
                                                    uses_customer,
                                                    status,
                                                    date_added,
                                                    store_id,
                                                    allowed_from
                                                )
                                           VALUES
                                                (
                                                    'Free Shipping Coupon',
                                                    '". $this->db->escape( $coupon_code ) ."',
                                                    'F',
                                                    '0.0000',
                                                    '1',
                                                    '1',
                                                    '0.0000',
                                                    CURRENT_DATE() + INTERVAL ". $day_key ." DAY,
                                                    CURRENT_DATE() + INTERVAL ". $day_key ." DAY,
                                                    '1',
                                                    '1',
                                                    '1',
                                                    NOW(),
                                                    '0',
                                                    '". $allowed_from ."'
                                                 )";

                    $insert_coupon_result = $this->db->query( $insert_coupon_sql );

                    if ( empty( $last_insert_id )) {
                        $last_insert_id = (int) $this->db->getLastId();
                    }

                    /**
                     * prepare and insert data in oc_coupon_customer table
                     */
                    if ( !empty( $last_insert_id )) {

                        $insert_coupon_customer_sql = "INSERT INTO ". DB_PREFIX ."coupon_customer
                                                            ( coupon_id, customer_id )
                                                       VALUES ( '". $last_insert_id ."', '". $customer_id ."' )";

                        $coupon_customer_result = $this->db->query( $insert_coupon_customer_sql );

                        $last_insert_id++;
                    }
                }
            }
        }

        return true;
    }

    /**
     * generates free shipping coupons every week for all eligible customers
     * @author Anurag Jain, 6 Feb 2019
     */
    public function generateFreeShippingCoupons()
    {

        //Set apache execution time limit to infinite
        ini_set( 'max_execution_time', 0 );

        $generate_coupon_data = array();
        $generate_coupon_data['is_ordered']   = isset( $_GET['is_ordered'] ) ? $_GET['is_ordered'] : '1';
        $generate_coupon_data['allowed_from'] = isset( $_GET['allowed_from'] ) ? $_GET['allowed_from'] : 'ANDROID_APP';
        $generate_coupon_data['gcm_id']       = isset( $_GET['gcm_id'] ) ? $_GET['gcm_id'] : '1';

        $current_week_customer_count = 0;

        // get eligible customers for free shipping
        $eligible_customers_array = $this->getFreeShippingEligibleCustomers( $generate_coupon_data );

        if ( !empty( $eligible_customers_array )) {

            $current_week_customer_count = count( $eligible_customers_array );

            // get required number of coupon codes from DB
            $coupon_codes_array = $this->getFreeShippingCouponWords( $current_week_customer_count );

            if ( !empty( $coupon_codes_array )) {

                $coupon_codes_count = count( $coupon_codes_array );

                $coupon_customer_array = array_combine( $eligible_customers_array, $coupon_codes_array );

                // insert new coupons to DB
                $this->insertFreeShippingCouponsInDB( $coupon_customer_array , $generate_coupon_data['allowed_from'] );
            }
        }

        echo "Free Shipping Coupons generated successfully !!!";
        exit;
    }

    /**
     * @param  array  $curl_data
     */
    public function executeCurlRequest( array $curl_data )
    {
        $request = $curl_data['request'];

        $api_url = $curl_data['api_url'];

        $data_json = json_encode( $request );


        $ch = curl_init( $api_url );

        curl_setopt( $ch, CURLOPT_HEADER, 0 );

        curl_setopt( $ch,
                     CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                     'Content-Length: ' . strlen( $data_json )));

        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_json );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec( $ch );

        $result = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $result );

        $result = json_decode( $result, true );

        return $result;
    }

    /**
     * prepares push notification data for free shipping
     * @param  array  $customer_to_coupon_code_array
     */
    public function prepareFreeShippingCouponPNData( array $customer_to_coupon_code_array ): array
    {
        if ( empty( $customer_to_coupon_code_array )) {

            return array();
        }

        $message_array = array();
        $message_array['notification_sending_time'] = date('Y-m-d H:i:s');
        $message_array['type'] = "instant";
        $message_array['data'] = array();

        foreach ( $customer_to_coupon_code_array as $customer_id => $coupon_code_data ) {

            $masked_telephone =  str_repeat( "x", strlen( $coupon_code_data['telephone'])-5) . substr( $coupon_code_data['telephone'], -5 );

            $message = "FREE Shipping !!\n"
                        . "Use coupon code ". strtoupper($coupon_code_data['code']) ." during checkout to get free surface shipping to direct locations, exclusively only for your account with mobile no: ". $masked_telephone .".\n"
                        . "Valid only till ". date_format( date_create($coupon_code_data['date_end']), 'd-M-Y') ." Midnight !";

            $pn_array = array();
            $pn_array['msg_type'] = "1";
            $pn_array['message'] = $message;
            $pn_array['title'] = "Free Shipping !!!";
            $pn_array['tickerText'] = "WholesaleBox";
            $pn_array['vibrate'] = "1";
            $pn_array['mobile'] = "";
            $pn_array['sound'] = "1";

            $data_array = array();
            $data_array['type'] = "customer";
            $data_array['id'] = $customer_id;
            $data_array['is_pn_to_send'] = true;
            $data_array['pn'] = $pn_array;
            $data_array['is_sms_to_send'] = false;
            $data_array['is_email_to_send'] = false;
            $data_array['email_to_head'] = false;
            $data_array['pn_to_head'] = false;
            $data_array['sms_to_head'] = false;
            $data_array['pn_to_sales_support'] = false;
            $data_array['web_pn_to_sales_support'] = false;
            $data_array['email_to_sales_support'] = false;
            $data_array['sms_to_sales_support'] = false;

            $message_array['data'][] = $data_array;

        }
        return $message_array;
    }

    /**
     * sends push notification to customers for free shipping coupons
     */
    public function sendDailyPNsForFreeShippingCoupons()
    {
        //Set apache execution time limit to infinite
        ini_set( 'max_execution_time', 0 );

        $current_coupons_sql = "SELECT
                                    c.code,
                                    cc.customer_id,
                                    c.date_end,
                                    cust.firstname,
                                    cust.telephone
                                FROM ". DB_PREFIX ."coupon c
                                INNER JOIN ". DB_PREFIX ."coupon_customer cc
                                    ON cc.coupon_id = c.coupon_id
                                INNER JOIN ". DB_PREFIX ."customer cust
                                    ON cust.customer_id = cc.customer_id
                                WHERE c.name = 'Free Shipping Coupon'
                                AND ((c.date_start = '0000-00-00' OR c.date_start <= CURRENT_DATE())
                                     AND (c.date_end = '0000-00-00' OR c.date_end >= CURRENT_DATE()))";

        $current_coupons_result = $this->db->query( $current_coupons_sql );

        if ( $current_coupons_result->num_rows ) {

            $customer_to_coupon_code_array = array();

            foreach ( $current_coupons_result->rows as $key => $value ) {
                $customer_to_coupon_code_array[ $value['customer_id'] ] = array(
                                                                            'code' => $value['code'],
                                                                            'date_end' => $value['date_end'],
                                                                            'firstname' => $value['firstname'],
                                                                            'telephone' => $value['telephone']
                                                                        );
            }

            $daily_pn_chunks = array_chunk( $customer_to_coupon_code_array, 500, true );

            foreach ( $daily_pn_chunks as $key => $customer_to_coupon_code_array_chunk ) {

                $pn_data = $this->prepareFreeShippingCouponPNData( $customer_to_coupon_code_array_chunk );

                if ( !empty( $pn_data )) {

                    $curl_data = array();
                    $curl_data['api_url'] = CRM_URL . 'crmapi/Notifications/sendNotificationFromWeb';
                    $curl_data['request'] = $pn_data;

                    $this->executeCurlRequest( $curl_data );
                }
            }
        }

        echo "Push Notifications released successfully !!!";
        exit;
    }

    /**
     * Public method to remove(mark sucessful=0) in oc_order_payment for wsb_credit_dummy_cash entry
     *   Logic: 1. order(s) with all suborder(s) either delivered + 6 MONTH OR cancelled
     *          2. having entry in order_payment with payment_gateway = 'wsb_credit'
     * @author: Nishu, Feb 2019
    */
    public function removeWsbCreditCashDummyEntryFromOrderPayment(){
        //Fetch Data to remove 'wsb_credit' payment entry with Logic:
        $sql = "
                SELECT
                    o.order_id,
                    GROUP_CONCAT(DISTINCT op.payment_id) AS wsb_credit_payment_ids
                FROM
                    ".DB_PREFIX."order AS o
                        INNER JOIN
                    ".DB_PREFIX."suborder AS osub ON osub.order_id = o.order_id AND
                                                     osub.order_status_id > 0
                        INNER JOIN
                    ".DB_PREFIX."order_payment AS op ON op.order_id = o.order_id
                                                        AND op.successfull = 1
                                                        AND op.payment_gateway = 'wsb_credit'
                GROUP BY o.order_id
                HAVING NOT SUM(osub.order_status_id <> 2 AND
                               (osub.delivered_date IS NULL OR
                                osub.delivered_date > NOW() - INTERVAL 6 MONTH))
               ";

        $result = $this->db->query($sql);

        foreach ($result->rows as $value) {
            $wsb_credit_payment_ids = explode(',', $value['wsb_credit_payment_ids']);

            if(!empty($wsb_credit_payment_ids)){
                foreach ($wsb_credit_payment_ids as $wsb_credit_payment_id) {
                    $data = array(
                                'payment_id'     => (int)$wsb_credit_payment_id,
                                'order_id'       => (int)$value['order_id'],
                                'selected_value' => 'CANCELED_DUMMY_ENTRY',
                                'action_comment' => 'Removing dummy entry using system cron',
                                'name'           => 'System',
                                'user_name'      => 'System Generated'
                            );
                    //Marking Order_payment entry as successfull=0 and remove advance voucher
                    $return_value = OrderEdit::deletePaymentEntryInEditOrderPayment($this, $data);
                }
            }

        }

        echo 'Completed';
    }

    /**
     * Method to reset all user profiles with new auto generated password strings
     * using password_hash() algorithm
     * @author: MSA Feb 2019
     */
    public function resetAllUserPasswords()
    {
        $user = new User($this->registry);
        $this->registry->set('user', $user);

        $sql = " SELECT
                        u.user_id,
                        u.username,
                        u.password,
                        ug.name as group_name

                FROM " . DB_PREFIX . "user AS u
                INNER JOIN
                    " . DB_PREFIX . "user_group AS ug
                        ON ug.user_group_id = u.user_group_id
                WHERE
                    status = 1
                ORDER BY u.user_group_id
               ";
        $query = $this->db->query($sql);

        $html = '<p>All khufiya vibhag user profiles updated for newly generated passwords</p>';
        $html .= '<table border="1" width="400">';
        $html .= '<tr>';
            $html .= '<th>Username</th>';
            $html .= '<th>New Passwords</th>';
            $html .= '<th>User Group</th>';
        $html .= '</tr>';
        if($query->num_rows) {

            foreach ($query->rows as $key => $user) {

                $user_id = $user['user_id'];

                $data = $this->user->getRandomPasswordString();

                if(!empty($data['password'])) {

                    $password = password_hash( $data['password'], PASSWORD_DEFAULT );

                        $sql = "UPDATE
                                    `" . DB_PREFIX . "user`
                                SET
                                    password     = '" . $this->db->escape($password) . "',
                                    old_password = '" . $this->db->escape($user['password']) . "',
                                    old_password_active = 1
                                WHERE
                                    user_id = '" . (int)$user_id . "'
                                ";
                       $this->db->query($sql);

                    $html .= '<tr>';
                        $html .= '<td>'.$user['username'].'</td>';
                        $html .= '<td>'.$data['password'].'</td>';
                        $html .= '<td>'.$user['group_name'].'</td>';
                    $html .= '</tr>';
                }

            }
        }
        $html .= '</table>';
        echo $html;

        /*Email newly generated user passwords list to Madhur & Rakesh Sir*/
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');

        $mail->setFrom(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->addReplyTo(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

        $mail->Subject = 'Generated new password for all active khufiya vibhag users -'.date('d/M/Y h:i:s');
        $mail->msgHTML($html);

        if ( $mail->send(1,false) ) {
            echo "<p>Email sent</p>";
        } else {
            echo "<p>Email failed</p>";
        }
    }

    /**
     * creates a json array file for category page filters from csv
     * @author: Anurag Jain, 16 Feb 2019
     */
    public function categoryFilterArray()
    {
        $folder_path = DIR_CACHE.'category_filter_csv';

        $filename = 'category_filters';

        $csv_filename = $filename . ".csv";

        $category_filters_array = array();

        $file = fopen( $folder_path . '/' . $csv_filename, 'r' );
        $category_filters_array = array();

        while (( $line = fgetcsv( $file )) !== FALSE ) {

            if ( !isset( $category_filters_array[ $line[0] ] )) {
                $category_filters_array[ $line[0] ] = array();
            }

            if ( !in_array( explode('-', $line[2])[0], $category_filters_array[ $line[0] ]['filter_group_ids'] )) {
                $category_filters_array[ $line[0] ]['filter_group_ids'][] = explode('-', $line[2])[0];
            }

            if ( !isset( $category_filters_array[ $line[0] ]['price_range_gap'] )) {
                $category_filters_array[ $line[0] ]['price_range_gap'] = !empty( $line[3] ) ? $line[3] : 0;
            }
        }

        fclose( $file );

        $status = file_put_contents( $folder_path . "/category_filters_array.json", json_encode( $category_filters_array ));

        if ( $status ) {
            echo "Success";
            exit;
        }

        echo "Failure";
        exit;
    }

    /*
        Public method to send SOR inventory email on weekly basis (every Monday).
        Author: MSA Feb 2019
    */
    public function alertUnsoldSORInventory()
    {
        $sql = "
                SELECT
                    op.product_id,
                    op.sku AS sku_code,
                    owp.invoice_date AS date_of_purchase,
                    SUM( owpb.pieces ) AS no_of_units_purchased,
                    MAX( op.quantity * op.piece_in_set ) AS no_of_units_in_stock,
                    DATEDIFF( CURRENT_DATE(), owp.invoice_date ) AS no_of_days_in_stock
                FROM
                    oc_wsb_purchase AS owp
                INNER JOIN
                    oc_wsb_purchase_breakup AS owpb ON owpb.purchase_id = owp.purchase_id
                INNER JOIN
                    oc_product as op ON op.product_id = owpb.product_id
                WHERE
                    owp.sor_purchase = 1
                    AND
                    op.sor_product = 1
                    AND
                    op.quantity > 0
                GROUP BY
                    op.product_id
                ORDER BY
                    no_of_days_in_stock DESC,
                    no_of_units_in_stock DESC
                ";
        $result = $this->db->query($sql);

        if( $result->num_rows )
        {
            $file_name = DIR_DLOAD . 'SOR-Inventory-'.date("d-m-Y").'.csv';

            $fp = fopen($file_name,'w');

            $data = array(
                            'SKU Code',
                            'Date of Purchase',
                            'No. of Units Purchased',
                            'No. of Units in Stock',
                            'No. of Days in Stock'
                        );
            //add heading line
            fputcsv($fp, $data);

            foreach ($result->rows as $key => $value) {

                $data = array(
                                $value['sku_code'],
                                $value['date_of_purchase'],
                                $value['no_of_units_purchased'],
                                $value['no_of_units_in_stock'],
                                $value['no_of_days_in_stock']
                            );

                //add SOR inventory data
                fputcsv($fp, $data);
            }

            $body = "Please find attached CSV file for the Unsold SOR Inventory.";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);

            $mail->Subject = 'Unsold SOR inventory - '.date('d/M/Y h:i:s');
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);

            if ( $mail->send(0,false) )
            {
                echo "success";
            } else {
                echo "error";
            }

            unlink($file_name);
        }
        echo 'Completed';
    }

    /*
        Public method to send email alert to store manager when
        purchase inventory goes stock-out.
        Author: MSA Feb 2019
    */
    public function alertUnsoldSellerMembershipInventory()
    {
        $sql = "
                SELECT
                    op.product_id,
                    op.sku AS sku_code,
                    date( op.date_added) as date_of_sku_creation,
                    MAX( op.quantity * op.piece_in_set ) AS no_of_units_in_stock,
                    DATEDIFF( CURRENT_DATE(), date(op.date_added) ) AS no_of_days_in_stock
                FROM
                    oc_product as op
                WHERE
                    op.sku LIKE 'WSBJPSM%'
                    AND
                    op.quantity > 0
                GROUP BY
                    op.product_id
                ORDER BY
                    no_of_days_in_stock DESC,
                    no_of_units_in_stock DESC
               ";

        $result = $this->db->query($sql);

        if( $result->num_rows )
        {
            $file_name = DIR_DLOAD . 'Seller-Membership-Inventory-'.date("d-m-Y").'.csv';

            $fp = fopen($file_name,'w');

            $data = array(
                            'SKU Code',
                            'Date of SKU Creation',
                            'No. of Units in Stock',
                            'No. of Days in Stock'
                        );
            //add heading line
            fputcsv($fp, $data);

            foreach ($result->rows as $key => $value)
            {
                $data = array(
                                $value['sku_code'],
                                $value['date_of_sku_creation'],
                                $value['no_of_units_in_stock'],
                                $value['no_of_days_in_stock']
                            );
                //add SOR inventory data
                fputcsv($fp, $data);
            }

            $body = "Please find attached CSV file for the Unsold Seller Membership Inventory.";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);

            $mail->Subject = 'Unsold Seller Membership Inventory - '.date('d/M/Y h:i:s');
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);

            if ( $mail->send(0,false) )
            {
                echo "success";
            } else {
                echo "error";
            }

            unlink($file_name);
        }
        echo 'Completed';
    }

    /**
     * Public method to get customer details whose wsb_credit approved and not placed order from last 30 days
     * @author: Nishu, Feb 2019
    */
    public function getWsbCreditClientNotOrderedFromLongTime(){
        $fresh_client    = array();
        $other_client    = array();
        $agent_wise_data = array();

        $sql = "
                SELECT
                    a.company,
                    IF(TRIM(CONCAT(c.firstname, ' ', c.lastname)) = '',
                       TRIM(CONCAT(a.firstname, ' ', a.lastname)),
                       TRIM(CONCAT(c.firstname, ' ', c.lastname))
                    ) AS owner_name,
                    a.city,
                    '' AS agent_name,
                    '' AS tl_name,
                    MAX(o.date_added) AS last_order_date,
                    MAX(osub.delivered_date) AS last_deliver_date,
                    DATEDIFF(NOW(), MAX(o.date_added)) AS ordered_days_diff,
                    DATEDIFF(NOW(), MAX(osub.delivered_date)) AS days_diff,
                    MAX(o.order_id) AS max_order,
                    c.customer_id,
                    CONCAT(c.firstname, ' ', c.lastname) AS customer_name,
                    IF(MAX(o.order_id) IS NULL, 1 , 0) AS fresh_client,
                    MIN(log.date_added) AS credit_activated_date
                FROM
                    oc_customer_wsb_credit AS cwc
                        INNER JOIN
                    oc_customer AS c ON c.customer_id = cwc.customer_id
                        LEFT JOIN
                    oc_admin_change_log AS log ON log.table_id = cwc.customer_id
                    AND log.table_name = 'oc_customer_wsb_credit'
                    AND log.field_name = 'status'
                    AND log.new_value  = 'ENABLED'
                        LEFT JOIN
                    oc_address AS a ON c.address_id = a.address_id
                        LEFT JOIN
                    oc_order AS o ON o.customer_id = c.customer_id
                        AND o.store_id IN (0 , 2, 9)
                        AND o.franchise_id = 0
                        AND o.stock_transfer = 0
                        LEFT JOIN
                    oc_suborder AS osub ON osub.order_id = o.order_id
                        AND osub.order_status_id > 0
                        AND osub.order_status_id != 2

                WHERE
                    cwc.status = 'ENABLED'
                GROUP BY
                    cwc.customer_id
                HAVING
                    (max_order IS NULL OR (days_diff >= 30 AND ordered_days_diff >= 30 ) )
                ORDER BY
                    days_diff DESC, a.company
               ";
        $qry = $this->db->query($sql);
        if($qry->num_rows > 0){
            $results = $qry->rows;

            $customer_ids    = array_unique( array_column($results, 'customer_id') );
            $customer        = BankTransfer::getCustomerCredentials($this->db);

            $alldata                 = array();
            $alldata['customer']     = $customer;
            $alldata['customer_ids'] = $customer_ids ?? array();

            //Get TL Data from CRM APIs
            $agent_tl_data = Customer::getTlDataForCustomers($alldata);

            foreach ($results as $key => $value) {
                $customer_id       = $value['customer_id'];
                $tl_name           = '';
                $agent_name        = '';
                $agent_assign_date = '';
                if(!empty($agent_tl_data[$customer_id])){
                    $tl_data = $agent_tl_data[$customer_id]['tl_data'] ?? array();
                    if(!empty($tl_data)){
                        foreach ($tl_data as $tl) {
                            $tl_name .= $tl->name. ',';
                        }
                        $tl_name = trim($tl_name, ',');
                    }

                    $agent_data = $agent_tl_data[$customer_id]['agent_data'] ?? array();
                    if(!empty($agent_data)){

                        $agent_name        = $agent_data->name;
                        $agent_assign_date = $agent_data->last_assignment_date;
                    }
                }
                $value['tl_name']           = trim($tl_name, ',');
                $value['agent_name']        = $agent_name;
                $value['agent_assign_date'] = $agent_assign_date;

                $tl_data = $agent_tl_data[$customer_id]['tl_data'] ?? array();
                if(!empty($tl_data)){
                    foreach ($tl_data as $tl) {
                        //Data agent_wise_breakup
                        if(!isset($agent_wise_data[$tl->email])){
                            $agent_wise_data[$tl->email] = array();
                        }

                        if($value['fresh_client'] == 1){
                            $agent_wise_data[$tl->email]['fresh_client'][] = $value;
                        }else{
                            $agent_wise_data[$tl->email]['other_client'][] = $value;
                        }
                    }
                }

                if(!empty($value['credit_activated_date'])){
                    $value['credit_activated_date'] = date('d-m-Y', strtotime($value['credit_activated_date']));
                }

                if($value['fresh_client'] == 1){
                    $fresh_client[$customer_id] = $value;
                }else{
                    $other_client[$customer_id] = $value;
                }
            }
            //Send mail containing all customers, whose WSB_CREDIT approved but no order placed from long time
            $this->sendMailWsbCreditNoOrderFromLongTime($fresh_client, $other_client);

            //To send mail TL or agent wise
            $this->sendMailTlOrAgentWise($agent_wise_data);
        }
        echo 'Completed';
    }

    /**
     * @info: Public function to send mail TL or agent wise
     * @author: Nishu, April 2019
    */
    private function sendMailTlOrAgentWise($agent_wise_data){

        if(!empty($agent_wise_data)){
            foreach ($agent_wise_data as $key => $value) {
                $fresh_client = $value['fresh_client'];
                $other_client = $value['other_client'];

                $html = MailTemplate::mailToWsbCreditClientNotOrderedFromLongTime($fresh_client, $other_client);

                if(!empty($fresh_client)){
                    $file1 = DIR_DLOAD . 'WSB_CREDIT-Fresh Client-' . date("dMYhis") . '.csv';
                    $fp1 = fopen($file1, 'w');

                    $head = array(
                                  'S. No.',
                                  'Customer ID',
                                  'Customer Name',
                                  'Shop Name',
                                  'Owner Name',
                                  'City',
                                  'Agent Name',
                                  'TL Name',
                                  'Agent Assigned Date',
                                  'Credit Activated Date'
                                );
                    fputcsv($fp1, $head);

                    $i=1;
                    foreach ($fresh_client as $data){

                        $row = array(
                                  $i,
                                  $data['customer_id'],
                                  trim($data['customer_name']),
                                  trim($data['company']),
                                  trim($data['owner_name']),
                                  trim($data['city']),
                                  trim($data['agent_name']),
                                  trim($data['tl_name']),
                                  trim($data['agent_assign_date']),
                                  $data['credit_activated_date']
                                );
                        fputcsv($fp1, $row);
                        $i++;
                    }

                    fclose($fp1);
                    chmod($file1, 0777);
                }


                if(!empty($other_client)){
                    $file2 = DIR_DLOAD . 'WSB_CREDIT-NotOrderedClient-' . date("dMYhis") . '.csv';
                    $fp2 = fopen($file2, 'w');
                    //.csv for those clients who has not placed order from long time
                    $head = array(
                                  'S. No.',
                                  'Customer ID',
                                  'Shop Name',
                                  'Owner Name',
                                  'City',
                                  'Agent Name',
                                  'TL Name',
                                  'Agent Assigned Date',
                                  'Last Ordered Date',
                                  'Last Delivered Date',
                                  'Days From Delivery',
                                  'Credit Activated Date'
                                );
                    fputcsv($fp2, $head);

                    $i=1;
                    foreach ($other_client as $data){
                        $last_order_date = '';
                        if(!empty($data['last_order_date'])){
                            $last_order_date = date("d-m-Y", strtotime($data['last_order_date']));
                        }
                        $last_deliver_date = '';
                        if(!empty($data['last_deliver_date'])){
                            $last_deliver_date = date("d-m-Y", strtotime($data['last_deliver_date']));
                        }

                        $row = array(
                                  $i,
                                  $data['customer_id'],
                                  trim($data['company']),
                                  trim($data['owner_name']),
                                  trim($data['city']),
                                  trim($data['agent_name']),
                                  trim($data['tl_name']),
                                  trim($data['agent_assign_date']),
                                  $last_order_date,
                                  $last_deliver_date,
                                  MIN($data['days_diff'], $data['ordered_days_diff']),
                                  $data['credit_activated_date']
                                );
                        fputcsv($fp2, $row);
                        $i++;
                    }
                    fclose($fp2);
                    chmod($file2, 0777);
                }

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');

                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');

                $mail->addAddress($key);

                $mail->Subject = 'WSB Credit approved Client not ordering from last 30 days ('.date("d F Y H:i").')';
                $mail->msgHTML($html);
                //Statement For adding attachment in mail
                if(!empty($fresh_client)){
                    $mail->AddAttachment($file1);
                }
                if(!empty($other_client)){
                    $mail->AddAttachment($file2);
                }
                $mail->send(0,false);

                if(!empty($fresh_client)){ unlink($file1); }
                if(!empty($other_client)){ unlink($file2); }


            }
        }
    }

    /**
     * @info : Public method to send mail for wsb_credit approved customer who has not placed order from long time
     * @author: Nishu, April 2019
    */
    private function sendMailWsbCreditNoOrderFromLongTime($fresh_client, $other_client){
        // Sending mail to operation
        $html = MailTemplate::mailToWsbCreditClientNotOrderedFromLongTime($fresh_client, $other_client);

        if(!empty($fresh_client)){
            $file1 = DIR_DLOAD . 'WSB_CREDIT-Fresh Client-' . date("dMYhis") . '.csv';
            $fp1 = fopen($file1, 'w');

            $head = array(
                          'S. No.',
                          'Customer ID',
                          'Customer Name',
                          'Shop Name',
                          'Owner Name',
                          'City',
                          'Agent Name',
                          'TL Name',
                          'Agent Assigned Date',
                          'Credit Activated Date'
                        );
            fputcsv($fp1, $head);

            $i=1;
            foreach ($fresh_client as $data){

                $row = array(
                          $i,
                          $data['customer_id'],
                          trim($data['customer_name']),
                          trim($data['company']),
                          trim($data['owner_name']),
                          trim($data['city']),
                          trim($data['agent_name']),
                          trim($data['tl_name']),
                          trim($data['agent_assign_date']),
                          $data['credit_activated_date']
                        );
                fputcsv($fp1, $row);
                $i++;
            }

            fclose($fp1);
            chmod($file1, 0777);
        }

        if(!empty($other_client)){
            $file2 = DIR_DLOAD . 'WSB_CREDIT-NotOrderedClient-' . date("dMYhis") . '.csv';
            $fp2 = fopen($file2, 'w');

            //.csv for those clients who has not placed order from long time
            $head = array(
                          'S. No.',
                          'Customer ID',
                          'Shop Name',
                          'Owner Name',
                          'City',
                          'Agent Name',
                          'TL Name',
                          'Agent Assigned Date',
                          'Last Ordered Date',
                          'Last Delivered Date',
                          'Days From Delivery',
                          'Credit Activated Date'
                        );
            fputcsv($fp2, $head);

            $i=1;
            foreach ($other_client as $data){
                $last_order_date = '';
                if(!empty($data['last_order_date'])){
                    $last_order_date = date("d-m-Y", strtotime($data['last_order_date']));
                }
                $last_deliver_date = '';
                if(!empty($data['last_deliver_date'])){
                    $last_deliver_date = date("d-m-Y", strtotime($data['last_deliver_date']));
                }

                $row = array(
                          $i,
                          $data['customer_id'],
                          trim($data['company']),
                          trim($data['owner_name']),
                          trim($data['city']),
                          trim($data['agent_name']),
                          trim($data['tl_name']),
                          trim($data['agent_assign_date']),
                          $last_order_date,
                          $last_deliver_date,
                          MIN($data['days_diff'], $data['ordered_days_diff']),
                          $data['credit_activated_date']
                        );
                fputcsv($fp2, $row);
                $i++;
            }

            fclose($fp2);
            chmod($file2, 0777);
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');

        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');

        $mail->addAddress(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
        $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
        $mail->addAddress(EMAIL_IDS['tele.manager']['email_id'], EMAIL_IDS['tele.manager']['name']);

        $mail->Subject = 'WSB Credit approved Client not ordering from last 30 days ('.date("d F Y H:i").')';
        $mail->msgHTML($html);
        //Statement For adding attachment in mail
        if(!empty($fresh_client)){
            $mail->AddAttachment($file1);
        }

        if(!empty($other_client)){
            $mail->AddAttachment($file2);
        }
        $mail->send(0,false);

        if(!empty($fresh_client)){
            unlink($file1);
        }
        if(!empty($other_client)){
            unlink($file2);
        }
    }

    /**
     * @info : Public method to get NACH Schedule for delivered ordered
     * @return: Nishu, March 2019
    */
    public function generateNachSchedule(){

        ini_set('max_execution_time', 0);

        //Remove wsb_credit dummy payment entry and notify internal team for those order(s) having pending balance
        $this->removeWsbCreditCashDummyEntryFromOrderPayment();

        //Generate NACH schedule for delivered WSB_CREDIT order(s)
        $nach = new NachBehaviour($this->registry);

        $nach->generateNachSchedule();

        echo 'Completed';
    }

    /**
     * @info: Cron to get data, all order(s) whose all suborders are
     *         either- (Cancelled and date_modified = current_date-1 day)
     *         or    - (delivered_date = current_date -30 days)
     * and Order_bal < -5
     * and Order_date_added >= 1 April 2018
     *
     * @author: Nishu, March 2019
     *        : MSA May 2019 (Updated)
    */
    public function getOrdersWithPendingBalanceToRecover() {

        $default_email_receivers = array('rohit','chandan','vikas','accounts');

        //Default value is set to 30 Days for suborder_delivered_date
        $days_diff          = $this->request->get['days'] ?? 30;
        $minimum_order_bal  = $this->request->get['order_bal'] ?? '-5';
        $payment_methods    = $this->request->get['payment_methods'] ?? '';
        $emails             = $this->request->get['emails'] ?? '';
        if(!empty($emails)) {
          $emails = explode(',', $emails);
        } else{
           $emails = $default_email_receivers;
        }

        $sql = "
                SELECT
                    MAX(osub.is_fully_failed_order) AS is_fully_failed_order,
                    o.order_id,
                    o.order_no,
                    o.customer_id,
                    TRIM(CONCAT(o.firstname, ' ', o.lastname)) AS customer_name,
                    o.payment_city,
                    o.payment_code,
                    o.date_added AS order_date,
                    MAX(osub.last_delivered_date) AS last_delivered_date,
                    o.total AS order_total,
                    o.payment_company as shop_name,

                    DATE( MAX(
                            IF( op.payment_gateway = 'wsb_credit_nach' && op.successfull = 1, op.txn_date_time, NULL )
                    ) ) as last_successfull_payment_date,

                    DATE( MAX(
                            IF( op.payment_gateway = 'wsb_credit_nach' && op.successfull = 0, op.txn_date_time, NULL )
                    ) ) as last_failed_nach_date,
                    ROUND((
                        - COALESCE(o.total, 0)
                        + COALESCE(cn.cn_amount, 0)
                        - COALESCE(cn.cod_failed_penalty, 0)
                        + COALESCE( SUM(IF(op.amount > 0 AND op.successfull = 1
                                    AND (op.payment_gateway = 'cashback'
                                    OR op.payment_gateway = 'coupon'),
                                op.amount,
                                0)) , 0)
                        + COALESCE( SUM(IF(op.amount > 0 AND op.successfull = 1
                                    AND op.payment_gateway != 'cashback'
                                    AND op.payment_gateway != 'coupon'
                                    AND op.payment_gateway != 'wsb_credit'
                                    AND op.txn_status      != 'cheque_deposited',
                                op.amount,
                                0)) , 0)
                        + COALESCE( SUM(IF(op.amount < 0 AND op.successfull = 1
                                    AND op.payment_gateway != 'cashback'
                                    AND op.payment_gateway != 'coupon'
                                    AND op.payment_gateway != 'wsb_credit',
                                op.amount,
                                0)) , 0)
                        - COALESCE( cn.less_cash_discount, 0)
                        + COALESCE( cn.other_charges, 0)
                        ) ,
                            2) AS order_bal,
                  DATEDIFF( NOW(), MAX(osub.last_delivered_date) ) AS days
                FROM
                    oc_order o
                    INNER JOIN (
                                SELECT order_id,
                                       GROUP_CONCAT(DISTINCT
                                                      IF(order_status_id = ".ORDER_STATUS['Canceled']." OR
                                                         order_status_id = ".ORDER_STATUS['Failed']." OR
                                                         DATEDIFF(NOW(), delivered_date) >= ".(int)$days_diff.",
                                                         1,
                                                         0)) AS suborderwise_flag,
                                       MAX(delivered_date) AS last_delivered_date,
                                       SUM(IF(order_status_id != ".ORDER_STATUS['Failed']." , 1, 0)) AS is_fully_failed_order
                                FROM oc_suborder
                                WHERE order_status_id > 0
                                GROUP BY order_id
                                HAVING suborderwise_flag = '1'
                               ) AS osub ON osub.order_id = o.order_id
                    LEFT JOIN
                        oc_order_payment AS op ON op.order_id = o.order_id

                    LEFT JOIN (
                                SELECT
                                    COALESCE(SUM(credit_note_amount), 0) AS cn_amount,
                                    COALESCE(SUM(cod_failed_penalty), 0) AS cod_failed_penalty,
                                    COALESCE(SUM(less_cash_discount), 0) AS less_cash_discount,
                                    COALESCE(SUM(other_charges), 0) AS other_charges,
                                    order_id
                                FROM
                                    oc_credit_note
                                WHERE
                                    credit_note_status = 1
                                GROUP BY
                                    order_id
                            ) AS cn ON cn.order_id = o.order_id

                WHERE
                            o.date_added >= '2018-04-01 00:00:00'
                        AND o.currency_code = 'INR'
                        AND o.store_id IN (". WSB_STORES_ID .")
                        AND o.stock_transfer = 0
                        AND o.franchise_id = 0
                ";

        if(!empty($payment_methods)) {
            $payment_codes = implode("','", explode(",",$payment_methods));
            $sql .= " AND o.payment_code IN('".$payment_codes."') ";
        }

        $sql .= "
                    GROUP BY o.order_id
                    HAVING order_bal < ".(float)$minimum_order_bal."
                    ORDER BY o.order_id ASC
                ";
        
        $qry = $this->db->query($sql);

        if ($qry->num_rows) {

            $data = $qry->rows;

            $file_name = DIR_DLOAD . 'OrderWithPendingBalance-'.date("d-m-Y").'.csv';
            $fp = fopen($file_name,'w');

            $head = array(
                            'Order No',
                            'Customer Name',
                            'Shop Name',
                            'Payment City',
                            'Payment Method',
                            'Order Date',
                            'Delivered Date',
                            'Order Total Value',
                            'Balance Pending',
                            'Last Successfull NACH Date',
                            'Last Failed NACH Date',
                            'Days'
                        );
            //add heading line
            fputcsv($fp, $head);

            foreach ($data as $key => $value)
            {
                $row = array(
                                $value['order_no'],
                                $value['customer_name'],
                                $value['shop_name'],
                                $value['payment_city'],
                                $value['payment_code'],
                                date('d M Y',strtotime($value['order_date'])),
                                date('d M Y',strtotime($value['last_delivered_date'])),
                                $value['order_total'],
                                ROUND($value['order_bal'], 2),
                                (!empty($value['last_successfull_payment_date'])) ? date('d M Y',strtotime($value['last_successfull_payment_date'])) : '',
                                (!empty($value['last_failed_nach_date'])) ? date('d M Y',strtotime($value['last_failed_nach_date'])) : '',
                                $value['days']
                            );

                fputcsv($fp, $row);
            }

            // Sending mail to operation
            $html = MailTemplate::orderWithAllSuborderEitherCancelledOrDeliveredButBalPending($data);

            $subject = 'Orders with Pending Balance Amount to be Recovered - '. date('d-m-Y H:i:s');

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');

            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
            $mail->addReplyTo(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);

            if(!empty($emails)) {
                foreach ($emails as $key => $value) {
                    if(!empty(EMAIL_IDS[$value]['email_id'])) {
                        $mail->addAddress(EMAIL_IDS[$value]['email_id'], EMAIL_IDS[$value]['name']);
                    }
                }
            }

            $mail->Subject = $subject;
            $mail->msgHTML($html);
            //Statement For adding attachment in mail
            $mail->AddAttachment($file_name);
            $mail->send(1, false);
            echo 'Mail Sent';

            unlink($file_name);
        }
    }

    public function placeOrderclubFactoryUnshippedOrderProduct(){
        ini_set('max_execution_time', 0);
        $this->load->model('catalog/product');
        $this->load->model('catalog/club_factory_product');
        $login_result = $this->clubfactoryLoginProcess();
        if(empty($login_result['firstErrorMessage']['message']) && !empty($login_result['success']) && $login_result['success']=='1'){
           $token = $login_result['model']['token'];
           $api_url = 'http://seller.clubfactory.com/oms/order/list?orderStatus=unshipped&pageNo=1&pageSize=1000';
                $result = $this->getClubfactoryUnshippedOrderList($token,$api_url);
                $order_success=array();
                $order_fail=array();
                    if(!empty($result['success']) && $result['success']=='1' && !empty($result['model']['result'])){
                            //check order placed or not
                        foreach ($result['model']['result'] as $key => $clubfactory_order_detail) {
                            $order_status=$this->checkClubfactoryOrderPlacedOrNot($clubfactory_order_detail);
                           if($order_status){
                                $get_order_product_detail=$this->getClubFactoryOrderProductDetail($clubfactory_order_detail['orderNo'],$clubfactory_order_detail['orderId'],$token);
                                //echo '<pre>'; print_r($get_order_product_detail);

                                $order_data=$this->createOrderFields($get_order_product_detail['model']);
                                if(!empty($order_data['products'])){
                                    $this->load->model('checkout/order');
                                    $input['order_id'] = $this->model_checkout_order->addOrder($order_data);
                                    if(!empty(trim($input['order_id']))){
                                        $input['order_status_id'] = ORDER_STATUS['Pending'];
                                        $input['notes'] = 'clubfactory order number is '.$clubfactory_order_detail['orderNo'].' and order id is '.$clubfactory_order_detail['orderId'];
                                        $history_arr = $this->model_checkout_order->addOrderHistory($input);
                                        if (!empty($history_arr['error'])) {
                                            //$this->model_catalog_product->updateClubfactoryOrderStatus($order_status,0);
                                            $order_fail[$key]['message']=$history_arr['error'];
                                            $order_fail[$key]['club_factory_order_id']=$clubfactory_order_detail['orderId'];
                                            $order_fail[$key]['club_factory_order_number']=$clubfactory_order_detail['orderNo'];
                                           // echo  $history_arr['error'];
                                        } else {
                                            $order_info = new OrderInfo();
                                            $order_no = $order_info->getOrderNo($this->db, $input['order_id']);
                                            $order_success[$key]['wsb_order_number']=$order_no;
                                            $order_success[$key]['club_factory_order_id']=$clubfactory_order_detail['orderId'];
                                            $order_success[$key]['club_factory_order_number']=$clubfactory_order_detail['orderNo'];
                                           // $this->model_catalog_product->updateClubfactoryOrderStatus($order_status,1);
                                            $clubfactory_order_detail['club_factory_order_id']=$clubfactory_order_detail['orderId'];
                                            $clubfactory_order_detail['wsb_order_id']=$input['order_id'];
                                            $this->model_catalog_club_factory_product->insertClubfactoryOrderPlaced($clubfactory_order_detail);
                                            sleep(3);

                                        }
                                    }else{
                                            $order_fail[$key]['message']='order not placed';
                                            $order_fail[$key]['club_factory_order_id']=$clubfactory_order_detail['orderId'];
                                            $order_fail[$key]['club_factory_order_number']=$clubfactory_order_detail['orderNo'];
                                    }
                                }else{
                                    $order_fail[$key]['message']='Club factory order product not found';
                                    $order_fail[$key]['club_factory_order_id']=$clubfactory_order_detail['orderId'];
                                    $order_fail[$key]['club_factory_order_number']=$clubfactory_order_detail['orderNo'];
                                }

                            }
                        }
                        if(!empty($order_fail)){
                            $subject='Club Factory Fail order List - '.date('d/M/Y h:i:s');
                            //$toAddress['email']='9874563210@mailinator.com';
                            $data['order_details'] = $order_fail;
                            $html = $this->load->view($this->config->get('config_template') . '/template/mail/send_mail_after_clubfactory_order_fail.tpl',$data);
                            $this->sendMailAfterClubFactoryOrder($subject, $html, 'fail');
                        }
                        if(!empty($order_success)){
                            $subject='Club Factory Placed order List - '.date('d/M/Y h:i:s');
                            //$toAddress['email']='9874563210@mailinator.com';
                            $data['order_details'] = $order_success;
                            $html = $this->load->view($this->config->get('config_template') . '/template/mail/send_mail_after_clubfactory_order_placed.tpl',$data);
                            $this->sendMailAfterClubFactoryOrder($subject, $html, 'success');
                            //echo '<pre>';print_r($order_success);
                        }
                    }else{
                        echo $result['firstErrorMessage']['message'];
                    }
                }else{
                    echo $login_result['firstErrorMessage']['message'];
                }
        echo 'Process complete';
        exit;
    }

    private function sendMailAfterClubFactoryOrder($subject, $html, $type){
        if (method_exists($this, 'get')) {
            $config = $this->get('config');
        } else {
            $config = $this->config;
        }
        $html = $html;
        $mail = new  PHPMailer();
        $mail->isSMTP();
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
        if($type=='success'){
            $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
            $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
            $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
        }else{
            $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
        }
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->isHTML(true);
        $mail->send();
    }
    private function getClubfactoryUnshippedOrderList($token,$api_url){
        $ch = curl_init( $api_url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch,
                     CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: '.$token));
        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec( $ch );
        $result = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $result );
        $result = json_decode( $result, true );
        return $result;
    }

    private function clubfactoryLoginProcess(){
        $request['loginName'] = CLUB_FACTORY_USERNAME;
        $request['password'] = CLUB_FACTORY_PASSWORD;
        $request['type'] = 'seller';
        $api_url = 'http://seller.clubfactory.com/auth/login';
        $data_json = json_encode($request);
        $ch = curl_init( $api_url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch,
                     CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                     'Content-Length: ' . strlen( $data_json )));
        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_json );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec( $ch );
        $result = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $result );
        $result = json_decode( $result, true );
        return $result;
    }

    private function checkClubfactoryOrderPlacedOrNot($clubfactory_order_detail){
                $this->load->model('catalog/club_factory_product');
                $response=$this->model_catalog_club_factory_product->checkClubfactoryOrderPlacedOrNot($clubfactory_order_detail);
                return $response;

    }


    private function getClubFactoryOrderProductDetail($order_number,$order_id,$token){
        $api_url = 'http://seller.clubfactory.com/oms/order/'.$order_number.'/detail?orderId='.$order_id;
        $ch = curl_init( $api_url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch,
                     CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: '.$token));
        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec( $ch );
        $result = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $result );
        $result = json_decode( $result, true );
        return  $result;
    }

        private function createOrderFields($order_deails){
             $this->load->model('localisation/country');
             $this->load->model('localisation/zone');
             $this->load->model('localisation/currency');
             $this->load->model('catalog/product');
             $this->load->model('catalog/club_factory_product');
             $data['products']=array();
            $currency_id= $this->model_localisation_currency->getCurrencyByCode($order_deails['currency']);

             $get_county_id=$this->model_localisation_country->getCountryIdByName($order_deails['consigneeInfo']['country']);
             $get_zone_id=$this->model_localisation_zone->getZoneIdByName($order_deails['consigneeInfo']['state']);
             $calculate_total= $order_deails['productsTotal']* $order_deails['dollarExchangeRate'];
             $total = round($calculate_total,2);
                 $data['store_id']="0";
                 $data['store_name']="WholesaleBox";
                 $data['store_url']=HTTPS_SERVER;
                 $data['customer_id']="";
                 $data['customer_id']="88772";
                 $data['firstname']="Govind";
                 $data['email']="test12@gmail.com";
                 $data['telephone']="7062176109";
                 if(!empty($order_deails['consigneeInfo']['phone']) &&  strlen($order_deails['consigneeInfo']['phone']>=10)){
                  $data['address_telephone']=array(substr($order_deails['consigneeInfo']['phone'], -10));
                 }
                 $data['payment_firstname']=$data['shipping_firstname']=$order_deails['consigneeInfo']['consigneeName'];
                 $data['payment_address_1']=$data['shipping_address_1']=$order_deails['consigneeInfo']['street'];
                 $data['payment_city']=$data['shipping_city']=$order_deails['consigneeInfo']['city'];
                 $data['payment_postcode']=$data['shipping_postcode']=$order_deails['consigneeInfo']['pinCode'];
                 $data['payment_country']=$data['shipping_country']=$order_deails['consigneeInfo']['country'];
                 $data['payment_country_id']=$data['shipping_country_id']=$get_county_id;
                 $data['payment_zone_id']=$data['shipping_zone_id']=$get_zone_id;
                 $data['payment_zone']=$data['shipping_zone']=$order_deails['consigneeInfo']['state'];
                 $data['payment_method']="Cheque or Cash Deposit/IMPS/NEFT/RTGS";
                 $data['payment_code']="bank_transfer";
                 $data['payment_company']=$data['shipping_company']="Club Factory";
                 $data['total']=$total;
                 $data['affiliate_id']="0";
                 $data['commission']="0.0000";
                 $data['marketing_id']="0";
                 $data['language_id']="1";
                 $data['currency_id']=$currency_id['currency_id']??'0';
                 $data['currency_code']=$order_deails['currency'];
                 $data['currency_value']=$data['currency_live_conversion_rate']="1";
                 $data['ip']=$data['forwarded_ip']="175.111.130.14";
                 $data['user_agent']="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36";
                 $data['accept_language']="en-US,en;q=0.9";
                 $data['order_from']="WEB";
                 $data['shipping_charge']="0";
                 $data['weight_class_id']="1";
                 $data['store_voucher']="";
                 $data['gst_number']="";
                 $data['gst_unregister_declared']="1";
                 $data['campaign_event_no']="";
                 $data['franchise_id']="0";
                 $data['franchise_margin']="0";
                 $data['shipping_method']='Self Pickup from Warehouse';
                 $data['shipping_code']='self_pickup';
                 $data['refund_status']='not_applicable';
                 $data['payment_custom_field']="";
                 $data['shipping_custom_field']="";
                 $data['custom_field']="";

                 if(!empty($order_deails['productList'])){
                    foreach ($order_deails['productList'] as $key => $product_value) {
                        $product_detail=$this->model_catalog_club_factory_product->getProductAccordingClubFactorySku($product_value['sellerSku']);
                        if(!empty($product_detail)){
                            $product_id=$product_detail['product_id'];
                            $product_details['piece_in_set']='1';
                            $product_details=$this->model_catalog_product->getProduct($product_id);
                            if(isset($product_details['weight'])){
                              $product_details['weight_per_piece']=$product_details['weight'];
                            }

                            if(!empty($product_detail['product_option_value_id'])){
                                $product_option_value=$this->model_catalog_club_factory_product->getProductOptionValueId($product_id,$product_detail['product_option_value_id']);
                                if(!empty($product_option_value)){
                                    $product_details['weight_per_piece']=$product_details['weight_per_piece']+$product_option_value['weight'];
                                    $product_details['option_price']=$product_option_value['option_price'];
                                    $product_details['option']['0']=$product_option_value;
                                }

                            }
                            if(!empty($product_details['option_price'])){
                                $price_details = $this->cart->getPrice(array('product_id' => (int)$product_id,'option_price'=>(float)$product_details['option_price']),$this);
                            }else{
                                $price_details = $this->cart->getPrice(array('product_id' => (int)$product_id),$this);
                            }
                            if(!empty($price_details['transfer_price_per_piece'])){
                              $product_details['transfer_price_per_piece']=round($price_details['transfer_price_per_piece'],2);
                            }
                            //$product_details['output_tax_rates']='0.00';
                             $calculate_product_total= $product_value['productPrice']* $order_deails['dollarExchangeRate'];
                             $product_details['price_per_piece']=round($calculate_product_total/(1+($product_details['tax_rate']/100)),2);
                             $product_details['quantity']=$product_value['quantity']??'1';
                             $product_details['output_tax_rates']=$product_details['tax_rate'];
                             $product_details['piece_in_set']='1';
                             $product_details['discount_breakup']='0';
                             $product_details['combo_product_id']=$this->model_catalog_product->getComboProductIdOfAssociate($product_id);
                             $data['products'][$key]=$product_details;
                        }
                    }
                 }

                 //$data['products'][]=$this->model_catalog_product->getProduct('159345');
                 return $data;

    }

    /**
     * @info: Public method to get client details whose return_rate is more then or equals to 10 percent of purchased order(s)
     * @author: Nishu, March 2019
    */
    public function getClientWithHighReturnRate(){
        $sql = "
                SELECT
                    COUNT(DISTINCT IF(ocr.order_product_id > 0
                                AND orr.return_reason_id != 7
                                AND orr.reason_type = 'RETURN'
                                AND cn.credit_note_status = 1,
                            oo.order_id,
                            NULL)) AS return_order_count,
                    COUNT(DISTINCT oo.order_id) AS order_count,
                    c.customer_id,
                    c.master_id,
                    CONCAT(c.firstname, ' ', c.lastname) AS customer_name,
                    MAX(oo.order_no) AS order_no
                FROM
                    oc_order AS oo
                        INNER JOIN
                    oc_customer AS c ON oo.customer_id = c.customer_id
                        INNER JOIN
                    oc_suborder AS so ON oo.order_id = so.order_id
                        LEFT JOIN
                    oc_credit_note AS cn ON cn.credit_note_id = oo.order_id
                        LEFT JOIN
                    oc_return AS ocr ON ocr.credit_note_id = cn.credit_note_id
                        LEFT JOIN
                    oc_return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
                WHERE
                    so.order_status_id > 0
                        AND so.order_status_id != 2
                        AND so.order_status_id != 8
                        AND oo.stock_transfer = 0
                        AND oo.store_id IN (0 , 2, 9)
                        AND so.buyer_invoice_id > 0
                        AND so.invoice_no > 0
                        AND oo.franchise_id = 0
                GROUP BY c.customer_id
                HAVING order_count >= 5
                    AND (return_order_count / order_count) * 100 >= 10
                ORDER BY
                    return_order_count DESC
               ";
        $qry = $this->db->query($sql);

        if ($qry->num_rows) {
            $data = $qry->rows;

            $file_name = DIR_DLOAD . 'ClientsWithHighReturnRate-'.date("d-m-Y").'.csv';
            $fp = fopen($file_name,'w');

            $head = array(
                            'Customer Id',
                            'Master Id',
                            'Customer Name',
                            'Last Order No',
                            'Total Orders',
                            'Total Return Order',
                            'Return Rate'
                        );
            //add heading line
            fputcsv($fp, $head);

            foreach ($data as $key => $value)
            {
                //Calculate Return rate
                $return_rate = ($value['return_order_count']/ $value['order_count']) * 100;

                $row = array(
                                $value['customer_id'],
                                $value['master_id'],
                                $value['customer_name'],
                                $value['order_no'],
                                $value['order_count'],
                                $value['return_order_count'],
                                ROUND($return_rate, 2)
                            );
                //add SOR inventory data
                fputcsv($fp, $row);
            }

            // Sending mail to operation
            $html = MailTemplate::mailClientWithHighReturnRate($data);

            $subject = 'Client list who having high return rate :'. date('d-m-Y H:i:s');

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            ;
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);

            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);

            $mail->Subject = $subject;
            $mail->msgHTML($html);
            //Statement For adding attachment in mail
            $mail->AddAttachment($file_name);
            $mail->send(0, false);
            echo 'Mail Sent';

            unlink($file_name);
        }
    }

    /*
        Public method to send email alert for Seller wise live stock inventory data
        Author: MSA April 2019
    */
    public function sellerWiseLiveStockInventory()
    {
        $sql = "
                SELECT
                    mss.seller_id,
                    mss.company,
                    mss.city,
                    CASE mss.seller_status WHEN 1 THEN 'Active' WHEN 2 THEN 'Inactive' ELSE 'Disabled' END AS seller_status,
                    CASE mss.vacation_mode WHEN 1 THEN 'Under Vacation - Nothing Live' ELSE 'No Vacation - All Active SKUs Live' END AS vacation_mode,
                    ca.category_id,
                    cads.name AS category_name,
                    COUNT(DISTINCT ocp.product_id) as active_skus,
                    SUM(ocp.quantity * ocp.piece_in_set) as active_total_pieces
                FROM oc_ms_seller AS mss
                INNER JOIN oc_ms_product AS msp ON msp.seller_id = mss.seller_id
                INNER JOIN oc_product AS ocp ON ( ocp.product_id = msp.product_id AND
                                                ocp.quantity > 0 AND
                                                ocp.piece_in_set > 0 AND
                                                ocp.status = 1 AND
                                                ocp.stock_status_id <> 5 AND
                                                ocp.is_archived = 0 AND
                                                (ocp.date_available IS NULL OR
                                                 ocp.date_available <= CURRENT_DATE())
                                                )
                INNER JOIN oc_product_to_category AS ptoc ON ptoc.product_id = ocp.product_id
                INNER JOIN oc_category AS ca ON ca.category_id = ptoc.category_id
                INNER JOIN oc_category_description AS cads ON cads.category_id=ca.category_id AND
                                                              cads.language_id = 1
                WHERE mss.seller_status = 1
                GROUP BY
                        mss.seller_id,
                        ca.category_id

            ";
        $result = $this->db->query($sql);
        if( $result->num_rows )
        {
            $file_name = DIR_DLOAD . 'Seller-Live-Stock-Inventory-'.date("d-m-Y").'.csv';
            $fp = fopen($file_name,'w');

            //add heading line
            $data = array(
                            'Seller Id',
                            'Company',
                            'City',
                            'CategoryId',
                            'CategoryName',
                            'Unique SKU Live',
                            'Total Pieces Live'
                        );
            fputcsv($fp, $data);

            foreach ($result->rows as $key => $value)
            {
                $data = array(
                                $value['seller_id'],
                                $value['company'],
                                $value['city'],
                                $value['category_id'],
                                $value['category_name'],
                                $value['active_skus'],
                                $value['active_total_pieces']
                            );
                fputcsv($fp, $data);
            }

            $body = "Please find attached CSV file for Seller wise Live Stock Inventory.";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addAddress(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
            $mail->addAddress(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

            $mail->Subject = 'Seller Wise Live Stock Inventory - '.date('d/M/Y h:i:s');
            $mail->AddAttachment($file_name);
            $mail->msgHTML($body);
            $mail->send(0, false);

            //delete csv file after email attchement
            unlink($file_name);
        }
        echo 'Completed';
    }

    /**
     * CRON syncs if customer's sms are tracked and processed for particular criteria
     * @author Anurag Jain, 18 Apr 2019
     */
    public function syncCustomerSMSCriteriaAvailibility()
    {
        $this->load->model('short_message_logs/short_message_logs');
        $sms_model = $this->model_short_message_logs_short_message_logs;

        $aws_mysqli = mysqli_connect( RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB );
        $all_criteria_array = array( 'all_sms', 'pos', 'gst', 'account', 'credit_card', 'bounce', 'loan', 'udaan', 'paytm', 'lazypay' );

        // get last synced id from main DB
        $last_sync_id = $sms_model->getLastCustomerCriteriaSyncId();

        // get latest tracked id from AWS
        $latest_sync_id = $sms_model->getLatestCustomerCriteriaSyncId( $aws_mysqli );

        // get distinct customers to be synced from AWS DB
        $all_customers_data = $sms_model->getCustomersToSyncForSMSCriteria( $aws_mysqli, $last_sync_id, $latest_sync_id );

        // sync customers and criteria from AWS to main DB
        if ( !empty( $all_customers_data )) {

            foreach ( $all_customers_data as $key => $customer_data ) {

                $customer_id = (int) $customer_data[0];
                $customer_criteria = array_filter( explode( ',', $customer_data[1] ));

                // get old stored criteria
                $old_critera = $sms_model->getCustomerOldSyncedCriteria( $customer_id );

                /** updating criteria **/
                foreach ( $all_criteria_array as $key => $criteria_key ) {

                    if ( in_array( $criteria_key, $customer_criteria ) && !in_array( $criteria_key, $old_critera )) {

                        $sms_model->addCustomerCriteriaAvailability( $customer_id, $criteria_key );
                    }
                }
            }
        }

        // update last synced id in main DB
        $sms_model->updateCustomerSMSCriteriaLatestSyncId( $latest_sync_id );

        exit("Data sync successful !!!");
    }

    /*
        Public method to auto disabled old products and updated for archived status
        @param: days  - Before number of days order for product id
        @param: category - products in category
        @param: except seller - products from except seller ids
        Author: MSA April 2019
    */
    public function autoDisableOldProductInventory()
    {
        $days = $this->request->get['days'] ?? 90;

        $sql ="
                SELECT DISTINCT
                  p.product_id
                FROM
                  oc_product as p
                  INNER JOIN oc_ms_product as msap
                          ON msap.product_id = p.product_id
                  INNER JOIN oc_ms_seller as ocms
                          ON ocms.seller_id = msap.seller_id
                             AND ocms.seller_invoice_generate = 1
                             AND ocms.seller_id <> 78355 "; // 78355 is WSB_FR (Franchise stores)

        $category_ids = $this->request->get['category'] ?? '';
        if(!empty($category_ids)){
            $sql .= " INNER JOIN oc_product_to_category as ptoc
                        ON ptoc.product_id = p.product_id
                           AND ptoc.category_id IN (". $this->db->escape($category_ids) .") ";
        }

        // WHERE conditions
        $sql .= " WHERE p.status = 1
                        AND p.is_archived = 0
                        AND p.date_added < CURDATE() - INTERVAL ".(int)$days." DAY ";

        $except_seller = $this->request->get['except_seller'] ?? '';
        if(!empty($except_seller)){
            $sql .= " AND msap.seller_id NOT IN (". $this->db->escape($except_seller) .") ";
        }

        // Condition that there is no entry in order_product table for this product in last $days days
        $sql .= " AND NOT EXISTS( SELECT 1
                                  FROM oc_order_product oop
                                  JOIN oc_order o ON oop.order_id = o.order_id
                                                     AND o.date_added >= CURDATE() - INTERVAL ".(int)$days." DAY
                                  WHERE oop.product_id = p.product_id ) ";

        $sql .= " AND NOT EXISTS (
                              SELECT
                                    1
                                  FROM
                                    oc_seller_change_log osc
                                  WHERE
                                    osc.product_id = p.product_id
                                    AND
                                    osc.modified >= CURDATE() - INTERVAL ".(int)$days." DAY
                          )";
                          
        $result = $this->db->query($sql);

        if($result->num_rows) {

            //Update products for archived status
             $products_ids = array_column($result->rows, 'product_id');
             $products_ids = implode(',', $products_ids);
             $sql = "
                     UPDATE oc_product
                     SET
                       status          = 0,
                       is_archived     = 1,
                       archived_date   = NOW()
                     WHERE
                       product_id IN (".$products_ids.")
                    ";
             $this->db->query($sql);

            //log entry for each product
            foreach ($result->rows as $key => $value) {

                //Set Data to add into admin_change_log
                // Recording change of is_archived
                $admin_change_data                  = array();
                $admin_change_data['table_id']      = (int)$value['product_id'];
                $admin_change_data['user_id']       = 0;
                $admin_change_data['name']          = 'autoDisableOldProductInventory';
                $admin_change_data['username']      = 'Automatic Cron';
                $admin_change_data['table_name']    = 'oc_product';
                $admin_change_data['source_field']  = 'cron';
                $admin_change_data['field_name']    = 'is_archived';
                $admin_change_data['ref_url']       = 'index.php?route=cron/cron/autoDisableOldProductInventory';
                $admin_change_data['old_value']     = 0;
                $admin_change_data['new_value']     = 1;
                $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                $admin_change_data['ip_address']    = $this->request->getIpAddress;
                $admin_change_data['file_location'] = 'cron/cron/autoDisableOldProductInventory';
                $admin_change_data['user_type']     = 'System';
                //Call dynamic static function for entry into admin change log
                CommonLib::addAdminChangeLog($this->db, $admin_change_data);

                // Recording change of status
                $admin_change_data['field_name']    = 'status';
                $admin_change_data['old_value']     = 1;
                $admin_change_data['new_value']     = 0;
                //Call dynamic static function for entry into admin change log
                CommonLib::addAdminChangeLog($this->db, $admin_change_data);
            }
        }

        // Send mail internally - to inform count of products archived and disabled (even if 0 - ensures that script is running)
        $totalProductsForArachivedStatus = $result->num_rows;
        $body = "<p>Total <b>".$totalProductsForArachivedStatus."</b> products archived today</p>";
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');

		$mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
		$mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);

		$mail->Subject = $totalProductsForArachivedStatus . ' Products auto disabled and archived - '.date('d/M/Y h:i:s');
		$mail->msgHTML($body);
		$mail->send(1, false);
        echo 'Completed';
    }

    /**
     * Public method to get active seller(S) who have not any order from last 15 days
     * @author: Nishu, May 2019
    */
    public function getActiveSellerWithNoOrder(){
        $sql = "
                SELECT
                    sellers.seller_id,
                    sellers.company,
                    sellers.nickname,
                    sellers.pickup_city_code
                FROM
                (SELECT
                   DISTINCT ms.seller_id,
                           ms.company,
                           ms.nickname,
                           ms.pickup_city_code
                 FROM " . DB_PREFIX . "product p
                 JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id
                 JOIN " . DB_PREFIX . "ms_seller ms ON ms.seller_id = mp.seller_id
                 WHERE p.quantity > 0 AND
                       p.status = 1 AND
                       p.is_archived = 0 AND
                       p.stock_status_id = 7 AND
                       ms.seller_status = 1 AND
                       ms.seller_invoice_generate = 1 AND
                       ms.vacation_mode = 0 AND
                       ms.seller_id NOT IN(18102,78355)
                 ) AS sellers
                LEFT JOIN
                    (
                        SELECT
                            DISTINCT op.seller_id
                        FROM
                            ". DB_PREFIX ."order AS o
                        INNER JOIN ". DB_PREFIX ."suborder AS osub ON o.order_id = osub.order_id
                            AND osub.order_status_id > 0
                            AND osub.order_status_id != 2
                        INNER JOIN ". DB_PREFIX ."order_product AS op ON op.order_id = o.order_id
                            AND op.edit_type != 'CANCELLED_BY_CUSTOMER'
                        WHERE
                            o.store_id IN (0 , 2, 9)
                            AND o.franchise_id = 0
                            AND o.stock_transfer = 0
                            AND o.date_added > NOW() - INTERVAL 15 DAY
                    ) AS orders ON orders.seller_id = sellers.seller_id
                WHERE
                    orders.seller_id IS NULL
               ";
        $qry = $this->db->query($sql);

        if($qry->num_rows > 0){
            //Group seller(s) data pickup_city_code wise
            $grouped_data = array();
            foreach ($qry->rows as $key => $value) {
                $pickup_city_code = $value['pickup_city_code'] ?? '';
                if(!isset($grouped_data[$pickup_city_code]) ){
                    $grouped_data[$pickup_city_code] = array();
                }

                $grouped_data[$pickup_city_code][] = $value;
            }
            //All seller(s) data mail
            $this->sendMailForActiveSellerWithNoOrderFromLongTime($qry->rows, "", 1);

            //Send Mail to BD team
            if(!empty($grouped_data)){
                foreach ($grouped_data as $pickup_city_code => $value) {
                    $this->sendMailForActiveSellerWithNoOrderFromLongTime($value, $pickup_city_code, 0);
                }
            }

        }
        echo "Completed";
    }

    /**
     * @info : Private method to Send Mail to BD team
     * @param: array
     * @author: Nishu, May 2019
    */
    private function sendMailForActiveSellerWithNoOrderFromLongTime($data, $pickup_city_code, $is_all_data = 1){

        if(
            !empty($data) &&
            (
                $is_all_data == 1
                    ||
                (!empty($pickup_city_code && !empty(EMAIL_IDS['BD'][$pickup_city_code])) )
            )
        ){

            $file_name = DIR_DLOAD . 'ActiveSellerWithNoOrder-'.date("d-m-Y").'.csv';
            $fp = fopen($file_name,'w');

            $head = array(
                            'Seller ID',
                            'Company',
                            'Nickname',
                            'Pickup City Code'
                        );
            //add heading line
            fputcsv($fp, $head);

            foreach ($data as $key => $value)
            {
                $row = array(
                                $value['seller_id'],
                                $value['company'],
                                $value['nickname'],
                                $value['pickup_city_code'],
                            );
                //add SOR inventory data
                fputcsv($fp, $row);
            }

            // Send mail internal team - to inform about seller(s) who has not any any order from last 15 days
            $html = MailTemplate::sendMailForActiveSellerWithNoOrderFromLongTime($data);
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

            if($is_all_data == 1){
                $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
                $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            }else{
                if(!empty(EMAIL_IDS['BD'][$pickup_city_code])){
                    foreach (EMAIL_IDS['BD'][$pickup_city_code] as $value) {
                        $mail->addAddress($value['email_id'], $value['name']);
                    }
                }
            }

            $mail->Subject = 'Active Seller(s) having no order from Last 15 Days - '.date('d/M/Y h:i:s');
            $mail->msgHTML($html);
            $mail->AddAttachment($file_name);
            $mail->send(1, false);
            echo 'Mail Sent';

            unlink($file_name);
        }
    }

    /**
     * @info: Public method cron to inform about alert increase wsb_credit
     * Logic:-
     *      --> Current status must be  = 'ENABLED' from oc_customer_wsb_credit table
     *      --> Last limit change was done 1 month back. need to check it using credit log
     *      --> Send alert mail to Vikas accounts
     *
     * @author: Nishu. June 2019
    */
    public function alertMailForIncreaseWsbCreditLimit(){
        $sql = "
                SELECT
                    cwc.customer_id,
                    CONCAT(c.firstname, ' ', c.lastname) AS customer_name,
                    cwc.credit_limit AS current_limit,
                    DATE(MAX(cwcl.date_added)) AS last_change_date
                FROM
                    oc_customer_wsb_credit AS cwc
                        INNER JOIN
                    oc_customer AS c ON c.customer_id = cwc.customer_id
                        INNER JOIN
                    oc_customer_wsb_credit_log AS cwcl ON cwc.customer_id = cwcl.customer_id
                WHERE
                    cwc.status = 'ENABLED'
                GROUP BY
                    cwc.customer_id
                HAVING last_change_date = CURRENT_DATE() - INTERVAL 1 MONTH
               ";

        $results = $this->db->query($sql);

        if($results->num_rows > 0){
            $data = $results->rows;

            $file_name = DIR_DLOAD . 'WsbCreditChangeDoneOneMonthBack.csv';
            $fp = fopen($file_name,'w');

            $head = array(
                            'Customer ID',
                            'Customer Name',
                            'Current Limit',
                            'Last Change Date'
                        );
            //add heading line
            fputcsv($fp, $head);

            foreach ($data as $key => $value)
            {
                $row = array(
                                $value['customer_id'],
                                $value['customer_name'],
                                $value['current_limit'],
                                $value['last_change_date']
                            );
                fputcsv($fp, $row);
            }

            // Send mail internal team - to inform about
            $html = MailTemplate::alertMailForIncreaseWsbCreditLimit($data);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');

            $mail->setFrom(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
            $mail->addReplyTo(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
            $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
            $mail->addCC(EMAIL_IDS['team_credit']['email_id'], EMAIL_IDS['team_credit']['name']);

            $mail->Subject = 'WSB Credit Changes Last done 1 Month Back - '.date('d/M/Y h:i:s');
            $mail->msgHTML($html);
            $mail->AddAttachment($file_name);
            $mail->send(1, false);
            echo 'Mail Sent';

            unlink($file_name);
        }
        echo 'Completed';
    }

    /**
     * updates product hotness value in solr
     * for products modified (hotness modified) in previous day
     * @author Anurag Jain, 08 Aug 2019
     */
    public function syncProductHotnessInSolr()
    {
        //Set apache execution time limit to infinite
        ini_set('max_execution_time', 0);

        $product_hotness_obj = new ProductHotness( $this->db );

        // Delete archived products from product_hotness table
        $product_hotness_obj->deleteArchivedProductHotness();

        // get eligible products for hotness update
        $date = date( "Y-m-d", strtotime( "yesterday" ));
        $start_date = $date . " 00:00:00";
        $end_date = $date . " 23:59:59";

        $products_to_sync = $product_hotness_obj->getUpdatedHotnessProductsByDate( (string) $start_date, (string) $end_date );

        if ( !empty( $products_to_sync )) {
            $product_hotness_obj->updateProductHotnessToSolr( $products_to_sync );
        }

        echo 'Hotness sync completed !!!';
        exit();
    }


  /**
    save commission of affiliate orders
   * @param  none
   * @return none
   * @author Mahaveer Choudhary, Sept 2019
   */
  public function getAffiliateOrderCommissionAmount( )
  {
         $this->load->model('affiliate/affiliate');
         // get affiliate orders
         $order_sql = "SELECT 
                       o.order_id,
                       o.customer_id,
                       a.affiliate_id
                       FROM " . DB_PREFIX ."order o 
                        JOIN " . DB_PREFIX ."customer c ON c.customer_id = o.customer_id 
                        JOIN " . DB_PREFIX ."affiliate a ON a.code = c.referral_code 
                        JOIN " . DB_PREFIX ."customer c2 ON c2.customer_id = a.customer_id 
                        LEFT JOIN " . DB_PREFIX ."affiliate_commission ac ON ac.order_id = o.order_id 
                        WHERE 
                           o.date_added <= (CURRENT_DATE() - INTERVAL 60 DAY)
                           AND o.store_id IN (0,2,9) 
                           AND o.stock_transfer = 0 
                           AND o.franchise_id = 0 
                           AND a.type = 'REFERRAL' 
                           AND ac.order_id IS NULL 
                          AND NOT EXISTS (SELECT 1 
                                           FROM oc_suborder s 
                                           WHERE s.order_id = o.order_id 
                                           AND s.order_status_id > 0 
                                           AND s.order_status_id <> 2 
                                           AND s.delivered_date IS NULL)
                             ORDER BY NULL"; 


        $order_result = $this->db->query( $order_sql );

        $order_data = array();
        $order_ids = array();
        $affiliate_ids = array();
        
        foreach($order_result->rows as $order_row)
        {
             $order_data[$order_row['order_id']]['affiliate_id'] = $order_row['affiliate_id'];
             $order_data[$order_row['order_id']]['customer_id'] = $order_row['customer_id'];
             $order_ids[] = $order_row['order_id'];
             $affiliate_ids[] = $order_row['affiliate_id'];
        }

        $affiliate_ids = array_unique($affiliate_ids);

        //get affiliate commission rates
         $commission_rates = array();  
         if(count($affiliate_ids) > 0)
          {
            $commission_rates = $this->model_affiliate_affiliate->getAffiliateCommissionRatesForCommission( (array) $affiliate_ids );
          } 

        //get affiliate orders count numbers
         $order_counts = array();  
         if(count($affiliate_ids) > 0)
          {
            $order_counts = $this->model_affiliate_affiliate->getAffiliateCommissionOrdersCount( (array) $affiliate_ids );
          }   

           //get orders net amount
          if(count($order_ids) > 0)
          {
           $order_ids = implode(",", $order_ids);
           $sql = "SELECT 
               (SUM(sale.net_sale) - SUM(coalesce(return_tbl.net_return, 0)) ) AS net_amount,
               sale.order_id
               FROM
                   (SELECT
                      oop.order_id,
                      osub.suborder_id,
                      SUM(oop.quantity * oop.piece_in_set * (oop.price_per_piece + oop.discount_per_piece)) AS net_sale
                     FROM
                       oc_order_product oop
                     JOIN oc_suborder osub ON osub.buyer_invoice_id = oop.buyer_invoice_id
                     AND osub.invoice_no > 0
                     AND osub.delivered_date IS NOT NULL
                     WHERE
                        oop.order_id IN (".$order_ids.")
                        GROUP BY oop.order_id, osub.suborder_id 
                     ORDER BY NULL) AS sale
              LEFT JOIN
                   (SELECT
                     cn.order_id,
                     cn.suborder_id,
                     SUM(r.quantity * (oop.price_per_piece + oop.discount_per_piece)) AS net_return
                    FROM
                      oc_credit_note cn
                    JOIN oc_return r ON r.credit_note_id = cn.credit_note_id 
                    JOIN oc_order_product oop ON r.order_product_id = oop.order_product_id
                    WHERE
                       cn.order_id IN (".$order_ids.") 
                       AND cn.credit_note_status = 1
                       GROUP BY cn.order_id, cn.suborder_id) AS return_tbl 
                       ON sale.suborder_id = return_tbl.suborder_id
              GROUP BY
                sale.order_id
                ORDER BY NULL ";

         $result = $this->db->query( $sql );

         foreach($result->rows as $row)
         {
                      
            $net_order_amount  = $row['net_amount'];
            $order_id          = $row['order_id'];
            $affiliate_id      = $order_data[$order_id]['affiliate_id'];
            $customer_id       = $order_data[$order_id]['customer_id'];
            
            if(isset($order_counts[$affiliate_id]))
            {
              if(isset($order_counts[$affiliate_id][$customer_id]))
              {
                $order_counts[$affiliate_id][$customer_id]++;
              }
              else
              {
                $order_counts[$affiliate_id][$customer_id] = 1;
              }
            }
            else
            {
              $order_counts[$affiliate_id][$customer_id] = 1;
            }

            
            $current_order_count = $order_counts[$affiliate_id][$customer_id];

            //calculate affiliate commission rate
            $referral_commission =  $this->model_affiliate_affiliate->getCommissionRates( (array) $commission_rates, (int) $affiliate_id, (int) $current_order_count);

             $commission_amount = ($referral_commission/100)*$net_order_amount;
            
              $insert_query = "INSERT into ". DB_PREFIX ."affiliate_commission set
                           affiliate_id = '".(int) $affiliate_id."',
                           order_id = '".(int) $order_id."',
                           customer_id = '".(int) $customer_id."',
                           order_amount = '".$this->db->escape($net_order_amount)."',
                           commission_rate = '".(int) $referral_commission."',
                           commission = '".$this->db->escape($commission_amount)."',
                           order_count = ' ". (int) $current_order_count ." ',
                           date_added = NOW()
                           ";
              $this->db->query( $insert_query );          
                          
         }
        }
         echo "success";
         exit;  

  }

  /**
   * public method to notify returns team about pending replacement. which needs further actions
   * @author: Nishu, Sept 2019
  */
  public function notifyAboutPendingReplacements(){
    $sql = "
            SELECT 
                o.order_no,
                CONCAT(os.company, '(', os.nickname, ')') AS seller_name,
                os.city AS seller_location,
                op.model AS product_code,
                (SELECT DATE(MIN(rt1.date_added)) 
                 FROM oc_return rt1 
                 WHERE rt1.order_product_id = rt.order_product_id 
                       AND rt1.master_return_id = rt.master_return_id
                       AND rt1.return_action_id IN ( ".RETURN_ACTION_IDS['Goods_Received'].",
                                          ".RETURN_ACTION_IDS['Extra_Goods_Received'].",
                                          ".RETURN_ACTION_IDS['Short_Goods_Received']." )
                ) AS goods_received_date, 
                rt.quantity AS return_quantity,
                rt.return_id
            FROM
                oc_return AS rt
                    STRAIGHT_JOIN
                oc_return_reason AS rr ON rt.return_reason_id = rr.return_reason_id
                    AND rr.reason_type = 'REPLACEMENT' 
                    AND rr.language_id = 1
                    INNER JOIN
                oc_order_product AS op ON op.order_product_id = rt.order_product_id
                    INNER JOIN
                oc_order AS o ON o.order_id = op.order_id
                    INNER JOIN
                oc_ms_seller AS os ON os.seller_id = op.seller_id
            WHERE
                rt.active_row = 1
                    AND rt.return_action_id IN (
                                                ".RETURN_ACTION_IDS['Goods_Received'].",
                                                ".RETURN_ACTION_IDS['Extra_Goods_Received'].",
                                                ".RETURN_ACTION_IDS['Short_Goods_Received'].",
                                                ".RETURN_ACTION_IDS['Goods_To_Seller_Waiting_For_Replaement'].",
                                                ".RETURN_ACTION_IDS['Replacement_Note']."
                                              )
            
           ";
    
    $results = $this->db->query($sql);

    if($results->num_rows > 0){

        $data = $results->rows;

        $file_name = DIR_DLOAD . 'PendingReplacements.csv';
        $fp = fopen($file_name,'w');

        $head = array(
                        'Order ID',
                        'Seller Name',
                        'Seller Location',
                        'Product Code',
                        'Goods Received Date From Buyer',
                        'Pcs Count'
                    );
        //add heading line
        fputcsv($fp, $head);

        foreach ($data as $key => $value)
        {
            $row = array(
                            $value['order_no'],
                            $value['seller_name'],
                            $value['seller_location'],
                            $value['product_code'],
                            $value['goods_received_date'],
                            $value['return_quantity']
                        );
            fputcsv($fp, $row);
        }

        // Send mail internal team - to inform about
        $html = MailTemplate::alertMailForPendingReplacements($data);
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');

        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);

        $mail->Subject = 'Pending Replacement(s) - '.date('d/M/Y h:i:s');
        $mail->msgHTML($html);
        $mail->AddAttachment($file_name);
        $mail->send(1, false);
        echo 'Mail Sent';

        unlink($file_name);
    }
   
    echo 'Completed';
  }


}
