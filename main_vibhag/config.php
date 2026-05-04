<?php
// HTTP
define('HTTP_SERVER', 'http://www.cddemo.in/main_vibhag/');
define('HTTP_CATALOG', 'http://www.cddemo.in/');
define('HTTP_DOMAIN', 'http://www.cddemo.in/');

// HTTPS
define('HTTPS_SERVER', 'http://www.cddemo.in/main_vibhag/');
define('HTTPS_CATALOG', 'http://www.cddemo.in/');

// WSB Stores
define('WSB_STORES_ID', '0,2,9');
define('RETURN_ADMIN_IDS', "1");
define('INTERNATIONAL_STORE_ID', '2');
define('SOR_STORE_ID', '9');
define('DIR_WSB_PURCHASE_IMAGE', '/Applications/MAMP/htdocs/cddemo/image/wsb_purchase/');
//Google api key gcm
define('API_ACCESS_KEY', 'AIzaSyCqnxMbpr2Y-51AH5bdIO7sR7v-oJQF4iE');
define('KEYBOARD_DISABLED', 0);
define('S3_ENABLED', 0);
define('DEBUG_SQL_STATUS', 0);

// DIR
define('DIR_APPLICATION', '/Applications/MAMP/htdocs/cddemo/main_vibhag/');
define('DIR_SYSTEM', '/Applications/MAMP/htdocs/cddemo/system/');
define('DIR_LANGUAGE', '/Applications/MAMP/htdocs/cddemo/main_vibhag/language/');
define('DIR_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/main_vibhag/view/template/');
define('DIR_ADMIN_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/main_vibhag/view/template/');
define('DIR_CONFIG', '/Applications/MAMP/htdocs/cddemo/system/config/');
define('DIR_IMAGE', '/Applications/MAMP/htdocs/cddemo/image/');
define('DIR_SYSTEM_IMAGE', '/Applications/MAMP/htdocs/cddemo/system/library/image/');
define('DIR_CACHE', '/Applications/MAMP/htdocs/cddemo/system/cache/');
define('DIR_DOWNLOAD', '/Applications/MAMP/htdocs/cddemo/system/downloads/');
define('DIR_UPLOAD', '/Applications/MAMP/htdocs/cddemo/system/upload/');
define('DIR_LOGS', '/Applications/MAMP/htdocs/cddemo/system/logs/');
// define('DIR_LOGS', '/Applications/MAMP/htdocs/opc/upload/system/storage/logs/');
define('DIR_MODIFICATION', '/Applications/MAMP/htdocs/cddemo/system/modification/');
define('DIR_CATALOG', '/Applications/MAMP/htdocs/cddemo/catalog/');
define('DIR_STOREFRONT', '/var/www/storefront/html/');
define('DIR_DLOAD_SLR_INV', '/Applications/MAMP/htdocs/cddemo/downloads/seller_invoice/');
define('DIR_DLOAD_BYR_INV', '/Applications/MAMP/htdocs/cddemo/downloads/buyer_invoice/');
define('DIR_DLOAD_GATI_DKT', '/Applications/MAMP/htdocs/cddemo/downloads/gati_docket_csv/');
define('DIR_DLOAD_SHP_LBL', '/Applications/MAMP/htdocs/cddemo/downloads/shipping_label/');
define('DIR_DLOAD_SLR_DBT_NOTE', '/Applications/MAMP/htdocs/cddemo/downloads/debit_note/');
define('DIR_DLOAD_BYR_CDT_NOTE', '/Applications/MAMP/htdocs/cddemo/downloads/credit_note/');
define('DIR_DLOAD_RCT_INV', '/Applications/MAMP/htdocs/cddemo/downloads/receipt_voucher/');
define('DIR_ADMIN_LANGUAGE', '/var/www/html/wholesale-box-opencart/main_vibhag/language/');
define('DIR_ADMIN', '/Applications/MAMP/htdocs/cddemo/main_vibhag/');
define('DIR_BASE', '/Applications/MAMP/htdocs/cddemo/');
//define('LOCAL_CDN_URL','http://www.cddemo.in/image/');
//define('LOCAL_CDN_URL_SSL','http://www.cddemo.in/image/');

