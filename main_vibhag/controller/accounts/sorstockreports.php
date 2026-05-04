<?php
class ControllerAccountsSorStockReports extends Controller {
	/**
  * Download SOR data based on filters
  * @author: kalyan 25th Sep, 2017
  */
  public function index() {

		$data = array();
    $data['error'] = false;
		
    $this->load->autoLoadLanguage('accounts/sorstockreports', $data);
		$this->load->model('accounts/sorstockreports');

		$this->document->setTitle($data['heading_sor_report']);    
    
    	$filter_data 	 = array();
    	$datas 			   = array();
      $heading = array(
        'Store Name',
        'Product Code',
        'HSN',
        'Seller Name',
        'Quantity (sets)',
        'Price',
        'Total Value'
        );

	    $datas[0] = $heading; 
	    /*
      * setting filters
      */
      $data['filter_store_name'] = '';
	    if ( !empty($this->request->get['store_name']) ) {
	      $data['filter_store_name']    = $this->request->get['store_name'];
	      $filter_data['store_name']    = $this->request->get['store_name'];
	    }
	    /*
      * get sellers
      */
	    $seller_profile = new SellerProfile($this);

      $sellers = $seller_profile->getSellers(array('get_address'=>true));

        $data['filter_seller_name']   = '';
        $data['seller_id'] 	          = '';
        $data['filter_seller_id']     = '';

	    if ( !empty($this->request->get['seller_id']) ) {
	      $data['filter_seller_id']           = $this->request->get['seller_id'];
        $filter_data['seller_id']           = $this->request->get['seller_id'];
	      
	    }      

      foreach ($sellers as $key => $seller) {
          if( $data['filter_seller_id'] == $seller['seller_id']){
              $data['filter_seller_name'] = $seller['company'];
          }
      }
     
      $data['sellers'] = $sellers;
      /*
      * get stores
      */
      $stors = $this->model_accounts_sorstockreports->storeSList();
     
      $data['stors'] = $stors;
	   
	    $data['filter_date_to'] = '';
      if ( !empty($this->request->get['date_to']) ) {
        $data['filter_date_to']           = $this->request->get['date_to'];
        $filter_data['date_to']    = $this->request->get['date_to'];
      }


	    if(isset($this->request->get['download']) && $this->request->get['download'] == 'csv'){
	      /*
        * get all products
        */
	      $stockData = $this->model_accounts_sorstockreports->getSorStockReport($filter_data);
	      
	      if (!empty($stockData) && count($stockData)>0) {
	      	
	      	$i=1;
	      	
	      	foreach ($stockData as $key => $value) {
            /*
            * if select end date than calculate stock report fot that date
            */
            if ( !empty($this->request->get['date_to']) ) {
              /*
              * get total purchase
              */
              $purchaseQty  = $this->model_accounts_sorstockreports->getSorTotalPurchaseProductQty($value,$this->request->get['date_to']);
              /*
              * get total sale
              */
              $salesQty     = $this->model_accounts_sorstockreports->getSorTotalSalesProductQty($value,$this->request->get['date_to']);             

              $purchaseQty  = ($purchaseQty/$value['piece_in_set']);
             
              $stock        = ($purchaseQty-$salesQty);

              $value['quantity'] = $stock;

            }
            $datas[$i]['store_sales'] = $value['store_sales'];
	      		$datas[$i]['model'] = $value['model'];
            $datas[$i]['hsn_code'] = $value['hsn_code'];
            $datas[$i]['company'] = $value['company'];
            $datas[$i]['quantity'] = $value['quantity'];
            $datas[$i]['price'] = number_format($value['price_per_set'],2);
            $datas[$i]['total_price'] = number_format(($value['quantity']*$value['price_per_set']),2);

	      		$i++;
	      	}
	      }
	      /*
        * call csv genterate functin
        */
	      $this->generateSorStockCsv($datas);
	    }
	  $url='';
	  
    $data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_sor_report'],
			'href' => $this->url->link('accounts/sorstockreports', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		$data['action'] = $this->url->link('accounts/sorstockreports','token=' . $this->session->data['token'], 'SSL');

		$data['token']        = $this->session->data['token'];
		$data['header']       = $this->load->controller('common/header');
		$data['column_left']  = $this->load->controller('common/column_left');
		$data['footer']       = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('accounts/sorstockreports.tpl', $data));

	}
  /**
  * Generate csv file
  * @author: kalyan 25th Sep, 2017
  */
  private function generateSorStockCsv($csvData) {
  
    $file_name = DIR_DOWNLOAD .'sor_stock_report/sor_stock_report_'.time().'.csv';
    $fp = fopen($file_name, 'w');
    
    ob_clean();
    
    if(empty($csvData)){
      $data = array("No Data Found.");
      fputcsv($fp, $data);
    }else{
      
      foreach ($csvData as $line) {
          fputcsv($fp, $line);
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
}