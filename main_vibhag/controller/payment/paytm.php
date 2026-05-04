<?php
class ControllerPaymentpaytm extends Controller {
	private $error = array();
	//function executed at load of page
	public function index() {
		require_once(DIR_SYSTEM . 'encdec_paytm.php');
		///require_once(DIR_SYSTEM . 'paytm_constants.php');
		//$this->language->load('payment/paytm');
        $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/paytm', $data);
		$this->document->setTitle($data['heading_title']);
		$arr = array();	

		foreach($this->request->post as $key => $value)
		{
			if($key == 'paytm_key')
			{
				 $arr[$key] = encrypt_e($value, PAYTM_SECRET_KEY);
				continue;
			}
			$arr[$key] = $value;
		}

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		    $storeId = $arr['paytm_store'];
		    unset($arr['paytm_store']);

		    $this->model_setting_setting->editSetting('paytm', $arr, $storeId);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['text_live'] = $this->language->get('text_live');

		$data['text_successful'] = $this->language->get('text_successful');

		$data['text_fail'] = $this->language->get('text_fail');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}
		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}
		if (isset($this->error['website'])) {
			$data['error_website'] = $this->error['website'];
		} else {
			$data['error_website'] = '';
		}
		
		if (isset($this->error['industry'])) {
			$data['error_industry'] = $this->error['industry'];
		} else {
			$data['error_industry'] = '';
		}

        if (isset($this->error['store'])) {
            $data['error_store'] = $this->error['store'];
        } else {
            $data['error_store'] = '';
        }

		
		if (isset($this->request->post['paytm_order_status_id'])) {
			$data['paytm_order_status_id'] = $this->request->post['paytm_order_status_id'];
		} else {
			$data['paytm_order_status_id'] = $this->config->get('paytm_order_status_id');
		}
		
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $data['text_home'],
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $data['text_payment'],
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $data['heading_title'],
			'href'      => $this->url->link('payment/paytm', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('payment/paytm', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['paytm_merchant'])) {
			$data['paytm_merchant'] = $this->request->post['paytm_merchant'];
		} else {
			$data['paytm_merchant'] = $this->config->get('paytm_merchant');
		}

        if (isset($this->request->post['paytm_website'])) {
			$data['paytm_website'] = $this->request->post['paytm_website'];
		} else {
			$data['paytm_website'] = $this->config->get('paytm_website');
		}
		
		if (isset($this->request->post['paytm_industry'])) {
			$data['paytm_industry'] = $this->request->post['paytm_industry'];
		} else {
			$data['paytm_industry'] = $this->config->get('paytm_industry');
		}
		
		if (isset($this->request->post['paytm_key'])) {
		
			$data['paytm_key'] = $this->request->post['paytm_key'];
		} else {
			$data['paytm_key'] = $this->config->get('paytm_key');

			$data['paytm_key'] = "";

			if ($this->config->get('paytm_key') != "") {
				$data['paytm_key'] = htmlspecialchars_decode(decrypt_e($this->config->get('paytm_key'),PAYTM_SECRET_KEY),ENT_NOQUOTES);
			}
		}

		
		if (isset($this->request->post['paytm_status'])) {
			$data['paytm_status'] = $this->request->post['paytm_status'];
		} else {
			$data['paytm_status'] = $this->config->get('paytm_status');
		}
		if (isset($this->request->post['paytm_callbackurl'])) {
			$data['paytm_callbackurl'] = $this->request->post['paytm_callbackurl'];
		} else {
			$data['paytm_callbackurl'] = $this->config->get('paytm_callbackurl');
		}
		if (isset($this->request->post['paytm_checkstatus'])) {
			$data['paytm_checkstatus'] = $this->request->post['paytm_checkstatus'];
		} else {
			$data['paytm_checkstatus'] = $this->config->get('paytm_checkstatus');
		}

		if (isset($this->request->post['paytm_environment'])) {
			$data['paytm_environment'] = $this->request->post['paytm_environment'];
		} else {
			$data['paytm_environment'] = $this->config->get('paytm_environment');
		}

        if (isset($this->request->post['paytm_store'])) {
            $data['paytm_store'] = $this->request->post['paytm_store'];
        } else {
            $data['paytm_store'] = "";
		    $rs = $this->db->query("select wsb_extns.store_id from ".DB_PREFIX."wsb_extension_to_store as wsb_extns INNER JOIN ".DB_PREFIX."extension as extn on extn.extension_id = wsb_extns.extension_id WHERE extn.code = 'paytm'");
            if($rs->num_rows > 0)
            {
                $data['paytm_store'] = $rs->row['store_id'];
            }
        }

        // adding store list for PayTM settings (start)
		$data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $data['text_default'],
        );

        $this->load->model('setting/store');
        $results = $this->model_setting_store->getStores();
        foreach ($results as $result) {
            $data['stores'][] = array(
                'store_id' => $result['store_id'],
                'name'     => $result['name'],
            );
        }
        // adding store list for paytm settings (end)

        $this->template = 'payment/paytm.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');
		//$this->response->setOutput($this->render());
        $this->response->setOutput($this->load->view('payment/paytm.tpl', $data));
		
	}
	//validate function to ensure required fields are filled before proceeding
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/paytm')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['paytm_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		if (!$this->request->post['paytm_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}
		if (!$this->request->post['paytm_website']) {
			$this->error['website'] = $this->language->get('error_website');
		}
		if (!$this->request->post['paytm_industry']) {
			$this->error['industry'] = $this->language->get('error_industry');
		}

        if (isset($this->request->post['paytm_store']) && $this->request->post['paytm_store'] == "") {
            $this->error['store'] = $this->language->get('error_store');
        }

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	public function orderAction() {
	}
}
?>