<?php
class ModelCatalogClubFactoryProduct extends Model {

    public function checkClubfactoryOrderPlacedOrNot($order_details) {
          $sql = "SELECT id FROM " . DB_PREFIX . "clubfactory_unpublish_order_list 
                  WHERE order_number='" . $this->db->escape($order_details['orderNo']) . "' 
                  AND club_factory_order_id   ='" . (int)$order_details['orderId'] . "'";

                  $result = $this->db->query($sql);
                      if ($result->num_rows) {
                            return 0;
                      }else{
                           return 1;

                      }
    }

    public function insertClubfactoryOrderPlaced($order_details) {
                            $insert= "INSERT INTO `" . DB_PREFIX . "clubfactory_unpublish_order_list`
                               SET
                               order_number = '" . $this->db->escape($order_details['orderNo']) . "',
                               wsb_order_id = '" . (int) $order_details['wsb_order_id'] . "',
                               club_factory_order_id  = '" . (int) $order_details['club_factory_order_id'] . "',
                               created = '". date('Y-m-d H:i:s')."',
                               status = 1";
                               $query = $this->db->query($insert);
                               $last_insert_id = $this->db->getLastId();
                               return $last_insert_id;

    }

    public function updateClubfactoryOrderStatus($id,$status) {
        if($status=='1'){
            $this->db->query("UPDATE " .DB_PREFIX."clubfactory_unpublish_order_list SET status = '1' WHERE id = '".(int)$id."'");
        }else{
            $this->db->query("DELETE FROM " .DB_PREFIX."clubfactory_unpublish_order_list  WHERE id = '".(int)$id."'");
        }
        return true;
    }

    public function getProductAccordingClubFactorySku($clubfactory_sku) {
                // $product_detail['product_id']='66048';
                // $product_detail['product_option_value_id']='40506';
                // return $product_detail;
            $sql = "SELECT product_id,product_option_value_id FROM " . DB_PREFIX . "product_sku_club_factory_mapping 
              WHERE clubfactory_sku='" . $this->db->escape($clubfactory_sku) . "' LIMIT 1";
            $result = $this->db->query($sql);
              if ($result->num_rows) {
                $product_detail['product_id']=$result->row['product_id'];
                $product_detail['product_option_value_id']=$result->row['product_option_value_id'];
                return $product_detail;
              }
                return 0;
    }

    public function getProductOptionValueId($product_id,$product_option_value_id) {
        $sql = "SELECT
                opov.product_id,
                opov.product_option_id,
                opov.product_option_value_id,
                opov.weight,
                opov.price,
                oco.type,
                ood.name,
                oovd.name AS value
              FROM
                 " . DB_PREFIX . "product_option_value AS opov
              LEFT JOIN
                 " . DB_PREFIX . "option oco ON(opov.option_id = oco.option_id)
              LEFT JOIN
                 " . DB_PREFIX . "option_description ood ON(opov.option_id = ood.option_id)
              LEFT JOIN
                 " . DB_PREFIX . "option_value_description oovd ON(
                  opov.option_value_id = oovd.option_value_id
                )
              WHERE
                opov.product_id='" . (int)$product_id . "' 
                AND opov.product_option_value_id='" . (int)$product_option_value_id . "'
                AND ood.language_id = '1' AND 
                oovd.language_id = '1'
                LIMIT 1";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0){
            return false;
        }else{
            return array(
                'product_id'       => $result->row['product_id'],
                'product_option_id' => $result->row['product_option_id'],
                'product_option_value_id'     => $result->row['product_option_value_id'],
                'name'       => $result->row['name'],
                'value' => $result->row['value'],
                'type'     => $result->row['type'],
                'option_price' => $result->row['price'],
                'weight' => $result->row['weight']
            );
        }
        exit;
    }

}
