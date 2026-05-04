<?php
// HTTP
define('HTTP_DOMAIN', 'http://www.cddemo.in/');
define('HTTP_SERVER', 'http://www.cddemo.in/');
define('INDIA_STORE_HOST', 'http://www.cddemo.in/');
define('INTERNATIONAL_STORE_HOST', 'http://www.cddemo.in/');
// HTTPS
define('HTTPS_SERVER', 'http://www.cddemo.in/');
define('HTTPS_SERVER_INTERNATIONAL', 'http://www.cddemo.in/');

define('DIR_WSB_PURCHASE_IMAGE', '/Applications/MAMP/htdocs/cddemo/image/wsb_purchase/');
//International Store id
define('INTERNATIONAL_STORE_ID', '2');
define('SOR_STORE_ID', '9');
define('SOR_STORE_URL', 'http://sor.wsb.in/');
define('WSB_STORES_ID', '0,2,9');
define('HTTPS_CATALOG', 'http://www.cddemo.in/');

//define('international_citrus_vanityurl','wholesalebox1co');
//define('international_citrus_access_key','FVO50NMJXYFYWBQ5R7XW-TEST');
//define('international_citrus_secret_key','0b0f90044f6f3f59d71135a9d2444ce67fba92e3_TEST');
define('DIR_SYSTEM_IMAGE', '/Applications/MAMP/htdocs/cddemo/system/library/image/');
define('KEYBOARD_DISABLED', 0);
include_once 'Mobile_Detect.php';
$detect = new Mobile_Detect_Class();
if (isset($_COOKIE['testmobile']) && $_COOKIE['testmobile'] == 1) {

	define('DIR_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/catalog/view/theme_new_mobile/');
	define('CONFIG_IS_MOBILE', 1);
} else {
	if ($detect->isMobile() || $detect->isTablet()) {
		define('CONFIG_IS_MOBILE', 1);
		define('DIR_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/catalog/view/theme_new_mobile/');
	} else {
		define('CONFIG_IS_MOBILE', 0);
		define('DIR_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/catalog/view/theme/');
	}

}
define('DIR_ADMIN_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/main_vibhag/view/template/');
define('DIR_COMMON_TEMPLATE', '/Applications/MAMP/htdocs/cddemo/catalog/view/theme/');

//APP VERSION
define('ANDROID_APP_VERSION', '23');
//Webite phone & whatsapp numbers
define('WHATSAPP_NUMBER', '+918696491521');
define('PHONE_NUMBER', '+918696491521');
// Api Key For Android
define('ANDROID_API_KEY', 'ApiKey2015Android');
define('QUALITY_EXPECTATION_POPUP', 0);
define('LATEST_APP_VERSION', 32);
// DIR
define('DIR_APPLICATION', '/Applications/MAMP/htdocs/cddemo/catalog/');
define('DIR_ADMIN', '/Applications/MAMP/htdocs/cddemo/khufiya_vibhag/');
define('DIR_SYSTEM', '/Applications/MAMP/htdocs/cddemo/system/');
define('DIR_LANGUAGE', '/Applications/MAMP/htdocs/cddemo/catalog/language/');
define('DIR_CONFIG', '/Applications/MAMP/htdocs/cddemo/system/config/');
define('DIR_IMAGE', '/Applications/MAMP/htdocs/cddemo/image/');
define('DIR_CACHE', '/Applications/MAMP/htdocs/cddemo/system/cache/');
define('DIR_DOWNLOAD', '/Applications/MAMP/htdocs/cddemo/system/download/');
define('DIR_UPLOAD', '/Applications/MAMP/htdocs/cddemo/system/upload/');
define('DIR_MODIFICATION', '/Applications/MAMP/htdocs/cddemo/system/modification/');
define('DIR_LOGS', '/Applications/MAMP/htdocs/cddemo/system/logs/');
define('DIR_CATALOG', '/Applications/MAMP/htdocs/cddemo/catalog/');
define('DIR_DLOAD_SLR_INV', '/Applications/MAMP/htdocs/cddemo/download/seller_invoice/');
define('DIR_DLOAD_BYR_INV', '/Applications/MAMP/htdocs/cddemo/download/buyer_invoice/');
define('DIR_DLOAD_GATI_DKT', '/Applications/MAMP/htdocs/cddemo/download/gati_docket_csv/');
define('DIR_DLOAD_SHP_LBL', '/Applications/MAMP/htdocs/cddemo/download/shipping_label/');
define('DIR_DLOAD_SLR_DBT_NOTE', '/Applications/MAMP/htdocs/cddemo/download/debit_note/');
define('DIR_DLOAD_BYR_CDT_NOTE', '/Applications/MAMP/htdocs/cddemo/download/credit_note/');
define('DIR_DLOAD_RCT_INV', '/Applications/MAMP/htdocs/cddemo/download/receipt_voucher/');
define('DIR_DLOAD', '/var/downloads/');
define('DEBUG_SQL_STATUS', 0);
define('DIR_BASE', '/Applications/MAMP/htdocs/cddemo/');
define('DIR_UNITTEST', '/Applications/MAMP/htdocs/cddemo/unitTest/Test/');

