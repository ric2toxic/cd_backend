<?php
class Language {
	private $default = 'english';
	private $directory;
	private $data = array();

	public function __construct($directory = '') {
		$this->directory = $directory;
	}

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function load($filename, $dir_language='') {
		$_ = array();
		
		$base_languge_folder = ($dir_language ? $dir_language : DIR_LANGUAGE); 

		 $file = $base_languge_folder . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		$file = $base_languge_folder . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}
    
    
    public function autoLoad($filename, &$controllerData) {
        $controllerData = array_merge($controllerData, $this->load($filename));
        return true;
	}

	public function switchLanguage($language) {
        $this->directory = $language;
	}
}
