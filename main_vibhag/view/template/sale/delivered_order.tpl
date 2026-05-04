<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_delivered_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_delivered_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_order_no; ?></label>
                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $entry_order_no; ?>" id="input-order-no" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-company"><?php echo $entry_company; ?></label>
                <input type="text" name="filter_company" value="<?php echo $filter_company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-city"><?php echo $entry_city; ?></label>
                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_delivered; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_delivered; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                  <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
              
            </div>
          </div>
        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-right"><?php if ($sort == 'o.order_no') { ?>
                    <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_no; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order; ?>"><?php echo $column_order_no; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.shipping_company') { ?>
                    <a href="<?php echo $sort_company; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_company; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_company; ?>"><?php echo $column_company; ?></a>
                    <?php } ?></td>
                 <td class="text-left"><?php if ($sort == 'o.shipping_city') { ?>
                    <a href="<?php echo $sort_city; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_city; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_city; ?>"><?php echo $column_city; ?></a>
                    <?php } ?></td>
                  <td class="text-left">Pymt</td>
                  <td class="text-right"><?php if ($sort == 'o.total') { ?>
                    <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total_bill; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_total; ?>"><?php echo $column_total_bill; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <!-- <td class="text-left"><?php // if ($sort == 'o.date_modified') { ?>
                    <a href="<?php // echo $sort_date_modified; ?>" class="<?php // echo strtolower($order); ?>"><?php // echo $column_date_modified; ?></a>
                    <?php // } else { ?>
                    <a href="<?php // echo $sort_date_modified; ?>"><?php // echo $column_date_modified; ?></a>
                    <?php // } ?></td> -->
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                <tr>
                  <td class="text-right"><?php echo $order['order_no']; ?></td>
                  <td class="text-left"><?php echo $order['customer'];?>

                  <!-- Dropshipper Start-->
                  <?php if ($order['is_dropshipper'] == 1 || $order['is_dropshipper']==2 || $order['is_dropshipper']==3 ) { ?>
                        <label> <span class="dropshipper_heading drop_head<?php echo $order['is_dropshipper']; ?>">Dropshipper</span>
                        </label>
                        <?php } else { ?>
                        
                  <?php } ?>
                 <!--Dropshipper END--> 

                  </td>
                  <td class="text-left"><?php echo $order['company']; ?></td>
                  <td class="text-left"><?php echo $order['city']; ?></td>
                  <td class="text-left"><?php echo $order['payment_mode']; ?></td>
                  <td class="text-right"><?php echo $order['total'];
                    if ($order['advance']) { ?>
                    <br/>
                    <label> <span class="advance_collected"><?php echo "Adv. ".$order['advance']; ?></span>
                    <?php } ?>
                  </td>
                  <td class="text-left"><?php echo $order['date_added']; ?></td>
                  <!-- <td class="text-left"><?php // echo $order['date_modified']; ?></td> -->
                  <td class="text-right">
                  <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>

                  <a href="<?php echo $order['Client_Dispute']; ?>"order-id="<?php echo $order['order_id']; ?>" id="button-delete<?php echo $order['order_id']; ?>" data-toggle="tooltip" title="<?php echo $button_Client_Dispute; ?>" class="btn btn-danger client_dispute"><i class="fa fa-user"></i></a>

                  <a href="<?php echo $order['complete']; ?>" order-id="<?php echo $order['complete']; ?>" data-toggle="tooltip" title="<?php echo $button_Complete; ?>" class="btn btn-primary complete"><i class="fa fa-check"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=sale/delivered_order&token=<?php echo $token; ?>';
	
	var filter_order_no = $('input[name=\'filter_order_no\']').val();
	
	if (filter_order_no) {
		url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
	}
	
	var filter_customer = $('input[name=\'filter_customer\']').val();
	
	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}
    
    var filter_company = $('input[name=\'filter_company\']').val();
	
	if (filter_company) {
		url += '&filter_company=' + encodeURIComponent(filter_company);
	}
    
    var filter_city = $('input[name=\'filter_city\']').val();
	
	if (filter_city) {
		url += '&filter_city=' + encodeURIComponent(filter_city);
	}
	
	var filter_order_status = $('select[name=\'filter_order_status\']').val();
	
	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}	

	var filter_total = $('input[name=\'filter_total\']').val();

	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}	
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
				
	location = url;
});
//--></script> 

<!-- Auto complete scripts -->
<script type="text/javascript"><!--
$('input[name=\'filter_customer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_customer\']').val(item['label']);
	}	
});
</script>

<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript">
</script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
$('.complete').on('click', function() {
  var order_id = $(this).attr('order-id');
  var url = 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/order/history&order_id='+order_id;
  $.ajax({
    url: url,
    type: 'post',
    dataType: 'json',
    data: 'order_status_id= 5',
    success: function(json) {
     // alert(json);
    }
  });
});
$('.client_dispute').on('click', function() {
  var order_id = $(this).attr('order-id');
  var url = 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/order/history&order_id='+order_id;
  $.ajax({
    url: url,
    type: 'post',
    dataType: 'json',
    data: 'order_status_id= 6',
    success: function(json) {
      //alert(json);
    }
  });
});

//--></script></div>
<?php echo $footer; ?>