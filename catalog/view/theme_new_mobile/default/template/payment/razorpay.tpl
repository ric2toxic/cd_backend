<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  var razorpay_options = {
    key: "<?php echo $key_id; ?>",
    amount: "<?php echo $total; ?>",
    name: "<?php echo $name; ?>",
    description: "Order # <?php echo $merchant_order_id; ?>",
    method: {netbanking: true, wallet: false},
    currency: "<?php echo $currency_code; ?>",
<?php if( $display_currency != 'INR') { ?>
    display_currency: "<?php echo $display_currency;  ?>",
<?php } ?>
    display_amount: "<?php echo $display_amount; ?>",
    prefill: {
      name:"<?php echo $card_holder_name; ?>",
      email: "<?php echo $email; ?>",
      contact: "<?php echo $phone; ?>"
    },
    notes: {
      opencart_order_id: "<?php echo $merchant_order_id; ?>"
    },
    modal: {
      ondismiss: function(){
        window.location.reload();
      }
    },
    handler: function (transaction) {
        document.getElementById('razorpay_payment_id').value = transaction.razorpay_payment_id;
        document.getElementById('razorpay-form').submit();
    }
  };
  var razorpay_submit_btn, razorpay_instance;

  function razorpaySubmit(el){
    if(typeof Razorpay == 'undefined'){
      setTimeout(razorpaySubmit, 200);
      if(!razorpay_submit_btn && el){
        razorpay_submit_btn = el;
        el.disabled = true;
        el.value = 'Please wait...';  
      }
    } else {
      if(!razorpay_instance){
        razorpay_instance = new Razorpay(razorpay_options);
        if(razorpay_submit_btn){
          razorpay_submit_btn.disabled = false;
          razorpay_submit_btn.value = "<?php echo $button_confirm; ?>";
        }
      }
      razorpay_instance.open();
      $('.payment_loading').hide();
    }
  }

</script>
<form name="razorpay-form" id="razorpay-form" action="<?php echo $return_url; ?>" method="POST">
  <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
  <input type="hidden" name="merchant_order_id" id="merchant_order_id" value="<?php echo $merchant_order_id ?>"/>
</form>

<?php if(isset($one_page_checkout_payment_method) && $one_page_checkout_payment_method=='one_page_checkout_payment_method'){ ?>
<!--<input type="hidden" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" />-->
<div class="payment_loading"></div>
<script type="text/javascript">
  window.onload = razorpaySubmit(this);
</script>
<?php } else{ ?>
  <div class="buttons">
    <div class="pull-left">
      <div class="continue back_collapse back-cash-delivery"><?php echo $button_back; ?></div>
    </div>
    <div class="pull-right">
      <input type="submit" onclick="razorpaySubmit(this);" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
<?php } ?>
