<?php
class ModelTotalCredit extends Model {
	
	private $_balance = 0.0;
	
	/**
	 * Internal method to do all the calculations for credit
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {

        // Credit Is DISABLED permanently
        return true;

		if ($this->config->get('credit_status')) {
			$this->load->language('total/credit');

			if ( (float)$this->_balance) {
				if ( $this->_balance > $total) {
					$credit = $total;
				} else {
					$credit = $this->_balance;
				}

				if ($credit > 0) {
					$total_data[] = array(
						'code'       => 'credit',
						'title'      => $this->language->get('text_credit'),
						'value'      => -$credit,
						'sort_order' => $this->config->get('credit_sort_order')
					);

					$total -= $credit;
				}
			}
		}
	}
	
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		if ($this->config->get('credit_status')) {
			$this->_balance = $this->customer->getBalance();
			$this->_doCalculations($total_data, $total);
		}
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		
		if ($this->config->get('credit_status')) {
			$this->load->language('total/credit');
			
			$user_id = $extra['user_id'];
			$this->_balance = $this->cart->getBalance($user_id);
			$this->_doCalculations($total_data, $total);
		}
	}

	public function confirm($order_info, $order_total) {
		$this->load->language('total/credit');

		if ($order_info['customer_id']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction 
			                  SET customer_id = '" . (int)$order_info['customer_id'] . "', 
			                    order_id = '" . (int)$order_info['order_id'] . "', 
			                    description = '" . $this->db->escape(sprintf($this->language->get('text_order_no'), (float)$order_info['order_no'])) . "', 
			                    amount = '" . (float)$order_total['value'] . "', 
			                    date_added = NOW()");
		}
	}

	public function unconfirm($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}
	
	
}
