<?php
// Update stock for 001_DL (seller_id = 17631) - RGL fashions 

require_once(__DIR__ . '/../../config.php');
require_once(DIR_SYSTEM . 'library/db/db.php');

// Stock / Quantity link
$api_link = 'http://rfpl.sugarkane.in/channelapi/getCatalogueInventory/wholesalebox';
$seller_id = 17631;

// Reading API using CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$quantity_json_from_api = curl_exec($ch);
curl_close($ch);

$quantity_array_from_api = json_decode($quantity_json_from_api, true);


// Loop to extract array(sku => array (size => quantity))
$quantity_data = array();
foreach ($quantity_array_from_api as $sku_inventory) {
    $sku_size = explode('-', trim($sku_inventory['sku']));
    if(isset($sku_size[1])){    
        $quantity_data[$sku_size[0]][$sku_size[1]] = (int)$sku_inventory['inventory'];    
    }    
} 

// Creating Database object
$db = new Database\DB( DB_SERVERS );

// Looping over each SKU to prepare set description and quantity
foreach ($quantity_data as $sku => $size_quantity) {
    $instock_sizes = array_filter($size_quantity);
    $sql = "";
    
    // If no sizes in stock
    if ( empty($instock_sizes) ) {
        $sql = "UPDATE " . DB_PREFIX . "product p 
                INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id 
                SET p.quantity = 0 
                WHERE p.sku LIKE '" . $db->escape(trim($sku)) . "' 
                  AND mp.seller_id = '" . (int)$seller_id . "'";
        
    } else {
        $piece_in_set = count($instock_sizes); // no of available sizes will be available in set
        $quantity = min($instock_sizes); // available quantity will be minimum quantity from all the available sizes
        $set_description = "1 Set = Total " . $piece_in_set . " pieces; 1 each of sizes " . implode(", ", array_keys($instock_sizes));
        
        $sql = "UPDATE " . DB_PREFIX . "product p 
                INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id 
                INNER JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id 
                SET p.quantity = '" . (int)$quantity . "', 
                    p.piece_in_set = '" . (int)$piece_in_set . "', 
                    pd.set_description = '" . $db->escape(trim($set_description)) . "'  
                WHERE p.sku LIKE '" . $db->escape(trim($sku)) . "' 
                  AND mp.seller_id = '" . (int)$seller_id . "' 
                  AND pd.language_id = 1";
    }
    
    $db->query($sql);
}

?>

