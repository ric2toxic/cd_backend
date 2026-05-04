<?php if( !empty( $data ) ) { ?>
<div style="margin-top: 10px;">
	<p><b>Hi <?php echo $data['buyer_name'];?></b>,</p>
	<p>Greeting from <b>Wholesalebox.in</p>
	<p><?php echo $data["comment_message"].$data['order_no']; ?></p>
	<table border="1">
			<thead>
				<tr>
					<th>S.No</td>
					<th>Image</td>
					<th>Model</th>
					<th>Reason</td>
					<th>Pieces</td>
					<th>Price / Pc</th>
					<th>Total Amount</th>
				</tr>
			</thead>
			<tbody>
					<?php $i = 1;?>
					<?php foreach($products as $product) {
							foreach($product as $pid => $values) {
								$quantity = $values["quantity"];
								$price_per_piece = $values["price_per_piece"];
								$total_amt = ($quantity * $price_per_piece);
					?>
							<tr align="center">
								<td><?php echo $i;?></td>
								<td><img src="<?php echo $values["image"];?>" width = "<?php echo $image_width;?>" height = "<?php echo $image_height;?>"></td>
								<td><?php echo $values["model"]; ?></td>
								<td><?php echo $values["return_reason_text"]; ?></td>
								<td><?php echo $values['quantity']; ?></td>
								<td><?php echo $values["price_per_piece"]; ?></td>
								<td><?php echo $total_amt; ?></td>
						 </tr>
					<?php
					$i++;
				   	}
				   }
					 ?>
			</tbody>
	</table>
</div>
<?php } ?>
