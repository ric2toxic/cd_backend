<?php

/**
 * Convert curreny value (number) to string in Indian format (Lakhs and Crores).
 * @param float $amount
 * @param string $curr
 * @return string
 */
function convert_to_currency_indian_format($amount, $curr = 'INR') {

    $number = round((float)$amount, 2);

    if ($number === 0.0) {
        return "Zero " . (($curr != 'INR') ? "Dollars " : "Rupees ") . " Only ";
    }

    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen(strval($no));
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? 'and ' : null;
            $str [] = ($number < 21) ? $words[strval($number)] .
                    " " . $digits[$counter] . $plural . " " . $hundred :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
        } else
            $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);

    $points = ($point) ?
            $words[10 * (int) ($point / 10)] . " " .
            $words[$point = $point % 10] : '';

    if (!empty($points)) {
        return ucwords($result) . (($curr != 'INR') ? "Dollars " : "Rupees ") . ucwords($points) . (($curr != 'INR') ? " Cents Only " : " Paise Only");
    } else {
        return ucwords($result) . (($curr != 'INR') ? "Dollars Only" : "Rupees Only ");
    }
}

//Function to get two date difference.
//////////////////////////////////////////////////////////////////////
//PARA: Date Should In YYYY-MM-DD Format
//RESULT FORMAT:
// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
// '%m Month %d Day'                                            =>  3 Month 14 Day
// '%d Day %h Hours'                                            =>  14 Day 11 Hours
// '%d Day'                                                        =>  14 Days
// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
// '%h Hours                                                    =>  11 Hours
// '%a Days                                                        =>  468 Days
//////////////////////////////////////////////////////////////////////
function dateDifference($date_1, $date_2, $differenceFormat = '%a') {
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);
}

// Returns key-value pair for Quarters
// Use the key as value in html
// Use the value as display text in html
function getQuarters() {
    return array('Q1' => '01 April TO 30 June',
        'Q2' => '01 July TO 30 September',
        'Q3' => '01 October TO 31 December',
        'Q4' => '01 January TO 31 March'
    );
}

// It will return an array of Financial Years between a given year range
// $begining - has to be int for the year. Defaults to 2015
// $end      - has to be int for the end financial year. Defaults to 0 - meaning current time
function getFinancialYears($beginning = 2015, $end = 0) {

    if (!$end) {
        // Get current year and month
        $current_year = (int) date('Y');
        $current_month = (int) date('m');

        if ($current_month <= 3) {
            $current_year -= 1;
        }

        $end = $current_year;
    }

    $financial_years = array();

    // Preparing years array
    for ($i = $beginning; $i <= $end; $i++) {

        $financial_years[] = $i . '-' . str_pad((float) substr($i, -2) + 1, 2, '0', STR_PAD_LEFT);
    }

    return $financial_years;
}


// It will return an array of Financial Years between a given year range
// $begining - has to be int for the year. Defaults to 2015
// $end      - has to be int for the end financial year. Defaults to 0 - meaning current time
function getCurrentFinancialYearDates() {


    $financial_year_dates = [];

    $pst = date('m');

    if($pst>=4) {
        $y=date('Y');
        $dtt=$y."-04-01";

        $financial_year_dates['start_date'] = $dtt;
        $pt = date('Y', strtotime('+1 year'));

        $ptt=$pt."-03-31";
        $financial_year_dates['end_date'] = $ptt;
    } else {
        $y=date('Y', strtotime('-1 year'));

        $dtt=$y."-04-01";
        $financial_year_dates['start_date'] = $dtt;

        $pt =date('Y');

        $ptt=$pt."-03-31";
        $financial_year_dates['end_date'] = $ptt;
    }

    return $financial_year_dates;
}

