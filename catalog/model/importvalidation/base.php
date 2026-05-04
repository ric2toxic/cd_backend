<?php
/**
 * @calss ModelInventoryvalidationBase
 * @date 05-05-2016
 * @author Ravindra Singh
 * */
class ModelInventoryvalidationBase extends Model {



	/**
	* Variable define title_key
	* */
	public $title_key = 0;
	/**
	* Variable define sku_key
	* */
	public $sku_key = 0;
	/**
	* Variable define trinsfer_price_key
	* */
	public $transfer_price_key = 0;
	/**
	* Variable define piece_in_set_key
	* */
	public $piece_in_set_key = 0;
	/**
	* Variable define size_color_set_key
	* */
	public $size_color_set_key = 0;
	/**
	* Variable define size_key
	* */
	public $size_key = 0;
	/**
	* Variable define quantity_key
	* */
	public $quantity_key = 0;
	/**
	* Variable define no_color_bleed_key
	* */
	public $no_color_bleed_key = 0;
	/**
	* Variable define no_shrinkage_key
	* */
	public $no_shrinkage_key = 0;

	/**
	* Variable define stitching_key
	* */
	public $stitching_key = 0;
	/**
	* Variable define side_slit_horizontal_stitching_key
	* */
	public $side_slit_horizontal_stitching_key = 0;
	/**
	* Variable define bottom_fold_stitching_key
	* */
	public $bottom_fold_stitching_key = 0;

	
	
	/**
	* Variable define sku
	* */
	public $sku;
	/**
	* Variable define transfer_price
	* */
	public $transfer_price = 0;
	/**
	* Variable define title
	* */
	public $title;
	/**
	* Variable define piece_in_set
	* */
	public $piece_in_set = 0;
	/**
	* Variable define set_description
	* */
	public $set_description = 0;
	/**
	* Variable define description
	* */
	public $description = 0;
	/**
	* Variable define quantity
	* */
	public $quantity = 0;
	

	/**
	* Variable define size_color_set
	* */
	public $size_color_set = 0;
	
	
	/**
	* Variable define size
	* */
	public $size = 0;
	
	/**
	* Variable define no_color_bleed
	* */
	public $no_color_bleed = 0;
	
	/**
	* Variable define no_shrinkage
	* */
	public $no_shrinkage = 0;
	
	/**
	* Variable define stitching
	* */
	public $stitching = 0;
	
	/**
	* Variable define side_slit_horizontal_stitching
	* */
	public $side_slit_horizontal_stitching = 0;
	
	/**
	* Variable define bottom_fold_stitching
	* */
	public $bottom_fold_stitching = 0;
	
	/**
	* Variable define meta_title
	* */
	public $meta_title = 0;
	
	/**
	* Variable define meta_tags
	* */
	public $meta_tags = 0;
	
	/**
	* Variable define meta_keywords
	* */
	public $meta_keywords = 0;
	
	/**
	* Variable define meta_description
	* */
	public $meta_description = 0;
	
	
	public $filters = array();
	
	
	/*public function __construct() {
	}*/

	/**
	 * Set value for filters(setter)
	 * */
	public function setFilters($val){
		//echo "<pre>"; print_r($this->filters);
		array_push($this->filters,$val);
		//echo "<pre>"; print_r($this->filters); exit;
	}
	
	/**
	 * Get value of filters(getter)
	 * */
	public function getFilters(){
		return $this->filters;
	}
	
	
	/**
	 * Set value for meta_title(setter)
	 * */
	public function setMetaTitle($val){
		$this->meta_title = $val;
	}
	/**
	 * Set value for meta_tags(setter)
	 * */
	public function setMetaTags($val){
		$this->meta_tags = $val;
	}
	/**
	 * Set value for meta_keywords(setter)
	 * */
	public function setMetaKeywords($val){
		$this->meta_keywords = $val;
	}
	/**
	 * Set value for meta_description(setter)
	 * */
	public function setMetaDescription($val){
		$this->meta_description = $val;
	}
	
