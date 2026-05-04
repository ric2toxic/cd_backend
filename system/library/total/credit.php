<?php
require_once('totalbase.php');
class Credit extends TotalBase{

	private $_balance = 0.0;

    public function __construct( $registry ){
        parent::__construct($registry);
	}

	/**
	 * Internal method to do all the calculations for credit
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {

        // Credit Is DISABLED permanently
        return true;


		if ($this->_config->get('credit_status')) {
			$this->_load->language('total/credit');

			if ( (float)$this->_balance) {
				if ( $this->_balance > $total) {
					$credit = $total;
				} else {
					$credit = $this->_balance;
				}

				if ($credit > 0) {
					$total_data[] = array(
						'code'       => 'credit',
						'title'      => $this->_language->get('text_credit'),
						'value'      => -$credit,
						'sort_order' => $this->_config->get('credit_sort_order')
					);

					$total -= $credit;
				}
			}
		}
	}

    private function _doSuborderCalculation( &$total_data , &$total ){
        if(!empty($this->suborder_id)){
            $credit = 0;
            $sql = "SELECT suborder_id,custom_totals
                    FROM ". DB_PREFIX . "suborder
                    WHERE order_id = '".(int)$this->order_id."'";
            $sid_custom_totals = $this->_db->query($sql)->rows;

            $sid_custom_totals = array_combine(
                array_column($sid_custom_totals,'suborder_id'),
                array_column($sid_custom_totals,'custom_totals')
            );

            if(!empty($sid_custom_totals[$this->suborder_id])){
                $custom_totals = unserialize($sid_custom_totals[$this->suborder_id]);
                if(!empty($custom_totals['credit'])){
                    $credit = $custom_totals['credit']['value'];
                }
                else{
                    $credit = $this->getSuborderCredit($sid_custom_totals);
                }
            }
            else{
                $credit = $this->getSuborderCredit($sid_custom_totals);
            }

            if( (float)$credit < 0){

                $language = $this->_registry->language->load('total/credit');
                $total_data[] = array(
    				'code'       => 'credit',
    				'title'      => $language['text_credit'],
    				'value'      => (float)$credit,
    				'sort_order' => $this->_sort_order['credit_sort_order']
			    );
			    $total += $credit;

            }
        }
    }

    private function getSuborderCredit($custom_totals){
        $credit = 0;
        $total_credit = OrderInfo::getTotalCredit($this->_db,$this->order_id);
        if( $total_credit < 0 ){
            $non_freezed_suborders = array();
            $credit_used = 0;
            foreach ($custom_totals as $sid => $custom_total) {
                $custom_total = !empty($custom_total) ? unserialize($custom_total) : '';
                if(!empty($custom_total['credit']) && $custom_total['credit']['locked'] == true){
                    $credit_used += (float)$custom_total['credit']['value'];
                }
                else{
                    $non_freezed_suborders[] = $sid;
                }
            }
            $reamining_credit = (float)$total_credit - (float)$credit_used;

            $splitOrder = new SplitOrder($this->_db);
            $splitOrder->setOptions( 'check_subtotal_in' , $non_freezed_suborders );
            $credit = $splitOrder->splitBySubtotal( $reamining_credit, $this->order_id, $this->suborder_id);
        }
        return $credit;
    }

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
        if($this->suborder){
            $this->_doSuborderCalculation($total_data, $total);
            return;
        }

        $franchise_id = $this->_cart->getFranchiseId();
        // if this order is by franchise, then we will not use customer's cashback in order
        if(!empty($franchise_id) && $this->_cart->checkFranchiseProductsInCart()){
            return;
        }

        if ($this->_config->get('credit_status')) {
			$this->_balance = $this->_customer->getBalance();
			$this->_doCalculations($total_data, $total);
		}
	}

	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {

		if ($this->_config->get('credit_status')) {
			$this->_load->language('total/credit');

			$user_id = $extra['user_id'];
			$this->_balance = $this->_cart->getBalance($user_id);
			$this->_doCalculations($total_data, $total);
		}
	}

	public function confirm($order_info, $order_total) {
		$this->_load->language('total/credit');

		if ($order_info['customer_id']) {
			$this->_db->query("INSERT INTO " . DB_PREFIX . "customer_transaction
			                  SET customer_id = '" . (int)$order_info['customer_id'] . "',
			                    order_id = '" . (int)$order_info['order_id'] . "',
			                    description = '" . $this->_db->escape(sprintf($this->_language->get('text_order_no'), (float)$order_info['order_no'])) . "',
			                    amount = '" . (float)$order_total['value'] . "',
			                    date_added = NOW()");
		}
	}

	public function unconfirm($order_id) {
		$this->_db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}


}
?>
