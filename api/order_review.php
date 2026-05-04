<?php
     require_once(__DIR__.'/system.php');
     require_once(DIR_SYSTEM.'library/solr/lookup.php');
     require_once(DIR_CATALOG.'form/register_form.php');
     require_once(DIR_SYSTEM.'library/customer.php');
     require_once( DIR_SYSTEM . 'library/operations/orders/order_info.php' );
     require_once( DIR_SYSTEM . 'library/operations/orders/order_product_review.php' );

    class Order_reviewController extends SystemController
    {
        private $error = "";

        public function __construct($params) {

            parent::__construct($params);

        }


    public function getLastOrder(){
       $this->load->model('restapi/service');
       $this->load->model('tool/image');
       $this->load->model('catalog/product');
       $this->load->model('account/order');
       $inputJSON = file_get_contents('php://input');
       $request = json_decode($inputJSON, TRUE);
       $is_product_rating_required=$is_order_rating_required='1';
       $check_access_token=$this->validateAccessTokenForUser($request);
       $oreder_products=array();
       $order_id='';
       if($check_access_token['status']=='1' ){
         $customer_id=$request['user_id'];
         $last_order_id=$this->model_restapi_service->getCustomerLastOrderIdWithAtleastOneDeliveredStatus((int)$customer_id);
         if(!empty($last_order_id)){
           $order_info = new OrderInfo();
           $order_product_review = new OrderProductReview();
           $last_order_details=$order_info->getOrderInfo($this->db,$last_order_id);
           foreach ($last_order_details['suborder'] as $key => $sub_order_value) {
             foreach ($sub_order_value['order_product'] as $key => $value) {
               $oreder_products[$value['order_product_id']]['order_product_id']=$value['order_product_id'];
               $oreder_products[$value['order_product_id']]['product_id']=$value['product_id'];
               $oreder_products[$value['order_product_id']]['seller_sku']=$value['seller_sku']??'';
               $oreder_products[$value['order_product_id']]['share_message']=$this->getShareMessage($last_order_details['order'],$value,$request);
               $OriginalImages = $this->model_catalog_product->getProductOriginalImages($value['product_id']);
               if(!empty($OriginalImages['image'])){
                 $oreder_products[$value['order_product_id']]['thumbnail_image']=$this->model_tool_image->resize($OriginalImages['image'],250, 375);
               }else{
                   $oreder_products[$value['order_product_id']]['thumbnail_image'] = $this->model_tool_image->resize('no_image.png', 250, 375);
               }
               $order_product_review_status=$order_product_review->getOrderProductReview($this->db,$value['order_product_id']);
               if(empty($order_id) && !empty($value['order_id'])){
                 $order_id=$value['order_id'];
                 $order_rating_avail=$this->model_account_order->checkOrderRatingAvailableOrNot($order_id);
                 $is_order_rating_required = $order_rating_avail>'0'?'0':'1';
               }
               if($is_product_rating_required=='1' && !empty($order_product_review_status[$value['order_product_id']])){
                 $is_product_rating_required='0';
               }
             }
           }
         }
             $data['order_products']=!empty($oreder_products)?$oreder_products:'';
             $data['order_id']=$order_id;
             $data['reasons']=$this->model_account_order->orderReviewReasonsList();
             if(count($oreder_products)> 0){
               $data['is_product_rating_required']=$is_product_rating_required;
                $data['is_order_rating_required']=$is_order_rating_required;
             }else{
               $data['is_product_rating_required']='0';
               $data['is_order_rating_required']='0';
             }
             if($data['is_product_rating_required']=='0'){
               $data['order_products']=null;
             }                     
                    $rt['data'] = $data;
                    $rt['status'] = '1';
                    $rt['status_text'] = 'Success';
                    $rt['message'] = 'Success';
                   }else{
                     $rt=$check_access_token;
                  }
     echo json_encode($rt); exit;      
    }

     
      public function setOrderReview(){
              $this->load->model('account/order');
              $inputJSON = file_get_contents('php://input');
              $request = json_decode($inputJSON, TRUE);
              $is_product_rating_required=$is_order_rating_required='1';
              $check_access_token=$this->validateAccessTokenForUser($request);
              $oreder_products=array();
              $order_id='';
              if($check_access_token['status']=='1' ){
                if(!empty($request['rating'])){
                    $save_order_review=$this->model_account_order->saveOrderReview($request);
                    if($save_order_review > '0'){
                      if(!empty($request['reasons'])){
                        $save_order_review=$this->model_account_order->saveOrderReviewReasons($save_order_review,$request['reasons']);
                        }
                           $rt['data'] = '';
                           $rt['status'] = '1';
                           $rt['status_text'] = 'Success';
                           $rt['message'] = 'Success';
                    }else{
                      $rt['error_code'] = '1005';
                      $rt['status'] = '0';
                      $rt['status_text'] = 'Failed';
                      $rt['message'] = 'Review rating not save please try again.';
                    }
                }
                else{
                    $rt['error_code'] = '1002';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Please add rating.';
                  }
             }else{
                    $rt=$check_access_token;
                  }
            echo json_encode($rt); exit;

      }

  /**
   * Function : validateAccessTokenForUser
   * Method to validate Access Token For User
   * Request Parameters : User ID, access token
   * Type : Post
   * @author Rahul
   * Output : access to ken is valid or not
   * */
  public function validateAccessTokenForUser($request){
    $this->load->model('restapi/service');
      if (!empty($request['user_id']) && !empty($request['access_token']))
        {
        $validate_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$request['access_token'], (int)$request['user_id']);
        if ($validate_access_token == '1')
          {
          $return['status'] = '1';
          $return['status_text'] = 'Success';
          }
          else
          {
          $return['error_code'] = '1003';
          $return['status'] = '0';
          $return['status_text'] = 'Failed';
          $return['message'] = 'Invalid access token or user id.';
          }
        }
        else
        {
        $return['error_code'] = '1001';
        $return['status'] = '0';
        $return['status_text'] = 'Failed';
        $return['message'] = 'http request does not have user id or access token.';
        }

      return $return;
  }

    private function getShareMessage($order_detail,$product_details,$request){
            $message='';
            if (!empty($request['default_sharing_template'])) {
                  $product_request['name']=$product_details['name']??'';
                  $product_request['model']=$product_details['model']??'';
                  $product_request['comment']=$product_details['comment']??'';
                  $product_request['customer_company']=$order_detail['shipping_company']??'';
                  $product_request['customer_firstname']=$order_detail['firstname']??'';
                  $product_request['customer_lastname']=$order_detail['lastname']??'';
                  $product_request['customer_telephone']=$order_detail['telephone']??'';
                  $product_request['price_per_piece']=$product_details['price_per_piece']??00;
                  $product_request['order_currency_id']=$order_detail['currency_id']??00;
                  $message=$this->createShareMessage($request,$product_request);

            }
            return $message;

    }

    private function createShareMessage($request,$product_request){
            $dummy_currency_id = $this->currency->currencies[DUMMY_INR_CURRENCY]['currency_id'];
            $share_message='';
            $default_template_text = '';
                if (!empty($request['default_sharing_template'])) {
                  $default_template_text = $request['default_sharing_template'];
                }
                $update_price_by = '0';
                  if (!empty($request['sharing_margin'])) {
                  $update_price_by = $request['sharing_margin'];
                }
                $find = array('[product_name]','[set]',
                  '[model]', '[markup_price]',
                  '[shop_name]','[my_name]','[mobile]');


                    $share_message = '';
                    $up_price = $product_request['price_per_piece'];
                    if (!empty($default_template_text)) {

                      $testString = $up_price ;
                      $pr = ltrim(preg_replace("/[^0-9.]/", "", $testString),'.');
                      if ($product_request['order_currency_id'] == $dummy_currency_id) {
                        $pr = $pr/$this->currency->getValue(DUMMY_INR_CURRENCY);
                      }
                      $updated_price = ($update_price_by * $pr)/100;
                      $updated_price = ceil($updated_price + $pr);

                      $updated_price = $this->currency->format($updated_price, $this->currency->getCode(), $this->currency->getValue(), false);
                      // will round to 5 only if currency is inr
                      if ($this->currency->getCode() == 'INR' || $this->currency->getCode() == DUMMY_INR_CURRENCY) {
                        $updated_price = $this->model_restapi_service->roundUpToAny($updated_price,5);
                      }

                      $uprice = $this->currency->format($updated_price, $this->currency->getCode(), 1);
                      $up_price = $uprice;
                      $replace = array(trim(html_entity_decode($product_request['name'])),
                        html_entity_decode($product_request['comment']),
                        $product_request['model'],$uprice,$product_request['customer_company'],
                        $product_request['customer_firstname'].' '.$product_request['customer_lastname'],
                        $product_request['customer_telephone']);
                        $share_message = str_replace($find, $replace, $default_template_text);
                    }
                    return $share_message;
    }

 


    }