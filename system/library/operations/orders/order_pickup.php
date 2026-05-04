<?php

/**
 * order pickup class for editing order product status
 * Mostly Contains Static functions
 *
 */
class OrderPickup {

    public $registry;
    public $db;
    public $config;
    public $request;
    public $error_msg = array(
        'YES' => 'Seller(s) invoice not generated',
        'SELLER_NOT_SUPPLIED' => '',
        'SELLER_LATER_DISPATCH' => 'Some products are marked as seller later dispatch, Clear then first'
    );

    public function __construct($registry) {

        if (method_exists($registry, 'get')) {
            $this->registry = $registry;
            $this->db = $registry->get('db');
            $this->config = $registry->get('config');
            $this->request = $registry->get('request');
        } else {
            $this->registry = $registry;
            $this->db = $registry->db;
            $this->config = $registry->config;
            $this->request = $registry->request;
        }
    }

    /**
     * order pickup for editing order product status.]
     * @param $order product id   original order snapshot
     * @param $status
     */
    public function changeOrderProductPickupStatus($getData = null) {

        $flag_hist_update = true;
        $partial_receive = false;
        if (!empty($getData) && isset($getData['data']['order_product_id'])) {

            $error_arr = $this->validatePickupStatus($getData['data']['order_product_id']);
            if (!empty($error_arr)) {
                return $error_arr;
            }

            $data = array();
            $data['order_product_id'] = $getData['data']['order_product_id'];
            $data['status'] = $getData['data']['issue_box'] . ' ' . $getData['data']['received_status'];
            $data['user_id'] = $getData['user_id'];
            $data['user_name'] = $getData['user_name'];
            $data['user_type'] = $getData['user_type'];
            $data['seller_id'] = $getData['data']['seller_id_no'];
            $data['seller_invoice_id'] = $getData['data']['seller_invoice_id'];
            $history = $this->getLastPickupHistory($data);
        }
        $data['edit_history'] = array(
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'user_type' => $data['user_type'],
            'user_ip' => $this->request->getIpAddress,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'date_added' => date('d-m-Y H:i:s'),
            'comment' => $getData['data']['issue_box'] . ' SELLER_NOT_SUPPLIED'
        );
        
        $order_product_details = $this->getOrderProductDetails($data['order_product_id']);
        
        if ($getData['data']['received_status'] == 'partial_receive') {
            $partial_receive = true;
            $data['changes'] = array(
                'quantity' => array(
                    'new' => $getData['total_piece_new'],
                    'old' => $getData['total_piece_old']
                )
            );

            $data['edit_type'] = 'SELLER_NOT_SUPPLIED';
            $data['quantity'] = $getData['total_piece_new'];
            //Code changes by Nilesh as per new requirement - for generic function splitOrderProduct will split accordingly
            $data['old_edit_type'] = 'SELLER_PARTIAL';
            OrderEdit::splitOrderProduct($this->db, $data['order_product_id'], $data);
            
            $getData['data']['received_status'] = 'Received';
            $flag_hist_update = false;
        } elseif ($getData['data']['received_status'] == 'Not_Given') {
            $data['edit_type'] = array('SELLER_NOT_SUPPLIED' =>
                array('product_id' =>
                    $getData['data']['product_id']));
            $order_store_obj = new OrderStores();
            $order_store_obj->updateEditTypeBySeller($this->db, $data['order_product_id'], $data, $data['seller_id']);
            $flag_hist_update = false;
        } else {
            $flag_hist_update = true;
        }
        $order_id = $getData['data']['order_id'];
        $suborder_id = $getData['data']['suborder_id'];

        //Update order and suborder total
        OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);
        
