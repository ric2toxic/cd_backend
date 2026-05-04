
<div class="trending_container">
    <h3 class="text_line"><span><?php echo $heading_title; ?></span></h3>

    <div class="home_categories" >
    <amp-carousel width=auto>
  <?php foreach ($products as $product) { ?>

    <div class='home_categories_product'>
     <amp-img srcset="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" width="<?php echo $product['amp_width']?>" height="<?php echo $product['amp_height']?>" ></amp-img>
      <!-- Start hover description -->    
      <div class="pd_description">
      <a href="<?php echo $product['href']; ?>">
        <span>
          <?php echo $product['set_description'];?>
        </span>
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
                                 <span class="nopadding"><?php echo $opt_value['name']; ?></span>
                    <?php  } } ?>
                              </span>
                <?php    }?>
                <?php } ?>
            </div>
            <?php } ?>
            <!-- End Product Options(Size)  -->
        </a>
      </div>
      <!-- end hover description -->
      <h6><a href="<?php echo $product['href']; ?>"><?php echo mb_strimwidth($product['name'], 0, 50, "..."); ?></a>
      </h6>
      <P><?php echo $product['price']; ?></P> 

      <amp-button type="button" <?php if ($product['quantity'] <= 0) { echo "disabled"; } ?> class="addtocart_new btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>"  onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> Add to Cart</amp-button>
      <div class="clear"></div>
    </div>

  <?php } ?>
  </amp-carousel>
</div>
</div>

