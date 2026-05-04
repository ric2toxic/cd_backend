<?php
class ControllerInformationCredit extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('information/credit');
        $this->load->model('module/credit_application');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $data['base'] = $server;
//echo "<pre>"; print_r($this->request->post); echo "</pre>";die;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {


            $this->model_module_credit_application->addCreditApplication($this->request);

           /* $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            //echo $this->config->get('config_email'); die;
            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
            $mail->setHtml(sprintf($this->language->get('email_message'), $this->request->post['name'], $this->request->post['firm'], $this->request->post['tenure_year'],$this->request->post['shop_since'], $this->request->post['telephone'],$this->request->post['email'], $this->request->post['city'], $this->request->post['location']), ENT_QUOTES, 'UTF-8');

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

            $mail->addAddress($this->config->get('config_email'), 'Customer');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesaelbox');
           // $mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
            $mail->Subject = html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8');
            $mail->msgHTML(sprintf($this->language->get('email_message'), $this->request->post['name'], $this->request->post['firm'], $this->request->post['tenure_year'],$this->request->post['shop_since'], $this->request->post['telephone'],$this->request->post['email'], $this->request->post['city'], $this->request->post['location']), ENT_QUOTES, 'UTF-8');

            $mail->send();


            $this->response->redirect($this->url->link('information/credit/success'));
        }

        $data['all_state'] = $this->model_module_credit_application->getState();
        //echo "<pre>";print_r($data['all_state']); echo "</pre>";
        $data['months'] = $this->language->get('array_months');
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/credit')
        );

        //$data['heading_title'] = $this->language->get('heading_title');
        $data['text_credit_facility'] = $this->language->get('text_credit_facility');


        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_firm'] = $this->language->get('entry_firm');
        $data['entry_tenure'] = $this->language->get('entry_tenure');
        $data['entry_telephone'] = $this->language->get('entry_mobile_no');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_location'] = $this->language->get('entry_location');
        $data['text_static_message'] = $this->language->get('text_static_message');
        $data['text_credit'] = $this->language->get('text_credit');
        $data['text_bottom_message'] = $this->language->get('text_bottom_message');
        $data['text_year'] = $this->language->get('text_year');
        $data['text_month'] = $this->language->get('text_month');
        $data['text_select'] = $this->language->get('text_select');

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['firm'])) {
            $data['error_firm'] = $this->error['firm'];
        } else {
            $data['error_firm'] = '';
        }

        if (isset($this->error['tenure'])) {
            $data['error_tenure'] = $this->error['tenure'];
        } else {
            $data['error_tenure'] = '';
        }

        if (isset($this->error['shop_since'])) {
            $data['error_shop_since'] = $this->error['shop_since'];
        } else {
            $data['error_shop_since'] = '';
        }

        if (isset($this->error['telephone'])) {
            $data['error_telephone'] = $this->error['telephone'];
        } else {
            $data['error_telephone'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['city'])) {
            $data['error_city'] = $this->error['city'];
        } else {
            $data['error_city'] = '';
        }

        if (isset($this->error['location'])) {
            $data['error_location'] = $this->error['location'];
        } else {
            $data['error_location'] = '';
        }



        $data['button_submit'] = $this->language->get('button_submit');

        $data['action'] = $this->url->link('information/credit');



        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['firm'])) {
            $data['firm'] = $this->request->post['firm'];
        } else {
            $data['firm'] = '';
        }

        if (isset($this->request->post['tenure_year'])) {
            $data['tenure_year'] = $this->request->post['tenure_year'];
        } else {
            $data['tenure_year'] = '';
        }
        if (isset($this->request->post['shop_since'])) {
            $data['shop_since'] = $this->request->post['shop_since'];
        } else {
            $data['shop_since'] = '';
        }
        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['location'])) {
            $data['location'] = $this->request->post['location'];
        } else {
            $data['location'] = '';
        }



        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/credit.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/credit.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/information/credit.tpl', $data));
        }
    }

    public function success() {
        $this->load->language('information/credit');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/credit')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_message'] = $this->language->get('text_success');

        $data['button_continue'] = $this->language->get('button_continue');

        $data['continue'] = $this->url->link('common/home');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
        }
    }

    protected function validate() {
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        if ((utf8_strlen($this->request->post['firm']) < 3) || (utf8_strlen($this->request->post['firm']) > 32)) {
            $this->error['firm'] = $this->language->get('error_firm');
        }
        if ((utf8_strlen($this->request->post['tenure_year']) < 2) || (utf8_strlen($this->request->post['tenure_year']) > 4)) {
            $this->error['tenure'] = $this->language->get('error_tenure');
        }
        if ($this->request->post['shop_since']=='') {
            $this->error['shop_since'] = $this->language->get('error_shop_since');
        }
        if ((int)(is_numeric($this->request->post['telephone'])) == 0 || utf8_strlen($this->request->post['telephone']) !=  10) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }
        if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }
        if ((utf8_strlen($this->request->post['city']) < 3) || (utf8_strlen($this->request->post['city']) > 32)) {
            $this->error['city'] = $this->language->get('error_city');
        }
        if ((utf8_strlen($this->request->post['location']) < 3) || (utf8_strlen($this->request->post['location']) > 32)) {
            $this->error['location'] = $this->language->get('error_location');
        }
        return !$this->error;
    }
}