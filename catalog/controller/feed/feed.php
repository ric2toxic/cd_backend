<?php
class ControllerFeedFeed extends Controller
{
    //w5a954d23x57dd56 -> Via
    //a5a954e23x57dd71 -> Adsadesigns.com
    //a5a954e23x57d100 -> Global
    public $tokens = array('w5a954d23x57dd56', 'a5a954e23x57dd71', 'a5a954e23x57d100');
    /**
     * @author KD
     * @return void
     * @example to create csv of all categories
     */

    public function categories()
    {
        $this->load->model('feed/feed');
        $categories = $this->model_feed_feed->getCategories();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=categories.csv');
            if ($categories) {
                $csv = fopen('php://output', 'w');
                fputcsv($csv, array('Categor Id', 'Name', 'Parent Id'));
                foreach ($categories as $category) {
                    if (!empty($category['sub_categories'])) {
                        foreach ($category['sub_categories'] as $sub_category) {
                            fputcsv($csv, array('Category Id' => $sub_category['sub_category_id'], 'Name' => $sub_category['sub_category_name'], 'parent_id' => $sub_category['parent_id']));
                        }
                    }
        fputcsv($csv, array('Category Id' => $category['category_id'], 'Name' => $category['name']));
                }
        fclose($csv);
            }

    }
    /**
     * @author KD
     * @return void
     * @example to create csv of all products
     */
    public function products(){

        $this->load->model('feed/feed');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $products_count = $this->model_feed_feed->getproductsCount();

        $pages = $products_count/10000;
        $mod = fmod($products_count,10000);
        if($mod > 0){
            $pages = $pages+1;
        }
        $tmp = 0;

        $file = DIR_UPLOAD . 'assets/feeds/products.csv';

        if (!file_exists(DIR_SYSTEM . 'upload/assets/feeds')) {
            mkdir(DIR_SYSTEM . 'upload/assets/feeds', 0777, true);
        }

        $csv = fopen($file, 'w');
        fputcsv($csv, array('SKU', 'Product Name', 'Category Id', 'Categories', 'Description', 'Set Description', 'Piece In Set', 'Price', 'Tax', 'weight', 'Filter', 'Quantity', 'Single', 'Images', 'Options', 'Date Added', 'Expected Dispatch Date', 'hsn_code'));
        $j = 0;
        for($i = 1; $i<= $pages; $i++){

            if( $i > 1 ){
                $lower_limit = $tmp;
            }else{
                $lower_limit = 0;
            }

            $upper_limit = 10000*$i;

            $tmp = $upper_limit;

            $filter_data = array(
                'lower_limit' => $lower_limit,
                'upper_limit' => $upper_limit
            );

            if (isset($this->request->get['catalog']) && $this->request->get['catalog'] == 1) {
                $filter_data['catalog'] = 1;
            }
            $products = $this->model_feed_feed->getproducts($filter_data);
            //echo "<pre>"; print_r($products); exit;
                if ($products) {

                    foreach ($products as $product) {
                        $j++;
                        $all_categories = $this->model_feed_feed->getAllCategoriesForProduct($product['product_id']);
                        $category_info = '';
                        $parent_category = $this->model_catalog_product->getCategory($product['product_id']);
                        $parent_category_information = $this->model_catalog_category->getCategory($parent_category);
                        if ( !empty($parent_category_information) ) {
                            $category_info .= html_entity_decode($parent_category_information['name'])." | ";
                            for ($k = 0; $k <= count($all_categories) - 1; $k++) {
                                $category_information = $this->model_catalog_category->getCategory($all_categories[$k]);
                                if (!empty($category_information) && count($category_information)>1) {
                                    if ($k ==  count($all_categories) - 1 && $category_information['name'] != $parent_category_information['name']) {
                                        $category_info .= html_entity_decode($category_information['name']);
                                    }elseif ($category_information['name'] != $parent_category_information['name']) {
                                        $category_info .= html_entity_decode($category_information['name']).", ";
                                    }
                                }
                            }
                        }

                        $categories = implode('-' , $all_categories);
                        $product_filters = $this->model_feed_feed->getProductFiltersData($product['product_id']);
                        $product_image = $this->model_catalog_product->getProductImages($product['product_id']);
                        $product_options = $this->model_feed_feed->getProductOptions($product['product_id']);

                        $filters = implode(", ", array_column($product_filters, 'filter_name'));

                        $static_content_url = STATIC_CONTENT_URL;
                        if($this->request->server['HTTPS']){
                            $static_content_url = STATIC_CONTENT_URL_SSL;
                        }

                        if(!empty($product_image)){
                            $p_image = implode(", ".$static_content_url, array_column($product_image, 'image'));
                            $product_images = ", ".$static_content_url.$p_image;
                        }else{
                            $product_images = '';
                        }

                        $product_option = implode(", ", array_column($product_options, 'name'));
                        $product_option_value = implode(", ", array_column($product_options, 'value'));

                        $description = strip_tags(html_entity_decode($product['description']));
                        $set_description = strip_tags(html_entity_decode($product['set_description']));

                        if($product['is_single'] == 1 || ($product['piece_in_set'] == 1 && $product['minimum'] == 1)){
                            $product_is_single = 1;
                        }else{
                            $product_is_single = 0;
                        }
                        // $product_price_fetcher = $this->model_catalog_product->getMinimalProductInfo($product['product_id']);
                        // $price = $this->model_catalog_product->getPrice($product_price_fetcher);
                        // $tax = $this->tax->getTax($price['unformatted_price'], $product['hsn_code']);

                        $products_status = cart::getPrice($product, $this);
                        $price  = $products_status['original_selling_price'];
                        $tax    = $products_status['original_selling_price']*$products_status['output_tax_rates']/100; 

                        fputcsv($csv, array('sku' => $product['model'], 'name' => $product['name'], 'category_id' => $categories, 'category_info' => $category_info, 'description' => $description, 'set_description' => $set_description, 'piece_in_set' => $product['piece_in_set'], 'price' => $price*$product['piece_in_set'], 'tax' => $tax*$product['piece_in_set'], 'Weight' => number_format($product['weight']*$product['piece_in_set'], 3, '.', ''), 'filter' => $filters, 'quantity' => $product['quantity'], 'single' => $product_is_single, 'images' => $static_content_url.$product['image'].$product_images, 'options' => $product_option."  ".$product_option_value, 'date_added' => $product['date_added'], 'expected_dispatch_date' => $product['expected_dispatch_date'], 'hsn_code' => $product['hsn_code']));

                    }
                }
        }

        fclose($csv);
        echo "Look for the generated CSV in system.upload/assets/feeds";
        die;
        }



