<?php

/**
 * Main Class for getting all Product Related Info.
 * Majority of functions here will be static.
 * @Author Rahul, 2018
 */
class Product {


     /**
     * Method to get order product image (Multi-Color SKU - Change)
     * @params:
     * $product_id - Product ID (int, required)
     * $order_product_id - order_product_id (not required)
     * @return: order product option_image and product default_image
     * @author Rahul
     */
    public static function getOrderProductOptionImage($db,$product_id, $order_product_id=NULL) {
        // Getting last history for this suborder
        $result = array();

        if(!empty($order_product_id)){
                $sql = "SELECT oop.order_product_id,p.product_id, p.image as default_image, ooo.product_option_value_id, pov.option_image FROM ".    DB_PREFIX . "order_product oop
                    INNER JOIN ".    DB_PREFIX . "product p ON p.product_id=oop.product_id
                    LEFT JOIN ".    DB_PREFIX . "order_option as ooo ON ooo.order_product_id=oop.order_product_id
                    LEFT JOIN ".    DB_PREFIX . "product_option_value as pov ON pov.product_option_value_id=ooo.product_option_value_id WHERE oop.order_product_id ='" . (int) $order_product_id . "' LIMIT 1";

        }
            else{
                   $sql = "SELECT p.product_id,  p.image as default_image FROM ".DB_PREFIX."product p 
                        WHERE p.product_id='" . (int) $product_id . "'";
            }
        $query = $db->query($sql);
         $resultImage='';
        if($query->num_rows) {
           $result = $query->row;
         if(isset($result['product_id'])){
                if(!empty($result['option_image'])){
                    $resultImage=$result['option_image'];  
                }
                else if(!empty($result['default_image'])){
                    $resultImage=$result['default_image'];
                }
            }
        }
        return $resultImage;
    }
// close getOrderProductOptionImage function

