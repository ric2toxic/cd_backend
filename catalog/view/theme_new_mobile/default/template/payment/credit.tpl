<form action="<?php echo $action;?>" method="post"  id="payment">
    <input name="partner_id" type="hidden" value="<?php echo $partner_id;?>" />
    <input name="order_id" type="hidden" value="<?php echo $order_id;?>" />
    <input name="transaction_id" type="hidden" value="<?php echo $order_id;?>" />
    <input name="buyer_registration_number" type="hidden" value="<?php echo $buyer_registration_number;?>" />
    <input name="transaction_amount" type="hidden" value="<?php echo $transaction_amount;?>" />
    <input name="session_id" type="hidden" value="<?php echo $session_id;?>" />
    <input name="cart_details" type="hidden" value="<?php echo $cart_details;?>" />
    <input name="callback_url" type="hidden" value="<?php echo $callback_url;?>" />
    <input name="request_type" type="hidden" value="<?php echo $request_type;?>"  />
    <input name="transaction_datetime" type="hidden" value="<?php echo $transaction_datetime;?>" />
    <input name="success_url" type="hidden" value="<?php echo $success_url;?>" />
    <input name="failure_url" type="hidden" value="<?php echo $failure_url;?>" />
    <input name="checksum_hash" type="hidden" value="<?php echo $checksum_hash;?>" />
    <?php if(isset($one_page_checkout_payment_method) && $one_page_checkout_payment_method=='one_page_checkout_payment_method'){ ?>
    <!--<input type="hidden" value="<?php // echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" />-->
    <div class="payment_loading"></div>
    <script type="text/javascript">
        document.getElementById('payment').submit();
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


