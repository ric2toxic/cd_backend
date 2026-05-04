<?php
final class Action {
	private $file;
	private $class;
	private $method;
	private $args = array();
	private $classes_to_throw_exceptions = array('Controllercroncron');

	public function __construct($route, $args = array()) {
		$path = '';

		// Break apart the route
		$parts = explode('/', str_replace('../', '', (string)$route));

		foreach ($parts as $part) {
			$path .= $part;

			if (is_dir(DIR_APPLICATION . 'controller/' . $path)) {
				$path .= '/';

				array_shift($parts);

				continue;
			}

			$file = DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';

			if (is_file($file)) {
				$this->file = $file;

				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);

				break;
			}
		}

		if ($args) {
			$this->args = $args;
		}

		$method = array_shift($parts);

		if ($method) {
			$this->method = $method;
		} else {
			$this->method = 'index';
		}
	}

	public function execute($registry) {
		
		// Stop any magical methods being called
		if (substr($this->method, 0, 2) == '__') {
			return false;
		}

		if (is_file($this->file)) {
			include_once(modification($this->file));

			$class = $this->class;

			$controller = new $class($registry);

			if (is_callable(array($controller, $this->method))) {
				//add try-catch exception statements
				if(in_array($class, $this->classes_to_throw_exceptions) ){
					try
					{
						return call_user_func_array(array($controller, $this->method), $this->args);
					
					}catch (\Throwable $exception) {

					    //Mail to track exception, to resolve issue
					    $this->mailException($exception, $controller->config);  
					}
				}else{
					return call_user_func_array(array($controller, $this->method), $this->args);
				}


			} else {
				return false;
			}

		} else {
			return false;
		}
	}


	/**
     * @info: Public method to send mail for exception information,
     *         defines private variable- for list of classes for handle exception
     * @param: Exception object, Config object
     * @author: Nishu, May 2019
	*/
	public function mailException($exception, $config){
	
		$error_trace = $exception->getTrace();
		$error_trace_to_mail = array();

		//Unset error array keys greater then 4, to ignore unneccesary data args
		foreach ($error_trace as $trace) {
			
			$class = $trace['class'] ?? '';
			if ( empty($class) || $class === 'Action' )
				break;
			
			$error_trace_to_mail[] = $trace;
		}

	    $body = "Exception Error:  ". $exception->getCode(). ': ' . $exception->getMessage(). ", " . $exception->getFile() .",  ". $exception->getLine();

	    $body .= "<br><br><br>";

	    $body .= "<pre>".print_r($error_trace_to_mail, true) . "</pre>";

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
	    
	    $mail->Subject = 'Exception raised in Cron :- '.date('d/M/Y h:i:s');
	    $mail->msgHTML($body);
	    $mail->send(0, false);

	}
}