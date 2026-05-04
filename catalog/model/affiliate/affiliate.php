<?php
class ModelAffiliateAffiliate extends Model {
	public function addAffiliate($data) {
		$this->event->trigger('pre.affiliate.add', $data);

		$customer_id = !empty( $data['customer_id'] ) ? $this->db->escape( $data['customer_id'] ) : '0';
		$discount_amount = !empty( $data['discount_amount'] ) ? $this->db->escape( $data['discount_amount'] ) : '0.00';
		$uses_customer = !empty( $data['uses_customer'] ) ? $this->db->escape( $data['uses_customer'] ) : '0';
		$code = !empty( $data['code'] ) ? $this->db->escape( $data['code'] ) : $this->db->escape( uniqid() );
		$share_link = $data['share_link'] ?? "";
		$salt = "";
		$password = "";
		if ( !empty( $data['password'] )) {
			$salt = substr( md5( uniqid( rand(), true )), 0, 9 );
			$password = sha1( $salt . sha1( $salt . sha1( $data['password'] )));
		}
		$commission = !empty( $data['commission'] ) ? $this->db->escape( $data['commission'] ) : (float)$this->config->get('config_affiliate_commission');

		$this->db->query("
						INSERT INTO " . DB_PREFIX . "affiliate
						SET
							customer_id = '" . $customer_id . "',
							firstname = '" . $this->db->escape($data['firstname']) . "',
							lastname = '" . $this->db->escape($data['lastname']) . "',
							email = '" . $this->db->escape($data['email']) . "',
							telephone = '" . $this->db->escape($data['telephone']) . "',
							fax = '" . $this->db->escape($data['fax']) . "',
							salt = '" . $this->db->escape($salt) . "',
							password = '" . $this->db->escape($password) . "',
							company = '" . $this->db->escape($data['company']) . "',
							website = '" . $this->db->escape($data['website']) . "',
							address_1 = '" . $this->db->escape($data['address_1']) . "',
							address_2 = '" . $this->db->escape($data['address_2']) . "',
							city = '" . $this->db->escape($data['city']) . "',
							postcode = '" . $this->db->escape($data['postcode']) . "',
							country_id = '" . (int)$data['country_id'] . "',
							zone_id = '" . (int)$data['zone_id'] . "',
							code = '" . $code . "',
							share_link = '". $share_link ."',
							discount_amount = '" . $discount_amount . "',
							uses_customer = '" . $uses_customer . "',
							commission = '" . $commission . "',
							tax = '" . $this->db->escape($data['tax']) . "',
							payment = '" . $this->db->escape($data['payment']) . "',
							cheque = '" . $this->db->escape($data['cheque']) . "',
							paypal = '" . $this->db->escape($data['paypal']) . "',
							bank_name = '" . $this->db->escape($data['bank_name']) . "',
							bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "',
							bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "',
							bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "',
							bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "',
							status = '1',
							approved = '" . (int)!$this->config->get('config_affiliate_approval') . "',
							date_added = NOW()
						");

		$affiliate_id = $this->db->getLastId();

		if ( empty( $data['dont_send_mail'] )) {

			$this->load->language('mail/affiliate');

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
			$message .= $this->language->get('text_approval') . "\n";

			if (!$this->config->get('config_affiliate_approval')) {
				$message .= $this->language->get('text_login') . "\n";
			} else {
				$message .= $this->language->get('text_approval') . "\n";
			}

			$message .= $this->url->link('affiliate/login', '', 'SSL') . "\n\n";
			$message .= $this->language->get('text_services') . "\n\n";
			$message .= $this->language->get('text_thanks') . "\n";
			$message .= html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			/*$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->request->post['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();
			*/


			$mail = new PHPMailer(true);
			$mail->isSMTP();
			$mail->maillerDebug = true;
			$mail->SMTPDebug = 0;
			$mail->Debugoutput = 'html';
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');

			//echo $this->config->get('config_email'); die;
			$mail->addAddress($this->request->post['email'], 'Customer');
			$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesaelbox');
			//$mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
			$mail->Subject = $subject;
			$mail-> Body =$message;

			$mail->send();

			// Send to main admin email if new affiliate email is enabled
			if ($this->config->get('config_affiliate_mail')) {
				$message  = $this->language->get('text_signup') . "\n\n";
				$message .= $this->language->get('text_store') . ' ' . html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8') . "\n";
				$message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
				$message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";

				if ($data['website']) {
					$message .= $this->language->get('text_website') . ' ' . $data['website'] . "\n";
				}

				if ($data['company']) {
					$message .= $this->language->get('text_company') . ' '  . $data['company'] . "\n";
				}

				$message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";
				$message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";

				$mail->setTo($this->config->get('config_email'));
				$mail->setSubject(html_entity_decode($this->language->get('text_new_affiliate'), ENT_QUOTES, 'UTF-8'));
				$mail->setText($message);
				$mail->send();

				// Send to additional alert emails if new affiliate email is enabled
				$emails = explode(',', $this->config->get('config_mail_alert'));

				foreach ($emails as $email) {
					if (utf8_strlen($email) > 0 && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}
		}

		$this->event->trigger('post.affiliate.add', $affiliate_id);

		return $affiliate_id;
	}

	public function editAffiliate($data) {
		$this->event->trigger('pre.affiliate.edit', $data);

		$affiliate_id = $this->affiliate->getId();

		$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		$this->event->trigger('post.affiliate.edit', $affiliate_id);
	}

	public function editPayment($data) {
		$this->event->trigger('pre.affiliate.edit.payment', $data);

		$affiliate_id = $this->affiliate->getId();

		$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		$this->event->trigger('post.affiliate.edit.payment', $affiliate_id);
	}

	public function editPassword($affiliate_id, $password)
	{
		//$affiliate_id = $this->affiliate->getId();
		$this->event->trigger('pre.affiliate.edit.password', $affiliate_id);
		$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' WHERE affiliate_id = '" . (int) $affiliate_id . "'");

		$this->event->trigger('post.affiliate.edit.password', $affiliate_id);
	}

	public function getAffiliate($affiliate_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		return $query->row;
	}

	public function getAffiliateByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getAffiliateByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function getTotalAffiliatesByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function addTransaction($affiliate_id, $amount = '', $order_id = 0) {
		$affiliate_info = $this->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$this->event->trigger('pre.affiliate.add.transaction');

			$this->load->language('mail/affiliate');

			$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate_transaction SET affiliate_id = '" . (int)$affiliate_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($this->language->get('text_order_id') . ' #' . $order_id) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$affiliate_transaction_id = $this->db->getLastId();

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($affiliate_id), $this->config->get('config_currency')));

			/*$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($affiliate_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();
			*/

			$mail = new PHPMailer(true);
			$mail->isSMTP();
			$mail->maillerDebug = true;
			$mail->SMTPDebug = 0;
			$mail->Debugoutput = 'html';
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');

			//echo $this->config->get('config_email'); die;
			$mail->addAddress($affiliate_info['email'], 'Customer');
			$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesaelbox');
			//$mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
			$mail->Subject = sprintf($this->language->get('text_transaction_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail-> Body =$message;

			$mail->send();

			$this->event->trigger('post.affiliate.add.transaction', $affiliate_transaction_id);
		}
	}

	public function deleteTransaction($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactionTotal($affiliate_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "affiliate_transaction WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		return $query->row['total'];
	}

	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->getIpAddress) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->getIpAddress) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "affiliate_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE affiliate_login_id = '" . (int)$query->row['affiliate_login_id'] . "'");
		}
	}

	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	/**
	 * prepares data to insert in table
	 * @param  array $affiliate_data [description]
	 * @return array                 [description]
	 * @author Anurag Jain, 15th July 2019
	 */
	public function prepareAffiliateData( array $affiliate_data )
	{
		$final_affiliate_data = array();
		$final_affiliate_data['customer_id'] = $affiliate_data['customer_id'] ?? '0';
		$final_affiliate_data['firstname'] = $affiliate_data['firstname'] ?? "";
		$final_affiliate_data['lastname'] = $affiliate_data['lastname'] ?? "";
		$final_affiliate_data['email'] = $affiliate_data['email'] ?? "";
		$final_affiliate_data['telephone'] = $affiliate_data['telephone'] ?? "";
		$final_affiliate_data['fax'] = $affiliate_data['fax'] ?? "";
		$final_affiliate_data['salt'] = $affiliate_data['salt'] ?? "";
		$final_affiliate_data['password'] = $affiliate_data['password'] ?? "";
		$final_affiliate_data['company'] = $affiliate_data['company'] ?? "";
		$final_affiliate_data['website'] = $affiliate_data['website'] ?? "";
		$final_affiliate_data['address_1'] = $affiliate_data['address_1'] ?? "";
		$final_affiliate_data['address_2'] = $affiliate_data['address_2'] ?? "";
		$final_affiliate_data['city'] = $affiliate_data['city'] ?? "";
		$final_affiliate_data['postcode'] = $affiliate_data['postcode'] ?? "";
		$final_affiliate_data['country_id'] = $affiliate_data['country_id'] ?? '0';
		$final_affiliate_data['zone_id'] = $affiliate_data['zone_id'] ?? "0";
		$final_affiliate_data['code'] = $affiliate_data['code'] ?? "";
		$final_affiliate_data['share_link'] = $affiliate_data['share_link'] ?? "";
		$final_affiliate_data['discount_amount'] = $affiliate_data['discount_amount'] ?? "0.00";
		$final_affiliate_data['uses_customer'] = $affiliate_data['uses_customer'] ?? "0";
		$final_affiliate_data['commission'] = $affiliate_data['commission'] ?? "0.00";
		$final_affiliate_data['tax'] = $affiliate_data['tax'] ?? "";
		$final_affiliate_data['payment'] = $affiliate_data['payment'] ?? "";
		$final_affiliate_data['cheque'] = $affiliate_data['cheque'] ?? "";
		$final_affiliate_data['paypal'] = $affiliate_data['paypal'] ?? "";
		$final_affiliate_data['bank_name'] = $affiliate_data['bank_name'] ?? "";
		$final_affiliate_data['bank_branch_number'] = $affiliate_data['bank_branch_number'] ?? "";
		$final_affiliate_data['bank_swift_code'] = $affiliate_data['bank_swift_code'] ?? "";
		$final_affiliate_data['bank_account_name'] = $affiliate_data['bank_account_name'] ?? "";
		$final_affiliate_data['bank_account_number'] = $affiliate_data['bank_account_number'] ?? "";
		$final_affiliate_data['ip'] = $affiliate_data['ip'] ?? "";
		$final_affiliate_data['status'] = $affiliate_data['status'] ?? '1';
		$final_affiliate_data['approved'] = $affiliate_data['approved'] ?? '1';
		$final_affiliate_data['date_added'] = 'NOW()';

		return $final_affiliate_data;
	}

	/**
	 * @param array $affiliate_data [description]
	 * @return int added affiliate id
	 * @author Anurag Jain, 15 July 2019
	 */
	public function createCustomerAffiliate( int $customer_id ): int
	{
		$affiliate_id = 0;
		$customer_data =array();

    	$affiliate_sql = "
                        SELECT 
                            c.customer_id,
                            c.firstname,
                            c.lastname,
                            c.email,
                            c.telephone
                        FROM ". DB_PREFIX ."customer c 
                        WHERE customer_id = '". $customer_id ."'
                        ";
        $affiliate_result = $this->db->query( $affiliate_sql );

        if ( $affiliate_result->num_rows ) {

        	$customer_data = $affiliate_result->row;
	    	$customer_data['code'] = $this->generateAffiliateTrackCode( $customer_data );
	        $customer_data['commission'] = '3.00';
	        $customer_data['discount_amount'] = '50.00';
	        $customer_data['uses_customer'] = '1';
	        $customer_data['share_link'] = $this->generateReferralShareLink( $customer_data['code'] );

			$final_affiliate_data = $this->prepareAffiliateData( $customer_data );
			$final_affiliate_data['dont_send_mail'] = true;
			$affiliate_id = $this->addAffiliate( $final_affiliate_data );
        }

		return $affiliate_id;
	}

	/**
	 * generates AffiliateTrackCode
	 * Algo:
	 * 	- Convert customer id to base 32
	 * 	- pad with leading zeros if less than 5 chars
	 * 	- skip char - I, L, O, 0 due to their vague resemblance => l and I, O and 0
	 * 	- replace them with W,X,Y,Z
	 * 	- cleanup firstname (and get 4 first chars)
	 * 	- Final Ref Code: firstname + base32_customer_id
	 * @param  array  $customer_data [description]
	 * @return string                [description]
	 * @author Anurag Jain, 16 July 2019
	 */
    public function generateAffiliateTrackCode( array $customer_data ): string
    {
        $filling_letters = array( 'I' => 'W', 'L' => 'X', 'O' => 'Y', '0' => 'Z' );

        $base32_number = base_convert( (int) $customer_data['customer_id'], 10, 32 ); // base decimal to 32
        $base32_number = strtoupper( $base32_number );

        foreach ( $filling_letters as $search => $replace ) {
            $base32_number = str_replace( $search, $replace, $base32_number );
        }

        $firstname = preg_replace( "/[^a-zA-Z0-9]+/", "", $customer_data['firstname'] );

        $firstname = !empty( $firstname ) ? substr( $firstname  , 0, 4 ) : "WHOLESALE";
        $referral_code = $firstname . $base32_number;

        return strtoupper( $referral_code );
    }

    /**
     * @param  int    $customer_id [description]
     * @return array              [description]
	 * @author Anurag Jain, 17 July 2019
     */
	public function getAffiliateByCustomerId( int $customer_id ): array
	{
		$query = $this->db->query(
									"
									SELECT
										*
									FROM " . DB_PREFIX . "affiliate
									WHERE customer_id = '" . $customer_id . "'",
									true
								);

		return $query->row;
	}

	/**
	 * @param  int    $customer_id [description]
	 * @return array              [description]
	 * @author Anurag Jain, 17 July 2019
	 */
	public function getReferralDetailsOfCustomer( int $customer_id ): array
	{
		$referral_details = array();
		$order_count = 0;
		$order_total = 0;
		$signup_count = 0;


    	$affiliate_details = $this->getAffiliateByCustomerId( $customer_id );

    	if ( !empty( $affiliate_details )) {

    		$referral_details['order_count'] = $this->getTotalOrdersOfReferredCustomers( (int) $affiliate_details['affiliate_id'] , true );


    		$referral_details['referral_count'] = $this->getAffiliateReferralCount( (int) $affiliate_details['affiliate_id'] );


    		$total_earning = $this->getAffiliateTentativeCommissionAmount( (int) $affiliate_details['affiliate_id'] );

    		$referral_details['total_earning'] = $this->currency->format($total_earning['commission']);

    		$referral_details['commission_amount'] = $this->currency->format($this->getAffiliateCurrentCommissionAmount( (int) $affiliate_details['affiliate_id'], 'PENDING' ));

    		$referral_details['paid_commission_amount'] = $this->currency->format($this->getAffiliateCurrentCommissionAmount( (int) $affiliate_details['affiliate_id'], 'PAID' ));


    		$referral_details['share_link'] = $affiliate_details['share_link'];

    		$referral_details['code']       = $affiliate_details['code'];

    		if ( empty( $referral_details['share_link'] && !empty( $referral_details['code'] ))) {

    			$link = $this->generateReferralShareLink( $referral_details['code'] );
				if ( !empty( $link )) {
					$this->insertReferralShareLink( $affiliate_details['affiliate_id'], $link );
				}
    		}
    	}

    	return $referral_details;
	}

	/**
	 * @param  int    $affiliate_id [description]
	 * @return int               [description]
	 * @author Anurag Jain, 17 July 2019
	 */
	public function getAffiliateOrdersCount( int $affiliate_id ): int
	{
		$referral_count = 0;
		$sql = "SELECT 
					count(oo.order_id) as referral_count
				FROM ". DB_PREFIX ."coupon c
				INNER JOIN ". DB_PREFIX ."coupon_history ch ON ch.coupon_id = c.coupon_id
				INNER JOIN ". DB_PREFIX ."order oo ON oo.order_id = ch.order_id
				INNER JOIN ". DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN ". DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id
				WHERE 
					oa.affiliate_id = '". (int)$affiliate_id ."'" ;

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$referral_count = $result->row['referral_count'] ?? 0;
		}

		return (int) $referral_count;
	}

	
	/**
	 * @param  int    $affiliate_id [description]
	 * @return int               [description]
	 * @author Mahaveer Choudhary, sept 2019
	 */
	public function getAffiliateCurrentCommissionOrderAmount( int $affiliate_id)
	{
			 $sql = "SELECT sum(order_amount) as order_amount 
			         from ". DB_PREFIX ."affiliate_commission
			         where affiliate_id = '". (int) $affiliate_id ."'";

         $result = $this->db->query( $sql );

         $order_amount = 0;
         if ( $result->num_rows ) 
         {
            $order_amount = $result->row['order_amount'];
         }  

		return $order_amount;
	}
	/**
	 * @param  int    $affiliate_id [description]
	 * @return int               [description]
	 * @author Anurag Jain, 17 July 2019
	 */
	public function getAffiliateCurrentCommissionAmount( int $affiliate_id, string $status = '' )
	{
			 $sql = "SELECT sum(commission) as commission_amount 
			         from ". DB_PREFIX ."affiliate_commission
			         where affiliate_id = '". (int) $affiliate_id ."'";
			
			 if($status != '')
			 {
			 	$sql .= " AND payment_status = '".$status."'";
			 }

         $result = $this->db->query( $sql );

         $commission_amount = 0;
         if ( $result->num_rows ) 
         {
            $commission_amount = $result->row['commission_amount'];
         }  

		return $commission_amount;
	}
	
    	/**
	 * @param  int    $affiliate_id [description]
	 * @return array               [description]
	 * @author Mahaveer Choudhary, 23 Sept 2019
	 */
	public function getAffiliateTentativeCommissionAmount( int $affiliate_id ) : array
	{
			    $sql = "SELECT
			          o.order_id,
			          o.customer_id,
                      SUM(oop.quantity * oop.piece_in_set * (oop.price_per_piece + oop.discount_per_piece)) AS net_sale
                     FROM
                       " . DB_PREFIX ."order o
                     JOIN " . DB_PREFIX ."suborder osub ON osub.order_id = o.order_id
                     AND osub.order_status_id > 0 AND osub.order_status_id <> 2
                     
                     JOIN " . DB_PREFIX ."order_product oop ON oop.suborder_id = osub.suborder_id
                     JOIN " . DB_PREFIX ."customer c ON c.customer_id = o.customer_id
                     JOIN " . DB_PREFIX ."affiliate a ON a.code = c.referral_code
                     JOIN " . DB_PREFIX ."customer c2 ON c2.customer_id = a.customer_id
                     LEFT JOIN " . DB_PREFIX ."affiliate_commission ac ON ac.order_id = o.order_id
                     WHERE
                        a.affiliate_id = '". (int) $affiliate_id  ."'
                        AND o.store_id IN (0,2,9) 
                        AND o.stock_transfer = 0 
                        AND o.franchise_id = 0
                        AND ac.order_id IS NULL
                     GROUP BY o.order_id   
                     ORDER BY o.date_added ASC";
                    

         $result = $this->db->query( $sql );

         //get affiliate cuurent commission amount and order amount
         $commission_amount  =  $this->getAffiliateCurrentCommissionAmount( (int) $affiliate_id );
          $net_sale          =  $this->getAffiliateCurrentCommissionOrderAmount( (int) $affiliate_id );


         //get affiliate commission rates
         $commission_rates = $this->getAffiliateCommissionRatesForCommission(array($affiliate_id));

         //get affiliate orders count numbers
         $order_counts = $this->getAffiliateCommissionOrdersCount(array($affiliate_id));

       foreach($result->rows as $row)
        {
           if(isset($order_counts[$affiliate_id]))
            {
               if(isset($order_counts[$affiliate_id][$row['customer_id']]))
               {
                 $order_counts[$affiliate_id][$row['customer_id']]++;
               }
               else
               {
                 $order_counts[$affiliate_id][$row['customer_id']] = 1;
               }
            }
            else
            {
              $order_counts[$affiliate_id][$row['customer_id']] = 1;
            }
            

           $current_order_count = $order_counts[$affiliate_id][$row['customer_id']];

            //calculate affiliate commission rate
            $referral_commission =  $this->getCommissionRates((array) $commission_rates, (int) $affiliate_id, (int) $current_order_count);
         
            if (isset($row['net_sale'])) 
            {
               $net_sale += $row['net_sale'];
              $commission_amount += ($referral_commission/100)*$row['net_sale'];
            } 
        }  

		return array('net_sale' => $net_sale, 'commission' => $commission_amount);

	}

	/**
	 * @param  string $referral_code [description]
	 * @author Anurag Jain, 17 July 2019
	 */
	public function generateReferralShareLink( string $referral_code )
	{
		$base_url = "https://play.google.com/store/apps/details?id=in.wholesalebox&referrer=";
		$base_url .= "utm_campaign%3DReferral";
		$base_url .= "%26utm_medium%3DReferral";
		$base_url .= "%26utm_source%3DAndroid";
		$base_url .= "%26utm_channel%3DReferral";
		$base_url .= "%26referral_code%3D" . strtoupper( $referral_code );

		$bitly_url = "";
		$bitly_url_data = convertUrlToShortUrl( $base_url );

		if ( !empty( $bitly_url_data['status'] )) {
			$bitly_url = $bitly_url_data['message'] ?? "";
		}

		return $bitly_url;
	}

	/**
	 * @param  int    $affiliate_id [description]
	 * @param  string $link         [description]
	 * @author Anurag Jain, 17 July 2019
	 */
	public function insertReferralShareLink( int $affiliate_id, string $link )
	{
		$insert_sql = "
						UPDATE ". DB_PREFIX ."affiliate
							SET share_link = '". $this->db->escape( $link ) ."'
						WHERE affiliate_id = '". $affiliate_id ."'
						";
		return $this->db->query( $insert_sql );
	}

	/**
	 * @param  int    $affiliate_id [description]
	 * @return int               [description]
	 * @author Anurag Jain, 19 July 2019
	 */
	public function getAffiliateReferralCount( int $affiliate_id ): int
	{
		$referral_count = 0;
		$sql = "
				SELECT
					count(c.customer_id) as referral_count
				FROM ". DB_PREFIX ."customer c
					INNER JOIN ". DB_PREFIX ."affiliate a ON a.code = c.referral_code 
				WHERE a.affiliate_id = '". $affiliate_id ."'" ;
		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$referral_count = $result->row['referral_count'] ?? 0;
		}

		return (int) $referral_count;
	}
	/*
	 * gets count of generated referral codes
	 * @return int
	 * @author Anurag Jain, 10th Aug 2019
	 */
	public function getTotalGeneratedReferralCodes(): int
	{
		$sql = "
				SELECT 
					count( oa.customer_id ) AS referral_code_count
				FROM ". DB_PREFIX ."affiliate oa
				 WHERE 
					oa.customer_id is not null
				    AND oa.code is not null
				    AND oa.share_link is not null";

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			return (int)$result->row['referral_code_count'] ?? 0;
		}

		return 0; 
	}

	/**
	 * gets count of signups using referral codes
	 * @return int
	 * @author Anurag Jain, 10th Aug 2019
	 */
	public function getTotalSignupsUsingReferralCodes(): int
	{
		$sql = "
				SELECT 
					count(cc.customer_id) AS signups_count
				FROM ". DB_PREFIX ."customer cc
					INNER JOIN ". DB_PREFIX ."affiliate oa 
						ON oa.code = cc.referral_code
				 WHERE oa.customer_id is not null";

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			return (int)$result->row['signups_count'] ?? 0;
		}

		return 0; 
	}

	/**
	 * gets order, customer and referrer details
	 * @return array
	 * @author Anurag Jain, 10th Aug 2019
	 */
	public function getAllOrdersUsingReferralCoupons(): array
	{
		$referral_order_data = array();
		$sql = "
				SELECT 
					oo.order_id,
				    oo.order_no,
				    cust.customer_id AS `customer_id`,
				    cust.firstname AS `customer_firstname`,
				    cust.referral_code AS `referral_code`,
				    cc.firstname AS `referrer_firstname`,
				    cc.customer_id AS `referrer_customer_id`
				FROM ". DB_PREFIX ."coupon c
				INNER JOIN ". DB_PREFIX ."coupon_history ch ON ch.coupon_id = c.coupon_id
				INNER JOIN ". DB_PREFIX ."order oo ON oo.order_id = ch.order_id
				INNER JOIN ". DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN ". DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id
				WHERE 
					c.name like 'REFERRAL%'";

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			return $result->rows;
		}

		return $referral_order_data; 
	}

	/**
	 * gets order count
	 * @return int
	 * @author Mahaveer, 29th Aug 2019
	 */
	public function getTotalOrdersOfReferredCustomers( int $affiliate_id = 0 ): int
	{
		$referral_order_data = array();
		    $sql = "SELECT 
					count(oo.order_id) as order_count
				FROM " . DB_PREFIX ."order oo
				INNER JOIN " . DB_PREFIX ."suborder osub ON osub.order_id = oo.order_id
				INNER JOIN " . DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN " . DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN " . DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id 
				where osub.order_status_id > '0'
			    AND oa.affiliate_id = '". $affiliate_id ."'
			    ORDER BY NULL";
	
		    $result = $this->db->query( $sql );

		    if ( $result->num_rows ) {
			return (int) $result->row['order_count'];
		    }

			return 0;
	}
	
	/**
	 * @param  array    $filter [description]
	 * @return array            [description]
	 * @author Mahaveer Choudhary, Aug 2019
	 */
	public function getAffiliateList( array $filter_data ): array
	{  
		$result = array();

		$sql = "SELECT 
					oa.affiliate_id,
					oa.type,
				    cust.referral_code AS `referral_code`,
				    cc.firstname AS `referrer_firstname`,
				    cc.lastname AS `referrer_lastname`,
				    cc.customer_id AS `referrer_customer_id`,
				    cust.customer_id AS order_customer_customer_id,
				    cust.firstname AS order_customer_firstname,
				    cust.lastname AS order_customer_lastname,
				    cust.email AS order_customer_email,
				    cust.telephone AS order_customer_telephone,
				    count(DISTINCT oo.customer_id) AS signup_count,
				    count(DISTINCT oo.order_id) as order_count,
				    SUM(oo.total) AS order_total
				FROM ". DB_PREFIX ."order oo
				INNER JOIN `" . DB_PREFIX ."suborder` osub ON osub.order_id = oo.order_id 
				INNER JOIN ". DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN ". DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id
				where osub.order_status_id > '0'";
   
         if (!empty($filter_data['filter_date_added_from'])) {
            $sql .= " AND oa.date_added >= '" . $this->db->escape($filter_data['filter_date_added_from']) . " 00:00:00'";
        }

        if (!empty($filter_data['filter_date_added_to'])) {
            $sql .= " AND oa.date_added <= '" . $this->db->escape($filter_data['filter_date_added_to']) . " 23:59:59'";
        }

        $sql .= " group by oa.affiliate_id"; 


        if (isset($filter_data['sort'])) {
            $sql .= " ORDER BY " . $filter_data['sort'];
        } else {
            $sql .= " ORDER BY oa.affiliate_id";
        }
        if (isset($filter_data['order']) && ($filter_data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        $query = $this->db->query($sql);
        $total_count = $query->num_rows;

        if (isset($filter_data['start']) || isset($filter_data['limit'])) {
            if ($filter_data['start'] < 0) {
                $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
                $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
        }
        
        $query = $this->db->query($sql);
		return array($total_count, $query->rows);
    }


	/**
	 * get total order  excluding cancel
	 * @return array
	 * @author Mahaveer Choudhary, Aug 2019
	 */
	public function getTotalOrdersExcludingCancelAffiliateCoupons(array $filter_data): array
	{
		$order_count = 0;
		$order_total = 0;
		$sql = "
				SELECT 
					count(oo.order_id) as order_count,
					SUM(ROUND(oo.currency_value*osub.total/oo.live_currency_conversion_rate,2)) AS order_total
				FROM ". DB_PREFIX ."coupon c
				INNER JOIN ". DB_PREFIX ."coupon_history ch ON ch.coupon_id = c.coupon_id
				INNER JOIN ". DB_PREFIX ."order oo ON oo.order_id = ch.order_id
				INNER JOIN ". DB_PREFIX ."suborder osub ON osub.order_id = oo.order_id
				INNER JOIN ". DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN ". DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id
				WHERE 
					c.name like 'REFERRAL%' 
					AND osub.order_status_id > 0 AND osub.order_status_id <> 2";

		 if (!empty($filter_data['filter_date_added_from'])) {
            $sql .=" AND oa.date_added >= '" . $this->db->escape($filter_data['filter_date_added_from']) . " 00:00:00'";
        }

        if (!empty($filter_data['filter_date_added_to'])) {
            $sql .= " AND oa.date_added <= '" . $this->db->escape($filter_data['filter_date_added_to']) . " 23:59:59'";
        }

         if (!empty($filter_data['affiliate_id'])) {
            $sql .= " AND oa.affiliate_id = '" . (int)$filter_data['affiliate_id'] . "'";
        }			

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$order_count = $result->row['order_count'];
			$order_total = $this->currency->format($result->row['order_total'], $this->config->get('config_currency'));
		}

		return array($order_count, $order_total); 
	}

	/**
	 * get total order 
	 * @return array
	 * @author Mahaveer Choudhary, Aug 2019
	 */
	public function getTotalOrdersAffiliateCoupons(array $filter_data): array
	{
		$order_count = 0;
		$order_total = 0;
		$sql = "
				SELECT 
					count(oo.order_id) as order_count,
					SUM(oo.total) AS order_total
				FROM ". DB_PREFIX ."order oo
				INNER JOIN `" . DB_PREFIX . "suborder` osub ON osub.order_id = oo.order_id 
				INNER JOIN ". DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN ". DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id
				WHERE osub.order_status_id > '0'";

		 if (!empty($filter_data['filter_date_added_from'])) {
            $sql .=" AND oa.date_added >= '" . $this->db->escape($filter_data['filter_date_added_from']) . " 00:00:00'";
        }

        if (!empty($filter_data['filter_date_added_to'])) {
            $sql .= " AND oa.date_added <= '" . $this->db->escape($filter_data['filter_date_added_to']) . " 23:59:59'";
        }

         if (!empty($filter_data['affiliate_id'])) {
            $sql .= " AND oa.affiliate_id = '" . (int)$filter_data['affiliate_id'] . "'";
        }			
         
		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$order_count = $result->row['order_count'];
			$order_total = $this->currency->format($result->row['order_total'], $this->config->get('config_currency'));
		}

		return array($order_count, $order_total); 
	}

	/**
	 * gets count of signups using referral codes
	 * @return int
	 * @author Mahaveer Choudhary, Aug 2019
	 */
	public function getTotalSignupsAffiliateCoupons(array $filter_data): int
	{
		$sql = "
				SELECT 
					count(cc.customer_id) AS signups_count
				FROM ". DB_PREFIX ."customer cc
					INNER JOIN ". DB_PREFIX ."affiliate oa 
						ON oa.code = cc.referral_code
				 WHERE oa.customer_id is not null";

		 if (!empty($filter_data['filter_date_added_from'])) {
            $sql .=" AND oa.date_added >= '" . $this->db->escape($filter_data['filter_date_added_from']) . " 00:00:00'";
        }

        if (!empty($filter_data['filter_date_added_to'])) {
            $sql .= " AND oa.date_added <= '" . $this->db->escape($filter_data['filter_date_added_to']) . " 23:59:59'";
        }

         if (!empty($filter_data['affiliate_id'])) {
            $sql .= " AND oa.affiliate_id = '" . (int)$filter_data['affiliate_id'] . "'";
        }			
		 

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			return (int)$result->row['signups_count'] ?? 0;
		}

		return 0; 
	}	

	/*
	 * gets count of generated referral codes
	 * @return int
	 * @author Mahaveer Choudhary, Sept 2019
	 */
	public function getTotalReferralCodes(array $filter_data): int
	{
		$sql = "
				SELECT 
					count( oa.customer_id ) AS referral_code_count
				FROM ". DB_PREFIX ."affiliate oa
				 WHERE 
					oa.customer_id is not null
				    AND oa.code is not null
				    AND oa.share_link is not null";


        if (!empty($filter_data['filter_date_added_from'])) {
            $sql .=" AND oa.date_added >= '" . $this->db->escape($filter_data['filter_date_added_from']) . " 00:00:00'";
        }

        if (!empty($filter_data['filter_date_added_to'])) {
            $sql .= " AND oa.date_added <= '" . $this->db->escape($filter_data['filter_date_added_to']) . " 23:59:59'";
        }

		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			return (int)$result->row['referral_code_count'] ?? 0;
		}

		return 0; 
	}


     /**
	 * gets count of signups using referral codes
	 * @return int
	 * @author Mahaveer Choudhary, Aug 2019
	 */
	public function getAffiliateSignups(array $affiliate_id): array
	{
		$return = array();
		$sql = "
				SELECT 
				    oa.affiliate_id,
					count(cc.customer_id) AS signups_count
				FROM ". DB_PREFIX ."customer cc
					INNER JOIN ". DB_PREFIX ."affiliate oa 
						ON oa.code = cc.referral_code
				 WHERE oa.customer_id is not null";
            
            $affiliate_id = implode(",", $affiliate_id);
      
            $sql .= " AND oa.affiliate_id IN(" . $affiliate_id . ")";

            $sql .= " group by oa.affiliate_id";
      			  
		$result = $this->db->query( $sql );

		if ( $result->num_rows ) 
		{
          foreach($result->rows as $row)
          {
            $return[$row['affiliate_id']] = $row['signups_count'];
          } 
			
		}
		return $return;
		
	}

     /**
	 * gets all commission rates of affiliate
	 * @return array
	 * @author Mahaveer Choudhary, Sept 2019
	 */
	public function getAffiliateCommissionRates(int $affiliate_id): array
	{
       
       $sql = "SELECT 
				    commission_rate_id,
				    affiliate_id,
				    order_count_from,
				    order_count_to,
				    rate,
				    created,
				    modified
				FROM ". DB_PREFIX ."affiliate_commission_rate
				 WHERE affiliate_id = '". (int) $affiliate_id  ."'
				 ORDER BY created ASC"; 

        $query = $this->db->query( $sql );

        return $query->rows;

	}

	/**
	 * save commission rates of affiliate
	 * @return bool
	 * @author Mahaveer Choudhary, Sept 2019
	 */
	public function saveCommissionRates(array $data): bool
	{
       
       $sql = "INSERT INTO ". DB_PREFIX ."affiliate_commission_rate 
                   SET
				    affiliate_id = '". (int) $data['affiliate_id'] ."',
				    order_count_from = '". (int) $data['order_count_from'] ."',
				    order_count_to = '". (int) $data['order_count_to'] ."',
				    rate = '". (int) $data['rate'] ."',
				    created = NOW(),
				    modified = NOW()"; 

        $query = $this->db->query( $sql );

        return true;
	}

	/**
	 * update commission rates of affiliate
	 * @return bool
	 * @author Mahaveer Choudhary, Sept 2019
	 */
	public function updateCommissionRates(array $data): bool
	{
       
       $sql = "UPDATE ". DB_PREFIX ."affiliate_commission_rate 
                   SET
				    affiliate_id = '". (int) $data['affiliate_id'] ."',
				    order_count_from = '". (int) $data['order_count_from'] ."',
				    order_count_to = '". (int) $data['order_count_to'] ."',
				    rate = '". (int) $data['rate'] ."',
				    modified = NOW()
				    where commission_rate_id = '". (int) $data['commission_rate_id'] ."'"; 
        $query = $this->db->query( $sql );

        return true;
	}

	/**
	 * delete commission rates of affiliate
	 * @return bool
	 * @author Mahaveer Choudhary, Sept 2019
	 */
	public function deleteCommissionRate(int $commission_rate_id): bool
	{
       
       $sql = "DELETE FROM ". DB_PREFIX ."affiliate_commission_rate 
                   where 
				    commission_rate_id = '". (int) $commission_rate_id ."'"; 

        $query = $this->db->query( $sql );

        return true;
	}

	  /**  
    get affiliate orders count number
   * @param  array    $affiliate_ids [description]
   * @return array               [description]
   * @author Mahaveer Choudhary, Sept 2019
   */
 public function getAffiliateCommissionOrdersCount(array $affiliate_ids): array
 {
    $order_counts = array();
    $affiliate_ids = implode(",", $affiliate_ids);

    $counts_sql = "SELECT  
                       affiliate_id,
                       customer_id,
                       count(affiliate_id) as order_count
                            FROM " . DB_PREFIX ."affiliate_commission 
                            where affiliate_id IN (".$affiliate_ids.")
                            GROUP BY affiliate_id, customer_id
                            ORDER BY NULL";
                  
    $counts_result = $this->db->query( $counts_sql ); 
     foreach($counts_result->rows as $key => $row)
     {
       $order_counts[$row['affiliate_id']][$row['customer_id']] = $row['order_count'];
     }   
    return $order_counts;  
 }
  /**  
    get commission rate of affiliates
   * @param  array    $affiliate_ids [description]
   * @return array               [description]
   * @author Mahaveer Choudhary, Sept 2019
   */
 public function getAffiliateCommissionRatesForCommission(array $affiliate_ids):array
 {
   $commission_rates = array();
   $affiliate_ids = implode(",", $affiliate_ids);

    $commission_sql = "SELECT 
                            affiliate_id, 
                            order_count_from, 
                            order_count_to, 
                            rate
                            FROM " . DB_PREFIX ."affiliate_commission_rate 
                            where affiliate_id IN (".$affiliate_ids.")
                            ORDER BY created DESC";
                  
    $commission_result = $this->db->query( $commission_sql ); 
     foreach($commission_result->rows as $key => $row)
     {
       $commission_rates[$row['affiliate_id']][$key]['order_count_from'] = $row['order_count_from'];
       $commission_rates[$row['affiliate_id']][$key]['order_count_to'] = $row['order_count_to'];
       $commission_rates[$row['affiliate_id']][$key]['rate'] = $row['rate'];
     }   
    return $commission_rates;  

 }
  /**  
    calculate commission rates
   * @param  array    $commission_rates [description]
   * @return array               [description]
   * @author Mahaveer Choudhary, Sept 2019
   */
 public function getCommissionRates(array $commission_rates, int $affiliate_id, int $current_order_no): int
 {
   $referral_commission = REFERRAL_COMMISSION; 
  
  if(isset($commission_rates[$affiliate_id]))
  {
    foreach($commission_rates[$affiliate_id] as $rates)
    {

      if(($rates['order_count_from'] <= $current_order_no && $rates['order_count_to'] >= $current_order_no) || ($rates['order_count_from'] <= $current_order_no && $rates['order_count_to'] == -1) )
      {
        $referral_commission = $rates['rate'];
        break;
      }

    }
  }  
   return $referral_commission;
 }

 /**  
    get Affiliate Last Order Details
   * @param  int    $affiliate_id [description]
   * @return array               [description]
   * @author Mahaveer Choudhary, Nov 2019
   */
 public function getAffiliateLastOrder(int $affiliate_id): array
 {
		$sql = "SELECT 
					oo.order_id,
					oo.order_no,
					oo.firstname,
					oo.lastname,
					oo.email,
					oo.telephone,
					oo.shipping_address_1,
					oo.shipping_address_2,
					oo.shipping_city,
					oo.shipping_postcode,
					oo.shipping_country,
					oo.shipping_zone,
					oo.date_added
				FROM ". DB_PREFIX ."order oo
				INNER JOIN `" . DB_PREFIX ."suborder` osub ON osub.order_id = oo.order_id 
				INNER JOIN ". DB_PREFIX ."customer cust ON cust.customer_id = oo.customer_id
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cust.referral_code
				INNER JOIN ". DB_PREFIX ."customer cc ON cc.customer_id = oa.customer_id
				where osub.order_status_id > '0' and oa.affiliate_id = '". (int)$affiliate_id ."'
				ORDER BY oo.order_id DESC 
				LIMIT 1";

        $query = $this->db->query($sql);
		return $query->row;
   
 }

  /**  
    get Affiliate Customers List
   * @param  int    $affiliate_id [description]
   * @return array               [description]
   * @author Mahaveer Choudhary, Nov 2019
   */
 public function getReferralCustomers(int $affiliate_id): array
 {
		$sql = "SELECT 
					cc.customer_id,
					cc.firstname,
					cc.lastname,
					cc.email,
					cc.telephone,
					cc.referral_code
				FROM ". DB_PREFIX ."customer cc
				INNER JOIN ". DB_PREFIX ."affiliate oa ON oa.code = cc.referral_code
				where oa.affiliate_id = '". (int)$affiliate_id ."' 
				ORDER BY NULL";

        $query = $this->db->query($sql);
		return $query->rows;
   
 }

   /**  
    remove Referral Customers
   * @param  int    $customer_id [description]
   * @return int               [description]
   * @author Mahaveer Choudhary, Nov 2019
   */
 public function removeReferral(int $customer_id): int
 {
		$sql = "UPDATE ". DB_PREFIX ."customer
		        set referral_code = ''  
				where customer_id = '". (int)$customer_id ."'";
        $query = $this->db->query($sql);
		return 1; 
 }

}
