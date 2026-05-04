<?php

class SellerInvoice {

    private $_order_id;
    private $_suborder_id;
    private $_invoice_prefix;
    private $_invoice_no;
    private $_invoice_data = null;
    private $_seller_invoice_id;
    private $_registry;
    private $_db;
    private $_load;
    private $_seller_id;
    private $_gst = 0;

    public function __construct($registry, $file_path = '') {

        $this->_registry = $registry;

        if (method_exists($registry, 'get')) {
            $this->_db = $registry->get('db');
            $customer = $registry->get('customer');
            $this->_load = $registry->get('load');

            if (!empty($customer) && method_exists($customer, 'isLogged')) {
                $this->_seller_id = $registry->get('customer')->isLogged();
            }
        } else {
            $this->_db = $registry->db;
            $this->_load = $registry->load;
            if (!is_null($registry->customer)) {
                $this->_seller_id = $registry->customer->isLogged();
            }
        }


        if (!empty($file_path)) {
            $file_path = unserialize(base64_decode($file_path));
            $this->_seller_invoice_id = $file_path['seller_invoice_id'];
            $this->updateInvoiceData();
        }
    }

    public function getSellerProductsTotalsByProductIds($order_product_ids, $opid_to_quantity = array()) {
        $sql = "SELECT  order_product_id,
                        piece_in_set * quantity as quantity,
                        transfer_price_per_piece
                FROM " . DB_PREFIX . "order_product
                WHERE order_product_id IN (" . implode(",", $order_product_ids) . ")";
        $result = $this->_db->query($sql);
        if ($result->num_rows > 0) {
            $total = 0;
            foreach ($result->rows as $rows) {
                if (!empty($opid_to_quantity)) {
                    $total += (int) $opid_to_quantity[$rows['order_product_id']] * (float) $rows['transfer_price_per_piece'];
                } else {
                    $total += (int) $rows['quantity'] * (float) $rows['transfer_price_per_piece'];
                }
            }
            return $total;
        }
    }

    public function getFile() {
        // get Invoice Date
        $order_data = $this->getOrderData();

        if (!isset($this->_invoice_data)) {
            $this->updateInvoiceData();
        }
        $this->_gst = $this->_invoice_data['gst'];

        $invoice_meta = $this->getInvoiceMeta($this->_invoice_data);
        $this->_invoice_data = array_merge($this->_invoice_data, $invoice_meta);

        $calculationProducts = $this->calculationProducts(
                $this->_registry, $this->_order_id, $this->_suborder_id, $this->_invoice_data['seller_id'], '', $this->_seller_invoice_id
        );
        $order_info = array(
            'order_data' => $order_data,
            'invoice_data' => $this->_invoice_data,
            'total_data' => $calculationProducts,
        );
        if ($this->_gst) {
            return $this->gstFormatInvoiceHtmlPdf($this->_registry, $order_info);
        } else {
            return $this->generateSellerInvoiceHTml($order_info);
        }
    }

    public function getInvoiceMeta($invoice) {
        $invoice_data = array();
        if (!empty($invoice['seller_invoice_meta'])) {
            $invoice_data = unserialize($invoice['seller_invoice_meta']);
        } else {
            $invoice_data['seller_data'] = $this->getSellerDetails($invoice['seller_id']);
            if (empty($invoice['vat_input_rule_id'])) {
                $zone_id = $invoice_data['seller_data']['zone_id'];
                $vat_input_rule_id = $this->getInputRuleId($zone_id, $invoice_data['seller_data']['pickup_city_code'], $invoice['date_added']);
                $sql = "UPDATE oc_seller_invoice SET vat_input_rule_id = '" . (int) $vat_input_rule_id . "' ";
                $sql .= "WHERE seller_id = '" . (int) $invoice['seller_id'] . "' AND ";
                $sql .= "suborder_id = '" . $this->_db->escape($invoice['suborder_id']) . "' AND ";
                $sql .= "order_id = '" . (int) $invoice['order_id'] . "'";
                if ($this->_db->query($sql)) {
                    $invoice['vat_input_rule_id'] = $vat_input_rule_id;
                }
            }
            $invoice_data['buyer_data'] = $this->getBuyerDetails($invoice['vat_input_rule_id']);
            $meta = serialize($invoice_data);
            $sql = "UPDATE oc_seller_invoice SET seller_invoice_meta = '" . $this->_db->escape($meta) . "' ";
            $sql .= "WHERE seller_id = '" . (int) $invoice['seller_id'] . "' AND ";
            $sql .= "suborder_id = '" . $this->_db->escape($invoice['suborder_id']) . "' AND ";
            $sql .= "order_id = '" . (int) $invoice['order_id'] . "'";
            $this->_db->query($sql);
        }
        return $invoice_data;
    }

    public function getSellerId() {
        $sql = "SELECT seller_id
                FROM " . DB_PREFIX . "seller_invoice
                WHERE  seller_invoice_id = '" . (int) $this->_seller_invoice_id . "'";
        $query = $this->_db->query($sql);
        if ($query->num_rows) {
            $this->_seller_id = $query->row['seller_id'];
            return $this->_seller_id;
        }
    }

    private function updateInvoiceData() {
        $sql = "SELECT *
                FROM " . DB_PREFIX . "seller_invoice
                WHERE seller_invoice_id = '" . (int) $this->_seller_invoice_id . "'";
        $query = $this->_db->query($sql);

        $this->_invoice_data = $query->row;

        $this->_order_id = $this->_invoice_data['order_id'];
        $this->_suborder_id = $this->_invoice_data['suborder_id'];
        $this->_invoice_prefix = $this->_invoice_data['seller_invoice_prefix'];
        $this->_invoice_no = $this->_invoice_data['seller_invoice_no'];

        return 1;
    }

    public function getInvoiceInfo($order_id, $suborder_id, $seller_id) {
        $sql = "SELECT *
                FROM " . DB_PREFIX . "seller_invoice
                WHERE order_id = '" . (int) $order_id . "'
                  AND suborder_id = '" . $this->_db->escape($suborder_id) . "'
                  AND seller_id = '" . (int) $seller_id . "'";
        $query = $this->_db->query($sql);
        return $query->row;
    }

    private function getOrderData() {
        // get order no. and date_added
        $selector = array('order' => array('select' => array('order_no', 'date_added')));
        $order_info = OrderInfo::getOrderInfo($this->_db, $this->_order_id, $this->_suborder_id, $selector);
        if (!empty($order_info['order'])) {
            return $order_info['order'];
        }

        // Empty data
        return false;
    }

