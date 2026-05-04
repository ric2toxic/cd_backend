<?php
class ModelTestTest extends Model
{

	public $db_crm;
 function __construct() {
       //CRM Database
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        // $registry->set('db_crm', $db_crm);
    }

    public function getAllCustomers(){

        $sql =   " SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name,a.address_1,a.address_2,a.postcode, a.city, ms.seller_id
                   FROM oc_customer c
                   INNER JOIN oc_address a ON c.customer_id = a.customer_id
                   LEFT JOIN oc_ms_seller ms ON c.customer_id = ms.seller_id
                   WHERE ms.seller_id is NULL";

        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getCustomersByCity($city){

        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name,a.address_1,a.address_2,a.postcode FROM oc_customer c INNER JOIN oc_address a ON c.customer_id = a.customer_id WHERE a.city like'%" . $city . "%'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    /**
     * Get Customer By City (Which Have Ordered Already)
     * function getCustomersByCityWithOrder
     * @param city
     * @return Customer Details With Orders Details
     * @author Ravindra Singh
     * @date 14-12-2015
     * */
    public function getCustomersByCityWithOrder($city = '')
    {
        $sql = "SELECT c.customer_id,c.email,c.telephone,
                    CONCAT(c.firstname, ' ', c.lastname) AS name,
                    CONCAT(a.address_1,a.address_2) AS address,
                    a.postcode,a.company,a.city,
                    (SELECT COUNT(*) FROM oc_order WHERE customer_id = c.customer_id) AS total_order,
                    MAX(o.date_added) AS last_order_date
                    FROM oc_customer c
                    INNER JOIN oc_address a
                    ON c.customer_id = a.customer_id
                    INNER JOIN oc_order o
                    ON c.customer_id = o.customer_id" ;
        if($city != 'all'){
            $sql .= " WHERE a.city
                    LIKE '%" . $city . "%'";
        }
        $sql .= " GROUP BY c.customer_id ORDER BY o.date_added DESC";

        $query = $this->db->query($sql);

        $arr_order = array();

        foreach ($query->rows as $record) {
            $order_sql = "SELECT o.order_no, o.date_added, o.total
                            FROM oc_order o
                             WHERE o.customer_id=" .
                "'" . $record['customer_id'] . "'";

            $record['orders'] = $this->db->query($order_sql)->rows;
            $arr_order[] = $record;
        }
        return $arr_order;
    }

    // get campus members with their team
    public function getCampusMembers()
    {

        $sql = "SELECT cm.*,ct.team_name FROM oc_campus_member cm INNER JOIN oc_campus_team ct ON cm.team_id = ct.team_id WHERE 1=1";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    // Convert Single Filter Products To Single Store
    public function convertSingleFiltersToStore()
    {

        $sql = "SELECT * FROM `oc_product_filter` WHERE `filter_id` = 142";

        $query = $this->db->query($sql);


        foreach ($query->rows as $val) {
            $q = "UPDATE `oc_product_to_store` SET `store_id` = '2' WHERE `oc_product_to_store`.`product_id` = '" . $val['product_id'] . "' AND `oc_product_to_store`.`store_id` = 0";
            $this->db->query($q);
        }

        return $query;
    }
    // Added by Parth 
    // check for the category ids of Cotton kurtis and Georgette kurtis on live
    // 71 id is for georgette in sample
    public function updateProductToCategory($category_id)
    {
        // cotton - 1(category id)
        // georgette -102
        // chiffon- 30
        // faux georgette-5
        // faux chiffon-36
        //73 - >cotton kurtis
        switch ($category_id) {
            case 74:
                $sql = "
                          SELECT DISTINCT p.product_id
                          FROM oc_product_to_category p2c
                          LEFT JOIN oc_product_filter pf
                          ON (p2c.product_id = pf.product_id)
                          LEFT JOIN oc_product p
                          ON (pf.product_id = p.product_id)
                          LEFT JOIN oc_product_description pd
                          ON (p.product_id = pd.product_id)
                          LEFT JOIN oc_product_to_store p2s
                          ON (p.product_id = p2s.product_id)
                          WHERE pd.language_id = '1'
                          AND p2c.category_id = '61'
                          AND pf.filter_id IN (102,30,5,36)
                ";
                // $sql = "SELECT product_id FROM `oc_product_filter` WHERE `filter_id` IN ('102','30','5','36')";
                $query = $this->db->query($sql);

                // echo count($query->rows);
                foreach ($query->rows as $val) {
                    echo "INSERT `oc_product_to_category` SET `category_id` = '74', `product_id` =" . $val['product_id'] . ";";
                    echo '<br />';
                }
                break;
            case 73:
                $sql = "
                          SELECT DISTINCT p.product_id
                          FROM oc_product_to_category p2c
                          LEFT JOIN oc_product_filter pf
                          ON (p2c.product_id = pf.product_id)
                          LEFT JOIN oc_product p
                          ON (pf.product_id = p.product_id)
                          LEFT JOIN oc_product_description pd
                          ON (p.product_id = pd.product_id)
                          LEFT JOIN oc_product_to_store p2s
                          ON (p.product_id = p2s.product_id)
                          WHERE pd.language_id = '1'
                          AND p2c.category_id = '61'
                          AND pf.filter_id IN (1)

                 ";
                $query = $this->db->query($sql);
                foreach ($query->rows as $val) {
                    echo $q = "INSERT INTO `oc_product_to_category` SET `category_id` = '73', `product_id` = " . $val['product_id'] . ";";
                    echo '<br />';
                    //$this->db->query($q);
                }
                break;
        }
    }

    public function insertMainCategory()
    {

        $sql = "SELECT product_id FROM oc_product_to_category WHERE category_id IN(73, 74)";
        $results = $this->db->query($sql);
        echo count($results->rows);
        foreach ($results->rows as $result) {

            echo 'INSERT INTO oc_product_to_category SET category_id = 61, product_id=' . $result['product_id'] . ';';
            echo '<br />';
        }


    }

    /**
     * Update Selling Price With price And Commission
     * function updateSellingPrice
     * @author Ravindra Singh
     * @date 22-12-2015
     * */
    public function updateSellingPrice()
    {
        $sql = "SELECT selling_price,price,product_id,seller_tax,commission,tax_class_id FROM oc_product";
        $query = $this->db->query($sql);
        $sellerPrice = array();
        foreach ($query->rows as $product) {
            $seller_tax_factor = 1.0 + ((float)$product['seller_tax'] / 100.0);
            $commission_factor = 1.0 + ((float)$product['commission'] / 100.0);
            $unit_price = ceil($commission_factor * (float)($product['price']) / $seller_tax_factor);
            /*if($product['product_id'] == "62"){
                echo $seller_tax_factor;
                echo $commission_factor;
                echo $unit_price; //exit;
                echo $this->currency->format($this->tax->calculate($unit_price, $product['tax_class_id'], $this->config->get('config_tax'))); exit;
            }*/

            //$sellerPrice[] = $unit_price;
            $query = "UPDATE " . DB_PREFIX . "product
						  SET selling_price = " . $unit_price . "
						  WHERE product_id = " . $product['product_id'];
            $this->db->query($query);
            $data[] = $product;
        }
        //$piece_in_set = (int)$query->row['piece_in_set'] > 1 ? (int)$query->row['piece_in_set'] : 1;


        //$price = $this->currency->format($this->tax->calculate($unit_price, $query->row['tax_class_id'], $this->config->get('config_tax')));


        return $data;
    }

    /**
     * Get purhase details seller wise for accounting purposes.
     */
    public function getPurchaseDetails()
    {
        $sql = "SELECT oop.order_no, oo.date_added, oo.invoice_date, CONCAT(oo.invoice_prefix, oo.invoice_no) AS invoice_no, CONCAT(oo.firstname, ' ', oo.lastname) AS client_name, oms.nickname, SUM(oop.total_pieces * oop.transfer_price_per_piece) AS sku_purchase FROM oc_order_product AS oop INNER JOIN oc_order AS oo ON oo.order_id = oop.order_id INNER JOIN oc_ms_seller AS oms ON oms.seller_id = oop.seller_id WHERE oo.order_status_id > 1 GROUP BY oop.order_no, oms.nickname ORDER BY oop.order_no ASC, oms.nickname ASC";
        $query = $this->db->query($sql);

        return $query;
    }

    /**
     * Get sale details buyer wise for accounting purposes.
     */
    public function getSaleDetails()
    {
        $sql = "SELECT oo.order_id AS id, oo.order_no, oo.invoice_date, CONCAT( oo.invoice_prefix, oo.invoice_no ) AS invoice_no, CONCAT( oo.firstname, ' ', oo.lastname ) AS client_name, oo.payment_company, oo.payment_city, oo.total AS Total_Amt, (SELECT oot.value FROM oc_order_total oot WHERE oot.order_id = id AND oot.code =  'sub_total') AS Product, (SELECT oot.value FROM oc_order_total oot WHERE oot.order_id = id AND oot.code =  'tax') AS Tax, (SELECT oot.value FROM oc_order_total oot WHERE oot.order_id = id AND oot.code =  'shipping') AS Logistics, (SELECT SUM(oot.value) FROM oc_order_total oot WHERE oot.order_id = id AND oot.code !=  'shipping' AND oot.code !=  'total' AND oot.code !=  'sub_total' AND oot.code !=  'tax' AND oot.code !=  'advance' GROUP BY oot.order_id) AS Others FROM oc_order AS oo WHERE oo.order_status_id >0";
        $query = $this->db->query($sql);

        return $query;
    }

    /**
     * Get Customers with their cart
     * function getCustomersWithCart
     * @author Ravindra Singh
     * @date 29-12-2015
     * */
    public function getCustomersWithCart()
    {
        //$sql =   "SELECT customer_id,cart FROM oc_customer WHERE cart LIKE '%:{s:%' ORDER BY customer_id";
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name,a.address_1,a.address_2,a.postcode FROM oc_customer c INNER JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.cart LIKE '%:{s:%'";
        //$sql =   "SELECT customer_id,cart FROM oc_customer ORDER BY customer_id";
        $query = $this->db->query($sql);
        //echo "<pre>"; print_r($query->rows); exit;
        return $query->rows;
    }


    /**
     * get all customers customer_id and referral codes
     */
    /*public function getallcustomers()
    {
        $query = $this->db->query("SELECT customer_id, referral_code FROM " . DB_PREFIX . "customer WHERE referral_code = 0 ");
        return $query->rows;
    }*/

    public function updatereferralcode($customer_id, $referral_code)
    {
        $sql = "UPDATE `oc_customer` SET referral_code = '$referral_code' WHERE customer_id = '$customer_id'";
        $query = $this->db->query($sql);
    }

    public function getcustomeridbyreferralcode($referral_code)
    {
        $sql = "SELECT customer_id FROM " . DB_PREFIX . "customer WHERE referral_code = '$referral_code' ";
        $query = $this->db->query($sql);
        return $query->row;
    }


    public function get_customer_has_order($customer_id){
        $sql = "SELECT customer_id, order_id FROM `oc_order` WHERE customer_id = '$customer_id'";
        $query = $this->db->query($sql);
        return $query->rows;
    }


    /**
     * Takes data from product to store and adds is_single =1
     * @author Parth Gupta
     * @dateTime 2016-01-19T14:09:14+0530
     * @param    string                   $value [description]
     * @return   array      product ids
     */
    public function convertToSingles($value=''){
        $sql = "SELECT DISTINCT product_id FROM
              ".DB_PREFIX."product_to_store ps WHERE
              ps.store_id=2";

        $q = $this->db->query($sql);

        $ps_result = $this->db->query($sql)->rows;

        //echo "<pre>";print_r($ps_result);die;

        if ($q->num_rows) {
            foreach ($ps_result as $key => $value) {

                $des_sql = "SELECT model, sku FROM ".DB_PREFIX."product p
                            WHERE p.product_id="."'".$value['product_id']."'";

                $des_result = $this->db->query($des_sql)->row;

                if (!stripos($des_result['model'], '-SNGL')) {
                    $mod_insert = "UPDATE ".DB_PREFIX."product p
                        SET `is_single`= '1',
                        `model` ="."'".$des_result['model']."-SNGL' ,
                        `sku` ="."'".$des_result['sku']."-SNGL' WHERE
                         `product_id`="."'".$value['product_id']."'";

                }else {
                    $mod_insert = "UPDATE ".DB_PREFIX."product p
                            SET `is_single`= '1' WHERE `product_id`="."'".$value['product_id']."'";

                }
                //echo $mod_insert;echo "<br>";
                if($this->db->query($mod_insert)){
                    $mod_delete = "UPDATE ".DB_PREFIX."product_to_store SET `store_id`='0'
                                    WHERE product_id="."'".$value['product_id']."'";


                    $this->db->query($mod_delete);
                };
            }

        }
        return 1;

    }


    /**
     *
     * Get Customers
     * function getCustomers
     * @author Ravindra Singh
     * @date 16-01-2016
     * */
    public function getCustomers(){
        $sql =   "SELECT customer_id,ws_gcm_registration_id FROM oc_customer";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    /**
     * Get Customers
     * function sendPushNotification
     * @param registrationIds msg || array array
     * @author Ravindra Singh
     * @date 16-01-2016
     * */
    public function sendPushNotification($registrationIds,$msg){

        // API access key from Google API's Console
        define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );

        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'data'			=> $msg
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;
    }

    public function getSellerActivities(){
        $sql = "SELECT * FROM `" . DB_PREFIX . "seller_change_log` WHERE mail = 0 ";
        $query = $this->db->query($sql);
        return $query->rows;

    }

    public function setEmailSent(){
        $sql = "UPDATE `" . DB_PREFIX . "seller_change_log` SET `mail`= 1";
        $query = $this->db->query($sql);

    }

    public function addProductsToInternationalStore($value=''){
        $sql = "SELECT DISTINCT product_id FROM
            ".DB_PREFIX."product_to_store ps WHERE
            ps.store_id=0";

        $results = $this->db->query($sql)->rows;

        foreach ($results as $result) {
            $this->db->query("INSERT INTO ".DB_PREFIX."product_to_store ps 
            SET ps.product_id=".$result.", ps.store_id=2 ");
        }
    }

    public function copyCategoryToStore($from, $to){
        $sql = "SELECT * FROM ".DB_PREFIX."category_to_store WHERE store_id=".$from;

        $query = $this->db->query($sql);

        foreach($query->rows as $row) {
            $sql = "SELECT * FROM ".DB_PREFIX."category_to_store WHERE store_id=". $to ." AND category_id = ".$row['category_id'];

            if(count($query->row == 0)) {
                echo $ins = "INSERT INTO " . DB_PREFIX . "category_to_store
                    SET category_id = ".$row['category_id'].",
                    store_id = ". $to .";";
                echo "<br />";
            }
        }
    }

    public function sorFix(){
        // echo $q = "select DISTINCT(p.model), p2s.product_id, p2s.store_id from oc_product p INNER JOIN oc_product_to_store p2s ON p.product_id = p2s.product_id where p.model like '%-SOR'";
        $q = "SELECT product_id FROM oc_product WHERE model like '%-SOR'";
        $query = $this->db->query($q);

        foreach($query->rows as $row) {
            echo '<br />';
            //echo $q = "UPDATE ".DB_PREFIX."product_to_store SET store_id = 6 WHERE product_id = ".$row['product_id'].";";
            //echo $q = "DELETE FROM oc_product_to_store WHERE product_id = ". $row['product_id'].";";
            echo $q = "INSERT INTO oc_product_to_store SET product_id = ". $row['product_id'].", store_id=9;";

        }
    }

    /**
     * Get Customer By City (Which Have Ordered Already)
     * function getCustomersByStateWithOrder
     * @param string state
     * @return Customer Details With Orders Details
     * @author Ravindra Singh
     * @date 14-12-2015
     * */
    public function getCustomersByStateWithOrder($state = '')
    {
        $arr_order = array();

        $zone_sql = "SELECT zone_id FROM ".DB_PREFIX."zone WHERE LCASE(name) = trim('".strtolower($state)."')";
        $query = $this->db->query($zone_sql);
        $zone_id = 0;

        if(count($query->rows) > 0) {
            foreach ($query->rows as $record) {
                $zone_id = $record['zone_id'];
            }
        }


        if(!empty($zone_id) && $zone_id > 0 ) {
            $sql = "SELECT c.customer_id,c.email,c.telephone,
                        CONCAT(c.firstname, ' ', c.lastname) AS name,
                        CONCAT(a.address_1,a.address_2) AS address,
                        a.postcode,a.company,a.city,
                        (SELECT COUNT(*) FROM oc_order WHERE customer_id = c.customer_id) AS total_order,
                        MAX(o.date_added) AS last_order_date
                        FROM oc_customer c
                        INNER JOIN oc_address a
                        ON c.customer_id = a.customer_id
                        INNER JOIN oc_order o
                        ON c.customer_id = o.customer_id
                        WHERE a.zone_id = ". $zone_id;

            $sql .= " GROUP BY c.customer_id ORDER BY o.date_added DESC";

            $query = $this->db->query($sql);

            $arr_order = array();

            foreach ($query->rows as $record) {
                $order_sql = "SELECT o.order_no, o.date_added, o.total
                            FROM oc_order o
                             WHERE o.customer_id=" .
                    "'" . $record['customer_id'] . "'";

                $record['orders'] = $this->db->query($order_sql)->rows;
                $arr_order[] = $record;
            }
        }


        return $arr_order;
    }

    public function searchedTerms(){

        $count_sql = "UPDATE oc_searched_terms st,
                (SELECT searched_term_id, keyword, COUNT(keyword) AS ck
                FROM oc_searched_terms
                GROUP BY keyword HAVING ( ck > 1 )) sst
              SET st.count = sst.ck 
              WHERE st.searched_term_id = sst.searched_term_id
              AND st.keyword = sst.keyword";
        $this->db->query($count_sql);

        $duplicate_sql = "SELECT searched_term_id FROM oc_searched_terms
                    WHERE searched_term_id NOT IN
                    (
                    SELECT MIN(searched_term_id)
                    FROM oc_searched_terms
                    GROUP BY keyword
                    )";
        $id = $this->db->query($duplicate_sql)->rows;
        $ids = array();
        foreach ($id as $value) {
            $ids[] = $value['searched_term_id'];
        }
        $st_ids = implode(',', $ids);

        if(!empty($st_ids)){
            $delete_sql = ("DELETE FROM oc_searched_terms WHERE searched_term_id IN (". $st_ids .")");
            $this->db->query($delete_sql);
        }

        $model_sql = "UPDATE oc_searched_terms st,
                (SELECT searched_term_id, keyword
                FROM oc_searched_terms
                Where keyword LIKE '%-%' OR keyword LIKE '%\_%') AS im
              SET st.is_model = 1
              WHERE st.searched_term_id = im.searched_term_id
              AND st.keyword = im.keyword";
        $this->db->query($model_sql);

        $model_sql = "UPDATE oc_searched_terms SET has_results = 1";
        $this->db->query($model_sql);
    }

    public function getIncentiveData($staff_id, $month, $year) {

        $sql = "SELECT oo.order_id, osub.suborder_id, oo.order_no, oo.code_version, 
                       oo.date_added as order_date
                FROM oc_order oo 
                INNER JOIN oc_suborder osub ON osub.order_id = oo.order_id 
                WHERE 
                  osub.order_status_id > 2 
                  AND oo.store_id IN (".WSB_STORES_ID.") 
                  AND MONTH(oo.date_added  ) = ".$month ."
                  AND YEAR(oo.date_added  ) = ".$year ."
                  AND oo.sales_staff_id = ".$staff_id."
                GROUP BY oo.order_id, osub.suborder_id ";

        $query = $this->db->query($sql);

        $results = '';
        $i = 0;


        foreach ($query->rows as $order) {
            $results[$i]['order_no'] = $order['order_no'];
            $results[$i]['order_date'] = $order['order_date'];


            if($order['code_version'] == '2.0') {

                $q = "SELECT SUM(quantity*piece_in_set*(price_per_piece + discount_per_piece)) as order_value_after_all_discount
                      FROM oc_order_product oop 
                      WHERE oop.order_id = ". $order['order_id'];

                $query_total = $this->db->query($q);

                if ($query_total->num_rows > 0) {
                    $purchase_value = $query_total->row['order_value_after_all_discount'];
                } else {
                    $purchase_value = 0;
                }
                //@todo -  loop should be change on suborder
                $credit_note_value = $this->getCreditNoteValue($order['order_id']);
                $results[$i]['version'] = '2.0';
            } else {
                //1.0 -> order placed before sub order breakup
                $q = "SELECT SUM(oot.value) as order_value_after_all_discount
                    FROM oc_order_total oot 
                    WHERE oot.order_id = ".$order['order_id'] ."
                    AND oot.suborder_id = ". $order['suborder_id']."
                    AND (oot.code LIKE 'sub_total' 
                    OR oot.code LIKE 'discount' 
                    OR oot.code LIKE 'deal_discount' 
                    OR oot.code LIKE 'cashback' 
                    OR oot.code LIKE 'coupon' 
                    OR oot.code LIKE 'paycharge')
                    ";

                $query_total = $this->db->query($q);

                if ($query_total->num_rows > 0) {
                    $purchase_value = $query_total->row['order_value_after_all_discount'];
                } else {
                    $purchase_value = 0;
                }

                $credit_note_value = $this->getCreditNoteValueForOldOrders($order['order_id']);
                $results[$i]['version'] = '1.0';

            }

            $results[$i]['purchase_value'] = $purchase_value;
            $results[$i]['credit_note_value'] = $credit_note_value;

            $results[$i]['net_order_value'] = $results[$i]['purchase_value'] - $results[$i]['credit_note_value'];

            $i++;
        }

        return $results;
    }

    public function getCreditNoteValue($order_id) {
        //get credit note for 2.0
        $q = "SELECT  sum(r.quantity*(oop.price_per_piece+oop.discount_per_piece)) as credit_note_value
                    FROM " . DB_PREFIX . "return r 
                    INNER JOIN " . DB_PREFIX . "seller_debit_note sdn
                    ON r.debit_note_id = sdn.debit_note_id
                    INNER JOIN " . DB_PREFIX . "order_product oop
                    ON oop.order_product_id = r.order_product_id
                    WHERE oop.order_id = " . $order_id ."
                    AND sdn.debit_note_status = 1
                    ";



        $query_cn_products = $this->db->query($q);
        $credit_note_value = 0;
        if ($query_cn_products->num_rows > 0) {
            $credit_note_value = $query_cn_products->row['credit_note_value'];
        }

        return $credit_note_value;
    }

    public function getCreditNoteValueForOldOrders($order_id) {
        //get subtotal

        $sub_total = $this->db->query("SELECT oot.value as sub_total
                                  FROM oc_order_total oot 
                                  WHERE oot.order_id = ".$order_id ."
                                  AND oot.code LIKE 'sub_total'"
        )->row['sub_total'];


        $discounts = $this->db->query("SELECT SUM(oot.value) as discounts
                                                FROM oc_order_total oot 
                                                WHERE oot.order_id = ".$order_id ."
                                                
                                                AND ( 
                                                   oot.code LIKE 'discount' 
                                                OR oot.code LIKE 'deal_discount' 
                                                OR oot.code LIKE 'cashback' 
                                                OR oot.code LIKE 'coupon' 
                                                OR oot.code LIKE 'paycharge')
                                                "
        )->row['discounts'];
        if($order_id == 8046) {
            echo $discounts ."--".$sub_total;
            echo '<br />'.  $discount_percentage = $discounts/$sub_total;
            echo '<br />'. (1-$discount_percentage);
        }
        $discount_percentage = $discounts/$sub_total;

        echo $q = "SELECT  sum(r.quantity*(oop.price_per_piece*".(1+$discount_percentage).")) as credit_note_value
                    FROM " . DB_PREFIX . "return r 
                    INNER JOIN " . DB_PREFIX . "seller_debit_note sdn
                    ON r.debit_note_id = sdn.debit_note_id
                    INNER JOIN " . DB_PREFIX . "order_product oop
                    ON oop.order_product_id = r.order_product_id
                    WHERE oop.order_id = " . $order_id ."
                    AND sdn.debit_note_status = 1";


        if($order_id == 8046) {
            echo $q;

        }
        $query_cn_products = $this->db->query($q);

        $credit_note_value = 0;
        if ($query_cn_products->num_rows > 0) {
            $credit_note_value = $query_cn_products->row['credit_note_value'];
        }
        return $credit_note_value;
    }

    public function getCustomerByTelephone($telephone) {
        if(defined('WSB_STORES_ID')) {
            $store_ids =  WSB_STORES_ID;
        }else{
            $store_ids = $this->config->get('config_store_id');
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($telephone) . "' AND store_id IN (".$store_ids.")";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getCartProducts($customer_id) {
        $cart_products = $this->cart->getProducts($customer_id);

        $products = array();

        foreach ($cart_products as $key => $product) {
            $option_data = array();
            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'type' => $option['type']
                );
            }

            $products[] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'sku' => $product['sku'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'piece_in_set' => $product['piece_in_set'],
                'total_pieces' => $product['total_pieces'],
                'weight_per_piece' => !empty($product['weight_per_piece']) ? $product['weight_per_piece'] : '',
                'subtract' => $product['subtract'],
                'price' => $product['price'],
                'price_per_piece' => $product['price_per_piece'],
                'total' => $product['total'],
                'tax' => $product['tax'],
                'discount_breakup' => $product['discount_breakup'],
                'discount_per_piece' => $product['discount_per_piece'],
                'comment' => $product['set_description'] . " " . $product['comment'],
                'seller_id' => $product['seller_id'],
                'seller_nickname' => $product['seller_nickname'],
                'seller_tax' => $product['seller_tax'],
                'commission' => $product['commission'],
                'store_sales' => $product['store_sales'],
                'store_pickup' => $product['store_pickup'],
                'output_tax_rates' => $product['output_tax_rates'],
                'customer_comment' => $product['comment'],
                'hsn_code' => $product['hsn_code'],
                'transfer_price_per_piece' => $product['transfer_price_per_piece'],
                'unit_id' => $product['unit_id'],
                'sor_product' => $product['sor_product'],
                'wsb_purchase_id' => $product['wsb_purchase_id'],
                'franchise_id' => $product['franchise_id'],
                'notes' => $product['notes'],
                'non_returnable' => $product['non_returnable']
            );

        }

        return $products;
    }

    public function getShortMsg($implode_cid, $mobile_array, $telephone,$controller) {
		
		$sql_msg = " select * from short_message_logs where customer_id IN (".$implode_cid.")";
        
        $query = $this->db_crm->query($sql_msg);
        
        if ($query->num_rows < 1) {
				echo "No record found";
				
				$sql_c = "SELECT c.device_info FROM `oc_customer` c
                WHERE c.customer_id IN (".$implode_cid.")
                ";
                $query_c = $this->db_crm->query($sql_c);
                
                $results_c = $query->rows;
                if ($query->num_rows > 0) {
					 foreach ($results_c as $result_c) {
						
						echo "Device Info:". $result_c['device_info'];
						echo "<br/>Please send thi info also on whatsapp so we can debug";
						
					 }
				} else {
						
						echo "<br />Device info not logged yet, when he willopen the app next time either we would have data or his device information to debug";
				}
				
				die;

		}
		
		
        $results = $query->rows;
        
        
                
        $today = Date('d_M_y');
        $filename = "short_message_".time().".csv";
        $dir_path =  DIR_SYSTEM . 'upload/assets/short_message';
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        $i = 1;
        $full_path = $dir_path . '/'.$filename;
        $handle = fopen($full_path, 'w');

        $i = 1;
        foreach ($results as $result) {

            $customer_id = $result['customer_id'];
            $name = $mobile_array[$customer_id]['name'];
            $message = $result['message'];
            $created = $result['created'];
            $sender_name = $result['sender_name'];
            $message_date = date('d-m-Y', $result['timestamp']);
            $business = $mobile_array[$customer_id]['company'];
            $mobile = $mobile_array[$customer_id]['telephone'];
            

            if ($i == 1) {
                $i++;
                $data = array('Name',
                    'Business Name',
                    'Mobile',
                    'Sender Name',
                    'Message',
                    'Msg Receive date',
                    'Created date'
                    );
                    

                fputcsv($handle,$data);
                $data = array($name, $business, $mobile, $sender_name, $message, $message_date, $created);
                fputcsv($handle,$data);

            } else {
                $i++;
                $data = array($name, $business, $mobile, $sender_name, $message, $message_date, $created);
                fputcsv($handle,$data);
            }

        }
        fclose($handle);
		sleep(5);
        if (file_exists($full_path)) {
            $csv_path = $full_path;

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            
            $mail->Host = $controller->config->get('config_mail_smtp_hostname');
            
            $mail->Port = $controller->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $controller->config->get('config_mail_smtp_username');
            $mail->Password = $controller->config->get('config_mail_smtp_password');
            $mail->setFrom($controller->config->get('config_mail_smtp_username'), 'Rakesh Singh');
            $mail->addReplyTo($controller->config->get('config_email'), 'Wholesale Box');
            $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->Subject = 'Short Message data';
            $mail->Body = 'Please find the csv of Short Message data - '.$telephone;
            $mail->AddAttachment($csv_path);
            //send to admin;
            $mail->send();
			sleep(2);
			unlink($csv_path);
            echo "Well Done!!!";
            exit;

        }
   }
   
   
}
