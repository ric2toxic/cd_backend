<?php

 // Configuration
if (is_file(__DIR__.'/../../config.php')) {
    require_once(__DIR__.'/../../config.php');
}

require_once DIR_API . "/../system.php";
require_once DIR_API . "/../data_packet.php";
require_once( DIR_SYSTEM . 'library/currency.php' );
require_once( DIR_SYSTEM . 'library/operations/orders/order_product.php');

/**
 * Orders controller
 * class is use for get order info for CRM 
 * @author Nilesh Sharma
 */
class OrdersController extends SystemController {

    public function __construct($params) {
        parent::__construct($params);
        $this->registry->set('currency', new Currency($this->registry));
    }

    /**
     * Function getOrderProductsHtml
     * @param : order_id, suborder_id
     * @return: array
     */
    public function getOrderProductsHtml() {
        $this->request = $this->args;
        $this->load->model('restapi/order');
        $data = array();
        $data['order_id'] = $order_id = $this->request['order_id'];
        $data['suborder_id'] = $suborder_id = $this->request['suborder_id'];
        $buyer_invoice = new BuyerInvoice($this);
        $buyer_invoice->setOptions('file_type', 'b2b');
        $buyer_invoice->setOptions('show_image', TRUE);
        $buyer_invoice->setOptions('only_html', TRUE);
        $buyer_invoice->setOptions('is_api', TRUE);
        $order_info = $this->model_restapi_order->getSuborderDetails($order_id, $suborder_id);

        $buyer_invoice->setOrderInfo($order_info);

        $products = $buyer_invoice->getProductsArrayBySuborderId($data['order_id'], $data['suborder_id']);

        $totals = !empty($products) ? $buyer_invoice->getTotals($data['order_id'], $data['suborder_id']) : array();

        foreach ($products as $product) {
            if ($product['store_pickup']) {
                $data['text_immediate_pickup'] = "Immediate Delivery done from Store";
                break;
            }
        }

        // array of customer comment on perticular product
        $data['customer_comment_array'] = array();
        foreach ($products as $key => $product_data) {
            if (!empty($product_data['customer_comment'])) {
                $data['customer_comment_array'][$product_data['order_product_id']] = array(
                    'model' => $product_data['model'],
                    'set_description' => $product_data['comment']);
            }
        }

        $data['product_info'] = $buyer_invoice->getProductsHtml($products, $totals);

//        $buyer_invoice->setOptions('get_all_product', TRUE);
//        $products = $buyer_invoice->getProductsArrayBySuborderId($data['order_id'], $data['suborder_id']);
//        $totals = $buyer_invoice->getTotals($data['order_id'], $data['suborder_id']);
//        $data['original_product_info'] = htmlentities($buyer_invoice->getProductsHtml($products, $totals));

        return json_encode($data);
    }

    /**
     * Function getHistory
     * @param : order_id, suborder_id
     * @return: array
     */
    public function getHistory() {
        $this->request = $this->args;
        $data = array(); // Initializing the data array to be passed on to template files

        $data['histories'] = array();
        $this->load->model('restapi/order');
        $data['order_id'] = $order_id = $this->request['order_id'];
        $data['suborder_id'] = $suborder_id = $this->request['suborder_id'];
        $results = $this->model_restapi_order->getOrderHistories($order_id, $suborder_id);
        foreach ($results as $result) {
            $data['histories'][] = array(
                'notify_email' => $result['notify_email'] ? 'Yes' : 'No',
                'status' => $result['status'],
                'comment' => nl2br($result['comment']),
                'notes' => nl2br($result['notes']),
                'date_added' => date('d/m/Y H:i:s', strtotime($result['date_added'])),
                'notify_sms' => $result['notify_sms'] ? 'Yes' : 'No',
                'user' => $result['user']
            );
        }

        $data['history_html'] = $this->load->view(DIR_ADMIN_TEMPLATE . 'sale/order_history.tpl', $data, true);
        return json_encode($data);
    }

}

?>
