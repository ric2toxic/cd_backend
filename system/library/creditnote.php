<?php

class CreditNote {

    private $_credit_note_prefix;
    private $_credit_note_no;
    private $_credit_note_id;
    private $_order_id;
    private $_suborder_id;
    private $_db;
    private $_registry;
    private $_config;
    private $_load;
    private $_currency;
    private $_ignorable_total = array('shipping', 'total_amt', 'credit', 'net_amount', 'advance');
    private $_failed_state_id = 8;
    private $_cancelled_flag = false;

    public function __construct($registry, $file_name = array()) {
        if (!empty($file_name)) {
            $file_name = unserialize(base64_decode($file_name));
            
            $this->_order_id = $file_name['order_id'];
            $this->_credit_note_id = $file_name['credit_note_id'];
        }
        $this->_registry = $registry;
        if (method_exists($registry, 'get')) {
            $this->db       = $registry->get('db');
            $this->currency = $registry->get('currency');
            $this->load     = $registry->get('load');
            $this->config   = $registry->get('config');
            $this->user     = $registry->get('user');
        } else {
            $this->db       = $registry->db;
            $this->currency = $registry->currency;
            $this->load     = $registry->load;
            $this->config   = $registry->config;
            $this->user     = $registry->user;
        }
    }

    public function getFile() {
        $data = $this->getCreditNoteData();
        $generate_pdf_file = $this->creditNotePdf($data);
        return $generate_pdf_file;
    }

    /**
     * @info: Public method to update amount and net_refundable amount in every credit note table if any changes found
     * @param: $credit_note_id, $net_amount, $total_amt
     * @auhtor: Nishu, May 2019
     *
    */
    public function updateCreditAmount($credit_note_id, $net_amount, $total_amt, $is_canceled = 0) {
        $sql = "
                SELECT
                    credit_note_amount,
                    net_refundable,
                    suborder_id
                FROM
                    " . DB_PREFIX . "credit_note
                WHERE 
                    credit_note_id = '" . (int) $credit_note_id . "'
               ";
        $qry = $this->db->query($sql);
        
        if($qry->num_rows > 0){
            $data = $qry->row;

            if(
                $data['credit_note_amount'] != $total_amt ||
                $data['net_refundable']     != $net_amount
            ){
                //Update data into DB
                $sql = "UPDATE  " . DB_PREFIX . "credit_note ";
                $sql .= "SET credit_note_amount = '" . (float) $total_amt  . "',
                             net_refundable = '" . (float) $net_amount . "' ";
                $sql .= "WHERE credit_note_id = '" . (int) $credit_note_id . "'";
                $this->db->query($sql);
            }

            //Redistribute amount to next schedules
            if($data['net_refundable'] != $net_amount || $is_canceled == 1){
                //Redistribute amount to NACH next coming  schedule(s) (CN's old net-refundable - new net-refundable) Amount
                
                if($is_canceled == 1){
                    $amount_to_redistribute = $data['net_refundable'];
                }else{
                    $amount_to_redistribute = $data['net_refundable'] - $net_amount;
                }
              
                $amount      = round((float)$amount_to_redistribute, 2);
                $suborder_id = $data['suborder_id'];
                $comment     = 'Credit Note Id: ' . $credit_note_id . ' is modified/generated, causing amount redistribution';

                $nach = new NachBehaviour($this->_registry);
                $nach->distributeAmountInRemainingSchedules($suborder_id,'today',$amount,$comment,array(),true);
            }
        }//IF end

    }//Function close

    /**
     * @info: Get Return Details for cancelled or active CN
     *          And if return_ids are empty in oc_credit_note table
     *              then update that return ids also in table
     * @author: Nishu, July 2018
    */
    public function getReturnDetails($cn_id, $cn_status){
        $result = array();

        //Get return details for cancelled CN
        if($cn_status != 1){
            $sql = "
                    SELECT ocr.*
                        FROM " . DB_PREFIX . "return AS ocr
                        INNER JOIN " . DB_PREFIX . "credit_note AS cn
                          ON FIND_IN_SET(ocr.return_id, cn.return_ids)
                        WHERE 
                          cn.credit_note_id = ". (int)$cn_id;
            $query = $this->db->query($sql);
            $result = $query->rows;
        }

        //Get return details for active CN
        // And also HandleCase: For Cancelled CN when return_ids are not updated on oc_credit_note table
        if($cn_status == 1 || empty($result)){
            $sql = "
                    SELECT * 
                      FROM " . DB_PREFIX . "return 
                      WHERE 
                        credit_note_id = ". (int)$cn_id ."
                   ";
            $query = $this->db->query($sql);
            $result = $query->rows;

            //If return_ids are empty, for cancelled CN
            if($cn_status != 1){
                $return_ids = implode(',', array_unique(array_column($result, 'return_id')) );

                //Fill return_ids to oc_credit_note table only if CN is cancelled
                $this->updateReturnIdsForCancelledCn($return_ids, $cn_id);
            }
        }

        return $result;
    }

