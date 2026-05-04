<?php
require 'data_packet.php';

// Configuration
if (is_file(__DIR__ . '/../config.php')) {
	require_once __DIR__ . '/../config.php';
}
//require('response_error.php');
abstract class API {
	/**
	 * Property: method
	 * The HTTP method this request was made in, either GET, POST, PUT or DELETE
	 */
	protected $method = '';
	/**
	 * Property: folder
	 * The folder requested in the URI. eg: folder/
	 */
	public $folder = '';

	/**
	 * Property: endpoint
	 * The Model requested in the URI. eg: /files
	 */
	public $endpoint = '';
	/**
	 * Property: verb
	 * An optional additional descriptor about the endpoint, used for things that can
	 * not be handled by the basic methods. eg: /files/process
	 */
	protected $verb = '';
	/**
	 * Property: args
	 * Any additional URI components after the endpoint and verb have been removed, in our
	 * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
	 * or /<endpoint>/<arg0>
	 */
	protected $args = Array();
	/**
	 * Property: file
	 * Stores the input of the PUT request
	 */
	protected $file = Null;

	/**
	 * Property: Controller Name
	 * use to create object of this controller
	 */
	public $endpointcontroller = Null;

	/**
	 * Property: api path
	 * use to check wether this api path require authentication
	 */
	public $requested_api = Null;

	/**
	 * Constructor: __construct
	 * assemble and pre-process the data
	 */

	public function __construct($request) {

		header("Content-Type: application/json");

		$this->args = explode('/', rtrim($request, '/'));

		$this->endpoint = array_shift($this->args);

		if (!file_exists($this->endpoint . ".php")) {
			$this->folder = $this->endpoint . '/';
			$this->endpoint = array_shift($this->args);
		}

		$this->endpointcontroller = ucfirst($this->endpoint) . "Controller";

		if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
			$this->verb = array_shift($this->args);
		}

		// URL of the api file name/method name
		$this->requested_api = $this->endpoint . "/" . $this->verb;

		$this->method = $_SERVER['REQUEST_METHOD'];
		if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
			if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
				$this->method = 'DELETE';
			} else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
				$this->method = 'PUT';
			} else {
				throw new Exception("Unexpected Header");
			}
		}

		switch ($this->method) {
		case 'DELETE':
		case 'POST':
			$this->request = $this->_cleanInputs($_POST);
			break;
		case 'GET':
			$this->request = $this->_cleanInputs($_GET);
			break;
		case 'PUT':
			$this->request = $this->_cleanInputs($_GET);
			$this->file = file_get_contents("php://input");
			break;
		default:
			$this->_response('Invalid Method', 405);
			break;
		}
	}

	public function processAPI() {

		try {
			$dp = new DataPacket();
			//Pass class's properties to the endpoint class
			$paramsArray = array("method" => $this->method, "args" => $this->args, "request" => $this->request, 'requested_api' => $this->requested_api);
			$apiObj = new $this->endpointcontroller($paramsArray);

			if (method_exists($apiObj, $this->verb)) {

				$result = $apiObj->{$this->verb}($this->args);

				if (isset($_REQUEST['profiling']) && $_REQUEST['profiling'] == DEBUG_SQL_PROFILE) {
					Debug::output();
				}

				if ($result == null) {

					//throw new Exception('Internal Server Error',500);
					$dp->message = 'Internal Server Error';
					$dp->statusCode = 500;
					return $this->_response($dp);
				}
				return $this->_response($result, 200);
			} else {
				//throw new Exception('Method Not Found',404);
				$dp->message = 'Method Not Found';
				$dp->statusCode = 404;
				return $this->_response($dp);
			}

		} catch (\Throwable $ex) {

			$this->mailException($ex);
			$dp->message = 'Internal server error';
			$dp->statusCode = 902;
			return $this->_response($dp);

		}
	}

	public function mailException($exception) {
		$config_data = array();
		$cache = new Cache('file');

		$store_setting_cache = $cache->get('store_setting');
		if (empty($store_setting_cache)) {
			$db = new Database\DB(DB_SERVERS);
			$query = $db->query("SELECT `setting_id`, `serialized`, `value`, `store_id`, `key`, `code`  FROM `" . DB_PREFIX . "setting` ORDER BY store_id ASC");
			$store_setting_cache = $query->rows;
			$cache->set('store_setting', $store_setting_cache);
		}

		foreach ($store_setting_cache as $result) {
			if ($result['store_id'] == 0) {
				if (!$result['serialized']) {
					$config_data[$result['key']] = $result['value'];
				} else {
					$config_data[$result['key']] = unserialize($result['value']);
				}
			}
		}

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

		// $mail = new PHPMailer();
		// $mail->isSMTP();
		// $mail->Host = $config_data['config_mail_smtp_hostname'];
		// $mail->Port = $config_data['config_mail_smtp_port'];
		// $mail->SMTPSecure = 'ssl';
		// $mail->SMTPAuth = true;
		// $mail->SMTPDebug = 0;
		// $mail->Debugoutput = 'html';
		// $mail->Username = $config_data['config_mail_smtp_username'];
		// $mail->Password = $config_data['config_mail_smtp_password'];
		// $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
		// $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
		// $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
		// $mail->Subject = html_entity_decode('Exception in API - '.date('d/M/Y h:i:s'), ENT_QUOTES, 'UTF-8');
		// $mail->msgHTML($body);
		// $mail->send();
	}

	private function _response($data, $status = 200) {
		header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
		return json_encode($data);
	}

	private function _cleanInputs($data) {
		$clean_input = Array();
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$clean_input[$k] = $this->_cleanInputs($v);
			}
		} else {
			$clean_input = trim(strip_tags($data));
		}
		return $clean_input;
	}

	private function _requestStatus($code) {
		$status = array(
			200 => 'OK',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			500 => 'Internal Server Error',
		);
		return ($status[$code]) ? $status[$code] : $status[500];
	}
}
?>
