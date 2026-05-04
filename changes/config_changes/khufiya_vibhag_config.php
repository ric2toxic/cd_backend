<?php
// HTTP
define('HTTP_SERVER', 'http://www.wsb.in/khufiya_vibhag/');
define('HTTP_CATALOG', 'http://www.wsb.in/');

// HTTPS
define('HTTPS_SERVER', 'http://www.wsb.in/khufiya_vibhag/');
define('HTTPS_CATALOG', 'http://www.wsb.in/');

// WSB Stores
define('WSB_STORES_ID', '0,2,9');
define('INTERNATIONAL_STORE_ID', '2');
define('SOR_STORE_ID', '9');

//Google api key gcm
define( 'API_ACCESS_KEY', 'AIzaSyCqnxMbpr2Y-51AH5bdIO7sR7v-oJQF4iE');

// DIR
define('DIR_APPLICATION', '/var/www/html/wholesalebox/khufiya_vibhag/');
define('DIR_SYSTEM', '/var/www/html/wholesalebox/system/');
define('DIR_LANGUAGE', '/var/www/html/wholesalebox/khufiya_vibhag/language/');
define('DIR_TEMPLATE', '/var/www/html/wholesalebox/khufiya_vibhag/view/template/');
define('DIR_CONFIG', '/var/www/html/wholesalebox/system/config/');
define('DIR_IMAGE', '/var/www/html/wholesalebox/image/');
define('DIR_CACHE', '/var/www/html/wholesalebox/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/wholesalebox/system/download/');
define('DIR_UPLOAD', '/var/www/html/wholesalebox/system/upload/');
define('DIR_LOGS', '/var/www/html/wholesalebox/system/logs/');
define('DIR_MODIFICATION', '/var/www/html/wholesalebox/system/modification/');
define('DIR_CATALOG', '/var/www/html/wholesalebox/catalog/');
define('DIR_STOREFRONT', '/var/www/storefront/html/');

define('DIR_BASE', '/var/www/html/');
define('DIR_DLOAD_SLR_INV', 'download/seller_invoice/');
define('DIR_DLOAD_GATI_DKT', 'download/gati_docket_csv/');
define('DIR_DLOAD_SHP_LBL', 'download/shipping_label/');
define('DIR_DLOAD_SLR_DBT_NOTE', '/var/www/html/wholesalebox/download/debit_note/');
define('DIR_DLOAD_BYR_INV', '/var/www/html/wholesalebox/download/buyer_invoice/');
define('KEYBOARD_DISABLED', 0);

include_once '../Mobile_Detect.php';

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'wholesalebox');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

//SOLR
define('SOLR_ENABLED', 1);
define('SOLR_HOST', 'localhost');
define('SOLR_PORT', '8983');
define('SOLR_WSBOX_ENABLED', 1);
define('SOLR_PATH', '/solr/wsbox/');

//RabbitMQ
define('EMAIL_QUEUE', 0);
define('SMS_QUEUE', 0);
define('ORDER_SPLIT_QUEUE',0);
define('PUSH_NOTIFICATION_QUEUE', 0);
define('PUSH_SYNC_PRODUCT_SOLR_QUEUE', 0);

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

//Wsbox-CRM
define('UMA_USER_ID','565');




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
                                '1'  => '1234567',
                                '38' => '456789',
                                '41'  => '123789',
                            );
define('USER_CHAABEE_ACCESS', $user_chaabee_access);
