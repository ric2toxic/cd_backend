<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search'])) {
			//$data['search'] = $this->request->get['search'];
			$data['search'] = trim($this->request->get['search']);
		} else {
			$data['search'] = '';
		}
		if (isset($this->request->get['category_id'])) {
			$top_cat = $this->model_catalog_category->getCategory($this->request->get['category_id']);
			$data['category_name'] = isset($top_cat['name']) ? $top_cat['name'] : '';
            $data['category_id'] = $this->request->get['category_id'];
		} 
		$this->load->model('catalog/category');
		$cats = $this->model_catalog_category->getCategories(0);
		$data['cats'] = $cats;
		// echo "<pre>"; print_r($cats);

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/search.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/search.tpl', $data);
		} else {
			return $this->load->view('default/template/common/search.tpl', $data);
		}
	}

	public function autoComplete(){
        $solr = new SolrProduct($this);
		//$this->load->model('solr/product');
		$keyword = $this->request->get['term'];
		$response = $solr->getAutoSuggestions($keyword);

		echo json_encode($response);

/*
		$config_solr = $this->config->solrConfig();
		// create a client instance
		$client = new Solarium\Client($config_solr);

		// get a suggester query instance
		$query = $client->createSuggester();
		$query->setQuery($keyword); //multiple terms
		$query->setDictionary('suggest');
		$query->setOnlyMorePopular(true);
		$query->setCount(10);
		$query->setCollate(true);

		// this executes the query and returns the result
		$resultset = $client->suggester($query);
		//echo $resultset->getResponse();

		//echo '<b>Query:</b> '.$query->getQuery().'<hr/>';
		$objResultData = json_decode($resultset->getResponse()->getBody());

		$objResults = $objResultData->suggest->suggest->$keyword;
		//print_r($objResults);
		// display results for each term
		if($objResults->numFound > 0) {
			$temp = array();
			$response = array();
			foreach ($objResults->suggestions as $result) {

				if(!in_array( $result->term, $temp)) {
					$response[]['name'] = $result->term;
				}
				$temp[] = $result->term;

			}
		}

		echo json_encode($response);

// display collation
		//echo 'Collation: '.$resultset->getCollation();
*/

	}
}