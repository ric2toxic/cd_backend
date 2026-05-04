<?php
class ControllerSaleTentativeRefund extends Controller {
	public function index() {
		$this->load->language('sale/return');
		$this->document->setTitle('Tentative Refund');
		$this->load->model('sale/tentative_refund');
		$this->getList();	
	}

	protected function getList() {
		$token = $this->session->data['token'];
		$url = '';
		$filter_data = array();
		if (isset($this->request->get['filter_order_no'])) {
			$filter_data['filter_order_no'] = $this->request->get['filter_order_no'];
			$url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
		} else {
			$filter_data['filter_order_no'] = null;
		}

		if (isset($this->request->get['filter_refund_type'])) {
			$filter_data['filter_refund_type'] = $this->request->get['filter_refund_type'];
			$url .= '&filter_refund_type=' . urlencode(html_entity_decode($this->request->get['filter_refund_type'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_refund_type'] = null;
		}

		if (isset($this->request->get['filter_is_approved'])) {
			$filter_data['filter_is_approved'] = $this->request->get['filter_is_approved'];
			$url .= '&filter_is_approved=' . urlencode(html_entity_decode($this->request->get['filter_is_approved'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_is_approved'] = null;
		}

		if (isset($this->request->get['filter_refund_date_from'])) {
			$filter_data['filter_refund_date_from'] = $this->request->get['filter_refund_date_from'];
			$url .= '&filter_refund_date_from=' . urlencode(html_entity_decode($this->request->get['filter_refund_date_from'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_refund_date_from'] = null;
		}
		if (isset($this->request->get['filter_refund_date_to'])) {
			$filter_data['filter_refund_date_to'] = $this->request->get['filter_refund_date_to'];
			$url .= '&filter_refund_date_to=' . urlencode(html_entity_decode($this->request->get['filter_refund_date_to'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_refund_date_to'] = null;
		}

		if (isset($this->request->get['filter_cn_date_from'])) {
			$filter_data['filter_cn_date_from'] = $this->request->get['filter_cn_date_from'];
			$url .= '&filter_cn_date_from=' . urlencode(html_entity_decode($this->request->get['filter_cn_date_from'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_cn_date_from'] = null;
		}
		if (isset($this->request->get['filter_cn_date_to'])) {
			$filter_data['filter_cn_date_to'] = $this->request->get['filter_cn_date_to'];
			$url .= '&filter_cn_date_to=' . urlencode(html_entity_decode($this->request->get['filter_cn_date_to'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_data['filter_cn_date_to'] = null;
		}


		if (isset($this->request->get['sort'])) {
			$filter_data['sort'] = $this->request->get['sort'];
		} else {
			$filter_data['sort'] = 'tr.date_added';
		}

		if (isset($this->request->get['order'])) {
			$filter_data['order'] = $this->request->get['order'];
		} else {
			$filter_data['order'] = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$filter_data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
		$filter_data['limit'] = $this->config->get('config_limit_admin');


	    // URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;

		if (isset($this->request->get['sort'])) {
			$general_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$general_url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$general_url .= '&page=' . $this->request->get['page'];
		}

		$data = array();// Initializing the data array to be passed on to template files
		$data = $filter_data;
        // Autoloading the lanugage
		$this->load->autoLoadLanguage('sale/return',$data);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $token, 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => 'Tentative Refund List',
			'href' => $this->url->link('sale/tentative_refund', 'token=' . $token . $general_url, 'SSL')
		);

		$data['download'] = $this->url->link('sale/tentative_refund/download', 'token=' . $token . $url, 'SSL');

		//Get total count for all return
		$return_total = $this->model_sale_tentative_refund->getAllTentativeRefunds($filter_data, 'count');
		//Get Data for all return which is showable
		$refunds = $this->model_sale_tentative_refund->getAllTentativeRefunds($filter_data, 'data');
		if(isset($refunds['refunds'])){
			$data['refunds'] = $refunds['refunds'];
		}else{
			$data['refunds'] = null;
		}

		if(isset($refunds['cn_amount'])){
			$data['cn_amount'] = $refunds['cn_amount'];
		}else{
			$data['cn_amount'] = null;
		}

		if(isset($refunds['payment'])){
			$data['payment'] = $refunds['payment'];
		}else{
			$data['payment'] = null;
		}

		if(isset($refunds['total_invoice'])){
			$data['total_invoice'] = $refunds['total_invoice'];
		}else{
			$data['total_invoice'] = null;
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		// URL For sorting
        $sort_url = $url;
		if ($filter_data['order'] == 'ASC') {
			$sort_url .= '&order=DESC';
		} else {
			$sort_url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$sort_url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_no'] = $this->url->link('sale/tentative_refund', 'token=' . $token . '&sort=oo.order_no' . $sort_url, 'SSL');
		$data['sort_customer'] = $this->url->link('sale/tentative_refund', 'token=' . $token . '&sort=oo.firstname' . $sort_url, 'SSL');
		
		// URL for pagination
		$pagination_url = $url;

		if (isset($this->request->get['sort'])) {
			$pagination_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$pagination_url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $return_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/tentative_refund', 'token=' . $token . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['token'] = $token;

		$data['is_approved_data'] = array('0'=>'NO',
										  '1'=>'YES',
										  '2'=>'REJECTED',
										  '3'=>'ON HOLD'
										);
		$data['refund_type_data'] = array('CREDIT_NOTE'=>'CREDIT_NOTE',
										  'EXCESS_PAYMENT_BY_CUSTOMER'=>'EXCESS_PAYMENT_BY_CUSTOMER'
										);
		$data['results'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($return_total - $this->config->get('config_limit_admin'))) ? $return_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $return_total, ceil($return_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('sale/tentative_refund_list.tpl', $data));
	}

	public function download(){
    	$filter_data = array();
        $url = array();
        foreach ($this->request->get as $key => $value) {
            if( $key != 'route' && $key != 'token' ){
                $filter_data[$key] = $value;
                $url[] = "$key=$value";
            }
        }
        $url = implode('&',$url);
        $this->load->model('sale/tentative_refund');
		$results = $this->model_sale_tentative_refund->getAllTentativeRefunds($filter_data, 'download');

    	require_once DIR_SYSTEM.'library/PHPExcel/IOFactory.php';
		$phpExcel = new PHPExcel;

		$phpExcel->getDefaultStyle()->getFont()->setName('Arial Black');
		$phpExcel->getDefaultStyle()->getFont()->setSize(14);
		$phpExcel ->getProperties()->setTitle("Bulk Inventory Listing");
		$phpExcel ->getProperties()->setCreator("Wholesalebox");
		$phpExcel ->getProperties()->setDescription("Excel for Tentative Refunds Listing");

		// Creating PHPExcel spreadsheet writer object
		$writer = PHPExcel_IOFactory::createWriter($phpExcel, "Excel2007");

		// will get the already created sheet

		$sheet = $phpExcel ->getActiveSheet();

		// setting title of the sheet
		$sheet->setTitle('Tentative Refund');

		// Creating spreadsheet header
		$sheet ->getCell('A1')->setValue('Order No');
		$sheet ->getCell('B1')->setValue('Customer');
		$sheet ->getCell('C1')->setValue('Email');
		$sheet ->getCell('D1')->setValue('Company');
		$sheet ->getCell('E1')->setValue('City');
		$sheet ->getCell('F1')->setValue('Net Invoice Value(A):(Total Invoices-TotalCNs)');
		$sheet ->getCell('G1')->setValue('Payment Received(B):(Total Received-TotalRefunds)');
		$sheet ->getCell('H1')->setValue('Balance Refund: (B-A)');
		$sheet ->getCell('I1')->setValue('Ref Id');
		$sheet ->getCell('J1')->setValue('Refund Ref');
		$sheet ->getCell('K1')->setValue('Refund Amt');
		$sheet ->getCell('L1')->setValue('Date');
		$sheet ->getCell('M1')->setValue('Is Approved');
		$row_count = 2;
		$total_invoice = $results['total_invoice'];
		$cn_amount     = $results['cn_amount'];
		$payment       = $results['payment'];
		foreach ($results['refunds'] as $result){
    		$order_id = $result['order_id'];
			//Calculate Net invoice value
			$net_invoice_val = '';
			$invoice_bal = 0;
            if(isset($total_invoice[$order_id])){
              $net_invoice_val .= $total_invoice[$order_id];
              $invoice_bal += (float)$total_invoice[$order_id];
            }
            if(isset($cn_amount[$order_id])){
              $cn = (-1)*(float)$cn_amount[$order_id];
              $net_invoice_val .= $cn;
              $invoice_bal += (float)$cn;
            }
            $net_invoice_val .= ' = Rs. '.$invoice_bal;

            //Calculate net payment received
            $net_payment_val = '';
            $payment_bal = 0;
			if(isset($payment[$order_id]['amount'])){
				$net_payment_val .= $payment[$order_id]['amount'];
				$payment_bal += (float)$payment[$order_id]['amount'];
			}
			if(isset($payment[$order_id]['refund'])){
				if(empty($payment[$order_id]['refund'])){
				  $refund_val = ' - '.$payment[$order_id]['refund'];
				}else{
				  $refund_val = ' '.$payment[$order_id]['refund'];
				}
				$net_payment_val .= $refund_val;
				$payment_bal += (float)$refund_val;
			}
			$net_payment_val .= ' = Rs. '.$payment_bal;

			//Calculate Balance Refund
			$bal_refund = ROUND($payment_bal-$invoice_bal, 2);

			$sheet ->getCell('A'.$row_count)->setValue($result['order_no']);
			$sheet ->getCell('B'.$row_count)->setValue($result['customer']);
			$sheet ->getCell('C'.$row_count)->setValue($result['email']);
			$sheet ->getCell('D'.$row_count)->setValue($result['shipping_company']);
			$sheet ->getCell('E'.$row_count)->setValue($result['shipping_city']);
			$sheet ->getCell('F'.$row_count)->setValue($net_invoice_val);
			$sheet ->getCell('G'.$row_count)->setValue($net_payment_val);
			$sheet ->getCell('H'.$row_count)->setValue($bal_refund);
			$sheet ->getCell('I'.$row_count)->setValue($result['ref_id']);
			$sheet ->getCell('J'.$row_count)->setValue($result['refund_ref']);
			$sheet ->getCell('K'.$row_count)->setValue($result['total_refund']);
			$sheet ->getCell('L'.$row_count)->setValue($result['date_added']);
			$is_approved = 'NO';
			if(!empty($result['is_approved']) && $result['is_approved'] == 1 ){
				$is_approved = 'YES';
			}else if(!empty($result['is_approved']) && $result['is_approved'] == 2 ){
				$is_approved = 'REJECTED';
			}
			$sheet ->getCell('M'.$row_count)->setValue($is_approved);
			$row_count++;
		}
		// Making headers text bold and larger
		$sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(12);

		// Autosize the columns
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		// download file 
		$file_path =  DIR_DOWNLOAD.'tentative_refund.xlsx';
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=".basename($file_path));
		header("Cache-Control: max-age=0");
		ob_clean();
		$writer->save('php://output');
		flush();
		readfile($file_path);
        exit();
	}

	public function updateIsApprovedTentativeRefund(){
		$data = $this->request->post;
		$this->load->model('sale/tentative_refund');
		$this->model_sale_tentative_refund->updateIsApprovedTentativeRefund($data);
		exit();
	}
}
