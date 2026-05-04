<?php echo $header; ?>
<div class="container nopadding">
  <div class="row">
<!--     <div class="col-sm-12 advertise_article">
      <marquee onMouseOver="this.stop();" OnMouseOut="this.start();">
        <?php echo $home_advertise_article;?>
      </marquee>
    </div> -->
  </div>
  <!--<div class="row">
    <h1>
      <a href="<?php echo $dealoftheday_link?>"> Deal Of The Day </a>
    </h1>
  </div>-->
  <div class="row top_promotional_blocks">
    <div class="col-sm-3">
      <div class="col-sm-12 block first ">
        <ul class="promo_box">
          <li class="ic_ruppee">
          </li>
          <li class="promo_text">Buy Direct At Factory Prices</li>
        </ul>
      </div>
    </div>

    <div class="col-sm-3">
      <div class="col-sm-12 block ">
        <ul class="promo_box">
          <li class="ic_return">

          </li>
          <li class="promo_text">Easy 48 Hour Return Policy</li>
        </ul>

      </div>
    </div>

    <div class="col-sm-3">
      <div class="col-sm-12 block ">
        <ul class="promo_box">
          <li class="ic_cod">

          </li>
          <li class="promo_text promo_cod">Cash On Delivery</li>
        </ul>

      </div>
    </div>

    <div class="col-sm-3">
      <div class="col-sm-12 block last">
        <ul class="promo_box">
          <li class="ic_door">

          </li>
          <li class="promo_text promo_door">Door Delivery</li>
        </ul>
      </div>
    </div>

  </div>
</div>

<div class="container banner_home">
    <div class="row">
      <div class="col-sm-8 col-xs-12">
        <?php // echo $content_top; ?>
      </div>
      <div class="col-sm-4">
        <!--<a href="https://play.google.com/store/apps/details?id=in.wholesalebox" target="_blank" >
          <img src="image/promo1.png" alt="Banner" style="margin-bottom:25px;" />
        </a>-->
<!-- 		<a href="deal-of-the-day" target="_blank" >
          <img src="image/imgpsh_fullsize.png" alt="Banner" style="margin-bottom:25px;" />
        </a> -->
<!--        <div>


         <iframe id="video" type="text/html" width="366" height="212" src="http://www.youtube.com/embed/cK_3ADnOlYk?rel=0&enablejsapi=1" frameborder="0" allowfullscreen></iframe>
       </div> -->
       <!-- <img src="image/video.png" alt="Banner" />-->
      </div>
    </div>
</div>
<?php /* ?>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <a href="<?php echo $kurti_category_href;?>" alt="Women kurti">
        <img src="image/women-kurti.png" alt="Women kurti" />
       </a>
    </div>
    <div class="col-sm-4">
      <a href="<?php echo $tops_category_href;?>" alt="Women Tops">
      <img src="image/tops.jpg" alt="Women Tops" />
        </a>
    </div>
    <div class="col-sm-4">
      <a href="<?php echo $catalouge_category_href;?>" alt="Suit catlouge">
        <img src="image/suit_catlouge_content_banner.jpg" alt="Suit catlouge" />
      </a>
    </div>

  </div>

</div>
<?php */ ?>
<div class="container">
  <div class="row"><?php //echo $column_left; ?> 

    <div id="content " class="col-sm-12 homepage">

        <?php echo $content_bottom; ?>

    </div>



    <?php //echo $column_right; ?></div>

</div>
<?php /* ?>
<div class="container-fluid footer_top">
  <div class="row">
    <div class="col-sm-12 nopadding  nomargin">
      <?php echo $footer_top; ?>
    </div>
  </div>
</div>
<?php */ ?>
<script type="text/javascript" >
  $(document).ready(function() {

    $("#owl-category-slider").owlCarousel({

      navigation:true,
      navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
      items: 3
    });
    $("#LatestProducts").owlCarousel({
      navigation:true,
      navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
      items: 4,
      pagination : false
    });

    $("#bestsellers").owlCarousel({
      navigation:true,
      navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
      items: 4,
      pagination : false
    });


    $("#testimonials").owlCarousel({
      navigation:false,
      navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
      items: 1,
      pagination : true
    });

  });

</script>

<?php echo $footer; ?>
