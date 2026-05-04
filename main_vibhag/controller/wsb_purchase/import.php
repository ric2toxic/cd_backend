<?php
class ControllerWsbPurchaseImport extends Controller{
    private $error = array();

    public function index() {
        
        $this->load->model('wsb_purchase/import');
        
        $data = array();// Initializing the data array to be passed on to template files
        
        $data['filter_purchase_firm_id']        = 0;
        $data['filter_invoice_no']              = "";
        $data['filter_seller_id']               = 0;
        $data['filter_invoice_date_from']       = "";
        $data['filter_invoice_date_to']         = "";
        $data['filter_purchase_value_from']     = "";
        $data['filter_purchase_value_to']       = "";
        $data['filter_date_added_from']         = "";
        $data['filter_date_added_to']           = "";        
        $data['filter_sku']                     = "";
        $data['order_by']                       = 'purchase_id';
        $data['order']                          = 'desc';
        $data['page']                           = 1;
        $data['limit']                          = $this->config->get('config_limit_admin');
        $url                                    = array();

        foreach ($this->request->get as $key => $value) {
             $data[$key] = $value;
             $url[] = "$key=$value";
        }
        $data['offset'] = ($data['page'] - 1) * $this->config->get('config_limit_admin');
        $url = implode('&',$url);

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $wsb_purchases = $this->model_wsb_purchase_import->getWsbPurchases($data);
        $wsb_purchase_total = $this->model_wsb_purchase_import->getWsbPurchases($data,$get_total = 1);
        $data['wsb_purchases'] = array();
        if(!empty($wsb_purchases)){
            foreach ($wsb_purchases as $key => $wsb_purchase) {
                $image_data = array();
                $file_url = '';
                $purchase_id = $wsb_purchase['purchase_id'];
                $wsb_purchases[$key] = $wsb_purchase;
                $wsb_purchases[$key]['seller'] = unserialize($wsb_purchase['seller_firm_meta'])['company'];
                $purchase_firm = unserialize($wsb_purchase['purchase_firm_meta']);
                $wsb_purchases[$key]['purchase_firm'] = $purchase_firm['company'].'<br>'.$purchase_firm['city'].'<br>'.$purchase_firm['tin'];
                $wsb_purchases[$key]['user'] = unserialize($wsb_purchase['user']);
                $wsb_purchases[$key]['return_url'] = $this->url->link('wsb_purchase/purchase_return', 'token=' . $this->session->data['token'] . '&purchase_id=' . $purchase_id, 'SSL');

                $image_data = $this->model_wsb_purchase_import->checkDownloadWsbPurchaseInvoiceImage($purchase_id);
                if(!empty($image_data['purchase_bill_image'])){
                    $encode_file = array();
                    $encode_file['purchase_id'] = $purchase_id;
                    $encode_file = base64_encode(serialize($encode_file));
                    $file_url = $this->securefiledownload->getDownloadLink('wsb_prchse_inv_img',$encode_file, false);   
                }
                $wsb_purchases[$key]['dwnlod_img_href'] = $file_url;
                //Get all DebitNotes for specific PurchaseId
                $wsb_dn_obj = new WsbPurchaseDebitNote($this);
                $wsb_purchases[$key]['dn_list'] = $wsb_dn_obj->getAllDnByPurchaseId($purchase_id);
                //Set DN download URL with dn list data
                if(!empty($wsb_purchases[$key]['dn_list'])) {
                    foreach($wsb_purchases[$key]['dn_list'] as $dn_key => $dn_value) {
                     //Secure download debit note link   
                        $file_name = array();
                        $file_name['dn_id'] = (int)$dn_value['debit_note_id'];
                        $file_name = serialize($file_name);
                        $file_name = base64_encode($file_name);
                        $wsb_purchases[$key]['dn_list'][$dn_key]['download_dn'] = $this->securefiledownload->getDownloadLink('wsb_purchase_return', $file_name, false); 
                    }
                }
            }
            $data['wsb_purchases'] = $wsb_purchases;
        }
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('wsb_purchase/report',$data);
        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('wsb_purchase/import', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        $data['add'] = $this->url->link('wsb_import/import/import', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $seller_profile = new SellerProfile($this);

        $sellers = $seller_profile->getSellers(array('get_address'=>true));
        $data['filter_seller_name'] = '';
        foreach ($sellers as $key => $seller) {
            if( $data['filter_seller_id'] == $seller['seller_id']){
                $data['filter_seller_name'] = $seller['company'];
            }
        }
        $data['sellers'] = $sellers;
        $data['purchase_firms'] = $this->model_wsb_purchase_import->getPurchaseFirms();

        $data['token'] = $this->session->data['token'];

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $sort_urls = array('invoice_no' => '','invoice_date' =>'','total_purchase_value'=>'','date_added'=>'');
        foreach ($sort_urls as $field => $link) {
            if($field == $data['order_by']){
                if($data['order']=='DESC'){
                    $sort_urls[$field] =  $this->url->link('wsb_purchase/import',$url,'SSL').'&order_by='.$field.'&order=ASC';
                }
                else{
                    $sort_urls[$field] =  $this->url->link('wsb_purchase/import',$url,'SSL').'&order_by='.$field.'&order=DESC';
                }
            }
            else{
                $sort_urls[$field] =  $this->url->link('wsb_purchase/import',$url,'SSL').'&order_by='.$field.'&order=DESC';
            }
        }
        $data['sort_url'] = $sort_urls;
        $pagination = new Pagination();
        $pagination->total = $wsb_purchase_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('wsb_purchase/import', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf( $this->language->get('text_pagination'),
                                    ($wsb_purchase_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($wsb_purchase_total - $this->config->get('config_limit_admin'))) ?
                                    $wsb_purchase_total :
                                    ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                     $wsb_purchase_total, ceil($wsb_purchase_total / $this->config->get('config_limit_admin'))
                                 );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
                
        if (in_array($this->user->getId(), explode(',',ADMIN_IDS))) {
            $data['show_delete_btn']    = 1;
        }else{
            $data['show_delete_btn']    = 0;
        }

        $data['session_succ_msg'] = '';

        if (!empty($this->session->data['success_msg'])) {
            $data['session_succ_msg'] = $this->session->data['success_msg'];
            unset($this->session->data['success_msg']);
        }

        $this->response->setOutput($this->load->view('wsb_purchase/report.tpl', $data));
    }

    /**
    * add wsb purchase
    * it will validate the csv file data
    * than display preview
    * than we can submit the data for add purchase
    * @author kalyan 28th Oct. 2017 Edited
    */
    public function import() {

        $this->load->model('wsb_purchase/import');
        $this->load->model('catalog/product');
        
        $products                       = array();
        $errorNotFound                  = 1;
        $data                           = array();
        $systemError                    = '';
        $pickUpCityCode                 = 'not_sor';
        $commonError                    = 'Please check below listed every product for errors.';
        $sor_order                      = 0;
        $wsb_seller_id                  = 0;
        /*
        * check order is sor or not sor
        */

        if (isset($this->request->post['sor_purchase']) && $this->request->post['sor_purchase']==1){ 

            $sor_order      = 1;            
        }
        /*
        * get wsb seller id
        */
        if (!empty($this->request->post['purchase_firm'])) {
            
            $wsb_seller_id  = $this->model_wsb_purchase_import->getSorSellerId($this->request->post['purchase_firm']);
        }
        /*
        * checking validation
        */
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            
            $file_type_error            = false;
            $mimes                      = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
            $sellerId                   = $this->request->post['seller_id'];

            /*
            * check duplicate invoice no for purchase
            */
            if ($this->model_wsb_purchase_import->checkDuplicatWsbPurchase($this->request->post['seller_id'], $this->request->post['invoice_no'])) {
                $errorNotFound                  = 0;
                $commonError                    = 'This invoice no. is duplicate for this seller.';
            }

            if(isset($_FILES['purchase_csv']['type']) && !in_array($_FILES['purchase_csv']['type'],$mimes)){
                
                $file_type_error = true;               
            }
            /*
            * get product record when prew mode set
            */ 
            if(!empty($_FILES['purchase_csv']['tmp_name']) && !$file_type_error){
                
                $file           = fopen($_FILES['purchase_csv']['tmp_name'],"r");
                /*
                * reading csv file code
                */
                if($file){
                    $i = 0;
                    while(! feof($file)){
                        if(!empty( $product = fgetcsv($file))){
                            if( strtolower(trim($product[0])) != 'sku' ){

                                $products[$i]['sku']                       = trim($product[0]);
                                $products[$i]['pieces']                    = (int)(trim($product[1]));
                                $products[$i]['transfer_price_per_piece']  = trim($product[2]);
                                $products[$i]['seller_tax']                = (float)(trim($product[3]));
                                $i++;
                            }
                        }
                    }
                }
                fclose($file);                
            }else{
               $products          = $this->request->post['product'];  
            }
            /*
            *
            */
            $mimes2                      = array('mage/png','image/jpeg','image/gif','image/bmp');
                
            if(isset($_FILES['purchase_image']['type']) && !in_array($_FILES['purchase_image']['type'],$mimes2)){
            
                $file_type_error = true;               
            }
            /*
            * checkin validations
            */           
            if (!empty($products) && count($products)>0) {

                $taxClass               = new Tax($this->registry);

                foreach ($products as $key => $value) {
                    /*
                    * set key for errors
                    */
                    $products[$key]['error'] = array();
                    /*
                    * get seller nickname
                    */
                    $sellerData     = SellerInfo::getSellerNicknameBasedOnSku($this->db,trim($value['sku']));

                    if (!empty($sellerData) && count($sellerData)>0) {

                        $products[$key]['nickname'] = $sellerData['nickname'];
                    }else{

                       $products[$key]['nickname'] = ''; 
                    }                    
                    /*
                    * get product store code
                    */
                    $productData = array();

                    $productData = $this->model_wsb_purchase_import->checkProducts(array($value['sku']), $sor_order);
                    
                    //prd($productData);

                    if (!empty($productData) && count($productData)>0) {
                        
                        $products[$key]['store_sales']  = $productData[0]['store_sales'];
                        $products[$key]['hsn_code']     = $productData[0]['hsn_code'];
                        $products[$key]['product_id']   = $productData[0]['product_id'];
                        $products[$key]['piece_in_set'] = $productData[0]['piece_in_set'];
                        $products[$key]['old_quantity'] = $productData[0]['quantity'];
                        $products[$key]['seller_id']    = $productData[0]['seller_id'];

                    }else{
                        
                        $products[$key]['store_sales']  = '';
                        $products[$key]['hsn_code']     = '';
                        $products[$key]['product_id']   = '';
                        $products[$key]['piece_in_set'] = '';

                        $products[$key]['error'][0] = 'This sku is not present in system';
                        $errorNotFound = 0;

                    }                                       
                    /*
                    * if found more than two product in database for particular sku
                    * than add value in array()
                    */
                    if (!empty($productData) && count($productData)>1) {
                        $products[$key]['error'][1] = 'This sku has duplicate records in system';
                        $errorNotFound = 0;
                    }
                    /*
                    * check seller tax
                    */
                    if (isset($value['seller_tax']) && $value['seller_tax']!='') {
                    }else{
                        $products[$key]['error'][2] = 'Seller Tax is not present for this sku in CSV File';
                        $errorNotFound = 0;
                    }
                    /*
                    * get tax class id
                    */                    
                    $tax_calss_id = '';
                    if (isset($productData[0]['hsn_code']) && $productData[0]['hsn_code']!='') {

                        $tax_calss_id = $taxClass->getTaxClassIdFromHSNCode($productData[0]['hsn_code']);
                    }                   
                                
                    if ($tax_calss_id>0) {
                       
                       $tarRate = $taxClass->getTaxRateForTaxIncludedPrice($value['transfer_price_per_piece'],$productData[0]['hsn_code'],$productData[0]['mrp']);
                       
                       if ($tarRate=='' || $tarRate!=$value['seller_tax']) {
                            
                            $products[$key]['error'][3] = 'Seller Tax is not proper in system for this sku';
                            $errorNotFound = 0;
                       }
                    }else{

                            $products[$key]['error'][4] = 'HSN CODE is not proper in system for this sku';
                            $errorNotFound = 0;
                    }
                    /*
                    * check pieces for product sets
                    * pieces in set
                    * total pieces
                    */ 
                    if (isset($productData[0]['piece_in_set']) && $productData[0]['piece_in_set']!='') {                   
                        
                        if(!$this->checkValidPiecesForProductSets($productData[0]['piece_in_set'],$value['pieces'])){
                            
                            $products[$key]['error'][5] = 'Pieces In Set is not proper in CSV file for this sku';
                            $errorNotFound = 0;
                        }
                    }
                    /*
                    * check product is importing second time
                    * if product is importing first time than it's quantity should be 0
                    */
                    $sorImportIsSecondTime  = 0;

                    if (isset($productData[0]['product_id']) && $productData[0]['product_id']!='') {

                       $sorImportIsSecondTime  = $this->model_wsb_purchase_import->checkSorPurchaseIsSecondTime($productData[0]['product_id']);                            
                    }
                   
                    if(isset($productData[0]['quantity']) && $productData[0]['quantity']!=0 && !$sorImportIsSecondTime){
                        
                        $products[$key]['error'][6] = 'This '.$value['sku'].' SKU is importing first time so quantity should be 0.';
                        $errorNotFound = 0;
                    }              
                    /*
                    * check whether purchase related to sor or not
                    */                    
                    if ($sor_order ==1){

                    }else{
                        if (isset($productData[0]['sor_product']) && $productData[0]['sor_product']==1) {
                            
                            $products[$key]['error'][9] = 'This '.$value['sku'].' SKU is SOR SKU so create new SKU for this product.';
                            $errorNotFound = 0;
                        }

                    }
                    /*
                    * check transfer price will be seme in CSV and oc_product table
                    */
                    if (isset($productData[0]['price']) && $value['transfer_price_per_piece']!= $productData[0]['price']) {
                        
                        $products[$key]['error'][10] = 'This '.$value['sku'].' SKU price is not valid. Price should be same in CSV and in system';
                            $errorNotFound = 0;
                    }
                    /*
                    * check 0 stock in CSV file
                    */
                    if ($products[$key]['pieces']<1) {
                        
                        $products[$key]['error'][11] = 'This '.$value['sku'].' SKU have 0 stock in CSV please check CSV.';
                            $errorNotFound = 0;
                    }



                }                   
            }else{
                
                $systemError = 'Error';
                $errorNotFound = 0;
            }
        }
        /*
        * submit the code for add purchase
        */
        if ($errorNotFound && $this->request->server['REQUEST_METHOD'] == 'POST'  && isset($this->request->post['submit'])) {       
            /*
            * validate all csv products
            */
            $producInsertData = array();

            if(!empty($products) && count($products)>0){            

                $total  = 0;
                foreach ($products as $key => $value) {
                    
                    $producInsertData[$key]['product_id']                 = $value['product_id'];
                    $producInsertData[$key]['sku']                        = $value['sku'];
                    $producInsertData[$key]['pieces']                     = $value['pieces'];
                    $producInsertData[$key]['transfer_price_per_piece']   = $value['transfer_price_per_piece'];
                    $producInsertData[$key]['seller_tax']                 = $value['seller_tax'];                   

                    $total += round($value['transfer_price_per_piece'],2) * (int)$value['pieces'];
                }
                /*
                * set purchase bill image
                */
                $data['purchase_bill_image'] = '';

                if(!empty($_FILES['purchase_image']['type'])){
                
                    $data['purchase_bill_image']          = $this->request->post['invoice_no'].'_'.$_FILES['purchase_image']['name'];              
                }
                /*
                * get seller Nickname
                */
                $sellerDetails  = SellerInfo::getSellerFirmDetails($this->db,$this->request->post['seller_id']);

                $sellerNickName = $sellerDetails['nickname'];
                
                $data['seller_id']          = $this->request->post['seller_id'];
                $data['purchase_firm_id']   = $this->request->post['purchase_firm'];
                $data['invoice_no']         = $this->request->post['invoice_no']; 
               
                if (isset($this->request->post['payment_done'])) {
                      $data['payment_done']       = $this->request->post['payment_done'];
                }else{
                      $data['payment_done']       = 0;
                }  
                
                $data['invoice_date']       = $this->request->post['invoice_date'];
                $data['payment_release_invoice_date_gap'] = $this->request->post['payment_release_invoice_date_gap'];
                $data['total_purchase_value']   = $total;
                $data['products']               = $producInsertData;

                $seller_inv                     = new SellerInvoice($this);
                $seller_firm_meta               = $seller_inv->getSellerDetails($data['seller_id']);

                $data['seller_firm_meta']       = serialize($seller_firm_meta);

                $purchase_firm_meta             = $this->model_wsb_purchase_import->getPurchaseFirmDetails($data['purchase_firm_id']);
                $data['purchase_firm_meta']     = serialize($purchase_firm_meta);
                
                if (isset($this->request->post['sor_purchase']) && $this->request->post['sor_purchase']==1) {

                    $data['sor_purchase']           = $this->request->post['sor_purchase'];
                    $data['payment_done']           = 1;

                }else{
                    $data['sor_purchase']           = 0;
                }                        

                $wsbPurchaseInvoiceId = $this->model_wsb_purchase_import->addWsbPurchase($data);
                /*
                * upload purchase bill image
                */
                if ($wsbPurchaseInvoiceId>0) {
                    
                   if(!empty($_FILES['purchase_image']['type'])){

                        $dir_name   = DIR_WSB_PURCHASE_IMAGE.$sellerNickName.'/';
                        $file       = $dir_name.$data['purchase_bill_image'];

                        if (!file_exists($dir_name)) {

                            mkdir($dir_name, 0777, true);
                        }
                        
                        move_uploaded_file($_FILES['purchase_image']['tmp_name'], $file); 
                    } 
                }
               /*
                * update product table with stock, sor_product and sor_invoice_no
                */
                    $stock_products     = array();
                    $product_id_array   = array();
                    $changes_data       = array();

                    foreach ($products as $keyin => $valuein) {
                        
                        $sorProductData = array();
                        /*
                        * update quantity with old stock
                        */
                        $sorProductData['pieces']           = (((int)$valuein['old_quantity']) + ((int)$valuein['pieces']/(int)$valuein['piece_in_set']));
                        $sorProductData['invoice_id']       = $wsbPurchaseInvoiceId;
                        $sorProductData['product_id']       = $valuein['product_id'];
                        $sorProductData['sku']              = $valuein['sku'];
                        $sorProductData['sor_order']        = $sor_order;
                        
                        if ($valuein['old_quantity']>1 && $valuein['seller_id']!=$sor_seller_id && $valuein['seller_id']!=$this->request->post['seller_id']) {

                            $stock_products[$keyin]['sku'] = $valuein['sku'];

                            $sorProductData['status']        = 0;
                        }
                        
                        $this->model_wsb_purchase_import->updateSorProductsWithStock($sorProductData); 

                    }

                    $product_id_array = array_column($products,'product_id');
                    /*
                    * assign seller to product
                    */
                    $this->model_catalog_product->ProductAssignToSeller($wsb_seller_id, $product_id_array, $changes_data);
                    
                $this->response->redirect($this->url->link('wsb_purchase/import','&token='.$this->request->get['token'],'SSL'));                
            }
        }        
        /*
        * display preiew mode
        */
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['preview']) ) {
             
            $data['seller_id']          = $this->request->post['seller_id'];
            $data['purchase_firm_id']   = $this->request->post['purchase_firm'];
            $data['invoice_no']         = $this->request->post['invoice_no'];
            $data['invoice_date']       = $this->request->post['invoice_date'];
            
            if (isset($this->request->post['payment_done'])) {
                
                $data['payment_done'] = $this->request->post['payment_done'];
            }else{
                
                $data['payment_done'] = 1;
            }
            if (!empty($this->request->post['payment_release_invoice_date_gap'])) {
                
                $data['payment_release_invoice_date_gap'] = $this->request->post['payment_release_invoice_date_gap'];
            }else{
                $data['payment_release_invoice_date_gap'] = 0;
            }
            
            if (isset($this->request->post['sor_purchase']) && $this->request->post['sor_purchase']==1) {
                $data['sor_purchase'] =1 ;
            }else{
                $data['sor_purchase'] =0;    
            }
            if (!empty($products) && count($products)>0) {
                $data['products'] = $products;
            }           
            
        }else{
               
            $data['seller_id']          = "";
            $data['purchase_firm_id']   = "";
            $data['invoice_no']         = "";
            $data['payment_done']       = 1;
            $data['invoice_date']       = "";
            $data['payment_release_invoice_date_gap'] = 0;
            $data['sor_purchase']       = "";

            if (!empty($products) && count($products)>0) {
                $data['products'] = $products;
            }

        }

        $this->load->autoLoadLanguage('wsb_purchase/import', $data);

        $data['breadcrumbs']    = array();
        $data['breadcrumbs'][]  = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/home')
        );

