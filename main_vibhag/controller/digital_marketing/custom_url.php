<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ControllerDigitalMarketingCustomUrl extends Controller {
	private $error = array();
	public function index() { 
                $this->load->language('digital_marketing/custom_url');  
		$this->document->setTitle($this->language->get('heading_title')); 
		// load model
		$this->load->model('digital_marketing/custom_url');  
		$this->getList(); 
	}

	protected function getList() {
            
            $data = array();
                
            //get filter
            if (isset($this->request->get['filter_query'])) {
                $filter_query = $this->request->get['filter_query'];
            } else {
                $filter_query = null;
            }
            if (isset($this->request->get['filter_keyword'])) {
                $filter_keyword = $this->request->get['filter_keyword'];
            } else {
                $filter_keyword = null;
            }
            if (isset($this->request->get['filter_meta_title'])) {
                $filter_meta_title = $this->request->get['filter_meta_title'];
            } else {
                $filter_meta_title = null; 
            }
            $data['filter_query'] = $filter_query;
            $data['filter_keyword'] = $filter_keyword;
            $data['filter_meta_title'] = $filter_meta_title; 
            
            if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
            } else {
                    $page = 1;
            }        
            
            
            // General URL (without sort or page)
            $url = '';

            if (isset($this->request->get['filter_query'])) {
                    $url .= '&filter_query=' . $this->request->get['filter_query'];
            }    
            if (isset($this->request->get['filter_page_limit'])) {
                    $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
            } 
            if (isset($this->request->get['filter_keyword'])) {
                    $url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
            }        
            if (isset($this->request->get['filter_meta_title'])) {
                    $url .= '&filter_meta_title=' . $this->request->get['filter_meta_title'];
            } 
        
            
            // URL for General links to ensure we reach same settings again on the list page
            $general_url = $url;
            if (isset($this->request->get['page'])) {
                    $general_url .= '&page=' . $this->request->get['page'];
            } 

	    // Autoloading the lanugage
            $this->load->autoLoadLanguage('digital_marketing/custom_url', $data);
            
            
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
                    'href' => $this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
            );

            $data['token'] = $this->session->data['token'];

                if (isset($this->request->get['filter_page_limit'])) {
			$filter_page_limit = $this->request->get['filter_page_limit'];
		} else {
			$filter_page_limit = $this->config->get('config_limit_admin');
		}
		
                $data['page_start_index']= (($page-1) * $filter_page_limit) + 1; 
                $data['filter_page_limit']= $filter_page_limit; 
                
                // URL for pagination
		$pagination_url = $url; 
                
                $data['delete'] = $this->url->link('digital_marketing/custom_url/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
                $data['add'] = $this->url->link('digital_marketing/custom_url/add', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
                
                $filter_data = array(
                        'filter_query'	  		=> $filter_query,
                        'filter_keyword'	=>  $filter_keyword,
                        'filter_meta_title'	  	=> $filter_meta_title,
                        'start'           => ($page - 1) * $filter_page_limit,
			'limit'           => $filter_page_limit
		);
                
                
                $custom_urls = $this->model_digital_marketing_custom_url->getCustomUrlList($filter_data);
                $data['custom_urls'] = array();
                foreach ($custom_urls as $result) {
                
                    $data['custom_urls'][] = array(
				//'id' 	=> $result['id'],
				'url_alias_id' 	=> $result['url_alias_id'],
				'query' 	=> $result['query'],
				'keyword' 	=> $result['keyword'],
				'url_type' 	=> $result['url_type'], 
				'title' 	=> $result['title'],
				'description' 	=> $result['description'],
				'short_description' 	=> $result['short_description'],
				'meta_title' 	=> $result['meta_title'],
				'meta_description' 	=> $result['meta_description'],
				'is_custom' 	=> $result['is_custom'],
				'edit'       => $this->url->link('digital_marketing/custom_url/edit', 'token=' . $this->session->data['token'] . '&custom_url_id=' . $result['url_alias_id'] . $url, 'SSL'),
				'delete'       => $this->url->link('digital_marketing/custom_url/delete', 'token=' . $this->session->data['token'] . '&custom_url_id=' . $result['url_alias_id'] . $url, 'SSL'),
				
			);
                }
                
                $data['no_records'] = $data['text_no_records'];
                
                $custom_url_total = $this->model_digital_marketing_custom_url->getTotalCustomUrls($filter_data);
                $data['custom_url_total'] = $custom_url_total;
                $pagination = new Pagination();
                $pagination->total = $custom_url_total;
		$pagination->page = $page;
		$pagination->limit = $filter_page_limit;
		$pagination->url = $this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
                $data['results'] = sprintf($data['text_pagination'],
									($custom_url_total) ? (($page - 1) * $filter_page_limit) + 1 : 0,
									((($page - 1) * $filter_page_limit) > ($custom_url_total - $filter_page_limit)) ? $custom_url_total : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
										$custom_url_total, ceil($custom_url_total / $filter_page_limit));
                
		
                $data['page_limit_array'] = array('30','60','100','200','500','1000');
                
                $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer'); 
                //echo "<pre>"; print_r($data); die;
                $this->response->setOutput($this->load->view('digital_marketing/custom_url_list.tpl', $data));
	}
        
        public function add() {
		$this->load->language('digital_marketing/custom_url');
                
                //load model
                $this->load->model('digital_marketing/custom_url');
                
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
                    
                        $url = '';
                        if (isset($this->request->get['filter_query'])) {
                            $url .= '&filter_query=' . $this->request->get['filter_query'];
                        }    
                        if (isset($this->request->get['filter_page_limit'])) {
                            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                        } 
                        if (isset($this->request->get['filter_keyword'])) {
                            $url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
                        }        
                        if (isset($this->request->get['filter_meta_title'])) {
                            $url .= '&filter_meta_title=' . $this->request->get['filter_meta_title'];
                        } 
                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];  
                        } 
                        //if url typr product the create Orignal URL for insert in db table    
                        if($this->request->post['url_type'] == 'product'){
                            $this->request->post['query'] = 'product_id=' . $this->request->post['search_id'];
                        }
                        $this->model_digital_marketing_custom_url->addCustomUrls($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
        
        
        public function edit() {
            
		$this->load->language('digital_marketing/custom_url');
                
                //load model
                $this->load->model('digital_marketing/custom_url');
                
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    
                        
                        $url = '';
                        if (isset($this->request->get['filter_query'])) {
                            $url .= '&filter_query=' . $this->request->get['filter_query'];
                        }    
                        if (isset($this->request->get['filter_page_limit'])) {
                            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                        } 
                        if (isset($this->request->get['filter_keyword'])) {
                            $url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
                        }        
                        if (isset($this->request->get['filter_meta_title'])) {
                            $url .= '&filter_meta_title=' . $this->request->get['filter_meta_title'];
                        } 
                        
                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];  
                        } 
                        //if url typr product the create Orignal URL for insert in db table    
                        if($this->request->post['url_type'] == 'product'){
                            $this->request->post['query'] = 'product_id=' . $this->request->post['search_id'];
                        }
                        $this->model_digital_marketing_custom_url->editCustomUrls($this->request->get['custom_url_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
        
        public function delete() {
                
		$this->load->language('digital_marketing/custom_url');
                
                //load model
                $this->load->model('digital_marketing/custom_url');
                
		$this->document->setTitle($this->language->get('heading_title'));
                
                if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validateDelete()) {
                        
                        $this->model_digital_marketing_custom_url->deleteCustomUrl($this->request->get['custom_url_id'], $this->request->post);

			$this->session->data['delete_success'] = $this->language->get('text_delete_success');
                        
                        $url = '';
                        if (isset($this->request->get['filter_query'])) {
                            $url .= '&filter_query=' . $this->request->get['filter_query'];
                        }    
                        if (isset($this->request->get['filter_page_limit'])) {
                            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
                        } 
                        if (isset($this->request->get['filter_keyword'])) {
                            $url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
                        }        
                        if (isset($this->request->get['filter_meta_title'])) {
                            $url .= '&filter_meta_title=' . $this->request->get['filter_meta_title'];
                        } 
                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];  
                        } 
                        
                        

			$this->response->redirect($this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
                $this->getList();
	}
        
        
        
        protected function getForm() {
            
            $data = array(); 
            // Autoloading the lanugage
            $this->load->autoLoadLanguage('digital_marketing/custom_url', $data);
            
            $data['text_form'] = !isset($this->request->get['custom_url_id']) ? $data['text_add'] : $data['text_edit'];
            
            //handel errors
            if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            } else {
                $data['error_warning'] = ''; 
            }
            
            if (isset($this->error['query'])) {
                $data['error_query'] = $this->error['query'];
            }else{
                $data['error_query'] = '';
            }
            
            if (isset($this->error['keyword'])) {
                $data['error_keyword'] = $this->error['keyword'];
            }else{
                $data['error_keyword'] = ''; 
            }
            
            if (isset($this->error['url_type'])) {
                $data['error_url_type'] = $this->error['url_type'];
            }else{
                $data['error_url_type'] = ''; 
            }
            
            if (isset($this->error['search_id'])) {
                $data['error_search_id'] = $this->error['search_id'];
            }else{
                $data['error_search_id'] = '';  
            }
            
            if (isset($this->error['title'])) {
                $data['error_title'] = $this->error['title'];
            }else{
                $data['error_title'] = ''; 
            }
            
            if (isset($this->error['meta_title'])) {
                $data['error_meta_title'] = $this->error['meta_title'];
            }else{
                $data['error_meta_title'] = ''; 
            }
            
            if (isset($this->error['meta_description'])) {
                $data['error_meta_description'] = $this->error['meta_description'];
            }else{
                $data['error_meta_description'] = ''; 
            }
            
            
            //get data
            if (isset($this->request->post['query'])) {
                $data['query'] = $this->request->post['query'];
            } else {
                $data['query'] = '';
            }
            
            if (isset($this->request->post['keyword'])) {
                $data['keyword'] = $this->request->post['keyword'];
            } else {
                $data['keyword'] = '';
            }
            
            if (isset($this->request->post['is_redirect_301'])) {
                $data['is_redirect_301'] = $this->request->post['is_redirect_301'];
            } else {
                $data['is_redirect_301'] = "0";
            }
            
            if (isset($this->request->post['title'])) {
                $data['title'] = $this->request->post['title'];
            } else {
                $data['title'] = '';
            }
            
            if (isset($this->request->post['description'])) {
                $data['description'] = $this->request->post['description'];
            } else {
                $data['description'] = '';
            }
            
            if (isset($this->request->post['short_description'])) {
                $data['short_description'] = $this->request->post['short_description'];
            } else {
                $data['short_description'] = '';
            }
            
            if (isset($this->request->post['url_type'])) {
                $data['url_type'] = $this->request->post['url_type'];
            } else {
                $data['url_type'] = '';
            }
            
            if (isset($this->request->post['search_id'])) {
                $data['search_id'] = $this->request->post['search_id'];
            } else {
                $data['search_id'] = '';
            }
            
            if (isset($this->request->post['meta_title'])) {
                $data['meta_title'] = $this->request->post['meta_title'];
            } else {
                $data['meta_title'] = '';
            }
            
            if (isset($this->request->post['meta_description'])) {
                $data['meta_description'] = $this->request->post['meta_description'];
            } else {
                $data['meta_description'] = '';
            }
            
            //echo "<pre>"; print_r($data); die;
            $url = '';
            if (isset($this->request->get['filter_query'])) {
                $url .= '&filter_query=' . $this->request->get['filter_query'];
            }    
            if (isset($this->request->get['filter_page_limit'])) {
                $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
            } 
            if (isset($this->request->get['filter_keyword'])) {
                $url .= '&filter_keyword=' . $this->request->get['filter_keyword'];
            }        
            if (isset($this->request->get['filter_meta_title'])) {
                $url .= '&filter_meta_title=' . $this->request->get['filter_meta_title'];
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
                    'href' => $this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $url, 'SSL')
            );
            
            if ($this->error && !isset($this->error['warning'])) {
                $this->error['warning'] = $data['error_warning'];
            }
            
            if (!isset($this->request->get['custom_url_id'])) {
                    $data['action'] = $this->url->link('digital_marketing/custom_url/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
            } else {
                    $data['action'] = $this->url->link('digital_marketing/custom_url/edit', 'token=' . $this->session->data['token'] . '&custom_url_id=' . $this->request->get['custom_url_id'] . $url, 'SSL');
            }
            
            
           
            
            // Get custom_url_detail for edit case 
            if (isset($this->request->get['custom_url_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                $custom_url_info = $this->model_digital_marketing_custom_url->getCustomUrlById($this->request->get['custom_url_id']);
                //echo "<pre>"; print_r($custom_url_info); die;
                $data['id'] = $custom_url_info['id'];
                $data['url_alias_id'] = $custom_url_info['url_alias_id'];
                $data['title'] = $custom_url_info['title'];
                $data['description'] = $custom_url_info['description'];
                $data['short_description'] = $custom_url_info['short_description'];
                $data['url_type'] = $custom_url_info['url_type'];
                $data['search_id'] = $custom_url_info['search_id'];
                $data['meta_title'] = $custom_url_info['meta_title'];
                $data['meta_description'] = $custom_url_info['meta_description'];
                $data['query'] = trim(html_entity_decode($custom_url_info['query']));
                $data['keyword'] = $custom_url_info['keyword'];
                $data['is_redirect_301'] = $custom_url_info['is_redirect_301'];
                $data['is_custom'] = $custom_url_info['is_custom'];
            }
            
            $data['cancel'] = $this->url->link('digital_marketing/custom_url', 'token=' . $this->session->data['token'] . $url, 'SSL');
            $data['token'] = $this->session->data['token'];
            
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            
            $this->response->setOutput($this->load->view('digital_marketing/custom_url_form.tpl', $data));
        }
        
        protected function validateForm() {
            
                $data = array(); 
                // Autoloading the lanugage
                $this->load->autoLoadLanguage('digital_marketing/custom_url', $data); 
                
                //load model
                $this->load->model('digital_marketing/custom_url');
                
                if (!$this->user->hasPermission('modify', 'digital_marketing/custom_url')) { 
			$this->error['warning'] = $data['error_permission'];
		}
                
                //echo "<pre>"; print_r($this->request->post); //die;

		if ( !empty($this->request->post['query']) && utf8_strlen(trim($this->request->post['query'])) < 3){
                    $this->error['query'] = $data['error_query'];
		}
                
                //check valid orignal url
                if( isset($this->session->data['url_validation'])) { 
                    //check valid orignal search url
                    if( isset($this->session->data['search_url_validation'])) { 
                        //echo $this->request->post['query']; //die;
                        
                        $search_data = explode("&", str_replace('amp;', '', $this->request->post['query']));
                        if (strpos($search_data[1], '#!') !== false) {
                            $pos = strpos($search_data[1], '#!');
                            $search_data[1] = substr($search_data[1], 0, $pos);
                        }
                        $search = explode('=', $search_data[1]);
                        if($search['0'] != 'search' && $search['1'] == ''){ 
                            $this->error['query'] = $data['error_search_url'];
                        }
                        
//                        if (strpos($this->request->post['query'], 'category&search') !== false) { 
//                            $this->error['query'] = $data['error_search_url'];
//                        }
                        
                    }
                    //check valid orignal defaul url
                    else if (strpos($this->request->post['query'], '#!filter') === false && strpos($this->request->post['query'], 'category&amp;search') === false) { 
                        //check category slug exits or not in url_alias where is_custom = 0
                        $category_slug_array = explode("/", $this->request->post['query']);
                        $category_slug = end($category_slug_array);     
                        $is_category_slug = $this->model_digital_marketing_custom_url->isSlugExits($category_slug,''); 
                        if($is_category_slug == FALSE){ 
                            $this->error['query'] =  $data['error_url'];
                        }
                    }
                    
                    
                    
                }
                
                
                if( preg_match('/\s/',trim($this->request->post['keyword'])) ){
                    $this->error['keyword'] = $data['error_keyword'];
                }
                if ( !preg_match('/\s/',trim($this->request->post['keyword'])) && (utf8_strlen(trim($this->request->post['keyword'])) < 3) || (utf8_strlen(trim($this->request->post['keyword'])) > 1024) ) {
			$this->error['keyword'] = $data['error_keyword'];
		}
                
                //check slug alresdy exits
                $keyword = trim($this->request->post['keyword']);
                $custom_url_id = '';
                if( isset($this->request->get['custom_url_id']) ){
                    $custom_url_id = trim($this->request->get['custom_url_id']);
                }
                $is_slug_exits = $this->model_digital_marketing_custom_url->isSlugExits($keyword,$custom_url_id);
                if($is_slug_exits == TRUE){
                    $this->error['keyword'] = $data['error_already_exits_keyword'];
                }
                
                //check slug categorieswise
                if (strpos($keyword, '#!') !== false) {
                    $pos = strpos($keyword, '#!');
                    $keyword = substr($keyword, 0, $pos);
                }
                $key_part = explode('/', $keyword);
                for($i = 0; $i < count($key_part);$i++){
                    //if($i == count($key_part) - 1){ 
                        $is_slug_exits = $this->model_digital_marketing_custom_url->isSlugExits($key_part[$i],$custom_url_id);
                        if($is_slug_exits == TRUE){
                            $this->error['keyword'] = $data['error_already_exits_keyword'];
                            break;
                        }
                    //}  
                }
                
                if ( ($this->request->post['url_type'] == '0') ) {
			$this->error['url_type'] = $data['error_url_type'];
		} 
                
                //check filter search have value 
                $filters_data = explode('#!', $this->request->post['query']);
                if(isset($filters_data[1])){
                    $filters = explode("&", str_replace('amp;', '', $filters_data[1]));
                    $filter_search_key = '';
                    $filter_search_value = '';
                    for($i = 0; $i < count($filters); $i++){
                        $filtr = explode('=', $filters[$i]);
                        if($filtr['0'] == 'search'){ 
                            $filter_search_key = $filtr['0'];
                            $filter_search_value = trim(html_entity_decode($filtr['1']));
                            break;
                        }
                    }
                }
                
                //set session for search_with_filter_validation
                if(isset($this->session->data['search_with_filter_validation'])) unset($this->session->data['search_with_filter_validation']);
                if(strpos($this->request->post['query'], "category&amp;search") && strpos($this->request->post['query'], '#!filter')){
                    $this->session->data['search_with_filter_validation'] = '1'; 
                }
                
                
                //check url type for Orignal URL have search
                $search_data = explode("&", str_replace('amp;', '', $this->request->post['query']));
                if(isset($search_data[1])){
                    if (strpos($search_data[1], '#!') !== false) {
                        $pos = strpos($search_data[1], '#!');
                        $search_data[1] = substr($search_data[1], 0, $pos);
                    }
                    $search = explode('=', $search_data[1]);
                    if($search['0'] == 'search' && $this->request->post['url_type'] != 'category_search'){         
                        $this->error['url_type'] = $data['error_url_type_search']; 
                    }
                    if(isset($this->session->data['search_with_filter_validation'])){
                        if( ($search['0'] == 'search' && $search['1'] == '') || ($filter_search_key == 'search' && $filter_search_value == '')){  
                            $this->error['query'] = $data['error_search_url'];
                        }
                    }else{ 
                        if($search['0'] == 'search' && $search['1'] == ''){  
                            $this->error['query'] = $data['error_search_url'];
                        }
                    }

                }
                
                
                
                
                if( isset($this->session->data['search_id_validation'])) {   
                    if ( !preg_match('/^[1-9][0-9]*$/', trim($this->request->post['search_id'])) || $this->request->post['search_id'] == 0) {
                        $this->error['search_id'] = $data['error_search_id'];
                    }    
                }
                
                
                if (utf8_strlen(trim($this->request->post['title'])) < 3){
			$this->error['title'] = $data['error_title'];
		}
                
                if (utf8_strlen(trim($this->request->post['meta_title'])) < 3){
			$this->error['meta_title'] = $data['error_meta_title'];
		}  
                
                if ((utf8_strlen($this->request->post['meta_description']) < 50)) {
			$this->error['meta_description'] = $data['error_meta_description'];
		}  
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $data['error_warning'];
		}
                
		
		return !$this->error;
	}
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'digital_marketing/custom_url')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        
        function setSessionForValidation() {
            
            //unset session 
            if(isset($this->session->data['search_id_validation'])) unset($this->session->data['search_id_validation']);
            if(isset($this->session->data['url_validation'])) unset($this->session->data['url_validation']);
            if(isset($this->session->data['search_url_validation'])) unset($this->session->data['search_url_validation']);
            
            if($this->request->post['url_type'] == 'product'){
                $this->session->data['search_id_validation'] = '1';
            }else if($this->request->post['url_type'] == 'category'){
                $this->session->data['search_id_validation'] = '1';
                $this->session->data['url_validation'] = '1';
            }elseif($this->request->post['url_type'] == 'category_search'){ 
                $this->session->data['url_validation'] = '1'; 
                $this->session->data['search_url_validation'] = '1';  
            }
            
        }
        
        function autoFillCategoryId(){
            
            $data_lang = array();
            $this->load->autoLoadLanguage('digital_marketing/custom_url', $data_lang); 
            
            $status = false;
            $msg = $data_lang['error_url'];  
            $data = array();
            
            $orignal_url = $this->request->post['orignal_url'];
            if (strpos($orignal_url, '#!') !== false) {
                $pos = strpos($orignal_url, '#!');
                $orignal_url = substr($orignal_url, 0, $pos);
            }
            //echo $orignal_url; die;
            $categories = explode("/", $orignal_url);
            $category = end($categories);
            
            //echo $category; die;
            $this->load->model('digital_marketing/custom_url');  
            $category_data = $this->model_digital_marketing_custom_url->getCategoryByName($category); 
            //echo "<pre>"; print_r($category_data); die;
            
            if(count($category_data) > 0){
                $data['category_id'] = $category_data['cat_id'];
                $status = true;
                $msg = '';
            }
            $data['status'] = $status;
            $data['msg'] = $msg; 
            //echo "<pre>"; print_r($data); die('bye');
            echo json_encode($data);
            
        }
        
        
}