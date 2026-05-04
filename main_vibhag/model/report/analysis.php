<?php
class ModelReportAnalysis extends Model {
    public function getProductWiseMargin($data = array()){

        $productwise_margins = array();

        $sql = "SELECT op.product_id, ";
        
        if (!empty($data['filter_group_by_order'])) {
            $sql .= " oop.model, oop.seller_sku as sku, oop.transfer_price_per_piece as price, oop.name, ";
        } else {
            $sql .= " op.model, op.sku, op.price,  ";
        }
        
        if(!empty($data['filter_brand_name']) ){
          $sql .= " fd.name AS brand_name, ";
        } else { // no brand filter applied - need to get brand of the product (if exists)
			
	      // Get the brand names in a product using Correlated Subquery
	      // Joining will not work here, as the data may get duplicate, triplicate etc (if more than one brand) 
			
		  $sql .= " (SELECT GROUP_CONCAT(fd1.name) 
		             FROM " . DB_PREFIX . "product_filter pf1 
		             INNER JOIN " . DB_PREFIX . "filter f1 ON f1.filter_id = pf1.filter_id AND f1.filter_group_id = 27 
		             INNER JOIN " . DB_PREFIX . "filter_description fd1 ON fd1.filter_id = f1.filter_id AND fd1.language_id = 1 
		             WHERE pf1.product_id = oop.product_id) AS brand_name, "; 
			
		}

