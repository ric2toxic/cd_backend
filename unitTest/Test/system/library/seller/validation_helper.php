<?php

	//Author Divya Porwal
	// February 2017
class ValidationHelper {
	
     private $registry;
     private $db;
     private $_load;
     private $_cart;
     private $_config;
     public $total_data = array();
     public $total = 0;
     public $taxes;
     public $cst_class_id = 12;
     public $cst;
     private $_session;
     private $data = array();


     public function __construct($registry){
		 
       if( method_exists( $registry , 'get' ) ){
           $this->db       = $registry->get('db');
           $this->_load     = $registry->get('load');
           $this->_cart     = $registry->get('cart');
           $this->_config     = $registry->get('config');
           $this->_session = $registry->get('session');
       }
       else{
           $this->db       = $registry->db;
           $this->_load     = $registry->load;
           $this->_cart     = $registry->cart;
           $this->_config = $registry->config;
           $this->_session = $registry->session;
       }
     
       $this->registry = $registry;
     }
     
	public function setData() {
		
		$data = array();
		$data['sheet_data'] = array(			
			'0' => Array (
				'SKU Code' => 'MariyazCollectionvol2_L',
				'Pieces in Set' => 'abc',
				'Size' => 132,
				'Transfer Price' => 'abc',
				'AvailableSets' => '',
				'Weight of a Piece' => '12',
				'Any additional Comments' => 'abc aop  divyaporwal',
				'Size Set / Color Set' => 'Size Set',
				'Set Description' => '',
			),			
		 );
	
		$data['rules'] = array(
				'SKU Code' => '_validate_sku',
				'Size Set / Color Set' => '_validate_size_or_color',
				'Size' => '_validate_size',
				'Set Description' => '_validate_set_description',
				'Pieces in Set' => '_validate_pieces_in_set',
				'Transfer Price' => '_validate_price',
				'AvailableSets' => '_validate_available_sets',
				'Weight of a Piece' => '_validate_weight',
				'Any additional Comments' => '_validate_comment',
				);
		
		return $data;	
		
	}
	
	public function addRowdetails($sku_code, $product_id) {
			
		$data_insert = array(
			'product_id' => $product_id,
			'model' => '1',
			'sku' => $sku_code,
			'upc' => '1',
			'ean' => '1',
			'jan' => '1',
			'isbn' => '1',
			'mpn' => '1',
			'location' => '1',
			'quantity' => '1',
			'stock_status_id' => '1',
			'image' => 'NA',
			'manufacturer_id' => '1',
			'shipping' => '1',
			'price' => '1',
			'selling_price' => '1',
			'price_per_set' => '1',
			'piece_in_set' => '1',
			'seller_tax' => '1',
			'commission' => '1',
			'points' => '1',
			'tax_class_id' => '1',
			'date_available'=> '1',
			'weight' => '1',
			'weight_class_id' => '1',
			'length' => '1',
			'width' => '1',
			'height' => '1',
			'length_class_id' => '1',
			'subtract' => '1',
			'minimum' => '1',
			'sort_order' => '1',
			'status' =>'1',
			'viewed' => '1',
			'date_added' => 'NOW()',
			'date_modified' => 'NOW()',
			'sold_out' => '1',
			'is_single' => '1',
			'singles_product_id' => '1',
			'date_out_of_stock' => '1',
			'expected_dispatch_date' => 'NOW()',
			'mrp' => '1',
       );
       
       $this->insertRow($data_insert);      
   }
   
