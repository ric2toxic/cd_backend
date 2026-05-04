<?php

class Controllerrestapilookup extends Controller
{
    public $registry;

    public function __construct($registry)
    {

        parent::__construct($registry);
        $this->registry = $registry;
    }

    /**
     * Get data from pincode
     */
    /**
     * Get data from pincode
     */
    public function pincode() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
            $response = '';
            if (isset($request['pincode']) && $request['pincode'] != '') {
                $solr = new SolrLookup($this);
                $response = $solr->gePincodeData($request['pincode']);


            }

            $this->response->addHeader('Content-Type: application/json;');

            echo json_encode($response);


            exit;
        }
    }
}
