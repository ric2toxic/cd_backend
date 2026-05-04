<?php

class ControllerRestapiProducts extends Controller {
	
	private $app_version_code = 1;
	private $call_from;
	private $getrequest;
	private $response;
	private $filter_store_product = 0; //find the store inventory only
	private $filter_store_inventory_age = ''; //filter storeinventory purchase before a date
	private $filter_store_code = ''; //filter product from a specific store
	private $search_term ='';
	private $category_id = 0;
	private $postcode = '';
	private $clearance_sale = '';
	private $show_exclusive_only = 0;
	private $category_facet = 0; //used when search is made without selcting category, e.g. home screen of app
	private $rating_filter = '';
	private $custom_store = "wholesale";
	private $filters = ''; 
	private $price_filter = '';
	private $product_ids = '';
	private $handpicked_ids = '';
	private $filter_product_total = 0;
	private $random_string = '';
	private $limit = 10;
	private $page = 1;
	private $last_filter_action = '';
	private $filter_only = 0;
	private $client_preferences = '';
	private $filter_seller_id = '';
	private $create_page_filters = '0';
	private $show_page_filters = '0';
	private $page_filters = '';
	private $_franchise_id = 0;
    private $_franchise_margin = 0;
    private $device_id = '';
    private $gcm_id = '';
    private $language = 'en';
    private $testResponse;
    private $is_customer_exclusive;
    private $country_code = null;
    private $popular_search = array();
	
	
	public function search($request_data='') {
		
		$this->load->model('restapi/service');
		
		$request = $this->_getRequestData($request_data);

		$this->_setCustomerDetail();
				
		$this->_checkApiCallSource();
		
		$this->validateApiCall();
		
		$this->_forceUpdate();
		
		$this->_getFranchiseDetailsFromHeader();
		
		$this->_setRequestDataForFilter();
		
		$filter_data = $this->_prepareFilterData();
								
		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			
			$solr = new SolrProduct($this);
			$products = $solr->getProductFromSolr($filter_data);
			$coun = $products['total']; //get total from SOLR query

		} else {

			$products = $this->model_restapi_service->getProducts($filter_data);
			$coun = $this->model_restapi_service->getTotalProducts($filter_data);
		}


		if($coun > $this->limit){
			$tpage = ceil($coun/$this->limit);
			$cpage = $this->page;
			$npage = $cpage + 1;
			if($npage > $tpage){
				$npage = 0;
			}
			$ppage = $cpage - 1;
			if($cpage < 1){
				$ppage = 0;
			}

		} else {
			$tpage = ceil($coun/$this->limit);
			$cpage = $this->page;
			$npage = '0';
			$ppage = '0';
		}

		$this->response['data']['popular_search'] = $this->popular_search;
					
