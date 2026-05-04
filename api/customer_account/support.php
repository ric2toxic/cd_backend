<?php
  require_once('system.php');
  require_once(DIR_SYSTEM.'library/customer.php');
  require_once(DIR_SYSTEM.'library/helpdesk.php');

  class supportController extends SystemController {

    private $data        = '';
    private $message     = 'No Data Found';
    private $statusCode  = 999;

        
    public function __construct($params) {
      parent::__construct($params);

      date_default_timezone_set('Asia/Kolkata');

      // Customer Class Object
      $this->registry->set('customer',new Customer($this->registry));

      // helpdesk Class Object
      $this->registry->set('helpdesk',new Helpdesk($this->registry));
    }
    
    /**
     * @info : Public method(API) to get ticket list
     * @author: Mahaveer, Sept  2019
    */    
    public function getTicketListData() 
    {
         $ticket_data = array();

         //Set request data to $filter_data
         $filter_data = $this->request;
         $filter['page']   = empty($filter_data['page'])? '1' : $filter_data['page'];
         $filter['status'] = empty($filter_data['status']) ? '' : $filter_data['status'];

         //Set customer data in $filter_data
          $this->load->model('account/helpdesk'); 
         
          $customer_id = $this->customer->getId();
          $customer_data = $this->model_account_helpdesk->gethelpdeskId($customer_id);

          $filter['helpdesk_id'] = $customer_data['helpdesk_id'];
          $filter['customer_id'] = $customer_data['customer_id'];
          $filter['telephone']   = $customer_data['telephone'];
          $filter['password']    = $customer_data['password'];        
          $filter['customer_access_token'] = $customer_data['password'];

         //Get Ticket(s) List by given request params for single customer
          $ticket_list = $this->helpdesk->viewAllTickets($filter);
          $data_count = 0;
          $pagination = '';
          if(!empty($ticket_list))
          { 
            $tickets    = $ticket_list['data_res'];
            $data_count = $ticket_list['data_count'];
            $pagination = $ticket_list['pagination'];
            foreach ($tickets as $output)
             {
                $d = date('d'.' '.'M', strtotime($output['created_at']));
                $day = date('l', strtotime($output['created_at'])); 
                $hour_minute = date('h:i A', strtotime($output['created_at'])); 
                $output['created_at'] = "Created on". ' ' .$day.','.' ' .$d.' '.'at'.' '.$hour_minute ;
                $ticket_data[] = $output;
            }         
          }

      //Set data for Response 
      $return_result = array();
      $return_result['ticket_data'] = $ticket_data;
      $return_result['data_count']  = $data_count;
      $return_result['pagination']  = $pagination;
          
      $this->data_packet->statusCode = 200;
      $this->data_packet->data       = $return_result;
      $this->data_packet->message    = "Data Successfully Found.";

      return $this->data_packet;
    }


      /**
     * @info : Public method(API) to get ticket details
     * @author: Mahaveer, Sept  2019
    */    
    public function getTicketDetail() 
    {
         $ticket_data = array();
         //Set request data to $filter_data
         $filter_data = $this->request;
         $filter['page']   = empty($filter_data['page'])? '1' : $filter_data['page'];
         $filter['ticket_id']   = empty($filter_data['ticket_id'])? '0' : $filter_data['ticket_id'];
         $filter['dont_update_read']   = empty($filter_data['dont_update_read'])? '0' : $filter_data['dont_update_read'];
         
         //Set customer data in $filter_data
          $this->load->model('account/helpdesk'); 
         
          $customer_id = $this->customer->getId();
          $customer_data = $this->model_account_helpdesk->gethelpdeskId($customer_id);

          $filter['helpdesk_id'] = $customer_data['helpdesk_id'];
          $filter['customer_id'] = $customer_data['customer_id'];
          $filter['telephone']   = $customer_data['telephone'];
          $filter['email']       = $customer_data['email'];
          $filter['password']    = $customer_data['password'];        
          $filter['customer_access_token'] = $customer_data['password'];


         //Get Ticket(s) detail by given request params for single customer
          $ticket_data = $this->helpdesk->viewsingleTicket($filter);


        //set reporting time
        if(!empty($ticket_data))
        {
          $now = date('Y-m-d h:i:s'); //current time                  
          $created = date('Y-m-d h:i:s',strtotime($ticket_data['created_at']));
          $diff= date_diff(date_create($now),date_create($created));           
          $reported = date('Y-m-d h:i:s',strtotime($ticket_data['created_at']));
          $diff= date_diff(date_create($now),date_create($reported));

          if($diff->d > 0){
            $ticket_data['report'] ='created'.' '.$diff->d.' '.(($diff->d > 1) ? 'days ago' : 'day ago');
          }elseif($diff->h > 0){ 
             $ticket_data['report'] ='created'.' an '.$diff->h.' '.(($diff->h > 1) ? 'hours ago' : 'hour ago');            
          }elseif($diff->i > 0){
            $ticket_data['report'] ='created'.' '.$diff->i.' '.(($diff->i > 1) ? 'seconds ago' : 'second ago');  
          }elseif($diff->s > 0){
            $ticket_data['report'] = 'created few seconds ago';     
          } 

         $ticket_data['description'] = nl2br($ticket_data['description']);
        }

        //set ticket attachment
        if(!empty($ticket_data['attachment']))
        {
          $i=0;
          foreach($ticket_data['attachment'] as $attachment)
          {
            $ext = pathinfo($attachment['attachment_name'], PATHINFO_EXTENSION); 
            if(strlen($ext) <= 3)
            {
              $ticket_data['attachment'][$i]['ext'] = $ext;
            }
            $size =$attachment['size'];
            $kb = $size/1024;
            $ticket_data['attachment'][$i]['size'] = round($kb,2).' KB';
            $i++; 
          }
        }   

      //Get Ticket(s) conversation
      if(!empty($ticket_data['conversation']))
      {
        $i=0;
        foreach($ticket_data['conversation'] as $conversation)
        {
          $now = date('Y-m-d h:i:s'); //current time
          $created = date('Y-m-d h:i:s',strtotime($conversation['created_at']));
          $diff= date_diff(date_create($now),date_create($created)); 
         
          if($diff->d > 0)
          {                    
            $time ='Said '.$diff->d.(($diff->d > 1) ? ' days' : ' day ').' '.$diff->h.' '.(($diff->h > 1) ? 'hours' : 'hour');
          }
          elseif($diff->h > 0)
          { 
            $time ='Said '.$diff->h.(($diff->h > 1) ? ' hours' : ' hour ').' '.$diff->i.' '.(($diff->i > 1) ? 'minutes' : ' minute ');            
          }
          elseif ($diff->i > 0) 
          {
            $time ='Said '.$diff->i.' '.(($diff->i > 1) ? 'minutes' : 'minute'). ' '.$diff->s.' '.(($diff->s > 0 ) ? 'seconds':'second');                        
          }
          elseif ($diff->h == 0 && $diff->i == 0 && $diff->s > 0 ) 
          {
            $time ='Said '.$diff->s.' '.(($diff->s > 1) ? 'seconds' : 'second'); 
          }
         
         $ticket_data['conversation'][$i]['time'] = $time;
         $ticket_data['conversation'][$i]['body'] = nl2br($conversation['body']);

         if(!empty($conversation['attachment']))
         {
           $j=0;
           foreach($conversation['attachment'] as $attachment)
           {
            $ext = pathinfo($attachment['attachment_name'], PATHINFO_EXTENSION); 
            if(strlen($ext) <= 3)
            {
              $ticket_data['conversation'][$i]['attachment'][$j]['ext'] = $ext;
            }
            $size =$attachment['size'];
            $kb = $size/1024;
             $ticket_data['conversation'][$i]['attachment'][$j]['size'] = round($kb,2).' KB';
            $j++; 
          }
        }
         $i++;
        }
      }


      //Set data for Response 
      $this->data_packet->statusCode = 200;
      $this->data_packet->data       = $ticket_data;
      $this->data_packet->message    = "Data Successfully Found.";

      return $this->data_packet;
    }


     /**
     * @info : Public method(API) to close ticket
     * @author: Mahaveer, Sept  2019
    */  
    public function closeTicket()
    {
        $close['ticket_id'] =  $this->request['ticket_id'];    
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
        $output =  $this->helpdesk->ticketClosed($close); 

        $this->data_packet->statusCode = 200;
        $this->data_packet->data       = $output;
        $this->data_packet->message    = "Ticket Closed Successfully";
        return $this->data_packet;

    } 

     /**
     * @info : Public method(API) to reply on conversation
     * @author: Mahaveer, Sept  2019
    */ 

    public function conversation_reply()
    {                   
         $reply_content = array();
        if(isset($this->request['ticket_id'])){
         $reply_content['ticket_id'] =  $this->request['ticket_id'];    
        }

        if(isset($this->request['body'])){
         $reply_content['body'] = $this->request['body'];    
        }

        $this->load->model('account/helpdesk');   
        $customer_id = $this->customer->getId();
       
        if(!empty($_FILES['attachment']))
        {
          $reply_content['attachment'] = $_FILES['attachment'];
        }
         
        $helpdesk_id = $this->model_account_helpdesk->getHelpdeskId($customer_id);            
        $reply_content['helpdesk_id']           = $helpdesk_id['helpdesk_id'];
        $reply_content['customer_id']           = $helpdesk_id['customer_id'];
        $reply_content['password']              = $helpdesk_id['password'];
        $reply_content['email']                 = $helpdesk_id['email'];
        $reply_content['telephone']             = $helpdesk_id['telephone'];
        $reply_content['customer_access_token'] = $helpdesk_id['password'];
        

        $output = $this->helpdesk->customerReply($reply_content);

        if(!empty($output['reply_id']))
       {
          $this->data_packet->statusCode = 200;
          $this->data_packet->data       = $output;
          $this->data_packet->message    = "Message sent Successfully"; 
       }
       else
       {
        $this->data_packet->statusCode = 100;
        $this->data_packet->data       = $output;
        $this->data_packet->message    = "There is something wrong with this api.";
       }      
       return $this->data_packet;
    } 

   /**
    *@author  - Mahaveer Choudhary.
    *@param   - {attachment_file,ticket_type_id,subject,description}.
    *@return  -  To create a new ticket*/

    public function createTicket()
    {
        $ticket = array();
        $ticket['ticket_type_id']     =  $this->request['ticket_type_id'];
        $ticket['ticket_subject']     =  $this->request['subject'];
        $ticket['ticket_description'] =  $this->request['description'];

        $this->load->model('account/helpdesk');  
        $customer_id = $this->customer->getId();       
        $helpdesk_id = $this->model_account_helpdesk->getHelpdeskId($customer_id);
        $ticket['helpdesk_id'] = $helpdesk_id['helpdesk_id'];
        $ticket['wsb_id']      = $helpdesk_id['customer_id'];
        $ticket['email']       = $helpdesk_id['email'];
        $ticket['telephone']   = $helpdesk_id['telephone'];
        $ticket['customer_access_token'] = $helpdesk_id['password'];
        $ticket['password'] = $helpdesk_id['password'];

        if(!empty($_FILES['attachment_file']))
        {
          $ticket['attachment']  = $_FILES['attachment_file'];   
        } 

        $output =  $this->helpdesk->createTicket($ticket); 
        if(!empty($output['ticket_id']))
        {
          $this->data_packet->statusCode = 200;
          $this->data_packet->data       = $output;
          $this->data_packet->message    = "Ticket created Successfully";  
        }
        else
        {
          $this->data_packet->statusCode = 100;
          $this->data_packet->data       = $output;
          $this->data_packet->message    = "There is something wrong with this api."; 
        }

        return $this->data_packet;

    }


   /**
    *@author  - Mahaveer Choudhary.
    *@param   - No param.
    *@return  - Ticket type for helpdesk_ticket(Ticket_type_id,title,description,status);
    *@desc    - On the basis of ticket type the ticket will be assigned to the agent.*/

    public function getHelpdeskTicketType()
    {
      $ticket_type =  $this->helpdesk->getHelpdeskTicketType(); 
      $this->data_packet->statusCode = 200;
      $this->data_packet->data       = $ticket_type;
      $this->data_packet->message    = "Data Successfully Found.";  
      return $this->data_packet;    
    }
    

  }