<?php

class ControllerInventoryImport extends Controller{

	private $data 		= array();
	private $error 		= array();

	private $commission 	= DEFAULT_SELLER_COMMISSION;
	private $tax_class_id 	= 9;
	private $seller_tax 	= 5.5;
	private $seller_id  	= '';
	private $category_id 	= '';
	private $parent_cat 	= '';
	private $image_folder 	= '';
	private $csv_name 		= '';
	private $image_path 	= '';
	private $csv_path 		= '';
	private $error_flag 	= 2;
	private $seller_code    = '';

    private $weight_allowed_min = 0.1;
    private $weight_allowed_max = 2;

    // Variables to be used in case of Custom API import
    private $_api_link;
	private $_seller_tax_api;
	private $_commission_api;
	private $_tax_class_id_api;
	private $_seller_id_api;
	private $_seller_code_api;
    private $_image_path_api;
    private $_seller_exclusive_status = '';
    
    //Default value for product_name_prefix
    private $_product_name_prefix       = '';

	private $_product_ids = array();

	public function index() {

        // 001_DL (RGL Fashions)
        if ( !empty($this->request->get['custom_api']) && strtolower(trim($this->request->get['custom_api'])) == '001_dl' ) {

            $this->_api_link            = 'http://rfpl.sugarkane.in/channelapi/getCatalogue/wholesalebox';
            $this->_seller_tax_api      = 0;
            $this->_commission_api      = 11.11;
            $this->_tax_class_id_api    = 9;
            $this->_seller_id_api       = 17631;
            $this->seller_id            = 17631; // used by dbUpload function
            $this->_seller_code_api     = '001_DL';

            if(NGINX_ENABLED == 1){

                $this->_image_path_api = 'catalog/001_DL';
            }else{

                $this->_image_path_api = DIR_IMAGE . 'catalog/001_DL';
			}

            // Import into DB - this code assumes that images have already been downloaded and kept in the above image path folder
            $this->import001DLApi();
            

        } else {

            if(NGINX_ENABLED == 1){
                $this->image_path = 'catalog/';
            }
            else{
                $this->image_path = DIR_IMAGE . 'catalog/';
			}
            $this->getForm();
        }

	}

