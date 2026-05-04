<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_tentative_nach;?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_tentative_nach;?></h3>
        <button type="button" class="btn btn-basic pull-right" style="margin-top: -9px;" data-toggle="modal" data-target="#add_nach_schedule" data-toggle="tooltip" data-original-title="Add New NACH Schedule of SuborderId"><i class="fa fa-plus"></i></button>
      </div>
      <div class="panel-body">
      <form method="post" name="tentative-nach-form" >
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="filter_customer_name-no"><?php echo $column_customer_name; ?></label>
                <input type="text" name="filter_customer_name" value="<?php echo $filter_customer_name; ?>" placeholder="<?php echo $column_customer_name; ?>" id="filter_customer_name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="order_no"><?php echo $entry_order_no; ?></label>
                <input type="text" name="filter_order_no" id="order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $entry_order_no; ?>" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="filter_customer_id"><?php echo $column_customer_id; ?></label>
                <input type="text" name="filter_customer_id" value="<?php echo $filter_customer_id; ?>" placeholder="<?php echo $column_customer_id; ?>" id="filter_customer_id" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="filter_umrn_lan"><?php echo $entry_umrn_lan; ?></label>
                <input type="text" name="filter_umrn_lan" value="<?php echo $filter_umrn_lan; ?>" placeholder="<?php echo $entry_umrn_lan; ?>" id="filter_umrn_lan" class="form-control" />
              </div>
            </div>
            
            
            <div class="col-sm-3">
                
              <div class="form-group">
                <label class="control-label" for="filter_nach_debit_date_from"><?php echo $column_nach_debit_date_from; ?></label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_nach_debit_date_from"
                           id="filter_nach_debit_date_from"
                           type="text"
                           value="<?php echo $filter_nach_debit_date_from; ?>"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
              
              <div class="form-group">
                <label class="control-label" for="filter_status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="filter_status" class="form-control">
                  <?php foreach ($status_options as $option) { ?>
                  <option value="<?php echo $option; ?>" <?php if($filter_status === $option) { echo "selected"; } ?>><?php echo $option; ?></option>
                  <?php } ?>
                  <option value="all" <?php if($filter_status === 'all') { echo "selected"; } ?>>All Statuses</option>
                </select>
              </div>
                

            </div>
            
            
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="filter_nach_debit_date_to"><?php echo $column_nach_debit_date_to; ?></label>
                <div class="input-group date">
                    <input class="form-control"
                           name="filter_nach_debit_date_to"
                           id="filter_nach_debit_date_to"
                           type="text"
                           value="<?php echo $filter_nach_debit_date_to; ?>"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
              
              <div class="row"> 
                  
               <div class="col-sm-5">
               <div class="form-group">
                <label class="control-label" for="filter_deffered_by_customer"><?php echo $entry_defferred_by_customer; ?></label>
                <select name="filter_deffered_by_customer" id="filter_deffered_by_customer" class="form-control">
                  <option value="0" <?php if($filter_deffered_by_customer === 0) { echo "selected"; } ?>>No</option>
                  <option value="1" <?php if($filter_deffered_by_customer === 1) { echo "selected"; } ?>>Yes</option>
                  <option value="-1" <?php if($filter_deffered_by_customer === -1) { echo "selected"; } ?>>All</option>
                </select>
               </div>
               </div>
               
               <div class="col-sm-7">
               <div class="form-group">
                <label class="control-label" for="filter_today_active_checksum"><?php echo $entry_today_active_checksum; ?></label>
                <select name="filter_today_active_checksum" id="filter_today_active_checksum" class="form-control">
                  <option value="1" <?php if($filter_today_active_checksum) { echo "selected"; } ?>>Today Active only (Override other filters; DO checksum)</option>
                  <option value="0" <?php if(!$filter_today_active_checksum) { echo "selected"; } ?>>All raw data (Consider filters; NO checksum)</option>
                </select>
               </div>
               </div>
               
              </div>
                            
            </div>
            
            
            </div>
            <div class="row">
              <div class="col-sm-12">
                <button type="button" id="btn_regenerate" class="btn btn-danger pull-left" name="button-regenerate"  data-toggle="tooltip" data-original-title="Regenerate NACH Schedule of Customer">
                  <i class="fa fa-refresh"></i> Re-generate</button>
                <input type="text" name="customer_id_to_regenerate" id="customer_id_to_regenerate" placeholder="<?php echo $column_customer_id; ?>" class="form-control pull-left" style="    width: 20%;margin-left: 5px;" />
             
             
                <button type="button" id="download_unverified_schedule" class="btn btn-basic pull-right" style="margin-left:10px;" name="download_unverified_schedule" data-toggle="tooltip" data-original-title="Download Unverified Receipt/Failed Schedules by Accounts">
                  <i class="fa fa-download"></i> Unverified Schedules</button>
                <button type="button" id="button-download" class="btn btn-basic pull-right" style="margin-left:10px;" name="button-download">
                  <i class="fa fa-download"></i> <?php echo $entry_download;?></button>
                <button type="submit" id="button-filter" class="btn btn-basic pull-right" style="margin-left:10px;" name="button-filter">
                  <i class="fa fa-search"></i> <?php echo $entry_filter; ?></button>
                <button type="button" id="button-current-download" class="btn btn-primary pull-right" style="margin-left:10px;" name="button-current-download">
                  <i class="fa fa-download"></i> <?php echo $entry_current_download;?></button>
              </div>
          </div>
        </div>
        </form>
          <div class="table-responsive" style="height:600px;">

          <?php if (!empty($tentative_nach) ) { ?>
            <table class="table table-bordered table-hover">
              <thead>
                <tr class="text-left">
                  <th rowspan="2">Debit Date</th>
                  <th rowspan="2">Customer</th>
                  <th colspan="5" style="text-align: center;">NACH A/c Details</th>
                  <th rowspan="2">Total Debit Amount</th>
                  <th rowspan="2">Action</th>
                </tr>
                <tr>
                  <th>A/c Name</th>
                  <th>A/c No</th>
                  <th>IFSC</th>
                  <th>UMRN (LAN)</th>
                  <th>Bank</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tentative_nach as $comp_key => $customer_data) { ?>
                  <tr class="customer_data_row" data-child="child-row-<?php echo $comp_key; ?>">
                    <td><?php echo $customer_data['nach_debit_date'];?></td>
                    <td><?php echo $customer_data['customer_name'] . ' (cid: ' . $customer_data['customer_id'] . ')';?></td>
                    <td><?php echo $customer_data['account_name'];?></td>
                    <td><?php echo $customer_data['account_no'];?></td>
                    <td><?php echo $customer_data['ifsc_code'];?></td>
                    <td><?php echo $customer_data['umrn_no'] . (!empty($customer_data['lan_no']) ? ' (' . $customer_data['lan_no'] . ')' : '');?></td>
                    <td><?php echo $customer_data['bank_type'];?></td>
                    <td><?php echo $customer_data['total_nach_debit_amount'];?></td>
                    <td>
                      <!-- View NACH Schedule(s) Button (to show rowwise breakup) -->
                      <button class="btn btn-sm btn-info btn-show-schedule" 
                              data-comp_key="<?php echo $comp_key; ?>" 
                              data-toggle="tooltip" 
                              data-original-title="View NACH Schedule(s)" >
                        <i class="fa fa-plus" id="show_hide_<?php echo $comp_key; ?>"></i>
                      </button>
                      
                      <!-- Mark Deffered Button (visible only if Debit Date is >= Current Date) -->
                      <?php if( strtotime($customer_data['nach_debit_date']) >= strtotime(date('Y-m-d')) ) { ?>
                      <button class="btn btn-sm btn-danger mark_deffered_for_customer" 
                              data-customer_id="<?php echo $customer_data['customer_id']; ?>"
                              data-customer_name="<?php echo $customer_data['customer_name']; ?>"  
                              data-deffer_date="<?php echo $customer_data['nach_debit_date']; ?>" 
                              data-toggle="tooltip" 
                              data-original-title="Defer NACH Debits of Customer" >
                        <i class="fa fa-ban"></i>
                      </button>
                      
                      <!-- Shift Schedule Button (visible only if Debit Date is >= Current Date) -->
                      <button class="btn btn-sm btn-warning shift_schedule_for_customer" 
                              data-customer_id="<?php echo $customer_data['customer_id']; ?>" 
                              data-customer_name="<?php echo $customer_data['customer_name']; ?>" 
                              data-shift_from_date="<?php echo $customer_data['nach_debit_date']; ?>" 
                              data-toggle="tooltip" 
                              data-original-title="Shift NACH Debits of Customer" >
                        <i class="fa fa-arrows-h"></i>
                      </button>
                      <?php } ?>
                      
                      <!-- Edit Credit Details of Customer Button -->
                      <a href="<?php echo $customer_data['customer_credits_link']; ?>" 
                         class="btn btn-primary margin-top btn btn-sm" 
                         data-toggle="tooltip" 
                         title="Edit Credit Details of Customer"  
                         target="_blank">
                        <i class="fa fa-credit-card"></i>
                      </a>
                    </td>
                  </tr>
                  
                  <tr class="child child-row-<?php echo $comp_key; ?> bg-success" style="display: none;">
                    <td class="text-center" colspan="10" id="child_row_<?php echo $comp_key; ?>">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <td>Order No (Order Id)</td>
                            <td>Total Debit Amount / Order Bal</td>
                            <td>Suborder (Total)</td>
                            <td>Schedule Id (Status)</td>
                            <td>Debit Amount</td>
                            <td>Action</td>
                          </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $customer_data['nach_schedules'] as $order_id => $order_data ){ ?>
                          <?php $rows_to_span = count($order_data['nach_schedules']); ?>
                          <?php $rows_spanned = false; ?>
                          <?php foreach( $order_data['nach_schedules'] as $nach_schedule ) { ?>
                          <tr>
                          <?php if ( !$rows_spanned ) { ?>
                            <td rowspan="<?php echo $rows_to_span;?>">
                              <a href="<?php echo $order_data['order_list_link']; ?>" target="_blank">
                                <?php echo $order_data['order_no'].' ('.$order_data['order_id'].')'; ?>
                              </a>
                            </td>
                            <td rowspan="<?php echo $rows_to_span;?>">
                              <?php echo $order_data['total_nach_debit_amount'] . ' / ' . ($order_data['order_bal'] ?? '--'); ?>
                            </td>
                          <?php $rows_spanned = true; ?>
                          <?php } ?>
                            <td>
                              <?php echo $nach_schedule['suborder_id']; ?> (<b><?php echo $nach_schedule['suborder_total']; ?></b>)<br>
                              <?php echo 'Delivery: ' . $nach_schedule['delivered_date']; ?>
                            </td>
                            <td>
                              <?php echo $nach_schedule['nach_schedule_id'] . ' (' . $nach_schedule['status'] . ')'; ?>
                              <?php if ( $nach_schedule['deffered_by_customer'] ) { ?>
                                <br><span class="label label-danger">Deffered</span>
                              <?php } ?> 
                            </td>
                            <td>
                              <?php echo $nach_schedule['nach_debit_amount']; ?><br>
                              <div id="amount_change_div_<?php echo $nach_schedule['nach_schedule_id'];?>" 
                                   style="display: none">
                                <input type="number" 
                                       name="amount_change_<?php echo $nach_schedule['nach_schedule_id'];?>" 
                                       id="amount_change_<?php echo $nach_schedule['nach_schedule_id'];?>" 
                                       value="<?php echo $nach_schedule['nach_debit_amount']; ?>" 
                                       class="form-control"
                                       data-old-value="<?php echo $nach_schedule['nach_debit_amount']; ?>" />
                                <textarea id="comment_<?php echo $nach_schedule['nach_schedule_id'];?>"
                                          class="form-control comment" 
                                          placeholder="Comment"></textarea>
                                <button name="save_btn" 
                                        class="save_btn btn btn-primary" 
                                        data-id="<?php echo $nach_schedule['nach_schedule_id'];?>"
                                        data-suborder_id="<?php echo $nach_schedule['suborder_id'];?>"
                                        data-debit_date="<?php echo $nach_schedule['nach_debit_date'];?>">
                                  <i class="fa fa-save"></i>
                                </button>
                                <button class="cancel_btn btn btn-danger" 
                                        data-id="<?php echo $nach_schedule['nach_schedule_id'];?>">
                                  <i class="fa fa-close"></i>
                                </button>
                              </div>
                            </td>
                            <td>
                            <!-- Mark Deffered Button (visible only if Debit Date is >= Current Date) -->
                            <?php if( strtotime($nach_schedule['nach_debit_date']) >= strtotime(date('Y-m-d')) ) { ?>
                              <button class="btn btn-sm btn-danger mark_deffered" 
                                      data-toggle="tooltip" 
                                      data-id="<?php echo $nach_schedule['nach_schedule_id']; ?>" 
                                      data-original-title="Defer this NACH Schedule">
                                <i class="fa fa-ban"></i>
                              </button>
                            <!-- Shift Schedule Button (visible only if Debit Date is >= Current Date) -->
                              <button class="btn btn-sm btn-warning shift_schedule" 
                                      data-id="<?php echo $nach_schedule['nach_schedule_id']; ?>" 
                                      data-shift_from_date="<?php echo $nach_schedule['nach_debit_date']; ?>"
                                      data-customer_id="<?php echo $customer_data['customer_id']; ?>" 
                                      data-customer_name="<?php echo $customer_data['customer_name']; ?>" 
                                      data-suborder_id="<?php echo $nach_schedule['suborder_id']; ?>"
                                      data-nach_debit_amount="<?php echo $nach_schedule['nach_debit_amount']; ?>"
                                      data-toggle="tooltip" 
                                      data-original-title="Shift this NACH Schedule">
                                <i class="fa fa-arrows-h"></i>
                              </button>
                            <!-- Change Amount Button (visible only if Debit Date is >= Current Date) -->
                              <button class="btn btn-sm btn-primary change_amount" 
                                      data-id="<?php echo $nach_schedule['nach_schedule_id']; ?>" 
                                      id="change_amount_<?php echo $nach_schedule['nach_schedule_id']; ?>" 
                                      data-toggle="tooltip" 
                                      data-original-title="Change the Debit Amount">
                                <i class="fa fa-money"></i>
                              </button>
                            <?php } ?>
                            <!-- View Changelog Button -->
                              <button class="btn btn-sm btn-primary" 
                                      data-toggle="tooltip" 
                                      data-original-title="View Changelog of this Schedule"
                                      data-id="<?php echo $nach_schedule['nach_schedule_id']; ?>"
                                      onclick="getScheduleHistory(this);">
                                <i class="fa fa-history"></i>
                              </button>
                            </td>
                          </tr>
                          <?php } ?>
                        <?php } ?>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <?php } else { ?>
              <div><?php echo $text_no_results; ?></div>
            <?php } ?>
          </div>
      </div>
    </div>
  </div>
