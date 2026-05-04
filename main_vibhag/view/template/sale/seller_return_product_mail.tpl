<?php if( !empty( $data ) ) { ?>
<div style="margin-top: 10px;">	
	<p><b>Hi <?php echo $seller_company;?></b>,</p>
	<p>Greeting from <b>Wholesalebox.in</b></p>
	<p><?php echo $data["comment_message"]; ?><?php echo !empty($orderNo) ? $orderNo : '-'; ?></p>
	<table border="1">
		<thead>
			<th>Image</td>
			<th>SKU</th>
			<th>Reason</td>
			<th>Pieces</td>
			<th>Transfer Price / pc</th>
			<th>Total Amount</th>
		</tr>
		</thead>
		<tbody>
			<?php foreach($products as $sid => $data_details) { 
				foreach($data_details as $product_values) {
					if($sid == $seller_id){
						$quantity                 = $product_values["quantity"];
						$transfer_price_per_piece = $product_values["transfer_price_per_piece"];
						$total_amt                = ($quantity * $transfer_price_per_piece);
					?>
					<tr align="center">
						<td><img src="<?php echo $product_values["image"];?>" width = "<?php echo $image_width;?>" height = "<?php echo $image_height;?>"></td>
						<td><?php echo $product_values["sku"]; ?></td>
						<td><?php echo $product_values["return_reason_text"]; ?></td>
						<td><?php echo $product_values['quantity']; ?></td>
						<td><?php echo $this->currency->format($product_values["transfer_price_per_piece"], 'INR', 1, true); ?></td>
						<td><?php echo $this->currency->format($total_amt, 'INR', 1, true); ?></td>
					</tr>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	<b>Regards</b> <br/>
    Business Development Team<br />
    Helpline: 9649558363<br />
    <a href="https://www.wholesalebox.in" target="_blank">www.wholesalebox.in</a>
</div>
<?php } ?>