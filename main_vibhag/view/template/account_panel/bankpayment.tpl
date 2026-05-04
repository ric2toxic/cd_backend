<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Account Panel'//$page_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Bank Payment'//echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <h3>Import CSV</h3>
        <div class="well">
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">

                    <!--div class="col-sm-3 login_seller">
                      <div class="form-group">
                        <div><h3>CSV Format</h3></div>
                        <a class="form-group" href="<?php //echo $csv_export; ?>"><button class="btn btn-primary">Download </button></a> 
                      </div>
                    </div-->

                    <div class="col-sm-3 login_seller">
                      <div class="form-group">
                        <div><h3>Payment CSV</h3></div>
                        <form action="<?php //echo $csv_import;?>" class="addBankPaymentAjax" enctype="multipart/form-data" method="post">
                          <input type="file" name="fileToUpload" />
                          <input class="btn btn-primary" type="submit" name="submit" value="Import" />
                        </form>
                      </div>
                    </div>

                    <div class="col-sm-3 login_seller">
                      <div class="form-group">
                        <div><h3>Salary CSV</h3></div>
                        <form action="" class="addSalaryAjax" enctype="multipart/form-data" method="post">
                          <input type="file" name="fileToUploadSalary" />
                          <input class="btn btn-primary" type="submit" name="submit" value="Import" />
                        </form>
                      </div>
                    </div>

                    <div class="col-sm-3 login_seller">
                      <div class="form-group">
                        <div><h3>Seller Payment CSV</h3></div>
                        <form action="" class="addSellerPaymentAjax" enctype="multipart/form-data" method="post">
                          <input type="file" name="fileToUploadSellerPayment" />
                          <input class="btn btn-primary" type="submit" name="submit" value="Import" />
                        </form>
                      </div>
                    </div>

                    <div class="col-sm-3 login_seller">
                      <div class="form-group">
                        <div><h3>Buyers Refund (Bank)</h3></div>
                        <form action="" class="addBuyersRefundBulkBankAjax" enctype="multipart/form-data" method="post">
                          <input type="file" name="fileToUploadBuyersRefundBulkBank" />
                          <input class="btn btn-primary" type="submit" name="submit" value="Import" />
                        </form>
                      </div>
                    </div>
                    <div class="col-sm-3 login_seller">
                      <div class="form-group">
                        <div><h3>Buyers Refund (PG)</h3></div>
                        <form action="" class="addBuyersRefundBulkPGAjax" enctype="multipart/form-data" method="post">
                          <input type="file" name="fileToUploadBuyersRefundBulkPG" />
                          <input class="btn btn-primary" type="submit" name="submit" value="Import" />
                        </form>
                      </div>
                    </div>                  
                </div>
        </div>

        <h3>Filters</h3>
        <div class="well">
          <div class="row">
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
                <label class="control-label" for="input-name"><?php echo "Reference"; ?></label>
                <input type="text" name="filter_ref" value="<?php echo $filter_ref; ?>" placeholder="<?php echo $filter_ref; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo 'Page Limit'; ?></label>
                <select name="filter_page_limit" id="input-status" class="form-control">
                  <option value="*">--Select--</option>
                  
                  <?php if(isset($page_limit_array)){ ?>
                  <?php foreach($page_limit_array as $page_limit_value){ ?>
                  <?php
                        if($page_limit_value == $filter_page_limit){
                          $page_selected = 'selected';
                        }else{
                          $page_selected = '';
                        }
                       ?>
                  <option value="<?php echo $page_limit_value; ?>" <?php echo $page_selected; ?>><?php echo $page_limit_value; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="seller_id">Amount From</label>
                <input type="text" class="form-control" value="<?php echo $filter_amount_from; ?>" name="filter_amount_from" placeholder="Amount From" />
              </div>

              <div class="form-group">
                <label class="control-label" for="seller_id">Amount To</label>
                <input type="text" class="form-control" value="<?php echo $filter_amount_to; ?>" name="filter_amount_to" placeholder="Amount To" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo "Status"; ?></label>
                <select name="filter_status" class="form-control filter_status">
                    <!--option value="AllConfirm">All</option-->
                      <?php
                        $filter_statusArry = array( '101' => 'All', '0'  => 'Pending', '1' => 'Done');
                        foreach( $filter_statusArry as $key => $value ) {
                        
                        if( $key == $filter_status ) {
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
                <label class="control-label" for="input-status"><?php echo 'Ledgers (Bank Cr.)'; ?></label>
                <select name="filter_bank" class="form-control filter_bank">
                    <option value="">All</option-->
                    <?php
                      foreach( $banks as $value ) {
                      
                      
                      if( $value['ledger_id'] == $filter_bank ) {
                    ?>
                    <option class="form-control" selected  value="<?php echo $value['ledger_id'];?>" ><?php echo $value['ledger_name'] ?> </option>
                    <?php
                      }
                      else {
                    ?>
                    <option class="form-control" value="<?php echo $value['ledger_id'];?>" > <?php echo  $value['ledger_name'] ?> </option>
                    <?php
                      }
                      }
                    ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status">Ledgers (Outflow Dr.)</label>
                <select name="filter_outflow" class="form-control filter_outflow">
                    <option value="">All</option>
                    <?php
                      foreach( $ledgers as $value ) {
                      
                      
                      if( $value['ledger_id'] == $filter_outflow ) {
                    ?>
                    <option class="form-control" selected  value="<?php echo $value['ledger_id'];?>" ><?php echo $value['ledger_name'] ?> </option>
                    <?php
                      }
                      else {
                    ?>
                    <option class="form-control" value="<?php echo $value['ledger_id'];?>" > <?php echo  $value['ledger_name'] ?> </option>
                    <?php
                      }
                      }
                    ?>
                </select>
              </div>
              <div class="form-group">

                <label class="control-label" for="input-name"><?php echo "Select Order"; ?></label>
                <select name="filter_order" class="form-control filter_order">
                    <option value="">Select Order</option>
                    <?php
                      $filter_orderArry = array( 'ASC'  => 'ASC' , 'DESC' => 'DESC');
                      foreach( $filter_orderArry as $key => $value ) {
                      
                      if( $key == $filter_order ) {
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

            <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
          </div>
        </div>
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <span style="font-weight: bold;">1) Done Entry : <?php echo $doneCount;?></span>
            </div>
            <div class="col-sm-3">
              <span style="font-weight: bold;">2) Pending Entry : <?php echo $pendingCount;?></span>
            </div>
          </div>
        </div>
          <div class="table-responsive">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>S.No</td>
                  <td>Date</td>
                  <td>Bank</td>
                  <td>Amount</td>            
                  <td>Mode</td>
                  <td>Reference</td>
                  <td>Group</td>
                  <td>Outflow (Ledger)</td>
                  <td>File</td>
                  <td>Action</td>
                  <td>Confirm1</td>
                  <td>Confirm2</td>
                  <td>Confirm3</td-->
                </tr>
              </thead>
              <tbody>              


                <?php if ($payments) { ?>
                <?php $i = 0; ?>
                <?php foreach ($payments as $key => $payment) { 

                    $i++;  
                    $statusx1 = $payment['confirm1'] ;
                    
                    $status_text1 = "Not Confirmed";
                    if ($statusx1 == 1 ) {
                       $status_text1 = "<span class='statusx1'>Confirmed</span>";
                       $status_text11 = "Confirmed";
                    }
                    if ($statusx1 == 0 ) {
                       $status_text1 = "<input type='checkbox' value='" . $payment['payment_id'] . "' name='statuschk1' class='statuschk1'>";
                       $status_text11 = "<input type='checkbox'>";
                    }

                    $statusx2 = $payment['confirm2'] ;
                    
                    $status_text2 = "Not Confirmed";
                    if ($statusx2 == 1 ) {
                       $status_text2 = "<span class='statusx2'>Confirmed</span>";
                       $status_text22 = "Confirmed";
                    }
                    if ($statusx2 == 0 ) {
                       $status_text2 = "<input type='checkbox' value='" . $payment['payment_id'] . "' name='statuschk2' class='statuschk2'>";
                       $status_text22 = "<input type='checkbox'>";
                    }

                    $statusx3 = $payment['confirm3'] ;
                    
                    $status_text3 = "Not Confirmed";
                    if ($statusx3 == 1 ) {
                       $status_text3 = "<span class='statusx3'>Confirmed</span>";
                       $status_text33 = "Confirmed";
                    }
                    if ($statusx3 == 0 ) {
                       $status_text3 = "<input type='checkbox' value='" . $payment['payment_id'] . "' name='statuschk3' class='statuschk3'>";
                       $status_text33 = "<input type='checkbox'>";
                    }

                    $trgreen = $payment['ledger_id2'] ;
                    $css = "";
                    if ($trgreen != 0 ) {
                      $css = "trgreen";
                    }
                ?>               
  
                <tr class="parent <?php echo $css; ?>" data-child="child_<?php echo $key; ?>" >
                  <td class="text-left"><?php echo $i; ?></td>
                  <td class="text-left"><?php echo $newDate = date("d-m-Y", strtotime($payment['dated'])); ?></td>
                  <td class="text-left"><?php echo $payment['ledger_name']; ?></td>
                  <td class="text-left"><?php echo $payment['amount']; ?></td>
                  <td class="text-left"><?php echo $payment['mode']; ?></td>
                  <td class="text-left"><?php echo $payment['reference']; ?></td>

                  <td class="text-left">
                      <select name="group_id" id="group_id" class="group_id" payment_id="<?php echo $payment['payment_id']?>">
                        <option value="">--Select Group--</option>
                        <?php
                            if(isset( $groups ) ) {
                              foreach($groups as $group)
                              {
                                if( $group['group_id'] == $payment['group_id']) {
                                ?>
                                  <option selected value="<?php echo $group['group_id']?>" > <?php echo $group['group_name'] ?> </option>
                                <?php
                                }
                                else
                                {
                                ?>
                                  <option value="<?php echo $group['group_id']?>" > <?php echo $group['group_name'] ?> </option>
                                <?php
                                }
                              }
                            }
                        ?>                        
                      </select>
                  </td>
              
                  <td class="text-left">
                    <select name="ledger_id" id="ledger_row_<?php echo $payment['payment_id']?>" class="ledger_id ledger_row_<?php echo $payment['payment_id']?>" payment_id="<?php echo $payment['payment_id']?>" amount="<?php echo $payment['amount'] ?>" dated="<?php echo $payment['dated'] ?>"  ref="<?php echo $payment['reference'] ?>"  bank_name="<?php echo $payment['ledger_name'] ?>">
                          <option value="">Select Ledger</option>
                          <?php
                          if(isset( $ledgers ) ) {
                            foreach($ledgers as $ledger)
                              {
                                if( $ledger['ledger_id'] == $payment['ledger_id2']) {
                                ?>
                                  <option group_id="<?php echo $ledger['group_id']?>" selected value="<?php echo $ledger['ledger_id']?>" > <?php echo $ledger['ledger_name'] ?> </option>
                                <?php
                                }
                                else
                                {
                                ?>
                                  <option group_id="<?php echo $ledger['group_id']?>" value="<?php echo $ledger['ledger_id']?>" > <?php echo $ledger['ledger_name'] ?> </option>
                                <?php
                                }
                              }
                          }
                          ?>
                    </select>
                    <input type="hidden" class="ledger_idHidden_row_<?php echo $payment['payment_id']?>" name="ledger_idHidden" value="<?php echo $payment['ledger_id2'] ?>">
                  </td>
                  <td class="text-left">
                    <?php
                    if( strlen( $payment['imgg'] ) > 0 ) {
                    ?>
                        <a href="<?php echo $payment['imgg'] ?>" target="_blank" width="500" height="50"><i class="fa fa-paperclip"></i></a>
                    <?php
                    }
                    ?>                   
                  </td>


<?php
  if ($statusx1==1 && $statusx2==1)
  {
    if ($user_id != 43)
    {
    ?>
                  <td class="text-left">
                  </td>
    <?php
    }
    else
    {
    ?>
                  <td class="text-left">
                    <form action="<?php //echo $payment['csv_import2']; ?>" class="addBankPaymentSubAjax" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="ledgeridAA" class="ledgeridAA" name="ledgeridAA" value="<?php echo $payment['ledger_id2'] ?>">
                        <input type="hidden" id="groupidAA" class="groupidAA" name="groupidAA" value="<?php echo $payment['group_id'] ?>">
                        <input type="hidden" id="payment_id" class="payment_id" name="payment_id" value="<?php echo $payment['payment_id'] ?>">
                        <input type="hidden" class="amountAjax" name="amountAjax">
                        <input type="hidden" id="datedROAA" class="datedROAA" name="datedROAA" value="<?php echo $payment['dated'] ?>">
                        <input type="hidden" class="refAA" name="refAA" value="<?php echo $payment['reference'] ?>">
                        <input type="file" name="fileToUpload3" id="fileToUpload3">
                        <input class="btn btn-primary" type="submit" name="submit" value="Save" />                        
                    </form>                  
                  </td>
    <?php
    }    
  }
  else
  {
  ?>
                  <td class="text-left">
                    <form action="<?php //echo $payment['csv_import2']; ?>" class="addBankPaymentSubAjax" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="ledgeridAA" class="ledgeridAA" name="ledgeridAA" value="<?php echo $payment['ledger_id2'] ?>">
                        <input type="hidden" id="groupidAA" class="groupidAA" name="groupidAA" value="<?php echo $payment['group_id'] ?>">
                        <input type="hidden" id="payment_id" class="payment_id" name="payment_id" value="<?php echo $payment['payment_id'] ?>">
                        <input type="hidden" class="amountAjax" name="amountAjax">
                        <input type="hidden" id="datedROAA" class="datedROAA" name="datedROAA" value="<?php echo $payment['dated'] ?>">
                        <input type="hidden" class="refAA" name="refAA" value="<?php echo $payment['reference'] ?>">
                        <input type="file" name="fileToUpload3" id="fileToUpload3">                 
                        <input class="btn btn-primary" type="submit" name="submit" value="Save" />
                    </form>
                  </td>
  <?php
  }
?>

<?php
  if ($user_id == 43)
  {
?>  
    <td class="text-left"><?php echo $status_text1 ?></td>
    <td class="text-left"><?php echo $status_text22 ?></td>
    <td class="text-left"><?php echo $status_text33 ?></td>
<?php
  } 
?>
<?php
  if ($user_id == 44)
  {
?>  
    <td class="text-left"><?php echo $status_text11 ?></td>
    <td class="text-left"><?php echo $status_text2 ?></td>
    <td class="text-left"><?php echo $status_text33 ?></td>
<?php
  } 
?>
<?php
  if ($user_id == 45)
  {
?>  
    <td class="text-left"><?php echo $status_text11 ?></td>
    <td class="text-left"><?php echo $status_text22 ?></td>
    <td class="text-left"><?php echo $status_text3 ?></td>
<?php
  } 
?>
                               
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

        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>

<!--/////////////////////////For Buyer's Refund////////////////////-->
  <!--div class="csv2"-->
  <div class="divBuyersRefund">
        <div class="well">
          <h3>Buyer's Refund</h3><br>
           <!--form action="" method="get" id="filter_form"-->
          <input type="hidden" id="amountAA" class="amountAA" name="amountAA">
          <input type="hidden" id="refAA" class="refAA" name="refAA" >
          <input type="hidden" id="bank_nameAA" class="bank_nameAA" name="bank_nameAA" >

          <input type="text" class="form-control order_no" id="order_no" placeholder="Enter Order No" name="order_no" />
          <br>
          <button type="button" id="btnBuyersRefund" class="btnBuyersRefund"> <?php echo 'Save'; ?></button>
          <button type="button" id="btnBuyersRefundCancel" class="btnBuyersRefundCancel"> <?php echo 'cancel'; ?></button>
          <span class="spanx">X</span>
        </div>
  </div>

<!--/////////////////////////For Bounce Entry////////////////////-->
  <div class="divBounce">
        <div class="well">
          <h3>1) Single Bounce Entry</h3><br>
          <button type="button" class="btnBounce2"> <?php echo 'Save'; ?></button>
        </div>

        <div class="well">
          <h3>2) Double Bounce Entry - Opposite entries of Bank Payment</h3><br>
          <input type="hidden" class="receipt_idBounce" name="receipt_idBounce"><!--xxxxxxxxxxxxxxxxx-->
          <table class="table table-bordered table-hover table_order_x ttbl">
            <thead>
              <tr>
                <td></td>
                <td>Date</td>
                <td>Bank</td>
                <td>Amount</td>
                <td>Mode</td>
                <td>Ref.No.</td>
              </tr>
            </thead>
            <tbody id="mytbodyBounce">
            
            </tbody>
          </table>
          <span class="spanx">X</span>
          <button type="button" id="btnBounce" class="btnBounce"> <?php echo 'Save'; ?></button>
          <button type="button" id="btnBounceCancel" class="btnBounceCancel"> <?php echo 'cancel'; ?></button>
        </div>
  </div>
  <div class="divBounce22">
        <div class="well">
          <h3>1) Single Bounce Entry</h3><br>
          <button type="button" class="btnBounce2"> <?php echo 'Save'; ?></button>
        </div>
        <div class="well">
          <h3>2) Double Bounce Entry - Opposite entries of Bank Payment</h3><br>
          <h3>No Entries in Opposite side</h3>
        </div>
        <span class="spanx">X</span>
  </div>
<!--/////////////////////////////////////////////-->
  <div class="ajaXloader" ></div>
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
<style>
.show_product_row + .child{
    display: table-row!important;
}

.divBuyersRefund, .divBounce, .divBounce22 {
  background: #fff none repeat scroll 0 0;
  border: 1px groove #aaa;
  border-radius: 5px;
  display: none;
  right: 1%;
  margin: 15px;
  padding: 15px;
  position: fixed;
  /*top: 30%;*/
  top: 5%;
  z-index:2;
  width: 645px;
  box-shadow: 1px 1px 2px 2px;
}
.divBuyersRefund .spanx, .divBounce .spanx, .divBounce22 .spanx{
    background: #000;
    border-radius: 17px;
    color: #fff;
    font-size: 18px;
    right: -11px;
    padding: 3px 7px;
    position: absolute;
    top: -12px
}
.order_no{display: inline-block; width:79%;  margin-bottom: 10px;}

tr.trgreen {background-color: greenyellow;}
</style>
<script type="text/javascript">
$('.date').datetimepicker({
      pickTime: false
    });
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
<script>   
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
<script>
jQuery('.statuschk1').click(function(){
    
  var valuex = jQuery(this);
  var row_id = jQuery(this).val();
  var confirm = "confirm1";

  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankpayment/editPaymentForConfirm&token=<?php echo $token; ?>',
    type: "POST",
    data : {row_id:row_id,confirm:confirm},
    ContentType:"application/json",
    async:false,
    success: function(response){

      if (response == 1)
      {
        alert("Please done entry Ist");
      } 
      if (response == 11)
      {
        alert("Confirmation Done");
        jQuery( valuex ).hide();
        jQuery( valuex ).after( "<span class='statusx1'>Confirmed</span>");
      }
     
    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus ); 
    }
  });
});
jQuery('.statuschk2').click(function(){
    
  var valuex = jQuery(this);
  var row_id = jQuery(this).val();
  var confirm = "confirm2";

  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankpayment/editPaymentForConfirm&token=<?php echo $token; ?>',
    type: "POST",
    data : {row_id:row_id,confirm:confirm},
    ContentType:"application/json",
    async:false,
    success: function(response){

      if (response == 1)
      {
        alert("Please done entry Ist");
      } 
      if (response == 2)
      {
        alert("Ist Confirmation not done yet!");
      } 
      if (response == 11)
      {
        alert("Confirmation Done");
        jQuery( valuex ).hide();
        jQuery( valuex ).after( "<span class='statusx2'>Confirmed</span>");
      }    
    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus ); 
    }
  });
});
jQuery('.statuschk3').click(function(){
    
  var valuex = jQuery(this);  
  var row_id = jQuery(this).val();
  var confirm = "confirm3";

  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankpayment/editPaymentForConfirm&token=<?php echo $token; ?>',
    type: "POST",
    data : {row_id:row_id,confirm:confirm},
    ContentType:"application/json",
    async:false,
    success: function(response){

      if (response == 1)
      {
        alert("Please done entry Ist");
      } 
      if (response == 2)
      {
        alert("Ist and IInd Confirmation not done yet!");
      } 
      if (response == 11)
      {
        alert("Confirmation Done");
        jQuery( valuex ).hide();
        jQuery( valuex ).after( "<span class='statusx3'>Confirmed</span>");        
      }       
    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus ); 
    }
  });
});
</script>

