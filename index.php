<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//// Opencart Version
define('VERSION', '2.0.3.1');

// Configuration
if (is_file('config.php')) {
	require_once 'config.php';
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

try {

	require_once DIR_SYSTEM . 'startup.php';

// Registry
	$registry = new Registry();

// Loader
	$loader = new Loader($registry);
	$registry->set('load', $loader);

// Config
	$config = new Config();
	$registry->set('config', $config);

// Database
	$db = new Database\DB(DB_SERVERS);
	$registry->set('db', $db);

// Cache
	$cache = new Cache('file');
	$registry->set('cache', $cache);

// Store
	$store_id = 0;
	$ssl_url = 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/';

	if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
		$ssl_url = 'https://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/';
	}

	$store_cache = $cache->get('store');
	if (!empty($store_cache)) {
		foreach ($store_cache as $store) {
			if ($store['ssl'] == $ssl_url) {
				$store_id = $store['store_id'];
				break;
			}
		}

	} else {
		$store_query = $db->query("SELECT `store_id`, `name`, `url`, `ssl` FROM " . DB_PREFIX . "store  ORDER BY url");
		$cache->set('store', $store_query->rows);
		foreach ($store_query->rows as $store) {
			if ($store['ssl'] == $ssl_url) {
				$store_id = $store['store_id'];
				break;
			}
		}
	}

	$config->set('config_store_id', $store_id);

// Store Setting
	$store_setting_cache = $cache->get('store_setting');
	if (empty($store_setting_cache)) {
		$query = $db->query("SELECT `setting_id`, `serialized`, `value`, `store_id`, `key`, `code`  FROM `" . DB_PREFIX . "setting` ORDER BY store_id ASC");
		$store_setting_cache = $query->rows;
		$cache->set('store_setting', $store_setting_cache);
	}

	foreach ($store_setting_cache as $result) {
		if ($result['store_id'] == 0 || $result['store_id'] == $store_id) {
			if (!$result['serialized']) {
				$config->set($result['key'], $result['value']);
			} else {
				$config->set($result['key'], unserialize($result['value']));
			}
		}
	}

	if ($store_id == 0) {
		$config->set('config_url', HTTP_SERVER);
		$config->set('config_ssl', HTTPS_SERVER);
	}

// Log
	$log = new Log($config->get('config_error_filename'));
	$registry->set('log', $log);

	function error_handler($errno, $errstr, $errfile, $errline) {
		global $log, $config;

		// error suppressed with @
		if (error_reporting() === 0) {
			return false;
		}

		switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
		}

		$debug_mode = false;
		if (isset($_GET['debug']) and $_GET['debug'] == 'yup') {
			echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
			$debug_mode = true;
		}
		if ($config->get('config_error_display')) {
			echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
		}

		///*if ($config->get('config_error_log')) {
		//$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
		//}*/

		if ($error == 'Fatal Error' and !$debug_mode) {
			// String should match exactly to the one mentioned above
			$redirect_url = sprintf("http://%s%smaintenance.html",
				/*isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',*/
				$_SERVER['SERVER_NAME'],
				$_SERVER['REQUEST_URI']);

			header("Location: " . $redirect_url);
			exit();

		}

		return true;
	}

// Error Handler
	set_error_handler('error_handler');

// Request
	$request = new Request();
	$registry->set('request', $request);

// Response
	$response = new Response();

	$response->addHeader('Content-Type: text/html; charset=utf-8');

	$response->setCompression($config->get('config_compression'));

	$registry->set('response', $response);

//mobile country code
	$mobile_country_code = array('CO' => array('AE' => '+971', 'AU' => '+61', 'BD' => '+880', 'CA' => '+1', 'IN' => '+91', 'MY' => '+60', 'OM' => '+968', 'SA' => '+966', 'GB' => '+44'), 'IN' => array('IN' => '+91'));
	$registry->set('mobile_country_code', $mobile_country_code);

