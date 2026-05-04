<form action="<?php echo $action;?>" method="post"  id="payment">
<input name="merchantAccessKey" type="hidden" value="<?php echo $citrus_access_key;?>" />
<input name="merchantTxnId" type="hidden" value="<?php echo $citrus_merchant_trans_id;?>" />
<input name="addressState" type="hidden" value="<?php echo $state;?>" />
<input name="addressCity" type="hidden" value="<?php echo $city;?>" />
<input name="addressStreet1" type="hidden" value="<?php echo $addr1;?>" />
<input name="addressCountry" type="hidden" value="<?php echo $country;?>" />
<input name="addressZip" type="hidden" value="<?php echo $zip;?>" />
<input name="firstName" type="hidden" value="<?php echo $firstname;?>"  />
<input name="lastName" type="hidden" value="<?php echo $lastname;?>" />
<input name="phoneNumber" type="hidden" value="<?php echo $phone;?>" />
<input name="email" type="hidden" value="<?php echo $email;?>" />
<input name="paymentMode" type="hidden" value="NET_BANKING" />
<input name="returnUrl" type="hidden" value="<?php echo $redir_url;?>" />
<input name="notifyUrl" type="hidden" value="<?php echo $notify_url;?>" />
<input name="orderAmount" type="hidden" value="<?php echo $total;?>" />
<input type="hidden" name="reqtime" value="<?php echo time()*1000; ?>" />
<input type="hidden" name="secSignature" value="<?php echo $secSignature;?>" />
<input type="hidden" name="currency" value="<?php echo $currency;?>" />
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


