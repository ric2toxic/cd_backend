<?php 
class ControllerSellerCategorySetDescription extends Controller {
	public function index(){

		$this->load->language('seller/category_set_description');

		$data['seller_id']          	= $this->customer->getId();
		$data['product_type']			= $this->request->get['product_type'];
		$data['product_id']        	 	= $this->request->get['product_id'];
        $data['sor_product_id']     	= $this->request->get['sor_id'];
        $data['piece_in_set']       	= $this->request->get['pieceinset'];

		if (file_exists(DIR_APPLICATION . 'model/dynamic_categories/' . $data['product_type'] . '.php')){
            $this->load->model('dynamic_categories/' . $data['product_type']);
            $modelfilename 	= 'model_dynamic_categories_' . $data['product_type'];
            $filename		= $data['product_type'];
            $data_category 	= $this->$modelfilename->index($data['product_id'], $data['sor_product_id']);
        } else {
            $this->load->model('dynamic_categories/general');
            $filename 		= 'general';
            $data_category 	= $this->model_dynamic_categories_general->index($data['product_id'], $data['sor_product_id']);
        }

        // Start General Tpl Files Parameter
		$data['err_enter_all_field'] 	= $this->language->get('err_enter_all_field');
		$data['text_set_type'] 			= $this->language->get('text_set_type');
		$data['text_size_set'] 			= $this->language->get('text_size_set');
		$data['text_color_set'] 		= $this->language->get('text_color_set');
		$data['text_free_size'] 		= $this->language->get('text_free_size');
		$data['text_pis'] 				= $this->language->get('text_pis');
		$data['text_individual_size'] 	= $this->language->get('text_individual_size');
		$data['text_sizes_like'] 		= $this->language->get('text_sizes_like');
		$data['text_submit'] 			= $this->language->get('text_submit');
        
        // End General Tpl Files Parameter
        
        
        // Start Yardage Tpl Files Parameter
        $data['text_fabric_length'] 	= $this->language->get('text_fabric_length');
		$data['text_fabric_width'] 		= $this->language->get('text_fabric_width');
		$data['text_weight'] 			= $this->language->get('text_weight');
		$data['text_submit'] 			= $this->language->get('text_submit');
		$data['text_transfer_price']	= $this->language->get('text_transfer_price');
		$data['entry_fabric_length']	= $this->language->get('entry_fabric_length');
		$data['entry_fabric_width']		= $this->language->get('entry_fabric_width');
		$data['entry_weight']			= $this->language->get('entry_weight');
		$data['entry_transfer_price']	= $this->language->get('entry_transfer_price');

		//End Yardage Tpl Files Parameter

		
        $data['header_seller'] 			= $this->load->controller('common/seller_header');
		$data['footer_seller'] 			= $this->load->controller('common/seller_footer');

		$this->response->setOutput($this->load->view('default/template/multiseller/manage_inventory_edit/' . $filename . '.tpl', $data));
	}
	public function updateSetDescription(){
		$data['product_id']		=	$this->request->post['product_id'];
		$data['sor_product_id']	=	$this->request->post['sor_product_id'];
		$data['product_type']	=	$this->request->post['product_type'];
		$data['info']			=	$this->request->post['info'];
		
		if (file_exists(DIR_APPLICATION . 'model/dynamic_categories/' . $data['product_type'] . '.php')){
            $this->load->model('dynamic_categories/' . $data['product_type']);
            $modelfilename 	= 'model_dynamic_categories_' . $data['product_type'];
            $data_category 	= $this->$modelfilename->updateSetDescription($data['product_id'], $data['sor_product_id'], $data['info']);
            $data_price_meter = $this->$modelfilename->updatePriceMeter($data['product_id'], $data['sor_product_id'], $data['info']);
        } else {
            $this->load->model('dynamic_categories/general');
            $filename 		= 'general';
            $data_category 	= $this->model_dynamic_categories_general->updateSetDescription($data['product_id'], $data['sor_product_id'], $data['info']);
        }
		
	}
}