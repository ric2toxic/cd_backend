<?php

require_once __DIR__ . '/../../khufiya_vibhag/config.php'; 
require_once(DIR_SYSTEM . 'library/db/db.php');


// Create DB object
$db = new Database\DB( DB_SERVERS );

// Include the main TCPDF library (search for installation path).
require_once('../library/tcpdf/tcpdf_import.php');
    
//Start Send MAil    
require_once '../library/phpmailer.php';

    /*for office mail*/
    $office_list = array();

    $office = array();
    $office[0]['id'] = '1';
    $office[0]['name'] = "wholesale jaipur head office";
    $office[0]['city_code'] = "JP";
    $office[0]['email'] = "amaratlalbairwa@gmail.com"; 
    //$office[0]['email'] = "wholesale.jaipur.head@gmail.com"; 
    $office[0]['type'] = "head"; 
    $office[0]['address'] = "Crystall Mall Banipark jaipur"; 
    
    $office[1]['id'] = '2';
    $office[1]['name'] = "wholesale surat head office";
    $office[1]['city_code'] = "ST";
    $office[1]['email'] = "amritlalbairwa90@gmail.com"; 
    //$office[1]['email'] = "wholesale.surat.head@gmail.com"; 
    $office[1]['type'] = "normal"; 
    $office[1]['address'] = "surat gujrat"; 
    
    $office[2]['id'] = '3';
    $office[2]['name'] = "wholesale jaipur mansrover"; 
    $office[2]['city_code'] = "JP";
    $office[2]['email'] = "demoamrit@gmail.com"; 
    //$office[2]['email'] = "wholesale.jaipur.1@gmail.com"; 
    $office[2]['type'] = "head"; 
    $office[2]['address'] = "mansrover plaza jaipur"; 
    
    $office[3]['id'] = '4';
    $office[3]['name'] = "wholesale surat 1";
    $office[3]['city_code'] = "ST";
    $office[3]['email'] = "amarat.bairwa@wholesalebox.biz"; 
    //$office[3]['email'] = "wholesale.surat.1@gmail.com"; 
    $office[3]['type'] = "normal"; 
    $office[3]['address'] = "surat gujrat"; 
    
    $office_list = $office;
    
    //echo "<pre>"; print_r($office_list); die;
    /*end dehli mail*/
    
    
    /* complete Query */
    $sql = "SELECT O.order_id, "
            . "SO.suborder_id, OP.order_product_id, "
            . "(OP.quantity * OP.piece_in_set) as total_pieces, "
            . "(OP.quantity * OP.piece_in_set * transfer_price_per_piece) as total_price, "
            . "MSP.seller_id, "
            . "MSS.nickname as seller_name, "
            . "MSS.company as seller_company, "
            . "MSS.pickup_city_code as seller_pickup_code, "
            . "MSS.email as seller_email, " 
            . "SP.picker_id, "
            . "PO.first_name as picker_first_name, "
            . "PO.last_name as picker_last_name, " 
            . "PO.pickup_city_code as picker_city_code, "
            . "PO.email as picker_email FROM " . DB_PREFIX . "order as O "
            . "left join  " . DB_PREFIX . "suborder as SO on SO.order_id = O.order_id "
            . "left join " . DB_PREFIX . "order_product as OP on OP.suborder_id = SO.suborder_id "
            . "left join " . DB_PREFIX . "ms_product as MSP on MSP.product_id = OP.product_id "
            . "left join " . DB_PREFIX . "ms_seller as MSS on MSS.seller_id = MSP.seller_id "
            . "left join " . DB_PREFIX . "seller_pickup as SP on SP.seller_id = MSS.seller_id "
            . "left join " . DB_PREFIX . "pickup_operations as PO on PO.id = SP.picker_id "
            . " WHERE SO.invoice_no = 0 AND (SO.order_status_id = '9' OR SO.order_status_id = '16') "
            . " group by PO.id, O.order_id, MSS.seller_id"; 
    
    //echo $sql; die; 
    $result = $db->query($sql)->rows;
    //echo "<pre>"; print_r($result); 
    //die; 
    
    //start mail for office
    $city_code_list = array();   
    foreach($result as $row){
        $city_code_list[] =  $row['picker_city_code'];
    }
    //create city_code_list group by
    $city_code_list = array_values(array_unique($city_code_list, SORT_REGULAR));  
    //echo "<pre>"; print_r($city_code_list); die;
    
    
    $office_email_list =  array();
    $office_email =  array();
    foreach($office_list as $offlist){
        foreach( $city_code_list as $offdata){
            if(strtoupper($offlist['city_code']) == strtoupper($offdata)){
                $office_email['email'] =  $offlist['email'];
                $office_email['city_code'] =  $offlist['city_code'];
            }
            $office_email_list[] = $office_email;
        }
    }
    $office_email_list = array_values(array_unique($office_email_list, SORT_REGULAR));  
    //echo "<pre>"; print_r($office_email_list);
    //die;
    
    
    foreach($city_code_list as $val){
        
        $sql = "SELECT O.order_id, "
            . "SO.suborder_id, OP.order_product_id, "
            . "(OP.quantity * OP.piece_in_set) as total_pieces, "
            . "(OP.quantity * OP.piece_in_set * transfer_price_per_piece) as total_price, "
            . "MSP.seller_id, "
            . "MSS.nickname as seller_name, "
            . "MSS.company as seller_company, "
            . "MSS.pickup_city_code as seller_pickup_code, "
            . "MSS.email as seller_email, " 
            . "SP.picker_id, "
            . "PO.first_name as picker_first_name, "
            . "PO.last_name as picker_last_name, " 
            . "PO.pickup_city_code as picker_city_code, "
            . "PO.email as picker_email FROM " . DB_PREFIX . "order as O "
            . "left join  " . DB_PREFIX . "suborder as SO on SO.order_id = O.order_id "
            . "left join " . DB_PREFIX . "order_product as OP on OP.suborder_id = SO.suborder_id "
            . "left join " . DB_PREFIX . "ms_product as MSP on MSP.product_id = OP.product_id "
            . "left join " . DB_PREFIX . "ms_seller as MSS on MSS.seller_id = MSP.seller_id "
            . "left join " . DB_PREFIX . "seller_pickup as SP on SP.seller_id = MSS.seller_id "
            . "left join " . DB_PREFIX . "pickup_operations as PO on PO.id = SP.picker_id "
            . " WHERE PO.pickup_city_code = '" . $val . "'"
            . " AND SO.invoice_no = 0 AND (SO.order_status_id = '9' OR SO.order_status_id = '16') "
            . " group by PO.id, O.order_id, MSS.seller_id"; 
            
        
        //echo $sql;  die;
        //echo "<br/>";
        $office_result = $db->query($sql)->rows;
        
        //html for picker_html
        $office_html = '';
        $office_html = '<h1 style="text-align :center">Pickup List ' .date('d-m-Y') .'</h1>';
        $office_html .= '<table align="center" border="1" width="100%">';
        $office_html .=    '<tr>';
        $office_html .=        '<th>Order Id</th>';
        $office_html .=        '<th>Suborder Id</th>';
        $office_html .=        '<th>Seller</th>';
        $office_html .=        '<th>Picker</th>';
        $office_html .=        '<th>Pickup City Code</th>';
        $office_html .=        '<th>No. of Pices</th>';
        $office_html .=        '<th>Tatal Amount</th>';
        $office_html .=    '</tr>';

        if(count($office_result) > 0){
            foreach($office_result as $row){
                if(strtoupper($row['seller_pickup_code']) == strtoupper($row['picker_city_code'])){
                    $office_html .=    '<tr>';
                    $office_html .=        '<td>' . $row['order_id'] .'</td>';
                    $office_html .=        '<td>' . $row['suborder_id'] .'</td>';
                    $office_html .=        '<td>' . $row['seller_name'] .'</td>';
                    $office_html .=        '<td>' . $row['picker_first_name'] . ' ' . $row['picker_last_name'] . '</td>';
                    $office_html .=        '<td>' . $row['seller_pickup_code'] .'</td>';
                    $office_html .=        '<td>' . $row['total_pieces'] .'</td>';
                    $office_html .=        '<td>' . $row['total_price'] .'</td>';
                    $office_html .=    '</tr>';
                }
            }
        }else{
            $office_html .=    '<tr>';
            $office_html .=        '<td colspan="7" style="text-align :center"> NO Records Found </td>';
            $office_html .=    '</tr>';
        }
        $office_html .= '</table>';
        //echo $office_html; 
        
        
            // PDF for office city wise 
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 041');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, 'Pickup List', PDF_HEADER_STRING);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->SetFont('times', '', 16);
        $pdf->AddPage();
        //add html here
        $pdf->writeHTML($office_html, true, false, true, false, '');  
        $pdf->Annotation(85, 27, 5, 5, 'text file', array('Subtype'=>'FileAttachment', 'Name' => 'PushPin', 'FS' => 'data/utf8test.txt'));
        $pdf->lastPage();
        $downl_path = DIR_DOWNLOAD . 'pickup_pdf/office/'.$row['seller_pickup_code'].'/';
        if (!file_exists($downl_path)) {
            $old = umask(0); 
            mkdir($downl_path, 0777, true); 
            umask($old); 
        }
        $file_name =  'pickup_list-' . date("d-m-Y") . '.pdf';
        $target_path = $downl_path . $file_name;
        $office_pdf = $pdf->Output($target_path, 'f');
            
        //for send mail city wise
        foreach($office_email_list as $r){
            if(strtoupper($r['city_code']) == strtoupper($val)){
                $city_mail = $r['email'];
                //echo $city_mail; 
                //for office mail
                $mail = new PHPMailer();
                $subject = "Office Pickup List " . date('d-m-Y');
                $body    = "Please check acttement file for more information";
                $mail->isSMTP(); 
                $mail->SMTPDebug = 1; 
                $mail->SMTPAuth = true; 
                $mail->SMTPSecure = 'tls'; 
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 587; 
                $mail->Username="demoamrit@gmail.com";        
                $mail->Password = "demoamrit@123";                   
                $mail->SetFrom("demoamrit@gmail.com");         
                $mail->Subject = $subject;                  
                $mail->Body    = $body;     

                $to_mail = $city_mail;
                $mail->addAddress($to_mail); 
                $mail->AddAttachment($target_path);
                $mail = $mail->Send(true);

                if($mail) {   //echo "1"; die;
                    echo "<p>The email was sent.</p>";
                }
                else { //echo "2"; die;
                    echo "<p>There was an error sending the mail.</p>";
                    echo $mail->ErrorInfo;
                }
                //for office mail
            }
        }
    }
    //die('hi');
    //end mail for office
    
    
    
    
    $html = '';
    $html = '<h1 style="text-align :center">Pickup List '. date('d-m-Y') .'</h1>';
    $html .= '<table align="center" border="1" width="100%">';
    $html .=    '<tr>';
    $html .=        '<th>Order Id</th>';
    $html .=        '<th>Suborder Id</th>';
    $html .=        '<th>Seller</th>';
    $html .=        '<th>Picker</th>';
    $html .=        '<th>Pickup City Code</th>';
    $html .=        '<th>No. of Pices</th>';
    $html .=        '<th>Tatal Amount</th>';
    $html .=    '</tr>';
    
    if(count($result) > 0){
        foreach($result as $row){
            if(strtoupper($row['seller_pickup_code']) == strtoupper($row['picker_city_code'])){
                $html .=    '<tr>';
                $html .=        '<td>' . $row['order_id'] .'</td>';
                $html .=        '<td>' . $row['suborder_id'] .'</td>';
                $html .=        '<td>' . $row['seller_name'] .'</td>';
                $html .=        '<td>' . $row['picker_first_name'] . ' ' . $row['picker_last_name'] .'</td>';
                $html .=        '<td>' . $row['seller_pickup_code'] .'</td>';
                $html .=        '<td>' . $row['total_pieces'] .'</td>';
                $html .=        '<td>' . $row['total_price'] .'</td>';
                $html .=    '</tr>';
            }
        }
    }else{
        $html .=    '<tr>';
        $html .=        '<td colspan="5" style="text-align :center"> NO Records Found </td>';
        $html .=    '</tr>';
    }
    
    $html .= '</table>';
    //echo $html; die;
    
    
