<?php

/**
 * Class to carry out secure file download operations.
 * It handles file downloads from both admin and frontend areas
 * All various file downloads which we create must be routed through
 * this function. It handles the following tasks.
 *  - Create file path.
 *  - Generate file download link whenever a page is opened where file
 *    download option is to be given.
 *  - Returns a serialized and encoded filepath string to be entered into db
 *  - Allows file download if certain permissions and restrictions are cleared.
 * @Author Vikas, 2016
 */
class SecureFileDownload {

    // @note Future file download cases need to be updated here
    /* This mentions the file description (type of file) */
	private $_directory_map = array(

		'shipping_label' 	=> DIR_DLOAD_SHP_LBL,
		'gati_docket_csv' 	=> DIR_DLOAD_GATI_DKT,
		'seller_invoice' 	=> DIR_DLOAD_SLR_INV,
		'seller_debit_note' => DIR_DLOAD_SLR_DBT_NOTE,
		'seller_replacement_note' => DIR_DLOAD_SLR_RPMT_NOTE,
		'buyer_b2b_invoice'	=> DIR_DLOAD_BYR_INV,
		'buyer_b2c_invoice'	=> DIR_DLOAD_BYR_INV,
		'buyer_credit_note'	=> DIR_DLOAD_BYR_CDT_NOTE,
		'wsb_prchse_inv_img'=> DIR_WSB_PURCHASE_IMAGE,
		'franchise_invoice' => DIR_DLOAD_BYR_INV,
		'wsb_purchase_return' => DIR_DLOAD_SLR_DBT_NOTE,
		'return_shipping_slip' => DIR_UPLOAD,
		'return_shipping_label'=> DIR_DLOAD_SHP_LBL
		);

	// @note Future file download cases need to be updated here
	/* This mentiond the file extension that is attached with this file desc (type) */
	private $_format_map = array(

        'shipping_label' 	=> 'pdf',
		'gati_docket_csv' 	=> 'csv',
		'seller_invoice' 	=> 'pdf',
		'seller_debit_note' => 'pdf',
		'seller_replacement_note' => 'pdf',
		'buyer_b2b_invoice'	=> 'pdf',
		'buyer_b2c_invoice'	=> 'pdf',
		'buyer_credit_note'	=> 'pdf',
		'wsb_prchse_inv_img'=> 'jpg',
		'franchise_invoice'	=> 'pdf',
		'wsb_purchase_return' => 'pdf',
		'return_shipping_label'=> 'pdf'
		);

	// @note Future file download cases need to be updated here
	/* This mentiond the admission maximum permission level required for this file desc (type) */
	private $_admin_permission_map = array(

		'shipping_label' 	=> array('access', 'sale/order'),
		'gati_docket_csv' 	=> array('access', 'sale/order'),
		'seller_invoice' 	=> array('access', 'sale/order'),
		'seller_debit_note' => array('access', 'sale/return'),
		'seller_replacement_note' => array('access', 'sale/return'),
		'buyer_b2b_invoice'	=> array('access', 'sale/order'),
		'buyer_b2c_invoice'	=> array('access', 'sale/order'),
		'buyer_credit_note'	=> array('access', 'sale/order'),
		'wsb_prchse_inv_img'=> array('access', 'wsb_purchase/import'),
		'franchise_invoice' => array(),
		'wsb_purchase_return' => array('access', 'wsb_purchase/purchase_return'),
		'return_shipping_label' => array('access', 'sale/return'),
		);

    // Storing important objects from registry
    private $_registry;
    private $_session;
    private $_url;
    private $_db;

    /**
     * Constructor
     */
    public function __construct($registry) {
    	$this->_registry = $registry;
    	$this->_session = $registry->get('session');
    	$this->_url = $registry->get('url');
    	$this->_db = $registry->get('db');
    }

