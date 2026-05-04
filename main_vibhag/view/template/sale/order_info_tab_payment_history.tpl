<?php 
	$paid_amount_by_customer = 0.00;
	if(!empty($order['payment_history'])) { 
		$paid_amount_by_customer = $order['payment_history']['payed_by_customer'];
	}

	$tentative_amount = 0.00;

	if( !empty( $order['tentative_advance_history'] ) ){
		foreach($order['tentative_advance_history'] as $tentive_histry){ 
			$tentative_amount += $tentive_histry['amount'];
		}
	}

	$total_amount = $order['particular_order_total'];
	$cashback_coupon_amount = $order['payment_history']['cashback_coupon_amount'];
	
	$balance_amount = - (float)$total_amount
					  + (float)$order['cn_amount']
					  - (float)$order['cod_failed_penalty']
					  + (float)$cashback_coupon_amount
					  + (float)$paid_amount_by_customer
                      + (float)$order['payment_history']['actual_refund_amt']
                      - (float)$order['less_cash_discount']
                      + (float)$order['other_charges'];
    $balance_amount = round($balance_amount, 2);

	$txt_color = 'text-primary';
	if($balance_amount < 0 ){
		$txt_color = 'text-danger';
	}
	$txt_hidden = '';
	if($cashback_coupon_amount <= 0 || $tentative_amount <= 0){
		$txt_hidden = 'hidden';
	}
?>
<div class="tentative-advance_summary">
	<div class="col-sm-12">
		<div class="col-sm-4">
			<label>
				<span class="recived_payment">Total Amount : </span>
				<span><?php echo $this->currency->format($total_amount, $order['currency_code'], $order['currency_value']); ?>/-</span>
			</label>
		</div>
		<div class="col-sm-4 text-left">
			<label>
				<span class="text-warning">Balance Amount : </span>
				<span class="<?php echo $txt_color; ?>" ><?php echo $this->currency->format($balance_amount, $order['currency_code'], $order['currency_value']); ?>/-</span>
			</label>
			<label class="text-info btn-xs <?php echo $txt_hidden; ?>">
				<span>Tentative Amount : </span>
				<span><?php echo $this->currency->format($tentative_amount, $order['currency_code'], $order['currency_value']); ?>/-</span>
			</label>
			<label class="text-info btn-xs <?php echo $txt_hidden; ?>">
				<span>Cashback / Coupon : </span>
				<span><?php echo $this->currency->format($cashback_coupon_amount, $order['currency_code'], $order['currency_value']); ?>/-</span>
			</label>
		</div>
		<div class="col-sm-4 text-right">
			<label class="text-success">
				<span>Payment Recived : </span>
				<span><?php echo $this->currency->format($paid_amount_by_customer, $order['currency_code'], $order['currency_value']); ?>/-</span>
			</label>
		</div>
	</div>
