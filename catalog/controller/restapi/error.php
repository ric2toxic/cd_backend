<?php
class ControllerRestapiError extends Controller{
    public function reportAllErrors() {

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
            $this->validateApiCall();



            if ( base64_encode(base64_decode($request['extra_info'], true)) === $request['extra_info']){
                $decode_extra_info = base64_decode($request['extra_info']);
            } else {
                $decode_extra_info = $request['extra_info'];
            }



            $html = "Errors"."</br></br>";
            $html .= "User id = ".$request['user_id']."</br>";
            $html .= "Method Url = ".$request['method_url']."</br>";
            $html .= "Error String = ".$request['error_string']."</br>";
            $html .= "Extra Info = ".$decode_extra_info;

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';

            $mail->SMTPDebug = 1;
            $mail->Debugoutput = 'html';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
            $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

            $mail->addAddress("rakesh.shekhawat@gmail.com",'Rakesh Shekhawat');
            $mail->addCC('ravindra.shekhawat.rajnota@gmail.com', 'Ravindra Singh');
            $mail->addCC("madhurbhaiya@gmail.com",'Madhur Bhaiya');
            $mail->addCC("neeraj.bagra@gmail.com",'Neeraj Bagra');

            $mail->Subject = 'Errors reported in App';

            $mail->msgHTML($html);

            if($mail->send()){
                $status = array(
                    'status' => "success"
                );
                echo json_encode($status);
            }else{
                $status = array(
                    'status' => "success"
                );
                echo json_encode($status);
            }
        }
    }

    public function validateApiCall(){

        if ($this->config->get('config_app_maintenance') == 1 ) {
            $rt['error_code'] = '8888';
            $rt['status'] = '0';
            $rt['status_text'] = 'failed';
            $rt['message'] = 'Hey!! Engineers @ work!!. We will be back shortly. C Ya';
            echo json_encode($rt); exit;
        }
        return true;

        $this->load->model('restapi/service');

        $headers = getallheaders();

        if(!$this->model_restapi_service->validateApiCall($headers)){
            $rt['error_code'] = '9999';
            $rt['status'] = '0';
            $rt['status_text'] = 'failed';
            $rt['message'] = 'Invalid API call';
            echo json_encode($rt); exit;
        }
    }
}
