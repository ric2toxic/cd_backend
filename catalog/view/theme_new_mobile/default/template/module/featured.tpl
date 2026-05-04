<!-- <h3><?php echo $heading_title; ?></h3> -->
<div class="row">
  <?php foreach ($products as $product) { ?>
  <div class="product-layout col-lg-3 col-md-3 col-sm-6 col-xs-6">

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
    $instock = '<span class="in_stock">'.$text_in_stock.'</span>';
    }
    ?>


    <div class="product-thumb transition">
      <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
      <div class="caption">
        <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
        <?php if ($product['rating']) { ?>
        <div class="rating">
          <?php for ($i = 1; $i <= 5; $i++) { ?>
          <?php if ($product['rating'] < $i) { ?>
          <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
          <?php } else { ?>
          <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
          <?php } ?>
          <?php } ?>
        </div>
        <?php } ?>
        
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
          <b><?php echo $text_moq_pre . $product['minimum'] . $text_moq_post; ?></b>
          <?php } else { ?>
          <b><?php echo $text_moq_default; ?></b>
          <?php } ?>
        </span>
        <?php } else { ?>
        <p>
          <b><?php echo $text_moq_default; ?></b>
        </p>
        <?php } ?>
        
        <p class="set-description"><?php echo $product['set_description']; ?></p>
        <div class="stock_circle_list <?php echo $bgcolor; ?>"><div><?php echo $instock; ?><span class="soldout"><?php echo $text_sold_out;?></span></div></div>
      </div>
      <div class="button-group">
        <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
        <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
      </div>
    </div>
  </div>
  <?php } ?>
</div>
