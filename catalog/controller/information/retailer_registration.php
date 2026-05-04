<?php
/**
 * Created by PhpStorm.
 * User: manoj
 * Date: 14/3/17
 * Time: 4:05 PM
 */

class Controllerinformationretailerregistration extends Controller {

    private $error = array();

    public function index() {

        $data = array();

        //default fields value
        $data['name'] = "";
        $data['reg_email'] = "";
        $data['reg_telephone'] = "";
        $data['alert_message'] = "";

        //on submit the subscribe form
        if(isset($this->request->post['form_button']) && $this->request->post['form_button']== 'subscribe_button') {

            $data['name'] = $this->request->post['name'];
            $data['reg_email'] = $this->request->post['reg_email'];
            $data['reg_telephone'] = $this->request->post['reg_telephone'];

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->signupCampaignValidation()) {

                // Add Subscriber
                // echo "INSERT INTO " . DB_PREFIX . "campaign_subscription SET store_id = '" . (int)$this->config->get('config_store_id') . "', subscriber_name = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['reg_email']) . "', telephone = '" . $this->db->escape($data['reg_telephone']) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()"; die;
                $this->db->query("INSERT INTO " . DB_PREFIX . "campaign_subscription SET store_id = '" . (int)$this->config->get('config_store_id') . "', `name` = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['reg_email']) . "', telephone = '" . $this->db->escape($data['reg_telephone']) . "', ip = '" . $this->db->escape($this->request->getIpAddress) . "', date_added = NOW()");
                $subscriber_id = $this->db->getLastId();

                if ($subscriber_id) {
                    
                    $data['name'] = "";
                    $data['reg_email'] = "";
                    $data['reg_telephone'] = "";

                    $data['alert_message'] = array("color" => "green", "btn-text" => "<span class='med'>Thank You</span>", "title" => "<span class='lrg text-success'>Congratulations!</span>", "message" => "<div class='alert alert-success'><strong>You have successfully subscribed with us.</strong></div>");
                } else {
                    $data['alert_message'] = array("color" => "red", "btn-text" => "<span class='med'>Try Again</span>", "title" => "<span class='lrg'>Encountered an error!</span>", "message" => "<div class='alert alert-success'>Subscription has not completed, please try again.</div>");
                }
            }

            if (!empty($this->error)) {
                $li = "";
                foreach ($this->error as $error) {
                    $li .= "<li>" . $error . "</li>";
                }
                $message_content = "<div class='alert alert-danger med'><ul>" . $li . "</ul></div>";

                $data['alert_message'] = array("color" => "red", "btn-text" => "<span class='med'>Try Again</span>", "title" => "<span class='lrg text-danger'>Encountered an error!</span>", "message" => $message_content);
            }
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/retailer_registration.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/retailer_registration.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/information/retailer_registration.tpl', $data));
        }

    }

    /**
     * [signupCampaignValidation signup validation]
     * @return array of error(s)
     */
    public function signupCampaignValidation()
    {
        $this->load->language('account/login');

        if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
            $this->error[] = $this->language->get('error_name');
        }

        // this is validation for same email in registration page
        if(!empty($this->request->post['reg_email'] )) {
            if ((utf8_strlen($this->request->post['reg_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['reg_email'])) {
                $this->error[] = "Please enter valid email address";
            }
            if ($this->checkSubscriberEmailExsist($this->request->post['reg_email'])) {
                $this->error[] = "Email address is already registered";
            }
        }

        if ((int)(is_numeric($this->request->post['reg_telephone'])) == 0 || utf8_strlen($this->request->post['reg_telephone']) !=  10) {
            $this->error[] = "Please enter valid mobile number";
        }

        if (!empty($this->request->post['reg_telephone']) && $this->checkSubscriberTelExsist($this->request->post['reg_telephone'])) {
            $this->error[] = "Mobile number is already registered";
        }

        return !$this->error;
    }


    private function checkSubscriberEmailExsist($email){
        $q = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "campaign_subscription WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND store_id = ".$this->config->get('config_store_id')." ";
        $query = $this->db->query($q);
        return $query->row['total'];
    }

    private function checkSubscriberTelExsist($telephone){
        $q = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "campaign_subscription WHERE LOWER(telephone) = '" . $this->db->escape(utf8_strtolower($telephone)) . "' AND store_id = ".$this->config->get('config_store_id')." ";
        $query = $this->db->query($q);
        return $query->row['total'];
    }

}
