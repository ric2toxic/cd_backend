<?php
class ControllerReportProductRating extends Controller {
	public function index() {
		//$this->load->language('report/product_purchased');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('report/product_rating', $data);

		$this->document->setTitle($data['heading_title']);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('report/product_rating', 'token=' . $this->session->data['token'], 'SSL')
		);

        $data['form_action'] = $this->url->link('report/product_rating/product_csv', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('report/product');

        $data['token']  = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
        $data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('report/product_rating.tpl', $data));
	}

    public function product_csv(){
        // echo "<pre>"."asdasadas"; print_r($this->request->post); echo "</pre>"; die;
        if(empty($this->request->post)){
            return true;
        }
        $sql = "SELECT DISTINCT p.product_id, p.model, CONCAT( oc.firstname , ' ', oc.lastname) as seller_name, 
				ocd.name as category_name ,ms.nickname 
                FROM oc_product p
                INNER JOIN oc_product_to_category ptc ON (p.product_id = ptc.product_id)
                INNER JOIN oc_ms_product msp ON (p.product_id = msp.product_id)
                INNER JOIN oc_ms_seller ms ON (msp.seller_id = ms.seller_id)
                INNER JOIN oc_product_to_category opc ON (opc.product_id = p.product_id)
                INNER JOIN oc_category_description ocd ON (ocd.category_id = opc.category_id)
                INNER JOIN oc_customer oc ON ms.seller_id = oc.customer_id
                WHERE ocd.language_id = 1 ";
                
        if( !empty($this->request->post['filter_date_from']) && !empty($this->request->post['filter_date_to']) ){
            $sql .= " AND p.date_added BETWEEN CAST('".$this->request->post['filter_date_from']."' AS DATE) AND CAST('".$this->request->post['filter_date_to']."' AS DATE)";
        }elseif( !empty($this->request->post['filter_date_from']) ){
            $sql .= " AND p.date_added > CAST('".$this->request->post['filter_date_from']."' AS DATE)";
        }elseif( !empty($this->request->post['filter_date_to']) ){
            $sql .= " AND p.date_added < CAST('".$this->request->post['filter_date_to']."' AS DATE)";
        }

        if( !empty($this->request->post['filter_category_id']) ){
            $sql .= " AND ptc.category_id = ".$this->request->post['filter_category_id'];
        }

        if( !empty($this->request->post['filter_seller_id']) ){
            $sql .= " AND msp.seller_id = ".$this->request->post['filter_seller_id'];
        }

        $sql .= " GROUP BY p.product_id";

        $result = $this->db->query($sql)->rows;

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=report.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "product_id,model,seller_name,category,seller_code,rating\r\n";
        
        foreach($result as $res){
            $rating = $this->db->sp_query("CALL syncRatingsToSolr(". $res['product_id'] .")")->row['rat'];
            echo $res['product_id'].",".$res['model'].",".$res['seller_name'].",".$res['category_name'].",".$res['nickname'].",".$rating."\r\n";
        }
    }
}
