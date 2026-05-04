<?php echo $header; ?>

<div id="page-wrapper">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_payments_report; ?></h1> <!--<?php if(isset($payment_details[0]['seller_name'])){echo 'for \''.$payment_details[0]['seller_name'].'\'';}?>-->
     
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="well" style="padding:10px;margin-bottom:0px;">
           <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-invoice-no"><?php echo $title_invoice_no; ?></label>
                <input type="text" name="filter_invoice_no" value="<?php echo $filter_invoice_no; ?>" placeholder="<?php echo $title_invoice_no; ?>" id="input-invoice-no" class="form-control" />
              </div>              
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_order_no; ?></label>
                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $entry_order_no; ?>" id="input-order-no" class="form-control" />
              </div>              
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-utr"><?php echo $header_payment_utr_no; ?></label>
                <input type="text" name="filter_utr" value="<?php echo $filter_utr; ?>" placeholder="<?php echo $header_payment_utr_no; ?>" id="input-utr-no" class="form-control" />
              </div>              
            </div>
            <div class="col-sm-4">             
              <div class="form-group">
                <label class="control-label" for="input-seller"><?php echo $title_payment_date_range; ?></label>
                <input type="text" name="filter_payment_date_range" value="<?php echo $filter_payment_date_range  ; ?>" placeholder="<?php echo $title_payment_date_range; ?>" id="input-payment-date-range" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">             
              <div class="form-group">
                <label class="control-label" for="input-seller"><?php echo $title_invoice_date_range; ?></label>
                <input type="text" name="filter_invoice_date_range" value="<?php echo $filter_invoice_date_range; ?>" placeholder="<?php echo $title_invoice_date_range; ?>" id="input-invoice-date-range" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                  <button style="margin-top: 25px;" type="button" id="button-search" class="btn btn-primary pull-left">
                    <i class="fa fa-search"></i> <?php echo $button_search; ?>
                  </button>
                  <button style="margin-top: 25px;" type="button" id="button-download-csv" class="btn btn-primary pull-left">
                    <i class="fa"></i> <?php echo $button_download_csv; ?>
                  </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <?php if(!empty($payment_details)) { ?>
        <table class="table table-bordered table-hover payment-details" align="center" id='main_table' style="font-size:11px;color:#000000;">
          <thead id='header'>
            <tr>
              <td style="text-align: center;vertical-align:middle;" rowspan="2"><?php echo $header_order_no; ?></td>
              <td style="text-align: center;vertical-align:middle;" rowspan="2"><?php echo $header_invoice_title; ?></td>
              <td style="text-align: center;vertical-align:middle;" rowspan="2"><?php echo $header_return_title; ?></td>
              <td style="text-align: center;vertical-align:middle;" rowspan="2"><?php echo $header_payment_title; ?></td>
              <td style="text-align: center;vertical-align:middle;" colspan="3"><?php echo $header_summary_title; ?></td>
            </tr>
            <tr>
              <td style="text-align: center;vertical-align:middle;"><?php echo $header_net_payable; ?></td>
              <td style="text-align: center;vertical-align:middle;"><?php echo $header_paid; ?></td>
              <td style="text-align: center;vertical-align:middle;"><?php echo $header_balance; ?></td>
            </tr>
          </thead>
          <tbody id="main_tbody" style="width: auto;">
            
          <!-- For Invoices -->

          <?php foreach($payment_details as $key => $order_data) {
            $invoice_count = count($order_data);
            $order_no_displayed = 0;
            foreach($order_data as $invoice_id => $invoice_details) {
              $net_payable = 0;
              $payment = 0; ?>
              <tr>
                <?php if(!$order_no_displayed) { ?>
                  <td style="text-align: center;vertical-align:middle;" rowspan="<?php echo $invoice_count; ?>"><?php echo isset($invoice_details['order_no'])?$invoice_details['order_no']:''; ?></td>
                <?php
                  $order_no_displayed = 1;
                } ?>
                
                <!-- invoice data -->
                <td style="text-align: left;vertical-align:middle;padding-left:6px;">
                  <!-- order status to be displayed in seller invoice case only(exclude wsb purchase) -->
                  <?php
                    if(!empty($invoice_details['invoice']['order_status'])) {
                      ?>
                      <i class="fa fa-info-circle tooltip_icon" aria-hidden="true" title="<?php echo 'Order Status: '.$invoice_details['invoice']['order_status']; ?>"></i>&nbsp;&nbsp;&nbsp;
                      <?php
                    } else {
                      echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    } 
                  ?>
                  Inv: <b><font color="#000080">
                    <a href="<?php echo $invoice_details['invoice']['download_link']; ?>" title="Download Invoice"><?php echo isset($invoice_details['invoice']['invoice_no'])?$invoice_details['invoice']['invoice_no']:'';?></a></font></b>
                  <?php echo "&nbsp;&nbsp;&nbsp;"; echo isset($invoice_details['invoice']['invoice_date']) ? $invoice_details['invoice']['invoice_date'] : '';
                  echo "&nbsp;&nbsp;&nbsp;"; ?><b><?php if($invoice_details['invoice']['invoice_value'] < 0){echo '<font color="#8B0000">';} else {echo '<font color="#006400">';} ?><?php echo $invoice_details['invoice']['invoice_value_formatted']; ?></font></b>

                  
                </td>
                
                <!-- return data -->
                <?php
                if(!empty($invoice_details['returns'])) { ?>
                  <td style="text-align: left;vertical-align:middle;padding-left:6px;">
                  <?php 
                  foreach($invoice_details['returns'] as $key => $return_details) { //echo $return_details['download_link'];die;
                    echo "DN: ";?><b><font color="#000080">
                      <a href="<?php echo $return_details['download_link']; ?>" title="Download Debit Note"><?php echo isset($return_details['dn_no'])?$return_details['dn_no']:'';?></a></font></b><?php 
                    echo "&nbsp;&nbsp;&nbsp;"; echo isset($return_details['dn_date'])?$return_details['dn_date'] : '';
                    echo "&nbsp;&nbsp;&nbsp;";?><b><?php echo '<font color="#8B0000">'; ?><?php echo $return_details['dn_value_formatted']; ?></font></b><br><?php
                  } ?>
                  </td>
                <?php } else { ?>
                    <td style="text-align: center"></td>
                <?php } ?>
                
                <!-- payment data -->
                <?php
                if(!empty($invoice_details['payments'])) { ?>
                  <td style="text-align: left;vertical-align:middle;padding-left:6px;">
                  <?php 
                  foreach($invoice_details['payments'] as $key => $payment_details) {?>
                      <b><font color="#000080"><?php echo isset($payment_details['trxn_utr'])?$payment_details['trxn_utr']:''; ?></font></b><?php 
                      echo "&nbsp;&nbsp;&nbsp;"; echo isset($payment_details['trxn_utr_date'])?$payment_details['trxn_utr_date'] : '';
                      echo "&nbsp;&nbsp;&nbsp;";?><b><?php if($payment_details['trxn_amount'] < 0){echo '<font color="#8B0000">';} else {echo '<font color="#006400">';} ?><?php echo $payment_details['trxn_amount_formatted']; ?></font></b><br><?php
                  } ?>
                  </td>
                <?php } else { ?>
                    <td style="text-align: center;vertical-align:middle;padding:2px;">
                      <?php if(!empty($invoice_details['full_return'])) {
                        echo "<b><font color='#8B0000'>".$invoice_details['full_return']."</font></b>";
                      } ?>
                    </td>
                <?php } ?>
                
                <!-- Summary -->
                <td style="color: #008000;vertical-align:middle;" align="center" rowspan="<?php echo $payable_rowspan; ?>">
                  <?php 
                    if((int)$invoice_details['net_payable'] > 0){
                      echo '<b><font color="#006400">';
                    } else {
                      echo '<b><font color="#000000">';
                    }
                    echo $invoice_details['net_payable_formatted'];
                  ?>
                  </font></b>
                </td>
                <td style="color: #FF4500;vertical-align:middle;" align="center" rowspan="<?php echo $payable_rowspan; ?>">
                  <?php if((int)$invoice_details['paid'] < 0){
                     echo '<b><font color="#8B0000">';
                    } else if((int)$invoice_details['paid'] > 0) {
                       echo '<b><font color="#006400">';
                    } else {
                      echo '<b><font color="#000000">';
                    }
                    echo $invoice_details['paid_formatted']; 
                  ?>
                  </font></b>
                </td>
                <td style="color: #FF4500;vertical-align:middle;" align="center" rowspan="<?php echo $payable_rowspan; ?>">
                  <?php if((int)$invoice_details['balance'] > 0){
                      echo '<b><font color="#8B0000">';
                    } else if((int)$invoice_details['balance'] < 0) {
                      echo '<b><font color="#006400">';
                    } else {
                      echo '<b><font color="#000000">';
                    }
                    echo $invoice_details['balance_formatted']; 
                  ?>
                  </font></b>
                </td>
                
              </tr>
            <?php }
            } ?>
          </tbody>
        </table>
        <table class="table table-bordered table-hover payment-details" align="center" id="header-fixed" style="font-size:11px;color:#000000;"></table>
      <?php }else{
          echo '<div><i>No Data Found.</i></div>';
      } ?>
    </div>
    <div class="row">
      <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
      <div class="col-sm-6 text-right"><?php echo $results; ?></div>
    </div>
  </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript"><!--
