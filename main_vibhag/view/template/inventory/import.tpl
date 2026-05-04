<?php echo $header; ?><?php echo $column_left; ?>
<?php //secho "<pre>"; print_r($csv_data); ?>

<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right"><button class="btn btn-success" 
				<?php if ((!isset($csv_data)) && empty($csv_data)) { ?>
					disabled="disabled"
				<?php } ?>
			id="button-upload"><i class="fa fa-database"></i></button></div>
				<h1><?php echo $heading_title; ?></h1>
				<ul class="breadcrumb">
				  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
				  <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				  <?php } ?>
				</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3><i class="fa fa-list"></i><?php echo $heading_title; ?></h3>
			</div>
			<div class="panel-body">
				<div class="well">
					<div class="row">
						<form action="<?php echo $form_action; ?>" class="" method="POST" enctype="multipart/form-data">
							<div class="col-sm-4">
								<div class="form-group required"><label for="seller-name-select" class="control-label"><?php echo $text_label_seller; ?></label><select class="form-control" id="seller_name" name="submit_seller">
									<option value="0"><?php echo $text_select_none  ?></option>
								<?php echo $product_seller;foreach ($sellers as $seller) { ?>
								<?php if ($seller['seller_id'] == $product_seller){
											$selected = 'selected="selected"';
										}else{
											$selected = '';
									} ?>
								<option value="<?php echo $seller['seller_id'] ?>" <?php echo $selected;?>><?php echo $seller['ms.company']; ?>-(<?php echo $seller['ms.nickname'] ?>)</option>
								<?php } ?>
								</select></div>
								<?php if ($error_seller) { ?>
								<div class="text-danger"><?php echo $error_seller; ?></div>
								<?php } ?>								
							</div>
							<div class="col-sm-4">
								<div class="form-group required"><label for="submit-category-select" class="control-label"><?php echo $text_label_product_category; ?></label><select class="form-control" id="category_name" name="submit_category"><option value="0"><?php echo $text_select_none; ?></option>
								<?php foreach ($categories as $category) { ?>
								<?php if ($category['category_id'] == $product_category){
										$selected = 'selected="selected"';
									}else{
										$selected = '';
								} ?>

								<option value="<?php echo $category['category_id']; ?>" <?php echo $selected; ?>><?php echo $category['name']; ?></option>
								<?php } ?>
								</select></div>
								<?php if ($error_category) { ?>
								<div class="text-danger"><?php echo $error_category; ?></div>
								<?php } ?>
								<?php if ($error_commission) { ?>
								<div class="text-danger"><?php echo $error_commission; ?></div>
								<?php } ?>
							</div>
							<div class="col-sm-4">

								<div class="form-group required">
									<label for="csvname" class="control-label">
									<?php echo $text_label_csv_name; ?>
									</label>
									<input type="file" class="form-control" id = "submit_csv" name="submit_csv" />
								</div>

								<?php if ($error_csv) { ?>
								<div class="text-danger"><?php echo $error_csv; ?></div>
								<?php } ?>
							</div>
							<div class="col-sm-4">
								<label>
									<input type="checkbox" name="wholesalebox" value="1" checked>
									<label>Add to wholesalebox</label>
								</label>
								<br>
								<label>
									<input type="checkbox" name="import_storefront" value="1" checked>
									<label>Add to storefront</label>
								</label>
							</div>
							<div class="col-sm-4">
								<label>
									<input type="checkbox" name="disabled_status" value="1" >
									<label>Disabled Status</label>
								</label>
								<br>
								<label>
									<input type="checkbox" name="without_image" value="1" >
									<label>Without Image</label>
								</label>								
							</div>
							<div class="col-sm-4">
								<label>
									<input id="wsb_code" type="checkbox" name="wsb_code" value="1" >
									<label>WSB Code same as Seller Code</label>
								</label>
								<br>
								<label>
									<input type="checkbox" name="do_not_prefix_seller_code" value="1" >
									<label>Do Not Prefix Seller Code</label>
								</label>								
							</div>
							<div class="col-sm-12">
								<div class="alert alert-info">
								  <strong>1.</strong> Default is (Seller_Code Random).<br>
								  <strong>2.</strong> Select WSB Code same as Seller Code and Select Do Not Prefix Seller Code (SKU).<br>
								  <strong>3.</strong> Select WSB Code same as Seller Code and UnSelect Do Not Prefix Seller Code (Seller Code+SKU).<br>
								  <strong>4.</strong> Select Do Not Prefix Seller Code and UnSelect WSB Code same as Seller Code (SKU Random).<br>
								</div>
							</div>
							<?php if ($error_image) { ?>
								<div class="text-danger"><?php echo $error_image; ?></div>
							<?php } ?>
							
							<?php if (!empty($file_format)) { ?>
							<div class="col-sm-12">
								<div class="text-danger" style="background-color: #fc8383; padding: 5px; color: #fff;"><?php echo $file_format; ?></div>
								</div>
							<?php } ?>
							
							<input style="margin-top: 27px;" type="submit" id = "validate-btn" class="btn btn-success pull-left form-control" value="Validate" />							
						</form>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="text-left"><?php echo $column_sr_no; ?></td>
								<td class="text-left"><?php echo $column_row_no; ?></td>
								<td class="text-left"><?php echo $column_sku; ?></td>
								<td class="text-left"><?php echo $column_title; ?></td>
								<td class="text-left"><?php echo $column_action; ?></td>
							</tr>

						</thead>
						<tbody>
						<?php

						 if(!empty($csv_data) && count($csv_data)>0){ ?>
						<?php $srno=0;foreach ($csv_data as $product) { $srno++?>
						<?php if (empty($product['error'])) {
							$clean_data[] = $product;
						} ?>
							<tr class="<?php if(!empty($product['error'])) {echo "alert-danger"." "."accordion-toggle";} else {echo "alert-success"." "."accordion-toggle";}  ?>" data-toggle="collapse" data-target="#error_<?php echo $product['sku'] ?>">
								<td><?php echo $srno; ?></td>
								<td><?php echo $product['row']; ?></td>
								<td><?php echo $product['sku']; ?></td>
								<td><?php echo $product['name']; ?></td>
								<td>
									<?php 
										if(empty($product['error'])) {
											echo "Passed"; 
										} else if (!empty($product['error'])) {
											echo "Error"; 
										} 
										if ($error_flag == 0) {
											echo " and Inserted";
										}
									?>
								
								</td>
							</tr>
							<?php if (isset($product['error']) && !empty($product['error'])) { ?>
							<tr>
								<td colspan="6" class="hiddenRow">
									<div class="accordian-body collapse" id="error_<?php echo $product['sku'] ?>">
										<table class="table table-bordered table-condensed" style="margin-bottom:4px">
											<thead>
												<tr>
													<td><?php echo "Sr No"; ?></td>
													<td><?php echo "Item"; ?></td>
													<td><?php echo "Error"; ?></td>
												</tr>
											</thead>
											<tbody>
										<?php $count=0 ;foreach (array_keys($product['error']) as $e => $error_key) { $count++;?>
													<tr>
														<td>
															<?php echo $count; ?>
														<td>
															<?php echo $error_key ?>
														</td>
														<td>
															<?php echo $product['error'][$error_key] ?>
														</td>
													</tr>
										<?php } ?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
							<?php };?>
						<?php } ?>
						<?php } ?>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
	var error_flag = '<?php echo $error_flag; ?>';
	
	$('#button-upload').click(function(argument) {
		$('tr.alert-success').hide();
		$.ajax({
		url : "index.php?route=inventory/import/dbUpload&token=<?php echo $this->session->data['token']; ?>",
		dataType : 'json',
		type : 'post'

		});
	});
	
	$('#validate-btn').on('click',function() {
		if(error_flag == 0) {
			$('#validate-btn').prop('value', 'Upload');
		}
		if($('#submit_csv').val() == '') {
			alert("please select file");
			return false;
		} else if($('#submit_csv').val().replace(/^.*[\\\/]/, '').split('.')[1] !== "csv") {
			alert("please select only csv file");
			$("#submit_csv").val('');
			return false;
		}
		if ($('#wsb_code').prop('checked')) {
			if (!confirm('Are you sure you want to processed with WSB code same as Seller Code.')) {
				return false;
			}
		}
	});

</script>
