<?php

class ControllerModuleStoreReview extends Controller
{
    private $error = array();

    public function index()
    {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('module/storereview', $data);       

        $this->document->setTitle($data['heading_title']);

        $this->load->model('extension/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_extension_module->addModule('storereview', $this->request->post);
            } else {
                $this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $data['text_success'];

            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if (file_exists($fn = 'http://vanstudio.co.ua/index.php?route=module/fb_sr_lite/module'))
        {
            $json = @file_get_contents($fn);
            if($json) {
                $info = json_decode($json);
                $data['text_dev_message'] = $info->message;
            }
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['text_module'],
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('module/storereview', 'token=' . $this->session->data['token'], 'SSL')
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('module/storereview', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('module/storereview', 'token=' . $this->session->data['token'], 'SSL');
        } else {
            $data['action'] = $this->url->link('module/storereview', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
        }

        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['order'])) {
            $data['order'] = $this->request->post['order'];
        } elseif (!empty($module_info)) {
            $data['order'] = $module_info['order'];
        } else {
            $data['order'] = '';
        }

        $data['token'] = $this->session->data['token'];


        if (isset($this->request->post['layout_id'])) {
            $data['layout_id'] = $this->request->post['layout_id'];
        } elseif (!empty($module_info)) {
            $data['layout_id'] = $module_info['layout_id'];
        } else {
            $data['layout_id'] = '';
        }

        if (isset($this->request->post['limit'])) {
            $data['limit'] = $this->request->post['limit'];
        } elseif (!empty($module_info)) {
            $data['limit'] = $module_info['limit'];
        } else {
            $data['limit'] = '3';
        }

        if (isset($this->request->post['text_limit'])) {
            $data['text_limit'] = $this->request->post['text_limit'];
        } elseif (!empty($module_info)) {
            $data['text_limit'] = $module_info['text_limit'];
        } else {
            $data['text_limit'] = '100';
        }

        if (isset($this->request->post['button_all'])) {
            $data['button_all'] = $this->request->post['button_all'];
        } elseif (!empty($module_info)) {
            $data['button_all'] = $module_info['button_all'];
        } else {
            $data['button_all'] = '1';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '1';
        }

        if (isset($this->request->post['module_description'])) {
            $data['module_description'] = $this->request->post['module_description'];
        } elseif (!empty($module_info)) {
            $data['module_description'] = $module_info['module_description'];
        } else {
            $data['module_description'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/storereview.tpl', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/storereview')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('review/store_review');

        $this->model_review_store_review->install();
    }

    public function uninstall() {
        $this->load->model('review/store_review');
        $this->model_review_store_review->uninstall();
    }
}