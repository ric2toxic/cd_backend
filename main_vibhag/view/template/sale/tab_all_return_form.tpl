<div class="row">
	<div class="col-lg-10 col-md-10">
    <div class="row">
    	<div class="col-lg-3 col-md-3">
    		<select id="input-return-reason" class="form-control pull-left" onchange="putSelectValue(this,'#products','.return_reason','.products','.order_product_id')">
	        <option value="0">Select</option>
	        <?php if(!empty($all_available_return_reasons)){ ?>
	        <?php foreach( $all_available_return_reasons as $return_option => $options ){ ?>
       		<optgroup label="<?php echo $options['title']; ?>">
            <?php foreach( $options['reasons'] as $reason ){ ?>
              <option value="<?php echo $reason; ?>" >
                <?php echo $return_reasons[$reason]['name']; ?>
              </option>
            <?php } ?>
            </optgroup>
	        <?php } ?>
	        <?php } ?>
    		</select>
    	</div>
    	<div class="col-lg-3 col-md-3">
        <select id="input-shipping-methods"
            class="form-control pull-left"
            onchange="putSelectValue(this,'#products','.shipping_methods','.products','.order_product_id')">
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
      <button type="button" name="submit" data-toggle="modal" id="add-return-btn" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i>
      </button>
       <button type="button" onClick="window.location.reload()" name="cancel" data-toggle="modal" id="add-return-btn" title="Cancel" class="btn btn-default"><i class="fa fa-reply"></i>
      </button>
    </span>
  </div>
