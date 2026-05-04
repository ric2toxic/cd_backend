<?php
/**
 * Created by PhpStorm.
 * User: Rakesh
 * Date: 8/5/2016
 * Time: 5:06 PM
 */

require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/customer_activity_log.php');

class SolrProduct {

    protected $registry;
    protected $show_out_of_stock;
    protected $_final_data_fetch;
    protected $_franchise_category_id;
    protected static $wsb_purchased_inventory_regex = '/WSB([JP|ST|MU|BL|KL|DL]{2})S([A-Za-z0-9_-]+)/';
    protected static $solr_stock_status_query = 'status:1 AND quantity:[1 TO *] AND -stock_status_id:5 AND seller_status:1 AND vacation_mode:0 AND price:[1 TO *] AND selling_price:[1 TO *] AND is_archived:0 AND hsn_code:([* TO *] AND -"") AND (exclusive:normal OR exclusive:both)';
    protected static $wpi_search_keyword = 'WSBJPS';
    protected static $solr_object = array(
                                            'endpoint' => array(
                                                    'localhost' => array(
                                                            'host' => SOLR_HOST,
                                                            'port' => SOLR_PORT,
                                                            'path' => SOLR_PATH,
                                                            'timeout' => 50000
                                                    )
                                            )
                                    );
    protected $fashcart_upcoming_products_days = 15;
    protected $product_count = 0;
    public function __construct($registry) {
        $this->registry = $registry;
        $this->show_out_of_stock = 0;
        $this->_final_data_fetch = false;
        $this->_franchise_category_id = 700000;
    }
    private $_request_data;

    private $_ratings_label = array( 
                                '3.0' => 'Average',
                                '4.0' => 'Good',
                                '5.0' => 'Excellent' 
                              );

    private $_categorywise_all_page_filters = array();

    protected $sor_filter_details = array(
                                        'filter_group_id' => '50000',
                                        'group_label' => 'Buyback',
                                        'group_description' => '',
                                        'filter' => array(
                                                        array('name' => 'Buyback Only', 'image' => '', 'filter_id' => '50002', 'product_count' => '0' )
                                                    )
                                    );
    protected $exclusive_filter_details = array(
                                            'filter_group_id' => '60000',
                                            'group_label' => 'Exclusive',
                                            'group_description' => '',
                                            'filter' => array(
                                                            array('name' => 'Exclusive Only', 'image' => '', 'filter_id' => '60002', 'product_count' => '0' )
                                                        )
                                        );
    protected $sor_filter_id = '50002';
    protected $exclusive_filter_id = '60002';

    /*
    * getProductFromSolr: To get products for Latest/Trending/Backend and Search/Categorywise(Personalized)
    * Author: Anurag Jain
    * Date: 15 Nov 2017
    */

    public function getProductFromSolr($data, $backend = 0) {

        // if( (int)$this->registry->customer->getTelephone() == 9958198866 && $backend == 0) {
        //   $data['csv_req'] = 1;
        // }
        

        /** search term split with '@@'
         * for now doing for 'wpi007' search term and clearence sale 
        **/
        if ( !empty( $data['filter_tag'] ) || !empty( $data['filter_name'] )) {

            $split_keyword = $data['filter_tag'] ?? $data['filter_name'];

            if ( !empty( $split_keyword )) {

                $split_keyword_array = explode( '@@', $split_keyword );

                if ( count( $split_keyword_array ) > 1 ) {

                    if ( isset( $data['filter_tag'] )) {
                        $data['filter_tag'] = $split_keyword_array[0] ?? '';
                    }

                    if ( isset( $data['filter_name'] )) {
                        $data['filter_name'] = $split_keyword_array[0] ?? '';
                    }

                    unset( $split_keyword_array[0] );

                    foreach ( $split_keyword_array as $key => $value ) {

                        $other_keys = explode( '=', $value );

                        if ( !empty( $other_keys[0] )) {

                            if ( $other_keys[0] == 'clearance_sale' ) {
                                $data['filter_special'] = 1;//(int) $other_keys[1];
                            }
                        }
                    }
                }
            }
        }

        $this->_request_data = $data;

        $call_from = '';
        if(!empty($data['call_from'])) {
          $call_from = $data['call_from'];
        }
        
        if(!empty($data['shuffle'])) {
            $shuffle = true;
        }

        $filter_retain = 0;
        if(!empty($data['filter_retain']) && $data['filter_retain'] == 1) {
            $filter_retain = 1;
        }

        $csv_req = 0;

        if(!empty($data['csv_req'])) {
            $csv_req = 1;
        }

        //*** Top priority products ***//
        $top_priority_products = array();
        $priority_fixed_position_count = 1;

        /**Sample**/
        //$top_priority_products['61'] = "76950,78592";

        if(!$backend && !empty(AAKARA_PRODUCT_IDS) && (empty($data['filter_tag']) && empty($data['filter_name'])) && empty($data['filter_franchise_tab']) && !(isset($data['filter_category_id']) && $data['filter_category_id'] == $this->_franchise_category_id)) {
          $top_priority_products = AAKARA_PRODUCT_IDS;
        }
        if(!empty($top_priority_products)) {
            $filter_category_ids = !empty($data['filter_category_id'])?explode(',', $data['filter_category_id']):array();
            foreach ($filter_category_ids as $key => $cat_id) {
                if(array_key_exists($cat_id, $top_priority_products)){
                    $final_top_priority_products_arr[] = $top_priority_products[$cat_id];
                }
            }
            if(!empty($final_top_priority_products_arr)) {
                $final_top_priority_products = implode(',', $final_top_priority_products_arr);
            }
            $final_top_priority_products_temp_arr = array();
            if(!empty($final_top_priority_products)) {
              $final_top_priority_products_temp = array_unique(explode(',',$final_top_priority_products));
              $top_priority_random = array_rand($final_top_priority_products_temp,$priority_fixed_position_count);
              if(is_array($top_priority_random)) {
                foreach ($top_priority_random as $key => $value) {
                  $final_top_priority_products_temp_arr[] = $final_top_priority_products_temp[$value];
                }
              } else {
                $final_top_priority_products_temp_arr[] = $final_top_priority_products_temp[$top_priority_random];
              }
            }
            $final_top_priority_products = implode(',', $final_top_priority_products_temp_arr);
        }

        if($backend == 0) {
            $this->registry->load->model('preferences');
            // if(isset($data['user_id']) && $data['user_id'] > 0) {
            //     // Check pref data exits in db
            //     $pref_result = $this->registry->model_preferences->checkPreferenceExistenceBySource( (int) $data['user_id'], 'algo' );
            //     if(!($pref_result)) {
            //         $this->registry->model_preferences->updateCustomerPreferences( 'reset', (string) $data['user_id'] );
            //     }
            // }
        }

        if (!empty($data['price_filter'])){
            $price_filter_arr = array_values(array_filter((explode('-', $data['price_filter']))));
            if(count($price_filter_arr) == 2) {
                $data['price_filter'] = $price_filter_arr[0].'-'.$price_filter_arr[1];
            } else if(count($price_filter_arr) == 1) {
                $data['price_filter'] = '1-'.$price_filter_arr[0];
            }
        }

        if(isset($data['sort']) && $data['sort'] == 'undefined'){
            $data['sort'] = 'sort_order';
        }
        if(isset($data['rating_filter']) && ( $data['rating_filter'] == 'nothing' || $data['rating_filter'] == 'undefined' )) {
            $data['rating_filter'] = '3';
        }

        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
        } else {
            $user_id = 0;
        }

        /************************** Create solr Common Query ***************************/
        $sql_facet = $sql = array();

        if( $backend == 1 ) {
            $data['status']     = 0;
            $data['quantity']   = 0;
            $data['is_single']  = 0;
            $data['wsb_store']  = 0;
            $data['backend']    = 0;
            if(!empty($data['sort'])) {
                $sort = explode(".", $data['sort']);
                $data['sort'] = $sort[1];
            }

            //*** franchise filter
            if(!empty($data['filter_franchise_tab'])) {
                $franchise_sql = '(franchise_id:[1 TO *])';
                if(isset($data['franchise_id']) && $data['franchise_id'] != '' && (int) $data['franchise_id'] != 0) {
                    $franchise_sql = '(franchise_id:('.$data['franchise_id'].'))';
                }
                $sql[] = $franchise_sql;
            } else {
                $franchise_sql = '(franchise_id:0 OR (*:* AND -franchise_id:[* TO *]))';
                $sql[] = $franchise_sql;
            }

            // associate product filter for Backend
            if (!empty($data['is_associate'])) {
              $sql_facet[] = $sql[] = 'is_associate:' . $data['is_associate'];
            }

            if (!empty($data['filter_price_from']) && !empty($data['filter_price_to'])) {
                $sql_facet[] = $sql[] = 'price:['.$data['filter_price_from'] .' TO '. $data['filter_price_to'].' ]';
            }

        } else {
            //*** franchise filter for frontend
            $franchise_sql = '(franchise_id:0 OR (*:* AND -franchise_id:[* TO *]))';
            if(isset($data['franchise_id']) && $data['franchise_id'] != '' && (int) $data['franchise_id'] != 0) {
                $franchise_sql = '(franchise_id:(0 OR '.$data['franchise_id'].') OR (*:* AND -franchise_id:[* TO *]))';
                if(!empty($data['filter_franchise_tab']) || $data['filter_category_id'] == $this->_franchise_category_id) {
                  $franchise_sql = '(franchise_id:('.$data['franchise_id'].'))';
                }
                if($data['filter_category_id'] == $this->_franchise_category_id) {
                  $data['filter_category_id'] = '';
                }
            }
            $sql_facet[] = $sql[] = $franchise_sql;

            // associate product filter for frontend i.e. associate products will never appear in frontend
            $sql_facet[] = $sql[] = '(is_associate:0 OR (*:* AND -is_associate:[* TO *]))';
            
            if (!empty($data["date_added_less_than"])) {
              $sql_facet[] = $sql[] = 'date_added:[* TO '. $data["date_added_less_than"] .']';
            }
            
            if (empty($data['store_code']) && !empty($data['store_product'])) {
              $sql_facet[] = $sql[] = '-store_sales:NO';
            }
        }

        //*** Product status
        if (!isset($data['status'])) {
            $sql_facet[] = $sql[] = 'status:1';
        }

        //*** Product out of stock status id
        $out_of_stock_id = 5;
        //$in_stock_id = 7;

        if (isset($data['show_out_of_stock'])) {
            $this->show_out_of_stock = $data['show_out_of_stock'];
        } else {
            $this->show_out_of_stock = 0;
        }

        //*** is_single

        if(empty($data['search_all_products']) && !isset($data['is_single'])) {
            if (!empty($data['custom_store']) && $data['custom_store'] == 'single') { // Show singles store items only
                $sql_facet[] = $sql[] = "(is_single:1 OR (piece_in_set:1 AND minimum:1))";
            }else{
                $sql_facet[] = $sql[] = "is_single:0";
            }
        }
        
        if ( isset($data['location']) && !empty((trim($data['location'])))) {
            $sql_facet[] = $sql[] = "city:(".strtoupper($data['location'])." OR ".strtolower($data['location'])." OR ".ucfirst(strtolower($data['location'])).")";
        }
        
        if ( isset($data['filter_seller_sku']) && !empty((trim($data['filter_seller_sku'])))) {
            $sql_facet[] = $sql[] = 'searchable:'. trim('*'.$data['filter_seller_sku'].'*');
        }

        if ( isset($data['filter_seller_id']) && !empty($data['filter_seller_id'])) {
            $seller_id = explode(',', $data['filter_seller_id']);
            foreach ($seller_id as $key => $value) {
                $seller[] = 'seller_id:'.$value;
            }
            $sql_facet[] = $sql[] = '('.implode(" OR ", $seller).')';
        }

        if ( isset($data['filter_special']) && !empty((trim($data['filter_special'])))) { // get products with special price
            $sql_facet[] = $sql[] = 'date_start:[* TO NOW] AND date_end:[NOW TO *]';
            $sql_facet[] = $sql[] = 'special_price:[1 TO *]';
        }

        // If we don't want spacific id's
        if( !empty($data['not_product_ids']) ) {
            $arr_product_ids = array();
            $arr_product_ids = explode("," , $data['not_product_ids']);
            $arr_product_ids = array_filter($arr_product_ids);
            $sql_facet[] = $sql[] = '-( id:'. implode(' OR id:', $arr_product_ids ).' )';
        }

        if(!empty($data['hsn_code'])){
            $sql_facet[] = $sql[] = "hsn_code:".$data['hsn_code'];
        }

        //** Category filters
        $category = array();

        if(!empty($call_from) && $call_from == 'customer_preference') {
            if(empty($data['filter_category_id'])) {

                $source = '';
                $request_for = $data['request_for'] ?? '';
                
                if(empty($request_for)) {
                    $source = 'cust'; // this is a call for fashcart (showing only customer app selected categories)
                }
                
                $customer_cat_preference_data_array = $this->registry->model_preferences->getCustomerPreferenceCategories(array($user_id), $source);

                if( empty($customer_cat_preference_data_array) && empty($request_for) ) { // request for fashcart; get ordered pref categories
                    $pref_type = 'order';
                    $source = '';
                    $customer_cat_preference_data_array = $this->registry->model_preferences->getCustomerPreferenceCategories(array($user_id), $source, $pref_type);
                }

                if(!empty($customer_cat_preference_data_array[$user_id])) {
                    $data['filter_category_id'] = $customer_cat_preference_data_array[$user_id];
                }
            }
            /*** remove top most parent category ids ***/
            if(!empty($data['filter_category_id'])) {
                $data['filter_category_id'] = $this->removeTopMostParentCategories($data['filter_category_id']);
            }
        }

        /*** remove non numeric and zero ***/
        if(!empty($data['filter_category_id']) || (isset($data['filter_category_id']) && ($data['filter_category_id'] == '0'))) {
            $category_ids_temp = explode(',', $data['filter_category_id']);
            foreach ($category_ids_temp as $key => $value) {
                if(!is_numeric($value) || empty($value)) {
                    unset($category_ids_temp[$key]);
                }
            }
            $category_ids_temp = array_unique($category_ids_temp);
            $data['filter_category_id'] = implode(',', $category_ids_temp);
        }
        
        if ( isset($data['filter_category_id']) && !empty($data['filter_category_id'])) { // get products form particular categories only
            $category_id = explode(',', $data['filter_category_id']);
            foreach ($category_id as $key => $value) {
                if(is_numeric($value)) {
                    $category[] = $value;
                }
            }

            $child_category = array();
            $sub_child_category = array();

            foreach ($category as $key => $cat_id) {
              $child_category[$cat_id] = $this->getAllChildCategories($cat_id);
            }

            if(!empty($child_category)) {
              $child_category = implode(',',$child_category);
              $child_category = explode(',',$child_category);
              $child_category = array_filter($child_category);

              foreach ($child_category as $key => $cat_id) {
                $sub_child_category[$cat_id] = $this->getAllChildCategories($cat_id);
              }
              if(!empty($sub_child_category)) {
                $sub_child_category = implode(',',$sub_child_category);
                $sub_child_category = explode(',',$sub_child_category);
                $sub_child_category = array_filter($sub_child_category);
              }
            }
            $category = array_unique(array_filter(array_merge($category, $child_category, $sub_child_category)));

            //** Static filters and category **//
            if(!empty($data['call_from']) && $data['call_from'] == 'app' && (in_array('73', $category_id) || in_array('90', $category_id))) {

                if(in_array('73', $category_id) && $data['call_from'] == 'app') {
                    $sql_facet[] = $sql[] = '(category_id:('.implode(" OR ", $category).') OR (category_id:61 AND filter_id:1))';
                }
                if(in_array('90', $category_id) && $data['call_from'] == 'app') {
                    $sql_facet[] = $sql[] = '(category_id:('.implode(" OR ", $category).') OR (category_id:61 AND filter_id:43))';
                }
            } else {
                $sql_facet[] = $sql[] = 'category_id:('.implode(" OR ", $category).')';
            }
            //** End Static filters and category**//
        } else {
          if(!empty($data['is_latest']) && !empty($data['call_from']) && $data['call_from'] == 'app') {
            $sql_facet[] = $sql[] = '-(*:* AND -category_id:[* TO *])';
            $data['is_latest'] = 0;
          }
        }

