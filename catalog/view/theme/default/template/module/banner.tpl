<div id="banner_list<?php echo $module; ?>" >
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <?php } ?>
  </div>
  <?php } ?>
</div>
<script type="text/javascript"><!--
    $(document).ready(function(){
        $('#banner_list<?php echo $module; ?>').owlCarousel({
            //items: 2,
            //autoPlay: 3000,
            //singleItem: true,
            navigation: true,
            navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
            pagination: false,
            itemsDesktop : [1920,3],
            itemsDesktopSmall : [1600,2],
            //transitionStyle: 'fade'
        });
    });
--></script>
