 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionShipmentLostbyCourierWhileResendingBack extends ReturnActionBase 
{	
	protected $action_name = 'Refund initiation';
	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry 	= $registry;
		
		if (method_exists($registry, 'get')) {
            $this->db 		= $registry->get('db');
            $this->load 	= $registry->get('load');
            $this->user   = $registry->get('user');
        } else {
            $this->db 		= $registry->db;
            $this->load 	= $registry->load;
            $this->user   = $registry->user;
        }
	}

	/**
	 * @info: Public Method to check given return data is for generating DN
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isDnGeneratable($return = array()){
		return true;
	}

	/**
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function sendEmail($data = array()) 
	{
		$this->emailToCustomer($data);
	}

	/**
	 * @info: Public Method to send customer email
	 * @param: $data Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function emailToCustomer($data)
	{
		if(!empty($data))
		{
			$mailer = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	  => 'Return/Replacement request for Order No '.$data['order_no'].' - Status Updated: '.$this->action_name,
									'to'		  => $data['customer']['email'],
									'cc'		  => '',
									'template'	  => '',
								    'html'		  => $this->customer_email_template($mailer, $data),
								    'attachments' => ''
									)
								);
			$mailer->send();
		}
	}

	/**
	 * @info: Public Method to get template for customer
	 * @param: Object ActionMailerClass object
	 * @param: Array $mail_data
	 * @return: String email template
	 * @author: Nishu, March 2018
	*/
	private function customer_email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html .= $mailer->getHeaderText($data['customer']['name']);
				$_html .= '<p>';
					$_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. ';
					$_html .= 'Courier partner has misplaced your parcel. We will initiate refund. ';
					$_html .= 'If you haven\'t added bank details then please add. Go to Wholsalebox Web/App – Login – My Account – Bank Details.';
				$_html .= '</p>';
			$_html .= '<p>Misplaced Products :</p>';
			$_html .= $mailer->getProductList($data);
		$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		return $_html;
	}


}//End of Class