	public function insertRow($data_insert) {
	    $sql = "INSERT INTO `" . DB_PREFIX . "product`
		          SET
		            product_id = '" . $this->db->escape($data_insert['product_id']) . "',
		            model = '" . $this->db->escape(trim($data_insert['model'])) . "',
		            sku = '" . $this->db->escape(trim($data_insert['sku'])) . "',
		            upc = '" . $this->db->escape($data_insert['upc']) . "',
		            ean = '" . $this->db->escape($data_insert['ean']) . "',
		            jan = '" . $this->db->escape($data_insert['jan']) . "',
		            isbn = '" . $this->db->escape($data_insert['isbn']) . "',
		            mpn = '" . $this->db->escape($data_insert['mpn']) . "',
		            location = '" . $this->db->escape($data_insert['location']) . "',
		            quantity = '" . $this->db->escape($data_insert['quantity']) . "',
		            stock_status_id = '" . $this->db->escape($data_insert['stock_status_id']) . "',
		            image = '" . $this->db->escape($data_insert['image']) . "',
		            manufacturer_id =  '" . $this->db->escape($data_insert['manufacturer_id']) . "',
		            shipping = '" . $this->db->escape($data_insert['shipping']) . "',
		            price = '" . $this->db->escape($data_insert['price']) . "',
		            selling_price = '" . $this->db->escape($data_insert['selling_price']) . "',
		            price_per_set = '" . $this->db->escape($data_insert['price_per_set']) . "',
		            seller_tax = '" . $this->db->escape($data_insert['seller_tax']) . "',
		            commission = '" . $this->db->escape($data_insert['commission']) . "',
		            points = '" . $this->db->escape($data_insert['points']) . "',
		            tax_class_id = '" . $this->db->escape($data_insert['tax_class_id']) . "',
		            date_available = '" . $this->db->escape($data_insert['date_available']) . "',
		            weight = '" . $this->db->escape($data_insert['weight']) . "',
		            weight_class_id = '" . $this->db->escape($data_insert['weight_class_id']) . "',
		            length = '" . $this->db->escape($data_insert['length']) . "',
		            width = '" . $this->db->escape($data_insert['width']) . "',
		            height = '" . $this->db->escape($data_insert['height']) . "',
		            length_class_id = '" . $this->db->escape($data_insert['length_class_id']) . "',
		            subtract = '" . $this->db->escape($data_insert['subtract']) . "',
		            minimum = '" . $this->db->escape($data_insert['minimum']) . "',
		            sort_order = '" . $this->db->escape($data_insert['sort_order']) . "',
		            status = '" . $this->db->escape($data_insert['status']) . "',
		            viewed = '" . $this->db->escape($data_insert['viewed']) . "',
		            date_added = '" . $this->db->escape($data_insert['date_added']) . "',
		            date_modified = '" . $this->db->escape($data_insert['date_modified']) . "',
		            sold_out = '" . $this->db->escape($data_insert['sold_out']) . "',
		            is_single = '" . $this->db->escape($data_insert['is_single']) . "', 
		            singles_product_id = '" . $this->db->escape($data_insert['singles_product_id']) . "',
		            date_out_of_stock = '" . $this->db->escape($data_insert['date_out_of_stock']) . "',
		            expected_dispatch_date = '" . $this->db->escape($data_insert['expected_dispatch_date']) . "',
		            mrp = '" . $this->db->escape($data_insert['mrp']) . "'
		           ";
		$query = $this->db->query($sql);
   }
   
	public function deleteRow($sku_code,$product_id) {
	   
	   $sql =  "DELETE FROM " . DB_PREFIX . "product" .
                        " WHERE sku = '".$sku_code."' and product_id = '".$product_id."'";
       $query = $this->db->query($sql);
	   
   }
  	