	/**
	 * Set value for title_key(setter)
	 * */
	public function setTitleKey($val){
		$this->title_key = $val;
	}
	/**
	 * Set value for sku_key(setter)
	 * */
	public function setSkuKey($val){
		$this->sku_key = $val;
	}
	/**
	 * Set value for transfer_price_key(setter)
	 * */
	public function setTransferPriceKey($val){
		$this->transfer_price_key = $val;
	}
	/**
	 * Set value for piece_in_set_key(setter)
	 * */
	public function setPieceInSetKey($val){
		$this->piece_in_set_key = $val;
	}
	/**
	 * Set value for size_color_set_key(setter)
	 * */
	public function setSizeColorSetKey($val){
		$this->size_color_set_key = $val;
	}
	/**
	 * Set value for size_key(setter)
	 * */
	public function setSizeKey($val){
		$this->size_key = $val;
	}
	/**
	 * Set value for quantity_key(setter)
	 * */
	public function setQuantityKey($val){
		$this->quantity_key = $val;
	}
	/**
	 * Set value for no_color_bleed_key(setter)
	 * */
	public function setNoColorBleedKey($val){
		$this->no_color_bleed_key = $val;
	}
	/**
	 * Set value for no_shrinkage_key(setter)
	 * */
	public function setNoShrinkageKey($val){
		$this->no_shrinkage_key = $val;
	}
	/**
	 * Set value for stitching_key(setter)
	 * */
	public function setStitchingKey($val){
		$this->stitching_key = $val;
	}
	/**
	 * Set value for side_slit_horizontal_stitching_key(setter)
	 * */
	public function setSideSlitHorizontalStitchingKey($val){
		$this->side_slit_horizontal_stitching_key = $val;
	}
	/**
	 * Set value for bottom_fold_stitching_key(setter)
	 * */
	public function setBottomFoldStitchingKey($val){
		$this->bottom_fold_stitching_key = $val;
	}
	
	/**
	 * Set value for sku(setter)
	 * */
	public function setSku($val){
		$this->sku = $val;
	}
	/**
	 * Set value for transfer price(setter)
	 * */
	public function setTransferPrice($val){
		$this->transfer_price = $val;
	}
	/**
	 * Set value for title(setter)
	 * */
	public function setTitle($val){
		$this->title = $val;
	}
	/**
	 * Set value for piece_in_set(setter)
	 * */
	public function setPieceInSet($val){
		$this->piece_in_set = $val;
	}
	/**
	 * Set value for set_description(setter)
	 * */
	public function setSetDescription($val){
		$this->set_description = $val;
	}
	/**
	 * Set value for description(setter)
	 * */
	public function setDescription($val){
		$this->description = $val;
	}
	
	/**
	 * Set value for quantity(setter)
	 * */
	public function setQuantity($val){
		$this->quantity = $val;
	}
	

	/**
	 * Set value for size_color_set(setter)
	 * */
	public function setSizeColorSet($val){
		$this->size_color_set = $val;
	}
	
	/**
	 * Set value for size(setter)
	 * */
	public function setSize($val){
		$this->size = $val;
	}
	
	/**
	 * Set value for no_color_bleed(setter)
	 * */
	public function setNoColorBleed($val){
		$this->no_color_bleed = $val;
	}
	/**
	 * Set value for no_shrinkage(setter)
	 * */
	public function setNoShrinkage($val){
		$this->no_shrinkage = $val;
	}
	/**
	 * Set value for stitching(setter)
	 * */
	public function setStitching($val){
		$this->stitching = $val;
	}
	/**
	 * Set value for side_slit_horizontal_stitching(setter)
	 * */
	public function setSideSlitHorizontalStitching($val){
		$this->side_slit_horizontal_stitching = $val;
	}
	/**
	 * Set value for bottom_fold_stitching(setter)
	 * */
	public function setBottomFoldStitching($val){
		$this->bottom_fold_stitching = $val;
	}
	
	
	
