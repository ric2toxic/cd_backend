<?php
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new Database\DB( DB_SERVERS );
$registry->set('db', $db);


// Store
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
    $store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
} else {
    $store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
}

if ($store_query->num_rows) {
    $config->set('config_store_id', $store_query->row['store_id']);
} else {
    $config->set('config_store_id', 0);
}

// Settings
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $result) {
    if (!$result['serialized']) {
        $config->set($result['key'], $result['value']);
    } else {
        $config->set($result['key'], unserialize($result['value']));
    }
}

if (!$store_query->num_rows) {
    $config->set('config_url', HTTP_SERVER);
    $config->set('config_ssl', HTTPS_SERVER);
}

// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $url);

// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);
// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();

$response->addHeader('Content-Type: text/html; charset=utf-8');

$response->setCompression($config->get('config_compression'));

$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
$session = new Session();
$registry->set('session', $session);

// Language Detection
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'");

foreach ($query->rows as $result) {
    $languages[$result['code']] = $result;
}

if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages)) {
    $code = $session->data['language'];
} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages)) {
    $code = $request->cookie['language'];
} else {
    $detect = '';

    if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
        $browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

        foreach ($browser_languages as $browser_language) {
            foreach ($languages as $key => $value) {
                if ($value['status']) {
                    $locale = explode(',', $value['locale']);

                    if (in_array($browser_language, $locale)) {
                        $detect = $key;
                        break 2;
                    }
                }
            }
        }
    }

    $code = $detect ? $detect : $config->get('config_language');
}


if (!isset($session->data['language']) || $session->data['language'] != $code) {
    $session->data['language'] = $code;
}

if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
    setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
}


// To change language in app
if (($request->server['REQUEST_METHOD'] == 'POST')) {
    $inputJSON = file_get_contents('php://input');
    $app_request_data = json_decode( $inputJSON, TRUE );
    if(isset($app_request_data['access_token']) && isset($app_request_data['language'])){
        $code = $app_request_data['language'];
        if(!isset($languages[$code]['language_id']) || empty($languages[$code]['language_id'])){
            $code = 'en';
        }
    }
}

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language
$language = new Language($languages[$code]['directory']);
$language->load($languages[$code]['directory']);
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// Customer
$customer = new Customer($registry);
$registry->set('customer', $customer);

// Tracking Code
if (isset($request->get['tracking'])) {
    setcookie('tracking', $request->get['tracking'], time() + 3600 * 24 * 1000, '/');

    $db->query("UPDATE `" . DB_PREFIX . "marketing` SET clicks = (clicks + 1) WHERE code = '" . $db->escape($request->get['tracking']) . "'");
}

// Affiliate
$registry->set('affiliate', new Affiliate($registry));

// Currency
$registry->set('currency', new Currency($registry));

// Tax
$registry->set('tax', new Tax($registry));

// Weight
$registry->set('weight', new Weight($registry));

// Length
$registry->set('length', new Length($registry));

// WSB common library
$registry->set('wsb', new Wsb($registry));

// Cart
$registry->set('cart', new Cart($registry));
// AppCart
$registry->set('appcart', new AppCart($registry));
$registry->set('backendcart', new BackendCart($registry));

// Encryption
$registry->set('encryption', new Encryption($config->get('config_encryption')));

//OpenBay Pro
$registry->set('openbay', new Openbay($registry));

// Event
$event = new Event($registry);
$registry->set('event', $event);

$query = $db->query("SELECT * FROM " . DB_PREFIX . "event");

foreach ($query->rows as $result) {
    $event->register($result['trigger'], $result['action']);
}

// Output
$response->setCompression($config->get('config_compression'));
$response->output();
