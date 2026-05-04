 <?php 

class validation  {


	protected $data          = array();
	protected $config        = array();
	protected $validate      = TRUE;
	protected $error_message = '';
	protected $error_field   = NULL;
	protected $postcode_regexp = array( 
                                        'AD' => '/^(?:AD)*(\d{3})$/',
                                        'AM' => '/^(\d{6})$/',
                                        'AR' => '/^([A-Z]\d{4}[A-Z]{3})$/',
									    'AT' => '/^(\d{4})$/',
										'AU' => '/^(\d{4})$/',
										'AX' => '/^(?:FI)*(\d{5})$/',
										'AZ' => '/^(?:AZ)*(\d{4})$/',
										'BA' => '/^(\d{5})$/',
										'BB' => '/^(?:BB)*(\d{5})$/',
										'BD' => '/^(\d{4})$/',
										'BE' => '/^(\d{4})$/',
										'BG' => '/^(\d{4})$/',
										'BH' => '/^(\d{3}\d?)$/',
										'BM' => '/^([A-Z]{2}\d{2})$/',
										'BN' => '/^([A-Z]{2}\d{4})$/',
										'BR' => '/^(\d{8})$/',
										'BY' => '/^(\d{6})$/',
										'CA' => '/^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ]) ?(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/',
										'CH' => '/^(\d{4})$/',
										'CL' => '/^(\d{7})$/',
										'CN' => '/^(\d{6})$/',
										'CR' => '/^(\d{4})$/',
										'CU' => '/^(?:CP)*(\d{5})$/',
										'CV' => '/^(\d{4})$/',
										'CX' => '/^(\d{4})$/',
										'CY' => '/^(\d{4})$/',
										'CZ' => '/^(\d{5})$/',
										'DE' => '/^(\d{5})$/',
										'DK' => '/^(\d{4})$/',
										'DO' => '/^(\d{5})$/',
										'DZ' => '/^(\d{5})$/',
										'EC' => '/^([a-zA-Z]\d{4}[a-zA-Z])$/',
										'EE' => '/^(\d{5})$/',
										'EG' => '/^(\d{5})$/',
										'ES' => '/^(\d{5})$/',
										'ET' => '/^(\d{4})$/',
										'FI' => '/^(?:FI)*(\d{5})$/',
										'FM' => '/^(\d{5})$/',
										'FO' => '/^(?:FO)*(\d{3})$/',
										'FR' => '/^(\d{5})$/',
										'GB' => '/^(([A-Z]\d{2}[A-Z]{2})|([A-Z]\d{3}[A-Z]{2})|([A-Z]{2}\d{2}[A-Z]{2})|([A-Z]{2}\d{3}[A-Z]{2})|([A-Z]\d[A-Z]\d[A-Z]{2})|([A-Z]{2}\d[A-Z]\d[A-Z]{2})|(GIR0AA))$/',
										'GE' => '/^(\d{4})$/',
										'GF' => '/^((97|98)3\d{2})$/',
										'GG' => '/^(([A-Z]\d{2}[A-Z]{2})|([A-Z]\d{3}[A-Z]{2})|([A-Z]{2}\d{2}[A-Z]{2})|([A-Z]{2}\d{3}[A-Z]{2})|([A-Z]\d[A-Z]\d[A-Z]{2})|([A-Z]{2}\d[A-Z]\d[A-Z]{2})|(GIR0AA))$/',
										'GL' => '/^(\d{4})$/',
										'GP' => '/^((97|98)\d{3})$/',
										'GR' => '/^(\d{5})$/',
										'GT' => '/^(\d{5})$/',
										'GU' => '/^(969\d{2})$/',
										'GW' => '/^(\d{4})$/',
										'HN' => '/^([A-Z]{2}\d{4})$/',
										'HR' => '/^(?:HR)*(\d{5})$/',
										'HT' => '/^(?:HT)*(\d{4})$/',
										'HU' => '/^(\d{4})$/',
										'ID' => '/^(\d{5})$/',
										'IL' => '/^(\d{5})$/',
										'IM' => '/^(([A-Z]\d{2}[A-Z]{2})|([A-Z]\d{3}[A-Z]{2})|([A-Z]{2}\d{2}[A-Z]{2})|([A-Z]{2}\d{3}[A-Z]{2})|([A-Z]\d[A-Z]\d[A-Z]{2})|([A-Z]{2}\d[A-Z]\d[A-Z]{2})|(GIR0AA))$/',
										'IN' => '/^(\d{6})$/',
										'IQ' => '/^(\d{5})$/',
										'IR' => '/^(\d{10})$/',
										'IS' => '/^(\d{3})$/',
										'IT' => '/^(\d{5})$/',
										'JE' => '/^(([A-Z]\d{2}[A-Z]{2})|([A-Z]\d{3}[A-Z]{2})|([A-Z]{2}\d{2}[A-Z]{2})|([A-Z]{2}\d{3}[A-Z]{2})|([A-Z]\d[A-Z]\d[A-Z]{2})|([A-Z]{2}\d[A-Z]\d[A-Z]{2})|(GIR0AA))$/',
										'JO' => '/^(\d{5})$/',
										'JP' => '/^(\d{7})$/',
										'KE' => '/^(\d{5})$/',
										'KG' => '/^(\d{6})$/',
										'KH' => '/^(\d{5})$/',
										'KP' => '/^(\d{6})$/',
										'KR' => '/^(?:SEOUL)*(\d{6})$/',
										'KW' => '/^(\d{5})$/',
										'KZ' => '/^(\d{6})$/',
										'LA' => '/^(\d{5})$/',
										'LB' => '/^(\d{4}(\d{4})?)$/',
										'LI' => '/^(\d{4})$/',
										'LK' => '/^(\d{5})$/',
										'LR' => '/^(\d{4})$/',
										'LS' => '/^(\d{3})$/',
										'LT' => '/^(?:LT)*(\d{5})$/',
										'LU' => '/^(\d{4})$/',
										'LV' => '/^(?:LV)*(\d{4})$/',
										'MA' => '/^(\d{5})$/',
										'MC' => '/^(\d{5})$/',
										'MD' => '/^(?:MD)*(\d{4})$/',
										'ME' => '/^(\d{5})$/',
										'MG' => '/^(\d{3})$/',
										'MK' => '/^(\d{4})$/',
										'MM' => '/^(\d{5})$/',
										'MN' => '/^(\d{6})$/',
										'MQ' => '/^(\d{5})$/',
										'MT' => '/^([A-Z]{3}\d{2}\d?)$/',
										'MV' => '/^(\d{5})$/',
										'MX' => '/^(\d{5})$/',
										'MY' => '/^(\d{5})$/',
										'MZ' => '/^(\d{4})$/',
										'NC' => '/^(\d{5})$/',
										'NE' => '/^(\d{4})$/',
										'NF' => '/^(\d{4})$/',
										'NG' => '/^(\d{6})$/',
										'NI' => '/^(\d{7})$/',
										'NL' => '/^(\d{4}[A-Z]{2})$/',
										'NO' => '/^(\d{4})$/',
										'NP' => '/^(\d{5})$/',
										'NZ' => '/^(\d{4})$/',
										'OM' => '/^(\d{3})$/',
										'PF' => '/^((97|98)7\d{2})$/',
										'PG' => '/^(\d{3})$/',
										'PH' => '/^(\d{4})$/',
										'PK' => '/^(\d{5})$/',
										'PL' => '/^(\d{5})$/',
										'PM' => '/^(97500)$/',
										'PR' => '/^(\d{9})$/',
										'PT' => '/^(\d{7})$/',
										'PW' => '/^(96940)$/',
										'PY' => '/^(\d{4})$/',
										'RE' => '/^((97|98)(4|7|8)\d{2})$/',
										'RO' => '/^(\d{6})$/',
										'RS' => '/^(\d{6})$/',
										'RU' => '/^(\d{6})$/',
										'SA' => '/^(\d{5})$/',
										'SD' => '/^(\d{5})$/',
										'SE' => '/^(?:SE)*(\d{5})$/',
										'SG' => '/^(\d{6})$/',
										'SH' => '/^(STHL1ZZ)$/',
										'SI' => '/^(?:SI)*(\d{4})$/',
										'SK' => '/^(\d{5})$/',
										'SM' => '/^(4789\d)$/',
										'SN' => '/^(\d{5})$/',
										'SO' => '/^([A-Z]{2}\d{5})$/',
										'SV' => '/^(?:CP)*(\d{4})$/',
										'SZ' => '/^([A-Z]\d{3})$/',
										'TC' => '/^(TKCA 1ZZ)$/',
										'TH' => '/^(\d{5})$/',
										'TJ' => '/^(\d{6})$/',
										'TM' => '/^(\d{6})$/',
										'TN' => '/^(\d{4})$/',
										'TR' => '/^(\d{5})$/',
										'TW' => '/^(\d{5})$/',
										'UA' => '/^(\d{5})$/',
										'US' => '/^\d{5}(-\d{4})?$/',
										'UY' => '/^(\d{5})$/',
										'UZ' => '/^(\d{6})$/',
										'VA' => '/^(\d{5})$/',
										'VE' => '/^(\d{4})$/',
										'VI' => '/^\d{5}(-\d{4})?$/',
										'VN' => '/^(\d{6})$/',
										'WF' => '/^(986\d{2})$/',
										'YT' => '/^(\d{5})$/',
										'ZA' => '/^(\d{4})$/',
										'ZM' => '/^(\d{5})$/',
										'CS' => '/^(\d{5})$/'
									   );

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config = NULL) {
		if ( ! empty($config)) {
			$this->initialize($config);
		}
	}

	// ------------------------------------------------------------------------
	/**
	 * Initialize library.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function initialize($config) {
		$this->config = $config;
	}
  

	// ------------------------------------------------------------------------
	/**
	 * Set fields data from array.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function set_data($data) {
		$this->data = (array) $data;
	}

	// ------------------------------------------------------------------------
	/**
	 * Exit due error.
	 *
	 * @access private
	 * @param mixed $error
	 * @param mixed $field (default: NULL)
	 * @return void
	 */
	private function _error($error, $field = NULL) {
		if ($this->validate) {
			$this->error_message = is_null($field)
				? $error
				: sprintf($error, $field);
			$this->error_field = $field;
			$this->validate = FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Return error data: message and field (if exists).
	 *
	 * @access public
	 * @return array
	 */
	public function get_error() {
		return array(
			'message'	=> $this->error_message
			, 'field'	=> $this->error_field
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Return error message.
	 *
	 * @access public
	 * @return string
	 */
	public function get_error_message() {
		return $this->error_message;
	}
	// ------------------------------------------------------------------------

	/**
	 * Check if form is valid.
	 *
	 * @access public
	 * @return void
	 */
	public function is_valid() {
		return $this->validate;
	}


	// ------------------------------------------------------------------------

	/**
	 * If you pass string parameter and not array, puts it in an array.
	 *
	 * @access private
	 * @param mixed &$param
	 * @return void
	 */
	private function _parse( & $param) {
		if ( ! is_array($param)) {
			$param = array($param);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Check required fields.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function required($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$this->data[$v] = isset($this->data[$v]) ? trim($this->data[$v]) : '';
				if (empty($this->data[$v])) {
					$this->_error($err_msg, $v);
				}
			}
		}

		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if email fields are valid.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function email($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (!empty($this->data[$v]))
				 {
					if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->data[$v])) 
					{
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}


	// ------------------------------------------------------------------------

    /** Method for server side gstin validation
    * @deprecated: This method is obsolete and should not be used; Instead, use validateGSTNumber() of GST library class
    * @param: $textobj : value of gst provisional id 
    * @param: $get_pancard_no: value is true or false , if you want to get pancard number from GST provisional id then pass to true, otherwise false
    * @return true or false
    * @author vikas, 2017
    */
    public function validateGSTNo($fields, $err_msg = '') {
        $gstObject = new GST($this);
        print_r($this);die;
        $this->_parse($fields);	
        //$regex_for_gst = '/^[0-9]{2}[A-Z]{3}[C,P,H,F,A,T,B,L,J,G,E]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}[A-Z]{1}[A-Z0-9]{1}?$/';
        $regex_for_gst = '/^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/';
        foreach ($fields as $v) {
            if ($this->is_valid()) {
                if (!empty($this->data[$v])) {
                    if (!preg_match($regex_for_gst, $this->data[$v])) {
                        $this->_error($err_msg, $v);
                    }
                }
            }
        }
        return $this;
    }

	// ------------------------------------------------------------------------

	/**
	 * Check that fields are not longer than a defined value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $len
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function maxlen($fields, $len, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if (strlen($this->data[$v]) > $len) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that fields are not shorter than a defined value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $len
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function minlen($fields, $len, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if (strlen($this->data[$v]) < $len) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}


	// ------------------------------------------------------------------------

	/**
	 * Check that the two fields are equal.
	 *
	 * @access public
	 * @param mixed $field_1
	 * @param mixed $field_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function equal($field_1, $field_2, $err_msg = '') 
	{
		if ($this->is_valid()) 
		{
			if (!empty($this->data[$field_1]) && !empty($this->data[$field_2]))
			{

			   if (strcmp($this->data[$field_1], $this->data[$field_2]) !== 0)
			    {
				  $this->_error($err_msg, $field_2);
			    }
			}    
		}
		return $this;
	}


	// ------------------------------------------------------------------------

	/**
	 * Check that the fields meet a particular regular expression.
	 *
	 * @access private
	 * @param mixed $fields
	 * @param mixed $regexp
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	private function regexp($fields, $regexp, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if ( ! preg_match($regexp, $this->data[$v])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check URL fields.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function url($fields, $err_msg = '') {
		$regexp = '/^(https?\:\/\/){0,1}(www\.){0,1}'
			.'([a-z0-9-_.]+)(\.{1})([a-z]{2,4})$/i';
		return $this->regexp($fields, $regexp, $err_msg);
	}


	// ------------------------------------------------------------------------

	/**
	 * Check URL postcode.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function postcode($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			$match=0;
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {

					foreach($this->postcode_regexp as $regexp)
					{
					   if (preg_match($regexp, $this->data[$v])) 
					   {
						   $match = 1;
					   }
					} 
				 if($match==0) { $this->_error($err_msg, $v); } 
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than numbers.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num($fields, $err_msg = '') {
		$regexp = '/^([0-9]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than numbers and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_s($fields, $err_msg = '') {
		$regexp = '/^([0-9\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that the fields have a mobile number.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function mobile_number($fields, $err_msg = '') {
		$regexp = '/^\d{10}$/';
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if($this->data[$v][0] == 0) { $this->_error($err_msg, $v); }
					if ( ! preg_match($regexp, $this->data[$v])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}



	// ------------------------------------------------------------------------

	/**
	 * Check that fields do not have spaces.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function no_spaces($fields, $err_msg = '') {
		$regexp = '/^([^\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if a numeric field is greater than a certain value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $num
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_gt($fields, $num, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			$this->num($v, $err_msg);
			if ($this->is_valid()) {
				if ($this->data[$v] < $num) {
					$this->_error($err_msg, $v);
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if a numeric field is less than a certain value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $num
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_lt($fields, $num, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			$this->num($v, $err_msg);
			if ($this->is_valid()) {
				if ($this->data[$v] > $num) {
					$this->_error($err_msg, $v);
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that the fields have a date.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date($fields, $err_msg = '') {
		$exp = '/^([0-9]{2})([^A-Za-z0-9]{1})([0-9]{2})'
			.'([^A-Za-z0-9]{1})([0-9]{4})$/';
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					$match = array();
					if ( ! preg_match($exp, $this->data[$v], $match)) {
						$this->_error($err_msg, $v);
					} elseif ( ! checkdate($match[3], $match[1], $match[5])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check the difference between two dates.
	 *
	 * @access private
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @return void
	 */
	private function date_diff($date_1, $date_2) {
		$d1 = strtotime($this->data[$date_1]);
		$d2 = strtotime($this->data[$date_2]);
		return round(($d1 - $d2)/60/60/24);
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that a date is larger than other.
	 *
	 * @access public
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_gt($date_1, $date_2, $err_msg = '') {
		if ($this->is_valid()) {
			if ( ! empty($date_1) && ! empty($date_2)) {
				if ($this->date_diff($date_1, $date_2, $err_msg) > 0) {
					$this->_error($err_msg);
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that a date is smaller than other.
	 *
	 * @access public
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_lt($date_1, $date_2, $err_msg = '') {
		if ($this->is_valid()) {
			if ( ! empty($date_1) && ! empty($date_2)) {
				if ($this->date_diff($date_1, $date_2, $err_msg) < 0) {
					$this->_error($err_msg);
				}
			}
		}
		return $this;
	}


	// ------------------------------------------------------------------------

	/**
	 * Check that the field has been checked.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $checked_value
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function checked($field, $checked_value, $err_msg = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field], $checked_value) !== 0) {
				$this->_error($err_msg);
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check that the field has been selected.
	 *
	 * @access public
	 * @param mixed $field
	 * @param string $err_msg (default: '')
	 * @param string $empty_value (default: '')
	 * @return void
	 */
	public function selected($field, $err_msg = '', $empty_value = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field], $empty_value) !== 0) {
				$this->_error($err_msg);
			}
		}
		return $this;
	}



}