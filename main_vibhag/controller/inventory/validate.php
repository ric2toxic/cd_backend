<?php

require_once(DIR_APPLICATION. 'controller/inventory/import.php');
/**
 * Author : Divya Porwal
 * Date : March,2017
 * */
 
class ControllerInventoryValidate extends Controller {
	
	public function index() {
		
		$this->image_path = DIR_IMAGE . 'catalog/';
		//$this->getForm();
		$this->getList();

	}

	public function getList() {
		
		// load All model
		$this->load->model('catalog/category');
		$this->load->model('localisation/tax_class');
		
		$this->load->model('catalog/product');
		
	//	print_r($this->request->get);
		//return;
		if (isset($this->request->get['filter_seller']) && !empty($this->request->get['filter_seller'])) {
			$filter_seller_id = $this->request->get['filter_seller'];
			
		} else {
			$filter_seller_id = $this->model_catalog_product->getSellerList();
			
		}

		if (isset($this->request->get['filter_category'])) {
			$filter_category_id = $this->request->get['filter_category'];
			
		} else {
			$tax_based_inventory = new TaxBasedInventory($this);
			$filter_category_id = $tax_based_inventory->getImportCategories('');
		
		}
	
		
		// 0 - pending for approval, 1 - rejected, 2 - approved, 3 - on hold
        if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = 0;
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
	
		$url = '';

		if (isset($this->request->get['filter_seller'])) {
			$url .= '&filter_seller=' . $this->request->get['filter_seller'];
		}

        if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		
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


	    $data = array(); // Initializing the data array to be passed on to template files
		// Autoloading the lanugage
		
		$this->load->autoLoadLanguage('inventory/import', $data);
		$this->document->setTitle($data['heading_title']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$filter_data = array(
			'filter_seller_id'   => $filter_seller_id,
			'filter_category_id' => $filter_category_id,
            'filter_status'      => $filter_status,
            'start'              => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'              => $this->config->get('config_limit_admin')		
		);		
		
		$data['token'] = $this->session->data['token'];
	
		$data['form_action']  = 'index.php?route=inventory/validate/index'.'&token=' . $this->session->data['token'];
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] 	= $this->load->controller('common/footer');
		$data['header'] 	= $this->load->controller('common/header');
		
		$data['filter_seller_id']     = $filter_seller_id;
        $data['filter_category_id']   = $filter_category_id;
		$data['filter_status']        = $filter_status;
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['trigger'] = 1;
		
		$results_total = count(json_decode($this->getFilterData($filter_data),true)['final_data']);	

		$pagination = new Pagination();
        $pagination->total = $results_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('inventory/validate', 'token=' . $this->session->data['token'] . $general_url . '&page={page}', 'SSL');
      
        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($results_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($results_total - $this->config->get('config_limit_admin'))) ? $results_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $results_total, ceil($results_total / $this->config->get('config_limit_admin')));
		
