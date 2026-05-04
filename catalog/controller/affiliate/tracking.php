<?php
class ControllerAffiliateTracking extends Controller {
	public function index() {
		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/tracking', '', 'SSL');

			$this->response->redirect($this->url->link('affiliate/login', '', 'SSL'));
		}

		$this->load->language('affiliate/tracking');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('affiliate/account', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('affiliate/tracking', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_description'] = sprintf($this->language->get('text_description'), $this->config->get('config_name'));

		$data['entry_code'] = $this->language->get('entry_code');
		$data['entry_generator'] = $this->language->get('entry_generator');
		$data['entry_link'] = $this->language->get('entry_link');

		$data['help_generator'] = $this->language->get('help_generator');

		$data['button_continue'] = $this->language->get('button_continue');

		$data['code'] = $this->affiliate->getCode();

		$data['continue'] = $this->url->link('affiliate/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/affiliate/tracking.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/affiliate/tracking.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/affiliate/tracking.tpl', $data));
		}
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results_data = $this->model_catalog_product->getProducts($filter_data);
			$results      = $results_data['products'];
			
			foreach ($results as $result) {
				$json[] = array(
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'link' => str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $result['product_id'] . '&tracking=' . $this->affiliate->getCode()))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * displays data related to referrals
	 * @author Anurag Jain, 10th Aug 2019
	 */
	public function trackReferrals()
	{	
		$this->load->model( 'affiliate/affiliate' );

		$referrals_data = array();

		$referrals_data['total_generated_referral_codes'] = $this->model_affiliate_affiliate->getTotalGeneratedReferralCodes();
		$referrals_data['total_signups_using_referral_codes'] = $this->model_affiliate_affiliate->getTotalSignupsUsingReferralCodes();
		$referrals_data['all_order_using_referral_codes'] = $this->model_affiliate_affiliate->getAllOrdersOfReferredCustomers();

		echo "Total Generated Referral Codes: <b>" . $referrals_data['total_generated_referral_codes'] . "</b><br><br>";
		echo "Total Signups Using Referral Codes: <b>" . $referrals_data['total_signups_using_referral_codes'] . "</b><br><br>";
		echo "All Orders Using Referral Codes: <b>" . count( $referrals_data['all_order_using_referral_codes'] ) . "</b>";

		if ( empty( $referrals_data['all_order_using_referral_codes'] )) {
			echo "0"; 
			exit();
		} else {
			echo "<br><br>";
			echo "<table border=1 cellpadding='5'>
					<th>S. No.</th>
					<th>Order Id</th>
					<th>Order No</th>
					<th>Customer Id</th>
					<th>Customer Firstname</th>
					<th>Referral Code</th>
					<th>Referrer Firstname</th>
					<th>Referrer Customer Id</th>";
					
					foreach ( $referrals_data['all_order_using_referral_codes'] as $key => $row_data ) {
						echo "<tr>";
						echo "<td>". ($key + 1) ."</td>";
						foreach ( $row_data as $key => $value ) {
							echo "<td>". $value ."</td>";
						}
						echo "</tr>";
					}
			echo "</table>";
		}
		exit();
	}
}