define('ADMIN_IDS', '100,41');
define('OPERATIONS_ADMIN_IDS', '100,15');
//define('GATI_API_LINK', 'http://www.gati.com/webservices/JGatiXMLpickup.jsp');
define('GATI_API_LINK', 'http://119.235.57.47:9080/TESTFKGatiXMLpickup2.jsp');

define('GATI_LTD_API_LINK', 'http://119.235.57.47:9080/TESTFKGatiXMLpickup2.jsp');
define('GATI_KWE_API_LINK', 'http://119.235.57.47:9080/GATIKWEPICKUPLBH.jsp');
//Local
//DotZot Local
define('DotZot_API_LINK', 'http://dotzot-test.azurewebsites.net/RestService/PushOrderDataService.svc/');
define('DotZot_API_CUSTCD', 'CC000100132');
define('DotZot_API_TRACKING_LINK', 'http://dotzot-test.azurewebsites.net/RestService/DocketTrackingService.svc/');

//Blue dart
define('BLUE_DART_USERNAME', 'WHOLESALEBOX INTERNET PVT. LTD');
define('BLUE_DART_VERSION_NUMBER', '1.3');
define('BLUE_DART_API_TYPE', 'S');
define('BLUE_DART_SOAP_CLIENT', 'http://netconnect.bluedart.com/Ver1.8/Demo/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl');
define('BLUE_DART_SOAP_CLIENT_HEADER_LOCATION', 'http://netconnect.bluedart.com/Ver1.8/Demo/ShippingAPI/WayBill/WayBillGeneration.svc');
define('BLUE_DART_SOAP_CLIENT_HEADER', 'http://www.w3.org/2005/08/addressing');
define('BLUE_DART_SOAP_CLIENT_ACTION_URI', 'http://tempuri.org/');
define('BLUE_DART_ACCESS', array(
	'jaipur' => array(
		'login_id' => 'JA337654',
		'license_key' => 'cc32aed23f7c2bf00e2f0172e618e818',
	),
	'new_delhi' => array(
		'login_id' => 'JA337654',
		'license_key' => 'cc32aed23f7c2bf00e2f0172e618e818',
	),
	'surat' => array(
		'login_id' => 'SUR00634',
		'license_key' => 'cc32aed23f7c2bf00e2f0172e618e818',
	),
	'mumbai' => array(
		'login_id' => 'BOM07152',
		'license_key' => 'cc32aed23f7c2bf00e2f0172e618e818',
	),
));

include_once '../Mobile_Detect.php';

define('EMAIL_QUEUE', 1);
define('SMS_QUEUE', 1);
define('ORDER_SPLIT_QUEUE', 1);
define('ORDER_BACKUP_QUEUE', 0);
define('SHORT_ORDER_SMS_QUEUE', 0);

define('POST_ORDER_ID_FOR_CASH_DISCOUNT_COUPON', 27321);
define('RECEIPT_VOUCHER_VALIDITY_DATE', '2018-01-08');

//For Cdn images
define('NGINX_ENABLED', 1);
if (NGINX_ENABLED == 1) {

	//Domain to server Static content
	define('STATIC_CONTENT_URL', 'https://d36qiqd7gl7e25.cloudfront.net/');
	if (!defined('STATIC_CONTENT_URL_SSL')) {
		define('STATIC_CONTENT_URL_SSL', 'https://d36qiqd7gl7e25.cloudfront.net/');
	}

//    define('LOCAL_CDN_URL','http://www.cddemo.in/image/');
	//    define('LOCAL_CDN_URL_SSL','http://www.cddemo.in/image/');

	define('LOCAL_CDN_URL', 'http://cdnimages.net/');
	define('LOCAL_CDN_URL_SSL', 'http://cdnimages.net/');

} else {

	define('STATIC_CONTENT_URL', 'http://www.cddemo.in/image/');
	define('STATIC_CONTENT_URL_SSL', 'http://www.cddemo.in/image/');

	define('LOCAL_CDN_URL', 'http://www.cddemo.in/staging/image/');
	define('LOCAL_CDN_URL_SSL', 'http://www.cddemo.in/image/');
}
define('CITRUS_GENERATE_INVOICE_LINK_URL', 'https://sboxinvoice.citruspay.com/invoices/standalone');
// DB
/*define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'wholesalebox1');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

define('DB_DATABASE_CRM', 'wholesalebox_crm');*/

