<?php
declare(strict_types=1);

class Franchise
{
  protected $registry;
  protected $db;
  protected $load;
  
  public function __construct(Controller $controller) {
    if (method_exists($controller, 'get')) {
        $this->registry = $controller;
        $this->db       = $controller->get('db');
        $this->load     = $controller->get('load');
    } else {
        $this->registry = $controller;
        $this->db       = $controller->db;
        $this->load     = $controller->load;
    }
  }

  /**
   * Public Method to check given customer is franchise-customer
   * @param: $customer_id Integer
   * @author: Nishu, Aug 2018
  */
  public function isCustomerFranchise(int $customer_id) : bool{
    $is_franchise = false;
    // check for provided customer id
    if(!empty($customer_id)){
      $sql = "
              SELECT 
                franchise_id
              FROM 
                " . DB_PREFIX . "franchise_data 
              WHERE 
                franchise_id = ". (int)$customer_id . "
                AND franchise_status = 1
              ";

      $query = $this->db->query($sql);
      if($query->num_rows > 0){
        $is_franchise = true; //Is actually Franchise Customer with franchise_status = 1
      }
    }
    return $is_franchise;
  }

  /**
   * @info: Get franchise data using franchise id
   * @param: franchise_id (int Required)
   * @author: Nishu, Aug 2018
   */
  public function getFranchiseData(int $franchise_id) : array{
    $data = array();
    if(!empty($franchise_id)){
        $sql = "
                SELECT 
                  * 
                FROM 
                  " . DB_PREFIX . "franchise_data 
                WHERE 
                  franchise_id = '" .(int)$franchise_id . "' 
                LIMIT 
                  1
              ";

        $result = $this->db->query($sql);

        if($result->num_rows > 0){
            $data = $result->row;
        }
      
    }//End of If
    return $data;
  }//End of getFranchiseData()

  /**
  * public method To get product assigned to the franchise
  * @request : product_id   : integer of product id
  * @request : franchise_id : integer of franchise id
  **/
  public function getProductToFranchise(int $product_id, int $franchise_id) : int{
    $data = 0;
    if(!empty($product_id) && !empty($franchise_id)){

      $sql = "SELECT new_product_id from
             ".DB_PREFIX."product_to_franchise
             WHERE product_id = '" . (int) $product_id . "'
             AND franchise_id = '" . (int) $franchise_id . "' ";

      $query = $this->db->query($sql);

      if($query->num_rows)
        $data = (int)$query->row['new_product_id'];
    }
    return $data;
  }

}//End of Class
