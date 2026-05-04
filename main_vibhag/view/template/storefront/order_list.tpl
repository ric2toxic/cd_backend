<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">

      <h1><?php echo $heading_title; ?></h1>
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
        <div class="row">
          <div class="col-xs-4">
            <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
          </div>
          <div class="col-xs-4">
            <div class="form-group store_front">
              <label class="control-label" for="input-name"><?php echo $text_store_list; ?></label>
              <select title="Stores" name="filter_store_list" class="form-control select_store_list">
                <option value="all"><?php echo $text_select_store_list;?></option>
                <?php foreach ($stores as $store) { ?>
                <?php
                      if($store['store_id'] == $filter_store_list){
                        $selected_id = 'selected';
                      }else{
                        $selected_id = '';
                      }
                  ?>
                <option value="<?php echo $store['store_id']; ?>" <?php echo $selected_id;?>><?php echo $store['name'];?></option>
                <?php } ?>
              </select>
              <button type="button" class="btn btn-primary pull-right button-filter button_store_list"><?php echo 'Ok'; ?></button>
            </div>
          </div>
          <div class="col-xs-4"></div>
        </div>
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
              <?php /* ?><!--<div class="form-group">
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
              </div> --><?php */?>
              <div class="form-group">
                <label class="control-label" for="input-company"><?php echo $entry_company; ?></label>
                <input type="text" name="filter_company" value="<?php echo $filter_company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
              </div>
              <div class="form-group">
                  <label class="control-label" for="input-customer"><?php echo $entry_total; ?></label> <br>
                  <input type="text" name="filter_total_low" value="<?php echo $filter_total_low; ?>" placeholder="<?php echo $entry_total_low; ?>" id="input-total-low" class="form-control filter_total" />
                  <input type="text" name="filter_total_high" value="<?php echo $filter_total_high; ?>" placeholder="<?php echo $entry_total_high; ?>" id="input-total-high" class="form-control filter_total" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
              <div class="form-group">
                <label class="control-label" for="input-city"><?php echo $entry_city; ?></label>
                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
              </div>
              
              <!-- <div class="form-group">
                <label class="control-label" for="input-date-modified"><?php // echo $entry_date_modified; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_modified" value="<?php // echo $filter_date_modified; ?>" placeholder="<?php // echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div> -->
              
              <!-- <div class="form-group">
                <label class="control-label" for="input-total"><?php // echo $entry_total; ?></label>
                <input type="text" name="filter_total" value="<?php // echo $filter_total; ?>" placeholder="<?php // echo $entry_total; ?>" id="input-total" class="form-control" />
              </div> -->
              
              <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
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
                  <!--<td class="text-right"><?php echo $column_action; ?></td> -->
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                  <tr>
                  <td class="text-center"><?php if (in_array($order['order_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                    <?php } ?>
                    </td>
                  <td class="text-right"><strong><?php echo $order['order_no']; ?></strong> <br> <?php echo $order['store_name']; ?> </td>
                  <td class="text-left">
                    <?php if($order['count_order'] > 1){ ?>
                      <label class="customer_total_orders"><i>Total Orders < <?php echo $order['count_order']; ?> ></i></label><br>
                    <?php } ?>
                    <a href="<?php echo $order['customer_link']; ?>" target="_blank"><?php echo $order['customer'];?></a>

                    <div class="btn-group" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                      <button type="button" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs"><i class="fa fa-lock"></i></button>
                      <ul class="dropdown-menu pull-left">
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $order['customer_id']; ?>&store_id=0" target="_blank"><?php echo $text_default; ?></a></li>
                        <?php foreach ($stores as $store) { ?>
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $order['customer_id']; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
                        <?php } ?>
                      </ul>
                    </div>
                  </td>
                  <td class="text-left"><strong><?php echo $order['company']; ?></strong> <br>
                    <div class="order_list_comment"><?php echo $order['comment']; ?></div></td>
                  <td class="text-left"><strong><?php echo $order['city']; ?></strong></td>
                  <td class="text-left"><?php echo $order['payment_mode']; ?></td>
                  <td class="text-right"><?php echo $order['total'];
                    if ($order['advance_amount']) { ?>
                    <br/>
                    <label> <span class="advance_collected"><?php echo "Adv. ".$order['advance']; ?></span>
                    <?php } ?>
                  </td>
                  <td class="text-left"><?php echo $order['date_added']; ?></td>
                  <!-- <td class="text-left"><?php // echo $order['date_modified']; ?></td> -->
                  <?php /* ?>
                  <!--<td class="text-right">
                    <a href="<?php echo $order['view']; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                    <a href="<?php echo $order['delete']; ?>" id="button-delete<?php echo $order['order_id']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                  </td> -->
                  <?php */ ?>
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
$('.button-filter').on('click', function() {
	url = 'index.php?route=storefront/order&token=<?php echo $token; ?>';
	
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
	
	/*var filter_order_status = $('select[name=\'filter_order_status\']').val();
	
	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}	*/

	var filter_total = $('input[name=\'filter_total\']').val();

	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}	
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

    var filter_stores = $('select[name=\'filter_store_list\']').val();

    if (filter_stores) {
      url += '&filter_store_list=' + encodeURIComponent(filter_stores);
    }
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}

    var filter_total_low = $('input[name=\'filter_total_low\']').val();

    if (filter_total_low) {
        url += '&filter_total_low=' + encodeURIComponent(filter_total_low);
    }

    var filter_total_high = $('input[name=\'filter_total_high\']').val();

    if (filter_total_high) {
        url += '&filter_total_high=' + encodeURIComponent(filter_total_high);
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

<script type="text/javascript"><!--
$('input[name^=\'selected\']').on('change', function() {
	$('#button-detail-invoice, #button-b2b-invoice, #button-b2c-invoice').prop('disabled', true);
	
	var selected = $('input[name^=\'selected\']:checked');
	
	if (selected.length) {
		$('#button-detail-invoice, #button-b2b-invoice, #button-b2c-invoice').prop('disabled', false);
	}
	
	for (i = 0; i < selected.length; i++) {
		if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
			$('#button-shipping').prop('disabled', false);
			
			break;
		}
	}
});

$('input[name^=\'selected\']:first').trigger('change');

$('a[id^=\'button-delete\']').on('click', function(e) {
	e.preventDefault();
	
	if (confirm('<?php echo $text_confirm; ?>')) {
		location = $(this).attr('href');
	}
});
//--></script> 
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>
<script type="text/javascript">
    $('input, select').on('keypress',function(e){
        if (e.keyCode == 13) {
            $('#button-filter').trigger('click');
        }
    });
</script>
<style>
    .filter_total{
        width: 50% !important;
        float: left;
    }
</style>
