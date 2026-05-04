<?php
$loop = 0;
foreach ($products as $product) { 
		$productInfo[$product['product_id']] = $product['row_data'];
?>

              <div class="product-layout product-grid col-lg-3 col-md-3 col-sm-3 col-xs-12"  id="product_thumb_<?php echo $product['product_id']  ?>">
              <div class="product-layout_box">
                <?php
                  $quantity = $product['quantity'];
                  $stock_status = $product['stock_status'];
                  $text_out_of_stock = $product['text_out_of_stock'];
                  $text_in_stock = $product['text_in_stock'];

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
                    echo '<div class="product-thumb " style="opacity:0.4;">'; }
                  else {
                    echo '<div class="product-thumb" data-product-id="'.$product['product_id'].'">'; }
                ?>
                  <div class="addtowishlist wishlist_btn_right" data-product-id="<?php echo $product['product_id']; ?>">
                  <button type="button" data-product-id="<?php echo $product['product_id']; ?>" class="addtowishlist" id="wishlist_<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>',event);"><i class=" <?php echo $product['fill_heart']; ?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
                  </div>

                <div class="image">
                    <?php if (!empty($product['percent_discount'])) { ?>
                    <div class="discount-arrow"><?php echo $product['percent_discount']; ?>% Discount</div>
                    <?php } ?>
                    <a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>"  class="fancybox productInfoAjax"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" width="<?php echo $product['image_width']; ?>" height="<?php echo $product['image_height']; ?>"/></a>
                </div>

                <div>
                  <div class="caption">
                      <?php  if($product['mrp'] > $product['unformatted_price']) { ?>
                      <div class="saving_money_margin">
                        <?php if($product['mrp'] != 0) { ?>
                          <p>
                            Your Margin : <?php echo  $product['margin_percentage']?>%
                          </p>
                        <?php } ?> 
                      </div>
                      <?php } ?>
                      <h4>
						<a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax ellipsis"><?php echo mb_strimwidth($product['name'], 0, 50, "..."); ?></a></h4>
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
                        
                      </div>
                      <div class="clear"></div>
                      <?php if($product['previously_ordered'] == 1){ ?>
                        <p class="previously_ordered"><?php echo '( ' . $text_previously_ordered . ' )'; ?></p>
                      <?php }?>
                    <!-- <?php // if ($product['rating']) { ?>
                    <div class="rating">
                      <?php // for ($i = 1; $i <= 5; $i++) { ?>
                      <?php // if ($product['rating'] < $i) { ?>
                      <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
                      <?php // } else { ?>
                      <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
                      <?php // } ?>
                      <?php // } ?>
                    </div>
                    <?php // } ?> -->

                <div class="product_description white">
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
                                <span><?php echo $opt_value['name']; ?></span>
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












                    
<!--                         <?php if($product['product_options']){ ?>
                        <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart_bottem btn checkoptionspop" html-data="<?php echo $product['product_id']; ?>" ><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                        <?php } else { ?>
                        <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart_bottem btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                        <?php } ?> -->
                   

                  </div>
                </div>
                </div>