// define('DB_DRIVER', 'mysqli');
// define('DB_PREFIX', 'oc_');
// define('DB_WSB_PREFIX', 'wsb_');

// // Master / Slave Credentials
// define('DB_HOSTNAME', array('master' => '127.0.0.1',
// 	'slave' => '127.0.0.1')
// );
// define('DB_USERNAME', array('master' => 'root',
// 	'slave' => 'root')
// );
// define('DB_PASSWORD', array('master' => 'root',
// 	'slave' => 'root')
// );
// define('DB_DATABASE', array('master' => 'wholesalebox1',
// 	'slave' => 'wholesalebox1')
// );
// define('DB_DATABASE_CRM', array('master' => 'wholesalebox_crm',
// 	'slave' => 'wholesalebox_crm')
// );
// define('DB_DATABASE_SRM', array('master' => 'wholesalebox_srm',
// 	'slave' => 'wholesalebox_srm')
// );
// define('DB_PORT', array('master' => '3306',
// 	'slave' => '3306')
// );

define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'cddemo');
define('DB_PORT', '8888');
define('DB_PREFIX', 'oc_');
define('DB_WSB_PREFIX', 'wsb_');
// define('DB_SERVERS', '127.0.0.1');
define('DB_SERVERS', array('master' => 'localhost', 'slave' => 'localhost'));

define('TAX_CLASS_ID_FOR_FOOTWEAR', 20);
define('LOWEST_RANGE_FOR_FOOTWEAR', 500);
define('LOWEST_GST_FOR_FOOTWEAR', 5);
define('HIGHEST_GST_FOR_FOOTWEAR', 18);

//SOLR
define('SOLR_ENABLED', 0);
define('SOLR_WSBOX_ENABLED', 0);

define('SOLR_HOST', 'localhost');
define('SOLR_PORT', '8983');
define('SOLR_PATH', '/solr/wsbox/');
define('SOLR_PATH_AUTO_SUGGESTIONS', '/solr/wsbox_auto_suggester/');
define('SOLR_PATH_ORDER', '/solr/orders/');
define('CST_CLASS_ID', 12);

// Fedex Constants
define('FEDEX_TEST_ACCOUNT_NO', '510087640');
define('FEDEX_TEST_METER_NO', '118791420');
define('FEDEX_TEST_KEY', 'VGQicxPyu0ZmunoD');
define('FEDEX_TEST_PASSWORD', 'LNwURjbjhdqBf0fvdXRh7pKPp');

//Fedex - DEMO
define('FEDEX_API_URL', 'https://wsbeta.fedex.com:443/web-services');
define('FEDEX_KEY', 'dLtCwOhiprXlBg0x');
define('FEDEX_PASSWORD', 'LwCjBVq38bBlc3S3Ax4WVO3em');
define('FEDEX_ACCOUNT_NUMBER', '604794161');
define('FEDEX_METER_NUMBER', '118992304');

//Connect india logistics
//Demo/Staging
define('CONNECT_INDIA_API_URL', 'http://cidemo.ap-southeast-1.elasticbeanstalk.com/api/consignments');
define('CONNECT_INDIA_ACCESS_ID', 'F4cPLbETDsdr6rkVyrhxmGA153s4B');
define('CONNECT_INDIA_ACCESS_KEY', '7gw9HWOuK8TPb6OutShxV9KujA7LdOS0TmS');

define('WSB_STORE_SELLERS', '26647 , 126');

define('SMSGUPSHUP_URL', 'http://enterprise.smsgupshup.com/GatewayAPI/rest?method=sendMessage&userid=2000150141&password=mFnhd8&v=1.1&msg_type=TEXT&auth_scheme=PLAIN&format=text');
define('OTPGUPSHUP_URL', 'http://enterprise.smsgupshup.com/GatewayAPI/rest?method=sendMessage&userid=2000167850&password=7q39Qk&v=1.1&msg_type=TEXT&auth_scheme=PLAIN&format=text');
define('TELECALLING_LATEST_APK_VERSION', 18);
//for sql profiling changes in backend config
//For sql profiling get parameter value

