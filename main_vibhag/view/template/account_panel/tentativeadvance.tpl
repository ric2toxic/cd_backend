<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Tentative Advance'//$page_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Tentative Advance'//echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">

        <h3>Filters</h3>
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name">Order No.</label>
                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $filter_order_no; ?>" id="input-name" class="form-control" />
              </div>

              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo "Reference No."; ?></label>
                <input type="text" name="filter_ref" value="<?php echo $filter_ref; ?>" placeholder="<?php echo $filter_ref; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo "Status"; ?></label>
                <select name="filter_confirm" class="form-control filter_confirm">
                    <!--option value="AllConfirm">All</option-->
                      <?php
                        $filter_confirmArry = array( '101' => 'All Status', '0'  => 'Pending', '1' => 'Verified', '2' => 'Rejected', '3' => 'Cancel', '4' => 'Return To Customer');
                        foreach( $filter_confirmArry as $key => $value ) {
                        
                        if( $key == $filter_confirm ) {
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
                  <label class="control-label" for="input-name">Transaction Status</label>
                  <select name="filter_status" class="form-control filter_status">
                      <option value="all">All</option>
                        <?php
                          $filter_statusArry = array( 'will_collect'  => 'will_collect' , 'will_deposit' => 'will_deposit',  'deposited' => 'deposited',  'will_transact' => 'will_transact',  'transacted' => 'transacted',  'on_credit' => 'on_credit');
                          foreach( $filter_statusArry as $key => $value ) {
                          
                          if( $key == $filter_status ) {
                        ?>
                        <option class="form-control" selected value="<?php echo $key  ?>" > <?php echo $value ?> </option>
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
                  <label class="control-label" for="seller_id">Amount From</label>
                  <input type="text" class="form-control" value="<?php echo $filter_amount_from; ?>" name="filter_amount_from" placeholder="Amount From" />
              </div>
              <div class="form-group">
                  <label class="control-label" for="seller_id">Amount To</label>
                  <input type="text" class="form-control" value="<?php echo $filter_amount_to; ?>" name="filter_amount_to" placeholder="Amount To" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo 'Staff'; ?></label>
                <select name="filter_staff" class="form-control filter_staff">
                    <option value="">All</option-->
                      <?php
                        foreach( $staffs as $value ) {
                        
                        
                        if( $value['staff_id'] == $filter_staff ) {
                      ?>
                      <option class="form-control" selected  value="<?php echo $value['staff_id'];?>" ><?php echo $value['name'] ?> </option>
                      <?php
                        }
                        else {
                      ?>
                      <option class="form-control" value="<?php echo $value['staff_id'];?>" > <?php echo  $value['name'] ?> </option>
                      <?php
                        }
                        }
                      ?>
                </select>
              </div>

              <div class="form-group date">
                <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
            </div>


            
          </div>
        </div>

          <div class="table-responsive">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>Date</td>
                  <td>Staff Member</td>
                  <td>Order No.</td>            
                  <td>Payment Mode</td>
                  <!--td>Cheque No</td-->
                  <td>Ref.No.</td>
                  <td>Collection Date</td>
                  
                  <!--td>txn id</td-->
                  
                  <td>Amount</td>
                  <td>notes</td>
                  <!--td>Bank Deposited</td-->
                  <td>Image</td>
                  <td>Branch</td>
                  <td>Transaction Status</td>
                  <td>Date created</td>
                  <td>Msg.Admin</td>
                  <td>Status</td>
                  <td>Action</td>
                </tr>
              </thead>
              <tbody>              


                <?php if ($tentativeAdvances) { ?>

                <?php $i = 0; ?>
                <?php foreach ($tentativeAdvances as $key => $tentativeAdvance) { 
                    $i++;  

                    if ($tentativeAdvance['collection_date'] == '0000-00-00')
                    {
                      $tentativeAdvance['collection_date'] = "";
                    }
                    else
                    {
                      $tentativeAdvance['collection_date'] = date("d-m-Y", strtotime($tentativeAdvance['collection_date']));
                    }
                    if ($tentativeAdvance['bank_deposited'] == 1)
                    {
                      $tentativeAdvance['bank_deposited'] = "Deposited";
                    }
                    else
                    {
                      $tentativeAdvance['bank_deposited'] = "";
                    }


                    $refNo = "";
                    if ($tentativeAdvance['merchant_txn_id'] != "")
                    {
                      if ($tentativeAdvance['cheque_no'] != "")
                      {
                        $refNo = $tentativeAdvance['cheque_no'].', '.$tentativeAdvance['merchant_txn_id'];
                      }
                      if ($tentativeAdvance['txn_id'] != "")
                      {
                        $refNo = $tentativeAdvance['txn_id'].', '.$tentativeAdvance['merchant_txn_id'];
                      }
                      if ($tentativeAdvance['payment_mode'] == 'cash')
                      {
                        $refNo = $tentativeAdvance['merchant_txn_id'];
                      }
                    }
                    else
                    {
                      if ($tentativeAdvance['cheque_no'] != "")
                      {
                        $refNo = $tentativeAdvance['cheque_no'];
                      }
                      if ($tentativeAdvance['txn_id'] != "")
                      {
                        $refNo = $tentativeAdvance['txn_id'];
                      }
                    }


                    $trgreen = $tentativeAdvance['confirm'] ;
                    $css = "";
                    if ($trgreen == 1 ) {
                      $css = "trgreen";
                    }
                    elseif ($trgreen == 2 ) {
                      $css = "trred";
                    }

                ?>

                <tr class="parent <?php echo $css; ?>" data-child="child_<?php echo $key; ?>" >
                  <td class="text-left"><?php echo $newDate = date("d-m-Y", strtotime($tentativeAdvance['dated'])); ?></td>
                  <td class="text-left"><?php echo $tentativeAdvance['staff_name']; ?></td>
                  <td class="text-left"><?php echo $tentativeAdvance['order_no']; ?></td>
                  <td class="text-left"><?php echo $tentativeAdvance['payment_mode']; ?></td>
                  <!--td class="text-left"><?php //echo $tentativeAdvance['cheque_no']; ?></td-->
                  <td class="text-left"><?php echo $refNo; ?></td>
                  <td class="text-left"><?php echo $tentativeAdvance['collection_date']; ?></td>


                  <!--td class="text-left"><?php //echo $tentativeAdvance['txn_id']; ?></td-->
                  
                  <td class="text-left"><?php echo $tentativeAdvance['amount']; ?></td>
                  <td class="text-left"><?php echo $tentativeAdvance['notes']; ?></td>
                  <!--td class="text-left"><?php //echo $tentativeAdvance['bank_deposited']; ?></td-->

                  <td class="text-left">
                    <?php
                    if( strlen( $tentativeAdvance['bank_deposited_image'] ) > 0 ) {
                    ?>
                        <a href="<?php echo $tentativeAdvance['bank_deposited_image'] ?>" target="_blank" width="500" height="50"><i class="fa fa-paperclip"></i></a>
                        <!--a target="blank" href="<?php //echo $tentativeAdvance['bank_deposited_image'] ?>"><img  width="80" src="<?php //echo $tentativeAdvance['bank_deposited_image'] ?>" > </a-->
                    <?php
                    }
                    ?>                   
                  </td>
                  <td class="text-left"><?php echo $tentativeAdvance['branch_name']; ?></td>
                  <td class="text-left"><?php echo $tentativeAdvance['transaction_status']; ?></td>
                  <td class="text-left"><?php echo $newDate = date("d-m-Y H:i:s", strtotime($tentativeAdvance['date_created'])); ?></td>
                  <td class="text-left">
                    <input type="text" name="txtmsgadmin" class="halfinput txtmsgadmin" id="txtmsgadmin" value="<?php echo $tentativeAdvance['msgadmin'];?>">
                  </td>

                  <td class="text-left">
                        <?php
                          if( $tentativeAdvance['confirm'] != 1) {
                            if( $tentativeAdvance['transaction_status'] == 'deposited' || $tentativeAdvance['transaction_status'] == 'transacted') {

                              $confirmArry = array( 0  => 'Pending' , 1 => 'Verified', 2 => 'Rejected', 3 => 'Cancel', 4 => 'Return to customer');
                              if( $tentativeAdvance['confirm'] == 1) {

                              ?>
                                
                                <select name="confirm" id="confirm" class="form-control confirm" tentative_advance_id="<?php echo $tentativeAdvance['tentative_advance_id']?>">
                                  <option class="form-control" selected value="1">Verified</option>
                                </select>
                              <?php
                              }
                              else
                              {
                              ?>
                                <select name="confirm" id="confirm" class="form-control confirm" tentative_advance_id="<?php echo $tentativeAdvance['tentative_advance_id']?>">
                                  <?php
                                    foreach( $confirmArry as $key => $value ) {
                                      if( $key == $tentativeAdvance['confirm'] ) {
                                      ?>
                                        <option class="form-control" selected value="<?php echo $key;?>"><?php echo $value ?></option>
                                      <?php
                                      }
                                      else
                                      {
                                      ?>
                                        <option class="form-control" value="<?php echo $key;?>"><?php echo $value ?></option>
                                      <?php
                                      }
                                    }
                                  ?>
                                </select> 
                              <?php
                              }
                            }
                          }
                        ?>
                  </td>

                  <td class="text-left">
                        <?php
                          if( $tentativeAdvance['confirm'] != 1) {
                            if( $tentativeAdvance['transaction_status'] == 'deposited' || $tentativeAdvance['transaction_status'] == 'transacted') {
                          ?>
                            <button type="button" class="btnTentativeAdvance" valuex="<?php echo $tentativeAdvance['tentative_advance_id'] ?>"> <?php echo 'Save'; ?></button>
                          <?php
                            }
                          }
                        ?>
                  </td>



                               
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


<!--/////////////////////////For Oc Order Payment Entry////////////////////-->
  
  <!--div class="divOcOrderPayment">
        <div class="well">
          <h3>Order payment Records</h3><br>
          <input type="hidden" id="tentative_advance_idA" class="tentative_advance_idA" name="tentative_advance_idA">
          <input type="hidden" id="msgadminA" class="msgadminA" name="msgadminA" >
          <input type="hidden" id="confirmA" class="confirmA" name="confirmA" >
          <input type="hidden" id="payment_idAA" class="payment_idAA" name="payment_idAA"> <!--only used in direct sales-->

          <!--table class="table table-bordered table-hover table_order_x">
            <thead>
              <tr>   
                <th></th>           
                <th>payment_id</th>
                <th>Order No</th>
                <th>Txn Ref.No.</th>
                <th>Txn date</th>
                <th>payment_gateway</th>
                <th>amount</th>
                <th>successfull</th>
                <th>bank_transfer_mode</th>
              </tr>
            </thead>
            <tbody id="mytbody">
            
            </tbody>
          </table> 
          <button type="button" id="btnVerify" class="btn-success btnVerify"> <?php echo 'Mark existing entry as Same and Verified'; ?></button>          
          <button type="button" id="btnContinue" class="btn-primary btnContinue"> <?php echo 'Continue Creating a Duplicate Entry'; ?></button>
          <button type="button" id="btnCancel" class="btn-danger btnCancel"> <?php echo 'Dont Do Anything !'; ?></button>
          <span class="spanx">X</span>
        </div>
  </div-->
<!--/////////////////////////////////////////////-->

<!--/////////////////////////For Oc Order Payment Entry////////////////////-->
  <div class="divOcOrderPayment">
        <div class="well">
          <h3>Previous Verification on this Order No.</h3><br>
          <table class="table table-bordered table-hover table_order_x ttbl">
            <thead>
              <tr>
                <td>Date</td>
                <td>Staff Member</td>
                <td>Order No.</td>            
                <td>Payment Mode</td>
                <td>Ref. No</td>
                <td>Collection Date</td>
                <td>Amount</td>
                <td>notes</td>
                <td>Image</td>
                <td>Branch</td>
                <td>Transaction Status</td>
                <td>Date Created</td>
              </tr>
            </thead>
            <tbody id="mytbody2">
            
            </tbody>
          </table>
        </div>

        <div class="well">
          <h3>Order payment Records</h3><br>
          <input type="hidden" class="tentative_advance_idA" name="tentative_advance_idA">
          <input type="hidden" class="msgadminA" name="msgadminA" >
          <input type="hidden" class="confirmA" name="confirmA" >
          <input type="hidden" class="payment_idAA" name="payment_idAA"> <!--only used in direct sales-->

          <table class="table table-bordered table-hover table_order_x">
            <thead>
              <tr>   
                <th></th>           
                <th>payment_id</th>
                <th>Order No</th>
                <th>Txn Ref.No.</th>
                <th>Txn date</th>
                <th>payment_gateway</th>
                <th>amount</th>
                <th>successfull</th>
                <th>bank_transfer_mode</th>
              </tr>
            </thead>
            <tbody id="mytbody">
            
            </tbody>
          </table> 
          <input type="text" class="form-control merchant_txn_id1" style="width:150px" placeholder="Enter Reference No." name="merchant_txn_id1" />
          <button type="button" id="btnVerify" class="btn-primary btnVerify"> <?php echo 'Mark existing entry as DUPLICATE and Verify it!'; ?></button>          
          <button type="button" id="btnContinue" class="btn-warning btnContinue"> <?php echo 'Continue Creating a NEW Entry'; ?></button>
          <button type="button" id="btnCancel" class="btn-danger btnCancel"> <?php echo 'Dont Do Anything!'; ?></button>
          <span class="spanx">X</span>
        </div>
  </div>

  <div class="divTentativeAdvanceByOrderID">
        <div class="well">
          <h3>Previous Verification on this Order No.</h3><br>
          <table class="table table-bordered table-hover table_order_x ttbl">
            <thead>
              <tr>
                <td>Date</td>
                <td>Staff Member</td>
                <td>Order No.</td>            
                <td>Payment Mode</td>
                <td>Ref. No</td>
                <td>Collection Date</td>
                <td>Amount</td>
                <td>notes</td>
                <td>Image</td>
                <td>Branch</td>
                <td>Transaction Status</td>
                <td>Date Created</td>
              </tr>
            </thead>
            <tbody id="mytbody3">
            
            </tbody>
          </table>
          <input type="text" class="form-control merchant_txn_id2" style="width:150px" placeholder="Enter Reference No." name="merchant_txn_id2" />
          <button type="button" id="btnSave22" class="btn-primary btnSave22"> <?php echo 'Continue'; ?></button>
          <button type="button" id="btnCancel22" class="btn-danger btnCancel22"> <?php echo 'Dont Do Anything!'; ?></button>
          <span class="spanx">X</span>
        </div>
  </div>

  <div class="divNoRecord">
        <div class="well">
          <h3>Tentative Advance</h3><br>

          <input type="text" class="form-control merchant_txn_id0" style="width:150px" placeholder="Enter Reference No." name="merchant_txn_id0" />
          <br>
          <button type="button" id="btnSave00" class="btn-primary btnSave00"> <?php echo 'Continue'; ?></button>
          <button type="button" id="btnCancel00" class="btn-danger btnCancel00"> <?php echo 'Dont Do Anything!'; ?></button>
          <span class="spanx">X</span>
        </div>
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

.divOcOrderPayment, .divTentativeAdvanceByOrderID, .divNoRecord {
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
  /*width: 645px;*/
  width: 814px;
  box-shadow: 1px 1px 2px 2px;
}
.divOcOrderPayment .spanx, .divTentativeAdvanceByOrderID .spanx, .divNoRecord .spanx{
    background: #000;
    border-radius: 17px;
    color: #fff;
    font-size: 18px;
    right: -11px;
    padding: 3px 7px;
    position: absolute;
    top: -12px
}
.table_order_x tr th, .table_order_x tr td{font-size: 10px;  width: inherit!important;}

button.btnVerify, button.btnContinue, button.btnCancel{margin-right: 48px;}

tr.trgreen {background-color: greenyellow;}
tr.trred {background-color: palevioletred;}

.divOcOrderPayment .ttbl tr td, .divTentativeAdvanceByOrderID .ttbl tr td {
    border: 2px solid #000!important;
}



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
  jQuery('.btnTentativeAdvance').click( function(){
    
    $(".btnTentativeAdvance").prop('disabled', 'disabled');
    
    var tentative_advance_id = $(this).attr('valuex');
    var msgadmin = $(this).closest('td').prev('td').prev('td').find('input#txtmsgadmin').val();
    var confirm = $(this).closest('td').prev('td').find('select.confirm').val();

    jQuery('.tentative_advance_idA').val(tentative_advance_id);
    jQuery('.msgadminA').val(msgadmin);
    jQuery('.confirmA').val(confirm);

    if (confirm == 1)
    {
      jQuery.ajax(
      {
        url: 'index.php?route=account_panel/tentativeadvance/getOcOrderPaymentRecord&token=<?php echo $token; ?>',
        type: "POST",
        data : {tentative_advance_id:tentative_advance_id, msgadmin:msgadmin,confirm:confirm},
        ContentType:"application/json",
        async:false,
        success: function(response){
    
        $("#mytbody").empty();
        $("#mytbody2").empty();
        $("#mytbody3").empty();
        response = $.parseJSON(response);

      //alert("hhhh");die;
        var data = response.getOcOrderPayment;
        var norecord = response.norecord ;
        
        var data2 = response.getTentativeAdvancesByOrderIDDetails;
        var norecord2 = response.norecord2 ;
        //var staff_id = data.staff_id;


        if (norecord == 0)
        {
          if (norecord2 == 0)
          {
            $('.divNoRecord').show();
          }
          else
          {
            //alert("Norecord 0 and Norecord 1");
            //alert("uuu2");die;
            var tbodyTest3 = "";

            $.each(data2, function(i, item) {
              
              //var imgg = "<a href='"+ item.bank_deposited_image +"' target='_blank' width='500' height='50'><i class='fa fa-paperclip'></i></a>";
              var imgg;
              //if (item.bank_deposited_image.length > 0)
              if (item.bank_deposited_image !== null)
              {
                imgg = "<a href='"+ item.bank_deposited_image +"' target='_blank' width='500' height='50'><i class='fa fa-paperclip'></i></a>";
              }
              else
              {
                imgg = '';
              }
              
              tbodyTest3 += "<tr>";

              tbodyTest3 += "<td>";
              tbodyTest3 += item.dated;
              tbodyTest3 += "</td>";

              tbodyTest3 += "<td>";
              tbodyTest3 += item.staff_name;
              tbodyTest3 += "</td>";

              tbodyTest3 += "<td>";
              tbodyTest3 += item.order_no;
              tbodyTest3 += "</td>";

              tbodyTest3 += "<td>";
              tbodyTest3 += item.payment_mode;
              tbodyTest3 += "</td>";

              tbodyTest3 += "<td>" ;
              //tbodyTest3 += 'item.refNo';
              tbodyTest3 += item.merchant_txn_id;
              tbodyTest3 += "</td>" ;

              tbodyTest3 += "<td>" ;
              tbodyTest3 += item.collection_date;
              tbodyTest3 += "</td>" ;

              tbodyTest3 += "<td>" ;
              tbodyTest3 += item.amount;
              tbodyTest3 += "</td>" ;

              tbodyTest3 += "<td>" ;
              tbodyTest3 += item.notes;
              tbodyTest3 += "</td>" ;
              
              tbodyTest3 += "<td>" ;
              //tbodyTest3 += item.bank_deposited_image;
              tbodyTest3 += imgg;
              tbodyTest3 += "</td>" ;

              tbodyTest3 += "<td>" ;
              tbodyTest3 += item.branch_name;
              tbodyTest3 += "</td>" ;
              
              tbodyTest3 += "<td>" ;
              tbodyTest3 += item.transaction_status;
              tbodyTest3 += "</td>" ;

              tbodyTest3 += "<td>" ;
              tbodyTest3 += item.date_created;
              tbodyTest3 += "</td>" ;

              tbodyTest3 += "</tr>" ;

              $("#mytbody3").empty();
              $("#mytbody3").append(tbodyTest3 );

              $(".btnTentativeAdvance").prop('disabled', 'disabled');
              //$('.divOcOrderPayment').show();
              $('.divTentativeAdvanceByOrderID').show();

             });
          }
        }
        else
        {
//
          var tbodyTest2 = "";

          $.each(data2, function(i, item) {

            //var imgg = "<a href='"+ item.bank_deposited_image +"' target='_blank' width='500' height='50'><i class='fa fa-paperclip'></i></a>";
            var imgg;
            //if (item.bank_deposited_image.length > 0)
            if (item.bank_deposited_image !== null)
            {
              imgg = "<a href='"+ item.bank_deposited_image +"' target='_blank' width='500' height='50'><i class='fa fa-paperclip'></i></a>";
            }
            else
            {
              imgg = '';
            }

            tbodyTest2 += "<tr>";

            tbodyTest2 += "<td>";
            tbodyTest2 += item.dated;
            tbodyTest2 += "</td>";

            tbodyTest2 += "<td>";
            tbodyTest2 += item.staff_name;
            tbodyTest2 += "</td>";

            tbodyTest2 += "<td>";
            tbodyTest2 += item.order_no;
            tbodyTest2 += "</td>";

            tbodyTest2 += "<td>";
            tbodyTest2 += item.payment_mode;
            tbodyTest2 += "</td>";

            tbodyTest2 += "<td>" ;
            //tbodyTest2 += 'item.refNo';
            tbodyTest2 += item.merchant_txn_id;
            tbodyTest2 += "</td>" ;

            tbodyTest2 += "<td>" ;
            tbodyTest2 += item.collection_date;
            tbodyTest2 += "</td>" ;

            tbodyTest2 += "<td>" ;
            tbodyTest2 += item.amount;
            tbodyTest2 += "</td>" ;

            tbodyTest2 += "<td>" ;
            tbodyTest2 += item.notes;
            tbodyTest2 += "</td>" ;
              
            tbodyTest2 += "<td>" ;
            //tbodyTest2 += item.bank_deposited_image;
            tbodyTest2 += imgg;
            tbodyTest2 += "</td>" ;              

            tbodyTest2 += "<td>" ;
            tbodyTest2 += item.branch_name;
            tbodyTest2 += "</td>" ;
              
            tbodyTest2 += "<td>" ;
            tbodyTest2 += item.transaction_status;
            tbodyTest2 += "</td>" ;

            tbodyTest2 += "<td>" ;
            tbodyTest2 += item.date_created;
            tbodyTest2 += "</td>" ;
          
            tbodyTest2 += "</tr>" ;

            $("#mytbody2").empty();
            $("#mytbody2").append(tbodyTest2 );

            //$(".btnTentativeAdvance").prop('disabled', 'disabled');
            //$('.divOcOrderPayment').show();
           
           });
//

          var radioPaymentID;
          var tbodyTest = "";
          $.each(data, function(i, item) {
        
            //radioPaymentID = "<input type='radio' name='radioPaymentID' checked='"+ checked +"' value='"+ item.payment_id +"' class='radioPaymentID'>";
            radioPaymentID = "<input type='radio' name='radioPaymentID' value='"+ item.payment_id +"' class='radioPaymentID'>";


            tbodyTest += "<tr>";
            tbodyTest += "<td>";
            tbodyTest += radioPaymentID;
            tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.payment_id;
            tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.order_no;
            tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.merchant_txn_id;
            tbodyTest += "</td>";

            tbodyTest += "<td>" ;
            tbodyTest += item.txn_date_time;
            tbodyTest += "</td>" ;

            tbodyTest += "<td>" ;
            tbodyTest += item.payment_gateway;
            tbodyTest += "</td>" ;

            tbodyTest += "<td>" ;
            tbodyTest += item.amount;
            tbodyTest += "</td>" ;

            tbodyTest += "<td>" ;
            tbodyTest += item.successfull;
            tbodyTest += "</td>" ;

            tbodyTest += "<td>" ;
            tbodyTest += item.bank_transfer_mode;
            tbodyTest += "</td>" ;
          
            tbodyTest += "</tr>" ;

            $("#mytbody").empty();
            $("#mytbody").append(tbodyTest );

            $(".btnTentativeAdvance").prop('disabled', 'disabled');
            $('.divOcOrderPayment').show();

            ///////radiobtn
            jQuery('.radioPaymentID').click(function(){   
              var value = $('input[name=radioPaymentID]:checked').val(); 
              jQuery('.payment_idAA').val(value);
            });
           
           });
        }


        
        },      
        error: function(jqXHR, textStatus, errorThrown)
        {
          alert( textStatus ); 
        }
      });
    }
    else
    {
      $('.ajaXloader').fadeIn(100);
      var merchant_txn_id = '0xx';

      jQuery.ajax(
      {
        url: 'index.php?route=account_panel/tentativeadvance/updateTentativeAdvanceConfirm&token=<?php echo $token; ?>',
        type: "POST",
        data : {tentative_advance_id:tentative_advance_id,msgadmin:msgadmin,confirm:confirm,merchant_txn_id:merchant_txn_id},
        ContentType:"application/json",
        async:true,
        success: function(response){

          var data = $.parseJSON(response);
          var status = data.status ;
          var msg;
          
          if( status == 1 ) {
            msg = "Try Again!";
            alert(msg);
          }

          if( status == 2 ) {
            msg = "Please Select Status";
            alert(msg);
            $('.ajaXloader').fadeOut(100);
          }        

          if( status == 3 ) {
            //alert(data.tentative_advance_id);die;
            //myFunction(data.tentative_advance_id, data.bank_transfer_mode, data.bank_name, data.bank_amount, data.order_id, data.order_no, data.payment_date, data.payment_reff_no, data.user_id, data.serialize_response, data.msgadmin );
          }  
          if( status == 333 ) {
            msg = "This is will be done by FSE, not by admin";
            alert(msg);
            $(".btnTentativeAdvance").prop('disabled', false);
            $('.ajaXloader').fadeOut(100);
          }  
          if( status == 51 ) {
            msg = "Entry has been saved Successfully";
            alert(msg);
            location.reload();
          }

        },      
        error: function(jqXHR, textStatus, errorThrown)
        {
          alert( textStatus ); 
        }
      });
      //$('.ajaXloader').fadeOut(100);
    }


  }); 
</script>
<script type="text/javascript">
  jQuery('.btnContinue').click( function(){

    $(".btnContinue").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var tentative_advance_id = $('.tentative_advance_idA').val();
    var msgadmin = $('.msgadminA').val();
    var confirm = $('.confirmA').val();

    var merchant_txn_id = $('.merchant_txn_id1').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/tentativeadvance/updateTentativeAdvanceConfirm&token=<?php echo $token; ?>',
      type: "POST",
      data : {tentative_advance_id:tentative_advance_id,msgadmin:msgadmin,confirm:confirm,merchant_txn_id:merchant_txn_id},
      ContentType:"application/json",
      async:true,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        if( status == 1 ) {
          msg = "Try Again!";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Please Select Verified";
          alert(msg);
          $(".btnContinue").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 3 ) {
          msg = "Please enter Reference No.";
          alert(msg);
          $(".btnContinue").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 51 ) {
          msg = "Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //$(".btnContinue").prop('disabled', false);
    //$('.ajaXloader').fadeOut(100);
  });  


  jQuery('.btnVerify').click( function(){

    $(".btnVerify").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var tentative_advance_id = $('.tentative_advance_idA').val();
    var msgadmin = $('.msgadminA').val();
    var confirm = $('.confirmA').val();

    var payment_id = $('.payment_idAA').val();

    var merchant_txn_id = $('.merchant_txn_id1').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/tentativeadvance/updateTentativeAdvanceConfirm2&token=<?php echo $token; ?>',
      type: "POST",
      data : {tentative_advance_id:tentative_advance_id,msgadmin:msgadmin,confirm:confirm,payment_id:payment_id,merchant_txn_id:merchant_txn_id},
      ContentType:"application/json",
      async:true,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        if( status == 1 ) {
          msg = "Try Again!";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Please Select Verified";
          alert(msg);
        }

        if( status == 3 ) {
          msg = "Please enter Reference No.";
          alert(msg);
          $(".btnVerify").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 11 ) {
          msg = "Please Select Radio Button of Payment ID";
          alert(msg);
          $(".btnVerify").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 51 ) {
          msg = "Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //$(".btnVerify").prop('disabled', false);
    //$('.ajaXloader').fadeOut(100);
  });  

</script>

<script type="text/javascript">
  jQuery('.btnSave22').click( function(){

    $(".btnSave22").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var tentative_advance_id = $('.tentative_advance_idA').val();
    var msgadmin = $('.msgadminA').val();
    var confirm = $('.confirmA').val();

    var merchant_txn_id = $('.merchant_txn_id2').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/tentativeadvance/updateTentativeAdvanceConfirm&token=<?php echo $token; ?>',
      type: "POST",
      data : {tentative_advance_id:tentative_advance_id,msgadmin:msgadmin,confirm:confirm,merchant_txn_id:merchant_txn_id},
      ContentType:"application/json",
      async:true,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        if( status == 1 ) {
          msg = "Try Again!";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Please Select Verified";
          alert(msg);
          $(".btnSave22").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 3 ) {
          msg = "Please enter Reference No.";
          alert(msg);
          $(".btnSave22").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 51 ) {
          msg = "Entry has been saved Successfully";
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
  jQuery('.btnSave00').click( function(){

    jQuery(".btnSave00").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var tentative_advance_id = jQuery('.tentative_advance_idA').val();
    var msgadmin = jQuery('.msgadminA').val();
    var confirm = jQuery('.confirmA').val();

    var merchant_txn_id = $('.merchant_txn_id0').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/tentativeadvance/updateTentativeAdvanceConfirm&token=<?php echo $token; ?>',
      type: "POST",
      data : {tentative_advance_id:tentative_advance_id,msgadmin:msgadmin,confirm:confirm,merchant_txn_id:merchant_txn_id},
      ContentType:"application/json",
      async:true,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        if( status == 1 ) {
          msg = "Try Again!";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Please Select Verified";
          alert(msg);
          jQuery(".btnSave00").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 3 ) {
          msg = "Please enter Reference No.";
          alert(msg);
          jQuery(".btnSave00").prop('disabled', false);
          $('.ajaXloader').fadeOut(100);
        }

        if( status == 51 ) {
          //jQuery('.ajaXloader').fadeOut(100);
          msg = "Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    //jQuery(".btnSave00").prop('disabled', false);
    //$('.ajaXloader').fadeOut(100);
  });  
</script>

<script type="text/javascript">
  jQuery('.btnCancel, .btnCancel22, .btnCancel00').click( function(){
      $('.tentative_advance_idA').val("");
      $('.msgadminA').val("");
      $('.confirmA').val("");
      $('.divOcOrderPayment').hide();
      $('.divTentativeAdvanceByOrderID').hide();
      $('.divNoRecord').hide();
      $(".btnTentativeAdvance").prop("disabled", false);

      $('.payment_idAA').val("");
      $("#mytbody").empty();
      $("#mytbody2").empty();
      $("#mytbody3").empty();
  });

  $('.spanx').click(function(){
      $('.tentative_advance_idA').val("");
      $('.msgadminA').val("");
      $('.confirmA').val("");
      $('.divOcOrderPayment').hide();
      $('.divTentativeAdvanceByOrderID').hide();
      $('.divNoRecord').hide();
      $(".btnTentativeAdvance").prop("disabled", false);

      $('.payment_idAA').val("");
      $("#mytbody").empty();
      $("#mytbody2").empty();
      $("#mytbody3").empty();
  }); 
</script>>
<script type="text/javascript"><!--
$('.button-filter').on('click', function() {

  var url = 'index.php?route=account_panel/tentativeadvance&token=<?php echo $token; ?>';

  var filter_order_no = $('input[name=\'filter_order_no\']').val();
  if (filter_order_no) {
    url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
  }  
  var filter_date_from = $('input[name=\'filter_date_from\']').val();
  if (filter_date_from) {
    url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
  }

  var filter_date_to = $('input[name=\'filter_date_to\']').val();
  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

  var filter_status = $('select[name=\'filter_status\']').val();
  if (filter_status) {
    url += '&filter_status=' + encodeURIComponent(filter_status);
  }  

  var filter_confirm = $('select[name=\'filter_confirm\']').val();
  if (filter_confirm) {
    url += '&filter_confirm=' + encodeURIComponent(filter_confirm);
  }

  var filter_order = $('select[name=\'filter_order\']').val();
  if (filter_order != '*') {
    url += '&filter_order=' + encodeURIComponent(filter_order);
  }

  var filter_page_limit = $('select[name=\'filter_page_limit\']').val();
  if (filter_page_limit != '*') {
    url += '&filter_page_limit=' + encodeURIComponent(filter_page_limit);
  }

  var filter_staff = $('select[name=\'filter_staff\']').val();
  if (filter_staff) {
    url += '&filter_staff=' + encodeURIComponent(filter_staff);
  }

  var filter_amount_from = $('input[name=\'filter_amount_from\']').val();
  if (filter_amount_from) {
    url += '&filter_amount_from=' + encodeURIComponent(filter_amount_from);
  }  

  var filter_amount_to = $('input[name=\'filter_amount_to\']').val();
  if (filter_amount_to) {
    url += '&filter_amount_to=' + encodeURIComponent(filter_amount_to);
  }
  var filter_ref = $('input[name=\'filter_ref\']').val();
  if (filter_ref) {
    url += '&filter_ref=' + encodeURIComponent(filter_ref);
  }  

  location = url;
  
});


</script>

