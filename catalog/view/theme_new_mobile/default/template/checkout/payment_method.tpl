<?php if ($error_warning) { ?>
<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php } ?>

<?php if ($payment_methods) { ?>
	<div class="row">
		<div class="paymentBox">
			<p><?php echo $text_payment_method; ?></p>
			<?php foreach ($payment_methods as $payment_method) { ?>
			<div class="radio">
			  <label>
				<?php if ($payment_method['code'] == $code || !$code) { ?>
				<?php $code = $payment_method['code']; ?>
				<input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" checked="checked" />
				<?php } else { ?>
				<input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" />
				<?php } ?>
				<?php echo $payment_method['title']; ?>
				<?php if ($payment_method['terms']) { ?>
				(<?php echo $payment_method['terms']; ?>)
				<?php } ?>
			  </label>
			</div>
			<?php } ?>
		</div>

        <?php if (!$is_dropshipper) { 		
			if($store_id != INTERNATIONAL_STORE_ID){
			?>
		<div class="payment_method_offers">
			<h3>Offers<span> (click on offer to apply) </span></h3>
			<div class="DiscountText discount-label colorbox" html-data="discount">
				<input type="radio" name="offers"  id="checkbox-2-1" />
				<label for="checkbox-2-1"><span><?php echo $text_discount; ?></span></label>
			</div>
			<?php if(isset($free_shipping)){ ?>
				<div class="DiscountText discount-label colorbox" html-data="free_shipping">
					<input type="radio" name="offers" id="checkbox-2-2" />
					<label for="checkbox-2-2"><span><?php echo $free_shipping; ?></span></label>
				</div>
			<?php } ?>
		</div>
        <?php 
	}
        } ?>
	</div>
<?php } ?>
<div style="clear:both;"></div>
<p><strong><?php echo $text_comments; ?></strong></p>
<p>
  <textarea name="comment" rows="8" class="form-control"><?php echo $comment; ?></textarea>
</p>
<input type="hidden" id="getOffer" name="getOffer" value="discount" />
<?php if ($text_agree) { ?>
<div class="buttons">
	<div class="row">
		<div class="col-sm-12">
			<div class="pull-left">
				<?php echo $text_agree; ?>
				<?php if ($agree) { ?>
				<input type="checkbox" name="agree" value="1" checked="checked" class="term_agree"/>
				<?php } else { ?>
				<input type="checkbox" name="agree" value="1" class="term_agree"/>
				<?php } ?>
				&nbsp;
			</div>
			<div class="term-condition-error error"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<?php if($popup == false){?>
			<div class="pull-left back_button_box">
				<div class="continue back_collapse back-payment-method"><span><?php echo $button_back; ?></span></div>
			</div>
			<?php } ?>
			<div class="pull-right continue_button_box">
				<input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" data-loading-text="<?php echo $text_loading; ?>" class="button-payment-method" />
			</div>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary button-payment-method" />
  </div>
</div>
<?php } ?>
<script>
$(document).ready(function(){
	$(".DiscountText").click(function(){
		$("div.DiscountText").removeClass("green").addClass("colorbox");
		$(this).removeClass("colorbox").addClass("green");
		$("#getOffer").val($(this).attr("html-data"));
	});

	$('.button-payment-method').click(function(){
		if($('.term_agree').is(':checked')) {
			$('.checkout_confirm').show();
			//$('.checkout-pages-block').removeClass('col-sm-8');
			//		$('.order-summary-block').hide();
			//$('.checkout-pages-block').addClass('col-sm-12');
			$('.checkout_payment_method').hide();
			$('.checkout_payment_address').hide();
			$('.new_shipping_address').hide();
			$("html, body").animate({scrollTop: 0});
		}
	});

	$('.back-payment-method').click(function(){
//        $('.account_billing_detail').hide();
//        $('.new_shipping_address').hide();
//        $('.checkout_payment_address').show();
//        $('.checkout_payment_method').hide();
//        $('.checkout_confirm').hide();

		$('.checkout_step1').show();
		$('.checkout_step2').hide();
		$('.checkout_step3').hide();
		$('.checkout_step4').hide();
		if($('#payment-existing #checkbox_same_ship_address').is(':checked')){
			$('.account_billing_detail').hide();
			$('.new_shipping_address').hide();
			$('.checkout_payment_address').show();
			$('.checkout_payment_method').hide();
			$('.checkout_confirm').hide();
		}else{
			$('.account_billing_detail').hide();
			$('.new_shipping_address').show();
			$('.checkout_payment_address').hide();
			$('.checkout_payment_method').hide();
			$('.checkout_confirm').hide();
		}
		$("html, body").animate({scrollTop: 0});
	});
});
</script>
