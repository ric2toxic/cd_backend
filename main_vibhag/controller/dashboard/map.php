<?php
class ControllerDashboardMap extends Controller {
	public function index() {

		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('dashboard/map', $data);

		$data['token'] = $this->session->data['token'];

		return $this->load->view('dashboard/map.tpl', $data);
	}

	public function map() {
		$json = array();

		$this->load->model('report/sale');

		$results = $this->model_report_sale->getTotalOrdersByCountry();

		foreach ($results as $result) {
			$json[strtolower($result['iso_code_2'])] = array(
				'total'  => $result['total'],
				'amount' => $this->currency->format($result['amount'], $this->config->get('currency_code'))
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}