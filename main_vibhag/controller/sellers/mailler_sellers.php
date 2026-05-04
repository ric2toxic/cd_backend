<?php
class ControllerSellersMaillerSellers extends Controller{
    private $error = array();

    public function mailerSellers(){

        //$this->load->language('sellers/mailler_sellers');
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sellers/mailler_sellers', $data);

        $this->load->model('sellers/mailler_sellers');
        $url = '';

        $this->document->setTitle($data['heading_title']);

        $data['text_category_id'] = $this->language->get('text_category_id');

        $data['heading_title'] = $data['text_mailer_sellers'];

        $data['cancel'] = $this->url->link('sellers/mailler_sellers', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['text_mailer_sellers'],
            'href' => $this->url->link('sellers/mailler_sellers/mailerSellers', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if(isset($this->request->get['seller_status'])){
            $seller_status = $this->request->get['seller_status'];
        }else{
            $seller_status = '';
        }


        $filter_data = array(
            'seller_status' => $seller_status
        );

        $results = $this->model_sellers_mailler_sellers->getSellersList($filter_data);
        
        $data['sellers_list']   = array();
        $seller_zone            = array();
        foreach($results as $result){
            $data['sellers_list'][] = array(
                'seller_id' => $result['seller_id'],
                'name' => $result['name'],
                'nickname' => $result['nickname'],
                'seller_status' => $result['seller_status'],
                'company' => $result['company'],
                'zone_name' => $result['zone_name'],
                'zone_code' => $result['zone_code']
            );
            $seller_zone[$result['zone_name']]     = $result['zone_code'];
        }
        $data['seller_zone'] = $seller_zone;

        $data['seller_status'] = $seller_status;

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->session->data['failure'])) {
            $data['failure'] = $this->session->data['failure'];

            unset($this->session->data['failure']);
        } else {
            $data['failure'] = '';
        }
        //echo "<pre>";print_r($data);die;
        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');



        $this->response->setOutput($this->load->view('sellers/mailler_to_sellers.tpl', $data));
    }



    // mail send
    public function sendMailSeller(){
        $this->load->language('sellers/mailler_sellers');

        $seller_ids = $this->request->post['sellers_list'];
        $msg_sellers = $this->request->post['msg_sellers'];
        $subject_sellers = html_entity_decode($this->request->post['mail_subject']);
        $additional_cc_emails = array_map('trim',explode(',',$this->request->post['additional_cc_emails']));
        $additional_bcc_emails = array_map('trim',explode(',',$this->request->post['additional_bcc_emails']));
        $upload_file = $this->request->files['files'];

        $this->load->model('sellers/mailler_sellers');
        $seller_details = $this->model_sellers_mailler_sellers->getSellersDetails($seller_ids);
        $data = array();
        $data['message'] = $msg_sellers;
        $data['subject'] = $subject_sellers;

        $attachments = array();
        if ($seller_details) {

            if($upload_file['size']> 0){
                if(!is_dir(DIR_UPLOAD.'mail_to_sellers')){
                    mkdir(DIR_UPLOAD.'mail_to_sellers', 0777, true);
                }
                for($i=0; $i<count($upload_file['size']); $i++){
                    $file_temp = $upload_file['tmp_name'][$i];
                    $destination_file = DIR_UPLOAD .'mail_to_sellers/'.$upload_file['name'][$i];
                    $attachments[] = DIR_UPLOAD .'mail_to_sellers/'.$upload_file['name'][$i];
                    move_uploaded_file($file_temp,$destination_file);
                }

            }   
            $data['attachments'] = $attachments;

            $user_id = $this->session->data['user_id'];
            $user_details  = $this->user->getUserName($user_id);
            $data['user'] = $user_details['name'];
            
            $mail = new  PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
            $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);


            foreach ($seller_details as $seller_id => $seller_detail) {
                $mail->addBCC($seller_detail['email'], $seller_detail['company']);

                // Check for additional emails
                $add_emails = $this->model_sellers_mailler_sellers->getAdditionalEmails($seller_id);

                if (!empty($add_emails)) {
                    foreach ($add_emails as $email){
                        $mail->addBCC($email['email'], $seller_detail['company']);
                    }
                }
            }

            if(!empty($additional_bcc_emails)){
                foreach ($additional_bcc_emails as $email){
                    $mail->addBCC($email);
                }
            }

            if(!empty($additional_cc_emails)){
                foreach($additional_cc_emails as $add_emails){
                    $mail->addCC($add_emails);
                }
            }

            $mail->Subject = 'Wholesale Box : ' . $subject_sellers;

            $mail->Body = html_entity_decode($msg_sellers, ENT_QUOTES, 'UTF-8');

            if($upload_file['size'] > 0) {
                for ($i = 0; $i < count($upload_file['size']); $i++) {
                    if (file_exists(DIR_UPLOAD . 'mail_to_sellers/' . $upload_file['name'][$i])) {
                        $mail->addAttachment(DIR_UPLOAD . 'mail_to_sellers/' . $upload_file['name'][$i]);
                    }
                }
            }

            $mail->isHTML(true);
            $mail->send();

            $email = array();
            $email['to'] = $mail->getToAddresses();
            $email['cc'] = $mail->getCcAddresses();
            $email['bcc'] = $mail->getBccAddresses();

            $data['emails'] = $email;

            $this->model_sellers_mailler_sellers->insertIntoMailerToSellersLog($data);

            $this->session->data['success'] = $this->language->get('text_success');
        } else {
            $this->session->data['failure'] = $this->language->get('text_failure');
        }

        $this->response->redirect($this->url->link('sellers/mailler_sellers/mailerSellers', 'token=' . $this->session->data['token'], 'SSL'));
    }
}
