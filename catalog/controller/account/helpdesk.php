<?php 

class ControllerAccountHelpdesk extends Controller{ 
   
    public function index(){
        $this->login_check();
        $this->load->model('account/helpdesk');
        $customer_id = $this->customer->getId();        
        $result      = $this->model_account_helpdesk->getHelpdeskId($customer_id);      
        if(empty($result['helpdesk_id'])){ 
          /*
            Now if the heldpesk_id in oc_customer is 0 then we will call createHelpdeskCustomer 
            if customer already exist with provided details then it will return helpdesk_id of existing customer in hd_cusotmer otherwise it will add customer and return its id(helpdesk_id).
          */   
          $this->createHelpdeskCustomer();          
        } 
        $this->viewHelpdeskTicket();          
    }  

    // public function checkContactExistanceOnHelpdesk(){
    //     $this->load->model('account/helpdesk');
    //     $customer_id = $this->customer->getId();
    //     $customer_details = $this->model_account_helpdesk->getHelpdeskId($customer_id);
    //     $customer_data['email']    = $customer_details['email'];
    //     $customer_data['password'] = $customer_details['password'];
    //     $customer_data['telephone'] = $customer_details['telephone'];
    //     $customer_data['wsb_id']   = $customer_details['customer_id'];
    //     $customer_data['customer_access_token'] = $customer_details['password'];
    //     $obj = new Helpdesk($this);
    //     $customer_data =  $obj->checkContactIfExist($customer_data);
    //     if(!empty($customer_data['customer_id'])){
    //         $helpdesk_id = $customer_data['customer_id'];
    //         $update = $this->model_account_helpdesk->updateHelpdeskId($helpdesk_id,$customer_id); 
    //     }else{ 
    //         $this->createHelpdeskCustomer();
    //     } 
    //     $this->viewHelpdeskTicket();
    // }    

    public function login_check(){
        if (!$this->customer->isLogged()) {
            // $this->session->data['redirect'] = $this->url->link('account/helpdesk', '', 'SSL');
            $this->response->redirect($this->url->link('account/login', '', 'SSL'));
        }
    }

    public function createHelpdeskCustomer(){
                                                                       
        $this->load->model('account/helpdesk');

        $customer_id = $this->customer->getId();
        // echo $customer_id;
        // die(); 

        $result = $this->model_account_helpdesk->getHelpdeskId($customer_id);        
        // echo "<pre>";
        // print_r($result);
        // die();
        $data = array();          
        $data['name']    = $result['firstname']. " " . $result['lastname'];          
        $data['email']   = $result['email'];
        $data['telephone']   = $result['telephone'];
        $data['customer_access_token'] = $result['password'];                   
        $data['wsb_id']  = $result['customer_id'];
        $data['password']  = $result['password'];
        $obj = new Helpdesk($this); 
        $helpdesk_id =  $obj->createContact($data);
        if(!empty($helpdesk_id)){        
        $this->model_account_helpdesk->updateHelpdeskId($helpdesk_id['customer_id'],$customer_id); 
        
        }
    
    }  

    public function common(&$data){ 

        $this->load->model('tool/image'); 

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }


        $data['request_uri'] = $_SERVER['REQUEST_URI'];
        if ($this->request->server['HTTPS']) {
            $data['in_store'] = 'https://'.INDIA_STORE_HOST;
            $data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
        } else {
            $data['in_store'] = 'http://'.INDIA_STORE_HOST;
            $data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
        }       

        $header_language = array();
        $footer_language = array();
        $login_language = array();
       
        $this->load->autoLoadLanguage('common/header', $header_language);
        $this->load->autoLoadLanguage('common/footer', $footer_language);
        $this->load->autoLoadLanguage('account/login', $login_language);
        $data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
        $data['direction'] = $header_language['direction'];
        
         $store_id = (int)($this->config->get('config_store_id'));
         $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        
        $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


        $this->load->language('account/helpdesk');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
        $data['title'] = $this->document->getTitle();
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();


        $language = array(); 
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('account/helpdesk', $language);
        $data['breadcrumbs'][] = array(
        'text' => $language['home'], 
        'href' => $this->url->link('common/home')
        );
      
        $data['breadcrumbs'][] = array(
            'text' => $language['my_orders'],
            'href' => $this->url->link('account/order', '', 'SSL')
        );  

        $data['breadcrumbs'][] = array(
        'text' => $language['tickets_list'],
        'href' => $this->url->link('account/helpdesk/viewHelpdeskTicket', '', 'SSL')
        );  
        