define('DEBUG_SQL_PROFILE', 'wsb@xyz123');
//UPI Constants
define('UPI_INITIATE_COLLECT_URL_STAGING', 'http://180.92.171.239:8080/upi/Merchant/Services/InitiateCollect/V1/WholesaleBox');
define('UPI_QUERY_TRANSACTION_URL_STAGING', 'http://180.92.171.239:8080/upi/Merchant/Services/QueryTransaction/V1/WholesaleBox');
define('UPI_INITIATE_COLLECT_URL', 'https://upiapp.infrasofttech.com/upi/Merchant/Services/InitiateCollect/V1/WholesaleBox');
define('UPI_QUERY_TRANSACTION_URL', 'https://upiapp.infrasofttech.com/upi/Merchant/Services/QueryTransaction/V1/WholesaleBox');
define('RAZORPAY_WEBHOOK_SECRET', 'QUsfcBnrrxhG9vEWPnkB96QWx3VXtaTi');
define('AAKARA_PRODUCT_IDS', array('61' => '159564,157450',
	'125' => '159564,157450',
	'70' => '159564,157450',
	'73' => '159564,157450'));
define('PRIORITY_PRODUCTS_SHUFFLE', 1);

define('STORES_OPEN_FOR_ONLINE_ORDER', array('JP', 'ST', 'DL', 'MU'));

//NeoGrowth APIs
define('NEOPAYLATER_BUYER_TRANSACTION_API', 'https://capdev.advancesuite.in:3018/neopaylater_buyer_transaction');
define('NEOPAYLATER_TRANSACTION_STATUS_API', 'https://capdev.advancesuite.in:3018/get_neopaylater_transaction_status');
$product_rating_config = array(
	'3' => 'Average',
	'4' => 'Good',
	'5' => 'Excellent',
);
define('PRODUCT_RATING_CONFIG', $product_rating_config);
define('CRM_FRANCHISE_ROLE_ID', 13);
define('DUMMY_SELLER_ID', 1);
define('DUMMY_SELLER_CODE', 'wsb_fr');
define('WSBOX_CRM_URL', '');
define('UMA_USER_ID', '');
define('CRM_UPDATE_LEAD_QUEUE', '');
define('CART_BANNER_MOBILE_APP', 22);
// added, Vikas , 18-June-2018

$allowed_ip_address = array(
	'127.0.0.1',
	'182.75.145.34',
	'182.74.29.242',
	'128.199.91.8',
	'182.74.29.243',
	'220.227.5.73',
	'175.111.130.14',
	'122.161.85.97',
);
define('ALLOWED_IP_ADDRESS', $allowed_ip_address);
define('MASTER_CHAABEE', '123456');
$user_chaabee_access = array(
	'1' => '1234567',
	'38' => '456789',
	'41' => '123789',
);
define('USER_CHAABEE_ACCESS', $user_chaabee_access);
define('CATALOG_CATEGORIES', '');
define('DEFAULT_SELLER_COMMISSION', '');
define('DIR_DLOAD_SLR_RPMT_NOTE', '');

define('CREDIT_PAYMENT_CODES', array('credit', 'lazypay', 'wsb_credit', 'wsb_credit_card', 'mswipe_credit'));

define('SITE_ENVIRONMENT', 'Test');

define('SHORT_SMS_PERMISSION', array('1'));

define('SMS_CRITERIA_ICON_PERMISSION', array());

define('CRM_URL', '');

//define('WEBENGAGE_LICENSE_CODE', '14646465');

define('RDS_SMSLOG_HOST', '127.0.0.1');
define('RDS_SMSLOG_USER', 'root');
define('RDS_SMSLOG_PASSWORD', 'wsbox@123');
define('RDS_SMSLOG_DB', 'wholesalebox');

define('WEBENGAGE_LICENSE_CODE', '~c2ab2836');
define('WEBENGAGE_API_KEY', '126a89eb-237b-447e-94cc-09e4981a146a');
define('ENABLE_ROUTE_PROFILING', 0);
include_once '../email_ids.php';
