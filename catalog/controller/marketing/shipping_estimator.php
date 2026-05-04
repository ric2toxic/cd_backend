<?php
class Controllermarketingshippingestimator extends Controller {
	public function index(){
		$data = '';
		$this->load->model('localisation/country');
		$data['countries']     = $this->model_localisation_country->getCountries();
		$data['states']        = $this->getZone(99);
		$data['get_state_list'] = $this->url->link('checkout/shipping/country', '', 'SSL');
		$data['get_state_through_pincode'] = $this->url->link('seller_panel/profile/getState', '', 'SSL');
		$data['shipping_estimator_url'] = $this->url->link('marketing/shipping_estimator/getShippingEstimator', '', 'SSL');
		
		$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/marketing/shipping_estimator.tpl', $data));
	}

	public function getZone($country_id) {
		if (isset($country_id) && !empty($country_id)){
			$this->load->model('localisation/zone');
			$result = $this->model_localisation_zone->getZonesByCountryId($country_id);
			return $result;
		}
	}
	public function getShippingEstimator(){
		if($this->request->post){
			
			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country']);
			if ($country_info) {
				$country = $country_info['name'];
				$iso_code_2 = $country_info['iso_code_2'];
				$iso_code_3 = $country_info['iso_code_3'];
				$address_format = $country_info['address_format'];
				
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$this->load->model('localisation/zone');
			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone']);

			if ($zone_info) {
				$zone = $zone_info['name'];
				$zone_code = $zone_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}
			$address = array(
				'firstname'      => '',
				'lastname'       => '',
				'company'        => '',
				'address_1'      => '',
				'address_2'      => '',
				'from_backend'   => true,
				'postcode'       => $this->request->post['pincode'],
				'city'           => '',
				'zone_id'        => $this->request->post['zone'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $this->request->post['country'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
			$this->load->model('shipping/weight');
			$quote = $this->model_shipping_weight->getQuote($address,$this->request->post['weight']);
			echo json_encode($quote);
			exit;
		}
	}
}
