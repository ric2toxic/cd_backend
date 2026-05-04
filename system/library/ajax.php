<?php

class ajax {

    protected $config   = array();

    public function __construct($config = NULL) {
        if ( ! empty($config)) {
            $this->initialize($config);
        }

    }
    /**
     * Get calculated price for a store
     * @param $product_id
     * @param $store_id
     * @param $seller_id
     * @return array
     */

    public function HTTP_ORIGIN()
    {
      if (isset($_SERVER['HTTP_ORIGIN'])) {
       header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
       header('Access-Control-Allow-Credentials: true');
       header('Access-Control-Max-Age: 86400');    // cache for 1 day
      }    // Access-Control headers are received during OPTIONS requests
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
           header("Access-Control-Allow-Methods: GET, POST, OPTIONS");                
       if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
           header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");        
           exit(0);
        }
    }


}
