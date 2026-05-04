<?php if(!empty($returns_for_cn)) { ?>
  <h3>Generate Credit Note</h3><br>
  <?php foreach( $returns_for_cn as $suborder_id => $cn_returns ){ ?>
  <div class="col-md-12">

    <div class="generate" style="float: right;">
      <input type="hidden" name="advance_collected" value="<?php echo !empty($advance_payments[$suborder_id]['advance_collected'])?$advance_payments[$suborder_id]['advance_collected']:0;?>"  />
      <input type="hidden" name="cod_security_balance" value="<?php echo $CODSecurityBalance; ?>"  />
      <input type="hidden" name="cash_discount" value="<?php echo !empty($advance_payments[$suborder_id]['cash_discount'])?$advance_payments[$suborder_id]['cash_discount']:0;?>"  />
      <input type="hidden" name="actual_advance_collected" value="<?php echo !empty($advance_payments[$suborder_id]['advance_collected'])?$advance_payments[$suborder_id]['advance_collected']:0;?>"  />

        <label for="reversal_shipping_<?php echo $suborder_id;?>">
          Refund - On Hold : 
        </label>
        <input 
          type="checkbox" 
          id="refund_onhold_<?php echo $suborder_id;?>" 
          name="refund_onhold" 
          value="ON_HOLD"> &nbsp&nbsp&nbsp
        
      <?php
      if($cod_failed_penalty[$suborder_id]['disabled']){
        $cod_failed_penalty[$suborder_id]['value'] = 0;
      ?>
        <label for="reversal_shipping_<?php echo $suborder_id;?>">
          Reversal of Shipping Charges(<u id="reversal_shipping_charges_<?php echo $suborder_id?>" data-privious="<?php echo $reversal_shipping[$suborder_id];?>"><?php echo $reversal_shipping[$suborder_id];?></u>) : 
        </label>
        <input 
          type="checkbox" 
          id="reversal_shipping_<?php echo $suborder_id;?>" 
          name="reversal_shipping" 
          value="<?php echo $reversal_shipping[$suborder_id];?>"> &nbsp&nbsp&nbsp
        <label for="shipping_charges_<?php echo $suborder_id;?>">Reverse Shipping Charge :</label>
        <input 
          type="text" 
          id="shipping_charges_<?php echo $suborder_id;?>"
          name="shipping_charges"
          value="<?php echo !empty($shipping[$suborder_id]) ? $shipping[$suborder_id] : 0; ?>" />
        &nbsp&nbsp&nbsp
      <?php } ?>

      <?php if(!$cod_failed_penalty[$suborder_id]['disabled']){ ?>
      <div class="col-md-3">
        <label for="cod_failed_penalty_<?php echo $suborder_id;?>">COD Security Balance : <?php echo !empty($CODSecurityBalance)?$CODSecurityBalance:0;?></label>
        &nbsp;&nbsp; 
        <label for="cod_failed_penalty_<?php echo $suborder_id;?>">Cash Discounts :  <?php echo !empty($advance_payments[$suborder_id]['cash_discount'])?$advance_payments[$suborder_id]['cash_discount']:0;?></label>
        &nbsp;&nbsp;<br>
        <label for="cod_failed_penalty_<?php echo $suborder_id;?>">Advance Collected : <?php echo !empty($advance_payments[$suborder_id]['advance_collected'])?$advance_payments[$suborder_id]['advance_collected']:0;?></label>
      </div>
        &nbsp;&nbsp;
        <label for="cod_failed_penalty_<?php echo $suborder_id;?>">Cod Failed Penalty :</label>
      <?php 
        $suborder_wise_cod_failed_penalty =  $advance_payments[$suborder_id]['advance_collected'] + $CODSecurityBalance ;
      ?> 
       <input 
          type="text"
          id="cod_failed_penalty_<?php echo $suborder_id;?>"
          name="cod_failed_penalty"
          value="<?php echo $suborder_wise_cod_failed_penalty; ?>" />
          &nbsp&nbsp&nbsp

      <?php } ?>
      
      <label for="other_charges_<?php echo $suborder_id;?>">Other Charges : </label>
      <input 
        type="text"
        id="other_charges_<?php echo $suborder_id;?>"
        name="other_charges"
        value="" /><br>
      
      <span style="width: 280px;" class="pull-right"><i><b>(Enter Negative value, if you want to Deduct from Credit note amount (refund LESS to customer), else Enter Positive, if refund MORE)</i></b>

      <div style="width: 100%">
        <span id="link-td">
          <?php reset($products); reset($cn_returns); ?>
          <button type="button"
            data-toggle="tooltip"
            data-order_id="<?php echo $products[key($products)]['order_id']; ?>"
            data-seller_id="<?php echo $products[$cn_returns[key($cn_returns)]['order_product_id']]['seller_id']; ?>"          
            data-order_no="<?php echo $order_no; ?>"
            data-cod-security-balance="<?php echo $CODSecurityBalance;?>"
            data-remaining_advance="<?php echo !empty($cod_failed_penalty[$suborder_id]['value']) ? $cod_failed_penalty[$suborder_id]['value'] : 0; ?>"
            data-is-cod-failed="<?php echo $cod_failed_penalty[$suborder_id]['value']?>"
            data-suborder_id="<?php echo $suborder_id; ?>"
            title="Generate Credit Note"
            onclick="return generate_credit_note(this, '<?php echo $suborder_id; ?>')"
            class="btn btn-warning genrate"
            data-original-title="Generate Credit Note">
            <i class="fa fa-cog"></i> Generate
          </button> 
          <!--<input type="hidden" class="return_id" 
          value="[<?php echo implode(",",array_keys($cn_returns)); ?>]" /> -->
        </span>
      </div>
      </span>
    </div>
  </div>
  <fieldset class="col-md-12">
    <div class="table-responsive">
      <table class ="table table-bordered" >
        <thead>
            <tr>
              <td style="width: 5px;"><input type="checkbox" onclick="clickAllId(this,'<?php echo $suborder_id?>')" class="cnForm" ></td>
              <td>S.NO</td>
              <td>Product</td>
              <td>Quantity</td>
              <td>MasterReturn Id</td>
              <td>OrderProduct Id</td>
              <td>Return Reason</td>
              <td>Return Action</td>
              <td>ReturnUpdated Dated</td>
            </tr>
        </thead>
        <tbody id="cn-products-<?php echo $suborder_id?>">
          <?php $i =1;?>
          <?php foreach ($cn_returns as $return){ ?>
            <tr class="cn-products-list-<?php echo $suborder_id?>">
              <td style="width: 5px;">
                <input type="checkbox" class="cn-return-ids-<?php echo $suborder_id?> cn_reversal_shipping" data-order-id="<?php echo $return['order_id'];?>" data-suborder-id="<?php echo $suborder_id?>" name="return_ids[]" value="<?php echo $return['return_id'];?>" data-order-no="<?php echo $order_no; ?>" 
                data-suborder-shipping-charge="<?php echo $return['shipping_charge'];?>"
                >
              </td>
              <td><?php echo $i; ?></td>
              <td>
                <?php echo $products[$return["order_product_id"]]['model']; ?><br>
                <i>(<?php echo $products[$return["order_product_id"]]['name']; ?>)</i>
                <?php
                  if($products[$return["order_product_id"]]['is_returnable'] == 0){
                    echo '<span style="color: red;"><br>(Non-Returnable Product)</span>';
                  }
                ?>
              </td>
              <td><?php echo $return["quantity"]; ?></td>
              <td><?php echo $return["master_return_id"]; ?></td>
              <td><?php echo $return["order_product_id"]; ?></td>
              <td><?php echo !empty($return["return_reason"]) ? $return['return_reason']['name'] : '-' ; ?></td>
              <td><?php echo !empty($return["return_action"]) ? $return['return_action']['name'] : '-' ; ?></td>
              <td><?php echo date("d-m-Y H:i:s",strtotime($return["date_added"])); ?></td>
            </tr>
          <?php
          $i++;
          }
          ?>
        </tbody>
      </table>
    </div>
  </fieldset>
  <?php }
  } ?>
  <?php $i=1; ?>
  <?php if(!empty($credit_notes)) { ?>
  <?php 
  $unique_cn = array_combine(
                  array_column($credit_notes, 'credit_note_id'),
                  $credit_notes);
  ?>
  <div class="col-md-12">
    <h3>Download Credit Note</h3>
    <table style="width:100%;" border="0" >
      <tr>
        <td></td>
        <?php 
        foreach($unique_cn as $cn_id => $cn) { ?>
        <td style="width:11%; text-align:right;">
          <div>
            <span><b><?php echo $cn['credit_note_no']; ?></b></span>&nbsp
            <a href="<?php echo $cn['credit_note_dload']; ?>" class="btn btn-sm btn-warning" id="credit_note_no_<?php echo $cn['credit_note_no']; ?>">
              <i class="fa fa-download"></i>
            </a>
            <?php if( $cn['cn_cancellable'] && $cn['credit_note_status'] && $admin_mode == 'on') { ?>
            <button class="btn btn-sm btn-danger credit_note_remove" 
              type="button" 
              data-credit-no="<?php echo $cn['credit_note_no']; ?>" 
              data-credit-id="<?php echo $cn['credit_note_id']; ?>" 
              data-order-id="<?php echo $order_id; ?>"
            > 
              <i class="fa fa-close"></i> 
            </button>
            <?php } ?>
          </div>
          <span><b>(<?php echo $cn['date_show'];?>)</b></span>
        </td>
        <?php } ?>
      </tr>
    </table><br>
    <fieldset>
      <div class="table-responsive">
        <table class ="table table-bordered" >
          <thead>
            <tr>
              <td>S.No</td>
              <td>Product</td>
              <td>Qty</td>
              <td>MasterReturnId<br><br>OrderProductId</td>
              <td>Return Reason</td>
              <td>Return Action</td>
              <td>Debit Note Numbers</td>
              <td>Credit Note No</td>
              <td>Credit Note Amount</td>
              <td>Net Refundable</td>
              <td>Date Added</td>
              <td>Created BY</td>
              <td>Action</td>
            </tr>
          </thead>
          <tboby>
            <?php foreach($credit_notes as $values){  ?>
            <tr>
              <td><?php echo $i;?></td>
              <td>
                <?php echo $products[$values['order_product_id']]['model']; ?><br>
                <i>(<?php echo $products[$values["order_product_id"]]['name']; ?>)</i>
                <?php
                  if($products[$values["order_product_id"]]['is_returnable'] == 0){
                    echo '<span style="color: red;"><br>(Non-Returnable Product)</span>';
                  }
                ?>
              </td>
              <td><?php echo $values['rtn_qty'];?></td>
              <td>
                <?php echo $values["master_return_id"]; ?><br>----<br><?php echo $values["order_product_id"]; ?>
              </td>
              <td>              
              <?php 
                if(!empty($returns[$values['return_id']])){
                  $return_reason = $returns[$values['return_id']]['return_reason'];

                  echo !empty($return_reason) ? $return_reason['name'] : '' ;
                }
              ?>
              </td>
              <td>
              <?php echo !empty($values["return_action_id"]) ? $return_actions[$values["return_action_id"]]['name'] : '-' ; ?>
              </td>
              <td><?php echo !empty($values["debit_note_no"]) ? $values["debit_note_no"]: ''; ?></td>
              <td>
                <?php echo $values["credit_note_prefix"].$values["credit_note_no"] ?><br>
                <b>(<?php echo $values["credit_note_id"] ?>)</b>
              </td>
              <td><?php echo $values["credit_note_amount"] ?></td>
              <td><?php echo $values["net_refundable"] ?></td>
              <td><?php echo date("d-m-Y H:i:s",strtotime($values["date_show"])); ?></td>
              <td><?php echo $values["credit_note_user"] ?></td>
              <td>
              <?php if($values['cn_cancellable']){
                  $str = 'data-toggle="modal"';
                }else{
                  $str = 'toggle="tooltip" title="Payment is initiated/done, check it manually"';
                }
              ?>
              <?php if($admin_mode == 'on'){ ?>
              <a 
                data-target="#edit_cn_pop_up" 
                data-order_id="<?php echo $products[$values['order_product_id']]['order_id'];?>"
                data-cn_id="<?php echo $values['credit_note_id'];?>" 
                data-penality="<?php echo $values['cod_failed_penalty'];?>" 
                data-iscod="<?php echo $values['is_cod_failed'];?>" 
                data-revship="<?php echo $values['shipping_collected'];?>" 
                data-reversal_shipping="<?php echo $values['reversal_shipping'];?>" 
                data-other_charges="<?php echo $values['other_charges'];?>" 
                data-refund_on_hold="<?php echo $values['payment_cleared'];?>"
                id="cn_edit_<?php echo $values['credit_note_id'];?>" 
                class="cn_edit_btn" <?php echo $str;?>
              >
                <i class="fa fa-edit fa-2x" aria-hidden="true"></i>
              <a>
              <?php } ?>
              &nbsp&nbsp&nbsp 
              <a href="<?php echo $values['credit_note_dload']; ?>" id="generate_cn_link<?php echo $values['credit_note_id']; ?>"><i class="fa fa-download fa-2x" aria-hidden="true"></i><a>
              </td>
            </tr>
            <?php $i++; } ?>
          </tbody>
        </table>
      </div>
    </fieldset>
  </div>
<?php }
echo '<input type="hidden" name="credit_order_id" value="'.$order_id.'">';
echo '<input type="hidden" name="credit_order_no" value="'.$order_no.'">';
?>
<!-------- Cancelled CreditNote   -------->
<?php $i=1; ?>
  <?php if(!empty($cancelled_cn)) { ?>
  <div class="col-md-12">
    <h3>Cancelled Credit Note</h3>
    <table style="width:100%;" border="0" >
      <tr>
        <td></td>
        <?php 
        foreach($cancelled_cn as $cn) { ?>
        <td style="width:11%; text-align:right;">
          <div>
            <span><b><?php echo $cn['credit_note_no']; ?></b></span>&nbsp
            <a href="<?php echo $cn['credit_note_dload']; ?>" class="btn btn-sm btn-warning" id="credit_note_no_<?php echo $cn['credit_note_no']; ?>">
              <i class="fa fa-download"></i>
            </a>
            <?php if( $cn['credit_note_status'] && $admin_mode == 'on') { ?>
            <button class="btn btn-sm btn-danger credit_note_remove" 
              type="button" 
              data-credit-no="<?php echo $cn['credit_note_no']; ?>" 
              data-credit-id="<?php echo $cn['credit_note_id']; ?>" 
              data-order-id="<?php echo $order_id; ?>"
            > 
              <i class="fa fa-close"></i> 
            </button>
            <?php } ?>
          </div>
          <span><b>(<?php echo $cn['date_added'];?>)</b></span>
        </td>
        <?php } ?>
      </tr>
    </table><br>
    <fieldset>
      <div class="table-responsive">
        <table class ="table table-bordered" >
          <thead>
            <tr>
              <td>S.No</td>
              <td>Credit Note No</td>
              <td>Credit Note Amount</td>
              <td>Net Refundable</td>
              <td>Date Added</td>
              <td>Created BY</td>
              <td>Action</td>
            </tr>
          </thead>
          <tboby>
            <?php foreach($cancelled_cn as $values){ ?>
            <tr>
              <td><?php echo $i;?></td>
              <td>
                <?php echo $values["credit_note_prefix"].$values["credit_note_no"] ?><br>
                <b>(<?php echo $values["credit_note_id"] ?>)</b>
              </td>
              <td><?php echo $values["credit_note_amount"] ?></td>
              <td><?php echo $values["net_refundable"] ?></td>
              <td><?php echo date("d-m-Y H:i:s",strtotime($values["date_added"])); ?></td>
              <td><?php echo $values["user"] ?></td>
              <td>
                <a href="<?php echo $values['credit_note_dload']; ?>" id="generate_cn_link<?php echo $values['credit_note_id']; ?>"><i class="fa fa-download fa-2x" aria-hidden="true"></i><a>
              </td>
            </tr>
            <?php $i++; } ?>
          </tbody>
        </table>
      </div>
    </fieldset>
  </div>
<?php } ?>
<!-- Starts Edit CN pop-up -->
<div id="edit_cn_pop_up" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Credit Note</h4>
      </div>
      <div class="edit_cn_modal_body modal-body">
        <form name="edit_cn_form" id="edit_cn_form" action="#">
          <div class="alert-danger" id="cn_error"></div><br>
          <input type="hidden" name="edit_cn_order_id" id="edit_cn_order_id" />
          <input type="hidden" name="edit_cn_id" id="edit_cn_id" />
          <input type="hidden" name="is_cod_failed_cn_edit" id="is_cod_failed_cn_edit" />
          <div class="col-sm-12 cod_penality_div">
            <label class="col-sm-5" for="cod_penality">COD Failed Penality<span class="required_field"> *</span>: </label>
            <input class="col-sm-7" type="text" name="cod_penality" id="cod_penality" placeholder="COD Failed Penality" />
            <input type="hidden" name="old_cod_penality" id="old_cod_penality" />
          </div>
          <br>
          <div class="col-sm-12 rev_shipping_div">
            <label class="col-sm-5" for="rev_shipping">Reverse Shipping<span class="required_field"> *</span>: </label>
            <input class="col-sm-7" type="text" name="rev_shipping" id="rev_shipping" placeholder="Reverse Shipping" />
            <input type="hidden" name="old_rev_shipping" id="old_rev_shipping" />
          </div>
          <br>
          <div class="col-sm-12 reversal_shipping_div">
            <label class="col-sm-5" for="reversal_shipping">Reversal of Shipping Charges: </label>
            <input type="text" class="col-sm-7" name="reversal_shipping" id="reversal_shipping" placeholder="Forward Shipping Charges" />
            <input type="hidden" name="old_reversal_shipping" id="old_reversal_shipping" />
          </div>
          <br><br>
          <div class="other_charges_div col-sm-12">
            <label for="other_charges" class="col-sm-5">Other Charges: </label>
            <input type="text" class="col-sm-7" name="other_charges" id="other_charges" placeholder="Other Charges" />
            <input type="hidden" name="old_other_charges" id="old_other_charges" />
          </div>
          <br><br>
          <div class="refund_on_hold_div col-sm-12">
            <label for="refund_on_hold" class="col-sm-5">Refund ON-HOLD: </label>
            <input 
              type="checkbox" 
              id="refund_on_hold" 
              name="refund_on_hold" 
              value="ON_HOLD" />
              <input type="hidden" name="old_refund_on_hold" id="old_refund_on_hold" />
          </div>
          <br><br>
          <div name="cn_comment_div col-sm-12">
            <label class="col-sm-5" for="cn_comment">Comment<span class="required_field"> *</span>: </label>
            <textarea  name="cn_comment" id="cn_comment" rows="5" cols="30" placeholder="Comment"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="return edit_cn();" name="submit_edit_cn" title="Submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-default edit_cn_close" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Ends Edit CN pop-up -->