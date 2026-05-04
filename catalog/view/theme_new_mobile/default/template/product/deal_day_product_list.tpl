<?php
    $i = 0;
    foreach ($products as $product) {

    $i++;
    $css_class = 'left';
    if( $i == 2){
        $i = 0;
        $css_class = 'right';
    }
 ?>

<?php
/************(04-01-2016) by vikas ****************************/
/**----- Start fill wishlist ---------- **/
    $fill_heart = 'fa fa-heart-o';
    if($this->customer->isLogged()){
if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
if(in_array($product['product_id'],$this->session->data['wishlist'])){
$fill_heart = 'fa fa-heart-o custom_heart';
}
}
}else{
if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
if(in_array($product['product_id'],$this->session->data['wishlist'])){
$fill_heart = 'fa fa-heart custom_heart';
}
}
}
/**----- End fill wishlist ---------- **/
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
        <a href="<?php echo $product['href']; ?>" class="fancybox ">
            <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
        </a>
        <button type="button" class="addtowishlist" data-wishlist-value = "<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="<?php echo $fill_heart ; ?>" id="wishlist_heart_<?php echo $product['product_id']?>"></i></button>
    </div>
    <div>
        <div class="caption">
            <h4><a href="<?php echo $product['href']; ?>" class="fancybox "><?php echo mb_strimwidth($product['name'], 0, 65, "..."); ?></a></h4>
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

            <?php
                    if($product['is_single'] == '1'){

                    }else{
                      if ($product['minimum']) { ?>
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
            <?php /* ?>
            <div class="stock_circle_list <?php echo $bgcolor; ?>"><div><?php echo $instock; ?><span class="soldout"><?php echo $text_sold_out;?></span></div></div>
            <?php */ ?>
            <?php } ?>
        </div>
        <div class="button-group">
            <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>

            <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
        </div>
    </div>
</div>
</div>
<?php } ?>

<!-- START wishlist color change in product list (04-01-2016) -->
<script type="text/javascript">
    $('button.addtowishlist').click(function(){
        $('#wishlist_heart_'+($(this).attr('data-wishlist-value'))).removeClass('fa fa-heart-o').addClass('fa fa-heart custom_heart');
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

