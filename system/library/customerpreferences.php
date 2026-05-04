<?php
class CustomerPreferences {

    private $_db;
    private $_registry;
    private $_load;
    private $_config;

    public function __construct( $registry ) {
        
        $this->_registry = $registry;

        if ( method_exists( $registry, 'get' )) {
            $this->_db = $registry->get('db');
            $this->_load = $registry->get('load');
            $this->_config = $registry->get('config');
        } else {
            $this->_db = $registry->db;
            $this->_load = $registry->load;
            $this->_config = $registry->config;
        }
    }

    /**
     * @param  array  $raw_pref_arr [description]
     * @return array              [description]
     * @author Anurag Jain, 12th Aug 2019
     */
    public function prepareCategoryWiseAlgoPreferences( array $raw_pref_arr ): array
    {
        if ( empty( $raw_pref_arr )) {
            return array();
        }

        $final_prefs_arr = array();

        foreach ( $raw_pref_arr as $key => $raw_data ) {

            $final_prefs_arr[ $raw_data['pref_type']]
                            [ $raw_data['category_id']] 
                            [ $raw_data['criteria_type']] 
                            [ $raw_data['criteria_type_value']] = array(
                                                                    'min_price' => $raw_data['min_price'] ?? "0",
                                                                    'max_price' => $raw_data['max_price'] ?? "0",
                                                                    'pieces_count' => $raw_data['pieces_count'] ?? "0",
                                                                    'total_amount' => $raw_data['total_amount'] ?? "0",
                                                                    'product_like_count' => $raw_data['product_like_count'] ?? "0",
                                                                    'product_dislike_count' => $raw_data['product_dislike_count'] ?? "0"
                                                                );

        }

        return $final_prefs_arr;
    }
    
    /**
     * @param  int    $customer_id  
     * @param  string $category_ids, comma separated
     * @param  string $source          
     * @param  array $preference_type 
     * @return array                  
     * @author Anurag Jain, 12th Aug 2019
     */
    public function getCustomerPreferencesByPreferenceType( int $customer_id, string $category_ids = '', string $source = 'algo', 
                                                            array $preference_type = array(), bool $formatted = false ): array
    {
        $sql = "
                SELECT 
                    category_id,
                    criteria_type,
                    criteria_type_value,
                    pref_type,
                    min_price,
                    max_price,
                    pieces_count,
                    total_amount,
                    product_like_count,
                    product_dislike_count
                FROM " . DB_PREFIX . "customer_preferences_all
                WHERE 
                    customer_id = '". (int) $customer_id ."' ";
        
        $sql .= " 
                AND source = '". $source ."'
                AND criteria_type IN ( 'seller', 'brand_filter', 'rating', 'NA' ) ";

        if ( !empty( $preference_type )) {
            $sql .= " AND pref_type IN ( '". implode( "','", $preference_type ) ."' )";
        } else {
            $sql .= " AND pref_type IN ( 'order', 'shortlist', 'product_review', 'return', 'NA' )";
        }

        if ( !empty( $category_ids )) {
            $sql .= " AND category_id IN ( ". $this->_db->escape( $category_ids ) ." )";
        }

        $pref_result = $this->_db->query( $sql );

        if( $pref_result->num_rows ) {

            if ( $formatted ) {
                return $this->prepareCategoryWiseAlgoPreferences( $pref_result->rows );
            }
            return $pref_result->rows;
        }

        return array();
    }

    /**
     * checks Preference Existence By specific Source 
     * @param  int    $customer_id [description]
     * @param  string $source      [description]
     * @return bool              [description]
     * @author Anurag Jain, 19th Aug 2019
     */
    public function checkPreferenceExistenceBySource( int $customer_id, string $source ): bool
    {
        $sql = "SELECT 
                    customer_id
                FROM " . DB_PREFIX . "customer_preferences_all
                WHERE customer_id = '". $customer_id ."'
                    AND source = '". $this->_db->escape( $source ) ."'
                LIMIT 1";

        $pref_query = $this->_db->query( $sql );

        if( $pref_query->num_rows ) {
            return true;
        }
        return false;
    }
    
