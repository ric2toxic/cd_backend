<?php

require_once( DIR_SYSTEM . 'library/customer_activity_log.php' );

class ModelPreferences extends Model {

    /**
     * @param  int    $customer_id     
     * @param  string $source          
     * @param  array $preference_type 
     * @return array                  
     * @author Anurag Jain, 12th Aug 2019
     */
    public function getCustomerPreferencesByPreferenceType( int $customer_id, string $category_ids = '', string $source = 'algo', 
                                                            array $preference_type = array(), bool $formatted = false ): array
    {   
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->getCustomerPreferencesByPreferenceType( $customer_id, $category_ids, $source, $preference_type, $formatted );
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
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->checkPreferenceExistenceBySource( $customer_id, $source );
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
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->getCustomerPreferenceCategories( $customer_ids_array, $source, $pref_type );
    }

    /**
     * formats Product details To customer category seller wise Preferences
     * @param  array  $eligible_products [description]
     * @return array                    [description]
     * @author: Anurag Jain, 20 July 2018
     */
    public function formatProductsToPreference( array $eligible_products ): array
    {
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->formatProductsToPreference( $eligible_products );
    }

    /**
     * @param  string $type         [description]
     * @param  string $customer_ids [description]
     * @return bool               [description]
     * @author: Anurag Jain, 30th Aug 2018
     */
    public function updateCustomerPreferences( string $type = 'update', string $customer_ids = '' ): bool
    {
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->updateCustomerPreferences( $type, $customer_ids );
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
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->createFinalPreferenceData( $new_pref_array, $old_pref_array, $pref_by = '' );
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
        $customer_pref_obj = new CustomerPreferences( $this->registry );
        return $customer_pref_obj->resetPreferenceTypeByDuration( $pref_type, $start_date, $end_date, $customer_ids );
    }

    /**
     * gets all saved preferences except for return prefs
     * using it in you may like 
     * @param  int    $customer [description]
     * @return string           [description]
     * @author: Anurag Jain, 31th Aug 2018
     */
    public function getAllPositivePreferenceCategories( int $customer ): string
    {
        $sql = " 
                SELECT 
                    GROUP_CONCAT( DISTINCT( category_id )) AS category_ids 
                FROM oc_customer_preferences_all
                WHERE 
                    customer_id = '". (int) $customer. "'
                    AND pref_type <> 'return' 
                GROUP BY customer_id
                ORDER BY NULL";

        $result = $this->db->query( $sql );
        
        if( $result->num_rows ) {
            return $result->row['category_ids'];
        }
        return '';
    }

    /**
     * @param  array  $customer_ids [description]
     * @return array                [description]
     * @author Anurag Jain, 21st June 2019
     */
    public function getCustomerCategoriesByPreferenceTypeForCRM( array $customer_ids )
    {
        $final_pref_cats = array();

        if ( !empty( $customer_ids )) {

            $all_pref_cat_ids = $this->getCustomerCategoryIdsGroupByPreferenceTypeForCRM( $customer_ids );

            if ( !empty( $all_pref_cat_ids )) {

                $this->load->model('catalog/category');

                foreach ( $all_pref_cat_ids as $pref_key => $category_ids ) {
                    $category_ids_name = $this->model_catalog_category->getCategoriesNameFromCategoryIds( explode( ',', $category_ids ));
                    $final_pref_cats[$pref_key] = $category_ids_name;
                }
            }
        }

        return $final_pref_cats;
    }

    /**
     * @param  array  $customer_ids [description]
     * @return array                [description]
     * @author Anurag Jain, 21st June 2019
     */
    public function getCustomerCategoryIdsGroupByPreferenceTypeForCRM( array $customer_ids )
    {
        $all_preferences = array();

        if ( !empty( $customer_ids )) {

            //transactional prefs: includes purchasing and shorlist prefs
            $transactional_prefernces = $this->getCustomerPreferenceCategories( $customer_ids, 'algo', 'order' );
            
            if ( !empty( $transactional_prefernces )) {
                $transactional_prefernces = implode( ',', $transactional_prefernces ) ?? "";
            }

            $shortlist_preferences = $this->getCustomerPreferenceCategories( $customer_ids, 'algo', "shortlist" );

            if ( !empty( $shortlist_preferences )) {
                $shortlist_preferences = implode( ',', $shortlist_preferences ) ?? "";
            }

            if ( !empty( $shortlist_preferences )) {

                if ( !empty( $transactional_prefernces )) {
                    $transactional_prefernces = $transactional_prefernces.",".$shortlist_preferences;
                } else {
                    $transactional_prefernces = $shortlist_preferences;
                }
            }

            if ( !empty( $transactional_prefernces )) {
                $all_preferences["transactional"] = implode( ',', array_unique( explode(',', $transactional_prefernces )));
            }

            // browsing prefs
            $cust_activity_log_obj = new CustomerActivityLog( $this->registry );
            $browsing_prefernces = $cust_activity_log_obj->getCustomerBrowsingCategories( $customer_ids );

            if ( !empty( $browsing_prefernces )) {
                $all_preferences["browsing"]  = $browsing_prefernces;
            }

            // crm prefs
            $crm_prefernces = $this->getCustomerPreferenceCategories( $customer_ids, 'crm' );
            if ( !empty( $crm_prefernces )) {
                $all_preferences["crm"] = implode( ",", array_unique( explode( ",", implode( ",", $crm_prefernces ) )));
            }
        }
        return $all_preferences;
    }

