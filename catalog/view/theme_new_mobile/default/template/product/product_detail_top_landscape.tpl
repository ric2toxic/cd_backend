<div class="col-sm-12 wrapper" id="main_img_container" >

    <?php if ($thumb || $images) { ?>

        <?php if ($images) { ?>
             <?php foreach ($images as $image) { ?>
             <a class="thumbnail popup_image" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"> <img src="<?php echo $image['popup']; ?>" width="300px" height="225px" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a> 

              <?php } ?>
    <?php } ?>

    <?php } ?>
</div>
