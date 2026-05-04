<?php
/**
*
*/
class ControllerSellerapiOrder extends Controller
{



    public function index(){


    }

    public function getLatestOrders(){
        $this->load->model('sellerapi/global');
        $this->model_sellerapi_global->headers();


        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
            $requestBody = $_POST;

            if($requestBody['seller_id'] > 0) {

                $orders = $this->MsLoader->MsOrderData->getLatestOrders($requestBody['seller_id']);

                $response = array("status" => "Success",
                    "orders" => $orders
                );
            }else{

                $response['status'] = 'Failure';
                $response['error'] = $this->error['warning'];
                $response['access_token'] = '';
                $response['orders'] = array();


            }
        }

        echo json_encode($response);
    }


}