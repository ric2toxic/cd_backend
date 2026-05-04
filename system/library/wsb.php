<?php

class Wsb {

    private $registry = '';

    public function __construct($registry){
        $this->registry = $registry;
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->load = $registry->get('load');

    }
    /**
     * Get calculated price for a store
     * @param $product_id
     * @param $store_id
     * @param $seller_id
     * @return array
     */
    public function getStorePrice($product_id, $price, $store_id, $seller_id){
        $calculated_price = array('price'=>0, 'commission'=>0);

        //Get price from product_to_store
        $sql = 'SELECT store_price FROM '.DB_PREFIX.'product_to_store'.
            ' WHERE store_id = '. $store_id.
            ' AND product_id = '. $product_id.
            ' AND store_price > 0';

        $query = $this->db->query($sql);

        if ($query->num_rows) {
            $calculated_price['price'] = $query->row['store_price'];

            //get WSB commission for store
            $price_markups = $this->getStoreBasedPriceMarkup($store_id, $seller_id);
            if(isset($price_markups['wsb_commission_over_tp']) && $price_markups['wsb_commission_over_tp'] > 0) {
                $calculated_price['commission'] = $price_markups['wsb_commission_over_tp'];
            }

        }else{ //Get price from seller_to_store

            $price_markups = $this->getStoreBasedPriceMarkup($store_id, $seller_id);
            if(is_array($price_markups) && !empty($price_markups)) {

                $factor_store_markup = 1 + ($price_markups['seller_markup_over_tp']/100);
                $calculated_price['price'] = $price * $factor_store_markup;
                if(isset($price_markups['wsb_commission_over_tp']) && $price_markups['wsb_commission_over_tp'] > 0) {
                    $calculated_price['commission'] = $price_markups['wsb_commission_over_tp'];
                }
                //echo '"'.$price.' - '.$factor_store_markup.' - '. $calculated_price['price'].'"';
            }
        }


        return $calculated_price;

    }

    /**
     * @param $store_id
     * @param $seller_id
     * @return int
     */
    public function getStoreBasedPriceMarkup($store_id, $seller_id){
        $sql = 'SELECT seller_markup_over_tp, wsb_commission_over_tp FROM '.DB_PREFIX.'wsb_seller_to_store'.
            ' WHERE store_id = '. $store_id.
            ' AND seller_id = '. $seller_id;
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return $query->row;
        }else{
            return 0;
        }


    }

    /**
     *
     */
    function getMData()
    {
        $detect = new Mobile_Detect_Class();
        if (!isset($_COOKIE['WSBSESSID']) && $detect->isMobile() ) {
            setcookie('WSBSESSID', rand(), time() + (86400 * 300), "/");
            $headers = getallheaders();
            $keys = "x-nokia-msisdn,X-MSISDN,X_MSISDN,HTTP_X_MSISDN,X-UP-CALLING-LINE-ID,X_UP_CALLING_LINE_ID,HTTP_X_UP_CALLING_LINE_ID,X_WAP_NETWORK_CLIENT_MSISDN";

            define('COMMA', ",");
            $keysArr = explode(COMMA, $keys);
            $numberFound = 0;
            foreach ($headers as $headerName => $headerValue) {
                foreach ($keysArr as $key) {
                    if (strtolower($headerName) == strtolower($key)) {

                        $numberFound = 1;

                        $file_path = DIR_DLOAD . 'm-data.csv';
                        if (file_exists($file_path)) {
                            $fp = fopen($file_path, 'a');
                        } else {
                            $fp = fopen($file_path, 'w');

                        }

                        $data = array($headerValue, $this->request->getIpAddress);

                        fputcsv($fp, $data);

                    }
                }
            }

            if ($numberFound == 0) {
                $file_path = DIR_DLOAD . 'm-data-headers.csv';
                if (file_exists($file_path)) {
                    $fp = fopen($file_path, 'a');
                } else {
                    $fp = fopen($file_path, 'w');

                }

                $output = implode(' + ', array_map(
                    function ($v, $k) { return sprintf("%s=%s", $k, $v); },
                    $headers,
                    array_keys($headers)
                ));
                $output_str = '"'.$output.'"';
                $data = array($this->request->getIpAddress, $output_str);


                fputcsv($fp, $data);
            }

        }
    }

}
