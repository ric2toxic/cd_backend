<?php
class ControllerReportCustomer extends Controller {

    public function getOrderStats() {
        $customer_id = $this->request->get['customer_id'] ?? 0;
        if (empty($customer_id)) {
            return "Error: Customer Id is incorrect";
        }

        $data = array();

        $this->load->model('report/customer');
        $data['order_stats'] = $this->model_report_customer->getOrderStats($customer_id);
        $data['total_order_link'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_customer_id=' . $data['order_stats']['all_related_customer_ids'] , 'SSL');
        $data['token'] = $this->session->data['token'];

        $this->response->setOutput($this->load->view('report/order_stats.tpl', $data));
    }
}