    /**
     * @info: Public method to update return_ids in oc_credit_note table for cancelled CN
     * @author: Nishu, July 2018
    */
    public function updateReturnIdsForCancelledCn($return_ids, $cn_id){
        if(empty($return_ids) || empty($cn_id)){
            return;
        }

        $update_sql = "
                        UPDATE
                            " . DB_PREFIX . "credit_note
                        SET 
                            return_ids = '". $this->db->escape($return_ids) ."'
                        WHERE
                            credit_note_id = ". (int)$cn_id ."
                            AND credit_note_status = 0
                      ";
                    
        $this->db->query($update_sql);
    }

    public function getCreditNoteData() {
        //get all Credit note details
        $pdf_data = array();
        $info = $this->getcreditNoteDetails();

        if(!empty($info)){
            $this->_credit_note_prefix = $info['credit_note_prefix'];
            $this->_suborder_id        = $info['suborder_id'];
            $this->_credit_note_id     = $info['credit_note_id'];

            //Get Return details by CreditNote Id
            $returns_data = $this->getReturnDetails($this->_credit_note_id, $info['credit_note_status']);

            if(empty($info['credit_note_status'])) {
                $this->_cancelled_flag = true;
            }
            $pdf_data['credit_note_info'] = $info;
            $pdf_data['credit_note_info']['credit_note_date'] = date('d-m-Y', strtotime($info['date_added']));


            $product_info = array();
            if(!empty($returns_data)){
                foreach ($returns_data as $key => $value) {
                    if (!empty($product_info[$value["order_product_id"]])) {
                        $product_info[$value["order_product_id"]]['quantity'] += (int) $value["quantity"];
                    } else {
                        $product_info[$value["order_product_id"]]['order_product_id'] = $value["order_product_id"];
                        $product_info[$value["order_product_id"]]['quantity'] = $value["quantity"];
                    }
                }
            }

            $buyer_invoice = new BuyerInvoice($this->_registry);
            $buyer_invoice->setOptions('product_info', $product_info);

            $get_totals = $buyer_invoice->getTotals($this->_order_id, $this->_suborder_id);
            $purchase_firm = array();
            $purchase_firm = $buyer_invoice->getPurchaseFirmAddress($this->_order_id, $this->_suborder_id);
            if(!empty($purchase_firm)){
                reset($purchase_firm);
            }
            $pdf_data['seller_data'] = $purchase_firm[key($purchase_firm)];
            $selector = array(
                'order' => array(
                    'select' => array(
                        'order_no',
                        'currency_value',
                        'currency_code',
                        'email',
                        'shipping_company',
                        'shipping_address_1',
                        'shipping_address_2',
                        'shipping_city',
                        'shipping_zone',
                        'shipping_zone_id',
                        'telephone',
                        'shipping_city',
                        'shipping_postcode',
                        'shipping_country',
                        'payment_custom_field',
                        'payment_company',
                        'payment_address_1',
                        'payment_address_2',
                        'payment_postcode',
                        'payment_city',
                        'payment_zone',
                        'payment_zone_id',
                        'gst_number'
                    )
                ),
                'suborder' => array(
                    'select' => array(
                        'order_status_id',
                        'invoice_prefix',
                        'invoice_no',
                        'invoice_date',
                        'shipping_charge',
                        'gst'
                    )
                )
            );
            $order_info = OrderInfo::getOrderInfo($this->db, $this->_order_id, '', $selector);
            $order = $order_info['order'];
            $order['payment_custom_field'] = unserialize($order['payment_custom_field']);
            $order['tin'] = !empty($order['payment_custom_field'][1]) ? $order['payment_custom_field'][1] : '';
            $pdf_data["buyer_data"] = $order;
            $pdf_data["buyer_data"]['order_status_id'] = $order_info['suborder'][$this->_suborder_id]['order_status_id'];
            $pdf_data["buyer_data"]['shipping_charge'] = $order_info['suborder'][$this->_suborder_id]['shipping_charge'];
            $pdf_data['credit_note_info']['order_no']       = $order['order_no'];
            $pdf_data['credit_note_info']['currency_value'] = $order['currency_value'];
            $pdf_data['credit_note_info']['currency_code'] = $order['currency_code'];

            $pdf_data['credit_note_info']['invoice_prefix'] = $order_info['suborder'][$info['suborder_id']]['invoice_prefix'];
            $pdf_data['credit_note_info']['invoice_no'] = $order_info['suborder'][$info['suborder_id']]['invoice_no'];
            $pdf_data['credit_note_info']['invoice_date'] = date('d-m-Y', strtotime($order_info['suborder'][$info['suborder_id']]['invoice_date']));
            $pdf_data['credit_note_info']['gst'] = $order_info['suborder'][$info['suborder_id']]['gst'];

            // credit_note_info have credit note no and date and show directly in pdf
            $pdf_data["return_order_product_id"] = $product_info;
            $order_product_ids = array_keys($product_info);
            $fields = array(
                'order_product_id', 'name', 'price_per_piece',
                'output_tax_rates', 'discount_per_piece',
                'model', 'hsn_code'
            );
            $buyer_invoice->setOptions('for_cn', true);
            $pdf_data['products'] = $buyer_invoice->getProductArrayByOrderProductIds($order_product_ids, $fields);
            $pdf_data["totals"] = $get_totals;
        }

        return $pdf_data;
    }