	/**
	 * This method creates a BuyerInvoice
	 * @param file_desc (string)
	 * Current allowed options are: 'shipping_label', 'gati_docket_csv', 'seller_invoice'
	 * @param file_name (string) - File name without extension (eg: xyz only (no .pdf))
	 * @return Array
	 * - 'download_link' key => Returns a file download link with serialization on file
	 * - 'db_file_string' key => Returns a file path (serialized) for db entry (if needed).
	 * - 'clean_file_path' => needed to save the file in the particular folder
	 * @warning For invalid file_desc inputs, it returns FALSE.
	 * @note This method is called by controllers. It is assumed that permission checks
	 * have been done at the controller end itself
	 * @usage - This method is called by controller(s).
	 * @author Vikas, 2016
	 */
	public function generateFileDownload ($file_desc, $file_name) {
		
		// First checking whether the file_desc is a valid case
		if ( !isset($this->_directory_map[$file_desc]) ) {
			return false;
		}

		// initializing return data array
		$return_data = array();

		$today = Date('d_M_y');

		// create directory if not exists
		if ( !file_exists( $this->_directory_map[$file_desc] . $today ) ) {
			mkdir($this->_directory_map[$file_desc] . $today, 0775, true);
		}

		// Creating today_date/file_name.file_extension string
		$rel_file_path = $today . '/' . $file_name . '.' . $this->_format_map[$file_desc];

		// Creating serialized relative file path string (it will also be db file string)
		$return_data['db_file_string'] = base64_encode($rel_file_path);

		// Creating download url link
		$url = '&file_desc=' . $file_desc . '&file_path=' . $return_data['db_file_string'];
		if ( !empty($this->_session->data['token']) ) {
			$url = 'token=' . $this->_session->data['token'] . $url;
		}
		$return_data['download_link'] = html_entity_decode($this->_url->link('download/download',
			                                               $url,
			                                               'SSL'));

		// Create complete clean file path
		$return_data['clean_file_path'] = $this->_directory_map[$file_desc] . $rel_file_path;

		return $return_data;
	}


	/**
	 * This function given a serialized file path and file desc
	 * returns the file download link
	 * @param file_desc (string)
	 * @param serialized_file_path (string). @warning It assumes that it is serialized path from db
	 * @param check_download , boolean defaulted to true - to check if download file exists or not
	 * @return file_download_link string
	 * @note returns false if invalid input data or if check_download is true and file does not exist
	 * @author Vikas, 2016
	 */
	public function getDownloadLink($file_desc, $serialized_file_path, $check_download = true) {
		// First checking whether the file_desc is a valid case
		if ( !isset($this->_directory_map[$file_desc]) ) {
			return false;
		}

		// Check if file exists or not physically
		if ( $check_download && !$this->checkDownloadExists($file_desc, $serialized_file_path) ) {
			return false;
		}

		$url = '&file_desc=' . $file_desc . '&file_path=' . $serialized_file_path;

		if ( !empty($this->_session->data['token']) ) {
			$url = 'token=' . $this->_session->data['token'] . $url;
		}

		return (html_entity_decode($this->_url->link('download/download', $url, 'SSL')));
	}