<!-- Start hover description -->
            <div class="hover_description_new product-grid" id= "hover_description_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">
               
                

                     <div class="button_add_cart">
                      <a href="<?php echo $product['href']; ?>"  data-product-id="<?php echo $product['product_id']; ?>"  class="fancybox productInfoAjax ellipsis">
                      <button data-gaid="<?php echo $product['detailpopup_tracking_id_for_ga']; ?>" type="button" class="quickview_new btn"><i class="fa fa-eye"></i> <span class=""><?php echo $detail_view; ?></span></button>
                      </a>   
                    <?php if($product['product_options']){ ?>
                      <button  type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart_bottem btn checkoptionspop" html-data="<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                    <?php } else { ?>
                      <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart_bottem btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" data-product-id="<?php echo $product['product_id']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');" data-gaid="<?php echo $product['cart_tracking_id_for_ga']; ?>"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                      <?php } ?>

                    </div>
                     <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
<!--                       <?php if($product['product_options']){ ?>
                      <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart_bottem btn checkoptionspop" html-data="<?php echo $product['product_id']; ?>" ><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                      <?php } else { ?>
                      <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart_bottem btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                      <?php } ?> -->



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
                    <?php if($product['exp_dispatch_date'] > 1) { ?>
                        <p class="exp_dispatch_date"><i aria-hidden="true" class="fa fa-tag"></i><?php echo $text_available_after; ?> <?php echo $product['exp_dispatch_date']; ?> <?php echo $text_days; ?></p>
                    <?php } ?>
                  <?php /* ?>
                    <p><?php echo $product['set_description']; ?></p>
                  <?php */ ?>


                
              
            </div>


                <!-- End hover description -->
        <div class="hover_description_new product-grid hover_options_list" id= "option_view_description_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">

          <div class="row pull-right">
            <button class="btn cross-options" data-product-id="<?php echo $product['product_id']; ?>">
              <i class="fa fa-close inner-cross-list" style="" ></i>
            </button>
          </div>
          <?php
                 if($product['product_options']){
                  $opt_value = array();
          ?>
          <div>
              <?php foreach($product['product_options'] as $options){
                  if(isset($options['name']) && $options['name']=='color'){
              ?>
              <table>
                <tbody>
                  <span><?php echo "Color";?>
                    <?php foreach($options['product_option_value'] as $opt_value){ ?>
                        <?php if(isset($opt_value['quantity']) && $opt_value['quantity'] > 0){ ?>
                        <tr>
                          <td>
                            <span><?php echo $opt_value['name']; ?></span>
                          </td>
                          <td><input id="opt-input_<?php echo $opt_value['product_option_value_id']; ?>"
                                         type="text"
                                         value="0"
                                         name="opt_quantities[<?php echo $options['product_option_id']; ?>][<?php echo $opt_value['product_option_value_id']; ?>]"
                                         data-bts-min="0"
                                         data-bts-max="<?php echo $opt_value['quantity']; ?>"
                                         data-bts-init-val=""
                                         data-bts-step="1"
                                         data-bts-decimal="0"
                                         data-bts-step-interval="100"
                                         data-bts-force-step-divisibility="round"
                                         data-bts-step-interval-delay="500"
                                         data-bts-prefix=""
                                         data-bts-postfix=""
                                         data-bts-prefix-extra-class=""
                                         data-bts-postfix-extra-class=""
                                         data-bts-booster="true"
                                         data-bts-boostat="10"
                                         data-bts-max-boosted-step="false"
                                         data-bts-mousewheel="true"
                                         data-bts-button-down-class="btn btn-default height_adjusted_options"
                                         data-bts-button-up-class="btn btn-default height_adjusted_options"
                                         size="2"
                                         <?php
                                         if(!$option_value['quantity']) {
                                         echo "style='background:#f2f2f2' ";
                                         echo "readonly='readonly'";
                                         }?>
                                         class="input_qty  pull-left touchspin_qty"
                                  />
                          <td>

                          </td>
                        </tr>

                  <?php  } }?>
                </tbody>
               </table>
              <?php }
                  if(isset($options['name']) && $options['name']=='Size'){
              ?>
              <div class="row div-options-table-list"><?php echo "Size:";?></div>
              <div class="options-table-list-actual">
              <table width="100%" style="color:white;">
                <tbody>
                  <?php foreach($options['product_option_value'] as $opt_value){ ?>
                       <?php if($opt_value['quantity'] > 0){ ?>
                         <tr>
                           <td>
                             <?php echo $opt_value['name']; ?>
                           </td>
                           <td>
                           <input id="opt-input_<?php echo $opt_value['product_option_value_id']; ?>"
                                    type="text"
                                    value="0"
                                    name="option_quantities[<?php echo $options['product_option_id']; ?>][<?php echo $opt_value['product_option_value_id']; ?>]"
                                    data-bts-min="0"
                                    data-bts-max="<?php echo $opt_value['quantity']; ?>"
                                    data-bts-init-val=""
                                    data-bts-step="1"
                                    data-bts-decimal="0"
                                    data-bts-step-interval="100"
                                    data-bts-force-step-divisibility="round"
                                    data-bts-step-interval-delay="500"
                                    data-bts-prefix=""
                                    data-bts-postfix=""
                                    data-bts-prefix-extra-class=""
                                    data-bts-postfix-extra-class=""
                                    data-bts-booster="true"
                                    data-bts-boostat="10"
                                    data-bts-max-boosted-step="false"
                                    data-bts-mousewheel="true"
                                    data-bts-button-down-class="btn btn-default height_adjusted_options background_adjusted_options"
                                    data-bts-button-up-class="btn btn-default height_adjusted_options background_adjusted_options"
                                    size="2"
                                    <?php
                                    if(!$opt_value['quantity']) {
                                    echo "style='background:#f2f2f2' ";
                                    echo "readonly='readonly'";
                                    }?>
                                    class="input_qty  pull-left touchspin_qty input-options-list"
                                   />
                                   <input type="hidden" value="<?php echo $opt_value['quantity']; ?>" name="option_max_quantities[<?php echo $options['product_option_id']; ?>][<?php echo $opt_value['product_option_value_id']; ?>]" class="mq">
                                   <input type="hidden" class="pd" name="product_id" value="<?php echo $product['product_id']; ?>" />

                                   </td>


                         </tr>
                  <?php  } } ?>


                  </tbody>
                  </table>

                  </div>
                  <div class="form-group">
                      <div class="col-md-12 col-sm-12 col-xs-12 div-button-cart-list">

                          <button type="button"  class="button-cart-list btn btn-lg" data-loading-text="Loading...." data-product-id="<?php echo $product['product_id']; ?>" ><i class="fa fa-shopping-cart"></i> +CART</button>
                      </div>

                  </div>
              <?php    }?>
              <?php } ?>
          </div>
          <?php } ?>
        </div>



              </div>
    
            </div>


          <?php 
            $loop++;
            if ($loop == 4) {
              echo '<div class="clearfix"></div>';
              $loop = 0;
            }
          } ?>

 <!-- Start Script for hover description -->
 <script type="text/javascript">
   $(document).ready(function(){
	
	var productInfoJson = <?php echo json_encode($productInfo); ?>;	
	productInfoJson = $.parseJSON(JSON.stringify(productInfoJson));

    $(".input_qty").TouchSpin({ });

     $('.product-thumb, .hover_description_new').mouseover(function(){
       //alert($( this ).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).show();
     }).mouseleave(function(){
       //alert($( this ).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).hide();
     });

       $('.button_add_wishlist').click(function(){
           $('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
       });

       $('.checkoptionspop').click(function () {
        $('#hover_description_'+$(this).attr('html-data')).css('opacity', '0');
         $('#option_view_description_'+$(this).attr('html-data')).show('slide', {direction: 'left'}, 200);
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

   $('.cross-options').click(function () {
     $('#option_view_description_'+ $(this).attr('data-product-id')).hide();
     $('#hover_description_'+$(this).attr('data-product-id')).css('opacity', '1');
   });

 </script>
 <script type="text/javascript">
   $('.button-cart-list').on('click', function() {

  if(getCookie("customer_mobile") == '')
        {
          $("input[name=redirect_cart]").val(1);
            $('#login_verify_popup').modal('show');
        }
    else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
       {
         $("input[name=redirect_cart]").val(1);
         $('#login_popup').modal('show');
       }
    else
    {
       
    var pid = $(this).attr('data-product-id');
    var element = '#option_view_description_'+$(this).attr('data-product-id');
     $.ajax({
       url: 'index.php?route=checkout/cart/addWithOptions&popup=true&list=true',
       type: 'post',
       data: $( element + ' input[type=\'text\'],'+ element+' input.pd,'+ element+ ' input.mq' ),
       // data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product select, #product textarea, #product #opt-input'),
       dataType: 'json',
       beforeSend: function() {
         $('#button-cart').button('loading');
       },
       complete: function() {
         $('#button-cart').button('reset');
       },
       success: function(json) {
         $('.alert, .text-danger').remove();
         $('.form-group').removeClass('has-error');
         // $('span').removeClass('remove-red');
         $('span.qty_span_hid').hide();
         $('.input_qty').removeClass("make_it_red");

         if (json['error']) {
           if (json['error']['option']) {
             for (i in json['error']['option']) {
               var element = $('#input-option' + i.replace('_', '-'));
               //alert(element.parent().hasClass('input-group'));
               if (element.parent().hasClass('input-group')) {

                 element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
               } else {
                 element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
               }
             }
           }
           if (json['error']['option_value']) {
             for (product_option_id in json['error']['option_value']) {
               for (j in json['error']['option_value'][product_option_id]) {
                 var element = $('#option-input_' + j);
                 var element_span = $('#span_input_' + j);
                 element.addClass("make_it_red");
                 element_span.css("display","block");
                 element_span.addClass("remove-red");
               }

             }
           }


           if (json['error']['recurring']) {
             $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
           }

           // Highlight any found errors
           $('.text-danger').parent().addClass('has-error');
         }

         if (json['success']) {
           element = $('#hover_description_'+pid);
           element_img = $('#product_thumb_'+ pid).find("img").eq(0);
           cart_add_animate(element_img);


           $('#cart > button#cart_btn', window.parent.document).html('<i class="fa fa-shopping-cart"></i><span id="cart-total-desktop"> Cart <span class="cart_number">' + json['total_in_cart'] + '</span></span>');
           $('#cart > button#cart_btn_mobile', window.parent.document).html('<i class="fa fa-shopping-cart"></i> <span id="cart-total">' + json['total_in_cart']+'</span>');

           $('#cart > ul').load('index.php?route=common/cart/info ul li');
         }
       }
     });
   }
   });

 </script>
 <!-- End Script for hover description -->
<script src="catalog/view/theme/default/javascript/jquery.elevatezoom.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.preload.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.cycle2.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.cycle2.carousel.min.js" type="text/javascript"></script>
