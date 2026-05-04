<?php

class ControllerSaleEditOrder extends Controller {

    public function index() {

        $data = array();
        $this->load->autoLoadLanguage('sale/order', $data);

        // Model
        $this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }
        if (isset($this->request->get['suborder_id'])) {
            $suborder_id = $this->request->get['suborder_id'];
        } else {
            $suborder_id = 0;
        }
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Edit Order',
            'href' => 'javascript:void(0)'
        );

        $selector = array('order' => array(),
            'suborder' => array(),
            'order_option' => array(),
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        $data['order_options'] = array();
        if (!empty($order_info['suborder'][$suborder_id]['order_option'])) {
            $data['order_options'] = array_combine(array_column($order_info['suborder'][$suborder_id]['order_option'], 'order_product_id'), $order_info['suborder'][$suborder_id]['order_option']);
        }
        $data['suborder_info_link'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $order_id . '&suborder_id=' . $suborder_id . '&filter_customer_id=' . $order_info['order']['customer_id'], 'SSL');

        $data['invoice_no'] = $order_info['suborder'][$suborder_id]['invoice_no'];
        $data['credit_check'] = 0;

        if (!empty($order_info)) {
            foreach ($order_info['suborder'] as $key => $value) {
                if ($value['invoice_no'] > 0) {
                    $data['credit_check'] = 1;
                }
            }
        }

        $buyer_invoice = new BuyerInvoice($this);
        $buyer_invoice->setOrderInfo($order_info);
        $buyer_invoice->setOptions('product_link', true);
        $buyer_invoice->setOptions('show_image', true);
        $buyer_invoice->setOptions('get_all_product', true);
        $order_products = $buyer_invoice->getProductsArrayBySuborderId($order_id, $suborder_id);
        $order_combo_products = $this->_filterOrderProductToCombo($order_products);
        $data['is_sets_edit'] = false;
        foreach ($order_products as $product) {
            if ((empty($product['seller_invoice_id']) || $product['seller_invoice_id'] == NULL) && $product['quantity'] > 1) {
                $data['is_sets_edit'] = true;
            }
        }
        $data['is_buyer_invoice_generated'] = 0;
        if (!empty($data['invoice_no']) && !empty($data['buyer_invoice_id'])) {
            $data['is_buyer_invoice_generated'] = 1;
        }

        $data['break_suborder_products'] = OrderEdit::getOrderProductDetailsForAnotherSuborder($this->db, $order_products, $order_id, $suborder_id);
        $data['order_combo_products'] = $order_combo_products;
        $this->load->model('catalog/product');
        $data['edit_types'] = $this->db->getEnumValues(DB_PREFIX . 'order_product', 'edit_type');
        unset($data['edit_types'][array_search('YES', $data['edit_types'])]);
        //$totals = $buyer_invoice->getTotals($order_id , $suborder_id);
        //$data['products_html'] = $buyer_invoice->getProductsHtml( $order_products , $totals );
        $data['customer_id'] = $order_info['order']['customer_id'];
        $customer_credit_status = !empty(OrderEdit::getCustomerCreditStatus($this->db, $data['customer_id'])) ? 1 : 0;
        
        $customer = new Customer($this->registry);
        $customer_wsb_credit_payment_data = $customer->getWsbCreditPaymentData($data['customer_id']);
        $customer_wsb_credit_payment_status = '';
        if (!empty($customer_wsb_credit_payment_data) ) {
          $customer_wsb_credit_payment_status = $customer_wsb_credit_payment_data['status'] ?? '';
        }

        // Devendra 27th August 2018, From now can not change payment method to credit 

        $payment_code = $order_info['order']['payment_code'] ?? '';
        $data['payment_methods'] = array(
            'cod'           => 'Cash on Delivery',
            'bank_transfer' => 'Prepaid',
            'udaan_credit'  => 'Udaan Credit' // Added by MSA on march 2019
        );
        if($payment_code != 'wsb_credit' && $customer_wsb_credit_payment_status == 'ENABLED'){
            $data['payment_methods']['wsb_credit'] = 'WSB Credit';
        }
        
        // Note that Credit Payment Codes will not be shown

        $data['shipping_methods'] = OrderEdit::getShippingMethodRequestArrayForSuborder($this->db, $order_id, $suborder_id);

        $data['deleted_order_history'] = OrderEdit::getDeletedOrderHistoryItemOfSuborderHtml($this, $order_id, $suborder_id);

        if (!empty($order_info)) {
            $data['order'] = $order_info['order'];
            if (trim(strtolower($data['order']['payment_code'])) == 'credit') {
                $data['order']['payment_code'] = 'credit';
            } else if (trim(strtolower($data['order']['payment_code'])) == 'wsb_credit') {
                $data['order']['payment_code'] = 'wsb_credit';
            } else if (trim(strtolower($data['order']['payment_code'])) == 'mswipe_credit') {
                $data['order']['payment_code'] = 'mswipe_credit';
            } else if (trim(strtolower($data['order']['payment_code'])) != 'cod') {
                $data['order']['payment_code'] = 'bank_transfer';
            }

            $data['suborder'] = $order_info['suborder'][$suborder_id];
            $data['products'] = $order_products;
            $data['order_no'] = $data['order']['order_no'];
            $data['order_id'] = $order_id;
            $data['suborder_id'] = $suborder_id;

            $data['shipping_code'] = $order_info['suborder'][$suborder_id]['shipping_code'];
            $data['shipping_method'] = $order_info['suborder'][$suborder_id]['shipping_method'];
            $data['shipping_charges'] = $order_info['suborder'][$suborder_id]['shipping_charge'];
            $data['duplicate_popup'] = $this->load->view('sale/marked_duplicate_popup.tpl');

            $sale_con = new ControllerSaleOrder($this->registry);
            $order_infos = array(
                'pincode' => $order_info['order']['shipping_postcode'],
                'payment_code' => $order_info['order']['payment_code'],
                'zone_id' => $order_info['order']['shipping_zone_id']);
            $data['courier_adv'] = $sale_con->orderInfoLabelCourierAdvisory($data, $order_infos);
            $data['total_weight'] = OrderEdit::getProductWeightForShipping($this->db, $order_id, $suborder_id);
        }

        $data['suborder_id_arrs'] = OrderEdit::getSubordersFromSameCity($this->db, $order_id, $suborder_id);
        $data['paycharge_data'] = OrderEdit::getAllProductInfoAndOrderInfo($this->db, $order_id);
        $data['ess_charges'] = OrderEdit::getEssShippingCharges($this->db, $order_id, $suborder_id);
        $data['suborder_edit_history'] = OrderEdit::getSuborderAllEditHistory($this->db, $order_id, $suborder_id, 'APPLY_ESS_SHIPPING');

        //Admin IDs

        if (in_array($this->user->getId(), explode(',', ADMIN_IDS))) {
            $data['is_admin'] = 1;
        } else {
            $data['is_admin'] = 0;
        }
        if (in_array($this->user->getId(), explode(',', OPERATIONS_ADMIN_IDS))) {
            $data['is_operation_admin'] = 1;
        } else {
            $data['is_operation_admin'] = 0;
        }

        //Stock Transfer
        $data['is_stock_transfer'] = 0;

        if (($order_info['order']['stock_transfer'] == 0 && (OrderEdit::checkOrderGSTIsWSBGST($this->db, $order_info['order']['gst_number'])))) {
            $data['is_stock_transfer'] = 1;
        }

        //Delete code for order history for admin
        $data['order_history'] = OrderEdit::getOrderHistoryItem($this->db, $order_id, $suborder_id);

        $data['save'] = $this->url->link('sale/edit_order/save', '&token=' . $this->session->data['token'], 'SSL');
        $data['update_selling_price'] = $this->url->link('sale/edit_order/editOrderProductSellingPrice', '&token=' . $this->session->data['token'], 'SSL');
        $data['update_transfer_price'] = $this->url->link('sale/edit_order/editOrderProductTransferPrice', '&token=' . $this->session->data['token'], 'SSL');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['session_succ_msg'] = '';
        if (!empty($this->session->data['success_msg'])) {
            $data['session_succ_msg'] = $this->session->data['success_msg'];
            unset($this->session->data['success_msg']);
        } else if (!empty($this->session->data['error_msg'])) {
            $data['session_err_msg'] = $this->session->data['error_msg'];
            unset($this->session->data['error_msg']);
        }


        //// order edit payment history /////
        $data['order']['order_id'] = $order_id;
        $data['order']['payment_history'] = $this->model_sale_order->getOrderPaymentHistory($order_id);

        // get all enum of payment_gateway from order_payment table
        $data['order']['enum_values'] = $this->db->getEnumValues(DB_PREFIX.'order_payment', 'payment_gateway');

        //////
        $data['order_edit_function'] = array(
            /* 'edit_product' => array('name' => 'EDIT PRODUCT',
              'function_work' => ''), */
            'remove_products' => array('name' => 'REMOVE PRODUCT',
                'function_work' => ''),
            'edit_change_payment_method' => array('name' => 'CHANGE PAYMENT METHOD',
                'function_work' => ''),
            'edit_shipping' => array('name' => 'EDIT SHIPPING',
                'function_work' => ''),
            'edit_address_ship_pay' => array('name' => 'EDIT ADDRESS',
                'function_work' => ''),
            'edit_refresh_paycharge' => array('name' => 'REFRESH PAYCHARGE DISCOUNT',
                'function_work' => ''),
            'edit_custom_paycharge' => array('name' => 'APPLY CUSTOM DISCOUNT',
                'function_work' => '')
        );

        // Devendra 27th August, if current payment method is credit, then can't change the payment method
        
        if($order_info['order']['payment_code'] != 'wsb_credit'){// show change payment method option for wsb_credit

            if (in_array($order_info['order']['payment_code'], CREDIT_PAYMENT_CODES) ) {
                unset($data['order_edit_function']['edit_change_payment_method']);
            }    
        }
        

        if ($data['is_admin'] || $data['is_operation_admin']) {
            $data['order_edit_function']['edit_remove_history'] = array('name' => 'EDIT ORDER HISTORY',
                'function_work' => '');
        }

        if ($data['is_admin']) {
            $data['order_edit_function']['edit_order_customer'] = array('name' => 'EDIT ORDER CUSTOMER',
                'function_work' => '');
        }
        if (($data['is_admin'] || $data['is_operation_admin']) &&
                (
                (!$data['is_buyer_invoice_generated']) ||
                ($data['is_buyer_invoice_generated'] &&
                ($order_info['suborder'][$suborder_id]['order_status_id'] == 9 ||
                $order_info['suborder'][$suborder_id]['order_status_id'] == 16
                )
                )
                )
        ) {
            $data['order_edit_function']['edit_break_suborder'] = array('name' => 'BREAK INTO ANOTHER SUBORDER',
                'function_work' => '');

            $data['order_edit_function']['edit_order_payment'] = array('name' => 'EDIT ORDER PAYMENT',
                'function_work' => '');
            
            $data['order_edit_function']['edit_gst_number'] = array('name' => 'UPDATE GST NUMBER',
                'function_work' => '');
        }

        if ($data['invoice_no'] == 0) {
            
            $data['order_edit_function']['edit_add_product'] = array('name' => 'ADD PRODUCT',
                'function_work' => '');
            
            //Edit Order product price required permission
            if( $this->user->hasPermission('access', 'sale/edit_order/editOrderProductSellingPrice', true) )
            {
                $data['order_edit_function']['edit_selling_price'] = array('name' => 'EDIT SELLING PRICE',
                'function_work' => '');
            }
            
        }
        if ($data['is_stock_transfer']) {
            $data['order_edit_function']['edit_stock_transfer'] = array('name' => 'STOCK TRANSFER',
                'function_work' => '');
        }

        //Check required permission for edit order product transfer price 
        if( $this->user->hasPermission('access', 'sale/edit_order/editOrderProductTransferPrice', true) )
        {
            $data['order_edit_function']['edit_transfer_price'] = array('name' => 'EDIT TRANSFER PRICE',
                'function_work' => '');
        }

        /*For LazyPay order, not allowed to add new product */
        if(strtolower($order_info['order']['payment_code']) == 'lazypay') {
            unset($data['order_edit_function']['edit_add_product']);
            unset($data['order_edit_function']['edit_shipping']);
        }

        /*For RBL order, not allowed to add new product */
        if(strtolower($order_info['order']['payment_code']) == 'rbl_credit') {
            unset($data['order_edit_function']['edit_add_product']);
            unset($data['order_edit_function']['edit_shipping']);
            unset($data['order_edit_function']['edit_selling_price']);
        }

        $this->getFunctionsWork($data);

        $this->response->setOutput($this->load->view('sale/order_edit.tpl', $data));
    }

    public function save() {
       
        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];

        if (!empty($this->request->post['products']) && empty($this->request->post['is_remove_product'])) {
            foreach ($this->request->post['products'] as $opid => $product_data) {
                $piece_in_set_data = array();
                $update_piece_in_set = false;

                $comment = !empty($product_data['comment']) ? $product_data['comment'] : '';
                $edit_type = !empty($product_data['edit_type']) ? $product_data['edit_type'] : 0;
                unset($product_data['comment'], $product_data['edit_type']);
                if (empty($product_data['delete']) &&
                        !empty($edit_type)) {

                    $data = array();
                    $data['edit_history'] = array(
                        'user_id' => $this->user->getId(),
                        'user_name' => $this->user->getUserName($this->user->getId())['name'],
                        'user_ip' => $_SERVER['SERVER_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'date_added' => date("Y-m-d H:i:s"),
                        'comment' => $comment,
                    );
                    $data['edit_type'] = $edit_type;
                    //$data['order_product_id']

                    foreach ($product_data as $field => $field_val) {
                        if ($field_val['new'] != $field_val['old'] && $field_val['new'] >= 0) {
                            if ($field_val == 'quantity') {
                                $data['piece_in_set'] = 1;
                            }
                            $data[$field] = $field_val['new'];
                        } else {
                            unset($product_data[$field]);
                        }
                    }

                    if (!empty($product_data)) {
                        $data['edit_history']['changes'] = $product_data;
                        OrderEdit::editOrderProducts($this->db, $opid, $data);
                    }
                } else if (isset($product_data['delete']) && !empty($edit_type)) {
                    $data = array();
                    $data['edit_type'] = $edit_type;
                    $data['edit_history'] = array(
                        'user_id' => $this->user->getId(),
                        'user_name' => $this->user->getUserName($this->user->getId())['name'],
                        'user_ip' => $_SERVER['SERVER_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'date_added' => date("Y-m-d H:i:s"),
                        'comment' => '',
                    );
                    OrderEdit::deleteOrderProduct($this->db, $opid, $data);
                }
            }
        }

        if (!empty($this->request->post['payment_method'])) {
            if ($this->request->post['payment_method']['new'] != $this->request->post['payment_method']['old']) {
                $data = array();
                $data['payment_code'] = $this->request->post['payment_method'];
                $data['user_id'] = $this->user->getId();
                $data['name'] = $this->user->getUserName($this->user->getId())['name'];
                $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];

                $data['apply_discounts'] = !empty($this->request->post['apply_discounts']) ? $this->request->post['apply_discounts'] : 0;
                $order_id = $this->request->post['order_id'];

                OrderEdit::editOrder($this, $order_id, $data);
            }
        }

        if (!empty($this->request->post['shipping_code'])) {

            $shipping_method_exp = explode(' (₹ ', $this->request->post['shipping_method']['new']);
            $this->request->post['shipping_method']['new'] = $shipping_method_exp[0];
            $data = array();
            $data['shipping_code'] = $this->request->post['shipping_code'];
            $data['shipping_method'] = $this->request->post['shipping_method'];

            $data['apply_ess_charges'] = !empty($this->request->post['apply_ess_charges']) ? 1 : 0;
            $data['ess_charges'] = !empty($this->request->post['ess_charges']) ? ((float) $this->request->post['ess_charges']) : 0;
            $data['new_shipping_value'] = (float) str_replace(',', '', rtrim($shipping_method_exp[1], ')'));


            $data['user_id'] = $this->user->getId();
            $data['users_name'] = $this->user->getUserName($this->user->getId())['name'];
            $order_id = $this->request->post['order_id'];
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];

            if (!empty($this->request->post['history_edit_id']) && !empty($this->request->post['comments'])) {

                $history_edit_id_arr = $this->request->post['history_edit_id'];
                $data['history_edit_id_arr'] = $history_edit_id_arr;
                $data['comments'] = $this->request->post['comments'];

                if (OrderEdit::removeEssCharges($this->db, $data)) {
                    $this->session->data['success_msg'] = "Successfully Updated.";
                }
            }

            OrderEdit::editOrderShipping($this->db, $order_id, $suborder_id, $data);
            OrderEdit::refreshOrCustomPaychargeDiscount($this, $order_id, '', $data);
        }

        if (!empty($this->request->post['stock_transfer'])) {

            $data = array();
            $data['user_id'] = $this->user->getId();
            $order_id = $this->request->post['order_id'];
            /*
             * get all sub order products
             */
            $selector = array(
                'order' => array('select' => array('customer_id')),
                'order_product' => array('select' => array('product_id', 'seller_id'))
            );
            $products = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
            /*
             * update customer id in ms_product tabel to seller id
             */
            $productIds = '';
            $change_log = array();

            foreach ($products['suborder'][$suborder_id]['order_product'] as $key => $value) {
                $productIds .= $value['product_id'] . ',';

                $change_log['changes_data'][$value['product_id']] = $value['seller_id'];
            }

            $productIds = rtrim($productIds, ',');

            $this->load->model('catalog/product');
            /*
             * update customer id in ms_product tabel to seller id
             */
            $this->model_catalog_product->ProductAssignToSeller($products['order']['customer_id'], $productIds, $change_log);
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];
            if (OrderEdit::updateToStockTransfer($this->db, $order_id, $data)) {
                $this->session->data['success_msg'] = "Successfully Updated. Please update shipping charges manually (if required).";
            }
        }

        if (!empty($this->request->post['history_id'])) {

            $old_order_history = unserialize(base64_decode($this->request->post['old_order_history']));
            $history_id_arr = $this->request->post['history_id'];
            $data = array();
            $data['user_id'] = $this->user->getId();
            $order_id = $this->request->post['order_id'];
            $data['history_id_arr'] = $history_id_arr;
            $data['old_order_history'] = $old_order_history;
            $data['comments'] = $this->request->post['comments'];
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];

            if (OrderEdit::removeOrderHistory($this->db, $order_id, $suborder_id, $data)) {
                $this->session->data['success_msg'] = "Successfully Updated.";
            }
        }

        if (!empty($this->request->post['order_product_id'])) {

            $check_validation = $this->checkBreakMoveSuborderValidation($this->request->post);

            if (!empty($check_validation)) {
                $this->session->data['error_msg'] = $check_validation;
                $this->response->redirect(
                        $this->url->link(
                                'sale/edit_order', '&token=' . $this->request->get['token'] . '&order_id=' . $order_id . '&suborder_id=' . $suborder_id, 'SSL'
                        )
                );
            }
            $data = array();
            $data['order_id'] = $order_id;
            $data['suborder_id'] = $suborder_id;
            $data['user_id'] = $this->user->getId();
            $data['order_product_id'] = explode(',', $this->request->post['order_product_id']);
            $suborder_id_arr = OrderEdit::getSubordersFromSameCity($this->db, $order_id, $suborder_id);
            $data['move_suborder_id'] = '';
            if (!empty($this->request->post['is_move_suborder']) && !empty($this->request->post['move_suborder_id']) && in_array($this->request->post['move_suborder_id'], $suborder_id_arr)) {
                $data['move_suborder_id'] = $this->request->post['move_suborder_id'];
            }
            $data['is_break_move_peice'] = $this->request->post['is_break_move_peice'];

            $data['sets_qty'] = array();
            if (!empty($this->request->post['sets_qty'])) {
                $data['sets_qty'] = unserialize(base64_decode($this->request->post['sets_qty']));
            }
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];

            if ($this->updateBreakedSuborderProduct($data)) {
                $this->session->data['success_msg'] = "Successfully Updated.";
            } else {
                $this->session->data['error_msg'] = "Something went wrong. Please contact tech.";
            }
        }

        if (!empty($this->request->post['is_add_order'])) {
            $data = $this->request->post;
            $error_arr = $this->addOrderProductValidation($order_id, $suborder_id, $data);

            if (!empty($error_arr) && count($error_arr) > 0) {
                $error_html = '';
                if (!empty($error_arr['p_error_msg'])) {
                    $error_html .= $error_arr['p_error_msg'];
                } else if (!empty($error_arr['products_arr'])) {
                    $error_html .= 'Some products added successfully.';

                    foreach ($error_arr['products_arr'] as $product_id => $product_arrs) {
//                            if (!empty($product_arrs['msg']) && is_array($product_arrs['msg'])) {
                        $error_html .= "<br>";
                        $error_html .= $product_arrs['p_name'] . " :- " . $product_arrs['msg'];
//                            }
                    }
                }
                $this->session->data['error_msg'] = $error_html;
            } else {
                //$this->session->data['success_msg'] = "Item added Successfully.";
            }
        }

        if (!empty($this->request->post['is_customer_edit'])) {
            $data = $this->request->post;
            $data['user_id'] = $this->user->getId();
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];

            if (OrderEdit::updateOrderCustomer($this, $order_id, $data)) {
                $this->session->data['success_msg'] = "Successfully Updated.";
            } else {
                $this->session->data['error_msg'] = "Something went wrong. Please try again.";
            }
        }

        if (!empty($this->request->post['apply_custom_discount'])) {
            $selector = array(
                'order' => array(
                    'select' => array(
                        'customer_id',
                        'payment_code',
                        'total',
                        'date_added'
                    ),
                ),
                'order_product' => array(
                    'select' => array(
                        'order_product_id',
                        'product_id',
                        'name',
                        'model',
                        'quantity',
                        'piece_in_set',
                        'price_per_piece',
                        'discount_per_piece',
                        'discount_breakup'
                    )
                )
            );
            $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);

            if (!empty($order_info['suborder'][$suborder_id]['order_product'])) {
                $order_products = $order_info['suborder'][$suborder_id]['order_product'];
                $order_products = array_combine(array_column($order_products, 'order_product_id'), $order_products);
            }
            $order_product_arr = array();
            if (!empty($this->request->post['wholesuborder'])) {
                if ((!empty($this->request->post['wholesuborder_rate']) ||
                        $this->request->post['wholesuborder_rate'] == 0) &&
                        !empty($this->request->post['wholesuborder_comment'])) {
                    $order_product_arr['custom_paycharge_rate'] = $this->request->post['wholesuborder_rate'];
                    $order_product_arr['custom_paycharge_comment'] = $this->request->post['wholesuborder_comment'];
                    $order_product_arr['order_product'] = $order_products;
                }
            } elseif (!empty($this->request->post['multipleproduct'])) {
                if ((!empty($this->request->post['wholesuborder_rate']) ||
                        $this->request->post['wholesuborder_rate'] == 0) &&
                        !empty($this->request->post['wholesuborder_comment'])) {
                    $order_product_arr['custom_paycharge_rate'] = $this->request->post['wholesuborder_rate'];
                    $order_product_arr['custom_paycharge_comment'] = $this->request->post['wholesuborder_comment'];
                    if (!empty($this->request->post['order_product_check'])) {
                        foreach ($this->request->post['order_product_check'] as $key => $value) {
                            $order_product_arr['order_product'][$value] = $order_products[$value];
                        }
                    }
                }
            } elseif (!empty($this->request->post['singleproduct'])) {
                foreach ($this->request->post['single_rate'] as $order_product_id => $value) {
                    if ((!empty($value) || $value == 0) &&
                            !empty($this->request->post['single_comment'][$order_product_id])) {
                        $order_product_arr['custom_paycharge_rate'] = $value;
                        $order_product_arr['custom_paycharge_comment'] = $this->request->post['single_comment'][$order_product_id];
                        $order_product_arr['order_product'][$order_product_id] = $order_products[$order_product_id];
                        break;
                    }
                }
            } else {
                $this->session->data['error_msg'] = "Something went wrong. Please try again.";
            }
            if (!empty($order_product_arr)) {
                $order_product_arr['customer_id'] = $order_info['order']['customer_id'];
                $order_product_arr['payment_code'] = $order_info['order']['payment_code'];
                $order_product_arr['date_added'] = $order_info['order']['date_added'];
                $order_product_arr['order_subtotal'] = $order_info['order']['total'];
                $data = array(
                    'user_id' => $this->user->getId(),
                    'name' => $this->user->getUserName($this->user->getId())['name'],
                    'user_name' => $this->user->getUserName($this->user->getId())['username'],
                    'suborder_id' => $suborder_id
                );

                $this->applyCustomPaychargeDiscount($order_id, $order_product_arr, $data);
            } else {
                $this->session->data['error_msg'] = "Something went wrong. Please try again.";
            }
        }

        if (!empty($this->request->post['refresh_discount'])) {
            $data = $this->request->post;
            $data['user_id'] = $this->user->getId();
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];
            
            $this->applyRefreshPaychargeDiscount($order_id, $data);
        }

        if (!empty($this->request->post['products']) && !empty($this->request->post['is_remove_product'])) {
            $return_arr = $this->_validateAndAddComoProductsSibling($order_id, $suborder_id, $this->request->post['products']);
            $seller_id_arr = array();

            foreach ($return_arr as $opid => $product_data) {

                $comment = !empty($product_data['comment']) ? $product_data['comment'] : '';
                $edit_type = !empty($product_data['edit_type']) ? $product_data['edit_type'] : 0;
                $quantity_update = !empty($product_data['quantity']) ? $product_data['quantity'] : 0;
                unset($product_data['comment'], $product_data['edit_type']);
                if (!empty($edit_type)) {
                    $data = array();
                    $data['edit_type'] = $edit_type;
                    $data['edit_history'] = array(
                        'user_id' => $this->user->getId(),
                        'name' => $this->user->getUserName($this->user->getId())['name'],
                        'user_name' => $this->user->getUserName($this->user->getId())['username'],
                        'user_ip' => $_SERVER['SERVER_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'date_added' => date("Y-m-d H:i:s"),
                        'comment' => $edit_type,
                        'additional_comment' => $comment,
                        'order_id' => $order_id,
                        'suborder_id' => $suborder_id
                    );

                    //If order product split
                    if (isset($product_data['quantity']) && $product_data['quantity'] > 0 && $product_data['quantity']['new'] < $product_data['quantity']['old']) {
                        $data['quantity'] = (int) $product_data['quantity']['new'];
                        $order_product_id = OrderEdit::splitOrderProduct($this->db, $opid, $data, '', true);
                    } else {
                        $data['edit_history'][] = $data['edit_history'];
                        OrderEdit::updateOrderProduct($this->db, $opid, $data);
                        $order_product_id = $opid;
                    }

                    $order_product_info = OrderEdit::getOrderProductByOPId($this->db, $order_product_id);
                    $product_info = OrderEdit::getProductDetails($this->db, $order_product_info['product_id']);

                    $seller_id_arr[] = $order_product_info['seller_id'];
                    if ((int) $product_info['piece_in_set'] === (int) $order_product_info['piece_in_set']) {
                        $order_option = array();
                        if (!empty($order_product_info['order_option'])) {
                            $order_option = $order_product_info['order_option'];
                        }

                        $check_seller_invoice_generate = SellerInfo::checkSellerInvoiceToBeGenerated($this->db, $order_product_info['seller_id']);
                        if ($edit_type == 'CANCELLED_BY_CUSTOMER' && empty($check_seller_invoice_generate)) {
                            OrderEdit::updateProductStock($this->db, $order_product_info['product_id'], $order_product_info['seller_id'], $order_product_info['quantity'], $order_option);
                        } else if ($edit_type == 'SELLER_NOT_SUPPLIED' && !empty($check_seller_invoice_generate)) {
                            OrderEdit::updateSellerStock($this->db, $order_product_info['product_id'], $order_product_info['seller_id'], $order_option);
                        }
                    } else {
                        $data_arr = array(
                            'suborder_id' => $order_product_info['suborder_id'],
                            'model' => $order_product_info['model'],
                            'quantity' => $order_product_info['quantity'],
                            'product_piece_in_set' => $order_product_info['piece_in_set'],
                            'order_product_piece_in_set' => $product_info['piece_in_set']
                        );
                        $this->sendMailPieceInSetMismatchWhenStockUpdate($data_arr);
                    }
                }
            }

            //Update order and suborder total
            OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);

            //send mail to seller
            if (!empty($seller_id_arr)) {
                $selector = array(
                    'suborder' => array(
                        'select' => array('order_status_id')
                    )
                );
                $suborder_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
                if (!empty($suborder_info['suborder'][$suborder_id]['order_status_id'])) {
                    $order_status = $suborder_info['suborder'][$suborder_id]['order_status_id'];
                    if ($order_status == 9 || $order_status == 16) {
                        $seller_id_arr = array_filter(array_unique($seller_id_arr));
                        $this->load->model('sale/order');
                        foreach ($seller_id_arr as $seller_id) {
                            $this->model_sale_order->sendSellerMail($order_id, $suborder_id, $seller_id);
                        }
                    }
                }
            }

            //check if all order product is cancelled then cancel suborder
            $check_for_cancelled_order_applicable = OrderEdit::checkIfOrderCanBeCancelled($this->db, $order_id, $suborder_id);
            if ($check_for_cancelled_order_applicable) {
                $add_order_history_arr = array(
                    'order_id' => $order_id,
                    'suborder_id' => $suborder_id,
                    'order_status_id' => 2,
                    'user' => $this->user->getUserName($this->user->getId())['username'],
                    'notify_email' => 0,
                    'notify_sms' => 0,
                    'comment' => 'Cancelled as per customer request',
                    'notes' => '',
                    'shippingco' => '',
                    'tracking' => '',
                    'give_cashback' => 1,
                    'is_stock_update' => 1
                );
                $this->load->model('checkout/order', 'frontend');
                $this->frontend_model_checkout_order->addOrderHistory($add_order_history_arr);
            }

            //edit order history log
            $this->updateOrderHistoryLogForRemovedOrderProducts($order_id, $suborder_id, $return_arr);

            //Reset order shipping method as per order sub-total MSA June 2019
            $this->resetFreeShippingBySubOrderTotal($this->db, $order_id, $suborder_id);

        }

        if (!empty($this->request->post['gst_number'])) {

            $gst_number  = $this->request->post['gst_number'] ?? '';
            $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
            $gst_number  = trim($gst_number);
            $customer_id = $this->request->post['customer_id'];
            $old_gst_number = $this->request->post['old_gst_number'];
            $error_msg = $this->_validateGstNumber($customer_id, $gst_number);

            if (!empty($error_msg)) {
                $this->session->data['error_msg'] = $error_msg;
            } else {
                $data_arr = array(
                    'order_id' => $order_id,
                    'gst_number' => $gst_number,
                    'old_gst_number' => $old_gst_number,
                    'customer_id' => $customer_id
                );
                $this->_updateGstNumberOfOrder($data_arr);
                $this->session->data['success_msg'] = "GST no. updated successfully.";
            }
        }

        $this->response->redirect(
                $this->url->link(
                        'sale/edit_order', '&token=' . $this->request->get['token'] . '&order_id=' . $order_id . '&suborder_id=' . $suborder_id, 'SSL'
                )
        );
    }
    
    /**
     * Public method to update shipping method if sub-order total < CART_SUB_TOTAL_FOR_FREE_SHIPPING
     * @param: DB Object $db
     * @param: int $order_id
     * @param: string $suborder_id
     * @author: MSA, June 2019
     */
    public function resetFreeShippingBySubOrderTotal(Database\db $db, int $order_id, string $suborder_id)
    {
        $selector = array(
            'order' => array(),
            'suborder' => array(),
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, '', $selector);
        $suborders = $order_info['suborder'] ?? array();
        
        //get products total that are not - CANCELLED_BY_CUSTOMER
        $products = OrderEdit::getAllProductInfoAndOrderInfo($db, $order_id);
        $order_subtotal = $products['order_subtotal'] ?? 0;

        foreach ($suborders as $suborder_id => $value) 
        {
            $old_shipping_code = $order_info['suborder'][$suborder_id]['shipping_code'];
            $old_shipping_code = 'free.free';
            if($old_shipping_code == 'free.free' && $order_subtotal < CART_SUB_TOTAL_FOR_FREE_SHIPPING)
            {
                $shipping_charges_for_order = OrderEdit::getShippingDetails($this->db, $order_id, $suborder_id);
                $shipping_charges = json_decode($shipping_charges_for_order, true);

                if(!empty($shipping_charges['shipping_method']['weight']['quote']))
                {
                    $shipping_charge_data = $shipping_charges['shipping_method']['weight']['quote']['weight_5'];
                    if(!empty($shipping_charge_data['cost'])) {
                        OrderEdit::changeShippingCharge($db, 
                                                        $shipping_charge_data['cost'], 
                                                        $order_id, $suborder_id, 
                                                        $shipping_charge_data['code'], 
                                                        $shipping_charge_data['title']
                                                    ); 
                    }
                }
            }   
        }
    }

    /**
     * Public method to update edit order history log for removed order products
     * @param: int $order_id
     * @param: string $suborder_id
     * @param: array $data
     * @author: MSA, May 2019
     */
    public function updateOrderHistoryLogForRemovedOrderProducts(int $order_id, string $suborder_id, array $data)
    {   
        $this->load->model('catalog/product');

        if(!empty($data)) {
            $log = array();
            $old_quantity = 0;
            $new_quantity = 0;
            foreach ($data as $key => $value) {
                   if(!empty($value['quantity']['old'])) {
                        $old_quantity = $value['quantity']['old'];
                        $new_quantity = $value['quantity']['new'];
                   }else{
                        $old_quantity = $this->model_catalog_product->getOrderProductQuantity($key);
                   }
                   $records_logs = array(
                        'order_id'      => $order_id,
                        'suborder_id'   => $suborder_id,
                        'key_id'        => $key,
                        'key_name'      => 'order_product_id',
                        'edit_type'     => 'DELETE_PRODUCT',
                        'field_name'    => 'Edit Order',
                        'old_value'     => $old_quantity,
                        'new_value'     => $new_quantity,
                        'user_id'       => $this->user->getId(),
                        'comment'       => 'Order Product Removed',
                        'name'          => $this->user->getUserName($this->user->getId())['name'],
                        'user_name'     => $this->user->getUserName($this->user->getId())['username']
                   );
                OrderEdit::saveEditHistory($this->db, $records_logs);
            }
        }
    }
    public function editOrderProductSellingPrice()
    {
        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];

        $this->load->model('catalog/product');

        $discount_type = $this->request->post['discount_type'] ?? '';
        $selling_price = $this->request->post['selling_price'] ?? array();

        //products to update selling price
        $productsEditSellingPrice = array();
        if(!empty($selling_price)) {
            foreach ($selling_price as $key => $value) {
                if($value['old'] != $value['new']) {
                    $productsEditSellingPrice[$key] = $value['new'];
                }
            }
        }
        if(!empty($productsEditSellingPrice)) {
            $tax = new Tax($this->registry);
            //update order product selling price
            foreach ($productsEditSellingPrice as $key => $value) {
                $order_product_id  = $key;
                $new_selling_price = $value;
                $products = OrderEdit::getOrderProductByOPId($this->db, $order_product_id);
                
                //update order product selling price
                $this->model_catalog_product->updateOrderProductSellingPrice($order_product_id,$new_selling_price);

                //log data
                $log_data = array(
                    'order_id'      => $order_id,
                    'suborder_id'   => $suborder_id,
                    'key_id'        => $products['order_product_id'],
                    'key_name'      => 'order_product_id',
                    'edit_type'     => 'EDIT_SELLING_PRICE',
                    'field_name'    => 'price_per_piece',
                    'old_value'     =>  $products['price_per_piece'],
                    'new_value'     =>  $new_selling_price,
                    'comment'       =>  'Edit Order > Edit Selling Price',
                    'user_id'       =>  $this->user->getId(),
                    'name'          =>  $this->user->getUserName()['username'],
                    'user_name'     =>  $this->user->getUserName()['username'] ,   
                ); 
                OrderEdit::saveEditHistory($this->db, $log_data);
                
                $updated_price = (float)$new_selling_price + (float)$products['discount_per_piece'];
                //getting mrp of product
                $mrp = $this->model_catalog_product->getOrderProductMrp($products['product_id']);
                //getting tax_class_id
                $tax_class_id = $tax->getTaxClassIdFromHSNCode($products['hsn_code']);
                 //getting output tax rates from the updated_price
                $output_tax_rates = $tax->getTaxRate($updated_price, $tax_class_id, array(), $mrp);
                if((float)$output_tax_rates != (float)$products['output_tax_rates']) {
                   $this->model_catalog_product->updateOrderProductOutputTaxRate($order_product_id,$output_tax_rates);
                   
                   //save log data 
                    $log_data['field_name'] = 'output_tax_rates';
                    $log_data['old_value']  = $products['output_tax_rates'];
                    $log_data['new_value']  = (float)$output_tax_rates;
                    OrderEdit::saveEditHistory($this->db, $log_data);
                }
            }    

            //refresh paycharge discounts
            $data = array();
            $data['order_id'] = $this->request->post['order_id'];
            $data['suborder_id'] = $this->request->post['suborder_id'];
            $data['user_id'] = $this->user->getId();
            $data['name'] = $this->user->getUserName($this->user->getId())['name'];
            $data['user_name'] = $this->user->getUserName($this->user->getId())['username'];
            $this->applyRefreshPaychargeDiscount($order_id, $data);
            
            //Update order and suborder total
            OrderEdit::updateOrderTotalsDueVariousAction($this->db, $products['order_id'], $products['suborder_id']);
        }

        $this->response->redirect(
                $this->url->link(
                        'sale/edit_order', '&token=' . $this->request->get['token'] . '&order_id=' . $order_id . '&suborder_id=' . $suborder_id, 'SSL'
                )
        );
    }

    public function editOrderProductTransferPrice()
    {
        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];
        $transfer_price = $this->request->post['transfer_price'] ?? array();

        $this->load->model('catalog/product');
        //products to update for transfer price
        $productsEditTransferPrice = array();
        if(!empty($transfer_price)) {
            foreach ($transfer_price as $key => $value) {
                if($value['old'] != $value['new']) {
                    $productsEditTransferPrice[$key] = $value['new'];
                }
            }
        }
        
        if(!empty($productsEditTransferPrice)) {
            $tax = new Tax($this->registry);
            //update order product selling price
            foreach ($productsEditTransferPrice as $key => $value) {
                $order_product_id  = $key;
                $new_transfer_price = $value;
                $products = OrderEdit::getOrderProductByOPId($this->db, $order_product_id);
                
                //update order product selling price
                $this->model_catalog_product->updateOrderProductTransferPrice($order_product_id,$new_transfer_price);

                //log data
                $log_data = array(
                    'order_id'      => $order_id,
                    'suborder_id'   => $suborder_id,
                    'key_id'        => $products['order_product_id'],
                    'key_name'      => 'order_product_id',
                    'edit_type'     => 'EDIT_TRANSFER_PRICE',
                    'field_name'    => 'transfer_price_per_piece',
                    'old_value'     =>  $products['transfer_price_per_piece'],
                    'new_value'     =>  $new_transfer_price,
                    'comment'       =>  'Edit Order > Edit Transfer Price',
                    'user_id'       =>  $this->user->getId(),
                    'name'          =>  $this->user->getUserName()['username'],
                    'user_name'     =>  $this->user->getUserName()['username'] ,   
                ); 
                OrderEdit::saveEditHistory($this->db, $log_data);
                
                $updated_price = (float)$new_transfer_price;
                
                //getting mrp of product
                $mrp = $this->model_catalog_product->getOrderProductMrp($products['product_id']);

                $seller_input_tax = $tax->getTaxRateForTaxIncludedPrice($updated_price, $products['hsn_code'], $mrp);

                if((float)$seller_input_tax != (float)$products['seller_input_tax']) {
                   
                   $this->model_catalog_product->updateOrderProductSellerInputTaxRate($order_product_id,$seller_input_tax);
                   
                   //save log data 
                    $log_data['field_name'] = 'seller_input_tax';
                    $log_data['old_value']  = $products['seller_input_tax'];
                    $log_data['new_value']  = (float)$output_tax_rates;
                    OrderEdit::saveEditHistory($this->db, $log_data);
                }
            }    
        }
        $this->response->redirect(
                $this->url->link(
                        'sale/edit_order', '&token=' . $this->request->get['token'] . '&order_id=' . $order_id . '&suborder_id=' . $suborder_id, 'SSL'
                )
        );
    }

    public function checkBreakMoveSuborderValidation($data) {
        $error_msg = '';
        if (!empty($data['order_product_id'])) {
            $order_product_id = explode(',', $data['order_product_id']);
            $buyer_invoice = new BuyerInvoice($this->registry);
            $order_products = $buyer_invoice->getProductArrayByOrderProductIds($order_product_id);
            $order_products = array_combine(array_column($order_products, 'order_product_id'), $order_products);

            //If all order product move or break this code will return false 
            if ((empty($data['is_move_suborder']) && empty($data['is_break_move_peice'])) ||
                    ((!empty($data['is_move_suborder']) && empty($data['is_break_move_peice'])))) {
                $check_all_product_move_break = OrderEdit::checkIfOrderCanBeCancelled($this->db, $data['order_id'], $data['suborder_id'], $order_product_id);
                if (!empty($check_all_product_move_break)) {
                    $error_msg .= "You cannot Break/Move all order products to New/Existing suborder.<br>";
                }
            }
            $selector = array('suborder' => array('select' => array('order_status_id', 'invoice_no')));
            $order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], $selector);
            if (!empty($order_info['suborder'][$data['suborder_id']]['order_status_id'])) {
                $curr_order_status_id = (int) $order_info['suborder'][$data['suborder_id']]['order_status_id'];
                $invoice_no = (int) $order_info['suborder'][$data['suborder_id']]['invoice_no'];

                if ($curr_order_status_id != 1 && $curr_order_status_id != 9 && $curr_order_status_id != 16 && $invoice_no > 0) {

                    $error_msg .= "Buyer invoice cannot be cancelled. Order should be in Processed / Tentative Processed status, for invoice to be Cancelled <br>";
                }
            }

            if (!empty($data['is_break_move_peice'])) {
                $sets_qty = unserialize(base64_decode($data['sets_qty']));
                if (!empty(unserialize(base64_decode($data['sets_qty'])))) {
                    foreach ($order_product_id as $value) {
                        if (empty($sets_qty[$value]) || $sets_qty[$value] == '' || $sets_qty[$value] == 0) {
                            $error_msg .= "Products sets quantity should not be empty or 0.<br>";
                        } else {
                            if ($sets_qty[$value] >= $order_products[$value]['quantity']) {
                                $error_msg .= "Move/Break Sets value cannot be greater than or equals to sets quantity.<br>";
                            }
                        }
                    }
                } else {
                    $error_msg .= "Please select products sets quantity first.<br>";
                }
            }
            if (!empty($data['is_move_suborder'])) {
                if (empty($data['move_suborder_id'])) {
                    $error_msg .= "Moving suborder id should be valid.<br>";
                }
            }
        } else {
            $error_msg .= "Please select products first.<br>";
        }
        return $error_msg;
    }

    public function addReviewProducts() {
        $data = array();
        $local_storage_product_detail = array();
        if (!empty($this->request->post['local_storage_product_detail'])) {
            $local_storage_product_detail = $this->request->post['local_storage_product_detail'];
        }
        $product_model_arr = array();

        if (!empty($local_storage_product_detail)) {
            foreach ($local_storage_product_detail as $product_id => $product_option_val) {
                if (empty($product_option_val['qty']) && empty($product_option_val['madel_name'])) {
                    foreach ($product_option_val as $product_option_id => $option_val_arr) {
                        foreach ($option_val_arr as $option_value_id => $option_qty) {
                            $product_model_arr[$product_id] = $option_qty['madel_name'];
                        }
                    }
                } else {
                    if ($product_option_val['qty'] > 0) {
                        $product_model_arr[$product_id] = $product_option_val['madel_name'];
                    }
                }
            }
        }

        //For product ids which have selected by admin
        $product_id_arr = array_keys($local_storage_product_detail);

        $data['product_list'] = array();
        $product_final_arr = array();

        if (!empty($product_model_arr)) {
            $product_model_arr = array_unique($product_model_arr);
            foreach ($product_model_arr as $product_id => $model) {
                $product_arr = json_decode(OrderEdit::getProductDetailsFromApi($product_id), true);
                $product_option_arr = array();
                if (!empty($product_arr['options'])) {
                    $product_option_arr = array_combine(array_column($product_arr['options'], 'product_option_id'), $product_arr['options']);
                    foreach ($product_option_arr as $product_option_id => &$option_value_arr) {
                        if ($option_value_arr['product_option_value']) {
                            $option_value_arr['product_option_value'] = array_combine(array_column($option_value_arr['product_option_value'], 'product_option_value_id'), $option_value_arr['product_option_value']);
                        }
                    }
                }

                if (!empty($product_arr['is_combo'])) {
                    $this->load->model('catalog/product');
                    $this->load->model('tool/image');
                    $associate_products = $this->model_catalog_product->getAssociateProducts($product_id);
                    if (!empty($associate_products)) {
                        foreach ($associate_products as $associate_product) {
                            $image_url = $this->model_tool_image->resize($associate_product['image'], $this->config->get('config_image_product_height'), $this->config->get('config_image_product_width'));
                            $product_final_arr[$associate_product['product_id']] = array(
                                'model' => $associate_product['model'],
                                'image' => $image_url,
                                'name' => $associate_product['name'],
                                'set_description' => $associate_product['set_description'],
                                'product_id' => $associate_product['product_id'],
                                'quantity' => $product_arr['quantity'],
                                'price' => $associate_product['price'],
                                'options' => $product_option_arr,
                                'stock' => $product_arr['stock_bool'],
                                'stock_status' => $product_arr['stock_status'],
                                'seller_id' => $associate_product['seller_id'],
                                'combo_product_id' => $product_id
                            );
                        }
                        continue;
                    }
                }

                $product_final_arr[$product_id] = array(
                    'model' => $product_arr['model'],
                    'image' => $product_arr['image'],
                    'name' => $product_arr['name'],
                    'set_description' => $product_arr['set_description'],
                    'product_id' => $product_arr['product_id'],
                    'quantity' => $product_arr['quantity'],
                    'combo_product_id' => $product_id,
                    'price' => $product_arr['price'],
                    'options' => $product_option_arr,
                    'stock' => $product_arr['stock_bool'],
                    'stock_status' => $product_arr['stock_status'],
                    'seller_id' => $product_arr['seller_id'],
                );
            }
        }

        $data['order_id'] = $this->request->post['order_id'];
        $data['suborder_id'] = $this->request->post['suborder_id'];
        $data['products_details'] = $local_storage_product_detail;
        $data['product_final_array'] = $product_final_arr;
        $data['save'] = $this->url->link('sale/edit_order/save', '&token=' . $this->session->data['token'], 'SSL');
        $this->response->setOutput($this->load->view('sale/order_edit_product_confirm.tpl', $data));
    }

    public function suborderBreakConfirm() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        $data['token'] = $this->session->data['token'];

        $data['order_id'] = $this->request->post['order_id'];
        $data['suborder_id'] = $this->request->post['suborder_id'];
        $order_product_id = $this->request->post['order_product_id'];
        $data['is_move_suborder'] = $this->request->post['is_move_suborder'];
        $data['is_break_move_peice'] = $this->request->post['is_break_move_peice'];

        $data['sets_qty'] = array();
        if (!empty($this->request->post['sets_qty'])) {
            $data['sets_qty'] = $this->request->post['sets_qty'];
        }

        $data['order_product_id'] = $order_product_id;
        $selector = array(
            'order' => array(
                'select' => array('order_id')
            ),
            'suborder' => array(
                'select' => array(
                    'gst',
                    'buyer_invoice_id',
                    'invoice_no',
                    'invoice_date',
                    'invoice_prefix',
                    'order_status_id',
                    'shipping_charge')
            ),
            'order_product' => array()
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], $selector);
        $order_details = $order_info['order'];
        $suborder_info = $order_info['suborder'][$data['suborder_id']];
        $products = $suborder_info['order_product'];
        unset($suborder_info['order_product']);

        $data['products'] = OrderEdit::filterOrderProducts($products);

        if ($data['is_break_move_peice']) {
            $data['shipping_details'] = OrderEdit::getReadjustedShippingForPiecesSplitting($products, $order_product_id, $suborder_info['shipping_charge'], $data['sets_qty']);
        } else {
            $data['shipping_details'] = OrderEdit::getReadjustedShippingForBreakSuborderIntoAnother($data['products'], $order_product_id, $suborder_info['shipping_charge']);
        }


        $data['suborder_info'] = $suborder_info;
        $data['suborder_id_arrs'] = OrderEdit::getSubordersFromSameCity($this->db, $data['order_id'], $data['suborder_id']);

        $data['save'] = $this->url->link('sale/edit_order/save', '&token=' . $this->session->data['token'], 'SSL');
        $this->response->setOutput($this->load->view('sale/order_edit_break_suborder_confirm.tpl', $data));
    }

    public function calculateInvoiceAmounts() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);
        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];
        $new_suborder_id = $this->request->post['new_move_suborder_id'];
        $order_product_id = explode(',', $this->request->post['order_product_id']);
        $is_break_move_peice = $this->request->post['is_break_move_peice'];
        $is_move_suborder = $this->request->post['is_move_suborder'];
        $sets_qty = array();

        if (!empty($this->request->post['sets_qty'])) {
            $sets_qty = unserialize(base64_decode($this->request->post['sets_qty']));
        }

        $suborder_info = OrderEdit::getTotalAmounts($this->db, $order_id, $suborder_id, $new_suborder_id);
        $buyer_invoice = new BuyerInvoice($this->registry);
        $order_products = $buyer_invoice->getProductsArrayBySuborderId($order_id, $suborder_id);

        $x = 1;
        $total_amount = 0;
        $total_amount_inc_tax = 0;
        $new_suborder_total_amount = 0;
        $new_suborder_total_amount_inc_tax = 0;
        $max_tax_rate_for_shipping_old = array();
        $max_tax_rate_for_shipping_new = array();
        $total_invoice_amount_new = 0;
        $total_invoice_amount_old = 0;
        $total_amount_set_break = 0;
        $total_amount_inc_tax_set_break = 0;
        foreach ($order_products as $product) {
            $quantity = $product['quantity'];
            $quantity_set_break = 0;


            $piece_in_set = $product['piece_in_set'];
            $price_per_piece = (float) $product['price_per_piece'];
            $discount_per_piece = (float) $product['discount_per_piece'];
            $total_pieces = ($quantity * $piece_in_set);
            $total_amount_per_product = (float) (($price_per_piece + $discount_per_piece) * $total_pieces);
            $total_amount_per_product_inc_tax = (float) ROUND(($total_amount_per_product * (1 + $product['output_tax_rates'] / 100)), 2);
            $total_amount += $total_amount_per_product;
            $total_amount_inc_tax += $total_amount_per_product_inc_tax;

            if (in_array($product['order_product_id'], $order_product_id)) {

                if (!empty($is_break_move_peice)) {
                    $quantity_set_break = $sets_qty[$product['order_product_id']];
                    $total_pieces_set_break = ($quantity_set_break * $piece_in_set);
                    $total_amount_per_product_set_break = (float) (($price_per_piece + $discount_per_piece) * $total_pieces_set_break);
                    $total_amount_per_product_inc_tax_set_break = (float) ROUND(($total_amount_per_product_set_break * (1 + $product['output_tax_rates'] / 100)), 2);
                    $total_amount_set_break += $total_amount_per_product_set_break;
                    $total_amount_inc_tax_set_break += $total_amount_per_product_inc_tax_set_break;

                    $new_suborder_total_amount += $total_amount_per_product_set_break;
                    $new_suborder_total_amount_inc_tax += $total_amount_per_product_inc_tax_set_break;
                } else {
                    $new_suborder_total_amount += $total_amount_per_product;
                    $new_suborder_total_amount_inc_tax += $total_amount_per_product_inc_tax;
                }
                $max_tax_rate_for_shipping_new[] = $product['output_tax_rates'];

                $x++;
            } else {
                $max_tax_rate_for_shipping_old[] = $product['output_tax_rates'];
            }
        }


        if ($is_break_move_peice) {
            $shipping_details = OrderEdit::getReadjustedShippingForPiecesSplitting($order_products, $order_product_id, $suborder_info[$suborder_id]['shipping_charge'], $sets_qty);
        } else {
            $shipping_details = OrderEdit::getReadjustedShippingForBreakSuborderIntoAnother($order_products, $order_product_id, $suborder_info[$suborder_id]['shipping_charge']);
        }





        $max_tax_rate_for_shipping_old = array_unique($max_tax_rate_for_shipping_old);
        $max_tax_rate_for_shipping_new = array_unique($max_tax_rate_for_shipping_new);
        $max_tax_rate_new = (float) max($max_tax_rate_for_shipping_new);
        if (!empty($max_tax_rate_for_shipping_old)) {
            $max_tax_rate_old = (float) max($max_tax_rate_for_shipping_old);
        } else {
            $max_tax_rate_old = $max_tax_rate_new;
        }




        $total_invoice_amount_old = (float) (($total_amount_inc_tax - $new_suborder_total_amount_inc_tax) + ROUND(($shipping_details['suborder_shipping_for_old'] * (1 + ($max_tax_rate_old / 100))), 2));
        $total_invoice_amount_new = (float) ($new_suborder_total_amount_inc_tax + ROUND(($shipping_details['suborder_shipping_for_new'] * (1 + ($max_tax_rate_new / 100))), 2));

        if (!empty($is_move_suborder)) {
            $total_invoice_amount_new = $total_invoice_amount_new + $suborder_info[$new_suborder_id]['total'];
        }
        $return = array(
            'total_invoice_amount_new' => ROUND($total_invoice_amount_new, 2),
            'total_invoice_amount_old' => ROUND($total_invoice_amount_old, 2)
        );
        echo json_encode($return);
    }

    /*
     * Function moved from controller/sale/order. Function used for break suborder into another suborder
     * @author: Nilesh
     */

    public function updateBreakedSuborderProduct($data) {


        $this->load->language('sale/order');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/edit_order')) {
            $json['error'] = $this->language->get('error_permission');
        }
        $order_product_id = $data['order_product_id'];
        $order_id = $data['order_id'];
        $suborder_id = $data['suborder_id'];

        //For combo product 
        $order_product_id_flip = array_flip($order_product_id);
        $order_product_id_new = $this->_validateAndAddComoProductsSibling($order_id, $suborder_id, $order_product_id_flip, true);
        if (!empty($data['sets_qty'])) {
            $data['sets_qty'] = $this->_validateAndAddComoProductsSibling($order_id, $suborder_id, $data['sets_qty'], true);
        }

        $order_product_id = $data['order_product_id'] = array_keys($order_product_id_new);
        $edit_history = array(
            'user_id' => $this->user->getId(),
            'name' => $this->user->getUserName($this->user->getId())['name'],
            'user_name' => $this->user->getUserName($this->user->getId())['username'],
            'user_ip' => $_SERVER['SERVER_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'date_added' => date("Y-m-d H:i:s")
        );

        if (!empty($data['move_suborder_id']) && empty($data['is_break_move_peice'])) {
            $result = OrderEdit::updateMovedSuborderProduct($this, $order_id, $suborder_id, $order_product_id, $edit_history, $data['move_suborder_id']);
        } else if (!empty($data['move_suborder_id']) && !empty($data['is_break_move_peice'])) {
            $result = OrderEdit::updateMovedSuborderOrderProductsQtyWise($this, $order_id, $suborder_id, $order_product_id, $edit_history, $data['sets_qty'], $data['move_suborder_id']);
        } else if (empty($data['move_suborder_id']) && empty($data['is_break_move_peice'])) {
            $result = OrderEdit::updateBreakedSuborderProduct($this, $order_id, $suborder_id, $order_product_id, $edit_history);
        } else if (empty($data['move_suborder_id']) && !empty($data['is_break_move_peice'])) {
            $result = OrderEdit::updateSplittedSuborderOrderProductsQtyWise($this, $order_id, $suborder_id, $order_product_id, $edit_history, $data['sets_qty']);
        }


        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * New Function for edit order coppied from sale/order
     */

    //get order information from order table by particular order id by vikas
    // order_edit address
    public function getInformationByOrderId() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        $data['token'] = $this->session->data['token'];

        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];

        $order_information = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id);

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        $this->load->model('localisation/zone');
        $data['zones'] = $this->model_localisation_zone->getZonesByCountryId($order_information['order']['payment_country_id']);

        // Custom Fields
        $this->load->model('sale/custom_field');
        $data['custom_fields'] = $this->model_sale_custom_field->getCustomFields();

        $data['payment_firstname'] = $order_information['order']['shipping_firstname'];
        $data['payment_lastname'] = $order_information['order']['shipping_lastname'];
        $data['payment_company'] = $order_information['order']['shipping_company'];
        $data['payment_telephone'] = $order_information['order']['telephone'];
        $data['payment_address_1'] = $order_information['order']['shipping_address_1'];
        $data['payment_address_2'] = $order_information['order']['shipping_address_2'];
        $data['payment_city'] = $order_information['order']['shipping_city'];
        $data['payment_postcode'] = $order_information['order']['shipping_postcode'];
        $data['payment_country'] = $order_information['order']['shipping_country'];
        $data['payment_country_id'] = $order_information['order']['shipping_country_id'];
        $data['payment_zone'] = $order_information['order']['shipping_zone'];
        $data['payment_zone_id'] = $order_information['order']['shipping_zone_id'];
        $data['gst_number'] = $order_information['order']['gst_number'];
        $data['gst'] = $order_information['suborder'][$suborder_id]['gst'];
        $data['payment_custom_field'] = unserialize($order_information['order']['shipping_custom_field']);

        $data['edit_address_detail'] = $this->url->link('sale/edit_order/order_update_address', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $order_id . '&suborder_id=' . $suborder_id . '&type=edit_address', 'SSL');
        $this->response->setOutput($this->load->view('sale/order_edit_address.tpl', $data));
    }

    public function getFunctionsWork(&$data) {

        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        foreach ($data['order_edit_function'] as $key => &$value) {
            $value['function_work'] = $this->load->view('sale/order_' . $key . '.tpl', $data);
        }
    }

    //update order information in order table by particular order id by vikas
    // order_edit_address
    public function order_update_address() {
        $this->request->post['order_id'] = $this->request->get['order_id'];
        $this->request->post['suborder_id'] = $this->request->get['suborder_id'];
        $country_id = $this->request->post['country'];

        if (!empty($this->request->post['zone'])) {
            $this->load->model('localisation/country');
            $country_name_list = $this->model_localisation_country->getCountry($country_id);
        }
        if (!empty($this->request->post['zone'])) {
            $zone_id = $this->request->post['zone'];
            $this->load->model('localisation/zone');
            $zone_name_list = $this->model_localisation_zone->getZone($zone_id);
        }

        $this->request->post['country_name'] = $country_name_list['name'];
        $this->request->post['zone_name'] = $zone_name_list['name'];

        $this->request->post['user_id'] = $this->user->getId();
        $this->request->post['name'] = $this->user->getUserName($this->user->getId())['name'];
        $this->request->post['user_name'] = $this->user->getUserName($this->user->getId())['username'];

        OrderEdit::updateEditAdress($this->db, $this->request->post);
        $this->session->data['success_msg'] = "Address Updated Successfully .";

        $url = '';

        if (isset($this->request->get['order_id'])) {
            $url .= '&order_id=' . $this->request->get['order_id'];
            $url .= '&suborder_id=' . $this->request->get['suborder_id'];
        }
        $this->response->redirect($this->url->link('sale/edit_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    public function getProductLists() {
        $data = array();

        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];
        $selector = array('order' => array('select' => 'order_id'),
            'suborder' => array('select' => 'suborder_id'),
            'order_product' => array('select' => array('order_product_id', 'product_id', 'quantity')),
            'order_option' => array(),
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        $product_info = $order_info['suborder'][$suborder_id]['order_product'];
        $product_info = array_combine(array_column($product_info, 'order_product_id'), $product_info);
        $product_options = array();
        $products_finaly_arr = array();
        if (!empty($order_info['suborder'][$suborder_id]['order_option'])) {
            $product_options = $order_info['suborder'][$suborder_id]['order_option'];
            $product_options = array_combine(array_column($product_options, 'order_product_id'), $product_options);
        }
        foreach ($product_info as $order_product_id => $order_product) {
            $product_id = $order_product['product_id'];

//            if (!empty($products_finaly_arr[$product_id])) {
//                continue;
//            }
            if (!empty($product_options[$order_product_id])) {
                $product_option_id = $product_options[$order_product_id]['product_option_id'];
                $product_option_value_id = $product_options[$order_product_id]['product_option_value_id'];
                $products_finaly_arr[$product_id][$product_option_id][$product_option_value_id] = $order_product['quantity'];
            } else {
                $products_finaly_arr[$product_id] = $order_product['quantity'];
            }
        }

        $products_details = json_decode(OrderEdit::getProductLists($this->request->post), true);
        if (!empty($products_details['data']['products'])) {
            $data['add_products'] = array_combine(array_column($products_details['data']['products'], 'product_id'), $products_details['data']['products']);
            $data['handpicked_ids'] = $products_details['data']['handpicked_ids'];
            $data['page'] = $products_details['data']['page'];
            $data['path'] = $products_details['data']['path'];


            $suborder_city_exp = explode('-', $suborder_id);
            $suborder_city = $suborder_city_exp[1];
            $seller_id_arr = array_unique(array_column($products_details['data']['products'], 'seller_id'));
            $seller_ids = implode(',', $seller_id_arr);
            $seller_pickup_city = OrderEdit::getSellerPickupCityCode($this->db, $seller_ids);
            foreach ($data['add_products'] as $product_id => &$products_arr) {
                $products_arr['seller_pick_city_code'] = '';
                if (!empty($seller_pickup_city[$products_arr['seller_id']])) {
                    $products_arr['seller_pick_city_code'] = $seller_pickup_city[$products_arr['seller_id']];
                }
            }
            $data['suborder_city'] = $suborder_city;
            $data['order_product_arr'] = $products_finaly_arr;
        } else {
            $data['no_record'] = "There is no record found with the selected keyword. Please search with different keywords.";
        }

        $this->response->setOutput($this->load->view('sale/order_edit_product_info.tpl', $data));
    }

    public function addOrderProductValidation($order_id, $suborder_id, $data) {

        $suborder_city_exp = explode('-', $suborder_id);
        $suborder_city = $suborder_city_exp[1];


        $product_ids_unset_arr = array();
        $selector = array('order' => array('select' =>
                array('order_id', 'email', 'stock_transfer', 'currency_id')
            ),
            'suborder' => array('select' =>
                array('suborder_id', 'order_status_id', 'buyer_invoice_id', 'shipping_code')
            ),
            'order_product' => array('select' =>
                array('order_product_id', 'product_id', 'discount_per_piece', 'discount_breakup', 'seller_invoice_id', 'edit_type', 'quantity', 'seller_id', 'model')
            ),
            'order_option' => array(),
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        $suborder_info = $order_info['suborder'][$suborder_id];
        $order_currency_id = $order_info['order']['currency_id'];

        $order_status_check = array(1, 9, 16);
        if (!empty($suborder_info['buyer_invoice_id'])) {
            return $product_ids_unset_arr = array('p_error_msg' => "Product not added as buyer invoice is already generated. Please contact tech");
        } else if (!(in_array($suborder_info['order_status_id'], $order_status_check))) {
            return $product_ids_unset_arr = array('p_error_msg' => "Product not added as order Status not match. Please contact tech");
        }
        $product_info = array();
        $product_id_arr = array();
        if (!empty($suborder_info['order_product'])) {
            $product_info = $suborder_info['order_product'];
            $product_info = array_combine(array_column($product_info, 'order_product_id'), $product_info);
            $product_id_arr = array_unique(array_column($product_info, 'product_id'));
        }
        $product_option_info = array();
        if (!empty($suborder_info['order_option'])) {
            $product_option_info = $suborder_info['order_option'];
            $product_option_info = array_combine(array_column($product_option_info, 'order_product_id'), $product_option_info);
        }
        unset($suborder_info['order_product']);
        unset($suborder_info['order_option']);

        $this->load->model('catalog/product', 'frontend');
        $this->load->model('tool/image');
        $product_details = array();
        $product_options = array();
        $product_stock_details = array();
        $seller_id = array();
        $for_update_suborder_products = array();
        foreach ($data['product'] as $product_id => $product_arr) {
            $product_options[$product_id] = $this->frontend_model_catalog_product->getProductOptions($product_id);
            if (!empty($product_options[$product_id])) {
                $product_options[$product_id] = array_combine(array_column($product_options[$product_id], 'product_option_id'), $product_options[$product_id]);
                foreach ($product_options[$product_id] as $product_option_id => &$value) {
                    $value['product_option_value'] = array_combine(array_column($value['product_option_value'], 'product_option_value_id'), $value['product_option_value']);
                }
            }

            $product_details[$product_id] = $this->frontend_model_catalog_product->getProduct($product_id);
            if (!empty($product_details[$product_id]['image'])) {
                $product_details[$product_id]['thumb'] = $this->model_tool_image->resize($product_details[$product_id]['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $product_details[$product_id]['thumb'] = '';
            }

            $product_details[$product_id]['href'] = HTTP_CATALOG . 'index.php?route=product/product&product_id=' . $product_id;

            $product_details[$product_id]['discount_per_piece'] = 0;
            $product_details[$product_id]['discount_breakup'] = NULL;
            if (!empty($product_details[$product_id])) {
                $product_details[$product_id]['stock_status'] = '';

                if (!empty($product_arr['stock_status'])) {
                    $product_details[$product_id]['stock_status'] = $product_arr['stock_status'];
                }
            }

            if (!empty($product_details[$product_id])) {
                $product_stock_details[$product_id] = cart::getProductStockStatus($product_details[$product_id]);
            }

            if (!empty($product_arr['seller_id'])) {
                $seller_id[$product_id] = $product_arr['seller_id'];
            }
            //For already added products in suborder

            if (!empty($product_info) && in_array($product_id, $product_id_arr)) {
                foreach ($product_info as $order_product_id => $order_products) {
                    if (empty($order_products['seller_invoice_id']) && ($order_products['edit_type'] == 'YES' || $order_products['edit_type'] == 'SELLER_LATER_DISPATCH')) {
                        if ($order_products['product_id'] == $product_id) {
                            if (!empty($product_option_info[$order_product_id]) && !empty($product_arr['qty']) && is_array($product_arr['qty'])) {
                                foreach ($product_arr['qty'] as $product_option_id => $option_value_arr) {
                                    if (!empty($product_option_info[$order_product_id]['product_option_id']) && $product_option_info[$order_product_id]['product_option_id'] == $product_option_id) {
                                        foreach ($option_value_arr as $option_value_id => $option_value) {
                                            if (!empty($product_option_info[$order_product_id]['product_option_value_id']) && $product_option_info[$order_product_id]['product_option_value_id'] == $option_value_id) {
                                                $product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id][$option_value_id]['is_already_added'] = true;
                                                //$for_update_suborder_products[$product_id]['qty'][$product_option_id][$option_value_id] = $option_value;
                                                $for_update_suborder_products[$product_id]['product_details'][] = array(
                                                    'product_option_id' => $product_option_id,
                                                    'product_option_value_id' => $option_value_id,
                                                    'qty' => $option_value
                                                );
                                            }
                                        }
                                    }
                                }
                            } else {
                                $product_ids_unset_arr['products_arr'][$product_id]['is_already_added'] = true;
                                $for_update_suborder_products[$product_id]['qty'] = $product_arr['qty'];
                            }
                        }
                    }
                }
            }

            if (!empty($product_info) && !empty($product_details[$product_id]) && $data['discount_type'] == 2) {
                $dis_rate_pay_arr = array();
                $dis_rate = 0;
                $curr_dis_per_p = 0;
                $curr_dis_per = 0;
                $payment_method = '';
                $amount = 0;
                $dis_break = array();
                foreach ($product_info as $order_product_id => $order_products) {
                    if (!empty($order_products['discount_per_piece'])) {
                        $discount_breakup = unserialize($order_products['discount_breakup']);
                        if (!empty($discount_breakup)) {
                            foreach ($discount_breakup as $dis_key => $dis_value) {
                                if ($dis_key == 'paycharge') {
                                    $dis_rate_pay_arr[] = abs($dis_value['valuep']);
                                    $amount = $dis_value['amount'];
                                    $payment_method = $dis_value['payment_method'];
                                } else if ($dis_key == 'coupon') {
                                    if (empty($dis_break[$dis_key])) {
                                        $curr_dis_per_p = (-1) * ROUND(((float) $product_details[$product_id]['selling_price'] * (float) $dis_value['discount'] / 100), 2);
                                        $dis_break[$dis_key] = array(
                                            'code' => $dis_value['code'],
                                            'type' => $dis_value['type'],
                                            'discount' => $dis_value['discount'],
                                            'value' => ROUND($curr_dis_per_p, 2)
                                        );
                                    } else {
                                        continue;
                                    }
                                } else if ($dis_key == 'membership') {
                                  if (empty($dis_break[$dis_key])) {
                                    $dis_per_piece = (-1) * ROUND(((float) $product_details[$product_id]['selling_price'] * (float) $dis_value['valuep'] / 100), 2);
                                    $curr_dis_per_p += $dis_per_piece;
                                      $dis_break[$dis_key] = array(
                                          'title' => $dis_value['title'],
                                          'code' => 'membership',
                                          'valuep' => $dis_value['valuep'],
                                          'value' => ROUND($dis_per_piece, 2)
                                      );
                                  } else {
                                      continue;
                                  }
                                }
                            }
                        } else {
                            $dis_rate_pay_arr[] = 0;
                            continue;
                        }
                    }
                }
                if (!empty($dis_rate_pay_arr)) {
                    $dis_rate = MIN($dis_rate_pay_arr);
                }

                if ($dis_rate > 0) {
                    $curr_dis_per = ROUND(((float) $product_details[$product_id]['selling_price'] * (float) $dis_rate / 100), 2);
                    $dis_break['paycharge'] = array(
                        'payment_method' => $payment_method,
                        'valuep' => (-1) * $dis_rate,
                        'amount' => $amount,
                        'value' => ROUND((-1) * $curr_dis_per, 2)
                    );
                }
                $dis_break = serialize($dis_break);
                $total_discount_per_piece = abs($curr_dis_per) + abs($curr_dis_per_p);
                if ($total_discount_per_piece > 0) {
                    $total_discount_per_piece = (-1) * $total_discount_per_piece;
                }
                $product_details[$product_id]['discount_breakup'] = $dis_break;
                $product_details[$product_id]['discount_per_piece'] = ROUND((float) $total_discount_per_piece, 2);
            }
        }

        if (!empty($product_stock_details)) {
            foreach ($product_stock_details as $product_id => $st_value) {
                if (isset($st_value['stock']) && $st_value['stock'] === false) {
                    $product_ids_unset_arr['products_arr'][$product_id]['msg'] = 'Product is out of stock.';
                    $product_ids_unset_arr['products_arr'][$product_id]['p_name'] = $product_details[$product_id]['model'];
                }
            }
        }

        $seller_ids = implode(',', array_unique($seller_id));
        $seller_pickup_city_code = OrderEdit::getSellerPickupCityCode($this->db, $seller_ids);

        if (!empty($seller_id)) {
            foreach ($seller_id as $product_id => $seller) {
                if (!empty($seller_pickup_city_code[$seller]) && $seller_pickup_city_code[$seller] != $suborder_city) {
                    $product_ids_unset_arr['products_arr'][$product_id]['msg'] = 'Product belongs to different pickup city compare to this suborder.';
                    $product_ids_unset_arr['products_arr'][$product_id]['is_diff_city'] = true;
                    $product_ids_unset_arr['products_arr'][$product_id]['p_name'] = $product_details[$product_id]['model'];
                }
            }
        }


        //Validation Start

        $order_status_check = array(1, 9, 16);
        if (!empty($suborder_info['buyer_invoice_id'])) {
            echo "Buyer Invoice is already generated.";
        } else if (!(in_array($suborder_info['order_status_id'], $order_status_check))) {
            echo "Order Status not match.";
        }
        $final_product_arr = $data['product'];
        foreach ($data['product'] as $product_id => $product_arr) {
            if (!empty($product_arr['qty']) && is_array($product_arr['qty'])) {
                if (!empty($product_ids_unset_arr['products_arr'][$product_id]['is_diff_city'])) {
                    unset($final_product_arr[$product_id]);
                    continue;
                }
                foreach ($product_arr['qty'] as $product_option_id => $product_option_arr) {
                    foreach ($product_option_arr as $option_value_id => $option_value_arr) {
                        if (!empty($product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id][$option_value_id])) {
                            if (count($product_option_arr) == 1) {
                                unset($final_product_arr[$product_id]);
                                if (!empty($product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id][$option_value_id]['is_already_added'])) {
                                    unset($product_ids_unset_arr['products_arr'][$product_id]);
                                }
                                continue;
                            }
                            unset($final_product_arr[$product_id]['qty'][$product_option_id][$option_value_id]);
                            if (!empty($product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id][$option_value_id]['is_already_added'])) {
                                unset($product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id][$option_value_id]);
                            }

                            if (isset($product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id]) && empty($product_ids_unset_arr['products_arr'][$product_id]['msg'][$product_option_id])) {
                                unset($product_ids_unset_arr['products_arr'][$product_id]);
                            }
                        }
                    }
                }
            } else {
                if (!empty($product_ids_unset_arr['products_arr'][$product_id])) {
                    unset($final_product_arr[$product_id]);
                    if (!empty($product_ids_unset_arr['products_arr'][$product_id]['is_already_added'])) {
                        unset($product_ids_unset_arr['products_arr'][$product_id]);
                    }
                }
            }
        }

        $return_product_arr = array();
        if (!empty($final_product_arr)) {
            foreach ($final_product_arr as $product_id => $product_arrs) {
                if (!empty($product_details[$product_id])) {

                    $selling_price = $product_details[$product_id]['selling_price'];
                    $transfer_price = $product_details[$product_id]['price'];
                    $output_tax_rates = $product_details[$product_id]['tax_rate'];
                    $seller_tax = $product_details[$product_id]['seller_tax'];
                    //GetPrice details params
                    $price_details_param = array('price' => $product_details[$product_id]['price'],
                        'mrp' => $product_details[$product_id]['mrp'],
                        'product_id' => (int) $product_id,
                        'commission' => $product_details[$product_id]['commission'],
                        'hsn_code' => $product_details[$product_id]['hsn_code']
                    );

                    if ((float) $product_details[$product_id]['hidden_selling_price'] > 0) {
                        $price_details_param['hidden_selling_price'] = $product_details[$product_id]['hidden_selling_price'];
                    }
                    if (!empty($product_arrs['qty']) && is_array($product_arrs['qty'])) {
                        $options_arr = array();
                        foreach ($product_arrs['qty'] as $product_option_id => $product_option_arr) {

                            if (!empty($product_options[$product_id][$product_option_id])) {
                                if (!empty($product_option_arr)) {
                                    foreach ($product_option_arr as $option_value_id => $new_qty) {
                                        $option_price = 0;
                                        $updated_price = false;
                                        if (!empty($product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id])) {
                                            $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['product_option_id'] = $product_option_id;
                                            $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['tab_name'] = $product_options[$product_id][$product_option_id]['name'];
                                            $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['type'] = $product_options[$product_id][$product_option_id]['type'];

                                            $options_arr = $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id];

                                            $product_details[$product_id]['selling_price'] = $selling_price;
                                            $product_details[$product_id]['price'] = $transfer_price;
                                            $product_details[$product_id]['tax_rate'] = $output_tax_rates;
                                            $product_details[$product_id]['seller_tax'] = $seller_tax;
                                            if ((float) $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['price'] != 0) {
                                                if ($product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['price_prefix'] == '+') {
                                                    $option_price += $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['price'];
                                                } elseif ($product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['price_prefix'] == '-') {
                                                    $option_price -= $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['price'];
                                                }
                                                if ($option_price != 0) {
                                                    $price_details_param['option_price'] = $option_price;
                                                    $price_details = $this->_getUpdatedPriceOfOrder($price_details_param, $order_currency_id);

                                                    $product_details[$product_id]['selling_price'] = $price_details['selling_price'];
                                                    $product_details[$product_id]['price'] = $price_details['transfer_price_per_piece'];
                                                    $product_details[$product_id]['tax_rate'] = $price_details['output_tax_rates'];
                                                    $product_details[$product_id]['seller_tax'] = $price_details['seller_tax'];
                                                    $updated_price = true;
                                                }
                                            }

                                            //Check if hidden selling price is > 0
                                            if ((float) $product_details[$product_id]['hidden_selling_price'] > 0) {
                                                $price_details = $this->_getUpdatedPriceOfOrder($price_details_param, $order_currency_id);
                                                $product_details[$product_id]['selling_price'] = $price_details['selling_price'];
                                                $product_details[$product_id]['tax_rate'] = $price_details['output_tax_rates'];
                                                $updated_price = true;
                                            }
                                            
                                            if (!$updated_price) {
                                                $price_details = $this->_getUpdatedPriceOfOrder($price_details_param, $order_currency_id);
                                                $product_details[$product_id]['selling_price'] = $price_details['selling_price'];
                                                $product_details[$product_id]['tax_rate'] = $price_details['output_tax_rates'];
                                            }


                                            $product_details[$product_id]['customer_comment'] = '';
                                            $product_details[$product_id]['new_qty'] = $new_qty;
                                            if ($product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['quantity'] < $new_qty) {
                                                $product_details[$product_id]['new_qty'] = $product_options[$product_id][$product_option_id]['product_option_value'][$option_value_id]['quantity'];
                                            }

                                            $product_details[$product_id]['options'] = $options_arr;
                                            $product_details[$product_id]['combo_product_id'] = $product_arrs['combo_product_id'];
                                            $return_product_arr['products'][] = $product_details[$product_id];
                                        }
                                    }
                                }
                            }
                        }
                    } else {

                        // update the product price according to hidden price(if applicable) and international price factor (if applicable)
                        $price_details = $this->_getUpdatedPriceOfOrder($price_details_param, $order_currency_id);
                        $product_details[$product_id]['selling_price'] = $price_details['selling_price'];
                        $product_details[$product_id]['tax_rate'] = $price_details['output_tax_rates'];
                            
                        $product_details[$product_id]['customer_comment'] = '';
                        $product_details[$product_id]['new_qty'] = $product_arrs['qty'];
                        $product_details[$product_id]['combo_product_id'] = $product_arrs['combo_product_id'];
                        if ($product_details[$product_id]['quantity'] < $product_arrs['qty']) {
                            $product_details[$product_id]['new_qty'] = $product_details[$product_id]['quantity'];
                        }
                        $return_product_arr['products'][] = $product_details[$product_id];
                    }
                }
            }
        }

        if (!empty($for_update_suborder_products)) {
            $data_arr = array(
                'shipping_code' => $suborder_info['shipping_code'],
                'email' => $order_info['order']['email'],
                'stock_transfer' => $order_info['order']['stock_transfer'],
                'order_status_id' => $suborder_info['order_status_id'],
                'user_id' => $this->user->getId(),
                'user_name' => $this->user->getUserName($this->user->getId())['username'],
                'name' => $this->user->getUserName($this->user->getId())['name'],
                'product_info' => $product_info,
                'product_option_info' => $product_option_info,
                'discount_type' => $data['discount_type']
            );

            foreach ($for_update_suborder_products as $product_id => $product_details_arr) {
                if (!empty($product_details_arr['product_details'])) {
                    foreach ($product_details_arr['product_details'] as $key => $value) {
                        $product_details[$product_id]['new_qty'] = $value['qty'];
                        $data_arr['products'][] = $product_details[$product_id];
                    }
                } else {
                    $product_details[$product_id]['new_qty'] = $product_details_arr['qty'];
                    $data_arr['products'][] = $product_details[$product_id];
                }
            }

            OrderEdit::updateOrderProductForSuborder($this, $order_id, $suborder_id, $for_update_suborder_products, $data_arr);
        }

        if (!empty($return_product_arr)) {
            $return_product_arr['shipping_code'] = $suborder_info['shipping_code'];
            $return_product_arr['email'] = $order_info['order']['email'];
            $return_product_arr['stock_transfer'] = $order_info['order']['stock_transfer'];
            $return_product_arr['order_status_id'] = $suborder_info['order_status_id'];
            $return_product_arr['user_id'] = $this->user->getId();
            $return_product_arr['user_name'] = $this->user->getUserName($this->user->getId())['username'];
            $return_product_arr['name'] = $this->user->getUserName($this->user->getId())['name'];
            $return_product_arr['discount_type'] = $data['discount_type'];
            OrderEdit::addOrderProductItem($this, $order_id, $suborder_id, $return_product_arr);
        }
        return $product_ids_unset_arr;
    }

    /**
     * function _getUpdatedPriceOfOrder is used to get updated information of product
     * @author Nilesh. 2018
     */
    private function _getUpdatedPriceOfOrder(array $data, $order_currency_id): array {
        if (!empty($data['hidden_selling_price']) && (float) $data['hidden_selling_price'] > 0) {
            //setting cart use hidden selling price as true
            Cart::$use_hidden_selling_price = true;
        }
        
        if(method_exists($this->registry,'get')){
     			$currency = $this->registry->get('currency');
     	  }
     	  else{
     			$currency = $this->registry->currency;
     	  }
        
        $dummy_currency_id = $currency->getId(DUMMY_INR_CURRENCY);
        if ($order_currency_id == $dummy_currency_id) {
          Cart::$apply_international_price_factor = true;
        }

        $price_details = Cart::getPrice($data, $this->registry);
        return $price_details;
    }

    /**
     * Method for Delete Wrong Entry in order payment by ajax
     * @request : payment_id : Integer of payment id,
     * @request : order_id : Integer of order id,
     * @request : selected_value : string selected value  i.e. ( WRONG_ENTRY , DUPLICATE_ENTRY , MERGED_ENTRY )
     * @request : action_comment : string of action comment 
     * @return NULL
     * @author : vikas, Feb 2018
     */
    public function deletePaymentEntryInEditOrderPayment() {
        $json = array();
        $data = array(
            'payment_id' => $this->request->post['payment_id'],
            'order_id' => $this->request->post['order_id'],
            'selected_value' => $this->request->post['selected_value'],
            'action_comment' => $this->request->post['action_comment'],
            'name' => $this->user->getUserName($this->user->getId())['name'],
            'user_name' => $this->user->getUserName($this->user->getId())['username']
        );

        $validation_data = $this->validationOfEditOrderPayment($data);
        if (empty($validation_data)) {
            $return_value = OrderEdit::deletePaymentEntryInEditOrderPayment($this, $data);
            if ($return_value) {
                $json['error'] = '';
                $json['message'] = 'Data update successfully !!';
                echo json_encode($json);
                exit;
            } else {
                $json['error'] = 'error';
                $json['message'] = 'Records is not available in our database. !!';
                echo json_encode($json);
                exit;
            }
        } else {
            echo json_encode($validation_data);
            exit;
        }
    }

    /**
     * Method for Edit Trxn Mode in order payment by ajax
     * @request : payment_id : Integer of payment id,
     * @request : order_id : Integer of order id,
     * @request : selected_value : string selected value  i.e. ( WRONG_ENTRY , DUPLICATE_ENTRY , MERGED_ENTRY ),
     * @request : action_comment : string of action comment, 
     * @request : fields_data : array of fields data, 
     * @return NULL
     * @author : vikas, Feb 2018
     */
    public function editTrxnModeInEditOrderPayment() {
        $json = array();
        $data = array(
            'payment_id' => $this->request->post['payment_id'],
            'order_id' => $this->request->post['order_id'],
            'selected_value' => $this->request->post['selected_value'],
            'action_comment' => $this->request->post['action_comment'],
            'fields_data' => $this->request->post['fields_data'],
            'name' => $this->user->getUserName($this->user->getId())['name'],
            'user_name' => $this->user->getUserName($this->user->getId())['username']
        );

        $validation_data = $this->validationOfEditOrderPayment($data, false);
        if (empty($validation_data)) {

            $return_value = OrderEdit::editTrxnModeInEditOrderPayment($this, $data);
            if ($return_value) {
                $json['error'] = '';
                $json['message'] = 'Data update successfully !!';
                echo json_encode($json);
                exit;
            } else {
                $json['error'] = 'error';
                $json['message'] = 'Records is not available in our database. !!';
                echo json_encode($json);
                exit;
            }
        } else {
            echo json_encode($validation_data);
            exit;
        }
    }

    /**
     * Method for Edit Trxn Ref. in order payment by ajax
     * @request : payment_id : Integer of payment id,
     * @request : order_id : Integer of order id,
     * @request : selected_value : string selected value  i.e. ( WRONG_ENTRY , DUPLICATE_ENTRY , MERGED_ENTRY ),
     * @request : action_comment : string of action comment, 
     * @request : fields_data : array of fields data, 
     * @return NULL
     * @author : vikas, Feb 2018
     */
    public function editTrxnRefInEditOrderPayment() {
        $json = array();
        $data = array(
            'payment_id' => $this->request->post['payment_id'],
            'order_id' => $this->request->post['order_id'],
            'selected_value' => $this->request->post['selected_value'],
            'action_comment' => $this->request->post['action_comment'],
            'fields_data' => $this->request->post['fields_data']
        );

        $validation_data = $this->validationOfEditOrderPayment($data, false);
        if (empty($validation_data)) {

            $return_value = OrderEdit::editTrxnRefInEditOrderPayment($this, $data);
            if ($return_value) {
                $json['error'] = '';
                $json['message'] = 'Data update successfully !!';
                echo json_encode($json);
                exit;
            } else {
                $json['error'] = 'error';
                $json['message'] = 'Records is not available in our database. !!';
                echo json_encode($json);
                exit;
            }
        } else {
            echo json_encode($validation_data);
            exit;
        }
    }

    /**
     * Method for validation of Edit order Payment
     * @param $data : array of data i.e. payment_id, order_id, selected_value, action_comment, fields_data,
     * @param $check_buyer_invoice_generated : default value is true. this check_buyer_invoice_generated variable is used for don't check buyer invoice when using edit Trxn Ref and edit Trxn Mode.
     * @return array of json
     * @author : vikas, Feb 2018
     */
    private function validationOfEditOrderPayment($data, $check_buyer_invoice_generated = true) {
        $json = array();

        if (empty($data['payment_id']) || empty($data['selected_value']) || empty($data['action_comment'])) {
            $json['error'] = 'error';
            $json['message'] = 'You don\'t have any change in this order payment !!';
        }

        if (!(OrderEdit::checkPaymentIsSuccessfull($this, $data['payment_id']))) {
            $json['error'] = 'error';
            $json['message'] = 'This order payment is also updated. So you can\'t change.';
        }

        if ($check_buyer_invoice_generated) {
            if (OrderEdit::checkBuyerInvoiceIsGenerated($this, $data['order_id'])) {
                $json['error'] = 'error';
                $json['message'] = 'Buyer Invoice is generated. So you can\'t change.';
            }
        }

        if (OrderEdit::checkRecPayIdIsAvailable($this, $data['payment_id'])) {
            $json['error'] = 'error';
            $json['message'] = 'This order payment is verified by accounts. So you can\'t change.';
        }

        return $json;
    }

    /*
     * Funtion applyRefreshPaychargeDiscount is used to refresh paycharge discount
     *  @author: Nilesh, 2018
     */

    public function applyRefreshPaychargeDiscount($order_id, $data) {
        if (OrderEdit::refreshOrCustomPaychargeDiscount($this, $order_id, '', $data)) {
            $this->session->data['success_msg'] = "Successfully Updated.";
        } else {
            $this->session->data['error_msg'] = "Something went wrong. Please try again.";
        }
    }

    /**
     * Funtion applyCustomPaychargeDiscount is used to apply custom paycharge discount in order products
     * @author: Nilesh, 2018
     */
    public function applyCustomPaychargeDiscount($order_id, $order_product_arr, $data) {
        if (OrderEdit::refreshOrCustomPaychargeDiscount($this, $order_id, $order_product_arr, $data)) {
            $this->session->data['success_msg'] = "Successfully Updated.";
        } else {
            $this->session->data['error_msg'] = "Something went wrong. Please try again.";
        }
    }

    /**
     * Funtion filterOrderProductToCombo to filter Order product array to combo product
     * @author: Nilesh, 2018
     */
    private function _filterOrderProductToCombo($order_products) {
        $final_array = array();
        foreach ($order_products as $key => $product) {
            if ((int) $product['product_id'] === (int) $product['combo_product_id']) {
                $final_array['non_combo_products'][$product['order_product_id']] = $product;
            } else {
                $final_array['combo_products'][$product['combo_product_id']][$product['order_product_id']] = $product;
            }
        }
        return $final_array;
    }

    /**
     * Function _validateAndAddComoProductsSibling to validate and add order product combo siblings to order product array if have siblings
     * @author: Nilesh, 2018
     */
    private function _validateAndAddComoProductsSibling($order_id, $suborder_id, $order_products_id_arr, $is_seller_invoice_check = false) {

        $selector = array(
            'order_product' => array()
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        $products_info = array();
        if (!empty($order_info['suborder'][$suborder_id]['order_product'])) {
            $products_info = $order_info['suborder'][$suborder_id]['order_product'];
        }
        $order_products_combo_siblings = OrderEdit::getOrderProductComboSiblings($products_info, $is_seller_invoice_check);
        $final_array = array();
        $final_array = $order_products_id_arr;
        if (!empty($order_products_combo_siblings)) {
            foreach ($order_products_combo_siblings as $order_product_id => $combo_product) {
                if (isset($order_products_id_arr[$order_product_id])) {
                    foreach ($combo_product as $value) {
                        $final_array[$value] = $order_products_id_arr[$order_product_id];
                    }
                }
            }
        }
        return $final_array;
    }

    /**
     * Sending mails
     * @param $data array of mail details
     * @author Nilesh, 2018
     */
    public function sendMailPieceInSetMismatchWhenStockUpdate($data) {

        if (method_exists($this, 'get')) {
            $config = $this->get('config');
        } else {
            $config = $this->config;
        }
        $html = "There is a mismatch of product piece in set \n " .
                "Product Code: <b>" . $data['model'] . " </b> \n " .
                "Suborder No.: <b>" . $data['suborder_id'] . "</b> \n " .
                "Quantity: <b>" . $data['quantity'] . "</b> \n " .
                "Order Product piece in set: <b>" . $data['order_product_piece_in_set'] . "</b> \n " .
                "Product piece in set: <b>" . $data['product_piece_in_set'] . "</b>";

        $subject = 'Mismatch in product piece in set product code ' . $data['model'] . ' Suborder No. ' . $data['suborder_id'];

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
        $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
    }

    /**
     * Funtion _validateGstNumber to validate gst number 
     * @author: Nilesh, 2018
     */
    private function _validateGstNumber($customer_id, $gst_number) {

        /*         * * GST Number check starts here ** */
        $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number  = trim($gst_number);
        $error = '';
        //$this->registry->set('customer_id', $customer_id);
        $customer = new CustomerEntity( $this->registry, $customer_id );
        $valid_gst_result = $customer->gstObject->validateGSTNumber($gst_number, (int) $customer_id);
        $data = array();
        $this->load->autoLoadLanguage('sale/customer', $data);
        if (!empty(trim($gst_number)) && !($valid_gst_result['result'] === true)) {
            if ($valid_gst_result['message'] == "error_regex") {
                $error = $this->language->get('error_gst_number');
            } elseif ($valid_gst_result['message'] == "error_checksum") {
                $error = sprintf($this->language->get('error_gst_checksum'), $valid_gst_result['gst_number_details']['gst_number_without_checksum'] . "<b>" . $valid_gst_result['gst_number_details']['gst_number_checksum'] . "</b>");
            } else if ($valid_gst_result['message'] == "error_duplicate") {
                $user_str = '';
                if (!empty($valid_gst_result['duplicate_gst_number_details'])) {
                    $duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
                    if (!empty($duplicate_gst_number_customer['telephone'])) {
                        $user_str = 'mobile number <strong>' . $duplicate_gst_number_customer['telephone'] . '</strong>';
                    } else if (!empty($duplicate_gst_number_customer['email'])) {
                        $len = strlen(explode('@', $duplicate_gst_number_customer['email'])[0]);
                        $user_str = 'email <strong>' . $duplicate_gst_number_customer['email'] . '</strong>';
                    }
                    $error = sprintf($this->language->get('error_exists_gst'), $gst_number, $user_str, '<strong>' . $duplicate_gst_number_customer['customer_id'] . '</strong>');
                }
            }
        }
        /*         * * GST Number validation Ends here ** */
        return $error;
    }

    /**
     * Funtion _updateGstNumberOfOrder to update gst number on order
     * @author: Nilesh, 2018
     */
    private function _updateGstNumberOfOrder($data) {

        $order_id = (int) $data['order_id'];
        $gst_number = $data['gst_number'] ?? '';
        $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number  = trim($gst_number);

        $old_gst_number = $data['old_gst_number'];
        $this->session->data['customer_id'] = $data['customer_id'];

        //creating customer object
        //$this->registry->set('customer_id', $data['customer_id']);
        $customer = new CustomerEntity( $this->registry, $data['customer_id'] );

        //updating GST no of order
        OrderEdit::updateGstNoOfOrder($this->db, $order_id, $gst_number);

        //setting GST no of customer data
        $result = $customer->setGSTNumber($gst_number);

        //logging history
        $history = array(
            'order_id' => $order_id,
            'suborder_id' => '',
            'edit_type' => 'UPDATE_GST_NO',
            'old_value' => $old_gst_number,
            'new_value' => $gst_number,
            'comment' => '',
            'key_name' => 'order_id',
            'key_id' => $order_id,
            'field_name' => 'gst_number',
            'user_id' => $this->user->getId(),
            'name' => $this->user->getUserName($this->user->getId())['name'],
            'user_name' => $this->user->getUserName($this->user->getId())['username']
        );
        OrderEdit::saveEditHistory($this->db, $history);
    }

   /**
     * Public method to check wsb credit payment status for the order
     * @author MSA, Oct 2019
     */
    public function check_payments()
    {
        $order_id = $this->request->get['order_id'] ?? '';
        $this->load->model('sale/order');
        $isPaymentEntry = $this->model_sale_order->isOrderHasWsbCreditPaymentEntry($order_id);
        echo $isPaymentEntry;
    }

}

?>
