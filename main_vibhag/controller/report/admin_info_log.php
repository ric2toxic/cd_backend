<?php
class ControllerReportAdminInfoLog extends Controller {
    public function index() {

        $data = array();
        //autoloading the language
        $this->load->autoLoadLanguage('report/admin_info_log',$data);

        $this->load->model('report/performance');
        $this->load->model('report/admin_info_log');

        $this->document->setTitle($data['heading_admin_info_log_title']);


        if (isset($this->request->get['filter_field_type'])) {
            $filter_field_type = $this->request->get['filter_field_type'];
        } else {
            $filter_field_type = null;
        }

        if (isset($this->request->get['filter_field_value'])) {
            $filter_field_value = $this->request->get['filter_field_value'];
        } else {
            $filter_field_value = null;
        }

        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = null;
        }

        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = null;
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_user_group'])) {
            $filter_user_group = $this->request->get['filter_user_group'];
        } else {
            $filter_user_group = null;
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_field_type'])) {
            $url .= '&filter_field_type=' . $this->request->get['filter_field_type'];
        }

        if (isset($this->request->get['filter_field_value'])) {
            $url .= '&filter_field_value=' . $this->request->get['filter_field_value'];
        }

        if (isset($this->request->get['filter_date_from'])) {
            $url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
        }

        if (isset($this->request->get['filter_date_to'])) {
            $url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
        }

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        if (isset($this->request->get['filter_user_group'])) {
            $url .= '&filter_user_group=' . $this->request->get['filter_user_group'];
        }


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_admin_info_log'],
            'href' => $this->url->link('report/admin_info_log', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );


        $data['all_field_type'] = $this->model_report_admin_info_log->getAllFieldType();
        $data['all_user_group'] = $this->model_report_admin_info_log->getAllUserGroup();
        

        $data['records'] = array();

        $filter_data = array(
            'filter_field_type'     => $filter_field_type,
            'filter_field_value'    => $filter_field_value,
            'filter_date_from'      => $filter_date_from,
            'filter_date_to'        => $filter_date_to,
            'filter_name'           => $filter_name,
            'filter_user_group'     => $filter_user_group,
            'start'                 => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                 => $this->config->get('config_limit_admin')
        );



        $records = $this->model_report_admin_info_log->getAmdminLogInfoDetails($filter_data);
        // $total_records = 0;
        $total_records = $this->model_report_admin_info_log->getTotalAmdminLogInfo($filter_data);

        if($records){

            foreach($records as $record){
                $data['records'][] = array(
                    'field_name' => ucwords($record['field_name']),
                    'new_value'=> ucwords($record['new_value']),
                    'username'   => ucwords($record['username']),
                    'name'       => ucwords($record['name']),
                    'user_group' => ucwords($record['user_type']),
                    'total'      => $record['total'],
                );
            }           
        }

        $pagination = new Pagination();
        $pagination->total = $total_records;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('report/performance/', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($data['text_pagination'], ($total_records) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_records - $this->config->get('config_limit_admin'))) ? $total_records : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_records, ceil($total_records / $this->config->get('config_limit_admin')));

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $data['filter_field_type']  = $filter_field_type;
        $data['filter_field_value'] = $filter_field_value;
        $data['filter_date_from']   = $filter_date_from;
        $data['filter_date_to']     = $filter_date_to;
        $data['filter_name']        = $filter_name;
        $data['filter_user_group']  = $filter_user_group;


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/admin_info_log_list.tpl', $data));

    }
}
