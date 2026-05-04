<?php
class ControllerCommonCreatetoken extends Controller {
	public function index() {
		//$this->load->language('common/dashboard');
        $this->load->model('user/user');
        $rt = array();
       if (($this->request->server['REQUEST_METHOD'] == 'POST'))
       {
          $inputJSON = file_get_contents('php://input');
          $request = json_decode($inputJSON, TRUE);

          $browse_token = $request['browse_token'];
          $this->user->getId(); 
         if($browse_token != $this->model_user_user->getNotificationToken($this->user->getId()))
         {   
           $this->model_user_user->editToken($browse_token, $this->user->getId());           
           $rt['status'] = '1';
		 }
         else
         {
           $rt['status'] = '2'; 
         }
	   }
       else
       {
        $rt['status'] = '0';
       }

        echo json_encode($rt);
        exit();
    }
}