</div><br>
<form action="" method="post" id="form-return" name="form-return" class="form-horizontal" >
<div class="table-responsive">
	<table class="table table-bordered">
    <thead>
      <tr>
        <td><input type="checkbox" onclick="clickAllId(this)" class= "addForm selectAll"/></td>
        <td>Product</td>
        <td>Model</td>
        <td>Total Pcs</td>
        <td>Price/Piece</td>
        <td>Master Return Id</td>
        <td>Return Reason</td>
        <td>Return Action</td>
        <td>Pieces To Return</td>
        <td>Shipping Method</td>
        <td>Comment</td>
        <td>Internal Notes</td>
        <td>User</td>
        <td>Action</td>
      </tr>
    </thead>
    <tbody id="products" class="addForm_active">
      <?php 

      $customer_payment_company = (!empty($order['payment_company']))?$order['payment_company']:'';

      if(!empty($products) && is_array($products)){
        foreach($products as $key => $product){
          $oopid             = $product['order_product_id'];
          $comboid           = $product['combo_product_id'];
          $buyer_invoice_id  = $product['buyer_invoice_id'];
          $seller_invoice_id = $product['seller_invoice_id'];
          $total_quantity    = (int)$product['quantity']*(int)$product['piece_in_set'];
          $remain_quantity   = $total_quantity;

         if(!empty($product['returns'])){
            $product_returns = $product['returns'];

            foreach($product_returns as $return) {
              $return_id = $return['return_id'];
              $master_return_id   = $return['master_return_id'];
              $return_reason_id   = $return['return_reason_id'];
              $return_reason_name = !empty($return['return_reason']) ? $return['return_reason']['name'] : '';
              $return_action_id   = $return['return_action_id'];
              $return_action_name = !empty($return['return_action'])?$return['return_action']['name']:'';
              $action_reason_id   = $return['return_action_reason_id'];
              $tr_class = ($return['debit_note_id'] > 0 ? 'bg-success' : 'bg-danger');
    ?>
    <?php 
      $isDisabled = "";
      if( !$return['is_editable'] && $admin_mode == 'off' ){
        $isDisabled = "Disabled";
      }
    ?>
      <tr
        id="<?php echo $return_id; ?>"
        data-returnId="<?php echo $return_id; ?>"
        data-orderProductId="<?php echo $oopid; ?>"
        class="products <?php echo $tr_class;?>"
      >
        <td>
          <input
            type="checkbox"
            class="addForm order_product_id add_return_chck"
            name="order_product_id[]" 
            value="<?php echo $oopid; ?>"
            data-combo-product-id="<?php echo $comboid; ?>"
            data-order-product-id="<?php echo $oopid; ?>"
            data-product-id="<?php echo $product['product_id']; ?>"
            data-master-return-id="<?php echo $master_return_id; ?>"
            data-seller-invoice-id="<?php echo $seller_invoice_id; ?>"
            data-customer-payment-company="<?php echo $customer_payment_company;?>"
          />
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][last_return_id]" 
            value="<?php echo $return_id; ?>" 
            class="last_return_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][last_return_reason]" 
            value="<?php echo $return_reason_id; ?>" 
            class="last_return_reason">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][last_return_quantity]" 
            value="<?php echo $return['quantity']; ?>" 
            class="last_return_quantity">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][buyer_invoice_id]" 
            value="<?php echo $product['buyer_invoice_id']; ?>" 
            class="buyer_invoice_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][seller_id]" 
            value="<?php echo $product['seller_id']; ?>" 
            class="seller_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][total_quantity]" 
            value="<?php echo $product['quantity'] * $product['piece_in_set']; ?>" 
            class="total_quantity">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][master_return_id]" 
            value="<?php echo $return['master_return_id']; ?>" 
            class="last_master_return_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][last_shipping_method]" 
            value="<?php echo $return['shipping_method']; ?>" 
            class="last_shipping_method">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][last_comment]" 
            value="<?php echo $return['comment']; ?>" 
            class="last_comment">
       </td>
        <td>
          <img
            src="<?php echo
            $pid_to_imgs[$oopid]; ?>"
            width="<?php echo $image_width; ?>px"
            height="<?php echo $image_height; ?>px"
          /><br><b><?php echo $oopid;?></b>
        </td>
        <td>
          <a href="<?php echo $product['link']; ?>"><?php echo $product['model']; ?></a>
          <i><?php echo !empty($oop_options[$oopid]['name'])? 
            "<br>".$oop_options[$oopid]['name'].' : '.$oop_options[$oopid]['value'] :
            '' ?> 
          </i>
          <?php if(!empty($return['credit_note_id']) && !empty($return['debit_note_id']) ){
            echo '<br>(CreditNote & DebitNote Generated)';
          }else if(!empty($return['credit_note_id']) ){
            echo '<br>(CreditNote Generated)';
          }else if(!empty($return['debit_note_id']) ){
            echo '<br>(DebitNote Generated)';
          }
            if($product['is_returnable'] == 0){
              echo '<br><span style="color: red; font-size: large;">(Non-Returnable Product)</span>';
            }else if(!empty($product['sor_msg'])){
              echo '<br><span style="color: orange;font-size: medium;">(<i>'. $product['sor_msg'] .'</i>)</span>';
            }
          ?>
        </td>
        <td><?php echo $total_quantity; ?></td>
        <td><?php echo $product['price_per_piece_with_currency']; ?></td>
        <td><?php echo $return['master_return_id']; ?></td>
       
        <td>
          <select style="width: 200px;"
          data-privious="<?php echo $return_reason_id?>"
          name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][return_reason]"
          class="return_reason form-control"
          <?php echo $isDisabled;?>
          <?php echo empty($product['return_reasons'])?"disabled":"";?>
           >
            <option value="0">Select</option>
            <?php  if(!empty($product['return_reasons'])){ ?>
            <?php foreach( $product['return_reasons'] as $return_option => $options ){ ?>
              <optgroup label="<?php echo $options['title']; ?>"><?php 
              foreach($options['reasons'] as $reason ){ 
                $str =''; 
                if($reason == $return_reason_id){ $str = 'selected="selected"';}
              ?>
              <option value="<?php echo $reason; ?>" <?php echo $str;?> ><?php echo $return_reasons[$reason]['name']; ?>
              </option>
              <?php } ?>
              </optgroup>
            <?php } ?>
            <?php } ?>
          </select><br>
          <?php echo $return_reason_name; ?>
        </td>
        <td>
          <?php echo !empty($return_action_name)? $return_action_name : ''; ?><br>
          <b><?php echo !empty($action_reason_id)?'('.$action_reasons[$action_reason_id].')':''; ?></b>
        </td>
        <td>
          <?php
            $qty_status = '';
            if(!empty($isDisabled) || empty($buyer_invoice_id)){
              $qty_status = 'Disabled';
            }
          ?>
          <input type="number"
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][quantity]"
            min="0"
            max="<?php echo $remain_quantity;?>"
            value="<?php echo !empty($return['quantity']) ? $return['quantity'] : 0; ?>"
            class="product_quantity form-control"
            data-privious="<?php echo !empty($return['quantity']) ? $return['quantity'] : 0; ?>" 
            <?php echo $qty_status;?> 
          />
          <span class="last_value">
           <?php echo '<b>'.$return['quantity'].'</b>'; ?>
          </span>
        </td>
        <td>
          <select name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][shipping_method]"
              class="form-control shipping_methods"
              data-privious="<?php echo $return['shipping_method']?>" 
              <?php echo $isDisabled;?>>
            <option value="">Select</option>
            <?php foreach ($shipping_method as $code => $text) { ?>
            <option value="<?php echo $code; ?>"
            <?php echo !empty($return_id) && $code == $return['shipping_method'] ? 'selected' : ''; ?>>
              <?php echo $text; ?>
            </option>
            <?php } ?>
          </select>
          <span class="last_value">
            <?php echo !empty($return_id) ? '<b>'.$return['shipping_method'].'</b>' : ''; ?>
          </span>
        </td>
        <td>
          <textarea
            name="product[<?php echo $oopid; ?>][<?php echo $return_id; ?>][comment]"
            rows="4"
            cols="10"
            class="comment"
            placeholder="<?php echo $entry_comment; ?>"
            data-privious="<?php echo $return['comment']; ?>" 
            <?php echo $isDisabled;?>
          ><?php echo $return['comment']; ?></textarea>
          <br>
          <span class="last_value">
            <?php echo !empty($return['comment']) ? '<b>'.$return['comment'].'<b>' : ''; ?>
          </span>
        </td>
        <td><?php echo (isset($return['internal_note'])? $return['internal_note'] : ''); ?></td>
        <td><?php echo !empty($return['user']) ? $return['user'] : ''; ?></td>
        <td>
          <a
            class="btn button-default"
            style="border: solid 1px #ccc;background:#fff"
            href="javascript:void(0)"
            data-opid="<?php echo $oopid; ?>"
            data-pmodel="<?php echo $product['model']?>"
            data-returnid="<?php echo $return_id; ?>"
            data-masterid="<?php echo $return['master_return_id']; ?>"
            onclick="getHistory(this)"
            toggle="tooltip"
            title="View History"
            data-original-title="View History">
            <i class="fa fa-eye "></i>
          </a>
          <a
            class="btn button-default resetRow"
            style="border: solid 1px #ccc;background:#fff"
            href="javascript:void(0)"
            onclick="resetRow('#<?php echo $oopid; ?>','<?php echo $return_id; ?>')"
            toggle="tooltip"
            title="Reset"
            data-original-title="Reset Row">
            <i class="fa fa-refresh "></i>
          </a>
            <a class="btn button-default show_defected_image"
              id="shipment_cancel_button_$return['return_id']"
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
      <?php 
        $return_actions_not_to_deduct_qty = array(
                                              RETURN_ACTION_IDS['Return_Request_Rejected'],
                                              RETURN_ACTION_IDS['Replacement_Request_Rejected'],
                                              RETURN_ACTION_IDS['Return_Goods_Rejected'],
                                              RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
                                              RETURN_ACTION_IDS['Customer_Picked_Items'],
                                              RETURN_ACTION_IDS['Cancelled_By_Customer'],
                                              RETURN_ACTION_IDS['Replacement_complete']
                                            );
              if(!in_array($return['return_action_id'], $return_actions_not_to_deduct_qty) ){
                $remain_quantity = $remain_quantity - $return['quantity'];
              }
            }
          }
      if($remain_quantity > 0 ){
          $isDisabled = "";
          if( 
              empty($product['return_reasons'])
                  ||
              (
                $product['is_returnable'] == '0'
                  && 
                $admin_mode == 'off'
              )
          ){
            $isDisabled = "disabled";
          }
      ?>
      <tr
      id="<?php echo $product['order_product_id']; ?>_new"
      data-orderProductId="<?php echo $product['order_product_id']; ?>"
      data-comboProductId="<?php echo $product['combo_product_id']; ?>"
      data-returnid="0"
      class="products"
      data-replacement="<?php echo !empty($product['case']['replacement']) || !empty($product['case']['cancelled']) ? 'true' : 'false'; ?>"
      data-quality="<?php echo !empty($product['case']['quality']) || !empty($product['case']['cancelled']) ? 'true' : 'false'; ?>"
      data-cancelled="<?php echo !empty($product['case']['cancelled']) ? 'true' : 'false'; ?>"
      data-failed="<?php echo !empty($product['case']['failed']) ? 'true' : 'false'; ?>"
      toggle="tooltip"
      >
        <td>
          <?php if($isDisabled == '' ){ ?>
          <input type="checkbox" 
                class="addForm order_product_id add_return_chck" 
                name="order_product_id[]" 
                value="<?php echo $oopid; ?>" 
                data-combo-product-id="<?php echo $product['combo_product_id']; ?>" 
                data-order-product-id="<?php echo $product['order_product_id']; ?>" 
                data-product-id="<?php echo $product['product_id']; ?>"
                data-master-return-id="<?php //echo $return['master_return_id']; ?>"
                data-seller-invoice-id="<?php echo $product['seller_invoice_id']; ?>"
                data-customer-payment-company="<?php echo $customer_payment_company;?>"
                />
          <?php } ?>
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][last_return_id]" 
            value="0" 
            class="last_return_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][last_return_reason]"
            value="0" 
            class="last_return_reason">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][last_return_quantity]" 
            value="0" 
            class="last_return_quantity">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][buyer_invoice_id]" 
            value="<?php echo $products[$oopid]['buyer_invoice_id']?>" 
            class="buyer_invoice_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][new][seller_id]" 
            value="<?php echo $product['seller_id']; ?>" 
            class="seller_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>][new][total_quantity]" 
            value="<?php echo $product['quantity'] * $product['piece_in_set']; ?>" 
            class="total_quantity">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][master_return_id]" 
            value="0" 
            class="last_master_return_id">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][last_shipping_method]" 
            value="" 
            class="last_shipping_method">
          <input type="hidden" 
            name="product[<?php echo $oopid; ?>]['new'][last_comment]" 
            value="" 
            class="last_comment">
        </td>
        <td>
          <img
            src="<?php echo
            $pid_to_imgs[$oopid]; ?>"
            width="<?php echo $image_width; ?>px"
            height="<?php echo $image_height; ?>px"
          /><br><b><?php echo $oopid;?></b>
        </td>
        <td>
          <a href="<?php echo $product['link']; ?>"><?php echo $product['model']; ?></a>
          <i><?php echo !empty($oop_options[$oopid]['name'])? 
            "<br>".$oop_options[$oopid]['name'].' : '.$oop_options[$oopid]['value'] :
            '' ?> 
          </i>
          <?php
            if($product['is_returnable'] == 0){
              echo '<span style="color: red; font-size: large;"><br>(Non-Returnable Product)</span>';
            }else if(!empty($product['sor_msg'])){
              echo '<br><span style="color: orange;font-size: medium;">(<i>'. $product['sor_msg'] .'</i>)</span>';
            }
          ?>
        </td>
        <td><?php echo $total_quantity; ?></td>
        <td><?php echo $product['price_per_piece_with_currency']; ?></td>
        <td>N/A</td>
        <td>
          <select style="width: 200px;"
          data-privious="0"
          name="product[<?php echo $oopid; ?>][new][return_reason]"
          class="return_reason form-control" 
          <?php echo $isDisabled;?> >
            <option value="0">Select</option>
            <?php  if(!empty($product['return_reasons'])){ ?>
            <?php foreach( $product['return_reasons'] as $return_option => $options ){ ?>
            <optgroup label="<?php echo $options['title']; ?>"><?php 
            foreach($options['reasons'] as $reason ){ 
            ?>
            <option value="<?php echo $reason; ?>"><?php echo $return_reasons[$reason]['name']; ?>
            </option>
            <?php } ?>
            </optgroup>
            <?php } ?>
            <?php } ?>
          </select>
        </td>
        <td>N/A</td>
        <td>
          <input type="number"
             data-privious="0"
             name="product[<?php echo $oopid; ?>][new][quantity]"
             min="0"
             <?php echo 'max="'.$remain_quantity.'"'; ?>
             value="0"
             class="product_quantity form-control"
             <?php echo $isDisabled;?> 
            />
        </td>
        <td>
          <select data-privious=""
                  name="product[<?php echo $oopid; ?>][new][shipping_method]"
                  class="form-control shipping_methods" <?php echo $isDisabled;?> >
            <option value="">Select</option>
            <?php foreach ($shipping_method as $code => $text) { ?>
            <option value="<?php echo $code; ?>">
                <?php echo $text; ?>
            </option>
            <?php } ?>
          </select>
        </td>
        <td>
          <textarea
            name="product[<?php echo $oopid; ?>][new][comment]"
            rows="4"
            cols="10"
            class="comment"
            data-privious=""
            placeholder="<?php echo $entry_comment; ?>"
            <?php echo $isDisabled;?>
            ></textarea>
        </td>
        <td></td>
        <td></td>
        <td>
          <a
            class="btn button-default"
            style="border: solid 1px #ccc;background:#fff"
            href="javascript:void(0)"
            data-opid="<?php echo $oopid; ?>"
            data-pmodel="<?php echo $product['model']?>"
            data-returnid=""
            onclick="getHistory(this)"
            toggle="tooltip"
            title="View History"
            data-original-title="View History">
            <i class="fa fa-eye "></i>
          </a>
          <a
            class="btn button-default resetRow"
            style="border: solid 1px #ccc;background:#fff"
            href="javascript:void(0)"
            onclick="resetRow('#<?php echo $oopid; ?>','0','_new')"
            toggle="tooltip"
            title="Reset"
            data-original-title="Reset Row">
            <i class="fa fa-refresh "></i>
          </a>
          <a class="btn button-default show_defected_image"
                id="shipment_cancel_button_$return['return_id']"
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
    <?php
          }
        } 
      } 
    ?>
    </tbody>
  </table>
</div>
</form>