    /**
     * gets categories in customers preference
     * @param  array  $customer_ids_array       [description]
     * @param  string $source        [description]
     * @param  string $pref_type [description]
     * @return array
     * @author: Anurag Jain (19 July 2018)
     */
    public function getCustomerPreferenceCategories( array $customer_ids_array, string $source = 'algo' , string $pref_type = '' ): array
    {

        $customer_category_array = array();

        $sql = "SELECT 
                    GROUP_CONCAT( DISTINCT( category_id )) AS category_ids, 
                    customer_id
                FROM " . DB_PREFIX . "customer_preferences_all
                WHERE customer_id IN (". implode( ',', $customer_ids_array ) .") ";

        if( !empty( $source )) {
            $sql .= " AND source = '". $this->_db->escape( $source ) ."' ";
        } else {
            $sql .= " AND source IN ('cust', 'algo', 'crm', 'admin') ";
        }

        $sql .= " AND criteria_type IN ('seller', 'brand_filter', 'rating', 'NA') ";

        if( !empty( $pref_type )) {
            $sql .= " AND pref_type = '". $this->_db->escape( $pref_type ) ."' ";
        } else {
            $sql .= " AND pref_type IN ('order', 'shortlist', 'product_review', 'return', 'NA') ";
        }

        $sql .= " GROUP BY customer_id ORDER BY NULL";
        $result = $this->_db->query( $sql );

        if( $result->num_rows ) {
            foreach ( $result->rows as $key => $preferences_data ) {
                $customer_category_array[$preferences_data['customer_id']] = $preferences_data['category_ids'];
            }
        }

        return $customer_category_array;
    }

    /**
     * formats Product details To customer category seller wise Preferences
     * @param  array  $eligible_products [description]
     * @return array                    [description]
     * @author: Anurag Jain, 20 July 2018
     */
    public function formatProductsToPreference( array $eligible_products ): array
    {
        if ( empty( $eligible_products )) {
            return array();
        }

        $preference_array = array();
        $preference_criteria_array = array(
                                        "seller",
                                        "rating"
                                    );
        $products_for_rating = array_unique( array_column( $eligible_products , 'product_id' ));
        if ( !empty( $products_for_rating )) {
            $products_to_rating = $this->getBulkProductsRatingFromDB( $products_for_rating );
        }

        foreach ( $eligible_products as $product_detail ) {

            $customer_id     = (int) $product_detail['customer_id'];
            $category_id     = (int) $product_detail['category_id'];
            $seller_id       = (int) $product_detail['seller_id'];
            $quantity        = (int) $product_detail['quantity'];
            $piece_in_set    = (int) $product_detail['piece_in_set'];
            $price_per_piece = (int) $product_detail['price_per_piece'];
            $product_id      = (int) $product_detail['product_id'];

            foreach ( $preference_criteria_array as $criteria_type ) {

                // Seller pref data manipulation (also handles if multiple products for same seller in same category)
                if ( $criteria_type == "seller" ) {

                    $final_min_price = $final_max_price = $price_per_piece;
                    $final_pieces_count = $quantity;
                    $final_total_amount = $quantity * $piece_in_set * $price_per_piece;

                    if( isset( $preference_array[$customer_id][$category_id][$criteria_type][$seller_id] )) {

                        $min_price = (int) $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['min_price'];
                        $max_price = (int) $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['max_price'];

                        if( $price_per_piece > $min_price ) {
                            $final_min_price = $min_price;
                        }
                        if( $price_per_piece < $max_price ) {
                            $final_max_price = $max_price;
                        }
                        $final_pieces_count += (int) $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['pieces_count'];
                        $final_total_amount += (int) $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['total_amount'];
                    }

                    $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['min_price']    = $final_min_price;
                    $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['max_price']    = $final_max_price;
                    $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['pieces_count'] = $final_pieces_count;
                    $preference_array[$customer_id][$category_id][$criteria_type][$seller_id]['total_amount'] = $final_total_amount;    
                }

                // Rating pref for category data manipulation according to product rating 
                if ( $criteria_type == "rating" ) {

                    $final_pieces_count = $quantity;
                    $final_total_amount = $quantity * $piece_in_set * $price_per_piece;

                    $product_rating = isset( $products_to_rating[ $product_id ][ $category_id ] ) 
                                        ? (int) $products_to_rating[ $product_id ][ $category_id ] 
                                        : 0;

                    if ( !empty( $product_rating ) && in_array( $product_rating , array( 3, 4, 5 ))) {

                        if( isset( $preference_array[$customer_id][$category_id][$criteria_type][$product_rating] )) {

                            $final_pieces_count += (int) $preference_array[$customer_id][$category_id][$criteria_type][$product_rating]['pieces_count'];
                            $final_total_amount += (int) $preference_array[$customer_id][$category_id][$criteria_type][$product_rating]['total_amount'];
                        }
                        
                        $preference_array[$customer_id][$category_id][$criteria_type][$product_rating]['pieces_count'] = $final_pieces_count;
                        $preference_array[$customer_id][$category_id][$criteria_type][$product_rating]['total_amount'] = $final_total_amount;
                    }
                }
            }
        }
        return $preference_array;
    }

