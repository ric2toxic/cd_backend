<?php


class ControllerInventoryImage extends Controller {
	
	public function index() {
		$this->getForm();
		
	}
	
	public function getForm(){
        if ($_SERVER['HTTPS']) {
            $static_content_url =  STATIC_CONTENT_URL_SSL;
        } else {
            $static_content_url =  STATIC_CONTENT_URL ;
        }
        $cdn_url = $static_content_url.'filemanager/';
		$data = array();
		$this->document->setTitle("Bulk Image Upload");
	
		$data['form_action'] 	= 'index.php?route=inventory/image/index'.'&token=' . $this->session->data['token'];
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 		= $this->load->controller('common/footer');
		$data['header'] 		= $this->load->controller('common/header');
		$data['token'] = $this->session->data['token'];
		$seller_profile = new SellerProfile($this);
        $sellers = $seller_profile->getSellers();
        $data['image_path'] = HTTPS_CATALOG.'khufiya_vibhag/view/image/ajax-loader.gif';

        $data['cdn_url'] = $cdn_url;
      
        $data['filter_seller_name'] = '';
        foreach ($sellers as $key => $seller) {
            $sellers[$key] = array(
                'seller_id' => $seller['seller_id'],
                'name' => $seller['company'],
                'nickname' => $seller['nickname']
            );
            if(isset($data['filter_seller_id'])) {
				if( $data['filter_seller_id'] == $seller['seller_id']){
					$data['filter_seller_name'] = $seller['company'];
				}
			}
           
        }
		
        $data['sellers'] = $sellers;

        $data['user_id'] = $this->user->getId();
        $this->response->setOutput(($this->load->view('inventory/image.tpl', $data)));
	}
	
	public function getSellerDetails() {
		
		$data = array();
		$searching = new BulkImage("","",$this->db);
		$data['search'] = $searching->autocompleteSearching($this->request->get['request']);
		$this->response->setOutput(json_encode( 
											array(
												'seller_info' => $data['search'],
											)
										));	

	}
	
	public function readZip() {

			if( !empty($_FILES) ) {
				//getting user_id
				$user_id = $this->user->getId();	
			
				//getting user_name
				$this->load->model('user/user');
				$user_name = $this->model_user_user->getUser($user_id)['username'];	
				$message = array();

				$bulk_image_upload = new BulkImage($_POST['id_field'],$_FILES, $this->db);

				$message = $bulk_image_upload->readZipFile($user_id,$user_name);
				
				$this->response->setOutput(json_encode( 
												array(
													'message' => $message,		
												)
											));	
			} else {
				$this->response->setOutput(json_encode( 
												array(
													'message' => "PLease select File",	
													 'flag' => 1,	
												)
											));	
			}
	}
	
}

?>
	
