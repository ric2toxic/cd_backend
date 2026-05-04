<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
  <?php if( empty($error_msg) ) { ?>
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" form="form-customer" id="cs-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
      <span style="margin-left: 50px;font-size: large;">Customer ID : <b><?php echo $customer_id; ?></b></span>
      <span>
      <button type="button" id="customer_credit_application" style="margin: 0px 40px 0px 5px;" data-toggle="tooltip" title="Click to move on Customer Credit Application Page." class="btn btn-primary pull-right" ><i class="fa fa-file-text" aria-hidden="true"></i> Credit Application</button>
    </span>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-customer_credit" class="form-horizontal">
          <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" />
          <input type="hidden" name="update_for" id="update_for" value="" />
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-wsb_credit" data-toggle="tab">WSB Credit</a></li>
            <li><a href="#tab-neo_growth" data-toggle="tab">Neo Growth</a></li>
            <li><a href="#tab-lazy_pay" data-toggle="tab">Lazy Pay</a></li>
            <li><a href="#tab-rbl_credit" data-toggle="tab">RBL Credit</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-wsb_credit">
              <?php echo $tab_wsb_credit_content; ?>
            </div>
            <div class="tab-pane" id="tab-neo_growth">
              <?php echo $tab_neogrowth_content; ?>
            </div>
            <div class="tab-pane" id="tab-lazy_pay">
              <?php echo $tab_laypay_content; ?>
            </div>

            <div class="tab-pane" id="tab-rbl_credit">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="rbl_credit_status">RBL Credit Status</label>
                <div class="col-sm-8">
                  <select name="rbl_credit_status" id="rbl_credit_status" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $rbl_credit_status; ?>">
                    <?php if ($rbl_credit_status) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php
    }else{
      echo '<b>'.$error_msg . '</b>';
    }
    ?>
  </div>
</div>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
$('.date').datetimepicker({
    pickTime: false,
    minDate: new Date()
});


$(document).ready(function(){
  $('#auto_nach_enabled').click(function(){
    if($(this).prop("checked") == true){
        $('#hddn_auto_nach_enabled').val(1);
    } else {
        $('#hddn_auto_nach_enabled').val(0);
    }
  });
});

$('#cs-submit-button').on('click', function(){
  
    //WSB Credit details
    var wsb_credit_limit_old  = $('#wsb_credit_payment_limit').attr('data-old-value');
    var wsb_credit_limit      = $('#wsb_credit_payment_limit').val();

    var wsb_credit_status_old = $('#wsb_credit_payment_status').attr('data-old-value');
    var wsb_credit_status     = $('#wsb_credit_payment_status').val();

    var nach_schedule_crontab_old = $('#nach_schedule_crontab').attr('data-old-value');
    var nach_schedule_crontab     = $('#nach_schedule_crontab').val();

    var days_before_nach_start_old = $('#days_before_nach_start').attr('data-old-value');
    var days_before_nach_start     = $('#days_before_nach_start').val();

    var schedule_days_for_nach_old = $('#schedule_days_for_nach').attr('data-old-value');
    var schedule_days_for_nach     = $('#schedule_days_for_nach').val();

    var auto_nach_enabled          = $('#hddn_auto_nach_enabled').val();
    var auto_nach_enabled_old      = $('#hddn_old_auto_nach_enabled').val();

    //Neogrowth Details
    var neogrowth_status_old = $('#neogrowth_credit_status').attr('data-old-value');
    var neogrowth_status     = $('#neogrowth_credit_status').val();
      
    var neogrowth_account_number_old = $('#neogrowth_account_number').attr('data-old-value');
    var neogrowth_account_number     = $('#neogrowth_account_number').val();

    var neogrowth_registration_number_old = $('#neogrowth_registration_number').attr('data-old-value');
    var neogrowth_registration_number     = $('#neogrowth_registration_number').val();

    //Lazypay Details
    var lazypay_status_old  = $('#lazypay_status').attr('data-old-value');
    var lazypay_status      = $('#lazypay_status').val();
      
    var lazypay_email_old   = $('#lazypay_email').attr('data-old-value');
    var lazypay_email       = $('#lazypay_email').val();

    var lazypay_mobile_old  = $('#lazypay_mobile').attr('data-old-value');
    var lazypay_mobile      = $('#lazypay_mobile').val();

     //RBL Credit
    var rbl_credit_old   = $('#rbl_credit_status').attr('data-old-value');
    var rbl_credit       = $('#rbl_credit_status').val();

    $('#update_for').val('');
    if(
      wsb_credit_limit       != wsb_credit_limit_old || 
      wsb_credit_status      != wsb_credit_status_old ||
      nach_schedule_crontab  != nach_schedule_crontab_old || 
      days_before_nach_start != days_before_nach_start_old || 
      schedule_days_for_nach != schedule_days_for_nach_old ||
      auto_nach_enabled      != auto_nach_enabled_old
    ){
      $('#update_for').val('wsb_credit');
      if(auto_nach_enabled == 1){
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
            async: false,
            data: 'crontab='+crontab+'&days_before_nach_start='+days_before_nach_start+'&schedule_days_for_nach='+schedule_days_for_nach,
            success: function(json) {
              if(json.trim().length > 0){
                if(!confirm(json.trim() + '??')){
                  $('#update_for').val('');
                  return false;
                }
              }
            }
          });
        }else{
          alert(err_msg);
        }
      }
    }else if(
        neogrowth_status_old != neogrowth_status ||
        neogrowth_account_number_old != neogrowth_account_number ||
        neogrowth_registration_number_old != neogrowth_registration_number
    ){
      $('#update_for').val('neogrowth');
    }else if(
        lazypay_status_old != lazypay_status ||
        lazypay_mobile_old != lazypay_mobile ||
        lazypay_email_old  != lazypay_email
    ){
      $('#update_for').val('lazypay');
    }else if( rbl_credit_old != rbl_credit ){
      $('#update_for').val('rbl_credit');
    }

    if($('#update_for').val().length == 0){
      return false;
    }

    var is_valid = validateForm();
    
    //If validation success 
    if(is_valid){
      $('#form-customer_credit').submit();
    }
    return is_valid;

});

