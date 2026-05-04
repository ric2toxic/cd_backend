<?php if(isset($one_page_checkout_payment_method) && $one_page_checkout_payment_method=='one_page_checkout_payment_method'){ ?>
<div class="payment_loading"></div>
<input type="hidden" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm button-cash-delivery" data-loading-text="<?php echo $text_loading; ?>" />
<?php } else{ ?>
	<div class="buttons">
		<div class="pull-left">
			<div class="continue back_collapse back-cash-delivery"><?php echo $button_back; ?> </div>
		</div>
	  <div class="pull-right">
		<input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm button-cash-delivery" data-loading-text="<?php echo $text_loading; ?>" />
	  </div>
	</div>
<?php } ?>
<script type="text/javascript"><!--
$('.button-confirm').on('click', function() {
	$.ajax({
		type: 'get',
		url: 'index.php?route=payment/cod/confirm',
		cache: false,
		beforeSend: function() {
			$('.button-confirm').button('loading');
		},
		complete: function() {
			$('.button-confirm').button('reset');
		},
		success: function() {
			location = '<?php echo $continue; ?>';
		}
	});
});
//--></script>
