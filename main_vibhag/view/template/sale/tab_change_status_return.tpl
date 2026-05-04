<?php $current_return_actions_for_tab = $current_return_actions_tab_wise[$tab_key]; ?>
<?php $all_avaiable_post_actions_for_tab = $all_avaiable_post_actions['tab'][$tab_key]; ?>
<div class="row">
	<div class="col-lg-10 col-md-10">
	    <div class="row">
	      	<div class="col-lg-3 col-md-3">
		        <select id="input-return-action" 
		        class="return_action form-control pull-left" 
		        onchange="putSelectValue(this,'#products-change-status-<?php echo $tab_key;?>','.return_action_list','.products-change-status','.return_id')">
		          <option value="0">Select</option>
		          <?php foreach ($all_avaiable_post_actions_for_tab as $key => $action_id) { ?>
		             <option value="<?php echo $action_id; ?>">
		               <?php echo $return_actions[$action_id]['name']; ?>
		             </option>
		          <?php } ?>
		        </select>
	      	</div>
	      	<div class="col-lg-3 col-md-3">
		        <select id="input-shipping-methods"
	                class="form-control pull-left"
	                onchange="putSelectValue(this,'#products-change-status-<?php echo $tab_key;?>','.shipping_methods_list','.products-change-status','.return_id')">
		          <option value="">Select</option>
		          <?php foreach ($shipping_method as $code => $text) { ?>
		          <option value="<?php echo $code; ?>"><?php echo $text; ?></option>
		          <?php } ?>
		        </select>
	      	</div>
	    </div>
    </div>
  	<div class="col-lg-2 col-md-2">
      	<span>
      		<button type="button" onclick="save_change_status('form-status-return','<?php echo $tab_key;?>');" title="<?php echo $button_save; ?>" class="btn btn-primary change-status-button"><i class="fa fa-save"></i>
      		</button>
	      	<button type="button" onClick="window.location.reload()" name="cancel" data-toggle="modal" id="add-return-btn" title="Cancel" class="btn btn-default"><i class="fa fa-reply"></i>
	      	</button>
      	</span>
  	</div>
</div><br>
<div class="table-responsive" style="float: right;font-size: 15px;">
	 <div class="color-box" 
	 	style="background-color: #F5DEB3;
	 			width: 15px;
    			height: 15px;
    			display: inline-block;
    			margin-right: 4px;
    			margin-top: 3px;
    			">
    </div><b>Color for RETURNs whose Buyer invoice is not generated.</b>
