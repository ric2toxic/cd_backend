<?php

class ControllerSellerSellerArchiveInventory extends Controller {
	
	public  $data = array();

    public function index(){

        $this->load->autoLoadLanguage('seller/manage-inventory',$this->data);
        $this->load->autoLoadLanguage('multiseller/multiseller',  $this->data);
        $this->load->model('seller/manage_inventory');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $data = array();

        $colMap = array(
            'sku' => 'p.sku',
            'name' => 'pd.name',
            'quantity' => 'p.quantity'
        );

        $sorts = array('sku', 'name', 'quantity');

        $filters = array_merge($sorts, array('products'));

        if (isset($this->request->get['page_wholesale'])) {
            $page_wholesale = $this->request->get['page_wholesale'];
        } else {
            $page_wholesale = 1;
        }
        $url = '';

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
            $url .= '&limit=' . $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_product_limit');
        }
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
            array(
                'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
                'href' => $this->url->link('seller_panel/account-order', '', 'SSL'),
            ),
            array(
                'text' => 'Archive Inventory',
                'href' => $this->url->link('seller/seller_archive_inventory', '', 'SSL'),
            )
        ));

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
            $url .= '&sort=' . $this->request->get['sort'];
        } else {
            $sort = 'p.date_modified';
        }

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if(isset($this->request->post['products_per_page']) && !empty($this->request->post['products_per_page'])){
            $limit = $this->request->post['products_per_page'];
            $url .= '&limit=' . $this->request->post['products_per_page'];
        }

        $seller_id = $this->customer->getId();

        $this->data['seller_store'] = $this->MsLoader->MsProduct->getSellerStores($seller_id);
        $this->data['stores'] = array();
        if(isset($this->data['seller_store']) && !empty($this->data['seller_store'])){
            foreach ($this->data['seller_store'] as $st) {
                $this->data['stores'][] = $this->MsLoader->MsProduct->getStore($st['store_id']);
            }
        }

        $con_wholesale['seller_id']=$seller_id;
        $con_wholesale['is_single']= 0;

        $sort_wholesale['order_way'] = $order;

        $this->data['searchTextValue']      = '';
        $this->data['category_id']          = '';
        $this->data['low_price']            = '';
        $this->data['price']                = '';
        $this->data['searchTextNameValue']  = '';
        $this->data['text_vacation_enable'] = $this->language->get('text_vacation_enable');
        $this->data['text_vacation_disable']= $this->language->get('text_vacation_disable');
        $this->data['searchTextLabel']      = $this->language->get('text_searchTextLabel');
        $this->data['searchTextNameLabel']  = $this->language->get('text_searchTextNameLabel');
        $this->data['off_vacation_mgs']     = $this->language->get('text_off_vacation_mgs');
        $this->data['on_vacation_mgs']      = $this->language->get('text_on_vacation_mgs');


        // Check seller sell at SOR or not
        $this->data['sor_button_label']         = $this->language->get('ms_sor_enabled');
        $this->data['sor_css']                  = 'sor_enabled';
        $this->data['change_to_sor']            = 1;
        $check_sor = $this->model_seller_manage_inventory->checkSellerSOR($seller_id);
        if($check_sor['sor_enabled'] == 1){
            $this->data['sor_button_label']     = $this->language->get('ms_sor_disabled');
            $this->data['sor_css']              = 'sor_disabled';
            $this->data['change_to_sor']        = 0;
        }
        $this->data['entry_markup_price']       = $this->language->get('entry_markup_price');
        $this->data['text_enter_markup_price']  = $this->language->get('text_enter_markup_price');
        $this->data['error_sor_mgs']            = $this->language->get('error_sor_mgs');

        $seller_markup_price = $this->model_seller_manage_inventory->getSellerMarkupPrice($seller_id);
        if(!empty($seller_markup_price['seller_markup_over_tp'])){
            $this->data['seller_markup_price']  = $seller_markup_price['seller_markup_over_tp'];
        }else{
            $this->data['seller_markup_price']  = '';
        }

        if(isset($this->request->get['filters']) && !empty($this->request->get['filters'])){
            $this->request->post['filters']  = $this->request->get['filters'];
            $this->data['searchTextValue']   = $this->request->post['filters'];
        }
        if(isset($this->request->get['category_id']) && !empty($this->request->get['category_id'])){
            $this->request->post['category_id']  = $this->request->get['category_id'];
            $this->data['category_id']   = $this->request->post['category_id'];
        }
        if(isset($this->request->get['low_price']) && !empty($this->request->get['low_price'])){
            $this->request->post['low_price']  = $this->request->get['low_price'];
            $this->data['low_price']   = $this->request->post['low_price'];
        }
        if(isset($this->request->get['price']) && !empty($this->request->get['price'])){
            $this->request->post['price']  = $this->request->get['price'];
            $this->data['price']   = $this->request->post['price'];
        }

        $filter_data['filters'] = '';
        if(isset($this->request->post['filters']) || isset($this->request->post['category_id']) || isset($this->request->post['price']) || isset($this->request->post['low_price'])){


            if(isset($this->request->post['filters']) && ($this->request->post['filters'] != '' || $this->request->post['filters'] != null)) {
                $this->data['searchTextValue']      = $this->request->post['filters'];
                $filter_data['filters']['p.sku']    = $this->request->post['filters'];
                $url .= '&filters='.$this->request->post['filters'];
            }
            if(isset($this->request->post['category_id']) && $this->request->post['category_id'] != 0) {
                $this->data['category_id']              = $this->request->post['category_id'];
                $filter_data['filters']['p.category_id']= $this->request->post['category_id'];
                $url .= '&category_id='.$this->request->post['category_id'];
            }
            if(isset($this->request->post['low_price']) && ($this->request->post['low_price'] != '' || $this->request->post['low_price'] != null)) {
                $this->data['low_price']              = $this->request->post['low_price'];
                $filter_data['filters']['p.low_price']= $this->request->post['low_price'];
                $url .= '&low_price='.$this->request->post['low_price'];
            }
            if(isset($this->request->post['price']) && ($this->request->post['price'] != '' || $this->request->post['price'] != null)) {
                $this->data['price']                  = $this->request->post['price'];
                $filter_data['filters']['p.price']    = $this->request->post['price'];
                $url .= '&price='.$this->request->post['price'];
            }
        }

        $cols = $colMap;
        $sort_wholesale['limit'] = $limit;

        $this->data['limit'] = $limit;
        $this->data['sort_wholesale'] = $sort_wholesale;
        $this->data['page_wholesale'] = $page_wholesale;

        $filter_data['order_by'] = $sort;
        $filter_data['order_way'] = $order;
        $filter_data['limit'] = $limit;
        $sort_wholesale['filters'] =$filter_data['filters'];

        if (isset($this->request->post['stock_status_change']) && !empty($this->request->post['stock_status_change']) ) {
            $stock_status = $this->request->post['stock_status_change'];
            if (isset($this->request->post['selected']) && !empty($this->request->post['selected'])) {
                foreach ($this->request->post['selected'] as $product_id) {
                    $this->model_seller_manage_inventory->InformSellerChangeLog('Stock_status', $stock_status, $product_id);
                    $this->model_seller_manage_inventory->changeStockStatusProducts($product_id, $stock_status);
                }
            }
        }
        
        $flag = 1;
        $sellerdataCount_wholesale = $this->model_seller_manage_inventory->getTotalSellerProducts($con_wholesale,$sort_wholesale, $cols, $flag);
        $filter_data['offset'] = ($page_wholesale - 1) * $limit;
        $sellerdata_wholesale = $this->model_seller_manage_inventory->getSellerProducts($con_wholesale, $filter_data, $cols, $flag);
		
        //echo "<pre>"; print_r($sellerdata_wholesale); exit;
        $pagination_wholesale = new Pagination();
        $pagination_wholesale->total = $sellerdataCount_wholesale;
        $pagination_wholesale->page = $page_wholesale;
        $pagination_wholesale->limit = $limit;
        if ($order == 'DESC') {
            $page_order = $url . '&order=DESC';
        } else {
            $page_order = $url . '&order=ASC';
        }
        $pagination_wholesale->url = $this->url->link('seller/seller_archive_inventory/', $page_order . '&page_wholesale={page}');

        $this->data['pagination_wholesale'] = $pagination_wholesale->render();

        $this->data['results_wholesale'] = sprintf($this->language->get('text_pagination'), ($sellerdataCount_wholesale) ? (($page_wholesale - 1) * $limit) + 1 : 0, ((($page_wholesale - 1) * $limit) > ($sellerdataCount_wholesale - $limit)) ? $sellerdataCount_wholesale : ((($page_wholesale - 1) * $limit) + $limit), $sellerdataCount_wholesale, ceil($sellerdataCount_wholesale / $limit));


        // Check if this seller is not a WSB store seller
        $wsb_store_sellers = explode(',',WSB_STORE_SELLERS);
        $seller_is_wsb_store = false;
        if ( in_array($this->customer->getId(), $wsb_store_sellers) ) {
            $seller_is_wsb_store = true;
        }
        /*
         * making new object of Inventory Archive to find out if product is archive or not
         * 1 => product is archive
         * 0 => product is not archive
         * */
        $archive_inventory = new InventoryArchive($this);

        foreach ($sellerdata_wholesale as $result) {
            if ($result['p.image']) {
                $image = $this->model_tool_image->resize($result['p.image'], 150, 150);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', 150, 150);
            }
            $pid = $this->model_seller_manage_inventory->GetProductIsSingle($result['model']);
            if(isset($pid) && !empty($pid)){
                $singles_product_option_size   = $this->model_seller_manage_inventory->GetProductSize($pid['product_id']);
                $singles_price  = $this->model_seller_manage_inventory->getSinglesPrice($pid['product_id']);
            }else{
                $singles_product_option_size       = '';
                $pid['product_id']  = '';
                $singles_price      = '';
            }
			
			$result['is_archived'] = $archive_inventory->getInventoryArchive($result['product_id']);
            // Get product option size quantity like - 38,40,42
            $wholesale_product_option_size = $this->model_seller_manage_inventory->GetProductSize($result['product_id']);
            //check if product exists in wholesale store
            $store_product = $this->model_seller_manage_inventory->GetStoreProduct($result['product_id'], 0);
            if(isset($store_product) && !empty($store_product)){
                $store_product = $store_product['product_id'];
            }else{
                $store_product = '';
            }

            $store['store_price'] = array();
            $count = 0;

            //Set store related information in data variable. LIke store price for a product, if a store is enabled/diabled for a store
            $enabled_on_stores = array();
            foreach ($result['store_info'] as $store) {
                $store_price  =   $store['store_price'];
                $store_id     =   $store['store_id'];

                if($store_price == 0){
                    //$this->load->model('catalog/product');
                    //$product_info = $this->model_catalog_product->getProduct($result['product_id']);
                    $markup_price =  $this->customer->getStoreConfigForSeller($store['store_id'], 'config_seller_store_commission');
                    $markup_price_factor = 1.0 + (float)$markup_price / 100;
                    $seller_tax_factor =  1.0 + ( (float)$result['seller_tax'] / 100.0 );

                    $price = ceil(($result['p.price']/$seller_tax_factor) * $markup_price_factor);
                    ///$price = $product_info['price'] - $tax;
                    $result['store_info'][$count]['store_price'] =  $price;
                }



                $store_product_id = $this->model_seller_manage_inventory->GetStoreProduct($result['product_id'], $store['store_id']);
                if(isset($store_product_id) && !empty($store_product_id)){
                    $enabled_on_stores[] = $store['store_id']; //$store_product_id['product_id'];
                }

                $count ++;
            }

            //$product_type = $this->model_seller_manage_inventory->GetProductCategoriesSlug($result['product_id']);
            $product_type['category_url']   = 'index.php?route=seller/category_set_description&product_type=general&popup=true';
            //product_type is for calc. child category and this is used in yardage case so don't remove it
            //category id is a product id which may may be parent or child.
            $product_type['category_id']    = $result['category_id'];
            $product_type['transferprice']  = 'Price/piece';
			
			
            $data_wholesale[] = array(
                'product_id'        => $result['product_id'],
                'image'             => $image,
                'store_product'     => $store_product,
                'enabled_on_stores' => $enabled_on_stores,
                'wholesale_option_sizes'   => $wholesale_product_option_size,
                'singles_option_sizes'      => $singles_product_option_size,
                'single_product_id' => $pid['product_id'],
                'name'              => $result['pd.name'],
                'quantity'          => (int)$result['p.quantity'],
                'sku'               => $result['p.sku'],
                'model'             => $result['model'],
                'stock_status'      => $result['p.stock_status'],
                'product_status'    => $result['status'],
                'seller_id'         => $result['seller_id'],
                'transferprice'     => $result['p.price'],
                'sor_price'         => $sor_product_price,
                'singles_price'     => $singles_price,
                'store_info'        => $result['store_info'],
                'set_description'   => $result['pd.set_description'],
                'category_id'       => $result['category_id'],
                'product_type'      => $product_type['category_url'],
                'pro_category_id'   => $product_type['category_id'],
                'pro_transferprice' => $product_type['transferprice'],
                'description'       => $result['pd.description'],
                'piece_in_set'      => $result['p.piece_in_set'],
                'single_product'    => $result['single_product'],
                'sor_product_id'    => $result['sor_product_id'],
                'special'            => $result['special'],
                'discount_type'     => $result['discount_type'],
                'discount_value'    => $result['discount_value'],
                'offer_date_start'  => $result['offer_date_start'],
                'offer_date_end'    => $result['offer_date_end'],
                'is_archived' 		=> $result['is_archived'],
                'get_pieces_sold'   => $this->model_catalog_product->getPiecesSold($result['product_id']),
                'editable_non_sor'  => (!$seller_is_wsb_store && $result['store_sales'] != 'NO') ? false : true
            );
        }
        $this->data['data_wholesale'] = isset($data_wholesale) ? $data_wholesale : '';
       
        $this->data['imgheading'] = $this->language->get('heading_image');
        $this->data['SKU'] = $this->language->get('heading_sku');
        $this->data['pName'] = $this->language->get('heading_name');
        $this->data['Quantity'] = $this->language->get('heading_quantity');
        $this->data['wholesale_price'] = $this->language->get('heading_wholesale_price');
        $this->data['yourstoreprice'] = $this->language->get('heading_yourstoreprice');
        $this->data['setdescription'] = $this->language->get('heading_setdescription');
        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_no_seller_store_text'] = $this->language->get('text_no_seller_store_text');
        $this->data['form_action'] = 'index.php?route=seller/seller_archive_inventory/index';
        $this->data['single_store'] = $this->url->link('seller/seller_archive_inventory/singlesStore', '', 'SSL');
        $this->data['wholesale_store'] = $this->url->link('seller/seller_archive_inventory/index', '', 'SSL');
        $this->data['searchText'] = $this->language->get('text_searchTextSku');
        $this->data['searchTextName'] = $this->language->get('text_searchTextName');
        $this->data['pieceinset'] = $this->language->get('text_piece_in_set');
        $this->data['add_products'] = $this->url->link('seller/product/add', '', 'SSL');
        $this->data['description'] = $this->language->get('text_description');
        $this->data['header_seller'] = $this->load->controller('common/seller_header');
        $this->data['footer_seller'] = $this->load->controller('common/seller_footer');
        $data['text_category'] = $this->language->get('text_category');
        $this->load->model('catalog/category');
        $this->data['categories'] = array();

        $categories_1 = $this->model_catalog_category->getCategories(0);
        foreach ($categories_1 as $category_1) {
            $level_2_data = array();

            $categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $level_3_data = array();

                $categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $level_3_data[] = array(
                        'category_id' => $category_3['category_id'],
                        'name'        => $category_3['name'],
                    );
                }

                $level_2_data[] = array(
                    'category_id' => $category_2['category_id'],
                    'name'        => $category_2['name'],
                    'children'    => $level_3_data
                );
            }

            $this->data['categories'][] = array(
                'category_id' => $category_1['category_id'],
                'name'        => $category_1['name'],
                'children'    => $level_2_data
            );
        }

        if ($order == 'DESC') {
            $sort_order = '&order=ASC&limit='.$limit;
        } else {
            $sort_order = '&order=DESC&limit='.$limit;
        }

        $seller_id = $this->customer->getId();
       
        $this->data['sort_sku_wholesale'] = $this->url->link('seller/seller_archive_inventory', '&sort=p.sku'. $sort_order, 'SSL');

        $this->data['sort_quantity_wholesale'] = $this->url->link('seller/seller_archive_inventory', '&sort=p.quantity'. $sort_order, 'SSL');

        $this->data['sort_price_wholesale'] = $this->url->link('seller/seller_archive_inventory', '&sort=p.price'. $sort_order, 'SSL');

        if(isset($_GET['sort'])){
            $this->data['sort_1'] = $_GET['sort'];
        }
        if(isset($_GET['order'])) {
            $this->data['order_1'] = $_GET['order'];
        }

        $this->data['order'] = $order;

		
        $this->data['footer'] = $this->load->controller('common/seller_footer');
        $this->data['header'] = $this->load->controller('common/seller_header');

        list($template) = $this->MsLoader->MsHelper->loadTemplate('seller_archive_inventory');
        $this->response->setOutput($this->load->view($template, $this->data));
    }
        
	//method to archive inventories from seller panel
	public function archiveInventory() {
		$seller_id          = $this->customer->getId();
		$product_ids = $this->request->post['product_ids'];
		$archive_inventory = new InventoryArchive($this);
		
		$flag = $archive_inventory->setInventoryArchive(0,$product_ids);
		echo json_encode($flag);
	}
}

?>