// This function returns an array for start date and end date,
// given the quarter and the financial year
// Accepted values for quarter is 'Q1', 'Q2', 'Q3', or, 'Q4'
// Accepted value for financial year is of format yyyy-yy
// Returns an array with key 'start_date' and 'end_date'
// Return date is of the format yyyy-mm-dd
function getDateRangeForQtrFY($quarter, $financial_year) {

    // Basic input date sanitization
    $quarter = strtoupper(trim($quarter));
    $financial_year = trim($financial_year);

    $start_date = '';
    $end_date = '';
    $yyyy = '';

    if (in_array($quarter, array('Q1', 'Q2', 'Q3'))) {

        $yyyy = substr($financial_year, 0, 4);

        switch ($quarter) {

            case 'Q1':

                $start_date = '04-01';
                $end_date = '06-30';
                break;

            case 'Q2':

                $start_date = '07-01';
                $end_date = '09-30';
                break;

            case 'Q3':

                $start_date = '10-01';
                $end_date = '12-31';
                break;
        }
    } elseif ($quarter == 'Q4') {

        $yyyy = substr($financial_year, 0, 2) . substr($financial_year, -2);
        $start_date = '01-01';
        $end_date = '03-31';
    }

    $start_date = $yyyy . '-' . $start_date;
    $end_date = $yyyy . '-' . $end_date;

    return array('start_date' => $start_date,
        'end_date' => $end_date);
}

/**
 * Perform GSTIN (GST number) validation, based on format and checksum.
 *
 * @param string $gst_no GST Number
 * @param bool $get_pancard_no Set to true, if you want to get pancard number from GST provisional id. Defaults to false
 * @return string|bool
 * @author vikas, 2017
 */
function validateGSTNo($gst_no, $get_pancard_no = false) {
    $regex_for_gst = '/^[0-9]{2}[A-Z]{3}[C,P,H,F,A,T,B,L,J,G,E]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}?$/';
    $flag = true;
    if ($gst_no !== "") {
        if (!preg_match($regex_for_gst, $gst_no)) {
            $flag = false;
        }
    }
    if ($flag) {
        if ($get_pancard_no) {
            $pancard_no = substr($gst_no, 2, 10);
            return $pancard_no;
        } else {
            return $flag;
        }
    } else {
        return false;
    }
}

/*
 * Function to create a Zip file and download it. It takes a list of files,
 * and zip them into a single file (with given name).
 *
 * If the list of files contain only one file, and the file is a local file,
 * then instead of zipping it, it would simply download that one file.
 *
 * @note: This function creates the zip file for temporary purpose in DIR_DOWNLOAD directory.
 * @note: This function would (try to) cleanup the .zip file and the source files, once download is complete. Please
 *        check $cleanup_source and $cleanup_destination input parameters.
 *
 * @param: $source - Array - List of files to zip
 *
 * @param: $destination - String - Name of the zip file. If the given name is empty, a unique name is generated and used.
 *                                 If the name does not contain .zip extension, it will be added as well.
 *                                 Only filename (basename, without extension) is considered in this string.
 *                                 Defaults to empty string.
 *
 * @param: $overwrite - Bool - If true, it will overwrite the file, if the given name file already exists.
 *                             If false, if the given name file already exists, it will generate unique name and Create the file.
 *                             Defaults to True.
 *
 * @param: $cleanup_source - Bool - If true, cleans up the $source files used to create zip, at the end.
 *                                  Defaults to True.
 *
 * @param: $cleanup_destination - Bool - If true, tries to clean up the zip file, after the download operation.
 *                                       Defaults to True.
 *
 * @return: FALSE if there is some error in creation of zip file; OR no valid files provided to zip.
 *          Else, on successful download, it exits the script execution.
 */

