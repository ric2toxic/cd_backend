<?php

class BuyerInvoice {

    private $_registry;
    private $_db;
    private $_load;
    private $_currency;
    private $_currency_code;
    private $_currency_value;
    private $_is_api = false;
    public  $gst;
    private $_order_id;
    private $_suborder_id;
    private $_selector = array(
        'order' => array(
            'select' => array(
                'order_id',
                'order_no',
                'currency_code',
                'currency_value',
                'live_currency_conversion_rate',
                'code_version',
                'payment_firstname',
                'payment_lastname',
                'payment_city',
                'payment_custom_field',
                'payment_company',
                'payment_address_1',
                'payment_address_2',
                'payment_postcode',
                'payment_zone',
                'payment_country',
                'telephone',
                'alternate_contact_number',
                /* Shiping address for GST START */
                'shipping_firstname',
                'shipping_lastname',
                'shipping_company',
                'shipping_address_1',
                'shipping_address_2',
                'shipping_city',
                'shipping_country',
                'shipping_zone_id',
                'payment_zone_id',
                /* Shiping address for GST END */
                'shipping_postcode',
                'date_added',
                'payment_code',
                'shipping_charge',
                'shipping_zone',
                'gst_number',
                'email',
                'firstname',
                'lastname',
                'customer_id',
                'store_id'
            )
        ),
        'suborder' => array(
            'select' => array(
                'suborder_id',
                'invoice_no',
                'invoice_prefix',
                'invoice_date',
                'date_added',
                'courier_partner',
                'cform_submit',
                'refundable_cform',
                'cst_with_cform',
                'tracking_no',
                'shipping_charge',
                'shipping_method',
                'custom_totals',
                'order_status_id',
                'gst'
            )
        )
    );
    private $_order_info = array();
    private $_get_full_path = false;
    private $_file_type = 'b2b';
    private $_b2c = false;
    private $_show_image = false;
    private $_product_link = false;
    private $_format;
    private $_show_categories = false;
    private $_product_info = array();
    private $_amount_formc = 0;
    private $_show_store_pickup_notation = false;
    private $_products = array();
    private $_totals = array();
    private $_skip_cst = false;
    private $_seller_info = array();
    private $_fields = array();
    private $_group_by_tax = true;
    private $_get_all_product = false;
    private $_is_igst = 1;
    private $_only_html = false;
    private $_total_breakup = true;
    private $_cancelled_flag = false;
    private $_for_cn = false;
    private $_is_receipt_voucher_validity = false;
    private $_images  = array();
    private $_minimum = array();

    public function __construct($registry, $filename = '') {
        if (!empty($filename)) {
            $filename = base64_decode($filename);
            $filename = unserialize($filename);
            $this->_order_id = $filename['order_id'];
            $this->_suborder_id = $filename['suborder_id'];
        }

        if (method_exists($registry, 'get')) {
            $this->_registry = $registry;
            $this->_db = $registry->get('db');
            $this->_load = $registry->get('load');
            $this->_currency = $registry->get('currency');
            $this->_config = $registry->get('config');
        } else {
            $this->_registry = $registry;
            $this->_db = $registry->db;
            $this->_load = $registry->load;
            $this->_currency = $registry->currency;
            $this->_config = $registry->config;
        }
    }

    public function setOrderInfoByOrderIdSuborderId($order_id, $suborder_id) {
        $this->_order_id    = (int)$order_id;
        $this->_suborder_id = $suborder_id;
        $this->_order_info  = OrderInfo::getOrderInfo($this->_db, $order_id, $suborder_id, $this->_selector);

        $suborder_detail    = $this->_order_info['suborder'][$suborder_id];
        $order_status_id    = (int)$suborder_detail['order_status_id'];

        $this->_cancelled_flag = ($order_status_id === ORDER_STATUS['Canceled']) ? true : false;
    }

    public function setOrderInfo($order_info) {

        if ( !empty($order_info['order']) || !empty($order_info['suborder']) ) {
            
            //Reset index of array
            reset($order_info['suborder']);

            $suborder_id = key($order_info['suborder']);

            $this->_order_id    = $order_info['order']['order_id'];
            $this->_suborder_id = $suborder_id;

            $suborder_detail    = $order_info['suborder'][$suborder_id];
            $order_status_id    = (int)$suborder_detail['order_status_id'];

            $this->_cancelled_flag = ($order_status_id === ORDER_STATUS['Canceled']) ? true : false;

            $this->_order_info = $order_info;

            $selector = $this->_selector;

            $given_keys = array_keys($order_info['order']);
            $this->_selector['order']['select'] = array_filter($selector['order']['select'], function($field) use ($given_keys) {
                return !in_array($field, $given_keys);
            });
            

            reset($order_info['suborder']);
            $given_keys = array_keys($order_info['suborder'][key($order_info['suborder'])]);
            $this->_selector['suborder']['select'] = array_filter($selector['suborder']['select'], function($field) use ($given_keys) {
                return !in_array($field, $given_keys);
            });
            
            if (!empty($this->_selector['order']['select']) || !empty($this->_selector['suborder']['select'])) {
                $order_info = OrderInfo::getOrderInfo($this->_db, $this->_order_id, $this->_suborder_id, $this->_selector);
                $this->_order_info['order'] = array_merge($this->_order_info['order'], $order_info['order']);
                
                $this->_order_info['suborder'][$this->_suborder_id] = array_merge($this->_order_info['suborder'][$this->_suborder_id], $order_info['suborder'][$this->_suborder_id]);
            }

        } else {
            throw new Exception("Order id and Suborder Id are mandatory in order info.");
        }
    }

    public function setOptions($flag_name, $flag_value) {
        if (property_exists($this, '_' . $flag_name)) {
            $this->{'_' . $flag_name} = $flag_value;
        } else {
            throw new Exception("Class BuyerInvoice doesn't contain $flag_name");
        }
    }

    public function getFile() {
        $this->setOrderInfoByOrderIdSuborderId($this->_order_id, $this->_suborder_id);
        $this->checkVatOrGST();
        $this->checkReceiptVoucherValidity();
        if ($this->gst){
            $pdf = $this->generateOrderInvoiceHtmlForGST();
        } else{
            $pdf = $this->generateOrderInvoiceHtml();
        }
        $file_name = $this->saveBuyerInvoiceAsPdf($pdf['html'], $pdf['invoice_no']);
        return $file_name;
    }

    public function checkVatOrGST() {
        if (empty($this->_order_info)) {
            $this->setOrderInfoByOrderIdSuborderId($this->_order_id, $this->_suborder_id);
        } else if (empty($this->_order_info['order']) && empty($this->_order_info['suborder'])) {
            throw new Exception(
            "Order info is missing.
                 You can use BuyerInvoice::setOrderInfo() or
                 BuyerInvoice::setOrderInfoByOrderIdSuborderId()"
            );
        } else {
            // Setting GST flag
            $this->gst = $this->_order_info['suborder'][$this->_suborder_id]['gst'];
        }
    }

    public function checkReceiptVoucherValidity() {
        if (!empty($this->_order_info['suborder'][$this->_suborder_id]['invoice_no']) && $this->_order_info['suborder'][$this->_suborder_id]['invoice_no'] > 0 && !empty($this->_order_info['suborder'][$this->_suborder_id]['invoice_date']) && date('Y-m-d', strtotime($this->_order_info['suborder'][$this->_suborder_id]['invoice_date'])) <= date('Y-m-d', strtotime(RECEIPT_VOUCHER_VALIDITY_DATE)) && $this->gst) {
            $this->_is_receipt_voucher_validity = true;
        }
    }

    public function getTotalCreditNoteForOrder($order_id) {
        $credits = $this->getCreditNoteForOrder($order_id);
        return (float) array_sum($credits);
    }

    public function getCreditNoteForOrder($order_id) {
        $selector = array('suborder' => array('select' => array('suborder_id')));
        $order_info = OrderInfo::getOrderInfo($this->_db, $order_id, '', $selector);
        $suborder_ids = array_keys($order_info['suborder']);
        $credit = array();
        foreach ($suborder_ids as $suborder_id) {
            $credit[$suborder_id] = $this->getCreditNoteForSuborder($order_id, $suborder_id);
        }
        return $credit;
    }

    public function getCreditNoteForSuborder($order_id, $suborder_id) {
        require_once(DIR_SYSTEM . 'library/total/credit.php');
        $credit_object = new Credit($this->_registry);
        $credit_object->suborder = true;
        $credit_object->suborder_id = $suborder_id;
        $credit_object->order_id = $order_id;
        $total_data = array();
        $total = $taxes = 0;
        $credit_object->getTotal($total_data, $total, $taxes);
        return !empty($total_data[0]['value']) ?
                (string) round(
                        $total_data[0]['value'], (int) $this->_currency->getDecimalPlace()
                ) : 0;
    }

    public function getCustomerId() {
        return OrderInfo::getCustomerIdFromOrder($this->_db, $this->_order_id);
    }

    /**
     * [getSellerDetails - This function gives the seller(wholesalebox) details on the basis of the seller_invoice
     *                   vat_input_rule_id. We are assuming all the seller_invoices are generated ]
     * @param  [type] $order_id    [description]
     * @param  [type] $suborder_id [description]
     * @return array() -  [array containing seller details]
     */
    public function getSellerDetails($order_id, $suborder_id) {
        $sql = "SELECT vat_input_rule_id, purchase_firm_id, seller_invoice_meta ";
        $sql .= "FROM " . DB_PREFIX . "seller_invoice ";
        $sql .= "INNER JOIN " . DB_PREFIX . "vat_input_rules ON ( vat_input_rule_id = rule_id ) ";
        $sql .= "WHERE order_id    = '" . (int) $order_id . "' AND ";
        $sql .= "suborder_id = '" . $this->_db->escape($suborder_id) . "' ";
        $result = $this->_db->query($sql);

        if ($result->num_rows > 0) {
            $seller_invoice_meta = array_combine(
                    array_column($result->rows, 'purchase_firm_id'), $result->rows
            );
            if (count($seller_invoice_meta) == 1) {
                $purchase_firm_id = key($seller_invoice_meta);
                $vat_input_rule_id = $seller_invoice_meta[$purchase_firm_id]['vat_input_rule_id'];
                $seller_invoice_meta = unserialize($seller_invoice_meta[$purchase_firm_id]['seller_invoice_meta']);
                return array($vat_input_rule_id => $seller_invoice_meta['buyer_data']);
            } else {
                throw new Exception("Sellers vat_input_rule_id should be same for all invoices of a suborder. Fix the seller vat_input_rule_id first.", 1);
            }
        } else {
            return false;
        }
    }

    public function getPurchaseFirmAddress($order_id, $suborder_id) {
        $sql = "SELECT vat_input_rule_id, purchase_firm_id, seller_invoice_meta ";
        $sql .= "FROM " . DB_PREFIX . "seller_invoice osi ";
        $sql .= "INNER JOIN " . DB_PREFIX . "vat_input_rules vir ON ( osi.vat_input_rule_id = vir.rule_id ) ";
        $sql .= "WHERE order_id    = '" . (int) $order_id . "' AND ";
        $sql .= "suborder_id = '" . $this->_db->escape($suborder_id) . "' ";
        $result = $this->_db->query($sql);
        if ($result->num_rows > 0) {
            $seller_invoice_meta = array_combine(
                    array_column($result->rows, 'purchase_firm_id'), $result->rows
            );
            if (count($seller_invoice_meta) == 1) {
                $purchase_firm_id = key($seller_invoice_meta);
                $seller_invoice_meta = unserialize($seller_invoice_meta[$purchase_firm_id]['seller_invoice_meta']);
                return array($purchase_firm_id => $seller_invoice_meta['buyer_data']);
            } else {
                throw new Exception("Purchase Frim id for all seller of a suborder should be same. Fix the seller vat_input_rule_id first.", 1);
            }
        }
    }

