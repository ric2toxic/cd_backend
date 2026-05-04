<?php
class ControllerCommonMenu extends Controller {

	private $_encrypt_method = "AES-256-CBC";
	private $_secret_key = 'abc123093';
	private $_order_pickup  = array(9,16);

	public function index() {
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', 'haha123'), 0, 16);

        $data = array_merge(isset($data) ? $data : array(), $this->load->language('multiseller/multiseller'));
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('common/menu', $data);
        
        $this->load->model('user/user');
        $user_data = $this->model_user_user->getUser($this->session->data['user_id']);
        $controller_access  = array();
        $methods_access     = array();
        if(isset($user_data['permission']['access'])) {
            if(isset($user_data['permission']['access']['methods'])) {
                $methods_access = $user_data['permission']['access']['methods'];
                unset($user_data['permission']['access']['methods']);
            }
            $controller_access = $user_data['permission']['access'];
        }
        
        $user_permissions = $user_data['permission'];
        
        $this->load->model('common/menu');
        
        $left_menus = $this->model_common_menu->getAllLeftPenalMenus($controller_access);
        
        // IF user has default landing page status, hide dahsboard menu from left penal
        if(!empty($user_data['dont_show_dashboard'])){

            array_shift($left_menus);
        }

        $data['text_opencart'] = $this->language->get('text_opencart');
        $data['text_tracking'] = $this->language->get('text_tracking');
        $data['text_customer_field'] = $this->language->get('text_customer_field');
        
        $new_menu = array();
        if(!empty($left_menus)) {
            foreach($left_menus as $key=>$value)
            {   
                $permission = $value['permission'];
                if(substr_count($permission,'/') > 1){
                    
                    $permission_list = explode('/',$permission);
                    $controller = $permission_list[0] .'/'.$permission_list[1];
                    $method  = $permission_list[2];
                    if(array_key_exists($controller, $methods_access))
                    {
                        $allowed_methods = $methods_access[$controller];
                        if(in_array($method, $allowed_methods)){
                            $new_menu[] = $value;
                        } 
                    }
                }else{
                    $new_menu[] = $value;;
                }
            }
        }

        $data['menu_tree'] = $this->recurse($new_menu);
                
        return $this->load->view('common/menu.tpl', $data);

        }
        
        /*
        * recurse - method to generate menus in ul>li format 
        * @param  Array[] data element
        * @param  integer parent id
        * @param  integer level id
        * @param  string title 
        * @return Array
        * @Author MSA Nov 2017
        *  */
        protected function recurse($menus, $parent = 0, $level = 0, $title = '')
        {
            $class = '';
            
            if($title == '') 
            {
                $class = ' id="menu" ';
            }
              
            $html = '<ul'.$class.'>';
            
            foreach($menus as $index => $menu)
            {
                if($menu['parent'] == $parent)
                {
                    $link = '';
                    
                    if($menu['sub_menu'])
                    {
                        $link = 'class="parent"';
                        
                    } else{
                        
                        $count = substr_count($menu['permission'],'/') ;
                        
                        if ( $count > 1 )
                        {
                            $link = 'href="'.$this->url->link($menu['link'], 'token=' . $this->session->data['token'], 'SSL') . '&permission_id='.$menu['id'] .'"';
                        }else{
                            $link = 'href="'.$this->url->link($menu['link'], 'token=' . $this->session->data['token'], 'SSL') .'"';
                        }
                        
                    }
                    
                    $html .= '<li id='.str_replace(' ','_',strtolower($menu['title'])).'>';
                        $html .= '<a '.$link.'>';
                        
                            if(!empty($menu['icon'])) {
                                $html .= '<i class="'.$menu['icon'].'" aria-hidden="true"></i>';
                            }
                            
                        $html .= '<span>' . $menu['title'] . '</span>';
                    $html .= '</a>';
                    
                    
                    $sub = $this->recurse($menus, $menu['id'], $level+1, $menu['title']);
                    
                    if($sub != '<ul></ul>')
                    {
                        $html .= $sub;
                    }
                        
                    
                    $html .= '</li>';
                }
            }
            return $html . '</ul>';
        }
        
}