        $data['column_left']  = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        if(CONFIG_IS_MOBILE == 1)
        {   
          $data['footer'] = $this->load->controller('common/footer');
          $data['header'] = $this->load->controller('common/header');
        }         
    }

    public function createHelpdeskTicket(){ 
        $this->login_check();
        $data = array();
        $this->common($data);     
        
        $language = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('account/helpdesk', $language);

        $data['heading_title']      = $language['support_desk'];
        $data['button_back']        = $language['back'];
        $data['button_new_address'] = $language['create_ticket']; 

        $data['create'] = $this->url->link('account/helpdesk/createHelpdeskTicket', '', 'SSL');
        $data['back'] = $this->url->link('account/helpdesk/viewHelpdeskTicket', '', 'SSL');        
        $data['action'] = $this->url->link('account/helpdesk/newTicketdata', '', 'SSL');                  
        $data['create_ticket']      = $language['create_ticket'] ;
        $data['subject']            = $language['subject'];
        $data['entry_subject']      = $language['enter_subject'];
        $data['description']        = $language['description'];
        $data['attachment']         = $language['attach_files'];
        $data['entry_description']  = $language['enter_description'];
        $data['button_continue']    = $language['submit'];   

        $this->load->model('account/helpdesk');
        $data['ticket_type'] = $this->getHelpdeskTicketType();

        if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/helpdesk/new_ticket.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/helpdesk/new_ticket.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('default/template/account/helpdesk/new_ticket.tpl', $data));
        }

    }  
  
    public function newTicketdata(){    
        // echo "<pre>";
        // print_r($this->request->post);
        // print_r($this->request->files);
        // die();

        $ticket['ticket_type_id']     =  $this->request->post['ticket_type_id'];
        $ticket['ticket_subject']     =  $this->request->post['subject'];
        $ticket['ticket_description'] =  $this->request->post['description'];

        $this->load->model('account/helpdesk');  
        $customer_id = $this->customer->getId();       
        $helpdesk_id = $this->model_account_helpdesk->getHelpdeskId($customer_id);
        $ticket['helpdesk_id'] = $helpdesk_id['helpdesk_id'];
        $ticket['wsb_id']      = $helpdesk_id['customer_id'];
        $ticket['email']       = $helpdesk_id['email'];
        $ticket['telephone']   = $helpdesk_id['telephone'];
        $ticket['customer_access_token'] = $helpdesk_id['password'];
        $ticket['password'] = $helpdesk_id['password'];

        // $today = date("Y/m/d");
     
        if(!$this->request->files['attachment_file']['error'] ){
          $ticket['attachment']  = $this->request->files['attachment_file'];   
        }      

        $result = $this->model_account_helpdesk->getHelpdeskId($customer_id);
             
        $obj = new Helpdesk($this);  
            
        $output =  $obj->createTicket($ticket);
        

        if(!empty($output['ticket_id'])){
         $this->session->data['success'] = true;
         $this->response->redirect($this->url->link('account/helpdesk/viewHelpdeskTicket','','SSL'));               
        }              
    }

    public function viewHelpdeskTicket(){
        $this->login_check();
        $filter['page']   = isset($this->request->get['page'])    ? $this->request->get['page'] : '1';
        $filter['status'] = isset($this->request->get['status']) ? $this->request->get['status'] : '';
        $data = array(); 
        $this->common($data); 

        if(isset($this->session->data['success'])){
            $data['success_message'] = "Your ticket has been created."; 
            unset($this->session->data['success']);
        }

        $language = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('account/helpdesk', $language);

        $data['heading_title']      = $language['support_desk'];
        $data['button_back']        = $language['back'];
        $data['button_new_address'] = $language['create_ticket']; 
        $data['all_tickets']        = $language['All_tickets'];

        $data['create'] = $this->url->link('account/helpdesk/createHelpdeskTicket', '', 'SSL');
        $data['back'] = $this->url->link('account/account', '', 'SSL');
        $data['page_url'] = $this->url->link('account/helpdesk', '', 'SSL');
        $data['status'] = $filter['status'];
        $this->load->model('account/helpdesk');  

        $customer_id = $this->customer->getId();


        $customer_data = $this->model_account_helpdesk->gethelpdeskId($customer_id);
        if(empty($customer_data['email'])){
            $data['show_modal'] = TRUE;
        }


        $filter['helpdesk_id'] = $customer_data['helpdesk_id'];
        $filter['customer_id'] = $customer_data['customer_id'];
        $filter['telephone']   = $customer_data['telephone'];
        $filter['password']    = $customer_data['password'];        
        $filter['customer_access_token'] = $customer_data['password'];

        $obj = new Helpdesk($this);       
        $outputs =  $obj->viewAllTickets($filter);
        $data['output'] = array();
        if(!empty($outputs)){ 
            $tickets            = $outputs['data_res'];
            $data['data_count'] = $outputs['data_count'];
            $data['pagination'] = $outputs['pagination'];
            foreach ($tickets as $output){                               
                $output['url'] = $this->url->link('account/helpdesk/viewSingleTicketAndConversation','&ticket_id='.$output['ticket_id'],'SSL');                
                date_default_timezone_set('Asia/Kolkata');
                $d = date('d'.' '.'M', strtotime($output['created_at']));
                $day = date('l', strtotime($output['created_at'])); 
                $hour_minute = date('h:i A', strtotime($output['created_at'])); 
                $output['created_at'] = "Created on". ' ' .$day.','.' ' .$d.' '.'at'.' '.$hour_minute ;
                $ticket_data[] = $output;
            }

            $data['ticket_data'] = $ticket_data;           
        }                    

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/helpdesk/view_tickets.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/helpdesk/view_tickets.tpl', $data));
        } else {
           $this->response->setOutput($this->load->view('default/template/account/helpdesk/view_tickets.tpl', $data));
        }

    }    

    /**
     *Pagination (load more) for the ticket conversation.
     */
                         

    public function conversationLoadMore(){        
        $ticket_id = isset($this->request->post['ticket_id'])?$this->request->post['ticket_id'] :0 ;
        $page      = isset($this->request->post['page']) ? $this->request->post['page'] : 0;
        $this->load->model('account/helpdesk');  
        $customer_id = $this->customer->getId(); 
        $result = $this->model_account_helpdesk->getHelpdeskId($customer_id);
        $single_ticket_param['customer_access_token'] = $result['password'];
        $single_ticket_param['helpdesk_id']  = $result['helpdesk_id'];
        $single_ticket_param['password']     = $result['password'];
        $single_ticket_param['ticket_id']    = $ticket_id;
        $single_ticket_param['page']         = $page;
        $single_ticket_param['customer_id']  = $result['customer_id'];

        $obj = new Helpdesk($this); 

        $output =  $obj->viewsingleTicket($single_ticket_param); 
        // echo "<pre>"; print_r($output['conversation']);
        $data['conversation'] = isset($output['conversation'])? $output['conversation']: '' ; 
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/helpdesk/conversation_load.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/helpdesk/conversation_load.tpl', $data));
        } else {
           $this->response->setOutput($this->load->view('default/template/account/helpdesk/conversation_load.tpl', $data));
        }

    }

    public function viewSingleTicketAndConversation(){
        $this->login_check();
        if(isset($this->request->get['ticket_id']) && !empty(isset($this->request->get['ticket_id'])) ){
            $ticket_id =  $this->request->get['ticket_id'];            
        }else{
            $this->response->redirect($this->url->link('account/helpdesk', '', 'SSL'));

        }
        $page      = isset($this->request->get['page']) ? $this->request->get['page'] : 0 ;

        if(isset($this->request->get['dont_update_read'])){            
         $single_ticket_param['dont_update_read'] =  $this->request->get['dont_update_read']; 
        }else{
         $single_ticket_param['dont_update_read'] = 0;   
        }
        

        $data = array();
        $this->common($data);      
        $language = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('account/helpdesk', $language);

        $data['ticket_id'] = $ticket_id;  
        $single_ticket_param['ticket_id']  = $ticket_id;  
        $single_ticket_param['page']       = $page;    
        $data['heading_title'] = $language['support_desk'];
        $data['button_back'] =   $language['back'] ;
        $data['button_new_address'] = $language['create_ticket'] ; 
        $data['button_continue'] = $language['submit'] ; 
        $data['ticket'] = $language['ticket']; 

        $data['create'] = $this->url->link('account/helpdesk/createHelpdeskTicket', '', 'SSL');
        $data['back']   = $this->url->link('account/helpdesk', '', 'SSL');
        $data['action'] = $this->url->link('account/helpdesk/conversation_reply', '', 'SSL'); 
        $data['close_ticket'] = $this->url->link('account/helpdesk/closeTicket', '', 'SSL');
        $data['conversation_load_url'] = $this->url->link('account/helpdesk/conversationLoadMore', '', 'SSL');

        if(isset($this->session->data['scroll_bottom'])){    
            $data['scroll_to_reply'] = true; 
            unset($this->session->data['scroll_bottom']);
        }
        $this->load->model('account/helpdesk');  

        $customer_id = $this->customer->getId(); 

        $result = $this->model_account_helpdesk->getHelpdeskId($customer_id);
        $single_ticket_param['customer_access_token'] = $result['password'];
        $single_ticket_param['helpdesk_id']  = $result['helpdesk_id'];
        $single_ticket_param['password']     = $result['password'];
        $single_ticket_param['email']        = $result['email'];
        $single_ticket_param['telephone']    = $result['telephone'];
        $single_ticket_param['customer_id']  = $result['customer_id'];

        $data['wsb_cust_data'] = $result; 

        $data['firstname'] = $result['firstname'];
        $upper_case_letters = ucwords($data['firstname']);
        $data['customer_letter'] = substr($upper_case_letters, 0,1);
        $data['lastname'] = $result['lastname'];

        $obj = new Helpdesk($this); 

        $output =  $obj->viewsingleTicket($single_ticket_param); 
        // echo "<pre>";    
        // print_r($output);
        // die();
        
        /* The user who created ticket his/her name will be in ticket_creater flag and its first letter will be displayed in circle.  */
        $capital = ucwords($output['ticket_creater']);
        $first_letter = substr($capital, 0,1);
        $data['letter_icon'] = $first_letter; 
        
        $data['output'] = $output; 

        // Created on Wed, 14 Jun at 6:42 PM      
                
        date_default_timezone_set('Asia/Kolkata');

        $now = date('Y-m-d h:i:s'); //current time                  
    
        $created = date('Y-m-d h:i:s',strtotime($output['created_at']));
        // print_r($created);
        $diff= date_diff(date_create($now),date_create($created));         

        // customer reported time near first letter logo  

        $reported = date('Y-m-d h:i:s',strtotime($output['created_at']));
                    
        $diff= date_diff(date_create($now),date_create($reported));

        if($diff->d > 0){
            $data['report'] ='created'.' '.$diff->d.' '.(($diff->d > 1) ? 'days ago' : 'day ago');
        }elseif($diff->h > 0){ 
            $data['report'] ='created'.' an '.$diff->h.' '.(($diff->h > 1) ? 'hours ago' : 'hour ago');            
        }elseif($diff->i > 0){
            $data['report'] ='created'.' '.$diff->i.' '.(($diff->i > 1) ? 'seconds ago' : 'second ago');  
        }elseif($diff->s > 0){
            $data['report'] = 'created few seconds ago';     
        }                  

        $data['conversation'] = isset($output['conversation'])? $output['conversation']: '' ; 

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/helpdesk/single_ticket_details_and_conversation.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/helpdesk/single_ticket_details_and_conversation.tpl', $data));
        } else {
           $this->response->setOutput($this->load->view('default/template/account/helpdesk/single_ticket_details_and_conversation.tpl', $data));
        }

    }       

                                                                         
    public function conversation_reply(){                   
    
        $ticket_id = '';
        if(isset($this->request->post['ticket_id'])){
         $reply_content['ticket_id'] =  $this->request->post['ticket_id'];    
        }

        $reply = '';
        if(isset($this->request->post['body'])){
         $reply_content['body'] = $this->request->post['body'];    
        }

        $this->load->model('account/helpdesk');   
        $customer_id = $this->customer->getId();
       
        // $today = date("Y/m/d");
       
        if(isset($this->request->files['attachment']) && !$this->request->files['attachment']['error']){
          $reply_content['attachment'] = $this->request->files['attachment'];
        }
         
        $helpdesk_id = $this->model_account_helpdesk->getHelpdeskId($customer_id);            
        $reply_content['helpdesk_id']           = $helpdesk_id['helpdesk_id'];
        $reply_content['customer_id']           = $helpdesk_id['customer_id'];
        $reply_content['password']              = $helpdesk_id['password'];
        $reply_content['email']                 = $helpdesk_id['email'];
        $reply_content['telephone']             = $helpdesk_id['telephone'];
        $reply_content['customer_access_token'] = $helpdesk_id['password'];

        $obj = new Helpdesk($this);  
        $output = $obj->customerReply($reply_content);        
        if(!empty($output['reply_id'])){
            $this->session->data['scroll_bottom'] = true;
            $this->response->redirect($this->url->link('account/helpdesk/viewSingleTicketAndConversation'.'&ticket_id='.$reply_content['ticket_id'],'SSL')); 
        }     

    }     


    public function closeTicket(){

        $close['ticket_id'] =  $this->request->get['ticket_id'];    
    
        $this->load->model('account/helpdesk'); 
  
        $customer_id = $this->customer->getId();           
        
        $result = $this->model_account_helpdesk->getHelpdeskId($customer_id);

        $close['helpdesk_id'] = $result['helpdesk_id'];
        $close['customer_access_token'] = $result['password']; 
        $close['password']  = $result['password'];
        $close['email']     = $result['email'];
        $close['telephone'] = $result['telephone'];
        $close['wsb_id']    = $result['customer_id'];
        
        $obj = new Helpdesk($this);  
        $output =  $obj->ticketClosed($close); 
                                        
        if(!empty($output)){ 
             $this->response->redirect($this->url->link('account/helpdesk/viewSingleTicketAndConversation'.'&ticket_id='.$close['ticket_id'],'SSL')); 
        }

    }  

     /**
    *@author  - Yogesh Mishra.
    *@param   - No param.
    *@return  - Ticket type for helpdesk_ticket(Ticket_type_id,title,description,status);
    *@desc    - On the basis of ticket type the ticket will be assigned to the agent.
    *           This method/api is called from create_profile(restapi) 
    *           and as a api from helpdesk-agent-panel CRM - SRM (WEB + APP).
    */ 

    public function getHelpdeskTicketType(){
        $obj    = new Helpdesk($this);  
        $ticket_type =  $obj->getHelpdeskTicketType(); 
        if(!empty($ticket_type)){
            return $ticket_type;
        }       
    }


    /**
    *@author Yogesh Mishra.
    *@param  customer_id, customer_access_token.
    *@return (json)$rt, $rt contains(status = 0/1, data = wsb_id, message = "whatever the situation is" )
    *@desc   This method/api is called from wsb-helpdesk-api to update the ws_access_token in the oc_customer 
    *        table and in respone it return the success message of updation.
    */
    public function updateAccessToken(){

        $this->load->model('account/helpdesk'); 
        if(($this->request->server['REQUEST_METHOD'] == 'POST')){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $rt = '';
            // $this->load->model('restapi/service');
            // $this->validateApiCall();

            if(isset($request['customer_id'])){
                $customer_id = $request['customer_id'];
                if(isset($request['customer_access_token'])){
                    $customer_token = $request['customer_access_token'];         
                        $password = isset($request['password'])?$request['password']:0; 
                        $exist_response = $this->model_account_helpdesk->getCustomerByPassword($customer_id,$password,$token  = 0);
                        if(!empty($exist_response)){
                             $update_response = $this->model_account_helpdesk->updateCustomerTokenByHelpdesk($customer_id,$customer_token);
                             
                            if(!empty($update_response)){
                                $rt['status'] = '1';  
                                $rt['data']['wsb_id'] = $request['customer_id'];
                                $rt['data']['message'] = 'customer_access_token update successfull.';
                            }else{
                                $rt['error_code'] = '1002';
                                $rt['status'] = '0';
                                $rt['status_text'] = 'Failed';
                                $rt['message'] = 'access_token update failed.';     
                            }
                        }else{                            
                            $rt['status'] = '0';                            
                            $rt['message'] = 'customer not exist';     
                        }
                }else{
                    $rt['error_code'] = '1002';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'http request does not have customer_access_token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Failed';
                $rt['message'] = 'http request does not have customer_id.';
            }
            echo json_encode($rt); exit;
        }
    }

    /**
    *@author Yogesh Mishra.
    *@param  password, wsb_id.
    *@return (json)$rt, $rt contains(status = 0/1, data = wsb_id, message = "customer existance status" )
    *@desc   This method/api is called from wsb-helpdesk-api to check if given customer's customer_id(wsb_id) 
    *        and password exist in the oc_customer. 
    */
    public function customerCheckByPassword(){

        $this->load->model('account/helpdesk'); 
        if(($this->request->server['REQUEST_METHOD'] == 'POST')){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $rt = array();
            // $this->load->model('restapi/service');
            // $this->validateApiCall();
            if(isset($request['source_id'])){
                if(isset($request['wsb_id'])){
                    $customer_id = $request['wsb_id'];
                    if($request['source_id'] == 5 || $request['source_id'] == 6){
                        if(isset($request['customer_access_token'])){
                            $customer_access_token = $request['customer_access_token'];
                                $response = $this->model_account_helpdesk->getCustomerByPassword($customer_id,$password = 0 , $customer_access_token);
                                if(!empty($response)){
                                    $rt['status'] = '1';  
                                    $rt['data']['wsb_id'] = $response;
                                    $rt['data']['message'] = 'customer exist';
                                }else{
                                    $rt['error_code'] = '1002';
                                    $rt['status'] = '0';
                                    $rt['status_text'] = 'Failed';
                                    $rt['message'] = 'customer does not exist.';     
                                }
                        }else{
                            $rt['error_code'] = '1002';
                            $rt['status'] = '0';
                            $rt['status_text'] = 'Failed';
                            $rt['message'] = 'http request does not have customer_access_token.';
                        }
                    }else{
                        if(isset($request['password'])){
                            $password = $request['password'];                       
                            $response = $this->model_account_helpdesk->getCustomerByPassword($customer_id,$password  , $customer_access_token = 0);
                            if(!empty($response)){
                                $rt['status'] = '1';  
                                $rt['data']['wsb_id'] = $response;
                                $rt['data']['message'] = 'customer exist';
                            }else{
                                $rt['error_code'] = '1002';
                                $rt['status'] = '0';
                                $rt['status_text'] = 'Failed';
                                $rt['message'] = 'customer does not exist.';     
                            }
                        }else{
                            $rt['error_code'] = '1002';
                            $rt['status'] = '0';
                            $rt['status_text'] = 'Failed';
                            $rt['message'] = 'http request does not have password.';
                        }
                    }
                }else{
                    $rt['error_code'] = '1002';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'http request does not have wsb_id.';
                }        
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Failed';
                $rt['message'] = 'http request does not have source_id.';
            }    
                echo json_encode($rt); exit;
        }
    }    

     /**
    *@author Yogesh Mishra.
    *@param  mobile.
    *@return (json)$rt, $rt contains(statusCode = 200/1000, 
    *                   message = "whatever the situation is",data).
    *@desc   This method/api is called from wsb-helpdesk-api to get the customer 
    *        from   oc_customer by customer's mobile number or wsb_id.            
    * 
    */
    public function getCustomerForHelpdek(){

        $this->load->model('account/helpdesk'); 
        if(($this->request->server['REQUEST_METHOD'] == 'POST')){

            $inputJSON = file_get_contents('php://input');
            if(!empty($inputJSON)){
                $request = json_decode( $inputJSON, TRUE );                
            }else{
                $request = $_REQUEST;
            }

            // print_r($_REQUEST); die();

            $rt = array();
            // $this->load->model('restapi/service');
            // $this->validateApiCall();
            // print_r($request); die();
            if(isset($request['agent_access_token']) && !empty($request['agent_access_token'])){
                $token['token']  = $request['agent_access_token'];
                $token['agent_id']  = $request['agent_id'];
                $obj    = new Helpdesk($this);
                $response =  $obj->checkAgentExistance($token); 
                if(empty($response) || $response['statusCode'] == '1000' ){
                    $rt['statusCode'] = '1000';                  
                    $rt['message'] = 'agent not authenticated.';   
                    echo json_encode($rt); exit;      
                }
            }else{
                $rt['statusCode'] = '1000';              
                $rt['message'] = 'Please provide agent_access_token.';   
                echo json_encode($rt); exit;  
            }    

            $no_param = 1;
            if(isset($request['mobile']) && !empty($request['mobile'])){
                $no_param = 0;
                $filter['mobile'] = $request['mobile'];      
            }

            if(isset($request['wsb_id']) && !empty($request['wsb_id'])){
                $no_param = 0;
                $filter['wsb_id'] = $request['wsb_id'];                
            }

            if(!empty($no_param)){
                $rt['statusCode'] = '1000';             
                $rt['message'] = 'Please provide customer mobile numer or wsb_id.';     
            }

            $customer_data = $this->model_account_helpdesk->getCustomerForHelpdek($filter);

            if(!empty($customer_data)){
                $rt['statusCode'] = '200';  
                $rt['message'] = 'Customer details successfully retrieved';
                $rt['data'] = $customer_data;
            }else{
                $rt['statusCode'] = '1000';           
                $rt['message'] = 'Customer Not exist.';     
            }

            echo json_encode($rt); exit;
        }
    }

}