<?php
/**
 * Cart Class
 * Cart has various construct modes. It can be created by customer, recreated
 * from database, or created using custom products from backend.
 * Cart must strive to do all database related queries at constructor stage itself
 * Repetitive queries and computations to be avoided.
 * @Author: Ravindra Shekhawat, 2016
 * @Author: Madhur Bhaiya, 2016
 */
class Cart {

	/*
	 * Private Data
	 * All variables need to be computed and stored as private
	 * Necessary ones are to be computed at constructor stage itself.
	 * To get any data from cart, first preference is to check whether
	 * the value has already been computed. If yes, then a simple 'get' has to be done
	 * Else, compute, set and return. 'set' will ensure avoidance of recalculation next time.
	 */

	// Configuration
	private $_config = null;
	// Database Object
	private $_db = null;
	// Customer Object
	private $_customer = null;
	// Session Object
	private $_session = null;
	// Tax Object
	private $_tax = null;
	// Weight Object
	private $_weight = null;
	// WSB Object - Currently used for SOR price determination
	private $_wsb = null;

	private $_registry = null;
    
    public static $use_hidden_selling_price = false;
		public static $apply_international_price_factor = false;

	// Cart Attributes
	public $_cart_data = null; // Cart data (products)
	public $_in_stock_cart_data = null; // Cart data (products which are in stock)
    private $_product_comments = null;
	private $_cod_available = null;
	private $_has_downloads = null;
	private $_has_shipping = null;
	private $_has_stock = null;
	private $_pickup_city_map = null;
	private $_coupon = null;


	// Cart Data Maps
	private $_product_ids = ''; // Comma separated string of unique product_ids in the cart
	private $_seller_ids = ''; // Comma separated string of unique product_ids in the cart
	private $_pid_detail_map = array(); // product_id => array(data from oc_product and oc_product_description)
	private $_pid_sid_map = array(); // product_id => seller_id
	private $_sid_detail_map = array(); // seller_id => array(vacation, cod, seller_status and so on..)
  private $_fid_pid_map = array(); // franchise id => product id array
	private $_pid_associate_ids_map = array(); // product_id => associate_product_ids array 
	
	// if atleast one associate product is out of stock then 
	// all other associate products of that combo will be marked as out of stock
	private $_combo_product_stock_status_map = array(); // product_id => stock status 

	private $_option_detail_map = array(); // cart serialized $key => option details
	private $_recur_detail_map = array(); // cart serialized $key => recurring details

    private $_franchise_id = null; // will be used when franchise cash or credit coupon is applied.
    private $_franchise_margin = 0; // will be used when franchise credit coupon is applied.


	/**
	 * Default Constructor
	 * This constructor is called by index.php in frontend
	 * If not logged in: Cart is recovered from cart_session_id in cookies.
	 * If logged in: Cart is recovered from last cart added row in oc_customer_cart table
	 */
	public function __construct($registry) {

		$this->_registry = $registry;
		
        if(method_exists( $registry , 'get' )){
    		// Initializing private data
    		$this->_config = $registry->get('config');
    		$this->_db = $registry->get('db');
    		$this->_cart_data = null;
    		$this->_in_stock_cart_data = null;
    		$this->_customer = $registry->get('customer');
    		$this->_session = $registry->get('session');
    		$this->_tax = $registry->get('tax');
    		$this->_weight = $registry->get('weight');
    		$this->_wsb = $registry->get('wsb');
    		
        }
        else{
            $this->_db = $registry->db;
            $this->_customer = $registry->customer;
            $this->_session = $registry->session;
        }
        $this->_coupon = null;

        if(!$this->_customer->isLogged()) {
            if (!isset($_COOKIE['cart_session_id'])) {
                setcookie('cart_session_id', session_id(), time() + (86400 * 30), "/");
            } else {
                setcookie('cart_session_id', $_COOKIE['cart_session_id'], time() + (86400 * 30), "/");
            }
        }

        $this->_pickup_city_map = array(
            "JP" => "JAIPUR",
            "DL" => "DELHI",
            "ST" => "SURAT",
            "BLR" => "BANGALORE",
            "KL" => "KOLKATA",
            "MU" => "MUMBAI",
            "BL" => "BANGALORE",
            "MB" => "MUMBAI"
        );
	}


	/**
	 * Method to get serialized cart data from oc_customer_cart table
	 * @param $user_id - defaulted to blank. Used in the case of app API calls
	 * @return It returns -1 if $this->_cart_data already exists,
	 *         or if there is no cart data in the table (NULL or empty),
	 *         or if there is cart data but after unserializing, it is found empty.
	 * Else, it returns unserialized cart data from the table.
	 *
	 * @note If it returns -1, calling function must use $this->_cart_data to access
	 * cart details (can be empty also; meaning no items in the cart).
	 * @author Madhur, 2016
	 */
	private function _getCustomerCartData($user_id='') {

		// Check if the cart data is already filled or not (default to null)
		if ( !empty($this->_cart_data) ) {
			return -1;
		}

		// Create SQL
		// Else get the cart data from tables
		$sql = "SELECT * FROM " . DB_PREFIX . "customer_cart";

		// If customer (user) id is provided - generally in case of App
		if ( !empty($user_id) ) {
			$sql .= " WHERE customer_id = '" . (int)$user_id . "'";

		} else { // Customer (user) id is not provided

			// Check if we can get it ourselves <-> customer is logged in
			$user_id = $this->_customer->getId();
			if ( !empty($user_id) ) { // customer is logged in
				$sql .= " WHERE customer_id = '" . (int)$user_id . "'";

			}
			 else if(isset($_REQUEST['cart_session_id']))
             {
             	$cart_session_id = $_REQUEST['cart_session_id'];
					$sql .= " WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'";
             }
             else if(isset($_COOKIE['cart_session_id'])) {  // Anonymous user - get cart_session_id from cookies
                    $cart_session_id = $_COOKIE['cart_session_id'];
					$sql .= " WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'";
			}
			else{
                $this->_cart_data = array();
                $this->_in_stock_cart_data = array();
			    return -1;
            }
		}
		// Run QUERY
		$query = $this->_db->query($sql, true);

		//check if product comments exist.
        if(!empty($query->row['product_comments']))
            $this->_product_comments = unserialize($query->row['product_comments']);

        // franchise id
        if(!empty($query->row['franchise_id']))
            $this->_franchise_id = $query->row['franchise_id'];

        // franchise margin
        if(!empty($query->row['franchise_margin']))
            $this->_franchise_margin = $query->row['franchise_margin'];

		// Check if cartdata exists
		if( !empty($query->row['cart_data']) ) {
			$cartdata = unserialize($query->row['cart_data']);
			if (!empty($cartdata)) {
				return $cartdata;
			}
		}

		// No cart found - Meaning table entry is either blank or NULL or unserialized data is empty
		$this->_cart_data = array(); // Initializing cart_data to empty array; will be used by the calling function
		$this->_in_stock_cart_data = array();
		return -1;
	}


