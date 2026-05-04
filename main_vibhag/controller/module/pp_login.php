<?php
class ControllerModulePPLogin extends Controller {
	private $error = array();

	public function index() {
		//$this->language->load('module/pp_login');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('module/pp_login', $data);

		$this->load->model('setting/setting');

		$this->document->setTitle($data['heading_title']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_login', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['client_id'])) {
			$data['error_client_id'] = $this->error['client_id'];
		} else {
			$data['error_client_id'] = '';
		}

		if (isset($this->error['secret'])) {
			$data['error_secret'] = $this->error['secret'];
		} else {
			$data['error_secret'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['text_module'],
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('module/pp_login', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('module/pp_login', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_login_client_id'])) {
			$data['pp_login_client_id'] = $this->request->post['pp_login_client_id'];
		} else {
			$data['pp_login_client_id'] = $this->config->get('pp_login_client_id');
		}

		if (isset($this->request->post['pp_login_secret'])) {
			$data['pp_login_secret'] = $this->request->post['pp_login_secret'];
		} else {
			$data['pp_login_secret'] = $this->config->get('pp_login_secret');
		}

		if (isset($this->request->post['pp_login_sandbox'])) {
			$data['pp_login_sandbox'] = $this->request->post['pp_login_sandbox'];
		} else {
			$data['pp_login_sandbox'] = $this->config->get('pp_login_sandbox');
		}

		if (isset($this->request->post['pp_login_debug'])) {
			$data['pp_login_debug'] = $this->request->post['pp_login_debug'];
		} else {
			$data['pp_login_debug'] = $this->config->get('pp_login_debug');
		}

		if (isset($this->request->post['pp_login_button_colour'])) {
			$data['pp_login_button_colour'] = $this->request->post['pp_login_button_colour'];
		} elseif ($this->config->get('pp_login_button_colour')) {
			$data['pp_login_button_colour'] = $this->config->get('pp_login_button_colour');
		} else {
			$data['pp_login_button_colour'] = 'blue';
		}

		if (isset($this->request->post['pp_login_seamless'])) {
			$data['pp_login_seamless'] = $this->request->post['pp_login_seamless'];
		} else {
			$data['pp_login_seamless'] = $this->config->get('pp_login_seamless');
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['locales'] = array();

		$data['locales'][] = array(
			'value' => 'en-gb',
			'text' => 'English (Great Britain)'
		);

		$data['locales'][] = array(
			'value' => 'zh-cn',
			'text' => 'Chinese (People\'s Republic of China)'
		);

		$data['locales'][] = array(
			'value' => 'zh-hk',
			'text' => 'Chinese (Hong Kong)',
		);

		$data['locales'][] = array(
			'value' => 'zh-tw',
			'text' => 'Chinese (Taiwan)'
		);

		$data['locales'][] = array(
			'value' => 'zh-xc',
			'text' => 'Chinese (US)'
		);

		$data['locales'][] = array(
			'value' => 'da-dk',
			'text' => 'Danish'
		);

		$data['locales'][] = array(
			'value' => 'nl-nl',
			'text' => 'Dutch'
		);

		$data['locales'][] = array(
			'value' => 'en-au',
			'text' => 'English (Australia)'
		);

		$data['locales'][] = array(
			'value' => 'en-us',
			'text' => 'English (US)',
		);

		$data['locales'][] = array(
			'value' => 'fr-fr',
			'text' => 'French'
		);

		$data['locales'][] = array(
			'value' => 'fr-ca',
			'text' => 'French (Canada)'
		);

		$data['locales'][] = array(
			'value' => 'fr-xc',
			'text' => 'French (international)'
		);

		$data['locales'][] = array(
			'value' => 'de-de',
			'text' => 'German'
		);

		$data['locales'][] = array(
			'value' => 'he-il',
			'text' => 'Hebrew (Israel)'
		);

		$data['locales'][] = array(
			'value' => 'id-id',
			'text' => 'Indonesian'
		);

		$data['locales'][] = array(
			'value' => 'it-il',
			'text' => 'Italian'
		);

		$data['locales'][] = array(
			'value' => 'ja-jp' ,
			'text' => 'Japanese'
		);

		$data['locales'][] = array(
			'value' => 'no-no',
			'text' => 'Norwegian'
		);

		$data['locales'][] = array(
			'value' => 'pl-pl',
			'text' => 'Polish');

		$data['locales'][] = array(
			'value' => 'pt-pt',
			'text' => 'Portuguese'
		);

		$data['locales'][] = array(
			'value' => 'pt-br',
			'text' => 'Portuguese (Brazil)'
		);

		$data['locales'][] = array(
			'value' => 'ru-ru',
			'text' => 'Russian'
		);

		$data['locales'][] = array(
			'value' => 'es-es',
			'text'  => 'Spanish'
		);

		$data['locales'][] = array(
			'value' => 'es-xc',
			'text'  => 'Spanish (Mexico)'
		);

		$data['locales'][] = array(
			'value' => 'sv-se',
			'text'  => 'Swedish'
		);

		$data['locales'][] = array(
			'value' => 'th-th',
			'text'  => 'Thai'
		);

		$data['locales'][] = array(
			'value' => 'tr-tr',
			'text'  => 'Turkish'
		);

		if (isset($this->request->post['pp_login_locale'])) {
			$data['pp_login_locale'] = $this->request->post['pp_login_locale'];
		} else {
			$data['pp_login_locale'] = $this->config->get('pp_login_locale');
		}

		$data['return_url'] = HTTPS_CATALOG . 'index.php?route=module/pp_login/login';

		if (isset($this->request->post['pp_login_status'])) {
			$data['pp_login_status'] = $this->request->post['pp_login_status'];
		} else {
			$data['pp_login_status'] = $this->config->get('pp_login_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/pp_login.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/pp_login')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pp_login_client_id']) {
			$this->error['client_id'] = $this->language->get('error_client_id');
		}

		if (!$this->request->post['pp_login_secret']) {
			$this->error['secret'] = $this->language->get('error_secret');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/event');

		$this->model_extension_event->addEvent('pp_login', 'post.customer.logout', 'module/pp_login/logout');
	}

	public function uninstall() {
		$this->load->model('extension/event');

		$this->model_extension_event->deleteEvent('pp_login');
	}
}