<div class="content col-sm-7">
  <?php 
    $form_type = 'add';
    if(!empty($wsb_credit_details)){ $form_type = 'edit'; }
  ?>
  <input type="hidden" name="hddn_form_type" id="hddn_form_type" value="<?php echo $form_type; ?>" />
  
  <div class="form-group">
    <label class="col-sm-4 control-label"><?php echo $entry_enable_wsb_credit_payment; ?></label>
    <div class="col-sm-8">
    <select name="wsb_credit_payment_status" id="wsb_credit_payment_status" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $wsb_credit_details['status']?? ''; ?>">
      <?php foreach($all_wsb_credit_payment_statuses as $value){ $str = '';
      ?>
        <?php if(!empty($wsb_credit_details['status']) && $wsb_credit_details['status'] === $value){ $str = 'selected="selected"'; } ?>
        <option value="<?php echo $value;?>" <?php echo $str;?> ><?php echo $value; ?></option>
      <?php }?>
    </select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="wsb_credit_payment_limit">
      <?php echo $entry_wsb_credit_payment_limit; ?>
    </label>
    <div class="col-sm-8">
      <?php if( !is_null($crif_score) ){ ?>
      <span>CRIF Score: <b><?php echo $crif_score; ?></b> on Date: <b><?php echo $crif_score_date; ?></b></span><br>
      <?php } ?>
      <?php if(!empty($credit_approved_limit) && !empty($credit_approved_date) ){ ?>
        <span>Approved Credit Limit In (Credit Application):<b> <?php echo $credit_approved_limit.'/-'; ?> </b> on <b><?php echo $credit_approved_date; ?></b></span>
      <?php } ?>
      <input type="text" name="wsb_credit_payment_limit" value="<?php echo $wsb_credit_details['credit_limit'] ?? 0; ?>"  id="wsb_credit_payment_limit" class="form-control" data-block-name="general" data-change="false" data-old-value="<?php echo $wsb_credit_details['credit_limit'] ?? 0; ?>" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="wsb_credit_payment_limit">
      <?php echo $entry_nach_schedule_crontab; ?>
    </label>
    <div class="col-sm-4">
      <input type="text" name="nach_schedule_crontab" value="<?php echo $wsb_credit_details['nach_schedule_crontab'] ?? '30 11 * * *'; ?>"  id="nach_schedule_crontab" class="form-control" data-block-name="general" data-change="false" data-old-value="<?php echo $wsb_credit_details['nach_schedule_crontab'] ?? ''; ?>" />
      <b><span class="crontab_title_check" style="color: green;"></span></b><br>
      <span><i><?php echo $wsb_credit_details['crontab_title'] ?? ''; ?></i></span>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <button type="button" id="check_nach_last_date" data-toggle="tooltip" title="Click to check Last NACH schedule date and total days to complete recovery." class="btn btn-info">Check Recovery Days</button>
      </div>
      <div class="form-group">
        <button type="button" id="check-crontab-title" data-toggle="tooltip" title="Click to check entered crontab string meaning" class="btn btn-primary">Check Crontab Meaning</button>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="days_before_nach_start">
      <?php echo $entry_days_before_nach_start; ?>
    </label>
    <div class="col-sm-8">
      <input type="text" name="days_before_nach_start" value="<?php echo $wsb_credit_details['days_before_nach_start'] ?? 4; ?>"  id="days_before_nach_start" class="form-control" data-block-name="general" data-change="false" data-old-value="<?php echo $wsb_credit_details['days_before_nach_start'] ?? 0; ?>" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="schedule_days_for_nach">
      <?php echo $entry_schedule_days_for_nach; ?>
    </label>
    <div class="col-sm-8">
      <input type="text" name="schedule_days_for_nach" value="<?php echo $wsb_credit_details['schedule_days_for_nach'] ?? 20; ?>"  id="schedule_days_for_nach" class="form-control" data-block-name="general" data-change="false" data-old-value="<?php echo $wsb_credit_details['schedule_days_for_nach'] ?? 0; ?>" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="schedule_days_for_nach">
      <?php echo $entry_auto_nach_enabled; ?>
    </label>
    <div class="col-sm-8">
      <!-- Rounded switch -->
      <label class="switch admin_mode_block">
        <?php 
          if(!empty($wsb_credit_details['auto_nach_enabled']) && $wsb_credit_details['auto_nach_enabled'] == 1) { $str = "checked"; }
          else{ $str =  "";}
        ?>
        <input type="checkbox" name="auto_nach_enabled" id="auto_nach_enabled" <?php echo $str;?> >
        <span class="slider round"></span>
        <input type="hidden" name="hddn_auto_nach_enabled" id="hddn_auto_nach_enabled" value="<?php echo $wsb_credit_details['auto_nach_enabled'] ?? 0; ?>">
        <input type="hidden" name="hddn_old_auto_nach_enabled" id="hddn_old_auto_nach_enabled" value="<?php echo $wsb_credit_details['auto_nach_enabled'] ?? 0; ?>">
      </label>
    </div>
  </div>
  <div class="form-group required">
    <label class="col-sm-4 control-label" for="wsb_credit_comment">
      <?php echo $entry_comment; ?>
    </label>
    <div  class="col-sm-8">
      <textarea placeholder="Remarks" id="wsb_credit_comment" name="wsb_credit_comment" class="form-control" cols="30"></textarea>
    </div>
  </div>
