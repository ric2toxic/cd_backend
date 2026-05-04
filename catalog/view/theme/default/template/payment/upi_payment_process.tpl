<?php echo $header; ?>
<div class='upi_pay_process_img' id='' style='background:rgba(255, 255, 255, 0) url("<?php echo STATIC_CONTENT_URL?>Preloader_2.gif") no-repeat scroll 0 0;'>

</div>
<div class='upi_pre_pay_text' id='text_msg'>
  <?php echo $text_message;?> 
</div>
<form action="<?php echo $action;?>" method="post" id="upi_payment">
  <input type="submit" value="" id="button-confirm" class="btn btn-primary button-confirm" />
  <input name="action_query" id="action_query" type="hidden" value="<?php echo $action_query;?>" />
  <input name="upi_txn_id" id="upi_txn_id" type="hidden" value="<?php echo $upi_txn_id;?>" />
  <input name="amount" id="amount" type="hidden" value="<?php echo $amount;?>" />
    <input name="payer_vpa" type="hidden" value="<?php echo $payer_vpa;?>" />
    <input name="initiate_response" type="hidden" value="<?php echo $initiate_response;?>" />
</form>
<!--<form action="<?php echo $action_query;?>" method="post" id="upi_payment_query">
  <input type="submit" value="" id="button-query" class="btn btn-primary button-confirm" />
  <input name="upi_txn_id" type="hidden" value="<?php echo $upi_txn_id;?>" />
</form>-->
<script>
  $(document).ready(function() {
    document.title = 'Payment';
    $("#button-confirm").hide();

      var startTimer = new Date();
      var action_query = $("#action_query").val();
      var upi_txn_id = $("#upi_txn_id").val();
      var req_time_out = 1000*60*4;//1000*60*3.50; //for live 3 mins;

      // -----------Require below code if taking vpa as input in offline mode---------------
      // var req_time_out = 0;
      // if(is_pay_now == 'true') {
      //  req_time_out = 1000*3; //for live 3 mins
      // }esle {
      //  req_time_out = 0;
      // }
      var req_complete = 1;
      var query_interval = setInterval(function() {
        if(req_complete == 1) {
          
          req_complete = 0; 
          //Query Transaction Status
          $.ajax({
              url: action_query,
              type: 'post',
              data: 'upi_txn_id='+upi_txn_id+'&ajax='+'1',
              dataType: 'json'
          }).done(function (json) {
              req_complete = 1;
              var total = 0;
              if (json['responseStatus'] == 'completed' || json['responseStatus'] == 'rejected' || json['responseStatus'] == 'failed' || json['responseStatus'] == 'expired') {
                document.getElementById('upi_payment').submit();
                clearInterval(query_interval);
              }
          });

        }
      }, 3000);

    setTimeout(function() {
      var passed = new Date() - startTimer;
      document.getElementById('upi_payment').submit();

    }, req_time_out);// 1000*60*3
  });
</script>