<script>
jQuery(document).ready(function(){
    $('.group_id').change(function() {

      var group_id = $(this).val();
      var payment_id = $(this).attr('payment_id');

      jQuery('.ledgeridAA').val('');
      jQuery('.groupidAA').val('');
      jQuery('.ledger_idHidden_row_'+payment_id).val('');

      jQuery.ajax(
      {
        url: 'index.php?route=account_panel/bankpayment/getLedgersOfGroup&token=<?php echo $token; ?>',
        type: "POST",
        data : {group_id:group_id, payment_id:payment_id},
        ContentType:"application/json",
        async:false,
      
        success: function(response){
  
          //$("#mytbody").empty();
          //$("#ledger_row_"+ payment_id).empty();
          $(".ledger_row_"+ payment_id).empty();
          //$(".ledger_id").empty();
          response = $.parseJSON(response);

          var data = response.getLedgerOfGroupID;
          var payment_id = response.payment_id;
          var status = response.status;

          if (status == 1)
          {
            //alert("Ledger");
            var tbodyTest = "";
            var tbodyTest = "<option>Select Ledger</option>";
            $.each(data, function(i, item) {

            //<option group_id="7" value="121"> Assets </option>
              tbodyTest += "<option group_id='"+ item.group_id  +"' value='"+ item.ledger_id  +"'>" ;
              tbodyTest += item.ledger_name;
              tbodyTest += "</option>" ;
          
              // $("#mytbody").empty();
              // $("#mytbody").append(tbodyTest );
              // $("#group_id").empty();
              // $("#group_id").append(tbodyTest );   
              // $(".ledger_id").empty();
              // $(".ledger_id").append(tbodyTest );  
              $("#ledger_row_"+ payment_id).empty();
              $("#ledger_row_"+ payment_id).append(tbodyTest );
         
            }); 
          }
          else
          {
            alert("No Ledger in this group"); 
            var tbodyTest = "";
            $("#ledger_row_"+ payment_id).empty();
          }


        },      
        error: function(jqXHR, textStatus, errorThrown)
        {
          alert( textStatus ); 
        }
      });      

   });
});
</script>