//============================================================+
// STARTS OF FILE FOR CREATE PDF
//============================================================+
    
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 041');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 041', PDF_HEADER_STRING);
$pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, 'Pickup List', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', '', 16);

// add a page
$pdf->AddPage();

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// attach an external file
$pdf->Annotation(85, 27, 5, 5, 'text file', array('Subtype'=>'FileAttachment', 'Name' => 'PushPin', 'FS' => 'data/utf8test.txt'));


// reset pointer to the last page
$pdf->lastPage();

//create path for download pdf
$downl_path = DIR_DOWNLOAD . 'pickup_pdf/admin/';
if (!file_exists($downl_path)) {
    $old = umask(0); 
    mkdir($downl_path, 0777, true); 
    umask($old); 
}
$file_name =  'pickup_list-' . date("d-m-Y") . '.pdf';

$target_path = $downl_path . $file_name;

//Close and output PDF document
$admin_pdf = $pdf->Output($target_path, 'f');

 
//============================================================+
// END OF FILE FOR CREATE PDF
//============================================================+

    
    //Mail for Admin
    $mail = new PHPMailer();
        $subject = "Pickup List " . date('d-m-Y');

        $body    = "Please check acttement file for more information";

        $mail->isSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587; // or 587
        //$mail->IsHTML(true);    
        $mail->Username="demoamrit@gmail.com";        
        $mail->Password = "demoamrit@123";                   
        $mail->SetFrom("demoamrit@gmail.com");         
        $mail->Subject = $subject;                  
        $mail->Body    = $body;      
        
        $to_mail = 'amarat.bairwa@wholesalebox.biz';
        $mail->addAddress($to_mail); 
        $mail->AddAttachment($target_path);
        $mail = $mail->Send(true);
        
        if($mail) {   
            echo "<p>The email was sent.</p>";
        }
        else {
            echo "<p>There was an error sending the mail.</p>";
            echo $mail->ErrorInfo;
        }
    //End Mail for Admin    
     


    $pickers_data = array();   
    foreach($result as $row){
        $pdata['picker_id'] =  $row['picker_id'];
        $pdata['picker_name'] =  $row['picker_first_name'] . ' ' . $row['picker_last_name'];
        $pdata['picker_email'] =  $row['picker_email'];
        $pickers_data[] = $pdata;
    }
    $pickers_data = array_values(array_unique($pickers_data, SORT_REGULAR)); 
    //echo "<pre>"; print_r($pickers_data);
    //die;
    //for picker
    foreach($pickers_data as $val){
        
        $sql = "SELECT O.order_id, "
            . "SO.suborder_id, OP.order_product_id, "
            . "(OP.quantity * OP.piece_in_set) as total_pieces, "
            . "(OP.quantity * OP.piece_in_set * transfer_price_per_piece) as total_price, "
            . "MSP.seller_id, "
            . "MSS.nickname as seller_name, "
            . "MSS.company as seller_company, "
            . "MSS.pickup_city_code as seller_pickup_code, "
            . "MSS.email as seller_email, " 
            . "SP.picker_id, "
            . "PO.first_name as picker_first_name, "
            . "PO.last_name as picker_last_name, " 
            . "PO.pickup_city_code as picker_city_code, "
            . "PO.email as picker_email FROM " . DB_PREFIX . "order as O "
            . "left join  " . DB_PREFIX . "suborder as SO on SO.order_id = O.order_id "
            . "left join " . DB_PREFIX . "order_product as OP on OP.suborder_id = SO.suborder_id "
            . "left join " . DB_PREFIX . "ms_product as MSP on MSP.product_id = OP.product_id "
            . "left join " . DB_PREFIX . "ms_seller as MSS on MSS.seller_id = MSP.seller_id "
            . "left join " . DB_PREFIX . "seller_pickup as SP on SP.seller_id = MSS.seller_id "
            . "left join " . DB_PREFIX . "pickup_operations as PO on PO.id = SP.picker_id "
            . " WHERE PO.id = " . $val['picker_id'] 
            . " AND SO.invoice_no = 0 AND (SO.order_status_id = '9' OR SO.order_status_id = '16') "
            . " group by O.order_id, MSS.seller_id"; 
            
        
        //echo $sql;  die;
        //echo "<br/>";
        $result = $db->query($sql)->rows;
        
        //html for picker_html
        $picker_html = '';
        $picker_html = '<h1 style="text-align :center">Pickup List ' .date('d-m-Y') .'</h1>';
        $picker_html .= '<table align="center" border="1" width="100%">';
        $picker_html .=    '<tr>';
        $picker_html .=        '<th>Order Id</th>';
        $picker_html .=        '<th>Suborder Id</th>';
        $picker_html .=        '<th>Seller</th>';
        $picker_html .=        '<th>Pickup City Code</th>';
        $picker_html .=        '<th>No. of Pices</th>';
        $picker_html .=        '<th>Tatal Amount</th>';
        $picker_html .=    '</tr>';

        if(count($result) > 0){
            foreach($result as $row){
                if(strtoupper($row['seller_pickup_code']) == strtoupper($row['picker_city_code'])){
                    $picker_html .=    '<tr>';
                    $picker_html .=        '<td>' . $row['order_id'] .'</td>';
                    $picker_html .=        '<td>' . $row['suborder_id'] .'</td>';
                    $picker_html .=        '<td>' . $row['seller_name'] .'</td>';
                    $picker_html .=        '<td>' . $row['seller_pickup_code'] .'</td>';
                    $picker_html .=        '<td>' . $row['total_pieces'] .'</td>';
                    $picker_html .=        '<td>' . $row['total_price'] .'</td>';
                    $picker_html .=    '</tr>';
                }
            }
        }else{
            $picker_html .=    '<tr>';
            $picker_html .=        '<td colspan="5" style="text-align :center"> NO Records Found </td>';
            $picker_html .=    '</tr>';
        }
        $picker_html .= '</table>';
        //echo $picker_html; 
        
        
        
        // PDF for picker 
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 041');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, 'Pickup List', PDF_HEADER_STRING);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->SetFont('times', '', 16);
        $pdf->AddPage();
        //add html here
        $pdf->writeHTML($picker_html, true, false, true, false, '');  
        $pdf->Annotation(85, 27, 5, 5, 'text file', array('Subtype'=>'FileAttachment', 'Name' => 'PushPin', 'FS' => 'data/utf8test.txt'));
        $pdf->lastPage();
        $downl_path = DIR_DOWNLOAD . 'pickup_pdf/picker/'.$val['picker_id'].'/';
        if (!file_exists($downl_path)) {
            $old = umask(0); 
            mkdir($downl_path, 0777, true); 
            umask($old); 
        }
        $file_name =  'pickup_list-' . date("d-m-Y") . '.pdf';
        $target_path = $downl_path . $file_name;
        $picker_pdf = $pdf->Output($target_path, 'f');
        
        //for picker_mail
        $mail = new PHPMailer();
        $subject = "Pickup List " . date('d-m-Y');
        $body    = "Please check acttement file for more information";
        $mail->isSMTP(); 
        $mail->SMTPDebug = 1; 
        $mail->SMTPAuth = true; 
        $mail->SMTPSecure = 'tls'; 
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587; 
        $mail->Username="demoamrit@gmail.com";        
        $mail->Password = "demoamrit@123";                   
        $mail->SetFrom("demoamrit@gmail.com");         
        $mail->Subject = $subject;                  
        $mail->Body    = $body;     
        
        $to_mail = $val['picker_email'];
        $mail->addAddress($to_mail); 
        $mail->AddAttachment($target_path);
        $mail = $mail->Send(true);
        
        if($mail) {   
            echo "<p>The email was sent.</p>";
        }
        else {
            echo "<p>There was an error sending the mail.</p>";
            echo $mail->ErrorInfo;
        }
        //for picker_mail
        
    }
    
//End Send MAil  
exit();

