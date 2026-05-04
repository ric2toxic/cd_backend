<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SOR Purchase
 *
 * @author Kalyan
 */
class ControllerTestSorPurchase extends Controller {    

    public function stockReport(){       
       
       $sql = "SELECT
                    p.product_id,
                    pd.name,
                    pd.set_description,
                    p.store_sales,
                    p.quantity,
                    p.piece_in_set,
                    p.model,
                    p.wsb_purchase_id,
                    p.image                  
                FROM oc_product p
                INNER JOIN oc_product_description pd
                    on p.product_id=pd.product_id AND pd.language_id=1 
                WHERE p.quantity>0 AND p.sor_product=1 and p.store_sales!='NO'                   
                ORDER BY p.store_sales ASC
                ";
        $result = $this->db->query($sql);
        $salesData = $result->rows;
        //pr($salesData);        
        $stcok = array();
        $i=0;
        if (!empty($salesData) && count($salesData)) {
            foreach ($salesData as $key => $value) {
                $stcok[$value['store_sales']][$i] = $value;
                $i++;
            }
        }
        //pr($stcok);
        if (!empty($stcok) && count($stcok)) {
            $bodyHtml = $this->setBodyHtml($stcok);
        }                 
    }
    /*
    *
    * set html and send mail to seller and accounts
    * @author: kalyan 20th Sep, 2017
    *
    */
    private function setBodyHtml($data) {
        return true;
    }
}