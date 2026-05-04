<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Payment Gateway Report'//$page_title; ?></h1>
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
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Payment Gateway Report'//$heading_title; ?></h3>
        <button type="button" id="button-export" class="btn btn-primary pull-right button-export"><?php echo 'Export'; ?></button>
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
                      <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $filter_order_no; ?>" id="input-name" class="form-control filter_order_no" />
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-name"><?php echo "Sub Order No."; ?></label>
                      <input type="text" name="filter_suborder_id" value="<?php echo $filter_suborder_id; ?>" placeholder="<?php echo $filter_suborder_id; ?>" id="input-name" class="form-control filter_suborder_id" />
                    </div>

                                
                  </div>

                  <div class="col-sm-4">
                    <div class="form-group date">
                      <label class="control-label" for="seller_id">Date From</label>
                      <div class="input-group date">
                        <input type="text" class="form-control filter_date_from" data-date-format="DD-MM-YYYY" value="<?php echo $filter_date_from; ?>" name="filter_date_from" placeholder="Date From" />
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>

                    <div class="form-group date">
                      <label class="control-label" for="seller_id">Date To</label>
                      <div class="input-group date">
                        <input type="text" class="form-control filter_date_to" data-date-format="DD-MM-YYYY" value="<?php echo $filter_date_to; ?>" name="filter_date_to" placeholder="Date To" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label" for="input-name"><?php echo "Payment Gateway"; ?></label>
                      <select name="filter_payment_gateway" class="form-control filter_payment_gateway">
                          <!--option value="AllConfirm">All</option-->
                            <?php
                              $filter_payment_gatewayArry = array( '101' => 'All', 'citrus'  => 'citrus', 'paytm' => 'paytm', 'razorpay' => 'razorpay');
                              foreach( $filter_payment_gatewayArry as $key => $value ) {
                              
                              if( $key == $filter_payment_gateway ) {
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

                    <div class="form-group">
                      <label class="control-label" for="seller_id">Order Value From</label>
                      <input type="text" class="form-control filter_total_from" value="<?php echo $filter_total_from; ?>" name="filter_total_from" placeholder="Order Value From" />
                    </div>

                    <div class="form-group">
                      <label class="control-label" for="seller_id">Order Value To</label>
                      <input type="text" class="form-control filter_total_to" value="<?php echo $filter_total_to; ?>" name="filter_total_to" placeholder="Order Value To" />
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
                  <td>Order No.</td>
                  <td width="50px">Order Date</td>
                  <td width="50px">Order Value</td>

                  <td class="text-left" colspan="7">
                    <table >
                      <tr>
                        <!--td style="width:90px!important">Sub Value</td-->
                        <td style="width:90px!important">Amt.Receivable</td>
                        <td style="width:130px!important">Merchant Txn ID</td>
                      </tr>
                    </table>
                  </td>
                  <td class="text-left" colspan="7">
                    <table >
                      <tr>
                        <td style="width:90px!important">Payment Date</td>
                        <td style="width:90px!important">Amount</td>
                        <td style="width:90px!important">Charges</td>
                        <td style="width:200px!important">Transaction Ref.No.</td>
                        <td>Payment Company</td>
                      </tr>
                    </table>
                  </td>
                  <td>Balance</td>
                </tr>
              </thead>
              <tbody>              
                <?php if (!empty($subOrders)) { ?>
                <?php $i = 1; ?>
                <?php foreach ($subOrders as $key => $subOrder) {  

                  $totalNet_receivable = 0;
                  $totalAmt = 0;
                  $trackArray = array();
                  
                ?>               
                <tr class="parent" data-child="child_<?php echo $key; ?>" style="border: 1px solid red!important;">
                  
                  <td class="text-left"><?php echo $subOrder['order_no']; ?></td>
                  <td class="text-left"><?php echo $newDate = date("d-m-Y", strtotime($subOrder['date_added'])); ?></td>
                  <td class="text-left"><?php echo round($subOrder['total'], 2); ?></td>

                  <td class="text-left" colspan="7">
                    <table>
                   <?php 
                     if (!empty($subOrder['suborder'])) {                       
                      foreach($subOrder['suborder'] as $suborderVal){

                        $totalNet_receivable += $suborderVal['amount'];
                        //$trackArray[$suborderVal['merchant_txn_id']] =  $suborderVal['amount'];
                        
                      ?>
                        <tr class="parent" data-child="child_<?php echo $key; ?>" >
                            <td class="text-left" style="width:100px!important"><?php echo $suborderVal['amount']; ?></td>
                            <td class="text-left" style="width:100px!important"><?php echo $suborderVal['merchant_txn_id']; ?></td>
                        </tr>
                      <?php
                      }
                     }
                   ?>
                    </table>
                  </td>
                  <td class="text-left" colspan="7">
                    <table>
                  <?php
                     if (!empty($subOrder['amounts'])) { 

                       foreach($subOrder['amounts'] as $suborderVal){

                        $totalAmt += $suborderVal['amount'];

                        ?>
                          <tr class="parent" data-child="child_<?php echo $key; ?>" >
                              <td class="text-left" style="width:90px!important"><?php echo $suborderVal['dated']; ?></td>
                              <td class="text-left" style="width:90px!important"><?php echo $suborderVal['amount']; ?></td>
                              <td class="text-left" style="width:100px!important"><?php echo $suborderVal['charges']; ?></td>
                              <td class="text-left" style="width:200px!important"><?php echo $suborderVal['ref']; ?></td>
                              <td class="text-left"><?php echo $suborderVal['payment_gateway']; ?></td>
                          </tr>
                        <?php

                       }

                     }
                  ?>
                    </table>
                  </td>
                  <td class="text-left">
                    <?php 
                      //$totalBal = $totalInvoiceAmt - $totalAmt; 
                      //$totalBal = $subOrder['total'] - $totalAmt; 
                      $totalBal = $totalNet_receivable - $totalAmt; 
                      echo round($totalBal, 2);
                    ?>
                  </td>
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

</script>

<script type="text/javascript"><!--
$('.button-filter').on('click', function() {

  
  var url = 'index.php?route=account_panel/paymentgateway&token=<?php echo $token; ?>';

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

  var filter_total_from = $('input[name=\'filter_total_from\']').val();
  if (filter_total_from) {
    url += '&filter_total_from=' + encodeURIComponent(filter_total_from);
  }  

  var filter_total_to = $('input[name=\'filter_total_to\']').val();
  if (filter_total_to) {
    url += '&filter_total_to=' + encodeURIComponent(filter_total_to);
  }
/*
  var filter_order_status = $('select[name=\'filter_order_status\']').val();
  if (filter_order_status) {
    url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
  }
*/
  var filter_payment_mode = $('select[name=\'filter_payment_mode\']').val();
  if (filter_payment_mode) {
    url += '&filter_payment_mode=' + encodeURIComponent(filter_payment_mode);
  }  

  var filter_payment_gateway = $('select[name=\'filter_payment_gateway\']').val();
  if (filter_payment_gateway) {
    url += '&filter_payment_gateway=' + encodeURIComponent(filter_payment_gateway);
  }

  location = url;

});

</script>

<script type="text/javascript"><!--
$('.button-export').on('click', function() {

  var url = 'index.php?route=account_panel/paymentgateway/exportcsv&token=<?php echo $token; ?>';

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

  var filter_total_from = $('input[name=\'filter_total_from\']').val();
  if (filter_total_from) {
    url += '&filter_total_from=' + encodeURIComponent(filter_total_from);
  }  

  var filter_total_to = $('input[name=\'filter_total_to\']').val();
  if (filter_total_to) {
    url += '&filter_total_to=' + encodeURIComponent(filter_total_to);
  }
/*
  var filter_order_status = $('select[name=\'filter_order_status\']').val();
  if (filter_order_status) {
    url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
  }
*/
  var filter_payment_mode = $('select[name=\'filter_payment_mode\']').val();
  if (filter_payment_mode) {
    url += '&filter_payment_mode=' + encodeURIComponent(filter_payment_mode);
  }

  var filter_payment_gateway = $('select[name=\'filter_payment_gateway\']').val();
  if (filter_payment_gateway) {
    url += '&filter_payment_gateway=' + encodeURIComponent(filter_payment_gateway);
  }


  location = url;

});

</script>

<script type="text/javascript">
  $(".filter_order_no,.filter_suborder_id,.filter_date_from,.filter_date_to,.filter_total_from,.filter_total_to,.filter_payment_mode,.filter_payment_gateway").keydown(function (e) {        
      
      if (e.keyCode==13) {

          var url = 'index.php?route=account_panel/paymentgateway&token=<?php echo $token; ?>';

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

            var filter_total_from = $('input[name=\'filter_total_from\']').val();
            if (filter_total_from) {
              url += '&filter_total_from=' + encodeURIComponent(filter_total_from);
            }  

            var filter_total_to = $('input[name=\'filter_total_to\']').val();
            if (filter_total_to) {
              url += '&filter_total_to=' + encodeURIComponent(filter_total_to);
            }
            var filter_payment_mode = $('select[name=\'filter_payment_mode\']').val();
            if (filter_payment_mode) {
              url += '&filter_payment_mode=' + encodeURIComponent(filter_payment_mode);
            }  

            var filter_payment_gateway = $('select[name=\'filter_payment_gateway\']').val();
            if (filter_payment_gateway) {
              url += '&filter_payment_gateway=' + encodeURIComponent(filter_payment_gateway);
            }

            location = url;

        }
  }); 
</script>