define('POST_ORDER_ID_FOR_CASH_DISCOUNT_COUPON', 27321);
define('RECEIPT_VOUCHER_VALIDITY_DATE', '2018-01-08');

//seller panel landing page after login
define('SELLER_PANEL_LANDING_PAGE_URL', HTTPS_SERVER . 'seller_panel/#/orders/getPickpupOrderRequested');

//For Cdn images
define('NGINX_ENABLED', 1);
if (NGINX_ENABLED == 1) {

	//Domain to server Static content
	define('STATIC_CONTENT_URL', 'https://d36qiqd7gl7e25.cloudfront.net/');
	if (!defined('STATIC_CONTENT_URL_SSL')) {
		define('STATIC_CONTENT_URL_SSL', 'https://d36qiqd7gl7e25.cloudfront.net/');
	}

	define('LOCAL_CDN_URL', 'http://www.cddemo.in/image/');
	define('LOCAL_CDN_URL_SSL', 'http://www.cddemo.in/image/');

	// define('LOCAL_CDN_URL','http://cdnimages.net/');
	// define('LOCAL_CDN_URL_SSL','http://cdnimages.net/');

} else {

	define('STATIC_CONTENT_URL', 'http://www.cddemo.in/image/');
	define('STATIC_CONTENT_URL_SSL', 'http://www.cddemo.in/image/');

	define('LOCAL_CDN_URL', 'http://www.cddemo.in/image/');
	define('LOCAL_CDN_URL_SSL', 'http://www.cddemo.in/image/');
}

define('COMMON_CSS', 'css/web/stylesheet.css?v=2');
define('COMMON_VENDOR_JS', 'js/web/vendor.js?v=2');
define('COMMON_REACT_JS', 'js/web/react-bundle.js?v=2');

// DB

/*define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'wholesalebox1');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');*/

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

// Master / Slave Credentials
// define('DB_HOSTNAME', array('master' => '127.0.0.1',
// 	'slave' => '127.0.0.1')
// );
// define('DB_USERNAME', array('master' => 'root',
// 	'slave' => 'root')
// );
// define('DB_PASSWORD', array('master' => '',
// 	'slave' => '')
// );
// define('DB_DATABASE', array('master' => 'cd',
// 	'slave' => 'wholesalebox1')
// );
// define('DB_DATABASE_CRM', array('master' => 'wholesalebox_crm',
// 	'slave' => 'wholesalebox_crm')
// );
// define('DB_DATABASE_SRM', array('master' => 'wholesalebox_srm',
// 	'slave' => 'wholesalebox_srm')
// );
// define('DB_PORT', array('master' => '8888',
// 	'slave' => '8888')
// );

define('EMAIL_QUEUE', 1);
define('SMS_QUEUE', 0);
define('ORDER_SPLIT_QUEUE', 1);
define('SOLR_ORDER_SYNC_QUEUE', 0);
define('SOLR_WSBOX_ENABLED', 1);

//SOLR
define('SOLR_ENABLED', 0);
define('SOLR_HOST', 'localhost');
define('SOLR_PORT', '8983');
define('SOLR_PATH', '/solr/wsbox/');
define('SOLR_PATH_AUTO_SUGGESTIONS', '/solr/wsbox_auto_suggester/');
define('SOLR_PATH_ORDER', '/solr/orders/');
define('SOLR_PATH_PINCODES', '/solr/pincodes/');
//Amazon S3
define('S3_ENABLED', 0);
define('payment_gateway', 'citrus');
define('CST_CLASS_ID', 12);

define('CRM_UPDATE_LEAD_QUEUE', 0);

define('ORDER_BACKUP_QUEUE', 0);

define('TAX_CLASS_ID_FOR_FOOTWEAR', 20);
define('LOWEST_RANGE_FOR_FOOTWEAR', 500);
define('LOWEST_GST_FOR_FOOTWEAR', 5);
define('HIGHEST_GST_FOR_FOOTWEAR', 18);

//PAYMENT_GATEWAY
define('PAYMENT_GATEWAY', 'citrus');

