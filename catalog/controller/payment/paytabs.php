<?php
error_reporting(1);
ob_start();

class ControllerPaymentPaytabs extends Controller {

// class constructor

	public function sendRequest($gateway_url, $request_string){

		$ch = @curl_init();
		@curl_setopt($ch, CURLOPT_URL, $gateway_url);
		@curl_setopt($ch, CURLOPT_POST, true);
		@curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_HEADER, false);
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		@curl_setopt($ch, CURLOPT_VERBOSE, true);
		$result = @curl_exec($ch);
		if (!$result)
			die(curl_error($ch));

		@curl_close($ch);
		
		return $result;
	}

    public function index() {
		$this->load->model('checkout/order');
		$this->load->language('payment/paytabs');

        $data['button_confirm'] = $this->language->get('button_confirm');
		$data['order_id'] = $this->session->data['order_id'];

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paytabs.tpl')) {
			return $this->load->view('default/template/payment/paytabs.tpl', $data);
		} else {
            return $this->load->view('default/template/payment/paytabs.tpl', $data);
		}
	}

    public function callback() {

        $this->load->model('checkout/order');

        //if api_key exists
        if (isset($_SESSION['secret_key'])) {
            $selector = array('order' => array());
            $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], $selector)['order'];
            if (!empty($this->session->data['net_order_totals'])) {
                $order_info['total'] = $this->session->data['net_order_totals'];
            }

            $secret_key = $_SESSION['secret_key'];
            $paytabs_merchant = $_SESSION['paytabs_merchant'];
            $request_param =array('secret_key'=>$secret_key,
              'merchant_email'=>$paytabs_merchant,'payment_reference'=>$_POST['payment_reference']);
            $request_string = http_build_query($request_param);

             //Send data for verification
            $response_data = $this->sendRequest('https://www.paytabs.com/apiv2/verify_payment',$request_string);
            $object = json_decode($response_data);

            $response = $object->response_code;

            $site = $_SERVER['HTTP_HOST'];

            if ($response == 100) {

                $input = array();
                $input['order_id'] = $this->session->data['order_id'];
                $input['order_status_id'] = $this->config->get('paytabs_order_status_id');
                $input['comment'] = 'Payment Successful. PayTabs Payment Id:' . $object->pt_invoice_id;

                $this->model_checkout_order->addOrderHistory($input);

                $paid_amount = $this->currency->convertLiveRates($object->amount, $object->currency, 'INR');
                $payable_amt = (float) $this->currency->convertLiveRates($order_info['total'], $order_info['currency_code'], 'INR');
                $paid_amt = (float) $paid_amount;
                
                //Define data array
                $data = array();
                $data['order_id'] = (int) $order_info['order_id'];
                $data['merchant_txn_id'] = $object->transaction_id;
                $data['order_no'] = $order_info['order_no'];
                $data['txn_status'] = 'SUCCESS';
                $data['payment_mode'] = 'PayTabs';
                $data['amount'] = (float) ($paid_amt);
                $data['txn_date_time'] = date('Y-m-d H:i:s');
                $data['date_added'] = 'NOW()';
                $data['payment_gateway'] = 'paytabs';
                $data['successfull'] = '1';
                $data['reference'] = $object->reference_no;
                $data['invoice_id'] = $object->pt_invoice_id;
                $data['payment_link'] = 'Customer Direct Payment';
                $data['json_format'] = serialize($response_data);
                $data['user_id'] = '0';
                $data['sales_staff_id'] = '0';

                //Insert data to Order Payment table
                OrderPayment::insertOrderPayment($this->db, $data);

                $this->response->redirect($this->url->link('checkout/success'));

            }else{
                $this->load->language('checkout/failure');
                $this->document->setTitle($this->language->get('heading_title'));

                $data['breadcrumbs'] = array();
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/home')
                );
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_basket'),
                    'href' => $this->url->link('checkout/cart')
                );
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_checkout'),
                    'href' => $this->url->link('checkout/checkout', '', 'SSL')
                );
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_failure'),
                    'href' => $this->url->link('checkout/failure')
                );

                $this->session->data['error'] = 'Paytabs Response: '.$object->result;
                $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL', 'payment'));

               /* $data['heading_title'] = $this->language->get('heading_title');
                $data['text_message'] = $object->result;
                $data['button_continue'] = $this->language->get('button_continue');
                $data['continue'] = $this->url->link('common/home');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['column_right'] = $this->load->controller('common/column_right');
                $data['content_top'] = $this->load->controller('common/content_top');
                $data['content_bottom'] = $this->load->controller('common/content_bottom');
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');
                //$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10,false);

                return $this->response->setOutput($this->load->view('default/template/payment/paytabs_error.tpl', $data));
                //$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/paytabs_error.tpl', $data));
               */
            }
			// if Sessions  doesn't exist
        } else {
            $input = array();
            $input['order_id'] = $this->session->data['order_id'];
            $input['order_status_id'] = 8;
            $input['comment'] = 'Session secret key not found. PayTabs Paypage id: '.isset($_SESSION['pid'])?$_SESSION['pid']:''.'. Payment url: '.isset($_SESSION['url'])?$_SESSION['url']:'';

            $this->model_checkout_order->addOrderHistory($input);
     		//$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10,false);
            $this->response->redirect($this->url->link('checkout/failure'));
        }

    }

	public function send() {
		$this->language->load('payment/paytabs');
        $this->load->model('account/order');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        $selector = array('order' => array());
		//$order_info = $this->model_account_order->getOrder($this->session->data['order_id']);
        $order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $this->session->data['order_id'], $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }

        $_SESSION['secret_key'] 	= $this->config->get('paytabs_security');
        $_SESSION['paytabs_merchant'] = $this->config->get('paytabs_merchant');

		$total_product_ammout = 0;
		foreach($this->cart->getProducts() AS $product) {
            $name[]= $product['name'];
            $quantity[]= $product['quantity'];
            $price[]= $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false);
            $total[]= $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'], false);
            $total_product_ammout += $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false)*$product['quantity'];
        }

        $products_per_title = implode(' || ',$name);
        $quantity		     = implode(' || ',$quantity);
        $price		         = implode(' || ',$price);
        $total		         = implode(' || ',$total);
        $cost 				  =    $this->session->data['shipping_method']['cost'];
        //$subtotal=$this->cart->getSubTotal();
        $subtotal = $total_product_ammout;
        $discount = 0;

        if(isset($this->session->data['tax'])){
            $cost = $cost + $this->session->data['tax'];
        }

        $other_charges = $this->currency->format($cost, $order_info['currency_code'], $order_info['currency_value'], false);
        $price1 = $subtotal + $other_charges;

        $discount = $price1 - $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $discount = $this->currency->format($discount, $order_info['currency_code'], $order_info['currency_value'], false);

	    $payment_country_info = $this->model_localisation_country->getcountry($order_info['payment_country_id']);
        $shipping_country_info = $this->model_localisation_country->getcountry($order_info['shipping_country_id']);

        $payment_state = $order_info['payment_zone'] == '' ? $order_info['payment_city'] : $order_info['payment_zone'];
        $shipping_state = $order_info['shipping_zone'] == '' ? $order_info['shipping_city'] : $order_info['shipping_zone'];

        if($payment_country_info['iso_code_3'] != 'USA' or $payment_country_info['iso_code_3'] != 'CAN') {
            $postal_code1 = 11111;

            // in case of USA and CAN, state should be two digit ISO code
            $payment_zone = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
            $shipping_zone = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
            if(!empty($payment_zone['code'])){
                $payment_state = $payment_zone['code'];
            }
            if(!empty($shipping_zone['code'])){
                $shipping_state = $shipping_zone['code'];
            }
        }

        // paytabs does not allow more than 60 character long address.
        $shipping_address = $order_info['shipping_address_1'].' '.$order_info['shipping_address_2'];
        if(strlen($shipping_address) > 60){
            $shipping_address = substr($shipping_address,0, 60);
        }

        $billing_address = $order_info['payment_address_1'].' '.$order_info['payment_address_2'];
        if(strlen($billing_address) > 60){
            $billing_address = substr($billing_address,0, 60);
        }

	    $payment_data = array(
			'merchant_email'       => $this->config->get('paytabs_merchant'),
			'secret_key'           => $this->config->get('paytabs_password'),
			'cc_first_name'        => $order_info['payment_firstname'],
			'cc_last_name'         => !empty($order_info['payment_lastname'])?$order_info['payment_lastname']:$order_info['payment_firstname'],
			'cc_phone_number'      => $this->getccPhone($payment_country_info['iso_code_2']),
			'phone_number'         => $order_info['telephone'],
			'billing_address'      => $billing_address,
			'city'                 => $order_info['payment_city'],
			'postal_code'          => $order_info['payment_postcode'] == '' ? $postal_code1 : $order_info['payment_postcode'],
			'country'              => $payment_country_info['iso_code_3'],
			'email'                => $order_info['email'], 
			'quantity'             => $quantity,
			'amount'               => $price1,
			'currency'             => $order_info['currency_code'],//$this->currency->getCode(),
			'title'                => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
			'ip_customer'          => $this->request->getIpAddress,
			'ip_merchant'          => $_SERVER['SERVER_ADDR'],
            'address_shipping'     => $shipping_address,
            'city_shipping'        => $order_info['shipping_city'],
            'state'       		   => $payment_state,
            'state_shipping'       => $shipping_state,
            'postal_code_shipping' => $order_info['shipping_postcode'] == '' ? $postal_code1 : $order_info['shipping_postcode'],
            'country_shipping'     => $shipping_country_info['iso_code_3'],
            "unit_price"           => $price,
            "products_per_title"   => $products_per_title,
            'ProductCategory'      => $products_per_title,
            'ProductName'          => $products_per_title,
            'ShippingMethod'       => $this->session->data['shipping_method']['title'],
            'other_charges'        => $other_charges,
            'cms_with_version'     =>' Open Cart 2.3',
            'discount'    		   => $discount,
            'reference_no'         => $order_info['order_no'],
            'site_url'             => $this->config->get('config_url'),
            'return_url'           => $this->url->link('payment/paytabs/callback', 'order_id=' .$this->session->data['order_id'])
		);

        //print_r($payment_data);die;

        $_SESSION['secret_key'] 		 = $this->config->get('paytabs_password');
        $_SESSION['paytabs_merchant'] = $this->config->get('paytabs_merchant');

        $lng=substr($this->language->get('code'), 0);
			
        if ($lng =='en') {
            $payment_data['msg_lang']  = 'English';
        } else {
            $payment_data['msg_lang']  = 'Arabic';
        }

        $request_string1 = http_build_query($payment_data);
        $response_data   = $this->sendRequest('https://www.paytabs.com/apiv2/create_pay_page', $request_string1);
        $object 		  = json_decode($response_data);
        $this->log->write($object->result);

        if(isset($object->payment_url) && $object->payment_url != ''){
            $url = $object->payment_url;
            $pid = $object->p_id;
            $_SESSION['url'] = $url;
            $_SESSION['pid'] = $pid;
        } else {
            $response = isset($object->result)?$object->result:$object->details;
            $this->session->data['error'] = 'PayTabs Response: '.$response;
            $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL', 'payment'));

            /*$this->load->language('checkout/failure');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_basket'),
            'href' => $this->url->link('checkout/cart')
            );

            $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('checkout/checkout', '', 'SSL')
            );

            $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_failure'),
            'href' => $this->url->link('checkout/failure')
            );

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_message'] = $object->result;

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');
            return $this->response->setOutput($this->load->view('default/template/payment/paytabs_error.tpl', $data));
            //return $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/paytabs_error.tpl', $data));
            */
        }

        $url = $_SESSION['url'];
        $this->response->redirect($url);//Redirect to the payment page
    }
	

    //Get CC phone
	function getccPhone($code){
        $countries = array(
          "AF" => '+93',//array("AFGHANISTAN", "AF", "AFG", "004"),
          "AL" => '+355',//array("ALBANIA", "AL", "ALB", "008"),
          "DZ" => '+213',//array("ALGERIA", "DZ", "DZA", "012"),
          "AS" => '+376',//array("AMERICAN SAMOA", "AS", "ASM", "016"),
          "AD" => '+376',//array("ANDORRA", "AD", "AND", "020"),
          "AO" => '+244',//array("ANGOLA", "AO", "AGO", "024"),
          "AG" => '+1-268',//array("ANTIGUA AND BARBUDA", "AG", "ATG", "028"),
          "AR" => '+54',//array("ARGENTINA", "AR", "ARG", "032"),
          "AM" => '+374',//array("ARMENIA", "AM", "ARM", "051"),
          "AU" => '+61',//array("AUSTRALIA", "AU", "AUS", "036"),
          "AT" => '+43',//array("AUSTRIA", "AT", "AUT", "040"),
          "AZ" => '+994',//array("AZERBAIJAN", "AZ", "AZE", "031"),
          "BS" => '+1-242',//array("BAHAMAS", "BS", "BHS", "044"),
          "BH" => '+973',//array("BAHRAIN", "BH", "BHR", "048"),
          "BD" => '+880',//array("BANGLADESH", "BD", "BGD", "050"),
          "BB" => '1-246',//array("BARBADOS", "BB", "BRB", "052"),
          "BY" => '+375',//array("BELARUS", "BY", "BLR", "112"),
          "BE" => '+32',//array("BELGIUM", "BE", "BEL", "056"),
          "BZ" => '+501',//array("BELIZE", "BZ", "BLZ", "084"),
          "BJ" =>'+229',// array("BENIN", "BJ", "BEN", "204"),
          "BT" => '+975',//array("BHUTAN", "BT", "BTN", "064"),
          "BO" => '+591',//array("BOLIVIA", "BO", "BOL", "068"),
          "BA" => '+387',//array("BOSNIA AND HERZEGOVINA", "BA", "BIH", "070"),
          "BW" => '+267',//array("BOTSWANA", "BW", "BWA", "072"),
          "BR" => '+55',//array("BRAZIL", "BR", "BRA", "076"),
          "BN" => '+673',//array("BRUNEI DARUSSALAM", "BN", "BRN", "096"),
          "BG" => '+359',//array("BULGARIA", "BG", "BGR", "100"),
          "BF" => '+226',//array("BURKINA FASO", "BF", "BFA", "854"),
          "BI" => '+257',//array("BURUNDI", "BI", "BDI", "108"),
          "KH" => '+855',//array("CAMBODIA", "KH", "KHM", "116"),
          "CA" => '+1',//array("CANADA", "CA", "CAN", "124"),
          "CV" => '+238',//array("CAPE VERDE", "CV", "CPV", "132"),
          "CF" => '+236',//array("CENTRAL AFRICAN REPUBLIC", "CF", "CAF", "140"),
          "CM" => '+237',//array("CENTRAL AFRICAN REPUBLIC", "CF", "CAF", "140"),
          "TD" => '+235',//array("CHAD", "TD", "TCD", "148"),
          "CL" => '+56',//array("CHILE", "CL", "CHL", "152"),
          "CN" => '+86',//array("CHINA", "CN", "CHN", "156"),
          "CO" => '+57',//array("COLOMBIA", "CO", "COL", "170"),
          "KM" => '+269',//array("COMOROS", "KM", "COM", "174"),
          "CG" => '+242',//array("CONGO", "CG", "COG", "178"),
          "CR" => '+506',//array("COSTA RICA", "CR", "CRI", "188"),
          "CI" => '+225',//array("COTE D'IVOIRE", "CI", "CIV", "384"),
          "HR" => '+385',//array("CROATIA (local name: Hrvatska)", "HR", "HRV", "191"),
          "CU" => '+53',//array("CUBA", "CU", "CUB", "192"),
          "CY" => '+357',//array("CYPRUS", "CY", "CYP", "196"),
          "CZ" => '+420',//array("CZECH REPUBLIC", "CZ", "CZE", "203"),
          "DK" => '+45',//array("DENMARK", "DK", "DNK", "208"),
          "DJ" => '+253',//array("DJIBOUTI", "DJ", "DJI", "262"),
          "DM" => '+1-767',//array("DOMINICA", "DM", "DMA", "212"),
          "DO" => '+1-809',//array("DOMINICAN REPUBLIC", "DO", "DOM", "214"),
          "EC" => '+593',//array("ECUADOR", "EC", "ECU", "218"),
          "EG" => '+20',//array("EGYPT", "EG", "EGY", "818"),
          "SV" => '+503',//array("EL SALVADOR", "SV", "SLV", "222"),
          "GQ" => '+240',//array("EQUATORIAL GUINEA", "GQ", "GNQ", "226"),
          "ER" => '+291',//array("ERITREA", "ER", "ERI", "232"),
          "EE" => '+372',//array("ESTONIA", "EE", "EST", "233"),
          "ET" => '+251',//array("ETHIOPIA", "ET", "ETH", "210"),
          "FJ" => '+679',//array("FIJI", "FJ", "FJI", "242"),
          "FI" => '+358',//array("FINLAND", "FI", "FIN", "246"),
          "FR" => '+33',//array("FRANCE", "FR", "FRA", "250"),
          "GA" => '+241',//array("GABON", "GA", "GAB", "266"),
          "GM" => '+220',//array("GAMBIA", "GM", "GMB", "270"),
          "GE" => '+995',//array("GEORGIA", "GE", "GEO", "268"),
          "DE" => '+49',//array("GERMANY", "DE", "DEU", "276"),
          "GH" => '+233',//array("GHANA", "GH", "GHA", "288"),
          "GR" => '+30',//array("GREECE", "GR", "GRC", "300"),
          "GD" => '+1-473',//array("GRENADA", "GD", "GRD", "308"),
          "GT" => '+502',//array("GUATEMALA", "GT", "GTM", "320"),
          "GN" => '+224',//array("GUINEA", "GN", "GIN", "324"),
          "GW" => '+245',//array("GUINEA-BISSAU", "GW", "GNB", "624"),
          "GY" => '+592',//array("GUYANA", "GY", "GUY", "328"),
          "HT" => '+509',//array("HAITI", "HT", "HTI", "332"),
          "HN" => '+504',//array("HONDURAS", "HN", "HND", "340"),
          "HK" => '+852',//array("HONG KONG", "HK", "HKG", "344"),
          "HU" => '+36',//array("HUNGARY", "HU", "HUN", "348"),
          "IS" => '+354',//array("ICELAND", "IS", "ISL", "352"),
          "IN" => '+91',//array("INDIA", "IN", "IND", "356"),
          "ID" => '+62',//array("INDONESIA", "ID", "IDN", "360"),
          "IR" => '+98',//array("IRAN, ISLAMIC REPUBLIC OF", "IR", "IRN", "364"),
          "IQ" => '+964',//array("IRAQ", "IQ", "IRQ", "368"),
          "IE" => '+353',//array("IRELAND", "IE", "IRL", "372"),
          "IL" => '+972',//array("ISRAEL", "IL", "ISR", "376"),
          "IT" => '+39',//array("ITALY", "IT", "ITA", "380"),
          "JM" => '+1-876',//array("JAMAICA", "JM", "JAM", "388"),
          "JP" => '+81',//array("JAPAN", "JP", "JPN", "392"),
          "JO" => '+962',//array("JORDAN", "JO", "JOR", "400"),
          "KZ" => '+7',//array("KAZAKHSTAN", "KZ", "KAZ", "398"),
          "KE" => '+254',//array("KENYA", "KE", "KEN", "404"),
          "KI" => '+686',//array("KIRIBATI", "KI", "KIR", "296"),
          "KP" => '+850',//array("KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF", "KP", "PRK", "408"),
          "KR" => '+82',//array("KOREA, REPUBLIC OF", "KR", "KOR", "410"),
          "KW" => '+965',//array("KUWAIT", "KW", "KWT", "414"),
          "KG" => '+996',//array("KYRGYZSTAN", "KG", "KGZ", "417"),
          "LA" => '+856',//array("LAO PEOPLE'S DEMOCRATIC REPUBLIC", "LA", "LAO", "418"),
          "LV" => '+371',//array("LATVIA", "LV", "LVA", "428"),
          "LB" => '+961',//array("LEBANON", "LB", "LBN", "422"),
          "LS" => '+266',//array("LESOTHO", "LS", "LSO", "426"),
          "LR" => '+231',//array("LIBERIA", "LR", "LBR", "430"),
          "LY" => '+218',//array("LIBYAN ARAB JAMAHIRIYA", "LY", "LBY", "434"),
          "LI" => '+423',//array("LIECHTENSTEIN", "LI", "LIE", "438"),
          "LU" => '+352',//array("LUXEMBOURG", "LU", "LUX", "442"),
          "MO" => '+389',//array("MACAU", "MO", "MAC", "446"),
          "MG" => '+261',//array("MADAGASCAR", "MG", "MDG", "450"),
          "MW" => '+265',//array("MALAWI", "MW", "MWI", "454"),
          "MY" => '+60',//array("MALAYSIA", "MY", "MYS", "458"),     
          "MX" => '+52',//array("MEXICO", "MX", "MEX", "484"),
          "MC" => '+377',//array("MONACO", "MC", "MCO", "492"),
          "MA" => '+212',//array("MOROCCO", "MA", "MAR", "504"),
       
          "NP" => '+977',//array("NEPAL", "NP", "NPL", "524"),
          "NL" => '+31',//array("NETHERLANDS", "NL", "NLD", "528"),
          "NZ" => '+64',//array("NEW ZEALAND", "NZ", "NZL", "554"),
          "NI" => '+505',//array("NICARAGUA", "NI", "NIC", "558"),
          "NE" => '+227',//array("NIGER", "NE", "NER", "562"),
          "NG" => '+234',//array("NIGERIA", "NG", "NGA", "566"),
          "NO" => '+47',//array("NORWAY", "NO", "NOR", "578"),
          "OM" => '+968',//array("OMAN", "OM", "OMN", "512"),
          "PK" => '+92',//array("PAKISTAN", "PK", "PAK", "586"),
          "PA" => '+507',//array("PANAMA", "PA", "PAN", "591"),
          "PG" => '+675',//array("PAPUA NEW GUINEA", "PG", "PNG", "598"),
          "PY" =>'+595',// array("PARAGUAY", "PY", "PRY", "600"),
          "PE" =>'+51',// array("PERU", "PE", "PER", "604"),
          "PH" =>'+63',// array("PHILIPPINES", "PH", "PHL", "608"),
          "PL" => '48',//array("POLAND", "PL", "POL", "616"),
          "PT" => '+351',//array("PORTUGAL", "PT", "PRT", "620"),
          "QA" => '+974',//array("QATAR", "QA", "QAT", "634"),
          "RU" => '+7',//array("RUSSIAN FEDERATION", "RU", "RUS", "643"),
          "RW" => '+250',//array("RWANDA", "RW", "RWA", "646"),
          "SA" => '+966',//array("SAUDI ARABIA", "SA", "SAU", "682"),
          "SN" => '+221',//array("SENEGAL", "SN", "SEN", "686"),
          "SG" => '+65',//array("SINGAPORE", "SG", "SGP", "702"),
          "SK" => '+421',//array("SLOVAKIA (Slovak Republic)", "SK", "SVK", "703"),
          "SI" => '+386',//array("SLOVENIA", "SI", "SVN", "705"),
          "ZA" => '+27',//array("SOUTH AFRICA", "ZA", "ZAF", "710"),
          "ES" => '+34',//array("SPAIN", "ES", "ESP", "724"),
          "LK" => '+94',//array("SRI LANKA", "LK", "LKA", "144"),
          "SD" => '+249',//array("SUDAN", "SD", "SDN", "736"),
          "SZ" => '+268',//array("SWAZILAND", "SZ", "SWZ", "748"),
          "SE" => '+46',//array("SWEDEN", "SE", "SWE", "752"),
          "CH" => '+41',//array("SWITZERLAND", "CH", "CHE", "756"),
          "SY" => '+963',//array("SYRIAN ARAB REPUBLIC", "SY", "SYR", "760"),
          "TZ" => '+255',//array("TANZANIA, UNITED REPUBLIC OF", "TZ", "TZA", "834"),
          "TH" => '+66',//array("THAILAND", "TH", "THA", "764"),
          "TG" => '+228',//array("TOGO", "TG", "TGO", "768"),
          "TO" => '+676',//array("TONGA", "TO", "TON", "776"),
          "TN" => '+216',//array("TUNISIA", "TN", "TUN", "788"),
          "TR" => '+90',//array("TURKEY", "TR", "TUR", "792"),
          "TM" => '+993',//array("TURKMENISTAN", "TM", "TKM", "795"),
          "UA" => '+380',//array("UKRAINE", "UA", "UKR", "804"),
          "AE" => '+971',//array("UNITED ARAB EMIRATES", "AE", "ARE", "784"),
          "GB" => '+44',//array("UNITED KINGDOM", "GB", "GBR", "826"),
          "US" => '+1'//array("UNITED STATES", "US", "USA", "840"),
          
        );

        return $countries[$code];
    }

}

?>