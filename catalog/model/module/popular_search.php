<?php
class ModelModulePopularSearch extends Model {

    // get popular tag and using in footer by vikas(09-07-2016)
    public function getPopularTags(){
        $query = $this->db->query("SELECT popular_search, link FROM " . DB_PREFIX . "popular_search");
        return $query->rows;
    }

}