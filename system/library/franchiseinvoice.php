<?php
class FranchiseInvoice 
{
    private $_order_id;
    private $_suborder_id;
    private $_franchise_id;
    private $_order_info = array();
    private $_invoice_info = array();
    private $_cancelled_flag = false;
    private $_totals = array();
    private $_gst_state_code = array();
    private $_get_full_path = false;
    private $_currency;
    private $_is_igst = 1;
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
                'lastname'
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

    public function __construct($registry, $filename = '') {
        if (!empty($filename)) {
            $filename = base64_decode($filename);
            $filename = unserialize($filename);
            $this->_order_id = $filename['order_id'];
            $this->_suborder_id = $filename['suborder_id'];
            $this->_franchise_id = $filename['franchise_id'];
        }

        if (method_exists($registry, 'get')) {
            $this->_registry = $registry;
            $this->_db = $registry->get('db');
            $this->_load = $registry->get('load');
            $this->_currency = $registry->get('currency');
        } else {
            $this->_registry = $registry;
            $this->_db = $registry->db;
            $this->_load = $registry->load;
            $this->_currency = $registry->currency;
        }
    }

    public function getFile() {
        $this->setOrderInfoByOrderIdSuborderId($this->_order_id, $this->_suborder_id);
        $pdf = $this->generateOrderInvoiceHtmlForGST();
        $file_name = $this->saveBuyerInvoiceAsPdf($pdf['html'], $pdf['invoice_no']);
        return $file_name;
    }

    /**
     * Public function to get Franchise Invoice Details
     *@param: $order_id, $suborder_id
     *@return: Array
     *@author: Nishu, Dec 2017
    */
    public function getFranchiseInvoiceDeatils($order_id, $suborder_id){
        $invoice_details = array();

        $sql = "SELECT * 
                 FROM " . DB_PREFIX . "franchise_invoice 
                 WHERE order_id = '". (int)$order_id ."'
                    AND suborder_id = '". $this->_db->escape($suborder_id) ."'
                    AND status = 1
                ";
        $result = $this->_db->query($sql);
        if($result->num_rows > 0){
            $invoice_details = $result->row;

            //Check and update if invoice meta is empty
            $invoice_details = $this->checkAndUpdateInvoiceMeta($invoice_details);
        }

        return $invoice_details;
    }


    /**
     * Public function to set Order and Sub-order details
     * @param: $order_id, $suborder_id
     * @return: void
     * @author: Nishu, Dec 2017
    */
    public function setOrderInfoByOrderIdSuborderId() {
        $order_id = $this->_order_id;
        $suborder_id = $this->_suborder_id;
        $franchie_id = $this->_franchise_id;
        $this->_order_info = OrderInfo::getOrderInfo($this->_db, $order_id, $suborder_id, $this->_selector);
       
        $this->_cancelled_flag = ((int) $this->_order_info['suborder'][$suborder_id]['order_status_id'] === 2) ? true : false;
        $this->_invoice_info = $this->getFranchiseInvoiceDeatils($order_id, $suborder_id);
    }

    /**
     * Public method to update seller meta for franchise invoice, if empty
     * @param:  array $invoice_details
     * @return: Boolean
     * @author: Nishu, Aug 2018
    */
    public function checkAndUpdateInvoiceMeta(array $invoice_details) : array{
        
        if( !empty($invoice_details) && empty($invoice_details['invoice_meta']) ){
            
            $franchise_id = $invoice_details['franchise_id'];

            $this->_load->model('account/customer');
            $this->_load->model('account/address');
           
            //Get SellerDetails
            $seller_details = $this->_load->model_account_customer->getCustomer($franchise_id);
            
            //Get Seller Address details
            $seller_address = $this->_load->model_account_address->getAddress($seller_details['address_id'], $franchise_id);

            if(empty($seller_address)){
              return false;
            }
        
            $seller_address['gst_number'] = $seller_details['gst_number'];
            //Get StateCode for buyer
            $this->_load->model('localisation/zone');
            $zone_ids = '';
            $zone_ids = $seller_address['zone_id'];
            $zone_ids = trim($zone_ids, ',');
            $state_codes = $this->_load->model_localisation_zone->getZoneGSTStateCode($zone_ids);
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
            $invoice_meta['seller_data']['tin']              = $seller_address['gst_number'];
            $invoice_meta['seller_data']['zone_id']          = $seller_address['zone_id'];
            $invoice_meta['seller_data']['country_id']       = $seller_address['country_id'];
            $invoice_meta['seller_data']['pickup_city_code'] = $seller_address['city'];
            $invoice_meta['seller_data']['state']            = $seller_address['zone'];
            $invoice_meta['seller_data']['country']          = $seller_address['country'];
            $invoice_meta['seller_data']['gst_state_code']   = $gst_state_code[$seller_address['zone_id']];

            $sql = "
                    UPDATE 
                        " . DB_PREFIX . "franchise_invoice 
                    SET
                      invoice_meta     = '". serialize($invoice_meta) ."'
                    WHERE
                      order_id         = '". (int)$invoice_details['order_id'] ."'
                      AND suborder_id  = '". $this->_db->escape($invoice_details['suborder_id']) ."'
                      AND franchise_id = '". (int)$invoice_details['franchise_id'] ."'
                ";
            
            $this->_db->query($sql);

            $invoice_details['invoice_meta'] = serialize($invoice_meta);

        }
        return $invoice_details;
    }