        $this->document->setTitle($data['page_title']);

        $data['form_action']                = 'index.php?route=inventory/import/index'.'&token=' . $this->session->data['token'];

        $data['column_left']                = $this->load->controller('common/column_left');
        $data['column_right']               = $this->load->controller('common/column_right');
        $data['content_top']                = $this->load->controller('common/content_top');
        $data['content_bottom']             = $this->load->controller('common/content_bottom');
        $data['footer']                     = $this->load->controller('common/footer');
        $data['header']                     = $this->load->controller('common/header');
        $data['sample_file']                = $this->url->link('wsb_purchase/import/downloadSample','&token=' . $this->session->data['token'],'SSL');
        /*
        * get seller data
        */
        $seller_profile         = new SellerProfile($this);        
        $data['seller_name']    = "";
        $sellers                = $seller_profile->getSellers(array('get_address'=>true,'only_seller_invoice_generate'=>'1'));

        foreach ($sellers as $key => $seller) {
            if( $data['seller_id'] == $seller['seller_id']){
                $data['seller_name'] = $seller['company'];
            }
        }
        
        $data['sellers']        = $sellers;
        $data['purchase_firms'] = $this->model_wsb_purchase_import->getPurchaseFirms();
        /*
        * set errors
        */
        
        if ($systemError!='' || !$errorNotFound) {
            $data['error_warning'] = $commonError;
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['import_seller'])) {
            $data['error_seller'] = $this->error['import_seller'];
        } else {
            $data['error_seller'] = '';
        }
        $this->response->setOutput(($this->load->view('wsb_purchase/import.tpl', $data)));
    }
    /**
     * Method to show wsbPurchases -> Analysis Menu
     * Used to display Inventory listing Storewise
     * Method to return an array of all the unique children category ids
     * given parent category ids. It goes upto infinite level deep.
     * Input(s):
     * @param array  data
     * Author: Murtaza
     */
    public function analysis() {

        $this->load->model('wsb_purchase/import');

        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        $this->document->setTitle($this->language->get('heading_title'));

        $data['nickname'] = "";
        $data['sku'] = "";
        $data['store_sales'] = "";
        $data['invoice_date'] = "";
        $data['filter_date_added_from'] = "";
        $data['filter_date_added_to'] = "";

        $data['filter_purchase_value_from'] = "";
        $data['filter_purchase_value_to'] = "";

        $data['order_by'] = 'purchase_id';
        $data['order'] = 'desc';
        $data['page'] = 1;


        $url = array();
        foreach ($this->request->get as $key => $value) {
             $data[$key] = $value;
             $url[] = "$key=$value";
        }

        $data['offset'] = ($data['page'] - 1) * $this->config->get('config_limit_admin');
        $url = implode('&',$url);

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
        $data['limit'] = $this->config->get('config_limit_admin');
        // Get Store Sales Options

        $data['store_sales_options'] = $this->db->getEnumValues(DB_PREFIX . 'product','store_sales');

        $data['store_sales_options'] = array_splice($data['store_sales_options'], 1, 4);

        $wsb_purchase_data = $this->model_wsb_purchase_import->getProducts($data);
        
        $wsb_purchase_total = $this->model_wsb_purchase_import->getProductsTotal($data);

        if(isset($wsb_purchase_data)) {
            $wsb_sales = $this->model_wsb_purchase_import->getSales(array_keys($wsb_purchase_data));

            foreach ($wsb_purchase_data as $product_id => $purchase_data ) {
                if(isset($wsb_sales[$product_id])) {

                    $wsb_purchase_data[$product_id]['sales_data2'] = $wsb_sales[$product_id];
                    $wsb_purchase_data[$product_id]['total_sales'] = array_sum(array_column($wsb_purchase_data[$product_id]['sales_data2'], 'pieces'));

                } else {
                    $wsb_purchase_data[$product_id]['sales_data2'] = array();
                    $wsb_purchase_data[$product_id]['total_sales'] = 0;
                }

                if(isset($wsb_sales[$product_id])) {
                    $wsb_purchase_data[$product_id]['aging'] = $this->model_wsb_purchase_import->calculateAging(
                    $wsb_sales[$product_id],
                    $wsb_purchase_data[$product_id]['purchase_data']
                    );
                }

                $wsb_purchase_data[$product_id]['total_purchasesmy'] = array_sum(array_column($wsb_purchase_data[$product_id]['purchase_data'], 'pieces'));

                $wsb_purchase_data[$product_id]['available_pieces'] = ($wsb_purchase_data[$product_id]['total_purchasesmy']-$wsb_purchase_data[$product_id]['total_sales']);

                $wsb_purchase_data[$product_id]['total_value'] = ($wsb_purchase_data[$product_id]['available_pieces'] * $wsb_purchase_data[$product_id]['price']);
                $wsb_purchase_data[$product_id]['img_width'] = $this->config->get('config_image_additional_width');
                $wsb_purchase_data[$product_id]['img_height'] = $this->config->get('config_image_additional_height');

            }
        }

        $data['wsb_purchase'] = $wsb_purchase_data;

        // Autoloading the lanugage
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('wsb_purchase/import', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        $data['add'] = $this->url->link('wsb_import/import/import', 'token=' . $this->session->data['token'] . $url, 'SSL');

         if (isset($this->session->data['error'])) {
             $data['error_warning'] = $this->session->data['error'];

             unset($this->session->data['error']);
         } elseif (isset($this->error['warning'])) {
             $data['error_warning'] = $this->error['warning'];
         } else {
             $data['error_warning'] = '';
         }

         if (isset($this->session->data['success'])) {
             $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
         } else {
             $data['success'] = '';
         }

        $pagination = new Pagination();
        $pagination->total = $wsb_purchase_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('wsb_purchase/import/analysis', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($wsb_purchase_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($wsb_purchase_total - $this->config->get('config_limit_admin'))) ? $wsb_purchase_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $wsb_purchase_total, ceil($wsb_purchase_total / $this->config->get('config_limit_admin')));


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('wsb_purchase/analysis.tpl', $data));

    }


    public function downloadSample(){
        $file = DIR_DOWNLOAD.'wsb_purchase_sample.csv';
        // echo $file;die;
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header('Cache-Control: no-cache');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
    }

    protected function validateForm() {
        $this->load->language('inventory/import');
        if (!$this->user->hasPermission('modify', 'wsb_purchase/import')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (empty($this->request->post['seller_id'])) {
            $this->error['import_seller'] = $this->language->get('error_seller');
        }
        if (empty($this->request->post['purchase_firm'])) {
            $this->error['import_purchase_firm'] = $this->language->get('error_purchase_firm');
        }
        if (empty($this->request->post['invoice_no'])) {
            $this->error['import_invoice_no'] = $this->language->get('error_invoice_no');
        }
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }

    private function saveData(){
        $csv_data = $this->csv_data;
        $heading = array();
        $heading = array_shift($csv_data);
        $num = count($heading);
    }
    /*
    * check total pieces are valid for product sets
    */
    private function checkValidPiecesForProductSets($piecesInSets, $totalPieces){
        if (($totalPieces % $piecesInSets)==0) {
            return true;
        }else{
            return false;
        }
    }
    /**
    * delete wsb purchase 
    * @param purchase_id
    * @author Kalyan 17th Oct. 2017
    */
    public function deleteWsbPurchase(){

        if (isset($this->request->get['purchase_id']) && $this->request->get['purchase_id']!='') {
             
            $this->load->model('wsb_purchase/import');

            $purchaseId = $this->request->get['purchase_id'];
            /*
            * check the purchase entery 
            */
            $data = $this->model_wsb_purchase_import->validPurchaseForDelete($purchaseId);
            
            if (!empty($data) && count($data)>0) {
                /*
                * delete the record
                */
                $status = $this->model_wsb_purchase_import->deleteWsbPurchase($purchaseId);
                /*
                * if purchase is deleted than send mail
                */
                if ($status) {
                    $mailStatus = $this->sendMailForDeleteWsbPurchase($data);
                    if ($mailStatus) {
                        $this->session->data['success_msg'] = 'Success : Purchase has been deleted';
                    }else{
                       $this->session->data['success_msg'] = 'Error : Purchase Deleted but EMAIL not sent'; 
                    }
                }else{
                  $this->session->data['success_msg'] = 'Error : Some Problem in delete';  
                }
                
            }else{
                $this->session->data['success_msg'] = 'Error : Delete not possiable';
            }
            /*
            * redirect the page on report listing page
            */
            $this->response->redirect($this->url->link('wsb_purchase/import','token='.$this->request->get['token'],'SSL'));
        }
    }
    /**
    * send mail whan wsb purhcase is deleted 
    * @param wsb purchase detail
    * @author Kalyan 23th Oct. 2017
    */
    public function sendMailForDeleteWsbPurchase($data){
        
        $html = '<table align="left" style="width:100%; border:solid #ccc 1px;">';
            
            $html .= '<tr>';
                $html .= '<td style="border-right:solid #ccc 1px;">Invoice No</td>';
                $html .= '<td style="border-right:solid #ccc 1px;">Invoice Date</td>';
                $html .= '<td style="border-right:solid #ccc 1px;">Purchase Value</td>';
                $html .= '<td style="border-right:solid #ccc 1px;">Purchased Firm</td>';
                $html .= '<td style="border-right:solid #ccc 1px;">Seller</td>';
                $html .= '<td style="border-right:solid #ccc 1px;">Payment Status</td>';
                $html .= '<td style="border-right:solid #ccc 1px;">User</td>';
                $html .= '<td>Date Added</td>';
            $html .= '</tr>';
            
            $udata = unserialize($data[0]['user']);
            //pr($udata);

            $html .= '<tr>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$data[0]['invoice_no'].'</td>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$data[0]['invoice_date'].'</td>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$this->currency->format($data[0]['total_purchase_value']).'</td>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$data[0]['purchase_firm_name'].'</td>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$data[0]['company'].'</td>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">Delayed '.$data[0]['payment_release_invoice_date_gap'].' days From invoice date.</td>';
                $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$udata['name'].'</td>';
                $html .= '<td style="border-top:solid #ccc 1px;">'.date('Y-m-d',strtotime($data[0]['date_added'])).'</td>';
            $html .= '</tr>';

        $html .= '</table>';

            $html .= '</br>';
            $html .= '</br>';
            $html .= '</br>';
        /*
        * product breakup table
        */
        $html .= '<table align="left" style="width:100%; border:solid #ccc 1px;">';
            
            $html .= '<tr>';
            $html .= '<td style="border-right:solid #ccc 1px;">SKU</td>';
            $html .= '<td style="border-right:solid #ccc 1px;">Pieces</td>';
            $html .= '<td>Price Per Piece</td>';            
            $html .= '</tr>';
            
            $udata = unserialize($data[0]['user']);
            //pr($udata);
            foreach ($data as $key => $value) {
                $html .= '<tr>';
                    $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$value['sku'].'</td>';
                    $html .= '<td style="border-top:solid #ccc 1px;border-right:solid #ccc 1px;">'.$value['pieces'].'</td>';
                    $html .= '<td style="border-top:solid #ccc 1px;">'.$value['transfer_price_per_piece'].'</td>';
                $html .= '</tr>';
            }
            

        $html .= '</table>';
        /*
        * send email 
        */
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        ;
        $mail->SMTPSecure = 'ssl';

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
        
        $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);

        $mail->Subject = 'WSB Purchase Deleted - Invoice No. is ('.$data[0]['invoice_no'].')';
        $mail->Body = $html;

        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    }

}
?>
