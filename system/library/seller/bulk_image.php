<?php

	class BulkImage {
		
		private $_db;
		private $_seller_id;
		private $_form_data;
		
		public function __construct($seller_id='', $form_data='', $db) {
			$this->_seller_id = $seller_id;
			$this->_form_data = $form_data;
			$this->_db = $db;
		}
		
		public function autocompleteSearching($request) {
			
			$query = '';
			if(!empty($request)) {
				$sql = "SELECT ms.nickname, ms.seller_id
					FROM ".DB_PREFIX. "ms_seller ms
					WHERE ms.nickname LIKE '".$request."%' AND ms.seller_status = 1
					ORDER BY ms.nickname ASC  ";
				$query = $this->_db->query($sql);
			}
			
			if(empty($query->rows) && !empty($request)) {
					
				$sql = "SELECT ms.company, ms.seller_id
					FROM ".DB_PREFIX. "ms_seller ms
					WHERE ms.company LIKE '".$request."%' AND ms.seller_status = 1
					ORDER BY ms.company ASC  ";
				$query = $this->_db->query($sql);	
				} 
				
			return $query?$query->rows:$query;
		}
		
		public function updateImageLog($serialize_array = array(),$old_name,$new_name,$old_size,$new_size, $status) {
			
			$this->_db->query("INSERT INTO " . DB_PREFIX . "image_upload
					                  SET seller_id = '" . (int)$this->_seller_id . "',
					                      user = '" . $serialize_array  . "',
					                      date_added = NOW(),
					                      original_file = '" . $old_name . "',
					                      original_size = '". $old_size. "',
					                      new_file = '". $new_name. "',
					                      new_size = '". $new_size. "',
					                      status = '". $status ."'
					                      ");
			
				
		}
		
		public function processImage($image,$status,$user_id,$user_name,$old_size, $file_new = '') {
			
			//storing variable to check if size has been changed and the image has been uploaded
			$change_size = 0;
			$new_size = 0;
			if(empty($file_new)) {
				
				$new_file = pathinfo($image)['dirname'].'/'.pathinfo($image)['filename'].'_tmp.'.pathinfo($image)['extension'];
				copy($image,$new_file);
				chmod($new_file,0777);

				system("mogrify -geometry x1200 $new_file"); 
				system("mogrify -quality x60 $new_file"); 
				$new_size = filesize($new_file)/1024;

				if(filesize($image)/1024 > filesize($new_file)/1024) {
					rename("$new_file", "$image");
					$change_size = 1;
				} else {
					unlink($new_file);
				}
				$this->updateImageLog(serialize(array($user_id,$user_name)),$image,'',$old_size,$new_size,$status);

			} else {
				
				$old_file = $image;
				$image = $file_new;
				$new_file = pathinfo($image)['dirname'].'/'.pathinfo($image)['filename'].'_tmp.'.pathinfo($image)['extension'];
				
				copy($image,$new_file);
				chmod($new_file,0777);
				system("mogrify -geometry x1200 $new_file"); 
				system("mogrify -quality x60 $new_file"); 
				chmod($new_file,0777);

				$new_size = filesize($new_file)/1024;

				if($old_size > $new_size) {
					rename("$new_file", "$file_new");
					$change_size = 1;

				} else {
					unlink($new_file);
				}
				
				$this->updateImageLog(serialize(array($user_id,$user_name)),$old_file,$file_new,$old_size,$new_size,$status);
				
			} 	
			
			return array($change_size, $new_size);
		}
		
		
		public function veriImages($image,$supported_image,$renamed_image = '') {
			
			$return = array();
			$return2 = array();
			
			//print(pathinfo($image)['dirname']);

			$relative_path = explode('/tmp-images-for-upload/', $image)[1];

			$ext = pathinfo($image, PATHINFO_EXTENSION);
			if($ext == "jpg" || "jpeg" || "JPG" || "JPEG") {
				$name_image = basename($image,".".$ext);
				$name = $name_image.".".strtolower($ext);
			} 
			
			// Using strtolower to overcome case sensitive
			if (!in_array(strtolower($ext), $supported_image)) {
			
			//storing errors which do not qualify the extension format criteria
				$msg_error = "Incorrect Format for Images";
				if(file_exists($image)) {
					unlink($image);
				} else if(file_exists($renamed_image)) {
					unlink($renamed_image);

				} 
				array_push($return,$relative_path);
				array_push($return2,$msg_error);
				
			} else if(!preg_match("/^[-a-zA-Z_0-9]*$/",trim($name_image))) {
				$msg_error2 =  "invalid Name.Only alphabets,Numbers,underscore(_) and dashes(-) are allowed.";
				if(file_exists($image)) {
					unlink($image);
				} else if(file_exists($renamed_image)) {
					unlink($renamed_image);

				} 
				
				array_push($return,$relative_path);
				array_push($return2,$msg_error2);
			}
		
			return array_merge($return,$return2);
		}
		
		public function readZipFile($user_id,$user_name) {
			
			//storing msgs with errors to display
			$msgs_error = array();
			//storing images with errors to display
			$images_with_error = array();
			
			$oldmask = umask(0);

			//checking for data if it is set
			if(!empty($this->_seller_id)) {
				$sql = "SELECT nickname 
				        FROM " . DB_PREFIX . "ms_seller 
				        WHERE seller_id = '" . (int)$this->_seller_id . "'";
				$query = $this->_db->query($sql);
				if ( !$query->num_rows ) {
					return array('error' => array("Invalid Seller Id",1));
				}
				$seller_code = trim(strtoupper($query->row['nickname']));
				
				$directory =  DIR_IMAGE.'catalog/'.$seller_code;
				if (!is_dir($directory)) {
					//creating directory if not exists by the name of seller_id
					mkdir($directory);
					chmod($directory, 0777);
				}	
			} else {
				return array('error' => array("No Seller Selected ! Please select Seller to Upload",1));
			}

			//carrying ouyt zip/unzip procuedures
			if($this->_form_data["myzip"]["name"]) {
				
				$filename = $this->_form_data["myzip"]["name"]; //name of file
				
				$source = $this->_form_data["myzip"]["tmp_name"]; //tmp name of file
				$type = $this->_form_data["myzip"]["type"]; //type of file
				$name = explode(".", $filename); //finding extension of file
				if(is_dir($directory."/".basename($filename,".zip"))) {
					$message = "File already already exists";
					return array('error' => array($message,1));			
				}
				$size = $this->_form_data['myzip']['size']; //finding size of file
				if((int)($size)/1000000 > 50) {
					
					$message = "Size Limit Exceeded";
					return array('error' => array($message,1));
								
				}
			
				//converting to lower case and checking
				$continue = (strtolower($name[1]) == 'zip');
				
				if(!$continue) {
					$message = "Incorrect File Format! Please try again.";
					return array('error' => array($message,1));

				} else {
					$targetzip = $directory . '/' . $filename;
					
					//moving file to target
					if(move_uploaded_file($source, $targetzip)) {	
						$zip = new ZipArchive();
						$x = $zip->open($targetzip);  // open the zip file to extract
						
						if ($x === true) {
							if(!is_dir($directory . '/tmp-images-for-upload/')) {
								mkdir($directory . '/tmp-images-for-upload/');
								chmod($directory . '/tmp-images-for-upload/', 0777);
							}
							$zip->extractTo($directory . '/tmp-images-for-upload/'); // place in the directory with same name  
							$zip->close();
							unlink($targetzip); // Deleting the zip file
						}
					
						$message = "Your .zip file was uploaded and unpacked.";
						$supported_image = array(
											'jpg',
											'jpeg',
											'JPEG',
											'JPG',
											);

						$images_with_error = array();
						$msgs_error = array();
						$images_size_change = array();
						$earlier_size = array();
						$new_size = array();
						$images = glob($directory . '/tmp-images-for-upload/' . explode(".",$filename)[0] . '/*', GLOB_BRACE);
						
						foreach($images as $image) {
							
							
							
							if(is_dir($image) ) {
								$image_folder_name = $image;
								$image = glob($image.'/*', GLOB_BRACE);
								
								foreach($image as $sub_image) {
									if(is_file($sub_image)) {
										
										$old_file = $sub_image;
										$old_size = filesize($sub_image)/1024;
										
										//renaming file to folder_name'_'image_name
										rename($sub_image,$image_folder_name.'_'.basename($sub_image));
										$result = $this->veriImages($old_file,$supported_image,$image_folder_name.'_'.basename($sub_image));
										
										if(!empty($result[0]) && !empty($result[1])) {
											$status = "rejected";
											array_push($images_with_error,$result[0]);
											array_push($msgs_error,$result[1]);
											$this->updateImageLog(serialize(array($user_id,$user_name)),$sub_image,'',$old_size,0,$status);

											
										} else {
											
											$status = "uploaded";
											//calling method to copy old image to new image and then compare sizes of both
											//the image with lesser size will be saved, other will be deleted
											
											//saving images whose size has change
											$return = $this->processImage($sub_image,$status,$user_id, $user_name,$old_size,$image_folder_name.'_'.basename($sub_image));
											if($return[0]) {
												
												$relative_path = explode('/tmp-images-for-upload/', $sub_image)[1];

												array_push($images_size_change,$relative_path);
												array_push($earlier_size,$old_size);
												array_push($new_size,$return[1]);
											}
										}
									} else {
										$sub_sub_images = glob($sub_image.'/*', GLOB_BRACE);

										foreach($sub_sub_images as $sub_sub_image) {
											
											$status = "rejected";
											$old_size = filesize($sub_sub_image)/1024;
											$this->updateImageLog(serialize(array($user_id,$user_name)),$sub_sub_image,'',$old_size,0,$status);
											unlink($sub_sub_image);
										}
										
										rmdir($sub_image);

									}
									
								}
								rmdir($image_folder_name);

							} else if(is_file($image)) {
								$old_name = $image;
								$old_size = filesize($image)/1024;
								
								if(!empty($this->veriImages($image,$supported_image,'')[0]) && !empty($this->veriImages($image,$supported_image,'')[1])) {
									
									$status = "rejected";
									array_push($images_with_error,$this->veriImages($image,$supported_image,'')[0]);
									array_push($msgs_error,$this->veriImages($image,$supported_image,'')[1]);
									$this->updateImageLog(serialize(array($user_id,$user_name)),$image,'',$old_size,0,$status);

								} else {
									
									$status = "uploaded";
									
									//calling method to copy old image to new image and then compare sizes of both
									//the image with lesser size will be saved, other will be deleted
																		
									//saving images whose size has change
									$relative_path = explode('/tmp-images-for-upload/', $image)[1];

									$return = $this->processImage($image,$status,$user_id, $user_name,$old_size,'');
									if($return[0]) {
										array_push($images_size_change,$relative_path);
										array_push($earlier_size,$old_size);
										array_push($new_size,$return[1]);
									}
								}
							}
						}
						
						
						//moving the contents of tmp directory
						rename($directory . '/tmp-images-for-upload/' . explode(".",$filename)[0] . '/', $directory .'/'. explode(".",$filename)[0] . '/');
						chmod( $directory .'/'. explode(".",$filename)[0] . '/', 0777);
						$var = $directory .'/'. explode(".",$filename)[0];
						exec("chmod -R 0777  $var");
						
						if (is_dir($directory.'/tmp-images-for-upload/')) {
							rmdir($directory.'/tmp-images-for-upload/');
						}
						
						chdir($directory.'/'. explode(".",$filename)[0]);
						
						$di = new RecursiveDirectoryIterator($directory.'/'. explode(".",$filename)[0]);
						$it = new RecursiveIteratorIterator($di);
						
						foreach($it as $file) {
						
							//checking for jpeg or jpg format
							if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == "jpg" && is_file($file)) {
								$ext = pathinfo($file, PATHINFO_EXTENSION);
								copy($file,$directory.'/'.basename(basename($file), $ext).'jpg');
								unlink($file);

							} else if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) == "jpeg" && is_file($file)) {
								
								$ext = pathinfo($file, PATHINFO_EXTENSION);

								copy($file,$directory.'/'.basename(basename($file), $ext).'jpg');
								unlink($file);
							}
						}
					
												
						//forcefully removing directory and keeping all images under seller_code directory
						rmdir($var);
						exec("chmod -R 0777  $var");   
						umask($oldmask);
						$message = "Files were successfully uploaded.Any errors are reported below";
						$flag = 0;
						} else {    
							$message = "There was a problem with the upload. Please try again.";
							$flag = 1;
						}
						return array( 
									'error' => array($message,$flag), 
									'image_error' => array_filter($images_with_error),
									'msg_error' => array_filter($msgs_error),			
									'images_size_change' => array_filter($images_size_change),
									'earlier_size' => array_filter($earlier_size),		
									'new_size' => array_filter($new_size),
									);
					}
				}	
			}
	}

?>
