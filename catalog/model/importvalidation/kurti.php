<?php
/**
 * Incluse Base class
 * */
require_once('base.php');
/**
 * @calss ModelImportvalidationKurti
 * @date 05-05-2016
 * @author Ravindra Singh
 * @import ModelInventoryvalidationBase
 * */
class ModelImportvalidationKurti extends ModelInventoryvalidationBase {
	
	/**
	* Variable define filter_value
	* */
	public $filter_value = 0;
	/**
	 * Set value for filter_value(setter)
	 * */
	public function setFilterValue($val){
		$this->filter_value = $val;
	}
	
	/**
	 * Get value of filter_value(getter)
	 * */
	public function getFilterValue(){
		return $this->filter_value;
	}
	/**
	 * Validate fields
	 * */	
	public function validate($data = array()){
		//echo "<pre>"; print_r($data); exit;
		if(isset($data['product_data_key'])){
			$product_data_key = $data['product_data_key'];
			/**
			 * Set value for parent class variables
			 **/
				
			if(isset($product_data_key[1]) && !empty($product_data_key[1])){
				parent::setTitleKey($product_data_key[1]);
			}
			
			if(isset($product_data_key[2]) && !empty($product_data_key[2])){
				parent::setSkuKey($product_data_key[2]);
			}
			
			if(isset($product_data_key[3]) && !empty($product_data_key[3])){
				parent::setTransferPriceKey($product_data_key[3]);
			}
			
			if(isset($product_data_key[5]) && !empty($product_data_key[5])){
				parent::setPieceInSetKey($product_data_key[5]);
			}
			
			if(isset($product_data_key[6]) && !empty($product_data_key[6])){
				parent::setSizeColorSetKey($product_data_key[6]);
			}
			
			if(isset($product_data_key[7]) && !empty($product_data_key[7])){
				parent::setSizeKey($product_data_key[7]);
			}
			
			
			if(isset($product_data_key[8]) && !empty($product_data_key[8])){
				parent::setQuantityKey($product_data_key[8]);
			}
			
			if(isset($product_data_key[9]) && !empty($product_data_key[9])){
				parent::setNoColorBleedKey($product_data_key[9]);
			}
			
			if(isset($product_data_key['10']) && !empty($product_data_key['10'])){
				parent::setNoShrinkageKey($product_data_key['10']);
			}
			
			
			if(isset($product_data_key['11']) && !empty($product_data_key['11'])){
				parent::setStitchingKey($product_data_key['11']);
			}
			
			
			if(isset($product_data_key['12']) && !empty($product_data_key['12'])){
				parent::setSideSlitHorizontalStitchingKey($product_data_key['12']);
			}
			
			if(isset($product_data_key['13']) && !empty($product_data_key['13'])){
				parent::setBottomFoldStitchingKey($product_data_key['13']);
			}
			
			
		}
		
		if(isset($data['product_data'])){
			$data = $data['product_data'];
		}
		

		
		$current_warnings = array();
		//echo "<pre>"; print_r($data); exit;
		
		/**
		 * Set value for parent class variables
		 **/
		
		if(isset($data[1]) && !empty($data[1])){
			parent::setTitle($data[1]);
		}else{
			parent::setTitle("");
		}
		
		if(isset($data[2]) && !empty($data[2])){
			parent::setSku($data[2]);
		}else{
			parent::setSku("");
		}
		
		if(isset($data[3]) && !empty($data[3])){
			parent::setTransferPrice($data[3]);
		}else{
			parent::setTransferPrice("0");
		}
		
		if(isset($data[4]) && !empty($data[4])){
			parent::setSetDescription($data[4]);
		}else{
			parent::setSetDescription("");
		}
		
		
		if(isset($data[5]) && !empty($data[5])){
			parent::setPieceInSet($data[5]);
		}
		
		if(isset($data[6]) && !empty($data[6])){
			parent::setSizeColorSet($data[6]);
		}
		
		if(isset($data[7]) && !empty($data[7])){
			parent::setSize($data[7]);
		}
		
		
		if(isset($data[8]) && !empty($data[8])){
			parent::setQuantity($data[8]);
		}else{
			parent::setQuantity("");
		}
		
		if(isset($data[9]) && !empty($data[9])){
			parent::setNoColorBleed($data[9]);
		}
		
		if(isset($data['10']) && !empty($data['10'])){
			parent::setNoShrinkage($data['10']);
		}
		
		
		if(isset($data['11']) && !empty($data['11'])){
			parent::setStitching($data['11']);
		}
		
		
		if(isset($data['12']) && !empty($data['12'])){
			parent::setSideSlitHorizontalStitching($data['12']);
		}
		
		if(isset($data['13']) && !empty($data['13'])){
			parent::setBottomFoldStitching($data['13']);
		}
		
		$description_fields = array();
		for($a = 9; $a <= 15; $a++){
			if(!empty($product_data_key[$a]) && !empty($data[$a])){
				$description_fields[$product_data_key[$a]] = $data[$a];
			}
		}
		if(!empty($description_fields)){
			$description = parent::create_description($description_fields);
			parent::setDescription($description);
		}
		
		$filters = array();
		$filter_value = "";
		for($a = 16; $a <= 21; $a++){
			if(!empty($product_data_key[$a]) && !empty($data[$a])){
				$filter_group = $this->get_filter_group($product_data_key[$a]);
				if(empty($filter_group)){
					$fgkErr = "Invalid Filter Group";
					$warning[$i]['key'] = "size_color_set_key";
					$warning[$i]['message'] = $fgkErr;
					$i++;
				}else{
					$filter_value.= $data[$a]." ,";
					$color_size_set_validate = parent::color_size_set_validate($data[$a]);
					$filter_data = parent::get_filter($data[$a],$filter_group['filter_group_id']);
					if(!empty($filter_data['filter_id'])){
						parent::setFilters($filter_data['filter_id']);
						$this->setFilterValue($filter_value);
					}
				}
			}
			/*if(!empty($data[$a]) && !empty($data[$a])){
				$filters[$product_data_key[$a]] = $data[$a];
			}*/
		}
		
		
		$base_warnings = parent::validate();
		
		return $warnings = array_merge($base_warnings,$current_warnings);
		
	}
	
