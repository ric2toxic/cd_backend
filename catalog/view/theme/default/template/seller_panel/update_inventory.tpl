<?php echo $header_seller; ?>
<div class="container">
<ul class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>
  <div class="row">
   <div id="content" class="card_box">
		<div class="col-sm-6 login_seller">
			<div class="form-group">
				<label class="col-sm-3 control-label"><h4><?php echo "Categories";//$ms_account_sellerinfo_zone; ?></h4></label>
				<div class="col-sm-8">
					<select name="seller[categories]" class="form-control selected_cat">
						<?php 
							foreach($categories as $id=>$category){
								echo "<option value='".$id."'>".$category."</option>";
							}
						?>
					</select>
					
				</div>
			</div>
			<div class="form-group">
				<div><h3><?php echo $download_excel; ?></h3></div>
				<a class="form-group" href="javascript:void(0);"><button class="download_excel btn btn-primary">Download Excel Format</button></a> 
			</div>
		</div>
		<div class="col-sm-6 login_seller">
			<!--<a href="< ?php echo $download_single_list; ?>">Download Single Products</a> ||-->
			<div class="form-group">
				<label class="col-sm-3 control-label"><h4><?php echo "Categories";//$ms_account_sellerinfo_zone; ?></h4></label>
				<div class="col-sm-8">
					<select name="seller[categories]" class="form-control selected_cat_import">
						<?php 
							foreach($categories as $id=>$category){
								echo "<option value='".$id."'>".$category."</option>";
							}
						?>
					</select>
					
				</div>
			</div>
			
			<div class="form-group">
				<div><h3><?php echo $upload_excel; ?></h3></div>
				<form action="<?php echo $import_inventry_list;?>" enctype="multipart/form-data" method="post">
					<input type="file" name="seller_product" />
					<?php 
					foreach($stores_data as $store){
					?>
						<label><?php echo $store['name'];?></label>
						<input type="checkbox" name="stores[]" value="<?php echo $store['store_id'];?>" />
					<?php 
					}
					?>
					<br />
					<input type="hidden" name="cat_id" id="import_cat_id" value="" />
					<input class="btn btn-primary" type="submit" name="submit" value="Import" />
					
				</form>
			</div>
		</div>
		
		<?php if(!empty($results)){?>
		<div class="container">
		  <h2>Error and success messages</h2>
		  <div class="panel-group">
			<div class="panel panel-default">
			<?php 
				//echo "<pre>"; print_r($results);
				$i = 0;
				foreach($results as $result){
					if(!empty($result['warnings'])){
						$class = "error_class";
					}else{
						$class = "success_class";
					}
			?>
			  <div class="panel-heading <?php echo $class;?>">
				<h4 class="panel-title">
				  <a data-toggle="collapse" href="#collapse<?php echo $i; ?>"><?php echo $result["product_sku"];?> - <?php echo $result["product_name"];?></a>
				</h4>
			  </div>
			  <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse">
				<?php 
						foreach($result['warnings'] as $warning){
					?>
							<div class="panel-body"><?php echo $warning["key"] .' '.$warning["message"];?></div>
					<?php } ?>
			  </div>
			<?php  $i++;} ?>
			</div>
		  </div>
		</div>
		<?php } ?>
	</div>
  </div>
</div>
<script>
$(".download_excel").click(function(){
	var download_url = '<?php echo $download_inventry_list; ?>';
	var cat_id = $("select.selected_cat option:selected").val();
	var url = download_url+'&category_id='+cat_id;
	//alert(url);
	window.location.href = url;
	
});

$("select.selected_cat_import").change(function(){
	var cat_id = $(this).val();
	$("#import_cat_id").val(cat_id);
});
$("select.selected_cat_import").trigger("change");

</script>
<style>
	.success_class{ border-bottom: 1px solid rgb(204, 204, 204) !important; background: #729e64 none repeat scroll 0 0 !important;}
	.error_class{border-bottom: 1px solid rgb(204, 204, 204) !important; background: #F03140 none repeat scroll 0 0 !important;}
	.panel-title a{color:#ffffff !important;}
</style>
<?php echo $footer_seller; ?>
