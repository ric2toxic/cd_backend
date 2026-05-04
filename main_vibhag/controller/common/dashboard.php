<?php
class ControllerCommonDashboard extends Controller {
	public function index() {
		//check for user landing page setting
		$landing_page = $this->user->isUserHasDefaultLandingPage();
		if (!empty($landing_page['dont_show_dashboard']) && !empty($landing_page['default_landing_page_url'])) {
			$this->response->redirect($this->url->link($landing_page['default_landing_page_url'], 'token=' . $this->session->data['token'], 'SSL'));

		} else if (!empty($landing_page['dont_show_dashboard']) && empty($landing_page['default_landing_page_url'])) {

			throw new \Exception('Wrong default landing page setting for user profile');
		}
		//check for user landing page setting

		$data = array(); // Initializing the data array to be passed on to template files
		// Autoloading the lanugage
		$this->load->autoLoadLanguage('common/dashboard', $data);

		$this->document->setTitle($data['heading_title']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		// Check install directory exists
		if (is_dir(dirname(DIR_APPLICATION) . '/install')) {
			$data['error_install'] = $this->language->get('error_install');
		} else {
			$data['error_install'] = '';
		}

		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['order'] = $this->load->controller('dashboard/order');
		$data['sale'] = $this->load->controller('dashboard/sale');
		$data['customer'] = $this->load->controller('dashboard/customer');
		$data['online'] = $this->load->controller('dashboard/online');
		$data['map'] = $this->load->controller('dashboard/map');
		$data['chart'] = $this->load->controller('dashboard/chart');
		$data['recent'] = $this->load->controller('dashboard/recent');
		$data['footer'] = $this->load->controller('common/footer');

		// Run currency update
		if ($this->config->get('config_currency_auto')) {
			$this->load->model('localisation/currency');

			$this->model_localisation_currency->refresh();
		}

		$this->response->setOutput($this->load->view('common/dashboard.tpl', $data));
	}

	/**
	 * Method for display field value on click to see. it's a common function.
	 * we can use multiple place in adminpanel tpl files.
	 * @author: vikas, 2017
	 */
	public function fieldValueOnClickToSee() {
		$this->load->model('common/common');
		$data = array();
		$data['field_name'] = $this->request->post['field_name'];
		$data['new_value'] = base64_decode($this->request->post['new_value']);
		$json = array();
		$this->model_common_common->fieldValueOnClickToSee($data);
		$json['value'] = $data['new_value'];
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Method - changePassword
	 * This method will generate a unique auto generated password string and reset user profile with new password.
	 * New password will generated using - password_hash algorithm
	 * @return: void
	 * @author: MSA, Feb 2019
	 */
	public function changePassword(): void{
		$user_id = $this->request->get['user_id'] ?? $this->user->getId();

		$data = $this->user->getRandomPasswordString();

		if (!empty($data['password'])) {

			$password = password_hash($data['password'], PASSWORD_DEFAULT);

			$sql = "UPDATE
                        `" . DB_PREFIX . "user`
                    SET
                        password            = '" . $this->db->escape($password) . "',
                        old_password        = NULL,
                        old_password_active = 0
                    WHERE
                        user_id = '" . (int) $user_id . "'";

			$this->db->query($sql);

			echo json_encode(array('password' => $data['password']));
		}
	}

}