</div>
<div class="col-sm-5">
  <div class="col-sm-12">
    <div class="customer_profile">
      <table class="table"> 
        <tr>
          <th class="col-sm-7">Current Status</th>
          <td class="col-sm-5">
              <?php echo $customer_profile['wsb_credit_status']; ?>

              <a class="button-default" style="background:#fff; float:right;" href="javascript:void(0)" data-id="<?php echo $customer_id;?>" onclick="getWsbCreditHistory(this)" toggle="tooltip" title="View WSB Credit History" data-original-title="View WSB Credit History"><u>View History</u></a>
          </td>
        </tr>
        <tr>
          <th>WSB Vintage (In Months)</th>
          <td><?php echo $customer_profile['wsb_vintage']; ?></td>
        </tr>
        <tr>
          <th>Credit Vintage (In Months)</th>
          <td><?php echo $customer_profile['wsb_credit_vintage']; ?></td>
        </tr>
        <tr>
          <th>Crif Score</th>
          <td><?php echo $customer_profile['crif_score']; ?></td>
        </tr>
        <tr>
          <th>Total Business</th>
          <td><?php echo $customer_profile['order_total']; ?></td>
        </tr>
        <tr>
          <th>Amount Approved</th>
          <td><?php echo $customer_profile['credit_activation_limit']; ?></td>
        </tr>
        <tr>
          <th>Current Limit</th>
          <td><?php echo $customer_profile['wsb_credit_current_limit']; ?></td>
        </tr>
        <tr>
          <th>Credit Business</th>
          <td><?php echo $customer_profile['total_credit_orders']; ?></td>
        </tr>
        <tr>
          <th>No of Credit Orders </th>
          <td><?php echo $customer_profile['count_credit_orders']; ?></td>
        </tr>
        <tr>
          <th>Avg Amt of credit Order</th>
          <td><?php echo $customer_profile['avg_wsb_credit_order_amount']; ?></td>
        </tr>
        <tr>
          <th>No Failed Credit Order</th>
          <td><?php echo $customer_profile['total_failed_credit_orders']; ?></td>
        </tr>
        <tr>
          <th>No of NACH Attempts</th>
          <td><?php echo $customer_profile['nach_attempts']; ?></td>
        </tr>
        <tr>
          <th>Successful Attempts</th>
          <td><?php echo $customer_profile['successfull_nach_attempts']; ?></td>
        </tr>
        <tr>
          <th>Success Ratio</th>
          <td><?php echo $customer_profile['nach_attempts_ratio']; ?></td>
        </tr>
        <tr>
          <th>Last NACH Success Date</th>
          <td><?php if( !empty($customer_profile['last_successfull_nach_date']) ) {
                    echo date('d M Y', strtotime($customer_profile['last_successfull_nach_date']) ); 
              }else{
                echo '-';
              }?>
          </td>
        </tr>
        <tr>
          <th>Last NACH Failed Date</th>
          <td><?php if( !empty($customer_profile['last_failed_nach_date']) ) {
                    echo date('d M Y', strtotime($customer_profile['last_failed_nach_date']) ); 
              }else{
                echo '-';
              }?>
          </td>
        </tr>
        <tr>
          <th>On Time repayment Orders</th>
          <td><?php echo $customer_profile['order_on_time_nach_recovery']; ?></td>
        </tr>
      </table>
      
    </div>
  </div>
</div>

<br /><br />
<div class="col-sm-12">
  <?php if(!empty($credit_ac_number)){ ?>
  <h4>Credit Application approved for A/c No.: <b><?php echo $credit_ac_number; ?></b></h4>
  <?php } ?>
  <div class="nach-details col-sm-12">
    <?php echo $nach_details_content; ?>
  </div>
