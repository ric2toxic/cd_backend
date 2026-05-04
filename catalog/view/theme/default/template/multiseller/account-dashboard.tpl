<?php echo $header_seller; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>

  <?php if (isset($success) && $success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>

  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?> ms-account-dashboard card_box"><?php echo $content_top; ?>
    <h1><?php echo $ms_account_dashboard_heading; ?></h1>
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="tile">
				<div class="tile-heading"><?php  echo $text_total_order; ?></div>
				<div class="tile-body"><i class="fa fa-shopping-cart"></i>
					<h2 class="pull-right"><?php echo $total_order; ?></h2>
				</div>
				<div class="tile-footer"><a href="<?php echo $view_orders; ?>"><?php echo $text_view_more; ?></a></div>
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="tile">
				<div class="tile-heading"><?php  echo $text_total_sales; ?></div>
				<div class="tile-body"><i class="fa fa-credit-card"></i>
					<h2 class="pull-right"><?php echo $total_sales; ?></h2>
				</div>
				<div class="tile-footer"><a href="<?php echo $view_sales; ?>"><?php echo $text_view_more; ?></a></div>
			</div>
		</div>
		<?php if (isset($total_customer)) { ?>
		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="tile">
				<div class="tile-heading"><?php  echo $text_total_customer; ?></div>
				<div class="tile-body"><i class="fa fa-user"></i>
					<h2 class="pull-right"><?php echo $total_customer; ?></h2>
				</div>
				<div class="tile-footer"><a href="<?php echo $view_customer; ?>"><?php echo $text_view_more; ?></a></div>
			</div>
		</div>
		<?php } ?>
	</div>
	<h2><?php echo $ms_account_dashboard_orders; ?></h2>
	<table class="list table table-bordered">
		<thead>
			<tr>
				<td><?php echo $ms_account_orders_id; ?></td>
				<td><?php echo $ms_status; ?></td>
				<td><?php echo $ms_order_date; ?></td>
				<td><?php echo $ms_account_orders_total; ?></td>
			</tr>
		</thead>

		<tbody>
		<?php if (isset($orders) && $orders) { ?>
			<?php foreach ($orders as $order) { ?>
			<tr>
				<td><a href="<?php echo $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order['order_id'] . '&suborder_id=' . $order['suborder_id']); ?>" class="" title="<?php echo $this->language->get('ms_view_modify') ?>"><?php echo $order['order_no']; ?></a></td>
				<td><?php echo $order['status']; ?></td>
				<td><?php echo $order['order_date']; ?></td>
				<td><?php echo $order['total']; ?></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td class="center" colspan="7"><?php echo $ms_account_orders_noorders; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer_seller; ?>


<script>
	$(document).ready(function(){
		$('.tile').mouseenter(function(){
			$(this).find('.tile-body i').css({"color":"#fff","opacity":"1"});
		}).mouseleave(function(){
			$(this).find('.tile-body i').css("opacity","0.3");
		});
	});
</script>
