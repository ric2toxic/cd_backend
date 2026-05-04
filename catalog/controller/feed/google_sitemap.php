<?php
class ControllerFeedGoogleSitemap extends Controller {
	private $chunk = 1000;

    public function index(){

        $output = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $sql_url = "SELECT * FROM " . DB_PREFIX . "url_alias "
            . " WHERE url_type IN ('category_search', 'multiple_query_string') "
            . " AND is_custom = 1";
        $query_custom_url = $this->db->query($sql_url);
        if($query_custom_url->num_rows > 0){
            foreach ($query_custom_url->rows as $result_custom_url) {

                if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID ){

                    $url = 'https://'.INDIA_STORE_HOST. '/' .$result_custom_url['keyword'];
                }else{

                    $url = 'https://'.INTERNATIONAL_STORE_HOST. '/' .$result_custom_url['keyword'];
                }

                $output .= '<url>';
                $output .= '<loc>' . $url . '</loc>';
                $output .= '<changefreq>weekly</changefreq>';
                $output .= '</url>';
            }
        }

        // Get all categories to make URLs
        $this->load->model('catalog/category');
        $results = $this->model_catalog_category->getAllCategoryIds();

        foreach ($results as $result) {

            $is_301_redirect = false;

            //replace old url to custom url if 301 redirect true
            $sql = "SELECT * FROM " . DB_PREFIX . "url_alias "
                . "WHERE search_id = '" . $result['category_id']
                . "' AND url_type = 'category'"
                . " AND is_custom = 1  LIMIT 1";
            $query = $this->db->query($sql);
            if($query->num_rows > 0){

                if($query->row['is_redirect_301'] == 1)
                    $is_301_redirect = true;

                if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID ){

                    $url = 'https://'.INDIA_STORE_HOST. '/' .$query->row['keyword'];
                }else{

                    $url = 'https://'.INTERNATIONAL_STORE_HOST. '/' .$query->row['keyword'];
                }

                $output .= '<url>';
                $output .= '<loc>' . $url . '</loc>';

                $date_modified = date('Y-m-d', strtotime($result['date_modified']));

                $output .= '<lastmod>'.$date_modified.'</lastmod>';
                $output .= '<changefreq>weekly</changefreq>';
                $output .= '</url>';
            }

