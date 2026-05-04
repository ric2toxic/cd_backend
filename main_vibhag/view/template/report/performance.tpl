<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
		<div class="pull-right">
        <?php if( $show_csv_button ) { ?>
          <label>CSV:</label>
          <a href="<?php echo $b2c_csv; ?>" data-toggle="tooltip" id="download_csv" title="<?php echo $button_csv; ?>" class="btn btn-warning"><i class="fa fa-file-excel-o"></i></a>
        <?php } ?>
        </div>
      <h1><?php echo $heading_performance; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_performancereports_list; ?></h3>
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
                <label class="control-label" for="input-sales-staff"><?php echo $entry_sales_staff; ?></label>
                <select name="filter_sales_staff" id="input-sales-staff" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($sales_staff_list as $sales_staff_id => $name) { ?>
                  <?php if ($sales_staff_id == $filter_sales_staff and isset($filter_sales_staff)) { ?>
                  <option value="<?php echo $sales_staff_id; ?>" selected="selected"><?php echo $name; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $sales_staff_id; ?>"><?php echo $name; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label" for="input-payment-code"><?php echo $entry_payment_code; ?></label>
                <select name="filter_payment_code" id="input-payment-code" class="form-control">
                  <option value="*">--Select--</option>
                  <?php if($payment_codes){ ?>
                  <?php foreach ($payment_codes as $payment_code_val ) { ?>
                  <?php if ($payment_code_val == $filter_payment_code ) { ?>
                  <option value="<?php echo $payment_code_val; ?>" selected="selected"><?php echo ucwords($payment_code_val); ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $payment_code_val; ?>"><?php echo ucwords($payment_code_val); ?></option>
                  <?php } ?>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>

            </div>
            <div class="col-sm-4">
               <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_from" value="<?php echo $filter_date_from; ?>" placeholder="<?php echo $entry_date_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-city"><?php echo $entry_client_city; ?></label>
                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_client_city; ?>" id="input-city" class="form-control" />
              </div>
                <div class="form-group">
                    <br>
                     <br>
                <label>
                    <input type="checkbox" name="filter_franchise_order" value="<?php echo $filter_franchise_order; ?>" <?php echo !empty($filter_franchise_order) ? 'checked' : ''; ?> placeholder="<?php echo $entry_franchise_order; ?>" id="input-franchise-order" class="control-label" />
                <span>
                    <?php echo $entry_franchise_order; ?>
                </span>
                </label>
                
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_to" value="<?php echo $filter_date_to; ?>" placeholder="<?php echo $entry_date_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                <select name="filter_order_status" id="input-order-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_order_status == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
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
                  <td class="text-right">
                    <a><?php echo $entry_order_no; ?></a>
                  </td>
                  <!--<td class="text-left">
                    <a><?php echo $entry_date_added; ?></a>
                  </td> -->
                  <!--  <td class="text-left">
                    <a><?php echo $entry_custumer_id; ?></a>
                  </td>-->
                  <td class="text-left">
                    <a><?php echo $entry_client_name; ?></a>
                  </td>
                  <td class="text-left">
                    <a><?php echo $entry_company; ?></a>
                  </td>
                  <td class="text-left">
                    <a><?php echo $entry_client_city; ?></a>
                  </td>
                  <td class="text-left">
                    <a><?php echo $entry_client_phone; ?></a>
                  </td>
                  <td class="text-center">
                    <a><?php echo $entry_sales_staff; ?></a>
                  </td>
                   <td class="text-right">
                    <a><?php echo $entry_client_total; ?></a>
                  </td>
                  <td>
                    <table class="table table-bordered">
                      <tr>
                        <td>Suborder No</td>
                        <td>Total</td>
                        <td>Status</td>
                        <td>Delivery Time</td>
                        <td>Shipping Method</td>
                        <td>Action</td>
                      </tr>
                    </table>
                  </td>
                  <!--<td class="text-left">
                    <a><?php echo $entry_ship_pin_code; ?></a>
                  </td> -->
                 
                  <!--<td class="text-center">
                    <a><?php echo $entry_order_status; ?></a>
                  </td>-->
                </tr>
              </thead>
              <tbody>
                <?php if ( $orders ) { ?>
                  <?php foreach ($orders as $order) { ?>
                      <tr>
                        <td class="text-right">
                          <b><?php echo $order['order_no']; ?></b><br>
                          <?php echo  date('d-m-Y',strtotime($order['date_added'])); ?>
                        </td>
                        <td class="text-left">
                          <strong><?php echo $order['client_name'];?> </strong> <br>
                          C_ID: <?php echo $order['customer_id']; ?>  
                          </td>
                        <td class="text-left order_list_comment">
                          <?php if($order['count_order'] > 0){ ?>
                            <label class="customer_total_orders">
                              <a href="<?php echo $order['total_order_link']; ?>" target="_blank">
                                Total Orders:  <?php echo $order['count_order']; ?>
                              </a>
                            </label><br>
                          <?php } ?>
                          <strong><?php echo $order['company'];?></strong><br>
                          <?php echo $order['comment'];?><br>
                        </td>
                        <td class="text-left order_list_comment">
                          <strong><?php echo $order['shipping_city']; ?></strong> <br>
                          <?php echo $order['shipping_postcode']; ?>
                        </td>
                        <td class="text-right">
                          <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($order['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                        </td>
                        <td class="text-center">
                          <?php foreach($order['sales_staff_name'] as $staff){
                            echo $staff."<br>";
                          } ?>
                        </td>
                        <td class="text-right"><?php echo $order['total']; ?></td>
                        <!--<td class="text-center"><?php echo $order['sales_staff_name']; ?></td> -->
                        <td>
                        <?php if( !empty( $order['suborders'] ) ) { ?>
                          <table class="table table-bordered">
                            <?php foreach($order['suborders'] as $key_suborder_id => $suborder_data){ ?>
                              <tr>
                                <td class="text-center"><?php echo $key_suborder_id; ?></td>
                                <td><?php echo $suborder_data['total']; ?></td>
                                <td>
                                  <strong> <?php echo $suborder_data['status'];  ?> </strong><br>
                                  <div class="order_list_comment"><?php echo $suborder_data['last_update_history']; ?></div>
                                </td>
                                <td>
                                  <strong> <?php echo ($suborder_data['delivery_time']) ? $suborder_data['delivery_time'] : '&nbsp';  ?> </strong><br>
                                </td>
                                <td><?php echo $suborder_data['shipping_method'];?></td>
                                <?php if ( $suborder_view_button ) { ?>
                                  <td class="">
                                    <a href="<?php echo $suborder_data['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info" target= "_blank"><i class="fa fa-eye"></i></a>
                                  </td>
                                <?php } ?>
                              </tr>
                            <?php } ?>
                          </table>
                        <?php } ?>  
                        </td>
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
$('#input-order-status').change(function(){  

  url = 'index.php?route=report/performance/csv&token=<?php echo $token; ?>';

  var filter_order_no = $('input[name=\'filter_order_no\']').val();

  if (filter_order_no) {
    url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
  }

  var filter_date_from = $('input[name=\'filter_date_from\']').val();

  if (filter_date_from) {
    url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
  }

  var filter_date_to = $('input[name=\'filter_date_to\']').val();

  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

    var filter_sales_staff = $('select[name=\'filter_sales_staff\']').val();

  if (filter_sales_staff != '*') {
    url += '&filter_sales_staff=' + encodeURIComponent(filter_sales_staff);
  }

    var filter_city = $('input[name=\'filter_city\']').val();

    if (filter_city) {
      url += '&filter_city=' + encodeURIComponent(filter_city);
    }

    var filter_order_status = $('select[name=\'filter_order_status\']').val();

    if (filter_order_status != '*') {
      url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }
    
    var filter_payment_code = $('select[name=\'filter_payment_code\']').val();

    if (filter_payment_code != '*') {
      url += '&filter_payment_code=' + encodeURIComponent(filter_payment_code);
    }
    $('#download_csv').attr('href',url);

});  
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/performance&token=<?php echo $token; ?>';

	var filter_order_no = $('input[name=\'filter_order_no\']').val();

	if (filter_order_no) {
		url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
	}

	var filter_date_from = $('input[name=\'filter_date_from\']').val();

	if (filter_date_from) {
		url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
	}

	var filter_date_to = $('input[name=\'filter_date_to\']').val();

	if (filter_date_to) {
		url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
	}

    var filter_sales_staff = $('select[name=\'filter_sales_staff\']').val();

	if (filter_sales_staff != '*') {
		url += '&filter_sales_staff=' + encodeURIComponent(filter_sales_staff);
	}

    var filter_city = $('input[name=\'filter_city\']').val();

    if (filter_city) {
      url += '&filter_city=' + encodeURIComponent(filter_city);
    }

    var filter_order_status = $('select[name=\'filter_order_status\']').val();

    if (filter_order_status != '*') {
      url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }
    
    var filter_payment_code = $('select[name=\'filter_payment_code\']').val();

    if (filter_payment_code != '*') {
      url += '&filter_payment_code=' + encodeURIComponent(filter_payment_code);
    }
    // for franchise order check
    if($('#input-franchise-order').prop('checked') == true) {
        var filter_franchise_order = 1;

        if (filter_franchise_order != '*') {
          url += '&filter_franchise_order=' + encodeURIComponent(filter_franchise_order);
        }
    }
    
    

	location = url;
});
//--></script>

<!-- Auto complete scripts -->
<script type="text/javascript"><!--
$('input[name=\'filter_order_no\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=report/performance/autocomplete&token=<?php echo $token; ?>&filter_order_no=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['order_no']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_order_no\']').val(item['label']);
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

//--></script></div>
<?php echo $footer; ?>
<script type="text/javascript">
  $(document).ready(function(){
    $('.sub_order').click(function(){
      //$('.sub_order_id').hide();
        var id = $(this).attr('data-id');
        $('#sub_order_id_'+id).toggle();
      });
  });
</script>