<?php
class ControllerCareersCareers extends Controller {
    private $error = array();
    public function index() {
        $this->load->language('careers/careers');
        $this->load->model('careers/careers');
        $this->load->model('tool/image');
        //$this->document->addStyle('catalog/view/theme/default/stylesheet/uploadify.css');
        //$this->document->addScript('catalog/view/javascript/jquery/jquery.uploadify.min.js');
        $data['route'] = '';
        if(isset($this->request->get['route']) && $this->request->get['route'] != '') {
            $data['route'] = $this->request->get['route'];
        }
        $data['banner_image']    =  $this->model_tool_image->getOriginalImage('career/careerbanner.jpg');
        $data['careerbanner_m']    =  $this->model_tool_image->getOriginalImage('career/careerbanner_m.jpg');
        $data['one']              =  $this->model_tool_image->getOriginalImage('career/01.jpg', $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
        $data['two']              =  $this->model_tool_image->getOriginalImage('career/02.jpg', $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
        $data['three']              =  $this->model_tool_image->getOriginalImage('career/03.jpg', $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
        $data['four']              =  $this->model_tool_image->getOriginalImage('career/04.jpg', $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
        $data['five']              =  $this->model_tool_image->getOriginalImage('career/05.jpg', $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
        $data['six']              =  $this->model_tool_image->getOriginalImage('career/06.jpg', $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );


        $data['is_home'] = 0;
        if (!isset($this->request->get['route']) ) {
            $data['is_home'] = 1;
        }


        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }


        $data['request_uri'] = $_SERVER['REQUEST_URI'];
        if ($this->request->server['HTTPS']) {
            $data['in_store'] = 'https://'.INDIA_STORE_HOST;
            $data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
        } else {
            $data['in_store'] = 'http://'.INDIA_STORE_HOST;
            $data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
        }

        $header_language = array();
        $footer_language = array();
        $login_language = array();
       
        $this->load->autoLoadLanguage('common/header', $header_language);
        $this->load->autoLoadLanguage('common/footer', $footer_language);
        $this->load->autoLoadLanguage('account/login', $login_language);
        $data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
        $data['direction'] = $header_language['direction'];
        
         $store_id = (int)($this->config->get('config_store_id'));
         $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

        $data['text_career'] = $this->language->get('text_career');

        $this->document->setTitle($this->language->get('text_career'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['title'] = $this->document->getTitle();
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        
        $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


        $data['base'] = $server;

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_career'),
            'href' => $this->url->link('careers/careers')
        );


       

        $data['all_data'] = $this->model_careers_careers->getAllData();
        //echo "<pre>";print_r($this->request->server['REQUEST_METHOD']); echo "</pre>";die;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
             //echo "<pre>";print_r($this->request->server['REQUEST_METHOD']); echo "</pre>"; //die;
            //echo "<pre>";print_r($this->request); echo "</pre>";die;

            $today = Date('d_M_Y');
            if (!file_exists(DIR_DOWNLOAD.'job_applications/'.$today)) {
                mkdir(DIR_DOWNLOAD.'job_applications/'.$today, 0777, true);
            }

            //$allowed_extension = array('doc','pdf', 'rtf','txt' );
            if($this->request->files['resume']['error'] == 0){
                $file_name = $this->request->files['resume']['name'];
                $file_temp_name = $this->request->files['resume']['tmp_name'];
                $file_path = DIR_DOWNLOAD.'job_applications/'.$today.'/'.$file_name;
                move_uploaded_file($file_temp_name,$file_path);
            }

            $this->model_careers_careers->jobAppliedData($this->request);

            if($this->request->post['current_ctc']){
                $current_ctc = $this->request->post['current_ctc'];
            }


            if($this->request->post['expected_ctc']){
                $expected_ctc = $this->request->post['expected_ctc'];
            }

            $member_email = explode(',',$this->request->post['member_email']);
            $post_name = $this->model_careers_careers->getPostName($this->request->post['job_id']);
            //$files_link = DIR_DOWNLOAD . 'job_applications/'.$today.'/'.$this->request->post['uploading_resume'];
            $files_link = DIR_DOWNLOAD . 'job_applications/'.$today.'/'.$this->request->files['resume']['name'];
            $mail = new PHPMailer();
            $mail->isSMTP();
            //$mail->SMTPDebug = 2;
            //$mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'),  $this->language->get('email_title'));
            $mail->addReplyTo($this->request->post['email'],  $this->request->post['your_name']);

            // $mail->addAddress($this->config->get('config_email'), 'WholesaleBox');
            $mail->addAddress($this->config->get('config_email'), $this->language->get('email_title'));
            foreach ($member_email as $mail_item) {
                $mail->addCC($mail_item);
            }
            //$mail->addCC("t.mode@mail.com");
            $msg = sprintf($this->language->get('email_message_for_admin'), $this->request->post['your_name'], $this->request->post['mobile'], $this->request->post['email'], $post_name, $current_ctc, $expected_ctc, $this->request->post['cover_letter']);

          

            $mail->Subject = $this->language->get('email_subject_for_admin');
            $mail->msgHTML($msg, ENT_QUOTES, 'UTF-8');
            $mail->addAttachment($files_link);
            //send to admin;
            //echo "<pre>"; print_r($mail); echo "</pre>";
            if($mail->send()){
                echo "success";
            }else{
                echo "error";
            }
            //$mail->send();

            $mail = new PHPMailer();
            $mail->isSMTP();
            //$mail->SMTPDebug = 2;
            //$mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'),  $this->language->get('email_title'));
            $mail->addReplyTo($this->language->get('email_to_hr'),  $this->language->get('email_title'));

            // $mail->addAddress($this->config->get('config_email'), 'WholesaleBox');
            $mail->addAddress($this->request->post['email'],  $this->language->get('email_title'));
            $mail->Subject = $this->language->get('email_subject_seekars');
            $mail->msgHTML(sprintf($this->language->get('email_message_seekars'), $this->request->post['your_name']), ENT_QUOTES, 'UTF-8');
            //send to job seekars;
            //echo "<pre>"; print_r($mail); echo "</pre>";die;
            if($mail->send()){
                echo "success";
            }else{
                echo "error";
            }
            //$mail->send();


            $this->response->redirect($this->url->link('careers/careers&static=careers', '', 'SSL'));
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['mobile'])) {
            $data['error_mobile'] = $this->error['mobile'];
        } else {
            $data['error_mobile'] = '';
        }


        if (isset($this->error['current_ctc'])) {
            $data['error_current_ctc'] = $this->error['current_ctc'];
        } else {
            $data['error_current_ctc'] = '';
        }

        
        if (isset($this->error['expected_ctc'])) {
            $data['error_expected_ctc'] = $this->error['expected_ctc'];
        } else {
            $data['error_expected_ctc'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['cover_letter'])) {
            $data['error_cover_letter'] = $this->error['cover_letter'];
        } else {
            $data['error_cover_letter'] = '';
        }

        if (isset($this->error['resume'])) {
            $data['error_resume'] = $this->error['resume'];
        } else {
            $data['error_resume'] = '';
        }
        //echo "<pre>"; print_r($_REQUEST); echo"</pre>";

        $data['text_name']          = $this->language->get('text_name');
        $data['text_email']         = $this->language->get('text_email');
        $data['text_mobile']        = $this->language->get('text_mobile');
        $data['text_current_ctc']   = $this->language->get('text_current_ctc');
        $data['text_expected_ctc']  = $this->language->get('text_expected_ctc');
        $data['text_cover_letter']  = $this->language->get('text_cover_letter');
        $data['text_resume']        = $this->language->get('text_resume');
        $data['title_popup']        = $this->language->get('title_popup');
        $data['text_email_label']   = $this->language->get('text_email_label');
        $data['text_empty']         = $this->language->get('text_empty');
        $data['text_top_heading']   = $this->language->get('text_top_heading');


        $data['form_action']        = $this->url->link('careers/careers&static=careers');

        if(CONFIG_IS_MOBILE == 1)
        {
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        }


           $data['images']['wholesalebox_about'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about.jpg');
           $data['images']['wholesalebox_about_2'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_2.jpg');
           $data['images']['wholesalebox_about_graph'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_graph.jpg');
           $data['images']['wholesalebox_about_how_we_work'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_how_we_work.jpg');
           $data['images']['wholesalebox_rohit_dangayach'] =  $this->model_tool_image->getOriginalImage('wholesalebox_rohit_dangayach.jpg');
           $data['images']['wholesalebox_chandan_agarwal'] =  $this->model_tool_image->getOriginalImage('wholesalebox_chandan_agarwal.jpg');
           $data['images']['wholesalebox_rakesh_shekhawat'] =  $this->model_tool_image->getOriginalImage('wholesalebox_rakesh_shekhawat.jpg');
           $data['images']['wholesalebox_madhur_maheshwari'] =  $this->model_tool_image->getOriginalImage('wholesalebox_madhur_maheshwari.jpg');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/careers/careers.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/careers/careers.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/careers/careers.tpl', $data));
        }

    }



    public function validate() {
        if ((utf8_strlen($this->request->post['your_name']) < 3) || (utf8_strlen($this->request->post['your_name']) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        if ((int)(is_numeric($this->request->post['mobile'])) == 0 || utf8_strlen($this->request->post['mobile']) !=  10) {
            $this->error['mobile'] = $this->language->get('error_mobile');
        }

        if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if ((utf8_strlen($this->request->post['cover_letter']) =='') || (utf8_strlen($this->request->post['cover_letter']) > 3000)) {
            $this->error['cover_letter'] = $this->language->get('error_cover_letter');
        }
        if ((utf8_strlen($this->request->files['resume']['name']) =='')) {
            $this->error['resume'] = $this->language->get('error_resume');
        }
        return !$this->error;
    }


    public function uploadImageBeforeSubmit(){
        //echo"<pre>"; print_r($this->request->files[0]); echo "</pre>"; exit;
        echo"<pre>"; print_r($_FILES); echo "</pre>"; exit;
        $today = Date('d_M_Y');
        if (!file_exists('download/job_applications/'.$today)) {
            mkdir('download/job_applications/'.$today, 0777, true);
        }
        //$allowed_extension = array('doc','pdf', 'rtf','txt' );
        if($this->request->files['0']['error'] == 0){
            $file_name = $this->request->files['0']['name'];
            $file_temp_name = $this->request->files['0']['tmp_name'];
            $file_path = DIR_DOWNLOAD.'job_applications/'.$today.'/'.$file_name;

            move_uploaded_file($file_temp_name,$file_path);
        }
        echo "success";
        exit;
    }

}