<amp-carousel class="carousel-preview img-responsive" type="slides" autoplay delay="5000" >
  <?php foreach ($banners as $banner) { ?>
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>">
    <amp-img src="<?php echo $banner['image']; ?>" layout="responsive" width="<?php echo $banner['img_width']; ?>"  height="<?php echo $banner['img_height']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></amp-img></a>
    <?php } else { ?>
    <amp-img src="<?php echo $banner['image']; ?>" layout="responsive" width="<?php echo $banner['img_width']; ?>"  height="<?php echo $banner['img_height']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></amp-img>
    <?php } ?>
  <?php } ?>
</<amp-carousel>
<!-- <script type="text/javascript">
  $(document).ready(function() {
    $('#slideshow<?php echo $module; ?>').owlCarousel({
      items: 6,
      autoPlay: 5000,
      singleItem: true,
      navigation: true,
      navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
      pagination: false,
      stopOnHover: true
    });
  });
</script> -->