<?php

class DebitNote {

    private $_debit_note_prefix;
    private $_debit_note_no;
    private $_order_no;
    private $_debit_note_id;
    private $_replacement_note_prefix;
    private $_replacement_note_no;
    private $_replacement_note_id;
    private $_db;
    private $_registry;
    private $_load;
    private $_currency;
    private $_currency_code;
    private $_currency_value;
    private $_cancelled_flag = false;

    public function __construct($registry, $file_name = array()) {
        if (!empty($file_name)) {
            $file_name = unserialize(base64_decode($file_name));
            $this->_order_no = $file_name['order_no'];
            if(!empty($file_name['debit_note_id'])) {
                $this->_debit_note_id = (int)$file_name['debit_note_id'];    
            }
            if(!empty($file_name['replacement_note_id'])) {
                $this->_replacement_note_id = (int)$file_name['replacement_note_id'];    
            }
        }
        $this->_currency_code = 'INR';
        $this->_currency_value = '1.00000000';
        $this->_registry = $registry;
        if (method_exists($registry, 'get')) {
            $this->_db = $registry->get('db');
            $this->_load = $registry->get('load');
            $this->_currency = $registry->get('currency');
        } else {
            $this->_db = $registry->db;
            $this->_load = $registry->load;
            $this->_currency = $registry->currency;
        }
    }

    public function getFile() {
        $data = $this->getDebitNoteData();
        
        if(!isset($data['custom_party_meta']) || empty($data['custom_party_meta'])){
            // get calculated prdouct totals
            $seller_invo_obj = new SellerInvoice($this->_registry);
            $return_calculated_data = $seller_invo_obj->calculationProducts($this->_registry, '', '', '', $data['product_data']);
        }else{
            $return_calculated_data = $data;
        }
        $generate_pdf_file = $this->debitNotePdf($return_calculated_data, $data);
        return $generate_pdf_file;
    }

    public function getReplacementFile() {
        $data = $this->getReplacementNoteData();
        
        if(!isset($data['custom_party_meta']) || empty($data['custom_party_meta'])){
            // get calculated prdouct totals
            $seller_invo_obj = new SellerInvoice($this->_registry);
            $return_calculated_data = $seller_invo_obj->calculationProducts($this->_registry, '', '', '', $data['product_data']);
        }else{
            $return_calculated_data = $data;
        }

        if(empty($data['replacement_note_info']['file_name'])) { 

            $generate_pdf_file = $this->replacementNotePdf($return_calculated_data, $data);    

       }else{ 

            $generate_pdf_file = $data['replacement_note_info']['file_name'];

            // check if file not exist in folder path, generate new and save it
            if(!file_exists(DIR_DLOAD_SLR_RPMT_NOTE . base64_decode($data['replacement_note_info']['file_name'])))
            {
               $generate_pdf_file = $this->replacementNotePdf($return_calculated_data, $data);    
            } 
        }
        
        return $generate_pdf_file;
    }

    public function getSellerId() {
        $data = $this->getDebitNoteDetails();
        if (!empty($data)) {
            return $data['seller_id'];
        }
    }

