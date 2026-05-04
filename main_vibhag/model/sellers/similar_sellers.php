<?php
class ModelSellersSimilarSellers extends Model{
    
    /**
    * Public method to get list of all similar sellers
    * @param: array $data [filters]
    * @return: array $result data
    * @author: MSA Nov 2019
    */
    public function getSimilarSellerList($data = array()) { 

        $sql = "
            SELECT 
              c.similar_seller_cluster_id, 
              c.category_id, 
              c.cluster_name, 
              ANY_VALUE(cd.name) AS category_name, 
              GROUP_CONCAT(DISTINCT CONCAT('[', ms2.nickname, ']') SEPARATOR ' ') AS seller_name
            FROM 
                similar_seller_cluster c
            JOIN 
                similar_seller_cluster_sellers cs1
              ON cs1.similar_seller_cluster_id = c.similar_seller_cluster_id
            JOIN 
                similar_seller_cluster_sellers cs2 
              ON cs2.similar_seller_cluster_id = cs1.similar_seller_cluster_id
            JOIN 
                oc_ms_seller ms1
              ON ms1.seller_id = cs1.seller_id 
            JOIN 
                oc_ms_seller ms2 
              ON ms2.seller_id = cs2.seller_id 
            JOIN 
                oc_category_description cd 
              ON cd.category_id = c.category_id 
                 AND 
                 cd.language_id = 1
            WHERE
                1 = 1
        ";

        $filter_category = (int)($data['filter_category'] ?? 0);

        if ($filter_category > 0) { 
            $sql .= " AND c.category_id = '" . $filter_category . "' ";  
        }
        
        // Pagination related filter condition
        $p = $data['page'] ?? '';
        if ( $p !== 'FIRST' && (int)$p > 0 ) {
            $sql .= " AND c.similar_seller_cluster_id < " . (int)$p;
        }

        $filter_cluster = trim(($data['filter_cluster'] ?? ''));

        if (!empty($filter_cluster)) { 
            $sql .= "  AND c.cluster_name LIKE '%" . $this->db->escape($filter_cluster) . "%'";
        }

        $filter_seller = trim(($data['filter_seller'] ?? ''));

        if(!empty($filter_seller)) {
            $sql .= " AND ms1.nickname LIKE '%" . $this->db->escape($filter_seller) . "%' ";
        }

        $sql .= " GROUP BY c.similar_seller_cluster_id " ;

        $sql .= " ORDER BY c.similar_seller_cluster_id DESC " ;
                     
        if (isset($data['limit'])) {
            if ((int)$data['limit'] < 1) {
                $data['limit'] = 30;
            }

            $sql .= " LIMIT " . (int) $data['limit'];
        }
        
        $result = $this->db->query( $sql );
        
        if($result->num_rows) {

            return $result->rows;
        }

        return array();
    }