// Getting all languages
	$languages = $cache->get('language');
	if (!$languages) {
		$query = $db->query("SELECT language_id,
                              name,
                              code,
                              locale,
                              image,
                              directory,
                              status
                              FROM `" . DB_PREFIX . "language`
                              WHERE status = '1' ORDER BY NULL");
		foreach ($query->rows as $result) {
			$languages[$result['code']] = $result;
		}
		$cache->set('language', $languages);
	}

// Default language set to English
	$code = 'en';

	$is_app = false;
// First for App - so that we can identify if it is app or not
	$obj_restapi = new Restapi($registry);
	if (($request->server['REQUEST_METHOD'] == 'POST')) {
		$inputJSON = file_get_contents('php://input');
		$app_request_data = json_decode($inputJSON, TRUE);

		if ((!empty($app_request_data['email'])
			&& (in_array($app_request_data['email'], array('test58@gmail.com', '3232323232'))))
			|| (!empty($app_request_data['mobile'])
				&& (in_array($app_request_data['mobile'], array('test58@gmail.com', '3232323232'))))) {

			log_app_test_data($app_request_data);
		}

		$headers = apache_request_headers();

		// Check if it is app call
		if (!empty($headers['SIGNATURE']) || !empty($headers['REQUEST_BY'])) {
			$is_app = true;

			if (MAINTENANCE_MODE == 1) {
				$rt = array();
				if (isset($request->get['route'])
					&&
					($request->get['route'] == "restapi/restapi/get_products_by_category/"
						|| $request->get['route'] == "restapi/restapi/get_products_by_category")) {
					$rt['error_code'] = '1111';
				} else {
					$rt['error_code'] = '8888';
				}

				$rt['status'] = '0';
				$rt['status_text'] = '';
				$rt['web_url'] = MAINTENANCE_URL;
				$rt['message'] = 'Planned Maintenance is in progress. We will be back soon.';
				echo json_encode($rt);exit;
			}
		}

		$obj_restapi->setRequestData($app_request_data);
		$obj_restapi->setRequestHeader($headers);

		if (isset($app_request_data['access_token']) && !empty($app_request_data['language'])) {
			$code = $app_request_data['language'];
			if (empty($languages[$code]['language_id'])) {
				$code = 'en';
			}
		}

		if (isset($app_request_data['access_token']) && isset($app_request_data['user_id']) && $app_request_data['user_id'] > 0) {
			$registry->set('customer_id', $app_request_data['user_id']);
		} else {
			$registry->set('customer_id', 0);
		}
	}
	$registry->set('restapi', $obj_restapi);

// Creating session variable
	$session = null;

// Language for website
	if (!$is_app) {

		// Session - to be created only when it is not App
		$session = new Session();
		$registry->set('session', $session);

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

			if ($detect) {
				$code = $detect;
			}
		}

		if (!isset($session->data['language']) || $session->data['language'] != $code) {
			$session->data['language'] = $code;
		}

		if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
			setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
		}

	}

// Url
	$url = new Url($config->get('config_url'),
		$config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'),
		$registry);
	$registry->set('url', $url);

// Setting Language in config variable
	$config->set('config_language_id', $languages[$code]['language_id']);
	$config->set('config_language', $languages[$code]['code']);

