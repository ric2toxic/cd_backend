<?php
// HTTP
define('HTTP_DOMAIN', 'wsb.in');
define('HTTP_SERVER', 'http://www.wsb.in/');
define('INDIA_STORE_HOST', 'www.wsb.in');
define('INTERNATIONAL_STORE_HOST', 'www.wsb.co');
// HTTPS
define('HTTPS_SERVER', 'http://www.wsb.in/');

//International Store id
define('INTERNATIONAL_STORE_ID', '2');
define('SOR_STORE_ID', '9');
define('SOR_STORE_URL', 'http://sor.wsb.in/');
define('WSB_STORES_ID', '0,2,9');

define('STATIC_CONTENT_URL','http://cdnimg.net/');
define('STATIC_CONTENT_URL_SSL','https://cdnimg.net/');

include_once 'Mobile_Detect.php';
$detect = new Mobile_Detect_Class();
if(isset($_COOKIE['testmobile']) && $_COOKIE['testmobile'] == 1) {

	define('DIR_TEMPLATE', '/var/www/html/wholesalebox/catalog/view/theme_new_mobile/');
	define('CONFIG_IS_MOBILE', 1);
}else{
	if ($detect->isMobile() || $detect->isTablet() ){
		define('CONFIG_IS_MOBILE', 1);
		define('DIR_TEMPLATE', '/var/www/html/wholesalebox/catalog/view/theme_new_mobile/');
	}else{
		define('CONFIG_IS_MOBILE', 0);
		define('DIR_TEMPLATE', '/var/www/html/wholesalebox/catalog/view/theme/');
	}

}

define('DIR_COMMON_TEMPLATE', '/var/www/html/wholesalebox/catalog/view/theme/');

//Webite phone & whatsapp numbers
define('WHATSAPP_NUMBER','+918696491521');
define('PHONE_NUMBER','+918696491521');

define('LATEST_API_VERSION', '2');

//ANDROID APP VERSION
define('ANDROID_APP_VERSION','23');
define('ANDROID_UNSTABLE_APK_ARRAY', '');
define('ANDROID_MINIMUM_APP_VERSION_CODE_REQUIRED',23);
define('ANDROID_API_KEY', 'ApiKey2015Android'); // Api Key For Android
define('API_ACCESS_KEY', 'AIzaSyCqnxMbpr2Y-51AH5bdIO7sR7v-oJQF4iE'); //Google api key gcm
define('ANDROID_LATEST_APP_VERSION', '2'); //API VERSION

//IOS APP VERSION
define('IOS_APP_VERSION','23');
define('IOS_UNSTABLE_APK_ARRAY', '');
define('IOS_MINIMUM_APP_VERSION_CODE_REQUIRED',23);
define('IOS_API_KEY', 'ApiKey2015Android'); //Api Key For IOS
define('IOS_API_ACCESS_KEY', 'AIzaSyCqnxMbpr2Y-51AH5bdIO7sR7v-oJQF4iE'); //IOS api key gcm
define('IOS_LATEST_APP_VERSION', '2'); //IOS API VERSION


define('CHECKOUT_LINK','1');
define('QUALITY_EXPECTATION_POPUP',0);
// DIR
define('DIR_APPLICATION', '/var/www/html/wholesalebox/catalog/');
define('DIR_SYSTEM', '/var/www/html/wholesalebox/system/');
define('DIR_LANGUAGE', '/var/www/html/wholesalebox/catalog/language/');
define('DIR_CONFIG', '/var/www/html/wholesalebox/system/config/');
define('DIR_IMAGE', '/var/www/html/wholesalebox/image/');
define('DIR_CACHE', '/var/www/html/wholesalebox/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/wholesalebox/system/download/');
define('DIR_UPLOAD', '/var/www/html/wholesalebox/system/upload/');
define('DIR_MODIFICATION', '/var/www/html/wholesalebox/system/modification/');
define('DIR_LOGS', '/var/www/html/wholesalebox/system/logs/');
define('DIR_CATALOG', '/var/www/html/wholesalebox/catalog/');

