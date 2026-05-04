<?php
class OrderStores {

  private $_pending_status          = ['YES','SELLER_LATER_DISPATCH'];
  private $_partial_done_status     = ['REJECTED_WRONG_PRODUCT','REJECTED_SELLER_DAMAGE','REJECTED_WSB_DAMAGE',
                                        'DAMAGE_BY_COURIER_COMPANY','SELLER_APPROVED','SELLER_PARTIAL','TRANSFER_TO_WSB_BOOKS'];
  private $_seller_cancelled_status = ['SELLER_NOT_SUPPLIED'];
  private $_customer_cancelled_status = ['CANCELLED_BY_CUSTOMER'];  

    /**
     * Method for get order Pickup Done
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return records of pickup done array
     * @author: vikas, 2017
     */

    public function getOrderPickupDone( $db, $seller_id, $filter_data ){

      $sql_1 = "  SELECT o.order_no,
                     o.order_id,
                     o.date_added,
                     date(o.date_added) as order_date,
                     osub.suborder_id,
                     '' as sor_invoice_no,
                     '' as sor_invoice_date,
                     SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as sale_amt,
                     GROUP_CONCAT(CONCAT(si.seller_invoice_prefix,si.seller_invoice_no)) as seller_invoices,
                     GROUP_CONCAT(DISTINCT DATE(si.date_added) ) as invoices_date,
                     SUM(IF(osub.order_status_id != 9 AND osub.order_status_id != 16, 1,0)) as order_dispatched_check,
                     SUM(IF(osub.order_status_id=9 OR osub.order_status_id=16,
                          IF(oop.edit_type = 'SELLER_NOT_SUPPLIED'
                              OR oop.edit_type ='SELLER_LATER_DISPATCH'
                              OR oop.edit_type ='YES',
                            1,
                            IF(TRIM(oop.pickup_status)='Received',0,1 )
                          ),
                          0
                       )
                     ) as not_picked_completely
              FROM oc_seller_invoice si 
              INNER JOIN oc_order_product oop 
                ON oop.seller_invoice_id = si.seller_invoice_id 
              INNER JOIN oc_suborder osub 
                ON osub.order_id = oop.order_id 
              INNER JOIN oc_order o 
                ON o.order_id = osub.order_id 
              WHERE oop.seller_id = '" .(int)$seller_id. "'
                AND oop.order_id = si.order_id 
                AND oop.suborder_id = si.suborder_id 
                AND oop.seller_id = si.seller_id
                AND oop.suborder_id = osub.suborder_id
                AND osub.order_status_id > 1 
                AND o.store_id IN (". WSB_STORES_ID .") 
                AND oop.sor_product = 0 
                AND o.stock_transfer = 0 ";     

      $sql = '';

      if ( !empty( $filter_data['suborder_id'] ) ) {
        $sql .= " AND osub.suborder_id LIKE '" . $filter_data['suborder_id'] . "%'";
      }  
        
      if ( !empty($filter_data['filter_payment_status'])) {
        if( strtolower($filter_data['filter_payment_status']) == 'paid' ){
          $sql .= " AND si.trxn_done IN  ('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE')";
        }else if( strtolower($filter_data['filter_payment_status']) == 'un_paid' ){
          $sql .= " AND si.trxn_done IN  ('NOT_DONE')";
        } 
      }  

      $sql .= " GROUP BY oop.suborder_id ";
      $sql .= " HAVING (order_dispatched_check > 0 OR not_picked_completely = 0) ";
/*
      if(!empty( $filter_data['filter_invoice_date_from'] ) && !empty( $filter_data['filter_invoice_date_to'] ) ){
        $date_from = "'". date('Y-m-d',strtotime($filter_data['filter_invoice_date_from'])) ."'";
        $date_to = "'". date('Y-m-d',strtotime($filter_data['filter_invoice_date_to'])) ."'";
        $sql .= " AND (checkAtleastOneDateInGivenRange(invoices_date, $date_from , $date_to)) ";
      }
*/

      if (!empty($filter_data['filter_order_invoice'])) {
          $sql .= " AND (seller_invoices LIKE '" . $filter_data['filter_order_invoice'] . "' 
          OR o.order_no LIKE '" . $filter_data['filter_order_invoice'] . "')";
      } else {
          if ( !empty( $filter_data['filter_invoice_no'] ) ) {
              $sql .= " AND seller_invoices LIKE '" . $filter_data['filter_invoice_no'] . "'";
          }

          if ( !empty( $filter_data['filter_order_no'] ) ) {
              $sql .= " AND o.order_no LIKE '" . $filter_data['filter_order_no'] . "'";
          }
      }