</div>
<!--  View NACH Schedule log history pop-up  -->
<div class="modal fade" id="schedule-history" tabindex="-1" role="dialog" aria-labelledby="NACH-Schedule-History">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="historyTab">NACH Schedule History</h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>S. No.</th>
              <th>Log Id</th>
              <th>Field Name</th>
              <th>Old Value</th>
              <th>New Value</th>
              <th>Comment</th>
              <th>Date</th>
              <th>User</th>
            </tr>
          </thead>
          <tbody id="history_body">

          </tbody>
        </table>
        <div class="error-msg"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Pop-up for input Date and comment to shift NACH schedule at both suborder_level and customer_level   -->
<div class="modal fade" id="shift_schedule_popup" tabindex="-1" role="dialog" aria-labelledby="NACH-Schedule-Shift-Pop-Up">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title nach_shift_popup_title"></h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">
        <input type="hidden" name="schedule_shift_level" id="schedule_shift_level" value="" />
        <input type="hidden" name="id" id="id" value="" />
        <input type="hidden" name="customer_name" id="customer_name" value="" />
        <input type="hidden" name="suborder_id" id="suborder_id" value="" />
        <input type="hidden" name="shift_from_date" id="shift_from_date" />
        <input type="hidden" name="nach_debit_amount" id="nach_debit_amount" value="" />

        <div class="error-msg"></div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="col-sm-4">
                <label class="control-label" for="new_date_to_shift_schedule">Select new date for shifting schedule(s):</label>
              </div>
              <div class="col-sm-8">
                <div class="form-group">
                  <div class="col-sm-6  input-group shift-date">
                    <input class="form-control"
                           name="new_date_to_shift_schedule"
                           id="new_date_to_shift_schedule"
                           type="text"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default calender_btn"><i class="fa fa-calendar"></i></button>
                    </span>
                  </div>
                  <div class="col-sm-1">OR</div>
                  <div class="col-sm-4 input-group">
                    <input type="checkbox" name="shift_to_end_date" id="shift_to_end_date" />
                    <label class="control-label" for="new_date_to_shift_schedule"> Shift Schedule(s) to End</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="col-sm-4">
                <label class="control-label" for="order_no">Comment :</label>
              </div>
              <div class="col-sm-4">
                <textarea placeholder="Comment" 
                  id="comment_to_shift_schedule"
                  class="form-control comment"></textarea>
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <div class="col-sm-2">
              <button id="submit_shift_schedule" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
            </div>
            <div class="col-sm-2">
              <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"></i> Cancel</button>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<!--  ADD NACH Schedule for SuborderId  -->