</div>

<!--  View NAHC Schedule log history pop-up  -->
<div class="modal fade" id="wsb_credit-history" tabindex="-1" role="dialog" aria-labelledby="WSB-Credit-History">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="historyTab">WSB Credit history</h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Log Id</th>
              <th>Customer Id</th>
              <th>Status</th>
              <th>Credit Limit</th>
              <th>Crontab</th>
              <th>Day Before NACH Start</th>
              <th>Total Schedule Days</th>
              <th>Auto NACH Enabled</th>
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
<style type="text/css">
  .nach-details{
    border: solid 1px;
    padding: 5px;
    background-color: #f2f4f7;
  }

/* The switch - the box around the slider (CSS for Mode on/off Button) */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<script type="text/javascript">

  $('#customer_credit_application').click(function(){
    var url = 'index.php?route=sale/customer_credit_application/index&filter_customer_id=<?php echo $customer_id; ?>&token=<?php echo $token; ?>';

    window.open(url, '_blank');
  });

  $('#check-crontab-title').click(function(){

    var crontab = $('#nach_schedule_crontab').val();
    $.ajax({
      url: 'index.php?route=sale/customer/getCrontabStringMeaning&token=<?php echo $token; ?>',
      type: 'post',
      data: 'crontab='+crontab,
      success: function(json) {
        $('.crontab_title_check').html('('+json+')');
      }
    });
  });

  //To check Last NACH schedule date and total days(including all bank holidays etc) \to recover complete amount according to customer's config data 
  $('#check_nach_last_date').click(function(){
    var crontab                = $('#nach_schedule_crontab').val().trim();
    var days_before_nach_start = $('#days_before_nach_start').val().trim();
    var schedule_days_for_nach = $('#schedule_days_for_nach').val().trim();
    var err_msg = "";
    if(crontab.length <= 0){
      err_msg  = "Crontab String Must be Entered. ";
    }
    if(days_before_nach_start.length <= 0){
      err_msg += "'Days After NACH Starts' Must be Entered. ";
    }
    if(schedule_days_for_nach.length <= 0){
      err_msg += "'Total Days to complete NACH' Must be Entered. ";
    }

    if(err_msg.length <= 0){
      $.ajax({
        url: 'index.php?route=sale/customer/checkRecoveryDaysForNachRules&token=<?php echo $token; ?>',
        type: 'post',
        data: 'crontab='+crontab+'&days_before_nach_start='+days_before_nach_start+'&schedule_days_for_nach='+schedule_days_for_nach,
        success: function(json) {
          alert(json.trim());
        }
      });
    }else{
      alert(err_msg);
    }
  });

  function getWsbCreditHistory(obj){
    var customer_id = $(obj).data('id');
    $.ajax({
      url: 'index.php?route=sale/customer_credits/getWsbCreditHistory&token=<?php echo $token; ?>',
      type: 'post',
      data: 'customer_id='+customer_id,
      success: function(json) {
        data = JSON.parse(json);
        
        if(data.hasOwnProperty('error')){
          alert(data.error);
        }else{
          
          if(data.length > 0 ){
            $('#history_body').html('');
            $('.error-msg').html('');
            var i = 1;
            var row = '';
            $(data).each( function( key, element ) {

                row  += '<tr>';
                row     +=    '<td>' + i  +'</td>';
                row     +=    '<td>' + element.log_id + '</td>';
                row     +=    '<td>' + element.customer_id  + '</td>';
                row     +=    '<td>' + element.status  + '</td>';
                row     +=    '<td>' + element.credit_limit + '</td>';
                row     +=    '<td>' + element.nach_schedule_crontab + '</td>';
                row     +=    '<td>' + element.days_before_nach_start + '</td>';
                row     +=    '<td>' + element.schedule_days_for_nach + '</td>';
                row     +=    '<td>' + element.auto_nach_enabled + '</td>';
                row     +=    '<td>' + element.comment + '</td>';
                row     +=    '<td>' + element.date_added + '</td>';
                row     +=    '<td>' + element.username + ' ('+ element.user_id +')' + '</td>';
                row     += '</tr>';
                i++;
            });
            $('#history_body').html(row);
          }else{
            $('#history_body').html('');
            $('.error-msg').html('<p class="alert alert-danger">No historical records found for this customer\'s WSB Credit change log.<p>');
          }
          $('#wsb_credit-history').modal('show');
        }
      }
    }); 

    
  }
</script>