//CITRUS sandbox credential
//Merchant sandbox Access Key: B5XEE12BI2G85CIXBX3Q
//sandbox API Key: 0b38019b08756a879d18c1819d112b036ddd0e74
//Production
// define('CITRUS_GENERATE_INVOICE_LINK_URL','https://invoice.citruspay.com/invoices/standalone');
// define('CITRUS_SEARCHING_TRANSACTIONS_BY_DATE_URL','https://admin.citruspay.com/api/v2/txn/search');
// define('CITRUS_TRANSACTIONS_ENQUIRY_URL','https://admin.citruspay.com/api/v2/txn/enquiry/');
// define('CITRUS_REFUND_PAYMENT_URL','https://admin.citruspay.com/api/v2/txn/refund');
//Sandbox
define('CITRUS_GENERATE_INVOICE_LINK_URL', 'https://sboxinvoice.citruspay.com/invoices/standalone');
define('CITRUS_SEARCHING_TRANSACTIONS_BY_DATE_URL', 'https://sandboxadmin.citruspay.com/api/v2/txn/search');
define('CITRUS_TRANSACTIONS_ENQUIRY_URL', 'https://sandboxadmin.citruspay.com/api/v2/txn/enquiry/');
define('CITRUS_REFUND_PAYMENT_URL', 'https://sandboxadmin.citruspay.com/api/v2/txn/refund');

define('international_citrus_vanityurl', 'wholesaleboxco');
define('international_citrus_access_key', 'FVO50NMJXYFYWBQ5R7XW-TEST');
define('international_citrus_secret_key', '0b0f90044f6f3f59d71135a9d2444ce67fba92e3_TEST');