            if($is_301_redirect == false){

                $url = $this->url->link('product/category', 'path=' . $result['category_id']) ;

                $output .= '<url>';
                $output .= '<loc>' . $url . '</loc>';

                $date_modified = date('Y-m-d', strtotime($result['date_modified']));

                $output .= '<lastmod>'.$date_modified.'</lastmod>';
                $output .= '<changefreq>weekly</changefreq>';
                $output .= '</url>';
            }
        }

        $output .= '</urlset>';

        $this->response->addHeader('Content-Type: application/xml');
        $this->response->setOutput($output);

    }

	public function old_index() {

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$data['start'] = $this->chunk* ($page-1);
		$data['limit'] = $this->chunk;
	
        if (!empty($this->request->get['category_id'])) {
            $data['filter_category_id'] = $this->request->get['category_id'];
        } else {
            exit;
        }

        if ($this->config->get('google_sitemap_status')) {
			if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID ){
				$hreflang = 'en-in';

			}else{
				$hreflang = 'x-default';
			}

			$output = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			$this->load->model('catalog/product');
			
			$products_data = $this->model_catalog_product->getProducts($data);
			$products      = $products_data['products'];

			$i = 0;
			foreach ($products as $product) {
					$url = $this->url->link('product/product', 'product_id=' . $product['product_id']);
					$alternate_url = $this->getAlternateUrl($url);
					$output .= '<url>';
					$output .= '<loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
					$output .= '<changefreq>daily</changefreq>';
					$output .= '</url>';


				if($i == 5)
					//break;
				$i++;
			}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	public function getCategories($parent_id = 0, $current_path ='') {
 		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$chunk = 300;
		$start = $chunk* ($page-1);
		$limit = $chunk;

		if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID ){
			$hreflang = 'en-in';

		}else{
			$hreflang = 'x-default';

		}

		$output = '';
		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
                $new_path = $current_path . '_' . $result['category_id'];
            }
			$url =$this->url->link('product/category', 'path=' . $new_path);
			$alternate_url = $this->getAlternateUrl($url);
			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
			$date_modified = date('Y-m-d', strtotime($result['date_modified']));
            $output .= '<lastmod>'.$date_modified.'</lastmod>';
			$output .= '<changefreq>daily</changefreq>';
			$output .= '</url>';

			$output .= $this->getCategories($result['category_id'], $new_path);
		}
		return $output;

	}

	public function getAlternateUrl($url){
		//$url = "http://www.wsb.in/suti-brand-premium-quality-indian-leggings-stijpsuti-leg-plus";
		$arr_url = explode("/", $url);
		$slug = $arr_url[count($arr_url)-1];

		if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID ){

			$host_url = 'https://'.INDIA_STORE_HOST;
		}else{

			$host_url = 'https://'.INTERNATIONAL_STORE_HOST;
		}


		return $host_url."/".$slug;

	}
	public function makeCategoryXml()
	{
		$final_output = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$final_output .=   $this->getCategories(0);
		$final_output .= '</urlset>';
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($final_output);

	}
	public function makeInformationXml()
	{
		$final_output = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		if ($this->config->get('google_sitemap_status')) {
			if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID ){
				$hreflang = 'en-in';

			}else{
				$hreflang = 'x-default';

			}
		}
		$this->load->model('catalog/information');

		$informations = $this->model_catalog_information->getInformations();

		foreach ($informations as $information) {
			$url = $this->url->link('information/information', 'information_id=' . $information['information_id']);
			$alternate_url = $this->getAlternateUrl($url);

			$final_output .= '<url>';
			//$final_output .= '<loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
			$final_output .= '<loc>' . $alternate_url . '</loc>';
			//$final_output .= '<xhtml:link rel="alternate" href="'.$alternate_url.'" hreflang="'.$hreflang.'" />';
			$final_output .= '<changefreq>weekly</changefreq>';
			//$final_output .= '<priority>0.5</priority>';
			$final_output .= '</url>';
		}

        $final_output .= '<url><loc>'.$this->getAlternateUrl('').'contact-us</loc><changefreq>weekly</changefreq></url>';
        $final_output .= '<url><loc>'.$this->getAlternateUrl('').'storelocator</loc><changefreq>weekly</changefreq></url>';
        $final_output .= '<url><loc>'.$this->getAlternateUrl('').'business</loc><changefreq>weekly</changefreq></url>';

        $final_output .= '</urlset>';
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($final_output);

	}
	public function final_sitemap()
	{
		$final_sitemap = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$final_sitemap .= '<sitemap>';
		$final_sitemap .= '<loc>'.$this->getAlternateUrl('').'sitemap/information</loc>';
		//$final_sitemap .= '<changefreq>weekly</changefreq>';
		$final_sitemap .= '</sitemap>';
		$final_sitemap .= '<sitemap>';
		$final_sitemap .= '<loc>'.$this->getAlternateUrl('').'sitemap/category</loc>';
		//$final_sitemap .= '<changefreq>daily</changefreq>';
		$final_sitemap .= '</sitemap>';

		$final_sitemap .= $this->category_sitemap(0);


		$final_sitemap .= '</sitemapindex>';
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($final_sitemap);
	}

	private function category_sitemap($parent_id)
	{
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$results = $this->model_catalog_category->getCategories($parent_id);
		$final_sitemap = '';
		foreach ($results as $result) {
			$data['filter_category_id'] = $result['category_id'];
			$total_products  = $this->model_catalog_product->getTotalProducts($data);
			
            $data['start'] = "0";
            $data['limit'] =$this->chunk;

			if ((int)$total_products <= $this->chunk){
				$final_sitemap .= '<sitemap>';
				$final_sitemap .= '<loc>'.$this->getAlternateUrl('').'sitemap/category/' . $result['category_id']. '</loc>';
                $date_modified = $this->model_catalog_category->getProductModifiedDate($data);
                //$date_modified = date(DATE_ATOM, strtotime($result['p_modified']));
				if($date_modified != null)
				    $final_sitemap .= '<lastmod>'.$date_modified.'</lastmod>';
				//$final_sitemap .= '<changefreq>daily</changefreq>';
				//$output .= '<priority>0.7</priority>';
				$final_sitemap .= '</sitemap>';
			}
			else
			{
				$count = 0;
				$page = 1;
				while( $count < (int)$total_products ){
                    $data['start'] = $this->chunk* ($page-1);
                    $data['limit'] = $this->chunk;
                    $date_modified = $this->model_catalog_category->getProductModifiedDate($data);

					$final_sitemap .= '<sitemap>';
					$final_sitemap .= '<loc>'.$this->getAlternateUrl('').'sitemap/category/' . $result['category_id']. '/'.$page.'</loc>';
                    //$date_modified = date(DATE_ATOM, strtotime($result['p_modified']));
                    if($date_modified != null)
				        $final_sitemap .= '<lastmod>'.$date_modified.'</lastmod>';
					//$final_sitemap .= '<changefreq>daily</changefreq>';
					//$output .= '<priority>0.7</priority>';
					$final_sitemap .= '</sitemap>';
					$count += $this->chunk;
					$page += 1;


				}
			}

			//$final_sitemap .= $this->category_sitemap($result['category_id']);
		}
		return $final_sitemap;

	}
}
