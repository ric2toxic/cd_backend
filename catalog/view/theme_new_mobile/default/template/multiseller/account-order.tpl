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
    <div id="content" class="<?php echo $class; ?> ms-account-order card_box"><?php echo $content_top; ?>
		<h1><?php echo $ms_account_orders_heading; ?></h1>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-requested" data-toggle="tab">Pickup Requested</a></li>
			<li><a href="#tab-done" data-toggle="tab">Pickup Done</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab-requested" >
				<div class="table-responsive">
		        <table class="list table table-bordered table-hover" id="list-requested-orders">
				<thead>
					<tr>
						<td class="small"><?php echo $ms_account_orders_id; ?></td>
						<?php /* ?><td class="large"><?php echo $ms_account_orders_customer; ?></td> <?php */ ?>
						<td class="medium"><?php echo $ms_status; ?></td>
						<?php /* ?><td><?php echo $ms_account_orders_products; ?></td><?php */ ?>
						<td class="medium"><?php echo $ms_date_processed; ?></td>
						<td class="small"><?php echo $ms_account_orders_total; ?></td>
						<td class="small"><?php echo $ms_action; ?></td>
						<!-- <td class="medium"><?php // echo $ms_invoice; ?></td> -->
						<!-- <td class="medium"><?php // echo $ms_payement_status; ?></td> -->
						<!-- <td class="medium"><?php // echo $ms_debit_note; ?></td> -->
					</tr>
					<tr class="filter">

		                <td><input class="order_no" type="text"/></td>
						<?php /* ?><td><input type="text"/></td><?php */ ?>
						<td></td>
						<?php /* ?><td><input type="text"/></td><?php */ ?>
						<td></td>
						<td><input type="text"/></td>
						<td></td>
						<!-- <td></td> -->
						<!-- <td></td> -->
						<!-- <td></td> -->
					</tr>
				</thead>
				
				<tbody></tbody>
				</table>
				</div>				
			</div>
		
			<div class="tab-pane" id="tab-done">
				<div class="table-responsive">
		        <table class="list table table-bordered table-hover" id="list-done-orders">
				<thead>
					<tr>
						<td class="small"><?php echo $ms_account_orders_id; ?></td>
						<?php /* ?><td class="large"><?php echo $ms_account_orders_customer; ?></td> <?php */ ?>
						<td class="small"><?php echo $ms_status; ?></td>
						<?php /* ?><td><?php echo $ms_account_orders_products; ?></td><?php */ ?>
						<td class="small"><?php echo $ms_date_created; ?></td>
						<td class="small"><?php echo $ms_account_orders_total; ?></td>
						<td class="small"><?php echo $ms_action; ?></td>
						<td class="medium"><?php echo $ms_invoice; ?></td>
						<td class="small"><?php echo $ms_payement_status; ?></td>
						<td class="medium"><?php echo $ms_debit_note; ?></td>
						<td class="medium"><?php echo $ms_debit_amount; ?></td>
						<td class="medium"><?php echo $ms_remarks; ?></td>
					</tr>
					<tr class="filter">

		                <td><input class="order_no" type="text"/></td>
						<?php /* ?><td><input type="text"/></td><?php */ ?>
						<td></td>
						<?php /* ?><td><input type="text"/></td><?php */ ?>
						<td><input  type="text"/></td>
						<td><input type="text"/></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</thead>
				
				<tbody></tbody>
				</table>
				</div>
			</div>
		</div>

      <div class="buttons clearfix">
        <div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<script>
	$(function() {
		$('#list-requested-orders').DataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-order/getTableDataRequested",
			"aoColumns": [
                //{ "mData": "order_id"},
                { "mData": "order_no"},
				//{ "mData": "customer_name" },
				{ "mData": "suborder_status", "bSortable": true },
				//{ "mData": "products", "bSortable": false, "sClass": "products" },
				//{ "mData": "date_created" },
                { "mData": "date_processed" },
				{ "mData": "total_amount" },
				{ "mData": "view_order" }
				// { "mData": "invoice_status" },
				// { "mData": "payement_status" },
				// { "mData": "remarks" }
			],
			"aaSorting":  [[0,'desc']]
		});
	});	
	$(function() {
		$('#list-done-orders').DataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-order/getTableDataDone",
			"aoColumns": [
                //{ "mData": "order_id"},
                { "mData": "order_no"},
				//{ "mData": "customer_name" },
				{ "mData": "suborder_status", "bSortable": true },
				//{ "mData": "products", "bSortable": false, "sClass": "products" },
				{ "mData": "date_created" },
				{ "mData": "total_amount" },
				{ "mData": "view_order" },
				{ "mData": "invoice_status" },
				{ "mData": "payement_status" },
				{ "mData": "debitnote" },
				{ "mData": "debit_amount" },
				{ "mData": "remarks" }
			],
			"aaSorting":  [[0,'desc']]
		});
	});
	        // Javascript to enable link to tab
	        var url = document.location.toString();
	        if (url.match('#')) {
	            $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
	        } 

	        // Change hash for page-reload
	        $('.nav-tabs a').on('shown.bs.tab', function (e) {
	            window.location.hash = e.target.hash;
	        })

</script>

<?php echo $footer_seller; ?>