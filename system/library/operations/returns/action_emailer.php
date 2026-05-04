 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

/**
 * 	ActionEmailer
 * 	@author @MSA
 */
class ActionEmailer extends ReturnActionBase 
{	
	protected $email;
	protected $default_from = "returns";
	protected $default_reply = "returns";
	protected $default_subject = "WholesaleBox Returns ";
	protected $is_template = false;
	protected $template_data = array();
	protected $header;
	protected $html;
	protected $footer;

	/**
    * Class constructor to create class object and initialized data members
    * @param: Registry $registry
    * @return: void
    * @author: MSA, April 2018
    */
	public function __construct($registry) {
		parent::__construct($registry);
		
		$this->setting();
	}

	/**
    * setting() Public method to create PHPMailer object and set basic settings
    * @param:  void
    * @return: void
    * @author: MSA, April 2018
    */
	public function setting()
	{

		$this->email = new PHPMailer();
		$this->email->isSMTP();
		$this->email->Host = $this->config->get('config_mail_smtp_hostname');
		$this->email->Port = $this->config->get('config_mail_smtp_port');
		$this->email->SMTPSecure = 'ssl';
		$this->email->SMTPDebug = 0;
		//$this->email->Debugoutput = 'html';
		$this->email->isHTML(true);
		$this->email->SMTPAuth 	= true;
		$this->email->Username 	= $this->config->get('config_mail_smtp_username');
		$this->email->Password 	= $this->config->get('config_mail_smtp_password');

		$this->setHeader();
		$this->setFooter();
	}

	private function setHeader() {
		$template = new MailTemplate();
		$this->header = $template->getGeneralHeader();
	}

	private function setFooter() {
		$template = new MailTemplate();
		$this->footer = $template->getGeneralFooter();
	}

	/**
    * setSubject() Public method to set subject in mailer object
    * @param:  String $subject
    * @return: void
    * @author: MSA, April 2018
    */
	public function setSubject($subject) {
		if(!empty($subject)) {
			$this->email->Subject = $subject; //.' ('.date('d/m/Y H:i:s').')';
		}else{
			$this->email->Subject = $this->default_subject; //.' ('.date('d/m/Y H:i:s').')';	
		}
	}

	/**
    * setTo() Public method to set [to] email address in mailer object
    * @param:  String $to
    * @return: void
    * @author: MSA, April 2018
    */
	public function setTo($to) {
		if(!empty($to)) {
			if(is_array($to)) {
				foreach($to as $address) {
					if(array_key_exists($address, EMAIL_IDS)) {
						$this->email->addAddress(EMAIL_IDS[$address]['email_id'], EMAIL_IDS[$address]['name']);
					}else{
						$this->email->addAddress($address);
					}
				}
			}else{
				if(array_key_exists($to, EMAIL_IDS)) {
					$this->email->addAddress(EMAIL_IDS[$to]['email_id'], EMAIL_IDS[$to]['name']);;	
				}else{
					$this->email->addAddress($to);
				}
			}
		}
	}
	
	/**
    * setCC() Public method to set [cc] email addresses in mailer object
    * @param:  String $bcc
    * @return: void
    * @author: MSA, April 2018
    */
	public function setCC($cc) {
		if(!empty($cc)) {
			if(is_array($cc)) {
				foreach($cc as $address) {
					if(array_key_exists($address, EMAIL_IDS)) {
						$this->email->addCC(EMAIL_IDS[$address]['email_id'], EMAIL_IDS[$address]['name']);
					}else{
						$this->email->addCC($address);
					}
				}
			}else{ 
				if(array_key_exists($cc, EMAIL_IDS)) {
					$this->email->addCC(EMAIL_IDS[$cc]['email_id'], EMAIL_IDS[$cc]['name']);
				}else{
					$this->email->addCC($cc);
				}
			}
		}
	}
	
	/**
    * setBCC() Public method to set [bcc] email addresses in mailer object
    * @param:  String $bcc
    * @return: void
    * @author: MSA, April 2018
    */
	public function setBCC($bcc) {
		if(!empty($bcc)) {
			if(is_array($bcc)) {
				foreach($bcc as $address) {
					if(array_key_exists($address, EMAIL_IDS)) {
						$this->email->addBCC(EMAIL_IDS[$address]['email_id'], EMAIL_IDS[$address]['name']);
					}else{
						$this->email->addBCC($address);
					}
				}
			}else{
				if(array_key_exists($bcc, EMAIL_IDS)) {
					$this->email->addBCC(EMAIL_IDS[$bcc]['email_id'], EMAIL_IDS[$bcc]['name']);	
				}else{
					$this->email->addBCC($bcc);
				}
			}
		}
	}
	
	/**
    * setFrom() Public method to set [from] email address in mailer object
    * @param:  String $from
    * @return: void
    * @author: MSA, April 2018
    */
	public function setFrom($from='') {
		if(!empty($from)) {
			$this->email->setFrom(EMAIL_IDS[$from]['email_id'], EMAIL_IDS[$from]['name']);
		}else{
			$this->email->setFrom(EMAIL_IDS[$this->default_from]['email_id'], EMAIL_IDS[$this->default_from]['name']);
		}
	}