	/**
	 * Method to update product details in $this->_pid_detail_map.
	 * Product details are obtained from oc_product, oc_product_description, oc_product_option tables, etc.
	 * It also updates price etc from oc_product_discount and oc_product_specials tables.
	 * If the product is a download, it is updated as well.
	 * @author Madhur, 2016
	 */
	private function _updateProductDetailsMap() {
		// oc_product
		$sql = "SELECT p.*, 
		               COALESCE(GROUP_CONCAT(ptc.category_id SEPARATOR ','),0) AS category_id  
				FROM " . DB_PREFIX . "product p 
				LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON ptc.product_id=p.product_id
		        WHERE p.product_id IN (" . $this->_product_ids . ")
		        GROUP BY p.product_id
		        ";
		$op_query = $this->_db->query($sql);
		if ($op_query->num_rows == 0) // Ensuring data sanity to skip if no product row found
			return false;

		foreach ($op_query->rows as $row) {
			foreach ($row as $field => $value) {
				$this->_pid_detail_map[$row['product_id']][$field] = $value;
			}
			$this->_fid_pid_map[$row['franchise_id']][] = $row['product_id'];
		}

		// oc_product_description
		$sql = "SELECT * FROM " . DB_PREFIX . "product_description
		        WHERE product_id IN (" . $this->_product_ids . ")
		          AND language_id = '" . (int)$this->_config->get('config_language_id') . "'";
		$opd_query = $this->_db->query($sql);
		foreach ($opd_query->rows as $row) {
			foreach ($row as $field => $value) {
				$this->_pid_detail_map[$row['product_id']][$field] = $value;
			}
		}

		//sor product
		 $sql = "SELECT product_id, sor_days FROM " . DB_PREFIX . "product_sor_terms
                WHERE product_id IN (" . $this->_product_ids . ")";
        $sor_query = $this->_db->query($sql);

        foreach ($sor_query->rows as $row) 
        {
			$this->_pid_detail_map[$row['product_id']]['is_sor_enabled'] = 1;

			if($this->_pid_detail_map[$row['product_id']]['piece_in_set'] > 1)
              {
                $this->_pid_detail_map[$row['product_id']]['sor_enabled_text'] = 'Buyback Guarantee within '.$row['sor_days'].' Days (Setwise)';
			    $this->_pid_detail_map[$row['product_id']]['is_sor_enabled'] = 'Buyback Guarantee within '.$row['sor_days'].' Days (full set only, not partial)';
              }
              else
              {
                $this->_pid_detail_map[$row['product_id']]['sor_enabled_text'] = 'Buyback Guarantee within '.$row['sor_days'].' Days';
			    $this->_pid_detail_map[$row['product_id']]['is_sor_enabled'] = 'Buyback Guarantee within '.$row['sor_days'].' Days';
              }

			
		}
 
		// Downloads
		$sql = "SELECT * FROM " . DB_PREFIX . "product_to_download p2d
		        LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id)
		        LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id)
		        WHERE p2d.product_id IN (" . $this->_product_ids . ")
		          AND dd.language_id = '" . (int)$this->_config->get('config_language_id') . "'";
		$download_query = $this->_db->query($sql);
		foreach ($download_query->rows as $download) {
			if ( !isset($this->_pid_detail_map[$download['product_id']]['download']) ) {
				$this->_pid_detail_map[$download['product_id']]['download'] = array();
			}
			$this->_pid_detail_map[$download['product_id']]['download'][] = array(
				'download_id' => $download['download_id'],
				'name'        => $download['name'],
				'filename'    => $download['filename'],
				'mask'        => $download['mask']
			);

		}

                foreach ($this->_pid_detail_map as $pid => $detail_row) {
                    if ( empty($detail_row) )
                        continue; // Product does not exist. Skip to next
                    $units = $this->getProductUnit($pid);
                    $this->_pid_detail_map[$pid]['unit_id'] = $units['unit_id'];
                    if(!empty($units['base_unit'])){
                        $this->_pid_detail_map[$pid]['base_unit'] = $units['base_unit'];
                    }
                    else{
                        $this->_pid_detail_map[$pid]['base_unit'] = 'Piece';
                    }
                    if(!empty($units['super_unit'])){
                        $this->_pid_detail_map[$pid]['super_unit'] = $units['super_unit'];
                    }
                    else{
                        $this->_pid_detail_map[$pid]['super_unit'] = 'Set';
                    }
                }

		// Looping over all products to set/adjust few more fields
		foreach ($this->_pid_detail_map as $pid => $detail_row) {
            if ( empty($detail_row) )
                continue; // Product does not exist. Skip to next
			$price_details = self::getPrice(array('product_id' => (int)$pid, 
			                                      'commission' => $detail_row['commission'], 
			                                      'hsn_code'   => $detail_row['hsn_code']
			                                      ), 
			                                $this->_registry
			                               );
			
			$this->_pid_detail_map[$pid]['selling_price'] = $price_details['selling_price'];
			$this->_pid_detail_map[$pid]['commission'] = $price_details['commission'];
			$this->_pid_detail_map[$pid]['transfer_price_per_piece'] = $price_details['transfer_price_per_piece'];

			$this->_pid_detail_map[$pid]['seller_tax_factor'] = $price_details['seller_tax_factor'];
			$this->_pid_detail_map[$pid]['commission_factor'] = $price_details['commission_factor'];
			
			$this->_pid_detail_map[$pid]['seller_tax'] = $price_details['seller_tax'];
			$this->_pid_detail_map[$pid]['output_tax_rates'] = $price_details['output_tax_rates'];
			$this->_pid_detail_map[$pid]['tax_class_id'] = $price_details['tax_class_id'];
		}

	}


	/**
	 * Method to update product to seller in $this->_pid_sid_map.
	 * It also updates the comma separated string of unique seller ids $this->_seller_ids.
	 * @author Madhur, 2016
	 */
	private function _updateProductToSellerMap() {
		// oc_ms_product
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_product
		        WHERE product_id IN (" . $this->_product_ids . ")";
		$omp_query = $this->_db->query($sql);

		foreach ($omp_query->rows as $row) {
			$this->_pid_sid_map[$row['product_id']] = $row['seller_id'];
		}

		// Updating $this->_seller_ids
		$this->_seller_ids = implode(",", array_unique(array_values($this->_pid_sid_map)));
	}

	/**
	 * Method to update seller details in $this->_sid_detail_map.
	 * Details consists of vacation_mode and cod_available for the seller.
	 * Atleast one cod_available = 0 will make $this->_cod_available = false
	 * @author Madhur, 2016
	 */
	private function _updateSellerDetailsMap() {
		$this->_cod_available = true;
		// oc_ms_seller
		$sql = "SELECT seller_id,
		               cod_available,
		               vacation_mode,
		               seller_status,
		               nickname,
		               pickup_city_code,
		               seller_invoice_generate,
		               non_returnable,
		               city 
		        FROM " . DB_PREFIX . "ms_seller
		        WHERE seller_id IN (" . $this->_seller_ids . ")";
		$oms_query = $this->_db->query($sql);

		foreach ($oms_query->rows as $row) {
			foreach ($row as $field => $value) {
				$this->_sid_detail_map[$row['seller_id']][$field] = $value;
			}

			if ( empty($row['cod_available']) ) {
				$this->_cod_available = false;
			}
		}
	}

	/**
	 * Method to update recurring details as per cart product key
	 * in $this->_recur_detail_map
	 * @author Madhur, 2016
	 */
	private function _updateRecurringDetailsMap() {

		foreach ($this->_recur_detail_map as $key => $recurring) {
			if ($recurring) {
				$sql = 	"SELECT * FROM `" . DB_PREFIX . "recurring` `p`
				         JOIN `" . DB_PREFIX . "product_recurring` `pp`
				           ON `pp`.`recurring_id` = `p`.`recurring_id`
				             AND `pp`.`product_id` = " . (int)$recurring['product_id'] . "
                         JOIN `" . DB_PREFIX . "recurring_description` `pd`
                           ON `pd`.`recurring_id` = `p`.`recurring_id`
                             AND `pd`.`language_id` = " . (int)$this->_config->get('config_language_id') . "
                         WHERE `pp`.`recurring_id` = " . (int)$recurring['recurring_id'] . "
                           AND `status` = 1";

				$recurring_query = $this->_db->query($sql);

				if ($recurring_query->num_rows) {
					$this->_recur_detail_map[$key] = array(
													'recurring_id'    => $recurring['recurring_id'],
													'name'            => $recurring_query->row['name'],
													'frequency'       => $recurring_query->row['frequency'],
													'price'           => $recurring_query->row['price'],
													'cycle'           => $recurring_query->row['cycle'],
													'duration'        => $recurring_query->row['duration'],
													'trial'           => $recurring_query->row['trial_status'],
													'trial_frequency' => $recurring_query->row['trial_frequency'],
													'trial_price'     => $recurring_query->row['trial_price'],
													'trial_cycle'     => $recurring_query->row['trial_cycle'],
													'trial_duration'  => $recurring_query->row['trial_duration']
													);
				} else {
					$this->_recur_detail_map[$key] = false;
				}
			}
		}
	}


	/**
	 * Method to update option details as per cart product key
	 * in $this->_option_detail_map
	 * @author Madhur, 2016
	 */
	private function _updateOptionDetailsMap() {

		foreach ($this->_option_detail_map as $key => $option_row) {

			if ( !empty($option_row['options']) ) {

				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;
				$option_data = array();

				foreach ($option_row['options'] as $product_option_id => $value) {

					$sql = "SELECT po.product_option_id, po.option_id, od.name, o.type
					        FROM " . DB_PREFIX . "product_option po
					          LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id)
					          LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)
					        WHERE po.product_option_id = '" . (int)$product_option_id . "'
					          AND po.product_id = '" . (int)$option_row['product_id'] . "'
					          AND od.language_id = '" . (int)$this->_config->get('config_language_id') . "'";
					$option_query = $this->_db->query($sql);

					if ($option_query->num_rows) {

						if ($option_query->row['type'] == 'select'
						    || $option_query->row['type'] == 'radio'
						    || $option_query->row['type'] == 'image') {

							$sql = "SELECT pov.option_value_id,
							               ovd.name,
							               pov.quantity,
							               pov.subtract,
							               pov.price,
							               pov.price_prefix,
							               pov.points,
							               pov.points_prefix,
							               pov.weight,
							               pov.weight_prefix,
							               pov.option_image
							        FROM " . DB_PREFIX . "product_option_value pov
							          LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id)
							          LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)
							        WHERE pov.product_option_value_id = '" . (int)$value . "'
							          AND pov.product_option_id = '" . (int)$product_option_id . "'
							          AND ovd.language_id = '" . (int)$this->_config->get('config_language_id') . "'";
							$option_value_query = $this->_db->query($sql);

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract']
								    && (!$option_value_query->row['quantity']
								        || ($option_value_query->row['quantity'] < $option_row['option_quantity']))) {
									$this->_option_detail_map[$key]['stock'] = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'image'                   => $option_value_query->row['option_image'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$sql = "SELECT pov.option_value_id,
								               ovd.name,
								               pov.quantity,
								               pov.subtract,
								               pov.price,
								               pov.price_prefix,
								               pov.points,
								               pov.points_prefix,
								               pov.weight,
								               pov.weight_prefix
								        FROM " . DB_PREFIX . "product_option_value pov
								          LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id)
								          LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)
								        WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "'
								          AND pov.product_option_id = '" . (int)$product_option_id . "'
								          AND ovd.language_id = '" . (int)$this->_config->get('config_language_id') . "'";
								$option_value_query = $this->_db->query($sql);

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract']
									    && (!$option_value_query->row['quantity']
									        || ($option_value_query->row['quantity'] < $option_row['option_quantity']))) {
										$this->_option_detail_map[$key]['stock'] = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text'
						          || $option_query->row['type'] == 'textarea'
						          || $option_query->row['type'] == 'file'
						          || $option_query->row['type'] == 'date'
						          || $option_query->row['type'] == 'datetime'
						          || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}

				$this->_option_detail_map[$key]['option_price'] = $option_price;
				$this->_option_detail_map[$key]['option_points'] = $option_points;
				$this->_option_detail_map[$key]['option_weight'] = $option_weight;
				$this->_option_detail_map[$key]['option_data'] = $option_data;

			} else {
				$this->_option_detail_map[$key] = false;
			}
		}
	}


	/**
	 * Method to get the cart products ($_cart_data)
	 * Method first checks if the $_cart_data variable is null or not.
	 * If null, it gets the information from database, else the cart data
	 * is returned directly from the internal member (avoiding rehitting database everytime)
	 *
	 * Devendra(31-05-2017): Added one more parameter $all_stock
	 * $all_stock = false(default) means it will return only that products which are in stock.
	 * $all_stock = true means it will return all products of the cart.
	 */
	public function getProducts($user_id='', $all_stock = false) {
		
		$cartdata = $this->_getCustomerCartData($user_id);

	
		if ($cartdata === -1) {
			if($all_stock) return (array)$this->_cart_data;
			else return (array)$this->_in_stock_cart_data;
		}

		// First we get list of unique product, option, and recurring_ids etc
		// This helps in minimizing SQL queries needed to run and optimize DB load
		
		foreach ($cartdata as $key => $quantity) {

			$product = unserialize(base64_decode($key));
			
			$associate_products = $this->__getAssociateProducts($product['product_id']);
			if (!empty($associate_products)) {
				$products = $associate_products;
				$this->_pid_associate_ids_map[$product['product_id']] = $associate_products;
			} else {
				$products[] = $product;
			}
			
			foreach($products as $product) {
				$this->_pid_detail_map[$product['product_id']] = array();
				// Options
				$this->_option_detail_map[$key] = array();
				$this->_option_detail_map[$key]['options'] = array();
				$this->_option_detail_map[$key]['product_id'] = $product['product_id'];
				$this->_option_detail_map[$key]['option_quantity'] = $quantity;
				if ( !empty($product['option']) ) {
					$this->_option_detail_map[$key]['options'] = $product['option'];
				}

				// Profile / Recurring
				$this->_recur_detail_map[$key] = false;
				if ( !empty($product['recurring_id']) ) {
					$this->_recur_detail_map[$key]['recurring_id'] = $product['recurring_id'];
					$this->_recur_detail_map[$key]['product_id'] = $product['product_id'];
				}
			}
		}

		// Updating Maps based on Ids extracted
		$this->_product_ids = implode(",", array_keys($this->_pid_detail_map));
		$this->_updateProductToSellerMap();
		$this->_updateSellerDetailsMap();
		$this->_updateProductDetailsMap(); // It is updated after seller because seller details are also reqd
		$this->_updateRecurringDetailsMap();
		$this->_updateOptionDetailsMap();

		// If cart does not have products from store for which store code is applied,then online products will be shown in stock.
        $apply_store_code = false;
        $coupon_data =  $this->getCoupon() ;
        if( !empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code']) ){
            $apply_store_code = true;
            /*foreach($this->_pid_detail_map as $product){
                if($product['store_sales'] == $coupon_data['wsb_store_code']){
                    $apply_store_code = true;
                    break;
                }
            }*/
        }


        // If special promo code is applied then, we need to calculate total store wise
        $apply_special_promo_code = false;
        $store_wise_price = array();
        if (!empty($coupon_data['coupon_buy_store_product'])){
            $apply_special_promo_code = true;
            //14-09-2017 Devendra: No need to calculate store-wise total, because store-wise min 5000rs limit is removed from now.
            /*foreach ($cartdata as $key => $quantity){
                $product = unserialize(base64_decode($key));
                $product_id = $product['product_id'];
                $price_per_piece = ceil( (($this->_pid_detail_map[$product_id]['transfer_price_per_piece'] + $this->_option_detail_map[$key]['option_price'])
                        * $this->_pid_detail_map[$product_id]['commission_factor'])
                    / $this->_pid_detail_map[$product_id]['seller_tax_factor'] );
                $product_total = $price_per_piece * $this->_pid_detail_map[$product_id]['piece_in_set'] * $quantity;
                $store_code = $this->_pid_detail_map[$product_id]['store_sales'];
                if(isset($store_wise_price[$store_code])){
                    $store_wise_price[$store_code] += $product_total;
                }
                else{
                    $store_wise_price[$store_code] = $product_total;
                }

            }*/
        }

        // checking for store trial coupon, This coupon is applicable for delhi store only.
        $apply_store_trial_code = false;
        if( !empty($coupon_data['wsb_store_delivery']) && !empty($coupon_data['wsb_store_code']) ){
            $apply_store_trial_code = true;
            /*foreach($this->_pid_detail_map as $product){
                if($product['store_sales'] == $coupon_data['wsb_store_code']){
                    $apply_store_trial_code = true;
                    break;
                }
            }*/
        }

        // check for bangalore+online coupon code
        $apply_bl_on_code=false;
        if(!empty($coupon_data['coupon_bl_on_store'])){
            $apply_bl_on_code = true;
        }

        // comma separated list to store products which are empty (i.e. products which are deleted after adding to the cart)
        $remove_empty_products_list = '';

				foreach ($cartdata as $key => $quantity) {
					$products = array();
          $product = unserialize(base64_decode($key));
					$combo_product_id = $product['product_id'];
					if (isset($this->_pid_associate_ids_map[$product['product_id']])) {
						$products = $this->_pid_associate_ids_map[$product['product_id']];
					} else {
						$products[] = $product;
					}
					
					$i = 0;
					foreach($products as $product) {
						$i++;
						$product_id = $product['product_id'];
						if(!is_null($this->_product_comments) && isset($this->_product_comments[$key]))
						    $comment = $this->_product_comments[$key];
						else
						    $comment = '';

						if ( empty($this->_pid_detail_map[$product_id]) ){
                $remove_empty_products_list .= $key.',';
                continue; // Product does not exist. Skip to next
            }

            $price_details = self::getPrice(array('price' => $this->_pid_detail_map[$product_id]['price'],
                'option_price' => $this->_option_detail_map[$key]['option_price'],
                'mrp' => $this->_pid_detail_map[$product_id]['mrp'],
                'product_id' => (int)$product_id,
                'commission' => $this->_pid_detail_map[$product_id]['commission'],
                'hsn_code'   => $this->_pid_detail_map[$product_id]['hsn_code'],
                'hidden_selling_price'   => $this->_pid_detail_map[$product_id]['hidden_selling_price'],
            ),
                $this->_registry
            );

            $this->_pid_detail_map[$product_id]['selling_price'] = $price_details['selling_price'];
            $this->_pid_detail_map[$product_id]['commission'] = $price_details['commission'];
            $this->_pid_detail_map[$product_id]['transfer_price_per_piece'] = $price_details['transfer_price_per_piece'];

            $this->_pid_detail_map[$product_id]['seller_tax_factor'] = $price_details['seller_tax_factor'];
            $this->_pid_detail_map[$product_id]['commission_factor'] = $price_details['commission_factor'];

            $this->_pid_detail_map[$product_id]['seller_tax'] = $price_details['seller_tax'];
            $this->_pid_detail_map[$product_id]['output_tax_rates'] = $price_details['output_tax_rates'];
            $this->_pid_detail_map[$product_id]['tax_class_id'] = $price_details['tax_class_id'];

						//if the product is WSB Purchased Inventory (oc_ms_seller.seller_invoice_generate = 0), 
						// then in transfer_price_per_piece, we will always use the price from oc_product.
						if( (int)$this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['seller_invoice_generate'] == 0 ){
							$this->_pid_detail_map[$product_id]['transfer_price_per_piece'] = $this->_pid_detail_map[$product_id]['price'];
						}

						// Check for stock
						$stock = true;
						$donot_have_enough_qty = false; //need this variable becuse we do not need to send stock == false inc case of $this->_pid_detail_map[$product_id]['quantity'] < $quantity
						$this->_has_stock = true;

						$stock_quantity = $this->_pid_detail_map[$product_id]['quantity'];

						// if product quantity is less than MOQ, then set the MOQ equals to quantity
            $this->_pid_detail_map[$product_id]['minimum'] = ($stock_quantity > $this->_pid_detail_map[$product_id]['minimum'])?$this->_pid_detail_map[$product_id]['minimum']:max(1,$stock_quantity);

            // If product is having option, then set the avilable quantity to the available quantity of the particular option
            if(!empty($this->_option_detail_map[$key]['option_data'][0]) ){
                $stock_quantity = $this->_option_detail_map[$key]['option_data'][0]['quantity'];
            }

						$output_array = array();
            $this->_pid_detail_map[$product_id]['seller_status'] =  $this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['seller_status'];
            $this->_pid_detail_map[$product_id]['vacation_mode'] =  $this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['vacation_mode'];

            $product_data = $this->_pid_detail_map[$product_id];
            $product_data['quantity'] = $stock_quantity;

            $output_array = self::getProductStockStatus($product_data);
			
						if ( isset($output_array['stock'])
						     && $output_array['stock'] === false) {
							$stock = false;
						} else if (isset($output_array['stock'])
						     && $output_array['stock'] === true ){

                if (($stock_quantity < $quantity)) {
                    $stock = false;
                    $donot_have_enough_qty = true;
                }else{
                    $stock = true;
                }
						}
            $price_per_piece = $this->_pid_detail_map[$product_id]['selling_price'];
            
						$weight_per_piece = $this->_pid_detail_map[$product_id]['weight'] + $this->_option_detail_map[$key]['option_weight'];

						// If this item is downloadable, set it to true in this object
						$download = false;
						if ( !empty($this->_pid_detail_map[$product_id]['download'])
						     && !$this->_has_downloads ) {
							$download = $this->_pid_detail_map[$product_id]['download'];
							$this->_has_downloads = true;
						}

						// If this item is to be shipped
						if ( !empty($this->_pid_detail_map[$product_id]['shipping'])
						     && !$this->_has_shipping ) {
							$this->_has_shipping = true;
						}


            $store_pickup = false;
            $wrong_store = false;
            $error_store_limit = false;
            $error_store_limit_msg = '';

            $cart_limit = (isset($this->_customer->is_dropshipper) && $this->_customer->is_dropshipper == 1)?$this->_config->get('config_dropshipper_cart_limit'):$this->_config->get('config_cart_limit');
            // checking if store voucher is not available and
            // product is not from jp and online
            if( $apply_store_code ){
                if( $this->_pid_detail_map[$product_id]['store_sales'] == $coupon_data['wsb_store_code'] ){
                    $store_pickup = true;
                }
                else{
                    $wrong_store = true;
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
                }
            }
            else if($apply_special_promo_code){
                //14-09-2017 Devendra:  Removed store-wise 5000 limit and making bangalore store products out of stock
                // 22-12-2017 Devendra: from now customer can purchase from any store, so commenting below bangalore store check
                /*$store_code = $this->_pid_detail_map[$product_id]['store_sales'];
                if($store_code == 'BLR' || $store_code == 'BL'){
                    $wrong_store = true;
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $donot_have_enough_qty = false;
                }*/
                /*$store = !empty($this->_pickup_city_map[$store_code])?$this->_pickup_city_map[$store_code]:$store_code;
                if($store == 'NO') $store = "Online";
                if((float)$store_wise_price[$store_code] < (float)$cart_limit){
                    $error_store_limit = true;
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
                    if($this->_registry->get('currency') != null)
                        $amount_text = $this->_registry->get('currency')->format( (float)$cart_limit - (float)$store_wise_price[$store_code] );
                    else
                        $amount_text = (float)$cart_limit - (float)$store_wise_price[$store_code];
                    $error_store_limit_msg = 'Please add more products from '.$store .' store inventory worth of '.( $amount_text ) .' to purchase this product, otherwise this product will not be included in your order.';
                }*/
            }
            else if($apply_store_trial_code){
                if( $this->_pid_detail_map[$product_id]['store_sales'] == $coupon_data['wsb_store_code'] ){
                    //
                }
                else{
                    $wrong_store = true;
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
                }
            }
            else if($apply_bl_on_code){
                if( !in_array( $this->_pid_detail_map[$product_id]['store_sales'] , array( 'NO' , 'BL' , 'BLR' )) ) {
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $wrong_store = true;
                    $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
                }
            }
            else {
                if( empty($coupon_data['coupon_franchise']) && $this->_pid_detail_map[$product_id]['store_sales'] != 'NO' && !in_array( $this->_pid_detail_map[$product_id]['store_sales'] , STORES_OPEN_FOR_ONLINE_ORDER) ) {
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $wrong_store = true;
                    $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
                }
            }

            // Check if franchise coupon(for customers of franchise) is applied,
            // 1. If not, and product belongs to franchise, then will be treated as out of stock.
            // 2. If yes, and product does not belongs to franchise, then will be treated as out of stock.
            if(!empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit'])){
                // If cart contains at least one product of the current franchise, then will mark online and other franchise products as out of stock
                if( (array_key_exists($this->_franchise_id,$this->_fid_pid_map) && count($this->_fid_pid_map[$this->_franchise_id]) > 0) &&
                    (empty($this->_pid_detail_map[$product_id]['franchise_id']) || ((int)$this->_pid_detail_map[$product_id]['franchise_id'] != (int)$this->_franchise_id) ) ){
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $wrong_store = true;
                    $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
                }
                else if(!empty($coupon_data['coupon_franchise_credit']) && ((int)$this->_pid_detail_map[$product_id]['franchise_id'] == (int)$this->_franchise_id) ){
                    // If franchise credit coupon is applied, then price will be updated according to franchise margin.
                    $update_price_by = 1 + (float)$this->_franchise_margin/100.0;
                    $price_per_piece = $price_per_piece*$update_price_by;
                }

            }
            else if( !empty($this->_pid_detail_map[$product_id]['franchise_id']) ){
                $stock = false;
                $this->_pid_detail_map[$product_id]['status'] = $stock;
                $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
            }

            // Check if coupon for franchise purchase is applied, if yes then franchise can only purchase wsb purchased inventory
            if(!empty($coupon_data['coupon_franchise']) && (int)$this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['seller_invoice_generate'] != 0){
                $stock = false;
                $this->_pid_detail_map[$product_id]['status'] = $stock;
                $donot_have_enough_qty = false; //Setting this to false, because here quantity is not the reason behind being out of stock.
            }

            // check if jp a to z inventory discount coupon code applied
            if(!empty($coupon_data['coupon_az_discount_five']) || !empty($coupon_data['coupon_az_discount_seven'])){
                if ($this->_pid_detail_map[$product_id]['store_sales'] == 'JP'
                    && strpos($this->_pid_detail_map[$product_id]['model'], 'JPSAZ') !== false) {
                    $store_pickup = true;
                } else {
                    $stock = false;
                    $this->_pid_detail_map[$product_id]['status'] = $stock;
                    $donot_have_enough_qty = false;
                }
            }

            $product_total = $this->_tax->calculate($price_per_piece ,$this->_pid_detail_map[$product_id]['tax_class_id'], $this->_config->get('config_tax'), $this->_pid_detail_map[$product_id]['mrp'])* $this->_pid_detail_map[$product_id]['piece_in_set'] * $quantity;
						$tax_class_id = $this->_pid_detail_map[$product_id]['tax_class_id'];


            /*-- diff between two date for get exp_dispatch_date and show on particular product */

            $exp_final_date = 0;
            if((bool)(strtotime($this->_pid_detail_map[$product_id]['expected_dispatch_date']))){
                if(isset($this->_pid_detail_map[$product_id]['expected_dispatch_date']) && $this->_pid_detail_map[$product_id]['expected_dispatch_date'] != '0000-00-00'){
                    $exp_dis_date = strtotime($this->_pid_detail_map[$product_id]['expected_dispatch_date']);
                    $today_date = strtotime(date('Y-m-d'));
                    $date_diff = $exp_dis_date - $today_date;
                    $exp_final_date = (int)($date_diff / (60 * 60 * 24));
                }
            }else{
                $exp_final_date = (int) $this->_pid_detail_map[$product_id]['expected_dispatch_date'];
            }

            if($exp_final_date > '1' && !empty($exp_final_date)){
                $exp_dispatch_date =  "Available after ".$exp_final_date. " Days";
            }else{
                $exp_dispatch_date = '';
            }

            if($this->_pid_detail_map[$product_id]['store_sales'] != 'NO')
                $pickupcity_code = $this->_pid_detail_map[$product_id]['store_sales'];
            else
                $pickupcity_code = $this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['pickup_city_code'];

            if(!empty($this->_pickup_city_map[$pickupcity_code])){
                $pickupcity = $this->_pickup_city_map[$pickupcity_code];
            }
            else{
                $pickupcity = $pickupcity_code;
            }

            $cod_available = ((int)$this->_pid_detail_map[$product_id]['cod_available'] & (int)$this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['cod_available']);

            $set_description = !empty($this->_pid_detail_map[$product_id]['set_description']) ? $this->_pid_detail_map[$product_id]['set_description'] : ''; 

            $notes = '';
            // If this product is purchased inventory, then we will check whether it belongs to 'STORE30' inventory or not
            if( (int)$this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['seller_invoice_generate'] == 0 ){
                $purchase_date_diff = (int)$this->_getWSBPurchasedProductDateDiff($product_id);
                $notes = $purchase_date_diff;
            }

            // check for non-returnable
            // if any of seller or category or product is marked as non-returnable, then will treat the product as non-returnable.
            $non_returnable =  ( $this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['non_returnable'] |
                                    $this->getCategoryLevelNonReturnable($this->_db,$product_id) |
                                    $this->_pid_detail_map[$product_id]['non_returnable'] ) ;

            $this->_cart_data[$key . '_' . $i] = array(
							'key'             => $key,
							'product_id'      => $product_id,
							'category_id'     => $this->_pid_detail_map[$product_id]['category_id'],
							'name'            => !empty($this->_pid_detail_map[$product_id]['name']) ? $this->_pid_detail_map[$product_id]['name'] : '',
							'is_sor_enabled'  => !empty($this->_pid_detail_map[$product_id]['is_sor_enabled']) ? $this->_pid_detail_map[$product_id]['is_sor_enabled'] : 0,
							'sor_enabled_text'  => !empty($this->_pid_detail_map[$product_id]['sor_enabled_text']) ? $this->_pid_detail_map[$product_id]['sor_enabled_text'] : '',
							'sor_enabled_detail_text'  => !empty($this->_pid_detail_map[$product_id]['sor_enabled_detail_text']) ? $this->_pid_detail_map[$product_id]['sor_enabled_detail_text'] : '',
							'set_description' => $set_description,
							'model'           => $this->_pid_detail_map[$product_id]['model'],
							'rating'          => $this->_pid_detail_map[$product_id]['rating'],
							'sku'             => $this->_pid_detail_map[$product_id]['sku'],
							'shipping'        => $this->_pid_detail_map[$product_id]['shipping'],
							'image'           => $this->_pid_detail_map[$product_id]['image'],
							'hsn_code'        => $this->_pid_detail_map[$product_id]['hsn_code'],
							'option'          => !empty($this->_option_detail_map[$key]) ? $this->_option_detail_map[$key]['option_data'] : array(),
							'download'        => $download,
							'quantity'        => $quantity,
							'piece_in_set'    => $this->_pid_detail_map[$product_id]['piece_in_set'],
							'total_pieces'    => $this->_pid_detail_map[$product_id]['piece_in_set'] * $quantity,
							'stock_quantity'  => $stock_quantity,
							'minimum'         => $this->_pid_detail_map[$product_id]['minimum'],
							'subtract'        => $this->_pid_detail_map[$product_id]['subtract'],
							'status'          => $this->_pid_detail_map[$product_id]['status'],
							'stock'           => $stock,
							'donot_have_enough_qty' => $donot_have_enough_qty,
							'price'           => $price_per_piece * $this->_pid_detail_map[$product_id]['piece_in_set'],
							'price_per_piece' => $price_per_piece,
				      'mrp'             => !empty($this->_pid_detail_map[$product_id]['mrp']) ? $this->_pid_detail_map[$product_id]['mrp'] : '' ,
							'total'           => $product_total,
							'points'          => ($this->_pid_detail_map[$product_id]['points'] ? ($this->_pid_detail_map[$product_id]['points'] + $option_points) * $quantity : 0),
							'tax_class_id'    => $tax_class_id,
							'weight_per_piece'=> $weight_per_piece,
							'weight'          => $weight_per_piece * $this->_pid_detail_map[$product_id]['piece_in_set'] * $quantity,
							'weight_class_id' => $this->_pid_detail_map[$product_id]['weight_class_id'],
							'length'          => $this->_pid_detail_map[$product_id]['length'],
							'width'           => $this->_pid_detail_map[$product_id]['width'],
							'height'          => $this->_pid_detail_map[$product_id]['height'],
							'length_class_id' => $this->_pid_detail_map[$product_id]['length_class_id'],
							'recurring'       => $this->_recur_detail_map[$key],
							'seller_id'       => $this->_pid_sid_map[$product_id],
							'seller_nickname' => $this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['nickname'],
							'seller_pickup_city_code' => strtoupper(trim($pickupcity_code)),
							'selling_price'   => $this->_pid_detail_map[$product_id]['selling_price'],
							'is_single'       => $this->_pid_detail_map[$product_id]['is_single'],
							'cod_available'   => $cod_available,
							'seller_tax'      => $this->_pid_detail_map[$product_id]['seller_tax'],
							'commission'      => $this->_pid_detail_map[$product_id]['commission'],
            	'discount_breakup'=> 0,
							'discount_per_piece' => 0,
            	'store_sales' => isset($this->_pid_detail_map[$product_id]['store_sales'])?$this->_pid_detail_map[$product_id]['store_sales']:'',
							'tax' => ($this->_tax->getTax( $price_per_piece ,$this->_pid_detail_map[$product_id]['hsn_code'], '', '', $this->_pid_detail_map[$product_id]['mrp']))*$quantity* $this->_pid_detail_map[$product_id]['piece_in_set'],
	            'pickup_city' => strtoupper(trim($pickupcity)),
	            'comment' => $comment,
	            'exp_dispatch_date' => $exp_dispatch_date,
	            'store_pickup' => $store_pickup,
	            'wrong_store' => $wrong_store, 
	            'transfer_price_per_piece' => $this->_pid_detail_map[$product_id]['transfer_price_per_piece'],
	            'output_tax_rates' => $this->_pid_detail_map[$product_id]['output_tax_rates'],
	            'error_store_limit' => $error_store_limit,
	            'error_store_limit_msg' => $error_store_limit_msg,
	            'base_unit' => $this->_pid_detail_map[$product_id]['base_unit'],
	            'super_unit' => $this->_pid_detail_map[$product_id]['super_unit'],
	            'unit_id' => $this->_pid_detail_map[$product_id]['unit_id'],
	            'sor_product'      => $this->_pid_detail_map[$product_id]['sor_product'],
							'wsb_purchase_id'      => $this->_pid_detail_map[$product_id]['wsb_purchase_id'],
	            'is_archived' => $this->_pid_detail_map[$product_id]['is_archived'],
	            'franchise_id'      => $this->_pid_detail_map[$product_id]['franchise_id'],
	            'notes' => $notes,
	            'non_returnable' => $non_returnable,
	            'seller_city' => $this->_sid_detail_map[$this->_pid_sid_map[$product_id]]['city'],
							'combo_product_id' => $combo_product_id
						);
						
						if ($combo_product_id != $product_id) {
							// here store the first associate products stock
							if (!isset($this->_combo_product_stock_status_map[$combo_product_id])) {
								$this->_combo_product_stock_status_map[$combo_product_id]['stock'] = $stock;
								$this->_combo_product_stock_status_map[$combo_product_id]['stock_quantity'] = $stock_quantity;
							} else {
								if (!$stock) {
									// for rest associate products if stock is false, then only update the status
									$this->_combo_product_stock_status_map[$combo_product_id]['stock'] = $stock;
								}
								// if current associate product quantity is less than prev, then update the stock quantity.
								if ($this->_combo_product_stock_status_map[$combo_product_id]['stock_quantity'] > $stock_quantity) {
									$this->_combo_product_stock_status_map[$combo_product_id]['stock_quantity'] = $stock_quantity;
								}
							}
							$this->_combo_product_stock_status_map[$combo_product_id]['keys'][] = $key . '_' . $i;
						}
		
						if($stock){
							$this->_in_stock_cart_data[$key . '_' . $i] = $this->_cart_data[$key . '_' . $i];
						}
					}
				}

				// now update the stock status of combo products
				foreach($this->_combo_product_stock_status_map as $combo_product) {
					foreach($combo_product['keys'] as $key) {
						$this->_cart_data[$key]['stock_quantity'] = $combo_product['stock_quantity'];
						// if only one associate product, then mark as out of stock
						if (sizeof($combo_product['keys']) == 1) {
							$combo_product['stock'] = false;
						}
						$this->_cart_data[$key]['stock'] = $combo_product['stock'];
						if (!$combo_product['stock']) {
							// if out of stock then unset it from stock cart data
							unset($this->_in_stock_cart_data[$key]);
						}
					}
				}
				
				if(!empty($remove_empty_products_list)){
		    	$this->removeProducts($remove_empty_products_list);
        }

		if($all_stock) {
			
			return (array)$this->_cart_data;
		}
		else {
			return (array)$this->_in_stock_cart_data;
		}
			
	}

	public function getRecurringProducts($user_id='', $all_stock = false) {
		$recurring_products = array();

		foreach ($this->getProducts($user_id, $all_stock) as $key => $value) {
			if ($value['recurring']) {
				$recurring_products[$key] = $value;
			}
		}

		return $recurring_products;
	}

	public function add($product_id, $qty = 1, $option = array(), $recurring_id = 0) {
		$this->_cart_data = null;
		$this->_in_stock_cart_data = null;
		$customer_id = $this->_customer->getId();
		if(isset($option) && !empty($option)){
			$product_option = key($option);
			$product_option_value = $option[key($option)];
		}else{
			$product_option = '';
			$product_option_value = '';
		}

		$cart_modified_from = 'DWEB';
		if(CONFIG_IS_MOBILE == 1){
            $cart_modified_from = 'MWEB';
        }

		if($this->_customer->isLogged()){
			$data = array('customer_id' => $customer_id,'access_token' => $this->_customer->getAccessToken(),'product_id' => $product_id,'product_quantity' => $qty,'product_option' => $product_option,'product_option_value' => $product_option_value,'cart_modified_from' => $cart_modified_from);
		}else{
             
            if(isset($_COOKIE['cart_session_id']) && !empty($_COOKIE['cart_session_id']))
            {
             	$cart_session_id = $_COOKIE['cart_session_id'];
            }
            else if(isset($_REQUEST['cart_session_id']) && !empty($_REQUEST['cart_session_id']))
             {
             	$cart_session_id = $_REQUEST['cart_session_id'];
             }
             else
             {
             	$cart_session_id = '';
             }
             

			$data = array('cart_session_id' => $cart_session_id,'product_id' => $product_id,'product_quantity' => $qty,'product_option' => $product_option,'product_option_value' => $product_option_value,'cart_modified_from' => $cart_modified_from);
		}

        $this->addToCart($data);

	}

	public function update($key, $qty) {
		$this->_cart_data = null;
		$this->_in_stock_cart_data = null;
		$user_id = $this->_customer->getId();

        $cart_modified_from = 'DWEB';
        if(CONFIG_IS_MOBILE == 1){
            $cart_modified_from = 'MWEB';
        }

        if($this->_customer->isLogged()){
			$data = array('user_id' => $user_id,'access_token' => $this->_customer->getAccessToken(),'key' => $key,'quantity' => $qty,'cart_modified_from' => $cart_modified_from);
		}elseif(isset($_COOKIE['cart_session_id'])){
			$data = array('cart_session_id' => $_COOKIE['cart_session_id'],'key' => $key,'quantity' => $qty,'cart_modified_from' => $cart_modified_from);
		}
		elseif(isset($_REQUEST['cart_session_id'])){
			$data = array('cart_session_id' => $_REQUEST['cart_session_id'],'key' => $key,'quantity' => $qty,'cart_modified_from' => $cart_modified_from);
		}

		$this->updateCart($data);
	}

	public function remove($key) {
		$this->_cart_data = null;
		$this->_in_stock_cart_data = null;

        $cart_modified_from = 'DWEB';
        if(CONFIG_IS_MOBILE == 1){
            $cart_modified_from = 'MWEB';
        }

        $user_id = $this->_customer->getId();

		if($this->_customer->isLogged()){
			$data = array('user_id' => $user_id,'access_token' => $this->_customer->getAccessToken(),'key' => $key,'cart_modified_from' => $cart_modified_from);
		}else if(isset($_COOKIE['cart_session_id'])){
			$data = array('cart_session_id' => $_COOKIE['cart_session_id'],'key' => $key,'cart_modified_from' => $cart_modified_from);
		}else if(isset($_REQUEST['cart_session_id'])){
			$data = array('cart_session_id' => $_REQUEST['cart_session_id'],'key' => $key,'cart_modified_from' => $cart_modified_from);
		}

        $product = unserialize(base64_decode($key));
        $product_hotness = new ProductHotness($this->_db);
        $product_hotness->updateHotness("remove-from-cart",$product['product_id']);

		$this->removeCart($data);
	}

    public function addComment($key, $comment) {

        $this->_cart_data = null;
        $this->_in_stock_cart_data = null;

        $user_id = $this->_customer->getId();

        if($this->_customer->isLogged()){
            $conditions = 'customer_id = '. (int)$user_id .' order by date_modified desc limit 1';
        }else if(isset($_COOKIE['cart_session_id'])){
            $conditions = 'cart_session_id = "'. $this->_db->escape($_COOKIE['cart_session_id']) .'" order by date_modified desc limit 1';
        }
        else if(isset($_REQUEST['cart_session_id'])){
            $conditions = 'cart_session_id = "'. $this->_db->escape($_REQUEST['cart_session_id']) .'" order by date_modified desc limit 1';
        }

        $customer_cart_data = $this->get_customer_cart($conditions);

        if (!empty($customer_cart_data)) {
            $ccdata = $customer_cart_data[0];
            $ccdata['product_comments'] = unserialize($ccdata['product_comments']);
            $ccdata['product_comments'][$key] = $comment;

            $cart_id = $ccdata['id'];
            $product_comments = serialize($ccdata['product_comments']);
            $sql = "UPDATE ".DB_PREFIX."customer_cart 
                    SET `product_comments`='".$this->_db->escape($product_comments)."',
                    `date_modified` = NOW()
                    WHERE `oc_customer_cart`.`id` = ".(int)$cart_id;

            if($this->_db->query($sql)){
                return true;
            }else{
                return false;
            }

        }
    }


	public function hasCOD($user_id='', $all_stock = false) {
		$cod = true;
		$products = $this->getProducts($user_id, $all_stock);
		foreach ($products as $product) {
			if ($product['cod_available'] == 0) {
				$cod = false;

				break;
			}
		}

		return $cod;
	}
	public function clear($extra = array()) {

		$this->_cart_data = null;

		$this->_in_stock_cart_data = null;
		if(isset($this->_session->data['order_id'])){
			$extra['cart_type'] = 'ordered';
			$extra['order_number'] = $this->_session->data['order_id'];
			//$this->cart->clear($extra);
		}

		if(isset($extra['cart_type'])){
			$cart_type = $extra['cart_type'];
		}else{
			$cart_type = 'clear_cart';
		}
		if(isset($extra['order_number'])){
			$order_number = $extra['order_number'];
		}else{
			$order_number = 0;
		}
		$user_id = $this->_customer->getId();

        $cart_modified_from = 'DWEB';
        if(CONFIG_IS_MOBILE == 1){
            $cart_modified_from = 'MWEB';
        }

		if(isset($user_id) && !empty($user_id)){
			$data = array('user_id' => $user_id,'access_token' => $this->_customer->getAccessToken(),'cart_type' => $cart_type,'order_number' => $order_number,'cart_modified_from' => $cart_modified_from);
		}elseif(isset($_COOKIE['cart_session_id'])){
			$data = array('cart_session_id' => $_COOKIE['cart_session_id'],'cart_type' => $cart_type,'order_number' => $order_number,'cart_modified_from' => $cart_modified_from);
		}
		elseif(isset($_REQUEST['cart_session_id'])){
			$data = array('cart_session_id' => $_REQUEST['cart_session_id'],'cart_type' => $cart_type,'order_number' => $order_number,'cart_modified_from' => $cart_modified_from);
		}
		
        $this->clear_cart($data);

	}


	/**
	 * Method to get total weight of the cart
	 * as per defined weight_class_id in config.
	 */
	public function getWeight($user_id='', $all_stock = false) {
		$weight = 0;
		$products = $this->getProducts($user_id, $all_stock);
		foreach ($products as $product) {
			if ($product['shipping']) {

				$weight += $this->_weight->convert($product['weight'],
				                                   $product['weight_class_id'],
				                                   $this->_config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}


	public function getSubTotal($user_id = '', $all_stock = false) {
		$total = 0;
		$products = $this->getProducts($user_id, $all_stock);
		foreach ($products as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	/** Deal Of teh day code (Ravindra Singh 22-01-2016) Start**/
	public function getSubTotalAccordingSeller($seller_id = 0, $all_stock = false) {
		$total = 0;
		$products = $this->getProducts($user_id, $all_stock);
		foreach ($products as $product) {
			//if(in_array($product['seller_id'],$sellers)){
			if($product['seller_id'] == $seller_id){
				$total += $product['total'];
			}
		}

		return $total;
	}
	/** Deal Of teh day code (Ravindra Singh 22-01-2016) End**/

	public function getTaxes($user_id = '', $all_stock = false) {
		$tax_data = array();
		$products = $this->getProducts($user_id, $all_stock);
		
		foreach ($products as $product) {
			if ($product['hsn_code']) {

				$tax_rates = $this->_tax->getRates($product['price_per_piece'], $product['tax_class_id'], '',$product['mrp']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity'] * $product['piece_in_set']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity'] * $product['piece_in_set']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getCST($cst_class_id, $all_stock = false) {
		$cst = 0.0;
        return $cst;
		$products = $this->getProducts('', $all_stock);
		foreach ($products as $product) {
			if ($product['tax_class_id']) {
				$cst += ( $product['quantity'] * $this->_tax->getCST($product['price'], $product['tax_class_id'], $cst_class_id) );
			}
		}

		return $cst;
	}

	public function getTotal($user_id='', $all_stock = false) {
		$total = 0;
		$products = $this->getProducts($user_id, $all_stock);
		foreach ($products as $product) {
			$total += $this->_tax->calculate($product['price'], $product['tax_class_id'], $this->_config->get('config_tax'), $product['mrp']) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts($user_id='', $all_stock = true) {

		$total_sets = 0; // Initializing

        $cartdata = $this->_getCustomerCartData($user_id);

        if ($cartdata === -1) {
            // Count by looping over the already filled cart product data
            if(empty($this->_cart_data)){
                return 0;
            }
            else {
                foreach ($this->_cart_data as $product) {
                    $total_sets += (int)$product['quantity'];
                }
            }
        }else{
            // Count by looping over the already filled cart product data
            foreach ($cartdata as $quantity) {
                $total_sets += (int)$quantity;
            }
        }

		return $total_sets;
	}

	public function hasProducts($user_id = '') {
		//return count($this->_session->data['cart']);
		$cartdata = array();
		if(!empty($user_id)){
			$query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id = '" . $this->_db->escape($user_id) . "'");
			//$query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->_db->escape($user_id) . "'");
		}else{
			$user_id = $this->_customer->getId();
			if(!empty($user_id)){
				$query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id = '" . $this->_db->escape($user_id) . "'");
			}else if(isset($_COOKIE['cart_session_id'])){
                $cart_session_id = $_COOKIE['cart_session_id'];
                $query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'");
			}
			else if(isset($_REQUEST['cart_session_id'])){
                $cart_session_id = $_REQUEST['cart_session_id'];
                $query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'");
			}
			else
			{
				return count($cartdata);
			}
		}
		//$query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id = '" . $this->_db->escape($user_id) . "'");
		if(isset($query->row['cart_data'])){
			$cartdata = unserialize($query->row['cart_data']);
		}
		return count($cartdata);
	}

	public function hasRecurringProducts($user_id='', $all_stock = false) {
		return count($this->getRecurringProducts($user_id, $all_stock));
	}

	/**
	 * Method to check if all the items in cart in stock or not.
	 * Returns false, even if one items is not in stock, else true.
	 */
	public function hasStock($user_id='') {
		if (!isset($this->_has_stock)) {
			$this->getProducts($user_id);
		}
		return $this->_has_stock;
	}

	/**
	 * Method to check if the cart has items to be shipped.
	 */
	public function hasShipping($user_id='') {
		if (!isset($this->_has_shipping)) {
			$this->getProducts($user_id);
		}
		return $this->_has_shipping;
	}


	/**
	 * Method to check if the cart has downloadable items.
	 */
	public function hasDownload($user_id='') {
		if (!isset($this->_has_downloads)) {
			$this->getProducts($user_id);
		}
		return $this->_has_downloads;
	}


	// @todo: this function should not be here.
    // Calling function must refer to customer class
	public function is_dropshipper($user_id){
		$rt = 0;
		$customer_query = $this->_db->query("SELECT is_dropshipper FROM " . DB_PREFIX . "customer
		                                     WHERE customer_id = '" . (int)$user_id . "'");
		if(isset($customer_query->row['is_dropshipper'])){
			$rt = $customer_query->row['is_dropshipper'];
		}
		return $rt;
	}


	// @todo: this function should not be here.
    // Calling function must refer to credit class from order totals
	public function getBalance($user_id) {
		$query = $this->_db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$user_id . "'");

		return $query->row['total'];
	}


    // @todo: this function should not be here.
    // Calling function must refer to cashback class from order totals
    public function getCashbackAvailable($user_id) {
		$query = $this->_db->query("SELECT SUM(amount - amount_utilized) AS total FROM " . DB_PREFIX . "customer_cashback
                                   WHERE customer_id = '" . (int)$user_id . "'
                                     AND expired = 0
                                     AND amount > 0
                                     AND (amount - amount_utilized) > 0");

		return $query->row['total'];
	}

    public function get_customer_cart($conditions){
        $query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE ".$conditions, true);
        return $query->rows;
    }

    public function addToCart($request)
    {
        if(!isset($request['product_id']) || !isset($request['product_quantity']) || (int)$request['product_quantity'] < 1 ){
            return false;
        }

        $record_update = 0;
        $cart_session_id = '0';
        $parent_cart_id = -1;
        $ip = '';
        $app_version = 0;
        $customer_id = $this->_customer->getId();
        $user_agent='';
        $last_cart_modified_from = (isset($request['cart_modified_from'])?$request['cart_modified_from']:'DWEB');
        $cart_modified_once_from = array();
        if (isset($request['user_agent'])) {
            $user_agent = $request['user_agent'];
        }

        if ($this->_customer->isLogged()) {
            $conditions = 'customer_id = ' . (int)$customer_id . ' order by date_modified desc limit 1';
        } else {
            $cart_session_id = $request['cart_session_id'];
            $conditions = " cart_session_id = '" . $this->_db->escape($cart_session_id) . "' order by date_modified desc limit 1";
        }
        $customer_cart_data = $this->get_customer_cart($conditions);
        if (!empty($customer_cart_data)) {
            $record_update = 1;
            $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
        }
        $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
        $cart_modified_once_from = serialize($cart_modified_once_from);

        $product_id = $request['product_id'];
        $product_quantity = $request['product_quantity'];

        if (isset($request['product_option'])) {
            $product_option_id = $request['product_option'];
            // echo "<pre>";print_r($product_option_id);die;
        } else {
            $product_option_id = '';
        }
        if (isset($request['product_option_value'])) {
            $product_option_value = $request['product_option_value'];
        } else {
            $product_option_value = '';
        }

        if (isset($request['ip'])) {
            $product_cart['ip'] = $request['ip'];
            $ip = $request['ip'];
        }
        if (isset($request['added_by_id'])) {
            $product_cart['added_by_id'] = $request['added_by_id'];
        }

        if (isset($request['added_by_name'])) {
            $product_cart['added_by_name'] = $request['added_by_name'];
        }

        $product_cart['added_time'] = Date('Y-m-d H:i:s');
        $product['product_id'] = (int)$product_id;
        $product_cart['product_id'] = (int)$product_id;
        if (!empty($product_option_id)) {
            $option = array($product_option_id => (string)$product_option_value);
            $product['option'] = $option;
            $product_cart['option'] = $option;
        }
        $key = base64_encode(serialize($product));
        $product_cart_key = base64_encode(serialize($product_cart));

        $update_hotness = 1;
        $cart_id = 0;
        if (!empty($customer_cart_data)) {
            $ccdata = $customer_cart_data[0];
            $ccdata['cart_data'] = unserialize($ccdata['cart_data']);
            $ccdata['cart_history'] = unserialize($ccdata['cart_history']);
            //echo "<pre>"; print_r(unserialize($ccdata['cart_data'])); exit;
            if (!isset($ccdata['cart_data'][$key])) {
                $ccdata['cart_data'][$key] = (int)$product_quantity;
            } else {
                $ccdata['cart_data'][$key] += (int)$product_quantity;
                $update_hotness = 0;
            }
            if (!isset($ccdata['cart_history'][$product_cart_key])) {
                $ccdata['cart_history'][$product_cart_key] = (int)$product_quantity;
            } else {
                $ccdata['cart_history'][$product_cart_key] += (int)$product_quantity;
            }

            $cart_id = $ccdata['id'];
            if ($ccdata['parent_cart_id'] == -1) {
                $parent_cart_id = $ccdata['id'];
            } else {
                $parent_cart_id = $ccdata['parent_cart_id'];
            }
            if (isset($request['cart_session_id']) && !empty($request['cart_session_id'])) {
                $cart_session_id = $request['cart_session_id'];
            } else {
                $cart_session_id = $ccdata['cart_session_id'];
            }
            $cart_data = serialize($ccdata['cart_data']);
            $cart_history = serialize($ccdata['cart_history']);
        } else {
            $cart_data[$key] = (int)$product_quantity;
            $cart_history[$product_cart_key] = (int)$product_quantity;
            $cart_data = serialize($cart_data);
            $cart_history = serialize($cart_history);
        }

        // if product is newly added in cart then update the product's hotness value
        if($update_hotness){
        	$product_hotness = new ProductHotness($this->_db);
            $product_hotness->updateHotness("add-to-cart", $product_id);
        }

        $set_customer_id = "";
        if($this->_customer->isLogged()){
            $set_customer_id = "`customer_id`=".(int)$customer_id.", ";
        }
        if($record_update == 1){
            $sql = "UPDATE ".DB_PREFIX."customer_cart 
                        SET ".$set_customer_id."
                        `parent_cart_id`='".(int)$parent_cart_id."',
                        `cart_session_id`='".$this->_db->escape($cart_session_id)."',
                        `cart_data`='".$this->_db->escape($cart_data)."',
                        `cart_history`='".$this->_db->escape($cart_history)."',
                        `user_agent`='".$this->_db->escape($user_agent)."',
                        `last_cart_modified_from`='".$this->_db->escape($last_cart_modified_from)."',
                        `cart_modified_once_from`='".$this->_db->escape($cart_modified_once_from)."',
                        `ip`='".$ip."',
                        `app_version`=".(int)$app_version.",
                        `date_modified` = NOW()
                        WHERE `oc_customer_cart`.`id` = ".(int)$cart_id;

        }else{
            $sql =  "INSERT INTO ".DB_PREFIX."customer_cart 
                        SET ".$set_customer_id."
                        `parent_cart_id`='".(int)$parent_cart_id."',
                        `cart_session_id`='".$this->_db->escape($cart_session_id)."',
                        `cart_data`='".$this->_db->escape($cart_data)."',
                        `cart_history`='".$this->_db->escape($cart_history)."',
                        `user_agent`='".$this->_db->escape($user_agent)."',
                        `last_cart_modified_from`='".$this->_db->escape($last_cart_modified_from)."',
                        `cart_modified_once_from`='".$this->_db->escape($cart_modified_once_from)."',
                        `ip`='".$ip."',
                        `app_version`=".(int)$app_version.",
                        `date_added` = NOW(),
                        `date_modified` = NOW()";

        }

        if($this->_db->query($sql)){
            return true;
        }else{
            return false;
        }

    }
    private function _bulk_update($data, $to_update){

        if (isset($data['customer_cart_data'][0]['cart_data'])) {
            $cart = unserialize($data['customer_cart_data'][0]['cart_data']);

            //echo '<pre>';print_r($cart); echo '</pre>----------';
            if (count($to_update) > 0) {

                foreach($to_update as $arr_keys_to_update) {
                    $qty = $arr_keys_to_update['quantity'];
                    $key = $arr_keys_to_update['key'];

                    if (isset($cart[$key])) {
                        if ((int)$qty > 0) {
                            $cart[$key] = (int)$qty;
                        } else {
                            unset($cart[$key]);
                        }
                    }

                }
            }

            $cart_id = $data['customer_cart_data'][0]['id'];
            $cart = serialize($cart);

            if($this->_db->query(
                "UPDATE ".DB_PREFIX."customer_cart 
				SET 
				`cart_data`='".$cart."',
				`last_cart_modified_from`='".$this->_db->escape($data['last_cart_modified_from'])."',
                `cart_modified_once_from`='".$this->_db->escape($data['cart_modified_once_from'])."',
				`date_modified` = NOW()
				WHERE `oc_customer_cart`.`id` = ".$cart_id
            )){
                return true;
            }else{
                return false;
            }

        }
    }

    private function _update($data){
        if(isset($data['customer_cart_data'][0]['cart_data'])){
            $cart = unserialize($data['customer_cart_data'][0]['cart_data']);
            $qty = $data['quantity'];
            $key = $data['key'];
            $cart_id = $data['customer_cart_data'][0]['id'];
            if (isset($cart[$key])) {
                if ((int)$qty > 0) {
                    $cart[$key] = (int)$qty;
                }else {
                    unset($cart[$key]);
                }
            }
            $cart = serialize($cart);

            if($this->_db->query(
                "UPDATE ".DB_PREFIX."customer_cart 
				SET 
				`cart_data`='".$cart."',
				`last_cart_modified_from`='".$this->_db->escape($data['last_cart_modified_from'])."',
                `cart_modified_once_from`='".$this->_db->escape($data['cart_modified_once_from'])."',
				`date_modified` = NOW()
				WHERE `oc_customer_cart`.`id` = ".$cart_id
            )){
                return true;
            }else{
                return false;
            }
        }
    }
    public function updateCart($request){

        if(isset($request['user_id']) && $request['user_id'] != '0'){
            $extra['user_id'] = $request['user_id'];
            $conditions = 'customer_id = '. (int)$request['user_id'] .' order by date_modified desc limit 1';
            $customer_cart_data = $this->get_customer_cart($conditions);
        }else{
            $extra['user_id'] = 0;
            $extra['cart_session_id'] = $request['cart_session_id'];
            $conditions = 'cart_session_id = "'. $this->_db->escape($request['cart_session_id']) .'" order by date_modified desc limit 1';
            $customer_cart_data = $this->get_customer_cart($conditions);
        }

        $last_cart_modified_from = (isset($request['cart_modified_from'])?$request['cart_modified_from']:'DWEB');
        $cart_modified_once_from = array();
        if (!empty($customer_cart_data)) {
            $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
        }
        $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
        $cart_modified_once_from = serialize($cart_modified_once_from);

        if(isset($request['key']) && !empty($request['key'])){
            $extra['key'] = $request['key'];
            $extra['quantity'] = $request['quantity'];
            $extra['customer_cart_data'] = $customer_cart_data;
            $extra['last_cart_modified_from'] = $last_cart_modified_from;
            $extra['cart_modified_once_from'] = $cart_modified_once_from;
            return $this->_update($extra);

        }else{
            //Changed code to accept product id in a comma separated string to
            //update quantity for multiple products

            $product_id_string = $request['product_id'];
            $product_quantity_string = $request['product_quantity'];

            if (isset($request['product_option'])) {
                $product_option_id_string = $request['product_option'];
            }else{
                $product_option_id_string = "";
            }


            if (isset($request['product_option_value'])) {
                $product_option_value_string = $request['product_option_value'];
            }else{
                $product_option_value_string = "";
            }

            $product_id_array = explode(",", $product_id_string);
            $product_quantity_array = explode(",", $product_quantity_string);
            $product_option_id_array = explode(",", $product_option_id_string);
            $product_option_value_array = explode(",", $product_option_value_string);


            $i = 0;
            $item_to_update = array();
            foreach ($product_id_array as $product_id) {

                $product_quantity = $product_quantity_array[$i];

                if (count($product_option_value_array) > 0 && $product_option_id_array[$i] > 0) {
                    $product_option_id = $product_option_id_array[$i];
                } else {
                    $product_option_id = 0;
                }

                if (count($product_option_value_array) > 0 && $product_option_value_array[$i] > 0) {
                    $product_option_value = $product_option_value_array[$i];
                } else {
                    $product_option_value = 0;
                }
                $item_to_update[] = $this->prepareExtraDataForUpdateCart($product_id, $product_quantity, $product_option_id, $product_option_value);

                $i++;
            }
            if(count($item_to_update) > 0) {

                $extra['customer_cart_data'] = $customer_cart_data;
                $extra['last_cart_modified_from'] = $last_cart_modified_from;
                $extra['cart_modified_once_from'] = $cart_modified_once_from;
                //echo "<pre>"; print_r(unserialize($extra['customer_cart_data'][0]['cart_data'])); exit;
                return $this->_bulk_update($extra, $item_to_update);
            }


        }
        return false;
    }
    private function prepareExtraDataForUpdateCart($product_id, $product_quantity,$product_option_id, $product_option_value){
        $product['product_id'] = (int)$product_id;

        if (!empty($product_option_id)) {
            $option = array($product_option_id => $product_option_value );
            $product['option'] = $option;
        }

        $key = base64_encode(serialize($product));
        $extra['key'] = $key;
        $extra['quantity'] = $product_quantity;

        return $extra;
    }

    public function removeCart($request)
    {
        if (isset($request['user_id'])) {
            $conditions = 'customer_id = ' . (int)($request['user_id'] ?? 0) . ' order by date_modified desc limit 1';
            $customer_cart_data = $this->get_customer_cart($conditions);
        } else {
            if (isset($request['cart_session_id'])) {
                $extra['cart_session_id'] = $request['cart_session_id'];
                $extra['record_update'] = 0;
                $extra['cart_session_id'] = $request['cart_session_id'];
                $conditions = "cart_session_id = '" . $this->_db->escape($request['cart_session_id']) . "' order by date_modified desc limit 1";
                $customer_cart_data = $this->get_customer_cart($conditions);
            }
        }

        $last_cart_modified_from = (isset($request['cart_modified_from'])?$request['cart_modified_from']:'DWEB');
        $cart_modified_once_from = array();
        if (!empty($customer_cart_data)) {
            $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
        }
        $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
        $cart_modified_once_from = serialize($cart_modified_once_from);

        if (isset($request['key']) && !empty($request['key'])) {
            $key = $request['key'];
        } else {
            $product_id = $request['product_id'];
            if (isset($request['product_option'])) {
                $product_option_id = $request['product_option'];
            } else {
                $product_option_id = '';
            }
            if (isset($request['product_option_value'])) {
                $product_option_value = $request['product_option_value'];
            } else {
                $product_option_value = '';
            }
            $product['product_id'] = (int)$product_id;

            if (!empty($product_option_id)) {
                $option = array($product_option_id => $product_option_value);
                $product['option'] = $option;
            }
            $key = base64_encode(serialize($product));
        }
        if (!empty($customer_cart_data)) {
            $ccdata = $customer_cart_data[0];
            $ccdata['cart_data'] = unserialize($ccdata['cart_data']);
            if (isset($ccdata['cart_data'][$key])) {
                unset($ccdata['cart_data'][$key]);

                $ccdata['product_comments'] = unserialize($ccdata['product_comments']);
                if(isset($ccdata['product_comments'][$key])){
                    unset($ccdata['product_comments'][$key]);
                }

                //remove coupon and other if cart becomes empty
                $others = '';
                if (empty($ccdata['cart_data'])) {
                    $others = ", coupon='', franchise_id=0, franchise_margin=0 ";
                }

                $cart_id = $ccdata['id'];
                $cart_data = serialize($ccdata['cart_data']);
                $product_comments = serialize($ccdata['product_comments']);
                $sql = "UPDATE ".DB_PREFIX."customer_cart 
				        SET `cart_data`='".$this->_db->escape($cart_data)."',`product_comments`='".$this->_db->escape($product_comments)."',
				        `last_cart_modified_from`='".$this->_db->escape($last_cart_modified_from)."',
                        `cart_modified_once_from`='".$this->_db->escape($cart_modified_once_from)."',
				        `date_modified` = NOW() " . $others ."
				        WHERE `oc_customer_cart`.`id` = ".(int)$cart_id;

                if($this->_db->query($sql)){
                    return true;
                }else{
                    return false;
                }
            }
        }

        return false;

    }

    public function removeProducts($keys)
    {
        $user_id = $this->_customer->getId();
        if($this->_customer->isLogged()){
            $request = array('user_id' => $user_id,'access_token' => $this->_customer->getAccessToken());
        }elseif(isset($_COOKIE['cart_session_id'])){
            $request = array('cart_session_id' => $_COOKIE['cart_session_id']);
        }elseif(isset($_REQUEST['cart_session_id'])){
            $request = array('cart_session_id' => $_REQUEST['cart_session_id']);
        }

        $last_cart_modified_from = 'DWEB';
        if(CONFIG_IS_MOBILE == 1){
            $last_cart_modified_from = 'MWEB';
        }

        if (isset($request['user_id'])) {
            $conditions = 'customer_id = ' . (int)($request['user_id'] ?? 0) . ' order by date_modified desc limit 1';
            $customer_cart_data = $this->get_customer_cart($conditions);
        } else {
            if (isset($request['cart_session_id'])) {
                $extra['cart_session_id'] = $request['cart_session_id'];
                $extra['record_update'] = 0;
                $extra['cart_session_id'] = $request['cart_session_id'];
                $conditions = "cart_session_id = '" . $this->_db->escape($request['cart_session_id']) . "' order by date_modified desc limit 1";
                $customer_cart_data = $this->get_customer_cart($conditions);
            }
        }
        $keys = explode(',',$keys);
        if (!empty($customer_cart_data)) {
            $cart_modified_once_from = unserialize($customer_cart_data[0]['cart_modified_once_from']);
            $cart_modified_once_from[$last_cart_modified_from] = Date('Y-m-d H:i:s');
            $cart_modified_once_from = serialize($cart_modified_once_from);

            $ccdata = $customer_cart_data[0];
            $ccdata['cart_data'] = unserialize($ccdata['cart_data']);
            $ccdata['product_comments'] = unserialize($ccdata['product_comments']);
            $product_hotness = new ProductHotness($this->_db);
            foreach ($keys as $key){
                if (isset($ccdata['cart_data'][$key])) {
                    unset($ccdata['cart_data'][$key]);

                    // Update the product's hotness value
                    $product = unserialize(base64_decode($key));
			        $product_hotness->updateHotness("remove-from-cart",$product['product_id']);
                }
                if(isset($ccdata['product_comments'][$key])){
                    unset($ccdata['product_comments'][$key]);
                }
            }

            //remove coupon and other if cart becomes empty
            $others = '';
            if (empty($ccdata['cart_data'])) {
                $others = ", coupon='', franchise_id=0, franchise_margin=0 ";
            }

            $cart_id = $ccdata['id'];
            $cart_data = serialize($ccdata['cart_data']);
            $product_comments = serialize($ccdata['product_comments']);
            $sql = "UPDATE ".DB_PREFIX."customer_cart 
                    SET `cart_data`='".$this->_db->escape($cart_data)."',`product_comments`='".$this->_db->escape($product_comments)."',
                        `last_cart_modified_from`='".$this->_db->escape($last_cart_modified_from)."',
                        `cart_modified_once_from`='".$this->_db->escape($cart_modified_once_from)."',
                        `date_modified` = NOW() " . $others ."
                    WHERE `oc_customer_cart`.`id` = ".(int)$cart_id;
            if($this->_db->query($sql)){
                return true;
            }else{
                return false;
            }

        }
        return false;

    }

    public function clear_cart($request){

        if(isset($request['user_id'])){
            $user_id = $request['user_id'];
        }else{
            $cart_session_id = $request['cart_session_id'];
        }
        if(isset($request['order_number'])){
            $order_number = $request['order_number'];
        }else{
            $order_number = 0;
        }
        if(isset($request['cart_type'])){
            $cart_type = $request['cart_type'];
        }else{
            $cart_type = '';
        }

        if(isset($user_id)){
            $sql = "SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id=".(int)$user_id;
        }else{
            $sql = "SELECT * FROM " . DB_PREFIX . "customer_cart WHERE cart_session_id='".$this->_db->escape($cart_session_id)."'";
        }
        $query = $this->_db->query($sql);
        $cart_history = $query->rows;

        $last_cart_modified_from = 'DWEB';
        if(CONFIG_IS_MOBILE == 1){
            $last_cart_modified_from = 'MWEB';
        }

        if(!empty($cart_history)){

            $cart_data  = unserialize($cart_history[0]['cart_data']);
            $product_hotness = new ProductHotness($this->_db);
            foreach ($cart_data as $key => $qty){
                // Update the product's hotness value
                $product = unserialize(base64_decode($key));
                $product_hotness->updateHotness("remove-from-cart",$product['product_id']);
            }

            if(!empty($user_id)){

                if($this->addCartHistory($user_id,$cart_type,$order_number,$last_cart_modified_from)){
                    $this->_db->query("DELETE FROM " . DB_PREFIX . "customer_cart WHERE customer_id=".(int)$user_id);
                    return true;
                }
            }

            if(!empty($cart_session_id)){

                $this->_db->query("DELETE FROM " . DB_PREFIX . "customer_cart WHERE cart_session_id='".$this->_db->escape($cart_session_id)."'");
                return true;
            }


        }
        return false;
    }

    /**
     * Method to set coupon code in cart
     * @params: coupon,user_id
     * @author: Devendra Dhayal, Date-Added: 01-06-2017
     */
   public function setCoupon($coupon, $user_id=''){
       // If customer (user) id is provided - generally in case of App
       if ( !empty($user_id) ) {
           $condition = " WHERE customer_id = '" . (int)$user_id . "'";

       } else { // Customer (user) id is not provided

           // Check if we can get it ourselves <-> customer is logged in
           $user_id = $this->_customer->getId();
           if ( !empty($user_id) ) { // customer is logged in
               $condition = " WHERE customer_id = '" . (int)$user_id . "'";

           } else {  // Anonymous user - get last_session_id from cookies

               if ( isset($_COOKIE['PHPSESSID']) || isset($_COOKIE['last_session_id']) ){
                   if ( isset($_COOKIE['PHPSESSID']) ) {
                       $cart_session_id = $_COOKIE['PHPSESSID'];
                   } else {
                       $cart_session_id = $_COOKIE['last_session_id'];
                   }
                   $condition = " WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'";
               } else {
                   return 0;
               }
           }
       }

       $sql = "UPDATE ".DB_PREFIX."customer_cart  SET  coupon='".$this->_db->escape( serialize($coupon) )."' ".$condition;
       $this->_db->query($sql);

	   //update the coupon
	   $this->_coupon = $coupon;

	   // Now coupon is changed, need to empty the cart data, so that future cart->getProducts() calls will return correct data
       $this->_cart_data = null;
       $this->_in_stock_cart_data = null;

       return 1;
   }

    /**
     * Method to get coupon code in cart
     * @params: user_id
     * @author: Devendra Dhayal, Date-Added: 01-06-2017
     */
    public function getCoupon($user_id=''){
		// If we have already retrieved coupon from database, then return from here only.
		if($this->_coupon != null){
			return $this->_coupon;
		}
        // If customer (user) id is provided - generally in case of App
        if ( !empty($user_id) ) {
            $condition = " WHERE customer_id = '" . (int)$user_id . "'";

        } else { // Customer (user) id is not provided

            // Check if we can get it ourselves <-> customer is logged in
            $user_id = $this->_customer->getId();
            if ( !empty($user_id) ) { // customer is logged in
                $condition = " WHERE customer_id = '" . (int)$user_id . "'";

            } else {  // Anonymous user - get last_session_id from cookies

                if ( isset($_COOKIE['PHPSESSID']) || isset($_COOKIE['last_session_id']) ){
                    if ( isset($_COOKIE['PHPSESSID']) ) {
                        $cart_session_id = $_COOKIE['PHPSESSID'];
                    } else {
                        $cart_session_id = $_COOKIE['last_session_id'];
                    }
                    $condition = " WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'";
                } else {
                    return '';
                }
            }
        }

        $sql = "SELECT coupon FROM ".DB_PREFIX."customer_cart ".$condition;
        $query = $this->_db->query($sql);
        if($query->num_rows > 0) {
            $this->_coupon = unserialize($query->row['coupon']);
            return $this->_coupon;
        }
        else{
            return '';
        }
    }
   	/**
    * Method to check whether product should be shown in instock or out of stock
    * This method can be used everywhere including listing, product detail, cart, admin panel products list page etc
    * Input  : @param an array of key-value pairs ex: ('qunatity' => '5', 'stock_quantity' => '10','seller_status' => '1')
    * Return: stock status (true or false) and reason(s) behind being out of stock (if stock is false) 
    * @author: Devendra Dhayal, Date-Added: 28-05-2017
    */
   public static function getProductStockStatus($product_info){
        $result = array();
        $result['stock'] = true;
        $result['reason'] = array();

        // product is set out of stock
        if( isset($product_info['stock']) && $product_info['stock'] == false ){
            $result['stock'] = false;
            $result['reason'][] = 'out_of_stock';
        }
        
         // product is archived
        if( isset($product_info['is_archived']) && (int)($product_info['is_archived']) == 1 ){
            $result['stock'] = false;
            $result['reason'][] = 'product archived';
        }

        // product stock_status is not In Stock( In case of product listing)
        if( isset($product_info['stock_status']) && $product_info['stock_status'] != 'In Stock' ){
            $result['stock'] = false;
            $result['reason'][] = 'out_of_stock';
        }
        
        // product's ordered quantity is greater than available qunatity
        if( isset($product_info['quantity']) && isset($product_info['stock_quantity'])
           && $product_info['quantity'] > $product_info['stock_quantity'] ){
            $result['stock'] = false;
            $result['reason'][] = 'donot_have_enough_qty';
        }
        
        // product's stock_status_id == 5
        if( isset($product_info['stock_status_id']) && $product_info['stock_status_id'] == 5 ){
            $result['stock'] = false;
            $result['reason'][] = 'out_of_stock';
        }
        
        // product is not enabled
        if( isset($product_info['status']) && $product_info['status'] != 1 ){
            $result['stock'] = false;
            $result['reason'][] = 'product_disabled';
        }
        
        // product seller is on vacation mode
        if( isset($product_info['vacation_mode']) && $product_info['vacation_mode'] != 0 ){
            $result['stock'] = false;
            $result['reason'][] = 'vacation_mode';
        }

        // product seller is not assigned
        if( (array_key_exists('seller_id',$product_info) && empty($product_info['seller_id'])) ){
            $result['stock'] = false;
            $result['reason'][] = 'seller_not_assigned';
        }

        // product seller is not enabled
        if( !empty($product_info['seller_status']) && $product_info['seller_status'] != 1 ){
            $result['stock'] = false;
            $result['reason'][] = 'seller_disable';
        }
        
        // product qunatity is zero
        if( isset($product_info['quantity']) && $product_info['quantity'] == 0 ){
            $result['stock'] = false;
            $result['reason'][] = 'quantity_zero';
        }

       // piece in set is zero
       if( isset($product_info['piece_in_set']) && $product_info['piece_in_set'] == 0 ){
           $result['stock'] = false;
           $result['reason'][] = 'Piece in set is 0';
       }

       // store not assigned
       if( isset($product_info['store_id']) && $product_info['store_id'] == '' ){
           $result['stock'] = false;
           $result['reason'][] = 'Store not assigned';
       }

       // product minimum is greater than available quantity
       /*if( isset($product_info['quantity']) && isset($product_info['minimum'])
        && $product_info['minimum'] > $product_info['quantity'] ){
           $result['stock'] = false;
           $result['reason'][] = 'moq_not_available';
       }*/
        
      
        // product available date is in future
        if( isset($product_info['date_available']) && strtotime($product_info['date_available']) > strtotime(date("Y-m-d")) ){
            $result['stock'] = false;
            $result['reason'][] = 'date_available_in_future';
        }
        
        // checking if store voucher is not available and product is not from jp and online
//        if( !isset($product_info['wsb_store_code']) && isset($product_info['store_sales'])
//            && !in_array( $product_info['store_sales'] ,array( 'NO' , 'JP' )) )
//        {
//            $result['stock'] = false;
//            $result['reason'][] = 'wrong_store';
//        }
        
        // check if store voucher is applied, store code of voucher will be send as wsb_store_code
        if( isset($product_info['wsb_store_code'])
           && isset($product_info['store_sales']) )
        {

            // product's store_sales does not matches with applied store code and also product is not from jp and online.
            if( $product_info['store_sales'] != $product_info['wsb_store_code']
               && !in_array( $product_info['store_sales'] , array('JP','NO') ) )
            {
                $result['stock'] = false;
                $result['reason'][] = 'wrong_store';
            }
        }
        
        // checking if HSN Code is valid or not
        if( !isset($product_info['hsn_code']) || 
			!(strlen($product_info['hsn_code']) >= 4 
				&& strlen($product_info['hsn_code']) <= 8 
				&& preg_match("/[0-9]{4,}/", $product_info['hsn_code']))) {
				
				$result['stock'] = false;
				$result['reason'][] = 'invalid HSN Code';	
		}
        
        // checking if HSN Code is valid or not
        if( !isset($product_info['hsn_code']) || 
			!(strlen($product_info['hsn_code']) >= 4 
				&& strlen($product_info['hsn_code']) <= 8 
				&& preg_match("/[0-9]{4,}/", $product_info['hsn_code']))) {
				
            $result['stock'] = false;
			$result['reason'][] = 'Invalid HSN Code';	
		} elseif ( empty($product_info['tax_class_id']) ) { // HSN Code is valid
            // But Tax class ID corresponding to it is Invalid or Not Recorded
            $result['stock'] = false;
            $result['reason'][] = 'Tax Class Not Assigned to HSN Code';	
        }
		
        return $result;
   }
  
   
   /**
    * This method will fetch all the price details for products listing
    * and return all the related prices to show on website.
    * @input ( $data (array(price,hsn_code,commission,pice_in_set)), $db)
    * @output ($product_price_info (array()))
    * */
   public static function getPrice($data = array(), $registry) {
	   if(method_exists($registry,'get')){
		  $db =  $registry->get('db');
		  $tax = $registry->get('tax');
			$currency = $registry->get('currency');
	   }
	   else{
		  $db =  $registry->db;
		  $tax = $registry->tax;
			$currency = $registry->currency;
	   }
	   
	   if (empty($data['product_id']))
	       return false;
	   
	   $oc_product_keys = array('price','hsn_code',
	                            'commission','mrp', 'hidden_selling_price');
	  
	  foreach ($oc_product_keys as $key => $field) {
		  if (isset($data[$field])) {
			  unset($oc_product_keys[$key]);
		  }
	  }	  
	  
	  // Getting product data

	  if ( !empty($oc_product_keys) ) {
          $sql = "SELECT ";
		  $sql .= " " . implode(', ', $oc_product_keys);
          $sql .= " FROM " . DB_PREFIX . "product 
		        WHERE product_id = '" . (int)$data['product_id'] . "'";
          $query = $db->query($sql);

          if ($query->num_rows) {
              foreach ($query->row as $field => $value) {
                  $data[$field] = $value;
              }
          }
	  }

	  $transfer_price_per_piece = (float)$data['price'];

	  if(isset($data['option_price'])){
          $transfer_price_per_piece += (float)$data['option_price'];
      }

      //getting seller tax_rate for trasnfer price (which is inclusive of tax)
      $seller_tax = (float)$tax->getTaxRateForTaxIncludedPrice($transfer_price_per_piece,
           $data['hsn_code'], $data['mrp']);
	
       //getting seller_tax_factor and commission factor
       $seller_tax_factor = 1.0 + ( (float)$seller_tax / 100.0 );
       $commission_factor = 1.0 + ( (float)$data['commission'] / 100.0 );
		
       //calculating selling price
       $selling_price = ceil( ($transfer_price_per_piece / $seller_tax_factor) * $commission_factor );
	  
       $original_selling_price = $selling_price;
       // Product specials (if any)
	  $special_price_query = $db->query("SELECT price
		  								 FROM " . DB_PREFIX . "product_special
										 WHERE product_id = '" . (int)$data['product_id'] . "'
										 AND (date_start = '0000-00-00 00:00:00' OR date_start < NOW())
										 AND (date_end = '0000-00-00 00:00:00' OR date_end > NOW())
										 ORDER BY priority ASC, price ASC LIMIT 1");
	  if ( $special_price_query->num_rows > 0 
		   && !empty($special_price_query->row['price']) 
		   && (float)$special_price_query->row['price'] < $transfer_price_per_piece ) {

          $transfer_price_per_piece = (float)($special_price_query->row['price']);
          if(isset($data['option_price'])){
              $transfer_price_per_piece += (float)$data['option_price'];
          }
          //getting seller tax_rate for trasnfer price (which is inclusive of tax)
          $seller_tax = (float)$tax->getTaxRateForTaxIncludedPrice($transfer_price_per_piece,$data['hsn_code'], $data['mrp']);

          //getting seller_tax_factor and commission factor
          $seller_tax_factor = 1.0 + ( (float)$seller_tax / 100.0 );
          $commission_factor = 1.0 + ( (float)$data['commission'] / 100.0 );

          //calculating selling price
          $selling_price = ceil( ($transfer_price_per_piece / $seller_tax_factor) * $commission_factor );

	  }

       $discount_percentage = 0;
       if($original_selling_price != $selling_price)
            $discount_percentage = (ceil((($original_selling_price - $selling_price)*100)/$original_selling_price));

       //getting tax_class_id
	   $tax_class_id = $tax->getTaxClassIdFromHSNCode($data['hsn_code']);
       if(self::$use_hidden_selling_price && (float)$data['hidden_selling_price'] > 0 ) {
            $selling_price = $data['hidden_selling_price'];
        }
				if (self:: $apply_international_price_factor) {
					$selling_price = $currency->convert($selling_price, 'INR', DUMMY_INR_CURRENCY);
				}
        //getting output tax rates from the selling price
       $output_tax_rates = $tax->getTaxRate($selling_price, $tax_class_id, array(), $data['mrp']);
       $tax_per_piece = $tax->getTax( $selling_price ,$data['hsn_code'], '', '', $data['mrp']);

       $saving_money = 0;
       $margin_percentage = 0;
       if(isset($data['mrp']) && $data['mrp'] > 0){
           $saving_money = $data['mrp'] - $selling_price;
           $margin_percentage = ceil(($saving_money*100)/$data['mrp']);
       }
              
      
       $return_data =  array(
						'transfer_price_per_piece' => $transfer_price_per_piece,
						'original_selling_price'   => $original_selling_price,
						'selling_price'            => $selling_price,
						'output_tax_rates'         => $output_tax_rates,
						'seller_tax'               => $seller_tax,
						'tax_class_id'             => $tax_class_id,
						'commission'       		   => $data['commission'], 
						'seller_tax_factor'        => $seller_tax_factor, 
						'commission_factor'        => $commission_factor,
                        'tax_per_piece'            => $tax_per_piece,
                        'saving_money'             => $saving_money,
                        'margin_percentage'        => $margin_percentage,
                        'discount_percentage'      => $discount_percentage
					);
		return $return_data;
   }

    /**
     * This method will give the product's units
     * @input ( $product_id )
     * @output ($product_unit_array (base_unit,super_unit))
     * */
   public function getProductUnit($product_id){

        $units = array();

        $sql = "SELECT unit_id FROM " . DB_PREFIX . "product
                WHERE product_id = '" . $product_id . "'";
        $op_query = $this->_db->query($sql);

        //first check unit_id of product
        if($op_query->row['unit_id'] != 0 && $op_query->row['unit_id'] != '' && $op_query->row['unit_id'] != NULL) {
            $unit_id = (int)$op_query->row['unit_id'];
        }else{
            //first get unit_id from product category_id have high MAX(tag_priority)
            $sql = "SELECT PC.category_id, PC.product_id, C.unit_id FROM " . DB_PREFIX . "product_to_category AS PC "
                . "LEFT JOIN " . DB_PREFIX . "category AS C ON C.category_id = PC.category_id "
                . "WHERE PC.product_id = " . $product_id . " ORDER BY C.tag_priority DESC limit 1";
            //echo $sql; die;
            $query = $this->_db->query($sql);
						if ($query->num_rows) {
							$unit_id = $query->row['unit_id'];
						} else {
							$unit_id = 1; // default unit
						}
        }
        $sql = "SELECT super_unit, base_unit FROM " . DB_PREFIX . "units
                WHERE unit_id = '" . $unit_id . "'
                AND status = '1'";
        $opu_query = $this->_db->query($sql);
         $units['unit_id'] = $unit_id;
        if($opu_query->num_rows > 0){
            $units['base_unit'] = $opu_query->row['base_unit'];
            $units['super_unit'] = $opu_query->row['super_unit'];
        }
        return $units;

   }

   public function getFranchiseId(){
       return $this->_franchise_id;
   }

   public function getFranchiseMargin(){
       return $this->_franchise_margin;
   }

    public function setFranchiseId($franchise_id, $user_id = ''){
       if(empty($user_id)){
           $user_id = $this->_customer->getId();
       }
        $sql = "UPDATE " . DB_PREFIX . "customer_cart SET franchise_id = " . (int)$franchise_id . " WHERE customer_id = " . (int)$user_id;
        $this->_db->query($sql);
    }

    public function setFranchiseMargin($franchise_margin, $user_id = ''){
        if(empty($user_id)){
            $user_id = $this->_customer->getId();
        }
        $sql = "UPDATE " . DB_PREFIX . "customer_cart SET franchise_margin = " . (int)$franchise_margin . " WHERE customer_id = " . (int)$user_id;
        $this->_db->query($sql);
    }

    public function checkFranchiseProductsInCart($franchise_id='',$user_id=''){
        if(empty($this->_fid_pid_map)){
            $this->getProducts($user_id);
        }
        if(empty($franchise_id)){
            $franchise_id = $this->_franchise_id;
        }
        if(array_key_exists($franchise_id,$this->_fid_pid_map) && count($this->_fid_pid_map[$franchise_id]) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    /*
     * To check whether a product belongs to 'Store30DaysPurchased' inventory or not.
     * @author: Devendra Dhayal
     * @input: product id
     * @output: difference between current date and purchased date
     * */
    private function _getWSBPurchasedProductDateDiff($product_id){
        $sql = "SELECT DATEDIFF(NOW(), wp.date_added) as last_purchased_days, wpb.product_id as product_id FROM oc_wsb_purchase wp INNER JOIN oc_wsb_purchase_breakup wpb ON wp.purchase_id = wpb.purchase_id WHERE wpb.product_id = ". (int)$product_id ." ORDER BY wp.purchase_id LIMIT 1";
        $result = $this->_db->query($sql);
        if($result->num_rows){
            return $result->row['last_purchased_days'];
        }
        else{
            return 0;
        }
    }

    /*

     * To copy cart data into cart history.
     * @author: Devendra Dhayal
     * @input:$user_id, $cart_type(action on which cart is going to be cleared)
     * */
    public function addCartHistory($user_id, $cart_type, $order_number = 0, $last_cart_modified_from = 'DWEB')
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id=" . (int)$user_id;
        $query = $this->_db->query($sql);
        $cart_history = $query->rows;

        if (!empty($cart_history)) {

            $cart_history = serialize($cart_history);

            $sql = "INSERT INTO " . DB_PREFIX . "wsb_cart_history 
                SET `customer_id`='" . (int)$user_id . "',
                `order_number`='" . (int)$order_number . "',
                `cart_type`='" . $this->_db->escape($cart_type) . "',
                `cart_history`='" . $this->_db->escape($cart_history) . "',
                `last_cart_modified_from`='" . $this->_db->escape($last_cart_modified_from) . "',
                `date_added` = NOW()";

            return $this->_db->query($sql);
        }

        return false;
    }

     /* To get category level non-returnable field for the input product.
     * @author: Devendra Dhayal, 14th December 2017
     * @input: product id
     * @output: non-returnable value
     * */
    public static function getCategoryLevelNonReturnable($db, $product_id){
        $sql = "SELECT MAX(c.non_returnable) AS non_returnable 
                FROM " . DB_PREFIX . "product_to_category pc 
                INNER JOIN " . DB_PREFIX . "category c ON pc.category_id = c.category_id 
                WHERE pc.product_id = ".(int)$product_id;

        $result = $db->query($sql);

        return (int)($result->row['non_returnable'] ?? 0);
    }

    public function getPickUpCityMap(){
        return $this->_pickup_city_map;
    }
		
		/* 
		* @method: to get associate products of a product.
		* @param: product_id
		* @return: associate products ids
		* @author: Devendra Dhayal, June 2017
		* */
		private function __getAssociateProducts($product_id) {
			$sql = "SELECT associate_product_id as product_id FROM " . DB_PREFIX . "product_to_associate 
							WHERE product_id='" . (int)$product_id . "'";
							
			$result = $this->_db->query($sql);
			if ($result->num_rows) {
				return $result->rows;
			} else {
				return array();
			}
		}
		
    /*
    * @method: to get stock status of a combo product. 
    * we will check it's associate products stock status, 
    * if any of associate is out of stock, then whole combo will be marked as out of stock
    * @param: $product id 
    * @return: stock status 
    * @author: Devendra, June 2018
    */
    public static function getComboProductStockStatus($product_id, $db) {
      $sql = "SELECT p.* FROM " . DB_PREFIX . "product p JOIN " . DB_PREFIX . "product_to_associate ps ON p.product_id=ps.associate_product_id  WHERE ps.product_id = '" . (int)$product_id . "'";
      $op_query = $db->query($sql);
      // if combo product have less than 2 associate products, we will mark it as out of stock
      if ($op_query->num_rows < 2)
          return false;
          
      foreach ($op_query->rows as $associate_product) {
        // Get seller_id
        $sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
                WHERE product_id = '" . (int)$associate_product['product_id'] . "'";
        $omp_query = $db->query($sql);
        if ($omp_query->num_rows == 0) // If no seller then return false
            return false;
            
        // oc_ms_seller query
        $sql = "SELECT seller_id, cod_available, vacation_mode, seller_status, non_returnable, city 
                FROM " . DB_PREFIX . "ms_seller
                WHERE seller_id = '" . (int)$omp_query->row['seller_id'] . "'";
        $oms_query = $db->query($sql);
        if ($oms_query->num_rows == 0)
            return false;
        
        $product_info = $associate_product;
        $product_info['seller_status'] = $oms_query->row['seller_status'];
        $product_info['vacation_mode'] = $oms_query->row['vacation_mode'];
        $stock_status_info = Cart::getProductStockStatus($product_info);
        if ( isset($stock_status_info['stock'])
            && $stock_status_info['stock'] === false){
            return false;
        }
      }

      // control came here, means every associate product is in-stock, so return true
      return true;
    }    

    /**
     * Function useHiddenSellingPrice is used to set static variable $use_hidden_selling_price as $bool for replaceing selling price with hidden_selling_price  
     * @param bool $bool
     * @author Nilesh, 2018
     */
    public function useHiddenSellingPrice(bool $bool = false) : void {
        self::$use_hidden_selling_price = $bool;
    }
		
		/*
		 * @method: setApplyInternationPriceFactor
		 * set the value of variable $apply_international_price_factor
		 * if true then price will be updated by dummy inr currency factor 
		 * @params: $apply_international_price_factor( true or false)
		 * @author: Devendra, Septemeber 2018
		 */
		public function setApplyInternationPriceFactor(bool $apply_international_price_factor = false) : void {
			self::$apply_international_price_factor = $apply_international_price_factor;
		}


    /*
		 * @method: totalProductsCount
		 * get cart products total count
		 * @params: $user_id( int )
		 * @author: Mahaveer, Nov 2019
		 */
	public function totalCartProductsCount() 
	{
		$total_sets = 0;
		$user_id = $this->_customer->getId();

		 if(!empty($this->_cart_data))
		 {
            foreach ($this->_cart_data as $product) 
            {
                $total_sets += (int)$product['quantity'];
             }
         }
        else
        {
	       $where = '';
			if ( !empty($user_id) ) 
			{ 
				$where = "WHERE customer_id = '" . (int)$user_id . "'";
			}
			else if(isset($_REQUEST['cart_session_id']))
            {
             	$cart_session_id = $_REQUEST['cart_session_id'];
				$where = "WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'";
            }
            else if(isset($_COOKIE['cart_session_id'])) 
            {  
                $cart_session_id = $_COOKIE['cart_session_id'];
				$where = "WHERE cart_session_id = '" . $this->_db->escape($cart_session_id) . "'";
			}
		   if($where != '')
		   {
			  $sql = "SELECT cart_data FROM " . DB_PREFIX . "customer_cart ".$where."  Order By NULL limit 1"; 
			  $query = $this->_db->query($sql);
			 
			  if($query->num_rows > 0)
			  {
			    $cartdata = unserialize($query->row['cart_data']);

			    foreach ($cartdata as $product) 
		        {
                  $total_sets += (int)$product;
                }
               }   
	     	}
	    } 		

		return $total_sets;

	}

}