<script>
    $(".addBankPaymentSubAjax").on('submit',(function(e) {

    e.preventDefault();
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/addAjaxBankPaymentSub&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      //ContentType:"application/json",
      //async:false,
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status ;
        var row = data.row ;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Invalid Buyer's Refund CSV! Mistake in columns ";
          alert(msg);
        }
        if( status == 2 ) {
          msg = "Please Enter proper date in Buyer's Refund CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 3 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 4 ) {
          msg = "Please Enter Ref.No. in Buyer's Refund CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 44 ) {
          msg = "Please remove Ref.No. in Buyer's Refund CSV, Row No. = "+ row ;
          alert(msg);
        }
        // if( status == 5 ) {
        //   msg = "Error in Reference No(Customer Name not found in oc_order_payment oc_order table), Row No. = "+ row ;
        //   alert(msg);
        // }
        if( status == 5 ) {
          msg = "Error in Order No(Customer Name not found in oc_order), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 6 ) {
          msg = "Please Enter Amount in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 7 ) {
          msg = "Please Enter Positive Amount Value in Buyer's Refund CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 8 ) {
          msg = "Total Amount Not Matched in Buyer's Refund CSV";
          alert(msg);
        }
        
        if( status == 21 ) {
          msg = "Please insert image file";
          alert(msg);
        }

        if( status == 22 ) {
          msg = "Please insert Buyer's Refund CSV";
          alert(msg);
        }        




        if( status == 51 ) {
          msg = "Buyer's Refund CSV has been saved Successfully";
          alert(msg);
          location.reload();
        }
        if( status == 52 ) {
          msg = "Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }
        if( status == 53 ) {
        
          msg = "Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }        
        if( status == 99 ) {
          msg = "Error! Try Again";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery('.ajaXloader').fadeOut(100);
  }));
  
</script>

<script>
    $(".addBankPaymentAjax").on('submit',(function(e) {

    e.preventDefault();

    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/importcsv&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      //ContentType:"application/json",
      //async:true,
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        // if( status == 1 ) {
        //   msg = "Invalid Buyer's Refund CSV! Mistake in columns ";
        //   alert(msg);
        // }
        if( status == 2 ) {
          msg = "Please Enter proper date in Buyer's Refund CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 3 ) {
          msg = "Please Enter Bank Name in CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 4 ) {
          msg = "Please Enter Amount in Numeric in CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 5 ) {
          msg = "Please Enter Positive Amount Value in CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 6 ) {
          msg = "Please Enter Mode of Payment in CSV";
          alert(msg);
        }
        if( status == 7 ) {
          msg = "Please Enter Ref.No. in CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 77 ) {
          msg = "Duplicate Ref.No. not allowed from backend, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 777 ) {
          msg = "Duplicate Ref.No. not allowed on CSV (frontend)";
          alert(msg);
        }
        if( status == 8 ) {
          msg = "Please Enter Perfect CSV of Bank Payment";
          alert(msg);
        }

        if( status == 22 ) {
          msg = "Please insert Bank Payment CSV";
          alert(msg);
        }             
        if( status == 53 ) {
          //jQuery('.ajaXloader').fadeOut(100);
          msg = "Bank Payment CSV has been entered Successfully";
          alert(msg);
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }  
     
        // if( status == 99 ) {
        //   msg = "Error! Try Again";
        //   alert(msg);
        //   window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
        // }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery('.ajaXloader').fadeOut(100);
  }));
  
</script>
<script>
    $(".addSalaryAjax").on('submit',(function(e) {

    e.preventDefault();
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/importSalaryCSV&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      //ContentType:"application/json",
      //async:false,
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Please insert Salary CSV";
          alert(msg);
        }
        if( status == 11 ) {
          msg = "Please insert Correct Salary CSV";
          alert(msg);
        }         
        if( status == 2 ) {
          msg = "Please Enter Ref.No. in Salary CSV, Row No. = "+ row ;
          alert(msg);
        }

        if( status == 22 ) {
          msg = "Ref No. not found in Payment Entry, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 23 ) {
          msg = "Entry already done, Please enter new Ref No. in Salary CSV, Row No. = "+ row ;
          alert(msg);
        }        
        if( status == 222 ) {
          msg = "Duplicate Ref.No. not allowed on frontend, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 3 ) {
          msg = "Please Enter Employee Code in Salary CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 333 ) {
          msg = "Employee Code not found in Ledger, Please create ledger Ist, Row No. = "+ row ;
          alert(msg);
        }              



        if( status == 51 ) {
          msg = "Salary CSV has been imported Successfully";
          alert(msg);
          //window.location='';
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }       
        if( status == 99 ) {
          msg = "Error! Try Again";
          alert(msg);
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery('.ajaXloader').fadeOut(100);
  }));
  
</script>
<!--/////////////////////////////////////////////-->
<script type="text/javascript">
  function myFunction() {
    //alert('hi');

      var ledgerid = $('.ledgeridAA').val();
      var groupid = $('.groupidAA').val();
      var payment_id = $('.payment_id').val();
      var amountOfPayment = $('.amountAA').val();
      var datedROAA = $('.datedROAA').val();
      var ref = $('.refAA').val();
      var bank_name = $('.bank_nameAA').val();

      jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/addAjaxSellerPayment&token=<?php echo $token; ?>',
      type: "POST",
      data : {ledgerid:ledgerid,groupid:groupid,payment_id:payment_id,amountOfPayment:amountOfPayment,datedROAA:datedROAA,ref:ref,bank_name:bank_name},
      ContentType:"application/json",
      //async:false,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          //msg = "Order No. not found in oc_order";
          msg = "No Entry found in oc_seller_invoice, oc_seller_debit_note, oc_wsb_purchase";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Entry already exists";
          alert(msg);
        }

        if( status == 11 ) {
          msg = "Seller ID should be unique in all Seller Payment's table according to bank reference No.";
          alert(msg);
        }

        if( status == 65 ) {
          msg = "Seller invoice meta not found in oc_seller_invoice";
          alert(msg);
        }

        if( status == 66 ) {
          msg = "Seller invoice meta not found in oc_debit_note";
          alert(msg);
        }

        if( status == 67 ) {
          msg = "Seller firm meta not found in oc_wsb_purchase";
          alert(msg);
        }

        if( status == 12 ) {
          msg = "Total Amount Not Matched, Diff  of Rs. = "+ row ;
          alert(msg);
        }

        if( status == 51 ) {
          msg = "Seller Payment Entry has been saved Successfully";
          alert(msg);
          //window.location='';
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
  }
</script>
<script>
jQuery(document).ready(function(){
      jQuery('.ledger_id').change(function() {

      var ledgerid = $(this).val(); //for hidden input type
      var group_id = $('option:selected', this).attr('group_id');
      var payment_id = $(this).attr('payment_id');
      var amount = $(this).attr('amount');
      var dated = $(this).attr('dated');      
      var ref = $(this).attr('ref');      
      var bank_name = $(this).attr('bank_name');      

      //jQuery('.ledgeridAA').val(ledgerid);
      jQuery('.ledger_idHidden_row_'+payment_id).val(ledgerid);
      jQuery('.ledgeridAA').val($('.ledger_idHidden_row_'+payment_id).val());
      jQuery('.groupidAA').val(group_id);
      jQuery('.payment_id').val(payment_id);
      jQuery('.amountAjax').val(amount);
      jQuery('.amountAA').val(amount);
      jQuery('.datedROAA').val(dated);    
      jQuery('.refAA').val(ref);    
      jQuery('.bank_nameAA').val(bank_name);

      var buyersRefundLedgerId = 11;
      var sellerPaymentLedgerID = 12;
      //var bounceLedgerId = 372;//local ledger id
      var bounceLedgerId = 3936;//live ledger id

      if (ledgerid == buyersRefundLedgerId)
      {
        $('.divBuyersRefund').show();
        $(".ledger_id").prop('disabled', 'disabled');
        $(".group_id").prop('disabled', 'disabled');
        $("input[type='checkbox']").prop('disabled', 'disabled');

        $('.divBounce22').hide();
        $('.divBounce').hide();
        $("form.addBankPaymentSubAjax .btn").prop('disabled', 'disabled');
      }
      if (ledgerid == sellerPaymentLedgerID)
      {
        //alert("ok");
        if(confirm("Are you sure you want this entry for seller payment ?")){
            //$("#delete-button").attr("href", "query.php?ACTION=delete&ID='1'");
            //return 1;
            myFunction();
        }
        else{
            //return false;
            //alert("zz");
        }
      }
      if (ledgerid == bounceLedgerId)
      {
        //alert(ledgerid);die;
        //myFunctionBounce(amount, ledgerid);
        myFunctionBounce(payment_id);
      }
   });
});
</script>
<script type="text/javascript">
  //function myFunctionBounce(amount, ledgerid) {
  function myFunctionBounce(payment_id) {

    var tbodyTest = "";

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/getReceiptByAmountLedgerID&token=<?php echo $token; ?>',
      type: "POST",
      data : {payment_id:payment_id},
      ContentType:"application/json",
      async:false,
      success: function(response){
  
        $("#mytbodyBounce").empty();

        response = $.parseJSON(response);

        var data = response.getReceiptByAmountLedgerID ;
        var norecord = response.norecord ;
        if (norecord == 0) {
            //alert("No Record found");
            $('.divBounce22').show();
            $('.divBounce').hide();
            $('.divBuyersRefund').hide();
            $(".ledger_id").prop('disabled', 'disabled');
            $(".group_id").prop('disabled', 'disabled');
            $("input[type='checkbox']").prop('disabled', 'disabled');
            $("form.addBankPaymentSubAjax .btn").prop('disabled', 'disabled');
        }
        else
        {
          var radioReceiptIDBounce;
          var tbodyTest = "";

          $.each(data, function(i, item) {
            
            radioReceiptIDBounce = "<input type='radio' name='radioReceiptIDBounce' value='"+ item.receipt_id +"' class='radioReceiptIDBounce'>";

            tbodyTest += "<tr>";
            
            tbodyTest += "<td>";
            tbodyTest += radioReceiptIDBounce;
            tbodyTest += "</td>";

            // tbodyTest += "<td>";
            // tbodyTest += item.payment_id;
            // tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.dated;
            tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.ledger_name;
            tbodyTest += "</td>";

            tbodyTest += "<td>" ;
            tbodyTest += item.amount;
            tbodyTest += "</td>" ; 

            tbodyTest += "<td>" ;
            tbodyTest += item.mode;
            tbodyTest += "</td>" ;    

            tbodyTest += "<td>" ;
            tbodyTest += item.reference;
            tbodyTest += "</td>" ;       

            tbodyTest += "</tr>" ;

            $("#mytbodyBounce").empty();
            $("#mytbodyBounce").append(tbodyTest );

           });

          $('.divBounce').show();
          $('.divBuyersRefund').hide();
          $(".ledger_id").prop('disabled', 'disabled');
          $(".group_id").prop('disabled', 'disabled');
          $("input[type='checkbox']").prop('disabled', 'disabled');
          $("form.addBankPaymentSubAjax .btn").prop('disabled', 'disabled');
        }
      
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });

    ///////radiobtn
    jQuery('.radioReceiptIDBounce').click(function(){  
      var value = $(this).val();
      jQuery('.receipt_idBounce').val(value);
    });
  }
</script>
<script type="text/javascript">
  jQuery('.btnBounce').click( function(){//xxxxxxxxxxxxxxxxxx

    $(".btnBounce").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var receipt_idBounce = $('.receipt_idBounce').val();
    var payment_id = $('.payment_id').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/addAjaxBounce&token=<?php echo $token; ?>',
      type: "POST",
      data : {receipt_id:receipt_idBounce,payment_id:payment_id},
      ContentType:"application/json",
      async:true,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Try Againx!";
          alert(msg);
          $(".btnBounce").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 51 ) {
          msg = "Bounce entry relatively of both sides has been saved Successfully";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //$(".btnSave22").prop('disabled', false);
    //$('.ajaXloader').fadeOut(100);
  });
</script>
<script type="text/javascript">
  jQuery('.btnBounce2').click( function(){//xxxxxxxxxxxxxxxxxx

    $(".btnBounce2").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var ledgerid = $('.ledgeridAA').val();
    var groupid = $('.groupidAA').val();
    var payment_id = $('.payment_id').val();
    var amountOfPayment = $('.amountAA').val();
    var datedROAA = $('.datedROAA').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/addAjaxBounce2&token=<?php echo $token; ?>',
      type: "POST",
      data : {ledgerid:ledgerid,groupid:groupid,payment_id:payment_id,amountOfPayment:amountOfPayment,datedROAA:datedROAA},
      ContentType:"application/json",
      async:true,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Try Againx!";
          alert(msg);
          $(".btnBounce2").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 51 ) {
          msg = "Bounce entry has been saved Successfully";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //$(".btnSave22").prop('disabled', false);
    //$('.ajaXloader').fadeOut(100);
  });
</script>
<script>
  jQuery('.btnBuyersRefundCancel').click( function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.payment_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");
      
      $('.order_no').val("");
      $("#mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $(".group_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $('.divBuyersRefund').hide();

      $(".mytbodyBounce").empty();
      $('.divBounce').hide();
      $("form.addBankPaymentSubAjax .btn").prop('disabled', false);
      $('.divBounce22').hide();

  });
  jQuery('.btnBounceCancel').click( function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.payment_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");
      
      $('.order_no').val("");
      $("#mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $(".group_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $('.divBuyersRefund').hide();

      $(".mytbodyBounce").empty();
      $('.divBounce').hide();
      $("form.addBankPaymentSubAjax .btn").prop('disabled', false);
      $('.divBounce22').hide();
  });
  $('.spanx').click(function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.payment_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");      
      
      $("#mytbody").empty();
      $('.order_no').val("");
      $(".ledger_id").prop("disabled", false);
      $(".group_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $('.divBuyersRefund').hide();

      $(".mytbodyBounce").empty();
      $('.divBounce').hide();
      $("form.addBankPaymentSubAjax .btn").prop('disabled', false);
      $('.divBounce22').hide();
  });

  jQuery('.btnBuyersRefund').click( function(){

    var order_no = $('.order_no').val();

      var ledgerid = $('.ledgeridAA').val();
      var groupid = $('.groupidAA').val();
      var payment_id = $('.payment_id').val();
      var amountOfPayment = $('.amountAA').val();
      var datedROAA = $('.datedROAA').val();
      var ref = $('.refAA').val();
      var bank_name = $('.bank_nameAA').val();

      jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/addAjaxBuyersRefund&token=<?php echo $token; ?>',
      type: "POST",
      data : {order_no:order_no,ledgerid:ledgerid,groupid:groupid,payment_id:payment_id,amountOfPayment:amountOfPayment,datedROAA:datedROAA,ref:ref,bank_name:bank_name},
      ContentType:"application/json",
      //async:false,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Order No. not found in oc_order";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Entry already exists";
          alert(msg);
        }        


        if( status == 51 ) {
          msg = "Buyer's Refund Entry has been saved Successfully";
          alert(msg);
          //window.location='';
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus );
      }
    });
  }); 