    public function getSellerDetails($seller_id = 0) {
        if (!empty($seller_id)) {
            $this->_seller_id = $seller_id;
        }
        $sql = "SELECT    nickname,
                          company,
                          address1,
                          address2,
                          pincode,
                          city,
                          gst_provisional_id as tin,
                          zone_id,
                          country_id,
                          pickup_city_code
                   FROM " . DB_PREFIX . "ms_seller
                   WHERE seller_id = '" . (int) $this->_seller_id . "'";
        $address_query = $this->_db->query($sql);

        if ($address_query->num_rows) {
            $address_data = $address_query->row;

            //Remove BOM chars from GST number for seller_invoice_meta
            $gst_number = $address_data['tin'] ?? '';
            $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
            $gst_number = trim($gst_number);
            $address_data['tin'] = $gst_number;

            // Getting state
            $query = $this->_db->query("SELECT name FROM " . DB_PREFIX . "zone
                                             WHERE zone_id = '" . (int) $address_data['zone_id'] . "'");
            $address_data['state'] = $query->num_rows ? $query->row['name'] : '';

            // Getting country
            $query = $this->_db->query("SELECT name FROM " . DB_PREFIX . "country
                                             WHERE country_id = '" . (int) $address_data['country_id'] . "'");
            $address_data['country'] = $query->num_rows ? $query->row['name'] : '';

            $this->_load->model('localisation/zone', 'frontend');
            if (method_exists($this->_registry, 'get')) {
                $model_zone_gstcode = $this->_registry->get('frontend_model_localisation_zone');
            } else {
                $model_zone_gstcode = $this->_registry->frontend_model_localisation_zone;
            }
            $address_data['state_code'] = $model_zone_gstcode->getZoneGSTStateCode($address_data['zone_id']);

            return $address_data;
        } else {
            return false;
        }
    }

    public function generateInvoiceNo($order_id, $suborder_id, $seller_id = '', $invoice_no = '', $invoice_date = '', $store_pickup = false) {
        $this->_order_id = $order_id;
        $this->_suborder_id = $suborder_id;
        if (!empty($seller_id)) {
            $this->_seller_id = $seller_id;
        }


        if (empty($invoice_no) && empty($invoice_date)) {
            // Check that order_id and suborder_id must have atleast one product of this seller
            $sql_check = "SELECT order_product_id
                          FROM " . DB_PREFIX . "order_product 
                          WHERE seller_id = '" . (int) $this->_seller_id . "'
                            AND order_id = '" . (int) $order_id . "'
                            AND suborder_id = '" . $this->_db->escape($suborder_id) . "'                            
                          ORDER BY order_product_id ASC LIMIT 1";
            $query_check = $this->_db->query($sql_check);
            if (empty($query_check->row['order_product_id'])) {
                return false;
            }

            //get seller detail
            $seller_details = $this->getSellerDetails($this->_seller_id);

            $seller_details['vat_input_rule_id'] = $this->getInputRuleId($seller_details['zone_id'], $seller_details['pickup_city_code']);

            // Get seller invoice prefix as per current financial year
            $financial_year = '';
            if ((int) (date('m')) <= 3) {
                $financial_year = date('Y', strtotime('-1 years')) . '-' . date('y');
            } else {
                $financial_year = date('Y') . '-' . date('y', strtotime('+1 years'));
            }
            $seller_invoice_prefix = $seller_details['nickname'] . '_' . 'WSB-' . $financial_year . '_';

            // Getting last invoice no for this prefix
            $sql_invoice_no = "SELECT seller_invoice_no
                                FROM " . DB_PREFIX . "seller_invoice
                                WHERE seller_invoice_prefix = '" . $this->_db->escape($seller_invoice_prefix) . "'
                                ORDER BY date_added DESC LIMIT 1 ";
            $query_invoice_no = $this->_db->query($sql_invoice_no);
            if ($query_invoice_no->num_rows) {
                $new_invoice_no = (int) ($query_invoice_no->row['seller_invoice_no']) + 1;
            } else {
                $new_invoice_no = 1;
            }

            //$admin_generated means operations team are generated invoice and it's value is default 1 
            $admin_generated = 1;
            // Insert new invoice no into db
            $seller_invoice_id = $this->insertInvoiceNO($seller_details, $new_invoice_no, '', $seller_invoice_prefix, $admin_generated, $store_pickup);
            if ($seller_invoice_id) {
                $file_name = array();
                $file_name['seller_invoice_id'] = $this->_db->escape($seller_invoice_id);
                $file_name['seller_invoice_prefix'] = $this->_db->escape($seller_invoice_prefix);
                $file_name['seller_invoice_no'] = $new_invoice_no;
                $file_name['order_id'] = $this->_order_id;
                $file_name['suborder_id'] = $this->_suborder_id;
                return $file_name;
            }
        } else if(!empty($invoice_no) && !empty($invoice_date)) {

            $seller_details = $this->getSellerDetails($this->_seller_id);
            $seller_details['vat_input_rule_id'] = $this->getInputRuleId($seller_details['zone_id'], $seller_details['pickup_city_code']);
            $seller_invoice_prefix = '';
            // Insert new invoice no into db
            // $result = $this->insertInvoiceNO($seller_details, $new_invoice_no, $seller_invoice_prefix);
            $seller_invoice_id = $this->insertInvoiceNO($seller_details, $invoice_no, $invoice_date, $seller_invoice_prefix);

            if ($seller_invoice_id) {
                $file_name = array();
                // $file_name['order_id'] = $this->_order_id;
                // $file_name['suborder_id'] = $this->_suborder_id;

                $file_name['seller_invoice_id'] = $seller_invoice_id;
                $file_name['seller_invoice_prefix'] = $this->_db->escape($seller_invoice_prefix);
                $file_name['seller_invoice_no'] = $invoice_no;
                $file_name['order_id'] = $this->_order_id;
                $file_name['suborder_id'] = $this->_suborder_id;
                return $file_name;
            } else {
                return $seller_invoice_id;
            }
        }
        else
        {
          return false;
        }
    }

    public function getInputRuleId($seller_zone_id, $pickupCityCode, $date_added = '') {
        $date_added = empty($date_added) ? date('Y-m-d') : date('Y-m-d', strtotime($date_added));
        $sql = "SELECT `rule_id`
                FROM " . DB_PREFIX . "vat_input_rules
                WHERE `zone_id` = '" . (int) $seller_zone_id . "'
                  AND DATE(`date_begin`) <= DATE('" . $this->_db->escape($date_added) . "')
                  AND ( `date_end` IS NULL OR DATE(`date_end`) >= DATE('" . $this->_db->escape($date_added) . "') )
                  AND `status` = 1 ";

        if ($pickupCityCode != '') {
            $sql .= " AND `pickup_city_code`='" . $this->_db->escape(strtoupper(trim($pickupCityCode))) . "' ";
        }
        $result = $this->_db->query($sql);

        if ($result->num_rows > 0) {
            return $result->row['rule_id'];
        } else {
            $sql = "SELECT `rule_id`
                    FROM " . DB_PREFIX . "vat_input_rules
                    WHERE `status` = 1
                    AND DATE(`date_begin`) <= DATE('" . $this->_db->escape($date_added) . "')
                    AND ( `date_end` IS NULL OR DATE(date_end) >= DATE('" . $this->_db->escape($date_added) . "') )
                    AND `is_default` = 1";

            $result = $this->_db->query($sql);
            if ($result->num_rows > 0) {
                return $result->row['rule_id'];
            } else {
                throw new Exception("There is no match for rule id for zone_id: $seller_zone_id and date: $date_added");
            }
        }
    }

    public function getBuyerDetails($vat_input_rule_id) {
        $sql = "SELECT purchase_firm_name as company,
                       purchase_firm_address1 as address1,
                       purchase_firm_address2 as address2,
                       purchase_firm_city as city,
                       purchase_firm_pincode as pincode,
                       purchase_firm_tin_no as tin,
                       purchase_firm_state as state,
                       purchase_firm_country as country,
                       purchase_firm_zone_id as zone_id
                FROM oc_vat_input_rules
                WHERE rule_id = $vat_input_rule_id";

        $result = $this->_db->query($sql);

        if ($result->num_rows > 0) {
            $result = $result->row;
            $result['email'] = 'info@wholesalebox.in';

            //Remove BOM chars from GST number for seller_invoice_meta
            $gst_number = $result['tin'] ?? '';
            $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
            $gst_number = trim($gst_number);
            $result['tin'] = $gst_number;

            $this->_load->model('localisation/zone', 'frontend');
            if (method_exists($this->_registry, 'get')) {
                $model_zone_gstcode = $this->_registry->get('frontend_model_localisation_zone');
            } else {
                $model_zone_gstcode = $this->_registry->frontend_model_localisation_zone;
            }
            $result['state_code'] = $model_zone_gstcode->getZoneGSTStateCode($result['zone_id']);
            return $result;
        }
    }

    private function insertInvoiceNO($seller_details, $new_invoice_no, $invoice_date, $seller_invoice_prefix, $admin_generated = 0, $store_pickup = false) {
        try {
            $this->_db->query(" START TRANSACTION ");

            if ($invoice_date) {
                $invoice_date = date('Y-m-d', strtotime(str_replace('/', '-', $invoice_date)));
            } else {
                $invoice_date = date('Y-m-d');
            }

            $seller_meta['seller_data'] = $seller_details;
            $seller_meta['buyer_data'] = $this->getBuyerDetails($seller_details['vat_input_rule_id']);
            $seller_meta = serialize($seller_meta);

            $sql_insert_new_invoice = "INSERT INTO " . DB_PREFIX . "seller_invoice
                                       SET order_id = '" . (int) $this->_order_id . "',
                                           suborder_id = '" . $this->_db->escape($this->_suborder_id) . "',
                                           seller_id = '" . (int) $this->_seller_id . "',
                                           seller_invoice_prefix = '" . $this->_db->escape($seller_invoice_prefix) . "',
                                           seller_invoice_no = '" . $this->_db->escape($new_invoice_no) . "',
                                           vat_input_rule_id = '" . (int) $seller_details['vat_input_rule_id'] . "',
                                           date_added = '" . $this->_db->escape($invoice_date) . "',
                                           seller_invoice_meta = '" . $this->_db->escape($seller_meta) . "'";

            $result = $this->_db->query($sql_insert_new_invoice);
            $invoice_inserted_id = $this->_db->getLastId();
            if ($admin_generated) {
                $update_ordr_prdct_sllr_invc_id = " UPDATE " . DB_PREFIX . "order_product
                                                    SET seller_invoice_id = '" . (int) $invoice_inserted_id . "',
                                                        edit_type = '" . $this->_db->escape('SELLER_APPROVED') . "'     
                                                    WHERE seller_id = '" . (int) $this->_seller_id . "'
                                                      AND order_id = '" . (int) $this->_order_id . "'
                                                      AND suborder_id = '" . $this->_db->escape($this->_suborder_id) . "'
                                                      AND edit_type IN ('YES', 'SELLER_PARTIAL')                                                       AND (seller_invoice_id=0 OR seller_invoice_id IS NULL)";
                $this->_db->query($update_ordr_prdct_sllr_invc_id);

                if ($store_pickup) {
                    $update_ordr_prdct_pickup_status = " UPDATE " . DB_PREFIX . "order_product  
                                                        SET pickup_status = 'Received', 
                                                            pickup_last_modified = NOW() 
                                                        WHERE order_id = '" . (int) $this->_order_id . "'
                                                          AND suborder_id = '" . $this->_db->escape($this->_suborder_id) . "'
                                                          AND pickup_status NOT IN ('Received', 'Issue') 
                                                          AND edit_type IN ('YES', 'SELLER_APPROVED', 'SELLER_PARTIAL')
                                                          AND seller_id = '" . (int) $this->_seller_id . "'";
                    $this->_db->query($update_ordr_prdct_pickup_status);
                }
            }

            if (!$invoice_inserted_id) {
                return false;
            }

            $this->_db->query(" COMMIT ");

            if (!$result) {
                return false;
            }

            return $invoice_inserted_id;
        } catch (Exception $e) {
            $this->_db->query(" ROLLBACK ");
            echo $e->getMessage();
        }
    }

    /* Method for calculate product price and set and many more
     * @param: $controller: reference of controller
     * @param: order_id: Integer order id
     * @param: order_id: String suborder id
     * @param: order_id: Integer seller id
     * @param: product_field: array of product field with product_ids and total piece
     * @output: return an array of calculations of products
     * @author: Vikas, 2016
     */

    public function calculationProducts($controller, $order_id, $suborder_id, $seller_id, $product_field = array(), $seller_invoice_id = '') {

        $op_quantity_map = array();
        if (!empty($product_field)) {
            $order_product_id = array_column($product_field, 'order_product_id');
            $op_quantity_map = array_combine(
                    array_column($product_field, 'order_product_id'), array_column($product_field, 'product_quantity')
            );
        }

        if (method_exists($controller, 'get')) {
            $db = $controller->get('db');
            $format = $controller->get('currency');
        } else {
            $db = $controller->db;
            $format = $controller->currency;
        }

        // getting order products for this seller
        $sql_product = "SELECT oop.product_id,
		                       oop.seller_sku,
		                       oop.hsn_code,
                               oop.name,
							   oop.quantity,
							   oop.piece_in_set,
							   oop.transfer_price_per_piece,
							   oop.seller_input_tax,
							   oop.order_product_id,
							   oop.seller_cst,
                               oo.name as option_name,
                               oo.value as option_value 
						FROM " . DB_PREFIX . "order_product oop ";
        $sql_product .= " LEFT JOIN " . DB_PREFIX . "order_option oo ON oo.order_product_id = oop.order_product_id ";
        if (empty($product_field)) {
            $sql_product .= "WHERE oop.order_id = '" . (int) $order_id . "'
                               AND oop.suborder_id = '" . $db->escape($suborder_id) . "'
                               AND oop.seller_id = '" . (int) $seller_id . "'";
            if (!empty($seller_invoice_id)) {
                $sql_product .= " AND oop.seller_invoice_id = '" . (int) $seller_invoice_id . "'";
            }
        } else {
            $sql_product .= "WHERE oop.order_product_id IN (" . implode(',', $order_product_id) . ")";
        }

        $product_query = $db->query($sql_product);

        // if no products, then no invoice can be generated
        if (!($product_query->num_rows)) {
            return false;
        }


        foreach ($product_query->rows as $key => $product_data) {
            $category_ids = $this->getCategories($db, $product_data['product_id']);
            $category_id = 0;
            foreach ($category_ids as $cat_id) {

                if ($cat_id['category_id'] != 0) {
                    $category_id = $cat_id['category_id'];
                    break;
                }
            }

            $category_query = $db->query("SELECT name FROM " . DB_PREFIX . "category_description
                                                      WHERE category_id = '" . (int) $category_id . "' AND language_id = '1'");

            $product_query->rows[$key]['category_name'] = ($category_query->num_rows ? $category_query->row['name'] : '');
        }
        $table_product_data = array();
        $total_product_amount = 0;
        $total_product_set = 0;
        $total_product_tax = 0;
        $total_product_pieces = 0;
        $grand_total = 0;

        foreach ($product_query->rows as $product_info) {

            $transfer_price_per_piece = (float) $product_info['transfer_price_per_piece'];
            $seller_tax = (float) $product_info['seller_input_tax'];
            $total_pieces = (int) $product_info['quantity'] * (int) $product_info['piece_in_set'];

            $rate_per_piece = round($transfer_price_per_piece / (1 + $seller_tax / 100), 2);
            $tax_piece = round($transfer_price_per_piece - $rate_per_piece, 2);

            // In case pieces are specified separately, such as in the case of debit note

            if (!empty($product_field)) {
                $total_pieces = (int) $op_quantity_map[$product_info['order_product_id']];
            }

            $amount = round(($rate_per_piece * $total_pieces), 2);
            $tax = round($tax_piece * $total_pieces, 2);

            // $product_name = $product_info['seller_sku'];
            // if ( !empty($product_info['category_name']) ) {
            //  $product_name .= (' - ' . $product_info['category_name']);
            // }

            $table_product_data['product_data'][] = array(
                'order_product_id' => (int) $product_info['order_product_id'],
                'product_sku' => $product_info['seller_sku'],
                'product_name' => $product_info['name'],
                'quantity' => (int) $product_info['quantity'],
                'piece_in_set' => (int) $product_info['piece_in_set'],
                'total_pieces' => (int) $total_pieces,
                'transfer_price_per_piece' => $transfer_price_per_piece,
                'seller_tax' => ($product_info['seller_cst'] && $seller_tax) ? ('CST (' . number_format($seller_tax, 2, '.', '') . '%)') : ($seller_tax ? number_format($seller_tax, 2, '.', '') . '%' : '--'),
                'rate_per_piece' => $rate_per_piece,
                'tax' => $tax ? $tax : '--',
                'amount' => $amount,
                'hsn_code' => $product_info['hsn_code'],
                'discount' => 0.00,
                'option_name' => $product_info['option_name'],
                'option_value' => $product_info['option_value']
            );

            $total_product_set += (int) $product_info['quantity'];
            $total_product_tax += $tax;
            $total_product_pieces += (int) $total_pieces;
            $total_product_amount += $amount;
        }
        $grand_total = $total_product_tax + $total_product_amount;
        $table_product_data['total_set'] = $total_product_set;
        $table_product_data['total_pieces'] = $total_product_pieces;
        $table_product_data['total_tax'] = $format->format($total_product_tax, 'INR', 1);
        $table_product_data['total_amount'] = $format->format($total_product_amount, 'INR', 1);
        $table_product_data['grand_total'] = $format->format($grand_total, 'INR', 1);

        $table_product_data['amount_in_word'] = convert_to_currency_indian_format($grand_total);

        return array(
            'table_product_data' => $table_product_data
        );
    }

    public function generateSellerInvoiceHTml($order_info) {
        $order_no = $order_info['order_data']['order_no'];
        $date_added = date('d-m-Y', strtotime($order_info['order_data']['date_added']));
        $invoice_no = $this->_invoice_prefix . $this->_invoice_no;
        $invoice_date = date('d-m-Y', strtotime($order_info['invoice_data']['date_added']));

        $order['product'] = $order_info['order_product'];
        $order['total'] = $order_info['order_total'];

        $seller_data = $order_info['invoice_data']['seller_data'];

        $payment_city = !empty($seller_data['city']) ? $seller_data['city'] : '';

        $tin_number = '';
        if (!empty($seller_data['tin'])) {
            $tin_number = $seller_data['tin'];
        }

        $seller_details = '';

        $seller_details = !empty($seller_data['company']) ? '<b>' . trim($seller_data['company']) . '</b><br>' : '';
        $seller_details .= '<b>' . $seller_data['address1'] . ',<br>' . $seller_data['address2'] . ',</b><br>';
        $seller_details .= '<b>' . $seller_data['city'] . '-' . $seller_data['pincode'] . '</b><br>';
        $seller_details .= '<b>' . $seller_data['state'] . ',' . $seller_data['country'] . '</b><br>';
        $seller_details .= 'Tin No: <b>' . trim($tin_number) . '</b><br>';

        $buyer_data = $order_info['invoice_data']['buyer_data'];

        $tbl = '<table width="100%" border="1" cellspacing="0" cellpadding="4">';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="40%" rowspan="4">' . $seller_details . ' </td>';
        $tbl .= '<td width="20%" >Original</td>';
        $tbl .= '<td width="20%" rowspan="2">Duplicate for Sellers</td>';
        $tbl .= '<td width="20%" rowspan="2">Triplicate for Transporter</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="20%" >For Sellers </td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%">Inv No: <b>' . $invoice_no . '</b></td>';
        $tbl .= '<td width="30%">Dated: <b>' . $invoice_date . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="60%">';
        $tbl .= '<b>BUYER: </b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . $buyer_data['company'] . '</b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . $buyer_data['address1'] . ', </b><br>';
        if (!empty($buyer_data['address2'])) {
            $tbl .= '<b> &nbsp;&nbsp;' . $buyer_data['address2'] . ', </b><br>';
        }
        $tbl .= '<b> &nbsp;&nbsp;' . $buyer_data['city'] . ' - ' . $buyer_data['pincode'] . ' </b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . $buyer_data['state'] . ' - ' . $buyer_data['country'] . '</b><br>';
        $tbl .= '&nbsp;&nbsp;Tin No: <b>' . $buyer_data['tin'] . '</b>';
        $tbl .= '</td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="40%" >Booked From: <b>' . $payment_city . '</b>  </td>';
        $tbl .= '<td width="30%" >Order No: <b>' . $order_no . '</b> </td>';
        $tbl .= '<td width="30%" >Dated: <b>' . $date_added . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="8%" align="center"><b>S. No.</b></td>';
        $tbl .= '<td width="32%" align="center"><b>Description</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Pcs / Set</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Total Pcs</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Total Sets</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Rate / Pc</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Tax Rate</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Tax</b></td>';
        $tbl .= '<td width="12%" align="center"><b>Amount</b></td>';
        $tbl .= '</tr>';
        $i = 1;
        $table_product = $order_info['total_data']['table_product_data'];
        foreach ($table_product['product_data'] as $product) {
            $tbl .= '<tr>';
            $tbl .= '<td width="8%" align="center">' . $i . '</td>';
            $tbl .= '<td width="32%" align="center">' . $product['product_name'] . '</td>';
            $tbl .= '<td width="8%" align="center">' . $product['piece_in_set'] . '</td>';
            $tbl .= '<td width="8%" align="center">' . $product['total_pieces'] . '</td>';
            $tbl .= '<td width="8%" align="center">' . $product['quantity'] . '</td>';
            $tbl .= '<td width="8%" align="right">' . number_format($product['rate_per_piece'], 2, '.', '') . '</td>';
            $tbl .= '<td width="8%" align="center">' . $product['seller_tax'] . '</td>';
            $tbl .= '<td width="8%" align="right">' . number_format($product['tax'], 2, '.', '') . '</td>';
            $tbl .= '<td width="12%" align="right">' . number_format($product['amount'], 2, '.', '') . '</td>';
            $tbl .= '</tr>';
            $i++;
        }

        // Html Row for order['total']
        $tbl .= '<tr>';
        $tbl .= '<td width="8%"></td>';
        $tbl .= '<td width="32%" align="right"><b>Total</b></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center">' . $table_product['total_pieces'] . '</td>';
        $tbl .= '<td width="8%" align="center">' . $table_product['total_set'] . '</td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"><b>' . $table_product['total_tax'] . '</b></td>';
        $tbl .= '<td width="12%" align="right"><b>' . $table_product['total_amount'] . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="8%"></td>';
        $tbl .= '<td width="32%" align="right"><b>Grand Total</b></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="8%" align="center"></td>';
        $tbl .= '<td width="12%" align="right"><b>' . $table_product['grand_total'] . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="100%"><b>' . $table_product['amount_in_word'] . '</b></td>';
        $tbl .= '</tr>';


        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left">';
        $tbl .= '<table>';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="60%">';
        $tbl .= 'Our VAT Reg. No: <b>' . $tin_number . ' </b><br>';
        $tbl .= 'All payments are to be made By A/C payee cheque / Draft Only.<br>';
        $tbl .= ' </td>';
        $tbl .= '<td width="40%" align="center">E & EO <br> For ' . $seller_data['company'] . ' <br><br><br><br><br>Authorised Signature<br></td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';

        //return  array('html' => $tbl, 'invoice_no' => $invoice_no );

        require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');
        //$action = 'I';
        require_once(DIR_SYSTEM . 'library/tcpdf/config/tcpdf_config.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle('Seller Invoice');
        $pdf->SetSubject('Seller Invoice');
        $pdf->SetKeywords('Seller Invoice');

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
        $pdf->SetFont('times', 'B', 20);

        // add a page
        $pdf->AddPage();

        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->writeHTML($tbl, true, false, false, false, '');

        // -----------------------------------------------------------------------------
        // Table with rowspans and THEAD
        // -----------------------------------------------------------------------------
        //Close and output PDF document
        if (!file_exists(DIR_DLOAD_SLR_INV)) {
            mkdir(DIR_DLOAD_SLR_INV, 0777, true);
        }
        $file_name = DIR_DLOAD_SLR_INV . '' . $this->_order_no . $invoice_no . '.pdf';
        $action = 'F';
        $pdf->Output($file_name, $action);
        $file_name = $this->_order_no . $invoice_no . '.pdf';
        return base64_encode($file_name);
    }

    /**
     * Method for get categories when we don't use any load model
     * @param $product_id : Interger of oproduct id
     * @param $db: objects of controller
     * @return category name
     * @author Vikas/Sudhanshu, 2016
     * */
    public function getCategories($db, $product_id) {
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");
        return $query->rows;
    }

    /**
     * Method for calculation of product of particular orders
     * @param $data = array of product information
     * @return array of product information with calculation
     * @author Vikas, 2017
     * */
    public static function getOrderProductWiseCalculation($data = array()) {
        $set = $data['quantity'];
        $tp = $data['transfer_price_per_piece'];
        $no_of_piece = $data['quantity'] * $data['piece_in_set'];
        $total_amount = $tp * $no_of_piece;
        $price_per_piece = round($tp / (1 + (float) $data['seller_input_tax'] / 100), 2);
        $product_amount = $price_per_piece * $no_of_piece;
        $vat_cst_amount = round($total_amount - $product_amount, 2);
        $vat_cst_rate = (float) $data['seller_input_tax'] . ' %';

        return array(
            'sets' => $set,
            'no_of_piece' => $no_of_piece,
            'price_per_piece' => $price_per_piece,
            'product_amount' => $product_amount,
            'vat_cst_rate' => $vat_cst_rate,
            'vat_cst_amount' => $vat_cst_amount,
            'total_amount' => $total_amount
        );
    }

    /**
     * Method for GST Format Pdf
     * @param : $order_info : arrays of data
     * @param : $controller : object of that class
     * @author: vikas, 2017
     */
    public function gstFormatInvoiceHtmlPdf($controller, $order_info) {
        if (method_exists($controller, 'get')) {
            $db = $controller->get('db');
            $format = $controller->get('currency');
        } else {
            $db = $controller->db;
            $format = $controller->currency;
        }


        $order_no = $order_info['order_data']['order_no'];
        $date_added = date('d-m-Y', strtotime($order_info['order_data']['date_added']));
        $invoice_no = $this->_invoice_prefix . $this->_invoice_no;
        $invoice_date = date('d-m-Y', strtotime($order_info['invoice_data']['date_added']));

        $seller_data = $order_info['invoice_data']['seller_data'];
        $buyer_data = $order_info['invoice_data']['buyer_data'];

        $payment_city = !empty($seller_data['city']) ? $seller_data['city'] : '';

        $tin_number = '';
        if (!empty($seller_data['tin'])) {
            $tin_number = $seller_data['tin'];
        }


        $seller_details = '';

        $seller_details = !empty($seller_data['company']) ? '<b>' . ucwords(trim($seller_data['company'])) . '</b><br>' : '';
        $seller_details .= !empty($seller_data['address1']) ? '<b>' . ucwords(trim($seller_data['address1'])) . ' ,</b><br>' : '';
        $seller_details .= !empty($seller_data['address2']) ? '<b>' . ucwords(trim($seller_data['address2'])) . ' ,</b><br>' : '';
        $seller_details .= '<b>' . ucwords($seller_data['city']) . ' - ' . $seller_data['pincode'] . '</b><br>';
        $seller_details .= '<b>' . ucwords($seller_data['state']) . ', ' . ucwords($seller_data['country']) . '</b><br>';
        $seller_details .= '<br>GSTIN/UIN: <b>' . trim($tin_number) . '</b><br>';
        $seller_details .= '<br><span style="text-align:right;">State Code : <b>' . $seller_data['state_code'][0]['gst_state_code'] . '</b></span>';




        $tbl  = '<p style="text-align:center;"><h1>TAX INVOICE</h1></p>' ;
        $tbl .= '<table width="100%" border="1" cellspacing="0" cellpadding="4">';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="30%" rowspan="4">' . $seller_details . ' </td>';
        $tbl .= '<td width="25%" >Original For Sellers</td>';
        $tbl .= '<td width="25%" >Duplicate for Sellers</td>';
        $tbl .= '<td width="20%" >Triplicate for Transporter</td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="35%" >Inv. No.: <b> ' . $invoice_no . '</b></td>';
        $tbl .= '<td width="35%" >Inv. Date: <b> ' . $invoice_date . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="35%" >P.O. No.: <b>' . $order_no . '</b></td>';
        $tbl .= '<td width="35%" >P.O. Date: <b>' . $date_added . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="35%">';
        $tbl .= '<b>Billed To: </b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['company'])) . '</b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['address1'])) . ', </b><br>';
        if (!empty($buyer_data['address2'])) {
            $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['address2'])) . ', </b><br>';
        }
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['city'])) . ' - ' . $buyer_data['pincode'] . ' </b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['state'])) . ' - ' . ucwords(strtolower($buyer_data['country'])) . '</b><br>';
        $tbl .= '<br>&nbsp;&nbsp;GSTIN/UIN: <b>' . $buyer_data['tin'] . '</b>';
        $tbl .= '<br><span style="text-align:right;">State Code : <b>' . $buyer_data['state_code'][0]['gst_state_code'] . '</b></span>';
        $tbl .= '</td>';

        $tbl .= '<td width="35%">';
        $tbl .= '<b>Shipped To: </b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['company'])) . '</b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['address1'])) . ', </b><br>';
        if (!empty($buyer_data['address2'])) {
            $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['address2'])) . ', </b><br>';
        }
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['city'])) . ' - ' . $buyer_data['pincode'] . ' </b><br>';
        $tbl .= '<b> &nbsp;&nbsp;' . ucwords(strtolower($buyer_data['state'])) . ' - ' . ucwords(strtolower($buyer_data['country'])) . '</b><br>';
        $tbl .= '<br>&nbsp;&nbsp;GSTIN/UIN: <b>' . $buyer_data['tin'] . '</b>';
        $tbl .= '<br><span style="text-align:right;">State Code : <b>' . $buyer_data['state_code'][0]['gst_state_code'] . '</b></span>';
        $tbl .= '</td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="6%" align="center"><b>S. No.</b></td>';
        $tbl .= '<td width="30%" align="center"><b>Description</b></td>';
        $tbl .= '<td width="12%" align="center"><b>HSN</b></td>';
        $tbl .= '<td width="7%" align="center"><b>Qty</b></td>';
        $tbl .= '<td width="7%" align="center"><b>Unit of Meas.</b></td>';
        // $tbl .=        '<td width="8%" align="center"><b>Total Sets</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Rate</b></td>';
        $tbl .= '<td width="10%" align="center"><b>Amount</b></td>';
        $tbl .= '<td width="8%" align="center"><b>Taxable Rate</b></td>';
        $tbl .= '<td width="12%" align="center"><b>Taxable Value</b></td>';
        $tbl .= '</tr>';
        $i = 1;
        $table_product = $order_info['total_data']['table_product_data'];
        $total_taxable_value = 0;
        foreach ($table_product['product_data'] as $product) {
            $product_option = !empty($product['option_name']) ? ' - ' . $product['option_name'] . ' : ' . $product['option_value'] : '';
            $tbl .= '<tr>';
            $tbl .= '<td width="6%" align="center">' . $i . '</td>';
            $tbl .= '<td width="30%" align="center"><b>' . $product['product_sku'] . '</b><br><i>' . $product['product_name'] . $product_option . '</i></td>';
            // $tbl .=        '<td width="8%" align="center">'.$product['piece_in_set'].'</td>';
            $tbl .= '<td width="12%" align="center">' . $product['hsn_code'] . '</td>';
            $tbl .= '<td width="7%" align="center">' . $product['total_pieces'] . '</td>';
            $tbl .= '<td width="7%" align="center">Piece</td>';
            // $tbl .=        '<td width="8%" align="center">'.$product['quantity'].'</td>';
            $tbl .= '<td width="8%" align="right">' . number_format($product['rate_per_piece'], 2, '.', '') . '</td>';
            $tbl .= '<td width="10%" align="right">' . number_format($product['amount'], 2, '.', '') . '</td>';
            $tbl .= '<td width="8%" align="center">' . $product['seller_tax'] . '</td>';
            // $tbl .=        '<td width="12%" align="right">'.number_format($product['tax'], 2, '.', '').'</td>';
            $tbl .= '<td width="12%" align="right">' . number_format($product['amount'], 2, '.', '') . '</td>';
            $tbl .= '</tr>';
            $total_taxable_value += $product['tax'];
            $i++;
        }

        if ($seller_data['state_code'][0]['gst_state_code'] == $buyer_data['state_code'][0]['gst_state_code']) {

            // Handling cases like 4.81 as tax value for division by 2
            //$temp = ((int)($total_taxable_value*1000))/10;
            $temp = round($total_taxable_value * 100);
            if ($temp % 2 == 0) {
                $cgst = $format->format($total_taxable_value / 2, 'INR', 1);
                $sgst = $format->format($total_taxable_value / 2, 'INR', 1);
            } else {
                $cgst = $format->format($total_taxable_value / 2, 'INR', 1, true, 3);
                $sgst = $format->format($total_taxable_value / 2, 'INR', 1, true, 3);
            }

            $igst = '-';
        } else {
            $cgst = '-';
            $sgst = '-';
            $igst = $format->format($total_taxable_value, 'INR', 1);
        }

        // Total taxable value with CGST, SGST, IGST
        $tbl .= '<tr>';
        $tbl .= '<td width="80%" align="right" colspan="7"><b>Total Taxable Value (a) </b></td>';
        $tbl .= '<td width="20%" align="right"><b>' . $table_product['total_amount'] . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="80%" align="right" colspan="7"><b>Add: CGST (b) </b></td>';
        $tbl .= '<td width="20%" align="right"><b>' . $cgst . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="80%" align="right" colspan="7"><b>Add: SGST (c) </b></td>';
        $tbl .= '<td width="20%" align="right"><b>' . $sgst . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="80%" align="right" colspan="7"><b>Add: IGST (d) </b></td>';
        $tbl .= '<td width="20%" align="right"><b>' . $igst . '</b></td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td width="80%" align="right" colspan="7"><b>Total GST (b+c+d) </b></td>';
        $tbl .= '<td width="20%" align="right"><b>' . $format->format($total_taxable_value) . '</b></td>';
        $tbl .= '</tr>';
        // Html Row for order['total']
        $tbl .= '<tr>';
        $tbl .= '<td width="80%" align="right" colspan="7"><b>Total Amount(inc. Tax) (a+b+c+d) </b></td>';
        $tbl .= '<td width="20%" align="right"><b>' . $table_product['grand_total'] . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<td width="100%"><b>' . $table_product['amount_in_word'] . '</b></td>';
        $tbl .= '</tr>';


        $tbl .= '<tr>';
        $tbl .= '<td width="100%" align="left">';
        $tbl .= '<table>';
        $tbl .= '<tbody>';
        $tbl .= '<tr>';
        $tbl .= '<td width="70%">';

        $tbl .= '<table width="100%" border="1" cellspacing="0" cellpadding="4">';
        $tbl .= '<tbody>';
        $tbl .= '<tr><td colspan="6" align="center"><b>HSN Code Wise Summary</b></td></tr>';
        $tbl .= '<tr>';
        $tbl .= '<td align="center">HSN</td>';
        $tbl .= '<td align="center">Product Value</td>';
        $tbl .= '<td align="center">Rate</td>';
        $tbl .= '<td align="center">CGST</td>';
        $tbl .= '<td align="center">SGST</td>';
        $tbl .= '<td align="center">IGST</td>';
        $tbl .= '</tr>';
        // hsn code wise summary
        $hsnsummary = $this->getSellerHSNCodeWiseDetails($table_product['product_data'], $seller_data['state_code'][0]['gst_state_code'], $buyer_data['state_code'][0]['gst_state_code'], $format);
        $total_product_value = 0;
        $total_sgst = 0;
        $total_cgst = 0;
        $total_igst = 0;
        foreach ($hsnsummary as $hsn_key => $hsn_arr) {
            foreach ($hsn_arr as $tax_rate => $hsn_values) {
                $tbl .= '<tr>';
                $tbl .= '<td align="center">' . $hsn_key . '</td>';
                $tbl .= '<td align="center">' . round($hsn_values['product_val'], 2) . '</td>';
                $tbl .= '<td align="center">' . $hsn_values['rate'] . '</td>';
                $tbl .= '<td align="center">' . (($hsn_values['cgst']) ? $hsn_values['cgst'] : "-") . '</td>';
                $tbl .= '<td align="center">' . (($hsn_values['sgst']) ? $hsn_values['sgst'] : "-") . '</td>';
                $tbl .= '<td align="center">' . (($hsn_values['igst']) ? $hsn_values['igst'] : "-") . '</td>';
                $tbl .= '</tr>';
                $total_product_value += $hsn_values['product_val'];
                $total_cgst += $hsn_values['cgst'];
                $total_sgst += $hsn_values['sgst'];
                $total_igst += $hsn_values['igst'];
            }
        }

        $tbl .= '<tr>';
        $tbl .= '<td align="center"><b>Total</b></td>';
        $tbl .= '<td align="center"><b>' . $total_product_value . '</b></td>';
        $tbl .= '<td align="center"></td>';
        $tbl .= '<td align="center"><b>' . (!empty($total_cgst) ? $total_cgst : "-") . '</b></td>';
        $tbl .= '<td align="center"><b>' . (!empty($total_sgst) ? $total_sgst : "-") . '</b></td>';
        $tbl .= '<td align="center"><b>' . (!empty($total_igst) ? $total_igst : "-") . '</b></td>';
        $tbl .= '</tr>';

        $tbl .= '</tbody>';
        $tbl .= '</table >';
        $tbl .= ' </td>';
        $tbl .= '<td width="30%" align="center">E & OE <br> For ' . $seller_data['company'] . ' <br><br><br><br>Authorised Signature<br></td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        $tbl .= '</td>';
        $tbl .= '</tr>';
        $tbl .= '</tbody>';
        $tbl .= '</table>';
        //return  array('html' => $tbl, 'invoice_no' => $invoice_no );

        require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');
        //$action = 'I';
        require_once(DIR_SYSTEM . 'library/tcpdf/config/tcpdf_config.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$html2pdf = new MyHtml2Pdf('P','A4','en', true, 'UTF-8');
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle('Seller Invoice');
        $pdf->SetSubject('Seller Invoice');
        $pdf->SetKeywords('Seller Invoice');

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
        //$pdf->SetFont($fontname, '', 9, '', 'false');

        // add a page
        $pdf->AddPage();

        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

        $pdf->writeHTML($tbl, true, false, false, false, '');

        // -----------------------------------------------------------------------------
        // Table with rowspans and THEAD
        // -----------------------------------------------------------------------------
        //Close and output PDF document
        if (!file_exists(DIR_DLOAD_SLR_INV)) {
            mkdir(DIR_DLOAD_SLR_INV, 0777, true);
        }

        $base_file_name = time() . '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $invoice_no) . '.pdf';
        $action = 'F';
        $pdf->Output(DIR_DLOAD_SLR_INV . $base_file_name, $action);
        return base64_encode($base_file_name);
    }

    /**
     * Method for HSN Summary
     * @param : $products : array of products
     * @param : $seller_state : state code of seller
     * @param : $seller_state : state code of seller
     * @return : array of hsn summary
     * @author : vikas, 2017
     * */
    private function getSellerHSNCodeWiseDetails($products, $seller_state, $buyer_state, $format) {
        $hsn_code_wise_arr = array();
        foreach ($products as $key => $value) {
            $sgst_cgst_tax = 0;
            $igst_tax = 0;
            $piece = $value['quantity'] * $value['piece_in_set'];
            $rate_per_piece = $value['rate_per_piece'];
            $rate_output_input = $value['seller_tax'];
            $product_value = (float) ($piece * $rate_per_piece);

            if ($seller_state == $buyer_state) {
                $temp = round($value['tax'] * 100);
                if ($temp % 2 == 0) {
                    $sgst_cgst_tax = ROUND($value['tax'] / 2, 2);
                } else {
                    $sgst_cgst_tax = ROUND($value['tax'] / 2, 3);
                }
            } else {
                $igst_tax = $value['tax'];
            }
            if (!empty($hsn_code_wise_arr)) {
                if (array_key_exists($value['hsn_code'], $hsn_code_wise_arr)) {
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['product_val'] += (float) $product_value;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['sgst'] += (float) $sgst_cgst_tax;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['cgst'] += (float) $sgst_cgst_tax;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['igst'] += (float) $igst_tax;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
                } else {
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['product_val'] = $product_value;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['sgst'] = (float) $sgst_cgst_tax;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['cgst'] = (float) $sgst_cgst_tax;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['igst'] = (float) $igst_tax;
                    $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
                }
            } else {
                $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['product_val'] = $product_value;
                $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['sgst'] = (float) $sgst_cgst_tax;
                $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['cgst'] = (float) $sgst_cgst_tax;
                $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['igst'] = (float) $igst_tax;
                $hsn_code_wise_arr[$value['hsn_code']][$value['seller_tax']]['rate'] = number_format((float) $rate_output_input, 2) . ' %';
            }
        }
        return $hsn_code_wise_arr;
    }

    /**
     * Public functio to get multiple invoices by multiple seller_invoices
     * @param: $order_id, $suborder_ids, $seller_id
     * @return: array
     * @author: Nishu, Nov 2017
     */
    public function getSellerInvoices($order_id, $suborder_ids, $seller_id = 0) {
        $invoices = array();
        if(empty($suborder_ids)){
            return $invoices;
        }
        $suborder_id = join("','", $suborder_ids);
        $whr = '';
        if(!empty($seller_id)){
            $whr = " AND seller_id = '" . (int) $seller_id . "'";
        }
        $sql = "SELECT *
                FROM " . DB_PREFIX . "seller_invoice
                WHERE order_id = '" . (int) $order_id . "'
                  AND suborder_id IN ('" . $suborder_id . "')
                  ". $whr;
        $query = $this->_db->query($sql);
        if ($query->num_rows > 0) {
            $invoices = $query->rows;
        }
        return $invoices;
    }
    /**
     * Public functio to get multiple invoices by multiple seller_invoices
     * @param: $seller_invoice_id, It can be single id OR array of multiple invoice ids
     * @return: array
     * @author: Nishu, Nov 2017
     */
    public function getSellerInvoiceById($seller_invoice_id) {
       $invoice = array();
       if(empty($seller_invoice_id)) {
            return $invoice;
       }
       if(is_array($seller_invoice_id)) {
            $seller_invoice_id = implode(',', $seller_invoice_id);
       }
        $sql = "SELECT *
                FROM " . DB_PREFIX . "seller_invoice
                WHERE 
                    seller_invoice_id IN (" . $seller_invoice_id . ")
                    ";
        $query = $this->_db->query($sql);
        if ($query->num_rows > 0) {
            $invoice = $query->rows;
            $invoice = array_combine(array_column($invoice, 'seller_invoice_id'), $invoice);
        }
        return $invoice;
    }

    /**
     * Public method to get seller invoice details for Debit Note
     * @param: $debit_note_id
     * @return: array
     * @author: Nishu, Jan 2018
     */
    public function getSellerInvoiceByDnId($debit_note_id) {
        $data = array();
        if(empty($debit_note_id)){
            return $data;
        }
        $sql = "SELECT si.*
                FROM " . DB_PREFIX . "seller_debit_note as dn
                
                    INNER JOIN 
                " . DB_PREFIX . "return as ocr 
                    ON dn.debit_note_id = ocr.debit_note_id

                   INNER JOIN " . DB_PREFIX . "order_product as oop
                ON oop.order_product_id =ocr.order_product_id

                   INNER JOIN " . DB_PREFIX . "seller_invoice as si
                ON si.seller_invoice_id = oop.seller_invoice_id
                
                WHERE 
                    dn.debit_note_id = '" . (int) $debit_note_id . "'
                LIMIT 0, 1
                ";
        $query = $this->_db->query($sql);
        if ($query->num_rows > 0) {
            $data = $query->row;
        }
        return $data;
    }

    /**
     * Public method to get seller invoice details for Replacement Note
     * @param: $replacement_note_id
     * @return: array
     * @author: Nishu, Jan 2018
     */
    public function getSellerInvoiceByRnId(int $replacement_note_id) {
        $data = array();
        if(empty($replacement_note_id)){
            return $data;
        }
        $sql = "SELECT si.*
                FROM " . DB_PREFIX . "replacement_note as rn
                    INNER JOIN 
                " . DB_PREFIX . "return as ocr 
                    ON rn.replacement_note_id = ocr.replacement_note_id
                   INNER JOIN " . DB_PREFIX . "order_product as oop
                ON oop.order_product_id =ocr.order_product_id

                   INNER JOIN " . DB_PREFIX . "seller_invoice as si
                ON si.seller_invoice_id = oop.seller_invoice_id
                
                WHERE 
                    rn.replacement_note_id = '" . (int) $replacement_note_id . "'
                LIMIT 0, 1
                ";
        $query = $this->_db->query($sql);
        if ($query->num_rows > 0) {
            $data = $query->row;
        }
        return $data;
    }


}
