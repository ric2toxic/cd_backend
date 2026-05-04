<?php
class ControllerPopularsearchPopularsearch extends Controller{
    private $error = array();

    public function index(){
        $this->load->language('popularsearch/popularsearch');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('popularsearch/popularsearch');

        $this->getList();
    }

    public function add(){
        $this->load->language('popularsearch/popularsearch');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('popularsearch/popularsearch');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_popularsearch_popularsearch->addPopularTag($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            $this->response->redirect($this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getForm();
    }

    public function edit(){
        $this->load->language('popularsearch/popularsearch');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('popularsearch/popularsearch');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_popularsearch_popularsearch->editPopularTag($this->request->get['popular_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            $this->response->redirect($this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete(){
        $this->load->language('popularsearch/popularsearch');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('popularsearch/popularsearch');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $popularsearch_id) {
                $this->model_popularsearch_popularsearch->deleteWordSynonyms($popularsearch_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            $this->response->redirect($this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    /**
     * get full information
     */
    protected function getList() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('popularsearch/popularsearch', $data);

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
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
            'href' => $this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('popularsearch/popularsearch/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('popularsearch/popularsearch/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $filter_data = array(
            'filter_name'	  => $filter_name,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        //$this->load->model('tool/image');

        $popularsearch_total = $this->model_popularsearch_popularsearch->getTotalPopularSearch($filter_data);

        $results = $this->model_popularsearch_popularsearch->getPopularTags($filter_data);

        foreach ($results as $result) {

            $data['popularsearch'][] = array(
                'popular_id'    => $result['popular_id'],
                'popular_search'   => $result['popular_search'],
                'edit'          => $this->url->link('popularsearch/popularsearch/edit', 'token=' . $this->session->data['token'] . '&popular_id=' . $result['popular_id'] . $url, 'SSL')
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

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $popularsearch_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($popularsearch_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($popularsearch_total - $this->config->get('config_limit_admin'))) ? $popularsearch_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $popularsearch_total, ceil($popularsearch_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('popularsearch/popularsearch_list.tpl', $data));
    }

    protected function getForm() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('popularsearch/popularsearch', $data);
        $data['heading_title'] = $data['heading_title'];

        $data['text_form']          = !isset($this->request->get['popularsearch_id']) ? $data['text_add'] : $data['text_edit'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
            'href' => $this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['popular_id'])) {
            $data['action'] = $this->url->link('popularsearch/popularsearch/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('popularsearch/popularsearch/edit', 'token=' . $this->session->data['token'] . '&popular_id=' . $this->request->get['popular_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('popularsearch/popularsearch', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['popular_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $popular_search_info = $this->model_popularsearch_popularsearch->getPopularTagWithID($this->request->get['popular_id']);
        }

        $data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['popular_search'])) {
            $data['popular_search'] = $this->request->post['popular_search'];
        } elseif (!empty($popular_search_info)) {
            $data['popular_search'] = $popular_search_info['popular_search'];
        } else {
            $data['popular_search'] = '';
        }

        if (isset($this->request->post['popular_link'])) {
            $data['popular_link'] = $this->request->post['popular_link'];
        } elseif (!empty($popular_search_info)) {
            $data['popular_link'] = $popular_search_info['link'];
        } else {
            $data['popular_link'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('popularsearch/popularsearch_form.tpl', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'popularsearch/popularsearch')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'popularsearch/popularsearch')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}