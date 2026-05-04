<?php
class ModelFeedFeed extends Model {
    public function getCategories($parent_id = 0){
        $query = $this->db->query("SELECT c.category_id, cd.name, c.image FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
        $categories = array();
        if ($query->num_rows > 0) {
            // Loop through the returned rows for processing
            foreach ($query->rows as $result) {
                if(!empty($result['image'])){
                    $img = $this->config->get('config_url').'image/'.$result['image'];
                }else{
                    $img = $this->config->get('config_url').'image/no_image.png';
                }
                $subCategories = $this->getSubCategories($result['category_id']);
                $categories[] = array(
                    'category_id' => $result['category_id'],
                    'name'      => html_entity_decode($result['name']),
                    'image'      => $img,
                    'sub_categories' => $subCategories
                );
            }
        }

        return $categories;
    }

    public function getSubCategories($parent_id = 0) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c
                               LEFT JOIN " . DB_PREFIX . "category_description cd
                               ON (c.category_id = cd.category_id)
                               LEFT JOIN " . DB_PREFIX . "category_to_store c2s
                               ON (c.category_id = c2s.category_id)
                               WHERE c.parent_id = '" . (int)$parent_id . "'
                               AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                               AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                               AND c.status = '1'
                               ORDER BY c.sort_order, LCASE(cd.name)");
    $categories = array();
    $i = 0;
    foreach ($query->rows as $result) {
        if(!empty($result['image'])){
            $img = $this->config->get('config_url').'image/'.$result['image'];
        }else{
            $img = $this->config->get('config_url').'image/no_image.png';
        }
        $categories[] = array(
            'sub_category_id' => $result['category_id'],
            'sub_category_name'      => html_entity_decode($result['name']),
            'image'      => $img,
            'parent_id'  => $parent_id
        );
        $i++;
    }
    return $categories;
}

    public function getProducts($filter = array()){

       $sql = "SELECT p.product_id,
                      p.model, 
                      p.piece_in_set, 
                      p.price,
                      p.weight,
                      p.quantity,
                      p.is_single,
                      p.minimum,
                      p.image,
                      p.date_added, 
                      p.expected_dispatch_date, 
                      p.hsn_code, 
                      pd.description, 
                      pd.set_description, 
                      pd.name 
                FROM ".DB_PREFIX."product p   
                   INNER JOIN ".DB_PREFIX."ms_product mp ON mp.product_id = p.product_id 
                   INNER JOIN ".DB_PREFIX."ms_seller ms ON ms.seller_id = mp.seller_id 
                   INNER JOIN ".DB_PREFIX."product_description pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'    
                WHERE p.stock_status_id = 7
                    AND p.status = 1
                    AND (p.is_single = 1 OR (p.piece_in_set = 1 AND p.minimum = 1))
                    AND p.hsn_code != ''  AND p.hsn_code IS NOT NULL 
                    AND p.is_associate = 0
                    AND (p.franchise_id IS NULL OR p.franchise_id = 0) 
                    AND p.is_archived = 0
                    AND p.quantity > 0
                    AND ms.seller_status = 1                    
                    AND ms.vacation_mode = 0
                    AND ms.nickname != 'WSB_FR'    
                ORDER BY FIELD(p.store_sales, 'JP', 'DL', 'ST', 'KL', 'MU', 'BL', 'NO') ";

        $query =  $this->db->query($sql);
        return $query->rows;
    }

    public function getProductsCount(){
        $sql = "SELECT COUNT(*) as total  
                FROM ".DB_PREFIX."product p   
                   INNER JOIN ".DB_PREFIX."ms_product mp ON mp.product_id = p.product_id 
                   INNER JOIN ".DB_PREFIX."ms_seller ms ON ms.seller_id = mp.seller_id 
                   INNER JOIN ".DB_PREFIX."product_description pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'    
                WHERE p.stock_status_id = 7
                    AND p.status = 1
                    AND (p.is_single = 1 OR (p.piece_in_set = 1 AND p.minimum = 1))
                    AND p.hsn_code != ''  AND p.hsn_code IS NOT NULL 
                    AND p.is_associate = 0
                    AND (p.franchise_id IS NULL OR p.franchise_id = 0) 
                    AND p.is_archived = 0
                    AND p.quantity > 0
                    AND ms.seller_status = 1                    
                    AND ms.vacation_mode = 0
                    AND ms.nickname != 'WSB_FR' " ;
        //echo $sql; die;
        $query =  $this->db->query($sql);
        return $query->row['total'];
    }