    /**
     * gets Bulk Products Rating From Solr
     * @param  array  $product_ids [description]
     * @return array              [description]
     */
    public function getBulkProductsRatingFromSolr( array $product_ids ): array
    {
        if ( empty( $product_ids )) {
            return array();
        }

        $product_to_rating = array();

        $product_ids = implode( ' OR ' , $product_ids );
        $config_solr = $this->_config->solrConfig();

        $client = new Solarium\Client( $config_solr );
        $sql = 'id:( ' . $product_ids . ' )';

        $query = $client->createSelect();
        $query->setQuery( $sql );

        $solr_result = $client->select( $query );

        if( $solr_result->getNumFound() ) {
            foreach ( $solr_result->getDocuments() as $key => $value ) {
                $product_to_rating[$value->getFields()['id']] = $value->getFields()['rating'] ?? "0";
            }
        }

        return $product_to_rating;
    }

    /**
     * gets Bulk Products Rating From DB
     * @param  array  $product_ids [description]
     * @return array              [description]
     */
    public function getBulkProductsRatingFromDB( array $product_ids ): array
    {
        if ( empty( $product_ids )) {
            return array();
        }
        $product_ids = implode( "','" , $product_ids );

        $sql = "
                SELECT 
                    omp.product_id,
                    p2c.category_id,
                    COALESCE( orr2.rating, orr1.rating ) as rating
                FROM 
                    ". DB_PREFIX ."ms_product omp
                    JOIN ". DB_PREFIX ."product_to_category p2c ON p2c.product_id = omp.product_id
                    JOIN ". DB_PREFIX ."review_rules orr1 ON orr1.seller_id = omp.seller_id AND orr1.rule_type IN ( 'global')
                    LEFT JOIN ". DB_PREFIX ."review_rules orr2 
                        ON orr2.seller_id = omp.seller_id AND orr2.rule_type IN ( 'category') AND orr2.category_id = p2c.category_id
                WHERE 
                    omp.product_id IN ('". $product_ids ."')
                GROUP BY omp.product_id, p2c.category_id
                ORDER BY NULL ";
        $result = $this->_db->query( $sql );

        if ( !$result->num_rows ) {
            return array();
        }

        $product_to_rating = array();
        foreach ( $result->rows as $key => $data ) {
            $product_to_rating[$data['product_id']][$data['category_id']] = $data['rating'];
        }

        return $product_to_rating;
    }
    
