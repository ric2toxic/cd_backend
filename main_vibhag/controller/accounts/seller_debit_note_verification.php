<?php
class ControllerAccountsSellerDebitNoteVerification extends Controller
{

	public function index()
	{
	    //Load the language File 
		$this->load->language('accounts/debit_note');

		//Heading
		$this->document->setTitle($this->language->get('heading_title'));

		//Model
		$this->load->model('accounts/debit_note');
        $this->getList();

    }

    protected function getList()
    {
    	//Number of records per page
    	$this->config->set('config_limit_admin',15);

    	$this->load->model('accounts/debit_note');

		if (isset($this->request->get['filter_seller_name'])) {
            $filter_seller_name = $this->request->get['filter_seller_name'];
        } else {
            $filter_seller_name = null;
        }

        if (isset($this->request->get['filter_order_no'])) {
            $filter_order_no = $this->request->get['filter_order_no'];
        } else {
            $filter_order_no = null;
        }

        if (isset($this->request->get['filter_debit_note_date_from'])) {
            $filter_debit_note_date_from = $this->request->get['filter_debit_note_date_from'];
        } else {
            $filter_debit_note_date_from = null;
        }

        if (isset($this->request->get['filter_debit_note_date_to'])) {
            $filter_debit_note_date_to = $this->request->get['filter_debit_note_date_to'];
        } else {
            $filter_debit_note_date_to = null;
        }

        if (isset($this->request->get['filter_location'])) {
            $filter_location = $this->request->get['filter_location'];
        } else {
            $filter_location = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = -1;
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        //for downloading the result in CSV
        if ( !empty($this->request->get['download_debit_note_report']) ) {
            $download_debit_note_report = $this->request->get['download_debit_note_report'];
        } else {
            $download_debit_note_report = false;
        }

		//General URL (without sorting Pages)
		$url = '';

		if (isset($this->request->get['filter_seller_name'])) {
			$url .= '&filter_seller_name=' . urlencode(html_entity_decode($this->request->get['filter_seller_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_no'])) {
			$url .= '&filter_order_no=' . urlencode(html_entity_decode($this->request->get['filter_order_no'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_debit_note_date_from'])) {
			$url .= '&filter_debit_note_date_from=' . urlencode(html_entity_decode($this->request->get['filter_debit_note_date_from'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_debit_note_date_to'])) {
            $url .= '&filter_debit_note_date_to=' . urlencode(html_entity_decode($this->request->get['filter_debit_note_date_to'], ENT_QUOTES, 'UTF-8'));
        }

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		// URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;

        if (isset($this->request->get['sort'])) {
            $general_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['debit_note_id'])) {
            $general_url .= '&debit_note_id=' . $this->request->get['debit_note_id'];
        }

        if (isset($this->request->get['page'])) {
            $general_url .= '&page=' . $this->request->get['page'];
        }


        $data = array();

        // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('accounts/debit_note', $data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
    
        $data['breadcrumbs'][] = array(
        'text' => $data['heading_title'],
        'href' => $this->url->link('accounts/debit_note', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $filter_data = array(
            'filter_seller_name'          => $filter_seller_name,
            'filter_location'             => $filter_location,
            'filter_order_no'             => $filter_order_no,
            'filter_status'               => $filter_status,
            'filter_debit_note_date_from' => $filter_debit_note_date_from,
            'filter_debit_note_date_to'   => $filter_debit_note_date_to,
            'start'                       => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                       => $this->config->get('config_limit_admin'),
            'download_debit_note_report'  => $download_debit_note_report
        );

        $total_debit_notes = $this->model_accounts_debit_note->getTotalDebitNotes($filter_data);
        $results = array();
        $debit_notes = $this->model_accounts_debit_note->getDebitNotes($filter_data);

        $data['debit_notes'] = array();
        if ($debit_notes) {

        	foreach ($debit_notes as $debit_note_row) {
        		$data['debit_notes'][] = array(
        			'seller_name' 			=> $debit_note_row['seller_name'],
				    'debit_note_id' 	    => $debit_note_row['debit_note_id'],
				    'location' 				=> $debit_note_row['location'],
				    'order_no' 				=> $debit_note_row['order_no'],
				    'order_date' 			=> $debit_note_row['order_date'],
				    'debit_note_date'       => $debit_note_row['debit_note_date'],
				    'debit_note_no' 	    => $debit_note_row['debit_note_no'],
				    'status' 				=> $debit_note_row['status'],
                    'total_value'           => $debit_note_row['total_value'],
                    'tax'                   => $debit_note_row['tax'],
                    'product_value'         => $debit_note_row['product_value'],
                    'comment'               => $debit_note_row['comment']
        		);
        	}
        }

        //To Download the csv
        if ( isset($this->request->get['download_debit_note_report']) && $this->request->get['download_debit_note_report'] ) {
            
            $download_results = $data['debit_notes'];

            $file_name = DIR_DOWNLOAD .'debit_notes.csv';
            $fp = fopen($file_name, 'w');
            
            $data = array('Seller Name', 
                          'Seller City', 
                          'Order No', 
                          'Order Date', 
                          'Debit Note Date', 
                          'Debit Note No', 
                          'Total Value',
                          'Total Product Value',
                          'Total Tax',
                          'Status',
                          'Comments');
            fputcsv($fp, $data);
            if( !empty($debit_notes) ) {

                foreach ($download_results as $key => $sub_value) {
                    $seller_name   = isset($sub_value['seller_name']) ? $sub_value['seller_name'] : '';
                    $location      = isset($sub_value['location']) ? $sub_value['location'] : '';
                    $order_no      = isset($sub_value['order_no']) ? $sub_value['order_no'] : '';
                    $order_date    = isset($sub_value['order_date']) ? $sub_value['order_date'] : '';
                    $debit_note_date  = isset($sub_value['debit_note_date']) ? $sub_value['debit_note_date'] : '';
                    $debit_note_no = isset($sub_value['debit_note_no']) ? $sub_value['debit_note_no'] : '';
                    $total_value   = isset($sub_value['total_value']) ? $sub_value['total_value'] : ''; 
                    $product_value= isset($sub_value['product_value']) ? $sub_value['product_value'] : ''; 
                    $tax = isset($sub_value['tax']) ? $sub_value['tax'] : '' ;
                    $status = isset($sub_value['status']) ? $sub_value['status'] : 0 ;
                    $comment = isset($sub_value['comment']) ? $sub_value['comment'] : '' ;

                    if($status){
                        $debit_note_status = 'Received';
                    } else {
                        $debit_note_status = 'Pending';
                    }

                    $data = array($seller_name, 
                                  $location,
                                  $order_no, 
                                  $order_date, 
                                  $debit_note_date, 
                                  $debit_note_no, 
                                  $total_value, 
                                  $product_value,
                                  $tax,
                                  $debit_note_status,
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

        // URL for pagination
        $pagination_url = $url;

        if (isset($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $total_debit_notes;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('accounts/seller_debit_note_verification', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($total_debit_notes) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_debit_notes - $this->config->get('config_limit_admin'))) ? $total_debit_notes : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_debit_notes, ceil($total_debit_notes / $this->config->get('config_limit_admin')));

        $data['token'] = $this->session->data['token'];

        $slr_profile = new SellerProfile($this);
        $data['cities'] = $slr_profile->getSellerCities();


        $data['filter_seller_name']          = $filter_seller_name;
        $data['filter_order_no']             = $filter_order_no;
        $data['filter_location']             = $filter_location;
        $data['filter_status']               = $filter_status;
        $data['filter_debit_note_date_from'] = $filter_debit_note_date_from;
        $data['filter_debit_note_date_to']   = $filter_debit_note_date_to;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('accounts/debit_note_list.tpl', $data));
	}

    /**
    * To Update oc_seller_deit_note details.
    * 
    * @param $data array data
    * @author Ashish 
    */
    public function updateStatusComment()
    {
        $json = array();

        $comment = $this->request->get['comment'];
        $debit_note_id = $this->request->get['debt_note_id'];
        $status  = 1;
        
        $this->load->model('accounts/debit_note');
        $result = $this->model_accounts_debit_note->updateDebitNoteStatusAndComment($comment, $debit_note_id, $status);

        if ($result) {
            $json['success'] = $result;
        } else {
            $json['error'] = $this->language->get('error_action');
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        
    }
}
?>