    /**
     * @author Manoj Singh Rajpurohit
     * @return void
     * @example to create csv of all products with custom changes for specific customer
     */
    public function custom_feed_products(){

        ini_set('memory_limit','-1');

        $this->load->model('feed/feed');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $products_count = $this->model_feed_feed->getproductsCount();


        $pages = $products_count/10000;
        $mod = fmod($products_count,10000);
        if($mod > 0){
            $pages = $pages+1;
        }
        $tmp = 0;


        $file = DIR_UPLOAD . 'assets/feeds/custom_feed_products.csv';

        if (!file_exists(DIR_SYSTEM . 'upload/assets/feeds')) {
            mkdir(DIR_SYSTEM . 'upload/assets/feeds', 0777, true);
        }

        $csv = fopen($file, 'w');
        fputcsv($csv, array('SKU', 'Product Name', 'Category Id', 'Categories', 'Description', 'Set Description', 'Piece In Set', 'Price', 'Tax', 'weight', 'Filter', 'Quantity', 'Single', 'Images', 'Size Options', 'Color Options', 'Date Added', 'Expected Dispatch Date', 'hsn_code'));
        $j = 0;
        for($i = 1; $i<= $pages; $i++){

            if( $i > 1 ){
                $lower_limit = $tmp;
            }else{
                $lower_limit = 0;
            }

            $upper_limit = 10000*$i;

            $tmp = $upper_limit;

            $filter_data = array(
                'lower_limit' => $lower_limit,
                'upper_limit' => $upper_limit
            );

            if (isset($this->request->get['catalog']) && $this->request->get['catalog'] == 1) {
                $filter_data['catalog'] = 1;
            }
            $products = $this->model_feed_feed->getproducts($filter_data);
            //echo "<pre>"; print_r($products); exit;
            if ($products) {

                foreach ($products as $product) {
                    $j++;
                    $all_categories = $this->model_feed_feed->getAllCategoriesForProduct($product['product_id']);
                    $category_info = '';
                    $parent_category = $this->model_catalog_product->getCategory($product['product_id']);
                    $parent_category_information = $this->model_catalog_category->getCategory($parent_category);
                    if ( !empty($parent_category_information) ) {
                        $category_info .= html_entity_decode($parent_category_information['name'])." | ";
                        for ($k = 0; $k <= count($all_categories) - 1; $k++) {
                            $category_information = $this->model_catalog_category->getCategory($all_categories[$k]);
                            if (!empty($category_information) && count($category_information)>1) {
                                if ($k ==  count($all_categories) - 1 && $category_information['name'] != $parent_category_information['name']) {
                                    $category_info .= html_entity_decode($category_information['name']);
                                }elseif ($category_information['name'] != $parent_category_information['name']) {
                                    $category_info .= html_entity_decode($category_information['name']).", ";
                                }
                            }
                        }
                    }

                    $categories = implode('-' , $all_categories);
                    $product_filters = $this->model_feed_feed->getProductFiltersData($product['product_id']);
                    $product_image = $this->model_catalog_product->getProductImages($product['product_id']);
                    $product_options = $this->model_feed_feed->getProductOptions($product['product_id']);

                    $filters = implode(", ", array_column($product_filters, 'filter_name'));

                    $static_content_url = "http://d36qiqd7gl7e25.cloudfront.net/img/dw=350,dh=350,q=100/";

                    if(!empty($product_image)){
                        $p_image = implode(", ".$static_content_url, array_column($product_image, 'image'));
                        $product_images = ", ".$static_content_url.$p_image;
                    }else{
                        $product_images = '';
                    }

                    $color_options = "";
                    $size_options = "";
                    $product_option = implode(", ", array_column($product_options, 'name'));
                    $product_option_value = implode(", ", array_column($product_options, 'value'));

                    if($product_option == "Size"){
                        $size_options = $product_option."  ".$product_option_value;
                    }

                    if($product_option == "Color"){
                        $color_options = $product_option."  ".$product_option_value;
                    }

                    $description = strip_tags(html_entity_decode($product['description']));
                    $set_description = strip_tags(html_entity_decode($product['set_description']));

                    if($product['is_single'] == 1 || ($product['piece_in_set'] == 1 && $product['minimum'] == 1)){
                        $product_is_single = 1;
                    }else{
                        $product_is_single = 0;
                    }
                    // $product_price_fetcher = $this->model_catalog_product->getMinimalProductInfo($product['product_id']);
                    // $price = $this->model_catalog_product->getPrice($product_price_fetcher);
                    // $tax = $this->tax->getTax($price['unformatted_price'], $product['hsn_code']);

                    $products_status = cart::getPrice($product, $this);
                    $price  = $products_status['original_selling_price'];
                    $tax    = $products_status['original_selling_price']*$products_status['output_tax_rates']/100;

                    if(!empty($description)) {

                        fputcsv($csv, array('sku' => $product['model'],
                            'name' => $product['name'],
                            'category_id' => $categories,
                            'category_info' => $category_info,
                            'description' => $description,
                            'set_description' => $set_description,
                            'piece_in_set' => $product['piece_in_set'],
                            'price' => $price*$product['piece_in_set'],
                            'tax' => $tax*$product['piece_in_set'],
                            'Weight' => number_format($product['weight']*$product['piece_in_set'], 3, '.', ''),
                            'filter' => $filters,
                            'quantity' => $product['quantity'],
                            'single' => $product_is_single,
                            'images' => $static_content_url.$product['image'].$product_images,
                            'size_options' => $size_options,
                            'color_options' => $color_options,
                            'date_added' => $product['date_added'],
                            'expected_dispatch_date' => $product['expected_dispatch_date'],
                            'hsn_code' => $product['hsn_code']));
                    }


                }
            }
        }

        fclose($csv);
        echo "Look for the generated CSV in system.upload/assets/feeds";
        die;
    }