	public function addProductdetails($seller_id,$product_id) {
		$data = array(
			'product_id' => $product_id,
			'seller_id' => $seller_id
			);
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_product`
		          SET
		            product_id = '" . $this->db->escape($data['product_id']) . "',
		            seller_id = '" . $this->db->escape($data['seller_id']) . "'
		            ";
		$query = $this->db->query($sql);
	}
	
	public function deleteProductdetails($product_id) {
		$sql =  "DELETE FROM " . DB_PREFIX . "ms_product" .
                        " WHERE product_id = '".$product_id."'";
		$query = $this->db->query($sql);
	}
	
	public function addRowSeller($seller_id) {

		$data_seller = array(
			'seller_id' => $seller_id,
			'nickname' => 'apy',
			'company' => 'abc',
			'website' => '1',
			'seller_description' => '1',
			'country_id' => '1',
			'zone_id' => '1',
			'avatar' => '1',
			'banner' => '1',
			'paypal' => '1',
			'date_created' => '1',
			'seller_approved' => 'NA',
			'product_validation' => '1',
			'seller_group' => '1',
			'commission_id' => '1',
			'address1' => '1',
			'address2' => '1',
			'pincode' => '1',
			'city' => '1',
			'pan' => '1',
			'tin' => '1',
			'tan' => '1',
			'non_returnable'=> '1',
			'bank_ac_holder_name' => '1',
			'bank_ac_number' => '1',
			'retype_ac_number' => '1',
			'ifsc_code' => '1',
			'bank_name' => '1',
			'bank_branch' => '1',
			'email' => '1',
			'alternate_email' => '1',
			'landline_no' => '1',
			'mobile_no' =>'1',
			'whatsapp_no' => '1',
			'alternatemobile_no' => 'NOW()',
			'theme' => 'NOW()',
			'access_token' => '1',
			'vacation_mode' => '1',
			'cod_available' => '1',
			'sor_enabled' => '1',
			'seller_agreement' => 'NOW()',
			'seller_agreement_acceptance_date' => '1',
       );
       
       $this->insertRowSeller($data_seller);      
		
		
	}
	
	public function insertRowSeller($data_seller) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_seller`
		          SET
		            seller_id = '" . $this->db->escape($data_seller['seller_id']) . "',
		            nickname = '" . $this->db->escape($data_seller['nickname']) . "',
		            company = '" . $this->db->escape($data_seller['company']) . "',
		            website = '" . $this->db->escape($data_seller['website']) . "',
		            seller_description = '" . $this->db->escape($data_seller['seller_description']) . "',
		            country_id = '" . $this->db->escape($data_seller['country_id']) . "',
		            zone_id = '" . $this->db->escape($data_seller['zone_id']) . "',
		            avatar = '" . $this->db->escape($data_seller['avatar']) . "',
		            banner = '" . $this->db->escape($data_seller['banner']) . "',
		            paypal = '" . $this->db->escape($data_seller['paypal']) . "',
		            date_created = '" . $this->db->escape($data_seller['date_created']) . "',
		            seller_approved = '" . $this->db->escape($data_seller['seller_approved']) . "',
		            product_validation =  '" . $this->db->escape($data_seller['product_validation']) . "',
		            seller_group = '" . $this->db->escape($data_seller['seller_group']) . "',
		            commission_id = '" . $this->db->escape($data_seller['commission_id']) . "',
		            address1 = '" . $this->db->escape($data_seller['address1']) . "',
		            address2 = '" . $this->db->escape($data_seller['address2']) . "',
		            city = '" . $this->db->escape($data_seller['city']) . "',
		            pan = '" . $this->db->escape($data_seller['pan']) . "',
		            tin = '" . $this->db->escape($data_seller['tin']) . "',
		            tan = '" . $this->db->escape($data_seller['tan']) . "',
		            non_returnable = '" . $this->db->escape($data_seller['non_returnable']) . "',
		            bank_ac_holder_name = '" . $this->db->escape($data_seller['bank_ac_holder_name']) . "',
		            bank_ac_number = '" . $this->db->escape($data_seller['bank_ac_number']) . "',
		            retype_ac_number = '" . $this->db->escape($data_seller['retype_ac_number']) . "',
		            ifsc_code = '" . $this->db->escape($data_seller['ifsc_code']) . "',
		            bank_name = '" . $this->db->escape($data_seller['bank_name']) . "',
		            bank_branch = '" . $this->db->escape($data_seller['bank_branch']) . "',
		            email = '" . $this->db->escape($data_seller['email']) . "',
		            alternate_email = '" . $this->db->escape($data_seller['alternate_email']) . "',
		            landline_no = '" . $this->db->escape($data_seller['landline_no']) . "',
		            mobile_no = '" . $this->db->escape($data_seller['mobile_no']) . "',
		            whatsapp_no = '" . $this->db->escape($data_seller['whatsapp_no']) . "',
		            alternatemobile_no = '" . $this->db->escape($data_seller['alternatemobile_no']) . "',
		            theme = '" . $this->db->escape($data_seller['theme']) . "',
		            access_token = '" . $this->db->escape($data_seller['access_token']) . "',
		            vacation_mode = '" . $this->db->escape($data_seller['vacation_mode']) . "', 
		            cod_available = '" . $this->db->escape($data_seller['cod_available']) . "',
		            sor_enabled = '" . $this->db->escape($data_seller['sor_enabled']) . "',
		            seller_agreement = '" . $this->db->escape($data_seller['seller_agreement']) . "',
		            seller_agreement_acceptance_date = '" . $this->db->escape($data_seller['seller_agreement_acceptance_date']) . "'
		           ";
		$query = $this->db->query($sql);
		
	}
	
	public function deleteRowSeller($seller_id) {
		$sql =  "DELETE FROM " . DB_PREFIX . "ms_seller" .
                        " WHERE seller_id = '".$seller_id."'";
		$query = $this->db->query($sql);
		
	}
	
	public function addRowCategory($category_id) {
		$data_category = array(
			'category_id' => $category_id,
			'image' => '2',
			'parent_id' => $category_id,
			'top' => '1',
			'column' => '1',
			'sort_order' => '1',
			'status' => '1',
			'tag_priority' => '1',
			'date_added' => '2016-11-03 15:42:40',
			'date_modified' => '2016-11-03 15:42:40',
			'min_weight' => '10.00',
			'max_weight' => '5.00'
       );
       $this->insertRowCategory($data_category);      
	}
	
	public function insertRowCategory($data_category) {
		           
		$sql = "INSERT INTO `oc_category`(`category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `status`, 
				`tag_priority`, `date_added`, `date_modified`, `min_weight`, `max_weight`) 
					VALUES (165,1,145,1,4,5,6,7,'2016-11-03 15:42:40','2016-11-03 15:42:40',10,11)";
					  
		$query = $this->db->query($sql);
	}
	
	public function deleteRowCategory($category_id) {
		$sql =  "DELETE FROM " . DB_PREFIX . "category" .
                        " WHERE category_id = '".$category_id."'";
                        
		$query = $this->db->query($sql);
	}
}

?>
