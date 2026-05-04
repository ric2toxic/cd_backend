<?php
class ControllerAccountsSellerpayments extends Controller {
    public function index() {

        $this->load->language('accounts/sellerpayments');
        $this->load->model('accounts/sellerpayments');

        $this->document->setTitle($this->language->get('heading_sellerpayment_title'));
        $data['heading_sellerpayment'] = $this->language->get('heading_sellerpayment');
        $data['heading_sellerpayment_list'] = $this->language->get('heading_sellerpayment_list');
        
        $data['get_sellers_invoice_payment'] = $this->model_accounts_sellerpayments->getSellerInvoicePayment();
        
        foreach($data['get_sellers_invoice_payment'] as $seller_id) {
            $data['get_seller_order_payment'] = $this->model_accounts_sellerpayments->getSellerInvoicePayment($seller_id['seller_id']);
        }
        
        $data['seller_debit_note_payment'] = $this->model_accounts_sellerpayments->getSellerDebitNotePayment();

//echo "<pre>"; print_r($data); die;

        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_sellerpayment'),
            'href' => $this->url->link('accounts/sellerpayments', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/sellerpayments.tpl', $data));
    }
}