	/**
	 * This function checks if the file physically exists on disk or not
	 * Protected method. Internally called.
	 * @param file_desc (string). assumed that it is validated at the calling method level
	 * @param serialized_file_path (string). @warning It assumes that it is serialized path from db
	 * @return true if exists, else false
	 * @author Vikas, 2016
	 */
	protected function checkDownloadExists($file_desc, $serialized_file_path) {

		if(empty($serialized_file_path)){
			return false;
		}

		$file = $this->_directory_map[$file_desc] . base64_decode($serialized_file_path);
    if ( file_exists( $file ) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * This function created for downloading file
	 * @param $get_request array. It should contain the following:
	 * @note: 'file_path' have a serialize value of file path.
	 * @note: 'file_desc' have single value. (available values can be checked from the map above)
	 * @param $caller ('admin', 'frontend' or 'public')
	 * @param $to_download : defaults to True. True means file is downloaded. False means filepath is returned
	 * @return File Downloaded; else Filepath or False (in invalid access or data given)
	 * @note Numerous permission checks etc are coded in here case by case
	 * @author Vikas, 2016
	 */
	public function downloadFile($get_request, $caller, $to_download=true) {

    if ( empty($get_request['file_desc']) or empty($get_request['file_path']) ) {
			return false; // insufficient data
		}

		$file_desc = $get_request['file_desc'];
		$file_path = $get_request['file_path'];

		// callerwise case handling
		if ( $caller == 'admin' ) {
			// check permission
			$user = $this->_registry->get('user');
			if ( !($user->hasPermission($this->_admin_permission_map[$file_desc][0],
				                        $this->_admin_permission_map[$file_desc][1])) ) {
				return false;
			}
			// Certain file types are allowed to be recreated
			if ($file_desc == 'seller_debit_note') {
				$debit_note = new DebitNote($this->_registry,$file_path);
				$file_path = $debit_note->getFile();
			}
			if ($file_desc == 'seller_replacement_note') { 
				$debit_note = new DebitNote($this->_registry,$file_path);
				$file_path = $debit_note->getReplacementFile();
			}
			if ($file_desc == 'wsb_purchase_return') {
				$wsb_debit_note = new WsbPurchaseDebitNote($this->_registry,$file_path);
				$file_path      = $wsb_debit_note->getDnByPurchaseId();
			}
			if ($file_desc == 'buyer_credit_note') {
				$credit_note = new CreditNote($this->_registry,$file_path);
				$file_path = $credit_note->getFile();
			}

            // Certain file types are allowed to be recreated
			if ($file_desc == 'seller_invoice') {
        		$seller_invoice = new SellerInvoice($this->_registry,$file_path);
				$file_path = $seller_invoice->getFile();
			}

			if ($file_desc == 'buyer_b2b_invoice') {

        		$buyer_invoice = new BuyerInvoice( $this->_registry , $file_path );
                $file_path = $buyer_invoice->getFile();
			}

			if ($file_desc == 'buyer_b2c_invoice') {
        		$buyer_invoice = new BuyerInvoice( $this->_registry , $file_path );
                $buyer_invoice->setOptions('file_type','b2c');
				$file_path = $buyer_invoice->getFile();
			}

			if ($file_desc == 'wsb_prchse_inv_img') {
        		$wsb_pur_img = new WsbPurchaseInvoiceImage( $this->_registry , $file_path );
				$file_path = $wsb_pur_img->getDownloadImage();
			}

		} elseif ( $caller == 'frontend') {

			// Check if customer is logged in or not
			$customer = $this->_registry->get('customer');
			
			if ( !$customer-> isLogged() ) {
				return false;
			}

			// get customer id because login test is passed
			$customer_id = $customer->getId();

			// @Note : all file_desc cases should be handled here
			if ( $file_desc == 'gati_docket_csv' ) {

				return false; // It cant be downloaded from frontend

			} elseif ( $file_desc == 'seller_invoice' ) {

        		$seller_invoice = new SellerInvoice($this->_registry,$file_path);
	    		if($customer_id == $seller_invoice->getSellerId()){
	          		$file_path = $seller_invoice->getFile();
	        	} else {
	          	return false;
        	}


			} elseif ( $file_desc == 'shipping_label' ) {
				// @todo db needs to be expanded to handle seller wise
				return false; // currently blocking download from frontend

			} elseif( $file_desc == 'seller_debit_note'){
				// Certain file types are allowed to be recreated
				$debit_note = new DebitNote($this->_registry,$file_path);
				$seller_id = $debit_note->getSellerId();
				if($seller_id == $customer_id){
					$file_path = $debit_note->getFile();
				}else{
					return false;
				}

			} elseif( $file_desc == 'buyer_b2b_invoice' ){
				//$this->_load->model('checkout/order','frontend');
        		$buyer_invoice = new BuyerInvoice( $this->_registry , $file_path );
        		$buyer_id = $buyer_invoice->getCustomerId();
        		if( $buyer_id ==  $customer_id ){
        			$file_path = $buyer_invoice->getFile();
        		}
        		else{
        			return false;
        		}

			} elseif ($file_desc == 'buyer_credit_note') {
				$credit_note = new CreditNote($this->_registry,$file_path);
				$file_path = $credit_note->getFile();
			}

			elseif( $file_desc == 'buyer_b2c_invoice' ){
				//$this->_load->model('checkout/order','frontend');
        		$buyer_invoice = new BuyerInvoice( $this->_registry , $file_path );
        		$buyer_id = $buyer_invoice->getCustomerId();
        		if( $buyer_id ==  $customer_id ){
                    $buyer_invoice->setOptions('file_type','b2c');
        			$file_path = $buyer_invoice->getFile();
        		}
        		else{
        			return false;
        		}
			}elseif( $file_desc == 'franchise_invoice' ){
				$franchise_invoice = new FranchiseInvoice( $this->_registry , $file_path );
        		$file_path = $franchise_invoice->getFile();
			} elseif ($file_desc == 'wsb_prchse_inv_img') {
        		$wsb_pur_img = new WsbPurchaseInvoiceImage( $this->_registry , $file_path );
				$file_path = $wsb_pur_img->getDownloadImage();
			} elseif($file_desc == 'wsb_purchase_return') {
				$wsb_debit_note = new WsbPurchaseDebitNote($this->_registry,$file_path);
				$file_path      = $wsb_debit_note->getDnByPurchaseId();
			} else {
				return false;
			}

		}elseif ( $caller == 'app') {
			if ($file_desc == 'buyer_credit_note') {
				$credit_note = new CreditNote($this->_registry,$file_path);
				$file_path = $credit_note->getFile();
			}elseif( $file_desc == 'franchise_invoice' ){
				$franchise_invoice = new FranchiseInvoice( $this->_registry , $file_path );
        		$file_path = $franchise_invoice->getFile();
			} elseif ( $file_desc == 'seller_invoice' ) {
                $seller_invoice = new SellerInvoice($this->_registry,$file_path);
                $file_path = $seller_invoice->getFile();
			} elseif ( $file_desc == 'seller_debit_note' ) {
                $debit_note = new DebitNote($this->_registry,$file_path);
                $file_path = $debit_note->getFile();
            }elseif( $file_desc == 'buyer_b2b_invoice' ){
        		$buyer_invoice = new BuyerInvoice( $this->_registry , $file_path );
        		$buyer_id = $buyer_invoice->getCustomerId();
        		$file_path = $buyer_invoice->getFile();
			}elseif( $file_desc == 'buyer_b2c_invoice' ){
				$buyer_invoice = new BuyerInvoice( $this->_registry , $file_path );
        		$buyer_id = $buyer_invoice->getCustomerId();
        		$buyer_invoice->setOptions('file_type','b2c');
        		$file_path = $buyer_invoice->getFile();
			}

		}else{
			// all other cases are disabled
			return false;
		}

		// If we are here, it implies that everything has passed.
		$file = $this->_directory_map[$file_desc] . base64_decode($file_path);
		// File path is required instead of downloading
		//echo $file; die;

		if (!$to_download)
		header('Content-Description: File Transfer');
		if ( $this->_format_map[$file_desc] == 'pdf' ) {
			header('Content-Type: application/pdf');
		} elseif ( $this->_format_map[$file_desc] == 'csv' ) {
			header('Content-Type: application/csv');
		} else {
			header('Content-Type: application/octet-stream');
		}

		header('Content-disposition: attachment; filename=' . basename($file));
		header('Expires: 0');
        header('Cache-Control: no-cache');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
		readfile($file);
		exit();

	}
}
