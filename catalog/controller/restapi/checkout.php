<?php
class ControllerRestapiCheckout extends Controller{
    /**
     * [function to get all checkout details]
     * returns payment address, shipping address and cart data
     * @author Kuldeep
     */
    public function getCheckOutDetails () {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
            $this->load->model('restapi/service');
            $data = array();
            $validate = $this->model_restapi_service->checkAccessTokenAndUserId($request);

            if (!$validate) {

                $this->load->model('restapi/cartservice');
                $data['addresses'] = array();

                $this->load->model('account/address');

                $data['default_address_id'] = $this->model_account_address->getAddressIdFromCustomerId($request['user_id']);

                $data['addresses'] = $this->model_account_address->getAddresses($request['user_id']);

                $data['cart'] = $this->model_restapi_cartservice->cart_data($request);
                $data['status'] = '1';
                $data['status_text'] = 'Success';
                $data['message'] = 'Checkout Details.';

            } else {
                $data = $validate;
            }

            echo json_encode($data); die;
        }
    }

     /** *******
     * Function : get_shipping_methods
     * Request Parameters : user_id,access_token,country_code
     * Type : Post
     * Output : {"status":"1","status_text":"Success","data" : "shipping methods data"}
     ******* */
    public function getShippingMethods(){
        $this->load->model('restapi/service');
        $this->load->model('restapi/cartservice');
        $data = array();
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $this->validateApiCall();
            $validate = $this->model_restapi_service->checkAccessTokenAndUserId($request);
            if(!$validate){
                $access_token = $request['access_token'];
                $user_id = $request['user_id'];
                if(isset($request['country_code'])){
                    $country_code = $request['country_code'];
                }else{
                    $country_code = "IN";
                }
                $user_data['user_id'] = $user_id;
                $user_data['country_code'] = $country_code;
                $data = $this->model_restapi_cartservice->getShippingMethods((string)$access_token, $user_data);
                $data['status'] = '1';
                $data['status_text'] = 'Success';
                $data['message'] = 'Checkout Details.';
            } else {

                $data = $validate;

            }
        }
        echo json_encode($data);
    }

    /** *******
     * Function : get_payment_methods
     * Request Parameters : user_id,access_token,country_code
     * Type : Post
     * Output : {"status":"1","status_text":"Success","data" : "payment methods data"}
     ******* */
    public function getPaymentMethod() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->model('restapi/service');
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
            $validate = $this->model_restapi_service->checkAccessTokenAndUserId($request);
            $data = array();
            if (!$validate) {

                if (isset($request['payment_address_id'])) {

                    $this->load->language('checkout/checkout');

                    $this->load->model('account/address');

                    $payment_address = $this->model_account_address->getAddress($request['payment_address_id'], $request['user_id']);

                    // Payment Methods
                    $data = array();

                    // Payment Methods
                    $data['payment_methods'] = array();

                    $this->load->model('extension/extension');
                    $results = $this->model_extension_extension->getExtensions('payment');

                    $total = 0;
                    foreach ($results as $result) {
                        if ($this->config->get($result['code'] . '_status')) {
                            $this->load->model('payment/' . $result['code']);

                            $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total);

                            if ($method) {
                                $data['payment_methods'][] = $method;
                            }
                        }
                    }

                } else {

                    $data['status'] = '0';
                    $data['status_text'] = 'Failed';
                    $data['message'] = 'http request does not have payment address id.';

                }
            } else {
                $data = $validate;
            }

            echo json_encode($data); exit;
        }
    }

    /** *******
     * Function : getCityByPinCode
     * Request Parameters : user_id,access_token,pin code
     * Type : Post
     * Output : {"status":"1","status_text":"Success","data" : "city", "state", "country"}
     ******* */
    public function getAddressByPinCode () {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->load->model('account/address');

            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);

            if (isset($request['pincode'])) {

                $this->validateApiCall();
                $data = $this->model_account_address->getAddressBypin($request['pincode']);
                $data['status'] = '1';
                $data['status_text'] = 'success';
                $data['message'] = 'city, state and country';

            } else {

                $data['error_code'] = '1001';
                $data['status'] = '0';
                $data['status_text'] = 'Failed';
                $data['message'] = 'http request does not have pincode.';

            }

            echo json_encode($data); exit;

        }
    }

    /** *******
     * Function : addPaymentAddress
     * Request Parameters : user_id,access_token,address
     * Type : Post
     * Output : {"status":"1","status_text":"Success"}
     ******* */
    public function addAddress () {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->model('restapi/service');
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
            $this->load->model('account/address');
            $validate = $this->model_restapi_service->checkAccessTokenAndUserId($request);
            if (!$validate) {

                $data['address_id'] = $this->model_account_address->addAddress($request, $request['user_id']);

                if (!empty($data['address_id'])) {
                    $data['status'] = '1';
                    $data['status_text'] = 'Success';
                    $data['message'] = 'Address successfully added.';

                } else {
                    $data['status'] = '0';
                    $data['status_text'] = 'Failed';
                    $data['message'] = 'address is not successfully added.';

                }

            } else {

                $data = $validate;

            }

            echo json_encode($data); exit;

        }
    }

    /** *******
     * Function : editPaymentAddress
     * Request Parameters : user_id,access_token,address
     * Type : Post
     * Output : {"status":"1","status_text":"Success"}
     ******* */
    public function editAddress () {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
            $this->load->model('account/address');
            $this->load->model('account/address');
            $validate = $this->model_restapi_service->checkAccessTokenAndUserId($request);

            if (!$validate) {

                $this->model_account_address->editAddress($request['address_id'], $request, $request['user_id']);

                $data['status'] = '1';
                $data['status_text'] = 'Success';
                $data['message'] = 'address successfully edited.';

            } else {

                $data = $validate;

            }

            echo json_encode($data); exit;

        }
    }

    /** *******
     ** validate api
     ******* */
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
