<div class="row tabs hidden edit_order_payment">	
	<table  class="table table-bordered payment_history_<?php echo $order['order_id']; ?>">
		<thead>
		  	<tr>
		    	<td class="text-center"> <?php echo $text_payment_merchant_txn_id; ?> </td>
				<td class="text-center"> <?php echo $text_payment_link; ?> </td>
		    	<td class="text-center"> <?php echo $text_payment_amount; ?> </td>
		    	<td class="text-center"> <?php echo $text_payment_gateway; ?> </td>
				<td class="text-center"> <?php echo $text_payment_mode; ?> </td>
			    <td class="text-center"> <?php echo $text_payment_status; ?> </td>
				<td class="text-center"> <?php echo $text_payment_txn_date_time; ?> </td>
			    <td class="text-center"> <?php echo $text_payment_date_added; ?> </td>
			    <td class="text-center"> <?php echo $text_payment_user; ?> </td>
				<td class="text-center"> <?php echo $text_payment_reference; ?> </td>
				<td class="text-center"> <?php echo $text_payment_action; ?> </td>
		  	</tr>
		</thead>
		<tbody>
		<?php 
			foreach($order['payment_history']['result'] as $payment_history) { 
				if($payment_history['successfull']){
		?>
				  	<tr>
						<td class="text-center">
							<span class="merchant_txn_id_<?php echo $payment_history['payment_id']; ?> common_show"><?php echo $payment_history['merchant_txn_id']; ?></span>
							<input type="text" 
								   name="merchant_txn_id" 
								   class="form-control hidden common_hidden" 
								   id="merchant_txn_id_<?php echo $payment_history['payment_id']; ?>" 
								   value="<?php echo $payment_history['merchant_txn_id']; ?>">
						</td>	
						<td class="text-center order_list_comment">
							<?php if(!($payment_history['payment_gateway'] == 'upi')) { ?>
								<a href="<?php echo $payment_history['payment_link']; ?>" target="_blank" > 
									<?php echo $payment_history['payment_link']; ?> 
								</a>
							<?php } else { ?> 
								<?php echo $payment_history['payment_link']; ?>
							<?php }?>
						</td>
						<td class="text-center">
			                <span><?php echo $this->currency->format($payment_history['amount'],'INR','1',true); ?></span>
			            </td>
						<td class="text-center">
							<span class="payment_gateway_<?php echo $payment_history['payment_id']; ?> common_show"><?php echo $payment_history['payment_gateway']; ?></span>
							<select class="form-control hidden common_hidden" 
									id="payment_gateway_<?php echo $payment_history['payment_id']; ?>">
								<?php foreach($order['enum_values'] as $enums) { ?>
									<option value="<?php echo $enums; ?>" <?php echo ($enums==$payment_history['payment_gateway']) ? 'selected' : '' ;?>><?php echo $enums; ?></option>
								<?php } ?>
							</select>
						</td>
						<td class="text-center order_list_comment">
							<span class="payment_mode_<?php echo $payment_history['payment_id']; ?> common_show"><?php echo $payment_history['payment_mode']; ?></span>
							<input type="text" 
								   name="payment_mode" 
								   id="payment_mode_<?php echo $payment_history['payment_id']; ?>" 
								   class="form-control hidden common_hidden" 
								   value="<?php echo $payment_history['payment_mode']; ?>">
						</td>
						<td class="text-center text-uppercase"><?php echo $payment_history['txn_status']; ?></td>
						<td class="text-center"><?php echo $payment_history['txn_date_time']; ?></td>
						<td class="text-center"><?php echo $payment_history['date_added']; ?></td>	
						<td class="text-center"><?php echo $payment_history['user']; ?></td>	
						<td class="text-center order_list_comment"><?php echo $payment_history['reference']; ?></td>	
						<td>
							<select class="form-control" 
									name="edit_order_payment_select" 
									id="edit_order_payment_select_<?php echo $payment_history['payment_id']; ?>"
									data-payment-id="<?php echo $payment_history['payment_id']; ?>">
								<option value="">--SELECT--</option>
								<option value="WRONG_ENTRY">WRONG ENTRY </option>
								<option value="DUPLICATE_ENTRY">DUPLICATE ENTRY </option>
								<option value="MERGED_ENTRY">MERGED ENTRY</option>
								<option value="EDIT_TRXN_MODE">EDIT TRXN MODE</option>
								<option value="EDIT_TRXN_REF">EDIT TRXN REF</option>
							</select> 
							<textarea class="form-control hidden" 
									  id="action_comment_<?php echo $payment_history['payment_id']; ?>" 
									  name="action_comment" 
									  cols="15" 
									  rows="3"></textarea><br/>
							<button class="btn btn-warning edit_order_payment_save hidden"
									id="edit_order_payment_save_<?php echo $payment_history['payment_id']; ?>"
									data-payment-id="<?php echo $payment_history['payment_id']; ?>"
									data-order-id="<?php echo $order['order_id']; ?>"> 
								Save 
							</button>
						</td>
				  	</tr>
				<?php } ?>		  
			<?php } ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">

	$('select[name=edit_order_payment_select]').on('change',function(){
		var payment_id = $(this).data('payment-id');
		var selected_value = $(this).val();
		$('.common_show').removeClass('hidden');
		$('.common_hidden').addClass('hidden');
		if($(this).val() !=''){
			$('#action_comment_'+payment_id).removeClass('hidden');
			$('#edit_order_payment_save_'+payment_id).removeClass('hidden');			
		} else {
			$('#action_comment_'+payment_id).addClass('hidden');
			$('#edit_order_payment_save_'+payment_id).addClass('hidden');			
		}

		if(selected_value == 'EDIT_TRXN_MODE'){
			$('span.payment_gateway_'+payment_id).addClass('hidden');
			$('#payment_gateway_'+payment_id).removeClass('hidden');
			$('span.payment_mode_'+payment_id).addClass('hidden');
			$('#payment_mode_'+payment_id).removeClass('hidden');
		}

		if(selected_value == 'EDIT_TRXN_REF'){
			$('span.merchant_txn_id_'+payment_id).addClass('hidden');
			$('#merchant_txn_id_'+payment_id).removeClass('hidden');
		}
		
	});

	$('.edit_order_payment_save').click(function(){
		var payment_id = $(this).data('payment-id');
		var order_id = $(this).data('order-id');
		var selected_value = $('#edit_order_payment_select_'+payment_id).val();
		var action_comment = $('#action_comment_'+payment_id).val();
		// array of fields_data means perticular field update by selected value i.e. merchant_txn_id, payment_gateway etc.... 
		var fields_data = {};
		if(selected_value !='' && action_comment !=''){
			if(selected_value == 'EDIT_TRXN_MODE'){
				var payment_gateway = $('#payment_gateway_'+payment_id).val();
				var payment_mode = $('#payment_mode_'+payment_id).val().toUpperCase();
				fields_data = {'payment_gateway':payment_gateway , 'payment_mode':payment_mode};

				edit_trxn_mode(payment_id, order_id, fields_data, selected_value, action_comment);
				return false;
			}
			if(selected_value == 'EDIT_TRXN_REF'){
				var merchant_txn_id = $('#merchant_txn_id_'+payment_id).val();
				fields_data = {'merchant_txn_id':merchant_txn_id };
				edit_trxn_ref(payment_id, order_id, fields_data, selected_value, action_comment);
				return false;
			}

			if(confirm('Are you sure ? ')){
				$.ajax({
					url: "index.php?route=sale/edit_order/deletePaymentEntryInEditOrderPayment&token=<?php echo $token; ?>",
		            type: "post",
		            dataType: "json",
		            data: {"payment_id":payment_id,
		            	   "order_id":order_id,
		            	   "selected_value":selected_value,
		            	   "action_comment":action_comment,
		            	   "fields_data": fields_data},
		            beforeSend: function() {
                        $('#edit_order_payment_save_'+payment_id).button('loading');
                    },
                    complete: function() {
                        $('#edit_order_payment_save_'+payment_id).button('reset');
                    },
		            success: function(data) {
		            	if(data['error']){
		            		alert(data['message']);
		            		return false;
		            	} else {
		            		alert(data['message']);
		            		$('#edit_order_payment_save_'+payment_id).addClass('hidden');		            		
		            		// window.location.reload(true);	
		            	}	

		            }
				});
			}
		} else {
			alert('Please select action and fill comment box !!');
		}
	});

	/**
	* method for edit trxn mode 
	* @param  payment_id 	: integer of payment_id 
	* @param  order_id 		: integer of order_id 
	* @param  fields_data	: array of fields_data 
	* @param  action_comment: string of action_comment 
	*/
	function edit_trxn_mode(payment_id, order_id, fields_data, selected_value, action_comment){
		
		if(confirm('Are you sure ? ')){
			$.ajax({
				url: "index.php?route=sale/edit_order/editTrxnModeInEditOrderPayment&token=<?php echo $token; ?>",
	            type: "post",
	            dataType: "json",
	            data: {"payment_id":payment_id,
	            	   "order_id":order_id,
	            	   "selected_value":selected_value,
	            	   "action_comment":action_comment,
	            	   "fields_data": fields_data},
	            beforeSend: function() {
                    $('#edit_order_payment_save_'+payment_id).button('loading');
                },
                complete: function() {
                    $('#edit_order_payment_save_'+payment_id).button('reset');
                },
	            success: function(data) {
	            	if(data['error']){
	            		alert(data['message']);
	            		return false;
	            	} else {
	            		alert(data['message']);
	            		$('#edit_order_payment_save_'+payment_id).addClass('hidden');	
	            		$('.common_hidden').addClass('hidden');
	            		$('.common_show').removeClass('hidden');
	            		$('.payment_gateway_'+payment_id).html($('#payment_gateway_'+payment_id).val());
	            		$('.payment_mode_'+payment_id).html($('#payment_mode_'+payment_id).val());
	            	}	

	            }
			});
		} else {
			return false;
		}
	}	

	function edit_trxn_ref(payment_id, order_id, fields_data, selected_value, action_comment){

		if(confirm('Are you sure ? ')){
			$.ajax({
				url: "index.php?route=sale/edit_order/editTrxnRefInEditOrderPayment&token=<?php echo $token; ?>",
	            type: "post",
	            dataType: "json",
	            data: {"payment_id":payment_id,
	            	   "order_id":order_id,
	            	   "selected_value":selected_value,
	            	   "action_comment":action_comment,
	            	   "fields_data": fields_data},
	            beforeSend: function() {
                    $('#edit_order_payment_save_'+payment_id).button('loading');
                },
                complete: function() {
                    $('#edit_order_payment_save_'+payment_id).button('reset');
                },
	            success: function(data) {
	            	if(data['error']){
	            		alert(data['message']);
	            		return false;
	            	} else {
	            		alert(data['message']);
	            		$('#edit_order_payment_save_'+payment_id).addClass('hidden');
	            		$('.common_hidden').addClass('hidden');
	            		$('.common_show').removeClass('hidden');
	            		$('.merchant_txn_id_'+payment_id).html($('#merchant_txn_id_'+payment_id).val());
	            	}	

	            }
			});
		} else {
			return false;
		}
	}	

</script>