//Paytm Constants
define('PAYTM_SECRET_KEY', 'asde1234098723#$');
define('PAYTM_PAYMENT_URL_PROD', 'https://secure.paytm.in/oltp-web/processTransaction');
define('PAYTM_STATUS_QUERY_URL_PROD', 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_PAYMENT_URL_TEST', 'https://pguat.paytm.com/oltp-web/processTransaction');
define('PAYTM_STATUS_QUERY_URL_TEST', 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_CALLBACK_URL_TAIL_PART', '/index.php?route=payment/paytm/callback');

// Fedex Constants
define('FEDEX_TEST_ACCOUNT_NO', '510087640');
define('FEDEX_TEST_METER_NO', '118791420');
define('FEDEX_TEST_KEY', 'VGQicxPyu0ZmunoD');
define('FEDEX_TEST_PASSWORD', 'LNwURjbjhdqBf0fvdXRh7pKPp');

define('WSB_STORE_SELLERS', '16 , 26648');
define('PRODUCT_IDS_NOT_CHECK_FOR_AUTO_PROCESSING', array(10001, 5686));

define('SMSGUPSHUP_URL',
	'http://enterprise.smsgupshup.com/GatewayAPI/rest?method=sendMessage&userid=2000150141&password=SDLPu7cs2N&v=1.1&msg_type=TEXT&auth_scheme=PLAIN&format=text');
define('OTPGUPSHUP_URL', 'http://enterprise.smsgupshup.com/GatewayAPI/rest?method=sendMessage&userid=2000167850&password=7q39Qk&v=1.1&msg_type=TEXT&auth_scheme=PLAIN&format=text');

$news = array();
$news[0]['image'] = 'https://www.wholesalebox.in/blog/wp-content/uploads/2018/06/WhatsApp-Image-2018-06-06-at-12.13.57.jpeg';
$news[0]['title'] = 'WholesaleBox at Rashtrapati bhavan';
$news[0]['link'] = 'https://www.wholesalebox.in/blog/wholesalebox-raised-2-mn-at-pitchrashtrapati-bhavan-event/';
define('NEWS', $news);

define('TELECALLING_LATEST_APK_VERSION', 18);
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
define('DEFAULT_SELLER_COMMISSION', '');

include_once 'email_ids.php';
define('OFFLINE_INVOICING_PAYMENT_GATEWAYS', array('citrus', 'razorpay'));
define('OFFLINE_INVOICING_PAYMENT_GATEWAYS_CLASSES', array('Citrus', 'Razorpay'));

//Suborder from City Code
// define('SUBORDER_CITY_CODE', array(
//                              'JP' => 'Jaipur',
//                              'ST' => 'Surat',
//                              'DL' => 'New Delhi',
//                              'MU' => 'Mumbai',
//                              'BL' => 'Bengaluru',
//                              'KL' => 'Kolkata',
//                              )
//  );
define('SITE_ENVIRONMENT', 'staging');
define('DIR_DLOAD_SLR_RPMT_NOTE', '');
define('WSBOX_CRM_URL', 'www.wholesalebox.biz');
define('CRM_URL', 'www.wholesalebox.biz');

define('WSB_STATE_GST', array('1485' => '24AABCW7022Q1Z0', '1483' => '07AABCW7022Q1ZW', '1493' => '27AABCW7022Q1ZU', '1501' => '08AABCW7022Q1ZU'));

define('CATALOG_CATEGORIES', '61,72');
define('SELLER_AGREEMENT_POPUP', 1);
define('MAINTENANCE_MODE', 0);
define('MAINTENANCE_URL', 'http://www.cddemo.in/maintenance.html');
define('DIR_SELLER_UPLOADS', '/Applications/MAMP/htdocs/cddemo/system/download/');
define('WSB_PURCHASE_INVENTORY_SEARCH_TERM', '');

define('SUBORDER_CITY_CODE', array());
define('COD_BLOCKED_CUSTOMERS', array());

//Lazypay settings
define('LAZYPAY_ACCESS_KEY', 'QOJZJFZ1K180UP3XS72G');
define('LAZYPAY_SECRET_KEY', 'e3730bbe69f3108afd6eb1ba205564fc3d5eb56d');
define('LAZYPAY_SIGNATURE_MOBILE', '9898989898');
define('LAZYPAY_SIGNATURE_EMAIL', 'vipul.jain@wholesalebox.in');
define('LAZYPAY_SIGNATURE_CURRENCY', 'INR');

define('LAZYPAY_PAYMENT_BASE_URL', 'https://sboxapi.lazypay.in');
define('LAZYPAY_PAYMENT_CHECK_ELIGIBILITY', '/api/lazypay/v2/payment/eligibility');
define('LAZYPAY_PAYMENT_PREAUTH', '/api/lazypay/preAuth/v0/initiate');
define('LAZYPAY_PAYMENT_AUTHORISED_PREAUTH', '/api/lazypay/preAuth/v0/pay');
define('LAZYPAY_PAYMENT_AUTHORISED_CAPTURE', '/api/lazypay/preAuth/v0/capture');
define('LAZYPAY_PAYMENT_PREAUTH_RELEASE', '/api/lazypay/preAuth/v0/release');
define('DUMMY_INR_CURRENCY', 'IND');
define('CREDIT_APPLICATION_VERSION', '2');
define('DIR_TEMPLATE_MOBILE', '/Applications/MAMP/htdocs/cddemo/catalog/view/theme_new_mobile/');
define('SEARCHABLE_FILTER_GROUP_IDS', array());

//Vernacular Language
define('VERNACULAR_LANGUAGE', array(
	'1' => 'english',
	'2' => 'hindi',
)
);

define('CREDIT_PAYMENT_CODES', array('credit', 'lazypay', 'wsb_credit', 'wsb_credit_card', 'mswipe_credit'));

define('EXCELLENT_PRODUCT_DROPSHIPPER_IDS', array('102016'));

define('COD_AVAILABLE_LIMIT', 2500);
define('COD_AdVANCE_AMOUNT_LIMIT', 500);
define('CART_BANNER_MOBILE_APP', 22);
define('WEBENGAGE_LICENSE_CODE', '~c2ab2836');
define('WEBENGAGE_API_KEY', '126a89eb-237b-447e-94cc-09e4981a146a');
define('SRM_TEAM_LEAD_USER_ID', '');
define('SURFACE_FREE_SHIPPING', 15000);

define('UPLOAD_CONTENT_URL_SSL', 'https://cdnimages.net/');

define('RDS_SMSLOG_HOST', 'localhost');
define('RDS_SMSLOG_USER', 'root');
define('RDS_SMSLOG_PASSWORD', 'root');
define('RDS_SMSLOG_DB', 'smslogs');
define('RDS_WSB_DB', 'wholesalebox1');
define('CART_SUB_TOTAL_FOR_FREE_SHIPPING', '0');
define('SOLR_ENABLED_CRM_WEB', '0');
define('AC_SMT_BLOCK_CUSTOMERS', '0');
define('CUSTOMER_WEBSITE_DOMAIN_URL', 'fashcart.com');
define('RBL_API_ANCHORID', '');
define('RBL_API_ENDPOINTS', '');
define('RBL_API_CLIENT_ID', '');
define('RBL_API_SECRET', '');
define('RBL_API_AUTHORIZATION', '');
define('RBL_PEM_FILE_PATH', '');
define('RBL_CERT_PASSWORD', '');
