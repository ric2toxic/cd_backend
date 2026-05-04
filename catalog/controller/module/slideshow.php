<?php
class ControllerModuleSlideshow extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		//$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');

		$data['banners'] = array();

        $store_id = $this->config->get('config_store_id');

        if(INTERNATIONAL_STORE_ID == $store_id){
			 if(CONFIG_IS_MOBILE){
				 $setting['banner_id'] =  $setting['international_mobile_banner_id'];
			 }else{
				 $setting['banner_id'] =  $setting['international_desktop_banner_id'];
			 }
        }else{
			if(CONFIG_IS_MOBILE){
				$setting['banner_id']  = $setting['home_mobile_banner_id'];
			}else{
				$setting['banner_id']  = $setting['banner_id'];
			}
        }

		$results = $this->model_design_banner->getBanner($setting['banner_id']);
		foreach ($results as $result) {
			//if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
					'img_width' => $setting['width'],
					'img_height' => $setting['height']
				);
			//}
		}

		$data['module'] = $module++;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/slideshow.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/slideshow.tpl', $data);
		} else {
			return $this->load->view('default/template/module/slideshow.tpl', $data);
		}
	}
}
