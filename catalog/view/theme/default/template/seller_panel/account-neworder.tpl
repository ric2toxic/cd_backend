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
    			<li class="<?php if($get_store_id == ''){ echo 'active';}?>"><a href="#wholesale" data-toggle="tab">WholesaleBox<sup class="seller_store_order_numbers"><?php echo $total_wsb; ?></sup></a></li>
    			<?php foreach ($stores as $store) { ?>
				<?php if($store['store_id']==$get_store_id){ $class_active = 'active'; }else{ $class_active = ''; }?>
    				<li class="<?php echo $class_active; ?>"><a href="#store_<?php echo $store['store_id'] ?>" data-toggle="tab"><?php echo $store['name']; ?><sup class="seller_store_order_numbers"><?php echo $store['iTotalRecords']; ?></sup></a></li>
    			<?php } ?>
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
							<li class="active"><a href="#tab-requested" data-toggle="tab">Pickup Requested</a></li>
							<li><a href="#tab-done" data-toggle="tab">Pickup Done</a></li>
							<li><a href="#tab-tentative" data-toggle="tab">Tentative Orders</a></li>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="tab-requested" >
								<div class="table-responsive">
						        <table class="list table table-bordered table-hover" id="list-requested-orders">
									<thead>
										<tr>
											<td class="small"><a class="pull-left" href="<?php echo $order_no_sort; ?>"> <?php echo $ms_account_orders_id; ?>

													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'op.order_no' && $tab == 'wholesale'){ ?>
													<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'op.order_no' && $tab == 'wholesale'){ ?>
													<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
													<div class="arrows_sort pull-right">
														<i class="fa fa-caret-up"></i></br>
														<i class="fa fa-caret-down"></i>
													</div>
													<?php } ?>
												</a></td>							
											<td class="medium"><?php echo $ms_status; ?></td>
											<td class="medium"><a class="pull-left" href="<?php echo $date_processed; ?>"><?php echo $ms_date_processed; ?>

													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'date_processed' && $tab == 'wholesale'){ ?>
													<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'date_processed' && $tab == 'wholesale'){ ?>
													<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
													<div class="arrows_sort pull-right">
														<i class="fa fa-caret-up"></i></br>
														<i class="fa fa-caret-down"></i>
													</div>
													<?php } ?>
												</a></td>
											<td class="small"><a class="pull-left" href="<?php echo $total_amount; ?>"><?php echo $ms_account_orders_total; ?>

													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'total_amount' && $tab == 'wholesale'){ ?>
													<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'total_amount' && $tab == 'wholesale'){ ?>
													<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
													<div class="arrows_sort pull-right">
														<i class="fa fa-caret-up"></i></br>
														<i class="fa fa-caret-down"></i>
													</div>
													<?php } ?>
												</a></td>
											<td class="small"><?php echo $ms_action; ?></td>
											<!-- <td class="medium"><?php // echo $ms_invoice; ?></td> -->
											<!-- <td class="medium"><?php // echo $ms_payement_status; ?></td> -->
											<!-- <td class="medium"><?php // echo $ms_debit_note; ?></td> -->
										</tr>
										<!-- <tr class="filter">

							                <td><input class="order_no" type="text"/></td>
											<?php /* ?><td><input type="text"/></td><?php */ ?>
											<td></td>
											<?php /* ?><td><input type="text"/></td><?php */ ?>
											<td></td>
											<td><input type="text"/></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr> -->
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
											<td><?php echo $order_requested['date_processed']; ?></td>
											<td><?php echo $order_requested['total_amount']; ?></td>
											<td><?php echo $order_requested['view_order']; ?></td>
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

							<div class="tab-pane" id="tab-done">
								<div class="table-responsive">
						        <table class="list table table-bordered table-hover" id="list-done-orders">
								<thead>
									<tr>
										<td class="small">
											<a href="<?php echo $order_no_sort_done; ?>" class="pull-left"><?php echo $ms_account_orders_id; ?>
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'op.order_no' && $tab == 'done'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'op.order_no' && $tab == 'done'){ ?>
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
											<a class="pull-left" href="<?php echo $date_processed_done; ?>"><?php echo $ms_date_processed; ?>
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'date_processed' && $tab == 'done'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'date_processed' && $tab == 'done'){ ?>
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
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'total_amount' && $tab == 'done'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'total_amount' && $tab == 'done'){ ?>
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
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'payment_date' && $tab == 'done'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'payment_date' && $tab == 'done'){ ?>
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
												<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'debit_note_date' && $tab == 'done'){ ?>
												<i class="fa fa-caret-down"></i>
												<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'debit_note_date' && $tab == 'done'){ ?>
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
							<div class="tab-pane" id="tab-tentative">
								<div class="table-responsive">
									<span><?php echo $tentative_tooltip; ?></span>
									<table class="list table table-bordered table-hover" id="list-done-orders">
										<thead>
										<tr>
											<td class="small"><a class="pull-left" href="<?php echo $order_no_sort_tentative; ?>"><?php echo $ms_account_orders_id; ?>

													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'op.order_no' && $tab == 'tentative'){ ?>
													<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'op.order_no' && $tab == 'tentative'){ ?>
													<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
													<div class="arrows_sort pull-right">
														<i class="fa fa-caret-up"></i></br>
														<i class="fa fa-caret-down"></i>
													</div>
													<?php } ?>
												</a></td>
											<?php /* ?><td class="large"><?php echo $ms_account_orders_customer; ?></td> <?php */ ?>
											<td class="small"><?php echo $ms_status; ?></td>
											<?php /* ?><td><?php echo $ms_account_orders_products; ?></td><?php */ ?>

											<td class="small"><a class="pull-left" href="<?php echo $total_amount_tentative; ?>"><?php echo $ms_account_orders_total; ?>

													<?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'total_amount' && $tab == 'tentative'){ ?>
													<i class="fa fa-caret-down"></i>
													<?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'total_amount' && $tab == 'tentative'){ ?>
													<i class="fa fa-caret-up"></i>
													<?php }else{ ?>
													<div class="arrows_sort pull-right">
														<i class="fa fa-caret-up"></i></br>
														<i class="fa fa-caret-down"></i>
													</div>
													<?php } ?>
												</a></td>

										</tr>

										</thead>

										<tbody>
										<?php if (!empty($wsb_orders['tentative'])) { ?>
										<?php // echo "<pre>"; print_r($wsb_orders['tentative']['data']); exit; ?>
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
		var url = "index.php?route=seller/account-order";

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

	/*
	* filters for wholesale store
	* */

	$('.button-filter_wholesale').click(function(){
		var url = "index.php?route=seller/account-order";


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

	/*
	*  filters for pickup-done tab
	* */

	$('.button-filter_done').click(function(){
		var url = "index.php?route=seller/account-order";
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
			url += '#position-top@tab-done';
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