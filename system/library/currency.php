<?php
class Currency {
	private $code;
	public $currencies = array();
	private $request_by_app = 0;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->language = $registry->get('language');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->cache = $registry->get('cache');
		
		$headers = getallheaders();
    if(!empty($headers['REQUEST_BY'])) {
        $request_by = strtolower($headers['REQUEST_BY']);
        if((strpos($request_by, 'android') !== false)
						|| (strpos($request_by, 'ios') !== false)
						|| (strpos($request_by, 'crm') !== false)
					  || (strpos($request_by, 'app') !== false)) {
            $this->request_by_app = 1;
        }
    }

    /** for seller app*/
    $getSellerRoute = (isset($this->request->get['route']))?explode('/',$this->request->get['route']):array();
   	if(!empty($getSellerRoute) && $getSellerRoute[0] == "restapi" && $getSellerRoute[1] == "seller") {
        $this->request_by_app = 1;
    } else {
        // do nothing
    }
		
		 $currency_cache = $this->cache->get('currency');
         if(empty($currency_cache))
         {
		   $query = $this->db->query("SELECT 
			                       currency_id, 
			                       title, 
			                       code, 
			                       symbol_left,
			                       symbol_right,
			                       decimal_place,
			                       value,
			                       status,
			                       conversion_rate,
			                       country_code
			                       FROM " . DB_PREFIX . "currency ORDER BY NULL");
           
           // set currency cache
           $currency_cache = $query->rows;
		   $this->cache->set('currency', $currency_cache);
		 } 
		
		foreach ($currency_cache as $result) {

			$symbol_left = $result['symbol_left'];
			
			if($result['code']=='INR' && !$this->request_by_app) {
				$symbol_left = getRupeeSymbol();
			}
			
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $symbol_left,
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value'],
				'conversion_rate'         => $result['conversion_rate']
			);
		}