    public function generateOrderInvoiceHtml() {
        $order = $this->_order_info['order'];
        $order['suborder'] = $this->_order_info['suborder'][$this->_suborder_id];
        $invoice_name = $order['suborder']['invoice_prefix'] . $order['suborder']['invoice_no'];
        $invoice_no = $order['suborder']['invoice_no'];
        $invoice_date = date("d-m-Y", strtotime($order['suborder']['invoice_date']));
        $order_no = $order['order_no'];
        $date_added = date("d-m-Y", strtotime($order['suborder']['date_added']));
        $payment_city = !empty($order['payment_city']) ? $order['payment_city'] : '';
        $custom_field = !empty($order['payment_custom_field']) ? unserialize($order['payment_custom_field']) : '';
        if (is_array($custom_field) && !empty($custom_field[1])) {
            $tin_number = trim($custom_field[1]);
        }
        $this->_file_type = strtolower(trim($this->_file_type));
        $buyers_details = '';
        if (empty($tin_number) || $this->_file_type == 'b2c') {
            $this->_b2c = true;
            $buyers_details = '<b>' . trim($order['payment_firstname'] . ' ' . $order['payment_lastname']) . '</b><br>';
            $buyers_details .= !empty(trim($order['payment_company'])) ? '<b>c/o ' . trim($order['payment_company']) . '</b><br>' : '';
        } else {
            $buyers_details = !empty(trim($order['payment_company'])) ? '<b>' . trim($order['payment_company']) . '</b><br>' : '';
            $this->_b2c = false;
        }

        $buyers_details .= !empty(trim($order['payment_address_1'])) ? trim($order['payment_address_1']) . ', ' : '';
        $buyers_details .= !empty(trim($order['payment_address_2'])) ? trim($order['payment_address_2']) . '<br>' : '<br>';
        $buyers_details .= '<b>' . trim($payment_city . ' - ' . $order['payment_postcode']) . '</b><br>';
        $buyers_details .= !empty(trim($order['payment_zone'])) ? trim($order['payment_zone']) . ', ' : '';
        $buyers_details .= !empty(trim($order['payment_country'])) ? trim($order['payment_country']) . '<br>' : '';
        $buyers_details .= !empty(trim($order['telephone'])) ? 'Tel: <b>' . trim($order['telephone']) . '</b><br>' : '';

        if (!$this->_b2c) {
            $buyers_details .= '<br>Buyer Tin No: <b>' . $tin_number . '</b><br>';
        }
        try {
            $seller_details = $this->getSellerDetails($this->_order_id, $this->_suborder_id);
            if ($seller_details) {
                $seller_details = $seller_details[key($seller_details)];
            }
            // else{
            //     exit( "Error: Seller Inoive is not generated for this order." );
            // }
            $this->_seller_info = $seller_details;
        } catch (Exception $e) {
            exit($e);
        }
        $tbl = '<table width="100%" border="1" cellspacing="0" cellpadding="4">';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center">All Subject to Jaipur Jurisdiction</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="40%" rowspan="4">';
        if (!empty($seller_details)) {
            $tbl .= '<b>' . $seller_details['company'] . '</b><br>';
            $tbl .= '<b>' . $seller_details['address1'] . ',';
            if (!empty($seller_details['address2'])) {
                $tbl .= $seller_details['address2'];
            }
            $tbl .= '</b><br>';
            $tbl .= '<b>' . $seller_details['city'] . ', ' . $seller_details['state'] . '</b><br>';
            $tbl .= '<b>' . $seller_details['country'] . '</b><br>';
            $tbl .= 'Seller Tin No: <b>' . $seller_details['tin'] . '</b><br><br>';
        } else {
            $tbl .= '<b>WHOLESALEBOX INTERNET PRIVATE LIMITED</b><br>';
        }
        $tbl .= 'Email: <b>operations@wholesalebox.in</b><br>';
        $tbl .= 'Helpline No: <b> (+91) 141 - 4049163 </b><br>';
        //$tbl .= 'Whatsapp: <b>+91 8696491521</b><br>';
        $tbl .= '<b>https://www.wholesalebox.in</b><br>';
        $tbl .= '</td>';
        $tbl .= '<td width="20%" >Original</td>';
        $tbl .= '<td width="20%" rowspan="2">Duplicate for Sellers</td>';
        $tbl .= '<td width="20%" rowspan="2">Triplicate for Transporter</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="20%" >For Buyers </td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%">Inv No: <b>' . ($invoice_no > 0 ? $invoice_name : '') . '</b></td>';
        $tbl .= '<td width="30%">Dated: <b>' . ($invoice_no > 0 ? $invoice_date : '') . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="60%" >' . $buyers_details . ' </td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="40%" >Booked From: <b>' . $payment_city . '</b>  </td>';
        $tbl .= '<td width="30%" >Order No: <b>' . $order_no . '</b> </td>';
        $tbl .= '<td width="30%" >Dated: <b>' . $date_added . '</b></td>';
        $tbl .= '</tr>';

        $payable_total = 0;
        $products = $this->getProductsArrayBySuborderId($this->_order_id, $this->_suborder_id);
        $this->_order_info['products'] = $products;
        $totals = $this->getTotals($this->_order_id, $this->_suborder_id);
        $tbl .= $this->getProductsHtml($products, $totals);
        $payable_total = $totals['net_amount']['value'];
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left">';
        $tbl .= '<table cellspacing="0" cellpadding="4">';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="60%">';
        $tbl .= 'Our ' . $seller_details['state'] . ' VAT Reg. No: <b>' . $seller_details['tin'] . '</b><br>';
        $tbl .= 'Our Central Sales Tax Reg. No: <b>' . $seller_details['tin'] . '</b><br><br><br>';
        $tbl .= ' </td>';
        $tbl .= '<td width="40%" align="center">E & EO <br> For ';
        if (!empty($seller_details)) {
            $tbl .= strtoupper(trim($seller_details['company']));
        } else {
            $tbl .= 'WHOLESALEBOX INTERNET PRIVATE LIMITED';
        }
        $tbl .= '<br><br><br></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left">';
        if ($this->_amount_formc > 0) {
            $tbl .= '<b style="font-size:12px">Your Form C Amount: ' . $this->_currency->format($this->_amount_formc, 'INR', 1) . '</b><br>';
        }
        $tbl .= 'Any additional Octroi, duty, special taxes etc. will be borne by buyer<br>';
        if ($this->_show_store_pickup_notation) {
            $tbl .= '* Immediate Delivery done from Store';
        }
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left">';
        $tbl .= '<br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';

        if ($this->_b2c) {
            $tbl .= '<tr>';
            $tbl .= '<td width="100%" align="left">';
            $tbl .= '<b>CUSTOMER DECLARATION --</b><br>';
            $tbl .= 'I hereby confirm that the above said product(s) are
                                    being purchased for my internal / personal consumption and not for re-sale.';
            $tbl .= '</td>';
            $tbl .= '</tr>';
        }

        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center">';
        $tbl .= '<b>THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE ANY SIGNATURE</b><br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl_head = "";

        if (strtolower(trim($order['suborder']['courier_partner'])) == 'gati') {
            if (!empty($order['shipping_postcode'])) {
                $sql = "SELECT ou FROM " . DB_PREFIX . "gati_pincodes
                        WHERE pincode = '" . $this->_db->escape($order['shipping_postcode']) . "'";
                $result = $this->_db->query($sql);
                $code = '';
                if ($result->num_rows) {
                    $code = $result->row['ou'];
                }
                $tbl_head .= '<table ><tbody><tr>';
                $tbl_head .= '<td width="50%" align="left"><h3>' . $code . '</h3></td>';
                $tbl_head .= '<td width="50%" align="right"><h3>Docket No: ' . $order['suborder']['tracking_no'] . '</h3></td>';
                $tbl_head .= '</tr></tbody></table>';
            }
        }

        $payment_code = strtolower(trim($order['payment_code']));
        if (
            ($payment_code == 'cod' || in_array($payment_code, CREDIT_PARTIAL_COD_CODES) ) 
            && $payable_total > 0
        ) {
            $formatted_payable_total = $this->_currency->format($payable_total, $order['currency_code'], $order['currency_value'], true);
            $tbl_head .= '<h2 align="center">CASH ON DELIVERY - ' . $formatted_payable_total . '</h2>';
        } else {
            $tbl_head .= '<h2 align="center">PREPAID</h2>';
        }
        if ($this->_b2c) {
            $tbl_head .= '<h4 align="center">RETAIL INVOICE' . ( $invoice_no > 0 ? ' - ' . $invoice_name : '' ) . '</h4>';
        } else {
            $tbl_head .= '<h4 align="center">INVOICE' . ( $invoice_no > 0 ? ' - ' . $invoice_name : '' ) . '</h4>';
        }

        $tbl = $tbl_head . $tbl;
        return array('html' => $tbl, 'invoice_no' => $invoice_name);
    }

    public function generateOrderInvoiceHtmlForGST() {
        $order = $this->_order_info['order'];
        $order['suborder'] = $this->_order_info['suborder'][$this->_suborder_id];
        $gst_state_code_billed = '';
        $gst_state_code_shipped = '';
        $gst_state_code_seller = '';
        $invoice_name = $order['suborder']['invoice_prefix'] . $order['suborder']['invoice_no'];
        $invoice_no = $order['suborder']['invoice_no'];
        $invoice_date = date("d-m-Y", strtotime($order['suborder']['invoice_date']));
        $order_no = $order['order_no'];
        $date_added = date("d-m-Y", strtotime($order['suborder']['date_added']));
        $payment_city = !empty($order['payment_city']) ? $order['payment_city'] : '';
        $shipping_city = !empty($order['shipping_city']) ? $order['shipping_city'] : '';
        $custom_field = !empty($order['payment_custom_field']) ? unserialize($order['payment_custom_field']) : '';
        $tin_number = $order['gst_number'];
        $this->_file_type = strtolower(trim($this->_file_type));
        $buyers_details = '';
        $buyers_details_ship = '';

        $buyers_details_ship .= '<span align="center"><b>Shipped To</b></span><br>';
        $buyers_details .= '<span align="center"><b>Billed To</b></span><br>';
        if (empty($tin_number) || $this->_file_type == 'b2c') {
            $this->_b2c = true;
            $buyers_details_ship .= '<b>' . trim($order['shipping_firstname'] . ' ' . $order['shipping_lastname']) . '</b><br>';
            $buyers_details_ship .= !empty(trim($order['shipping_company'])) ? '<b>c/o ' . trim($order['shipping_company']) . '</b><br>' : '';
            $buyers_details .= '<b>' . trim($order['payment_firstname'] . ' ' . $order['payment_lastname']) . '</b><br>';
            $buyers_details .= !empty(trim($order['payment_company'])) ? '<b>c/o ' . trim($order['payment_company']) . '</b><br>' : '';
        } else {
            $buyers_details_ship .= !empty(trim($order['shipping_company'])) ? '<b>' . trim($order['shipping_company']) . '</b><br>' : '';
            $buyers_details .= !empty(trim($order['payment_company'])) ? '<b>' . trim($order['payment_company']) . '</b><br>' : '';
            $this->_b2c = false;
        }

        /*Manage customer alternate contact numbers */
         $phones = array();
         if(!empty($order['telephone'])) {
            $phones = array( $order['telephone'] );
         }
         if(!empty($order['alternate_contact_number'])) {
            $alternate_contact_number = json_decode($order['alternate_contact_number'],true);
            $phones = array_merge($alternate_contact_number, $phones);
         }
         $phones = implode(', ', $phones);
        /*Manage customer alternate contact numbers */

        $buyers_details .= !empty(trim($order['payment_address_1'])) ? trim($order['payment_address_1']) . ', ' : '';
        $buyers_details .= !empty(trim($order['payment_address_2'])) ? trim($order['payment_address_2']) . '<br>' : '<br>';
        $buyers_details .= '<b>' . trim($payment_city . ' - ' . $order['payment_postcode']) . '</b><br>';
        $buyers_details .= !empty(trim($order['payment_zone'])) ? trim($order['payment_zone']) . ', ' : '';
        $buyers_details .= !empty(trim($order['payment_country'])) ? trim($order['payment_country']) . '<br>' : '';
        //$buyers_details .= !empty(trim($order['telephone'])) ? 'Tel: <b>' . trim($order['telephone']) . '</b><br>' : '';
        $buyers_details .= !empty(trim($phones)) ? 'Phone(s): <b>' . trim($phones) . '</b><br>' : '';
        $buyers_details_ship .= !empty(trim($order['shipping_address_1'])) ? trim($order['shipping_address_1']) . ', ' : '';
        $buyers_details_ship .= !empty(trim($order['shipping_address_2'])) ? trim($order['shipping_address_2']) . '<br>' : '<br>';
        $buyers_details_ship .= '<b>' . trim($shipping_city . ' - ' . $order['shipping_postcode']) . '</b><br>';
        $buyers_details_ship .= !empty(trim($order['shipping_zone'])) ? trim($order['shipping_zone']) . ', ' : '';
        $buyers_details_ship .= !empty(trim($order['payment_country'])) ? trim($order['payment_country']) . '<br>' : '';
        //$buyers_details_ship .= !empty(trim($order['telephone'])) ? 'Tel: <b>' . trim($order['telephone']) . '</b><br>' : '';
        $buyers_details_ship .= !empty(trim($phones)) ? 'Phone(s): <b>' . trim($phones) . '</b><br>' : '';


        if (!$this->_b2c) {
            $buyers_details .= '<br>GSTIN / UIN: <b>' . $order['gst_number'] . '</b><br>';
            $buyers_details_ship .= '<br>GSTIN / UIN: <b>' . $order['gst_number'] . '</b><br>';
        }
        try {
            $seller_details = $this->getSellerDetails($this->_order_id, $this->_suborder_id);
            if ($seller_details) {
                $seller_details = $seller_details[key($seller_details)];
            }
            // else{
            //     exit( "Error: Seller Inoive is not generated for this order." );
            // }
            $this->_seller_info = $seller_details;
        } catch (Exception $e) {
            exit($e);
        }
        $zone_id_arr = $order['shipping_zone_id'] . ',' . $order['payment_zone_id'];
        $zone_id_arr = rtrim($zone_id_arr, ',');
        $this->_load->model('localisation/zone', 'frontend');
        if (method_exists($this->_registry, 'get')) {
            $model_zone_gstcode = $this->_registry->get('frontend_model_localisation_zone');
        } else {
            $model_zone_gstcode = $this->_registry->frontend_model_localisation_zone;
        }
        $get_zone_state_code = $model_zone_gstcode->getZoneGSTStateCode($zone_id_arr);
        foreach ($get_zone_state_code as $key => $value) {
            if ($value['zone_id'] == $order['payment_zone_id'])
                $gst_state_code_billed = $value['gst_state_code'];
            if ($value['zone_id'] == $order['shipping_zone_id'])
                $gst_state_code_shipped = $value['gst_state_code'];
        }
        $buyers_details .= '<span align="right"><b>State Code: ' . $gst_state_code_billed . '</b></span>';
        $buyers_details_ship .= '<span align="right"><b>State Code: ' . $gst_state_code_shipped . '</b></span>';

        $tbl = '<table width="100%" border="1" cellspacing="0" cellpadding="4" >';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center" colspan="10">All Subject to Jaipur Jurisdiction</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="40%" rowspan="4" colspan="2" >';
        if (!empty($seller_details)) {
            $tbl .= '<b>' . $seller_details['company'] . '</b><br>';
            $tbl .= '<b>' . $seller_details['address1'] . ',';
            if (!empty($seller_details['address2'])) {
                $tbl .= $seller_details['address2'] . '</b><br>';
            }
            $tbl .= '<b>' . $seller_details['city'] . ' - ' . $seller_details['pincode'] . ',<br>' . $seller_details['state'] . '</b><br>';
            $tbl .= '<b>' . $seller_details['country'] . '</b><br>';
            $tbl .= 'GSTIN / UIN: <b>' . (!empty($seller_details['tin']) ? $seller_details['tin'] : "") . '</b><br><br>';
        } else {
            $tbl .= '<b>WHOLESALEBOX INTERNET PRIVATE LIMITED</b><br>';
        }
        $tbl .= 'Email: <b>operations@wholesalebox.in</b><br>';
        $tbl .= 'Helpline No: <b> (+91) 141 - 4049163 </b><br>';
        //$tbl .= 'Whatsapp: <b>+91 8696491521</b><br>';
        $tbl .= '<b>https://www.wholesalebox.in</b><br>';
        $tbl .= '<br><br><span align="right"><b>State Code: ' . (!empty($seller_details['state_code'][0]['gst_state_code']) ? $seller_details['state_code'][0]['gst_state_code'] : "") . '</b></span>';
        $tbl .= '</td>';
        $tbl .= '<td width="20%" colspan="3" >Original For Buyers</td>';
        $tbl .= '<td width="20%" colspan="3">Duplicate for Sellers</td>';
        $tbl .= '<td width="20%" colspan="3">Triplicate for Transporter</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" colspan="3">PO No: <b>' . ((isset($order['suborder']['suborder_id']) && $order['suborder']['suborder_id'] != '') ? $order['suborder']['suborder_id'] : '') . '</b></td>';
        $tbl .= '<td width="30%" colspan="6">PO Date: <b>' . ((isset($order['suborder']['date_added']) && $order['suborder']['date_added'] != '') ? date("d-m-Y", strtotime($order['suborder']['date_added'])) : '') . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" colspan="3">Inv. No: <b>' . ($invoice_no > 0 ? $invoice_name : '') . '</b></td>';
        $tbl .= '<td width="30%" colspan="6">Inv. Date: <b>' . ($invoice_no > 0 ? $invoice_date : '') . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" colspan="3" >' . $buyers_details . ' </td>';
        $tbl .= '<td width="30%" colspan="6" >' . $buyers_details_ship . ' </td>';
        $tbl .= '</tr>';

        $payable_total = 0;
        $products = $this->getProductsArrayBySuborderId($this->_order_id, $this->_suborder_id);
        $this->_order_info['products'] = $products;
        $totals = $this->getTotals($this->_order_id, $this->_suborder_id);
        $amount_in_words = '';
        if (!empty($totals) && isset($totals['amount_in_word'])) {
            $amount_in_words = $totals['amount_in_word']['value'];
            unset($totals['amount_in_word']);
        }
        $tbl .= $this->getProductsHtmlForGST($products, $totals);
        $payable_total = $totals['net_amount']['value'];
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center" colspan="10">' . ($amount_in_words) . '</td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td width="100%" align="left" colspan="10">';
        $tbl .= '<table cellspacing="0" cellpadding="4" width="100%" >';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $advance_arr = AdvanceVoucher::getAdvanceVouchersByData($this->_db, array('order_id' => $this->_order_id, 'suborder_id' => $this->_suborder_id));
        $tbl .= '<td width="50%">';

        if (!empty($advance_arr) && $this->_is_receipt_voucher_validity) {
            $advance_arr_count = count($advance_arr);
            $count = 1;
            $table_close = false;
            foreach ($advance_arr as $key => $value) {
                if ($value['value'] > 0) {
                    if ($count == 1) {
                        $tbl .= '<table cellspacing="0" cellpadding="4" width="100%" border="1">
                        <thead>
                            <tr>
                                <th colspan="2" align="center"><b>Receipt Voucher Details</b></th>
                            </tr>
                            <tr>
                                <th><b>Receipt No.</b></th>
                                <th><b>Date</b></th>                                
                            </tr>
                        </thead>
                        <tbody>';
                        $table_close = true;
                    }
                    $tbl .= '<tr>
                            <td>' . $value['advance_voucher_no'] . '</td>
                            <td>' . date("d-m-Y", strtotime($value['advance_voucher_date'])) . '</td>
                        </tr>';
                }
                $count++;
            }
            if ($table_close) {
                $tbl .= '</tbody>
                            </table>';
            }
        }
        $tbl .= '</td>';
        $tbl .= '<td width="50%" align="center">E & OE <br><br><br> For <br>';
        if (!empty($seller_details)) {
            $tbl .= strtoupper(trim($seller_details['company']));
        } else {
            $tbl .= 'WHOLESALEBOX INTERNET PRIVATE LIMITED';
        }
        $tbl .= '<br></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left" colspan="2">';
        
        if ($this->_amount_formc > 0) {
            $tbl .= '<b style="font-size:12px">Your Form C Amount: ' . $this->_currency->format($this->_amount_formc, 'INR', 1) . '</b><br>';
        }

        $tbl .= '<table cellspacing="0" cellpadding="4" width="100%" border="0">';
            $tbl .= '<tr>';
                $tbl .= '<td width="50%">';

                    if ($this->_amount_formc > 0) {
                        $tbl .= '<b style="font-size:12px">Your Form C Amount: ' . $this->_currency->format($this->_amount_formc, 'INR', 1) . '</b><br>';
                    }

                    $tbl .= '<table cellspacing="0" cellpadding="4" width="100%">';
                        $tbl .= '<tr>';
                            $tbl .= '<td>';
                            $tbl .= 'i. Certified that the particulars given above are true and correct.<br>';
                            $tbl .= 'ii. Any additional Octroi, duty, special taxes etc. will be borne by buyer.<br>';

                        if ($order['store_id'] == INTERNATIONAL_STORE_ID) {
                            $tbl .= 'iii. If there will be any custom duty on this order then it will be customer\'s responsibility to clear goods from custom. For any help, please WhatsApp on +91 9116134795<br>';
                            $tbl .= 'iv. Payment Terms :';
                        }else{
                            $tbl .= 'iii. Payment Terms :';    
                        }
                        $tbl .= '</td>';    
                    $tbl .= '</tr>';    
                    $tbl .= '<tr>';
                       $tbl .= '<td>COD  : At the time of Delivery<br>';
                       $tbl .= 'PREPAID  : At the time of placing the Order<br>';
                       $tbl .= 'CREDIT   : Pay Later Scheme (*)</td>';
                    $tbl .= '</tr>';   

                    $tbl .= '</table>';

                $tbl .= '</td>';
                $tbl .= '<td width="50%" valign="top">';
                       $tbl .= '<table cellspacing="0" cellpadding="4" width="100%" border="1">';
                            $tbl .= '<tr><td style="text-align:left;">';
                            $tbl .= '&nbsp;&nbsp;BANK DETAILS :<br>
                                    ICICI Bank<br>
                                    Wholesalebox Internet Pvt Ltd<br>
                                    A/c No: 674605500343<br>
                                    IFSC code: ICIC0006746<br>
                                    Bank: ICICI Banipark Branch (Branch code: 6746),<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    Jaipur, Rajasthan';
                            $tbl .= '</td></tr>';
                       $tbl .= '</table>';
                $tbl .= '</td>';
            $tbl .= '</tr>';
        $tbl .= '</table>';
        
        $tbl .= '<table cellspacing="0" cellpadding="4" width="100%">'; 
         $tbl .= '<tr>';
           $tbl .= '<td>* Upto 30 days interest free credit by Wholesalebox<br>';
           $tbl .= '** Outstanding amount on 30th day will be financed by channel partner on behalf of customer</td>';
        $tbl .= '</tr>'; 

        if ($this->_show_store_pickup_notation) {
            $tbl .= '<tr>';
               $tbl .= '<td>* Immediate Delivery done from Store</td>';
            $tbl .= '</tr>'; 
        }

       $tbl .= '</table>';

        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left" colspan="2">';
        $tbl .= '<br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        if ($this->_b2c) {
            $tbl .= '<tr>';
            $tbl .= '<td width="100%" align="left" colspan="2">';
            $tbl .= '<b>CUSTOMER DECLARATION --</b><br>';
            $tbl .= 'Customer has declared in the purchase order that he is not registered under GST as non applicability of the law.';
            $tbl .= '</td>';
            $tbl .= '</tr>';
        }
        $tbl .= '<tr><td>&nbsp;</td></tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center" colspan="2">';
        $tbl .= '<h2>30 Days FREE Credit Facility available for SHOPS with good banking transactions,<br>Call / Whatsapp +91 8239778680</h2>';
        $tbl .= '<b>THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE ANY SIGNATURE</b><br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl_head = "";

