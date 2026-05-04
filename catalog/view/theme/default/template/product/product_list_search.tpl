<?php
$loop = 0;
foreach ($products as $product) {
            $productInfo[$product['product_id']] = $product['row_data'];
?>
              <div class="product-layout product-grid col-lg-3 col-md-3 col-sm-3 col-xs-12">
              <div class="product-layout_box">
                <?php
                  $quantity = $product['quantity'];
                  $stock_status = $product['stock_status'];
                  $text_out_of_stock = $product['text_out_of_stock'];
                  $text_in_stock = $product['text_in_stock'];
                  $text_sold_out = $product['text_sold_out'];

                  if($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock'){
                    $bgcolor = 'red';
                    $instock = '<span class="in_stock">'.$text_out_of_stock.'</span> ';
                  }else{
                    $bgcolor = 'green';
                    if($product['is_single'] == '1'){
                      //$instock = '<span class="in_stock">'.$product['items_in_stock'].'</span>';
                      $instock = '';
                    }else{
                      $instock = '<span class="in_stock">'.$text_in_stock.'</span>';
                    }
                  }
                ?>
                <?php /* if($product['is_single'] == '1'){ ?>
                 <span class="singleStore"><?php echo $single_item;?></span>
                <?php } */ ?>
                <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') {
                    echo '<div class="product-thumb " data-product-id="'.$product['product_id'].'" style="opacity:0.4;">'; }
                  else {
                    echo '<div class="product-thumb" data-product-id="'.$product['product_id'].'">'; }
                ?>
                  <div class="addtowishlist wishlist_btn_right" data-product-id="<?php echo $product['product_id']; ?>">
                  <button type="button" class="addtowishlist" id="wishlist_<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>',event);"><i class=" <?php echo $fill_heart;?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
                  </div>

                <div class="image">
                  <?php if (!empty($product['percent_discount'])) { ?>
                  <div class="discount-arrow"><?php echo $product['percent_discount']; ?>% Discount</div>
                  <?php } ?>
                  <a class="fancybox productInfoAjax" data-product-id="<?php echo $product['product_id']; ?>"  href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" width="<?php echo $product['img_width']; ?>" height="<?php echo $product['img_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a>
                </div>
  
                <div>
                  <div class="caption">
                  <?php  if($product['mrp'] > $product['unformatted_price']) { ?>
                      <div class="saving_money_margin">
                        <?php if($product['mrp'] != 0) { ?>
                          <p>
                            Your Margin : <?php echo $product['margin_percentage'] ?>%
                          </p>
                        <?php } ?> 
                      </div>
                      <?php } ?>
                    <h4><a class="fancybox productInfoAjax" data-product-id="<?php echo $product['product_id']; ?>" href="<?php echo $product['href']; ?>"><?php echo mb_strimwidth($product['name'], 0, 50, "..."); ?></a></h4>

                                        <?php if($product['previously_ordered'] == 1){ ?>
                      <p class="previously_ordered"><?php echo '( '. $text_previously_ordered . ' )'; ?></p>
                    <?php }?>

                    <?php if ($product['price']) { ?>
                      <div class="price_box">
                        <?php if (!$product['special']) { ?>
                          <span class="price-weight"><?php echo $product['price']; ?></span><?php echo $text_per_piece; ?>
                        <?php } else { ?>
                          <span class="price-weight"><?php echo $product['special']; ?></span><br> <span class="price-old"><?php echo $product['price']; ?></span> <?php echo $text_per_piece; ?>
                        <?php } ?>
                        <?php /* if ($product['tax']) { ?>
                        <br><span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                        <?php } */ ?>
                      </div>
                    <?php } ?>
                    <div class="display">


                    <?php if($product['rating'] > 0){ ?>
                    <div class="rating">

                        <!--<?php for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($product['rating'] < $i) { ?>
                        <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } else { ?>
                        <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } ?>
                        <?php } ?>-->
                          <?php if($product['rating'] == 5){
                                echo "<span class='label label-success' style='padding:3px; margin:3px 0px;'>Excellent Quality</span>";
                          } else if($product['rating'] == 4){
                          echo "<span class='label label-warning' style='padding:3px; margin:3px 0px;'>Good Quality</span>";
                          } else if($product['rating'] <= 3){
                          echo "<span class='label label-danger' style='padding:3px; margin:3px 0px;'>Average Quality</span>";
                          } else {
                          // do nothing
                          }?>


                    </div>
                    <?php } ?>
                    <div class="clear"></div>
                    </div>
                    <div class="clear"></div>

                 <div class="product_description">
                                <span class="product_set_description color_box">
                                    <?php echo $product['set_description'];?>
                                </span>
                 </div>
                 <!-- Start Product Options(Size)  -->
                 <?php 
                                   if($product['product_options']){
                                    $opt_value = array();
                            ?>
                 <div class="product_options">
                   <?php foreach($product['product_options'] as $options){
                                    if(isset($options['name']) && $options['name']=='color'){
                                ?>
                                        <span class='title_color'><?php echo "Color:";?>
                                          <?php foreach($options['product_option_value'] as $opt_value){ ?>
                                          <?php if(isset($opt_value['quantity']) && $opt_value['quantity'] > 0){ ?>
                                            <span style='background:<?php echo $opt_value['name']; ?>'></span>
                   <?php  } }?>
                   </span>
                   <?php }
                                    if(isset($options['name']) && $options['name']=='Size'){
                                ?>
                                        <span class='title_size'><?php echo "Size:";?>
                                          <?php foreach($options['product_option_value'] as $opt_value){ ?>
                                          <?php if($opt_value['quantity'] > 0){ ?>
                                           <span><?php echo $opt_value['name']; ?></span>
                                          <?php  } } ?>
                                        </span>
                   <?php    }?>
                   <?php } ?>
                 </div>
                 <?php } ?>
                 <!-- End Product Options(Size)  -->







                    <?php
                    if($product['is_single'] == '1'){

                    }else{ ?>
                    
                      <!-- <div class="stock_circle_list <?php echo $bgcolor; ?>"><div><?php echo $instock; ?><span class="soldout"><?php echo $text_sold_out;?></span></div></div>-->
                    <?php } ?>
                  </div>
                </div>


               <!-- Start hover description -->
               <div class="hover_description_new product-grid" id= "hover_description_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" >
          
                   <?php
                      /*------ start fill heart after user login ----*/
                      $fill_heart = $this->customer->getWishlistIcon($product['product_id']);
                   /*------ END fill heart after user login ----*/
                   ?>
                  <div class="button_add_cart">

                      <a href="<?php echo $product['href']; ?>" class="fancybox productInfoAjax ellipsis">
                      <button data-gaid="<?php echo $product['detailpopup_tracking_id_for_ga']; ?>" type="button" class="quickview_new btn"><i class="fa fa-eye"></i> <span class=""><?php echo $detail_view; ?></span></button>
                      </a>
                     <?php
                        if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
                            <button type="button"  class="addtocart_bottem btn comment_btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $i_want_this_design; ?></span></button>
                     <?php }else{ ?>
                            <button type="button"  class="addtocart_bottem btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');" data-product-id="<?php echo $product['product_id']; ?>" data-gaid="<?php echo $product['cart_tracking_id_for_ga']; ?>"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                     <?php } ?>
                     </div>
                      <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
                      <div class="out-of-stock_new">Out Of Stock!!!</div>
                      <?php }else{ ?><?php } ?>
                    <?php if ($product['minimum']) { ?>
                        <span class="min-moq_new">
                          <?php if ($product['minimum'] > 1) { ?>
                            <?php echo $text_moq_pre . $product['minimum'] . $text_moq_post; ?>
                          <?php } else { ?>
                            <?php echo $text_moq_default; ?>
                          <?php } ?>
                         <?php if(isset($product['cod_available']) && $product['cod_available']==0){ ?>
                            <label class="not_available_cod_new"></label>
                          <?php }?>
                        </span>
                      <?php } else { ?>
                        <p>
                          <?php echo $text_moq_default; ?>
                        </p>
                      <?php } ?>

                      <?php /* ?>
                      <p><?php //echo $product['set_description']; ?></p>
                      <?php */ ?>
                      <?php if(isset($product['exp_dispatch_date']) && $product['exp_dispatch_date'] > 1) { ?>
                      <div class="exp_dispatch_date offer-block"><i aria-hidden="true" class="fa fa-tag"></i> <?php echo $text_available_after; ?> <?php echo $product['exp_dispatch_date']; ?> <?php echo $text_days; ?></div>
                      <?php } ?>

                
               </div>
               <!-- End hover description -->
                </div>
              </div>

                <?php if (isset($product['quantity']) && $product['quantity'] <= 0 ) { ?>
                <!-- Start Comment Popup -->
                  <div class="comment_popup " id="comment_popup_<?php echo $product['product_id'];?>">
                    <div class="content_popup ">
                      <div class="popup-header msg_body_<?php echo $product['product_id'];?>">
                        <button type="button" class="close" data-product-id="<?php echo $product['product_id'];?>">&times;</button>
                        <h4 class="popup-title"><?php echo $comment_popup_heading; ?></h4>
                      </div>
                      <div class="popup-body">
                        <!--<input type="hidden" name="user_id" value="<?php echo $customer_id; ?>" class="user_id_<?php echo $product['product_id'];?>">-->
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" class="product_id_<?php echo $product['product_id'];?>">
                        <input type="hidden" name="product_status" value="<?php echo 'out of stock'; ?>" class="product_status_<?php echo $product['product_id'];?>">
                        <textarea name="popup_comment" class="popup_comment_<?php echo $product['product_id'];?>"></textarea>
                      </div>
                      <div class="popup-footer ">
                        <button type="button" class="btn btn-default" data-product-id="<?php echo $product['product_id'];?>"><?php echo $comment_popup_send;?></button>
                      </div>
                    </div>
                  </div>
                <!-- End Comment Popup -->
                <?php } ?>
            </div>

            <?php 
            $loop++;
            if ($loop == 4) {
              echo '<div class="clearfix"></div>';
              $loop = 0;
            }
          } ?>

 <!-- Start Script for hover description -->
 <div id="results"></div>

 <script type="text/javascript">
   $(document).ready(function(){

      <?php $productInfo = isset($productInfo)?$productInfo:''; ?>
       var productInfoJson = <?php echo json_encode($productInfo); ?>;
       productInfoJson = $.parseJSON(JSON.stringify(productInfoJson));

       $('.product-thumb, .hover_description').mouseover(function(){
       //alert($( this ).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).show();
     }).mouseleave(function(){
       //alert($( this ).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).hide();
     });

     $('.button_add_wishlist').click(function(){
       $('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
     });





       $(".productInfoAjax").each(function() {

           var hrefA = $(this).attr('href');
           var popup_string = '&';
           var find_query_string = hrefA.indexOf("?");
           if(find_query_string > 0) {
               popup_string = '&';
           }
           hrefA = hrefA+popup_string+'popup=true';

           var getProductId = $(this).attr('data-product-id');
           var getInfo = "";

           $.each(productInfoJson, function (key, data) {
               if(getProductId == key)
                   getInfo = data;
           });

           $(this).fancybox({
               type: "ajax",
               beforeLoad: function() {
                   this.ajax.data = getInfo;
                   this.ajax.type = 'POST';
               },
               maxWidth: 1200,
               width:'90%',
               padding:0,
               'hideOnContentClick': true,
               'href' : hrefA,
               openEffect	: 'none',
               closeEffect	: 'none',

               afterShow: function() {
                   $('.zoomContainer').remove();

                   $(".more_desc").click(function() {
                       $(".fancybox-inner").animate({
                           scrollTop: $(".show_description").offset().top
                       }, 1500);
                   });
               },
               afterClose: function() {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                    $('.addedincart_popup_close').trigger('click');
               }
           });
       });

       $(document).on('click', '.productRelatedAjax', function(e){
            e.preventDefault();
            var postUrl = $(this).attr('href');
            var getRelatedProductId = $(this).attr('data-product-id');
            var getInfo = "";

            $.each(productRelatedJson, function (key, data) {
               if(getRelatedProductId == key)
                   getRelatedInfo = data;
            });

           $.fancybox({
               type: "ajax",
               beforeLoad: function() {
                   this.ajax.data = getRelatedInfo;
                   this.ajax.type = 'POST';
               },
               maxWidth: 1200,
               width:'90%',
               padding:0,
               'href' : postUrl,
               openEffect	: 'none',
               closeEffect	: 'none',
               afterShow: function() {
                   $('.zoomContainer').remove();

                   $(".more_desc").click(function() {
                       $(".fancybox-inner").animate({
                           scrollTop: $(".show_description").offset().top
                       }, 1500);
                   });
               },
               afterClose: function() {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                    $('.addedincart_popup_close').trigger('click');
               }
           });


       });


   });

 </script>
 <!-- End Script for hover description -->

 <!-- Start Comment Popup -->
 <script>
   $('.comment_btn').click(function(){
     //alert($(this).attr('data-product-id'));
     <?php if ($logged) { ?>
       $('.content_popup .popup-body,.content_popup .popup-footer ').show();
       $('.alert-success').hide();
       $('.popup-body textarea').val('');
       $('.comment_popup').hide();
       $('#comment_popup_' + $(this).attr('data-product-id')).show();
     <?php }else{ ?>
       location = 'index.php?route=account/login';
     <?php } ?>
   });

   $('.popup-header button.close').click(function(){
     $('#comment_popup_'+$(this).attr('data-product-id')).hide();
   });


    $('.popup-footer .btn-default').click(function(){
      //var user_id = $('.user_id_'+$(this).attr('data-product-id')).val();
      var product_id = $('.product_id_'+$(this).attr('data-product-id')).val();
      var product_status = $('.product_status_'+$(this).attr('data-product-id')).val();
      var popup_comment = $('.popup_comment_'+$(this).attr('data-product-id')).val();

      $.ajax({
        type : "POST",
        url  : 'index.php?route=product/search/user_comment',
        data : {product_id:product_id,product_status:product_status,popup_comment:popup_comment},
        beforeSend: function() {
          $('.popup-footer .btn-default').button('loading');
        },
        complete: function() {
          $('.popup-footer .btn-default').button('reset');
        },
        success: function(data){
          $('.content_popup .popup-body,.content_popup .popup-footer ').hide();
          $('.msg_body_'+data['product_id']).after('<div class="alert alert-success"><i class="fa fa-check-circle"></i>'+data['message']+'</div>');
        }
      });
    });
 </script>
 <!-- End Comment Popup -->
<script  src="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js" type="text/javascript"></script>
<script src="catalog/view/theme/default/javascript/jquery.elevatezoom.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.preload.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.cycle2.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.cycle2.carousel.min.js" type="text/javascript"></script>
