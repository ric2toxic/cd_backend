<?php
class ModelAccountAddress extends Model {

   public function addressFields() {
       return array(
           'customer_id',
           'firstname',
           'lastname',
           'company',
           'telephone',
           'address_1',
           'address_2',
           'city',
           'postcode',
           'country_id',
           'zone_id'
       );
   }


	public function addAddress($data, $customer_id = 0) {
		$this->event->trigger('pre.customer.add.address', $data);

        if(!empty($data['address_telephone'])){
            $data['address_telephone'] = json_encode($data['address_telephone']);
        }
        else
        {
          $data['address_telephone'] = json_encode(array());
        }

		if ($customer_id == 0) {
			$customer_id = $this->customer->getId();
		}

		$sql = "INSERT INTO " . DB_PREFIX . "address 
		            SET 
		                customer_id = '" . (int)$customer_id . "', 
		                firstname = '" . $this->db->escape($data['firstname']) . "', 
		                lastname = '" . $this->db->escape($data['lastname']) . "', 
		                company = '" . $this->db->escape($data['company']) . "', 
		                address_1 = '" . $this->db->escape($data['address_1']) . "', 
		                address_2 = '" . $this->db->escape($data['address_2']) . "', 
		                postcode = '" . $this->db->escape($data['postcode']) . "', 
		                city = '" . $this->db->escape($data['city']) . "', 
		                telephone = '" . $this->db->escape($data['address_telephone']) . "', 
		                zone_id = '" . (int)$data['zone_id'] . "', 
		                country_id = '" . (int)$data['country_id'] . "'";
		$this->db->query($sql);

		$address_id = $this->db->getLastId();

		$default_address_id = 0;
		if(isset($data['default']) && $data['default'] == 1){
			$default_address_id = $address_id;
		} else {
			$default_address = $this->getDefaultAddress($customer_id);
			if($default_address == false) {
				$default_address_id = $address_id;
			}
		}
		
		if($default_address_id > 0){
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$default_address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		$this->event->trigger('post.customer.add.address', $address_id);

		return $address_id;
	}

	public function editAddress($address_id, $data, $customer_id = 0) {

		$this->event->trigger('pre.customer.edit.address', $data);

		/*if(!isset($data['address_telephone'])){
			$data['address_telephone'] = '';
		}*/

        if(!empty($data['address_telephone'])){
            $data['telephone'] = json_encode($data['address_telephone']);
        }
        else
        {
          $data['telephone'] = json_encode(array());
        }

		if ($customer_id == 0) {
			$customer_id = $this->customer->getId();
		}

		$addressFields = $this->addressFields();

		$sql = "UPDATE " . DB_PREFIX . "address SET ";
        $set = array();
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                if (!empty($value) && in_array($key, $addressFields)) {
                    $set[] = $key . " = '" . $this->db->escape($value) . "'";
                }
            }

            $set_fields = implode(", ", $set);
            $sql .= $set_fields;
            $sql .= " WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$customer_id . "'";
        }


		   // firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', telephone = '" . $this->db->escape($data['address_telephone']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "' WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$customer_id . "'";
		$this->db->query($sql);

		$default_address_id = 0;
		if(isset($data['default']) && $data['default'] == 1) {
			$default_address_id = $address_id;
		} else {
			$default_address = $this->getDefaultAddress($customer_id);
			if($default_address !== false && ($default_address['address_id'] !== $address_id)) {
				$default_address_id = $default_address['address_id'];
			} else {
				$all_addresses = $this->getAddresses($customer_id);
				if(count($all_addresses) == 1){
					$default_address_id = $address_id;
				} else {
					foreach ($all_addresses as $key => $address_data) {
						if($address_data['address_id'] != $address_id){
							$default_address_id = $address_data['address_id'];
							break;
						}
					}
				}
			}
		}
		
		if($default_address_id > 0){
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$default_address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}
		
