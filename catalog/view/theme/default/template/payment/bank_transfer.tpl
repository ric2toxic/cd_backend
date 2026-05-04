<?php if(isset($one_page_checkout_payment_method) && $one_page_checkout_payment_method=='one_page_checkout_payment_method'){ ?>
<div class="payment_loading"></div>
<input type="hidden" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" data-loading-text="<?php echo $text_loading; ?>" />
<?php } else{ ?>
	<h2><?php echo $text_instruction; ?></h2>
	<p><b><?php echo $text_description; ?></b></p>
	<div class="well well-sm">
		<p><?php echo $bank; ?></p>
		<p><?php echo $text_payment; ?></p>
	</div>
	<div class="buttons">
		<div class="pull-left">
			<div class="continue back_collapse back-cash-delivery"><?php echo $button_back; ?></div>
		</div>
	  <div class="pull-right">
		<input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button-confirm" data-loading-text="<?php echo $text_loading; ?>" />
	  </div>
	</div>
<?php } ?>
<script type="text/javascript"><!--
$('.button-confirm').on('click', function() {
	$.ajax({
		type: 'get',
		url: 'index.php?route=payment/bank_transfer/confirm',
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