	public function meta_data(){
		$parent_id = "61";
		$string = "kurti ,";
		// Create Meta Tags and keywords 
		$categories = parent::getCategories($parent_id);
		//echo "<pre>"; print_r($categories); exit;
		foreach($categories as $category){
			$string.= $category['name'].' ,';
		}
		$string.= $this->GetFilterValue()." wholesale";
		
		// Create Meta Title 
		$meta_title = parent::getTitle().'-'.$this->MsLoader->MsSeller->getNickname().'_'.parent::getSku().'-wholesalebox'; 
		parent::setMetaTitle($this->toSlug($meta_title));
		
		// Create Meta Tags 
		$meta_tags = $string; 
		parent::setMetaTags($meta_tags);
		
		// Create Meta Keywords 
		$meta_keywords = $string; 
		parent::setMetaKeywords($meta_keywords);
		
		
		// Create Meta Description 
		
		$description = "";
		$description.= parent::getTitle().' ,'.$this->GetFilterValue()." Avalaible in lowest wholesale rates , ".$this->MsLoader->MsSeller->getNickname().'_'.parent::getSku();
		$meta_description = $description; 
		parent::setMetaDescription($meta_description);
		//return parent::toSlug($string);
	}
	
	public function product_data_map(){
		//echo "<pre>"; print_r(parent::create_meta_title()); exit;
		$directory =  DIR_IMAGE.'catalog/VAS_JP/kurtis/';
		//echo $directory = $this->image_path.$this->seller_code .'/'. $this->image_folder ;; exit;
		$product_map = array();
		$product_map['title'] = parent::getTitle();
		$product_map['sku'] = parent::getSku();
		$product_map['price'] = parent::getTransferPrice();
		$product_map['set_description'] = parent::getSetDescription();
		$product_map['description'] = parent::getDescription();
		$product_map['piece_in_set'] = parent::getPieceInSet();
		$product_map['quantity'] = parent::getQuantity();
		$product_map['meta_title'] = parent::getMetaTitle();
		$product_map['tag'] = parent::getMetaTags();
		$product_map['meta_keyword'] = parent::getMetaKeywords();
		$product_map['meta_description'] = parent::getMetaDescription();
		$product_map['product_filter'] = parent::getFilters();
		$product_map['product_image'] = parent::readImages($directory, parent::getSku());
		if(isset($product_map['product_image'][0]) && !empty($product_map['product_image'][0])){
			$product_map['image'] = $product_map['product_image'][0];
		}
		return $product_map;
	}
	
}
