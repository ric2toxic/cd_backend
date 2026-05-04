<?php
class ControllerErrorNotFound extends Controller {
	public function index() {
		$this->load->language('error/not_found');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (isset($this->request->get['route'])) {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($route, $url, $this->request->server['HTTPS'])
			);
		}

		//dynamic parent category by vikas (06-02-2016)
		$get_Parent_category = $this->model_catalog_category->getCategories(0);

		foreach($get_Parent_category as $category_parent){
			$data['category_parent'][] = array(
				'category_id'	=>	$category_parent['category_id'],
				'name'	=> $category_parent['name'],
				'href'     => $this->url->link('product/category', 'path=' . $category_parent['category_id'])
			);
		}
		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
						'title' => $result['title'],
						'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}
		$data['title'] = $this->document->getTitle();
		$this->document->setTitle($this->language->get('heading_title'));
		$data['base'] = $server;
		$data['heading_title'] = $this->language->get('heading_title');
		$data['home_page'] = $this->url->link('common/home');
		$data['text_error'] = $this->language->get('text_error');

		$data['button_continue'] = $this->language->get('button_continue');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
		}
	}
}