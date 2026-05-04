<?php
declare(strict_types=1);

class FranchiseProduct extends Franchise
{

  public function __construct(Controller $controller) {
    parent::__construct($controller);  
  }

  /**
   * Public method to copy product for franchise
   * @author: Nishu, Aug 2017
  */
  public function copyProduct(array $product, int $product_option_value_id){
    $this->load->model('catalog/product', 'admin');
    
    $product_id    = $product['product_id'];
    $franchise_id  = $product['franchise_id'];

    $copy_product_id = $this->registry->admin_model_catalog_product->copyProduct($product_id, $product, $product['quantity'], $product_option_value_id);

    $this->registry->admin_model_catalog_product->insertProductToFranchise($product['product_id'], $franchise_id, $copy_product_id);
  }

  /**
   * Public method to update product quantity for franchise's product
   * @author: Nishu, Aug 2017
  */
  public function stockUpProduct(array $product, int $new_product_id, int $product_option_value_id){
    
    $this->load->model('catalog/product', 'admin');
   
    // If new_product_id exists means product already exist for this franchise, we just need to update the quantity
    $new_product_quantity = $this->registry->admin_model_catalog_product->getProductQuantity($new_product_id);

    $updated_quantity = (int) $product['quantity'] + (int) $new_product_quantity;

    $this->registry->admin_model_catalog_product->updateNewProductQuantity($new_product_id, $updated_quantity);

    // check for option
    if ($product_option_value_id) {
        $this->registry->admin_model_catalog_product->updateProductOptionQuantity($new_product_id,$product_option_value_id,$product['quantity']);
    }
  }

  /**
   * Public Methos to 
   *     Add product  To-Franchise- If franchise order that product First time
   *     Update Stock To-Franchise- If that product is already ordered by Franchise
   * @param: Array
   * @author: Nishu, Aug 2018
  */
  public function AddOrUpdateFranchiseProduct(array $data){
    
    //Cutomer id is empty
    if(empty($data['customer_id'])){
      return false;
    }

    $franchise_id = (int)$data['customer_id'];
    
    //Get Franchise data from parent class
    $franchise_data = $this->getFranchiseData($franchise_id);

    if (!empty($franchise_data)) {
      //Get product details
      $file_name = array();
      $file_name['order_id']    = (int)$data['order_id'];
      $file_name['suborder_id'] = $data['suborder_id'];
      $file_name                = base64_encode(serialize($file_name));
      $buyer_invoice = new BuyerInvoice($this->registry, $file_name);
      $buyer_invoice->setOptions('get_full_path', TRUE);

      $products = $buyer_invoice->getProductsArrayBySuborderId($data['order_id'], $data['suborder_id']);

      //Empty check for all_products from buyer invoice
      if (!empty($products)) {

        foreach ($products as $key => $product) {

          // get the product options if any
          $product_option = OrderProduct::getOrderProductOption($this->db, (int)$product['order_product_id']);

          if(!empty($product_option)){

              $product_option_value_id = (int)$product_option['product_option_value_id'];
          } else {

              $product_option_value_id = 0;
          }

          // get the new_product_id corresponding to the current product_id and franchise_id
          $new_product_id = $this->getProductToFranchise((int)$product['product_id'], $franchise_id);
         
          if (empty($new_product_id)) {
            
            //If that product ordered first time by franchise
            $product['franchise_id'] = $franchise_id;
            $this->copyProduct($product, $product_option_value_id);

          } else {

            //Stockup product quantity, already ordered product by franchise
            $this->stockUpProduct($product, (int)$new_product_id, $product_option_value_id);

          }
        }
      }
    }
    
            
  }

}
