<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Version
define('VERSION', '2.0.3.1');

// Configuration
if (is_file('config.php')) {
	require_once 'config.php';
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: ../install/index.php');
	exit;
}

try {
// Startup
	require_once DIR_SYSTEM . 'startup.php';

	$request = new Request();

// Configuration - khufiya access with access chaabee
	if (is_file(DIR_SYSTEM . 'library/main_vibhag/access_keys_config.php')
		&&
		is_file(DIR_SYSTEM . 'library/main_vibhag/access_check.php')
	) {
		require_once DIR_SYSTEM . 'library/main_vibhag/access_keys_config.php';
		// IP based user checking to allow khufiya vibhag access
		require_once DIR_SYSTEM . 'library/main_vibhag/access_check.php';

		$chaabee = $request->get['chaabee'] ?? $session->data['chaabee'] ?? '';

		$remote_ip = getClientIpAddress();

		$khufiyaAccessCheckObj = new KhufiyaAccessCheck($chaabee, $remote_ip, $session);

		$access_allowed = $khufiyaAccessCheckObj->checkAccess();

		if (!$access_allowed) {

			$khufiyaAccessCheckObj->showError();
		}
	}

// Registry
	$registry = new Registry();

	// $session = new Session();
	$session = new Session();
	$session->start();
	$registry->set('session', $session);

// Config
	$config = new Config();
	$registry->set('config', $config);

// Database
	$db = new Database\DB(DB_SERVERS);
	$registry->set('db', $db);

// Cache
	$cache = new Cache('file');
	$registry->set('cache', $cache);

// Settings
	$store_setting_cache = $cache->get('store_setting');
	if (!empty($store_setting_cache)) {
		$store_setting = $store_setting_cache;
	} else {
		$query = $db->query("SELECT `setting_id`, `serialized`, `value`, `store_id`, `key`, `code`  FROM `" . DB_PREFIX . "setting` ORDER BY store_id ASC");
		$store_setting = $query->rows;
		$cache->set('store_setting', $store_setting);
	}

	foreach ($store_setting as $result) {
		if ($result['store_id'] == 0) {
			if (!$result['serialized']) {
				$config->set($result['key'], $result['value']);
			} else {
				$config->set($result['key'], unserialize($result['value']));
			}
		}
	}

// Loader
	$loader = new Loader($registry);
	$registry->set('load', $loader);

// Url
	$url = new Url(HTTP_SERVER, $config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER, $registry);
	$registry->set('url', $url);

// Log
	$log = new Log($config->get('config_error_filename'));
	$registry->set('log', $log);

// Request
	$registry->set('request', $request);

// Response
	$response = new Response();
	$response->addHeader('Content-Type: text/html; charset=utf-8');
	$registry->set('response', $response);

// Session
	$registry->set('session', $session);

// Language
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

	if (empty($config->get('config_admin_language'))) {
		$config->set('config_admin_language', 'en');
	}

	$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);
// Language
	$language = new Language($languages[$config->get('config_admin_language')]['directory']);
	$language->load($languages[$config->get('config_admin_language')]['directory']);
	$registry->set('language', $language);

// Document
	$registry->set('document', new Document());

// Currency
	$registry->set('currency', new Currency($registry));

// Weight
	$registry->set('weight', new Weight($registry));

// Length
	$registry->set('length', new Length($registry));

// User
	$registry->set('user', new User($registry));

//Barcode
	if (file_exists('../system/library/php-barcode/barcode.php')) {
		require_once '../system/library/php-barcode/barcode.php';
	}

// Load WSB Class
	$registry->set('wsb', new Wsb($registry));

// Secure File Download Class
	$registry->set('securefiledownload', new SecureFileDownload($registry));

// Event
	$event = new Event($registry);
	$registry->set('event', $event);

// Tax
	$registry->set('tax', new Tax($registry));

//event
	/*$event_cache = $cache->get('event');
	if(!$event_cache)
	{
	  $query = $db->query("SELECT `event_id`, `code`, `trigger`, `action` FROM " . DB_PREFIX . "event ORDER BY NULL");
	  $event_cache = $query->rows;
	  $cache->set('event', $event_cache);
	}

	foreach ($event_cache as $result) {
		$event->register($result['trigger'], $result['action']);
	}
*/
// Front Controller
	$controller = new Front($registry);

// Login
	$controller->addPreAction(new Action('common/login/check'));

// // Permission
	// 	$controller->addPreAction(new Action('error/permission/check'));
	// // Pre Actions
	// if ($config->has('action_pre_action')) {
	// 	foreach ($config->get('action_pre_action') as $value) {
	// 		$controller->addPreAction(new Action($value));
	// 	}
	// }

// Router
	if (isset($request->get['route'])) {
		$action = new Action($request->get['route']);
	} else {
		$action = new Action('common/dashboard');
	}

// Profiling
	if (defined('ENABLE_ROUTE_PROFILING') && ENABLE_ROUTE_PROFILING) {
		$profiling = new Profiling($db);
		$profiling->start();
	}

// Dispatch
	$controller->dispatch($action, new Action('error/not_found'));

	if (defined('ENABLE_ROUTE_PROFILING') && ENABLE_ROUTE_PROFILING) {
		$profiling->end();
		// record the profiling data in db
		$profiling->updateProfilingDataInDB();
	}

// Output
	$response->output();

} catch (\Throwable $exception) {

	//Mail to track exception, to resolve issue
	mailException($exception, $config);

	echo "Contact Administrator!" . "</br>";
	echo "Exception Error:  " . $exception->getCode() . ': ' . $exception->getMessage() . ", " . $exception->getFile() . ",  " . $exception->getLine();
}

function mailException($exception, $config) {

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
	$mail->Username = $config->get('config_mail_smtp_username');
	$mail->Password = $config->get('config_mail_smtp_password');

	$mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
	$mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
	$mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);

	$mail->Subject = 'Exception in khufiya Vibhag - ' . date('d/M/Y h:i:s');
	$mail->msgHTML($body);
	$mail->send(0, false);

}

?>