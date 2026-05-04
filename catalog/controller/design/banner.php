<?php
class ControllerDesignBanner extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		$this->getList();
	}

	public function add() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate())) {
			//echo "<pre>"; print_r($this->request->post); die;
			$this->model_design_banner->addBanner($this->request->post);

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

			$this->response->redirect($this->url->link('design/banner', 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') &&  $this->validate()) {

			$this->model_design_banner->editBanner($this->request->get['banner_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('design/banner', 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $banner_id) {
				$this->model_design_banner->deleteBanner($banner_id);
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

			$this->response->redirect($this->url->link('design/banner', 'SSL'));
		}

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
			'href' => $this->url->link('common/dashboard', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/banner', 'SSL')
		);

		$data['add'] = $this->url->link('design/banner/add', 'SSL');
		$data['delete'] = $this->url->link('design/banner/delete', 'SSL');

		$data['banners'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$data['strs'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());


		$data['count']	=	0;
		foreach ($data['strs'] as $st) {
			$data['stores'][] = $this->MsLoader->MsProduct->getStore($st['store_id']);
			$data['count']++;
		}
		//echo "<pre>"; print_r($data['stores']); die;
		$banner_total = $this->model_design_banner->getTotalBanners();

		$results = $this->model_design_banner->getBanners($filter_data);
		//echo "<pre>"; print_r($results); die;
		foreach ($results as $result) {
			if($result['store_id'] !=0) {
				$data['banners'][] = array(
					'banner_id' => $result['banner_id'],
					'name' => $result['name'],
					'status' => ($result['status'] ? $this->language->get('Enabled') : $this->language->get('Disabled')),
					'edit' => $this->url->link('design/banner/edit', '&banner_id=' . $result['banner_id'] . $url, 'SSL')
				);
			}
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

		$data['sort_name'] = $this->url->link('design/banner', '&sort=name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('design/banner', '&sort=status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $banner_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('design/banner', '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($banner_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($banner_total - $this->config->get('config_limit_admin'))) ? $banner_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $banner_total, ceil($banner_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/seller_header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/seller_footer');

		$this->response->setOutput($this->load->view('default/template/design/banner_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['banner_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('Enabled');
		$data['text_disabled'] = $this->language->get('Disabled');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_width'] = $this->language->get('text_width');
		$data['text_height'] = $this->language->get('text_height');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_banner_width'] = $this->language->get('entry_banner_width');
		$data['entry_banner_height'] = $this->language->get('entry_banner_height');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_banner_add'] = $this->language->get('button_banner_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$store_id = (int)($this->config->get('config_store_id'));
		//echo $store_id; die;


		$data['strs'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
		//echo "<pre>"; print_r($data['strs']); die;

		$data['count']	=	0;
		foreach ($data['strs'] as $st) {
			$data['stores'][] = $this->MsLoader->MsProduct->getStore($st['store_id']);
			$data['count']++;
		}
		//echo "<pre>"; print_r($data['stores']); die;
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

		if (isset($this->error['select_store'])) {
			$data['error_select_store'] = $this->error['select_store'];
		} else {
			$data['error_select_store'] = '';
		}

		if (isset($this->error['banner_position'])) {
			$data['error_banner_position'] = $this->error['banner_position'];
		} else {
			$data['error_banner_position'] = '';
		}

		if (isset($this->error['banner_width'])) {
			$data['error_banner_width'] = $this->error['banner_width'];
		} else {
			$data['error_banner_width'] = '';
		}

		if (isset($this->error['banner_height'])) {
			$data['error_banner_height'] = $this->error['banner_height'];
		} else {
			$data['error_banner_height'] = '';
		}

		if (isset($this->error['banner_image'])) {
			$data['error_banner_image'] = $this->error['banner_image'];
		} else {
			$data['error_banner_image'] = array();
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
			'href' => $this->url->link('common/dashboard', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/banner', 'SSL')
		);

		if (!isset($this->request->get['banner_id'])) {
			$data['action'] = $this->url->link('design/banner/add', 'SSL');
		} else {
			$data['action'] = $this->url->link('design/banner/edit', '&banner_id=' . $this->request->get['banner_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('design/banner', 'SSL');

		if (isset($this->request->get['banner_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$banner_info = $this->model_design_banner->getBannerNew($this->request->get['banner_id']);
		}

		$data['token'] = $this->session->data['token'];
		//echo "<pre>"; print_r($banner_info); die;
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($banner_info)) {
			$data['name'] = $banner_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($banner_info)) {
			$data['status'] = $banner_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['store_id'])) {
			$data['store_id'] = $this->request->post['store_id'];
		} elseif (!empty($banner_info)) {
			$data['store_id'] = $banner_info['store_id'];
		} else {
			$data['store_id'] = '';
		}

		if (isset($this->request->post['banner_position'])) {
			$data['banner_position'] = $this->request->post['banner_position'];
		} elseif (!empty($banner_info)) {
			$data['banner_position'] = $banner_info['positions'];
		} else {
			$data['banner_position'] = '';
		}

		if (isset($this->request->post['banner_width'])) {
			$data['banner_width'] = $this->request->post['banner_width'];
		} elseif (!empty($banner_info)) {
			$data['banner_width'] = $banner_info['width'];
		} else {
			$data['banner_width'] = '';
		}

		if (isset($this->request->post['banner_height'])) {
			$data['banner_height'] = $this->request->post['banner_height'];
		} elseif (!empty($banner_info)) {
			$data['banner_height'] = $banner_info['height'];
		} else {
			$data['banner_height'] = '';
		}

		$data['positions'] = array(
			array(
			'value' => 	'homepage_slideshow',
			'position' => 'Homepage Slider'
			)
		);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (isset($this->request->post['banner_image'])) {
			$banner_images = $this->request->post['banner_image'];
		} elseif (isset($this->request->get['banner_id'])) {
			$banner_images = $this->model_design_banner->getBannerImages($this->request->get['banner_id']);
		} else {
			$banner_images = array();
		}

		$data['banner_images'] = array();

		foreach ($banner_images as $banner_image) {
			//if (is_file(DIR_IMAGE . $banner_image['image'])) {
				$image = $banner_image['image'];
				$thumb = $banner_image['image'];
			//} else {
				//$image = '';
				//$thumb = 'no_image.png';
			//}

			$data['banner_images'][] = array(
				'banner_image_description' => $banner_image['banner_image_description'],
				'link'                     => $banner_image['link'],
				'image'                    => $image,
				'thumb'                    => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order'               => $banner_image['sort_order']
			);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/seller_header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/seller_footer');
		//echo '<pre>'; print_r($data); echo '</pre>';
		$this->response->setOutput($this->load->view('default/template/design/banner_form.tpl', $data));
	}

	public function validate(){
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		if ($this->request->post['store_id'] == 0 || $this->request->post['name'] == '') {
			$this->error['select_store'] = $this->language->get('error_select_store');
		}
		if ($this->request->post['banner_position'] == '') {
			$this->error['banner_position'] = $this->language->get('error_banner_position');
		}

		if ($this->request->post['banner_width'] == '') {
			$this->error['banner_width'] = $this->language->get('error_banner_width');
		}

		if ($this->request->post['banner_height'] == '') {
			$this->error['banner_height'] = $this->language->get('error_banner_height');
		}

		if (isset($this->request->post['banner_image'])) {
			foreach ($this->request->post['banner_image'] as $banner_image_id => $banner_image) {
				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					if ((utf8_strlen($banner_image_description['title']) < 2) || (utf8_strlen($banner_image_description['title']) > 64)) {
						$this->error['banner_image'][$banner_image_id][$language_id] = $this->language->get('error_title');
					}
				}
			}
		}

		return !$this->error;
	}

}