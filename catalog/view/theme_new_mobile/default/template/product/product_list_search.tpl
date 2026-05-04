<?php
    $i = 0;
    foreach ($products as $product) {

    $productInfo[$product['product_id']] = $product['row_data'];

    ++$i;
    $css_class = 'left';
    if( $i == 2){
        $i = 0;
        $css_class = 'right';
    }
 ?>
<!--<div class="product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-5 item">-->
<div class="product-layout col-xs-6 col-sm-3 item <?php echo $css_class;?>">
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

  <?php
                    if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') {
                        $css_pofstock_class =  ' style="opacity:0.4;"';
                      } else {
                        $css_pofstock_class = '';
                      }
                ?>
        <div class="product-thumb " <?php echo $css_pofstock_class;?>>

           <div class="image">
           <?php if($hide_price == 0) { ?>
           <a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax">
           <?php } else { ?>
           <a>
           <?php } ?>

           <img src="<?php echo $product['thumb']; ?>" width="<?php echo $product['img_width']; ?>" height="<?php echo $product['img_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a>

            <?php
                /*------ start fill heart after user login ----*/
                $fill_heart = $this->customer->getWishlistIcon($product['product_id']);
                /*------ END fill heart after user login ----*/
            ?>

            <?php if($hide_price == 0) { ?>
            <div class="button_add_wishlist" data-product-id="<?php echo $product['product_id']; ?>">
                <button type="button" class="addtowishlist" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class=" <?php echo $fill_heart;?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
            </div>
            <?php } ?>

        </div>
        <div>
        <div class="caption">
            <!-- show MRP -->
            <?php if($hide_price == 0)
            { ?>
            <?php  if($product['mrp'] > $product['unformatted_price']) { ?>
              <div class="saving_money_margin">
                <?php if($product['mrp'] != 0) { ?>
                  <p>
                    Your Margin : <?php echo $product['margin_percentage'] ?>%
                  </p>
                <?php } ?> 
              </div>
            <?php } ?>
           <?php } ?> 

           <?php if($hide_price == 0)
            { ?>
          <h4><a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax"><?php echo mb_strimwidth($product['name'], 0, 65, "..."); ?></a></h4>
          <?php } else { ?>
           <h4><a data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax"><?php echo mb_strimwidth($product['name'], 0, 65, "..."); ?></a></h4>
          <?php } ?>

            <?php if($product['rating'] > 0){ ?>
                <div class="rating">
                    <p>
                        <?php /* for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($product['rating'] < $i) { ?>
                        <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } else { ?>
                        <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } ?>
                        <?php } */?>
                        
                        <!-- Added by Amarat (07-july-2017) -->
                        <?php 
                            if ($product['rating'] == 1 || $product['rating'] == 2 || $product['rating'] == 3) { 
                                $rating_class = 'danger';
                                $product_rating = 'Average';
                            }elseif($product['rating'] == 4){
                                $rating_class = 'warning';
                                $product_rating = 'Good';
                            }elseif($product['rating'] == 5){
                                $rating_class = 'success';
                                $product_rating = 'Excellent';
                            }else{
                                $rating_class = '';
                                $product_rating = '';
                            }
                        ?>
                        <span class="label rating_span label-<?php echo $rating_class ?>"><?php echo $product_rating ?> Quality</span>
                        
                        
                        
                        <br/>
                    </p>
                </div>
            <?php } ?>
            <?php if($product['previously_ordered'] == 1){ ?>
            <p class="previously_ordered"><?php echo '( '. $text_previously_ordered . ' )'; ?></p>
            <?php }?>

          <?php
           if($hide_price == 0) {
           if ($product['price']) { ?>
          <p class="price">
            <?php if (!$product['special']) { ?>
            <span class="price-new"><?php echo $product['price']; ?></span><?php echo $text_per_piece; ?>
            <?php } else { ?>
            <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span> <?php echo $text_per_piece; ?>
            <?php } ?>
            <?php /* if ($product['tax']) { ?>
            <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
            <?php } */ ?>
          </p>
          <?php } } ?>

          <?php
            if($product['is_single'] == '1'){

            }else{ ?>
            <?php  if ($product['minimum']) { ?>
                <span class="min-moq">
                  <?php if ($product['minimum'] > 1) { ?>
                  <?php echo $text_moq_pre . $product['minimum'] . $text_moq_post; ?>
                  <?php } else { ?>
                  <?php echo $text_moq_default; ?>
                  <?php } ?>
                </span>
          <?php } else { ?>
          <p>
            <?php echo $text_moq_default; ?>
          </p>
          <?php } ?>
          <?php if(isset($product['exp_dispatch_date']) && $product['exp_dispatch_date'] > 1) { ?>
              <div class="exp_dispatch_date offer-block"><i aria-hidden="true" class="fa fa-tag"></i> <?php echo $text_available_after; ?> <?php echo $product['exp_dispatch_date']; ?> <?php echo $text_days; ?></div>
          <?php } ?>
          <p><?php echo $product['set_description']; ?></p>
          <?php /* ?>
          <div class="stock_circle_list <?php echo $bgcolor; ?>"><div><?php echo $instock; ?><span class="soldout"><?php echo $text_sold_out;?></span></div></div>
          <?php */ ?>
          <?php } ?>
            <?php if(isset($product['cod_available']) && $product['cod_available']==0){ ?>
            <span class="not_available_cod"><?php echo $text_cod_available; ?></span>
            <?php }?>
        </div>
          <div class="button-group">
          <?php if($hide_price == 0) { ?>
              <?php
                    if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
                        <button type="button" class="addtocart comment_btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $i_want_this_design; ?></span></button>
              <?php }else{ ?>
                        <button data-gaid="<?php echo $product['cart_tracking_id_for_ga']; ?>" type="button" class="addtocart" data-product-id="<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
              <?php } ?>
               <?php } ?>
            <!--<button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>-->
          </div>
        </div>
  </div>
    <?php if (isset($product['quantity']) && $product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
        <!-- Start Comment Popup -->
        <div class="comment_popup" id="comment_popup_<?php echo $product['product_id'];?>">
            <div class="content_popup">
                <div class="popup-header msg_body_<?php echo $product['product_id'];?>">
                    <button type="button" class="close" data-product-id="<?php echo $product['product_id'];?>">&times;</button>
                    <h4 class="popup-title"><?php echo $comment_popup_heading;?></h4>
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
<?php } ?>