	/**
    * setReply() Public method to set [replyTo] email address in mailer object
    * @param:  String $reply
    * @return: void
    * @author: MSA, April 2018
    */
	public function setReply($reply='') {
		if(!empty($reply)) {
			$this->email->addReplyTo(EMAIL_IDS[$reply]['email_id'], EMAIL_IDS[$reply]['name']);
		}else{
			$this->email->addReplyTo(EMAIL_IDS[$this->default_reply]['email_id'], EMAIL_IDS[$this->default_reply]['name']);
		}
	}
	
	/**
    * setAttachment() Public method to add [attachments] with mailer object
    * @param:  String $attachments
    * @return: void
    * @author: MSA, April 2018
    */
	public function setAttachment($attachments = array())	{
		if(!empty($attachments)) {
			foreach($attachments as $file_path) {
				$this->email->AddAttachment($file_path);
			}
		}
	}
	
	/**
    * setBody() Public method to add [body] template with mailer object
    * @param:  String $template
    * @return: void
    * @author: MSA, April 2018
    */
	public function setBody($template='') {
		
		$body = $this->header;

		if($this->is_template) {
			$body .= $this->getBody($template);
		}else{
			$body .= $template;
		}

		$body .= $this->footer;
		//echo $body; die;
		$this->email->Body = $body;
	}
	
	/**
    * getBody() Public method to get [body] template html with mailer object
    * @param:  String $template
    * @return: void
    * @author: MSA, April 2018
    */
	public function getBody($template) {
		$_html = '';

		if(!empty($template)) {

			$action = isset( $template[0] ) ? strtolower( $template[0] ) : '';

			$data 	= isset( $template[1] ) ? $template[1] : '';

			$_html = $this->setEmailTemplate($action,$data);
			
		}

		return $_html; 
	}
	
	/**
    * emailSetting() Public method to set mailer object data
    * @param:  Array $params
    * @return: void
    * @author: MSA, April 2018
    */
	public function emailSetting($params = array()){
		//pr($params); die;
		$subject = isset($params['subject']) ? $params['subject'] : $this->default_subject;
		$this->setSubject($subject);
		
		$from = isset($params['from']) ? $params['from'] : $this->default_from;
		$this->setFrom($from);

		$to = isset($params['to']) ? $params['to'] : '';
		$this->setTo($to);
		
		$cc = isset($params['cc']) ? $params['cc'] : '';
		if(!empty($cc)) {
			$this->setCC($cc);	
		}
		
		$bcc = isset($params['bcc']) ? $params['bcc'] : '';
		if(!empty($bcc)) {
			$this->setBCC($bcc);	
		}
		
		$reply = isset($params['reply']) ? $params['reply'] : $this->default_reply;
		$this->setReply($reply);
		
		$template = isset($params['template']) ? $params['template'] : '';
		if(!empty($template)) {
			$this->is_template = true;
			$this->setBody($template);
		}else{
			$html = isset($params['html']) ? $params['html'] : '';
			$this->setBody($html);
		}
		
		$attchments = isset($params['attachments']) ? $params['attachments'] : '';
		if(!empty($attchments)) {
			$this->setAttachment($attchments);
		}
	}
	
