<?php /* Opencart Module v2.0 for Citrus Payment Gateway - Copyrighted file (viatechs.in) - Please do not modify/refactor/disasseble/extract any or all part content  */ ?>
<?php 
	class ModelPaymentWsbCreditCard extends Model
	{  
		public function getMethod($address, $total) 
		{
			$this->load->language('payment/wsb_credit_card');
            $method_data = array();
            // Oct 2019, customer level wsb_credit_card_status is removed, so for now permanently disabling this payment
			$status = false;
			if ($status) 
			{        
				$method_data = array(
					'code'=> 'wsb_credit_card',
					'title'=> $this->language->get('text_title'),
					'terms' => '',
					'sort_order' => $this->config->get('wsb_credit_card_sort_order'));
			}       
			return $method_data; 
		}
	}
?>