        $sql .= "      SUM( oop.quantity * oop.piece_in_set ) as total_pieces_sold, 
                                             
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                   oop.price_per_piece , 2 )
                          ) AS gross_product_sales, 
                       
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                   oop.discount_per_piece , 2 )
                          ) AS total_product_discount, 
                          
                       SUM( oop.quantity * oop.piece_in_set * 
                             ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                    (oop.price_per_piece + oop.discount_per_piece) * 
                                    (oop.output_tax_rates / 100) , 2 )
                          ) AS total_product_sales_tax, 
                          
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND(oop.transfer_price_per_piece / (1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)),2) 
                          ) AS total_product_purchase, 
                       
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND( oop.transfer_price_per_piece 
                                    - 
                                   ROUND(oop.transfer_price_per_piece / (1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)),2)
                                   , 2 ) ) AS total_product_purchase_tax, 

                       oop.seller_id";
                       
        if ( !empty($data['filter_group_by_order']) ) {
            $sql .= ", o.order_no, 
                       osub.suborder_id, 
                       o.date_added as order_date, 
                       
                       IF(osub.buyer_invoice_id > 0 AND osub.invoice_no > 0, CONCAT(osub.invoice_prefix, osub.invoice_no), '') AS sale_invoice_no, 
                       IF(osub.buyer_invoice_id > 0 AND osub.invoice_no > 0, osub.invoice_date, '') AS invoice_date, 
                       
                       o.customer_id, 
                       c.master_id, 
                       CONCAT(o.firstname, ' ', o.lastname) as customer_name, 
                       o.shipping_company, 
                       o.shipping_city, 
                       o.shipping_zone, 
                       o.shipping_country, 
                       o.shipping_postcode, 
                       oop.comment ";
        }
        
        $sql .= " FROM " . DB_PREFIX . "order o 
                  INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id 
                  INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_id = o.order_id 
                  INNER JOIN " . DB_PREFIX . "ms_seller ms ON ms.seller_id = oop.seller_id 
                  INNER JOIN " . DB_PREFIX . "product op ON op.product_id = oop.product_id ";
        
        if (!empty($data['filter_group_by_order'])) { 
    	  $sql .= " INNER JOIN " . DB_PREFIX . "customer c ON o.customer_id = c.customer_id ";
    	}

        if (!empty($data['filter_brand_name'])) { 
          $sql .= " INNER JOIN " . DB_PREFIX . "product_filter AS pf ON pf.product_id = op.product_id ";
          $sql .= " INNER JOIN " . DB_PREFIX . "filter_description AS fd ON fd.filter_id = pf.filter_id AND fd.language_id = 1 ";
        }
                  
        $sql .= " WHERE oop.suborder_id = osub.suborder_id 
                    AND osub.order_status_id > 0 
                    AND osub.order_status_id != 2 
                    AND o.store_id IN (" . WSB_STORES_ID . ") 
                    AND o.stock_transfer = 0 ";
        
        if( !empty($data['filter_brand_name']) ){
            $sql .= " AND fd.filter_id = ".(int)$data['filter_brand_name'];
        }

        if( empty($data['filter_include_non_invoiced_orders']) ){
            $sql .= " AND osub.invoice_no > 0 
                      AND osub.buyer_invoice_id > 0 
                      AND oop.buyer_invoice_id = osub.buyer_invoice_id ";
        } else {
            $sql .= " AND CASE 
                              WHEN osub.invoice_no > 0 AND osub.buyer_invoice_id > 0 
                                  THEN oop.buyer_invoice_id = osub.buyer_invoice_id 
                              ELSE oop.edit_type IN ('YES', 
                                                     'SELLER_PARTIAL', 
                                                     'SELLER_APPROVED', 
                                                     'SELLER_LATER_DISPATCH', 
                                                     'DAMAGE_BY_COURIER_COMPANY'
                                                    )
                          END
                    ";
        }

        if( isset( $data['filter_wsb_product_code'] ) ){
            if ( !empty($data['filter_group_by_order']) ) {
                $sql .=  " AND oop.model LIKE '%".$this->db->escape($data['filter_wsb_product_code'])."%'";
            } else {
                $sql .=  " AND op.model LIKE '%".$this->db->escape($data['filter_wsb_product_code'])."%'";
            }
        }

        if( isset( $data['filter_seller_sku_code'] ) ){
            if ( !empty($data['filter_group_by_order']) ) {
                $sql .=  " AND oop.seller_sku LIKE '%".$this->db->escape($data['filter_seller_sku_code'])."%'";
            } else {
                $sql .=  " AND op.sku LIKE '%".$this->db->escape($data['filter_seller_sku_code'])."%'";
            }
        }

        if( isset( $data['filter_seller_id'] ) ){
            $sql .=  " AND oop.seller_id = '". (int)($data['filter_seller_id'])."' ";
        }

        if( isset( $data['filter_seller_pickup_city_code'] ) ){
            $sql .=  " AND ms.pickup_city_code = '". $this->db->escape($data['filter_seller_pickup_city_code'])."' ";
        }

        if( isset( $data['filter_category'] ) ){
            $sql .=  " AND opc.category_id = '". (int)($data['filter_category'])."' ";
        }

        if (isset($data['filter_sale_invoice_date_from'])) {
            $sql .= " AND DATE(osub.invoice_date) >=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_from']) . "') ";
        }

        if (isset($data['filter_sale_invoice_date_to'])) {
            $sql .= " AND DATE(osub.invoice_date) <=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_to']) . "') ";
        }

        if (isset($data['filter_order_date_from'])) {
            $sql .= " AND DATE(osub.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
        }

        if (isset($data['filter_order_date_to'])) {
            $sql .= " AND DATE(osub.date_added) <=  DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
        }
        
        if (!empty($data['filter_group_by_order'])) {
            
            if( isset( $data['filter_customer'] ) ){
                $sql .= " AND (CONCAT(o.firstname,' ',o.lastname) LIKE '%" .$this->db->escape(trim($data['filter_customer'])) ."%' 
                               OR o.shipping_company LIKE '%".$this->db->escape(trim($data['filter_customer'])) ."%' 
                              ) ";
            }

            $sql .= " GROUP BY oop.order_product_id 
                      ORDER BY op.product_id ASC, o.order_id ASC";
        } else {
            $sql .= " GROUP BY op.product_id 
                      ORDER BY op.product_id ASC";
        }

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {

          $productwise_margins = $query->rows;

          $product_ids = array_column($productwise_margins, 'product_id');

          if (empty($data['filter_group_by_order'])) {
            // Getting product names
            $name_sql = "SELECT product_id, name FROM " . DB_PREFIX . "product_description 
                         WHERE product_id IN (" . implode(',', $product_ids) . ") 
                           AND language_id = 1";
            $name_query = $this->db->query($name_sql);
            $name_product_ids = array_column($name_query->rows, 'product_id');
            $name_productid_wise = array_combine($name_product_ids, $name_query->rows); 
          }
          
          // Getting product categories
          $categories_sql = "SELECT opc.product_id, 
                                    GROUP_CONCAT(DISTINCT ocd.name) AS categories 
                             FROM " . DB_PREFIX . "product_to_category opc 
                             INNER JOIN " . DB_PREFIX . "category_description ocd ON opc.category_id = ocd.category_id 
                             WHERE opc.product_id IN (" . implode(',', $product_ids) . ") 
                               AND ocd.language_id = 1
                             GROUP BY opc.product_id ";
          $categories_query = $this->db->query($categories_sql);
          $categories_product_ids = array_column($categories_query->rows, 'product_id');
          $categories_productid_wise = array_combine($categories_product_ids, $categories_query->rows); 

          // Getting seller details
          $seller_ids = array_column($productwise_margins, 'seller_id');
          $seller_sql = "SELECT seller_id, nickname, company, city, pickup_city_code 
                         FROM " . DB_PREFIX . "ms_seller 
                         WHERE seller_id IN (" . implode(',', $seller_ids) . ")";
          $seller_query = $this->db->query($seller_sql);
          $sellerwise_details = array_combine(array_column($seller_query->rows, 'seller_id'), $seller_query->rows);

          foreach ($productwise_margins as $key => $value) {

            $margin = $value['gross_product_sales'] + $value['total_product_discount'] - $value['total_product_purchase'];
            $productwise_margins[$key]['margin'] = $margin;

            $margin_percentage = ($margin*100)/($value['gross_product_sales'] + $value['total_product_discount']);
            $productwise_margins[$key]['margin_percentage'] = $margin_percentage;

            if (empty($data['filter_group_by_order'])) {
              // product name
              $productwise_margins[$key]['name'] = !empty($name_productid_wise[$value['product_id']]) ? $name_productid_wise[$value['product_id']]['name'] : '';
            }
            
            // product categories
            $productwise_margins[$key]['categories'] = !empty($categories_productid_wise[$value['product_id']]) ? $categories_productid_wise[$value['product_id']]['categories'] : '';
            
            // product seller details
            $productwise_margins[$key]['nickname'] = !empty($sellerwise_details[$value['seller_id']]) ? $sellerwise_details[$value['seller_id']]['nickname'] : '';
            $productwise_margins[$key]['company'] = !empty($sellerwise_details[$value['seller_id']]) ? $sellerwise_details[$value['seller_id']]['company'] : '';
            $productwise_margins[$key]['city'] = !empty($sellerwise_details[$value['seller_id']]) ? $sellerwise_details[$value['seller_id']]['city'] : '';
            $productwise_margins[$key]['pickup_city_code'] = !empty($sellerwise_details[$value['seller_id']]) ? $sellerwise_details[$value['seller_id']]['pickup_city_code'] : '';
          }

        }

        return $productwise_margins;
    }

    public function getSellerWiseRevenue($data = array()){
      
      $sql = "SELECT oms.seller_id,
                     oms.nickname, 
                     oms.company, 
                     oms.city,
                     oms.date_created,
                     oms.pickup_city_code,                      
                                                                   
                     SUM( (o.currency_value / o.live_currency_conversion_rate) * 
                         oop.quantity * 
                         oop.piece_in_set * 
                        (oop.price_per_piece + oop.discount_per_piece) 
                     ) AS total_product_sales, 
                      
                     SUM( (o.currency_value / o.live_currency_conversion_rate) * 
                         oop.quantity * 
                         oop.piece_in_set * 
                        (oop.price_per_piece + oop.discount_per_piece) * 
                        (CAST(oop.output_tax_rates AS DECIMAL(10,2)) / 100)
                     ) AS total_product_sales_tax,
                      
                     SUM( oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece / 
                          (1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)) ) AS total_product_purchase, 
                   
                     SUM( oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece *  
                          (1 - (1/(1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)))) ) AS total_product_purchase_tax 

              FROM " . DB_PREFIX . "order o 
              INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id 
              INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_id = o.order_id 
              INNER JOIN " . DB_PREFIX . "ms_seller oms ON oms.seller_id = oop.seller_id ";

      if( isset( $data['filter_category'] ) ){
        $sql .= "INNER JOIN " . DB_PREFIX . "product_to_category opc ON opc.product_id = oop.product_id ";
      }

      $sql .= "WHERE oop.suborder_id = osub.suborder_id 
                AND oop.edit_type IN ('DAMAGE_BY_COURIER_COMPANY', 'SELLER_APPROVED', 'SELLER_PARTIAL') 
                AND osub.invoice_no > 0
                AND osub.order_status_id != 2
                AND o.stock_transfer = 0 
                AND o.store_id IN (" . WSB_STORES_ID . ") 
                AND o.franchise_id = 0 ";

      if( isset( $data['filter_seller_id'] ) ){
        $sql .=  " AND oop.seller_id = '". (int)($data['filter_seller_id'])."' ";
      }

      if( isset( $data['filter_category'] ) ){
        $sql .=  " AND opc.category_id = '". (int)($data['filter_category'])."' ";
      }

      if (isset($data['filter_seller_date_added_from'])) {
        $sql .= " AND DATE(oms.date_created) >=  DATE('" . $this->db->escape($data['filter_seller_date_added_from']) . "') ";
      }

      if (isset($data['filter_seller_date_added_to'])) {
        $sql .= " AND DATE(oms.date_created) <=  DATE('" . $this->db->escape($data['filter_seller_date_added_to']) . "') ";
      }

      if (isset($data['filter_sale_invoice_date_from'])) {
        $sql .= " AND DATE(osub.invoice_date) >=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_from']) . "') ";
      }

      if (isset($data['filter_sale_invoice_date_to'])) {
        $sql .= " AND DATE(osub.invoice_date) <=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_to']) . "') ";
      }
      
      if (isset($data['filter_order_date_from'])) {
        $sql .= " AND DATE(osub.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
      }

      if (isset($data['filter_order_date_to'])) {
        $sql .= " AND DATE(osub.date_added) <=  DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
      }

      if( isset( $data['filter_seller_pickup_city_code'] ) ){
        $sql .=  " AND oms.pickup_city_code = '". $this->db->escape($data['filter_seller_pickup_city_code'])."' ";
      }

      $sql .= "GROUP BY oms.seller_id
               ORDER BY oms.nickname ASC";
      $query = $this->db->query($sql);

      foreach ($query->rows as $key => $value) {
          
        $margin = $value['total_product_sales'] - $value['total_product_purchase'];
        $query->rows[$key]['margin'] = round($margin,2);
        
        $margin_percentage = ($margin*100)/$value['total_product_sales'];
        $query->rows[$key]['margin_percentage'] = round($margin_percentage,2);
      }

      return $query->rows;        
    }
    
    
    public function getOrderWiseMargin($data = array()){

        $orderwise_margins = array();

        $sql ="SELECT  o.order_id, 
                       o.order_no, 
                       osub.suborder_id, 
                       IF(osub.order_status_id != 2 
                            AND osub.invoice_no > 0 
                            AND osub.buyer_invoice_id > 0, 
                          CONCAT(osub.invoice_prefix, '_', osub.invoice_no), 
                          '') as sale_invoice_no, 
                       IF(osub.order_status_id != 2 
                            AND osub.invoice_no > 0 
                            AND osub.buyer_invoice_id > 0, 
                          osub.invoice_date, 
                          '') as sale_invoice_date, 
                       o.date_added as order_date, 
                       o.customer_id, 
                       oc.master_id,  
                       CONCAT(o.firstname, ' ', o.lastname) as customer_name, 
                       o.shipping_company, 
                       o.shipping_city, 
                       o.shipping_zone, 
                       o.shipping_country, 
                       o.shipping_postcode, 
                       o.payment_code, 
                       oos.name as order_status, 

                      (SELECT 
                          MIN(oh.date_added) AS processing_date
                        FROM
                            oc_order_history AS oh
                        WHERE
                            oh.order_id = o.order_id
                                AND oh.suborder_id = osub.suborder_id
                                AND oh.order_status_id IN (9 , 16)
                      ) AS processing_date,
                       
                       ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                          osub.shipping_charge , 2 
                       )  AS shipping_charged, 
                       
                       IF(osub.gst = 1, 
                          ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                  osub.shipping_charge * 
                                  (MAX(oop.output_tax_rates)/100) , 2 
                               ), 
                          0 
                         ) AS tax_on_shipping, 
                                             
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                   oop.price_per_piece , 2 )
                          ) AS gross_product_sales, 
                          
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                   oop.discount_per_piece , 2 )
                          ) AS total_product_discount, 
                       
                       SUM( oop.quantity * oop.piece_in_set * 
                             ROUND( (o.currency_value / o.live_currency_conversion_rate) * 
                                    (oop.price_per_piece + oop.discount_per_piece) * 
                                    (oop.output_tax_rates / 100) , 2 )
                          ) AS total_product_sales_tax, 
                          
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND(oop.transfer_price_per_piece / (1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)),2) 
                          ) AS total_product_purchase, 
                       
                       SUM( oop.quantity * oop.piece_in_set * 
                            ROUND( oop.transfer_price_per_piece 
                                    - 
                                   ROUND(oop.transfer_price_per_piece / (1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)),2)
                                   , 2 ) ) AS total_product_purchase_tax 
                            
                FROM " . DB_PREFIX . "order o 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id 
                INNER JOIN " . DB_PREFIX . "order_status oos ON oos.order_status_id = osub.order_status_id 
                INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_id = o.order_id                 
                INNER JOIN " . DB_PREFIX . "customer oc ON oc.customer_id = o.customer_id 
                WHERE  oop.suborder_id = osub.suborder_id 
                  AND  osub.order_status_id > 0 
                  AND  o.stock_transfer = 0 
                  AND  oos.language_id = 1 
                  AND  o.store_id IN (" . WSB_STORES_ID . ") 
                ";
        
        if( empty($data['filter_include_cancelled_orders']) ){
            $sql .= " AND osub.order_status_id != 2 ";
        }
        
        if( empty($data['filter_include_non_invoiced_orders']) ){
            $sql .= " AND osub.invoice_no > 0 
                      AND osub.buyer_invoice_id > 0 
                      AND oop.buyer_invoice_id = osub.buyer_invoice_id ";
        } else {
            $sql .= " AND ( CASE 
                              WHEN (osub.order_status_id != 2 AND osub.invoice_no > 0 AND osub.buyer_invoice_id > 0) 
                                  THEN oop.buyer_invoice_id = osub.buyer_invoice_id 
                              
                              WHEN osub.order_status_id != 2 
                                  THEN oop.edit_type IN ('YES', 
                                                         'SELLER_PARTIAL', 
                                                         'SELLER_APPROVED', 
                                                         'SELLER_LATER_DISPATCH', 
                                                         'DAMAGE_BY_COURIER_COMPANY'
                                                        )
                              ELSE 1=1 
                          END )
                    ";
        }

        if( isset( $data['filter_customer'] ) ){
            $sql .= " AND (CONCAT(o.firstname,' ',o.lastname) LIKE '%" .$this->db->escape(trim($data['filter_customer'])) ."%' 
                           OR o.shipping_company LIKE '%".$this->db->escape(trim($data['filter_customer'])) ."%' 
                          ) ";
        }

        if( isset( $data['filter_shipping_city'] ) ){
            $sql .=  " AND o.shipping_city LIKE '%" . $this->db->escape(trim($data['filter_shipping_city'])) . "%' ";
        }

        
        if (isset($data['filter_order_date_from'])) {
            $sql .= " AND DATE(osub.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
        }

        if (isset($data['filter_order_date_to'])) {
            $sql .= " AND DATE(osub.date_added) <=  DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
        }
        

        if (isset($data['filter_sale_invoice_date_from'])) {
            $sql .= " AND DATE(osub.invoice_date) >=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_from']) . "') ";
        }

        if (isset($data['filter_sale_invoice_date_to'])) {
            $sql .= " AND DATE(osub.invoice_date) <=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_to']) . "') ";
        }

        $sql .= " GROUP BY osub.suborder_id  
                  ORDER BY o.order_id ASC, osub.suborder_id ASC ";
                 
        $query = $this->db->query($sql);
        

        if ( $query->num_rows ) {

          $orderwise_margins = $query->rows;
          
          $orders = array_unique(array_column($orderwise_margins, 'order_id'));
          
          $sales_sql = "SELECT ooss.order_id, GROUP_CONCAT(DISTINCT oss.role) as sales_role 
                        FROM " . DB_PREFIX . "order_sales_staff ooss 
                        INNER JOIN " . DB_PREFIX . "sales_staff oss ON ooss.sales_staff_id = oss.staff_id 
                        WHERE ooss.order_id IN (" . implode(",", $orders) . ") 
                        GROUP BY ooss.order_id";
          $sales_query = $this->db->query($sales_sql);
          
          $order_to_sales = array();
          
          if ($sales_query->num_rows) {
              $order_to_sales = array_combine(array_column($sales_query->rows, 'order_id'), 
                                              array_column($sales_query->rows, 'sales_role')
                                             );
          }
          
          foreach ($orderwise_margins as $key => $value) {
            
            $margin = $value['gross_product_sales'] + $value['total_product_discount'] - $value['total_product_purchase'];
            $orderwise_margins[$key]['margin'] = $margin;

            $margin_percentage = ($margin*100)/($value['gross_product_sales'] + $value['total_product_discount']);
            $orderwise_margins[$key]['margin_percentage'] = $margin_percentage;
            
            $orderwise_margins[$key]['sales_role'] = '';
            if (!empty($order_to_sales[$orderwise_margins[$key]['order_id']])) {
                $orderwise_margins[$key]['sales_role'] = $order_to_sales[$orderwise_margins[$key]['order_id']];
            }

          }

        }

        return $orderwise_margins;
    }
    
    
    public function getCustomerWiseRevenue($data = array()){

        $customerwise_revenue = array();

        $sql ="SELECT  c.customer_id, 
                       CONCAT(c.firstname, ' ', c.lastname) as customer_name, 
                       c.date_added as customer_onboarding_date, 
                       o.shipping_company, 
                       o.shipping_city, 
                       o.shipping_zone, 
                       o.shipping_country, 
                       o.shipping_postcode, 
                       COUNT( DISTINCT o.order_id) as no_of_orders, 
                                              
                       SUM( (o.currency_value / o.live_currency_conversion_rate) * 
                             oop.quantity * 
                             oop.piece_in_set * 
                            (oop.price_per_piece + oop.discount_per_piece) 
                          ) AS total_product_sales, 
                          
                       SUM( (o.currency_value / o.live_currency_conversion_rate) * 
                             oop.quantity * 
                             oop.piece_in_set * 
                            (oop.price_per_piece + oop.discount_per_piece) * 
                            (CAST(oop.output_tax_rates AS DECIMAL(10,2)) / 100)
                          ) AS total_product_sales_tax,
                          
                       SUM( oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece / 
                            (1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)) ) AS total_product_purchase, 
                       
                       SUM( oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece *  
                            (1 - (1/(1 + ((IF(oop.seller_cst = 0, oop.seller_input_tax, 0))/100)))) ) AS total_product_purchase_tax 
                       
                FROM " . DB_PREFIX . "order o 
                INNER JOIN " . DB_PREFIX . "customer c ON c.customer_id = o.customer_id 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id 
                INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_id = o.order_id 
                WHERE oop.suborder_id = osub.suborder_id 
                  AND oop.edit_type IN ('DAMAGE_BY_COURIER_COMPANY', 'SELLER_APPROVED', 'SELLER_PARTIAL') 
                  AND  osub.invoice_no > 0
                  AND  o.stock_transfer = 0
                  AND  osub.order_status_id != 2 
                  AND  o.store_id IN (" . WSB_STORES_ID . ") 
                  AND o.franchise_id = 0 ";

        if( isset( $data['filter_customer'] ) ){
            $sql .= " AND (CONCAT(c.firstname,' ',c.lastname) LIKE '%" .$this->db->escape(trim($data['filter_customer'])) ."%' 
                           OR o.shipping_company LIKE '%".$this->db->escape(trim($data['filter_customer'])) ."%' 
                          ) ";
        }

        if( isset( $data['filter_shipping_city'] ) ){
            $sql .=  " AND o.shipping_city LIKE '%" . $this->db->escape(trim($data['filter_shipping_city'])) . "%' ";
        }

        
        if (isset($data['filter_order_date_from'])) {
            $sql .= " AND DATE(osub.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
        }

        if (isset($data['filter_order_date_to'])) {
            $sql .= " AND DATE(osub.date_added) <=  DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
        }
        

        if (isset($data['filter_sale_invoice_date_from'])) {
            $sql .= " AND DATE(osub.invoice_date) >=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_from']) . "') ";
        }

        if (isset($data['filter_sale_invoice_date_to'])) {
            $sql .= " AND DATE(osub.invoice_date) <=  DATE('" . $this->db->escape($data['filter_sale_invoice_date_to']) . "') ";
        }
        
        
        if (isset($data['filter_customer_onboarding_date_from'])) {
            $sql .= " AND DATE(c.date_added) >=  DATE('" . $this->db->escape($data['filter_customer_onboarding_date_from']) . "') ";
        }

        if (isset($data['filter_customer_onboarding_date_to'])) {
            $sql .= " AND DATE(c.date_added) <=  DATE('" . $this->db->escape($data['filter_customer_onboarding_date_to']) . "') ";
        }

        $sql .= " GROUP BY o.customer_id  
                  ORDER BY o.customer_id ASC";
                 
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {

          $customerwise_revenue = $query->rows;

          foreach ($customerwise_revenue as $key => $value) {
            
            $margin = $value['total_product_sales'] - $value['total_product_purchase'];
            $customerwise_revenue[$key]['margin'] = $margin;

            $margin_percentage = ($margin*100)/$value['total_product_sales'];
            $customerwise_revenue[$key]['margin_percentage'] = $margin_percentage;

          }

        }

        return $customerwise_revenue;
    }
    
    public function getProductWiseReturn($data=array()) {
      $selector = "";
      $join     = "";
      $whr      = "";
      if(!empty($data['filter_brand_name'])){
        $selector = " fd.name AS brand_name, ";
        $join .= " INNER JOIN " . DB_PREFIX . "product_filter AS pf ON pf.product_id = oop.product_id ";
        $join .= " INNER JOIN " . DB_PREFIX . "filter_description AS fd ON fd.filter_id = pf.filter_id AND fd.language_id = 1 ";
        $whr  .= " AND fd.filter_id = " . (int)$data['filter_brand_name'] . " ";
      } else { // no brand filter applied - need to get brand of the product (if exists)
			
	      // Get the brand names in a product using Correlated Subquery
	      // Joining will not work here, as the data may get duplicate, triplicate etc (if more than one brand) 
			
		$selector = " (SELECT GROUP_CONCAT(fd1.name) 
		             FROM " . DB_PREFIX . "product_filter pf1 
		             INNER JOIN " . DB_PREFIX . "filter f1 ON f1.filter_id = pf1.filter_id AND f1.filter_group_id = 27 
		             INNER JOIN " . DB_PREFIX . "filter_description fd1 ON fd1.filter_id = f1.filter_id AND fd1.language_id = 1 
		             WHERE pf1.product_id = oop.product_id) AS brand_name, "; 
			
	  }

		  $sql = "
              SELECT  ".$selector."
                oop.product_id,
                oop.model,
                oop.name AS product,
                seller.nickname,
                seller.company,
                seller.city,
                seller.pickup_city_code,
                o.order_no,
                IF(o.stock_transfer = 1, 'YES', 'NO') AS stock_transfer,
                IF(oop.sor_product = 1 , 'YES', 'NO') AS is_sor,
                oop.suborder_id,
                oop.seller_sku,
                oop.quantity,
                oop.price_per_piece,
                oop.discount_per_piece,
                oop.seller_input_tax,
                oop.output_tax_rates,
                oop.transfer_price_per_piece,
                orr.name AS return_reason,
                ora.name AS return_action,
                ocd.name AS category,
                ocr1.return_reason_id,
                ocr1.quantity AS return_qty,
                oop.product_id,
                seller.seller_id, 
                oop.suborder_id,
                CONCAT(cn.credit_note_prefix, cn.credit_note_no) AS cn_no,
                dn.date_added AS dn_date,
                CONCAT(dn.debit_note_prefix, dn.debit_note_no) AS dn_no,
                cp.firm_name AS custom_party_name,
                cn.date_added AS cn_date,
                CONCAT(osub.invoice_prefix, ' ', osub.invoice_no) AS buyer_invoice_no,
                osub.invoice_date AS buyer_invoice_date
              FROM      
                oc_return AS ocr1
                    INNER JOIN
                (SELECT 
                      MAX(return_id) AS return_id,
                  MAX(debit_note_id) AS debit_note_id,
                  MAX(credit_note_id) AS credit_note_id
                  FROM
                      oc_return
                GROUP BY 
                  order_product_id, master_return_id
                ) AS ocr2 ON ocr1.return_id = ocr2.return_id AND ocr1.active_row = 1
                    INNER JOIN
                oc_order_product AS oop ON ocr1.order_product_id = oop.order_product_id 
                ".$join." 
                    INNER JOIN
                oc_order o ON oop.order_id = o.order_id
                    INNER JOIN
                oc_suborder osub ON osub.order_id = o.order_id
                    INNER JOIN
                oc_ms_seller AS seller ON seller.seller_id = oop.seller_id
                    INNER JOIN
                oc_return_reason orr ON ocr1.return_reason_id = orr.return_reason_id
                    INNER JOIN
                oc_return_action ora ON ocr1.return_action_id = ora.return_action_id
                    LEFT JOIN
                oc_seller_debit_note AS dn ON dn.debit_note_id = ocr2.debit_note_id
                    AND dn.debit_note_status = 1
                    LEFT JOIN
                oc_custom_parties AS cp ON cp.custom_id = dn.custom_id AND dn.custom_id > 0
                    LEFT JOIN
                oc_credit_note AS cn ON cn.credit_note_id = ocr2.credit_note_id
                    AND cn.credit_note_status = 1
                    LEFT JOIN
                oc_product_to_category AS ocp ON ocp.product_id = oop.product_id
                    LEFT JOIN
                oc_category_description AS ocd ON ocd.category_id = ocp.category_id AND ocd.language_id = 1

                WHERE
                    cn.credit_note_id IS NOT NULL
                    AND oop.suborder_id = osub.suborder_id
                    AND osub.order_status_id != 2
                    AND o.franchise_id = 0 
            ";
        if(!empty($data['filter_brand_name'])){
          $sql .= $whr;
        }
		    
		    if( !empty($data['filter_date_from']) ){//Filter for order date from
            $sql .= " AND DATE(o.date_added) >= DATE('". $this->db->escape($data['filter_date_from']) ."')";
        }
        if( !empty($data['filter_date_to']) ){//Filter for order date To
            $sql .= " AND DATE(o.date_added)  <= DATE('". $this->db->escape($data['filter_date_to']) . "')";
        }
        //Filter for Product Model
        if( !empty($data['filter_model']) ){
            $sql .= " AND oop.model LIKE '%".$this->db->escape($data['filter_model'])."%'";
        }
        //Filter for Product Category
        if( !empty($this->request->post['filter_category_id']) ){
            $sql .= " AND ocd.category_id = ". (int)$this->request->post['filter_category_id'];
        }
        //Filter for Product Seller
        if( !empty($data['filter_seller_id']) ){
            $sql .= " AND seller.seller_id = ". (int)$data['filter_seller_id'];
        }
        //Filter for Seller PickupCityCode
        if( isset( $data['filter_seller_pickup_city_code'] ) ){
          $sql .=  " AND seller.pickup_city_code = '". $this->db->escape($data['filter_seller_pickup_city_code'])."' ";
        }
        //Filter for order Number
        if( !empty($data['filter_order']) ){
            $sql .= " AND o.order_no LIKE '%".$this->db->escape($data['filter_order'])."%'";
        }
        $sql .= " GROUP BY ocr1.order_product_id, ocr1.master_return_id 
				  ORDER BY oop.order_product_id DESC ";
		
  		$query = $this->db->query($sql);
 
  		if($query->num_rows) {
  			return $query->rows;
  		} else {
  			return array();
  		}
  	}


    /**
     * method for get unique seller pickup city code
     * @return  array of pickup city
     * @author vikas, 2017
    */
    public function getSellerPickupCityCode(){
      $sql = "SELECT 
                DISTINCT pickup_city_code 
              FROM 
                " . DB_PREFIX . "ms_seller
              WHERE 
                pickup_city_code IS NOT NULL
                AND pickup_city_code != ''
                ";
      $query = $this->db->query($sql);
      if( $query->num_rows ){
        return array_column($query->rows,'pickup_city_code');
      } else {
        return array();
      }
    }
}
