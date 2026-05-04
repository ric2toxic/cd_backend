<?php
 /**
  * Author : Divya Porwal
  * Checks for images corresponding to SKU
  * @inputs : array(skus)
  * @output : array(sku->images)
  * */
	class CheckImage {
		
		private $_db;
		private $_import_csv_id;
		private $_seller_id;
		private $_seller_code;
		
		public function __construct($db,$id_seller) {
			
			$this->_db = $db;
			$this->_seller_id = $id_seller;
		}
		
		public function pickImagesSku($sku_list = array()) {
			
			$sql = "SELECT nickname FROM `" . DB_PREFIX . "ms_seller` AS s WHERE s.seller_id = ".(int)($this->_seller_id)."" ;
		
			$seller_code = $this->_db->query($sql)->row['nickname'];
			
			$directory_source =  DIR_IMAGE.'catalog/'.($seller_code);
			$folder_name =  glob($directory_source."/". "*", GLOB_BRACE);
			$file_names = glob($directory_source."/"."*jpg", GLOB_BRACE);
			$files = array();
			$files_in_folder = array();
			$sku_images = array();
	
			//list of folders given in the directory
		/*	foreach ($folder_name as $folder) {
				//search for folders with sku name
				if(is_dir($folder) && in_array(strtolower(basename($folder)),array_map('strtolower',$sku_list))) {	
					$files = glob($folder."/"."*jpg", GLOB_BRACE);
					$key = array_search(strtolower(basename($folder)),array_map('strtolower',$sku_list));
					$sku_images[$sku_list[$key]] = $files;
					
				} elseif(is_dir($folder) && !in_array(basename($folder),$sku_list)) {
					// search for files with sku name inside folders named randomly
					
					$directory = new RecursiveDirectoryIterator($folder);
					$iterator = new RecursiveIteratorIterator($directory);
					foreach($iterator as $a) {
						
						if(!$a->isDir()) {
							
							$key = array_search(strtolower(basename($a, ".jpg")),array_map('strtolower',$sku_list));
							if(in_array($sku_list[$key],array_keys($sku_images))) {
								array_push($sku_images[$sku_list[$key]],$a->getPathname());
							} else {
								$sku_images[$sku_list[$key]][] = $a->getPathname();
							}
							array_push($files_in_folder,$a->getPathname());
						}
					}	
				}

			}*/
			

			$file_image = array();
			/*foreach($file_names as $file) {
				
				if(is_file($file) && in_array(strtolower(basename($file, ".jpg")),array_map('strtolower',$sku_list))) {
					$key = array_search(strtolower(basename($file, ".jpg")),array_map('strtolower',$sku_list));
					if(in_array($sku_list[$key],array_keys($sku_images))) {
						array_push($sku_images[$sku_list[$key]],$file);
					} else {
						$sku_images[$sku_list[$key]][] = $file;
					}
					array_push($file_image,$file);
				}
			}*/
			
			//finding files present inside seller folder

			$file_nameq = array();
			foreach($file_names as $file) {
				array_push($file_nameq,basename(str_replace('/var/www/html/wholesalebox/image/catalog/'.$seller_code.'/', '', $file),".jpg"));
			}
		
			foreach($sku_list as $key=>$value) {
				if(!empty($sku_images[$value])) {
					$count = count($sku_images[$value]);
				} else {
					$count = 0;
				}
				foreach($file_nameq as $file) {
					$string = strtolower($value);
					$a = $string.".*";
						
					if(preg_match("/$a/", strtolower($file))) {
						if(!empty($sku_images[$value])) {
							$sku_images[$value][$count] = '/var/www/html/wholesalebox/image/catalog/'.$seller_code.'/'.$file.".jpg";
							$count++;
						} else {
							$sku_images[$value][$count] = '/var/www/html/wholesalebox/image/catalog/'.$seller_code.'/'.$file.".jpg";
							$count++;
						}
					} 
				}	
				}
		
			$images_send = array();
			foreach($sku_images as $key=>$value) {
				$images_send[$key] = array();
				array_push($images_send[$key],array_map(function($val){ return HTTPS_CATALOG.preg_replace('/\/var\/www\/html\/wholesalebox\//',"",$val); },$value));
			}
			
			//echo "<pre>";
		   
			return $images_send;
			
		}
		
	}

?>
