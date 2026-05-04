<?php

/**
* 
*/
class ModelRestapiWsbcartservice extends Model
{
	
    public function add($extra){ 
		$record_update = $extra['record_update'];
		$cart_session_id = 0;
		$parent_cart_id = 0;
		$user_agent = '';
		$ip = '';
		$app_version = '';
		
		$customer_id = $extra['user_id'];
		$cart = $extra['cart_data'];
		$cart_history = $extra['cart_history'];
		
		if(isset($extra['cart_session_id'])){
			$cart_session_id = $extra['cart_session_id'];
		}
		if(isset($extra['parent_cart_id'])){
			$parent_cart_id = $extra['parent_cart_id'];
		}
		if(isset($extra['user_agent'])){
			$user_agent = $extra['user_agent'];
		}
		if(isset($extra['ip'])){
			$ip = $extra['ip'];
		}
		if(isset($extra['version'])){
			$app_version = $extra['version'];
		}
		
		if($record_update == 1){
			$customer_id = $extra['user_id'];
			$cart_id = $extra['cart_id'];

			if($this->db->query("UPDATE ".DB_PREFIX."customer_cart 
								SET 
									`customer_id` 		= ".(int)$customer_id.",
									`parent_cart_id` 	= '".$this->db->escape($parent_cart_id)."',
									`cart_session_id` 	= '".$this->db->escape($cart_session_id)."',
									`cart_data` 		= '".$this->db->escape($cart)."',
									`cart_history` 		= '".$this->db->escape($cart_history)."',
									`user_agent` 		= '".$this->db->escape($user_agent)."',
									`ip` 				= '".$this->db->escape($ip)."',
									`app_version` 		= '".$this->db->escape($app_version)."',
									`last_cart_modified_from` = '".$this->db->escape($extra['last_cart_modified_from'])."',
					                `cart_modified_once_from` = '".$this->db->escape($extra['cart_modified_once_from'])."',
									`date_modified` = NOW()
								WHERE 
									`oc_customer_cart`.`id` = ".(int)$cart_id
								)
			) {
				return true;
			}else{
				return false;
			}
		}else{	
		
			if($this->db->query("INSERT IGNORE INTO ".DB_PREFIX."customer_cart 
								SET 
									`customer_id` 		= ".(int)$customer_id.",
									`parent_cart_id` 	= '".$this->db->escape($parent_cart_id)."',
									`cart_session_id` 	= '".$this->db->escape($cart_session_id)."',
									`cart_data` 		= '".$this->db->escape($cart)."',
									`cart_history` 		= '".$this->db->escape($cart_history)."',
									`user_agent` 		= '".$this->db->escape($user_agent)."',
									`ip` 				= '".$this->db->escape($ip)."',
									`app_version`		= '".$this->db->escape($app_version)."',
									`last_cart_modified_from`='".$this->db->escape($extra['last_cart_modified_from'])."',
					                `cart_modified_once_from`='".$this->db->escape($extra['cart_modified_once_from'])."'")
			) {
				return true;
			}else{
				return false;
			}
		}
	}

    /** Function is being used to update multiple items quantity
     * @param $data
     * @param $to_update
     * @return bool
     */
	public function bulk_update($data, $to_update){

        if (isset($data['customer_cart_data'][0]['cart_data'])) {
            $cart = unserialize($data['customer_cart_data'][0]['cart_data']);

            //echo '<pre>';print_r($cart); echo '</pre>----------';
            if (count($to_update) > 0) {

                foreach($to_update as $arr_keys_to_update) {
                    $qty = $arr_keys_to_update['quantity'];
                    $key = $arr_keys_to_update['key'];

                    if (isset($cart[$key])) {
                        if ((int)$qty > 0) {
                            $cart[$key] = (int)$qty;
                        } else {
                            unset($cart[$key]);
                        }
                    }

                }
            }

            $cart_id = $data['customer_cart_data'][0]['id'];
            $cart = serialize($cart);

            if($this->db->query(
                "UPDATE ".DB_PREFIX."customer_cart 
				SET 
					`cart_data`='".$this->db->escape($cart)."',
					`last_cart_modified_from`='".$this->db->escape($data['last_cart_modified_from'])."',
	                `cart_modified_once_from`='".$this->db->escape($data['cart_modified_once_from'])."',
					`date_modified` = NOW()
				WHERE `oc_customer_cart`.`id` = ".(int)$cart_id
            )){
                return true;
            }else{
                return false;
            }

        }
    }
	
	public function update($data){
		if(isset($data['customer_cart_data'][0]['cart_data'])){
			$cart = unserialize($data['customer_cart_data'][0]['cart_data']);
			$qty = $data['quantity'];
			$key = $data['key'];
			$cart_id = $data['customer_cart_data'][0]['id'];
            if (isset($cart[$key])) {
                if ((int)$qty > 0) {
                    $cart[$key] = (int)$qty;
                }else {
                    unset($cart[$key]);
                }
            }
			$cart = serialize($cart);

			if($this->db->query("UPDATE ".DB_PREFIX."customer_cart 
								 SET 
									`cart_data`='".$this->db->escape($cart)."',
									`last_cart_modified_from`='".$this->db->escape($data['last_cart_modified_from'])."',
					                `cart_modified_once_from`='".$this->db->escape($data['cart_modified_once_from'])."',
									`date_modified` = NOW()
								 WHERE 
								 	`oc_customer_cart`.`id` = ".(int)$cart_id )
			){
				return true;
			}else{
				return false;
			}
		}
		
	}
	
	public function updateUserId($data){
		$conditions = 'customer_id = '. (int)($data['user_id'] ?? 0) .' order by date_modified desc limit 1';
		$cartData = $this->get_customer_cart($conditions);
		if(isset($cartData[0]['cart_data']) && !empty($cartData[0]['cart_data'])){
			$pCartData  = unserialize($cartData[0]['cart_data']);
			$cond = 'cart_session_id = "'. $this->db->escape($data['last_session_id']) .'" order by date_modified desc limit 1';	
			$cart_data = $this->get_customer_cart($cond);
			if(isset($cart_data[0]['cart_data']) && !empty($cart_data[0]['cart_data'])){
				$cCartData = unserialize($cart_data[0]['cart_data']); 	
				$qty = 0;
				foreach($pCartData as $key => $qty){
					if ((int)$qty && ((int)$qty > 0)) {
						if (!isset($cCartData[$key])) {
							$cCartData[$key] = (int)$qty;
						} else {
							$cCartData[$key] += (int)$qty;
						}
					}
				}
				if($cartData['0']['parent_cart_id'] == 0){
					$parent_cart_id = $cartData['0']['id'];
				}else{
					$parent_cart_id = $cartData['0']['parent_cart_id'];
				}
				$this->db->query("UPDATE ".DB_PREFIX."customer_cart 
								 SET `parent_cart_id`= '".$this->db->escape($parent_cart_id)."'
								 WHERE `id` = '".(int)$cart_data[0]['id']."'"
								);
				
			}
		}
				
		if(isset($cCartData) && !empty($cCartData)){
			$this->db->query("UPDATE ".DB_PREFIX."customer_cart 
							SET `customer_id`=0
							WHERE `customer_id` = '".(int)$data['user_id']."'"
							);
			if($this->db->query("UPDATE ".DB_PREFIX."customer_cart 
								SET `customer_id`=".(int)$data['user_id'].",
									`cart_data`= '".$this->db->escape(serialize($cCartData))."',
									`cart_session_id`= '0'
								WHERE 
									`cart_session_id` = '".$this->db->escape($data['last_session_id'])."'"
				)){
						return true;
			}else{
				return false;
			}
		}else{
			if($this->db->query("UPDATE ".DB_PREFIX."customer_cart 
								SET `customer_id`=".(int)$data['user_id'].",
								`cart_session_id`= '0'
								WHERE `cart_session_id` = '".$this->db->escape($data['last_session_id'])."'"
						)
			){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function get_customer_cart( string $conditions ){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE ".$conditions);
		return $query->rows;
	}
	
	public function get_cart_total( int $user_id ){
		$total_cart = 0;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id='".(int)$user_id."' OR cart_session_id='".$this->db->escape($user_id)."'");
		if(isset($query->row['cart_data']) && !empty($query->row['cart_data'])){
			$cart_data = unserialize($query->row['cart_data']);
			foreach($cart_data as $key => $qnt){
				$total_cart += $qnt;
			}
		}
		return $total_cart; 
	}
	public function clearCart($extra){
		if(isset($extra['user_id'])){
			$user_id = $extra['user_id'];
		}
		if(isset($extra['cart_session_id'])){
			$cart_session_id = $extra['cart_session_id'];
		}
		if(isset($extra['order_number'])){
			$order_number = $extra['order_number'];
		}else{
			$order_number = 0;
		}
		if(isset($extra['cart_type'])){
			$cart_type = $extra['cart_type'];
		}else{
			$cart_type = '';
		}
		if(isset($user_id)){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE customer_id=".(int)$user_id);
		}else{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE cart_session_id='".$this->db->escape($cart_session_id)."'");
		}
		
		if(isset($query->row['parent_cart_id']) && $query->row['parent_cart_id'] != 0){
			$sub_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_cart WHERE parent_cart_id=".(int)$query->row['parent_cart_id']." OR id = ".(int)$query->row['parent_cart_id']);
		}
		if(!empty($sub_query)){
			$cart_history = $sub_query->rows;
		}else{
			$cart_history = $query->rows;
		}
		if(!empty($cart_history)){
			$cart_history = serialize($cart_history);
			
			
			if($this->db->query(
					"INSERT INTO ".DB_PREFIX."wsb_cart_history 
					SET `customer_id` 		= '".(int)$user_id."',
						`order_number` 		= '".$this->db->escape($order_number)."',
						`cart_type` 		= '".$this->db->escape($cart_type)."',
						`cart_history` 		= '".$this->db->escape($cart_history)."',
						`last_cart_modified_from` ='".$this->db->escape($extra['last_cart_modified_from'])."',
						`date_added` = NOW()"
					)){
						if(isset($user_id)){
							$this->db->query("DELETE FROM " . DB_PREFIX . "customer_cart WHERE customer_id=".$user_id);
						}else{
							$this->db->query("DELETE FROM " . DB_PREFIX . "customer_cart WHERE cart_session_id='".$cart_session_id."'");
						}
						$this->db->query("DELETE FROM " . DB_PREFIX . "customer_cart WHERE parent_cart_id=".$query->row['parent_cart_id']." OR id = ".$query->row['parent_cart_id']);
						return true;
				}else{
					return false;
				}
		}else{
			return false;
		}
	}
	
	public function remove_cart_data($extra){
		$cart_id = $extra['cart_id'];
		$cart = $extra['cart_data'];
        $product_comments = $extra['product_comments'];

        //remove coupon and other if cart becomes empty
        $others = '';
        if (empty(unserialize($cart))) {
            $others = ", coupon='', franchise_id=0, franchise_margin=0 ";
        }

		if($this->db->query(
							"UPDATE ".DB_PREFIX."customer_cart 
							SET 
								`cart_data` 		='".$this->db->escape($cart)."',
								`product_comments` 	='".$this->db->escape($product_comments)."',
								`last_cart_modified_from`='".$this->db->escape($extra['last_cart_modified_from'])."',
				                `cart_modified_once_from`='".$this->db->escape($extra['cart_modified_once_from'])."',
								`date_modified`  	= NOW() " . $others ."
							WHERE `oc_customer_cart`.`id` = ".(int)$cart_id
			)){
				return true;
			}else{
				return false;
			}
	}

    public function addComment($extra){
        $cart_id = $extra['cart_id'];
        $product_comments = $extra['product_comments'];
        if($this->db->query(
            "UPDATE ".DB_PREFIX."customer_cart 
				SET 
				`product_comments`='".$this->db->escape($product_comments)."',
				`date_modified` = NOW()
				WHERE `oc_customer_cart`.`id` = ".(int)$cart_id
        )){
            return true;
        }else{
            return false;
        }
    }
	
	public function UpdateOtp( int $user_id, string $otp ) { 
		if($this->db->query("UPDATE " . DB_PREFIX . "customer SET otp = '".$this->db->escape($otp)."' WHERE customer_id = '" . (int)$user_id . "'")){
			return 1;
		}else{
			return 0;
		}	
		
	}

	public function countProducts( int $user_id ){
      return  $this->cart->countProducts($user_id);
    }
	
	public function insertRequirement($data){
		if(isset($data['refrenece_link']) && !empty($data['refrenece_link'])){
			$refrenece_link = $data['refrenece_link'];
		}else{
			$refrenece_link = '';
		}
		if(isset($data['description']) && !empty($data['description'])){
			$description = $data['description'];
		}else{
			$description = '';
		}

		if(!empty($data)){
			$sql  = "INSERT INTO ".DB_PREFIX."customer_post_requirement_details SET ";
			$sql .= "category_name = '". $this->db->escape($data['category_name'])."',";
			$sql .= "quantity = ".(int) $data['quantity'].",";
			$sql .= "name = '".$this->db->escape($data['name'])."',";
			$sql .= "required_days = '".$this->db->escape($data['required_days'])."',";
			$sql .= "price_to = ". $this->db->escape($data['price_to']).",";
			$sql .= "price_from = ". $this->db->escape($data['price_from']).",";
			$sql .= "description = '". $this->db->escape($description)."',";
			$sql .= "refrenece_link = '". $this->db->escape($refrenece_link)."'";
			
			if($this->db->query($sql)){
				$id = $this->db->getLastId();
				if(!empty($_FILES)){
					$return_data['images_data'] = $this->uploadImages($id);

				}
				else
				{
					$return_data['images_data'] = '';
				}
				$return_data["success"] = 1;
				return $return_data;				
			}else{
				return 0;
			}
		}		
	}

	public function uploadImagesOnserver($post){
		$ch = curl_init();
		$target_url = 'https://cdnimages.net/fileupload.php?directory='.$post['directory'].'&filename='.$post['filename'];
		curl_setopt($ch, CURLOPT_URL,$target_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post['data']);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$result = curl_exec ($ch);
		
		$result_array = json_decode($result,true);
		if(isset($result_array['success'])){
			return 1;
		}else{
			return 0;
		}
	}

	public function getCategoryName( int $category_id ){
		$sql = "SELECT name from ".DB_PREFIX."category_description
				WHERE category_id = ".(int)$category_id."";
		$result = $this->db->query($sql);
		return $result->row;
	}

	public function uploadImages($id){
		$i = 0;
		foreach($_FILES['reference_image']['name'] as $values){
			
			$save['image_name']     = $values;
			$save['requirement_id'] = $id;
			$return_image_name[]['image_name'] = $values;
			$this->saveImages($save);
			$post['directory'] = 'post_your_requirement';
			
			$post['filename']  = basename(html_entity_decode($values, ENT_QUOTES, 'UTF-8')); 
			
			$file_name_with_full_path = $_FILES['reference_image']['tmp_name'][$i];
			
			if ($_FILES['reference_image']['error'][$i] != UPLOAD_ERR_OK) {
				continue;
			}

			if(is_uploaded_file($_FILES['reference_image']['tmp_name'][$i])){
				if (function_exists('curl_file_create')) { // php 5.5+
				$cFile = curl_file_create($file_name_with_full_path);
				} else { // 
					$cFile = '@' . realpath($file_name_with_full_path);
				}
			}

			$post['data'] = array('file'=> $cFile);
			if(!empty($post)){
				if($this->model_restapi_wsbcartservice->uploadImagesOnserver($post)){
				}else{
					$all_images_uploaded = 'false';
				}
			}
			$i++;
		}
		return $return_image_name;
	}

	public function saveImages($data){
		$sql = "INSERT INTO ".DB_PREFIX."customer_post_requirement_images 
				SET
					customer_post_requirement_id = ".(int)$data['requirement_id'].",
					image_name = '".$this->db->escape($data['image_name'])."'
				";
		$this->db->query($sql);	
	}

	public function getmailBody($data){
		if(!empty($data)){
			$subject = "Product Requirement query for".$data['category_name'];
			$body  = "Dear Prabhav,";
			$body .= "<br>";
			$body .= "<br>";
			$body .= "The Customer has requested following requirement ";
			$body .= "<br>";
			$body .= "<br>";
			$body .= "Customer Name: ".$data['firstname'];
			$body .= "<br>";
			$body .= " Customer Phone Number: ".$data['telephone'];
			$body .= "<br>";
			$body .= " Customr Email: ".$data['email'];
			$body .= "<br>";
			$body .= "<br>";
			$body .= "Category: ".$data['category_name'];
			$body .= "<br>";
			$body .= "<br>";
			$body .= "Quantity: ".$data['quantity'];
			$body .= "<br>";
			$body .= "<br>";
			if(!empty($data['required_days'])){
				$body .= " Required Days: ".$data['required_days'];
				$body .= "<br>";
			}
			if(!empty($data['price_from'] || $data['price_to'])){
				$body .= " Price Range: ".$data['price_from']." to ".$data['price_to'];
				$body .= "<br>";
			}
			$body .= "<br>";
			$body .= "<br>";
			$body .= "<br>";
			if(!empty($data['refrenece_link'])){
				$body .= "Refernce Link: <a href='".$data['refrenece_link']."'>Link</a>";
				$body .= "<br>";
				$body .= "<br>";
				$body .= "<br>";
			}
			if(!empty($data['description'])){
				$body .= "Customer message: ".$data['description'];
				$body .= "<br>";
				$body .= "<br>";
			}	
			if(!empty($data['image_name'])){
				$body .= "Customer has attached reference images. Please check it in the attachment of email.";
				$body .= '<table>';
				$body .= 	'<tbody>';
				foreach($data['image_name'] as $values){
					$target_url = "https://cdnimages.net/post_your_requirement/".$values['image_name'];
					$src_url    = "https://cdnimages.net/img/dw=250,dh=150,q=90/post_your_requirement/".$values['image_name'];
					$body .= 	  "<tr>";
					$body .= 	  	"<td><a href='".$target_url."' target='_blank'><img src='".$src_url."'></a></td>";
					$body .= 	  "</tr>";
				}
				$body .= '</tbody>';
				$body .= '</table>';
			}
		}

		return $body;
		
	}

}
