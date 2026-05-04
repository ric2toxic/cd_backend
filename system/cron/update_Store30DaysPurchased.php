<?php
$start = microtime(true);
require_once __DIR__ . '/../../config.php';
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/solr/product.php');
require_once(DIR_SYSTEM . 'cron/syncproducttosolr.php');

if ( !(SOLR_ENABLED && SOLR_WSBOX_ENABLED) ){
    die("SOLR is disabled.");
}

$db = new Database\DB( DB_SERVERS );

$current_date = date('Y-m-d H:i:s');

// Number of records going to fetch in one iteration.
$limit = 100;

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

$total_records=0;
$start_limit = 0;

do{
    $sql = "SELECT DATEDIFF(NOW(), wp.date_added) as last_purchased_days, wpb.product_id as product_id FROM oc_wsb_purchase wp INNER JOIN oc_wsb_purchase_breakup wpb ON wp.purchase_id = wpb.purchase_id ORDER BY wp.purchase_id LIMIT ".$start_limit.",".$limit;
    $query = $db->query($sql);
    if($query->num_rows < 1){
        exit;
    }
    $total_records = $query->num_rows;
    $products = $query->rows;
    $start_limit = $start_limit + $total_records;
    foreach ($products as $product){
        $last_purchased_days = $product['last_purchased_days'];
        $product_id = $product['product_id'];

        // get a select query instance
        $query  = $client->createSelect();
        $sql    = 'id:' . $product_id;
        $query->setQuery($sql);
        $query->getFields(array('id'));
        $query->setStart(0, 10);

        // this executes the query and returns the result
        $resultset = $client->select($query);

        foreach ($resultset as $key => $document) {
            if($key == 0 && empty($document->searchable)){
                $searchable = array();
            }else{
                $searchable  = $document->searchable;
            }
        }
        if($last_purchased_days > 30 && !in_array('Store30DaysPurchased',$searchable)) {
            array_push($searchable,'Store30DaysPurchased');
        }
        else if($last_purchased_days < 31 && in_array('Store30DaysPurchased',$searchable)) {
            $key = array_search('Store30DaysPurchased',$searchable);
            unset($searchable[$key]);
        }
        $searchable = array_unique($searchable);
        $searchable = array_values($searchable);
        $new_arr = array();
        $new_arr['product_id'] = $product_id;
        $new_arr['searchable']['old'] = '';
        $new_arr['searchable']['new'] = $searchable;

        $change_detail_arr = json_encode($new_arr);
        $table_name = '';
        $syncProductToSolr = New syncProductToSolr($product_id, $table_name, $change_detail_arr);
        $syncProductToSolr->dailyOnceUpdateToSolr(false);

    }

}while($total_records == $limit);
$end = microtime(true);
echo "Total Time taken: ". ($end - $start).' seconds';
exit();