function createAndDownloadZip(array $source,
                              string $destination = '',
                              bool $overwrite = true,
                              bool $cleanup_source = true,
                              bool $cleanup_destination = true) {
                                  
    $files_to_zip = $files_to_unlink = $file_contents_to_zip = array();

    // Read files accordingly
    foreach ($source as $file) {
        // if Local file exists
        if ( file_exists($file) ) {
            $files_to_zip[basename($file)] = $file;
            if ($cleanup_source) // If cleanup of source files is enabled
                $files_to_unlink[] = $file;
                
        } elseif ( ($file_content = @file_get_contents($file)) !== false ) {
            // if Remote file exists
            $file_contents_to_zip[basename($file)] = $file_content;
        }
    }

    // If there is even a single file_content to zip OR there are more than one file, we create zip
    if ( count($file_contents_to_zip) > 0 || count($files_to_zip) > 1 ) {
        
        // Sanitizing zip file name. Zip file will always be created in DIR_DOWNLOAD directory
        $zip_filename = trim(pathinfo($destination, PATHINFO_FILENAME));
        $file_to_download = DIR_DOWNLOAD . $zip_filename . '.zip';
        
        // If the zip_filename is empty OR overwrite is set to false, and zip file already exists at the determined path
        if ( empty($zip_filename) || (file_exists($file_to_download) && $overwrite === false) ) {
            
            // In this case, we generate a Unique zip file name instead
            while (true) {
                $file_to_download = DIR_DOWNLOAD . uniqid('wsb_') . '.zip';
                if ( !file_exists($file_to_download) ) break;
            }
        }
        
        //create the archive
        $zip = new ZipArchive();
        if ($zip->open($file_to_download, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== true) return false;
        
        // Add files to zip
        foreach ($files_to_zip as $bname => $f) 
            $zip->addFile($f, $bname);
            
        // Add file contents (string) to zip
        foreach ($file_contents_to_zip as $bname => $s)
            $zip->addFromString($bname, $s);
            
        $zip->close();
            
    } elseif ( count($files_to_zip) === 1 ) {  
        $file_to_download = reset($files_to_zip);
    } else {
        return false;
    }
    
    // Set headers and download the file
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');            
    header('Content-Length: ' . filesize($file_to_download));
    readfile($file_to_download);
    
    if ( $cleanup_destination ) // If cleanup of destination files is enabled
        $files_to_unlink[] = $file_to_download;
    
    // Cleanup
    foreach ($files_to_unlink as $fu)
        @unlink($fu);
        
    exit(); // End the execution here, to avoid inavdvertent writing
}

function validateBankIFSC($ifsc_code, $return = "echo") {
    $json = array();

    $json = getBankAddressByIFSC($ifsc_code);
    if ($return == 'echo') {
        echo $json;
    } else {
        return $json;
    }
    exit;
}


function getBankAddressByIFSC($ifsc_code) {
    $json = array();
    if (!empty($ifsc_code)) {
        $link = "https://ifsc.razorpay.com/" . $ifsc_code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $json = curl_exec($ch);
        curl_close($ch);
    }
    $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);
    
    return $json;
}

/**
 * Method to print Canceled pdf.
 *
 * @param $html
 * @param $pdf_data_arr
 * @return string
 */
function downloadCancelledPdf($html, $pdf_data_arr) {

    require_once(DIR_SYSTEM . 'library/tcpdf/mytcpdf.php');
    $pdf = new MYTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(PDF_AUTHOR);
    $pdf->SetTitle($pdf_data_arr['title']);
    $pdf->SetSubject($pdf_data_arr['subject']);
    $pdf->SetKeywords($pdf_data_arr['keywords']);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    //$pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);




    // ---------------------------------------------------------
    // set font
    $pdf->SetFont('times', 'B', 8);

    //$font_file_path =  DIR_BASE.'/vendor/tecnickcom/tcpdf/fonts/DejaVuSans.ttf';
    //$fontname = TCPDF_FONTS::addTTFfont($font_file_path, 'TrueTypeUnicode', '', 32);
    //$pdf->SetFont($fontname, '', 8, '', 'false');

    // add a page
    $pdf->AddPage();
    $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

    $pdf->writeHTML($html, true, false, false, false, '');

    // -----------------------------------------------------------------------------
    // Table with rowspans and THEAD
    // -----------------------------------------------------------------------------

    if (!file_exists($pdf_data_arr['download_path'])) {
        mkdir($pdf_data_arr['download_path'], 0777, true);
    }

    //Close and output PDF document
    $action = 'F';
    $pdf->Output($pdf_data_arr['download_path'] . $pdf_data_arr['pdf_name'].".pdf", $action);
    return base64_encode($pdf_data_arr['pdf_name'].".pdf");
}


/**
 * Method to generate query string
 * @param $field_name
 * @param Database\DB $db Database connection object
 * @param $input
 * @param $operator
 * @return string query i.e.  p.model LIKE '%ABT%' AND p.model LIKE '%PTS%'
 * @author vikas, 2017
 */
