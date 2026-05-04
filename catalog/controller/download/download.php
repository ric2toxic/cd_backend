<?php
class ControllerDownloadDownload extends Controller {

	public function index() {
		if(isset($this->request->get['user_id']) && !empty($this->request->get['user_id'])){
			$user_id = $this->request->get['user_id'] ?? 0;
			$access_token = $this->request->get['access_token'] ?? '' ;
			$this->load->model('restapi/service');
			$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
			if($check_access_token > 0){
				if ( !$this->securefiledownload->downloadFile($this->request->get,'app') ) {
					header("Location: " . HTTPS_SERVER);
				}
			}
		}else{
			// we have get data in array from url i.e. file_desc and serialization file_path
			if ( !$this->securefiledownload->downloadFile($this->request->get,'frontend') ) {
				header("Location: " . HTTPS_SERVER);
			}
		}
	}
}