        //** Price filter
        if (!empty($data['price_filter'])  && $data['price_filter'] != 'undefined') { // If price range is selected
            $price_min = '';
            $price_max = '';

            if (isset($data['price_filter']) ) {
                $prices = explode('-', $data['price_filter']);
                $price_min = $prices[0];
                $price_max = $prices[1];
            }

            if($this->registry->currency->getCode() == 'INR'){
                $price_values = $this->registry->currency->currencies['INR']['value'];
            }else{
                $price_values = $this->registry->currency->currencies['USD']['value'];
            }


            if($price_max == 0){
                $price_min = $price_min/$price_values;
                $sql[] = "selling_price:[".$price_min." TO * ]";
            }else{
                $price_min = $price_min/$price_values;
                $price_max = $price_max/$price_values;

                $sql[] = "selling_price:[".$price_min." TO ".$price_max. "]";

            }
        }
        //** Rating Filter
        if(!empty($data['rating_filter'])){ // If ratings selected
            $rating_arr = explode(',', $data['rating_filter']);
            if(count($rating_arr) > 1){
                $data['rating_filter'] = min($rating_arr);
            }
            $sql[] = "(rating:[".$data['rating_filter']." TO *] OR (*:* AND -rating:[* TO *]))";

            if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                $sql_facet[] = "(rating:[4 TO *] OR (*:* AND -rating:[* TO *]))";
            }
        }
        else{
            // If international store then, we will not show average products.
            if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                $sql_facet[] = $sql[] = "(rating:[4 TO *] OR (*:* AND -rating:[* TO *]))";
            }
        }

        //** Filters
        if ( !empty( $data['filter_filter'] )) {

            // sor product filter
            if ( strpos( $data['filter_filter'], $this->sor_filter_id ) !== false ) {

                $sql_facet[] = $sql[] = 'sor_type:[* TO *] AND sor_days:[1 TO *]';

                // remove sor filter id from original 'filter_filter'
                $temp_filters = explode( ',', $data['filter_filter'] );

                if (( $key = array_search( $this->sor_filter_id, $temp_filters )) !== false ) {
                    unset( $temp_filters[$key] );
                }

                $data['filter_filter'] = implode( ',', $temp_filters );

                $is_sor_filter_applied = true;
            }

            // exclusive product filter
            if ( strpos( $data['filter_filter'], $this->exclusive_filter_id ) !== false ) {

                $sql_facet[] = $sql[] = '(exclusive:exclusive OR exclusive:both)';

                // remove sor filter id from original 'filter_filter'
                $temp_filters = explode( ',', $data['filter_filter'] );

                if (( $key = array_search( $this->exclusive_filter_id, $temp_filters )) !== false ) {
                    unset( $temp_filters[$key] );
                }

                $data['filter_filter'] = implode( ',', $temp_filters );

                $is_exclusive_filter_applied = true;

            }

            if ( !empty( $data['filter_filter'] )) {

                $implode_filter = array();

                $filters = array_unique(explode(',', $data['filter_filter']));

                foreach ($filters as $filter_id) {
                    $implode_filter[] = (int)$filter_id;
                }

                $filter_string = implode(',', $implode_filter);

                $filter_result = $this->getFilterDetailsWithGroup( $filter_string );

                if( $filter_result ) {
                    foreach ($filter_result as $result) {
                        $filter_or = '('.str_replace(",", " OR ", $result['filter_ids']).')';
                        $filter_group_key = 'filter_group_df_'.$result['filter_group_id'];
                        $filter_sql = $filter_group_key.':'.$filter_or;

                        if( in_array( $result['filter_group_id'], SEARCHABLE_FILTER_GROUP_IDS )) {
                            $searchable_filter_group_sql = 'searchable:('.str_replace(",", "* OR *", '*'.trim($result['filter_names']).'*').')';
                            $filter_sql = '(' . $filter_sql . ' OR ('. $searchable_filter_group_sql .'))';
                        }
                        $sql[] = $filter_sql;
                    }
                }
            }
        }

        if(!isset($data['backend'])){

            $sql_facet[] = $sql[] = 'quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id;
            $sql_facet[] = $sql[] = 'seller_status:1';
            $sql_facet[] = $sql[] = 'vacation_mode:0';
            if(empty($data['price_filter'])){
                $sql_facet[] = $sql[] = 'price:[1 TO *]';
                $sql_facet[] = $sql[] = 'selling_price:[1 TO *]';
            }
            $sql_facet[] = $sql[] = 'is_archived:0';
            $sql_facet[] = $sql[] = 'hsn_code:([* TO *] AND -"")';
            //$sql_facet[] = $sql[] = 'tax_class_id:[1 TO *]';

            $call_from = '';
            if ( !empty($data['call_from']) ) {
                $call_from = strtolower(trim($data['call_from']));
            }

            //*** App only
            if($call_from !== 'app') $sql[] = 'app_only:0';

            //** store_code
            if(isset($data['store_code']) && !empty($data['store_code'])) {
                $upper_store_code = strtoupper($data['store_code']);
                $sql_facet[] = $sql[] = "store_sales:".$upper_store_code;
            }

        } else { // for Backend

            //** SOR Filter
            if ( isset( $data['sor_filter'] ) && $data['sor_filter'] !== '' ) {
                $sor_enums = $this->registry->db->getEnumValues('oc_product_sor_terms','sor_type');
                if ( !empty( $sor_enums ) && isset( $sor_enums[$data['sor_filter']] )) {
                    $sql[] = 'sor_type:'. $sor_enums[$data['sor_filter']] .' AND sor_days:[1 TO *]';
                }
            }

            if ( isset($data['filter_seller_sku']) && !empty((trim($data['filter_seller_sku'])))) {
            // $sql[] = 'searchable:'. trim('*'.$data['filter_seller_sku'].'*');

                $sllr_sku_filter_operator = $data['sllr_sku_filter_operator'];
                $sllr_sku_filter_type_string = $data['sllr_sku_filter_type_string'];
                $sllr_sku_filter_val_from = $data['sllr_sku_filter_val_from'];
                $sllr_sku_filter_val_to = $data['sllr_sku_filter_val_to'];

                // $sql .= " AND ( p.sku LIKE '%" . $this->db->escape(trim($data['filter_seller_sku'])) . "%'" ;
                $searchable = ' ( searchable:'. trim('*'.$data['filter_seller_sku'].'*');

                if(!empty($sllr_sku_filter_type_string)){
                    $searchable .= queryStringForSolr('searchable', $sllr_sku_filter_type_string, $sllr_sku_filter_operator);
                }

                if(!empty($sllr_sku_filter_val_from) && !empty($sllr_sku_filter_val_to) ){
                    $searchable .= queryIntegerForSolr('searchable', $sllr_sku_filter_val_from, $sllr_sku_filter_val_to, $sllr_sku_filter_operator);
                }
                $searchable .= " ) ";
                $sql[] = $searchable;
            }

            if (isset($data['filter_model']) && $data['filter_model'] != '') {
                // $sql_facet[] = $sql[] = 'model_copy:'.trim('*'.$data['filter_model'].'*');

                $filter_operator = $data['filter_operator'];
                $filter_type_string = $data['filter_type_string'];
                $filter_val_from = $data['filter_val_from'];
                $filter_val_to = $data['filter_val_to'];

                $model_copy = ' ( model_copy:'.trim('*'.$data['filter_model'].'*') ;

                if(!empty($filter_type_string)){
                    $model_copy .= queryStringForSolr('model_copy', $filter_type_string, $filter_operator);
                }

                if(!empty($filter_val_from) && !empty($filter_val_to) ){
                    $model_copy .= queryIntegerForSolr('model_copy', $filter_val_from, $filter_val_to, $filter_operator);
                }
                $model_copy .= " ) ";

                $sql[] = $model_copy;

            }


            if (!empty($data['filter_seller_list'])) {
                $sql[] = 'seller_id:'.$data['filter_seller_list'];
            }

            if (!empty($data['filter_name'])) {
                $sql[] = 'name:'.$data['filter_name'];
            }

            if (!empty($data['filter_commission'])) {
                $sql[] = 'commission:'.$data['filter_commission'];
            }

            if (!empty($data['filter_price'])) {
                $sql[] = 'price:'.$data['filter_price'];
            }

            if (isset($data['filter_quantity']) && $data['filter_quantity'] != '') {
                $sql[] = 'quantity:'.$data['filter_quantity'];
            }

            if (isset($data['filter_model']) && $data['filter_model'] != '') {
                $sql[] = 'model_copy:'.trim('*'.$data['filter_model'].'*');
            }

            if (isset($data['filter_status']) && $data['filter_status'] != '') {
                $sql[] = 'status:'.$data['filter_status'];
            }

            if (isset($data['filter_non_single']) && $data['filter_non_single'] == 1 ) {
                $sql[] = 'is_single:false';
            }

            if (isset($data['filter_non_sor']) && $data['filter_non_sor'] == 1) {
                $sql[] = '-store_id:9';
            }

            if ( isset($data['filter_category']) && !empty((trim($data['filter_category'])))) {
                // get products form particular categories ony
                $sql[] = 'category_id:'. trim($data['filter_category']);
            }

            if ( isset($data['filter_sort_order']) && !empty((trim($data['filter_sort_order'])))) {
                $sql[] = 'sort_order:'. trim($data['filter_sort_order']);
            }

            if ( isset($data['filter_hsn_code']) && !empty((trim($data['filter_hsn_code'])))) {
                $sql[] = 'hsn_code:*'. trim($data['filter_hsn_code']) . '*';
            }

            $sql[] = '*:*';
        }

        //*** Store id
        if ($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
            $stores[] = 0;
        } elseif ($this->registry->config->get('config_store_id') == SOR_STORE_ID) {
            $stores[] = SOR_STORE_ID;
        } else {
            $stores[] = 0;
        }
        if(!isset($data['backend'])){
            $sql_facet[] = $sql[] = 'store_id:' . implode(' OR store_id:', $stores);
        }

        $resultset = array();
        $product_data = array();
        $product_data['products'] = '';
        $detail_fetch = 1;

        /************************** Get Products for Backend ***************************/

        if(isset($data['backend']) || $backend == 1) {

            $sql_and = !empty($sql) ? implode(" AND ", $sql): '';

            $sort = '';
            if(count(explode('.', $data['sort'])) > 1) {
                $sort = explode('.', $data['sort'])[1];
            } else {
                $sort = $data['sort'];
            }
            $solr_result = $this->executeSolrQuery($resultset, $sql_and, $data['start'], $data['limit'], $sort, $data['order']);
            $product_data['products'] = $resultset;
            $product_data['product_total'] = $solr_result->getNumFound();
            return $product_data;
            exit();
        }

        /************************** Get Latest Products from Solr ***************************/

        else if(isset($data['is_latest']) && $data['is_latest'] == 1){

            $resultset_priority = array();
            $resultset = array();
            if(!empty($category_id) && !empty($final_top_priority_products)) {
                if(in_array('61', $category_id) || in_array('125', $category_id) || in_array('70', $category_id)) {
                    $top_priority_sql = $sql;
                    $top_priority_sql[] = "id:(".str_replace(',', ' OR ', $final_top_priority_products).")";
                    $top_priority_sql_and = !empty($top_priority_sql) ? implode(" AND ", $top_priority_sql) : '*:*';

                    $priority_sort = '';
                    $priority_order = '';
                    if(!empty(PRIORITY_PRODUCTS_SHUFFLE)) {
                      //Below is to get random results
                      $randString = mt_rand();
                      $priority_sort = 'random_'.$randString;
                      $priority_order = 'DESC';
                    }

                    $config_solr = $this->registry->config->solrConfig();
                    // Get solr client instance
                    $client = new Solarium\Client($config_solr);
                    $client->getPlugin('postbigrequest');
                    // Get select
                    $query = $client->createSelect();

                    // set query
                    $query->setQuery($top_priority_sql_and);
                    $query->addSort($priority_sort, $query::SORT_DESC);

                    // execute query
                    $solr_result = $client->select($query);

                    // format result
                    foreach ($solr_result as $document) {
                        $resultset_priority[] = $document->id;
                    }
                }
            }
            $solr_result = $this->getLatestProducts($data, $sql);
            
            if ( !empty($data['is_group']) ) {

                foreach ($solr_result as $seller_id => $index) {
                    foreach ($index as $key => $value) {
                        $resultset[] = $value->getFields()['id'];
                    }
                }
            } else {
                $resultset = array_merge($resultset, $solr_result);
                $latest_product_count = $this->product_count ?? 0;
                $product_data['product_total'] = $latest_product_count + count($resultset_priority);
                $product_data['total'] = $latest_product_count + count($resultset_priority);
            }

            $result_count = count($resultset);
            shuffle($resultset);
            $resultset = array_merge($resultset_priority, $resultset);
        }

        /************************** GetTrending Products from Solr ***************************/

        else if(isset($data['is_trending']) && $data['is_trending'] == 1){
            $resultset_priority = array();
            $resultset = array();
            if(!empty($category_id) && !empty($final_top_priority_products)) {
                if(in_array('61', $category_id) || in_array('125', $category_id) || in_array('70', $category_id)) {
                  $top_priority_sql = $sql;
                  $top_priority_sql[] = "id:(".str_replace(',', ' OR ', $final_top_priority_products).")";
                  $top_priority_sql_and = !empty($top_priority_sql) ? implode(" AND ", $top_priority_sql) : '*:*';

                  $priority_sort = '';
                  $priority_order = '';
                  if(!empty(PRIORITY_PRODUCTS_SHUFFLE)) {
                    //Below is to get random results
                    $randString = mt_rand();
                    $priority_sort = 'random_'.$randString;
                    $priority_order = 'DESC';
                  }

                  $config_solr = $this->registry->config->solrConfig();
                  // Get solr client instance
                  $client = new Solarium\Client($config_solr);
                  $client->getPlugin('postbigrequest');
                  // Get select
                  $query = $client->createSelect();

                  // set query
                  $query->setQuery($top_priority_sql_and);
                  $query->addSort($priority_sort, $query::SORT_DESC);

                  // execute query
                  $solr_result = $client->select($query);

                  // format result
                  foreach ($solr_result as $document) {
                      $resultset_priority[] = $document->id;
                  }
                }
            }
            $solr_result = $this->getTrendingProducts($data, $sql);
            $result_count = count($solr_result);
            if($result_count < $data['limit'] && $data['days'] <= 97){
                $new_data = $data;
                $new_data['days'] = 90+$data['days'];
                $product_data = $this->getProductFromSolr($new_data);
            } else {
                foreach ($solr_result as $seller_id => $index) {
                    foreach ($index as $key => $value) {
                        $resultset[] = $value->getFields()['id'];
                    }
                }
                shuffle($resultset);
            }
            if(empty($product_data['products'])) {
              $resultset = array_merge($resultset_priority, $resultset);
            }
        }
        /************************** Get Categorywise/search products from Solr ***************************/
        else {

            $model_solr_product = new model_solr_product($this->registry);

            //$this->registry->load->model('preferences');
            $_total_handpicked_products = 300; // no. of handpicked products

            /*********Search Keyword********/
            $keyword = '';
            if (isset($data['filter_tag']) && $data['filter_tag'] != '') {
                $keyword = trim($data['filter_tag']);
            }
            if (isset($data['filter_name']) && $data['filter_name'] != '') {
                $keyword = trim($data['filter_name']);
            }

            $check_keyword = $keyword;

            if ($keyword != '') {

                $original_keyword = $keyword;
                $keyword = array_map( 'trim', array_filter( explode( " ", preg_replace( '/([^a-zA-Z0-9\_\- ]+)/', " ", $keyword ))));
                $keyword = implode( " ", $keyword );
                    
                /*** RXT Code search ***/
                // this code shifted to controller
                // if(strtolower(trim($keyword)) == "rxt") {
                //     $data['sort'] = "p.date_added";
                //     $data['order'] = "DESC";
                // }
                
                /**
                 * added search feature for purchase inventory
                 */
                if( strtolower($original_keyword) == strtolower(WSB_PURCHASE_INVENTORY_SEARCH_TERM) ) {
                    
                    $sql_facet[] = $sql[] =  "(seller_id:(". implode(' OR ', WSB_PURCHASE_INVENTORY_SELLERS) ."))";
                
                } else {

                    $pos = (int)strpos($keyword, " ");

                    if ($pos > 0) {

                        $check_if_first_char_inverted = strpos(html_entity_decode($keyword), '"');

                        if ($check_if_first_char_inverted === 0) { //do exact match search
                            $keyword = html_entity_decode($keyword);
                            $keyword = substr($keyword, 1, strlen($keyword));
                            //check if last character is double quote
                            //get last char
                            $inverted = substr($keyword, -1);
                            if($inverted == '"'){
                                $keyword = substr($keyword, 0, strlen($keyword)-1);
                            }
                            $keyword = addslashes($keyword);
                            $arr_search = explode(" ", $keyword);
                            $search_string = implode("+", $arr_search);
                            $sql_facet[] = $sql[] = 'searchable:"' . $search_string . '"';


                        } else {
                            $keyword = addslashes($keyword);
                            $arr_search = explode(" ", $keyword);

                            $sql_searchable = array();

                            foreach ($arr_search as $word) {

                                $sql_searchable[] = $this->getSynonyms($word);//$search_for_all_words;
                            }

                            /**
                             * changed search priority
                             * first priority: exact same keyword ex: "rayon kurti"
                             * second priority: rayon+kurti (other words can come in between)
                             * third priority: synonyms
                             */
                            $first_priority_search = '(searchable:"'. $keyword . '")^1000';
                            $sql_facet[] = $sql[] = '('. $first_priority_search .' OR ('. implode(' AND ', $sql_searchable).'))';

                        }

                    } else {

                        $additional_words = array();
                        // To allow seller specific product search
                        // eg: 001_JP and 001JP
                        // This is done because new product codes dont have '_'
                        if ( (int)strpos($keyword, "_") > 0 ) {
                            $additional_words[] = trim(str_replace("_","",$keyword));
                        }

                        $sql_searchable[] = $this->getSynonyms($keyword, $additional_words);
                        $sql_facet[] = $sql[] =  $sql_searchable[0];

                    }
                }
            }

            /********App Requests***********/

            if($call_from == 'app') {

                //*** Non Servicable Areas
                $post_code = '';
                if ( !empty($data['post_code']) ) {
                    $post_code = strtolower(trim($data['post_code']));
                }

                if($call_from == 'app' && !empty($post_code)) $sql_facet[] = $sql[] = '-non_serviceable_areas:'.$post_code;

            }
            // case for handpicked products
            if( isset( $data['page'] ) && $data['page'] == 1 && $data['sort'] == 'sort_order' && empty( $data['filter_only'] )) {

                $all_requested_categories = $category;
                if(!empty($call_from) && $call_from == 'customer_preference') {
                  $all_requested_categories[] = 'customer_preference';
                }

                if ( !empty( $keyword )) {
                    $all_requested_categories[] = 'search';
                }
                //$csv_req = 1;

                $resultset = array();

                /***Top priority**/
                $top_priority_resultset = array();

                if(!empty($final_top_priority_products)
                  && empty($data['filter_filter']) 
                  && empty($data['rating_filter']) 
                  && empty($data['price_filter']) 
                  && (empty($call_from) || (!empty($call_from) && $call_from != 'customer_preference'))) {
                    $top_priority_sql = $sql;
                    $top_priority_sql[] = "id:(".str_replace(',', ' OR ', $final_top_priority_products).")";
                    $top_priority_sql_and = !empty($top_priority_sql) ? implode(" AND ", $top_priority_sql) : '*:*';

                    $priority_sort = '';
                    $priority_order = '';
                    if(!empty(PRIORITY_PRODUCTS_SHUFFLE)) {
                      //Below is to get random results
                      $randString = mt_rand();
                      $priority_sort = 'random_'.$randString;
                      $priority_order = 'DESC';
                    }

                    $solr_result_top_priority = $this->executeSolrQuery($top_priority_resultset, $top_priority_sql_and, 0, 10, $priority_sort, $priority_order);
                    if(!empty($top_priority_resultset)) {
                        $resultset = array_unique(array_filter($top_priority_resultset));
                    }
                }
                /*******/

                // define all expected resultsets
                $resultset_wsb_store = array();
                $resultset_same_seller = array();
                $resultset_same_city_seller = array();

                $resultset_sub_latest = array();
                $resultset_sort_order = array();

                $resultset_order_history = array();
                $resultset_shortlist = array();
                $resultset_recently_viewed = array();

                /********Calculate expected count***********/

                // define mainpool's percentages
                $kurti_categories = array();//array('61','73','125','74','90','93');
                if(!empty(array_intersect($kurti_categories, $category))) {
                 $expected_percentage_cart = 0;
                 $expected_percentage_latest = 100;
                 $expected_percentage_personalized = 0;
                } else {
                 $expected_percentage_cart = 30;
                 $expected_percentage_latest = 30;
                 $expected_percentage_personalized = 40;
                }
                if(!empty($call_from) && $call_from == 'customer_preference') {
                   $expected_percentage_cart = 0;
                   $expected_percentage_latest = 30;
                   $expected_percentage_personalized = 70;
                   $_total_handpicked_products = 100;

                   // for now only latest 15 days products are fetched 
                   $sql_facet[] = $sql[] = "date_added:[NOW/DAY-".$this->fashcart_upcoming_products_days."DAY TO *]";

                   // prioritize wsbjps products for upcoming designs in fashcart for dropshipper
                   if( !empty($data['is_dropshipper']) && $data['is_dropshipper'] == 1 ) {

                        $sql_upcoming_wsbjps = $sql;
                        $sql_upcoming_wsbjps[] = "(searchable:*wsbjps*)";
                        $sql_and_upcoming_wsbjps = implode(' AND ', $sql_upcoming_wsbjps);

                        $this->executeSolrQuery($top_priority_resultset, $sql_and_upcoming_wsbjps, 0, $data['limit'], 'hotness_value', 'DESC');

                        if (!empty($top_priority_resultset)) {
                            shuffle($top_priority_resultset);
                            $resultset = array_merge($top_priority_resultset, $resultset);
                        }
                        
                   }
                }
                // define subpool counts
                $cart_subpool_count = 3;
                $latest_subpool_count = 2;
                $personalized_subpool_count = 3;

                // define subpool percentages
                if(!empty(array_intersect($kurti_categories, $category))) {
                 $expected_percentage_sort_order = 100; // 25% of latest
                 $expected_percentage_sub_latest = 0; // 75% of latest
                } else {
                 $expected_percentage_sort_order = 25; // 25% of latest
                 $expected_percentage_sub_latest = 75; // 75% of latest
                }

                $expected_percentage_order = 0; //
                $expected_percentage_shortlist = 0; //
                $expected_percentage_recent = 0; //

                // get mainpool product counts
                $expected_prod_count_cart = ceil($expected_percentage_cart/100*$_total_handpicked_products);
                $expected_prod_count_latest = ceil($expected_percentage_latest/100*$_total_handpicked_products);
                $expected_prod_count_personalized = ceil($expected_percentage_personalized/100*$_total_handpicked_products);

                /********Calculate original count***********/

                $pref_order_history_arr = array();
                $pref_return_history_arr = array();
                $pref_shortlist_arr = array();
                $pref_product_review_arr = array();
                
                if(!empty($user_id)){
                    
                    $pref_cats = '';
                    if ( !empty( $all_requested_categories )) {
                        
                        if ( !in_array( 'search', $all_requested_categories )) {

                            $all_requested_categories_temp = $all_requested_categories;

                            if (( $key = array_search( 'customer_preference', $all_requested_categories_temp )) !== false ) {
                                unset( $all_requested_categories_temp[$key] );
                            }

                            if ( !empty( $all_requested_categories_temp )) {
                                $pref_cats = implode( ',' , $all_requested_categories_temp );
                            }
                        }
                    }

                    // Get server preferences
                    $all_pref_arr = $this->registry->model_preferences->getCustomerPreferencesByPreferenceType( (int) $user_id, $pref_cats, 'algo', 
                                                                                                                            array('order', 'shortlist', 'product_review', 'return'), 
                                                                                                                            true );

                    $pref_order_history_arr = $all_pref_arr['order'] ?? array();
                    $pref_shortlist_arr = $all_pref_arr['shortlist'] ?? array();
                    $pref_product_review_arr = $all_pref_arr['product_review'] ?? array();
                    $pref_return_history_arr = $all_pref_arr['return'] ?? array();
                }

                // Get client preferences
                $client_pref_shortlist = array();
                $client_pref_recent = array();
                $raw_client_cust_pref = array();

                if((isset($data['client_preferences']) && !empty($data['client_preferences']))) {
                    if(is_array($data['client_preferences'])) {
                        $raw_client_cust_pref = $data['client_preferences'];
                    } else {
                        $raw_client_cust_pref = json_decode(htmlspecialchars_decode($data['client_preferences']),true);
                    }
                }

                if(!empty($raw_client_cust_pref)){
                    $raw_client_cust_pref_arr = array();
                    foreach ($raw_client_cust_pref as $key => $arr_data) {
                        foreach ($arr_data as $key_data => $value) {
                            $raw_client_cust_pref_arr[$key][$key_data] = $value;
                        }
                    }

                    if(!empty($raw_client_cust_pref_arr)) {
                        $client_pref_shortlist_arr = array();
                        $client_pref_recent_arr = array();

                        foreach ($raw_client_cust_pref_arr as $key => $pref_data) {
                            if($pref_data['type'] == 1){
                                $client_pref_shortlist_arr[] = $pref_data;
                            } else if($pref_data['type'] == 2) {
                                $client_pref_recent_arr[] = $pref_data;
                            }
                        }
                        if(!empty($client_pref_shortlist_arr)){
                            foreach ($client_pref_shortlist_arr as $key => $value) {
                                $client_pref_shortlist_arr[$key]['customer_id'] = $user_id;
                            }
                            $client_pref_shortlist = $this->registry->model_preferences->formatProductsToPreference( $client_pref_shortlist_arr );
                        }
                        if(!empty($client_pref_recent_arr)){
                            foreach ($client_pref_recent_arr as $key => $value) {
                                $client_pref_recent_arr[$key]['customer_id'] = $user_id;
                            }
                            $client_pref_recent = $this->registry->model_preferences->formatProductsToPreference( $client_pref_recent_arr );
                        }
                    }
                }

                $final_pref_arr_order = array();
                $final_pref_arr_shortlist = array();
                $final_pref_arr_recent = array();
                $final_pref_arr_product_review = array();
                $client_pref_shortlist_f = array();
                $client_pref_recent_f = array();

                if(!empty($client_pref_shortlist)) {
                    foreach ($client_pref_shortlist as $user => $client_data) {
                        $client_pref_shortlist_f = $client_pref_shortlist[$user];
                    }
                }
                if(!empty($client_pref_recent)) {
                    foreach ($client_pref_recent as $user => $client_data) {
                        $client_pref_recent_f = $client_pref_recent[$user];
                    }
                }

                if(!empty($pref_order_history_arr)) {
                    $final_pref_arr_order = $pref_order_history_arr;
                }

                if(!empty($pref_product_review_arr)) {
                    $final_pref_arr_product_review = $pref_product_review_arr;
                }

                if(!empty($pref_shortlist_arr) && !empty($client_pref_shortlist_f)) {

                    $final_pref_arr_shortlist = $this->registry->model_preferences->createFinalPreferenceData($pref_shortlist_arr, $client_pref_shortlist_f);

                } else if(!empty($pref_shortlist_arr)) {
                    $final_pref_arr_shortlist = $pref_shortlist_arr;
                } else if(!empty($client_pref_shortlist_f)) {
                    $final_pref_arr_shortlist = $client_pref_shortlist_f;
                }

                if(!empty($client_pref_recent_f)) {
                    $final_pref_arr_recent = $client_pref_recent_f;
                }

                // Calculate original count for personalized/cart (login and cart status)
                if(empty($user_id)) {
                    if(!empty($final_pref_arr_shortlist) || !empty($final_pref_arr_recent)) {
                        $expected_prod_count_personalized += $expected_prod_count_cart;
                    } else {
                        $expected_prod_count_personalized = 0;
                    }
                    $expected_prod_count_cart = 0;
                } else {
                    if(!($this->registry->cart->hasProducts())){
                        $expected_prod_count_personalized += $expected_prod_count_cart;
                        $expected_prod_count_cart = 0;
                    }
                    if(empty($final_pref_arr_shortlist) && empty($final_pref_arr_recent) && empty($final_pref_arr_order)) {
                        $expected_prod_count_personalized = 0;
                    }
                }

                // get product count of subpool for cartpool
                $mod_cart = $expected_prod_count_cart%$cart_subpool_count;
                $expected_prod_count_wsb_store = floor($expected_prod_count_cart/$cart_subpool_count) + $mod_cart;

                $expected_prod_count_seller_same_city = $expected_prod_count_same_seller = floor($expected_prod_count_cart/$cart_subpool_count);

                // get product count of subpool for latestpool
                $expected_prod_count_sort_order = ceil($expected_percentage_sort_order*$expected_prod_count_latest/100);
                $expected_prod_count_sub_latest = floor($expected_percentage_sub_latest*$expected_prod_count_latest/100);

                // get product count of subpool for personalized
                if($expected_prod_count_personalized > 0) {

                    $expected_prod_count_order = 0;
                    $expected_prod_count_shortlist = 0;
                    $expected_prod_count_recent = 0;

                    if(!empty($final_pref_arr_order)) {
                        $expected_prod_count_order = ceil(50*$expected_prod_count_personalized/100);
                        if(empty($final_pref_arr_recent) && empty($final_pref_arr_shortlist)) {
                            $expected_prod_count_order = ceil(100*$expected_prod_count_personalized/100);
                        }
                    }
                    if(!empty($final_pref_arr_shortlist)) {
                        $expected_prod_count_shortlist = ceil(50*$expected_prod_count_personalized/100);
                        if(empty($final_pref_arr_order) && empty($final_pref_arr_recent)) {
                            $expected_prod_count_shortlist = ceil(100*$expected_prod_count_personalized/100);
                        }
                        if(!empty($final_pref_arr_order) && !empty($final_pref_arr_recent)) {
                            $expected_prod_count_shortlist = ceil(25*$expected_prod_count_personalized/100);
                        }
                    }
                    if(!empty($final_pref_arr_recent)) {
                        $expected_prod_count_recent = ceil(50*$expected_prod_count_personalized/100);
                        if(empty($final_pref_arr_order) && empty($final_pref_arr_shortlist)) {
                            $expected_prod_count_recent = ceil(100*$expected_prod_count_personalized/100);
                        }
                        if(!empty($final_pref_arr_order) && !empty($final_pref_arr_shortlist)) {
                            $expected_prod_count_recent = ceil(25*$expected_prod_count_personalized/100);
                        }
                    }
                }

                // get return preference; seller to be removed from sort order/latest/personalized data
                $pref_return_sql = '';

                if(!empty($pref_return_history_arr)){
                    $pref_return_seller_arr = array();
                    foreach ($pref_return_history_arr as $cat_id => $cat_pref_detail) {

                        if ( !empty( $cat_pref_detail['seller'] )) {
                            
                            foreach ($cat_pref_detail['seller'] as $seller_id => $seller_pref_detail) {
                                $pref_return_seller_arr[] = $seller_id;
                            }
                        }
                    }
                    if(!empty($pref_return_seller_arr)){
                        $pref_return_sql = "-seller_id:(".implode(" OR ", array_unique(array_filter($pref_return_seller_arr))).")";
                    }
                }

                // get disliked sellers(Order Product like/dislike); to be removed from whole Personalization
                $product_review_sub_sql = array();
                $sql_seller_dislike = '';
                if(!empty($final_pref_arr_product_review)) {
                  $sql_for = 'product_review';
                  $product_review_sub_sql = $this->getPersonalizedSQL($final_pref_arr_product_review, $all_requested_categories, $sql_for);
                  if(!empty($product_review_sub_sql['exclude'])) {
                    $sql_seller_dislike = " AND ".$product_review_sub_sql['exclude'];
                  }
                }

                //********** City (Cart) Criteria **********

                if(!empty($expected_prod_count_cart) && !empty($this->registry->cart->hasProducts())) {
                    $cart_products = $this->registry->cart->getProducts('', true);

                    $seller_pickup_city_code = array();
                    $seller_id = array();
                    $cart_seller_city_code = array();
                    $cart_seller_id = array();

                    if(!empty($cart_products)) {
                        foreach ($cart_products as $key => $cart_product_data) {
                            $cart_seller_city_code[] = $cart_product_data['seller_pickup_city_code'];
                            $cart_seller_id[] = $cart_product_data['seller_id'];
                        }
                    }

                    // *********** WSB Store same city

                    $result_wsb_sellers = array();
                    if(!empty($cart_seller_city_code)) {
                        $result_wsb_sellers = $model_solr_product->getWSBStoreSellerId(array_unique($cart_seller_city_code));
                    }

                    if(!empty($result_wsb_sellers)) {
                        foreach ($result_wsb_sellers as $key => $seller_data) {
                            $wsb_sellers_arr[] = $seller_data['seller_id'];
                        }
                        if(!empty($wsb_sellers_arr)) {
                            $sql_wsb_store = ' AND seller_id:('.implode(" OR ", array_unique(array_filter($wsb_sellers_arr))).')';
                        } else {
                            $sql_wsb_store = '';
                        }

                        $sql_and_wsb_store = !empty($sql) ? implode(" AND ", $sql).$sql_wsb_store.$sql_seller_dislike : '';
                        if(!empty($pref_return_sql)){
                            $sql_and_wsb_store = $sql_and_wsb_store.' AND '.$pref_return_sql;
                        }

                        if(count($resultset) > 0){
                            $sql_and_wsb_store .= ' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')';
                        }

                        $solr_result_wsb_store = $this->executeSolrQuery($resultset, $sql_and_wsb_store, 0, $expected_prod_count_wsb_store, 'hotness_value', 'DESC');

                        foreach ($solr_result_wsb_store->getDocuments() as $document) {
                            $resultset_wsb_store[] = $document->id;
                        }
                    }

                    // *********** same seller
                    $original_prod_count_same_seller = $expected_prod_count_same_seller + ($expected_prod_count_wsb_store-count($resultset_wsb_store));
                    if(!empty($cart_seller_id)) {

                        //include similar seller (also use scoring to promote same seller)
                        $cart_similar_seller = $model_solr_product->getSimilarSellers($cart_seller_id, $category);
                        if($cart_similar_seller) {
                            foreach ($cart_similar_seller as $key => $similar_seller_ids) {
                                $similar_seller_arr = explode(',', $similar_seller_ids['similar_sellers']);
                                foreach ($similar_seller_arr as $key => $value) {
                                    $cart_seller_id[] = $value;
                                }
                            }
                        }

                        $sql_same_seller = ' AND seller_id:('.implode(" OR ", array_unique(array_filter($cart_seller_id))).')';
                    } else {
                        $sql_same_seller = '';
                    }

                    $sql_duplicate_product = '';
                    if(count($resultset) > 0){
                        $sql_duplicate_product = ' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')';
                    }

                    $sql_and_same_seller = !empty($sql) ? implode(" AND ", $sql).$sql_same_seller.$sql_duplicate_product.$sql_seller_dislike : '';
                    if(!empty($pref_return_sql)){
                        $sql_and_same_seller = $sql_and_same_seller.' AND '.$pref_return_sql;
                    }
                    $solr_result_same_seller = $this->executeSolrQuery($resultset, $sql_and_same_seller, 0, $original_prod_count_same_seller, 'hotness_value', 'DESC');

                    foreach ($solr_result_same_seller->getDocuments() as $document) {
                        $resultset_same_seller[] = $document->id;
                    }

                    // *********** sellers from same city
                    $original_prod_count_seller_same_city = $expected_prod_count_seller_same_city + ($original_prod_count_same_seller-count($resultset_same_seller));

                    $result_all_sellers = $model_solr_product->getSellersWithCity(array_unique(array_filter($cart_seller_city_code)));
                    $same_city_sellers_arr = array();
                    if(!empty($result_all_sellers)) {
                        foreach ($result_all_sellers as $key => $seller_data) {
                            $same_city_sellers_arr[] = $seller_data['seller_id'];
                        }
                    }

                    // Same city sellers count limited to 50
                    if(!empty($same_city_sellers_arr)){
                        shuffle($same_city_sellers_arr);
                        $same_city_sellers_arr = array_slice($same_city_sellers_arr, 0, 50);
                    }

                    if(!empty($same_city_sellers_arr)) {
                        $sql_same_city_seller = ' AND seller_id:('.implode(" OR ", array_unique(array_filter($same_city_sellers_arr))).')';
                    } else {
                        $sql_same_city_seller = '';
                    }

                    $sql_duplicate_product = '';
                    if(count($resultset) > 0){
                        $sql_duplicate_product = ' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')';
                    }

                    $sql_and_same_city_seller = !empty($sql) ? implode(" AND ", $sql).$sql_same_city_seller.$sql_duplicate_product.$sql_seller_dislike : '';
                    if(!empty($pref_return_sql)){
                        $sql_and_same_city_seller = $sql_and_same_city_seller.' AND '.$pref_return_sql;
                    }
                    $solr_result_same_city_seller = $this->executeSolrQuery($resultset, $sql_and_same_city_seller, 0, $original_prod_count_seller_same_city, 'hotness_value', 'DESC');

                    foreach ($solr_result_same_city_seller->getDocuments() as $document) {
                        $resultset_same_city_seller[] = $document->id;
                    }
                }

                //*********** Latest products Criteria **********

                //****** Sort Order 1 TO 998 products
                if( $keyword == '' ){

                    $sql_sort_order = ' AND sort_order:[1 TO 998]';
                    $sql_and_sort_order = !empty($sql) ? implode(" AND ", $sql).$sql_sort_order.$sql_seller_dislike : '';
                    if(!empty($pref_return_sql)){
                        $sql_and_sort_order = $sql_and_sort_order.' AND '.$pref_return_sql;
                    }

                    if(count($resultset) > 0){
                        $sql_and_sort_order .= ' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')';
                    }
                    //Below is to get random results
                    $randString = mt_rand();
                    $solr_result_sort_order = $this->executeSolrQuery($resultset, $sql_and_sort_order, 0, $expected_prod_count_sort_order, 'random_'.$randString, 'DESC');

                    foreach ($solr_result_sort_order->getDocuments() as $document) {
                        $resultset_sort_order[] = $document->id;
                    }
                }

                //***** Latest products from the different seller in same category
                $original_prod_count_sub_latest = $expected_prod_count_sub_latest + ($expected_prod_count_sort_order-count($resultset_sort_order));
                $sql_latest_same_cat = '';
                if(count($resultset) > 0){
                    $sql_latest_same_cat = ' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')';
                }

                $sql_and_sub_latest = !empty($sql) ? implode(" AND ", $sql).$sql_latest_same_cat.$sql_seller_dislike : '';
                if(!empty($pref_return_sql)){
                    $sql_and_sub_latest = $sql_and_sub_latest.' AND '.$pref_return_sql;
                }

                $group_data = array(
                                "is_group" => 1,
                                "group_field" => "seller_id",
                                "group_limit" => 2,
                                "group_sort_data_by" => "date_added"
                              );
                $latest_query_count = 1;
                $resultset_sub_latest_for_csv = array();
                while ($latest_query_count <= 3 && (count($resultset_sub_latest) < $original_prod_count_sub_latest)) {
                  $group_data['latest_query_count'] = $latest_query_count;
                  $sql_and_sub_latest_temp = $sql_and_sub_latest;
                  if(!empty($resultset_sub_latest)){
                    $sql_and_sub_latest_temp = $sql_and_sub_latest." AND -id:(".implode(' OR ',array_unique($resultset_sub_latest)).")";
                  }
                  $calculated_limit = ceil(($original_prod_count_sub_latest-count($resultset_sub_latest)));
                  $solr_result_sub_latest = $this->executeSolrQuery($resultset, $sql_and_sub_latest_temp, 0, $calculated_limit, 'date_added', 'DESC', $group_data);
                  foreach ($solr_result_sub_latest as $seller_id => $index) {
                    foreach ($index as $key => $value) {
                      if($original_prod_count_sub_latest > count($resultset_sub_latest)) {
                        $resultset[] = $resultset_sub_latest[] = $value->getFields()['id'] ?? "0";
                        $resultset_sub_latest_for_csv[] = array(
                                                                "id"=>$value->getFields()['id'] ?? "0",
                                                                "seller_id"=>$value->getFields()['seller_id'] ?? "0",
                                                                "date_added"=>$value->getFields()['date_added'] ?? "",
                                                                "model"=>$value->getFields()['model'] ?? ""
                                                              );
                      } else {
                        break 2;
                      }
                    }
                  }
                  $latest_query_count++;
                }
                if($original_prod_count_sub_latest > count($resultset_sub_latest)) {
                  if(!empty($resultset)) {
                    $sql_and_sub_latest .= " AND -id:(".implode(' OR ',array_unique($resultset)).")";
                  }
                  $solr_result_sub_latest = $this->executeSolrQuery($resultset, $sql_and_sub_latest, 0, $original_prod_count_sub_latest-count($resultset_sub_latest), 'date_added', 'DESC');
                  foreach ($solr_result_sub_latest->getDocuments() as $document) {
                  	$resultset_sub_latest[] = $document->id;

                    $resultset_sub_latest_for_csv[] = array(
                                                        "id"=>$document->id ?? "0",
                                                        "seller_id"=>$document->seller_id ?? "0",
                                                        "date_added"=>$document->date_added ?? "",
                                                        "model"=>$document->model ?? ""
                                                      );
                  }
                }

                //********** Personalized products Criteria **********

                if($expected_prod_count_personalized > 0) {

                    // Order History Personalization
                    $original_prod_count_order = $expected_prod_count_order;
                    if($expected_prod_count_order > 0 && !empty($final_pref_arr_order)) {
                        $original_prod_count_order = $expected_prod_count_order + ($original_prod_count_sub_latest-count($resultset_sub_latest));
                        $pref_order_sql = array();

                        $order_sub_sql = $this->getPersonalizedSQL($final_pref_arr_order, $all_requested_categories);

                        $order_sub_sql = array_filter($order_sub_sql);
                        $product_review_sub_sql = array_filter($product_review_sub_sql);

                        if(!empty($order_sub_sql) || !empty($product_review_sub_sql)) {

                            !empty($sql)?$pref_order_sql[] = '('.implode(" AND ", $sql).')':'';

                            //if(!empty($order_sub_sql)) {
                              if(!empty($product_review_sub_sql)) {
                                if(!empty($product_review_sub_sql['include'])) {
                                  if(!empty($order_sub_sql)) {
                                    $pref_order_sql[] = '(('.implode(" AND ", $order_sub_sql).') OR ('.$product_review_sub_sql['include'].'))';
                                  } else {
                                    $pref_order_sql[] = '('.$product_review_sub_sql['include'].')';
                                  }
                                } else {
                                  if(!empty($order_sub_sql)) {
                                    $pref_order_sql[] = '('.implode(" AND ", $order_sub_sql).')';
                                  }
                                }
                                // if(!empty($product_review_sub_sql['exclude'])) {
                                //   $pref_order_sql[] = $product_review_sub_sql['exclude'];
                                // }
                              } else {
                                if(!empty($order_sub_sql)) {
                                  $pref_order_sql[] = '('.implode(" AND ", $order_sub_sql).')';
                                }
                              }
                            //}

                            !empty($pref_return_sql)?$pref_order_sql[] = $pref_return_sql:'';
                            !empty($resultset)?$pref_order_sql[] = '-id:('.implode(' OR ', array_unique(array_filter($resultset))).')':'';

                            $sql_and_order = implode(' AND ', $pref_order_sql).$sql_seller_dislike;
                            $solr_result_order = $this->executeSolrQuery($resultset, $sql_and_order, 0, $original_prod_count_order, 'hotness_value', 'DESC');
                            foreach ($solr_result_order->getDocuments() as $document) {
                                $resultset_order_history[] = $document->id;
                            }
                        }
                    }

                    // Shortlist Personalization
                    $original_prod_count_shortlist = $expected_prod_count_shortlist;
                    if($expected_prod_count_shortlist > 0) {
                        $original_prod_count_shortlist = $expected_prod_count_shortlist + ($original_prod_count_order-count($resultset_order_history));

                        $pref_shortlist_sql = array();

                        $shortlist_sub_sql = $this->getPersonalizedSQL($final_pref_arr_shortlist, $all_requested_categories);
                        if(!empty($shortlist_sub_sql)) {

                            !empty($sql)?$pref_shortlist_sql[] = '('.implode(" AND ", $sql).')':'';
                            !empty($shortlist_sub_sql)?$pref_shortlist_sql[] = '('.implode(" AND ", $shortlist_sub_sql).')':'';
                            !empty($pref_return_sql)?$pref_shortlist_sql[] = $pref_return_sql:'';
                            !empty($resultset)?$pref_shortlist_sql[] = '-id:('.implode(' OR ', array_unique(array_filter($resultset))).')':'';

                            $sql_and_shortlist = implode(' AND ', $pref_shortlist_sql).$sql_seller_dislike;
                            $solr_result_shortlist = $this->executeSolrQuery($resultset, $sql_and_shortlist, 0, $original_prod_count_shortlist, 'hotness_value', 'DESC');
                            foreach ($solr_result_shortlist->getDocuments() as $document) {
                                $resultset_shortlist[] = $document->id;
                            }
                        }
                    }

                    // Recently Viewed Personalization
                    $original_prod_count_recent = $expected_prod_count_recent;
                    if($expected_prod_count_recent > 0) {
                        $original_prod_count_recent = $expected_prod_count_recent + ($original_prod_count_shortlist-count($resultset_shortlist));

                        $pref_recent_sql = array();
                        $recent_sub_sql = $this->getPersonalizedSQL($final_pref_arr_recent, $all_requested_categories);

                        if(!empty($recent_sub_sql)) {

                            !empty($sql)?$pref_recent_sql[] = '('.implode(" AND ", $sql).')':'';
                            !empty($recent_sub_sql)?$pref_recent_sql[] = '('.implode(" AND ", $recent_sub_sql).')':'';
                            !empty($pref_return_sql)?$pref_recent_sql[] = $pref_return_sql:'';
                            !empty($resultset)?$pref_recent_sql[] = '-id:('.implode(' OR ', array_unique(array_filter($resultset))).')':'';

                            $sql_and_recent = implode(' AND ', $pref_recent_sql).$sql_seller_dislike;
                            $solr_result_recent = $this->executeSolrQuery($resultset, $sql_and_recent, 0, $original_prod_count_recent, 'hotness_value', 'DESC');
                            foreach ($solr_result_recent->getDocuments() as $document) {
                                $resultset_recently_viewed[] = $document->id;
                            }
                        }
                    }
                }

                // set serach keyword to DB
                if (!empty($check_keyword)) {
                    $has_results = count($resultset);
                    if ($has_results != 0) {
                        $has_results = 1;
                    } else {
                        $has_results = 0;
                    }
                    $model_solr_product->setSearchedKeyword($check_keyword, $has_results);
                }

                //*******Create final resultset by picking from pools*******//
                $final_resultset = array();

                shuffle($resultset_wsb_store);
                shuffle($resultset_same_seller);
                shuffle($resultset_same_city_seller);
                shuffle($resultset_sort_order);
                shuffle($resultset_sub_latest);
                shuffle($resultset_order_history);
                shuffle($resultset_shortlist);
                shuffle($resultset_recently_viewed);

                //**********Subpool picking***********
                $final_cart_resultset = array();
                $final_latest_resultset = array();
                $final_personalized_resultset = array();

                // Cart
                if($expected_prod_count_cart > 0) {
                    $cart_data_arr = array();
                    $cart_data_arr['wsb_store'] = $resultset_wsb_store;
                    $cart_data_arr['same_seller'] = $resultset_same_seller;
                    $cart_data_arr['same_city_seller'] = $resultset_same_city_seller;

                    $cart_ratio_arr = array();
                    $final_cart_resultset = $this->getPoolwiseResultset($cart_data_arr, $cart_ratio_arr);
                }

                // Latest
                if($expected_prod_count_latest > 0) {
                    $latest_data_arr = array();
                    $latest_data_arr['sort_order'] = $resultset_sort_order;
                    $latest_data_arr['sub_latest'] = $resultset_sub_latest;

                    $latest_ratio_arr = array();
                    $latest_ratio_arr['sort_order'] = 1;
                    $latest_ratio_arr['sub_latest'] = 3;

                    $final_latest_resultset = $this->getPoolwiseResultset($latest_data_arr, $latest_ratio_arr);
                }

                // Personalized
                if($expected_prod_count_personalized > 0) {
                    $personalized_data_arr = array();
                    $personalized_data_arr['order'] = $resultset_order_history;
                    $personalized_data_arr['shortlist'] = $resultset_shortlist;
                    $personalized_data_arr['recent'] = $resultset_recently_viewed;

                    $personalized_ratio_arr = array();
                    $final_personalized_resultset = $this->getPoolwiseResultset($personalized_data_arr, $personalized_ratio_arr);
                }

                //**********Mainpool picking***********

                $final_data_arr = array();
                $final_data_arr['cart'] = $final_cart_resultset;
                $final_data_arr['latest'] = $final_latest_resultset;
                $final_data_arr['personalized'] = $final_personalized_resultset;

                $final_ratio_arr = array();
                $final_resultset = array_merge($top_priority_resultset, $this->getPoolwiseResultset($final_data_arr, $final_ratio_arr));
            }

            $filter_facets = array();
            if(!empty($data['sort']) && $data['sort'] == 'sort_order'){ // case for handpicked (other than latest/trending)
                if(!isset($final_resultset)) { $final_resultset = array(); }
                $handpicked_ids = (isset($data['handpicked_ids']) && !empty($data['handpicked_ids']) && $data['page'] > 1) ?$data['handpicked_ids']:rtrim(strtr(base64_encode(gzdeflate(implode(',',$final_resultset), 9)), '+/', '-_'), '=');

                $product_data['handpicked_ids'] = $handpicked_ids;

                $handpicked_ids = gzinflate(base64_decode(strtr($handpicked_ids, '-_', '+/')));
                $handpicked_ids = explode("," , $handpicked_ids);
                $resultset = $handpicked_ids;
                $handpicked_count = count($resultset);

                $sql_key = array_search('quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id, $sql);

                if(array_search('quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id, $sql) && $this->show_out_of_stock != 0){
                    $sql_facet[$sql_key] = $sql[$sql_key] = '(*:* OR (quantity:0 OR stock_status_id:'.$out_of_stock_id.')^20000)';
                    //$sql_facet[$sql_key] = '(*:* OR (quantity:0 OR stock_status_id:'.$out_of_stock_id.')^20000)';
                }

                if(count($resultset) > 0 && !empty($resultset[0])) {
                    $sql_and = !empty($sql) ? implode(" AND ", $sql).' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')' : '';
                } else {
                    $sql_and = !empty($sql) ? implode(" AND ", $sql) : '';
                }
                if (count($resultset) > 0 && empty($resultset[0])){
                    $resultset = array();
                    $handpicked_count = 0;
                }

                /*** For pages with limit ***/
                $chunk_start = $start = (isset($data['start']) && $data['start'] != '')?$data['start']:0;
                $chunk_limit = $limit = (isset($data['limit']) && $data['limit'] != '')?$data['limit']:10;
                $mod = $handpicked_count%$limit;

                if($start < $handpicked_count && ($start+$limit) <= $handpicked_count) {
                    $start = 0;
                    $limit = 0;
                } else if($start < $handpicked_count && ($start+$limit) > $handpicked_count) {
                    $start = 0;
                    $limit = $limit - $mod;
                } else if($start >= $handpicked_count && ($start+$limit) > $handpicked_count) {
                    $start = ($start-$handpicked_count);
                    $limit = $limit;
                    $chunk_start = 0;
                    $resultset = array();
                }

                $this->_final_data_fetch = true;
                
                if(!empty($call_from) && $call_from == 'customer_preference') {
                  $product_data['product_total'] = $handpicked_count;
                  $product_data['total'] = $handpicked_count;
                } else {
                  $solr_result = $this->executeSolrQuery($resultset, $sql_and, $start, $limit, 'hotness_value', 'DESC');
                  $product_data['product_total'] = $solr_result->getNumFound()+$handpicked_count;
                  $product_data['total'] = $solr_result->getNumFound()+$handpicked_count;
                
                    // $in_stock_sql_key = array_search( 'quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id, $sql );

                    // if ( $in_stock_sql_key && ( $this->show_out_of_stock != 0 )) {

                    //     $sql_facet[$in_stock_sql_key] = $sql[$in_stock_sql_key] = '(quantity:0 OR stock_status_id:' . $out_of_stock_id . ')';

                    //     if(count($resultset) > 0 && !empty($resultset[0])) {
                    //         $sql_and = !empty($sql) ? implode(" AND ", $sql).' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')' : '';
                    //     } else {
                    //         $sql_and = !empty($sql) ? implode(" AND ", $sql) : '';
                    //     }

                    //     if ( $limit > 0 
                    //          && ( count($solr_result->getDocuments()) < $chunk_limit )) {
                    //         $start += (count($solr_result->getDocuments())-$solr_result->getNumFound());
                    //         $limit -= count($solr_result->getDocuments());
                    //     } else {
                    //         $start = 0;
                    //         $limit = 0;
                    //     }

                    //     $out_of_stock_solr_result = $this->executeSolrQuery( $resultset, $sql_and, $start, $limit, 'hotness_value', $data['order'] );

                    //     $product_data['product_total'] += $out_of_stock_solr_result->getNumFound();
                    //     $product_data['total'] += $out_of_stock_solr_result->getNumFound();
                    // }
                
                  $facet_resultset = array();
                  $solr_result = $this->executeSolrQuery($facet_resultset, !empty($sql) ? implode(" AND ", $sql) : '*:*', 0, 0);
                  $filter_facets = $solr_result->getData()['facets'];
                }
                
                $shuffle = false;
                if(isset($data['shuffle']) && $data['shuffle'] == false){
                    $shuffle = false;
                }

            } else {

                if(!empty($data['sort']) && !empty($data['order'])) {

                    /***Top priority**/
                    $top_priority_resultset = array();

                    if((strpos($data['sort'], 'date_added') !== false) && !empty($final_top_priority_products) && empty($data['filter_filter']) && empty($data['rating_filter']) && empty($data['price_filter'])) {
                        $top_priority_sql = $sql;
                        $top_priority_sql[] = "id:(".str_replace(',', ' OR ', $final_top_priority_products).")";
                        $top_priority_sql_and = !empty($top_priority_sql) ? implode(" AND ", $top_priority_sql) : '*:*';

                        $priority_sort = '';
                        $priority_order = '';
                        if(!empty(PRIORITY_PRODUCTS_SHUFFLE)) {
                          //Below is to get random results
                          $randString = mt_rand();
                          $priority_sort = 'random_'.$randString;
                          $priority_order = 'DESC';
                        }
                        $solr_result_top_priority = $this->executeSolrQuery($top_priority_resultset, $top_priority_sql_and, 0, 10, $priority_sort, $priority_order);
                        $top_priority_resultset = array_unique(array_filter($top_priority_resultset));
                    }
                    /*******/

                    $sort = '';
                    $order = '';
                    $sql_key = array_search('quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id, $sql);

                    if(array_search('quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id, $sql) && $this->show_out_of_stock != 0){
                        $sql_facet[$sql_key] = $sql[$sql_key] = '(*:* OR (quantity:0 OR stock_status_id:'.$out_of_stock_id.')^20000)';
                        //$sql_facet[$sql_key] = '(*:* OR (quantity:0 OR stock_status_id:'.$out_of_stock_id.')^20000)';
                    }

                    if(!empty($top_priority_resultset) && count($top_priority_resultset) > 0) {
                        $sql[] = "-id:(".implode(' OR ', $top_priority_resultset).")";
                    }

                    $sql_and = implode(' AND ', $sql);

                    if(count(explode('.', $data['sort'])) > 1) {
                        $sort = explode('.', $data['sort'])[1];
                    } else {
                        $sort = $data['sort'];
                    }
                    $this->_final_data_fetch = true;
                    $start = (isset($data['start']) && $data['start'] != '')?$data['start']:0;
                    $limit = (isset($data['limit']) && $data['limit'] != '')?$data['limit']:10;
                    $chunk_start = 0;
                    $chunk_limit = $limit;

                    $solr_result = $this->executeSolrQuery($resultset, $sql_and, $start, $limit, $sort, $data['order']);
                    $product_data['product_total'] = $solr_result->getNumFound() + count($top_priority_resultset);
                    $product_data['total'] = $solr_result->getNumFound() + count($top_priority_resultset);

                    // $in_stock_sql_key = array_search( 'quantity:[1 TO *] AND -stock_status_id:'.$out_of_stock_id, $sql );

                    // if ( $in_stock_sql_key && ( $this->show_out_of_stock != 0 )) {

                    //     $sql_facet[$in_stock_sql_key] = $sql[$in_stock_sql_key] = '(quantity:0 OR stock_status_id:' . $out_of_stock_id . ')';

                    //     if(count($resultset) > 0 && !empty($resultset[0])) {
                    //         $sql_and = !empty($sql) ? implode(" AND ", $sql).' AND -id:('.implode(" OR ", array_unique(array_filter($resultset))).')' : '';
                    //     } else {
                    //         $sql_and = !empty($sql) ? implode(" AND ", $sql) : '';
                    //     }
                        
                    //     if ( $limit > 0 
                    //          && ( count($solr_result->getDocuments()) < $chunk_limit )) {
                    //         $start += (count($solr_result->getDocuments())-$solr_result->getNumFound());
                    //         $limit -= count($solr_result->getDocuments());
                    //     } else {
                    //         $start = 0;
                    //         $limit = 0;
                    //     }

                    //     $out_of_stock_solr_result = $this->executeSolrQuery( $resultset, $sql_and, $start, $limit, $sort, $data['order'] );

                    //     $product_data['product_total'] += $out_of_stock_solr_result->getNumFound();
                    //     $product_data['total'] += $out_of_stock_solr_result->getNumFound();
                    // }
                
                    $filter_facets = $solr_result->getData()['facets'];

                    if((strpos($data['sort'], 'date_added') !== false) && $data['page'] == 1 && empty($data['filter_filter']) && empty($data['rating_filter']) && empty($data['price_filter'])) {
                        $resultset = array_merge($top_priority_resultset, $resultset);
                    }
                }
            }

            if( ( !empty( $data['facets'] ) 
                  && $data['facets'] 
                  && $data['page'] == 1 
                  && $filter_retain == 0 ) 
                || ( isset( $data['filter_only'] ) 
                     && $data['filter_only'] == 1 )
                || ( isset($data['create_page_filters']) && $data['create_page_filters'] == '1' )) {

                //*****************Code for filter facets*********************//
                $sql_facet[] = "(selling_price:[1 TO *])";
                $sql_and_facet = !empty($sql_facet) ? implode(" AND ", $sql_facet) : '*:*';

                $config_solr = $this->registry->config->solrConfig();
                // create a client instance
                $client_f = new Solarium\Client($config_solr);
                $client_f->getPlugin('postbigrequest');
                // get a select query instance
                $query_f = $client_f->createSelect();

                $query_f->setQuery($sql_and_facet);

                $last_filter_action = isset($data['last_filter_action'])?$data['last_filter_action']:'';

                $group_to_filter_name_arr = array();
                if( !empty($filter_result) ) {
                    $group_to_filter_name_arr = array_column($filter_result, 'filter_names', 'filter_group_id');
                }

                if (isset($data['facets']) && $data['facets'] == true) {

                    $filter_facets[] = $filter_facets;
                    $facets_fields = $this->_getFiltersFromFacets($filter_facets);

                    $facetSet = $query_f->getFacetSet();

                    if(!empty($facets_fields['filters'])){

                        $data['clicked_filter'] = isset($data['clicked_filter'])?$data['clicked_filter']:'';

                        $filter_array = array();
                        $filter_array = explode(',', $data['clicked_filter']);

                        $clicked_filter_id = '';
                        if($last_filter_action == 'filter'){
                            $clicked_filter_id = $filter_array[sizeof($filter_array)-1];
                        }

                        foreach ($facets_fields['filters'] as $filter_group_id => $filter_group_id_data) {

                            if ( $filter_group_id == $this->sor_filter_details['filter_group_id'] || $filter_group_id == $this->exclusive_filter_details['filter_group_id'] )
                                continue;

                            $df_query_created = 0;
                            $clicked_filter_group = '';

                            foreach ($filter_group_id_data['filter'] as $key => $filter_data) {

                                if($filter_data['filter_id'] == $clicked_filter_id){
                                    $clicked_filter_group = $filter_group_id;
                                }

                                if(in_array($filter_data['filter_id'], $filter_array) && $df_query_created == 0){

                                    $filter_facet_or = '('.str_replace(",", " OR ", $data['clicked_filter']).')';
                                    $filter_group_key = 'filter_group_df_'.$filter_group_id;
                                    $filter_facet_sql = $filter_group_key.':'.$filter_facet_or;

                                    if( in_array( $filter_group_id, SEARCHABLE_FILTER_GROUP_IDS )
                                        && !empty( $group_to_filter_name_arr[$filter_group_id] )) {

                                        $searchable_filter_facet_group_sql = 'searchable:('.str_replace(",", "* OR *", '*'.trim($group_to_filter_name_arr[$filter_group_id]).'*').')';

                                        $filter_facet_sql = '(' . $filter_facet_sql . ' OR ('. $searchable_filter_facet_group_sql .'))';
                                    }

                                    $query_f->addFilterQuery(array('key'=>'filter_group_df_'.$filter_group_id, 'query'=>$filter_facet_sql, 'tag'=>'df_'.$filter_group_id));
                                    $df_query_created++;
                                }
                            }

                            if((int) $filter_group_id == $clicked_filter_group){
                                $facetSet->createFacetField(array('key'=>'filter_group_df_'.$filter_group_id, 'field'=>'filter_group_df_'.$filter_group_id, 'exclude'=>'df_'.$filter_group_id));
                            }else{
                                $facetSet->createFacetField(array('key'=>'filter_group_df_'.$filter_group_id, 'field'=>'filter_group_df_'.$filter_group_id));
                            }
                        }
                    }

                    // Price filter faceting

                    if( isset($data['price_filter']) ) {

                        if($this->registry->currency->getCode() == 'INR'){
                            $price_values = $this->registry->currency->currencies['INR']['value'];
                        }else{
                            $price_values = $this->registry->currency->currencies['USD']['value'];
                        }

                        $price_query = '';
                        if($data['price_filter'] == ''){
                            $price_query = "(selling_price:[1 TO *])";
                        } else {

                            $price_array = explode('-', $data['price_filter']);
                            $min = $price_array[0];
                            $max = $price_array[1];

                            if($max == 0){
                                $min = $min/$price_values;
                                $price_query = "selling_price:[".$min." TO * ]";
                            }else{
                                $min = $min/$price_values;
                                $max = $max/$price_values;
                                $price_query = "selling_price:[".$min." TO ".$max."]";
                            }
                        }

                        $query_f->addFilterQuery(array('key'=>'price', 'query'=>$price_query, 'tag'=>'price'));

                        if($last_filter_action == 'price'){
                            $facetSet->createFacetField(array('key'=>'price', 'field'=>'selling_price', 'exclude'=>'price', 'limit'=>'-1'));
                        } else {
                            $facetSet->createFacetField(array('key'=>'price', 'field'=>'selling_price', 'limit'=>'-1'));
                        }
                    }

                    //Rating filter faceting

                    if( (isset($data['rating_filter'])) || $last_filter_action == 'rating'){

                        $data['rating_filter'] = isset($data['rating_filter'])?$data['rating_filter']:'';
                        $rating_query = '';

                        if($data['rating_filter'] == '') {
                            if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                                $rating_query = "(rating:[4 TO *] OR (*:* AND -rating:[* TO *]))";
                            } else {
                                $rating_query = "(rating:[* TO *] OR (*:* AND -rating:[* TO *]))";
                            }
                        } else {
                            $rating_query = '(rating:['.$data['rating_filter'].' TO *] OR (*:* AND -rating:[* TO *]))';
                        }

                        $query_f->addFilterQuery(array('key'=>'rating', 'query'=>$rating_query, 'tag'=>'rating'));

                        if($last_filter_action == 'rating') {
                            $facetSet->createFacetField(array('key'=>'rating', 'field'=>'rating', 'exclude'=>'rating'));
                        } else {
                            $facetSet->createFacetField(array('key'=>'rating', 'field'=>'rating'));
                        }
                    } else {
                        $facetSet->createFacetField(array('key'=>'rating', 'field'=>'rating'));
                    }

                    /// category filter for app

                    if(!empty($data['category_facet'])) {
                        $facetSet->createFacetField(array('key'=>'category_id', 'field'=>'category_id'));
                    }
                }
                $solr_result_f = $client_f->select($query_f);
                if(($call_from == 'app' && ( $data['filter_only'] == 1 || $data['create_page_filters'] == '1' )) || ($call_from != 'app')){
                    if(isset($solr_result_f) && !empty($solr_result_f->getFacetSet())){
                        $product_data['filter_facets'] = $this->getFilterDetailsFromFacets($solr_result_f->getFacetSet()->getFacets());

                        if ( !empty( $solr_result_f->getFacetSet()->getFacets()['price'] )
                             && !empty( $solr_result_f->getFacetSet()->getFacets()['price']->getValues() )) {

                            $current_price_raw_facets = array_filter( $solr_result_f->getFacetSet()->getFacets()['price']->getValues() );
                        }

                        if ( !empty( $product_data['filter_facets']['filters'] )) {
                            
                            // exclusive product filter
                            if ( $this->exclusive_filter_details['filter'][0]['product_count'] > 0 ) {
                                $product_data['filter_facets']['filters'] = array( $this->exclusive_filter_details['filter_group_id'] => $this->exclusive_filter_details ) 
                                                                            + $product_data['filter_facets']['filters'];
                            }
                            
                            // sor product filter
                            if ( $this->sor_filter_details['filter'][0]['product_count'] > 0 ) {
                                $product_data['filter_facets']['filters'] = array( $this->sor_filter_details['filter_group_id'] => $this->sor_filter_details ) 
                                                                            + $product_data['filter_facets']['filters'];
                            }
                            
                        } else {

                            // exclusive product filter
                            if ( $this->exclusive_filter_details['filter'][0]['product_count'] > 0 ) {
                                $product_data['filter_facets']['filters'] = array( $this->exclusive_filter_details['filter_group_id'] => $this->exclusive_filter_details );
                            }

                            // sor product filter
                            if ( $this->sor_filter_details['filter'][0]['product_count'] > 0 ) {
                                $product_data['filter_facets']['filters'] = array( $this->sor_filter_details['filter_group_id'] => $this->sor_filter_details );
                            }
                        }

                    }
                }

            }

            $product_data['products'] = array();

            if(!(isset($data['sort'])) || empty($data['sort'])){
                $return = $this->getProductFromSolrOnly($data);
                $solr_result = $return['solr_result'];

                foreach ($solr_result as $document) {
                    $resultset[] = $document->id;
                }

                $product_data['product_total'] = $solr_result->getNumFound();
                $product_data['total'] = $solr_result->getNumFound();
                $this->getProductDetails($resultset, $data, $call_from, $product_data);
                $detail_fetch = 0;
            }

            //*********Products to display according to page numbers (per page limit)
            if(isset($data['start']) && isset($data['limit'])){
                $slice_start = isset($chunk_start)?$chunk_start:$data['start'];
                $slice_limit = isset($chunk_limit)?$chunk_limit:$data['limit'];
                $resultset = array_slice($resultset, $slice_start, $slice_limit);
                if(isset($shuffle) && $shuffle == true){
                    shuffle($resultset);
                }
            }
        }

        // Getting product details from DB
        if((!empty($resultset) && $call_from != 'app') || (isset($data['filter_only']) && $data['filter_only'] != 1) || !isset($data['sort']) || ($call_from == 'app' && !isset($data['filter_only']))) {
            if(!empty($detail_fetch)) {
              $this->getProductDetails($resultset, $data, $call_from, $product_data);
            }
        }

        // Get/Show page filters
        if ( !empty( $data['page_filters'] )) {
            
            $product_data['page_filters'] = $data['page_filters'];
            $page_filters = unserialize( gzinflate( base64_decode( strtr( $data['page_filters'], '-_', '+/' ))));
        }
        
        if ( !empty( $data['create_page_filters'] ) && !empty( $product_data['filter_facets'] )) {

            if ( !empty( $current_price_raw_facets ) && !empty( $product_data['filter_facets']['price'] )) {
                $product_data['filter_facets']['price']['all_price_facets'] = $current_price_raw_facets;
            }

            $page_filters = $this->getPageFilters( $product_data['filter_facets'], $category );
            unset( $product_data['filter_facets']['price']['all_price_facets'] );
            
            if ( !empty($page_filters) ) {
                $product_data['page_filters'] = rtrim( strtr( base64_encode( gzdeflate( serialize( $page_filters ), 9 )), '+/', '-_' ), '=' );
            }
        }

        if ( !empty( $data['show_page_filters'] )) {

            $current_page_filter_key = ( $data['page'] / 2 ) - 1;

            if ( isset( $page_filters[ $current_page_filter_key ] )) {
                $product_data['current_page_filter'] = $page_filters[ $current_page_filter_key ];
            }
        }


        if($csv_req == 1){
//************** CODE for CSV (TESTING only) ***************************//
            $folder_path = '../personalization_csv';
            if($call_from == 'app'){
                $folder_path = 'personalization_csv';
            }
            if (!file_exists($folder_path)) {
                mkdir($folder_path, 7777, true);
            }
//chmod($folder_path, 0777);
            $filename = 'per_cat_user-'.$user_id;
            if(!empty($keyword)){
                $filename = 'per_search_user-'.$user_id;
            }
            if(!empty($all_requested_categories)) {
              $filename .= '_'.implode("-", $all_requested_categories);
            }
            if($call_from == 'app'){
                $filename = 'app_per_cat_user-'.$user_id;
                if(!empty($keyword)){
                    $filename = 'app_per_search_user-'.$user_id;
                }
                if(!empty($all_requested_categories)) {
                  $filename .= '_'.implode("-", $all_requested_categories);
                }
            }
            $csv_filename = $filename."_".date("Y-m-d_H-i",time()).".csv";

// create a file pointer connected to the output stream
            $file = fopen($folder_path.'/'.$csv_filename, 'w');

            $csv_data = array();
            $csv_data[] = array('User Id: ', $user_id);
            $csv_data[] = array('Page No.: ', $data['page']);
            if($data['page'] == 1){
                if(!empty($all_requested_categories)) {
                    $csv_data[] = array('Categories', implode(',', $all_requested_categories));

                }
                $csv_data[] = array('');
                $csv_data[] = array('');
                $csv_data[] = array('', '', 'Expected Counts', 'Original Counts');
                $csv_data[] = array('');
                $csv_data[] = array('Cart Pool:', '', $expected_prod_count_cart, count($final_cart_resultset));
                $csv_data[] = array('', 'WSB Store:', $expected_prod_count_wsb_store, count($resultset_wsb_store));
                $csv_data[] = array('', 'Same/Similar Seller:', $expected_prod_count_same_seller, count($resultset_same_seller));
                $csv_data[] = array('', 'Same City Seller:', $expected_prod_count_seller_same_city, count($resultset_same_city_seller));
                $csv_data[] = array('');
                $csv_data[] = array('Latest Pool:', '', $expected_prod_count_latest, count($final_latest_resultset));
                $csv_data[] = array('', 'Sort Order:', $expected_prod_count_sort_order, count($resultset_sort_order));
                $csv_data[] = array('', 'Latest:', $expected_prod_count_sub_latest, count($resultset_sub_latest));
                $csv_data[] = array('');
                $csv_data[] = array('Personalized:', '', $expected_prod_count_personalized, count($final_personalized_resultset));
                $csv_data[] = array('', 'Order:', $expected_prod_count_order, count($resultset_order_history));
                $csv_data[] = array('', 'Shortlist:', $expected_prod_count_shortlist, count($resultset_shortlist));
                $csv_data[] = array('', 'Recent:', $expected_prod_count_recent, count($resultset_recently_viewed));
                $csv_data[] = array('');
                $csv_data[] = array('');
                $csv_data[] = array('');

                $csv_data[] = array('', '', '', '', '');

// cart
                if(!empty($cart_seller_city_code)) {
                    $csv_data[] = array('Cart Pool:', '', 'Cart City - ', implode(',', array_unique($cart_seller_city_code)));

                    //wsb store
                    if(isset($solr_result_wsb_store) && !empty($solr_result_wsb_store->getDocuments())) {
                        $csv_data[] = array('', 'WSB Store:', 'Count - ', count($solr_result_wsb_store->getDocuments()), '', '', '', 'SQL:', $sql_and_wsb_store);
                        $csv_data[] = array('','', 'Product_id', 'Model', 'Seller', 'Specification', 'Data');
                        foreach ($solr_result_wsb_store->getDocuments() as $wsb_store_docs) {
                            $csv_data[] = array('', '', $wsb_store_docs->id, $wsb_store_docs->model, $wsb_store_docs->seller_id, 'hotness', $wsb_store_docs->hotness_value);
                        }
                    }else {
                        $csv_data[] = array('', 'WSB Store:', 'Count - ');
                    }
                    $csv_data[] = array('');

                    // same seller
                    if(isset($solr_result_same_seller) && !empty($solr_result_same_seller->getDocuments())) {
                        $csv_data[] = array('', 'Same Seller:', 'Count - ', count($solr_result_same_seller->getDocuments()), '', '', '', 'SQL:', $sql_and_same_seller);
                        $csv_data[] = array('','', 'Product_id', 'Model', 'Seller', 'Specification', 'Data');
                        foreach ($solr_result_same_seller->getDocuments() as $same_seller_docs) {
                            $csv_data[] = array('', '', $same_seller_docs->id, $same_seller_docs->model, $same_seller_docs->seller_id, 'hotness', $same_seller_docs->hotness_value);
                        }
                    }else {
                        $csv_data[] = array('', 'Same Seller:', 'Count - ');
                    }
                    $csv_data[] = array('');

                    // same city seller
                    if(isset($solr_result_same_city_seller) && !empty($solr_result_same_city_seller->getDocuments())) {
                        $csv_data[] = array('', 'Same City Seller:', 'Count - ', count($solr_result_same_city_seller->getDocuments()), '', '', '', 'SQL:', $sql_and_same_city_seller);
                        $csv_data[] = array('','', 'Product_id', 'Model', 'Seller', 'Specification', 'Data');
                        foreach ($solr_result_same_city_seller->getDocuments() as $same_city_seller_docs) {
                            $csv_data[] = array('', '', $same_city_seller_docs->id, $same_city_seller_docs->model, $same_city_seller_docs->seller_id, 'hotness', $same_city_seller_docs->hotness_value);
                        }
                    }else {
                        $csv_data[] = array('', 'Same City Seller:', 'Count - ');
                    }
                    $csv_data[] = array('');
                } else {
                    $csv_data[] = array('Cart Pool:', '', 'Cart City - ');
                }

                $csv_data[] = array('');

// Latest
                $csv_data[] = array('Latest Pool:', '', '');

// sort order
                if(isset($solr_result_sort_order) && !empty($solr_result_sort_order->getDocuments())) {
                    $csv_data[] = array('', 'Sort Order:', 'Count - ', count($solr_result_sort_order->getDocuments()), '', '', '', 'SQL:', $sql_and_sort_order);
                    $csv_data[] = array('','', 'Product_id', 'Model', 'Seller', 'Specification', 'Data');
                    foreach ($solr_result_sort_order->getDocuments() as $sort_order_docs) {
                        $csv_data[] = array('', '', $sort_order_docs->id, $sort_order_docs->model, $sort_order_docs->seller_id, 'sort_order', $sort_order_docs->sort_order);
                    }
                }else {
                    $csv_data[] = array('', 'Sort Order:', 'Count - ');
                }
                $csv_data[] = array('');

// sub latest
                if(isset($resultset_sub_latest) && !empty($resultset_sub_latest)) {
                    $csv_data[] = array('', 'Sub Latest:', 'Count - ', count($resultset_sub_latest), '', '', '', 'SQL:', $sql_and_sub_latest);
                    $csv_data[] = array('','', 'Product_id', 'Model', 'Seller', 'Specification', 'Data');
                    foreach ($resultset_sub_latest_for_csv as $key => $latest_docs) {
                        $csv_data[] = array('', '', $latest_docs['id'], $latest_docs['model'], $latest_docs['seller_id'], 'date_added', $latest_docs['date_added']);
                    }
                    // foreach ($solr_result_sub_latest as $seller_id => $index) {
                    //     foreach ($index as $key => $value) {
                    //       $csv_data[] = array('', '', $value->getFields()['id'], $value->getFields()['model'], $value->getFields()['seller_id'], 'date_added', $value->getFields()['date_added']);
                    //     }
                    // }
                } else {
                    $csv_data[] = array('', 'Sub Latest:', 'Count - ');
                }
                $csv_data[] = array('');

// Personalized
                $csv_data[] = array('Personalized Pool:', '', '');

// order
                $csv_data[] = array('', '', '', '', '', '', '', 'Server Order JSON:');
                if(isset($solr_result_order) && !empty($solr_result_order->getDocuments())) {
                    $csv_data[] = array('', 'Order:', 'Count - ', count($solr_result_order->getDocuments()), '', '', '', 'SQL:', $sql_and_order);
                    $csv_data[] = array('', '', '', '', '', '', '', 'Order Json:', json_encode($final_pref_arr_order));
                    if(!empty($final_pref_arr_product_review)) {
                      $csv_data[] = array('', '', '', '', '', '', '', 'Product Review Json:', json_encode($final_pref_arr_product_review));
                    }
                    $csv_data[] = array('','', 'Product_id', 'Model', 'Seller');
                    foreach ($solr_result_order->getDocuments() as $order_docs) {
                        $csv_data[] = array('', '', $order_docs->id, $order_docs->model, $order_docs->seller_id, 'hotness', $order_docs->hotness_value);
                    }
                } else {
                    $csv_data[] = array('', 'Order:', 'Count - ');
                }
                $csv_data[] = array('');

// shortlist
                if(isset($solr_result_shortlist) && !empty($solr_result_shortlist->getDocuments())) {
                    $csv_data[] = array('', 'Shortlist:', 'Count - ', count($solr_result_shortlist->getDocuments()), '', '', '', 'SQL:', $sql_and_shortlist);
                    $csv_data[] = array('', '', '', '', '', '', '', 'Shortlist Json:', json_encode($final_pref_arr_shortlist));
                    $csv_data[] = array('','', 'Product_id', 'Model', 'Seller');
                    foreach ($solr_result_shortlist->getDocuments() as $shortlist_docs) {
                        $csv_data[] = array('', '', $shortlist_docs->id, $shortlist_docs->model, $shortlist_docs->seller_id, 'hotness', $shortlist_docs->hotness_value);
                    }
                } else {
                    $csv_data[] = array('', 'Shortlist:', 'Count - ');
                }
                $csv_data[] = array('');

// recent
                if(isset($solr_result_recent) && !empty($solr_result_recent->getDocuments())) {
                    $csv_data[] = array('', 'Recent:', 'Count - ', count($solr_result_recent->getDocuments()), '', '', '', 'SQL:', $sql_and_recent);
                    $csv_data[] = array('', '', '', '', '', '', '', 'Recent Json:', json_encode($final_pref_arr_recent));
                    $csv_data[] = array('','', 'Product_id', 'Model', 'Seller');
                    foreach ($solr_result_recent->getDocuments() as $recent_docs) {
                        $csv_data[] = array('', '', $recent_docs->id, $recent_docs->model, $recent_docs->seller_id, 'hotness', $recent_docs->hotness_value);
                    }
                } else {
                    $csv_data[] = array('', 'Recent:', 'Count - ');
                }
                $csv_data[] = array('');

//shuffled pools
                $csv_data[] = array('Shuffled Subpools:');
                $csv_data[] = array('', '', '', '', '', '', '', 'Wsb Store:', implode(',', $resultset_wsb_store));
                $csv_data[] = array('', '', '', '', '', '', '', 'Same Seller:', implode(',', $resultset_same_seller));
                $csv_data[] = array('', '', '', '', '', '', '', 'Same City Seller:', implode(',', $resultset_same_city_seller));
                $csv_data[] = array('', '', '', '', '', '', '', 'Sort Order:', implode(',', $resultset_sort_order));
                $csv_data[] = array('', '', '', '', '', '', '', 'Sub Latest:', implode(',', $resultset_sub_latest));
                $csv_data[] = array('', '', '', '', '', '', '', 'Order:', implode(',', $resultset_order_history));
                $csv_data[] = array('', '', '', '', '', '', '', 'Shortlist:', implode(',', $resultset_shortlist));
                $csv_data[] = array('', '', '', '', '', '', '', 'Recent:', implode(',', $resultset_recently_viewed));
                $csv_data[] = array('');

//main pools after picking
                $csv_data[] = array('Main Pools:');
                $csv_data[] = array('', '', '', '', '', '', '', 'Cart: (ratio - 1:1:1)', implode(',', $final_cart_resultset));
                $csv_data[] = array('', '', '', '', '', '', '', 'Latest: (ratio - 1:3)', implode(',', $final_latest_resultset));
                $csv_data[] = array('', '', '', '', '', '', '', 'Personalized: (ratio - 1:1:1)', implode(',', $final_personalized_resultset));
                $csv_data[] = array('');

//Final result after picking
                $csv_data[] = array('Final Result Picking:');
                $csv_data[] = array('', '', '', '', '', '', '', 'Final: (ratio - 1:1:1)', implode(',', $final_resultset));
                $csv_data[] = array('');
            }

//Final result after picking
            $csv_data[] = array('');
            $csv_data[] = array('Page resultset:', '', '', '', '', '', '', '', implode(',', $resultset));
            $csv_data[] = array('');

// Other data
            $csv_data[] = array('');
            $csv_data[] = array('');
            $csv_data[] = array('');
            $csv_data[] = array('Page Data: ', '', '', '', '', '', '', '', json_encode($data));
            $csv_data[] = array('');

            foreach ($csv_data as $row) {
                fputcsv($file, $row);
            }
            fclose ($file);

//*************** CSV Code Ends *******************//
        }
        
        // log customer activity data
        if ( !empty( $data['sort'] ) 
             && $data['sort'] == "sort_order"
             && ( !empty( $original_keyword ) || !empty( $data['filter_category_id'] ))
             && strtolower( SITE_ENVIRONMENT ) == 'production' ) {

            try {

                $customer_activity_log_obj =  new CustomerActivityLog( $this->registry );

                $data['keyword'] = $keyword ?? "";
                $data['request_by'] = getallheaders()['REQUEST_BY'] ?? "";
                $data['session_id'] = session_id() ?? '';

                $customer_activity_log_obj->logCustomerRecentActivity( true, $data );

            } catch ( \Exception $e ) {
                return $product_data;
            }
        }

        return $product_data;

    }

    /*
    * getPoolwiseResultset: To get data after picking
    * Author: Anurag Jain
    * Date: 04 Dec 2017
    */

    private function getPoolwiseResultset($data_arr, $ratio_arr) {

        $final_resultset = array();

        if(empty($ratio_arr)){
            foreach ($data_arr as $key => $value) {
                $ratio_arr[$key] = 1;
            }
        }
        if(empty($data_arr)) {
            return $final_resultset;
        }

        while(!empty($data_arr)){
            foreach ($data_arr as $pool_name => $pool_arr) {
                if(!empty($pool_arr)) {
                    $pick_ratio = $ratio_arr[$pool_name];
                    while ($pick_ratio > 0 && !empty($data_arr[$pool_name])) {
                        $final_resultset[] = array_shift($data_arr[$pool_name]);
                        $pick_ratio--;
                    }
                    if(empty($data_arr[$pool_name])){
                        unset($data_arr[$pool_name]);
                        if(count($data_arr) == 1) {
                            foreach ($data_arr as $key => $value) {
                                $final_resultset = array_merge($final_resultset, $data_arr[$key]);
                                unset($data_arr[$key]);
                            }
                            break;
                        }
                    }
                } else {
                    unset($data_arr[$pool_name]);
                }
            }
        }
        return $final_resultset;
    }

    /*
    * getPersonalizedSQL: To get sql according to personalized data
    * Author: Anurag Jain
    * Date: 04 Dec 2017
    */

    private function getPersonalizedSQL($data, $requested_categories, $sql_for = '') {
        $model_solr_product = new model_solr_product($this->registry);
        $pref_final_seller_ids = array();
        $pref_final_seller_min_price = 0.0;
        $pref_final_seller_max_price = 0.0;

        $pref_sub_sql = array();
        if(!empty($data)){
            if($sql_for == 'product_review') {
              $seller_dislike_min_count = 2; // seller will be considered disliked if total review count is greater than $seller_dislike_min_count
              $pref_seller_sql = array();
              $included_seller = array();
              $excluded_seller = array();
              foreach ($data as $cat_id => $cat_pref_detail) {
                if((isset($cat_pref_detail['seller']) && in_array($cat_id, $requested_categories)) 
                    || (in_array('customer_preference', $requested_categories)) 
                    || (in_array('search', $requested_categories ))) {
                  foreach ($cat_pref_detail['seller'] as $seller_id => $seller_review_data) {
                    if (isset($seller_review_data['product_like_count']) && isset($seller_review_data['product_dislike_count'])) {
                      if(($seller_review_data['product_dislike_count'] > $seller_review_data['product_like_count'])
                        && ($seller_review_data['product_dislike_count']) >= $seller_dislike_min_count) {
                        if(!(in_array($seller_id, $excluded_seller))) {
                          $pref_seller_sql['exclude'][] = "-seller_id:(".$seller_id.")";
                          $excluded_seller[] = $seller_id;
                        }
                      } else {
                        if(!(in_array($seller_id, $included_seller))) {
                          $pref_seller_sql['include'][] = "(seller_id:".$seller_id.")^100000";
                          $included_seller[] = $seller_id;
                        }
                      }
                    }
                  }
                }
              }

              $pref_final_seller_sql = array();
              if(!empty($pref_seller_sql)) {
                if(!empty($pref_seller_sql['exclude'])) {
                  $pref_final_seller_sql['exclude'] = implode(' AND ', array_filter($pref_seller_sql['exclude']));
                }
                if(!empty($pref_seller_sql['include'])) {
                  $pref_final_seller_sql['include'] = implode(' OR ', array_filter($pref_seller_sql['include']));
                }
              }
              if(!empty($pref_final_seller_sql)) {
                //$pref_sub_sql[] = implode(" AND ", $pref_final_seller_sql);
                $pref_sub_sql = $pref_final_seller_sql;
              }
            } else {
              $pref_seller_sql = array();
              foreach ($data as $cat_id => $cat_pref_detail) {

                  if(in_array($cat_id, $requested_categories) 
                     || (in_array('customer_preference', $requested_categories) 
                     || in_array('search', $requested_categories ))) {
                      $seller_pref = $cat_pref_detail['seller'] ?? array();
                      $rating_pref = $cat_pref_detail['rating'] ?? array();

                      //$pref_seller_sql = array();
                      foreach ($seller_pref as $seller_id => $seller_pref_detail) {
                          $similar_seller_id_arr = array();
                          $similar_seller_id_arr[] = $seller_id;

                          //include similar seller (also use scoring to promote same seller)
                          $similar_seller = $model_solr_product->getSimilarSellers($seller_id, $cat_id);
                          if($similar_seller) {
                              foreach ($similar_seller as $key => $similar_seller_ids) {
                                  $similar_seller_arr = explode(',', $similar_seller_ids['similar_sellers']);
                                  foreach ($similar_seller_arr as $key => $value) {
                                      $similar_seller_id_arr[] = $value;
                                  }
                              }
                          }

                          // query with score boosting
                          $similar_seller_id_arr = array_unique(array_filter($similar_seller_id_arr));
                          if(!empty($similar_seller_id_arr)) {
                              $sim = implode(' OR ', $similar_seller_id_arr);
                              $pref_seller_sql[] = '(seller_id:('.$sim.'))^'.$seller_pref_detail['pieces_count'];
                          }

                          if(($seller_pref_detail['min_price'] < $pref_final_seller_min_price) || $pref_final_seller_min_price == 0.0) {
                              $pref_final_seller_min_price = $seller_pref_detail['min_price'];
                          }
                          if(($seller_pref_detail['max_price'] > $pref_final_seller_max_price)) {
                              $pref_final_seller_max_price = $seller_pref_detail['max_price'];
                          }
                      }
                      if(!empty($pref_seller_sql)){
                          $pref_sub_sql_seller = "(".implode(" OR ", $pref_seller_sql).")";
                      }
                      $pref_order_rating_sql = array();
                      foreach ($rating_pref as $rating => $rating_pref_detail) {
                          if($rating_pref_detail['pieces_count'] != 0){
                              $pref_order_rating_sql[] = 'rating:'.(int)$rating.'^'.$rating_pref_detail['pieces_count'];
                          }
                      }
                      if(!empty($pref_order_rating_sql)){
                          $pref_sub_sql_sub[] = "(".implode(" OR ", $pref_order_rating_sql).")";
                      }
                  }
              }

              //price pref_sql
              if($pref_final_seller_max_price != 0){
                  $pref_final_seller_max_price = $pref_final_seller_max_price + ceil(20/100*$pref_final_seller_max_price);
                  $pref_final_seller_min_price = $pref_final_seller_min_price - floor(20/100*$pref_final_seller_min_price);

                  if(isset($pref_final_seller_min_price) && isset($pref_final_seller_max_price) && !empty($pref_final_seller_min_price) && !empty($pref_final_seller_max_price)){
                      $pref_sub_sql_sub[] = '(selling_price:['.$pref_final_seller_min_price.' TO '.$pref_final_seller_max_price.'])^1000';
                  }
              }
              if(!empty($pref_sub_sql_seller)) {
                $pref_sql_final[] = $pref_sub_sql_seller;
              }
              if(!empty($pref_sub_sql_sub) && !empty( $pref_sub_sql_seller )) {
                $pref_sql_final[] = "(".$pref_sub_sql_seller." AND "."(".implode(' AND ', $pref_sub_sql_sub)."))";
              }
              if(!empty($pref_sql_final)) {
                $pref_sub_sql[] = "(".implode(' OR ',$pref_sql_final).")";
              }
            }
        }
        return $pref_sub_sql;
    }

    /*
    * getLatestProducts: To get Latest products (Grouped by seller)
    * Author: Anurag Jain
    * Date: 15 Nov 2017
    */

    private function getLatestProducts($data, $common_sql){

        if(isset($data['sort'])){
            $sort_arr = explode('.', $data['sort']);
            if(count($sort_arr) > 1 && $sort_arr[1] == 'date_added'){
                $sort = $sort_arr[1];
            }
        }
        
        if(empty($data['filter_category_id'])) {
          $common_sql[] = '-(*:* AND -category_id:[* TO *])';
        }
        
        $sql_and = !empty($common_sql) ? implode(" AND ", $common_sql) : '';

        $config_solr = $this->registry->config->solrConfig();
        $client = new Solarium\Client($config_solr);
        $query = $client->createSelect();

        $query->setQuery($sql_and);
        $query->setStart($data['start'])->setRows($data['limit']);

        // Grouping
        if(!empty($data['is_group']) && !empty($data['group_field']) &&  !empty($data['group_limit']) && !empty($data['group_sort_data_by']) ){
            // get grouping component and set a field to group by
            $groupComponent = $query->getGrouping();
            $groupComponent->addField($data['group_field']);
            // maximum number of items per group
            $groupComponent->setLimit($data['group_limit']);
            // sort by in groups
            $query->addSort( $data['group_sort_data_by'], $query::SORT_DESC);
            // get a group count
            $groupComponent->setNumberOfGroups(true);
            $solr_result = $client->select($query);
            $groups = $solr_result->getGrouping();

            $group_result_arr = array();
            foreach ($groups as $groupKey => $fieldGroup) {
                foreach ($fieldGroup as $valueGroup) {
                    if(!empty($data['group_get_p_id']) && $data['group_get_p_id'] == 1 ){
                        foreach ($valueGroup as $document) {
                            foreach ($document as $field => $value) {
                                if($field == 'id') {
                                    $group_result_arr[] = $value;
                                }
                            }
                        }
                    } else {
                        $group_result_arr[(int)$valueGroup->getValue()] = $valueGroup->getDocuments();
                    }
                }
            }
        } else {

            $start = $data['start'] ?? 0;
            $limit = $data['limit'] ?? 10;
            $query->setStart($start)->setRows($limit);

            $sort_by = $data['sort'] ?? 'date_added';
            $sort_order = $data['order'] ?? 'DESC';
            // add sorting
            if(!empty($sort_by) && !empty($sort_order)) {
                if(strtoupper($sort_order) == 'DESC'){
                    $query->addSort($sort_by, $query::SORT_DESC);
                } else {
                    $query->addSort($sort_by, $query::SORT_ASC);
                }
            }
            
            $solr_result = $client->select($query);
            $this->product_count = $solr_result->getNumFound();
            // format result
            foreach ($solr_result as $document) {
                $group_result_arr[] = $document->id;
            }
        }

        return $group_result_arr;
    }

    /*
    * getTrendingProducts: To get Trending products
    * Author: Anurag Jain
    * Date: 15 Nov 2017
    */

    private function getTrendingProducts($data, $common_sql){

        $common_sql[] = 'date_added:[NOW/DAY-'.$data['days'].'DAY TO *]';
        $sort = $data['sort'];
        
        if(empty($data['filter_category_id'])) {
          $common_sql[] = '-(*:* AND -category_id:[* TO *])';
        }
        
        $sql_and = !empty($common_sql) ? implode(" AND ", $common_sql) : '';

        $config_solr = $this->registry->config->solrConfig();
        $client = new Solarium\Client($config_solr);
        $query = $client->createSelect();

        $query->setQuery($sql_and);
        $query->setStart($data['start'])->setRows($data['limit']);

        // Grouping
        if(!empty($data['is_group']) && !empty($data['group_field']) &&  !empty($data['group_limit']) && !empty($data['group_sort_data_by']) ){
            // get grouping component and set a field to group by
            $groupComponent = $query->getGrouping();
            $groupComponent->addField($data['group_field']);
            // maximum number of items per group
            $groupComponent->setLimit($data['group_limit']);
            // sort by in groups
            $query->addSort( $data['group_sort_data_by'], $query::SORT_DESC);
            // get a group count
            $groupComponent->setNumberOfGroups(true);
            $solr_result = $client->select($query);
            $groups = $solr_result->getGrouping();

            $group_result_arr = array();
            foreach ($groups as $groupKey => $fieldGroup) {
                foreach ($fieldGroup as $valueGroup) {
                    if(!empty($data['group_get_p_id']) && $data['group_get_p_id'] == 1 ){
                        foreach ($valueGroup as $document) {
                            foreach ($document as $field => $value) {
                                if($field == 'id') {
                                    $group_result_arr[] = $value;
                                }
                            }
                        }
                    } else {
                        $group_result_arr[(int)$valueGroup->getValue()] = $valueGroup->getDocuments();
                    }
                }
            }
        }

        return $group_result_arr;

        // $query->addSort($sort, $query::SORT_DESC);
        // $solr_result = $client->select($query);
        // return $solr_result;
    }

    /*
    * executeSolrQuery: common function to execute solr query
    * Author: Anurag Jain
    * Date: 15 Nov 2017
    */

    private function executeSolrQuery(&$resultset, $sql_and = '*:*', $start = 0, $limit = 10, $sort_by = '', $sort_order = '', $group_data = array()) {
        
        if ( strtolower( $sort_by ) == "order_id" ) {
            $sort_by = "hotness_value";
            $sort_order = "DESC";
        }

        $sort_by = trim($sort_by);
        $sort_order = trim($sort_order);

        // Get solr configuration
        $config_solr = $this->registry->config->solrConfig();

        // Get solr client instance
        $client = new Solarium\Client($config_solr);
        $client->getPlugin('postbigrequest');
        // Get select
        $query = $client->createSelect();

        // set query
        if(!empty($group_data['latest_query_count']) && $sort_by == 'date_added') {
          switch ($group_data['latest_query_count']) {
            case 1:
              $sql_and .= ' AND date_added:[NOW/DAY-15DAY TO *]';
              break;
            case 2:
              $sql_and .= ' AND date_added:[NOW/DAY-30DAY TO *]';
              break;
            default:
              $sql_and .= '';
              break;
          }
        }
        $query->setQuery($sql_and);

        // set start and rows/limit
        $start = abs( (int) $start );
        $limit = abs( (int) $limit );
        $query->setStart($start)->setRows($limit);
        if($this->show_out_of_stock == 1 && $this->_final_data_fetch){
            $query->addSort('score', $query::SORT_ASC);
        }

        // add sorting
        if(!empty($sort_by) && !empty($sort_order)) {
            if(strtoupper($sort_order) == 'DESC'){
                $query->addSort($sort_by, $query::SORT_DESC);
            } else {
                $query->addSort($sort_by, $query::SORT_ASC);
            }
        }

        $facet_json = $this->_getJsoNFacetStringForSolrRequest();
        $customizer = $client->getPlugin('customizerequest');

        // add a GET param thats only used for a single request (the default setting is no persistence)
        $customizer->createCustomization('json.facet')
            ->setType('param')
            ->setName('json.facet')
            ->setValue($facet_json);

        if( isset($this->_request_data['filter_special']) && !empty( trim($this->_request_data['filter_special']) ) ) {
            $query->createFilterQuery('valid_special_price')->setQuery("{!frange l=1}sub(selling_price,special_price)");
        }

        if(!empty($group_data)) {
          // Grouping
          if(!empty($group_data['is_group']) && !empty($group_data['group_field']) &&  !empty($group_data['group_limit']) && !empty($group_data['group_sort_data_by']) ){
              // get grouping component and set a field to group by
              $groupComponent = $query->getGrouping();
              $groupComponent->addField($group_data['group_field']);
              // maximum number of items per group
              $groupComponent->setLimit($group_data['group_limit']);
              // sort by in groups
              //Below is to get random results
              $randString = mt_rand();
              $group_sort = 'random_'.$randString;
              $groupComponent->setSort( $group_sort." desc");
              // get a group count
              $groupComponent->setNumberOfGroups(true);
              $solr_result = $client->select($query);
              $groups = $solr_result->getGrouping();
              $group_result_arr = array();
              foreach ($groups as $groupKey => $fieldGroup) {
                  foreach ($fieldGroup as $valueGroup) {
                      if(!empty($group_data['group_get_p_id']) && $group_data['group_get_p_id'] == 1 ){
                          foreach ($valueGroup as $document) {
                              foreach ($document as $field => $value) {
                                  if($field == 'id') {
                                      $group_result_arr[] = $value;
                                  }
                              }
                          }
                      } else {
                          $group_result_arr[(int)$valueGroup->getValue()] = $valueGroup->getDocuments();
                      }
                  }
              }
          }
          return $group_result_arr;
        }
        // execute query
        $solr_result = $client->select($query);

        // format result
        foreach ($solr_result as $document) {
            $resultset[] = $document->id;
        }

        return $solr_result;
    }

    // private $_init_priority_prod = array();     // Variable to store products whose sort_order is set to higher priority than rest
    // private $_init_random_prod = array();       // Variable to store initial hand picked random products for the initial pages

    // private $_merge_handpicked = false;         // Defaulted to false. only time set to true when we are at the first page of handpicked

    //  Loop states for getting handpicked product ids
    //  * 0 - First page; Getting products where sort order < 999
    //  * 1 - First page; Getting randomized products from last 90 days and sort order >= 999
    //  * 2 - First page; Getting outside 90 days area products randomized and sort order >= 999

    // private $_hand_picked_loop_state = 0; // Defaulting to 0 ensures that we get into the loop of sort first time atleast

    // private $_product_total = 0; // This is needed to be stored for handling handpicked products as products are determined in parts.
    // // Also, for page 2 to $num_hpick_pages, we dont use solr search to get products


    /**
     * Get product from solr
     * @param $data
     * @return array of arrays
     *  - 'products' or 'data' : array of arrays of various product ids with details
     *  - 'product_total' or 'total': total number of products possible as per filter data
     *  - 'handpicked_ids': List of handpicked ids in case sort is sort_order or undefined or empty. Key is only found when in first five pages
     *  - 'random_string': Random seed for random sort to be used in next page. Only available if random key is generated and required
     *  -
     */

    // public function getProductFromSolrOLD($data, $backend = 0) {
    //     // For handpicked case, number of pages which we want to control
    //     $num_hpick_pages = 5;

    //     //will have filters matched to search results
    //     $filter_facets = '';

    //     // Intializing return array
    //     $product_data = array();

    //     $resultset = array();
    //     //Set page = 1 if it is not set, useful for related products
    //     if(empty($data['page'])){
    //         $data['page'] = 1;
    //     }

    //     // Check whether app or web from which we have getproducts request
    //     $call_from = '';
    //     if ( !empty($data['call_from']) ) {
    //         $call_from = strtolower(trim($data['call_from']));
    //     }

    //     // Random seed - Use already existing one (if exists) to avoid duplicity for subsequent pages
    //     if ( !empty($data['random_string']) ) {
    //         $random_string = trim($data['random_string']);
    //     } else {
    //         $random_string = time();
    //     }
    //     $product_data['random_string'] = $random_string;

    //     // If we are on the handpicked products pages 2 to $num_hpick_pages and we have product_ids to show from
    //     if ( isset($data['sort'])
    //          && (empty(trim($data['sort']))
    //              || $data['sort'] == 'sort_order'
    //              || $data['sort'] == 'undefined')
    //          && isset($data['page'])
    //          && (int)$data['page'] >= 2
    //          && (int)$data['page'] <= $num_hpick_pages
    //          && !empty($data['handpicked_ids'])
    //          && !empty($data['limit'])
    //          && ( isset($data['product_total'])
    //               || isset($data['total']) ) ) {

    //         $handpicked_ids = gzinflate(base64_decode(strtr($data['handpicked_ids'], '-_', '+/')));
    //         $handpicked_ids = explode("," , $handpicked_ids);

    //         if ( count($handpicked_ids) > (($data['page']-1)*$data['limit']) ) {
    //             $resultset = array_slice($handpicked_ids, ($data['page']-1)*$data['limit'], $data['limit']);

    //             $product_data['handpicked_ids'] = $data['handpicked_ids'];

    //             $this->_product_total = isset($data['product_total']) ? (int)$data['_product_total'] : (int)$data['total'];
    //             $data['facets'] = false;
    //             goto SKIP_SOLR;
    //         }
    //     }

    //     // Checking we have valid start and limit values. This variables are later modified based on required sorting
    //     // So we ensure that we dont change the original data fields
    //     $sort_start = isset($data['start']) ? (int)$data['start'] : 0;
    //     $sort_limit = !empty($data['limit']) ? (int)$data['limit'] : 60;

    //     $new_data = $data;
    //     $config_solr = $this->registry->config->solrConfig();

    //     // create a client instance
    //     $client = new Solarium\Client($config_solr);

    //     // get a select query instance
    //     $query = $client->createSelect();

    //     if( $backend == 1 ) {
    //         $data['status']     = 0;
    //         $data['quantity']   = 0;
    //         $data['is_single']  = 0;
    //         $data['wsb_store']  = 0;
    //         $data['backend']    = 0;
    //         if(!empty($data['sort'])) {
    //             $sort = explode(".", $data['sort']);
    //             $data['sort'] = $sort[1];
    //         }
    //     }

    //     $return = $this->getProductFromSolrOnly($data);
    //     $solr_result = $return['solr_result'];
    //     $sql_and = $return['sql_and'];

    //     if(isset($return['facets'])){
    //         $filter_facets = $return['facets'];
    //     }
    //     $this->_product_total = $solr_result->getNumFound();

    //     $resultset = array();
    //     if(isset($return['result_group_field'])){
    //         if(!empty($return['result_group_field'])) {
    //             //print_r($return['result_group_field']);
    //             foreach ($return['result_group_field'] as $seller_id => $index) {
    //                 foreach ($index as $key => $value) {
    //                     $resultset[] = $value->getFields()['id'];
    //                 }
    //             }
    //         }
    //     }

    //     // If sorting is specified by the controller
    //     while ( isset($data['sort'])
    //             && $this->_hand_picked_loop_state >= 0
    //             && $this->_hand_picked_loop_state <= 3 && (!(isset($data['is_latest'])))) {

    //         $sort_sql = array(); // Customized sql for various sort conditions
    //         $sort = '';
    //         $data['sort'] = trim($data['sort']);

    //         //Cases other than the Handpicked products where data['sort'] == 'sort_order'
    //         if ( !empty($data['sort']) && $data['sort'] != 'undefined' &&  $data['sort'] != 'random' && $data['sort'] != 'sort_order' ) {
    //             //break from . as parameter has table alias
    //             $arr_sort = explode(".", $data['sort']);

    //             if ( count($arr_sort) > 1 ) {
    //                 $sort = $arr_sort[1];
    //             } else {
    //                 $sort = $data['sort'];
    //             }

    //             $this->_hand_picked_loop_state = -1; // We set this state so that it does not loop again

    //         } else if ( empty($data['sort']) or $data['sort'] == 'sort_order' or $data['sort'] == 'undefined' ) { // Handpicked case

    //             // If we are loop status of 0 and the page number is also 1
    //             if ( $this->_hand_picked_loop_state == 0
    //                 and isset($data['page']) and $data['page'] == 1 ) {

    //                 // WHERE condition to account for only those products where sort_order is < 999
    //                 $sort_sql[] = 'sort_order:[* TO 998]';

    //                 // Sorting needs to be sort_order ASC
    //                 $sort = 'sort_order';
    //                 $data['order'] = 'ASC';

    //                 // We need to get data for first $num_hpick_pages pages
    //                 $sort_start = 0;
    //                 $sort_limit = $num_hpick_pages * $data['limit'];

    //             } else if ( $this->_hand_picked_loop_state == 1
    //                 and isset($data['page']) and $data['page'] == 1 ) { // Determining the next 90 days products

    //                 $sort_sql[] = 'sort_order:[999 TO *]'; // We need to avoid getting the priority products again
    //                 $sort_sql[] = "date_added:[NOW/DAY-90DAY TO *]"; // Getting the last 90 day products

    //                 // Sorting needs to be random
    //                 $sort = 'random_' . time();

    //                 // Determining shortfall
    //                 $sort_start = 0;
    //                 $sort_limit = floor($num_hpick_pages * $data['limit'] / 2) - count($this->_init_priority_prod);

    //             } else if ( $this->_hand_picked_loop_state == 2
    //                 and isset($data['page']) and $data['page'] == 1 ) { // Determining the next 90 days products

    //                 $sort_sql[] = 'sort_order:[999 TO *]'; // We need to avoid getting the priority products again
    //                 $sort_sql[] = "date_added:[* TO NOW/DAY-90DAY]"; // Getting the products beyond last 90 days

    //                 // Sorting needs to be random
    //                 $sort = 'random_' . time();

    //                 // Determining shortfall
    //                 $sort_start = 0;
    //                 $sort_limit = floor($num_hpick_pages * $data['limit'] / 2) - count($this->_init_priority_prod) - count($this->_init_random_prod);

    //             } else if ( $this->_hand_picked_loop_state == 3
    //                 and isset($data['page']) and $data['page'] == 1 ) { // Determining the balance 150 products based on hotness factor

    //                 // Ids determined using handpicked loops must not be coming in the hotness search result
    //                 $arr_skip_ids = array_merge($this->_init_priority_prod, $this->_init_random_prod);
    //                 $sort_sql[] = ' -id:('. implode(' OR ', $arr_skip_ids ).')';

    //                 // Sorting needs to be random
    //                 $sort = 'hotness_value';
    //                 // This will be last loop so we can afford to set sort desc. previous loops will run with sort asc
    //                 $data['order'] = 'desc';

    //                 // If we are coming here, it means there are more products to show than handpicked ids
    //                 $sort_start = 0;
    //                 $sort_limit = ($num_hpick_pages * $data['limit']) - floor($num_hpick_pages * $data['limit'] / 2);

    //             } else {

    //                 // We will need to skip the handpicked products from first five pages
    //                 if ( !empty($data['handpicked_ids']) ) {
    //                     $arr_skip_ids = explode("," , gzinflate(base64_decode(strtr($data['handpicked_ids'], '-_', '+/'))) );
    //                     $sort_sql[] = ' -id:('. implode(' OR ', $arr_skip_ids ).')';

    //                     // Returning back the handpicked ids
    //                     $product_data['handpicked_ids'] = $data['handpicked_ids'];
    //                 }else{
    //                     //Setting it 0 so pagination works well in legacy mode of app where we do not pass handpick ID's
    //                     $num_hpick_pages = 0;
    //                 }

    //                 $sort = 'random_'.$random_string;

    //                 // Ensuring that start and limit are correct.
    //                 $sort_limit = !empty($data['limit']) ? (int)$data['limit'] : 60;

    //                 $sort_start =  $sort_limit * ((int)$data['page'] - ($num_hpick_pages+1)) ; // Start counter has to be reset from ($num_hpick_pages+1)th page


    //                 $this->_hand_picked_loop_state = -1; // We set this state so that it does not loop again
    //             }

    //         } else {

    //             $sort = 'random_'.$random_string;

    //             $this->_hand_picked_loop_state = -1; // We set this state so that it does not loop again
    //         }


    //         // Joining all WHERE queries under AND conditions
    //         $sort_sql_and = !empty($sort_sql) ? ' AND ' . implode(" AND ", $sort_sql) : '';
    //         $query->setQuery( $sql_and . ' ' . $sort_sql_and );

    //         if ($sort_start < 0) {
    //             $sort_start = 0;
    //         }
    //         if ($sort_limit < 0) {
    //             $sort_limit = 0;
    //         }

    //         $query->clearSorts();

    //         $query->setStart($sort_start)->setRows($sort_limit);

    //         $data['order'] = trim($data['order']);
    //         if(isset($data['order']) && strtolower($data['order']) == 'asc' && $data['order'] != 'undefined') {

    //             $query->addSort( $sort, $query::SORT_ASC);
    //         }else{

    //             $query->addSort( $sort, $query::SORT_DESC);

    //         }

    //         $solr_result = $client->select($query);

    //         // Now check the handpicked loop state, and accordingly pass the result set
    //         if ( $this->_hand_picked_loop_state >= 0 and $this->_hand_picked_loop_state <= 3 ) {

    //             foreach ($solr_result as $document) {

    //                 if ( $this->_hand_picked_loop_state == 0 )
    //                     $this->_init_priority_prod[] = $document->id; // Fill priority (sort order < 999) array
    //                 else // 1,2 and 3
    //                     $this->_init_random_prod[] = $document->id; // Fill random prod array
    //             }

    //             // If we are in loop state 0, 1, or 2
    //             if ( $this->_hand_picked_loop_state <= 2 ) {
    //                 // Logically only > = 0 are possible. If = 0 reached, that means need to jump to hotness loop
    //                 if ( ( floor($num_hpick_pages*$data['limit']/2) - count($this->_init_priority_prod) - count($this->_init_random_prod) ) == 0 ) {
    //                     $this->_hand_picked_loop_state = 3;
    //                 } else {
    //                     // It is still > 0 that means handpicked array is not full. yet we are on final stage of this loop
    //                     // this means there are no more products to show, so no point of going to hotness loop, hence break
    //                     if ( $this->_hand_picked_loop_state == 2 ) {
    //                         $this->_hand_picked_loop_state = -1;
    //                     } else { // still in handpicked loop and need to move to next step to get remaining products
    //                         $this->_hand_picked_loop_state += 1;
    //                     }
    //                 }
    //             } else {
    //                 // we are at loop state 3, time to break
    //                 $this->_hand_picked_loop_state = -1;
    //             }

    //             $this->_merge_handpicked = true;

    //         }
    //         //$check_keyword = $data['filter_name'];
    //         // display the total number of documents found by solr
    //         if (!empty($check_keyword)) {
    //             $has_results = $solr_result->getNumFound();
    //             if ($has_results != 0) {
    //                 $has_results = 1;
    //             } else {
    //                 $has_results = 0;
    //             }
    //             $solar_product = new model_solr_product($this);
    //             $solar_product->setSearchedKeyword($check_keyword, $has_results);
    //         }

    //     }

    //     if ( $this->_merge_handpicked ) {

    //         // mergings arrays
    //         $this->_init_priority_prod = array_merge($this->_init_priority_prod, $this->_init_random_prod);

    //         // Create first page array
    //         $resultset = array_slice($this->_init_priority_prod, 0, (int)$data['limit']);
    //         shuffle($resultset);

    //         $product_data['handpicked_ids'] = rtrim(strtr(base64_encode(gzdeflate(implode(',', $this->_init_priority_prod), 9)), '+/', '-_'), '=');

    //     } else { // not in first five pages of handpicked products. page 1 case is handled above and page 2-$num_hpick_pages at the very beginning of this function
    //         if((!(isset($data['is_latest'])))) {
    //             foreach ($solr_result as $document) {
    //                 $resultset[] = $document->id;
    //             }
    //         }
    //     }
    //     if(!empty($data['is_trending'])){
    //         shuffle($resultset);
    //         $resultset = array_slice($resultset, 0, 10);
    //     }

    //     //*****************Code for filter facets*********************//

    //     $sql_and_facet = $return['sql_and_facet'];

    //     // create a client instance
    //     $client_f = new Solarium\Client($config_solr);

    //     // get a select query instance
    //     $query_f = $client_f->createSelect();

    //     $query_f->setQuery($sql_and_facet);

    //     $last_filter_action = isset($data['last_filter_action'])?$data['last_filter_action']:'';
    //     //$last_filter_action = 'stock';

    //     if (isset($data['facets']) && $data['facets'] == true) {

    //         $filter_facets[] = $filter_facets;
    //         $facets_fields = $this->_getFiltersFromFacets($filter_facets);

    //         $facetSet = $query_f->getFacetSet();

    //         if(!empty($facets_fields['filters'])){

    //             $data['clicked_filter'] = isset($data['clicked_filter'])?$data['clicked_filter']:'';
    //             $filter_array = array();
    //             $filter_array = explode(',', $data['clicked_filter']);

    //             $clicked_filter_id = '';
    //             if($last_filter_action == 'filter'){
    //                 $clicked_filter_id = $filter_array[sizeof($filter_array)-1];
    //             }

    //             foreach ($facets_fields['filters'] as $filter_group_id => $filter_group_id_data) {

    //                 $df_query_created = 0;
    //                 $clicked_filter_group = '';

    //                 foreach ($filter_group_id_data['filter'] as $key => $filter_data) {

    //                     if($filter_data['filter_id'] == $clicked_filter_id){
    //                         $clicked_filter_group = $filter_group_id;
    //                     }
    //                     if(in_array($filter_data['filter_id'], $filter_array) && $df_query_created == 0){
    //                         $query_f->addFilterQuery(array('key'=>'filter_group_df_'.$filter_group_id, 'query'=>'filter_group_df_'.$filter_group_id.':('.str_replace(',',' OR ',$data['clicked_filter']).')', 'tag'=>'df_'.$filter_group_id));
    //                         $df_query_created++;
    //                     }
    //                 }

    //                 if((int) $filter_group_id == $clicked_filter_group){
    //                     $facetSet->createFacetField(array('key'=>'filter_group_df_'.$filter_group_id, 'field'=>'filter_group_df_'.$filter_group_id, 'exclude'=>'df_'.$filter_group_id));
    //                 }else{
    //                     $facetSet->createFacetField(array('key'=>'filter_group_df_'.$filter_group_id, 'field'=>'filter_group_df_'.$filter_group_id));
    //                 }
    //             }
    //         }

    //         // Price filter faceting

    //         if( isset($data['price_filter']) ) {

    //             $price_query = '';
    //             if($data['price_filter'] == ''){
    //                 $price_query = "(selling_price:[1 TO *])";
    //             } else {
    //                 $price_array = explode('-', $data['price_filter']);
    //                 $min = $price_array[0];
    //                 $max = $price_array[1];
    //                 $price_query = "selling_price:[".$min." TO ".$max."]";
    //             }

    //             $query_f->addFilterQuery(array('key'=>'price', 'query'=>$price_query, 'tag'=>'price'));

    //             if($last_filter_action == 'price'){
    //                 $facetSet->createFacetField(array('key'=>'price', 'field'=>'selling_price', 'exclude'=>'price', 'limit'=>'-1'));
    //             } else {
    //                 $facetSet->createFacetField(array('key'=>'price', 'field'=>'selling_price', 'limit'=>'-1'));
    //             }
    //         }

    //         //Rating filter faceting

    //         if( (isset($data['rating_filter'])) || $last_filter_action == 'rating'){

    //             $data['rating_filter'] = isset($data['rating_filter'])?$data['rating_filter']:'';
    //             $rating_query = '';

    //             if($data['rating_filter'] == '') {
    //                 $rating_query = "(rating:[* TO *] OR (*:* -rating:[* TO *]))";
    //             } else {
    //                 $rating_query = 'rating:['.$data['rating_filter'].' TO *]';
    //             }

    //             $query_f->addFilterQuery(array('key'=>'rating', 'query'=>$rating_query, 'tag'=>'rating'));

    //             if($last_filter_action == 'rating') {
    //                 $facetSet->createFacetField(array('key'=>'rating', 'field'=>'rating', 'exclude'=>'rating'));
    //             } else {
    //                 $facetSet->createFacetField(array('key'=>'rating', 'field'=>'rating'));
    //             }
    //         } else {
    //             $facetSet->createFacetField(array('key'=>'rating', 'field'=>'rating'));
    //         }

    //     }

    //     $solr_result_f = $client_f->select($query_f);

    //     SKIP_SOLR:

    //     // Getting product details
    //     $this->getProductDetails($resultset, $data, $call_from, $product_data);


    //     // Handling the case when we dont have any data for trending products in the last 90 days
    //     // Then we increment the area of search by further 90 days (only for one time) and so on...
    //     $data['product_not_found_loop_count'] = isset($data['product_not_found_loop_count'])?$data['product_not_found_loop_count']:0;

    //     if(!empty($data['days']) && $data['product_not_found_loop_count'] < 1){
    //         if(empty($product_data['products'])){
    //             $new_data['days'] = $data['days'] + 90;
    //             $new_data['product_not_found_loop_count'] = $data['product_not_found_loop_count'] + 1;
    //             $product_data = $this->getProductFromSolr($new_data);
    //         }
    //     }

    //     if(isset($solr_result_f) && !empty($solr_result_f->getFacetSet())){
    //         $product_data['filter_facets'] = $this->getFilterDetailsFromFacets($solr_result_f->getFacetSet()->getFacets());
    //     } else {
    //         if (isset($data['facets']) && $data['facets'] == true) {
    //             $product_data['filter_facets'] = $this->_getFiltersFromFacets($filter_facets);
    //         }
    //     }
    //     return $product_data;
    // }

    /*
     * This function is being called in solr search, receive filter facets from solr result set and
     * convert those in array which we will use on search and category page
     * @param $filter_facets array
     * @return  $filter_list array
     */
    private function _getFiltersFromFacets($filter_facets) {

        $solar_product = new model_solr_product($this->registry);

        //get filter groups and then remove those groups which has empty array in filter_facet
        $filter_groups = $solar_product->getAllFilterGroupsFromDB();

        $dynamic_field_slug = 'filter_group_df_';
        $filter_list = array();

        // sor_type facets
        if ( isset( $filter_facets['sor_type'] ) && isset( $filter_facets['sor_type']['buckets'] )) {
            
            $sor_type_count = 0;
            
            foreach( $filter_facets['sor_type']['buckets'] as $sor_type_bucket ) {

                $sor_type_count += $sor_type_bucket['count'];
            }

            if ( $sor_type_count > 0 ) {
                $this->sor_filter_details['filter'][0]['product_count'] = $sor_type_count;
                $filter_list['filters'][ $this->sor_filter_details['filter_group_id'] ] = $this->sor_filter_details;
            }
        }

        // exclusive facets
        if ( isset( $filter_facets['exclusive'] ) && isset( $filter_facets['exclusive']['buckets'] )) {

            $exclusive_count = 0;
            foreach( $filter_facets['exclusive']['buckets'] as $exclusive_bucket ) {

                if ( ( $exclusive_bucket['val'] == 'both' || $exclusive_bucket['val'] == 'exclusive' )) {
                    $exclusive_count += $exclusive_bucket['count'];
                }
            }

            if ( $exclusive_count > 0 ) {
                $this->exclusive_filter_details['filter'][0]['product_count'] = $exclusive_count;
                $filter_list['filters'][ $this->exclusive_filter_details['filter_group_id'] ] = $this->exclusive_filter_details;
            }
        }

        if ( count($filter_groups ) > 0) {

            foreach ($filter_groups as $filter_group) {

                //Star rating
                if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                    $filter_list['rating'] = array('5','4');
                }
                else{
                    $filter_list['rating'] = array('5','4','3');
                }

                //Stock status filter
                /*
                $filter_list['stock_filters'] = array(
                    'In Stock' => 0, //its 0, because in query we have variable show_out_of_stock = 0
                    'All' => 1
                );
                */
                //make dynamic field name
                $dynamic_field = $dynamic_field_slug.$filter_group['filter_group_id'];
                if (array_key_exists($dynamic_field, $filter_facets) && count($filter_facets[$dynamic_field]['buckets']) > 0 && !($filter_group['filter_group_id'] == 11)) {

                    $filter_list['filters'][$filter_group['filter_group_id']]['filter_group_id'] = $filter_group['filter_group_id'];
                    $filter_list['filters'][$filter_group['filter_group_id']]['group_label'] =  $filter_group['name'];
                    $filter_list['filters'][$filter_group['filter_group_id']]['group_description'] =  $filter_group['description'];

                    foreach ($filter_facets[$dynamic_field]['buckets'] as $bucket) {
                        //some id as comma, we have to check this issue in SOLR, here we must get number
                        $filter_id = explode(",", $bucket['val'])[0];
                        $product_count = $bucket['count'];

                        $filter_info = $this->getFilterDetail($filter_id);
                        
                        if ( isset($filter_info['image']) ) {
                            $this->registry->load->model('tool/image');
                            $image_url = $this->registry->model_tool_image->resize( $filter_info['image'], 100, 100 );
                        }
                        
                        $filter_list['filters'][$filter_group['filter_group_id']]['filter'][] = array(
                            'name' => $filter_info['name'] ?? "",
                            'image' => $image_url ?? "",
                            'filter_id' => $filter_id,
                            'product_count' => $product_count
                        );

                    }

                }
            }
        }
        return $filter_list;

    }

    public function getFilterDetailsFromFacets($arr_facets){

        $filter_list = array();
        // category
        if(array_key_exists('category_id', $arr_facets)) {

            $this->registry->load->model('catalog/category');
            $cat_arr = array();
            foreach ($arr_facets['category_id']->getValues() as $key => $value) {
                if((int)$value > 0) {
                  $cat_detail = $this->registry->model_catalog_category->getCategory($key);
                  if(!empty($cat_detail['name'])) {
                    $cat_name = $cat_detail['name'];
                    $cat_arr[$key] = array(
                                    'label' => $cat_name,
                                    'count'=> $value
                                    );
                  }
                }
            }
            if(!empty($cat_arr)) {
                $filter_list['category'] = $cat_arr;
            }
        }

         //Star rating
        if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
            $filter_list['rating'] = array('5','4');
        }
        else{
            $filter_list['rating'] = array('5','4','3');
        }

        $solar_product = new model_solr_product($this->registry);

        //get filter groups and then remove those groups which has empty array in filter_facet
        $filter_groups = $solar_product->getAllFilterGroupsFromDB();

        $dynamic_field_slug = 'filter_group_df_';

        if ( count($filter_groups ) > 0) {
            foreach ($filter_groups as $filter_group) {
                $dynamic_field = $dynamic_field_slug.$filter_group['filter_group_id'];
                if (array_key_exists($dynamic_field, $arr_facets)) {

                    $filter_list['filters'][$filter_group['filter_group_id']]['filter_group_id'] = $filter_group['filter_group_id'];
                    $filter_list['filters'][$filter_group['filter_group_id']]['group_label'] =  $filter_group['name'];
                    $filter_list['filters'][$filter_group['filter_group_id']]['group_description'] =  $filter_group['description'];

                    $filters_array = $arr_facets[$dynamic_field]->getValues();
                    foreach ($filters_array as $filter_id => $count) {

                        $filter_id_exploded = explode(",", $filter_id);
                        if(sizeof($filter_id_exploded) == 1) {
                          $product_count = $count;

                          $filter_info = $this->getFilterDetail($filter_id);

                          if ( isset($filter_info['image']) ) {
                            $this->registry->load->model('tool/image');
                            $image_url = $this->registry->model_tool_image->resize( $filter_info['image'],100,100 );
                          }

                          $filter_list['filters'][$filter_group['filter_group_id']]['filter'][] = array(
                              'name' => $filter_info['name'] ?? "",
                              'image' => $image_url ?? "",
                              'filter_id' => $filter_id,
                              'product_count' => $product_count
                          );
                      }

                    }
                }
            }
        }

        if(array_key_exists('price', $arr_facets)) {

            if(!empty(array_filter($arr_facets['price']->getValues()))) {
                $min_price = min(array_keys(array_filter($arr_facets['price']->getValues())));
                $max_price = max(array_keys(array_filter($arr_facets['price']->getValues())));
            } else {
                $min_price = 0.0;
                $max_price = 0.0;
            }

            if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
               $min_price = $this->registry->currency->convert($min_price,'INR',$this->registry->currency->getCode());
               $max_price = $this->registry->currency->convert($max_price,'INR',$this->registry->currency->getCode());
           }

            $filter_list['price'] = array($min_price, $max_price) ;
        }

        if(array_key_exists('rating', $arr_facets)) {
            $filter_list['rating'] = $arr_facets['rating']->getValues();
        }

        return $filter_list;
    }


    /**
     * Getting product details.
     * This function returns product details, given the resultSet of product_ids
     * from SOLR run. It generates info as needed for the medium we are currently
     * looking at, which are, web or app.
     * It also handles the case of handpicked designs, when we have product ids for multiple
     * pages, but we want the details to be determined only for the current page.
     * Input params:
     * @param $resultset: Array of product ids
     * @param $data:      Filter date from controller
     * @param $call_from: From which medium, is this method called (like web or app)
     * @param $product_data: Passed by reference, which will be filled up for eventual return back to the controller
     */
    public function getProductDetails($resultset, $data, $call_from, &$product_data) {

        // Getting product details for app and website are separated.
        // In order to make product load faster, we get minimum info to display in app rightaway
        if ($call_from == 'app') {
            $product_data['data'] = array();

            $this->registry->load->model('restapi/service');
            //$product_data['total'] = $this->_product_total;

            if ($data['user_id']) {
                $user_id = $data['user_id'];
            } else {
                $user_id = 0;
            }

            $out_of_stock_product_count = 0;
            foreach ($resultset as $product_id) {

                $product_info = $this->registry->model_restapi_service->getProduct($product_id, $user_id);
                if (!empty($product_info)){
                    // 08-07-2017: Check product's stock status, if product is out of stock, then changing the quantity and stock to 0
                    // stock_status is coming from model_catalog_product->getProduct method
                    if(isset($product_info['stock_status']) && $product_info['stock_status'] == 'out of stock' ) {
                        $out_of_stock_product_count += 1;
                        $product_info['quantity'] = "0";
                        $product_info['stock'] = "0";
                        if(!empty($this->show_out_of_stock)) {
                            $product_data['data'][] = $product_info;
                        } else {
                            if(isset($product_data['product_total'])) {
                                $product_data['product_total'] -= 1;
                            }
                            if(isset($product_data['total'])) {
                                $product_data['total'] -= 1;
                            }
                        }
                    } else {
                        $product_data['data'][] = $product_info;
                    }
                }
            }

            if ( !empty($resultset) && empty( $product_data['data'] ) && count( $resultset ) == $out_of_stock_product_count ) {
                $product_data['send_status'] = "1";
            }

        } else { // Getting data for web

            $product_data['products'] = array();

            $this->registry->load->model('catalog/product');
            //$product_data['product_total'] = $this->_product_total;

            foreach ($resultset as $product_id) {

                $product_info = $this->registry->model_catalog_product->getProduct($product_id);

                if (!empty($product_info)) {
                    if(isset($product_info['stock_status']) && $product_info['stock_status'] == 'out of stock' ) {
                        $product_info['quantity'] = "0";
                        $product_info['stock'] = "0";
                        if(!empty($this->show_out_of_stock)) {
                            $product_data['products'][$product_id] = $product_info;
                        } else {
                            if(isset($product_data['product_total'])) {
                                $product_data['product_total'] -= 1;
                            }
                            if(isset($product_data['total'])) {
                                $product_data['total'] -= 1;
                            }
                        }
                    } else {
                        $product_data['products'][$product_id] = $product_info;
                    }
                }
            }
        }
    }


    /**
     * add new document to solr
     * @param $data
     */
    public function addProductToSolr($data){
        //$data = $data; //Conver array in stdClass object
        $config_solr = $this->registry->config->solrConfig();
        // create a client instance
        $client = new Solarium\Client($config_solr);
        // get an update query instance
        $update = $client->createUpdate();
        // create a new document for the data
        // please note that any type of validation is missing in this example to keep it simple!
        $doc = $update->createDocument();

        $doc->id                = (int)$data['product_id'];
        $doc->sku               = $data['sku'];
        $doc->model             = $data['model'];
        $doc->hsn_code          = $data['hsn_code'];
        $doc->price             = (float)$data['price'];
        $doc->selling_price     = (float)$data['selling_price'];
        $doc->stock_status      = $data['stock_status'];
        $doc->stock_status_id   = (int)$data['stock_status_id'];
        $doc->is_archived       = 0;
        $doc->tax_class_id      = isset($data['tax_class_id'])?$data['tax_class_id']:'0';

        //Get Languages
        $this->registry->load->model('localisation/language');
        $languages          = $this->registry->model_localisation_language->getLanguages();
        $name               = '';
        $description        = '';
        $set_description    = '';
        $tag                = '';

        foreach ($languages as $key => $val) {
            if(isset($data['product_description'][$val['language_id']])) {
                $name .= " " . $data['product_description'][$val['language_id']]['name'];
                $set_description .= " " . $data['product_description'][$val['language_id']]['set_description'];
                $description .= " " . $data['product_description'][$val['language_id']]['description'];
                $tag .= " " . $data['product_description'][$val['language_id']]['tag'];
            }
        }

        $doc->name              = trim($name);
        $doc->set_description   = trim($set_description);
        $doc->description       = trim($description);
        $doc->tag               = trim($tag);
        $doc->is_single         = (int)$data['is_single'];
        $doc->minimum           = (int)$data['minimum'];
        $doc->piece_in_set      = (int)$data['piece_in_set'];
        $doc->status            = (int)$data['status'];
        $doc->quantity          = (int)$data['quantity'];
        $doc->sort_order        = (int)$data['sort_order'];
        $doc->date_added        = date('Y-m-d\TH:i:s\Z', time());
        $doc->date_available    = date('Y-m-d\TH:i:s\Z', strtotime($data['date_available']));
        $doc->viewed            = (int)$data['viewed'];
        if(!empty($data['store_sales'])){
            $doc->store_sales   =  $data['store_sales'];
        }else{
            $doc->store_sales   =  'NO';
        }
        if(!empty($data['exclusive'])){
            $doc->exclusive   =  $data['exclusive'];
        }else{
            $doc->exclusive   =  'normal';
        }

        if(isset($data['seller_id']) && ($data['seller_id'] != 0)) {
            $doc->seller_id     = (int)$data['seller_id'];
            $doc->seller_status = (int)$data['seller_status'];
            $doc->vacation_mode = (int)$data['vacation_mode'];
            $doc->app_only      = (int)$data['app_only'];
            $doc->non_serviceable_areas = explode(',',$data['non_serviceable_areas']);

        }else{
            $doc->seller_id     = 0;
            $doc->seller_status = 0;
            $doc->vacation_mode = 0;
            $doc->app_only      = 0;
            $doc->non_serviceable_areas = array();
        }

        $doc->rating            = 0;
        $doc->hsn_code          = isset($data['hsn_code'])?$data['hsn_code']:"";

        $doc->franchise_id      = isset($data['franchise_id'])?(int)$data['franchise_id']:0;

        // SOR terms
        if( !empty( $data['sor_type'] ) && !empty( $data['sor_days'] )) {
            $doc->sor_type = $data['sor_type'];
            $doc->sor_days = (int) $data['sor_days'];
        }

        /* Stores, Categories, Filters Sync can be done by trigger
        //Stores
        $doc->store_id          = $data['product_store'];

        //categories
        $this->registry->load->model('catalog/category');
        $category_name = array();
        if (count($data['product_category']) > 0) {
            foreach($data['product_category'] as $category) {
                $category_languages = $this->getCategoryWithAllLanguages($category);
                foreach($category_languages as $category_info){
                    $category_name[] = $category_info['name'];
                }
            }
        }
        $category_id = $data['product_category'];
        $category_name = implode(',', $category_name);
        $doc->category_id       = $category_id;
        $doc->categories        = $category_name;

        //Filters
        $doc->filter_id = $data['product_filter'];
        $filter_by_group = array();
        if (count($data['product_filter']) > 0) {
            //dynamic filed with filter group id
            foreach($data['product_filter'] as $filter) {
                if($filter > 0) {

                    $filter_group_id = $this->getFilterGroup($filter);
                    $filter_by_group[$filter_group_id][] = $filter;
                }
            }
            if (count($filter_by_group) > 0) {
                foreach ($filter_by_group as $group_id => $arr_filter_id) {
                    $dynamic_filed_name = 'filter_group_df_'.$group_id;
                    $doc->$dynamic_filed_name = implode(",", $arr_filter_id);
                }
            }
        }
        */

        $update->addDocument($doc);
        $update->addCommit();

        // this executes the query and returns the result
        $result = $client->update($update);
        /*
        echo '<b>Update query executed</b><br/>';
        echo 'Query status: ' . $result->getStatus(). '<br/>';
        echo 'Query time: ' . $result->getQueryTime();
        */
        //print_r($doc);
    }

    /**
     * update solr document
     * @param $data
     * @author vikas, 2018
     */
    public function editProductToSolr($data){
        $solr_obj = array(
                'endpoint' => array(
                        'localhost' => array(
                                'host' => SOLR_HOST,
                                'port' => SOLR_PORT,
                                'path' => SOLR_PATH,
                                'timeout' => 50000
                        )
                )
        );

        // create a client instance
        $client = new Solarium\Client($solr_obj);

        // get an update query instance
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        $doc->setKey('id', $data['product_id']);

        foreach ($data as $key => $value) {
            if(!($key == 'product_id') ) {
                if( $value === "" ) { $value = NULL; }
                if($value === NULL) { $doc->setField($key, ""); }
                $doc->addField($key, $value);
                $doc->setFieldModifier($key, 'set');
                if($key == 'nickname') {
                    $nickname_delimited_field = $this->getNicknameDelimitedFieldForSolr($value);
                    if(!empty($nickname_delimited_field)) {
                        $doc->addField($nickname_delimited_field['nickname_delimited_key'], $nickname_delimited_field['nickname_delimited_value']);
                        $doc->setFieldModifier($nickname_delimited_field['nickname_delimited_key'], 'set');
                    }
                }
            }
        }

         // Code to remove filter_group_df_ which don't have filters now
        $query = $client->createSelect();
        $sql = 'id:'.$data['product_id'];
        $query->setQuery($sql);
        $select_result = $client->select($query);

        if($select_result->getNumFound() > 0) {
            //$select_arr = $select_result->getData()['response']['docs'][0];

            foreach ($select_result as $document) {
                $select_arr = $document->getFields();
            }
            if(!empty($select_arr) && !empty($data)) {
                $result = array_diff_key($select_arr,$data);

                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        if(strpos( $key, 'filter_group_df_' ) !== false){
                            $doc->addField($key, '');
                            $doc->setFieldModifier($key, 'set');
                        }
                    }
                }
            }
        }

        $update->addDocument($doc);
        $update->addCommit();
        // this executes the query and returns the result
        $result = $client->update($update);

        // To manage Purchased Inventory stock statuses
        self::manageWsbPurchasedInventorySearchability($data['product_id']);
    }

    /**
     * Partially update solr document
     * @param $data
     * @author Garvit
     */
    public static function atomicUpdateToSolr($data){

        $solr_obj = array(
                'endpoint' => array(
                        'localhost' => array(
                                'host' => SOLR_HOST,
                                'port' => SOLR_PORT,
                                'path' => SOLR_PATH,
                                'timeout' => 50000
                        )
                )
        );

        // create a client instance
        $client = new Solarium\Client($solr_obj);

        // get an update query instance
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        $doc->setKey('id', $data['product_id']);

        // Code to remove filter_group_df_ which don't have filters now
        $query = $client->createSelect();
        $sql = 'id:'.$data['product_id'];
        $query->setQuery($sql);
        $select_result = $client->select($query);
        if($select_result->getNumFound() > 0 && empty($data['skip_filter_groups'])) {
            //$select_arr = $select_result->getData()['response']['docs'][0];

            foreach ($select_result as $document) {
                $select_arr = $document->getFields();
            }
            if(!empty($select_arr) && !empty($data['fields'])) {
                $result = array_diff_key($select_arr,$data['fields']);

                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        if(strpos( $key, 'filter_group_df_' ) !== false){
                            $doc->addField($key, '');
                            $doc->setFieldModifier($key, 'set');
                        }
                    }
                }
            }
        }

        // Update code
        foreach($data['fields'] as $key=>$val){

            $doc->addField($key, $val);
            $doc->setFieldModifier($key, 'set');
            if($key == 'nickname') {
                $nickname_delimited_field = SolrProduct::getNicknameDelimitedFieldForSolr($val);
                if(!empty($nickname_delimited_field)) {
                    $doc->addField($nickname_delimited_field['nickname_delimited_key'], $nickname_delimited_field['nickname_delimited_value']);
                    $doc->setFieldModifier($nickname_delimited_field['nickname_delimited_key'], 'set');
                }
            }
        }

        $update->addDocument($doc)->addCommit();
        // this executes the query and returns the result
        $result = $client->update($update);
        
        // To manage Purchased Inventory stock statuses
        // manageWsbPurchasedInventorySearchability function calls atomicUpdateToSolr; skip_same_function_call will be used to skip interloop calling
        if(empty($data['skip_same_function_call'])) {
            self::manageWsbPurchasedInventorySearchability($data['product_id']);
        }
    }

    /**
     * Partially update the solr document in bulk [Commit after updatig all docs]
     * @param $data array
     *   -  Array to pass general data based on the $type specified.
     * @param $type string
     *   -  Current available options:
     *        - 'vacation_mode'         : $data array must contain 'seller_id' and 'vacation_mode' in $value keys
     *        - 'seller_status'         : $data array must contain 'seller_id' and 'seller_status' in $value keys
     *        - 'seller_rating'         : $data array must contain 'seller_id' and 'seller_rating' in $value keys
     *        - 'app_only'              : $data array must contain 'app_only' in $value keys
     *        - 'non_serviceable_areas' : $data array must contain 'non_serviceable_areas' in $value keys
     * @author Garvit
     **/
    public static function atomicBulkUpdateToSolr($data, $type) {

        $db = new Database\DB( DB_SERVERS );

        $solr_obj = array(
                'endpoint' => array(
                        'localhost' => array(
                                'host' => SOLR_HOST,
                                'port' => SOLR_PORT,
                                'path' => SOLR_PATH,
                                'timeout' => 50000
                        )
                )
        );

        $client = new Solarium\Client($solr_obj);
        $update = $client->createUpdate();

        $seller_id     = (int)$data['seller_id'];

        $sql = "SELECT product_id FROM " . DB_PREFIX . "ms_product WHERE seller_id = '" . $seller_id . "'";
        $query = $db->query($sql);
        $updated_products = array();
        
        if ( ( $type == 'nickname' || $type == 'city' || $type == 'vacation_mode' || $type == 'seller_status' || $type == 'app_only' || $type == 'non_serviceable_areas' ) && $query->num_rows ) {
            $change_value  = $data['value'];
            foreach ($query->rows as $product) {
                $doc = $update->createDocument();
                $doc->setKey('id', $product['product_id']);
                $doc->addField($type, $change_value);
                $doc->setFieldModifier($type, 'set');
                
                if($type == 'nickname') {
                    $nickname_delimited_field = SolrProduct::getNicknameDelimitedFieldForSolr($change_value);
                    if(!empty($nickname_delimited_field)) {
                        $doc->addField($nickname_delimited_field['nickname_delimited_key'], $nickname_delimited_field['nickname_delimited_value']);
                        $doc->setFieldModifier($nickname_delimited_field['nickname_delimited_key'], 'set');
                    }
                }
                
                $update->addDocument($doc);
                $updated_products[] = $product['product_id'];
            }
        } /* elseif ( $type == 'seller_status' && $query->num_rows ) {
            $change_value  = (int)$data['value'];
            if($change_value != 1) {
                foreach ($query->rows as $product) {
                   self::_deleteProductFromSolrByTrigger($solr_obj, $product['product_id']);
                }
            }
        } */ elseif ( $type == 'seller_rating' && $query->num_rows ) {
            $db_obj = new Database\DB( DB_SERVERS );

            foreach ($query->rows as $product) {
                self::_getRatingsFromDb($product['product_id'], $update, $db_obj);
            }
        }

        $update->addCommit();
        $result = $client->update($update);
        
        // To manage Purchased Inventory stock statuses
        if(!empty($updated_products)) {
            foreach ($updated_products as $key => $product_id) {
                self::manageWsbPurchasedInventorySearchability($product_id);
            }
        }
    }

    /* *
     * _getRatingsFromDb
     * @info   Here We Call Stored Procedure for Sync Rating To Solr.
     * @param  $product_id
     * @param  $update
     * @author Garvit
     **/

    // rating is added in oc_product, rating will be sync by product_change_log
    /* private function _getRatingsFromDb($product_id, $update, $db_obj) {
        $rating = $db_obj->sp_query("CALL syncRatingsToSolr(". $product_id .")")->row['rat'];
        if($rating != ''){
            $doc = $update->createDocument();
            $doc->setKey('id', $product_id);
            $doc->addField('rating', $rating);
            $doc->setFieldModifier('rating', 'set');
            $update->addDocument($doc);
        }
    } */


    /* *
     * _syncOrderWishlistToSolr
     * @info   Here We sync Order and wishlist for a spacific product. AND IN ORDER AND WISHLIST WE STORE CUSTOMER_ID to KNOW THAT THIS PRODUCT IS ORDER BY THIS Customer.
     * @param  $product_id
     * @param  $update
     * @param  $db_obj
     * @author Garvit
     **/

    // order wishlist will be manage by customer preferences logic.
    /* private function _syncOrderWishlistToSolr($product_id, $update, $db_obj) {
        $order_by = $db_obj->query("SELECT DISTINCT o.customer_id AS order_by
                                    FROM oc_order o
                                    INNER JOIN oc_order_product op ON (o.order_id = op.order_id)
                                    WHERE op.product_id = ".$product_id)->rows;

        $wishlist = $db_obj->query("SELECT DISTINCT customer_id AS wishlist
                                    FROM oc_customer_wishlist
                                    WHERE product_id = ".$product_id)->rows;

        if($order_by != '' || $wishlist != ''){
            $doc = $update->createDocument();
            $doc->setKey('id', $product_id);

            if($order_by != ''){
                $order_by = array_column($order_by, 'order_by');
                $order_by_count = count($order_by);
                $doc->addField('order_by', $order_by);
                $doc->addField('order_by_count', $order_by_count);
                $doc->setFieldModifier('order_by', 'set');
                $doc->setFieldModifier('order_by_count', 'set');
            }
            if($wishlist != ''){
                $wishlist = array_column($wishlist, 'wishlist');
                $wishlist_count = count($wishlist);
                $doc->addField('wishlist', $wishlist);
                $doc->addField('wishlist_count', $wishlist_count);
                $doc->setFieldModifier('wishlist', 'set');
                $doc->setFieldModifier('wishlist_count', 'set');
            }
            $update->addDocument($doc);
        }
    } */

    /**
     * _deleteProductFromSolrByTrigger
     * Remove document from solr indexing
     * @param  $solr_obj
     * @param  $product_id
     * @author Garvit
     **/
    private function _deleteProductFromSolrByTrigger($solr_obj, $product_id){
        $client = new Solarium\Client($solr_obj);
        $update = $client->createUpdate();
        $update->addDeleteById($product_id);
        $update->addCommit();
        $result = $client->update($update);
    }

    /**
     * Remove document from solr indexing
     * @param $id
     */
    public function deleteProductFromSolr($id){
        $config_solr = $this->registry->config->solrConfig();
        // create a client instance
        $client = new Solarium\Client($config_solr);

        // get an update query instance
        $update = $client->createUpdate();

        // add the delete id and a commit command to the update query
        $update->addDeleteById($id);
        $update->addCommit();

        // this executes the query and returns the result
        $result = $client->update($update);
    }


    /**
     * Get category data in all languages
     * @param $category_id
     * @return mixed
     */

    public function getCategoryWithAllLanguages($category_id) {
        $sql = "SELECT DISTINCT *
                FROM " . DB_PREFIX . "category c
                LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
                WHERE c.category_id = '" . (int)$category_id . "'
                AND c.status = '1'";
        $query = $this->registry->db->query($sql);

        return $query->rows;
    }

    /**
     * Get synonyms set in custom dictionaery
     * @param $word
     * @param $additional_words - defaulted to empty array.
     *        Use in the case when additional words also need to be
     *        searched for besides the synonyms
     * @return string
     */
    public function getSynonyms($word, $additional_words=array()){


        $solar_product = new model_solr_product($this->registry);
        $synonyms_words = $solar_product->getDictionarySynonyms($word);
        if (!empty($additional_words) ) {
            $synonyms_words = array_merge($synonyms_words, $additional_words);
        }

        //add OR between synonyms words
        $synonyms_words_string = implode("* OR searchable:*", array_map('trim', $synonyms_words));

        // priority for searched word
        $synonyms_words_string = str_ireplace( $word.'*', $word.'*^500', $synonyms_words_string );
        
        $search_for_all_words = ' (searchable:*' . trim($synonyms_words_string) . '*)';

        //$search_for_all_words = implode("* AND searchable:*", $arr_search);
        //$search_for_all_words = ' (searchable:*' . trim($search_for_all_words) . '*)';

        return $search_for_all_words;
    }

    /**
     * Get autosuggestions
     * @param $keyword
     * @return array
     */
    public function getAutoSuggestions($keyword){
        
        $keyword = array_map( 'trim', array_filter( explode( " ", preg_replace( '/([^a-zA-Z0-9\_\- ]+)/', " ", $keyword ))));
        $keyword = implode( " ", $keyword );
        
        $response = array();
        // if(strlen($keyword) > 2) {
        $config_solr = $this->registry->config->solrConfig(SOLR_PATH_AUTO_SUGGESTIONS);
        // create a client instance
        $client = new Solarium\Client($config_solr);

        // get a select query instance
        $query = $client->createSelect();

        $sql = array();

        $arr_search = explode(" ", $keyword);

        $sql[] = 'user_query:*' . implode("* AND user_query:*", $arr_search) . '*';
        //$sql[] = 'user_query:'.$keyword.'*';
        $sql_and = implode(" AND ", $sql);

        $query->setQuery($sql_and);


        $query->setFields(array('id', 'user_query'));

        $query->setStart(0, 10);

        $query->addSort('user_query_count', $query::SORT_DESC);

        // this executes the query and returns the result
        $resultset = $client->select($query);


        $patterns = array();
        $replacements = array();

        foreach ($arr_search as $word) {
            $patterns[] = '/' . $word . '/i';
            $replacements[] = '<b>' . $word . '</b>';
        }
        $i = 0;
        foreach ($resultset as $document) {
            if(strlen($document->user_query) > 5) {
                $highlighted = preg_replace($patterns, $replacements, $document->user_query);

                $response[$i]['label'] = $highlighted;
                $response[$i]['value'] = $document->user_query;
                $i++;
            }
        }
        //}

        return $response;

    }
    /**
     * get product form mysql and Sync to Solr Document
     * @author Garvit Joshi
     * @param $product_id
     */
    public function getProductDataFromSolr($product_id){
        $config_solr = $this->registry->config->solrConfig();
        // create a client instance
        $client = new Solarium\Client($config_solr);
        // get a select query instance
        $query  = $client->createSelect();
        $sql    = 'id:' . $product_id;
        $query->setQuery($sql);
        $query->getFields(array('id'));
        $query->setStart(0, 10);

        // this executes the query and returns the result
        $resultset = $client->select($query);

        $results = array();
        foreach ($resultset as $document) {
            $results = $this->getDataFromSolrObject($document);
        }

        return $results;
    }

    private function getDataFromSolrObject($document){
        // Return the data of documents According to product In solr
        $results = array();

        $results['id']              = $document->id;
        $results['model']           = $document->model;
        $results['price']           = $document->price;
        $results['selling_price']   = $document->selling_price;
        $results['stock_status']    = $document->stock_status[0];
        $results['stock_status_id'] = $document->stock_status_id;
        $results['name']            = $document->name[0];
        $results['set_description'] = $document->set_description[0];
        $results['description']     = $document->description[0];
        $results['tag']             = $document->tag[0];
        $results['is_single']       = $document->is_single;
        $results['minimum']         = $document->minimum;
        $results['piece_in_set']    = $document->piece_in_set;
        $results['status']          = $document->status;
        $results['quantity']        = $document->quantity;
        $results['sort_order']      = $document->sort_order;
        $results['date_added']      = $document->date_added;
        $results['date_available']  = $document->date_available;
        $results['viewed']          = $document->viewed;
        $results['seller_id']       = $document->seller_id;
        $results['seller_status']   = $document->seller_status;
        $results['vacation_mode']   = $document->vacation_mode;
        $results['app_only']        = $document->app_only;
        $results['non_serviceable_areas']   = $document->non_serviceable_areas;
        $results['rating']          = $document->rating;
        $results['store_id']        = $document->store_id;
        $results['category_id']     = $document->category_id;
        $results['categories']      = $document->categories;
        $results['filter_id']       = $document->filter_id;
        $results['order_by']        = $document->order_by;
        $results['wishlist']        = $document->wishlist;
        $results['order_by_count']  = $document->order_by_count;
        $results['wishlist_count']  = $document->wishlist_count;

        // Convert Object to array to access dynamic filters key
        $sql = "SELECT f.filter_group_id
                    FROM oc_product_filter pf
                    INNER JOIN oc_filter f
                    ON f.filter_id=pf.filter_id
                    WHERE product_id=".$document->id."
                    GROUP BY pf.product_id, f.filter_group_id";
        $filter_group = $this->registry->db->query($sql)->rows;

        foreach ($filter_group as $val) {
            $filter_grp_id = "filter_group_df_".$val['filter_group_id'];
            $results['filter_group_df_'.$val['filter_group_id']] = $document->$filter_grp_id;
        }

        return $results;
    }

    /**
     *This Functions Take products Only from Solr not Database.
     **/
    public function getProductFromSolrOnly($data){

        $config_solr = $this->registry->config->solrConfig();
        // create a client instance
        $client = new Solarium\Client($config_solr);
        $sql = array();

        // Product out of stock status id
        $out_of_stock_id = 5;
        $in_stock_id = 7;
        $show_exclusive_only = 0;

        //*** franchise filter
        $franchise_sql = '(franchise_id:0 OR (*:* AND -franchise_id:[* TO *]))';
        if(isset($data['franchise_id']) && $data['franchise_id'] != '' && (int) $data['franchise_id'] != 0) {
           $franchise_sql = '(franchise_id:(0 OR '.$data['franchise_id'].') OR (*:* AND -franchise_id:[* TO *]))';
        }
        $sql[] = $franchise_sql;

        if (!isset($data['status'])) {
            $sql[] = 'status:1'; // Product status to be enabled in order to be displassyable
        }

        if (!empty($data['filter_date_added'])) {
         $sql[] = 'date_added:['.$data['filter_date_added'].' TO NOW]'; 
        }

        if (isset($data['show_out_of_stock'])) {
            $show_out_of_stock = $data['show_out_of_stock'];
        }
        else {
            $show_out_of_stock = 0;
        }

        if(!isset($data['quantity'])) {
            if ($show_out_of_stock == 0) { // quantity >= 1 and not marked out of stock
                $sql[] = 'quantity:[1 TO *] AND -stock_status_id:' . $out_of_stock_id;
            } elseif ($show_out_of_stock == 2) {
                $sql[] = 'quantity:0 OR stock_status_id:'.$out_of_stock_id;
            }
        }

        if ( isset($data['filter_seller_sku']) && !empty((trim($data['filter_seller_sku'])))) {
            // $sql[] = 'searchable:'. trim('*'.$data['filter_seller_sku'].'*');

            $sllr_sku_filter_operator = $data['sllr_sku_filter_operator'];
            $sllr_sku_filter_type_string = $data['sllr_sku_filter_type_string'];
            $sllr_sku_filter_val_from = $data['sllr_sku_filter_val_from'];
            $sllr_sku_filter_val_to = $data['sllr_sku_filter_val_to'];

            // $sql .= " AND ( p.sku LIKE '%" . $this->db->escape(trim($data['filter_seller_sku'])) . "%'" ;
            $searchable = ' ( searchable:'. trim('*'.$data['filter_seller_sku'].'*');

            if(!empty($sllr_sku_filter_type_string)){
                $searchable .= queryStringForSolr('searchable', $sllr_sku_filter_type_string, $sllr_sku_filter_operator);
            }

            if(!empty($sllr_sku_filter_val_from) && !empty($sllr_sku_filter_val_to) ){
                $searchable .= queryIntegerForSolr('searchable', $sllr_sku_filter_val_from, $sllr_sku_filter_val_to, $sllr_sku_filter_operator);
            }
            $searchable .= " ) ";
            $sql[] = $searchable;
        }

        if ( isset($data['filter_category_id']) && !empty($data['filter_category_id'])) { // get products form particular categories ony
            $category_id = explode(',', $data['filter_category_id']);
            foreach ($category_id as $key => $value) {
                $category[] = 'category_id:'.$value;
            }
            $sql[] = '('.implode(" OR ", $category).')';
        } else {
          if(!empty($data['is_latest'])) {
            $sql[] = '-(*:* AND -category_id:[* TO *])';
          }
        }

        if ( isset($data['filter_seller_id']) && !empty($data['filter_seller_id'])) {
            $seller_id = explode(',', $data['filter_seller_id']);
            foreach ($seller_id as $key => $value) {
                $seller[] = 'seller_id:'.$value;
            }
            $sql[] = '('.implode(" OR ", $seller).')';
        }

        if ( isset($data['filter_special']) && !empty((trim($data['filter_special'])))) { // get products with special price

            $sql[] = 'date_start:[* TO NOW] AND date_end:[NOW TO *]';
            $sql[] = 'special_price:[1 TO *]';
        }

        $sql_facet = $sql;

        if (!empty($data['price_filter'])  && $data['price_filter'] != 'undefined') { // If price range is selected
            $price_min = '';
            $price_max = '';

            if (isset($data['price_filter']) ) {
                $prices = explode('-', $data['price_filter']);
                $price_min = $prices[0];
                $price_max = $prices[1];
            }

            if($this->registry->currency->getCode() == 'INR'){
                $price_values = $this->registry->currency->currencies['INR']['value'];
            }else{
                $price_values = $this->registry->currency->currencies['USD']['value'];
            }


            if($price_max == 0){
                $price_min = $price_min/$price_values;
                $sql[] = "selling_price:[".$price_min." TO * ]";
            }else{
                $price_min = $price_min/$price_values;
                $price_max = $price_max/$price_values;

                $sql[] = "selling_price:[".$price_min." TO ".$price_max. "]";

            }

        }

        if(empty($data['search_all_products']) && !isset($data['is_single'])) {
            if (!empty($data['custom_store']) && $data['custom_store'] == 'single') { // Show singles store items only
                $sql_facet[] = $sql[] = "(is_single:1 OR (piece_in_set:1 AND minimum:1))";
            }else{
                $sql_facet[] = $sql[] = "is_single:0";
            }
        }



        if(!empty($data['rating_filter'])){ // If ratings selected
            $sql[] = "rating:[".$data['rating_filter']." TO *]";
        }
        else{
            // If international store then, we will not show average products.
            if($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                $sql[] = "(rating:[4 TO *] OR (*:* AND -rating:[* TO *]))";
            }
        }

        if(!empty($data['hsn_code'])){
            $sql_facet[] = $sql[] = "hsn_code:".$data['hsn_code'];
        }
        //Filters
        if (!empty($data['filter_filter'])) {
            $implode_filter = array();

            $filters = array_unique(explode(',', $data['filter_filter']));

            foreach ($filters as $filter_id) {
                $implode_filter[] = (int)$filter_id;
            }

            $filter_string = implode(',', $implode_filter);
            
            $filter_result = $this->getFilterDetailsWithGroup( $filter_string );

            if( $filter_result ) {
                foreach ($filter_result as $result) {
                    $filter_or = '('.str_replace(",", " OR ", $result['filter_ids']).')';
                    $filter_group_key = 'filter_group_df_'.$result['filter_group_id'];
                    $filter_sql = $filter_group_key.':'.$filter_or;

                    if( in_array( $result['filter_group_id'], SEARCHABLE_FILTER_GROUP_IDS )) {
                        $searchable_filter_group_sql = 'searchable:('.str_replace(",", "* OR *", '*'.trim($result['filter_names']).'*').')';
                        $filter_sql = '(' . $filter_sql . ' OR ('. $searchable_filter_group_sql .'))';
                    }
                    $sql[] = $filter_sql;
                }
            }
        }

        $call_from = '';
        if ( !empty($data['call_from']) ) {
            $call_from = strtolower(trim($data['call_from']));
        }

        $post_code = '';
        if ( !empty($data['post_code']) ) {
            $post_code = strtolower(trim($data['post_code']));
        }

        //Search by keyword which comes in filter_name or filter_tag
        if(!isset($data['backend'])){
            $sql_facet[] = $sql[] = '(is_associate:0 OR (*:* AND -is_associate:[* TO *]))';
            $sql_facet[] = $sql[] = 'seller_status:1';
            $sql_facet[] = $sql[] = 'vacation_mode:0';
            /*$sql_facet[] = */$sql[] = 'price:[1 TO *]';
            $sql_facet[] = $sql[] = 'selling_price :[1 TO *]';
            $sql_facet[] = $sql[] = 'is_archived:0';
            $sql_facet[] = $sql[] = 'hsn_code:([* TO *] AND -"")';
            //$sql_facet[] = $sql[] = 'tax_class_id:[1 TO *]';


            if($call_from !== 'app') $sql_facet[] = $sql[] = 'app_only:0';
            if($call_from == 'app' && !empty($post_code)) $sql_facet[] = $sql[] = '-non_serviceable_areas:'.$post_code;

            $keyword = '';
            if (isset($data['filter_tag']) && $data['filter_tag'] != '') {
                $keyword = trim($data['filter_tag']);
            }
            if (isset($data['filter_name']) && $data['filter_name'] != '') {
                $keyword = trim($data['filter_name']);
            }

            $check_keyword = $keyword;

            if ($keyword != '') {

                $original_keyword = $keyword;
                $keyword = array_map( 'trim', array_filter( explode( " ", preg_replace( '/([^a-zA-Z0-9\_\- ]+)/', " ", $keyword ))));
                $keyword = implode( " ", $keyword );
                
                /**
                 * added search feature for purchase inventory
                 */
                if( strtolower($original_keyword) == strtolower(WSB_PURCHASE_INVENTORY_SEARCH_TERM) ) {
                    
                    $sql_facet[] = $sql[] =  "(seller_id:(". implode(' OR ', WSB_PURCHASE_INVENTORY_SELLERS) ."))";
                
                } else {
                    $pos = (int)strpos($keyword, " ");

                    if ($pos > 0) {

                        $check_if_first_char_inverted = strpos(html_entity_decode($keyword), '"');

                        if ($check_if_first_char_inverted === 0) { //do exact match search
                            $keyword = html_entity_decode($keyword);
                            $keyword = substr($keyword, 1, strlen($keyword));
                            //check if last character is double quote
                            //get last char
                            $inverted = substr($keyword, -1);
                            if($inverted == '"'){
                                $keyword = substr($keyword, 0, strlen($keyword)-1);
                            }
                            $keyword = addslashes($keyword);
                            $arr_search = explode(" ", $keyword);
                            $search_string = implode("+", $arr_search);
                            // $search_string .= '+OR+"'.$search_string.'"^30';
                            $sql_facet[] = $sql[] = 'searchable:"' . $search_string . '"';


                        } else {
                            $keyword = addslashes($keyword);
                            $arr_search = explode(" ", $keyword);

                            $sql_searchable = array();

                            foreach ($arr_search as $word) {

                                $sql_searchable[] = $this->getSynonyms($word);//$search_for_all_words;
                            }

                            /**
                             * changed search priority
                             * first priority: exact same keyword ex: "rayon kurti"
                             * second priority: rayon+kurti (other words can come in between)
                             * third priority: synonyms
                             */
                            $first_priority_search = '(searchable:"'. $keyword . '")^1000';
                            $sql_facet[] = $sql[] = '('. $first_priority_search .' OR ('. implode(' AND ', $sql_searchable).'))';

                        }

                    } else {

                        $additional_words = array();
                        // To allow seller specific product search
                        // eg: 001_JP and 001JP
                        // This is done because new product codes dont have '_'
                        if ( (int)strpos($keyword, "_") > 0 ) {
                            $additional_words[] = trim(str_replace("_","",$keyword));
                        }

                        $sql_searchable[] = $this->getSynonyms($keyword, $additional_words);
                        $sql_facet[] = $sql[] =  $sql_searchable[0];

                    }
                }
            }

            if(isset($data['store_code']) && !empty($data['store_code'])) {
                $upper_store_code = strtoupper($data['store_code']);
                $sql_facet[] = $sql[] = "store_sales:".$upper_store_code;
            }else{
              /*  if(!isset($data['is_search'])){
                    $not_in_seller = explode(",", SOLR_NOT_IN_SELLER);
                    $not_in_seller_arr = array();
                    foreach($not_in_seller as $val){
                        $not_in_seller_arr[] ='seller_id:'.$val;
                    }
                    $sql[] = '-('.implode(" OR ", $not_in_seller_arr).')';
                } */
            }
        } else {

            //** SOR Filter
            if ( isset( $data['sor_filter'] ) && $data['sor_filter'] !== '' ) {
                $sor_enums = $this->registry->db->getEnumValues('oc_product_sor_terms','sor_type');
                if ( !empty( $sor_enums ) && isset( $sor_enums[$data['sor_filter']] )) {
                    $sql[] = 'sor_type:'. $sor_enums[$data['sor_filter']] .' AND sor_days:[1 TO *]';
                }
            }

            // associate product filter for Backend
            if (!empty($data['is_associate'])) {
              $sql_facet[] = $sql[] = 'is_associate:' . $data['is_associate'];
            }
            
            if (!empty($data['filter_seller_list'])) {
                $sql_facet[] = $sql[] = 'seller_id:'.$data['filter_seller_list'];
            }

            if (!empty($data['filter_name'])) {
                $sql_facet[] = $sql[] = 'name:'.$data['filter_name'];
            }

            if (!empty($data['filter_commission_from']) && !empty($data['filter_commission_to'])) {
                $sql_facet[] = $sql[] = 'commission:['.$data['filter_commission_from'] .' TO '. $data['filter_commission_to'].' ]';
            }

            if (!empty($data['filter_price_from']) && !empty($data['filter_price_to'])) {
                $sql_facet[] = $sql[] = 'price:['.$data['filter_price_from'] .' TO '. $data['filter_price_to'].' ]';
            }

            if (isset($data['filter_quantity']) && $data['filter_quantity'] != '') {
                $sql_facet[] = $sql[] = 'quantity:'.$data['filter_quantity'];
            }

            if (isset($data['filter_model']) && $data['filter_model'] != '') {
                // $sql_facet[] = $sql[] = 'model_copy:'.trim('*'.$data['filter_model'].'*');

                $filter_operator = $data['filter_operator'];
                $filter_type_string = $data['filter_type_string'];
                $filter_val_from = $data['filter_val_from'];
                $filter_val_to = $data['filter_val_to'];

                $model_copy = ' ( model_copy:'.trim('*'.$data['filter_model'].'*') ;

                if(!empty($filter_type_string)){
                    $model_copy .= queryStringForSolr('model_copy', $filter_type_string, $filter_operator);
                }

                if(!empty($filter_val_from) && !empty($filter_val_to) ){
                    $model_copy .= queryIntegerForSolr('model_copy', $filter_val_from, $filter_val_to, $filter_operator);
                }
                $model_copy .= " ) ";

                $sql_facet[] = $sql[] = $model_copy;

            }

            if (isset($data['filter_status']) && $data['filter_status'] != '') {
                $sql_facet[] = $sql[] = 'status:'.$data['filter_status'];
            }

            if (isset($data['filter_non_single']) && $data['filter_non_single'] == 1 ) {
                      $sql_facet[] = $sql[] = 'is_single:false';
            }

            if (isset($data['filter_non_sor']) && $data['filter_non_sor'] == 1) {
                $sql_facet[] = $sql[] = '-store_id:9';
            }

            if ( isset($data['filter_category']) && !empty((trim($data['filter_category'])))) {
                // get products form particular categories ony
                $sql_facet[] = $sql[] = 'category_id:'. trim($data['filter_category']);
            }

            if ( isset($data['filter_sort_order_from']) && !empty((trim($data['filter_sort_order_to'])))) {
                // get products by sort order
                $sql_facet[] = $sql[] = 'sort_order:['.$data['filter_sort_order_from'] .' TO '. $data['filter_sort_order_to'].' ]';
            }

            if ( isset($data['filter_store_sales_code']) && !empty((trim($data['filter_store_sales_code'])))) {
                // get products store sales
                $sql_facet[] = $sql[] = 'store_sales:'. trim($data['filter_store_sales_code']);
            }

            $sql_facet[] = $sql[] = '*:*';
        }

        //search in Wholesalebox stores only
        if ($this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
            $stores[] = 0;
        } elseif ($this->registry->config->get('config_store_id') == SOR_STORE_ID) {
            $stores[] = SOR_STORE_ID;
        } else {
            $stores[] = 0;
        }
        $sql_facet[] = $sql[] = 'store_id:' . implode(' OR store_id:', $stores);

        //if filter data has product_ids -> it will be the case in case of app, where app call api to get data for bunch of products
        if ( !empty($data['product_ids']) ) {
            $product_ids = $data['product_ids'];
            $arr_product_ids = explode("," , $product_ids);
            $sql_facet[] = $sql[] = '(id:'. implode(' OR id:', $arr_product_ids ).')';
        }

        // If we don't want spacific id's
        if( !empty($data['not_product_ids']) ) {
            $arr_product_ids = array();
            $arr_product_ids = explode("," , $data['not_product_ids']);
            $arr_product_ids = array_filter($arr_product_ids);
            $sql_facet[] = $sql[] = '-( id:'. implode(' OR id:', $arr_product_ids ).' )';
        }


        // Used for getting trending products
        if (!empty($data['days'])) {
            $sql_facet[] = $sql[] = "date_added:[NOW/DAY-". $data['days'] ."DAY TO *]";
        }

        // Used for Getting products By customer Order
        if (!empty($data['order_by'])) {
            $sql_facet[] = $sql[] = "order_by:". $data['order_by'];
        }
        // Used for Getting products By customer Wishlist
        if (!empty($data['wishlist'])) {
            $sql_facet[] = $sql[] = "wishlist:". $data['wishlist'];
        }

        // Used for Getting products By customer Wishlist, Orders
        if (!empty($data['customer_preference'])) {
            $sql_facet[] = $sql[] = "( order_by:". $data['customer_preference']. " OR wishlist:". $data['customer_preference'] ." )";
        }

        if (!empty($data['filter_old_days_products'])) {
            $sql_facet[] = $sql[] = "date_added:[* TO NOW/DAY-". $data['filter_old_days_products'] ."DAY]";
        }

        // This is needed to get correct product total at outset without any sort
        // This is to ensure that we always have correct pagination
        $sql_and = implode(" AND ", $sql);
        $sql_and_facet = implode(" AND ", $sql_facet);
        // get a select query instance
        $query = $client->createSelect();
        $query->setQuery($sql_and);
        // Get Spacific fields and $data['set_fields'] should be an ARRAY
        if (isset($data['select_fields'])) {
            $query->setFields($data['select_fields']);
        }
        // Facet
        // facet is used for count display the total number of documents found by solr
        if(!empty($data['is_facet'])) {
            if(!empty($data['facet_field'])) {
                // get the facetset component
                $facetSet = $query->getFacetSet();
                // create a facet field instance and set options
                $facetSet->createFacetField('field')->setField($data['facet_field']);
                // this executes the query and returns the result
                $resultset = $client->select($query);
                $facet_result = $resultset->getFacetSet()->getFacet('field');
            } else {
                //Make string for faceting
                $facet_json = $this->_getJsoNFacetStringForSolrRequest();
                $customizer = $client->getPlugin('customizerequest');

                // add a GET param thats only used for a single request (the default setting is no persistence)
                $customizer->createCustomization('json.facet')
                    ->setType('param')
                    ->setName('json.facet')
                    ->setValue($facet_json);
            }
        }
        
        if ( isset($data['filter_special']) && !empty( trim($data['filter_special']) ) ) {
            $query->createFilterQuery('valid_special_price')->setQuery("{!frange l=1}sub(selling_price,special_price)");
        }

        // Limit
        $start_limit    = 0;
        $end_limit      = 10;
        if(isset($data['start']) && !empty($data['start'])){
            $start_limit = $data['start'];
        }
        if(isset($data['limit']) && !empty($data['limit'])){
            $end_limit = $data['limit'];
        }

        $query->setStart($start_limit)->setRows($end_limit);

        if (isset($data['filter_limit']) ) {
            $start_limit    = 0;
            $end_limit      = 10;
            $limit = explode('-', $data['filter_limit']);
            if(isset($limit[1])){
                $start_limit    = (int)$limit[0];
                $end_limit      = (int)$limit[1];
            } else {
                $end_limit      = (int)$limit[0];
            }
            $query->setStart($start_limit)->setRows($end_limit);
        }

        // To get a data by sort and Order by.
        if(isset($data['sort_data_by']) && isset($data['order_data_by']) && strtolower($data['order_data_by']) == 'asc') {
            $query->addSort( $data['sort_data_by'], $query::SORT_ASC);
        } elseif(isset($data['sort_data_by']) && isset($data['order_data_by']) && strtolower($data['order_data_by']) == 'desc') {
            $query->addSort( $data['sort_data_by'], $query::SORT_DESC);
        } elseif(!empty($data['sort_data_by'])) {
            $query->addSort( $data['sort_data_by'], $query::SORT_DESC);
        } elseif(isset($data['sort']) && isset($data['order']) && (isset($data['is_trending']) OR isset($data['is_latest']))) {
            $sort_arr = explode('.', $data['sort']);
            if(count($sort_arr)>1){
                $query->addSort( $sort_arr[1], strtolower($data['order']));
            }else {
                $query->addSort( $data['sort'], strtolower($data['order']));
            }
            if(isset($data['limit'])){
                $query->setStart(0)->setRows($data['limit']);
            }
        }

        // Grouping
        if(!empty($data['is_group']) && !empty($data['group_field']) &&  !empty($data['group_limit']) && !empty($data['group_sort_data_by']) ){
            //echo "in grouping";
            // get grouping component and set a field to group by
            $groupComponent = $query->getGrouping();
            $groupComponent->addField($data['group_field']);
            // maximum number of items per group
            $groupComponent->setLimit($data['group_limit']);
            // sort by in groups
            $query->addSort( $data['group_sort_data_by'], $query::SORT_DESC);
            // get a group count
            $groupComponent->setNumberOfGroups(true);
            $resultset = $client->select($query);
            $groups = $resultset->getGrouping();
        }
        $solr_result = $client->select($query);
        $arr_response = $solr_result->getData();
        $return = array(
            'solr_result' => $solr_result,
            'sql_and'     => $sql_and,
            'sql_and_facet' => $sql_and_facet,
            'response'    => isset($arr_response['response'])?$arr_response['response']:''
        );

        if(!empty($data['is_facet'])) {
            if(!empty($data['facet_field'])){
                $facet_result_arr = array();
                foreach ($facet_result as $value => $count) {
                    $facet_result_arr[$value] = $count;
                }
                $return['result_facet_field'] = $facet_result_arr;
            }else{
                $return['facets'] =  $arr_response['facets'];
            }
        }
        if(!empty($data['select_fields'])) {
            $result_select_fields = array();
            foreach ($data['select_fields'] as $fields) {
                foreach ($solr_result as $document) {
                    $result_select_fields[$document->id][$fields] = $document->$fields;
                }
            }
            $return['result_select_fields'] = $result_select_fields;
        }
        if( !empty($data['is_group']) && !empty($data['group_field']) &&  !empty($data['group_limit']) && !empty($data['group_sort_data_by']) ){
            $group_result_arr = array();
            foreach ($groups as $groupKey => $fieldGroup) {
                foreach ($fieldGroup as $valueGroup) {
                    if(!empty($data['group_get_p_id']) && $data['group_get_p_id'] == 1 ){
                        foreach ($valueGroup as $document) {
                            foreach ($document as $field => $value) {
                                if($field == 'id') {
                                    $group_result_arr[] = $value;
                                }
                            }
                        }
                    } else {
                        $group_result_arr[(int)$valueGroup->getValue()] = $valueGroup->getDocuments();
                    }
                }
            }
            $return['result_group_field'] = $group_result_arr;
        }
        return $return;
    }

    /**
     *
     */
    private function _getJsoNFacetStringForSolrRequest() {

       // $j = '{filter_group_df_1:{type:terms, field:filter_group_df_1},filter_group_df_2:{type:terms, field:filter_group_df_2},filter_group_df_80:{type:terms, field:filter_group_df_80} }';
        $model_solr_product = new model_solr_product($this->registry);

        $filter_groups = $model_solr_product->getAllFilterGroupsFromDB();
        $dynamic_field_slug = 'filter_group_df_';
        $arr_facet_json = array();
        if ( count($filter_groups ) > 0) {

            // sor_type
            $arr_facet_json['sor_type'] = array(
                    'type' => 'terms',
                    'field' => 'sor_type'
                );

            // exclusive
            $arr_facet_json['exclusive'] = array(
                    'type' => 'terms',
                    'field' => 'exclusive'
                );

            foreach ($filter_groups as $filter_group) {
                $arr_facet_json[$dynamic_field_slug.$filter_group['filter_group_id']] = array(
                    'type' => 'terms',
                    'field' => $dynamic_field_slug.$filter_group['filter_group_id']
                );
            }
        }

        if (count($arr_facet_json) > 0) {
           return json_encode($arr_facet_json);
        } else {
            return 0;
        }



    }
    /**
     * get filter group data fro a filter
     * @param $filter_id
     * @return int
     */
     public function getFilterGroup($filter_id) {

        if ($filter_id > 0) {
            $sql = "SELECT filter_group_id FROM " . DB_PREFIX . "filter WHERE filter_id = " . (int)$filter_id;

            // Create DB object
            $db = new Database\DB( DB_SERVERS );
            $row = $db->query($sql);

            if (isset($row->row['filter_group_id']) && $row->row['filter_group_id'] > 0){
                $filter_group_id = $row->row['filter_group_id'];
            } else {
                $filter_group_id = 0;
            }
        }

        return $filter_group_id;

    }

    /**
     * Get Filter Data
     */
    public function getFilterDetail($filter_id) {
        if ($filter_id > 0) {
            $sql = "
                    SELECT 
                        flt.filter_group_id, 
                        fd.filter_id, 
                        fd.name ,
                        flt.image 
                    FROM 
                        " . DB_PREFIX . "filter_description fd
                    INNER JOIN ".DB_PREFIX."filter flt ON flt.filter_id = fd.filter_id
                    WHERE 
                        fd.filter_id = " . (int)$filter_id;

            $row = $this->registry->db->query($sql);
            if (isset($row->row['filter_group_id']) && $row->row['filter_group_id'] > 0) {
                $filter_data = $row->row;
            } else {
                $filter_data = '';
            }
        }

        return $filter_data;
    }


    /**
     * @param $seller_id
     */
    public function copySellerProductToSor($seller_id){

        $sql = "SELECT mp.product_id
                FROM ".DB_PREFIX."ms_product mp
                INNER JOIN ".DB_PREFIX."product p ON p.product_id = mp.product_id
                LEFT JOIN ".DB_PREFIX."product_to_store p2s ON p2s.product_id = mp.product_id
                WHERE seller_id = ".(int)$seller_id."
                AND p.is_single = 0
                AND p2s.store_id=0";

        $query = $this->registry->db->query($sql);

        if(count($query->rows) > 0){
            $arr_product = array();
            foreach($query->rows as $row){
                $arr_product[] = $row['product_id'];
            }
            $this->copyProductIdsToSor($arr_product, 1, $seller_id);
        }

    }

    /**
     * @param array of product_id
     * @param $sync_to_solr
     */
    public function copyProductIdsToSor($arr_product_ids, $sync_to_solr = 0, $seller_id = 0){

        if(is_array($arr_product_ids) && count($arr_product_ids) > 0){

            $arr_product = array();
            foreach($arr_product_ids as $product_id){

                $sql = "SELECT product_id FROM ". DB_PREFIX."product_to_store".
                        " WHERE product_id=".(int)$product_id .
                        " AND store_id = ". (int)SOR_STORE_ID;

                $query = $this->registry->db->query($sql);

                if(empty($query->row['product_id'])) {
                    $sql = "INSERT INTO " . DB_PREFIX . "product_to_store".
                            " SET product_id=". (int)$product_id.",
                              store_id =". (int)SOR_STORE_ID;
                    $this->registry->db->query($sql);
                    $arr_product[] = $product_id;
                }
            }
        }
    }
    public function removeSellerProductToSor($seller_id){

        $sql = "SELECT mp.product_id
                FROM ".DB_PREFIX."ms_product mp
                INNER JOIN ".DB_PREFIX."product p ON p.product_id = mp.product_id
                LEFT JOIN ".DB_PREFIX."product_to_store p2s ON p2s.product_id = mp.product_id
                WHERE seller_id = ".(int)$seller_id."
                AND p.is_single = 0
                AND p2s.store_id=0";

        $query = $this->registry->db->query($sql);

        if(count($query->rows) > 0){
            $arr_product = array();
            foreach($query->rows as $row){
                $arr_product[] = $row['product_id'];
            }
            $this->removeProductToSor($arr_product, 1, $seller_id);
        }
    }
    public function removeProductToSor($arr_product_ids, $sync_to_solr = 0, $seller_id = 0){
        if(is_array($arr_product_ids) && count($arr_product_ids) > 0){
            $arr_product_id = implode(',', $arr_product_ids);
            $sql = "DELETE FROM " . DB_PREFIX . "product_to_store".
                " WHERE product_id IN (". $arr_product_id.") AND
                store_id =". SOR_STORE_ID;
            $this->registry->db->query($sql);
        }
    }

		public function getAllChildCategories($cat_id) {
			$sql = "SELECT GROUP_CONCAT(category_id) AS child FROM oc_category where parent_id = '".(int)$cat_id."'";
			$query = $this->registry->db->query($sql);
			if($query->num_rows){
					return $query->row['child'];
			}
			return false;
		}
        
    public function getNicknameDelimitedFieldForSolr($value) {
        if(empty($value)) {
            return false;
        }
        $nickname_delimited_field = array(
                                          'nickname_delimited_key' => 'nickname_delimited',
                                          'nickname_delimited_value' => implode('', explode('_', $value))
                                         );
        
        return $nickname_delimited_field;
    }
    
    /**************************************************
    @description: Function to manage Wsb Purchased Inventory Searchability
                - checks if valid wsb Inventory
                - get all inventories with same designs
                - prioritise and calculates next eligible inventory
                - removes 'WSBJPS' from searchable of all inventories except JP
                - adds 'WSBJPS' in searchable of next eligible inventory (except JP)
    @params: $current_product_id: (int) product id whose data is modified
    @return: (boolean) true: if valid wpi and next inventory logic completed; false: if not valid wpi
    @author: Anurag Jain (9 July 2018)
    **************************************************/
    public static function manageWsbPurchasedInventorySearchability($current_product_id) {
        
        require_once(DIR_SYSTEM . 'library/cart.php');
        $db = new Database\DB( DB_SERVERS );
        
        // get product details
        $current_product_info = array();
        $current_product_info = self::getProductStockStatusValidatorFields($current_product_id, $db)[0];
        
        // check if product is valid wsb purchased inventory
        $wsb_purchased_product_match_array = self::checkValidWsbPurchasedProduct($current_product_info['model']);
        
        if(!empty($wsb_purchased_product_match_array[2])) { // index '2' is for design in regex match result
            
            // check current stock status
            $is_current_product_in_stock = self::getProductFinalStockStatus($current_product_info, $db);
                
            $current_product_design = $wsb_purchased_product_match_array[2];
            $wpi_same_design_product_ids = self::getWsbPurchasedProductsByDesignFromSolr($current_product_design);
            if(!empty($wpi_same_design_product_ids)) {
                
                $product_ids = implode(',', $wpi_same_design_product_ids);
                $wpi_same_design_products_info = self::getProductStockStatusValidatorFields($product_ids, $db);//echo "<pre>";print_r($wpi_same_design_products_info);die;
                
                if(!empty($wpi_same_design_products_info)) {
                    $next_searchable_inventory = array(); // this will now be displayed on site; when WSBJPS is searched
                    $current_design_jps_model = '';
                    foreach ($wpi_same_design_products_info as $key => $product_info) {
                        $wpi_product_match_array = self::checkValidWsbPurchasedProduct($product_info['model']);
                        
                        $product_city_code = $wpi_product_match_array[1];
                        $is_product_in_stock = self::getProductFinalStockStatus($product_info, $db);
                        
                        if($is_product_in_stock) {
                            
                            if((!empty(array_search($product_city_code, WSB_STOCK_DISPLAY_CITY_PRIORITY)))
                              && ((!empty($next_searchable_inventory['city_code']) 
                                && (array_search($next_searchable_inventory['city_code'], WSB_STOCK_DISPLAY_CITY_PRIORITY) > array_search($product_city_code, WSB_STOCK_DISPLAY_CITY_PRIORITY)))
                              || (empty($next_searchable_inventory)))) {
                                $next_searchable_inventory['city_code'] = $product_city_code;
                                $next_searchable_inventory['product_info'] = $product_info;
                            }
                        }
                        
                        if(strtolower($product_city_code) != 'jp') {
                            // remove WSBJPS from searchable for every found product except for JP
                            $update_type = 'remove';
                            self::updateWsbPurchasedProductSearchable($update_type, $product_info);
                        } else {
                            $current_design_jps_model = $product_info['model'];
                        }
                    }
                    
                    if(!empty($next_searchable_inventory) && strtolower($next_searchable_inventory['city_code']) != 'jp') {
                        if(!empty($current_design_jps_model)) {
                            $next_searchable_inventory['product_info']['current_design_jps_model'] = $current_design_jps_model;
                        }
                        // add WSBJPS in searchable for next valid instock inventory
                        $update_type = 'add';
                        self::updateWsbPurchasedProductSearchable($update_type, $next_searchable_inventory['product_info']);
                    }
                }
            }
            //echo "Next Inventory: <pre>";print_r($next_searchable_inventory);die;
            return true;
        }
        return false;
    }
    
    /**************************************************
    @description: Function to check if product is valid Wsb Purchased Inventory
    @params: $product_model: (string) model to check valid wpi
    @return: $matches: (array) includes city code and design; false: if not valid inventory
    @author: Anurag Jain (9 July 2018)
    **************************************************/
    public static function checkValidWsbPurchasedProduct($product_model) {
        if(preg_match(self::$wsb_purchased_inventory_regex, $product_model, $matches)) {
            return $matches;
        }
        return false;
    }
    
    /**************************************************
    @description: Function to check product's stock status in solr
    @params: (int) product_id
    @return: (boolean) true:if in stock; false: if out of stock
    @author: Anurag Jain (9 July 2018)
    **************************************************/    
    public static function getProductStockStatusSolr($product_id) {
        
        $client = new Solarium\Client(self::$solr_object);
        $query = $client->createSelect();
        
        $solr_sql = 'id:'.$product_id;
        $solr_sql .= ' AND '.self::$solr_stock_status_query;
        
        $query->setQuery($solr_sql);
        $solr_result = $client->select($query);
        if($solr_result->getNumFound()) {
            return true;
        }
        return false;
    }
    
    /**************************************************
    @description: function used to get same design products using design
    @params: $design: (string) design of product
    @return: (array) of product ids
    @author: Anurag Jain (9 July 2018)
    **************************************************/
    public static function getWsbPurchasedProductsByDesignFromSolr($design) {
        $resultset = array();
        
        $client = new Solarium\Client(self::$solr_object);
        $query = $client->createSelect();
        
        $solr_sql = 'model:*WSB*'.$design;
        $query->setQuery($solr_sql);
        $solr_result = $client->select($query);
        
        if($solr_result->getNumFound()) {
            foreach ($solr_result as $document) {
                $resultset[] = $document->id;
            }
        }
        return $resultset;
    }
    
    /**************************************************
    @description: function used to get combined stock status (from DB and SOLR)
    @params: $product_info: (array) product info(including fields used to check stock status) with product_id and model
    @return: (boolean) true: if product is in stock; false: if product is out of stock
    @author: Anurag Jain (9 July 2018)
    **************************************************/
    public static function getProductFinalStockStatus($product_info, $db) {
        // For DB
        $stock_status_db = true;
        if(empty($product_info['total_associates'])) { // normal product
            $stock_status_info = Cart::getProductStockStatus($product_info);
            if( isset($stock_status_info['stock'])
                && $stock_status_info['stock'] === false ) {
                $stock_status_db = false;
            }
        } else if(isset($product_info['total_associates'])
                    && $product_info['total_associates'] == 1) { // combo product with only one associate product, so mark it as out of stock 
            $stock_status_db = false;
        } else if(isset($product_info['total_associates'])) { // combo product with multiple associate products 
            $combo_product_stock_status = Cart::getComboProductStockStatus($product_info['product_id'], $db);
            if ($combo_product_stock_status == false) {
                $stock_status_db = false;
            }
        }
        
        // For SOLR
        $stock_status_solr = self::getProductStockStatusSolr($product_info['product_id']); 
        
        if($stock_status_db == false || $stock_status_solr == false) {
            return false; // product is out of stock
        }
        return true;
    }
    
    /**************************************************
    @description: function used to update searchable field of product in solr 
    @params: $update_type: (string) 'add' or 'remove'
    @author: Anurag Jain (9 July 2018)
    **************************************************/
    public static function updateWsbPurchasedProductSearchable($update_type, $product_info) {
        
        $update_data = array();
        $update_data['product_id'] = $product_info['product_id'];
        $update_data['skip_filter_groups'] = 1;
        $update_data['skip_same_function_call'] = 1;
        $update_data['fields']['model'] = $product_info['model'];
        
        if($update_type == 'add') {
            $searchable_update_array = array();
            if(!empty($product_info['current_design_jps_model'])) {
                $searchable_update_array[] = $product_info['current_design_jps_model'];
            }
            $searchable_update_array[] = self::$wpi_search_keyword;
            $update_data['fields']['searchable'] = $searchable_update_array;
        }
        self::atomicUpdateToSolr($update_data);
        return true;
    }
    
	/**************************************************
	@description: function will get fields used to check a product's stock status
	@params: $product_ids: comma separated product ids 
             $db: db object
	@return: (array) product fields
	@author: Anurag Jain (11 July 2018)
	**************************************************/
	public static function getProductStockStatusValidatorFields($product_ids, $db) {
        
        if(empty($product_ids)) {
            return false;
        }
        
		$sql = "SELECT 
					p.product_id,
					p.model,
					p.sku,
					p.is_archived,
					p.tax_class_id,
					p.hsn_code,
					p.stock_status_id,
					p.quantity,
					p.status,
					p.piece_in_set,
					p.date_available,
					p.store_sales,
					p.is_associate,
                    count(opa.product_id) as total_associates,
					mp.seller_id,
					ms.vacation_mode,
					ms.seller_status
				FROM " . DB_PREFIX . "product p
                    LEFT JOIN " . DB_PREFIX . "product_to_associate opa 
                        ON p.product_id = opa.product_id 
					INNER JOIN " . DB_PREFIX . "ms_product mp 
						ON (mp.product_id = p.product_id) 
					INNER JOIN " . DB_PREFIX . "ms_seller ms 
						ON (mp.seller_id = ms.seller_id) 
				WHERE p.product_id IN (" . $product_ids . ")
                GROUP BY p.product_id";
		
		$query = $db->query($sql);
        if($query->num_rows) {
            return $query->rows;
        }
        return false;
	}
    
    /**************************************************
    @description: function will remove Top Most Parent Categories from given categories
    @params: $category_ids: (string) comma separated category ids
    @return: (string) comma separated category ids
    @author: Anurag Jain (30 July 2018)
    **************************************************/
    public function removeTopMostParentCategories($category_ids) {
        if(empty($category_ids)) {
            return '';
        }
        $sql = "SELECT GROUP_CONCAT(DISTINCT(category_id)) AS category_ids
                FROM ". DB_PREFIX ."category
                WHERE parent_id <> '0'
                    AND category_id IN (". $category_ids .")";
        
        $query = $this->registry->db->query($sql);
        if($query->num_rows) {
            return $query->row['category_ids'];
        }
        return '';
    }

    /**
     * @param  string $filter_string comma separated filter ids
     */
    public function getFilterDetailsWithGroup( string $filter_string )
    {
        $sql = "SELECT 
                        f.filter_group_id, 
                        GROUP_CONCAT(fd.name SEPARATOR ',') as filter_names,
                        GROUP_CONCAT(f.filter_id SEPARATOR ',') as filter_ids
                      FROM  " . DB_PREFIX . "filter f
                        INNER JOIN " . DB_PREFIX . "filter_description fd
                            ON fd.filter_id = f.filter_id
                      WHERE f.filter_id IN (" . $filter_string . ")
                        AND fd.language_id = '1'
                      GROUP BY filter_group_id";

        $filter_result = $this->registry->db->query($sql);
        
        if( $filter_result->num_rows ) {
            return $filter_result->rows;
        }
        return false;
    }

    /**
     * gets page filters by using current filter facets and categories
     * @param  array  $current_filter_facets
     * @param  $category_ids
     * @return array  page_filters
     * @author Anurag Jain, 16 Feb 2019
     */
    public function getPageFilters( array $current_filter_facets, $category_ids ): array
    {
        $page_filters = array();
        
        if ( empty( $current_filter_facets ) || empty( $category_ids )) {
            return $page_filters;
        }

        $categorywise_all_page_filters = $this->getAllPageFilters();

        $page_filters = $this->getCurrentCategoryPageFilters( $current_filter_facets, $category_ids );

        return $page_filters;
    }

    /**
     * returns all page filters using json file system/library/cache/category_filter_csv/category_filters_array.json
     * @return array
     * @author Anurag Jain, 16 Feb 2019
     */
    public function getAllPageFilters()
    {
        $this->_categorywise_all_page_filters = json_decode( file_get_contents(  DIR_CACHE . 'category_filter_csv/category_filters_array.json' ), true );
        return $this->_categorywise_all_page_filters;
    }

    /**
     * calculates current page filters with facets
     * @param  array  $current_filter_facets
     * @param  array  $category_ids
     * @return array
     * @author Anurag Jain, 16 Feb 2019
     */
    public function getCurrentCategoryPageFilters( array $current_filter_facets, array $category_ids ): array
    {   
        $current_category_all_page_price = array();
        $current_category_all_page_ratings = array();
        $current_category_all_page_filters = array();

        // set price page filters
        if ( !empty( $current_filter_facets['price']['all_price_facets'] )) {
            $all_price_facets = $current_filter_facets['price']['all_price_facets'];
            $current_category_all_page_price = $this->getPricePageFilters( $all_price_facets, $category_ids );
        }


        // set rating page filters
        if ( !empty( $current_filter_facets['rating'] )) {
            $current_category_all_page_ratings = $this->getRatingPageFilters( $current_filter_facets['rating'] );
        }

        // set other page filters
        if ( !empty( $category_ids )) {
            $current_category_all_page_filters = $this->getNormalPageFilters( $current_filter_facets, $category_ids );
        }

        // set rating filter above normal filters
        if ( !empty( $current_category_all_page_ratings )) {
            $priority_rating[] = $current_category_all_page_ratings;
            $current_category_all_page_filters = array_merge( $priority_rating, $current_category_all_page_filters );
        } 

        // set price filter above all
        if ( !empty( $current_category_all_page_price )) {
            $priority_price[] = $current_category_all_page_price;
            $current_category_all_page_filters = array_merge( $priority_price, $current_category_all_page_filters );
        } 
        
        $current_category_all_page_filters = array_values( $current_category_all_page_filters );

        return $current_category_all_page_filters;
    }

    /**
     * @param  array  $all_price_facets
     * @return array
     * @author Anurag Jain, 23 Feb 2019
     */
    public function getPricePageFilters( array $all_price_facets, array $category_ids ): array
    {
        $current_category_all_page_price = array();
        $current_category_all_page_prices_temp = array();

        $total_product_count = array_sum( $all_price_facets );

        $current_category_all_page_prices_temp_count = 5;
        $price_range_min_gap = !empty( $this->_categorywise_all_page_filters[ $category_ids[0]]['price_range_gap'] ) 
                                ? (int) $this->_categorywise_all_page_filters[ $category_ids[0]]['price_range_gap']
                                : 300;

        ksort( $all_price_facets );
        
        $per_range_product_count = ceil( $total_product_count / $current_category_all_page_prices_temp_count );

        $per_range_product_count_temp = $per_range_product_count;
        
        $current_category_all_page_prices_temp = array();
        $is_new_range = 1;
        $current_range_key = 0;
        $nearest_round_of = 100;

        foreach ( $all_price_facets as $price_key => $prod_count ) {

            if ( $current_category_all_page_prices_temp_count && $per_range_product_count_temp ) {
                
                // set new range mininum price
                if ( $is_new_range ) {

                    $current_category_all_page_prices_temp[ $current_range_key ]['min'] = isset( $current_category_all_page_prices_temp[ $current_range_key - 1 ]['max'] ) 
                                                                    ? $current_category_all_page_prices_temp[ $current_range_key - 1 ]['max'] + 1 // last price range max + 1
                                                                    : floor( $price_key / $nearest_round_of ) * $nearest_round_of ; // round of to nearest 100 (lower side)
                    $is_new_range = 0;
                }

                $per_range_product_count_temp -= $prod_count;

                // set max range
                if ( $per_range_product_count_temp <= 0 ) {

                    $max_price = ceil( $price_key / $nearest_round_of ) * $nearest_round_of;

                    if ( ((int)$max_price - (int)$current_category_all_page_prices_temp[ $current_range_key ]['min']) >= $price_range_min_gap ) {
                        $current_category_all_page_prices_temp[ $current_range_key ]['max'] = $max_price;
                        $per_range_product_count_temp = $per_range_product_count;
                        $is_new_range = 1;
                        $current_category_all_page_prices_temp_count--;
                        $current_range_key++;
                    }
                }
            }
        }

        // set max range set for last range
        if ( isset( $current_category_all_page_prices_temp[ $current_range_key ]['min'] ) && !isset( $current_category_all_page_prices_temp[ $current_range_key ]['max'] )) {

            if (  ((int)$price_key - (int)$current_category_all_page_prices_temp[ $current_range_key ]['min']) >= $price_range_min_gap  ) {
                
                $max_price = ceil( $price_key / $nearest_round_of ) * $nearest_round_of;
                $current_category_all_page_prices_temp[ $current_range_key ]['max'] = $max_price;

            } else {
                $current_category_all_page_prices_temp[ $current_range_key - 1 ]['max'] = $max_price;
                unset( $current_category_all_page_prices_temp[ $current_range_key ] );
            }
        }

        // set range labels
        if ( !empty( $current_category_all_page_prices_temp )) {
            
            foreach ( $current_category_all_page_prices_temp as $key => $range_details ) {
                
                $min = $range_details['min'];
                $max = $range_details['max'];

                if ( $this->registry->currency->getCode() == 'INR' ) {
                    $left_symbol = "₹";
                } else {
                    $left_symbol = $this->registry->currency->getSymbolLeft();
                }

                if( $this->registry->config->get('config_store_id') == INTERNATIONAL_STORE_ID ) {

                    $min = $this->registry->currency->convert( $min, 'INR', $this->registry->currency->getCode() );
                    $max = $this->registry->currency->convert( $max, 'INR', $this->registry->currency->getCode() );


                    if ( $key == 0 ) {
                        $min = floor( $min );
                    }

                    if ( $key == ( count( $current_category_all_page_prices_temp )-1 )) {
                        $max = ceil( $max );
                    }

                    $min = floor( $min );
                    $max = floor( $max );

                    $current_category_all_page_prices_temp_name = $left_symbol.$min . " - " . $left_symbol.$max;

                    if ( $key != 0 ) {
                        $min += 1;
                        $current_category_all_page_prices_temp_name = $left_symbol.$min . " - " .$left_symbol.$max;
                    }

                } else {
                
                    $current_category_all_page_prices_temp_name = $left_symbol.$min . " - " .$left_symbol.$max;
                }

                if ( $key == 0 ) {
                    $current_category_all_page_prices_temp_name = "Less than " . $left_symbol.$max;
                }
                if ( $key == ( count( $current_category_all_page_prices_temp )-1 )) {
                    $current_category_all_page_prices_temp_name = "More than " . $left_symbol.( $min - 1 );
                }

                $current_category_all_page_prices_temp[ $key ]['name'] = $current_category_all_page_prices_temp_name;
                $current_category_all_page_prices_temp[ $key ]['value'] = $min . "-" .$max;

                unset( $current_category_all_page_prices_temp[ $key ]['min'] );
                unset( $current_category_all_page_prices_temp[ $key ]['max'] );
            }
        }

        if ( count( $current_category_all_page_prices_temp ) > 1 ) {

            $current_category_all_page_price['group_label'] = 'Price';
            $current_category_all_page_price['filter_type'] = 'price';
            $current_category_all_page_price['price'] = array_values( $current_category_all_page_prices_temp );
        }

        return $current_category_all_page_price;
    }

    /**
     * @param  array $current_filter_facets_rating
     * @return array      
     * @author Anurag Jain, 23 Feb 2019           
     */
    public function getRatingPageFilters( array $current_filter_facets_rating ) {

        $current_category_all_page_ratings = array();
        $current_category_all_page_ratings_temp = array();

        foreach ( $current_filter_facets_rating as $rating_key => $rating_product_count ) {

            if ( $rating_key != '0.0' && (int) $rating_product_count > 0 ) {
                $current_category_all_page_ratings_temp['rating'][] = array(
                                                                     'name' => $this->_ratings_label[ $rating_key ] ?? $rating_key,
                                                                     'value' => $rating_key
                                                                    );
            }
        }

        if ( !empty( $current_category_all_page_ratings_temp['rating'] ) && count( $current_category_all_page_ratings_temp['rating'] ) > 1 ) {

            $current_category_all_page_ratings['group_label'] = 'Rating';
            $current_category_all_page_ratings['filter_type'] = 'rating';
            $current_category_all_page_ratings['rating'] = array_values( $current_category_all_page_ratings_temp['rating'] );
        }

        return $current_category_all_page_ratings;
    }

    /**
     * @param  array $current_filter_facets 
     * @param  array $category_ids          
     * @return array      
     * @author Anurag Jain, 23 Feb 2019                   
     */
    public function getNormalPageFilters( array $current_filter_facets, array $category_ids ): array 
    {

        $current_category_all_page_filters = array();
        
        foreach ( $category_ids as $key => $category_id ) {

            if ( !empty( $this->_categorywise_all_page_filters[ $category_id ]['filter_group_ids'] )) {

                foreach ( $this->_categorywise_all_page_filters[ $category_id ]['filter_group_ids'] as $key => $filter_group_id ) {

                    if ( isset( $current_filter_facets['filters'][ $filter_group_id ] )) {

                        // set group key
                        if ( !isset( $current_category_all_page_filters[ $filter_group_id ] )) {

                            $current_category_all_page_filters[ $filter_group_id ] = array(
                                                                                        'filter_group_id' => $filter_group_id,
                                                                                        'group_label' => $current_filter_facets['filters'][ $filter_group_id ]['group_label'],
                                                                                        'filter_type' => 'filter'
                                                                                    );


                            $product_count_arr = array();

                            foreach ( $current_filter_facets['filters'][ $filter_group_id ]['filter'] as $key => $row ) {
                                $product_count_arr[ $key ] = $row['product_count'];
                            }

                            array_multisort( $product_count_arr, SORT_DESC, $current_filter_facets['filters'][ $filter_group_id ]['filter'] );
                            
                            $current_category_all_page_filters[ $filter_group_id ]['filter'] = array_slice( $current_filter_facets['filters'][ $filter_group_id ]['filter'], 0, 5 );
                        }
                    }

                    // if no filters then remove group too
                    if ( !empty( $current_category_all_page_filters[ $filter_group_id ]['filter'] ) && count( $current_category_all_page_filters[ $filter_group_id ]['filter'] ) > 1 ) {
                        $current_category_all_page_filters[ $filter_group_id ]['filter'] = array_values( $current_category_all_page_filters[ $filter_group_id ]['filter'] );
                    } else {
                        unset( $current_category_all_page_filters[ $filter_group_id ] );
                    }
                }
            }
        }

        return $current_category_all_page_filters;
    }
}
