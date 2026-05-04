<?php

require_once __DIR__ . '/../../config.php';
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/solr/product.php');
require_once(DIR_SYSTEM . 'cron/syncproducttosolr.php');

// Dont run if SOLR not enabled for this
if (!SOLR_ENABLED || !SOLR_WSBOX_ENABLED) {
    exit();
}

// Setting a time limit for this producer to run.
$time_limit = 50; // 50 seconds. Currently cron is set to spawn a producer every minute. to avoid collision
$start_time = microtime(true);

// Create DB object
$db = new Database\DB( DB_SERVERS );

// List of table_name to sync in this Producer task
// Add to this array whenever a new table needs to be synced
$tables_to_sync = array('oc_product', 
                        'oc_product_description', 
                        'oc_product_to_store', 
                        'oc_product_to_category', 
                        'oc_product_filter', 
                        'oc_ms_seller',
                        'oc_ms_product'
                       );

// Loop until we reach the time limit for this producer
while ( (microtime(true) - $start_time) <= $time_limit ) {

    // Get 30 change_id(s) at a time
    $sql = "SELECT change_id, product_id, table_name, change_details
            FROM " . DB_PREFIX . "product_change_log
            WHERE solr_synced = 0
            AND date_added < NOW() - INTERVAL 1 MINUTE 
            AND table_name IN ('" . implode("','",$tables_to_sync) . "') 
            ORDER BY product_id, table_name ASC LIMIT 1";
    $result = $db->query($sql)->rows;
    if ( empty($result) ) {
        sleep(5);
        continue;
    }
    
    // Tables which have multiple entries due to insert and delete operations in edit product code
    $multi_valued_field = array("oc_product_to_category", "oc_product_filter", "oc_product_to_store");
    
    $tables_array = array();
    $final_array = array();
    $filter_groups = array();
    foreach ($result as $key => $value) {
        
        $new_arr = array();
        
        if(in_array($value['table_name'], $multi_valued_field)) {

            // if($value['table_name'] == 'oc_product_filter'){
            //     $change_details = json_decode($value['change_details']);
            //     if(!empty($change_details->filter_id->old)) {
            //         $final_array['filter_groups'][$value['product_id']]['old'][] = $change_details->filter_id->old;
            //     } elseif(!empty($change_details->filter_id->new)) {
            //         $final_array['filter_groups'][$value['product_id']]['new'][] = $change_details->filter_id->new;
            //     }
            // }
            if(!in_array($value['table_name'], $tables_array) || $last_product_id != $value['product_id']){
                $new_arr = array();
                switch($value['table_name']){
                    case 'oc_product_to_category':
                        $sql = "SELECT GROUP_CONCAT(category_id) AS category_id  
                                FROM oc_product_to_category WHERE product_id =".(int) $value['product_id'];
                        $category_id = $db->query($sql)->row['category_id'];
                        
                        $categories = '';
                        if(!empty($category_id)) {
                            $sql = "SELECT GROUP_CONCAT(name) AS categories FROM oc_category_description 
                                    WHERE category_id IN( " .$category_id. " )";
                            $categories = $db->query($sql)->row['categories'];
                        }

                        $new_arr['product_id'] = $value['product_id'];
                        $new_arr['category_id']['old'] = '';
                        $new_arr['category_id']['new'] = $category_id;
                        $new_arr['categories']['old'] = '';
                        $new_arr['categories']['new'] = $categories;
                        $final_array[$value['table_name']][$value['product_id']] = json_encode($new_arr);

                        $tables_array[] = "oc_product_to_category";
                        break;
                    case 'oc_product_filter':
                        $sql = "SELECT GROUP_CONCAT(filter_id) AS filter_id FROM oc_product_filter WHERE product_id =".(int) $value['product_id'];
                        $filter_id = $db->query($sql)->row['filter_id'];

                        $filters = '';
                        if(!empty($filter_id)) {
                            $sql = "SELECT GROUP_CONCAT(name) AS filters 
                                    FROM ".DB_PREFIX."filter_description WHERE filter_id IN( " .$filter_id. " )";
                            $filters = $db->query($sql)->row['filters'];
                        }

                        $new_arr['product_id'] = $value['product_id'];
                        $new_arr['filter_id']['old'] = '';
                        $new_arr['filter_id']['new'] = $filter_id;
                        $new_arr['filters']['old'] = '';
                        $new_arr['filters']['new'] = $filters;
                        $final_array[$value['table_name']][$value['product_id']] = json_encode($new_arr);
                        
                        $tables_array[] = 'oc_product_filter';

                        break;
                    case 'oc_product_to_store':
                        $sql = "SELECT GROUP_CONCAT(store_id) AS store_id FROM oc_product_to_store WHERE product_id =".(int)$value['product_id'];
                        $store_id = $db->query($sql)->row['store_id'];
                        $new_arr['product_id'] = $value['product_id'];
                        $new_arr['store_id']['old'] = '';
                        $new_arr['store_id']['new'] = $store_id;
                        $final_array[$value['table_name']][$value['product_id']] = json_encode($new_arr);

                        $tables_array[] = 'oc_product_to_store';
                        break;
                }
                $last_product_id = $value['product_id'];
            }
        } elseif($value['table_name'] == "oc_product_description") {
            $new_arr = array();
            if(!in_array($value['table_name'], $tables_array) || $last_product_id != $value['product_id']){
                $sql = "SELECT name, set_description, description, tag FROM oc_product_description WHERE product_id =".(int)$value['product_id']." AND language_id = 1";
                $product_description = $db->query($sql)->row;
                
                $new_arr['product_id'] = $value['product_id'];
                $new_arr['name']['old'] = '';
                $new_arr['name']['new'] = $product_description['name'] ?? '';
                $new_arr['set_description']['old'] = '';
                $new_arr['set_description']['new'] = $product_description['set_description'] ?? '';
                $new_arr['description']['old'] = '';
                $new_arr['description']['new'] = $product_description['description'] ?? '';
                $new_arr['tag']['old'] = '';
                $new_arr['tag']['new'] = $product_description['tag'] ?? '';
                $final_array[$value['table_name']][$value['product_id']] = json_encode($new_arr);
                
                $tables_array[] = 'oc_product_to_store';
            }
            $last_product_id = $value['product_id'];
        } elseif($value['table_name'] == "oc_ms_product"){
            $value['change_details'] = json_decode($value['change_details'], true);
            foreach($value['change_details'] as $new_key => $new_val){
                if($new_key == 'product_id'){
                    $new_arr['product_id'] = $new_val;
                }else{
                    $new_arr[$new_key]['old'] = $new_val['old'];
                    $new_arr[$new_key]['new'] = $new_val['new'];
                }
                $final_array[$value['table_name']][$value['product_id']] = json_encode($new_arr);
            }
        } else {
            $value['change_details'] = json_decode($value['change_details'], true);
            foreach($value['change_details'] as $new_key => $new_val){
                if($new_key == 'product_id' || $new_key == 'seller_id'){
                    $new_arr[$new_key] = $new_val;
                }else{
                    if($new_key == "stock_status_id") {
                        $sql = "SELECT name FROM oc_stock_status WHERE stock_status_id =". (int)$value['change_details']['stock_status_id']['new'] ." AND language_id = 1";
                        $stock_status = $db->query($sql)->row['name'];
                        $new_arr['stock_status_id']['old'] = $value['change_details']['stock_status_id']['old'];
                        $new_arr['stock_status_id']['new'] = $value['change_details']['stock_status_id']['new'];
                        $new_arr['stock_status']['old'] = '';
                        $new_arr['stock_status']['new'] = $stock_status;
                    } elseif($new_key == "selling_price") {
                        $sql = "UPDATE oc_product SET selling_price = ". $value['change_details']['selling_price']['new'] ." where product_id =".(int)$new_arr['product_id'];
                        $db->query($sql);
                        $new_arr[$new_key]['old'] = $new_val['old'];
                        $new_arr[$new_key]['new'] = $new_val['new'];
                    } elseif($new_key == "hsn_code") {
                        $sql = "UPDATE oc_product op 
                                INNER JOIN oc_hsn oh ON op.hsn_code = oh.hsn_code 
                                SET op.tax_class_id  = oh.tax_class_id 
                                WHERE product_id =".$new_arr['product_id'];
                        $db->query($sql);
                        $new_arr[$new_key]['old'] = $new_val['old'];
                        $new_arr[$new_key]['new'] = $new_val['new'];
                    }
                    elseif($new_key == "date_available"){
                        $new_arr[$new_key]['old'] = $new_val['old'];
                        $new_arr[$new_key]['new'] = date('Y-m-d\TH:i:s\Z', strtotime($new_val['new']));
                    } else {
                        $new_arr[$new_key]['old'] = $new_val['old'];
                        $new_arr[$new_key]['new'] = $new_val['new'];
                    }
                }
                $final_array[$value['table_name']][$value['product_id']] = json_encode($new_arr);
            }
        }
    }

    foreach ($final_array as $table_name => $value) {
        foreach($value as $product_id => $change_details) {
            // if($table_name == 'filter_groups'){
            //     $new_filters = array();
            //     $old_filters = array();
            //     $new_arr     = array();
                
            //     if ( !isset($change_details['old']) ) {
            //         $change_details['old'] = array();
            //     }
                
            //     $frash_arr = array_unique(array_merge($change_details['old'], $change_details['new']));
            //     foreach ($frash_arr as $value) {
            //         in_array($value, $change_details['new'])?$filters_grp[1][] = $value : $filters_grp[0][] = $value; 
            //     }
            //     for($i=0; $i<=1; $i++){
                    
            //         $filter_groups = array();
            //         if ( !empty($filters_grp[$i]) ) {
            //             $sql = "SELECT f.filter_group_id,
            //                     GROUP_CONCAT(f.filter_id SEPARATOR ',') as filter_ids
            //                     FROM oc_filter f 
            //                     WHERE f.filter_id IN (". implode(',', $filters_grp[$i]) .") 
            //                     GROUP BY f.filter_group_id";
            //             $filter_groups = $db->query($sql)->rows;
            //         }
            //         foreach($filter_groups as $val){
            //             $new_arr['product_id']= $product_id;
            //             if($i == 0) {
            //                 $new_arr["filter_group_df_".$val['filter_group_id']]['old'] = '';
            //                 $new_arr["filter_group_df_".$val['filter_group_id']]['new'] = ''; 
            //             } else {
            //                 $new_arr["filter_group_df_".$val['filter_group_id']]['old'] = '';
            //                 $new_arr["filter_group_df_".$val['filter_group_id']]['new'] = explode(',',$val['filter_ids']); 
            //             }
            //         }
            //     }
            //     $change_details = json_encode($new_arr);
            // }
            $syncProductToSolr = New syncProductToSolr($product_id, $table_name, $change_details);
            $syncProductToSolr->updateProductToSolr();
        }
    }

    // //Instead of updating, delete the processed entries (20-05-2017)
    $sql = "DELETE FROM " . DB_PREFIX . "product_change_log
            WHERE change_id IN (" . implode(",",array_column($result, 'change_id')) . ")";
    $db->query($sql);
}

exit();

