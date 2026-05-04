<?php 
class ModelDynamicCategoriesYardage extends Model {
	public function index($product_id, $product_sor_id) {

	}

	public function UpdateSetDescription($product_id, $sor_product_id, $data=array()){
		
		$fabric_length 	= $data['fabric_length'];
		$fabric_width 	= $data['fabric_width'];
		$fabric_weight 	= $data['fabric_weight'];

		$set_des 		= '" 1 Set = ' . $fabric_length . ' meters; Width: ' . $fabric_width . ' inches "';
        $type		 	= 'set_description';

        $this->load->model('seller/manage_inventory');
	    $this->model_seller_manage_inventory->InformSellerChangeLog($type, $set_des, $product_id);
	    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET set_description = ".$set_des." WHERE product_id = '" . (int)$product_id . "'");
	    if(!empty($sor_product_id) && isset($sor_product_id)){
	        $this->db->query("UPDATE " . DB_PREFIX . "product_description SET set_description = ".$set_des." WHERE product_id = '" . (int)$sor_product_id . "'");
        }
    }
    public function updatePriceMeter($product_id, $sor_product_id, $data=array()){
		$fabric_length 	= $data['fabric_length'];
		$fabric_width 	= $data['fabric_width'];
		$fabric_weight 	= $data['fabric_weight'];
		$price_meter 	= $data['price_meter'];

		$price 			= $price_meter * $fabric_length;
		$price_per_set 	= $price_meter * $fabric_length;
		$weight   		= $fabric_weight * $fabric_length * 1.2 / 1000; 


		//Selling price  and commission code 
		$commission_limit = 10;

        $record = $this->db->query("SELECT price, seller_tax, commission FROM ".DB_PREFIX."product WHERE product_id = '" . $product_id . "'");
        
        $seller_tax_factor = 1.0 + ( (float)$record->row['seller_tax'] / 100.0 );
        
        /* Evaluating new commission based on the price change */
        $commission = (float)($record->row['commission']);
        $fv             = $record->row['price'];
        if ( $commission < $commission_limit ) {
            $old_commission_factor = 1.0 + ($commission / 100.0);
            $old_selling_price = ceil( $old_commission_factor * (float)$record->row['price'] / $seller_tax_factor );
            $new_selling_price = ceil( $old_commission_factor * $fv / $seller_tax_factor);
            
            if ( $new_selling_price < $old_selling_price ) {
                $expected_selling_price = $old_selling_price - (0.8 * ($old_selling_price - $new_selling_price)); // Taking 20% benefit
                $new_commission = (($expected_selling_price*$seller_tax_factor/$fv) - 1)*100;
                
                if ($new_commission > $commission and $new_commission < $commission_limit)
                    $commission = $new_commission;
            }
        } /* evaluation new commission */
                    
        $piece_in_set = 1;
        $price_per_set = $fv * $piece_in_set;
        
        $commission_factor = 1.0 + ($commission / 100.0);
        $selling_price = ceil($commission_factor * $fv / $seller_tax_factor);
        $new = $fv;
        $type = 'price';
        $this->model_seller_manage_inventory->InformSellerChangeLog($type, $new, $product_id);
        if (is_numeric($fv)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET price = ".$fv.", price_per_set = ".$price_per_set.", selling_price = " . $selling_price . ", commission = " . $commission . ", weight = '" . $weight . "' WHERE product_id = '" . $product_id . "'"); 
        }

    }

}