$(document).ready(function(){
  var tableOffset = $("#main_table").offset().top;
  var $header = $("#main_table > thead").clone();
  var $fixedHeader = $("#header-fixed").append($header);
  
  function setHeaderWidth() {
    
    var bodyWidth = document.getElementById("main_tbody").offsetWidth;
    
    var mainTable = document.getElementById("main_table");
    var mainTableTrs = mainTable.getElementsByTagName("tr");
    var mainTableCells1 = mainTableTrs[0].getElementsByTagName("td");
    var mainTableCells2 = mainTableTrs[1].getElementsByTagName("td");
    
    var headerFixedTable = document.getElementById("header-fixed");
    var headerFixedTableTrs = headerFixedTable.getElementsByTagName("tr");
    var headerFixedTableCells1 = headerFixedTableTrs[0].getElementsByTagName("td");
    var headerFixedTableCells2 = headerFixedTableTrs[1].getElementsByTagName("td");
    
    for (var i = 0; i < mainTableCells1.length; i++) {
      var tdWidth1 = mainTableCells1[i].offsetWidth;
      headerFixedTableCells1[i].style.width = tdWidth1+"px";
    }
    for (var i = 0; i < mainTableCells2.length; i++) {
      var tdWidth2 = mainTableCells2[i].offsetWidth;
      headerFixedTableCells2[i].style.width = tdWidth2+"px";
    }
    $fixedHeader.css('width',bodyWidth+'px');
  }
  $(window).bind("scroll", function() {
    setHeaderWidth();
    var offset = $(this).scrollTop();
    
    if (offset >= tableOffset && $fixedHeader.is(":hidden")) {
      $fixedHeader.show();
    } else if (offset < tableOffset) {
      $fixedHeader.hide();
    }
  });
});

