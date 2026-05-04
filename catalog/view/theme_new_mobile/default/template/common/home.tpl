<!-- winter snow -->
 
  <?php echo $header; ?>

<div class="mob_container">
	<div class="slideshow_div">

			<?php echo $mobile_slide_b; ?>

		</div>
	<?php
		foreach($categories as $category){
	?>
			<div class="cat_box" style="background:url(<?php echo $category['thumb']; ?>) no-repeat;">
				<a href="<?php echo $category['href']; ?>" class="cat_image" >
					<span class="textOnImage"><?php echo $category['name']; ?></span>
				</a>
			</div>
	<?php
		}
	?>
</div>
<?php /* ?>
<div id="marqee_container">
<marquee class = "marqee_text"> <?php echo $home_advertise_article; ?> </marquee>
</div>
<?php */ ?>
<?php echo $footer; ?>

<script type="text/javascript">
	$("marquee").hover(function () {
		this.stop();
	}, function () {
		this.start();
	});
</script>
