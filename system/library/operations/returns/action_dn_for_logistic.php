 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionDnForLogistic extends ReturnActionBase 
{	
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
	 * @info: Public Method to check given return data is for generating CN
	 *         Over-writing parent method
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isCnGeneratable($return = array()){
		return true;
	}

	/**
	 * @info: Public Method to send DN updated email alert to accounts and return 
	 * @param:  $data Array
	 * @return: Boolen
	 * @author: MSA, July 2018
	*/
	public function sendDnUpdateEmail($data) {
		if(!empty($data)) {
			$mailer   = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	=> 'Order No '.$data['debit_note_info']['order_no'].' - Status: Debit Note Updated',
									'to'		=> array('returns', 'account'),
									'bcc'		=> array('vikas'), 
									'template'	=> '',
								    'html'		 => $this->getDnUpdateEmailTemplate($mailer, $data),
								    'attachments'=> !empty($data['attachments'])? $data['attachments']:''
									)
								);
			//Send Mail
			$mailer->send();
		}

	}

	/**
	 * @info: Public Method to generate HTML content for DN update email
	 * @param:  $mailer Object
	 * @param:  $data Array
	 * @return: Boolen
	 * @author: MSA, July 2018
	*/
	public function getDnUpdateEmailTemplate($mailer, $data)
	{
		$debit_note_no = $data['debit_note_info']['debit_note_prefix'].$data['debit_note_info']['debit_note_no'];

		$_html  = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html  = '<p>Dear All,</p>';
			$_html .= '<p>Debit Note <b>'.$debit_note_no.'</b> details has been updated.</p>';
			$_html .= '<p>Find updated debit note pdf file in attachment.</p>';
		$_html .= '</div>';
		return $_html;
	}

}//End of Class
