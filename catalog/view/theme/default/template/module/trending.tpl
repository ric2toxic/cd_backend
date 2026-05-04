
    <h3><?php echo $heading_title; ?></h3>

    <div class="row card_box_flat nopadding hidden-xs list_craousel" id="TrendingProducts">
  <?php foreach ($products as $product) { ?>

    <div class="slidebox_container owl-item">

      <div class="product-thumb" style="border: none !important;" data-product-id="<?php echo $product['product_id']; ?>" >
        <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');" class="addtowishlist pull-right" id="wishlist_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>"><i class=" <?php echo $product['fill_heart']; ?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>


    <!--     <div class="button_add_wishlist releted_product_wishlist" data-product-id="<?php //echo $product['product_id']; ?>">
 
        </div> -->
        <!--<a href="<?php // echo $this->url->link('product/category', 'path='.$kurti_cat_id, 'SSL');?>" title="Women kurti at wholesale price" ><img src="<?php // echo $base; ?>image/home/women-kurti-wholesale-price-100x150.jpg" alt="Women kurti at wholesale price" title="Women kurti at wholesale price" /></a>-->
        <div class="aligncenter">
          <h6 style="font-size: 12px; font-weight: 400;"><a href="<?php echo $product['href']; ?>"><?php echo mb_strimwidth($product['name'], 0, 50, "..."); ?></a></h6>
          <?php echo $product['price']; ?>
          <!--<h3><a href="<?php // echo $this->url->link('product/category', 'path='.$kurti_cat_id, 'SSL');?>" title="Women kurti at wholesale price" >Women Kurtis @ wholesale</a></h3>-->

        </div>


      </div>
      <!-- Start hover description -->
              <div class="hover_description product-grid col-sm-12" id= "hover_description_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" style="display:none;">
                  <?php /* ?>
                  <div class="button-group">

                      <div class="button_add_cart">
                      <a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe ellipsis">
                        <button type="button" class="quickview btn"><i class="fa fa-eye"></i></button>
                      </a>


                        <button type="button" <?php if ($product['quantity'] <= 0) { echo "disabled"; } ?> class="addtocart btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> </button>
                      </div>
                      <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
                  </div>
                  <?php */ ?>

                  <div class="product_description">
                    <span class="product_set_description">
                      <?php echo $product['set_description'];?>
                    </span>
                  </div>
                  <div class="button-group">

                      <div class="button_add_cart">
                          <a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe ellipsis">
                              <button type="button" class="quickview btn"><i class="fa fa-eye"></i></button>
                          </a>


                          <button type="button" <?php if ($product['quantity'] <= 0) { echo "disabled"; } ?> class="addtocart btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> </button>
                      </div>
                      <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
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
              </div>
      <!-- End hover description -->

      </div>

  <?php } ?>
</div>

