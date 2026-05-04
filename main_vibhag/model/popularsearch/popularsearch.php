<?php
class ModelPopularsearchPopularsearch extends Model{

    // adding popular tag
    public function addPopularTag($data = array()){
        if(!empty($data)){
            $sql = "INSERT INTO " . DB_PREFIX . "popular_search SET
                popular_search = '" . $this->db->escape($data['popular_search']) . "',
                link = '" . $this->db->escape($data['popular_link']) . "',
                created = NOW()
                ";
            $this->db->query($sql);
        }
    }

    // updating popular tag
    public function editPopularTag($popular_id,$data){
        $sql = "UPDATE " . DB_PREFIX . "popular_search SET
         popular_search = '" . $this->db->escape($data['popular_search']) . "',
         link = '" . $this->db->escape($data['popular_link']) . "',
         modified = NOW()
         WHERE popular_id = '" . (int)$popular_id . "'";
        $this->db->query($sql);
    }

    // deleting popular tag
    public function deleteWordSynonyms($popular_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "popular_search WHERE popular_id = '" . (int)$popular_id . "'");
    }

    // Total popular tag
    public function getTotalPopularSearch($data = array()){
        $sql = "SELECT COUNT(DISTINCT ps.popular_id) AS total FROM " . DB_PREFIX . "popular_search ps WHERE ps.popular_search LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    // listing popular tag
    public function getPopularTags($data = array()){
        $sql = "SELECT * FROM " . DB_PREFIX . "popular_search ps";
        if(isset($data['filter_name']) && !empty($data['filter_name'])){
            $sql .= " WHERE ps.popular_search LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
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
        //echo "<pre>"; print_r($query); echo "</prE>"; //die;
        return $query->rows;

    }


    public function getPopularTagWithID($popular_id){
        $sql = "SELECT * FROM " . DB_PREFIX . "popular_search ps WHERE popular_id = '" . (int)($popular_id) . "'";
        $query = $this->db->query($sql);
        return $query->row;

    }

}