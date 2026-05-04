<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><!--<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>-->
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-customer').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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

          </div>
          <div class="col-xs-4"></div>
        </div>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                <input type="text" name="filter_telephone" value="<?php echo $filter_telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <br/>
                <label><input type="checkbox" name="filter_is_dropshipper" value="<?php $is_dropshipper; ?>" <?php echo ($filter_is_dropshipper == 1 || $filter_is_dropshipper == 2 || $filter_is_dropshipper == 3) ? 'checked="checked"' : ''; ?> class="control-label"/></label>
                <label class="control-label"><?php echo $entry_dropshipper; ?></label>
                <button type="button" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
            </div>  
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.telephone') { ?>
                    <a href="<?php echo $sort_telephone; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_telephone; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_telephone; ?>"><?php echo $column_telephone; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php if ($sort == 'cart_items') { ?>
                    <a href="<?php echo $sort_cart_items; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_cart_items; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_cart_items; ?>"><?php echo $column_cart_items; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.last_cart_modified') { ?> 
                    <a href="<?php echo $sort_last_cart_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_last_cart_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_last_cart_modified; ?>"><?php echo $column_last_cart_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'last_login') { ?> 
                    <a href="<?php echo $sort_last_login; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_last_login; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_last_login; ?>"><?php echo $column_last_login; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_ip; ?></td>
                  <td class="text-left"><?php if ($sort == 'c.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($customers) { ?>
                <?php foreach ($customers as $customer) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($customer['customer_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><strong><?php echo $customer['name'];?></strong>
                  <?php if ($customer['company'] or $customer['city']) { ?>
                    <br><?php echo trim($customer['company'] . ' ' . $customer['city']); ?>
                  <?php } ?>
                  <?php if($customer['is_dropshipper']==1 || $customer['is_dropshipper']==2 || $customer['is_dropshipper']==3 ){ ?>
                    <br><label class="dropshipper_heading drop_head<?php echo $customer['is_dropshipper']; ?>">
                      Dropshipper</label> 
                  <?php }else{
                  }?>
                  </td>
                  <td class="text-left">
                    <span class="click_to_see btn-primary btn-xs" data-field-type="email" data-field-value="<?php echo base64_encode($customer['email']); ?>" onClick="clickToSee(this)"> Click to see </span>
                  </td>
                  <td class="text-left">
                    <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($customer['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                    <?php if ($customer['app_installed']) { ?>
                    <br/>
                    <label> <span class="app_installed"><?php echo "App Installed"; ?></span>
                    <?php } ?>
                  </td>
                  
                  <td class="text-center"><?php echo $customer['cart_items']; ?></td>
                  <td class="text-left"><?php echo $customer['last_cart_modified']; ?></td>
                  <td class="text-left"><?php echo $customer['date_added']; ?></td>
                  <td class="text-left"><?php echo $customer['status']; ?></td>
                  <td class="text-left"><?php echo $customer['ip']; ?></td>
                  <td class="text-left"><?php echo $customer['last_login'];?></td>

                  <td class="text-right">
                    <div class="btn-group" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                      <button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><i class="fa fa-lock"></i></button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=0" target="_blank"><?php echo $text_default; ?></a></li>
                        <?php foreach ($stores as $store) { ?>
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
                        <?php } ?>
                      </ul>
                    </div>
                    <?php if ($customer['unlock']) { ?>
                    <a href="<?php echo $customer['unlock']; ?>" data-toggle="tooltip" title="<?php echo $button_unlock; ?>" class="btn btn-warning"><i class="fa fa-unlock"></i></a>
                    <?php } else { ?>
                    <button type="button" class="btn btn-warning" disabled><i class="fa fa-unlock"></i></button>
                    <?php } ?>
                    <a href="<?php echo $customer['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
	url = 'index.php?route=storefront/customer&token=<?php echo $token; ?>';
	
	var filter_name = $('input[name=\'filter_name\']').val();
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	
	var filter_email = $('input[name=\'filter_email\']').val();
	
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}
    
    var filter_telephone = $('input[name=\'filter_telephone\']').val();
	
	if (filter_telephone) {
		url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
	}
	
	var filter_status = $('select[name=\'filter_status\']').val();
	
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status); 
	}
		
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

    var filter_stores = $('select[name=\'filter_store_list\']').val();

    if (filter_stores) {
      url += '&filter_store_list=' + encodeURIComponent(filter_stores);
    }

  if( $('input[name=\'filter_is_dropshipper\']').is(":checked")){ 
    var $dropshipper_value = "1,2,3";
    filter_is_dropshipper = $dropshipper_value;
        if (filter_is_dropshipper) {
            url += '&filter_is_dropshipper=' + encodeURIComponent(filter_is_dropshipper);
        }
  }else{
    var $dropshipper_value = "0,1,2,3";
    filter_is_dropshipper = $dropshipper_value;
        if (filter_is_dropshipper) {
            url += '&filter_is_dropshipper=' + encodeURIComponent(filter_is_dropshipper);
        }
  }



  location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
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
		$('input[name=\'filter_name\']').val(item['label']);
	}	
});

$('input[name=\'filter_email\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['email'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_email\']').val(item['label']);
	}	
});

$('input[name=\'filter_telephone\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_telephone=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['telephone'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_telephone\']').val(item['label']);
	}	
});
//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?> 
