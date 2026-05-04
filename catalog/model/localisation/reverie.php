<?php

class ModelLocalisationReverie extends Model {

    public $user_id = "rakesh.shekhawat@wholesalebox.in";
    public $app_id = "rev.web.wsb";
    public $api_key = "VkChkCAfH3ZQKcgTxHvRLMtW5nytAbt5EwpO";

    public function translate($in_array, $language, $domain=4){
        $api_url = "http://demo.reverieinc.com/parabola/transliterateSimpleJSON ";
        $api_info = array( 'inArray' => $in_array,
            'REV-APP-ID'	=> 	$this->app_id,
            'REV-API-KEY' => $this->api_key,
            'domain' => $domain,
            'language' => $language,
            'originLanguage' => 'english',
            'webSdk' => 0
        );

       // echo '<pre>'; print_r($api_info);

        $api_info = json_encode($api_info);

        $curl = curl_init();



        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=UTF-8'));
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $api_info);

        $json = curl_exec($curl);

        $arr_json = json_decode($json);
        if(isset($arr_json->status) && $arr_json->status == 1) {
            return $arr_json;
        }else{
            return false;
        }
    }

    public function localisation($in_array, $language, $domain=6){
        $api_url = "http://demo.reverieinc.com/localization/localizeJSON";
        //echo "<pre>"; print_r($in_array);
        $api_info = array( 'inArray' => $in_array,
            'REV-APP-ID'	=> 	$this->app_id,
            'REV-API-KEY' => $this->api_key,
            'domain' => $domain,
            'targetLanguage' => $language,
            'inputLanguage' => 'english',
            "webSdk"=> 0,
            "tokenize"=> 0

        );

        // echo '<pre>'; print_r($api_info);

        $api_info = json_encode($api_info);

        $curl = curl_init();



        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=UTF-8'));
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $api_info);

        $json = curl_exec($curl);

        $arr_json = json_decode($json);
        //echo '<pre>'; print_r($arr_json);
        if(isset($arr_json->status) && $arr_json->status == 1) {
            return $arr_json;
        }else{
            return false;
        }

    }

    /**
     * This function will be used to update the database with new translated value provided by reverie api
     * @param $translated Array have array received from reverie api
     * @param $language string have language code
     * @param $table string table name which needs to be updated like category+description, product_description, information or filter+description
     * @param $field string Which field of table needs to be updated
     */
    public function update($translated, $language, $table, $field){

        if(isset($translated->outArray) && count($translated->outArray) > 0) {

            $language_id = $this->getLanguageIdByCode($language);
            foreach($translated->outArray as $outArray) {
                $in_string = $outArray->inString;
                $translated_response = $outArray->transResponse;

                $record_ids = $this->getRecordByInString($table, $field, $in_string);
                //Check if translation already exist
                foreach ($record_ids as $record_id){

                    $translation_exist = $this->getLanguageRecordId($table, $record_id, $language_id);

                    if ($translation_exist > 0) {
                        //update the respective field with new translation
                        $this->updateLanguageRecord($table, $record_id, $language_id, $field, $translated_response);
                    } else {
                        //Get the record of English language and insert new with all fields and then update new translated field
                        $this->insertNewLanguageRecord($table, $record_id, $language_id, $field, $translated_response);
                    }

                }
            }
        }



    }

    /**
     * @param $language_code
     * @return language_id
     */

    public function getLanguageIdByCode($language_code){
        $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $language_code . "'");

        if($row = $query->row) {
            return (int)$row['language_id'];
        }else{
            return 0;
        }

    }

    /**
     * @param $table
     * @param $field
     * @param $in_string
     * @return int
     */
    public function getRecordByInString($table, $field, $in_string){

       $return_field = $this->table($table)['id'];
       $sql = "SELECT ". $return_field ." FROM ".DB_PREFIX.$table." WHERE ". $field ." = '".$this->db->escape($in_string)."'";
       $query = $this->db->query($sql);
        if($rows = $query->rows) {
            return  $rows;
        }else{
            return 0;
        }
    }

    public function getLanguageRecordId($table, $record_ids, $language_id){
        foreach($record_ids as $key=>$value){
            $record_id = $value;
        }
        $record_key = $this->table($table)['id'];

        $sql = "SELECT count(".$record_key.") as total FROM ".DB_PREFIX.$table." WHERE ". $record_key ." = '".$record_id."' AND language_id = ".$language_id;
        //echo $sql; die;
        $query = $this->db->query($sql);
        return (int)$query->row['total'];

    }

    /**
     * @param $table
     * @param $record_id
     * @param $language_id
     * @param $field
     * @param $translated_response
     */
    public function updateLanguageRecord($table, $record_ids, $language_id, $field, $translated_response){
        foreach($record_ids as $key=>$value){
            $record_id = $value;
        }

        if($record_id > 0) {
            if($table == 'filter_group_description'){
                $record_key = 'filter_group_id';
            }else{
                $record_key = $this->table($table)['id'];
            }

          echo  $sql = "UPDATE " . DB_PREFIX . $table . "
                SET " . $field . " = '" . $this->db->escape($translated_response) . "'
                WHERE " . $record_key . " = " . $record_id . "
                AND language_id = " . $language_id;

            $this->db->query($sql);

            echo "<br />Record ID: ".$record_id ." has been updated <br /><br />";
        }
    }

    public function insertNewLanguageRecord($table, $record_ids, $language_id, $field, $translated_response){
        foreach($record_ids as $key=>$value){
            $record_id = $value;
        }
        if($record_id > 0) {
            $record_key = $this->table($table)['id'];
            echo "new = ". $sql = "INSERT INTO " . DB_PREFIX . $table . "
                SET " . $field . " = '" .$this->db->escape($translated_response) . "',
                language_id = " . $language_id.",
                 " . $record_key . " = " . $record_id;

            $this->db->query($sql);
            echo "<br />Record ID: ".$record_id ." has been inserted <br /><br />";
        }
    }
    /**
     * @return array
     */
    public function table($table){
        $tables = array(

            "category_description"    => array("id"=>"category_id"),
            "product_description"     => array("id"=>"product_id"),
            "filter_description"      => array("id"=>"filter_id"),
            "information_description" => array("id"=>"information_id"),
        );

        return $tables[$table];

    }

    public function localiseSingleProduct($data = array(), $product_id, $single_field = ''){
        //find languages form language model except base language english
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();
            
            //loop for languages
            foreach ($languages as $language) {
            
                if ($language['code'] != 'en') {
                    $language_code = $language['code']; //that will come from loop
                    //echo $language_code;
                    $language_id = $language['language_id']; //get it from loop
                    $language_id_for_english = 1;
                    $request = $this->convertInReveireRequestFormat($data[$language_id_for_english]); //1 is language id for english
                    
                    $in_string = array_values($request);//array of values
                    
                    //call localistaio
                    if(empty($single_field)){
                        $translated = $this->localisation($in_string, $language_code);
                        
                        //Find key from $request by returned in_string value
                        $resulted_string = array();
                        foreach ($translated->outArray as $value) {
                            $key = array_search($value->inString, $request);
                            $resulted_string[$key] = $value->response;
                        }
                        $table = 'product_description';
                        $record_ids = array('product_id' => $product_id);

                        $translation_exist = $this->getLanguageRecordId($table, $record_ids, $language_id);
                        if ($translation_exist > 0) {
                            //update the respective field with new translation
                            $this->updateProduct($product_id, $language_id, $resulted_string);
                        } else {
                            //Get the record of English language and insert new with all fields and then update new translated field
                            $this->insertProduct($product_id, $language_id, $resulted_string);
                        }
                    }else{
                        $translated = $this->localisation($data, $language_code);
                        $translated_data = '';
                        foreach($translated->outArray as $result){
                            $translated_data = $result->response;
                        }
                        $record_ids = array('product_id' => $product_id);
                        $table = 'product_description';
                        $this->updateLanguageRecord($table, $record_ids, $language_id, $single_field, $translated_data);
                    }

                }
            }
    }

    public function convertInReveireRequestFormat($data){
        //make a regular expression to remove html tags from description
        $des = strip_tags(html_entity_decode($data['description']));
       //    echo $des; die;
        $request_array = array(
            'name' => $data['name'],
            'set_description' => $data['set_description'],
            'description' => $des
        );
        $request = array_filter($request_array);
        return $request;
    }

    public function insertProduct($product_id, $language_id, $resulted_string){
        if(isset($resulted_string['set_description'])){
            $set_description = $resulted_string['set_description'];
        }else{
            $set_description = '';
        }

        if(isset($resulted_string['description'])){
            $description = $resulted_string['description'];
        }else{
            $description = '';
        }

        if($product_id > 0) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . $language_id . "',name = '" . $this->db->escape($resulted_string['name']) . "', set_description = '" . $this->db->escape($set_description) . "', description = '" . $this->db->escape($description). "'");
        }
    }

    public function updateProduct($product_id, $language_id, $resulted_string){
        if(isset($resulted_string['set_description'])){
            $set_description = $resulted_string['set_description'];
        }else{
            $set_description = '';
        }

        if(isset($resulted_string['description'])){
            $description = $resulted_string['description'];
        }else{
            $description = '';
        }
        // also remove validation in backend where product is added
        if($product_id > 0) {
            //echo "UPDATE " . DB_PREFIX . "product_description SET name = '" . $resulted_string['name'] . "', set_description = '" . $resulted_string['set_description'] . "', description = '" . $this->db->escape($resulted_string['description']). "' WHERE product_id  = '" . (int)$product_id . "' AND language_id = '" . $language_id . "'"; die;
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($resulted_string['name']) . "', set_description = '" . $this->db->escape($set_description) . "', description = '" . $this->db->escape($description). "' WHERE product_id  = '" . (int)$product_id . "' AND language_id = '" . $language_id . "'");
        }
    }
    /**
    * This code is for localising categories when added or edited
    */
    public function localizeSingleCategory($data = array(), $category_id){
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language) {
            if ($language['code'] != 'en') {
                $language_code = $language['code'];
                $language_id = $language['language_id'];
                $language_id_for_english = 1;
                $request = $this->convertCategoryInReveireRequestFormat($data[$language_id_for_english]); //1 is language id for english
                $in_string = array_values($request);//array of values
                    $translated = $this->localisation($in_string, $language_code);

                    $resulted_string = array();
                   if(isset($translated->outArray))
                   { 
                    foreach ($translated->outArray as $value) {
                        $key = array_search($value->inString, $request);
                        $resulted_string[$key] = $value->response;
                    }
                   } 
                    //echo "<pre>"; print_r($resulted_string); die;
                    $table = 'category_description';
                    $record_ids = array('category_id' => $category_id);
                    $translation_exist = $this->getLanguageRecordId($table, $record_ids, $language_id);
                    if ($translation_exist > 0) {
                        $this->updateCategory($category_id, $language_id, $resulted_string);
                    } else {
                        $this->insertCategory($category_id, $language_id, $resulted_string);
                    }
            }
        }
    }
    public function convertCategoryInReveireRequestFormat($data){
        //make a regular expression to remove html tags from description
        $des = strip_tags(html_entity_decode($data['description']));
        //    echo $des; die;
        $request = array(
            'name' => $data['name'],
            'description' => $des
        );
        return $request;
    }

    public function updateCategory($category_id, $language_id, $resulted_string){
        if($category_id > 0) {
            //echo "UPDATE " . DB_PREFIX . "category_description SET name = '" . $resulted_string['name'] . "', description = '" . $this->db->escape($resulted_string['description']). "' WHERE category_id  = '" . (int)$category_id . "' AND language_id = '" . $language_id . "'"; die;
           if(isset($resulted_string['name']) && $resulted_string['description']) 
           { 
            $this->db->query("UPDATE " . DB_PREFIX . "category_description SET name = '" . $this->db->escape($resulted_string['name']) . "', description = '" . $this->db->escape($resulted_string['description']). "' WHERE category_id  = '" . (int)$category_id . "' AND language_id = '" . $language_id . "'");
           } 
        }
    }

    public function insertCategory($category_id, $language_id, $resulted_string){
        if($category_id > 0) {
            //echo "INSERT INTO " . DB_PREFIX . "product_description SET name = '" . $resulted_string['name'] . "', set_description = '" . $resulted_string['set_description'] . "', set_description = '" . $resulted_string['description']. "'"; die;
            $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET language_id = '" . $language_id . "',name = '" . $this->db->escape($resulted_string['name']) . "', description = '" . $this->db->escape($resulted_string['description']). "'");
        }
    }
    /*
     * end of localising category code
     * */

    public function localizeFilters($data, $filter_group_id, $filter_ids = array()){
        //echo "<pre>"; print_r($data); die;
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language) {
            if ($language['code'] != 'en') {
                $language_code = $language['code'];
                $language_id = $language['language_id'];
                $language_id_for_english = 1;
                $request = $data['filter_group_description'][$language_id_for_english]; //1 is language id for english
                //echo "<pre>"; print_r($request); die;
                $in_string = array_values($request);//array of values
                $translated = $this->localisation($in_string, $language_code);
                //echo "<pre>"; print_r($translated); die;
                $resulted_string = '';
                foreach ($translated->outArray as $value) {
                    $resulted_string = $value->response;
                }
                //echo "<pre>"; print_r($resulted_string); die;
                $table = 'filter_group_description';
                $record_ids = array('filter_group_id' => $filter_group_id);
                $field = 'name';
                $this->updateLanguageRecord($table, $record_ids, $language_id, $field, $resulted_string);
                // for filter
                //echo "<pre>"; print_r($filter_ids); die;
                if(!empty($filter_ids)){
                    $filter_loop = $filter_ids;
                }else{
                    $filter_loop = $data['filter'];
                }
                foreach ($filter_loop as $filter){
                    $filter_id = $filter['filter_id'];
                    $in_string_filter = array_values($filter['filter_description'][1]);
                    $translated_filter = $this->localisation($in_string_filter, $language_code);
                    $resulted_string_filter = '';
                    foreach ($translated_filter->outArray as $value_filter) {
                        $resulted_string_filter = $value_filter->response;
                    }
                    $table_filter = 'filter_description';
                    $this->updateLanguageRecordFilter($table_filter, $record_ids, $filter_id, $language_id, $field, $resulted_string_filter);

                }


            }
        }
    }
    public function updateLanguageRecordFilter($table, $record_ids, $filter_id, $language_id, $field, $translated_response){
        foreach($record_ids as $key=>$value){
            $record_id = $value;
        }

        if($record_id > 0) {
            $record_key = 'filter_group_id';
            $sql = "UPDATE " . DB_PREFIX . $table . "
                SET " . $field . " = '" . $this->db->escape($translated_response) . "'
                WHERE " . $record_key . " = " . $record_id . "
                AND language_id = " . $language_id . " AND filter_id =" . $filter_id;

            $this->db->query($sql);

            //echo "<br />Record ID: ".$record_id ." has been updated <br /><br />";
        }
    }

    public function translateInformation($data, $info_id){
        $in_array = array(
            0 => $data['title']
        );
        $record_id = array(
            'record_id' => $info_id
        );
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language) {
            if ($language['code'] != 'en') {
                $language_code = $language['code']; //that will come from loop
                $language_id = $language['language_id']; //get it from loop
                $out_array = $this->translate($in_array, $language_code, 4);
                foreach($out_array->outArray as $result){
                    $translated_data = $result->transResponse;
                }
                //echo "<pre>"; print_r($out_array); die;
                $this->updateLanguageRecord('information_description', $record_id, $language_id, 'title', $translated_data);
            }
        }
    }
}