    /**
     *
     */
        public function checkStock()
        {
            if (in_array($this->request->get['token'], $this->tokens)) {

                //Write action to txt log

                $log  = "Token - ".$this->request->get['token']. ' IP - ' .$this->request->getIpAddress.' Date - '.date("F j, Y, g:i a").PHP_EOL;

                file_put_contents(DIR_SYSTEM . 'logs/check_stock_log.txt', $log, FILE_APPEND);

                if (isset($_REQUEST['model']) && $_REQUEST['model'] != '') {
                    $this->load->model('feed/feed');
                    $quantity = $this->model_feed_feed->checkStock($_REQUEST['model']);
                    if ($quantity < 1) {
                        $stock_status = 'Out of stock';
                    } else {
                        $stock_status = 'In stock';
                    }
                    $response = array('status' => $stock_status, 'quantity' => $quantity);
                } else {
                    $response = array('status' => 'Product not found', 'quantity' => 0);
                }

                echo json_encode($response);
            }else{
                echo "You are not authorized to use this function."; die;
            }
        }

    /**
     * @author KD
     * @return void
     * @example to create csv of all products in a new format
     */

    public function viaProductsCsv(){

        $static_content_url = STATIC_CONTENT_URL;
        if($this->request->server['HTTPS']){
            $static_content_url = STATIC_CONTENT_URL_SSL;
        }

        $file = DIR_UPLOAD . 'assets/feeds/products.csv';

        if (!file_exists(DIR_SYSTEM . 'upload/assets/feeds')) {
            mkdir(DIR_SYSTEM . 'upload/assets/feeds', 0777, true);
        }

        $this->load->model('feed/feed');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $products = $this->model_feed_feed->getproducts();

        if ($products) {
            $csv = fopen($file, 'w');
            fputcsv($csv, array('SKU', 'Brand', 'Category', 'Item Name', 'Item Model', 'Item Description', 'Set Description', 'Item Color', 'Item Image Url', 'MRP', 'Price'));
            foreach ($products as $product) {
                $product_images = $product['image'];
                $description = strip_tags(html_entity_decode($product['description']));
                $set_description = strip_tags(html_entity_decode($product['set_description']));
                $category = $this->model_catalog_product->getCategory($product['product_id']);
                $category_info = $this->model_catalog_category->getCategory($category);
                fputcsv($csv, array('sku' => $product['sku'], 'brand' => '', 'category' => $category_info['name'], 'name' => $product['name'], 'model' => $product['model'], 'description' => $description, 'set_description' => $set_description, 'options' => '', 'images' => $static_content_url . $product_images, 'mrp' => '', 'price' => $product['price']));
            }

            fclose($csv);

        }
    }

    /**
     * @author KD
     * @return void
     * @example to create csv of ALL product filters
     */

    public function viaProductAttributesCsv(){

        $file = DIR_UPLOAD . 'assets/feeds/products_attributes.csv';

        if (!file_exists(DIR_SYSTEM . 'upload/assets/feeds')) {
            mkdir(DIR_SYSTEM . 'upload/assets/feeds', 0777, true);
        }

        $this->load->model('feed/feed');
        $this->load->model('catalog/product');
        $products = $this->model_feed_feed->getproducts();

        if ($products) {
            $csv = fopen($file, 'w');
            fputcsv($csv, array('SKU', 'Spec Group', 'Spec key', 'Spec Value'));
            foreach ($products as $product) {
                $product_filters = $this->model_feed_feed->getProductFiltersData($product['product_id']);
                foreach ($product_filters as $filter){
                    fputcsv($csv, array('sku' => $product['sku'], 'spec_group' => '', 'group_name' => $filter['group_name'], 'filter_value' => $filter['filter_name']));
                }
            }

            fclose($csv);

        }

    }

    }
