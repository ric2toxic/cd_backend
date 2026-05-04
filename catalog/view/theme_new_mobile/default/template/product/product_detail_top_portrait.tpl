<div class="col-sm-12 wrapper" id="main_img_container" >
    <?php if ($images) {
            foreach ($images as $image) {
?>              <a class="thumbnail popup_image" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"> <img class="img-responsive" src="<?php echo $image['pan_detail']; ?>" width="150px" height="225px" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
<?php       }
         }
?>
</div>