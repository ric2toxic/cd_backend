<?php
class SolrOrder{

    private $_registry;

    public function __construct( $registry ){
        $this->_registry = $registry;
    }

    public function getProductFromSolr($filters){
        $config_solr = $this->_registry->config->solrConfig(SOLR_PATH_ORDER);
        // create a client instance
        $client = new Solarium\Client($config_solr);
        $sql = array();

        foreach ( $filters as $key => $value) {
            $sql[] = $key.':*'.$value.'*';
        }

        $sql_and = implode('&',$sql);
        if(empty($filters)){
            $sql_and = "*:*";
        }
        //Make string for faceting
        // $facet_json = $this->_getJsoNFacetStringForSolrRequest();
        // $customizer = $client->getPlugin('customizerequest');
        //
        // // add a GET param thats only used for a single request (the default setting is no persistence)
        // $customizer->createCustomization('json.facet')
        //     ->setType('param')
        //     ->setName('json.facet')
        //     ->setValue($facet_json);

        // get a select query instance
        $query = $client->createSelect();
        $query->setQuery($sql_and);

        $solr_result = $client->select($query);

        $response = $solr_result->getData();

        $return = array(
            'solr_result' => $solr_result,
            'sql_and'     => $sql_and,
            //'facets'      => $arr_response['facets'],
            'response'    => $response
        );

        return $return;
    }

    public function addSolrOrder($data){
        if(!empty($data['id']) ){
            $config_solr = $this->_registry->config->solrConfig(SOLR_PATH_ORDER);
            // create a client instance
            $client = new Solarium\Client($config_solr);
            // get an update query instance
            $update = $client->createUpdate();
            // create a new document for the data
            // please note that any type of validation is missing in this example to keep it simple!
            $doc = $update->createDocument();

            foreach ( $data as $key => $value) {
                $doc->{$key} = $value;
            }

            $update->addDocument($doc);
            $update->addCommit();

            // this executes the query and returns the result
            $result = $client->update($update);
        }

    }

    public function atomicUpdateToSolr($data){
        //echo '<pre>';print_r($data); die;
        if(!empty($this->_registry->config)){
            $solr_config = $this->_registry->config->solrConfig( SOLR_PATH_ORDER );
        }
        else{
            $solr_config = $this->_registry->solr_config;
        }
        // create a client instance
        $client = new Solarium\Client($solr_config);

        // get an update query instance
        $update = $client->createUpdate();

        $doc = $update->createDocument();
        $doc->setKey('id', $data['order_id']);
        foreach($data['fields'] as $key => $val){

            $doc->addField($key, $val);
            $doc->setFieldModifier($key, 'set');
        }

        $update->addDocument($doc)->addCommit();
        // this executes the query and returns the result
        $result = $client->update($update);
        if($result){
            return true;
        }
    }

}

?>