	protected function validateForm() {
		$this->load->language('inventory/import');
		
		if (!$this->user->hasPermission('modify', 'inventory/import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['submit_seller']) {
			$this->error['import_seller'] = $this->language->get('error_seller');
		}		
		if (!$this->request->post['submit_category']) {
			$this->error['import_category'] = $this->language->get('error_category');
		}
		if (!defined('DEFAULT_SELLER_COMMISSION')) {
          $this->error['import_commission'] = $this->language->get('error_commission');
        }
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		if($this->error && !isset($this->error['image'])) {
			$this->error['image'] = $this->language->get('image_warning');
		}
		
		if ($this->request->post['submit_seller'] && !isset($this->request->post['without_image'])) {

			$this->seller_code = $this->db->query("SELECT nickname 
                                      FROM oc_ms_seller 
                                      WHERE seller_id = '" . (int)$this->request->post['submit_seller'] . "'")->row['nickname'];
            if(NGINX_ENABLED == 1){
                if ($_SERVER['HTTPS']) {
                    $static_content_url =  STATIC_CONTENT_URL_SSL;
                } else {
                    $static_content_url =  STATIC_CONTENT_URL ;
                }
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false
                    )
                );
                $cdn_url = $static_content_url.'filemanager/checkDirectory.php?directory='.$this->image_path.$this->seller_code;
                $dir_data = file_get_contents($cdn_url, false, stream_context_create($arrContextOptions));
                $dir_data = json_decode($dir_data,true);
                if($dir_data['status'] == false){
                    $this->error['import_image'] = sprintf($this->language->get('error_image'),
                        $this->image_path.$this->seller_code);
				}
            }
            else{
                if (!is_dir($this->image_path.$this->seller_code)) {
                    $this->error['import_image'] = sprintf($this->language->get('error_image'),
                        $this->image_path.$this->seller_code);
                }
            }

		}
		
		return !$this->error;
	}
	/* Method to read data from API of 001_DL and import into DB
     * Method assumes that images have been already downloaded and processed
     * and kept in the specific folder
     * @author Divya Porwal
	 * Date : 8.March.2017
     */
	public function import001DLApi() {

        // fetch JSON from API
        $json_data = $this->getUrlContent($this->_api_link);

        // convert JSON to our product import array format
		$input_product = $this->read001DLJson($json_data);

		//insert into database
         $this->dbUpload($input_product);
	}
	//This method reads json data and converts it to the format which is readable
	//input : json data $json_data
	//output : array $final_arr
    public function read001DLJson($json_data) {

        //code added by Nilesh as per new requirement of seller exclusive
        if(!empty($this->seller_id)) {
            $selector = array('select'=> array('exclusive'));
            $this->_seller_exclusive_status = (string) SellerInfo::getSellerInfo($this->db, $this->seller_id, $selector)[$this->seller_id]['exclusive'];
        }
		$seller_tax_factor = 1.0 + ( (float)$this->_seller_tax_api/ 100.0 );
		$commission_factor = 1.0 + ( (float)$this->_commission_api / 100.0 );

		//hardcoding the category id for specific categories
		$product_category_mapping = array(
										'Bottom' => '75',
										'Churidar' => '86',
										'Capris' => '110',
										'Dress' => '91',
										'Jeggings' => '77',
										'Jacket' => '75',
										'Jumpsuit' => '91',
										'Top' => '72',
										'Palazzo Pant' => '65',
										'Kurti' => '61',
										'Shirt' => '72',
										'Tee' => '157',
										'Tunic' => '72',
										'Polo' => '157',
										'Shrug' => '64',
										'Pant' => '78',
										'Skirts' => '68',
										 'Play Suit' => '91',
										 'Bodycon' => '91',
										'Cardigan' => '75',
										'Shirt Dress' => '91',
										'Printed Shirt' => '72',
										'Skater Dress' => '91',
										'Shorts' => '127',
										'Culottes' => '68',
										'Jodhpuri Pants' => '78',
										'Solid Top' => '72',
										'Printed Harem Pants' => '83',
										'Printed Kurti' => '61',
										'Striped Top' => '72',
										'Printed Culottes' => '68',
										'Printed Shorts' => '127',
										'Solid Romper' => '91',
										'Printed Romper' => '91',
										'Printed Jumpsuit' => '91',
										'Solid Shirt' => '72',
										'Printed Top' => '72',
										'Printed Shrug' => '64',
										'Shift Dress' => '91',
										'Denim Shirt' => '72',
										'Crop Top' => '72',
										'Denim Culottes' => '68',
										 'Midi Dress' => '91',
										'Striped Jumpsuit' => '91',
										'Check Shirt' => '72',
										'Solid Kurti' => '61',
										'Denim Top' => '72',
										'Printed Jacket' => '75',
										'Maxi Dress' => '91',
										'Peplum Jacket' => '75',
										 'Solid Jacket' => '75',
										'Embroidered Kurti' => '61',
										'Printed Straight Pants' => '78',
										'Straight Pants' => '78',
										 'A-line Kurti' => '61',
                                         'Mufflers' => '84',
                                         'Swing Dress' => '91',
                                         'Printed Scarf' => '64',
                                         'Printed Mufflers' => '84',
                                         'Printed Tunic' => '72',
                                         'Embroidered Top' => '72',
                                         'Printed Maxi Top' => '72',
                                         'Solid Mufflers' => '84',
                                         'A Line Dress' => '91',
                                         'Blouson Dress' => '91',
                                        );

                                         //'Caps' => '1000',
                                         //'Gloves' => '1002',
                                         // 'Solid Beanie' => '1005',
                                         //'Cap' => '1007',

        $json_array = json_decode($json_data, true);

        $all_skus = array_column($json_array, 'styleCode');
        $unique_skus = array_unique($all_skus);
        $final_product_input = array();
        $error_flag_api = false;
        // Preparing product input array based on Unique SKU codes
        foreach ($unique_skus as $key => $sku) {

            $final_product_input[$sku] = array();

            // Determining product category as per subCategory from API and corresponding hardcoded map
            $final_product_input[$sku]['product_category'] = 0;
            $parent_category_id = 0;
            if(array_key_exists($json_array[$key]['subCategory'], $product_category_mapping)){
                $final_product_input[$sku]['product_category']  = array($product_category_mapping[$json_array[$key]['subCategory']]);    
                $parent_category_id = $this->getParentCategory($product_category_mapping[$json_array[$key]['subCategory']]);
                if ( !empty($parent_category_id) ) {
                    $final_product_input[$sku]['product_category'][] = $parent_category_id;
                }
            }

            $mrp = (float)str_replace(",","",$json_array[$key]['mrp']);
            $transfer_price = (float)str_replace(",","",$json_array[$key]['TP']);

            $name = trim($json_array[$key]['Brand']." Brand (MRP: Rs " . $mrp . ") " .
                         $json_array[$key]['productName']);

            $final_product_input[$sku]['row'] = $key;
            $final_product_input[$sku]['model']	= strtoupper(trim(preg_replace('/[^\da-z]/i','',$this->_seller_code_api) .
                                                                  substr(md5(microtime()),mt_rand(0,23),8)));
            $final_product_input[$sku]['hsn_code']      = substr($json_array[$key]['HSN'], 2);
            $final_product_input[$sku]['name']          = $json_array[$key]['productName'];
            $final_product_input[$sku]['tax_class_id']  = (int)($this->_tax_class_id_api);
            $final_product_input[$sku]['seller_tax']    = (float)($this->_seller_tax_api);
            $final_product_input[$sku]['seller_id']     = (int)($this->_seller_id_api);
            $final_product_input[$sku]['commission']    = (float)($this->_commission_api);
            $final_product_input[$sku]['price'] 	    =  $transfer_price;
            $final_product_input[$sku]['selling_price'] = ceil($commission_factor * $transfer_price / $seller_tax_factor);
            $final_product_input[$sku]['mrp']           = (float)$mrp;
            $final_product_input[$sku]['keyword'] 	    =  $this->readSeoUrl($name, $final_product_input[$sku]['model']);
            $final_product_input[$sku]['length'] 	    = '';
            $final_product_input[$sku]['width'] 		= '';
            $final_product_input[$sku]['height'] 		= '';
            $final_product_input[$sku]['length_class_id'] 	= '';
            $final_product_input[$sku]['manufacturer_id'] 	= '';
            $final_product_input[$sku]['sku']           	= trim($sku);
            $final_product_input[$sku]['manufacturer'] 		= '';
            $final_product_input[$sku]['points'] 			= '';
            $final_product_input[$sku]['location'] 			= '';
            $final_product_input[$sku]['date_available'] 	= '';
            $final_product_input[$sku]['is_single'] 	    = 0; // Currently no singles are listed through this mode
            $final_product_input[$sku]['quantity'] 			= 0;
            $final_product_input[$sku]['weight'] 			= (float)trim(str_replace(",","",$json_array[$key]['Weight']))/1000;
            $final_product_input[$sku]['expected_dispatch_date'] = '0000-00-00';
            $final_product_input[$sku]['minimum'] 			= 1;
            $final_product_input[$sku]['status'] 			= 1;
            $final_product_input[$sku]['shipping'] 			= 1;
            $final_product_input[$sku]['sort_order'] 		= 999;
            $final_product_input[$sku]['subtract']			= 1;
            $final_product_input[$sku]['weight_class_id']	= 1;
            $final_product_input[$sku]['stock_status_id']	= 7;
            $final_product_input[$sku]['product_status']	= 1;
            $final_product_input[$sku]['product_approve']	= 1;
            $final_product_input[$sku]['product_store']		= array(0,2);
            $final_product_input[$sku]['product_description'][1]['description'] = $json_array[$key]['Description'] . ". ";
            $final_product_input[$sku]['product_description'][1]['tag'] = implode(",", explode(" ", $name)).", wholesale";
            $final_product_input[$sku]['product_description'][1]['meta_keyword'] = implode(",", explode(" ", $name)).", wholesale";
            $final_product_input[$sku]['product_description'][1]['meta_title'] =  $name. " " . $final_product_input[$sku]['model'] . " - Wholesale";
            $final_product_input[$sku]['product_description'][1]['meta_description'] = $name. " " . $final_product_input[$sku]['model'] . " - Wholesale";
            $final_product_input[$sku]['product_description'][1]['name'] = $name;
            
            if(!empty($this->_seller_exclusive_status)) {
                $final_product_input[$sku]['exclusive'] = $this->_seller_exclusive_status;
            }
            
            // Creating product description from various other descriptors
            if ( !empty($json_array[$key]['season']) ) {
                $final_product_input[$sku]['product_description'][1]['description'] .= "Season: " . $json_array[$key]['season'] . ". ";
            }

            if ( !empty($json_array[$key]['base_material']) ) {
                $final_product_input[$sku]['product_description'][1]['description'] .= "Base Material: " . $json_array[$key]['base_material'] . ". ";
            }

            if ( !empty($json_array[$key]['base_color']) ) {
                $final_product_input[$sku]['product_description'][1]['description'] .= "Base Color: " . $json_array[$key]['base_color'] . ". ";
            }

            if ( !empty($json_array[$key]['FabricType']) ) {
                $final_product_input[$sku]['product_description'][1]['description'] .= "Fabric Type: " . $json_array[$key]['FabricType'] . ". ";
            }

            // Now determining unique number of sizes for this styleCode
            // Get all the keys for this styleCode - corresponding to different sizes
            $keys = array_keys($all_skus, $sku);
            $piece_in_set = count($keys);
            $sizes_in_set = array();
            foreach ($keys as $k) {
                $sizes_in_set[] = $json_array[$k]['size'];
            }

            $final_product_input[$sku]['product_description'][1]['set_description'] = '1 Set = Total ' . $piece_in_set .
                                                                                      ' pieces; 1 each of sizes: ' .
                                                                                      implode(', ', $sizes_in_set);
            $final_product_input[$sku]['piece_in_set'] = $piece_in_set;
            $final_product_input[$sku]['price_per_set'] = $transfer_price * $piece_in_set;

            // Reading images - rgl-images.php is code used in advance to download all the images at once
            $images	= $this->readImages($this->_image_path_api, $sku);

            if (!empty($images)) {
                foreach ($images as $key_image => $value_image) {
                    if(NGINX_ENABLED == 1){
                        $final_product_input[$sku]['product_image'][$key_image]['image'] 	  = $value_image;
                    }
                    else{
                        $final_product_input[$sku]['product_image'][$key_image]['image'] 	  = str_replace('/var/www/html/image/','',$value_image);
					}
                    $final_product_input[$sku]['product_image'][$key_image]['sort_order'] = $key_image+1;
                }
            }

            $final_product_input[$sku]['error']             = $this->validateData($final_product_input[$sku], 1);
            if($final_product_input[$sku]['error']){
                $error_flag_api = true;
            }            
        }

        if($error_flag_api){
            $file_name = DIR_DOWNLOAD .'inventory_import_api_error.csv';
            $fp = fopen($file_name, 'w');
            
            $data = array('S.No.', 
                          'Row No.', 
                          'Sku Code', 
                          'Item', 
                          'Error');
            fputcsv($fp, $data);
            if( !empty($final_product_input) ) {
                $i = 1;
                foreach ($final_product_input as $key => $sub_value) {
                    $s_no = $i;
                    $row = $sub_value['row'];
                    $sku = $sub_value['sku'];
                    foreach($sub_value['error'] as $error_item_key => $errors){
                        $item = $error_item_key;
                        $errors = $errors;

                        $data = array($s_no, 
                                      $row,
                                      $sku, 
                                      $item, 
                                      $errors);
                        fputcsv($fp, $data);

                    }
                    $i++;
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
        } else{
            return $final_product_input;    
        }		
	}


	//getting and reading contents from API
	//storing it in array $result
	//Author : Divya Porwal
	//Date : 7.March.2017
	public function getUrlContent($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if(!empty($post)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}


	public function getForm(){		
		
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('inventory/import', $data);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() ) {
			
			$this->seller_id 				= trim($this->request->post['submit_seller']);
			$this->category_id				= trim($this->request->post['submit_category']);
			$sellerInfo                     = new SellerInfo();
            $this->commission               = DEFAULT_SELLER_COMMISSION;
			
            $sql = "
                    SELECT 
                        nickname, product_name_prefix, prefix_mode
                    FROM 
                        oc_ms_seller 
                    WHERE 
                        seller_id = '" . (int)$this->seller_id . "'
                    ";
            $seller_result = $this->db->query($sql);

			$seller_details    	= $seller_result->row;
            $this->seller_code  = $seller_details['nickname'];

            //Set Product name prefix only if seller's product prefix mode is active
            if(!empty($seller_details['prefix_mode']) && !empty($seller_details['product_name_prefix']) ){
                $this->_product_name_prefix   = $seller_details['product_name_prefix'];
            }
            
            $this->image_folder				= $this->image_path.$this->seller_code;                                      
			$this->csv_data = $this->request->files['submit_csv'];
			
			if($this->error_flag == 1 || $this->error_flag == 2) {
				
                $csvData = $this->getData();

                //prd($csvData);
                
                if (!is_array($csvData)) {
                   
                    if ($csvData=='file_format_error') {
                        
                        $this->error_flag = 3;

                        $this->error['file_format'] = 'CSV File column value should be in this secunce (Title,Set Description, Description, SKU Code, HSN Code, Transfer Price, Pieces in Set, Available Sets, Weight of a Piece)';
                        
                    }
                }else{
                    
                    $data['csv_data'] = $csvData;
                }

			}

			if($this->error_flag == 0){

				$data['csv_data'] = $this->dbUpload($this->getData());

                /* set product rating after csv uploaded*/
                $this->model_catalog_product->setProductRatingWhenProductAssignToSeller($this->seller_id, $this->_product_ids);
                /****/
                
				/*
				* set the data for which all checkbox is selected
				* 1 for checked 0 for unchecked
				*/
				$checkboxs_status = array();
				if (isset($this->request->post['disabled_status']) && $this->request->post['disabled_status']==1) {
					$checkboxs_status['disabled_status'] = 1;
				}else{
					$checkboxs_status['disabled_status'] = 0;
				}
				if (isset($this->request->post['without_image']) && $this->request->post['without_image']==1) {
					$checkboxs_status['without_image'] = 1;
				}else{
					$checkboxs_status['without_image'] = 0;
				}
				if (isset($this->request->post['wsb_code']) && $this->request->post['wsb_code']==1) {
					$checkboxs_status['wsb_code'] = 1;
				}else{
					$checkboxs_status['wsb_code'] = 0;
				}
				$this->logEntry($data['csv_data'],$this->csv_data['name'],$checkboxs_status);				
				$target = DIR_UPLOAD.basename($this->csv_data['name']);
				unlink($target);
			}			
		}


		$this->load->model('catalog/category');
		$this->load->model('localisation/tax_class');

        $sql = "select seller_id, `nickname` as `ms.company`, `company` as `ms.nickname` 
                from oc_ms_seller where seller_status = 1 order by `nickname` asc";

		$data['sellers']					= $this->db->query($sql)->rows;
		$data['categories']					= $this->model_catalog_category->getCategories();
		$data['tax_classes']				= $this->model_localisation_tax_class->getTaxClasses();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/home')
		);

		if ((isset($this->request->post['submit_seller'])) && !empty($this->request->post['submit_seller'])) {
			$data['product_seller']			= $this->request->post['submit_seller'];
		}else{
			$data['product_seller']			= '';
		}
		if ((isset($this->request->post['submit_category'])) && !empty($this->request->post['submit_category'])) {
			$data['product_category']		= $this->request->post['submit_category'];
		}else{
			$data['product_category']		= '';
		}

		$this->document->setTitle($data['heading_title']);

		$data['form_action'] 				= 'index.php?route=inventory/import/index'.'&token=' . $this->session->data['token'];
		//$data['button_action'] 				= 'index.php?route=inventory/import/index'.'&token=' . $this->session->data['token'];


		$data['column_left'] 				= $this->load->controller('common/column_left');
		$data['column_right']				= $this->load->controller('common/column_right');
		$data['content_top'] 				= $this->load->controller('common/content_top');
		$data['content_bottom'] 			= $this->load->controller('common/content_bottom');
		$data['footer'] 					= $this->load->controller('common/footer');
		$data['header'] 					= $this->load->controller('common/header');		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['import_seller'])) {
			$data['error_seller'] = $this->error['import_seller'];
		} else {
			$data['error_seller'] = '';
		}
		