</div>	
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
	    <!--<td class="text-center"> <?php echo $text_payment_refund; ?> </td> -->
			<td class="text-center"> <?php echo $text_payment_action; ?> </td>

	  </tr>
	</thead>
	<tbody>
	<?php $i = 0; foreach($order['payment_history']['result'] as $payment_history) { $i++; ?>
	  <tr <?php if($payment_history['successfull'] == 1 && $payment_history['amount'] > 0 && $payment_history['payment_gateway'] != 'cash') { ?>
							class="payment_success" 
				<?php } else{ ?>
							class="<?php echo 'payment_'.$payment_history['payment_gateway'];?>"
				<?php } ?> >
				<td class="text-center"><?php echo $payment_history['merchant_txn_id']; ?></td>	
				<td class="text-center"><?php if(!($payment_history['payment_gateway'] == 'upi')) {?><a href="<?php echo $payment_history['payment_link']; ?>" target="_blank" style="word-break: break-all;"><?php }?><?php echo $payment_history['payment_link']; ?></a></td>
				<td class="text-center">
                    <!--<?php /* if(!empty((float)$order['without_crncy_frmt_sub_total'])){ ?>
                    <span class="percentage_amount"> 
                        <?php 
                            $payment_history['amount_percentage']=( $payment_history['amount']/(float)$order['without_crncy_frmt_sub_total'] )* 100;
                         ?> 
                    </span>
                    <?php } */ ?> -->
                    <span><?php echo $this->currency->format($payment_history['amount'],'INR','1',true); ?></span>
                </td>
				<td class="text-center"><?php echo $payment_history['payment_gateway']; ?></td>
				<td class="text-center"><?php echo $payment_history['payment_mode']; ?></td>
				<td class="text-center text-uppercase"><?php echo $payment_history['txn_status']; ?></td>
				<?php if($payment_history['txn_date_time'] == '0000-00-00 00:00:00'){ ?>
						<td class="text-center"></td>
				<?php }else{ ?>
						<td class="text-center"><?php echo $payment_history['txn_date_time']; ?></td>
				<?php } ?>
				<td class="text-center"><?php echo $payment_history['date_added']; ?></td>
				<td class="text-center"><?php echo $payment_history['user']; ?></td>	
				<td class="text-center"><?php echo $payment_history['reference']; ?></td>	
				<td>
					<?php if($payment_history['successfull'] == 1 && $payment_history['bank_transfer_mode'] == 'cheque_deposited') { ?>
					<input type="text" class=" editable_bank_transfer_mode" value="<?php echo ucwords(str_replace('_',' ',$payment_history['bank_transfer_mode']));?>" data-payment-id="<?php echo $payment_history['payment_id']; ?>" readonly>
					<div class="form-group select_bank_transfer_mode" id="select_bank_transfer_mode_<?php echo $payment_history['payment_id']; ?>">
                        <select name="bank_transfer_mode" id="bank_transfer_mode">
                            <option value="cheque_success"><?php echo $text_cheque_success; ?></option>
                            <option value="cheque_failed"><?php echo $text_cheque_failed; ?></option>	
                        </select>
                        <button type="button" class="btn btn-primary btn-sm bank_transfer_save" name="bank_transfer_save" id="bank_transfer_save" data-payment-id="<?php echo $payment_history['payment_id']; ?>"><i class="fa fa-save"></i></button>
                    </div>
                    <?php } ?>
                    <?php if($payment_history['successfull'] == 1 && $payment_history['payment_gateway'] != 'bank_transfer' && $payment_history['payment_gateway'] != 'cash' ){ ?>
						<!--	<button class="btn btn-danger fa fa-reply refund_payment_links_<?php echo $payment_history['order_no']; ?>" onclick="refund_payment_links(this)" data-id="<?php echo $order['order_id']; ?>" data-merchant_txn_id="<?php echo $payment_history['merchant_txn_id']; ?>" data-total="<?php echo $payment_history['amount']; ?>" data-no="<?php echo $payment_history['order_no']; ?>" title="Refund" type="button" data-toggle="dropdown">  -->
					<?php } elseif($payment_history['payment_gateway'] == 'cash') { ?>
							<button class="btn btn-info fa fa-reply update_payment update_payment_links_<?php echo $payment_history['payment_id']; ?>" data-id="<?php echo $order['order_id']; ?>" data-pid="<?php echo $payment_history['payment_id']; ?>" data-merchant_txn_id="<?php echo $payment_history['merchant_txn_id']; ?>" data-amt="<?php echo $payment_history['amount']; ?>" data-total="<?php echo $order['without_crncy_frmt_sub_total']; ?>" data-no="<?php echo $payment_history['order_no']; ?>" title="Update" type="button" data-toggle="dropdown"> 
					<?php } ?>
				</td>
	  </tr>
	<?php } ?>
	</tbody>
</table>