	/**
    * send() Public method to send email
    * @param:  Boolean[0/1] $flag1
    * @param:  Boolean[0/1] $flag2
    * @return: void
    * @author: MSA, April 2018
    */
	public function send($flag1 = 0, $flag2 = false){
		if($this->email->Send($flag1,$flag2)) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
    * setEmailTemplate() Public method to select Html template as body part for mailer object
    * @param:  String $action
    * @param:  Array $data
    * @return: void
    * @author: MSA, April 2018
    */
	public function setEmailTemplate($action,$data) {

		$_html = '';
		
		if(method_exists($this, $action)) {

			$_html = $this->$action($data);

		}
		return $_html;
	}

	/**
    * getProductList() Public method to generate return item list
    * @param:  Array $data
    * @return: String
    * @author: MSA, April 2018
    */
	public function getProductList($data = array()) {
		$_html = '';
		$isExtraShortQuantity = 0;
		if(!empty($data['returns'])) 
		{
			$_html .= '<table style="font-family:Arial,Helvetica,sans-serif;max-width:680px" width="100%" cellspacing="1" cellpadding="1" border="1">';
				$_html .= '<tr>';
					$_html .= '<th>S.No</th>';
					$_html .= '<th>Image</th>';
					$_html .= '<th>Model</th>';
					$_html .= '<th>Reason</th>';
					$_html .= '<th>Return Type</th>';
					$_html .= '<th>Quantity</th>';
				if( ( isset($data['action_id']) && $data['action_id'] )
						&& 
						in_array(
							$data['action_id'],
							array(
								RETURN_ACTION_IDS['Extra_Goods_Received'],
								RETURN_ACTION_IDS['Short_Goods_Received']
							) 
						)
				  	){
						$_html .= '<th>Received Quantity</th>';
						$isExtraShortQuantity = 1;
					}	
					$_html .= '<th>Comment</th>';

				$_html .= '</tr>';
				$sno=1;
				foreach($data['returns'] as $return) {
					$_html .= '<tr>';
						$_html .= '<td>'.$sno.'</td>';
						$_html .= '<td><img src="'.$return['image'].'" alt="" /></td>';
						$_html .= '<td>'.$return['model'].'</td>';
						$_html .= '<td>'.$return['return_reason'].'</td>';
						$_html .= '<td>'.$return['return_type'].'</td>';
						
					if($isExtraShortQuantity){
						$_html .= '<td>'.$return['actual_quantity'].'</td>';
						$_html .= '<td>'.$return['return_quantity'].'</td>';
					}else{
						$_html .= '<td>'.$return['return_quantity'].'</td>';
					}	
						
						$_html .= '<td>'.$return['comment'].'</td>';

					$_html .= '</tr>';
				$sno++;}
			$_html .= '</table>';
			}

		if(!empty($data['relisted'])) 
		{
			$_html .= '<h4>New Listed Product</h4>';
			$_html .= '<table style="font-family:Arial,Helvetica,sans-serif;max-width:680px" width="100%" cellspacing="1" cellpadding="1" border="1">';
				$_html .= '<tr>';
					$_html .= '<th>S.No</th>';
					$_html .= '<th>Product ID</th>';
					$_html .= '<th>Image</th>';
					$_html .= '<th>Model</th>';
					$_html .= '<th>Quantity</th>';
					$_html .= '<th>Internal Note</th>';

				$_html .= '</tr>';
				$sno=1;
				foreach($data['relisted'] as $return) {
					$_html .= '<tr>';
						$_html .= '<td>'.$sno.'</td>';
						$_html .= '<td>'.$return['product_id'].'</td>';
						$_html .= '<td><img src="'.$return['image'].'" alt="" /></td>';
						$_html .= '<td>'.$return['model'].'</td>';
						$_html .= '<td>'.$return['return_quantity'].'</td>';	
						$_html .= '<td>'.$return['internal_note'].'</td>';
				$_html .= '</tr>';
				$sno++;}
			$_html .= '</table>';
		}

		return $_html;	
	}
	
	/**
    * getSellerProductList() Public method to generate return item list for seller email
    * @param:  Array $data
    * @return: String
    * @author: MSA, April 2018
    */
	public function getSellerProductList($data = array()) {
		$_html = '';
		$isExtraShortQuantity = 0;
		if(!empty($data['returns'])) 
		{
			$_html .= '<table style="font-family:Arial,Helvetica,sans-serif;max-width:680px" width="100%" cellspacing="1" cellpadding="1" border="1">';
				$_html .= '<tr>';
					$_html .= '<th>S.No</th>';
					$_html .= '<th>Image</th>';
					$_html .= '<th>SKU</th>';
					$_html .= '<th>Reason</th>';
					$_html .= '<th>Return Type</th>';
					$_html .= '<th>Quantity</th>';
				$_html .= '</tr>';
				$sno=1;
				foreach($data['returns'] as $return) {
					$_html .= '<tr>';
						$_html .= '<td>'.$sno.'</td>';
						$_html .= '<td><img src="'.$return['image'].'" alt="" /></td>';
						$_html .= '<td>'.$return['seller_sku'].'</td>';
						$_html .= '<td>'.$return['return_reason'].'</td>';
						$_html .= '<td>'.$return['return_type'].'</td>';
						$_html .= '<td>'.$return['return_quantity'].'</td>';
					$_html .= '</tr>';
				$sno++;}
			$_html .= '</table>';
			}

		

		return $_html;	
	}


	/**
    * getHeaderText() Public method to generate email header content
    * @param:  String $name
    * @return: String
    * @author: MSA, April 2018
    */
	public function getHeaderText($name='')
	{
		$_html = '<p>Dear '.ucfirst($name).',</p>';
		$_html .= '<p>Greetings from Wholesalebox.in</p>';
		return $_html;
	}

	/**
    * getFooterText() Public method to generate email footer content
    * @return: String
    * @author: MSA, April 2018
    */
	public function getFooterText($data = array()) {
		$_html = '<p>Please call '.RETURN_HELPLINE.' for any help or clarification.</p>';
		$_html .= '<p>Hoping to get a repeat order from you soon :)</p>';
		$_html .= '<p>Warm regards</p>';
		return $_html;
	}

	/**
    * getSellerFooterText() Public method to generate email footer content for seller emails
    * @return: String
    * @author: MSA, April 2018
    */
	public function getSellerFooterText($data = array()) {
		$_html  = '<p>Please call '.RETURN_HELPLINE.' for any help or clarification.</p>';
		$_html .= '<p>Warm regards <br> WholesaleBox Team</p>';
		return $_html;
	}


}//End of Class



