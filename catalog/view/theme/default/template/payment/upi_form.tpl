<form action="<?php echo $action;?>" method="post"  id="payment">

    <input name="order_id" type="hidden" value="<?php echo $order_id;?>" />
    <input name="amount" type="hidden" value="<?php echo $amount;?>" />
    <input name="total" type="hidden" value="<?php echo $total;?>" />
    <input name="order_no" type="hidden" value="<?php echo $order_no;?>" />
    <input name="currency_code" type="hidden" value="<?php echo $currency_code;?>" />
    <input name="currency_value" type="hidden" value="<?php echo $currency_value;?>" />
    <input name="upi_txn_id" type="hidden" value="<?php echo $upi_txn_id;?>" />
    <input name="payer_vpa" type="hidden" value="<?php echo $payer_vpa;?>" />
    <input name="initiate_response" id="initiate_response" type="hidden" value="<?php echo $initiate_response;?>" />


    <?php if(isset($one_page_checkout_payment_method) && $one_page_checkout_payment_method=='one_page_checkout_payment_method'){ ?>
    <!--<input type="hidden" value="<?php // echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" />-->
    <div class="payment_loading"></div>
    <script type="text/javascript">
        document.getElementById('payment').submit();
        //alert($("#initiate_response").val());
    </script>
    <?php } else{ ?>
     <div class="buttons">
         <div class="pull-left">
             <div class="continue back_collapse back-cash-delivery"><?php echo $button_back; ?></div>
         </div>
       <div class="pull-right">
         <input type="submit" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" />
       </div>
     </div>
    <?php } ?>
</form>