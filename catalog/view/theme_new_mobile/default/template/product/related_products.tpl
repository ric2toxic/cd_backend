<?php
$productRelatedInfo = array();
if (!empty($products)) { ?>
<div class="row whitebox">
  <div class="col-sm-12 col-xs-12">
    <h3><?php echo $text_related; ?></h3>
    <div class="related_products">

      <div class="owl-related-nav">
        <span class="owl-related-prev"><i class="fa fa-arrow-circle-left"></i></span>
        <span class="owl-related-next"><i class="fa fa-arrow-circle-right"></i></span>
      </div>
      <div id="owl-related" class="owl-carousel">
        <?php $i = 0; ?>
        <?php foreach ($products as $product) { $productRelatedInfo[$product['product_id']] = $product['row_data']; ?>

        <?php $class = 'col-lg-3 col-md-3 col-sm-6 col-xs-12'; ?>

        <div >
          <div class="product-thumb  transition">
            <div class="thumb-inner">
              <div class="image"><a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productRelatedAjax"><img src="<?php echo $product['image_medium']; ?>" width="<?php echo $product['img_releted_width']; ?>" height="<?php echo $product['img_releted_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
              <?php /* ?>
              <div class="caption">
                <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                <p><?php echo $product['description']; ?></p>
                <?php if ($product['rating']) { ?>
                <div class="rating">
                  <?php for ($i = 1; $i <= 5; $i++) { ?>
                  <?php if ($product['rating'] < $i) { ?>
                  <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                  <?php } else { ?>
                  <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                  <?php } ?>
                  <?php } ?>
                </div>
                <?php } ?>

              </div>
              <?php */ ?>
              <div class="caption_box">
                <h4><a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productRelatedAjax"><?php echo $product['name']; ?></a></h4>
                <?php if ($product['price']) { ?>
                <p class="price">
                  <?php if (!$product['special']) { ?>
                  <?php echo $product['price']; ?>
                  <?php } else { ?>
                  <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                  <?php } ?>
                  <?php if ($product['tax']) { ?>
                  <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                  <?php } ?>
                </p>
              </div>
              <?php } ?>
            </div>
            <?php /* ?>
            <div class="button-group">
              <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span> <i class="fa fa-shopping-cart"></i></button>
              <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><span class="fa fa-heart fa-5x"></span></button>
              <!-- <button type="button" data-toggle="tooltip" title="<?php //echo $button_compare; ?>" onclick="compare.add('<?php //echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
            </div>
            <?php */ ?>
          </div>
        </div>
        <?php if ((isset($column_left) && isset($column_right)) && ($i % 2 == 0)) { ?>
        <div class="clearfix visible-md visible-sm"></div>
        <?php } elseif ((isset($column_left) || isset($column_right)) && ($i % 3 == 0)) { ?>
        <div class="clearfix visible-md"></div>
        <?php } elseif ($i % 4 == 0) { ?>
        <div class="clearfix visible-md"></div>
        <?php } ?>
        <?php $i++; ?>
        <?php } ?>
      </div>
    </div>
  </div>

</div>
<?php }
$arrToJsonStr = htmlspecialchars(json_encode($productRelatedInfo), ENT_QUOTES, 'UTF-8');
?>
<input type='hidden' id='productRelatedJson' value='<?php echo $arrToJsonStr; ?>' />