      if ( !empty($filter_data['filter_sale_from']) && !empty($filter_data['filter_sale_to']) ) {
        $sql .= " AND (sale_amt >= '" . (float)$filter_data['filter_sale_from'] . "' 
        AND sale_amt <= '" . (float)$filter_data['filter_sale_to'] . "')";
      }           

      if ( !empty($filter_data['sort']) ) {
        $sql .= " ORDER BY " . $filter_data['sort'];
      } else {
        $sql .= " ORDER BY o.order_id";
      }

      if ( !empty($filter_data['order']) && ($filter_data['order'] == 'DESC') ) {
        $sql .= " DESC";
      } else {
        $sql .= " ASC";
      }

      if( !$filter_data['download_pickup_done_report'] ){
        if( !empty($filter_data['limit']) && $filter_data['limit'] != 'all'){        
          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {
            if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
          }   
        }
      }
     
      $sql = $sql_1 . $sql;


      $query = $db->query($sql);
      $results = array();
     
      
      if( $query->num_rows >0 ){
        
        $order_ids = array_column($query->rows, 'order_id');
        
        // seller invoices
        $sllr_inv_sql = "SELECT si.seller_invoice_id,
                                si.seller_invoice_prefix, 
                                si.seller_invoice_no,
                                date(si.date_added) as date_added,
                                si.order_id,
                                si.suborder_id,
                                si.trxn_amount,
                                si.trxn_done,
                                si.trxn_utr,
                                date(si.trxn_utr_date) as trxn_utr_date,
                                SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as seller_inv_amount
                         FROM " . DB_PREFIX . "seller_invoice si
                         INNER JOIN " . DB_PREFIX . "order_product oop
                            ON oop.seller_invoice_id = si.seller_invoice_id AND oop.sor_product='0'
                         WHERE si.order_id IN ('" . implode("', '", $order_ids) . "')
                            AND si.seller_id = '" .(int)$seller_id. "'
                         GROUP BY oop.seller_invoice_id
                         ORDER BY si.order_id DESC  ";

        $sllr_inv_query = $db->query($sllr_inv_sql);
        $seller_invoices = array();
        if($sllr_inv_query->num_rows ){
          foreach($sllr_inv_query->rows as $key => $seller_inv_datas){
            $seller_invoices[$seller_inv_datas['suborder_id']][] = $seller_inv_datas;
          }
        }

        // seller debit note
        $debit_note_sql_1 = "SELECT sdn.order_id,
                                    sdn.suborder_id,
                                  oop.order_product_id, 
                                  sdn.debit_note_no, 
                                  sdn.debit_note_id, 
                                  sdn.debit_note_prefix,
                                  re.quantity*oop.transfer_price_per_piece as debit_note_amount,
                                  sdn.date_added,
                                  sdn.trxn_done,
                                  sdn.trxn_amount,
                                  sdn.trxn_utr,
                                  sdn.trxn_utr_date
                          FROM oc_seller_debit_note sdn
                          INNER JOIN
                            oc_return re ON sdn.debit_note_id = re.debit_note_id
                          INNER JOIN
                            oc_order_product oop ON oop.order_product_id = re.order_product_id
                          INNER JOIN
                            oc_wsb_purchase wsbp ON wsbp.purchase_id = oop.wsb_purchase_id 
                          WHERE 
                            sdn.order_id IN ('" . implode("', '", $order_ids) . "')
                            AND wsbp.seller_id = " . (int)$seller_id. "
                            AND sdn.debit_note_status = 1 
                            AND oop.suborder_id = sdn.suborder_id
                            AND (sdn.custom_id = 0 OR sdn.custom_id = NULL)
                          GROUP BY 
                            sdn.debit_note_id    
                          ORDER BY 
                            sdn.order_id DESC  ";

        $debit_note_sql_2 = "SELECT sdn.order_id,
                                    sdn.suborder_id,
                                  oop.order_product_id, 
                                  sdn.debit_note_no, 
                                  sdn.debit_note_id, 
                                  sdn.debit_note_prefix,
                                  sdn.debit_note_amount,
                                  sdn.date_added,
                                  sdn.trxn_done,
                                  sdn.trxn_amount,
                                  sdn.trxn_utr,
                                  sdn.trxn_utr_date
                          FROM oc_seller_debit_note sdn
                          INNER JOIN
                            oc_return re ON sdn.debit_note_id = re.debit_note_id
                          INNER JOIN
                            oc_order_product oop ON oop.order_product_id = re.order_product_id
                          WHERE 
                            sdn.order_id IN ('" . implode("', '", $order_ids) . "')
                            AND oop.seller_id = " . (int)$seller_id. "
                            AND sdn.debit_note_status = 1 
                            AND oop.suborder_id = sdn.suborder_id
                            AND (sdn.custom_id = 0 OR sdn.custom_id = NULL)
                          GROUP BY sdn.debit_note_id    
                          ORDER BY sdn.order_id DESC  ";
        $debit_note_sql = "(".$debit_note_sql_1 .") UNION (" . $debit_note_sql_2 .")";
        $debit_note_query = $db->query($debit_note_sql);
        $debit_notes = array();
        if($debit_note_query->num_rows ){
          foreach($debit_note_query->rows as $key => $debit_note_datas){
            $debit_notes[$debit_note_datas['suborder_id']][] = $debit_note_datas;
          }
        }

        // get sale amount
        $sale_amt_sql = "SELECT order_id, 
                                  SUM( quantity * piece_in_set * transfer_price_per_piece ) as sale_amt
                          FROM " . DB_PREFIX. "order_product
                          WHERE order_id IN ('" . implode("', '", $order_ids) . "')
                            AND seller_id = " . (int)$seller_id. "
                            AND seller_invoice_id > 0
                            AND edit_type NOT IN ('SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH')
                          GROUP BY suborder_id  
                          ORDER BY suborder_id DESC ";
        $sale_amt_query = $db->query($sale_amt_sql);
        $sale_amts = array();
        if($sale_amt_query->num_rows ){
          foreach($sale_amt_query->rows as $key => $sale_amt_datas){
            $sale_amts[$sale_amt_datas['order_id']] = $sale_amt_datas;
          }
        }

        $sql_history = "SELECT order_id, 
                               DATE(MIN(date_added)) as order_processing_date 
                        FROM " . DB_PREFIX . "order_history 
                        WHERE order_id IN ('" . implode("', '", $order_ids) . "')
                          AND order_status_id IN (9,16) 
                        GROUP BY suborder_id 
                        ORDER BY order_id DESC";
        $query_history = $db->query($sql_history);
        $order_process_lists = array();
        if($query_history->num_rows ){
          foreach($query_history->rows as $key => $history_datas){
            $order_process_lists[$history_datas['order_id']] = $history_datas;
          }
        }

        $results['records']         = $query->rows;
        $results['seller_invoices'] = $seller_invoices;
        $results['debit_notes']     = $debit_notes;
        $results['order_process_lists'] = $order_process_lists;

        return $results;
      }
    }


    /**
     * Method for get Total Record Pickup Done
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return Total of pickup done records
     * @author: vikas, 2017
     */

    public function getTotalRecordPickupDone( $db, $seller_id, $filter_data ){
        return 9999;
    }

    /**
     * Method for get order Products
     * @param: $db: Database Object
     * @param: $order_id: Interger of order id
     * @param: $suborder_id: string of order id
     * @param: $seller_id: Integer of seller id
     * @param: $edit_type: array of edit type
     * @param: $get_seller_invoice_ids: invoice id with commas in string format
     * @param: $getOnlySor: string of get only sor
     * @return records of pickup done array
     * @author: vikas, 2017
     */

    public function getOrderProducts( $obj , $order_id, $suborder_id, $seller_id, $edit_type = array() , $get_seller_invoice_ids = '' ,$getonlySor = ''){
      
        $obj->load->model('tool/image');
        $sql = "SELECT  oop.order_product_id,
                        oop.product_id,
                        oop.name,
                        oop.seller_sku,
                        oop.quantity,
                        oop.piece_in_set,
                        (oop.quantity * oop.piece_in_set) as total_pieces,
                        oop.transfer_price_per_piece,
                        oop.seller_input_tax,
                        oop.seller_cst,
                        oop.comment,
                        oop.suborder_id,
                        oop.seller_id,
                        oop.sor_product,
                        oop.seller_invoice_id,
                        oop.pickup_status, 
                        oop.edit_history,
                        oop.combo_product_id, ";

        if($getonlySor == 'get_sor' ){
          $sql .= " wsbp.invoice_no as sor_invoice_no,";
          $sql .= " wsbp.date_added as sor_invoice_date,";
        } 

        $sql .= "                
                        osi.seller_invoice_no,
                        osi.date_added
                FROM oc_order o
                INNER JOIN oc_order_product oop
                  ON ( oop.order_id = o.order_id )";
        
        if($getonlySor == 'get_sor' ){
          $sql .= " INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp
                  ON ( oop.wsb_purchase_id = wsbp.purchase_id AND oop.sor_product = 1)";
        }
                  
        $sql .= "LEFT JOIN oc_seller_invoice osi
                  ON (oop.seller_invoice_id=osi.seller_invoice_id)  
                WHERE o.order_id = '" . (int)$order_id  . "'
                  AND oop.suborder_id = '" . $obj->db->escape($suborder_id) . "' 
                  AND o.stock_transfer = 0 ";

        if($getonlySor == 'get_sor' ){
          $sql .= " AND wsbp.seller_id = '" . (int)$seller_id  . "' AND oop.sor_product='1'";
        }else{
          $sql .= "AND oop.seller_id = '" . (int)$seller_id . "' AND oop.sor_product='0'";
        }

        if( !empty($edit_type) ){
          $sql .= " AND oop.edit_type IN ('" . implode("','", $edit_type). "')";
        }

        if( !empty($get_seller_invoice_ids) ){
          $sql .= " AND oop.seller_invoice_id IN ('" . $get_seller_invoice_ids . "')"; 
        }

        $query = $obj->db->query($sql);

        $product_record_array = array();
        
        $combo_products_array = array();

        array_walk($query->rows, function(&$products , $key) use (&$product_record_array, &$combo_products_array){
          $product_record_array[$products['suborder_id']][$products['order_product_id']] = $products;
          // creating map for combo product
          if ($products['combo_product_id'] != $products['product_id']) {
            $combo_products_array[$products['combo_product_id']][(int)$products['seller_invoice_id']][sizeof(unserialize($products['edit_history']))][] = $products['order_product_id'];
          }
        });
        
        foreach ( $product_record_array as $suborder_key => $subroders_data) {
          foreach ( $subroders_data as $key => $values) {
            // get image
            $query_image = $obj->db->query("SELECT image FROM " . DB_PREFIX ."product WHERE product_id = " . (int)$values['product_id'] ."");
            if( $query_image->num_rows ){
              $product_record_array[$suborder_key][$key]['image'] = $obj->model_tool_image->resize($query_image->row['image'],
                                                                        $obj->config->get('config_image_additional_width'),
                                                                        $obj->config->get('config_image_additional_height')
                                                                        );
                $product_record_array[$suborder_key][$key]['mobile_image'] = $obj->model_tool_image->resizeBasedOnLargeDimension($query_image->row['image'], '400');

            } else {
              $product_record_array[$suborder_key][$key]['image'] = '';
                $product_record_array[$suborder_key][$key]['mobile_image'] = '';
            }

            $product_record_array[$suborder_key][$key]['href'] = $obj->url->link('product/product', 'product_id=' . $values['product_id']);
            

            $seller_invoice_obj = SellerInvoice::getOrderProductWiseCalculation( $values );
            $product_record_array[$suborder_key][$key]['no_of_piece']    = $seller_invoice_obj['no_of_piece'];
            $product_record_array[$suborder_key][$key]['price_per_piece']= $seller_invoice_obj['price_per_piece'];
            $product_record_array[$suborder_key][$key]['product_amount'] = number_format($seller_invoice_obj['product_amount'],2);
            $product_record_array[$suborder_key][$key]['vat_cst_rate']   = $seller_invoice_obj['vat_cst_rate'];
            $product_record_array[$suborder_key][$key]['vat_cst_amount'] = $seller_invoice_obj['vat_cst_amount'];
            $product_record_array[$suborder_key][$key]['total_amount']   = number_format($seller_invoice_obj['total_amount'],2);
            $product_record_array[$suborder_key][$key]['amount_per_piece']   = $seller_invoice_obj['total_amount']/($seller_invoice_obj['no_of_piece']?$seller_invoice_obj['no_of_piece']:1);
            $product_record_array[$suborder_key][$key]['partial']    = $this->checkPartialOrder($obj, $values['product_id'], $key);
            $product_record_array[$suborder_key][$key]['sibling_associates']    =  isset($combo_products_array[$values['combo_product_id']]) ? $combo_products_array[$values['combo_product_id']][(int)$values['seller_invoice_id']][sizeof(unserialize($values['edit_history']))] : array();
          }
        }
        return $product_record_array;
    }


    /**
     * Method for get Partial status
     * @param: $db: Database Object
     * @param: $product_id: Integer of product id
     * @author: veer, 2018
     */

    public function checkPartialOrder($obj, $product_id, $order_product_id = '')
    {
      // check for category 
      // for catalog categories, can't mark a product partial
      $sql = "SELECT category_id
                FROM " . DB_PREFIX . "product_to_category
                WHERE product_id = ". (int)$product_id ." 
                  AND category_id IN (".CATALOG_CATEGORIES.") limit 1";

       $query = $obj->db->query($sql); 
       if( $query->num_rows > 0 ){
            return false;
       }   
       
       if (empty($order_product_id)) return true; 
       
       // check order product 
       // can't mark combo product partial
       $sql = "SELECT product_id, combo_product_id
                 FROM " . DB_PREFIX . "order_product
                 WHERE order_product_id = '". (int)$order_product_id ."' ";
 
        $query = $obj->db->query($sql); 
        if( $query->num_rows > 0 ){
             if ($query->row['product_id'] != $query->row['combo_product_id']) {
               return false;
             }
        } 
        
        return true;        
    }

    /**
     * Method for get Order Tentative
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return records of tentative Order (pending orders) array
     * @author: vikas, 2017
     */

    public function getOrderTentative( $db, $seller_id, $filter_data ){
        $sql = "SELECT o.order_id,
                       osub.suborder_id, 
                       o.order_no,
                       o.date_added as order_date_added, 
                       osub.order_status_id,
                       SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub
                  ON ( osub.order_id = o.order_id )
                INNER JOIN " . DB_PREFIX . "order_product oop
                  ON ( oop.order_id = o.order_id )
                WHERE osub.order_status_id = 1 
                  AND oop.seller_id = ". (int)$seller_id ."
                  AND oop.suborder_id = osub.suborder_id 
                  AND o.store_id IN (". WSB_STORES_ID .") 
                  AND oop.sor_product='0' 
                  AND oop.edit_type='YES' 
                  AND o.stock_transfer = 0 ";

                
                if ( !empty( $filter_data['order_id'] ) ) {
                 $sql .= " AND o.order_id = " . (int) $filter_data['order_id'];
                }

                if ( !empty( $filter_data['suborder_id'] ) ) {
                 $sql .= " AND osub.suborder_id = " . (int) $filter_data['suborder_id'];
                }

                $sql .= " GROUP BY oop.order_id ";

                if ( !empty($filter_data['sort']) ) {
                    $sql .= " ORDER BY " . $filter_data['sort'];
                } else {
                    $sql .= " ORDER BY o.date_added";
                }

                if ( !empty($filter_data['order']) && ($filter_data['order'] == 'DESC') ) {
                    $sql .= " DESC";
                } else {
                    $sql .= " ASC";
                }

                if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {

                    if ($filter_data['start'] < 0) {
                        $filter_data['start'] = 0;
                    }

                    if ($filter_data['limit'] < 1) {
                        $filter_data['limit'] = 20;
                    }

                    $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
                }

        $query = $db->query($sql);

        if( $query->num_rows ){
            return $query->rows;
        } else {
            return array();
        }
    }

    /**
     * Method for get Order Tentative
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return records of tentative Order (pending orders) array
     * @author: vikas, 2017
     */

    public function getTotalOrderTentative( $db, $seller_id, $filter_data =array() ){

      $sql = "SELECT o.order_id,
                       osub.suborder_id, 
                       o.order_no,
                       o.date_added as order_date_added, 
                       osub.order_status_id,
                       SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub
                  ON ( osub.order_id = o.order_id )
                INNER JOIN " . DB_PREFIX . "order_product oop
                  ON ( oop.order_id = o.order_id )
                WHERE osub.order_status_id = 1 
                  AND oop.seller_id = ". (int)$seller_id ."
                  AND oop.suborder_id = osub.suborder_id 
                  AND o.store_id IN (". WSB_STORES_ID .") 
                  AND oop.sor_product='0' 
                  AND oop.edit_type='YES'
                  AND o.stock_transfer = 0 ";

                $sql .= " GROUP BY oop.order_id ";  

            if ( !empty( $filter_data['order_id'] ) ) {
                 $sql .= " AND o.order_id LIKE '%" . $filter_data['order_id'] . "%'";
                }  
            
            if ( !empty( $filter_data['suborder_id'] ) ) {
                 $sql .= " AND osub.suborder_id = " . (int) $filter_data['suborder_id'];
                }

        $query = $db->query($sql);

        return $query->num_rows;
    }

    /**
     * Method for get Pickup order Requested
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return records of Pickup order requested array
     * @author: vikas, 2017
     *        : MSA May 2019(Updated)
     */
    public function getPickpupOrderRequested( $db, $seller_id, $filter_data ){

          $sql = "SELECT oop.order_id,
                         oop.seller_id,
                         oop.suborder_id,
                         o.order_no,
                         o.date_added,
                         SUM(oop.quantity* oop.piece_in_set* oop.transfer_price_per_piece) as total,
                         (  SELECT MIN(oh.date_added) FROM oc_order_history oh 
                            WHERE oh.order_id = oop.order_id AND 
                                oh.suborder_id = oop.suborder_id AND 
                                      oh.order_status_id IN (".implode(',',ORDER_STATUS_CLUSTERS['processed']).")
                         ) AS order_processing_date ,
                        
                        SUM(IF(oop.pickup_status != 'Received'
                                OR oop.seller_invoice_id = 0
                                OR oop.seller_invoice_id IS NULL ,
                                1,
                                0 
                              )
                            ) as not_picked_completely,

                        SUM(IF(oop.edit_type IN ('".implode("','", $this->_pending_status)."'),1, 0)) AS pending_status_count,
                        SUM(IF(oop.edit_type IN ('".implode("','", $this->_partial_done_status)."'),1, 0)) AS partial_done_status_count,
                        SUM(IF(oop.edit_type IN ('".implode("','", $this->_seller_cancelled_status)."'),1, 0)) AS seller_cancel_status_count,
                        SUM(IF(oop.edit_type IN ('".implode("','", $this->_customer_cancelled_status)."'),1, 0)) AS customer_cancel_status_count

                  FROM " . DB_PREFIX . "order_product oop
                      
                      INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = oop.order_id 
                                AND osub.order_status_id IN (".implode(',',ORDER_STATUS_CLUSTERS['processed']).") 
                      
                      INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id AND 
                                               o.store_id IN (".WSB_STORES_ID.") AND 
                                               o.stock_transfer = 0   

                  
                    WHERE 
                        oop.seller_id = '".(int)$seller_id ."'  AND                         
                        oop.sor_product = 0 AND 
                        oop.edit_type NOT IN ('".implode("','", $this->_customer_cancelled_status)."') AND 
                        oop.suborder_id = osub.suborder_id 

                 ";
        
        if ( !empty( $filter_data['order_id'] ) ) {
            $sql .= " AND o.order_id = " .$db->escape($filter_data['order_id']);
        }

        if ( !empty( $filter_data['suborder_id'] ) ) {
            $sql .= " AND osub.suborder_id = '" .$db->escape($filter_data['suborder_id'])."'";
        }

        if ( !empty( $filter_data['filter_order_no_requested'] ) ) {
            $sql .= " AND o.order_no LIKE '%" .$db->escape($filter_data['filter_order_no_requested']). "%'";
        }
        
        $sql .= " GROUP BY oop.suborder_id ";
        $sql .= " HAVING not_picked_completely > 0 ";

        if(!empty($filter_data['edit_type_status'])) 
        {
          if( $filter_data['edit_type_status'] == 'pending' ) {
            
            $sql .= " AND pending_status_count > 0 ";
          
          }else if( $filter_data['edit_type_status'] == 'partial' ) {

            $sql .= " AND ( pending_status_count = 0 AND partial_done_status_count > 0  AND seller_cancel_status_count > 0 )";

          }else if( $filter_data['edit_type_status'] == 'cancelled' ) {

            $sql .= " AND ( pending_status_count = 0 AND partial_done_status_count = 0 AND seller_cancel_status_count > 0 )  ";
          
          }else if($filter_data['edit_type_status'] == 'invoiced') {

            $sql .= " AND ( pending_status_count = 0 AND partial_done_status_count > 0 AND seller_cancel_status_count = 0 )  ";
              
          }
        }

        if ( !empty($filter_data['filter_order_amount_from']) && !empty($filter_data['filter_order_amount_to']) ) {
            $sql .= " AND (total >= '" . (float)$filter_data['filter_order_amount_from'] . "' 
                   AND total <= '" . (float)$filter_data['filter_order_amount_to'] . "')";
        }

        if ( !empty( $filter_data['filter_order_processing_date_from'] ) ) {
            $sql .= " AND order_processing_date >= DATE('" . date('Y-m-d',strtotime($filter_data['filter_order_processing_date_from'])) ."') ";
        }          
 
        if ( !empty( $filter_data['filter_order_processing_date_to'] ) ) {
            $sql .= " AND order_processing_date <= DATE('". date('Y-m-d',strtotime($filter_data['filter_order_processing_date_to'])) . "') ";
        }

        if ( !empty($filter_data['sort']) ) {
            if($filter_data['sort'] == 'order_processing_date_time') {
              $sql .= " ORDER BY order_processing_date ";
            }else{
              $sql .= " ORDER BY " . $filter_data['sort'];  
            }
        } else {

            $sql .= " ORDER BY order_processing_date ";
        }

        if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
          
            $sql .= " ASC";
        } else {
          
            $sql .= " DESC";
        }

        if( !empty($filter_data['limit']) && $filter_data['limit'] != 'all'){
          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {
            if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
          }   
        }

        //echo $sql; die;
        $query = $db->query($sql);

        if( $query->num_rows ){

          $result = array();
          foreach($query->rows as $key => $values){            

            if($values['pending_status_count'] > 0) { 

                $edit_type_status = 'Pending';

            }else if($values['pending_status_count'] == 0  && $values['partial_done_status_count'] > 0 && $values['seller_cancel_status_count'] > 0) {
           
              $edit_type_status = 'Partial';
            
            }else if($values['pending_status_count'] == 0 && $values['partial_done_status_count'] == 0 && $values['seller_cancel_status_count'] > 0) {

              $edit_type_status = 'Cancelled';  
            
            }else if($values['pending_status_count'] == 0 && $values['seller_cancel_status_count'] == 0 && $values['partial_done_status_count'] > 0){ 

              $edit_type_status = 'Success';  
              
            }

            $result[$values['order_id']][] = array(
                'order_id'    => $values['order_id'],
                'suborder_id' => $values['suborder_id'],
                'order_no'    => $values['order_no'],
                'date_added'  => $values['date_added'],
                'total'       => $values['total'],
                'order_processing_date' => $values['order_processing_date'],
                'edit_type_status' => $edit_type_status
              );
          }

           return $result;
        } else {
            return array();
        }        
    }

    /**
     * Method for get Total Order Pickup Requested
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return Total order of pickup requested
     * @author: vikas, 2017
     */

    public function getTotalOrderPickpupRequested( $db, $seller_id, $filter_data ){
        $sql = "SELECT COUNT(dt.suborder_id) AS total_rows FROM ( ";
        $sql .= "SELECT oop.suborder_id,
                       SUM(oop.quantity* oop.piece_in_set* oop.transfer_price_per_piece) as total,
                       (  SELECT MIN(oh.date_added) FROM oc_order_history oh 
                          WHERE oh.order_id = oop.order_id AND 
                              oh.suborder_id = oop.suborder_id AND 
                                    oh.order_status_id IN (".implode(',',ORDER_STATUS_CLUSTERS['processed']).")
                       ) AS order_processing_date ,
                      
                      SUM(IF(oop.pickup_status != 'Received'
                              OR oop.seller_invoice_id = 0
                              OR oop.seller_invoice_id IS NULL ,
                              1,
                              0 
                            )
                          ) as not_picked_completely,

                      SUM(IF(oop.edit_type IN ('".implode("','", $this->_pending_status)."'),1, 0)) AS pending_status_count,
                      SUM(IF(oop.edit_type IN ('".implode("','", $this->_partial_done_status)."'),1, 0)) AS partial_done_status_count,
                      SUM(IF(oop.edit_type IN ('".implode("','", $this->_seller_cancelled_status)."'),1, 0)) AS seller_cancel_status_count,
                      SUM(IF(oop.edit_type IN ('".implode("','", $this->_customer_cancelled_status)."'),1, 0)) AS customer_cancel_status_count

                FROM " . DB_PREFIX . "order_product oop
                    
                    INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = oop.order_id 
                              AND osub.order_status_id IN (".implode(',',ORDER_STATUS_CLUSTERS['processed']).") 
                    
                    INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id AND 
                                             o.store_id IN (".WSB_STORES_ID.") AND 
                                             o.stock_transfer = 0   

                
                  WHERE 
                      oop.seller_id = '".(int)$seller_id ."'  AND                         
                      oop.sor_product = 0 AND 
                      oop.edit_type NOT IN ('".implode("','", $this->_customer_cancelled_status)."') AND 
                      oop.suborder_id = osub.suborder_id 

               ";
      
            if ( !empty( $filter_data['order_id'] ) ) {
                $sql .= " AND o.order_id = " .$db->escape($filter_data['order_id']);
            }

            if ( !empty( $filter_data['suborder_id'] ) ) {
                $sql .= " AND osub.suborder_id = '" .$db->escape($filter_data['suborder_id'])."'";
            }

            if ( !empty( $filter_data['filter_order_no_requested'] ) ) {
                $sql .= " AND o.order_no LIKE '%" .$db->escape($filter_data['filter_order_no_requested']). "%'";
            }
            
            $sql .= " GROUP BY oop.suborder_id ";
            $sql .= " HAVING not_picked_completely > 0 ";

          if(!empty($filter_data['edit_type_status'])) 
          {
            if( $filter_data['edit_type_status'] == 'pending' ) {
              
              $sql .= " AND pending_status_count > 0 ";
            
            }else if( $filter_data['edit_type_status'] == 'partial' ) {

              $sql .= " AND ( pending_status_count = 0 AND partial_done_status_count > 0  AND seller_cancel_status_count > 0 )";

            }else if( $filter_data['edit_type_status'] == 'cancelled' ) {

              $sql .= " AND ( pending_status_count = 0 AND partial_done_status_count = 0 AND seller_cancel_status_count > 0 )  ";
            
            }else if($filter_data['edit_type_status'] == 'invoiced') {

              $sql .= " AND ( pending_status_count = 0 AND partial_done_status_count > 0 AND seller_cancel_status_count = 0 )  ";
                
            }
          }

          if ( !empty($filter_data['filter_order_amount_from']) && !empty($filter_data['filter_order_amount_to']) ) {
              $sql .= " AND (total >= '" . (float)$filter_data['filter_order_amount_from'] . "' 
                     AND total <= '" . (float)$filter_data['filter_order_amount_to'] . "')";
          }

          if ( !empty( $filter_data['filter_order_processing_date_from'] ) ) {
              $sql .= " AND order_processing_date >= DATE('" . date('Y-m-d',strtotime($filter_data['filter_order_processing_date_from'])) ."') ";
          }          

          if ( !empty( $filter_data['filter_order_processing_date_to'] ) ) {
              $sql .= " AND order_processing_date <= DATE('". date('Y-m-d',strtotime($filter_data['filter_order_processing_date_to'])) . "') ";
          }

          $sql .= " ) AS dt ";

          
          $query = $db->query($sql);
          
          return ($query->row['total_rows'] ?? 0);
    }


  /**
   * Method for update edit type and edit history by seller when create a new invoice of our orders
   * @param: $db: Database Object
   * @param: $order_product_id: Integer of order product id
   * @param: $details: array of details with edit_type i.e (seller_approved, seller_not_supplied) and edit_history i.e. (user_id, user_type, user_ip, user_agent)
   * @return NULL
   * @author: vikas, 2017
   */
  public function updateEditTypeBySeller( $db, $order_product_id, $details, $seller_id, $seller_invoice_id = 0 ){
    try{
      $option_data = array();
      $db->query( " START TRANSACTION " );
      $sql = "select oop.edit_history,
                     ooo.product_option_value_id
               FROM oc_order_product oop LEFT JOIN 
                    oc_order_option ooo ON oop.order_product_id = ooo.order_product_id
               WHERE oop.order_product_id = " .(int) $order_product_id . "";
      $query = $db->query($sql);

      $old_edit_history = array();
      
      if($query->num_rows){
        if(!empty($query->row['edit_history'])){
          $old_edit_history = unserialize($query->row['edit_history']);  
        }
        if(!empty($query->row['product_option_value_id'])) {
            $option_data['product_option_value_id'] = $query->row['product_option_value_id'];
        }
      }

      if(isset($details['edit_history'])){
        $old_edit_history[] = $details['edit_history'];
      }

      $sql = "UPDATE " . DB_PREFIX . "order_product
              SET seller_invoice_id = '" .$db->escape($seller_invoice_id) . "',
                  edit_type = '" . $db->escape(key($details['edit_type'])) . "',
                  edit_history = '" . $db->escape(serialize($old_edit_history)) . "',
                  last_modified = NOW()
              WHERE order_product_id = '" . (int)$order_product_id . "'";        
      
      $db->query($sql); 
      if(key($details['edit_type']) == 'SELLER_NOT_SUPPLIED'){
        //Code changes by Nilesh as per new requirement for option products quantity wil zero in option and overall product quantity will be sum of available quantity of options seller product stock update will be only one function
        //As per new requirement stock out is not required for purchase inventory 
        $is_seller_can_invoice = SellerInfo::checkSellerInvoiceToBeGenerated($db, $seller_id);
        if (!empty($is_seller_can_invoice)) {
            OrderEdit::updateSellerStock($db, (int)$details['edit_type']['SELLER_NOT_SUPPLIED']['product_id'], $seller_id, $option_data);
        }
      }
      
      $db->query( " COMMIT " );       
    }
     catch(Exception $e){
         $db->query( " ROLLBACK " );
         echo $e->getMessage();

     }
  }

  /**
   * Method for get Seller Invoice Generated of perticular orders
   * @param: $obj: Class Object
   * @param: $order_id: Integer of order id
   * @param: $suborder_id: String of suborder id
   * @param: $seller_id: Integer of seller id
   * @return array of products with seller invoice 
   * @author: vikas, 2017
   */
  public function getSellerInvoiceGenerated($registry, $obj, $order_id, $suborder_id, $seller_id ){

    $sql = "SELECT si.seller_invoice_id, 
                   si.seller_invoice_no, 
                   si.seller_invoice_prefix,
                   date(si.date_added) as date_added  
            FROM " . DB_PREFIX. "seller_invoice si
            INNER JOIN " . DB_PREFIX. "order_product oop
              ON (oop.seller_invoice_id = si.seller_invoice_id)
            WHERE si.order_id = " . $order_id . " 
              AND si.suborder_id LIKE '".$suborder_id."' 
              AND si.seller_id ='" . $seller_id."'
            ORDER BY si.seller_invoice_no ASC" ;
    $query = $obj->db->query($sql);

    if( $query->num_rows ){

      foreach($query->rows as $key => $values){
        $encode_file = '';
        $seller_invoice = array();
         if( !empty($values['seller_invoice_no']) ){
            $seller_invoice['order_id'] = $order_id;
            $seller_invoice['suborder_id'] = $suborder_id;
            $seller_invoice['seller_invoice_no'] = $values['seller_invoice_no'];
            $seller_invoice['seller_invoice_id'] = $values['seller_invoice_id'];
            $seller_invoice['seller_invoice_prefix'] = ( $values['seller_invoice_prefix'] ) ? $values['seller_invoice_prefix'] : '' ;
            $seller_invoice = base64_encode(serialize($seller_invoice));
            $secureFileDload = new SecureFileDownload($registry);
            $encode_file = $secureFileDload->getDownloadLink('seller_invoice', $seller_invoice, false);
         }
         $edit_type = array();

        $inv_map_products[$values['seller_invoice_id']]['invoice_pdf'] = $encode_file;
        $inv_map_products[$values['seller_invoice_id']]['invoice_no'] = $values['seller_invoice_prefix'].''.$values['seller_invoice_no'];
        $inv_map_products[$values['seller_invoice_id']]['date'] = date('d F Y', strtotime($values['date_added']));
        $inv_map_products[$values['seller_invoice_id']]['products_data'] = $this->getOrderProducts($obj , 
                                                                                                   $order_id,
                                                                                                   $suborder_id, 
                                                                                                   $seller_id, 
                                                                                                   $edit_type, 
                                                                                                   $values['seller_invoice_id']);
        $pickup_status = $this->getOrderProducts($obj , 
                                                 $order_id,
                                                 $suborder_id, 
                                                 $seller_id, 
                                                 $edit_type, 
                                                 $values['seller_invoice_id']);
        $inv_map_products[$values['seller_invoice_id']]['pickup_status'] = true;
        $total_invoiced_product = count($pickup_status[$suborder_id]);
        foreach ($pickup_status as $key => $suborder_data) {
          $total_picked_up = 0;
          foreach ($suborder_data as $suborder_id_key => $order_product) {
            if($order_product['pickup_status'] =='Picked_Up'){
              $total_picked_up += 1;
            }
            if($order_product['pickup_status'] =='Received'){
              $inv_map_products[$values['seller_invoice_id']]['pickup_status'] = false;
            }
          }
        }


        $invoiced_date = $values['date_added'];
        $current_date = date('Y-m-d');
        $date_diff = (strtotime($current_date) - strtotime($invoiced_date));
        $days = $date_diff/(60 * 60 * 24);
        $inv_map_products[$values['seller_invoice_id']]['edit_invoice_available'] = true;
        
        if($days > 2){
          $inv_map_products[$values['seller_invoice_id']]['edit_invoice_available'] = false;
        }
        
        if($total_invoiced_product == $total_picked_up){
          $inv_map_products[$values['seller_invoice_id']]['pickup_status'] = false;
        }

      }

      return $inv_map_products;

    } else {
      return false;
    }
  }

  /**
   * Method to check if the invoice number given by the seller is Unique
   * @param $db: Database object
   * @param $seller_id: Integer of seller_id
   * @param $invoice_no: string for invoice_no
   * @return true if invoice_no is Valid and Usable for the new Order
   *         false if invoice_no already exists in DB
   * @author Vikas/Madhur, 2017
   */
  public function checkSellerInvoiceNoIsUnique($db, $seller_id, $invoice_no) {

    $get_fin_dates = getCurrentFinancialYearDates();    
  
    $sql = "SELECT CONCAT(seller_invoice_prefix, seller_invoice_no) as invoice_no 
            FROM " . DB_PREFIX . "seller_invoice 
            WHERE seller_id = '" . (int)$seller_id . "' 
              AND (DATE(date_added) >= '".$get_fin_dates['start_date']."' AND DATE(date_added) <= '".$get_fin_dates['end_date']."' )
              AND CONCAT(seller_invoice_prefix, seller_invoice_no) = '" . $db->escape($invoice_no) . "' 
              AND trxn_done != 'INVALID_NO_GOODS' 
            LIMIT 1";
    $query = $db->query($sql);
    if ($query->num_rows) {
      return false;
    } else {
      return true;
    }
  }

  /** 
   * Method to set Mark Out Of Stock (out of stock (quantity = 0 and edit type = seller_not_given) )
   * @request: array of request with order id , suborder id , array of order product id   
   * @return NULL
   * @author Vikas, 2017
   */
  public function setMarkOutOfStock($db , $data, $seller_id = '' ){
    if(!empty($data['mark_type']) && $data['mark_type'] == 'seller_later_dispatch'){
    
      $comment = 'SELLER_LATER_DISPATCH';
      $edit_type = 'SELLER_LATER_DISPATCH';
    
    } else {
    
      $comment = 'SELLER_NOT_SUPPLIED';
      $edit_type = 'SELLER_NOT_SUPPLIED';
    
    }
    
    $edit_history = array(
                          'user_id' => $seller_id,
                          'user_type' => 'seller',
                          'user_ip' => $_SERVER['SERVER_ADDR'],
                          'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                          'date_added' => date('d-m-Y'),
                          'comment' => $comment
                          );

    // fetching value of subtract from product table and check that value is 0 or 1.
    // if subtract has 0 value that means quantity is not never 0.
    if(empty($data['mark_type'])){

      $sql_pro_info = "SELECT product_id,subtract 
                       FROM " . DB_PREFIX . "product
                       WHERE product_id IN (" . implode(',', $data['product_ids']) . ")";

      $query_pro_query = $db->query($sql_pro_info);
      if( $query_pro_query->num_rows ){
        foreach($query_pro_query->rows as $key => $values){

          if( $values['subtract'] ){
            $data['product_ids'] = array($values['product_id']);  
            $this->insertSellerChangeLog($db, $data, $seller_id);

            $sql = "UPDATE ". DB_PREFIX."product 
              SET quantity = '0'
              WHERE product_id IN (" . $values['product_id'] . ")";
            $db->query($sql);
          }
        }
      }
    }    

    $history_sql = "SELECT order_product_id,
                           edit_history
            FROM " . DB_PREFIX . "order_product
            WHERE order_id = ". (int)$data['order_id']. "
                  AND suborder_id = '" . $db->escape($data['suborder_id']) . "'
                  AND order_product_id IN (" . implode(',', $data['order_product_ids']) . ")";
    $history_query = $db->query($history_sql);
    $multiple_edit_history = array();
    $opid_edit_his_map = array_combine(array_column($history_query->rows, 'order_product_id'), $history_query->rows);

    foreach ( $opid_edit_his_map as $order_product_id_key => $edit_history_value) {
      if(empty($edit_history_value['edit_history'])){
        $multiple_edit_history[0] = $edit_history;
        $oop_sql = "UPDATE " . DB_PREFIX . "order_product
                    SET edit_type = '" . $db->escape($edit_type). "',
                        edit_history = '". $db->escape(serialize($multiple_edit_history))."',
                        last_modified = NOW()
                    WHERE order_id = ". (int)$data['order_id']. "
                      AND suborder_id = '" . $db->escape($data['suborder_id']) . "'
                      AND order_product_id = " . (int)$order_product_id_key . "";
        $db->query($oop_sql);
      } else {
        $multiple_edit_history = unserialize($edit_history_value['edit_history']);
        $multiple_edit_history[] = $edit_history;
         $oop_sql = "UPDATE " . DB_PREFIX . "order_product
                    SET edit_type = '" . $db->escape($edit_type). "',
                        edit_history = '". $db->escape(serialize($multiple_edit_history))."',
                        last_modified = NOW()
                    WHERE order_id = ". (int)$data['order_id']. "
                      AND suborder_id = '" . $db->escape($data['suborder_id']) . "'
                      AND order_product_id = " . (int)$order_product_id_key . "";
        $db->query($oop_sql); 
      }
    }
    //Update order and suborder total
    OrderEdit::updateOrderTotalsDueVariousAction($db, $data['order_id'], $data['suborder_id']);
  }

   /** 
   * Method to seller change log
   * @param: $db: Object of Database   
   * @param: $data: array of data   
   * @param: $seller_id : Integer of seller Id   
   * @return NULL
   * @author Vikas, 2017
   */
  public function insertSellerChangeLog($db , $data, $seller_id ){
    $seller_nick_name = SellerInfo::getSellerFirmDetails($db, $seller_id)['nickname'];
    $oop_sql = "SELECT product_id,
                       quantity,
                       sku
                FROM " . DB_PREFIX . "product 
                WHERE product_id IN (" . implode(',', $data['product_ids']) . ")"; 
    $oop_query = $db->query($oop_sql);
    
    if( $oop_query->num_rows ){
      foreach($oop_query->rows as $key => $values){
        $sql = "INSERT INTO ". DB_PREFIX."seller_change_log 
                SET seller_id = '" . $seller_id . "',
                    product_id = '" . (int)$values['product_id'] . "',
                    product = '" . $db->escape($values['sku']) . "',
                    nickname = '" . $db->escape($seller_nick_name) . "',
                    updated_type = '" . $db->escape('set_quantity') ."',
                    old = '" . (int)$values['quantity'] .  "',
                    new = 0 ,
                    modified = NOW() ";
        $db->query($sql);
      }
    } else {
      return false;
    }
  }

  public function getReturnOrderDetails($db, $seller_id, $filter_data, $flag = false) {
		
	$sql = "
        SELECT 
  				osbn.debit_note_no,
  				osbn.debit_note_amount as debit_note_amount,
  				oo.order_no,
  				oo.order_id as order_id,
  				osbn.date_added as debit_note_date,
                GROUP_CONCAT(DISTINCT osi.seller_invoice_id) as seller_invoice_id,
                GROUP_CONCAT(DISTINCT CONCAT(osi.seller_invoice_prefix,osi.seller_invoice_no, ';', DATE(osi.date_added), ';', COALESCE(osi.trxn_amount,0))) as seller_invoice_number
				FROM 
          oc_seller_debit_note osbn
				INNER JOIN oc_return ore
				  on osbn.debit_note_id = ore.debit_note_id
				INNER JOIN oc_order_product oop
				  on oop.order_product_id = ore.order_product_id
				INNER JOIN oc_order oo
				  on oo.order_id = oop.order_id
        INNER JOIN oc_seller_invoice osi
          ON osi.seller_invoice_id = oop.seller_invoice_id
				WHERE 
          osbn.debit_note_status = 1 
				  AND osi.seller_id = ". $seller_id."
          AND (osbn.custom_id = 0 OR osbn.custom_id = NULL)
			";
	
	if ( !empty( $filter_data['filter_order_no'] ) ) {
            $sql .= " AND oo.order_no LIKE '%" .$db->escape($filter_data['filter_order_no']). "%'";
       }
    
    if ( !empty( $filter_data['filter_debit_ref_no'] ) ) {
            $sql .= " AND osbn.debit_note_no LIKE '%" .$db->escape($filter_data['filter_debit_ref_no']). "%'";
       }

    $sql .= "GROUP BY osbn.debit_note_id ";
	
    $having_in_sql = false;

    if (!empty($filter_data['filter_debit_rate_to']) ) {
		
		if(empty($filter_data['filter_debit_rate_to'])) {
			$filter_data['filter_debit_rate_from'] = 0;	
		}
		
		$sql .= (!$having_in_sql ? " HAVING " : " AND ");
        $having_in_sql = true;
        $sql .= " (debit_note_amount >= '" . (float)$filter_data['filter_debit_rate_from'] . "' 
                  AND debit_note_amount <= '" . (float)$filter_data['filter_debit_rate_to'] . "')";
     }
   
     if ( !empty( $filter_data['filter_sale_from'] ) ) {
          $sql .= (!$having_in_sql ? " HAVING " : " AND ");
          $having_in_sql = true;
          $sql .= " debit_note_date >= DATE('" . date('Y-m-d',strtotime($filter_data['filter_sale_from'])) ."') ";
     }          
 
     if ( !empty( $filter_data['filter_sale_to'] ) ) {
          $sql .= (!$having_in_sql ? " HAVING " : " AND ");
          $having_in_sql = true;
          $sql .= " debit_note_date <= DATE('". date('Y-m-d',strtotime($filter_data['filter_sale_to'])) . "') ";
     }        
	
     if ( !empty($filter_data['sort']) ) {
          $sql .= " ORDER BY " . $filter_data['sort'];
     } else {
          $sql .= " ORDER BY debit_note_date ";
     }

     if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
          $sql .= " ASC";
     } else {
          $sql .= " DESC";
     }
	
     if ( (int)($flag) == 1 && isset($filter_data['start']) || isset($filter_data['limit']) ) {

          if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
          }

          if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
          }

          $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
     }
	
  
     $query = $db->query($sql);
   
     if( $query->num_rows ){
        return $query->rows;
     } else {
          return array();
     }        
  }

  /**
     * Method for get Sor Product
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return Sor Product records
     */
  public function getOrderSorProduct( $db, $seller_id, $filter_data ){
      $sql = "SELECT o.order_id,
                       osub.suborder_id, 
                       o.order_no,
                       o.date_added,
                       wsbp.invoice_no as sor_invoice_no,
                       wsbp.date_added as sor_invoice_date,
                       SUM(oop.quantity* oop.piece_in_set* oop.transfer_price_per_piece)/COUNT(DISTINCT oh.order_history_id) AS sale_amt, 
                       SUM(oop.quantity* oop.piece_in_set* oop.transfer_price_per_piece)/COUNT(DISTINCT oh.order_history_id) as total, 
                       DATE(MIN(oh.date_added)) as order_processing_date,
                       MIN(oh.date_added) as order_processing_date_time,
                       osub.order_status_id,
                       oos.name as order_status_name,
                       SUM(IF(osub.order_status_id != 9 AND osub.order_status_id != 16, 1,0)) as order_dispatched_check,
                  SUM(IF(TRIM(oop.pickup_status)='Received',1,0 )) as picked_up 
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub
                  ON ( osub.order_id = o.order_id ) 
                INNER JOIN " . DB_PREFIX . "order_product oop
                  ON ( oop.order_id = o.order_id )
                INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp
                  ON ( oop.wsb_purchase_id = wsbp.purchase_id )   
                INNER JOIN " . DB_PREFIX . "order_history oh 
                  ON ( oh.order_id = o.order_id )
                INNER JOIN " . DB_PREFIX . "order_status oos 
                  ON ( oos.order_status_id = osub.order_status_id )   
                WHERE 
                  wsbp.seller_id = '".(int)$seller_id ."'
                  AND oop.suborder_id = osub.suborder_id 
                  AND oh.suborder_id = osub.suborder_id 
                  AND oop.suborder_id = oh.suborder_id 
                  AND osub.order_status_id > 1 
                  AND o.store_id IN (". WSB_STORES_ID .") 
                  AND oop.sor_product='1' 
                  AND oop.pickup_status='Received' 
                  AND o.stock_transfer = 0 
                  AND oos.language_id  = 1";
     
       if ( !empty( $filter_data['order_id'] ) ) {
            $sql .= " AND o.order_id LIKE '%" .$db->escape($filter_data['order_id']). "%'";
        } 

      if ( !empty( $filter_data['suborder_id'] ) ) {
            $sql .= " AND osub.suborder_id LIKE '%" .$db->escape($filter_data['suborder_id']). "%'";
        }
 

        if ( !empty( $filter_data['filter_order_no'] ) ) {
            $sql .= " AND o.order_no LIKE '%" .$db->escape($filter_data['filter_order_no']). "%'";
        }

      if ( !empty( $filter_data['filter_invoice_no'] ) ) {
            $sql .= " AND wsbp.invoice_no LIKE '%" .$db->escape($filter_data['filter_invoice_no']). "%'";
        }

      if ( !empty($filter_data['filter_payment_status'])) {
          if( strtolower($filter_data['filter_payment_status']) == 'paid' ){
              $sql .= " AND si.trxn_done IN  ('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE')";
          }else if( strtolower($filter_data['filter_payment_status']) == 'un_paid' ){
              $sql .= " AND si.trxn_done IN  ('NOT_DONE')";
          }
      }

      $sql .= " GROUP BY oop.suborder_id ";
      $sql .= " HAVING (order_dispatched_check > 0 OR picked_up > 0) ";

        if ( !empty($filter_data['filter_order_amount_from']) && !empty($filter_data['filter_order_amount_to']) ) {
            $sql .= " AND (total >= '" . (float)$filter_data['filter_order_amount_from'] . "' 
                   AND total <= '" . (float)$filter_data['filter_order_amount_to'] . "')";
        }

        if ( !empty( $filter_data['filter_invoice_date_from'] ) ) {
            $sql .= " AND order_processing_date >= DATE('" . date('Y-m-d',strtotime($filter_data['filter_invoice_date_from'])) ."') ";
        }

        if ( !empty( $filter_data['filter_invoice_date_to'] ) ) {
            $sql .= " AND order_processing_date <= DATE('". date('Y-m-d',strtotime($filter_data['filter_invoice_date_to'])) . "') ";
        }
        if ( !empty( $filter_data['filter_sale_from'] ) ) {
            $sql .= " AND sale_amt >='".$filter_data['filter_sale_from']."' ";
        }

        if ( !empty( $filter_data['filter_sale_to'] ) ) {
            $sql .= " AND sale_amt <='".$filter_data['filter_sale_to']."' ";
        }

        if ( !empty($filter_data['sort']) ) {
            $sql .= " ORDER BY " . $filter_data['sort'];
        } else {
            $sql .= " ORDER BY order_processing_date_time ";
        }

        if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if( !empty($filter_data['limit']) && $filter_data['limit'] != 'all'){
          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {
            if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
          }
        }

      $query = $db->query($sql);

      $results = array();

      if( $query->num_rows ){
        $order_ids = array_column($query->rows, 'order_id');
        
        // seller invoices
        /*$sllr_inv_sql = "SELECT si.seller_invoice_id,
                                si.seller_invoice_prefix, 
                                si.seller_invoice_no,
                                date(si.date_added) as date_added,
                                si.order_id,
                                si.trxn_amount,
                                si.trxn_done,
                                si.trxn_utr,
                                date(si.trxn_utr_date) as trxn_utr_date,
                                SUM(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as seller_inv_amount
                         FROM " . DB_PREFIX . "seller_invoice si
                         INNER JOIN " . DB_PREFIX . "order_product oop
                            ON oop.seller_invoice_id = si.seller_invoice_id AND oop.sor_product='0'
                         WHERE si.order_id IN ('" . implode("', '", $order_ids) . "')
                            AND si.seller_id = '" .(int)$seller_id. "'
                         GROUP BY oop.seller_invoice_id
                         ORDER BY si.order_id DESC  ";

        $sllr_inv_query  = $db->query($sllr_inv_sql);*/
        $seller_invoices = array();
        /*if($sllr_inv_query->num_rows ){
          foreach($sllr_inv_query->rows as $key => $seller_inv_datas){
            $seller_invoices[$seller_inv_datas['order_id']][] = $seller_inv_datas;
          }
        }*/

        // seller debit note
        $debit_note_sql_1 = "
                          SELECT 
                              sdn.order_id,
                              oop.order_product_id, 
                              sdn.debit_note_no, 
                              sdn.debit_note_id, 
                              sdn.debit_note_prefix,
                              re.quantity*oop.transfer_price_per_piece as debit_note_amount,
                              sdn.date_added,
                              sdn.trxn_done,
                              sdn.trxn_amount,
                              sdn.trxn_utr,
                              sdn.trxn_utr_date
                          FROM 
                            oc_seller_debit_note sdn
                          INNER JOIN
                            oc_return re ON sdn.debit_note_id = re.debit_note_id
                          INNER JOIN
                            oc_order_product oop ON oop.order_product_id = re.order_product_id
                          INNER JOIN
                            oc_wsb_purchase wsbp ON wsbp.purchase_id = oop.wsb_purchase_id 
                          WHERE sdn.order_id IN ('" . implode("', '", $order_ids) . "')
                            AND wsbp.seller_id = " . (int)$seller_id. "
                            AND sdn.debit_note_status = 1 
                            AND oop.suborder_id = sdn.suborder_id
                            AND (sdn.custom_id = 0 OR sdn.custom_id = NULL)
                          GROUP BY 
                            sdn.debit_note_id    
                          ORDER BY 
                            sdn.order_id DESC  ";

        $debit_note_sql_2 = "SELECT sdn.order_id,
                                  oop.order_product_id, 
                                  sdn.debit_note_no, 
                                  sdn.debit_note_id, 
                                  sdn.debit_note_prefix,
                                  sdn.debit_note_amount,
                                  sdn.date_added,
                                  sdn.trxn_done,
                                  sdn.trxn_amount,
                                  sdn.trxn_utr,
                                  sdn.trxn_utr_date
                          FROM oc_seller_debit_note sdn
                          INNER JOIN
                            oc_return re ON sdn.debit_note_id = re.debit_note_id
                          INNER JOIN
                            oc_order_product oop ON oop.order_product_id = re.order_product_id
                          WHERE sdn.order_id IN ('" . implode("', '", $order_ids) . "')
                            AND oop.seller_id = " . (int)$seller_id. "
                            AND sdn.debit_note_status = 1 
                            AND oop.suborder_id = sdn.suborder_id
                            AND (sdn.custom_id = 0 OR sdn.custom_id = NULL)
                          GROUP BY sdn.debit_note_id
                          ORDER BY sdn.order_id DESC  ";

        $debit_note_sql = "(".$debit_note_sql_1 .") UNION (" . $debit_note_sql_2 .")";
        $debit_note_query = $db->query($debit_note_sql);
        $debit_notes = array();
        if($debit_note_query->num_rows ){
          foreach($debit_note_query->rows as $key => $debit_note_datas){
            $debit_notes[$debit_note_datas['order_id']][] = $debit_note_datas;
          }
        }

        // get sale amount
        $sale_amt_sql = "SELECT order_id, 
                                  SUM( quantity * piece_in_set * transfer_price_per_piece ) as sale_amt
                          FROM " . DB_PREFIX. "order_product
                          WHERE order_id IN ('" . implode("', '", $order_ids) . "')
                            AND seller_id = " . (int)$seller_id. "
                            AND edit_type IN ('SELLER_APPROVED','SELLER_PARTIAL')
                          GROUP BY order_id  
                          ORDER BY order_id DESC ";
        $sale_amt_query = $db->query($sale_amt_sql);
        $sale_amts = array();
        if($sale_amt_query->num_rows ){
          foreach($sale_amt_query->rows as $key => $sale_amt_datas){
            $sale_amts[$sale_amt_datas['order_id']] = $sale_amt_datas;
          }
        }

        $sql_history = "SELECT order_id, 
                               DATE(MIN(date_added)) as order_processing_date 
                        FROM " . DB_PREFIX . "order_history 
                        WHERE order_id IN ('" . implode("', '", $order_ids) . "')
                          AND order_status_id IN (9,16) 
                        GROUP BY order_id 
                        ORDER BY order_id DESC";
        $query_history = $db->query($sql_history);
        $order_process_lists = array();
        if($query_history->num_rows ){
          foreach($query_history->rows as $key => $history_datas){
            $order_process_lists[$history_datas['order_id']] = $history_datas;
          }
        }

        $results['records']         = $query->rows;
        $results['seller_invoices'] = $seller_invoices;
        $results['debit_notes']     = $debit_notes;
        $results['order_process_lists'] = $order_process_lists;

        return $results;
      }
  }
    /**
     * Method for get Total Record Sor Product
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return Total of Sor Product records
     */
    public function getTotalRecordSorProduct( $db, $seller_id, $filter_data ){
      $sql = "SELECT o.order_id,
                       osub.suborder_id, 
                       o.order_no,
                       o.date_added,
                       oh.date_added as order_processing_date,
                       wsbp.invoice_no as sor_invoice_no,
                       wsbp.date_added as sor_invoice_date,
                       SUM(oop.quantity* oop.piece_in_set* oop.transfer_price_per_piece)/COUNT(DISTINCT oh.order_history_id) AS sale_amt, 
                       SUM(oop.quantity* oop.piece_in_set* oop.transfer_price_per_piece)/COUNT(DISTINCT oh.order_history_id) as total, 
                       DATE(MIN(oh.date_added)) as order_processing_date,
                       MIN(oh.date_added) as order_processing_date_time,
                       osub.order_status_id,
                       oos.name as order_status_name,
                       SUM(IF(osub.order_status_id != 9 AND osub.order_status_id != 16, 1,0)) as order_dispatched_check,
                  SUM(IF(TRIM(oop.pickup_status)='Received',1,0 )) as picked_up 
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub
                  ON ( osub.order_id = o.order_id ) 
                INNER JOIN " . DB_PREFIX . "order_product oop
                  ON ( oop.order_id = o.order_id )
                INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp
                  ON ( oop.wsb_purchase_id = wsbp.purchase_id )   
                INNER JOIN " . DB_PREFIX . "order_history oh 
                  ON ( oh.order_id = o.order_id )
                INNER JOIN " . DB_PREFIX . "order_status oos 
                  ON ( oos.order_status_id = osub.order_status_id )   
                WHERE wsbp.seller_id = '".(int)$seller_id ."'
                  AND oop.suborder_id = osub.suborder_id 
                  AND oh.suborder_id = osub.suborder_id 
                  AND oop.suborder_id = oh.suborder_id 
                  AND osub.order_status_id > 1 
                  AND o.store_id IN (". WSB_STORES_ID .") 
                  AND oop.sor_product='1' 
                  AND oop.pickup_status='Received' 
                  AND o.stock_transfer = 0 
                  AND oos.language_id  = 1";

        if ( !empty( $filter_data['order_id'] ) ) {
            $sql .= " AND o.order_id LIKE '%" .$db->escape($filter_data['order_id']). "%'";
        } 

        if ( !empty( $filter_data['suborder_id'] ) ) {
            $sql .= " AND osub.suborder_id LIKE '%" .$db->escape($filter_data['suborder_id']). "%'";
        }         

        if ( !empty( $filter_data['filter_order_no'] ) ) {
            $sql .= " AND o.order_no LIKE '%" .$db->escape($filter_data['filter_order_no']). "%'";
        }

        if ( !empty($filter_data['filter_payment_status'])) {
            if( strtolower($filter_data['filter_payment_status']) == 'paid' ){
                $sql .= " AND si.trxn_done IN  ('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE')";
            }else if( strtolower($filter_data['filter_payment_status']) == 'un_paid' ){
                $sql .= " AND si.trxn_done IN  ('NOT_DONE')";
            }
        }

        $sql .= " GROUP BY oop.suborder_id ";
        $sql .= " HAVING (order_dispatched_check > 0 OR picked_up > 0) ";

        if ( !empty($filter_data['filter_order_amount_from']) && !empty($filter_data['filter_order_amount_to']) ) {
            $sql .= " AND (total >= '" . (float)$filter_data['filter_order_amount_from'] . "' 
                   AND total <= '" . (float)$filter_data['filter_order_amount_to'] . "')";
        }

        if ( !empty( $filter_data['filter_invoice_date_from'] ) ) {
            $sql .= " AND order_processing_date >= DATE('" . date('Y-m-d',strtotime($filter_data['filter_invoice_date_from'])) ."') ";
        }

        if ( !empty( $filter_data['filter_invoice_date_to'] ) ) {
            $sql .= " AND order_processing_date <= DATE('". date('Y-m-d',strtotime($filter_data['filter_invoice_date_to'])) . "') ";
        }
        if ( !empty( $filter_data['filter_sale_from'] ) ) {
            $sql .= " AND sale_amt >='".$filter_data['filter_sale_from']."' ";
        }

        if ( !empty( $filter_data['filter_sale_to'] ) ) {
            $sql .= " AND sale_amt <='".$filter_data['filter_sale_to']."' ";
        }

        if ( !empty($filter_data['sort']) ) {
            $sql .= " ORDER BY " . $filter_data['sort'];
        } else {
            $sql .= " ORDER BY order_processing_date_time ";
        }

        if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        $query = $db->query($sql);
        return $query->num_rows;
    }


  /** 
   * Method to Revert Goods 
   * @param: $db: Object of Database   
   * @param: $data: array of order Product ids   
   * @param: $seller_id : Integer of seller Id   
   * @return NULL
   * @author Vikas, 2017
   */
  public function revertGoodsBySeller($db, $order_product_ids = array(),$seller_id, $order_id, $suborder_id ){
    $return_value = 0;
    $sql = "SELECT invoice_no 
            FROM " . DB_PREFIX . "suborder
            WHERE order_id = '" .$order_id . "'
              AND suborder_id = '" .$suborder_id . "'";
    $query = $db->query($sql);
    if($query->num_rows){
      if(!empty($query->row['invoice_no'])){
        $return_value = 1;
      } else {

        $nick_name = SellerInfo::getSellerFirmDetails($db, (int)$seller_id)['nickname'];
        $edit_history = array(
          'user_id'   => $seller_id,
          'user_name' => $nick_name,
          'user_type' => 'seller',
          'user_ip'   => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '',
          'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
          'date_added'=> date('d-m-Y H:i:s'),
          'comment'   => 'Product revert by seller'
        ); 

        foreach( $order_product_ids as $ord_prd_id ){
          $sql = "SELECT edit_history 
                  FROM ". DB_PREFIX . "order_product
                  WHERE order_product_id = '". $ord_prd_id . "'";
          $query = $db->query($sql);   
          
          $fetch_edit_history = array();
          if( $query->num_rows ){
            $fetch_edit_history = unserialize($query->row['edit_history']);
            $fetch_edit_history[] = $edit_history;

            $update_sql = "UPDATE ".DB_PREFIX . "order_product 
                         SET edit_type = 'YES', 
                             edit_history = '" .serialize($fetch_edit_history) . "'
                         WHERE order_product_id = " . $ord_prd_id ;  
            $update_query = $db->query($update_sql);
          }      
        }
        $return_value = 0;
        //Update order and suborder total
        OrderEdit::updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id);
      }
    }
    return $return_value;
  }
  /**
     * Method for get Pickup order Requested
     * @param: $db: Database Object
     * @param: $seller_id: Integer of seller id
     * @param: $filter_data: array of filters data
     * @return records of sor invoices
     * @author: kalyan, 9th Nov 2017
     */
    public function getSorInvoicesList( $db, $seller_id, $filter_data, $get_count='' ){ 

         $sql = "SELECT 
                  wsbp.purchase_id,
                  wsbp.seller_id,
                  wsbp.invoice_no,
                  wsbp.invoice_date,
                  wsbp.total_purchase_value
                  FROM " . DB_PREFIX . "wsb_purchase wsbp               
                  WHERE wsbp.seller_id = '".(int)$seller_id ."'
                    AND wsbp.sor_purchase =1";
        
        /*
        * set filters
        */
        if ( !empty( $filter_data['filter_invoice_no'] ) ) {
            $sql .= " AND wsbp.invoice_no LIKE '%" .$db->escape($filter_data['filter_invoice_no']). "%'";
        }

       if ( !empty( $filter_data['purchase_id'] ) ) {
            $sql .= " AND wsbp.purchase_id ='" .$db->escape($filter_data['purchase_id']). "'";
        }
        /*
        * set order
        */
        $sql .= " ORDER BY wsbp.purchase_id ";

        if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
            $sql .= " ASC";
        } else {
            $sql .= " ASC";
        }
        /*
        * set limit
        */
        if( !empty($filter_data['limit']) && $get_count != 'get_count'){

          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {

            if ($filter_data['start'] < 0) {

              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

            if ($get_count!='get_count') {

              $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
            }
          }   
        }
        
        $query = $db->query($sql);

        if ($get_count=='get_count') {

          return $query->num_rows;         
        }else{

         return $query->rows; 
        }
        
    }
    /**
     * Method for get SOR invoice product
     * @param: $db: Database Object
     * @param: $seller_id: purchase id
     * @return records of sor invoice products
     * @author: kalyan, 9th Nov 2017
     */
    public function getSorInvoicesProductList( $db, $sor_purchase_id ){ 

         $sql = "SELECT 
                  wsbpb.purchase_id,
                  wsbpb.product_id,
                  wsbpb.sku,
                  wsbpb.pieces,
                  wsbpb.transfer_price_per_piece,
                  wsbpb.seller_tax,
                  p.image
                  FROM " . DB_PREFIX . "wsb_purchase_breakup wsbpb 
                  INNER JOIN " . DB_PREFIX . "product p 
                  ON (p.product_id=wsbpb.product_id)
                  WHERE wsbpb.purchase_id = '".(int)$sor_purchase_id ."'";
        
        
        /*
        * set order
        */
        $sql .= " ORDER BY wsbpb.product_id ASC";
        //echo $sql; die;
        $query = $db->query($sql);

        return $query->rows;         
    }
    /**
     * Method for get SOR particular product sold qty and amount
     * @param: $db: Database Object
     * @param:  proudct id array
     * @param: $seller_id: purchase id
     * @return records of particular product sold qty and amount
     * @author: kalyan, 9th Nov 2017
     * @author: kalyan, updated on 17th Feb 2018
     */
    public function getSorProductSoldQuantity( $db, $product_id_array, $sor_purchase_id='' ){ 
          
      if(empty($product_id_array)) {
       return false;
      }
      
      $product_array = array();

      $formatted_product_id_array = implode("','", $product_id_array);

      $sql = "SELECT 
                oop.seller_sku,
                oop.product_id,
                sum(oop.quantity*oop.piece_in_set) as total_sold_qty,
                sum(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total_sale_value,
                oop.transfer_price_per_piece
              FROM " . DB_PREFIX . "order_product oop
              INNER JOIN " . DB_PREFIX . "suborder so 
                ON oop.buyer_invoice_id = so.buyer_invoice_id  
              INNER JOIN " . DB_PREFIX . "order o
                ON so.order_id=o.order_id 
              WHERE oop.product_id IN('".$formatted_product_id_array."')
                AND o.stock_transfer='0'  
                AND oop.sor_product=1
                AND so.buyer_invoice_id>0 
                AND so.invoice_no > 0
                AND so.order_status_id > 0
                AND so.order_status_id != 2";

      
      if ($sor_purchase_id>0) {
        $sql .="  AND oop.wsb_purchase_id = '".(int)$sor_purchase_id ."'";
      }          
      

      $sql .= " GROUP BY oop.product_id";
     
      $query = $db->query($sql);

      if($query->num_rows >0){
            
            foreach ($query->rows as $key => $value) {
              $product_array[$value['product_id']] = $value;
            }
            return $product_array;

       }else{
            return false;
       }        
    }
    /**
     * Method for get SOR invoice product
     * @param: $db: Database Object
     * @param: $seller_id: purchase id
     * @return records of sor invoice products
     * @author: kalyan, 10th Nov 2017
     */
    public function getSorProductList($db, $seller_id, $filter_data, $get_count = ''){ 

         $sql = "SELECT 
                  wsbpb.purchase_id,
                  wsbpb.product_id,
                  wsbpb.sku,
                  sum(wsbpb.pieces) as total_pur_qty,
                  wsbpb.seller_tax,
                  p.image,
                  wsbpb.transfer_price_per_piece
                  FROM " . DB_PREFIX . "wsb_purchase_breakup wsbpb 
                  INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp 
                  ON (wsbp.purchase_id=wsbpb.purchase_id)
                  INNER JOIN " . DB_PREFIX . "product p 
                  ON (p.product_id=wsbpb.product_id)
                  WHERE wsbp.seller_id = '".(int)$seller_id ."' AND wsbp.sor_purchase=1 
                  GROUP BY wsbpb.sku 
                  HAVING total_pur_qty>0
                  ";
        
        
        /*
        * set filters
        */
        if ( !empty( $filter_data['filter_invoice_no'] ) ) {
            $sql .= " AND wsbp.invoice_no LIKE '%" .$db->escape($filter_data['filter_invoice_no']). "%'";
        }

        if (!empty( $filter_data['product_id'] ) ) {
            $sql .= " AND wsbpb.product_id = '" .$db->escape($filter_data['product_id']). "'";
        }
        /*
        * set order
        */
        $sql .= " ORDER BY p.product_id ";

        if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }
        /*
        * set limit
        */
        if( !empty($filter_data['limit']) && $get_count != 'get_count'){

          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {

            if ($filter_data['start'] < 0) {

              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

            if ($get_count!='get_count') {

              $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
            }
          }   
        }
        
        $query = $db->query($sql);

        if ($get_count=='get_count') {

          return $query->num_rows;         
        }else{

         return $query->rows; 
        }       
    }
    /**
     * Method for get total paid amount to seller against sor invoice
     * @param: $db: Database Object
     * @param: $seller_id: proudct id
     * @param: $purchase_id: purchase id
     * @return records of total amount
     * @author: kalyan, 15th Nov 2017
     */
    public function getPaidAmountAgainstSorInvoice($db, $seller_id, $sor_purchase_id_array ){ 
          
          if(empty($sor_purchase_id_array)) {
           return false;
          }

          $product_array = array();

          $formatted_sor_purchase_id_array = implode("','", $sor_purchase_id_array);

         $sql = "SELECT
                  sum(swbsp.trxn_amount) as total_paid_amount,
                  swbsp.wsb_purchase_id
                  FROM " . DB_PREFIX . "wsb_sor_payment swbsp         
                  WHERE swbsp.wsb_purchase_id IN('".$formatted_sor_purchase_id_array."') 
                  AND swbsp.seller_id='".(int)$seller_id."'
                  AND swbsp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE')
                  GROUP BY swbsp.wsb_purchase_id"; 
       
        $query = $db->query($sql);

        if($query->num_rows >0){
          
          foreach ($query->rows as $key => $value) {
            $product_array[$value['wsb_purchase_id']] = $value;
          }
          return $product_array;

        }else{
          return false;
        }         
    }
    /**
     * Method for get total payment amount againgst a single SKU
     * @param: $db: Database Object
     * @param: $seller_id: proudct id
     * @param: $seller_id: seller_id
     * @return total amount and suborder id
     * @author: kalyan, 15th Nov 2017
     */
    public function getTotalPaidAmountAgainstSingleSorSku($db, $product_id_array, $seller_id){ 
        
        if(empty($product_id_array)) {
         return false;
        }

        $product_array = array();

        $formatted_product_id_array = implode("','", $product_id_array);

        $sql = "SELECT
                oop.suborder_id,
                oop.product_id,
                oop.transfer_price_per_piece,
                sum(oop.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as total_paid_amount
                FROM " . DB_PREFIX . "order_product oop
                INNER JOIN " . DB_PREFIX . "wsb_sor_payment swbsp
                ON oop.suborder_id=swbsp.suborder_id 
                WHERE oop.sor_product=1 
                AND oop.sor_payment_id=swbsp.sor_payment_id 
                AND oop.product_id IN('".$formatted_product_id_array."')
                AND swbsp.seller_id = '".(int)$seller_id ."'
                AND swbsp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE')
                GROUP BY product_id 
                ";

        $query = $db->query($sql);

        if($query->num_rows >0){
          
          foreach ($query->rows as $key => $value) {
            $product_array[$value['product_id']] = $value;
          }
          return $product_array;

        }else{
          return false;
        }                 
    }
    /**
     * Method for get total paid amount to seller against sor sku
     * @param: $db: Database Object
     * @param: $seller_id: proudct id
     * @param: $purchase_id: purchase id
     * @return records of total amount
     * @author: kalyan, 15th Nov 2017
     */
    public function getSorOrderPayments( $db, $order_id, $seller_id ){ 

         $sql = "SELECT
                  oop.suborder_id,
                  oop.suborder_id,
                  swbsp.trxn_utr,
                  swbsp.trxn_utr_date,
                  swbsp.trxn_done,
                  swbsp.sor_payment_id,
                  sum(swbsp.trxn_amount) as total_paid_amount
                  FROM " . DB_PREFIX . "order_product oop
                  INNER JOIN " . DB_PREFIX . "wsb_sor_payment swbsp
                  ON oop.suborder_id=swbsp.suborder_id 
                  WHERE oop.sor_product=1 
                  AND oop.sor_payment_id=swbsp.sor_payment_id 
                  AND oop.order_id='".(int)$order_id."'
                  AND swbsp.seller_id = '".(int)$seller_id ."'
                  AND swbsp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE') 
                  GROUP BY oop.suborder_id"; 
        
        $query = $db->query($sql);

        return $query->rows;         
    }
    /**
     * Method for get all payment entery of single sor sku against a seller
     * @param: $db: Database Object
     * @param: $seller_id: proudct id
     * @param: $seller_id: seller_id
     * @return array of all payments against single sor sku
     * @author: kalyan, 17th Nov 2017
     */
    public function getPaidAmountDetailAgainstSingleSorSku($db, $filter_data, $get_count=''){ 

         $sql = "SELECT
                  oop.seller_sku,
                  oop.suborder_id,
                  oop.quantity,
                  oop.piece_in_set,
                  oop.transfer_price_per_piece,
                  oop.order_product_id,
                  swbsp.trxn_utr,
                  swbsp.trxn_utr_date,
                  swbsp.trxn_done
                  FROM " . DB_PREFIX . "order_product oop
                  INNER JOIN " . DB_PREFIX . "wsb_sor_payment swbsp
                  ON oop.suborder_id=swbsp.suborder_id 
                  WHERE oop.sor_product=1 
                  AND oop.sor_payment_id=swbsp.sor_payment_id
                  AND swbsp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE') 
                  AND oop.product_id='" .(int)$filter_data['product_id']. "' 
                  AND swbsp.seller_id='" .(int)$filter_data['seller_id']. "' 
                  AND oop.wsb_purchase_id='" .(int)$filter_data['purchase_id']. "'"; 
        
        
        /*
        * set order
        */
        $sql .= " ORDER BY suborder_id ";

        if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }
        /*
        * set limit
        */
        if( !empty($filter_data['limit']) && $get_count != 'get_count'){

          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {

            if ($filter_data['start'] < 0) {

              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

            if ($get_count!='get_count') {

              $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
            }
          }   
        }
        
        $query = $db->query($sql);

        if ($get_count=='get_count') {

          return $query->num_rows;         
        }else{

         return $query->rows; 
        }         
    }
    /**
     * Method for get order Products
     * @param: $db: Database Object
     * @param: $order_id: Interger of order id
     * @param: $suborder_id: string of order id
     * @param: $seller_id: Integer of seller id
     * @param: $edit_type: array of edit type
     * @param: $get_seller_invoice_ids: invoice id with commas in string format
     * @param: $getOnlySor: string of get only sor
     * @return records of pickup done array
     * @author: kalyan 24th Nov, 2017
     */

    public function getSorSubOrderProducts($obj, $order_id, $seller_id){
      
      $obj->load->model('tool/image');

        $sql = "SELECT  oop.order_product_id,
                        oop.product_id,
                        oop.name,
                        oop.seller_sku,
                        oop.quantity,
                        oop.piece_in_set,
                        oop.transfer_price_per_piece,
                        oop.seller_input_tax,
                        oop.seller_cst,
                        oop.comment,
                        oop.suborder_id,
                        oop.seller_id,
                        oop.sor_product,
                        oop.seller_invoice_id,
                        wsbp.invoice_no as sor_invoice_no,
                        wsbp.date_added as sor_invoice_date
                FROM oc_order o 
                INNER JOIN oc_order_product oop 
                  ON ( oop.order_id = o.order_id ) 
                INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp 
                  ON ( oop.wsb_purchase_id = wsbp.purchase_id AND oop.sor_product = 1) 
                WHERE o.order_id = '" . (int)$order_id  . "' 
                AND wsbp.seller_id = '" . (int)$seller_id  . "' AND oop.sor_product='1' 
                AND oop.pickup_status = 'Received' 
                AND o.stock_transfer = 0 ";
           
       
        $query = $obj->db->query($sql);

        $product_record_array = array();

        array_walk($query->rows, function(&$products , $key) use (&$product_record_array){
          $product_record_array[$products['suborder_id']][$products['order_product_id']] = $products;
        });

        
        foreach ( $product_record_array as $suborder_key => $subroders_data) {
          foreach ( $subroders_data as $key => $values) {
            // get image
            $query_image = $obj->db->query("SELECT image FROM " . DB_PREFIX ."product WHERE product_id = " . (int)$values['product_id'] ."");
            if( $query_image->num_rows ){
              $product_record_array[$suborder_key][$key]['image'] = $obj->model_tool_image->resize($query_image->row['image'],
                                                                        $obj->config->get('config_image_additional_width'),
                                                                        $obj->config->get('config_image_additional_height')
                                                                        );
            } else {
              $product_record_array[$suborder_key][$key]['image'] = '';
            }

            $product_record_array[$suborder_key][$key]['href'] = $obj->url->link('product/product', 'product_id=' . $values['product_id']);
            

            $seller_invoice_obj = SellerInvoice::getOrderProductWiseCalculation( $values );
            $product_record_array[$suborder_key][$key]['no_of_piece']    = $seller_invoice_obj['no_of_piece'];
            $product_record_array[$suborder_key][$key]['price_per_piece']= $seller_invoice_obj['price_per_piece'];
            $product_record_array[$suborder_key][$key]['product_amount'] = $seller_invoice_obj['product_amount'];
            $product_record_array[$suborder_key][$key]['vat_cst_rate']   = $seller_invoice_obj['vat_cst_rate'];
            $product_record_array[$suborder_key][$key]['vat_cst_amount'] = $seller_invoice_obj['vat_cst_amount'];
            $product_record_array[$suborder_key][$key]['total_amount']   = $seller_invoice_obj['total_amount'];
          }
        }
        return $product_record_array;
    }

   /**
   * Method for seller update invoiced products
   * @param $db           : Object of DB
   * @param $order_id     : Integer of order id,
   * @param $suborder_id  : String of suborder id
   * @param $details      : Array of details with edit type and new edit history
   * @param $seller_id    : Integer of seller id
   * @return Update Invoiced products by seller
   * @author Vikas, 2017
   */
    public function sellerEditedAfterGeneratingInvoice($db, $order_id, $suborder_id, $order_product_id_key, $details, $seller_id){ 
      
      // get edit_history from db of perticular order_product_id

      $sql = "SELECT *
              FROM " . DB_PREFIX . "order_product
              WHERE order_product_id = '" . (int) $order_product_id_key . "'";
      $product_data = $db->query($sql);
      
      $old_edit_history = array();
      
      if($product_data->num_rows){
        $old_edit_history = unserialize($product_data->row['edit_history']);
      }

      if(key($details['edit_type'])=='SELLER_APPROVED'){
        
        $old_edit_history[] = $details['edit_history'];

        $sql = "UPDATE " . DB_PREFIX . "order_product
              SET pickup_status = '" . $db->escape('Not_Given') . "',
                  edit_history = '" . $db->escape(serialize($old_edit_history)) . "',
                  last_modified = NOW()
              WHERE order_product_id = '" . (int)$order_product_id_key . "'"; 
        $db->query($sql);                

      } else if(key($details['edit_type'])=='SELLER_NOT_SUPPLIED'){

        $old_edit_history[] = $details['edit_history'];
        
        $sql = "UPDATE " . DB_PREFIX . "order_product
              SET pickup_status = '" . $db->escape('Not_Given') . "',
                  seller_invoice_id = 0,
                  edit_type = '" . $db->escape(key($details['edit_type'])) . "',
                  edit_history = '" . $db->escape(serialize($old_edit_history)) . "',
                  last_modified = NOW()
              WHERE order_product_id = '" . (int)$order_product_id_key . "'"; 
        $db->query($sql);       

      } else if(key($details['edit_type'])=='SELLER_LATER_DISPATCH'){

        $old_edit_history[] = $details['edit_history'];

        $sql = "UPDATE " . DB_PREFIX . "order_product
              SET pickup_status = '" . $db->escape('Not_Given') . "',
                  seller_invoice_id = 0,
                  edit_type = '" . $db->escape(key($details['edit_type'])) . "',
                  edit_history = '" . $db->escape(serialize($old_edit_history)) . "',
                  last_modified = NOW()
              WHERE order_product_id = '" . (int)$order_product_id_key . "'";
        $db->query($sql);       

      } else if(key($details['edit_type'])=='SELLER_PARTIAL'){
        
        if($details['edit_type']['SELLER_PARTIAL']['value']){
            
            // -- new
            $details['edit_history']['comment'] = 'SELLER_NOT_SUPPLIED';
                        $edit_details = array(
                            'edit_type' 	=> 'SELLER_NOT_SUPPLIED',
                            'edit_history' 	=> $details['edit_history']
                        );

                        $edit_details['quantity'] = $details['edit_type']['SELLER_PARTIAL']['value'];
                        //Code changes by Nilesh as per new requirement - for generic function splitOrderProduct will split accordingly
                        $edit_details['old_edit_type'] = 'SELLER_PARTIAL';

                        OrderEdit::splitOrderProduct( $db, $order_product_id_key, $edit_details, $details['edit_type']['SELLER_PARTIAL']['seller_invoice_id']);
            
        } else {

          $sql_qty = "SELECT SUM(quantity*piece_in_set) as total_qty
                  FROM " . DB_PREFIX . "order_product
                  WHERE suborder_id = '" . $db->escape($suborder_id) . "'
                    AND product_id = '" . (int)$details['edit_type']['SELLER_PARTIAL']['product_id'] . "'"; 
          $query_qty = $db->query($sql_qty)->row['total_qty'];

          $sql_delete = "DELETE  FROM " . DB_PREFIX . "order_product 
                         WHERE seller_invoice_id = 0
                          AND suborder_id = '" . $db->escape($suborder_id) . "'
                          AND product_id = '" . (int)$details['edit_type']['SELLER_PARTIAL']['product_id'] . "'
                          AND edit_type = '" .$db->escape('SELLER_NOT_SUPPLIED') . "'";
          $db->query($sql_delete);   

          $details['edit_history']['comment'] = 'SELLER_NOT_SUPPLIED';
          $old_edit_history[] = $details['edit_history'];

          $sql = "UPDATE " . DB_PREFIX . "order_product 
                  SET pickup_status = '" . $db->escape('Not_Given') . "',
                      quantity = '" . (int)$query_qty . "',
                      piece_in_set = 1,
                      seller_invoice_id = 0,
                      edit_type = 'SELLER_NOT_SUPPLIED',
                      edit_history = '" . $db->escape(serialize($old_edit_history)) . "'
                  WHERE seller_invoice_id = '" . (int)$details['edit_type']['SELLER_PARTIAL']['seller_invoice_id'] . "'
                        AND suborder_id = '" . $db->escape($suborder_id) . "'
                        AND product_id = '" . (int)$details['edit_type']['SELLER_PARTIAL']['product_id'] . "'";

          $db->query($sql);             
        }     
      }
    }

  /**
   * Method for seller update invoice no. and date after generated invoice
   * @param $db               : Object of DB
   * @param $updateInvoiceData: array of data with invoice no. and date and seller_invoice_id
   * @return Update Invoiced no and date by seller
   * @author Vikas, 2017
   */

  public function sellerUpdateInvoiceNoAndDateAfterGeneratingInvoice($db,$updateInvoiceData = array()) {

    $sql = "SELECT seller_invoice_id, 
                   CONCAT(seller_invoice_prefix, seller_invoice_no) as invoice_no 
            FROM " . DB_PREFIX . "seller_invoice 
            WHERE seller_id = '" . (int)$updateInvoiceData['seller_id']. "' 
              AND CONCAT(seller_invoice_prefix, seller_invoice_no) = '" . $db->escape($updateInvoiceData['invoice_no']) . "' 
              AND trxn_done != 'INVALID_NO_GOODS' 
            LIMIT 1";

    $query = $db->query($sql);

    if($query->num_rows){
      if($query->row['seller_invoice_id']!=$updateInvoiceData['sllr_inv_id']){
          // means invoice no is already exist for some other invoice, so return false
        return false;
      }
    }

    // update the invoice no and invoice date
      $sql = "UPDATE " .  DB_PREFIX  . "seller_invoice
            SET seller_invoice_no = '" .$db->escape($updateInvoiceData['invoice_no']) . "',
                date_added = '" . $db->escape(date('Y-m-d', strtotime(str_replace('/', '-', $updateInvoiceData['invoice_date'])))) ."'
            WHERE seller_invoice_id = '" . (int)$updateInvoiceData['sllr_inv_id'] . "'";
      $db->query($sql);

      return true;

  }

  /**
   * Method for seller update invoie trxn_done
   * @param $db               : Object of DB
   * @param $updateInvoiceData: array of data with invoice no. and date and seller_invoice_id
   * @return Update Invoiced no and date by seller
   * @author Mahaveer, 201
   */
  public function updateSellerInvoiceForNoGoods($db,$updateInvoiceData = array()) {

    // update trxn_done

      $order_product_sql = "SELECT seller_invoice_id  FROM " . DB_PREFIX . "order_product 
            WHERE seller_id = '" . (int)$updateInvoiceData['seller_id']. "'
             AND  seller_invoice_id = '" . (int)$updateInvoiceData['sllr_inv_id']. "' 
            LIMIT 1";

      $order_product_query = $db->query($order_product_sql); 
   
      if($order_product_query->num_rows==0)
      {
         $sql = "UPDATE " .  DB_PREFIX  . "seller_invoice
            SET trxn_done = 'INVALID_NO_GOODS'
            WHERE seller_invoice_id = '" . (int)$updateInvoiceData['sllr_inv_id'] . "'";
          $db->query($sql);
      }    
      return true;

  }
  /**
  * Method for checking if there is atleast one valid order product id
  * Primary usage for Validing Client Side input for generating seller invoice
  * @param $db : object of DB
  * @param $order_product_id : array of order_product_id
  * @return true or false
  * @author : vikas, Dec 2017
  */
  public function checkOrderProductIdsIsAvailableOrNot($db, $order_product_ids = array()){
    $sql = "SELECT order_product_id
            FROM " . DB_PREFIX . "order_product
            WHERE order_product_id IN ( " . implode(',', $order_product_ids) . ") 
            LIMIT 1";
    $query = $db->query($sql);
    if($query->num_rows){
      return true;
    } else {
      return false;
    }
  }

    /**
     * Method to get suborder processing date
     * @param $db : object of DB
     * @param $suborder_id : suborder id
     * @return processing date
     * @author : Devendra, 17/01/2018
     */
  public function getSubOrderProcessingDate($db, $suborder_id){
      $sql = "SELECT suborder_id, 
                      DATE(MIN(date_added)) as order_processing_date 
                      FROM " . DB_PREFIX . "order_history 
                      WHERE suborder_id = '" . $db->escape($suborder_id) . "' 
                      AND order_status_id IN (9,16) ";
      $query = $db->query($sql);
      if( $query->num_rows )
          return $query->row['order_processing_date'];
      else
          return '';
  }
  /**
     * Method for get SOR product sales return
     * @param  : $db: Database Object
     * @param  : proudct id
     * @param  : $seller_id: purchase id
     * @return : records of all product return quantity
     * @author : kalyan, 23th Feb 2018
     */
    public function getSorProductsSalesReturns($db, $product_id_array, $single_sku='', $sor_purchase_id=0){ 
        
        $product_data           = array();
        $formatted_product_ids  = implode("','", $product_id_array);
        
        $sql = "SELECT
                  sum(r.quantity) as quantity,
                  oop.product_id,
                  sum(r.quantity*oop.piece_in_set*oop.transfer_price_per_piece) as return_value,
                  oop.order_product_id
                FROM " . DB_PREFIX . "credit_note cn
                INNER JOIN " . DB_PREFIX . "return r 
                  ON r.credit_note_id = cn.credit_note_id
                INNER JOIN " . DB_PREFIX . "order_product oop
                  ON r.order_product_id = oop.order_product_id 
                INNER JOIN " . DB_PREFIX . "suborder so
                  ON oop.buyer_invoice_id = so.buyer_invoice_id  
                INNER JOIN " . DB_PREFIX . "order oo
                  ON oo.order_id = so.order_id   
                WHERE oop.product_id IN ('".$formatted_product_ids."') 
                  AND so.buyer_invoice_id > 0 
                  AND so.invoice_no > 0 
                  AND so.order_status_id > 0 
                  AND so.order_status_id != 2
                  AND oo.stock_transfer = 0 
                  AND oo.franchise_id = 0 
                  AND cn.credit_note_status = 1 
                ";

        if ($sor_purchase_id > 0) {
          $sql .="  AND oop.wsb_purchase_id = '".(int)$sor_purchase_id ."' ";
         }

        $sql .= " GROUP BY oop.product_id ";

        $query = $db->query($sql);

        if ($query->num_rows >0) {
          
          foreach ($query->rows as $key => $value) {

            if ($single_sku=='single_sku') {
              $product_data[] = $value['order_product_id'];
            }else{

                if(isset($product_data[$value['product_id']]))
                {
                   $product_data[$value['product_id']] += $value['quantity'];  
                }
                else
                {
                   $product_data[$value['product_id']] = $value['quantity'];  
                }
             

            }            
          }
        }
        return $product_data;
    }
    /**
     * Method for get SOR product Purchase return
     * @param  : $db: Database Object
     * @param  : proudct id
     * @param  : $seller_id: purchase id
     * @return : records of all product return quantity
     * @author : kalyan, 23th Feb 2018
     */
    public function getSorProductsPurchaseReturns($db, $product_id_array, $single_sku='', $sor_purchase_id=0){ 
        
        $product_data           = array();
        $formatted_product_ids  = implode("','", $product_id_array);
        
        $sql = "SELECT DISTINCT
                  owprb.quantity,
                  owprb.product_id,
                  sum(owprb.quantity*owprb.transfer_price_per_piece) as total_purchase_return_amount,
                  sum(owprb.quantity) as total_purchase_return_pieces,
                  owprb.transfer_price_per_piece
                FROM " . DB_PREFIX . "wsb_purchase_return_breakup owprb
                INNER JOIN " . DB_PREFIX . "wsb_purchase_return owpr 
                  ON owprb.debit_note_id = owpr.debit_note_id
                WHERE owprb.product_id IN ('".$formatted_product_ids."') 
                  AND owprb.purchase_firm_id = owpr.purchase_firm_id 
                  AND owprb.purchase_id = owpr.purchase_id 
                  AND owpr.debit_note_status = 1
                  AND owpr.debit_note_no > 0";

        if ($sor_purchase_id > 0) {
          $sql .="  AND owpr.purchase_id = '".(int)$sor_purchase_id ."'";
         } 

         $sql .="  GROUP BY owprb.product_id";       

        $query = $db->query($sql);

        if ($query->num_rows >0) {
          
          foreach ($query->rows as $key => $value) {

            if ($single_sku=='single_sku') {
              $product_data[] = $value['product_id'];
            }else{
              $product_data[$value['product_id']]['quantity'] = $value['total_purchase_return_pieces'];  
              $product_data[$value['product_id']]['transfer_price_per_piece'] = $value['transfer_price_per_piece'];  
            }            
          }
        }
        return $product_data;
    }

  public function SendNotificationToSalesStaff($obj, $order_id)
  {     
        $html_body                  = "";
        $send_wait                  = 'instant';
        $objDateTime                = new DateTime();
        $notification_sending_time  = $objDateTime->format('Y-m-d H:i:s');
        $user_data    = array();
        $mail_send    =0;
        $order_no     =0;

        if(SITE_ENVIRONMENT == 'Test')
        {
          $path_name = 'staging/'; 
        }
        else
        {
          $path_name = ''; 
        }

    $order_data = $this->getOrderMailData($obj, $order_id, array('SELLER_NOT_SUPPLIED', 'SELLER_LATER_DISPATCH', 'SELLER_PARTIAL'));

    
    $crm_user_id = $this->getCrmUserId($obj, $order_id);

    
   
    $html_body = $html_body.'<table align="center" border="1" cellpadding="5" cellspacing="0" width="100%">
                      <tbody>
                        <tr><th>Product</th>
                            <th>Set Description</th>
                            <th>No. of Pieces</th>
                            <th>Price / Piece</th>
                            <th>Product Amount</th>
                            <th>Tax Rate</th>
                            <th>Tax Amount</th>
                            <th>Total Amount</th>
                            <th>Type</th>
                            <th>Modified Date</th>
                        </tr>';
       

        foreach($order_data as $products)
        {
         foreach($products as $product)
         { 
           $order_no  =  $product['order_no'];
           $date_added = date("d-m-Y");
           $edit_history = unserialize($product['edit_history']);
           array_reverse($edit_history);

           foreach($edit_history as $history)
           {
             if($history['comment'] == $product['edit_type'])
             {
              $date_added = $history['date_added'];
             }
           }

          $mail_send =1; 
          $html_body = $html_body.'<tr><th>'.$product['name'].' 
                                       <br /> '.$product['seller_sku'].'</th>
                                      <th>'.$product['comment'].'</th>
                                      <th>'.$product['no_of_piece'].'</th>
                                      <th>'.$product['price_per_piece'].'</th>
                                      <th>'.$product['product_amount'].'</th>
                                      <th>'.$product['vat_cst_rate'].'</th>
                                      <th>'.$product['vat_cst_amount'].'</th>
                                      <th>'.$product['total_amount'].'</th>
                                      <th>'.str_replace("_", " ", $product['edit_type']).'</th>
                                      <th>'.$date_added.'</th>
                                      </tr>';
          }                             
        }

       $html_body = $html_body.'</tbody></table>';
       $html_body = "Order No: ".$order_no." <br />".$html_body;
       $subject   = 'Seller updated Order status of Order no '.$order_no.' - '.date("d-m-Y h:i:s");

        $i = 0;
 
        $user_data[$i]['type']              = 'crm_user'; 
        $user_data[$i]['id']                = $crm_user_id; 
        $user_data[$i]['is_pn_to_send']     = true;              
        $user_data[$i]['is_sms_to_send']    = false; 
        $user_data[$i]['is_email_to_send']  = true;
        $user_data[$i]['email']['cc']       = array();
        $user_data[$i]['email']['to']       = array();
        $user_data[$i]['email']['subject']  = $subject;
        $user_data[$i]['email']['body']     = serialize($html_body);
        $user_data[$i]['email']['is_html']  = true;
        $user_data[$i]['email_to_head']     = true;
        $user_data[$i]['pn_to_head']        = true;
        $user_data[$i]['is_web_pn_to_send'] = true;
        $user_data[$i]['web_pn_to_head']    = true;
        $user_data[$i]['sms_to_head']       = false;

        $user_data[$i]['email_to_sales_support']     = true;
        $user_data[$i]['pn_to_sales_support']        = true;
        $user_data[$i]['web_pn_to_sales_support']    = true;
        $user_data[$i]['sms_to_sales_support']       = false;
        $user_data[$i]['web_pn']                     = array('title'=>$subject,
                                                             'body'=>$html_body,
                                                             'icon'=>'https://d36qiqd7gl7e25.cloudfront.net/img/catalog/rsz_wsb_tmp_logo_286.png',
                                                             'action'=>'');

        
        $api_data['notification_sending_time']  = $notification_sending_time;
        $api_data['type']                       = $send_wait;
        $api_data['data']                       = $user_data;
        /*
         * api code
        */ 
        if($mail_send == 1)
        {       
          $json_data = json_encode($api_data);

          $url       = 'https://www.wholesalebox.biz/'.$path_name.'crmapi/Notifications/sendNotificationFromWeb';
          $curl      = curl_init();
         // Set SSL if required
          if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
          }
             
          curl_setopt($curl, CURLOPT_HEADER, false);
          curl_setopt($curl, CURLINFO_HEADER_OUT, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS,$json_data);             
          $json = curl_exec($curl);
          curl_close($curl);
        }  
  }    




    public function getOrderMailData( $obj, $order_id, $edit_type = array()){
      
        $sql = "SELECT 
                        o.order_no,
                        oop.order_product_id,
                        oop.product_id,
                        oop.name,
                        oop.seller_sku,
                        oop.quantity,
                        oop.piece_in_set,
                        oop.transfer_price_per_piece,
                        oop.seller_input_tax,
                        oop.seller_cst,
                        oop.comment,
                        oop.edit_history,
                        oop.suborder_id,
                        oop.seller_id,
                        oop.sor_product,
                        oop.seller_invoice_id,
                        oop.pickup_status, 
                        oop.edit_type, ";

        $sql .= "osi.seller_invoice_no,
                        osi.date_added
                FROM oc_order o
                INNER JOIN oc_order_product oop
                  ON ( oop.order_id = o.order_id )";
                  
        $sql .= "LEFT JOIN oc_seller_invoice osi
                  ON (oop.seller_invoice_id=osi.seller_invoice_id)  
                WHERE o.order_id = '" . (int)$order_id  . "'
                  AND o.stock_transfer = 0 ";

        $sql .= "AND oop.sor_product='0'";
       

        if( !empty($edit_type) ){
          $sql .= " AND oop.edit_type IN ('" . implode("','", $edit_type). "')";
        }

        $query = $obj->db->query($sql);

        $product_record_array = array();

        array_walk($query->rows, function(&$products , $key) use (&$product_record_array){
          $product_record_array[$products['suborder_id']][$products['order_product_id']] = $products;
        });

        
        foreach ( $product_record_array as $suborder_key => $subroders_data) {
          foreach ( $subroders_data as $key => $values) {
        
            $product_record_array[$suborder_key][$key]['href'] = $obj->url->link('product/product', 'product_id=' . $values['product_id']);
            

            $seller_invoice_obj = SellerInvoice::getOrderProductWiseCalculation( $values );
            $product_record_array[$suborder_key][$key]['no_of_piece']    = $seller_invoice_obj['no_of_piece'];
            $product_record_array[$suborder_key][$key]['price_per_piece']= $seller_invoice_obj['price_per_piece'];
            $product_record_array[$suborder_key][$key]['product_amount'] = $seller_invoice_obj['product_amount'];
            $product_record_array[$suborder_key][$key]['vat_cst_rate']   = $seller_invoice_obj['vat_cst_rate'];
            $product_record_array[$suborder_key][$key]['vat_cst_amount'] = $seller_invoice_obj['vat_cst_amount'];
            $product_record_array[$suborder_key][$key]['total_amount']   = $seller_invoice_obj['total_amount'];
            $product_record_array[$suborder_key][$key]['amount_per_piece']   = $seller_invoice_obj['total_amount']/($seller_invoice_obj['no_of_piece']?$seller_invoice_obj['no_of_piece']:1);
          }
        }
        return $product_record_array;
    }

  public function getCrmUserId( $obj, $order_id)
  {
    $sql = "SELECT  oss.crm_user_id from oc_order_sales_staff ooss INNER JOIN oc_sales_staff oss ON oss.staff_id = ooss.sales_staff_id where ooss.order_id='".(int)$order_id."'";

    $query = $obj->db->query($sql);

   if(isset($query->row['crm_user_id']))
   {
     return $query->row['crm_user_id'];
   }
   else
   {
    return 0;
   }
    

  } 

  /**
  * Method for get order_id, suborder_id, and seller_id are same in a single value by array of order product id 
  * @param : $obj : object of db
  * @param : $order_product_id : array of order product id
  * @return: true or false,
  * @author: vikas Apr, 2018
  */
  public function getSameOrderIdandSubOrderIdandSellerId( $db, $order_product_ids){
    $sql = "SELECT  order_id,
                    suborder_id,
                    seller_id
            FROM oc_order_product
            WHERE order_product_id IN ( " . implode(',', $order_product_ids ). ")
            GROUP BY order_id, suborder_id, seller_id";
    $query = $db->query($sql);

    $result = array();
    if($query->num_rows == 1){
      
      $result = array(
                      'order_id'    => $query->row['order_id'],
                      'suborder_id' => $query->row['suborder_id'],
                      'seller_id'   => $query->row['seller_id']
                    );
    }

    return $result;

  }

  /**
  * Method for get SOR invoice products data
  * @param : Database\DB $db
  * @param : int         $purchase_id
  * @return: array       $result 
  * @author: MSA May 2019
  */
  public function getSorInvoiceDetails(Database\DB $db, int $purchase_id)
  {

    $sql = "
        SELECT
            oms.company as seller_name, 
            owp.invoice_no, 
            owp.invoice_date, 
            owpb.product_id, 
            owpb.sku,
            SUM(owpb.pieces) AS total_pieces_purchased, 
            SUM(owpb.pieces*owpb.transfer_price_per_piece) AS total_amount_purchased, 
            COALESCE(dt2.total_pieces_returned_to_seller,0) AS pieces_returned_to_seller, 
            COALESCE(dt2.total_return_amount,0) AS amount_returned_to_seller, 
            COALESCE(dt3.pieces_sold,0) - COALESCE(dt4.pieces_returned,0) AS net_pieces_sold_to_customer, 
            COALESCE(dt3.amount_sold,0) - COALESCE(dt4.amount_returned,0) AS net_amount_sold_to_customer,
            owp.purchase_id,
            oms.seller_id,
            owpb.transfer_price_per_piece,
            p.image 
          FROM 
            " . DB_PREFIX . "wsb_purchase owp 
          INNER JOIN " . DB_PREFIX . "ms_seller oms on oms.seller_id = owp.seller_id 
          INNER JOIN " . DB_PREFIX . "wsb_purchase_breakup owpb on owpb.purchase_id = owp.purchase_id 
          INNER JOIN " . DB_PREFIX . "product p ON p.product_id = owpb.product_id
          LEFT JOIN (SELECT owpr2.purchase_id, 
                            owprb2.product_id, 
                            sum(owprb2.quantity) as total_pieces_returned_to_seller, 
                            sum(owprb2.quantity * owprb2.transfer_price_per_piece) as total_return_amount 
                     FROM " . DB_PREFIX . "wsb_purchase_return owpr2 
                     INNER JOIN " . DB_PREFIX . "wsb_purchase_return_breakup owprb2 on owprb2.debit_note_id = owpr2.debit_note_id 
                     INNER JOIN " . DB_PREFIX . "wsb_purchase owp2 on owp2.purchase_id = owpr2.purchase_id and owp2.sor_purchase = 1 
                     WHERE owpr2.debit_note_status = 1
                     GROUP BY owpr2.purchase_id, owprb2.product_id) as dt2 on dt2.purchase_id = owp.purchase_id and 
                                                                              dt2.product_id = owpb.product_id 
          LEFT JOIN (SELECT 
                       oop3.wsb_purchase_id, 
                       sum(oop3.quantity*oop3.piece_in_set) as pieces_sold, 
                       sum(oop3.quantity*oop3.piece_in_set*oop3.transfer_price_per_piece) as amount_sold  
                     FROM " . DB_PREFIX . "order o3 
                     INNER JOIN " . DB_PREFIX . "suborder osub3 on osub3.order_id = o3.order_id and 
                                               osub3.order_status_id > 0 and 
                                               osub3.order_status_id <> 2 and 
                                               osub3.invoice_no > 0 
                     INNER JOIN " . DB_PREFIX . "order_product oop3 on oop3.buyer_invoice_id = osub3.buyer_invoice_id and 
                                                   oop3.sor_product = 1 
                     WHERE o3.store_id in (0,2,9) and 
                           o3.stock_transfer = 0 and 
                           o3.franchise_id = 0 
                     GROUP BY oop3.wsb_purchase_id) AS dt3 on dt3.wsb_purchase_id = owp.purchase_id 
          LEFT JOIN (SELECT 
                       oop4.wsb_purchase_id, 
                       sum(ort4.quantity) as pieces_returned, 
                       sum(ort4.quantity*oop4.transfer_price_per_piece) AS amount_returned  
                     FROM " . DB_PREFIX . "credit_note ocn4 
                     INNER JOIN " . DB_PREFIX . "return ort4 on ort4.credit_note_id = ocn4.credit_note_id 
                     INNER JOIN " . DB_PREFIX . "order_product oop4 on oop4.order_product_id = ort4.order_product_id and oop4.sor_product = 1 
                     INNER JOIN " . DB_PREFIX . "suborder osub4 on osub4.buyer_invoice_id = oop4.buyer_invoice_id and osub4.invoice_no > 0 and 
                                               osub4.order_status_id > 0 and osub4.order_status_id <> 2 
                     INNER JOIN " . DB_PREFIX . "order o4 on o4.order_id = osub4.order_id 
                                                            and o4.store_id in (0,2,9) 
                                                            and o4.stock_transfer = 0 
                                                            and o4.franchise_id = 0 
                     WHERE 
                       ocn4.credit_note_status = 1 
                     group by oop4.wsb_purchase_id) AS dt4 on dt4.wsb_purchase_id = owp.purchase_id 
          WHERE owp.sor_purchase = 1
                and 
                owp.purchase_id = ".(int)$purchase_id."  
          GROUP BY owp.purchase_id, owpb.product_id 
      ";

      $results = $db->query($sql);
      if($results->num_rows){
        return $results->rows;
      }
      return array();
  }

  /**
  * Method for get all SOR invoices data
  * @param : Database\DB $db
  * @param : int         $seller_id
  * @param : array       $filter_data
  * @param : bool        $is_count
  * @return: array       $result 
  * @author: MSA May 2019
  */
  public function getAllSorInvoices(Database\DB $db, int $seller_id, array $filter_data = array(), bool $is_count = false )
  {

    $sql = "SELECT 
                  oms.company as seller_name,
                  owp.purchase_id,
                  owp.seller_id,
                  owp.invoice_no, 
                  owp.invoice_date, 
                  dt1.total_pieces, 
                  dt1.invoice_amount 
            FROM 
                " . DB_PREFIX . "wsb_purchase owp        
            
            INNER JOIN 
                " . DB_PREFIX . "ms_seller oms 
                    ON oms.seller_id = owp.seller_id 
            
            INNER JOIN 
                (SELECT 
                    owp1.purchase_id, 
                    SUM(owpb1.pieces) as total_pieces, 
                    SUM(owpb1.pieces * owpb1.transfer_price_per_piece) as invoice_amount  
                FROM 
                    " . DB_PREFIX . "wsb_purchase owp1 
                INNER JOIN 
                    " . DB_PREFIX . "wsb_purchase_breakup owpb1 
                      ON owpb1.purchase_id = owp1.purchase_id 
                WHERE
                    owp1.sor_purchase = 1 
                GROUP BY 
                    owp1.purchase_id

                ) as dt1 ON dt1.purchase_id = owp.purchase_id
            WHERE
                oms.seller_id = " . (int) $seller_id . "  
             ";

        $sql .= " ORDER BY owp.purchase_id ";

        $sql .= $filter_data['order'] ?? "ASC ";

        /* set limit */
        if( !empty($filter_data['limit']) && !$is_count){

          if ( isset($filter_data['start']) || isset($filter_data['limit']) ) {

            if ($filter_data['start'] < 0) {

              $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
            }

              $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
          }   
        }
        
        //echo $sql;die;

        $query = $db->query($sql);

        if ( $is_count ) {

          return $query->num_rows;

        }else{

         return $query->rows; 
        }

  }

   /**
  * Method for get all SOR invoices pieces return to seller
  * @param : Database\DB $db
  * @param : int         $purchase_id
  * @return: array       $result 
  * @author: MSA May 2019
  */
  public function getTotalPiecesReturnToSeller(Database\DB $db, array $purchase_ids)
  {
    //dt2
    $sql = "
          SELECT 
            owpr2.purchase_id, 
            SUM(owprb2.quantity) as total_pieces_returned_to_seller, 
            SUM(owprb2.quantity * owprb2.transfer_price_per_piece) as total_return_amount 
          FROM
            " . DB_PREFIX . "wsb_purchase_return owpr2 
          INNER JOIN 
            " . DB_PREFIX . "wsb_purchase_return_breakup owprb2 
              ON owprb2.debit_note_id = owpr2.debit_note_id 
          INNER JOIN 
            " . DB_PREFIX . "wsb_purchase owp2 
            ON owp2.purchase_id = owpr2.purchase_id 
              AND owp2.sor_purchase = 1 
          WHERE
            owpr2.purchase_id IN (".implode(',', $purchase_ids).")
            AND
            owpr2.debit_note_status = 1
          GROUP BY
           owpr2.purchase_id
        ";
    $results = $db->query($sql);
    if($results->num_rows){
      $purchase_ids = array_column($results->rows, 'purchase_id');
      return array_combine($purchase_ids, $results->rows);
    }
    return array();
  }  

  /**
  * Method for get total sold pieces for purchase ids
  * @param : Database\DB $db
  * @param : array       $purchase_ids
  * @return: array       $result data
  * @author: MSA May 2019
  */
  public function getTotalPiecesSoldToCustomers(Database\DB $db, array $purchase_ids)
  {
      $sql = "
          SELECT 
             oop3.wsb_purchase_id, 
             sum(oop3.quantity*oop3.piece_in_set) as pieces_sold, 
             sum(oop3.quantity*oop3.piece_in_set*oop3.transfer_price_per_piece) as amount_sold  
          FROM 
            " . DB_PREFIX . "order o3 
          INNER JOIN 
            " . DB_PREFIX . "suborder osub3 
              ON osub3.order_id = o3.order_id 
                AND
                osub3.order_status_id > 0 
                AND
                osub3.order_status_id <> 2 
                AND
                osub3.invoice_no > 0 
          INNER JOIN 
            " . DB_PREFIX . "order_product oop3 
              ON oop3.buyer_invoice_id = osub3.buyer_invoice_id 
                 AND
                 oop3.sor_product = 1 
          WHERE
            oop3.wsb_purchase_id IN (".implode(',', $purchase_ids).")  
            AND
            o3.store_id in (0,2,9) 
            AND
            o3.stock_transfer = 0 
            AND o3.franchise_id = 0 
          GROUP By 
            oop3.wsb_purchase_id
        ";
      $results = $db->query($sql);
      if($results->num_rows){
        $purchase_ids = array_column($results->rows, 'wsb_purchase_id');
        return array_combine($purchase_ids, $results->rows);
      }
      return array();
  }

  /**
  * Method for get total return pieces amount
  * @param : Database\DB $db
  * @param : array       $purchase_ids
  * @return: array       $result data
  * @author: MSA May 2019
  */  
  public function getTotalPiecesReturnAmount(Database\DB $db, array $purchase_ids)
  {
    $sql = "
            SELECT 
              oop4.wsb_purchase_id, 
              SUM(ort4.quantity) as pieces_returned, 
              SUM(ort4.quantity*oop4.transfer_price_per_piece) AS amount_returned  
            
            FROM 
                oc_credit_note ocn4 
            
            INNER JOIN 
                oc_return ort4 ON ort4.credit_note_id = ocn4.credit_note_id 
            
            INNER JOIN oc_order_product oop4 ON oop4.order_product_id = ort4.order_product_id AND oop4.sor_product = 1 
            
            INNER JOIN oc_suborder osub4 ON osub4.buyer_invoice_id = oop4.buyer_invoice_id 
                                        AND osub4.invoice_no > 0 
                                        AND osub4.order_status_id > 0 
                                        AND osub4.order_status_id <> 2 
            INNER JOIN oc_order o4 ON o4.order_id = osub4.order_id 
                                   AND o4.store_id in (0,2,9) 
                                   AND o4.stock_transfer = 0 
                                   AND o4.franchise_id = 0 
            WHERE 
              oop4.wsb_purchase_id IN (".implode(',', $purchase_ids).")
              AND
              ocn4.credit_note_status = 1 

            GROUP BY oop4.wsb_purchase_id
        ";
      $results = $db->query($sql);
      if($results->num_rows){
        $purchase_ids = array_column($results->rows, 'wsb_purchase_id');
        return array_combine($purchase_ids, $results->rows);
      }
      return array();
  }

  /**
  * Method for get total paid amount for SOR invoice
  * @param : Database\DB $db
  * @param : int         $seller_id
  * @return: array       $data
  * @author: MSA May 2019
  */
  public function getTotalPaidAmountForSorInvoice(Database\DB $db, int $seller_id, array $purchase_ids){
      
      $result = array();
      $purchase_ids = array_filter(array_map('intval', $purchase_ids));
      
      if ( !empty($purchase_ids) && !empty($seller_id) ) {
        
          // Getting date from wsb_sor_payment table
          $sql1 = "SELECT wsb_purchase_id, COALESCE(SUM(trxn_amount), 0) AS amount_paid 
                   FROM " . DB_PREFIX . "wsb_sor_payment AS wsp 
                   WHERE seller_id = " . (int)$seller_id . "
                     AND wsb_purchase_id IN (".implode(',', $purchase_ids).")
                     AND trxn_done IN ('BANK_REQUESTED', 'BANK_PROCESSED', 'BANK_SUCCESS') 
                   GROUP BY wsb_purchase_id 
                   ORDER BY NULL";
          $query1 = $db->query($sql1);
          if ($query1->num_rows) {
              $result = array_combine(array_column($query1->rows, 'wsb_purchase_id'), 
                                      array_column($query1->rows, 'amount_paid'));
          }
          
          // Getting data from trxn_details table for WSB_PURCHASE entries
          $sql2 = "SELECT trxn_for_id AS wsb_purchase_id, COALESCE(SUM(trxn_amount), 0) AS amount_paid 
                   FROM " . DB_PREFIX . "trxn_details 
                   WHERE trxn_for_id IN (".implode(',', $purchase_ids).")
                     AND trxn_done IN ('BANK_REQUESTED', 'BANK_PROCESSED', 'BANK_SUCCESS') 
                     AND trxn_for = 'WSB_PURCHASE' 
                  GROUP BY wsb_purchase_id 
                  ORDER BY NULL";
          $query2 = $db->query($sql2);
          foreach ($query2->rows as $row) {
              $result[$row['wsb_purchase_id']] = round((float)($result[$row['wsb_purchase_id']] ?? 0.00), 2) + 
                                                 round((float)$row['amount_paid'], 2);
          }
          
          // Getting data from trxn_details table for WSB_SOR_PAYMENT entries
          $sql3 = "SELECT wsp.wsb_purchase_id, COALESCE(SUM(td.trxn_amount), 0) AS amount_paid 
                   FROM " . DB_PREFIX . "trxn_details AS td 
                   JOIN " . DB_PREFIX . "wsb_sor_payment AS wsp 
                     ON wsp.sor_payment_id = td.trxn_for_id 
                        AND wsp.wsb_purchase_id IN (".implode(',', $purchase_ids).") 
                   WHERE td.trxn_for = 'WSB_SOR_PAYMENT' 
                     AND td.trxn_done IN ('BANK_REQUESTED', 'BANK_PROCESSED', 'BANK_SUCCESS') 
                  GROUP BY wsp.wsb_purchase_id 
                  ORDER BY NULL";
          $query3 = $db->query($sql3);
          foreach ($query3->rows as $row) {
              $result[$row['wsb_purchase_id']] = round((float)($result[$row['wsb_purchase_id']] ?? 0.00), 2) + 
                                                 round((float)$row['amount_paid'], 2);
          }
      }
      
      return $result;
    }

}
