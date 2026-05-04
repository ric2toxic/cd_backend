<?php
class ModelDictionarySearchDictionary extends Model{

    // adding word with synonyms
    public function addWordSynonyms($data = array()){
        //echo "<pre>"; print_r($data); echo "</pre>";//die;
        if(!empty($data)){
            $sql = "INSERT INTO " . DB_PREFIX . "search_dictionary SET
                word = '" . $this->db->escape($data['word']) . "',
                synonyms = '" . $this->db->escape(html_entity_decode($data['word_synonyms'])) ."'
                ";
             //echo "<pre>"; print_r($sql); echo "</pre>"; die;
            $this->db->query($sql);
        }
    }

    // updating word with synonyms
    public function editWordSynonyms($dictionary_id,$data){
        //echo "<prE>"; print_r($data); echo "</pre>"; die;
        $sql = "UPDATE " . DB_PREFIX . "search_dictionary SET
         word = '" . $this->db->escape($data['word']) . "',
         synonyms = '" . $this->db->escape(html_entity_decode($data['word_synonyms'])) ."'
         WHERE id = '" . (int)$dictionary_id . "'";
        $this->db->query($sql);
    }

    // deleting word with synonyms
    public function deleteWordSynonyms($id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "search_dictionary WHERE id = '" . (int)$id . "'");
    }

    // Total word with synonyms
    public function getTotalWordSynonyms($data = array()){
        $sql = "SELECT COUNT(DISTINCT wd.id) AS total FROM " . DB_PREFIX . "search_dictionary wd WHERE wd.word LIKE '" . $this->db->escape($data['filter_name']) . "%'";// AND wd.status LIKE '" . $this->db->escape($data['filter_status']) . "%'";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    // listing word with synonyms
    public function getWordSynonyms($data = array()){
        //echo "<prE>"; print_r($data); //die;
        //$sql = "SELECT * FROM " . DB_PREFIX . "search_dictionary wd  WHERE pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        $sql = "SELECT * FROM " . DB_PREFIX . "search_dictionary wd WHERE wd.word LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR wd.synonyms LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
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



    public function getWordSynonymsWithID($id){
        //$sql = "SELECT * FROM " . DB_PREFIX . "search_dictionary wd  WHERE pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "search_dictionary wd WHERE id = '" . $this->db->escape($id) . "'";
        $query = $this->db->query($sql);
        return $query->row;

    }

}