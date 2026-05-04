<?php

declare(strict_types=1);

require_once(DIR_SYSTEM . 'library/nach_bank/nach_bank_base.php');

class YesBankNach extends NachBankBase 
{	
	public function __construct(Registry $registry) {
        parent::__construct($registry);
	}

    /*
     * @info: Public method to generate NACH Sheet for Yes Bank, and return the filepath string.
     * @param $data - array 
     * @return string - filepath
     */
	public function generateNachSheet(array $data) : string {
                
        $filename = DIR_DOWNLOAD.'NACH_DR_' . date('dmY') . '_NACH00000000005291_WholesaleboxInternetPvtLtd_001.csv';

        $fp = fopen($filename, 'w');

        if( !empty($data) ) {
            
            // Creating CSV Header Row
            $head = array(
                        'Lan No',
                        'UMRN',
                        'Amount',
                        'Settlement Date',
                        'User Number'
                      );
            fputcsv( $fp , $head );

            foreach ($data as $value) {
                
                $row = array(
                        $value['lan_no'],
                        $value['umrn_no'],
                        $value['nach_debit_amount'] ?? 0,
                        date('dmY'),
                        'NACH00000000005291'
                       );
                fputcsv( $fp , $row );
            }
        }else{
            $no_data = array('No Data Available.');
            fputcsv( $fp , $no_data );
        }
        fclose($fp);

        return $filename;
    }
	
}//End of Class
