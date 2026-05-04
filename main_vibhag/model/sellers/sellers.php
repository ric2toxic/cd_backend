<?php
class ModelSellersSellers extends Model{
    // adding sellers
    public function addSeller($data = array()) {

        // insert pickup_city_code
        $pickup_city_code = substr($data['seller_nickname'], (strpos($data['seller_nickname'],'_')+1), 2);

         $password_secret = password_hash($data['seller_password'], PASSWORD_DEFAULT);

        $this->db->query("INSERT INTO ". DB_PREFIX ."customer
                            SET firstname = '" . $this->db->escape($data['seller_firstname']) . "',
                                lastname = '" . $this->db->escape($data['seller_lastname']) . "',
                                email = '" . $this->db->escape($data['seller_email']) . "',
                                salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "',
                                password = '" . $password_secret . "',
                                password_mode = 'new',
                                telephone = '" . $this->db->escape($data['seller_telephone']) . "',
                                date_added = NOW()
                        ");

        $customer_id = $this->db->getLastId();

        if(!empty($data['sor_enable_checkbox'])){
            $sor_enabled = $data['sor_enable_checkbox'];
        }else{
            $sor_enabled = 0;
        }

        $this->db->query("INSERT INTO ". DB_PREFIX ."ms_seller
                            SET seller_id = '" . (int)$customer_id . "',
                                nickname = '" . $this->db->escape($data['seller_nickname']) . "',
                                company = '" . $this->db->escape($data['seller_company']) . "',
                                seller_description = '" . $this->db->escape($data['seller_description']) . "',
                                address1 = '" . $this->db->escape($data['seller_address1']) . "',
                                address2 = '" . $this->db->escape($data['seller_address2']) . "',
                                city = '" . $this->db->escape($data['seller_city']) . "',
                                country_id = '" . $this->db->escape($data['seller_country']) . "',
                                zone_id = '" . $this->db->escape($data['seller_zone']) . "',
                                seller_status = '" . $this->db->escape($data['seller_status']) . "',
                                email = '" . $this->db->escape($data['seller_email']) . "',
                                pan = '". $this->db->escape($data['seller_pan_no']) ."',
                                tin = '". $this->db->escape($data['seller_tin_no']) ."',
                                seller_approved = '1',
                                product_validation = '2',
                                seller_group = '1',
                                sor_enabled = '".(int)$sor_enabled."',
                                date_created = NOW()
                         ");

        if(!empty($data['seller_seokeyword'])){
            $this->db->query("INSERT INTO ". DB_PREFIX ."url_alias
                        SET query = 'seller_id=".$customer_id."',
                            keyword = '" . $this->db->escape($data['seller_seokeyword']) . "'
                        ");
        }

        if(!empty($data['seller_additional_email'])){
            $array_additional_email = explode(',',rtrim(trim($data['seller_additional_email']),','));
            foreach($array_additional_email as $emails){
                $this->db->query("INSERT INTO oc_customer_additional_email
                        SET customer_id = '" . $customer_id . "',
                        email = '" . $this->db->escape($emails) . "'");
            }
        }

        // adding content in oc_review_rules
        //get content of oc_review_rules

        if(isset($data['category_rating']) && !empty($data['category_rating'])){
            foreach($data['category_rating'] as $data_rating){
                if($data_rating['rating'] != 0){
                    $this->db->query("INSERT INTO oc_review_rules
                          SET seller_id = ".(int)$customer_id.",
                                rule_type = 'category',
                                category_id = ".(int)$data_rating['category_id'].",
                                rating = ".(int)$data_rating['rating']."
                       ");
                }
            }
        }

        if(isset($data['categories_global_review']) && ($data['categories_global_review'] != 0 )){
            $this->db->query("INSERT INTO oc_review_rules
                              SET seller_id = ".(int)$customer_id.",
                                rating = ".$this->db->escape($data['categories_global_review']).",
                                rule_type = 'global'");
        }

    }


    // update sellers
    public function editSeller($seller_id, $data) {

        // insert/update pickup_city_code
        $pickup_city_code = substr($data['seller_nickname'], (strpos($data['seller_nickname'],'_')+1), 2);


        // Getting current status of the seller for future comparisons
        $currentSellerStatus = $this->getSellerStatus($seller_id);


        $this->db->query("UPDATE  ". DB_PREFIX ."customer
                            SET firstname = '" . $this->db->escape($data['seller_firstname']) . "',
                                lastname = '" . $this->db->escape($data['seller_lastname']) . "',
                                email = '" . $this->db->escape($data['seller_email']) . "',
                                telephone = '" . $this->db->escape($data['seller_telephone']) . "'
                           WHERE customer_id = '". (int)$seller_id ."'
                        ");

        if ( isset($data['seller_password']) and !empty($data['seller_password']) ) {

            $password_secret = password_hash($data['seller_password'], PASSWORD_DEFAULT);

            $this->db->query("UPDATE " . DB_PREFIX . "customer
                              SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "',
                              password = '" .$password_secret . "',
                              password_mode = 'new'
                              WHERE customer_id = '" . (int)$seller_id . "'");

        }

        $this->db->query("UPDATE ". DB_PREFIX ."ms_seller
                          SET nickname = '" . $this->db->escape($data['seller_nickname']) . "',
                              company = '" . $this->db->escape($data['seller_company']) . "',
                              seller_description = '" . $this->db->escape($data['seller_description']) . "',
                              address1 = '" . $this->db->escape($data['seller_address1']) . "',
                              address2 = '" . $this->db->escape($data['seller_address2']) . "',
                              city = '" . $this->db->escape($data['seller_city']) . "',
                              country_id = '" . (int)($data['seller_country']) . "',
                              zone_id = '" . (int)($data['seller_zone']) . "',
                              seller_status = '" . (int)($data['seller_status']) . "',
                              email = '" . $this->db->escape($data['seller_email']) . "',
                              pan = '". $this->db->escape($data['seller_pan_no']) ."',
                              tin = '". $this->db->escape($data['seller_tin_no']) ."',
                              seller_approved = '1',
                              product_validation = '2',
                              seller_group = '1',
                              bank_ac_holder_name = '". $this->db->escape($data['bank_ac_holder_name']) ."',
                              bank_ac_number = '". $this->db->escape($data['bank_ac_number']) ."',
                              ifsc_code = '". $this->db->escape($data['ifsc_code']) ."',
                              bank_name = '". $this->db->escape($data['bank_name']) ."',
                              bank_branch = '". $this->db->escape($data['bank_branch']) ."',
                              bank_city = '". $this->db->escape($data['bank_city']) ."',
                              bank_state = '". $this->db->escape($data['bank_state']) ."'
                          WHERE seller_id = '". (int)$seller_id ."'");

        $check_seoKeyword = $this->db->query("SELECT * FROM oc_url_alias WHERE query = 'seller_id=". (int)$seller_id. "'");


        if(!empty($data['seller_seokeyword'])){
            if($check_seoKeyword->num_rows > 0){
                $this->db->query("UPDATE ". DB_PREFIX ."url_alias
                            SET keyword = '" . $this->db->escape($data['seller_seokeyword']) . "'
                            WHERE query = 'seller_id=". (int)$seller_id. "'
                        ");
            }else{
                $this->db->query("INSERT INTO ". DB_PREFIX ."url_alias
                        SET query = 'seller_id=".(int)$seller_id."',
                            keyword = '" . $this->db->escape($data['seller_seokeyword']) . "'
                        ");
            }
        }

        // Updating product statuses, if the seller status is changed.
        if ( (int)$data['seller_status'] != $currentSellerStatus) {

            if ( (int)$data['seller_status'] == 1) {
                $this->db->query("UPDATE  ". DB_PREFIX ."product p
                                  LEFT JOIN ". DB_PREFIX ."ms_product mp ON (mp.product_id = p.product_id)
                                  SET p.status = '1'
                                  WHERE mp.seller_id = '". (int)$seller_id . "'
                                    AND p.status = '0'");

            } else if ( (int)$data['seller_status'] == 2 OR $data['seller_status'] == 3) {
                $this->db->query("UPDATE  ". DB_PREFIX ."product p
                                  LEFT JOIN ". DB_PREFIX ."ms_product mp ON (mp.product_id = p.product_id)
                                  SET p.status = '0'
                                  WHERE mp.seller_id = '". (int)$seller_id . "'
                                    AND p.status = '1'");
            }

        }

        // Additional emails of the seller
        $this->db->query("DELETE FROM oc_customer_additional_email WHERE customer_id = '" . (int)$seller_id . "'");

        if(!empty($data['seller_additional_email'])){
            $array_additional_email = explode(',',rtrim(trim($data['seller_additional_email']),','));
            foreach($array_additional_email as $emails){
                $this->db->query("INSERT INTO oc_customer_additional_email
                                  SET customer_id = '" . (int)$seller_id . "',
                                      email = '" . $this->db->escape($emails) . "'");
            }
        }

        if(isset($data['sor_enable_checkbox'])){
            $data['sor_enable_checkbox'] = $data['sor_enable_checkbox'];
        }else{
            $data['sor_enable_checkbox'] = 0;
        }

        $this->MsLoader->MsSeller->updateSorSetting($seller_id,$data['sor_enable_checkbox']);

        // update oc_wsb_seller_to_store
        $this->db->query("UPDATE oc_wsb_seller_to_store
                          SET seller_markup_over_tp = '" . (float)$data['seller_markup'] . "',
                              wsb_commission_over_tp = '" . (float)$data['wsb_commission'] . "'
                          WHERE seller_id = '" . (int)$seller_id . "'");


        ////////////              Updating Review/Rating rules         ///////////////////////////
        $this->db->query("DELETE FROM " . DB_PREFIX . "review_rules WHERE seller_id = '" . (int)$seller_id . "'");

        if(isset($data['category_rating']) && !empty($data['category_rating'])){
            foreach($data['category_rating'] as $data_rating){
                if($data_rating['rating'] != 0 ){
                    $this->db->query("INSERT INTO oc_review_rules
                          SET seller_id = ".(int)$seller_id.",
                                rule_type = 'category',
                                category_id = ".(int)$data_rating['category_id'].",
                                rating = ".(int)$data_rating['rating']."
                       ");
                }
            }
        }

        if(isset($data['categories_global_review']) && ($data['categories_global_review'] != 0)){
            $this->db->query("INSERT INTO oc_review_rules
                              SET seller_id = ".(int)$seller_id.",
                                  rating = ".$this->db->escape($data['categories_global_review']).",
                                  rule_type = 'global'");
        }

    }


    // delete seller
    public function deleteSeller($seller_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ms_product WHERE seller_id = '" . (int)$seller_id . "'");
    }


    // get seller information by seller id
    public function getSeller($seller_id) {

        $query = $this->db->query("SELECT ms.company as seller_company,ms.*,c.*,wss.*,
                                   (SELECT keyword FROM `oc_url_alias` WHERE query = 'seller_id=".$seller_id."' ) as seller_seokeyword
                                   FROM ". DB_PREFIX ."ms_seller ms
                                     INNER JOIN ". DB_PREFIX ."customer c
                                       ON (ms.seller_id = c.customer_id)
                                     LEFT JOIN ". DB_PREFIX ."wsb_seller_to_store wss
                                       ON (wss.seller_id = ms.seller_id)
                                     WHERE ms.seller_id = '" . (int)$seller_id . "'
                                     ");

        $additional_emails = $this->db->query("SELECT email FROM ". DB_PREFIX ."customer_additional_email WHERE customer_id = '".(int)$seller_id."' ");
        $query->row['additional_email'] = implode(',',array_column($additional_emails->rows,'email'));

        return $query->row;
    }

    // Get only seller status for a seller_id
    public function getSellerStatus($seller_id) {

        $query = $this->db->query("SELECT seller_status FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");

        if ($query->num_rows)
            return (int)($query->row['seller_status']);
        else
            return false;
    }


    //get Sellers List
    public function getSellers($data = array()){
        $sql = "SELECT  ms.seller_id,
                        CONCAT(c.firstname, ' ', c.lastname) AS seller,
                        ms.nickname,
                        ms.company,
                        c.email,
                        c.telephone,
                        ms.date_created,
                        ms.seller_status,
                        (SELECT COUNT(*) as total FROM `oc_ms_product` WHERE seller_id = ms.seller_id ) as product_total,
                        (SELECT COUNT(*) as total FROM `oc_review_rules` WHERE seller_id = ms.seller_id ) as seller_total_rating,
                        ms.vacation_mode
                FROM ". DB_PREFIX ."ms_seller ms
                INNER JOIN ". DB_PREFIX ."customer c
                ON (ms.seller_id = c.customer_id)
                INNER JOIN ". DB_PREFIX ."seller_updates su
                ON (su.seller_id = ms.seller_id)";


        if (!empty($data['filter_sale'])) {
            $sql .= " INNER JOIN ". DB_PREFIX ."ms_product mp
                ON (ms.seller_id = mp.seller_id)
                INNER JOIN ". DB_PREFIX ."product_special ps
                ON (mp.product_id = ps.product_id)
                AND date_start <= DATE(NOW()) AND date_end >= DATE(NOW())";
        }

        if (!empty($data['filter_seller'])) {
            $sql .= " AND ms.nickname LIKE '%" . $this->db->escape($data['filter_seller']) . "%'";
        }

        if (!empty($data['filter_company'])) {
            $sql .= " AND ms.company LIKE '%" . $this->db->escape($data['filter_company']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $sql .= " AND c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (!empty($data['filter_telephone'])) {
            $sql .= " AND c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
        }

        if (isset($data['filter_vacation']) and $data['filter_vacation'] != "*") {
            $sql .= " AND ms.vacation_mode = '" . (int)$data['filter_vacation'] . "'";
        }

        if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] != "*") {
          if($data['filter_seller_status'] > 0 ){
            $sql .= " AND ms.seller_status = '" . $this->db->escape($data['filter_seller_status']) . "'";
          }else{
            $sql .= " AND su.verification_status = '" . $this->db->escape($data['filter_seller_status']) . "'";
            $sql .= " GROUP BY su.seller_id ";
          }
        }

        if (isset($data['filter_rating']) and $data['filter_rating'] != "*") {
            $sql .= " HAVING seller_total_rating = '" . (int)$data['filter_rating'] . "'";
        }


        $sort_data = array(
            'ms.nickname',
            'ms.company',
            'c.firstname',
            'c.email',
            'c.telephone',
            'ms.seller_status',
            'ms.date_created',
            'product_total'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY ms.nickname";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 30;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }


    // get Total sellers
    public function getTotalSellers($data = array()){
        $sql = "SELECT ms.seller_id,
                  (SELECT COUNT(*) as total FROM `oc_review_rules` WHERE seller_id = ms.seller_id ) as seller_total_rating
                FROM ". DB_PREFIX ."ms_seller ms
                INNER JOIN ". DB_PREFIX ."customer c
                  ON (ms.seller_id = c.customer_id)
                INNER JOIN ". DB_PREFIX ."seller_updates su
                ON (su.seller_id = ms.seller_id)";  

        if (!empty($data['filter_sale'])) {
            $sql .= " INNER JOIN ". DB_PREFIX ."ms_product mp
                ON (ms.seller_id = mp.seller_id)
                INNER JOIN ". DB_PREFIX ."product_special ps
                ON (mp.product_id = ps.product_id) WHERE
                date_start <= DATE(NOW()) AND date_end >= DATE(NOW())";
        }

        if (!empty($data['filter_seller'])) {
            $sql .= " AND ms.nickname LIKE '%" . $this->db->escape($data['filter_seller']) . "%'";
        }

        if (!empty($data['filter_company'])) {
            $sql .= " AND ms.company LIKE '%" . $this->db->escape($data['filter_company']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $sql .= " AND c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (!empty($data['filter_telephone'])) {
            $sql .= " AND c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
        }

        if (isset($data['filter_vacation']) and $data['filter_vacation'] != "*") {
            $sql .= " AND ms.vacation_mode = '" . (int)$data['filter_vacation'] . "'";
        }

        if (!empty($data['filter_seller_status']) and $data['filter_seller_status'] != "*") {
          if($data['filter_seller_status'] > 0 ){
            $sql .= " AND ms.seller_status = '" . $this->db->escape($data['filter_seller_status']) . "'";
          }else{
            $sql .= " AND su.verification_status = '" . $this->db->escape($data['filter_seller_status']) . "'";
            $sql .= " GROUP BY su.seller_id ";
          }
        }

        if (isset($data['filter_rating']) and $data['filter_rating'] != "*") {
            $sql .= " HAVING seller_total_rating = '" . (int)$data['filter_rating'] . "'";
        }
        //echo $sql; die;
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    // get seller categroy rating
    public function getSellerCategoryRating($seller_id){
        $sql = "SELECT rr.seller_id,
                        rr.category_id,
                        rr.rating,
                        cd.name
                FROM oc_review_rules rr
                LEFT JOIN oc_category_description cd
                   ON (rr.category_id = cd.category_id)
                WHERE rr.seller_id = ".$seller_id . "
                   AND cd.language_id = 1 ";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    //get seller global rating
    public function getSellerGlobalRating($seller_id){
        $sql = "SELECT rr.seller_id,
                        rr.category_id,
                        rr.rating
                FROM oc_review_rules rr
                WHERE rr.seller_id = ".$seller_id . "
                   AND rr.category_id = '0'
                ";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getSellerClearanceSaleStatus($seller_id) {
        $sql = "SELECT ps.product_id
                FROM ". DB_PREFIX ."ms_product mp
                INNER JOIN ". DB_PREFIX ."product_special ps
                ON (mp.product_id = ps.product_id)
                WHERE mp.seller_id = ".$seller_id ."
                AND date_start <= DATE(NOW()) AND date_end >= DATE(NOW())";

        $query = $this->db->query($sql);
        //echo "<pre>"; print_r($query->row); die;
        return $query->row;
    }

    // Get Sellers From product id
    public function getSellersFromOrderProductId($order_product_id, $fields = array()){
      if(empty($fields)){
        $fields = '*';
      }
      else{
        if(in_array('product_id',$fields)){
          $key = array_search('order_product_id', $fields);
          unset($fields[$key]);
        }
        $fields = 'oms.'.implode(',oms.',$fields);
        $fields .= ',op.order_product_id';
      }
      $sql  = "SELECT op.seller_sku,$fields ";
      $sql .= "FROM ".DB_PREFIX."order_product op ";
      $sql .= "LEFT JOIN ".DB_PREFIX."ms_seller oms ";
      $sql .=       "ON ( oms.seller_id = op.seller_id ) ";
      $sql .= "WHERE op.order_product_id IN ( ".implode(',',$order_product_id)." )";
      $result = $this->db->query($sql);
      if(!empty($result)){
        foreach($result->rows as $key => $values){
          if(!empty($values['zone_id'])){
             $zone_query = $this->db->query("SELECT name as state FROM ".DB_PREFIX."zone
                                                  WHERE zone_id = " .(int)$values['zone_id']);
             if( $zone_query->num_rows > 0 ){
                 $result->rows[$key]['state'] = $zone_query->row['state'];
             }
          }

          if(!empty($values['country_id'])){
             $country_query = $this->db->query("SELECT name as country FROM ".DB_PREFIX."country
                                                  WHERE country_id = " .(int)$values['country_id']);
             if( $country_query->num_rows > 0){
                 $result->rows[$key]['country'] = $country_query->row['country'];
             }
          }
        }
        return $result->rows;
      }
      return array();
    }

    public function getSellersName($filter_name){
        $sql = "SELECT  seller_id,
                        nickname
                FROM ". DB_PREFIX ."ms_seller
                WHERE nickname LIKE '%" . $filter_name['filter_name'] . "%'
                LIMIT 5";
        return $this->db->query($sql)->rows;
    }
    
    /*
    // @Authour : Amarat
    // @desrciption : get seller category_list for seller promotion
    // @params : seller_id (integer value)
    */
    
    public function getSellerCategoryList($seller_id) {
        
        $data = array();
        
        //get seller category 
        $sql = "SELECT PC.category_id FROM " . DB_PREFIX . "product_to_category AS PC WHERE PC.product_id in "
                . "(SELECT MSP.product_id FROM " . DB_PREFIX . "ms_product AS MSP WHERE MSP.seller_id = " . $seller_id . ")"
                . " GROUP by PC.category_id";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            foreach ($result->rows as $key => $val) {
              
                $sql = "SELECT CD.category_id, count(PC.product_id) as product_count, CD.name 
                        FROM " . DB_PREFIX . "category_description AS CD
                          LEFT JOIN " . DB_PREFIX . "product_to_category AS PC ON CD.category_id = PC.category_id
                          LEFT JOIN " . DB_PREFIX . "product AS P ON P.product_id = PC.product_id
                          LEFT JOIN " . DB_PREFIX . "ms_product AS mP ON mP.product_id = P.product_id
                          LEFT JOIN " . DB_PREFIX . "ms_seller AS s ON s.seller_id = mP.seller_id
                        WHERE CD.category_id = " .$val['category_id']." 
                          AND s.seller_id = ".$seller_id."
                          AND CD.language_id = 1
                          AND P.status = 1 AND P.stock_status_id = 7
                          AND P.is_archived <> 1
                          AND P.quantity > 0
                          AND s.vacation_mode = 0
                          AND s.seller_status = 1
                          AND P.piece_in_set > 0
                          AND (P.date_available IS NULL OR P.date_available <= ".strtotime(date("Y-m-d")).")
                          AND CHAR_LENGTH(P.hsn_code) >= 4
                          AND CHAR_LENGTH(P.hsn_code) <= 8
                          AND P.hsn_code REGEXP '[0-9]{4,}'
                          AND P.sort_order > 998";
                          
                //echo $sql; die;
                $query = $this->db->query($sql);
                $res = $query->row;
                $data[$key]['category_id'] = $res['category_id'];
                $data[$key]['name'] = $res['name'];
                $data[$key]['product_count'] = $res['product_count']; 
            } 
            
        }
        return $data; 
    }
    
    /*
    // @Authour : Amarat
    // @desrciption : get seller promotion_list
    // @params : seller_id (integer value)
    */
    
    public function getSellerPromotion($seller_id){
        
        $result = array(); 
        
        $sql = "SELECT * FROM " . DB_PREFIX . "seller_promotion WHERE seller_id = " . $seller_id . "";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $result = $query->rows;
        }
        return $result;  
    }
    
    public function updateSellerPromotion( $type, $update_data, $promotion_id = 0 ) {
      
      if($type == "update") {
        $update_sql_head = "UPDATE ". DB_PREFIX ."seller_promotion SET ";
        if($promotion_id) {
          $update_sql_tail = " WHERE id = ".$promotion_id;
        }
        
      } else if($type == "insert") {
        $update_sql_head = "INSERT INTO ". DB_PREFIX ."seller_promotion SET ";
        $update_sql_tail = "";
      }
      $update_sql_data = implode(',',$update_data);
      $update_sql = $update_sql_head.$update_sql_data.$update_sql_tail;
      
      $result = $this->db->query($update_sql);
      return $result;
    }

    public function getSellersSorTerms($seller_id)
    {
      $query = $this->db->query("SELECT sor_days, sor_type from ".DB_PREFIX."seller_sor_terms
          WHERE seller_id = ".(int)$seller_id."");
      if ($query->num_rows) 
      {
        return $query->row;
      }
      else
      {
         return array('sor_days'=>'','sor_type'=>'regular');
      }
    }

    

    public function updateSellerSorTerms($seller_id, $sor_days, $sor_type, $product_update=0)
    {
      $query = $this->db->query("SELECT seller_id from ".DB_PREFIX."seller_sor_terms
          WHERE seller_id = ".(int)$seller_id."");
      
      if ($query->num_rows) 
      {
         $sql = "UPDATE ". DB_PREFIX ."seller_sor_terms SET 
              sor_days = ".(int)$sor_days.",
              sor_type = '".$this->db->escape($sor_type)."'
              where  seller_id = ".(int)$seller_id;
      }
      else
      {
        $sql = "INSERT INTO ". DB_PREFIX ."seller_sor_terms SET 
              seller_id = ".(int)$seller_id.",
              sor_days = ".(int)$sor_days.",
              sor_type = '".$this->db->escape($sor_type)."'";
      }        
      $this->db->query($sql);  



      if($product_update)
      {
        $this->load->model('catalog/product');
        $query = $this->db->query("SELECT product_id FROM oc_ms_product WHERE seller_id = ".(int)$seller_id."");
        $product_ids = array_column($query->rows,'product_id');
       if(count($product_ids) > 0)
       { 
         if($sor_days)
         {
           $this->model_catalog_product->bulkUpdateSor($sor_days, $sor_type, $product_ids, 'insert');
         }
         else
         {
           $this->model_catalog_product->bulkUpdateSor('', '', $product_ids, 'remove');
         }
       } 

      } 

    }
    
    public function updateProductSortOrder( $product_id, $new_sort_order ) {
      $sql = "UPDATE ".DB_PREFIX."product 
              SET sort_order = ".$new_sort_order.", 
                  date_modified = NOW()
              WHERE product_id = ".$product_id;
      $result = $this->db->query($sql);
      return $result;
    }
    
    public function getCategoryName($category_id){
      $sql = "SELECT name from ".DB_PREFIX."category_description
          WHERE category_id = ".$category_id."";
      $result = $this->db->query($sql);
      return $result->row;
    }

    /**
     * Function updateSellerExclusiveStatus is used to update exclusive status of sellers
     * @params $seller_id_arr - seller_id array()
     * @params $exclusive_status - exclusive_status string
     * @params $is_all_product_update_check -  0 or 1
     * @return  true if successful else false
     * @author  Nilesh, 2018
     */
    public function updateSellerExclusiveStatus(array $seller_id_arr, string $exclusive_status, int $is_all_product_update_check = 1): void {
        $product_change_log = new ProductChangeLog($this->registry);
        if ($is_all_product_update_check) {
            $seller_product = SellerInfo::getSellerProducts($this->db, $seller_id_arr);
            if (!empty($seller_product)) {
                foreach ($seller_product as $seller_id => $product_ids) {
                    $sql = "SELECT product_id,
                               exclusive 
                    FROM " . DB_PREFIX . "product
                    WHERE product_id IN(" . implode(",", $product_ids) . ")";
                    $query = $this->db->query($sql);
                    if ($query->num_rows) {
                        foreach ($query->rows as $product_excl) {
                            if ($product_excl['exclusive'] != $exclusive_status) {
                                $source_field = 'seller_list';
                                $changes_data = array(
                                    'exclusive' => array(
                                        'old_value' => $product_excl['exclusive'],
                                        'new_value' => $exclusive_status
                                    )
                                );
                                $product_change_log->recordLogs($product_excl['product_id'], $changes_data, $source_field);
                            }
                        }
                    }
                }
            }
            $this->_updateProductTableExclusiveStatus($seller_id_arr, $exclusive_status);
        }
        //For logging
        $sql = "SELECT seller_id,
                       exclusive 
                FROM " . DB_PREFIX . "ms_seller
                WHERE seller_id IN(" . implode(",", $seller_id_arr) . ")";
        $query = $this->db->query($sql);

        $this->_updateMsSellerTableExclusiveStatus($seller_id_arr, $exclusive_status);

        if ($query->num_rows) {
            foreach ($query->rows as $seller_ids) {
                if ($seller_ids['exclusive'] != $exclusive_status) {
                    $source_field = 'seller_list';
                    $changes_data = array(
                        'exclusive' => array(
                            'old_value' => $seller_ids['exclusive'],
                            'new_value' => $exclusive_status
                        )
                    );
                    $product_change_log->recordLogs($seller_ids['seller_id'], $changes_data, $source_field, '', 'oc_ms_seller');
                }
            }
        }
    }

    /**
     * Function _updateMsSellerTableExclusiveStatus is used to update exclusive status of sellers in ms_seller table
     * @params $seller_id_arr - seller_id array()
     * @params $exclusive_status - exclusive_status string
     * @author  Nilesh, 2018
     */
    private function _updateMsSellerTableExclusiveStatus(array $seller_id_arr, string $exclusive_status) : void {
        $sql = "UPDATE " . DB_PREFIX . "ms_seller
                SET exclusive = '" . $this->db->escape($exclusive_status) . "'
                WHERE seller_id IN (" . implode(',', $seller_id_arr) . ")";
        $this->db->query($sql);
    }

    /**
     * Function _updateProductTableExclusiveStatus is used to update exclusive status of given products
     * @params $seller_id_arr - seller_id array()
     * @params $exclusive_status - exclusive_status string
     * @author  Nilesh, 2018
     */
    private function _updateProductTableExclusiveStatus(array $seller_id_arr, string $exclusive_status): void {
        $sql = "UPDATE " . DB_PREFIX . "product op
                INNER JOIN " . DB_PREFIX . "ms_product omp ON omp.product_id = op.product_id
                SET op.exclusive = '" . $this->db->escape($exclusive_status) . "'
                WHERE omp.seller_id IN (" . implode(',', $seller_id_arr) . ")";
        $this->db->query($sql);
    }

}
