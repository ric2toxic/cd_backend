
<?php if( !empty( $data ) ) { ?>
<div style="width: 44%;margin: auto;margin-top: 10px;">
	<p><b>Hi <?php echo $data['buyer_name'];?></b>,</p>
	<p>Greeting from <b>Wholesalebox.in</b></p>
	<p><?php echo $comment_message; ?> <?php echo $data['order_no']; ?></p>
	<?php foreach($data['products'] as $value){
		if(isset($value["last_return_reason"]) || isset($value["last_return_quantity"])){
			$return = "true";
		} else{
			$return = "false";
		}

	}
	?>
	<?php if($return == "true"){ ?>
		<table border="1" width="100%" style="margin-bottom: 20px;">
			<thead>
				<tr align="left">
					<th colspan="7">Previous Return Request</th>
				</tr>
				<tr>
					<th>S.No</td>
					<th>Image</td>
					<th>Model</th>
					<th>Reason</td>
					<th>Pieces</td>
				</tr>
			</thead>
			<tbody>
				<?php $i =1;?>
				<?php foreach($data['products'] as $value){ ?>
					<tr align="center">
						<td><?php echo $i; ?></td>
						<td><img src="<?php echo HTTPS_SERVER.'image/'.$value["product_image"];?>" width = "<?php echo $image_width;?>" height = "<?php echo $image_height;?>"></td>
						<td><?php echo $value["model"]; ?></td>
						<td><?php echo $value["last_return_reason"]?></td>
						<td><?php echo $value["quantity"]; ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
			</tbody>
		</table>
	<?php
	}
	?>
	<table border="1" width="100%">
		<thead>
			<tr align="left">
				<th colspan="7">Order No: # <?php echo $data['order_no']; ?>(Current)</th>
			</tr>
			<tr>
				<th>S.No.</td>
				<th>Image</td>
				<th>Model</th>
				<th>Return Reason</th>
				<th>Return Pieces</th>
				<th>Total Pieces</th>
			</tr>
		</thead>
		<tbody>
			<?php $i =1;?>
			<?php foreach($data['products'] as $value){ ?>
			<?php
					$quantity = $value["quantity"];
					$price_per_piece = $value["price_per_piece"];
					$total_amt = ($quantity * $price_per_piece);
			?>
			<tr align="center">
				<td><?php echo $i; ?></td>
				<td><img src="<?php echo HTTPS_SERVER.'image/'.$value["product_image"];?>" width = "<?php echo $image_width;?>" height = "<?php echo $image_height;?>"></td>
				<td><?php echo $value["model"]; ?></td>
				<td><?php echo $value["return_reason_text"]; ?></td>
				<td><?php echo $quantity; ?></td>
				<td><?php echo $value["total_pieces"]; ?></td>
				</tr>
			<?php
				$i++;
				}
			?>

		</tbody>
	</table>
	<?php if(isset($fotter_comment)){ ?>
	<p><?php echo $fotter_comment;?></p>
	<?php } ?>
</div>
<?php } ?>