		if (isset($products['data']) && !empty($products['data'])) {
										
			$this->response['status'] = '1';
			$this->response['status_text'] = 'Success';
			$this->response['current_page'] = $cpage;
			$this->response['next_page'] = $npage;
			$this->response['previous_page'] = $ppage;
			$this->response['total_page'] = $tpage;
			$this->response['total_products'] = ($coun%10 == 1) ? ($coun + 1) : $coun;

			
			$this->response['data']['products'] = $products['data'];
			

			if ( isset( $products['page_filters'] )) {
				$this->response['data']['page_filters'] = $products['page_filters'];
			}

			if ( isset( $products['current_page_filter'] ) && $npage > 0 ) {
				$this->response['data']['current_page_filter'] = $products['current_page_filter'];
			}
			
			//display banners in search results
			$this->_getBanners();
			
			$this->response['whatsapp_number'] = WHATSAPP_NUMBER;
			$this->response['phone_number'] = PHONE_NUMBER;
			$this->response['new_country'] = $this->country_code;
			
			$this->response['handpicked_ids']     = (isset($products['handpicked_ids'])) ? $products['handpicked_ids'] : '';
			$this->response['product_total']      = $coun;
			$this->response['random_string']      = '';


		} else if(isset($products['filter_facets']) && $coun > 0 && empty( $products['send_status'] )) {

			$products['filter_facets']['rating'] = $this->_getFilterFacetOnly($products);

			$this->response['status'] = '1';
			$this->response['status_text'] = 'Success';
			$this->response['current_page'] = $cpage;
			$this->response['next_page'] = $npage;
			$this->response['previous_page'] = $ppage;
			$this->response['total_page'] = $tpage;
			$this->response['total_products'] = $coun;
			
			$this->response['data']['filter_facets'] = $products['filter_facets'];
			$this->response['whatsapp_number'] = WHATSAPP_NUMBER;
			$this->response['phone_number'] = PHONE_NUMBER;

			$this->response['new_country'] = $this->country_code;
			$this->response['handpicked_ids']     = (isset($products['handpicked_ids']))?$products['handpicked_ids']:'';
			$this->response['product_total']      = $coun;
			$this->response['random_string']      = '';
		
		} else {
			$this->response['error_code'] = '1001';
			$this->response['status'] = '0';

			if ( !empty( $products['send_status'] )) {
				$this->response['status'] = $products['send_status'];
				$this->response['total_products'] = $coun;
				$this->response['next_page'] = $npage;
			}

			$this->response['status_text'] = 'failed';
			$this->response['message'] = 'Products Not Found.';

			if ( $this->page <= 1 && !empty( $this->getrequest['show_single'] )) {
				$this->response['message'] = 'No products found in Single store. For more products, switch to Wholesale store from left navigation menu.';
			}

			  if(!empty($search)) {
				$this->response['extended_message'] = "Search Term: '".$search."'. \n You can find similar items in other categories, tap on button given below to switch category.";
			  }
			  
			  if(!empty($products['filter_facets']['price'][0]) || !empty($products['filter_facets']['price'][1])) {
				
				$products['filter_facets']['rating'] = $this->_getFilterFacetOnly($products);
				$this->response['data']['filter_facets'] = $products['filter_facets'];
			  }
		}
		
