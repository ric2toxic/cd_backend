<?php
class ControllerDictionarySearchDictionary extends Controller{
    private $error = array();

    public function index(){
        $this->load->language('dictionary/search_dictionary');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('dictionary/search_dictionary');

        $this->getList();
    }

    public function add(){
        $this->load->language('dictionary/search_dictionary');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('dictionary/search_dictionary');
        //$this->load->model('catalog/filter');
        //echo "<pre>";print_r($this->request->server['REQUEST_METHOD']); echo "</pre>"; die;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            //echo "<prE>"; print_r($this->request->post); echo "</pre>"; die;
            //$this->model_catalog_product->dynamicmetatags($this->request->post);

            $this->model_dictionary_search_dictionary->addWordSynonyms($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            /*if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }*/
            $this->response->redirect($this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getForm();
    }

    public function edit(){
        $this->load->language('dictionary/search_dictionary');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('dictionary/search_dictionary');
        //echo "<pre>";print_r($this->request->server['REQUEST_METHOD']); echo "</pre>"; die;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            //$this->model_catalog_product->dynamicmetatags($this->request->post);
            $this->model_dictionary_search_dictionary->editWordSynonyms($this->request->get['dictionary_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            /*if (isset($this->request->get['filter_status'])) {
                  $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }*/


            $this->response->redirect($this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete(){
        $this->load->language('dictionary/search_dictionary');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('dictionary/search_dictionary');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $dictionary_id) {
                $this->model_dictionary_search_dictionary->deleteWordSynonyms($dictionary_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            /*if (isset($this->request->get['filter_status'])) {
                  $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }*/

            $this->response->redirect($this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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
        
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('dictionary/search_dictionary', $data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('dictionary/search_dictionary/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('dictionary/search_dictionary/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $filter_data = array(
            'filter_name'	  => $filter_name,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        //$this->load->model('tool/image');

        $dictionary_total = $this->model_dictionary_search_dictionary->getTotalWordSynonyms($filter_data);

        $results = $this->model_dictionary_search_dictionary->getWordSynonyms($filter_data);

        foreach ($results as $result) {

            $data['dictionary'][] = array(
                'id'            => $result['id'],
                'word'          => $result['word'],
                'word_synonyms' => $result['synonyms'],
                'edit'          => $this->url->link('dictionary/search_dictionary/edit', 'token=' . $this->session->data['token'] . '&dictionary_id=' . $result['id'] . $url, 'SSL')
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

/*
        $data['sort_job_type'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=job_type' . $url, 'SSL');
        $data['sort_model'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
        $data['sort_price'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
        $data['sort_commission'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=p.commission' . $url, 'SSL');
        $data['sort_quantity'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');
        */

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $dictionary_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($dictionary_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($dictionary_total - $this->config->get('config_limit_admin'))) ? $dictionary_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $dictionary_total, ceil($dictionary_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('dictionary/search_dictionary.tpl', $data));
    }

    protected function getForm() {
        //echo "<prE>"; print_r($this->request->get); echo "</pre>"; die;
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('dictionary/search_dictionary', $data);

        $data['text_form']      = !isset($this->request->get['dictionary_id']) ? $data['text_add'] : $data['text_edit'];

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
            'href' => $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['dictionary_id'])) {
            $data['action'] = $this->url->link('dictionary/search_dictionary/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('dictionary/search_dictionary/edit', 'token=' . $this->session->data['token'] . '&dictionary_id=' . $this->request->get['dictionary_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('dictionary/search_dictionary', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['dictionary_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $job_info = $this->model_dictionary_search_dictionary->getWordSynonymsWithID($this->request->get['dictionary_id']);
        }

        $data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['word'])) {
            $data['word'] = $this->request->post['word'];
        } elseif (!empty($job_info)) {
            $data['word'] = $job_info['word'];
        } else {
            $data['word'] = '';
        }

        if (isset($this->request->post['word_synonyms'])) {
            $data['word_synonyms'] = $this->request->post['word_synonyms'];
        } elseif (!empty($job_info)) {
            $data['word_synonyms'] = $job_info['synonyms'];
        } else {
            $data['word_synonyms'] = '';
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('dictionary/search_dictionary_form.tpl', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'dictionary/search_dictionary')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

//        foreach ($this->request->post['dictionary_description'] as $language_id => $value) {
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
        if (!$this->user->hasPermission('modify', 'dictionary/search_dictionary')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}