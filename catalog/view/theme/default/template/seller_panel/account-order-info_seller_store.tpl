<?php echo $header_seller; ?>
<div class="container-fluid ms-account-order-info">
  <ul class="breadcrumb">
    <!-- <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?> -->
  </ul>
  <?php if (isset($error_warning) && $error_warning) { ?>
  <div class="alert alert-danger warning main"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>

  <?php if (isset($success) && ($success)) { ?>
		<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?></div>
  <?php } ?>

    <?php if (isset($statustext) && ($statustext)) { ?>
        <div class="alert alert-<?php echo $statusclass; ?>"><?php echo $statustext; ?></div>
    <?php } ?>

  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>

	<!-- order information -->
	<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-book"></i> <?php echo $text_order_detail; ?></h3>
	</div>
	<table class="table table-responsive table-bordered">
		<tbody>
			<tr>
				<td style="width: 50%;">
					<b><?php echo $text_order_no; ?></b> #<?php echo $order_no; ?><br />
					<b><?php echo $ms_status; ?>:</b> <?php $status_name = $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $order_status_id)); 
					switch ($status_name) {
						case 'Processed':
							echo "Pickup Requested";
							break;
						
						default:
							echo "Pickup Done";
							break;
					}?><br />
					<b><?php echo $text_date_processed; ?></b> <?php echo $date_processed; ?></td>
			</tr>
		</tbody>
	</table>
	</div>
	<!-- address information -->
<?php if(in_array($store_id,$seller_stores)){ ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-book"></i> <?php echo "Address Information"; ?></h3>
		</div>
		<table class="table table-responsive table-bordered">
			<thead>
				<tr>
					<td><b>Ship To:</b></td>
					<td><b>Buyer Details</b></td>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					<address>
						<?php echo $payment_firstname; ?>
						<?php echo $payment_lastname; ?><br>
						<?php echo $payment_address_1; ?><br>
						<?php echo $payment_city; ?> -
						<?php echo $payment_postcode; ?> <br>
						<?php echo $payment_zone; ?> -
						<?php echo $payment_country; ?>
					</address>
					<b>Telephone: </b> <?php echo $telephone;?>
				</td>
				<td>
					<address>
						<?php echo $shipping_firstname; ?>
						<?php echo $shipping_lastname; ?><br>
						<?php echo $shipping_address_1; ?><br>
						<?php echo $shipping_city; ?> -
						<?php echo $shipping_postcode; ?><br>
						<?php echo $shipping_zone; ?> -
						<?php echo $shipping_country; ?>
					</address>
					<b>Telephone: </b> <?php echo $telephone;?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
<?php } ?>
	<!-- products -->
	<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $ms_account_products; ?></h3>
	</div>
	<table class="table table-responsive table-bordered text-center">
		<thead>
			<tr>

				<td class="left"><?php echo $column_photo; ?></td>
				<td class="left"><?php echo $column_sku; ?></td>
				<td class="left"><?php echo $column_comment; ?></td>
				<td class="right"><?php echo $column_no_of_set; ?></td>
				<td class="right"><?php echo $column_no_of_peices_in_set; ?></td>
				<td class="right"><?php echo $column_price_per_set; ?></td>		
				<td class="right"><?php echo $column_price_per_piece; ?></td>		
				<td class="right"><?php echo $column_total_inc_tax; ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($products as $product) { ?>
			<tr>
				<td class="left"><a href="<?php echo $product['href']; ?>" target="_blank"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a></td>
				<td class="left"><a href="<?php echo $product['href']; ?>" target="_blank"><?php echo $product['sku']; ?></a></td>
				<td class="left"><!--<a href="<?php echo $product['href']; ?>" target="_blank">--><?php echo $product['comment']; ?><!--</a>--></td>
				<td class="right"><?php echo $product['quantity']; ?></td>
				<td class="right"><?php echo $product['piece_in_set']; ?></td>
				<td class="right"><?php echo $product['price']; ?></td>		
				<td class="right"><?php echo $product['price_per_piece']; ?></td>
				<td class="right"><?php echo $product['total']; ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot style="text-align: center;">
			<?php foreach ($totals as $total) { ?>
			<tr>
				<td colspan="6"></td>
				<td><b><?php echo $total['title']; ?>:</b></td>
				<td><?php echo $this->currency->format($total['value'], $this->config->get('config_currency')); ?></td>
			</tr>
			<?php } ?>
		</tfoot>
	</table>
	</div>

	<!-- <div class="buttons">
		<div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><span><?php echo $button_back; ?></span></a></div>
	</div> -->
    </div>
    <?php echo $column_right; ?></div>
</div>

<?php echo $footer_seller; ?>
