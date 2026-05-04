<?php
class ControllerJobsJobsOpening extends Controller{
    private $error = array();

    public function index(){
        $this->load->language('jobs/jobs_opening');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('jobs/jobs_opening');

        $this->getList();
    }

    public function add(){
        $this->load->language('jobs/jobs_opening');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('jobs/jobs_opening');
        //$this->load->model('catalog/filter');
        //echo "<pre>";print_r($this->request->server['REQUEST_METHOD']); echo "</pre>"; die;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            //echo "<prE>"; print_r($this->request->post); echo "</pre>"; die;
            //$this->model_catalog_product->dynamicmetatags($this->request->post);

            $this->model_jobs_jobs_opening->addJobPost($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            /*if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }*/
            $this->response->redirect($this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getForm();
    }

    public function edit(){
        $this->load->language('jobs/jobs_opening');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('jobs/jobs_opening');
        //echo "<pre>";print_r($this->request->server['REQUEST_METHOD']); echo "</pre>"; die;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            //echo "<prE>"; print_r($this->request->post); echo "</pre>"; die;
            //$this->model_catalog_product->dynamicmetatags($this->request->post);
            $this->model_jobs_jobs_opening->editJobPost($this->request->get['jobs_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            /*if (isset($this->request->get['filter_status'])) {
                  $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }*/


            $this->response->redirect($this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete(){
        $this->load->language('jobs/jobs_opening');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('jobs/jobs_opening');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $job_id) {
                $this->model_jobs_jobs_opening->deleteJob($job_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            /*if (isset($this->request->get['filter_status'])) {
                  $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }*/

            $this->response->redirect($this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    /**
     * get full information
     */
    protected function getList() {

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }


        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }


        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('jobs/jobs_opening', $data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('jobs/jobs_opening/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('jobs/jobs_opening/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['products'] = array();
//echo "<pre>"; print_r($this->request->get); echo "</pre>"; die;
        $filter_data = array(
            'filter_name'	  => $filter_name,
            'filter_status'   => $filter_status,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        //$this->load->model('tool/image');

        $jobs_total = $this->model_jobs_jobs_opening->getTotalJobs($filter_data);

        $results = $this->model_jobs_jobs_opening->getJobs($filter_data);

        foreach ($results as $result) {

            $data['jobs'][] = array(
                'job_id'        => $result['job_id'],
                'title'         => $result['title'],
                'job_type'      => $result['job_type'],
                'description'   => $result['description'],
                'start_date'    => $result['start_date'],
                'close_date'    => $result['end_date'],
                'location'      => $result['location'],
                'email_to'      => $result['email_to'],
                'status'        => ($result['status']) ? $data['text_enabled'] : $data['text_disabled'],
                'edit'          => $this->url->link('jobs/jobs_opening/edit', 'token=' . $this->session->data['token'] . '&jobs_id=' . $result['job_id'] . $url, 'SSL')
            );
        }

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

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_commission'])) {
            $url .= '&filter_commission=' . $this->request->get['filter_commission'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_non_single'])) {
            $url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_job_type'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=job_type' . $url, 'SSL');
        $data['sort_model'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
        $data['sort_price'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
        $data['sort_commission'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=p.commission' . $url, 'SSL');
        $data['sort_quantity'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_commission'])) {
            $url .= '&filter_commission=' . $this->request->get['filter_commission'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_non_single'])) {
            $url .= '&filter_non_single=' . $this->request->get['filter_non_single'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $jobs_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($jobs_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($jobs_total - $this->config->get('config_limit_admin'))) ? $jobs_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $jobs_total, ceil($jobs_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
        $data['filter_status'] = $filter_status;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('jobs/jobs_opening.tpl', $data));
    }

    protected function getForm() {
        //echo "<prE>"; print_r($this->request->get); echo "</pre>"; die;

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('jobs/jobs_opening', $data);

        $data['text_form']      = !isset($this->request->get['jobs_id']) ? $data['text_add'] : $data['text_edit'];
        $data['entry_set_description'] = $this->language->get('entry_set_description');
        $data['entry_description'] = $this->language->get('entry_description');

//        $data['button_attribute_add'] = $this->language->get('button_attribute_add');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['jobs_id'])) {
            $data['action'] = $this->url->link('jobs/jobs_opening/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('jobs/jobs_opening/edit', 'token=' . $this->session->data['token'] . '&jobs_id=' . $this->request->get['jobs_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('jobs/jobs_opening', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['jobs_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $job_info = $this->model_jobs_jobs_opening->getJob($this->request->get['jobs_id']);
        }

        $data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['job_title'])) {
            $data['title'] = $this->request->post['job_title'];
        } elseif (!empty($job_info)) {
            $data['title'] = $job_info['title'];
        } else {
            $data['title'] = '';
        }


        if (isset($this->request->post['job_type'])) {
            $data['job_type'] = $this->request->post['job_type'];
        } elseif (!empty($job_info)) {
            $data['job_type'] = $job_info['job_type'];
        } else {
            $data['job_type'] = '';
        }

        if (isset($this->request->post['job_description'])) {
            $data['description'] = $this->request->post['job_description'];
        } elseif (!empty($job_info)) {
            $data['description'] = $job_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['job_start_date'])) {
            $data['start_date'] = $this->request->post['job_start_date'];
        } elseif (!empty($job_info)) {
            $data['start_date'] = $job_info['start_date'];
        } else {
            $data['start_date'] = '';
        }

        if (isset($this->request->post['job_close_date'])) {
            $data['end_date'] = $this->request->post['job_close_date'];
        } elseif (!empty($job_info)) {
            $data['end_date'] = $job_info['end_date'];
        } else {
            $data['end_date'] = '';
        }

        if (isset($this->request->post['job_location'])) {
            $data['location'] = $this->request->post['job_location'];
        } elseif (!empty($job_info)) {
            $data['location'] = $job_info['location'];
        } else {
            $data['location'] = '';
        }

        if (isset($this->request->post['job_email_to'])) {
            $data['email_to'] = $this->request->post['job_email_to'];
        } elseif (!empty($job_info)) {
            $data['email_to'] = $job_info['email_to'];
        } else {
            $data['email_to'] = '';
        }        
        if (isset($this->request->post['job_input_image'])) {
            $data['job_icon'] = $this->request->post['job_input_image'];
        } elseif (!empty($job_info)) {
            $data['job_icon'] = $job_info['job_icon'];
        } else {
            $data['job_icon'] = '';
        }

        if (isset($this->request->post['job_status'])) {
            $data['status'] = $this->request->post['job_status'];
        } elseif (!empty($job_info)) {
            $data['status'] = $job_info['status'];
        } else {
            $data['status'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('jobs/jobs_opening_form.tpl', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'jobs/jobs_opening')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

//        foreach ($this->request->post['jobs_description'] as $language_id => $value) {
//            if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 510)) {
//                $this->error['name'][$language_id] = $this->language->get('error_name');
//            }
//
//            if ((utf8_strlen($value['meta_title']) < 0) || (utf8_strlen($value['meta_title']) > 255)) {
//                $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
//            }
//        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'jobs/jobs_opening')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}