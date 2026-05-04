<?php
class ControllerCommonMaintenance extends Controller {
	public function index() {
		if ($this->config->get('config_maintenance')) {
			$route = '';

			if (isset($this->request->get['route'])) {
				$part = explode('/', $this->request->get['route']);

				if (isset($part[0])) {
					$route .= $part[0];
				}
			}

			// Show site if logged in as admin
			$this->load->library('user');

			$this->user = new User($this->registry);

			if (($route != 'payment' && $route != 'api') && !$this->user->isLogged()) {
				return new Action('common/maintenance/info');
			}
		}
	}

	public function info() {

        $data = array();

        $this->load->language('common/maintenance');
        
        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));


        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		

		if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			$this->response->addHeader('HTTP/1.1 503 Service Unavailable');
		} else {
			$this->response->addHeader('HTTP/1.0 503 Service Unavailable');
		}

		$this->response->addHeader('Retry-After: 3600');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_maintenance'),
			'href' => $this->url->link('common/maintenance')
		);

		$data['message'] = $this->language->get('text_message');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/maintenance.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/maintenance.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/maintenance.tpl', $data));
		}
	}
}
