<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Ledger with Detail'//$page_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Ledger with Detail'//$heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <form action="" method="get" id="filter_form">
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group">
                          <select name="ledger_id" id="ledger_id">
                              <option value="">Select Ledger</option>>
                              <?php
                                if(isset( $ledgers ) ) {
                                  foreach($ledgers as $ledger)
                                  {
                                  ?>
                                      <option value="<?php echo $ledger['ledger_id']; ?>"
                                              <?php echo $ledger['ledger_id'] == $ledger_id ? 'selected' : '';  ?>  >
                                              <?php echo $ledger['ledger_name'];//echo $ledger['ledger_id']; ?>
                                      </option>
                                 <?php  
                                  }
                                }
                              ?>
                          </select>
                          
                          <button type="submit" form="filter_form" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>                          
                        </div>                        
                    </div>
                    <!--div class="col-sm-4">
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Date From</label>
                            <div class="input-group date">
                              <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php //echo $filter_date_from; ?>" name="filter_date_from" placeholder="Date From" />
                              <span class="input-group-btn">
                                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                              </span>
                            </div>
                        </div>
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Date To</label>
                            <div class="input-group date">
                                <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php //echo $filter_date_to; ?>" name="filter_date_to" placeholder="Date To" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    </div-->
                </div>
            </form>
        </div>
        <form action="<?php //echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive analysis">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>S.No.</td>
                  <td class="text-left" colspan="7">
                    <table >
                      <tr>
                        <td width="50px">Dated</td>
                        <td>Order ID</td>
                        <td>Order No.</td>
                        <td>Sub Order No.</td>
                        <td style="width:180px!important">Customer</td>
                        <td>Invoice Amt</td>
                        <td>Credit Note</td>
                      </tr>
                    </table>
                  </td> 
                  <td>receipts</td>
                  <td>Payments</td>
                  <td>Balance</td>
                  
                </tr>
              </thead>
              <tbody>              
                <?php if (!empty($subOrders)) { ?>
                <?php $i = 1; ?>
                <?php foreach ($subOrders as $key => $subOrder) {  

                  $totalInvoiceAmt = 0;
                  $totalcreditNoteAmt = 0;
                ?>               
                <tr class="parent" data-child="child_<?php echo $key; ?>" >
                  <td class="text-left"><?php echo $i; ?></td>
                  <td class="text-left" colspan="7">
                    <table>
                   <?php 
                      foreach($subOrder['suborder'] as $suborderVal){

                        $ledgerName = $suborderVal['customer_id'];
                        $ledgerName .= (!empty(trim($suborderVal['firstname'])) ? '_' . trim($suborderVal['firstname']) : '');
                        $ledgerName .= (!empty(trim($suborderVal['lastname'])) ? '_' . trim($suborderVal['lastname']) : '');
                        $ledgerName .= (!empty(trim($suborderVal['payment_company'])) ? '_' . trim($suborderVal['payment_company']) : '');
                        $totalInvoiceAmt += $suborderVal['invoiceAmt'];
                        $totalcreditNoteAmt += $suborderVal['creditNoteAmt'];
                      ?>
                        <tr class="parent" data-child="child_<?php echo $key; ?>" >
                            <td class="text-left"><?php echo $newDate = date("d-m-Y", strtotime($suborderVal['date_added'])); ?></td>
                            <td class="text-left"><?php echo $suborderVal['order_id']; ?></td>
                            <td class="text-left"><?php echo $suborderVal['order_no']; ?></td>
                            <td class="text-left"><?php echo $suborderVal['suborder_id']; ?></td>
                            <td style="width:180px!important"><?php echo $ledgerName; ?></td>
                            <td class="text-left"><?php echo $suborderVal['invoiceAmt']; ?></td>
                            <td class="text-left"><?php echo $suborderVal['creditNoteAmt']; ?></td>
                        </tr>
                      <?php
                      }
                   ?>
                    </table>
                  </td>
                  <td class="text-left"><?php echo $subOrder['receipts']; ?></td>
                  <td class="text-left"><?php echo $subOrder['payments']; ?></td>
                  <td class="text-left">
                    <?php 
                      $totalBal = $totalInvoiceAmt - $totalcreditNoteAmt - $subOrder['receipts'] + $subOrder['payments']; 
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
        <!-- <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div> -->
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
