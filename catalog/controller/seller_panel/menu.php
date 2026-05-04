<?php
class ControllerSellerMenu extends ControllerSellerAccount {
	private $error = array();

	public function index() {
		$this->load->language('seller/menu');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('seller/menu');
		$this->getList();
	}
/*
	public function delete() {
		$this->load->language('seller/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/menu');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menu_id) {
				$this->model_seller_menu->deleteMenu($menu_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('seller/menu', $url, 'SSL'));
		}

		$this->getList();
	}
*/
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

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('seller/account-profile', '' , 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('seller/menu', $url, 'SSL')
		);

		$data['add'] = $this->url->link('seller/menu/add', $url , 'SSL');
		$data['delete'] = $this->url->link('seller/menu/delete', $url, 'SSL');

		$data['menus'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		//print_r($store_id); die;
		$menu_total = $this->model_seller_menu->getTotalMenus();

		$results = $this->model_seller_menu->getMenus($filter_data);

		foreach ($results as $result) {
			$data['menus'][] = array(
				'id' => $result['id'],
				'name'      => $result['name'],
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'edit'      => $this->url->link('seller/menu/getForm', 'menu_id=' . $result['id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('seller/menu', $url, 'SSL');
		$data['sort_status'] = $this->url->link('seller/menu', $url, 'SSL');

		$url = '';

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
		$pagination->url = $this->url->link('seller/menu', $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($menu_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($menu_total - $this->config->get('config_limit_admin'))) ? $menu_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $menu_total, ceil($menu_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header_seller'] = $this->load->controller('common/seller_header');
		$data['footer_seller'] = $this->load->controller('common/seller_footer');
		$this->response->setOutput($this->load->view('default/template/multiseller/menu_list.tpl', $data));
	}

	public function getForm() {

		$this->load->language('seller/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/menu');

		$data['storeid'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
		$seller_store_id =array();
		$data['count']	=	0;
		foreach($data['storeid'] as $value){
			$seller_store_id[] = $value['store_id'];
			$data['stores'][] = $this->MsLoader->MsProduct->getStore($value['store_id']);
            $data['count']++;
		}

		$this->load->language('seller/account-profile');
		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}else{
			$data['strs'] = $data['storeid'];
			$store_id = $data['strs'][0]['store_id'];
			$this->session->data['seller_store_id'] = $store_id;
		}
		$data['select_seller_store_id'] = $store_id;

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_default'] = $this->language->get('text_default');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('Save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_menu_add'] = $this->language->get('button_add');
		$data['button_remove'] = $this->language->get('button_remove');

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


		$url = '';

		$getMenu_name = $this->model_seller_menu->getMenu_name($this->request->get['menu_id']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('seller/account-profile', '' , 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('seller/menu', $url, 'SSL')
		);

		$data['breadcrumbs'][] = array(
				'text' => $getMenu_name,
				'href' => $this->url->link('seller/menu/getForm&menu_id='.$this->request->get['menu_id'], $url, 'SSL')
		);

		$data['cancel'] = $this->url->link('seller/menu', $url, 'SSL');
		$this->load->model('catalog/category');
		//$this->load->model('catalog/information');
		$this->load->model('seller/menu');
		if (isset($this->request->get['menu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			//$arr_menu_info = $this->model_seller_menu->getMenu($this->request->get['menu_id'], $store_id);
			$arr_menu_info = $this->model_seller_menu->getMenu($this->request->get['menu_id'], $data['select_seller_store_id']);
			//$i = 0;
			$menus = array();
			$res = array();
			foreach ($arr_menu_info as $arr_menu_detail) {

				if ($arr_menu_detail['link_type'] == 'category' && $arr_menu_detail['value'] != '') {
					$category_info = $this->model_catalog_category->getCategory($arr_menu_detail['value']);
					$arr_menu_detail['value_label'] = $category_info['name'];
				} elseif ($arr_menu_detail['link_type'] == 'page' && $arr_menu_detail['value'] != '') {
					//$page_info = $this->model_catalog_information->getPage($arr_menu_detail['value']);
					$page_info = $this->model_seller_menu->getPage($arr_menu_detail['value'],$data['select_seller_store_id']);
					$arr_menu_detail['value_label'] =  $page_info['title'];
				}else {
					$arr_menu_detail['value_label'] = $arr_menu_detail['value'];
				}
//				$data['menu_info'][$i] = $arr_menu_detail;
//				$i++;

				if($arr_menu_detail['parent_id'] == 0) {
					$menus['parent'][] = $arr_menu_detail;
				}else{
					$menus['child'][$arr_menu_detail['parent_id']][] = $arr_menu_detail;
				}
			}

			if(isset($menus['parent'])) {
				foreach ($menus['parent'] as $rs) {
					if (isset($menus['child'][$rs['id']])) {
						$child = $menus['child'][$rs['id']];
						$rs['child'] = $child;
					}
					$res[] = $rs;
				}
			}
			//echo "<pre>"; print_r($res); exit;
		}

		$data['menu_info'] = $res;

		//echo '<pre>';print_r($data['menu_info']);echo '</pre>'; die;
		$data['header_seller'] = $this->load->controller('common/seller_header');
		$data['footer_seller'] = $this->load->controller('common/seller_footer');

		$this->response->setOutput($this->load->view('default/template/multiseller/menu_form.tpl', $data));
	}

	public function edit() {
		$this->load->model('seller/menu');
		//echo "<pre>"; print_r($this->request->post); echo "</pre>";
		$this->model_seller_menu->saveMenu($this->request->post);
		$menu_id = $this->request->get['menu_id'];
		$this->response->redirect($this->url->link('seller/menu/getForm', 'menu_id='.$menu_id , 'SSL'));
	}

	public function deleteMenuAll(){
		$this->load->model('seller/menu');
		$parent_menu_item_id = $this->request->post['parent_menu_item_id'];
		$store_id= $this->request->post['store_id'];

		$getAllMenu= count($this->model_seller_menu->fetchMenuByParentMenuID($parent_menu_item_id,$store_id));

		if($getAllMenu > 0){
			$this->model_seller_menu->deleteAllMenus($parent_menu_item_id);
		}else{
			$this->model_seller_menu->deleteChildMenus($parent_menu_item_id);
		}
	}
}