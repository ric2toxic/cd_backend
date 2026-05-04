<?php

/* 
 * Author : Amarat
 */
class ControllerTestTestunit extends Controller
{
    public function updateUnitInProductFromCategory() {
        //select product_id, category_id, unit_id 
        $sql = "select PC.product_id, PC.category_id, C.unit_id from " . DB_PREFIX . "product_to_category as PC left join " . DB_PREFIX . "category as C on C.category_id = PC.category_id where C.unit_id != 0";
        $result = $this->db->query($sql);
        if(count($result->rows)){
            foreach($result->rows as $row){
                $product_id = $row['product_id'];
                $category_id = $row['category_id'];
                $unit_id = $row['unit_id'];
                //update unit in od_product table
                $sql = "update " . DB_PREFIX . "product set unit_id = " . $unit_id . " where unit_id = 0 and product_id = " . $product_id;
                //echo $sql; die;
                $this->db->query($sql);
            }
            die('success'); 
        }else{
            die('No record found'); 
        }
    }

    public function updateUnitInCategoryRandomly() {
        // get all recort from category
        $sql = "SELECT category_id FROM " . DB_PREFIX . "category";
        $result = $this->db->query($sql);
        
        foreach($result->rows as $row){
            //echo $row['category_id']."<br/>";
            
            $sql = "UPDATE " . DB_PREFIX . "category SET unit_id = " . rand(1,5) . " WHERE category_id = " . $row['category_id'];
            //echo $sql."<br>"; //die;
            $this->db->query($sql);
        }
        die('success');
    }
    
    public function updateUnitInSomeProductRandomly() {
        // get all recort from category
        $sql = "SELECT product_id FROM " . DB_PREFIX . "product";
        $result = $this->db->query($sql);
        
        foreach($result->rows as $row){
            //echo $row['category_id']."<br/>";
            $sql = "UPDATE " . DB_PREFIX . "product SET unit_id = " . rand(1,15) . " WHERE product_id = " . $row['product_id'];
            //echo $sql."<br>"; die;
            $this->db->query($sql);
        }
        
        //set unit_id 0 > 5
        $sql = "UPDATE " . DB_PREFIX . "product SET unit_id = 0 WHERE unit_id > 5";
        //echo $sql."<br>"; die;
        $this->db->query($sql);
            
        die('success');
    }
    
}
