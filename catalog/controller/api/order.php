<?php

class ControllerApiOrder extends Controller {

    public function add() {
        
    }

    public function edit() {
        
    }

    public function delete() {
        
    }

    public function history() { 

        // Order ID, Suborder ID and Order Status ID are a must field while adding History from Admin panel
        if (empty($this->request->get['order_id']) || empty($this->request->get['suborder_id']) || empty($this->request->get['order_status_id'])) {
            return false;
        }

        $this->load->language('api/order');
        $this->load->model('checkout/order');
        //$this->load->model('lead/lead');

        // $input array for addOrderHistory method
        $input = array();
        $input['order_id'] = (int) $this->request->get['order_id'];
        $input['suborder_id'] = $this->request->get['suborder_id'];
        $input['order_status_id'] = (int) $this->request->get['order_status_id'];
        $input['user'] = $this->request->post['user'];

        // Other keys whose values are if not supplied, we set to their default value as specified in the array below
        $keys = array(
            'notify_email' => false,
            'notify_sms' => false,
            'comment' => '',
            'notes' => '',
            'shippingco' => '',
            'tracking' => '',
            'give_cashback' => true
        );

        foreach ($keys as $key => $default_value) {
            if (!isset($this->request->post[$key])) {
                $input[$key] = $default_value;
            } else {
                $input[$key] = $this->request->post[$key];
            }
        }
        
        // Update Courier and tracking no details for the Suborder, if supplied.
        // @todo: This should have been in addOrderHistory in first place, but 
        // for now left here as addOrderHistory method itself will be dismantled.
        // Instead, it will be moved to OrderState classes (specifically Shipped states)
        // - Madhur
        $this->model_checkout_order->updateShippingInfo($input['order_id'], $input['suborder_id'], $input['shippingco'], $input['tracking']);

        //Updated courier dockets table for Shipped and Tracking status
        if(
            isset($this->request->get['order_status_id'])
            && $this->request->get['order_status_id'] == (int)ORDER_STATUS['Shipped with tracking'])    
        {
            $buyer_invoice  = new BuyerInvoice($this);
            $productDetails = $buyer_invoice->getOrderProductsDetailWithOrderInfo($this->request->get['order_id'], $this->request->get['suborder_id']);
            $totals         = $buyer_invoice->getTotals($this->request->get['order_id'], $this->request->get['suborder_id']);
            if( isset($productDetails['payment_code']) && strtolower(trim($productDetails['payment_code'])) != "cod"){
                $payment_mode = 'prepaid';
            } else if(isset($productDetails['payment_code']) && strtolower(trim($productDetails['payment_code'])) == "cod" && $totals['net_amount'] > 0) { 
                $payment_mode = 'cod';
            }else{
                $payment_mode = 'prepaid';
            }
            $params = array(
                'order_id'      => $this->request->get['order_id'],
                'suborder_id'   => $this->request->get['suborder_id'],
                'order_status_id'=> $this->request->get['order_status_id'],
                'shippingco'    => $this->request->post['shippingco'],
                'tracking'      => $this->request->post['tracking'],
                'payment_mode'  => $payment_mode,
                'cod_amount'    => isset($totals['net_amount']['value'])?$totals['net_amount']['value']:0,
                'parcel_amount' => isset($totals['total_amt']['value'])?$totals['total_amt']['value']:0,
            );
            $this->model_checkout_order->updateCourierDockets($params);
        }
        
        
        
        // Initializing Return array
        $json = array();
        // Adding to SubOrder History
        $history_arr = $this->model_checkout_order->addOrderHistory($input);
        if (!empty($history_arr['error'])) {
            $json['error'] = $history_arr['error'];
        } else {
            // Updating Status for Order No in CRM leads table
            $input['order_no'] = OrderInfo::getOrderNo($this->db, $input['order_id']);
            //	$this->model_lead_lead->updateLeadStatus($input);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
