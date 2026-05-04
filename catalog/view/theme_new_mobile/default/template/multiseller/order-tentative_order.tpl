<div id="position-top">
<?php echo $header_seller; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div class="row">
    	<div class="col-sm-12">
    		<ul class="nav nav-tabs seller_store_tabs">
    			<li class="<?php if($get_store_id == ''){ echo 'active';}?>"><a href="<?php echo $pickup_requested; ?>">WholesaleBox<sup class="seller_store_order_numbers"><?php echo $total_wsb; ?></sup></a></li>
    			<?php $i = 0; foreach ($stores as $store) { ?>
				<?php if($store['store_id']==$get_store_id){ $class_active = 'active'; }else{ $class_active = ''; }?>
    				<li class="<?php echo $class_active; ?>"><a href="<?php echo $store_orders[$i]; ?>"><?php echo $store['name']; ?><sup class="seller_store_order_numbers"><?php echo $store['iTotalRecords']; ?></sup></a></li>
    			<?php $i++; } ?>
    		</ul>
    	</div>
    </div>
    <div id="content" class="<?php echo $class; ?> ms-account-order card_box"><?php echo $content_top; ?>
		<h1><?php echo $ms_account_orders_heading; ?></h1>

		<div class="row">
			<div class="col-sm-12">
				<div class="tab-content">
					<div class="tab-pane <?php if($get_store_id == ''){ echo 'active';}?>" id="wholesale">
						<ul class="nav nav-tabs">
							<li><a href="<?php echo $pickup_requested; ?>">Pickup Requested</a></li>
							<li><a href="<?php echo $pickup_done; ?>">Pickup Done</a></li>
							<li class="active"><a href="<?php echo $tentative_orders; ?>">Tentative Orders</a></li>
						</ul>
						<div class="tab-content">
							<div id="tab-tentative">
								<div class="table-responsive">
									<span><?php echo $tentative_tooltip; ?></span>
									<table class="list table table-bordered table-hover" id="list-done-orders">
										<thead>
											<tr>
												<td class="small">
													<a class="pull-left" href="<?php echo $order_no_sort_tentative; ?>"><?php echo $ms_account_orders_id; ?>
														<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'op.order_no'){ ?>
														<i class="fa fa-caret-down"></i>
														<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'op.order_no'){ ?>
														<i class="fa fa-caret-up"></i>
														<?php }else{ ?>
														<div class="arrows_sort pull-right">
															<i class="fa fa-caret-up"></i></br>
															<i class="fa fa-caret-down"></i>
														</div>
														<?php } ?>
													</a>
												</td>
												<?php /* ?><td class="large"><?php echo $ms_account_orders_customer; ?></td> <?php */ ?>
												<td class="small"><?php echo $ms_status; ?></td>
												<?php /* ?><td><?php echo $ms_account_orders_products; ?></td><?php */ ?>

												<td class="small">
													<a class="pull-left" href="<?php echo $total_amount_tentative; ?>"><?php echo $ms_account_orders_total; ?>
														<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'total_amount'){ ?>
														<i class="fa fa-caret-down"></i>
														<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'total_amount'){ ?>
														<i class="fa fa-caret-up"></i>
														<?php }else{ ?>
														<div class="arrows_sort pull-right">
															<i class="fa fa-caret-up"></i></br>
															<i class="fa fa-caret-down"></i>
														</div>
														<?php } ?>
													</a>
											</td>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($wsb_orders['tentative'])) { ?>
												<?php foreach ($wsb_orders['tentative']['data'] as $order_done) { ?>
													<tr>
														<td><?php echo $order_done['order_no']; ?></td>
														<td><?php echo $order_done['suborder_status']; ?></td>
														<td><?php echo $order_done['total_amount']; ?></td>
													</tr>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
									<div class="breadcrumb row custom-pagination-class">
										<div class="col-sm-6 text-left"><?php echo $pagination_wsb_tentative; ?></div>
										<div class="col-sm-6 text-right"><?php echo $results_wsb_tentative; ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

      <div class="buttons clearfix">
        <div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><?php echo $button_back; ?></a>
        </div>
      </div>
      <?php echo $content_bottom; ?>
  </div>
    <?php echo $column_right; ?>
</div>
</div>
</div>
<?php echo $footer_seller; ?>
<script type="text/javascript">
	$(document).ready(function () {
		$(document).delegate('.date_processed', 'focus', function () {
			$(this).datetimepicker({
				format: 'YYYY-MM-DD'
			});
		});
		$('.view_order').click(function(){
	  	    var href = $(this).attr('data-href');
	        var href = href+'&popup=true';
	        $.fancybox({
	            'hideOnContentClick': true,
	            maxWidth: 980,
	            width:'100%',
	            padding:0,
	            type: 'iframe',
	            href: href,
	            iframe: {
	                preload: false // fixes issue with iframe and IE
	            }
	        });
		});
	});
</script>