		 if (empty($request_data)) {
			
			 $this->_sendResponse();
         } else {
			return $this->response;
         }
	}
		
	private function _getRequestData($request_data){
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') || !empty($request_data) ) {

		    if (empty($request_data)) {
                $inputJSON = file_get_contents('php://input');
                $this->getrequest = json_decode($inputJSON, TRUE);
            }else{
                $this->getrequest = $request_data;
            }
            
        }
        
        return true;   
            
	} 
	
	private function _checkApiCallSource() {
		if(!empty($this->getrequest['request_by'])){
			switch ($this->getrequest['request_by']) {
					
					case 'ANDROID APP':
					$this->call_from = 'ANDROID';
					break;
					
					case 'IOS_APP':
					$this->call_from = 'IOS';
					break;
					
					default:
					$this->call_from = 'ANDROID';
					break;
			}
		}else{
			$this->call_from = 'ANDROID';
		}
	                  
	}
	
	private function _forceUpdate() {
		
		$this->load->model('restapi/service');
		$force_update = $this->model_restapi_service->forceUpdate('','');
		
        $this->response['compulsory_update_flag'] = $force_update;
        
		
		
		if ($this->response['compulsory_update_flag'] != 'not') {
			
			if ($this->call_from == 'IOS') {
			
			$this->response['APP_VERSION'] = IOS_APP_VERSION;
			$this->response['APP_MESSAGE'] = 'You\'re missing out some great features, Update WholesaleBox App now!.';
		
			} elseif ($this->call_from == 'ANDROID') {
				
				$this->response['ANDROID_APP_VERSION'] = ANDROID_APP_VERSION; //current Android app version is 58 .update/change ANDROID_APP_VERSION to APP_VERSION in app version >= 63.
				$this->response['ANDROID_APP_MESSAGE'] = 'You\'re missing out some great features, Update WholesaleBox App now!.';
			
			}
			$this->response['status'] = '0';
			$this->response['status_text'] = 'Success';
			
			$this->_sendResponse();
        }
	}
	
	private function _sendResponse() {
		
		echo json_encode($this->response);
		exit;
		
	}
	
	 private function _getStorePurchasedDaysMap() {
		 
		$result = array();
		foreach (STORE_PURCHASED_DAYS as $store => $store_data) {
		  foreach ($store_data as $key => $value) {
			$result['STORE' . $store . $key] = array(
			  'store' => $store,
			  'days' => $value
			);
		  }
		}
		return $result;
	}
	
	private function _setPostcode() {
		if (isset($this->getrequest['postcode'])) {
              $this->postcode = $this->getrequest['postcode'];
          }
	}
	
	private function _setUserId() {
		if (isset($this->getrequest['user_id'])) {
              $this->user_id = $this->getrequest['user_id'];
          }
	}
	private function _setDeviceId() {
		if (isset($this->getrequest['device_id'])) {
              $this->device_id = $this->getrequest['device_id'];
          }
	}
	private function _setGcmId() {
		if (isset($this->getrequest['gcm_id'])) {
              $this->gcm_id = $this->getrequest['gcm_id'];
          }
	}
	
	private function _setAppVersionCode() {
		if (isset($this->getrequest['app_version_code'])) {
              $this->app_version_code = $this->getrequest['app_version_code'];
          }
	}
	private function _setIsCustomerExclusive() {
		if (count($this->customer_detail)>0) {
            $this->is_customer_exclusive = 1;
          }else{
          	$this->is_customer_exclusive = 0;
          }
	}
	private function _setCustomerDetail() {
		$this->load->model('restapi/service');
		if (!empty($this->getrequest['user_id'])) {
			$rs = $this->model_restapi_service->checkCustomerByID((int)$this->getrequest['user_id']);
            $this->customer_detail = $rs??null;
          }
	}


	private function _setCountryCode() {
		if (!empty($this->customer_detail['country_code'])) {
            $this->country_code = $this->customer_detail['country_code'];
          }
	}

	private function _setFilterForStoreInventory() {
		// for store 30 products
       if (!empty($this->getrequest['search'])) {

            $store_purchased_days_map = $this->_getStorePurchasedDaysMap();
            
            if (isset($store_purchased_days_map[$this->getrequest['search']])) {
                  $this->filter_store_product = 1;
                  $this->filter_store_code = $store_purchased_days_map[$this->getrequest['search']]['store'];
                  $purchase_days = $store_purchased_days_map[$this->getrequest['search']]['days'];
                  $date = date_create();
                  date_sub($date,date_interval_create_from_date_string($purchase_days . " days"));
                  $this->filter_store_inventory_age = date_format($date,"Y-m-d\TH:i:s\Z");
            }else{
            	$this->search_term =  (string)$this->getrequest['search'];
            }
		}
	}
	
	private function _setOffersKeyData() {
		
		if ( isset($this->getrequest['offers']) ) {

			$offer_data = array();
			
			if ( is_array($this->getrequest['offers']) ) {
				foreach ( $this->getrequest['offers'] as $offer_key => $offer_value ) {
					$offer_data = explode("__", $offer_key);
					break;
				}
			}

			if ( count($offer_data) == 2 ) {

				$offer_value = $offer_data[0];
				$offer_type = $offer_data[1];

				if ( $offer_type == 'category' ) {
					$this->category_id = (int) $offer_value;

				} elseif ( $offer_type == 'search' ) {
					$this->search_term = (string) $offer_value;

				} elseif ( $offer_type == 'sale' ) {
					/** to use clearance sale please add banner_type = sale in khufiya and give value 1 */
					$this->clearance_sale = (string) $offer_value;
				}

			} else {

				if ( isset($request['offers']['show_exclusive_only']) ) {
					$this->show_exclusive_only = trim($this->getrequest['offers']['show_exclusive_only']);

				} elseif ( is_array($this->getrequest['offers']) && sizeof($this->getrequest['offers']) > 0 ) {
				  $this->search_term = (string)array_values($this->getrequest['offers'])[0];

				} else {
				  $this->search_term =  (string)$this->getrequest['offers'];
				}
			}
		}
	}
	
	private function _setFranchiseTab() {
		if (!empty($this->getrequest['category_id'])) {
			
			if(strpos($this->getrequest['category_id'], '_F')) {
				$this->getrequest['category_id'] = str_replace('_F','',$this->getrequest['category_id']);
				$this->filter_franchise_tab = true;
			}
			$this->category_id = $this->getrequest['category_id'];
		} 
	}
	
	private function _setCategoryFacet() {
		
		 if (isset($this->getrequest['category_facet'])) {
              $this->category_facet = $this->getrequest['category_facet'];
          }
	}
	
	private function _setRatingFilter() {
		
		if (isset($this->getrequest['rating_filter'])) {
			$this->rating_filter = $this->getrequest['rating_filter'];
		}
	}
	
	private function _setFilters() {
		
		if (isset($this->getrequest['filter'])) {
			$this->filters = $this->getrequest['filter'];
		}
			
	}
	
	private function _setStore() {
		if(isset($this->getrequest['show_single']) && $this->getrequest['show_single'] == "1"){
			$this->custom_store = "single";
		}
	}
	
	private function _setSort() {
		
		if (!empty($this->filter_store_product)) {
               
                $this->sort = 'date_added';
                $this->order = 'DESC';
                return true;              
        } 
		
		if (isset($this->getrequest['sort'])) {
			$prnd = "0";
			if($this->getrequest['sort'] == "hand_picked"){
				$this->sort = 'sort_order';
				$this->order = "ASC";
			}else if($this->getrequest['sort'] == "latest_designs"){
				$this->sort = 'date_added';
				$this->order = "DESC";
			}else if($this->getrequest['sort'] == "price_low_to_high"){
				$this->sort = 'selling_price';
				$this->order = "ASC";
			}else if($this->getrequest['sort'] == "price_high_to_low"){
				$this->sort = 'selling_price';
				$this->order = "DESC";
			}else if($this->getrequest['sort'] == "rating_low"){
				$this->sort = 'rating';
				$this->order = "ASC";
			}else if($this->getrequest['sort'] == "rating_high"){
				$this->sort = 'rating';
				$this->order = "DESC";
			}else if($this->getrequest['sort'] == "special_price"){
				$this->sort = 'special_price';
				$this->order = "DESC";
			} else if(strtolower(trim($this->getrequest['search'])) == "rxt") {
				// if sort is not present in request and search keyword is rxt, then set sort to date added
				$this->sort = "p.date_added";
				$this->order = "DESC";
			} else {
				
				if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
					$this->sort = 'sort_order';
				}else{
					$this->sort = 'rand()';
				}
				$this->order = "ASC";
			}

		} else if(strtolower(trim($this->getrequest['search'])) == "rxt") {
			$this->sort = "p.date_added";
			$this->order = "DESC";
		} else {
			if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
				$this->sort = 'sort_order';
			}else{
				$this->sort = 'rand()';
			}
			$this->order = "ASC";
		}
	}
	
	private function _setPriceFilter() {
		if (isset($this->getrequest['price_filter'])) {
			$this->price_filter = $this->getrequest['price_filter'];
		} 
	}
	
	private function _setProductIds() {
		if (isset($this->getrequest['product_ids'])) {
			$this->product_ids = $request['product_ids'];
		}
	}
	
	private function _setHandpickedIds() {
		// Handpicked ids, if we are in sort_order mode
		if (isset($this->getrequest['handpicked_ids'])) {
			$this->handpicked_ids = $this->getrequest['handpicked_ids'];
		}
	}
	
	private function _setFilterProductTotal() {
		if (isset($this->getrequest['product_total'])) {
			$this->filter_product_total = $this->getrequest['product_total'];
		} 
	}

	private function _setRandomString() {
		// product total for page 2 to 5
		if (isset($this->getrequest['random_string'])) {
			$this->random_string = trim($this->getrequest['random_string']);
		}
	}

	private function _setPage() {
		if (isset($this->getrequest['page'])) {
			$this->page = $this->getrequest['page'];
		} 
	}

	private function _setLimit() {
		if (isset($this->getrequest['limit'])) {
			$this->limit = $this->getrequest['limit'];
		} 
	}

	private function _setLastFilterAction() {
		if (isset($this->getrequest['last_filter_action'])) {
			$this->last_filter_action = $this->getrequest['last_filter_action'];
		} 
	}

	private function _setFilterOnly() {
		if (isset($this->getrequest['filter_only'])) {
			$this->filter_only = $this->getrequest['filter_only'];
		} 
	}
	
	private function _setLanguage() {
		if (isset($this->getrequest['language'])) {
			$this->language = $this->getrequest['language'];
		} 
	}

	private function _setClientPreferences() {
		if (isset($this->getrequest['client_preferences'])) {
			$this->client_preferences = $this->getrequest['client_preferences'];
		} 
	}

	private function _setFilterSellerId() {
		if (isset($this->getrequest['filter_seller_id'])) {
			$this->filter_seller_id = $this->getrequest['filter_seller_id'];
		} 
	}
	
	//It used to display popular filters in search results
	private function _setPageFilter() {
		// page filters inflated string
        if ( !empty( $this->getrequest['page_filters'] )) {
            $this->page_filters = $this->getrequest['page_filters'];
        }
		 // page filter flag
		if ( (int) $this->app_version_code > 107 ) {
			if ( empty( $this->last_filter_action ) && empty( $this->filters ) 
				&& !empty( $this->category_id ) && empty( $this->price_filter ) 
				&& empty( $this->rating_filter )) {

				if ( ( !isset( $this->getrequest['show_page_filters'] )) || ( !empty( $this->getrequest['show_page_filters'] ))) {

					if ( $this->page == '1' && empty( $this->page_filters )) {
						$this->create_page_filters = '1';
					}

					if ( !empty( $this->getrequest['show_page_filters'] ) || $this->page % 2 == 0 ) {
						$this->show_page_filters = '1';
					}
				}
			}
		}
	}


	private function _setPopularSearch(){
			$popular = $this->model_restapi_service->getPopularSearch();
			$i = 0;
			foreach($popular as $pkey=>$pvalue){
				$this->popular_search[] = (object) $pvalue['popular_search'];
				$i++;
			}
	}
	
	private function _setRequestDataForFilter() {
		$this->_setFilterForStoreInventory(); //For store30
		$this->_setFranchiseTab();
		$this->_setUserId();
		$this->_setDeviceId();
		$this->_setGcmId();
		$this->_setAppVersionCode();
		$this->_setOffersKeyData();
		$this->_setCategoryFacet();
		$this->_setRatingFilter();
		$this->_setPostcode();
		$this->_setFilters();
		$this->_setStore();
		$this->_setPriceFilter();
		$this->_setProductIds();
		$this->_setHandpickedIds();
		$this->_setFilterProductTotal();
		$this->_setRandomString();
		$this->_setPage();
		$this->_setLimit();
		$this->_setLastFilterAction();
		$this->_setFilterOnly();
		$this->_setClientPreferences();
		$this->_setFilterSellerId();
		$this->_setLanguage();
		$this->_setSort();
		$this->_setIsCustomerExclusive();
		$this->_setPageFilter();
		$this->_setPopularSearch();
		$this->_setCountryCode();
	}
	
	 private function _getFranchiseDetailsFromHeader(){
		 
        $headers = getallheaders();
        if(isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int)$headers['crm_role_id'] == (int)CRM_FRANCHISE_ROLE_ID){
            $this->load->model('restapi/service');
            $franchise_id = $this->model_restapi_service->getCustomerIdUsingCRMUserId($headers['crm_user_id']);
            if($franchise_id != 0){
                $this->_franchise_id = $franchise_id;
                if(isset($headers['franchise_margin'])){
                    $this->_franchise_margin = $headers['franchise_margin'];
                }
            }
        }

       
    }
	
	private function _prepareFilterData() {
		//echo $this->rating_filter; exit;
		
		 $filter_data = array(
							'filter_name'                     => $this->search_term,
							'filter_filter'                   => $this->filters,
							'sort'                            => $this->sort,
							'order'                           => $this->order,
							'start'                           => abs( (int) ($this->page - 1) ) * $this->limit,
							'limit'                           => $this->limit,
							'price_filter'                    => $this->price_filter,
							'user_id'                         => $this->user_id, // To get message according to product
							'seller'                          => '',//$sellers,
							'custom_store'                    => $this->custom_store,
							'product_ids'                     => $this->product_ids,
							'page'                            => $this->page,
							'handpicked_ids'                  => $this->handpicked_ids,
							'product_total'                   => $this->filter_product_total,
							'random_string'                   => $this->random_string,
							'filter_special'                  => $this->clearance_sale,
							'call_from'                       => 'app',
							'rating_filter'                   => $this->rating_filter,
							'is_facet'                        => 1,
							'show_exclusive_only'             => $this->show_exclusive_only,
							'last_filter_action'              => $this->last_filter_action,
							'filter_only'                     => $this->filter_only,
							'facets'                          => true,
							'client_preferences'              => $this->client_preferences,
							'category_facet'                  => $this->category_facet,
							'filter_seller_id'                => $this->filter_seller_id,
							'date_added_less_than'            => $this->filter_store_inventory_age,
							'store_product'                   => $this->filter_store_product,
							'store_code'                      => $this->filter_store_code,
							'create_page_filters'             => $this->create_page_filters,
							'show_page_filters'				  => $this->show_page_filters,
							'page_filters'					  => $this->page_filters,
							'device_id'     				  => $this->device_id,
							'gcm_id'     					  => $this->gcm_id,
							'filter_category_id'			  => $this->category_id,
							'franchise_id'					  => $this->_franchise_id,
							'filter_franchise_tab'			  => $this->filter_franchise_tab,
							'postcode'						  => $this->postcode,
							'clicked_filter'				  => $this->filters
						);
						
			if (in_array($this->call_from, array('ANDROID', 'IOS'))) {
				$filter_data['call_from'] = 'app';
			}
			
			return $filter_data;
						
	}
	
	private function _getBanners() {
		
			if ( (int) $this->app_version_code > 114 && strtoupper( $this->call_from ) == "ANDROID" ) {
				
				$this->load->model('restapi/service');
				
				$credit_activation_status = $this->getrequest['credit_activation_status'] ?? 0;
				$customer_dropshipper = $this->getrequest['customer_dropshipper'] ?? 0;

				$page_banners = $this->model_restapi_service->appListPageAllBanners( $this->language, (int) $credit_activation_status, (int) $customer_dropshipper );

				if ( !empty( $page_banners )
					 && ( $this->page % 2 != 0 )) { // only on odd pages
					$current_page_banner_key = ( floor( $this->page / 2 ));

					if ( isset( $page_banners[ $current_page_banner_key ] )) {
						$this->response['data']['banner'] = $page_banners[ $current_page_banner_key ];
					}
				}
			}
		
		return true;
	}
	
	private function _getFilterFacetOnly($products) {
		
		$rating_arr_new = new stdClass();
		
		if(((int) $this->app_version_code > 70 || ($this->call_from == 'IOS' && (int) $this->app_version_code > 1))) {
			
			if(isset($products['filter_facets']['rating'])) {
				
				$rating_arr_old = $products['filter_facets']['rating'];
				$rating_arr_new = array();
				if(isset($rating_arr_old['0.0'])){unset($rating_arr_old['0.0']);}
				if(isset($rating_arr_old['3.0'])){$rating_arr_new['3.0'] = array('label'=>'Average','count'=>$rating_arr_old['3.0']);}
				if(isset($rating_arr_old['4.0'])){$rating_arr_new['4.0'] = array('label'=>'Good','count'=>$rating_arr_old['4.0']);}
				if(isset($rating_arr_old['5.0'])){$rating_arr_new['5.0'] = array('label'=>'Excellent','count'=>$rating_arr_old['5.0']);}

			}
		}
		
		return $rating_arr_new;
	}
	private function manage_error_reporting() {
		$headers = getallheaders();
		if(!empty($headers['disable_error_reporting'])) {
		  error_reporting(0);
		}
		return true;
	}
	public function validateApiCall(){
		ini_set('display_errors',1);
		error_reporting(E_ALL);
		$this->manage_error_reporting();
		if ($this->config->get('config_app_maintenance') == 1 ) {
			$rt['error_code'] = '8888';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Hey!! Engineers @ work!!. We will be back shortly. C Ya';
			echo json_encode($rt); exit;
		}
		return true;

		$this->load->model('restapi/service');

		$headers = getallheaders();

		if(!$this->model_restapi_service->validateApiCall($headers)){
			$rt['error_code'] = '9999';
			$rt['status'] = '0';
			$rt['status_text'] = 'failed';
			$rt['message'] = 'Invalid API call';
			echo json_encode($rt); exit;
		}
	}
	
}
