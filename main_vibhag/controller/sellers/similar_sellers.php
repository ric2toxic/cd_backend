<?php
class ControllerSellersSimilarSellers extends Controller {
    private $error = array();
    
    public function index(){ 
            
        $this->load->language('sellers/similar_sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sellers/similar_sellers');

        $this->getList();
        
    }

    
    public function add() { 
        $data = array(); 
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sellers/similar_sellers', $data);

        //load model
        $this->load->model('sellers/similar_sellers');
        
        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                

                $url = '';
                if (isset($this->request->get['filter_seller'])) {
                    $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_category'])) {
                    $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_similar_seller'])) {
                    $url .= '&filter_similar_seller=' . urlencode(html_entity_decode($this->request->get['filter_similar_seller'], ENT_QUOTES, 'UTF-8'));
                }      
                
                if (isset($this->request->get['filter_page_limit'])) {
                    $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                } 
                if (isset($this->request->get['page'])) {
                    $url .= '&page=' . $this->request->get['page'];  
                } 
                
                $this->model_sellers_similar_sellers->addSimilarSeller($this->request->post);   

                $this->session->data['success'] = $this->language->get('text_success_add');

                $this->response->redirect($this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() { 

        $this->load->language('sellers/similar_sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sellers/similar_sellers');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            $this->model_sellers_similar_sellers->editSimilarSeller($this->request->post);
            
            $this->session->data['success'] = $this->language->get('text_success_edit');

            $url = '';

            if (isset($this->request->get['filter_seller'])) {
                $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_category'])) {
                $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_similar_seller'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_similar_seller'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }


        $this->getForm(true);
    }

    public function delete() { 
        $this->load->language('sellers/similar_sellers');

        $this->document->setTitle($this->language->get('heading_title'));

        //load model
        $this->load->model('sellers/similar_sellers');
        
        if (!empty($this->request->get['delete_id']) && $this->validateDelete()) {  

            $this->model_sellers_similar_sellers->deleteSimialrSeller($this->request->get['delete_id']); 

            $this->session->data['success'] = $this->language->get('text_success_delete');

            $url = '';

            if (isset($this->request->get['filter_seller'])) {
                $url .= '&filter_seller=' . urlencode(html_entity_decode($this->request->get['filter_seller'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_category'])) {
                $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_similar_seller'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    
    protected function getList() { 
            
        // Initializing
        $data = array();
        $filter_data = array();
        $general_url = '';

        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sellers/similar_sellers', $data);

        // Looping over GET request params
        foreach ( $this->request->get as $key => $value ) {
            // Deal with filter_% keys
            if ( stripos($key, 'filter_') === 0 ) {
                // Filter(s) to get Orders from Model
                $filter_data[$key] = $value;

                // Populating URL
                $general_url .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));

                // Populating data array
                $data[$key] = $value;
            }
        }

        // Sorting is always ORDER BY order_id DESC; Getting Page number
        $page  = $this->request->get['page']  ?? 'FIRST';
        $filter_data['limit'] = 30;
        $filter_data['page'] = $page;
                
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
        
        if (isset($this->session->data['delete_success'])) {
                $data['delete_success'] = $this->session->data['delete_success'];
                unset($this->session->data['delete_success']);
        } else {
                $data['delete_success'] = '';
        }
             
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
                'text' => $data['text_home'],
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['token'] = $this->session->data['token'];

        $data['delete'] = $this->url->link('sellers/similar_sellers/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');

        $data['add'] = $this->url->link('sellers/similar_sellers/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
        //pr($filter_data);die;
        $similar_seller_list = $this->model_sellers_similar_sellers->getSimilarSellerList($filter_data);
        $data['similar_sellers'] = array(); 
        $similar_sellers = array();

          if(!empty($similar_seller_list)) {
                      
                foreach ($similar_seller_list as $result) { 
                  $data['similar_sellers'][] = array( 
                        'similar_seller_cluster_id' => $result['similar_seller_cluster_id'],
                        'cluster_name'      => $result['cluster_name'],
                        'seller_name'       => $result['seller_name'],
                        'category_name'     => $result['category_name'],
                        'edit'              => $this->url->link('sellers/similar_sellers/edit', 'token=' . $this->session->data['token'] . '&edit_id=' . $result['similar_seller_cluster_id'] . $general_url, 'SSL'),
                        'delete'            => $this->url->link('sellers/similar_sellers/delete', 'token=' . $this->session->data['token'] . '&delete_id=' . $result['similar_seller_cluster_id'] . $general_url, 'SSL'), 
                  );
                }
            }
               
            $data['page_limit_array'] = array('30','60','100','200','500','1000');

            $pagination = new PaginationV2();
            $pagination->page = $page;
            $pagination->next = end($data['similar_sellers'])['similar_seller_cluster_id'] ?? '';
            $pagination->total = count($data['similar_sellers']);
            $pagination->limit = 30;
            $pagination->url = $this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $general_url . '&page={page}', 'SSL');
            $data['pagination'] = $pagination->render();
            //get category list for filter
            $data['category_filter_list'] = $this->model_sellers_similar_sellers->getCategoryForFilter(); 

            $data['header'] = $this->load->controller('common/header');
    		$data['column_left'] = $this->load->controller('common/column_left');
	        $data['footer'] = $this->load->controller('common/footer'); 

            $this->response->setOutput($this->load->view('sellers/similar_sellers_list.tpl', $data));
	}

    protected function getForm() { 

            $data = array(); 
            // Autoloading the lanugage
            $this->load->autoLoadLanguage('sellers/similar_sellers', $data);
            
            $data['text_form'] = !isset($this->request->get['edit_id']) ? $data['text_add'] : $data['text_edit'];
            
            //handel errors
            if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            } else {
                $data['error_warning'] = ''; 
            }
            
            if (isset($this->error['category_id'])) {
                $data['error_category_id'] = $this->error['category_id'];
            }else{
                $data['error_category_id'] = '';
            }
            
            if (isset($this->error['seller_id'])) {
                $data['error_seller_id'] = $this->error['seller_id'];
            }else{
                $data['error_seller_id'] = ''; 
            }
            
            if (isset($this->error['recorde_exits'])) {
                $data['error_recorde_exits'] = $this->error['recorde_exits'];
            }else{
                $data['error_recorde_exits'] = ''; 
            }
            
            if (isset($this->error['similar_seller'])) {
                $data['error_similar_seller'] = $this->error['similar_seller'];
            }else{
                $data['error_similar_seller'] = ''; 
            }

            if (isset($this->error['cluster_name'])) {
                $data['error_cluster_name'] = $this->error['cluster_name'];
            }else{
                $data['error_cluster_name'] = ''; 
            }
            
            //get data
            if (isset($this->request->post['category_id'])) {
                $data['category_id'] = $this->request->post['category_id'];
            } else {
                $data['category_id'] = '';
            }

            if (isset($this->request->post['seller_id'])) { 
                $data['seller_id'] = $this->request->post['seller_id'];
            } else {
                $data['seller_id'] = '';
            }
            
            if (isset($this->request->post['cluster_name'])) { 
                $data['cluster_name'] = $this->request->post['cluster_name'];
            } else {
                $data['cluster_name'] = '';
            }

            if (isset($this->request->post['similar_seller'])) { 
                $data['similar_seller'] = $this->request->post['similar_seller'];
            } else {
                $data['similar_seller'] = '';
            }
            
            $url = '';
            if (isset($this->request->get['filter_seller'])) {
                $url .= '&filter_seller=' . $this->request->get['filter_seller'];
            }    
            if (isset($this->request->get['filter_page_limit'])) {
                $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
            } 
            if (isset($this->request->get['filter_category'])) {
                $url .= '&filter_category=' . $this->request->get['filter_category'];
            }        
            if (isset($this->request->get['filter_similar_seller'])) {
                $url .= '&filter_similar_seller=' . $this->request->get['filter_similar_seller'];
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
                    'href' => $this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $url, 'SSL')
            );
            
            if ($this->error && !isset($this->error['warning'])) {
                $this->error['warning'] = $data['error_warning'];
            }
            
            
            if (empty($this->request->get['edit_id'])) { 
                $data['action'] = $this->url->link('sellers/similar_sellers/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
            } else { 
                $data['action'] = $this->url->link('sellers/similar_sellers/edit', 'token=' . $this->session->data['token'] . '&edit_id=' . $this->request->get['edit_id'] . $url, 'SSL');
            }

            $data['category_data'] = $this->model_sellers_similar_sellers->getAllCategory(); 

            $data['similar_seller_list'] = array();
            $data['seller_name'] = '';
            $data['category_name'] = '';

            if (!empty($this->request->get['edit_id'])) { 
                
                $edit_data = $this->model_sellers_similar_sellers->getEditSellers((int)$this->request->get['edit_id']); 
                
                $edit_id            = $this->request->get['edit_id'] ?? 0;
                $selected_seller_id = $edit_data['seller_id'] ?? 0;
                $selected_category_id = $edit_data['category_id'] ?? 0;

                $data['seller_id'] = $selected_seller_id;
                
                $data['cluster_name'] = $edit_data['cluster_name'];   
                $data['category_id'] = $selected_category_id;   

                $cat_info = $this->model_sellers_similar_sellers->getcategory($selected_category_id); 
                $data['category_name'] = $cat_info['name'] ?? ''; 

                $data['edit_id'] = $edit_id;
            }
            
            $data['cancel'] = $this->url->link('sellers/similar_sellers', 'token=' . $this->session->data['token'] . $url, 'SSL');
            $data['token'] = $this->session->data['token'];
            
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('sellers/similar_seller_form.tpl', $data));
        }

    protected function validateForm() {
        
        if (!$this->user->hasPermission('modify', 'sellers/similar_sellers')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ( empty(trim($this->request->post['category_id'])) ) { 
            $this->error['category_id'] = $this->language->get('error_category_id'); 
        }

        if ( empty(trim($this->request->post['cluster_name'])) ) { 
            $this->error['cluster_name'] = $this->language->get('error_cluster_name'); 
        }

        if ( empty($this->request->post['similar_seller']) ) {
            $this->error['similar_seller'] = $this->language->get('error_similar_seller');
        }
        
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        
        return !$this->error;
    }

    protected function validateDelete() { 
        if (!$this->user->hasPermission('modify', 'sellers/similar_sellers')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    public function getAllseller(){ 
        $data = array();
        $status = false;
        $html = '';
        $selected_html = '';
        $seller_count = '0';

        $this->load->model('sellers/similar_sellers');
        
        $category_id = $this->request->post['category_id'] ?? 0;
        $edit_id     = $this->request->post['edit_id'] ?? 0;     

        $selected_sellers_data  =  $this->model_sellers_similar_sellers->getSimilarSeller($edit_id);
        $all_seller_data        = $this->model_sellers_similar_sellers->getAllseller($category_id, $edit_id, $selected_sellers_data); 

       
        if(count($all_seller_data) > 0){
            $status = true;
            //$html = '<input type="checkbox" onclick="$(\'input[name*=seller_id]\').prop(\'checked\', this.checked)" >';
            $html = '';
            $seller_count = count($all_seller_data);  
            foreach ($all_seller_data as $val){
                $html .='<p id="'.$val['seller_id'].'">
                            <label style="font-weight:normal;">
                                <a title="' . $val['seller_name'] .'">
                                 <input name="seller_id[]" type="checkbox" value="' . $val['seller_id'] .'">&nbsp;'.substr($val['seller_name'],0,35).'
                                </a>
                            </label>
                        </p>';
            }
        }
        //selected sellers html
        if(count($selected_sellers_data) > 0){
            $status = true;
            //$selected_html = '<input type="checkbox" onclick="$(\'input[name*=similar_seller]\').prop(\'checked\', this.checked)" >';
            $selected_html = '';
            $seller_count = count($selected_sellers_data);  
            foreach ($selected_sellers_data as $val){
                    $selected_html .= ' <p id="' . $val['seller_id'] .'">
                                        <label style="font-weight:normal;">
                                        <a title="' . $val['seller_name']."]" .'"><input name="selected_seller[]" type="checkbox" value="' . $val['seller_id'] .'"> ' . substr($val['seller_name'],0,35) . '</a>
                                        <input name="similar_seller[]" type="hidden" value="' . $val['seller_id'] .'">
                                        </label>
                                    </p>';
            }
        }                       
        
        $data['status'] =  $status;
        $data['seller_count'] = $seller_count;
        $data['html'] =  $html;
        $data['selected_html'] = $selected_html;

        echo json_encode($data);  
    } 
    
    public function getSimilarSeller(){  
        $data = array();
        $status = false;
        $html = '';
        
        $this->load->model('sellers/similar_sellers');
        
        $similar_seller_cluster_id = $this->request->post['edit_id'];
       
        $similar_seller_data = $this->model_sellers_similar_sellers->getSimilarSeller($similar_seller_cluster_id); 

        if(count($similar_seller_data) > 0){
            array_shift($similar_seller_data);
            $status = true;
            $html = '';
            foreach ($similar_seller_data as $val){
                $html .= ' <p id="' . $val['seller_id'] .'">
                                <label style="font-weight:normal;">
                                <a title="' . $val['seller_name']."]" .'"><input name="selected_seller[]" type="checkbox" value="' . $val['seller_id'] .'"> ' . substr($val['seller_name'],0,35) . '</a>
                                <input name="similar_seller[]" type="hidden" value="' . $val['seller_id'] .'">
                                </label>
                            </p>';
            }
            
        }
        
        $data['status'] =  $status;
        $data['html'] =  $html;
        echo json_encode($data); 
    }


}
