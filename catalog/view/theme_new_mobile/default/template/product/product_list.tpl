<div class="row row-eq-height">
<?php
    $col = 0;
    foreach ($products as $product) {
    $productInfo[$product['product_id']] = $product['row_data'];

    $col++;
    $css_class = 'left';
    if( $col == 2){
        $col = 0;
        $css_class = 'right';

    }
 ?>

<?php
/************(04-01-2016) by vikas ****************************/
/**----- Start fill wishlist ---------- **/
    $fill_heart = $this->customer->getWishlistIcon($product['product_id']);
/**----- End fill wishlist ---------- **/
?>
<!--<div class="product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-5 item">-->
<div class="product-layout col-xs-6 col-sm-6 item <?php echo $css_class;?>">
    <?php
                  $quantity = $product['quantity'];
                  $stock_status = $product['stock_status'];
                  $text_out_of_stock = $product['text_out_of_stock'];
                  $text_in_stock = $product['text_in_stock'];
                  $singleCSS = 'wholesale_set_item';

                  if($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock'){
                    $bgcolor = 'red';
                    $instock = '<span class="in_stock">'.$text_out_of_stock.'</span> ';
                  }else{
                    $bgcolor = 'green';
                    if($product['is_single'] == '1'){
                        //$instock = '<span class="in_stock">'.$product['items_in_stock'].'</span>';
                        $instock = '';
                        $singleCSS = 'single_item';
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
    <div class="product-thumb  <?php echo $css_pofstock_class;?> <?php echo $singleCSS;?>">

    <div class="image">
        <?php if (!empty($product['percent_discount'])) { ?>
        <div class="discount-arrow"><?php echo $product['percent_discount']; ?>% Discount</div>
        <?php } ?>
        <a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax">
            <img width="<?php echo $product['image_width']; ?>" height="<?php echo $product['image_height']; ?>" src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
        </a>
        <button type="button" class="addtowishlist" data-wishlist-value = "<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="<?php echo $fill_heart ; ?>" id="wishlist_heart_<?php echo $product['product_id']?>"></i></button>
    </div>
    <div>
        <div class="caption">
        <?php
            if($product['is_single'] == '1'){

            }else{ ?>
              <?php  if($product['mrp'] > $product['unformatted_price']) { ?>
              <div class="saving_money_margin">
                <?php if($product['mrp'] != 0) { ?>
                  <p>
                    Your Margin : <?php echo  $product['margin_percentage']?>%
                  </p>
                <?php } ?> 
              </div>
        <?php } ?>
            <h4><a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax"><?php echo mb_strimwidth($product['name'], 0, 65, "..."); ?></a></h4>
            <?php if($product['rating'] > 0){ ?>
                <div class="rating">
                    <p>
                        <?php /*for ($i = 1; $i <= 5; $i++) { ?>
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
            <?php if ($product['price']) { ?>
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
            <?php } ?>

            <?php if ($product['minimum']) { ?>
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
            <p><?php echo $product['set_description']; ?></p>
            <?php } ?>

            <?php if(isset($product['cod_available']) && $product['cod_available']==0){ ?>
            <span class="not_available_cod"><?php echo $text_cod_available; ?></span>
            <?php }?>
            <?php if($product['exp_dispatch_date'] > 1) { ?>
                <p class="exp_dispatch_date"><i aria-hidden="true" class="fa fa-tag"></i><?php echo $text_available_after; ?> <?php echo $product['exp_dispatch_date']; ?> <?php echo $text_days; ?></p>
            <?php } ?>
        </div>
        <div class="btnaddtocart">
            <div class="button-group">
                <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart" data-product-id="<?php echo $product['product_id'];?>" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');" data-gaid="<?php echo $product['cart_tracking_id_for_ga']; ?>"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>

                <!-- <button type="button" data-toggle="tooltip" title="<?php // echo $button_compare; ?>" onclick="compare.add('<?php // echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
            </div>
        </div>
    </div>
</div>
</div>

<?php if($css_class == 'right'){
 echo '</div><div class="row row-eq-height">';
}?>
<?php } ?>
</div>

<!-- START wishlist color change in product list (04-01-2016) -->
<script type="text/javascript">
    $('button.addtowishlist').click(function(){
        $('#wishlist_heart_'+($(this).attr('data-wishlist-value'))).removeClass('fa fa-heart-o').addClass('fa fa-heart custom_heart');
    });
    $(document).ready(function(){

        var productInfoJson = $.parseJSON(JSON.stringify(<?php echo json_encode($productInfo); ?>));

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
                maxWidth: 768,
                width:'90%',
                padding:0,
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                },
                'hideOnContentClick': true,
                'href' : hrefA,
                centerOnScroll: true,
                autoScale: true,
                openEffect	: 'none',
                closeEffect	: 'none',
                afterShow: function() {
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
                afterClose: function() {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
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
                maxWidth: 768,
                width:'90%',
                padding:0,
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                },
                'hideOnContentClick': true,
                'href' : postUrl,
                centerOnScroll: true,
                autoScale: true,
                openEffect	: 'none',
                closeEffect	: 'none',
                afterShow: function() {
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
                afterClose: function() {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                }
            });

        });

    });
</script>
<!-- END wishlist color change in product list -->
<!--
<script src="http://cdnjs.cloudflare.com/ajax/libs/masonry/3.1.5/masonry.pkgd.min.js"></script>
<script>

    $(window).load(function () {
        var container = document.querySelector('#list_container');

        var msnry = new Masonry( container, {
            // options
            columnWidth: '.item',
            itemSelector: '.item'
        });
        console.log(msnry);
    });

</script>

-->