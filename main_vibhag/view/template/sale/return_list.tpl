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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_order_no; ?></label>
                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $entry_order_no; ?>" id="input-order-no" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-debit-note-no"><?php echo $entry_debit_note_no; ?></label>
                <input type="text" name="filter_debit_note_no" value="<?php echo $filter_debit_note_no; ?>" placeholder="<?php echo $entry_debit_note_no; ?>" id="input-debit-note-no" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-debit-note-no">Return Request Date From</label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_return_request_date_added_from"
                           type="text"
                           value="<?php echo $filter_return_request_date_added_from; ?>"
                           placeholder="Return Request Date From"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-debit-note-date-from"><?php echo $entry_debit_note_date_from; ?></label>
                <div class="input-group date">
                  <input id="input-debit-note-date-from" class="form-control" name="filter_debit_note_date_from" type="text" value="<?php echo $filter_debit_note_date_from; ?>" placeholder="<?php echo $entry_debit_note_date_from; ?>" data-date-format="YYYY-MM-DD" type="text" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-debit-note-no">Return Request Date To</label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_return_request_date_added_to"
                           type="text"
                           value="<?php echo $filter_return_request_date_added_to; ?>"
                           placeholder="Return Request Date To"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-company"><?php echo $entry_company; ?></label>
                <input type="text" name="filter_company" value="<?php echo $filter_company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-product" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-debit-note-date-to"><?php echo $entry_debit_note_date_to; ?></label>
                <div class="input-group date">
                  <input id="input-debit-note-date-to" class="form-control" name="filter_debit_note_date_to" type="text" value="<?php echo $filter_debit_note_date_to; ?>" placeholder="<?php echo $entry_debit_note_date_to; ?>" data-date-format="YYYY-MM-DD" type="text" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="filter_return_replacement">Return / Replacement</label>
                <select name="filter_return_replacement" class="form-control">
                  <option value="">--Select(All)--</option>
                  <?php foreach($getReturnReplace as $rr){?>
                    <?php $selected = '';
                    if($rr == $filter_return_replacement) { $selected = 'selected'; } ?>
                    <option value="<?php echo $rr;?>" <?php echo $selected; ?> ><?php echo $rr;?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-city"><?php echo $entry_city; ?></label>
                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-model" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-return-action"><?php echo $entry_return_action; ?></label>
                <select name="filter_return_action" class="form-control">
                  <option value="">--Select(All)--</option>
                  <?php foreach($getReturnAction as $return_action){?>
                    <?php if($return_action['return_action_id'] == $filter_return_action) { $selected = 'selected'; } else { $selected = '';}?>
                    <option value="<?php echo $return_action['return_action_id'];?>" <?php echo $selected; ?> ><?php echo $return_action['name'];?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="filter_dn_generated">Debit Note Generated Or Not</label>
                <select name="filter_dn_generated" class="form-control">
                  <option value="">--Select(All)--</option>
                  <?php foreach($isDNGenerated as $rr){?>
                    <?php $selected = '';
                    if($rr == $filter_dn_generated) { $selected = 'selected'; } ?>
                    <option value="<?php echo $rr;?>" <?php echo $selected; ?> ><?php echo $rr;?></option>
                  <?php } ?>
                </select>
              </div>
              <a href="<?php echo @$download; ?>" style="margin-left:10px;" class="btn btn-primary pull-right"><i class="fa fa-download"></i>Download CSV</a>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center">
                    <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                    </td>
                  <td class="text-right"><?php echo $column_order_no; ?></td>
                  <td class="text-left"><?php echo $column_customer; ?> / Mobile </td>
                  <td class="text-left"><?php echo $column_email; ?></td>
                  <td class="text-left"><?php echo $column_company; ?><br>
                    (<?php echo $column_city; ?>)
                  </td>
                  <td class="text-left">DebitNote</td>
                  <td class="text-left">Order Product Id</td>
                  <td class="text-left"><?php echo $column_master_return_id; ?></td>
                  <td class="text-left">Return Request Date</td>
                  <td><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($returns) { ?>
                <?php foreach ($returns as $return) { ?>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" name="selected[]" value="" />
                  </td>
                  <td class="text-right"><?php echo $return['order_no']; ?></td>
                  <td class="text-left"><?php echo $return['customer']; ?><br>
                    <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($return['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                  </td>
                  <td class="text-left">
                    <span class="click_to_see btn-primary btn-xs" data-field-type="email" data-field-value="<?php echo base64_encode($return['email']); ?>" onClick="clickToSee(this)"> Click to see </span>
                  </td>
                  <td class="text-left order_list_comment">
                    <?php echo $return['company']; ?><br>
                    (<?php echo $return['city']; ?>)
                  </td>
                  <td class="text-left">
                    <?php if(!empty($return['dn_details'])){ ?>
                      <table>
                        <?php foreach($return['dn_details'] as $dn){ ?>
                        <?php $dn_data = explode('#', $dn);?>
                        <tr>
                            <td>
                              <input type="hidden" name="hddn_dn_id" value="<?php echo $dn_data[0];?>">
                              <?php echo $dn_data[1].$dn_data[2]; ?>
                            </td>
                            <td><?php echo $dn_data[3]; ?></td>
                            <td><?php echo $dn_data[4]; ?></td>
                        </tr>
                        <?php } ?>
                      </table>
                    <?php }?>
                  </td>
                  <td class="text-left"><?php echo $return['order_product_id']; ?></td>
                  <td class="text-left"><?php echo $return['master_return_id']; ?></td>
                  <td class="text-left"><?php echo $return['return_request_date']; ?></td>
                  <td class="text-right"><a href="<?php echo $return['view']; ?>" data-toggle="tooltip" title="<?php echo $button_return_order; ?>" class="btn btn-danger"><i class="fa fa-reply"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
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
</div>
<?php echo $footer; ?>
<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=sale/return&token=<?php echo $token; ?>';

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

  var filter_return_replacement = $('select[name=\'filter_return_replacement\']').val();
  if (filter_return_replacement) {
    url += '&filter_return_replacement=' + encodeURIComponent(filter_return_replacement);
  }

  var filter_dn_generated = $('select[name=\'filter_dn_generated\']').val();
  if (filter_dn_generated) {
    url += '&filter_dn_generated=' + encodeURIComponent(filter_dn_generated);
  }

  var filter_return_action = $('select[name=\'filter_return_action\']').val();

  if (filter_return_action) {
    url += '&filter_return_action=' + encodeURIComponent(filter_return_action);
  }

  var filter_debit_note_no = $('input[name=\'filter_debit_note_no\']').val();

  if (filter_debit_note_no) {
    url += '&filter_debit_note_no=' + encodeURIComponent(filter_debit_note_no);
  }

  var filter_debit_note_date_from = $('input[name=\'filter_debit_note_date_from\']').val();

  if (filter_debit_note_date_from) {
    url += '&filter_debit_note_date_from=' + encodeURIComponent(filter_debit_note_date_from);
  }

  var filter_debit_note_date_to = $('input[name=\'filter_debit_note_date_to\']').val();

  if (filter_debit_note_date_to) {
    url += '&filter_debit_note_date_to=' + encodeURIComponent(filter_debit_note_date_to);
  }
  var filter_return_request_date_added_to = $('input[name=\'filter_return_request_date_added_to\']').val();

  if(filter_return_request_date_added_to){
    url += '&filter_return_request_date_added_to=' + encodeURIComponent(filter_return_request_date_added_to);
  }
  var filter_return_request_date_added_from = $('input[name=\'filter_return_request_date_added_from\']').val();
  if(filter_return_request_date_added_from){
    url += '&filter_return_request_date_added_from=' + encodeURIComponent(filter_return_request_date_added_from);
  }
	location = url;
});
//--></script>
<script type="text/javascript">
  $('input[name*=\'filter_\'], select[name*=\'filter_\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });
</script>
  <script type="text/javascript"><!--
/*$('input[name=\'filter_customer\']').autocomplete({
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
});*/

//--></script>
<script type="text/javascript"><!--
  $('.date').datetimepicker({
    pickTime: false
  });
//--></script>