    /**
    * Public method to get list of category filters
    * @param: void
    * @return: array $result data
    * @author: MSA Nov 2019
    */
    public function getCategoryForFilter(){
        $result = array();
        $sql = "SELECT 
                    ssc.category_id,
                    MAX(cd.name) as name
                FROM 
                    similar_seller_cluster ssc
                JOIN
                    ". DB_PREFIX ."category_description cd
                        ON cd.category_id = ssc.category_id AND cd.language_id = 1
                GROUP BY 
                    ssc.category_id
                ORDER BY NULL
            ";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $result = $query->rows;
        }
        return $result;
    }
    
    /**
    * Public method to get list of all categories
    * @param: void
    * @return: array $result data
    * @author: MSA Nov 2019
    */
    public function getAllCategory() {     
        $result = array();
        $sql = "SELECT  
                    c.category_id,
                    cd.name
                FROM ". DB_PREFIX ."category c
                INNER JOIN " . DB_PREFIX . "category_description cd
                        ON cd.category_id = c.category_id AND cd.language_id = 1
                WHERE 
                    cd.language_id = 1
                ORDER BY 
                    cd.name";    
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $result = $query->rows;
        }
        return $result;
    }

    /**
    * Public method to get list of all sellers
    * @param: int seller_id
    * @param: int category_id
    * @param: int edit_id
    * @return: array $result data
    * @author: MSA Nov 2019
    */
    public function getAllseller( int $category_id, int $edit_id, array $selected_sellers) {     
        $result = array();
        $exiting_seller = array();
        
        if(!empty($selected_sellers)){ 
            $exiting_seller = array_column($selected_sellers, 'seller_id');
        }

        $result = $this->getRemaningSellerCategoryWise($category_id,$exiting_seller);
        return $result;
    }

    /**
    * Public method to get similar sellers
    * @param: int $similar_seller_cluster_id
    * @return: array $data
    * @author: MSA Nov 2019
    */
    public function getSimilarSeller( int $similar_seller_cluster_id ) { 
        $data = array();
        
        if( $similar_seller_cluster_id > 0 )
        {
            $sql = "
                SELECT
                  cs.seller_id,
                  ms.nickname,
                  ms.company
                FROM
                    similar_seller_cluster_sellers cs
                JOIN
                    similar_seller_cluster ssc
                     ON cs.similar_seller_cluster_id = ssc.similar_seller_cluster_id
                JOIN
                    oc_ms_seller as ms
                    ON ms.seller_id = cs.seller_id
                WHERE
                    ssc.similar_seller_cluster_id = ".(int)$similar_seller_cluster_id."
                ORDER BY
                    nickname
            ";

            $result = $this->db->query($sql);
            
            if($result->num_rows) {
                foreach($result->rows as $key => $row){
                    $data[$key]['seller_id'] = $row['seller_id'];
                    $data[$key]['seller_name'] = $row['nickname'] . "  - " . $row['company']; 
                }
            }
        } 
          
        return $data;     
    } 

    /**
    * Public method to get remaining seller categories
    * @param: int $category_id
    * @param: array exiting_seller
    * @return: array $data
    * @author: MSA Nov 2019
    */
    public function getRemaningSellerCategoryWise($category_id,$exiting_seller) {    
        $result = array();
        $res = array();
        $data = array();
        
        $sql = "
                SELECT 
                  DISTINCT ms.seller_id,
                  ms.nickname,
                  ms.company
                FROM
                    ". DB_PREFIX ."ms_seller as ms
                INNER JOIN 
                    ". DB_PREFIX ."ms_product as msp ON msp.seller_id = ms.seller_id
                INNER JOIN 
                    ". DB_PREFIX ."product_to_category as ptc ON ptc.product_id = msp.product_id
                WHERE
                    ptc.category_id = " . (int)$category_id . "
                ";
        
        if(!empty($exiting_seller)) {

            $exiting_seller = array_unique(array_filter(array_map('intval', $exiting_seller), function($v) {return $v > 0;}));
            $sql .= " AND ms.seller_id NOT IN (" . $this->db->escape(implode(",",$exiting_seller)) . ")";
        }

        $sql .= " ORDER BY ms.nickname " ;

        $result = $this->db->query($sql);

        if(!empty($result->num_rows)){

            foreach ($result->rows as $key => $value) {

               $data[] = array(
                    'seller_id'     => $value['seller_id'],
                    'seller_name'   => $value['nickname'] . " - " . $value['company']
               );
            }
        }
        return $data;
    }

    /**
    * Public method to get edit seller data 
    * @param: int $id
    * @return: array $data
    * @author: MSA Nov 2019
    */
    public function getEditSellers( int $id )
    {   
        $sql = "
                SELECT 
                    ssc.similar_seller_cluster_id,
                    ssc.category_id,
                    ssc.cluster_name,
                    cd.name AS category_name,
                    GROUP_CONCAT(mss.nickname) AS seller_name
                FROM 
                    similar_seller_cluster ssc
                JOIN
                    similar_seller_cluster_sellers cs 
                        ON cs.similar_seller_cluster_id = ssc.similar_seller_cluster_id
                JOIN 
                    ". DB_PREFIX ."category_description cd 
                        ON cd.category_id = ssc.category_id AND cd.language_id = 1
                JOIN
                    ". DB_PREFIX ."ms_seller mss 
                        ON mss.seller_id = cs.seller_id
                WHERE
                   ssc.similar_seller_cluster_id = ".(int)$id."
                ";
        $result = $this->db->query($sql);
        $data = array();
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }

    /**
    * Public method to get category name
    * @param: int $category_id
    * @return: array $data
    * @author: MSA Nov 2019
    */
    public function getcategory( int $category_id ){
        
        $data = array();
        
        $sql = "SELECT name 
                FROM 
                    ".DB_PREFIX."category_description
                WHERE 
                    category_id = " . (int)$category_id . "
                    AND 
                    language_id = 1
                ";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $data = $query->row;
        }
        
        return $data;
    }

    /**
    * Public method to add similar sellers data
    * @param:  array $data
    * @return: void
    * @author: MSA Nov 2019
    */
    public function addSimilarSeller($data = array()) {  

        $category_id = $this->request->post['category_id'] ?? 0;
        $cluster_name   = $this->request->post['cluster_name'] ?? '';    
        $similar_seller_data = $this->request->post['similar_seller'] ?? array();  
        
        if(!empty($category_id) && 
           !empty($cluster_name) && 
           !empty($similar_seller_data)
        ) {  
            //add seller cluster data
            $sql = "INSERT INTO 
                            similar_seller_cluster
                        SET 
                            category_id = ".(int)$category_id.",
                            cluster_name= '".$this->db->escape(trim($cluster_name))."'
                        ";
            $this->db->query($sql);

            $similar_seller_cluster_id = $this->db->getLastId();

            if(!empty($similar_seller_cluster_id)) {
               
               //add similar sellers data
                $sql = "INSERT INTO similar_seller_cluster_sellers
                        (`similar_seller_cluster_id`,`seller_id`)
                        VALUES 
                       ";
                //for other similar sellers
                foreach ($similar_seller_data as $cluster_seller_id) 
                {
                   $sql .= "( ".(int)$similar_seller_cluster_id.", ".(int)$cluster_seller_id." ),";            

                } 
                
                $sql = rtrim($sql, ", ");
                
                $this->db->query($sql);       
            }
        }
    }

    /**
    * Public method to edit similar sellers data
    * @param:  array $data
    * @return: void
    * @author: MSA Nov 2019
    */
    public function editSimilarSeller( array $data ) {  
        
        //first delete all similar seller clusters
        $this->deleteEditSimialrSeller( $data );
        
        //insert again
        $this->saveEditSimilarSeller( $data );
    }

    /**
    * Public method to delete similar seller data
    * @param:  array $data
    * @return: void
    * @author: MSA Nov 2019
    */
    public function deleteEditSimialrSeller( array $data ) {      
        if(!empty($data['edit_id']))
        {
            //delete similar seller clusters
            $sql = "DELETE FROM similar_seller_cluster_sellers
                    WHERE
                        similar_seller_cluster_id = ".(int)$data['edit_id']."
                    ";
            $result = $this->db->query($sql);
        }
    }

    /**
    * Public method to save edit similar seller data
    * @param:  array $data
    * @return: void
    * @author: MSA Nov 2019
    */
    public function saveEditSimilarSeller( array $data )
    {
        if(!empty($data['edit_id']) && !empty($data['similar_seller']))
        {
            //edit similar sellers table data
            $cluster_name =  $data['cluster_name'] ?? '';
            $update_sql = "UPDATE similar_seller_cluster
                             SET 
                                cluster_name = '".$this->db->escape(trim($cluster_name))."'
                            WHERE
                                similar_seller_cluster_id = ".(int)$data['edit_id']."
                            ";
            $this->db->query($update_sql);

            //add similar sellers data
            $sql = "INSERT INTO similar_seller_cluster_sellers
                    (`similar_seller_cluster_id`,`seller_id`)
                    VALUES 
                   ";

            //for other similar sellers
            foreach ($data['similar_seller'] as $cluster_seller_id) 
            {
               $sql .= "( ".(int)$data['edit_id'].", ".(int)$cluster_seller_id." ),";            
            } 
            
            $sql = rtrim($sql, ", ");
            
            $this->db->query($sql);  
        }
    }

    /**
    * Public method to delete similar seller data from db table
    * @param:  int $delete_id
    * @return: void
    * @author: MSA Nov 2019
    */
    public function deleteSimialrSeller(int $delete_id)
    {
        if(!empty($delete_id))
        {
            //delete similar seller cluster record
            $sql = "DELETE FROM similar_seller_cluster
                    WHERE
                        similar_seller_cluster_id = ".(int)$delete_id."
                    ";
            $result = $this->db->query($sql);

            /*similar_seller_cluster_sellers table data will deleted with foreign key constraint*/
        }
    }

}
