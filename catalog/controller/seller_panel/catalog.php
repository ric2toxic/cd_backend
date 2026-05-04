<?php
class ControllerSellerCatalog extends Controller {
	public  $data = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->MsLoader->MsHelper->addStyle('multiseller');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'),$this->language->load('product/product'));
	}
	/**
	 * get sellers from DB
	 */
	/*public function NotifySellerAboutInventory(){


		$sellers = $this->MsLoader->MsSeller->getSellers($this->customer->getId());
		;



		foreach ($sellers as $value){

			$seller_id = $value['seller_id'];
			$html = $this->load->view($this->config->get('config_template') . '/template/mail/sellerinventoryreview.tpl');
			$html = str_replace("[SELLERNAME]",$value['c.name'],$html);
			$products = $this->MsLoader->MsProduct->getProducts(
				array(
					'seller_id' => $seller_id ,
					'language_id' => $this->config->get('config_language_id'),
					'product_status' => array(MsProduct::STATUS_ACTIVE, MsProduct::STATUS_INACTIVE, MsProduct::STATUS_DISABLED, MsProduct::STATUS_UNPAID)
				)
			);

			$i = 1;
			$trdata = '';
			foreach($products as $rs){
				//echo '<pre>'; print_r($rs); exit;
				$img = $rs['p.image'];
				$image = "<img src='".$img."' />";
				$sku = $rs['p.sku'];
				$quantity = $rs['p.quantity'];

				$status = " ";



				 $trdata .= '<tr><td>'.$i.'</td><td>'.$image.'</td><td>'.$sku.'</td><td>'.$quantity.'</td><td>'.$status.'</td></tr>';
				//echo $trdata = '<tr><td>tseter</td></tr>'; exit;

				//echo $trdata = '<tr><td>'.$i.'</td></tr>'; exit;

				$i++;
			}
			//echo '<pre>'; print_r($trdata); exit;
			$html = str_replace("PRODUCTS",$trdata,$html);

			echo '<pre>'; print_r($html);
			//echo '<pre>'; print_r($products);


		}

		exit;



	}*/

	public function mailSend(){
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->maillerDebug = true;
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');

		$mail->addAddress("rathorekuldeep023@gmail.com", 'Customer');
		$mail->setFrom($this->config->get('config_email'), 'Wholesaelbox');
		$mail->Subject = "test subject";
		$mail->Body= "Test Content";

		if($mail->send()){
			echo 'success'; exit;
		}else{
			echo 'error'; exit;
		}

	}

}
?>