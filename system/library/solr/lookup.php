<?php
class SolrLookup extends Model
{

    protected $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $pincode string
     */
    public function gePincodeData($pincode) {

        $config_solr = $this->registry->config->solrConfig(SOLR_PATH_PINCODES);

        // create a client instance
        $client = new Solarium\Client($config_solr);

        // get a select query instance
        $query = $client->createSelect();

        $sql = "pincode:" . (int) $pincode;
        $query->setQuery($sql);


        // $query->setFields(array('id', 'user_query'));

        $query->setStart(0, 10);


        // this executes the query and returns the result
        $resultset = $client->select($query);

        $response = array();
        $i = 0;
        foreach ($resultset as $document) {
            $response[$i]['pincode'] = $document->pincode;
            $response[$i]['taluka'] = $document->taluka;
            $response[$i]['city'] = $document->city;
            $response[$i]['zone_id'] = $document->zone_id;
            $response[$i]['zone'] = $document->zone;
            $response[$i]['country_id'] = $document->country_id;
            $response[$i]['country'] = $document->country;

            $i++;
        }

       return $response;
    }
}