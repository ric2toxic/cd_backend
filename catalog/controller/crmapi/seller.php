<?php
class ControllerCrmapiSeller extends Controller 
{
	/*
    *Get client monthly sale by seller_id
    * 03-04-2017
    */
    public function getSellerSales()
    {

        $seller_id = 92;         

        $today = date('Y-m-d');
         
        $last_month = date('Y-m-d', strtotime(date('Y-m')." -1 month")); 

        $sql = "SELECT 

                COUNT(DISTINCT oop.seller_sku) AS sku,

                COUNT(DISTINCT oop.order_id) AS total_order,

                FORMAT(sum(oop.transfer_price_per_piece * oop.piece_in_set * oop.quantity),2) AS total_price,

                FORMAT(sum(orrbyoop.transfer_price_per_piece * orrbyoop.piece_in_set * orr.quantity), 2) AS return_order_price, 

                oop.sku,oop.order_id, oms.seller_id, omp.product_id
                
                FROM " . DB_PREFIX . "ms_seller oms
                
                INNER JOIN 
                " . DB_PREFIX . "ms_product omp ON oms.seller_id = omp.seller_id
                
                INNER JOIN
                " . DB_PREFIX . "order_product oop ON omp.product_id = oop.product_id

                INNER JOIN
                " . DB_PREFIX . "order oo ON oop.order_id = oo.order_id

                INNER JOIN
                " . DB_PREFIX . "suborder os ON oo.order_id = os.order_id

                LEFT JOIN
                " . DB_PREFIX . "return orr ON (oop.order_product_id = orr.order_product_id)

                LEFT JOIN
                " . DB_PREFIX . "seller_debit_note osdn ON (orr.debit_note_id = osdn.debit_note_id AND osdn.debit_note_status = 1 AND orr.active_row = 1)

                LEFT JOIN
                " . DB_PREFIX . "order_product orrbyoop ON orr.order_product_id = orrbyoop.order_product_id


                WHERE

                oms.seller_id = '$seller_id'

                AND os.date_added <= '$today'

                AND os.date_added >= '$last_month'

                /*AND os.order_status_id IN (5,15)*/

                /*GROUP BY oop.order_id*/

                /*ORDER BY oop.order_id DESC*/ 

                ";

        //echo $sql; die;
        $result = $this->db->query($sql);

            //echo '<pre>';
           // print_r($result); die;

      
        //$total_order = '';
        //total_price = '';       
       // $sku = '';

        foreach ($result->rows as $key => $value) {

            // $total_order += $value['total_order'] ; 
            //$total_price += $value['total_price'];

            //if (!empty($value['sku']))
                //$sku[]  =  $value['sku']; return_total_price

                $total_order = !empty($value['total_order']) ? $value['total_order'] : 0 ;

                $return_order_price = !empty($value['return_order_price']) ? $value['return_order_price'] : 0 ;

                $total_price = !empty($value['total_price']) ? $value['total_price'] : 0 ;

                $net_price = $total_price - $return_order_price;

                //echo $net_price;die;


                $total_sku = !empty($value['sku']) ? $value['sku'] : 0 ;

        }

        //if ( is_array($sku) && !empty($sku) ) 
            //$sku = count(array_unique($sku));

        //else
             //$sku = 0;

        //$sku = $value['sku'];

        $result2 = $this->getClientGrossSales($seller_id);

        $results1 = array (                    
                        'total_monthly_order' => $total_order, 
                        'total_monthly_price' => $net_price, 
                        'total_monthly_sku'   => $total_sku
                    );

        //print_r($results1); die;

        $result = array_merge($result2,$results1);

        echo json_encode($result); exit;     
    }

    /*
    * Client Gross Sales
    * 03-04-2017
    */
    public function getClientGrossSales($seller_id)
    {    
        $sql = "SELECT oop.sku, count(DISTINCT oop.sku)as totalsku ,sum(oop.transfer_price_per_piece * oop.piece_in_set * oop.quantity ) as total_price, count(oop.order_id) as total_order
                
                FROM " . DB_PREFIX . "ms_seller oms
                
                INNER JOIN 
                " . DB_PREFIX . "ms_product omp ON oms.seller_id = omp.seller_id
                
                INNER JOIN
                " . DB_PREFIX . "order_product oop ON omp.product_id = oop.product_id

                INNER JOIN
                " . DB_PREFIX . "order oo ON oop.order_id = oo.order_id

                INNER JOIN
                " . DB_PREFIX . "suborder os ON oo.order_id = os.order_id

                WHERE
                oms.seller_id = '$seller_id'
                
                /*AND os.order_status_id = 5,15*/
                GROUP BY oop.order_id
                ORDER BY oop.order_id DESC
                ";             

        $result = $this->db->query($sql);                           
      
        $total_order = '';
        $total_price = '';       
        $sku = '';

        foreach ($result->rows as $key => $value) {

            $total_order += $value['total_order']; 
            $total_price += $value['total_price'];

            if (!empty($value['sku']))
                $sku[]  =  $value['sku'];        
        }

        if ( is_array($sku) && !empty($sku) ) 
            $sku = count(array_unique($sku));

        else
            $sku = 0;

        $results = array('total_order' => !empty($total_order)?$total_order:0, 'total_price' => !empty($total_price)?$total_price:0 , 
            'total_sku' => $sku);        

        return $results;   
    }

    /*
    * Saller Rating
    * 05-04-2017    
    */
    public function sallerRating() {

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );            
        }        

        $seller_id  = !empty($request['seller_id']) ? $request['seller_id'] : 0;
        $rating     = !empty($request['rating']) ? $request['rating'] : 0;
        $sales_staff_id = !empty($request['sales_staff_id']) ? $request['sales_staff_id'] : 0;

        if ( $seller_id > 0 && $rating != '' && $sales_staff_id > 0) {

            //Get old seller rating for save in oc_seller_updates
            $previous_rating = "SELECT seller_rating FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '$seller_id'";
            $previous_rating = $this->db->query($previous_rating);
            $previous_rating = $previous_rating->row['seller_rating'];
            

            //Update new seller rating
            $sql = "UPDATE " . DB_PREFIX . "ms_seller SET 
                    seller_rating = '$rating' 
                    WHERE
                    seller_id = '$seller_id' ";                
            
            $query = $this->db->query($sql);

            if ($query) {

                //Add seller rating history in oc_seller_updates
                $sql = "INSERT INTO " . DB_PREFIX . "seller_updates SET
                    seller_id           = '$seller_id' ,
                    sales_staff_id      = '$sales_staff_id',
                    update_type         = 'seller_rating',
                    new_value           = '$rating',
                    verification_status = '',
                    date_added          = NOW(),
                    previous_value      = '$previous_rating'
                    ";            
                
                $query = $this->db->query($sql);
                
                $result['status'] = '1';
                $result['status_text'] = 'Success.';
                $result['message'] = 'Seller Rating has been updated.'; 

            } else {

                $result['status'] = '0';
                $result['status_text'] = 'Error.';
                $result['message'] = 'Db error.';
            }
        } else {

            $result['status'] = '0';
            $result['status_text'] = 'Error.';
            $result['message'] = 'Input Correct value in perameters.';
        }        

        echo json_encode($result); exit;
    }
}