    public function getDebitNoteData() {
        $pdf_data = array();
        //get all debit note details
        $info = $this->getDebitNoteDetails();

        if(isset($info['debit_note_status']) && empty($info['debit_note_status']) ){
            $this->_cancelled_flag = true;
        } 
        if(empty($info['custom_debit_note_meta'])){
            $return_data_info = $this->getReturnDetails($info['debit_note_id']);
            foreach ($return_data_info as $product_data) {
                $pdf_data['product_data'][$product_data['return_id']] = $product_data;
            }

            $seller_invoice = new SellerInvoice($this->_registry);
            $invoice_data = $seller_invoice->getSellerInvoiceByDnId($info['debit_note_id']);

            $pdf_data['invoice_prefix'] = $invoice_data['seller_invoice_prefix'];
            $pdf_data['invoice_no'] = $invoice_data['seller_invoice_no'];
            $pdf_data['invoice_date'] = $invoice_data['date_added'];
            $pdf_data['gst'] = $invoice_data['gst'];
            $debit_note_meta = $seller_invoice->getInvoiceMeta($invoice_data);
            $pdf_data['seller_address'] = $debit_note_meta['seller_data'];
            $pdf_data['buyer_data'] = $debit_note_meta['buyer_data'];
        }else{
            $selector = array();
            $result = OrderInfo::getOrderInfo($this->_db, $info['order_id'], '', $selector);
            $pdf_data = $info;
            $custom_party_meta = unserialize($info['custom_debit_note_meta']);
            $pdf_data['buyer_data'] = $custom_party_meta['buyer_data'];
            $pdf_data['custom_party_meta'] = $custom_party_meta['custom_party'];
            $pdf_data['gst'] = $pdf_data['buyer_data']['gst_number'];
            $pdf_data['order_no'] = $result['order']['order_no'];
        }

        // debit_note_info have debit note no and date and show directly in pdf
        $pdf_data['debit_note_info'] = array(
            'debit_note_id'     => (int)$info['debit_note_id'],
            'debit_note_prefix' => $info['debit_note_prefix'],
            'debit_note_no'     => $info['debit_note_no'],
            'debit_note_amount' => $info['debit_note_amount'],
            'seller_id'         => $info['seller_id'],
            'debit_note_date'   => date('d-m-Y', strtotime($info['date_added'])),
            'order_id'          => $info['order_id'],
            'order_no'          => $this->_order_no,
            'debit_note_status' => (int)$info['debit_note_status']
        );

        return $pdf_data;
    }

    private function getDebitNoteDetails() {
        $data = array();
        $sql = "SELECT *
    			FROM " . DB_PREFIX . "seller_debit_note
    			WHERE debit_note_id = '" . (int) $this->_debit_note_id . "'";
        $query = $this->_db->query($sql);
        if($query->num_rows > 0){
            $this->_debit_note_prefix = $query->row['debit_note_prefix'];
            $this->_debit_note_no = $query->row['debit_note_no'];
            $data = $query->row;
        }
        return $data;
    }

    /**
     * @info: Get Return Details for cancelled or active DN
     *          And if return_ids are empty in oc_seller_debit_note table
     *              then update that return ids also in table
     * @author: Nishu, July 2018
    */
    public function getReturnDetails($debit_note_id = 0) {

        $result = array();

        //Get return details for cancelled DN
        if($this->_cancelled_flag){
             $sql = "
                    SELECT 
                        ocr.*, 
                        ocr.quantity as product_quantity,
                        ocrr.name
                    FROM 
                        " . DB_PREFIX . "return AS ocr
                    INNER JOIN 
                        " . DB_PREFIX . "seller_debit_note AS dn ON FIND_IN_SET(ocr.return_id, dn.return_ids)
                    LEFT JOIN 
                        " . DB_PREFIX . "return_reason ocrr ON (ocr.return_reason_id = ocrr.return_reason_id)
                        WHERE 
                          dn.debit_note_id = ". (int)$debit_note_id;
            $query = $this->_db->query($sql);
            $result = $query->rows;
        }

        //Get return details for active DN
            // And also HandleCase: 
                //For Cancelled DN when return_ids are not updated on oc_seller_debit_note table
        if( !$this->_cancelled_flag || empty($result) ){
            $sql = "
                    SELECT 
                      ocr.*, 
                      ocr.quantity as product_quantity,
                      ocrr.name
                      FROM " . DB_PREFIX . "return AS ocr
                        LEFT JOIN " . DB_PREFIX . "return_reason ocrr
                          ON (ocr.return_reason_id = ocrr.return_reason_id)
                        WHERE 
                          ocr.debit_note_id = ". (int)$debit_note_id ; 
            $query = $this->_db->query($sql);
            $result = $query->rows;

            //If return_ids are empty, for cancelled DN
            if($this->_cancelled_flag){
                $return_ids = implode(',', array_unique(array_column($result, 'return_id')) );

                //Fill return_ids to oc_credit_note table only if CN is cancelled
                $this->updateReturnIdsForCancelledDn($return_ids, $debit_note_id);
            }
        }

        return $result;
    }