        if (strtolower(trim($order['suborder']['courier_partner'])) == 'gati') {
            if (!empty($order['shipping_postcode'])) {
                $sql = "SELECT ou FROM " . DB_PREFIX . "gati_pincodes
                        WHERE pincode = '" . $this->_db->escape($order['shipping_postcode']) . "'";
                $result = $this->_db->query($sql);
                $code = '';
                if ($result->num_rows) {
                    $code = $result->row['ou'];
                }
                $tbl_head .= '<table><tbody><tr>';
                $tbl_head .= '<td width="50%" align="left"><h3>' . $code . '</h3></td>';
                $tbl_head .= '<td width="50%" align="right"><h3>Docket No: ' . $order['suborder']['tracking_no'] . '</h3></td>';
                $tbl_head .= '</tr></tbody></table>';
            }
        }

        $payment_code = strtolower(trim($order['payment_code']));
        if ( $payment_code == 'cod' && $payable_total > 0) {
            
            $formatted_payable_total = $this->_currency->format($payable_total, $order['currency_code'], $order['currency_value'], true);
            $tbl_head .= '<h2 align="center">CASH ON DELIVERY - ' . $formatted_payable_total . '</h2>';
        
        } else if (in_array($payment_code, CREDIT_PARTIAL_COD_CODES) && $payable_total > 0) { 

            $formatted_payable_total = $this->_currency->format($payable_total, $order['currency_code'], $order['currency_value'], true);
            $tbl_head .= '<h2 align="center">CREDIT INVOICE</h2>';
            $tbl_head .= '<h3 align="center">Partial Cash On Delivery - ' . $formatted_payable_total . '</h3>';

        } else if (in_array($payment_code, CREDIT_PAYMENT_CODES)) {
            $tbl_head .= '<h2 align="center">CREDIT INVOICE</h2>';

        } else {
            $tbl_head .= '<h2 align="center">PREPAID</h2>';
        }

