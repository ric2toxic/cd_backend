<?php
class Controllerpreferencespreferences extends Controller {
	public function index() {
		//$this->load->language('preferences/preferences');
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('preferences/preferences', $data);
		
		$this->load->model('preferences/preferences');
		$data['preferences'] = $this->model_preferences_preferences->getMasterPreferences();

		$data['token'] = $this->session->data['token'];
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('preferences/preferences', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['cancel'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');

		$data['action'] = $this->url->link('preferences/preferences/savePreferences', 'token=' . $this->session->data['token'], 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('preferences/preferences.tpl', $data));
	}
	public function savePreferences() {
		//echo "<pre>"; print_r($this->request->post); die;
		$category = $this->request->post['category'];
		$i = 0;
		$data = array();
		foreach ($category as $cat => $cat_value) {
			$category_id 	= $cat;
			$category_image = $cat_value['image'];
			$filter_id 		= '';
			if (is_array($cat_value['filter'])){
				foreach ($cat_value['filter'] as $fil => $fil_value) {
					$filter_id .= $fil_value.',';
				}
			}
			$data[$i]['category_id'] 	= $category_id;
			$data[$i]['category_image'] = $category_image;
			$data[$i]['filter_id'] 		= $filter_id;
			$i++;
		}

		$this->load->model('preferences/preferences');
		$this->model_preferences_preferences->saveMasterPreferences($data);
		$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
	}
	public function autocompleteFilter() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {

			$filter_name = $this->request->get['filter_name'];
			$category_id = $this->request->get['cat_id'];

			$this->load->model('catalog/category', 'frontend');
			$filters = $this->frontend_model_catalog_category->getFiltersOfProducts($category_id,'',$filter_name);

			foreach ($filters as $filter) {
				foreach ($filter['filter'] as $value) {
					$json[] = array(
						'filter_id' => $value['filter_id'],
						'name'      => strip_tags(html_entity_decode($filter['name'] . ' &gt; ' . $value['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
		}

		$sort_order = array();
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}
		array_multisort($sort_order, SORT_ASC, $json);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
}