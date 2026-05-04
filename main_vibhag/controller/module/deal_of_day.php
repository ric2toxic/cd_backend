<?php
class ControllerModuleDealOfDay extends Controller{
  public function index() {
	 
    //$this->load->language('module/deal_of_day');
    $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
    $this->load->autoLoadLanguage('module/deal_of_day', $data);

    $this->load->language('english');

    $this->document->setTitle($data['heading_title']);

    $this->load->model('module/deal_of_day');

		$this->load->model('setting/setting');
		$sendAllData = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			//echo "<pre>"; print_r($this->request->post); exit;
			//unset($this->request->post['dealDay']);
			//unset($this->request->post['sellers_list_id']);
			//echo "<pre>"; print_r($this->request->post); exit;
			$this->model_setting_setting->editSetting('deal_of_day', $this->request->post);
			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

    //$this->document->setTitle($this->language->get('heading_title'));

	$data['text_per_piece'] = $this->language->get('text_per_piece');
    $data['text_form']          =!isset($this->request->get['deal_day_id']) ? $data['text_add'] : $data['text_edit'];

    //get Seller List
    $data['sellers_list'] = $this->model_module_deal_of_day->getSellerList();

    //$this->model_module_deal_of_day->addDealDay($this->request->post);

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
        'text' => $data['text_home'],
        'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
    );

    $data['breadcrumbs'][] = array(
        'text' => $data['heading_title'],
        'href' => $this->url->link('module/deal_of_day', 'token=' . $this->session->data['token'] , 'SSL')
    );


    $data['action'] = $this->url->link('module/deal_of_day', 'token=' . $this->session->data['token'], 'SSL');

    $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


    //echo "<pre>";print_r($this->request->post);echo "</pre>";

    //echo "<pre>";print_r($this->request->post);echo "</pre>";die;
		/*$this->load->model('extension/extension');

		$data['deals'] = array();

		foreach ($this->model_extension_extension->getInstalled('deal_of_day') as $deal) {
			//if (file_exists(DIR_APPLICATION . 'controller/module/' . $deal . '.php')) {
				//$this->load->language('module/' . $deal);
				$data['deals'][] = array(
					'name' => $this->language->get('heading_title'),
					'code' => $payment,
				);
			//}
		}*/


	$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['deal_of_day_status'])) {
			$data['deal_of_day_status'] = $this->request->post['deal_of_day_status'];
		} else {
			$data['deal_of_day_status'] = $this->config->get('deal_of_day_status');
		}

		if (isset($this->request->post['deal_of_day_sort_order'])) {
			$data['deal_of_day_sort_order'] = $this->request->post['deal_of_day_sort_order'];
		} else {
			$data['deal_of_day_sort_order'] = $this->config->get('deal_of_day_sort_order');
		}

		if (isset($this->request->post['deal_of_day'])) {
			$data['deal_of_day'] = $this->request->post['deal_of_day'];
		} else {
			$data['deal_of_day'] = $this->config->get('deal_of_day');
		}




    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
	//echo "<pre>"; print_r($data); exit;
    $this->response->setOutput($this->load->view('module/deal_of_day.tpl', $data));
  }
  protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/deal_of_day')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}

