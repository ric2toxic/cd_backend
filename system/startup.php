<?php
// Error Reporting
error_reporting(E_ALL);

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
function autoload($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once(modification($file));
		return true;
	}

	return false;
}

spl_autoload_register('autoload');
spl_autoload_extensions('.php');

// Engine
require_once(modification(DIR_SYSTEM . 'engine/action.php'));
require_once(modification(DIR_SYSTEM . 'engine/controller.php'));
require_once(modification(DIR_SYSTEM . 'engine/event.php'));
require_once(modification(DIR_SYSTEM . 'engine/front.php'));
require_once(modification(DIR_SYSTEM . 'engine/loader.php'));
require_once(modification(DIR_SYSTEM . 'engine/model.php'));
require_once(modification(DIR_SYSTEM . 'engine/registry.php'));

// Helper
require_once(DIR_SYSTEM . 'helper/json.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');
require_once(DIR_SYSTEM . 'helper/utilities.php');

//solr
require_once(DIR_SYSTEM . 'library/solr/product.php');
require_once(DIR_SYSTEM . 'library/solr/model_solr_product.php');
require_once(DIR_SYSTEM . 'library/solr/order.php');
require_once(DIR_SYSTEM . 'library/solr/lookup.php');
require_once(DIR_SYSTEM . 'library/customer_nach_details.php');
require_once(DIR_SYSTEM . 'library/CronSchedule.php');
require_once(DIR_SYSTEM . 'library/nach.php');
require_once(DIR_SYSTEM . 'library/bank_holiday.php');
require_once(DIR_SYSTEM . 'library/nach_behaviour.php');
require_once(DIR_SYSTEM . 'library/pagination_v2.php');

// Loaded All files in system/library/operations
$path = DIR_SYSTEM.'library/operations/';
loadFiles($path);
$path = DIR_SYSTEM.'library/seller/';
loadFiles($path);

$path = DIR_SYSTEM.'library/Cron/';
loadFiles($path);

$path = DIR_SYSTEM.'library/nach_bank/';
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

require_once( DIR_SYSTEM . 'library/total/totalfactory.php' );
require_once( DIR_SYSTEM . 'library/sales_staff.php' );
require_once( DIR_SYSTEM . 'library/crm/order_crm_api.php');
require_once( DIR_SYSTEM . 'library/crm/crm_api.php');
//require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');

require_once( DIR_SYSTEM . 'library/entity/customer_entity.php');
require_once( DIR_SYSTEM . 'library/curl.php');
require_once( DIR_SYSTEM . 'library/mysftp.php');
