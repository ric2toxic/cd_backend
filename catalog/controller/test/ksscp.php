<?php
class ControllerTestKsScp extends Controller{

    public function scp(){
        error_reporting(0);
        ini_set('display_errors', 0);
        
        $dom = new DOMDocument();  

      //load the html  
      $html = $dom->loadHTMLFile("http://report.wydr.in/sales.php");  

        //discard white space   
      $dom->preserveWhiteSpace = false;   

        //the table by its tag name  
      $tables = $dom->getElementsByTagName('table');   


          //get all rows from the table  
      $rows = $tables->item(1)->getElementsByTagName('tr'); 
        
        // get each column by tag name  
      $cols = $rows->item(0)->getElementsByTagName('th'); 

      $row_headers = NULL;
      foreach ($cols as $node) {
          //print $node->nodeValue."\n";   
          $row_headers[] = $node->nodeValue;
      }   

      $table = array();
        //get all rows from the table  
      $rows = $tables->item(0)->getElementsByTagName('tr');   

      foreach ($rows as $row){   
         // get each column by tag name  
          $cols = $row->getElementsByTagName('td');   
          $row = array();
          $i=0;
          foreach ($cols as $node) {
              # code...
              //print $node->nodeValue."\n";   
              if($row_headers==NULL)
                  $row[] = $node->nodeValue;
              else
                  $row[$row_headers[$i]] = $node->nodeValue;
              $i++;
          }   
          $table[] = $row;
      }

        $c = 0;
        $dir_name = '/var/downloads';
        $file = $dir_name.'/srpcsv.csv';
        if (!file_exists($dir_name)) {
            mkdir($dir_name, 0777, true);
        }

      $fp = fopen($file , 'r');

      if($fp){
          while(!feof($fp)){
                $content = fgets($fp);
            if($content)    $c++;
          }
      }
      fclose($fp);      

          $headers = array(
            'Date',
            'Time',
            'Time Recorded',            
            'Fashion Order',
            'Fashion GMV',            
            'Footwear Order',
            'Footwear GMV',            
            'Toys & Babycare Order',
            'Toys & Babycare GMV',            
            'Beauty & Perfumes Order',
            'Beauty & Perfumes GMV',            
            'Hardware Order',
            'Hardware GMV',            
            'Electronics & Appliances Order',
            'Electronics & Appliances GMV',            
            'Home Furnishing Order',
            'Home Furnishing GMV',            
            'Home Decor Order',
            'Home Decor GMV',            
            'Jewelry & Watches Order',
            'Jewelry & Watches GMV',            
            'Home Supplies Order',
            'Home Supplies GMV',            
            'Automotive Order',
            'Automotive GMV',            
            'Sports & Fitness Order',
            'Sports & Fitness GMV',            
            'Computers Order',
            'Computers GMV',
            'Mobiles & Tablets Order',
            'Mobiles & Tablets GMV',            
            'Grand Total',
            'Total Order',
            'Total GMV'
            ); 

          $datearray = explode(' ', $table[0][0]);

          $i=0;
          $data = '';

          foreach ($table as $key => $value) {

            if ($key>2) {
              if ($value[0]=='Category' && $value[1]=='Orders') {
                $i++;
                
              }
              $u = 0;
              if ($value[0]!='Category' && $value[1]!='Orders') {
                $cat = $value[0];
                if ($i>0) {
                  $cat = explode('==', $value[0]);
                  if (isset($cat[1])) {
                    $cat = $cat[1];
                  }else{
                    $cat = 'Grand Total';
                  }
                  
                }
                $data[$i][$cat][$u++] = $value[0];
                $data[$i][$cat][$u++] = $value[1];
                $data[$i][$cat][$u++] = $value[2];      
              }
            }
            
          }

          $datas = '';
          /*
          * set columns title
          */
          if ($c<1) {
            $datas[0]=$headers;
          }

          /*
          * set first three column value of every row
          */
          $r1=1;
          $r2=2;
          $r3=3;

          if ($c>1) {
            $r1=$c+1;
            $r2=$c+2;
            $r3=$c+3;
          }

          $datas[$r1][0] = $datearray[1];
          $datas[$r1][1] = $datearray[2];
          $datas[$r1][2] = date('Y-m-d H:i:s');

          $datearray1 = explode(' ', $table[0][1]);
          $datas[$r2][0] = date('Y-m-d', strtotime('-1 day', strtotime($datearray[1])));
          $datas[$r2][1] = $datearray1[1].' '.$datearray1[2];
          $datas[$r2][2] = date('Y-m-d H:i:s');

          $datearray2 = explode(' ', $table[0][2]);
          $datas[$r3][0] = date('Y-m-d', strtotime('-7 day', strtotime($datearray[1])));
          $datas[$r3][1] = $datearray2[1].' '.$datearray2[2];
          $datas[$r3][2] = date('Y-m-d H:i:s');

          $tableKey = array(
            'Fashion',
            'Footwear',
            'Toys & Babycare',
            'Beauty & Perfumes',
            'Hardware',
            'Electronics & Appliances',
            'Home Furnishing',
            'Home Decor',
            'Jewelry & Watches',
            'Home Supplies',
            'Automotive',
            'Sports & Fitness',
            'Computers',
            'Mobiles & Tablets',
            'Grand Total',
            );

          if ($c<1) {
            $j = 1;
          }else{
            $j=($c+1);
          }

          foreach ($data as $key => $value) {
            
                if (array_key_exists("Fashion",$value)) {
                  $datas[$j][3] = $value['Fashion'][1];
                  $datas[$j][4] = $value['Fashion'][2];
                }else{        
                  $datas[$j][3] = '0';
                  $datas[$j][4] = '0';
                }
                if (array_key_exists("Footwear",$value)) {
                  $datas[$j][5] = $value['Footwear'][1];
                  $datas[$j][6] = $value['Footwear'][2];
                }else{
                  $datas[$j][5] = '0';
                  $datas[$j][6] = '0';
                }
                if (array_key_exists("Toys & Babycare",$value)) {
                  $datas[$j][7] = $value['Toys & Babycare'][1];
                  $datas[$j][8] = $value['Toys & Babycare'][2];
                }else{
                  $datas[$j][7] = '0';
                  $datas[$j][8] = '0';
                }
                if (array_key_exists("Beauty & Perfumes",$value)) {
                  $datas[$j][9] = $value['Beauty & Perfumes'][1];
                  $datas[$j][10] = $value['Beauty & Perfumes'][2];
                }else {
                  $datas[$j][9] = '0';
                  $datas[$j][10] = '0';
                }
                if (array_key_exists("Hardware",$value)) {
                  $datas[$j][11] = $value['Hardware'][1];
                  $datas[$j][12] = $value['Hardware'][2];
                }else {
                  $datas[$j][11] = '0';
                  $datas[$j][12] = '0';
                }
                if (array_key_exists("Electronics & Appliances",$value)) {
                  $datas[$j][13] = $value['Electronics & Appliances'][1];
                  $datas[$j][14] = $value['Electronics & Appliances'][2];
                }else {
                  $datas[$j][13] = '0';
                  $datas[$j][14] = '0';
                }
                if (array_key_exists("Home Furnishing",$value)) {
                  $datas[$j][15] = $value['Home Furnishing'][1];
                  $datas[$j][16] = $value['Home Furnishing'][2];
                }else {
                  $datas[$j][15] = '0';
                  $datas[$j][16] = '0';
                }
                if (array_key_exists("Home Decor",$value)) {
                  $datas[$j][17] = $value['Home Decor'][1];
                  $datas[$j][18] = $value['Home Decor'][2];
                }else {
                  $datas[$j][17] = '0';
                  $datas[$j][18] = '0';
                }
                if (array_key_exists("Jewelry & Watches",$value)) {
                  $datas[$j][19] = $value['Jewelry & Watches'][1];
                  $datas[$j][20] = $value['Jewelry & Watches'][2];
                }else {
                  $datas[$j][19] = '0';
                  $datas[$j][20] = '0';
                }
                if (array_key_exists("Home Supplies",$value)) {
                  $datas[$j][21] = $value['Home Supplies'][1];
                  $datas[$j][22] = $value['Home Supplies'][2];
                }else {
                  $datas[$j][21] = '0';
                  $datas[$j][22] = '0';
                }
                if (array_key_exists("Automotive",$value)) {
                  $datas[$j][23] = $value['Automotive'][1];
                  $datas[$j][24] = $value['Automotive'][2];
                }else {
                  $datas[$j][23] = '0';
                  $datas[$j][24] = '0';
                }
                if (array_key_exists("Sports & Fitness",$value)) {
                  $datas[$j][25] = $value['Sports & Fitness'][1];
                  $datas[$j][26] = $value['Sports & Fitness'][2];
                }else {
                  $datas[$j][25] = '0';
                  $datas[$j][26] = '0';
                }
                if (array_key_exists("Computers",$value)) {
                  $datas[$j][27] = $value['Computers'][1];
                  $datas[$j][28] = $value['Computers'][2];
                }else {
                  $datas[$j][27] = '0';
                  $datas[$j][28] = '0';
                }
                if (array_key_exists("Mobiles & Tablets",$value)) {
                  $datas[$j][29] = $value['Mobiles & Tablets'][1];
                  $datas[$j][30] = $value['Mobiles & Tablets'][2];
                }else {
                  $datas[$j][29] = '0';
                  $datas[$j][30] = '0';
                }
                if (array_key_exists("Grand Total",$value)) {
                  $datas[$j][31] = $value['Grand Total'][0];
                  $datas[$j][32] = $value['Grand Total'][1];
                  $datas[$j][33] = $value['Grand Total'][2];
                }else{
                  $datas[$j][31] = '';
                  $datas[$j][32] = '';
                  $datas[$j][33] = '';
                }
            $j++;
          }
        ob_clean();        
        if ($c<1) {
          
          $fp = fopen($file , 'w');
          
          foreach ($datas as $key => $value) {
            fputcsv($fp, $value);
          }

          /*if (file_exists($file)) {
           
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
          }*/
        }else{

          $fp = fopen($file, "a");
          foreach ($datas as $key => $value) {
            fputcsv($fp, $value);
          }
          
        }
        fclose($fp);
    }

 }