<!-- Start Comment Popup -->
<script>

    var productInfoJson = $.parseJSON(JSON.stringify(<?php echo json_encode($productInfo); ?>));

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

    $('.button_add_wishlist').click(function(){
        $('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
    });

    $(document).ready(function() {
        $(".productInfoAjax").each(function () {

            var hrefA = $(this).attr('href');
            var popup_string = '&';
            var find_query_string = hrefA.indexOf("?");
            if (find_query_string > 0) {
                popup_string = '&';
            }
            hrefA = hrefA + popup_string + 'popup=true';

            var getProductId = $(this).attr('data-product-id');
            var getInfo = "";

            $.each(productInfoJson, function (key, data) {
                if (getProductId == key)
                    getInfo = data;
            });

            $(this).fancybox({
                type: "ajax",
                beforeLoad: function () {
                    this.ajax.data = getInfo;
                    this.ajax.type = 'POST';
                },
                maxWidth: 768,
                width: '90%',
                padding: 0,
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                },
                'hideOnContentClick': true,
                'href': hrefA,
                centerOnScroll: true,
                autoScale: true,
                openEffect: 'none',
                closeEffect: 'none',
                afterShow: function () {
                    $('.zoomContainer').remove();

                    var owl = $("#main_img_container");
                    owl.owlCarousel({
                        itemsMobile : [768, 1],
                        autoPlay : false,
                        lazyLoad : true,
                        singleItem: true,
                        items: 1,
                    });
                },
                afterClose: function () {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                }
            });
        });
        $(document).on('click', '.productRelatedAjax', function (e) {
            e.preventDefault();
            var postUrl = $(this).attr('href');
            var getRelatedProductId = $(this).attr('data-product-id');
            var getInfo = "";

            $.each(productRelatedJson, function (key, data) {
                if (getRelatedProductId == key)
                    getRelatedInfo = data;
            });

            $.fancybox({
                type: "ajax",
                beforeLoad: function () {
                    this.ajax.data = getRelatedInfo;
                    this.ajax.type = 'POST';
                },
                maxWidth: 768,
                width: '90%',
                padding: 0,
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                },
                'hideOnContentClick': true,
                'href': postUrl,
                centerOnScroll: true,
                autoScale: true,
                openEffect: 'none',
                closeEffect: 'none',
                afterShow: function () {
                    $('.zoomContainer').remove();

                    var owl = $("#main_img_container");
                    owl.owlCarousel({
                        itemsMobile : [768, 1],
                        autoPlay : false,
                        lazyLoad : true,
                        singleItem: true,
                        items: 1,
                    });
                },
                afterClose: function () {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                }
            });

        });
    });

</script>
<!-- End Comment Popup -->
<script  src="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js" type="text/javascript"></script>