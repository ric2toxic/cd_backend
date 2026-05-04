<?php echo $header_seller; ?>
<div class="container ms-account-order-info">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
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
					<b><?php echo $ms_status; ?>:</b> <?php echo $order_status_name; ?><br />
					<b><?php echo $text_date_processed; ?></b> <?php echo $date_processed; ?></td>
			</tr>
		</tbody>
	</table>
	</div>

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
				<td class="right"><?php echo $column_price_per_set; ?></td>
				<td class="right"><?php echo $column_no_of_peices_in_set; ?></td>
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
				<td class="right"><?php echo $product['price']; ?></td>
				<td class="right"><?php echo $product['piece_in_set']; ?></td>
				<td class="right"><?php echo $product['total']; ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot style="text-align: center;">
			<?php foreach ($totals as $total) { ?>
			<tr>
				<td colspan="5"></td>
				<td><b><?php echo $total['title']; ?>:</b></td>
				<td><?php echo $total['text']; ?></td>
			</tr>
			<?php } ?>
		</tfoot>
	</table>
	</div>

	<div class="buttons">
		<div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><span><?php echo $button_back; ?></span></a></div>
	</div>
    <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<?php echo $footer_seller; ?>
