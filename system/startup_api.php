<?php
// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Check Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
	exit('PHP5.3+ Required');
}

if (!ini_get('date.timezone')) {
	date_default_timezone_set('Asia/Kolkata');
}

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Check if SSL
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

require DIR_SYSTEM . '../vendor/autoload.php';

// Modification Override
function modification($filename) {
	if (!defined('DIR_CATALOG')) {
		$file = DIR_MODIFICATION . 'catalog/' . substr($filename, strlen(DIR_APPLICATION));
	} else {
		$file = DIR_MODIFICATION . 'admin/' .  substr($filename, strlen(DIR_APPLICATION));
	}

	if (substr($filename, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
		$file = DIR_MODIFICATION . 'system/' . substr($filename, strlen(DIR_SYSTEM));
	}

	if (is_file($file)) {
		return $file;
	}

	return $filename;
}

// Autoloader
/*function autoload($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once(modification($file));
		return true;
	}

	return false;
}*/

//spl_autoload_register('autoload');
//spl_autoload_extensions('.php');

// Engine
require_once(DIR_SYSTEM . 'engine/registry.php');
require_once(DIR_SYSTEM . 'engine/loader.php');
require_once(DIR_SYSTEM . 'engine/model.php');
require_once(DIR_SYSTEM . 'engine/event.php');


// Helper
require_once(DIR_SYSTEM . 'helper/json.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');
require_once(DIR_SYSTEM . 'helper/utilities.php');

//solr
/*
require_once(DIR_SYSTEM . 'library/solr/product.php');
require_once(DIR_SYSTEM . 'library/solr/model_solr_product.php');
require_once(DIR_SYSTEM . 'library/solr/order.php');
require_once(DIR_SYSTEM . 'library/solr/lookup.php');
*/

// Loaded All files in system/library/operations
/*$path = DIR_SYSTEM.'library/operations/';
loadFiles($path);
$path = DIR_SYSTEM.'library/seller/';
loadFiles($path);
*/

$path = DIR_SYSTEM.'library/db/';
loadFiles($path);

function loadFiles($path){
    $files = scandir($path);
    foreach ($files as $key => $file) {

		if( $file == 'rgl-images.php' ){
			continue;
		} 

        if(strpos($file,'.php') && is_file($path.$file)){
            require_once( $path . $file );
        }
        else if(is_dir($path.$file) && $file != '.' && $file != '..'){
            loadFiles($path.$file.'/');
        }
    } 
}
require_once(modification(DIR_SYSTEM . 'engine/registry.php'));
require_once(modification(DIR_SYSTEM . 'library/config.php'));

//require_once( DIR_SYSTEM . 'library/total/totalfactory.php' );

require_once( DIR_SYSTEM . 'library/currency.php' );
require_once( DIR_SYSTEM . 'library/tax.php' );
require_once( DIR_SYSTEM . 'library/url.php' );
require_once( DIR_SYSTEM . 'library/request.php' );
require_once(DIR_SYSTEM . 'library/cache.php');
require_once(DIR_SYSTEM . 'library/cache/file.php');

require_once(DIR_SYSTEM . 'library/restapi.php');
require_once(DIR_SYSTEM . 'library/operations/orders/order_info.php');
require_once(DIR_SYSTEM . 'library/operations/orders/split_order.php');
require_once(DIR_SYSTEM . 'library/total/totalfactory.php');
require_once(DIR_SYSTEM . 'library/CronSchedule.php');

//Library file return_action_base.php & return_action_clusters.php needed to use return reasons and actions
require_once( DIR_SYSTEM . 'library/operations/returns/return_action_base.php');
require_once( DIR_SYSTEM . 'library/operations/returns/return_action_clusters.php');
require_once( DIR_SYSTEM . 'library/operations/returns/return_reasons.php');
require_once( DIR_SYSTEM . 'library/gst.php');
require_once( DIR_SYSTEM . 'library/entity/customer_entity.php');
require_once( DIR_SYSTEM . 'library/operations/payment_gateway/wsb_credit.php');

//Include Order_status
require_once( DIR_SYSTEM . 'library/operations/orders/order_status.php');