    public function getAllCategoriesForProduct($product_id = 0){
        $sql = "SELECT category_id
               FROM oc_product_to_category
               WHERE product_id = $product_id";
        $query =  $this->db->query($sql);
        $array = array();
        foreach ($query->rows as $category){
            if($category['category_id'] != 0){
                $array[] = $category['category_id'];
            }
        }
        return $array;
    }

    public  function getProductFiltersData($product_id){

        $q = "SELECT 
                f.filter_id, 
                GROUP_CONCAT(fd.name SEPARATOR ', ') as filter_name,
                (
                    SELECT 
                        name 
                    FROM 
                        ".DB_PREFIX."filter_group_description fgd 
                    WHERE 
                        fl.filter_group_id = fgd.filter_group_id 
                        AND fgd.language_id = 1
                ) as group_name
              FROM 
                ".DB_PREFIX."product_filter f
              INNER JOIN 
                ".DB_PREFIX."filter_description fd ON f.filter_id = fd.filter_id
              INNER JOIN 
                ".DB_PREFIX."filter fl ON f.filter_id = fl.filter_id
              WHERE 
                f.product_id = ".(int)$product_id."
                 AND fd.language_id =1
              GROUP BY 
                group_name";

        $query = $this->db->query($q);
        return $query->rows;

    }

    public function getProductOptions($product_id) {
        $product_option_data = array();

        $product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po 
                                                  INNER JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) 
                                                  INNER JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) 
                                                  WHERE po.product_id = '" . (int)$product_id . "' 
                                                    AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                                                  ORDER BY o.sort_order");
        $name = array();
        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov 
                                                            INNER JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) 
                                                            INNER JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) 
                                                            WHERE pov.product_id = '" . (int)$product_id . "' 
                                                              AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' 
                                                              AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
            $product_option_value_query_data = $product_option_value_query->rows;
           
            $sort_order = array_column($product_option_value_query_data, 'sort_order');
            array_multisort($sort_order, SORT_ASC, $product_option_value_query_data);

            foreach ($product_option_value_query_data as $product_option_value) {
                $name[] = $product_option_value['name'];
                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'option_value_id'         => $product_option_value['option_value_id'],
                    'name'                    => $product_option_value['name'],
                    'image'                   => $product_option_value['image'],
                    'quantity'                => $product_option_value['quantity'],
                    'subtract'                => $product_option_value['subtract'],
                    'price'                   => $product_option_value['price'],
                    'price_prefix'            => $product_option_value['price_prefix'],
                    'weight'                  => $product_option_value['weight'],
                    'weight_prefix'           => $product_option_value['weight_prefix']
                );
            }
            $name_value = implode(", ", $name);
            $product_option_data[] = array(
                'product_option_id'    => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $product_option['option_id'],
                'name'                 => $product_option['name'],
                'type'                 => $product_option['type'],
                'value'                => $name_value,
                'required'             => $product_option['required']
            );
        }

        return $product_option_data;
    }

    /**
     * @param $product_id
     * @return int
     */
    public function checkStock($model){

        $sql = "SELECT p.quantity, p.stock_status_id, ms.vacation_mode, ms.seller_status
                FROM " . DB_PREFIX . "product p
                INNER JOIN ". DB_PREFIX ."ms_product mp ON (p.product_id = mp.product_id)
                INNER JOIN ". DB_PREFIX ."ms_seller ms ON (mp.seller_id = ms.seller_id) 
                WHERE LOWER(p.model) = '".$this->db->escape(trim(strtolower($model)) )."'";
        $query = $this->db->query($sql);
        if(isset($query->row['stock_status_id']) && $query->row['stock_status_id'] == 7 && isset($query->row['vacation_mode']) && $query->row['vacation_mode'] == 0 && isset($query->row['seller_status']) && $query->row['seller_status'] == 1 ){
            $quantity = $query->row['quantity'];
        }else{
            $quantity = 0;
        }

        return $quantity;
    }

}