    /**
     * Public function to generate HTML for franchise invoice
     * @param: void
     * @return: Array
     * @author: Nishu, Dec 2017
    */
    public function generateOrderInvoiceHtmlForGST() {
        $this->_currency = $this->_currency;
        $order = $this->_order_info['order'];
        $invoice = $this->_invoice_info;
        $invoice_meta = unserialize($invoice['invoice_meta']);
        $seller_details = $invoice_meta['seller_data'];
        $this->_seller_info = $seller_details;
        $order['suborder'] = $this->_order_info['suborder'][$this->_suborder_id];
        $gst_state_code_billed = '';
        $gst_state_code_shipped = '';
        $gst_state_code_seller = '';
        $invoice_name  = $invoice['invoice_prefix'] . $invoice['invoice_no'];
        $invoice_no    = $invoice['invoice_no'];
        $invoice_date  = date("d-m-Y", strtotime($invoice['date_added']));
        $order_no      = $order['order_no'];
        $date_added    = date("d-m-Y", strtotime($order['suborder']['date_added']));
        $payment_city  = !empty($order['payment_city']) ? $order['payment_city'] : '';
        $shipping_city = !empty($order['shipping_city']) ? $order['shipping_city'] : '';
        $custom_field  = !empty($order['payment_custom_field']) ? unserialize($order['payment_custom_field']) : '';
        $tin_number    = $order['gst_number'];
        $buyers_details = '';
        $buyers_details_ship = '';

        $buyers_details_ship .= '<span align="center"><b>Shipped To</b></span><br>';
        $buyers_details .= '<span align="center"><b>Billed To</b></span><br>';
        $buyers_details_ship .= !empty(trim($order['shipping_company'])) ? '<b>' . trim($order['shipping_company']) . '</b><br>' : '';
        $buyers_details .= !empty(trim($order['payment_company'])) ? '<b>' . trim($order['payment_company']) . '</b><br>' : '';
        $buyers_details .= !empty(trim($order['payment_address_1'])) ? trim($order['payment_address_1']) . ', ' : '';
        $buyers_details .= !empty(trim($order['payment_address_2'])) ? trim($order['payment_address_2']) . '<br>' : '<br>';
        $buyers_details .= '<b>' . trim($payment_city . ' - ' . $order['payment_postcode']) . '</b><br>';
        $buyers_details .= !empty(trim($order['payment_zone'])) ? trim($order['payment_zone']) . ', ' : '';
        $buyers_details .= !empty(trim($order['payment_country'])) ? trim($order['payment_country']) . '<br>' : '';
        $buyers_details .= !empty(trim($order['telephone'])) ? 'Tel: <b>' . trim($order['telephone']) . '</b><br>' : '';
        $buyers_details_ship .= !empty(trim($order['shipping_address_1'])) ? trim($order['shipping_address_1']) . ', ' : '';
        $buyers_details_ship .= !empty(trim($order['shipping_address_2'])) ? trim($order['shipping_address_2']) . '<br>' : '<br>';
        $buyers_details_ship .= '<b>' . trim($shipping_city . ' - ' . $order['shipping_postcode']) . '</b><br>';
        $buyers_details_ship .= !empty(trim($order['shipping_zone'])) ? trim($order['shipping_zone']) . ', ' : '';
        $buyers_details_ship .= !empty(trim($order['payment_country'])) ? trim($order['payment_country']) . '<br>' : '';
        $buyers_details_ship .= !empty(trim($order['telephone'])) ? 'Tel: <b>' . trim($order['telephone']) . '</b><br>' : '';
        
        if (!empty($tin_number)) {
			$buyers_details .= '<br>GSTIN / UIN: <b>' . $tin_number . '</b><br>';
			$buyers_details_ship .= '<br>GSTIN / UIN: <b>' . $tin_number . '</b><br>';
		}
            
        $zone_id_arr = $order['shipping_zone_id'] . ',' . $order['payment_zone_id'];
        $zone_id_arr = rtrim($zone_id_arr, ',');

        //Get StateCode for buyer
        $this->_load->model('localisation/zone');
        $zone_ids  = '';
        $zone_ids  = $invoice_meta['seller_data']['zone_id'];
        $zone_ids .= !empty($order['payment_zone_id'])? ','.$order['payment_zone_id'] : '';
        $zone_ids .= !empty($order['shipping_zone_id'])? ','.$order['shipping_zone_id']: '';
        $zone_ids  = trim($zone_ids, ',');
        $state_codes = $this->_load->model_localisation_zone->getZoneGSTStateCode($zone_ids);
        $gst_state_code = array();
        foreach ($state_codes as $state_code) {
            $gst_state_code[$state_code['zone_id']] = $state_code['gst_state_code'];
        }
        if ((int) $gst_state_code[$order['payment_zone_id']] === (int) $gst_state_code[$invoice_meta['seller_data']['zone_id']]) {
            $this->_is_igst = 0;
        }
        $this->_gst_state_code = $gst_state_code;
        $buyers_details .= '<span align="right"><b>State Code: ' . $gst_state_code[$order['payment_zone_id']] . '</b></span>';
        $buyers_details_ship .= '<span align="right"><b>State Code: ' . $gst_state_code[$order['shipping_zone_id']] . '</b></span>';
        $tbl = '<table width="100%" border="1" cellspacing="0" cellpadding="4" >';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center" colspan="10">'. $order['payment_city'] .'</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="40%" rowspan="4" colspan="2" >';
        if (!empty($seller_details)) {
            $tbl .= '<b>' . $seller_details['company'] . '<br>';
            $tbl .= $seller_details['address1'] . ',';
            if (!empty($seller_details['address2'])) {
                $tbl .= $seller_details['address2'];
            }
            $tbl .=  '</b><br>';
            $tbl .= '<b>' . $seller_details['city'] . ', ' . $seller_details['state'] . '</b><br>';
            $tbl .= '<b>' . $seller_details['country'] . '</b><br>';
            $tbl .= 'GSTIN / UIN: <b>' . (!empty($seller_details['tin']) ? $seller_details['tin'] : "") . '</b><br><br>';
            $tbl .= '<br><br><span align="right"><b>State Code: ' . (!empty($seller_details['gst_state_code']) ? $seller_details['gst_state_code'] : "") . '</b></span>';
        }
        $tbl .= '</td>';
        $tbl .= '<td width="20%" colspan="3" >Original For Buyers</td>';
        $tbl .= '<td width="20%" colspan="3">Duplicate for Sellers</td>';
        $tbl .= '<td width="20%" colspan="2">Triplicate for Transporter</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" colspan="3">PO No: <b>' . ((isset($order['suborder']['suborder_id']) && $order['suborder']['suborder_id'] != '') ? $order['suborder']['suborder_id'] : '') . '</b></td>';
        $tbl .= '<td width="30%" colspan="5">PO Date: <b>' . ((isset($order['suborder']['date_added']) && $order['suborder']['date_added'] != '') ? date("d-m-Y", strtotime($order['suborder']['date_added'])) : '') . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" colspan="3">Inv. No: <b>' . ($invoice_no > 0 ? $invoice_name : '') . '</b></td>';
        $tbl .= '<td width="30%" colspan="5">Inv. Date: <b>' . ($invoice_no > 0 ? $invoice_date : '') . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" colspan="3" >' . $buyers_details . ' </td>';
        $tbl .= '<td width="30%" colspan="5" >' . $buyers_details_ship . ' </td>';
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
        $tbl .= '<td width="50%">';
        $tbl .= '</td>';
        $tbl .= '<td width="50%" align="center">E & OE <br><br><br> For <br>';
        if (!empty($seller_details)) {
            $tbl .= strtoupper(trim($seller_details['company']));
        } 
        $tbl .= '<br><br><br></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left" colspan="2">';
        $tbl .= 'i. Certified that the particulars given above are true and correct.<br>';
        $tbl .= 'ii. Any additional Octroi, duty, special taxes etc. will be borne by buyer<br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left" colspan="2">';
        $tbl .= '<br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="center" colspan="2">';
        $tbl .= '<b>THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE ANY SIGNATURE</b><br>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl_head  = '<h2 align="center">PREPAID</h2>';      
        $tbl_head .= '<h4 align="center">TAX INVOICE' . ( $invoice_no > 0 ? ' - ' . $invoice_name : '' ) . '</h4>';
        $tbl = $tbl_head . $tbl;
        return array('html' => $tbl, 'invoice_no' => $invoice_name);
    }

