<?php

/*
* This class used for CURL services - get/post requests
* @author MSA July 2019
* @return [type] [<description>]
*/

class Curl {
	
	/**
    * Public static function for CURL get request
    * @param string $url
    * $param string $response_type
    * @author: MSA, July 2019
    */
    public static function get( string $url, string $response_type = 'json' )
    {
        $curl      = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->_url); 

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 

        $result = curl_exec($curl); 

        curl_close($curl); 

        if( $type == 'json') {

            $result = json_encode( $result ) ;    
        }

        return $result;   
    }

    /**
    * Public static function for CURL post request
    * @param string $url
    * @param array $post_data
    * @param string $type
    * $param arrat $headers
    * @author: MSA, July 2019
    */
    public static function post(string $url, array  $post_data, string $type = 'json', array  $headers = array() )
    {
        $headers = self::getHeader( strtolower($type), $headers );

        $curl      = curl_init();
        
        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {

           curl_setopt($curl, CURLOPT_PORT, 443);
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data) );            
        
        $result = curl_exec($curl);

        curl_close($curl);

        if( $type == 'json') {

            $result = json_encode( $result ) ;    
        }
        
        return $result;
    }

    public static function getHeader(string $type, array $headers ): array
    {
        if( $type == 'json' )
        {
            $header = array('Content-type: application/json');
        }

        if( $type == 'xml' )
        {
            $header = array('Content-type: text/xml');
        }

        if( !empty( $headers ) ) {

            $header  = array_merge( $header, $headers );
        }

        return $header;
    }

	/**
    * Public static function to call RBL REST API's using CURL with SSL connection setting
    * @param string $url
    * @param array $post_data
    * @author: MSA, July 2019
    */
    public static function callRblAPI( string $url, array  $post_data )
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . RBL_API_AUTHORIZATION,
                                                     "Content-type: application/json"
                                                    ));
        curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($curl, CURLOPT_SSLCERT, RBL_PEM_FILE_PATH);
        curl_setopt($curl, CURLOPT_SSLCERTPASSWD, RBL_CERT_PASSWORD);

        $result = curl_exec($curl);
        if( curl_error($curl) ) {
            return curl_error($curl); 
        } 
        curl_close($curl);
        return $result;
    }



}
