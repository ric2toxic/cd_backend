<?php
class ModelTestTest extends Model
{

	public $db_crm;
	
	public function getDataForNeoGrowth($telephone) {
			$this->load->model('report/analysis', 'admin');
			
			$sql = "select customer_id, master_id from oc_customer where telephone='".$telephone."'";
			$query = $this->db->query($sql);
			$results = $query->rows;
			if ($query->num_rows < 1) {
				echo "No record found"; die;
			}
			$master_id = 0;
			
			foreach ($results as $result_customer) {
				$customer_id = $result_customer['customer_id'];
				$master_id = $result_customer['master_id'];
				//data is the request to filter orders 
				$data = array(
					'filter_customer_id'=>$customer_id,
					'filter_include_cancelled_orders'=>1,
					'filter_include_non_invoiced_orders'=>1
					
				);
				$orders = $this->admin_model_report_analysis->getOrderWiseMargin($data);
				
				
				
				$today = Date('d_M_y');
				$filename = $telephone."-".time().".csv";
				$dir_path =  DIR_SYSTEM . 'upload/assets/neo_growth';
				if (!is_dir($dir_path)) {
					mkdir($dir_path, 0777, true);
				}
				$i = 1;
				$full_path = $dir_path . '/'.$filename;
				$handle = fopen($full_path, 'w');

				$i = 1;
				foreach ($orders as $order) {

					$master_id = $master_id;
					$order_date = $order['date_added'];
					$order_number = $order['order_number'];
					$order_amount = $order['total'];
					

					if ($i == 1) {
						$i++;
						$data = array(
							'Order date',
							'Order number',
							'Order amount',
							'Client MID'
							
							);
							

						

					} else {
						$i++;
						$data = array($order_date, $order_number, $order_amount, $master_id);
					}
					
					
					fputcsv($handle,$data);

				}
				fclose($handle);
				sleep(5);
				if (file_exists($full_path)) {
					$csv_path = $full_path;

					$mail = new PHPMailer();
					$mail->isSMTP();
					$mail->SMTPSecure = 'ssl';
					$mail->SMTPDebug = 2;
					$mail->Debugoutput = 'html';
					
					$mail->Host = $controller->config->get('config_mail_smtp_hostname');
					
					$mail->Port = $controller->config->get('config_mail_smtp_port');
					$mail->SMTPAuth = true;
					$mail->Username = $controller->config->get('config_mail_smtp_username');
					$mail->Password = $controller->config->get('config_mail_smtp_password');
					$mail->setFrom($controller->config->get('config_mail_smtp_username'), 'Rakesh Singh');
					$mail->addReplyTo($controller->config->get('config_email'), 'Wholesale Box');
				    $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
					$mail->Subject = 'Short Message data';
					$mail->Body = 'Please find the csv of Short Message data - '.$telephone;
					$mail->AddAttachment($csv_path);
					//send to admin;
					$mail->send();
					sleep(2);
					unlink($csv_path);
					echo "Well Done!!!";
					exit;

				}
				
			}
			
			
	}
}
