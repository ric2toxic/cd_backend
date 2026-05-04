<?php
class ControllerReviewBulkProductReviews extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('review/bulk_product_reviews');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->getForm();
	}

	public function add() {
		$this->load->language('review/bulk_product_reviews');

		$this->load->model('review/bulk_product_reviews');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_review_bulk_product_reviews->addReview($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('review/bulk_product_reviews', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	protected function getForm() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('review/bulk_product_reviews', $data);

		$data['text_form'] = $this->language->get('text_add');

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


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$url = '';
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('review/bulk_product_reviews', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);


		$data['action'] = $this->url->link('review/bulk_product_reviews/add', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['token'] = $this->session->data['token'];


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('review/bulk_product_reviews_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'review/bulk_product_reviews')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		/*if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['text']) < 1) {
			$this->error['text'] = $this->language->get('error_text');
		}*/

//		if (!isset($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
//			$this->error['rating'] = $this->language->get('error_rating');
//		}

		return !$this->error;
	}
}