    public function getProductsArrayBySuborderId($order_id, $suborder_id) {
        $images = array();
        $this->_load->model('tool/image');
        $sql = "SELECT  product_id, ";
        $sql .= "order_product_id,";
        $sql .= "model,";
        $sql .= "suborder_id,";
        $sql .= "name,";
        $sql .= "quantity,";
        $sql .= "piece_in_set,";
        $sql .= "price_per_piece,";
        $sql .= "discount_per_piece,";
        $sql .= "discount_breakup,";
        $sql .= "weight_per_piece,";
        $sql .= "tax,";
        $sql .= "seller_sku,";
        $sql .= "comment,";
        $sql .= "output_tax_rates,";
        $sql .= "store_pickup,";
        $sql .= "edit_type,";
        $sql .= "edit_history, ";
        $sql .= "hsn_code, ";
        $sql .= "customer_comment, ";
        $sql .= "seller_id, ";
        $sql .= "sor_product ";
        $sql .= " FROM " . DB_PREFIX . "order_product AS OP ";
        $sql .= " WHERE order_id = '" . (int) $order_id . "' AND ";
        $sql .= "suborder_id = '" . $this->_db->escape($suborder_id) . "'";
        $sql .= " ORDER BY order_product_id ASC";
        $result = $this->_db->query($sql);
        if ($result->num_rows > 0) {
            $order_product_details = $result->rows;
        }else{
            return array();
        }
        $product_ids = array_column($order_product_details, 'product_id');
        $pid_cid = array();
        $sql = "SELECT product_id,category_id
                FROM " . DB_PREFIX . "product_to_category
                WHERE product_id IN (" . implode(",", $product_ids) . ")";
        $query = $this->_db->query($sql);
        if ($query->num_rows > 0) {
            $cids = array_column($query->rows, 'category_id');
            $pid_cid = array_combine(array_column($query->rows, 'product_id'), $cids);
            $sql = "SELECT category_id,name
                    FROM " . DB_PREFIX . "category_description
                    WHERE category_id IN (" . implode(",", $cids) . ") AND
                          language_id = '1'";
            $cid_cname = $this->_db->query($sql)->rows;
            $cid_cname = array_combine(
                    array_column($cid_cname, 'category_id'), array_column($cid_cname, 'name')
            );
        }
        $products = array();
        foreach ($order_product_details as $key => $product) {
            $products[$key] = $product;
            if (!empty($pid_cid[$product['product_id']])) {
                $products[$key]['category'] = $cid_cname[$pid_cid[$product['product_id']]];
            }
        }
        if (empty($this->_order_info['products'])) {
            $this->_order_info['products'] = $products;
        }
        return $products;
    }