<div class="update_payments display_none" >
	<div class="tab-pane" id="tab-manual_bank_transfer">
			<div class="col-sm-6">
					<div class="form-group required">
							<label class="control-label text-left" for="payment_reff_no"><?php echo $text_payment_reff_no; ?></label>
							<input type="text" name="payment_reff_no" value="" placeholder="<?php echo $entry_payment_reff_no; ?>" id="payment_reff_no" class="form-control" />
							<span class="error_bank payment_reff_no"><?php echo $error_payment_reff_no; ?></span>
					</div>
					<div class="form-group required">
							<label class="control-label text-left" for="bank_name"><?php echo $text_bank_name; ?></label>
							<select name="bank_name" id="bank_name" class="form-control">
								<option selected="selected" value="ICICI">ICICI</option>
								<option value="Standard Chartered">Standard Chartered</option>
								<option value="Yes Bank">Yes Bank</option>
							</select>
							<span class="error_bank bank_name"><?php echo $error_bank_name; ?></span>
					</div>
			</div>
			<div class="col-sm-6">
					<div class="form-group required">
							<label class="control-label text-left" for="bank_amount "><?php echo $text_bank_total_amount; ?></label>
							<input type="text" name="bank_amount" value="" placeholder="<?php echo $entry_bank_total_amount; ?>" id="bank_amount" class="form-control" />
							<span class="error_bank bank_amount"><?php echo $error_bank_amount; ?></span>
					</div>
					<div class="form-group required">
							<label class="control-label text-left" for="payment_date"><?php echo $text_payment_date; ?></label>
							<div class="input-group payment_history_date">
									<span class="input-group-btn">
											<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
									</span>
									<input type="text" name="payment_date" value="" placeholder="<?php echo $entry_payment_date; ?>" id="payment_date" class="form-control" />
							</div>
							<span class="error_bank payment_date"><?php echo $error_payment_date; ?></span>
					</div>
			</div>
			<div class="col-sm-12">
					<div class="common_buttons_payment_link  pull-left">
							<button type="button" class="btn btn-danger form-control payment_cancel"><?php echo $text_cancel; ?></button>
					</div>
					<div class="common_buttons_payment_link pull-right">
							<button type="button" class="btn btn-success form-control manual_bank_send"><?php echo $text_save; ?></button>
					</div>
			</div>
	</div>
</div>
<table  class="table table-bordered">
	<caption><h3>Tentative Advance History</h3></caption>
	<thead>
	  <tr>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_pymnt_mod; ?> </td>
		<td class="text-center"> <?php echo $txt_tentative_advnc_histry_chq_no; ?> </td>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_txn_id; ?> </td>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_date; ?> </td>
		<td class="text-center"> <?php echo $txt_tentative_advnc_histry_amt; ?> </td>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_notes; ?> </td>
		<td class="text-center"> <?php echo $txt_tentative_advnc_histry_brnch_name; ?> </td>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_staff_name; ?> </td>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_txn_status; ?> </td>
	    <td class="text-center"> <?php echo $txt_tentative_advnc_histry_date_created; ?> </td>
	  </tr>
	</thead>
	<tbody>
		<?php if(!empty($data['order']['tentative_advance_history'])){ ?>
		<?php 
			$tentative_history = $data['order']['tentative_advance_history'];
			foreach($tentative_history as $tentive_histry){ 
		?>
	  	<tr>
			<td class="text-center"><?php echo $tentive_histry['payment_mode'];?></td>
			<td class="text-center"><?php echo $tentive_histry['cheque_no'];?></td>
			<td class="text-center"><?php echo $tentive_histry['txn_id'];?></td>
			<td class="text-center"><?php echo $tentive_histry['dated'];?></td>
			<td class="text-center"><?php echo $tentive_histry['amount'];?></td>
			<td class="text-center"><?php echo $tentive_histry['notes'];?></td>
			<td class="text-center"><?php echo $tentive_histry['branch_name'];?></td>
			<td class="text-center"><?php echo $tentive_histry['staff_name'];?></td>
			<td class="text-center"><?php echo $tentive_histry['transaction_status'];?></td>
			<td class="text-center"><?php echo $tentive_histry['date_created'];?></td>
	  	</tr>
	  	<?php } ?>
	  	<?php } ?>
	</tbody>
