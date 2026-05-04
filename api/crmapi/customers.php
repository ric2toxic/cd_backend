<?php

 // Configuration
if (is_file(__DIR__.'/../../config.php')) {
    require_once(__DIR__.'/../../config.php');
}

require_once DIR_API . "/../system.php";
require_once DIR_API . "/../data_packet.php";
require_once(DIR_SYSTEM . 'library/solr/product.php');
require_once( DIR_SYSTEM . 'library/currency.php' );
require_once( DIR_SYSTEM . 'library/customer.php' );
require_once( DIR_SYSTEM . 'library/tax.php' );
require_once( DIR_SYSTEM . 'library/cart.php' );
require_once DIR_SYSTEM . 'library/solr/model_solr_product.php';

/**
 * Customer controller
 * use for get customer data to crm 
 */
class CustomersController extends SystemController {
    
    /**
     * Contain total product count for lead
     * @var int
     */
    private $total_count = 0;
    private $total_requested_count = 15;
    private $catalog_limit = 3;
   
    
    private $catalog_categorys = array('125'=>'Kurti Catalog','70'=>'Suit Catalog','124'=>'Saree Catalog');
    private $catalog_category_ids = array();
    
    private $normal_products = array();
    private $catalog_products = array();
    
    
    public function __construct($params) {
        
        $this->catalog_category_ids = array_keys($this->catalog_categorys);
        
        parent::__construct($params);
        // Currency
        $this->registry->set('currency', new Currency($this->registry));
        // Customer
        $this->registry->set('customer', new Customer($this->registry));
        // Tax
        $this->registry->set('tax', new Tax($this->registry));
        // Cart
        $this->registry->set('cart', new Cart($this->registry));
    }

    public function getCustomerPreferencesInfo($customer = array()) {

        

        if (isset($this->args)) {
            $customer = $this->args['customer_list'];
            $category_ids = $this->args['category_ids'];
            $price_filter = $this->args['price_filter'];
            $seller_special_price = $this->args['seller_special_price']??'';
        } else {
            $category_ids = $customer['category_ids'];
            $customer = $customer['customer_list'];
            $price_filter = $customer['price_filter'];
            $seller_special_price = $this->args['seller_special_price']??'';
        }
        
        
        if (!empty($seller_special_price)) {            
           $data =  $this->getSpecialPriceProducts();
           return $data; exit;
        }


        $min_price = $price_filter['min'] ?? 0;
        $max_price = $price_filter['max'] ?? 100000000;
            
        $product_details = array();
        $products_data = array();
        $products_data['total_products'] = 0;
        
        if (isset($customer) && !empty($customer)) {


            $this->load->model('restapi/service');

            $customers = array();
            $data = array();
            $data['customers'] = array();
            
            foreach($customer as $cus_id){
                $data['customers'][$cus_id] = $category_ids;
            }
            
            $data['request_for'] = 'crm';            
            $data['price_filter'] = $min_price .'-' . $max_price;
            
            // $data['customers'] = $customers[$customer] = $category_ids;
            
            $data['limit'] = $this->total_requested_count;
            
            $products_data = $this->model_restapi_service->getProductsByCustomerPreferences($data);
            
            
        }
        
        if (isset($products_data['total_products']) && ($products_data['total_products']) == 0) {

            $data = array();
            $data['filter_category_id'] = implode(',', $category_ids);
            // $data['days'] = 20;
            $data['start'] = 0;
            $data['price_filter'] = $min_price .'-' . $max_price;
            $data['limit'] = $this->total_requested_count;
            $data['sort_data_by'] = 'date_added';
            $data['order_data_by'] = 'desc';
            $solr = new SolrProduct($this);

            $products = $this->getProductFromSolar($solr, $data);
            $product_ids = $this->getProductsData($products);
            $products_data = $this->getProductsInfo($product_ids);
         
        }
        
        if(empty($products_data)){
            $data = array();
            $data['products'] = array();
            return $data;
            exit;
        }
        
        $catalog_products = $this->seprateCatalogProducts($products_data);
        
        $data = array();
        $data['normal_products'] = $this->normal_products;
        $data['catalog_products'] = $this->catalog_products;
        return $data;
        exit;
    }

