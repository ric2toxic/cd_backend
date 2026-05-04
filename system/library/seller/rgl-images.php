<?php

// Specific code to download images for RGL Fashions (001_DL).
// Dont run without asking Madhur

// prevent external access
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // If a "remote" address is set, we know that this is not a CLI call
    header('HTTP/1.1 403 Forbidden');
    die('Access denied. Go away, shoo!');
}

$api_link = 'http://rfpl.sugarkane.in/channelapi/getCatalogue/wholesalebox';

// Getting JSON via CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$json_data = curl_exec($ch);
curl_close($ch);


// converting JSON to array
$destination_folder = '/var/www/html/image/catalog/001_DL/';
$final_arr = json_decode($json_data, true);

$unique_stylecodes = array_unique(array_column($final_arr, 'styleCode'));
//echo "<pre>"; print_r($unique_stylecodes); echo"</pre>"; exit();

foreach ( array_keys($unique_stylecodes) as $key ) {
    $count = 1;
    foreach ( $final_arr[$key]['image'] as $image_url ) {
		$original_name = basename($image_url); //name.jpg
		$original_extension = substr($original_name, strrpos($original_name, '.')); // ".jpg"		
		$stored_name = $destination_folder.$final_arr[$key]['styleCode']."_".$count. $original_extension;
        
        if ( file_exists($stored_name) ) {
            // Ignore downloading this file
            continue;
        }
        
        $img = file_get_contents($image_url);		
		if($img) {
		  file_put_contents($stored_name, $img);
		  chmod($stored_name, 0777);  //changed to add the zero
          $count++;
		}
    }
}

?>