function queryString($field_name, Database\DB $db, $input, $operator){

    $sql = '';
    if(!empty($input)){
        $sql .=  " " . $operator . " " ;
        $sql .= " ( ";
        $input = explode(';', $input);

        foreach($input as $string_value){
            if(empty($string_value)){
                continue;
            }

            $sql .=  " ". $field_name . " LIKE '%" . $db->escape(trim($string_value)) . "%' ";
            $sql .=  " " . $operator ;
        }
        $sql = rtrim($sql,$operator);
        $sql .= " ) ";
    }

    return $sql;
}

/**
 * Method to generate query string for integer.
 * @param $field_name
 * @param Database\DB $db Database connection object
 * @param $from
 * @param $to
 * @param $operator
 * @return string query i.e.  p.model LIKE '%001%' AND p.model LIKE '%003%'
 * @author vikas, 2017
 */
function queryInteger($field_name, Database\DB $db, $from, $to, $operator){
    $max_characters = max(strlen($from), strlen($to));

    $sql = '';
    if(!empty($from) && !empty($to) && !empty($operator)){
        $sql .= " ". $operator ." " ;
        $sql .= " ( ";
        for( $i = (int)$from; $i <= (int)$to; $i++){

            $sql .=  $field_name . " LIKE '%" . $db->escape(trim(str_pad( "$i" , $max_characters, "0", STR_PAD_LEFT))) . "%'";
            if( $i < (int)$to) {
                $sql .= " OR ";
            }
        }
        $sql .= " ) ";
    }

    return $sql;
}


/**
 * Method to generate query string for SOLR search.
 * @param $field_name
 * @param $input
 * @param $operator
 * @return string query i.e.  p.model : '*ABT*' AND p.model : '*PTS*'
 * @author vikas, 2017
 */
function queryStringForSolr($field_name, $input, $operator){

    $sql = '';
    if(!empty($input)){
        $sql .=  " " . $operator . " " ;
        $sql .= " ( ";
        $input = explode(';', $input);

        foreach($input as $string_value){
            if(empty($string_value)){
                continue;
            }
            $sql .=  " " . $field_name . " : *" . trim($string_value) . "* ";
            $sql .=  " " . $operator ;
        }
        $sql = rtrim($sql,$operator);
        $sql .= " ) ";
    }

    return $sql;
}

/*
* Method for string query for solr
* @param : $from : from has integer value
* @param : $to : to has integer value
* @param : $operator : operator has AND or OR
* @return : return query i.e.  p.model : '*001*' AND p.model : '*003*'
* @author : vikas, 2017
*/

function queryIntegerForSolr($field_name, $from, $to, $operator){
    $max_characters = max(strlen($from), strlen($to));

    $sql = '';
    if(!empty($from) && !empty($to) && !empty($operator)){
        $sql .= " ". $operator ." " ;
        $sql .= " ( ";
        for( $i = (int)$from; $i <= (int)$to; $i++){

            $sql .=  $field_name . " : *" . trim(str_pad( "$i" , $max_characters, "0", STR_PAD_LEFT)) . "*";
            if( $i < (int)$to) {
                $sql .= " OR ";
            }
        }
        $sql .= ") ";
    }

    return $sql;
}

/**
 * Created by Murtaza
 * Date: 14/12/2017
**/
// Returns key-value pair for Months
// Use the key as value in html
// Use the value as display text in html
function getMonths() {
    return array(
        '1' => 'Jan',
        '2' => 'Feb',
        '3' => 'Mar',
        '4' => 'Apr',
        '5' => 'May',
        '6' => 'Jun',
        '7' => 'Jul',
        '8' => 'Aug',
        '9' => 'Sep',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec'
    );
}

/**
 * Method to return an array of years
 * @param int $beginning Financial year to begin from. Defaults to 2015.
 * @param int $end Financial year to end at. Default to 0, which means current year.
 * If $end is less than $beginning, then it is considered as $beginning year only.
 * @return array
 */
function getYears(int $beginning = 2015, int $end = 0) : array {

    $end = max($end, $beginning, (int)date('Y'));
    $financial_years = array();
    for ($i = $beginning; $i <= $end; $i++) {
        //$financial_years[] = $i . '-' . str_pad((float) substr($i, -2) + 1, 2, '0', STR_PAD_LEFT);
        $financial_years[$i] = $i;
    }
    return $financial_years;
}

