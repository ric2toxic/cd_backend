<?php
$dir = "personalization_csv/";

$result = scan_dir($dir);
if(!empty($result)) {
	foreach ($result as $key => $value) {
		$url = 'personalization_csv/'.$value;
		echo "filename: <a href='".$url."'>" . $value . "</a><br>";
	}
} else {
	echo "No previous CSV generated.";
}
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}
?>