  /**
   * Method to get order product image (Multi-Color SKU - Change)
   * @params:
   * $order_product_id - order_product_id (array, required)
   * @return: order product option_image and product default_image
   * @author: Rahul
   *           AND Updated By: Nishu 16th June 2018
  */
  public static function getOrderProductOptionImages($db, $order_product_id_array, $is_op_id = 1) {
    $resultImages = array();

    // Need to sanitize the input string, which is supposed to be a comma separated string of "int" order_product_id_array
    // So, we explode back to array. array_map to convert them to integers (to prevent SQL injection)
    // Afterwards, we array_filter it out to remove invalid values. Then array_unique to remove duplicates

    $order_product_id_array = array_unique(array_filter(array_map('intval', $order_product_id_array), function($v) {return $v > 0;}));

    if(!empty($order_product_id_array)){

      $order_product_id_string = implode(', ',$order_product_id_array);
        if($is_op_id == 1){
           //Getting Images for OrderProductIds
           $result = array();
                   $sql = "
                       SELECT
                           oop.order_product_id,
                           p.product_id,
                           p.image as default_image,
                           ooo.product_option_value_id,
                           pov.option_image
                       FROM ".    DB_PREFIX . "order_product oop
                       INNER JOIN ".    DB_PREFIX . "product p ON p.product_id=oop.product_id
                       LEFT JOIN ".    DB_PREFIX . "order_option as ooo ON ooo.order_product_id=oop.order_product_id
                       LEFT JOIN ".    DB_PREFIX . "product_option_value as pov ON pov.product_option_value_id=ooo.product_option_value_id
                       WHERE oop.order_product_id IN (" . $db->escape($order_product_id_string) . ")";
           $query = $db->query($sql);
      }else{
          //Get Images for ProductIds
          $sql = "SELECT 
                      p.product_id,  p.image as default_image 
                  FROM ".DB_PREFIX."product p
                  WHERE p.product_id IN (" . $db->escape($order_product_id_string) . ")"
                  ;
           $query = $db->query($sql);
      }
       
       //Initialize Empty Array
       $resultImages=array();
       //Check for Query returned resultSet or not
       if($query->num_rows > 0) {
          $results = $query->rows;
          if($is_op_id == 1){ //Section For OrderProduct Wise images
               foreach($results as $key=>$result){
                   if(!empty($result['option_image'])){
                       $resultImages[$result['order_product_id']]=$result['option_image'];  
                   } else if(!empty($result['default_image'])){
                       $resultImages[$result['order_product_id']]=$result['default_image'];
                   } else {
                        //No Image is available, Default Image returning
                        $resultImages[$result['order_product_id']]="placeholder.png"; 
                   }
               }

           }else{ //Section For Product Wise images
               foreach($results as $key=>$result){
                   if(!empty($result['default_image'])){
                       $resultImages[$result['product_id']] = $result['default_image'];
                   } else {
                        //No Image is available, Default Image returning
                        $resultImages[$result['product_id']] = "placeholder.png"; 
                    }
               }
           }          
       }

       //Get All OrderProductIds/ProductIds whose images are available
       $available_images_for_opids = array_keys($resultImages);

       //Get OrderProductIds, whose image(s) are not in array $resultImages
       $remaining_opids = array_diff($order_product_id_array, $available_images_for_opids);
       
       //Loop Pending opIds, for images, Assign them default placeholder Image
       foreach ($remaining_opids as $id) {
          $resultImages[$id] = "placeholder.png"; 
       }

    }

    return $resultImages; //Return Images as response
  }
// close getOrderProductOptionImages function
   
   
   /**
     * Method to update the quantity of combo product on basis of associate products quantity
     * @param : associate product_id
     * @return : true or false
     * @author: Devendra, June 2018
     * Description: Function moved by Nilesh to use this anywhere in code without loading model
     */
    public static function updateComboProductQuantityUsingAssociate($db, $associate_product_id) {
        // get combo products
        $sql = "SELECT product_id FROM " . DB_PREFIX . "product_to_associate 
						WHERE associate_product_id='" . (int) $associate_product_id . "'";
        $result = $db->query($sql);
        if ($result->num_rows < 1) {
            return true;
        }

        // update all combo product's quantity
        foreach ($result->rows as $product) {
            self::updateComboProductQuantity($db, $product['product_id']);
        }
        return true;
    }

// close updateComboProductQuantityUsingAssociate function	
    /**
     * Method to update the quantity of combo product on basis of associate products quantity
     * @param : combo product_id
     * @return : true or false
     * @author: Devendra, June 2018
     * Description: Function moved by Nilesh to use this anywhere in code without loading model
     */
    public static function updateComboProductQuantity($db, $combo_product_id) {
        // get combo products
        $sql = "SELECT p.quantity FROM " . DB_PREFIX . "product_to_associate ps
					JOIN " . DB_PREFIX . "product p ON ps.associate_product_id = p.product_id 
						WHERE ps.product_id='" . (int) $combo_product_id . "'";
        $result = $db->query($sql);
        if ($result->num_rows < 1) {
            return true;
        }
        // get the array of associate product quantities.
        $associate_product_quantities = array_column($result->rows, 'quantity');

        // get the lowest quantity
        $new_quantity = min($associate_product_quantities);

        $sql = "UPDATE " . DB_PREFIX . "product SET quantity='" . (int) $new_quantity . "' 
						WHERE product_id ='" . (int)$combo_product_id . "'";

        return $db->query($sql);
    }

    /**
     * Method to get quantity of product with option's quantity
     * @param : String product_ids single or multiple comma sperated
     * @return : true or false
     * @author: Nishu, Nov 2018
     */
    public static function getProductQtyWithOptionQty($db, $product_ids) {
        $data = array();
        $sql = "
                SELECT 
                  p.product_id,
                  p.piece_in_set,
                  p.quantity AS product_total_qty,
                  COALESCE(pov.quantity, 0) AS option_qty,
                  pov.product_option_value_id
                FROM 
                  " . DB_PREFIX . "product AS p
                LEFT JOIN 
                  " . DB_PREFIX . "product_option_value pov ON pov.product_id = p.product_id 
                WHERE 
                  p.product_id IN (" . $db->escape($product_ids). ") ";

        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            
          $data = $result->rows;
        }
        return $data;
    }


    /**
     * @info: Public method to get filter list with filter_group_id
     * @param: $filter_group_id
     * @return array
     * @author: Nishu
    */
    public static function getFiltersByGroupId($db, int $filter_group_id){
      $data = array();
      $sql = "
              SELECT
                fd.filter_id,
                fd.name
              FROM 
                ".DB_PREFIX."filter_description AS fd
              INNER JOIN 
                ".DB_PREFIX."filter AS f ON f.filter_id = fd.filter_id
              WHERE
                fd.language_id        = 1
                AND f.filter_group_id = ". (int)$filter_group_id ."
             ";
      $qry = $db->query($sql);
      if($qry->num_rows > 0){
        $data = $qry->rows;
      }
      
      return $data;
    }


// close updateComboProductQuantity function
}

// close Product class
?>