</script>

<script>
    $(".addSellerPaymentAjax").on('submit',(function(e) {

    e.preventDefault();
    
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/importSellerPaymentCSV&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      //ContentType:"application/json",
      //async:false,
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var diff = data.diff;
        var msg = "";
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( data.status22) {
          msg = "1) Ref No. not found in Payment Entry :- Ref.No. = "+ data.row22 ;
          msg += "\n";
          //alert(msg);
        }
        if( data.status23 ) {
          msg += "2) Entry already done, Please enter new Ref No. in Seller Payment CSV :- Ref.No. = "+ data.row23 ;
          msg += "\n";
          //alert(msg);
        }

        if( data.status31 ) {
          msg += "3) No Entry found in oc_seller_invoice, oc_seller_debit_note, oc_wsb_purchase :- Ref.No. = "+ data.row31 ;
          msg += "\n";
          //alert(msg);
        }
        if (msg)
        {
          alert(msg);
        }


//////////////////        
        if( status == 1 ) {
          msg = "Please insert Seller Payment CSV";
          alert(msg);
        }
        if( status == 11 ) {
          msg = "Please insert Correct Seller Payment CSV";
          alert(msg);
        }         
        if( status == 2 ) {
          msg = "Please Enter Ref.No. in Seller Payment CSV, Row No. = "+ row ;
          alert(msg);
        }

        if( status == 22 ) {
          msg = "Ref No. not found in Payment Entry, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 23 ) {
          msg = "Entry already done, Please enter new Ref No. in Seller Payment CSV, Row No. = "+ row ;
          alert(msg);
        }        
        if( status == 222 ) {
          //msg = "Duplicate Ref.No. not allowed on frontend (in CSV itself), Row No. = "+ row ;
          msg = "Duplicate Ref.No. not allowed on frontend (in CSV itself)";
          alert(msg);
        }
            
        if( status == 31 ) {
          msg = "No Entry found in oc_seller_invoice, oc_seller_debit_note, oc_wsb_purchase, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 32 ) {
          msg = "Seller ID should be unique in all Seller Payment's table according to bank reference No. Row No. = "+ row ;
          alert(msg);
        }
        if( status == 33 ) {
          //msg = "Total Amount Not Matched, Diff  of Rs. = "+ row ;//row no also
          msg = "Total Amount Not Matched, Diff  of Rs. = "+ diff +", in row No. "+ row;//row no also
          alert(msg);
        }

        if( status == 65 ) {
          msg = "Seller invoice meta not found in oc_seller_invoice";
          alert(msg);
        }

        if( status == 66 ) {
          msg = "Seller invoice meta not found in oc_debit_note";
          alert(msg);
        }

        if( status == 67 ) {
          msg = "Seller firm meta not found in oc_wsb_purchase, Row No. = "+ row ;
          alert(msg);
        }

        if( status == 51 ) {
          msg = "Seller Payment CSV has been imported Successfully";
          alert(msg);
          //window.location='';
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }       
        if( status == 99 ) {
          msg = "Error! Try Again";
          alert(msg);
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery('.ajaXloader').fadeOut(100);
  }));
  
</script>

<script>
    $(".addBuyersRefundBulkPGAjax").on('submit',(function(e) {

    e.preventDefault();
    
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/importBuyersRefundBulkCSVPG&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      //ContentType:"application/json",
      //async:false,
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var diff = data.diff;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Please insert Buyer's Refund Bulk PG CSV";
          alert(msg);
        }
        if( status == 2 ) {
          msg = "Please Enter proper date in Buyer's Refund Bulk PG CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 3 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        // if( status == 5 ) {
        //   msg = "Error in Reference No(Customer Name not found in oc_order_payment oc_order table), Row No. = "+ row ;
        //   alert(msg);
        // }
        if( status == 4 ) {
          msg = "Error in Order No(Customer Name not found in oc_order), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 5 ) {
          msg = "Please Enter Ref.No. in Buyer's Refund Bulk PG CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 6 ) {
          msg = "Please Enter Amount in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 7 ) {
          msg = "Please Enter Positive Amount Value in Buyer's Refund Bulk PG CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 8 ) {
          msg = "Please Enter Bank's Ref.No., Row No. = "+ row ;
          alert(msg);
        }
        if( status == 9 ) {
          msg = "Please Enter Payment Gateway, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 10 ) {
          msg = "Please Enter correct Payment Gateway name which is citrus, paytm, razorpay & neo growth, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 11 ) {
          msg = "Payment Gateway should not be duplicate in Buyer's Refund Bulk PG CSV" ;
          alert(msg);
        }
        if( status == 12 ) {
          msg = "Bank Ref No. not found in Bank Payment Entry, Ref.No. = "+ row ;
          alert(msg);
        }
        if( status == 13 ) {
          msg = "Entry already done, Please enter new Bank Ref No. in Buyer's Refund Bulk PG CSV, Ref.No. = "+ row ;
          alert(msg);
        }
        if( status == 14 ) {
          msg = "Amount not matched. from Bank Statement on Bank Ref.No. = "+ row ;
          alert(msg);
        }



        if( status == 50 ) {
          msg = "Please insert correct Buyer's Refund Bulk PG CSV";
          alert(msg);
        }

        if( status == 51 ) {
          msg = "Buyer's Refund Bulk PG CSV has been imported Successfully";
          alert(msg);
          location.reload();
        }       
        if( status == 99 ) {
          msg = "Error! Try Again";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery('.ajaXloader').fadeOut(100);
  }));  
</script>

<script>
    $(".addBuyersRefundBulkBankAjax").on('submit',(function(e) {

    e.preventDefault();
    
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankpayment/importBuyersRefundBulkCSVBank&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      //ContentType:"application/json",
      //async:false,
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var diff = data.diff;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Please insert Buyer's Refund Bulk Bank CSV";
          alert(msg);
        }
        if( status == 2 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 3 ) {
          msg = "Error in Order No(Customer Name not found in oc_order), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 4 ) {
          msg = "Duplicate Bank Ref. No. not allowed in CSV";
          alert(msg);
        }
        if( status == 5 ) {
          msg = "Please Enter Bank's Ref.No., Row No. = "+ row ;
          alert(msg);
        }
        if( status == 6 ) {
          msg = "Bank Ref No. not found in Bank Payment Entry, Row.No. = "+ row ;
          alert(msg);
        }
        if( status == 7 ) {
          msg = "Entry already done, Please enter new Bank Ref No. in Buyer's Refund Bulk CSV, Row.No. = "+ row ;
          alert(msg);
        }
        if( status == 8 ) {
          msg = "Rows counting errors, Order No. and Bank Ref. No. should have same rows" ;
          alert(msg);
        }



        if( status == 50 ) {
          msg = "Please insert correct Buyer's Refund Bulk CSV";
          alert(msg);
        }

        if( status == 51 ) {
          msg = "Buyer's Refund Bulk CSV has been imported Successfully";
          alert(msg);
          //window.location='';
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }       
        if( status == 99 ) {
          msg = "Error! Try Again";
          alert(msg);
          //window.location='index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery('.ajaXloader').fadeOut(100);
  }));
  
</script>


<script type="text/javascript"><!--
$('.button-filter').on('click', function() {

  //var url = 'index.php?route=catalog/product&token=<?php echo $token; ?>';
  var url = 'index.php?route=account_panel/bankpayment&token=<?php echo $token; ?>';

  var filter_ref = $('input[name=\'filter_ref\']').val();
  if (filter_ref) {
    url += '&filter_ref=' + encodeURIComponent(filter_ref);
  }  
  var filter_date_from = $('input[name=\'filter_date_from\']').val();
  if (filter_date_from) {
    url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
  }

  var filter_date_to = $('input[name=\'filter_date_to\']').val();
  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

  var filter_order = $('select[name=\'filter_order\']').val();
  if (filter_order != '*') {
    url += '&filter_order=' + encodeURIComponent(filter_order);
  }

  var filter_page_limit = $('select[name=\'filter_page_limit\']').val();

  if (filter_page_limit != '*') {
    url += '&filter_page_limit=' + encodeURIComponent(filter_page_limit);
  }

  var filter_amount_from = $('input[name=\'filter_amount_from\']').val();
  if (filter_amount_from) {
    url += '&filter_amount_from=' + encodeURIComponent(filter_amount_from);
  }  

  var filter_amount_to = $('input[name=\'filter_amount_to\']').val();
  if (filter_amount_to) {
    url += '&filter_amount_to=' + encodeURIComponent(filter_amount_to);
  }

  var filter_bank = $('select[name=\'filter_bank\']').val();
  if (filter_bank) {
    url += '&filter_bank=' + encodeURIComponent(filter_bank);
  }

  var filter_outflow = $('select[name=\'filter_outflow\']').val();
  if (filter_outflow) {
    url += '&filter_outflow=' + encodeURIComponent(filter_outflow);
  }

  var filter_status = $('select[name=\'filter_status\']').val();
  if (filter_status) {
    url += '&filter_status=' + encodeURIComponent(filter_status);
  }

  location = url;
  
});

  
  $('.date').datetimepicker({
      pickTime: false
  });
</script>