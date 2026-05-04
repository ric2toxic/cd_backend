<?php /*?>
<h3><?php echo $heading_title; ?></h3>
<div class="row product-layout">
  <?php foreach ($products as $product) { ?>
  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="product-thumb transition">
      <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
      <div class="caption">
        <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
        <p><?php echo $product['description']; ?></p>
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
      <div class="button-group">
        <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<?php */?>






<!-- add new design for recently viewed  -->
<div id="myCarousel-viewed" class="thumbs-col col-md-12 col-sm-12  vertical-slider carousel vertical col-xs-12 recenlty_viewed_product" data-ride="carousel">
  <div class="carousel carousel-inner carousel-recently-viewed ">
    <?php
        $i=0;
        foreach ($products as $product) {

          if($i==0) {
            $active  = 'active';
          }else{
            $active='';
          }
          $i++;
    ?>
      <div class="viewed-product-thumb item <?php echo $active; ?>">
        <div class="product-thumb transition ">
          <div class="thumb-inner">
            <div class="image">
              <a href="<?php echo $product['href']; ?>">
                <img src="<?php echo $product['thumb']; ?>" width="<?php echo $product['img_width']; ?>"  height="<?php echo $product['img_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
              </a>
            </div>
            <div class="caption_box">
              <h4>
                <a href="<?php echo $product['href']; ?>">
                  <?php echo mb_strimwidth($product['name'], 0, 30, "..."); ?>
                </a>
              </h4>
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
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>



<script type="text/javascript">
$(document).ready(function () {
  $('#myCarousel-viewed').carousel({
    pause: true,
    interval: false
  });
  $('.btn-vertical-slider-viewed').on('click', function () {
    if ($(this).attr('data-slide') == 'next') {
      $('#myCarousel-viewed').carousel('next');
    }
    if ($(this).attr('data-slide') == 'prev') {
      $('#myCarousel-viewed').carousel('prev');
    }
  });

  $('.carousel-recently-viewed .item').each(function() {
    var item = $(this);
    item.nextAll().each(function(index) {
      //alert(index);
      if (index < 0) {
        $(this).children(':first-child').clone().appendTo(item);
      }
    });
  });
});
</script>
