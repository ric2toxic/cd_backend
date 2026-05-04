<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>Tentative Refund</h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> Tentative Refund List</h3>
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
                <label class="control-label" for="filter_refund_date_from">Refund Calculation Date From</label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_refund_date_from"
                           type="text"
                           value="<?php echo $filter_refund_date_from; ?>"
                           placeholder="YYYY-MM-DD"
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
                <label class="control-label" for="filter_cn_date_from">CreditNote Date From</label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_cn_date_from"
                           type="text"
                           value="<?php echo $filter_cn_date_from; ?>"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="filter_refund_date_to">Refund Calculation Date To</label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_refund_date_to"
                           type="text"
                           value="<?php echo $filter_refund_date_to; ?>"
                           placeholder="YYYY-MM-DD"
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
                <label class="control-label" for="filter_cn_date_to">CreditNote Date To</label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_cn_date_to"
                           type="text"
                           value="<?php echo $filter_cn_date_to; ?>"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Refund Type</label><br>
                <select name="filter_refund_type" class="form-control">
                  <option value="all">All</option>
                  <?php foreach($refund_type_data as $key=>$val){ ?>
                  <?php $str = ($filter_refund_type == $key)?'selected="Selected"': ''; ?>
                   <option <?php echo $str; ?> value="<?php  echo $key; ?>">
                     <?php echo $val;?>
                   </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Is Approved</label><br>
                <select name="filter_is_approved" class="form-control">
                  <option value="-1">All</option>
                  <?php foreach($is_approved_data as $key=>$val){ ?>
                  <?php $str = ($filter_is_approved == $key)?'selected="Selected"': ''; ?>
                   <option <?php echo $str; ?> value="<?php  echo $key; ?>">
                     <?php echo $val;?>
                   </option>
                  <?php } ?>
                </select>
              </div>
              <a href="<?php echo @$download; ?>" style="margin-left:10px;" class="btn btn-primary pull-right"><i class="fa fa-download"></i>CSV</a>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
       
          <div class="table-responsive">
          <?php if ($refunds) { ?>
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th style="width:250px"><a href="<?php echo $sort_order_no; ?>" >Order</a></th>
                  <th class="text-left" style="width:300px">Customer</th>
                  <th class="text-left">Contact / Email</th>
                  <th class="text-left" style="width: 350px;">Bank Details</th>
                  <th class="text-left">Refund Ref</th>
                  <th class="text-left" style="width:250px">
                    Net Invoice Value(A)<br>
                    (Total Invoices-TotalCNs)
                  </th>
                  <th class="text-left" style="width:250px">
                    Payment Received(B)<br>
                    (Total Received-TotalRefunds)
                  </th>
                  <th class="text-left" style="width:250px">
                    Balance Refund<br>
                    (B-A)
                  </th>
                  <th class="text-left">Refund Amt</th>
                  <th class="text-left">Refund Calculation Date</th>
                  <th class="text-left">Is Approved</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($refunds as $refund) { ?>
                <?php $order_id = $refund['order_id']; ?>
                <tr>
                  <td>
                    <?php echo $refund['order_no']; ?><br>
                    <?php echo (!empty($refund['suborder_id'])? '('.$refund['suborder_id'].')' : ''); ?>
                  </td>
                  <td class="text-left">
                    <?php echo $refund['customer']; ?><br>
                    <?php echo $refund['shipping_company']; ?><br>
                    (<?php echo $refund['shipping_city']; ?>)
                  </td>
                  <td>
                    <?php echo $refund['telephone']; ?><br>
                    <span class="click_to_see btn-primary btn-xs" data-field-type="email" data-field-value="<?php echo base64_encode($refund['email']); ?>" onClick="clickToSee(this)"> Click to see </span>
                  </td>
                  
                  <td>
                    <b>A/c Holder Name</b>: <br>
                      <?php 
                        echo (!empty($refund['bank_ac_holder_name']) ? $refund['bank_ac_holder_name'] : '-' );
                      ?><br><br>
                    <b>A/c No.</b>: <br>
                      <?php 
                        echo (!empty($refund['bank_ac_number']) ? $refund['bank_ac_number'] : '-' );
                      ?><br><br>
                    <b>IFSC</b> :<br>
                      <?php 
                        echo (!empty($refund['ifsc_code']) ? $refund['ifsc_code'] : '-' );
                      ?>
                  </td>
                  <td>
                    Ref Id: <?php echo $refund['ref_id'];?><br>
                    <?php echo $refund['refund_ref'];?><br>
                    <?php echo( !empty($refund['cn_date']) ? 'CN Date: '.$refund['cn_date'] : '');?>
                  </td>
                  <td class="text-left">
                    <?php 
                      $invoice_bal = 0;
                      if(isset($total_invoice[$order_id])){
                        echo $total_invoice[$order_id];
                        $invoice_bal += (float)$total_invoice[$order_id];
                      }
                      if(isset($cn_amount[$order_id])){
                        $cn = (-1)*(float)$cn_amount[$order_id];
                        echo $cn;
                        $invoice_bal += (float)$cn;
                      }
                      echo '<br>= Rs. '.$invoice_bal;
                    ?>
                  </td>
                  <td class="text-left">
                    <?php 
                      $payment_bal = 0;
                      if(isset($payment[$order_id]['amount'])){
                        echo $payment[$order_id]['amount'];
                        $payment_bal += (float)$payment[$order_id]['amount'];
                      }
                      if(isset($payment[$order_id]['refund'])){
                        if(empty($payment[$order_id]['refund'])){
                          $refund_val = ' - '.$payment[$order_id]['refund'];
                        }else{
                          $refund_val = ' '.$payment[$order_id]['refund'];
                        }
                        echo $refund_val;
                        $payment_bal += (float)$refund_val;
                      }
                      echo '<br>= Rs. '.$payment_bal;
                    ?>
                  </td>
                  <td><b><?php echo ROUND($payment_bal-$invoice_bal, 2); ?></b></td>
                  
                  <td class="text-left">
                    <b><?php echo $refund['total_refund'];?></b>
                  </td>
                  <td class="text-left">
                    <?php echo $refund['date_added'];?>
                  </td>
                  
                  <td class="text-left">
                    <select id="is_approved_<?php echo $refund['id'];?>" class="is_approved"
                      data-refund_id = "<?php echo $refund['id'];?>" >
                      <?php foreach($is_approved_data as $key=>$val){ 
                            $str = '';
                            if($key == $refund['is_approved']){ $str = 'selected="selected"'; }
                      ?>
                      <option value="<?php echo $key;?>" <?php echo $str;?> >
                        <?php echo $val;?>
                      </option>
                      <?php }?>
                    </select><br>
                    <?php $disabled = '';
                    if($refund['is_approved'] == '2'){ 
                      $disabled = 'style="display: none;"'; 
                    ?>
                    <i><?php echo $refund['rejected_comment']; ?></i><br>
                    <?php } ?>
                    <textarea id="comment_<?php echo $refund['id'];?>" style="display: none;"></textarea>
                    <br>
                    <button data-refund_id="<?php echo $refund['id']?>" <?php echo $disabled; ?> class="btn-primary btn-save">Save</button>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <?php } else { ?>
                <div><?php echo $text_no_results; ?></div>
            <?php } ?>
          </div>
        
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
<style type="text/css">
td { white-space: nowrap; } 
</style>
<script type="text/javascript">
var token = '<?php echo $token; ?>';

$('.is_approved').on('change', function() {
  var refund_id = $(this).data('refund_id');
  if($(this).val() == 2){
    $('#comment_'+refund_id).css('display', 'block');
  }else{
    $('#comment_'+refund_id).css('display', 'none');
  }
});

$('#button-filter').on('click', function() {
	url = 'index.php?route=sale/tentative_refund&token='+token;

	var filter_order_no = $('input[name=\'filter_order_no\']').val();
	if (filter_order_no) {
		url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
	}

	var filter_refund_type = $('select[name=\'filter_refund_type\']').val();
	if (filter_refund_type) {
		url += '&filter_refund_type=' + encodeURIComponent(filter_refund_type);
	}

	var filter_is_approved = $('select[name=\'filter_is_approved\']').val();
	if (filter_is_approved) {
		url += '&filter_is_approved=' + encodeURIComponent(filter_is_approved);
	}

	var filter_refund_date_from = $('input[name=\'filter_refund_date_from\']').val();
	if (filter_refund_date_from) {
		url += '&filter_refund_date_from=' + encodeURIComponent(filter_refund_date_from);
	}
  var filter_refund_date_to = $('input[name=\'filter_refund_date_to\']').val();
  if (filter_refund_date_to) {
    url += '&filter_refund_date_to=' + encodeURIComponent(filter_refund_date_to);
  }

  var filter_cn_date_from = $('input[name=\'filter_cn_date_from\']').val();
  if (filter_cn_date_from) {
    url += '&filter_cn_date_from=' + encodeURIComponent(filter_cn_date_from);
  }
  var filter_cn_date_to = $('input[name=\'filter_cn_date_to\']').val();
  if (filter_cn_date_to) {
    url += '&filter_cn_date_to=' + encodeURIComponent(filter_cn_date_to);
  }  
 
	location = url;
});

$('.btn-save').on('click', function() { 
  var refund_id = $(this).data('refund_id');
  if($('#comment_'+refund_id).css('display') == 'block' && $('#comment_'+refund_id).val().length == 0)
  {
    alert('Rejected Reasons/Comment can not be empty.');
    return false;
  }
  data = {
          id            : refund_id,
          is_approved   : $('#is_approved_'+refund_id).val(),
          comment       : $('#comment_'+refund_id).val()
         };
  $.ajax({
    type:'post',
    url:'index.php?route=sale/tentative_refund/updateIsApprovedTentativeRefund&token='+token,
    data:data,
    success: function(json) {
      alert('Successfully Updated.');
      location.reload();
    }
  });
});

$('.date').datetimepicker({
    pickTime: false
});

</script>
