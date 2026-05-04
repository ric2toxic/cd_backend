<?php echo $header_seller; ?>

<?php if($popup == true){ ?>
	<div class="container-fluid ms-account-order-info">
<?php }else{ ?>
	<div class="container ms-account-order-info">
<?php } ?>
<br>
 <?php if($show_store_sales_notice){ ?>
<div class="alert alert-warning alert-dismissible" role="alert" style="color: #8a6d3b;letter-spacing: 0.5px;padding:10px">
  <button type="button" style="right:-2px;top:-8px;" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Note: </strong>For the highlighted items in yellow color, NO pickup is required. These are sold from WholesaleBox stores directly. Only invoice needs to be generated against them
</div>
<?php } ?>
  <!-- <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>-->
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
					<b>Order No: </b> #<?php echo $order_no; ?><br />
					<b>Order Status: </b> <?php echo $order_status_name; ?><br />
					<b>Date Processed: </b> <?php echo $date_processed; ?></td>
			</tr>
		</tbody>
	</table>
	</div>
	<!-- products -->
	<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Order Products: </h3>
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
			<tr style="background-color:<?php echo $product['store_sales'] != 'NO' ? '#fff8c3' : 'inherit'; ?>">
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
				<td><?php echo $total['text']; ?></td>
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
