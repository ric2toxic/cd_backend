<?php
class ModelPaymentCOD extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/cod');
        $this->load->model('localisation/zone');
        $geo_zone_id = 0;
        $zone_id = 0;

        if(isset($address['zone_id'])) {
            $zone_id = $address['zone_id'];
        } elseif (isset($address['Zone_id'])){
            $zone_id = $address['Zone_id'];
        }

        if($zone_id > 0) {
             $geo_zone_id =   $this->model_localisation_zone->getGeoZoneId($address['zone_id']);
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone 
                WHERE geo_zone_id = '" . (int)$this->config->get('cod_geo_zone_id') . "' 
                AND country_id = '" . (int)$address['country_id'] . "' 
                AND (zone_id = '" . (int)$address['zone_id'] . "')";

		$query = $this->db->query($sql);

		if ($this->config->get('cod_total') > 0 && $this->config->get('cod_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('cod_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} elseif ($geo_zone_id > 0 && in_array($geo_zone_id, array(5, 49))) {
		    //5 & 49 is geo_zone_id for surface couriers where COD is available
		    $status = true;
        } else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'cod',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('cod_sort_order')
			);
		}

		return $method_data;
	}
}
