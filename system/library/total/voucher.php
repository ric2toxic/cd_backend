<?php
require_once('totalbase.php');
class Voucher extends TotalBase {

    public function __construct( $registry ){
        parent::__construct($registry);
	}

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		if (isset($this->_session->data['voucher'])) {
			$this->_load->language('total/voucher');

			$this->_load->model('checkout/voucher','frontend');

			$voucher_info = $this->_registry->frontend_model_checkout_voucher->getVoucher($this->_session->data['voucher']);

			if ($voucher_info) {
				if ($voucher_info['amount'] > $total) {
					$amount = $total;
				} else {
					$amount = $voucher_info['amount'];
				}

				$total_data[] = array(
					'code'       => 'voucher',
					'title'      => sprintf($this->_language->get('text_voucher'), $this->_session->data['voucher']),
					'value'      => -$amount,
					'sort_order' => $this->_config->get('voucher_sort_order')
				);

				$total -= $amount;
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}

		$this->_load->model('checkout/voucher','frontend');

		$voucher_info = $this->_registry->frontend_model_checkout_voucher->getVoucher($code);

		if ($voucher_info) {
			$this->_db->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int)$voucher_info['voucher_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
		}
	}

	public function unconfirm($order_id) {
		$this->_db->query("DELETE FROM `" . DB_PREFIX . "voucher_history` WHERE order_id = '" . (int)$order_id . "'");
	}
}
?>
