<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class WsbPurchaseDebitNote {
    private $_db;
    private $_registry;
    private $_load;
    private $_currency;
    private $_currency_code;
    private $_currency_value;
    private $_cancelled_flag = false;
    private $_debit_note_prefix = '';
    private $_debit_note_year   = '';
    private $_debit_note_no     = 0;
    private $all_hsn_codes = array();
    private $dn_html = '';
    private $dn_amt = 0.00;
    private $_dn_id = 0;

    public function __construct($registry, $file_name = array()) {
        if (!empty($file_name)) {
            $data    = unserialize(base64_decode($file_name));
            $this->_dn_id = !empty($data['dn_id']) ? $data['dn_id'] : 0;
        }
        $this->_currency_code  = 'INR';
        $this->_currency_value = '1.00000000';
        $this->_registry       = $registry;
        if (method_exists($registry, 'get')) {
            $this->_db       = $registry->get('db');
            $this->user      = $registry->get('user');
            $this->_load     = $registry->get('load');
            $this->_currency = $registry->get('currency');
        } else {
            $this->_db       = $registry->db;
            $this->user      = $registry->user;
            $this->_load     = $registry->load;
            $this->_currency = $registry->currency;
        }
    }

    /**
     * Public method to get URL to download debitNote
     * @author: Nishu, Jan 2018
    */
    public function getFile() {
        // get return products for debit note
        $seller_invo_obj = new SellerInvoice($this->_registry);
        $return_calculated_data = $seller_invo_obj->calculationProducts($this->_registry, '', '', '', $data['product_data']);
      
        $generate_pdf_file = $this->debitNotePdf($return_calculated_data, $data);
        return $generate_pdf_file;
    }

    
    /**
     * get details of Debit Note data details
     * @param  Array
     * @return Product Details Array
     * @author Nishu Rani
     * @date   18 Jan 2018
    */
    public function getWsbPurchaseDnDetails(array $data) {
        //Set Return data with array()
        $dn_data = array();
        if (empty($data)) {
            return $dn_data;
        }
       
       $sql = "SELECT     
                wp.*,
                op.*,
                wpb.breakup_id,
                wpb.sku as product_sku,
                wpb.transfer_price_per_piece as product_price,
                wpb.seller_tax as product_tax,
                wpb.pieces as purchase_qty,
                opd.name as product_name,
                SUM(IF(wsb_return.debit_note_status =1, wsb_return.quantity, 0)) as dn_qty
            FROM
                ".DB_PREFIX."wsb_purchase AS wp
                    INNER JOIN
                ".DB_PREFIX."wsb_purchase_breakup AS wpb ON wp.purchase_id = wpb.purchase_id
                    INNER JOIN
                ".DB_PREFIX."product AS op ON op.product_id = wpb.product_id
                    INNER JOIN
                ".DB_PREFIX."product_description AS opd ON op.product_id = opd.product_id
                    LEFT JOIN 
                (
                   SELECT
                       wprb.quantity,
                       wprb.product_id,
                       wprb.purchase_id,
                       wprb.transfer_price_per_piece,
                       wpr.debit_note_status
                   FROM
                       ".DB_PREFIX."wsb_purchase_return_breakup as wprb
                   INNER JOIN
                       ".DB_PREFIX."wsb_purchase_return as wpr ON wprb.debit_note_id = wpr.debit_note_id
               ) AS wsb_return ON wsb_return.purchase_id = wp.purchase_id 
               AND wsb_return.product_id = wpb.product_id
               AND wsb_return.transfer_price_per_piece = wpb.transfer_price_per_piece

            WHERE
                wp.purchase_id = ". (int)$data['purchase_id'] ."
                AND wpb.breakup_id IN (" . implode(',',$data['breakup_ids']).")
                AND opd.language_id = 1
            GROUP BY wp.purchase_id, wpb.breakup_id
           ";

        $query = $this->_db->query($sql);
        $purchase_firm_id = 0;
        if($query->num_rows > 0){
            $dn_data['product'] = array();
            $products = $query->rows;
            foreach ($products as $product) {
                $product_id = $product['product_id'];
                $breakup_id = $product['breakup_id'];

                if($product['purchase_qty'] <  $product['dn_qty']){
                    $min_qty = 0;
                }else{
                    //Set minimum Qty to product qty passed from Form
                    $min_qty = $data['dn_qty'][$breakup_id];
                    //Get returnable qty by substracting GeneratedDnQty from Purchase Qty 
                    $returnable_qty =  $product['purchase_qty'] -  $product['dn_qty'];
                    if($returnable_qty < $min_qty){
                        $min_qty = $returnable_qty;
                    }
                }

                $purchase_firm_id = $product['purchase_firm_id'];
                if(empty($dn_data['seller_data'])){//If seller_data not set
                    $dn_data['seller_data'] = unserialize($product['seller_firm_meta']);
                }
                if(empty($dn_data['buyer_data'])){//If buyer_data not set
                    $dn_data['buyer_data'] = unserialize($product['purchase_firm_meta']);
                }
                if(empty($dn_data['invoice_no'])){//If invoice_no not set
                    $dn_data['invoice_no'] = $product['invoice_no'];
                }
                if(empty($dn_data['invoice_date'])){//If invoice_date not set
                    $dn_data['invoice_date'] = $product['invoice_date'];
                }
                if(empty($dn_data['is_sor'])){//If is_sor index key not set
                    $dn_data['is_sor'] = $product['sor_purchase'];
                }

                /////////////////////////////////////////////
                //Transfer Price per piece from wsb_purchase_breakup table
                $transfer_price = $product['product_price'];
                $base_price = $transfer_price / (1 + $product['product_tax'] / 100);
                $base_price = round($base_price, 2);
                //Create Tax  class object
                $tax_obj = new Tax($this->_registry);
                $tax_rate = $tax_obj->getTaxRate($base_price , $product['tax_class_id']);
                //Generate new transfer price per piece with new tax rates
                $new_transfer_price = $base_price * (1 + $tax_rate / 100);
                $new_transfer_price = round($new_transfer_price, 2);
                ///////////////////////////////////////////

                $dn_data['product'][$breakup_id]                   = $product;
                $dn_data['product'][$breakup_id]['product_id']     = $product['product_id'];
                $dn_data['product'][$breakup_id]['model']          = $product['model'];
                $dn_data['product'][$breakup_id]['sku']            = $product['product_sku'];
                $dn_data['product'][$breakup_id]['hsn_code']       = $product['hsn_code'];
                $dn_data['product'][$breakup_id]['quantity']       = $min_qty;
                $dn_data['product'][$breakup_id]['transfer_price'] = $new_transfer_price;
                $dn_data['product'][$breakup_id]['base_price']     = $base_price;
                $dn_data['product'][$breakup_id]['tax']            = $tax_rate;
                $dn_data['product'][$breakup_id]['product_name']   = $product['product_name'];
            }
        }
        $dn_data['dn_date']          = date("Y-m-d");
        $dn_data['purchase_id']      = (int)$data['purchase_id'];
        $dn_data['purchase_firm_id'] = (int)$purchase_firm_id;
        //set DebitNote Prefix and DebitNote Num
        $this->setDnPrefixAndNum($dn_data['buyer_data']);
        
        return $dn_data;
    }

    /**
     * Public method to set DebitNote Prefix and DebitNote Num
     * @param: $data Array
     * @return: $html String
     * @author: Nishu, Jan 2018
    */
    public function setDnPrefixAndNum($data){
        if(isset($data['tin'])){
            $gstin = $data['tin'];
        }else{
            $gstin = 0;
        }
        $prefix_obj = new Prefixes();
        $prefix_detail = $prefix_obj->getPrefix($this->_db, $gstin, 'DEBIT_NOTE');
        //Get Cuurent Financial Year
        $financial_year = '';
        if ( (int)(date('m')) <= 3 ) {
            $financial_year = date('y-', strtotime('-1 years'));
        } else {
            $financial_year = date('y-');
        }

        $this->_debit_note_prefix = $prefix_detail['prefix'];
        $this->_debit_note_year   = $financial_year;
        
        if($prefix_detail['financial_year'] == $financial_year){//Check for debitNote num is generating for new year for same financial year
            $this->_debit_note_no     = (int)$prefix_detail['available_no'];
        }else{
            $this->_debit_note_no     = 1;
        }
    }

    /**
     * Public method to debitNote preview HTML
     * @param: $data Array
     * @return: $html String
     * @author: Nishu, Jan 2018
    */
    public function getDebitNotePreviewHTML($data) {
        $pdf_data = $this->getWsbPurchaseDnDetails($data);
        $html = $this->generateDnPdfHtml($pdf_data);
        $this->dn_html = $html;
        return $html;
    }


    /**
     * @info: Public method to generate html for debit note pdf
     * @return: String
     * @author: Nishu, Jan 2018
    */
    public function generateDnPdfHtml($pdf_data) {
        //Generate HTML structure
        $table = '';
        ob_start();
        include(DIR_SYSTEM.'/library/wsbpurchasednhtml.php');
        $table = ob_get_contents();
        ob_get_clean();
        $table = str_replace(array('&#x20b9;'), array('Rs.'), $table);
        return $table;
    }

    /**
      Public function to grouping products for HSN code
     * @param array, array
     * @return array
     * @author Nishu, 19 Jan 2018
     */
    public function summrizeProductsForHsnCode($pdf_data) {

        $groupHsnCodeWiseProducts = array();
        foreach ($pdf_data as $product) {
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['tax']]['taxable_value'] = 0.0;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['tax']]['taxable_rate']  = 0.0;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['tax']]['tax_value']     = 0.0;
        }
        foreach ($pdf_data as $product) {
            $total_amt_per_product = (float) $product['quantity'] * (float) $product["rate_per_piece"];
            $total_discount_per_product = (float)$product['quantity'] * (float)$product['discount'];

            $taxable_value_per_product = (float)($total_amt_per_product + (float)$total_discount_per_product);

            $total_tax_value = (float) ($product["tax"] * (float)$taxable_value_per_product) / 100;

            //Sum of product values for HSN code
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['tax']]['taxable_rate'] = (float)$product['tax'];
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['tax']]['taxable_value'] += (float) $taxable_value_per_product;
            $groupHsnCodeWiseProducts[$product['hsn_code']][$product['tax']]['tax_value'] += (float) $total_tax_value;
        }
        return $groupHsnCodeWiseProducts;
    }

    /**
     * @info: Public method to generate debit note
     * @param: $data
     * @return: $dn_id Integer
     * @author: Nishu, Jan 2018
    */
    public function generateDebitNote($data) {
        $dn_id = 0;
        if(empty($data)){
            return $dn_id;
        }
        $payment_cleared = "NO";

        if(!empty($data['is_sor'])){
            $payment_cleared = "NOT_APPLICABLE";
        }

        //Insert DebitNote for WSB Purchase returns
        $sql = "INSERT INTO  ".DB_PREFIX."wsb_purchase_return
                SET 
                   purchase_id       = ". (int)$data['purchase_id'] .",
                   purchase_firm_id  = ". (int)$data['purchase_firm_id'] .",
                   debit_note_prefix = '". $this->_db->escape($this->_debit_note_prefix.$this->_debit_note_year) ."',
                   debit_note_no     = '". $this->_db->escape($this->_debit_note_no) ."',
                   debit_note_amount = '". (float)$this->dn_amt ."',
                   user_id           = ". (int)$this->user->getId() .",
                   user_name         = '". $this->_db->escape($this->user->getUserName()['name']) ."',
                   date_added        = NOW(),
                   payment_cleared   = '". $this->_db->escape($payment_cleared) ."'
                ";
        $this->_db->query($sql);
        $dn_id = $this->_db->getLastId();

        //Get Current Financial year
        $financial_year = '';
        if ( (int)(date('m')) <= 3 ) {
            $financial_year = date('y-', strtotime('-1 years'));
        } else {
            $financial_year = date('y-');
        }
        $data['dn_id']  = $dn_id;
        $data['dn_amt'] = $this->dn_amt;
        //Update Available Debit Note no for available DebitNote Prefix
        $prefix_data = array();
        $prefix_data['available_no'] = (int)$this->_debit_note_no + 1;
        $prefix_data['prefix']       = $this->_debit_note_prefix;
        $prefix_data['financial_year']= $financial_year;
        Prefixes::updatePrefixAvailableNo($this->_db, $prefix_data);
        $this->addTrxnDetailsIntoDb($data);
        return $dn_id; //Return newly added Id for DebitNote.
    }

    /**
     * @info: Save TRXN Details for WSB PURCHASE DEBIT NOTE
     * @param: $data Array
     * @return: void
     * @author: Nishu, Jan 2018
    */
    public function addTrxnDetailsIntoDb($data){

        $trxn_done       = "NOT_DONE";
        
        if(!empty($data['is_sor'])){
            $trxn_done       = "NOT_APPLICABLE";
        }
        if(!empty($data)){
            $sql = "
                    INSERT INTO  ".DB_PREFIX."trxn_details
                    SET 
                       trxn_for         = 'WSB_PURCHASE_RETURN',
                       trxn_for_id      = ". (int)$data['dn_id'] .",
                       trxn_done        = '". $this->_db->escape($trxn_done) ."',
                       trxn_amount      = 0,
                       trxn_date_added  = NOW()
                   ";
            $this->_db->query($sql);
        }
    }

    /**
     * @info: Public method to save DebitNote Breakup 
     *         i.e. ProductWise Details
     * @param: $data
     * @return: vois
     * @author: Nishu, Jan 2018
    */
    public function saveDebitNoteBreakUp($data) {
        if(empty($data)){
            return; //return if $data is empty
        }
        if(empty($data['dn_id'])){
            return; //Return if Debit Note is not generated for WSB purchase
        }

        if(!empty($data['product'])){
            foreach ($data['product'] as $product_id => $product) {
                //Insert DebitNote Breakup into DB i.e. oc_wsb_purchase_return_breakup
                $sql = "INSERT INTO  ".DB_PREFIX."wsb_purchase_return_breakup
                        SET
                           debit_note_id            = ". (int)$data['dn_id'] .", 
                           purchase_id              = ". (int)$data['purchase_id'] .",
                           purchase_firm_id         = ". (int)$data['purchase_firm_id'] .",
                           product_id               = '". (int)$product['product_id'] ."',
                           model                    = '". $this->_db->escape($product['model']) ."',
                           sku                      = '". $this->_db->escape($product['sku']) ."',
                           hsn_code                 = '". $this->_db->escape($product['hsn_code']) ."',
                           quantity                 = ". (int)$product['quantity'] .",
                           base_price_per_piece     = ". (float)$product['base_price'] .",
                           tax_rate                 = ". (float)$product['tax'] .",
                           transfer_price_per_piece = ". (float)$product['transfer_price'] .",
                           user_id                  = ". (int)$this->user->getId() .",
                           user_name        = '". $this->_db->escape($this->user->getUserName()['name']) ."',
                           date_added       = NOW()
                        ";
                $this->_db->query($sql);
                
                //Update product qty into DB i.e. oc_product
               /*
                Update oc_product Quantity  manually
                $update_sql = "UPDATE ".DB_PREFIX."product
                        SET
                           quantity        = quantity- FLOOR(". (int)$product['quantity'] ."/ piece_in_set)
                        WHERE 
                            product_id = ". (int)$product_id ."
                        ";
                $this->_db->query($update_sql);*/
            }
        }
        return; //Return newly added Id for DebitNote.
    }

    /**
     * @INFO: Save DebitNote pdf and return file_path
     * @param: $html String
     * @return: File Path
     * @author: Nishu, Jan 2018
    */
    public function saveDnPdf($html){
        $pdf_data_arr = array(
                        'title'    => 'WSB Purchase Debit Note',
                        'subject'  => 'WSB Purchase Debit Note',
                        'pdf_name' => $this->_debit_note_prefix. $this->_debit_note_year . $this->_debit_note_no,
                        'download_path' => DIR_DLOAD_SLR_DBT_NOTE
                      );
        
        require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');
        $file_name = $pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'] . '.pdf';
        
        if (!file_exists($pdf_data_arr['download_path'])) {
          mkdir($pdf_data_arr['download_path'], 0777, true);
        }
        try{
            $html2pdf = new MyHtml2Pdf('P','A4','en', false, 'UTF-8');
            //$html2pdf->pdf->SetDisplayMode('fullpage');    
            $html2pdf->setDefaultFont("arial");
            $html2pdf->writeHTML($html, true, false, false, false, '');
            $html2pdf->output($file_name , 'F');
            //ob_end_clean();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        return $pdf_data_arr['pdf_name'] . '.pdf';
    }

    /**
     * @INFO: Get all Generated DNs for given Purchase Id
     * @param: $purchase_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getAllDnByPurchaseId($purchase_id = 0){
        $data = array();
        if(empty($purchase_id)){
            return $data;
        }
        $sql = "SELECT * 
                 FROM ".DB_PREFIX."wsb_purchase_return as wpr
                 WHERE
                    wpr.purchase_id = ". (int)$purchase_id;
        $query = $this->_db->query($sql);
        if($query->num_rows > 0){
            $data = $query->rows;
            $data = array_combine(
                        array_column($data, 'debit_note_id'), 
                        $data);
        }
        return $data;
    }

    /**
     * @INFO: Get all Generated DNs for given Purchase Id
     * @param: $purchase_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getDnByPurchaseId(){
        $data = array();
        if(empty($this->_dn_id)){
            return $data;
        }
        $sql = "SELECT 
                    wp.purchase_id,
                    wp.purchase_firm_id,
                    wp.invoice_no,
                    wp.invoice_date,
                    wp.gst,
                    wp.seller_firm_meta,
                    wp.purchase_firm_meta,
                    wpr.debit_note_id,
                    wpr.purchase_firm_id,
                    wpr.debit_note_status,
                    wpr.debit_note_prefix,
                    wpr.debit_note_no,
                    wprb.id AS return_breakup_id,
                    wprb.product_id,
                    wprb.model,
                    wprb.sku,
                    wprb.hsn_code,
                    wprb.base_price_per_piece,
                    wprb.transfer_price_per_piece as new_transfer_price,
                    wprb.tax_rate,
                    wpr.date_added as dn_date,
                    wprb.quantity as dn_qty,
                    opd.name as product_name
                FROM
                ".DB_PREFIX."wsb_purchase AS wp
                    INNER JOIN 
                ".DB_PREFIX."wsb_purchase_return as wpr ON wpr.purchase_id = wp.purchase_id
                    INNER JOIN 
                ".DB_PREFIX."wsb_purchase_return_breakup as wprb ON wprb.debit_note_id = wpr.debit_note_id
                    INNER JOIN
                ".DB_PREFIX."product_description AS opd ON wprb.product_id = opd.product_id
                WHERE 
                  wpr.debit_note_id = ". (int)$this->_dn_id ."
                  AND opd.language_id = 1
              ";
        $query = $this->_db->query($sql);
        
        $purchase_firm_id = 0;
        if($query->num_rows > 0){
            $products = $query->rows;

            foreach ($products as $product) {
                $product_id        = $product['product_id'];
                $return_breakup_id = $product['return_breakup_id'];
                $purchase_firm_id = $product['purchase_firm_id'];
                if(empty($dn_data['seller_data'])){//If seller_data not set
                    $dn_data['seller_data'] = unserialize($product['seller_firm_meta']);
                }
                if(empty($dn_data['buyer_data'])){//If buyer_data not set
                    $dn_data['buyer_data'] = unserialize($product['purchase_firm_meta']);
                }
                if(empty($dn_data['invoice_no'])){//If invoice_no not set
                    $dn_data['invoice_no'] = $product['invoice_no'];
                }
                if(empty($dn_data['invoice_date'])){//If invoice_date not set
                    $dn_data['invoice_date'] = $product['invoice_date'];
                }
                if(empty($dn_data['dn_date'])){//If invoice_date not set
                    $dn_data['dn_date'] = $product['dn_date'];
                }

                $this->_cancelled_flag = !$product['debit_note_status'];

                if(empty($this->_debit_note_prefix)){//If debit_note_prefix not set
                    $this->_debit_note_prefix = $product['debit_note_prefix'];
                }
                if(empty($this->_debit_note_no)){//If debit_note_no not set
                    $this->_debit_note_no = $product['debit_note_no'];
                }
                $dn_data['product'][$return_breakup_id]['product_id']     = $product['product_id'];
                $dn_data['product'][$return_breakup_id]['model']          = $product['model'];
                $dn_data['product'][$return_breakup_id]['sku']            = $product['sku'];
                $dn_data['product'][$return_breakup_id]['hsn_code']       = $product['hsn_code'];
                $dn_data['product'][$return_breakup_id]['quantity']       = $product['dn_qty'];
                $dn_data['product'][$return_breakup_id]['transfer_price'] = $product['new_transfer_price'];
                $dn_data['product'][$return_breakup_id]['tax']            = $product['tax_rate'];
                $dn_data['product'][$return_breakup_id]['product_name']   = $product['product_name'];
                $dn_data['purchase_id']                                   = (int)$product['purchase_id'];
                $dn_data['purchase_firm_id']                              = (int)$product['purchase_firm_id'];
            }
        }

        $this->_debit_note_year   = '';
        
        $html = $this->generateDnPdfHtml($dn_data);

        return base64_encode($this->saveDnPdf($html));
    }

    /**
    * Public method to update debit note status for canceled status
    * @param integer debit_note_id
    * @return void
    * @author MSA July 18
    */
    public function updatedDebitNoteStatusForCancel() {

        $data = array();
        if(empty($this->_dn_id)){
            return $data;
        }
        $sql = "
                UPDATE 
                    ".DB_PREFIX."wsb_purchase_return
                SET
                    debit_note_status = 0,
                    payment_cleared   = 'NO'
                WHERE
                    debit_note_id = '".(int)$this->_dn_id ."'         
                ";
        $this->_db->query($sql);

    }

}