        //Check for invalid seller invoice ids
        $no_items_in_invoice_hence_invalid_marked = false;
        $invalid_invoice_arr = array();
        $sql = "SELECT osi.seller_invoice_id,
                        osi.order_id,
                        osi.suborder_id
                FROM oc_seller_invoice osi 
                WHERE NOT EXISTS 
                        (SELECT 1 
                            FROM oc_order_product oop 
                            WHERE osi.seller_invoice_id = oop.seller_invoice_id) AND
                      osi.trxn_done='NOT_DONE' AND
                      osi.order_id = '" . (int) $order_id . "' AND
                      osi.suborder_id = '" . $this->db->escape($suborder_id) . "'";
        $invalid_invoice_query = $this->db->query($sql);
        if ($invalid_invoice_query->num_rows) {
            $invalid_invoice_arr = $invalid_invoice_query->rows;
        }
        if (!empty($invalid_invoice_arr)) {
            foreach ($invalid_invoice_arr as $key => $value) {
                $this->db->query("UPDATE " . DB_PREFIX . "seller_invoice SET 
                                    trxn_done = 'INVALID_NO_GOODS'
                                WHERE seller_invoice_id='" . (int) $value['seller_invoice_id'] . "'");
            }
            $no_items_in_invoice_hence_invalid_marked = true;
        }        
        
        /*
         * Seller Invoice pdf attachment code to mail is commented for now
          $seller_invoice = array();
          $seller_invoice['seller_invoice_id'] = (int) $getData['data']['seller_invoice_id'];
          $seller_invoice['gst'] = 1;
          $seller_invoice = serialize($seller_invoice);
          $secureFileDload = new SecureFileDownload($this->registry);
          $seller_inv_dload_link['url'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false);
         * 
         */

        if ($partial_receive || $getData['data']['received_status'] != 'Received') {
            $order_no = explode('-', $suborder_id)[0];
            $data_arr = array(
                'order_id' => $order_id,
                'order_no' => $order_no,
                'suborder_id' => $suborder_id,
                'seller_invoice_id' => $order_product_details['seller_invoice_id'],
                'seller_nickname' => $order_product_details['nickname'],
                'seller_company' => $order_product_details['company'],
                'old_pickup_status' => $order_product_details['pickup_status'],
                'new_pickup_status' => $getData['data']['received_status'],
                'seller_id' => $order_product_details['seller_id'],
                'edit_type' => $order_product_details['edit_type'],
                'sku' => $order_product_details['seller_sku'],
                'total_qty' => $order_product_details['total_qty'],
                'new_qty' => (!empty($getData['total_piece_new']) ? $getData['total_piece_new'] : 'N/A'),
                'old_qty' => (!empty($getData['total_piece_old']) ? $getData['total_piece_old'] : 'N/A'),
                'issue_box' => (!empty($getData['data']['issue_box']) ? $getData['data']['issue_box'] : ''),
                'seller_invoice_no'=> $order_product_details['org_seller_invoice_no'],
                'invoice_dated'=> $order_product_details['invoice_dated'],
                'no_items_in_invoice_hence_invalid_marked'=> $no_items_in_invoice_hence_invalid_marked
            );

            $this->sendMailsForChangePickupStatus($data_arr);
        }
        $sql = "UPDATE " . DB_PREFIX . "order_product SET 
                    pickup_status         = '" . $this->db->escape($getData['data']['received_status']) . "',
                    pickup_last_modified = NOW() , 
                    pickup_history        = '" . $this->db->escape($history) . "'
                WHERE order_product_id='" . (int) $getData['data']['order_product_id'] . "' 
               ";

        if ($this->db->query($sql)) {
            return 1;
        } else {
            $error['error'] = "Unable to change status";
            return $error['error'];
        }
    }


    public function changePickupHistory($getData = null) {
        $flag_hist_update = true;
     
        if (!empty($getData) && isset($getData['data']['order_product_id'])) {
            $data = array();
            $data['order_product_id'] = $getData['data']['order_product_id'];
            $data['status']           = $getData['data']['received_status'];
            $data['user_id']          = $getData['user_id'];
            $data['user_name']        = $getData['user_name'];
            $data['user_type']        = $getData['user_type'];
            $data['seller_id']        = $getData['data']['seller_id'];
            $history = $this->getLastPickupHistory($data);
        }

        $sql = "UPDATE " . DB_PREFIX . "order_product SET 
                    pickup_status         = '" . $this->db->escape($getData['data']['received_status']) . "',
                    pickup_last_modified = NOW() , 
                    pickup_history        = '" . $this->db->escape($history) . "'
                WHERE order_product_id='" . (int) $getData['data']['order_product_id'] . "' 
               ";
        if ($this->db->query($sql)) {
            return 1;
        } else {
            $error['error'] = "Unable to change status";
            return $error['error'];
        }
    }



