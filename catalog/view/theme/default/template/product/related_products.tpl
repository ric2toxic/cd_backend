<script src="catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/jquery/owl-carousel/owl.carousel.css" type="text/css" rel="stylesheet" media="screen" />
<style>
    .hover_description{display: none;}
</style>
<?php
$productRelatedInfo = array();
if (!empty($products)) { ?>
<div class="container product_page home_width_adjustment" style="width:100%" >
  <div class="row">
    <div class="col-sm-12 bottom_releted_product">
      <h4><a href="javascript:void(0);" class="heading_title"><?php echo $text_related; ?></a> </h4>
      <hr/>
      <div class="related_products" style="position:relative;">
       
        <div class="owl-related-nav product_detail_owl">
          <span class="owl-related-prev"><i class="fa fa fa-angle-left"></i></span>
          <span class="owl-related-next"><i class="fa fa-angle-right"></i></span>
        </div>
       
        <div id="owl-related" class="owl-carousel">
          <?php $i = 0; ?>
          <?php foreach ($products as $product) {   $productRelatedInfo[$product['product_id']] = $product['row_data']; ?>
          
          <?php $class = 'col-lg-3 col-md-3 col-sm-6 col-xs-12'; ?>
         
          <div>
            <div class="product-thumb  transition related_product" id="related_product_<?php echo $product['product_id']; ?>">
              <div class="thumb-inner related_product_adjust" data-product-id = "<?php echo $product['product_id']; ?>" >
                <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['image_medium']; ?>" width="<?php echo $product['img_releted_width']; ?>" height="<?php echo $product['img_releted_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
                <?php /* ?>
                <div class="caption">
                  <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                  <p><?php echo $product['description']; ?></p>
                  <?php if ($product['rating']) { ?>
                  <div class="rating">
                    <?php for ($i = 1; $i <=5; $i++) { ?>
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
                  <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
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
                    <?php } ?>
                </div>
                <?php
                      /*------ start fill heart after user login ----*/
                      $fill_heart = $this->customer->getWishlistIcon($product['product_id']);
                      /*------ END fill heart after user login ----*/
                      ?>
                <div class="button_add_wishlist releted_product_wishlist" data-product-id="<?php echo $product['product_id']; ?>">
                  <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>',event);" class="addtowishlist" id="wishlist_<?php echo $product['product_id']; ?>"   ><i class=" <?php echo $fill_heart;?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
                </div>
              </div>

            </div>


            <div class="hover_description product-grid col-sm-12" id= "hover_description_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">

                  <div class="button-group">

                      <div class="button_add_cart">
                          <a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productRelatedAjax ellipsis" >
                              <button type="button" class="quickview btn"><i class="fa fa-eye"></i></button>
                          </a>


                          <button type="button <?php if ($product['quantity'] <= 0) { echo 'disabled'; } ?>"

                           class="addtocart btn product_addtocart" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i> </button>
                      </div>
                      <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
                  </div>
                  <!-- Start Product Options(Size)  -->


                  <!-- End Product Options(Size)  -->
              </div>




          </div>


          <?php $i++; ?>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<input type="hidden" id="productRelatedJson" value='<?php echo htmlspecialchars(json_encode($productRelatedInfo), ENT_QUOTES, 'UTF-8'); ?>' />
