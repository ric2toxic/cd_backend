<?php
require_once(DIR_SYSTEM . 'library/cart.php');
class ControllerSellersSellers extends Controller {
    private $error = array();
    private $_seller_profile;

    public function __construct($registry){
        parent::__construct($registry);
        $this->_seller_profile = new SellerProfile( $this );
    }

    public function index(){
        $this->load->language('sellers/sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sellers/sellers');

        $this->getList();
    }

    public function add() {
        //$this->load->language('sellers/sellers');
        $data = array();
        $this->load->autoLoadLanguage('sellers/sellers',$data);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sellers/sellers');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            //automatic generate password
            $this->request->post['seller_password'] = substr(mt_rand(), 0, 6);

            $add_seller = $this->_seller_profile->addSeller( $this->request->post );

            if( $add_seller['status'] ){

                $this->session->data['success'] = $this->language->get('text_success');

                $url = '';

                if (isset($this->request->get['filter_seller'])) {
                    $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_company'])) {
                    $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_email'])) {
                    $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_telephone'])) {
                    $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_vacation'])) {
                    $url .= '&filter_vacation=' . (int)$this->request->get['filter_vacation'];
                }

                if (isset($this->request->get['filter_seller_status'])) {
                    $url .= '&filter_seller_status=' . (int)$this->request->get['filter_seller_status'];
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


                $this->SellerAccountEmail($this->request->post['seller_email'],
                                          $this->request->post['seller_password'],
                                          $this->request->post['seller_company'],
                                          $this->request->post['seller_additional_email']);

                $this->response->redirect($this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            } elseif( !$add_seller['status'] ) {
                $this->error['warning'] = $add_seller['message'];
            }
        }

        $this->getForm(false);
    }

    public function edit() {

        $this->load->language('sellers/sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sellers/sellers');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['changes_data'] = $_POST['changes_data'];

            $this->_seller_profile->editSeller( $this->request->get['seller_id'], $this->request->post );
            
            //update seller promotion
            if(count($this->request->post['promotion']) > 0){
                $this->updateSellerPromotion($this->request->get['seller_id'],$this->request->post['promotion']);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_seller'])) {
                $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_company'])) {
                $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_telephone'])) {
                $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_vacation'])) {
                $url .= '&filter_vacation=' . (int)$this->request->get['filter_vacation'];
            }

            if (isset($this->request->get['filter_seller_status'])) {
                $url .= '&filter_seller_status=' . (int)$this->request->get['filter_seller_status'];
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

            if(isset($this->request->post['seller_password']) && !empty($this->request->post['seller_password'])){
                $this->SellerPasswordChangeEmail($this->request->post['seller_email'],
                                                $this->request->post['seller_password'],
                                                $this->request->post['seller_company'],
                                                $this->request->post['seller_additional_email']);
            }

            $this->response->redirect($this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        
        $this->getForm(true);
    }

    public function deleteSeller() {
        $this->load->language('sellers/sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sellers/sellers');

        if (isset($this->request->get['seller_id']) && $this->validateDelete()) {
           // $this->model_sellers_sellers->deleteSeller($this->request->get['seller_id']);

            $this->_seller_profile->deleteSeller( $this->request->get['seller_id'] );

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_seller'])) {
                $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_company'])) {
                $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_telephone'])) {
                $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_vacation'])) {
                $url .= '&filter_vacation=' . (int)$this->request->get['filter_vacation'];
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

            $this->response->redirect($this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function deleteCompletely() {
        $this->load->language('sellers/sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/customer');

        if (isset($this->request->get['seller_id']) && $this->validateDelete()) {
            $this->model_sale_customer->deleteCustomer($this->request->get['seller_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_seller'])) {
                $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_company'])) {
                $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_telephone'])) {
                $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_vacation'])) {
                $url .= '&filter_vacation=' . (int)$this->request->get['filter_vacation'];
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

            $this->response->redirect($this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function getList(){
        $data = array();
        $this->load->autoLoadLanguage('sellers/sellers', $data);
        $this->load->model('localisation/zone');

        $data['filter_sale'] = 0;
        if (isset($this->request->get['filter_sale'])) {
            $data['filter_sale'] = 1;
        }

        $filter_seller              = $this->request->get['filter_seller'] ?? NULL;
        $filter_company             = $this->request->get['filter_company'] ?? NULL;
        $filter_email               = $this->request->get['filter_email'] ?? NULL;
        $filter_sale                = $this->request->get['filter_sale'] ?? NULL;
        $filter_telephone           = $this->request->get['filter_telephone'] ?? NULL;
        $filter_seller_id           = $this->request->get['filter_seller_id'] ?? NULL;
        $filter_vacation            = $this->request->get['filter_vacation'] ?? NULL;
        $filter_rating              = $this->request->get['filter_rating'] ?? NULL;
        $filter_seller_status       = $this->request->get['filter_seller_status'] ?? NULL;
        $filter_product_name_prefix = $this->request->get['filter_product_name_prefix'] ?? NULL;
        $filter_city                = $this->request->get['filter_city'] ?? NULL;
        $filter_state               = $this->request->get['filter_state'] ?? NULL;
        
        $sort   = $this->request->get['sort'] ?? 'ms.date_created';
        $order  = $this->request->get['order'] ?? 'DESC'; 
        $page   = $this->request->get['page'] ?? 1;
        
        // General URL (without sort or page)
        $url = '';

        if (isset($this->request->get['filter_seller'])) {
            $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_company'])) {
            $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_sale'])) {
            $url .= '&filter_sale=' . urlencode(html_entity_decode($this->request->get['filter_sale'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_telephone'])) {
            $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_seller_id'])) {
            $url .= '&filter_seller_id=' . urlencode(html_entity_decode($this->request->get['filter_seller_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_vacation'])) {
            $url .= '&filter_vacation=' . urlencode(html_entity_decode($this->request->get['filter_vacation'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_rating'])) {
            $url .= '&filter_rating=' . urlencode(html_entity_decode($this->request->get['filter_rating'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_seller_status'])) {
            $url .= '&filter_seller_status=' . urlencode(html_entity_decode($this->request->get['filter_seller_status'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product_name_prefix'])) {
            $url .= '&filter_product_name_prefix=' . urlencode(html_entity_decode($this->request->get['filter_product_name_prefix'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_city'])) {
            $url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_state'])) {
            $url .= '&filter_state=' . urlencode(html_entity_decode($this->request->get['filter_state'], ENT_QUOTES, 'UTF-8'));
        }

        // URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;


        if (isset($this->request->get['sort'])) {
            $general_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $general_url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $general_url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['add'] = $this->url->link('sellers/sellers/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
        $data['delete'] = $this->url->link('sellers/sellers/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');


        $data['sellers'] = array();

        $filter_data = array(
            'filter_seller'              => $filter_seller,
            'filter_company'             => $filter_company,
            'filter_email'               => $filter_email,
            'filter_sale'                => $filter_sale,
            'filter_telephone'           => $filter_telephone,
            'filter_seller_id'           => $filter_seller_id,
            'filter_vacation'            => $filter_vacation,
            'filter_rating'              => $filter_rating,
            'filter_seller_status'       => $filter_seller_status,
            'filter_product_name_prefix' => $filter_product_name_prefix,
            'filter_city'                => $filter_city,
            'filter_state'               => $filter_state,
            'sort'                       => $sort,
            'order'                      => $order,
            'start'                      => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                      => $this->config->get('config_limit_admin')
        );

        $results       = $this->_seller_profile->getSellers($filter_data);
        $results_total = $this->_seller_profile->getTotalSellers($filter_data);

        $this->load->model('sale/customer');
        $this->load->model('sellers/sellers');

        foreach ($results as $result) {
            //$clearance_sale = $this->model_sellers_sellers->getSellerClearanceSaleStatus($result['seller_id']);

            $clearance_sale = $this->_seller_profile->getSellerClearanceSaleStatus($result['seller_id']);
            if (!empty($clearance_sale)) {
               $sale = 'on sale';
            } else {
               $sale = '';
            }
            $data['sellers'][] = array(
                'seller_id'    => $result['seller_id'],
                'name'         => $result['seller'],
                'nick_name'    => $result['nickname'],
                'company'      => $result['company'],
                'product_name_prefix' => $result['product_name_prefix'],
                'prefix_mode'  => $result['prefix_mode'],
                'email'        => $result['email'],
                'telephone'    => $result['telephone'],
                'seller_id'    => $result['seller_id'],
                'date_added'   => $result['date_created'],
                'product_total'=> $result['product_total'],
                'status'       => $result['seller_status'],
                'edit'         => $this->url->link('sellers/sellers/edit',
                                                   'token=' . $this->session->data['token'] .
                                                   '&seller_id=' . $result['seller_id'] .
                                                   $general_url,
                                                   'SSL'),
                'seller_login' => $this->url->link('sale/customer/login',
                                                   'token=' . $this->session->data['token'] .
                                                   '&customer_id=' . $result['seller_id'] .
                                                   '&store_id=0',
                                                   'SSL'),
                'deleteSeller' => $this->url->link('sellers/sellers/deleteSeller',
                                                   'token=' . $this->session->data['token'] .
                                                   '&seller_id=' . $result['seller_id'] .
                                                   $general_url,
                                                   'SSL'),
                'deleteCompletely'=>$this->url->link('sellers/sellers/deleteCompletely',
                                                     'token=' . $this->session->data['token'] .
                                                     '&seller_id=' . $result['seller_id'] .
                                                     $general_url,
                                                     'SSL'),
                'seller_vacation' => $result['vacation_mode'],
                'seller_total_rating' => $result['seller_total_rating'],
                'on_sale'      => $sale,
                'exclusive'      => $result['exclusive'],
                'sor_terms'      => $this->model_sellers_sellers->getSellersSorTerms($result['seller_id'])
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

        if (isset($this->request->post['checked_selected'])) {
            $data['checked_selected'] = (array)$this->request->post['checked_selected'];
        } else {
            $data['checked_selected'] = array();
        }


        $sorting_url = $url;

        if ($order == 'ASC') {
            $sorting_url .= '&order=DESC';
        } else {
            $sorting_url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $sorting_url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_seller_name']= $this->url->link('sellers/sellers',
                                                     'token=' . $this->session->data['token'] .
                                                     '&sort=ms.nickname' .
                                                     $sorting_url,
                                                     'SSL');
        $data['sort_company']    = $this->url->link('sellers/sellers',
                                                 'token=' . $this->session->data['token'] .
                                                 '&sort=ms.company' .
                                                 $sorting_url,
                                                 'SSL');
        $data['sort_email']      = $this->url->link('sellers/sellers',
                                                    'token=' . $this->session->data['token'] .
                                                    '&sort=c.email' .
                                                    $sorting_url,
                                                    'SSL');
        $data['sort_telephone']  = $this->url->link('sellers/sellers',
                                                    'token=' . $this->session->data['token'] .
                                                    '&sort=c.telephone' .
                                                    $sorting_url,
                                                    'SSL');
        $data['sort_status']     = $this->url->link('sellers/sellers',
                                                    'token=' . $this->session->data['token'] .
                                                    '&sort=ms.seller_status' .
                                                    $sorting_url,
                                                    'SSL');
        $data['sort_date_added'] = $this->url->link('sellers/sellers',
                                                    'token=' . $this->session->data['token'] .
                                                    '&sort=ms.date_created' .
                                                    $sorting_url,
                                                    'SSL');
        $data['sort_product_no'] = $this->url->link('sellers/sellers',
                                                    'token=' . $this->session->data['token'] .
                                                    '&sort=product_total' .
                                                    $sorting_url,
                                                    'SSL');


        $pagination_url = $url ;

        if (isset($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }

        $pagination        = new Pagination();
        $pagination->total = $results_total;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url   = $this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($results_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($results_total - $this->config->get('config_limit_admin'))) ? $results_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $results_total, ceil($results_total / $this->config->get('config_limit_admin')));

        $data['filter_seller']              = $filter_seller;
        $data['filter_company']             = $filter_company;
        $data['filter_email']               = $filter_email;
        $data['filter_sale']                = $filter_sale;
        $data['filter_telephone']           = $filter_telephone;
        $data['filter_seller_id']           = $filter_seller_id;
        $data['filter_vacation']            = $filter_vacation;
        $data['filter_rating']              = $filter_rating;
        $data['filter_seller_status']       = $filter_seller_status;
        $data['filter_product_name_prefix'] = $filter_product_name_prefix;
        $data['filter_city']                = $filter_city;
        $data['filter_state']               = $filter_state;
        $data['sort']                       = $sort;
        $data['order']                      = $order;

        $data['zone'] = $this->model_localisation_zone->getZonesByCountryId(99); // india states
        
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        $data['header']      = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('sellers/sellers_list.tpl',$data));
    }

    protected function getForm($edit_seller = false) {

        //$seller_profile = new SellerProfile( $this );

        $this->load->model('localisation/country');
        $this->load->model('catalog/category');

        $data = array();
        $this->load->autoLoadLanguage('sellers/sellers',$data);


        $data['edit_seller'] = $edit_seller;
        $data['text_form']   = !isset($this->request->get['seller_id']) ? $data['text_add'] : $data['text_edit'];
        $data['token']       = $this->session->data['token'];

        $data['seller_id']                     = $this->request->get['seller_id'] ?? 0;
        $data['error_warning']                 = $this->error['warning'] ?? '';
        $data['error_seller_nickname']         = $this->error['seller_nickname'] ?? '';
        $data['error_seller_firmname']         = $this->error['seller_firmname'] ?? '';
        $data['error_product_name_prefix']     = $this->error['error_product_name_prefix'] ?? '';
        $data['error_seller_telephone']        = $this->error['seller_telephone'] ?? '';
        $data['error_seller_email']            = $this->error['seller_email'] ?? '';
        $data['error_firm_address']            = $this->error['seller_firm_address'] ?? '';
        $data['error_city']                    = $this->error['city'] ?? '';
        $data['error_pincode']                 = $this->error['pincode'] ?? '';
        $data['error_pickup_address']          = $this->error['pickup_address'] ?? '';
        $data['error_pickup_city']             = $this->error['pickup_city'] ?? '';
        $data['error_pickup_pincode']          = $this->error['pickup_pincode'] ?? '';
        $data['error_primary_contact_name']    = $this->error['primary_contact_name'] ?? '';
        $data['error_primary_contact_no']      = $this->error['primary_contact_no'] ?? '';
        $data['error_pickup_holder_name']      = $this->error['pickup_holder_name'] ?? '';
        $data['error_pickup_contact_no']       = $this->error['pickup_contact_no'] ?? '';
        $data['error_account_holder_name']     = $this->error['account_holder_name'] ?? '';
        $data['error_account_contact_no']      = $this->error['account_contact_no'] ?? '';
        $data['error_inventory_holder_name']   = $this->error['inventory_holder_name'] ?? '';
        $data['error_inventory_contact_no']    = $this->error['inventory_contact_no'] ?? '';
        $data['error_pan']                     = $this->error['pan'] ?? '';
        $data['error_bank_ac_holder_name']     = $this->error['bank_ac_holder_name'] ?? '';
        $data['error_bank_ac_number']          = $this->error['bank_ac_number'] ?? '';
        $data['error_ifsc_code']               = $this->error['ifsc_code'] ?? '';
        $data['error_app_only']                = $this->error['error_app_only'] ?? '';
        $data['error_service_area']            = $this->error['error_service_area'] ?? '';
        $data['error_pickup_city_code']        = $this->error['error_pickup_city_code'] ?? '';
        $data['error_seller_gstin_validation'] = $this->error['error_seller_gstin_validation'] ?? '';
        $data['error_distinct_category']       = $this->error['distinct_category'] ?? '';
        $data['error_promotion']               = $this->error['promotion'] ?? array();
        
        $url = '';

        if (isset($this->request->get['filter_seller'])) {
            $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_company'])) {
            $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_telephone'])) {
            $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_vacation'])) {
            $url .= '&filter_vacation=' . urlencode(html_entity_decode($this->request->get['filter_vacation'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_seller_status'])) {
            $url .= '&filter_seller_status=' . urlencode(html_entity_decode($this->request->get['filter_seller_status'], ENT_QUOTES, 'UTF-8'));
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
            'href' => $this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['seller_id'])) {
            $data['action'] = $this->url->link('sellers/sellers/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('sellers/sellers/edit', 'token=' . $this->session->data['token'] . '&seller_id=' . $this->request->get['seller_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('sellers/sellers', 'token=' . $this->session->data['token'] . $url, 'SSL');
       
            
            if(!empty($this->request->get['seller_id'])){
                $data['show_password_box'] = true;
                $seller_info = $this->_seller_profile->getSeller($this->request->get['seller_id']);
            }else if(!empty($this->request->post['seller_id'])){                
                $data['show_password_box'] = true;
                $seller_info = $this->_seller_profile->getSeller($this->request->post['seller_id']);
            }else{
                $data['show_password_box'] = false;
                $seller_info = array();
            }
            
           
        if(!empty($this->request->get['seller_id'])){
            $seller_info = $this->_seller_profile->getSeller($this->request->get['seller_id']);
        }else if(!empty($this->request->post['seller_id'])){                
            $seller_info = $this->_seller_profile->getSeller($this->request->post['seller_id']);
        }else{                
            $seller_info = array();
        }

        if (isset($this->request->post['seller_nickname'])) {
            $data['nickname'] = $this->request->post['seller_nickname'];
        } elseif (!empty($seller_info)) {
            $data['nickname'] = ( !is_numeric($seller_info['nickname']) ) ? $seller_info['nickname'] : '';
        } else {
            $data['nickname'] = '';
        }

        if (isset($this->request->post['seller_firmname'])) {
            $data['seller_firmname'] = $this->request->post['seller_firmname'];
        } elseif (!empty($seller_info)) {
            $data['seller_firmname'] = $seller_info['company'];
        } else {
            $data['seller_firmname'] = '';
        }

        $data['product_name_prefix'] = $this->request->post['product_name_prefix'] ?? $seller_info['product_name_prefix'] ?? '';
        $data['prefix_mode'] = $this->request->post['prefix_mode'] ?? $seller_info['prefix_mode'] ?? '';

        if (isset($this->request->post['seller_telephone'])) {
            $data['seller_telephone'] = $this->request->post['seller_telephone'];
        } elseif (!empty($seller_info)) {
            $data['seller_telephone'] = $seller_info['telephone'];
        } else {
            $data['seller_telephone'] = '';
        }

        if (isset($this->request->post['seller_email'])) {
            $data['seller_email'] = $this->request->post['seller_email'];
        } elseif (!empty($seller_info)) {
            $data['seller_email'] = $seller_info['email'];
        } else {
            $data['seller_email'] = '';
        }

        if (isset($this->request->post['seller_additional_email'])) {
            $data['seller_additional_email'] = $this->request->post['seller_additional_email'];
        } elseif (!empty($seller_info)) {
            $data['seller_additional_email'] = $seller_info['additional_email'];
        } else {
            $data['seller_additional_email'] = '';
        }

        if (isset($this->request->post['seller_password'])) {
            $data['password'] = $this->request->post['seller_password'];
        } else {
            $data['password'] = '';
        }

        if (isset($this->request->post['seller_firm_address'])) {
            $data['seller_firm_address'] = $this->request->post['seller_firm_address'];
        } elseif (!empty($seller_info)) {
            $data['seller_firm_address'] = $seller_info['address1'];
            if(!empty($seller_info['address2'])){
                $data['seller_firm_address'] .= ', '.$seller_info['address2'];
            }
        } else {
            $data['seller_firm_address'] = '';
        }

        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } elseif (!empty($seller_info)) {
            $data['city'] = $seller_info['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['pincode'])) {
            $data['pincode'] = $this->request->post['pincode'];
        } elseif (!empty($seller_info)) {
            $data['pincode'] = $seller_info['pincode'];
        } else {
            $data['pincode'] = '';
        }

        if (isset($this->request->post['country_id'])) {
            $data['country_id'] = $this->request->post['country_id'];
        } elseif (!empty($seller_info)) {
            $data['country_id'] = $seller_info['country_id'];
        } else {
            $data['country_id'] = '';
        }

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (!empty($seller_info)) {
            $data['zone_id'] = $seller_info['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        if (isset($this->request->post['pickup_address'])) {
            $data['pickup_address'] = $this->request->post['pickup_address'];
        } elseif (!empty($seller_info)) {
            $data['pickup_address'] = $seller_info['pickup_address'];
        } else {
            $data['pickup_address'] = '';
        }

        if (isset($this->request->post['pickup_city'])) {
            $data['pickup_city'] = $this->request->post['pickup_city'];
        } elseif (!empty($seller_info)) {
            $data['pickup_city'] = $seller_info['pickup_city'];
        } else {
            $data['pickup_city'] = '';
        }

        if (isset($this->request->post['pickup_pincode'])) {
            $data['pickup_pincode'] = $this->request->post['pickup_pincode'];
        } elseif (!empty($seller_info)) {
            $data['pickup_pincode'] = $seller_info['pickup_pincode'];
        } else {
            $data['pickup_pincode'] = '';
        }

        if (isset($this->request->post['pickup_country'])) {
            $data['pickup_country_id'] = $this->request->post['pickup_country'];
        } elseif (!empty($seller_info)) {
            $data['pickup_country_id'] = $seller_info['pickup_country_id'];
        } else {
            $data['pickup_country_id'] = '';
        }

        if (isset($this->request->post['pickup_zone'])) {
            $data['pickup_zone_id'] = $this->request->post['pickup_zone'];
        } elseif (!empty($seller_info)) {
            $data['pickup_zone_id'] = $seller_info['pickup_zone_id'];
        } else {
            $data['pickup_zone_id'] = '';
        }

        if (isset($this->request->post['pickup_city_code'])) {
            $data['pickup_city_code'] = $this->request->post['pickup_city_code'];
        } elseif (!empty($seller_info)) {
            $data['pickup_city_code'] = $seller_info['pickup_city_code'];
        } else {
            $data['pickup_city_code'] = '';
        }

        $data['comment_on'] = false;
        if (isset($this->request->post['seller_status'])) {
            $data['seller_status'] = $this->request->post['seller_status'];
            //used for show additional detail
            $data['comment_on'] = true;
        } elseif (!empty($seller_info)) {
            $data['seller_status'] = $seller_info['seller_status'];
        } else {
            $data['seller_status'] = '';
        }

        if (isset($this->request->post['sor_enable_checkbox'])) {
            $data['sor_enable_checkbox'] = $this->request->post['sor_enable_checkbox'];
        } elseif (!empty($seller_info)) {
            $data['sor_enable_checkbox'] = $seller_info['sor_enabled'];
        } else {
            $data['sor_enable_checkbox'] = '';
        }

        if (isset($this->request->post['seller_markup'])) {
            $data['seller_markup'] = $this->request->post['seller_markup'];
        } elseif (!empty($seller_info)) {
            $data['seller_markup'] = $seller_info['seller_markup_over_tp'];
        } else {
            $data['seller_markup'] = '';
        }

        if (isset($this->request->post['wsb_commission'])) {
            $data['wsb_commission'] = $this->request->post['wsb_commission'];
        } elseif (!empty($seller_info)) {
            $data['wsb_commission'] = $seller_info['wsb_commission_over_tp'];
        } else {
            $data['wsb_commission'] = '';
        }

        if (isset($this->request->post['pan'])) {
            $data['pan'] = $this->request->post['pan'];
        } elseif (!empty($seller_info)) {
            $data['pan'] = $seller_info['pan'];
        } else {
            $data['pan'] = '';
        }

        if (isset($this->request->post['tin'])) {
            $data['tin'] = $this->request->post['tin'];
        } elseif (!empty($seller_info)) {
            $data['tin'] = $seller_info['tin'];
        } else {
            $data['tin'] = '';
        }

        if (isset($this->request->post['gst_arn'])) {
            $data['gst_arn'] = $this->request->post['gst_arn'];
        } elseif (!empty($seller_info)) {
            $data['gst_arn'] = $seller_info['gst_arn'] ;
        } else {
            $data['gst_arn'] = '';
        }

        if (isset($this->request->post['gst_provisional_id'])) {
            $data['gst_provisional_id'] = $this->request->post['gst_provisional_id'];
        } elseif (!empty($seller_info)) {
            $data['gst_provisional_id'] = $seller_info['gst_provisional_id'];
        } else {
            $data['gst_provisional_id'] = '';
        }

        if (!empty($this->request->post['changes_data'])) {
            $data['changes_data'] = $this->request->post['changes_data'];
        } else {
            $data['changes_data'] = '';
        }        

        // tin taxs type Taxable, Tax free, Composite
        $data['tin_taxs_type'] = array();
        $tin_tax_type = array('1'=>'Tax Free', '2'=>'Taxable', '3'=>'Composite');
        if (!empty($seller_info)) {
            $tin_taxs = $seller_info['tin_tax_type'];
            $tin_tax_array = array();
            if(!empty($tin_taxs)){
                $tin_taxs = explode(',', $tin_taxs);
                foreach($tin_taxs as $tin_tax_val){
                    $tin_tax_array[] = $tin_tax_type[$tin_tax_val];
                }
                $data['tin_taxs_type'] = $tin_tax_array;
            }
        }

        if (isset($this->request->post['bank_ac_holder_name'])) {
            $data['bank_ac_holder_name'] = $this->request->post['bank_ac_holder_name'];
        } elseif (!empty($seller_info)) {
            $data['bank_ac_holder_name'] = $seller_info['bank_ac_holder_name'];
        } else {
            $data['bank_ac_holder_name'] = '';
        }

        if (isset($this->request->post['bank_ac_number'])) {
            $data['bank_ac_number'] = $this->request->post['bank_ac_number'];
        } elseif (!empty($seller_info)) {
            $data['bank_ac_number'] = $seller_info['bank_ac_number'];
        } else {
            $data['bank_ac_number'] = '';
        }

        if (isset($this->request->post['ifsc_code'])) {
            $data['ifsc_code'] = $this->request->post['ifsc_code'];
        } elseif (!empty($seller_info)) {
            $data['ifsc_code'] = $seller_info['ifsc_code'];
        } else {
            $data['ifsc_code'] = '';
        }

        if (isset($this->request->post['primary_contact_name'])) {
            $data['primary_contact_name'] = $this->request->post['primary_contact_name'];
        } elseif (!empty($seller_info)) {
            $data['primary_contact_name'] = $seller_info['primary_contact_name'];
        } else {
            $data['primary_contact_name'] = '';
        }

        if (isset($this->request->post['primary_contact_no'])) {
            $data['primary_contact_no'] = $this->request->post['primary_contact_no'];
        } elseif (!empty($seller_info)) {
            $data['primary_contact_no'] = $seller_info['primary_contact_no'];
        } else {
            $data['primary_contact_no'] = '';
        }

        if (isset($this->request->post['pickup_holder_name'])) {
            $data['pickup_holder_name'] = $this->request->post['pickup_holder_name'];
        } elseif (!empty($seller_info)) {
            $data['pickup_holder_name'] = $seller_info['pickup_holder_name'];
        } else {
            $data['pickup_holder_name'] = '';
        }

        if (isset($this->request->post['pickup_contact_no'])) {
            $data['pickup_contact_no'] = $this->request->post['pickup_contact_no'];
        } elseif (!empty($seller_info)) {
            $data['pickup_contact_no'] = $seller_info['pickup_contact_no'];
        } else {
            $data['pickup_contact_no'] = '';
        }

        if (isset($this->request->post['account_holder_name'])) {
            $data['account_holder_name'] = $this->request->post['account_holder_name'];
        } elseif (!empty($seller_info)) {
            $data['account_holder_name'] = $seller_info['account_holder_name'];
        } else {
            $data['account_holder_name'] = '';
        }

        if (isset($this->request->post['account_contact_no'])) {
            $data['account_contact_no'] = $this->request->post['account_contact_no'];
        } elseif (!empty($seller_info)) {
            $data['account_contact_no'] = $seller_info['account_contact_no'];
        } else {
            $data['account_contact_no'] = '';
        }

        if (isset($this->request->post['inventory_holder_name'])) {
            $data['inventory_holder_name'] = $this->request->post['inventory_holder_name'];
        } elseif (!empty($seller_info)) {
            $data['inventory_holder_name'] = $seller_info['inventory_holder_name'];
        } else {
            $data['inventory_holder_name'] = '';
        }

        if (isset($this->request->post['inventory_contact_no'])) {
            $data['inventory_contact_no'] = $this->request->post['inventory_contact_no'];
        } elseif (!empty($seller_info)) {
            $data['inventory_contact_no'] = $seller_info['inventory_contact_no'];
        } else {
            $data['inventory_contact_no'] = '';
        }

        if(!empty($this->request->post)){
            if(isset($this->request->post['same_as_primary_address'])){
                $data['same_as_primary_address'] = $this->request->post['same_as_primary_address'];    
            } else{
                $data['same_as_primary_address'] = 0;    
            }

            if (isset($this->request->post['same_as_primary_pickup'])) {
                $data['same_as_primary_pickup'] = $this->request->post['same_as_primary_pickup'];
            } else {
                $data['same_as_primary_pickup'] = 0;
            }

            if (isset($this->request->post['same_as_primary_account'])) {
                $data['same_as_primary_account'] = $this->request->post['same_as_primary_account'];
            } else {
                $data['same_as_primary_account'] = 0;
            }

            if (isset($this->request->post['same_as_primary_inventory'])) {
                $data['same_as_primary_inventory'] = $this->request->post['same_as_primary_inventory'];
            } else {
                $data['same_as_primary_inventory'] = 0;
            }
        }
        else{
            if(!empty($seller_info)){
                $data['same_as_primary_address'] = $seller_info['same_as_primary_address'];
                $data['same_as_primary_pickup'] = $seller_info['same_as_primary_pickup']; 
                $data['same_as_primary_account'] = $seller_info['same_as_primary_account'];
                $data['same_as_primary_inventory'] = $seller_info['same_as_primary_inventory'];   
            } else {
                $data['same_as_primary_address'] = 0;
                $data['same_as_primary_pickup'] = 0; 
                $data['same_as_primary_account'] = 0;
                $data['same_as_primary_inventory'] = 0;  
            }
            
        }

        if (isset($this->request->post['additional_details'])) {
            $data['additional_details'] = $this->request->post['additional_details'];
        }else{
            $data['additional_details'] = '';
        }

        $data['seller_app_only'] = '';
        if (!isset($this->error['error_app_only'])) {
            if (isset($this->request->post['seller_app_only'])) {
                $data['seller_app_only'] = $this->request->post['seller_app_only'];
            } elseif (!empty($seller_info)) {
                $data['seller_app_only'] = $seller_info['app_only'];
            }
        }    
        
        if (isset($this->request->post['non_serviceable_areas'])) {
            $data['non_serviceable_areas'] = $this->request->post['non_serviceable_areas'];
        } elseif (!empty($seller_info)) {
            $data['non_serviceable_areas'] = $seller_info['non_serviceable_areas'];
        } else {
            $data['non_serviceable_areas'] = '';
        }

        $data['seller_non_returnable'] = $this->request->post['seller_non_returnable'] ?? $seller_info['non_returnable'] ?? '';
        
        $data['countries'] = $this->model_localisation_country->getCountries();

        $data['categories'] = array();
        $categories_1 = $this->model_catalog_category->getCategoriesByParent(0);
        foreach ($categories_1 as $category_1) {
            $level_2_data = array();

            $categories_2 = $this->model_catalog_category->getCategoriesByParent($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $level_3_data = array();

                $categories_3 = $this->model_catalog_category->getCategoriesByParent($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $level_3_data[] = array(
                        'category_id' => $category_3['category_id'],
                        'name'        => $category_3['name'],
                    );
                }

                $level_2_data[] = array(
                    'category_id' => $category_2['category_id'],
                    'name'        => $category_2['name'],
                    'children'    => $level_3_data
                );
            }

            $data['categories'][] = array(
                'category_id' => $category_1['category_id'],
                'name'        => $category_1['name'],
                'children'    => $level_2_data
            );
        }


        // Category Rating
        if (isset($this->request->post['category_rating'])) {
            $data['category_rating'] = $this->request->post['category_rating'];
        } elseif (isset($this->request->get['seller_id'])) {
            $data['category_rating'] = $this->_seller_profile->getSellerCategoryRating($this->request->get['seller_id']);
        } else {
            $data['category_rating'] = array();
        }

        // Category Global Review
        if (isset($this->request->post['categories_global_review'])) {
            $data['categories_global_review'] = $this->request->post['categories_global_review'];
        } elseif (isset($this->request->get['seller_id'])) {
            $data['categories_global_review'] = $this->_seller_profile->getSellerGlobalRating($this->request->get['seller_id']);
        } else {
            $data['categories_global_review'] = array();
        }


        // for show history (change log)
        $data['change_log'] = array();
        $data['pending_verification'] = array();
        if( !empty($this->request->get['seller_id']) ) {
             $seller_update_activity = $this->_seller_profile->getSellerUpdateActivity( $this->request->get['seller_id'] );

            if( $seller_update_activity ) {
                foreach( $seller_update_activity as $seller_updates ) {
                    $zones = array();
                    if( $seller_updates['update_type'] == 'zone_id' || ($seller_updates['update_type'] == 'pickup_zone_id' || $seller_updates['update_type'] == 'pickup_zone') ){
                        $zones['new_value'] = $seller_updates['new_value'];
                        $zones['previous_value'] = $seller_updates['previous_value'];
                        $zones = $this->_seller_profile->getZoneName($zones);
                    }
                    if( !empty($zones) ){
                        $seller_updates['new_value'] = $zones[$seller_updates['new_value']];
                        if( !empty($seller_updates['previous_value']) ) {
                            $seller_updates['previous_value'] = $zones[$seller_updates['previous_value']];
                        }    
                    }

                    // tin taxs type Taxable, Tax free, Composite
                    $new_tin_tax_array = array();
                    $previous_tin_tax_array = array();
                    if( $seller_updates['update_type'] == 'tin_tax_type' ) {
                       $new_tin_tax_type = explode(',',$seller_updates['new_value']);

                        if (!empty($seller_info)) {
                            $new_tin_taxs = $seller_updates['new_value'];
                            if(!empty($new_tin_taxs)){
                                $new_tin_taxs = explode(',', $new_tin_taxs);
                                foreach($new_tin_taxs as $tin_tax_val){
                                    $new_tin_tax_array[] = $tin_tax_type[$tin_tax_val];
                                }

                                $seller_updates['new_value'] = implode(',', $new_tin_tax_array);
                            }
                            if(!empty($seller_updates['previous_value'])) { 
                                $previous_tin_taxs = $seller_updates['previous_value'];
                                if(!empty($previous_tin_taxs)){
                                    $previous_tin_taxs = explode(',', $previous_tin_taxs);
                                    foreach($previous_tin_taxs as $tin_tax_val){
                                        $previous_tin_tax_array[] = $tin_tax_type[$tin_tax_val];
                                    }

                                    $seller_updates['previous_value'] = implode(',', $previous_tin_tax_array);
                                }
                            }    
                        }
                    }

                    $data['change_log'][] = array(
                        'update_id'         => $seller_updates['update_id'],
                        'update_group'      => $seller_updates['update_group'],
                        'update_type'       => $seller_updates['update_type'],
                        'new_value'         => ($seller_updates['new_value']) ? $seller_updates['new_value'] : '--NONE--',
                        'date_added'        => date('d-m-Y',strtotime($seller_updates['date_added'])),
                        'verification_status'=> $seller_updates['verification_status'],
                        'verified_by'       => $seller_updates['verified_by'],
                        'previous_value'    => ($seller_updates['previous_value']) ? $seller_updates['previous_value'] : '--NONE--',
                        'additional_details'=> ($seller_updates['additional_details']) ? $seller_updates['additional_details'] : ''
                    );
                }
            }


            $tinArray = array('tin_tax_type'=>'', 'tin'=>'', 'tin_image'=>'','primary_address_verify'=>array());
            $panArray = array('pan'=>'', 'pan_image' =>'');
            $bankArray = array('bank_ac_number' => '', 'bank_ac_holder_name' => '', 'cancel_cheque_image' => '', 'ifsc_code' => '');
            $gstArray = array('gst_arn'=>'', 'gst_provisional_id'=>'', 'gst_certificate_image'=>'');

            $get_pending_verification = $this->_seller_profile->getPendingVerification( $this->request->get['seller_id'] );
            $data['number_of_pending_verification'] = count($get_pending_verification);
            if( $get_pending_verification ) {
                foreach( $get_pending_verification as $seller_updates ) {
                    $image = '';
                    if(strpos($seller_updates['update_type'], '_image') !== false){
                        $image = $seller_updates['new_value'];
                    }

                    if($seller_updates['verification_status'] == 'attached_tin_address'){
                        $tinArray['primary_address_verify']['update_id'][] = $seller_updates['update_id'];
                        $tinArray['primary_address_verify']['update_type'][$seller_updates['update_type']] = $seller_updates['new_value'];
                    }
                    
                    // tin taxs type Taxable, Tax free, Composite
                    $tin_tax_array = array();                    

                    if($seller_updates['update_type'] == "tin_tax_type"){

                        $new_tin_tax_type = explode(',',$seller_updates['new_value']);

                        if (!empty($seller_info)) {
                            $tin_taxs = $seller_updates['new_value'];
                            if(!empty($tin_taxs)){
                                $tin_taxs = explode(',', $tin_taxs);
                                foreach($tin_taxs as $tin_tax_val){
                                    $tin_tax_array[$tin_tax_val] = $tin_tax_type[$tin_tax_val];
                                }
                            }
                        }

                        
                        $seller_updates['tin_tax_type']  = (!empty($tin_tax_array) ? $tin_tax_array : '' );                       
                        $tinArray['tin_tax_type'] = $seller_updates;
                    }

                    if($seller_updates['update_type'] == "tin"){
                        $tinArray['tin'] = $seller_updates;                      
                    }


                    if($seller_updates['update_type'] == "tin_image"){
                        $seller_updates['images'] = $image;
                        $tinArray['tin_image'] = $seller_updates;                        
                    }

                    //Pan
                    if($seller_updates['update_type'] == "pan"){
                        $panArray['pan'] = $seller_updates;                      
                    }

                    if($seller_updates['update_type'] == "pan_image"){
                        $seller_updates['images'] = $image;
                        $panArray['pan_image'] = $seller_updates;                      
                    }                     

                    //Bank
                    if($seller_updates['update_type'] == "bank_ac_number"){
                        $bankArray['bank_ac_number'] = $seller_updates;                      
                    }

                    if($seller_updates['update_type'] == "bank_ac_holder_name"){
                        $bankArray['bank_ac_holder_name'] = $seller_updates;                      
                    }

                    if($seller_updates['update_type'] == "cancel_cheque_image"){
                        $seller_updates['images'] = $image;
                        $bankArray['cancel_cheque_image'] = $seller_updates;                      
                    }

                    if($seller_updates['update_type'] == "ifsc_code"){
                        $bankArray['ifsc_code'] = $seller_updates;                      
                    }

                    //GST Details
                    if($seller_updates['update_type'] == "gst_arn"){
                        $gstArray['gst_arn'] = $seller_updates;                      
                    }

                    if($seller_updates['update_type'] == "gst_provisional_id"){
                        $gstArray['gst_provisional_id'] = $seller_updates;                      
                    }

                    if($seller_updates['update_type'] == "gst_certificate_image"){
                        $seller_updates['images'] = $image;
                        $gstArray['gst_certificate_image'] = $seller_updates;                      
                    }
                }

                $data['pending_verification'] = array('tin'=>$tinArray, 'pan'=>$panArray, 'bank'=>$bankArray, 'gst'=>$gstArray);
                
            }
            
            // get seller category_list for promotion
            $seller_category_list = $this->model_sellers_sellers->getSellerCategoryList($this->request->get['seller_id']);
            $data['promoted_seller_category_list'] = $seller_category_list;
            // verify/modify category product count with SOLR
            foreach ($seller_category_list as $key => $category_details) {
              
              $category_id = $category_details['category_id'];
              $fetch_detail_count = 0;
              if(!empty($category_id)) {
                
                $solr_result = $this->getPromotableIdsFromSolr( $this->request->get['seller_id'], $category_id, $fetch_detail_count);
                if(empty($solr_result)) {
                  $seller_category_list[$key]['product_count'] = 0;
                } else if($solr_result->getNumFound() < $seller_category_list[$key]['product_count']) {
                  $seller_category_list[$key]['product_count'] = $solr_result->getNumFound();
                }
                if($seller_category_list[$key]['product_count'] == 0) {
                  unset($seller_category_list[$key]);
                } else {
                  $seller_category_list[$key]['product_count'] = $this->getPromotionCount($seller_category_list[$key]['product_count']);
                }
              } else {
                unset($seller_category_list[$key]);
              }
            }
            $data['seller_category_list'] = $seller_category_list;
            
            //get promotion_category 
            if (isset($this->request->post['promotion'])) { 
                    $promotion = $this->request->post['promotion']; 
            } else
            if (isset($this->request->get['seller_id']) ) { 
                    $promotion = $this->model_sellers_sellers->getSellerPromotion($this->request->get['seller_id']);
            } else {  
                    $promotion = array();
            }
            $data['promotion'] = $promotion; 
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sellers/seller_form.tpl', $data));
    }



    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'sellers/sellers')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        $seller_id = 0;
        if(!empty($this->request->get['seller_id'])) {
            $seller_id = $this->request->get['seller_id'];
        }
        
        if ( empty($this->request->post['seller_nickname']) || (utf8_strlen($this->request->post['seller_nickname']) < 1) ) {
            $this->error['seller_nickname'] = $this->language->get('error_seller_nickname');
        }

        if ((utf8_strlen($this->request->post['seller_firmname']) < 1) || (utf8_strlen(trim($this->request->post['seller_firmname'])) > 64)) {
            $this->error['seller_firmname'] = $this->language->get('error_seller_firmname');
        }

        $product_name_prefix = trim($this->request->post['product_name_prefix']) ?? '';

        if( utf8_strlen($product_name_prefix) > 12 ){
            $this->error['error_product_name_prefix'] = $this->language->get('error_product_name_prefix');
        }
        if( utf8_strlen($product_name_prefix) > 0 
            && 
            !preg_match('/^([a-zA-Z0-9]+)$/', $product_name_prefix)
        ){
            $this->error['error_product_name_prefix'] = $this->language->get('error_product_name_prefix_alph_numeric');
        }

        if (!empty($product_name_prefix) && !isset($this->error['error_product_name_prefix'])) {
            $duplicate_seller_id = $this->_seller_profile->getSellerIdByProductNamePrefix($product_name_prefix);
            if (!empty($duplicate_seller_id) && $duplicate_seller_id != $seller_id) {
                $this->error['error_product_name_prefix'] = $this->language->get('error_duplicate_product_name_prefix');
            }
        }
        
        if ((utf8_strlen($this->request->post['seller_telephone']) < 10) || (!preg_match('/^\d{10}$/',$this->request->post['seller_telephone']))) {
            $this->error['seller_telephone'] = $this->language->get('error_seller_telephone');
        }

        if ((utf8_strlen($this->request->post['seller_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['seller_email'])) {
            $this->error['seller_email'] = $this->language->get('error_seller_email');
        }

        if ( empty($this->request->post['seller_firm_address'] ) ) {
            $this->error['seller_firm_address'] = $this->language->get('error_seller_firm_address');
        }

        if ((utf8_strlen($this->request->post['city']) < 1) || (utf8_strlen(trim($this->request->post['city'])) > 64)) {
            $this->error['city'] = $this->language->get('error_city');
        }

        if ((utf8_strlen($this->request->post['pincode']) < 6) || (utf8_strlen($this->request->post['pincode']) > 6)) {
            $this->error['pincode'] = $this->language->get('error_pincode');
        }

        if ( empty($this->request->post['pickup_address'] ) ) {
            $this->error['pickup_address'] = $this->language->get('error_pickup_address');
        }

        if ((utf8_strlen($this->request->post['pickup_city']) < 1) || (utf8_strlen(trim($this->request->post['pickup_city'])) > 64)) {
            $this->error['pickup_city'] = $this->language->get('error_pickup_city');
        }

        if ((utf8_strlen($this->request->post['pickup_pincode']) < 6) || (utf8_strlen($this->request->post['pickup_pincode']) > 6)) {
            $this->error['pickup_pincode'] = $this->language->get('error_pickup_pincode');
        }

        if ((utf8_strlen($this->request->post['pan']) < 1) || (utf8_strlen(trim($this->request->post['pan'])) > 10)) {
            $this->error['pan'] = $this->language->get('error_pan');
        }

        if ((utf8_strlen($this->request->post['primary_contact_name']) < 1) || (utf8_strlen(trim($this->request->post['primary_contact_name'])) > 64)) {
            $this->error['primary_contact_name'] = $this->language->get('error_primary_contact_name');
        }

        if ((utf8_strlen($this->request->post['primary_contact_no']) < 10) || (!preg_match('/^\d{10}$/',$this->request->post['primary_contact_no']))) {
            $this->error['primary_contact_no'] = $this->language->get('error_primary_contact_number');
        }

        if ((utf8_strlen($this->request->post['pickup_holder_name']) < 1) || (utf8_strlen(trim($this->request->post['pickup_holder_name'])) > 64)) {
            $this->error['pickup_holder_name'] = $this->language->get('error_pickup_holder_name');
        }

        if ((utf8_strlen($this->request->post['pickup_contact_no']) < 10) || (!preg_match('/^\d{10}$/',$this->request->post['pickup_contact_no']))) {
            $this->error['pickup_contact_no'] = $this->language->get('error_pickup_contact_number');
        }

        if ((utf8_strlen($this->request->post['account_holder_name']) < 1) || (utf8_strlen(trim($this->request->post['account_holder_name'])) > 64)) {
            $this->error['account_holder_name'] = $this->language->get('error_account_holder_name');
        }

        if ((utf8_strlen($this->request->post['account_contact_no']) < 10) || (!preg_match('/^\d{10}$/',$this->request->post['account_contact_no']))) {
            $this->error['account_contact_no'] = $this->language->get('error_account_contact_number');
        }

        if ((utf8_strlen($this->request->post['inventory_holder_name']) < 1) || (utf8_strlen(trim($this->request->post['inventory_holder_name'])) > 64)) {
            $this->error['inventory_holder_name'] = $this->language->get('error_inventory_holder_name');
        }

        if ((utf8_strlen($this->request->post['inventory_contact_no']) < 10) || (!preg_match('/^\d{10}$/',$this->request->post['inventory_contact_no']))) {
            $this->error['inventory_contact_no'] = $this->language->get('error_inventory_contact_number');
        }

        if ((utf8_strlen($this->request->post['bank_ac_holder_name']) < 1) || (utf8_strlen(trim($this->request->post['bank_ac_holder_name'])) > 64)) {
            $this->error['bank_ac_holder_name'] = $this->language->get('error_bank_ac_holder_name');
        }

        if ((utf8_strlen($this->request->post['bank_ac_number']) < 1) || (utf8_strlen(trim($this->request->post['bank_ac_number'])) > 64)) {
            $this->error['bank_ac_number'] = $this->language->get('error_bank_ac_number');
        }

        if ((utf8_strlen($this->request->post['ifsc_code']) < 11) || (utf8_strlen(trim($this->request->post['ifsc_code'])) > 11)) {
            $this->error['ifsc_code'] = $this->language->get('error_ifsc_code');
        }

        if(!empty($this->request->post['non_serviceable_areas'])){
            $non_serviceable_data = explode(',', $this->request->post['non_serviceable_areas']);
            foreach($non_serviceable_data as $service_areas){
                if(utf8_strlen($service_areas)!=6){
                    $this->error['error_service_area'] = $this->language->get('error_service_area');
                }
            }

            if(empty($this->request->post['seller_app_only'])){
                $this->error['error_app_only'] = $this->language->get('error_app_only');
            }    
        }

        if ((utf8_strlen($this->request->post['pickup_city_code']) < 2) || (utf8_strlen(trim($this->request->post['pickup_city_code'])) > 2)) {
            $this->error['error_pickup_city_code'] = $this->language->get('error_pickup_city_code');
        }

        // start: check for gstin provisional id
        
		$this->request->post['gst_provisional_id'] = $this->request->post['gst_provisional_id'] ?? "";
		$this->request->post['old_gst_provisional_id'] = $this->request->post['old_gst_provisional_id'] ?? "";
		
		if( $this->request->post['gst_provisional_id'] != $this->request->post['old_gst_provisional_id'] ) { // don't validate if same value
            
            if ( empty($this->request->post['gst_provisional_id']) ) {
                $this->error['error_seller_gstin_validation'] = $this->language->get('error_seller_gstin_validation');
            } else {
                
                $sellerObject = new SellerGST($this->registry);
                $valid_gst_result = $sellerObject->validateGSTNumber($this->request->post['gst_provisional_id'], $seller_id);
                
                // regex error
        		if ( !empty(trim($this->request->post['gst_provisional_id'])) && !($valid_gst_result['result'] === true) ) {
        			
                    if($valid_gst_result['message'] == "error_regex") {
        				$this->error['error_seller_gstin_validation'] = $this->language->get('error_correct_seller_gstin_validation');
        			} elseif($valid_gst_result['message'] == "error_checksum") { // checksum error
        				$this->error['error_seller_gstin_validation'] = sprintf($this->language->get('error_gst_checksum'),
        				 									 $valid_gst_result['gst_number_details']['gst_number_without_checksum']."<b>".$valid_gst_result['gst_number_details']['gst_number_checksum']."</b>");
        			} else if($valid_gst_result['message'] == "error_duplicate") { // duplicacy error
                        
                        $user_str = '';
                        if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
        					$duplicate_gst_number_details = $valid_gst_result['duplicate_gst_number_details'];
                            if(!empty($duplicate_gst_number_details['telephone'])) {
                                $user_str = 'mobile number <strong>' .$duplicate_gst_number_details['telephone']. '</strong>';
                            } else if(!empty($duplicate_gst_number_details['email'])){
                                $len = strlen(explode('@',$duplicate_gst_number_details['email'])[0]);
                                $user_str = 'email <strong>' .$duplicate_gst_number_details['email'].'</strong>';
                            }
                            $this->error['warning'] = sprintf($this->language->get('error_gst_no_already_exists'), $this->request->post['gst_provisional_id'], $user_str, $valid_gst_result['duplicate_gst_number_entity'], '<strong>'.$duplicate_gst_number_details['entity_id'].'</strong>' );
                        }
        			}
        		} else if( !$this->MatchGstPancard($this->request->post['pan'], $valid_gst_result['pan']) ) {
                    $this->error['error_seller_gstin_validation'] = $this->language->get('error_not_match_seller_gstin_validation');
                }
            }
        }
        // end: check for gstin provisional id
        
        $seller_id = 0;
        if(!empty($this->request->get['seller_id'])){
            $seller_id = $this->request->get['seller_id'];
        }

        if (isset($this->request->post['promotion'])) {
            $cat_arr = array();
            foreach ($this->request->post['promotion'] as $key => $promo) {
              
                    if(in_array($promo['category_id'], $cat_arr)) {
                      $this->error['promotion'][$key]['distinct_category'] = $this->language->get('error_distinct_category');
                      $this->error['distinct_category'] = $this->language->get('error_distinct_category');
                    }
                    
                    $cat_arr[] = $promo['category_id'];
                    if ($promo['category_id'] == '0') {
                        $this->error['promotion'][$key]['category'] = $this->language->get('error_promotion_category');
                    }
                    if ( empty($promo['product_count']) || $promo['product_count'] < '1' ) {
                        $this->error['promotion'][$key]['count'] = $this->language->get('error_promotion_count');
                    }
            }
            // if(count($cat_arr) !== count(array_unique($cat_arr))){ 
            //     $this->error['distinct_category'] = $this->language->get('error_distinct_category');
            // }
        }
        
        return !$this->error;
    }
    
    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sellers/sellers')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    /** Method for match gst provisional id to pan card
    *@param: $pan_card_value : get request pan card number   
    *@param: $pan_no_form_gst_provisional_id : fetch pancard number from gst provisional id
    *@return true or false
    *@author vikas, 2017 
    */ 
    private function MatchGstPancard($pan_card_value, $pan_no_form_gst_provisional_id){
        $flag = true;
        if($pan_no_form_gst_provisional_id != $pan_card_value ){
            $flag = false;
        }
        return $flag;
    }

    private function SellerAccountEmail($email, $password, $company, $additional_emails) {

        $data['email'] = $email;
        $data['password'] = $password;
        $html = $this->load->view('sellers/seller_account_email.tpl',$data);

        $mail = new  PHPMailer();

        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
        $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

        $mail->addAddress($email, $company);

        $array_additional_email = explode(',',rtrim(trim($additional_emails),','));
        foreach($array_additional_email as $emails){
            $mail->addAddress($emails);
        }

        $mail->addCC(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

        $mail->Subject = 'Welcome to WholesaleBox : Seller Panel Access';

        $mail->msgHTML($html);
        $mail->isHTML(true);
        $mail->send();
    }

    private function SellerPasswordChangeEmail($email, $password, $company, $additional_emails) {

        $data['email'] = $email;
        $data['password'] = $password;
        $data['company'] = $company;
        $html = $this->load->view('mail/seller_password_change_mail.tpl',$data);

        $mail = new  PHPMailer();

        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
        $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

        $mail->addAddress($email, $company);

        $array_additional_email = explode(',',rtrim(trim($additional_emails),','));
        foreach($array_additional_email as $emails){
            $mail->addAddress($emails);
        }

        $mail->addCC(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

        $mail->Subject = 'Welcome to WholesaleBox : Password Changed';

        $mail->msgHTML($html);
        $mail->isHTML(true);
        $mail->send();
    }

    public function updateSellerVacationMode() {

        $json = array();

        if (!$this->user->hasPermission('modify', 'sellers/sellers')) {
            $json['error'] = 'You do not have permission to change Seller Vacation Mode !';
        } else {
            $selected_seller = implode(',',$this->request->post['selected_seller']);
            $vacation_mode_value = implode(',',$this->request->post['vacation_mode']);

            $this->load->model('seller/manage_inventory','frontend');
            $this->frontend_model_seller_manage_inventory->updateVacation($vacation_mode_value, $selected_seller);

            $json['success'] = "Success";
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    //get bank address and branch by IFSC code
    public function bankDetails(){

        // system/helper/utilities.php
        $json = validateBankIFSC($this->request->get['ifsc_code']);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }

    /* comment after open when separate seller panel created */
    /**
    * Method for update verification with approved or Cancel
    * @request: $seller_id : Integer of seller id
    * @request: $update_id : Integer of validation id
    * @request: $verification_status : String of validation status ie. approved or Cancel
    * @request: $input_name : String of input name ie. pan , tin , ifsc code etc...
    * @request: $new_input_value : String of new input value ie. text box value
    * @return NULL
    * @author vikas, 2017
    */

    public function updateVerificationRequest(){
        $json = array();
        $this->load->language('sellers/sellers');
        $valid_gst = true;
        if(!empty($this->request->post['seller_data']) && !empty($this->request->post['seller_data']['gst_provisional_id'])) {
            $sellerObject = new SellerGST($this->registry);
            $valid_gst_result = $sellerObject->validateGSTNumber($this->request->post['seller_data']['gst_provisional_id'],$this->request->post['seller_id']);
            if ( !empty(trim($this->request->post['seller_data']['gst_provisional_id'])) && !($valid_gst_result['result'] === true) ) {
    			if($valid_gst_result['message'] == "error_regex") {
                    $valid_gst = false;
    				$error = $this->language->get('error_correct_seller_gstin_validation');
    			} elseif($valid_gst_result['message'] == "error_checksum") { // checksum error
                    $valid_gst = false;
    				$error = sprintf($this->language->get('error_gst_checksum'),
    				 									 $valid_gst_result['gst_number_details']['gst_number_without_checksum']."<b>".$valid_gst_result['gst_number_details']['gst_number_checksum']."</b>");
    			} else if($valid_gst_result['message'] == "error_duplicate") { // duplicacy error
                    $valid_gst = false;
                    $user_str = '';
                    if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
    					$duplicate_gst_number_details = $valid_gst_result['duplicate_gst_number_details'];
                        if(!empty($duplicate_gst_number_details['telephone'])) {
                            $user_str = 'mobile number ' .$duplicate_gst_number_details['telephone'];
                        } else if(!empty($duplicate_gst_number_details['email'])){
                            $len = strlen(explode('@',$duplicate_gst_number_details['email'])[0]);
                            $user_str = 'email ' .$duplicate_gst_number_details['email'].'';
                        }
                        $error = sprintf($this->language->get('error_gst_no_already_exists_alert'), $this->request->post['seller_data']['gst_provisional_id'], $user_str, $valid_gst_result['duplicate_gst_number_entity'], $duplicate_gst_number_details['entity_id'] );
                    }
    			}
    		}
        }
        if(!$valid_gst) {
            $json['status'] = 0;
            $json['error'] = $error ?? "Invalid GST Number !!!";
        } else {            
            $this->_seller_profile->updateVerificationApproved( $this->request->post );
            $json['status'] = 1;
            $json['success'] = "Update successfully";
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    /**
    * Method for get new nick name
    * @param $seller_id : Integer of seller id
    * @return seller id, category id , rating
    * @author vikas, 2017
    */
    public function getNewNickname(){
        $citycode = $this->request->post['citycode'];
        $seller_id = $this->request->post['seller_id'];
        $json = array();
        if( !empty($citycode) ){
            $get_data = $this->_seller_profile->getNewNickname( $citycode, $seller_id );
            if( $get_data ){
                $json['nickname'] = $get_data;
            } else {
                $json['error'] = 'Please create new series of nickname !';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
    * Method for download verify image
    * @return json encode value with src.
    * @author vikas, 2017
    */
    public function verify_dwnlod_img(){

        if( empty($this->request->post['image_name']) ){
            return false;
        }

        $file = array();

        $basename = $this->request->post['image_name'];
        foreach( $basename as $image_key => $image_value ){
            // $filename = DIR_SELLER_UPLOADS.''.$image_value; // don't accept other directories
            $filename = $image_value; // don't accept other directories

            // Read image path, convert to base64 encoding
            $imageData = base64_encode(file_get_contents($filename));

            // Format the image SRC:  data:{mime};base64,{data};
            $file[$image_key] = 'data: '.mime_content_type($filename).';base64,'.$imageData;
        }

        echo json_encode($file);
    }

    /**
    * Method for generate auto state/region.
    * @return json encode value with state name.
    * @author vikas, 2017
    */
    public function getAutoGenerateState(){
        $json = array();
        if( !empty($this->request->post) ){
            $request['pincode'] = $this->request->post['pincode'];
            $data_json = json_encode($request);
            $api_url = "https://www.wholesalebox.in/index.php?route=restapi/lookup/pincode";
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array('Content-Type: application/json',
                            'Content-Length: ' . strlen($data_json))
            );
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
            $results = json_decode($result, true );

            if( $results ){
                $json['city'] = $results[0]['city'];
                $json['zone'] = $results[0]['zone'];
                $json['zone_id'] = $results[0]['zone_id'];
            } else{
                $json['error'] = 'Pincode select your new state!';
            }

            $json = json_encode($json);
            echo $json ; exit;
        } else {
            $json['error'] = json_encode(array('failler' => 'Pincode is invalid'));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }

    public function autocomplete(){
        $json = array();
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('sellers/sellers');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_sellers_sellers->getSellersName($filter_data);
			foreach ($results as $result) {
				$json[] = array(
					'seller_id'     => $result['seller_id'],
					'name'          => strip_tags(html_entity_decode($result['nickname'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    public function saveSorSeller()
    {
        $this->load->model('sellers/sellers');
        $sor_days       = $this->request->post['sor_days'];
        $sor_type       = $this->request->post['sor_type'];
        $product_update = $this->request->post['product_update'];
        $seller_id = $this->request->post['seller_id'];
        $this->model_sellers_sellers->updateSellerSorTerms($seller_id, $sor_days, $sor_type, $product_update );
        echo json_encode(array('success'=>'SOR terms update successfully.'));
      
    }
    
    /**
    * Method for update seller promotion
    * @param $seller_id : Integer of seller id
    * @return NULL
    * @author Amarat/Anurag
    */
    public function updateSellerPromotion( $seller_id, $data ) {
      $this->load->model('sellers/sellers');
      
      $mail_data = array();
      
      // get all previous promotions for this seller
      $all_old_promotion = $this->model_sellers_sellers->getSellerPromotion($seller_id);
      
      foreach ($data as $nkey => $new_promotion) {
        $category_found = 0;
        if(!empty($all_old_promotion)) { // update promotion product count OR status update
          foreach ($all_old_promotion as $okey => $old_promotion) {
            if($old_promotion['category_id'] == $new_promotion['category_id']) {
              $final_product_ids = $previous_promoted_ids = explode(',', $old_promotion['product_ids']);
              $category_found = 1;
              $update_data = array();
              
              $product_count_diff = $new_promotion['product_count'] - $old_promotion['product_count'];
              
              /***
              // Below commented code will be updated and used in case if promoted count can be modified within 48hrs.
              
              if( ($new_promotion['product_count'] > $old_promotion['product_count']) && (int)$new_promotion['status'] == 1) { // More products to be promoted
                $this->processSellerPromotion( $final_product_ids, $seller_id, $new_promotion['category_id'], $product_count_diff, $previous_promoted_ids );
                $update_data[] = " product_count = ".count($final_product_ids);
                $update_data[] = " product_ids = '".implode(',',$final_product_ids)."'";
                
              } else if( ($new_promotion['product_count'] < $old_promotion['product_count']) && (int)$new_promotion['status'] == 1) { // Few products to be demoted
              //   $update_data[] = " product_count = ".$new_promotion['product_count'];
              //   // TODO :demote ids in product table
              // 
              //   list($final_product_ids, $to_demote_ids) = array_chunk($previous_promoted_ids, $new_promotion['product_count']);
              //   $update_data[] = " product_ids = '".implode(',',$final_product_ids)."'";
              // 
              // }
              ***/
              
              if( ($old_promotion['status'] != $new_promotion['status']) ) { // Status update 
                $update_data[] = "status = ".$new_promotion['status'];
              }
              
              if(!empty($update_data)) {
                $type = "update";
                $update_data[] = " modified_date = NOW()";
                $this->model_sellers_sellers->updateSellerPromotion( $type, $update_data, $old_promotion['id'] );
              }
            }
          }
        }
        if(!$category_found) {
          $insert_data = array();
          $final_product_ids = array();
          $this->processSellerPromotion( $final_product_ids, $seller_id, $new_promotion['category_id'], $new_promotion['product_count'] );
          
          $type = "insert";
          $insert_data[] = "seller_id = ". (int)$seller_id;
          $insert_data[] = "category_id = ". $new_promotion['category_id'];
          $insert_data[] = "product_count = ". count($final_product_ids);
          $insert_data[] = "status = ". (int)$new_promotion['status'];
          $insert_data[] = "product_ids = '". implode(',', $final_product_ids)."'";
          $insert_data[] = "added_date =  NOW()";
          $insert_data[] = "modified_date = NOW()";
          $this->model_sellers_sellers->updateSellerPromotion( $type, $insert_data );
          
          $mail_data['category_ids'][] = array(
                                          "category_name" => $this->model_sellers_sellers->getCategoryName($new_promotion['category_id'])['name'],
                                          "count" => count($final_product_ids)
          );
        }
      }
      
      // Send mail for promotion
      if(!empty($mail_data)) {
        $mail_data['seller_id'] = $seller_id;
        $this->sendPromotionMail($mail_data);
      }
      return true;
    }
    
    public function processSellerPromotion( &$final_product_ids, $seller_id, $category_id, $product_count, $previous_promoted_ids = null ) {
      $this->load->model('sellers/sellers');
      $product_change_log = new ProductChangeLog($this->registry);
      
      // Get promoted ids
      $promoted_product_ids = $this->getPromotableProductIds($seller_id, $category_id, $product_count, $previous_promoted_ids);
      if( !empty($promoted_product_ids) ) {
        
        $changes_data['sort_order']['new_value'] = '1';
        $source_field = 'product_edit';
        
        foreach ($promoted_product_ids as $key => $product_data) {
          $final_product_ids[] = $product_data['product_id'];
          
          // TODO: change sort_order of product
          $new_sort_order = 1;
          $this->model_sellers_sellers->updateProductSortOrder($product_data['product_id'], $new_sort_order);
          
          // Log change in admin log
          $changes_data['sort_order']['old_value'] = $product_data['sort_order'];
          $product_change_log->recordLogs($product_data['product_id'], $changes_data, $source_field);
        }
      }
    }
    
    public function getPromotableProductIds( $seller_id, $category_id, $product_count, $previous_promoted_ids = null ) {
      
      $this->load->model("catalog/product");
      $new_product_count = $product_count;
      $product_ids = array();
      $checked_ids = array();
      if(!empty($previous_promoted_ids)) {
        $checked_ids = $previous_promoted_ids;
      }
      
      while ($new_product_count > 0) {
        
        $resultset = array();
        // verify stock status in solr
        $solr_result = $this->getPromotableIdsFromSolr( $seller_id, $category_id, $new_product_count, $checked_ids );
        if($solr_result) {
          // format result
          foreach ($solr_result as $document) {
              $resultset[] = $document->id;
              $checked_ids[] = $document->id;
          }
        }
        if(!empty($resultset)) {    
          foreach ($resultset as $key => $value) {
            $op_result_row = $this->model_catalog_product->getProduct((int)$value);
            if($op_result_row) {
              $stock_status_info = Cart::getProductStockStatus($op_result_row);
              if ( isset($stock_status_info['stock']) && !($stock_status_info['stock'] === false) ) {
                $product_ids[$op_result_row['product_id']]['product_id'] = $op_result_row['product_id'];
                $product_ids[$op_result_row['product_id']]['sort_order'] = $op_result_row['sort_order'];
              }
            }
          }
          $new_product_count = $product_count - count($product_ids);
        } else {
          $new_product_count = 0;
          break;
        }
      }
      if(!empty($product_ids)) {
        return $product_ids;
      } else {
        return false;
      }
    }
    
    public function getPromotableIdsFromSolr( $seller_id, $category_id, $new_product_count, $checked_ids = array() ) {
      
      $config_solr = $this->config->solrConfig();
      
        // Get solr client instance
      $client = new Solarium\Client($config_solr);
      $client->getPlugin('postbigrequest');
        // Get select 
      $query = $client->createSelect();
      $solr_query = array();
      $solr_query[] = "seller_id:".$seller_id;
      $solr_query[] = "category_id:".$category_id;
      $solr_query[] = "quantity:[1 TO *]";
      $solr_query[] = "stock_status_id:7";
      $solr_query[] = "status:1";
      $solr_query[] = "sort_order:[999 TO *]";
      $solr_query[] = "is_archived:0";
      $solr_query[] = "vacation_mode:0";
      $solr_query[] = "seller_status:1";
      $solr_query[] = "piece_in_set:[1 TO *]";
      $solr_query[] = '(stock_status:"In Stock")';
      $solr_query[] = "-id:(56568)";
      // hsn_code and tax_class_id check skipped
      
      if(!empty(array_unique(array_filter($checked_ids)))) {
        $solr_query[] = "-id:(".implode(' OR ', array_unique(array_filter($checked_ids))).")";
      }
        // set query
      $solr_query_and = implode(' AND ', $solr_query);
      $query->setQuery($solr_query_and);
      $start = 0;
      $limit = $new_product_count;
      $query->setStart($start)->setRows($limit);
      
      //Below is to get random results
      $randString = mt_rand();        
      $query->addSort('random_'.$randString, $query::SORT_DESC);
      
      $solr_result = $client->select($query);
      if($solr_result->getNumFound()) {
        return $solr_result;  
      }
      return false;
    }
    
    public function getPromotionCount($product_count) {
      $calculated_product_count = ceil(25*$product_count/100);
      
      if($calculated_product_count > 100) {
        $product_count = 100;
      } else if($calculated_product_count <= 25 && $product_count >= 25) {
        $product_count = 25;
      } else if($calculated_product_count <= 100 && $product_count >= 25) {
        $product_count = $calculated_product_count;
      }
      return $product_count;
    }
    
    public function sendPromotionMail($data) {
      
      $this->load->model("sellers/sellers");
      
      $seller_data = $this->model_sellers_sellers->getSeller($data['seller_id']);
      $mail_data['seller_name'] = $seller_data['seller_company'];
      $mail_data['seller_code'] = $seller_data['nickname'];
      $mail_data['promotion_date'] = date("d-M-Y");
      $mail_data['promoted_by'] = $this->user->getUserName()['name'];
      $mail_data['category_data'] = $data['category_ids'];
      
      $html = MailTemplate::mailForSellerPromotion($mail_data);
      
      $mail = new  PHPMailer();

      $mail->isSMTP();
      $mail->Host = $this->config->get('config_mail_smtp_hostname');
      $mail->Port = $this->config->get('config_mail_smtp_port');
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = $this->config->get('config_mail_smtp_username');
      $mail->Password = $this->config->get('config_mail_smtp_password');
      $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
      
      if(SITE_ENVIRONMENT == "Production") {
        $mail->addAddress(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
      } else {
        $mail->addAddress(EMAIL_IDS['anurag']['email_id'], EMAIL_IDS['anurag']['name']);
        $mail->addAddress(EMAIL_IDS['ankita']['email_id'], EMAIL_IDS['ankita']['name']);
      }
      
      $mail->Subject = 'Seller Promotion - Start';
      $mail->isHTML(true);
      $mail->msgHTML($html);
      $mail->send();
    }
    
    /**
     * Function updateSellerExclusiveStatus is used to update exclusive status of sellers
     * @request seller_arr - seller_id array()
     * @request exclusive_status - exclusive_status string
     * @request check_product_exclusive - check_product_exclusive 0 or 1
     * @return  json string
     * @author  Nilesh, 2018
     */
    public function updateSellerExclusiveStatus() {
        $output = array();

        if (!empty($this->request->post['seller_arr']) && !empty($this->request->post['exclusive_status'])) {
            $this->load->model("sellers/sellers");
            $seller_id_arr = $this->request->post['seller_arr'];
            $exclusive_status = $this->request->post['exclusive_status'];
            $check_product_exclusive = $this->request->post['check_product_exclusive'];
            
            $this->model_sellers_sellers->updateSellerExclusiveStatus($seller_id_arr, $exclusive_status, $check_product_exclusive);
            

            $output['success'] = 'Successfully updated!';
        } else {
            $output['error'] = 'Please select seller and exclusive status first!';
        }
        echo json_encode($output);
    }
}
