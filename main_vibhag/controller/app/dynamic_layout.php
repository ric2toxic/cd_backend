<?php
require_once(DIR_SYSTEM . '/library/app/module.php');
require_once(DIR_SYSTEM . '/library/app/layout.php');

/**
* App Dynamic layout's
* @author   GARVIT
*/
class ControllerAppDynamicLayout extends Controller {

	/**
	* index
	* Get layouts from cache and show.
	* @author   GARVIT
	*/
	public function index() {
		$this->layoutList();
    }

	/**
	* _common 
	* Common functionality of all methods.
	* @param  	$data ARRAY
	* @return 	$data ARRAY
	* @author   GARVIT
	*/
	private function _common(&$data){
		$this->load->autoLoadLanguage('app/dynamic_layout', $data);

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('app/dynamic_layout', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['token'] = $this->session->data['token'];

		return $data;
	}

	/**
	* layoutList
	* Get Modules and layout from cache file.
	* @author   GARVIT
	*/
	public function layoutList(){
		$data = array();
        $this->_common($data);
		$data['create_new_layout'] = $this->url->link('app/dynamic_layout/layoutForm', 'token=' . $this->session->data['token'], 'SSL');
		$data['layout'] = $this->_getCacheFile();
		
		    //  pr($data['layout']); die;
        $this->response->setOutput($this->load->view('app/layout_list.tpl', $data));
	}

	/**
	* layoutForm
	* Get Modules from library/app_dynamic_layout.php by object.
	* user can create a layout using module and save it into cache file and in restapi send layout if exist in app.
	* @author   GARVIT
	*/
	public function layoutForm(){
		$data = array();
		$this->_common($data);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_create_title'],
			'href' => $this->url->link('app/dynamic_layout/layoutForm', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['save_layout'] = $this->url->link('app/dynamic_layout/saveLayout', 'token=' . $this->session->data['token'], 'SSL');

		// Layout Index(Array) in cache file
		$data['layout_index'] 	= 0;
		$data['layout_detail'] 	= array('layout_id'=>'','module_id'=>'');
		if(isset($this->request->get['layout_index'])){
			$data['layout_index'] 	= $this->request->get['layout_index'];
			$layout_detail 	= $this->_getCacheFile($data['layout_index']);
			$data['layout_detail']['layout_id'] = $layout_detail['layout_id'];
			$data['layout_detail']['module_id'] = $layout_detail['module_id'];
		}
		// echo "<pre>"; print_r($data['layout_detail']); die;

		// Layout
		$layout_obj = new Layout();
		$data['layout_arr'] = isset($layout_obj->layout_array)?$layout_obj->layout_array:array();

		// Module
		$module_obj = new Module($this);
		$data['module_arr'] = isset($module_obj->module_array)?$module_obj->module_array:array();
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
        $this->response->setOutput($this->load->view('app/layout_form.tpl', $data));
	}

	/**
	* saveLayout
	* appand/save Layout in cache file.
	* @param  	$data ARRAY
	* @author   GARVIT
	*/
	public function saveLayout(){
		$data = array();
		// echo "<pre>"; print_r($this->request->post); die;
		if( !empty($this->request->post) ){
			if( isset($this->request->post['radio_layout']) && !empty($this->request->post['radio_layout']) ) {
				$layout_obj 		= new Layout();
				$method_name 		= $this->request->post['radio_layout'];
				if(method_exists($layout_obj,$method_name)){
					$layout_data 	= $layout_obj->$method_name($this->request->post['layout']);
					$json_layout['layout_data'] 	= $layout_data;
					$json_layout['layout_name'] 	= $method_name;
				}else{
					$data['error'] 	.= "\n Layout Method Not Exist";
				}
			}else{
				$data['error'] 	.= "\n Please Select Layout";
			}

			if(	isset($this->request->post['radio_module']) && !empty($this->request->post['radio_module']) ) {
				$module_obj 		= new Module($this);
				$method_name 		= $this->request->post['radio_module'];
				if(method_exists($module_obj,$method_name)){
					$module_data 	= $module_obj->$method_name($this->request->post['module']);
					$json_module['module_data'] 	= $module_data; 
					$json_module['module_name'] 	= $method_name;
				}else{
					$data['error'] 	.= "\n Module Method Not Exist";
				}
			}else{
				$data['error'] 	.= "\n Please Select Module";
			}
		}else{
			$data['error'] .= "\n Data is Empty";
		}
		if(!isset($data['error'])) {
			$data['success'] 	= $this->_saveCacheFile($this->request->post['layout_index'], $json_layout, $json_module);
			$this->response->redirect($this->url->link('app/dynamic_layout', 'token=' . $this->session->data['token']  , 'SSL'));
		}else{ 
			echo $data['error']; die;
		}
	}

	private function _saveCacheFile($layout_index, $json_layout, $json_module){
	
		$final_array = array();
		$final_array['layout_id'] 	= $json_layout['layout_name'];
		$final_array['layout'] 		= $json_layout['layout_data'];
		$final_array['module_id'] 	= $json_module['module_name'];
		ksort($json_module['module_data']);
		$final_array['module'] 		= array_values($json_module['module_data']);
		$cache_file_name = 'dynamic_app_layouts';
		$cache_data = array();
		if(!empty($this->cache->get($cache_file_name))){
			$cache_data 	=  $this->cache->get($cache_file_name);
			if($layout_index==0){
				$last_layout_index = key( array_slice( $cache_data, -1, 1, TRUE ) );
				$final_array['layout_index'] = $last_layout_index+1;
				array_push($cache_data, $final_array);
			}else{
				$final_array['layout_index'] 	= 	$layout_index;
				$cache_data[$layout_index] 		= 	$final_array;
			}
		}else{
			if($layout_index==0){
				$final_array['layout_index'] = 1;
				$cache_data = array(1=>$final_array);
			}
		}
		//pr($cache_data); die;
		$cache_obj = new cache('file',31536000);
		$cache_obj->set($cache_file_name, $cache_data);
	}
	
	private function _getCacheFile($val=''){
		$cache_file_name = 'dynamic_app_layouts';

		$cache_data 	= array();
		if(!empty($this->cache->get($cache_file_name))){
			$cache_data 	=  $this->cache->get($cache_file_name);
		}

		if(!empty($val)){
			$result = $cache_data[$val];
		}else{
			$result = $cache_data;
		}
		return $result;
	}

	/**
	* getLayoutTpl
	* Get Layout's Tpl according to layout.
	* @param  	$data 	ARRAY
	* @author   GARVIT
	*/
	public function getLayoutTpl() {
		$data = array();
		if( isset($this->request->post['layout_name']) && !empty($this->request->post['layout_name']) ) {
		
			$data['layout'] 	= array();
			if(isset($this->request->post['layout_index']) && !empty($this->request->post['layout_index'])){
				$layout_index 	= $this->request->post['layout_index'];
				$layout_detail 	= $this->_getCacheFile($layout_index);
				$data['layout'] = $layout_detail['layout'];
			}
			// pr($data['layout']); die;

			$layout_name = $this->request->post['layout_name'];
			$this->load->autoLoadLanguage('app/dynamic_layout', $data);
			$response = $this->load->view('app/layout/'.$layout_name.'.tpl', $data);
			echo $response;
		}
	}

	/**
	* getModuleTpl
	* Get Layout's Tpl according to layout.
	* @param  	$data 	ARRAY
	* @author   GARVIT
	*/
	public function getModuleTpl() {
		$data = array();
		if( isset($this->request->post['module_name']) && !empty($this->request->post['module_name']) ) {
			$module_name = $this->request->post['module_name'];
			$this->load->autoLoadLanguage('app/dynamic_layout', $data);
			
			$this->load->model("tool/image");
			$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			
			$modules 	= array();
			if(isset($this->request->post['layout_index']) && !empty($this->request->post['layout_index']) ){
				$layout_index 	= $this->request->post['layout_index'];
				$module_detail 	= $this->_getCacheFile($layout_index);
				$modules = $module_detail['module'];
			}
			$data['module'] = array();
			foreach($modules as $module){
				//if (is_file(DIR_IMAGE . $module['image'])) {
                    $module['image'] = str_replace(STATIC_CONTENT_URL,"",$module['image']);
					$image = $module['image'];
					$thumb = $module['image'];
					$module['directory'] = dirname($module['image']);
				/*} else {
					$image = '';
					$thumb = 'no_image.png';
					$module['directory'] = '';
				}*/
				$module['thumb']  = $this->model_tool_image->resize($thumb, $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				$data['module'][] = $module;
			}

			$response = $this->load->view('app/module/'.$module_name.'.tpl', $data);
			echo $response;
		}
	}
}