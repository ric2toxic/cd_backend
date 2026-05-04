<?php

class ControllerTestTest2 extends Controller
{
    //Move order
    public function mvOrd() {
        // check for access token
        if ( isset($_GET['authorize']) and $_GET['authorize'] == '16m@D552' and isset($_GET['order_no']) ) {// validation passed

            // Valid order to move - checking
            $current_query = $this->db->query("SELECT CONCAT(o.firstname , ' ' , o.lastname) as name, o.order_id, o.order_no
                                               FROM oc_order o 
                                               WHERE o.order_no = '" . $this->db->escape($_GET['order_no']) . "' 
                                               AND NOT EXISTS (SELECT 1 FROM oc_suborder osub 
                                                               WHERE osub.order_id = o.order_id AND 
                                                                     osub.order_status_id > 0)  ");
            if (!$current_query->num_rows) {
                echo("Invalid Order No to Move! It should be in Missing.");
                return;
            }

            $current_order_id = (int)($current_query->row['order_id']);
            $current_order_no = (float)($current_query->row['order_no']);
            $client_name = $current_query->row['name'];

            // Find a missing order to move to - dont forget to change the date here
            $new_query = $this->db->query("SELECT o.order_id, o.order_no, o.date_added FROM oc_order o 
                                           JOIN oc_suborder osub on osub.order_id = o.order_id  
                                           WHERE DATE(o.date_added) = '2019-05-31' 
                                             AND o.store_id IN (0,2) 
                                           GROUP BY o.order_id 
                                           HAVING SUM(osub.order_status_id = 0) = COUNT(*) 
                                           ORDER BY o.date_added ASC LIMIT 1");
            if (!$new_query->num_rows) {
                echo("No Available Missing Orders to Move To. Please contact Madhur !");
                return;
            }

            $new_order_id = (int)($new_query->row['order_id']);
            $new_order_no = (float)($new_query->row['order_no']);
            $new_order_date = $new_query->row['date_added'];

            // Cleaning up the missing order
            // Check if there are payment entries.
            $q = $this->db->query("SELECT * from oc_order_payment where order_id = " . (int)$new_order_id)->rows;
            if ( !empty($q) ) {
                echo "check order payment and advance voucher"; echo "<pre>"; print_r($q); echo "</pre>";
                die;
            }
            
		    $this->db->query("DELETE FROM `oc_order_option` WHERE order_id = '" . (int)$new_order_id . "'");
            $this->db->query("DELETE FROM `oc_order_product` WHERE order_id = '" . (int)$new_order_id . "'");
		    $this->db->query("DELETE FROM `oc_order_total` WHERE order_id = '" . (int)$new_order_id . "'");
		    $this->db->query("DELETE FROM `oc_order_history` WHERE order_id = '" . (int)$new_order_id . "'");
            $this->db->query("DELETE FROM `oc_suborder` WHERE order_id = '" . (int)$new_order_id . "'");
            $this->db->query("DELETE FROM `oc_order` WHERE order_id = '" . (int)$new_order_id . "'");
            


            // Move the Current Order
            // Due to FK constraints, order_id get auto updated in other details. 
            // So need to update suborder_id first
            $this->db->query("UPDATE oc_suborder SET suborder_id = '" . $new_order_no . "', 
                                                     date_added = '" . $new_order_date . "' 
                              WHERE order_id = '" . (int)$current_order_id . "'");
            $this->db->query("UPDATE oc_order_option SET suborder_id = '" . $new_order_no . "' 
                              WHERE order_id = '" . $current_order_id . "'");
            $this->db->query("UPDATE oc_order_product SET suborder_id = '" . $new_order_no . "' 
                              WHERE order_id = '" . $current_order_id . "'");           
            
            $this->db->query("UPDATE oc_order SET order_id = '" . $new_order_id . "', 
                                                  order_no = '" . $new_order_no . "', 
                                                  date_added = '" . $new_order_date . "'
                              WHERE order_id = '" . (int)$current_order_id . "'");
            $this->db->query("UPDATE oc_suborder SET order_id = '" . $new_order_id . "' 
                              WHERE order_id = '" . (int)$current_order_id . "'");
            $this->db->query("UPDATE oc_order_option SET order_id = '" . $new_order_id . "'
                              WHERE order_id = '" . $current_order_id . "'");
            $this->db->query("UPDATE oc_order_product SET order_id = '" . $new_order_id . "' 
                              WHERE order_id = '" . $current_order_id . "'");
            $this->db->query("UPDATE oc_order_total SET order_id = '" . $new_order_id . "', 
                                                        suborder_id = '" . $new_order_no . "' 
                              WHERE order_id = '" . $current_order_id . "'");


            echo($client_name . "- Order No: " . $current_order_no . " is moved to New Order No: " . $new_order_no . " Dated " . date_format(new DateTime($new_order_date), "d-M-Y"));
            return;
        } else {
            echo("Invalid Data Or Authorization Not Accepted !");
            return;
        }
    }

    
    
    public function invDta(){
        
        if ( empty($this->request->get['fte']) ) {
            echo "No from_date specified"; exit();
        }
        
        if ( empty($this->request->get['tte']) ) {
            echo "No to_date specified"; exit();
        }
        
        $from = $this->request->get['fte'];
        $to = $this->request->get['tte'];
        
        //goto PURCHASED;      
        //goto CUSTOMER_RETENTION;  
        
        // Number of total orders
        $sql = "SELECT COUNT(DISTINCT o.order_id) AS total_orders
                FROM oc_order o 
                INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
                WHERE osub.order_status_id > 0 
                  AND osub.order_status_id != 2 
                  AND o.store_id IN (0,2,9) 
                  AND DATE(o.date_added) >= DATE('" . $this->db->escape($from) . "')  
                  AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                  AND o.stock_transfer = 0";
        $query = $this->db->query($sql);
        $total_orders = (int)$query->row['total_orders'];
        echo 'Total Orders: ' . $total_orders . '</br>';
        
        
        
        //Suborders
        $sql = "SELECT osub.suborder_id, 
                       o.currency_value, 
                       o.live_currency_conversion_rate, 
                       o.code_version, 
                       osub.shipping_charge, 
                       o.shipping_city
                FROM oc_order o 
                INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
                WHERE osub.order_status_id > 0 
                  AND osub.order_status_id != 2 
                  AND o.store_id IN (0,2,9) 
                  AND DATE(o.date_added) >= DATE('" . $this->db->escape($from) . "')  
                  AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                  AND o.stock_transfer = 0";
        $query = $this->db->query($sql);
        $suborders = $query->rows;
        
        $gross_gmv_sales = 0;
        $gross_gmv_sales_citywise = array();
        
        $gross_gmv_purchase = 0;
        $gross_gmv_purchase_ST = 0;
        $gross_gmv_purchase_AHMD = 0;
        $gross_gmv_purchase_JP = 0;
        $gross_gmv_purchase_DL = 0;
        
        
        $shipping_charged = 0;
        
        $gross_gmv_sales_suborderwise = array();
        
        foreach ( $suborders as $suborder ) {
            
            $sql = "";
            $shipping_sql = "";
            $shipping_in_base_currency = 0;
            
            // GROSS GMV Sales
            if ( (int)$suborder['code_version'] == 1 ) {
                
                $sql = "SELECT SUM(value) AS sum_value 
                        FROM oc_order_total 
                        WHERE suborder_id LIKE '" . $this->db->escape($suborder['suborder_id']) . "' 
                          AND (code LIKE 'sub_total' 
                            OR code LIKE 'tax' 
                            OR code LIKE 'paycharge' 
                            OR code LIKE 'coupon' 
                            OR code LIKE 'discount' 
                            OR code LIKE 'deal_discount' 
                            OR code LIKE 'cashback')";
                
                $shipping_sql = "SELECT SUM(value) AS shipping  
                                 FROM oc_order_total 
                                 WHERE suborder_id LIKE '" . $this->db->escape($suborder['suborder_id']) . "' 
                                   AND code LIKE 'shipping'";
                $shipping_query = $this->db->query($shipping_sql);
                $shipping_in_base_currency = (float)($shipping_query->row['shipping']);
            } else {
                
                $sql = "SELECT SUM(quantity * 
                                   piece_in_set * 
                                   (price_per_piece + discount_per_piece) * 
                                   (1.0 + (CAST(output_tax_rates AS DECIMAL(10,2)) / 100))) AS sum_value
                        FROM oc_order_product 
                        WHERE suborder_id LIKE '" . $this->db->escape($suborder['suborder_id']) . "' ";
                        
                $shipping_in_base_currency = (float)($suborder['shipping_charge']);
            }
            
            $query = $this->db->query($sql);
            $suborder_gross_sale = round(((float)$suborder['currency_value'] * 
                                          (float)($query->row['sum_value'])) / 
                                          (float)($suborder['live_currency_conversion_rate']),2);
                                          
            $gross_gmv_sales_suborderwise[$suborder['suborder_id']] = $suborder_gross_sale;
            
            $gross_gmv_sales += $suborder_gross_sale;
            
            $city = strtolower(trim($suborder['shipping_city']));
            if ($city == 'bengaluru') {
				$city = 'bangalore';
			}
            if ( !isset($gross_gmv_sales_citywise[$city]) ) {
                $gross_gmv_sales_citywise[$city] = 0;
            }
            $gross_gmv_sales_citywise[$city] += $suborder_gross_sale;
                                       
                                       
            // Gross GMV (Purchase)
            $sql = "SELECT oms.nickname, 
                           SUM(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS sum_value
                    FROM oc_order_product oop 
                    INNER JOIN oc_ms_product omp ON omp.product_id = oop.product_id 
                    INNER JOIN oc_ms_seller oms ON oms.seller_id = omp.seller_id 
                    WHERE oop.suborder_id LIKE '" . $this->db->escape($suborder['suborder_id']) . "' 
                    GROUP BY oms.nickname";
            $query = $this->db->query($sql);
            
            foreach ($query->rows as $row) {
                
                if ( strtolower(substr(trim($row['nickname']),-2)) == "st" && strtolower(substr(trim($row['nickname']),0,1)) !== "a" ) {
                    $gross_gmv_purchase_ST += round($row['sum_value'],2);
                } elseif ( strtolower(substr(trim($row['nickname']),-2)) == "st" && strtolower(substr(trim($row['nickname']),0,1)) == "a" ) {
                    $gross_gmv_purchase_AHMD += round($row['sum_value'],2);
                } elseif ( strtolower(substr(trim($row['nickname']),-2)) == "jp" ) {
                    $gross_gmv_purchase_JP += round($row['sum_value'],2);
                } elseif ( strtolower(substr(trim($row['nickname']),-2)) == "dl" ) {
                    $gross_gmv_purchase_DL += round($row['sum_value'],2);
                }
                $gross_gmv_purchase += round($row['sum_value'],2);
            }

            // Delivery Cost Charged - Pass through almost
            
            $shipping += round(((float)$suborder['currency_value'] * $shipping_in_base_currency) / 
                                (float)($suborder['live_currency_conversion_rate']),2);

        }
        
        echo "Total Gross GMV (sales): " . $gross_gmv_sales . "</br>";
        echo "Avg Order Size: " . round($gross_gmv_sales/$total_orders, 2) . "</br>";
        
        echo "</br>";
        
        // Finding individual Gross GMV Sales of Top 3 cities
        arsort($gross_gmv_sales_citywise);
        $i = 0;
        foreach ($gross_gmv_sales_citywise as $city => $gross_sale) {
            $i++;
            echo "City " . $i . " Name: " . $city . "</br>";
            $percent_gross_gmv_sales_city = round(100*$gross_gmv_sales_citywise[$city]/$gross_gmv_sales,2);
            
            echo "Gross GMV (Sales), ".$city." : ".$gross_gmv_sales_citywise[$city]."</br>";
            echo "% Gross GMV (Sales), ".$city." : ".$percent_gross_gmv_sales_city."</br>";
            
            if ($i == 3) {
                break;
            }
        }

        echo "</br></br>";
        
        
        echo "Total Gross GMV (purchase): " . $gross_gmv_purchase . "</br>";
        echo "</br>";
        
        $percent_gmv_purchase_ST = round(100*$gross_gmv_purchase_ST/$gross_gmv_purchase,2);
        echo "% Sourcing from Surat: " . $percent_gmv_purchase_ST . "</br>";
        
        $percent_gmv_purchase_AHMD = round(100*$gross_gmv_purchase_AHMD/$gross_gmv_purchase,2);
        echo "% Sourcing from Ahmedabad: " . $percent_gmv_purchase_AHMD . "</br>";

        $percent_gmv_purchase_JP = round(100*$gross_gmv_purchase_JP/$gross_gmv_purchase,2);
        echo "% Sourcing from Jaipur: " . $percent_gmv_purchase_JP . "</br>";

        $percent_gmv_purchase_DL = round(100*$gross_gmv_purchase_DL/$gross_gmv_purchase,2);
        echo "% Sourcing from Delhi: " . $percent_gmv_purchase_DL . "</br>";
        
        $percent_gmv_purchase_other = 100 - $percent_gmv_purchase_ST - $percent_gmv_purchase_JP - $percent_gmv_purchase_DL - $percent_gmv_purchase_AHMD;
        echo "% Sourcing from Other hubs: " . $percent_gmv_purchase_other . "</br>";
        
        echo "</br></br>";
        
        echo "Total Shipping charged: " . $shipping . "</br>";
        
        echo "</br></br>";
        
        echo "----  REPEAT BUSINESS METRICS ---" . "</br>";
        
        // % of business from old clients vs Total (definition of new clients is the one who have 
        // registered in the same time and ordered in the same time range)
        $sql = "SELECT DISTINCT osub.suborder_id 
                FROM oc_order o 
                INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
                INNER JOIN oc_customer c ON c.customer_id = o.customer_id 
                WHERE osub.order_status_id > 0 
                  AND osub.order_status_id != 2 
                  AND o.store_id in (0,2,9) 
                  AND DATE(o.date_added) >= DATE('" . $this->db->escape($from) . "')  
                  AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                  AND DATE(c.date_added) >= DATE('" . $this->db->escape($from) . "')  
                  AND DATE(c.date_added) <= DATE('" . $this->db->escape($to) . "') 
                  AND o.stock_transfer = 0";
        $query = $this->db->query($sql);
        
        // Gross GMV (sales) NEW
        $gross_gmv_sales_new_1 = 0;
        foreach ($query->rows as $row) {
            $gross_gmv_sales_new_1 += (float)$gross_gmv_sales_suborderwise[$row['suborder_id']];
        }

        $gross_gmv_sales_old_1 = $gross_gmv_sales - $gross_gmv_sales_new_1;
        
        echo "% of business from old clients vs Total (definition of new clients is the one who have </br> 
        registered in the same time and ordered in the same time range): ";
        $percent_old_clients_gross_gmv_sales_1 = round(100*($gross_gmv_sales_old_1 / $gross_gmv_sales),2);
        echo $percent_old_clients_gross_gmv_sales_1 . "</br>";
        
        
        //% of business from New Clients vs Total (First Order from clients registered in this time frame or earlier)
        // Get all customers which ordered first time in this time range
        $sql = "SELECT o.customer_id 
                FROM oc_order o 
                INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
                WHERE osub.order_status_id > 0 
                  AND osub.order_status_id != 2 
                  AND o.store_id in (0,2,9) 
                  AND o.stock_transfer = 0 
                GROUP BY o.customer_id 
                HAVING MIN(DATE(o.date_added)) >= DATE('" . $this->db->escape($from) . "')  
                   AND MIN(DATE(o.date_added)) <= DATE('" . $this->db->escape($to) . "')";
        $query = $this->db->query($sql);
        echo "</br>";
        echo "% of business from New Clients vs Total (First Order from clients registered in this </br> 
        time frame or earlier): ";
        if ( !$query->num_rows ) {
            echo "0 </br>";
        } else {
            $sql = "SELECT DISTINCT osub.suborder_id 
                    FROM oc_order o 
                    INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
                    WHERE osub.order_status_id > 0 
                      AND osub.order_status_id != 2 
                      AND o.store_id in (0,2,9) 
                      AND o.stock_transfer = 0 
                      AND DATE(o.date_added) >= DATE('" . $this->db->escape($from) . "')  
                      AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                      AND o.customer_id IN (" . implode(",",array_column($query->rows, 'customer_id')) . ")";
            $query = $this->db->query($sql);
            
            // Gross GMV (sales) NEW
            $gross_gmv_sales_new_2 = 0;
            foreach ($query->rows as $row) {
                $gross_gmv_sales_new_2 += (float)$gross_gmv_sales_suborderwise[$row['suborder_id']];
            }

            $percent_new_clients_gross_gmv_sales_2 = round(100*($gross_gmv_sales_new_2 / $gross_gmv_sales),2);
            echo $percent_new_clients_gross_gmv_sales_2 . "</br>";
        }
         
       // Customer Retention
       CUSTOMER_RETENTION: 
       
       echo "</br></br>";
       echo "----  Customer Retention ---" . "</br>";
       
       // Total Buyer base
       $sql = "SELECT COUNT(DISTINCT(c.master_id)) as customers
               FROM oc_order o 
               INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
               INNER JOIN oc_customer c ON c.customer_id = o.customer_id 
               WHERE osub.order_status_id > 0 
                 AND osub.order_status_id != 2
                 AND osub.invoice_no > 0  
                 AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                 AND o.stock_transfer = 0 
                 AND o.store_id IN (0,2,9)";
       $query = $this->db->query($sql);
       echo "</br>";
       echo "Total Buyer base: ";
       echo $query->row['customers'] . "</br>";
       
       
       // Repeating buyer (more than 1 order)
       $sql = "SELECT c.master_id, 
                      COUNT(DISTINCT o.order_id) as orders 
               FROM oc_order o 
               INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
               INNER JOIN oc_customer c ON c.customer_id = o.customer_id 
               WHERE osub.order_status_id > 0 
                 AND osub.order_status_id != 2 
                 AND osub.invoice_no > 0 
                 AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                 AND o.stock_transfer = 0 
                 AND o.store_id IN (0,2,9) 
               GROUP BY c.master_id  
               HAVING orders > 1";
       $query = $this->db->query($sql);
       echo "</br>";
       echo "Repeating buyer (2 or more orders): ";
       echo $query->num_rows . "</br>";
       
       // Repeating buyer (2+ orders)
       $sql = "SELECT c.master_id, COUNT(DISTINCT o.order_id) as orders 
               FROM oc_order o 
               INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
               INNER JOIN oc_customer c ON c.customer_id = o.customer_id 
               WHERE osub.order_status_id > 0 
                 AND osub.order_status_id != 2
                 AND osub.invoice_no > 0 
                 AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                 AND o.stock_transfer = 0 
                 AND o.store_id IN (0,2,9) 
               GROUP BY c.master_id 
               HAVING orders > 2";
       $query = $this->db->query($sql);
       echo "</br>";
       echo "Repeating buyer (3 or more orders): ";
       echo $query->num_rows . "</br>";
       
       // Repeating buyer (2+ orders and active in last 3 months)
       $sql = "SELECT c.master_id, COUNT(DISTINCT o.order_id) as orders, DATE(MAX(o.date_added)) as last_order_date
               FROM oc_order o 
               INNER JOIN oc_suborder osub ON osub.order_id = o.order_id 
               INNER JOIN oc_customer c ON c.customer_id = o.customer_id 
               WHERE osub.order_status_id > 0 
                 AND osub.order_status_id != 2 
                 AND osub.invoice_no > 0 
                 AND DATE(o.date_added) <= DATE('" . $this->db->escape($to) . "') 
                 AND o.stock_transfer = 0 
                 AND o.store_id IN (0,2,9) 
               GROUP BY c.master_id 
               HAVING orders > 2 
                  AND last_order_date >= DATE_SUB( DATE('" . $this->db->escape($to) . "'), INTERVAL 3 MONTH )";
       $query = $this->db->query($sql);
       echo "</br>";
       echo "Active Repeating buyer (3 or more orders): ";
       echo $query->num_rows . "</br>";
         
         
         
         exit();
         
        PURCHASED:
        
        //----- Purchased Inventory -----//
        echo "</br></br>";
        echo "---- PURCHASED INVENTORY ---- </br></br>";
        
        // Current STOCK LEVEL
        $sql = "SELECT SUM(op.quantity * 
                           op.piece_in_set * 
                           ROUND((owpb.transfer_price_per_piece/(1 + op.seller_tax/100)), 2)) as current_stock 
                FROM oc_wsb_purchase_breakup owpb 
                INNER JOIN oc_product op ON op.product_id = owpb.product_id 
                WHERE op.quantity > 0 AND op.piece_in_set > 0";
        $query = $this->db->query($sql);
        
        echo "</br></br>";
        echo "Current STOCK Level: ";
        echo round($query->row['current_stock'], 2);
        
        //Inventory purchased in the period
        $sql = "SELECT SUM(owpb.pieces * ROUND((owpb.transfer_price_per_piece/(1 + op.seller_tax/100)), 2)) as total_purchased 
                FROM oc_wsb_purchase owp 
                INNER JOIN oc_wsb_purchase_breakup owpb ON owpb.purchase_id = owp.purchase_id 
                INNER JOIN oc_product op ON op.product_id = owpb.product_id 
                WHERE DATE(owp.invoice_date) >= DATE('" . $this->db->escape($from) . "')  
                  AND DATE(owp.invoice_date) <= DATE('" . $this->db->escape($to) . "')";
        $query = $this->db->query($sql);
        
        echo "</br></br>";
        echo "Inventory purchased in the period: ";
        echo round($query->row['total_purchased'], 2);
        
        
        //Inventory Sold in the period
        $sql = "SELECT  SUM( (o.currency_value / o.live_currency_conversion_rate) * 
                              oop.quantity * 
                              oop.piece_in_set * 
                             (oop.price_per_piece + oop.discount_per_piece) 
                           ) AS total_sold , 
                        SUM(  oop.quantity * 
                              oop.piece_in_set * 
                             ROUND((oop.transfer_price_per_piece / (1 + oop.seller_input_tax/100)), 2)
                           ) AS total_order_purchase 
                FROM oc_wsb_purchase_breakup owpb 
                INNER JOIN oc_order_product oop ON oop.product_id = owpb.product_id 
                INNER JOIN oc_suborder osub ON osub.order_id = oop.order_id 
                INNER JOIN oc_order o ON o.order_id = osub.order_id 
                WHERE osub.suborder_id = oop.suborder_id 
                  AND osub.invoice_no > 0 
                  AND osub.order_status_id != 2 
                  AND DATE(osub.invoice_date) >= DATE('" . $this->db->escape($from) . "')  
                  AND DATE(osub.invoice_date) <= DATE('" . $this->db->escape($to) . "')";
        $query = $this->db->query($sql);
        
        echo "</br></br>";
        echo "Inventory sold in the period: ";
        echo round($query->row['total_sold'], 2);
        
        echo "</br></br>";
        echo "Margins made on purchased inventory: ";
        $margin = $query->row['total_sold'] - $query->row['total_order_purchase'];
        echo round($margin, 2);
        
        echo "</br></br>";
        echo "Margin % on Purchase: ";
        echo round(100*$margin/$query->row['total_order_purchase'], 2);
        
        
        // Finding running inventory sold
        // Find stuck product_id(s)
        $sql = "SELECT owpb.product_id, 
                       SUM(op.quantity * 
                           op.piece_in_set * 
                           ROUND((owpb.transfer_price_per_piece/(1 + op.seller_tax/100)), 2)) as current_stock, 
                       SUM(owpb.pieces) as total_pieces_purchased, 
                       DATE(MAX(owp.invoice_date)) as last_purchase_date, 
                       DATE(MIN(owp.invoice_date)) as first_purchase_date 
                FROM oc_wsb_purchase_breakup owpb 
                INNER JOIN oc_wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
                INNER JOIN oc_product op ON op.product_id = owpb.product_id 
                WHERE op.quantity > 0 AND op.piece_in_set > 0 
                GROUP BY owpb.product_id 
                HAVING DATE_SUB(NOW(), INTERVAL 30 DAY) > DATE(last_purchase_date)";
        $current_inventory_query = $this->db->query($sql);
        
        $ten_percent_nonmoving = array();
        $twentyfive_percent_nonmoving = array();
        
        foreach ($current_inventory_query->rows as $row) {
            
            // Find sold pieces in first 30 days of the first purchase date
            $sql = "SELECT  SUM(oop.quantity * 
                              oop.piece_in_set) as pieces_sold_30_days 
                    FROM oc_order_product oop 
                    INNER JOIN oc_suborder osub ON osub.order_id = oop.order_id 
                    INNER JOIN oc_order o ON o.order_id = osub.order_id 
                    WHERE oop.product_id = '" . (int)$row['product_id'] . "' 
                       AND osub.suborder_id = oop.suborder_id 
                       AND osub.invoice_no > 0 
                       AND osub.order_status_id != 2 
                       AND DATE(osub.invoice_date) >= DATE('" . $this->db->escape($row['first_purchase_date']) . "')  
                       AND DATE(osub.invoice_date) <= DATE_ADD('" . $this->db->escape($row['first_purchase_date']) . "', INTERVAL 30 DAY) 
                       ";
            $query = $this->db->query($sql);
            
            $pieces_sold_30_days = (int)$query->row['pieces_sold_30_days'];
            
            if ( $pieces_sold_30_days <= 0.1 * (int)$row['total_pieces_purchased'] ) {
                $ten_percent_nonmoving[] = $row;
            }
            
            if ( $pieces_sold_30_days <= 0.25 * (int)$row['total_pieces_purchased'] ) {
                $twentyfive_percent_nonmoving[] = $row;
            }
        }
            
        // Finding Non moving inventory
        echo "</br></br>";
        echo "10% Non Moving: ";
        echo round(array_sum(array_column($ten_percent_nonmoving, 'current_stock')), 2);
        
        echo "</br></br>";
        echo "25% Non Moving: ";
        echo round(array_sum(array_column($twentyfive_percent_nonmoving, 'current_stock')), 2);

        
    }    
    
    
    
    public function stockUpFranchiseProducts() {
		
		//To add/update product quantity for franchise stock--- Start
		$franchise_info = array();
		$franchise_info['customer_id'] = (int)$_GET['c'];
		$franchise_info['order_id']    = (int)$_GET['o'];
		$franchise_info['suborder_id'] = $_GET['s'];

		$franchise_product = new FranchiseProduct($this);
		$franchise_product->AddOrUpdateFranchiseProduct($franchise_info);
	}
    
    
 
}