	/**
	 * Get value of meta_title(getter)
	 * */
	public function getMetaTitle(){
		return $this->meta_title;
	}
	/**
	 * Get value of meta_tags(getter)
	 * */
	public function getMetaTags(){
		return $this->meta_tags;
	}
	/**
	 * Get value for meta_keywords(getter)
	 * */
	public function getMetaKeywords(){
		return $this->meta_keywords;
	}
	/**
	 * Get value of meta_description(getter)
	 * */
	public function getMetaDescription(){
		return $this->meta_description;
	}
	
	
	/**
	 * Get value of title_key(getter)
	 * */
	public function getTitleKey(){
		return $this->title_key;
	}
	/**
	 * Get value of sku_key(getter)
	 * */
	public function getSkuKey(){
		return $this->sku_key;
	}
	/**
	 * Get value of trinsfer_price_key(getter)
	 * */
	public function getTrinsferPriceKey(){
		return $this->trinsfer_price_key;
	}
	/**
	 * Get value of piece_in_set_key(getter)
	 * */
	public function getPieceInSetKey(){
		return $this->piece_in_set_key;
	}
	/**
	 * Get value of size_color_set_key(getter)
	 * */
	public function getSizeColorSetKey(){
		return $this->size_color_set_key;
	}
	/**
	 * Get value of size_key(getter)
	 * */
	public function getSizeKey(){
		return $this->size_key;
	}
	/**
	 * Get value of quantity_key(getter)
	 * */
	public function getQuantityKey(){
		return $this->quantity_key;
	}
	/**
	 * Get value of no_color_bleed_key(getter)
	 * */
	public function getNoColorBleedKey(){
		return $this->no_color_bleed_key;
	}
	/**
	 * Get value of no_shrinkage_key(getter)
	 * */
	public function getNoShrinkageKey(){
		return $this->no_shrinkage_key;
	}
	/**
	 * Get value of stitching_key(getter)
	 * */
	public function getStitchingKey(){
		return $this->stitching_key;
	}
	/**
	 * Get value of side_slit_horizontal_stitching_key(getter)
	 * */
	public function getSideSlitHorizontalStitchingKey(){
		return $this->side_slit_horizontal_stitching_key;
	}
	/**
	 * Get value for bottom_fold_stitching_key(getter)
	 * */
	public function getBottomFoldStitchingKey(){
		return $this->bottom_fold_stitching_key;
	}
	
	
	/**
	 * Get value of sku(getter)
	 * */
	public function getSku(){
		return $this->sku;
	}
	/**
	 * Get value of transfer price(getter)
	 * */
	public function getTransferPrice(){
		return $this->transfer_price;
	}
	/**
	 * Get value of title(getter)
	 * */
	public function getTitle(){
		return $this->title;
	}
	/**
	 * Get value of piece_in_set(getter)
	 * */
	public function getPieceInSet(){
		return $this->piece_in_set;
	}
	/**
	 * Get value of set_description(getter)
	 * */
	public function getSetDescription(){
		return $this->set_description;
	}
	/**
	 * Get value of description(getter)
	 * */
	public function getDescription(){
		return $this->description;
	}
	/**
	 * Get value of quantity(getter)
	 * */
	public function getQuantity(){
		return $this->quantity;
	}
	
	/**
	 * Get value for size_color_set(getter)
	 * */
	public function getSizeColorSet($val){
		return $this->size_color_set;
	}	
	/**
	 * Get value of size(getter)
	 * */
	public function getSize(){
		return $this->size;
	}
	
	
	/**
	 * Get value of no_color_bleed(getter)
	 * */
	public function getNoColorBleed(){
		return $this->no_color_bleed;
	}
	/**
	 * Get value of no_shrinkage(getter)
	 * */
	public function getNoShrinkage(){
		return $this->no_shrinkage;
	}
	/**
	 * Get value of stitching(getter)
	 * */
	public function getStitching(){
		return $this->stitching;
	}
	/**
	 * Get value of side_slit_horizontal_stitching(getter)
	 * */
	public function getSideSlitHorizontalStitching(){
		return $this->side_slit_horizontal_stitching;
	}
	/**
	 * Get value of bottom_fold_stitching(getter)
	 * */
	public function getBottomFoldStitching(){
		return $this->bottom_fold_stitching;
	}
	
	/**
	 * Validate common fields
	 * */	
	
