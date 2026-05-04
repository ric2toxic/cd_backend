<?php
abstract class Controller {
	protected $registry;
	
	public function __construct($registry) {
		$this->registry = $registry;
        if( isset($this->session->data['ORDER_FROM']) && ($this->session->data['ORDER_FROM'] == 'ANDROIDAPP' || $this->session->data['ORDER_FROM'] == 'IOSAPP' ) ){
            // Do not validate the token in case of app
        }
        else{
            $this->_validateToken();
        }
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function  isDeviceMobile(){

		if(CONFIG_IS_MOBILE == 1){
			return true;
		}else{
			return false;
		}
	}

	private function _validateToken() {
            if(isset($this->request->get['route'])) {
                $route = $this->request->get['route']; 
                $arr_route = explode("/", $route);
                if($arr_route[0] == 'seller_panel' || ($arr_route[0] == 'common' && $arr_route[1] == 'seller_home')) {
                    $this->response->redirect(SELLER_PANEL_LANDING_PAGE_URL);
                }
				
			    if($arr_route[0] == 'account' && $arr_route[1] != 'login' && $arr_route[1] != 'logout' && $arr_route[1] != 'credit_application' && $arr_route[1] && (isset($arr_route[2]) && $arr_route[2] != 'checkoutlogin')) {

                    if ((isset($this->session->data['ctoken']) && !isset($this->request->get['ctoken'])) || ((isset($this->request->get['ctoken']) && (isset($this->session->data['ctoken']) && ($this->request->get['ctoken'] != $this->session->data['ctoken']))))) {
                        $this->response->redirect($this->url->link('account/login', 'error=token is invalid', 'SSL'));
                    }
                 
                    
                }
            }
        }
    
    public function getCustomUrlFilters() {
            
        //@author : Amarat
        //Description : Change meta data for custom url 
        if(isset($this->session->data['is_custom']) && $this->session->data['is_custom'] == 1){ 
            $this->load->model('catalog/custom_url'); 
            $url_alias_id = $this->session->data['url_alias_id']; 
            $custom_url_data = $this->model_catalog_custom_url->getCustomUrlInfo($url_alias_id);
            //echo "<pre>"; print_r($custom_url_data); die;
            //set custom_url meta data
            $data['meta_data']['title'] = $custom_url_data['meta_title'];
            $data['meta_data']['description'] = $custom_url_data['meta_description'];
            $data['meta_data']['keywords'] = $custom_url_data['keyword'];
            
            //set deccription for custom URL
            $data['title'] = $custom_url_data['title'];
            $data['description'] = $custom_url_data['description'];
            $data['short_description'] = $custom_url_data['short_description'];
        }
        
        
        //@author : Amarat
        //Description : Change filter data for custom filter url 
        $data['filters'] = array();
        $data['price_filter'] = '';
        $data['options'] = array();
        $data['rating_filter'] = '';
        
        
        //Add condition for filter
        if(isset($this->session->data['is_custom'])){ 
            $this->load->model('catalog/custom_url'); 
            $url_alias_id = $this->session->data['url_alias_id']; 
            $custom_url_data = $this->model_catalog_custom_url->getCustomUrlInfo($url_alias_id);
            $custom_url = $custom_url_data['query'];
            
            //set default filter data
            //$data['price_filter'] = '';
            //$data['rating_filter'] = '';
            //$data['options'] = '';
            //$data['filters'] = '';
            $data['sort'] = 'sort_order';
            $data['order'] = 'ASC';
            $data['path'] = '';
            $data['search'] = '';
            $data['stock_filter'] = 0;
            $data['search_sale'] = 0;
            
            if (isset($this->request->get['path'])) {
                $data['path'] = $this->request->get['path'];
            } else {
                $data['path'] = '';
            }
                
            
            //echo $this->request->get['path']; die;
            if( isset($this->session->data['is_filter'])) {  
                
                $filters_data = explode('#!', $custom_url);
                //echo "<pre>"; print_r($filters_data); //die;
                $filters = explode("&", str_replace('amp;', '', $filters_data[1]));
                //echo "<pre>"; print_r($filters); //die;

                $data1 = array();
                $filtr = array();
                for($i = 0; $i < count($filters); $i++){
                    $filtr = explode('=', $filters[$i]);

                    //echo $i . "<pre>";  print_r($filtr);
                    //echo "<br/>";

                    if($filtr['0'] == 'filter'){ 
                        $data['filters'] = explode(",", $filtr['1']);
                    }
                    elseif ($filtr['0'] == 'price_filter') {
                        $data['price_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'order'){ 
                        $data['order'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'option') {
                        $data['options'] = explode(",", $filtr['1']);
                    }
                    if( $filtr['0'] == 'rating_filter') {
                        $data['rating_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'sort'){ 
                        $data['sort'] = $filtr['1']; 
                    }
                    if($filtr['0'] == 'search'){ 
                        $data['search'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'stock_filter'){
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['stock_filter'] = $filtr['1'];
                        }
                    }
                    if($filtr['0'] == 'clearance_sale'){ 
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['search_sale'] = $filtr['1'];
                        }
                    }
                    
                } 

            }else if( isset($this->session->data['is_search'])) {  
                $search_data = explode("&", str_replace('amp;', '', $custom_url));
                for($i = 0; $i < count($search_data); $i++){
                    $search = explode('=', $search_data[$i]);
                    for($j = 0; $j < count($search['1']); $j++){
                        if($search['0'] == 'search'){ 
                            $data['search'] = $search['1'];  
                        }
                    }
                }
            }else if( isset($this->session->data['is_search_with_filter'])) {
                
                //filter data for search with filters case
                $filters_data = explode('#!', $custom_url);
                //echo "<pre>"; print_r($filters_data); //die;
                $filters = explode("&", str_replace('amp;', '', $filters_data[1]));
                //echo "<pre>"; print_r($filters); //die;

                $data1 = array();
                $filtr = array();
                for($i = 0; $i < count($filters); $i++){
                    $filtr = explode('=', $filters[$i]);

                    //echo $i . "<pre>";  print_r($filtr);
                    //echo "<br/>";

                    if($filtr['0'] == 'filter'){ 
                        $data['filters'] = explode(",", $filtr['1']);
                    }
                    elseif ($filtr['0'] == 'price_filter') {
                        $data['price_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'order'){ 
                        $data['order'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'option') {
                        $data['options'] = explode(",", $filtr['1']);
                    }
                    if( $filtr['0'] == 'rating_filter') {
                        $data['rating_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'sort'){ 
                        $data['sort'] = $filtr['1']; 
                    }
                    if($filtr['0'] == 'search'){ 
                        $data['search'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'stock_filter'){
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['stock_filter'] = $filtr['1'];
                        }
                    }
                    if($filtr['0'] == 'clearance_sale'){ 
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['search_sale'] = $filtr['1'];
                        }
                    }

                }
                
                // search data for search_with_filter case
                if (strpos($custom_url, '#!') !== false) {
                    $pos = strpos($custom_url, '#!');
                    $custom_url = substr($custom_url, 0, $pos);
                }
                $search_data = explode("&", str_replace('amp;', '', $custom_url));
                for($i = 0; $i < count($search_data); $i++){
                    $search = explode('=', $search_data[$i]);
                    for($j = 0; $j < count($search['1']); $j++){
                        if($search['0'] == 'search'){ 
                            $data['search'] = $search['1'];  
                        }
                    }
                }
                
                
            }
            
            
        }
        return $data; 
    }
    
    
}
