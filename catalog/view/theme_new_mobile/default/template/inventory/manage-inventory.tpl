<?php echo $header; ?>
<?php //echo '<pre>'; print_r($data);?>
<table class="list table table-bordered">
		<thead>
			<tr>
				<!--<td><?php echo $image; ?></td>
				<?php if (!$this->config->get('msconf_hide_customer_email')) { ?>
					<td><?php echo $ms_account_orders_customer; ?></td>
				<?php } ?>
				<td><?php echo $ms_status; ?></td>
				<?php /* ?><td style="width: 40%"><?php echo $ms_account_orders_products; ?></td><?php */ ?>
				<td><?php echo $ms_date_created; ?></td>
				<td><?php echo $ms_account_orders_total; ?></td>
				<td><?php echo $ms_action; ?></td>-->
				<td>Image</td>
				<td>SKU</td>
				<td>Name</td>
				<td>Quantity</td>
			</tr>
		</thead>

		<tbody>
		<?php if (isset($data) && $data) { ?>
			<?php foreach ($data as $product) { ?>
			<tr>
				<td>
					<?php //echo $image = $this->MsLoader->MsFile->resizeImage('no_image.png', 50, 50);?>
					<a href="<?php echo $this->url->link('product/product/view', 'product_id=' . $product['product_id']); ?>" class="" title=""><?php echo $product['p.image']; ?></a></td>
				<td><?php echo $product['p.sku']; ?></td>
				<td><?php echo $product['pd.name']; ?></td>
				<td>
					<span class="quantity_text" id="quantity_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['p.quantity']; ?></span>
					<span style="display:none;" id="quantity_input_<?php echo $product['product_id']; ?>"><input style="width: 50px;" type="text" id="quantity_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['p.quantity']; ?>" /> <span class="save_quantity" html-data="<?php echo $product['product_id']; ?>">Save</span></span>
				</td>
			</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td class="center" colspan="7"><?php echo $ms_account_orders_noorders; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php echo $footer; ?>
<script>
$(document).ready(function(){
	$("span.quantity_text").click(function(){
		$(this).hide();
		$("span#quantity_input_"+$(this).attr("html-data")).show();
	});
	
	$("span.save_quantity").click(function(){
		$("span#quantity_input_"+$(this).attr("html-data")).hide();
		//console.log($(this));
		//alert($(this).attr("value"));
		$("span#quantity_text_"+$(this).attr("html-data")).html($("input#quantity_val_"+$(this).attr("html-data")).val());
		$("span#quantity_text_"+$(this).attr("html-data")).show();
		
		$.ajax({
			url : "index.php?route=inventory/manage-inventory/UpdateQuantity",
			type: "post",
			dataType: "json",
			data: "quantity="+$("input#quantity_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data"),
			success: function( data ) {
				//alert(data);
				if(data.trim() == 'success'){
					alert("here");
				}else{
					alert("123");
				}
				
				/*response( $.map( data, function( item ) {
					//console.log(item);
					var code = item.split("|");
					/*return {
						label: "<b>dddd</b>",
						value: code[0],
						data : item
					}*/
					/*return {label: highlight(item, text),
						value: item};

				}));*/
			}
		});
		
	});
	
});
</script>