	public function validate($data = array()){
		$warning = array();
		$i = 0;
		 /*@sku validation
		 * @rule preg_match("/^[-a-zA-Z_1-9]*$/",$sku) || Only alphabets,Numbers,underscore(_) and dashes(-) are allowed.
		 * */
		if (!preg_match("/^[-a-zA-Z_1-9]*$/",$this->sku)) {
		  $skuErr = "Invalid sku code.Only alphabets,Numbers,underscore(_) and dashes(-) are allowed.";
		  $warning[$i]['key'] = "sku";
		  $warning[$i]['message'] = $skuErr;
		  $i++;
		}elseif(empty($this->sku)){
			$skuErr = "Invalid sku code.Field can't be empty.";
			$warning[$i]['key'] = "sku";
			$warning[$i]['message'] = $skuErr;
			$i++;
		}else{
			$pdata = $this->getProductIdBySkuAndSeller($this->sku);
			if(!empty($pdata)){
				$skuErr = "Invalid sku code.Already exists with same name.";
				$warning[$i]['key'] = "sku";
				$warning[$i]['message'] = $skuErr;	
				$i++;
			}
			
		}
		
		 /*@transfer_price validation
		 * @rule is_numeric($transfer_price) || Only Numbers are allowed.
		 * */
		if (!is_numeric($this->transfer_price)) {
			  $tpErr = "Invalid transfer price.Only numbers are allowed.";
			  $warning[$i]['key'] = "transfer_price";
			  $warning[$i]['message'] = $tpErr;
			  $i++;
		}else{
			if($this->transfer_price <= 0){
				$tpErr = "Transfer price must be greater than 0.";
				$warning[$i]['key'] = "transfer_price";
				$warning[$i]['message'] = $tpErr;
				$i++;
			}
		}
		
		
		 /*@title validation
		 * @rule empty($transfer_price) || Field can't be empty.
		 * */
		if (empty($this->title)){
		  $titleErr = "Invalid title.Field can't be empty.";
		  $warning[$i]['key'] = "title";
		  $warning[$i]['message'] = $titleErr;
		  $i++;
		}
		
		
		 /*@piece_in_set validation
		 * @rule only integer number || Only Numbers are allowed.
		 * */
		 
		if (!preg_match('/^[1-9]*$/', $this->piece_in_set)) {
			  $psErr = "Only integer numbers are allowed.";
			  $warning[$i]['key'] = "piece_in_set";
			  $warning[$i]['message'] = $psErr;
			  $i++;
		}
		
		/*@quantity validation
		 * @rule only number || Only Numbers are allowed.
		 * */
		if (!preg_match('/^[0-9]*$/', $this->quantity)) {
			  $qErr = "Only numbers are allowed.";
			  $warning[$i]['key'] = "quantity";
			  $warning[$i]['message'] = $qErr;
			  $i++;
		}
		
		
		/*@size_color_set validation
		 * @rule Color Set or Size Set
		 * */
		if(!empty($this->size_color_set_key) || $this->size_color_set_key != 0){
			$filter_group = $this->get_filter_group($this->size_color_set_key);
			if(empty($filter_group)){
				$fgkErr = "Invalid Filter Group";
				$warning[$i]['key'] = "size_color_set_key";
				$warning[$i]['message'] = $fgkErr;
				$i++;
			}else{
				//echo "<Pre>"; print_r($filter_group); exit;
				$color_size_set_validate = $this->color_size_set_validate($this->size_color_set);
				if(empty($color_size_set_validate)){
					$filter_data = $this->get_filter($this->size_color_set,$filter_group['filter_group_id']);
					if(!empty($filter_data['filter_id'])){
						$this->setFilters($filter_data['filter_id']);
					}
				}else{
					$warning = array_merge($color_size_set_validate,$warning);	
					$i++;
				}
			}
		}


		/*@no_color_bleed validation
		 * @rule yes or empty || yes or empty.
		 * */
		if($this->no_color_bleed != '0'){ 
			if (empty($this->no_color_bleed) || $this->no_color_bleed != 'YES') {
				  $ncbErr = "Invalid value for no_color_bleed.";
				  $warning[$i]['key'] = "no_color_bleed";
				  $warning[$i]['message'] = $ncbErr;
				  $i++;
			}
		}
		/*@no_shrinkage validation
		 * @rule yes or empty || yes or empty.
		 * */
		if($this->no_shrinkage != '0'){
			if (empty($this->no_shrinkage) || $this->no_shrinkage != 'YES') {
				  $nsErr = "Invalid value for no_color_bleed.";
				  $warning[$i]['key'] = "no_shrinkage";
				  $warning[$i]['message'] = $nsbErr;
				  $i++;
			}
		}
		
		/*@stitching validation
		 * @rule empty || Can not be empty.
		 * */
		if($this->stitching != '0'){
			if (empty($this->stitching)) {
				  $stErr = "Field can not be empty.";
				  $warning[$i]['key'] = "no_shrinkage";
				  $warning[$i]['message'] = $stbErr;
				  $i++;
			}
		}
		
		/*@side_slit_horizontal_stitching validation
		 * @rule yes or empty || yes or empty.
		 * */
		if($this->side_slit_horizontal_stitching != '0'){ 
			if (empty($this->side_slit_horizontal_stitching) || $this->side_slit_horizontal_stitching != 'YES') {
				  $sshtErr = "Invalid value for no_color_bleed.";
				  $warning[$i]['key'] = "side_slit_horizontal_stitching";
				  $warning[$i]['message'] = $sshtErr;
				  $i++;
			}
		}
		
		/*@bottom_fold_stitching validation
		 * @rule single ,Double or empty || single ,Double or empty.
		 * */
		if($this->bottom_fold_stitching != '0'){
			if (!empty($this->bottom_fold_stitching) || $this->bottom_fold_stitching != 'single' || $this->bottom_fold_stitching != 'double') {
				  $bfsErr = "Invalid value for bottom_fold_stitching.";
				  $warning[$i]['key'] = "bottom_fold_stitching";
				  $warning[$i]['message'] = $bfsErr;
				  $i++;
			}
		}
		
		return $warning;
		
	}
	/**
	 * check filter validation
	 * 
	 */
	 