    /**
     * get last pickup history
     * @param $order product id
     * @param $status
     */
    private function getLastPickupHistory($getData = null) {

        $data = array();

        $sql = "SELECT pickup_history
                    FROM " . DB_PREFIX . "order_product
                    WHERE order_product_id='" . (int) $getData['order_product_id'] . "' 
                    AND pickup_history!='' ";

        $query = $this->db->query($sql);
        $result = $query->row;

        if (!empty($result) && isset($result['pickup_history'])) {

            $data = unserialize($result['pickup_history']);

            $history = array(
                'user_id' => $getData['user_id'],
                'user_name' => $getData['user_name'],
                'user_type' => $getData['user_type'],
                'user_ip' => $this->request->getIpAddress,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'date_added' => date('d-m-Y H:i:s'),
                'comment' => $getData['status']
            );

            array_push($data, $history);
        } else {

            $history = array(
                'user_id' => $getData['user_id'],
                'user_name' => $getData['user_name'],
                'user_type' => $getData['user_type'],
                'user_ip' => $this->request->getIpAddress,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'date_added' => date('d-m-Y H:i:s'),
                'comment' => $getData['status']
            );

            $data[] = $history;
        }
        $data = serialize($data);

        return $data;
    }

    public function getOrderProductDetails($order_product_id) {
        $sql = "SELECT oop.edit_type,
                       oop.seller_sku,
                       (oop.quantity * oop.piece_in_set) AS total_qty,
                       oop.pickup_status,
                       oop.seller_invoice_id,
                       oop.seller_id,
                       os.order_status_id,
                       ms.seller_invoice_generate,
                       ms.nickname, 
                       ms.company,
                       CONCAT(osi.seller_invoice_prefix,osi.seller_invoice_no) AS org_seller_invoice_no,
                       osi.date_added as invoice_dated
                FROM oc_order_product oop INNER JOIN 
                     oc_suborder os ON oop.order_id = os.order_id INNER JOIN 
                     oc_ms_seller ms ON oop.seller_id = ms.seller_id LEFT JOIN
                     oc_seller_invoice osi ON oop.seller_invoice_id = osi.seller_invoice_id  
                WHERE oop.order_product_id = '" . (int) $order_product_id . "' AND
                      oop.suborder_id=os.suborder_id";
        $query = $this->db->query($sql);
        return $query->row;
    }

    /**
     * Check for pickup buttons
     * @param $order product id
     * @param $status
     */
    private function validatePickupStatus($order_product_id) {
        $error_array = array();
        $order_info = $this->getOrderProductDetails($order_product_id);

        if ($order_info['order_status_id'] == 2) {
            $error_array['error'] = "This order is cancelled.";
        } elseif ($order_info['pickup_status'] != 'Not_Given' && $order_info['pickup_status'] != 'Picked_Up') {
            $error_array['error'] = "Pickup status is already marked";
        } elseif ($order_info['edit_type'] == 'YES' && $order_info['seller_invoice_generate'] == 1) {
            $error_array['error'] = "Seller " . $order_info['nickname'] . " not generated invoice.";
        } elseif (($order_info['edit_type'] == 'SELLER_APPROVED' || $order_info['edit_type'] == 'SELLER_PARTIAL') && $order_info['seller_invoice_id'] == 0 && $order_info['seller_invoice_generate'] != 0) {
            $error_array['error'] = "Seller Invoice Id for seller " . $order_info['nickname'] . " not assigned.";
        }
        return $error_array;
    }

    /**
     * Sending mails
     * @param $data array of mail details
     */
    public function sendMailsForChangePickupStatus($data) {

        $html = MailTemplate::changePickupstatusMailTemplate($data);
        $subject = 'Invoice ' . $data['seller_invoice_no'] . ' for Order No. ' . $data['order_no'] . ' EDITED - ' . date('d-m-Y H:i:s');
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        ;
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom('sellers@wholesalebox.in', 'Sellers WholesaleBox');
        $mail->addReplyTo('sellers@wholesalebox.in', 'Sellers WholesaleBox');
        $seller_emails = SellerInfo::getSellerEmails($this->db, $data['seller_id'], true);
        if (!empty($seller_emails)) {
            foreach ($seller_emails as $seller_email) {
                $mail->addAddress($seller_email);
            }
        }
        $mail->addCC('accounts@wholesalebox.in', 'Accounts WholesaleBox');
        $mail->addCC('operations@wholesalebox.in', 'Operations WholesaleBox');
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
    }

}

?>