    public function getTotals($order_id, $suborder_id) {
        $order = $this->_order_info['order'];
        $grand_total = 0;
        $round_off = 0;
        $suborder = $this->_order_info['suborder'][$this->_suborder_id];
        // Getting Raw Total values
        $order['total'] = $this->getRawTotal();
        foreach ($order['total'] as $total) {
            if ($total['code'] != 'sub_total') {
                $total_quantity = $total_pieces = '';
            }
            // Show CST instead of tax, if C Form submit option is selected, and payment tin no is there, and file type is b2b
            if ($total['code'] == 'tax') {
                $tax_totals = $this->getCst($total['value'], $total['title']);
                $tax_totals['tax']['value'] = $tax_totals['tax']['value'];
                $totals = array_merge($totals, $tax_totals);
                $grand_total += ((float) $total['value']);
            } elseif ($total['code'] != 'total' && $total['code'] != 'round_off') {
                $value = $this->_currency->format((float) $total['value'], $order['currency_code'], $order['currency_value'], false);
                $totals[$total['code']] = array('title' => $total['title'], 'value' => $value);
                $grand_total += (float) $total['value'];
            } else if ($total['code'] == 'total') {
                $value = $this->_currency->format((float) $grand_total, $order['currency_code'], $order['currency_value'], false);
                
                $totals['total_amt'] = array('title' => 'Total Invoice Amount', 'value' => $value);
                
            } elseif ($total['code'] == 'round_off') {
                $round_off = (float) $total['value'];
            }
            // end foreach
        }
        if (!empty($round_off)) {
            $value = $this->_currency->format((float) $round_off, $order['currency_code'], $order['currency_value'], false);
            $totals['round_off'] = array('title' => 'Round Off', 'value' => $value);
        }
        $payable_total = (float) $grand_total + (float) $round_off;
        if ($payable_total <= 0.01)
            $payable_total = 0;
        $value = $this->_currency->format((float) $payable_total, $order['currency_code'], $order['currency_value'], false);

        $totals['net_amount'] = array('title' => 'Net Payable Amount', 'value' => $value);
        $curreny_grand_total = $this->_currency->format(
                (float) $grand_total, $order['currency_code'], $order['currency_value'], false
        );

        $totals['amount_in_word'] = array('title' => 'Amount in words', 'value' => convert_to_currency_indian_format($curreny_grand_total, $order['currency_code']));
        return $totals;
    }