$('#button-download-csv').on('click', function() {
  var base_url = 'index.php?route=seller_panel/paymentreports&token=<?php echo $token; ?>'+'&download=csv';
  var url = base_url;
  var filter_order_no = $('input[name=\'filter_order_no\']').val();
  if (filter_order_no) {
      url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
  }
  var filter_invoice_no = $('input[name=\'filter_invoice_no\']').val();
  if (filter_invoice_no) {
      url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
  }
  var filter_utr = $('input[name=\'filter_utr\']').val();
  if (filter_utr) {
      url += '&filter_utr=' + encodeURIComponent(filter_utr);
  }
  var filter_payment_date_range = $('input[name=\'filter_payment_date_range\']').val();
  if (filter_payment_date_range) {
      url += '&filter_payment_date_range=' + encodeURIComponent(filter_payment_date_range);
  }
  var filter_payment_date_range = $('input[name=\'filter_payment_date_range\']').val();
  if (filter_payment_date_range) {
      url += '&filter_payment_date_range=' + encodeURIComponent(filter_payment_date_range);
  }
  var filter_payment_date_range = $('input[name=\'filter_payment_date_range\']').val();
  if (filter_payment_date_range) {
      url += '&filter_payment_date_range=' + encodeURIComponent(filter_payment_date_range);
  }
  location = url;
});
////////////////////////////////////////////////////////
$('#button-search').on('click', function() {
  var base_url = 'index.php?route=seller_panel/paymentreports&token=<?php echo $token; ?>';
  var url = base_url;
  
  var filter_order_no = $('input[name=\'filter_order_no\']').val();
  if (filter_order_no) {
      url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
  }
  
  var filter_invoice_no = $('input[name=\'filter_invoice_no\']').val();
  if (filter_invoice_no) {
      url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
  }
  
  var filter_utr = $('input[name=\'filter_utr\']').val();
  if (filter_utr) {
      url += '&filter_utr=' + encodeURIComponent(filter_utr);
  }
  
  var filter_invoice_date_range = $('input[name=\'filter_invoice_date_range\']').val();
  if (filter_invoice_date_range) {
      url += '&filter_invoice_date_range=' + encodeURIComponent(filter_invoice_date_range);
  }
  
  var filter_payment_date_range = $('input[name=\'filter_payment_date_range\']').val();
  if (filter_payment_date_range) {
      url += '&filter_payment_date_range=' + encodeURIComponent(filter_payment_date_range);
  }
  location = url;
});