//Function to validate form value before submitting form
function validateForm(){
  var is_valid = true;
  var wsb_credit_limit              = $.trim($('#wsb_credit_payment_limit').val());
  var wsb_credit_status             = $.trim($('#wsb_credit_payment_status').val());
  var wsb_credit_form_type          = $.trim($('#hddn_form_type').val());
  var wsb_credit_comment            = $.trim($('#wsb_credit_comment').val());
  var neogrowth_account_number      = $.trim($('#neogrowth_account_number').val());
  var neogrowth_registration_number = $.trim($('#neogrowth_registration_number').val());
  var lazypay_mobile                = $.trim($('#lazypay_mobile').val());
  var lazypay_email                 = $.trim($('#lazypay_email').val());

  var update_for = $('#update_for').val();
  if(update_for == 'wsb_credit'){
    if( wsb_credit_status == 'ENABLED' && (wsb_credit_limit == '' || wsb_credit_limit <= 0) ){
      alert('WSB Credit approved limit must not be Empty or Less Then 0.');
      is_valid = false;
    }else if( wsb_credit_form_type == 'edit' && wsb_credit_comment.length == 0 ) {
      alert('WSB Credit edit reason remark/comment must entered.');
      is_valid = false;
    }
  }else if(update_for == 'neogrowth'){
    if(neogrowth_account_number == ''){
      alert('NeoGrowth account number must not be Empty.');
      is_valid = false;
    }else if(neogrowth_registration_number == '' ){
      alert('NeoGrowth registration number must not be Empty.');
      is_valid = false;
    }
  }else if(update_for == 'lazypay'){
    if(lazypay_mobile == '' ){
      alert('Lazypay Mobile must not be Empty.');
      is_valid = false;
    }else if(!lazypay_mobile.match('[0-9]{10}'))  {
      alert("Please enter 10 digit mobile number for Lazypay.");
      is_valid = false;
    }else if(lazypay_email == ''){
      alert('Lazypay Email Id must not be Empty.');
      is_valid = false;
    }else if(!email_validation(lazypay_email)){
      alert('Lazypay Email Id is not valid.');
      is_valid = false;
    } 
  }

  return is_valid;
}
</script> 
<?php echo $footer; ?>