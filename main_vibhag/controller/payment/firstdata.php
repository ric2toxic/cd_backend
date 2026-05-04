<?php
class ControllerPaymentFirstdata extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/firstdata');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/firstdata', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('firstdata', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['text_card_type'] = $this->language->get('text_card_type');
		$data['entry_status_decline_pending'] = $this->language->get('entry_status_decline_pending');
		$data['entry_status_decline_stolen'] = $this->language->get('entry_status_decline_stolen');
		$data['entry_status_decline_bank'] = $this->language->get('entry_status_decline_bank');

		$data['notify_url'] = HTTPS_CATALOG . 'index.php?route=payment/firstdata/notify';

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error_merchant_id'])) {
			$data['error_merchant_id'] = $this->error['error_merchant_id'];
		} else {
			$data['error_merchant_id'] = '';
		}

		if (isset($this->error['error_secret'])) {
			$data['error_secret'] = $this->error['error_secret'];
		} else {
			$data['error_secret'] = '';
		}

		if (isset($this->error['error_live_url'])) {
			$data['error_live_url'] = $this->error['error_live_url'];
		} else {
			$data['error_live_url'] = '';
		}

		if (isset($this->error['error_demo_url'])) {
			$data['error_demo_url'] = $this->error['error_demo_url'];
		} else {
			$data['error_demo_url'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['text_payment'],
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('payment/firstdata', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/firstdata', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['firstdata_merchant_id'])) {
			$data['firstdata_merchant_id'] = $this->request->post['firstdata_merchant_id'];
		} else {
			$data['firstdata_merchant_id'] = $this->config->get('firstdata_merchant_id');
		}

		if (isset($this->request->post['firstdata_secret'])) {
			$data['firstdata_secret'] = $this->request->post['firstdata_secret'];
		} else {
			$data['firstdata_secret'] = $this->config->get('firstdata_secret');
		}

		if (isset($this->request->post['firstdata_live_demo'])) {
			$data['firstdata_live_demo'] = $this->request->post['firstdata_live_demo'];
		} else {
			$data['firstdata_live_demo'] = $this->config->get('firstdata_live_demo');
		}

		if (isset($this->request->post['firstdata_geo_zone_id'])) {
			$data['firstdata_geo_zone_id'] = $this->request->post['firstdata_geo_zone_id'];
		} else {
			$data['firstdata_geo_zone_id'] = $this->config->get('firstdata_geo_zone_id');
		}

		if (isset($this->request->post['firstdata_total'])) {
			$data['firstdata_total'] = $this->request->post['firstdata_total'];
		} else {
			$data['firstdata_total'] = $this->config->get('firstdata_total');
		}

		if (isset($this->request->post['firstdata_sort_order'])) {
			$data['firstdata_sort_order'] = $this->request->post['firstdata_sort_order'];
		} else {
			$data['firstdata_sort_order'] = $this->config->get('firstdata_sort_order');
		}

		if (isset($this->request->post['firstdata_status'])) {
			$data['firstdata_status'] = $this->request->post['firstdata_status'];
		} else {
			$data['firstdata_status'] = $this->config->get('firstdata_status');
		}

		if (isset($this->request->post['firstdata_debug'])) {
			$data['firstdata_debug'] = $this->request->post['firstdata_debug'];
		} else {
			$data['firstdata_debug'] = $this->config->get('firstdata_debug');
		}

		if (isset($this->request->post['firstdata_auto_settle'])) {
			$data['firstdata_auto_settle'] = $this->request->post['firstdata_auto_settle'];
		} elseif (!isset($this->request->post['firstdata_auto_settle']) && $this->config->get('firstdata_auto_settle') != '') {
			$data['firstdata_auto_settle'] = $this->config->get('firstdata_auto_settle');
		} else {
			$data['firstdata_auto_settle'] = 1;
		}

		if (isset($this->request->post['firstdata_order_status_success_settled_id'])) {
			$data['firstdata_order_status_success_settled_id'] = $this->request->post['firstdata_order_status_success_settled_id'];
		} else {
			$data['firstdata_order_status_success_settled_id'] = $this->config->get('firstdata_order_status_success_settled_id');
		}

		if (isset($this->request->post['firstdata_order_status_success_unsettled_id'])) {
			$data['firstdata_order_status_success_unsettled_id'] = $this->request->post['firstdata_order_status_success_unsettled_id'];
		} else {
			$data['firstdata_order_status_success_unsettled_id'] = $this->config->get('firstdata_order_status_success_unsettled_id');
		}

		if (isset($this->request->post['firstdata_order_status_decline_id'])) {
			$data['firstdata_order_status_decline_id'] = $this->request->post['firstdata_order_status_decline_id'];
		} else {
			$data['firstdata_order_status_decline_id'] = $this->config->get('firstdata_order_status_decline_id');
		}

		if (isset($this->request->post['firstdata_order_status_void_id'])) {
			$data['firstdata_order_status_void_id'] = $this->request->post['firstdata_order_status_void_id'];
		} else {
			$data['firstdata_order_status_void_id'] = $this->config->get('firstdata_order_status_void_id');
		}

		if (isset($this->request->post['firstdata_live_url'])) {
			$data['firstdata_live_url'] = $this->request->post['firstdata_live_url'];
		} else {
			$data['firstdata_live_url'] = $this->config->get('firstdata_live_url');
		}

		if (empty($data['firstdata_live_url'])) {
			$data['firstdata_live_url'] = 'https://ipg-online.com/connect/gateway/processing';
		}

		if (isset($this->request->post['firstdata_demo_url'])) {
			$data['firstdata_demo_url'] = $this->request->post['firstdata_demo_url'];
		} else {
			$data['firstdata_demo_url'] = $this->config->get('firstdata_demo_url');
		}

		if (isset($this->request->post['firstdata_card_storage'])) {
			$data['firstdata_card_storage'] = $this->request->post['firstdata_card_storage'];
		} else {
			$data['firstdata_card_storage'] = $this->config->get('firstdata_card_storage');
		}

		if (empty($data['firstdata_demo_url'])) {
			$data['firstdata_demo_url'] = 'https://test.ipg-online.com/connect/gateway/processing';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/firstdata.tpl', $data));
	}

	public function install() {
		$this->load->model('payment/firstdata');
		$this->model_payment_firstdata->install();
	}

	public function uninstall() {
		$this->load->model('payment/firstdata');
		$this->model_payment_firstdata->uninstall();
	}

	public function action() {
		if ($this->config->get('firstdata_status')) {
			$this->load->model('payment/firstdata');

			$firstdata_order = $this->model_payment_firstdata->getOrder($this->request->get['order_id']);

			if (!empty($firstdata_order)) {
				//$this->load->language('payment/firstdata');

				$data = array(); // Initializing the data array to be passed on to template files
	            // Autoloading the lanugage
	            $this->load->autoLoadLanguage('payment/firstdata', $data);

				$firstdata_order['total_captured'] = $this->model_payment_firstdata->getTotalCaptured($firstdata_order['firstdata_order_id']);
				$firstdata_order['total_formatted'] = $this->currency->format($firstdata_order['total'], $firstdata_order['currency_code'], 1, true);
				$firstdata_order['total_captured_formatted'] = $this->currency->format($firstdata_order['total_captured'], $firstdata_order['currency_code'], 1, true);

				$data['firstdata_order'] = $firstdata_order;
				$data['merchant_id'] = $this->config->get('firstdata_merchant_id');
				$data['currency'] = $this->model_payment_firstdata->mapCurrency($firstdata_order['currency_code']);
				$data['amount'] = number_format($firstdata_order['total'], 2);

				$data['request_timestamp'] = date("Y:m:d-H:i:s");

				$data['hash'] = sha1(bin2hex($data['merchant_id'] . $data['request_timestamp'] . $data['amount'] . $data['currency'] . $this->config->get('firstdata_secret')));

				$data['void_url'] = $this->url->link('payment/firstdata/void', 'token=' . $this->session->data['token'], 'SSL');
				$data['capture_url'] = $this->url->link('payment/firstdata/capture', 'token=' . $this->session->data['token'], 'SSL');
				$data['notify_url'] = HTTPS_CATALOG . 'index.php?route=payment/firstdata/notify';

				if ($this->config->get('firstdata_live_demo') == 1) {
					$data['action_url'] = $this->config->get('firstdata_live_url');
				} else {
					$data['action_url'] = $this->config->get('firstdata_demo_url');
				}

				if (isset($this->session->data['void_success'])) {
					$data['void_success'] = $this->session->data['void_success'];

					unset($this->session->data['void_success']);
				} else {
					$data['void_success'] = '';
				}

				if (isset($this->session->data['void_error'])) {
					$data['void_error'] = $this->session->data['void_error'];

					unset($this->session->data['void_error']);
				} else {
					$data['void_error'] = '';
				}

				if (isset($this->session->data['capture_success'])) {
					$data['capture_success'] = $this->session->data['capture_success'];

					unset($this->session->data['capture_success']);
				} else {
					$data['capture_success'] = '';
				}

				if (isset($this->session->data['capture_error'])) {
					$data['capture_error'] = $this->session->data['capture_error'];

					unset($this->session->data['capture_error']);
				} else {
					$data['capture_error'] = '';
				}

				$data['order_id'] = $this->request->get['order_id'];
				$data['token'] = $this->request->get['token'];

				return $this->load->view('payment/firstdata_order.tpl', $data);
			}
		}
	}

	public function void() {
		$this->load->language('payment/firstdata');

		if ($this->request->post['status'] == 'FAILED') {
			if (isset($this->request->post['fail_reason'])) {
				$this->session->data['void_error'] = $this->request->post['fail_reason'];
			} else {
				$this->session->data['void_error'] = $this->language->get('error_void_error');
			}
		}

		if ($this->request->post['status'] == 'DECLINED') {
			$this->session->data['void_success'] = $this->language->get('success_void');
		}

		$this->response->redirect($this->url->link('sale/order/info', 'order_id=' . $this->request->post['order_id'] . '&token=' . $this->session->data['token'], 'SSL'));
	}

	public function capture() {
		$this->load->language('payment/firstdata');

		if ($this->request->post['status'] == 'FAILED') {
			if (isset($this->request->post['fail_reason'])) {
				$this->session->data['capture_error'] = $this->request->post['fail_reason'];
			} else {
				$this->session->data['capture_error'] = $this->language->get('error_capture_error');
			}
		}

		if ($this->request->post['status'] == 'APPROVED') {
			$this->session->data['capture_success'] = $this->language->get('success_capture');
		}

		$this->response->redirect($this->url->link('sale/order/info', 'order_id=' . $this->request->post['order_id'] . '&token=' . $this->session->data['token'], 'SSL'));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/firstdata')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['firstdata_merchant_id']) {
			$this->error['error_merchant_id'] = $this->language->get('error_merchant_id');
		}

		if (!$this->request->post['firstdata_secret']) {
			$this->error['error_secret'] = $this->language->get('error_secret');
		}

		if (!$this->request->post['firstdata_live_url']) {
			$this->error['error_live_url'] = $this->language->get('error_live_url');
		}

		if (!$this->request->post['firstdata_demo_url']) {
			$this->error['error_demo_url'] = $this->language->get('error_demo_url');
		}

		return !$this->error;
	}
}