<?php

class model_solr_product extends Model{

	protected $registry; 
    protected $db;
    protected $load;

    public function __construct($registry) {
        
        $this->registry = $registry;
        $this->db       = $registry->db;
        $this->load     = $registry->load; 

    }


    public function getAllFilterGroupsFromDB() { 

        $sql = "SELECT fg.filter_group_id, fgd.`name`, fgd.description
                FROM ". DB_PREFIX ."filter_group_description fgd
                INNER JOIN " .DB_PREFIX. "filter_group fg
                ON fg.filter_group_id = fgd.filter_group_id
                WHERE language_id = 1 
                ORDER BY sort_order ASC";

        $query = $this->db->query($sql);

        return $query->rows;

    }

    public function getDictionarySynonyms($word) {
        
        $synonyms = array();
        
        $word = trim($word);

        if ( !empty($word) ) {
            array_push($synonyms, $word);
            $fts_word = getFullTextSearchString( $word );
            
            $sql = " SELECT word, synonyms FROM " . DB_PREFIX. "search_dictionary 
                     WHERE MATCH (synonyms,word)
                     AGAINST ('" .$this->db->escape($fts_word)."' IN BOOLEAN MODE)";
            $query = $this->db->query($sql);    
        
            foreach ($query->rows as $match) {
                array_push( $synonyms, $match['word'] );
                $synonyms = array_merge( $synonyms, explode(',', trim($match['synonyms'])) );
            }
        }
        
        // Sanitizing synonyms; removing empty strings after trimming, removing duplicates
        $synonyms = array_unique(array_filter(array_map('trim', $synonyms)));

        return $synonyms;
    }

    public function setSearchedKeyword($keyword, $has_results = 0){

        return true;
	}

    public function getSellersWithCity($cart_seller_city_code) {

        $city_code_list = implode("','", $cart_seller_city_code);
        
        $sql = "SELECT seller_id
                FROM ".DB_PREFIX."ms_seller
                WHERE pickup_city_code IN ('".$city_code_list."')";

        $query = $this->db->query($sql);   

        if($query->num_rows){
            return $query->rows;
        }
        return false;
    }

    public function getWSBStoreSellerId($city_code) {

        $pickup_city_map = array(
            "JP" => "JAIPUR",
            "DL" => "NEW DELHI,DELHI",
            "ST" => "SURAT",
            "BLR" => "BANGALORE",
            "BL" => "BANGALORE",
            "KL" => "KOLKATA",
            "MU" => "MUMBAI",
            "MB" => "MUMBAI"
        );

        foreach ($city_code as $key => $value) {
			if(!empty($pickup_city_map[strtoupper($value)])) {
				$city_by_map = $pickup_city_map[strtoupper($value)];
			} else {
				continue;
			}
            $city_exploded = explode(',', $city_by_map);
            if(count($city_exploded) > 1) {
                foreach ($city_exploded as $key => $value) {
                    $city[] = $value;
                }
            } else {
                $city[] = $city_by_map;
            }
        }
		if(!empty($city)) {
			$city_list = implode("','", array_unique($city));

	        $sql = "SELECT DISTINCT (s.seller_id)
	                FROM ".DB_PREFIX."ms_seller AS s
	                INNER JOIN oc_vat_input_rules AS v on v.purchase_firm_id = s.purchase_firm_id
	                WHERE v.purchase_firm_city IN ('".$city_list."')
	                AND v.status = 1 AND s.purchase_firm_id > 0";
	                
	        $query = $this->db->query($sql);
	        if($query->num_rows){
	            return $query->rows;
	        }
		}
        return false;

    }

    public function getSimilarSellers($sellers, $categories) : array {

        // Assuming that input param $sellers can be int, string of comma separated int(s), or an array
        $sids = !is_array($sellers) ? explode(',', (string)$sellers) : $sellers;
        $sids = array_map(function($v){return (int)trim($v);}, $sids);
        $sids = array_unique(array_filter($sids, function($v){return $v > 0;}));

        // If no sellers left after sanitization, return empty array
        if (empty($sids)) {return array(); }

         // Assuming that input param $categories can be int, string of comma separated int(s), or an array
        $cids = !is_array($categories) ? explode(',', (string)$categories) : $categories;
        $cids = array_map(function($v){return (int)trim($v);}, $cids);
        $cids = array_unique(array_filter($cids, function($v){return $v > 0;}));
        $category_condition = "";
        if ( !empty($cids) ) {
            $category_condition = " AND ssc.category_id IN (" . implode(',', $cids) . ")";
        }

        $sql = "
                SELECT 
                    GROUP_CONCAT(DISTINCT cs2.seller_id) as similar_sellers
                FROM
                    similar_seller_cluster_sellers as cs1
                JOIN 
                    similar_seller_cluster ssc ON ssc.similar_seller_cluster_id = cs1.similar_seller_cluster_id
                        ".$category_condition."
                JOIN
                    similar_seller_cluster_sellers as cs2 ON cs2.similar_seller_cluster_id = cs1.similar_seller_cluster_id
                WHERE
                    cs1.seller_id IN (" . implode(',', $sids) . ")
                    AND
                    cs2.seller_id NOT IN(" . implode(',', $sids) . ")
                ";
        $query = $this->db->query($sql);
        return $query->rows;        

     }

    protected function getRequestHeaders()
    {
       return $this->header;
    }
		
}

?>