		if (isset($this->error['import_commission'])) {
			$data['error_commission'] = $this->error['error_commission'];
		} else {
			$data['error_commission'] = '';
		}

		if (isset($this->error['import_category'])) {
			$data['error_category'] = $this->error['import_category'];
		} else {
			$data['error_category'] = '';
		}

		if (isset($this->error['import_commission'])) {
			$data['error_commission'] = $this->error['import_commission'];
		} else {
			$data['error_commission'] = '';
		}
		if (isset($this->error['import_image'])) {
			$data['error_image'] = $this->error['import_image'];
		} else {
			$data['error_image'] = '';
		}
        
		if (isset($this->error['import_csv'])) {
			$data['error_csv'] = $this->error['import_csv'];
		} else {
			$data['error_csv'] = '';
		}
        if (isset($this->error['file_format'])) {
            $data['file_format'] = $this->error['file_format'];
        } else {
            $data['file_format'] = '';
        }

		$data['error_flag'] = $this->error_flag;
        
		$this->response->setOutput(($this->load->view('inventory/import.tpl', $data)));
	}

	public function getData(){
		
        //code added by Nilesh as new requirement of seller exclusive
        if(!empty($this->seller_id)) {
            $selector = array('select'=> array('exclusive'));
            $this->_seller_exclusive_status = (string) SellerInfo::getSellerInfo($this->db, $this->seller_id, $selector)[$this->seller_id]['exclusive'];
        }
        
        //$sellerInfo   = new SellerInfo();
            
		$image_path   = $this->image_path.$this->seller_code;

		//move_uploaded_file(file,newloc)
		$source = $this->csv_data['tmp_name'];
		$target = DIR_UPLOAD.basename($this->csv_data['name']);
		
		//getting tax object
        $tax = new Tax($this->registry);
		if(file_exists($target)) {
			$file = fopen($target,"r");

			while(! feof($file)) {
				$csv_data[] = fgetcsv($file);
            }
		   fclose($file);
		} else if(move_uploaded_file($source, $target)) {	
			exec("chmod -R 0777  $target");
			$file = fopen($target,"r");

			while(! feof($file)) {
				$csv_data[] = fgetcsv($file);
            }
		   fclose($file);
		}

		
        /*
        * check file titles on proper place
        */
        $fileFormatError = '';
        if (!empty($csv_data)) {
            if (
                trim(strtolower($csv_data[0][0]))!='title' || 
                trim(strtolower($csv_data[0][1]))!='set description' || 
                trim(strtolower($csv_data[0][2]))!='description' || 
                trim(strtolower($csv_data[0][3]))!='sku code' || 
                trim(strtolower($csv_data[0][4]))!='hsn code' || 
                trim(strtolower($csv_data[0][5]))!='transfer price' || 
                trim(strtolower($csv_data[0][6]))!='pieces in set' || 
                trim(strtolower($csv_data[0][7]))!='available sets' || 
                trim(strtolower($csv_data[0][8]))!='weight of a piece'
                ) {
                
                $fileFormatError = 'file_format_error';
               
            }
        }		
		$heading 	= array();
		$heading 	= array_shift($csv_data);
		$num 		= count($heading);
		$error_flag = 0;

		
        if ($fileFormatError=='') {
            
            foreach($csv_data as $key=>$value) {
                if(!empty($value)) {
                    
                    // check if we have MRP at 9th column (index = 8)
                    $mrp_in_sheet       = false;                    
                    $filter_start_index = 9;
                    /*
                    * if mrp set in CSV file
                    */
                    if ( trim(strtolower($heading[9])) == 'mrp' ) {
                        $mrp_in_sheet       = true;
                        $filter_start_index = 10;
                    }
                    
                    /*
                    * assuming that MRP and Store wont be at the same time
                    */
                    $store_in_sheet = false;
                    if (  !$mrp_in_sheet && trim(strtolower($heading[9])) == 'store' ) {
                        $store_in_sheet     = true;
                        $filter_start_index = 10;
                    }
                    /*
                    * if commission column is set
                    */
                    if ( trim(strtolower($heading[9])) == 'commission' ) {
                        
                        $filter_start_index = 11;
                    }
                    
                    $csv_import_data[$key]['row']           = $key+1;
                    $csv_import_data[$key]['name']          = trim($csv_data[$key][0]);
                    $csv_import_data[$key]['sku']           = trim($csv_data[$key][3]);
                    $csv_import_data[$key]['hsn_code']      = trim($csv_data[$key][4]);
                    $csv_import_data[$key]['price']         = trim($csv_data[$key][5]);
                    $csv_import_data[$key]['piece_in_set']  = trim($csv_data[$key][6]);
                    $csv_import_data[$key]['quantity']      = ( !empty($csv_data[$key][7]) and $csv_data[$key][7] > 0)  ? $csv_data[$key][7] : 0;
                    $csv_import_data[$key]['weight_per_piece']  = trim($csv_data[$key][8]);
                    $csv_import_data[$key]['weight']            = trim($csv_data[$key][8]);
                    $csv_import_data[$key]['price_per_set']     = trim(trim($csv_data[$key][6]) * trim($csv_data[$key][5]));
                    $csv_import_data[$key]['mrp']     = 0.0;
                    
                    if ( $mrp_in_sheet ) {
                        $csv_import_data[$key]['mrp'] = ( !empty(trim($csv_data[$key][9])) and (trim($csv_data[$key][9]) > 0.0) ) ? trim($csv_data[$key][9]) : 0.0;
                    }
                        
                    $csv_import_data[$key]['store_sales']  = 'NO';

                    if ( $store_in_sheet ) {
                        $csv_import_data[$key]['store_sales'] = ( !empty(trim($data_csv[9])) ) ? strtoupper(trim($data_csv[9])) : 'NO';
                    }
                    
                    $csv_import_data[$key]['expected_dispatch_date'] = '0000-00-00';
         
                    for ($index=$filter_start_index; $index <= $num-1; $index++) { 
                       if(trim($csv_data[$key][$index]) != '')
                       {         
                        $csv_import_data[$key]['product_filter'][]  = $this->readFilterId(trim($heading[$index]), trim($csv_data[$key][$index])); 
                        $csv_import_data[$key]['filters'][trim($heading[$index])] = trim($csv_data[$key][$index]);
                       }
                    }
                    
                    $csv_import_data[$key]['minimum']           = 1;

                    if ((isset($this->request->post['disabled_status']) && $this->request->post['disabled_status']==1) || (isset($this->request->post['without_image']) && $this->request->post['without_image']==1)) {
                        $csv_import_data[$key]['status']            = 0;
                    }else{
                        $csv_import_data[$key]['status']            = 1;
                    }
                    
                    $csv_import_data[$key]['shipping']          = 1;
                    $csv_import_data[$key]['sort_order']        = 999;
                    $csv_import_data[$key]['subtract']          = 1;
                    $csv_import_data[$key]['weight_class_id']   = 1;
                    $csv_import_data[$key]['stock_status_id']   = 7;
                    $csv_import_data[$key]['product_status']    = 1; 
                    $csv_import_data[$key]['product_approve']   = 1;
                    $csv_import_data[$key]['product_store']     = array(0,2);
                    $image_array                                = array();
                    $withoutImage                               = '';
                    $csv_import_data[$key]['product_category']  = array($this->category_id, $this->getParentCategory($this->category_id));

                    if (!isset($this->request->post['without_image'])) {
                        $image_array                                = $this->readImages($image_path, $csv_import_data[$key]['sku']);
                    }else{
                        $withoutImage = 1;
                    }

                    $csv_import_data[$key]['validation_images'] = $image_array;             
                    $csv_images                                 = $image_array;

                    if (!empty($csv_images)) {
                        foreach ($csv_images as $key_image => $value_image) {
                            if(NGINX_ENABLED == 1){
                                $csv_import_data[$key]['product_image'][$key_image]['image']            = $value_image;
                            }else{
                                $csv_import_data[$key]['product_image'][$key_image]['image']            = str_replace(DIR_IMAGE,'',$value_image);
                            }
                            $csv_import_data[$key]['product_image'][$key_image]['sort_order']       = $key_image+1;
                        }
                    }
                        
                    $csv_import_data[$key]['date_out_of_stock'] = '0000-00-00';
                    $csv_import_data[$key]['date_modified']     = '0000-00-00';
                    $csv_import_data[$key]['length']            = '';
                    $csv_import_data[$key]['width']             = '';
                    $csv_import_data[$key]['height']            = '';
                    $csv_import_data[$key]['length_class_id']   = '';
                    $csv_import_data[$key]['manufacturer_id']   = '';
                    $csv_import_data[$key]['manufacturer']      = '';
                    $csv_import_data[$key]['points']            = '';
                    $csv_import_data[$key]['location']          = '';
                    $csv_import_data[$key]['date_available']    = '0000-00-00';
                    $csv_import_data[$key]['is_single']         = 0; // Currently no singles are listed through this mode                   
                    
                    $csv_import_data[$key]['commission']        = trim($this->commission);

                    $csv_import_data[$key]['seller_id']         = trim($this->seller_id); 
                    $csv_import_data[$key]['seller_code']       = trim($this->seller_code);

                    if (isset($this->request->post['do_not_prefix_seller_code']) && $this->request->post['do_not_prefix_seller_code']==1 && isset($this->request->post['wsb_code']) && $this->request->post['wsb_code']==1) {

                        $csv_import_data[$key]['model']             = strtoupper(trim(trim($csv_data[$key][3])));    
                    
                    }else if (isset($this->request->post['wsb_code']) && $this->request->post['wsb_code']==1 && !isset($this->request->post['do_not_prefix_seller_code'])) {

                        $csv_import_data[$key]['model']             = strtoupper(trim($this->seller_code.'_'.trim($csv_data[$key][3])));    
                    
                    }else if (isset($this->request->post['do_not_prefix_seller_code']) && $this->request->post['do_not_prefix_seller_code']==1 && !isset($this->request->post['wsb_code'])) {

                        $csv_import_data[$key]['model']             = strtoupper(trim(preg_replace('/[^\da-z]/i',
                                                                                                       '',
                                                                                                       trim($csv_data[$key][3])) 
                                                                                                       . 
                                                                                      substr(md5(microtime()),mt_rand(0,23),8)));    
                    
                    }else{
                        $csv_import_data[$key]['model']             = strtoupper(trim(preg_replace('/[^\da-z]/i',
                                                                                                       '',
                                                                                                       $this->seller_code) 
                                                                                                       . 
                                                                                      substr(md5(microtime()),mt_rand(0,23),8))); 
                    }
                    
                    $csv_import_data[$key]['validation_images'] = $this->readImages($image_path, $csv_import_data[$key]['sku']);        
                    $csv_images = $csv_import_data[$key]['validation_images'];
                    $csv_import_data[$key]['product_description'] = array('1' => array(
                                'name'                  => trim($csv_data[$key][0]),
                                'set_description'       => trim($csv_data[$key][1]),
                                'description'           => trim($csv_data[$key][2]),
                                'tag'                   => implode(",", explode(" ", trim($csv_data[$key][0]))).", wholesale",
                                'meta_keyword'          => implode(",", explode(" ", trim($csv_data[$key][0]))).", wholesale",
                                'meta_title'            => trim($csv_data[$key][0]). " " . $csv_import_data[$key]['model'] . " - Wholesale" ,
                                'meta_description'      => trim($csv_data[$key][0]). " " . $csv_import_data[$key]['model'] . " - Wholesale" ,
                    ));

                    $csv_import_data[$key]['keyword']           = $this->readSeoUrl(trim($csv_data[$key][0]), $csv_import_data[$key]['model']);

                    //if (!$errorInCommission) {
                        $csv_import_data[$key]['error']             = $this->validateData($csv_import_data[$key], $withoutImage);
                    //}
                    
                    if(!empty($csv_import_data[$key]['error'])) {
                        $error_flag = 1;
                    }
                    if (isset($this->error['product'])) {
                        unset($this->error['product']);
                    }
                    if(!empty($this->_seller_exclusive_status)) {
                        $csv_import_data[$key]['exclusive'] = $this->_seller_exclusive_status;
                    }
                }
            }
            
            
            $this->error_flag = $error_flag == 1 ? 1:0;
            if($this->error_flag != 0) {
                unlink($target);
            }

            return isset($csv_import_data)? $csv_import_data : '';
        }else{
            unlink($target);
          return $fileFormatError;  
        }
		 
	}


	protected function validateData($product = array(), $withoutImage ='' ){
		
        $this->load->model('catalog/filter');
		$this->load->language('inventory/import');
		
        $filters                = $this->model_catalog_filter->getFilters();
		$filter_groups          = $this->model_catalog_filter->getFilterGroups();
		$filter_names           = array_column($filters, 'name');
		$filter_group_names     = array_column($filter_groups, 'name');

		$inventory_validation   = new InventoryValidation($this, $product['seller_id'], $this->category_id);

			if(!empty($product['sku'])) {
		
        		if(!empty($inventory_validation->getProductIdBySkuAndSeller($product['sku']))) {
					$this->error['product']['sku_code'] = $this->language->get('error_duplicate_sku');
				}
			}
			if(!empty($this->category_id)) {
				$min_weight =  $inventory_validation->getWeightRange()['min_weight'];
				$max_weight =  $inventory_validation->getWeightRange()['max_weight'];
				if((float)$product['weight'] < (float)($min_weight)/1000 || (float)$product['weight'] > (float)($max_weight)/1000) {
					$this->error['product']['weight'] = $this->language->get('error_incorrect_weight');
				}
			}
			if (empty($product['product_description'][1]['name'])) {
				$this->error['product']['name'] 				= $this->language->get('error_empty_product_name');
			}
			if (empty($product['product_description'][1]['set_description'])) {
				$this->error['product']['set_description'] 		= $this->language->get('error_empty_product_set_description');
			}
			if (empty($product['sku'])) {
				$this->error['product']['sku'] 					= $this->language->get('error_empty_product_sku');
			}
			if (preg_match("[^a-zA-Z0-9- \n]", $product['sku'])) {
				$this->error['product']['sku_data'] 			= $this->language->get('error_data_product_sku');
			}
            $hsn_code = trim($product['hsn_code']);
            if (!( (strlen($hsn_code) == 4 || strlen($hsn_code) == 6 || strlen($hsn_code) == 8) 
                && preg_match("/[0-9]{4,}/", $hsn_code))) {
				$this->error['product']['hsn_code'] 			= 'Invalid HSN Code';
			}
			if (empty($product['price'])) {
				$this->error['product']['price'] 				= $this->language->get('error_empty_product_price');
			}
			if (empty($product['piece_in_set'])) {
				$this->error['product']['piece_in_set'] 		= $this->language->get('error_empty_piece_in_set');
			}
			if (!is_numeric($product['piece_in_set'])) {
				$this->error['product']['piece_in_set_data'] 	= $this->language->get('error_data_piece_in_set');
			}
			if (!is_numeric($product['quantity'])) {
				$this->error['product']['quantity_data'] 		= $this->language->get('error_data_quantity');
			}
			if (empty($product['minimum'])) {
				$this->error['product']['minimum'] 				= $this->language->get('error_empty_minimum');
			}
			if (!is_numeric($product['minimum'])) {
				$this->error['product']['minimum_data'] 		= $this->language->get('error_data_minimum');
			}
			if (empty($product['weight'])) {
				$this->error['product']['weight'] 				= $this->language->get('error_empty_weight');
			}
			if (empty($product['validation_images']) && !$withoutImage) {
				$this->error['product']['image'] 				= $this->language->get('error_empty_image');
			}

            if ( isset($product['filters']) ) {
                foreach ($product['filters'] as $filter_key => $filter_value) {
                    if (empty($filter_key)) {
                        $this->error['product']['empty_field'] 		= $this->language->get('error_empty_field');
                    }
                    if (!empty($filter_value)) {

                        if (!in_array( strtolower($filter_key), array_map("strtolower", $filter_group_names) )) { // case insensitive matching
                            $this->error['product'][$filter_key] 	= sprintf($this->language->get('error_data_filter_field'),$filter_key);
                        }
                        if (!in_array(strtolower($filter_value), array_map("strtolower", $filter_names) )) { // case insensitive matching
                            $this->error['product'][$filter_value] 	= sprintf($this->language->get('error_data_filter_value'),$filter_value);
                        }
                    }
                }
            }
            //pr($this->error);
	    return isset($this->error['product'])? $this->error['product'] : '';
	}


	public function readImages($directory, $sku=''){

        if(NGINX_ENABLED == 1){
            if ($_SERVER['HTTPS']) {
                $static_content_url =  STATIC_CONTENT_URL_SSL;
            } else {
                $static_content_url =  STATIC_CONTENT_URL ;
            }
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false
                )
            );
            $cdn_url = $static_content_url.'filemanager/readImages.php?directory='.$directory.'&sku='.$sku;
            $image_files_paths = file_get_contents($cdn_url, false, stream_context_create($arrContextOptions));
            
            $image_files_paths = json_decode($image_files_paths,true);
            return $image_files_paths;
        }


		if (is_dir($directory) &&!empty($sku)) {
			$images = scandir($directory);
		}else{
			return 0;
		}

		$image_files_paths = array();

		if (isset($images)) {
			if (in_array($sku, $images)) {
				$dir = $directory . '/'.$sku;
				if (is_dir($dir)) {
					foreach (scandir($dir) as $key => $value) {
						if (in_array($value, array(".", "..", ".DS_Store", "Thumbs.db"))) {
							continue;
						}
						else{
							$image_files_paths[] = $dir.'/'.$value;
						}
					 }
				}
			return $image_files_paths;
			}
			else{
				foreach ($images as $key => $value) {
					if (substr_compare($value, $sku, 0, strlen($sku)) == 0) {
						$image_files_paths[] = $directory.'/'.$value;
					}
				}
			return $image_files_paths;
			}
		}
		else
			return 0;

	}
	public function dbUpload($data = array()){
      
        $this->load->model('catalog/product');
		
        $temp_data                  = $data;		
		$inserted_data              = array();
        $product_id_array           = array();
        $product_id_temp_array      = array();
        $i                          = 0;

		foreach ($temp_data as $product) {

			if (!isset($product['inserted']) && empty($product['inserted'])) {

				if (empty($product['error'])) {

                    if(!empty($this->_product_name_prefix )){
                        $language_id  = 1;
                        $product_name = $product['product_description'][$language_id]['name'] ?? '';
                        $product_name = $this->_product_name_prefix. ' - ' . $product_name;
                        $product['product_description'][$language_id]['name'] = $product_name;
                    }

					if ($product_id = $this->model_catalog_product->addProduct($product)) {
                    /*
                    * using substr we will get a unique product array
                    * it will remov all store's name product only one will store in array
                    */    
                    $sku_temp                           = '';    
                    $sku_temp                           = substr($product['sku'],6);

                    $product_id_temp_array[$sku_temp]['product_id']      = $product_id;
                    $product_id_temp_array[$sku_temp]['seller_id']       = $this->seller_id; 
                    
                    if (isset($data[0]['product_category'][0])) {
                        $product_id_temp_array[$sku_temp]['category_id']   = $data[0]['product_category'][0];
                    } else{
                        $product_id_temp_array[$sku_temp]['category_id']   = '';  
                    }
					
                    $product['inserted']                = 1;

                    $mssql = "INSERT INTO ".DB_PREFIX."ms_product 
                              SET
                                product_id = '".(int)$product_id."',
                                seller_id  = '".(int)$this->seller_id."'
                            ";
						$this->db->query($mssql);

						$solr = new SolrProduct($this);

						//check if Seller SOR selling status is enabled
						$selector = array('select'=> array('sor_enabled'));
                        if( !empty(SellerInfo::getSellerInfo($this->db, $this->seller_id, $selector)[$this->seller_id]['sor_enabled']) ) {
							$arr_product_ids[] = $product_id;
							$solr->copyProductIdsToSor($arr_product_ids,'',$this->seller_id);
						}
                        $i++;

                        $this->_product_ids[] = $product_id;
					}
				}else{
					$product['inserted'] = 0;
				}
			}

		$inserted_data[] = $product;

		}
        /*if (!empty($product_id_temp_array)) {
            $date_added = date('Y-m-d H:i:s');
            foreach ($product_id_temp_array as $key => $value) {

                $sql_notification = "INSERT INTO " . DB_PREFIX . "product_notification (
                                                `seller_id`,
                                                `category_id`,
                                                `product_id`, 
                                                `operation_type`,
                                                `date_added` 
                                                ) VALUES (
                                                '".(int)$value['seller_id']."', 
                                                '".(int)$value['category_id']."', 
                                                '".(int)$value['product_id']."',
                                                'NEW_PRODUCT',  
                                                '".$date_added."'   
                                                )";
                                               
                $this->db->query($sql_notification);
            }
            
        }*/
		return $inserted_data ;
	}

	public function readFilterId($heading, $csv_filter_name){

		$this->load->model('catalog/filter');

		 if (is_array($this->model_catalog_filter->getFilterId($heading, $csv_filter_name)) && !empty($this->model_catalog_filter->getFilterId($heading, $csv_filter_name))){
		 	return $this->model_catalog_filter->getFilterId($heading, $csv_filter_name)['filter_id'];
		 }
		 else{
		 	return '';
		 }
	}

	public function readSeoUrl($product_name, $product_model){

		$this->load->model('catalog/product');

	return $this->model_catalog_product->setSeoUrl($product_name, $product_model);
	}

	public function getParentCategory($category_id){

		$this->load->model('catalog/category');
		$var = $this->model_catalog_category->getCategory($category_id);
		return ($this->model_catalog_category->getCategory($category_id)['parent_id']);

	}
	public function logEntry($data = array(), $csvfile = null, $checkboxs_status = null){

		foreach ($data as $product) {
			$sql =
                    "INSERT INTO ".DB_PREFIX."inventory_import_log 
                      SET csv_row          = '".$this->db->escape($product['row'])."',
                          model            = '".$this->db->escape($product['model'])."',
                          price_per_set    = '".$this->db->escape($product['price_per_set'])."',
                          piece_in_set     = '".$this->db->escape($product['piece_in_set'])."',
                          weight_per_piece = '".$this->db->escape($product['weight_per_piece'])."',
                          inserted         = '".$this->db->escape($product['inserted'])."',
                          error            = '".$this->db->escape(serialize($product['error']))."',
                          date_added       = NOW() ,
                          csv_name         = '". $this->db->escape($csvfile)."',
                          checkboxs_status = '". $this->db->escape(serialize($checkboxs_status)) ."' ";					

			$this->db->query($sql);
		}

	}

}

?>
