<form  action="<?php echo $action;?>" method="post"  id="payment">
    <input name="order_id" type="hidden" value="<?php echo $order_id;?>" />
    <input name="order_no" type="hidden" value="<?php echo $order_no;?>" />
	<input name="transaction_id" type="hidden" value="<?php echo $transaction_id;?>" />
    <input name="buyer_registration_number" type="hidden" value="<?php echo $buyer_registration_number;?>" />
    <input name="transaction_amount" type="hidden" value="<?php echo $transaction_amount;?>" />
    <input name="session_id" type="hidden" value="<?php echo $session_id;?>" />
    <input name="cart_details" type="hidden" value="<?php echo $cart_details;?>" />
    <input name="request_type" type="hidden" value="<?php echo $request_type;?>"  />
    <input name="transaction_datetime" type="hidden" value="<?php echo $transaction_datetime;?>" />
    <input name="lpTxnId" id="lpTxnId" type="hidden" value="" />
    <input name="preAuthToken" id="preAuthToken" type="hidden" value="" />
    <input name="autoDebitToken" id="autoDebitToken" type="hidden" value="" />
    <input name="customParams" id="customParams" type="hidden" value="" />

    <?php if(isset($success) && $success ) { ?>
        <div class="buttons" style="margin:10px;">

         <p id="error-msg" style="color:red"></p>

            <p>
               <?php echo sprintf($text_charge, $transaction_amount, $dueDate); ?>
            </p>

            <p><?php echo $text_otp_msg; ?></p>

            <p>
                <input name="otp" id="otp" type="text" value="" class="form-control" placeholder="Enter OTP" />
            </p>
          <div class="otp_main">
            <div style="float: left; padding-left:10px; font-weight: bold"><?php echo $text_otp_expire; ?> :&nbsp;&nbsp;</div>  
            <div style="padding-left:10px; font-weight: bold" class="countdown"></div>
            <div id="vlink" style="padding-left:10px;"></div>
          </div>

            <p class="pull-right" style="display: none">
                <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" />
            </p>

            <div class="pull-left" style="margin-top: 20px;">
             <?php echo $text_lazy_tc_1; ?> <a href="http://www.lazypay.in/tnc.html" target="_blank" title="LazyPay terms & conditions">T&C</a> <?php echo $text_lazy_tc_2; ?>
            </div>
            
            <div class="pull-left" style="margin-top: 10px;">
              <b><?php echo $text_lazypay_features; ?></b>
              <ul>
                <li><?php echo $text_lazypay_features_1; ?></li>
                <li><?php echo $text_lazypay_features_2; ?></li>
                <li><?php echo $text_lazypay_features_3; ?></li>
                <li><?php echo $text_lazypay_features_4; ?></li>
              </ul>
            </div>

        </div>

    <?php } else { ?>
        <div class="buttons">
            
            <div class="pull-left" style="color:red;">
                <?php echo $msg;?>
            </div>

        </div>
    <?php } ?>

</form>
<script type="text/javascript"><!--
$('#lazypay_submit').on('click', function() {
    
    if( $('#otp').val().trim() == '' ) {

         $('#alert_body').html('<?php echo $error_otp; ?>');
          $('#alert_popup').modal({
              backdrop: 'static',
              keyboard: false
          });
     return false; 
    }
    
    $.ajax({
        type: 'post',
        url : 'index.php?route=payment/lazypay/verify_otp',
        data: $('#payment').serialize(),
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            $('#button-confirm').button('loading');
        },
        complete: function() {
            //$('#button-confirm').button('reset');
        },
        success: function(data) {
          if(data.success == 1){ 
            $('#payment').submit();
            $('.otp_main').hide();
          }else{
            $('#error-msg').html(data.message);
            $('#button-confirm').button('reset');
          }   
        }
    });
});

function showTimer()
{
    var timer2 = "3:00";
    var interval = setInterval(function() {
      var timer = timer2.split(':');
      //by parsing integer, I avoid all extra string processing
      var minutes = parseInt(timer[0], 10);
      var seconds = parseInt(timer[1], 10);
      --seconds;
      minutes = (seconds < 0) ? --minutes : minutes;

      if (minutes < 0) {
         clearInterval(interval);
         var vlink = '<a href="javascript:void(0)"><?php echo $text_otp_resend; ?></a>'; 
         $('#vlink').html(vlink);
       } 

      seconds = (seconds < 0) ? 59 : seconds;
      seconds = (seconds < 10) ? '0' + seconds : seconds;
        
      $('.countdown').html(minutes + ':' + seconds);
      timer2 = minutes + ':' + seconds;
      
      if (minutes < 0) {
         $('.countdown').html('');
      } 
      
    }, 1000);
}

showTimer();

$('#vlink').click(function(){
    $('#vlink').html('');
    $('#error-msg').html('');
    $('#otp').val('');
    showTimer();

    $.ajax({
        type: 'post',
        url : 'index.php?route=payment/lazypay/resend_otp',
        data: $('#payment').serialize(),
        dataType: 'json',
        cache: false,
        beforeSend: function() {},
        complete: function() {},
        success: function(data) {
          console.log($data);
          console.log(data.transaction_id);
          console.log(data.lpTxnId);
          if(data.success == 1){ 
            $('#transaction_id').val(data.transaction_id);
            $('#lpTxnId').val(data.lpTxnId);
          }else{
            $('#error-msg').html(data.message);
          } 
        },
        error:function() {
            $('#error-msg').html('Lazypay API error');
        }
    }); 
        
})


//--></script>

