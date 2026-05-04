<?php
class ModelPaymentWsbCredit extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/wsb_credit');

		$wsb_credit_status = $this->customer->getWsbCreditPaymentData();
		if (empty($wsb_credit_status) || $wsb_credit_status['status'] != 'ENABLED') {
			return array();
		}

		//Get customer Id from customer's private member
		$customer_id = $this->customer->getId();
		
		$wsb_credit_payment = new WsbCreditPayment($this);

        //Get Avaiable credit balace for given customer_id
        $wsb_credit_bal = $wsb_credit_payment->getAvaiableCreditBalance($customer_id);
        
        //Check if customer  has no WSB credit balance to place an order
        if($wsb_credit_bal <= 0){
            return array();
        }

		$method_data = array(
							'code'       => 'wsb_credit',
							'title'      => $this->language->get('text_title'),
							'terms'      => '',
							'sort_order' => $this->config->get('wsb_credit_sort_order')
						);

		return $method_data;
	}
}