// Creating the finalized Language object and storing in registry
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
// save campaign id in cookie for 30 days if campaing id is set into url
	if (isset($_REQUEST['campaign_event_no']) && !empty($_REQUEST['campaign_event_no'])) {
		setcookie('campaign_event_no', $_REQUEST['campaign_event_no'], time() + 2592000, '/');
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
//$registry->set('wsbcart', new WsbCart($registry));
	$registry->set('backendcart', new BackendCart($registry));

// Secure File Download Class
	$registry->set('securefiledownload', new SecureFileDownload($registry));

// Encryption
	$registry->set('encryption', new Encryption($config->get('config_encryption')));

//OpenBay Pro
	$registry->set('openbay', new Openbay($registry));

// Event
	$event = new Event($registry);
	$registry->set('event', $event);

/*$query = $db->query("SELECT * FROM " . DB_PREFIX . "event");

foreach ($query->rows as $result) {
$event->register($result['trigger'], $result['action']);
}*/

// Front Controller
	$controller = new Front($registry);

// Maintenance Mode
	$controller->addPreAction(new Action('common/maintenance'));

// SEO URL's
	$controller->addPreAction(new Action('common/seo_url'));

//Check mobile site
	/*if(CONFIG_IS_MOBILE == 1)
	{
	    if (strpos($request->get['route'], 'account') !== false)
	    {
	        if(empty($_SERVER['HTTP_X_REQUESTED_WITH']))
	       {
	          header('Location: '.HTTPS_SERVER);
	       }
	    }
*/

// Router

	if (isset($request->get['route'])) {

		if (CONFIG_IS_MOBILE == 0) {
			if ($request->get['route'] == "product/product") {
				$request->get['route'] = "react/product";
			}

			if ($request->get['route'] == "product/category" || $request->get['route'] == "product/search") {
				$request->get['route'] = "react/list";
			}
			if ($request->get['route'] == "account/order" || $request->get['route'] == "account/order/info" || $request->get['route'] == "account/success") {
				$request->get['route'] = "react/my_account";
			}
			if ($request->get['route'] == "account/statement") {
				$request->get['route'] = "react/account_statement";
			}
			if ($request->get['route'] == "account/edit") {
				$request->get['route'] = "react/profile/";
			}
			if ($request->get['route'] == "account/address" || $request->get['route'] == "account/address/add" || $request->get['route'] == "account/address/edit") {
				$request->get['route'] = "react/profile/address";
			}
			if ($request->get['route'] == "account/bank_details") {
				$request->get['route'] = "react/profile/bank_details";
			}
			if ($request->get['route'] == "account/wishlist") {
				$request->get['route'] = "react/wishlist";
			}

			if ($request->get['route'] == "account/return/getReturns") {
				$request->get['route'] = "react/returns";
			}

			if ($request->get['route'] == "account/helpdesk" || $request->get['route'] == "account/helpdesk/createHelpdeskTicket" || $request->get['route'] == "account/helpdesk/viewSingleTicketAndConversation") {
				$request->get['route'] = "react/support";
			}
		} else {
			if ($request->get['route'] == "product/category" && (!empty($request->get['search']) || (isset($request->get['store_product']) && isset($request->get['purchase_days'])))) {
				$request->get['route'] = "product/search";
			}

			if ($request->get['route'] == "account/order" || $request->get['route'] == "account/order/info") {
				header('Location: ' . HTTPS_SERVER . 'account/order_history');
				exit();
			}
		}

		// For Desktop and mobile web - check if controller is payment then master DB will be used.
		$splitRoute = explode('/', $request->get['route']);
		if (isset($splitRoute) && ($splitRoute[0] == "payment" || $splitRoute[0] == "checkout")) {
			$db->useWriteDbOnly();
		}

		$action = new Action($request->get['route']);

	} else {
		if (CONFIG_IS_MOBILE == 0) {

			//$action = new Action('common/home');
			$action = new Action('react/home');

		} else {

			$action = new Action('common/home');
		}

	}

	function log_app_test_data($app_request_data) {
		$app_test_data = "Date of Request: " . date("d/m/Y H:i:s") . "\n";
		$app_test_data .= "Request URL: " . $_SERVER['REQUEST_URI'] . "\n";
		$app_test_data .= "Request Data: " . json_encode($app_request_data) . "\n\n\n";

		$file = fopen(DIR_LOGS . "app_google_test_logs.txt", "a");
		fwrite($file, $app_test_data);
		fclose($file);
	}

	function mailException($exception, $config, $subject = '') {
		exit;
		$error_trace = $exception->getTrace();
		$error_trace_to_mail = array();

		//Unset error array keys greater then 4, to ignore unneccesary data args
		foreach ($error_trace as $trace) {

			$class = $trace['class'] ?? '';
			if (empty($class) || $class === 'Action') {
				break;
			}

			$error_trace_to_mail[] = $trace;
		}

		$body = "Exception Error:  " . $exception->getCode() . ': ' . $exception->getMessage() . ", " . $exception->getFile() . ",  " . $exception->getLine();

		$body .= "<br><br><br>";

		$body .= "<pre>" . print_r($error_trace_to_mail, true) . "</pre>";

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $config->get('config_mail_smtp_hostname');
		$mail->Port = $config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Username = $config->get('config_mail_smtp_username');
		$mail->Password = $config->get('config_mail_smtp_password');
		$mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
		$mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
		$mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
		$mail->Subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
		$mail->msgHTML($body);
		$mail->send();
	}

// Dispatch
	$controller->dispatch($action, new Action('error/not_found'));

// Output
	$response->output();

} catch (Throwable $e) {

	echo '<pre>';
	print_r($e);exit;
	// Handle error
	$redirect_url = sprintf("http://%s/maintenance.html",
		$_SERVER['SERVER_NAME']);
	// $this->mailException($e, $config, 'Fatal Error in Live Site - ' . date('d/M/Y h:i:s'));
	header("Location: " . $redirect_url);
	exit();
}
