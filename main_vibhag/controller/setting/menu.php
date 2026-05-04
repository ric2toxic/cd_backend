<?php
/**
* Controller class to manage Khufiya vibhag left penal menus. 
* . 
* @author: MSA, 2017
*/
class ControllerSettingMenu extends Controller {
    
    private $error = array();
    
    /*
     * Index - controller default method to list all menus
     * @return Array[] menu listing
     * @Author MSA Nov 2017
     *  */
    public function index() {

        $this->load->language('setting/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/menu');

        $this->getList();
    }
    
    /*
     * getList - method to get menu listing 
     * @param  integer page number
     * @return Array[] menu listing
     * @Author MSA Nov 2017
     *  */
    protected function getList() {

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage

        $this->load->autoLoadLanguage('setting/menu', $data);

            $url = '';

            if (isset($this->request->get['sort'])) {
                    $sort = $this->request->get['sort'];
            } else {
                    $sort = 'title';
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
                            'href' => $this->url->link('setting/menu', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['add'] = $this->url->link('setting/menu/add', 'token=' . $this->session->data['token'], 'SSL');

            $data['delete'] = $this->url->link('setting/menu/delete', 'token=' . $this->session->data['token'], 'SSL');

            $data['menu'] = array();
           
            $data['menu'][] = array(
                            'id' => 0,
                            'name'     => $this->config->get('config_name') . $data['text_default'],
                            'url'      => HTTP_CATALOG,
                            'edit'     => $this->url->link('setting/menu', 'token=' . $this->session->data['token'], 'SSL')
            );

            $menu_total = $this->model_common_menu->getTotalMenus();

            $results = $this->model_common_menu->getAllAdminMenu();
            
            $data['menus'] = $this->buildTree($results);

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
            
            $pagination = new Pagination();
            $pagination->total = $menu_total;
            $pagination->page = $page;
            $pagination->limit = $this->config->get('config_limit_admin');
            $pagination->url = $this->url->link('setting/menu', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($data['text_pagination'], ($menu_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($menu_total - $this->config->get('config_limit_admin'))) ? $menu_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $menu_total, ceil($menu_total / $this->config->get('config_limit_admin')));

            $data['sort'] = $sort;
            $data['order'] = $order;
            
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            
            $this->response->setOutput($this->load->view('setting/menu_list.tpl', $data));
    }
    
    /*
     * add - method to add new menu 
     * @param  Array[] data list
     * @return 
     * @Author MSA Nov 2017
     *  */    
    public function add() {
        $this->load->language('setting/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/menu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

                //pr($this->request->post); die;

                $this->model_common_menu->addMenu($this->request->post);

                $this->session->data['success'] = $this->language->get('text_success');

                $url = '';

                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

                if (isset($this->request->get['page'])) {
                        $url .= '&page=' . $this->request->get['page'];
                }

                $this->response->redirect($this->url->link('setting/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }
    
    /*
     * edit - method to edit menu details
     * @param  Array[] data list
     * @return 
     * @Author MSA Nov 2017
     *  */ 
    public function edit() {
        $this->load->language('setting/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/menu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

                $this->model_common_menu->editMenu($this->request->get['id'], $this->request->post);

                $this->session->data['success'] = $this->language->get('text_success');

                $url = '';

                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

                if (isset($this->request->get['page'])) {
                        $url .= '&page=' . $this->request->get['page'];
                }

                $this->response->redirect($this->url->link('setting/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }
    
    /*
     * delete - method to delete menu
     * @param  integer menu id 
     * @return list view
     * @Author MSA Nov 2017
     *  */ 
    public function delete() {
        
        $this->load->language('setting/menu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/menu');
        
        if (isset($this->request->get['delid']) && $this->validateDelete()) {
            
                $this->model_common_menu->deleteMenu($this->request->get['delid']);

                $this->session->data['success'] = $this->language->get('text_delete');

                $this->response->redirect($this->url->link('setting/menu', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }
    
    /*
     * validateDelete - method validate user permission for this action
     * @return boolean 
     * @Author MSA Nov 2017
     *  */ 
    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'setting/menu')) {
                $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('common/menu');

        $menu_id = $this->request->get['delid'];
        
        if (!$menu_id) { 
                $this->error['warning'] = $this->language->get('error_default');
        }
        
        $sub_menu_total = $this->model_common_menu->getSubMenuItemsByMenuId($menu_id);
        
        if ($sub_menu_total) {
                $this->error['warning'] = sprintf($this->language->get('error_menu'), $sub_menu_total);
        }

        return !$this->error;
    }

    /*
     * getForm - method to get form for add/edit action
     * @param  Array[] data list
     * @return form view
     * @Author MSA Nov 2017
     *  */ 
    protected function getForm() {
        $data = array(); // Initializing the data array to be passed on to template files
       // Autoloading the lanugage
       $this->load->autoLoadLanguage('setting/menu', $data);

           $data['text_form'] = !isset($this->request->get['id']) ? $data['text_add'] : $data['text_edit'];
           $data['user_id']  = isset($this->request->get['id']) ? $this->request->get['id'] : '';


           if (isset($this->error['warning'])) {
                   $data['error_warning'] = $this->error['warning'];
           } else {
                   $data['error_warning'] = '';
           }

           if (isset($this->error['title'])) {
                   $data['error_title'] = $this->error['title'];
           } else {
                   $data['error_title'] = '';
           }

           if (isset($this->error['error_permission_controller'])) {
                   $data['error_permission_controller'] = $this->error['error_permission_controller'];
           } else {
                   $data['error_permission_controller'] = '';
           }

           if (isset($this->error['link'])) {
                   $data['error_link'] = $this->error['link'];
           } else {
                   $data['error_link'] = '';
           }

           $url = '';

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
                   'href' => $this->url->link('setting/menu', 'token=' . $this->session->data['token'] . $url, 'SSL')
           );

           if (!isset($this->request->get['id'])) {
                   $data['action'] = $this->url->link('setting/menu/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
           } else {
                   $data['action'] = $this->url->link('setting/menu/edit', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'] . $url, 'SSL');
           }

           $data['cancel'] = $this->url->link('setting/menu', 'token=' . $this->session->data['token'] . $url, 'SSL');

           if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                $menu_info = $this->model_common_menu->getAdminMenu($this->request->get['id']);
           }
           
           if (isset($this->request->post['parent'])) {
                   $data['parent'] = $this->request->post['parent'];
           } elseif (!empty($menu_info)) {
                   $data['parent'] = $menu_info['parent'];
           } else {
                   $data['parent'] = '';
           }
           
           if (isset($this->request->post['title'])) {
                   $data['title'] = $this->request->post['title'];
           } elseif (!empty($menu_info)) {
                   $data['title'] = $menu_info['title'];
           } else {
                   $data['title'] = '';
           }

           if (isset($this->request->post['permission'])) {
                   $data['permission'] = $this->request->post['permission'];
           } elseif (!empty($menu_info)) {
                   $data['permission'] = $menu_info['permission'];
           } else {
                   $data['permission'] = '';
           }

           if (isset($this->request->post['link'])) {
                   $data['link'] = $this->request->post['link'];
           } elseif (!empty($menu_info)) {
                   $data['link'] = $menu_info['link'];
           } else {
                   $data['link'] = '';
           }

           if (isset($this->request->post['icon'])) {
                   $data['icon'] = $this->request->post['icon'];
           } elseif (!empty($menu_info)) {
                   $data['icon'] = $menu_info['icon'];
           } else {
                   $data['icon'] = '';
           }

           if (isset($this->request->post['sub_menu'])) {
                   $data['sub_menu'] = $this->request->post['sub_menu'];
           } elseif (!empty($menu_info)) {
                   $data['sub_menu'] = $menu_info['sub_menu'];
           } else {
                   $data['sub_menu'] = '';
           }

           if (isset($this->request->post['status'])) {
                   $data['status'] = $this->request->post['status'];
           } elseif (!empty($menu_info)) {
                   $data['status'] = $menu_info['status'];
           } else {
                   $data['status'] = '1';
           }
           
           if (isset($this->request->post['menu_order'])) {
                   $data['menu_order'] = $this->request->post['menu_order'];
           } elseif (!empty($menu_info)) {
                   $data['menu_order'] = $menu_info['menu_order'];
           } else {
                   $data['menu_order'] = '0';
           }
           

           $source = $this->model_common_menu->getMenusForDropDownOptions();    
           $menu_tree = $this->buildTree($source);
           $data['tree_options'] = $this->buildOptions($menu_tree,$data['parent']);

           $data['token'] = $this->request->get['token'];
           $data['header'] = $this->load->controller('common/header');
           $data['column_left'] = $this->load->controller('common/column_left');
           $data['footer'] = $this->load->controller('common/footer');

           $this->response->setOutput($this->load->view('setting/menu_form.tpl', $data));
    }
     
    /*
     * buildTree - method to generate parent-child tree structure
     * @param  Array[] data element
     * @param  integer parent id
     * @return Array
     * @Author MSA Nov 2017
     *  */
    protected function buildTree($elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) { 

            if ($element['parent'] == $parentId) {

                $children = $this->buildTree($elements, $element['id']);

                if ($children) {

                    $element['children'] = $children;

                }

                $branch[] = $element;
            }
        }
        return $branch;
    }
    
    /*
     * buildOptions - method to generate parent-child tree structure for dropdown
     * @param  Array[] data element
     * @param  integer target id for selected
     * @return Options string
     * @Author MSA Nov 2017
     *  */
    protected function buildOptions($arr, $target, $parent = NULL) {

        $html = "";

        foreach ( $arr as $key => $v )
        {
          if ( $v['id'] == $target ) {

            $html .= "<option value='".$v['id']."' selected>$parent {$v['title']}</option>\n";

          } else {

            $html .= "<option value='".$v['id']."'>$parent {$v['title']}</option>\n";

          }
          if (array_key_exists('children', $v)) {

              $html .= $this->buildOptions($v['children'],$target,$parent . $v['title']." > ");

          }

        }
        return $html;
    }
    
    /*
     * validateForm - method to validate form fields
     * @param  Array[] data 
     * @return Array[] error list
     * @Author MSA Nov 2017
     *  */
    protected function validateForm() {

        if (!$this->user->hasPermission('modify', 'setting/menu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['title']) < 1) || (utf8_strlen($this->request->post['title']) > 200)) {
                $this->error['title'] = $this->language->get('error_title');
        }

        if ((utf8_strlen(trim($this->request->post['permission'])) < 1) || (utf8_strlen(trim($this->request->post['permission'])) > 500)) {
                $this->error['error_permission_controller'] = $this->language->get('error_permission_controller');
        }

        if ((utf8_strlen(trim($this->request->post['link'])) < 1) || (utf8_strlen(trim($this->request->post['link'])) > 500)) {
                $this->error['link'] = $this->language->get('error_link');
        }
        return !$this->error;
    }
        
        
}