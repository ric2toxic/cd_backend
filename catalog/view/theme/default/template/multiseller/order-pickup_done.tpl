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
							<li class="active"><a href="<?php echo $pickup_done; ?>">Pickup Done</a></li>
							<li><a href="<?php echo $tentative_orders; ?>">Tentative Orders</a></li>
						</ul>

						<div class="tab-content">							
							<div id="tab-done">
								<div class="table-responsive">
						        <table class="list table table-bordered table-hover" id="list-done-orders">
								<thead>
									<tr>
										<td class="small">
											<a href="<?php echo $order_no_sort_done; ?>" class="pull-left"><?php echo $ms_account_orders_id; ?>
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
										<td class="small"><?php echo $ms_status; ?></td>
										<td class="small">
											<a class="pull-left" href="<?php echo $date_processed_done; ?>"><?php echo $ms_order_date; ?>
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'date_added'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'date_added'){ ?>
												<i class="fa fa-caret-up"></i>
												<?php }else{ ?>
												<div class="arrows_sort pull-right">
													<i class="fa fa-caret-up"></i></br>
													<i class="fa fa-caret-down"></i>
												</div>
												<?php } ?>
											</a>
										</td>
										<td class="small">
											<a class="pull-left" href="<?php echo $total_amount_done; ?>"><?php echo $ms_account_orders_total; ?>
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
										<td class="small">
											<a class="pull-left" href="<?php echo $payment_status_done; ?>"><?php echo $ms_payement_status; ?>
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'payment_date'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'payment_date'){ ?>
												<i class="fa fa-caret-up"></i>
												<?php }else{ ?>
												<div class="arrows_sort pull-right">
													<i class="fa fa-caret-up"></i></br>
													<i class="fa fa-caret-down"></i>
												</div>
												<?php } ?>
											</a>
										</td>
										<td class="medium">
											<a class="pull-left" href="<?php echo $debit_note_done; ?>"><?php echo $ms_debit_note; ?>
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'debit_note_date'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'debit_note_date'){ ?>
												<i class="fa fa-caret-up"></i>
												<?php }else{ ?>
												<div class="arrows_sort pull-right">
													<i class="fa fa-caret-up"></i></br>
													<i class="fa fa-caret-down"></i>
												</div>
												<?php } ?>
											</a>
										</td>
										<td class="medium"><?php echo $ms_debit_amount; ?></td>
										<td class="medium"><?php echo $ms_remarks; ?></td>
									</tr>
									<tr>
										<td class="small"><input type="text" class="order_filter_done" name="order_filter_wholesale" value="<?php echo $order_no_done;?>"></td>
										<td class="small"></td>
										<td class="small"><input type="text" name="date_filter_done" class="date_filter_done date_processed" value="<?php echo $date_done;?>"></td>
										<td class="small"></td>
										<td class="small"><input type="text" name="payment_date_filter_done" value="<?php echo $payment_date_done; ?>" class="date_processed payment_date_filter_done"></td>
										<td class="small"><input type="text" name="debit_note_date_filter_done" value="<?php echo $debit_note_date_done; ?>" id="date_processed" class="date_processed debit_note_date_filter_done"></td>
										<td class="small"></td>
										<td class="small">
											<button type="button" id="button-filter_done" class="btn btn-primary button-filter_done"><i class="fa fa-search"></i> Filter </button>
										</td>
									</tr>
								</thead>

								<tbody>
									<?php if (!empty($wsb_orders['pickup_done'])) { ?>
									<?php foreach ($wsb_orders['pickup_done']['data'] as $order_done) { ?>
									<tr>
										<td><?php echo $order_done['order_no']; ?></td>
										<td><?php echo $order_done['suborder_status']; ?></td>
										<td><?php echo $order_done['date_processed']; ?></td>
										<td><?php echo $order_done['total_amount']; ?></td>
										<td><?php echo $order_done['payement_status']; ?></td>
										<td><?php echo $order_done['debitnote']; ?></td>
										<td><?php echo $order_done['debit_amount']; ?></td>
										<td><?php echo $order_done['remarks']; ?></td>
									</tr>
									<?php } ?>
									<?php } ?>
								</tbody>
								</table>
								<div class="breadcrumb row custom-pagination-class">
								    <div class="col-sm-6 text-left"><?php echo $pagination_wsb_done; ?></div>
								    <div class="col-sm-6 text-right"><?php echo $results_wsb_done; ?></div>
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

	/*
	*  filters for pickup-done tab
	* */

	$('.button-filter_done').click(function(){
		var url = "index.php?route=seller/account-order/pickupDone";
		var date_filter = $('.date_filter_done').val();
		var order_filter = $('.order_filter_done').val();
		var payment_date_filter_done = $('.payment_date_filter_done').val();
		var debit_note_date_filter_done = $('.debit_note_date_filter_done').val();

		if(order_filter || date_filter || payment_date_filter_done || debit_note_date_filter_done){
			if(order_filter !=''){
				url += '&order_filter_done='+ encodeURIComponent(order_filter);
			}
			if(date_filter != ''){
				url += '&date_filter_done='+ encodeURIComponent(date_filter);
			}
			if(payment_date_filter_done != ''){
				url += '&payment_date_filter_done='+ encodeURIComponent(payment_date_filter_done);
			}
			if(debit_note_date_filter_done != ''){
				url += '&debit_note_date_filter_done='+ encodeURIComponent(debit_note_date_filter_done);
			}
		}
		location = url;
	});

	// Javascript to enable link to tab
	var url = document.location.toString();
	//var urls = url.replace('@1', '');
	//alert(url);
	if (url.match('@')) {
		$('.nav-tabs a[href=#'+url.split('@')[1]+']').tab('show') ;
	}


</script>