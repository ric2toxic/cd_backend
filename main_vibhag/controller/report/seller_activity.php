<?php
class ControllerReportSellerActivity extends Controller {
    public function index() {
        //$this->load->language('report/seller_activity');
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('report/seller_activity', $data);
        $this->document->setTitle($data['heading_title']);

        if (isset($this->request->get['filter_product'])) {
            $filter_product = $this->request->get['filter_product'];
        } else {
            $filter_product = null;
        }

        if (isset($this->request->get['filter_nickname'])) {
            $filter_nickname = $this->request->get['filter_nickname'];
        } else {
            $filter_nickname = null;
        }

        if (isset($this->request->get['filter_date'])) {
            $filter_date = $this->request->get['filter_date'];
        } else {
            $filter_date = '';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . urlencode($this->request->get['filter_product']);
        }

        if (isset($this->request->get['filter_nickname'])) {
            $url .= '&filter_nickname=' . $this->request->get['filter_nickname'];
        }

        if (isset($this->request->get['filter_date'])) {
            $url .= '&filter_date=' . $this->request->get['filter_date'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->load->model('report/customer');
        $data['activities'] = array();

        $filter_data = array(
            'filter_product'   => $filter_product,
            'filter_nickname'         => $filter_nickname,
            'filter_date'	=> $filter_date,
            'start'             => ($page - 1) * 20,
            'limit'             => 20
        );

        $activity = $this->model_report_customer->getSellerActivities($filter_data);
        $activity_total = $this->model_report_customer->getTotalSellerActivities($filter_data);

        foreach($activity as $result){
            $data['activities'][] = array(
                'nickname'             => $result['nickname'],
                'updated_type'         => $result['updated_type'],
                'product_id'           => $result['product'],
                'modified'           => $result['modified']
            );
        }

        $data['filter_product'] = $filter_product;
        $data['filter_seller'] = $filter_nickname;
        $data['filter_date'] = $filter_date;
        $data['token'] = $this->session->data['token'];

        $pagination = new Pagination();
        $pagination->total = $activity_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('report/seller_activity', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($activity_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($activity_total - $this->config->get('config_limit_admin'))) ? $activity_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $activity_total, ceil($activity_total / $this->config->get('config_limit_admin')));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/seller_activity.tpl', $data));
    }

    }