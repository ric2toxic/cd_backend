<?php
class ControllerToolBackup extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('tool/backup');
        $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('tool/backup', $data);
		$this->document->setTitle($data['heading_title']);

		$this->load->model('tool/backup');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'tool/backup')) {
			if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
				$content = file_get_contents($this->request->files['import']['tmp_name']);
			} else {
				$content = false;
			}

			if ($content) {
				$this->model_tool_backup->restore($content);

				$this->session->data['success'] = $data['text_success'];

				$this->response->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->error['warning'] = $data['error_empty'];
			}
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['restore'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL');

		$data['backup'] = $this->url->link('tool/backup/backup', 'token=' . $this->session->data['token'], 'SSL');

		$data['tables'] = $this->model_tool_backup->getTables();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tool/backup.tpl', $data));
	}

	public function backup() {
		$this->load->language('tool/backup');

		if (!isset($this->request->post['backup'])) {
			$this->session->data['error'] = $this->language->get('error_backup');

			$this->response->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
		} elseif ($this->user->hasPermission('modify', 'tool/backup')) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=' . DB_DATABASE . '_' . date('Y-m-d_H-i-s', time()) . '_backup.sql');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->load->model('tool/backup');

			$this->response->setOutput($this->model_tool_backup->backup($this->request->post['backup']));
		} else {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
}
