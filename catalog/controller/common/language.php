<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['text_language'] = $this->language->get('text_language');
        $data['text_change_language'] = $this->language->get('text_change_language');
		$data['action'] = $this->url->link('common/language/language', '', 'SSL');

		$data['code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);
			}
		}

		if (!isset($this->request->get['route'])) {
			$data['redirect'] = $this->url->link('common/home');
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['redirect'] = $this->url->link($route, $url, 'SSL');
		}
       if(isset($this->request->get['amp']) && $this->request->get['amp'] == 1) {
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/language-amp.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/language-amp.tpl', $data);
			} else {
				return $this->load->view('default/template/common/language-amp.tpl', $data);
			}
		}else{
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/language.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/common/language.tpl', $data);
			} else {
				return $this->load->view('default/template/common/language.tpl', $data);
			}
		}
	}

	public function language() {
		if (isset($this->request->request['code'])) {
			$this->session->data['language'] = $this->request->request['code'];
		}

		if (isset($this->request->request['redirect'])) {
			$this->response->redirect($this->request->request['redirect']);
		} else {
			$this->response->redirect($this->url->link('common/home', '', 'SSL'));
		}
	}
}