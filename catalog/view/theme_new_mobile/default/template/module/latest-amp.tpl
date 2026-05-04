<div class="hidden-xs">
<h3><?php echo $heading_title; ?></h3>
<div class="row card_box_flat nopadding hidden-xs" id="LatestProducts">
  <?php foreach ($products as $product) {
  //echo '<pre>'; print_r($product);
  ?>

  <div class="row nopadding hidden-md hidden-sm slidebox_wrapper" >

    <div class="slidebox_container">


      <div class="product-thumb" style="border: none !important;">
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
        <!--<a href="<?php // echo $this->url->link('product/category', 'path='.$kurti_cat_id, 'SSL');?>" title="Women kurti at wholesale price" ><img src="<?php // echo $base; ?>image/home/women-kurti-wholesale-price-100x150.jpg" alt="Women kurti at wholesale price" title="Women kurti at wholesale price" /></a>-->
        <div class="aligncenter">
          <h5><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h5>
          <!--<h3><a href="<?php // echo $this->url->link('product/category', 'path='.$kurti_cat_id, 'SSL');?>" title="Women kurti at wholesale price" >Women Kurtis @ wholesale</a></h3>-->

        </div>
      </div>

      </div>
    </div>

  <?php } ?>
</div>
</div>