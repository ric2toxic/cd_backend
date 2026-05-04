<?php
class ControllerCommonHeader extends Controller {
	public function index() {

		$this->load->model('tool/image');
        $data = array_merge($this->load->language('multiseller/multiseller'), isset($data) ? $data : array());
				
		$this->MsLoader->MsHelper->addStyle('multiseller');

		// note: renamed catalog
		$lang = "view/javascript/multimerch/datatables/lang/" . $this->config->get('config_language') . ".lng";
		$data['dt_language'] = file_exists(DIR_APPLICATION . $lang) ? "'catalog/$lang'" : "undefined";
        
        
		/*$this->document->addStyle('catalog/view/theme/default/stylesheet/jquery.fancybox.css');
		$this->document->addScript('catalog/view/theme/default/javascript/fancybox/jquery.fancybox.js');*/

		$data = array();
		$data['color_template'] = '';
		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}

		// Check whether store is single or wholesalesale
		if(isset($this->request->get['store']) && $this->request->get['store'] != ''){
			$this->redirectOnSingleStore();
		}

		if(!file_exists(DIR_TEMPLATE."default/stylesheet/colors/".$data['color_template']."/".$data['color_template'].".css")){
			$data['color_template'] = 'default';
		}
		$this->load->language('common/header');
		$this->load->language('common/search');

        $data['text_search'] = $this->language->get('text_search');

        if (isset($this->request->get['search'])) {
            $data['searchText'] = $this->request->get['search'];
        } else {
            $data['searchText'] = '';
        }

		$this->load->model('setting/store');
		//$data['store_switch'] = $this->model_setting_store->getStoreSwitch();
		// $this->setTheme();
		
		 $this->model_setting_store->getInternationalSwitch();
		
		$data['popup'] = false;
		if (isset($this->request->get['popup'])) {
			$data['popup'] = $this->request->get['popup'];
		}

        $data['social_meta_tags'] = $this->document->getSocialMetaTags();

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		$data['route'] = '';
		if(isset($this->request->get['route']) && $this->request->get['route'] != '') {
			$data['route'] = $this->request->get['route'];
		}
		$data['is_home'] = 0;

		if (!isset($this->request->get['route']) ) {
			$data['is_home'] = 0;
          
                $this->request->get['amp'] = 1;


		}
		$data['base'] = $server;
                
                //@author : Amarat
                //Description : Change meta data for custom url 

                //Add condition for meta_data
                if(isset($this->session->data['is_custom'])){ 
                    $this->load->model('catalog/custom_url'); 
                    $url_alias_id = $this->session->data['url_alias_id']; 
                    $custom_url_data = $this->model_catalog_custom_url->getCustomUrlInfo($url_alias_id);
                    //set custom_url meta data
                    $data['title'] = $custom_url_data['meta_title'];
                    $data['description'] = $custom_url_data['meta_description'];
                    $data['keywords'] = $custom_url_data['keyword'];
                }else{
                    //default meta data
                    $data['title'] = $this->document->getTitle();
                    $data['description'] = $this->document->getDescription();
                    $data['keywords'] = $this->document->getKeywords();
                }
                
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		$data['text_we_are_hiring'] = $this->language->get('text_we_are_hiring');


		if ($this->config->get('config_google_analytics_status')) {
			$data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		} else {
			$data['google_analytics'] = '';
		}

		$data['name'] = $this->config->get('config_name');


		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
		$data['logo'] = $this->model_tool_image->getOriginalImage($this->config->get('config_logo'));

		$data['sor_store_link_show'] = 1;

		if($this->config->get('config_store_id') != SOR_STORE_ID) {

			$data['store_id'] = $this->config->get('config_store_id');
			$data['text_store_link'] = $this->language->get('text_sor');

			$data['sor_store_link'] = SOR_STORE_URL;
			$data['mobile_logo'] = $this->model_tool_image->getOriginalImage('mobile_logo.png');
		}else{

			$data['store_id'] = $this->config->get('config_store_id');
			$data['text_store_link'] = $this->language->get('text_back_to_website');
			$data['sor_store_link'] = "http://".INDIA_STORE_HOST;
			$data['mobile_logo'] = $this->model_tool_image->getOriginalImage('sor_mobile_logo.png');
		}