</table>

<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
    <!--
    $('.payment_history_date').datetimepicker({
        maxDate: new Date()
    });
    //-->
</script>
<script type="text/javascript">
	 $(document).ready(function() {
				$('.update_payment').click(function() {
						remove_bank_info();
						var data_amt = $(this).attr('data-amt');

						$('.manual_bank_send').attr('data-pid', $(this).attr('data-pid'));
						$('.manual_bank_send').attr('data-id', $(this).attr('data-id'));
						$('.manual_bank_send').attr('data-no', $(this).attr('data-no'));
						$('.manual_bank_send').attr('data-total', $(this).attr('data-total'));

						$('#bank_amount').val(data_amt);
						$('#bank_amount').prop("disabled", true);
						$('.update_payments').show();
				});

				$('.manual_bank_send').click(function() {
					var payment_id              = $(this).attr('data-pid');
					var order_id                = $(this).attr('data-id');
					var order_no                = $(this).attr('data-no');
					var order_total             = $(this).attr('data-total');
					var payment_reff_no         = $('#payment_reff_no').val();
					var bank_name               = $('#bank_name').val();
					var bank_amount             = $('#bank_amount').val();
					var payment_date            = $('#payment_date').val();
					if (payment_reff_no == '') {
							$('.payment_reff_no').show();
							return false;
					}
					if (bank_name == '') {
							$('.bank_name').show();
							return false;
					}
					if (bank_amount == '') {
							$('.bank_amount').show();
							return false;
					}else if(bank_amount <= 0){
							$('.bank_amount').show();
							$('.bank_amount').text('Enter the amount is greater than 0');
							return false;
					}
					if (payment_date == '') {
							$('.payment_date').show();
							return false;
					}
					var update = 1;
					$.ajax({
							url: "index.php?route=sale/order/manualBankTransfer&token=<?php echo $token; ?>",
							type: "post",
							dataType: "json",
							data: "update="+update+"&payment_id="+payment_id+"&order_id=" + order_id + "&order_no=" + order_no + "&order_total=" + order_total + '&payment_reff_no=' + payment_reff_no + '&bank_name=' + bank_name + '&bank_amount=' + bank_amount + '&payment_date=' + payment_date,
							beforeSend: function() {
									$('.manual_bank_send').button('loading');
							},
							complete: function() {
									window.location.reload();
							}
					});
				});

				$('.payment_cancel').click(function() {
						$('.update_payments').hide();
				});
				// Set empty value in Manual Bank Transfer
        function remove_bank_info(){
            $('#payment_reff_no').val('');
            $('#bank_amount').val('');
            $('#payment_date').val('');
        }


        $(".bank_transfer_save").on('click',function(){
            let obj = $(this);
            var bank_transfer_mode = $("select[name=\'bank_transfer_mode\']").val();
            var payment_id = obj.data('payment-id');
            var order_id = '<?php echo $order["order_id"]; ?>';
            $.ajax({
                url: "index.php?route=sale/order/changeBankTransferMode&token=<?php echo $token; ?>",
                type:'post',
                dataType:'json',
                data: "payment_id="+payment_id+"&bank_transfer_mode="+bank_transfer_mode+"&order_id="+order_id,
                beforeSend: function() {
                    $('.bank_transfer_save').button('loading');
                },
                complete: function() {
                    $('.bank_transfer_save').button('reset');
                },
                success: function(json){
                    if(json['success']=='success'){
                        obj.parents('tr').find('td').eq(5).text(json['bnk_trnfr_mode']);
                    }else{
                        alert('Please select bank transfer mode!');
                    }
                }
            });
        });

        $('.editable_bank_transfer_mode').on('dblclick',function(){
        	var payment_id = $(this).data('payment-id');
        	$('#select_bank_transfer_mode_'+payment_id).show();
        	$(this).hide();
        });

	});
	 
</script>