		if (isset($headers['currency']) && (array_key_exists($headers['currency'], $this->currencies))) {
			$this->set($headers['currency']);
		} elseif (isset($this->request->get['currency']) && (array_key_exists($this->request->get['currency'], $this->currencies))) {
			$this->set($this->request->get['currency']);
		} elseif ((isset($this->session->data['currency'])) && (array_key_exists($this->session->data['currency'], $this->currencies))) {
			$this->set($this->session->data['currency']);
		} elseif ((isset($this->request->cookie['currency'])) && (array_key_exists($this->request->cookie['currency'], $this->currencies))) {
			$this->set($this->request->cookie['currency']);
		} else {
			$this->set($this->config->get('config_currency'));
		}
	}

	public function set($currency) { 
		$this->code = $currency;

		if (!isset($this->session->data['currency']) || ($this->session->data['currency'] != $currency)) {
			$this->session->data['currency'] = $currency;
		}

		if (!isset($this->request->cookie['currency']) || ($this->request->cookie['currency'] != $currency)) {
			setcookie('currency', $currency, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
		}
	}
	
	/*
	* method: setCurrencyTemporary
	* set the currency for current request only i.e. will not change currency in session and cookies
	* @params: currency 
	* @author: Devendra 10th September 2018 
	*/
	public function setCurrencyTemporary($currency) { 
		$this->code = $currency;
	}

	public function money_format($number, $currency = '', $value = '', $format = true, $decimal=-1, $call_from = '') {

		if ($currency && $this->has($currency)) {
			$symbol_left   = $this->currencies[$currency]['symbol_left'];
			$symbol_right  = $this->currencies[$currency]['symbol_right'];
			$decimal_place = (int)$decimal>0 ? (int)$decimal : $this->currencies[$currency]['decimal_place'];
		} else {
			$symbol_left   = $this->currencies[$this->code]['symbol_left'];
			$symbol_right  = $this->currencies[$this->code]['symbol_right'];
			$decimal_place = (int)$decimal>0 ? (int)$decimal : $this->currencies[$this->code]['decimal_place'];

			$currency = $this->code;
		}

		/***
			- added by Anurag Jain
			- added for frontend zero decimal point requirement
 			- using in frontend; can be used anywhere required 
		***/
		if(!empty($call_from)) { 
			if(strtolower($call_from) == 'frontend') {

				$number_arr = explode('.', $number);
				$decimal_part = 0;
				if(count($number_arr) > 1) {
					$decimal_part = (int) $number_arr[1];
				}
				if($decimal_part < 1) {
					$decimal_place = $decimal;
				}
			}
		}

		if ($value) {
			$value = $value;
		} else {
			$value = $this->currencies[$currency]['value'];
		}

		if ($value) {
			$value = (float)$number * $value;
		} else {
			$value = $number;
		}

		$string = '';

		if (($symbol_left) && ($format)) {
			$string .= $symbol_left;
			if(!$this->request_by_app) {
				$string .= ' ';
			}
		}

		if ($format) {
			$decimal_point = $this->language->get('decimal_point');
		} else {
			$decimal_point = '.';
		}

		if ($format) {
			$thousand_point = $this->language->get('thousand_point');
		} else {
			$thousand_point = '';
		}

		setlocale(LC_MONETARY, 'en_IN');
		$string .= money_format('%!i', $value);

		//$string .= number_format(round($value, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);

		if (($symbol_right) && ($format)) {
			$string .= $symbol_right;
			if(!$this->request_by_app) {
				$string .= ' ';
			}
		}
		return $string;
	}

	public function format($number, $currency = '', $value = '', $format = true, $decimal=-1, $call_from = '') {

		if ($currency && $this->has($currency)) {
			$symbol_left   = $this->currencies[$currency]['symbol_left'];
			$symbol_right  = $this->currencies[$currency]['symbol_right'];
			$decimal_place = (int)$decimal>0 ? (int)$decimal : $this->currencies[$currency]['decimal_place'];
		} else {
			$symbol_left   = $this->currencies[$this->code]['symbol_left'];
			$symbol_right  = $this->currencies[$this->code]['symbol_right'];
			$decimal_place = (int)$decimal>0 ? (int)$decimal : $this->currencies[$this->code]['decimal_place'];

			$currency = $this->code;
		}

		/***
			- added by Anurag Jain
			- added for frontend zero decimal point requirement
 			- using in frontend; can be used anywhere required 
		***/
		if(!empty($call_from)) { 
			if(strtolower($call_from) == 'frontend') {

				$number_arr = explode('.', $number);
				$decimal_part = 0;
				if(count($number_arr) > 1) {
					$decimal_part = (int) $number_arr[1];
				}
				if($decimal_part < 1) {
					$decimal_place = $decimal;
				}
			}
		}

		if ($value) {
			$value = $value;
		} else {
			$value = $this->currencies[$currency]['value'];
		}

		if ($value) {
			$value = (float)$number * $value;
		} else {
			$value = $number;
		}

		$string = '';

		if (($symbol_left) && ($format)) {
			$string .= $symbol_left;
			if(!$this->request_by_app) {
				$string .= ' ';
			}
		}

		if ($format) {
			$decimal_point = $this->language->get('decimal_point');
		} else {
			$decimal_point = '.';
		}

		if ($format) {
			$thousand_point = $this->language->get('thousand_point');
		} else {
			$thousand_point = '';
		}

		$string .= number_format(round($value, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);

		if (($symbol_right) && ($format)) {
			$string .= $symbol_right;
			if(!$this->request_by_app) {
				$string .= ' ';
			}
		}
		return $string;
	}

	/***
	 * Remove Decimal From Price 
	 * Date: 25-12-2015
	 * @author: Ravindra Singh
	 * formatWebservices
	 * */
	public function formatWebservices($number, $currency = '', $value = '', $format = true, $call_from = '') {
		if ($currency && $this->has($currency)) {
			$symbol_left   = $this->currencies[$currency]['symbol_left'];
			$symbol_right  = $this->currencies[$currency]['symbol_right'];
			$decimal_place = $this->currencies[$currency]['decimal_place'];
		} else {
			$symbol_left   = $this->currencies[$this->code]['symbol_left'];
			$symbol_right  = $this->currencies[$this->code]['symbol_right'];
			$decimal_place = $this->currencies[$this->code]['decimal_place'];
			$currency = $this->code;
		}

		/***
			- added by Anurag Jain
			- added for frontend zero decimal point requirement
 			- using in frontend; can be used anywhere required 
		***/
		if(!empty($call_from)) { 
			if(strtolower($call_from) == 'frontend') {

				$number_arr = explode('.', $number);
				$decimal_part = 0;
				if(count($number_arr) > 1) {
					$decimal_part = (int) $number_arr[1];
				}
				if($decimal_part < 1) {
					$decimal_place = 0;
				}
			}
		}

		if ($value) {
			$value = $value;
		} else {
			$value = $this->currencies[$currency]['value'];
		}

		if ($value) {
			$value = (float)$number * $value;
		} else {
			$value = $number." ";
		}
		$string = '';

		if (($symbol_left) && ($format)) {
			$string .= $symbol_left;
		}

		if ($format) {
			$decimal_point = $this->language->get('decimal_point');
		} else {
			$decimal_point = '.';
		}

		if ($format) {
			$thousand_point = $this->language->get('thousand_point');
		} else {
			$thousand_point = '';
		}

		$string .= number_format(round($value."&nbsp", (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);

		if (($symbol_right) && ($format)) {
			$string .= $symbol_right;
		}

		return $string." ";
	}

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function getId($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['currency_id'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_left'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_right'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['decimal_place'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getCode() {
		return $this->code;
	}
	public function getCurrencyConversionRate() {
		return $this->currencies[$this->code]['value'];
	}

	public function getValue($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['value'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function getLiveConversionRate($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['conversion_rate'];
		} elseif ($currency && isset($this->currencies[$currency])) {

			return $this->currencies[$currency]['conversion_rate'];
		} else {
			return 0;
		}
	}
	public function convertLiveRates($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['conversion_rate'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['conversion_rate'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function has($currency) {
		return isset($this->currencies[$currency]);
	}

}
