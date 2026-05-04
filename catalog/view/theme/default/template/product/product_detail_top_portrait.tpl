<div class="product-detail home_width_adjustment">
  <div class="row product_img_detail">
    <div class="col-sm-1 vetical-image-offset-detail">
      <?php if ($thumb || $images) { ?>
        <div class=" pull-right">
            <?php
              if ($images) {
                if(count($images)>3){
            ?><a href="#" id="prev3">
            <img src="image/product_verticel_top.png" data-slide="next" class="btn-vertical-slider glyphicon glyphicon-circle-arrow-up"></a>
            <?php } ?>

                <div id="gallery_01"  class="slideshow vertical"
                     data-cycle-fx=carousel
                     data-cycle-timeout=0
                     data-cycle-next="#next3"
                     data-cycle-prev="#prev3"
                     data-cycle-slides="> a"
                     data-cycle-carousel-visible=3
                     data-cycle-carousel-vertical=true data-allow-wrap=false >

                  <?php
                     $i=0;
                       $preload_img_string = '';
                       foreach ($images as $image) {
                        if($i==0) {
                          $active  = 'active';
                        }else{
                          $active='';
                        }
                     $i++;
                     $preload_img_string .= "'".$image['popup']."','".$image['pan_detail']."',";
                  ?>
                  <a class=" getcurrentImg-detail <?php echo $active; ?> detail-page-thumb" rel="group" href="#" data-image="<?php echo $image['pan_detail'];?>" data-zoom-image="<?php echo $image['popup'];?>" title="<?php echo $heading_title;?>" >
                      <img src="<?php echo $image['thumb']; ?>" width="<?php echo $image['thumb_img_width']; ?>" height="<?php echo $image['thumb_img_height']; ?>" title="<?php echo $heading_title;?>" alt="<?php echo $heading_title;?>" />
                  </a>

                <?php }
                    $preload_img_string = substr($preload_img_string ,0,-1);
                ?>
                </div>
            <?php  if(count($images)>3){ ?> <a href="#" id="next3">
            <img  src="image/product_verticel_bottom.png" data-slide="prev" class="btn-vertical-slider glyphicon glyphicon-circle-arrow-down"></a>
            <?php }?>
            <?php } ?>

        </div>
      <?php } ?>
    </div>
    <div class="col-sm-4 col-xs-11 main-image mainImgToDown nopadding" style="height: 432px">

        <?php if ($thumb) {
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

        <?php if (!empty($percent_discount)) { ?>
        <div class="discount-arrow"><?php echo $percent_discount; ?>% Discount</div>
        <?php } ?>
        <a class="thumbnail panzoom-parent" href="javascript:void(0);" title="<?php echo $heading_title; ?>">
          <img id="img_zoom" src="<?php echo $pan_detail;?>" data-zoom-image="<?php echo $popup;?>" width="<?php echo $popup_img_width ?>" height="<?php echo $popup_img_height ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>"/>
        </a>
      <!--<span class="downloadImages col-md-9 col-xs-12 col-sm-8 pull-right main-image"><?php echo '<a href="'.$downloadImages.'">'.$downloadText.'</a>'; ?></span>-->
      <?php if(isset($exp_final_date) && $exp_final_date > 1) { ?>
        <div class="available_after_days offer-block"><i aria-hidden="true" class="fa fa-tag"></i> <?php echo $text_available_after; ?> <?php echo $exp_final_date; ?> <?php echo $text_days; ?></div>
      <?php } ?>
    
      <?php } ?>
    </div>
    <div class="col-sm-7 col-xs-12 nopadding product_details_popup" id="product_details_popup">
        <section class="category_head1">
          <ul class="breadcrumb" vocab="http://schema.org/" typeof="BreadcrumbList">
            <?php $c = 1; ?>
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
              <li property="itemListElement" typeof="ListItem">
                <a href="<?php echo $breadcrumb['href']; ?>" property="item" typeof="WebPage"><span  property="name"><?php echo $breadcrumb['text']; ?></span></a>
                <meta property="position" content="<?php echo $c; ?>">
              </li>
            <?php $c++; ?>
            <?php } ?>
          </ul>
        </section>
        <div class="col-sm-12">
          <h1 class="product_detail_popup_heading"><a><?php echo $heading_title; ?></a></h1>
        </div>
        <div class="col-sm-7">
          <div class="col-sm-10 nopadding">
              <ul class="list-unstyled">
                <?php if ($manufacturer) { ?>
                <li><?php echo $text_manufacturer; ?> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></li>
                <?php } ?>
                <?php if($previously_ordered == 1){ ?>
                <li class="product_code"><?php echo $text_model; ?> <?php echo $model; ?> &nbsp;&nbsp;&nbsp;</li>
                <br/>
                <li class="previously_ordered"><?php echo '( ' . $text_previously_ordered . ' ) '; ?></li>
                <?php }else{ ?>
                  <li><?php echo $text_model; ?> <?php echo $model; ?> &nbsp;&nbsp;&nbsp;</li>
                <?php } ?>

                  <!-- set-descreption srart -->
                <?php

                <!-- <li><h4><?php echo $stock; ?></li></h4> -->
              </ul>
            </div>
            <div class="col-sm-2">
                <?php
                /*------ start fill heart after user login ----*/
                $fill_heart = $this->customer->getWishlistIcon($product_id);
                /*------ END fill heart after user login ----*/
                ?>
              <div class="button_add_wishlist" data-product-id="<?php echo $product_id; ?>">
                <button type="button" id="wishlist_<?php echo $product_id; ?>" data-toggle="tooltip" class="addtowishlist" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>',event);">
                  <i class="<?php echo $fill_heart; ?>" id="wishlist_heart_<?php echo $product_id; ?>"></i>
                </button>
              </div>
            </div>
            <div class="col-sm-12 nopadding">
              <?php if($rating > 0){ ?>
                <div class="rating">
                  <!--<p>
                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <?php if ($rating < $i) { ?>
                    <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                    <?php } else { ?>
                    <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                    <?php } ?>
                    <?php } ?><br/>
                  </p>-->

                  <?php if($rating == 5){
                     echo "<span class='label label-success' style='padding:5px; margin:3px 0px; font-size: 13px;'>Excellent Quality</span>";
                  } else if($rating == 4){
                  echo "<span class='label label-warning' style='padding:5px; margin:3px 0px; font-size: 13px;'>Good Quality</span>";
                  } else if($rating <= 3){
                  echo "<span class='label label-danger' style='padding:5px; margin:3px 0px; font-size: 13px;'>Average Quality</span>";
                  } else {
                  // do nothing
                  }?>
                </div>
              <?php } ?>
            </div>
            <div class="col-sm-12 product_set_description">
              <?php if ($minimum > 1) { ?>
              <span class="label label-minimum-order"><?php echo $text_moq_pre . $minimum . $text_moq_post; ?></span>
              <?php } else { ?>
              <span class="label label-minimum-order"><?php echo $text_moq_default; ?></span>
              <?php } ?>
              <p>
               <?php echo $set_description; ?>
              </p>
              <a class="more_desc" href="javascript:void(0);">More description</a>
            </div>
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
                  <?php } ?>
                </table>
              </div>
            </div>
            <?php } ?>
            
            <!-- set-descreption end -->
            <!-- sharing option -->
            <div class="share-heading">Download image :</div>
            <div class="share-icons col-sm-12 nopadding">
              <!-- <div class="facebook_share col-sm-2"><a href="https://www.facebook.com"><i class="fa fa-facebook"></i></a></div>
              <div class="tweeter_share col-sm-2"><a href="https://twitter.com/"><i class="fa fa-twitter"></i></a></div>
              <div class="whatsapp_share col-sm-2"><a href="https://web.whatsapp.com/"><i class="fa fa-whatsapp"></i></a></div>
              <div class="instagram_share col-sm-2"><a href="https://www.instagram.com/"><i class="fa fa-instagram"></i></a></div> -->
              <div class="getImage downloadImages main-image col-sm-2"><a href="javascript:void(0);"><i class="fa fa-download"></i></a></div>
            </div>
            <div class="col-sm-12 nopadding">
              <?php if($cod_available == 0){ ?>
              <div class="col-sm-12 ask-a-question">Ask A Question</div>
              <div class="col-sm-12 cod-not-avaiable">
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
                  <button type="button" class="close" data-product-id="<?php echo $product_id; ?>">&times;
                  </button>
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
        </div>
        <div class="col-sm-5 rightsideproductdetail nopadding">

        <div class="col-sm-12 col-xs-12 product_detail">
          <div class="offer-block">           
            <div class="nopadding">
              <i class="fa fa-tag" aria-hidden="true"></i>
              2% on all prepaid orders.
            </div>
            <div class="nopadding">
              <i class="fa fa-tag" aria-hidden="true"></i>
              3% on Rs. 25,000 and above prepaid orders.
            </div>
          </div>
          
          <?php if($mrp > $unformatted_price ) { ?>
          <div class="col-sm-12 product-detail nopadding">
            <div class="saving_money">
              <p class="col-sm-6 nopadding"><span class="mrp_title">MRP:</span> <br /><strike><?php echo $format_mrp; ?></strike></p>
              <p class="col-sm-6"><spam class="mrp_title">Your Margin:</spam> <br /><?php echo $saving_money; ?></p>
            </div>
          </div>
          <?php } ?>

          <?php if ($selling_price) { ?>
            <div class="row">
              <?php if (!$special) { ?>          
              <div class="col-sm-12">
                <h3 itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="main_price">
                  <span id ="sell-price"  itemprop="price" content="<?php echo $price ?>"><?php echo $selling_price ?></span> <br/>
                  <span><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id and $text_tax_rate) { echo $text_tax_rate;} ?> </span>
                </h3>
                <input type= "hidden" id ="product_unit_price" value ="<?php echo floatval($unformatted_price); ?>" />
              </div>           
            </div>
            
              <?php if(!empty($custom_store_selling_price) && !empty($custom_store_product_href)){ ?>
              <div class="col-sm-12 store">
                <?php if($is_single== 0){ ?>
                  <div class="store_information">
                    <h2 class="store_title"><a href= "<?php echo $custom_store_product_href; ?>" ><?php echo $text_single;?></a></h2>
                    <p class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?>
                      </p>
                    <span class="store_tax"><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id and $text_custom_tax_rate) { echo $text_custom_tax_rate;} ?></span>
                  </div>
                <?php }else{ ?>
                  <div class="store_information">
                    <h2 class="store_title"><a href="<?php echo $custom_store_product_href; ?>" ><?php echo $text_wholesaleBox;?></a></h2>
                    <p class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?></p>
                    <span class="store_tax"><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id and $text_custom_tax_rate) { echo $text_custom_tax_rate;} ?></span>
                  </div>
                <?php } ?>
              <?php }else{ ?>
                <?php if ($review_status) { ?>


                  <?php /* ?> <hr>
                  <!-- AddThis Button BEGIN -->
                  <div class="addthis_toolbox addthis_default_style">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a> <!-- <a class="addthis_button_pinterest_pinit"></a> <a class="addthis_counter addthis_pill_style"></a> --> 
                  </div>
                  <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-515eeaf54693130e"></script>
                  <!-- AddThis Button END -->
                  <?php */ ?>
            </div>
                <?php } ?>
                <?php }?>

           <?php 

              /* if($is_single == '1'){

                }else{ */ ?>
            <?php /* } */?>
            <?php } else { ?>
            <div class="col-sm-12">
              <span style="text-decoration: line-through;"><?php echo $selling_price . $text_per_piece; ?></span>
            </div>
            <div class="col-sm-12 special-price">
              <h3><?php echo $special; ?></h3>
              <span class="store_tax"><?php if ($tax_class_id and $text_tax_rate) { echo $text_withoutslash_piece  . $text_tax_rate; } ?></span>
            </div>
            <?php } ?>
            <?php /* if ($tax) { ?>
            <div class="col-sm-12"><?php echo $text_tax; ?> <?php echo $tax; ?></div>
            <?php } */ ?>
            <?php if ($points) { ?>
            <div class="col-sm-12"><?php echo $text_points; ?> <?php echo $points; ?></div>
            <?php } ?> 
          </div>
            <?php } ?>
            <?php 
            if(isset($options_tpl)){
              echo $options_tpl;
            }
            ?>
        </div>
            <!-- <div class="col-sm-12 details_heading">
              <div class="col-sm-11">
                <h1 class="product_detail_popup_heading"><?php echo $heading_title; ?></h1>
              </div>
              <div class="col-sm-1">
                
              </div>
            </div> -->      
    </div>

      <!--<div class="col-sm-12 money_back_guarantee">
        <h4>48 Hours return 100% money back guarantee </h4>
      </div>-->

      <?php if (isset($quantity) && $quantity <= 0) { ?>
      <!-- Start Comment Popup -->
      <div class="comment_popup " id="comment_popup_detail_<?php echo $product_id;?>" style="top:25%; left:15%; width: 50%;">
        <div class="content_popup ">
          <div class="popup-header msg_body">
            <button type="button" class="close" data-product-id="<?php echo $product_id;?>">&times;</button>
            <h4 class="popup-title"><?php echo $comment_popup_heading;?></h4>
          </div>
          <div class="popup-body">
            <!--<input type="hidden" name="user_id" value="<?php echo $customer_id; ?>" class="user_id_<?php echo $product['product_id'];?>">-->
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_id_<?php echo $product_id;?>">
            <input type="hidden" name="product_status" value="<?php echo 'out of stock'; ?>" class="product_status_<?php echo $product_id;?>">
            <textarea name="popup_comment" class="popup_comment_<?php echo $product_id;?>"></textarea>
          </div>
          <div class="popup-footer ">
            <button type="button" class="btn btn-default custom-btn-default" data-product-id="<?php echo $product_id;?>"><?php echo $comment_popup_send;?>
            </button>
          </div>
        </div>
      </div>
      <!-- End Comment Popup -->
      <?php } ?>
  </div>
