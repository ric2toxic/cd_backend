<?php
final class Loader {
    
    public function __get($key) {
	    return $this->registry->get($key);
	}

	public function __set($key, $value) {
	    $this->registry->set($key, $value);
	}
    
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
        
        require_once(DIR_SYSTEM . 'library/msloader.php');
		$registry->set('MsLoader', new MsLoader($registry));
	}

	public function controller($route, $args = array()) {
		$action = new Action($route, $args);

		return $action->execute($this->registry);
	}

	public function model($model, $front='') {

		$base_folder = '';
		$model_key_prefix = '';

		if($front == 'frontend'){
			$base_folder = DIR_CATALOG;
			$model_key_prefix = 'frontend_';
		} else if($front == 'admin'){
			$base_folder = DIR_ADMIN;
			$model_key_prefix = 'admin_';
		} else {
			$base_folder = DIR_APPLICATION;
			$model_key_prefix = '';
		}

		$file = $base_folder . 'model/' . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

		if (file_exists($file)) {
			include_once($file);

			$this->registry->set($model_key_prefix . 'model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $file . '!');
			exit();
		}
	}

	public function view($template, $data = array(), $absolute_path = false) {
		
		$file = DIR_TEMPLATE . $template;
		
		if ( $absolute_path ) {
			$file = $template;
		}		

		if (file_exists($file)) {
			extract($data);

			ob_start();

			require(modification($file));

			$output = ob_get_contents();

			ob_end_clean();

			return $output;
		} else {
			trigger_error('Error: Could not load template ' . $file . '!');
			exit();
		}
	}

	public function library($library) {
		$file = DIR_SYSTEM . 'library/' . $library . '.php';

		if (file_exists($file)) {
			include_once(modification($file));
		} else {
			trigger_error('Error: Could not load library ' . $file . '!');
			exit();
		}
	}

	public function helper($helper) {
		$file = DIR_SYSTEM . 'helper/' . $helper . '.php';

		if (file_exists($file)) {
			include_once(modification($file));
		} else {
			trigger_error('Error: Could not load helper ' . $file . '!');
			exit();
		}
	}

	public function form($form) {
		$file = DIR_APPLICATION . 'form/' . $form . '.php';

		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load form ' . $file . '!');
			exit();
		}
	}	

	public function config($config) {
		$this->registry->get('config')->load($config);
	}

	public function language($language, $dir_lang='') {
		return $this->registry->get('language')->load($language, $dir_lang);
	}
    
    /**
     * Auto Loading a lanugage file based on its key-value pair
     * @params $language - string giving language file path
     * @params $data - array passed by reference. Filled up by controller to pass on to view files (templates).
     * @usage
     * // Initialize the $data array in controller
     * $data = array();
     * ... // populate it as per ur needs. at some place, u can autopopulate language entries as well
     * $this->language->autoLoadLanguage('sale/order', $data);  // autoloading language file from sale > order file
     *
     * @note The key in the $data variable for the lanugage variables will be exactly same as the key defined in the language files.
     * @author: Madhur
     */
    public function autoLoadLanguage($language, &$data) {
        return $this->registry->get('language')->autoLoad($language, $data);
    }

}