    /**
     * @param  string $source      [description]
     * @param  string $pref_type   [description]
     * @param  string $customer_id [description]
     * @return bool              [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function deleteOldPreference( string $source, string $pref_type, string $customer_id = '', string $operation_type = 'QUICK' ): bool
    {
        $sql = "DELETE ". $operation_type ." FROM ". DB_PREFIX ."customer_preferences_all ";

        $where = array();
        if ( !empty( $source )) {
            $where[] = " source = '". $this->_db->escape( $source ) ."' ";
        }
        if ( !empty( $pref_type )) {
            $where[] = " pref_type = '". $this->_db->escape( $pref_type ) ."' ";
        }
        if ( !empty( $customer_id )) {
            $where[] = " customer_id IN ( ". $this->_db->escape( $customer_id ) ." ) ";
        }

        if ( !empty( $where )) {
            $sql .= " WHERE ".implode( " AND " , $where );
        }

        return $this->_db->query( $sql );
    }

    /**
     * updates/reset all algo prefs
     * @param  string $type         [description]
     * @param  string $customer_ids [description]
     * @return bool               [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function updateCustomerPreferences( string $type = 'update', string $customer_ids = '' ): bool
    {
        $start_date = '';
        $end_date = '';
        $order_limit = 0;

        if ( $type == 'update' ) {
            $yesterday_date = date( "Y-m-d", strtotime( "yesterday" ));
            $start_date = $yesterday_date . " 00:00:00";
            $end_date = $yesterday_date . " 23:59:59";
        }

        if ( $type == 'reset' ) {
            $order_limit = 5;
        }

        $update_order_prefs_result          = $this->updateOrderPreferences( $type, $customer_ids, $start_date, $end_date, $order_limit );
        $update_shortlist_prefs_result      = $this->updateShortlistPreferences( $type, $customer_ids, $start_date, $end_date );
        $update_product_review_prefs_result = $this->updateProductReviewPreferences( $type, $customer_ids, $start_date, $end_date );
        $update_return_prefs_result         = $this->updateReturnPreferences( $type, $customer_ids, $start_date, $end_date, $order_limit );

        // optimize changes
        $this->_db->query( "OPTIMIZE TABLE ". DB_PREFIX ."customer_preferences_all" );
        
        return $update_order_prefs_result && $update_shortlist_prefs_result && $update_product_review_prefs_result && $update_return_prefs_result;
    }

    /**
     * updates/reset order prefs
     * @param  string      $type         [description]
     * @param  string      $customer_ids [description]
     * @param  string      $start_date   [description]
     * @param  string      $end_date     [description]
     * @param  int|integer $order_limit  [description]
     * @return bool                    [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function updateOrderPreferences( string $type = 'update', 
                                            string $customer_ids = '', 
                                            string $start_date = '', 
                                            string $end_date = '', 
                                            int $order_limit = 0 ): bool
    {
        if ( empty( $customer_ids ) 
             && empty( $start_date )
             && empty( $end_date )) {
            return false;
        }

        if ( $type == 'reset' ) {
            $this-> deleteOldPreference( 'algo', 'order', $customer_ids );
        }

        $update_order_seller_sql = $this->getUpdateOrderPreferenceSQL( $type, 'seller', $customer_ids, $start_date, $end_date, $order_limit );
        $update_order_rating_sql = $this->getUpdateOrderPreferenceSQL( $type, 'rating', $customer_ids, $start_date, $end_date, $order_limit );

        return $this->_db->query( $update_order_seller_sql ) && $this->_db->query( $update_order_rating_sql );
    }

    /**
     * sql for updates/reset order prefs
     * @param  string      $type          [description]
     * @param  string      $criteria_type [description]
     * @param  string      $customer_ids  [description]
     * @param  string      $start_date    [description]
     * @param  string      $end_date      [description]
     * @param  int|integer $order_limit   [description]
     * @return string                     [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function getUpdateOrderPreferenceSQL( string $type = 'update', 
                                                 string $criteria_type = 'seller', 
                                                 string $customer_ids = '', 
                                                 string $start_date = '', 
                                                 string $end_date = '', 
                                                 int $order_limit = 0 ): string
    {
        if ( "update" ) {
            $base_table = DB_PREFIX . "order AS o";
        }

        if ( $type == "reset" ) {
            // customer ids
            if ( !empty( $customer_ids )) {
                $reset_sql .= " customer_id IN ( ". $this->_db->escape( $customer_ids ) ." )";
            } else {
                $reset_sql .= " customer_id <> 0";
            }
            $base_table = "( SELECT order_id, customer_id FROM ". DB_PREFIX ."order WHERE ". $reset_sql ." ORDER BY order_id DESC LIMIT ". (int) $order_limit . " ) AS o ";
        }

        if ( $criteria_type == "seller" ) {
            $criteria_type_value = "omp.seller_id";
            $extra_join = "";
            $last_group_by = "omp.seller_id";
        }

        if ( $criteria_type == "rating" ) {
            $criteria_type_value = "COALESCE(orr2.rating, orr1.rating) AS rating_calc";
            $extra_join = " 
                            JOIN ". DB_PREFIX ."review_rules orr1 ON orr1.seller_id = omp.seller_id AND orr1.rule_type = 'global' 
                            LEFT JOIN ". DB_PREFIX ."review_rules orr2 ON orr2.seller_id = omp.seller_id AND orr2.category_id = p2c.category_id AND orr2.rule_type = 'category' 
                          ";
            $last_group_by = "rating_calc";
        }

        $sql = "
                INSERT IGNORE INTO ". DB_PREFIX ."customer_preferences_all
                ( 
                    SELECT 
                        o.customer_id, 
                        p2c.category_id, 
                        'algo', 
                        '". $criteria_type ."', 
                        ". $criteria_type_value .", 
                        'order', 
                        MIN(op.selling_price), 
                        MAX(op.selling_price), 
                        SUM(oop.quantity),
                        SUM(oop.quantity * oop.piece_in_set * oop.price_per_piece),
                        0,0, 
                        NOW(), 
                        NOW() 
                    FROM ". $base_table ."
                        JOIN ". DB_PREFIX ."order_product AS oop ON oop.order_id = o.order_id
                        JOIN ". DB_PREFIX ."product_to_category p2c ON p2c.product_id = oop.product_id 
                        JOIN ". DB_PREFIX ."ms_product omp ON omp.product_id = oop.product_id 
                        JOIN ". DB_PREFIX ."product op ON op.product_id = oop.product_id 
                        ". $extra_join ."
                    WHERE ";

        // customer ids
        if ( !empty( $customer_ids )) {
            $sql .= " o.customer_id IN ( ". $this->_db->escape( $customer_ids ) ." )";
        } else {
            $sql .= " o.customer_id <> 0";
        }

        // duration
        if ( !empty( $start_date ) && !empty( $end_date )) {
            $sql .= " AND o.date_added >= '". $this->_db->escape( $start_date ) ."' AND o.date_added <= '". $this->_db->escape( $end_date ) ."'";
        }

        $sql .= " GROUP BY o.customer_id, p2c.category_id, ". $last_group_by ."
                )
                ON DUPLICATE KEY UPDATE 
                    min_price = LEAST(min_price, VALUES(min_price)), 
                    max_price = GREATEST(max_price, VALUES(max_price)),
                    pieces_count = pieces_count + VALUES(pieces_count),
                    total_amount = total_amount + VALUES(total_amount) ";

        return $sql;
    }

    /**
     * updates/reset shortlist prefs
     * @param  string $type         [description]
     * @param  string $customer_ids [description]
     * @param  string $start_date   [description]
     * @param  string $end_date     [description]
     * @return bool               [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function updateShortlistPreferences( string $type = 'update', 
                                                string $customer_ids = '', 
                                                string $start_date = '', 
                                                string $end_date = '' ): bool
    {
        if ( empty( $customer_ids ) 
             && empty( $start_date )
             && empty( $end_date )) {
            return false;
        }

        if ( $type == 'reset' ) {
            $this-> deleteOldPreference( 'algo', 'shortlist', $customer_ids );
        }

        $update_shortlist_seller_sql = $this->getUpdateShortlistPreferenceSQL( $type, 'seller', $customer_ids, $start_date, $end_date );
        $update_shortlist_rating_sql = $this->getUpdateShortlistPreferenceSQL( $type, 'rating', $customer_ids, $start_date, $end_date );

        return $this->_db->query( $update_shortlist_seller_sql ) && $this->_db->query( $update_shortlist_rating_sql );
    }

    /**
     * sql for updates/reset shortlist prefs
     * @param  string $type          [description]
     * @param  string $criteria_type [description]
     * @param  string $customer_ids  [description]
     * @param  string $start_date    [description]
     * @param  string $end_date      [description]
     * @return string                [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function getUpdateShortlistPreferenceSQL( string $type = 'update', 
                                                     string $criteria_type = 'seller', 
                                                     string $customer_ids = '', 
                                                     string $start_date = '', 
                                                     string $end_date = '' ): string
    {

        if ( $criteria_type == "seller" ) {
            $criteria_type_value = "omp.seller_id";
            $extra_join = "";
            $last_group_by = "omp.seller_id";
        }

        if ( $criteria_type == "rating" ) {
            $criteria_type_value = "COALESCE(orr2.rating, orr1.rating) AS rating_calc";
            $extra_join = " 
                            JOIN ". DB_PREFIX ."review_rules orr1 ON orr1.seller_id = omp.seller_id AND orr1.rule_type = 'global' 
                            LEFT JOIN ". DB_PREFIX ."review_rules orr2 ON orr2.seller_id = omp.seller_id AND orr2.category_id = p2c.category_id AND orr2.rule_type = 'category' 
                          ";
            $last_group_by = "rating_calc";
        }

        $sql = "
                INSERT IGNORE INTO ". DB_PREFIX ."customer_preferences_all
                ( 
                    SELECT 
                        cw.customer_id, 
                        p2c.category_id, 
                        'algo', 
                        '". $criteria_type ."', 
                        ". $criteria_type_value .", 
                        'shortlist', 
                        MIN(op.selling_price), 
                        MAX(op.selling_price), 
                        0,0,0,0, 
                        NOW(), 
                        NOW()
                    FROM ". DB_PREFIX ."customer_wishlist cw
                        JOIN ". DB_PREFIX ."product_to_category p2c ON p2c.product_id = cw.product_id 
                        JOIN ". DB_PREFIX ."ms_product omp ON omp.product_id = cw.product_id 
                        JOIN ". DB_PREFIX ."product op ON op.product_id = cw.product_id
                        ". $extra_join ."
                    WHERE ";

        // customer ids
        if ( !empty( $customer_ids )) {
            $sql .= " cw.customer_id IN ( ". $this->_db->escape( $customer_ids ) ." )";
        } else {
            $sql .= " cw.customer_id <> 0";
        }

        // duration
        if ( !empty( $start_date ) && !empty( $end_date )) {
            $sql .= " AND cw.date_added >= '". $this->_db->escape( $start_date ) ."' AND cw.date_added <= '". $this->_db->escape( $end_date ) ."'";
        }

        $sql .= " GROUP BY cw.customer_id, p2c.category_id, ". $last_group_by ."
                )
                ON DUPLICATE KEY UPDATE 
                    min_price = LEAST(min_price, VALUES(min_price)), 
                    max_price = GREATEST(max_price, VALUES(max_price)) ";

        return $sql;
    }

    /**
     * updates/reset product review prefs
     * @param  string $type         [description]
     * @param  string $customer_ids [description]
     * @param  string $start_date   [description]
     * @param  string $end_date     [description]
     * @return bool               [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function updateProductReviewPreferences( string $type = 'update', 
                                                    string $customer_ids = '', 
                                                    string $start_date = '', 
                                                    string $end_date = '' ): bool
    {
        if ( empty( $customer_ids ) 
             && empty( $start_date )
             && empty( $end_date )) {
            return false;
        }

        if ( $type == 'reset' ) {
            $this-> deleteOldPreference( 'algo', 'product_review', $customer_ids );
        }

        $update_product_review_seller_sql       = $this->getUpdateProductReviewPreferenceSQL( $type, 'seller', $customer_ids, $start_date, $end_date );
        $update_product_review_rating_sql       = $this->getUpdateProductReviewPreferenceSQL( $type, 'rating', $customer_ids, $start_date, $end_date );
        $update_product_review_brand_filter_sql = $this->getUpdateProductReviewPreferenceSQL( $type, 'brand_filter', $customer_ids, $start_date, $end_date );

        return $this->_db->query( $update_product_review_seller_sql ) && $this->_db->query( $update_product_review_rating_sql ) && $this->_db->query( $update_product_review_brand_filter_sql );
    }

    /**
     * sql for updates/reset product review prefs
     * @param  string $type          [description]
     * @param  string $criteria_type [description]
     * @param  string $customer_ids  [description]
     * @param  string $start_date    [description]
     * @param  string $end_date      [description]
     * @return string                [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function getUpdateProductReviewPreferenceSQL( string $type = 'update', 
                                                         string $criteria_type = 'seller', 
                                                         string $customer_ids = '', 
                                                         string $start_date = '', 
                                                         string $end_date = '' ): string
    {
        if ( $criteria_type == "seller" ) {
            $criteria_type_value = "omp.seller_id";
            $extra_join = "";
            $last_group_by = "omp.seller_id";
        }

        if ( $criteria_type == "rating" ) {
            $criteria_type_value = "COALESCE(orr2.rating, orr1.rating) AS rating_calc";
            $extra_join = " 
                            JOIN ". DB_PREFIX ."review_rules orr1 ON orr1.seller_id = omp.seller_id AND orr1.rule_type = 'global' 
                            LEFT JOIN ". DB_PREFIX ."review_rules orr2 ON orr2.seller_id = omp.seller_id AND orr2.category_id = p2c.category_id AND orr2.rule_type = 'category' 
                          ";
            $last_group_by = "rating_calc";
        }

        if ( $criteria_type == "brand_filter" ) {
            $criteria_type_value = "pf.filter_id";
            $extra_join = " 
                            JOIN ". DB_PREFIX ."product_filter pf ON pf.product_id = op.product_id
                            JOIN ". DB_PREFIX ."filter f ON f.filter_id = pf.filter_id
                            JOIN ". DB_PREFIX ."filter_group_description fgd ON fgd.filter_group_id = f.filter_group_id AND fgd.name = 'Brand Name' ";
            $last_group_by = "pf.filter_id";
        }

        $sql = "
                INSERT IGNORE INTO ". DB_PREFIX ."customer_preferences_all
                ( 
                    SELECT 
                        o.customer_id, 
                        p2c.category_id, 
                        'algo', 
                        '". $criteria_type ."', 
                        ". $criteria_type_value .", 
                        'product_review', 
                        0,0,0,0, 
                        SUM( IF( opr.product_review = 'like', 1, 0 )) AS product_like_count,
                        SUM( IF( opr.product_review = 'dislike', 1, 0 )) AS product_dislike_count,
                        NOW(), 
                        NOW()
                    FROM ". DB_PREFIX ."order_product_review opr
                        JOIN ". DB_PREFIX ."order_product AS oop ON oop.order_product_id = opr.order_product_id
                        JOIN ". DB_PREFIX ."order AS o ON o.order_id = oop.order_id
                        JOIN ". DB_PREFIX ."product_to_category p2c ON p2c.product_id = oop.product_id 
                        JOIN ". DB_PREFIX ."ms_product omp ON omp.product_id = oop.product_id 
                        JOIN ". DB_PREFIX ."product op ON op.product_id = oop.product_id 
                        ". $extra_join ."
                    WHERE ";

        // customer ids
        if ( !empty( $customer_ids )) {
            $sql .= " o.customer_id IN ( ". $this->_db->escape( $customer_ids ) ." )";
        } else {
            $sql .= " o.customer_id <> 0";
        }

        // duration
        if ( !empty( $start_date ) && !empty( $end_date )) {
            $sql .= " AND opr.product_review_date >= '". $this->_db->escape( $start_date ) ."' AND opr.product_review_date <= '". $this->_db->escape( $end_date ) ."'";
        }

        $sql .= " GROUP BY o.customer_id, p2c.category_id, ". $last_group_by ." 
                  HAVING ( product_like_count > 0 OR product_dislike_count > 0 ))
                ON DUPLICATE KEY UPDATE 
                    product_like_count = product_like_count + VALUES(product_like_count),
                    product_dislike_count = product_dislike_count + VALUES(product_dislike_count) ";

        return $sql;
    }

    /**
     * updates/reset return prefs
     * @param  string $type         [description]
     * @param  string $customer_ids [description]
     * @param  string $start_date   [description]
     * @param  string $end_date     [description]
     * @return bool               [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function updateReturnPreferences( string $type = 'update', 
                                             string $customer_ids = '', 
                                             string $start_date = '', 
                                             string $end_date = '',
                                             int $order_limit = 0 ): bool
    {
        if ( empty( $customer_ids ) 
             && empty( $start_date )
             && empty( $end_date )) {
            return false;
        }

        if ( $type == 'reset' ) {
            $this-> deleteOldPreference( 'algo', 'return', $customer_ids );
        }

        $update_return_seller_sql = $this->getUpdateReturnPreferenceSQL( $type, 'seller', $customer_ids, $start_date, $end_date );
        $update_return_rating_sql = $this->getUpdateReturnPreferenceSQL( $type, 'rating', $customer_ids, $start_date, $end_date );

        return $this->_db->query( $update_return_seller_sql ) && $this->_db->query( $update_return_rating_sql );
    }

    /**
     * sql for updates/reset return prefs
     * @param  string $type          [description]
     * @param  string $criteria_type [description]
     * @param  string $customer_ids  [description]
     * @param  string $start_date    [description]
     * @param  string $end_date      [description]
     * @return string                [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function getUpdateReturnPreferenceSQL( string $type = 'update', 
                                                  string $criteria_type = 'seller', 
                                                  string $customer_ids = '', 
                                                  string $start_date = '', 
                                                  string $end_date = '' ): string
    {
        if ( $criteria_type == "seller" ) {
            $criteria_type_value = "omp.seller_id";
            $extra_join = "";
            $last_group_by = "omp.seller_id";
        }

        if ( $criteria_type == "rating" ) {
            $criteria_type_value = "COALESCE(orr2.rating, orr1.rating) AS rating_calc";
            $extra_join = " 
                            JOIN ". DB_PREFIX ."review_rules orr1 ON orr1.seller_id = omp.seller_id AND orr1.rule_type = 'global' 
                            LEFT JOIN ". DB_PREFIX ."review_rules orr2 ON orr2.seller_id = omp.seller_id AND orr2.category_id = p2c.category_id AND orr2.rule_type = 'category' 
                          ";
            $last_group_by = "rating_calc";
        }

        $sql = "
                INSERT IGNORE INTO ". DB_PREFIX ."customer_preferences_all
                ( 
                    SELECT 
                        o.customer_id, 
                        p2c.category_id, 
                        'algo', 
                        '". $criteria_type ."', 
                        ". $criteria_type_value .", 
                        'return', 
                        MIN(op.selling_price), 
                        MAX(op.selling_price), 
                        SUM(r.quantity),
                        SUM(r.quantity * oop.piece_in_set * oop.price_per_piece),
                        0, 0, 
                        NOW(), 
                        NOW()
                    FROM ". DB_PREFIX ."return r
                        JOIN ". DB_PREFIX ."order_product AS oop ON oop.order_product_id = r.order_product_id
                        JOIN ". DB_PREFIX ."order AS o ON o.order_id = oop.order_id
                        JOIN ". DB_PREFIX ."product_to_category p2c ON p2c.product_id = oop.product_id 
                        JOIN ". DB_PREFIX ."ms_product omp ON omp.product_id = oop.product_id 
                        JOIN ". DB_PREFIX ."product op ON op.product_id = oop.product_id 
                        ". $extra_join ."
                    WHERE ";

        // customer ids
        if ( !empty( $customer_ids )) {
            $sql .= " o.customer_id IN ( ". $this->_db->escape( $customer_ids ) ." )";
        } else {
            $sql .= " o.customer_id <> 0";
        }

        // duration
        if ( !empty( $start_date ) && !empty( $end_date )) {
            $sql .= " AND r.date_added >= '". $this->_db->escape( $start_date ) ."' AND r.date_added <= '". $this->_db->escape( $end_date ) ."'";
        }

        $sql .= " AND r.return_reason_id = '". RETURN_REASON_IDS['Quality_Issue'] ."'";

        $sql .= " GROUP BY o.customer_id, p2c.category_id, ". $last_group_by ."
                )
                ON DUPLICATE KEY UPDATE 
                    min_price = LEAST(min_price, VALUES(min_price)), 
                    max_price = GREATEST(max_price, VALUES(max_price)),
                    pieces_count = pieces_count + VALUES(pieces_count),
                    total_amount = total_amount + VALUES(total_amount) ";

        return $sql;
    }
    
    /**
     * @param  array  $new_pref_array [description]
     * @param  array  $old_pref_array [description]
     * @param  string $pref_by        [description]
     * @return array                 [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function createFinalPreferenceData( array $new_pref_array, array $old_pref_array, string $pref_by = '' ): array 
    {    
        $final_pref_array = $old_pref_array;
        
        foreach ($new_pref_array as $cat_id => $pref_option) {
            
            if(array_key_exists($cat_id, $old_pref_array)){ 
                    
                // Seller
                
                foreach ($pref_option['seller'] as $seller_id => $seller_data) {
                    if(array_key_exists($seller_id, $old_pref_array[$cat_id]['seller'])) {
                        
                        if($seller_data['min_price'] < $old_pref_array[$cat_id]['seller'][$seller_id]['min_price']) {
                            $final_pref_array[$cat_id]['seller'][$seller_id]['min_price'] = $seller_data['min_price'];
                        }
                        
                        if($seller_data['max_price'] > $old_pref_array[$cat_id]['seller'][$seller_id]['max_price']) {
                            $final_pref_array[$cat_id]['seller'][$seller_id]['max_price'] = $seller_data['max_price'];
                        }
                        
                        $final_pref_array[$cat_id]['seller'][$seller_id]['pieces_count'] += $seller_data['pieces_count'];
                        $final_pref_array[$cat_id]['seller'][$seller_id]['total_amount'] += $seller_data['total_amount'];
                        
                    } else {
                        $final_pref_array[$cat_id]['seller'][$seller_id] = $new_pref_array[$cat_id]['seller'][$seller_id];
                    }
                }
                
                // Rating
                
                if ( isset( $final_pref_array[$cat_id]['rating']['5'] )) {
                    
                    $final_pref_array[$cat_id]['rating']['5']['pieces_count'] += $pref_option['rating']['5']['pieces_count'];
                    $final_pref_array[$cat_id]['rating']['5']['total_amount'] += $pref_option['rating']['5']['total_amount'];
                }
                
                if ( isset( $final_pref_array[$cat_id]['rating']['4'] )) {
                    $final_pref_array[$cat_id]['rating']['4']['pieces_count'] += $pref_option['rating']['4']['pieces_count'];
                    $final_pref_array[$cat_id]['rating']['4']['total_amount'] += $pref_option['rating']['4']['total_amount'];

                }
                
                if ( isset( $final_pref_array[$cat_id]['rating']['3'] )) {
                    $final_pref_array[$cat_id]['rating']['3']['pieces_count'] += $pref_option['rating']['3']['pieces_count'];
                    $final_pref_array[$cat_id]['rating']['3']['total_amount'] += $pref_option['rating']['3']['total_amount'];
                }
                
            } else {
                $final_pref_array[$cat_id] = $new_pref_array[$cat_id];
            }
        }
        return $final_pref_array;
    }

    /**
     * @param  string $pref_type    [description]
     * @param  string $start_date   [description]
     * @param  string $end_date     [description]
     * @param  string $customer_ids [description]
     * @return bool               [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function resetPreferenceTypeByDuration( string $pref_type, string $start_date, string $end_date, string $customer_ids = '' ): bool
    {
        if ( $pref_type == "shortlist" ) {
            return $this->updateShortlistPreferences( 'reset', $customer_ids, $start_date, $end_date );
        }

        if ( $pref_type == "product_review" ) {
            return $this->updateProductReviewPreferences( 'reset', $customer_ids, $start_date, $end_date );
        }
    }
}
?>