        if ($this->_b2c) {
            $tbl_head .= '<h4 align="center">B2C TAX INVOICE' . ( $invoice_no > 0 ? ' - ' . $invoice_name : '' ) . '</h4>';
        } else {
            $tbl_head .= '<h4 align="center">B2B TAX INVOICE' . ( $invoice_no > 0 ? ' - ' . $invoice_name : '' ) . '</h4>';
        }
        $tbl = $tbl_head . $tbl;
        return array('html' => $tbl, 'invoice_no' => $invoice_name);
    }

    public function getProductsHtml($products, $order_totals) {
        $order = $this->_order_info['order'];
        $i = 1;
        $total_quantity = 0;
        $total_pieces = 0;
        $tbl = '';
        if ($this->_is_api) {
            $tbl .= '<table class="table table-bordered" border="1"><tbody>';
        }
        $tbl .= '<tr>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 8) . '%" align="center"><b>S. No.</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 38 : 41) . '%" align="center"><b>SKU</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%" align="center"><b>Comment</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%" align="center"><b>Sets</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%" align="center"><b>Pcs / Set</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] ? 6 : 7) . '%" align="center"><b>Pcs</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="center"><b>Rate / Pc</b></td>';
        if ($order['code_version'] != 1.0) {
            $tbl .= '<td width="8%" align="center"><b>Dis. / Pc</b></td>';
        }
        $tbl .= '<td width="8%" align="center"><b>Tax Rate</b></td>';
        $tbl .= '<td width="14%" align="center"><b>Amount (Ex. Tax)</b></td>';
        $tbl .= '</tr>';

        foreach ($products as $product) {
            $tbl .= '<tr class="' . ( $product['store_pickup'] ? ' bg-info ' : '' ) .
                    ( ($product['edit_type'] != 'YES' && $product['edit_type'] != 'SELLER_APPROVED' && $product['edit_type'] != 'SELLER_PARTIAL') ? ' bg-warning ' : '' ) . '">';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 8) . '%" align="center">';
            $tbl .= ( $product['store_pickup'] ? '* ' : '' ) . $i;
            $tbl .= '</td>';
            if ($product['store_pickup']) {
                $this->_show_store_pickup_notation = $product['store_pickup'];
            }
            if ($this->_show_image && !empty($product['image'])) {
                $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 40 : 41) . '%" align="left">';
                $tbl .= '<img src="' . $product['image'] . '"
                              width="' . $product['width'] . 'px"
                              height="' . $product['height'] . 'px" />
                         <br>';
                if ($this->_product_link) {
                    $link = $this->_registry->url->link(
                            $this->_product_link, '&product_id=' . $product['product_id'], 'SSL'
                    );
                    $tbl .= '<strong> <a href="' . $link . '" target = "_blank">';
                    $tbl .= '<b>' . $product['model'] . '</b>';
                    $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                    $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                    $tbl .= '</a></strong>';
                    if ($this->_only_html) {
                        $tbl .= !empty($product['customer_comment']) ? ' <br><label style="color: #115376; font-size: 12px;font-weight: normal;"> ' . $product['customer_comment'] . '</label>' : '';
                    }
                } else {
                    $tbl .= '<strong>';
                    $tbl .= '<b>' . $product['model'] . '</b>';
                    $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                    $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                    $tbl .= '</strong>';
                    if ($this->_only_html) {
                        $tbl .= !empty($product['customer_comment']) ? ' <br><label style="color: #115376; font-size: 12px;font-weight: normal;"> ' . $product['customer_comment'] . '</label>' : '';
                    }
                }
                $tbl .= '</td>';
            } else {
                $tbl .= '<td width="' . ($order['code_version'] > 1.0 ? 38 : 41) . '%" align="left">';
                $tbl .= '<b>' . $product['model'] . '</b>';
                $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                if ($this->_only_html) {
                    $tbl .= !empty($product['customer_comment']) ? ' <br><label style="color: #115376; font-size: 12px; font-weight: normal;"> ' . $product['customer_comment'] . '</label>' : '';
                }
                $tbl .= '</td>';
            }
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%" align="center">' . $product['comment'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%" align="center">' . $product['quantity'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%" align="center">' . $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 7) . '%" align="center">' . (float) $product['quantity'] * (float) $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $product['price_per_piece'], $order['currency_code'], $order['currency_value'], false
            );
            $tbl .= '</td>';
            if ($order['code_version'] != 1.0) {
                $tbl .= '<td width="8%" align="right">';
                $tbl .= $this->_currency->format(
                        (float) $product['discount_per_piece'], $order['currency_code'], $order['currency_value'], false
                );
                $tbl .= '</td>';
            }
            $ext = str_replace('P', '%', substr($product['output_tax_rates'], -1));
            $ext = empty($ext) ? '%' : $ext;
            $tax_rate = number_format((float) str_replace('P', '', $product['output_tax_rates']), 2, '.', '');
            $tbl .= '<td width="8%" align="center">' . $tax_rate . ' ' . $ext . '</td>';
            $tbl .= '<td width="14%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $product['quantity'] *
                    (float) $product['piece_in_set'] *
                    (float) $product['price_per_piece'], $order['currency_code'], $order['currency_value'], false
            );
            $tbl .= '</td>';
            $tbl .= '</tr>';
            $i++;
            $total_quantity += $product['quantity'];
            $total_pieces += (float) $product['quantity'] * (float) $product['piece_in_set'];
        }

        if ($this->_total_breakup) {
            $total_row = '<tr>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 8) . '%%"></td>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 38 : 41) . '%%" align="right"><b>%s</b></td>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%%" align="center"></td>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%%" align="center">%s</td>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%%" align="center"></td>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 7) . '%%" align="center">%s</td>';
            $total_row .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%%" align="center"></td>';
            if ($order['code_version'] != 1.0) {
                $total_row .= '<td width="8%%" align="center"></td>';
            }
            $total_row .= '<td width="8%%" align="center"></td>';
            $total_row .= '<td width="14%%" align="right">%s</td>';
            $total_row .= '</tr>';
            foreach ($order_totals as $key => $total) {
                // Html Row for order['total']

                if (empty($total['title']) || empty($total['value'])) {
                    continue;
                }
                if ($key == 'advance') {
                    $tbl .= sprintf($total_row, '', '', '', '');
                }
                if ($key != 'sub_total') {
                    $total_quantity = $total_pieces = '';
                }
                if ($key == 'paycharge' && !empty($total['breakup'])) {
                    foreach ($total['breakup'] as $dis_rate => $paycharge_arr) {
                        $dis_percent = $total['title'] . " (" . (-1) * $dis_rate . "%)";
                        $tbl .= sprintf(
                                $total_row, $dis_percent, $total_quantity, $total_pieces, $this->_currency->format((float) $paycharge_arr['discount'], $order['currency_code'], 1)
                        );
                    }
                } else {
                    $tbl .= sprintf(
                            $total_row, $total['title'], $total_quantity, $total_pieces, $this->_currency->format((float) $total['value'], $order['currency_code'], 1)
                    );
                }

                if ($key == 'net_amount') {
                    $payable_total = $total['value'];
                }
            }
        }
        if ($this->_is_api) {
            $tbl .= '</tbody></table>';
        }
        return $tbl;
    }

    public function getProductsHtmlForSuborderBreak($products) {
        $order = $this->_order_info['order'];
        $i = 1;
        $total_quantity = 0;
        $total_pieces = 0;
        $seller_ids = array_column($products, 'seller_id');
        $seller_name = array_column($products, 'seller_name');
        $seller_arr = array_combine($seller_ids, $seller_name);
        $seller_checkbox = '<tr><td colspan="11">';
        if (!empty($seller_arr)) {
            foreach ($seller_arr as $seller_id => $seller_name) {
                $seller_checkbox .= ' <input type="checkbox" class="check_seller" name="check_seller' . $seller_id . '" value="' . $seller_id . '"> <span>' . $seller_name . '</span> ';
            }
        }
        $seller_checkbox .= ' </td></tr>';
        $tbl = '';
        $tbl .= $seller_checkbox;
        $tbl .= '<tr>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 8) . '%" align="center"></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 38 : 41) . '%" align="center"><b>Seller Name</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 38 : 41) . '%" align="center"><b>SKU</b></td>';
        //$tbl .=        '<td width="41%" align="center"><b>IMAGE</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%" align="center"><b>Sets</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%" align="center"><b>Pcs / Set</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] ? 6 : 7) . '%" align="center"><b>Pcs</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] ? 6 : 7) . '%" align="center"><b>Weight/Pcs (Kg.)</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="center"><b>Rate / Pc</b></td>';
        if ($order['code_version'] != 1.0) {
            $tbl .= '<td width="8%" align="center"><b>Dis. / Pc</b></td>';
        }
        $tbl .= '<td width="8%" align="center"><b>Tax Rate</b></td>';
        //$tbl .=        '<td width="8%" align="center"><b>Tax</b></td>';
        $tbl .= '<td width="14%" align="center"><b>Amount (Ex. Tax)</b></td>';
        $tbl .= '</tr>';

        foreach ($products as $product) {

            $tbl .= '<tr>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 8) . '%" align="center">';
            $tbl .= '<input type="checkbox" value="' . $product['order_product_id'] . '" class="order-product" dir="' . $product['product_id'] . '" data-seller="' . $product['seller_id'] . '">';
            $tbl .= '</td>';
            $tbl .= '<td width="' . ($order['code_version'] > 1.0 ? 38 : 41) . '%" align="left">' . $product['seller_name'] . '</td>';
            if ($product['store_pickup']) {
                $this->_show_store_pickup_notation = $product['store_pickup'];
            }
            //$tbl .=      '<td width="41%" align="center">'.$product['model'].'</td>';
            if ($this->_show_image && !empty($product['image'])) {
                $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 40 : 41) . '%" align="left">';
                $tbl .= '<img src="' . $product['image'] . '"
                              width="' . $product['width'] . 'px"
                              height="' . $product['height'] . 'px" />
                         <br>';
                if ($this->_product_link) {
                    $link = $this->_registry->url->link(
                            $this->_product_link, '&product_id=' . $product['product_id'], 'SSL'
                    );
                    $tbl .= '<strong> <a href="' . $link . '" target = "_blank">';
                    $tbl .= '<b>' . $product['model'] . '</b>';
                    $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                    $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                    $tbl .= '</a></strong>';
                } else {
                    $tbl .= '<strong>';
                    $tbl .= '<b>' . $product['model'] . '</b>';
                    $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                    $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                    $tbl .= '</strong>';
                    if ($this->_only_html) {
                        $tbl .= !empty($product['customer_comment']) ? ' <br><label style="color: #115376; font-size: 12px;font-weight: normal;"> ' . $product['customer_comment'] . '</label>' : '';
                    }
                }
                $tbl .= '</td>';
            } else {
                $tbl .= '<td width="' . ($order['code_version'] > 1.0 ? 38 : 41) . '%" align="left">';
                $tbl .= '<b>' . $product['model'] . '</b>';
                $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                if ($this->_only_html) {
                    $tbl .= !empty($product['customer_comment']) ? ' <br><label style="color: #115376; font-size: 12px; font-weight: normal;"> ' . $product['customer_comment'] . '</label>' : '';
                }
                $tbl .= '</td>';
            }
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 5 : 7) . '%" align="center">' . $product['quantity'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%" align="center">' . $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 7) . '%" align="center">' . (float) $product['quantity'] * (float) $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 7) . '%" align="center">' . (float) $product['weight_per_piece'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $product['price_per_piece'], $order['currency_code'], $order['currency_value'], false
            );
            $tbl .= '</td>';
            if ($order['code_version'] != 1.0) {
                $tbl .= '<td width="8%" align="right">';
                $tbl .= $this->_currency->format(
                        (float) $product['discount_per_piece'], $order['currency_code'], $order['currency_value'], false
                );
                $tbl .= '</td>';
            }
            $ext = str_replace('P', '%', substr($product['output_tax_rates'], -1));
            $ext = empty($ext) ? '%' : $ext;
            $tax_rate = number_format((float) str_replace('P', '', $product['output_tax_rates']), 2, '.', '');
            $tbl .= '<td width="8%" align="center">' . $tax_rate . ' ' . $ext . '</td>';
            //$tbl .=        '<td width="8%" align="right">'.number_format((float)$product['tax'], 2, '.', '').'</td>';
            $tbl .= '<td width="14%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $product['quantity'] *
                    (float) $product['piece_in_set'] *
                    (float) $product['price_per_piece'], $order['currency_code'], $order['currency_value'], false
            );
            $tbl .= '</td>';
            $tbl .= '</tr>';
            $i++;
            $total_quantity += $product['quantity'];
            $total_pieces += (float) $product['quantity'] * (float) $product['piece_in_set'];
        }
        return $tbl;
    }

    public function getProductsHtmlForGST($products, $order_totals) {
        $order = $this->_order_info['order'];
        $order['suborder'] = $this->_order_info['suborder'][$this->_suborder_id];
        $seller_state = '';
        $seller_zone_id = 0;
        $seller_state_code = '';
        if (!empty($this->_seller_info)) {
            $seller_state = $this->_seller_info['state'];
            $seller_state_code = $this->_seller_info['state_code'][0]['gst_state_code'];
        } else {
            $seller_details = $this->getSellerDetails($this->_order_id, $this->_suborder_id);
            if (!empty($seller_details)) {
                $seller_details = $seller_details[key($seller_details)];
                $seller_state = $seller_details['state'];
                $seller_zone_id = $seller_details['zone_id'];
                $seller_state_code = $seller_details['state_code'][0]['gst_state_code'];
                $this->_seller_info = $seller_details;
            }
        }
        $i = 1;
        $total_quantity = 0;
        $total_pieces = 0;
        $tbl = '';
        $tbl .= '<tr>';
        $tbl .= '<td width="6%" align="center"><b>S. No.</b></td>';
        $tbl .= '<td width="21%" align="center"><b>Item Description</b></td>';
        $tbl .= '<td width="15%" align="center"><b>HSN</b></td>';
        //$tbl .=        '<td width="41%" align="center"><b>IMAGE</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] ? 6 : 7) . '%" align="center"><b>Qty</b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%" align="center"><b>Unit of Meas. </b></td>';
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="center"><b>Rate / Pc</b></td>';
        if ($order['code_version'] != 1.0) {
            $tbl .= '<td width="7%" align="center"><b>Dis. / Pc</b></td>';
        }
        $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="center"><b>Product Value</b></td>';
        $tbl .= '<td width="8%" align="center"><b>GST Rate</b></td>';
        $tbl .= '<td width="14%" align="center"><b>Amount (Inc. Tax)</b></td>';
        $tbl .= '</tr>';
        $hsn_cal_arr = array();
        $sgst_cgst_tax = 0;
        $igst_tax = 0;
        $shipping_per_peice = $this->getProductShipping($products);
        $shipping_tax_wise = $this->setShippingTaxWise($shipping_per_peice);


        $this->_load->model('localisation/zone', 'frontend');
        if (method_exists($this->_registry, 'get')) {
            $model_zone_gstcode = $this->_registry->get('frontend_model_localisation_zone');
        } else {
            $model_zone_gstcode = $this->_registry->frontend_model_localisation_zone;
        }
        $get_zone_state_code = $model_zone_gstcode->getZoneGSTStateCode($order['payment_zone_id']);

        if ((int) $seller_state_code === (int) $get_zone_state_code[0]['gst_state_code']) {
            $this->_is_igst = 0;
        }

        foreach ($products as $product) {
            $tbl .= '<tr class="' . ( $product['store_pickup'] ? ' bg-info ' : '' ) . '">';
            $tbl .= '<td width="6%" align="center">';
            $tbl .= ( $product['store_pickup'] ? '* ' : '' ) . $i;
            $tbl .= '</td>';
            if ($product['store_pickup']) {
                $this->_show_store_pickup_notation = $product['store_pickup'];
            }
            //$tbl .=      '<td width="41%" align="center">'.$product['model'].'</td>';
            if ($this->_show_image && !empty($product['image'])) {
                $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 40 : 41) . '%" align="left">';
                $tbl .= '<img src="' . $product['image'] . '"
                              width="' . $product['width'] . 'px"
                              height="' . $product['height'] . 'px" />
                         <br>';
                if ($this->_product_link) {
                    $link = $this->_registry->url->link(
                            $this->_product_link, '&product_id=' . $product['product_id'], 'SSL'
                    );
                    $tbl .= '<strong> <a href="' . $link . '" target = "_blank">';
                    $tbl .= '<b>' . $product['model'] . '</b>';
                    $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                    $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                    $tbl .= '</a></strong>';
                } else {
                    $tbl .= '<strong>';
                    $tbl .= '<b>' . $product['model'] . '</b>';
                    $tbl .= !empty($product['name']) ? '<br><i style="font-size:10px">' . $product['name'] . '</i>' : '';
                    $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                    $tbl .= '</strong>';
                }
                $tbl .= '</td>';
            } else {
                $tbl .= '<td width="21%" align="left">';
                $tbl .= '<b>' . $product['model'] . '</b>';
                $tbl .= !empty($product['name']) ? '<br><i style="font-size:9px">' . $product['name'] . '</i>' : '';
                $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
                $tbl .= '</td>';
                $tbl .= '<td width="15%" align="left">';
                $tbl .= '<b>' . $product['hsn_code'] . '</b>';
                $tbl .= '</td>';
            }
            $piece = (float) $product['quantity'] * (float) $product['piece_in_set'];
            $rate_per_piece = (float) $product['price_per_piece'];
            $discount_per_piece = (float) $product['discount_per_piece'];
            $product_value = (float) $piece * (float) ($rate_per_piece + ($discount_per_piece));
            $sgst_cgst_igst_tax = (float) $product_value * ((float) $product['output_tax_rates'] / 100);

            if ($this->_is_igst) {
                $igst_tax = $sgst_cgst_igst_tax;
            } else {
                $sgst_cgst_tax = $sgst_cgst_igst_tax / 2;
            }

            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 6 : 7) . '%" align="center">' . (float) $product['quantity'] * (float) $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 7 : 7) . '%" align="center">Piece</td>';
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $product['price_per_piece'], $order['currency_code'], $order['currency_value'], false
            );
            $tbl .= '</td>';
            if ($order['code_version'] != 1.0) {
                $tbl .= '<td width="7%" align="right">';
                $tbl .= $this->_currency->format(
                        (float) $product['discount_per_piece'], $order['currency_code'], $order['currency_value'], false
                );
                $tbl .= '</td>';
            }
            $tbl .= '<td width="' . ($order['code_version'] != 1.0 ? 8 : 8) . '%" align="center">' . $this->_currency->format(
                            (float) $product_value, $order['currency_code'], $order['currency_value'], false
                    ) . '</td>';

            $tax_rate = number_format((float) $product['output_tax_rates'], 2);
            $tbl .= '<td width="8%" align="center">' . $tax_rate . ' %' . '</td>';
            //$tbl .=        '<td width="8%" align="right">'.number_format((float)$product['tax'], 2, '.', '').'</td>';
            $amount_inc_tax = (float) $product_value * (1 + (float) $product['output_tax_rates'] / 100);
            $tbl .= '<td width="14%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $amount_inc_tax, $order['currency_code'], $order['currency_value'], false
            );
            $tbl .= '</td>';
            $tbl .= '</tr>';
            $i++;
            $total_quantity += $product['quantity'];
            $total_pieces += (float) $product['quantity'] * (float) $product['piece_in_set'];
        }
        $hsn_cal_arr = self::getHSNCodeWiseDetails($products, $seller_state_code, $get_zone_state_code[0]['gst_state_code']);
        /*
         * Advance Code
         */
        $advance_arr = $this->getAdvance();
        $advance_exc_tax_total = 0;
        $advance_tax_val_total = 0;
        $advance_tax_igst_total = 0;
        $advance_tax_cgst_sgst_total = 0;
        $advance_cgst_sgst = array();
        $advance_igst = array();
        if (!empty($advance_arr)) {
            $advance_arr_tax_rate_wise = $this->setAdvance($products, $advance_arr);
            foreach ($advance_arr_tax_rate_wise as $key => $value) {
                $advance_exc_tax = (float) $value['advance_val_tax_wise_group'] - (float) $value['advance_tax_val_tax_wise_group'];
                $advance_exc_tax_total += $advance_exc_tax;
                $advance_tax_val_total += (float) $value['advance_tax_val_tax_wise_group'];
                if ($this->_is_igst) {
                    $advance_igst[$key] = (float) $value['advance_tax_val_tax_wise_group'];
                    $advance_tax_igst_total += (float) $value['advance_tax_val_tax_wise_group'];
                } else {
                    $advance_cgst_sgst[$key] = (float) $value['advance_tax_val_tax_wise_group'] / 2;
                    $advance_tax_cgst_sgst_total += (float) $value['advance_tax_val_tax_wise_group'];
                }
                /*
                 * Code not required un-necessary showing advances in buyer invoice
                 * already showing in HSN summary table
                 * 
                 * $tbl .= '<tr>';
                  $tbl .= '<td width="6%" align="center">' . $i . '</td>';

                  $tbl .= '<td width="21%" align="left"><b>Less: Advance Received</b></td>';
                  $tbl .= '<td width="15%" align="left"></td>';
                  $tbl .= '<td width="6%" align="center"></td>';
                  $tbl .= '<td width="7%" align="center"></td>';
                  $tbl .= '<td width="8%" align="right"></td>';
                  $tbl .= '<td width="7%" align="right"></td>';

                  $tbl .= '<td width="8%" align="center"> - ' . $this->_currency->format(
                  (float) $advance_exc_tax, $order['currency_code'], $order['currency_value'], false
                  ) . '</td>';

                  $tbl .= '<td width="8%" align="center">' . $key . ' %' . '</td>';
                  $tbl .= '<td width="14%" align="right">';
                  $tbl .= $this->_currency->format(
                  (float) - $value['advance_val_tax_wise_group'], $order['currency_code'], $order['currency_value'], false
                  );
                  $tbl .= '</td>';
                  $tbl .= '</tr>';
                  $i++;
                 * 
                 */
            }
        }
        /*
         * Please Do not delete this code. use for different tax rates
         * 
         *
          foreach ($shipping_tax_wise as $key => $value) {
          $tax_rate = number_format((float) $key, 2);
          $shipping_exc_tax = (float) $value / ( 1 + (float) $tax_rate / 100);
          $tbl .= '<tr>';
          $tbl .= '<td width="6%" align="center">' . $i . '</td>';
          $tbl .= '<td width="21%" align="left"><b>Add: Shipping</b></td>';
          $tbl .= '<td width="15%" align="left"></td>';
          $tbl .= '<td width="6%" align="center"></td>';
          $tbl .= '<td width="7%" align="center"></td>';
          $tbl .= '<td width="8%" align="right"></td>';
          $tbl .= '<td width="7%" align="right"></td>';
          $tbl .= '<td width="8%" align="center"> ' . $this->_currency->format(
          (float) $shipping_exc_tax, $order['currency_code'], $order['currency_value'], false
          ) . '</td>';
          $tbl .= '<td width="8%" align="center">' . $tax_rate . ' %' . '</td>';
          $tbl .= '<td width="14%" align="right">';
          $tbl .= $this->_currency->format(
          (float) $value, $order['currency_code'], $order['currency_value'], false
          );
          $tbl .= '</td>';
          $tbl .= '</tr>';
          $i++;
          }

         */
        $max_tax_rate = max(array_column($products, 'output_tax_rates'));
        $shipping_inc_tax = (float) $order['suborder']['shipping_charge'] * (1 + (float) $max_tax_rate / 100);

        /*
         * 
         * Code not required un-necessary showing advances in buyer invoice
         * already showing in HSN summary table
         * 
          $tbl .= '<tr>';
          $tbl .= '<td width="6%" align="center">' . $i . '</td>';
          $tbl .= '<td width="21%" align="left"><b>Add: Shipping</b></td>';
          $tbl .= '<td width="15%" align="left"></td>';
          $tbl .= '<td width="6%" align="center"></td>';
          $tbl .= '<td width="7%" align="center"></td>';
          $tbl .= '<td width="8%" align="right"></td>';
          $tbl .= '<td width="7%" align="right"></td>';
          $tbl .= '<td width="8%" align="center"> ' . $this->_currency->format(
          (float) $order['suborder']['shipping_charge'], $order['currency_code'], $order['currency_value'], false
          ) . '</td>';
          $tbl .= '<td width="8%" align="center">' . $this->_currency->format(
          (float) $max_tax_rate, $order['currency_code'], $order['currency_value'], false
          ) . ' %' . '</td>';
          $tbl .= '<td width="14%" align="right">';
          $tbl .= $this->_currency->format(
          (float) $shipping_inc_tax, $order['currency_code'], $order['currency_value'], false
          );
          $tbl .= '</td>';
          $tbl .= '</tr>';
         */
        $total_row = '<tr>';
        $total_row_first_not_repeat = '';
        $hsn_codewise_summ_table = '';
        $hsn_codewise_summ_table .= '
                <table cellspacing="0" cellpadding="4" border="1" width="100%">
                <thead>
                    <tr>
                        <th colspan="6" align="center" width="100%"><b>Summary</b></th>
                    </tr>
                    <tr>
                        <th width="20%" align="center"><b>HSN</b></th>
                        <th width="17%" align="center"><b>Value</b></th>
                        <th width="12%" align="center"><b>Rate</b></th>
                        <th width="17%" align="center"><b>SGST</b></th>
                        <th width="17%" align="center"><b>CGST</b></th>
                        <th width="17%" align="center"><b>IGST</b></th>
                    </tr>
                </thead>
                <tbody>';
        if (!empty($hsn_cal_arr)) {
            $total_pv = 0;
            $total_sgst = 0;
            $total_cgst = 0;
            $total_igst = 0;
            $total_inc_tax = 0;

            foreach ($hsn_cal_arr as $hsn_code_val => $hsn_rate_arr) {
                foreach ($hsn_rate_arr as $key => $value) {
                    $total_hsn_val = (float) $value['product_val'] + (float) $value['sgst'] + (float) $value['cgst'] + (float) $value['igst'];
                    $sgst_hsn = $this->_currency->format((float) $value['sgst'], $order['currency_code'], $order['currency_value'], false);
                    $cgst_hsn = $this->_currency->format((float) $value['cgst'], $order['currency_code'], $order['currency_value'], false);
                    $igst_hsn = $this->_currency->format((float) $value['igst'], $order['currency_code'], $order['currency_value'], false);
                    $total_pv += (float) $value['product_val'];
                    $total_sgst += (float) $sgst_hsn;
                    $total_cgst += (float) $cgst_hsn;
                    $total_igst += (float) $igst_hsn;
                    $total_inc_tax += (float) $total_hsn_val;
                    $hsn_codewise_summ_table .= '<tr>
                        <td><b>' . ($hsn_code_val) . '</b></td>
                        <td align="right">' . ($this->_currency->format((float) $value['product_val'], $order['currency_code'], $order['currency_value'], false)) . '</td>
                        <td align="right">' . (($value['rate'] != '') ? $value['rate'] : "-") . '</td>
                        <td align="right">' . (($sgst_hsn != '0.00') ? $sgst_hsn : "-") . '</td>
                        <td align="right">' . (($cgst_hsn != '0.00') ? $cgst_hsn : "-") . '</td>
                        <td align="right">' . (($igst_hsn != '0.00') ? $igst_hsn : "-") . '</td>
                    </tr>';
                }
            }
            $hsn_codewise_summ_table .= '<tr>
                        <td align="right"><b>Product Total</b></td>
                        <td align="right">' . ($this->_currency->format((float) $total_pv, $order['currency_code'], $order['currency_value'], false)) . '</td>
                        <td align="right"></td>
                        <td align="right">' . (($total_sgst != '0') ? ($this->_currency->format((float) $total_sgst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        <td align="right">' . (($total_cgst != '0') ? ($this->_currency->format((float) $total_cgst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        <td align="right">' . (($total_igst != '0') ? ($this->_currency->format((float) $total_igst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        
                        </tr>';
            $shipping_tax_amount = (float) $order['suborder']['shipping_charge'] * ((float) $max_tax_rate / 100);
            $ship_igst = '';
            $ship_cgst_sgst = '';
            if ($this->_is_igst) {
                $ship_igst = (float) $shipping_tax_amount;
            } else {
                $ship_cgst_sgst = (float) $shipping_tax_amount / 2;
            }
            $hsn_codewise_summ_table .= '<tr>
                        <td align="right"><b>Add: Shipping</b></td>
                        <td align="right">' . ($this->_currency->format((float) $order['suborder']['shipping_charge'], $order['currency_code'], $order['currency_value'], false)) . '</td>
                        <td align="right">' . number_format((float) $max_tax_rate, 2, '.', '') . " %" . '</td>
                        <td align="right">' . (($ship_cgst_sgst != '') ? ($this->_currency->format((float) $ship_cgst_sgst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        <td align="right">' . (($ship_cgst_sgst != '') ? ($this->_currency->format((float) $ship_cgst_sgst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        <td align="right">' . (($ship_igst != '') ? ($this->_currency->format((float) $ship_igst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        </tr>';

            if (!empty($advance_arr) && !empty($advance_arr_tax_rate_wise) && $this->_is_receipt_voucher_validity) {

                foreach ($advance_arr_tax_rate_wise as $key => $value) {
                    $advance_exc_tax = (-1) * ((float) $value['advance_val_tax_wise_group'] - (float) $value['advance_tax_val_tax_wise_group']);
                    $hsn_codewise_summ_table .= '<tr>
                        <td align="right"><b>Less: Advance</b></td>
                        <td align="right">' . ($this->_currency->format((float) $advance_exc_tax, $order['currency_code'], $order['currency_value'], false)) . '</td>
                        <td align="right">' . number_format((float) $key, 2, '.', '') . " %" . '</td>
                        <td align="right">' .
                            (
                            (!empty($advance_cgst_sgst[$key]) && $advance_cgst_sgst[$key] != '') ?
                            ($this->_currency->format(((-1) * (float) $advance_cgst_sgst[$key]), $order['currency_code'], $order['currency_value'], 1)) :
                            "-"
                            ) . '</td>
                        <td align="right">' .
                            (
                            (!empty($advance_cgst_sgst[$key]) && $advance_cgst_sgst[$key] != '') ?
                            ($this->_currency->format(((-1) * (float) $advance_cgst_sgst[$key]), $order['currency_code'], $order['currency_value'], 1)) :
                            "-"
                            ) . '</td>
                        <td align="right">' .
                            (
                            (!empty($advance_igst[$key]) && $advance_igst[$key] != '') ?
                            ($this->_currency->format(((-1) * (float) $advance_igst[$key]), $order['currency_code'], $order['currency_value'], 1)) :
                            "-"
                            ) . '</td>
                        </tr>';
                }
            }

            if ($this->_is_receipt_voucher_validity) {
                $total_ex_tax = (float) $total_pv + (float) $order['suborder']['shipping_charge'] + ((-1) * (float) $advance_exc_tax_total);
                $total_ship_product_igst = (float) $total_igst + (float) $ship_igst - (float) $advance_tax_igst_total;
                $total_ship_product_sgst = (float) $total_sgst + (float) $ship_cgst_sgst - (float) $advance_tax_cgst_sgst_total / 2;
                $total_ship_product_cgst = (float) $total_cgst + (float) $ship_cgst_sgst - (float) $advance_tax_cgst_sgst_total / 2;
            } else {
                $total_ex_tax = (float) $total_pv + (float) $order['suborder']['shipping_charge'];
                $total_ship_product_igst = (float) $total_igst + (float) $ship_igst;
                $total_ship_product_sgst = (float) $total_sgst + (float) $ship_cgst_sgst;
                $total_ship_product_cgst = (float) $total_cgst + (float) $ship_cgst_sgst;
            }


            $hsn_codewise_summ_table .= '<tr>
                        <td align="right"><b>Total</b></td>
                        <td align="right">' . ($this->_currency->format((float) $total_ex_tax, $order['currency_code'], $order['currency_value'], 1)) . '</td>
                        <td align="right"></td>
                        <td align="right">' . (($total_ship_product_sgst != '0') ? ($this->_currency->format((float) $total_ship_product_sgst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        <td align="right">' . (($total_ship_product_cgst != '0') ? ($this->_currency->format((float) $total_ship_product_cgst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        <td align="right">' . (($total_ship_product_igst != '0') ? ($this->_currency->format((float) $total_ship_product_igst, $order['currency_code'], $order['currency_value'], 1)) : "-") . '</td>
                        </tr>';
        }
        $hsn_codewise_summ_table .= '</tbody></table>';
        $row_span_count = 1;
        if (!empty($order_totals['paycharge']['breakup'])) {
            $row_span_count = count($order_totals['paycharge']['breakup']);
        }
        $total_row_first_not_repeat .= '<td width="63%" rowspan="' . (int) (count($order_totals) + $row_span_count) . '" colspan="6" >' . $hsn_codewise_summ_table . '</td>';
        $total_row .= '%s<td width="23%%" align="right" colspan="3"><b>%s</b></td>';
        $total_row .= '<td width="14%%" align="right" >%s</td>';


        $total_row .= '</tr>';
        $x = 0;
        $order_totals = $this->setOrderTotals($order_totals);

       // if (!empty($advance_arr)) {
        //     $advance_total = (float) array_sum(array_column($advance_arr, 'value'));
        //     if ($advance_total > 0) {
        //         $order_totals['total_advance']['value'] = '-' . $advance_exc_tax_total;
        //     }
        // }
        foreach ($order_totals as $key => $total) {
            // Html Row for order['total']
            if (!$x) {
                $tbl .= sprintf($total_row, $total_row_first_not_repeat, '', '');
                $x++;
            }
            if ($key == 'advance') {
                $tbl .= sprintf($total_row, '', '', '', '');
            }
            if ($key != 'sub_total') {
                $total_quantity = $total_pieces = '';
            }
            if ($key == 'paycharge' && !empty($total['breakup'])) {
                foreach ($total['breakup'] as $dis_rate => $paycharge_arr) {
                    $dis_percent = $total['title'] . " (" . (-1) * $dis_rate . "%)";
                    $tbl .= sprintf(
                            $total_row, '', $dis_percent, $this->_currency->format((float) $paycharge_arr['discount'], $order['currency_code'], 1)
                    );
                }
            } else {
                $tbl .= sprintf(
                        $total_row, '', $total['title'], $this->_currency->format((float) $total['value'], $order['currency_code'], 1)
                );
            }

            if ($key == 'net_amount') {
                $payable_total = $total['value'];
            }
        }
        return $tbl;
    }

    public static function getHSNCodeWiseDetails($products, $seller_state, $buyer_state) {
        $hsn_code_wise_arr = array();

        foreach ($products as $key => $value) {
            $sgst_cgst_tax = 0;
            $igst_tax = 0;
            $piece = $value['quantity'] * $value['piece_in_set'];
            $rate_per_piece = (
                    (isset($value['price_per_piece']) && !empty($value['price_per_piece'])) ?
                    (float) $value['price_per_piece'] :
                    (
                    (isset($value['transfer_price_per_piece']) && !empty($value['transfer_price_per_piece'])) ?
                    (float) $value['transfer_price_per_piece'] : 0)
                    );
            $rate_output_input = (
                    (isset($value['output_tax_rates']) && !empty($value['output_tax_rates'])) ?
                    (float) $value['output_tax_rates'] :
                    (
                    (isset($value['seller_tax']) && !empty($value['seller_tax'])) ?
                    (float) $value['seller_tax'] : 0)
                    );
            $discount_per_piece = (
                    (isset($value['discount_per_piece']) && !empty($value['discount_per_piece'])) ?
                    (float) $value['discount_per_piece'] : 0);
            $product_value = $piece * ((float) $rate_per_piece + ((float) $discount_per_piece));
            $sgst_cgst_igst_tax = (float) $product_value * ((float) $rate_output_input / 100);

            if ((int) $seller_state === (int) $buyer_state) {
                $sgst_cgst_tax = (float) $sgst_cgst_igst_tax / 2;
            } else {
                $igst_tax = (float) $sgst_cgst_igst_tax;
            }

            $hsn_key = $value['hsn_code'];
            $rate_key = (float) $rate_output_input;
            if (!empty($hsn_code_wise_arr[$hsn_key])) {
                if (!empty($hsn_code_wise_arr[$hsn_key][$rate_key])) {
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['product_val'] = (float) $hsn_code_wise_arr[$hsn_key][$rate_key]['product_val'] + (float) $product_value;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['sgst'] = (float) $hsn_code_wise_arr[$hsn_key][$rate_key]['sgst'] + (float) $sgst_cgst_tax;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['cgst'] = (float) $hsn_code_wise_arr[$hsn_key][$rate_key]['cgst'] + (float) $sgst_cgst_tax;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['igst'] = (float) $hsn_code_wise_arr[$hsn_key][$rate_key]['igst'] + (float) $igst_tax;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['rate'] = number_format((float) $rate_output_input, 2) . '%';
                } else {
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['product_val'] = $product_value;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['sgst'] = $sgst_cgst_tax;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['cgst'] = $sgst_cgst_tax;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['igst'] = $igst_tax;
                    $hsn_code_wise_arr[$hsn_key][$rate_key]['rate'] = number_format((float) $rate_output_input, 2) . '%';
                }
            } else {
                $hsn_code_wise_arr[$hsn_key][$rate_key]['product_val'] = $product_value;
                $hsn_code_wise_arr[$hsn_key][$rate_key]['sgst'] = $sgst_cgst_tax;
                $hsn_code_wise_arr[$hsn_key][$rate_key]['cgst'] = $sgst_cgst_tax;
                $hsn_code_wise_arr[$hsn_key][$rate_key]['igst'] = $igst_tax;
                $hsn_code_wise_arr[$hsn_key][$rate_key]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
            }

           // if (!empty($hsn_code_wise_arr)) {
           //     if (array_key_exists($value['hsn_code'], $hsn_code_wise_arr)) {
           //         $hsn_code_wise_arr[$value['hsn_code']]['product_val'] = (float) $hsn_code_wise_arr[$value['hsn_code']]['product_val'] + (float) $product_value;
           //         $hsn_code_wise_arr[$value['hsn_code']]['sgst'] = (float) $hsn_code_wise_arr[$value['hsn_code']]['sgst'] + (float) $sgst_cgst_tax;
           //         $hsn_code_wise_arr[$value['hsn_code']]['cgst'] = (float) $hsn_code_wise_arr[$value['hsn_code']]['cgst'] + (float) $sgst_cgst_tax;
           //         $hsn_code_wise_arr[$value['hsn_code']]['igst'] = (float) $hsn_code_wise_arr[$value['hsn_code']]['igst'] + (float) $igst_tax;
           //         $hsn_code_wise_arr[$value['hsn_code']]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
           //     } else {
           //         $hsn_code_wise_arr[$value['hsn_code']]['product_val'] = $product_value;
           //         $hsn_code_wise_arr[$value['hsn_code']]['sgst'] = $sgst_cgst_tax;
           //         $hsn_code_wise_arr[$value['hsn_code']]['cgst'] = $sgst_cgst_tax;
           //         $hsn_code_wise_arr[$value['hsn_code']]['igst'] = $igst_tax;
           //         $hsn_code_wise_arr[$value['hsn_code']]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
           //     }
           // } else {
           //     $hsn_code_wise_arr[$value['hsn_code']]['product_val'] = $product_value;
           //     $hsn_code_wise_arr[$value['hsn_code']]['sgst'] = $sgst_cgst_tax;
           //     $hsn_code_wise_arr[$value['hsn_code']]['cgst'] = $sgst_cgst_tax;
           //     $hsn_code_wise_arr[$value['hsn_code']]['igst'] = $igst_tax;
           //     $hsn_code_wise_arr[$value['hsn_code']]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
           // }
        }
        return $hsn_code_wise_arr;
    }

    public function getProductShipping($products) {

        $order['suborder'] = $this->_order_info['suborder'][$this->_suborder_id];
        $max_tax_rate = max(array_column($products, 'output_tax_rates'));
        $sum_of_products = 0;
        $shipping_all_product = 0;
        $shipping_per_product = array();
        $lenght_product = count($products);
        $i = 1;

        foreach ($products as $key => $value) {
            $sum_of_products += $value['quantity'] * $value['piece_in_set'] * (float) $value['price_per_piece'];
        }
        foreach ($products as $key => $value) {
            $shipping_per_peice = round(((((float) $value['price_per_piece']) / (float) $sum_of_products) * (float) $order['suborder']['shipping_charge']), 2);
            if ($i == $lenght_product) {
                $last_product_shipping = round(((float) $order['suborder']['shipping_charge'] - (float) $shipping_all_product) / ($value['quantity'] * $value['piece_in_set']), 2);
                $shipping_per_product[$value['order_product_id']]['shipping_per_product'] = $last_product_shipping;
            } else {
                $shipping_per_product[$value['order_product_id']]['shipping_per_product'] = $shipping_per_peice;
            }
            $shipping_per_product[$value['order_product_id']]['shipping_per_product_rate'] = $max_tax_rate;
            $shipping_per_product[$value['order_product_id']]['shipping_product_qty'] = $value['quantity'] * $value['piece_in_set'];

            $shipping_all_product += round((((((float) $value['price_per_piece']) / (float) $sum_of_products) * (float) $order['suborder']['shipping_charge']) * $value['quantity'] * $value['piece_in_set']), 2);
            $i++;
        }
        return $shipping_per_product;
    }

    public function setShippingTaxWise($shipping_per_peice) {

        foreach ($shipping_per_peice as $key => $value) {
            $tax_keys = $value['shipping_per_product_rate'];
            if (!empty($shipping_tax_group[$tax_keys])) {
                $shipping_tax_group[$tax_keys] += ((float) $value['shipping_per_product'] * $value['shipping_product_qty']);
            } else {
                $shipping_tax_group[$tax_keys] = ((float) $value['shipping_per_product'] * $value['shipping_product_qty']);
            }
        }
        return $shipping_tax_group;
    }

    public function setOrderTotals($order_totals) {
        if (!empty($order_totals['sub_total']['title'])) {
            $order_totals['sub_total']['title'] = 'Total Product Value';
        }
        if (!empty($order_totals['paycharge']['title'])) {
            $order_totals['paycharge']['title'] = 'Less: ' . $order_totals['paycharge']['title'];
        }
        if (!empty($order_totals['coupon']['title'])) {
            $order_totals['coupon']['title'] = 'Less: Coupon Discount';
        }
        if (!empty($order_totals['cashback']['title'])) {
            $order_totals['cashback']['title'] = 'Less: Cashback Discount';
        }
        if (!empty($order_totals['credit'])) {
            unset($order_totals['credit']);
        }
        // if (!empty($order_totals['total_amt'])) {
        //     unset($order_totals['total_amt']);
        // }
        if (!empty($order_totals['tax']['title'])) {
            $order_totals['tax']['title'] = 'GST';
        }
        if (!empty($order_totals['shipping']['title'])) {
            $order_totals['shipping']['title'] = 'Add: Shipping';
        }
        return $order_totals;
    }

    /**
     * public method to fetch product details
    */
    public function getOrderProductsWithSellerName(int $order_id, string $suborder_id){
        $order_product_details = array();

        if( !empty($order_id) && !empty($order_id) ){
            $sql = "
                    SELECT 
                       product_id,
                       oop.name,
                       oop.order_product_id,
                       oop.model,
                       oop.suborder_id,
                       oop.name,
                       oop.quantity,
                       oop.piece_in_set,
                       oop.price_per_piece,
                       oop.discount_per_piece,
                       oop.discount_breakup,
                       oop.weight_per_piece,
                       oop.tax,
                       oop.seller_sku,
                       oop.comment,
                       oop.output_tax_rates,
                       oop.store_pickup,
                       oop.edit_type,
                       oop.edit_history,
                       oop.hsn_code, 
                       oop.customer_comment, 
                       CONCAT(oms.company, ' - ', oms.nickname) as seller_name, 
                       oop.seller_id
                    FROM 
                        " . DB_PREFIX . "order_product oop 
                    INNER JOIN 
                        " . DB_PREFIX . "ms_seller oms ON oop.seller_id = oms.seller_id
                    WHERE 
                        oop.order_id = '" . (int) $order_id . "' 
                        AND oop.suborder_id = '" . $this->_db->escape($suborder_id) . "'
                        AND oop.edit_type = 'SELLER_LATER_DISPATCH'
                    ORDER BY 
                       oop.order_product_id ASC
                   ";

            $result = $this->_db->query($sql);
            if ($result->num_rows > 0) {
                $order_product_details = $result->rows;
            }
        }  
        return $order_product_details;
    }

    /**
     * Private method to set images and minimum values product wise 
     * @author: Nishu, Aug 2018
    */
    private function setProductImagesToClassMembers(array $product_ids) : void{
        //Not empty check
        if(!empty($product_ids)){

            //Load images model, if we have to show images with product details
            $this->_load->model('tool/image');
            if (method_exists($this->_registry, 'get')) {
                $model_tool_image = $this->_registry->get('model_tool_image');
            } else {
                $model_tool_image = $this->_registry->model_tool_image;
            }

            // Getting images of the product(s)
            $sql = "
                    SELECT 
                       product_id, minimum, image 
                    FROM 
                       " . DB_PREFIX . "product
                    WHERE 
                       product_id IN (" . implode(",", $product_ids) . ")
                   ";
            $query = $this->_db->query($sql);
            $this->_images = array_combine(
                                 array_column($query->rows, 'product_id'), 
                                 array_column($query->rows, 'image')
                              );

            //Resize Images
            foreach ($this->_images as $key => $image) {
                $this->_images[$key] = $model_tool_image->resize(
                                                $image, 
                                                $this->_config->get('config_image_product_width'), 
                                                $this->_config->get('config_image_product_height')
                                        );
            }


            $this->_minimum = array_combine(
                                   array_column($query->rows, 'product_id'), 
                                   array_column($query->rows, 'minimum')
                               );
        }
        return;
    }

    /**
     * Private method to set Product category
     * @author: Nishu, Aug 2018
    */
    private function getProductCategoryByProductIds(array $product_ids) : array{
        $data = array();
        //Not empty check
        if(!empty($product_ids)){

            $sql = "
                    SELECT 
                        product_id,category_id
                    FROM 
                        " . DB_PREFIX . "product_to_category
                    WHERE 
                        product_id IN (" . implode(",", $product_ids) . ")
                    ";
            $query = $this->_db->query($sql);

            if ($query->num_rows > 0) {
                $cids    =  array_column($query->rows, 'category_id');
                $pid_cid = array_combine(
                            array_column($query->rows, 'product_id'), 
                            $cids
                           );

                $sql = "
                        SELECT 
                            category_id,name
                        FROM 
                            " . DB_PREFIX . "category_description
                        WHERE 
                            category_id IN (" . implode(",", $cids) . ") 
                            AND language_id = '1'
                       ";

                $result = $this->_db->query($sql);
                if($result->num_rows > 0){
                    $cid_cname = $result->rows;
                    $cid_cname = array_combine(
                                    array_column($cid_cname, 'category_id'), 
                                    array_column($cid_cname, 'name')
                                 );

                    foreach ($pid_cid as $pid => $cid) {
                        $data[$pid] = $cid_cname[$cid] ?? '';
                    }
                }
            }
        }

        return $data;
    }

    /** 
     * @Updated by Nishu, Aug 2018 (Code modularized)
    */
    public function getProductsArrayBySuborderId($order_id, $suborder_id = '') {
        $products = array();

        //Get orderProduct details by suborder_id
        $order_product_details = OrderProduct::getOrderProductBySuborderId($this->_db, (int)$order_id, (string)$suborder_id, $this->_get_all_product);

        if ( !empty($order_product_details) ) {
        
            $product_ids = array_column($order_product_details, 'product_id');

            if ($this->_show_image) {
                //Set images array to member variables of class
                $this->setProductImagesToClassMembers($product_ids);
            }

            if ($this->_show_categories) {
                //Set product categories, product id wise
                $cid_cname = $this->getProductCategoryByProductIds($product_ids);
            }
            
            foreach ($order_product_details as $key => $product) {
                
                $products[$key] = $product;
                if ($this->_show_image) {
                    $products[$key]['image']   = $this->_images[$product['product_id']] ?? '';
                    $products[$key]['minimum'] = $this->_minimum[$product['product_id']] ?? '';
                    $products[$key]['width']   = $this->_config->get('config_image_additional_width');
                    $products[$key]['height']  = $this->_config->get('config_image_additional_height');
                }
                
                if ($this->_show_categories) {
                    //Set Categories name for product details
                    $products[$key]['category'] = $cid_cname[$product['product_id']] ?? '';
                }
                
            }
            if (empty($this->_order_info['products'])) {
                $this->_order_info['products'] = $products;
            }
        }

        return $products;
    }

    /** 
     * @Updated by Nishu, Aug 2018 (Code modularized)
    */
    public function getProductsArrayBySuborderIdEditTypeWise($order_id, $suborder_id) {
        $products = array();

        $order_product_details = $this->getOrderProductsWithSellerName((int)$order_id, $suborder_id);
        
        if ( !empty($order_product_details) ) {

            //Get all product_ids from result set
            $product_ids = array_column($order_product_details, 'product_id');

            if ($this->_show_image) { //If show_image option set to true
                //Set images array to member variables of class
                $this->setProductImagesToClassMembers($product_ids);
            }
            
            if ($this->_show_categories) { //If show_category option set to true
                //Set product categories, product id wise
                $cid_cname = $this->getProductCategoryByProductIds($product_ids);
            }
            
            //Loop over order products
            foreach ($order_product_details as $key => $product) {
                $products[$key] = $product;
                if ($this->_show_image) {
                    
                    $products[$key]['image']   = $this->_images[$product['product_id']] ?? '';
                    $products[$key]['minimum'] = $this->_minimum[$product['product_id']] ?? '';
                    $products[$key]['width']   = $this->_config->get('config_image_additional_width');
                    $products[$key]['height']  = $this->_config->get('config_image_additional_height');
                }
                
                if ($this->_show_categories) {
                    //Set Categories name for product details
                    $products[$key]['category'] = $cid_cname[$product['product_id']] ?? '';
                }
            }
            if (empty($this->_order_info['products'])) {
                $this->_order_info['products'] = $products;
            }
        }

        return $products;
    }

    public function getProductArrayByOrderProductIds(array $order_product_id, array $fields = array()) : array {

        if ( empty($order_product_id) ) return array();
        
        $sql = "SELECT ";
        if (!empty($fields) && is_array($fields)) {
            $sql .= implode(",", $fields);
        } else {
            $sql .= " * ";
        }
        $sql .= " FROM " . DB_PREFIX . "order_product ";
        $sql .= "WHERE order_product_id IN (" . implode(",", $order_product_id) . ") ";
        if ($this->_for_cn) {
            $sql .= " AND buyer_invoice_id > 0 ";
        }
        $result = $this->_db->query($sql);
        return $result->rows;
    }

    public function getTotals($order_id, $suborder_id) {
        if (!empty($this->_seller_info)) {
            $seller_state = $this->_seller_info['state'];
        } else {
            $seller_details = $this->getSellerDetails($this->_order_id, $this->_suborder_id);
            if (!empty($seller_details)) {
                $seller_details = $seller_details[key($seller_details)];
                $seller_state = $seller_details['state'];
                $this->_seller_info = $seller_details;
            }
        }
        if (empty($this->_order_info)) {
            $this->setOrderInfoByOrderIdSuborderId($order_id, $suborder_id);
        } else if (empty($this->_order_info['order']) && empty($this->_order_info['suborder'])) {
            throw new Exception(
            "Order info is missing.
                 You can use BuyerInvoice::setOrderInfo() or
                 BuyerInvoice::setOrderInfoByOrderIdSuborderId()"
            );
        }

        $order = $this->_order_info['order'];
        $grand_total = 0;
        $advance = 0;
        $credit = 0;
        $round_off = 0;
        $this->_amount_formc = 0;
        $suborder = $this->_order_info['suborder'][$this->_suborder_id];

        // Setting GST flag
        if (!isset($suborder['gst'])) {
            $sql = "SELECT gst FROM " . DB_PREFIX . "suborder 
                    WHERE order_id = '" . (int) $this->_order_id . "' 
                      AND suborder_id = '" . $this->_db->escape($this->_suborder_id) . "'";
            $this->gst = $this->_db->query($sql)->row['gst'];
        } else {
            $this->gst = $suborder['gst'];
        }

        // Getting Raw Total values
        $order['total'] = $this->getRawTotal();
        if (!$this->gst) {
            $custom_total = array();
            if (!empty($suborder['custom_totals'])) {
                $custom_total = unserialize($suborder['custom_totals']);
            }
        }
        $advance_tax_sum = 0;
        foreach ($order['total'] as $total) {
            if ($total['code'] != 'sub_total') {
                $total_quantity = $total_pieces = '';
            }
            // Show CST instead of tax, if C Form submit option is selected, and payment tin no is there, and file type is b2b

            if ($total['code'] == 'tax') {
                $tax_totals = $this->getCst($total['value'], $total['title']);


                $tax_vals = !empty($tax_totals['tax']['value']) ? $tax_totals['tax']['value'] : 0;
                $cforms_cst_sum = (!empty($tax_totals['cst']['value']) && !empty($tax_totals['refund_c_form']['value'])) ? ($tax_totals['cst']['value'] + $tax_totals['refund_c_form']['value']) : 0;

                $applied_tax = !empty($tax_vals) ? $tax_vals : $cforms_cst_sum;
                $tax_totals['tax']['value'] = $applied_tax + $advance_tax_sum;


                //$tax_totals['tax']['value'] = $tax_totals['tax']['value'] + $advance_tax_sum;
                $totals = array_merge($totals, $tax_totals);
                $grand_total += ((float) $total['value']);
            } elseif (
                $total['code'] != 'advance' 
                && $total['code'] != 'credit' 
                && $total['code'] != 'total' 
                && $total['code'] != 'round_off' 
                && $total['code'] != 'paycharge'
            ) {
                $value = $this->_currency->format((float) $total['value'], $order['currency_code'], $order['currency_value'], false);
                $totals[$total['code']] = array('title' => $total['title'], 'value' => $value);

                $grand_total += (float) $total['value'];
            } elseif ($total['code'] == 'paycharge') {
                $value = $this->_currency->format((float) $total['value'], $order['currency_code'], $order['currency_value'], false);
                if(!empty($total['breakup'])) {
                    $total['breakup'] = $total['breakup'];
                } else {
                    $total['breakup'] = array();
                }
                $totals[$total['code']] = array('title' => $total['title'], 'value' => $value, 'breakup' => $total['breakup']);

                $grand_total += (float) $total['value'];
            } else if ($total['code'] == 'total') {
                $value = $this->_currency->format((float) $grand_total, $order['currency_code'], $order['currency_value'], false);
                if ($this->gst) {
                    $totals['total_amt'] = array('title' => 'Total Invoice Amount', 'value' => $value);
                } else {
                    $totals['total_amt'] = array('title' => 'Total Amount', 'value' => $value);
                }
            } elseif ($total['code'] == 'advance') {
                $advance = (float) $total['value'];
            } elseif ($total['code'] == 'credit') {
                $credit = (float) $total['value'];
            } elseif ($total['code'] == 'round_off') {
                $round_off = (float) $total['value'];
            }
            // end foreach
        }
        if ($order['code_version'] != 1.0) {
            if (!$this->gst) {
                if (!empty($custom_total['advance']) &&
                        strtolower(trim($order['payment_code'])) == 'cod' &&
                        $suborder['order_status_id'] != 2) {
                    $advance = (-1) * (float) $custom_total['advance']['value'];
                } else if ($suborder['order_status_id'] != 2) {
                    $advance = $this->getAdvance();
                }
            }
        }


        if ($suborder['order_status_id'] == 2) {
            $credit = 0;
        }
        if ($advance < 0 && !$this->gst) {
            $value = $this->_currency->format(
                    (float) $advance, $order['currency_code'], $order['currency_value'], false
            );
            $totals['advance'] = array('title' => 'Advance Collected', 'value' => $value);
        }

        if (!$this->gst) {
            $value = $this->_currency->format((float) $credit, $order['currency_code'], $order['currency_value'], false);
            $totals['credit'] = array('title' => 'Credit against GR (Inc Tax)', 'value' => $value);
        }
        $coupon_advance_arr = array();
        $coupon_advance = 0;
        $cashback_advance = 0;
        $advance_amount = 0;
        $credit_sales = 0;
        if ($this->gst) {
            $advance = $this->getAdvance();
            if (!empty($advance)) {
                foreach ($advance as $payment_id => $adv_arr) {
                    if ($adv_arr['payment_gateway'] == 'coupon') {
                        if (!empty($coupon_advance_arr[$adv_arr['payment_mode']])) {
                            $coupon_advance_arr[$adv_arr['payment_mode']] += $adv_arr['value'];
                        } else {
                            $coupon_advance_arr[$adv_arr['payment_mode']] = $adv_arr['value'];
                        }
                    } else if ($adv_arr['payment_gateway'] == 'cashback') {
                        $cashback_advance += $adv_arr['value'];
                    } else if (strtolower($adv_arr['payment_gateway'])  == 'wsb_credit') {
                        $credit_sales += $adv_arr['value'];
                    } else {
                        $advance_amount += $adv_arr['value'];
                    }
                }
                $advance = (-1) * (float) $advance_amount;
            } else {
                $advance = 0;
            }

            if (!empty($coupon_advance_arr)) {
                $x = 1;
                foreach ($coupon_advance_arr as $payment_mode => $coupon_advances) {
                    $coupon_advance += $coupon_advances;
                    $coupon_advances = (-1) * (float) $coupon_advances;
                    $coupon_advance_vl = $this->_currency->format(
                            (float) $coupon_advances, $order['currency_code'], $order['live_currency_conversion_rate'], false
                    );
                    $totals['total_coupon_' . $x] = array('title' => 'Less: ' . $payment_mode, 'value' => $coupon_advance_vl);
                    $x++;
                }
            }

            if ($cashback_advance > 0) {
                $cashback_advance = (-1) * (float) $cashback_advance;
                $cashback_advance_vl = $this->_currency->format(
                        (float) $cashback_advance, $order['currency_code'], $order['live_currency_conversion_rate'], false
                );
                $totals['total_cashback'] = array('title' => 'Less: Cashback', 'value' => $cashback_advance_vl);
            }

            if ($credit_sales > 0) {
                $credit_sales = (-1) * (float) $credit_sales;
                $credit_sales_vl = $this->_currency->format(
                        (float) $credit_sales, $order['currency_code'], $order['live_currency_conversion_rate'], false
                );
                $totals['total_credit_sales'] = array('title' => 'Less: Credit Sales', 'value' => $credit_sales_vl);
            }

            if ( $advance < 0 ) {
                $advalue = $this->_currency->format(
                    (float) $advance, $order['currency_code'], $order['live_currency_conversion_rate'], false);
                $totals['total_advance'] = array('title' => 'Less: Advance Received', 'value' => $advalue);
            }
        }
        $coupon_advance = (-1) * (float) $coupon_advance;
        $advance = $advance + $cashback_advance + $coupon_advance + $credit_sales;

        if (!empty($round_off)) {
            $value = $this->_currency->format((float) $round_off, $order['currency_code'], $order['currency_value'], false);
            $totals['round_off'] = array('title' => 'Round Off', 'value' => $value);
        }

        $payable_total = (float) $grand_total + (float) $advance + (float) $credit + (float) $round_off;
        if ($payable_total <= 0.01)
            $payable_total = 0;
        $value = $this->_currency->format((float) $payable_total, $order['currency_code'], $order['currency_value'], false);

        $totals['net_amount'] = array('title' => 'Net Payable Amount', 'value' => $value);
        $curreny_grand_total = $this->_currency->format(
                (float) $grand_total, $order['currency_code'], $order['currency_value'], false
        );
        if ($this->gst) {
            $totals['amount_in_word'] = array('title' => 'Amount in words', 'value' => convert_to_currency_indian_format($curreny_grand_total, $order['currency_code']));
        }
        if (empty($this->_fields)) {
            return $this->_totals = $totals;
        } else {
            $fields = $this->_fields;
            $totals = array_filter($totals, function ( $code ) use ($fields) {
                return in_array($code, $fields);
            }, ARRAY_FILTER_USE_KEY);
            return $this->_totals = $totals;
        }
    }

    /**
     * Public method to format totals breakup of buyer invoice i.e. add rupees symbol to amount fields only
     * @param: $data Array
     * @return Array
     * @author: Nishu, Aug 2018
    */
    public function formatOrderTotalValues(array $data){
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $key_code = strtolower($key);

                //checks for which we have to add rupees symbol as prefix
                if( $key_code == 'amount_in_word' ){
                    unset($data[$key]);
                    continue;
                }

                //set value as row_value key for all totals breakup 

                //Using currency value as 1, because of currency value factor is already applied
                $data[$key]['row_value'] = $value['value'];
                $data[$key]['value']     = $this->_currency->format(round($value['value'], 2), $this->_currency_code, 1);

            }
        }

        return $data;
    }

    private function getRawTotal() {
        $order = $this->_order_info['order'];
        $totals = array();
        if ($order['code_version'] == 1.0) {
            $selector = array(
                'order_total' => array(
                    'sort' => array('suborder_id' => 'ASC',
                        'sort_order' => 'ASC'
                    )
                )
            );
            $order_totals = OrderInfo::getOrderInfo($this->_db, $this->_order_id, $this->_suborder_id, $selector);
            $totals = $order_totals['suborder'][$this->_suborder_id]['order_total'];
        } else {
            if (!empty($this->_product_info)) {
                $product_info = $this->_product_info;
                if (!empty($this->_products)) {
                    $products = $this->_products;
                } else {
                    $fields = array(
                        "product_id",
                        "order_product_id",
                        "suborder_id",
                        "quantity",
                        "piece_in_set",
                        "price_per_piece",
                        "discount_per_piece",
                        "discount_breakup",
                        "weight_per_piece",
                        "tax",
                        "output_tax_rates"
                    );
                    $order_product_ids = array_column($this->_product_info, 'order_product_id');
                    $products = $this->getProductArrayByOrderProductIds($order_product_ids, $fields);
                }

                $products_for_total = array();
                foreach ($products as $key => $value) {
                    if (!empty($product_info[$value['order_product_id']])) {
                        // setting piece_in_set to 1 for all product total calculation and quantity as return quantity
                        $products_for_total[$value['order_product_id']] = $value;
                        $products_for_total[$value['order_product_id']]['piece_in_set'] = 1;
                        $products_for_total[$value['order_product_id']]['quantity'] = $product_info[$value['order_product_id']]['quantity'];
                    }
                }
                $this->_products = $products_for_total;
            } else {
                if (!empty($this->_products)) {
                    $this->_products = $this->getProductsArrayBySuborderId(
                            $this->_order_id, $this->_suborder_id, false
                    );
                }
                $this->_products = !empty($this->_products) ?
                        $this->_products :
                        $this->getProductsArrayBySuborderId(
                                $this->_order_id, $this->_suborder_id, false
                );
            }
            $split_order_object = new SplitOrder($this->_db);
            $order['total'] = $split_order_object->splitOrderTotals($this->_order_id, $this->_suborder_id, $this->_products, $this->gst);
            $totals = $order['total'][$this->_suborder_id];
        }
        return $totals;
    }

    public function getTotalsBreakups($order_id, $suborder_id) {
        if (empty($this->_order_info)) {
            $this->setOrderInfoByOrderIdSuborderId($order_id, $suborder_id);
        } else if (empty($this->_order_info['order']) && empty($this->_order_info['suborder'])) {
            throw new Exception(
            "Order info is missing.
                 You can use BuyerInvoice::setOrderInfo() or
                 BuyerInvoice::setOrderInfoByOrderIdSuborderId()"
            );
        }

        if ($this->_order_info['order']['code_version'] == 1.0) {
            $totals = $this->getRawTotal();
            $totals = array_combine(
                    array_column($totals, 'code'), array_column($totals, 'value')
            );
            // getting order total from oc_order because in order having version 1.0
            // in order_total, total value is net payble value( excluding advance and credit )
            $sql = "SELECT total
                   FROM " . DB_PREFIX . "order
                   WHERE order_id = '" . (int) $this->_order_id . "'";

            $result = $this->_db->query($sql);
            if ($result->num_rows > 0) {
                $totals['total'] = $result->row['total'];
            }
            $discount_types = array('paycharge', 'coupon', 'cashback', 'discount', 'deal_discount');
            $total_discount = 0;
            $discounts = array_walk($totals, function( $value, $field, $discount_types ) use (&$total_discount) {
                if (in_array($field, $discount_types)) {
                    $total_discount += (float) $value;
                }
            }, $discount_types);

            $discount_factor = (float) $total_discount / (float) $totals['sub_total'];
            if (empty($this->_products)) {
                $this->_products = $this->getProductsArrayBySuborderId($this->_order_id, $this->_suborder_id);
            }
            array_walk($this->_products, function( &$products, $key ) use ($discount_factor) {
                $products['discount_per_piece'] = $discount_factor * (float) $products['price_per_piece'];
            });

            $tax_wise_breakup = $this->calculateTaxWiseTotal()['tax_wise_breakup'];

            $totals = array(
                'subtotal' => isset($totals['sub_total']) ? $totals['sub_total'] : 0,
                'discount' => $total_discount,
                'tax' => isset($totals['tax']) ? $totals['tax'] : 0,
                'shipping' => isset($totals['shipping']) ? $totals['shipping'] : 0,
                'total' => isset($totals['total']) ? $totals['total'] : 0,
                'tax_wise_breakup' => $tax_wise_breakup
            );
        } else {
            $shipping = $this->_order_info['suborder'][$this->_suborder_id]['shipping_charge'];
            if (empty($this->_products)) {
                $this->_products = $this->getProductsArrayBySuborderId($this->_order_id, $this->_suborder_id);
            }
            $totals = $this->calculateTaxWiseTotal();
            $totals['shipping'] = $shipping;
            $totals['total'] = (float) $totals['product_total'] + (float) $shipping;
        }
        return $totals;
    }

    public function calculateTaxWiseTotal() {
        if (empty($this->_products)) {
            throw new Exception("first set the products using BuyerInvoice::setOptions");
        }
        $totals = array(
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'product_total' => 0,
        );
        $tax_wise_breakup = array();
        foreach ($this->_products as $key => $product) {
            $quantity = (int) $product['piece_in_set'] * (int) $product['quantity'];
            $discount = ((float) $product['discount_per_piece'] * $quantity);
            $total_product_price = ((float) $product['price_per_piece'] * $quantity);
            $total_product_price_with_discount = $total_product_price + $discount;
            $tax = $total_product_price_with_discount * (float) $product['output_tax_rates'] / 100;

            $totals['subtotal'] += (float) $total_product_price;
            $totals['discount'] += (float) $discount;
            $totals['tax'] += (float) $tax;
            $totals['product_total'] += (float) $total_product_price_with_discount + (float) $tax;

            if ($this->_group_by_tax) {
                if (empty($tax_wise_breakup[$product['output_tax_rates']])) {
                    $tax_wise_breakup[$product['output_tax_rates']] = array(
                        'subtotal' => 0,
                        'discount' => 0,
                        'tax' => 0,
                        'tax_rate' => 0,
                        'product_total' => 0,
                    );
                }
                $tax_wise_breakup[$product['output_tax_rates']]['subtotal'] += (float) $total_product_price;
                $tax_wise_breakup[$product['output_tax_rates']]['discount'] += (float) $discount;
                $tax_wise_breakup[$product['output_tax_rates']]['tax'] += (float) $tax;
                $tax_wise_breakup[$product['output_tax_rates']]['tax_rate'] = $product['output_tax_rates'];
                $tax_wise_breakup[$product['output_tax_rates']]['product_total'] += (float) $total_product_price_with_discount + (float) $tax;
            }
        }
        if (!$this->_skip_cst) {
            $cst = $this->getCst($totals['tax'], 'tax');
            array_walk($cst, function( $value, $key ) use (&$totals) {
                if ($key == 'cst') {
                    unset($totals['tax']);
                }
                $totals[$key] = $value['value'];
            });
        }

        $totals['tax_wise_breakup'] = $tax_wise_breakup;
        array_walk_recursive($totals, function(&$val) {
            $val = round($val, 2);
        });
        return $totals;
    }

    private function getCst($tax_value, $tax_title) {
        // Getting Seller Details
        $seller_details = array();
        $totals = array();
        $seller_state = "";
        if (!empty($this->_seller_info)) {
            $seller_state = $this->_seller_info['state'];
        } else {
            $seller_details = $this->getSellerDetails($this->_order_id, $this->_suborder_id);
            if (!empty($seller_details)) {
                $seller_details = $seller_details[key($seller_details)];
                $seller_state = $seller_details['state'];
                $this->_seller_info = $seller_details;
            }
        }
        $order = $this->_order_info['order'];
        $suborder = $this->_order_info['suborder'][$this->_suborder_id];
        if (!empty($order['payment_custom_field'])) {
            $tin_number = unserialize($order['payment_custom_field']);
            $tin_number = !empty($tin_number[1]) ? $tin_number[1] : '';
        }


        if ($suborder['cform_submit'] != 'no_submit' &&
                !empty($tin_number) &&
                $this->_file_type == 'b2b' &&
                strtolower($seller_state) != strtolower($order['payment_zone'])) {

            // As for now cst calculation is hard coded.
            $title = 'CST';
            if ($order['code_version'] == 1.0) {
                $value = $this->_currency->format(
                        (float) $suborder['cst_with_cform'], $order['currency_code'], $order['currency_value'], false
                );
                $totals['cst'] = array('title' => $title, 'value' => $value);
                $title = 'Refundable amount on Form C submission';
                $value = $this->_currency->format(
                        (float) $suborder['refundable_cform'], $order['currency_code'], $order['currency_value'], false
                );
                $totals['refund_c_form'] = array('title' => $title, 'value' => $value);
            } else {

                $total_tax = (float) $tax_value;
                $cst = 0;
                $cst_rate = 2; // Hardcoded to 2%
                if (!empty($this->_products)) {
                    $products = $this->_products;
                } else {
                    $products = $this->getProductsArrayBySuborderId($this->_order_id, $this->_suborder_id);
                }
                foreach ($products as $key => $product) {

                    $rate_to_use = min($cst_rate, (float) $product['output_tax_rates']);

                    // calculating total form c amount
                    if ($cst_rate < (float) $product['output_tax_rates']) {
                        $this->_amount_formc += (int) $product['quantity'] * (int) $product['piece_in_set'] * ((float) $product['price_per_piece'] + (float) $product['discount_per_piece']);
                    }
                    $cst += ((int) $product['quantity'] * (int) $product['piece_in_set'] *
                            ((float) $product['price_per_piece'] + (float) $product['discount_per_piece']) * $rate_to_use) / 100;
                }

                $refundable_amount = $total_tax - $cst;
                $cst_value = $this->_currency->format(
                        (float) $cst, $order['currency_code'], $order['currency_value'], false
                );
                $totals['cst'] = array('title' => $title, 'value' => $cst_value);
                $title = 'Refundable amount on Form C submission';
                $refundable_amount = $this->_currency->format(
                        $refundable_amount, $order['currency_code'], $order['currency_value'], false
                );
                $totals['refund_c_form'] = array('title' => $title, 'value' => $refundable_amount);
            }
        } else {
            $tax_value = $this->_currency->format(
                    (float) $tax_value, $order['currency_code'], $order['currency_value'], false
            );
            $totals = array('tax' => array('title' => $tax_title, 'value' => $tax_value));
        }

        return $totals;
    }

    private function getAdvance() {
        $order = $this->_order_info['order'];
        $advance = 0;

        // Determining advance if code version > 1.0
        if ($this->gst) {
            $advance = AdvanceVoucherlib::calculateAdvanceVouchers($this->_db, $this->_order_id, $this->_suborder_id);
            return $advance;
        } else {
            if (strtolower(trim($order['payment_code'])) == 'cod' && $order['code_version'] != 1.0) {
                $advance = AdvanceVoucherlib::calculateAdvanceVouchers($this->_db, $this->_order_id, $this->_suborder_id);
                $advance = $advance['value'];
                if ($advance > 0) {
                    $advance = (-1) * (float) $advance;
                }
            }
        }

        return $advance;
    }

    /*
     * Function for set advance for buyer invoice
     */

    public function setAdvance($product_arr, $advance_arr) {
        $products_subtotal_inc_tax = 0;
        $products_subtotal_advavce_wise = 0;
        $all_product_adv = 0;
        $product_wise_amount = array();
        $final_rate_group_arr = array();
        $advance_voucher_product_factor = array();
        $advance_total = 0;
        $final_arr = array();

        if (empty($advance_arr))
            return false;

        $advance_total = (float) array_sum(array_column($advance_arr, 'value'));
        foreach ($product_arr as $key => $value) {
            $tax_rate = number_format((float) $value['output_tax_rates'], 2);
            $shipping_per_piece = !empty($value['shipping_per_piece']) ? $value['shipping_per_piece'] : 0;
            $product_price_excl_tax = (((float) $value['price_per_piece'] + ((float) $value['discount_per_piece']) + (float) $shipping_per_piece) * ($value['piece_in_set'] * $value['quantity']));
            $product_wise_amount[$value['order_product_id']]['tax_value'] = (float) $product_price_excl_tax * (float) $tax_rate / 100;

            $product_wise_amount[$value['order_product_id']]['product_value_inc_tax'] = $product_price_excl_tax + $product_wise_amount[$value['order_product_id']]['tax_value'];
            $product_wise_amount[$value['order_product_id']]['rate'] = $tax_rate;
            $products_subtotal_inc_tax = $products_subtotal_inc_tax + $product_wise_amount[$value['order_product_id']]['product_value_inc_tax'];
        }
        foreach ($advance_arr as $ad_key => $ad_value) {
            $advance_voucher_product_factor[$ad_value['advance_voucher_id']] = (float) $ad_value['value'] / (float) $products_subtotal_inc_tax;
        }

        $length_product_wise_amount = count($product_wise_amount);
        $i = 1;
        $temp = '';
        foreach ($product_wise_amount as $key => $value) {
            $single_product_adv = 0;
            $single_product_tax_val = 0;

            foreach ($advance_voucher_product_factor as $fc_key => $fc_value) {
                $single_product_adv += round((float) $value['product_value_inc_tax'] * (float) $fc_value, 2);
                $single_product_tax_val += (float) $value['tax_value'] * (float) $fc_value;
                if ($i != $length_product_wise_amount) {
                    $all_product_adv += (float) $value['product_value_inc_tax'] * (float) $fc_value;
                }
            }
            if ($i == $length_product_wise_amount) {
                $last_advance = round((float) $advance_total - (float) $all_product_adv, 2);
                $final_arr[$key]['advance_total_inc_tax'] = $last_advance;
            } else {
                $final_arr[$key]['advance_total_inc_tax'] = $single_product_adv;
            }

            $final_arr[$key]['advance_total_tax_value'] = $single_product_tax_val;
            $final_arr[$key]['rate'] = $value['rate'];
            $i++;

            $tax_keys = $value['rate'];
            if (!empty($final_rate_group_arr[$tax_keys])) {
                $final_rate_group_arr[$tax_keys]['advance_val_tax_wise_group'] += $final_arr[$key]['advance_total_inc_tax'];
                $final_rate_group_arr[$tax_keys]['advance_tax_val_tax_wise_group'] += $final_arr[$key]['advance_total_tax_value'];
            } else {
                $final_rate_group_arr[$tax_keys]['advance_val_tax_wise_group'] = $final_arr[$key]['advance_total_inc_tax'];
                $final_rate_group_arr[$tax_keys]['advance_tax_val_tax_wise_group'] = $final_arr[$key]['advance_total_tax_value'];
            }
        }
        return $final_rate_group_arr;
    }

    public function saveBuyerInvoiceAsPdf($html, $pdf_name) {
        $pdf_data_arr = array(
            'title' => 'Wholesalebox Invoice',
            'subject' => 'Wholesalebox Invoice',
            'keywords' => 'Wholesalebox, Buyer, Invoice',
            'pdf_name' => $pdf_name,
            'download_path' => DIR_DLOAD_BYR_INV
        );

        if ($this->_cancelled_flag) {

            return downloadCancelledPdf($html, $pdf_data_arr);
        } else {
            require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');
            $action = 'I';
            require_once(DIR_SYSTEM . 'library/tcpdf/config/tcpdf_config.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor(PDF_AUTHOR);
            $pdf->SetTitle($pdf_data_arr['title']);
            $pdf->SetSubject($pdf_data_arr['subject']);
            $pdf->SetKeywords($pdf_data_arr['keywords']);

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


            // ---------------------------------------------------------
            // set font
            $pdf->SetFont('times', 'B', 8);

           // $font_file_path =  DIR_BASE.'/vendor/tecnickcom/tcpdf/fonts/DejaVuSans.ttf';
            //$fontname = TCPDF_FONTS::addTTFfont($font_file_path, 'TrueTypeUnicode', '', 32);
            //$pdf->SetFont($fontname, '', 8, '', 'false');

            // add a page
            $pdf->AddPage();

            $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
            
            $pdf->writeHTML($html, true, false, false, false, '');

            // -----------------------------------------------------------------------------
            // Table with rowspans and THEAD
            // -----------------------------------------------------------------------------

            if (!file_exists($pdf_data_arr['download_path'])) {
                mkdir($pdf_data_arr['download_path'], 0777, true);
            }

            //Close and output PDF document
            $action = 'F';
            $pdf->Output($pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'] . ".pdf", $action);

            if ($this->_get_full_path)
                return base64_encode($pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'] . ".pdf");
            else
                return base64_encode($pdf_data_arr['pdf_name'] . ".pdf");
        }
    }

    public function getOrderProductsDetailWithOrderInfo($order_id, $suborder_id) {

        $this->_selector['order_product'] = array(
            'select' => array(
                'quantity',
                'piece_in_set',
                'price_per_piece',
                'price_per_piece',
                'discount_per_piece',
                'output_tax_rates',
                'hsn_code',
                'order_product_id',
                'name',
                'model'
            )
        );
        if (!empty($order_id) && !empty($suborder_id)) {
            $order_info = OrderInfo::getOrderInfo($this->_db, $order_id, $suborder_id, $this->_selector);
            if ($order_info['suborder'][$suborder_id]['invoice_no'] == 0) {
                return false;
            }
            $order_detail = $order_info['order'];
            $suborder_detail = $order_info['suborder'][$suborder_id];
            $order_product_detail = $suborder_detail['order_product'];
            unset($suborder_detail['order_product']);
            $zone_id_arr = $order_detail['shipping_zone_id'] . ',' . $order_detail['payment_zone_id'];
            $zone_id_arr = rtrim($zone_id_arr, ',');
            $this->_load->model('localisation/zone', 'frontend');
            if (method_exists($this->_registry, 'get')) {
                $model_zone_gstcode = $this->_registry->get('frontend_model_localisation_zone');
            } else {
                $model_zone_gstcode = $this->_registry->frontend_model_localisation_zone;
            }
            $get_zone_state_code = $model_zone_gstcode->getZoneGSTStateCode($zone_id_arr);


            $wsb_tin = '';
            $wsb_warehouse = '';
            $wsb_state_code = '';
            $seller_state_code = '';
            $shipping_state_code = '';
            if (!empty($get_zone_state_code)) {
                $seller_state_code = isset($get_zone_state_code[0]['gst_state_code']) ? $get_zone_state_code[0]['gst_state_code'] : '';
                $shipping_state_code = isset($get_zone_state_code[0]['code']) ? $get_zone_state_code[0]['code'] : '';
            }
            $purchased_firm = $this->getPurchaseFirmAddress($order_id, $suborder_id);

            if (!empty($purchased_firm)) {
                reset($purchased_firm);
                $wsb_tin = $purchased_firm[key($purchased_firm)]['tin'];
                $wsb_warehouse = $purchased_firm[key($purchased_firm)]['company'];
                if (!empty($purchased_firm[key($purchased_firm)]['state_code'][0]['gst_state_code'])) {
                    $wsb_state_code = $purchased_firm[key($purchased_firm)]['state_code'][0]['gst_state_code'];
                }
            }

            if (!empty($wsb_state_code) && !empty($seller_state_code)) {
                $is_igst = ((int) $wsb_state_code === (int) $seller_state_code) ? 0 : 1;
            }

            $total_product_qty = 0;
            $product_return = array();
            foreach ($order_product_detail as $product) {

                $product_wise_tax = 0;
                $igst = 0;
                $sgst_cgst = 0;
                $product_qty = $product['quantity'] * $product['piece_in_set'];
                $total_product_qty += $product_qty;
                $product_value = ((float) $product['price_per_piece'] + (float) $product['discount_per_piece']);
                $product_taxable_amount = $product_value * $product_qty;
                $product_wise_tax = ROUND(((float) $product_taxable_amount * ((float) $product['output_tax_rates'] / 100)), 2);
                if (!empty($is_igst)) {
                    $igst = $product_wise_tax;
                } else {
                    $sgst_cgst = ((float) $product_wise_tax / 2);
                }
                $product_return[] = array(
                    'SGSTAmount' => $sgst_cgst,
                    'CGSTAmount' => $sgst_cgst,
                    'IGSTAmount' => $igst,
                    'HSCode' => $product['hsn_code'],
                    'InvoiceDate' => $suborder_detail['invoice_date'],
                    'InvoiceNumber' => $suborder_detail['invoice_no'],
                    'ItemID' => $product['order_product_id'],
                    'ItemName' => $product['name'],
                    'ItemValue' => $product_value,
                    'Itemquantity' => $product_qty,
                    'SKUNumber' => $product['model'],
                    'SellerGSTNNumber' => $wsb_tin,
                    'SellerName' => $wsb_warehouse,
                    'TaxableAmount' => $product_taxable_amount,
                    'TotalValue' => $product_taxable_amount
                );
            }

            /* remove special char from seller tin number*/
            if(!empty($wsb_tin)) {
                $wsb_tin = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($wsb_tin));
            }

            $alternate_contact_numbers = '';
            if(!empty($order_detail['alternate_contact_number'])) {
                $alternate_contact_numbers = json_decode($order_detail['alternate_contact_number'],true);
                $alternate_contact_numbers = $alternate_contact_numbers[0] ?? '';
                
                /*Remove special chars from value */    
                $alternate_contact_numbers = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($alternate_contact_numbers));
                /*Allow only 0-9 digit in contact number and removed all other chars */
                $alternate_contact_numbers = preg_replace('/[^0-9]/', '', $alternate_contact_numbers);
            }

            $final_array = array(
                'InvoiceNo' => $suborder_detail['invoice_no'],
                'invoice_number' => $suborder_detail['invoice_prefix'] . $suborder_detail['invoice_no'],
                'invoice_date' => $suborder_detail['invoice_date'],
                'gstin' => $wsb_tin,
                'pan_no' => substr($wsb_tin, 2, -3),
                'shipping_address_1' => $order_detail['shipping_address_1'],
                'shipping_address_2' => $order_detail['shipping_address_2'],
                'shipping_city' => $order_detail['shipping_city'],
                'shipping_company' => $order_detail['shipping_company'],
                'shipping_postcode' => $order_detail['shipping_postcode'],
                'shipping_zone' => $order_detail['shipping_zone'],
                'shipping_zone_id' => $order_detail['shipping_zone_id'],
                'shipping_state_code' => $shipping_state_code,
                'shipping_country' => $order_detail['shipping_country'],
                'telephone' => $order_detail['telephone'],
                'alternate_contact_number' => $alternate_contact_numbers,
                'customer_id' => $order_detail['customer_id'],
                'customer_name' => ( trim($order_detail['shipping_firstname'] . ' ' . $order_detail['shipping_lastname']) != '' ) ? trim($order_detail['shipping_firstname'] . ' ' . $order_detail['shipping_lastname']) : trim($order_detail['shipping_company']),
                'customer_email' => $order_detail['email'],
                'order_no' => $order_detail['order_no'],
                'payment_code' => $order_detail['payment_code'],
                'itemdtl' => $product_return
            );
            return $final_array;
        } else {
            return false;
        }
    }

}

?>