</div>
<script type="text/javascript">
  // preload for product thumb
  $(document).ready(function () {
    $.preload(<?php echo $preload_img_string;?>);

    var getZoomDivWidth = ($("div#content").width() / 2) + 100;
     $('.zoomContainer').remove();
    //initiate the plugin and pass the id of the div containing gallery images
     /*$("#img_zoom").elevateZoom({
        gallery:'gallery_01',
        cursor: 'crosshair',
        galleryActiveClass: 'active',
        imageCrossfade: true,
        borderSize : 0,
        responsive : true,
        zoomWindowWidth   : getZoomDivWidth,
        zoomWindowHeight  : 400,
        zoomWindowOffetx  : 10,
        zoomWindowOffety  : -2
    });*/
      var zoomConfig = {
          cursor: 'crosshair',
          galleryActiveClass: 'active',
          imageCrossfade: true,
          borderSize : 0,
          responsive : true,
          zoomWindowWidth   : getZoomDivWidth,
          zoomWindowHeight  : 400,
          zoomWindowOffetx  : 10,
          zoomWindowOffety  : -2 };

      var image = $('#gallery_01 a');
      var zoomImage = $('img#img_zoom');

      zoomImage.elevateZoom(zoomConfig);//initialise zoom

      image.on('click', function(e){ e.preventDefault();
          // Remove old instance od EZ
          $('.zoomContainer').remove();
          zoomImage.removeData('elevateZoom');
          // Update source for images
          zoomImage.attr('src', $(this).data('image'));
          zoomImage.data('zoom-image', $(this).data('zoom-image'));
          // Reinitialize EZ
          zoomImage.elevateZoom(zoomConfig);
      });

  });

  $('.slideshow').cycle({
      fx: 'carousel',
      timeout: 0
  });

  $.fn.cycle.defaults.autoSelector = '.slideshow';
</script>