/*
* convertUnit - method to convert unit type values
* @param  float $value
* @param  string $conversion_type
* @return float converted unit value. If $conversion_type is invalid/undefined, then it returns back the same input $value.
* @Author MSA DEC 2017
*  */
function convertUnit($value, $conversion_type)
{
    $unit_types = [

        'CM_TO_MM'      => 10,
        'CM_TO_INCH'    => 0.393701,
        'CM_TO_METER'   => 0.01,
        'CM_TO_FEET'    => 0.0328084,

        'METER_TO_MM'   => 1000,
        'METER_TO_CM'   => 100,
        'METER_TO_INCH' => 39.3701,
        'METER_TO_FEET' => 3.28084,
        'METER_TO_SQUARE_METER' => 10.7639,
        'METER_TO_YARD' => 1.09361,
        'METER_TO_SQUARE_YARD'  => 1.19599,
        'METER_TO_GAZ'  => 1.0989,

        'KG_TO_MG'      => 1000000,
        'KG_TO_GM'      => 1000,
        'KG_TO_LBS'     => 2.20462,
        'KG_TO_STONE'   => 0.157473,
        'KG_TO_POUND'   => 2.20462,
        'KG_TO_TONNE'   => 0.001,

        'LBS_TO_KG'     => 0.45359,
        'INCH_TO_CM'    => 2.54,

    ];

    if(!empty($conversion_type)) {

        if(array_key_exists($conversion_type, $unit_types))
        {
            return (float) $value * $unit_types[$conversion_type];
        }
    }
    return $value;
}

/**
* Method to get Amount with k means thousand, L means Lakh, Cr means Crore etc..,
* @param float $amount Amount value
* @return string Amount with k, L ex: (22k, 1.1L)
* @author Vikas, May 2018
**/
function abbreviateInIndianCurrency($amount){
    $amount = round((float)($amount), 2);
    if ($amount > 10000000) {
        $total = round($amount / 10000000, 1) . 'Cr';
    } elseif ($amount > 100000) {
        $total = round($amount / 100000, 1) . 'L';
    } elseif ($amount > 1000) {
        $total = round($amount / 1000, 1) . 'K';
    } else {
        $total = round($amount,1);
    }

    return getRupeeSymbol().' '.$total;
}


/**
* Method to get Rupees symbol
* @return string Rupees symbol
* @author Vikas, May 2018
*/
function getRupeeSymbol(){
    $symbol = '&#x20b9;';
    return $symbol;
}


function decodeApiData($data) {
    $result = array();
    $data = explode("&", base64_decode($data));
    foreach($data as $set_data) {

        if(isset($set_data[0]) && isset($set_data[1]))
        {
         $set_data = explode("=",$set_data);
         $result[$set_data[0]]= urldecode($set_data[1]);
        }
    }

    return $result;
}

/**
 * Method to find key with given prefix and and return as subarray in given array
 * @param string Array
 * @param array $data
 * @return array
 * @author Nishu, Aug 2018
 */
function setSubArrayWithKeyPrefix(string $prefix, array $data){
    if(!empty($data)){
        foreach ($data as $key => $value) {
            if (strpos($key, $prefix) === 0 ) {
                $new_key       = str_replace($prefix.'_', '', $key);
                $data[$prefix][$new_key] = $value;
            }
        }
    }
    return $data;
}

/**
 * Get IP address of client machine
 */
function getClientIpAddress(){

    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';

    return  $ipaddress;
}


/*
 * Function to check if the given input string is of valid datetime format.
 * Adapted from Stack Overflow: https://stackoverflow.com/a/12323025
 *
 * @param $date : String; Date string to be validated.
 * @param $format : String; refer PHP's DateTime class to prepare a format string
 *
 * @return boolean; True if $date is of proper format; else False.
 */
function validateDate(string $date, string $format) : bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * Method to get Browser description.
 * @return string
 */
function getBrowser()
{
    $user_agent  =  $_SERVER['HTTP_USER_AGENT'];
    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );

    foreach ( $browser_array as $regex => $value ) {
        if ( preg_match( $regex, $user_agent )) {
            return $value;
        }
    }
    // If no match
    return "Unknown Browser";
}

/**
 * Get OS Platform
 * @param string $request_by
 * @return string
 */
