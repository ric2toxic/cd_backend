<?php 
class ModelDynamicCategoriesGeneral extends Model {
	public function index($product_id, $sor_product_id){
		
	}
	public function UpdateSetDescription(){
        if (isset($this->request->post['set_des'])) {
            $set_des 		= '"'.$this->request->post['set_des'].'"';
            $product_id 	= $this->request->post['product_id'];
            $type 			= 'set_description';
            $this->InformSellerChangeLog($type, $set_des, $product_id);
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET set_description = ".$set_des." WHERE product_id = '" . (int)$product_id . "'");
            if(!empty($this->request->post['sor_product_id']) && isset($this->request->post['sor_product_id'])){
                $sku_product_id = $this->request->post['sor_product_id'];
                $this->db->query("UPDATE " . DB_PREFIX . "product_description SET set_description = ".$set_des." WHERE product_id = '" . (int)$sku_product_id . "'");
            }
            $rt = 'success';
        }else{
            $rt = 'error';
        }
        return $rt;
    }
}