    public function getSpecialPriceProducts() {

        $this->total_requested_count = 1000;
        $data = array();

        $data['start'] = 0;
        $data['limit'] = $this->total_requested_count;
        $data['filter_tag'] = WSB_PURCHASE_INVENTORY_SEARCH_TERM;
        $data['filter_special'] = 1;

        $data['sort_data_by'] = 'date_added';
        $data['order_data_by'] = 'desc';
        $solr = new SolrProduct($this);

        $solr_products = $solr->getProductFromSolrOnly($data);

        $product_ids = array();
        if ($solr_products['solr_result']->getNumFound() > 0) {
            foreach ($solr_products['solr_result'] as $document) {
                $product_ids[] = $document->id;
            }
        }
        
        $products_data = array();
        if (!empty($product_ids)) {
            $products_data = $this->getProductsInfo($product_ids);
        }

     
        $data = array();
        $data['status'] = 1;
        $data['products'] = $products_data;
        return $data;
        exit;
    }

    public function getProductsData($products) {

        if (empty($products)) {
            return array();
        }
        $all_products = $products;
        $product_ids = array();
        if ((int) $this->total_count == 0) {
            return $product_ids;
        } else if ((int) $this->total_count <= $this->total_requested_count) {

            foreach ($products as $prod) {
                foreach ($prod['products'] as $product_id) {
                    $product_ids[] = $product_id;
                }
            }

            return $product_ids;
        } else {
            $count = floor($this->total_requested_count / count($products));

            $count = $count > 0 ? $count : 1;


            goto GET_PRODUCTS;

            GET_PRODUCTS:
            foreach ($products as $prod) {
                if (count($product_ids) == $total_products_req) {
                    break;
                }
                $product_count = 0;

                foreach ($prod['products'] as $key => $document) {
                    if ($product_count == $count) {
                        if ($prod['products_count'] > $count) {
                            $products2[] = $prod;
                        }
                        break;
                    }
                    if (!in_array($document, $product_ids)) {
                        $product_ids[] = $document;
                        $product_count ++;
                    }
                    unset($prod['products'][$key]);
                    if (count($product_ids) == $this->total_requested_count) {
                        break;
                    }
                }
            }
            if (count($product_ids) < $this->total_requested_count && count($products2) > 0) {
                $products = $products2;
                $products2 = array();
                $count = floor(($this->total_requested_count - count($product_ids)) / count($products));
                $count = $count > 0 ? $count : 1;
                goto GET_PRODUCTS;
            }

            return $product_ids;
        }
    }

    public function getLatestSellerProductsInfo($category_seller = array(), $category_ids = array()) {

        $total_products_req = 30;
        $solr = new SolrProduct($this);
        $total_count = 0;

        $products = array();

        foreach ((array) $category_seller as $category => $seller_list) {

            $data['filter_category_id'] = $category;
            $sellers = (array) $seller_list;


            if (!empty($sellers['sellers'])) {
                foreach ($sellers['sellers'] as $seller_id => $price_info) {
                    $price_info = (array) $price_info;

                    $data['filter_seller_id'] = $seller_id;
                    $min_price = 0.8 * ($price_info['min_price']);
                    $max_price = 1.2 * ($price_info['max_price']);
                    $data['price_filter'] = $min_price . '-' . $max_price;
                    $data['days'] = 20;
                    $data['start'] = 0;
                    $data['limit'] = $total_products_req;
                    $data['sort_data_by'] = 'date_added';
                    $data['order_data_by'] = 'desc';

                    $result = $this->getProductFromSolar($solr, $data, $total_count);

                    $products = array_merge($products, $result);
                }
            }
        }

        if (empty($products)) {

            $data = array();
            $data['filter_category_id'] = implode(',', $category_ids);
            $data['days'] = 20;
            $data['start'] = 0;
            $data['limit'] = $total_products_req;
            $data['sort_data_by'] = 'date_added';
            $data['order_data_by'] = 'desc';

            $products = $this->getProductFromSolar($solr, $data, $total_count);
        }



        if (empty($products)) {
            return array();
        }
        $all_products = $products;
        $product_ids = array();
        if ((int) $total_count == 0) {
            return $product_ids;
        } else if ((int) $total_count <= $total_products_req) {


            foreach ($products as $prod) {
                foreach ($prod['products'] as $product_id) {
                    $product_ids[] = $product_id;
                }
            }

            return $product_ids;
        } else {
            $count = floor($total_products_req / count($products));

            $count = $count > 0 ? $count : 1;


            goto GET_PRODUCTS;

            GET_PRODUCTS:
            foreach ($products as $prod) {
                if (count($product_ids) == $total_products_req) {
                    break;
                }
                $product_count = 0;

                foreach ($prod['products'] as $key => $document) {
                    if ($product_count == $count) {
                        if ($prod['products_count'] > $count) {
                            $products2[] = $prod;
                        }
                        break;
                    }
                    if (!in_array($document, $product_ids)) {
                        $product_ids[] = $document;
                        $product_count ++;
                    }
                    unset($prod['products'][$key]);
                    if (count($product_ids) == $total_products_req) {
                        break;
                    }
                }
            }
            if (count($product_ids) < $total_products_req && count($products2) > 0) {
                $products = $products2;
                $products2 = array();
                $count = floor(($total_products_req - count($product_ids)) / count($products));
                $count = $count > 0 ? $count : 1;
                goto GET_PRODUCTS;
            }

            return $product_ids;
        }
    }

