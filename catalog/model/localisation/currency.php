<?php
class ModelLocalisationCurrency extends Model {
	public function getCurrencyByCode($currency) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($currency) . "'");

		return $query->row;
	}

	public function getCurrencies() {
		$currency_data = array();
		
		// text to show on website and app in currency list
		$currency_text_map = array(
			'INR' => '₹ INR',
			(string)DUMMY_INR_CURRENCY => '₹ INR',
			'USD' => '$ USD',
			'EUR' => '€ EUR'
		);

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency ORDER BY title ASC");

		foreach ($query->rows as $result) {
			$currency_data[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'code'          => $result['code'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value'],
				'status'        => $result['status'],
				'date_modified' => $result['date_modified'],
				'text'					=> isset($currency_text_map[$result['code']]) ? $currency_text_map[$result['code']] : $result['symbol_left'] . ' ' . $result['code'],
				'country_code'	=> $result['country_code']
			);
		}
		
		return $currency_data;
	}    
}