    /**
     * Function to set customer preferences (categories and prices) from CRM
     * We will delete all old crm prefs for given customers and then will insert fresh preferences
     * @param array $data array(
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
                                )
                            )
     * @return bool
     * @author: Anurag Jain (26 June 2018)
     */
    public function setCustomerPreferencesFromCRM( array $data ): bool
    {
        if( empty( $data )) {
            return false;
        }

        $customer_ids_array = array_keys( $data );

        $customer_pref_obj = new CustomerPreferences( $this->registry );

        // delete old crm prefs for customers
        $customer_pref_obj->deleteOldPreference( 'crm', '', implode( ',', $customer_ids_array ));
        $insert_result = $this->addCustomersNonAlgoPreferences( $data, 'crm' );

        if( $insert_result ) {
            return true;
        }
        return false;
    }

    /**
     * Function to set customer preferences (categories and prices) from app
     * @param array $data [description]
     * @return bool [<description>]
     * @author Anurag Jain, 6th Sept 2019
     */
    public function setCustomerPreferencesFromApp( array $data ): bool
    {
        if( empty( $data )) {
            return false;
        }

        $customer_ids_array = array_keys( $data );

        $customer_pref_obj = new CustomerPreferences( $this->registry );

        // delete old cust prefs
        $customer_pref_obj->deleteOldPreference( 'cust', '', implode( ',', $customer_ids_array ), 'IGNORE' );
        $insert_result = $this->addCustomersNonAlgoPreferences( $data );

        if( $insert_result ) {
            return true;
        }
        return false;
    }

    /**
     * Function to insert new preferences for given customers
     * @param array
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
                            )
                        );
     * @return bool
     * @author: Anurag Jain (26 June 2018)
    */
    public function addCustomersNonAlgoPreferences( array $data, string $source = 'cust' ): bool
    {
        if( empty( $data )) {
            return false;
        }

        $insert_sql = "
                        INSERT IGNORE INTO ". DB_PREFIX ."customer_preferences_all
                            (customer_id, category_id, source, min_price, max_price)
                        VALUES ";

        $insert_values_array = array();
        foreach ( $data as $customer_id => $category_data ) {

            foreach ( $category_data as $category_id => $cat_data ) {
                
                if( !is_array( $cat_data )) {
                    $category_id = $cat_data;
                }

                $min_price = ((int) $cat_data['min_price']) ?? 0;
                $max_price = ((int) $cat_data['max_price']) ?? 0;

                $insert_values[] = "('" . (int) $customer_id . "',
                                     '" . (int) $category_id . "',
                                     '". $this->db->escape( $source ) ."',
                                     '" . (int) $min_price . "',
                                     '" . (int) $max_price . "')";
            }
        }

        $insert_values = implode( ',', $insert_values );
        $insert_sql .= $insert_values;

        return $this->db->query( $insert_sql );
    }

    /**
     * @param  int    $customer_id [description]
     * @param  string $source      [description]
     * @param  string $pref_type   [description]
     * @return bool              [description]
     * @author: Anurag Jain
     */
    public function checkPreferenceExistence( int $customer_id, string $source = 'cust', $pref_type = '' ): bool
    {
        $sql = "
                SELECT customer_id
                FROM ". DB_PREFIX ."customer_preferences_all
                WHERE 
                    customer_id = '". (int) $customer_id."'
                    AND source = '". $source ."'
                LIMIT 1";
        $pref_query = $this->db->query( $sql );
        
        if( $pref_query->num_rows ) {
            return true;
        }
        return false;
    }

    public function getCustomerAlgoPreferencesCategorySellerwise( array $customer_array ): array
    {
        if( empty( $customer_array )) {
            return array();
        }

        $customer_ids = implode( ',', $customer_array );

        $final_result = array();

        $sql = "
                SELECT 
                    category_id,
                    criteria_type_value AS seller_id,
                    min(min_price) AS min_price, 
                    max(max_price) AS max_price
                FROM ". DB_PREFIX ."customer_preferences_all
                WHERE customer_id IN (". $this->db->escape( $customer_ids ) .")
                    AND criteria_type = 'seller'
                GROUP BY category_id, criteria_type_value";

        $result = $this->db->query( $sql );

        if( $result->num_rows ) {
            foreach ( $result->rows as $key => $data ) {
                $final_result[ $data['category_id'] ]['sellers'][ $data['seller_id'] ]['min_price'] = $data['min_price'];
                $final_result[ $data['category_id'] ]['sellers'][ $data['seller_id'] ]['max_price'] = $data['max_price'];
            }
        }
        return $final_result;
    }
}
