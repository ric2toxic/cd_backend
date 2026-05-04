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
									<div class="pull-right">
										<a href="<?php echo $download_order_pickup; ?>" class="download_order_pick_done btn btn-warning">Download</a>
									</div>
						        <table class="list table table-bordered table-hover" id="list-done-orders">
								<thead>
									<tr>
										<td class="text-left"><?php if ($sort == 'o.order_no') { ?>
											<a href="<?php echo $order_no; ?>" class="<?php echo strtolower($order); ?>"><?php echo $pickup_order; ?></a>
											<?php } else { ?>
											<a href="<?php echo $order_no; ?>"><?php echo $pickup_order; ?></a>
											<?php } ?>
										</td>
										<td class="text-left"><?php if ($sort == 'o.date_added') { ?>
											<a href="<?php echo $order_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $pickup_order_date; ?></a>
											<?php } else { ?>
											<a href="<?php echo $order_date; ?>"><?php echo $pickup_order_date; ?></a>
											<?php } ?>
										</td>
										<!-- <td class="text-left">
										WSBox Invoice Date
										</td> -->
										<td class="text-left">
                                            <?php echo $order_total_amt; ?>
										</td>
										<td class="small"><?php echo $order_payment_status; ?></td>
										<td class="medium"><?php echo $pickup_debit_note; ?></td>
										<td class="medium"><?php echo $pickup_debit_amt; ?></td>
										<td class="medium"><?php echo $pickup_remarks; ?></td>
										<td class="medium"><?php echo 'Action'; ?></td>
									</tr>
									<tr>
										<td class="small"><input type="text" name="filter_order_no" class="order_filter_done"  value="<?php echo $filter_order_no;?>" size="15"></td>
										<td class="small"><input type="text" name="filter_date_order" class="date_filter_done date_processed" value="<?php echo $filter_date_order;?>" size="10"></td>
										<td class="small"></td>
										<td class="small"></td>
										<td class="small"></td>
										<td class="small"></td>
										<td class="small"></td>
										<td class="small">
											<button type="button" id="button-filter" class="btn btn-primary button-filter"><i class="fa fa-search"></i> Filter </button>
										</td>
									</tr>
								</thead>

								<tbody>
								<?php //echo "<pre>"; print_r($order_data); die; ?>
									<?php if (!empty($order_data)) { ?>
									<?php foreach ($order_data as $order_done) { ?>
									<tr>
										<td><?php echo $order_done['order_no_popup']; ?></td>
										<td><?php echo $order_done['order_date']; ?></td>
										<!-- <td><?php //echo $order_done['wsb_invoice_date']; ?></td> -->
										<td><?php echo $order_done['total_amount']; ?></td>
										<td>
											<?php echo $order_done['payment_status']; ?> <br>
											<?php echo $order_done['payment_ref_no']; ?> <br>
											<?php echo $order_done['payment_date']; ?> <br>

										</td>
										<td>
											<?php if(!empty($order_done['debit_note'])) { ?>
											<select onchange="toggleDebitNo(this,'.debit_note_download_btn','.debit_note_amount','.debit_note_date')" name="debit_notes_no" class="btn btn-ms btn-default debit_notes_no">
												<option value="">--Select--</option>
												<?php foreach($order_done['debit_note'] as $key => $val) { ?>
													<option value="<?php echo $key;?>"
															data-amount="<?php echo $val;?>"
															data-url="<?php echo $order_done['debit_note_url'][$key]; ?>"
															data-date = "<?php echo $order_done['debit_note_date'][$key]; ?>" >
															<?php echo $key;?>
													</option>
												<?php } ?>
											</select>
											<p class="debit_note_date"></p>
											<?php } ?>
										</td>
										<td><span class="debit_note_amount"></span></td>
										<td><?php echo $order_done['remarks']; ?></td>
										<td>
											<p><a href="javascript:void(0);"
												data-href="index.php?route=seller/account-order/downloadDebitNotePdf&order_no=<?php echo $order_done['order_no']; ?>&debit_note_no="
												class="hidden btn btn-danger btn-xs debit_note_download_btn"
												data-order-id = "<?php echo $order_done['order_id']; ?>"
												title="Debit Note" >Debit Note
											</a></p>
											<?php if($order_done['seller_invoice']) { ?>
												<p><a href="<?php echo $order_done['seller_invoice']; ?>" class="btn btn-success btn-xs" title="View Invoice">View Invoice</a></p>
											<?php } ?>
										</td>
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
	function toggleDebitNo(obj,btn,amnt,dbt_date){
		var val = $(obj).val();
		$(obj).parents('tr').find(amnt).html('');
		if(val!=''){
			var option = $(obj).find('option[value="' + val + '"]');
			$(obj).parents('tr').find(btn).attr('href', option.data('url') );
			var amount = option.data('amount');
			var dbt_added_date = option.data('date');
			$(obj).parents('tr').find(amnt).html(amount);
			$(obj).parents('tr').find(dbt_date).html(dbt_added_date);
			$(obj).parents('tr').find(btn).removeClass('hidden');
		}else{
			$(obj).parents('tr').find(btn).addClass('hidden');
		}
	}
</script>

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

		$('select.debit_notes_no').each(function(){
			if($(this).find('option').length <= 2){
			   var value = $(this).find('option').eq(1).attr('value');
			   $(this).find('option[value="'+ value +'"]').prop('selected',true);
			   $(this).change();
			   $(this).attr('disabled',true);
			}
		});

	});

	/*
	*  filters for pickup-done tab
	* */

	$('.button-filter').click(function(){
		var url = "index.php?route=seller/account-order/pickupDone";

		var filter_order_no = $('input[name=\'filter_order_no\']').val();

		if (filter_order_no) {
			url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
		}

		var filter_date_order = $('input[name=\'filter_date_order\']').val();

		if (filter_date_order) {
			url += '&filter_date_order=' + encodeURIComponent(filter_date_order);
		}

		location = url;
	});


	$('input').on('keypress',function(e){
		if (e.keyCode == 13) {
			$('#button-filter').trigger('click');
		}
	});


	// Javascript to enable link to tab
	var url = document.location.toString();
	//var urls = url.replace('@1', '');
	//alert(url);
	if (url.match('@')) {
		$('.nav-tabs a[href=#'+url.split('@')[1]+']').tab('show') ;
	}

</script>