		$this->response->setOutput($this->load->view('inventory/validate.tpl', $data));
	}
	
	
	public function getFilterData($filter_data) {
		
		
		$new_data = array();
		$data_with_tax = array();
		$getinventorydata = new GetInventoryData($this);
		$output = $getinventorydata->getInventoryData($filter_data);
		
		$this->load->model('catalog/category');	
		
		$output_new = array();
		
		
		if(!empty($output)) {
			
			foreach($output as $key=>$value) {
				
				if(array_key_exists('import_csv_id',$value)) {
						
					$value['category_name'] = $this->model_catalog_category->getCategory($value['category_id'])['name'];
					$excel_sheet_tax = json_decode($value['import_data'],true)['Tax'];

					$get_inventory_data = new GetInventoryData($this,$value['category_id'] ,$value['seller_id'],'');
					//getting seller zone id
					$seller_info = SellerInfo::getSellerZoneIdAndPickupCityCode($this->db, $seller_id);

					$seller_zone_id  	= $seller_info['zone_id'];
					$pickup_city_code   = $seller_info['pickup_city_code'];
					
					$calculated_tax = $get_inventory_data->getTaxForSeller($excel_sheet_tax,$seller_zone_id, $pickup_city_code);
					
					$value['seller_tax'] = $calculated_tax[0];
					
					if($calculated_tax[1] != "Contact administrator") {
						
						$value['tax_class_id'] = $this->db->query("SELECT tax_class_id FROM " .DB_PREFIX. "tax_class WHERE title = '".$calculated_tax[1]. "'")->row['tax_class_id'];
					} else {
						
						$value['tax_class_id'] = $calculated_tax[1];
						$this->db->query("UPDATE " .DB_PREFIX. "inventory_import_product SET status = '3' WHERE import_product_id = ".$value['import_product_id']."");
					
					}
					
					$inventory_validation = new InventoryValidation($this, $value['seller_id'], $value['category_id']);
					
					$weight_range = $inventory_validation->getWeightRange();
					
					$value['min_weight'] = 	$weight_range['min_weight'];
					$value['max_weight'] = $weight_range['max_weight'];

					$naming_filters_ids = $this->db->query("SELECT naming_filters FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$value['category_id'] . "'")->row['naming_filters'];
						
					if(!empty($naming_filters_ids)) {
							
						$naming_filter_names = $this->db->query("SELECT name FROM " . DB_PREFIX . 
												"filter_group_description WHERE language_id = 1 AND filter_group_id IN (" . $naming_filters_ids . ")")->rows;
						
						$naming_filter_group_names = array();
							foreach($naming_filter_names as $filter_names) {
								array_push($naming_filter_group_names,$filter_names['name']);
							}
							
						} else {
							$naming_filter_group_names = '';
						}
						
					$value['naming_filters'] = $naming_filter_group_names;						
					$output_new[$value['import_csv_id']][$key] = $value;
				}
			}
			
			ksort($output_new,SORT_NUMERIC);
			
			foreach($output_new as $key=>$data) {
				
				$new_data[$key] = array_values($data);
			}
		}	
		
		return json_encode( 
					array(
						'final_data' => $new_data?$new_data:'',
						)
					);		
	}
	
	public function getForm(){
		
		// load All model
		$this->load->model('catalog/category');
		$this->load->model('localisation/tax_class');
		
		$this->load->model('catalog/product');
		
	//	print_r($this->request->get);
		//return;
		if (isset($this->request->get['filter_seller']) && !empty($this->request->get['filter_seller'])) {
			$filter_seller_id = $this->request->get['filter_seller'];
			
		} else {
			$filter_seller_id = $this->model_catalog_product->getSellerList();
			
		}

		if (isset($this->request->get['filter_category'])) {
			$filter_category_id = $this->request->get['filter_category'];
			
		} else {
		
			$tax_based_inventory = new TaxBasedInventory($this);
			$filter_category_id = $tax_based_inventory->getImportCategories('');
		}

		// 0 - pending for approval, 1 - rejected, 2 - approved, 3 - on hold
        if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = 0;
		}

	
		$url = '';

		if (isset($this->request->get['filter_seller'])) {
			$url .= '&filter_seller=' . $this->request->get['filter_seller'];
		}

        if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		
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


	    $data = array(); // Initializing the data array to be passed on to template files
		// Autoloading the lanugage
		$this->load->autoLoadLanguage('inventory/import', $data);
		$this->document->setTitle($data['heading_title']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$filter_data = array(
			'filter_seller_id'   => $filter_seller_id,
			'filter_category_id' => $filter_category_id,
            'filter_status'      => $filter_status,
		);
		
		
		$data['token'] = $this->session->data['token'];
	
		$data['form_action']  = 'index.php?route=inventory/validate/index'.'&token=' . $this->session->data['token'];
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] 	= $this->load->controller('common/footer');
		$data['header'] 	= $this->load->controller('common/header');
		
		$data['filter_seller_id']     = $filter_seller_id;
        $data['filter_category_id']   = $filter_category_id;
		$data['filter_status']        = $filter_status;
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

	
		$this->response->setOutput($this->getFilterData($filter_data));	
	} 
	

	
	//public function to get inventory data to show in tpl
	public function getInventoryData() {
		
		
		if(isset($this->request->get['category']) && isset($this->request->get['id_seller']) && isset($this->request->get['status'])) {
			
			
			
			$output_old = array();
			$data_with_tax = array();
			$new_data = array();
			
		
			if($this->request->get['category'] == "A") {
				$this->load->model('catalog/category');
				$categories= $this->model_catalog_category->getCategories();
			} else {
				$categories = $this->request->get['category'];
			}
			
			$getinventorydata = new GetInventoryData($this->db,$categories,$this->request->get['id_seller'], $this->request->get['status']);
			$output_old = $getinventorydata->inventoryMethod();
			
			$this->load->model('catalog/category');	
			$output = array();
			
			foreach($output_old as $key=>$value) {
				
				if(array_key_exists('import_csv_id',$value)) {
					
					$value['category_name'] = $this->model_catalog_category->getCategory($value['category_id'])['name'];
					unset($value['category_id']);
					$output[$value['import_csv_id']][$key] = $value;
				}
			}
			
			ksort($output,SORT_NUMERIC);
			
			
			foreach($output as $key=>$data) {
				$new_data[$key] = array_values($data);
			}
			
			$get_tax_details= new GetInventoryData($this->db, $categories,$this->request->get['id_seller'], $this->request->get['status']);
			$data_with_tax = $get_tax_details->getTaxDetails($this,$new_data);
			
		
			$this->response->setOutput(json_encode( 
											array(
												'final_data' => $new_data,
												'tax_data' => $data_with_tax,
											)
										));	
		} else {
			$this->response->setOutput(json_encode( 
											array(
												'final_data' => 'Invalid values',
											)
										));	
		}
		
	}
	
	//public method to get all product ids from a import_csv_id
	public function getProductIds($import_csv_id, $selected_status) {
		
		$sql = "SELECT import_product_id FROM `". DB_PREFIX . "inventory_import_product` WHERE import_csv_id = ".(int)($import_csv_id)." AND status = ".(int)($selected_status)."";
		return $this->db->query($sql)->rows;
	}
	
	//method to take out useraction history from oc_inventory_product table
	public function getUserActionHistory($product_id) {
		 
		$sql_status = "SELECT  user_action_history FROM `". DB_PREFIX . "inventory_import_product`  WHERE import_product_id = ".(int)($product_id).""; 		
		return $this->db->query($sql_status)->row['user_action_history'];
		
	}
	
	//method keeps track of all the user related activities in validate inventory panel
	//input : product_id , status_new, comment, warning
	public function userActionHistory($product_id,$status,$comment,$warning) {
				
		$serialzed_array = array();
		$date_time =  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		
		//getting user_id
		$user_id = $this->user->getId();	
		
		//getting user_name
		$this->load->model('user/user');
		$user_name = $this->model_user_user->getUser($user_id)['username'];	
		
		$serialzed_array = array(
							'user_id' => $user_id,
							'user_name' => $user_name,
							'new_status' => $status,
							'comment' => $comment,
							'warning' => $warning,
							'date_added' => $date_time,
							);
	
		$previous_user_history = $this->getUserActionHistory($product_id);
		
		if(empty($previous_user_history)) {
			$new_user_history = base64_encode(serialize($serialzed_array));
		} else {
			
			$new_user_history = base64_encode(serialize(array_merge_recursive(unserialize(base64_decode($previous_user_history)),$serialzed_array)));
		}

		$sql_status = "UPDATE `". DB_PREFIX . "inventory_import_product` SET user_action_history = '".$new_user_history."' WHERE import_product_id = ".(int)($product_id).""; 		
		$this->db->query($sql_status);
	}
	
	
	//public method to get all the images corresponding to the import_csv_id
	public function checkImage() {
		if(isset($this->request->get['id_seller']) && !empty($this->request->get['sku_arr'])) {
			$output = array();
			$check_image = new CheckImage($this->db,$this->request->get['id_seller']);
			$output = $check_image->pickImagesSku($this->request->get['sku_arr']);
			$this->response->setOutput(json_encode( 
											array(
												'images' => $output,
											)
										));	
		} else {
			$this->response->setOutput(json_encode( 
											array(
												'final_data' => 'Invalid values',
											)
										));
		}	
	}
	
	//public method to add comment in db
	public function addComment() {
		if(isset($this->request->get['comment'])) {
			$product_ids = array();
			$product_ids = $this->getProductIds($this->request->get['value_import_id'],$this->request->get['selected_status']);
			foreach($product_ids as $product_id) {
				$this->userActionHistory($product_id['import_product_id'],3,$this->request->get['comment'],'');
			}
			$comment = new SetInventoryData($this->db,$this->request->get['value_import_id']);
			$comment->setComment($this->request->get['comment'],$this->request->get['status'], $product_ids);
			
			return json_encode(
								array(
									'final_data' => 'comment',
									));
		}
	}
	
	//public method to add product in db
	public function addProduct() {
		
		
		
		if(!isset($this->request->get['images_arr']) || count(array_filter($this->request->get['images_arr'])) == 0) {
			
				$flag = 4;
				$this->rejectProduct($this->request->get['product_id']);
				$message = "product has no images! Product Rejected!";
				$this->response->setOutput(json_encode( 
												array(
													'flag' => $flag,
													'message' => $message,
												)
											));
		} else if((int)($this->request->get['checking_tax_flag']) == 1) {
			$flag = 3;
			$this->rejectProduct($this->request->get['product_id']);
			$message = "Tax class ID not valid! Please check ! product rejected!";
			$this->response->setOutput(json_encode( 
										array(
											'flag' => $flag,
											'message' => $message,
											)
										));	
		} else if(isset($this->request->get['product_id']) &&  (int)($this->request->get['checking_tax_flag']) == 0) {
			
			$output = array();
			
			$sql = 	"SELECT seller_id FROM `" . DB_PREFIX . "inventory_import_product` AS p INNER JOIN 
					`" . DB_PREFIX ."inventory_import_csv` AS c WHERE p.import_product_id = ". (int)($this->request->get['product_id']) ."";
					
			$seller_id = $this->db->query($sql)->row['seller_id'];
			
			$sql_status = "SELECT seller_status FROM`". DB_PREFIX . "ms_seller` AS s WHERE s.seller_id = ".(int)($seller_id).""; 
			
			$query_status = $this->db->query($sql_status);
			$flag = 0;
			$sql_nickname = "SELECT nickname FROM`". DB_PREFIX . "ms_seller` AS s WHERE s.seller_id = ".(int)($seller_id).""; 
			$seller_code = $this->db->query($sql_nickname)->row['nickname'];
			$product_id = $this->request->get['product_id'];
			
			if((int)($query_status->row['seller_status']) == 1) {
				
				if(isset($this->request->get['tax_class_id_arr'])) {
					$output = $this->insertProduct($this->request->get['product_id'],2, $seller_id,$seller_code,$this->request->get['images_arr']);
					if($output == 1) {
						$flag = 2;
						$this->rejectProduct($this->request->get['product_id']);
						$message = "duplicate SKU Found! product rejected!";
						$this->response->setOutput(json_encode( 
													array(
														'flag' => $flag,
														'message' => $message,
													)
												));
					} else {
						$final_product_id = $this->load->controller('inventory/import/dbUpload', $output)[0]['product_id'];
						//updating final_product_id
						$sql = "UPDATE " . DB_PREFIX . "inventory_import_product SET final_product_id = '".(int)($final_product_id)."'
								WHERE import_product_id = '" . (int)$product_id . "'";
						$query = $this->db->query($sql);
						$flag = 0;
						$message = "data inserted successfully";
						$this->response->setOutput(json_encode( 
													array(
														'flag' => $flag,
														'message' => $message,
													)
												));
					}

				} else {
					
					$output = $this->insertProduct($this->request->get['product_id'],2,$seller_id,$seller_code,$this->request->get['images_arr']);
					
					if($output == 1) {
						$flag = 2;
						$message = "duplicate SKU Found! product rejected!";
						$this->response->setOutput(json_encode( 
													array(
														'flag' => $flag,
														'message' => $message,
													)
												));
					} else {
						$final_product_id = $this->load->controller('inventory/import/dbUpload', $output)[0]['product_id'];
						//updating final_product_id
						$sql = "UPDATE " . DB_PREFIX . "inventory_import_product SET final_product_id = '".(int)($final_product_id)."'
								WHERE import_product_id = '" . (int)$product_id . "'";
						$query = $this->db->query($sql);
						$flag = 0;
						$message = "data inserted successfully";
						$this->response->setOutput(json_encode( 
													array(
														'flag' => $flag,
														'message' => $message,
													)
												));
					}
				}
			} else {
				$flag = 1;
				$message = "seller status not active, cannot insert product";
				$this->response->setOutput(json_encode( 
											array(
												'flag' => $flag,
												'message' => $message,
											)
										));
			}
		}
	}
	
	//pick selective images 
	public function pickImage() {

		
		if(!empty($this->request->get['import_product_id']) && !empty($this->request->get['selected'])) {
			$images = array();
			$sql = 	"SELECT seller_id FROM `" . DB_PREFIX . "inventory_import_product` AS p INNER JOIN 
					`" . DB_PREFIX ."inventory_import_csv` AS c WHERE p.import_product_id = ". (int)($this->request->get['import_product_id']) ."";
			$seller_id = $this->db->query($sql)->row['seller_id'];

			
			$check_image = new CheckImage($this->db, $seller_id);
			
			$images = $check_image->pickImagesSku($this->request->get['selected']);
			
			$this->response->setOutput(json_encode( 
											array(
												'images' => $images,
												'flag' => 0,
											)
										));	
		} else {
			$this->response->setOutput(json_encode( 
											array(
												'images' => 'select images',
												'flag' => 1,
											)
										));	
		}
		
	}
	
	public function downloadFile() {
		$get_inventory_data = new GetInventoryData($this);
		$get_inventory_data->downloadFile($this->request->get['file_path']);
	}
	
	//method to reject product , input : product id
	public function rejectProduct($product_id = false) {
		if($product_id) {
			$this->request->get['product_id'] = $product_id;
		}
		if(!empty($this->request->get['product_id'])) {
			$import_product_id = $this->request->get['product_id'];
			
			//calling useraction history method to keep track of activity on validate inventory panel
			$this->userActionHistory($import_product_id,1,'','');

			//updating status for inventory import product, reject status is 1
			$sql = "UPDATE " . DB_PREFIX . "inventory_import_product SET status = 1
				    WHERE import_product_id = '" . (int)$import_product_id . "'";
			$query = $this->db->query($sql);
			$this->response->setOutput(json_encode( 
											array(
												'msg' => 'Product rejected!',
												'flag' => 1,
											)
										));	
		}
	}
	
	
	//make array to insert product 
	public function insertProduct($import_product_id, $status, $seller_id, $seller_code, $images) {
			
		
			
			$data = array();
			$sql = "SELECT import_data FROM `" . DB_PREFIX . "inventory_import_product` WHERE import_product_id = ".(int)($import_product_id)."" ;
			$query = $this->db->query($sql);
		
			//selecting category id
			$sql2 = "SELECT category_id FROM `" . DB_PREFIX . "inventory_import_csv` AS c INNER JOIN 
						`" . DB_PREFIX ."inventory_import_product` AS p WHERE p.import_csv_id = c.import_csv_id 
						AND p.validated = 1  AND c.seller_id = ".(int)($seller_id)." AND p.import_product_id = ".(int)($import_product_id)."" ;
			
			$import_data = json_decode($query->rows[0]['import_data'],true);
			
			$this->load->model('catalog/category');	
			$category_name = $this->model_catalog_category->getCategory($this->db->query($sql2)->rows[0]['category_id'])['name'];
			$category = $this->db->query($sql2)->rows[0]['category_id'];
			$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($this->db,$seller_id);
			$bool_category_taxable = $this->db->query("SELECT taxable FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category . "'")->row['taxable'];

			/*foreach(array_unique($removed_sku_from_product)  as $sku) {
				if(is_array($tax_class_id_arr)) {
					unset($tax_class_id_arr[$sku]);
				}
				unset($import_data[$sku]);
			}*/
			
			/*if(is_array($tax_class_id_arr)) {
				$tax_class_id_arr = array_values($tax_class_id_arr);
			}*/
			
			$image_path = DIR_IMAGE . 'catalog/';
			
			
			//storing images in this folder after optimisng them
			$destination_folder = DIR_IMAGE . 'catalog/'. $seller_code; 
			
			$oldmask = umask(0);

			if (!is_dir($destination_folder)) {
				//creating directory if not exists by the name of seller_id
				mkdir($destination_folder, 0777);
			}	
		
			$final_arr = array();
			$commission_factor = 10;
			$commission = 10;
			$seller_tax_factor = 10;
			
			//if JP then tax will be corresponding to the tax_class_id
			//if non JP, seller tax will be 0
			$image_path = $image_path.$seller_code;
			$filter_start = 2;
			$filters_array = array();
			$images2 = array();
			$name_list = array();
			$filters_array = array();
			//fetching naming filters for this category from db
			$name_list = explode(",",$this->db->query("SELECT naming_filters FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$this->db->query($sql2)->rows[0]['category_id'] . "'")->row['naming_filters']);
			$filter_values = array();
			
			$array_not_filters = ['AvailableSets','Color Description','Color Set Sizes','Weight of a Piece (in gm)','Transfer Price','Tax','Set Type','SKU Code','Pieces in Size Set','Pieces in Free Size Set','Free Size quantity','Color Set quantity','Any additional Comments','Sizes in Size Set', 'id','Quantity', 'Pieces in Set'];
			
			$str = "";
			
			
			//filter naming convention using naming field in oc_category
			foreach ($import_data as $key=>$val) {
				
				if(!in_array($key,$array_not_filters) && $val != "") {

					$filter_values[$key] = $val;
					$filter_group_id = $this->db->query("SELECT filter_group_id FROM " . DB_PREFIX . 
											"filter_group_description WHERE name = "."'$key'"."")->row['filter_group_id'];
					if(in_array($filter_group_id,$name_list)) {
						$str .= $val. " ";
					}
				}
			}
					
			array_push($data,$import_data);
	
			foreach($data as $key=>$value) {
				
				$keys = array_keys($data[$key]);
				$value = array_values($data[$key]);
				$validation = new InventoryValidation($this,$seller_id,$category);
				
				if(!empty($validation->getProductIdBySkuAndSeller($data[$key]['SKU Code']))) {
					
					return 1;	//checking for duplicate sku code
					
				} else {
					
					
					foreach ($filter_values as $filter_group_name => $filter) { 
						if(!empty($filter)) {
							
							$final_arr[$key]['product_filter'][] = $this->load->controller('inventory/import/readFilterId',
																array(trim($filter_group_name),trim($filter))
																);
							$final_arr[$key]['filters'][$filter_group_name] = trim($filter);
							
						}
						
					}
							
					
					$final_arr[$key]['name'] = $str.$category_name;
					$final_arr[$key]['sku'] = $data[$key]['SKU Code'];
					$final_arr[$key]['price'] = trim($data[$key]['Transfer Price']);
					
					//color set
					if((int)($data[$key]['Set Type']) == 1) {
						
						$final_arr[$key]['piece_in_set'] = count(explode(",", trim($data[$key]['Color Set quantity'])));
						$final_arr[$key]['quantity']  = $data[$key]['Color Set quantity'];
						$size_options = $data[$key]['Color Set Sizes'];
						$set_description = "1 Set = Total " . $final_arr[$key]['piece_in_set'] . " pieces; 1 each of color " . $data[$key]['Color Description'];
						
					} else if((int)($data[$key]['Set Type']) == 0) {
						//size set
						$pieces = explode(",",trim($data[$key]['Pieces in Size Set']));
						$sizes = explode(",",trim($data[$key]['Sizes in Size Set']));
						for($i = 0; $i < sizeof($pieces); $i++) {
							if($pieces[$i] == 0) {
								unset($sizes[$i]);
							}
						} 
						$final_arr[$key]['piece_in_set'] = count(array_filter(explode(",", $data[$key]['Pieces in Size Set'])));
						$final_arr[$key]['quantity']  = (int)min(array_filter(explode(",",$data[$key]['Pieces in Size Set'])));
						$set_description = "1 Set = Total " . $final_arr[$key]['piece_in_set'] . " pieces; 1 each of sizes " . implode(",",$sizes);
						
					} else if((int)($data[$key]['Set Type']) == 2) {
						
						$pieces = $data[$key]['Pieces in Free Size Set'];
						$final_arr[$key]['piece_in_set'] = $pieces;
						$final_arr[$key]['quantity']  = $data[$key]['Free Size quantity'];
						$set_description = "1 Set = Total " . $pieces . " pieces of free size set; Available sets ". $final_arr[$key]['quantity'] ;
					} else if((int)($data[$key]['Set Type']) == 3) {
						
						$pieces = $data[$key]['Pieces in Set'];
						$final_arr[$key]['piece_in_set'] = $pieces;
						$final_arr[$key]['quantity']  = $data[$key]['Quantity'];
						$set_description = "1 Set = Total " . $pieces . " pieces of not specific set; Available sets ". $final_arr[$key]['quantity'];
					}
					
					$final_arr[$key]['weight_per_piece'] = (float)($data[$key]['Weight of a Piece (in gm)']/1000);
					$final_arr[$key]['weight'] = (float)(($data[$key]['Weight of a Piece (in gm)']*$final_arr[$key]['piece_in_set'])/1000);
					$final_arr[$key]['price_per_set'] 	= $final_arr[$key]['price']/(int)$final_arr[$key]['piece_in_set'];
					$final_arr[$key]['mrp'] = 0; //set to 0 
					$final_arr[$key]['expected_dispatch_date'] = '0000-00-00';
				
					
					//fixing issue for cases when only one product is listed in inventory
					if(empty(array_filter($final_arr[$key]['product_filter']))) {
						unset($final_arr[$key]['product_filter']);
						unset($final_arr[$key]['filters']);
						$str = "";
						$filter_start = 2;
						for ($index=$filter_start; $index < count($data[$key])-14; $index++) { 
							$str = $str." ".trim($value[$index]);
							$final_arr[$key]['product_filter'][] = $this->load->controller('inventory/import/readFilterId',
																	array(trim($keys[$index]),trim($value[$index]))
																	);
							$final_arr[$key]['filters'][$keys[$index]] = trim($value[$index]);
						}
						unset($final_arr[$key]['name']);
						$final_arr[$key]['name'] = $str. " ". $category_name;

					}
				
					$final_arr[$key]['minimum'] = 1;
					$final_arr[$key]['status'] 	= 1;
					$final_arr[$key]['shipping'] = 1;
					$final_arr[$key]['sort_order'] = 999;
					$final_arr[$key]['subtract'] = 1;
					$final_arr[$key]['weight_class_id']	= 1;
					$final_arr[$key]['stock_status_id']	= 7;
					$final_arr[$key]['product_status']	= 1; 
					$final_arr[$key]['product_approve']	= 1;
					$final_arr[$key]['product_store']	= array(0,2);
					$parent_cat = $this->load->controller('inventory/import/getParentCategory',$category);
					$final_arr[$key]['product_category'] = array($category, $parent_cat);
					$final_arr[$key]['seller_code']		= trim($seller_code); 
					$final_arr[$key]['model']	=  strtoupper(trim(preg_replace('/[^\da-z]/i','',$seller_code).substr(md5(microtime()),mt_rand(0,23),8)));                        
					$count = 0; //initialising counter for naming images
					
					if (!empty($images)) {
						foreach ($images as $key_image => $value_image) {  
							
							$original_name = basename($value_image); //name.jpg
							
							$original_extension = substr($value_image, strrpos($value_image, '.')); // ".jpg"
							
							//getting path 
							$destination_path = pathinfo(DIR_IMAGE.str_replace('http://www.wsb.in/image/','',$value_image))['dirname'];
							
							//saving file using the naming convention
							$stored_name = $destination_path.'/'.$final_arr[$key]['model']."_".$count. $original_extension;
							
							if(file_exists(DIR_IMAGE.str_replace('http://www.wsb.in/image/','',$value_image))) {

								//copying image from source to destination
								copy(DIR_IMAGE.str_replace('http://www.wsb.in/image/','',$value_image),$stored_name);
								//deleting original image
								unlink(DIR_IMAGE.str_replace('http://www.wsb.in/image/','',$value_image));
								//modifying the permission of image
								chmod($stored_name, 0777);
								if(filesize($stored_name)/1000 > 250) {
									exec("mogrify -geometry x800 $stored_name");
									exec("mogrify -quality 60 $stored_name"); 
								}
								
								//again modifying the permission of image
								chmod($stored_name, 0777);
							}
							$final_arr[$key]['product_image'][$key_image]['image'] 	= str_replace('/var/www/html/wholesalebox/image/','',$stored_name);
							$final_arr[$key]['product_image'][$key_image]['sort_order']	= $key_image+1;
							$count++;
						}
					}
					
					$final_arr[$key]['length'] 			= '';
					$final_arr[$key]['width'] 			= '';
					$final_arr[$key]['height'] 			= '';
					$final_arr[$key]['length_class_id'] 	= '';
					$final_arr[$key]['manufacturer_id'] 	= '';
					$final_arr[$key]['manufacturer'] 		= '';
					$final_arr[$key]['points'] 			= '';
					$final_arr[$key]['location'] 			= '';
					$final_arr[$key]['date_available'] 	= '';
					$final_arr[$key]['is_single'] 	    = 0; 
					$final_arr[$key]['commission']		= trim($commission);				
				
				
					$tax_based_inventory = new GetInventoryData($this,$category,$seller_id,'');
					
					$seller_info = SellerInfo::getSellerZoneIdAndPickupCityCode($this->db, $seller_id);

					$seller_zone_id  	= $seller_info['zone_id'];
					$pickup_city_code   = $seller_info['pickup_city_code'];

					$tax_data_array = $tax_based_inventory->getTaxForSeller($data[$key]['Tax'],$seller_zone_id, $pickup_city_code);
					
					if($tax_data_array[1] != "Contact administrator") {
						$final_arr[$key]['tax_class_id'] = $this->db->query("SELECT tax_class_id FROM " .DB_PREFIX. "tax_class WHERE title = '".$tax_data_array[1]. "'")->row['tax_class_id'];
					}
					
					$final_arr[$key]['seller_tax'] =  preg_replace("/[^0-9,.]/", "", $tax_data_array[0]);
					
				/*	die;
					
					if($sellerinput == 0 || $bool_tax == 3 ) {
						//no input case
						  = 0; 
						
						if(is_array($tax_class_id_arr)) {
							$final_arr[$key]['tax_class_id'] = $tax_class_id_arr[$key];
						} else {
							 = $tax_class_id_arr;
						}

					} else if((int)($bool_tax) == 1 || (int)($bool_category_taxable) == 0) {
						
						$final_arr[$key]['seller_tax']  = 0; 
						$final_arr[$key]['tax_class_id'] = 0;
						
						
					} else {
						
						//with input case
						$final_arr[$key]['seller_tax']  = $data[$key]['Tax'];
						$tax_rate_sql = "SELECT tax_rate_id from `" .DB_PREFIX. "tax_rate` AS t WHERE t.rate = 
										".(float)($data[$key]['Tax'])."";

						$tax_rate_query = $this->db->query($tax_rate_sql);
						if($tax_rate_query->num_rows >= 1) {
							$tax_rate_id = $tax_rate_query->row['tax_rate_id'];
							$sql_tax_class_id = "SELECT tax_class_id from `" .DB_PREFIX. "tax_rule` AS tc WHERE tc.tax_rate_id = 
											".(int)($tax_rate_id)."";
							$tax_class_query = $this->db->query($sql_tax_class_id);
							$tax_class_id = $tax_class_query->row['tax_class_id'];
							$final_arr[$key]['tax_class_id'] = $tax_class_id;
						}
						if(!isset($tax_rate_query->row['tax_rate_id'])){
								
								$data[$key]['sale_tax'] = (float)($data[$key]['Tax']);
								$final_arr[$key]['tax_class_id'] = 'tax rate not set! Please set the tax rate id';

						}
					}
					
					*/
					
					$final_arr[$key]['seller_id']		= trim($seller_id); 
					 
					$final_arr[$key]['selling_price']		= ceil($commission_factor * $final_arr[$key]['price'] / $seller_tax_factor); 

					$final_arr[$key]['product_description'] = array('1' => array(
							'name'					=> trim($final_arr[$key]['name']),
							'set_description' 		=> $set_description,
							'description' 			=> trim($data[$key]['Any additional Comments']),
							'tag' 					=> trim(implode(" ", explode(" ", $final_arr[$key]['name'])).", wholesale", ","),
							'meta_keyword'		    => trim(implode(" ", explode(" ", $final_arr[$key]['name'])).", wholesale", ","),
							'meta_title'			=> trim($final_arr[$key]['name']. " " . $final_arr[$key]['model'] . " - Wholesale", ",") ,
							'meta_description'		=> trim($final_arr[$key]['name']. " " . $final_arr[$key]['model'] . " - Wholesale", ",") ,
							));
							
					//$final_arr[$key]['product_option'] = '';
					$final_arr[$key]['keyword'] = $this->load->controller('inventory/import/readSeoUrl',array($final_arr[$key]['name'], $final_arr[$key]['model']));	
				}
			
				umask($oldmask);
				
				
				//calling useraction history method to keep track of activity on validate inventory panel
				$this->userActionHistory($import_product_id,2,'','');
				
				//updating status for inventory import product, approve status is 2
				$sql = "UPDATE " . DB_PREFIX . "inventory_import_product SET status = '".(int)($status)."'
						WHERE import_product_id = '" . (int)$import_product_id . "'";
				$query = $this->db->query($sql);

				return $final_arr?$final_arr:'';
					
				}

		}
		

}

?>