function getPlatform(string $request_by = "") : string {

    $os_platform = strtoupper( $request_by );
    $os_platform = str_replace( " ", "_", $os_platform );

    if ( empty( $os_platform )) {

        if( CONFIG_IS_MOBILE == 1 ) {
            $os_platform = 'MOBILE_WEB';
        } else {
            $os_platform = 'WEB';
        }
    }

    return $os_platform;
}

/**
 * Method to generate short urls for long urls
 * @param string $longUrl
 * @return array|string
 * @author MSA, July 2019
 */
function convertUrlToShortUrl(string $longUrl = '') {
    $longUrl = trim($longUrl);
    if(empty($longUrl) || !defined('BITLY_URL_SHORTNER_KEY') ){ return $longUrl; }

    $ch = curl_init();
    $rt  = array();

    /** @noinspection PhpUndefinedConstantInspection */
    $url = 'https://api-ssl.bitly.com/v3/shorten?access_token='. BITLY_URL_SHORTNER_KEY .'&longUrl=' . urlencode($longUrl) . '&format=json';

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $data = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($data);
    if(!empty($data)) {
        if (isset($data->status_code) && ($data->status_code == '200')) {
            $rt['status'] = 1;
            $rt['message'] = $data->data->url;
        } else {
            $rt['status'] = 0;
            $rt['message'] = $data->status_txt;
        }
    }else{
        $rt['status'] = 0;
        $rt['message'] = 'Short URL generator API not responding.';
    }

    return $rt;
}


/**
 * Method to take an input string and tokenize it into an array of words for Full Text Searching (FTS).
 *
 * This method is used when an input string can be made up of multiple words (let's say, separated by space characters),
 * and we need to use different Boolean operators on each of the words. The tokenizing process is similar to extraction
 * of words by FTS parser in MySQL. The operators used for matching in Boolean condition are removed from the input $phrase.
 * These characters as of latest version of MySQL (8+) are: +-><()~*:""&|@  (@ is specific for InnoDB)
 * We can also execute the following query to get updated list: show variables like 'ft_boolean_syntax';
 * Afterwards, the modified string is split into individual words considering either space, comma, and, period (.) characters.
 * Details at: https://dev.mysql.com/doc/refman/8.0/en/fulltext-natural-language.html
 *
 * @param string $phrase Input statement/phrase consisting of words
 * @return array Tokenized words
 * @author Madhur, 2019
 */
function tokenizeStringIntoFTSWords(string $phrase) : array {
    $phrase_mod = trim(preg_replace('/[><()~*:"&|@+-]/', ' ', trim($phrase)));
    $words_arr = preg_split('/[\s,.]/', $phrase_mod, null, PREG_SPLIT_NO_EMPTY);

    // filter out the fulltext stop words and words whose length is less than 3.
    $fts_words = array();
    $fulltext_stop_words = array(
        'about','are','com','for','from','how','that','this','was','what',
        'when','where','who','will','with','und','the','www'
    );
    foreach($words_arr as $word) {
        // By default MySQL FULLTEXT index does not store words whose length is less than 3.
        // Check innodb_ft_min_token_size Ref: https://dev.mysql.com/doc/refman/8.0/en/innodb-parameters.html#sysvar_innodb_ft_min_token_size
        // So we need to ignore words whose length is less than 3.
        if(strlen($word) < 3) continue;

        // Ignore the fulltext stop words, whose length is greater than 3 or equal to 3.
        // Ref: https://dev.mysql.com/doc/refman/8.0/en/fulltext-stopwords.html
        if (in_array($word, $fulltext_stop_words)) continue;

        $fts_words[] = $word;
    }

    return $fts_words;
}

/**
 * This method will take a filter string and format the string according to the full text search rules,
 * for more details see: https://dev.mysql.com/doc/refman/8.0/en/fulltext-boolean.html
 * @param string filter string
 * @return full text search string
 * @author Devendra, Sep 2019
 */
function getFullTextSearchString(string $filter): string {
    if (empty($filter)) return "";

    $words_arr = tokenizeStringIntoFTSWords($filter);
    $fts_string = "";
    foreach($words_arr as $word) {
        $fts_string .= "+" . $word . "* ";
    }
    // remove the space from the end
    $fts_string = rtrim($fts_string);

    return $fts_string;
}