    private function getRawTotal() {
        $order = $this->_order_info['order'];
        $totals = array();
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
            foreach ($products as $value) {
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
        $order['total'] = $split_order_object->splitOrderTotals($this->_order_id, $this->_suborder_id, $this->_products, 1);
        $totals = $order['total'][$this->_suborder_id];
        return $totals;
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


    public function getProductsHtmlForGST($products, $order_totals) {
        $order = $this->_order_info['order'];
        $order['suborder'] = $this->_order_info['suborder'][$this->_suborder_id];
        $i = 1;
        $total_quantity = 0;
        $total_pieces = 0;
        $tbl = '';
        $tbl .= '<tr>';
        $tbl .= '<td width="6%" align="center"><b>S. No.</b></td>';
        $tbl .= '<td width="21%" align="center"><b>Item Description</b></td>';
        $tbl .= '<td width="15%" align="center"><b>HSN</b></td>';
        $tbl .= '<td width="6%" align="center"><b>Qty</b></td>';
        $tbl .= '<td width="7%" align="center"><b>Unit of Meas. </b></td>';
        $tbl .= '<td width="8%" align="center"><b>Rate / Pc</b></td>';
        $tbl .= '<td width="7%" align="center"><b>Dis. / Pc</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Product Value</b></td>';
        $tbl .= '<td width="8%" align="center"><b>GST Rate</b></td>';
        $tbl .= '<td width="14%" align="center"><b>Amount (Inc. Tax)</b></td>';
        $tbl .= '</tr>';
        $hsn_cal_arr = array();
        $sgst_cgst_tax = 0;
        $igst_tax = 0;
        $shipping_per_peice = $this->getProductShipping($products);
        $shipping_tax_wise = $this->setShippingTaxWise($shipping_per_peice);

        foreach ($products as $product) {
            $tbl .= '<tr>';
            $tbl .= '<td width="6%" align="center">';
            $tbl .=  $i;
            $tbl .= '</td>';
            $tbl .= '<td width="21%" align="left">';
            $tbl .= '<b>' . $product['model'] . '</b>';
            $tbl .= !empty($product['name']) ? '<br><i style="font-size:9px">' . $product['name'] . '</i>' : '';
            $tbl .= !empty($product['category']) ? ' - ' . $product['category'] : '';
            $tbl .= '</td>';
            $tbl .= '<td width="15%" align="left">';
            $tbl .= '<b>' . $product['hsn_code'] . '</b>';
            $tbl .= '</td>';
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

            $tbl .= '<td width="6%" align="center">' . (float) $product['quantity'] * (float) $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="7%" align="center">Piece</td>';
            $tbl .= '<td width="8%" align="right">';
            $tbl .= $this->_currency->format(
                        (float) $product['price_per_piece'], $order['currency_code'], $order['currency_value'], false);
            $tbl .= '</td>';
            $tbl .= '<td width="7%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $product['discount_per_piece'], $order['currency_code'], $order['currency_value'], false);
            $tbl .= '</td>';
            $tbl .= '<td width="8%" align="center">' . $this->_currency->format(
                            (float) $product_value, $order['currency_code'], $order['currency_value'], false) . '</td>';
            $tax_rate = number_format((float) $product['output_tax_rates'], 2);
            $tbl .= '<td width="8%" align="center">' . $tax_rate . ' %' . '</td>';
            $amount_inc_tax = (float) $product_value * (1 + (float) $product['output_tax_rates'] / 100);
            $tbl .= '<td width="14%" align="right">';
            $tbl .= $this->_currency->format(
                    (float) $amount_inc_tax, $order['currency_code'], $order['currency_value'], false);
            $tbl .= '</td>';
            $tbl .= '</tr>';
            $i++;
            $total_quantity += $product['quantity'];
            $total_pieces += (float) $product['quantity'] * (float) $product['piece_in_set'];
        }
        $hsn_cal_arr = self::getHSNCodeWiseDetails($products, $this->_seller_info['gst_state_code'], $this->_gst_state_code[$order['payment_zone_id']]);
        $max_tax_rate = max(array_column($products, 'output_tax_rates'));
        $shipping_inc_tax = (float) $order['suborder']['shipping_charge'] * (1 + (float) $max_tax_rate / 100);
        $total_row = '<tr>';
        $total_row_first_not_repeat = '';
        $hsn_codewise_summ_table = '';
        $hsn_codewise_summ_table .= '
                <table cellspacing="0" cellpadding="4" border="1" width="100%" >
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
            $total_ship_product_igst = (float) $total_igst + (float) $ship_igst;
            $total_ship_product_sgst = (float) $total_sgst + (float) $ship_cgst_sgst / 2;
            $total_ship_product_cgst = (float) $total_cgst + (float) $ship_cgst_sgst / 2;
            $total_ex_tax = (float) $total_pv + (float) $order['suborder']['shipping_charge'];
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
        $total_row_first_not_repeat .= '<td width="63%" rowspan="' . (int) (count($order_totals) + 1) . '" colspan="6" >' . $hsn_codewise_summ_table . '</td>';
        $total_row .= '%s<td width="23%%" align="right" colspan="3"><b>%s</b></td>';
        $total_row .= '<td width="14%%" align="right" >%s</td>';
        $total_row .= '</tr>';
        $x = 0;
        $order_totals = $this->setOrderTotals($order_totals);
        foreach ($order_totals as $key => $total) {
            // Html Row for order['total']
            if (!$x) {
                $tbl .= sprintf($total_row, $total_row_first_not_repeat, '', '');
                $x++;
            }
            if ($key != 'sub_total') {
                $total_quantity = $total_pieces = '';
            }
            $tbl .= sprintf(
                    $total_row, '', $total['title'], $this->_currency->format((float) $total['value'], $order['currency_code'], 1)
            );
            if ($key == 'net_amount') {
                $payable_total = $total['value'];
            }
        }
        $data = array();
        $data['order_id']     = $this->_order_id;
        $data['suborder_id']  = $this->_suborder_id;
        $data['franchise_id'] = $this->_franchise_id;
        $data['amount']       = $payable_total;
        $this->updateInvoiceAmount($data);
        return $tbl;
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
        }
        return $hsn_code_wise_arr;
    }

    public function setOrderTotals($order_totals) {
        if (!empty($order_totals['sub_total']['title'])) {
            $order_totals['sub_total']['title'] = 'Total Product Value';
        }
        if (!empty($order_totals['paycharge']['title'])) {
            $order_totals['paycharge']['title'] = 'Less: ' . $order_totals['paycharge']['title'];
        }
        if (!empty($order_totals['tax']['title'])) {
            $order_totals['tax']['title'] = 'GST';
        }
        if (!empty($order_totals['shipping']['title'])) {
            $order_totals['shipping']['title'] = 'Add: Shipping';
        }
        return $order_totals;
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

            //$font_file_path =  DIR_BASE.'/vendor/tecnickcom/tcpdf/fonts/DejaVuSans.ttf';
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

    /**
     * Public functoin to update amount in franchise invoice table
     * @param: $data Array
     * @return: void
     * @author: Nishu, Dec 2017
    */
    public function updateInvoiceAmount($data){
        $sql = "UPDATE oc_franchise_invoice 
                 SET invoice_amount = '". (float)$data['amount'] ."'
                 WHERE order_id = '". (int)$data['order_id'] ."'
                    AND suborder_id = '". $this->_db->escape($data['suborder_id']) ."'
                    AND franchise_id = '". (int)$data['franchise_id'] ."'";
        $this->_db->query($sql);
        return;
    }

    public function setOptions($flag_name, $flag_value) {
        if (property_exists($this, '_' . $flag_name)) {
            $this->{'_' . $flag_name} = $flag_value;
        } else {
            throw new Exception("Class BuyerInvoice doesn't contain $flag_name");
        }
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
}