<div class="modal fade" id="add_nach_schedule" tabindex="-1" role="dialog" aria-labelledby="Add-NACH-Schedule">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="historyTab">Add NACH Schedule</h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">

        <div class="error-msg" id="add_nach_error"></div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="col-sm-4">
                <label class="control-label required-label" for="new_date_to_shift_schedule">Schedule Date : </label>
              </div>
              <div class="col-sm-8">
                <div class="form-group">
                  <div class="col-sm-6  input-group shift-date">
                    <input class="form-control"
                           name="new_schedule_date"
                           id="new_schedule_date"
                           type="text"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default calender_btn"><i class="fa fa-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group">
              <div class="col-sm-4">
                <label class="control-label required-label" for="add_suborder_id">Suborder-ID : </label>
              </div>
              <div class="col-sm-4">
                <input type="text" name="add_suborder_id" id="add_suborder_id" style="margin-bottom: 5px;" class="form-control" />
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group">
              <div class="col-sm-4">
                <label class="control-label required-label" for="schedule_amount">Schedule Amount : </label>
              </div>
              <div class="col-sm-4">
                <input type="text" name="schedule_amount" id="schedule_amount" class="form-control" />
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group">
              <div class="col-sm-4">
                <label class="control-label required-label" for="comment_to_add_schedule">Comment : </label>
              </div>
              <div class="col-sm-4">
                <textarea placeholder="Comment" 
                  id="comment_to_add_schedule"
                  class="form-control comment"></textarea>
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <div class="col-sm-2">
              <button id="submit_add_schedule" class="btn btn-primary"><i class="fa fa-save"></i> Add</button>
            </div>
            <div class="col-sm-2">
              <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"></i> Cancel</button>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<?php echo $footer; ?>
