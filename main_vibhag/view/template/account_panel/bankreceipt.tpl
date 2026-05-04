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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Bank Receipt'//echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">
                    <div class="col-sm-4 login_seller">       
                      <div class="form-group">
                        <div><h3><?php echo 'Import Csv file'; ?></h3></div>
                        <form action="<?php //echo $csv_import;?>" class="addBankReceiptAjax" enctype="multipart/form-data" method="post">
                          <input type="file" name="fileToUpload" />
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
                        $filter_statusArry = array( '101' => 'All', '0'  => 'Pending', '1' => 'done');
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
                <label class="control-label" for="input-status"><?php echo 'Ledgers (Bank Dr.)'; ?></label>
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
                <label class="control-label" for="input-status">Ledgers (Inflow Cr.)</label>
                <select name="filter_inflow" class="form-control filter_inflow">
                      <option value="">All</option>
                      <?php
                        foreach( $ledgers as $value ) {
                        
                        
                        if( $value['ledger_id'] == $filter_inflow ) {
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
        
          <div class="table-responsive analysis">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>S.No</td>
                  <td>Date</td>
                  <td>Bank</td>
                  <td>Amount</td>            
                  <td>Mode</td>
                  <td>Reference</td>
                  <td>Inflow (Ledgers)</td>
                  <td>No. of Orders</td>
                  <td>Action</td>
                  <td>Confirm</td>
                </tr>
              </thead>
              <tbody>              
                <?php if ($receipts) { ?>
                <?php $i = 0; ?>
                <?php foreach ($receipts as $key => $receipt) { 
                    $i++;
                    $statusx = $receipt['confirm'] ;
                    
                    $status_text = "Not Confirmed";
                    if ($statusx == 1 ) {
                       $status_text = "<span class='statusx'>Confirmed</span>";
                       $status_text11 = "Confirmed";
                    }
                    if ($statusx == 0 ) {
                       $status_text = "<input type='checkbox' value='" . $receipt['receipt_id'] . "' name='statuschk' class='statuschk'>";
                       $status_text11 = "Not Confirm";
                    }

                    $trgreen = $receipt['ledger_id2'] ;
                    $css = "";
                    if ($trgreen != 0 ) {
                      $css = "trgreen";
                    }
                ?>
  
                <tr class="parent <?php echo $css; ?>" data-child="child_<?php echo $key; ?>" receipt_id="<?php echo $receipt['receipt_id']?>" >
                  <td class="text-left"><?php echo $i; ?></td>
                  <td class="text-left"><?php echo $newDate = date("d-m-Y", strtotime($receipt['dated'])); ?></td>                  

                  <td class="text-left"><?php echo $receipt['ledger_name']; ?></td>
                  <td class="text-left"><?php echo $receipt['amount']; ?></td>
                  <td class="text-left"><?php echo $receipt['mode']; ?></td>
                  <td class="text-left"><?php echo $receipt['reference']; ?></td>

                  <td class="text-left">
                    <?php if ($trgreen != $meeshoLedgerID && $trgreen != $clubfactoryLedgerID ) { ?>
                    <select name="ledger_id" id="ledger_row_<?php echo $receipt['receipt_id']?>" class="ledger_id" receipt_id="<?php echo $receipt['receipt_id']?>" amount="<?php echo $receipt['amount'] ?>" dated="<?php echo $receipt['dated'] ?>" ref="<?php echo $receipt['reference'] ?>" bank_name="<?php echo $receipt['ledger_name'] ?>">
                          <option value="">---- Select ----</option>
                          <?php
                          if(isset( $ledgers ) ) {
                            foreach($ledgers as $ledger)
                            {
                            ?>
                                <option group_id="<?php echo $ledger['group_id']?>" value="<?php echo $ledger['ledger_id'] ?>" 
                                        <?php echo $receipt['ledger_id2'] == $ledger['ledger_id'] ? 'selected' : ''; ?> > 
                                        <?php echo $ledger['ledger_name'] ?> 
                                </option>
                           <?php  
                            }
                          }
                          ?>
                    </select>
                    <?php } ?>
                    <input type="hidden" class="ledger_idHidden_row_<?php echo $receipt['receipt_id']?>" name="ledger_idHidden" value="<?php echo $receipt['ledger_id2'] ?>">

                  </td>      

                  <td class="text-left">
                        <?php echo $receipt['no_of_order']; ?>
                  </td>
                  <?php
                    if( ($statusx==1 && $user_id != 43) || $trgreen == $meeshoLedgerID || $trgreen == $clubfactoryLedgerID) { ?>
                      <td class="text-left"> </td>
                  <?php
                    }
                    else
                    {
                  ?>
                  <td class="text-left">
                    <form action="<?php //echo $receipt['csv_import2']; ?>" class="addBankReceiptSubAjax" method="post" enctype="multipart/form-data">
                        <input type="hidden" class="ledgeridAA" name="ledgeridAA" value="<?php echo $receipt['ledger_id2'] ?>">
                        <input type="hidden" class="groupidAA" name="groupidAA" value="<?php echo $receipt['group_id'] ?>">
                        <input type="hidden" class="receipt_id" name="receipt_id" value="<?php echo $receipt['receipt_id'] ?>"> 
                        <input type="hidden" class="amountAjax" name="amountAjax">
                        <input type="hidden" class="datedROAA" name="datedROAA" value="<?php echo $receipt['dated'] ?>">
                        <input type="hidden" class="refAA" name="refAA" value="<?php echo $receipt['reference'] ?>">
                        <input type="hidden" class="noOfOrdersAA" name="noOfOrdersAA">
                        <input type="file" name="fileToUpload2">
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
    <td class="text-left"><?php echo $status_text ?></td>
<?php
  } 
  else
  {
?>  
    <td class="text-left"><?php echo $status_text11 ?></td>
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
  <div class="divBuyersRefund">
        <div class="well">
          <h3>Buyer's Refund</h3><br>
          <input type="hidden" class="refAA" name="refAA" >
          <input type="hidden" class="bank_nameAA" name="bank_nameAA" >

          <input type="text" class="form-control order_noBRBC" style="width: 192px" placeholder="Enter Order No" name="order_noBRBC" />
          <input type="text" class="form-control order_noRefNoBankPymt" style="width: 100px" placeholder="Enter Reference" name="order_noRefNoBankPymt" />
          <br>
          <button type="button" class="btnBuyersRefund"> <?php echo 'Save'; ?></button>
          <button type="button" class="btnBuyersRefundCancel"> <?php echo 'cancel'; ?></button>
          <span class="spanx">X</span>
        </div>
  </div>  
  <!--/////////////////////////For Salary emp_code////////////////////-->
  <div class="divSalary">
        <div class="well">
          <h3>Salary</h3><br>
          <!--input type="hidden" id="refAA" class="refAA" name="refAA" >
          <input type="hidden" id="bank_nameAA" class="bank_nameAA" name="bank_nameAA" -->

          <input type="text" class="form-control emp_code" placeholder="Enter Employee Code" name="emp_code" />
          <br>
          <button type="button" id="btnEmpCode" class="btnEmpCode"> <?php echo 'Save'; ?></button>
          <button type="button" id="btnEmpCodeCancel" class="btnEmpCodeCancel"> <?php echo 'cancel'; ?></button>
          <span class="spanx">X</span>
        </div>
  </div>  
<!--/////////////////////////For Direct Sales////////////////////-->
  <div class="divDirectSales">
        <div class="well">
          <h3>Direct Sales</h3>

          <form class="addDirectSalesAjax" method="post" enctype="multipart/form-data">

            <input type="hidden" class="ledgeridDS" name="ledgeridDS"> <!--only used in direct sales-->
            <input type="hidden" class="groupidDS" name="groupidDS"> <!--only used in direct sales-->
            <input type="hidden" class="receipt_idDS" name="receipt_idDS"> <!--only used in direct sales-->
            <input type="hidden" class="amountAA" name="amountAA">
            <input type="hidden" class="bank_nameAA" name="bank_nameAA" >
            <input type="hidden" class="datedROAA" name="datedROAA"><!--Again it is used for DS round off trnfr in staff-->
            <input type="hidden" class="refAA" name="refAA" ><!--Only used to update merchantTxnID of OcOrderPayment-->

            <div class="FormRowx direct_sales_order_table">
            <?php $jj = 0;?>
              <div class="formBOxx">
                <div class="col-sm-4">
                  <div class="submit_order_x">
                    <input type="text" class="form-control order_no" name="order_no" />
                    <input type="hidden" class="payment_idAA" name="payment_idAA[]"> <!--only used in direct sales-->
                  </div>
                  <button type="button" form="filter_form" id="" width:"50px" class="submit_order"> <?php echo 'Click'; ?></button>
                </div>
                <div class="col-sm-8" style="padding: 0px;">
                  <table class="table table-bordered table-hover table_order_x">
                    <thead>
                      <tr>              
                        <th>  </th>
                        <th> Txn Date </th>
                        <th> Date Added </th>
                        <th> Amount </th>
                        <th> Order No </th>
                        <th> Txn ID  </th>
                      </tr>
                    </thead>
                    <!--tbody id="mytbody"-->
                    <tbody class="mytbody">
                    
                    </tbody>
                  </table> 
                </div>
                <span class="removeBtnICards"><i class="fa fa-remove"></i></span>
              </div>
              <div>
                <span class="AddOneMorex" onclick="clone_icard()"><i class="fa fa-plus-square"></i>Add New</span>
                <input type="file" class="upload-file" name="fileToUpload" />
              </div>
              <?php $jj++;?>
            </div>

            <div>
              <input type="checkbox" name="dsb" value="dsb"/>
              <label class="control-label">Move To Shadow (Direct Sales Balance)</label>
            </div>

            <input class="btn btn-primary" type="submit" name="submit" value="SaveSubmit" />
            <input class="btn btn-primary" name="click_staff_list" type="button" onclick="getStaffList()" value="Click to get Staff List" />

            <select name="staff" class="staff mytbodyStaff">
              <option value="0">Select Staff</option>
            </select>
            
          </form>

          <span class="spanx">X</span>
        </div>
  </div>

<!--/////////////////////////For COD Security Deposit ////////////////////-->
  <div class="divCodSecurityDeposit">
        <div class="well">
          <h3>COD Security Deposit</h3>

          <form class="addCodSecurityDepositAjax" method="post" enctype="multipart/form-data">

            <input type="hidden" class="ledgeridCOD" name="ledgeridCOD"> <!--only used in direct sales-->
            <input type="hidden" class="groupidCOD" name="groupidCOD"> <!--only used in direct sales-->
            <input type="hidden" class="receipt_idCOD" name="receipt_idCOD"> <!--only used in direct sales-->
            <input type="hidden" class="customer_idCOD" name="customer_idCOD"> <!--only used in direct sales-->

            <div class="FormRowxCOD cod_security_deposit_order_table" style="padding-bottom: 10px;">
            <?php $jj = 0;?>
              <div class="formBOxxCOD">
                <div class="col-sm-4">
                  <div class="submit_order_x">
                    <input type="text" class="form-control customer_id" name="customer_id" placeholder="Customer ID" />
                  </div>
                  <button type="button" form="filter_form" id="" width:"50px" class="get_customer"> Click </button>
                </div>
                <div class="col-sm-8" style="padding: 0px;">
                  <table class="table table-bordered table-hover table_order_x">
                    <thead>
                      <tr>              
                        <th> Customer ID </th>
                        <th> First Name </th>
                        <th> LasT Name </th>
                        <th> City </th>
                        <th> Company</th>
                        <th> Telephone </th>
                      </tr>
                    </thead>
                    <!--tbody id="mytbody"-->
                    <tbody class="mytbody">
                    
                    </tbody>
                  </table> 
                </div>
              </div>
              <?php $jj++;?>
            </div>
          <div id="CODSecurityButtonDiv">
            <input class="btn btn-primary save_cod_security_deposit" type="button" name="submit" value="Save COD Security Deposit" />
          </div>
          </form>

          <span class="spanx" style="cursor: pointer;">X</span>
        </div>
  </div>

<!--/////////////////////////For Oc Order Payment Entry////////////////////-->
  <div class="divQRCode">
        <div class="well">
          <h3>QR Code Entries</h3><br>
          
          <form class="addQRCodeAjax" method="post" enctype="multipart/form-data">

            <!--input type="hidden" class="payment_idQR" name="payment_idQR[]"--> <!--only used in direct sales and QR Code-->
            <input type="hidden" class="QRCodeDataArray" name="QRCodeDataArray" value=""> <!--only used in QR Code-->
            <div class="direct_sales_order_table">
              <table class="table table-bordered table-hover table_order_x">
                <thead>
                  <tr>   
                    <th></th>           
                    <th>Order No</th>
                    <th>Txn ID</th>
                    <th>Payment Gateway</th>
                    <th>Amount</th>
                    <th>Successfull</th>
                    <th>Reference</th>
                    <th>Payment Link</th>
                  </tr>
                </thead>
                <tbody class="mytbodyQRCode">
                
                </tbody>
              </table>
            </div>
            <!--button type="button" class="btn-primary btnQRCode">Save</button-->
            <input class="btn btn-primary" type="submit" name="submit" value="SaveSubmit" />
          
          </form>

          <span class="spanx">X</span>
        </div>
  </div>
  
  <!--/////////////////////////For Membership Fee Deposit ////////////////////-->
  <div class="divMembershipDeposit">
        <div class="well">
          <h3>Membership Fee Deposit</h3>

          <form class="addMembershipDepositAjax" method="post" enctype="multipart/form-data">

            <input type="hidden" class="ledgeridMembership" name="ledgeridMembership"> 
            <input type="hidden" class="groupidMembership" name="groupidMembership"> 
            <input type="hidden" class="receipt_idMembership" name="receipt_idMembership"> 
            <input type="hidden" class="customer_idMembership" name="customer_idMembership">
            
            <div class="membership_div membership_deposit_order_table" style="padding-bottom: 10px;">
              <label>Choose the membership</label>
              <div class="formBOxxM">
                <div class="col-sm-6" style="padding: 0px;">
                  <table class="table table-bordered table-hover table_order_x">
                    <thead>
                      <tr> 
                        <th></th>             
                        <th> Membership </th>
                        <th> Fee </th>
                      </tr>
                    </thead>
                    <!--tbody id="mytbody"-->
                    <tbody class="mytbody">
                    </tbody>
                  </table> 
                </div>
              </div>
            </div> 

            <div class="FormRowxM membership_deposit_order_table" style="padding-bottom: 10px;">
            <?php $jj = 0;?>
              <div class="formBOxxM">
                <div class="col-sm-4">
                  <div class="submit_order_x">
                    <input type="text" class="form-control customer_id" name="customer_id" placeholder="Customer ID" />
                  </div>
                  <button type="button" form="filter_form" id="" width:"50px" class="get_customer_by_id"> Click </button>
                </div>
                <div class="col-sm-8" style="padding: 0px;">
                  <table class="table table-bordered table-hover table_order_x">
                    <thead>
                      <tr>              
                        <th> Customer ID </th>
                        <th> First Name </th>
                        <th> Last Name </th>
                        <th> Telephone </th>
                      </tr>
                    </thead>
                    <!--tbody id="mytbody"-->
                    <tbody class="mytbody">
                    
                    </tbody>
                  </table> 
                </div>
              </div>
              <?php $jj++;?>
            </div>
          <div id="MembershipButtonDiv">
            <input class="btn btn-primary save_membership_deposit" type="button" name="submit" value="Save Membership Fee Deposit" />
          </div>
          </form>

          <span class="spanx" style="cursor: pointer;">X</span>
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
          <input type="hidden" class="payment_idBounce" name="payment_idBounce"><!--xxxxxxxxxxxxxxxxx-->
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
<!--/////////////////////////For Bounce Entry////////////////////-->
  <div class="divError">
        <div class="well">
          <h3>Message :-</h3><br>
          <table class="table table-bordered table-hover table_order_x ttbl">
            <thead>
              <tr>
                <td>Row No.</td>
                <td>Name</td>
                <td>Message.</td>
              </tr>
            </thead>
            <tbody class="mytbodyError">
            
            </tbody>
          </table>
          <button type="button" class="btnErrorCancel"> <?php echo 'cancel'; ?></button>
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
</style>
<style>
.show_product_row + .child{
    display: table-row!important;
}
.divCodSecurityDeposit, .divQRCode, .divBounce, .divBounce22, .divError{
  background: #fff;
  border: 1px solid #000;
  border-radius: 0px;
  display: none;
  right: 1%;
  margin: 15px;
  padding: 0px;
  position: fixed;
  /*top: 30%;*/
  top: 5%;
  z-index:2;
  width: 645px;
  box-shadow: 1px 1px 2px 2px;
  border-radius: 4px;
}
.divCodSecurityDeposit .well, .divQRCode .well, .divBounce .well, .divBounce22 .well, .divError .well{
  background: #fff;
  border: 0px;
  margin-bottom: 0px;
  padding: 8px;

}
.divCodSecurityDeposit .well h3{ font-size: 16px;}
.divCodSecurityDeposit .well .submit_order_x .form-control{height: 27px;  width: 100px!important;}
.divCodSecurityDeposit .well .submit_order_x {float: left; margin-right: 10px;}
.table_order_x tr th, .table_order_x tr td{font-size: 10px;  width: inherit!important;}
.direct_sales_order_table{max-height: 400px;overflow-x: hidden; overflow-y: scroll;}
.divCodSecurityDeposit .spanx, .divQRCode .spanx, .divBounce .spanx, .divBounce22 .spanx{
    background: #000;
    border-radius: 17px;
    color: #fff;
    font-size: 18px;
    right: -7px;
    padding: 3px 7px;
    position: absolute;
    top: -10px;
}
#CODSecurityButtonDiv{display: none; text-align: center;}

.divDirectSales, .divQRCode, .divBounce, .divBounce22, .divError{
  background: #fff;
  border: 1px solid #000;
  border-radius: 0px;
  display: none;
  right: 1%;
  margin: 15px;
  padding: 0px;
  position: fixed;
  /*top: 30%;*/
  top: 5%;
  z-index:2;
  width: 645px;
  box-shadow: 1px 1px 2px 2px;
  border-radius: 4px;
}
.divDirectSales .well, .divQRCode .well, .divBounce .well, .divBounce22 .well, .divError .well{
  background: #fff;
  border: 0px;
  margin-bottom: 0px;
  padding: 8px;

}
.divDirectSales .well h3{ font-size: 16px;}
.divDirectSales .well .submit_order_x .form-control{height: 27px;  width: 100px!important;}
.divDirectSales .well .submit_order_x {float: left; margin-right: 10px;}
.table_order_x tr th, .table_order_x tr td{font-size: 10px;  width: inherit!important;}
.direct_sales_order_table{max-height: 400px;overflow-x: hidden; overflow-y: scroll;}
.divDirectSales .spanx, .divQRCode .spanx, .divBounce .spanx, .divBounce22 .spanx{
    background: #000;
    border-radius: 17px;
    color: #fff;
    font-size: 18px;
    right: -7px;
    padding: 3px 7px;
    position: absolute;
    top: -10px;
}

.divMembershipDeposit, .divQRCode, .divBounce, .divBounce22, .divError{
  background: #fff;
  border: 1px solid #000;
  border-radius: 0px;
  display: none;
  right: 1%;
  margin: 15px;
  padding: 0px;
  position: fixed;
  /*top: 30%;*/
  top: 5%;
  z-index:2;
  width: 645px;
  box-shadow: 1px 1px 2px 2px;
  border-radius: 4px;
}
.divMembershipDeposit .well, .divQRCode .well, .divBounce .well, .divBounce22 .well, .divError .well{
  background: #fff;
  border: 0px;
  margin-bottom: 0px;
  padding: 8px;

}
.divMembershipDeposit .well h3{ font-size: 16px;}
.divMembershipDeposit .well .submit_order_x .form-control{height: 27px;  width: 100px!important;}
.divMembershipDeposit .well .submit_order_x {float: left; margin-right: 10px;}
.membership_deposit_order_table{max-height: 400px;overflow-x: hidden; overflow-y: scroll;}
.divMembershipDeposit .spanx, .divQRCode .spanx, .divBounce .spanx, .divBounce22 .spanx{
    background: #000;
    border-radius: 17px;
    color: #fff;
    font-size: 18px;
    right: -7px;
    padding: 3px 7px;
    position: absolute;
    top: -10px;
}
#MembershipButtonDiv{display: none; text-align: center;}
.FormRowxM {
    width: 100%;
    float: left;
    position: relative;
    padding-bottom: 4.5em;
}
.formBOxxM {
    width: 95%;
    position: relative;
    border: 1px solid #ddd;
    float: left;
    width: 100%!important;
    padding: 1em;
}

.divBuyersRefund, .divSalary{
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
.divBuyersRefund .spanx, .divSalary .spanx{
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

.divBuyersRefundx {
  background: #fff none repeat scroll 0 0;
  border: 1px groove #aaa;
  border-radius: 5px;
  display: none;
  right: 1%;
  margin: 15px;
  padding: 15px;
  position: fixed;
  top: 30%;
  width: 645px;
}
.divBuyersRefundx .spanxx{
    background: #000 none repeat scroll 0 0;
    border-radius: 17px;
    color: #fff;
    font-size: 18px;
    left: 533px;
    padding: 3px 7px;
    position: relative;
    top: -167px;
}
tr.trgreen {background-color: greenyellow;}


.FormRowx {
    width: 100%;
    float: left;
    position: relative;
    padding-bottom: 4.5em;
}
.formBOxx {
    width: 95%;
    position: relative;
    border: 1px solid #ddd;
    float: left;
    width: 100%!important;
    padding: 1em;
}
.formBOxx:first-child .removeBtnICards {
    display: none !important;
}
.removeBtn, .removeBtnICards {
    background: red none repeat scroll 0 0;
    border-radius: 100px;
    color: #ffffff;
    cursor: cell;
    float: left;
    font-size: 13px;
    height: 25px;
    line-height: 24px;
    margin-left: -46px;
    margin-top: 13px;
    opacity: 0.9;
    padding: 0;
    position: absolute;
    right: 9px;
    text-align: center;
    top: 0;
    transition: all 0.3s ease-in-out 0s;
    width: 25px;
    z-index: 9999999;
}
.AddOneMore, .AddOneMorex {
    background: #7dc144 none repeat scroll 0 0;
    border-radius: 6px;
    bottom: 15px;
    color: #ffffff;
    cursor: cell;
    float: left;
    font-size: 16px;
    height: 37px;
    line-height: 37px;
    margin-left: -46px;
    margin-top: 13px;
    opacity: 0.9;
    padding: 0 12px;
    position: absolute;
    right: 23px;
    text-align: center;
    transition: all 0.3s ease-in-out 0s;
    width: 124px;
    z-index: 9999999;
}

.AddOneMorex{
    float: left;
    margin-top: 10px;
    position: initial !important;
    margin-left: 0px !important;
  }

.upload-file{
    margin-left: -5px;
    margin-top: 13px;
    opacity: 0.9;
    padding: 14px 12px;
   
}

.AddOneMorex {
    right: auto!important;
    left: 3.5em;
    padding: 5px!important;
    height: auto!important;
    width: auto!important;
    line-height: 22px!important;
    font-size: 14px!important;
}
</style>

<script type="text/javascript">
$('.upload-file').click(function(){
  $('.AddOneMorex').hide();
  $('.formBOxx').hide();
});
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
jQuery('.statuschk').click(function(){
    
  var valuex = jQuery(this);
  var row_id = jQuery(this).val();

  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankreceipt/editReceiptForConfirm&token=<?php echo $token; ?>',
    type: "POST",
    data : {row_id:row_id},
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
        jQuery( valuex ).after( "<span class='statusx'>Confirmed</span>");
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
    $(".addBankReceiptSubAjax").on('submit',(function(e) {

    e.preventDefault();
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxBankReceiptSub&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
      success: function(response){
        var data = $.parseJSON(response);
        var msgs = data.msgs ;
        jQuery('.ajaXloader').fadeOut(100);

        if ($.isNumeric(msgs) == false)
        {
          var tbodyTest = "";

          $.each(msgs, function(i, item) {

            tbodyTest += "<tr>";

            tbodyTest += "<td>";
            tbodyTest += item.row;
            tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.name;
            tbodyTest += "</td>";

            tbodyTest += "<td>";
            tbodyTest += item.msg;
            tbodyTest += "</td>";
            
            tbodyTest += "</tr>" ;
            $(".mytbodyError").empty();
            $(".mytbodyError").append(tbodyTest );

            $('.divError').show();
           
           });
        }
        else
        {
          if (msgs == 256)
          {
            $('.divQRCode').show();
            $('.divDirectSales').hide();
            $('.divBuyersRefund').hide();
            $('.divSalary').hide();
            $(".ledger_id").prop('disabled', 'disabled');
            $(".statuschk").prop('disabled', 'disabled');
            var dataQRCode = data.pmntIDQRDArray;
            var dataQRCodeArray = data.QRCodeDataArray;
            myFunction(dataQRCode, dataQRCodeArray);
          }
          else if (msgs == 51)
          {
            alert("COD CSV has been saved Successfully");
            location.reload();
          }
          else if (msgs == 52)
          {
            alert("Payment Gateway CSV has been saved Successfully");
            location.reload();
          }
          else if (msgs == 53)
          {
            alert("Credit Agency CSV has been saved Successfully");
            location.reload();
          }
          else if (msgs == 54)
          {
            alert("Entry has been saved Successfully");
            location.reload();
          }
          else if (msgs == 99)
          {
            alert("Error! Try Again!");
            location.reload();
          }
          else if (msgs == 200)
          {
            alert("Successfully Done!");
            location.reload();
          }
        }
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
  }));

    $(".addBankReceiptSubAjax2222").on('submit',(function(e) {

    e.preventDefault();
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxBankReceiptSub&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
      success: function(response){
        var data = $.parseJSON(response);
        var status = data.status ;
        var row = data.row ;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Invalid COD CSV! Mistake in columns";
          alert(msg);
        }
        if( status == 2 ) {
          msg = "Please Enter proper date in COD CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 3 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 333 ) {
          msg = "Error in Order No(Order No not found in oc_order table), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 4 ) {
          msg = "Please Enter Ref.No. in COD CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 5 ) {
          msg = "Error in Reference No(Customer Name not found in oc_suborder oc_order table), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 6 ) {
          msg = "Please Enter Amount in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 7 ) {
          msg = "Please Enter Positive Amount Value in COD CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 8 ) {
          msg = "Total Amount Not Matched in COD CSV, Diff  of Rs. = "+ row ;
          alert(msg);
        }
        
        if( status == 21 ) {
          msg = "Invalid Payment Gateway CSV! Mistake in columns ";
          alert(msg);
        }
        if( status == 22 ) {
          msg = "Please Enter proper date in Payment Gateway CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 23 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 24 ) {
          msg = "Please Enter Ref.No. in Payment Gateway CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 25 ) {
          msg = "Error in Order No. and Reference No.(Customer Name not found in oc_order_payment table), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 255 ) {
          msg = "Error in Order No(Customer Name not found in oc_order table), Row No. = "+ row ;
          alert(msg);
        }

        //for QR Code Paytm
        if( status == 256 ) {
          $('.divQRCode').show();
          $('.divDirectSales').hide();
          $('.divBuyersRefund').hide();
          $('.divSalary').hide();
          $(".ledger_id").prop('disabled', 'disabled');
          $(".statuschk").prop('disabled', 'disabled');
          var dataQRCode = data.pmntIDQRDArray;
          var dataQRCodeArray = data.QRCodeDataArray;
          myFunction(dataQRCode, dataQRCodeArray);
        }


        if( status == 26 ) {
          msg = "Please Enter Amount in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 266 ) {
          msg = "Please Enter Amount not equals to zero, Row No. = "+ row ;
          alert(msg);
        }        
        if( status == 27 ) {
          msg = "Please Enter Positive Amount Value in Payment Gateway CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 28 ) {
          msg = "Please Enter Charges in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 29 ) {
          msg = "Please Enter Positive Charges Value in Payment Gateway CSV, Row No. = "+ row ;
          alert(msg);
        }     
        if( status == 30 ) {
          msg = "Total Amount Not Matched in Payment Gateway CSV, Diff  of Rs. = "+ row ;
          alert(msg);
        }
        if( status == 300 ) {
          msg = "Please fill value in Charges, Row No. = "+ row ;
          alert(msg);
        }

        ////Credit Agency
        if( status == 41 ) {
          msg = "Invalid Credit Agency CSV! Mistake in columns";
          alert(msg);
        }
        if( status == 42 ) {
          msg = "Please Enter proper date in Credit Agency CSV, Row No. = "+ row ;
          alert(msg);
        }
        
        if( status == 43 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        // if( status == 333 ) {
        //   msg = "Error in Order No(Order No not found in oc_order table), Row No. = "+ row ;
        //   alert(msg);
        // }
        // if( status == 44 ) {
        //   msg = "Please Enter Ref.No. in COD CSV, Row No. = "+ row ;
        //   alert(msg);
        // }
        // if( status == 45 ) {
        //   msg = "Error in Reference No(Customer Name not found in oc_suborder oc_order table), Row No. = "+ row ;
        //   alert(msg);
        // }
        if( status == 46 ) {
          msg = "Please Enter Amount in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 47 ) {
          msg = "Please Enter Positive Amount Value in Credit Agency CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 48 ) {
          msg = "Please Enter Charges in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 49 ) {
          msg = "Please Enter Positive Charges Value in Credit Agency CSV2, Row No. = "+ row ;
          alert(msg);
        }     
        if( status == 50 ) {
          msg = "Total Amount Not Matched in Credit Agency CSV, Diff  of Rs. = "+ row ;
          alert(msg);
        }



        if( status == 666 ) {
          msg = "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team";
          alert(msg);
          location.reload();
        }
        
        if( status == 31 ) {
          msg = "Please insert COD CSV";
          alert(msg);
        } 
        if( status == 32 ) {
          msg = "Please insert Payment gateway CSV";
          alert(msg);
        }                      

        if( status == 51 ) {
          msg = "COD CSV has been saved Successfully";
          alert(msg);
          location.reload();
        }
        if( status == 52 ) {
          msg = "Payment Gateway CSV has been saved Successfully";
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
          //window.location='index.php?route=account_panel/bankreceipt&token=<?php //echo $token; ?>';
          location.reload();
        }
        else if (msgs == 200)
        {
          alert("Successfully Done!");
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
  }));
  
</script>
<script type="text/javascript">
  function myFunction(dataQRCode, dataQRCodeArray) {

    $('.QRCodeDataArray').val(dataQRCodeArray);

    //var radioOrderID;
    var radioPaymentID;
    var radioPaymentIDName;
    var radioPaymentIDClass;
    
    var paymentIDQR;

    var tbodyTest = "";

    $.each(dataQRCode, function(i, item) {

        radioPaymentIDName = "radioPaymentID" + (i+1);
        radioPaymentIDClass = "radioPaymentID" + (i+1);
        radioPaymentIDClass += " radioPaymentID";
        
        trClass = "trClass" + (i+1);
        payment_idQRClass = "payment_idQR" + (i+1);

        paymentIDQR = "<input type='hidden' class='"+ payment_idQRClass +"' name='payment_idQR[]'>";

        tbodyTest += "<tr class='"+ trClass +"'>";
        tbodyTest += "<td>" ;
        tbodyTest += i+1;
        tbodyTest += "</td>" ;
          
        tbodyTest += "<td>";
        tbodyTest += paymentIDQR;
        tbodyTest += "</td>";

        tbodyTest += "</tr>" ;

        $.each(item, function (iind, obj) {
          radioPaymentID = "<input type='radio' name='"+ radioPaymentIDName +"' data-attrqr='"+ (i+1) +"' value='"+ obj.payment_id +"' class='"+ radioPaymentIDClass +"'>";


          tbodyTest += "<tr>";

          tbodyTest += "<td>";
          tbodyTest += radioPaymentID;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.order_no;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.merchant_txn_id;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.payment_gateway;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.amount;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.successfull;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.reference;
          tbodyTest += "</td>";

          tbodyTest += "<td>";
          tbodyTest += obj.payment_link;
          tbodyTest += "</td>";

          tbodyTest += "</tr>";

          $(".mytbodyQRCode").empty();
          $(".mytbodyQRCode").append(tbodyTest );
        });
    });


    jQuery('.radioPaymentID').click(function(){  
      var value = $(this).val();

      var data_i = $(this).data('attrqr');
      jQuery('.payment_idQR'+data_i).val(value);
    });
  }

</script>
<script>


    jQuery(".addQRCodeAjax").on('submit',(function(e) {

    e.preventDefault();

    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxQRCode&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      success: function(response){
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var msg;

        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 52 ) {
          msg = "Payment Gateway CSV has been saved Successfully";
          alert(msg);
          location.reload();
        }

        if( status == 91 ) {
          msg = "Please select QR Code Entries";
          alert(msg);
        }

        if( status == 92 ) {
          msg = "Error in Payment ID, possibly changed in HTML inspect element";
          alert(msg);
        }

        if( status == 93 ) {
          msg = "Error in counting QR Code entries, possibly changed array name in HTML inspect element";
          alert(msg);
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
  }));
  
</script>
<script>
    $(".addBankReceiptAjax").on('submit',(function(e) {
    e.preventDefault();
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/importcsv&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
      success: function(response){
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 2 ) {
          msg = "Please Enter proper date in Bank Receipt CSV, Row No. = "+ row ;
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
          msg = "Please Enter Mode of Receipt in CSV";
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
          msg = "Please Enter Perfect CSV of Bank Receipt";
          alert(msg);
        }

        if( status == 22 ) {
          msg = "Please insert Bank Receipt CSV";
          alert(msg);
        }             
        if( status == 53 ) {
        
          msg = "Bank Receipt CSV has been entered Successfully";
          alert(msg);
          window.location='index.php?route=account_panel/bankreceipt&token=<?php echo $token; ?>';
        }
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
  }));
  
</script>
<!--/////////////////////////////////////////////-->

<script>
jQuery(document).ready(function(){
      jQuery('.ledger_id').change(function() {

      var ledgerid = $(this).val(); //for hidden input type
      var group_id = $('option:selected', this).attr('group_id');
      var receipt_id = $(this).attr('receipt_id');
      var amount = $(this).attr('amount');
      var dated = $(this).attr('dated');
      var ref = $(this).attr('ref');
      var bank_name = $(this).attr('bank_name');

      var ledgeridDS = $(this).val(); //for hidden input type
      var group_idDS = $('option:selected', this).attr('group_id');
      var receipt_idDS = $(this).attr('receipt_id');      

      //jQuery('.ledgeridAA').val(ledgerid);
      jQuery('.ledger_idHidden_row_'+receipt_id).val(ledgerid);
      jQuery('.ledgeridAA').val($('.ledger_idHidden_row_'+receipt_id).val());
      jQuery('.groupidAA').val(group_id);
      jQuery('.receipt_id').val(receipt_id);
      jQuery('.amountAjax').val(amount);
      jQuery('.amountAA').val(amount);
      jQuery('.datedROAA').val(dated);
      jQuery('.refAA').val(ref);
      jQuery('.bank_nameAA').val(bank_name);
      
      jQuery('.ledgeridMembership').val($('.ledger_idHidden_row_'+receipt_id).val());
      jQuery('.groupidMembership').val(group_id);
      jQuery('.receipt_idMembership').val(receipt_id);

      jQuery('.ledgeridDS').val($('.ledger_idHidden_row_'+receipt_id).val());
      jQuery('.groupidDS').val(group_id);
      jQuery('.receipt_idDS').val(receipt_id);

      jQuery('.ledgeridCOD').val($('.ledger_idHidden_row_'+receipt_id).val());
      jQuery('.groupidCOD').val(group_id);
      jQuery('.receipt_idCOD').val(receipt_id);

      var directSalesLedgerId = 10;
      var buyersRefundLedgerId = 11;
      var salaryLedgerId = 14;
      var membershipLedgerId = '<?php echo !empty($membership_ledger_id)?$membership_ledger_id:0?>'; // for local(8343), staging(9552), for live()

      //var bounceLedgerId = 372;//local ledger id
      var bounceLedgerId = 3936;//live ledger id

      var codSecurityDeposit = '<?php echo !empty($cod_security_deposit_ledger_id)?$cod_security_deposit_ledger_id:0?>'; // for local(8343), staging(9552), for live()


      if (ledgerid == codSecurityDeposit)
      { 
        $('.divCodSecurityDeposit').show();
        $('.divDirectSales').hide();
        $('.divBuyersRefund').hide();
        $('.divSalary').hide();
        $('.divBounce').hide();
        $(".ledger_id").prop('disabled', 'disabled');
        $(".statuschk").prop('disabled', 'disabled');
      }
      else if (ledgerid == directSalesLedgerId)
      {
        $('.AddOneMorex').show();
        $('.formBOxx').show();
        $('.divDirectSales').show();
        $('.divBuyersRefund').hide();
        $('.divSalary').hide();
        $('.divBounce').hide();
        $(".ledger_id").prop('disabled', 'disabled');
        $(".statuschk").prop('disabled', 'disabled');
      } 
      else if (ledgerid == membershipLedgerId)
      {
        getMembershipLevels(amount);
        $('.divMembershipDeposit').show();
        $('.divDirectSales').hide();
        $('.divBuyersRefund').hide();
        $('.divSalary').hide();
        $('.divBounce').hide();
        $(".ledger_id").prop('disabled', 'disabled');
        $(".statuschk").prop('disabled', 'disabled');
      } 
      else if (ledgerid == buyersRefundLedgerId)
      {
        $('.divBuyersRefund').show();
        $('.divDirectSales').hide();
        $('.divSalary').hide();
        $('.divBounce').hide();
        $(".ledger_id").prop('disabled', 'disabled');
        $(".statuschk").prop('disabled', 'disabled');
      }
      else if (ledgerid == salaryLedgerId)
      {
        $('.divSalary').show();
        $('.divBuyersRefund').hide();
        $('.divDirectSales').hide();
        $('.divBounce').hide();
        $(".ledger_id").prop('disabled', 'disabled');
        $(".statuschk").prop('disabled', 'disabled');
      }
      if (ledgerid == bounceLedgerId)
      {
        myFunctionBounce(receipt_id);
      }
   });
});
</script>
<script type="text/javascript">
  //function myFunctionBounce(amount, ledgerid) {
  function myFunctionBounce(receipt_id) {

    var tbodyTest = "";

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/getPaymentByAmountLedgerID&token=<?php echo $token; ?>',
      type: "POST",
      data : {receipt_id:receipt_id},
      ContentType:"application/json",
      async:false,
      success: function(response){
        $("#mytbodyBounce").empty();
        response = $.parseJSON(response);
        var data = response.getPaymentByAmountLedgerID ;
        var norecord = response.norecord ;
        if (norecord == 0) {
            //alert("No Record found");
            $('.divBounce22').show();
            $('.divBounce').hide();
            $('.divDirectSales').hide();
            $('.divBuyersRefund').hide();
            $('.divSalary').hide();
            $(".ledger_id").prop('disabled', 'disabled');
            $(".statuschk").prop('disabled', 'disabled');
            $("form.addBankReceiptSubAjax .btn").prop('disabled', 'disabled');
        }
        else
        {
          var radioPaymentIDBounce;
          var tbodyTest = "";

          $.each(data, function(i, item) {
            
            radioPaymentIDBounce = "<input type='radio' name='radioPaymentIDBounce' value='"+ item.payment_id +"' class='radioPaymentIDBounce'>";

            tbodyTest += "<tr>";
            
            tbodyTest += "<td>";
            tbodyTest += radioPaymentIDBounce;
            tbodyTest += "</td>";

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
          $('.divDirectSales').hide();
          $('.divBuyersRefund').hide();
          $('.divSalary').hide();
          $(".ledger_id").prop('disabled', 'disabled');
          $(".statuschk").prop('disabled', 'disabled');
          $("form.addBankReceiptSubAjax .btn").prop('disabled', 'disabled');
        }
      
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });

    ///////radiobtn
    jQuery('.radioPaymentIDBounce').click(function(){  
      var value = $(this).val();
      jQuery('.payment_idBounce').val(value);
    });
  }
</script>
<script type="text/javascript">
  jQuery('.btnBounce').click( function(){//xxxxxxxxxxxxxxxxxx

    $(".btnBounce").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var payment_idBounce = $('.payment_idBounce').val();
    var receipt_id = $('.receipt_id').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxBounce&token=<?php echo $token; ?>',
      type: "POST",
      data : {payment_id:payment_idBounce,receipt_id:receipt_id},
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

        if( status == 666 ) {
          msg = "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team";
          alert(msg);
          location.reload();
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
  });
</script>
<script type="text/javascript">
  jQuery('.btnBounce2').click( function(){//xxxxxxxxxxxxxxxxxx

    $(".btnBounce2").prop('disabled', 'disabled');
    $('.ajaXloader').fadeIn(100);

    var ledgerid = $('.ledgeridAA').val();
    var groupid = $('.groupidAA').val();
    var receipt_id = $('.receipt_id').val();
    var amountOfReceipt = $('.amountAA').val();
    var datedROAA = $('.datedROAA').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxBounce2&token=<?php echo $token; ?>',
      type: "POST",
      data : {ledgerid:ledgerid,groupid:groupid,receipt_id:receipt_id,amountOfReceipt:amountOfReceipt,datedROAA:datedROAA},
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

        if( status == 666 ) {
          msg = "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team";
          alert(msg);
          location.reload();
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
  });
</script>
<script>
  jQuery('.btnDirectSalesCancel').click( function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.receipt_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");

      $('.ledgeridDS').val("");
      $('.groupidDS').val("");
      $('.receipt_idDS').val("");

      $('.payment_idAA').val("");
      $('.order_no').val("");
      //$("#mytbody").empty();
      $(".mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $(".statuschk").prop("disabled", false);
      $('.divDirectSales').hide();

  });

  jQuery('.btnBuyersRefundCancel').click( function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.receipt_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");

      $('.ledgeridDS').val("");
      $('.groupidDS').val("");
      $('.receipt_idDS').val("");
      
      $('.payment_idAA').val("");
      $('.order_no').val("");
      //$("#mytbody").empty();
      $(".mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $('.divBuyersRefund').hide();

      $(".mytbodyError").empty();
      $('.divError').hide();
  });

  jQuery('.btnEmpCodeCancel').click( function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.receipt_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");

      $('.ledgeridDS').val("");
      $('.groupidDS').val("");
      $('.receipt_idDS').val("");
      
      $('.payment_idAA').val("");
      $('.order_no').val("");
      //$("#mytbody").empty();
      $(".mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $('.divSalary').hide();

      $(".mytbodyError").empty();
      $('.divError').hide();
  });  

  jQuery('.btnBounceCancel').click( function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.receipt_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");

      $('.ledgeridDS').val("");
      $('.groupidDS').val("");
      $('.receipt_idDS').val("");
      
      $('.payment_idAA').val("");
      $('.order_no').val("");
      //$("#mytbody").empty();
      $(".mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $(".mytbodyBounce").empty();
      $('.divBounce').hide();
      $("form.addBankReceiptSubAjax .btn").prop('disabled', false);
      $('.divBounce22').hide();

      $(".mytbodyError").empty();
      $('.divError').hide();
  });

  jQuery('.btnErrorCancel').click( function(){
      $(".mytbody").empty();
      $(".ledger_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $(".mytbodyBounce").empty();
      $('.divBounce').hide();
      $("form.addBankReceiptSubAjax .btn").prop('disabled', false);
      $('.divBounce22').hide();

      $(".mytbodyError").empty();
      $('.divError').hide();
  });

  $('.spanx').click(function(){
      $('.ledgeridAA').val("");
      $('.groupidAA').val("");
      $('.receipt_id').val("");
      $('.amountAjax').val("");
      $('.amountAA').val("");
      $('.datedROAA').val("");
      $('.refAA').val("");
      $('.bank_nameAA').val("");

      $('.ledgeridDS').val("");
      $('.groupidDS').val("");
      $('.receipt_idDS').val("");
      
      $('.payment_idAA').val("");
      //$("#mytbody").empty();
      $(".mytbody").empty();
      $('.order_no').val("");
      $('.customer_id').val("");
      $(".ledger_id").prop("disabled", false);
      $("input[type='checkbox']").prop("disabled", false);
      $('.divCodSecurityDeposit').hide();
      $('#CODSecurityButtonDiv').hide();
      
      $('.divDirectSales').hide();
      $('.divBuyersRefund').hide();
      $('.divSalary').hide();
      
      $('.divMembershipDeposit').hide();
      $('#MembershipButtonDiv').hide();

      $(".mytbodyQRCode").empty();
      $('.divQRCode').hide();

      $(".mytbodyBounce").empty();
      $('.divBounce').hide();
      $("form.addBankReceiptSubAjax .btn").prop('disabled', false);
      $('.divBounce22').hide();

      $(".mytbodyStaff").empty();
      
      $('.FormRowx').find('.formBOxx.cloneIdx').remove();

      $(".mytbodyError").empty();
      $('.divError').hide();

  });  

  jQuery('.btnBuyersRefund').click( function(){

    var order_no = $('.order_noBRBC').val();
    var RefNoBankPymt = $('.order_noRefNoBankPymt').val();

      var ledgerid = $('.ledgeridAA').val();
      var groupid = $('.groupidAA').val();
      // var payment_id = $('.payment_id').val();
      // var amountOfPayment = $('.amountAA').val();
      var receipt_id = $('.receipt_id').val();
      var amountOfReceipt = $('.amountAA').val();
      var datedROAA = $('.datedROAA').val();
      var ref = $('.refAA').val();
      var bank_name = $('.bank_nameAA').val();

      jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxBuyersRefund&token=<?php echo $token; ?>',
      type: "POST",
      data : {order_no:order_no,RefNoBankPymt:RefNoBankPymt,ledgerid:ledgerid,groupid:groupid,receipt_id:receipt_id,amountOfReceipt:amountOfReceipt,datedROAA:datedROAA,ref:ref,bank_name:bank_name},
      ContentType:"application/json",
      //async:false,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;

        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Order No. not found in oc_order_payment";
          alert(msg);
        }

        if( status == 2 ) {
          msg = "Entry already exists";
          alert(msg);
        }        

        if( status == 11 ) {
          msg = "Please enter Order No.";
          alert(msg);
        }

        if( status == 12 ) {
          msg = "Please enter Ref No. of Bank Payment";
          alert(msg);
        }

        if( status == 666 ) {
          msg = "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team";
          alert(msg);
          location.reload();
        }

        if( status == 51 ) {
          msg = "Buyer's Refund Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
  });

  jQuery('.btnEmpCode').click( function(){

    var emp_code = $('.emp_code').val();

      var ledgerid = $('.ledgeridAA').val();
      var groupid = $('.groupidAA').val();
      // var payment_id = $('.payment_id').val();
      // var amountOfPayment = $('.amountAA').val();
      var receipt_id = $('.receipt_id').val();
      var amountOfReceipt = $('.amountAA').val();
      var datedROAA = $('.datedROAA').val();
      var ref = $('.refAA').val();
      var bank_name = $('.bank_nameAA').val();

      jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxSalary&token=<?php echo $token; ?>',
      type: "POST",
      data : {emp_code:emp_code,ledgerid:ledgerid,groupid:groupid,receipt_id:receipt_id,amountOfReceipt:amountOfReceipt,datedROAA:datedROAA,ref:ref,bank_name:bank_name},
      ContentType:"application/json",
      //async:false,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;

        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Employee Code not found in oc_ledger";
          alert(msg);
        }

        if( status == 666 ) {
          msg = "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team";
          alert(msg);
          location.reload();
        }

        if( status == 51 ) {
          msg = "Salary Entry has been saved Successfully";
          alert(msg);
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



<script type="text/javascript">
var jj = <?php echo $jj; ?>;
  function clone_icard()
  {
    var cln = $('.FormRowx').find('.formBOxx ').not('.cloneIdx').clone();
    cln.addClass('cloneIdx abc_'+jj);
    cln.find('*').val('');
    cln.find('.mytbody').empty();
    var apndPlace = $('.FormRowx');
    var rmbtn = cln.find('.removeBtnICards');
    rmbtn.click(function(){
      $(this).closest('.formBOxx').remove();
      clearSelectBoxForStaff();//added after when mytbodyStaff
    })

    var submit1 = cln.find('.submit_order');
    submit1.click(function(){
      clearSelectBoxForStaff();//added after when mytbodyStaff
      var order_no = $(this).parent().find('input.form-control.order_no').val();
      var amount = 32000;//no work

      //it is used for jj (counter Number)
      var orderClass = $(this).closest('.formBOxx').attr("class");
      //var lastChar = orderClass.substr(orderClass.length - 1); // => "1"
      var counterNoOfFormBOxx = orderClass.split("_").pop();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/getOrderDetail&token=<?php echo $token; ?>',
      type: "POST",
      data : {order_no:order_no,amount:amount},
      ContentType:"application/json",
      async:false,
      success: function(response){
  
      cln.find('.mytbody').empty();
      response = $.parseJSON(response);

      
      var data = response.orderDetails ;
      var norecord = response.norecord ;
      if (norecord == 0) {
          alert("No Record found");
      }

      //var radioOrderID;
      var radioPaymentID;
      //var radioPaymentIDName = "radioPaymentID" + jj;
      //var radioPaymentIDClass = "radioPaymentID" + jj;
      var radioPaymentIDName = "radioPaymentID" + counterNoOfFormBOxx;
      var radioPaymentIDClass = "radioPaymentID" + counterNoOfFormBOxx;

      var tbodyTest = "";

      $.each(data, function(i, item) {
        
        radioPaymentID = "<input type='radio' name='"+ radioPaymentIDName +"' value='"+ item.payment_id +"' class='"+ radioPaymentIDClass +"'>";

        tbodyTest += "<tr>";
        tbodyTest += "<td>";
        tbodyTest += radioPaymentID;
        tbodyTest += "</td>";

        tbodyTest += "<td>";
        tbodyTest += item.txn_date_time;
        tbodyTest += "</td>";

        tbodyTest += "<td>";
        tbodyTest += item.date_added;
        tbodyTest += "</td>";

        tbodyTest += "<td>" ;
        tbodyTest += item.amount;
        tbodyTest += "</td>" ;

        tbodyTest += "<td>" ;
        tbodyTest += item.order_no;
        tbodyTest += "</td>" ;

        tbodyTest += "<td>" ;
        tbodyTest += item.merchant_txn_id;
        tbodyTest += "</td>" ;        
        
        tbodyTest += "</tr>" ;

        cln.find('.mytbody').empty();
        cln.find('.mytbody').append(tbodyTest );

        ///////radiobtn - Start
          var radioClass = "." + radioPaymentIDClass;
          jQuery(radioClass).click(function(){ 
            var value = $("input[name='"+ radioPaymentIDName +"']:checked").val(); 
        
            jQuery(cln.find('.payment_idAA')).val(value);
            clearSelectBoxForStaff();//added after when mytbodyStaff
          });
        ///////radiobtn - End
       
       });    

      
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    })
    
    apndPlace.append(cln);
    jj++;
  }


  jQuery('.get_customer').click(function(){
     
    var customer_id = $(this).parent().find('input.form-control.customer_id').val();
    var ledger_id   = $('.ledgeridCOD').val();
    var group_id    = $('.groupidCOD').val();
    var receipt_id  = $('.receipt_idCOD').val();

    $('.customer_idCOD').val( customer_id );
    
    if( customer_id == '') { alert('Enter customer id.'); return false;}

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/getCustomerDetail&token=<?php echo $token; ?>',
      type: "GET",
      data : {customer_id:customer_id},
      dataType:"json",
      async:false,
      success: function(response){
  
          var html_tr = "<tr>";  
          var html_td = "";
          $.each(response, function(i, item) {
            
             html_td += "<td>" + item  + "</td>";

           }); 
          
          if(html_td=='') {
            html_tr += "<td colspan='4' align='center'>No Result</td>";
            $("#CODSecurityButtonDiv").hide()
          }else{
            $("#CODSecurityButtonDiv").show()
          }


           html_tr += html_td;
          html_tr   += "</tr>";  
         
          $('.FormRowxCOD').find('.formBOxxCOD ').not('.cloneIdx').find('.mytbody').empty();
          $('.FormRowxCOD').find('.formBOxxCOD ').not('.cloneIdx').find('.mytbody').append(html_tr );

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    })

  })

  $('.save_cod_security_deposit').click(function(){

    var customer_id = $('.customer_idCOD').val();
    var ledger_id   = $('.ledgeridCOD').val();
    var group_id    = $('.groupidCOD').val();
    var receipt_id  = $('.receipt_idCOD').val();

    if( customer_id == '') { alert('Error:: Customer id not found.'); return false;}
   
    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/setCustomerCODSecurityDeposit&token=<?php echo $token; ?>',
      type: "POST",
      data : {ledger_id:ledger_id, group_id:group_id, receipt_id:receipt_id, customer_id:customer_id},
      dataType:"json",
      async:false,
      success: function(response){
          if(response.status) {
            $('.spanx').trigger('click');
            alert(response.status);
            location.reload();
          } else if(response.error) {
            alert(response.error);
          }
      },
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( "Error::" + textStatus ); 
      }
    })

  })

  jQuery('.submit_order').click( function(){ 

    clearSelectBoxForStaff();//added after when mytbodyStaff
    
    var order_no = $(this).parent().find('input.form-control.order_no').val();
    var amount = $('.amountAA').val();
    
    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/getOrderDetail&token=<?php echo $token; ?>',
      type: "POST",
      data : {order_no:order_no,amount:amount},
      ContentType:"application/json",
      async:false,
      success: function(response){
  
      $('.FormRowx').find('.formBOxx ').not('.cloneIdx').find('.mytbody').empty();
      response = $.parseJSON(response);

      
      var data = response.orderDetails ;
      var norecord = response.norecord ;
      if (norecord == 0) {
          alert("No Record found");
      }

      //var radioOrderID;
      var radioPaymentID;
      var tbodyTest = "";

      $.each(data, function(i, item) {
        
        radioPaymentID = "<input type='radio' name='radioPaymentID' value='"+ item.payment_id +"' class='radioPaymentID'>";

        tbodyTest += "<tr>";
        tbodyTest += "<td>";
        tbodyTest += radioPaymentID;
        tbodyTest += "</td>";

        tbodyTest += "<td>";
        tbodyTest += item.txn_date_time;
        tbodyTest += "</td>";

        tbodyTest += "<td>";
        tbodyTest += item.date_added;
        tbodyTest += "</td>";

        tbodyTest += "<td>" ;
        tbodyTest += item.amount;
        tbodyTest += "</td>" ;

        tbodyTest += "<td>" ;
        tbodyTest += item.order_no;
        tbodyTest += "</td>" ;

        tbodyTest += "<td>" ;
        tbodyTest += item.merchant_txn_id;
        tbodyTest += "</td>" ;        
        
        tbodyTest += "</tr>" ;

        $('.FormRowx').find('.formBOxx ').not('.cloneIdx').find('.mytbody').empty();
        $('.FormRowx').find('.formBOxx ').not('.cloneIdx').find('.mytbody').append(tbodyTest );

          ///////radiobtn
          jQuery('.radioPaymentID').click(function(){   
            var value = jQuery('input[name=radioPaymentID]:checked').val(); 

            jQuery(this).closest('.formBOxx').find('.payment_idAA').val(value);
            //$(".staff").empty();//added after when mytbodyStaff
            clearSelectBoxForStaff();//added after when mytbodyStaff
            
          });
       
       });    

      
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });



  });

</script>

<script>
jQuery('.get_customer_by_id').click(function(){
   
  var customer_id = $(this).parent().find('input.form-control.customer_id').val();
  var ledger_id   = $('.ledgeridMembership').val();
  var group_id    = $('.groupidMembership').val();
  var receipt_id  = $('.receipt_idMembership').val();

  $('.customer_idMembership').val( customer_id );
  
  if( customer_id == '') { alert('Enter customer id.'); return false;}

  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankreceipt/getCustomerDetail&token=<?php echo $token; ?>',
    type: "GET",
    data : {customer_id:customer_id},
    dataType:"json",
    async:false,
    success: function(response){

        var html_tr = "<tr>";  
        var html_td = "";
        $.each(response, function(i, item) {
          
           html_td += "<td>" + item  + "</td>";

         }); 
        
        if(html_td=='') {
          html_tr += "<td colspan='4' align='center'>No Result</td>";
          $("#MembershipButtonDiv").hide()
        }else{
          $("#MembershipButtonDiv").show()
        }


         html_tr += html_td;
        html_tr   += "</tr>";  
       
        $('.FormRowxM').find('.formBOxxM ').not('.cloneIdx').find('.mytbody').empty();
        $('.FormRowxM').find('.formBOxxM ').not('.cloneIdx').find('.mytbody').append(html_tr );

    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus ); 
    }
  })

})

function getMembershipLevels(amountOfReceipt) {
  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankreceipt/getMembershipLevelDetails&token=<?php echo $token; ?>',
    type: "GET",
    dataType:"json",
    async:false,
    success: function(response){     
        var html_tr = "";
        $.each(response, function(i, item) {
            var html_td = '';
            if (amountOfReceipt == item.membership_fees) {
              html_td += '<td><input type="radio" name="membership_id" value="'+ item.membership_id +'" checked/></td>' ;
            } else {
              html_td += '<td><input type="radio" name="membership_id" value="'+ item.membership_id +'" /></td>' ;
            }
            html_td += '<td>'+ item.membership_name +'</td>';
            html_td += '<td>'+ item.membership_fees +'</td>';
            html_tr += "<tr>" + html_td + "</tr>";  
         }); 
        
        if(html_tr == '') {
          html_tr = "<td colspan='4' align='center'>No Active Membership levels found.</td>";
        }
        
        if (response.error) {
          html_tr = "<td colspan='4' align='center'>Error: " + response.error + "</td>";
        }

       
        $('.membership_div').find('.formBOxxM ').not('.cloneIdx').find('.mytbody').empty();
        $('.membership_div').find('.formBOxxM ').not('.cloneIdx').find('.mytbody').append(html_tr );

    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus ); 
    }
  })

}

$('.save_membership_deposit').click(function(){

  var customer_id = $('.customer_idMembership').val();
  var ledger_id   = $('.ledgeridMembership').val();
  var group_id    = $('.groupidMembership').val();
  var receipt_id  = $('.receipt_idMembership').val();
  var membership_id  = $("input[name='membership_id']:checked").val();

  if( customer_id == '') { alert('Error:: Customer id not found.'); return false;}
  if( typeof membership_id == 'undefined' || membership_id == '') { alert('Error:: No membership selected.'); return false;}
 
  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/bankreceipt/depositeMembershipFee&token=<?php echo $token; ?>',
    type: "POST",
    data : {ledger_id:ledger_id, group_id:group_id, receipt_id:receipt_id, customer_id:customer_id, membership_id:membership_id},
    dataType:"json",
    async:false,
    success: function(response){
        if(response.status) {
          $('.spanx').trigger('click');
          alert(response.success);
          location.reload();
        } else {
          alert(response.error);
        }
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( "Error::" + textStatus ); 
    }
  })

})
</script>

<script>
    jQuery(".addDirectSalesAjax").on('submit',(function(e) {

    e.preventDefault();

    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxDirectSales2&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false

      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var msg;

        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 11 ) {
          msg = "Order Payment ID not in numeric, Please Select Order Payment ID (Radio Buttons) of All Order Nos.";
          alert(msg);
        }
        if( status == 12 ) {
          msg = "Please select on Order Payment ID radio button";
          alert(msg);
        }
        if( status == 13 ) {
          msg = "Duplicate Payment ID ";
          alert(msg);
        }
        if( status == 14 ) {
          msg = "Entry already exists";
          alert(msg);
        }
        if( status == 15 ) {
          msg = "Total Amount Not Matched, Diff  of Rs. = "+ row +" , Please Select Sales Staff to transfer diff. amount";
          alert(msg);
        }
        if( status == 16 ) {
          msg = "Payment ID in same Order ID! Try Again";
          alert(msg);
        }
        if( status == 17 ) {
          msg = "CSV's Amount Total not match with Bank Amount in list.";
          alert(msg);
        }
        if( status == 18 ) {
          msg = "CSV's all order no(s) are not existing in DB.";
          alert(msg);
        }
        if( status == 19 ) {
          msg = "Duplicate order no(s) found in CSV.";
          alert(msg);
        }
        if( status == 1 ) {
          msg = "Invalid in Ledger! Try Again";
          alert(msg);
          location.reload();
        }

        if( status == 2 ) {
          msg = "Entry already exists";
          alert(msg);
        }        

        if( status == 666 ) {
          msg = "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team";
          alert(msg);
          location.reload();
        }

        if( status == 51 ) {
          msg = "Direct Sales Entry has been saved Successfully";
          alert(msg);
          location.reload();
        }
        if( status == 99 ) {
          msg = "Error! Try Again";
          alert(msg);
          location.reload();
        }

        if( status == 0 ) {
          msg = data.error_msg;
          alert(msg);
          location.reload();
        }

        if( status == 200 ) {
          msg = 'Successfully Uploaded!';
          alert(msg);
          location.reload();
        }

      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
  }));
  
</script>
<script type="text/javascript">
  //Not in use
  jQuery('.btnDirectSales').click( function(){

      var ledgerid = $('.ledgeridAA').val();
      var groupid = $('.groupidAA').val();
      var receipt_id = $('.receipt_id').val();
      var amountOfReceipt = $('.amountAA').val();
      
      var payment_id = $('.payment_idAA').val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/addAjaxDirectSales&token=<?php echo $token; ?>',
      type: "POST",
      data : {ledgerid:ledgerid,groupid:groupid,receipt_id:receipt_id,amountOfReceipt:amountOfReceipt,payment_id:payment_id},
      ContentType:"application/json",
      async:false,
      success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        if( status == 1 ) {
          msg = "Invalid in Ledger! Try Again ";
          alert(msg);
          location.reload();
        }

        if( status == 2 ) {
          msg = "Entry already exists";
          alert(msg);
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
  }); 
</script>


<script type="text/javascript">
  function getStaffList()
  {

    var payIDAA = [];
    $('.FormRowx :radio:checked').each(function(){
       payIDAA.push($(this).val());
    });
    
    //alert(payIDAA.length);die;
    if (payIDAA.length == 0)
    {
        var msg = "Please Select atleast one of Order Payment ID (Radio Buttons)";
        alert(msg);
    }
    else
    {
      jQuery.ajax(
      {
        url: 'index.php?route=account_panel/bankreceipt/getStaffs&token=<?php echo $token; ?>',
        type: "POST",
        data : {payIDAA:payIDAA},
        ContentType:"application/json",
        async:false,
        success: function(response){
    
        $(".mytbodyStaff").empty();
        response = $.parseJSON(response);

        var data = response.staffListArr ;
        var norecord = response.norecord ;
        
        var tbodyTest = "";
        //hard code for Direct Sales Balance Ledger ID = '7634', we use 'dsb' bcos in future it can be possible of staff_id = 7634
        if (norecord == 0) {
            alert("No Record found");
        }
        else
        {
          tbodyTest = "<option value='0'>Select Staff</option>";

          $.each(data, function(i, item) {

            tbodyTest += "<option value='"+ item.sales_staff_id+"'>";
            tbodyTest += item.name;       
            tbodyTest += "</option>";

           });

            $(".mytbodyStaff").empty();
            $(".mytbodyStaff").append(tbodyTest );
        }

        },      
        error: function(jqXHR, textStatus, errorThrown)
        {
          alert( textStatus ); 
        }
      });
    }


  }

  function clearSelectBoxForStaff()
  {
    $(".mytbodyStaff").empty();

    var selectList = "<select name='staff' class='staff mytbodyStaff'>";
    selectList += "<option value='0'>Select Staff</option>";
    selectList += "</select>";
    
    $('.staff').html(selectList);
  }
</script>

<script>
    $(".addDirectSalesBulkCSV").on('submit',(function(e) {

    e.preventDefault();
    
    jQuery('.ajaXloader').fadeIn(100);

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/bankreceipt/importDirectSalesBulkCSV&token=<?php echo $token; ?>',
      type: "POST",
      data: new FormData(this),
      contentType: false,       // The content type used when sending data to the server.
      cache: false,             // To unable request pages to be cached
      processData:false,        // To send DOMDocument or non processed data file it is set to false
  
      success: function(response){
        
        
        var data = $.parseJSON(response);
        var status = data.status;
        var row = data.row;
        var diff = data.diff;
        var msg;
        
        jQuery('.ajaXloader').fadeOut(100);
        
        if( status == 1 ) {
          msg = "Please insert Direct Sales Bulk Bank CSV";
          alert(msg);
        }
        if( status == 2 ) {
          msg = "Please Enter proper date in Direct Sales Bulk CSV, Row No. = "+ row ;
          alert(msg);
        }

        if( status == 3 ) {
          msg = "Please Enter Order No. in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 4 ) {
          msg = "Error in Order No(Customer Name not found in oc_order), Row No. = "+ row ;
          alert(msg);
        }
        if( status == 44 ) {
          msg = "Duplicate Order No. not allowed in CSV";
          alert(msg);
        }
        if( status == 5 ) {
          msg = "Please Enter Amount in Numeric, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 6 ) {
          msg = "Please Enter Positive Amount Value in Direct Sales Bulk PG CSV, Row No. = "+ row ;
          alert(msg);
        }
        if( status == 7 ) {
          msg = "Please Enter Bank's Ref.No., Row No. = "+ row ;
          alert(msg);
        }
        if( status == 8 ) {
          msg = "Bank Ref No. not found in Bank Payment Entry, Ref.No. = "+ row ;
          alert(msg);
        }
        if( status == 9 ) {
          msg = "Entry already done, Please enter new Bank Ref No. in Direct Sales Bulk PG CSV, Ref.No. = "+ row ;
          alert(msg);
        }

        if( status == 10 ) {
          msg = "Amount not matched. from Bank Statement on Bank Ref.No. = "+ row ;
          alert(msg);
        }
        if( status == 11 ) {
          msg = "This order has previously done entry, Order No."+ row ;
          alert(msg);
        }
        if( status == 12 ) {
          msg = "This order has more than 1 similar entries in oc_order_payment table according to order No. and Amount, Order No."+ row ;
          alert(msg);
        }

        if( status == 13 ) {
          msg = "This order is already linked with other entry, Order No."+ row ;
          alert(msg);
        }
        
        if( status == 50 ) {
          msg = "Please insert correct Direct Sales Bulk CSV";
          alert(msg);
        }

        if( status == 51 ) {
          msg = "Direct Sales Bulk CSV has been imported Successfully";
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
  }));
  
</script>

<script type="text/javascript"><!--
$('.button-filter').on('click', function() {

  var url = 'index.php?route=account_panel/bankreceipt&token=<?php echo $token; ?>';

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

  var filter_inflow = $('select[name=\'filter_inflow\']').val();
  if (filter_inflow) {
    url += '&filter_inflow=' + encodeURIComponent(filter_inflow);
  }

  var filter_status = $('select[name=\'filter_status\']').val();
  if (filter_status) {
    url += '&filter_status=' + encodeURIComponent(filter_status);
  }

  location = url;
  
});
</script>

<!-- //////////// To select Only 1 field either checkbox of shadow balance or to get Sales Staff list, Another field will get disabled ////////////-->
<script type="text/javascript">
  $('input[name="dsb"]').click(function(){
    if($(this).is(':checked')){
      $('input[name="click_staff_list"').attr('disabled',true);  
      $('select[name="staff"]').attr('disabled',true);
    } else {
      $('input[name="click_staff_list"').attr('disabled',false);  
      $('select[name="staff"]').attr('disabled',false);
    }
  });

  $('input[name="click_staff_list"').click(function(){
    $('input[name="dsb"]').attr('disabled',true);
  });

</script>