    /**
     * @info: Public method to get replacment note data for pdf generation process
     * @author: MSA, August 2018
    */
    public function getReplacementNoteData() {
        $pdf_data = array();
        //get all debit note details
        $info = $this->getReplacmentNoteDetails();
        
        $return_data_info = $this->getReturnReplacementDetails($info['replacement_note_id']);
        foreach ($return_data_info as $product_data) {
            $pdf_data['product_data'][$product_data['return_id']] = $product_data;
        }

        $seller_invoice = new SellerInvoice($this->_registry);
        $invoice_data = $seller_invoice->getSellerInvoiceByRnId($info['replacement_note_id']);

        $pdf_data['invoice_prefix'] = $invoice_data['seller_invoice_prefix'];
        $pdf_data['invoice_no'] = $invoice_data['seller_invoice_no'];
        $pdf_data['invoice_date'] = $invoice_data['date_added'];
        $pdf_data['gst'] = $invoice_data['gst'];
        $debit_note_meta = $seller_invoice->getInvoiceMeta($invoice_data);
        $pdf_data['seller_address'] = $debit_note_meta['seller_data'];
        $pdf_data['buyer_data'] = $debit_note_meta['buyer_data'];
        

        // debit_note_info have debit note no and date and show directly in pdf
        $pdf_data['replacement_note_info'] = array(
            'replacement_note_id'     => (int)$info['replacement_note_id'],
            'replacement_note_prefix' => $info['replacement_note_prefix'],
            'replacement_note_no'     => $info['replacement_note_no'],
            'replacement_note_amount' => $info['replacement_note_amount'],
            'seller_id'         => $invoice_data['seller_id'],
            'replacement_note_date'   => date('d-m-Y', strtotime($info['date_added'])),
            'order_id'          => $invoice_data['order_id'],
            'order_no'          => $this->_order_no,
            'file_name'               => $info['file_name']
        );
        return $pdf_data;
    }

    private function getReplacmentNoteDetails() {
        $data = array();
        $sql = "SELECT *
                FROM " . DB_PREFIX . "replacement_note
                WHERE replacement_note_id = '" . (int) $this->_replacement_note_id . "'";
        $query = $this->_db->query($sql);
        if($query->num_rows > 0){
            $this->_replacement_note_prefix = $query->row['replacement_note_prefix'];
            $this->_replacement_note_no = $query->row['replacement_note_no'];
            $data = $query->row;
        }
        return $data;
    }

    /**
     * @info: Get Return Replacement Details from replacement note table
     * @author: MSA, August 2018
    */
    public function getReturnReplacementDetails($replacement_note_id = 0) {
        $result = array();
        $sql = "
                SELECT 
                  ocr.*, 
                  ocr.quantity as product_quantity,
                  ocrr.name
                  FROM " . DB_PREFIX . "return AS ocr
                    LEFT JOIN " . DB_PREFIX . "return_reason ocrr
                      ON (ocr.return_reason_id = ocrr.return_reason_id)
                    WHERE 
                      ocr.replacement_note_id = ". (int)$replacement_note_id ; 
            $query = $this->_db->query($sql);
            $result = $query->rows;
            return $result;
    }



    /**
     * @info: Public method to update return_ids in oc_seller_debit_note table for cancelled DN
     * @author: Nishu, July 2018
    */
    public function updateReturnIdsForCancelledDn($return_ids, $debit_note_id){
        if(empty($return_ids) || empty($debit_note_id)){
            return;
        }

        $update_sql = "
                        UPDATE
                            " . DB_PREFIX . "seller_debit_note
                        SET 
                            return_ids = '". $this->_db->escape($return_ids) ."'
                        WHERE
                            debit_note_id = ". (int)$debit_note_id ."
                            AND debit_note_status = 0
                      ";
                    
        $this->_db->query($update_sql);
    }