</div><br><br>
<div class="table-responsive">
<form name="form-status-return" method="post">
<input type="hidden" name="manually_shipment_courier_<?php echo $tab_key;?>" value="">	
<input type="hidden" name="manually_shipment_docket_<?php echo $tab_key;?>" value="">	
<input type="hidden" name="manually_shipment_warehouse_<?php echo $tab_key;?>" value="">	
  <table class="table table-bordered">
	<thead>
      <tr>
        <td style="width: 5px;"><input type="checkbox" onclick="clickAllId(this,'<?php echo $tab_key;?>')" class="statusForm selectAll"/></td>
        <td>Product</td>
        <td>Model</td>
        <td style="width: 15%;">Return Reason</td>
        <td style="width: 10px;">Master Return Id</td>
        <td style="width: 20%;">Return Action</td>
        <td>Shipping Method</td>
        <!--<td>Payment Method</td> -->
        <td>Internal Note</td>
        <td>User</td>
        <td style="width: 15px;">Action</td>
      </tr>
    </thead>
    <tbody id="products-change-status-<?php echo $tab_key?>">
        <?php 
        foreach($returns as $return_id => $return){ 
            $op_id                   = $return['order_product_id'];
            $return_action_id        = $return['return_action_id'];
            $product                 = $products[$op_id];
            $comment 				 = $return['comment'];
            $return_reason_id        = $return['return_reason_id'];
            $return_reason_name      = !empty($return['return_reason']) ? $return['return_reason']['name'] : '';
            $return_action_id        = $return['return_action_id'];
            $return_action_reason_id = $return['return_action_reason_id'];
            $master_return_id        = $return['master_return_id'];
            $quantity 				 = $return['quantity'];
            $next_actions_for_return = array();
            $buyer_invoice_id        = $return['product']['buyer_invoice_id'];
            $seller_invoice_id       = $return['product']['seller_invoice_id'];

           	$credit_note_id 		 = !empty($return['credit_notes']['credit_note_id'])
           							    ? $return['credit_notes']['credit_note_id']
           							    : '';
			$credit_note_no 		 = !empty($return['credit_notes']['credit_note_prefix'])
           							    ? $return['credit_notes']['credit_note_prefix'].$return['credit_notes']['credit_note_no']
           							    : '';           							    
			$credit_note_amount		 = !empty($return['credit_notes']['net_refundable'])
           							    ? $return['credit_notes']['net_refundable']
           							    : '';


            if(in_array($return_action_id, $current_return_actions_for_tab)) {

                $next_actions_for_return = $return['post_actions'];
                $isDisabled = "";
		        if(!$return['is_editable']){
		            //$isDisabled = "Disabled";
		        }

		        $seller_id = $product['seller_id'];
		        //Set Background Color for return action row
		        $row_color = '';
		        if(empty($buyer_invoice_id)){
		        	$row_color = ' style="background-color: #F5DEB3;" ';
		        }

		    $helpdesk_ticket_id = (!empty($return['helpdesk_ticket_id'])) 
		    						? $return['helpdesk_ticket_id']
		    						: '';    
		    //pr($return);						
           ?>
          	<tr class="products-change-status" <?php echo $row_color; ?> >
          	    <td style="width: 5px;">
          	    	<input 
      	    			type="checkbox" 
      	    			class="change_status return_id" 
      	    			name="return_id[]" 
      	    			id='change_status_return_<?php echo $tab_key;?>_<?php echo $return_id; ?>'
      	    			value="<?php echo $return_id; ?>" 
      	    			data-tab="<?php echo $tab_key;?>"
      	    			data-is-qty-editable = "<?php echo $return_actions[$return_action_id]['is_qty_editable'];?>"
      	    			data-is-returnable="<?php echo $product['is_returnable']; ?>"
      	    			data-model="<?php echo $product['model']; ?>"
      	    			data-seller-id="<?php echo $products[$op_id]['seller_id']; ?>"
      	    			data-seller-invoice-id="<?php echo $products[$op_id]['seller_invoice_id']; ?>"
      	    			data-seller-nickname="<?php echo $sellers[$seller_id]['nickname']; ?>"
      	    			data-seller-company="<?php echo $sellers[$seller_id]['company']; ?>"
      	    			data-suborder-id="<?php echo $products[$op_id]['suborder_id']; ?>"
      	    			data-return-id="<?php echo $return_id; ?>"
      	    			data-order-product-id="<?php echo $op_id?>" 
      	    			data-master-return-id="<?php echo $master_return_id?>" 
      	    			data-return-reason-id="<?php echo $return_reason_id?>"
      	    			data-credit-note-id="<?php echo $credit_note_id;?>"
      	    			data-credit-note-no="<?php echo $credit_note_no;?>"
      	    			data-credit-note-amount="<?php echo $credit_note_amount;?>"
      	    			data-comment = "<?php echo $comment?>"
      	    			data-helpdesk-ticket-id = "<?php echo $helpdesk_ticket_id;?>"
      	    			/>
          	    </td>
          		<td>
          			<img
		            src="<?php echo $pid_to_imgs[$op_id]; ?>"
		            width="<?php echo $image_width; ?>px"
		            height="<?php echo $image_height; ?>px"
		          /><br><b><?php echo $op_id;?></b>
          		</td>
          		<td>
          			<a href="<?php echo $product['link']; ?>"><?php echo $product['model']; ?></a>
			        <i><?php echo !empty($oop_options[$op_id]['name'])? 
			            "<br>".$oop_options[$op_id]['name'].' : '.$oop_options[$op_id]['value'] :
			            '' ?> 
			        </i>
			        <?php
			            if($product['is_returnable'] == 0){
			              echo '<span style="color: red; font-size: large;"><br>(Non-Returnable Product)</span>';
			            }
		            ?>
          		</td>
          		<td>
          			<b><?php echo !empty($return_reason_name)? $return_reason_name: '';?></b>
          			<br><br>
          			<b>Qty : <span id="action_quantity_fixed_<?php echo $tab_key;?>_<?php echo $return_id; ?>"><?php echo $return['quantity']?></span></b>
          		</td>
          		<td style="width: 10px;">
          			<b><?php echo $master_return_id;?></b>
          		</td>
          		<td style="width: 20%;">
          			<?php echo !empty($return_actions[$return_action_id])?  $return_actions[$return_action_id]['name'] : '';?> <br>
          			<?php if(strtolower($admin_mode) == 'on' || !empty($next_actions_for_return['Available Actions'])){ ?>
          			<select name="product[<?php echo $op_id; ?>][<?php echo $return_id; ?>][return_action]" 
          				class="form-control pull-left return_action_list" 
          				style="margin-bottom: 10px" 
          				id="return_action_list_<?php echo $tab_key;?>_<?php echo $return_id; ?>"
          				data-privious="<?php echo $return_actions[$return_action_id]['return_action_id'];?>" 
          				data-tab="<?php echo $tab_key;?>"
          				data-opid="<?php echo $return_id; ?>" 
          				data-rid="<?php echo $return_id; ?>"
						data-quantity="<?php echo $quantity;?>"
          				>
          				<option value="0">Select Next Action</option>
          				<?php foreach($next_actions_for_return as $action_title => $return_actions_arr) { ?>
          					<?php if(strtolower($admin_mode) == 'on' && !empty($return_actions_arr)){ ?>
          					<optgroup label="<?php echo $action_title; ?>">
          					<?php }?>
          					<?php foreach($return_actions_arr as $key => $action_id) { ?>
					            <?php if(isset($return_actions[$action_id])) { ?>
					            	<option value="<?php echo $action_id; ?>"><?php echo $return_actions[$action_id]['name']; ?></option>
					        	<?php } ?>
					        <?php } ?>
					        <?php if(strtolower($admin_mode) == 'on' && !empty($return_actions_arr)){ ?>
					        </optgroup>
					        <?php }?>
					    <?php }?>
          			</select><br><br><br>
          			<?php } ?>
          			<?php $is_display = (!$return_actions[$return_action_id]['is_qty_editable'])?'none':'' ; ?>
          			<div 
          			   id="action_quantity_<?php echo $tab_key;?>_<?php echo $return_id; ?>"
          			   style="display: <?php echo $is_display?>">
	          			<b>Quantity:</b>
	          			<input 
	          			    class="form-control change_return_quantity" 
	      					style="width: 150px;"
	      					type="number" 
	      					name="product[<?php echo $op_id; ?>][<?php echo $return_id; ?>][quantity]" 
	      					id="return_action_quantity_<?php echo $tab_key;?>_<?php echo $return_id; ?>" 
	      					value="<?php echo $quantity;?>" 
	      					data-quantity="<?php echo $quantity;?>"
	      					data-privious="<?php echo $quantity;?>"
	      					data-tab="<?php echo $tab_key?>"
	      					data-return-id="<?php echo $return_id; ?>"
	      					><br>(<i>Please Enter Actually Received Quantity</i>)
          			</div>
          		</td>
          		<td>
          		  <?php if(!empty($next_actions_for_return)){  ?>
		          <select name="product[<?php echo $op_id; ?>][<?php echo $return_id; ?>][shipping_method]"
		              	class="form-control shipping_methods shipping_methods_list" 
		              	data-privious="<?php echo $return['shipping_method']?>"
		              	id="shipping_methods_list_<?php echo $tab_key;?>_<?php echo $return_id; ?>" <?php echo ($return['return_shipment_tracking_id'] > 0)?'disabled':''?>  >
		            <option value="">Select</option>
		            <?php foreach ($shipping_method as $code => $text) { ?>
		            <option value="<?php echo $code; ?>"
		            <?php echo !empty($return_id) && $code == $return['shipping_method'] ? 'selected' : ''; ?>>
		              <?php echo $text; ?>
		            </option>
		            <?php } ?>
		          </select>
		          <?php }else{
		          	echo $shipping_method[$return['shipping_method']];
		          } ?>
		        </td>
		        <td>
		        	<select name="return[<?php echo $op_id; ?>][<?php echo $return_id; ?>][action_reason]" 
		        			data-privious="<?php echo $return['return_action_reason_id']?>"
		        			id="action_reason_<?php echo $tab_key;?>_<?php echo $return_id; ?>" 
		        			style="display: none; width: 150px;" 
		        			class="action_reason form-control">
			        </select><br><br>
			        <?php if(isset($action_reasons[$return_action_reason_id])){ ?>
			        <b>(<?php echo $action_reasons[$return_action_reason_id]; ?>)</b><br>
			        <?php } ?>
		        	<textarea
		            		name="product[<?php echo $op_id; ?>][<?php echo $return_id; ?>][internal_note]" 
		            		id="internal_note_<?php echo $tab_key;?>_<?php echo $return_id; ?>" 
		            		rows="4" 
		            		cols="10"
		            		data-privious="<?php echo $return['internal_note']; ?>" 
		            		class="internal_note" 
		            			<?php echo ( empty($next_actions_for_return) && (strtolower($admin_mode) != 'on') ) ? 'disabled' :' '; ?>
		            		><?php echo $return['internal_note']; ?></textarea>
		        </td>
		        <td><?php echo !empty($return['user']) ? $return['user'] : ''; ?></td>
		        <td style="width: 15px;">

		         <?php if(strtolower($admin_mode) == 'on'){ ?>
		         	<a class="btn button-default split-return" 
		         		style="border: solid 1px #ccc;background:#fff" 
		         		href="javascript:void(0)" 
		         		data-tab_key="<?php echo $tab_key;?>"
          				data-order_id="<?php echo $order_id; ?>" 
          				data-rid="<?php echo $return_id; ?>"
          				data-rqty="<?php echo $return['quantity']; ?>"
          				onclick="splitReturn(this)"
          				toggle="tooltip"
          				title="Split Return"
          				data-original-title="Split Return"
		         		>
		            	<i class="fa fa-share-alt" aria-hidden="true" style="font-size:15px;"></i>
		          	</a>
		          	<a class="btn button-default convert-return" 
		         		style="border: solid 1px #ccc;background:#fff" 
		         		href="javascript:void(0)" 
		         		data-tab_key="<?php echo $tab_key;?>"
          				data-order_id="<?php echo $order_id; ?>" 
          				data-rid="<?php echo $return_id; ?>"
          				data-rtype="<?php echo $return['return_type']; ?>"
          				data-raction_id="<?php echo $return['return_action_id']; ?>"
          				onclick="convertReturn(this)"
          				toggle="tooltip"
          				title="Return / Replacement Conversion"
          				data-original-title="Return / Replacement Conversion"
		         		>
		            	<i class="fa fa-exchange" style="font-size:15px;"></i>
		          	</a>
		          <?php } ?>
		          <a
		            class="btn button-default"
		            style="border: solid 1px #ccc;background:#fff"
		            href="javascript:void(0)"
		            data-opid="<?php echo $op_id; ?>"
		            data-pmodel="<?php echo $product['model']?>"
		            data-returnid="<?php echo $return_id; ?>"
		            data-masterid="<?php echo $master_return_id; ?>"
		            onclick="getHistory(this)"
		            toggle="tooltip"
		            title="View History"
		            data-original-title="View History">
		            <i class="fa fa-eye "></i>
		          </a>
		          <a
		            class="btn button-default resetChangeStatusRow"
		            style="border: solid 1px #ccc;background:#fff"
		            href="javascript:void(0)"
		            data-return-tab="<?php echo $tab_key;?>"
		            data-return-id="<?php echo $return_id; ?>"
		            data-container-id="products-change-status"
		            data-shipping-method="<?php echo $return['shipping_method']?>"
		            toggle="tooltip"
		            title="Reset"
		            data-original-title="Reset Row">
		            <i class="fa fa-refresh "></i>
		          </a>
		          <a class="btn button-default show_defected_image"
			          id="shipment_cancel_button_<?php echo $return['return_id']?>"
			          data-order-product-id="<?php echo $return['order_product_id']?>"
			          data-master-return-id="<?php echo $return['master_return_id']?>"
			          style="border: solid 1px #ccc;background:#fff"
			          href="javascript:void(0)"
			          toggle="tooltip"
			          title="View replacement defected image">
			          <i class="fa fa-picture-o" aria-hidden="true"></i>
				    </a>
		        </td>
          	  </tr>
          <?php }?>
        <?php }?>
    </tbody>
  </table>
    <!-- Popup to split return -->
	<div id="split-return-popup-<?php echo $tab_key;?>" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Split Return (Into 2 returns)</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="alert alert-danger" id="err-div-<?php echo $tab_key;?>" style="display: none;"></div>
	        <input type="hidden" class="order_id-<?php echo $tab_key;?>" />
	        <input type="hidden" class="return_id-<?php echo $tab_key;?>" />
	        <h3>
	            Total Return Quantity : 
	            <span class="ttl-return-qty-<?php echo $tab_key;?>">0</span><br><br>
	        </h3>
	        <h4>Split Return Quantity Into : </h4>
	        <input 
	           type="number" 
	           min="0"
	           name="split-return-qty-<?php echo $tab_key;?>" 
	           class="split-return-qty-<?php echo $tab_key;?>" 
	           value="0" 
	        />
	      </div>
	      <div class="modal-footer">
	        <button id="slit-button-<?php echo $tab_key;?>" type="button" class="btn btn-primary" onclick="splitReturnSubmit('<?php echo $tab_key;?>');">Split</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>

	  </div>

	</div>

	<!-- Popup to convert return into replacement or vice versa-->
	<div id="return_replacement_convert_popup-<?php echo $tab_key;?>" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Return / Replacement Conversion</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="alert alert-danger" id="convert-err-div-<?php echo $tab_key;?>" style="display: none;"></div>
	      	<input type="hidden" class="order_id-<?php echo $tab_key;?>" />
	        <input type="hidden" class="return_id-<?php echo $tab_key;?>" />
	        <input type="hidden" class="rtype-<?php echo $tab_key;?>" />
	        <h4>Reason Name: </h4>
	        <select class="form-control convert-return-reason" id="convert-return-reason-<?php echo $tab_key;?>">
	        	<option value="0">Select</option>
	        </select><br><br>
	        <h4>Return Action : </h4>
	        <select class="form-control" id="convert-return-action-<?php echo $tab_key;?>">
	        	<option value="0">Select</option>
	        	<?php foreach ($all_avaiable_post_actions_for_tab as $key => $action_id) { ?>
		            <option value="<?php echo $action_id; ?>">
		              	<?php echo $return_actions[$action_id]['name']; ?>
		            </option>
	            <?php } ?>
	        </select><br><br>
	        <h4>Internal Note: </h4>
	        <textarea id="convert-internal-note-<?php echo $tab_key;?>" rows="5" cols="72"></textarea>
	      </div>
	      <div class="modal-footer">
	        <button id="convert-return-button-<?php echo $tab_key;?>" type="button" class="btn btn-primary" onclick="convertReturnSubmit('<?php echo $tab_key;?>');">Convert</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>  
	</div>
	<!-- Manually Generated Reverse Shipment Popup -->
	<div id="manually_shipment_popup-<?php echo $tab_key;?>" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Manually Generate Reverse Shipment</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="alert alert-danger" id="manually-shipment-err-div-<?php echo $tab_key;?>" style="display: none;"></div>
	        <h4>Courier Partner: </h4>
	        <?php if(!empty($courier_partners_reverse)) { ?>
            <select id="manually-shipment-courier-<?php echo $tab_key;?>" class="form-control">
                <option value="">Select Courier Partner</option>
                <?php foreach($courier_partners_reverse as $row) { ?>
                <option value="<?php echo $row['courier_name']?>"><?php echo $row['courier_name']?></option>
                <?php } ?><option value="Gati">Gati</option>
            </select>    
            <?php } ?>
	        <br><br>
	        <h4>Docket No : </h4>
	        <input type="text" class="form-control" id="manually-shipment-docketno-<?php echo $tab_key;?>" >
	      	<br><br>
	        <h4>Warehouse : </h4>
	        <?php if(!empty($Warehouses)) { ?>
	        <select id="manually-shipment-warehouse-<?php echo $tab_key;?>" class="form-control">
                <option value="">Select Warehouse</option>
                <?php foreach($Warehouses as $row) { ?>
                <option value="<?php echo $row['warehouse_id']?>"><?php echo $row['warehouse_name']; ?></option>
                <?php } ?>
            </select>    
            <?php } ?>
	      </div>
	      <div class="modal-footer">
	        <button id="manually-shipment-return-button-<?php echo $tab_key;?>" type="button" class="btn btn-primary" onclick="submitManuallyShipment('form-status-return','<?php echo $tab_key;?>');" >Generate</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- Manually Generated Reverse Shipment Popup -->

</form>
</div>