<?php
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

/*
* This class used for connecting RBL SFTP server to Read/Write .CSV files
* @author MSA July 2019
* @return [type] [<description>]
*/

class MYSFTP {

    private $_sftp;


	/**
    * Public function to connect RBL server using SFTP connection
    * @author: MSA, July 2019
    */
    public function connect()
    {
        try{

            $rsa = new RSA();

            $rsa->loadKey( file_get_contents( RBL_SFTP_KEY ) );

            $this->_sftp = new SFTP( RBL_SFTP_HOST, RBL_SFTP_PORT );

            $this->_sftp->login( RBL_SFTP_USER, RBL_SFTP_PASSWORD, $rsa);

        }catch(Exception $e) {

            throw new \Exception('SFTP connection failed!!');

        }
    }

	/**
    * Public function to get required resource from RBL server using SFTP connection
    * @param string $source [file name]
    * @author: MSA, July 2019
    */
    public function get(string $source)
    {
        $list = array();

        try {
            
            $this->connect();

            $data = $this->_sftp->get( $source );
            
            if(!empty($data)) {

               $list = $this->format( $data );

            }
            
            return $list;

        }catch(Exception $e){

            throw new \Exception('Failed to read file from SFTP server.');
        }
    }
	
	/**
    * Public function to put[add/append] data to a resource on RBL server using SFTP connection
    * @param string $source [file name]
    * @param array  $data [data to write on server resource]
    * @author: MSA, July 2019
    */
    public function put(string $source, array $data)
    {
        try {
            
            $this->connect();

            //$string = implode(",",$data)."\n" ;

            $string = implode("|",$data)."\n" ;

            $mode = ( $this->_sftp::RESUME | $this->_sftp::RESUME_START );

            return $this->_sftp->put( $source, $string, $mode );

        }catch(Exception $e){

            throw new \Exception('Failed to write on SFTP file');

        }
    }
	
	/**
    * Private function to format RBL server result data
    * @param string $data
    * @author: MSA, July 2019
    */
    private function format(string $data)
    {
        $list = array();

        $data = explode("\n", $data);
        
        if(!empty($data)) {

            foreach ($data as $key => $value) {

                $value = explode(",", $value);

                //$value = explode("|", $value);

                if(!empty($value[0]) && !empty($value[1])) {
                    
                    $list[] = $value;
                }
            }
        }
        
        // remove heading row
        array_shift($list);

        return $list;
    }

}
