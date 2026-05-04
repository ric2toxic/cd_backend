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
		<br/>
		<div class="row">
			<div class="col-sm-12">
				<div class="tab-content">
					<?php foreach ($stores as $store) { ?>
						<?php
								// it is used for value set in input box and values get from controller
							if($store['store_id'] == $url_get_store_id){ $order_no = $order_filter;}else{ $order_no = '';}
							if($store['store_id'] == $url_get_store_id){ $customer = $customer_filter;}else{ $customer = '';}
							if($store['store_id'] == $url_get_store_id){ $customer_number = $customer_number_filter;}else{ $customer_number = '';}
							if($store['store_id'] == $url_get_store_id){ $customer_address = $customer_address_filter;}else{ $customer_address = '';}
							if($store['store_id'] == $url_get_store_id){ $status = $status_filter;}else{ $status = '';}
							if($store['store_id'] == $url_get_store_id){ $date = $date_filter;}else{ $date = '';}

						?>
					<?php if($store['store_id']==$get_store_id){ $class_active = 'active'; }else{ $class_active = ''; }?>
					<div></div>
					<div class="tab-pane <?php echo $class_active; ?> " id="store_<?php echo $store['store_id']; ?>">
						<div class="tab-content">
							<div class="table-responsive">
								<table class="list table table-bordered table-hover" id="table_<?php echo $store['store_id']; ?>">
									<thead>
										<tr>
											<td>
												<a class="pull-left" href="<?php echo $order_no_sort_demo; ?>&store_id=<?php echo $store['store_id']; ?>&tab=demo_<?php echo $store['store_id']; ?>#position-top@store_<?php echo $store['store_id']; ?>">
											 		<?php echo $ms_account_orders_id; ?>
													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'order_no' && $tab == 'demo_'.$store['store_id']){ ?>
														<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'order_no' && $tab == 'demo_'.$store['store_id']){ ?>
														<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
														<div class="arrows_sort pull-right">
															<i class="fa fa-caret-up"></i></br>
															<i class="fa fa-caret-down"></i>
														</div>
													<?php } ?>
												</a>
											</td>
											<td><?php echo $ms_account_orders_customer; ?></td>
											<td>Customer Number</td>
											<td>Customer Address</td>
											<td><?php echo $ms_status; ?></td>
											<td>
												<a class="pull-left" href="<?php echo $date_processed_demo; ?>&store_id=<?php echo $store['store_id']; ?>&tab=demo_<?php echo $store['store_id']; ?>#position-top@store_<?php echo $store['store_id']; ?>">
													<?php echo $ms_date_processed; ?>
													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'date_added' && $tab == 'demo_'.$store['store_id']){ ?>
														<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'date_added' && $tab == 'demo_'.$store['store_id']){ ?>
														<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
														<div class="arrows_sort pull-right">
															<i class="fa fa-caret-up"></i></br>
															<i class="fa fa-caret-down"></i>
														</div>
													<?php } ?>
												</a>
											</td>
											<td>
												<a class="pull-left" href="<?php echo $total_amount_demo; ?>&store_id=<?php echo $store['store_id']; ?>&tab=demo_<?php echo $store['store_id']; ?>#position-top@store_<?php echo $store['store_id']; ?>">
													<?php echo $ms_account_orders_total; ?>
													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'total' && $tab == 'demo_'.$store['store_id']){ ?>
														<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'total' && $tab == 'demo_'.$store['store_id']){ ?>
														<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
														<div class="arrows_sort pull-right">
															<i class="fa fa-caret-up"></i></br>
															<i class="fa fa-caret-down"></i>
														</div>
													<?php } ?>
												</a>
											</td>
											<td><?php echo $ms_action; ?></td>
										</tr>
										<tr>
											<input type="hidden" name="store_id_filter" value="<?php echo $store['store_id']; ?>">
											<td class="small"><input class="form-control order_filter_<?php echo $store['store_id']; ?>" type="text" name="order_filter" value="<?php echo $order_no;?>" ></td>
											<td class="small"><input class="form-control customer_filter_<?php echo $store['store_id']; ?>" type="text" name="customer_filter" value="<?php echo $customer;?>" ></td>
											<td class="small"><input class="form-control customer_number_filter_<?php echo $store['store_id']; ?>" type="text" name="customer_number_filter" value="<?php echo $customer_number;?>" ></td>
											<td class="small"><input class="form-control customer_address_filter_<?php echo $store['store_id']; ?>" type="text" name="customer_address_filter" value="<?php echo $customer_address;?>" ></td></td>
											<td class="small">
												<select class="form-control status_filter_<?php echo $store['store_id']; ?>" name= "status_filter">
													<option value="" >Select Status</option>
							                        <?php foreach($order_status_name as $status_name){ 
								                        if($status == $status_name['name']){
															$selected = 'selected="selected"';
								                        }else{
								                        	$selected = '';
								                        }  ?>
							                        	<option value="<?php echo $status_name['name']; ?>" <?php echo $selected; ?> ><?php echo $status_name['name']; ?></option>
							                        <?php } ?>
							                    </select>
											</td>
											<td class="small"><input class="form-control date_processed date_filter_<?php echo $store['store_id']; ?>" type="text" name="date_filter" value="<?php echo $date;?>" id="date_processed" ></td>
											<td class="small"></td>
											<td class="small">
												<button type="button" id="button-filter-<?php echo $store['store_id']; ?>" class="btn btn-primary button-filter" data-store-id="<?php echo $store['store_id']; ?>"><i class="fa fa-search"></i> Filter </button>
											</td>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($store['data'])) { ?>
											<?php foreach ($store['data'] as $store_order) { ?>
											<tr>
												<td><?php echo $store_order['order_no']; ?></td>
												<td><?php echo $store_order['customer_name']; ?></td>
												<td><?php echo $store_order['customer_number']; ?></td>
												<td><?php echo $store_order['customer_address']; ?></td>
												<td><?php echo $store_order['suborder_status']; ?></td>
												<td><?php echo $store_order['date_processed']; ?></td>
												<td><?php echo $store_order['total_amount']; ?></td>
												<td><?php echo $store_order['view_order']; ?></td>
											</tr>
											<?php } ?>
										<?php } ?>
									</tbody>
								</table>
								<div class="breadcrumb row custom-pagination-class">
									<div class="col-sm-6 text-left"><?php echo $store['pagination_seller_store']; ?></div>
									<div class="col-sm-6 text-right"><?php echo $store['results_seller_store']; ?></div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
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

	$('.button-filter').click(function(){
		var url = "index.php?route=seller/account-order/storeOrders";

		var store_id = $(this).attr('data-store-id');
		if(store_id){
			url += '&store_id='+ encodeURIComponent(store_id);
		}

		var order_filter = $('.order_filter_'+store_id).val();
		if(order_filter){
			url += '&order_filter='+ encodeURIComponent(order_filter);
		}

		var customer_filter = $('.customer_filter_'+store_id).val();
		if(customer_filter){
			url += '&customer_filter='+ encodeURIComponent(customer_filter);
		}

		var customer_number_filter = $('.customer_number_filter_'+store_id).val();
		if(customer_number_filter){
			url += '&customer_number_filter='+ encodeURIComponent(customer_number_filter);
		}

		var customer_address_filter = $('.customer_address_filter_'+store_id).val();
		if(customer_address_filter){
			url += '&customer_address_filter='+ encodeURIComponent(customer_address_filter);
		}

		var status_filter = $('.status_filter_'+store_id).val();
		if(status_filter){
			url += '&status_filter='+ encodeURIComponent(status_filter);
		}

		var date_filter = $('.date_filter_'+store_id).val();
		if(date_filter){
			url += '&date_filter='+ encodeURIComponent(date_filter);
		}

		location = url;
	});

	// Javascript to enable link to tab
	var url = document.location.toString();
	if (url.match('@')) {
		$('.nav-tabs a[href=#'+url.split('@')[1]+']').tab('show') ;
	}


</script>