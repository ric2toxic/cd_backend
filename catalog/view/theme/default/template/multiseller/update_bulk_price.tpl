<?php echo $header_seller; ?>
<div class="container">
<ul class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>
  <div class="row">
  <div id="content" class="card_box">
   <div class="warning panel-body">
   		
   		<?php 
   		if (isset($errors)) {
   			foreach ($errors as $error) { ?>
   				<?php foreach ($error as $key => $e) { ?>
                    <?php if($key == 'success'){ ?>
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i> <?php echo $e; ?>
                            <button type="button" class="close" data-dismiss="alert">×</button>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger">
                            <i class="fa fa-times-circle"></i> <?php echo $e; ?>
                            <button type="button" class="close" data-dismiss="alert">×</button>
                        </div>
                    <?php } ?>
   				<?php } ?>
   				<?php //echo "<pre>"; print_r($errors); die;?>

   			<?php } ?>   		
   		<?php } ?>
   		
   </div>
			<div class="col-sm-6 login_seller">
				<div class="form-group">
					<div><h3><?php echo $text_wsb_download_csv; ?></h3></div>
					<a class="form-group" href="<?php echo $download_wholesale_list; ?>"><button class="btn btn-primary">Download Wholesale Products</button></a> 
				</div>
			</div>
			<div class="col-sm-6 login_seller">				
				<div class="form-group">
					<div><h3><?php echo $text_wsb_upload_csv; ?></h3></div>
					<form action="<?php echo $import_product_list;?>" enctype="multipart/form-data" method="post">
						<input type="file" name="seller_product" />
						<input class="btn btn-primary" type="submit" name="submit" value="Import" />
					</form>
				</div>
			</div>
		 

    </div>
  </div>
</div>
<?php echo $footer_seller; ?>