    /**
      Public function to grouping products for HSN code
     * @param array, array
     * @return array
     * @author Nishu
     */
    public function summrizeProductsForHsnCode($pdf_data, $all_hsn_codes) {
        $groupHsnCodeWiseProducts = array();
        foreach ($pdf_data as $product) {
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['seller_tax']]['taxable_value'] = 0.0;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['seller_tax']]['taxable_rate'] = 0.0;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['seller_tax']]['tax_value'] = 0.0;
        }
        foreach ($pdf_data as $product) {
            $op_id = $product['order_product_id'];

            $total_amt_per_product = (float) $product['amount'];
            $total_discount_per_product = (float)$product['discount'];

            $taxable_value_per_product = (float)$product['taxable_value'];

            $total_tax_value = (float)$product['tax_value'];

            //Sum of product values for HSN code
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['seller_tax']]['taxable_rate'] = (float)$product['seller_tax'];
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['seller_tax']]['taxable_value'] += (float) $taxable_value_per_product;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['seller_tax']]['tax_value'] += (float) $total_tax_value;
        }
        return $groupHsnCodeWiseProducts;
    }

    /**
     * Public function to update debit note amount in db
     * @param: $debit_note_id, $amount
     * @return void
     * @author: Nishu, Nov 2017
    */
    public function updateDebitNoteAmount($debit_note_id, $net_amount) {
        $sql = "UPDATE  " . DB_PREFIX . "seller_debit_note ";
        $sql .= "SET debit_note_amount = " . (float) $net_amount . " ";
        $sql .= "WHERE debit_note_id = " . (int) $debit_note_id;
        $this->_db->query($sql);
    }

    /**
     * Public function to update replacement note amount in db
     * @param: $replacement_note_id, $amount
     * @return void
     * @author: MSA, August 2018
    */
    public function updateReplacementNoteAmount($replacement_note_id, $net_amount) {
        $sql = "UPDATE  " . DB_PREFIX . "replacement_note ";
        $sql .= "SET replacement_note_amount = " . (float) $net_amount . " ";
        $sql .= "WHERE replacement_note_id = " . (int) $replacement_note_id;
        $this->_db->query($sql);
    }

    /**
     * Public function to generate HTML structure for pdf(after GST is applicable) 
     * @param array, array
     * @return string
     * @author Nishu
     */
    public function generatePdfHtmlForGst($return_calculated_data, $pdf_data) {
        $table = '';
        ob_start();
        include(DIR_SYSTEM.'/library/debitnote_general_html.php');
        $table = ob_get_contents();
        ob_get_clean();
        return $table;
    }


      /**
     * Public function to generate HTML structure for pdf(after GST is applicable) 
     * @param array, array
     * @return string
     * @author Nishu
     */
    public function generatePdfHtmlForCustomParty($pdf_data) {
        $total_tax_value = 0.0;
        $total_taxable_value = 0.0;
        $all_hsn_codes = array();
        $gst_state_code = array();
        $buyer_zones = $pdf_data['custom_party_meta']['zone_id'] . "," . $pdf_data['buyer_data']['zone_id'];
        //Get StateCode for buyer
        $model_localisation_zone = null;
        $this->_load->model('localisation/zone', 'frontend');
        if (method_exists($this->_registry, 'get')) {
            $model_localisation_zone = $this->_registry->get('frontend_model_localisation_zone');
        } else {
            $model_localisation_zone = $this->_registry->frontend_model_localisation_zone;
        }
        $state_codes = $model_localisation_zone->getZoneGSTStateCode($buyer_zones);
        foreach ($state_codes as $state_code) {
            $gst_state_code[$state_code['zone_id']] = $state_code['gst_state_code'];
        }

        //Generate HTML structure
        $table = '';
        ob_start();
        include(DIR_SYSTEM.'/library/debitnote_custom_html.php');
        $table = ob_get_contents();
        ob_get_clean();
        return $table;
    }


    /**
     * Public function to generate HTML structure for pdf(before GST is applicable) 
     * @param array, array
     * @return string
     * @author Nishu
     */
    public function generatePdfHtml($return_calculated_data, $pdf_data) {
        //Generate HTML structure
        $table = '';
        ob_start();
        include(DIR_SYSTEM.'/library/debitnote_general_non_gst_html.php');
        $table = ob_get_contents();
        ob_get_clean();
        // /echo $table;die;
        return $table;
    }

    /**
     * Public function to Download pdf as debite note
     * @param array, array
     * @return string
     */
    public function debitNotePdf($return_calculated_data, $pdf_data) {
        //Check if GST is applicable or not
        if ($pdf_data['gst'] == 0) {
            //Before GST is applicable(Before 1st July 2017)
            $table = $this->generatePdfHtml($return_calculated_data, $pdf_data);
        }else if(!empty($pdf_data['custom_party_meta'])){
            //DebitNote for Custom Parties
            $table = $this->generatePdfHtmlForCustomParty($pdf_data);
        }else {
            //After GST is applicable(After 1st July 2017)
            $table = $this->generatePdfHtmlForGst($return_calculated_data, $pdf_data);
        }
        $pdf_data_arr = array(
                            'title' => 'Wholesalebox Debit Note',
                            'subject' => 'Wholesalebox Debit Note',
                            'keywords' => 'Wholesalebox, Debit Note, Debit Note',
                            'pdf_name' => $this->_order_no . $this->_debit_note_prefix . $this->_debit_note_no,
                            'download_path' => DIR_DLOAD_SLR_DBT_NOTE
                        );

      
        require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');
        $file_name = $pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'] . '.pdf';
        if (!file_exists($pdf_data_arr['download_path'])) {
          mkdir($pdf_data_arr['download_path'], 0777, true);
        }
        try{
            $html2pdf = new MyHtml2Pdf('P', 'A4', 'en', true, 'UTF-8');
            //$html2pdf->setDefaultFont("arial");
             $html2pdf->setDefaultFont('times','B',8);
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
     * Public function to Download pdf as replacement note
     * @param array, array
     * @return string
     */
    public function replacementNotePdf($return_calculated_data, $pdf_data) {
        //Generate HTML structure
        $table = '';
        ob_start();
        include(DIR_SYSTEM.'/library/replacement_note_html.php');
        $table = ob_get_contents();
        ob_get_clean();

        $pdf_data_arr = array(
                            'title' => 'Wholesalebox Replacement  Challan',
                            'subject' => 'Wholesalebox Replacement  Challan',
                            'keywords' => 'Wholesalebox, Replacement  Challan',
                            'pdf_name' => $this->_order_no . $this->_replacement_note_prefix . $this->_replacement_note_no,
                            'download_path' => DIR_DLOAD_SLR_RPMT_NOTE
                        );

        require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');
        $file_name = $pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'] . '.pdf';

        if (!file_exists($pdf_data_arr['download_path'])) {
          mkdir($pdf_data_arr['download_path'], 0777, true);
        }

        try{
            $html2pdf = new MyHtml2Pdf('P', 'A4', 'en', true, 'UTF-8');
            //$html2pdf->setDefaultFont("arial");
             $html2pdf->setDefaultFont('times','B',8);
            //$html2pdf->AddFont('dejavusans');
            $html2pdf->writeHTML($table, true, false, false, false, '');
            $html2pdf->output($file_name , 'F');
            //ob_end_clean();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        $file_name = base64_encode($pdf_data_arr['pdf_name'] . '.pdf');

        /*Update replacement note table for generated pdf file name*/
            $this->saveReplacementFileName(
                                            $pdf_data['replacement_note_info']['replacement_note_id'], 
                                            $file_name
                                           );
        /*Update replacement note table for generated pdf file name*/

        return $file_name;
    }

    /**
     * Public function to save pdf file name with replacement note details
     * @param $replacement_note_id integer
     * @param $file_name string
     * @return void
     */
    public function saveReplacementFileName($replacement_note_id, $file_name)
    {  
        if(!empty($replacement_note_id) && !empty($file_name)) {
            $sql = "UPDATE  " . DB_PREFIX . "replacement_note ";
            $sql .= "SET file_name = '" . $this->_db->escape($file_name) . "' ";
            $sql .= "WHERE replacement_note_id = " . (int) $replacement_note_id;
            $this->_db->query($sql);
        }
    }

}