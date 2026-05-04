<?php
require(DIR_SYSTEM . 'library/seller_agreement/seller_agreement.php');
class ControllerSellerAgreementSellerAgreement extends Controller {
    private $error = array();

    public function index(){

        $data = array();
        $this->load->autoLoadLanguage('seller_agreement/seller_agreement',$data);

        $this->document->setTitle($data['heading_title']);

        $this->getList();
    }

    public function add() {
        $data = array();
        $this->load->autoLoadLanguage('seller_agreement/seller_agreement',$data);

        $this->document->setTitle($data['heading_title']);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() ) {

            $seller_agreement = new SellerAgreement( $this );

            $this->request->post['user'] = $this->user->getUserName()['username'];

            $seller_agreement->addAgreement($this->request->post);

            $this->session->data['success'] = $data['text_success'];

            $url = '';

            if (isset($this->request->get['filter_clause_type'])) {
                $url .= '&filter_clause_type=' . urlencode(html_entity_decode($this->request->get['filter_clause_type'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_clause_version'])) {
                $url .= '&filter_clause_version=' . urlencode(html_entity_decode($this->request->get['filter_clause_version'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_clause_status'])) {
                $url .= '&filter_clause_status=' . urlencode(html_entity_decode($this->request->get['filter_clause_status'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_clause_date_added'])) {
                $url .= '&filter_clause_date_added=' . urlencode(html_entity_decode($this->request->get['filter_clause_date_added'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('seller_agreement/seller_agreement', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm(false);
    }

    public function edit() {

        $data = array();
        $this->load->autoLoadLanguage('seller_agreement/seller_agreement',$data);

        $this->document->setTitle($data['heading_title']);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() ) {

            $seller_agreement = new SellerAgreement( $this );

            $this->request->post['user'] = $this->user->getUserName()['username'];

            $seller_agreement->editAgreement($this->request->get['agreement_id'], $this->request->post);


            $this->session->data['success'] = $data['text_success'];

            $url = '';

            if (isset($this->request->get['filter_clause_type'])) {
                $url .= '&filter_clause_type=' . urlencode(html_entity_decode($this->request->get['filter_clause_type'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_clause_version'])) {
                $url .= '&filter_clause_version=' . urlencode(html_entity_decode($this->request->get['filter_clause_version'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_clause_status'])) {
                $url .= '&filter_clause_status=' . urlencode(html_entity_decode($this->request->get['filter_clause_status'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_clause_date_added'])) {
                $url .= '&filter_clause_date_added=' . urlencode(html_entity_decode($this->request->get['filter_clause_date_added'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('seller_agreement/seller_agreement', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }


        $this->getForm(true);
    }

    public function getList(){
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('seller_agreement/seller_agreement', $data);

        if (!empty($this->request->get['filter_clause_type'])) {
            $filter_clause_type = $this->request->get['filter_clause_type'];
        } else {
            $filter_clause_type = '';
        }

        if (!empty($this->request->get['filter_clause_version'])) {
            $filter_clause_version = $this->request->get['filter_clause_version'];
        } else {
            $filter_clause_version = '';
        }

        if (isset($this->request->get['filter_clause_status'])) {
            $filter_clause_status = $this->request->get['filter_clause_status'];
        } else {
            $filter_clause_status = 1;
        }

        if (!empty($this->request->get['filter_clause_date_added'])) {
            $filter_clause_date_added = $this->request->get['filter_clause_date_added'];
            
        } else {
            $filter_clause_date_added = '';
        }

        if (!empty($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'date_added';
        }

        if (!empty($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (!empty($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }


        // General URL (without sort or page)
        $url = '';

        if (!empty($this->request->get['filter_clause_type'])) {
            $url .= '&filter_clause_type=' . urlencode(html_entity_decode($this->request->get['filter_clause_type'], ENT_QUOTES, 'UTF-8'));
        }

        if (!empty($this->request->get['filter_clause_version'])) {
            $url .= '&filter_clause_version=' . urlencode(html_entity_decode($this->request->get['filter_clause_version'], ENT_QUOTES, 'UTF-8'));
        }

        if (!empty($this->request->get['filter_clause_status'])) {
            $url .= '&filter_clause_status=' . urlencode(html_entity_decode($this->request->get['filter_clause_status'], ENT_QUOTES, 'UTF-8'));
        }

        if (!empty($this->request->get['filter_clause_date_added'])) {
            $url .= '&filter_clause_date_added=' . urlencode(html_entity_decode($this->request->get['filter_clause_date_added'], ENT_QUOTES, 'UTF-8'));
        }

        
        // URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;


        if (!empty($this->request->get['sort'])) {
            $general_url .= '&sort=' . $this->request->get['sort'];
        }

        if (!empty($this->request->get['order'])) {
            $general_url .= '&order=' . $this->request->get['order'];
        }

        if (!empty($this->request->get['page'])) {
            $general_url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('seller_agreement/seller_agreement', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['add'] = $this->url->link('seller_agreement/seller_agreement/add', 
                                        'token=' . $this->session->data['token'] . 
                                        $general_url, 
                                        'SSL');

        $data['sellers_agreement'] = array();

        $filter_data = array(
            'filter_clause_type'       => $filter_clause_type,
            'filter_clause_version'    => $filter_clause_version,
            'filter_clause_status'     => $filter_clause_status,
            'filter_clause_date_added' => $filter_clause_date_added,
            'sort'                     => $sort,
            'order'                    => $order,
            'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                    => $this->config->get('config_limit_admin')
        );



        $seller_agreement_obj = new SellerAgreement( $this );

        $results_total = $seller_agreement_obj->getTotalSellerAgreements($filter_data);
        $results = $seller_agreement_obj->getSellerAgreements($filter_data);        

        foreach ($results as $result) {

            $data['sellers_agreement'][] = array(
                'clause_type'   => $result['clause_type'],
                'clause_version'=> $result['clause_version'],
                'clause_content'=> $result['clause_content'],
                'date_added'    => date('d-m-Y',strtotime($result['date_added'])),
                'user'          => $result['user'],
                'status'        => ( $result['status'] ) ? 'Active' : 'Inactive',
                'edit'          => $this->url->link('seller_agreement/seller_agreement/edit', 
                                                   'token=' . $this->session->data['token'] . 
                                                   '&agreement_id=' . $result['agreement_id'] . 
                                                   $general_url, 
                                                   'SSL'),
            );
        }

        $data['token'] = $this->session->data['token'];

        if (!empty($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (!empty($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }


        $pagination_url = $url ;

        if (!empty($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (!empty($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $results_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('seller_agreement/seller_agreement', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($results_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($results_total - $this->config->get('config_limit_admin'))) ? $results_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $results_total, ceil($results_total / $this->config->get('config_limit_admin')));

        $data['filter_clause_type'] = $filter_clause_type;
        $data['filter_clause_version'] = $filter_clause_version;
        $data['filter_clause_status'] = $filter_clause_status;
        $data['filter_clause_date_added'] = $filter_clause_date_added;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('seller_agreement/agreement_list.tpl',$data));
    }

    protected function getForm($edit_seller = false) {

        $seller_agreement_obj = new SellerAgreement( $this );

        $data = array();
        $this->load->autoLoadLanguage('seller_agreement/seller_agreement',$data);
      
        $data['text_form'] = !isset($this->request->get['agreement_id']) ? $data['text_add'] : $data['text_edit'];
        $data['token'] = $this->session->data['token'];

        $data['error_warning'] = '';
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }

        if (isset($this->error['clause_type'])) {
            $data['error_warning'] = $this->error['clause_type'];
        }

        if (isset($this->error['clause_version'])) {
            $data['error_warning'] = $this->error['clause_version'];
        }
        
        if (isset($this->error['clause_content'])) {
            $data['error_warning'] = $this->error['clause_content'];
        }
        
        $url = '';

        if (isset($this->request->get['filter_clause_type'])) {
            $url .= '&filter_clause_type=' . urlencode(html_entity_decode($this->request->get['filter_clause_type'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_clause_version'])) {
            $url .= '&filter_clause_version=' . urlencode(html_entity_decode($this->request->get['filter_clause_version'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_clause_status'])) {
            $url .= '&filter_clause_status=' . urlencode(html_entity_decode($this->request->get['filter_clause_status'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_clause_date_added'])) {
            $url .= '&filter_clause_date_added=' . urlencode(html_entity_decode($this->request->get['filter_clause_date_added'], ENT_QUOTES, 'UTF-8'));
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
            'href' => $this->url->link('seller_agreement/seller_agreement', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['agreement_id'])) {
            $data['action'] = $this->url->link('seller_agreement/seller_agreement/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('seller_agreement/seller_agreement/edit', 'token=' . $this->session->data['token'] . '&agreement_id=' . $this->request->get['agreement_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('seller_agreement/seller_agreement', 'token=' . $this->session->data['token'] . $url, 'SSL');


        if (isset($this->request->get['agreement_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $agreement_info = $seller_agreement_obj->getSellerAgreement($this->request->get['agreement_id']);
        }

        if (isset($this->request->post['clause_type'])) {
            $data['clause_type'] = $this->request->post['clause_type'];
        } elseif (!empty($agreement_info)) {
            $data['clause_type'] = $agreement_info['clause_type'];
        } else {
            $data['clause_type'] = '';
        }

        if (isset($this->request->post['clause_version'])) {
            $data['clause_version'] = $this->request->post['clause_version'];
        } elseif (!empty($agreement_info)) {
            $data['clause_version'] = $agreement_info['clause_version'];
        } else {
            $data['clause_version'] = '';
        }

        if (isset($this->request->post['clause_status'])) {
            $data['clause_status'] = $this->request->post['clause_status'];
        } elseif (!empty($agreement_info)) {
            $data['clause_status'] = $agreement_info['status'];
        } else {
            $data['clause_status'] = '1';
        }

        if (isset($this->request->post['clause_content'])) {
            $data['clause_content'] = $this->request->post['clause_content'];
        } elseif (!empty($agreement_info)) {
            $data['clause_content'] = $agreement_info['clause_content'];
        } else {
            $data['clause_content'] = '';
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('seller_agreement/agreement_form.tpl', $data));
    } 

    protected function validateForm() {
        $data = array();
        $this->load->autoLoadLanguage('seller_agreement/seller_agreement',$data);
        
        if (!$this->user->hasPermission('modify', 'seller_agreement/seller_agreement')) {
            $this->error['warning'] = $data['error_permission'];
        }

        if(empty($this->request->post['clause_type'])){
            $this->error['clause_type'] = $data['error_clause_type'];
        }

        if(empty($this->request->post['clause_version'])){
            $this->error['clause_version'] = $data['error_clause_version'];
        }

        if(empty(($this->request->post['clause_content']))){
          $this->error['clause_content'] = $data['error_clause_content'];
        }

        return !$this->error;
    }
  
}