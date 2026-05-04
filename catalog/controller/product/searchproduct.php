<?php
class ControllerProductSearchproduct extends Controller {
	private $error = array();

    public function getsearchproduct($name){
        $this->load->model('catalog/searchproduct');
        //echo $name; exit;
        /*echo json_encode($this->request->post['name']); exit;
        echo '<pre>'; print_r($this->request->post['name']); exit;
        echo '<pre>'; print_r($_REQUEST); exit;*/

        //echo '<pre>'; print_r($this->request->post['name']); exit;
        if (isset($this->request->post['name'])) {
            $data['filter_name'] = $this->request->post['name'];
        }else{
            $data['filter_name'] = 'Yellow';
        }
        $data = $this->model_catalog_searchproduct->getProducts($data);
        echo json_encode($data); exit;
        //echo '<pre>';print_r($data); exit;
    }

}