define('DIR_BASE', '/var/www/html/');
define('DIR_DLOAD_SLR_INV', 'download/seller_invoice/');
define('DIR_DLOAD_GATI_DKT', 'download/gati_docket_csv/');
define('DIR_DLOAD_SHP_LBL', 'download/shipping_label/');
define('DIR_DLOAD_SLR_DBT_NOTE', '');
define('DIR_DLOAD_BYR_INV', '/var/www/html/wholesalebox/download/buyer_invoice/');
define('DIR_DLOAD_BYR_CDT_NOTE', '/var/www/html/wholesalebox/download/cradit_note/');
// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'wholesalebox');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
define('DB_WSB_PREFIX', 'wsb_');
define('DB_DATABASE_CRM', 'wsbox_cakecrm');

//SRM DB
define('DB_DRIVER_SRM', 'mysqli');
define('DB_HOSTNAME_SRM', '127.0.0.1');
define('DB_USERNAME_SRM', 'root');
define('DB_PASSWORD_SRM', '123456');
define('DB_DATABASE_SRM', 'wsb_bd_crm');

define('SRM_TEAM_LEAD_USER_ID', '41');
define('SRM_UPDATE_LEAD_QUEUE','0');

//SOLR
define('SOLR_NOT_IN_SELLER', '61,2');
define('SOLR_ENABLED', 1);
define('SOLR_HOST', 'localhost');
define('SOLR_PORT', '8983');
define('SOLR_WSBOX_ENABLED', 1);
define('SOLR_PATH', '/solr/wsbox/');
define('SOLR_PATH_AUTO_SUGGESTIONS', '/solr/wsbox_auto_suggester/');
define('SOLR_PATH_Payment_Transactions', '/solr/wsbox_payment_transactions/');

//RabbitMQ
define('EMAIL_QUEUE', 0);
define('SMS_QUEUE', 0);
define('ORDER_SPLIT_QUEUE',0);
define('PUSH_NOTIFICATION_QUEUE', 0);
define('PUSH_SYNC_PRODUCT_SOLR_QUEUE', 0);

//Amazon S3
define('S3_ENABLED', 0);

//PAYMENT_GATEWAY
define('PAYMENT_GATEWAY','citrus');

//CITRUS sandbox credential
//Merchant sandbox Access Key: B5XEE12BI2G85CIXBX3Q
//sandbox API Key: 0b38019b08756a879d18c1819d112b036ddd0e74
//Production
// define('CITRUS_GENERATE_INVOICE_LINK_URL','https://invoice.citruspay.com/invoices/standalone');
// define('CITRUS_SEARCHING_TRANSACTIONS_BY_DATE_URL','https://admin.citruspay.com/api/v2/txn/search');
// define('CITRUS_TRANSACTIONS_ENQUIRY_URL','https://admin.citruspay.com/api/v2/txn/enquiry/');
// define('CITRUS_REFUND_PAYMENT_URL','https://admin.citruspay.com/api/v2/txn/refund');
//Sandbox
define('CITRUS_GENERATE_INVOICE_LINK_URL','https://sboxinvoice.citruspay.com/invoices/standalone');
define('CITRUS_SEARCHING_TRANSACTIONS_BY_DATE_URL','https://sboxinvoice.citruspay.com/api/v2/txn/search');
define('CITRUS_TRANSACTIONS_ENQUIRY_URL','https://sandboxadmin.citruspay.com/api/v2/txn/enquiry/');
define('CITRUS_REFUND_PAYMENT_URL','https://sandboxadmin.citruspay.com/api/v2/txn/refund');

define('international_citrus_vanityurl','wholesaleboxco');
define('international_citrus_access_key','FVO50NMJXYFYWBQ5R7XW-TEST');
define('international_citrus_secret_key','0b0f90044f6f3f59d71135a9d2444ce67fba92e3_TEST');

//Paytm Constants 
define('PAYTM_SECRET_KEY', 'asde1234098723#$');
define('PAYTM_PAYMENT_URL_PROD', 'https://secure.paytm.in/oltp-web/processTransaction');
define('PAYTM_STATUS_QUERY_URL_PROD', 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_PAYMENT_URL_TEST', 'https://pguat.paytm.com/oltp-web/processTransaction');
define('PAYTM_STATUS_QUERY_URL_TEST', 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_CALLBACK_URL_TAIL_PART', '/index.php?route=payment/paytm/callback');

// Totals
define('CST_CLASS_ID', 12);

//Wsbox-CRM
define('CRM_TEAM_LEAD_USER_ID', '41');
define('VANDANA_USER_ID', '175');
define('SALE_KHAN_USER_ID', '381');
define('CRM_UPDATE_LEAD_QUEUE','0');
define('UMA_USER_ID','565');