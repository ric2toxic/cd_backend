<?php
/**
 * BlogController
 * Use for wordpress(/blog), 
 * @author Manish
 */
require_once('system.php');
require_once ('data_packet.php');
require_once(DIR_SYSTEM . 'library/document.php');
require_once(DIR_SYSTEM . 'library/cart.php');
require_once(DIR_SYSTEM . 'library/solr/product.php');
require_once(DIR_SYSTEM . 'library/solr/model_solr_product.php');

class BlogController extends SystemController
{    
    public function __construct($params) {

        parent::__construct($params);
        // Currency
        //$this->registry->set('currency', new Currency($this->registry));
        // Tax
        //$this->registry->set('tax', new Tax($this->registry));
        // Document
        $this->registry->set('document', new Document($this->registry));
        // Cart
        $this->registry->set('cart', new Cart($this->registry));
    }   
    
    
    /**
     * get_latest_product
     * Get latest product according to customer preference    
     * @param   category_ids    POST
     * @return  latest_product  JSON
     * @author  Manish
     */
    public function get_latest_product() { 
        // $rt             = array();           
        // $user_id        = 0;
        // $view_more      = 0;
        // $page           = 1;
        // $group_limit    = 2;
        // $flag           = 'blog';        

        $this->load->model('restapi/service');

        $this->request['filter']['start'] = 0;
        $this->request['filter']['limit'] = $this->request['product_limit'];
        $this->request['filter']['is_group'] = 1;
        $this->request['filter']['group_field'] = 'seller_id';
        $this->request['filter']['group_limit'] = 1;        
        $this->request['filter']['filter_category_id'] = !empty($this->request['product_category']) ? $this->request['product_category'] : '';        
        $this->request['filter']['filter_name'] = !empty($this->request['keyword']) ? str_replace('-', ' ', strtolower($this->request['keyword'])) : ''; 
        $this->request['filter']['group_sort_data_by'] = 'date_added';               
        $this->request['filter']['filter_filter'] = !empty($this->request['product_filter']) ? $this->request['product_filter'] : '';

        // echo '<pre>'; print_r($this->request['filter']); exit;

        $solr = new SolrProduct($this);
        $return = $solr->getProductFromSolrOnly($this->request['filter'])['result_group_field'];        

        $final_ids = array();
        $product_data = array();

        if (!empty($return)) {
            foreach ($return as $key => $value) {
                $final_ids[] = $value[0]['id'];
            }

            $call_from = 'blog';
            $solr->getProductDetails($final_ids, array(), $call_from, $product_data); 
        }

        $rt['success_code'] = '1001';
        $rt['status'] = '1';
        $rt['status_text'] = 'Success';
        $rt['result'] = $product_data;
           
        echo json_encode($rt); exit;
    }    
}
?>
