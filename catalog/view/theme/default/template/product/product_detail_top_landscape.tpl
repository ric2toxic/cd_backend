<div class="col-sm-12 product_img_detail_landscape home_width_adjustment">
  <?php if ($thumb) { ?>
  <div class="col-md-6 col-xs-12 col-sm-6 main-image mainImgToDown">
    <?php
        if($quantity <= 0 || strtolower($stock_status) == 'out of stock'){
          $bgcolor = 'red';
          $instock = '<span class="in_stock">'.$text_out_of_stock.'</span>';
        }else{
          $bgcolor = 'green';
          /* if($is_single == '1'){
              //$instock = '<span class="in_stock">'.$text_in_stock.'</span>';
          }else{ */
              $instock = '<span class="in_stock">'.$text_in_stock.'</span>';
          /* } */
        }
        /* if($is_single == '1'){
        }else{ */
    ?>
    <!--<div class="stock_circle <?php echo $bgcolor; ?>"><div><?php echo $instock; ?><span class="soldout"><?php echo $text_sold_out;?></span></div></div>-->
    <?php /* } */ ?>
    <a class="thumbnail panzoom-parent" href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>">
      <?php if (!empty($percent_discount)) { ?>
      <div class="discount-arrow"><?php echo $percent_discount; ?>% Discount</div>
      <?php } ?>
      <img class="panzoom" src="<?php echo $popup; ?>" width="<?php echo $popup_img_width; ?>" height="<?php echo $popup_img_height; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
    <!--<span class="downloadImages col-md-9 col-xs-12 col-sm-8 pull-right main-image"><?php echo '<a href="'.$downloadImages.'">'.$downloadText.'</a>'; ?></span>-->
    <div class="col-sm-12 ">
      <?php if ($thumb || $images) { ?>
      <div class="thumbnails">
        <div id="landscape_caraousel" data-ride="carousel">
          <?php
            if ($images) { ?>
            <?php
                 $i=0;
                   $preload_img_string = '';
                   foreach ($images as $image) {

                 $i++;
                 $preload_img_string .= "'".$image['popup']."',";
              ?>
              <div class="show_small_size" style="display :inline-block; width:100px;"><a class="thumbnail" html-data="<?php echo $image['popup']; ?>" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"> <img src="<?php echo $image['thumb']; ?>" width="<?php echo $image['thumb_img_width']; ?>" height="<?php echo $image['thumb_img_height']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a></div>
            <?php } ?>
          <?php } ?>

        </div>
      </div>
      <?php } ?>
    </div>
    <span class="getImage downloadImages col-md-9 col-xs-12 col-sm-8 pull-right main-image"><?php echo '<a href="javascript:void(0);">'.$downloadText.'</a>'; ?></span>
    <?php if(isset($exp_final_date) && $exp_final_date > 1) { ?>
    <div class="available_after_days offer-block"><i aria-hidden="true" class="fa fa-tag"></i><?php echo $text_available_after; ?> <?php echo $exp_final_date; ?> <?php echo $text_days; ?> </div>
    <?php } ?>
  </div>
  <?php } ?>
  <div class="col-sm-6 col-xs-12 nopadding product_details_popup">
    <div class="col-sm-12 details_heading">
      <div class="col-sm-11">
        <h1 class="product_detail_popup_heading"><?php echo $heading_title; ?></h1>
      </div>
      <div class="col-sm-1">
        <?php
        /*------ start fill heart after user login ----*/
        $fill_heart = 'fa fa-heart-o';
        if($this->customer->isLogged()){
          if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
            if(in_array($product_id,$this->session->data['wishlist'])){
              $fill_heart = 'fa fa-heart custom_heart';
            }
          }
        }else{
          if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
            if(in_array($product_id,$this->session->data['wishlist'])){
              $fill_heart = 'fa fa-heart custom_heart';
            }
          }
        }
        /*------ END fill heart after user login ----*/
        ?>
        <div class="button_add_wishlist" data-product-id="<?php echo $product_id; ?>">
          <button type="button" id="wishlist_<?php echo $product_id; ?>" data-toggle="tooltip" class="addtowishlist" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');">
            <i class="<?php echo $fill_heart; ?>" id="wishlist_heart_<?php echo $product_id; ?>"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="codeandrating">
      <div class="col-sm-9">
        <ul class="list-unstyled">
          <?php if ($manufacturer) { ?>
          <li><?php echo $text_manufacturer; ?> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></li>
          <?php } ?>
          <?php if($previously_ordered == 1){ ?>
          <li class="product_code"><?php echo $text_model; ?> <?php echo $model; ?> &nbsp;&nbsp;&nbsp;</li>
          <li class="previously_ordered"><?php echo '( ' . $text_previously_ordered . ' ) '; ?></li>
          <?php }else{ ?>
            <li><?php echo $text_model; ?> <?php echo $model; ?> &nbsp;&nbsp;&nbsp;</li>
          <?php } ?>

          <?php

          <!-- <li><h4><?php echo $stock; ?></li></h4> -->
        </ul>
      </div>
      <?php if($rating > 0){ ?>
      <div class="rating col-sm-3">
        <p>
          <?php for ($i = 1; $i <= 5; $i++) { ?>
          <?php if ($rating < $i) { ?>
          <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
          <?php } else { ?>
          <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
          <?php } ?>
          <?php } ?><br/>
        </p>
      </div>
      <?php } ?>
    </div>
          <?php if($mrp > $unformatted_price ) { ?>
            <div class="col-sm-12 product-detail">
              <div class="saving_money offer-block">
                  <p class="col-sm-6 nopadding">MRP: <br />><?php echo $format_mrp; ?></p>
                  <p class="col-sm-6">Margin: <br /><?php echo $saving_money; ?></p>
                  <div class="cross_image"></div>
              </div>
            </div>
            <?php } ?>
    <div class="col-sm-12 col-xs-12 product_detail">
      <?php if ($price) { ?>
      <div class="row">
        <?php if (!$special) { ?>
        <div class="col-sm-12 nopadding">
          <div class="col-sm-6 nopadding">
              <h3 class="main_price">
              <span id ="sell-price"><?php echo $price ?></span> <br/>
              <span><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id and $text_tax_rate) { echo $text_plus_cst . $text_tax_rate;} ?> </span>
            </h3>
            <input type= "hidden" id ="product_unit_price" value ="<?php echo floatval($unformatted_price); ?>" />
          </div>
          <div class="col-sm-6">
            <?php
                  if(!empty($custom_store_selling_price) && !empty($custom_store_product_href)){
                     if($is_single== 0){
            ?>
                    <div class="store_information">
                      <h1 class="store_title"><a href= "<?php echo $custom_store_product_href; ?>" ><?php echo $text_single;?></a></h1>
                      <p class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?>
                        </p>
                      <h3 class="store_tax"><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id and $text_tax_rate) { echo $text_plus_cst . $text_tax_rate;} ?></h3>
                    </div>
                  <?php }else{ ?>
                    <div class="store_information">
                      <h1 class="store_title"><a href="<?php echo $custom_store_product_href; ?>" ><?php echo $text_wholesaleBox;?></a></h1>
                      <p class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?></p>
                      <h3 class="store_tax"><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id and $text_tax_rate) { echo $text_plus_cst . $text_tax_rate;} ?></h3>
                    </div>
                 <?php } ?>
            <?php }else{ ?>
            <?php if ($review_status) { ?>

              <?php /* ?> <hr>
              <!-- AddThis Button BEGIN -->
              <div class="addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <!-- <a class="addthis_button_pinterest_pinit"></a> <a class="addthis_counter addthis_pill_style"></a> --> </div>
              <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-515eeaf54693130e"></script>
              <!-- AddThis Button END -->
              <?php */ ?>
            </div>
            <?php } ?>
            <?php }?>
          </div>
        </div>
        <div class="offer-block col-sm-12 nopadding">
            <i class="fa fa-tag" aria-hidden="true"></i>
            2% discount on all prepaid order.
        </div>
        <?php

          /* if($is_single == '1'){

            }else{ */ ?>
        <div class="col-sm-12 product_set_description">
          <?php if ($minimum > 1) { ?>
          <span class="label label-minimum-order"><?php echo $text_moq_pre . $minimum . $text_moq_post; ?></span>
          <?php } else { ?>
          <span class="label label-minimum-order"><?php echo $text_moq_default; ?></span>
          <?php } ?>
          <p>
           <?php echo $set_description; ?>
          </p>
          <a class="more_desc" href="<?php echo $_SERVER['REQUEST_URI'];?>#description">More description</a>
        </div>
        <?php /* } */?>
        <?php } else { ?>
        <div class="col-sm-12"><span style="text-decoration: line-through;"><?php echo $price . $text_per_piece; ?></span></div>
        <div class="col-sm-12">
          <h2><?php echo $special . $text_per_piece; ?> <span><?php if ($tax_class_id and $text_tax_rate) { echo $text_plus_cst . $text_tax_rate; } ?></span></h2>
        </div>
        <?php } ?>
        <?php /* if ($tax) { ?>
        <div class="col-sm-12"><?php echo $text_tax; ?> <?php echo $tax; ?></div>
        <?php } */ ?>
        <?php if ($points) { ?>
        <div class="col-sm-12"><?php echo $text_points; ?> <?php echo $points; ?></div>
        <?php } ?>
        <?php if ($discounts) { ?>
        <div class="col-sm-12 qty-discount-price">
          <div class="row">
            <table class="table table-bordered">
              <tr>
                <th class="table_data_color">
                  <?php echo $entry_qty; ?>
                </th>
                <th class="table_data_color table_data_align">
                  <?php echo $entry_price .$text_per_piece ; ?><?php if ($tax_class_id) { echo $text_tax; } ?>
                </th>

              </tr>
              <?php
              $i = 0;
              foreach ($discounts as $discount) {
              $i++;
              ?>
              <tr>
                <td>
                  <span class="discount-qty"><?php echo $discount['quantity'] . $text_discount;; ?></span>
                </td>
                <td align="right" style="color:#000;">
                  <?php echo $discount['price']; ?>
                </td>
              </tr>
              <?php
      }
  ?>
            </table>
          </div>
        </div>
        <?php } ?>
      </div>
      <?php } ?>
      <?php
      if(isset($options_tpl)){
        echo $options_tpl;
      }
      ?>
    <div class="col-sm-12 ">
      <?php if(isset($cod_available) && ($cod_available == 0)){ ?>
      <div class="col-sm-6 ask-a-question">Ask A Question</div>
      <div class="col-sm-6 cod-not-avaiable">
      <div><?php echo $text_cod_available; ?></div>
      </div>
      <?php }else{ ?>
      <div class="col-sm-12 ask-a-question">Ask A Question</div>
      <?php } ?>
    </div>
    <!-- Ask a question popup -->
    <div class="question_popup" id="question_popup">
      <div class="content_q_popup ">
        <div class="popup-q-header msg_q_body">
          <button type="button" class="close" data-product-id="<?php echo $product_id; ?>">&times;</button>
          <h4 class="popup-title"><?php echo $question_popup_heading;?></h4>
        </div>
        <div class="popup-q-body">
          <?php if(isset($logged_in)){ ?>
          <input type="hidden" name="customer_name_login" value="<?php echo $customer_name; ?>" class="customer_q_name_login">
          <?php }else{ ?>
          <div class="form-group col-sm-12">
            <label class="col-sm-2 control-label"><?php echo "Name"; ?></label>
            <div class="col-sm-10">
              <input type="text" name="customer_name" value="" class="customer_q_name">
            </div>
          </div>

          <?php } ?>
          <?php if(isset($logged_in)){ ?>
          <input type="hidden" name="customer_telephone_login" value="<?php echo $telephone; ?>" class="customer_q_telephone_login">
          <input type="hidden" name="customer_email_login" value="<?php echo $email; ?>" class="customer_q_email_login">
          <?php }else{ ?>
          <div class="form-group col-sm-12">
            <label class="col-sm-2 control-label"><?php echo "Mobile"; ?></label>
            <div class="col-sm-10">
              <input type="text" name="customer_telephone" value="" class="customer_q_telephone">
            </div>
          </div>
          <div class="form-group col-sm-12">
            <label class="col-sm-2 control-label"><?php echo "Email"; ?></label>
            <div class="col-sm-10">
              <input type="text" name="customer_email" value="" class="customer_q_email">
            </div>
          </div>
          <?php } ?>
          <!--<input type="hidden" name="user_id" value="<?php echo $customer_id; ?>" class="user_id_<?php echo $product['product_id'];?>">-->
          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_q_id_<?php echo $product_id;?>">
          <div class="form-group col-sm-12">
            <label class="col-sm-2 control-label"><?php echo "Your Question"; ?></label>
            <div class="col-sm-10">
              <textarea name="popup_q_question" class="popup_q_question_<?php echo $product_id;?>"></textarea>
            </div>
          </div>

        </div>
        <div class="popup-q-footer ">
          <button type="button" class="btn btn-default custom-btn-default question-btn" data-product-id="<?php echo $product_id;?>"><?php echo $comment_popup_send;?></button>
        </div>
      </div>
    </div>
    <!-- Ask a question popup ends here -->
    </div>

    <!--<div class="col-sm-12 money_back_guarantee">
      <h4>48 Hours return 100% money back guarantee </h4>
    </div>-->

    <?php
          if(isset($seller_returnable) && $seller_returnable != 0){
            if($is_single == 1){
    ?>
              <div class="col-sm-12 return_accepted">
                <h4><?php echo $text_no_return; ?></h4>
              </div>
    <?php
            }else{
     ?>
              <div class="col-sm-12 return_accepted">
                <h4><?php echo $text_no_return; ?></h4>
              </div>
    <?php   }
          }else{
              if($is_single == 1){
    ?>
              <div class="col-sm-12 return_accepted">
                <h4><?php echo $text_no_return; ?></h4>
              </div>
    <?php   }
         }
    ?>

    <?php if (isset($product['quantity']) && $product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
    <!-- Start Comment Popup -->
    <div class="comment_popup " id="comment_popup">
      <div class="content_popup ">
        <div class="popup-header msg_body">
          <button type="button" class="close" data-product-id="<?php echo $product['product_id'];?>">&times;</button>
          <h4 class="popup-title"><?php echo $comment_popup_heading;?></h4>
        </div>
        <div class="popup-body">
          <!--<input type="hidden" name="user_id" value="<?php echo $customer_id; ?>" class="user_id_<?php echo $product['product_id'];?>">-->
          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_id_<?php echo $product['product_id'];?>">
          <input type="hidden" name="product_status" value="<?php echo 'out of stock'; ?>" class="product_status_<?php echo $product['product_id'];?>">
          <textarea name="popup_comment" class="popup_comment_<?php echo $product['product_id'];?>"></textarea>
        </div>
        <div class="popup-footer ">
          <button type="button" class="btn btn-default custom-btn-default" data-product-id="<?php echo $product['product_id'];?>"><?php echo $comment_popup_send;?></button>
        </div>
      </div>
    </div>
    <!-- End Comment Popup -->
    <?php } ?>
  </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $("#landscape_caraousel").owlCarousel({
      navigation:true,
      navigationText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
      items: 4,
      pagination : false
    });
  });
</script>
<script type="text/javascript">
  // preload for product thumb
$(document).ready(function () {
  $.preload(
    <?php echo rtrim($preload_img_string,','); ?>
  );
});
</script>