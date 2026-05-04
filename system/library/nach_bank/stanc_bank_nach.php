<?php

declare(strict_types=1);

require_once(DIR_SYSTEM . 'library/nach_bank/nach_bank_base.php');

class StancBankNach extends NachBankBase 
{
	public function __construct(Registry $registry) {
        parent::__construct($registry);
	}

    /*
     * @info: Public method to generate NACH Sheet for Stanc Bank, and return the filepath string.
     * @param $data - array 
     * @return string - filepath
     */
	public function generateNachSheet(array $data) : string {
        
    	$filename = DIR_DOWNLOAD.'MIS-NACHDROW_NACH00000000005291_' . date('dmY') . '.csv';

        $fp = fopen($filename, 'w');

        if( !empty($data) ) {
            
            $total_amount = array_sum(array_column($data, 'nach_debit_amount'));
            
            // Creating CSV Header Row
            $head = array(
                        '56',
                        '',
                        'Wholesalebox Internet Private Limited',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $total_amount,
                        date('dmY'),
                        '',
                        '',
                        '',
                        'NACH00000000005291',
                        'NACH WSB Credit',
                        'SCBL0036001',
                        '75105102107',
                        count($data),
                        '',
                        '',
                        '',
                        ''
                      );
            fputcsv( $fp , $head );

            foreach ($data as $value) {
                
                $row = array(
                        '67',
                        '',
                        '',
                        '',
                        '',
                        $value['account_name'],
                        '',
                        '',
                        'Wholesalebox Internet Private Limited',
                        '',
                        $value['nach_debit_amount'] ?? 0,
                        '',
                        '',
                        '',
                        '',
                        $value['ifsc_code'],
                        "'".$value['account_no'],
                        'SCBL0036001',
                        'NACH00000000005291',
                        $value['customer_id'].': Reference',
                        '10',
                        '',
                        $value['umrn_no']
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
