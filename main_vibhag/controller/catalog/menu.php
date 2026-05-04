<?php
class ControllerCatalogMenu extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/menu');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('seller/menu', 'frontend');
		$this->getList();
	}

	protected function getList() {

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '&token=' . $this->session->data['token'];

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
        
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('catalog/menu', $data);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('catalog/menu', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['add'] = $this->url->link('catalog/menu/add', $url , 'SSL');
		$data['delete'] = $this->url->link('catalog/menu/delete', $url, 'SSL');

		$data['menus'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		//print_r($store_id); die;
		$menu_total = $this->frontend_model_seller_menu->getTotalMenus();

		$results = $this->frontend_model_seller_menu->getMenus($filter_data);

		foreach ($results as $result) {
			$data['menus'][] = array(
				'id' => $result['id'],
				'name'      => $result['name'],
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'edit'      => $this->url->link('catalog/menu/getParentForm', 'menu_id=' . $result['id'] . $url, 'SSL')
			);
		}

		if (isset($this->error['warning'])) {
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

		$url = '&token=' . $this->session->data['token'];

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/menu', $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/menu', $url, 'SSL');

		$url = '&token=' . $this->session->data['token'];

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $menu_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/menu', $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($menu_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($menu_total - $this->config->get('config_limit_admin'))) ? $menu_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $menu_total, ceil($menu_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');
		$this->response->setOutput($this->load->view('catalog/menu_list.tpl', $data));
	}


	public function getParentForm() {

		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('catalog/menu', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('seller/menu', 'frontend');
		$this->load->model('catalog/menu');

		$data['stores'] = 0; 

		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = 0; 
		}else{
			$store_id = $data['stores'];
			$this->session->data['seller_store_id'] = 0;
		}
		$data['select_seller_store_id'] = $store_id;

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}




		$url = 'token=' . $this->session->data['token'];

		$getMenu_detail = $this->frontend_model_seller_menu->getMenu_detail($this->request->get['menu_id']);
		$getMenu_name = $getMenu_detail['name'];
		$data['select_seller_store_id'] = $getMenu_detail['store_id'];
	
		if(isset($this->request->post['menu_store_language'])){
			$data['menu_store_language'] = $this->request->post['menu_store_language'];
		}else{
			$data['menu_store_language'] = 1;
		}
		$data['get_language'] = $this->model_catalog_menu->getLanguage();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', $url, 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('catalog/menu', $url, 'SSL')
		);

		$data['breadcrumbs'][] = array(
				'text' => $getMenu_name,
				'href' => $this->url->link('catalog/menu/getForm&menu_id='.$this->request->get['menu_id'], $url, 'SSL')
		);

		$data['cancel'] = $this->url->link('catalog/menu', $url, 'SSL');
		$this->load->model('catalog/category', 'frontend');
		//$this->load->model('catalog/information');
		$this->load->model('seller/menu', 'frontend');
		if (isset($this->request->get['menu_id'])) {
			$arr_menu_info = $this->model_catalog_menu->getMenu($this->request->get['menu_id'], $data['select_seller_store_id'], $data['menu_store_language']);
			$menus = array();
			$res = array();
			foreach ($arr_menu_info as $arr_menu_detail) {

				if ($arr_menu_detail['link_type'] == 'category' && $arr_menu_detail['value'] != '') {
					$category_info = $this->frontend_model_catalog_category->getCategory($arr_menu_detail['value']);
						$arr_menu_detail['value_label']='';
						if(isset($category_info['name'])){
							$arr_menu_detail['value_label'] = $category_info['name'];
						}
					
				} elseif ($arr_menu_detail['link_type'] == 'page' && $arr_menu_detail['value'] != '') {
					//$page_info = $this->model_catalog_information->getPage($arr_menu_detail['value']);
					$page_info = $this->model_catalog_menu->getPage($arr_menu_detail['value'],$data['select_seller_store_id']);
					$arr_menu_detail['value_label'] =  $page_info['title'];
				}else {
					$arr_menu_detail['value_label'] = $arr_menu_detail['value'];
				}

                $arr_menu_detail['add_child'] = $this->url->link('catalog/menu/getForm', 'menu_id=' . $this->request->get['menu_id']. '&parent_id=' . $arr_menu_detail['id'] .'&'. $url, 'SSL');

				if($arr_menu_detail['parent_id'] == 0) {
					$menus['parent'][] = $arr_menu_detail;
				}
			}
		}

		if(isset($menus['parent']))
		{
			$data['menu_info'] = $menus['parent'];
		}else{
			$data['menu_info'] = '';	
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('catalog/menu_parent_form.tpl', $data));
	}


	public function getForm() {

		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('catalog/menu', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('seller/menu', 'frontend');
		$this->load->model('catalog/menu');

		$data['stores'] = 0; 

		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = 0; 
		}else{
			$store_id = $data['stores'];
			$this->session->data['seller_store_id'] = 0;
		}
		$data['select_seller_store_id'] = $store_id;

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}


		$url = 'token=' . $this->session->data['token'];

		$getMenu_detail = $this->frontend_model_seller_menu->getMenu_detail($this->request->get['menu_id']);
		$getMenu_name = $getMenu_detail['name'];
		$data['select_seller_store_id'] = $getMenu_detail['store_id'];

		if(isset($this->request->post['menu_store_language'])){
			$data['menu_store_language'] = $this->request->post['menu_store_language'];
		}else{
			$data['menu_store_language'] = 1;
		}
		$data['get_language'] = $this->model_catalog_menu->getLanguage();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', $url, 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('catalog/menu', $url, 'SSL')
		);

		$data['breadcrumbs'][] = array(
				'text' => $getMenu_name,
				'href' => $this->url->link('catalog/menu/getForm&menu_id='.$this->request->get['menu_id'], $url, 'SSL')
		);

		$data['cancel'] = $this->url->link('catalog/menu/getParentForm', 'menu_id=' . $this->request->get['menu_id'] .'&'. $url, 'SSL');
		$this->load->model('catalog/category', 'frontend');
		//$this->load->model('catalog/information');
		$this->load->model('seller/menu', 'frontend');
		if (isset($this->request->get['menu_id'])) {
			$arr_menu_info = $this->model_catalog_menu->getMenu($this->request->get['menu_id'], $data['select_seller_store_id'], $data['menu_store_language']);
			$menus = array();
			$res = array();
			foreach ($arr_menu_info as $arr_menu_detail) {

				if ($arr_menu_detail['link_type'] == 'category' && $arr_menu_detail['value'] != '') {
					$category_info = $this->frontend_model_catalog_category->getCategory($arr_menu_detail['value']);
					$arr_menu_detail['value_label']='';
						if(isset($category_info['name'])){
							$arr_menu_detail['value_label'] = $category_info['name'];
						}
				} elseif ($arr_menu_detail['link_type'] == 'page' && $arr_menu_detail['value'] != '') {
					//$page_info = $this->model_catalog_information->getPage($arr_menu_detail['value']);
					$page_info = $this->model_catalog_menu->getPage($arr_menu_detail['value'],$data['select_seller_store_id']);
					$arr_menu_detail['value_label'] =  $page_info['title'];
				}else {
					$arr_menu_detail['value_label'] = $arr_menu_detail['value'];
				}
 
				if($arr_menu_detail['parent_id'] == 0) {
					$menus['parent'][] = $arr_menu_detail;
				}else{
					$menus['child'][$arr_menu_detail['parent_id']][] = $arr_menu_detail;
				}
			}


			if(isset($menus['parent'])) {
				foreach ($menus['parent'] as $rs) {

                if($rs['id'] == $this->request->get['parent_id'])
                {	
					if (isset($menus['child'][$rs['id']])) {
						$child = $menus['child'][$rs['id']];
						$rs['child'] = $child;
					}
					
                  if(isset($rs['child']))
                  {   
					foreach ($rs['child'] as $key => $sub_child) {
					if (isset($menus['child'][$sub_child['id']])) {

						$child2 = $menus['child'][$sub_child['id']];
						$rs['child'][$key]['child'] = $child2;
					}

					if(isset($rs['child'][$key]['child']))
					{
					  foreach ($rs['child'][$key]['child'] as $key3 => $sub_child3) {
					  if (isset($menus['child'][$sub_child3['id']])) {

						$child3 = $menus['child'][$sub_child3['id']];
						$rs['child'][$key]['child'][$key3]['child'] = $child3;
					 }}
					} 
					
				   }
                   }
				   $res[] = $rs;
                  }
				}
			}
		}
		if(isset($res) && !empty($res)){
			$data['menu_info'] = $res;
		}else{
			$data['menu_info'] = '';	
		}


		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('catalog/menu_form.tpl', $data));
	}

	public function edit() {

		$this->load->model('catalog/menu');
		$this->load->language('catalog/menu');
		$this->model_catalog_menu->saveMenu($this->request->post);
		$menu_id = $this->request->get['menu_id'];
	    $this->session->data['success'] = $this->language->get('text_success');
		$this->response->redirect($this->url->link('catalog/menu/getParentForm', 'token=' . $this->session->data['token'] . '&menu_id='.$menu_id , 'SSL'));
	}
	public function menu_store_status() {
		$this->load->model('catalog/menu');
		$menu_status = $this->request->post['menu_status'];
		$menu_lang = $this->request->post['menu_lang'];
		$this->model_catalog_menu->menu_store_status($menu_status, $menu_lang);
	}
	
	public function deleteMenuAll(){
		$this->load->model('seller/menu', 'frontend');
		$parent_menu_item_id = $this->request->post['parent_menu_item_id'];
		$store_id= $this->request->post['store_id'];

		$getAllMenu= count($this->frontend_model_seller_menu->fetchMenuByParentMenuID($parent_menu_item_id,$store_id));

		if($getAllMenu > 0){
			$this->frontend_model_seller_menu->deleteAllMenus($parent_menu_item_id);
		}else{
			$this->frontend_model_seller_menu->deleteChildMenus($parent_menu_item_id);
		}
	}

	public function autocompleteCategory() {
		$json = array();

		$store_id = $this->request->get['store_id'];

		if (isset($this->request->get['filter_name'])) {
			//$this->load->model('seller/coupon', 'frontend');
                        $this->load->model('catalog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC'
			);

			//$results = $this->frontend_model_seller_coupon->getCategories($filter_data, $store_id);
			$results = $this->model_catalog_category->getCategories($filter_data, $store_id);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// get show all pages in dropdown at seller panel
	public function autoComplatePages(){

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/menu');

			$filter_data = array(
					'filter_name' => $this->request->get['filter_name'],
					'sort'        => 'name',
					'order'       => 'ASC',
				//'start'       => 0,
				//'limit'       => 5
			);

			$results = $this->model_catalog_menu->getPages($filter_data);

			foreach ($results as $result) {
				$json[] = array(
						'category_id' => $result['information_id'],
						'name'        => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}