<?php
require_once('totalbase.php');
class KlarnaFee extends TotalBase{

    public function __construct( $registry ){
        parent::__construct($registry);
	}

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0) {
		$this->_load->language('total/klarna_fee');

		$status = true;

		$klarna_fee = $this->_config->get('klarna_fee');

		if (isset($this->_session->data['payment_address_id'])) {
			$this->_load->model('account/address','frontend');

			$address = $this->_registry->frontend_model_account_address->getAddress($this->_session->data['payment_address_id']);
		} elseif (isset($this->_session->data['guest']['payment'])) {
			$address = $this->_session->data['guest']['payment'];
		}

		if (!isset($address)) {
			$status = false;
		} elseif (!isset($this->_session->data['payment_method']['code']) || $this->_session->data['payment_method']['code'] != 'klarna_invoice') {
			$status = false;
		} elseif (!isset($klarna_fee[$address['iso_code_3']])) {
			$status = false;
		} elseif (!$klarna_fee[$address['iso_code_3']]['status']) {
			$status = false;
		} elseif ($this->_cart->getSubTotal() >= $klarna_fee[$address['iso_code_3']]['total']) {
			$status = false;
		}

		if ($status) {
			$total_data[] = array(
				'code'       => 'klarna_fee',
				'title'      => $this->_language->get('text_klarna_fee'),
				'value'      => $klarna_fee[$address['iso_code_3']]['fee'],
				'sort_order' => $klarna_fee[$address['iso_code_3']]['sort_order']
			);

			$tax_rates = $this->_tax->getRates($klarna_fee[$address['iso_code_3']]['fee'], $klarna_fee[$address['iso_code_3']]['tax_class_id']);

			foreach ($tax_rates as $tax_rate) {
				if (!isset($taxes[$tax_rate['tax_rate_id']])) {
					$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
				} else {
					$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
				}
			}

			$total += $klarna_fee[$address['iso_code_3']]['fee'];
		}
	}
}
?>
