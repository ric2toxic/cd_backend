<?php
class ControllerAccountsSellerInvoiceVerification extends Controller
{

	public function index()
	{
	    //Load the language File 
		$this->load->language('accounts/seller_invoice');

		//Heading
		$this->document->setTitle($this->language->get('heading_title'));

		//Model
		$this->load->model('accounts/seller_invoice');
        $this->getList();

    }

    protected function getList()
    {
    	//Number of records per page
         $this->load->model('tool/image');
    	$this->config->set('config_limit_admin',15);

    	$this->load->model('accounts/seller_invoice');

         // Initializing
        $data = array();
        $filter_data = array();
        $general_url = '';

        // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('accounts/seller_invoice', $data);

        // Looping over GET request params
        foreach ( $this->request->get as $key => $value ) {
            // Deal with filter_% keys
            if ( stripos($key, 'filter_') === 0 ) {
                // Filter(s) to get Orders from Model
                $filter_data[$key] = $value;

                // Populating URL
                $general_url .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));

                // Populating data array
                $data[$key] = $value;
            }
        }

        // Sorting is always ORDER BY seller_invoice_id DESC; Getting Page number
        $page  = $this->request->get['page']  ?? 'FIRST';
        $filter_data['limit'] = 15;
        $filter_data['page'] = $page;

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
    
        $data['breadcrumbs'][] = array(
        'text' => $data['heading_title'],
        'href' => $this->url->link('accounts/seller_invoice_verification', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        //for downloading the result in CSV
        if ( !empty($this->request->get['download_seller_invoice_report']) ) {
            $filter_data['download_seller_invoice_report'] = $this->request->get['download_seller_invoice_report'];
        } else {
            $filter_data['download_seller_invoice_report'] = false;
        }

        $results = array();
        $invoices = $this->model_accounts_seller_invoice->getInvoices($filter_data);

        $data['invoices'] = array();
        if ($invoices) {

        	foreach ($invoices as $invoice_row) {
                if($invoice_row['invoice_image'] == '') 
                    { $invoice_row['invoice_image'] = 'https://cdnimages.net/img/placeholder.png';
                      $invoice_image_upload = 0;
                     }
                else
                    { 
                        $invoice_row['invoice_image'] = $this->model_tool_image->getOriginalImage($invoice_row['invoice_image']);
                        $invoice_image_upload = 1; 
                    }

        		$data['invoices'][] = array(
        			'seller_name' 			=> $invoice_row['seller_name'],
				    'seller_invoice_id' 	=> $invoice_row['seller_invoice_id'],
                    'seller_invoice_no'     => $invoice_row['seller_invoice_no'],
                    'suborder_id'           => $invoice_row['suborder_id'],
                    'invoice_image'         => $invoice_row['invoice_image'],
                    'invoice_image_upload'  => $invoice_image_upload,
				    'location' 				=> $invoice_row['location'],
				    'order_no' 				=> $invoice_row['order_no'],
				    'order_date' 			=> $invoice_row['order_date'],
				    'purchase_invoice_date' => $invoice_row['purchase_invoice_date'],
				    'purchase_invoice_no' 	=> $invoice_row['purchase_invoice_no'],
				    'total_value' 			=> $invoice_row['total_value'],
				    'total_product_value' 	=> $invoice_row['total_product_value'],
                    'total_tax'             => $invoice_row['total_tax'],
				    'status' 				=> $invoice_row['status'],
                    'comment'               => $invoice_row['comment']
        		);
        	}
        }

        //To Download the csv
        if ( isset($this->request->get['download_seller_invoice_report']) && $this->request->get['download_seller_invoice_report'] ) {

            $download_results = $data['invoices'];

            $file_name = DIR_DOWNLOAD .'invoices.csv';
            $fp = fopen($file_name, 'w');
            
            $data = array('Seller Name', 
                          'Seller City', 
                          'Order No', 
                          'Order Date', 
                          'Purchase Invoice Date', 
                          'Purchase Invoice No', 
                          'Total Value',
                          'Total Product Value',
                          'Total Tax',
                          'Status',
                          'Comments');
            fputcsv($fp, $data);
            if( !empty($invoices) ) {

                foreach ($download_results as $key => $sub_value) {
                    //pr($sub_value);
                    $seller_name   = isset($sub_value['seller_name']) ? $sub_value['seller_name'] : '';
                    $location      = isset($sub_value['location']) ? $sub_value['location'] : '';
                    $order_no      = isset($sub_value['order_no']) ? $sub_value['order_no'] : '';
                    $order_date    = isset($sub_value['order_date']) ? $sub_value['order_date'] : '';
                    $purchase_invoice_date  = isset($sub_value['purchase_invoice_date']) ? $sub_value['purchase_invoice_date'] : '';
                    $purchase_invoice_no = isset($sub_value['purchase_invoice_no']) ? $sub_value['purchase_invoice_no'] : '';
                    $total_value   = isset($sub_value['total_value']) ? $sub_value['total_value'] : ''; 
                    $total_product_value= isset($sub_value['total_product_value']) ? $sub_value['total_product_value'] : ''; 
                    $total_tax = isset($sub_value['total_tax']) ? $sub_value['total_tax'] : '' ;
                    $status = isset($sub_value['status']) ? $sub_value['status'] : 0 ;
                    $comment = isset($sub_value['comment']) ? $sub_value['comment'] : '' ;

                    if($sub_value['status'] == 0 && $sub_value['invoice_image_upload'] == 0)
                    {
                        $invoice_status = 'Pending';
                    }
                    else if($sub_value['status'] == 0 && $sub_value['invoice_image_upload'] == 1)
                    {    
                        $invoice_status = 'Received';
                    } else {
                        $invoice_status = 'Approved';
                    }

                    $data = array($seller_name, 
                                  $location,
                                  $order_no, 
                                  $order_date, 
                                  $purchase_invoice_date, 
                                  $purchase_invoice_no, 
                                  $total_value, 
                                  $total_product_value,
                                  $total_tax,
                                  $invoice_status,
                                  $comment
                                  );
                    fputcsv($fp, $data);
                }
            }    

            fclose($fp);

            if (file_exists($file_name)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_name));
                readfile($file_name);
                exit();
            }

        }

        $pagination = new PaginationV2();
        $pagination->page = $page;
        if(!empty($invoices)){
            $pagination->next = end($invoices)['seller_invoice_id'] ?? 0;
        }
        $pagination->total = count($invoices);
        $pagination->limit = 15;
        $pagination->url = $this->url->link('accounts/seller_invoice_verification', 'token=' . $this->session->data['token'] . $general_url . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();

        $data['token'] = $this->session->data['token'];

        $slr_profile = new SellerProfile($this);
        $data['cities'] = $slr_profile->getSellerCities();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('accounts/seller_invoice_list.tpl', $data));
	}

    /**
    * To Update oc_seller_invoice details.
    * 
    * @param $data array data
    * @author Ashish 
    */
    public function updateInvoiceComment()
    {
        $json = array();

        $comment = $this->request->get['comment'];
        $seller_inv_id = $this->request->get['seller_inv_id'];
        $status  = 1;
        
        $this->load->model('accounts/seller_invoice');
        $result = $this->model_accounts_seller_invoice->updateInvoiceStatus($comment, $seller_inv_id, $status);

        if ($result) {
            $json['success'] = $result;
        } else {
            $json['error'] = $this->language->get('error_action');
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        
    }


    /**
    * Method for update Seller Invoice No.
    * @request: seller_invoice_id : Integer of Seller Invoice Id
    * @request: seller_invoice_no : String of Seller Invoice No.
    * @return : json data with status message
    * @author : Vikas, Apr 2018
    */
    public function updateSellerInvoiceNo(){
        $json = array();
        
        if(empty($this->request->post['seller_invoice_id']) && empty($this->request->post['seller_invoice_no'])){
            $json['status'] = 1;
            $json['message'] = 'Empty Invoice no. received. It cannot be updated!';
            echo json_encode($json);
            exit;
        }

        $data = array();
        $data['seller_invoice_id'] = $this->request->post['seller_invoice_id'];
        $data['seller_invoice_no'] = $this->request->post['seller_invoice_no'];
        
        // regex for invoice no
        if(!preg_match("/^[A-Za-z0-9\/-]{1,16}$/", $data['seller_invoice_no'], $output_array)){
            $json['status'] =  3; 
            $json['message'] =  'Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/).'; 
            echo json_encode($json);
            exit;
        }

        $this->load->model('accounts/seller_invoice');

        $result = $this->model_accounts_seller_invoice->updateSellerInvoiceNo($data);

        if( $result ) {
            $json['status'] = 2;
            $json['message'] = 'Invoice no. updated successfully!!';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 1;
            $json['message'] = 'Seller invoice no. could not be updated. Please try again after some time. If issue persists, please contact Tech Administrator !!';
            echo json_encode($json);
            exit;
        }   
    }


    public function invoice_approve()
    {
        $json = array();
        if(empty($this->request->post['seller_invoice_id']))
        {
            $json['status'] = 0;
            $json['message'] = 'Empty Invoice no. received. It cannot be updated!';
            echo json_encode($json);
            exit;
        }

        $data = array();
        $data['seller_invoice_id'] = $this->request->post['seller_invoice_id'];
        $data['comment']           = $this->request->post['comment'];
        $data['suborder_id']       = $this->request->post['suborder_id'];
        $data['status']            = $this->request->post['status'];
        $this->load->model('accounts/seller_invoice');

       if($data['status'] == 1)
       { 
        if(!empty($this->request->post['updatedImage'])){
            $imageData = $this->request->post['updatedImage'];
            list($type, $imageData) = explode(';', $imageData);
            list($vls, $imagetype) = explode('image/', $type);
            list(, $imageData)      = explode(',', $imageData);
            $imageData = base64_decode($imageData);
            $temapname=time().'.'.trim($imagetype);
            $tmp_name='/tmp/'.$temapname;
            file_put_contents($tmp_name, $imageData);
            $file['name']=$temapname;
            $file['tmp_name']=$tmp_name;
            
            $image_name = $this->model_accounts_seller_invoice->uploadImgUsingCurl($file,$data['seller_invoice_id'],$data['suborder_id']);

        }
        else if(!empty($this->request->files['invoice_new_image']['name']) && !empty($this->request->files['invoice_new_image']['name']))
        {
            $file  = $this->request->files['invoice_new_image'];
            $image_name = $this->model_accounts_seller_invoice->uploadImgUsingCurl($file,$data['seller_invoice_id'],$data['suborder_id']);
        }
        $result = $this->model_accounts_seller_invoice->approveSellerInvoice($data);
       }
       else
       {
        $result = $this->model_accounts_seller_invoice->rejectSellerInvoice($data);
       } 

        if( $result ) {
            $json['status'] = 1;
            $json['message'] = 'Invoice update successfully!!';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 0;
            $json['message'] = 'Seller invoice no. could not be updated. Please try again after some time. If issue persists, please contact Tech Administrator !!';
            echo json_encode($json);
            exit;
        }
    }

    public function save_comment()
    {
        $json = array();
        if(empty($this->request->post['seller_invoice_id']))
        {
            $json['status'] = 0;
            $json['message'] = 'Empty Invoice no. received. It cannot be updated!';
            echo json_encode($json);
            exit;
        }

        $data = array();
        $data['seller_invoice_id'] = $this->request->post['seller_invoice_id'];
        $data['comment'] = $this->request->post['comment'];
        $this->load->model('accounts/seller_invoice');

        $result = $this->model_accounts_seller_invoice->SellerInvoiceComment($data);

        if( $result ) {
            $json['status'] = 1;
            $json['message'] = 'Comment updated successfully!!';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 0;
            $json['message'] = 'Seller invoice no. could not be updated. Please try again after some time. If issue persists, please contact Tech Administrator !!';
            echo json_encode($json);
            exit;
        }
    }    


}
?>