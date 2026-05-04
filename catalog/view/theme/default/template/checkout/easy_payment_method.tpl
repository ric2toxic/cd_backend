<?php if ($error_warning) { ?>
<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php } ?>
   <h3><i class="fa fa-credit-card" aria-hidden="true"></i> <?php echo $text_checkout_payment_method; ?></h3>
			  
<?php if ($payment_methods) { ?>
<p><?php echo $text_payment_method; ?></p>
<?php foreach ($payment_methods as $payment_method) { ?>
<div class="radio">
  <label>
    <?php /* if ($payment_method['code'] == $code || !$code) { ?>
    <?php $code = $payment_method['code']; ?>
    <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" title="<?php echo $payment_method['title']; ?>" checked="checked" />
    <?php } else { */?>
    <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" title="<?php echo $payment_method['title']; ?>" />
    <?php  // } ?>
    <?php echo $payment_method['title']; ?>
  </label>
</div>
<?php } ?>
<?php } ?>

<?php if(!$CODServiceability && !empty($this->session->data['shipping_zone_id'])){ ?>
    <div class="alert alert-info" style="color:#31708f; padding: 5px;">
        We are sorry but Cash on Delivery option is not available for your address.<br/>Please select other payment methods to enjoy further discount(s)
    </div>
    <div class=""></div>
<?php }?>
<?php if (isset($show_offers) && $show_offers == 1) { ?>
<div class="payment_method_offers">
  <h3>Offers</h3>
  <div class="DiscountText discount-label colorbox" html-data="discount">
 <!-- 
   <input type="radio" name="offers"  id="checkbox-2-1" /> 
 -->
   
   
    <label for="checkbox-2-1"><span><?php echo $text_discount; ?></span></label>
  </div>
  <?php /*  if(isset($free_shipping)){ ?>
  <div class="DiscountText discount-label colorbox" html-data="free_shipping">
    <input type="radio" name="offers" id="checkbox-2-2" />
    <label for="checkbox-2-2"><span><?php echo $free_shipping; ?></span></label>
  </div>
  <?php  } */  ?>
</div>
<?php } ?>
<input type="hidden" id="getOffer" name="getOffer" value="discount" />

<div class="bank_transfer_detail" style="display: none;">
  <h3><?php echo $text_instruction; ?></h3>
  <p><b><?php echo $text_description; ?></b></p>
  <div class="well well-sm">
    <p><?php echo $bank_transfer; ?></p>
    <p><?php echo $text_payment; ?></p>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('input[name=\'payment_method\']').on('change',function(){
      if($(this).val()=='bank_transfer'){
        $('.bank_transfer_detail').css("display","block");
      }else{
        $('.bank_transfer_detail').css("display","none");
      }
    });


    $(".DiscountText").click(function(){
      $("div.DiscountText").removeClass("green").addClass("colorbox");
      $(this).removeClass("colorbox").addClass("green");
      $("#getOffer").val($(this).attr("html-data"));
    });
  });
</script>