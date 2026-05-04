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
							<li class="active"><a href="<?php echo $pickup_requested; ?>">Pickup Requested</a></li>
							<li><a href="<?php echo $pickup_done; ?>">Pickup Done</a></li>
							<li><a href="<?php echo $tentative_orders; ?>">Tentative Orders</a></li>
						</ul>

						<div class="tab-content">
							<div id="tab-requested" >
								<div class="table-responsive">
						        <table class="list table table-bordered table-hover" id="list-requested-orders">
									<thead>
										<tr>
											<td class="small"><a class="pull-left" href="<?php echo $order_no_sort; ?>"> <?php echo $ms_account_orders_id; ?>
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
												</a></td>
											<td class="medium"><?php echo $ms_status; ?></td>
											<td class="medium"><a class="pull-left" href="<?php echo $date_added; ?>"><?php echo $ms_order_date; ?>
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
												</a></td>
											<td class="small">
                                                <?php echo $ms_account_orders_total; ?>
											</td>
											<td class="small"><?php echo $ms_action; ?></td>
										</tr>
										<tr>
											<td class="small"><input type="text" class="order_filter_wholesale" name="order_filter_wholesale" value="<?php echo $order_no_wholesale;?>"></td>
											<td class="small"></td>
											<td class="small"><input type="text" name="date_filter_wholesale" class="date_filter_wholesale date_processed" value="<?php echo $date_wholesale;?>"></td>
											<td class="small"></td>
											<td class="small">
												<button type="button" id="button-filter_wholesale" class="btn btn-primary button-filter_wholesale"><i class="fa fa-search"></i> Filter </button>
											</td>
										</tr>
									</thead>

									<tbody>
										<?php if (!empty($wsb_orders['pickup_requested'])) { ?>
										<?php foreach ($wsb_orders['pickup_requested']['data'] as $order_requested) { ?>
										<tr>
											<td><?php echo $order_requested['order_no']; ?></td>
											<td><?php echo $order_requested['suborder_status']; ?></td>
											<td><?php echo $order_requested['order_date']; ?></td>
											<td><?php echo $order_requested['total_amount']; ?></td>
											<td>
												<?php if($order_requested['seller_invoice']) { ?>
													<a href="<?php echo $order_requested['seller_invoice']; ?>"
													   class="btn btn-success btn-xs btn-view-invoice">View Invoice</a>
												<?php }else if($order_requested['invoice_no'] == 0){ ?>
													<button class="btn btn-info btn-xs btn-generate-invoice"
													   title="Generate Invoice"
													   data-order-id="<?php echo $order_requested['order_id']; ?>"
													   data-suborder-id="<?php echo $order_requested['suborder_id']; ?>"
													   id="btn-generate-invoice-<?php echo $order_requested['suborder_id']; ?>">Generate Invoice</button>
												<?php } ?>
											</td>
										</tr>
										<?php } ?>
										<?php } ?>
									</tbody>
								</table>
								<div class="breadcrumb row custom-pagination-class">
								    <div class="col-sm-6 text-left"><?php echo $pagination_wsb_requested; ?></div>
								    <div class="col-sm-6 text-right"><?php echo $results_wsb_requested; ?></div>
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
	* filters for wholesale store
	* */
	$('.button-filter_wholesale').click(function(){
		var url = "index.php?route=seller/account-order/pickupRequested";

		var order_filter = $('.order_filter_wholesale').val();
		if(order_filter){
			url += '&order_filter_wholesale='+ encodeURIComponent(order_filter);
		}

		var date_filter = $('.date_filter_wholesale').val();
		if(date_filter){
			url += '&date_filter_wholesale='+ encodeURIComponent(date_filter);
		}

		location = url;
	});


	$(document).ready(function(){
		$('.btn-generate-invoice').click(function(){
			var order_id = $(this).attr('data-order-id');
			var suborder_id = $(this).attr('data-suborder-id');
			$.ajax({
				type:'GET',
				url:'index.php?route=seller/account-order/generateSellerInvoiceNo',
				data: {'order_id':order_id,'suborder_id':suborder_id},
				beforeSend: function() {
					$('#btn-generate-invoice-'+suborder_id).button('loading');
				},
				success:function(data){
					var json_data = jQuery.parseJSON(data);
					if (json_data['link']) {
						$('#btn-generate-invoice-'+suborder_id).replaceWith('<a href="'+json_data['link']+'" class="btn btn-success btn-xs btn-view-invoice">View Invoice</a>');
					} else {
						alert('Invoice could not be generated. Please try again !');
					}
				}
			});
		});




	});

</script>