    public function getPurchaseFirmDetailsFromSuborder_ids($order_id, $suborder_ids) {
        $purchase_firms = array();
        $buyer_invoice = new BuyerInvoice($this->_registry);
        if (is_array($suborder_ids)) {
            foreach ($suborder_ids as $suborder_id) {
                $pchse_frm = $buyer_invoice->getPurchaseFirmAddress($order_id, $suborder_id);
                $purchase_firms[key($pchse_frm)] = $pchse_frm[key($pchse_frm)];
            }
            if (count($purchase_firms) == 1) {
                return $purchase_firms[key($purchase_firms)];
            } else {
                throw new Exception("Purchase Frim id for all seller of a suborder should be same. Fix the seller vat_input_rule_id first.", 1);
            }
        } else {
            $purchase_firms = $buyer_invoice->getPurchaseFirmAddress($order_id, $suborder_id);
            return $purchase_firms[key($purchase_firms)];
        }
    }

    private function getcreditNoteDetails() {
        $data = array();
        $sql  = "
                SELECT cn.*
                FROM oc_credit_note cn
                WHERE 
                   cn.credit_note_id = '" . (int) $this->_credit_note_id . "'
                ";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $data = $query->row;
        }
        return $data;
    }

    /**
      Public function to grouping products for HSN code
     * @param array, array
     * @return array
     * @author Nishu
     */
    public function summrizeProductsForHsnCode($pdf_data, $all_hsn_codes) {
        $groupHsnCodeWiseProducts = array();
        foreach ($pdf_data['products'] as $product) {
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['output_tax_rates']]['product_value'] = 0.0;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['output_tax_rates']]['taxable_rate'] = 0.0;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['output_tax_rates']]['tax_value'] = 0.0;
        }
        foreach ($pdf_data['products'] as $product) {
            $op_id = $product['order_product_id'];

            $total_amt_per_product = (float) $pdf_data['return_order_product_id'][$op_id]['quantity'] * (float) $product["price_per_piece"];
            $total_discount_per_product = (float) $pdf_data['return_order_product_id'][$op_id]['quantity'] * (float) $product["discount_per_piece"];

            $taxable_value_per_product = (float) ($total_amt_per_product + $total_discount_per_product);

            $total_tax_value = (float) ($product["output_tax_rates"] * $taxable_value_per_product) / 100;

            //Sum of product values for HSN code
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['output_tax_rates']]['taxable_rate'] = $product['output_tax_rates'];
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['output_tax_rates']]['product_value'] += (float) $taxable_value_per_product;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['output_tax_rates']]['tax_value'] += (float) $total_tax_value;
        }
        return $groupHsnCodeWiseProducts;
    }

    /**
      Public function to calculate tax on given value
     * @param $value, $tax_rate
     * @return integer
     * @author Nishu
     */
    public function calculateTaxValue($value, $tax_rate) {
        return (float) $value * ($tax_rate / 100);
    }

    /**
     * Public function to calculate total advance for CN
     * @param $data array
     * @return Number
     * @author Nishu, August 2017
     */
    public function getTotalAdvanceForCN($data) {
        
        $advance_collected =  AdvanceVoucherLib::getTotalAdvanceOfSuborderPaymentGatewayWise($this->db, $data['order_id'], $data['suborder_id']);

        $advance_value = $advance_collected['advance_collected'];

        $condition = "";
        if (isset($data['credit_note_id'])) {
            $condition = " AND credit_note_id != '" . (int) $data['credit_note_id'] . "' ";
        }
        $sql = "SELECT * 
              FROM oc_credit_note
            WHERE 
              suborder_id = '" . $this->db->escape($data['suborder_id']) . "' AND 
              credit_note_status = '1' " . $condition;
        $result = $this->db->query($sql);
        $previousCNs = $result->rows;
        $previouslyAppliedAdvance = 0.0;
        foreach ($previousCNs as $key => $previousCN) {
            $previouslyAppliedAdvance += $previousCN['cod_failed_penalty'];
        }
        if ($advance_value > $previouslyAppliedAdvance) {
            return ((float) $advance_value - (float) $previouslyAppliedAdvance);
        } else if($advance_value > 0 ) {
            return (float) $advance_value;
        }else{
            return 0.0;
        }
    }

    /**
      Public function to get COD failed sub-totals
     * @param $pdf_data array, $total_section array
     * @return void
     * @author Nishu
     */
    public function getCodFailedSubTotals($pdf_data, &$total_section, $total_val) {
        $data                   = array();
        $data['credit_note_id'] = $pdf_data['credit_note_info']['credit_note_id'];
        $data['order_id']       = $pdf_data['credit_note_info']['order_id'];
        $data['suborder_id']    = $pdf_data['credit_note_info']['suborder_id'];
        $data['db']             = $this->db;

        $advance_value          = $pdf_data['credit_note_info']['advance_collected'];
        $cod_failed_penalty     = $pdf_data['credit_note_info']['cod_failed_penalty'];

        $total_section['not_refundable'] = array(
            'title' => 'COD Failure Charges (f) ',
            'value' => (-1) * $cod_failed_penalty
        );
        $total_section['not_collected'] = array(
            'title' => 'Amount Not Collected in COD (g)',
            'value' => (float) (-1) * ($total_val - $advance_value)
        );
    }

    public function creditNotePdf($pdf_data) {
        //Check if GST is applicable or not
        if ($pdf_data['credit_note_info']['gst'] == 0) {
           
            //Before GST is applicable(Before 1st July 2017)
            $table = '';
            ob_start();
            include(DIR_SYSTEM.'/library/creditnote_html.php');
            $table = ob_get_contents();
            ob_get_clean();
        } else {
           
            $table = '';
            ob_start();
            include(DIR_SYSTEM.'/library/creditnote_gst_html.php');
            $table = ob_get_contents();
            ob_get_clean();
        }
        $table = str_replace(array('&#x20b9;'), array('Rs.'), $table);

        $pdf_data_arr = array(
                            'title' => 'Wholesalebox Credit Note',
                            'subject' => 'Wholesalebox Credit Note',
                            'keywords' => 'Wholesalebox, Credit Note, Credit Note',
                            'pdf_name' => $this->_credit_note_prefix . $pdf_data['credit_note_info']['credit_note_no'],
                            'download_path' => DIR_DLOAD_BYR_CDT_NOTE
                        );
       
        //Download pdf file
        require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');
        $file_name = $pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'] . '.pdf';
        if (!file_exists($pdf_data_arr['download_path'])) {
          mkdir($pdf_data_arr['download_path'], 0777, true);
        }
        try{
            $html2pdf = new MyHtml2Pdf('P','A4','en', true, 'UTF-8');
            $html2pdf->setDefaultFont("times",'B',8);
            //$html2pdf->AddFont('dejavusans');
            $html2pdf->writeHTML($table, true, false, false, false, '');
            $html2pdf->output($file_name , 'F');
            //ob_end_clean();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        $file_name = base64_encode($pdf_data_arr['pdf_name'] . '.pdf');
        return $file_name;
    }

    /**
     * Public function to get Total return Quantity by giving master return id
     * @param: $master_return_id
     * @return: array
     * @author: Nishu, August 2017
     */
    public function getReturnQtyByMasterId($master_return_id) {

        $rejected_return_actions = array(
                                    RETURN_ACTION_IDS['Return_Request_Rejected'],
                                    RETURN_ACTION_IDS['Return_Goods_Rejected'],
                                    RETURN_ACTION_IDS['Replacement_Request_Rejected']
                                  );

        $data = array();
        $total_qty = 0;
        $product_wise_qty = array();
        $sql = "SELECT 
                  GROUP_CONCAT(CONCAT(r.return_id, ':' ,r.order_product_id, ':', r.quantity)) as return_combo,
                  r.return_reason_id, 
                  r.return_id
              FROM  
                " . DB_PREFIX . "return as r
              INNER JOIN 
                " . DB_PREFIX . "order_product as oop ON oop.order_product_id = r.order_product_id  
              WHERE  
                r.master_return_id = '" . (int) $master_return_id . "' 
                AND r.active_row = 1
                AND oop.buyer_invoice_id IS NOT NULL
                AND oop.buyer_invoice_id > 0
                AND r.return_action_id NOT IN (". implode(',', $rejected_return_actions) .")
              GROUP BY  
                    r.order_product_id
            ";
        $result = $this->db->query($sql);
        $return_ids = array();
        if ($result->num_rows > 0) {
            foreach ($result->rows as $key => $value) {
                $return_ids[] = $value['return_id'];
                $max_return_id = 0;
                $max_arr = array();
                $combo_arr = explode(',', $value['return_combo']);
                foreach ($combo_arr as $val) {
                    $arr = explode(':', $val);
                    if ($max_return_id < $arr[0]) {
                        $max_arr = $arr;
                        $max_return_id = $arr[0];
                    }
                }
                $total_qty += (int) $max_arr[2];
                $product_wise_qty[$max_arr[1]] = (int) $max_arr[2];
            }
        }
        $data['return_ids'] = $return_ids;
        $data['return_reason_id'] = $result->row['return_reason_id'];
        $data['return_qty'] = $total_qty;
        // $data['return_weight']    = $this->getWeigthByorderProductIds($product_wise_qty);
        $data['return_value'] = $this->getReturnValueByOrderProduct($product_wise_qty);
        return $data;
    }

    /**
     * Public function to get Total return Quantity by giving master return id
     * @param: $master_return_id
     * @return: array
     * @author: Nishu, August 2017
     */
    public function getReturnQtyById($return_id) {
        $data = array();
        $total_qty = 0;
        $product_wise_qty = array();
        $sql = "SELECT 
                  GROUP_CONCAT(CONCAT(r.return_id, ':' ,r.order_product_id, ':', r.quantity)) as return_combo,
                  r.return_reason_id, 
                  r.return_id
              FROM  
                 " . DB_PREFIX . "return as r
              INNER JOIN 
                 " . DB_PREFIX . "order_product as oop ON oop.order_product_id = r.order_product_id  
              WHERE  
                r.return_id = '" . (int) $return_id . "' 
                AND r.active_row = 1
              GROUP BY  
                r.order_product_id
            ";
        $result = $this->db->query($sql);
        $return_ids = array();
        if ($result->num_rows > 0) {
            foreach ($result->rows as $key => $value) {
                $return_ids[] = $value['return_id'];
                $max_return_id = 0;
                $max_arr = array();
                $combo_arr = explode(',', $value['return_combo']);
                foreach ($combo_arr as $val) {
                    $arr = explode(':', $val);
                    if ($max_return_id < $arr[0]) {
                        $max_arr = $arr;
                        $max_return_id = $arr[0];
                    }
                }
                $total_qty += (int) $max_arr[2];
                $product_wise_qty[$max_arr[1]] = (int) $max_arr[2];
            }
        }
        $data['return_ids'] = $return_ids;
        $data['return_reason_id'] = $result->row['return_reason_id'];
        $data['return_qty'] = $total_qty;
        // $data['return_weight']    = $this->getWeigthByorderProductIds($product_wise_qty);
        $data['return_value'] = $this->getReturnValueByOrderProduct($product_wise_qty);
        return $data;
    }

    /**
     * Public function to get Total return product's weight by order product ids
     * @param: order product ids (Comma seperated string)
     * @return: string
     * @author: Nishu, August 2017
     */
    public function getWeigthByorderProductIds($data) {
        $ttl_weight = 0.0;
        $order_product_ids = array_keys($data);
        $order_product_ids = implode(',', $order_product_ids);
        $sql = "SELECT oop.order_product_id,
              CASE
              WHEN oop.weight_per_piece >= 0.001 THEN oop.weight_per_piece
              ELSE op.weight
              END as weight_per_piece
            FROM " . DB_PREFIX . "order_product oop
            INNER JOIN " . DB_PREFIX . "product AS op ON op.product_id = oop.product_id
            WHERE oop.order_product_id IN (" . $order_product_ids . ")
            ";
        $result = $this->db->query($sql);
        foreach ($result->rows as $value) {
            $ttl_weight += (float) ($value['weight_per_piece'] * $data[$value['order_product_id']]);
        }
        return $ttl_weight;
    }

    /**
     * Public function to get Total return product value by order product ids
     * @param: order product ids (Comma seperated string)
     * @return: string
     * @author: Nishu, August 2017
     */
    public function getReturnValueByOrderProduct($data) {
        $total_value = 0.0;
        $order_product_ids = array_keys($data);
        $order_product_ids = implode(',', $order_product_ids);
        $sql = "SELECT *
            FROM " . DB_PREFIX . "order_product
            WHERE order_product_id IN (" . $order_product_ids . ")
            ";
        $result = $this->db->query($sql);
        foreach ($result->rows as $value) {
            $total_value += (($value['price_per_piece'] + $value['discount_per_piece']) * $data[$value['order_product_id']]) * (1 + ($value['output_tax_rates'] / 100));
        }
        return $total_value;
    }

    /**
     * Public function to CANCEL all CNs for specific sub-order
     * @param:  $order_id, $suborder_id
     * @return: Boolen
     * @author: Nishu, Oct 2017
     */
    public function cancelAllCNsBySuborderId($order_id, $suborder_id) {
        if (!empty($suborder_id)) {
            $sql = "SELECT 
                        cn . *,
                        oo.order_no,
                        td.trxn_done,
                        td.trxn_amount,
                        td.trxn_utr_date,
                        GROUP_CONCAT(CONCAT(IF(td.trxn_done IS NULL, '-',  td.trxn_done),
                                    ':',
                                    IF(td.trxn_amount IS NULL, '0',  td.trxn_amount),
                                    ':',
                                    IF(td.trxn_utr_date IS NULL, '-',  td.trxn_utr_date))) as trxn
                    FROM 
                       " . DB_PREFIX . "credit_note as cn
                       INNER JOIN 
                    oc_order as oo ON oo.order_id = cn.order_id
                       LEFT JOIN 
                    oc_trxn_details as td ON td.trxn_for_id = cn.credit_note_id 
                        AND td.trxn_for = 'CREDIT_NOTE'
                    WHERE
                        cn.order_id = " . (int) $order_id . " 
                        AND cn.suborder_id = '" . $this->db->escape($suborder_id) . "'
                        AND cn.credit_note_status = 1
                    GROUP BY cn.credit_note_id
                   ";
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                $all_cns = $result->rows;
                $cn_ids = implode(',', array_column($all_cns, 'credit_note_id'));
                $update_sql = "
                      UPDATE " . DB_PREFIX . "credit_note 
                      SET 
                          credit_note_status = 0,
                          net_refundable     = 0
                      WHERE
                          credit_note_id IN (" . $this->db->escape($cn_ids) . ")
                    ";
                $update_result = $this->db->query($update_sql);
                $data = array();
                if ($update_result) {
                    foreach ($all_cns as $key => $value) {
                        $cn_id = $value['credit_note_id'];
                        $data[$cn_id]['order_no']  = $value['order_no'];
                        $data[$cn_id]['cn_id']     = $cn_id;
                        $data[$cn_id]['cn_no']     = $value['credit_note_no'];
                        $data[$cn_id]['cn_amount'] = $value['net_refundable'];
                        $data[$cn_id]['cn_date']   = date('d/m/Y', strtotime($value['date_added']));
                        $data[$cn_id]['trxn']      = explode(',', $value['trxn']);
                    }
                    $this->sendMailForCNCancellation($data);
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function to  send Mail to nofity about CN cancellation
     * @param:  $data array
     * @return: void
     * @author: Nishu, Oct 2017
     */
    public function sendMailForCNCancellation($data) {
        $html = MailTemplate::mailForCNCancellation($data);
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
        $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->Subject = 'Credit Notes Cancelled: (' . $today_date . ')';
        $mail->msgHTML($html);
        $mail->send();
    }

    /**
     * Public function to get Total return Quantity by giving master return id
     * @param: $master_return_id
     * @return: array
     * @author: Nishu, August 2017
     */
    public function getReturnQtyByReturnId($return_id) {
        $data = array();
        $total_qty = 0;
        $product_wise_qty = array();
        $sql = "SELECT *
              FROM  
                " . DB_PREFIX . "return 
              WHERE return_id = ". (int)$return_id;
        $result = $this->db->query($sql);
        $return_ids = array();
        if ($result->num_rows > 0) {
            $product_wise_qty[$result->row['order_product_id']] = (int) $result->row['quantity'];
            $data['return_value'] = (float)$this->getReturnValueByOrderProduct($product_wise_qty);
        }

        return $data;
    }

    /**
     * @info: Public method to update oc_credit_note table with payment_cleared flag
     * @author: Nishu, July 2018
    */
    public function updateCnPaymentClearedFlag($data){
        if(!empty($data['credit_note_id']) && !empty($data['payment_cleared'])){
            $sql = "
                    UPDATE
                        " . DB_PREFIX . "credit_note
                    SET
                        payment_cleared = '". $this->db->escape($data['payment_cleared'])."'
                    WHERE
                        credit_note_id IN (".$data['credit_note_id'].")
                   ";
           
            $this->db->query($sql);
        }
    }
    
    /**
     * Public function to get all credit notes of an order
     * it will return credit notes with status 1
     * @param: $order_id
     * @return: credit notes array
     * @author: Devendra, July 2018
     */
    public static function getCreditNotesOfAnOrder($db, $order_id) {
      $sql = "SELECT credit_note_id, net_refundable, credit_note_prefix, credit_note_no FROM " . DB_PREFIX . "credit_note
              WHERE order_id = '" . (int)$order_id . "' AND credit_note_status = 1";
              
      $result = $db->query($sql);
      if ($result->num_rows) {
        return $result->rows;
      }
      
      return array();
    }

    /**
     * @info: Public method to get credit note net_refundable amount
     * @param: integer $dn_id
     * @return: float net_refundable
     * @author: MSA, July 2018
    */
    public function getCnNetRefundable($cn_id)
    {
        $sql = "
                SELECT 
                    net_refundable
                FROM     
                     " . DB_PREFIX . "credit_note
                WHERE
                    credit_note_id = '".(int)$cn_id."'

            ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            return $result->row['net_refundable'];
        }
        return 0;
    }

    /**
     * @info: Public Method to get CN Info for given multiple order_ids for given field list comma seperated
     * @param: $db, $order_ids, $field_list
     * @return: array
     * @author: Nishu, Jan 2019
    */
    public static function getCnInfoByOrderIds($db, array $order_ids, string $field_list):array{
        $result = array();

        $sql = "
                SELECT
                    order_id, credit_note_id AS cn_id , ".$field_list."
                FROM
                    ".DB_PREFIX."credit_note
                WHERE
                    order_id IN (".implode(',', $order_ids).")
                    AND credit_note_status = 1
               ";
        $qry = $db->query($sql);
        if($qry->num_rows > 0){
            foreach ($qry->rows as $value) {
                $result[$value['order_id']][$value['cn_id']] = $value;
            }
        }


        return $result;

    }

    public static function getCnDetailsToCalculateOrderBalance($db, $order_ids, $suborder_id = ''){
        if(!empty($order_ids)){
            
            if(is_array($order_ids)){
                $order_ids = implode(',', $order_ids);
            }
            $whr = "";
            if(!empty($suborder_id)){
                $whr = " AND suborder_id = '". $db->escape($suborder_id) ."'";
            }

            $data = array();
            $sql = "
                    SELECT 
                        COALESCE( SUM(credit_note_amount), 0) AS cn_amount,
                        COALESCE( SUM(cod_failed_penalty), 0) AS cod_failed_penalty,
                        COALESCE( SUM(less_cash_discount), 0) AS less_cash_discount,
                        COALESCE( SUM(other_charges), 0) AS other_charges,
                        order_id
                    FROM
                        ".DB_PREFIX."credit_note
                    WHERE
                        order_id IN (". $order_ids .")
                        AND credit_note_status = 1 ". $whr ."
                    GROUP BY
                        order_id
                   ";
            
            $query = $db->query($sql);
            if($query->num_rows > 0 ){
                $data = array_combine( array_column($query->rows, 'order_id'), $query->rows );
            }
        }
        return $data;
    }

    /**
     * @info: Public method to calculate and update credit_note_amount and net_refundable amount 
     * @param : integer $credit_note_id
     * @return : void 
     * @author: Nishu, May 2019
    */
    public function calculateAndUpdateCreditNoteAmount(int $credit_note_id, $is_canceled = 0) {
        if(!empty($credit_note_id)){
            $data = $this->calculateCreditNoteAmount($credit_note_id);

            //Calculate cn amount and net_refundable values for credit_note_id
            if(empty($data)){
                throw new \Exception('CreditNote::calculateAndUpdateCreditNoteAmount - CN Amount could not be calculated for credit_note_id: ' . $credit_note_id);
            }

            $net_amount = $data['calculated_net_refundable'] ?? 0 ;
            $total_amt  = $data['calculated_credit_note_amount'] ?? 0;

            //Update credit amount and adjust diffrence amount into NACH schedule(s)
            $this->updateCreditAmount($credit_note_id, $net_amount, $total_amt, $is_canceled);
        }
    }

    /**
     * @info: Public method to calculate credit_note_amount and net_refundable amount 
     * @param : integer $credit_note_id
     * @return : array 
     * @author: Nishu, May 2019
    */
    public function calculateCreditNoteAmount(int $credit_note_id) : array {
        $data = array();

        if(!empty($credit_note_id)){
            $select_sql = "
                            SELECT 
                                dt.credit_note_id, 
                                dt.calculated_credit_note_amount, 
                                (dt.calculated_credit_note_amount 
                                + dt.cash_discount 
                                - dt.less_cash_discount 
                                + dt.other_charges 
                                - IF(dt.is_cod_failed = 1, dt.calculated_credit_note_amount - dt.advance_collected, 0) 
                                - dt.cod_failed_penalty) AS calculated_net_refundable 
                            FROM 
                            (   
                              SELECT 
                                ocn.credit_note_id, 
                                ocn.is_cod_failed, 
                                ocn.cash_discount, 
                                ocn.less_cash_discount, 
                                ocn.other_charges, 
                                ocn.cod_failed_penalty, 
                                ocn.advance_collected, 
                              
                                /* Subtotal */
                                SUM((oop.price_per_piece + oop.discount_per_piece) * ort.quantity) 
                                  +
                                /* Shipping charges (reversal, reverse recovered, invoice (cod failed) */   
                                (ocn.invoice_shipping - ocn.shipping_collected + ocn.reversal_shipping)
                               
                                  +
                                /* Total Tax */
                                ROUND(( 
                                       /* Tax on product */
                                       SUM((oop.price_per_piece + oop.discount_per_piece) * ort.quantity * (oop.output_tax_rates/100))
                                         + 
                                       /* Tax on Shipping charges */
                                       (ocn.invoice_shipping - ocn.shipping_collected + ocn.reversal_shipping) * (MAX(oop.output_tax_rates)/100)
                                      ),2) AS calculated_credit_note_amount
                              
                              FROM ".DB_PREFIX."credit_note ocn 
                              JOIN ".DB_PREFIX."return ort ON ort.credit_note_id = ocn.credit_note_id 
                              JOIN ".DB_PREFIX."order_product oop ON oop.order_product_id = ort.order_product_id 
                              WHERE ocn.credit_note_id = ". (int)$credit_note_id ." 
                              GROUP BY ocn.credit_note_id 
                            ) AS dt
                          ";
            $qry = $this->db->query($select_sql);
            if($qry->num_rows > 0){
                $data = $qry->row;
            }
        }
        return $data;
    }

    /**
     * @info: Public method to get All credit note by given master_return_id
     * @param: int $master_return_id
     * @return: array
     * @author: Nishu, Sept 2019
    */
    public function getAllCreditNoteByMasterReturnId(int $master_return_id){
        $data = array();
        if(!empty($master_return_id)){
            $sql = "
                    SELECT
                        cn.credit_note_id,
                        cn.order_id,
                        cn.credit_note_amount,
                        cn.net_refundable,
                        r.master_return_id
                    FROM
                        ".DB_PREFIX."credit_note AS cn
                    INNER JOIN
                        ".DB_PREFIX."return AS r ON r.credit_note_id = cn.credit_note_id
                    WHERE
                        cn.credit_note_status = 1
                        AND r.master_return_id = ".(int)$master_return_id;

            $result = $this->db->query($sql);
            if($result->num_rows > 0 ){
                $data = $result->rows;
            }
        }
        return $data;
    }

}
