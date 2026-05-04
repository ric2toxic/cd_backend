<?php

/**
* 
*/
class ControllerMarketingTemplate extends Controller
{
	private $error 		= array();

	public function index(){
		//$this->load->language('marketing/template');
        $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('marketing/template', $data);
	    
		$this->document->setTitle($data['heading_title']);

		$data['text_existing_templates'] = $this->language->get('text_existing_templates');

		// $data['text_customer_all'] = $this->language->get('text_customer_all');
		// $data['text_customer'] = $this->language->get('text_customer');

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('marketing/template', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['form_action'] 				= 'index.php?route=marketing/template/index'.'&token=' . $this->session->data['token'];


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$template_text = $this->request->post['template'];
			$template_title = $this->request->post['template_title'];

			$this->saveTemplate($template_title, $template_text);


		}
		$data['admin_templates'] = $this->getAdminTemplates();
		// echo "<pre>"; print_r($data['admin_templates']); die;
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['template_title'])) {
			$data['error_template_title'] = $this->error['template_title'];
		} else {
			$data['error_template_title'] = '';
		}

		if (isset($this->error['template'])) {
			$data['error_template'] = $this->error['template'];
		} else {
			$data['error_template'] = '';
		}
		$this->response->setOutput($this->load->view('marketing/template.tpl', $data));

	}

	public function saveTemplate($title, $template){
		return $this->db->query("INSERT INTO ".DB_PREFIX."share_templates  
		SET customer_id=0"." 
		, template="."'".$template."'"." 
		, status=1"." 
		, title="."'".$title."'");

	}
	public function getAdminTemplates(){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."share_templates st WHERE st.customer_id = '0'")->rows;
	}
	public function validateForm(){

		if (!$this->user->hasPermission('modify', 'marketing/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (empty($this->request->post['template_title'])) {
			$this->error['template_title'] = $this->language->get('error_template_title');
		}		
		if (empty($this->request->post['template'])) {
			$this->error['template'] = $this->language->get('error_template');
		}

		return !$this->error;

	}
	public function updateTemplateText(){
		$temp = $this->request->post['template'];
		$template_id = $this->request->post['template_id'];

		$sql_update = "UPDATE ".DB_PREFIX."share_templates  
		SET template="."'".$temp."'"." 
		WHERE 
		template_id=".$template_id ." AND
		customer_id ='0'"  ;

		$this->db->query($sql_update);


	}

}