    public function getProductFromSolar($solr_object, $data) {

        $solr_products = $solr_object->getProductFromSolrOnly($data);

        $products = array();
        if ($solr_products['solr_result']->getNumFound() > 0) {

            $seller_products_ids = array();
            $seller_products['seller_id'] = $seller_id??'';
            $seller_products['products_count'] = count($solr_products['solr_result']);
            foreach ($solr_products['solr_result'] as $document) {
                $seller_products_ids[] = $document->id;
            }

            $seller_products['products'] = $seller_products_ids;

            $products[] = $seller_products;
            $this->total_count = $this->total_count + $seller_products['products_count'];
        }

        return $products;
    }

    public function getProductsInfo($get_Products) {

        $all_products_info = array();

        $this->load->model('catalog/product');

        foreach ($get_Products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);
            if (!empty($product_info)) {
                $all_products_info[$product_id] = $product_info;
            }
        }
        return $all_products_info;
    }
    
    public function seprateCatalogProducts($products_data) {

        $this->load->model('catalog/product');
        $catalog_products = array();
        $i = 0;
        foreach ($products_data as $key => $prod_data) {

            if (isset($prod_data['products'])) {
                $customer_count = 0;
                $product_count = 0;
                foreach ($prod_data['products'] as $val) {

                    $category_id = $val['category_id'];
                    $product_id = $val['product_id'];

                    if (in_array($category_id, $this->catalog_category_ids)) {
                        if ($i == $this->catalog_limit) {
                            break;
                        }
                        $this->catalog_products[$product_count] = $val;
                        $this->catalog_products[$product_count]['catalog_images'] = $this->model_catalog_product->getProductImages($product_id);
                   
                        $i++;
                    }else{
                        $this->normal_products[$product_count] = $val;
                    }
                    $product_count++;
                }

                if ($product_count >= 15) {
                    break;
                }
            } else {

                $category_id = $prod_data['category_id'];
                $product_id = $prod_data['product_id'];

                if (in_array($category_id, $this->catalog_category_ids)) {
                    if ($i == $this->catalog_limit) {
                        break;
                    }
                    $this->catalog_products[$key] = $prod_data;
                    $this->catalog_products[$key]['catalog_images'] = $this->model_catalog_product->getProductImages($product_id);
                    $i++;
                }else{
                     $this->normal_products[$key] = $prod_data;
                }
            }
        }
    }

    /**
     * getCustomersPreferencesInWebsite
     * @author Vishnu Shekhawat
     * @description return customer preferences in website for view in crm
     * @return
     * if preference found for customer  
      array(
        overall_min_price=>0,
     
        overall_max_price=>0,
      
        preferences=>array(
        
            category_id=>array(

                min_price=>0,
                max_price=>0,
                name=>category_name
            )
        )
      )
      else
      array(
            status=>0,
            statusText=>Error,
            message=>error_message
      )
     * 
     */
    public function getCustomersPreferencesInWebsite(){
        
        $customer_preferences = array();
        $response = array();
        
        if (isset($this->args['customer_ids'])) {
            $customer = $this->args['customer_ids'];
        }


        if (empty($customer)) {
            $response['status'] = 0;
            $response['statusText'] = 'Error';
            $response['message'] = 'Customer list is empty';
            return $response;
            exit;
        }
        
        $this->load->model('preferences');
      
        $this->load->model('catalog/category');
        
        
        $category_seller = $this->model_preferences->getCustomerAlgoPreferencesCategorySellerwise($customer);
        
        if (empty($category_seller)) {
            $response['status'] = 0;
            $response['statusText'] = 'Error';
            $response['message'] = 'Preferences are not set for this customer.';
            return $response;
            exit;
        }

        $category_ids = array_keys($category_seller);
        $category_ids_name = $this->model_catalog_category->getCategoriesNameFromCategoryIds($category_ids);
        
        $category_preferences = array();
        
        $overall_min_price = 0;
        $overall_max_price = 0;
        
        foreach ($category_seller as $category_id => $value) {
            $category_name = isset($category_ids_name[$category_id]) ? $category_ids_name[$category_id] : '';
            $category_preferences[$category_id]['name'] = $category_name;
            
            $min_price = 0;
            $max_price = 0;

            foreach ($value['sellers'] as $seller_id => $price_range) {
                $cat_min_price = $price_range['min_price'];
                $cat_max_price = $price_range['max_price'];
                if ($min_price == 0) {
                    $min_price = $cat_min_price;
                }
              
                if ($overall_min_price == 0) {
                    $overall_min_price = $cat_min_price;
                }
                
                if ($cat_min_price < $min_price) {
                    $min_price = $cat_min_price;
                }

                if ($cat_max_price > $max_price) {
                    $max_price = $cat_max_price;
                }
                
                if ($cat_min_price < $overall_min_price) {
                    $overall_min_price = $cat_min_price;
                }

                if ($cat_max_price > $overall_max_price) {
                    $overall_max_price = $cat_max_price;
                }
            }
            $category_preferences[$category_id]['min_price'] = $min_price;
            $category_preferences[$category_id]['max_price'] = $max_price;
          
        }
        
        
        if (empty($category_preferences)) {
            $response['status'] = 0;
            $response['statusText'] = 'Error';
            $response['message'] = 'Preferences are not set for this customer.';
            return $response;
            exit;
        }
        
        $response['overall_min_price'] = $overall_min_price;
        $response['overall_max_price'] = $overall_max_price;
            
        $response['status'] = 1;
        $response['statusText'] = 'Success';
        $response['preferences'] = $category_preferences;
        return $response;
        exit;
        
    }
    
    
    /**
     * setCustomerPreferencesFromCRM
     * @author Vishnu Shekhawat
     * @description set customer preferences in website when a agent update lead preferences in crm
         $data: array(
                'customer_id' => array(
                    'category_id' => array(
                        'min_price' => '50.0',
                        'max_price' => '100.0'
                   )
                ),

                '44' => array(
                    '61' => array(
                        'min_price' => '500.0',
                        'max_price' => '1000.0'
                    )
                 ,
                '62'
                )
            );
     * 
     * @return string
     */
    public function setCustomerPreferencesFromCRM() {

        $data = array();
        $response = array();

        if (isset($this->args['data'])) {
            $data = $this->args['data'];
        }


        if (empty($data)) {
            $response['status'] = 0;
            $response['statusText'] = 'Error';
            $response['message'] = 'data is empty';
            return $response;
            exit;
        }

        $this->load->model('preferences');
        $this->model_preferences->setCustomerPreferencesFromCRM($data);

        $response['status'] = 1;
        $response['statusText'] = 'Success';
        $response['message'] = 'Preferences set successfully.';
        return $response;
        exit;
    }

}

?>