////////////////////////// search on enter key press in input //////////////////////////

$('[id^=input]').keypress(function (e) {
 var key = e.which;
 if(key == 13) {   // the enter key code
    $('#button-search').click();
    return false;  
  }
});

/////////////////////invoice date range date picker//////////////////

$('#input-invoice-date-range').daterangepicker({
  "autoUpdateInput": false,
  "locale": {
    "format": "DD/MM/YYYY",
    "applyLabel": "Update",
    "cancelLabel": "Clear"
  }
}, function(start, end, label) {
$('#input-invoice-date-range').val(start.format('DD/MM/YYYY')+" - "+end.format('DD/MM/YYYY'));
});
$('#input-invoice-date-range').on('cancel.daterangepicker', function(ev, picker) {
  $('#input-invoice-date-range').val('');
});
$('#input-invoice-date-range').on('apply.daterangepicker', function(ev, picker) {
  $('#input-invoice-date-range').val(picker.startDate.format('DD/MM/YYYY')+" - "+picker.endDate.format('DD/MM/YYYY'));
});

/////////////////////payment done date picker//////////////////

$('#input-payment-date-range').daterangepicker({
  "autoUpdateInput": false,
  "locale": {
    "format": "DD/MM/YYYY",
    "applyLabel": "Update",
    "cancelLabel": "Clear"
  }
}, function(start, end, label) {
$('#input-payment-date-range').val(start.format('DD/MM/YYYY')+" - "+end.format('DD/MM/YYYY'));
});
$('#input-payment-date-range').on('cancel.daterangepicker', function(ev, picker) {
  $('#input-payment-date-range').val('');
});
$('#input-payment-date-range').on('apply.daterangepicker', function(ev, picker) {
  $('#input-payment-date-range').val(picker.startDate.format('DD/MM/YYYY')+" - "+picker.endDate.format('DD/MM/YYYY'));
});
</script>

<style type="text/css">
.btn{margin-right: 10px;}
.block{border: 1px solid rgb(221, 221, 221);padding: 3px;margin-bottom: 2px;}
#header-fixed { 
  position: fixed; 
  top: 50px; display:none;
  background-color:white;
  width: 100%;
}
.tooltip_icon{ font-size:120%;margin-left:7px; }
.panel-default{font-size:11px;}
</style>