<style type="text/css">
  td { white-space: nowrap; } 
  .on_hold_date{ width:70%!important; }
  .date_btn_nopadding{
    padding: 0px;
  }
  .show_schedule_row + .child{
      display: table-row!important;
  }
  .comment{
    margin: 5px 0px;
  }
  .form-group .required-label:after {
    content:"*";
    color:red;
  }
</style>
<script type="text/javascript">
  var token = '<?php echo $token; ?>';
  
  $('#btn_regenerate').click(function(){
    alert('Regenerate option Unavailable currently!');
    return false;
    var customer_id = $('#customer_id_to_regenerate').val().trim();
    if(customer_id.length <= 0){
      alert('Invalid Customer Id');    
    }else{
      if(confirm("Warning! Do you really want to regenerate the pending NACH Debit Schedules of the customer: "+ customer_id +" ? Note that this change is irreversible, and the new schedule will be generated based on the new wsb_credit configuration rules for this customer. ?")){
        $.ajax({
          url: 'index.php?route=account_panel/tentative_nach/regenerateScheduleOfCustomer&token=<?php echo $token; ?>',
          type: 'post',
          data: 'customer_id='+customer_id,
          success: function(json) {
            json = $.trim(json);
            if(json.length > 0){
              alert(json);
            }else{
              alert("NACH Schedule(s) for the customer regenerated Successfully!!");
              location.reload();
            }
          }
        }); 
      }
    }
  });

  $('.btn-show-schedule').click(function(){

    var comp_key = $(this).data('comp_key');
    var plus_class_exist = $('#show_hide_'+comp_key).hasClass('fa-plus');

    if(plus_class_exist){
      $(this).parents('tr').addClass('show_schedule_row bg-success');
      $('#show_hide_'+comp_key).removeClass("fa-plus").addClass("fa-minus");
    }else{
      $(this).parents('tr').removeClass('show_schedule_row bg-success');
      $('#show_hide_'+comp_key).removeClass("fa-minus").addClass("fa-plus");
    }
  });

  //Download Current Sheet
  $('#button-current-download').on('click', function() { 
    var url = 'index.php?route=account_panel/tentative_nach/downloadTodayBankSheet&token=<?php echo $token; ?>';
    location = url;
  });

  //Download Breakup CSV
  $('#button-download').on('click', function() { 
    var url = 'index.php?route=account_panel/tentative_nach/downloadCsv&token=<?php echo $token; ?>';

    var filter_order_no = $('#order_no').val();
    if (filter_order_no) {
      url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }

    var filter_customer_name = $('#filter_customer_name').val();
    if (filter_customer_name) {
      url += '&filter_customer_name=' + encodeURIComponent(filter_customer_name);
    }

    var filter_customer_id = $('#filter_customer_id').val();
    if (filter_customer_id) {
      url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
    }

    var filter_umrn_lan = $('#filter_umrn_lan').val();
    if (filter_umrn_lan) {
      url += '&filter_umrn_lan=' + encodeURIComponent(filter_umrn_lan);
    }

    var filter_nach_debit_date_from = $('#filter_nach_debit_date_from').val();
    /* Even if no date selected - we need to set the key - otherwise defaults to current date */
    url += '&filter_nach_debit_date_from=' + encodeURIComponent(filter_nach_debit_date_from);
    
    var filter_nach_debit_date_to = $('#filter_nach_debit_date_to').val();
    /* Even if no date selected - we need to set the key - otherwise defaults to current date */
    url += '&filter_nach_debit_date_to=' + encodeURIComponent(filter_nach_debit_date_to);

    var filter_deffered_by_customer = $('#filter_deffered_by_customer').val();
    if (filter_deffered_by_customer) {
      url += '&filter_deffered_by_customer=' + encodeURIComponent(filter_deffered_by_customer);
    }
    
    var filter_status = $('#filter_status').val();
    if (filter_status) {
      url += '&filter_status=' + encodeURIComponent(filter_status);
    }
    
    var filter_today_active_checksum = $('#filter_today_active_checksum').val();
    if (filter_today_active_checksum) {
      url += '&filter_today_active_checksum=' + encodeURIComponent(filter_today_active_checksum);
    }
    
    location = url;
  });

  //Dowload
  $('#download_unverified_schedule').on('click', function() { 
    var url = 'index.php?route=account_panel/tentative_nach/downloadUnverifiedSchedule&token=<?php echo $token; ?>';
    location = url;
  });

  //Add New NACH Schedule
  $('#submit_add_schedule').on('click', function(){
    
    var new_schedule_date = $('#new_schedule_date').val().trim();
    if (new_schedule_date.length <= 0) {
      alert("NACH Schedule Date is Mendatory!!");
      return false;
    }

    var suborder_id = $('#add_suborder_id').val().trim();
    if (suborder_id.length <= 0) {
      alert("Suborder-ID is mendatory!!");
      return false;
    }

    var regex = /(?:\d*\.\d{1,2}|\d+)$/;
    var schedule_amount = $('#schedule_amount').val().trim();
    if (schedule_amount.length <= 0) {
      alert("Schedule Amount is Mendatory!!");
      return false;
    }else if (! regex.test(schedule_amount)) {
      alert("Schedule Amount is not valid number!!");
      return false;
    }

    var comment_to_add_schedule = $('#comment_to_add_schedule').val().trim();
    if (comment_to_add_schedule.length <= 0) {
      alert("Comment is mendatory!!");
      return false;
    }

    var url = 'index.php?route=account_panel/tentative_nach/addNachSchedule&token=<?php echo $token; ?>';

    $.ajax({
      url: url,
      type: 'post',
      data: 'nach_debit_date='+new_schedule_date+'&suborder_id='+suborder_id+'&nach_debit_amount='+schedule_amount+'&comment='+comment_to_add_schedule,
      success: function(json) {
        
        data = JSON.parse(json);
        
        if(data.status == 'success' ){
          location.reload();
        }else{
          alert(data.message.trim());
        }
      }
    }); 


  });

  $(document).ready(function(){
    var current_date = new Date();

    $('.shift-date').datetimepicker({
      minDate: current_date, // Current date i.e. today validation
      pickTime: false
    });
    $('.date').datetimepicker({
      pickTime: false
    });
  });

  function getScheduleHistory(obj){
    var nach_schedule_id = $(obj).data('id');
    $.ajax({
      url: 'index.php?route=account_panel/tentative_nach/getScheduleHistory&token=<?php echo $token; ?>',
      type: 'post',
      data: 'nach_schedule_id='+nach_schedule_id,
      success: function(json) {
        
        data = JSON.parse(json);
        if(data.length > 0 ){
          $('#history_body').html('');
          $('.error-msg').html('');
          var i = 1;
          var row = '';
          $(data).each( function( key, element ) {

              row  += '<tr>';
              row     +=    '<td>' + i  +'</td>';
              row     +=    '<td>' + element.log_id + '</td>';
              row     +=    '<td>' + element.field_name  + '</td>';
              row     +=    '<td>' + element.old_value  + '</td>';
              row     +=    '<td>' + element.new_value + '</td>';
              row     +=    '<td>' + element.comment + '</td>';
              row     +=    '<td>' + element.date_added + '</td>';
              row     +=    '<td>' + element.user_name + ' ('+ element.user_id +')' + '</td>';
              row     += '</tr>';
              i++;
          });
          $('#history_body').html(row);
        }else{
          $('#history_body').html('');
          $('.error-msg').html('<p class="alert alert-danger">No Changelog records found for this Schedule!<p>');
        }
      }
    }); 

    $('#schedule-history').modal('show');
  }

  $('#shift_to_end_date').change(function(){
    if($(this).prop("checked") == true){
        $('#new_date_to_shift_schedule').val('');
        $("#new_date_to_shift_schedule").attr( 'readOnly' , 'true' );
        $('.calender_btn').css( 'display' , 'none' );
    }
    else if($(this).prop("checked") == false){
        $("#new_date_to_shift_schedule").removeAttr( 'readOnly' , 'false' );
        $('.calender_btn').css( 'display' , 'block' );
    }
  });

  $('.mark_deffered_for_customer').click(function(){
    var customer_id = $(this).data('customer_id');
    var customer_name = $(this).data('customer_name');
    var deffer_date = $(this).data('deffer_date');

    if(confirm("Do you really want to Defer all the NACH debit(s) for customer: "+ customer_id + " - " + customer_name +", on "+ deffer_date +" ?")){
      $.ajax({
        url: 'index.php?route=account_panel/tentative_nach/deferNachSchedulesOfCustomer&token=<?php echo $token; ?>',
        type: 'post',
        data: 'customer_id='+customer_id+'&deffer_date='+deffer_date,
        success: function(json) {
          json = $.trim(json);
          if(json.length > 0){
            alert(json);
          }else{
            alert("NACH Schedule(s) deffered for the customer Successfully!!");
            location.reload();
          }
        }
      }); 
    }
  });

  $('.shift_schedule').click(function(){
    var customer_id       = $(this).data('customer_id');
    var customer_name     = $(this).data('customer_name');
    var nach_schedule_id = $(this).data('id');
    var shift_from_date  = $(this).data('shift_from_date');
    var suborder_id      = $(this).data('suborder_id');
    var nach_debit_amount = $(this).data('nach_debit_amount');
    
    $('.nach_shift_popup_title').text("Shift NACH Schedule(s) of the "+ customer_name +" (cid: "+ customer_id +") on " + shift_from_date);
    $('#schedule_shift_level').val('schedule');
    $('#id').val(nach_schedule_id);
    $('#shift_from_date').val(shift_from_date);
    $('#suborder_id').val(suborder_id);
    $('#nach_debit_amount').val(nach_debit_amount);

    $('#shift_schedule_popup').modal('show');

  });

  $('.shift_schedule_for_customer').click(function(){
    var customer_id       = $(this).data('customer_id');
    var customer_name     = $(this).data('customer_name');
    var shift_from_date   = $(this).data('shift_from_date');
    
    $('.nach_shift_popup_title').text("Shift NACH Schedule(s) of the "+ customer_name +" (cid: "+ customer_id +") on " + shift_from_date);
    $('#schedule_shift_level').val('customer');
    $('#id').val(customer_id);
    $('#customer_name').val(customer_name);
    $('#shift_from_date').val(shift_from_date);

    $('#shift_schedule_popup').modal('show');

  });
  function handler(e){
      e.stopPropagation();
      e.preventDefault();
  }

  $('#submit_shift_schedule').click(function(){

    var schedule_shift_level = $('#schedule_shift_level').val().trim();
    var suborder_id   = $('#suborder_id').val();

    var shift_from_date = $('#shift_from_date').val().trim();
    var new_date        = $('#new_date_to_shift_schedule').val().trim();
    var comment         = $('#comment_to_shift_schedule').val().trim();
    
    //Error validation
    if(new_date.trim() == 0 && $('#shift_to_end_date').prop("checked") == false){
      alert("Please provide a new date to shift the Schedule, OR, select shift schedule to the end!");
      return false;
    }else if( new_date.trim() == shift_from_date.trim() ){
      alert("You cannot select the date same as the current date of the schedule for shifting!");
      return false;
    }

    var shift_to_date = new_date.trim();
    if(shift_to_date == 0 && $('#shift_to_end_date').prop("checked") == true){
      shift_to_date = "the end";
    } else if(shift_to_date == 0) {
      alert("Something went wrong! Please refresh the page, and Try again!");
      return false;
    }

    //validate inputs
    var validate_msg    = validateShiftScheduleInput();

    if(validate_msg == 'success'){
      //To close popup
      $('#shift_schedule_popup').modal('hide');
      //To disable screen
      document.addEventListener("click",handler,true);

      if(schedule_shift_level == 'customer'){
        var customer_id   = $('#id').val();
        var customer_name = $('#customer_name').val();
        
        if(confirm("Do you really want to Shift all the NACH debit(s) for this customer "+ customer_id +" - "+ customer_name +" to "+ shift_to_date +" ?")){
          $.ajax({
            url: 'index.php?route=account_panel/tentative_nach/shiftNachSchedulesOfCustomer&token=<?php echo $token; ?>',
            type: 'post',
            data: 'customer_id='+customer_id+'&given_date='+shift_from_date+'&new_date='+new_date+'&comment='+comment,
            success: function(json) {
              json = $.trim(json);
              if(json.length > 0){
                alert(json);
                document.removeEventListener("click",handler,true);
              }else{
                alert("Schedule(s) of the customer are shifted Successfully!");
                location.reload();
              }
            }
          }); 
        }

      }else{

        var nach_schedule_id  = $('#id').val();
        var nach_debit_amount = $('#nach_debit_amount').val();

        if(confirm("Do you really want to Shift this NACH Debit Schedule of '"+ suborder_id +"', amounting '"+ nach_debit_amount +"', on '"+ shift_from_date +"' to "+ shift_to_date +" ?")){
          $.ajax({
            url: 'index.php?route=account_panel/tentative_nach/shiftNachSchedule&token=<?php echo $token; ?>',
            type: 'post',
            data: 'nach_schedule_id='+nach_schedule_id+'&new_date='+new_date+'&comment='+comment,
            success: function(json) {
              json = $.trim(json);
              if(json.length > 0){
                alert(json);
                document.removeEventListener("click",handler,true);
              }else{
                alert("NACH Schedule is shifted Successfully!");
                location.reload();
              }
            }
          }); 
        }
      }
    }else{
      alert(validate_msg);
    }
    return;
    
  });

  function validateShiftScheduleInput(){
    var err_msg = 'success';

    shift_comment = $('#comment_to_shift_schedule').val().trim();
    if( shift_comment <= 0){
      err_msg = "Comment must be entered.";
      return err_msg;
    }

    return err_msg;
  }

  $('.mark_deffered').click(function(){
    var nach_schedule_id = $(this).data('id');

    if(confirm("Are you sure that you want to mark this NACH schedule as 'Deffered' ?")){
      $.ajax({
        url: 'index.php?route=account_panel/tentative_nach/markDefferedByCustomer&token=<?php echo $token; ?>',
        type: 'post',
        data: 'nach_schedule_id='+nach_schedule_id,
        success: function(json) {
          json = $.trim(json);
          if(json.length > 0){
            alert(json);
          }else{
            alert("NACH Schedule is Defferred Successfully!");
            location.reload();
          }
        }
      }); 
    }
  });

  $('.change_amount').click(function(){
    $(this).hide();
    var nach_schedule_id = $(this).data('id');
    $('#amount_change_div_'+nach_schedule_id).css('display', 'block');
  });
  $('.cancel_btn').click(function(){
    var nach_schedule_id = $(this).data('id');
    $('#change_amount_'+nach_schedule_id).show();
    $('#amount_change_div_'+nach_schedule_id).css('display', 'none');
  });

  $('.save_btn').click(function(){
    var nach_schedule_id    = $(this).data('id');
    var suborder_id         = $(this).data('suborder_id');
    var debit_date          = $(this).data('debit_date');
    var updated_amount      = $('#amount_change_'+nach_schedule_id).val();
    var old_amount          = $('#amount_change_'+nach_schedule_id).attr('data-old-value');
    var comment             = $('#comment_'+nach_schedule_id).val();
    
    if(updated_amount <= '0' || old_amount == updated_amount){
      alert('Invalid amount! Please ensure that the revised amount is not negative, and not same as previous one!');
    }else if($.trim(comment).length <= 0){
      alert('Proper Comment must be entered to change amount in a Schedule!');
    }else if(old_amount != updated_amount){
      if(confirm("Do you really want to change the NACH Debit amount of '"+ suborder_id +"', on Date:"+ debit_date +", from "+ old_amount +" to "+ updated_amount +" ?")){
        $.ajax({
          url: 'index.php?route=account_panel/tentative_nach/updateNachAmount&token=<?php echo $token; ?>',
          type: 'post',
          data: 'nach_schedule_id='+nach_schedule_id+'&comment='+comment+'&old_amount='+old_amount+'&updated_amount='+updated_amount,
          success: function(json) {
            json = $.trim(json);
            if(json.length > 0){
              alert(json);
            }else{
              alert("Amount in the NACH Schedule(s) is Updated Successfully!");
              location.reload();
            }
          }
        });
      }
    }
  });

</script>
