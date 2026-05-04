<?php

class ControllerFranchiseFranchise extends Controller {

    // For franchise products
    public function products() {
        # Just for menu permission to allow the permission to different users
    }

    // For franchise orders
    public function orders() {
        # Just for menu permission to allow the permission to different users
    }

    /**
     * Function changePaymentMethodOfFranchiseOrder use to change payment method for franchise orders to any other payment method - use by ajax
     * @param (int) $order_id - order id
     * @author Nilesh, 2018
     */
    public function changePaymentMethodOfFranchiseOrder() {

        $order_id = $this->request->post['order_id'];
        if (isset($this->request->post['payment_code'])) {
            $payment_code = $this->request->post['payment_code'];
        } else {
            $payment_code = '';
        }

        $data = array(
            'payment_code' => array(
                'new' => $payment_code,
                'old' => 'franchise'
            ),
            'user_id' => $this->user->getId(),
            'name' => $this->user->getUserName($this->user->getId())['name'],
            'user_name' => $this->user->getUserName($this->user->getId())['username']
        );

        OrderEdit::editOrder($this, $order_id, $data);
    }

}

?>