	public function color_size_set_validate($field_value = '') {
		$warning = array();
		if(!empty($field_value)){
			if($field_value == 'Color Set'){ 
				/*@size validation
				 * @rule empty($transfer_price) || Field can't be empty.
				 * */
				if(empty($this->size)){
					  $sErr = "Field can't be empty.";
					  $warning[0]['key'] = "size";
					  $warning[0]['message'] = $sErr;
				}
				//use size field
			}else if($field_value == 'Size Set'){ 
				//skip size field
			}
		}
		return $warning;
	}
	/**
	 * getSingleProductBySkuAndSellerId
	 * 
	 */
	public function getProductIdBySkuAndSeller($sku){
		$seller_id = $this->customer->getId();
		$q = "SELECT p.*
			  FROM ".DB_PREFIX."product p
			  LEFT JOIN " . DB_PREFIX . "ms_product mp ON (p.product_id = mp.product_id) 
			  WHERE p.sku='".$sku."' AND mp.seller_id = '".$seller_id."'";
		$query = $this->db->query($q);
		return $query->row;
	}
	/**
	 * get filter group id 
	 **/
	public function get_filter_group($field_value){
		$q = "SELECT fgd.*
			  FROM ".DB_PREFIX."filter_group_description fgd
			  WHERE fgd.name='".$field_value."'";
		$query = $this->db->query($q);
		return $query->row;
	}
	
	/**
	 * get filter id 
	 **/
	public function get_filter($field_value,$filter_group_id){
		$q = "SELECT 
				fd.*,
				f.filter_group_id
			  FROM
			  	".DB_PREFIX."filter AS f
			  INNER JOIN ".DB_PREFIX."filter_description fd 
			  	ON f.filter_id = fd.filter_id
			  		AND fd.name='".$this->db->escape($field_value)."'
			  WHERE 
			  	f.filter_group_id='".(int)$filter_group_id."'";
		$query = $this->db->query($q);
		return $query->row;
	}
	
	/**
	 * create description
	 **/
	public function create_description($field_data){
		$description = "";
		foreach($field_data as $key=>$val){
			$description.= $key." : ".$val.". ";
		}
		return $description;
	}
	
	public function toSlug ($string) {
        $string = strtolower($string);
        // Strip any unwanted characters
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        // Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        // Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);

        return $string;
	}
	
	
	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}
	
	public function readImages($directory, $sku=''){
		if (is_dir($directory) && !empty($sku)) { 
			$images = scandir($directory);
		}else{
			return 0;
		}
		 //echo "<pre>"; print_r($images);die;
		$image_files_paths = array();

		if (isset($images)) {
			if (in_array($sku, $images)) {
				$dir = $directory . '/'.$sku;
				if (is_dir($dir)) {
					foreach (scandir($dir) as $key => $value) {
						if (in_array($value, array(".", "..", ".DS_Store", "Thumbs.db"))) {
							continue;
						}
						else{
							$image_files_paths[] = $dir.'/'.$value;
						}
					 } 
				}
			return $image_files_paths;
			}
			else{
				foreach ($images as $key => $value) {
					if (substr_compare($value, $sku, 0, strlen($sku)) == 0) {
						$image_files_paths[] = $directory.'/'.$value;
					}
				}
			return $image_files_paths;
			}
		}
		else
			return 0;

	}
}
