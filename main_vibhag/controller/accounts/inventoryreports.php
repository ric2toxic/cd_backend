<?php

class ControllerAccountsInventoryreports extends Controller {

    public function index() {

        $this->load->model('accounts/inventoryreports');

        // Language AutoLoad
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('accounts/inventoryreports', $data);

        $this->document->setTitle($data['heading_inventoryreports_title']);

        $general_url = '';
        $filter_data = array();

        $data['stock_value'] = '';
        $data['filter_date'] = '';
        if (!empty($this->request->get['filter_date'])) {
            $filter_date = $this->request->get['filter_date'];
            $filter_data['filter_date'] = $filter_date;
            $data['filter_date'] = $filter_date;
        }

        $data['error'] = false;
        if (empty($filter_data)) {
            $data['error'] = true;
        } else {
            $results = $this->model_accounts_inventoryreports->getOnlineInventories($filter_data);
            $this->_generateOnlineInventory($results);
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_inventoryreports'],
            'href' => $this->url->link('accounts/inventoryreports', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['token'] = $this->session->data['token'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/inventoryreports.tpl', $data));
    }

    /*
     * Latest code
     */

    private function _generateOnlineInventory($results) {
        $file_name = DIR_DOWNLOAD . 'online-inventory-report.csv';
        $fp = fopen($file_name, 'w');

        $data = array(
            'Order No',
            'Suborder No',
            'Seller',
            'Seller/Credit Note Inv. No',
            'Seller/Credit Note Inv. Date',
            'WSB Product Code',
            'Product ID',
            'Total Pieces',
            'Transfer Price',
            'Tax Rate',
            'Amount',
            'Taxable Amount'
        );
        fputcsv($fp, $data);

        foreach ($results as $key => $value) {
            $breakup = array($value['order_no'],
                $value['suborder_id'],
                $value['nickname'],
                $value['seller_or_credit_inv_no'],
                date('d-m-Y', strtotime($value['seller_or_credit_inv_date'])),
                $value['wsb_product_code'],
                $value['product_id'],
                $value['total_pieces'],
                $value['transfer_price_per_piece'],
                $value['input_tax_rate'],
                $value['amount'],
                $value['taxable_value']
            );
            fputcsv($fp, $breakup);
        }

        fclose($fp);
        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }
    }

    public function getTotalAmountForOnlineInventoryReport() {
        $results = array();
        if (!empty($this->request->get['filter_date'])) {
            $this->load->model('accounts/inventoryreports');
            $data = array(
                'filter_date' => $this->request->get['filter_date']
            );
            $results = $this->model_accounts_inventoryreports->getOnlineInventories($data, true);
        }
        if(empty($results)) {
            $results['error_msg'] = 'Invalid request.';
        }
        echo json_encode($results);
    }

}