         if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
         {
         	 $data['international_store'] = 1;
         }
         else
         {
       	   $data['international_store'] = 0;
         }
		//$data['referral'] = $this->url->link('common/header/referralUrl', '', 'SSL');
		$data['text_home'] = $this->language->get('text_home');
		// Get wishlist total
        $total = $this->customer->getTotalWishlists();
		$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $total);
		$data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
		$data['manufacturer'] = $this->language->get('manufacturer');
		$data['dashboard'] = $this->language->get('dashboard');
		$data['text_my_orders'] = $this->language->get('text_my_orders');

		$data['text_account'] = $this->language->get('text_account');
		$data['text_login_signup'] = $this->language->get('text_login_signup');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_all'] = $this->language->get('text_all');
        $data['text_single_store'] = $this->language->get('text_single_store');
        $data['text_wholesale_set_store'] = $this->language->get('text_wholesale_set_store');
		$data['scripts'] = $this->document->getScripts();

		$data['home'] = $this->url->link('common/home', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['register'] = $this->url->link('account/register', 'static=register', 'SSL');
		$data['login'] = $this->url->link('account/login', 'static=login', 'SSL');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['shopping_cart'] = $this->url->link('checkout/cart', '', 'SSL');
		$data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		$data['contact'] = $this->url->link('information/contact', '', 'SSL');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['cust_name'] = $this->customer->getFirstName();
		$data['careers'] = $this->url->link('careers/careers', 'static=careers', 'SSL');
		
		$data['manufacturer_link'] = $this->url->link('common/seller_home', '', 'SSL');
		$data['manufacturer_dashboard_link'] = $this->url->link('seller_panel/account-order', '', 'SSL');
		$data['seller_login'] = $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId());
		$status = true;

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", str_replace(array("\r\n", "\r"), "\n", trim($this->config->get('config_robots'))));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);
		//echo "<pre>"; print_r($categories); exit;
		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'], 'SSL'),
                        'name_for_id' => preg_replace('/\s+/', '-', strtolower($child['name']))

                    );
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'], 'SSL'),
                    'name_for_id' => preg_replace('/\s+/', '-', strtolower($category['name']))
				);
			}
		}

		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['search_mobile'] = $this->load->controller('common/search_mobile');
		$data['cart'] = $this->load->controller('common/cart');


		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}

		$menus = $this->model_catalog_category->menus(1,0);
		

		if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
        {
           $menus = $this->model_catalog_category->getMenu('Mobile-International');
        }
        else
        {
           $menus = $this->model_catalog_category->getMenu('Mobile');
        }



		if(isset($menus) && !empty($menus)){
			foreach($menus as $menu_categories){
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->menus(1,$menu_categories['id']);

				foreach ($children as $child) {
					
					if($child['link_type']=='megamenu'){
						$child_href = $child['megamenu'];
						$children_data[] = array(
							'name'  =>  html_entity_decode($child_href),
							'href'  => 'megamenu'
						);
					}else{
						if($child['link_type']=='category'){
							$child_href = $this->url->link('product/category', 'path=' . $menu_categories['value'] . '_' . $child['value'], 'SSL');
						}else if($child['link_type']=='page'){
							$child_href = $this->url->link('information/information', 'information_id=' . $child['value'], 'SSL');
						}else{
							$child_href = $child['value'];
						}
						$children_data[] = array(
							'name'  => $child['link_title'],
							'href'  => $child_href,
                            'name_for_id' => preg_replace('/\s+/', '-', strtolower($child['link_title']))
						);
					}
				}

				if($menu_categories['link_type']=='category'){
					$parent_href = $this->url->link('product/category', 'path=' . $menu_categories['value'], 'SSL');
				}else if($menu_categories['link_type']=='page'){
					$parent_href = $this->url->link('information/information', 'information_id=' . $menu_categories['value'], 'SSL');
				}else{
					$parent_href = $menu_categories['value'];
				}
				// Level 1
				$data['menus'][] = array(
						'name'     => $menu_categories['link_title'],
						'children' => $children_data,
						'href'     => $parent_href,
                        'name_for_id' => preg_replace('/\s+/', '-', strtolower($menu_categories['link_title']))
				);
			}	
		}
        
		if($data['popup'] == true){
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header-popup.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/common/header-popup.tpl', $data);
			} else {
				return $this->load->view('default/template/common/header-popup.tpl', $data);
			}
		}else { 

			if(isset($this->request->get['amp']) && $this->request->get['amp'] == 1) {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header-amp.tpl')) {
					return $this->load->view($this->config->get('config_template') . '/template/common/header-amp.tpl', $data);
				} else {
					return $this->load->view('default/template/common/header-amp.tpl', $data);
				}
			}else{
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
					return $this->load->view($this->config->get('config_template') . '/template/common/header.tpl', $data);
				} else {
					return $this->load->view('default/template/common/header.tpl', $data);
				}
			}
		}
	}
	/**
	 *
	 */
	private function setTheme(){
		//$store_id = $this->config->get('config_store_id');

		//include $_SERVER['DOCUMENT_ROOT'].'/Mobile_Detect.php';
		//$detect = new Mobile_Detect();
		//&& $this->config->get('config_store_id')!=1
		//$data['device'] = '';
		//if ($detect->isMobile() && !$detect->isTablet() ){
		//	$this->config->set('config_template', 'wsbmobile');
		//}
	}


	/**
	 * displaying singles on the same store
	 * @author Parth Gupta
	 * @dateTime 2016-01-20T16:36:08+0530
	 * @return json  called by ajax
	 */
	public function getStoreSwitchNew(){
		$this->load->model('setting/store');
		// $storenew =  $this->model_setting_store->getStoreSwitchNew();
		// echo json_encode("success");

		if (isset($this->session->data['custom_store']) && !empty($this->session->data['custom_store'])) {
			if ($this->session->data['custom_store'] == 'single') {

				$this->session->data['custom_store'] = 'set';
					$data['store_switch'] = array(
							'label'	   => $this->language->get('text_single_store')
					);

			}
			elseif ($this->session->data['custom_store'] == 'set') {
				$this->session->data['custom_store'] ='single'	;

					$data['store_switch'] = array(
				 			// 'url'      => $result['url'].$url_path.$build_query_string,
							'label'	   => $this->language->get('text_wholesale_set_store')
					);

			}
		}
		else {
			$this->session->data['custom_store'] ='single';
			$data['store_switch'] = array(
				// 'url'      => $result['url'].$url_path.$build_query_string,
					'label'	   => $this->language->get('text_single_store')
			);
		}

		// return $data['store_switch'];
		echo json_encode("success");
	}

	public function redirectOnSingleStore(){
		$this->session->data['custom_store'] ='single'	;

		$data['store_switch'] = array(
			// 'url'      => $result['url'].$url_path.$build_query_string,
				'label'	   => $this->language->get('text_wholesale_set_store')
		);


		header('Location: '.str_replace("store=single", "", $_SERVER['REQUEST_URI']));
		exit;


	}
	public function referralUrl(){
		$data = array();
		$data['header'] = $this->load->controller('common/header');
		$data['refer_eran'] = $this->language->get('refer_eran');

		$data['footer'] = $this->load->controller('common/footer');
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/referral.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/referral.tpl', $data));
		}

	}


	/********** add app link by vikas (08-02-2016)**********************/
	public function app_link(){
		//echo "<pre>";print_r($this->request->post['app_data']); echo "</pre>";die;
		if(!empty($this->request->post['app_data']) && isset($this->request->post['app_data'])){
			$app_data = $this->request->post['app_data'];
			setcookie("$app_data", $app_data, time() + (86400 * 1), "/",".".HTTP_DOMAIN ); // 86400 = 1 day
			echo "success"; exit;
		}

	}
	/********************************/
}
