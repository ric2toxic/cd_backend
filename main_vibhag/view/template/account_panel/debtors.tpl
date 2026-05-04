<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Sundry Debtors Report'//$page_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Sundry Debtors Report'//$heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <!--form action="" method="get" id="filter_form"-->
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">

                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label" for="input-name"><?php echo "Order No."; ?></label>
                      <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $filter_order_no; ?>" id="input-name" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-name"><?php echo "Sub Order No."; ?></label>
                      <input type="text" name="filter_suborder_id" value="<?php echo $filter_suborder_id; ?>" placeholder="<?php echo $filter_suborder_id; ?>" id="input-name" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-name"><?php echo "Tracking No."; ?></label>
                      <input type="text" name="filter_tracking_no" value="<?php echo $filter_tracking_no; ?>" placeholder="<?php echo $filter_tracking_no; ?>" id="input-name" class="form-control" />
                    </div>
                  </div>



                  <div class="col-sm-4">
                    <div class="form-group date">
                      <label class="control-label" for="seller_id">Date From</label>
                      <div class="input-group date">
                        <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_from; ?>" name="filter_date_from" placeholder="Date From" />
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>

                    <div class="form-group date">
                      <label class="control-label" for="seller_id">Date To</label>
                      <div class="input-group date">
                        <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_to; ?>" name="filter_date_to" placeholder="Date To" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>



                    <div class="form-group">
                      <label class="control-label" for="input-status"><?php echo 'Order Status'; ?></label>
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
                      <button type="button" id="button-export" class="btn btn-primary pull-right button-export"><i class="fa fa-search"></i> <?php echo 'Export'; ?></button>
                    </div>
                  </div>

                  <div class="col-sm-4">

                    <div class="form-group">
                      <label class="control-label" for="seller_id">Order Value From</label>
                      <input type="text" class="form-control" value="<?php echo $filter_total_from; ?>" name="filter_total_from" placeholder="Order Value From" />
                    </div>

                    <div class="form-group">
                      <label class="control-label" for="seller_id">Order Value To</label>
                      <input type="text" class="form-control" value="<?php echo $filter_total_to; ?>" name="filter_total_to" placeholder="Order Value To" />
                    </div>

                    <div class="form-group">
                      <label class="control-label" for="input-status"><?php echo 'Bank'; ?></label>
                   

                      <select name="filter_payment_mode" class="form-control filter_payment_mode">
                          <option value="">All</option>

                            <?php foreach ($paymentModes as $paymentMode) { ?>
                            <?php if ($paymentMode['payment_mode'] == $filter_payment_mode) { ?>
                            <option value="<?php echo $paymentMode['payment_mode']; ?>" selected="selected"><?php echo $paymentMode['payment_mode']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $paymentMode['payment_mode']; ?>"><?php echo $paymentMode['payment_mode']; ?></option>
                            <?php } ?>
                            <?php } ?>

                      </select>
                    </div>

                    <div class="form-group">
                      <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                    </div>
                  </div>
                </div>
            <!--/form-->
        </div>
        <form action="<?php //echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive analysis">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>S.No.</td>
                  <td>Debtors Name</td>
                  <td>Receipts</td>
                  <td>Payments</td>
                  <td>Balance</td>
                </tr>
              </thead>
              <tbody>              
                <?php if (!empty($debtorsStatement)) { ?>
                <?php $i = 1; ?>
                <?php foreach ($debtorsStatement as $key => $debtor) {  


                ?>               
                <tr class="parent" data-child="child_<?php echo $key; ?>" style="border: 1px solid red!important;">
                  <td class="text-left"><?php echo $i ?></td>
                  <td class="text-left"><?php echo $debtor['ledger_name']; ?></td>
                  <td class="text-left"><?php echo $debtor['receipts']; ?></td>
                  <td class="text-left"><?php echo $debtor['payments']; ?></td>
                </tr>

                <?php $i++;} ?>
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
          <div class="col-sm-6 text-left"><?php //echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php //echo $results; ?></div>
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

</script>

<script type="text/javascript"><!--
$('.button-filter').on('click', function() {

  
  var url = 'index.php?route=account_panel/orderpayment&token=<?php echo $token; ?>';

  var filter_order_no = $('input[name=\'filter_order_no\']').val();
  if (filter_order_no) {
    url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
  }

  var filter_suborder_id = $('input[name=\'filter_suborder_id\']').val();
  if (filter_suborder_id) {
    url += '&filter_suborder_id=' + encodeURIComponent(filter_suborder_id);
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

  var filter_payment_mode = $('select[name=\'filter_payment_mode\']').val();
  if (filter_payment_mode) {
    url += '&filter_payment_mode=' + encodeURIComponent(filter_payment_mode);
  }




  location = url;

});

  
  // $('.date').datetimepicker({
  //     pickTime: false
  // });
</script>

<script type="text/javascript"><!--
$('.button-export').on('click', function() {

  
  var url = 'index.php?route=account_panel/orderpayment/exportcsv&token=<?php echo $token; ?>';

  var filter_order_no = $('input[name=\'filter_order_no\']').val();
  if (filter_order_no) {
    url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
  }

  var filter_suborder_id = $('input[name=\'filter_suborder_id\']').val();
  if (filter_suborder_id) {
    url += '&filter_suborder_id=' + encodeURIComponent(filter_suborder_id);
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

  var filter_payment_mode = $('select[name=\'filter_payment_mode\']').val();
  if (filter_payment_mode) {
    url += '&filter_payment_mode=' + encodeURIComponent(filter_payment_mode);
  }




  location = url;

});

  
  // $('.date').datetimepicker({
  //     pickTime: false
  // });
</script>