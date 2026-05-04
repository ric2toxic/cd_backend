<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Order Receipt Report'; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php //echo $breadcrumb['text']; ?></a></li>
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
      <div class="panel-body">
        <div class="well">
          <input type="hidden" name="route" value="<?php echo $route; ?>">
          <input type="hidden" name="token" value="<?php echo $token; ?>">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name">Order No.</label>
                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $filter_order_no; ?>" id="input-name" class="form-control filter_order_no" />
              </div>
              <div class="form-group">
                <label class="control-label" for="filter_customer_id">Customer Id</label>
                <input type="text" name="filter_customer_id" value="<?php echo $filter_customer_id; ?>" placeholder="" id="filter_customer_id" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-name">Tracking No.</label>
                <input type="text" name="filter_tracking_no" value="<?php echo $filter_tracking_no; ?>" placeholder="<?php echo $filter_tracking_no; ?>" id="input-name" class="form-control filter_tracking_no" />
              </div>
            </div>
              <div class="col-sm-4">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group date">
                      <label class="control-label" for="seller_id">Date From</label>
                      <div class="input-group date">
                        <input type="text" class="form-control filter_date_from" data-date-format="DD-MM-YYYY" value="<?php echo $filter_date_from; ?>" name="filter_date_from" placeholder="Date From" />
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group date">
                      <label class="control-label" for="seller_id">Date To</label>
                      <div class="input-group date">
                        <input type="text" class="form-control" data-date-format="DD-MM-YYYY" value="<?php echo $filter_date_to; ?>" name="filter_date_to" placeholder="Date To" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label" for="input-status">Order Status</label>
                  <select name="filter_order_status" class="form-control filter_order_status">
                    <option value="">All</option>
                      <?php foreach ($order_statuses_qry as $order_status) { ?>
                      <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>

                  </select>
                </div>
                <div class="form-group">
                  <label class="control-label" for="input-name">Returns / Buyer's Refund</label>
                  <select name="filter_cr_note" class="form-control filter_cr_note">
                        <?php
                          $filter_cr_noteArry = array( '1'  => 'With Returns / Buyers Refund', '2' => 'Without Returns / Buyers Refund');
                          foreach( $filter_cr_noteArry as $key => $value ) {
                          
                          if( $key == $filter_cr_note ) {
                        ?>
                        <option class="form-control" selected  value="<?php echo $key  ?>" > <?php echo $value ?> </option>
                        <?php
                          }
                          else {
                        ?>
                        <option class="form-control" value="<?php echo $key  ?>" > <?php echo  $value ?> </option>
                        <?php
                          }
                          }
                        ?>
                  </select>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label" for="seller_id">Order Value From</label>
                      <input type="text" class="form-control filter_total_from" value="<?php echo $filter_total_from; ?>" name="filter_total_from" placeholder="Order Value From" />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label" for="seller_id">Order Value To</label>
                      <input type="text" class="form-control filter_total_to" value="<?php echo $filter_total_to; ?>" name="filter_total_to" placeholder="Order Value To" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label" for="select-payment-code">Payment Code</label>
                  <select name="filter_payment_code" id="select-payment-code" class="form-control">
                    <option value="*"><?php echo '---- Select Payment Code ----'; ?></option>
                    <?php if(!empty( $payment_codes )){ ?>
                      <?php foreach($payment_codes as $payment_code) { ?>
                        <option value="<?php echo $payment_code; ?>" 
                          <?php echo ($filter_payment_code == $payment_code) ? 'selected' : '' ; ?> >
                          <?php echo ucwords(str_replace('_', ' ', $payment_code)); ?>
                        </option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                      <label class="control-label" for="select-payment-type">Type</label>
                      <select name="filter_payment_type" id="select-payment-type" class="form-control">
                        <option value="">All</option>
                        <option value="receipt" <?php echo ($filter_payment_type == 'receipt') ? 'selected' : '' ; ?> >Receipt</option>
                        <option value="payment" <?php echo ($filter_payment_type == 'payment') ? 'selected' : '' ; ?> >Payment</option>
                      </select>
                    </div>
                    <div class="col-sm-6">
                      <label class="control-label" for="select-verified">Verified</label>
                      <select name="filter_verified" id="select-payment-verified" class="form-control">
                        <option value="">All</option>
                        <option value="verified" <?php echo ($filter_verified == 'verified') ? 'selected' : '' ; ?> >Yes</option>
                        <option value="unverified" <?php echo ($filter_verified == 'unverified') ? 'selected' : '' ; ?> >No</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                  <button type="button" id="button-download" name="download" 
                      class="btn btn-primary pull-right button-download"
                      style="margin-right: 10px;" 
                  >
                  <i class="fa fa-download"></i> Download</button>
                </div>
              </div>
            </div>
        </div>
        <form action="<?php //echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive analysis">
          <?php if (!empty($orders)) { ?>
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>Order No.</td>
                  <td>Customer ID</td>
                  <td width="500px">Customer Name</td>
                  <td width="50px">Order Date</td>
                  <td width="50px">Order Value</td>
                  <td class="text-left" colspan="7">
                    <table >
                      <tr>
                        <td style="width:90px!important">Sub Value</td>
                        <td style="width:130px!important">Courier</td>
                        <td style="width:130px!important">Tracking No.</td>
                        <td style="width:100px!important">Order Status</td>
                      </tr>
                    </table>
                  </td>
                  <?php
                    if ($filter_cr_note == 1) {
                  ?>
                    <td class="text-left" colspan="7">
                      <table >
                        <tr>
                          <td style="width:130px!important">Cr.Note No.</td>
                          <td style="width:90px!important">Amount</td>
                        </tr>
                      </table>
                    </td>
                  <?php
                    }
                  ?>
                  <td>Cashback<br>Coupon</td>
                  <td class="text-left" colspan="7">
                    <table >
                      <tr>
                        <td style="width:90px!important">Payment Date</td>
                        <td style="width:90px!important">Amount</td>
                        <td style="width:200px!important">Transaction Ref.No.</td>
                        <td>Payment Company</td>
                        <td>Bank</td>
                        <td>Verified</td>
                      </tr>
                    </table>
                  </td> 
                  <td>Balance</td>
                </tr>
              </thead>
              <tbody>              
                
                <?php foreach ($orders as $order_id => $order) { ?>               
                <tr class="parent" data-child="child_<?php echo $order_id; ?>" style="border: 1px solid red!important;">
                  <td class="text-left"><?php echo $order['order_no']; ?></td>
                  <td class="text-left"><?php echo $order['customer_id']; ?></td>
                  <td class="text-left"><?php echo $order['customer_name']; ?></td>
                  <td class="text-left">
                    <?php echo date("d-m-Y", strtotime($order['order_date'])); ?></td>
                  <td class="text-left"><?php echo round($order['total'], 2); ?></td>
                  <td class="text-left" colspan="7">
                    <table>
                      <?php if (!empty($subOrders[$order_id])) {  
                        foreach($subOrders[$order_id] as $suborderVal){
                      ?>
                        <tr class="parent" data-child="child_<?php echo $order_id; ?>" >
                          <td class="text-left" style="width:90px!important"><?php echo Round($suborderVal['total'], 2); ?></td>
                          <td class="text-left" style="width:90px!important"><?php echo $suborderVal['courier_partner'] ?? ''; ?></td>
                          <td class="text-left" style="width:130px!important"><?php echo $suborderVal['tracking_no'] ?? ''; ?></td>
                          <td class="text-left" style="width:100px!important"><?php echo $suborderVal['status_name'] ?? ''; ?></td>
                        </tr>
                      <?php
                      }
                     }
                   ?>
                  </table>
                </td>
                  <?php if ($filter_cr_note == 1) {
                    ?>
                      <td class="text-left" colspan="7">
                        <table>
                          <?php 
                          if (!empty($all_cn[$order_id])) {  
                            foreach($all_cn[$order_id] as $cn){
                          ?>
                            <tr class="parent" data-child="child_<?php echo $order_id; ?>" >
                              <td class="text-left" style="width:90px!important">
                              <?php echo $cn['cn_no']; ?></td>
                              <td class="text-left" style="width:90px!important">
                              <?php echo $cn['credit_note_amount']; ?></td>
                            </tr>
                          <?php
                          }
                         }
                        ?>
                        </table>
                      </td>
                    <?php } ?>
                  <td class="text-center">
                    <?php echo $coupon_cashback_discount[$order_id] ?? 0; ?>
                  </td>
                  <td class="text-left" colspan="7">
                    <table>
                      <?php
                      if (!empty($payments[$order_id])) { 
                       foreach($payments[$order_id] as $payment){

                        $payment_type = $payment['payment_gateway'] ?? '';
                       
                        if($payment['payment_gateway'] == 'wsb_credit_nach'){
                          $payment_type = 'Others';
                        }else if($payment['amount'] < 0 ){
                          $payment_type = 'Refund';
                        }else if($payment['amount'] > 0 && strtotime($payment['txn_date_time']) <= strtotime($order['min_invoice_date']) ){
                          $payment_type = 'Advance';
                        }
                      ?>
                        <tr class="parent" data-child="child_<?php echo $order_id; ?>" >
                            <td class="text-left" style="width:90px!important">
                              <?php echo date("d-m-Y H:i:s", strtotime($payment['payment_date'])); ?>
                            </td>
                            <td class="text-left" style="width:90px!important">
                              <?php echo $payment['amount']; ?>
                            </td>
                            <td class="text-left" style="width:200px!important">
                              <?php echo $payment['merchant_txn_id'] ?? 'N/A'; ?>
                            </td>
                            <td class="text-left">
                              <b>(<?php echo $payment_type; ?>)</b><br>
                              <?php echo $payment['payment_gateway']; ?>
                            </td>
                            <td class="text-left"><?php echo $payment['payment_mode']; ?></td>
                            <td class="text-left">
                              <b><?php echo $payment['rec_pay_id']>0 ? 'Verified' : 'Unverified';?></b>
                            </td>
                        </tr>
                      <?php
                     }
                    }
                    ?>
                    </table>
                  </td>
                  <td class="text-left">
                    <?php echo $all_order_balance[$order_id] ?? 0; ?>
                  </td>
                </tr>
                <?php } ?>
                
              </tbody>
            </table>
            <?php } else { ?>
              <div class="text-center"><b><?php echo $text_no_results; ?></b></div>
            <?php } ?>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
<a href="#" class="scrollToTop">Top</a>
</div>
<?php echo $footer; ?>
<style>
.scrollToTop{
  display: none;
  position: fixed;
  bottom: 20px;
  right: 30px;
  z-index: 99;
  border: none;
  outline: none;
  background-color: red;
  color: white;
  cursor: pointer;
  padding: 15px;
  border-radius: 10px;
}

.scrollToTop:hover{
  text-decoration:none;
}
</style>
<script>
$('.date').datetimepicker({
      pickTime: false
    });
    
$(document).ready(function(){
  
  //Check to see if the window is top if not then display button
  $(window).scroll(function(){
    if ($(this).scrollTop() > 100) {
      $('.scrollToTop').fadeIn();
    } else {
      $('.scrollToTop').fadeOut();
    }
  });
  
  //Click event to scroll to top
  $('.scrollToTop').click(function(){
    $('html, body').animate({scrollTop : 0},800);
    return false;
  });
  
});
</script>
<style>
.show_product_row + .child{
    display: table-row!important;
}
</style>
<script type="text/javascript">
  $('.toggleclass').click(function(){
      if($(this).prop('checked')){
          $(this).parents('tr').addClass('show_product_row bg-success');
      }
      else{
          $(this).parents('tr').removeClass('show_product_row bg-success');
      }

  });
  $('#seller_id').on('keyup',function(){
      if($(this).val().trim().length == 0){
          $('input[name="filter_seller_id"]').val(0);
      }
  });

  //Enter key pressed
  $(document).bind('keypress', function(e) {
      if(e.keyCode==13){
        $('.button-filter').trigger('click');
      }
  });

  $('.button-filter,.button-download').on('click', function() {
    var req_type = $(this).attr('id');
    var url = 'index.php?route=account_panel/orderpayment&token=<?php echo $token; ?>'+'&req_type='+req_type;

    var filter_order_no = $('input[name=\'filter_order_no\']').val();
    if (filter_order_no) {
      url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }

    var filter_verified = $('select[name=\'filter_verified\']').val();
    if (filter_verified) {
      url += '&filter_verified=' + encodeURIComponent(filter_verified);
    }

    var filter_payment_type = $('select[name=\'filter_payment_type\']').val();
    if (filter_payment_type) {
      url += '&filter_payment_type=' + encodeURIComponent(filter_payment_type);
    }

    var filter_date_from = $('input[name=\'filter_date_from\']').val();
    if (filter_date_from) {
      url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
    }

    var filter_date_to = $('input[name=\'filter_date_to\']').val();
    if (filter_date_to) {
      url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
    }

    var filter_tracking_no = $('input[name=\'filter_tracking_no\']').val();
    if (filter_tracking_no) {
      url += '&filter_tracking_no=' + encodeURIComponent(filter_tracking_no);
    }

    var filter_total_from = $('input[name=\'filter_total_from\']').val();
    if (filter_total_from) {
      url += '&filter_total_from=' + encodeURIComponent(filter_total_from);
    }

    var filter_total_to = $('input[name=\'filter_total_to\']').val();
    if (filter_total_to) {
      url += '&filter_total_to=' + encodeURIComponent(filter_total_to);
    }

    var filter_order_status = $('select[name=\'filter_order_status\']').val();
    if (filter_order_status) {
      url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }

    var filter_cr_note = $('select[name=\'filter_cr_note\']').val();
    if (filter_cr_note) {
      url += '&filter_cr_note=' + encodeURIComponent(filter_cr_note);
    }

    var filter_customer_id = $('input[name=\'filter_customer_id\']').val();
    if (filter_customer_id) {
      url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
    }

    var filter_payment_code = $('select[name=\'filter_payment_code\']').val();
    if (filter_payment_code != '*') {
        url += '&filter_payment_code=' + encodeURIComponent(filter_payment_code);
    }

    location = url;
  });

</script>