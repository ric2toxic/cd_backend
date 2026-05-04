<?php

class ControllerSellerDashboard extends ControllerSellerAccount {
    public function index() {
        // paypal listing payment confirmation
        if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
            $this->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
        }

        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('account/order');

        $seller_id = $this->customer->getId();

        $seller = $this->MsLoader->MsSeller->getSeller($seller_id);

        $my_first_day = date('Y-m-d H:i:s', mktime(0, 0, 0, date("n"), 1));

        $this->data['seller'] = array_merge(
            $seller,
            array('balance' => 0),
            array('commission_rates' => 0),
            array('total_earnings' => 0),
            array('earnings_month' => 0),
            array('sales_month' => 0),
            array('seller_group' => ''),
            array('date_created' => date($this->language->get('date_format_short'), strtotime($seller['ms.date_created'])))
        //array('total_products' => $this->MsLoader->MsProduct->getTotalProducts(array(
        //'seller_id' => $seller_id,
        //'enabled' => ))
        );

       // if ($seller['ms.avatar'] && file_exists(DIR_IMAGE . $seller['ms.avatar'])) {
            $this->data['seller']['avatar'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_seller_avatar_dashboard_image_width'), $this->config->get('msconf_seller_avatar_dashboard_image_height'));
        //} else {
          //  $this->data['seller']['avatar'] = $this->MsLoader->MsFile->resizeImage('ms_no_image.jpg', $this->config->get('msconf_seller_avatar_dashboard_image_width'), $this->config->get('msconf_seller_avatar_dashboard_image_height'));
       // }

        $payments = 0;

        $orders = array();

        $this->load->model('localisation/order_status');
        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();
        $suborder = array();

        foreach ($orders as $order) {

            $status_name = $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $order['order_status_id']));

            if (isset($suborder['order_status_id']) && $suborder['order_status_id'] && $order['order_status_id'] != $suborder['order_status_id']) {
                $status_name .= ' (' . $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $suborder['order_status_id'])) . ')';
            }

            $products = $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order['order_id'], 'seller_id' => $seller_id));

            foreach($products as $key=>$p)
                $products[$key]['options']	=  $this->model_account_order->getOrderOptions($order['order_id'], $p['order_product_id']);

            $this->data['orders'][] = array(
                'order_id' => $order['order_id'],
                'order_no' => $order['order_no'],
                'customer' => "{$order['firstname']} {$order['lastname']} ({$order['email']})",
                'status' => $status_name,
                'products' => $products,
                'date_created' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
                'total' => $this->currency->format($this->MsLoader->MsOrderData->getOrderTotal($order['order_id'], array('seller_id' => $seller_id)), $this->config->get('config_currency'))
            );
        }

        $this->data['link_back'] = $this->url->link('account/account', '', 'SSL');

        $this->document->setTitle($this->language->get('ms_account_dashboard_heading'));

        $this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
            array(
                'text' => $this->language->get('text_account'),
                'href' => $this->url->link('account/account', '', 'SSL'),
            ),
            array(
                'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
                'href' => $this->url->link('seller_panel/account-order', '', 'SSL'),
            )
        ));

        list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-dashboard');
        $this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
    }


}

?>