		$this->event->trigger('post.customer.edit.address', $address_id);
	}

	public function deleteAddress($address_id) {
		$this->event->trigger('pre.customer.delete.address', $address_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

		$this->event->trigger('post.customer.delete.address', $address_id);
	}


	public function getAddress($address_id, $customer_id = 0) {
		//echo $address_id; die;
		if ($customer_id == 0) {
			$customer_id = $this->customer->getId();
		}

		$address_query = $this->db->query("SELECT a.address_id,
            	        a.firstname, 
            	        a.lastname, 
            	        a.company, 
            	        a.address_1, 
            	        a.address_2, 
            	        a.postcode, 
            	        a.city, 
            	        a.zone_id, 
            	        a.telephone,  
            	        a.country_id, 
            	        c.name as country,
            	        c.iso_code_2,
            	        c.iso_code_3,
            	        c.address_format,
            	        z.name as zone,
            	        z.code as zone_code
            	         FROM " . DB_PREFIX . "address a 
            	         INNER JOIN  " . DB_PREFIX . "country c
            	         ON a.country_id = c.country_id
            	         INNER JOIN  " . DB_PREFIX . "zone z
            	         ON a.zone_id = z.zone_id
            	         WHERE a.customer_id = '" . (int)$customer_id . "'
            	         AND  a.address_id = '" . (int)$address_id . "'");

		if ($address_query->num_rows) {
			/*$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}*/

			$address_data = array(
				'address_id'     => $address_query->row['address_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'address_telephone' => is_array(json_decode($address_query->row['telephone'])) ? json_decode($address_query->row['telephone']) : explode(",", $address_query->row['telephone']),
				'zone'           => $address_query->row['zone'],
				'zone_code'      => $address_query->row['zone_code'],
				'country_id'     => $address_query->row['country_id'],
				'country'        => $address_query->row['country'],
				'iso_code_2'     => $address_query->row['iso_code_2'],
				'iso_code_3'     => $address_query->row['iso_code_3'],
				'address_format' => $address_query->row['address_format']
			);
            
			return $address_data;
		} else {
			return false;
		}
	}

    public function getCustomerAddressId($customer_id = 0)
    {
       $address_query = $this->db->query("SELECT address_id
                        FROM " . DB_PREFIX . "customer
                         WHERE customer_id = '" . (int)$customer_id . "'");
        return $address_query->row['address_id'];                    
    }

	public function getAddresses($customer_id = 0) {
		$address_data = array();

		if ($customer_id == 0) {
			$customer_id = $this->customer->getId();
		}

		if(!empty($customer_id)) {

            $query = $this->db->query(
            	"SELECT a.address_id,
            	        a.firstname, 
            	        a.lastname, 
            	        a.company, 
            	        a.address_1, 
            	        a.address_2, 
            	        a.postcode, 
            	        a.city, 
            	        a.zone_id, 
            	        a.telephone,  
            	        a.country_id, 
            	        c.name as country,
            	        c.iso_code_2,
            	        c.iso_code_3,
            	        c.address_format,
            	        z.name as zone,
            	        z.code as zone_code
            	         FROM " . DB_PREFIX . "address a 
            	         INNER JOIN  " . DB_PREFIX . "country c
            	         ON a.country_id = c.country_id
            	         INNER JOIN  " . DB_PREFIX . "zone z
            	         ON a.zone_id = z.zone_id
            	         WHERE a.customer_id = '" . (int)$customer_id . "'
            	         group by a.address_id");
         

            foreach ($query->rows as $result) {
                /*$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");

                if ($country_query->num_rows) {
                    $country = $country_query->row['name'];
                    $iso_code_2 = $country_query->row['iso_code_2'];
                    $iso_code_3 = $country_query->row['iso_code_3'];
                    $address_format = $country_query->row['address_format'];
                } else {
                    $country = '';
                    $iso_code_2 = '';
                    $iso_code_3 = '';
                    $address_format = '';
                }*/

                /*$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");

                if ($zone_query->num_rows) {
                    $zone = $zone_query->row['name'];
                    $zone_code = $zone_query->row['code'];
                } else {
                    $zone = '';
                    $zone_code = '';
                }*/
                $incomplete_address = 0;
                if ($result['address_1'] == '' OR $result['city'] == '' OR $result['zone'] == '') {
                    $incomplete_address = 1;
                }
                
                if ($customer_id == 0) {
                    $address_data[$result['address_id']] = array(
                        'address_id' => $result['address_id'],
                        'firstname' => $result['firstname'],
                        'lastname' => $result['lastname'],
                        'company' => $result['company'],
                        'address_1' => $result['address_1'],
                        'address_2' => $result['address_2'],
                        'postcode' => $result['postcode'],
                        'city' => $result['city'],
                        'zone_id' => $result['zone_id'],
                        'address_telephone' => is_array(json_decode($result['telephone'])) ? json_decode($result['telephone']) : explode(",", $result['telephone']),
                        'zone' => $result['zone'],
                        'zone_code' => $result['zone_code'],
                        'country_id' => $result['country_id'],
                        'country' => $result['country'],
                        'iso_code_2' => $result['iso_code_2'],
                        'iso_code_3' => $result['iso_code_3'],
                        'address_format' => $result['address_format'],
                        'incomplete_address' => $incomplete_address

                    );
                } else {
                    $address_data[] = array(
                        'address_id' => $result['address_id'],
                        'firstname' => $result['firstname'],
                        'lastname' => $result['lastname'],
                        'company' => $result['company'],
                        'address_1' => $result['address_1'],
                        'address_2' => $result['address_2'],
                        'postcode' => $result['postcode'],
                        'city' => $result['city'],
                        'zone_id' => $result['zone_id'],
                        'address_telephone' => is_array(json_decode($result['telephone'])) ? json_decode($result['telephone']) : explode(",", $result['telephone']),
                        'zone' => $result['zone'],
                        'zone_code' => $result['zone_code'],
                        'country_id' => $result['country_id'],
                        'country' => $result['country'],
                        'iso_code_2' => $result['iso_code_2'],
                        'iso_code_3' => $result['iso_code_3'],
                        'address_format' => $result['address_format'],
                        'incomplete_address' => $incomplete_address

                    );
                }
            }
        }

		return $address_data;
	}

	public function getTotalAddresses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}


	// when customer delete our default address then update new address id in customer table
	public function updateAddressId($customer_id){
		$sql = "SELECT address_id FROM ".DB_PREFIX."address WHERE customer_id = ".(int)$customer_id. " ORDER BY address_id ASC ";
		$query = $this->db->query($sql);
		$update_query = "UPDATE oc_customer SET address_id = ".$query->row['address_id']." WHERE customer_id = ".(int)$customer_id;
		$this->db->query($update_query);
	}

	public function addAddressFromBackend($data) {
		if(!isset($data['address_telephone'])){
			$data['address_telephone'] = '';
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', telephone = '" . $this->db->escape($data['address_telephone']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "'");

	}

	public function getAddressIdFromCustomerId ($customer_id = 0) {
		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['address_id'];
	}

	public function getAddressByPin ($pincode) {
        $info_to_be_populated = array();

        if ($pincode != '') {
            $solr = new SolrLookup($this);
            $response = $solr->gePincodeData($pincode);

            if (count($response) > 0) {
                $city = $response[0]['city'];
                $state = $response[0]['zone'];
                $country = $response[0]['country'];
            }

            $info_to_be_populated = array(
                'city' => $city,
                'state' => $state,
                'country' => $country
            );
        }

        return $info_to_be_populated;

        /*
		$ch = curl_init('http://maps.googleapis.com/maps/api/geocode/json?address='.$pincode.'&sensor=true');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$data = json_decode($result);

		$info_to_be_populated = array();

		if ( isset($data->results[0]->address_components) && !empty($data->results[0]->address_components) ) {

			$city = '';
			$state = '';
			$country = '';
			foreach ($data->results[0]->address_components as $api_return){
				//echo $api_return->types[0]."<br />";
				if ( $api_return->types[0] == 'administrative_area_level_2' ) {
					$city = $api_return->long_name;
				}

				if ( $api_return->types[0] == 'administrative_area_level_1' ) {
					$state = $api_return->long_name;
				}

				if ( $api_return->types[0] == 'country' ) {
					$country = $api_return->long_name;
				}

				$info_to_be_populated = array(
					'city' => $city,
					'state' => $state,
					'country' => $country
				);
			}

			return $info_to_be_populated;
		}
        */
	}

	/**
     * Get Default address
     */

	public function getDefaultAddress($customer_id) {
        $query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id. "'");
        $address_id = $query->row['address_id'];
        if ($address_id > 0) {
           return $this->getAddress($address_id, $customer_id);

        } else {
            return false;
        }


    }
}
